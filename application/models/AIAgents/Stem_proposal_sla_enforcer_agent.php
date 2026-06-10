<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * STEM Learning - Proposal SLA Enforcer Agent
 * Migration 026 (Phase 1, live 1 Jun 2026)
 *
 * Responsibilities:
 *  1. Open a proposal_sla_tracker row when init_call.cstatus transitions to 6 (Positive)
 *  2. Hard-block BD planner draft (insert into bd_planner_block_log) for any open SLA past deadline
 *  3. Process breaches: wallet debit Rs 1000, grade penalty minus 10, auto-downgrade cstatus 6 to 3
 *  4. Grant the single allowed 24h extension on BD request
 *  5. Close the SLA on proposal submission
 *
 * Designed to run from:
 *   - Hook: after_lead_progression_update (open the SLA)
 *   - CLI: php index.php proposal_sla_enforcer enforce_now (every 30 min via cron)
 *   - Hook: before_planner_draft (block check)
 *   - API: /api/proposal/sla/* (controller)
 *
 * Founder rule (verbatim): "Wherever the proposals are need to be sent.
 * It has to be hard block within 48 hours proposal has to be sent."
 */
class Proposal_sla_enforcer_agent
{
    const SLA_HOURS                   = 48;
    const EXTENSION_HOURS             = 24;
    const WALLET_DEBIT_RS             = 1000;
    const GRADE_PENALTY_POINTS        = 10;
    const DOWNGRADE_FROM_CSTATUS      = 6;
    const DOWNGRADE_TO_CSTATUS        = 3;
    const POSITIVE_CSTATUS            = 6;
    const CRON_BATCH_LIMIT            = 500;

    protected $CI;
    protected $db;
    protected $log_prefix = '[proposal_sla_enforcer]';

    public function __construct()
    {
        $this->CI =& get_instance();
        $this->CI->load->database();
        $this->db = $this->CI->db;
        $this->CI->load->helper(['date','url']);
        $this->CI->load->library('wallet_lib');
        $this->CI->load->model('planning_grade_model');
    }

    // -----------------------------------------------------------------
    // HOOK: called from progression model when cstatus changes to 6
    // -----------------------------------------------------------------
    public function open_sla_for_positive($cid_id, $bd_uid, $cm_uid = null, $positive_at = null)
    {
        if (empty($cid_id) || empty($bd_uid)) {
            log_message('error', $this->log_prefix . ' open_sla_for_positive missing args');
            return ['ok' => false, 'error' => 'missing_args'];
        }

        $positive_at = $positive_at ?: date('Y-m-d H:i:s');
        $sla_deadline = date('Y-m-d H:i:s', strtotime($positive_at) + (self::SLA_HOURS * 3600));

        $existing = $this->db
            ->select('id, status')
            ->from('proposal_sla_tracker')
            ->where('cid_id', $cid_id)
            ->get()->row_array();

        if ($existing) {
            log_message('info', $this->log_prefix . ' SLA already exists for cid_id=' . $cid_id);
            return ['ok' => true, 'sla_id' => $existing['id'], 'already_existed' => true];
        }

        $row = [
            'cid_id'        => $cid_id,
            'bd_uid'        => $bd_uid,
            'cm_uid'        => $cm_uid,
            'positive_at'   => $positive_at,
            'sla_deadline'  => $sla_deadline,
            'status'        => 'open',
        ];
        $this->db->insert('proposal_sla_tracker', $row);
        $sla_id = $this->db->insert_id();

        log_message('info', $this->log_prefix . " opened SLA id={$sla_id} cid={$cid_id} bd={$bd_uid} deadline={$sla_deadline}");
        return ['ok' => true, 'sla_id' => $sla_id, 'sla_deadline' => $sla_deadline];
    }

    // -----------------------------------------------------------------
    // HOOK: called when BD uploads a proposal document
    // -----------------------------------------------------------------
    public function mark_proposal_submitted($cid_id, $bd_uid, $doc_url)
    {
        $sla = $this->db->select('*')->from('proposal_sla_tracker')
            ->where('cid_id', $cid_id)
            ->where_in('status', ['open','extended'])
            ->get()->row_array();

        if (!$sla) {
            log_message('warning', $this->log_prefix . ' mark_proposal_submitted no open SLA cid=' . $cid_id);
            return ['ok' => false, 'error' => 'no_open_sla'];
        }

        if ((int)$sla['bd_uid'] !== (int)$bd_uid) {
            return ['ok' => false, 'error' => 'bd_mismatch'];
        }

        $this->db->where('id', $sla['id'])->update('proposal_sla_tracker', [
            'proposal_submitted_at' => date('Y-m-d H:i:s'),
            'proposal_doc_url'      => $doc_url,
            'status'                => 'submitted',
        ]);

        $this->_unblock_planner_drafts($bd_uid, $cid_id, 'proposal_submitted');

        log_message('info', $this->log_prefix . " submitted sla_id={$sla['id']} cid={$cid_id} bd={$bd_uid}");
        return ['ok' => true, 'sla_id' => $sla['id']];
    }

    // -----------------------------------------------------------------
    // API: grant the single allowed 24h extension
    // -----------------------------------------------------------------
    public function grant_extension($sla_id, $bd_uid, $reason)
    {
        $sla = $this->db->select('*')->from('proposal_sla_tracker')
            ->where('id', $sla_id)->get()->row_array();

        if (!$sla) return ['ok' => false, 'error' => 'sla_not_found'];
        if ((int)$sla['bd_uid'] !== (int)$bd_uid) return ['ok' => false, 'error' => 'bd_mismatch'];
        if ($sla['status'] !== 'open') return ['ok' => false, 'error' => 'sla_not_open'];
        if ((int)$sla['extension_used'] === 1) return ['ok' => false, 'error' => 'extension_already_used'];
        if (empty($reason) || strlen($reason) < 10) return ['ok' => false, 'error' => 'reason_too_short'];

        $new_deadline = date('Y-m-d H:i:s', strtotime($sla['sla_deadline']) + (self::EXTENSION_HOURS * 3600));

        $this->db->where('id', $sla_id)->update('proposal_sla_tracker', [
            'extension_used'       => 1,
            'extension_granted_at' => date('Y-m-d H:i:s'),
            'extension_reason'     => substr($reason, 0, 300),
            'sla_deadline'         => $new_deadline,
            'status'               => 'extended',
        ]);

        $this->_unblock_planner_drafts($bd_uid, $sla['cid_id'], 'extension_granted');

        log_message('info', $this->log_prefix . " extension granted sla_id={$sla_id} new_deadline={$new_deadline}");
        return ['ok' => true, 'new_deadline' => $new_deadline];
    }

    // -----------------------------------------------------------------
    // HOOK: called BEFORE BD planner draft insert
    // Returns ['allowed' => bool, 'blocking_cid_ids' => [...], 'reason' => string]
    // -----------------------------------------------------------------
    public function check_planner_block($bd_uid, $plan_date)
    {
        $now = date('Y-m-d H:i:s');
        $breaches = $this->db
            ->select('cid_id')
            ->from('proposal_sla_tracker')
            ->where('bd_uid', $bd_uid)
            ->where_in('status', ['open','extended'])
            ->where('sla_deadline <=', $now)
            ->get()->result_array();

        if (empty($breaches)) {
            return ['allowed' => true, 'blocking_cid_ids' => [], 'reason' => null];
        }

        $cid_ids = array_map(function($r){ return (int)$r['cid_id']; }, $breaches);
        $cid_csv = implode(',', $cid_ids);

        $existing_block = $this->db->select('id')->from('bd_planner_block_log')
            ->where('bd_uid', $bd_uid)
            ->where('plan_date', $plan_date)
            ->where('unblocked_at IS NULL')
            ->get()->row_array();

        if (!$existing_block) {
            $this->db->insert('bd_planner_block_log', [
                'bd_uid'           => $bd_uid,
                'plan_date'        => $plan_date,
                'block_reason'     => 'proposal_sla_breach',
                'blocking_cid_ids' => $cid_csv,
                'blocking_count'   => count($cid_ids),
            ]);

            $this->db->where('uid', $bd_uid)
                ->where('plan_date', $plan_date)
                ->update('daily_planner', [
                    'blocked_by_proposal_sla_at' => $now,
                    'blocking_cid_ids'           => $cid_csv,
                ]);
        }

        log_message('info', $this->log_prefix . " blocked bd={$bd_uid} plan_date={$plan_date} cids=[{$cid_csv}]");
        return [
            'allowed'          => false,
            'blocking_cid_ids' => $cid_ids,
            'reason'           => 'proposal_sla_breach',
            'message'          => 'Submit proposals for ' . count($cid_ids) . ' leads before drafting tomorrow plan.',
        ];
    }

    // -----------------------------------------------------------------
    // CLI: enforce_now - find breached SLAs and apply penalty + downgrade
    // Run every 30 min via cron.
    // -----------------------------------------------------------------
    public function enforce_now()
    {
        $now = date('Y-m-d H:i:s');
        $log = [
            'started_at'         => $now,
            'breaches_processed' => 0,
            'downgrades'         => 0,
            'wallet_debits_rs'   => 0,
            'errors'             => [],
        ];

        $breaches = $this->db
            ->select('*')
            ->from('proposal_sla_tracker')
            ->where_in('status', ['open','extended'])
            ->where('sla_deadline <=', $now)
            ->where('breach_processed_at IS NULL')
            ->limit(self::CRON_BATCH_LIMIT)
            ->get()->result_array();

        foreach ($breaches as $sla) {
            try {
                $res = $this->_process_one_breach($sla);
                if ($res['ok']) {
                    $log['breaches_processed']++;
                    $log['downgrades']++;
                    $log['wallet_debits_rs'] += self::WALLET_DEBIT_RS;
                } else {
                    $log['errors'][] = ['sla_id' => $sla['id'], 'error' => $res['error']];
                }
            } catch (Exception $e) {
                $log['errors'][] = ['sla_id' => $sla['id'], 'exception' => $e->getMessage()];
                log_message('error', $this->log_prefix . " exception sla_id={$sla['id']}: " . $e->getMessage());
            }
        }

        $log['finished_at'] = date('Y-m-d H:i:s');
        log_message('info', $this->log_prefix . ' enforce_now ' . json_encode($log));
        return $log;
    }

    // -----------------------------------------------------------------
    // Process a single SLA breach: wallet debit + grade penalty + downgrade
    // -----------------------------------------------------------------
    protected function _process_one_breach($sla)
    {
        $cid_id = (int)$sla['cid_id'];
        $bd_uid = (int)$sla['bd_uid'];
        $sla_id = (int)$sla['id'];

        $cstatus_now = $this->db->select('current_status_id')
            ->from('init_call')->where('id', $cid_id)
            ->get()->row('current_status_id');

        if ((int)$cstatus_now !== self::POSITIVE_CSTATUS) {
            $this->db->where('id', $sla_id)->update('proposal_sla_tracker', [
                'status'                => 'breached',
                'breach_processed_at'   => date('Y-m-d H:i:s'),
                'wallet_debit_rs'       => 0,
                'grade_penalty_points'  => 0,
                'downgrade_from_cstatus'=> $cstatus_now,
                'downgrade_to_cstatus'  => $cstatus_now,
            ]);
            return ['ok' => true, 'no_downgrade' => true, 'reason' => 'cstatus_already_changed'];
        }

        $wallet_ok = $this->CI->wallet_lib->debit(
            $bd_uid,
            self::WALLET_DEBIT_RS,
            'Proposal SLA breach for cid_id ' . $cid_id,
            'proposal_sla_breach'
        );
        if (!$wallet_ok) {
            log_message('warning', $this->log_prefix . " wallet debit failed bd={$bd_uid} sla={$sla_id}");
        }

        $this->CI->planning_grade_model->apply_penalty(
            $bd_uid,
            self::GRADE_PENALTY_POINTS,
            'proposal_sla_breach',
            $cid_id
        );

        $this->db->where('id', $cid_id)->update('init_call', [
            'current_status_id' => self::DOWNGRADE_TO_CSTATUS,
        ]);

        $this->db->insert('lead_progression_log', [
            'cid_id'             => $cid_id,
            'from_cstatus'       => self::DOWNGRADE_FROM_CSTATUS,
            'to_cstatus'         => self::DOWNGRADE_TO_CSTATUS,
            'transition_at'      => date('Y-m-d H:i:s'),
            'transition_by_uid'  => 0,
            'creation_path_hint' => 'proposal_sla_auto_downgrade',
            'reason'             => 'No proposal in 48 hours (migration 026)',
        ]);

        $this->db->where('id', $sla_id)->update('proposal_sla_tracker', [
            'status'                => 'downgraded',
            'breach_processed_at'   => date('Y-m-d H:i:s'),
            'wallet_debit_rs'       => self::WALLET_DEBIT_RS,
            'grade_penalty_points'  => self::GRADE_PENALTY_POINTS,
            'downgrade_from_cstatus'=> self::DOWNGRADE_FROM_CSTATUS,
            'downgrade_to_cstatus'  => self::DOWNGRADE_TO_CSTATUS,
        ]);

        log_message('info', $this->log_prefix . " downgraded sla_id={$sla_id} cid={$cid_id} bd={$bd_uid}");
        return ['ok' => true];
    }

    // -----------------------------------------------------------------
    // Unblock planner drafts for a BD when SLA resolved
    // -----------------------------------------------------------------
    protected function _unblock_planner_drafts($bd_uid, $cid_id, $unblock_reason)
    {
        $blocks = $this->db->select('*')->from('bd_planner_block_log')
            ->where('bd_uid', $bd_uid)
            ->where('unblocked_at IS NULL')
            ->get()->result_array();

        foreach ($blocks as $b) {
            $remaining = array_filter(
                explode(',', $b['blocking_cid_ids']),
                function($c) use ($cid_id) { return (int)$c !== (int)$cid_id; }
            );

            if (empty($remaining)) {
                $this->db->where('id', $b['id'])->update('bd_planner_block_log', [
                    'unblocked_at' => date('Y-m-d H:i:s'),
                    'unblocked_by' => $unblock_reason,
                ]);
                $this->db->where('uid', $bd_uid)
                    ->where('plan_date', $b['plan_date'])
                    ->update('daily_planner', [
                        'blocked_by_proposal_sla_at' => null,
                        'blocking_cid_ids'           => null,
                    ]);
            } else {
                $this->db->where('id', $b['id'])->update('bd_planner_block_log', [
                    'blocking_cid_ids' => implode(',', $remaining),
                    'blocking_count'   => count($remaining),
                ]);
            }
        }
    }

    // -----------------------------------------------------------------
    // Probe endpoint helper - used by /api/proposal/sla/probe
    // -----------------------------------------------------------------
    public function probe()
    {
        $has_table = $this->db->table_exists('proposal_sla_tracker');
        return [
            'migration'         => '026',
            'phase'             => 1,
            'deployed'          => $has_table ? true : false,
            'sla_hours'         => self::SLA_HOURS,
            'extension_hours'   => self::EXTENSION_HOURS,
            'wallet_debit_rs'   => self::WALLET_DEBIT_RS,
            'grade_penalty'     => self::GRADE_PENALTY_POINTS,
            'now'               => date('Y-m-d H:i:s'),
        ];
    }
}

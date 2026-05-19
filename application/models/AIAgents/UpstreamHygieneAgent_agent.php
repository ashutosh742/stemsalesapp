<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * STEM Learning - Upstream Hygiene Agent
 * Migration 028 (Upstream Hygiene + Proposal Backlog Sweep)
 *
 * Responsibilities:
 *  1. Nightly scan of all cstatus 1 and 2 CIDs: compute days_stagnant,
 *     upsert upstream_hygiene_state, set flags, fire wallet debits,
 *     fire auto-Lost transitions at hard thresholds.
 *  2. Process proposal_backlog_legacy: seed from v_approved_mom_no_proposal,
 *     expire grace windows after 14 days.
 *  3. Block BD from creating new leads when stagnancy counts are over threshold.
 *
 * Designed to run from:
 *   - Hook: after_lead_progression_update (state row open/close)
 *   - CLI / cron: php index.php upstream_hygiene run_nightly (7:20 AM cron 34f41737)
 *   - Hook: before_new_lead_create (block check)
 *   - API: /api/upstream_hygiene/* (controller)
 *
 * Mirror of stem_proposal_sla_enforcer_agent.php (migration 026).
 * Author: STEM ops, 2026-05-17.
 */
class Upstream_hygiene_agent
{
    // ------------------------------------------------------------------
    // CONSTANTS
    // ------------------------------------------------------------------
    const MIGRATION                     = '028';

    // cstatus 1 (Open) thresholds (days).
    const OPEN_NEAR_MISS_DAYS           = 21;
    const OPEN_STAGNANT_DAYS            = 45;
    const OPEN_FIRST_ACTION_SLA_DAYS    = 7;

    // cstatus 2 (Reachout) thresholds (days).
    const REACHOUT_NEAR_MISS_DAYS       = 14;
    const REACHOUT_STAGNANT_DAYS        = 30;
    const REACHOUT_FIRST_ACTION_SLA_DAYS= 5;

    // Planner block thresholds.
    const OPEN_BLOCK_THRESHOLD          = 10;  // stagnant_45 Open rows
    const REACHOUT_BLOCK_THRESHOLD      = 5;   // stagnant_30 Reachout rows

    // Wallet debit per stagnant row.
    const WALLET_DEBIT_RS               = 200;

    // Auto-Lost cstatus.
    const LOST_CSTATUS                  = 13;

    // Lost reason codes.
    const LOST_REASON_OPEN              = 'abandoned_open';
    const LOST_REASON_REACHOUT          = 'no_response';

    // Proposal backlog grace window (days from migration 028 deploy date).
    const BACKLOG_GRACE_DAYS            = 14;

    // Reachout valid actiontypes: phone (1), email (2), WhatsApp (10).
    const REACHOUT_VALID_ACTIONTYPES    = [1, 2, 10];

    // Batch limit for nightly scan.
    const CRON_BATCH_LIMIT              = 1000;

    // Pilot uid list (used externally; stored here for reference in comments).
    // Pilot uids: 42 (Priya), 43 (Ravi), 44 (Anita), 45 (Vikram), 46 (Sneha), 12 (CM Anjali).

    protected $CI;
    protected $db;
    protected $log_prefix = '[upstream_hygiene]';

    // ------------------------------------------------------------------
    // CONSTRUCTOR (dependency injection matches 026 pattern)
    // ------------------------------------------------------------------
    public function __construct()
    {
        $this->CI =& get_instance();
        $this->CI->load->database();
        $this->db = $this->CI->db;
        $this->CI->load->helper(['date', 'url']);
        $this->CI->load->library('wallet_lib');
        $this->CI->load->model('planning_grade_model');
    }

    // ------------------------------------------------------------------
    // ENTRY POINT: run nightly from cron 34f41737 (7:20 AM IST).
    // ------------------------------------------------------------------
    public function run_nightly_detection()
    {
        $log = [
            'migration'             => self::MIGRATION,
            'started_at'            => date('Y-m-d H:i:s'),
            'open_state_updated'    => 0,
            'reachout_state_updated'=> 0,
            'near_miss_fired'       => 0,
            'stagnant_fired'        => 0,
            'auto_lost_fired'       => 0,
            'wallet_debits_rs'      => 0,
            'backlog_grace_expired' => 0,
            'errors'                => [],
        ];

        // Step 1: Refresh upstream_hygiene_state for all cs1 CIDs.
        $log['open_state_updated'] = $this->_refresh_state_for_cstatus(1);

        // Step 2: Refresh upstream_hygiene_state for all cs2 CIDs.
        $log['reachout_state_updated'] = $this->_refresh_state_for_cstatus(2);

        // Step 3: Process near-miss flags (cs1 >= 21d, cs2 >= 14d).
        $nm = $this->_process_near_miss_flags();
        $log['near_miss_fired'] = $nm['fired'];

        // Step 4: Process stagnant flags and wallet debits (cs1 >= 45d, cs2 >= 30d).
        $sf = $this->_process_stagnant_flags();
        $log['stagnant_fired']    = $sf['fired'];
        $log['wallet_debits_rs']  = $sf['wallet_rs'];

        // Step 5: Auto-Lost transitions for hard flags not yet processed.
        $al = $this->_process_auto_lost();
        $log['auto_lost_fired'] = $al['fired'];
        foreach ($al['errors'] as $e) { $log['errors'][] = $e; }

        // Step 6: Expire proposal backlog grace windows.
        $log['backlog_grace_expired'] = $this->expire_grace_windows();

        $log['finished_at'] = date('Y-m-d H:i:s');
        log_message('info', $this->log_prefix . ' run_nightly_detection ' . json_encode($log));
        return $log;
    }

    // ------------------------------------------------------------------
    // PUBLIC: compute stagnant Open rows over threshold.
    // Returns array of rows from upstream_hygiene_state for cstatus 1.
    // ------------------------------------------------------------------
    public function compute_stagnant_open($days_threshold = 45)
    {
        $threshold = max(1, (int)$days_threshold);
        return $this->db->query("
            SELECT s.*,
                   ic.compny_nm AS school_name,
                   ub.firstName AS bd_name,
                   uc.firstName AS cm_name
              FROM upstream_hygiene_state s
              LEFT JOIN init_call ic ON ic.id = s.cid_id
              LEFT JOIN user ub      ON ub.uid = s.bd_uid
              LEFT JOIN user uc      ON uc.uid = s.cm_uid
             WHERE s.cstatus = 1
               AND s.days_stagnant >= ?
             ORDER BY s.days_stagnant DESC
             LIMIT ?
        ", [$threshold, self::CRON_BATCH_LIMIT])->result_array();
    }

    // ------------------------------------------------------------------
    // PUBLIC: compute stagnant Reachout rows over threshold.
    // Returns array of rows from upstream_hygiene_state for cstatus 2.
    // ------------------------------------------------------------------
    public function compute_stagnant_reachout($days_threshold = 30)
    {
        $threshold = max(1, (int)$days_threshold);
        return $this->db->query("
            SELECT s.*,
                   ic.compny_nm AS school_name,
                   ub.firstName AS bd_name,
                   uc.firstName AS cm_name
              FROM upstream_hygiene_state s
              LEFT JOIN init_call ic ON ic.id = s.cid_id
              LEFT JOIN user ub      ON ub.uid = s.bd_uid
              LEFT JOIN user uc      ON uc.uid = s.cm_uid
             WHERE s.cstatus = 2
               AND s.days_stagnant >= ?
             ORDER BY s.days_stagnant DESC
             LIMIT ?
        ", [$threshold, self::CRON_BATCH_LIMIT])->result_array();
    }

    // ------------------------------------------------------------------
    // PUBLIC: debit BD wallet Rs 200 (or custom amount) for stagnant row.
    // Logs to upstream_hygiene_log. Guards against double debit via
    // wallet_debited column on upstream_hygiene_state.
    // ------------------------------------------------------------------
    public function debit_wallet($cid_id, $bd_uid, $reason, $rs = self::WALLET_DEBIT_RS)
    {
        $cid_id = (int)$cid_id;
        $bd_uid = (int)$bd_uid;
        $rs     = (float)$rs;

        // Check wallet_debited guard.
        $state = $this->db->select('wallet_debited')
            ->from('upstream_hygiene_state')
            ->where('cid_id', $cid_id)
            ->get()->row_array();

        if ($state && (int)$state['wallet_debited'] === 1) {
            log_message('info', $this->log_prefix . " wallet already debited cid={$cid_id}, skipping");
            return ['ok' => true, 'already_debited' => true];
        }

        $wallet_ok = $this->CI->wallet_lib->debit(
            $bd_uid,
            $rs,
            'Upstream hygiene stagnancy: ' . $reason . ' cid_id=' . $cid_id,
            'upstream_hygiene_stagnant'
        );

        if (!$wallet_ok) {
            log_message('warning', $this->log_prefix . " wallet debit failed bd={$bd_uid} cid={$cid_id}");
        }

        // Log the debit event.
        $this->db->insert('upstream_hygiene_log', [
            'cid_id'       => $cid_id,
            'event_type'   => 'wallet_debit',
            'days_at_event'=> $this->_get_days_stagnant($cid_id),
            'rs_amount'    => $rs,
            'notes'        => $reason,
        ]);

        // Mark debited so nightly re-run does not fire twice.
        $this->db->where('cid_id', $cid_id)
            ->update('upstream_hygiene_state', ['wallet_debited' => 1]);

        log_message('info', $this->log_prefix . " wallet debit Rs {$rs} bd={$bd_uid} cid={$cid_id}");
        return ['ok' => (bool)$wallet_ok, 'rs' => $rs];
    }

    // ------------------------------------------------------------------
    // PUBLIC: auto-move lead to Lost (cstatus 13).
    // Writes funnel_change_log entry via the existing trigger on init_call.
    // ------------------------------------------------------------------
    public function auto_move_to_lost($cid_id, $reason)
    {
        $cid_id = (int)$cid_id;

        $lead = $this->db->select('cstatus, mainbd')
            ->from('init_call')
            ->where('id', $cid_id)
            ->get()->row_array();

        if (!$lead) {
            log_message('error', $this->log_prefix . " auto_move_to_lost: cid={$cid_id} not found");
            return ['ok' => false, 'error' => 'cid_not_found'];
        }

        if ((int)$lead['cstatus'] === self::LOST_CSTATUS) {
            return ['ok' => true, 'already_lost' => true];
        }

        if (!in_array((int)$lead['cstatus'], [1, 2])) {
            // Lead moved to another stage before nightly ran. Log and exit.
            $this->db->where('cid_id', $cid_id)
                ->update('upstream_hygiene_state', ['hard_flag' => 1]);
            return ['ok' => true, 'skipped' => true, 'reason' => 'cstatus_moved_already'];
        }

        // Set context variables so trigger writes correct source.
        $this->db->query("SET @change_user_id = 0");
        $this->db->query("SET @change_source = 'system_auto'");

        // Perform the cstatus update; trigger trg_init_call_funnel_change
        // writes funnel_change_log automatically.
        $this->db->where('id', $cid_id)->update('init_call', [
            'cstatus'        => self::LOST_CSTATUS,
            'lost_reason'    => $reason,
            'lost_at'        => date('Y-m-d H:i:s'),
        ]);

        // Log the hard_auto_lost event in our hygiene log.
        $this->db->insert('upstream_hygiene_log', [
            'cid_id'       => $cid_id,
            'event_type'   => 'hard_auto_lost',
            'days_at_event'=> $this->_get_days_stagnant($cid_id),
            'rs_amount'    => 0,
            'notes'        => 'auto-Lost reason=' . $reason,
        ]);

        // Mark hard_flag on state row (trigger will delete the row but
        // this guards against any race where trigger fires after this update).
        $this->db->where('cid_id', $cid_id)
            ->update('upstream_hygiene_state', ['hard_flag' => 1]);

        log_message('info', $this->log_prefix . " auto_move_to_lost cid={$cid_id} reason={$reason}");
        return ['ok' => true, 'cid_id' => $cid_id, 'reason' => $reason];
    }

    // ------------------------------------------------------------------
    // PUBLIC: check if a BD is blocked from creating new leads.
    // Returns true if block threshold is hit.
    // ------------------------------------------------------------------
    public function check_bd_block($bd_uid)
    {
        $bd_uid = (int)$bd_uid;

        $stagnant_45 = (int)$this->db->query("
            SELECT COUNT(*) AS cnt
              FROM upstream_hygiene_state
             WHERE bd_uid = ?
               AND cstatus = 1
               AND days_stagnant >= ?
               AND hard_flag = 0
        ", [$bd_uid, self::OPEN_STAGNANT_DAYS])->row('cnt');

        if ($stagnant_45 >= self::OPEN_BLOCK_THRESHOLD) {
            $this->_write_block_log($bd_uid, 'stagnant_45_over_10');
            return [
                'blocked'  => true,
                'reason'   => 'stagnant_45_over_10',
                'count'    => $stagnant_45,
                'threshold'=> self::OPEN_BLOCK_THRESHOLD,
                'message'  => 'New lead creation is blocked. Clear ' . $stagnant_45 . ' stale Open leads first.',
            ];
        }

        $stagnant_30 = (int)$this->db->query("
            SELECT COUNT(*) AS cnt
              FROM upstream_hygiene_state
             WHERE bd_uid = ?
               AND cstatus = 2
               AND days_stagnant >= ?
               AND hard_flag = 0
        ", [$bd_uid, self::REACHOUT_STAGNANT_DAYS])->row('cnt');

        if ($stagnant_30 >= self::REACHOUT_BLOCK_THRESHOLD) {
            $this->_write_block_log($bd_uid, 'stagnant_30_over_5');
            return [
                'blocked'  => true,
                'reason'   => 'stagnant_30_over_5',
                'count'    => $stagnant_30,
                'threshold'=> self::REACHOUT_BLOCK_THRESHOLD,
                'message'  => 'New lead creation is blocked. Clear ' . $stagnant_30 . ' stale Reachout leads first.',
            ];
        }

        // No block: if there is an open block log row, close it.
        $this->db->where('bd_uid', $bd_uid)
            ->where('unblocked_at IS NULL')
            ->update('upstream_hygiene_block_log', [
                'unblocked_at' => date('Y-m-d H:i:s'),
            ]);

        return ['blocked' => false];
    }

    // ------------------------------------------------------------------
    // PUBLIC: process_backlog_sweep
    // Bulk-insert into proposal_backlog_legacy from v_approved_mom_no_proposal.
    // Safe to re-run (IGNORE guard on unique key).
    // ------------------------------------------------------------------
    public function process_backlog_sweep()
    {
        // v_approved_mom_no_proposal is provided by the ops team.
        // It must expose columns: cid_id, mom_approved_at.
        $has_view = $this->db->table_exists('v_approved_mom_no_proposal');
        if (!$has_view) {
            log_message('error', $this->log_prefix . ' v_approved_mom_no_proposal not found, backlog sweep skipped');
            return ['ok' => false, 'error' => 'view_not_found'];
        }

        $this->db->query("
            INSERT IGNORE INTO proposal_backlog_legacy
                (cid_id, mom_approved_at, days_since_mom_approved,
                 grace_window_ends_at, status)
            SELECT
                v.cid_id,
                v.mom_approved_at,
                DATEDIFF(NOW(), v.mom_approved_at),
                DATE_ADD(NOW(), INTERVAL ? DAY),
                'legacy_grace'
            FROM v_approved_mom_no_proposal v
            WHERE NOT EXISTS (
                SELECT 1 FROM proposal_sla_tracker pst WHERE pst.cid_id = v.cid_id
            )
        ", [self::BACKLOG_GRACE_DAYS]);

        $inserted = $this->db->affected_rows();
        log_message('info', $this->log_prefix . " process_backlog_sweep inserted={$inserted}");
        return ['ok' => true, 'inserted' => $inserted];
    }

    // ------------------------------------------------------------------
    // PUBLIC: expire_grace_windows
    // Move proposal_backlog_legacy rows from legacy_grace to legacy_overdue
    // after BACKLOG_GRACE_DAYS have passed.
    // ------------------------------------------------------------------
    public function expire_grace_windows()
    {
        $now = date('Y-m-d H:i:s');
        $this->db->query("
            UPDATE proposal_backlog_legacy
               SET status = 'legacy_overdue',
                   updated_at = NOW()
             WHERE status = 'legacy_grace'
               AND grace_window_ends_at <= ?
        ", [$now]);

        $expired = $this->db->affected_rows();
        log_message('info', $this->log_prefix . " expire_grace_windows expired={$expired}");
        return $expired;
    }

    // ------------------------------------------------------------------
    // PROBE: used by /api/upstream_hygiene/probe endpoint.
    // ------------------------------------------------------------------
    public function probe()
    {
        $has_state_table = $this->db->table_exists('upstream_hygiene_state');
        $has_log_table   = $this->db->table_exists('upstream_hygiene_log');
        return [
            'migration'                  => self::MIGRATION,
            'deployed'                   => ($has_state_table && $has_log_table),
            'open_near_miss_days'        => self::OPEN_NEAR_MISS_DAYS,
            'open_stagnant_days'         => self::OPEN_STAGNANT_DAYS,
            'reachout_near_miss_days'    => self::REACHOUT_NEAR_MISS_DAYS,
            'reachout_stagnant_days'     => self::REACHOUT_STAGNANT_DAYS,
            'wallet_debit_rs'            => self::WALLET_DEBIT_RS,
            'open_block_threshold'       => self::OPEN_BLOCK_THRESHOLD,
            'reachout_block_threshold'   => self::REACHOUT_BLOCK_THRESHOLD,
            'backlog_grace_days'         => self::BACKLOG_GRACE_DAYS,
            'now'                        => date('Y-m-d H:i:s'),
        ];
    }

    // ------------------------------------------------------------------
    // PRIVATE: refresh upstream_hygiene_state for a given cstatus.
    // Computes last qualifying touch and days_stagnant for every live
    // CID in that cstatus. Does NOT fire flags or debits.
    // ------------------------------------------------------------------
    protected function _refresh_state_for_cstatus($cstatus)
    {
        $cstatus = (int)$cstatus;

        // Build the actiontype filter for cstatus 2 (phone, email, WhatsApp).
        $actiontype_clause = '';
        $params = [$cstatus];
        if ($cstatus === 2) {
            $placeholders = implode(',', array_fill(0, count(self::REACHOUT_VALID_ACTIONTYPES), '?'));
            $actiontype_clause = "AND t.actiontype_id IN ({$placeholders})";
            $params = array_merge($params, self::REACHOUT_VALID_ACTIONTYPES);
        }

        // UPSERT: compute last_touch_at and days_stagnant for each CID.
        // Uses createDate as fallback when there are zero touch events.
        $sql = "
            INSERT INTO upstream_hygiene_state
                (cid_id, bd_uid, cm_uid, cstatus, days_stagnant,
                 last_touch_at, last_touch_actiontype)
            SELECT
                ic.id,
                ic.mainbd,
                (SELECT parent_uid FROM reporting_hierarchy
                  WHERE employee_uid = ic.mainbd AND active = 1 LIMIT 1),
                ic.cstatus,
                COALESCE(
                    DATEDIFF(CURDATE(), last_t.last_touch),
                    DATEDIFF(CURDATE(), DATE(ic.createDate))
                ),
                last_t.last_touch,
                last_t.last_actiontype
            FROM init_call ic
            LEFT JOIN (
                SELECT t.cid_id,
                       MAX(DATE(t.event_date)) AS last_touch,
                       SUBSTRING_INDEX(GROUP_CONCAT(t.actiontype_id
                           ORDER BY t.event_date DESC), ',', 1) AS last_actiontype
                  FROM tblcallevents t
                 WHERE 1=1
                   {$actiontype_clause}
                 GROUP BY t.cid_id
            ) last_t ON last_t.cid_id = ic.id
            WHERE ic.cstatus = ?
            ON DUPLICATE KEY UPDATE
                bd_uid                = VALUES(bd_uid),
                cm_uid                = VALUES(cm_uid),
                cstatus               = VALUES(cstatus),
                days_stagnant         = VALUES(days_stagnant),
                last_touch_at         = VALUES(last_touch_at),
                last_touch_actiontype = VALUES(last_touch_actiontype)
        ";
        $params[] = $cstatus;
        $this->db->query($sql, $params);
        return $this->db->affected_rows();
    }

    // ------------------------------------------------------------------
    // PRIVATE: set near_miss_flag and log event for rows crossing threshold.
    // ------------------------------------------------------------------
    protected function _process_near_miss_flags()
    {
        $fired = 0;

        // cstatus 1: near-miss at 21 days.
        $rows_cs1 = $this->db->query("
            SELECT cid_id, bd_uid, cm_uid, days_stagnant
              FROM upstream_hygiene_state
             WHERE cstatus = 1
               AND days_stagnant >= ?
               AND near_miss_flag = 0
               AND stagnant_flag = 0
             LIMIT ?
        ", [self::OPEN_NEAR_MISS_DAYS, self::CRON_BATCH_LIMIT])->result_array();

        foreach ($rows_cs1 as $r) {
            $this->db->where('cid_id', $r['cid_id'])
                ->update('upstream_hygiene_state', ['near_miss_flag' => 1]);
            $this->db->insert('upstream_hygiene_log', [
                'cid_id'       => $r['cid_id'],
                'event_type'   => 'near_miss',
                'days_at_event'=> $r['days_stagnant'],
                'rs_amount'    => 0,
                'notes'        => 'cs1 near_miss at ' . self::OPEN_NEAR_MISS_DAYS . 'd threshold',
            ]);
            $fired++;
        }

        // cstatus 2: near-miss at 14 days.
        $rows_cs2 = $this->db->query("
            SELECT cid_id, bd_uid, cm_uid, days_stagnant
              FROM upstream_hygiene_state
             WHERE cstatus = 2
               AND days_stagnant >= ?
               AND near_miss_flag = 0
               AND stagnant_flag = 0
             LIMIT ?
        ", [self::REACHOUT_NEAR_MISS_DAYS, self::CRON_BATCH_LIMIT])->result_array();

        foreach ($rows_cs2 as $r) {
            $this->db->where('cid_id', $r['cid_id'])
                ->update('upstream_hygiene_state', ['near_miss_flag' => 1]);
            $this->db->insert('upstream_hygiene_log', [
                'cid_id'       => $r['cid_id'],
                'event_type'   => 'near_miss',
                'days_at_event'=> $r['days_stagnant'],
                'rs_amount'    => 0,
                'notes'        => 'cs2 near_miss at ' . self::REACHOUT_NEAR_MISS_DAYS . 'd threshold',
            ]);
            $fired++;
        }

        return ['fired' => $fired];
    }

    // ------------------------------------------------------------------
    // PRIVATE: set stagnant_flag, fire wallet debit for rows crossing
    // the hard threshold. Does NOT fire auto-Lost (that is _process_auto_lost).
    // ------------------------------------------------------------------
    protected function _process_stagnant_flags()
    {
        $fired     = 0;
        $wallet_rs = 0;

        // cstatus 1: stagnant at 45 days.
        $rows_cs1 = $this->db->query("
            SELECT cid_id, bd_uid, cm_uid, days_stagnant
              FROM upstream_hygiene_state
             WHERE cstatus = 1
               AND days_stagnant >= ?
               AND stagnant_flag = 0
             LIMIT ?
        ", [self::OPEN_STAGNANT_DAYS, self::CRON_BATCH_LIMIT])->result_array();

        foreach ($rows_cs1 as $r) {
            $this->db->where('cid_id', $r['cid_id'])
                ->update('upstream_hygiene_state', ['stagnant_flag' => 1]);
            $this->db->insert('upstream_hygiene_log', [
                'cid_id'       => $r['cid_id'],
                'event_type'   => 'stagnant',
                'days_at_event'=> $r['days_stagnant'],
                'rs_amount'    => 0,
                'notes'        => 'cs1 stagnant at ' . self::OPEN_STAGNANT_DAYS . 'd',
            ]);
            $res = $this->debit_wallet(
                $r['cid_id'], $r['bd_uid'],
                'stagnant_45_open', self::WALLET_DEBIT_RS
            );
            if ($res['ok'] && empty($res['already_debited'])) {
                $wallet_rs += self::WALLET_DEBIT_RS;
            }
            $fired++;
        }

        // cstatus 2: stagnant at 30 days.
        $rows_cs2 = $this->db->query("
            SELECT cid_id, bd_uid, cm_uid, days_stagnant
              FROM upstream_hygiene_state
             WHERE cstatus = 2
               AND days_stagnant >= ?
               AND stagnant_flag = 0
             LIMIT ?
        ", [self::REACHOUT_STAGNANT_DAYS, self::CRON_BATCH_LIMIT])->result_array();

        foreach ($rows_cs2 as $r) {
            $this->db->where('cid_id', $r['cid_id'])
                ->update('upstream_hygiene_state', ['stagnant_flag' => 1]);
            $this->db->insert('upstream_hygiene_log', [
                'cid_id'       => $r['cid_id'],
                'event_type'   => 'stagnant',
                'days_at_event'=> $r['days_stagnant'],
                'rs_amount'    => 0,
                'notes'        => 'cs2 stagnant at ' . self::REACHOUT_STAGNANT_DAYS . 'd',
            ]);
            $res = $this->debit_wallet(
                $r['cid_id'], $r['bd_uid'],
                'stagnant_30_reachout', self::WALLET_DEBIT_RS
            );
            if ($res['ok'] && empty($res['already_debited'])) {
                $wallet_rs += self::WALLET_DEBIT_RS;
            }
            $fired++;
        }

        return ['fired' => $fired, 'wallet_rs' => $wallet_rs];
    }

    // ------------------------------------------------------------------
    // PRIVATE: fire auto-Lost for all stagnant rows with hard_flag = 0.
    // Called after _process_stagnant_flags in the same nightly run.
    // ------------------------------------------------------------------
    protected function _process_auto_lost()
    {
        $fired  = 0;
        $errors = [];

        // cstatus 1 auto-Lost.
        $rows_cs1 = $this->db->query("
            SELECT cid_id, bd_uid
              FROM upstream_hygiene_state
             WHERE cstatus = 1
               AND stagnant_flag = 1
               AND hard_flag = 0
             LIMIT ?
        ", [self::CRON_BATCH_LIMIT])->result_array();

        foreach ($rows_cs1 as $r) {
            try {
                $res = $this->auto_move_to_lost($r['cid_id'], self::LOST_REASON_OPEN);
                if ($res['ok'] && empty($res['already_lost']) && empty($res['skipped'])) {
                    $fired++;
                }
            } catch (Exception $e) {
                $errors[] = ['cid_id' => $r['cid_id'], 'exception' => $e->getMessage()];
                log_message('error', $this->log_prefix . ' auto_lost cs1 exception cid=' . $r['cid_id'] . ': ' . $e->getMessage());
            }
        }

        // cstatus 2 auto-Lost.
        $rows_cs2 = $this->db->query("
            SELECT cid_id, bd_uid
              FROM upstream_hygiene_state
             WHERE cstatus = 2
               AND stagnant_flag = 1
               AND hard_flag = 0
             LIMIT ?
        ", [self::CRON_BATCH_LIMIT])->result_array();

        foreach ($rows_cs2 as $r) {
            try {
                $res = $this->auto_move_to_lost($r['cid_id'], self::LOST_REASON_REACHOUT);
                if ($res['ok'] && empty($res['already_lost']) && empty($res['skipped'])) {
                    $fired++;
                }
            } catch (Exception $e) {
                $errors[] = ['cid_id' => $r['cid_id'], 'exception' => $e->getMessage()];
                log_message('error', $this->log_prefix . ' auto_lost cs2 exception cid=' . $r['cid_id'] . ': ' . $e->getMessage());
            }
        }

        return ['fired' => $fired, 'errors' => $errors];
    }

    // ------------------------------------------------------------------
    // PRIVATE: write or refresh block log row for a BD.
    // ------------------------------------------------------------------
    protected function _write_block_log($bd_uid, $reason)
    {
        $existing = $this->db->select('id')
            ->from('upstream_hygiene_block_log')
            ->where('bd_uid', $bd_uid)
            ->where('reason', $reason)
            ->where('unblocked_at IS NULL')
            ->get()->row_array();

        if (!$existing) {
            $this->db->insert('upstream_hygiene_block_log', [
                'bd_uid'    => $bd_uid,
                'reason'    => $reason,
                'blocked_at'=> date('Y-m-d H:i:s'),
            ]);
        }
    }

    // ------------------------------------------------------------------
    // PRIVATE: helper to read days_stagnant from state row.
    // ------------------------------------------------------------------
    protected function _get_days_stagnant($cid_id)
    {
        $row = $this->db->select('days_stagnant')
            ->from('upstream_hygiene_state')
            ->where('cid_id', (int)$cid_id)
            ->get()->row_array();
        return $row ? (int)$row['days_stagnant'] : 0;
    }
}

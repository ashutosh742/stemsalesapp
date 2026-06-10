<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * STEM Learning - Lead Query Tracker Agent
 * Migration 026 (Phase 2, live 1 Jul 2026)
 *
 * Responsibilities:
 *  1. Create lead_query_checklist rows from BD or CM input
 *  2. Compute SLA deadline based on query_type defaults
 *  3. Resolve queries (record resolution doc and note)
 *  4. Detect breaches and record penalties (5 points; CM scorecard or BD grade)
 *  5. Suggest queries auto-detected from MoM v2 next_meeting_purpose field
 *
 * Founder rule (verbatim): "Wherever any queries there that client want to
 * visit the School client one document documentation is all should be checked
 * and ensure that all these things are hard done within 48 hours"
 *
 * SLA defaults by query_type (hours):
 *   school_visit_request    -> 72   (needs coordination)
 *   documentation_check     -> 48
 *   budget_clarification    -> 48
 *   curriculum_alignment    -> 72
 *   site_readiness          -> 96
 *   principal_availability  -> 48
 *   tender_doc              -> 96
 *   csr_approval            -> 168  (1 week)
 *   product_demo            -> 96
 *   other                   -> 48
 *
 * Penalty rules:
 *   BD owner breach -> minus 5 grade points
 *   CM owner breach -> CM scorecard K17 impacted (cm_scorecard_impacted=1)
 *   3 plus breaches in a calendar week for the same owner -> notified_to_rm=1
 */
class Lead_query_tracker_agent
{
    const BREACH_PENALTY_POINTS        = 5;
    const RM_NOTIFY_BREACH_THRESHOLD   = 3;
    const CRON_BATCH_LIMIT             = 500;

    protected $CI;
    protected $db;
    protected $log_prefix = '[lead_query_tracker]';

    protected static $SLA_DEFAULTS = [
        'school_visit_request'   => 72,
        'documentation_check'    => 48,
        'budget_clarification'   => 48,
        'curriculum_alignment'   => 72,
        'site_readiness'         => 96,
        'principal_availability' => 48,
        'tender_doc'             => 96,
        'csr_approval'           => 168,
        'product_demo'           => 96,
        'other'                  => 48,
    ];

    public function __construct()
    {
        $this->CI =& get_instance();
        $this->CI->load->database();
        $this->db = $this->CI->db;
        $this->CI->load->model('planning_grade_model');
        $this->CI->load->model('line_manager_scorecard_model');
    }

    // -----------------------------------------------------------------
    // API: raise a new query
    // -----------------------------------------------------------------
    public function raise_query($payload)
    {
        $required = ['cid_id','query_type','query_text','raised_by_uid','raised_by_role','owner_uid','owner_role'];
        foreach ($required as $f) {
            if (!isset($payload[$f]) || $payload[$f] === '') {
                return ['ok' => false, 'error' => 'missing_' . $f];
            }
        }

        $query_type = $payload['query_type'];
        if (!isset(self::$SLA_DEFAULTS[$query_type])) {
            return ['ok' => false, 'error' => 'invalid_query_type'];
        }
        $sla_hours = isset($payload['sla_hours']) && is_numeric($payload['sla_hours'])
            ? max(1, min(240, (int)$payload['sla_hours']))
            : self::$SLA_DEFAULTS[$query_type];

        $raised_at = date('Y-m-d H:i:s');
        $sla_deadline = date('Y-m-d H:i:s', strtotime($raised_at) + ($sla_hours * 3600));

        $row = [
            'cid_id'         => (int)$payload['cid_id'],
            'query_type'     => $query_type,
            'query_text'     => substr(trim($payload['query_text']), 0, 500),
            'raised_by_uid'  => (int)$payload['raised_by_uid'],
            'raised_by_role' => $payload['raised_by_role'],
            'owner_uid'      => (int)$payload['owner_uid'],
            'owner_role'     => $payload['owner_role'],
            'sla_hours'      => $sla_hours,
            'raised_at'      => $raised_at,
            'sla_deadline'   => $sla_deadline,
            'status'         => 'open',
        ];

        $this->db->insert('lead_query_checklist', $row);
        $query_id = $this->db->insert_id();

        log_message('info', $this->log_prefix . " raised query_id={$query_id} cid={$row['cid_id']} type={$query_type} owner={$row['owner_uid']}/{$row['owner_role']} deadline={$sla_deadline}");
        return ['ok' => true, 'query_id' => $query_id, 'sla_deadline' => $sla_deadline];
    }

    // -----------------------------------------------------------------
    // API: mark a query in progress (owner picked it up)
    // -----------------------------------------------------------------
    public function mark_in_progress($query_id, $owner_uid)
    {
        $q = $this->db->select('*')->from('lead_query_checklist')->where('id', $query_id)->get()->row_array();
        if (!$q) return ['ok' => false, 'error' => 'query_not_found'];
        if ((int)$q['owner_uid'] !== (int)$owner_uid) return ['ok' => false, 'error' => 'owner_mismatch'];
        if ($q['status'] !== 'open') return ['ok' => false, 'error' => 'not_in_open_state'];

        $this->db->where('id', $query_id)->update('lead_query_checklist', ['status' => 'in_progress']);
        return ['ok' => true];
    }

    // -----------------------------------------------------------------
    // API: resolve a query
    // -----------------------------------------------------------------
    public function resolve_query($query_id, $owner_uid, $resolution_note, $resolution_doc_url = null)
    {
        $q = $this->db->select('*')->from('lead_query_checklist')->where('id', $query_id)->get()->row_array();
        if (!$q) return ['ok' => false, 'error' => 'query_not_found'];
        if ((int)$q['owner_uid'] !== (int)$owner_uid) return ['ok' => false, 'error' => 'owner_mismatch'];
        if (!in_array($q['status'], ['open','in_progress'])) return ['ok' => false, 'error' => 'not_resolvable'];
        if (empty($resolution_note) || strlen(trim($resolution_note)) < 5) {
            return ['ok' => false, 'error' => 'resolution_note_too_short'];
        }

        $this->db->where('id', $query_id)->update('lead_query_checklist', [
            'resolved_at'        => date('Y-m-d H:i:s'),
            'resolution_note'    => substr(trim($resolution_note), 0, 500),
            'resolution_doc_url' => $resolution_doc_url,
            'status'             => 'resolved',
        ]);

        log_message('info', $this->log_prefix . " resolved query_id={$query_id} by={$owner_uid}");
        return ['ok' => true];
    }

    // -----------------------------------------------------------------
    // API: drop a query (no longer relevant)
    // -----------------------------------------------------------------
    public function drop_query($query_id, $by_uid, $reason)
    {
        $q = $this->db->select('*')->from('lead_query_checklist')->where('id', $query_id)->get()->row_array();
        if (!$q) return ['ok' => false, 'error' => 'query_not_found'];

        $this->db->where('id', $query_id)->update('lead_query_checklist', [
            'resolved_at'     => date('Y-m-d H:i:s'),
            'resolution_note' => 'Dropped: ' . substr($reason, 0, 480),
            'status'          => 'dropped',
        ]);
        return ['ok' => true];
    }

    // -----------------------------------------------------------------
    // CLI: enforce_now - find breached queries, record penalty, escalate
    // Run every hour via cron.
    // -----------------------------------------------------------------
    public function enforce_now()
    {
        $now = date('Y-m-d H:i:s');
        $log = [
            'started_at'         => $now,
            'breaches_processed' => 0,
            'escalated_to_rm'    => 0,
            'errors'             => [],
        ];

        $breaches = $this->db
            ->select('*')
            ->from('lead_query_checklist')
            ->where_in('status', ['open','in_progress'])
            ->where('sla_deadline <=', $now)
            ->where('breach_processed_at IS NULL')
            ->limit(self::CRON_BATCH_LIMIT)
            ->get()->result_array();

        foreach ($breaches as $q) {
            try {
                $res = $this->_process_one_breach($q);
                if ($res['ok']) {
                    $log['breaches_processed']++;
                    if (!empty($res['notified_to_rm'])) $log['escalated_to_rm']++;
                } else {
                    $log['errors'][] = ['query_id' => $q['id'], 'error' => $res['error']];
                }
            } catch (Exception $e) {
                $log['errors'][] = ['query_id' => $q['id'], 'exception' => $e->getMessage()];
                log_message('error', $this->log_prefix . " exception q={$q['id']}: " . $e->getMessage());
            }
        }

        $log['finished_at'] = date('Y-m-d H:i:s');
        log_message('info', $this->log_prefix . ' enforce_now ' . json_encode($log));
        return $log;
    }

    // -----------------------------------------------------------------
    // Internal: process one query breach
    // -----------------------------------------------------------------
    protected function _process_one_breach($q)
    {
        $query_id  = (int)$q['id'];
        $owner_uid = (int)$q['owner_uid'];
        $owner_role = $q['owner_role'];

        $hours_overdue = round(
            (strtotime(date('Y-m-d H:i:s')) - strtotime($q['sla_deadline'])) / 3600, 2
        );

        $week_breaches = $this->db
            ->where('owner_uid', $owner_uid)
            ->where('breached_at >=', date('Y-m-d 00:00:00', strtotime('monday this week')))
            ->from('lead_query_breach_log')
            ->count_all_results();
        $notified_to_rm = ($week_breaches + 1) >= self::RM_NOTIFY_BREACH_THRESHOLD ? 1 : 0;

        $cm_scorecard_impacted = ($owner_role === 'cm') ? 1 : 0;
        $bd_grade_impacted     = ($owner_role === 'bd') ? 1 : 0;

        $this->db->insert('lead_query_breach_log', [
            'query_id'              => $query_id,
            'cid_id'                => (int)$q['cid_id'],
            'owner_uid'             => $owner_uid,
            'owner_role'            => $owner_role,
            'hours_overdue'         => $hours_overdue,
            'penalty_points'        => self::BREACH_PENALTY_POINTS,
            'cm_scorecard_impacted' => $cm_scorecard_impacted,
            'bd_grade_impacted'     => $bd_grade_impacted,
            'notified_to_rm'        => $notified_to_rm,
        ]);

        if ($bd_grade_impacted) {
            $this->CI->planning_grade_model->apply_penalty(
                $owner_uid,
                self::BREACH_PENALTY_POINTS,
                'lead_query_sla_breach',
                (int)$q['cid_id']
            );
        }
        if ($cm_scorecard_impacted) {
            $this->CI->line_manager_scorecard_model->record_query_breach(
                $owner_uid, (int)$q['cid_id'], self::BREACH_PENALTY_POINTS
            );
        }

        $this->db->where('id', $query_id)->update('lead_query_checklist', [
            'status'              => 'breached',
            'breach_processed_at' => date('Y-m-d H:i:s'),
        ]);

        log_message('info', $this->log_prefix . " breached query_id={$query_id} owner={$owner_uid}/{$owner_role} overdue={$hours_overdue}h rm_notify={$notified_to_rm}");
        return ['ok' => true, 'notified_to_rm' => $notified_to_rm];
    }

    // -----------------------------------------------------------------
    // Auto-suggest queries from MoM v2 next_meeting_purpose field
    // Called from MoM submission hook (migration 021 / 025 integration)
    // -----------------------------------------------------------------
    public function suggest_from_mom($mom_id)
    {
        $mom = $this->db->select('id, cid_id, bd_uid, cm_uid, next_meeting_purpose, queries_raised_text')
            ->from('mom_data')->where('id', $mom_id)->get()->row_array();
        if (!$mom) return ['ok' => false, 'error' => 'mom_not_found'];

        $suggestions = [];
        $text = strtolower(trim($mom['queries_raised_text'] ?? '') . ' ' . trim($mom['next_meeting_purpose'] ?? ''));
        if ($text === '') return ['ok' => true, 'suggestions' => []];

        $keyword_map = [
            'visit'                  => ['type' => 'school_visit_request',  'role' => 'bd'],
            'site visit'             => ['type' => 'school_visit_request',  'role' => 'bd'],
            'come to school'         => ['type' => 'school_visit_request',  'role' => 'bd'],
            'document'               => ['type' => 'documentation_check',   'role' => 'bd'],
            'paperwork'              => ['type' => 'documentation_check',   'role' => 'bd'],
            'budget'                 => ['type' => 'budget_clarification',  'role' => 'bd'],
            'price'                  => ['type' => 'budget_clarification',  'role' => 'bd'],
            'curriculum'             => ['type' => 'curriculum_alignment',  'role' => 'bd'],
            'syllabus'               => ['type' => 'curriculum_alignment',  'role' => 'bd'],
            'site readiness'         => ['type' => 'site_readiness',        'role' => 'bd'],
            'room'                   => ['type' => 'site_readiness',        'role' => 'bd'],
            'principal availability' => ['type' => 'principal_availability','role' => 'cm'],
            'principal meet'         => ['type' => 'principal_availability','role' => 'cm'],
            'tender'                 => ['type' => 'tender_doc',            'role' => 'bd'],
            'csr'                    => ['type' => 'csr_approval',          'role' => 'cm'],
            'demo'                   => ['type' => 'product_demo',          'role' => 'bd'],
        ];

        foreach ($keyword_map as $keyword => $config) {
            if (strpos($text, $keyword) !== false) {
                $owner_uid = ($config['role'] === 'cm') ? (int)$mom['cm_uid'] : (int)$mom['bd_uid'];
                if ($owner_uid <= 0) continue;
                $suggestions[] = [
                    'cid_id'         => (int)$mom['cid_id'],
                    'query_type'     => $config['type'],
                    'query_text'     => 'Auto-suggested from MoM: ' . substr($text, 0, 200),
                    'owner_uid'      => $owner_uid,
                    'owner_role'     => $config['role'],
                    'sla_hours'      => self::$SLA_DEFAULTS[$config['type']],
                ];
            }
        }

        $unique_types = [];
        $deduped = [];
        foreach ($suggestions as $s) {
            if (!isset($unique_types[$s['query_type']])) {
                $unique_types[$s['query_type']] = 1;
                $deduped[] = $s;
            }
        }
        return ['ok' => true, 'suggestions' => $deduped];
    }

    public function probe()
    {
        return [
            'migration' => '026',
            'phase'     => 2,
            'deployed'  => $this->db->table_exists('lead_query_checklist'),
            'sla_defaults' => self::$SLA_DEFAULTS,
            'breach_penalty_points' => self::BREACH_PENALTY_POINTS,
        ];
    }
}

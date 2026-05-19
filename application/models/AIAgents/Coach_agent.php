<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * STEM Learning - Coach Agent
 * Migration 036 (BD Coach + Greetings + Knowledge Repository)
 *
 * Responsibilities:
 *  1. Nightly skill signal computation and rolling score aggregation
 *  2. Daily drill assignment matched to largest skill gap
 *  3. Onboarding ladder management (30-60-90 day checkpoints)
 *  4. CM manual coaching adjustments (capped at +/- 10 pts/week)
 *
 * Stage-to-skill mapping:
 *  cstatus 1   -> prospecting, cold_opening, faq_recall
 *  cstatus 2   -> discovery, active_listening
 *  cstatus 3   -> pitching, product_demo, objection_handling
 *  cstatus 6   -> presentation, proposal_writing
 *  cstatus 7   -> followup, reference_handling, soft_closing
 *  cstatus 8   -> negotiation, stakeholder_mapping
 *  cstatus 9   -> closing, contract_clarity, referral_asking
 *  cstatus 12  -> onboard_handover, testimonial_capture, upsell_seed
 *
 * Designed to run from:
 *  - Cron 00:30 IST: compute_skill_scores nightly
 *  - Cron 06:45 IST: assign_drill for each BD
 *  - API: /api/coach/* (Coach controller)
 *
 * Feature flag: feature_flag.coach_036_enabled (0=off, 1=pilot, 2=org)
 *
 * Migration 036. Author: STEM ops, 2026-05-18.
 */
class Coach_agent extends CI_Model
{
    // ------------------------------------------------------------------
    // Stage-to-skill map. Keys are cstatus values.
    // ------------------------------------------------------------------
    private $stage_skill_map = [
        1  => ['prospecting', 'cold_opening', 'faq_recall'],
        2  => ['discovery', 'active_listening'],
        3  => ['pitching', 'product_demo', 'objection_handling'],
        6  => ['presentation', 'proposal_writing'],
        7  => ['followup', 'reference_handling', 'soft_closing'],
        8  => ['negotiation', 'stakeholder_mapping'],
        9  => ['closing', 'contract_clarity', 'referral_asking'],
        12 => ['onboard_handover', 'testimonial_capture', 'upsell_seed'],
    ];

    // Onboarding checkpoint definitions keyed by day_offset.
    private $onboarding_checkpoints = [
        1  => ['module' => 'Product walkthrough', 'description' => 'Pass product quiz 8 of 10', 'owner_role' => 'self'],
        3  => ['module' => 'Barge meeting shadow', 'description' => 'MoM signed by senior BD', 'owner_role' => 'senior_bd'],
        5  => ['module' => 'Pitch role-play with CM', 'description' => 'Recording scored A or B', 'owner_role' => 'cm'],
        10 => ['module' => 'First 10 prospect research calls', 'description' => '10 init_call rows with notes', 'owner_role' => 'cm'],
        15 => ['module' => 'First proposal draft', 'description' => 'Coach grade B or above', 'owner_role' => 'cm'],
        30 => ['module' => 'First Tentative meeting solo', 'description' => 'MoM signed, cstatus 3 reached', 'owner_role' => 'cm'],
        45 => ['module' => '5 Positive leads in pipeline', 'description' => 'cstatus 6 count >= 5', 'owner_role' => 'rm'],
        60 => ['module' => 'First Proposal sent autonomously', 'description' => 'Coach pre-approval + sent', 'owner_role' => 'rm'],
        90 => ['module' => 'First Won closure or clear path', 'description' => 'Won or 2 Very Positive with named DM', 'owner_role' => 'director'],
    ];

    // Manual adjustment cap per week in points.
    const MANUAL_ADJ_CAP_PTS = 10;

    // Rolling score window in days.
    const ROLLING_WINDOW_DAYS = 30;

    // Batch limit for nightly cron.
    const CRON_BATCH_LIMIT = 200;

    public function __construct()
    {
        parent::__construct();
        $this->load->database();
    }

    // ==========================================================================
    // SKILL SCORING
    // ==========================================================================

    /**
     * Nightly cron entry point.
     * Calls sp_compute_skill_scores_nightly then returns updated scores.
     *
     * @param  int    $uid          BD user id. 0 = all BDs.
     * @param  string $period_start Y-m-d
     * @param  string $period_end   Y-m-d
     * @return array  ['ok', 'scores' => [...], 'bd_uid']
     */
    public function compute_skill_scores($uid, $period_start, $period_end)
    {
        $uid          = (int)$uid;
        $period_start = $this->db->escape_str($period_start);
        $period_end   = $this->db->escape_str($period_end);

        // Call the stored procedure that aggregates signals into bd_skill_score.
        $this->db->query('CALL sp_compute_skill_scores_nightly()');

        // Return the updated scores for the requested BD (or all).
        $sql = "
            SELECT s.bd_uid, s.skill_code, s.current_score, s.grade,
                   s.signals_30d_count, s.last_updated,
                   sd.skill_name, sd.category
              FROM bd_skill_score s
              LEFT JOIN skill_definition sd ON sd.skill_code = s.skill_code
             WHERE s.last_updated BETWEEN ? AND ?
        ";
        $params = [$period_start . ' 00:00:00', $period_end . ' 23:59:59'];
        if ($uid > 0) {
            $sql   .= ' AND s.bd_uid = ?';
            $params[] = $uid;
        }
        $sql .= ' ORDER BY s.bd_uid, s.current_score ASC';

        $scores = $this->db->query($sql, $params)->result_array();
        return ['ok' => true, 'bd_uid' => $uid, 'scores' => $scores];
    }

    // ------------------------------------------------------------------

    /**
     * Assign a coaching drill matched to the BD's lowest-grade skill.
     *
     * @param  int    $uid        BD user id
     * @param  string $skill_code Specific skill to target (or auto-pick from gap)
     * @param  string $level      new|mid|senior
     * @return array  ['ok', 'drill_id', 'drill_title', 'due_date']
     */
    public function assign_drill($uid, $skill_code, $level)
    {
        $uid        = (int)$uid;
        $skill_code = $this->db->escape_str($skill_code);
        $level      = in_array($level, ['new', 'mid', 'senior']) ? $level : 'new';

        // Pick a suitable active drill for this skill and level.
        $drill = $this->db->query("
            SELECT id, title, duration_sec, drill_type, content_url
              FROM coaching_drill
             WHERE skill_code = ?
               AND (difficulty = ? OR difficulty IS NULL)
               AND active = 1
             ORDER BY RAND()
             LIMIT 1
        ", [$skill_code, $level])->row_array();

        if (empty($drill)) {
            // Fallback: any active drill for this skill.
            $drill = $this->db->query("
                SELECT id, title, duration_sec, drill_type, content_url
                  FROM coaching_drill
                 WHERE skill_code = ?
                   AND active = 1
                 ORDER BY RAND()
                 LIMIT 1
            ", [$skill_code])->row_array();
        }

        if (empty($drill)) {
            return ['ok' => false, 'error' => 'no_drill_found', 'skill_code' => $skill_code];
        }

        $assigned_date = date('Y-m-d');
        $due_date      = date('Y-m-d', strtotime('+2 days'));

        $this->db->trans_start();
        $this->db->query("
            INSERT INTO coaching_assignment
                (bd_uid, drill_id, assigned_date)
            VALUES (?, ?, ?)
        ", [$uid, (int)$drill['id'], $assigned_date]);
        $insert_id = $this->db->insert_id();
        $this->db->trans_complete();

        if ($this->db->trans_status() === false) {
            return ['ok' => false, 'error' => 'db_transaction_failed'];
        }

        return [
            'ok'          => true,
            'assignment_id' => $insert_id,
            'drill_id'    => (int)$drill['id'],
            'drill_title' => $drill['title'],
            'drill_type'  => $drill['drill_type'],
            'duration_sec'=> (int)$drill['duration_sec'],
            'content_url' => $drill['content_url'],
            'due_date'    => $due_date,
        ];
    }

    // ------------------------------------------------------------------

    /**
     * Return top skill gaps for a BD from the pre-computed view.
     *
     * @param  int $uid BD user id
     * @return array
     */
    public function get_skill_gaps($uid)
    {
        $uid = (int)$uid;
        if (!$uid) return ['ok' => false, 'error' => 'missing_uid'];

        $rows = $this->db->query("
            SELECT v.skill_code, v.skill_name, v.current_score, v.grade,
                   v.drill_id, v.drill_title, v.drill_type, v.drill_content_url
              FROM v_bd_skill_gaps_today v
             WHERE v.bd_uid = ?
             ORDER BY v.current_score ASC
             LIMIT 3
        ", [$uid])->result_array();

        return ['ok' => true, 'bd_uid' => $uid, 'gaps' => $rows];
    }

    // ------------------------------------------------------------------

    /**
     * Insert a raw skill signal row.
     *
     * @param  int    $uid            BD user id
     * @param  string $skill_code
     * @param  string $signal_type    positive|gap|critical_gap
     * @param  float  $signal_value   score_delta -3..3
     * @param  int    $source_event_id tblcallevents id or 0
     * @return array  ['ok', 'signal_id']
     */
    public function log_skill_signal($uid, $skill_code, $signal_type, $signal_value, $source_event_id)
    {
        $uid            = (int)$uid;
        $skill_code     = $this->db->escape_str($skill_code);
        $signal_type    = in_array($signal_type, ['positive', 'gap', 'critical_gap']) ? $signal_type : 'gap';
        $signal_value   = max(-3, min(3, (float)$signal_value));
        $source_event_id = (int)$source_event_id;

        if (!$uid || !$skill_code) {
            return ['ok' => false, 'error' => 'missing_required_fields'];
        }

        $this->db->trans_start();
        $this->db->query("
            INSERT INTO bd_skill_signal
                (bd_uid, skill_code, signal_type, score_delta,
                 evidence_type, evidence_ref_id, source, observed_at)
            VALUES (?, ?, ?, ?, 'callevent', ?, 'agent', NOW())
        ", [$uid, $skill_code, $signal_type, $signal_value, $source_event_id]);
        $signal_id = $this->db->insert_id();
        $this->db->trans_complete();

        if ($this->db->trans_status() === false) {
            return ['ok' => false, 'error' => 'db_transaction_failed'];
        }

        return ['ok' => true, 'signal_id' => $signal_id];
    }

    // ==========================================================================
    // ONBOARDING LADDER
    // ==========================================================================

    /**
     * Return all 9 checkpoint statuses for a BD.
     *
     * @param  int $uid BD user id
     * @return array
     */
    public function onboarding_status($uid)
    {
        $uid = (int)$uid;
        if (!$uid) return ['ok' => false, 'error' => 'missing_uid'];

        // Ensure checkpoint rows exist; seed any missing ones.
        $this->_seed_onboarding_checkpoints($uid);

        $rows = $this->db->query("
            SELECT oc.id, oc.day_offset, oc.module_name, oc.checkpoint_description,
                   oc.owner_role, oc.status, oc.evidence_ref,
                   oc.completed_at, oc.target_due_at
              FROM onboarding_checkpoint oc
             WHERE oc.bd_uid = ?
             ORDER BY oc.day_offset ASC
        ", [$uid])->result_array();

        // Compute summary stats.
        $passed  = 0;
        $failed  = 0;
        $pending = 0;
        foreach ($rows as $r) {
            if ($r['status'] === 'passed')  $passed++;
            if ($r['status'] === 'failed')  $failed++;
            if ($r['status'] === 'pending') $pending++;
        }

        return [
            'ok'           => true,
            'bd_uid'       => $uid,
            'checkpoints'  => $rows,
            'summary'      => [
                'total'   => count($rows),
                'passed'  => $passed,
                'failed'  => $failed,
                'pending' => $pending,
            ],
        ];
    }

    // ------------------------------------------------------------------

    /**
     * Mark a checkpoint passed/failed with evidence.
     *
     * @param  int    $uid             BD user id
     * @param  string $checkpoint_code day_offset (1,3,5,...,90) as string
     * @param  string $status          passed|failed|in_progress|skipped
     * @param  string $evidence_json   JSON string of evidence references
     * @return array  ['ok', 'checkpoint_id']
     */
    public function mark_checkpoint($uid, $checkpoint_code, $status, $evidence_json)
    {
        $uid             = (int)$uid;
        $day_offset      = (int)$checkpoint_code;
        $allowed_statuses = ['pending', 'in_progress', 'passed', 'failed', 'skipped'];
        $status          = in_array($status, $allowed_statuses) ? $status : 'in_progress';
        $evidence_json   = $evidence_json ?: '{}';

        if (!$uid || !$day_offset) {
            return ['ok' => false, 'error' => 'missing_required_fields'];
        }

        $this->db->trans_start();

        // Upsert: update if exists, insert if not.
        $existing = $this->db->query("
            SELECT id FROM onboarding_checkpoint
             WHERE bd_uid = ? AND day_offset = ?
             LIMIT 1
        ", [$uid, $day_offset])->row_array();

        $completed_at = in_array($status, ['passed', 'failed', 'skipped']) ? date('Y-m-d H:i:s') : null;

        if ($existing) {
            $this->db->query("
                UPDATE onboarding_checkpoint
                   SET status = ?, evidence_ref = ?, completed_at = ?
                 WHERE id = ?
            ", [$status, $evidence_json, $completed_at, (int)$existing['id']]);
            $checkpoint_id = (int)$existing['id'];
        } else {
            $def = isset($this->onboarding_checkpoints[$day_offset])
                 ? $this->onboarding_checkpoints[$day_offset]
                 : ['module' => 'Custom', 'description' => '', 'owner_role' => 'cm'];

            $due_at = date('Y-m-d', strtotime("+{$day_offset} days",
                     strtotime($this->_bd_join_date($uid))));

            $this->db->query("
                INSERT INTO onboarding_checkpoint
                    (bd_uid, day_offset, module_name, checkpoint_description,
                     owner_role, status, evidence_ref, completed_at, target_due_at)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
            ", [$uid, $day_offset, $def['module'], $def['description'],
               $def['owner_role'], $status, $evidence_json, $completed_at, $due_at]);
            $checkpoint_id = $this->db->insert_id();
        }

        $this->db->trans_complete();

        if ($this->db->trans_status() === false) {
            return ['ok' => false, 'error' => 'db_transaction_failed'];
        }

        return ['ok' => true, 'checkpoint_id' => $checkpoint_id, 'status' => $status];
    }

    // ==========================================================================
    // CM MANUAL ADJUSTMENT
    // ==========================================================================

    /**
     * Apply a CM manual skill score adjustment for a BD.
     * Hard cap at +/- 10 points net per week per (BD, skill).
     *
     * @param  int    $cm_uid
     * @param  int    $bd_uid
     * @param  string $skill_code
     * @param  float  $delta_pts   Positive or negative, capped at +/- 10
     * @param  string $note        Free text reason (max 500 chars)
     * @return array  ['ok', 'applied_delta', 'weekly_total']
     */
    public function apply_cm_manual_adjustment($cm_uid, $bd_uid, $skill_code, $delta_pts, $note)
    {
        $cm_uid     = (int)$cm_uid;
        $bd_uid     = (int)$bd_uid;
        $skill_code = $this->db->escape_str($skill_code);
        $delta_pts  = (float)$delta_pts;
        $note       = substr((string)$note, 0, 500);

        if (!$cm_uid || !$bd_uid || !$skill_code) {
            return ['ok' => false, 'error' => 'missing_required_fields'];
        }

        // Enforce +/- 10 pt weekly cap per (bd_uid, skill_code).
        $week_start = date('Y-m-d', strtotime('monday this week'));
        $week_end   = date('Y-m-d', strtotime('sunday this week'));

        $weekly_used = (float)$this->db->query("
            SELECT COALESCE(SUM(score_delta), 0) AS total
              FROM bd_skill_signal
             WHERE bd_uid = ?
               AND skill_code = ?
               AND source = 'cm'
               AND DATE(observed_at) BETWEEN ? AND ?
        ", [$bd_uid, $skill_code, $week_start, $week_end])->row('total');

        $remaining_cap = self::MANUAL_ADJ_CAP_PTS - abs($weekly_used);
        if ($remaining_cap <= 0) {
            return [
                'ok'           => false,
                'error'        => 'weekly_cap_reached',
                'weekly_total' => $weekly_used,
                'cap'          => self::MANUAL_ADJ_CAP_PTS,
            ];
        }

        // Clip delta to remaining cap (preserve sign).
        $sign            = ($delta_pts >= 0) ? 1 : -1;
        $applied_delta   = $sign * min(abs($delta_pts), $remaining_cap);

        $signal_type = ($applied_delta >= 0) ? 'positive' : 'gap';

        $this->db->trans_start();

        // Insert a skill signal with source=cm.
        $this->db->query("
            INSERT INTO bd_skill_signal
                (bd_uid, skill_code, signal_type, score_delta,
                 evidence_type, note, source, observed_at)
            VALUES (?, ?, ?, ?, 'huddle', ?, 'cm', NOW())
        ", [$bd_uid, $skill_code, $signal_type, $applied_delta, $note]);

        // Write the manual adjustment to bd_skill_score if the row exists.
        $this->db->query("
            UPDATE bd_skill_score
               SET manual_adjustment_pts = COALESCE(manual_adjustment_pts, 0) + ?,
                   last_updated = NOW()
             WHERE bd_uid = ? AND skill_code = ?
        ", [$applied_delta, $bd_uid, $skill_code]);

        // Log the CM action.
        $this->db->query("
            INSERT INTO coach_gate_signal
                (bd_uid, signal_type, signal_value, triggered_by_uid,
                 note, created_at)
            VALUES (?, 'cm_manual_adjustment', ?, ?, ?, NOW())
        ", [$bd_uid, $applied_delta, $cm_uid, $note]);

        $this->db->trans_complete();

        if ($this->db->trans_status() === false) {
            return ['ok' => false, 'error' => 'db_transaction_failed'];
        }

        return [
            'ok'            => true,
            'applied_delta' => $applied_delta,
            'weekly_total'  => $weekly_used + $applied_delta,
            'cap'           => self::MANUAL_ADJ_CAP_PTS,
        ];
    }

    // ==========================================================================
    // PRIVATE HELPERS
    // ==========================================================================

    /**
     * Return BD's join_date from the user table.
     *
     * @param  int $uid
     * @return string Y-m-d
     */
    private function _bd_join_date($uid)
    {
        $row = $this->db->query("
            SELECT DATE(joined_at) AS jd FROM user WHERE uid = ? LIMIT 1
        ", [(int)$uid])->row_array();
        return $row ? $row['jd'] : date('Y-m-d');
    }

    // ------------------------------------------------------------------

    /**
     * Seed the 9 onboarding checkpoint rows for a BD if they do not exist yet.
     *
     * @param  int $uid
     * @return void
     */
    private function _seed_onboarding_checkpoints($uid)
    {
        $uid      = (int)$uid;
        $join_date = $this->_bd_join_date($uid);

        foreach ($this->onboarding_checkpoints as $day_offset => $def) {
            $due_at = date('Y-m-d', strtotime("+{$day_offset} days", strtotime($join_date)));
            $this->db->query("
                INSERT IGNORE INTO onboarding_checkpoint
                    (bd_uid, day_offset, module_name, checkpoint_description,
                     owner_role, status, target_due_at)
                VALUES (?, ?, ?, ?, ?, 'pending', ?)
            ", [$uid, $day_offset, $def['module'], $def['description'],
               $def['owner_role'], $due_at]);
        }
    }

    // ------------------------------------------------------------------

    /**
     * Map a cstatus to required skill codes.
     *
     * @param  int $cstatus
     * @return array
     */
    public function skills_for_cstatus($cstatus)
    {
        return isset($this->stage_skill_map[(int)$cstatus])
             ? $this->stage_skill_map[(int)$cstatus]
             : [];
    }

    // ------------------------------------------------------------------

    /**
     * Evaluate a single lead and write skill signal rows.
     * Called by nightly cron. Inspects last 7 days of tblcallevents + MoMs.
     *
     * @param  int $lead_id init_call.id
     * @return array ['signals_written']
     */
    public function evaluate_lead_skill_signals($lead_id)
    {
        $lead_id = (int)$lead_id;

        $lead = $this->db->query("
            SELECT id, mainbd, cstatus FROM init_call WHERE id = ? LIMIT 1
        ", [$lead_id])->row_array();

        if (empty($lead)) return ['ok' => false, 'error' => 'lead_not_found'];

        $bd_uid  = (int)$lead['mainbd'];
        $cstatus = (int)$lead['cstatus'];

        $required_skills = $this->skills_for_cstatus($cstatus);
        if (empty($required_skills)) return ['ok' => true, 'signals_written' => 0];

        // Count call events in last 7 days.
        $event_count = (int)$this->db->query("
            SELECT COUNT(*) AS cnt FROM tblcallevents
             WHERE cid_id = ?
               AND DATE(event_date) >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)
        ", [$lead_id])->row('cnt');

        // Check for MoM in last 7 days.
        $mom_count = (int)$this->db->query("
            SELECT COUNT(*) AS cnt FROM mom_data m
             INNER JOIN tblcallevents t ON t.id = m.event_id
             WHERE t.cid_id = ?
               AND DATE(t.event_date) >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)
        ", [$lead_id])->row('cnt');

        $written = 0;
        foreach ($required_skills as $skill_code) {
            // Simple heuristic: positive if there are events with MoM, gap if events with no MoM.
            if ($event_count > 0 && $mom_count > 0) {
                $signal_type = 'positive';
                $score_delta = 1;
            } elseif ($event_count > 0 && $mom_count === 0) {
                $signal_type = 'gap';
                $score_delta = -1;
            } else {
                $signal_type = 'critical_gap';
                $score_delta = -2;
            }

            $this->db->query("
                INSERT INTO bd_skill_signal
                    (bd_uid, lead_id, cstatus_at_observation, skill_code,
                     signal_type, score_delta, evidence_type, source, observed_at)
                VALUES (?, ?, ?, ?, ?, ?, 'callevent', 'agent', NOW())
            ", [$bd_uid, $lead_id, $cstatus, $skill_code, $signal_type, $score_delta]);
            $written++;
        }

        return ['ok' => true, 'signals_written' => $written, 'lead_id' => $lead_id];
    }

    // ------------------------------------------------------------------

    /**
     * Nightly batch: evaluate all active leads then compute rolling scores.
     * Entry point for cron 00:30 IST.
     *
     * @return array summary log
     */
    public function run_nightly_batch()
    {
        $log = [
            'started_at'     => date('Y-m-d H:i:s'),
            'leads_processed'=> 0,
            'signals_written'=> 0,
            'errors'         => [],
        ];

        $active_cstatuses = [1, 2, 3, 6, 7, 8, 9, 12];
        $in_clause        = implode(',', $active_cstatuses);

        $leads = $this->db->query("
            SELECT id, mainbd, cstatus FROM init_call
             WHERE cstatus IN ({$in_clause})
               AND mainbd IS NOT NULL
               AND mainbd > 0
             LIMIT " . self::CRON_BATCH_LIMIT
        )->result_array();

        foreach ($leads as $lead) {
            try {
                $res = $this->evaluate_lead_skill_signals((int)$lead['id']);
                if (!empty($res['ok'])) {
                    $log['leads_processed']++;
                    $log['signals_written'] += (int)($res['signals_written'] ?? 0);
                } else {
                    $log['errors'][] = ['lead_id' => $lead['id'], 'error' => $res['error'] ?? 'unknown'];
                }
            } catch (Exception $e) {
                $log['errors'][] = ['lead_id' => $lead['id'], 'exception' => $e->getMessage()];
                log_message('error', '[coach_agent] nightly exception lead=' . $lead['id'] . ' ' . $e->getMessage());
            }
        }

        // Roll up signals into bd_skill_score via SP.
        $this->db->query('CALL sp_compute_skill_scores_nightly()');

        $log['finished_at'] = date('Y-m-d H:i:s');
        log_message('info', '[coach_agent] nightly_batch ' . json_encode($log));
        return $log;
    }

    // ------------------------------------------------------------------

    /**
     * Return coaching assignments for a BD.
     *
     * @param  int $uid BD user id
     * @return array
     */
    public function get_drill_list($uid)
    {
        $uid = (int)$uid;
        if (!$uid) return ['ok' => false, 'error' => 'missing_uid'];

        $rows = $this->db->query("
            SELECT ca.id, ca.drill_id, ca.assigned_date, ca.completed_at,
                   ca.self_rating, ca.cm_rating, ca.notes,
                   cd.skill_code, cd.drill_type, cd.title,
                   cd.duration_sec, cd.content_url
              FROM coaching_assignment ca
              LEFT JOIN coaching_drill cd ON cd.id = ca.drill_id
             WHERE ca.bd_uid = ?
             ORDER BY ca.assigned_date DESC
             LIMIT 30
        ", [$uid])->result_array();

        return ['ok' => true, 'bd_uid' => $uid, 'assignments' => $rows];
    }
}

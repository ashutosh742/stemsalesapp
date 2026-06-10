<?php
defined('BASEPATH') OR exit('No direct script access allowed');
date_default_timezone_set("Asia/Kolkata");

/**
 * Review_v2_model - STEM Review v2 (migration 020)
 *
 * Owns the review v2 surface:
 *   - schedule a review (bootstrap + per-cadence cron creates pending rows)
 *   - launch a session (BD self-assessment first, then manager session)
 *   - auto-pull 30-KPI snapshot from existing migration views
 *   - save ratings (both BD self and manager) with gap_flag computed by trigger
 *   - manage action items
 *   - daily skip-level register populated by cron 0c647bbd 7:30 AM
 *   - plan-submit gate check (called from existing planner submit flow)
 *
 * @property CI_DB_query_builder $db
 * @property CI_Config $config
 */
class Review_v2_model extends CI_Model {

    public function __construct() {
        parent::__construct();
        $this->load->database();
    }

    /* ===================================================================
     * 1) SCHEDULE management
     * =================================================================== */

    /**
     * Get reviews pending for a manager (today + next 7 days)
     */
    public function pending_for_manager($manager_uid) {
        $sql = "SELECT * FROM v_review_pending_for_manager
                WHERE manager_uid = ? ORDER BY scheduled_date ASC";
        return $this->db->query($sql, [$manager_uid])->result();
    }

    /**
     * Get reviews due today for a BD (self-assessment)
     */
    public function pending_for_bd($bd_uid) {
        $sql = "SELECT rs.*, rt.name AS review_type_name,
                       ud.name AS manager_name
                FROM review_schedule rs
                LEFT JOIN review_types rt ON rt.id = rs.review_type_id
                LEFT JOIN user_details ud ON ud.user_id = rs.manager_uid
                WHERE rs.bd_uid = ?
                  AND rs.status IN ('pending','in_progress')
                  AND rs.scheduled_date <= DATE_ADD(CURDATE(), INTERVAL 1 DAY)
                ORDER BY rs.scheduled_date ASC";
        return $this->db->query($sql, [$bd_uid])->result();
    }

    /**
     * Bootstrap pilot schedule (called once after CM confirms cluster mapping)
     */
    public function bootstrap_pilot_schedule() {
        // Pilot BDs: 42 (Priya), 43 (Ravi), 44 (Anita), 45 (Vikram), 46 (Sneha)
        // Manager: CM Anjali uid=12
        // First weekly review: Mon 1 Jun 2026 (one week after pilot start)
        $pilot_bds = [42, 43, 44, 45, 46];
        $manager_uid = 12;
        $first_review_date = '2026-06-01'; // Monday after pilot start
        $review_type_id = 1; // Weekly

        foreach ($pilot_bds as $bd_uid) {
            $exists = $this->db->where([
                'bd_uid' => $bd_uid,
                'review_type_id' => $review_type_id,
                'scheduled_date' => $first_review_date
            ])->count_all_results('review_schedule');
            if ($exists == 0) {
                $this->db->insert('review_schedule', [
                    'bd_uid' => $bd_uid,
                    'manager_uid' => $manager_uid,
                    'review_type_id' => $review_type_id,
                    'scheduled_date' => $first_review_date,
                    'min_duration_minutes' => 25,
                    'status' => 'pending'
                ]);
            }
        }
        return ['inserted_for' => $pilot_bds, 'first_date' => $first_review_date];
    }

    /**
     * Compute next scheduled date per BD cadence rules
     * - Pilot or grade-C/D last week => weekly (next Monday)
     * - All others => fortnightly
     */
    public function compute_next_schedule_date($bd_uid, $last_session_date = null) {
        // Default fortnightly
        $cadence_days = 14;
        // If BD had grade C/D in latest planning_grade row, switch to weekly
        $grade = $this->db->select('grade_band')
            ->where('bd_uid', $bd_uid)
            ->order_by('plan_date', 'DESC')
            ->limit(1)
            ->get('bd_progression_daily');
        if ($grade && $grade->num_rows() > 0) {
            $g = $grade->row()->grade_band;
            if (in_array($g, ['C','D'])) $cadence_days = 7;
        }
        // If BD in pilot uids
        if (in_array($bd_uid, [42,43,44,45,46])) $cadence_days = 7;

        $base = $last_session_date ?: date('Y-m-d');
        $next = date('Y-m-d', strtotime($base . " +{$cadence_days} days"));
        // Snap to next Monday
        $dow = date('N', strtotime($next));
        if ($dow > 1) {
            $next = date('Y-m-d', strtotime($next . ' +' . (8 - $dow) . ' days'));
        }
        return $next;
    }

    /* ===================================================================
     * 2) SESSION lifecycle
     * =================================================================== */

    /**
     * Start session - called when BD opens self-assessment OR manager opens session
     */
    public function start_session($schedule_id, $by_uid_optional = null) {
        $sch = $this->db->get_where('review_schedule', ['id' => $schedule_id])->row();
        if (!$sch) return ['error' => 'schedule_not_found'];

        $window_days = $this->_window_for_type($sch->review_type_id);
        $window_to = date('Y-m-d', strtotime('-1 day'));
        $window_from = date('Y-m-d', strtotime("{$window_to} -{$window_days} days"));

        // Check if session already exists for this schedule
        $existing = $this->db->get_where('review_session_v2', ['schedule_id' => $schedule_id])->row();
        if ($existing) {
            return ['session_id' => $existing->id, 'status' => $existing->status, 'reused' => true];
        }

        $this->db->insert('review_session_v2', [
            'schedule_id' => $schedule_id,
            'by_uid' => $sch->manager_uid,
            'to_uid' => $sch->bd_uid,
            'review_type_id' => $sch->review_type_id,
            'window_from' => $window_from,
            'window_to' => $window_to,
            'started_at' => date('Y-m-d H:i:s'),
            'status' => 'scheduled'
        ]);
        $session_id = $this->db->insert_id();

        $this->db->update('review_schedule', ['status' => 'in_progress'], ['id' => $schedule_id]);

        // Auto-pull KPI snapshot
        $this->snapshot_kpis_for_session($session_id);

        return ['session_id' => $session_id, 'window_from' => $window_from, 'window_to' => $window_to, 'reused' => false];
    }

    private function _window_for_type($review_type_id) {
        switch ((int)$review_type_id) {
            case 1: return 7;    // Weekly
            case 2: return 15;   // Fortnightly
            case 3: return 30;   // Monthly
            case 4: return 90;   // Quarterly
            case 5: return 180;  // Half yearly
            case 6: return 365;  // Annual
            default: return 7;
        }
    }

    /* ===================================================================
     * 3) KPI auto-pull
     * =================================================================== */

    /**
     * For each metric in review_metric_catalog, compute the kpi_value for the
     * BD over the session window and INSERT/UPDATE the rating row (ratings stay NULL).
     */
    public function snapshot_kpis_for_session($session_id) {
        $session = $this->db->get_where('review_session_v2', ['id' => $session_id])->row();
        if (!$session) return false;
        $bd_uid = $session->to_uid;
        $win_from = $session->window_from;
        $win_to = $session->window_to;

        $metrics = $this->db->where('is_active', 1)->get('review_metric_catalog')->result();
        $inserted = 0;
        foreach ($metrics as $m) {
            $val = $this->_compute_metric($m->metric_key, $bd_uid, $win_from, $win_to);

            // Upsert
            $existing = $this->db->get_where('review_metric_rating_v2', [
                'session_id' => $session_id,
                'metric_key' => $m->metric_key
            ])->row();
            $payload = [
                'session_id' => $session_id,
                'metric_key' => $m->metric_key,
                'kpi_value' => is_numeric($val) ? $val : null,
                'kpi_value_text' => is_numeric($val) ? null : (is_string($val) ? $val : json_encode($val))
            ];
            if ($existing) {
                $this->db->where('id', $existing->id)->update('review_metric_rating_v2', $payload);
            } else {
                $this->db->insert('review_metric_rating_v2', $payload);
                $inserted++;
            }
        }
        return ['metrics_snapshotted' => count($metrics), 'inserted' => $inserted];
    }

    /**
     * Compute a single metric value. Each metric_key maps to a SQL query
     * against existing migration views. If view is missing, return null.
     */
    private function _compute_metric($key, $bd_uid, $win_from, $win_to) {
        // Pattern: bind to existing views from migrations 008-019.
        // We keep this in one switch for clarity; production can break it out.
        switch ($key) {
            case 'won_rs_in_window':
                $q = "SELECT COALESCE(SUM(closed_value_rs),0) AS v
                      FROM lead_progression_log
                      WHERE bd_uid = ? AND to_cstatus = 12
                        AND DATE(created_at) BETWEEN ? AND ?";
                $r = $this->db->query($q, [$bd_uid, $win_from, $win_to])->row();
                return $r ? (float)$r->v : 0;

            case 'won_count':
                $q = "SELECT COUNT(*) AS v
                      FROM lead_progression_log
                      WHERE bd_uid = ? AND to_cstatus = 12
                        AND DATE(created_at) BETWEEN ? AND ?";
                $r = $this->db->query($q, [$bd_uid, $win_from, $win_to])->row();
                return $r ? (int)$r->v : 0;

            case 'avg_deal_size_rs':
                $q = "SELECT COALESCE(AVG(closed_value_rs),0) AS v
                      FROM lead_progression_log
                      WHERE bd_uid = ? AND to_cstatus = 12
                        AND DATE(created_at) BETWEEN ? AND ?";
                $r = $this->db->query($q, [$bd_uid, $win_from, $win_to])->row();
                return $r ? round((float)$r->v, 2) : 0;

            case 'positive_conversion_count':
                $q = "SELECT COUNT(*) AS v FROM lead_progression_log
                      WHERE bd_uid = ? AND to_cstatus = 6
                        AND DATE(created_at) BETWEEN ? AND ?";
                $r = $this->db->query($q, [$bd_uid, $win_from, $win_to])->row();
                return $r ? (int)$r->v : 0;

            case 'very_positive_count':
                $q = "SELECT COUNT(*) AS v FROM lead_progression_log
                      WHERE bd_uid = ? AND to_cstatus = 9
                        AND DATE(created_at) BETWEEN ? AND ?";
                $r = $this->db->query($q, [$bd_uid, $win_from, $win_to])->row();
                return $r ? (int)$r->v : 0;

            case 'plans_submitted_on_time_pct':
                $q = "SELECT
                        CASE WHEN COUNT(*) = 0 THEN NULL
                        ELSE ROUND(SUM(submitted_by_cutoff = 1) * 100.0 / COUNT(*), 2) END AS v
                      FROM daily_planner
                      WHERE bd_uid = ? AND plan_date BETWEEN ? AND ?";
                $r = $this->db->query($q, [$bd_uid, $win_from, $win_to])->row();
                return $r ? $r->v : null;

            case 'same_day_plan_count':
                $q = "SELECT COUNT(*) AS v FROM daily_planner
                      WHERE bd_uid = ? AND is_same_day_plan = 1
                        AND plan_date BETWEEN ? AND ?";
                $r = $this->db->query($q, [$bd_uid, $win_from, $win_to])->row();
                return $r ? (int)$r->v : 0;

            case 'auto_band_breach_count':
            case 'wfo_breach_count':
                $col = $key === 'wfo_breach_count' ? 'wfo_breach_count' : 'auto_band_breach_count';
                $q = "SELECT COALESCE(SUM({$col}),0) AS v
                      FROM band_violation_log
                      WHERE bd_uid = ? AND DATE(created_at) BETWEEN ? AND ?";
                $r = $this->db->query($q, [$bd_uid, $win_from, $win_to])->row();
                return $r ? (int)$r->v : 0;

            case 'planning_grade_avg':
                // Grade band to numeric: A+ 5, A 4, B 3, C 2, D 1
                $q = "SELECT ROUND(AVG(
                        CASE grade_band
                          WHEN 'Aplus' THEN 5 WHEN 'A' THEN 4 WHEN 'B' THEN 3
                          WHEN 'C' THEN 2 WHEN 'D' THEN 1 ELSE NULL END),2) AS v
                      FROM bd_progression_daily
                      WHERE bd_uid = ? AND plan_date BETWEEN ? AND ?";
                $r = $this->db->query($q, [$bd_uid, $win_from, $win_to])->row();
                return $r ? $r->v : null;

            case 'planning_incentive_rs_earned':
                $q = "SELECT COALESCE(SUM(incentive_rs),0) AS v
                      FROM bd_progression_daily
                      WHERE bd_uid = ? AND plan_date BETWEEN ? AND ?";
                $r = $this->db->query($q, [$bd_uid, $win_from, $win_to])->row();
                return $r ? (float)$r->v : 0;

            case 'new_leads_added':
                $q = "SELECT COUNT(*) AS v FROM init_call
                      WHERE creator_id = ? AND new_lead = 1
                        AND DATE(createDate) BETWEEN ? AND ?";
                $r = $this->db->query($q, [$bd_uid, $win_from, $win_to])->row();
                return $r ? (int)$r->v : 0;

            case 'barge_vs_research_ratio':
                $q = "SELECT
                        (SELECT COUNT(*) FROM tblcallevents
                         WHERE created_by = ? AND actiontype_id = 4 AND purpose_id = 66
                           AND event_date BETWEEN ? AND ?) AS barge,
                        (SELECT COUNT(*) FROM tblcallevents
                         WHERE created_by = ? AND actiontype_id = 10 AND purpose_id = 94
                           AND event_date BETWEEN ? AND ?) AS research";
                $r = $this->db->query($q, [$bd_uid, $win_from, $win_to, $bd_uid, $win_from, $win_to])->row();
                if (!$r || $r->research == 0) return null;
                return round($r->barge / $r->research, 2);

            case 'seeded_into_plan_count':
                $q = "SELECT COUNT(*) AS v FROM location_prospect_suggestion
                      WHERE seed_status = 'seeded'
                        AND DATE(updated_at) BETWEEN ? AND ?
                        AND seeded_planner_id IN (SELECT id FROM daily_planner WHERE bd_uid = ?)";
                $r = $this->db->query($q, [$win_from, $win_to, $bd_uid])->row();
                return $r ? (int)$r->v : 0;

            case 'meetings_completed':
                $q = "SELECT COUNT(*) AS v FROM tblcallevents
                      WHERE created_by = ? AND actiontype_id IN (3,4)
                        AND event_date BETWEEN ? AND ?";
                $r = $this->db->query($q, [$bd_uid, $win_from, $win_to])->row();
                return $r ? (int)$r->v : 0;

            case 'mom_written_pct':
                $q = "SELECT
                        CASE WHEN COUNT(*) = 0 THEN NULL
                        ELSE ROUND(SUM(EXISTS(SELECT 1 FROM mom_data m WHERE m.event_id = c.id)) * 100.0 / COUNT(*),2) END AS v
                      FROM tblcallevents c
                      WHERE c.created_by = ? AND c.actiontype_id IN (3,4)
                        AND c.event_date BETWEEN ? AND ?";
                $r = $this->db->query($q, [$bd_uid, $win_from, $win_to])->row();
                return $r ? $r->v : null;

            case 'stuck_leads_cstatus_6_over_7d':
                $q = "SELECT COUNT(*) AS v FROM init_call
                      WHERE mainbd = ? AND current_status_id = 6
                        AND DATEDIFF(CURDATE(), last_status_change_at) > 7";
                $r = $this->db->query($q, [$bd_uid])->row();
                return $r ? (int)$r->v : 0;

            case 'stuck_leads_cstatus_8_over_30d':
                $q = "SELECT COUNT(*) AS v FROM init_call
                      WHERE mainbd = ? AND current_status_id = 8
                        AND DATEDIFF(CURDATE(), last_status_change_at) > 30";
                $r = $this->db->query($q, [$bd_uid])->row();
                return $r ? (int)$r->v : 0;

            case 'stuck_leads_cstatus_9_over_14d':
                $q = "SELECT COUNT(*) AS v FROM init_call
                      WHERE mainbd = ? AND current_status_id = 9
                        AND DATEDIFF(CURDATE(), last_status_change_at) > 14";
                $r = $this->db->query($q, [$bd_uid])->row();
                return $r ? (int)$r->v : 0;

            case 'expense_actuals_compliance_pct':
                $q = "SELECT
                        CASE WHEN COUNT(*) = 0 THEN NULL
                        ELSE ROUND(SUM(gate_result <> 'blocked_actuals_missing') * 100.0 / COUNT(*),2) END AS v
                      FROM plan_submit_gate_log
                      WHERE bd_uid = ? AND plan_date BETWEEN ? AND ?";
                $r = $this->db->query($q, [$bd_uid, $win_from, $win_to])->row();
                return $r ? $r->v : null;

            case 'variance_breach_count':
                $q = "SELECT COUNT(*) AS v FROM expense_actuals_log
                      WHERE bd_uid = ? AND requires_dual_approval = 1
                        AND DATE(submitted_at) BETWEEN ? AND ?";
                $r = $this->db->query($q, [$bd_uid, $win_from, $win_to])->row();
                return $r ? (int)$r->v : 0;

            case 'unreturned_advance_rs_total':
                $q = "SELECT COALESCE(SUM(amount),0) AS v FROM travel_advance
                      WHERE bd_uid = ? AND consumed_status IN ('pending','rolled')
                        AND linked_cancellation_event_id IS NOT NULL";
                $r = $this->db->query($q, [$bd_uid])->row();
                return $r ? (float)$r->v : 0;

            case 'avg_daily_app_minutes':
                $q = "SELECT COALESCE(AVG(total_minutes),0) AS v
                      FROM usage_daily_per_user
                      WHERE user_id = ? AND usage_date BETWEEN ? AND ?";
                $r = $this->db->query($q, [$bd_uid, $win_from, $win_to])->row();
                return $r ? round((float)$r->v, 2) : null;

            // Default: metric not yet wired; return null. UI hides null metrics.
            default:
                return null;
        }
    }

    /* ===================================================================
     * 4) RATING save (BD self + manager)
     * =================================================================== */

    public function save_bd_self_rating($session_id, $metric_key, $rating, $remarks = '') {
        $rating = (int)$rating;
        if ($rating < 1 || $rating > 5) return ['error' => 'rating_out_of_range'];

        $existing = $this->db->get_where('review_metric_rating_v2',
            ['session_id' => $session_id, 'metric_key' => $metric_key])->row();

        $payload = ['bd_self_rating' => $rating, 'bd_remarks' => $remarks];
        if ($existing) {
            $this->db->where('id', $existing->id)->update('review_metric_rating_v2', $payload);
        } else {
            $payload['session_id'] = $session_id;
            $payload['metric_key'] = $metric_key;
            $this->db->insert('review_metric_rating_v2', $payload);
        }

        $this->db->update('review_session_v2',
            ['status' => 'bd_self_in_progress'], ['id' => $session_id]);
        return ['ok' => true];
    }

    public function save_manager_rating($session_id, $metric_key, $rating, $remarks = '') {
        $rating = (int)$rating;
        if ($rating < 1 || $rating > 5) return ['error' => 'rating_out_of_range'];

        $existing = $this->db->get_where('review_metric_rating_v2',
            ['session_id' => $session_id, 'metric_key' => $metric_key])->row();

        $payload = ['manager_rating' => $rating, 'manager_remarks' => $remarks];
        if ($existing) {
            $this->db->where('id', $existing->id)->update('review_metric_rating_v2', $payload);
        } else {
            $payload['session_id'] = $session_id;
            $payload['metric_key'] = $metric_key;
            $this->db->insert('review_metric_rating_v2', $payload);
        }

        $this->db->update('review_session_v2',
            ['status' => 'manager_in_progress'], ['id' => $session_id]);
        return ['ok' => true];
    }

    public function mark_bd_self_done($session_id) {
        $this->db->update('review_session_v2', [
            'status' => 'bd_self_done',
            'bd_self_completed_at' => date('Y-m-d H:i:s')
        ], ['id' => $session_id]);
        // Compute bd_self_avg_rating
        $avg = $this->db->select_avg('bd_self_rating', 'avg')
            ->where('session_id', $session_id)
            ->where('bd_self_rating IS NOT NULL')
            ->get('review_metric_rating_v2')->row();
        if ($avg) {
            $this->db->update('review_session_v2',
                ['bd_self_avg_rating' => $avg->avg], ['id' => $session_id]);
        }
        return ['ok' => true];
    }

    /* ===================================================================
     * 5) ACTION items
     * =================================================================== */

    public function add_action_item($session_id, $owner_uid, $action_text, $due_date,
                                     $priority = 'medium', $owner_role = 'BD') {
        $this->db->insert('review_action_item', [
            'session_id' => $session_id,
            'owner_uid' => $owner_uid,
            'owner_role' => $owner_role,
            'action_text' => $action_text,
            'due_date' => $due_date,
            'priority' => $priority,
            'status' => 'open'
        ]);
        return $this->db->insert_id();
    }

    public function open_action_items_for_owner($owner_uid) {
        return $this->db->where('owner_uid', $owner_uid)
            ->where_in('status', ['open','in_progress'])
            ->order_by('priority DESC, due_date ASC')
            ->get('review_action_item')->result();
    }

    public function close_action_item($action_id, $closed_by, $evidence = '') {
        $this->db->where('id', $action_id)->update('review_action_item', [
            'status' => 'done',
            'closed_at' => date('Y-m-d H:i:s'),
            'closed_by' => $closed_by,
            'closure_evidence' => $evidence
        ]);
        return ['ok' => true];
    }

    /* ===================================================================
     * 6) SESSION close
     * =================================================================== */

    public function close_session($session_id, $closed_by, $comments = '') {
        $session = $this->db->get_where('review_session_v2', ['id' => $session_id])->row();
        if (!$session) return ['error' => 'session_not_found'];

        // Compute manager_avg_rating and band
        $r = $this->db->query("SELECT AVG(manager_rating) AS avg_r
            FROM review_metric_rating_v2
            WHERE session_id = ? AND manager_rating IS NOT NULL",
            [$session_id])->row();
        $avg = $r ? (float)$r->avg_r : 0;

        $band = 'D';
        if ($avg >= 4.5) $band = 'Aplus';
        elseif ($avg >= 4.0) $band = 'A';
        elseif ($avg >= 3.0) $band = 'B';
        elseif ($avg >= 2.0) $band = 'C';

        $delta = null;
        if ($session->bd_self_avg_rating !== null) {
            $delta = round(($avg - (float)$session->bd_self_avg_rating) / 5.00 * 100, 2);
        }

        $start_ts = $session->manager_started_at ?: $session->started_at;
        $duration = $start_ts ? max(1, (int)((time() - strtotime($start_ts)) / 60)) : null;

        $this->db->update('review_session_v2', [
            'completed_at' => date('Y-m-d H:i:s'),
            'duration_minutes' => $duration,
            'manager_avg_rating' => $avg,
            'overall_band' => $band,
            'delta_pct' => $delta,
            'comments_md' => $comments,
            'status' => 'completed'
        ], ['id' => $session_id]);

        // Close out schedule and create next one
        if ($session->schedule_id) {
            $this->db->update('review_schedule',
                ['status' => 'completed'], ['id' => $session->schedule_id]);
            $next_date = $this->compute_next_schedule_date($session->to_uid, date('Y-m-d'));
            $exists = $this->db->where([
                'bd_uid' => $session->to_uid,
                'review_type_id' => $session->review_type_id,
                'scheduled_date' => $next_date
            ])->count_all_results('review_schedule');
            if ($exists == 0) {
                $this->db->insert('review_schedule', [
                    'bd_uid' => $session->to_uid,
                    'manager_uid' => $session->by_uid,
                    'review_type_id' => $session->review_type_id,
                    'scheduled_date' => $next_date,
                    'min_duration_minutes' => $session->review_type_id == 1 ? 25 : 20,
                    'status' => 'pending'
                ]);
            }
        }
        return ['ok' => true, 'avg_rating' => $avg, 'band' => $band, 'delta_pct' => $delta];
    }

    /* ===================================================================
     * 7) PLAN-SUBMIT GATE
     * =================================================================== */

    /**
     * Called by the planner submit flow to check whether the manager has
     * overdue reviews. Returns:
     *   ['gate_result' => 'passed' | 'blocked_review_overdue' | 'warning_only', ...]
     */
    public function check_plan_submit_gate($manager_uid, $plan_date = null) {
        $plan_date = $plan_date ?: date('Y-m-d', strtotime('+1 day'));
        $mode = $this->config->item('review_gate_enforcement_mode') ?: 'warning'; // off | warning | hard

        $row = $this->db->get_where('v_review_overdue_manager',
            ['manager_uid' => $manager_uid])->row();
        $count = $row ? (int)$row->overdue_count : 0;
        $ids = $row ? $row->overdue_schedule_ids : null;

        $result = 'passed';
        if ($count > 0) {
            if ($mode === 'hard') $result = 'blocked_review_overdue';
            elseif ($mode === 'warning') $result = 'warning_only';
            // off => still passed
        }

        $this->db->insert('review_gate_log', [
            'manager_uid' => $manager_uid,
            'plan_date' => $plan_date,
            'evaluated_at' => date('Y-m-d H:i:s'),
            'gate_result' => $result,
            'overdue_review_count' => $count,
            'overdue_review_ids_json' => $ids ? json_encode(explode(',', $ids)) : null,
            'enforcement_mode' => $mode
        ]);

        return [
            'gate_result' => $result,
            'overdue_count' => $count,
            'overdue_schedule_ids' => $ids,
            'enforcement_mode' => $mode
        ];
    }

    /* ===================================================================
     * 8) SKIP-LEVEL register
     * =================================================================== */

    /**
     * Daily roll-up called by cron 0c647bbd 7:30 AM extension
     */
    public function refresh_skip_register($period_start, $period_end) {
        $sql = "SELECT
                  rs.manager_uid AS cm_uid,
                  COUNT(*) AS scheduled_count,
                  SUM(rs.status = 'completed') AS completed_count,
                  SUM(rs.status = 'missed') AS missed_count
                FROM review_schedule rs
                WHERE rs.scheduled_date BETWEEN ? AND ?
                GROUP BY rs.manager_uid";
        $rows = $this->db->query($sql, [$period_start, $period_end])->result();

        $updated = 0;
        foreach ($rows as $r) {
            $on_time = $r->scheduled_count > 0
                ? round((float)$r->completed_count * 100.0 / (float)$r->scheduled_count, 2)
                : null;

            $agg = $this->db->query("SELECT
                AVG(duration_minutes) AS avg_dur,
                AVG(manager_avg_rating) AS avg_rating
                FROM review_session_v2
                WHERE by_uid = ? AND DATE(completed_at) BETWEEN ? AND ?",
                [$r->cm_uid, $period_start, $period_end])->row();

            $cluster_avg = $this->db->query("SELECT AVG(manager_avg_rating) AS avg_r
                FROM review_session_v2
                WHERE DATE(completed_at) BETWEEN ? AND ?",
                [$period_start, $period_end])->row();
            $cluster_avg_v = $cluster_avg ? (float)$cluster_avg->avg_r : null;
            $self_avg = $agg ? (float)$agg->avg_rating : null;
            $inflation_flag = ($cluster_avg_v && $self_avg && ($self_avg - $cluster_avg_v) >= 1.0) ? 1 : 0;

            // Upsert
            $existing = $this->db->where([
                'cm_uid' => $r->cm_uid,
                'period_start' => $period_start,
                'period_end' => $period_end
            ])->get('review_skip_register')->row();

            $payload = [
                'cm_uid' => $r->cm_uid,
                'period_start' => $period_start,
                'period_end' => $period_end,
                'scheduled_count' => $r->scheduled_count,
                'completed_count' => $r->completed_count,
                'missed_count' => $r->missed_count,
                'on_time_pct' => $on_time,
                'avg_duration_minutes' => $agg ? round((float)$agg->avg_dur, 2) : null,
                'avg_rating_given' => $self_avg,
                'cluster_avg_rating' => $cluster_avg_v,
                'inflation_flag' => $inflation_flag
            ];
            if ($existing) {
                $this->db->where('id', $existing->id)->update('review_skip_register', $payload);
            } else {
                $this->db->insert('review_skip_register', $payload);
            }
            $updated++;
        }
        return ['cms_updated' => $updated];
    }

    public function skip_level_dashboard($period_start, $period_end) {
        return $this->db->query("SELECT * FROM v_review_skip_level_dashboard
            WHERE period_start = ? AND period_end = ?
            ORDER BY discipline_flag DESC, on_time_pct ASC",
            [$period_start, $period_end])->result();
    }

    /* ===================================================================
     * 9) HELPERS
     * =================================================================== */

    public function session_with_ratings($session_id) {
        $session = $this->db->get_where('review_session_v2', ['id' => $session_id])->row();
        if (!$session) return null;

        $ratings = $this->db->query("SELECT r.*, mc.metric_label, mc.category, mc.weight, mc.applies_to_role
            FROM review_metric_rating_v2 r
            LEFT JOIN review_metric_catalog mc ON mc.metric_key = r.metric_key
            WHERE r.session_id = ?
            ORDER BY mc.category, mc.weight DESC", [$session_id])->result();

        $actions = $this->db->where('session_id', $session_id)->get('review_action_item')->result();

        return ['session' => $session, 'ratings' => $ratings, 'actions' => $actions];
    }
}

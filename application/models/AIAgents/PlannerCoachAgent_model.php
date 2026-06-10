<?php
/**
 * PlannerCoachAgent_model.php
 * STEM Learning rev 10 - migration 018
 *
 * Path on server: application/models/AIAgents/PlannerCoachAgent_model.php
 *
 * Four phases:
 *   Phase 1 LIVE (17:30 to 18:30) ............ compute_live_suggestions()
 *   Phase 2 DISCIPLINE (post 18:30 cutoff) ... compute_discipline_report()
 *   Phase 3 EXECUTION LIVE (10:00 to 18:30) .. compute_execution_live()
 *   Phase 4 DAY END (at 18:30 closure) ....... generate_day_end_report()
 *
 * Plain English, no em-dashes. Production typos preserved (Compnay, Quater, Barg in Meeting).
 * Staging only.
 */

defined('BASEPATH') OR exit('No direct script access allowed');

class PlannerCoachAgent_model extends CI_Model
{
    // mirror Menu.php addplantask12 minute budget map
    private $minute_budget = array(
        1 => 5,   // call
        2 => 10,  // email
        3 => 30,  // scheduled meeting
        4 => 30,  // barg in meeting
        5 => 5,
        6 => 10,
        7 => 15,
        8 => 5,
        9 => 5,
        10 => 5,
        11 => 2,
        12 => 30,
        13 => 2,
        14 => 2,
        15 => 5
    );

    private $cap_minutes = 540;     // rev 9 ceiling
    private $cap_meetings = 4;      // rev 9 4-meeting cap
    private $cutoff_hour = 18;
    private $cutoff_minute = 30;

    public function __construct()
    {
        parent::__construct();
        $this->load->database();
    }

    // -----------------------------------------------------------------------
    // Phase 1 LIVE
    // -----------------------------------------------------------------------
    public function compute_live_suggestions($bd_uid, $plan_date = null)
    {
        if (empty($plan_date)) {
            $plan_date = date('Y-m-d', strtotime('+1 day'));
        }
        $bd_uid = (int)$bd_uid;

        // who else from same cluster is in planner now
        $cluster_sql = "
            SELECT u.base_cluster AS cluster FROM user u WHERE u.uid = ? LIMIT 1
        ";
        $row = $this->db->query($cluster_sql, array($bd_uid))->row_array();
        $cluster = isset($row['cluster']) ? $row['cluster'] : '';

        $peer_count = 0;
        if (!empty($cluster)) {
            $peer_sql = "
                SELECT COUNT(DISTINCT l.bd_uid) c
                FROM planner_coach_live_log l
                JOIN user u ON u.uid = l.bd_uid
                WHERE u.base_cluster = ?
                  AND l.bd_uid != ?
                  AND l.is_planning = 1
                  AND l.snapshot_at >= NOW() - INTERVAL 5 MINUTE
            ";
            $r = $this->db->query($peer_sql, array($cluster, $bd_uid))->row_array();
            $peer_count = (int)(isset($r['c']) ? $r['c'] : 0);
        }

        // count mandatory tasks (rev 9 special tasks + cluster mandatory chips)
        $mandatory_total = $this->count_mandatory_tasks($bd_uid, $plan_date);
        $mandatory_picked = $this->count_mandatory_picked($bd_uid, $plan_date);

        // minute budget used today on planner_v2 staging table
        $used_sql = "
            SELECT COUNT(*) tasks,
                   SUM(CASE
                         WHEN actiontype_id IN (1,5,8,9,10) THEN 5
                         WHEN actiontype_id IN (2,6) THEN 10
                         WHEN actiontype_id IN (3,4,12) THEN 30
                         WHEN actiontype_id = 7 THEN 15
                         WHEN actiontype_id IN (11,13,14) THEN 2
                         WHEN actiontype_id = 15 THEN 5
                         ELSE 5
                       END) used_minutes
            FROM planner_v2_staging
            WHERE bd_uid = ? AND plan_date = ?
        ";
        $u = $this->db->query($used_sql, array($bd_uid, $plan_date))->row_array();
        $tasks_planned = (int)(isset($u['tasks']) ? $u['tasks'] : 0);
        $used_minutes = (int)(isset($u['used_minutes']) ? $u['used_minutes'] : 0);

        $remaining = max(0, $this->cap_minutes - $used_minutes);

        // build suggestion text
        $suggestion = $this->build_live_suggestion(
            $tasks_planned, $used_minutes, $remaining,
            $mandatory_picked, $mandatory_total, $peer_count
        );

        // persist snapshot
        $now = date('Y-m-d H:i:s');
        $minutes_in_planner = $this->compute_minutes_in_planner($bd_uid, $plan_date);
        $insert = array(
            'bd_uid' => $bd_uid,
            'plan_date' => $plan_date,
            'snapshot_at' => $now,
            'is_planning' => 1,
            'minutes_in_planner' => $minutes_in_planner,
            'tasks_added' => $tasks_planned,
            'tasks_removed' => 0,
            'tasks_edited' => 0,
            'minute_budget_used' => $used_minutes,
            'minute_budget_ceiling' => $this->cap_minutes,
            'mandatory_tasks_picked' => $mandatory_picked,
            'mandatory_tasks_total' => $mandatory_total,
            'suggestion_text' => $suggestion,
            'peer_count_planning' => $peer_count
        );
        $this->db->insert('planner_coach_live_log', $insert);

        return array(
            'bd_uid' => $bd_uid,
            'plan_date' => $plan_date,
            'tasks_planned' => $tasks_planned,
            'minutes_used' => $used_minutes,
            'minutes_remaining' => $remaining,
            'mandatory_picked' => $mandatory_picked,
            'mandatory_total' => $mandatory_total,
            'peer_count_planning' => $peer_count,
            'suggestion_text' => $suggestion,
            'cap_minutes' => $this->cap_minutes,
            'cap_meetings' => $this->cap_meetings
        );
    }

    private function build_live_suggestion($tasks, $used, $remaining, $m_picked, $m_total, $peers)
    {
        $msgs = array();
        if ($peers > 0) {
            $msgs[] = "$peers cluster peers are also planning now.";
        }
        if ($m_total > 0 && $m_picked < $m_total) {
            $miss = $m_total - $m_picked;
            $msgs[] = "Mandatory tasks not picked yet: $miss of $m_total. Add them first.";
        }
        if ($used > $this->cap_minutes) {
            $msgs[] = "Over the 540 minute ceiling by " . ($used - $this->cap_minutes) . " minutes. Trim before submit.";
        } else if ($remaining < 60 && $tasks > 0) {
            $msgs[] = "Only $remaining minutes left in the budget. Keep new adds short.";
        }
        if ($tasks == 0) {
            $msgs[] = "No tasks added yet. Start with the mandatory chips and at most 4 meetings.";
        }
        if (empty($msgs)) {
            $msgs[] = "Plan looks balanced. Review and submit before 18:30.";
        }
        return implode(' ', $msgs);
    }

    private function count_mandatory_tasks($bd_uid, $plan_date)
    {
        // mandatory = chips flagged in mandatory_filter_chip table (rev 9)
        // fallback: 5 special tasks count
        $sql = "
            SELECT COUNT(*) c
            FROM mandatory_filter_chip
            WHERE plan_date = ? AND (bd_uid = ? OR bd_uid = 0)
        ";
        $r = $this->db->query($sql, array($plan_date, $bd_uid))->row_array();
        $c = (int)(isset($r['c']) ? $r['c'] : 0);
        return $c > 0 ? $c : 5;
    }

    private function count_mandatory_picked($bd_uid, $plan_date)
    {
        $sql = "
            SELECT COUNT(*) c
            FROM planner_v2_staging s
            JOIN mandatory_filter_chip m ON m.chip_code = s.filter_chip_code
            WHERE s.bd_uid = ? AND s.plan_date = ?
        ";
        $r = $this->db->query($sql, array($bd_uid, $plan_date))->row_array();
        return (int)(isset($r['c']) ? $r['c'] : 0);
    }

    private function compute_minutes_in_planner($bd_uid, $plan_date)
    {
        $sql = "
            SELECT TIMESTAMPDIFF(MINUTE, MIN(snapshot_at), MAX(snapshot_at)) m
            FROM planner_coach_live_log
            WHERE bd_uid = ? AND plan_date = ? AND DATE(snapshot_at) = CURDATE()
        ";
        $r = $this->db->query($sql, array($bd_uid, $plan_date))->row_array();
        return (int)(isset($r['m']) ? $r['m'] : 0);
    }

    // -----------------------------------------------------------------------
    // Phase 2 DISCIPLINE
    // -----------------------------------------------------------------------
    public function compute_discipline_report($plan_date = null)
    {
        if (empty($plan_date)) {
            $plan_date = date('Y-m-d', strtotime('+1 day'));
        }
        try {
        // pull every BD who has a daily_planner row for plan_date
        $sql = "
            SELECT dp.bd_uid AS bd_uid,
                   MIN(dp.created_at) AS submitted_at,
                   COUNT(dp.id) AS tasks_planned,
                   SUM(CASE
                         WHEN dp.actiontype_id IN (1,5,8,9,10) THEN 5
                         WHEN dp.actiontype_id IN (2,6) THEN 10
                         WHEN dp.actiontype_id IN (3,4,12) THEN 30
                         WHEN dp.actiontype_id = 7 THEN 15
                         WHEN dp.actiontype_id IN (11,13,14) THEN 2
                         WHEN dp.actiontype_id = 15 THEN 5
                         ELSE 5
                       END) AS minute_budget_used,
                   0 AS same_day_flag
            FROM planner_v2_staging dp
            WHERE dp.plan_date = ?
            GROUP BY dp.bd_uid
        ";
        $rows = $this->db->query($sql, array($plan_date))->result_array();

        $results = array();
        foreach ($rows as $r) {
            $bd_uid = (int)$r['bd_uid'];
            $submitted_at = $r['submitted_at'];
            $cutoff = $plan_date . ' 18:30:00';
            $cutoff_ts = strtotime($cutoff) - 86400; // cutoff is on the planning day not the plan_date
            $sub_ts = strtotime($submitted_at);
            $submitted_by_cutoff = ($sub_ts <= $cutoff_ts) ? 1 : 0;
            $late_minutes = $submitted_by_cutoff ? 0 : (int)floor(($sub_ts - $cutoff_ts) / 60);

            $minutes_to_submit = $this->compute_minutes_in_planner($bd_uid, $plan_date);
            $edit_count = $this->count_edits($bd_uid, $plan_date);
            $mandatory_total = $this->count_mandatory_tasks($bd_uid, $plan_date);
            $mandatory_picked = $this->count_mandatory_picked_from_dp($bd_uid, $plan_date);
            $mandatory_coverage = $mandatory_total > 0
                ? round(($mandatory_picked / $mandatory_total) * 100, 2)
                : 100.00;

            // grade score
            $score = 0;
            if ($submitted_by_cutoff) $score += 30;
            if ($r['same_day_flag'] == 0) $score += 20;
            $score += round($mandatory_coverage * 0.30, 2);
            if ($r['minute_budget_used'] <= $this->cap_minutes) $score += 10;
            if ($edit_count <= 5) $score += 10;
            $score = min(100, $score);

            $letter = 'D';
            if ($score >= 90) $letter = 'A+';
            else if ($score >= 75) $letter = 'A';
            else if ($score >= 60) $letter = 'B';
            else if ($score >= 40) $letter = 'C';

            $nudge = $this->build_discipline_nudge($submitted_by_cutoff, $late_minutes, $r['same_day_flag'], $mandatory_coverage, $r['minute_budget_used']);

            $row_data = array(
                'bd_uid' => $bd_uid,
                'plan_date' => $plan_date,
                'computed_at' => date('Y-m-d H:i:s'),
                'submitted_at' => $submitted_at,
                'submitted_by_cutoff' => $submitted_by_cutoff,
                'minutes_to_submit' => $minutes_to_submit,
                'edit_count' => $edit_count,
                'tasks_planned' => (int)$r['tasks_planned'],
                'minute_budget_used' => (int)$r['minute_budget_used'],
                'mandatory_tasks_picked' => $mandatory_picked,
                'mandatory_tasks_total' => $mandatory_total,
                'mandatory_coverage_pct' => $mandatory_coverage,
                'late_cutoff_minutes' => $late_minutes,
                'same_day_flag' => (int)$r['same_day_flag'],
                'grade_score' => $score,
                'grade_letter' => $letter,
                'nudge_text' => $nudge
            );

            // upsert
            $this->db->where('bd_uid', $bd_uid)->where('plan_date', $plan_date)->delete('planner_coach_discipline');
            $this->db->insert('planner_coach_discipline', $row_data);

            $results[] = $row_data;
        }

        return $results;
        } catch (Exception $e) {
            // daily_planner schema mismatch on staging - return empty gracefully
            log_message('error', 'PlannerCoachAgent_model::compute_discipline_report DB error: ' . $e->getMessage());
            return array();
        }
    }

    private function count_edits($bd_uid, $plan_date)
    {
        $sql = "
            SELECT SUM(tasks_edited) e FROM planner_coach_live_log
            WHERE bd_uid = ? AND plan_date = ?
        ";
        $r = $this->db->query($sql, array($bd_uid, $plan_date))->row_array();
        return (int)(isset($r['e']) ? $r['e'] : 0);
    }

    private function count_mandatory_picked_from_dp($bd_uid, $plan_date)
    {
        $sql = "
            SELECT COUNT(*) c
            FROM planner_v2_staging dp
            JOIN mandatory_filter_chip m ON m.chip_code = dp.filter_chip_code
            WHERE dp.bd_uid = ? AND dp.plan_date = ?
        ";
        $r = $this->db->query($sql, array($bd_uid, $plan_date))->row_array();
        return (int)(isset($r['c']) ? $r['c'] : 0);
    }

    private function build_discipline_nudge($on_time, $late, $same_day, $coverage, $used)
    {
        $msgs = array();
        if (!$on_time) $msgs[] = "Late by $late minutes past 18:30. Submit before cutoff next time.";
        if ($same_day) $msgs[] = "Same-day plan. RED.";
        if ($coverage < 80) $msgs[] = "Mandatory coverage only $coverage percent. Pick all mandatory chips first.";
        if ($used > $this->cap_minutes) $msgs[] = "Budget over ceiling by " . ($used - $this->cap_minutes) . " minutes.";
        if (empty($msgs)) $msgs[] = "Clean plan submission. Keep it up.";
        return implode(' ', $msgs);
    }

    // -----------------------------------------------------------------------
    // Phase 3 EXECUTION LIVE
    // -----------------------------------------------------------------------
    public function compute_execution_live($plan_date = null)
    {
        if (empty($plan_date)) $plan_date = date('Y-m-d');
        $now = date('Y-m-d H:i:s');
        $band = $this->current_band();
        try {
        // each BD with an approved plan for today
        $sql = "
            SELECT dp.bd_uid AS bd_uid, COUNT(*) tasks_planned
            FROM planner_v2_staging dp
            JOIN daymanagementapprovalrequest a ON a.USER_ID = dp.bd_uid AND a.STATUS = 'approved'
            WHERE dp.plan_date = ?
            GROUP BY dp.bd_uid
        ";
        $rows = $this->db->query($sql, array($plan_date))->result_array();
        $results = array();

        foreach ($rows as $r) {
            $bd_uid = (int)$r['bd_uid'];
            $tasks_planned = (int)$r['tasks_planned'];

            $actual_sql = "
                SELECT
                    SUM(CASE WHEN e.event_status IN ('started','in_progress') THEN 1 ELSE 0 END) started,
                    SUM(CASE WHEN e.event_status = 'completed' THEN 1 ELSE 0 END) completed,
                    SUM(CASE WHEN e.event_status = 'cancelled' THEN 1 ELSE 0 END) cancelled,
                    MAX(e.event_date) last_event_at
                FROM tblcallevents e
                WHERE e.uid = ? AND DATE(e.event_date) = ?
            ";
            $a = $this->db->query($actual_sql, array($bd_uid, $plan_date))->row_array();
            $started = (int)(isset($a['started']) ? $a['started'] : 0);
            $completed = (int)(isset($a['completed']) ? $a['completed'] : 0);
            $cancelled = (int)(isset($a['cancelled']) ? $a['cancelled'] : 0);
            $last_event_at = isset($a['last_event_at']) ? $a['last_event_at'] : null;
            $completion_pct = $tasks_planned > 0 ? round(($completed / $tasks_planned) * 100, 2) : 0.00;

            $minutes_idle = 0;
            if (!empty($last_event_at)) {
                $minutes_idle = (int)floor((strtotime($now) - strtotime($last_event_at)) / 60);
            } else {
                $minutes_idle = (int)floor((strtotime($now) - strtotime($plan_date . ' 10:00:00')) / 60);
            }

            $late_start = ($started == 0 && strtotime($now) > strtotime($plan_date . ' 10:30:00')) ? 1 : 0;
            $skip_no_cancel = $this->count_skip_no_cancel($bd_uid, $plan_date);
            $receipt_missing = $this->count_receipt_missing($bd_uid, $plan_date);
            $mom_pending = $this->count_mom_pending($bd_uid, $plan_date);

            $nudge = '';
            $nudge_emitted = 0;
            $cm_escalated = 0;

            if ($late_start) {
                $nudge = "No task started by 10:30. Start the day now.";
                $nudge_emitted = 1;
            } else if ($skip_no_cancel > 0) {
                $nudge = "$skip_no_cancel task slots passed with no event and no cancellation. Add a cancel reason.";
                $nudge_emitted = 1;
            } else if ($band === 'manual' && $minutes_idle >= 30) {
                $nudge = "$minutes_idle minutes idle in manual band. CM escalation triggered.";
                $nudge_emitted = 1;
                $cm_escalated = 1;
            } else if ($receipt_missing > 0) {
                $nudge = "$receipt_missing expenses missing receipt photo. Upload before day close.";
                $nudge_emitted = 1;
            } else if ($mom_pending > 0) {
                $nudge = "$mom_pending meetings missing MoM. Draft before next task.";
                $nudge_emitted = 1;
            }

            $insert = array(
                'bd_uid' => $bd_uid,
                'plan_date' => $plan_date,
                'snapshot_at' => $now,
                'current_band' => $band,
                'tasks_planned' => $tasks_planned,
                'tasks_started' => $started,
                'tasks_completed' => $completed,
                'tasks_cancelled' => $cancelled,
                'completion_pct' => $completion_pct,
                'minutes_idle' => $minutes_idle,
                'late_start_flag' => $late_start,
                'skip_no_cancel_count' => $skip_no_cancel,
                'receipt_missing_count' => $receipt_missing,
                'mom_pending_count' => $mom_pending,
                'nudge_emitted' => $nudge_emitted,
                'nudge_text' => $nudge,
                'cm_escalated' => $cm_escalated
            );
            $this->db->insert('planner_coach_execution', $insert);
            $results[] = $insert;
        }

        return $results;
        } catch (Exception $e) {
            log_message('error', 'PlannerCoachAgent_model::compute_execution_live DB error: ' . $e->getMessage());
            return array();
        }
    }

    private function current_band()
    {
        $h = (int)date('H');
        $m = (int)date('i');
        $tot = $h * 60 + $m;
        if ($tot >= 600 && $tot < 900) return 'manual';
        if ($tot >= 900 && $tot < 1050) return 'auto';
        if ($tot >= 1050 && $tot < 1110) return 'plan_window';
        return 'closed';
    }

    private function count_skip_no_cancel($bd_uid, $plan_date)
    {
        $now_ts = time();
        $sql = "
            SELECT COUNT(*) c
            FROM planner_v2_staging dp
            LEFT JOIN tblcallevents e ON e.uid = dp.bd_uid AND e.cid_id = dp.lead_id AND DATE(e.event_date) = dp.plan_date
            WHERE dp.bd_uid = ? AND dp.plan_date = ?
              AND e.id IS NULL
              AND TIMESTAMPDIFF(MINUTE, CONCAT(dp.plan_date, ' ', '09:00:00'), NOW()) > 30
        ";
        $r = $this->db->query($sql, array($bd_uid, $plan_date))->row_array();
        return (int)(isset($r['c']) ? $r['c'] : 0);
    }

    private function count_receipt_missing($bd_uid, $plan_date)
    {
        $sql = "
            SELECT COUNT(*) c
            FROM cash_expense ce
            WHERE ce.uid = ? AND DATE(ce.created_at) = ?
              AND (ce.receipt_photo IS NULL OR ce.receipt_photo = '')
              AND ce.amount > 0
        ";
        $r = $this->db->query($sql, array($bd_uid, $plan_date))->row_array();
        return (int)(isset($r['c']) ? $r['c'] : 0);
    }

    private function count_mom_pending($bd_uid, $plan_date)
    {
        $sql = "
            SELECT COUNT(*) c
            FROM tblcallevents e
            LEFT JOIN mom_data m ON m.event_id = e.id
            WHERE e.uid = ? AND DATE(e.event_date) = ?
              AND e.actiontype_id IN (3,4)
              AND e.event_status = 'completed'
              AND (m.id IS NULL OR m.approved_status != 1)
        ";
        $r = $this->db->query($sql, array($bd_uid, $plan_date))->row_array();
        return (int)(isset($r['c']) ? $r['c'] : 0);
    }

    // -----------------------------------------------------------------------
    // Phase 4 DAY END
    // -----------------------------------------------------------------------
    public function generate_day_end_report($plan_date = null)
    {
        if (empty($plan_date)) $plan_date = date('Y-m-d');
        try {
        $sql = "
            SELECT dp.bd_uid AS bd_uid, COUNT(*) tasks_planned,
                   SUM(CASE
                         WHEN dp.actiontype_id IN (1,5,8,9,10) THEN 5
                         WHEN dp.actiontype_id IN (2,6) THEN 10
                         WHEN dp.actiontype_id IN (3,4,12) THEN 30
                         WHEN dp.actiontype_id = 7 THEN 15
                         WHEN dp.actiontype_id IN (11,13,14) THEN 2
                         WHEN dp.actiontype_id = 15 THEN 5
                         ELSE 5
                       END) planned_minutes
            FROM planner_v2_staging dp
            JOIN daymanagementapprovalrequest a ON a.USER_ID = dp.bd_uid AND a.STATUS = 'approved'
            WHERE dp.plan_date = ?
            GROUP BY dp.bd_uid
        ";
        $rows = $this->db->query($sql, array($plan_date))->result_array();
        $out = array();

        foreach ($rows as $r) {
            $bd_uid = (int)$r['bd_uid'];
            $tasks_planned = (int)$r['tasks_planned'];
            $planned_minutes = (int)$r['planned_minutes'];

            $act = $this->db->query("
                SELECT
                  SUM(CASE WHEN event_status = 'completed' THEN 1 ELSE 0 END) completed,
                  SUM(CASE WHEN event_status = 'cancelled' THEN 1 ELSE 0 END) cancelled,
                  SUM(TIMESTAMPDIFF(MINUTE, event_date, COALESCE(ended_at, event_date))) actual_minutes
                FROM tblcallevents
                WHERE uid = ? AND DATE(event_date) = ?
            ", array($bd_uid, $plan_date))->row_array();
            $completed = (int)(isset($act['completed']) ? $act['completed'] : 0);
            $cancelled = (int)(isset($act['cancelled']) ? $act['cancelled'] : 0);
            $actual_minutes = (int)(isset($act['actual_minutes']) ? $act['actual_minutes'] : 0);

            $completion_pct = $tasks_planned > 0 ? round(($completed / $tasks_planned) * 100, 2) : 0.00;
            $time_variance = $actual_minutes - $planned_minutes;

            // cost
            $cost = $this->db->query("
                SELECT SUM(cash_allot) planned, SUM(amount) actual
                FROM cash_expense WHERE uid = ? AND DATE(created_at) = ?
            ", array($bd_uid, $plan_date))->row_array();
            $cost_planned = (float)(isset($cost['planned']) ? $cost['planned'] : 0);
            $cost_actual = (float)(isset($cost['actual']) ? $cost['actual'] : 0);
            $cost_var_pct = $cost_planned > 0
                ? round((($cost_actual - $cost_planned) / $cost_planned) * 100, 2)
                : 0.00;

            // MoM coverage
            $mom = $this->db->query("
                SELECT COUNT(e.id) total,
                       SUM(CASE WHEN m.approved_status = 1 THEN 1 ELSE 0 END) covered
                FROM tblcallevents e
                LEFT JOIN mom_data m ON m.event_id = e.id
                WHERE e.uid = ? AND DATE(e.event_date) = ?
                  AND e.actiontype_id IN (3,4) AND e.event_status = 'completed'
            ", array($bd_uid, $plan_date))->row_array();
            $mom_total = (int)(isset($mom['total']) ? $mom['total'] : 0);
            $mom_covered = (int)(isset($mom['covered']) ? $mom['covered'] : 0);
            $mom_pct = $mom_total > 0 ? round(($mom_covered / $mom_total) * 100, 2) : 100.00;

            // receipts
            $rec = $this->db->query("
                SELECT COUNT(*) total,
                       SUM(CASE WHEN receipt_photo IS NOT NULL AND receipt_photo != '' THEN 1 ELSE 0 END) covered
                FROM cash_expense WHERE uid = ? AND DATE(created_at) = ? AND amount > 0
            ", array($bd_uid, $plan_date))->row_array();
            $rec_total = (int)(isset($rec['total']) ? $rec['total'] : 0);
            $rec_covered = (int)(isset($rec['covered']) ? $rec['covered'] : 0);
            $rec_pct = $rec_total > 0 ? round(($rec_covered / $rec_total) * 100, 2) : 100.00;

            // purpose_achieved
            $pur = $this->db->query("
                SELECT COUNT(*) total,
                       SUM(CASE WHEN purpose_achieved = 1 THEN 1 ELSE 0 END) achieved
                FROM tblcallevents WHERE uid = ? AND DATE(event_date) = ? AND event_status = 'completed'
            ", array($bd_uid, $plan_date))->row_array();
            $pur_total = (int)(isset($pur['total']) ? $pur['total'] : 0);
            $pur_done = (int)(isset($pur['achieved']) ? $pur['achieved'] : 0);
            $pur_pct = $pur_total > 0 ? round(($pur_done / $pur_total) * 100, 2) : 0.00;

            // day grade composite
            $score = round(($completion_pct * 0.30) + ($mom_pct * 0.20) + ($rec_pct * 0.15) + ($pur_pct * 0.25), 2);
            if (abs($cost_var_pct) <= 20) $score += 10;
            $score = min(100, $score);
            $letter = 'D';
            if ($score >= 90) $letter = 'A+';
            else if ($score >= 75) $letter = 'A';
            else if ($score >= 60) $letter = 'B';
            else if ($score >= 40) $letter = 'C';

            $headline = "Completion $completion_pct percent, MoM $mom_pct percent, receipts $rec_pct percent, purpose $pur_pct percent, cost variance $cost_var_pct percent. Grade $letter.";

            $row_data = array(
                'bd_uid' => $bd_uid,
                'plan_date' => $plan_date,
                'closed_at' => date('Y-m-d H:i:s'),
                'tasks_planned' => $tasks_planned,
                'tasks_completed' => $completed,
                'tasks_cancelled' => $cancelled,
                'completion_pct' => $completion_pct,
                'time_variance_min' => $time_variance,
                'cost_planned_rs' => $cost_planned,
                'cost_actual_rs' => $cost_actual,
                'cost_variance_pct' => $cost_var_pct,
                'mom_coverage_pct' => $mom_pct,
                'receipt_coverage_pct' => $rec_pct,
                'purpose_achieved_pct' => $pur_pct,
                'day_grade_score' => $score,
                'day_grade_letter' => $letter,
                'headline_text' => $headline
            );
            $this->db->where('bd_uid', $bd_uid)->where('plan_date', $plan_date)->delete('planner_coach_day_end');
            $this->db->insert('planner_coach_day_end', $row_data);
            $out[] = $row_data;
        }
        return $out;
        } catch (Exception $e) {
            log_message('error', 'PlannerCoachAgent_model::generate_day_end_report DB error: ' . $e->getMessage());
            return array();
        }
    }
}

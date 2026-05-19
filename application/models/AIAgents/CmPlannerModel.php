<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * STEM CRM - Migration 023 - CM Planner Model
 *
 * CM owns a daily plan (cm_daily_plan) and a joint-meeting log
 * (cm_joint_meeting_log). The trigger trg_callevent_cm_joint_log already inserts
 * a log row on every mandatory cstatus 8/9/12 meeting. This model lets the CM
 * confirm/dispute attendance and lets the BD record CM presence from the MoM
 * screen.
 *
 * Plain English. 'Rs' for rupees. No em-dashes.
 */
class CmPlanner_model extends CI_Model {

    private $T_PLAN  = 'cm_daily_plan';
    private $T_LOG   = 'cm_joint_meeting_log';
    private $T_USER  = 'user';
    private $T_LEAD  = 'init_call';
    private $T_EVENT = 'tblcallevents';

    public function __construct() {
        parent::__construct();
        $this->load->database();
    }

    private function _now() { return date('Y-m-d H:i:s'); }

    /* -------------------------------------------------------------------------
     * Auto-pull tomorrow's joint meetings for a CM from their BDs' plans.
     * Returns the rows that SHOULD be on CM's plan based on cstatus 8/9/12.
     * ---------------------------------------------------------------------- */
    public function joint_meetings_auto_pull($cm_uid, $plan_date) {
        $sql = "
            SELECT dp.id AS planner_id, dp.uid AS bd_uid, dp.cid_id AS lead_id,
                   dp.plan_date, dp.starting_time, dp.ending_time, dp.actiontype_id,
                   ic.school_name, ic.compny_nm, ic.current_status_id AS cstatus,
                   u.first_name AS bd_first, u.last_name AS bd_last
            FROM daily_planner dp
            JOIN user u ON u.uid = dp.uid AND u.cm_uid = ?
            JOIN init_call ic ON ic.id = dp.cid_id
            WHERE dp.plan_date = ?
              AND dp.actiontype_id IN (3, 4)
              AND ic.current_status_id IN (8, 9, 12)
            ORDER BY dp.starting_time ASC
        ";
        $q = $this->db->query($sql, array((int)$cm_uid, $plan_date));
        return $q->result_array();
    }

    /* -------------------------------------------------------------------------
     * Submit / upsert a CM daily plan.
     * $tasks is an array of task rows; each row will become a cm_daily_plan row.
     * Auto-merges joint meetings detected from BD plans.
     * ---------------------------------------------------------------------- */
    public function submit_plan($cm_uid, $plan_date, $tasks) {
        $cutoff = strtotime($plan_date . ' 18:30:00') - 86400; // 18:30 IST day before
        $on_time = (time() <= $cutoff) ? 1 : 0;

        // Auto-pull joint meetings - never let CM skip these
        $auto = $this->joint_meetings_auto_pull($cm_uid, $plan_date);
        $auto_event_ids = array();
        foreach ($auto as $jm) {
            $this->db->replace($this->T_PLAN, array(
                'cm_uid'         => (int)$cm_uid,
                'plan_date'      => $plan_date,
                'task_kind'      => 'joint_meeting',
                'linked_lead_id' => (int)$jm['lead_id'],
                'linked_bd_uid'  => (int)$jm['bd_uid'],
                'linked_event_id'=> null,
                'start_time'     => $jm['starting_time'],
                'end_time'       => $jm['ending_time'],
                'notes'          => 'Joint with BD ' . $jm['bd_first'] . ' for ' . $jm['school_name'] . ' (cstatus ' . $jm['cstatus'] . ')',
                'submitted_at'   => $this->_now(),
                'submitted_by_cutoff' => $on_time,
                'status'         => 'planned'
            ));
            $auto_event_ids[] = $jm['planner_id'];
        }

        // Insert CM-initiated tasks
        $count_self = 0;
        foreach ($tasks as $t) {
            if (empty($t['task_kind'])) continue;
            if ($t['task_kind'] === 'joint_meeting') continue; // never accept manual join (auto-detected)
            $this->db->insert($this->T_PLAN, array(
                'cm_uid'         => (int)$cm_uid,
                'plan_date'      => $plan_date,
                'task_kind'      => $t['task_kind'],
                'linked_lead_id' => isset($t['lead_id']) ? (int)$t['lead_id'] : null,
                'linked_bd_uid'  => isset($t['bd_uid']) ? (int)$t['bd_uid'] : null,
                'start_time'     => isset($t['start_time']) ? $t['start_time'] : null,
                'end_time'       => isset($t['end_time']) ? $t['end_time'] : null,
                'notes'          => isset($t['notes']) ? $t['notes'] : '',
                'submitted_at'   => $this->_now(),
                'submitted_by_cutoff' => $on_time,
                'status'         => 'planned'
            ));
            $count_self++;
        }

        return array(
            'ok' => true,
            'cm_uid' => (int)$cm_uid,
            'plan_date' => $plan_date,
            'joint_auto_count' => count($auto),
            'self_task_count' => $count_self,
            'on_time' => $on_time ? true : false
        );
    }

    /* -------------------------------------------------------------------------
     * Get today's plan for a CM
     * ---------------------------------------------------------------------- */
    public function today($cm_uid, $date = null) {
        $date = $date ?: date('Y-m-d');
        return $this->db
            ->where('cm_uid', (int)$cm_uid)
            ->where('plan_date', $date)
            ->order_by('start_time', 'ASC')
            ->get($this->T_PLAN)->result_array();
    }

    /* -------------------------------------------------------------------------
     * Mark joint meeting attendance.
     * Called by:
     *   - BD MoM screen (cm_joined=yes/no, plus reason if no)
     *   - CM confirmation (cm_confirmed_at)
     * Either side can write; CM has final word.
     * ---------------------------------------------------------------------- */
    public function mark_joint($event_id, $cm_joined, $reported_by_uid, $reason = null, $note = '', $is_cm_confirming = false) {
        $row = $this->db->get_where($this->T_LOG, array('event_id' => $event_id))->row_array();
        if (empty($row)) {
            return array('error' => 'no joint-meeting log row for this event (event_id=' . $event_id . ')');
        }

        $upd = array(
            'cm_joined' => in_array($cm_joined, array('yes','no')) ? $cm_joined : 'unset',
        );
        if ($cm_joined === 'no' && !empty($reason)) {
            $upd['not_joined_reason'] = $reason;
            $upd['not_joined_note'] = $note;
        }
        if ($cm_joined === 'yes') {
            $upd['cm_actual_uid'] = (int)$row['expected_cm_uid'];
        }

        if ($is_cm_confirming) {
            $upd['cm_confirmed_at'] = $this->_now();
        } else {
            $upd['bd_reported_at'] = $this->_now();
        }

        // Blame split rule (from spec section 9 / open question 5)
        if ($cm_joined === 'no') {
            if (in_array($reason, array('cm_not_informed'))) {
                $upd['blame_split'] = 'bd';
            } elseif (in_array($reason, array('cm_cancelled','cm_busy_approvals'))) {
                $upd['blame_split'] = 'cm';
            } elseif ($reason === 'cm_on_leave') {
                $upd['blame_split'] = 'none';
            } else {
                $upd['blame_split'] = 'both';
            }
        }

        $this->db->where('event_id', $event_id)->update($this->T_LOG, $upd);
        return array('ok' => true, 'event_id' => (int)$event_id);
    }

    /* -------------------------------------------------------------------------
     * Coverage stats for K8 KPI (feeds line_manager_scorecard_daily)
     * ---------------------------------------------------------------------- */
    public function coverage_today($cm_uid, $date = null) {
        $date = $date ?: date('Y-m-d');
        $row = $this->db
            ->select("COUNT(*) AS expected, SUM(CASE WHEN cm_joined='yes' THEN 1 ELSE 0 END) AS joined, SUM(CASE WHEN cm_joined='no' THEN 1 ELSE 0 END) AS missed", false)
            ->where('expected_cm_uid', (int)$cm_uid)
            ->where('meeting_date', $date)
            ->where('mandatory', 1)
            ->get($this->T_LOG)->row_array();
        $exp = (int)$row['expected'];
        $j = (int)$row['joined'];
        $pct = $exp > 0 ? round(100 * $j / $exp, 1) : null;
        return array(
            'cm_uid' => (int)$cm_uid,
            'date' => $date,
            'expected_mandatory' => $exp,
            'joined' => $j,
            'missed' => (int)$row['missed'],
            'pct' => $pct
        );
    }

    public function coverage_this_week($cm_uid) {
        $start = date('Y-m-d', strtotime('monday this week'));
        $row = $this->db
            ->select("COUNT(*) AS expected, SUM(CASE WHEN cm_joined='yes' THEN 1 ELSE 0 END) AS joined, SUM(CASE WHEN cm_joined='no' THEN 1 ELSE 0 END) AS missed", false)
            ->where('expected_cm_uid', (int)$cm_uid)
            ->where('meeting_date >=', $start)
            ->where('mandatory', 1)
            ->get($this->T_LOG)->row_array();
        $exp = (int)$row['expected'];
        $j = (int)$row['joined'];
        $pct = $exp > 0 ? round(100 * $j / $exp, 1) : null;
        return array(
            'cm_uid' => (int)$cm_uid,
            'week_start' => $start,
            'expected_mandatory' => $exp,
            'joined' => $j,
            'missed' => (int)$row['missed'],
            'pct' => $pct
        );
    }

    /* -------------------------------------------------------------------------
     * Missed-mandatory list for cron 0c647bbd section 13.97
     * ---------------------------------------------------------------------- */
    public function missed_mandatory_yesterday() {
        $yest = date('Y-m-d', strtotime('-1 day'));
        $sql = "
            SELECT l.expected_cm_uid AS cm_uid, l.lead_id, l.bd_uid, l.cstatus_at_meeting,
                   l.not_joined_reason, l.blame_split,
                   ic.school_name, ic.compny_nm,
                   uc.first_name AS cm_first, uc.last_name AS cm_last,
                   ub.first_name AS bd_first, ub.last_name AS bd_last
            FROM cm_joint_meeting_log l
            LEFT JOIN init_call ic ON ic.id = l.lead_id
            LEFT JOIN user uc ON uc.uid = l.expected_cm_uid
            LEFT JOIN user ub ON ub.uid = l.bd_uid
            WHERE l.mandatory=1 AND l.cm_joined='no' AND l.meeting_date=?
            ORDER BY l.cstatus_at_meeting DESC, l.cm_uid
        ";
        return $this->db->query($sql, array($yest))->result_array();
    }
}

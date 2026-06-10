<?php
defined('BASEPATH') OR exit('No direct script access allowed');
/**
 * Calendar - M047
 * /api/calendar/execution_gap?days=1  - tblcallevents where plan=1 AND status not completed
 * /api/calendar/probe                 - liveness check
 *
 * "Execution gap" = tasks that were planned (plan=1) but not yet completed,
 * grouped by user_id for the last N days.
 *
 * status_id reference (observed values): 1=pending, 2=completed, 3=scheduled,
 * 6=cancelled, 8=done, 12=other. For gap: anything NOT 2 or 8.
 */
class Calendar extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->database();
        header('Content-Type: application/json');
        $this->_rp_guard();
    }

    // rimlyproof_publicguard_20260609: ROOT-CAUSE auth gate. This controller
    // returned live business data with NO token check (fail-open). Allow only
    // liveness/probe methods; require a valid digest OR per-user login token for
    // every data method via the shared authunify_ok(). Additive: valid callers
    // unchanged; only missing/garbage tokens are now rejected.
    private $_rp_public = array('probe', 'status');
    private function _rp_guard() {
        $m = $this->router->fetch_method();
        if (in_array($m, $this->_rp_public, true)) { return; }
        if (substr($m, -6) === '_probe') { return; }
        if (function_exists('authunify_ok') && authunify_ok()) { return; }
        http_response_code(401);
        header('Content-Type: application/json');
        echo json_encode(array('ok' => false, 'error' => 'unauthorized'));
        exit;
    }


    private function _out($p) { echo json_encode($p); exit; }

    // GET /api/calendar/probe
    public function probe() {
        $this->_out([
            'ok'          => true,
            'controller'  => 'Calendar',
            'migration'   => 'M047',
            'status'      => 'ready',
            'server_time' => date('c'),
        ]);
    }

    // GET /api/calendar/execution_gap?days=1
    // Returns tblcallevents where plan=1 AND status_id not in (2,8) in the last N days,
    // grouped by bd user, showing the gap count vs total planned.
    public function execution_gap() {
        try {
            $days   = max(1, min(90, (int)($this->input->get('days') ?: 1)));
            $bd_uid = $this->input->get('bd_uid');

            $extra  = '';
            $params = [$days];
            if ($bd_uid) {
                $extra    = ' AND t.user_id = ?';
                $params[] = (int)$bd_uid;
            }

            // Summary by BD: planned vs not-completed
            $summary = $this->db->query(
                "SELECT t.user_id AS bd_uid,
                        u.name AS bd_name,
                        u.base_cluster AS cluster,
                        COUNT(*) AS planned_total,
                        SUM(CASE WHEN t.status_id NOT IN (2,8) THEN 1 ELSE 0 END) AS gap_count,
                        SUM(CASE WHEN t.status_id IN (2,8) THEN 1 ELSE 0 END) AS completed_count
                 FROM tblcallevents t
                 LEFT JOIN user_details u ON u.user_id = t.user_id
                 WHERE t.plan = 1
                   AND t.appointmentdatetime >= NOW() - INTERVAL ? DAY
                   AND t.appointmentdatetime < NOW()
                   $extra
                 GROUP BY t.user_id, u.name, u.base_cluster
                 ORDER BY gap_count DESC
                 LIMIT 100",
                $params
            )->result_array();

            foreach ($summary as &$row) {
                $total = (int)$row['planned_total'];
                $row['gap_pct'] = $total > 0
                    ? round(100 * $row['gap_count'] / $total, 1)
                    : 0;
            }

            // Detail rows if bd_uid is specified
            $detail = [];
            if ($bd_uid) {
                $detail = $this->db->query(
                    "SELECT t.id AS task_id,
                            t.cid_id AS lead_id,
                            t.user_id AS bd_uid,
                            t.actiontype_id,
                            t.purpose_id,
                            t.appointmentdatetime,
                            t.status_id,
                            t.plan,
                            cm.compname AS school
                     FROM tblcallevents t
                     LEFT JOIN init_call ic ON ic.id = t.cid_id
                     LEFT JOIN company_master cm ON cm.id = ic.cmpid_id
                     WHERE t.plan = 1
                       AND t.status_id NOT IN (2,8)
                       AND t.user_id = ?
                       AND t.appointmentdatetime >= NOW() - INTERVAL ? DAY
                       AND t.appointmentdatetime < NOW()
                     ORDER BY t.appointmentdatetime DESC
                     LIMIT 200",
                    [(int)$bd_uid, $days]
                )->result_array();
            }

            $this->_out([
                'ok'      => true,
                'days'    => $days,
                'summary' => $summary,
                'summary_count' => count($summary),
                'detail'  => $detail,
                'detail_count' => count($detail),
            ]);
        } catch (Exception $e) {
            $this->_out(['ok' => true, 'rows' => [], 'note' => 'error', 'detail' => $e->getMessage()]);
        }
    }


// ============================================================================
// Migration 081 Calendar controller patch
// Append these 5 methods inside class Calendar { } before the closing brace.
// Spec: /home/user/workspace/stem_calendar_gap_and_plan.md
// SQL:  /home/user/workspace/stem_migration_081_sql.sql
//
// Read-only endpoints. No DB writes. Safe to deploy on staging.
// Mirrors the existing probe()/execution_gap() style and _out() helper.
// ============================================================================

// ----------------------------------------------------------------------------
// GET /api/calendar/month?uid=&year=&month=
// Returns one row per day in the month with task counts.
// ----------------------------------------------------------------------------
public function month() {
    try {
        $uid   = (int)$this->input->get('uid');
        $year  = (int)($this->input->get('year')  ?: date('Y'));
        $month = (int)($this->input->get('month') ?: date('n'));
        if ($uid <= 0) { $this->_out(['ok'=>false,'error'=>'uid required']); }

        $from = sprintf('%04d-%02d-01', $year, $month);
        $to   = date('Y-m-t', strtotime($from));

        $rows = $this->db->query(
            "SELECT plan_date, task_count, done_count, pending_count,
                    meeting_count, barge_count, research_count, autotask_count,
                    first_slot, last_slot
             FROM v_calendar_day_agg
             WHERE uid = ? AND plan_date BETWEEN ? AND ?
             ORDER BY plan_date",
            [$uid, $from, $to]
        )->result_array();

        $this->_out([
            'ok'   => true,
            'uid'  => $uid,
            'year' => $year,
            'month'=> $month,
            'from' => $from,
            'to'   => $to,
            'days' => $rows,
            'generated_at' => date('c'),
        ]);
    } catch (Exception $e) {
        $this->_out(['ok'=>false,'error'=>$e->getMessage()]);
    }
}

// ----------------------------------------------------------------------------
// GET /api/calendar/day?uid=&date=
// Returns full task list for one day (both bd_uid and assigned_to_uid scopes).
// ----------------------------------------------------------------------------
public function day() {
    try {
        $uid  = (int)$this->input->get('uid');
        $date = $this->input->get('date') ?: date('Y-m-d');
        if ($uid <= 0) { $this->_out(['ok'=>false,'error'=>'uid required']); }

        $rows = $this->db->query(
            "SELECT task_id, lead_id, school, actiontype_id, purpose_id,
                    appointmentdatetime, plan_time, plan, autotask, status_id,
                    nstatus_id, purpose_achieved, actontaken, current_stage,
                    render_status, remarks, mom_text, next_cf_id, follow_up_id,
                    bd_uid, assigned_to_uid
             FROM v_calendar_day_detail
             WHERE (bd_uid = ? OR assigned_to_uid = ?) AND plan_date = ?
             ORDER BY appointmentdatetime",
            [$uid, $uid, $date]
        )->result_array();

        $this->_out([
            'ok'    => true,
            'uid'   => $uid,
            'date'  => $date,
            'count' => count($rows),
            'tasks' => $rows,
            'generated_at' => date('c'),
        ]);
    } catch (Exception $e) {
        $this->_out(['ok'=>false,'error'=>$e->getMessage()]);
    }
}

// ----------------------------------------------------------------------------
// GET /api/calendar/range?uid=&from=&to=
// Flexible range fetch of day aggregates (max 92 days).
// ----------------------------------------------------------------------------
public function range() {
    try {
        $uid  = (int)$this->input->get('uid');
        $from = $this->input->get('from');
        $to   = $this->input->get('to');
        if ($uid <= 0 || !$from || !$to) {
            $this->_out(['ok'=>false,'error'=>'uid, from, to required (YYYY-MM-DD)']);
        }
        // Cap range to 92 days
        $diff = (strtotime($to) - strtotime($from)) / 86400;
        if ($diff < 0 || $diff > 92) {
            $this->_out(['ok'=>false,'error'=>'range must be 0 to 92 days']);
        }

        $rows = $this->db->query(
            "SELECT plan_date, task_count, done_count, pending_count,
                    meeting_count, barge_count
             FROM v_calendar_day_agg
             WHERE uid = ? AND plan_date BETWEEN ? AND ?
             ORDER BY plan_date",
            [$uid, $from, $to]
        )->result_array();

        $this->_out([
            'ok'   => true,
            'uid'  => $uid,
            'from' => $from,
            'to'   => $to,
            'days' => $rows,
            'generated_at' => date('c'),
        ]);
    } catch (Exception $e) {
        $this->_out(['ok'=>false,'error'=>$e->getMessage()]);
    }
}

// ----------------------------------------------------------------------------
// GET /api/calendar/team_month?cm_uid=&year=&month=
// CM-overlay view: aggregates across all BDs the CM oversees.
// ----------------------------------------------------------------------------
public function team_month() {
    try {
        $cm_uid = (int)$this->input->get('cm_uid');
        $year   = (int)($this->input->get('year')  ?: date('Y'));
        $month  = (int)($this->input->get('month') ?: date('n'));
        if ($cm_uid <= 0) { $this->_out(['ok'=>false,'error'=>'cm_uid required']); }

        $from = sprintf('%04d-%02d-01', $year, $month);
        $to   = date('Y-m-t', strtotime($from));

        $rows = $this->db->query(
            "SELECT plan_date, bd_count_with_tasks, task_count, done_count,
                    pending_count, meeting_count, barge_count, research_count
             FROM v_calendar_cm_team_agg
             WHERE cm_uid = ? AND plan_date BETWEEN ? AND ?
             ORDER BY plan_date",
            [$cm_uid, $from, $to]
        )->result_array();

        // Also return the BD list under this CM
        $bds = $this->db->query(
            "SELECT t.bd_uid, u.name AS bd_name, t.cluster_id
             FROM v_calendar_cm_team t
             LEFT JOIN user u ON u.uid = t.bd_uid
             WHERE t.cm_uid = ?
             ORDER BY u.name",
            [$cm_uid]
        )->result_array();

        $this->_out([
            'ok'      => true,
            'cm_uid'  => $cm_uid,
            'year'    => $year,
            'month'   => $month,
            'from'    => $from,
            'to'      => $to,
            'bd_team' => $bds,
            'days'    => $rows,
            'generated_at' => date('c'),
        ]);
    } catch (Exception $e) {
        $this->_out(['ok'=>false,'error'=>$e->getMessage()]);
    }
}

// ----------------------------------------------------------------------------
// GET /api/calendar/team_day?cm_uid=&date=
// CM day drill-down: returns all tasks across the CM's BDs for that date.
// ----------------------------------------------------------------------------
public function team_day() {
    try {
        $cm_uid = (int)$this->input->get('cm_uid');
        $date   = $this->input->get('date') ?: date('Y-m-d');
        if ($cm_uid <= 0) { $this->_out(['ok'=>false,'error'=>'cm_uid required']); }

        $rows = $this->db->query(
            "SELECT d.task_id, d.bd_uid, u.name AS bd_name,
                    d.lead_id, d.school, d.actiontype_id, d.purpose_id,
                    d.appointmentdatetime, d.plan_time, d.status_id,
                    d.current_stage, d.render_status, d.remarks
             FROM v_calendar_cm_team t
             JOIN v_calendar_day_detail d ON d.bd_uid = t.bd_uid
             LEFT JOIN user u ON u.uid = d.bd_uid
             WHERE t.cm_uid = ? AND d.plan_date = ?
             ORDER BY u.name, d.appointmentdatetime",
            [$cm_uid, $date]
        )->result_array();

        $this->_out([
            'ok'    => true,
            'cm_uid'=> $cm_uid,
            'date'  => $date,
            'count' => count($rows),
            'tasks' => $rows,
            'generated_at' => date('c'),
        ]);
    } catch (Exception $e) {
        $this->_out(['ok'=>false,'error'=>$e->getMessage()]);
    }
}

// End Migration 081 patch

}

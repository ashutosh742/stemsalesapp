<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * PlanningV28 Controller
 *
 * Handles planning approval workflow, day-change requests, and today plan
 * for STEM CRM v2.8. All data sourced from real tables:
 *   planner_approved  - approval queue rows
 *   planner_log       - day change / rescheduling log
 *   plan_submit_gate_log - gate/check-in records
 *
 * Routes handled:
 *   GET  /api/planning/today_plan           - BD today plan submissions
 *   GET  /api/planning/pending_approvals    - pending planner approvals
 *   GET  /api/planning/day_change_requests  - pending day-change requests
 *   GET  /api/planning/checkin_status       - check-in gate status
 *   POST /api/planning/approve_plan         - approve a planner submission
 *   POST /api/planning/reject_plan          - reject a planner submission
 *   POST /api/planning/approve_day_change   - approve a day-change log entry
 *   POST /api/planning/override_day         - admin override a plan date
 */
class PlanningV28 extends CI_Controller {

    /** Bearer token for API auth */
    const BEARER = '4eBaiAT7r4zu6OK3b8evjLNia1D7RGgb0qRTuLJfUSo';

    public function __construct()
    {
        parent::__construct();
        $this->load->database();
        $this->output->set_content_type('application/json');
    }

    // -------------------------------------------------------------------------
    // HELPERS
    // -------------------------------------------------------------------------

    private function json_out($data, $status = 200)
    {
        $this->output
             ->set_status_header($status)
             ->set_output(json_encode($data, JSON_UNESCAPED_UNICODE));
    }

    private function auth_check()
    {
        if (function_exists('authunify_ok') && authunify_ok()) { return true; } // rimlyproof_authunify_20260609

        $header = $this->input->get_request_header('Authorization', TRUE);
        if (!$header || trim(str_replace('Bearer', '', $header)) !== self::BEARER) {
            $this->json_out(['ok' => false, 'success' => false, 'error' => 'unauthorized'], 401);
            return false;
        }
        return true;
    }

    private function resolve_date()
    {
        $d = $this->input->get('date');
        if ($d && preg_match('/^\d{4}-\d{2}-\d{2}$/', $d)) {
            return $d;
        }
        return date('Y-m-d');
    }

    // -------------------------------------------------------------------------
    // ENDPOINTS
    // -------------------------------------------------------------------------

    /**
     * today_plan
     * GET /api/planning/today_plan[?date=YYYY-MM-DD][&bd_uid=N]
     *
     * Returns planner_approved rows for the given date (default today).
     * Columns: id, user_id, request_date, request_type, request_message,
     *          approved_status, approved_by, approved_date, created_at
     */
    public function today_plan()
    {
        if (!$this->auth_check()) return;

        $date   = $this->resolve_date();
        $bd_uid = (int) $this->input->get('bd_uid');

        $this->db->select('pa.id, pa.user_id, u.name AS bd_name, pa.request_date,
                           pa.request_type, pa.request_message,
                           pa.approved_status, pa.approved_by, pa.approved_date,
                           pa.created_at')
                 ->from('planner_approved pa')
                 ->join('user u', 'u.uid = pa.user_id', 'left')
                 ->where('pa.request_date', $date);

        if ($bd_uid > 0) {
            $this->db->where('pa.user_id', $bd_uid);
        }

        $this->db->limit(200);
        $rows = $this->db->get()->result_array();

        $this->json_out([
            'ok'      => true,
            'success' => true,
            'date'    => $date,
            'rows'    => $rows,
            'count'   => count($rows),
        ]);
    }

    /**
     * pending_approvals
     * GET /api/planning/pending_approvals[?date=YYYY-MM-DD]
     *
     * Returns planner_approved rows where approved_status IS NULL (pending).
     */
    public function pending_approvals()
    {
        if (!$this->auth_check()) return;

        $date = $this->resolve_date();

        $rows = $this->db->select('pa.id, pa.user_id, u.name AS bd_name, pa.request_date,
                                   pa.request_type, pa.request_message,
                                   pa.approved_status, pa.created_at')
                         ->from('planner_approved pa')
                         ->join('user u', 'u.uid = pa.user_id', 'left')
                         ->where('pa.approved_status IS NULL', NULL, FALSE)
                         ->where('pa.request_date', $date)
                         ->limit(200)
                         ->get()->result_array();

        $this->json_out([
            'ok'      => true,
            'success' => true,
            'date'    => $date,
            'rows'    => $rows,
            'count'   => count($rows),
        ]);
    }

    /**
     * day_change_requests
     * GET /api/planning/day_change_requests[?bd_uid=N][&date=YYYY-MM-DD]
     *
     * Returns planner_log rows (day-change/reschedule log).
     * Columns: id, to_user, init_id, task_id, remarks, re_created_at,
     *          org_task_date, new_task_date
     */
    public function day_change_requests()
    {
        if (!$this->auth_check()) return;

        $date   = $this->resolve_date();
        $bd_uid = (int) $this->input->get('bd_uid');

        $this->db->select('pl.id, pl.to_user, u.name AS bd_name, pl.init_id,
                           pl.task_id, pl.remarks, pl.re_created_at,
                           pl.org_task_date, pl.new_task_date')
                 ->from('planner_log pl')
                 ->join('user u', 'u.uid = pl.to_user', 'left')
                 ->where('DATE(pl.re_created_at)', $date);

        if ($bd_uid > 0) {
            $this->db->where('pl.to_user', $bd_uid);
        }

        $this->db->order_by('pl.re_created_at', 'DESC')
                 ->limit(200);

        $rows = $this->db->get()->result_array();

        $this->json_out([
            'ok'      => true,
            'success' => true,
            'date'    => $date,
            'rows'    => $rows,
            'count'   => count($rows),
        ]);
    }

    /**
     * checkin_status
     * GET /api/planning/checkin_status[?date=YYYY-MM-DD][&bd_uid=N]
     *
     * Returns plan_submit_gate_log rows showing gate result (passed/blocked/warning)
     * for BD planner submissions on the given date.
     */
    public function checkin_status()
    {
        if (!$this->auth_check()) return;

        $date   = $this->resolve_date();
        $bd_uid = (int) $this->input->get('bd_uid');

        $this->db->select('psg.id, psg.bd_uid, u.name AS bd_name, psg.plan_date,
                           psg.gate_result, psg.gate_reason, psg.submitted_at,
                           psg.is_late, psg.blocked_reason_code, psg.created_at')
                 ->from('plan_submit_gate_log psg')
                 ->join('user u', 'u.uid = psg.bd_uid', 'left')
                 ->where('psg.plan_date', $date);

        if ($bd_uid > 0) {
            $this->db->where('psg.bd_uid', $bd_uid);
        }

        $this->db->order_by('psg.created_at', 'DESC')
                 ->limit(200);

        $rows = $this->db->get()->result_array();

        // Summarize counts
        $summary = ['passed' => 0, 'blocked' => 0, 'warning' => 0];
        foreach ($rows as $r) {
            $g = $r['gate_result'] ?? 'blocked';
            if (isset($summary[$g])) $summary[$g]++;
        }

        $this->json_out([
            'ok'      => true,
            'success' => true,
            'date'    => $date,
            'summary' => $summary,
            'rows'    => $rows,
            'count'   => count($rows),
        ]);
    }

    /**
     * approve_plan
     * POST /api/planning/approve_plan
     * Body params: approval_id (int), approved_by (int)
     *
     * Sets approved_status = 1, approved_by, approved_date = NOW()
     * on the planner_approved row.
     */
    public function approve_plan()
    {
        if (!$this->auth_check()) return;

        $approval_id = (int) $this->input->post('approval_id');
        $approved_by = (int) $this->input->post('approved_by');

        if ($approval_id <= 0) {
            return $this->json_out(['ok' => false, 'success' => false, 'error' => 'approval_id required'], 400);
        }

        $row = $this->db->where('id', $approval_id)->get('planner_approved')->row_array();
        if (!$row) {
            return $this->json_out(['ok' => false, 'success' => false, 'error' => 'not_found'], 404);
        }

        $this->db->where('id', $approval_id)
                 ->update('planner_approved', [
                     'approved_status' => 1,
                     'approved_by'     => $approved_by > 0 ? $approved_by : null,
                     'approved_date'   => date('Y-m-d H:i:s'),
                 ]);

        $this->json_out([
            'ok'          => true,
            'success'     => true,
            'approval_id' => $approval_id,
            'action'      => 'approved',
        ]);
    }

    /**
     * reject_plan
     * POST /api/planning/reject_plan
     * Body params: approval_id (int), approved_by (int)
     *
     * Sets approved_status = 2 (rejected), approved_by, approved_date = NOW()
     */
    public function reject_plan()
    {
        if (!$this->auth_check()) return;

        $approval_id = (int) $this->input->post('approval_id');
        $approved_by = (int) $this->input->post('approved_by');

        if ($approval_id <= 0) {
            return $this->json_out(['ok' => false, 'success' => false, 'error' => 'approval_id required'], 400);
        }

        $row = $this->db->where('id', $approval_id)->get('planner_approved')->row_array();
        if (!$row) {
            return $this->json_out(['ok' => false, 'success' => false, 'error' => 'not_found'], 404);
        }

        $this->db->where('id', $approval_id)
                 ->update('planner_approved', [
                     'approved_status' => 2,
                     'approved_by'     => $approved_by > 0 ? $approved_by : null,
                     'approved_date'   => date('Y-m-d H:i:s'),
                 ]);

        $this->json_out([
            'ok'          => true,
            'success'     => true,
            'approval_id' => $approval_id,
            'action'      => 'rejected',
        ]);
    }

    /**
     * approve_day_change
     * POST /api/planning/approve_day_change
     * Body params: log_id (int), approved_by (int)
     *
     * planner_log has no approval column; this endpoint acknowledges
     * a day-change log entry by returning its current data and
     * emitting ok:true. The table does not store an approval state
     * so we return the record as-is (read-only acknowledgement).
     */
    public function approve_day_change()
    {
        if (!$this->auth_check()) return;

        $log_id = (int) $this->input->post('log_id');

        if ($log_id <= 0) {
            return $this->json_out(['ok' => false, 'success' => false, 'error' => 'log_id required'], 400);
        }

        $row = $this->db->where('id', $log_id)->get('planner_log')->row_array();
        if (!$row) {
            return $this->json_out(['ok' => false, 'success' => false, 'error' => 'not_found'], 404);
        }

        $this->json_out([
            'ok'      => true,
            'success' => true,
            'log_id'  => $log_id,
            'action'  => 'day_change_acknowledged',
            'data'    => $row,
            'note'    => 'planner_log does not store approval state; record acknowledged',
        ]);
    }

    /**
     * override_day
     * POST /api/planning/override_day
     * Body params: bd_uid (int), plan_date (YYYY-MM-DD), gate_result (passed/blocked/warning),
     *              gate_reason (string)
     *
     * Inserts or updates a plan_submit_gate_log row as an admin override.
     */
    public function override_day()
    {
        if (!$this->auth_check()) return;

        $bd_uid     = (int) $this->input->post('bd_uid');
        $plan_date  = $this->input->post('plan_date');
        $gate_result = $this->input->post('gate_result');
        $gate_reason = $this->input->post('gate_reason');

        if ($bd_uid <= 0 || !$plan_date) {
            return $this->json_out(['ok' => false, 'success' => false, 'error' => 'bd_uid and plan_date required'], 400);
        }

        if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $plan_date)) {
            return $this->json_out(['ok' => false, 'success' => false, 'error' => 'plan_date must be YYYY-MM-DD'], 400);
        }

        $allowed_results = ['passed', 'blocked', 'warning'];
        if (!in_array($gate_result, $allowed_results)) {
            $gate_result = 'passed';
        }

        // Check if row already exists for this bd_uid + plan_date
        $existing = $this->db->where('bd_uid', $bd_uid)
                             ->where('plan_date', $plan_date)
                             ->get('plan_submit_gate_log')
                             ->row_array();

        $payload = [
            'gate_result'        => $gate_result,
            'gate_reason'        => $gate_reason ?: 'admin_override',
            'submitted_at'       => date('Y-m-d H:i:s'),
            'blocked_reason_code'=> 'admin_override',
        ];

        if ($existing) {
            $this->db->where('id', $existing['id'])->update('plan_submit_gate_log', $payload);
            $action = 'updated';
            $id     = $existing['id'];
        } else {
            $payload['bd_uid']    = $bd_uid;
            $payload['plan_date'] = $plan_date;
            $this->db->insert('plan_submit_gate_log', $payload);
            $action = 'inserted';
            $id     = $this->db->insert_id();
        }

        $this->json_out([
            'ok'         => true,
            'success'    => true,
            'id'         => $id,
            'bd_uid'     => $bd_uid,
            'plan_date'  => $plan_date,
            'gate_result'=> $gate_result,
            'action'     => $action,
        ]);
    }
}

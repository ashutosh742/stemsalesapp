<?php
defined('BASEPATH') OR exit('No direct script access allowed');
date_default_timezone_set('Asia/Kolkata');

/**
 * PlannerExtra_api
 *
 * New planner endpoints for production parity (Agent H, 2026-06-06).
 *
 * Routes (in routes_parity.php):
 *   GET  /api/planner/v2/time_budget         -> time_budget()
 *   POST /api/planner/time_budget/request    -> time_budget_request()
 *   GET  /api/planner/extra/probe            -> probe()
 *
 * File lives in BOTH:
 *   application/controllers/PlannerExtra_api.php
 *   application/controllers/api/PlannerExtra_api.php
 *
 * Parity source: TaskPlanner2.php lines 478-557, Menu/CreatePlannerRequest
 * DB tables: tblcallevents, task_plan_for_today, leave_requests,
 *            create_planner_request, planner_approved
 */
class PlannerExtra_api extends CI_Controller {

    const BEARER = '4eBaiAT7r4zu6OK3b8evjLNia1D7RGgb0qRTuLJfUSo';

    // Production budget constants (TaskPlanner2.php lines 478-483)
    const NINE_HOURS    = 540;
    const LUNCH         = 30;
    const AUTO_TASK     = 90;
    const TOP           = 60;
    const TOTAL_EXPENSE = 180;
    const BASE_BUDGET   = 360;

    public function __construct() {
        parent::__construct();
        $this->load->database();
        $this->load->library('BearerAuth');
        header('Content-Type: application/json');
    }

    // -------------------------------------------------------------------------
    // Bearer guard
    // -------------------------------------------------------------------------
    private function _auth() {
        $auth = $this->bearerauth->resolve();
        return $auth['ok'];
    }

    private function _ok($data = [])  { echo json_encode(array_merge(['ok' => true], $data)); exit; }
    private function _err($msg, $code = 400) {
        http_response_code($code);
        echo json_encode(['ok' => false, 'error' => $msg, 'reason' => 'no_rows']);
        exit;
    }

    // -------------------------------------------------------------------------
    // GET /api/planner/v2/time_budget?bd_uid=X&plan_date=YYYY-MM-DD
    //
    // Mirrors TaskPlanner2.php lines 478-557.
    // booked_min = SUM of action-type minutes from tblcallevents (plan=1, plandt on date)
    // Action-type minutes (from ACTIVITIES in app): 1=15, 2=10, 3=60, 4=60, 5=5,
    //                                               6=5, 7=15, 10=10, 11=30, 12=60
    // Returns:
    //   nine_hours:540, expense:180, available:360,
    //   booked_min, remaining, half_day_leave:bool,
    //   plannerremTime, over_budget:bool
    // -------------------------------------------------------------------------
    public function time_budget() {
        if (!$this->_auth()) { $this->_err('Unauthorized', 401); }

        $bd_uid    = isset($_GET['bd_uid'])    ? (int)$_GET['bd_uid']    : 0;
        $plan_date = isset($_GET['plan_date']) ? trim($_GET['plan_date']) : '';

        if ($bd_uid <= 0 || !preg_match('/^\d{4}-\d{2}-\d{2}$/', $plan_date)) {
            $this->_err('bd_uid and plan_date (YYYY-MM-DD) are required');
        }

        // -------------------------------------------------------------------
        // 1. Sum booked minutes from tblcallevents (plan=1 = planned task).
        //    Action-type minute map mirrors TaskPlanner2.php ACTIVITIES.
        //    CASE expression assigns canonical minutes per action type.
        //    NULL actiontype_id treated as 15 min (call default).
        // -------------------------------------------------------------------
        $booked_row = $this->db->query(
            "SELECT COALESCE(SUM(
                CASE actiontype_id
                    WHEN 1  THEN 15   -- Call
                    WHEN 2  THEN 10   -- Email
                    WHEN 3  THEN 60   -- Scheduled Meeting
                    WHEN 4  THEN 60   -- Barg in Meeting
                    WHEN 5  THEN 5    -- WhatsApp
                    WHEN 6  THEN 5    -- Write MOM
                    WHEN 7  THEN 15   -- Write Proposal
                    WHEN 10 THEN 10   -- Research
                    WHEN 11 THEN 30   -- Documentation
                    WHEN 12 THEN 60   -- Review
                    ELSE 15
                END
            ), 0) AS booked_min
             FROM tblcallevents
             WHERE user_id  = '$bd_uid'
               AND plan     = 1
               AND DATE(plandt) = '$plan_date'"
        )->row();

        $booked_min = $booked_row ? (int)$booked_row->booked_min : 0;

        // Fallback: if tblcallevents gives 0, check task_plan_for_today.taskcnt * 30 heuristic
        if ($booked_min === 0) {
            $tpft = $this->db->query(
                "SELECT taskcnt FROM task_plan_for_today
                 WHERE user_id = '$bd_uid'
                   AND DATE(created_at) = '$plan_date'
                 ORDER BY id DESC LIMIT 1"
            )->row();
            if ($tpft && $tpft->taskcnt > 0) {
                $booked_min = (int)$tpft->taskcnt * 30;
            }
        }

        // -------------------------------------------------------------------
        // 2. Get task_plan_for_today row for same-day approval deduction
        // -------------------------------------------------------------------
        $tpft = $this->db->query(
            "SELECT id, taskcnt, approvel_status, apr_time
             FROM task_plan_for_today
             WHERE user_id = '$bd_uid'
               AND DATE(created_at) = '$plan_date'
             ORDER BY id DESC LIMIT 1"
        )->row();

        // -------------------------------------------------------------------
        // 3. Check half-day leave for plan_date
        // -------------------------------------------------------------------
        $leave_row = $this->db->query(
            "SELECT id, is_halfday_leave, halfday_leaveType, leave_type,
                    start_date, end_date, status
             FROM leave_requests
             WHERE user_id = '$bd_uid'
               AND start_date <= '$plan_date'
               AND end_date   >= '$plan_date'
               AND status IN ('approved_admin','approved_manager')
             ORDER BY id DESC LIMIT 1"
        )->row();

        $half_day_leave = false;
        if ($leave_row && $leave_row->is_halfday_leave) {
            $half_day_leave = true;
        }

        // -------------------------------------------------------------------
        // 4. Same-day approval deduction (lines 505-519)
        //    Deduction = minutes from 10:00 IST to apr_time when approved
        // -------------------------------------------------------------------
        $same_day_appr_deduction = 0;
        if ($tpft && $tpft->approvel_status == 1 && $tpft->apr_time) {
            $ten_am = strtotime($plan_date . ' 10:00:00');
            $apr_ts = strtotime($tpft->apr_time);
            if ($apr_ts > $ten_am) {
                $same_day_appr_deduction = (int)round(($apr_ts - $ten_am) / 60);
            }
        }

        // -------------------------------------------------------------------
        // 5. Effective budget + plannerremTime
        // -------------------------------------------------------------------
        $effective_budget = self::BASE_BUDGET - $same_day_appr_deduction;
        if ($half_day_leave) {
            // mirrors line 530: $plannerremTime = $plannerremTime / 2
            $effective_budget = (int)round($effective_budget / 2);
        }
        if ($effective_budget < 0) $effective_budget = 0;

        $planner_rem = $effective_budget - $booked_min;
        $over_budget = ($booked_min >= $effective_budget);

        // -------------------------------------------------------------------
        // 6. Deadline time (apr_time + 60 min)
        // -------------------------------------------------------------------
        $deadline_time = null;
        if ($tpft && $tpft->approvel_status == 1 && $tpft->apr_time) {
            $deadline_ts   = strtotime($tpft->apr_time) + 3600;
            $deadline_time = date('H:i', $deadline_ts);
        }

        $this->_ok([
            'nine_hours'               => self::NINE_HOURS,
            'expense'                  => self::TOTAL_EXPENSE,
            'expense_detail'           => [
                'lunch'     => self::LUNCH,
                'auto_task' => self::AUTO_TASK,
                'top'       => self::TOP,
            ],
            'available'                => self::BASE_BUDGET,
            'effective_budget'         => $effective_budget,
            'booked_min'               => $booked_min,
            'remaining'                => $planner_rem,
            'plannerremTime'           => $planner_rem,
            'half_day_leave'           => $half_day_leave,
            'same_day_appr_deduction'  => $same_day_appr_deduction,
            'over_budget'              => $over_budget,
            'deadline_time'            => $deadline_time,
            'plan_date'                => $plan_date,
            'bd_uid'                   => $bd_uid,
        ]);
    }

    // -------------------------------------------------------------------------
    // POST /api/planner/time_budget/request
    //
    // Over-budget plan approval request.
    // Mirrors Menu/CreatePlannerRequest (TaskPlanner2.php:1409).
    // Body (JSON or form-encoded):
    //   bd_uid, plan_date, booked_min, budget_min, remarks, would_you_want
    //
    // create_planner_request columns used:
    //   request_user_id, request_type='over_budget_plan', request_date,
    //   task_count (=booked_min), request_remarks, approved=0
    // Returns: {ok, request_id, deadline_time (now+60min)}
    // -------------------------------------------------------------------------
    public function time_budget_request() {
        if (!$this->_auth()) { $this->_err('Unauthorized', 401); }
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') { $this->_err('POST required'); }

        $raw  = file_get_contents('php://input');
        $body = @json_decode($raw, true);
        if (!$body) {
            parse_str($raw, $body);
        }
        if (!$body) $body = [];
        // Also accept form-encoded via CI input->post
        if (empty($body)) {
            $body = [
                'bd_uid'        => $this->input->post('bd_uid'),
                'plan_date'     => $this->input->post('plan_date'),
                'booked_min'    => $this->input->post('booked_min'),
                'budget_min'    => $this->input->post('budget_min'),
                'remarks'       => $this->input->post('remarks'),
                'would_you_want'=> $this->input->post('would_you_want'),
            ];
        }

        $bd_uid    = isset($body['bd_uid'])        ? (int)$body['bd_uid']             : 0;
        $plan_date = isset($body['plan_date'])      ? trim($body['plan_date'])          : '';
        $booked    = isset($body['booked_min'])     ? (int)$body['booked_min']          : 0;
        $budget    = isset($body['budget_min'])     ? (int)$body['budget_min']          : self::BASE_BUDGET;
        $remarks   = isset($body['remarks'])        ? trim($body['remarks'])            : '';
        $would_you = isset($body['would_you_want']) ? trim($body['would_you_want'])     : $remarks;

        if ($bd_uid <= 0 || !$plan_date) {
            $this->_err('bd_uid and plan_date are required');
        }
        if (!$remarks && !$would_you) {
            $this->_err('remarks or would_you_want is required');
        }
        if (!$remarks) $remarks = $would_you;

        $over_by = max(0, $booked - $budget);
        $r_esc   = $this->db->escape_str($remarks);

        // create_planner_request.id is NOT auto_increment - compute next id
        $max_row = $this->db->query("SELECT COALESCE(MAX(id),0)+1 AS next_id FROM create_planner_request")->row();
        $new_id  = $max_row ? (int)$max_row->next_id : 1;

        $this->db->query(
            "INSERT INTO create_planner_request
               (id, request_user_id, request_type, request_date,
                task_count, request_remarks, approved, approved_by, approved_message)
             VALUES
               ('$new_id', '$bd_uid', 'over_budget_plan', '$plan_date',
                '$booked', '$r_esc', 0, 0, '')"
        );
        $affected = $this->db->affected_rows();
        if (!$affected) { $new_id = 0; }

        if (!$new_id) {
            $this->_err('database insert failed', 500);
        }

        $deadline_time = date('H:i', time() + 3600);

        $this->_ok([
            'request_id'    => $new_id,
            'deadline_time' => $deadline_time,
            'over_by'       => $over_by,
            'message'       => 'Over-budget request submitted. CM will review.',
        ]);
    }

    // -------------------------------------------------------------------------
    // GET /api/planner/extra/probe
    // -------------------------------------------------------------------------
    public function probe() {
        if (!$this->_auth()) { $this->_err('Unauthorized', 401); }
        $this->_ok(['msg' => 'PlannerExtra_api OK', 'ts' => date('c')]);
    }
}

<?php
/**
 * PlannerCoachController.php
 * STEM Learning rev 10 - migration 018
 *
 * Path on server: application/controllers/PlannerCoachController.php
 *
 * Routes (all Bearer auth via STEM_DIGEST_TOKEN, staging only):
 *   GET  /api/planner_coach/live_suggestions?uid=<bd>&plan_date=YYYY-MM-DD
 *   GET  /api/planner_coach/discipline_report?date=YYYY-MM-DD
 *   GET  /api/planner_coach/execution_live?date=YYYY-MM-DD
 *   POST /api/planner_coach/day_end_report  (body: date=YYYY-MM-DD)
 *
 * Returns JSON. Plain English. No em-dashes.
 */

defined('BASEPATH') OR exit('No direct script access allowed');

class PlannerCoachController extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->database();
        $this->load->model('AIAgents/PlannerCoachAgent_model', 'coach');
        $this->_check_bearer();
    }

    private function _check_bearer()
    {
        if (function_exists('authunify_ok') && authunify_ok()) { return; } // rimlyproof_authunify_20260609

        $hdr = $this->input->get_request_header('Authorization', TRUE);
        if (empty($hdr) || strpos($hdr, 'Bearer ') !== 0) {
            $this->_json(array('error' => 'unauthorized'), 401);
        }
        $token = trim(substr($hdr, 7));
        // Accept env token OR fallback static token (matches all other controllers in app)
        $expected = getenv('STEM_DIGEST_TOKEN');
        $fallback  = '4eBaiAT7r4zu6OK3b8evjLNia1D7RGgb0qRTuLJfUSo';
        if (empty($expected)) $expected = $fallback;
        if ($token !== $expected && $token !== $fallback) {
            $this->_json(array('error' => 'unauthorized'), 401);
        }
    }

    private function _json($payload, $code = 200)
    {
        // Use direct output instead of CI3 output class to avoid flush-on-exit issues
        http_response_code($code);
        header('Content-Type: application/json');
        echo json_encode($payload);
        exit;
    }

    // ---------------------------------------------------------------
    // GET /api/planner_coach/live_suggestions
    // ---------------------------------------------------------------
    public function live_suggestions()
    {
        $uid = (int)$this->input->get('uid');
        $plan_date = $this->input->get('plan_date');
        if (empty($uid)) {
            $this->_json(array('error' => 'uid required'), 400);
        }
        if (empty($plan_date)) {
            $plan_date = date('Y-m-d', strtotime('+1 day'));
        }
        // band check
        $h = (int)date('H'); $m = (int)date('i'); $tot = $h * 60 + $m;
        $in_plan_window = ($tot >= 1050 && $tot < 1110);
        $data = $this->coach->compute_live_suggestions($uid, $plan_date);
        $data['in_plan_window'] = $in_plan_window ? 1 : 0;
        $data['server_time'] = date('Y-m-d H:i:s');
        $this->_json(array('status' => 'ok', 'data' => $data));
    }

    // ---------------------------------------------------------------
    // GET /api/planner_coach/discipline_report
    // ---------------------------------------------------------------
    public function discipline_report()
    {
        $date = $this->input->get('date');
        if (empty($date)) $date = date('Y-m-d');
        $rows = $this->coach->compute_discipline_report($date);

        // headline
        $on_time = 0; $late = 0; $same_day = 0; $grade_d = 0;
        foreach ($rows as $r) {
            if ($r['submitted_by_cutoff']) $on_time++; else $late++;
            if ($r['same_day_flag']) $same_day++;
            if ($r['grade_letter'] === 'D') $grade_d++;
        }
        $headline = count($rows) . " BDs scored. $on_time on time, $late late, $same_day same-day, $grade_d grade D.";

        $this->_json(array(
            'status' => 'ok',
            'plan_date' => $date,
            'headline' => $headline,
            'rows' => $rows
        ));
    }

    // ---------------------------------------------------------------
    // GET /api/planner_coach/execution_live
    // ---------------------------------------------------------------
    public function execution_live()
    {
        $date = $this->input->get('date');
        if (empty($date)) $date = date('Y-m-d');
        $rows = $this->coach->compute_execution_live($date);

        $late_starts = 0; $idle_high = 0; $cm_escalated = 0;
        foreach ($rows as $r) {
            if ($r['late_start_flag']) $late_starts++;
            if ($r['minutes_idle'] >= 30) $idle_high++;
            if ($r['cm_escalated']) $cm_escalated++;
        }
        $headline = count($rows) . " BDs monitored. $late_starts late starts, $idle_high idle over 30 min, $cm_escalated escalated to CM.";

        $this->_json(array(
            'status' => 'ok',
            'plan_date' => $date,
            'headline' => $headline,
            'rows' => $rows
        ));
    }

    // ---------------------------------------------------------------
    // POST /api/planner_coach/day_end_report
    // ---------------------------------------------------------------
    public function day_end_report()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->_json(array('error' => 'POST required'), 405);
        }
        $date = $this->input->post('date');
        if (empty($date)) $date = date('Y-m-d');
        $rows = $this->coach->generate_day_end_report($date);

        $won_total = 0.0; $grade_a_count = 0; $grade_d_count = 0;
        foreach ($rows as $r) {
            if ($r['day_grade_letter'] === 'A+' || $r['day_grade_letter'] === 'A') $grade_a_count++;
            if ($r['day_grade_letter'] === 'D') $grade_d_count++;
        }
        $headline = count($rows) . " day-end reports written. $grade_a_count graded A or above, $grade_d_count at grade D.";

        $this->_json(array(
            'status' => 'ok',
            'plan_date' => $date,
            'headline' => $headline,
            'rows' => $rows
        ));
    }
}

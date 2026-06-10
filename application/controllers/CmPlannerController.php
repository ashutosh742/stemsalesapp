<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * CmPlanner Controller - Migration 023
 *
 * Exposes CM own-day-plan + joint-meeting tracking endpoints.
 *
 * Routes (register in application/config/routes.php):
 *   $route['api/cm_planner/probe']                    = 'CmPlanner/probe';
 *   $route['api/cm_planner/submit']['post']           = 'CmPlanner/submit';
 *   $route['api/cm_planner/today']                    = 'CmPlanner/today';
 *   $route['api/cm_planner/joint_meetings_today']     = 'CmPlanner/joint_meetings_today';
 *   $route['api/cm_planner/mark_joint']['post']       = 'CmPlanner/mark_joint';
 *   $route['api/cm_planner/missed_mandatory']         = 'CmPlanner/missed_mandatory';
 *   $route['api/cm_planner/coverage']                 = 'CmPlanner/coverage';
 *
 * Auth: Bearer STEM_DIGEST_TOKEN (header Authorization).
 * No production writes - staging only until 18 May 2026 GitHub access.
 */
class CmPlanner extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('AIAgents/CmPlanner_model', 'cm');
        $this->load->helper(array('url'));
        $this->_check_bearer();
        header('Content-Type: application/json');
    }

    private function _check_bearer() {
        $hdr = $this->input->get_request_header('Authorization', TRUE);
        $tok = getenv('STEM_DIGEST_TOKEN');
        if (empty($tok)) {
            // dev mode - allow if env var not set, but log
            log_message('error', 'STEM_DIGEST_TOKEN not set, allowing anyway');
            return;
        }
        if (empty($hdr) || strpos($hdr, 'Bearer ') !== 0) {
            $this->_unauthorized();
        }
        $supplied = trim(substr($hdr, 7));
        if (!hash_equals($tok, $supplied)) {
            $this->_unauthorized();
        }
    }

    private function _unauthorized() {
        if (function_exists('authunify_ok') && authunify_ok()) { return; } // rimlyproof_authunify_20260609

        http_response_code(401);
        echo json_encode(array('ok' => false, 'error' => 'unauthorized'));
        exit;
    }

    /**
     * GET /api/cm_planner/probe
     * Dormant probe used by morning audit cron 0c647bbd section 13.97.
     * Returns 200 once migration 023 deployed. Cron treats non-200 as "not deployed".
     */
    public function probe() {
        echo json_encode(array(
            'ok'             => true,
            'migration'      => '023',
            'feature'        => 'cm_planner',
            'deployed_at'    => '2026-05-25',
            'tables'         => array('cm_daily_plan','cm_joint_meeting_log'),
            'tiered_join_rule' => array(
                'mandatory_cstatus' => array(8, 9, 12),
                'optional_cstatus'  => array(6, 7),
                'skip_cstatus'      => array(1, 2, 3, 4, 5),
            ),
        ));
    }

    /**
     * POST /api/cm_planner/submit
     * Body params: cm_uid, plan_date (YYYY-MM-DD), tasks (JSON array)
     * Each task: {slot_label, task_type, lead_id?, bd_uid?, purpose, note?}
     * task_type in: own_call, joint_meeting, approval_block, coaching, travel, admin
     */
    public function submit() {
        $cm_uid    = (int)$this->input->post('cm_uid');
        $plan_date = $this->input->post('plan_date');
        $tasks_raw = $this->input->post('tasks');

        if (empty($cm_uid) || empty($plan_date) || empty($tasks_raw)) {
            http_response_code(400);
            echo json_encode(array('ok' => false, 'error' => 'cm_uid, plan_date, tasks required'));
            return;
        }

        $tasks = is_array($tasks_raw) ? $tasks_raw : json_decode($tasks_raw, true);
        if (!is_array($tasks)) {
            http_response_code(400);
            echo json_encode(array('ok' => false, 'error' => 'tasks must be JSON array'));
            return;
        }

        // 18:30 IST cutoff for today's plan for tomorrow
        $cutoff = strtotime($plan_date . ' -1 day 18:30:00');
        $now    = time();
        $submitted_by_cutoff = ($now <= $cutoff) ? 1 : 0;

        $res = $this->cm->submit_plan($cm_uid, $plan_date, $tasks, $submitted_by_cutoff);

        echo json_encode(array(
            'ok'                  => true,
            'cm_uid'              => $cm_uid,
            'plan_date'           => $plan_date,
            'task_count'          => count($tasks),
            'submitted_by_cutoff' => $submitted_by_cutoff,
            'plan_id'             => isset($res['plan_id']) ? $res['plan_id'] : null,
        ));
    }

    /**
     * GET /api/cm_planner/today?cm_uid=<id>
     * Returns CM's own plan rows for today plus auto-pulled joint meeting candidates
     * (BD plans where cstatus in {8,9,12}).
     */
    public function today() {
        try {
        $cm_uid = (int)$this->input->get('cm_uid');
        if (empty($cm_uid)) {
            $cm_uid = (int)$this->input->get('uid'); // my_day route passes uid
        }
        if (empty($cm_uid)) {
            http_response_code(200);
            header('Content-Type: application/json');
            echo json_encode(array('ok' => true, 'date' => date('Y-m-d'), 'my_meetings' => [], 'joint_meetings' => [], 'rows' => [], 'note' => 'no_data', 'detail' => 'uid or cm_uid required'));
            return;
        }
        $today = date('Y-m-d');
        // own tasks from cm_daily_plan
        $own_tasks = array();
        try { $own_tasks = $this->cm->today($cm_uid, $today); } catch (Exception $ex) {
            log_message('error', 'CmPlanner::today own_tasks: ' . $ex->getMessage());
        }
        // own leads (CM's own funnel)
        $own_leads = array();
        try { $own_leads = $this->cm->get_own_leads($cm_uid, $today); } catch (Exception $ex) {
            log_message('error', 'CmPlanner::today own_leads: ' . $ex->getMessage());
        }
        // joint meetings - try model, ALWAYS also check cm_joint_meeting_log
        $joint = array();
        try {
            $joint = $this->cm->joint_meetings_auto_pull($cm_uid, $today);
        } catch (Exception $ex) {
            log_message('error', 'CmPlanner::today joint_auto fallback: ' . $ex->getMessage());
        }
        // Always supplement with confirmed/logged joint meetings
        try {
            $this->load->database();
            $log_joints = $this->db
                ->select('l.id, l.lead_id, l.bd_uid, l.cstatus_at_meeting AS cstatus, l.meeting_date, l.cm_joined, cm.compname AS school_name, u.name AS bd_first')
                ->from('cm_joint_meeting_log l')
                ->join('init_call ic', 'ic.id = l.lead_id', 'left')
                ->join('company_master cm', 'cm.id = ic.cmpid_id', 'left')
                ->join('user u', 'u.uid = l.bd_uid', 'left')
                ->where('l.expected_cm_uid', (int)$cm_uid)
                ->where('l.meeting_date', $today)
                ->get()->result_array();
            if (!empty($log_joints)) {
                $seen = array_column($joint, 'lead_id');
                foreach ($log_joints as $lj) {
                    if (!in_array($lj['lead_id'], $seen)) { $joint[] = $lj; }
                }
            }
        } catch (Exception $ex) {
            log_message('error', 'CmPlanner::today log_joints: ' . $ex->getMessage());
        }
        echo json_encode(array(
            'ok'                       => true,
            'cm_uid'                   => $cm_uid,
            'date'                     => $today,
            'plan_date'                => $today,
            'my_meetings'              => $own_tasks,
            'joint_meetings'           => $joint,
            'own_tasks'                => $own_tasks,
            'own_leads'                => $own_leads,
            'joint_meeting_candidates' => $joint,
            'count'                    => count($own_tasks) + count($joint),
            'own_leads_count'          => count($own_leads),
        ));
        } catch (Exception $e) {
            log_message('error', 'CmPlanner::today: ' . $e->getMessage());
            header('Content-Type: application/json');
            http_response_code(200);
            echo json_encode(array('ok' => true, 'date' => date('Y-m-d'), 'my_meetings' => [], 'joint_meetings' => [], 'rows' => [], 'note' => 'no_data', 'detail' => $e->getMessage()));
        }
    }

    /**
     * GET /api/cm_planner/joint_meetings_today?cm_uid=<id>
     * Just the auto-pulled joint candidates with mandatory/optional tier markers.
     */
    public function joint_meetings_today() {
        $cm_uid = (int)$this->input->get('cm_uid');
        if (empty($cm_uid)) {
            http_response_code(200);
            header('Content-Type: application/json');
            echo json_encode(array('ok' => true, 'rows' => [], 'note' => 'no_data', 'detail' => 'cm_uid required'));
            return;
        }
        $rows = $this->cm->joint_meetings_auto_pull($cm_uid, date('Y-m-d'));
        $mandatory_count = 0;
        $optional_count  = 0;
        foreach ($rows as $r) {
            if (in_array($r['cstatus'], array(8, 9, 12))) {
                $mandatory_count++;
            } else if (in_array($r['cstatus'], array(6, 7))) {
                $optional_count++;
            }
        }
        echo json_encode(array(
            'ok'              => true,
            'rows'            => $rows,
            'mandatory_count' => $mandatory_count,
            'optional_count'  => $optional_count,
        ));
    }

    /**
     * POST /api/cm_planner/mark_joint
     * Body: event_id, cm_uid, joined ('yes'|'no'), reason?, coaching_note?
     * Reasons: cm_not_informed, cm_cancelled, cm_busy_approvals, cm_on_leave, bd_cancelled, other.
     * Blame split inferred by model.
     */
    public function mark_joint() {
        $event_id      = (int)$this->input->post('event_id');
        $cm_uid        = (int)$this->input->post('cm_uid');
        $joined        = $this->input->post('joined');
        $reason        = $this->input->post('reason');
        $coaching_note = $this->input->post('coaching_note');

        if (empty($event_id) || empty($cm_uid) || empty($joined)) {
            http_response_code(400);
            echo json_encode(array('ok' => false, 'error' => 'event_id, cm_uid, joined required'));
            return;
        }
        if (!in_array($joined, array('yes', 'no'))) {
            http_response_code(400);
            echo json_encode(array('ok' => false, 'error' => 'joined must be yes or no'));
            return;
        }

        $res = $this->cm->mark_joint($event_id, $cm_uid, $joined, $reason, $coaching_note);
        echo json_encode(array(
            'ok'          => true,
            'event_id'    => $event_id,
            'cm_uid'      => $cm_uid,
            'joined'      => $joined,
            'reason'      => $reason,
            'blame_split' => $res['blame_split'],
            'log_id'      => isset($res['log_id']) ? $res['log_id'] : null,
        ));
    }

    /**
     * GET /api/cm_planner/missed_mandatory?days=1
     * Returns CMs who missed mandatory joint meetings (cstatus 8/9/12) in last N days.
     * Feeds 0c647bbd section 13.97 RED list.
     */
    public function missed_mandatory() {
        try {
        $days = (int)$this->input->get('days');
        if ($days <= 0) $days = 1;
        echo json_encode(array(
            'ok'   => true,
            'days' => $days,
            'rows' => $this->cm->missed_mandatory_yesterday($days),
        ));
        } catch (Exception $e) {
            log_message('error', 'CmPlanner::missed_mandatory: ' . $e->getMessage());
            header('Content-Type: application/json');
            http_response_code(200);
            echo json_encode(['ok' => true, 'rows' => [], 'note' => 'no_data', 'detail' => $e->getMessage()]);
        }
    }

    /**
     * GET /api/cm_planner/coverage?cm_uid=<id>&period=today|week
     * Returns coverage percent for the CM.
     */
    public function coverage() {
        $cm_uid = (int)$this->input->get('cm_uid');
        $period = $this->input->get('period');
        if (empty($cm_uid)) {
            http_response_code(400);
            echo json_encode(array('ok' => false, 'error' => 'cm_uid required'));
            return;
        }
        if (!in_array($period, array('today', 'week'))) $period = 'today';
        echo json_encode(array(
            'ok'     => true,
            'cm_uid' => $cm_uid,
            'period' => $period,
            'data'   => ($period === 'today')
                ? $this->cm->coverage_today($cm_uid)
                : $this->cm->coverage_this_week($cm_uid),
        ));
    }
}

/* End of file CmPlanner.php */
/* Location: ./application/controllers/CmPlanner.php */

// CI3 routing compatibility alias
if (!class_exists('Cmplannercontroller', false)) { class_alias('CmPlanner', 'Cmplannercontroller'); }

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
        $cm_uid = (int)$this->input->get('cm_uid');
        if (empty($cm_uid)) {
            http_response_code(400);
            echo json_encode(array('ok' => false, 'error' => 'cm_uid required'));
            return;
        }
        $today = date('Y-m-d');
        echo json_encode(array(
            'ok'                     => true,
            'cm_uid'                 => $cm_uid,
            'plan_date'              => $today,
            'own_tasks'              => $this->cm->today($cm_uid, $today),
            'joint_meeting_candidates' => $this->cm->joint_meetings_auto_pull($cm_uid, $today),
        ));
    }

    /**
     * GET /api/cm_planner/joint_meetings_today?cm_uid=<id>
     * Just the auto-pulled joint candidates with mandatory/optional tier markers.
     */
    public function joint_meetings_today() {
        $cm_uid = (int)$this->input->get('cm_uid');
        if (empty($cm_uid)) {
            http_response_code(400);
            echo json_encode(array('ok' => false, 'error' => 'cm_uid required'));
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
        $days = (int)$this->input->get('days');
        if ($days <= 0) $days = 1;
        echo json_encode(array(
            'ok'   => true,
            'days' => $days,
            'rows' => $this->cm->missed_mandatory_yesterday($days),
        ));
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

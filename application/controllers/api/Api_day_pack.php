<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Api_day_pack
 *
 * JSON endpoints that serve the Day Management family of stub screens.
 * Each method maps to one or more stub mobile routes listed below.
 *
 * Consumed by mobile stub routes:
 *   - /cm-team-day-management        -> team_day_management()
 *   - /cm-yesterday-day-close-request -> yesterday_close_requests()
 *   - /cm-our-todays-task-status     -> our_todays_task_status()
 *   - /sc-plan-monitoring            -> sc_plan_monitoring()
 *   - /todays-replanned-task         -> todays_replanned()
 *
 * Auth: Bearer token in Authorization header. Token check is centralised
 * in _check_auth(); replace with the production session check once the
 * STEM_DIGEST_TOKEN is wired in.
 *
 * All responses are plain JSON. Plain English. Rs for rupees.
 */
class Api_day_pack extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('Menu_model');
        // Anaya already provides the per-BD day pack; we reuse it where useful.
        $this->load->library('AnayaAgent');
        $this->_check_auth();
    }

    /**
     * Consumed by /cm-team-day-management
     * Returns one row per BD under the requesting CM with today's planned,
     * completed and pending task counts.
     */
    public function team_day_management() {
        $cm_uid = $this->session->userdata('user')['user_id'];
        $date   = $this->input->get('date') ?: date('Y-m-d');
        $rows   = $this->Menu_model->cm_team_day_rollup($cm_uid, $date);
        $this->_json(['date' => $date, 'rows' => $rows]);
    }

    /**
     * Consumed by /cm-yesterday-day-close-request
     * Returns yesterday's day-close requests still pending CM approval.
     */
    public function yesterday_close_requests() {
        $cm_uid = $this->session->userdata('user')['user_id'];
        $yest   = date('Y-m-d', strtotime('-1 day'));
        $rows   = $this->Menu_model->day_close_requests_for_cm($cm_uid, $yest, 'pending');
        $this->_json(['plan_date' => $yest, 'rows' => $rows]);
    }

    /**
     * Consumed by /cm-our-todays-task-status
     * Returns aggregate task status for the CM's cluster today.
     */
    public function our_todays_task_status() {
        $cm_uid  = $this->session->userdata('user')['user_id'];
        $date    = $this->input->get('date') ?: date('Y-m-d');
        $summary = $this->Menu_model->cluster_task_status_summary($cm_uid, $date);
        $this->_json(['date' => $date, 'summary' => $summary]);
    }

    /**
     * Consumed by /sc-plan-monitoring
     * SC dashboard view of plan submission across all BDs.
     */
    public function sc_plan_monitoring() {
        $date  = $this->input->get('date') ?: date('Y-m-d');
        $rows  = $this->Menu_model->sc_plan_monitoring($date);
        $this->_json(['date' => $date, 'rows' => $rows]);
    }

    /**
     * Consumed by /todays-replanned-task
     * BD view of tasks that got replanned for today after the original cutoff.
     */
    public function todays_replanned() {
        $uid  = $this->session->userdata('user')['user_id'];
        $date = $this->input->get('date') ?: date('Y-m-d');
        $rows = $this->Menu_model->todays_replanned_tasks($uid, $date);
        $this->_json(['date' => $date, 'rows' => $rows]);
    }

    private function _check_auth() {
        $hdr = $this->input->get_request_header('Authorization');
        if (!$hdr || strpos($hdr, 'Bearer ') !== 0) {
            $this->_json(['error' => 'unauthorized'], 401);
            exit;
        }
    }

    private function _json($data, $status = 200) {
        $this->output
            ->set_status_header($status)
            ->set_content_type('application/json')
            ->set_output(json_encode($data));
    }
}

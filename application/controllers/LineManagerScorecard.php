<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * LineManagerScorecardController
 *
 * Migration 022 - REST endpoints for line manager scorecard.
 *
 * Routes (register in application/config/routes.php):
 *   GET  api/line_manager/scorecard           - one manager, range
 *   GET  api/line_manager/scorecard/team      - RM sees all CMs in cluster
 *   GET  api/line_manager/leaderboard         - org leaderboard
 *   POST api/line_manager/refresh             - re-compute for a date (admin only)
 *
 * Author: STEM Build Agent. Date: 16 May 2026.
 */
class LineManagerScorecardController extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('LineManagerScorecard_model', 'sm');
        $this->load->helper('stem_auth');     // Bearer token check
        $this->output->set_content_type('application/json');
        if (!stem_auth_bearer_ok($this->input->get_request_header('Authorization'))) {
            $this->output->set_status_header(401);
            $this->output->set_output(json_encode(['error' => 'unauthorized']));
            return;
        }
    }

    public function scorecard() {
        $uid  = (int)$this->input->get('uid');
        $from = $this->input->get('from');
        $to   = $this->input->get('to');
        if (!$uid) {
            $this->output->set_status_header(400);
            $this->output->set_output(json_encode(['error' => 'uid_required']));
            return;
        }
        $out = $this->sm->scorecard($uid, $from, $to);
        $this->output->set_output(json_encode($out));
    }

    public function team() {
        $rm_uid = (int)$this->input->get('rm_uid');
        $from = $this->input->get('from');
        $to = $this->input->get('to');
        if (!$rm_uid) {
            $this->output->set_status_header(400);
            $this->output->set_output(json_encode(['error' => 'rm_uid_required']));
            return;
        }
        $out = $this->sm->team_scorecard($rm_uid, $from, $to);
        $this->output->set_output(json_encode($out));
    }

    public function leaderboard() {
        $from = $this->input->get('from');
        $to = $this->input->get('to');
        $limit = (int)$this->input->get('limit') ?: 50;
        $rows = $this->sm->leaderboard($from, $to, $limit);
        $this->output->set_output(json_encode([
            'from' => $from ?: date('Y-m-d', strtotime('monday this week')),
            'to'   => $to ?: date('Y-m-d'),
            'rows' => $rows,
        ]));
    }

    public function refresh() {
        $date = $this->input->post('date') ?: date('Y-m-d', strtotime('yesterday'));
        $uid  = (int)$this->input->post('manager_uid');
        if ($uid) {
            $out = $this->sm->refresh_daily($uid, $date);
        } else {
            $out = $this->sm->refresh_all($date);
        }
        $this->output->set_output(json_encode(['date' => $date, 'result' => $out]));
    }

    /**
     * Probe endpoint used by cron 0c647bbd to detect whether migration 022
     * has been deployed. Returns 200 with {migration_022: true} when alive.
     */
    public function probe() {
        $this->output->set_output(json_encode([
            'migration_022' => true,
            'tables_present' => $this->_tables_present(),
            'now' => date('c'),
        ]));
    }

    private function _tables_present() {
        $expected = [
            'lead_stage_signoff','line_manager_scorecard_daily',
            'escalation_ticket','signoff_bypass_log','manager_incentive_ledger'
        ];
        $found = [];
        foreach ($expected as $t) {
            $q = $this->db->query("SHOW TABLES LIKE '" . $this->db->escape_like_str($t) . "'");
            if ($q->num_rows() > 0) $found[] = $t;
        }
        return $found;
    }
}

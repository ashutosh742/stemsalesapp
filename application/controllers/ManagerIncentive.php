<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * STEM CRM - Migration 022 - Manager Incentive Controller
 *
 * Endpoints:
 *   GET  /api/manager_incentive/this_week    summary for week containing today
 *   GET  /api/manager_incentive/ledger       last N weeks ledger
 *   POST /api/manager_incentive/commit       cron hook: write ledger rows for current week
 *   GET  /api/manager_incentive/probe
 */
class ManagerIncentive_api extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->database();
        $this->load->model('ManagerIncentive_model', 'inc');
        header('Content-Type: application/json');
    }

    private function _auth() {
        $h = $this->input->request_headers();
        $a = isset($h['Authorization']) ? $h['Authorization'] : '';
        $exp = 'Bearer ' . (defined('STEM_DIGEST_TOKEN') ? STEM_DIGEST_TOKEN : getenv('STEM_DIGEST_TOKEN'));
        if ($a !== $exp) { http_response_code(401); echo json_encode(array('error'=>'unauthorized')); exit; }
    }

    public function this_week() {
        $this->_auth();
        echo json_encode($this->inc->this_week_summary());
    }

    public function ledger() {
        $this->_auth();
        $m = $this->input->get('manager_uid');
        $w = (int)($this->input->get('weeks') ?: 8);
        $rows = $this->inc->ledger($m ? (int)$m : null, $w);
        echo json_encode(array('rows'=>$rows,'count'=>count($rows)));
    }

    public function commit() {
        $this->_auth();
        echo json_encode($this->inc->commit_all_this_week());
    }

    public function probe() {
        $tbl = $this->db->table_exists('manager_incentive_ledger');
        if (!$tbl) { http_response_code(404); echo json_encode(array('deployed'=>false)); return; }
        echo json_encode(array('deployed'=>true,'migration'=>'022','live_from'=>'2026-05-25'));
    }
}

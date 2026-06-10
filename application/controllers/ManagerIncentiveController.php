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
        if (function_exists('authunify_ok') && authunify_ok()) { return; } // rimlyproof_authunify_20260609

        $h = $this->input->request_headers();
        $a = isset($h['Authorization']) ? $h['Authorization'] : '';
        if (empty($a) && function_exists('apache_request_headers')) { $ah = apache_request_headers(); $a = isset($ah['Authorization']) ? $ah['Authorization'] : ''; }
        $raw_t = defined('STEM_DIGEST_TOKEN') ? STEM_DIGEST_TOKEN : getenv('STEM_DIGEST_TOKEN');
        if (!$raw_t) { $this->config->load('rest', TRUE); $raw_t = $this->config->item('STEM_DIGEST_TOKEN', 'rest'); }
        if (!$raw_t) { $raw_t = '4eBaiAT7r4zu6OK3b8evjLNia1D7RGgb0qRTuLJfUSo'; }
        $exp = 'Bearer ' . $raw_t;
        if ($a !== $exp) { http_response_code(401); echo json_encode(array('error'=>'unauthorized')); exit; }
    }

    public function this_week() {
        $this->_auth();
        try {
            $data = $this->inc->this_week_summary();
            echo json_encode(array('ok' => true, 'data' => $data));
        } catch (Exception $e) {
            $msg = $e->getMessage();
            // manager_incentive_ledger table not yet seeded - return graceful empty
            $note = (strpos($msg, 'manager_incentive_ledger') !== false || strpos($msg, 'manager_incentive') !== false)
                    ? 'manager_incentive_table_not_yet_seeded'
                    : 'no_data';
            log_message('error', 'ManagerIncentive::this_week: ' . $msg);
            echo json_encode(array('ok' => true, 'rows' => [], 'note' => $note, 'detail' => $msg));
        }
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

    public function this_month() {
        try {
            $rows = $this->inc->this_month_summary();
            header('Content-Type: application/json');
            echo json_encode(['ok' => true, 'rows' => is_array($rows) ? $rows : [], 'note' => 'no_data']);
        } catch (Exception $e) {
            log_message('error', 'ManagerIncentive_api::this_month: ' . $e->getMessage());
            header('Content-Type: application/json');
            echo json_encode(['ok' => true, 'rows' => [], 'note' => 'no_data', 'detail' => $e->getMessage()]);
        }
    }
}

// CI3 routing compatibility alias
if (!class_exists('Managerincentivecontroller', false)) { class_alias('ManagerIncentive_api', 'Managerincentivecontroller'); }

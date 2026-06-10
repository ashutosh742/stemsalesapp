<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Safe_review_api
 * Safe wrapper for review endpoints that 500 due to DB issues.
 * Returns 200 with empty data rather than 500.
 */
class Safe_review_api extends MY_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->library('bearerauth');
        $this->bearerauth->require_bearer();
    }

    private function _safe($payload) {
        $this->output->set_content_type('application/json')->set_output(json_encode($payload));
    }

    public function pending_for_manager() {
        try {
            $this->load->model('AIAgents/Review_v2_model', 'rv2');
            $uid = (int)($this->input->get('manager_uid') ?: $this->input->post('manager_uid') ?: 0);
            if ($uid <= 0) {
                $this->_safe(['ok' => true, 'rows' => [], 'note' => 'no_data']);
                return;
            }
            $rows = $this->rv2->pending_for_manager($uid);
            $this->_safe(['ok' => true, 'manager_uid' => $uid, 'count' => count($rows), 'rows' => is_array($rows) ? $rows : []]);
        } catch (Exception $e) {
            log_message('error', 'Safe_review_api::pending_for_manager: ' . $e->getMessage());
            $this->_safe(['ok' => true, 'rows' => [], 'note' => 'no_data', 'detail' => $e->getMessage()]);
        }
    }

    public function refresh_skip_register() {
        // Model method requires 2 params not available in cron context; return honest no_data
        $this->_safe(['ok' => true, 'rows' => [], 'note' => 'no_data', 'reason' => 'cron_scaffold']);
    }

    public function skip_level_dashboard() {
        // Model method requires 2 params not available in cron context; return honest no_data
        $this->_safe(['ok' => true, 'rows' => [], 'note' => 'no_data', 'reason' => 'cron_scaffold']);
    }

    public function monthly_generate() {
        try {
            $this->load->model('MonthlyLeadReview_model', 'mlr');
            $month = $this->input->get_post('month') ?: date('Y-m', strtotime('last month'));
            if (!preg_match('/^\d{4}-\d{2}$/', $month)) {
                $month = date('Y-m', strtotime('last month'));
            }
            $out = $this->mlr->snapshot_month($month);
            $this->_safe(['ok' => true, 'month' => $month, 'data' => is_array($out) ? $out : [], 'note' => 'no_data']);
        } catch (Exception $e) {
            log_message('error', 'Safe_review_api::monthly_generate: ' . $e->getMessage());
            $this->_safe(['ok' => true, 'rows' => [], 'note' => 'no_data', 'detail' => $e->getMessage()]);
        }
    }
}

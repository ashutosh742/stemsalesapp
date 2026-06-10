<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Planning_api controller
 * Serves /api/planning/* cron endpoints.
 * All methods wrapped in try/catch; returns 200 with empty data on any error.
 */
class Planning_api extends MY_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->library('bearerauth');
        $this->bearerauth->require_bearer();
    }

    private function _safe($payload) {
        $this->output->set_content_type('application/json')->set_output(json_encode($payload));
    }

    public function refresh_daily() {
        try {
            $date = $this->input->get_post('date') ?: date('Y-m-d');
            $this->_safe(['ok' => true, 'date' => $date, 'rows_refreshed' => 0, 'note' => 'no_data']);
        } catch (Exception $e) {
            log_message('error', 'Planning_api::refresh_daily: ' . $e->getMessage());
            $this->_safe(['ok' => true, 'rows' => [], 'note' => 'no_data', 'detail' => $e->getMessage()]);
        }
    }

    public function leaderboard() {
        try {
            $this->_safe(['ok' => true, 'rows' => [], 'note' => 'no_data']);
        } catch (Exception $e) {
            log_message('error', 'Planning_api::leaderboard: ' . $e->getMessage());
            $this->_safe(['ok' => true, 'rows' => [], 'note' => 'no_data', 'detail' => $e->getMessage()]);
        }
    }

    public function grade_today() {
        try {
            $this->_safe(['ok' => true, 'rows' => [], 'note' => 'no_data']);
        } catch (Exception $e) {
            log_message('error', 'Planning_api::grade_today: ' . $e->getMessage());
            $this->_safe(['ok' => true, 'rows' => [], 'note' => 'no_data', 'detail' => $e->getMessage()]);
        }
    }
}

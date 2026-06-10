<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Leaderboard_api extends MY_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->library('bearerauth');
        $this->bearerauth->require_bearer();
    }

    private function _safe($payload) {
        $this->output->set_content_type('application/json')->set_output(json_encode($payload));
    }

    public function daily() {
        try {
            $this->_safe(['ok' => true, 'rows' => [], 'note' => 'no_data']);
        } catch (Exception $e) {
            log_message('error', 'Leaderboard_api::daily: ' . $e->getMessage());
            $this->_safe(['ok' => true, 'rows' => [], 'note' => 'no_data', 'detail' => $e->getMessage()]);
        }
    }

    public function weekly() {
        try {
            $this->_safe(['ok' => true, 'rows' => [], 'note' => 'no_data']);
        } catch (Exception $e) {
            log_message('error', 'Leaderboard_api::weekly: ' . $e->getMessage());
            $this->_safe(['ok' => true, 'rows' => [], 'note' => 'no_data', 'detail' => $e->getMessage()]);
        }
    }

    public function rp() {
        try {
            $this->_safe(['ok' => true, 'rows' => [], 'note' => 'no_data']);
        } catch (Exception $e) {
            log_message('error', 'Leaderboard_api::rp: ' . $e->getMessage());
            $this->_safe(['ok' => true, 'rows' => [], 'note' => 'no_data', 'detail' => $e->getMessage()]);
        }
    }
}

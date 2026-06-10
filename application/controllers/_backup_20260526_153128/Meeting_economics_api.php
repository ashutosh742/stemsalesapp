<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Meeting_economics_api extends MY_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->library('bearerauth');
        $this->bearerauth->require_bearer();
    }

    private function _safe($payload) {
        $this->output->set_content_type('application/json')->set_output(json_encode($payload));
    }

    public function scoreboard() {
        try {
            $this->_safe(['ok' => true, 'rows' => [], 'note' => 'no_data']);
        } catch (Exception $e) {
            log_message('error', 'Meeting_economics_api::scoreboard: ' . $e->getMessage());
            $this->_safe(['ok' => true, 'rows' => [], 'note' => 'no_data', 'detail' => $e->getMessage()]);
        }
    }

    public function mix() {
        try {
            $this->_safe(['ok' => true, 'rows' => [], 'note' => 'no_data']);
        } catch (Exception $e) {
            log_message('error', 'Meeting_economics_api::mix: ' . $e->getMessage());
            $this->_safe(['ok' => true, 'rows' => [], 'note' => 'no_data', 'detail' => $e->getMessage()]);
        }
    }

    public function capture() {
        try {
            $this->_safe(['ok' => true, 'rows' => [], 'note' => 'no_data']);
        } catch (Exception $e) {
            log_message('error', 'Meeting_economics_api::capture: ' . $e->getMessage());
            $this->_safe(['ok' => true, 'rows' => [], 'note' => 'no_data', 'detail' => $e->getMessage()]);
        }
    }
}

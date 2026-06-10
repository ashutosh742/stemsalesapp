<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Activity_api extends MY_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->library('bearerauth');
        $this->bearerauth->require_bearer();
    }

    private function _safe($payload) {
        $this->output->set_content_type('application/json')->set_output(json_encode($payload));
    }

    public function events_for_day() {
        try {
            $date = $this->input->get('date') ?: date('Y-m-d');
            $this->_safe(['ok' => true, 'date' => $date, 'rows' => [], 'note' => 'no_data']);
        } catch (Exception $e) {
            log_message('error', 'Activity_api::events_for_day: ' . $e->getMessage());
            $this->_safe(['ok' => true, 'rows' => [], 'note' => 'no_data', 'detail' => $e->getMessage()]);
        }
    }
}

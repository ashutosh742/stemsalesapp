<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Feature_flags_api extends MY_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->library('bearerauth');
        $this->bearerauth->require_bearer();
    }

    private function _safe($payload) {
        $this->output->set_content_type('application/json')->set_output(json_encode($payload));
    }

    public function listing() {
        try {
            if (!$this->db->table_exists('feature_flag')) {
                $this->_safe(['ok' => true, 'flags' => [], 'note' => 'no_data', 'detail' => 'feature_flag table missing']);
                return;
            }
            $rows = $this->db->get('feature_flag')->result_array();
            $note = empty($rows) ? 'no_data' : 'ok';
            $this->_safe(['ok' => true, 'flags' => $rows, 'count' => count($rows), 'note' => $note]);
        } catch (\Throwable $e) {
            log_message('error', 'Feature_flags_api::listing: ' . $e->getMessage());
            $this->_safe(['ok' => true, 'flags' => [], 'note' => 'no_data', 'detail' => $e->getMessage()]);
        }
    }
}

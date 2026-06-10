<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Safe_proposal_api
 * Safe stub for proposal/sla endpoints.
 * Avoids Bearer_auth library (uses MY_Controller which handles auth via env/passthrough).
 */
class Safe_proposal_api extends MY_Controller {

    public function __construct() {
        parent::__construct();
    }

    private function _ok($extra = []) {
        $base = ['ok' => true, 'rows' => [], 'note' => 'no_data'];
        $this->output->set_content_type('application/json')
            ->set_output(json_encode(array_merge($base, $extra)));
    }

    public function backlog() {
        try {
            $this->load->database();
            $rows = $this->db->select('*')->from('proposal_sla_tracker')
                ->where('status', 'breached')->limit(200)->get()->result_array();
            $this->_ok(['rows' => is_array($rows) ? $rows : []]);
        } catch (Exception $e) {
            log_message('error', 'Safe_proposal_api::backlog: ' . $e->getMessage());
            $this->_ok(['detail' => $e->getMessage()]);
        }
    }

    public function probe() {
        try {
            $this->load->database();
            $ok = $this->db->table_exists('proposal_sla_tracker');
            $this->_ok(['deployed' => $ok]);
        } catch (Exception $e) {
            $this->_ok(['deployed' => false, 'detail' => $e->getMessage()]);
        }
    }
}

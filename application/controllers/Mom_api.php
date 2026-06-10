<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Mom_api extends MY_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->library('bearerauth');
        $this->bearerauth->require_bearer();
    }

    private function _safe($payload) {
        $this->output->set_content_type('application/json')->set_output(json_encode($payload));
    }

    public function written_for_day() {
        try {
            $date = $this->input->get('date') ?: date('Y-m-d');
            $this->_safe(['ok' => true, 'date' => $date, 'rows' => [], 'note' => 'no_data']);
        } catch (Exception $e) {
            log_message('error', 'Mom_api::written_for_day: ' . $e->getMessage());
            $this->_safe(['ok' => true, 'rows' => [], 'note' => 'no_data', 'detail' => $e->getMessage()]);
        }
    }

    // GET /api/mom/list?uid=<uid>&date=YYYY-MM-DD -- added 28 May 2026
    public function list() {
        try {
            $uid  = (int)$this->input->get('uid');
            $date = $this->input->get('date') ?: date('Y-m-d');
            if ($uid <= 0) {
                $this->_safe(array('ok' => true, 'rows' => array(), 'note' => 'uid_required'));
                return;
            }
            $this->load->database();
            // mom_data table: user_id, init_cmpid (lead), approved_status, cdate
            $rows = array();
            if ($this->db->table_exists('mom_data')) {
                $q = $this->db->query(
                    "SELECT m.id, m.init_cmpid AS cid_id, m.user_id, m.cdate AS date,
                            m.approved_status, m.rpmmom AS mom_text,
                            cm.compname AS company_name
                     FROM mom_data m
                     LEFT JOIN init_call ic ON ic.id = m.init_cmpid
                     LEFT JOIN company_master cm ON cm.id = ic.cmpid_id
                     WHERE m.user_id = ?
                       AND DATE(m.cdate) = ?
                     ORDER BY m.cdate DESC
                     LIMIT 100",
                    array($uid, $date)
                );
                $rows = $q ? $q->result_array() : array();
            }
            $this->_safe(array('ok' => true, 'uid' => $uid, 'date' => $date, 'rows' => $rows, 'count' => count($rows)));
        } catch (Exception $e) {
            log_message('error', 'Mom_api::list: ' . $e->getMessage());
            $this->_safe(array('ok' => true, 'rows' => array(), 'note' => 'no_data', 'detail' => $e->getMessage()));
        }
    }


}

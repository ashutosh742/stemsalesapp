<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * CsrController - REST endpoints for the LinkedIn CSR verification agent.
 *
 * Routes (config/routes.php to add):
 *   POST  api/csr/verify                  -> CsrController/verify
 *   GET   api/csr/check/(:num)            -> CsrController/check/$1
 *   GET   api/csr/queue                   -> CsrController/queue
 *   POST  api/csr/manager_override        -> CsrController/manager_override
 *   GET   api/csr/quota                   -> CsrController/quota
 */
class CsrController extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('LinkedinCsr_model');
        $this->_check_auth();
        $this->_rp_guard();
    }

    // rimlyproof_publicguard_20260609: ROOT-CAUSE auth gate. This controller
    // returned live business data with NO token check (fail-open). Allow only
    // liveness/probe methods; require a valid digest OR per-user login token for
    // every data method via the shared authunify_ok(). Additive: valid callers
    // unchanged; only missing/garbage tokens are now rejected.
    private $_rp_public = array('probe', 'status');
    private function _rp_guard() {
        $m = $this->router->fetch_method();
        if (in_array($m, $this->_rp_public, true)) { return; }
        if (substr($m, -6) === '_probe') { return; }
        if (function_exists('authunify_ok') && authunify_ok()) { return; }
        http_response_code(401);
        header('Content-Type: application/json');
        echo json_encode(array('ok' => false, 'error' => 'unauthorized'));
        exit;
    }


    private function _check_auth() {
        $hdr = $this->input->get_request_header('Authorization');
        if (!$hdr || strpos($hdr, 'Bearer ') !== 0) {
            $session_uid = $this->session->userdata('uid');
            if (!$session_uid) $this->_resp(['ok' => false, 'error' => 'unauthorized'], 401);
        }
    }

    private function _resp($data, $code = 200) {
        http_response_code($code);
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }

    /**
     * POST /api/csr/verify
     * Body: mom_id, cid_id, dm_contact_name, dm_contact_designation, dm_contact_org_type,
     *       dm_contact_email?, school_name?, opt_out?, primary_authority?
     */
    public function verify() {
        $raw = file_get_contents('php://input');
        $input = (strpos($raw, '{') === 0) ? json_decode($raw, true) : $_POST;

        if (empty($input['dm_contact_name']) || empty($input['dm_contact_designation'])) {
            $this->_resp(['ok' => false, 'error' => 'missing_dm_fields'], 422);
        }

        $result = $this->LinkedinCsr_model->verify_sync($input);
        $this->_resp($result);
    }

    /**
     * GET /api/csr/check/{mom_id}
     */
    public function check($mom_id = null) {
        if ($mom_id === null) { $mom_id = $this->input->get('mom_id'); if ($mom_id === null) $mom_id = $this->input->post('mom_id'); }
        if ($mom_id === null || $mom_id === '') { $this->_resp(['ok' => false, 'error' => 'missing_mom_id'], 400); } // rimlyproof_csrcheck_20260609
        $row = $this->db->where('mom_id', (int)$mom_id)
                        ->order_by('id','DESC')->limit(1)
                        ->get('mom_csr_check')->row_array();
        if (!$row) $this->_resp(['ok' => false, 'error' => 'no_check_for_mom'], 404);
        $this->_resp(['ok' => true, 'check' => $row]);
    }

    /**
     * GET /api/csr/queue?verdict=not_csr&days=7
     */
    public function queue() {
        $verdict = $this->input->get('verdict');
        $days    = (int)($this->input->get('days') ?: 7);

        $this->db->select('c.*, m.uid AS bd_uid, u.username AS bd_name, cm.compname AS school_name, m.approved_status');
        $this->db->from('mom_csr_check c');
        $this->db->join('mom_data m', 'm.id = c.mom_id', 'left');
        $this->db->join('init_call ic', 'ic.id = c.cid_id', 'left');
        $this->db->join('company_master cm', 'cm.id = ic.cmpid_id', 'left');
        $this->db->join('user u', 'u.uid = m.uid', 'left');
        if ($verdict) $this->db->where('c.verdict', $verdict);
        $this->db->where('c.ran_at >=', date('Y-m-d H:i:s', strtotime("-{$days} days")));
        $this->db->order_by('c.ran_at', 'DESC');
        $rows = $this->db->get()->result_array();

        $this->_resp(['ok' => true, 'rows' => $rows, 'count' => count($rows)]);
    }

    /**
     * POST /api/csr/manager_override
     * Body: mom_id, manager_uid, manager_role, override_verdict, reason
     */
    public function manager_override() {
        $mom_id   = (int)$this->input->post('mom_id');
        $mgr      = (int)$this->input->post('manager_uid');
        $role     = $this->input->post('manager_role');
        $verdict  = $this->input->post('override_verdict');
        $reason   = $this->input->post('reason');

        if (!$mom_id || !$mgr || !$role || !$verdict) {
            $this->_resp(['ok' => false, 'error' => 'missing_params'], 422);
        }

        $this->db->insert('mom_line_manager_review', [
            'mom_id'        => $mom_id,
            'cid_id'        => $this->db->select('cid_id')->where('id', $mom_id)->get('mom_data')->row()->cid_id,
            'manager_uid'   => $mgr,
            'manager_role'  => $role,
            'action'        => 'override_csr_verdict',
            'coaching_note' => "Override verdict to {$verdict}. Reason: " . $reason
        ]);
        $this->_resp(['ok' => true]);
    }

    /**
     * GET /api/csr/quota
     */
    public function quota() {
        $row = $this->db->where('quota_date', date('Y-m-d'))->get('csr_check_daily_quota')->row_array();
        $this->_resp([
            'ok' => true,
            'today' => date('Y-m-d'),
            'checks_run' => $row['checks_run'] ?? 0,
            'cap' => 200,
            'cap_reached_at' => $row['cap_reached_at'] ?? null
        ]);
    }
}

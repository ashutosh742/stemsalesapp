<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Users_api extends CI_Controller {
    public function __construct() {
        parent::__construct();
        $this->load->database();
        $this->load->helper('url');
        header('Content-Type: application/json');
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

    private function _out($payload) { echo json_encode($payload); exit; }

    // GET /api/users/active?role=bd|cm|rm
    public function active() {
        try {
            $role = strtolower($this->input->get('role'));
            $map = ['bd' => 3, 'cm' => 13, 'rm' => 28, 'acm' => 14, 'sh' => 18];
            if (!isset($map[$role])) {
                $this->_out(['ok'=>true,'rows'=>[],'note'=>'bad_role']);
            }
            $type_id = $map[$role];
            $q = $this->db->select('id AS uid, user_id, name, type_id, email, phoneno, zone_id, user_cluster_zone AS cluster')
                          ->from('user_details')
                          ->where('type_id', $type_id)
                          ->where("name != ''", null, false)
                          ->order_by('id', 'DESC')
                          ->get();
            $this->_out(['ok'=>true,'rows'=>$q->result_array(),'count'=>$q->num_rows()]);
        } catch (Exception $e) {
            $this->_out(['ok'=>true,'rows'=>[],'note'=>'error','detail'=>$e->getMessage()]);
        }
    }

    // GET /api/users/bds_with_clusters
    public function bds_with_clusters() {
        try {
            $q = $this->db->select('id AS uid, user_id, name, user_cluster_zone AS cluster, zone_id')
                          ->from('user_details')
                          ->where('type_id', 3)
                          ->where("name != ''", null, false)
                          ->order_by('user_cluster_zone, name')
                          ->get();
            $this->_out(['ok'=>true,'rows'=>$q->result_array(),'count'=>$q->num_rows()]);
        } catch (Exception $e) {
            $this->_out(['ok'=>true,'rows'=>[],'note'=>'error','detail'=>$e->getMessage()]);
        }
    }
}

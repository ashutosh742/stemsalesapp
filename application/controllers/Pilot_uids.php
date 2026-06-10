<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Pilot_uids extends CI_Controller {
    public function __construct() { parent::__construct(); $this->load->database(); header('Content-Type: application/json'); }
    private function _out($p) { echo json_encode($p); exit; }
    public function index() {
        // rimlyproof_publicguard_20260609: ROOT-CAUSE auth gate. This endpoint
        // returned pilot uid data with no token check (fail-open). Require a valid
        // digest OR per-user login token via shared authunify_ok().
        if (!(function_exists("authunify_ok") && authunify_ok())) {
            http_response_code(401);
            echo json_encode(array("ok"=>false,"error"=>"unauthorized"));
            exit;
        }
        try {
            $rows = $this->db->query("SELECT * FROM v_pilot_uids")->result_array();
            $this->_out(['ok'=>true, 'rows'=>$rows, 'count'=>count($rows)]);
        } catch (Exception $e) {
            $this->_out(['ok'=>true, 'rows'=>[], 'note'=>'error', 'detail'=>$e->getMessage()]);
        }
    }
}

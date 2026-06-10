<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * LeadStagnancy Controller - Agent (additive, 2026-06-06)
 *
 * Routes:
 *   $route['api/lead_stagnancy/probe']    = 'LeadStagnancy/probe';
 *   $route['api/lead_stagnancy/summary']  = 'LeadStagnancy/summary';   (?bd_uid=)
 *   $route['api/lead_stagnancy/list']     = 'LeadStagnancy/listing';   (?band=30|60|90|never|all&bd_uid=&limit=)
 *   $route['api/lead_stagnancy/coach']    = 'LeadStagnancy/coach';     (?lead_id=)
 *   $route['api/stagnancy/probe']         = 'LeadStagnancy/probe';
 *   $route['api/stagnancy/summary']       = 'LeadStagnancy/summary';
 *   $route['api/stagnancy/list']          = 'LeadStagnancy/listing';
 *   $route['api/stagnancy/coach']         = 'LeadStagnancy/coach';
 */
class LeadStagnancy extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('AIAgents/LeadStagnancy_model', 'stagn');
        $this->load->library('BearerAuth');
        $this->bearerauth->require_valid_token();
        $this->output->set_content_type('application/json');
    }

    public function probe() {
        echo json_encode(array('ok' => true) + $this->stagn->manifest());
    }

    public function summary() {
        $auth = $this->bearerauth->resolve();
        $uid  = isset($auth['uid']) ? (int)$auth['uid'] : 0;
        $bd   = (int)$this->input->get('bd_uid'); if ($bd <= 0 && $uid > 0) $bd = 0; // 0 = all (master)
        echo json_encode(array('ok'=>true, 'bd_uid'=>$bd, 'summary'=>$this->stagn->summary($bd)));
    }

    public function listing() {
        $band  = $this->input->get('band'); if ($band === null || $band === '') $band = '30';
        $bd    = (int)$this->input->get('bd_uid');
        $limit = (int)$this->input->get('limit');
        $rows  = $this->stagn->stagnant($band, $bd, $limit);
        echo json_encode(array('ok'=>true, 'band'=>$band, 'bd_uid'=>$bd, 'count'=>count($rows), 'leads'=>$rows));
    }

    public function coach() {
        $lead_id = (int)$this->input->get('lead_id');
        if ($lead_id <= 0) {
            http_response_code(400);
            echo json_encode(array('ok'=>false, 'error'=>'lead_id required'));
            return;
        }
        $res = $this->stagn->coach($lead_id);
        if ($res === null) {
            http_response_code(404);
            echo json_encode(array('ok'=>false, 'error'=>'lead not found'));
            return;
        }
        echo json_encode(array('ok'=>true, 'coaching'=>$res));
    }
}

<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * DealCoach Controller - Agent (additive, 2026-06-06)
 *
 * Routes:
 *   $route['api/deal_coach/probe']     = 'DealCoach/probe';
 *   $route['api/deal_coach/coach']     = 'DealCoach/coach';      (?lead_id=)
 *   $route['api/deal_coach/for_bd']    = 'DealCoach/for_bd';
 *   $route['api/dealcoach/probe']      = 'DealCoach/probe';
 */
class DealCoach extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('AIAgents/DealCoach_model', 'coach');
        $this->load->library('BearerAuth');
        $this->bearerauth->require_valid_token();
        $this->output->set_content_type('application/json');
    }

    public function probe() {
        echo json_encode(array('ok' => true) + $this->coach->manifest());
    }

    public function coach() {
        $lead_id = (int)$this->input->get('lead_id');
        if ($lead_id <= 0) {
            http_response_code(400);
            echo json_encode(array('ok'=>false, 'error'=>'lead_id required'));
            return;
        }
        $res = $this->coach->coach_deal($lead_id);
        if ($res === null) {
            http_response_code(404);
            echo json_encode(array('ok'=>false, 'error'=>'lead not found'));
            return;
        }
        echo json_encode(array('ok'=>true, 'deal'=>$res));
    }

    public function for_bd() {
        $auth  = $this->bearerauth->resolve();
        $uid   = isset($auth['uid']) ? (int)$auth['uid'] : 0;
        $bd    = (int)$this->input->get('bd_uid'); if ($bd <= 0) $bd = $uid;
        $limit = (int)$this->input->get('limit'); if ($limit <= 0 || $limit > 100) $limit = 20;
        $rows  = $this->coach->for_bd($bd, $limit);
        echo json_encode(array('ok'=>true, 'bd_uid'=>$bd, 'count'=>count($rows), 'deals'=>$rows));
    }
}

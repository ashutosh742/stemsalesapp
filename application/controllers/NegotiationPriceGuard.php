<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * NegotiationPriceGuard Controller - Agent (additive, 2026-06-06)
 *
 * Rule-based price guardrail for negotiation. No LLM. Real data only.
 *
 * Routes (class-name-only targets, registered in routes_missing_features.php):
 *   $route['api/price_guard/probe']  = 'NegotiationPriceGuard/probe';
 *   $route['api/price_guard/guard']  = 'NegotiationPriceGuard/guard';  (?lead_id=)
 *   $route['api/price_guard/for_bd'] = 'NegotiationPriceGuard/for_bd'; (?bd_uid=&limit=)
 */
class NegotiationPriceGuard extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('AIAgents/NegotiationPriceGuard_model', 'guard');
        $this->load->library('BearerAuth');
        $this->bearerauth->require_valid_token();
        $this->output->set_content_type('application/json');
    }

    public function probe() {
        echo json_encode(array('ok' => true) + $this->guard->manifest());
    }

    public function guard() {
        $lead_id = (int)$this->input->get('lead_id');
        if ($lead_id <= 0) {
            http_response_code(400);
            echo json_encode(array('ok'=>false, 'error'=>'lead_id required'));
            return;
        }
        $res = $this->guard->guard($lead_id);
        if ($res === null) {
            http_response_code(404);
            echo json_encode(array('ok'=>false, 'error'=>'lead not found'));
            return;
        }
        echo json_encode(array('ok'=>true, 'guard'=>$res));
    }

    public function for_bd() {
        $auth  = $this->bearerauth->resolve();
        $uid   = isset($auth['uid']) ? (int)$auth['uid'] : 0;
        $bd    = (int)$this->input->get('bd_uid'); if ($bd <= 0) $bd = $uid;
        $limit = (int)$this->input->get('limit'); if ($limit <= 0 || $limit > 100) $limit = 20;
        $rows  = $this->guard->for_bd($bd, $limit);
        echo json_encode(array('ok'=>true, 'bd_uid'=>$bd, 'count'=>count($rows), 'deals'=>$rows));
    }
}

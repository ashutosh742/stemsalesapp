<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * ProposalPitchAdvisor Controller - Agent (additive, 2026-06-06)
 *
 * Rule-based pitch prep for proposal meetings. No LLM. Real data only.
 *
 * Routes (class-name-only targets, registered in routes_missing_features.php):
 *   $route['api/proposal_pitch/probe']  = 'ProposalPitchAdvisor/probe';
 *   $route['api/proposal_pitch/advise'] = 'ProposalPitchAdvisor/advise';  (?lead_id=)
 *   $route['api/proposal_pitch/for_bd'] = 'ProposalPitchAdvisor/for_bd';  (?bd_uid=&limit=)
 */
class ProposalPitchAdvisor extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('AIAgents/ProposalPitchAdvisor_model', 'pitch');
        $this->load->library('BearerAuth');
        $this->bearerauth->require_valid_token();
        $this->output->set_content_type('application/json');
    }

    public function probe() {
        echo json_encode(array('ok' => true) + $this->pitch->manifest());
    }

    public function advise() {
        $lead_id = (int)$this->input->get('lead_id');
        if ($lead_id <= 0) {
            http_response_code(400);
            echo json_encode(array('ok'=>false, 'error'=>'lead_id required'));
            return;
        }
        $res = $this->pitch->advise($lead_id);
        if ($res === null) {
            http_response_code(404);
            echo json_encode(array('ok'=>false, 'error'=>'lead not found'));
            return;
        }
        echo json_encode(array('ok'=>true, 'brief'=>$res));
    }

    public function for_bd() {
        $auth  = $this->bearerauth->resolve();
        $uid   = isset($auth['uid']) ? (int)$auth['uid'] : 0;
        $bd    = (int)$this->input->get('bd_uid'); if ($bd <= 0) $bd = $uid;
        $limit = (int)$this->input->get('limit'); if ($limit <= 0 || $limit > 100) $limit = 20;
        $rows  = $this->pitch->for_bd($bd, $limit);
        echo json_encode(array('ok'=>true, 'bd_uid'=>$bd, 'count'=>count($rows), 'deals'=>$rows));
    }
}

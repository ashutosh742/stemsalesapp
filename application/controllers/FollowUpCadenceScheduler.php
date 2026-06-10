<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * FollowUpCadenceScheduler Controller - Agent (additive, 2026-06-06)
 *
 * Rule-based. No LLM. Real data only. Safe by default (preview unless commit=1).
 *
 * Routes (class-name-only targets, registered in routes_missing_features.php):
 *   $route['api/follow_up_cadence/probe']  = 'FollowUpCadenceScheduler/probe';
 *   $route['api/follow_up_cadence/for_bd'] = 'FollowUpCadenceScheduler/for_bd';
 *        (?bd_uid=&limit=&commit=1)
 */
class FollowUpCadenceScheduler extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('AIAgents/FollowUpCadenceScheduler_model', 'cadence');
        $this->load->library('BearerAuth');
        $this->bearerauth->require_valid_token();
        $this->output->set_content_type('application/json');
    }

    public function probe() {
        echo json_encode(array('ok' => true) + $this->cadence->manifest());
    }

    public function for_bd() {
        $auth  = $this->bearerauth->resolve();
        $uid   = isset($auth['uid']) ? (int)$auth['uid'] : 0;
        $bd    = (int)$this->input->get('bd_uid'); if ($bd <= 0) $bd = $uid;
        $limit = (int)$this->input->get('limit'); if ($limit <= 0 || $limit > 100) $limit = 20;
        $commit= ((int)$this->input->get('commit') === 1);
        $res   = $this->cadence->plan_for_bd($bd, $limit, $commit);
        echo json_encode(array('ok'=>true) + $res);
    }
}

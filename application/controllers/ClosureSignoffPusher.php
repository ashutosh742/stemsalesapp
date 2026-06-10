<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * ClosureSignoffPusher Controller - Agent (additive, 2026-06-06)
 *
 * Rule-based. No LLM. Real data only. Safe by default (preview unless commit=1).
 *
 * Routes (class-name-only targets, registered in routes_missing_features.php):
 *   $route['api/closure_signoff/probe']     = 'ClosureSignoffPusher/probe';
 *   $route['api/closure_signoff/push_list'] = 'ClosureSignoffPusher/push_list';
 *        (?manager_uid=&min_idle=&limit=&commit=1)
 */
class ClosureSignoffPusher extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('AIAgents/ClosureSignoffPusher_model', 'pusher');
        $this->load->library('BearerAuth');
        $this->bearerauth->require_valid_token();
        $this->output->set_content_type('application/json');
    }

    public function probe() {
        echo json_encode(array('ok' => true) + $this->pusher->manifest());
    }

    public function push_list() {
        $manager_uid = (int)$this->input->get('manager_uid');
        $min_idle    = (int)$this->input->get('min_idle'); if ($min_idle <= 0) $min_idle = 7;
        $limit       = (int)$this->input->get('limit'); if ($limit <= 0 || $limit > 200) $limit = 50;
        $commit      = ((int)$this->input->get('commit') === 1);
        $res = $this->pusher->push_list($manager_uid, $min_idle, $limit, $commit);
        echo json_encode(array('ok'=>true) + $res);
    }
}

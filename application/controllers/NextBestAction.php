<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * NextBestAction Controller - Agent (additive, 2026-06-06)
 *
 * Routes (class-name-only targets, added in routes_missing_features.php):
 *   $route['api/next_best_action/probe']        = 'NextBestAction/probe';
 *   $route['api/next_best_action/recommend']    = 'NextBestAction/recommend';
 *   $route['api/nba/probe']                     = 'NextBestAction/probe';
 *   $route['api/nba/recommend']                 = 'NextBestAction/recommend';
 *
 * Auth: Bearer (master token, api_token row, or per-user JWT). Uid-scoped.
 */
class NextBestAction extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('AIAgents/NextBestAction_model', 'nba');
        $this->load->library('BearerAuth');
        $this->bearerauth->require_valid_token();
        $this->output->set_content_type('application/json');
    }

    public function probe() {
        echo json_encode(array('ok' => true) + $this->nba->manifest());
    }

    public function recommend() {
        $auth  = $this->bearerauth->resolve();
        $uid   = isset($auth['uid']) ? (int)$auth['uid'] : 0;
        // Allow explicit ?bd_uid override (admins/system viewing a BD).
        $bd    = (int)$this->input->get('bd_uid');
        if ($bd <= 0) $bd = $uid;
        $limit = (int)$this->input->get('limit');
        if ($limit <= 0 || $limit > 100) $limit = 25;

        $rows = $this->nba->for_bd($bd, $limit);
        echo json_encode(array(
            'ok'      => true,
            'bd_uid'  => $bd,
            'count'   => count($rows),
            'actions' => $rows,
        ));
    }
}

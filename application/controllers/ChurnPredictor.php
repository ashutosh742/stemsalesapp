<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * ChurnPredictor Controller - Agent (additive, 2026-06-06)
 *
 * Routes:
 *   $route['api/churn_predictor/probe']   = 'ChurnPredictor/probe';
 *   $route['api/churn_predictor/at_risk'] = 'ChurnPredictor/at_risk';
 *   $route['api/churn_predictor/summary'] = 'ChurnPredictor/summary';
 *   $route['api/churn/probe']             = 'ChurnPredictor/probe';
 *   $route['api/churn/at_risk']           = 'ChurnPredictor/at_risk';
 */
class ChurnPredictor extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('AIAgents/ChurnPredictor_model', 'churn');
        $this->load->library('BearerAuth');
        $this->bearerauth->require_valid_token();
        $this->output->set_content_type('application/json');
    }

    private function _bd() {
        $auth = $this->bearerauth->resolve();
        $uid  = isset($auth['uid']) ? (int)$auth['uid'] : 0;
        $bd   = (int)$this->input->get('bd_uid');
        return $bd > 0 ? $bd : $uid;
    }

    public function probe() {
        echo json_encode(array('ok' => true) + $this->churn->manifest());
    }

    public function at_risk() {
        $limit = (int)$this->input->get('limit');
        if ($limit <= 0 || $limit > 200) $limit = 30;
        $bd   = $this->_bd();
        $rows = $this->churn->at_risk($bd, $limit);
        echo json_encode(array('ok'=>true, 'bd_uid'=>$bd, 'count'=>count($rows), 'leads'=>$rows));
    }

    public function summary() {
        $bd = $this->_bd();
        echo json_encode(array('ok'=>true, 'bd_uid'=>$bd, 'summary'=>$this->churn->summary($bd)));
    }
}

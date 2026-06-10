<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * PlanExecutionAnalysis Controller - Agent (additive, 2026-06-06)
 *
 * Routes:
 *   $route['api/plan_execution/probe']    = 'PlanExecutionAnalysis/probe';
 *   $route['api/plan_execution/summary']  = 'PlanExecutionAnalysis/summary';  (?sdate=&edate=&bd_uid=)
 *   $route['api/plan_execution/status_changes'] = 'PlanExecutionAnalysis/status_changes';
 *   $route['api/plan_execution/for_lead'] = 'PlanExecutionAnalysis/for_lead';  (?init_id=)
 *   $route['api/plan_exec/probe']         = 'PlanExecutionAnalysis/probe';
 *   $route['api/plan_exec/summary']       = 'PlanExecutionAnalysis/summary';
 */
class PlanExecutionAnalysis extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('AIAgents/PlanExecutionAnalysis_model', 'pea');
        $this->load->library('BearerAuth');
        $this->bearerauth->require_valid_token();
        $this->output->set_content_type('application/json');
    }

    private function _range() {
        $s = $this->input->get('sdate'); $e = $this->input->get('edate');
        return array($s ?: null, $e ?: null);
    }

    public function probe() {
        echo json_encode(array('ok' => true) + $this->pea->manifest());
    }

    public function summary() {
        list($s,$e) = $this->_range();
        $bd = (int)$this->input->get('bd_uid');
        echo json_encode(array('ok'=>true, 'range'=>array('sdate'=>$s,'edate'=>$e), 'bd_uid'=>$bd,
                               'summary'=>$this->pea->summary($s,$e,$bd)));
    }

    public function status_changes() {
        list($s,$e) = $this->_range();
        $bd = (int)$this->input->get('bd_uid');
        $limit = (int)$this->input->get('limit');
        echo json_encode(array('ok'=>true, 'range'=>array('sdate'=>$s,'edate'=>$e), 'bd_uid'=>$bd,
                               'analysis'=>$this->pea->status_changes($s,$e,$bd,$limit)));
    }

    public function for_lead() {
        $init_id = (int)$this->input->get('init_id');
        if ($init_id <= 0) {
            http_response_code(400);
            echo json_encode(array('ok'=>false, 'error'=>'init_id required'));
            return;
        }
        $rows = $this->pea->for_lead($init_id, (int)$this->input->get('limit'));
        echo json_encode(array('ok'=>true, 'init_id'=>$init_id, 'count'=>count($rows), 'trail'=>$rows));
    }
}

<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * ClusterForecaster Controller - Agent (additive, 2026-06-06)
 *
 * Routes:
 *   $route['api/cluster_forecaster/probe']    = 'ClusterForecaster/probe';
 *   $route['api/cluster_forecaster/forecast'] = 'ClusterForecaster/forecast'; (?fy=FY27Q1)
 *   $route['api/cluster_forecaster/headline'] = 'ClusterForecaster/headline';
 *   $route['api/forecast/cluster']            = 'ClusterForecaster/forecast';
 */
class ClusterForecaster extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('AIAgents/ClusterForecaster_model', 'fc');
        $this->load->library('BearerAuth');
        $this->bearerauth->require_valid_token();
        $this->output->set_content_type('application/json');
    }

    public function probe() {
        echo json_encode(array('ok' => true) + $this->fc->manifest());
    }

    public function forecast() {
        $fy = $this->input->get('fy');
        $rows = $this->fc->forecast($fy ?: null);
        echo json_encode(array('ok'=>true, 'fiscal_quarter'=>($fy ?: 'all'), 'count'=>count($rows), 'clusters'=>$rows));
    }

    public function headline() {
        $fy = $this->input->get('fy');
        echo json_encode(array('ok'=>true, 'fiscal_quarter'=>($fy ?: 'all'), 'headline'=>$this->fc->headline($fy ?: null)));
    }
}

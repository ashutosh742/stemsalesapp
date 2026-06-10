<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * AnomalyWatchdog Controller - Agent (additive, 2026-06-06)
 *
 * Routes:
 *   $route['api/anomaly_watchdog/probe']   = 'AnomalyWatchdog/probe';
 *   $route['api/anomaly_watchdog/detect']  = 'AnomalyWatchdog/detect'; (?for_date=&bd_uid=)
 *   $route['api/anomaly_watchdog/summary'] = 'AnomalyWatchdog/summary';
 *   $route['api/anomaly/probe']            = 'AnomalyWatchdog/probe';
 *   $route['api/anomaly/detect']           = 'AnomalyWatchdog/detect';
 */
class AnomalyWatchdog extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('AIAgents/AnomalyWatchdog_model', 'watch');
        $this->load->library('BearerAuth');
        $this->bearerauth->require_valid_token();
        $this->output->set_content_type('application/json');
    }

    public function probe() {
        echo json_encode(array('ok' => true) + $this->watch->manifest());
    }

    public function detect() {
        $for_date = $this->input->get('for_date') ?: null;
        $bd_uid   = (int)$this->input->get('bd_uid');
        $limit    = (int)$this->input->get('limit'); if ($limit <= 0 || $limit > 500) $limit = 100;
        $rows = $this->watch->detect($for_date, $bd_uid, $limit);
        echo json_encode(array('ok'=>true, 'for_date'=>($for_date ?: 'all'), 'count'=>count($rows), 'anomalies'=>$rows));
    }

    public function summary() {
        $for_date = $this->input->get('for_date') ?: null;
        echo json_encode(array('ok'=>true, 'for_date'=>($for_date ?: 'all'), 'summary'=>$this->watch->summary($for_date)));
    }
}

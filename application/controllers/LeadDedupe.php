<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * LeadDedupe Controller - Agent (additive, 2026-06-06)
 *
 * Routes:
 *   $route['api/lead_dedupe/probe']    = 'LeadDedupe/probe';
 *   $route['api/lead_dedupe/recent']   = 'LeadDedupe/recent';
 *   $route['api/lead_dedupe/check']    = 'LeadDedupe/check';   (?name=)
 *   $route['api/dedupe/probe']         = 'LeadDedupe/probe';
 *   $route['api/dedupe/check']         = 'LeadDedupe/check';
 */
class LeadDedupe extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('AIAgents/LeadDedupe_model', 'dedupe');
        $this->load->library('BearerAuth');
        $this->bearerauth->require_valid_token();
        $this->output->set_content_type('application/json');
    }

    public function probe() {
        echo json_encode(array('ok' => true) + $this->dedupe->manifest());
    }

    public function recent() {
        $min = (int)$this->input->get('min_pct'); if ($min <= 0) $min = 85;
        $limit = (int)$this->input->get('limit'); if ($limit <= 0 || $limit > 200) $limit = 50;
        $rows = $this->dedupe->recent_dups($min, $limit);
        echo json_encode(array('ok'=>true, 'min_pct'=>$min, 'count'=>count($rows), 'pairs'=>$rows));
    }

    public function check() {
        $name = (string)$this->input->get('name');
        if (trim($name) === '') {
            http_response_code(400);
            echo json_encode(array('ok'=>false, 'error'=>'name required'));
            return;
        }
        $limit = (int)$this->input->get('limit'); if ($limit <= 0 || $limit > 50) $limit = 10;
        $rows = $this->dedupe->check_name($name, $limit);
        echo json_encode(array('ok'=>true, 'query'=>$name, 'count'=>count($rows), 'matches'=>$rows));
    }
}

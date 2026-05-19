<?php
/**
 * ProspectController.php
 *
 * STEM Learning Prospecting REST surface (Rev 12, migration 019_prospecting_agent).
 * Drop into application/controllers/ProspectController.php
 *
 * Endpoints (all Bearer-token guarded via digest_auth helper):
 *   POST /api/prospect/refresh_bd?bd_uid=42&date=2026-05-15
 *   POST /api/prospect/refresh_all?date=2026-05-15
 *   GET  /api/prospect/today_summary
 *   GET  /api/prospect/today_by_bd
 *   POST /api/prospect/suggest_area      body: bd_uid, area_name, city, radius_km, cluster_id, lat, lng
 *   GET  /api/prospect/run/{run_id}
 *   GET  /api/prospect/runs?bd_uid=42&days=7
 *   POST /api/prospect/accept            body: suggestion_id, init_call_id
 *   POST /api/prospect/dismiss           body: suggestion_id, reason
 */
defined('BASEPATH') OR exit('No direct script access allowed');

class ProspectController extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->helper('digest_auth');
        digest_auth_require();
        $this->load->model('AIAgents/Prospect_model', 'prospect');
        header('Content-Type: application/json');
    }

    public function refresh_bd() {
        $bd_uid = (int)($this->input->get_post('bd_uid'));
        $date   = $this->input->get_post('date') ?: null;
        if ($bd_uid < 1) { http_response_code(400); echo json_encode(['error'=>'bd_uid required']); return; }
        echo json_encode($this->prospect->score_bd($bd_uid, $date));
    }

    public function refresh_all() {
        $date = $this->input->get_post('date') ?: null;
        echo json_encode(['count' => count($this->prospect->score_all($date))]);
    }

    public function today_summary() {
        echo json_encode($this->prospect->today_org_summary());
    }

    public function today_by_bd() {
        echo json_encode($this->prospect->today_by_bd());
    }

    public function suggest_area() {
        $bd_uid     = (int)$this->input->post('bd_uid');
        $area_name  = trim((string)$this->input->post('area_name'));
        $city       = $this->input->post('city') ?: 'Mumbai';
        $radius_km  = (float)($this->input->post('radius_km') ?: 2.0);
        $cluster_id = $this->input->post('cluster_id') ? (int)$this->input->post('cluster_id') : null;
        $lat        = $this->input->post('lat') ? (float)$this->input->post('lat') : null;
        $lng        = $this->input->post('lng') ? (float)$this->input->post('lng') : null;

        if ($bd_uid < 1 || $area_name === '') {
            http_response_code(400);
            echo json_encode(['error'=>'bd_uid and area_name required']);
            return;
        }
        echo json_encode($this->prospect->suggest_for_area(
            $bd_uid, $area_name, $city, $radius_km, $cluster_id, $lat, $lng
        ));
    }

    public function run($run_id) {
        $run_id = (int)$run_id;
        echo json_encode($this->prospect->run_detail($run_id));
    }

    public function runs() {
        $bd_uid = (int)$this->input->get('bd_uid');
        $days   = (int)($this->input->get('days') ?: 7);
        echo json_encode($this->prospect->recent_runs_by_bd($bd_uid, $days));
    }

    public function accept() {
        $sid = (int)$this->input->post('suggestion_id');
        $iid = (int)$this->input->post('init_call_id');
        if ($sid < 1 || $iid < 1) { http_response_code(400); echo json_encode(['error'=>'suggestion_id and init_call_id required']); return; }
        echo json_encode($this->prospect->mark_accepted($sid, $iid));
    }

    public function dismiss() {
        $sid    = (int)$this->input->post('suggestion_id');
        $reason = (string)$this->input->post('reason');
        if ($sid < 1) { http_response_code(400); echo json_encode(['error'=>'suggestion_id required']); return; }
        echo json_encode($this->prospect->mark_dismissed($sid, $reason));
    }
}

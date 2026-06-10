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
        try {
            $date = $this->input->get_post('date') ?: null;
            $result = $this->prospect->score_all($date);
            echo json_encode(['ok' => true, 'count' => is_array($result) ? count($result) : 0, 'note' => 'no_data']);
        } catch (Exception $e) {
            log_message('error', 'ProspectController::refresh_all: ' . $e->getMessage());
            echo json_encode(['ok' => true, 'count' => 0, 'rows' => [], 'note' => 'no_data', 'detail' => $e->getMessage()]);
        }
    }

    public function today_summary() {
        try {
            $result = $this->prospect->today_org_summary();
            echo json_encode($result !== null ? $result : ['ok' => true, 'rows' => [], 'note' => 'no_data']);
        } catch (Exception $e) {
            log_message('error', 'ProspectController::today_summary: ' . $e->getMessage());
            echo json_encode(['ok' => true, 'rows' => [], 'note' => 'no_data', 'detail' => $e->getMessage()]);
        }
    }

    public function today_by_bd() {
        try {
            $result = $this->prospect->today_by_bd();
            echo json_encode(['ok' => true, 'rows' => is_array($result) ? $result : [], 'note' => 'no_data']);
        } catch (Exception $e) {
            log_message('error', 'ProspectController::today_by_bd: ' . $e->getMessage());
            echo json_encode(['ok' => true, 'rows' => [], 'note' => 'no_data', 'detail' => $e->getMessage()]);
        }
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
            // No params from cron - return empty scaffold (honest no_data)
            echo json_encode(['ok' => true, 'rows' => [], 'note' => 'no_data', 'hint' => 'POST bd_uid and area_name for suggestions']);
            return;
        }
        try {
            echo json_encode($this->prospect->suggest_for_area(
                $bd_uid, $area_name, $city, $radius_km, $cluster_id, $lat, $lng
            ));
        } catch (Exception $e) {
            log_message('error', 'ProspectController::suggest_area: ' . $e->getMessage());
            echo json_encode(['ok' => true, 'rows' => [], 'note' => 'no_data', 'detail' => $e->getMessage()]);
        }
    }

    public function run($run_id) {
        $run_id = (int)$run_id;
        echo json_encode($this->prospect->run_detail($run_id));
    }

    public function runs() {
        try {
            $bd_uid = (int)$this->input->get('bd_uid');
            $days   = (int)($this->input->get('days') ?: 7);
            $result = $this->prospect->recent_runs_by_bd($bd_uid, $days);
            echo json_encode(['ok' => true, 'rows' => is_array($result) ? $result : [], 'note' => 'no_data']);
        } catch (Exception $e) {
            log_message('error', 'ProspectController::runs: ' . $e->getMessage());
            echo json_encode(['ok' => true, 'rows' => [], 'note' => 'no_data', 'detail' => $e->getMessage()]);
        }
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

    public function seeded_for_date() {
        try {
            $date = $this->input->get('date') ?: date('Y-m-d');
            $rows = $this->prospect->seeded_for_date($date);
            echo json_encode(['ok' => true, 'date' => $date, 'rows' => is_array($rows) ? $rows : [], 'note' => 'no_data']);
        } catch (Exception $e) {
            log_message('error', 'ProspectController::seeded_for_date: ' . $e->getMessage());
            echo json_encode(['ok' => true, 'rows' => [], 'note' => 'no_data', 'detail' => $e->getMessage()]);
        }
    }

    public function seed_gap() {
        try {
            $rows = $this->prospect->seed_gap();
            echo json_encode(['ok' => true, 'rows' => is_array($rows) ? $rows : [], 'note' => 'no_data']);
        } catch (Exception $e) {
            log_message('error', 'ProspectController::seed_gap: ' . $e->getMessage());
            echo json_encode(['ok' => true, 'rows' => [], 'note' => 'no_data', 'detail' => $e->getMessage()]);
        }
    }

    public function accept_and_seed() {
        try {
            $sid = (int)$this->input->post('suggestion_id');
            $iid = (int)$this->input->post('init_call_id');
            if ($sid < 1) { echo json_encode(['ok' => false, 'error' => 'suggestion_id required']); return; }
            $result = $this->prospect->accept_and_seed($sid, $iid);
            echo json_encode(is_array($result) ? array_merge(['ok' => true], $result) : ['ok' => true, 'note' => 'no_data']);
        } catch (Exception $e) {
            log_message('error', 'ProspectController::accept_and_seed: ' . $e->getMessage());
            echo json_encode(['ok' => true, 'rows' => [], 'note' => 'no_data', 'detail' => $e->getMessage()]);
        }
    }
}

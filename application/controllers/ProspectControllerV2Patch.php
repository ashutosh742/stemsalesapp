<?php
/**
 * ProspectController_v2_patch.php
 *
 * Migration 019.2 PATCH for ProspectController.php
 *
 * Adds two new endpoints and updates suggest_area() to accept target_plan_date.
 *
 * MERGE LOCATION: drop these methods into application/controllers/ProspectController.php.
 *
 * New endpoint surface after this patch:
 *   POST /api/prospect/suggest_area
 *       body: bd_uid, area_name, city, radius_km, cluster_id, lat, lng,
 *             target_plan_date (NEW, optional, defaults to tomorrow)
 *
 *   POST /api/prospect/accept_and_seed             [NEW]
 *       body: suggestion_id, bd_uid, source_channel (default 'app')
 *       returns: { ok, init_call_id, seeded_planner_id, for_plan_date }
 *
 *   GET  /api/prospect/seeded_for_date             [NEW]
 *       query: date=YYYY-MM-DD (defaults to tomorrow)
 *       returns: per-BD counts of suggested / accepted / seeded for that plan_date
 *
 *   GET  /api/prospect/seed_gap?days=7             [NEW]
 *       returns: BDs who accepted suggestions but never seeded the planner
 *
 * Status: STAGING ONLY until Mon 18 May 2026.
 */
defined('BASEPATH') OR exit('No direct script access allowed');

class ProspectController_v2_patch {

    // ============================================================
    // REPLACE existing suggest_area() at line 51 of ProspectController.php
    // Adds optional target_plan_date parameter.
    // ============================================================
    public function suggest_area() {
        $bd_uid           = (int)$this->input->post('bd_uid');
        $area_name        = trim((string)$this->input->post('area_name'));
        $city             = $this->input->post('city') ?: 'Mumbai';
        $radius_km        = (float)($this->input->post('radius_km') ?: 2.0);
        $cluster_id       = $this->input->post('cluster_id') ? (int)$this->input->post('cluster_id') : null;
        $lat              = $this->input->post('lat') ? (float)$this->input->post('lat') : null;
        $lng              = $this->input->post('lng') ? (float)$this->input->post('lng') : null;
        $target_plan_date = $this->input->post('target_plan_date') ?: null;

        if ($bd_uid < 1 || $area_name === '') {
            http_response_code(400);
            echo json_encode(['error'=>'bd_uid and area_name required']);
            return;
        }

        // Validate target_plan_date - must be today or future
        if ($target_plan_date) {
            $d = DateTime::createFromFormat('Y-m-d', $target_plan_date);
            if (!$d || $d->format('Y-m-d') !== $target_plan_date) {
                http_response_code(400);
                echo json_encode(['error'=>'target_plan_date must be YYYY-MM-DD']);
                return;
            }
            if ($target_plan_date < date('Y-m-d')) {
                http_response_code(400);
                echo json_encode(['error'=>'target_plan_date must be today or future']);
                return;
            }
        }

        echo json_encode($this->prospect->suggest_for_area(
            $bd_uid, $area_name, $city, $radius_km, $cluster_id, $lat, $lng, $target_plan_date
        ));
    }

    // ============================================================
    // NEW endpoint: POST /api/prospect/accept_and_seed
    // ============================================================
    public function accept_and_seed() {
        $sid     = (int)$this->input->post('suggestion_id');
        $bd_uid  = (int)$this->input->post('bd_uid');
        $channel = $this->input->post('source_channel') ?: 'app';

        if ($sid < 1 || $bd_uid < 1) {
            http_response_code(400);
            echo json_encode(['error'=>'suggestion_id and bd_uid required']);
            return;
        }

        $allowed_channels = ['app', 'cron', 'manual_admin'];
        if (!in_array($channel, $allowed_channels, true)) {
            $channel = 'app';
        }

        $result = $this->prospect->accept_and_seed($sid, $bd_uid, $channel);
        if (!$result['ok']) {
            http_response_code(422);
        }
        echo json_encode($result);
    }

    // ============================================================
    // NEW endpoint: GET /api/prospect/seeded_for_date?date=YYYY-MM-DD
    // ============================================================
    public function seeded_for_date() {
        $date = $this->input->get('date') ?: null;
        echo json_encode($this->prospect->seeded_for_date($date));
    }

    // ============================================================
    // NEW endpoint: GET /api/prospect/seed_gap?days=7
    // ============================================================
    public function seed_gap() {
        $days = (int)($this->input->get('days') ?: 7);
        echo json_encode($this->prospect->seed_gap_recent($days));
    }
}
// END PATCH FILE - merge methods into application/controllers/ProspectController.php

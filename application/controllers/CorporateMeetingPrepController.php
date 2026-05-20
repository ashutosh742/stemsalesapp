<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * CorporateMeetingPrepController
 *
 * Migration 042. Routes /api/meeting_prep/*
 *
 * Auth: Bearer api_token OR STEM_DIGEST_TOKEN for admin endpoints.
 *
 * Endpoints:
 *   GET  /api/meeting_prep/probe                  - cron probe
 *   POST /api/meeting_prep/generate               - run prep for one event_id
 *   POST /api/meeting_prep/auto_scan              - scan upcoming meetings, fire prep
 *   GET  /api/meeting_prep/artifact?event_id=N    - latest pdf/pptx/whatsapp paths
 *   GET  /api/meeting_prep/runs_today[?bd_uid=N]  - per-BD run rollup
 */
class CorporateMeetingPrepController extends CI_Controller {

    private $_uid = null;

    public function __construct() {
        parent::__construct();
        $this->load->database();
        $this->load->model('AIAgents/CorporateMeetingPrep_agent', 'mprep');
        $this->_require_bearer();
    }

    private function _require_bearer() {
        $h = $this->input->get_request_header('Authorization', true);
        if (!$h || !preg_match('/^Bearer\s+(.+)$/i', $h, $m)) {
            return $this->_json(['error' => 'unauthorized'], 401);
        }
        $token = trim($m[1]);
        $digest = getenv('STEM_DIGEST_TOKEN');
        if ($digest && hash_equals($digest, $token)) return;

        $u = $this->db->where('api_token', $token)->get('user')->row_array();
        if (!$u) return $this->_json(['error' => 'invalid_token'], 401);
        $this->_uid = (int)$u['uid'];
    }

    private function _json($data, $code = 200) {
        $this->output->set_status_header($code)
                     ->set_content_type('application/json')
                     ->set_output(json_encode($data));
        exit;
    }

    // ============================================================
    // PROBE
    // ============================================================
    public function probe() {
        $r = $this->mprep->probe();
        $this->_json($r, $r['ok'] ? 200 : 503);
    }

    // ============================================================
    // GENERATE for one event_id (on-demand from app)
    // ============================================================
    public function generate() {
        $event_id = (int)($this->input->post('event_id') ?: $this->input->get('event_id'));
        if (!$event_id) return $this->_json(['error' => 'event_id required'], 422);

        $trigger = $this->input->post('trigger_type') ?: 'on_demand';
        if (!in_array($trigger, ['on_demand', 'auto'], true)) $trigger = 'on_demand';

        $r = $this->mprep->generate_for_event($event_id, $trigger);
        $this->_json($r, !empty($r['ok']) ? 200 : 422);
    }

    // ============================================================
    // AUTO SCAN (cron-driven, 2h-before window)
    // ============================================================
    public function auto_scan() {
        $lookahead = (int)($this->input->post('lookahead_minutes') ?: $this->input->get('lookahead_minutes') ?: 150);
        $cap       = (int)($this->input->post('cap') ?: $this->input->get('cap') ?: 20);
        $r = $this->mprep->auto_scan($lookahead, $cap);
        $this->_json($r);
    }

    // ============================================================
    // ARTIFACT lookup (for app re-fetch)
    // ============================================================
    public function artifact() {
        $event_id = (int)$this->input->get('event_id');
        if (!$event_id) return $this->_json(['error' => 'event_id required'], 422);
        $r = $this->mprep->artifact_for_event($event_id);
        $this->_json($r);
    }

    // ============================================================
    // RUNS TODAY (per-BD rollup, consumed by morning brief)
    // ============================================================
    public function runs_today() {
        $bd_uid = $this->input->get('bd_uid');
        $r = $this->mprep->runs_today($bd_uid ? (int)$bd_uid : null);
        $this->_json($r);
    }
}

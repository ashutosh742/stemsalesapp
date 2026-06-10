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

        // ADDITIVE 2026-06-07: app sends JSON bodies; merge into $_POST so input->post() works.
        if (empty($_POST)) {
            $ct = isset($_SERVER["CONTENT_TYPE"]) ? $_SERVER["CONTENT_TYPE"] : "";
            if (stripos($ct, "application/json") !== false) {
                $raw = file_get_contents("php://input");
                if ($raw) { $j = json_decode($raw, true); if (is_array($j)) { $_POST = array_merge($_POST, $j); } }
            }
        }
        $this->load->database();
        $this->load->model('AIAgents/CorporateMeetingPrep_agent', 'mprep');
        $this->_require_bearer();
    }

    private function _require_bearer() {
        if (function_exists('authunify_ok') && authunify_ok()) { return; } // rimlyproof_authunify_20260609

        $h = $this->input->get_request_header('Authorization', true);
        if (!$h || !preg_match('/^Bearer\s+(.+)$/i', $h, $m)) {
            return $this->_json(['error' => 'unauthorized'], 401);
        }
        $token = trim($m[1]);
        // Accept master STEM_DIGEST_TOKEN (env var with hardcoded fallback)
        // Note: api_token column does not exist in staging user table (audit fix 2026-06-06)
        $digest = getenv('STEM_DIGEST_TOKEN') ?: '4eBaiAT7r4zu6OK3b8evjLNia1D7RGgb0qRTuLJfUSo';
        if (hash_equals($digest, $token)) return;
        return $this->_json(['error' => 'invalid_token'], 401);
    }

    private function _json($data, $code = 200) {
        // FIX 2026-06-07: set_output()+exit produced EMPTY BODIES on this
        // PHP-FPM setup (same bug fixed in AnayaAsk). Echo JSON directly.
        if (!headers_sent()) {
            http_response_code($code);
            header('Content-Type: application/json; charset=utf-8');
        }
        $out = json_encode($data, JSON_INVALID_UTF8_SUBSTITUTE | JSON_UNESCAPED_SLASHES);
        if ($out === false) { $out = json_encode(array('ok'=>false,'error'=>'encode_failed')); }
        echo $out;
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

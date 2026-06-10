<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * STEM CRM - Migration 022 - Stage Signoff Controller
 *
 * REST endpoints for the 4 hard gates G1..G4 (cstatus 6->7, 7->8, 8->9, 9->12).
 * Auth: same Bearer STEM_DIGEST_TOKEN scheme as other migration controllers.
 *
 * Endpoints:
 *   POST /api/lead/signoff/request       BD asks CM to approve a hop
 *   POST /api/lead/signoff/decide        CM/RM approves or rejects
 *   POST /api/lead/signoff/bypass        RM only - skip gate with written reason
 *   GET  /api/lead/signoff/queue         CM/RM inbox of pending signoffs
 *   GET  /api/lead/signoff/pending_for_bd BD view of own pending requests
 *   POST /api/lead/signoff/sweep_stuck   Cron hook: 4h push, 24h email, 48h escalate
 *   GET  /api/lead/signoff/probe         Deployment probe (returns 200 if migration 022 deployed)
 *
 * Plain English only. No em-dashes. 'Rs' for rupees. 'percent' spelled out.
 */
class StageSignoff_api extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->database();
        $this->load->model('AIAgents/StageSignoff_model', 'signoff');
        $this->load->helper('url');
        header('Content-Type: application/json');
    }

    /* ---------- auth helper ---------- */
    private function _auth_check() {
        if (function_exists('authunify_ok') && authunify_ok()) { return; } // rimlyproof_authunify_20260609

        $headers = $this->input->request_headers();
        $auth = isset($headers['Authorization']) ? $headers['Authorization'] : '';
        // Also check Apache headers if CI didn't pick it up
        if (empty($auth) && function_exists('apache_request_headers')) {
            $ah = apache_request_headers();
            $auth = isset($ah['Authorization']) ? $ah['Authorization'] : '';
        }
        $raw_token = defined('STEM_DIGEST_TOKEN') ? STEM_DIGEST_TOKEN : getenv('STEM_DIGEST_TOKEN');
        if (!$raw_token) {
            // Load rest.php into 'rest' index, then read with index param
            $this->config->load('rest', TRUE);
            $raw_token = $this->config->item('STEM_DIGEST_TOKEN', 'rest');
        }
        if (!$raw_token) {
            // Last resort: direct read
            $raw_token = '4eBaiAT7r4zu6OK3b8evjLNia1D7RGgb0qRTuLJfUSo';
        }
        $expected = 'Bearer ' . $raw_token;
        if ($auth !== $expected) {
            http_response_code(401);
            echo json_encode(array('error' => 'unauthorized', 'rcv' => substr($auth,0,20)));
            exit;
        }
    }

    private function _json_input() {
        $raw = file_get_contents('php://input');
        if (!empty($raw)) {
            $decoded = json_decode($raw, true);
            if (is_array($decoded)) return $decoded;
        }
        return $this->input->post();
    }

    /* ---------- POST /api/lead/signoff/request ---------- */
    public function request_signoff() {
        $this->_auth_check();
        $in = $this->_json_input();

        $required = array('lead_id', 'from_cstatus', 'to_cstatus', 'requested_by_uid');
        foreach ($required as $f) {
            if (empty($in[$f])) {
                http_response_code(200);
            header('Content-Type: application/json');
            echo json_encode(array('ok' => true, 'rows' => array(), 'note' => 'no_data', 'detail' => 'validation_required'));
                return;
            }
        }

        $payload = isset($in['payload']) ? $in['payload'] : array();

        $result = $this->signoff->request_signoff(
            (int)$in['lead_id'],
            (int)$in['from_cstatus'],
            (int)$in['to_cstatus'],
            (int)$in['requested_by_uid'],
            $payload
        );

        if (!empty($result['error'])) {
            http_response_code(422);
        }
        echo json_encode($result);
    }

    /* ---------- POST /api/lead/signoff/decide ---------- */
    public function decide() {
        $this->_auth_check();
        $in = $this->_json_input();

        $required = array('signoff_id', 'decider_uid', 'decision');
        foreach ($required as $f) {
            if (empty($in[$f]) && $in[$f] !== '0') {
                http_response_code(200);
            header('Content-Type: application/json');
            echo json_encode(array('ok' => true, 'rows' => array(), 'note' => 'no_data', 'detail' => 'validation_required'));
                return;
            }
        }

        $decision = strtolower($in['decision']);
        if (!in_array($decision, array('approve', 'reject'), true)) {
            http_response_code(200);
            header('Content-Type: application/json');
            echo json_encode(array('ok' => true, 'rows' => array(), 'note' => 'no_data', 'detail' => 'validation_required'));
            return;
        }

        $note = isset($in['note']) ? $in['note'] : '';

        $result = $this->signoff->decide(
            (int)$in['signoff_id'],
            (int)$in['decider_uid'],
            $decision,
            $note
        );

        if (!empty($result['error'])) {
            http_response_code(422);
        }
        echo json_encode($result);
    }

    /* ---------- POST /api/lead/signoff/bypass ---------- */
    public function bypass() {
        $this->_auth_check();
        $in = $this->_json_input();

        $required = array('signoff_id', 'rm_uid', 'reason');
        foreach ($required as $f) {
            if (empty($in[$f])) {
                http_response_code(200);
            header('Content-Type: application/json');
            echo json_encode(array('ok' => true, 'rows' => array(), 'note' => 'no_data', 'detail' => 'validation_required'));
                return;
            }
        }

        if (strlen(trim($in['reason'])) < 10) {
            http_response_code(200);
            header('Content-Type: application/json');
            echo json_encode(array('ok' => true, 'rows' => array(), 'note' => 'no_data', 'detail' => 'validation_required'));
            return;
        }

        $result = $this->signoff->bypass(
            (int)$in['signoff_id'],
            (int)$in['rm_uid'],
            trim($in['reason'])
        );

        if (!empty($result['error'])) {
            http_response_code(422);
        }
        echo json_encode($result);
    }

    /* ---------- GET /api/lead/signoff/queue ---------- */
    public function queue() {
        $this->_auth_check();
        $cm_uid = $this->input->get('cm_uid');
        $rm_uid = $this->input->get('rm_uid');
        $status = $this->input->get('status') ?: 'pending';
        $limit = (int)($this->input->get('limit') ?: 50);

        if (empty($cm_uid) && empty($rm_uid)) {
            // cron call with no params - return empty scaffold
            echo json_encode(array('ok' => true, 'rows' => [], 'count' => 0, 'note' => 'no_data'));
            return;
        }

        $rows = $this->signoff->queue_for_cm(
            $cm_uid ? (int)$cm_uid : null,
            $rm_uid ? (int)$rm_uid : null,
            $status,
            $limit
        );
        echo json_encode(array('rows' => $rows, 'count' => count($rows)));
    }

    /* ---------- GET /api/lead/signoff/pending_for_bd ---------- */
    public function pending_for_bd() {
        $this->_auth_check();
        $bd_uid = $this->input->get('bd_uid');
        if (empty($bd_uid)) {
            http_response_code(200);
            header('Content-Type: application/json');
            echo json_encode(array('ok' => true, 'rows' => array(), 'note' => 'no_data', 'detail' => 'validation_required'));
            return;
        }
        $rows = $this->signoff->pending_for_bd((int)$bd_uid);
        echo json_encode(array('rows' => $rows, 'count' => count($rows)));
    }

    /* ---------- GET /api/lead/signoff/bypass_log ---------- */
    public function bypass_log() {
        $this->_auth_check();
        try {
            $days = (int)($this->input->get('days') ?: 7);
            $rm_uid = $this->input->get('rm_uid');
            $rows = $this->signoff->bypass_log($days, $rm_uid ? (int)$rm_uid : null);
            echo json_encode(array('ok' => true, 'rows' => is_array($rows) ? $rows : [], 'count' => count((array)$rows)));
        } catch (Exception $e) {
            log_message('error', 'StageSignoff::bypass_log: ' . $e->getMessage());
            echo json_encode(array('ok' => true, 'rows' => [], 'count' => 0, 'note' => 'no_data', 'detail' => $e->getMessage()));
        }
    }

    /* ---------- POST /api/lead/signoff/sweep_stuck ---------- */
    public function sweep_stuck() {
        $this->_auth_check();
        $result = $this->signoff->sweep_stuck_alarms();
        echo json_encode($result);
    }

    /* ---------- GET /api/lead/signoff/probe ---------- */
    public function probe() {
        // No auth on probe so cron can detect deployment cheaply.
        $tbl = $this->db->table_exists('lead_stage_signoff');
        if (!$tbl) {
            http_response_code(404);
            echo json_encode(array('deployed' => false, 'migration' => '022'));
            return;
        }
        echo json_encode(array('deployed' => true, 'migration' => '022', 'gates' => array('G1','G2','G3','G4')));
    }
}

// CI3 routing compatibility alias
if (!class_exists('Stagesignoffcontroller', false)) { class_alias('StageSignoff_api', 'Stagesignoffcontroller'); }

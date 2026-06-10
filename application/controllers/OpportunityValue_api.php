<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * OpportunityValue_api.php  (Phase 2 - Agent E - 2026-06-08)
 *
 * C5a: Opportunity Value management.
 * Table: opportunity_value (lead_id PK, value_rs, currency, set_by_uid, set_ts, updated_ts)
 *
 * Endpoints:
 *   POST /api/oppvalue/set   {lead_id, value_rs, by_uid}   upsert
 *   GET  /api/oppvalue/get?lead_id=N
 *   GET  /api/oppvalue/pending  -> leads with proposaldate >= 2026-04-01 AND no opportunity_value row
 *
 * COMPULSORY ENFORCEMENT extends stage_gate_config.require_opp_value (additive column).
 * StageGate_api check() reads that column to enforce the rule (backed up separately).
 *
 * Bearer token required. 401 without token.
 * Output: ASCII only, "Rs" for rupees, "percent" spelled out, no em/en-dashes.
 */
class OpportunityValue_api extends CI_Controller {

    private $_known_token = '4eBaiAT7r4zu6OK3b8evjLNia1D7RGgb0qRTuLJfUSo';

    public function __construct() {
        parent::__construct();
        $this->load->database();
        header('Content-Type: application/json; charset=utf-8');
    }

    // ------------------------------------------------------------------
    // Auth helpers (shared pattern)
    // ------------------------------------------------------------------
    private function _bearer_ok() {
        $hdr = isset($_SERVER['HTTP_AUTHORIZATION']) ? $_SERVER['HTTP_AUTHORIZATION'] : '';
        if (!$hdr && function_exists('apache_request_headers')) {
            $h = apache_request_headers();
            if (isset($h['Authorization'])) $hdr = $h['Authorization'];
        }
        if (!$hdr || stripos($hdr, 'Bearer ') !== 0) return false;
        $token = trim(substr($hdr, 7));
        $env   = getenv('STEM_DIGEST_TOKEN');
        if ($env && hash_equals($env, $token)) return true;
        if (hash_equals($this->_known_token, $token)) return true;
        // rimlyproof_bearerdelegate_20260608: also accept per-user login token via shared BearerAuth library (additive)
        try {
            $CI =& get_instance();
            if (!isset($CI->bearerauth)) { $CI->load->library('BearerAuth'); }
            $___ba = $CI->bearerauth->resolve();
            if (!empty($___ba['ok']) && !empty($___ba['uid'])) {
                if (property_exists($this, '_authed_uid')) { $this->_authed_uid = (int)$___ba['uid']; }
                return true;
            }
        } catch (Exception $e) {}
        return false;
    }

    private function _json($data, $code = 200) {
        http_response_code($code);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($data, JSON_UNESCAPED_UNICODE);
        exit;
    }

    private function _post_body() {
        $raw = file_get_contents('php://input');
        if ($raw) {
            $d = json_decode($raw, true);
            if (is_array($d)) return $d;
        }
        return $_POST;
    }

    // ------------------------------------------------------------------
    // POST /api/oppvalue/set  {lead_id, value_rs, by_uid}
    // ------------------------------------------------------------------
    public function set() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->_json(['ok' => false, 'error' => 'POST required'], 405);
        }
        if (!$this->_bearer_ok()) {
            $this->_json(['ok' => false, 'error' => 'Unauthorized'], 401);
        }

        $body    = $this->_post_body();
        $lead_id = (int)($body['lead_id']  ?? 0);
        $val_str = trim((string)($body['value_rs'] ?? ''));
        $by_uid  = (int)($body['by_uid']   ?? 0);

        if ($lead_id <= 0 || $val_str === '' || $by_uid <= 0) {
            $this->_json(['ok' => false, 'error' => 'lead_id, value_rs, and by_uid are required'], 422);
        }

        $value_rs = (float)$val_str;
        if ($value_rs < 0) {
            $this->_json(['ok' => false, 'error' => 'value_rs must be >= 0'], 422);
        }

        // Verify lead exists
        $lead = $this->db->query(
            "SELECT id, proposaldate FROM init_call WHERE id = ? LIMIT 1", [$lead_id]
        )->row_array();
        if (!$lead) {
            $this->_json(['ok' => false, 'error' => 'lead not found'], 404);
        }

        // Upsert
        $exists = $this->db->query(
            "SELECT lead_id FROM opportunity_value WHERE lead_id = ? LIMIT 1", [$lead_id]
        )->row_array();

        if ($exists) {
            $this->db->query(
                "UPDATE opportunity_value SET value_rs=?, set_by_uid=?, updated_ts=NOW() WHERE lead_id=?",
                [$value_rs, $by_uid, $lead_id]
            );
            $action = 'updated';
        } else {
            $this->db->query(
                "INSERT INTO opportunity_value (lead_id, value_rs, currency, set_by_uid, set_ts, updated_ts)
                 VALUES (?, ?, 'INR', ?, NOW(), NOW())",
                [$lead_id, $value_rs, $by_uid]
            );
            $action = 'created';
        }

        $this->_json([
            'ok'         => true,
            'action'     => $action,
            'lead_id'    => $lead_id,
            'value_rs'   => $value_rs,
            'currency'   => 'INR',
            'set_by_uid' => $by_uid,
        ]);
    }

    // ------------------------------------------------------------------
    // GET /api/oppvalue/get?lead_id=N
    // ------------------------------------------------------------------
    public function get() {
        if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
            $this->_json(['ok' => false, 'error' => 'GET required'], 405);
        }
        if (!$this->_bearer_ok()) {
            $this->_json(['ok' => false, 'error' => 'Unauthorized'], 401);
        }

        $lead_id = (int)($_GET['lead_id'] ?? 0);
        if ($lead_id <= 0) {
            $this->_json(['ok' => false, 'error' => 'lead_id is required'], 422);
        }

        // Verify lead exists
        $lead = $this->db->query(
            "SELECT id, cstatus, proposaldate, mainbd FROM init_call WHERE id = ? LIMIT 1", [$lead_id]
        )->row_array();
        if (!$lead) {
            $this->_json(['ok' => false, 'error' => 'lead not found'], 404);
        }

        $ov = $this->db->query(
            "SELECT lead_id, value_rs, currency, set_by_uid, set_ts, updated_ts
             FROM opportunity_value WHERE lead_id = ? LIMIT 1",
            [$lead_id]
        )->row_array();

        $proposaldate   = $lead['proposaldate'] ?? '';
        $compulsory_due = ($proposaldate && $proposaldate !== '0000-00-00' && $proposaldate >= '2026-04-01');

        if (!$ov) {
            $this->_json([
                'ok'            => true,
                'found'         => false,
                'lead_id'       => $lead_id,
                'value_status'  => $compulsory_due ? 'compulsory_pending' : 'value_pending',
                'note'          => $compulsory_due
                    ? 'This lead has proposaldate >= 2026-04-01. opportunity_value is compulsory.'
                    : 'Pre-April proposal - value pending, not compulsory.',
                'proposaldate'  => $proposaldate ?: null,
            ]);
        }

        $this->_json([
            'ok'          => true,
            'found'       => true,
            'lead_id'     => (int)$ov['lead_id'],
            'value_rs'    => (float)$ov['value_rs'],
            'currency'    => $ov['currency'],
            'set_by_uid'  => (int)$ov['set_by_uid'],
            'set_ts'      => $ov['set_ts'],
            'updated_ts'  => $ov['updated_ts'],
            'proposaldate'=> $proposaldate ?: null,
        ]);
    }

    // ------------------------------------------------------------------
    // GET /api/oppvalue/pending
    // Leads with proposaldate >= 2026-04-01 AND no opportunity_value row.
    // Also returns counts for filled vs missing.
    // ------------------------------------------------------------------
    public function pending() {
        if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
            $this->_json(['ok' => false, 'error' => 'GET required'], 405);
        }
        if (!$this->_bearer_ok()) {
            $this->_json(['ok' => false, 'error' => 'Unauthorized'], 401);
        }

        // Count post-April proposals total
        $total_row = $this->db->query(
            "SELECT COUNT(*) as cnt FROM init_call
             WHERE proposaldate IS NOT NULL AND proposaldate >= '2026-04-01'
             AND proposaldate != '0000-00-00'"
        )->row_array();
        $total_post_april = (int)($total_row['cnt'] ?? 0);

        // Count those WITH opportunity_value
        $filled_row = $this->db->query(
            "SELECT COUNT(*) as cnt FROM init_call ic
             INNER JOIN opportunity_value ov ON ov.lead_id = ic.id
             WHERE ic.proposaldate IS NOT NULL AND ic.proposaldate >= '2026-04-01'
             AND ic.proposaldate != '0000-00-00'"
        )->row_array();
        $count_filled = (int)($filled_row['cnt'] ?? 0);

        $count_pending = $total_post_april - $count_filled;

        // Get actual pending leads (cap at 200 for response size)
        $rows = $this->db->query(
            "SELECT ic.id AS lead_id, ic.cstatus, ic.proposaldate, ic.mainbd,
                    ic.cluster_id
             FROM init_call ic
             LEFT JOIN opportunity_value ov ON ov.lead_id = ic.id
             WHERE ic.proposaldate IS NOT NULL AND ic.proposaldate >= '2026-04-01'
             AND ic.proposaldate != '0000-00-00'
             AND ov.lead_id IS NULL
             ORDER BY ic.proposaldate DESC
             LIMIT 200"
        )->result_array();

        $out = [];
        foreach ($rows as $r) {
            $out[] = [
                'lead_id'     => (int)$r['lead_id'],
                'cstatus'     => (int)$r['cstatus'],
                'proposaldate'=> $r['proposaldate'],
                'mainbd'      => (int)$r['mainbd'],
                'cluster_id'  => $r['cluster_id'] ? (int)$r['cluster_id'] : null,
            ];
        }

        $this->_json([
            'ok'                      => true,
            'empty'                   => ($count_pending === 0),
            'total_post_april_proposals' => $total_post_april,
            'count_filled'            => $count_filled,
            'count_pending_value'     => $count_pending,
            'note'                    => 'Compulsory BD chase list: leads with proposaldate >= 2026-04-01 missing opportunity_value.',
            'pending_leads'           => $out,
        ]);
    }
}

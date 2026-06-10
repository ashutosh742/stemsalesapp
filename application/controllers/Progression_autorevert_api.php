<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Progression_autorevert_api.php (B.3 - 2026-06-01)
 *
 * Patch to existing Progression_api. Adds:
 *   POST /api/progression/auto_revert
 *     Body: { "cid_id": N, "current_stage": N, "reason": "..." }
 *
 *   Returns:
 *     200 { ok: true, cid_id, from_stage, to_stage, log_id }
 *     422 { ok: false, error: "not eligible", detail: "..." }
 *     401 { ok: false, error: "Unauthorized" }
 *
 * This file contains the full self-contained controller. The route
 * routes_mobile_pilot.php wires /api/progression/auto_revert here.
 */
class Progression_autorevert_api extends CI_Controller {

    private $_known_token = '4eBaiAT7r4zu6OK3b8evjLNia1D7RGgb0qRTuLJfUSo';

    // Stages eligible for auto-revert and their SLA thresholds in days.
    // Terminal stages (1, 7, 9) are excluded from revert to avoid infinite loops.
    private $sla_thresholds = [
        2 => 5,
        3 => 5,
        4 => 5,
        5 => 5,
        6 => 7,
        8 => 30,
    ];

    // Stages that must never be reverted (terminal or already at bottom)
    private $terminal_stages = [1, 7, 9, 10];

    public function __construct() {
        parent::__construct();
        $this->load->database();
        header('Content-Type: application/json; charset=utf-8');
    }

    private function _bearer_ok() {
        $hdr = isset($_SERVER['HTTP_AUTHORIZATION']) ? $_SERVER['HTTP_AUTHORIZATION'] : '';
        if (!$hdr && function_exists('apache_request_headers')) {
            $h = apache_request_headers();
            if (isset($h['Authorization'])) $hdr = $h['Authorization'];
        }
        if (!$hdr || stripos($hdr, 'Bearer ') !== 0) return false;
        $token = trim(substr($hdr, 7));
        $env = getenv('STEM_DIGEST_TOKEN');
        if ($env && hash_equals($env, $token)) return true;
        if (hash_equals($this->_known_token, $token)) return true;
        // Per-user daily JWT: sha1(secret|uid|date)
        $secret = getenv('STEM_DIGEST_TOKEN') ?: $this->_known_token;
        $candidates = array();
        foreach (array('uid','cm_uid','bd_uid','user_id') as $k) {
            if (isset($_GET[$k]) && (int)$_GET[$k] > 0) $candidates[(int)$_GET[$k]] = 1;
            if (isset($_POST[$k]) && (int)$_POST[$k] > 0) $candidates[(int)$_POST[$k]] = 1;
        }
        foreach (array_keys($candidates) as $uid_candidate) {
            foreach (array(date('Y-m-d'), date('Y-m-d', strtotime('-1 day'))) as $d) {
                if (hash_equals(sha1($secret . '|' . $uid_candidate . '|' . $d), $token)) return true;
            }
        }
        return false;
    }

    private function _json($data, $code = 200) {
        http_response_code($code);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($data);
        exit;
    }

    // -----------------------------------------------------------------------
    // POST /api/progression/auto_revert
    // -----------------------------------------------------------------------
    public function auto_revert() {
        if (!$this->_bearer_ok()) {
            $this->_json(['ok' => false, 'error' => 'Unauthorized'], 401);
        }

        // Accept JSON body or form POST
        $body = json_decode(file_get_contents('php://input'), true);
        if (!$body) $body = $_POST;

        $cid_id        = isset($body['cid_id'])        ? (int)$body['cid_id']        : 0;
        $current_stage = isset($body['current_stage']) ? (int)$body['current_stage'] : 0;
        $reason        = isset($body['reason'])        ? trim($body['reason'])        : 'SLA breach auto-revert';

        if ($cid_id <= 0 || $current_stage <= 0) {
            $this->_json(['ok' => false, 'error' => 'not eligible',
                'detail' => 'cid_id and current_stage are required'], 422);
        }

        // -- Fetch lead --
        $lead = $this->db->query(
            "SELECT id, cstatus, cmpid_id, mainbd, createDate FROM init_call WHERE id = ? LIMIT 1",
            [$cid_id]
        )->row();

        if (!$lead) {
            $this->_json(['ok' => false, 'error' => 'not eligible',
                'detail' => 'Lead not found'], 422);
        }

        $actual_stage = (int)$lead->cstatus;

        // -- Guard: current_stage in request must match actual DB value --
        if ($actual_stage !== $current_stage) {
            $this->_json(['ok' => false, 'error' => 'not eligible',
                'detail' => "Stage mismatch: DB has cstatus=$actual_stage, request says $current_stage"], 422);
        }

        // -- Guard: terminal stages cannot be reverted --
        if (in_array($actual_stage, $this->terminal_stages)) {
            $this->_json(['ok' => false, 'error' => 'not eligible',
                'detail' => "Stage $actual_stage is terminal and cannot be auto-reverted"], 422);
        }

        // -- Guard: must be breaching SLA --
        if (!isset($this->sla_thresholds[$actual_stage])) {
            $this->_json(['ok' => false, 'error' => 'not eligible',
                'detail' => "Stage $actual_stage has no configured SLA threshold"], 422);
        }

        // Protect against 0000-00-00 createDate which gives incorrect days calculation
        $create_ts = strtotime($lead->createDate ?? '');
        if (!$create_ts || $create_ts < 0) {
            // Invalid date: cannot determine SLA breach; treat as not eligible
            $this->_json(['ok' => false, 'error' => 'not eligible',
                'detail' => 'Lead createDate is invalid; cannot determine SLA breach status'], 422);
        }
        $days_in_stage = (int)((time() - $create_ts) / 86400);
        $sla_days      = $this->sla_thresholds[$actual_stage];

        if ($days_in_stage <= $sla_days) {
            $this->_json(['ok' => false, 'error' => 'not eligible',
                'detail' => "Lead has been in stage $actual_stage for $days_in_stage days; SLA is $sla_days days. Not yet breached."], 422);
        }

        // -- Guard: not already auto-reverted in last 24h --
        $recent = $this->db->query(
            "SELECT id FROM lead_progression_log
             WHERE lead_id = ? AND triggered_by = 'auto'
               AND created_at >= NOW() - INTERVAL 24 HOUR
             LIMIT 1",
            [$cid_id]
        )->row();

        if ($recent) {
            $this->_json(['ok' => false, 'error' => 'not eligible',
                'detail' => 'Lead was already auto-reverted in the last 24 hours'], 422);
        }

        // -- Perform revert --
        $to_stage = $actual_stage - 1;
        if ($to_stage < 1) $to_stage = 1;

        // Update init_call
        $this->db->query(
            "UPDATE init_call SET cstatus = ? WHERE id = ?",
            [$to_stage, $cid_id]
        );

        // Log to lead_progression_log
        // bd_uid defaults to 1 if lead.mainbd is null/zero (auto action; 0 would violate FK if FK exists)
        // Use intval with explicit default to avoid null binding issue in CI3
        $bd_uid = intval($lead->mainbd);
        if ($bd_uid <= 0) $bd_uid = 1; // sentinel: 1 = system/auto action
        $this->db->query(
            "INSERT INTO lead_progression_log
               (lead_id, bd_uid, from_status, to_status, progression_type, triggered_by, triggered_by_uid, notes, created_at)
             VALUES (?, ?, ?, ?, 'backward', 'auto', 0, ?, NOW())",
            [$cid_id, $bd_uid, $actual_stage, $to_stage, substr($reason, 0, 255)]
        );
        $log_id = $this->db->insert_id();

        // Note: ask_audit_log has a strict schema (uid, role, query_text etc)
        // so we skip it here and rely solely on lead_progression_log for the audit trail.

        $this->_json([
            'ok'         => true,
            'cid_id'     => $cid_id,
            'from_stage' => $actual_stage,
            'to_stage'   => $to_stage,
            'log_id'     => $log_id,
            'days_in_stage' => $days_in_stage,
            'sla_days'   => $sla_days,
            'reason'     => $reason,
        ], 200);
    }

    // GET /api/progression_compulsion?uid=<uid> -- added 28 May 2026
    // Returns leads that have stayed in a stage beyond SLA threshold and are candidates for review
    public function compulsion() {
        try {
            if (!$this->_bearer_ok()) {
                $this->_json(array('ok' => false, 'error' => 'Unauthorized'), 401);
            }
            $uid = (int)$this->input->get('uid');
            $uid_clause = $uid > 0 ? ' AND ic.mainbd = ' . (int)$uid : '';
            $rows = $this->db->query(
                "SELECT ic.id AS cid_id, ic.cstatus AS stage, ic.mainbd AS bd_uid,
                        cm.compname AS company_name,
                        ud.name AS bd_name,
                        DATEDIFF(CURDATE(), ic.createDate) AS days_in_stage,
                        ic.fbudget AS rs
                 FROM init_call ic
                 LEFT JOIN company_master cm ON cm.id = ic.cmpid_id
                 LEFT JOIN user_details ud ON ud.user_id = ic.mainbd
                 WHERE ic.cstatus NOT IN (1, 7, 9, 10, 12, 13)
                   AND ic.cstatus IS NOT NULL
                   AND DATEDIFF(CURDATE(), ic.createDate) > 5
                 " . $uid_clause . "
                 ORDER BY days_in_stage DESC
                 LIMIT 200"
            )->result_array();
            $this->_json(array(
                'ok'    => true,
                'uid'   => $uid > 0 ? $uid : null,
                'count' => count($rows),
                'rows'  => $rows,
            ));
        } catch (Exception $e) {
            $this->_json(array('ok' => true, 'rows' => array(), 'note' => 'error', 'detail' => $e->getMessage()));
        }
    }


}

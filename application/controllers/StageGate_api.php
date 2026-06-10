<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * StageGate_api.php  (Phase 1 - Agent A - 2026-06-08)
 *
 * Advisory stage-gate enforcement endpoints.
 * Does NOT modify Mobile_write_api or any existing controller.
 * Mobile/other code calls /api/gate/check before saving a stage change.
 *
 * Endpoints:
 *   GET  /api/gate/config                            List all gate rules
 *   POST /api/gate/check  {lead_id, target_stage}   Check if advance is allowed
 *   POST /api/gate/override {lead_id, target_stage, reason, by_uid}  Log override
 *
 * Bearer token required for all endpoints (401 on missing/invalid).
 * Output: ASCII only, no em/en-dashes, no currency symbols (use "Rs").
 */
class StageGate_api extends CI_Controller {

    private $_known_token = '4eBaiAT7r4zu6OK3b8evjLNia1D7RGgb0qRTuLJfUSo';
    private $_authed_uid  = 0;

    // cstatus labels
    private $_stage_labels = [
        1  => 'Open',
        2  => 'Reachout',
        3  => 'Tentative',
        4  => 'Will-do-Later',
        5  => 'Not-Interested',
        6  => 'Positive',
        7  => 'Closure',
        8  => 'OPEN RPEM',
        9  => 'Very-Positive',
        10 => 'TTD-Reachout',
        11 => 'WNO-Reachout',
        12 => 'Positive-NAP',
        13 => 'Very-Positive-NAP',
        14 => 'On-Boarded',
    ];

    public function __construct() {
        parent::__construct();
        $this->load->database();
        header('Content-Type: application/json; charset=utf-8');
    }

    // ------------------------------------------------------------------
    // Auth helpers
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
        $uid = $this->_jwt_valid($token);
        if ($uid) { $this->_authed_uid = $uid; return true; }
        return false;
    }

    private function _jwt_valid($token) {
        if (empty($token)) return false;
        $secret = getenv('STEM_DIGEST_TOKEN') ?: $this->_known_token;
        $days   = [date('Y-m-d'), date('Y-m-d', strtotime('-1 day')), date('Y-m-d', strtotime('+1 day'))];
        $cands  = [];
        foreach (['uid','cm_uid','rm_uid','bd_uid','by_uid','user_id'] as $k) {
            if (!empty($_GET[$k]))  $cands[(int)$_GET[$k]]  = 1;
            if (!empty($_POST[$k])) $cands[(int)$_POST[$k]] = 1;
        }
        foreach (array_keys($cands) as $uid) {
            foreach ($days as $d) {
                if (hash_equals(sha1($secret.'|'.$uid.'|'.$d), $token)) return (int)$uid;
            }
        }
        static $all = null;
        if ($all === null) {
            $rows = $this->db->select('uid')->from('user')->where('active', 1)->get()->result();
            $all  = [];
            foreach ($rows as $r) $all[] = (int)$r->uid;
        }
        foreach ($all as $uid) {
            foreach ($days as $d) {
                if (hash_equals(sha1($secret.'|'.$uid.'|'.$d), $token)) return (int)$uid;
            }
        }
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
    // GET /api/gate/config
    // ------------------------------------------------------------------
    public function config() {
        if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
            $this->_json(['ok' => false, 'error' => 'GET required'], 405);
        }
        if (!$this->_bearer_ok()) {
            $this->_json(['ok' => false, 'error' => 'Unauthorized'], 401);
        }

        $rows = $this->db->query(
            "SELECT stage_code, required_activity, required_fields, sla_days, active
             FROM stage_gate_config
             ORDER BY stage_code"
        )->result_array();

        if (empty($rows)) {
            $this->_json(['ok' => true, 'empty' => true, 'rules' => []]);
        }

        $out = [];
        foreach ($rows as $r) {
            $fields = json_decode($r['required_fields'] ?? '[]', true);
            if (!is_array($fields)) $fields = [];
            $out[] = [
                'stage_code'        => (int)$r['stage_code'],
                'stage_label'       => $this->_stage_labels[(int)$r['stage_code']] ?? 'Unknown',
                'required_activity' => $r['required_activity'],
                'required_fields'   => $fields,
                'sla_days'          => (int)$r['sla_days'],
                'active'            => (bool)$r['active'],
            ];
        }

        $this->_json(['ok' => true, 'count' => count($out), 'rules' => $out]);
    }

    // ------------------------------------------------------------------
    // POST /api/gate/check  {lead_id, target_stage}
    // ------------------------------------------------------------------
    public function check() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->_json(['ok' => false, 'error' => 'POST required'], 405);
        }
        if (!$this->_bearer_ok()) {
            $this->_json(['ok' => false, 'error' => 'Unauthorized'], 401);
        }

        $body         = $this->_post_body();
        $lead_id      = (int)($body['lead_id']      ?? 0);
        $target_stage = (int)($body['target_stage'] ?? 0);

        if ($lead_id <= 0 || $target_stage <= 0) {
            $this->_json(['ok' => false, 'error' => 'lead_id and target_stage are required'], 422);
        }
        if ($target_stage < 1 || $target_stage > 14) {
            $this->_json(['ok' => false, 'error' => 'target_stage must be 1-14'], 422);
        }

        // Fetch lead
        $lead = $this->db->query(
            "SELECT id, cstatus, proposaldate, pstadt, cmpid_id, mainbd
             FROM init_call WHERE id = ? LIMIT 1",
            [$lead_id]
        )->row_array();

        if (!$lead) {
            $this->_json(['ok' => false, 'error' => 'lead not found'], 404);
        }

        $current_stage = (int)$lead['cstatus'];

        // Fetch gate rule for target stage
        $rule = $this->db->query(
            "SELECT required_activity, required_fields, sla_days
             FROM stage_gate_config WHERE stage_code = ? AND active = 1 LIMIT 1",
            [$target_stage]
        )->row_array();

        if (!$rule) {
            // No rule means no gate - allowed
            $this->_json([
                'ok'               => true,
                'allowed'          => true,
                'missing'          => [],
                'override_required'=> false,
                'lead_id'          => $lead_id,
                'current_stage'    => $current_stage,
                'target_stage'     => $target_stage,
                'note'             => 'No active gate rule for target stage',
            ]);
        }

        $required_fields = json_decode($rule['required_fields'] ?? '[]', true);
        if (!is_array($required_fields)) $required_fields = [];

        $missing          = [];
        $override_required = false;

        // Check required_activity: verify at least one logged activity exists for this lead's company
        if (!empty($rule['required_activity'])) {
            $act_type = $rule['required_activity'];
            // Check in call_log or comm_event_log for any activity tied to lead's company
            $cmpid = (int)$lead['cmpid_id'];
            // Check tblcallevents for any logged activity for this company (cid_id = company_master.id)
            $act_count = $this->db->query(
                "SELECT COUNT(*) as cnt FROM tblcallevents
                 WHERE cid_id = ?",
                [$cmpid]
            )->row_array();
            $has_activity = (isset($act_count['cnt']) && $act_count['cnt'] > 0);

            if (!$has_activity) {
                $missing[] = 'required_activity:' . $act_type;
                $override_required = true;
            }
        }

        // Check required_fields on the lead row
        foreach ($required_fields as $field) {
            $val = $lead[$field] ?? null;
            if ($val === null || $val === '' || $val === '0000-00-00') {
                $missing[] = 'required_field:' . $field;
                $override_required = true;
            }
        }


        // --- Phase 2 Agent E: ADDITIVE opportunity_value gate check ---
        // If stage_gate_config.require_opp_value=1 AND lead.proposaldate >= 2026-04-01,
        // then opportunity_value must exist for this lead.
        // This check only applies for target stages with require_opp_value=1.
        $opp_gate_row = $this->db->query(
            "SELECT require_opp_value FROM stage_gate_config WHERE stage_code = ? AND active = 1 LIMIT 1",
            [$target_stage]
        )->row_array();
        $requires_opp_val = !empty($opp_gate_row) && !empty($opp_gate_row["require_opp_value"]);
        if ($requires_opp_val) {
            $pd = $lead["proposaldate"] ?? "";
            if ($pd && $pd !== "0000-00-00" && $pd >= "2026-04-01") {
                $ov_exists = $this->db->query(
                    "SELECT lead_id FROM opportunity_value WHERE lead_id = ? LIMIT 1",
                    [$lead_id]
                )->row_array();
                if (!$ov_exists) {
                    $missing[] = "required_field:opportunity_value";
                    $override_required = true;
                }
            }
        }
        // --- end Phase 2 Agent E addition ---

        $allowed = empty($missing);

        $this->_json([
            'ok'                => true,
            'allowed'           => $allowed,
            'missing'           => $missing,
            'override_required' => $override_required,
            'lead_id'           => $lead_id,
            'current_stage'     => $current_stage,
            'current_label'     => $this->_stage_labels[$current_stage] ?? 'Unknown',
            'target_stage'      => $target_stage,
            'target_label'      => $this->_stage_labels[$target_stage]  ?? 'Unknown',
            'sla_days'          => (int)$rule['sla_days'],
        ]);
    }

    // ------------------------------------------------------------------
    // POST /api/gate/override  {lead_id, target_stage, reason, by_uid}
    // ------------------------------------------------------------------
    public function override() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->_json(['ok' => false, 'error' => 'POST required'], 405);
        }
        if (!$this->_bearer_ok()) {
            $this->_json(['ok' => false, 'error' => 'Unauthorized'], 401);
        }

        $body         = $this->_post_body();
        $lead_id      = (int)($body['lead_id']      ?? 0);
        $target_stage = (int)($body['target_stage'] ?? 0);
        $reason       = trim((string)($body['reason']  ?? ''));
        $by_uid       = (int)($body['by_uid']       ?? 0);

        if ($lead_id <= 0 || $target_stage <= 0 || $by_uid <= 0 || $reason === '') {
            $this->_json(['ok' => false, 'error' => 'lead_id, target_stage, by_uid, and reason are required'], 422);
        }

        // Verify lead exists
        $exists = $this->db->query(
            "SELECT id FROM init_call WHERE id = ? LIMIT 1", [$lead_id]
        )->row_array();
        if (!$exists) {
            $this->_json(['ok' => false, 'error' => 'lead not found'], 404);
        }

        $this->db->query(
            "INSERT INTO lead_gate_override_log (lead_id, target_stage, reason, by_uid, ts)
             VALUES (?, ?, ?, ?, NOW())",
            [$lead_id, $target_stage, substr($reason, 0, 2000), $by_uid]
        );

        $insert_id = $this->db->insert_id();

        $this->_json([
            'ok'           => true,
            'override_id'  => (int)$insert_id,
            'lead_id'      => $lead_id,
            'target_stage' => $target_stage,
            'by_uid'       => $by_uid,
            'logged'       => true,
        ]);
    }
}

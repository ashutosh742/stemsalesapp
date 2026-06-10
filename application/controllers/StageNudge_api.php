<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * StageNudge_api.php  (Phase 2 - Agent F - 2026-06-08)
 *
 * E4 Stage-Triggered WhatsApp Nudges
 *
 * Manages rules for when to fire approved WhatsApp templates:
 *   - on stage-enter (from_stage -> to_stage match)
 *   - idle-past-N-days in a given stage
 *
 * DOES NOT duplicate CommOrchestrator / Whatsapp sending logic.
 * POST /api/nudge/fire queues into comm_draft_queue (existing pipeline).
 * Actual WhatsApp sends require approved Meta templates.
 *
 * Endpoints:
 *   GET  /api/nudge/rules          List all rules
 *   POST /api/nudge/rule/save      Create or update a rule
 *   GET  /api/nudge/due            Evaluate init_call vs rules -> due list (no send)
 *   POST /api/nudge/fire           Queue nudge into existing comm pipeline
 *
 * Auth: Bearer 4eBaiAT7r4zu6OK3b8evjLNia1D7RGgb0qRTuLJfUSo (or STEM_DIGEST_TOKEN env)
 * Output: ASCII only. No em/en-dashes. Rs for rupees.
 *
 * Author: STEM Phase 2 Agent F  2026-06-08
 */
class StageNudge_api extends CI_Controller {

    private $_known_token = '4eBaiAT7r4zu6OK3b8evjLNia1D7RGgb0qRTuLJfUSo';
    private $_authed_uid  = 0;

    private $_stage_labels = array(
        1 => 'Open', 2 => 'Reachout', 3 => 'Tentative',
        4 => 'Will-do-Later', 5 => 'Not-Interested', 6 => 'Positive',
        7 => 'Closure', 8 => 'OPEN RPEM', 9 => 'Very-Positive',
        10 => 'TTD-Reachout', 11 => 'WNO-Reachout', 12 => 'Positive-NAP',
        13 => 'Very-Positive-NAP', 14 => 'On-Boarded',
    );

    public function __construct() {
        parent::__construct();
        $this->load->database();
        header('Content-Type: application/json; charset=utf-8');
        $this->_ensure_table();
    }

    // -------------------------------------------------------------------------
    // TABLE BOOTSTRAP
    // -------------------------------------------------------------------------
    private function _ensure_table() {
        $this->db->query("
            CREATE TABLE IF NOT EXISTS stage_nudge_rule (
                id            INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
                from_stage    INT NULL COMMENT 'NULL = any stage',
                to_stage      INT NULL COMMENT 'NULL = not a stage-enter rule',
                idle_days     INT NULL COMMENT 'NULL = not an idle rule',
                template_code VARCHAR(80)  NOT NULL COMMENT 'comm_template_v2.template_key',
                active        TINYINT(1)  NOT NULL DEFAULT 1,
                created_at    DATETIME    NOT NULL DEFAULT CURRENT_TIMESTAMP,
                updated_at    DATETIME    NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
        ");
    }

    // -------------------------------------------------------------------------
    // GET /api/nudge/rules
    // List all nudge rules
    // -------------------------------------------------------------------------
    public function rules() {
        if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
            return $this->_json(array('ok' => false, 'error' => 'GET required'), 405);
        }
        if (!$this->_bearer_ok()) {
            return $this->_json(array('ok' => false, 'error' => 'Unauthorized'), 401);
        }

        $rows = $this->db->get('stage_nudge_rule')->result_array();
        if (empty($rows)) {
            return $this->_json(array('ok' => true, 'empty' => true, 'count' => 0, 'rules' => array()));
        }

        $out = array();
        foreach ($rows as $r) {
            $out[] = array(
                'id'            => (int) $r['id'],
                'from_stage'    => $r['from_stage'] !== null ? (int) $r['from_stage'] : null,
                'to_stage'      => $r['to_stage']   !== null ? (int) $r['to_stage']   : null,
                'idle_days'     => $r['idle_days']  !== null ? (int) $r['idle_days']  : null,
                'template_code' => $r['template_code'],
                'active'        => (bool)(int) $r['active'],
                'trigger_type'  => ($r['to_stage'] !== null) ? 'stage_enter' : 'idle',
                'created_at'    => $r['created_at'],
            );
        }
        $this->_json(array('ok' => true, 'count' => count($out), 'rules' => $out));
    }

    // -------------------------------------------------------------------------
    // POST /api/nudge/rule/save
    // Create or update a rule (id in body = update; omit = create)
    // Body params: id(opt), from_stage(opt), to_stage(opt), idle_days(opt),
    //              template_code(req), active(opt default 1)
    // -------------------------------------------------------------------------
    public function rule_save() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return $this->_json(array('ok' => false, 'error' => 'POST required'), 405);
        }
        if (!$this->_bearer_ok()) {
            return $this->_json(array('ok' => false, 'error' => 'Unauthorized'), 401);
        }

        $body = $this->_post_body();

        $template_code = isset($body['template_code']) ? trim($body['template_code']) : '';
        if (empty($template_code)) {
            return $this->_json(array('ok' => false, 'error' => 'template_code is required'), 422);
        }

        // Validate: must be stage_enter OR idle rule, not neither
        $to_stage  = isset($body['to_stage'])  && $body['to_stage']  !== '' ? (int) $body['to_stage']  : null;
        $idle_days = isset($body['idle_days']) && $body['idle_days'] !== '' ? (int) $body['idle_days'] : null;

        if ($to_stage === null && $idle_days === null) {
            return $this->_json(array('ok' => false, 'error' => 'Provide to_stage (stage-enter rule) or idle_days (idle rule) - at least one required'), 422);
        }
        if ($to_stage !== null && ($to_stage < 1 || $to_stage > 14)) {
            return $this->_json(array('ok' => false, 'error' => 'to_stage must be 1-14'), 422);
        }
        if ($idle_days !== null && $idle_days < 1) {
            return $this->_json(array('ok' => false, 'error' => 'idle_days must be >= 1'), 422);
        }

        $from_stage = isset($body['from_stage']) && $body['from_stage'] !== '' ? (int) $body['from_stage'] : null;
        $active     = isset($body['active']) ? (int)(bool)$body['active'] : 1;

        $data = array(
            'from_stage'    => $from_stage,
            'to_stage'      => $to_stage,
            'idle_days'     => $idle_days,
            'template_code' => $template_code,
            'active'        => $active,
        );

        $id = isset($body['id']) ? (int) $body['id'] : 0;
        if ($id > 0) {
            $exists = $this->db->get_where('stage_nudge_rule', array('id' => $id))->row_array();
            if (empty($exists)) {
                return $this->_json(array('ok' => false, 'error' => 'rule not found'), 404);
            }
            $this->db->where('id', $id)->update('stage_nudge_rule', $data);
            $this->_json(array('ok' => true, 'action' => 'updated', 'id' => $id));
        } else {
            $this->db->insert('stage_nudge_rule', $data);
            $new_id = $this->db->insert_id();
            $this->_json(array('ok' => true, 'action' => 'created', 'id' => $new_id));
        }
    }

    // -------------------------------------------------------------------------
    // GET /api/nudge/due
    // Evaluate active rules against init_call and return leads currently DUE
    // a nudge. Does NOT send anything.
    // Optional param: bd_uid (filter by owner)
    // -------------------------------------------------------------------------
    public function due() {
        if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
            return $this->_json(array('ok' => false, 'error' => 'GET required'), 405);
        }
        if (!$this->_bearer_ok()) {
            return $this->_json(array('ok' => false, 'error' => 'Unauthorized'), 401);
        }

        // Load active rules
        $rules = $this->db->where('active', 1)->get('stage_nudge_rule')->result_array();
        if (empty($rules)) {
            return $this->_json(array('ok' => true, 'empty' => true, 'count' => 0, 'due' => array(),
                'note' => 'No active nudge rules defined'));
        }

        $bd_uid = (int) $this->input->get('bd_uid');

        // Separate rule types
        $stage_rules = array(); // trigger on cstatus match
        $idle_rules  = array(); // trigger on idle_days in a stage

        foreach ($rules as $r) {
            if ($r['to_stage'] !== null) {
                $stage_rules[] = $r;
            }
            if ($r['idle_days'] !== null) {
                $idle_rules[] = $r;
            }
        }

        // Build query: active leads (not closed/not-interested)
        $active_stages = array(1,2,3,6,8,9,10,12,13);

        $this->db->select('ic.id as lead_id, ic.cmpid_id, cm.compname, ic.cstatus, ic.updated_at, ic.mainbd')
            ->from('init_call ic')
            ->join('company_master cm', 'ic.cmpid_id = cm.id', 'left')
            ->where_in('ic.cstatus', $active_stages);
        if ($bd_uid > 0) {
            $this->db->where('ic.mainbd', $bd_uid);
        }
        $leads = $this->db->get()->result_array();

        if (empty($leads)) {
            return $this->_json(array('ok' => true, 'empty' => true, 'count' => 0, 'due' => array(),
                'note' => 'No active leads found'));
        }

        $due = array();
        $now = time();

        foreach ($leads as $lead) {
            $cstatus     = (int) $lead['cstatus'];
            $stage_label = isset($this->_stage_labels[$cstatus]) ? $this->_stage_labels[$cstatus] : 'Unknown';
            $updated_ts  = strtotime($lead['updated_at']);
            $days_idle   = ($now - $updated_ts > 0) ? (int) floor(($now - $updated_ts) / 86400) : 0;

            // Check stage-enter rules (match on to_stage = current cstatus)
            foreach ($stage_rules as $r) {
                if ((int)$r['to_stage'] === $cstatus) {
                    $due[] = array(
                        'lead_id'       => (int) $lead['lead_id'],
                        'company'       => trim($lead['compname']),
                        'cstatus'       => $cstatus,
                        'stage_label'   => $stage_label,
                        'trigger'       => 'stage_enter',
                        'days_idle'     => $days_idle,
                        'rule_id'       => (int) $r['id'],
                        'template_code' => $r['template_code'],
                        'bd_uid'        => (int) $lead['mainbd'],
                    );
                }
            }

            // Check idle rules
            foreach ($idle_rules as $r) {
                $rule_stage = $r['from_stage'] !== null ? (int) $r['from_stage'] : null;
                $idle_threshold = (int) $r['idle_days'];
                // If from_stage is set, only match that stage; else match any stage
                $stage_match = ($rule_stage === null || $rule_stage === $cstatus);
                if ($stage_match && $days_idle >= $idle_threshold) {
                    $due[] = array(
                        'lead_id'       => (int) $lead['lead_id'],
                        'company'       => trim($lead['compname']),
                        'cstatus'       => $cstatus,
                        'stage_label'   => $stage_label,
                        'trigger'       => 'idle',
                        'days_idle'     => $days_idle,
                        'idle_threshold'=> $idle_threshold,
                        'rule_id'       => (int) $r['id'],
                        'template_code' => $r['template_code'],
                        'bd_uid'        => (int) $lead['mainbd'],
                    );
                }
            }
        }

        // De-duplicate: one entry per lead per rule
        $seen = array();
        $deduped = array();
        foreach ($due as $d) {
            $key = $d['lead_id'] . '_' . $d['rule_id'];
            if (!isset($seen[$key])) {
                $seen[$key]  = 1;
                $deduped[]   = $d;
            }
        }

        $this->_json(array(
            'ok'    => true,
            'count' => count($deduped),
            'due'   => $deduped,
            'note'  => 'This is an evaluation only. Use POST /api/nudge/fire to queue a specific nudge.',
        ));
    }

    // -------------------------------------------------------------------------
    // POST /api/nudge/fire
    // Queue a WhatsApp nudge into the existing comm_draft_queue pipeline.
    // Does NOT blast. Requires approved Meta template.
    // Body: lead_id (int, required), template_code (string, required)
    // -------------------------------------------------------------------------
    public function fire() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return $this->_json(array('ok' => false, 'error' => 'POST required'), 405);
        }
        if (!$this->_bearer_ok()) {
            return $this->_json(array('ok' => false, 'error' => 'Unauthorized'), 401);
        }

        $body = $this->_post_body();

        $lead_id       = isset($body['lead_id'])       ? (int) $body['lead_id']           : 0;
        $template_code = isset($body['template_code']) ? trim($body['template_code'])      : '';

        if ($lead_id <= 0 || empty($template_code)) {
            return $this->_json(array('ok' => false, 'error' => 'lead_id and template_code are required'), 422);
        }

        // Verify lead exists
        $lead = $this->db->select('ic.id, ic.cmpid_id, ic.mainbd, ic.cstatus, ic.dm_mobile, cm.compname')
            ->from('init_call ic')
            ->join('company_master cm', 'ic.cmpid_id = cm.id', 'left')
            ->where('ic.id', $lead_id)
            ->get()->row_array();

        if (empty($lead)) {
            return $this->_json(array('ok' => false, 'error' => 'lead not found'), 404);
        }

        // Check if WhatsApp Business Token is configured
        $wa_token = getenv('WHATSAPP_BUSINESS_TOKEN');
        $wa_ready = !empty($wa_token);

        // Check if the template is registered in comm_template_v2
        $tpl = $this->db->get_where('comm_template_v2', array('template_key' => $template_code, 'is_active' => 1))->row_array();

        // Queue into comm_draft_queue (existing pipeline).
        // We use status 'pending_review' so a human must approve before actual send.
        // This is the safe path: no auto-blast, no direct WhatsApp API call.
        $draft_data = array(
            'event_id'           => 0,   // no event log entry for manual nudge
            'cid_id'             => $lead_id,
            'owner_uid'          => (int)($lead['mainbd'] ?: 0),
            'owner_role'         => 'bd',
            'template_key'       => $template_code,
            'recipient_to_email' => '',  // WhatsApp - phone as identifier
            'recipient_to_name'  => trim($lead['compname']),
            'recipient_to_role'  => 'dm',
            'subject'            => 'WhatsApp nudge: ' . $template_code,
            'body_plain'         => 'WhatsApp template: ' . $template_code . ' | Lead: ' . $lead_id . ' | Company: ' . trim($lead['compname']) . ' | Mobile: ' . ($lead['dm_mobile'] ?: 'not set'),
            'status'             => 'pending_review',
            'ai_model'           => null,
            'created_at'         => date('Y-m-d H:i:s'),
        );
        $this->db->insert('comm_draft_queue', $draft_data);
        $draft_id = $this->db->insert_id();

        $note = 'Nudge queued as pending_review. Actual WhatsApp send requires: (1) approved Meta template, (2) WHATSAPP_BUSINESS_TOKEN configured. BD must approve draft before dispatch.';
        if (!$wa_ready) {
            $note .= ' WHATSAPP_BUSINESS_TOKEN not set - stub mode active.';
        }
        if (empty($tpl)) {
            $note .= ' Template "' . $template_code . '" not found in comm_template_v2 - register template first.';
        }

        $this->_json(array(
            'ok'                       => true,
            'queued'                   => true,
            'draft_id'                 => $draft_id,
            'lead_id'                  => $lead_id,
            'template_code'            => $template_code,
            'whatsapp_token_ready'     => $wa_ready,
            'template_registered'      => !empty($tpl),
            'note'                     => $note,
        ));
    }

    // -------------------------------------------------------------------------
    // Auth helpers (pattern from StageGate_api)
    // -------------------------------------------------------------------------
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
        $days   = array(date('Y-m-d'), date('Y-m-d', strtotime('-1 day')), date('Y-m-d', strtotime('+1 day')));
        $cands  = array();
        foreach (array('uid','cm_uid','rm_uid','bd_uid','by_uid','user_id') as $k) {
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
            $all  = array();
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
        // Fall back to $_POST
        return $_POST;
    }
}

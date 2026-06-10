<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * TriggerBuilder_api.php  (Phase 3 - Agent G - D6 - 2026-06-08)
 *
 * No-Code Trigger Builder: configure automation rules that WOULD fire
 * on lead events. Dry-evaluation only - actual execution is handled by
 * a future scheduled/recurring task (not this controller).
 *
 * Tables:
 *   trigger_rule   (id, name, when_event, condition_json, active, created_by_uid, created_ts)
 *   trigger_action (id, rule_id, action_type, action_json)
 *
 * Endpoints:
 *   GET  /api/trigger/rules                  List all active trigger rules
 *   POST /api/trigger/rule/save              Create or update a rule (+ actions)
 *   POST /api/trigger/rule/delete            Soft-delete a rule by id
 *   GET  /api/trigger/evaluate?lead_id=      Dry-run: which rules would fire?
 *
 * Bearer token required. 401 on missing token. ASCII output. Rs for rupees.
 * NOTE: Actual trigger execution is deferred to a scheduled/recurring task.
 */
class TriggerBuilder_api extends CI_Controller {

    private $_known_token = '4eBaiAT7r4zu6OK3b8evjLNia1D7RGgb0qRTuLJfUSo';

    public function __construct() {
        parent::__construct();
        $this->load->database();
        header('Content-Type: application/json; charset=utf-8');
    }

    // ------------------------------------------------------------------
    // Auth
    // ------------------------------------------------------------------
    private function _bearer_ok() {
        // rimlyproof_authunify_20260609: delegate to canonical fail-closed validator.
        // Replaces malformed if/try control flow that rejected valid Bearer tokens.
        if (function_exists('authunify_ok')) {
            return authunify_ok() ? true : false;
        }
        // Fallback: direct BearerAuth resolve (still fail-closed)
        try {
            $CI =& get_instance();
            if (!isset($CI->bearerauth)) { $CI->load->library('BearerAuth'); }
            $___ba = $CI->bearerauth->resolve();
            if (!empty($___ba['ok'])) {
                if (property_exists($this, '_authed_uid')) { $this->_authed_uid = (int)$___ba['uid']; }
                return true;
            }
        } catch (Exception $e) {}
        return false;
    }

    private function _require_auth() {
        if (!$this->_bearer_ok()) {
            http_response_code(401);
            echo json_encode(['ok' => false, 'error' => 'Unauthorized']);
            exit;
        }
    }

    private function _json($data, $code = 200) {
        http_response_code($code);
        echo json_encode($data, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
        exit;
    }

    // ------------------------------------------------------------------
    // Schema bootstrap - called lazily on first use
    // ------------------------------------------------------------------
    private function _ensure_tables() {
        $this->db->query("
            CREATE TABLE IF NOT EXISTS trigger_rule (
                id              INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
                name            VARCHAR(200) NOT NULL,
                when_event      VARCHAR(60)  NOT NULL COMMENT 'e.g. stage_enter, idle_days, value_set',
                condition_json  TEXT         NOT NULL DEFAULT '{}',
                active          TINYINT(1)   NOT NULL DEFAULT 1,
                created_by_uid  INT UNSIGNED NOT NULL DEFAULT 0,
                created_ts      DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
                INDEX idx_active (active),
                INDEX idx_event  (when_event)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
        ");
        $this->db->query("
            CREATE TABLE IF NOT EXISTS trigger_action (
                id          INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
                rule_id     INT UNSIGNED NOT NULL,
                action_type VARCHAR(60)  NOT NULL COMMENT 'e.g. create_task, send_nudge, notify',
                action_json TEXT         NOT NULL DEFAULT '{}',
                INDEX idx_rule (rule_id)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
        ");
    }

    // ------------------------------------------------------------------
    // GET /api/trigger/rules
    // ------------------------------------------------------------------
    public function rules() {
        $this->_require_auth();
        $this->_ensure_tables();

        $rows = $this->db->query(
            "SELECT r.*, GROUP_CONCAT(a.id) as action_ids
             FROM trigger_rule r
             LEFT JOIN trigger_action a ON a.rule_id = r.id
             WHERE r.active = 1
             GROUP BY r.id
             ORDER BY r.id ASC"
        )->result_array();

        // Expand actions per rule
        $out = [];
        foreach ($rows as $row) {
            $actions = [];
            if ($row['action_ids']) {
                $acts = $this->db->query(
                    "SELECT * FROM trigger_action WHERE rule_id = ?",
                    [(int)$row['id']]
                )->result_array();
                foreach ($acts as $a) {
                    $a['action_json'] = json_decode($a['action_json'], true) ?: [];
                    $actions[] = $a;
                }
            }
            unset($row['action_ids']);
            $row['condition_json'] = json_decode($row['condition_json'], true) ?: [];
            $row['actions'] = $actions;
            $out[] = $row;
        }

        if (empty($out)) {
            $this->_json(['ok' => true, 'empty' => true, 'rules' => []]);
        }
        $this->_json(['ok' => true, 'rules' => $out, 'count' => count($out)]);
    }

    // ------------------------------------------------------------------
    // POST /api/trigger/rule/save
    // Body: { name, when_event, condition_json, active?, created_by_uid?, actions[] }
    //   actions[]: [{ action_type, action_json }]
    // ------------------------------------------------------------------
    public function rule_save() {
        $this->_require_auth();
        $this->_ensure_tables();

        $body = json_decode(file_get_contents('php://input'), true) ?: [];
        $id         = isset($body['id'])   ? (int)$body['id'] : 0;
        $name       = isset($body['name']) ? trim($body['name']) : '';
        $when_event = isset($body['when_event']) ? trim($body['when_event']) : '';
        $cond_raw   = isset($body['condition_json']) ? $body['condition_json'] : [];
        $active     = isset($body['active']) ? (int)$body['active'] : 1;
        $by_uid     = isset($body['created_by_uid']) ? (int)$body['created_by_uid'] : 0;
        $actions    = isset($body['actions']) && is_array($body['actions']) ? $body['actions'] : [];

        if (!$name || !$when_event) {
            $this->_json(['ok' => false, 'error' => 'name and when_event required'], 400);
        }

        $cond_json = json_encode(is_array($cond_raw) ? $cond_raw : []);

        if ($id) {
            $this->db->query(
                "UPDATE trigger_rule SET name=?, when_event=?, condition_json=?, active=? WHERE id=?",
                [$name, $when_event, $cond_json, $active, $id]
            );
            // Replace actions
            $this->db->query("DELETE FROM trigger_action WHERE rule_id = ?", [$id]);
        } else {
            $this->db->query(
                "INSERT INTO trigger_rule (name, when_event, condition_json, active, created_by_uid, created_ts)
                 VALUES (?, ?, ?, ?, ?, NOW())",
                [$name, $when_event, $cond_json, $active, $by_uid]
            );
            $id = $this->db->insert_id();
        }

        foreach ($actions as $a) {
            $atype = isset($a['action_type']) ? trim($a['action_type']) : '';
            $ajson = json_encode(isset($a['action_json']) && is_array($a['action_json']) ? $a['action_json'] : []);
            if ($atype) {
                $this->db->query(
                    "INSERT INTO trigger_action (rule_id, action_type, action_json) VALUES (?, ?, ?)",
                    [$id, $atype, $ajson]
                );
            }
        }

        $this->_json(['ok' => true, 'rule_id' => $id, 'actions_saved' => count($actions)]);
    }

    // ------------------------------------------------------------------
    // POST /api/trigger/rule/delete
    // Body: { id }  -> soft delete (active=0)
    // ------------------------------------------------------------------
    public function rule_delete() {
        $this->_require_auth();
        $this->_ensure_tables();

        $body = json_decode(file_get_contents('php://input'), true) ?: [];
        $id = isset($body['id']) ? (int)$body['id'] : 0;
        if (!$id) {
            $this->_json(['ok' => false, 'error' => 'id required'], 400);
        }

        $this->db->query("UPDATE trigger_rule SET active = 0 WHERE id = ?", [$id]);
        $affected = $this->db->affected_rows();

        $this->_json(['ok' => true, 'soft_deleted' => $affected > 0, 'rule_id' => $id]);
    }

    // ------------------------------------------------------------------
    // GET /api/trigger/evaluate?lead_id=
    // Dry-run: returns which active rules WOULD fire for the lead.
    // No side effects. Actual execution is a future scheduled/recurring task.
    // ------------------------------------------------------------------
    public function evaluate() {
        $this->_require_auth();
        $this->_ensure_tables();

        $lead_id = (int)$this->input->get('lead_id');
        if (!$lead_id) {
            $this->_json(['ok' => false, 'error' => 'lead_id required'], 400);
        }

        // Fetch lead data
        $lead = $this->db->query(
            "SELECT ic.id, ic.cstatus, ic.mainbd, ic.cluster_id, ic.proposaldate,
                    ic.cmpid_id, ic.pstadt,
                    DATEDIFF(NOW(), COALESCE(ic.pstadt, ic.proposaldate, NOW())) AS idle_days
             FROM init_call ic
             WHERE ic.id = ? LIMIT 1",
            [$lead_id]
        )->row_array();

        if (!$lead) {
            $this->_json(['ok' => true, 'empty' => true, 'lead_id' => $lead_id,
                          'would_fire' => [], 'note' => 'lead not found']);
        }

        // Get value if exists (Phase 2 table may not exist yet - defensive)
        $lead_value = null;
        try {
            $vrow = $this->db->query(
                "SELECT value_rs FROM opportunity_value WHERE lead_id = ? LIMIT 1",
                [$lead_id]
            )->row_array();
            if ($vrow) $lead_value = (float)$vrow['value_rs'];
        } catch (Exception $e) { /* table may not exist yet */ }

        // Fetch all active rules
        $rules = $this->db->query(
            "SELECT r.*, GROUP_CONCAT(a.action_type ORDER BY a.id SEPARATOR ',') AS action_types
             FROM trigger_rule r
             LEFT JOIN trigger_action a ON a.rule_id = r.id
             WHERE r.active = 1
             GROUP BY r.id"
        )->result_array();

        $would_fire = [];

        foreach ($rules as $rule) {
            $cond   = json_decode($rule['condition_json'], true) ?: [];
            $fires  = false;
            $reason = '';

            switch ($rule['when_event']) {
                case 'stage_enter':
                    // condition: { "stage": 6 }
                    $target = isset($cond['stage']) ? (int)$cond['stage'] : -1;
                    if ($target < 0 || $target === (int)$lead['cstatus']) {
                        $fires  = true;
                        $reason = 'lead cstatus=' . $lead['cstatus'] . ' matches stage_enter condition';
                    }
                    break;

                case 'idle_days':
                    // condition: { "min_days": 7 }
                    $min = isset($cond['min_days']) ? (int)$cond['min_days'] : 7;
                    if ((int)$lead['idle_days'] >= $min) {
                        $fires  = true;
                        $reason = 'idle_days=' . $lead['idle_days'] . ' >= min=' . $min;
                    }
                    break;

                case 'value_set':
                    // condition: { "min_rs": 100000 }
                    if ($lead_value !== null) {
                        $min_rs = isset($cond['min_rs']) ? (float)$cond['min_rs'] : 0;
                        if ($lead_value >= $min_rs) {
                            $fires  = true;
                            $reason = 'opportunity_value Rs ' . $lead_value . ' >= min Rs ' . $min_rs;
                        }
                    }
                    break;

                default:
                    // Unknown event type - evaluate as not firing
                    $reason = 'unknown when_event: ' . $rule['when_event'];
                    break;
            }

            if ($fires) {
                $acts = $this->db->query(
                    "SELECT action_type, action_json FROM trigger_action WHERE rule_id = ?",
                    [(int)$rule['id']]
                )->result_array();
                foreach ($acts as &$a) {
                    $a['action_json'] = json_decode($a['action_json'], true) ?: [];
                }
                unset($a);

                $would_fire[] = [
                    'rule_id'    => (int)$rule['id'],
                    'rule_name'  => $rule['name'],
                    'when_event' => $rule['when_event'],
                    'reason'     => $reason,
                    'actions'    => $acts,
                ];
            }
        }

        $this->_json([
            'ok'          => true,
            'lead_id'     => $lead_id,
            'lead_cstatus'=> (int)$lead['cstatus'],
            'idle_days'   => (int)$lead['idle_days'],
            'dry_run'     => true,
            'note'        => 'Dry evaluation only - no side effects. Actual execution is a future scheduled/recurring task.',
            'would_fire'  => $would_fire,
            'fire_count'  => count($would_fire),
        ]);
    }
}

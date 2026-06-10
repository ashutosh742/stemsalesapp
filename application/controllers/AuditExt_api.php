<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * AuditExt_api - Phase 1 Agent B, I2 Audit Hook Helper
 * Created: 2026-06-08 (additive only)
 *
 * This is a THIN OPT-IN helper. Other new Phase 1 controllers may POST here
 * to record structured before/after snapshots. It does NOT retrofit existing
 * controllers (AuditApi, company_log, init_call_contact_history remain unchanged).
 *
 * Endpoints:
 *   POST /api/audit/log        - write one audit event (entity, entity_id, action, before_json, after_json, by_uid)
 *   GET  /api/audit/recent     - fetch recent events (entity, entity_id)
 *
 * Auth: Bearer 4eBaiAT7r4zu6OK3b8evjLNia1D7RGgb0qRTuLJfUSo
 * Table: audit_event_ext (new; separate from company_log / init_call_contact_history)
 * Rules: ASCII only, empty -> {ok:true, empty:true}
 */
class AuditExt_api extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->database();
    }

    // -------------------------------------------------------------------------
    // Auth helper
    // -------------------------------------------------------------------------
    private function _bearer() {
        if (function_exists('authunify_ok') && authunify_ok()) { return true; } // rimlyproof_authunify_20260609

        $expected = '4eBaiAT7r4zu6OK3b8evjLNia1D7RGgb0qRTuLJfUSo';
        $cfg_file = APPPATH . 'config/digest_token.txt';
        if (file_exists($cfg_file)) {
            $t = trim(file_get_contents($cfg_file));
            if ($t) { $expected = $t; }
        }

        $header = '';
        if (!empty($_SERVER['HTTP_AUTHORIZATION'])) {
            $header = $_SERVER['HTTP_AUTHORIZATION'];
        } elseif (!empty($_SERVER['REDIRECT_HTTP_AUTHORIZATION'])) {
            $header = $_SERVER['REDIRECT_HTTP_AUTHORIZATION'];
        } elseif (function_exists('getallheaders')) {
            foreach (getallheaders() as $k => $v) {
                if (strcasecmp($k, 'Authorization') === 0) { $header = $v; break; }
            }
        }

        $provided = trim(str_replace(['Bearer ', 'Bearer'], '', $header));
        if (!$provided || $provided !== $expected) {
            $this->output->set_status_header(401)
                ->set_content_type('application/json')
                ->set_output(json_encode(['ok' => false, 'error' => 'unauthorized']));
            return false;
        }
        return true;
    }

    private function _json($data, $status = 200) {
        $this->output->set_status_header($status)
            ->set_content_type('application/json')
            ->set_output(json_encode($data));
    }

    private function _input_json() {
        $raw = file_get_contents('php://input');
        if ($raw && $raw[0] === '{') { return json_decode($raw, true) ?: []; }
        return $_POST ?: [];
    }

    // -------------------------------------------------------------------------
    // POST /api/audit/log
    // Required: entity, entity_id, action, by_uid
    // Optional: before_json, after_json
    // -------------------------------------------------------------------------
    public function log() {
        if (!$this->_bearer()) return;

        $in          = $this->_input_json();
        $entity      = isset($in['entity'])    ? trim($in['entity'])    : '';
        $entity_id   = isset($in['entity_id']) ? (int)$in['entity_id'] : 0;
        $action      = isset($in['action'])    ? trim($in['action'])    : '';
        $by_uid      = isset($in['by_uid'])    ? (int)$in['by_uid']    : 0;

        $before_raw  = isset($in['before_json']) ? $in['before_json'] : null;
        $after_raw   = isset($in['after_json'])  ? $in['after_json']  : null;

        if (!$entity || !$entity_id || !$action || !$by_uid) {
            $this->_json(['ok' => false, 'error' => 'entity, entity_id, action, by_uid are required'], 422);
            return;
        }

        // Normalise before/after to JSON strings
        $before_str = null;
        $after_str  = null;

        if ($before_raw !== null) {
            $before_str = is_array($before_raw) ? json_encode($before_raw) : (string)$before_raw;
        }
        if ($after_raw !== null) {
            $after_str = is_array($after_raw) ? json_encode($after_raw) : (string)$after_raw;
        }

        $this->db->query(
            "INSERT INTO audit_event_ext (entity, entity_id, action, before_json, after_json, by_uid, ts)
             VALUES (?, ?, ?, ?, ?, ?, NOW())",
            [$entity, $entity_id, $action, $before_str, $after_str, $by_uid]
        );
        $new_id = $this->db->insert_id();

        $this->_json([
            'ok'           => true,
            'action'       => 'audit_logged',
            'id'           => (int)$new_id,
            'entity'       => $entity,
            'entity_id'    => $entity_id,
            'generated_at' => date('c'),
        ]);
    }

    // -------------------------------------------------------------------------
    // GET /api/audit/recent?entity=&entity_id=
    // Both filters are optional; limit defaults to 50
    // -------------------------------------------------------------------------
    public function recent() {
        if (!$this->_bearer()) return;

        $entity    = trim((string)$this->input->get('entity'));
        $entity_id = (int)$this->input->get('entity_id');
        $limit     = (int)$this->input->get('limit');
        if ($limit <= 0 || $limit > 200) { $limit = 50; }

        $where  = ['1=1'];
        $params = [];

        if ($entity !== '') {
            $where[]  = 'entity = ?';
            $params[] = $entity;
        }
        if ($entity_id > 0) {
            $where[]  = 'entity_id = ?';
            $params[] = $entity_id;
        }

        $where_sql = implode(' AND ', $where);

        $params[] = $limit;

        $rows = $this->db->query(
            "SELECT id, entity, entity_id, action, before_json, after_json, by_uid, ts
             FROM audit_event_ext
             WHERE {$where_sql}
             ORDER BY ts DESC
             LIMIT ?",
            $params
        )->result_array();

        if (empty($rows)) {
            $this->_json([
                'ok'           => true,
                'empty'        => true,
                'rows'         => [],
                'count'        => 0,
                'filters'      => ['entity' => $entity ?: null, 'entity_id' => $entity_id ?: null],
                'note'         => 'audit_event_ext is opt-in. Existing audit trails are in company_log and init_call_contact_history (see AuditApi /api/audit/field_history).',
                'generated_at' => date('c'),
            ]);
            return;
        }

        foreach ($rows as &$r) {
            $r['id']        = (int)$r['id'];
            $r['entity_id'] = (int)$r['entity_id'];
            $r['by_uid']    = (int)$r['by_uid'];

            // Decode JSON blobs for convenience; keep raw if decode fails
            if ($r['before_json']) {
                $d = json_decode($r['before_json'], true);
                $r['before'] = is_array($d) ? $d : $r['before_json'];
            } else {
                $r['before'] = null;
            }
            if ($r['after_json']) {
                $d = json_decode($r['after_json'], true);
                $r['after'] = is_array($d) ? $d : $r['after_json'];
            } else {
                $r['after'] = null;
            }
        }
        unset($r);

        $this->_json([
            'ok'           => true,
            'empty'        => false,
            'rows'         => $rows,
            'count'        => count($rows),
            'filters'      => ['entity' => $entity ?: null, 'entity_id' => $entity_id ?: null],
            'generated_at' => date('c'),
        ]);
    }
}

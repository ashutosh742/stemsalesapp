<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * SavedSegment_api - Phase 1 Agent B, A5 Saved Smart Lists
 * Created: 2026-06-08 (additive only)
 *
 * Endpoints:
 *   GET  /api/segment/list?owner_uid=  - list saved segments for a user
 *   POST /api/segment/save             - create or update a segment
 *   POST /api/segment/delete           - soft-delete via active flag
 *
 * Auth: Bearer 4eBaiAT7r4zu6OK3b8evjLNia1D7RGgb0qRTuLJfUSo
 * Table: saved_segment
 * filter_json stores serialised lead filters: cluster, stage, partner_type, etc.
 * Rules: ASCII only, empty -> {ok:true, empty:true}
 */
class SavedSegment_api extends CI_Controller {

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
    // GET /api/segment/list?owner_uid=
    // -------------------------------------------------------------------------
    public function list_index() {
        if (!$this->_bearer()) return;

        $owner_uid = (int) $this->input->get('owner_uid');

        $where  = ['active = 1'];
        $params = [];

        if ($owner_uid > 0) {
            $where[]  = 'owner_uid = ?';
            $params[] = $owner_uid;
        }

        $where_sql = implode(' AND ', $where);

        $rows = $this->db->query(
            "SELECT id, owner_uid, name, filter_json, created_ts, updated_ts
             FROM saved_segment
             WHERE {$where_sql}
             ORDER BY updated_ts DESC
             LIMIT 200",
            $params
        )->result_array();

        if (empty($rows)) {
            $this->_json([
                'ok'           => true,
                'empty'        => true,
                'rows'         => [],
                'count'        => 0,
                'owner_uid'    => $owner_uid,
                'generated_at' => date('c'),
            ]);
            return;
        }

        // Decode filter_json for convenience; keep raw string too
        foreach ($rows as &$r) {
            $r['id']        = (int)$r['id'];
            $r['owner_uid'] = (int)$r['owner_uid'];
            $decoded = json_decode($r['filter_json'], true);
            $r['filters']   = is_array($decoded) ? $decoded : [];
        }
        unset($r);

        $this->_json([
            'ok'           => true,
            'empty'        => false,
            'rows'         => $rows,
            'count'        => count($rows),
            'owner_uid'    => $owner_uid,
            'generated_at' => date('c'),
        ]);
    }

    // -------------------------------------------------------------------------
    // POST /api/segment/save
    // Required: name, filter_json (JSON string or object), owner_uid
    // Optional: id (for update)
    // -------------------------------------------------------------------------
    public function save() {
        if (!$this->_bearer()) return;

        $in         = $this->_input_json();
        $id         = isset($in['id']) ? (int)$in['id'] : 0;
        $owner_uid  = isset($in['owner_uid']) ? (int)$in['owner_uid'] : 0;
        $name       = isset($in['name']) ? trim($in['name']) : '';
        $filter_raw = isset($in['filter_json']) ? $in['filter_json'] : null;

        if (!$owner_uid || !$name || $filter_raw === null) {
            $this->_json(['ok' => false, 'error' => 'owner_uid, name, filter_json are required'], 422);
            return;
        }

        // Normalise filter_json to a JSON string
        if (is_array($filter_raw)) {
            $filter_str = json_encode($filter_raw);
        } elseif (is_string($filter_raw)) {
            // Validate it is valid JSON
            $decoded = json_decode($filter_raw, true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                $this->_json(['ok' => false, 'error' => 'filter_json must be valid JSON'], 422);
                return;
            }
            $filter_str = $filter_raw;
        } else {
            $this->_json(['ok' => false, 'error' => 'filter_json must be a JSON string or object'], 422);
            return;
        }

        if ($id > 0) {
            $existing = $this->db->query(
                "SELECT id FROM saved_segment WHERE id = ? AND owner_uid = ? AND active = 1",
                [$id, $owner_uid]
            )->row_array();
            if (!$existing) {
                $this->_json(['ok' => false, 'error' => 'segment not found or does not belong to owner_uid'], 404);
                return;
            }
            $this->db->query(
                "UPDATE saved_segment SET name=?, filter_json=?, updated_ts=NOW() WHERE id = ?",
                [$name, $filter_str, $id]
            );
            $this->_json(['ok' => true, 'action' => 'updated', 'id' => $id, 'generated_at' => date('c')]);
        } else {
            $this->db->query(
                "INSERT INTO saved_segment (owner_uid, name, filter_json, created_ts) VALUES (?, ?, ?, NOW())",
                [$owner_uid, $name, $filter_str]
            );
            $new_id = $this->db->insert_id();
            $this->_json(['ok' => true, 'action' => 'created', 'id' => (int)$new_id, 'generated_at' => date('c')]);
        }
    }

    // -------------------------------------------------------------------------
    // POST /api/segment/delete  (soft delete via active=0)
    // Required: id, owner_uid
    // -------------------------------------------------------------------------
    public function delete() {
        if (!$this->_bearer()) return;

        $in        = $this->_input_json();
        $id        = isset($in['id']) ? (int)$in['id'] : 0;
        $owner_uid = isset($in['owner_uid']) ? (int)$in['owner_uid'] : 0;

        if (!$id || !$owner_uid) {
            $this->_json(['ok' => false, 'error' => 'id and owner_uid are required'], 422);
            return;
        }

        $existing = $this->db->query(
            "SELECT id FROM saved_segment WHERE id = ? AND owner_uid = ? AND active = 1",
            [$id, $owner_uid]
        )->row_array();
        if (!$existing) {
            $this->_json(['ok' => false, 'error' => 'segment not found or already deleted'], 404);
            return;
        }

        $this->db->query(
            "UPDATE saved_segment SET active = 0, updated_ts = NOW() WHERE id = ?",
            [$id]
        );

        $this->_json(['ok' => true, 'action' => 'deleted', 'id' => $id, 'generated_at' => date('c')]);
    }
}

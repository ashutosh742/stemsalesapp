<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Push_api - Phase 1 Agent D, Mobile Push Token Registry
 * Created: 2026-06-08 (additive only)
 *
 * Endpoints:
 *   POST /api/push/register   - upsert device token
 *   POST /api/push/unregister - deactivate device token
 *   GET  /api/push/tokens     - list active tokens for a user (debug)
 *
 * Auth: Bearer 4eBaiAT7r4zu6OK3b8evjLNia1D7RGgb0qRTuLJfUSo
 * Table: push_device_token
 * Rules: ASCII only, no em/en-dashes, additive only
 */
class Push_api extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->database();
    }

    // -------------------------------------------------------------------------
    // Auth helper - 401 if no/bad bearer
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
        if ($raw && isset($raw[0]) && $raw[0] === '{') { return json_decode($raw, true) ?: []; }
        return $_POST ?: [];
    }

    // -------------------------------------------------------------------------
    // POST /api/push/register
    // Required: user_uid, expo_push_token, platform
    // Optional: device_info
    // Upserts: on duplicate expo_push_token, update user_uid/platform/device_info/active/updated_ts
    // Returns: {"ok":true,"registered":true,"id":N}
    // -------------------------------------------------------------------------
    public function register() {
        if (!$this->_bearer()) return;

        $in              = $this->_input_json();
        $user_uid        = isset($in['user_uid'])        ? (int)$in['user_uid']             : 0;
        $expo_push_token = isset($in['expo_push_token']) ? trim((string)$in['expo_push_token']) : '';
        $platform        = isset($in['platform'])        ? trim((string)$in['platform'])       : '';
        $device_info     = isset($in['device_info'])     ? trim((string)$in['device_info'])    : null;

        if ($user_uid <= 0 || $expo_push_token === '' || $platform === '') {
            $this->_json(['ok' => false, 'error' => 'user_uid, expo_push_token, platform are required'], 422);
            return;
        }

        // Upsert: insert new or update existing token record
        $this->db->query(
            "INSERT INTO push_device_token
               (user_uid, expo_push_token, platform, device_info, active, created_ts, updated_ts)
             VALUES (?, ?, ?, ?, 1, NOW(), NOW())
             ON DUPLICATE KEY UPDATE
               user_uid    = VALUES(user_uid),
               platform    = VALUES(platform),
               device_info = VALUES(device_info),
               active      = 1,
               updated_ts  = NOW()",
            [$user_uid, $expo_push_token, $platform, $device_info]
        );

        // Fetch the id (works for both insert and update paths)
        $row = $this->db->query(
            "SELECT id FROM push_device_token WHERE expo_push_token = ? LIMIT 1",
            [$expo_push_token]
        )->row_array();

        $this->_json(['ok' => true, 'registered' => true, 'id' => (int)$row['id']]);
    }

    // -------------------------------------------------------------------------
    // POST /api/push/unregister
    // Required: expo_push_token
    // Sets active=0 for the given token.
    // Returns: {"ok":true}
    // -------------------------------------------------------------------------
    public function unregister() {
        if (!$this->_bearer()) return;

        $in              = $this->_input_json();
        $expo_push_token = isset($in['expo_push_token']) ? trim((string)$in['expo_push_token']) : '';

        if ($expo_push_token === '') {
            $this->_json(['ok' => false, 'error' => 'expo_push_token is required'], 422);
            return;
        }

        $this->db->query(
            "UPDATE push_device_token SET active = 0, updated_ts = NOW() WHERE expo_push_token = ?",
            [$expo_push_token]
        );

        $this->_json(['ok' => true]);
    }

    // -------------------------------------------------------------------------
    // GET /api/push/tokens?user_uid=
    // Lists active tokens for a user (debug endpoint).
    // Empty result: {"ok":true,"empty":true,"rows":[]}
    // -------------------------------------------------------------------------
    public function tokens() {
        if (!$this->_bearer()) return;

        $user_uid = (int) $this->input->get('user_uid');
        if ($user_uid <= 0) {
            $this->_json(['ok' => false, 'error' => 'user_uid is required'], 422);
            return;
        }

        $rows = $this->db->query(
            "SELECT id, user_uid, expo_push_token, platform, device_info, active, created_ts, updated_ts
             FROM push_device_token
             WHERE user_uid = ? AND active = 1
             ORDER BY updated_ts DESC",
            [$user_uid]
        )->result_array();

        if (empty($rows)) {
            $this->_json(['ok' => true, 'empty' => true, 'rows' => []]);
            return;
        }

        foreach ($rows as &$r) {
            $r['id']       = (int)$r['id'];
            $r['user_uid'] = (int)$r['user_uid'];
            $r['active']   = (int)$r['active'];
        }
        unset($r);

        $this->_json(['ok' => true, 'empty' => false, 'rows' => $rows, 'count' => count($rows)]);
    }
}

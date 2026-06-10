<?php
/**
 * Offline_sync (Controller)
 *
 * REST endpoints for the mobile offline cache (Migration 033).
 * All endpoints require Bearer token authentication via the existing
 * stem_auth_helper pattern.
 *
 * Routes to add to application/config/routes.php:
 *   $route['api/offline/snapshot']          = 'offline_sync/snapshot';
 *   $route['api/offline/sync']              = 'offline_sync/sync_batch';
 *   $route['api/offline/conflicts']         = 'offline_sync/conflicts';
 *   $route['api/offline/conflict/resolve']  = 'offline_sync/conflict_resolve';
 *   $route['api/offline/device/register']   = 'offline_sync/device_register';
 *
 * CodeIgniter 3 controller. Extends CI_Controller.
 * Base URL: https://stemapp.in
 * Staging only - never run on production.
 *
 * Author: STEM ops
 * Date: 2026-05-19
 * Migration: 033
 */

defined('BASEPATH') OR exit('No direct script access allowed');

class Offline_sync extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('Offline_sync_model', 'sync_model');
        $this->load->helper('url');
    }

    // -------------------------------------------------------------------------
    // GATE HELPER
    // Validates the Bearer token and returns the uid from the session or token.
    // Returns false and sends 401 if invalid.
    // Mirrors the pattern used in existing STEM controllers.
    // -------------------------------------------------------------------------
    private function _gate()
    {
        $auth_header = $this->input->get_request_header('Authorization', TRUE);
        if (!$auth_header || strpos($auth_header, 'Bearer ') !== 0) {
            $this->_json(['ok' => false, 'error' => 'MISSING_TOKEN'], 401);
            return false;
        }

        // rimlyproof_authunify_20260609: validate via the shared resolver
        // (digest token OR per-user login token). The missing stem_auth helper
        // (migration 022) was never shipped; authunify is the single source of truth.
        if (!(function_exists('authunify_ok') && authunify_ok())) {
            $this->_json(['ok' => false, 'error' => 'INVALID_TOKEN'], 401);
            return false;
        }
        $uid = function_exists('authunify_uid') ? (int)authunify_uid() : 0;
        if ($uid <= 0) {
            // master/digest token (uid 0): allow caller to pass an explicit uid.
            $uid = (int)$this->input->get('uid');
        }
        return array('uid' => $uid, 'role' => 'bd');
    }

    // -------------------------------------------------------------------------
    // GET /api/offline/snapshot?since=<ISO_DATETIME>&page=<int>
    //
    // Returns a full snapshot (no since param) or a delta snapshot (since param
    // present) of leads, today plan, and pending tasks for the calling BD.
    // CM Anjali (uid 12) receives combined snapshot for all pilot BDs.
    //
    // Headers required:
    //   Authorization: Bearer <token>
    //   X-Device-Id: <device_id>
    //   X-App-Version: <semver>
    // -------------------------------------------------------------------------
    public function snapshot()
    {
        if ($this->input->method() !== 'get') {
            $this->_json(['ok' => false, 'error' => 'METHOD_NOT_ALLOWED'], 405);
            return;
        }

        $user = $this->_gate();
        if (!$user) return;

        $uid   = (int) $user['uid'];
        $since = $this->input->get('since');
        $page  = (int) ($this->input->get('page') ?: 0);

        // Check feature flag: offline_enabled must be 1.
        $this->load->database();
        $flag = $this->db->select('flag_value')
                         ->where('flag_key', 'offline_enabled')
                         ->get('feature_flag')
                         ->row_array();
        if (!$flag || (int) $flag['flag_value'] !== 1) {
            $this->_json(['ok' => false, 'error' => 'OFFLINE_NOT_ENABLED'], 403);
            return;
        }

        if ($since) {
            $data = $this->sync_model->build_delta_snapshot($uid, $since);
        } else {
            $data = $this->sync_model->build_full_snapshot($uid, $page);
        }

        $this->_json(['ok' => true, 'data' => $data]);
    }

    // -------------------------------------------------------------------------
    // POST /api/offline/sync
    //
    // Receives a batch of queued ops from the device sync worker.
    // Body (JSON):
    //   {
    //     "device_id": "abc123",
    //     "app_version": "2.4.1",
    //     "ops": [
    //       {
    //         "queue_id": "<uuid>",
    //         "op_type": "create|update|delete",
    //         "table_name": "init_call|tblcallevents|mom_drafts",
    //         "row_id": <int or null>,
    //         "payload": { <changed fields only> },
    //         "client_ts": "2026-05-25T09:14:22.000Z"
    //       },
    //       ...
    //     ]
    //   }
    //
    // Returns per-item results with status applied|conflict|rejected.
    // -------------------------------------------------------------------------
    public function sync_batch()
    {
        if ($this->input->method() !== 'post') {
            $this->_json(['ok' => false, 'error' => 'METHOD_NOT_ALLOWED'], 405);
            return;
        }

        $user = $this->_gate();
        if (!$user) return;

        $uid  = (int) $user['uid'];
        $body = json_decode($this->input->raw_input_stream, true);

        if (!is_array($body) || empty($body['ops'])) {
            $this->_json(['ok' => false, 'error' => 'MISSING_OPS_ARRAY'], 400);
            return;
        }

        $device_id   = isset($body['device_id'])   ? (string) $body['device_id']   : 'unknown';
        $app_version = isset($body['app_version']) ? (string) $body['app_version'] : '';
        $ops         = (array) $body['ops'];

        // Cap batch size at 100 ops per call.
        if (count($ops) > 100) {
            $this->_json(['ok' => false, 'error' => 'BATCH_TOO_LARGE_MAX_100'], 400);
            return;
        }

        $result = $this->sync_model->apply_queue_batch($uid, $device_id, $app_version, $ops);
        $this->_json(['ok' => true, 'result' => $result]);
    }

    // -------------------------------------------------------------------------
    // GET /api/offline/conflicts?uid=<int>
    //
    // Returns all unresolved conflict rows for a given uid.
    // Calling user must be the uid or the CM of that BD.
    // -------------------------------------------------------------------------
    public function conflicts()
    {
        if ($this->input->method() !== 'get') {
            $this->_json(['ok' => false, 'error' => 'METHOD_NOT_ALLOWED'], 405);
            return;
        }

        $user = $this->_gate();
        if (!$user) return;

        $caller_uid  = (int) $user['uid'];
        $target_uid  = (int) ($this->input->get('uid') ?: $caller_uid);

        // Calling user must be the target or must be the CM of target.
        if ($caller_uid !== $target_uid) {
            if (!$this->_is_manager_of($caller_uid, $target_uid)) {
                $this->_json(['ok' => false, 'error' => 'FORBIDDEN'], 403);
                return;
            }
        }

        $rows = $this->sync_model->get_conflicts($target_uid);
        $this->_json(['ok' => true, 'conflicts' => $rows, 'count' => count($rows)]);
    }

    // -------------------------------------------------------------------------
    // POST /api/offline/conflict/resolve
    //
    // Body (JSON or form-encoded):
    //   { "queue_id": "<uuid>", "action": "accept_server|escalate" }
    //
    // accept_server: marks the conflict resolved, server value stands.
    // escalate: flags for CM review, adds note.
    // -------------------------------------------------------------------------
    public function conflict_resolve()
    {
        if ($this->input->method() !== 'post') {
            $this->_json(['ok' => false, 'error' => 'METHOD_NOT_ALLOWED'], 405);
            return;
        }

        $user = $this->_gate();
        if (!$user) return;

        $uid = (int) $user['uid'];

        // Accept JSON or form-encoded body.
        $body = json_decode($this->input->raw_input_stream, true);
        if (!$body) {
            $body = $this->input->post();
        }

        $queue_id = isset($body['queue_id']) ? (string) $body['queue_id'] : '';
        $action   = isset($body['action'])   ? (string) $body['action']   : 'accept_server';

        if (empty($queue_id)) {
            $this->_json(['ok' => false, 'error' => 'MISSING_QUEUE_ID'], 400);
            return;
        }

        $result = $this->sync_model->resolve_conflict($uid, $queue_id, $action);

        if (!$result['ok']) {
            $this->_json(['ok' => false, 'error' => $result['reason']], 404);
            return;
        }

        $this->_json(['ok' => true, 'result' => $result]);
    }

    // -------------------------------------------------------------------------
    // POST /api/offline/device/register
    //
    // Called once per device on first launch after deploy.
    // Body (JSON):
    //   { "device_id": "abc123", "app_version": "2.4.0" }
    //
    // Upserts the device into offline_device_registry.
    // -------------------------------------------------------------------------
    public function device_register()
    {
        if ($this->input->method() !== 'post') {
            $this->_json(['ok' => false, 'error' => 'METHOD_NOT_ALLOWED'], 405);
            return;
        }

        $user = $this->_gate();
        if (!$user) return;

        $uid  = (int) $user['uid'];
        $body = json_decode($this->input->raw_input_stream, true);
        if (!$body) {
            $body = $this->input->post();
        }

        $device_id   = isset($body['device_id'])   ? (string) $body['device_id']   : '';
        $app_version = isset($body['app_version']) ? (string) $body['app_version'] : '';

        if (empty($device_id)) {
            $this->_json(['ok' => false, 'error' => 'MISSING_DEVICE_ID'], 400);
            return;
        }

        $this->load->database();
        $existing = $this->db->where('uid', $uid)
                             ->where('device_id', $device_id)
                             ->get('offline_device_registry')
                             ->row_array();

        if ($existing) {
            $this->db->where('id', $existing['id'])->update('offline_device_registry', [
                'app_version' => $app_version,
                'active'      => 1,
                'updated_at'  => date('Y-m-d H:i:s'),
            ]);
        } else {
            $this->db->insert('offline_device_registry', [
                'uid'         => $uid,
                'device_id'   => $device_id,
                'app_version' => $app_version,
                'active'      => 1,
            ]);
        }

        $this->_json(['ok' => true, 'registered' => true, 'uid' => $uid, 'device_id' => $device_id]);
    }

    // =========================================================================
    // PRIVATE HELPERS
    // =========================================================================

    private function _json($data, $status = 200)
    {
        $this->output
             ->set_status_header($status)
             ->set_content_type('application/json', 'utf-8')
             ->set_output(json_encode($data, JSON_UNESCAPED_UNICODE));
    }

    private function _is_manager_of($manager_uid, $bd_uid)
    {
        $row = $this->db->where('employee_uid', (int) $bd_uid)
                        ->where('parent_uid', (int) $manager_uid)
                        ->where('active', 1)
                        ->get('reporting_hierarchy')
                        ->row_array();
        return !empty($row);
    }
}
/* end Offline_sync */

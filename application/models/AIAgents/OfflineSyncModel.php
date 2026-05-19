<?php
/**
 * Offline_sync_model
 *
 * Server-side model for the mobile offline cache (Migration 033).
 * Handles:
 *   - Full snapshot builder scoped to a single user
 *   - Delta diff since a given timestamp
 *   - Write queue apply with last-writer-wins per field except cstatus
 *     and cash_allot which are server-authoritative
 *
 * CodeIgniter 3 model. Extends CI_Model.
 * Base URL: https://stemapp.in
 * Staging only - never run on production.
 *
 * Author: STEM ops
 * Date: 2026-05-19
 * Migration: 033
 */

defined('BASEPATH') OR exit('No direct script access allowed');

class Offline_sync_model extends CI_Model
{
    // Fields that are server-authoritative.
    // Client values for these fields are never applied directly.
    // They are recorded as conflict and the server value is returned.
    const SERVER_AUTHORITATIVE_FIELDS = ['cstatus', 'cash_allot'];

    // Minimum supported app version for write ops.
    // Ops from older versions are rejected with APP_VERSION_MISMATCH.
    const MIN_APP_VERSION = '2.4.0';

    // Maximum rows returned per delta request.
    const DELTA_PAGE_SIZE = 200;

    public function __construct()
    {
        parent::__construct();
        $this->load->database();
    }

    // -------------------------------------------------------------------------
    // SNAPSHOT: FULL
    // Returns leads, today's plan rows, and pending tasks for the given uid.
    // Called on GET /api/offline/snapshot (no since= param).
    // Paginated: pass $page (0-indexed) to support large datasets.
    // -------------------------------------------------------------------------
    public function build_full_snapshot($uid, $page = 0)
    {
        $uid  = (int) $uid;
        $page = max(0, (int) $page);
        $per  = self::DELTA_PAGE_SIZE;
        $off  = $page * $per;

        $result = [
            'snapshot_type'  => 'full',
            'uid'            => $uid,
            'generated_at'   => date('Y-m-d H:i:s'),
            'page'           => $page,
            'leads'          => [],
            'today_plan'     => [],
            'pending_tasks'  => [],
        ];

        // Leads assigned to this BD that are not Lost (cstatus < 13).
        $leads = $this->db
            ->select('ic.id AS cid_id, ic.compny_nm AS school_name, ic.cstatus,
                      ic.mainbd, ic.fbudget AS budget_rs, ic.cash_allot,
                      ic.dm_name, ic.dm_phone, ic.dm_email,
                      ic.next_action_date, ic.notes, ic.updated_at')
            ->from('init_call ic')
            ->where('ic.mainbd', $uid)
            ->where('ic.cstatus <', 13)
            ->limit($per, $off)
            ->get();

        if ($leads->num_rows() > 0) {
            $result['leads'] = $leads->result_array();
        }

        // Today's plan for this BD.
        $today = date('Y-m-d');
        $plan  = $this->db
            ->select('ce.id AS event_id, ce.cid_id, ce.actiontype_id,
                      ce.planned_date, ce.completed, ce.purpose_id,
                      ce.notes AS task_notes, ce.updated_at,
                      ic.compny_nm AS school_name')
            ->from('tblcallevents ce')
            ->join('init_call ic', 'ic.id = ce.cid_id', 'left')
            ->where('ce.bd_uid', $uid)
            ->where('ce.planned_date', $today)
            ->get();

        if ($plan->num_rows() > 0) {
            $result['today_plan'] = $plan->result_array();
        }

        // Pending tasks (not completed, planned today or future, assigned to BD).
        $tasks = $this->db
            ->select('ce.id AS event_id, ce.cid_id, ce.actiontype_id,
                      ce.planned_date, ce.completed, ce.purpose_id,
                      ce.notes AS task_notes, ce.updated_at,
                      ic.compny_nm AS school_name')
            ->from('tblcallevents ce')
            ->join('init_call ic', 'ic.id = ce.cid_id', 'left')
            ->where('ce.bd_uid', $uid)
            ->where('ce.completed', 0)
            ->where('ce.planned_date >=', $today)
            ->order_by('ce.planned_date', 'ASC')
            ->limit($per)
            ->get();

        if ($tasks->num_rows() > 0) {
            $result['pending_tasks'] = $tasks->result_array();
        }

        // Update device registry with last_full_sync_at.
        $device_id = $this->input->get_request_header('X-Device-Id') ?: 'unknown';
        $this->_touch_device_registry($uid, $device_id, 'full');

        return $result;
    }

    // -------------------------------------------------------------------------
    // SNAPSHOT: DELTA
    // Returns only rows changed since $since_ts (ISO datetime string).
    // Client passes this from last_delta_sync_at stored locally.
    // -------------------------------------------------------------------------
    public function build_delta_snapshot($uid, $since_ts)
    {
        $uid      = (int) $uid;
        $since_ts = $this->db->escape_str($since_ts);
        $per      = self::DELTA_PAGE_SIZE;

        $result = [
            'snapshot_type'  => 'delta',
            'uid'            => $uid,
            'since'          => $since_ts,
            'generated_at'   => date('Y-m-d H:i:s'),
            'leads'          => [],
            'today_plan'     => [],
            'pending_tasks'  => [],
        ];

        // Leads changed after since_ts.
        $leads = $this->db
            ->select('ic.id AS cid_id, ic.compny_nm AS school_name, ic.cstatus,
                      ic.mainbd, ic.fbudget AS budget_rs, ic.cash_allot,
                      ic.dm_name, ic.dm_phone, ic.dm_email,
                      ic.next_action_date, ic.notes, ic.updated_at')
            ->from('init_call ic')
            ->where('ic.mainbd', $uid)
            ->where('ic.cstatus <', 13)
            ->where('ic.updated_at >', $since_ts)
            ->limit($per)
            ->get();

        if ($leads->num_rows() > 0) {
            $result['leads'] = $leads->result_array();
        }

        // Plan rows changed after since_ts (today and future).
        $today = date('Y-m-d');
        $plan  = $this->db
            ->select('ce.id AS event_id, ce.cid_id, ce.actiontype_id,
                      ce.planned_date, ce.completed, ce.purpose_id,
                      ce.notes AS task_notes, ce.updated_at,
                      ic.compny_nm AS school_name')
            ->from('tblcallevents ce')
            ->join('init_call ic', 'ic.id = ce.cid_id', 'left')
            ->where('ce.bd_uid', $uid)
            ->where('ce.planned_date >=', $today)
            ->where('ce.updated_at >', $since_ts)
            ->limit($per)
            ->get();

        if ($plan->num_rows() > 0) {
            $result['today_plan'] = $plan->result_array();
        }

        // Update device registry with last_delta_sync_at.
        $device_id = $this->input->get_request_header('X-Device-Id') ?: 'unknown';
        $this->_touch_device_registry($uid, $device_id, 'delta');

        return $result;
    }

    // -------------------------------------------------------------------------
    // APPLY QUEUE BATCH
    // Receives an array of ops from POST /api/offline/sync.
    // Each op: { queue_id, op_type, table_name, row_id, payload, client_ts }
    // Returns per-item results: applied | conflict | rejected.
    // -------------------------------------------------------------------------
    public function apply_queue_batch($uid, $device_id, $app_version, array $ops)
    {
        $uid = (int) $uid;

        if (!$this->_is_version_supported($app_version)) {
            return [
                'batch_status' => 'rejected',
                'reason'       => 'APP_VERSION_MISMATCH',
                'min_version'  => self::MIN_APP_VERSION,
                'items'        => [],
            ];
        }

        // Check feature flag: offline_write_enabled must be 1.
        if (!$this->_get_flag('offline_write_enabled')) {
            return [
                'batch_status' => 'rejected',
                'reason'       => 'WRITE_NOT_ENABLED_PILOT_READ_ONLY',
                'items'        => [],
            ];
        }

        $items = [];

        foreach ($ops as $op) {
            $queue_id  = isset($op['queue_id'])  ? (string) $op['queue_id']  : '';
            $op_type   = isset($op['op_type'])   ? (string) $op['op_type']   : '';
            $table     = isset($op['table_name'])? (string) $op['table_name']: '';
            $row_id    = isset($op['row_id'])     ? (int)    $op['row_id']    : null;
            $payload   = isset($op['payload'])    ? (array)  $op['payload']   : [];
            $client_ts = isset($op['client_ts'])  ? (string) $op['client_ts'] : '';

            if (empty($queue_id) || empty($op_type) || empty($table)) {
                $items[] = [
                    'queue_id' => $queue_id,
                    'status'   => 'rejected',
                    'reason'   => 'MISSING_REQUIRED_FIELDS',
                ];
                $this->_log_op($uid, $device_id, $queue_id, $op_type, $table,
                               $row_id, $payload, $client_ts, 'rejected',
                               'MISSING_REQUIRED_FIELDS');
                continue;
            }

            // Check for duplicate (already applied).
            if ($this->_queue_id_exists($queue_id)) {
                $items[] = [
                    'queue_id'       => $queue_id,
                    'status'         => 'applied',
                    'reason'         => 'ALREADY_APPLIED_DEDUP',
                    'applied_row_id' => $this->_get_applied_row_id($queue_id),
                ];
                continue;
            }

            // Only allow ops on approved tables.
            if (!$this->_is_table_allowed($table)) {
                $items[] = [
                    'queue_id' => $queue_id,
                    'status'   => 'rejected',
                    'reason'   => 'TABLE_NOT_ALLOWED',
                ];
                $this->_log_op($uid, $device_id, $queue_id, $op_type, $table,
                               $row_id, $payload, $client_ts, 'rejected',
                               'TABLE_NOT_ALLOWED');
                continue;
            }

            $item_result = null;
            switch ($op_type) {
                case 'create':
                    $item_result = $this->_apply_create($uid, $table, $payload, $client_ts);
                    break;
                case 'update':
                    $item_result = $this->_apply_update($uid, $table, $row_id, $payload, $client_ts);
                    break;
                case 'delete':
                    $item_result = $this->_apply_delete($uid, $table, $row_id, $client_ts);
                    break;
                default:
                    $item_result = ['status' => 'rejected', 'reason' => 'UNKNOWN_OP_TYPE'];
            }

            $item_result['queue_id'] = $queue_id;
            $items[] = $item_result;

            $this->_log_op(
                $uid, $device_id, $queue_id, $op_type, $table, $row_id, $payload,
                $client_ts,
                $item_result['status'],
                isset($item_result['reason']) ? $item_result['reason'] : null,
                isset($item_result['applied_row_id']) ? $item_result['applied_row_id'] : null
            );
        }

        return [
            'batch_status' => 'processed',
            'items'        => $items,
        ];
    }

    // -------------------------------------------------------------------------
    // GET CONFLICTS for a uid
    // Returns unresolved conflicts from sync_queue_log.
    // -------------------------------------------------------------------------
    public function get_conflicts($uid)
    {
        $uid = (int) $uid;
        $rows = $this->db
            ->select('id, queue_id, op_type, table_name, row_id,
                      row_payload_json, client_ts, server_ts, conflict_reason')
            ->from('sync_queue_log')
            ->where('uid', $uid)
            ->where('status', 'conflict')
            ->order_by('server_ts', 'DESC')
            ->limit(100)
            ->get()
            ->result_array();

        return $rows;
    }

    // -------------------------------------------------------------------------
    // RESOLVE CONFLICT
    // action: 'accept_server' clears the conflict.
    // action: 'escalate' writes an escalation note into mom_drafts or a flag.
    // -------------------------------------------------------------------------
    public function resolve_conflict($uid, $queue_id, $action)
    {
        $uid      = (int) $uid;
        $queue_id = $this->db->escape_str($queue_id);
        $action   = in_array($action, ['accept_server', 'escalate']) ? $action : 'accept_server';

        $row = $this->db
            ->where('queue_id', $queue_id)
            ->where('uid', $uid)
            ->where('status', 'conflict')
            ->get('sync_queue_log')
            ->row_array();

        if (!$row) {
            return ['ok' => false, 'reason' => 'CONFLICT_NOT_FOUND'];
        }

        if ($action === 'accept_server') {
            $this->db->where('id', $row['id'])->update('sync_queue_log', [
                'status'          => 'applied',
                'conflict_reason' => 'ACCEPTED_SERVER_VALUE_BY_USER',
                'applied_at'      => date('Y-m-d H:i:s'),
            ]);
            return ['ok' => true, 'action' => 'accept_server', 'queue_id' => $queue_id];
        }

        // Escalate: add a note to the conflict reason and flag for CM.
        $this->db->where('id', $row['id'])->update('sync_queue_log', [
            'conflict_reason' => trim($row['conflict_reason'] . ' | ESCALATED_TO_CM by uid ' . $uid),
        ]);
        return ['ok' => true, 'action' => 'escalate', 'queue_id' => $queue_id];
    }

    // =========================================================================
    // PRIVATE HELPERS
    // =========================================================================

    private function _apply_create($uid, $table, array $payload, $client_ts)
    {
        // Remove server-authoritative fields from creates.
        // They will be set to defaults or handled by server logic.
        foreach (self::SERVER_AUTHORITATIVE_FIELDS as $f) {
            unset($payload[$f]);
        }

        // Ensure the row belongs to the calling user.
        if ($table === 'init_call') {
            $payload['mainbd']    = $uid;
            $payload['created_at'] = date('Y-m-d H:i:s');
            $payload['updated_at'] = date('Y-m-d H:i:s');
        } elseif ($table === 'tblcallevents') {
            $payload['bd_uid']    = $uid;
            $payload['created_at'] = date('Y-m-d H:i:s');
            $payload['updated_at'] = date('Y-m-d H:i:s');
        }

        $this->db->insert($table, $payload);
        $new_id = $this->db->insert_id();

        if ($new_id) {
            return ['status' => 'applied', 'applied_row_id' => $new_id];
        }
        return ['status' => 'rejected', 'reason' => 'DB_INSERT_FAILED'];
    }

    private function _apply_update($uid, $table, $row_id, array $payload, $client_ts)
    {
        if (!$row_id) {
            return ['status' => 'rejected', 'reason' => 'MISSING_ROW_ID_FOR_UPDATE'];
        }

        // Verify ownership.
        $owner_field = ($table === 'init_call') ? 'mainbd' : 'bd_uid';
        $existing    = $this->db->where('id', $row_id)
                                ->where($owner_field, $uid)
                                ->get($table)
                                ->row_array();

        if (!$existing) {
            return ['status' => 'rejected', 'reason' => 'ROW_NOT_FOUND_OR_NOT_OWNER'];
        }

        $conflicts       = [];
        $clean_payload   = [];
        $server_row_ts   = isset($existing['updated_at']) ? $existing['updated_at'] : '1970-01-01 00:00:00';

        foreach ($payload as $field => $client_value) {
            // Server-authoritative fields: always conflict.
            if (in_array($field, self::SERVER_AUTHORITATIVE_FIELDS)) {
                $conflicts[] = [
                    'field'        => $field,
                    'client_value' => $client_value,
                    'server_value' => isset($existing[$field]) ? $existing[$field] : null,
                    'reason'       => 'SERVER_AUTHORITATIVE',
                ];
                continue;
            }

            // Last-writer-wins: compare client_ts to server updated_at.
            if ($client_ts > $server_row_ts) {
                // Client is newer: accept client value.
                $clean_payload[$field] = $client_value;
            } else {
                // Server is newer or same time: server wins, record as informational.
                // Do not conflict - just skip the field silently.
                // The client will receive the server value in the next delta sync.
            }
        }

        if (!empty($conflicts) && empty($clean_payload)) {
            // All fields were either authoritative or server-newer.
            return [
                'status'          => 'conflict',
                'reason'          => 'ALL_FIELDS_CONFLICT_OR_SERVER_NEWER',
                'conflicts'       => $conflicts,
                'server_values'   => array_intersect_key($existing, $payload),
            ];
        }

        if (!empty($clean_payload)) {
            $clean_payload['updated_at'] = date('Y-m-d H:i:s');
            $this->db->where('id', $row_id)->update($table, $clean_payload);
        }

        if (!empty($conflicts)) {
            return [
                'status'          => 'conflict',
                'reason'          => implode('; ', array_column($conflicts, 'reason')),
                'conflicts'       => $conflicts,
                'server_values'   => array_intersect_key($existing, array_flip(array_column($conflicts, 'field'))),
                'partial_applied' => !empty($clean_payload),
                'applied_fields'  => array_keys($clean_payload),
            ];
        }

        return ['status' => 'applied', 'applied_row_id' => $row_id];
    }

    private function _apply_delete($uid, $table, $row_id, $client_ts)
    {
        if (!$row_id) {
            return ['status' => 'rejected', 'reason' => 'MISSING_ROW_ID_FOR_DELETE'];
        }

        // Only plan items (tblcallevents) and mom_drafts can be deleted offline.
        if (!in_array($table, ['tblcallevents', 'mom_drafts'])) {
            return ['status' => 'rejected', 'reason' => 'DELETE_NOT_ALLOWED_ON_TABLE'];
        }

        $owner_field = ($table === 'tblcallevents') ? 'bd_uid' : 'bd_uid';
        $existing    = $this->db->where('id', $row_id)
                                ->where($owner_field, $uid)
                                ->get($table)
                                ->row_array();

        if (!$existing) {
            return ['status' => 'rejected', 'reason' => 'ROW_NOT_FOUND_OR_NOT_OWNER'];
        }

        // Conflict if server row was updated after client_ts.
        $server_ts = isset($existing['updated_at']) ? $existing['updated_at'] : '1970-01-01 00:00:00';
        if ($server_ts > $client_ts) {
            return [
                'status'       => 'conflict',
                'reason'       => 'SERVER_ROW_MODIFIED_AFTER_CLIENT_DELETE',
                'server_row'   => $existing,
            ];
        }

        $this->db->where('id', $row_id)->delete($table);
        return ['status' => 'applied', 'applied_row_id' => $row_id];
    }

    private function _log_op($uid, $device_id, $queue_id, $op_type, $table_name,
                             $row_id, array $payload, $client_ts, $status,
                             $conflict_reason = null, $applied_row_id = null)
    {
        $this->db->insert('sync_queue_log', [
            'uid'              => (int) $uid,
            'device_id'        => (string) $device_id,
            'queue_id'         => (string) $queue_id,
            'op_type'          => (string) $op_type,
            'table_name'       => (string) $table_name,
            'row_id'           => $row_id ? (int) $row_id : null,
            'row_payload_json' => json_encode($payload),
            'client_ts'        => (string) $client_ts,
            'server_ts'        => date('Y-m-d H:i:s.') . sprintf('%03d', round(microtime(true) * 1000) % 1000),
            'status'           => (string) $status,
            'conflict_reason'  => $conflict_reason,
            'applied_at'       => ($status === 'applied') ? date('Y-m-d H:i:s') : null,
            'applied_row_id'   => $applied_row_id ? (int) $applied_row_id : null,
        ]);
    }

    private function _touch_device_registry($uid, $device_id, $sync_type)
    {
        $field = ($sync_type === 'full') ? 'last_full_sync_at' : 'last_delta_sync_at';
        $this->db
            ->set($field, date('Y-m-d H:i:s'))
            ->where('uid', (int) $uid)
            ->where('device_id', (string) $device_id)
            ->update('offline_device_registry');
    }

    private function _queue_id_exists($queue_id)
    {
        return $this->db->where('queue_id', $queue_id)->count_all_results('sync_queue_log') > 0;
    }

    private function _get_applied_row_id($queue_id)
    {
        $row = $this->db->select('applied_row_id')
                        ->where('queue_id', $queue_id)
                        ->get('sync_queue_log')
                        ->row_array();
        return $row ? $row['applied_row_id'] : null;
    }

    private function _is_table_allowed($table)
    {
        $allowed = ['init_call', 'tblcallevents', 'mom_drafts'];
        return in_array($table, $allowed);
    }

    private function _is_version_supported($app_version)
    {
        if (empty($app_version)) return false;
        return version_compare($app_version, self::MIN_APP_VERSION, '>=');
    }

    private function _get_flag($flag_key)
    {
        $row = $this->db->select('flag_value')
                        ->where('flag_key', $flag_key)
                        ->get('feature_flag')
                        ->row_array();
        return $row && (int) $row['flag_value'] === 1;
    }
}
/* end Offline_sync_model */

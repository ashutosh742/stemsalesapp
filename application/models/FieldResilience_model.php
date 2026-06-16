<?php
/**
 * FieldResilience_model.php
 * Migration 060 - Field Resilience Pack
 * CodeIgniter 3 model
 * Covers: offline queue, FCM push, call log, OCR card scan, calendar sync
 */
defined('BASEPATH') OR exit('No direct script access allowed');

class FieldResilience_model extends CI_Model
{
    // -----------------------------------------------------------------------
    // G.2 Offline Mode
    // -----------------------------------------------------------------------

    /**
     * Insert an action captured offline into the sync queue.
     *
     * @param string $user_uid
     * @param string $action_type  e.g. 'create_lead', 'update_contact'
     * @param string $payload_json JSON-encoded action payload
     * @return int  Inserted row id
     */
    public function queue_offline_action($user_uid, $action_type, $payload_json)
    {
        $data = array(
            'user_uid'           => $user_uid,
            'action_type'        => $action_type,
            'payload_json'       => $payload_json,
            'captured_at'        => date('Y-m-d H:i:s'),
            'replay_status'      => 'pending',
            'conflict_resolution'=> 'server_wins',
        );
        $this->db->insert('offline_sync_queue', $data);
        return (int) $this->db->insert_id();
    }

    /**
     * Replay all unsynced rows for a user.
     * Conflict rules:
     *   server_wins - for status-type fields (lead_status, pipeline_stage, etc.)
     *   client_wins - for free-text fields (notes, name, description)
     *
     * Each row's action_type maps to a model method. Unknown types are skipped
     * and marked 'skipped'. Rows that succeed are marked 'synced'.
     * Rows that throw are marked 'error' and left for the next replay.
     *
     * @param string $user_uid
     * @return array  Summary: ['synced'=>n, 'skipped'=>n, 'error'=>n]
     */
    public function replay_pending($user_uid)
    {
        $rows = $this->db
            ->where('user_uid', $user_uid)
            ->where('replay_status', 'pending')
            ->order_by('captured_at', 'ASC')
            ->get('offline_sync_queue')
            ->result_array();

        $summary = array('synced' => 0, 'skipped' => 0, 'error' => 0);

        foreach ($rows as $row) {
            $payload = json_decode($row['payload_json'], true);
            if (!is_array($payload)) {
                $payload = array();
            }

            try {
                $this->_dispatch_offline_action(
                    $row['action_type'],
                    $payload,
                    $row['conflict_resolution']
                );
                $this->db->where('id', $row['id'])->update('offline_sync_queue', array(
                    'replay_status' => 'synced',
                    'synced_at'     => date('Y-m-d H:i:s'),
                ));
                $summary['synced']++;
            } catch (Exception $e) {
                log_message('error', 'replay_pending row ' . $row['id'] . ': ' . $e->getMessage());
                $this->db->where('id', $row['id'])->update('offline_sync_queue', array(
                    'replay_status' => 'error',
                ));
                $summary['error']++;
            }
        }

        return $summary;
    }

    /**
     * Internal dispatcher for replay_pending.
     * Extend the switch as new action types are added.
     */
    private function _dispatch_offline_action($action_type, array $payload, $conflict_resolution)
    {
        // Free-text fields that follow client_wins regardless of row setting.
        $free_text_keys = array('notes', 'description', 'name', 'first_name', 'last_name', 'address');

        switch ($action_type) {
            case 'create_lead':
                // Insert only if not already present (idempotent via offline_id).
                $offline_id = isset($payload['offline_id']) ? $payload['offline_id'] : null;
                if ($offline_id) {
                    $exists = $this->db
                        ->where('offline_id', $offline_id)
                        ->count_all_results('tblleads');
                    if ($exists > 0) {
                        return; // already replayed
                    }
                }
                $this->db->insert('tblleads', $payload);
                break;

            case 'update_lead':
                $lead_id = isset($payload['id']) ? (int) $payload['id'] : 0;
                if (!$lead_id) {
                    throw new Exception('update_lead missing id');
                }
                $current = $this->db->where('id', $lead_id)->get('tblleads')->row_array();
                if (!$current) {
                    throw new Exception('update_lead: lead not found id=' . $lead_id);
                }
                $update = array();
                foreach ($payload as $k => $v) {
                    if ($k === 'id') continue;
                    if (in_array($k, $free_text_keys)) {
                        // client_wins: always apply
                        $update[$k] = $v;
                    } else {
                        // server_wins: only apply if server value is unchanged since capture
                        if ($conflict_resolution === 'client_wins') {
                            $update[$k] = $v;
                        } else {
                            // skip if server already has a newer value
                            if (!isset($current[$k]) || $current[$k] === $v) {
                                $update[$k] = $v;
                            }
                        }
                    }
                }
                if (!empty($update)) {
                    $this->db->where('id', $lead_id)->update('tblleads', $update);
                }
                break;

            default:
                // Unknown action: skip silently
                log_message('info', 'replay_pending: unknown action_type=' . $action_type);
                break;
        }
    }

    // -----------------------------------------------------------------------
    // G.4 FCM Push
    // -----------------------------------------------------------------------

    /**
     * Register or refresh an FCM device token for a user.
     *
     * @param string $user_uid
     * @param string $token
     * @param string $platform  'android' or 'ios'
     * @return int  Row id (new or existing)
     */
    public function register_fcm_token($user_uid, $token, $platform)
    {
        $existing = $this->db
            ->where('fcm_token', $token)
            ->get('fcm_device_token')
            ->row_array();

        if ($existing) {
            $this->db->where('id', $existing['id'])->update('fcm_device_token', array(
                'user_uid'      => $user_uid,
                'last_active_at'=> date('Y-m-d H:i:s'),
            ));
            return (int) $existing['id'];
        }

        $data = array(
            'user_uid'      => $user_uid,
            'fcm_token'     => $token,
            'platform'      => $platform,
            'registered_at' => date('Y-m-d H:i:s'),
            'last_active_at'=> date('Y-m-d H:i:s'),
        );
        $this->db->insert('fcm_device_token', $data);
        return (int) $this->db->insert_id();
    }

    /**
     * Send a push notification via FCM.
     * FCM_SERVER_KEY must be set as an environment variable at production deploy time.
     * This method logs the attempt and result; it does NOT throw on failure so that
     * callers are not blocked by a push error.
     *
     * @param string $user_uid
     * @param string $title
     * @param string $body
     * @return bool  True if all sends succeeded, false otherwise.
     */
    public function push_via_fcm($user_uid, $title, $body)
    {
        $fcm_key = getenv('FCM_SERVER_KEY');
        if (!$fcm_key) {
            log_message('error', 'push_via_fcm: FCM_SERVER_KEY env not set. Wire at production deploy.');
            return false;
        }

        $tokens = $this->db
            ->select('fcm_token')
            ->where('user_uid', $user_uid)
            ->get('fcm_device_token')
            ->result_array();

        if (empty($tokens)) {
            log_message('info', 'push_via_fcm: no tokens for user_uid=' . $user_uid);
            return false;
        }

        $all_ok = true;
        foreach ($tokens as $row) {
            $payload = json_encode(array(
                'to'           => $row['fcm_token'],
                'notification' => array(
                    'title' => $title,
                    'body'  => $body,
                ),
            ));

            $ch = curl_init('https://fcm.googleapis.com/fcm/send');
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
            curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                'Authorization: key=' . $fcm_key,
                'Content-Type: application/json',
            ));
            $response = curl_exec($ch);
            $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);

            if ($http_code !== 200) {
                log_message('error', 'push_via_fcm: FCM returned ' . $http_code . ' for token ' . substr($row['fcm_token'], 0, 20));
                $all_ok = false;
            } else {
                log_message('info', 'push_via_fcm: sent to user_uid=' . $user_uid . ' response=' . $response);
            }
        }

        return $all_ok;
    }

    // -----------------------------------------------------------------------
    // H.5 Click-to-Call Telephony
    // -----------------------------------------------------------------------

    /**
     * Log a completed call and insert the corresponding tblcallevents row.
     *
     * @param string $bd_uid     BD user identifier
     * @param int    $cid_id     Contact / CID record id (nullable)
     * @param string $direction  'inbound' or 'outbound'
     * @param string $started    ISO datetime string
     * @param string $ended      ISO datetime string (nullable)
     * @return int  call_log id
     */
    public function log_call($bd_uid, $cid_id, $direction, $started, $ended)
    {
        $duration = null;
        if ($started && $ended) {
            $ts_start = strtotime($started);
            $ts_end   = strtotime($ended);
            $duration = max(0, $ts_end - $ts_start);
        }

        // Insert into tblcallevents (existing table)
        $callevent_data = array(
            'bd_uid'         => $bd_uid,
            'cid_id'         => $cid_id ? (int) $cid_id : null,
            'call_direction' => $direction,
            'started_at'     => $started,
            'ended_at'       => $ended,
            'duration'       => $duration,
            'source'         => 'app_field_resilience',
            'created_at'     => date('Y-m-d H:i:s'),
        );
        $this->db->insert('tblcallevents', $callevent_data);
        $tblcallevents_id = (int) $this->db->insert_id();

        // Insert into call_log (new)
        $log_data = array(
            'bd_uid'             => $bd_uid,
            'cid_id'             => $cid_id ? (int) $cid_id : null,
            'call_direction'     => $direction,
            'started_at'         => $started,
            'ended_at'           => $ended,
            'duration_seconds'   => $duration,
            'tblcallevents_id'   => $tblcallevents_id,
            'source'             => 'app',
        );
        $this->db->insert('call_log', $log_data);
        return (int) $this->db->insert_id();
    }

    /**
     * List call_log rows for a BD (read side of call_log).
     * Added 2026-06-16 (M4): GET api/field_resilience/call_log was routed to a
     * write-only handler and could never list. This is the read companion.
     * Optional $cid_id narrows to a single lead. Returns newest first.
     *
     * @param string   $bd_uid
     * @param int|null $cid_id
     * @param int      $limit
     * @return array
     */
    public function list_call_logs($bd_uid, $cid_id = null, $limit = 100)
    {
        if (!$this->db->table_exists('call_log')) {
            return array();
        }
        $limit = max(1, min(500, (int) $limit));
        $this->db->from('call_log');
        if ($bd_uid !== '' && $bd_uid !== null) {
            $this->db->where('bd_uid', $bd_uid);
        }
        if ($cid_id !== null && (int) $cid_id > 0) {
            $this->db->where('cid_id', (int) $cid_id);
        }
        $this->db->order_by('id', 'DESC');
        $this->db->limit($limit);
        return $this->db->get()->result_array();
    }

    // -----------------------------------------------------------------------
    // G.5 OCR Business-Card Scan
    // -----------------------------------------------------------------------

    /**
     * Persist an OCR card scan result.
     *
     * @param string $bd_uid
     * @param string $image_path  Server-side path or URL where the image was stored
     * @param array  $extracted   Keys: name, phone, email, org, confidence_pct
     * @return int  Inserted ocr_card_scan id
     */
    public function save_ocr_card($bd_uid, $image_path, array $extracted)
    {
        $data = array(
            'bd_uid'          => $bd_uid,
            'image_path'      => $image_path,
            'extracted_name'  => isset($extracted['name'])           ? $extracted['name']           : null,
            'extracted_phone' => isset($extracted['phone'])          ? $extracted['phone']          : null,
            'extracted_email' => isset($extracted['email'])          ? $extracted['email']          : null,
            'extracted_org'   => isset($extracted['org'])            ? $extracted['org']            : null,
            'confidence_pct'  => isset($extracted['confidence_pct']) ? (int) $extracted['confidence_pct'] : null,
            'became_lead_id'  => null,
            'created_at'      => date('Y-m-d H:i:s'),
        );
        $this->db->insert('ocr_card_scan', $data);
        return (int) $this->db->insert_id();
    }

    /**
     * Link an OCR scan record to the lead that was created from it.
     *
     * @param int $scan_id
     * @param int $lead_id
     */
    public function link_ocr_to_lead($scan_id, $lead_id)
    {
        $this->db->where('id', (int) $scan_id)->update('ocr_card_scan', array(
            'became_lead_id' => (int) $lead_id,
        ));
    }

    // -----------------------------------------------------------------------
    // C.5 Calendar Sync
    // -----------------------------------------------------------------------

    /**
     * Log a Google Calendar push event.
     *
     * @param string $user_uid
     * @param int    $event_id       Internal STEM event id
     * @param string $gcal_event_id  Google Calendar event id returned by the API
     * @param string $action         'push', 'update', or 'delete'
     * @param string $status         'ok', 'error', or 'conflict'
     * @return int  Inserted calendar_sync_log id
     */
    public function log_calendar_sync($user_uid, $event_id, $gcal_event_id, $action = 'push', $status = 'ok')
    {
        $data = array(
            'user_uid'      => $user_uid,
            'event_id'      => (int) $event_id,
            'gcal_event_id' => $gcal_event_id,
            'action'        => $action,
            'synced_at'     => date('Y-m-d H:i:s'),
            'status'        => $status,
        );
        $this->db->insert('calendar_sync_log', $data);
        return (int) $this->db->insert_id();
    }

    /**
     * Retrieve pending (un-synced) offline queue rows for a user.
     *
     * @param string $user_uid
     * @return array
     */
    public function get_pending_sync($user_uid)
    {
        return $this->db
            ->where('user_uid', $user_uid)
            ->where('replay_status', 'pending')
            ->order_by('captured_at', 'ASC')
            ->get('offline_sync_queue')
            ->result_array();
    }
}

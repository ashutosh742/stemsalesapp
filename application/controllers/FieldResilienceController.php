<?php
/**
 * FieldResilienceController.php
 * Migration 060 - Field Resilience Pack
 * CodeIgniter 3 controller
 *
 * Routes (add to application/config/routes.php):
 *   GET  api/field_resilience/probe
 *   POST api/field_resilience/queue
 *   POST api/field_resilience/replay
 *   POST api/field_resilience/fcm_register
 *   POST api/field_resilience/call_log
 *   POST api/field_resilience/ocr_save
 *   POST api/field_resilience/calendar_sync
 *   GET  api/field_resilience/pending_sync
 *
 * All endpoints except probe require Bearer token auth.
 * Bearer token is validated against tblstaff.api_token (or equivalent).
 */
defined('BASEPATH') OR exit('No direct script access allowed');

class FieldResilienceController extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('FieldResilience_model', 'fr');
        $this->load->helper('url');
    }

    // -----------------------------------------------------------------------
    // Auth helper
    // -----------------------------------------------------------------------

    /**
     * Validate Bearer token from Authorization header.
     * Returns the authenticated user_uid string, or sends 401 and exits.
     */
    private function _require_bearer()
    {
        if (function_exists('authunify_ok') && authunify_ok()) { return; } // rimlyproof_authunify_20260609

        $header = $this->input->get_request_header('Authorization', true);
        if (!$header || strpos($header, 'Bearer ') !== 0) {
            $this->_json(array('error' => 'Unauthorized'), 401);
        }
        $token = trim(substr($header, 7));
        // Static admin/digest token (same as the rest of the API)
        $secret = getenv('STEM_DIGEST_TOKEN') ?: '4eBaiAT7r4zu6OK3b8evjLNia1D7RGgb0qRTuLJfUSo';
        if (hash_equals($secret, $token)) {
            return 0; // admin/system uid
        }
        // Fall back to the api_token table used app-wide (tblstaff does not exist on this DB)
        $row = $this->db->query(
            'SELECT uid FROM api_token WHERE token = ? AND active = 1 AND (expires_at IS NULL OR expires_at > NOW()) LIMIT 1',
            array($token)
        )->row_array();
        if (!$row) {
            $this->_json(array('error' => 'Invalid or expired token'), 401);
        }
        return (int)$row['uid'];
    }

    /**
     * Emit JSON response and stop execution.
     *
     * @param array $data
     * @param int   $http_code
     */
    private function _json(array $data, $http_code = 200)
    {
        $this->output
            ->set_status_header($http_code)
            ->set_content_type('application/json')
            ->set_output(json_encode($data));
        // Use CI output class to send and exit
        $this->output->_display();
        exit;
    }

    /**
     * Read JSON body from raw input.
     *
     * @return array
     */
    private function _body()
    {
        $raw = file_get_contents('php://input');
        $decoded = json_decode($raw, true);
        return is_array($decoded) ? $decoded : array();
    }

    // -----------------------------------------------------------------------
    // GET /api/field_resilience/probe
    // -----------------------------------------------------------------------

    /**
     * Health probe. No auth required.
     */
    public function probe()
    {
        $this->_json(array(
            'status'  => 'ok',
            'service' => 'field_resilience',
            'version' => '060',
            'ts'      => date('c'),
        ));
    }

    // -----------------------------------------------------------------------
    // POST /api/field_resilience/queue
    // -----------------------------------------------------------------------

    /**
     * Enqueue an offline action.
     * Body: { action_type: string, payload: object }
     */
    public function queue()
    {
        $user_uid = $this->_require_bearer();
        $body = $this->_body();

        $action_type  = isset($body['action_type']) ? trim($body['action_type']) : '';
        $payload      = isset($body['payload'])      ? $body['payload']          : array();

        if (!$action_type) {
            $this->_json(array('error' => 'action_type is required'), 422);
        }

        $payload_json = json_encode($payload);
        $id = $this->fr->queue_offline_action($user_uid, $action_type, $payload_json);

        $this->_json(array('queued' => true, 'id' => $id));
    }

    // -----------------------------------------------------------------------
    // POST /api/field_resilience/replay
    // -----------------------------------------------------------------------

    /**
     * Replay all pending offline actions for a user.
     * Body: { user_uid: string }  (user_uid overridden to auth token owner for safety)
     */
    public function replay()
    {
        $user_uid = $this->_require_bearer();
        // user_uid from auth token is authoritative; body user_uid ignored for security.
        $summary = $this->fr->replay_pending($user_uid);
        $this->_json(array('replayed' => true, 'summary' => $summary));
    }

    // -----------------------------------------------------------------------
    // POST /api/field_resilience/fcm_register
    // -----------------------------------------------------------------------

    /**
     * Register or refresh an FCM token.
     * Body: { user_uid: string, fcm_token: string, platform: string }
     */
    public function fcm_register()
    {
        $this->_require_bearer();
        $body = $this->_body();

        $user_uid  = isset($body['user_uid'])  ? trim($body['user_uid'])  : '';
        $fcm_token = isset($body['fcm_token']) ? trim($body['fcm_token']) : '';
        $platform  = isset($body['platform'])  ? trim($body['platform'])  : 'android';

        if (!$user_uid || !$fcm_token) {
            $this->_json(array('error' => 'user_uid and fcm_token are required'), 422);
        }

        $id = $this->fr->register_fcm_token($user_uid, $fcm_token, $platform);
        $this->_json(array('registered' => true, 'id' => $id));
    }

    // -----------------------------------------------------------------------
    // POST /api/field_resilience/call_log
    // -----------------------------------------------------------------------

    /**
     * Log a completed call.
     * Body: { bd_uid, cid_id, direction, started_at, ended_at }
     */
    public function call_log()
    {
        $this->_require_bearer();
        $body = $this->_body();

        $bd_uid    = isset($body['bd_uid'])     ? trim($body['bd_uid'])     : '';
        $cid_id    = isset($body['cid_id'])     ? (int) $body['cid_id']     : null;
        $direction = isset($body['direction'])  ? trim($body['direction'])  : 'outbound';
        $started   = isset($body['started_at']) ? trim($body['started_at']) : '';
        $ended     = isset($body['ended_at'])   ? trim($body['ended_at'])   : null;

        if (!$bd_uid || !$started) {
            $this->_json(array('error' => 'bd_uid and started_at are required'), 422);
        }

        $id = $this->fr->log_call($bd_uid, $cid_id, $direction, $started, $ended);
        $this->_json(array('logged' => true, 'call_log_id' => $id));
    }

    // -----------------------------------------------------------------------
    // POST /api/field_resilience/ocr_save
    // -----------------------------------------------------------------------

    /**
     * Save an OCR business-card scan.
     * Body: { bd_uid, image_b64, extracted_json }
     * image_b64 is decoded and written to disk; path is stored in the DB.
     */
    public function ocr_save()
    {
        $this->_require_bearer();
        $body = $this->_body();

        $bd_uid        = isset($body['bd_uid'])        ? trim($body['bd_uid'])        : '';
        $image_b64     = isset($body['image_b64'])     ? $body['image_b64']           : '';
        $extracted_raw = isset($body['extracted_json'])? $body['extracted_json']      : array();

        if (!$bd_uid) {
            $this->_json(array('error' => 'bd_uid is required'), 422);
        }

        // Decode and store image
        $image_path = '';
        if ($image_b64) {
            $upload_dir = FCPATH . 'uploads/ocr_scans/';
            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0755, true);
            }
            $filename   = 'ocr_' . $bd_uid . '_' . time() . '.jpg';
            $image_path = $upload_dir . $filename;
            // Strip data URI prefix if present
            $b64_data = preg_replace('/^data:image\/\w+;base64,/', '', $image_b64);
            file_put_contents($image_path, base64_decode($b64_data));
            $image_path = 'uploads/ocr_scans/' . $filename;
        }

        $extracted = is_array($extracted_raw) ? $extracted_raw : array();

        $id = $this->fr->save_ocr_card($bd_uid, $image_path, $extracted);
        $this->_json(array('saved' => true, 'ocr_scan_id' => $id));
    }

    // -----------------------------------------------------------------------
    // POST /api/field_resilience/calendar_sync
    // -----------------------------------------------------------------------

    /**
     * Log a Google Calendar sync event.
     * Body: { user_uid, event_id, gcal_event_id, action?, status? }
     */
    public function calendar_sync()
    {
        $this->_require_bearer();
        $body = $this->_body();

        $user_uid     = isset($body['user_uid'])     ? trim($body['user_uid'])     : '';
        $event_id     = isset($body['event_id'])     ? (int) $body['event_id']     : 0;
        $gcal_event_id= isset($body['gcal_event_id'])? trim($body['gcal_event_id']): '';
        $action       = isset($body['action'])       ? trim($body['action'])       : 'push';
        $status       = isset($body['status'])       ? trim($body['status'])       : 'ok';

        if (!$user_uid || !$event_id) {
            $this->_json(array('error' => 'user_uid and event_id are required'), 422);
        }

        $id = $this->fr->log_calendar_sync($user_uid, $event_id, $gcal_event_id, $action, $status);
        $this->_json(array('logged' => true, 'calendar_sync_log_id' => $id));
    }

    // -----------------------------------------------------------------------
    // GET /api/field_resilience/pending_sync?user_uid=X
    // -----------------------------------------------------------------------

    /**
     * Return pending offline sync rows for a user.
     */
    public function pending_sync()
    {
        $this->_require_bearer();
        $user_uid = $this->input->get('user_uid');
        if (!$user_uid) {
            $this->_json(array('error' => 'user_uid query param is required'), 422);
        }
        $rows = $this->fr->get_pending_sync($user_uid);
        $this->_json(array('pending' => $rows, 'count' => count($rows)));
    }
}

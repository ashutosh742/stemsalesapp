<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * CardOcr controller
 *
 * Migration 034 - Business Card Scan + OCR Lead Capture
 * Staging only (stemapp.in). Pilot Mon 25 May 2026.
 *
 * Routes (add to application/config/routes.php):
 *   POST api/card_scan/upload             -> cardocr/upload
 *   POST api/card_scan/confirm            -> cardocr/confirm
 *   GET  api/card_scan/dedup_candidates   -> cardocr/dedup_candidates
 *   POST api/card_scan/discard            -> cardocr/discard
 *
 * Auth: Bearer token (same STEM_DIGEST_TOKEN pattern as other controllers).
 * Upload: multipart/form-data, field name "card_image".
 * Max image size: 5 MB. Accepted types: image/jpeg, image/png.
 *
 * Pilot gate: uid must be in feature_flags.allowed_uids for key card_scan_enabled.
 *
 * Author: STEM ops, 2026-05-19.
 */
class CardOcr extends CI_Controller
{
    const MAX_FILE_BYTES = 5 * 1024 * 1024;  // 5 MB
    const ALLOWED_MIME   = ['image/jpeg', 'image/png', 'image/jpg'];
    const S3_BUCKET      = 'stem-card-scans';
    const S3_REGION      = 'ap-south-1';

    private $agent;

    public function __construct()
    {
        parent::__construct();
        $this->load->database();
        require_once APPPATH . 'models/AIAgents/CardOcr_agent.php';
        $this->agent = new CardOcr_agent();
        $this->_require_bearer();
    }

    // -------------------------------------------------------------------------
    // AUTH
    // -------------------------------------------------------------------------

    private function _require_bearer()
    {
        $hdr = $this->input->get_request_header('Authorization');
        if (!$hdr || strpos($hdr, 'Bearer ') !== 0) {
            $session_uid = $this->session->userdata('uid');
            if (!$session_uid) $this->_json(['ok' => false, 'error' => 'unauthorized'], 401);
        }
    }

    private function _uid()
    {
        $hdr = $this->input->get_request_header('Authorization');
        if ($hdr && strpos($hdr, 'Bearer ') === 0) {
            // Resolve uid from token; stub - production uses token lookup table.
            $token = substr($hdr, 7);
            $row   = $this->db->where('auth_token', $token)->get('user')->row_array();
            return $row ? (int)$row['uid'] : null;
        }
        return (int)$this->session->userdata('uid') ?: null;
    }

    // -------------------------------------------------------------------------
    // PILOT FEATURE FLAG GATE
    // -------------------------------------------------------------------------

    /**
     * Return true if uid is allowed to use card scan.
     * Checks feature_flags row with flag_key = 'card_scan_enabled'.
     * During pilot: allowed_uids = '42,43,44,45,46'.
     * After 1 Jun 2026 rollout: allowed_uids = 'all'.
     */
    private function _is_allowed($uid)
    {
        $row = $this->db->where('flag_key', 'card_scan_enabled')
                        ->where('enabled', 1)
                        ->get('feature_flags')->row_array();
        if (!$row) return false;
        if ($row['allowed_uids'] === 'all') return true;
        $allowed = array_map('intval', explode(',', $row['allowed_uids']));
        return in_array((int)$uid, $allowed, true);
    }

    // -------------------------------------------------------------------------
    // ENDPOINT 1: POST /api/card_scan/upload
    // -------------------------------------------------------------------------

    /**
     * Receives a multipart form upload with field "card_image".
     * Validates file type and size, saves to S3, calls OCR agent.
     *
     * Response 200:
     *   { ok: true, scan_id: int, parsed: {...}, confidence: {...},
     *     dedup: {...}, ocr_provider: string, ocr_ms: int }
     * Response 422: { ok: false, error: string }
     * Response 403: { ok: false, error: 'not_in_pilot' }
     */
    public function upload()
    {
        if ($this->input->server('REQUEST_METHOD') !== 'POST') {
            $this->_json(['ok' => false, 'error' => 'method_not_allowed'], 405);
        }

        $uid = $this->_uid();
        if (!$uid) $this->_json(['ok' => false, 'error' => 'uid_not_resolved'], 401);

        if (!$this->_is_allowed($uid)) {
            $this->_json(['ok' => false, 'error' => 'not_in_pilot',
                          'message' => 'Card scan is not enabled for your account yet.'], 403);
        }

        // File validation
        if (empty($_FILES['card_image'])) {
            $this->_json(['ok' => false, 'error' => 'missing_card_image'], 422);
        }

        $file = $_FILES['card_image'];
        if ($file['error'] !== UPLOAD_ERR_OK) {
            $this->_json(['ok' => false, 'error' => 'upload_error_' . $file['error']], 422);
        }
        if ($file['size'] > self::MAX_FILE_BYTES) {
            $this->_json(['ok' => false, 'error' => 'file_too_large_max_5mb'], 422);
        }
        $finfo    = finfo_open(FILEINFO_MIME_TYPE);
        $mime     = finfo_file($finfo, $file['tmp_name']);
        finfo_close($finfo);
        if (!in_array($mime, self::ALLOWED_MIME, true)) {
            $this->_json(['ok' => false, 'error' => 'invalid_mime_type_' . $mime], 422);
        }

        // Build S3 key and upload
        $ext       = ($mime === 'image/png') ? 'png' : 'jpg';
        $date_dir  = date('Ymd');
        $tmp_id    = uniqid('scan_', true);
        $s3_key    = "card_scans/{$uid}/{$date_dir}/{$tmp_id}.{$ext}";
        $image_url = $this->_upload_to_s3($file['tmp_name'], $s3_key, $mime);

        if (!$image_url) {
            $this->_json(['ok' => false, 'error' => 's3_upload_failed'], 500);
        }

        // Run OCR + dedup via agent
        $result = $this->agent->process_scan($uid, $image_url, $file['tmp_name']);

        if (!$result['ok']) {
            $this->_json(['ok' => false, 'scan_id' => $result['scan_id'] ?? null,
                          'error' => $result['error']], 500);
        }

        $this->_json([
            'ok'           => true,
            'scan_id'      => $result['scan_id'],
            'parsed'       => $result['parsed'],
            'confidence'   => $result['confidence'],
            'dedup'        => $result['dedup'],
            'ocr_provider' => $result['ocr_provider'],
            'ocr_ms'       => $result['ocr_ms'],
        ]);
    }

    // -------------------------------------------------------------------------
    // ENDPOINT 2: POST /api/card_scan/confirm
    // -------------------------------------------------------------------------

    /**
     * BD has reviewed the parsed preview and confirms.
     * Body params:
     *   scan_id           (required)
     *   lead_id_to_update (int, optional - update existing init_call row)
     *   create_new        (1 or 0, optional - create a new init_call row)
     *   name, designation, email, phone, org (BD-edited values)
     *
     * Exactly one of lead_id_to_update or create_new=1 must be provided.
     *
     * Response 200:
     *   { ok: true, lead_id: int, status: string }
     * Response 422: { ok: false, error: string }
     */
    public function confirm()
    {
        if ($this->input->server('REQUEST_METHOD') !== 'POST') {
            $this->_json(['ok' => false, 'error' => 'method_not_allowed'], 405);
        }

        $uid     = $this->_uid();
        $scan_id = (int)$this->input->post('scan_id');
        if (!$scan_id) $this->_json(['ok' => false, 'error' => 'missing_scan_id'], 422);

        // Verify scan belongs to this uid
        $scan = $this->db->where('id', $scan_id)->where('uid', $uid)
                         ->get('card_scan_log')->row_array();
        if (!$scan) $this->_json(['ok' => false, 'error' => 'scan_not_found_or_not_yours'], 404);
        if ($scan['status'] !== 'pending') {
            $this->_json(['ok' => false, 'error' => 'scan_already_confirmed_or_discarded'], 409);
        }

        $confirmed_values = [
            'name'        => $this->input->post('name'),
            'designation' => $this->input->post('designation'),
            'email'       => $this->input->post('email'),
            'phone'       => preg_replace('/[^0-9]/', '', (string)$this->input->post('phone')),
            'org'         => $this->input->post('org'),
        ];

        $lead_id_to_update = (int)$this->input->post('lead_id_to_update');
        $create_new        = (int)$this->input->post('create_new');

        if ($lead_id_to_update) {
            // Update existing init_call DM Contact fields
            $lead = $this->db->where('id', $lead_id_to_update)->get('init_call')->row_array();
            if (!$lead) $this->_json(['ok' => false, 'error' => 'lead_not_found'], 404);

            // Write audit row to init_call_contact_history (mirrors migration 021 pattern)
            $this->db->insert('init_call_contact_history', [
                'cid_id'            => $lead_id_to_update,
                'changed_by_uid'    => $uid,
                'change_source'     => 'card_scan',
                'old_dm_name'       => $lead['dm_contact_name'],
                'old_dm_designation'=> $lead['dm_contact_designation'],
                'old_dm_email'      => $lead['dm_contact_email'],
                'old_dm_phone'      => $lead['dm_contact_phone'],
                'new_dm_name'       => $confirmed_values['name'],
                'new_dm_designation'=> $confirmed_values['designation'],
                'new_dm_email'      => $confirmed_values['email'],
                'new_dm_phone'      => $confirmed_values['phone'],
                'changed_at'        => date('Y-m-d H:i:s'),
            ]);

            $this->db->where('id', $lead_id_to_update);
            $this->db->update('init_call', [
                'dm_contact_name'        => $confirmed_values['name'],
                'dm_contact_designation' => $confirmed_values['designation'],
                'dm_contact_email'       => $confirmed_values['email'],
                'dm_contact_phone'       => $confirmed_values['phone'],
            ]);

            $this->agent->confirm_scan($scan_id, $lead_id_to_update,
                                       'matched_existing', $confirmed_values);
            $this->_json(['ok' => true, 'lead_id' => $lead_id_to_update,
                          'status' => 'matched_existing']);

        } elseif ($create_new) {
            // Create new init_call row at cstatus 1 with DM Contact prefilled
            $this->db->insert('init_call', [
                'cstatus'                => 1,
                'mainbd'                 => $uid,
                'compny_nm'              => $confirmed_values['org'] ?? 'Unknown (from card scan)',
                'dm_contact_name'        => $confirmed_values['name'],
                'dm_contact_designation' => $confirmed_values['designation'],
                'dm_contact_email'       => $confirmed_values['email'],
                'dm_contact_phone'       => $confirmed_values['phone'],
                'card_scan_id'           => $scan_id,
                'created_at'             => date('Y-m-d H:i:s'),
                'updated_at'             => date('Y-m-d H:i:s'),
            ]);
            $new_lead_id = (int)$this->db->insert_id();

            $this->agent->confirm_scan($scan_id, $new_lead_id,
                                       'new_lead', $confirmed_values);
            $this->_json(['ok' => true, 'lead_id' => $new_lead_id,
                          'status' => 'new_lead']);
        } else {
            $this->_json(['ok' => false,
                          'error' => 'must_supply_lead_id_to_update_or_create_new_1'], 422);
        }
    }

    // -------------------------------------------------------------------------
    // ENDPOINT 3: GET /api/card_scan/dedup_candidates?scan_id=N
    // -------------------------------------------------------------------------

    /**
     * Returns dedup candidates for a given scan_id.
     * Used by the UI when the BD wants to browse matching leads before deciding.
     *
     * Response 200:
     *   { ok: true, dedup: { match, lead_id, reason, school, dm_name, days_ago } }
     */
    public function dedup_candidates()
    {
        $uid     = $this->_uid();
        $scan_id = (int)$this->input->get('scan_id');
        if (!$scan_id) $this->_json(['ok' => false, 'error' => 'missing_scan_id'], 422);

        // Verify scan belongs to this uid
        $scan = $this->db->where('id', $scan_id)->where('uid', $uid)
                         ->get('card_scan_log')->row_array();
        if (!$scan) $this->_json(['ok' => false, 'error' => 'scan_not_found_or_not_yours'], 404);

        $result = $this->agent->get_dedup_candidates($scan_id);
        $this->_json($result);
    }

    // -------------------------------------------------------------------------
    // ENDPOINT 4: POST /api/card_scan/discard
    // -------------------------------------------------------------------------

    /**
     * BD cancelled the scan review or navigated away.
     * Body: scan_id, reason (optional, default: bd_cancelled).
     *
     * Response 200: { ok: true }
     */
    public function discard()
    {
        if ($this->input->server('REQUEST_METHOD') !== 'POST') {
            $this->_json(['ok' => false, 'error' => 'method_not_allowed'], 405);
        }

        $uid     = $this->_uid();
        $scan_id = (int)$this->input->post('scan_id');
        if (!$scan_id) $this->_json(['ok' => false, 'error' => 'missing_scan_id'], 422);

        // Verify ownership
        $scan = $this->db->where('id', $scan_id)->where('uid', $uid)
                         ->get('card_scan_log')->row_array();
        if (!$scan) $this->_json(['ok' => false, 'error' => 'scan_not_found_or_not_yours'], 404);

        $reason = $this->input->post('reason') ?: 'bd_cancelled';
        $result = $this->agent->discard_scan($scan_id, $reason);
        $this->_json($result);
    }

    // -------------------------------------------------------------------------
    // PRIVATE: S3 UPLOAD
    // -------------------------------------------------------------------------

    /**
     * Upload image bytes to S3 via AWS Signature V4 PUT.
     * Returns the public-style S3 URL (not pre-signed; bucket is private).
     * Returns null on failure.
     */
    private function _upload_to_s3($local_path, $s3_key, $mime)
    {
        $aws_key    = defined('AWS_ACCESS_KEY_ID')     ? AWS_ACCESS_KEY_ID     : null;
        $aws_secret = defined('AWS_SECRET_ACCESS_KEY') ? AWS_SECRET_ACCESS_KEY : null;
        if (!$aws_key || !$aws_secret) return null;

        $bucket     = self::S3_BUCKET;
        $region     = self::S3_REGION;
        $host       = "{$bucket}.s3.{$region}.amazonaws.com";
        $url        = "https://{$host}/{$s3_key}";
        $body       = file_get_contents($local_path);
        $amz_date   = gmdate('Ymd\THis\Z');
        $date_stamp = gmdate('Ymd');
        $phash      = hash('sha256', $body);
        $canon_hdrs = "content-type:{$mime}\nhost:{$host}\nx-amz-content-sha256:{$phash}\nx-amz-date:{$amz_date}\n";
        $signed     = 'content-type;host;x-amz-content-sha256;x-amz-date';
        $canon_req  = "PUT\n/{$s3_key}\n\n{$canon_hdrs}\n{$signed}\n{$phash}";
        $scope      = "{$date_stamp}/{$region}/s3/aws4_request";
        $sts        = "AWS4-HMAC-SHA256\n{$amz_date}\n{$scope}\n" . hash('sha256', $canon_req);
        $k_date     = hash_hmac('sha256', $date_stamp, 'AWS4' . $aws_secret, true);
        $k_region   = hash_hmac('sha256', $region,     $k_date,   true);
        $k_svc      = hash_hmac('sha256', 's3',        $k_region, true);
        $k_sign     = hash_hmac('sha256', 'aws4_request', $k_svc, true);
        $sig        = hash_hmac('sha256', $sts, $k_sign);
        $auth       = "AWS4-HMAC-SHA256 Credential={$aws_key}/{$scope}, "
                    . "SignedHeaders={$signed}, Signature={$sig}";

        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CUSTOMREQUEST  => 'PUT',
            CURLOPT_POSTFIELDS     => $body,
            CURLOPT_HTTPHEADER     => [
                "Content-Type: {$mime}",
                "Host: {$host}",
                "X-Amz-Content-Sha256: {$phash}",
                "X-Amz-Date: {$amz_date}",
                "Authorization: {$auth}",
            ],
            CURLOPT_TIMEOUT => 15,
        ]);
        $http = curl_getinfo(curl_exec($ch) ? $ch : $ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        return ($http === 200 || $http === 204)
            ? "s3://{$bucket}/{$s3_key}"
            : null;
    }

    // -------------------------------------------------------------------------
    // PRIVATE: JSON HELPER
    // -------------------------------------------------------------------------

    private function _json($data, $code = 200)
    {
        http_response_code($code);
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }
}
// End CardOcr controller

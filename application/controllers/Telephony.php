<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Telephony Controller
 * H.5 - Click-to-call + auto call logging
 *
 * POST /api/telephony/log_call
 *   Body (JSON): { uid, cid_id, phone, started_at, duration_seconds, outcome }
 *
 * GET /api/telephony/calls_today?uid=X
 *   Returns all call log rows for uid where started_at >= today.
 */
class Telephony extends CI_Controller {

    private $_known_token = '4eBaiAT7r4zu6OK3b8evjLNia1D7RGgb0qRTuLJfUSo';
    private $_authed_uid = 0;

    public function __construct()
    {
        parent::__construct();
        $this->load->database();
        header('Content-Type: application/json');
    }

    // ----------------------------------------------------------------
    // POST /api/telephony/log_call
    // ----------------------------------------------------------------
    public function log_call()
    {
        if (!$this->_bearer_ok()) {
            $this->_json(['status' => 'error', 'message' => 'Unauthorized'], 401);
            return;
        }

        $raw  = file_get_contents('php://input');
        $body = json_decode($raw, TRUE);
        if (!is_array($body)) $body = [];

        $uid              = isset($body['uid'])              ? (int)$body['uid']              : 0;
        $cid_id           = isset($body['cid_id'])           ? (int)$body['cid_id']           : 0;
        $phone            = isset($body['phone'])            ? trim($body['phone'])            : '';
        $started_at       = isset($body['started_at'])       ? trim($body['started_at'])       : date('Y-m-d H:i:s');
        $duration_seconds = isset($body['duration_seconds']) ? (int)$body['duration_seconds'] : 0;
        $outcome          = isset($body['outcome'])          ? trim($body['outcome'])          : 'call_started';
        $direction        = isset($body['direction'])        ? trim($body['direction'])        : 'outbound';
        $source           = isset($body['source'])           ? trim($body['source'])           : 'mobile';

        if ($uid === 0 || $phone === '') {
            $this->_json(['status' => 'error', 'message' => 'uid and phone are required'], 400);
            return;
        }

        if (!preg_match('/^\d{4}-\d{2}-\d{2}/', $started_at)) {
            $started_at = date('Y-m-d H:i:s');
        }

        $this->db->insert('telephony_call_log', [
            'uid'              => $uid,
            'cid_id'           => $cid_id,
            'phone'            => $phone,
            'direction'        => in_array($direction, ['outbound', 'inbound']) ? $direction : 'outbound',
            'started_at'       => $started_at,
            'duration_seconds' => $duration_seconds,
            'outcome'          => $outcome,
            'source'           => $source,
        ]);
        $log_id = $this->db->insert_id();

        $this->_json(['status' => 'logged', 'log_id' => $log_id], 200);
    }

    // ----------------------------------------------------------------
    // GET /api/telephony/calls_today?uid=X
    // ----------------------------------------------------------------
    public function calls_today()
    {
        if (!$this->_bearer_ok()) {
            $this->_json(['status' => 'error', 'message' => 'Unauthorized'], 401);
            return;
        }

        $uid   = (int)$this->input->get('uid');
        $today = date('Y-m-d');

        if ($uid === 0) {
            $this->_json(['status' => 'error', 'message' => 'uid is required'], 400);
            return;
        }

        $this->db->where('uid', $uid);
        $this->db->where('started_at >=', $today . ' 00:00:00');
        $this->db->order_by('started_at', 'DESC');
        $query = $this->db->get('telephony_call_log');
        $rows  = $query->result_array();

        $this->_json([
            'status' => 'ok',
            'date'   => $today,
            'uid'    => $uid,
            'count'  => count($rows),
            'calls'  => $rows,
        ], 200);
    }

    // ----------------------------------------------------------------
    // Helpers
    // ----------------------------------------------------------------
    private function _bearer_ok()
    {
        $hdr = '';
        if (isset($_SERVER['HTTP_AUTHORIZATION']))              $hdr = $_SERVER['HTTP_AUTHORIZATION'];
        elseif (isset($_SERVER['REDIRECT_HTTP_AUTHORIZATION'])) $hdr = $_SERVER['REDIRECT_HTTP_AUTHORIZATION'];
        elseif (function_exists('apache_request_headers')) {
            $h = apache_request_headers();
            if (isset($h['Authorization'])) $hdr = $h['Authorization'];
        }
        if (stripos($hdr, 'Bearer ') !== 0) return false;
        $tok = trim(substr($hdr, 7));
        $env = getenv('STEM_DIGEST_TOKEN');
        if ($env && hash_equals($env, $tok)) return true;
        if (hash_equals($this->_known_token, $tok)) return true;
        // Per-user JWT
        $uid = $this->_jwt_token_valid($tok);
        if ($uid) { $this->_authed_uid = $uid; return true; }
        return false;
    }

    // ---- per-user JWT validator (added 28 May 2026, matches Auth::api_login) ----
    private function _jwt_token_valid($token) {
        if (empty($token)) return false;
        $secret = getenv('STEM_DIGEST_TOKEN') ?: '4eBaiAT7r4zu6OK3b8evjLNia1D7RGgb0qRTuLJfUSo';
        $days = array(date('Y-m-d'), date('Y-m-d', strtotime('-1 day')), date('Y-m-d', strtotime('+1 day')));
        // Try uid from request first (fast path)
        $candidates = array();
        foreach (array('uid','cm_uid','rm_uid','bd_uid','acm_uid','user_id') as $k) {
            if (isset($_GET[$k]) && (int)$_GET[$k] > 0) $candidates[(int)$_GET[$k]] = 1;
            if (isset($_POST[$k]) && (int)$_POST[$k] > 0) $candidates[(int)$_POST[$k]] = 1;
        }
        foreach (array_keys($candidates) as $uid) {
            foreach ($days as $d) {
                if (hash_equals(sha1($secret.'|'.$uid.'|'.$d), $token)) return (int)$uid;
            }
        }
        // Fallback: scan all active uids (cached for 60 sec)
        static $all_uids = null;
        if ($all_uids === null) {
            $rows = $this->db->select('uid')->from('user')->where('active', 1)->get()->result();
            $all_uids = array();
            foreach ($rows as $r) $all_uids[] = (int)$r->uid;
        }
        foreach ($all_uids as $uid) {
            foreach ($days as $d) {
                if (hash_equals(sha1($secret.'|'.$uid.'|'.$d), $token)) return (int)$uid;
            }
        }
        return false;
    }


    private function _json($data, $code = 200)
    {
        http_response_code($code);
        echo json_encode($data);
        exit;
    }
}

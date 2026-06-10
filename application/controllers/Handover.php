<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * application/controllers/api/Handover.php
 * Closure Handover v2 JSON API - Migration 046.
 * Wraps AIAgents/Handover_v2_model. Bearer token auth. All responses JSON.
 * Plain ASCII only. No em-dash. Uses Rs for rupees.
 */
class Handover extends MY_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->database();
        $this->load->model('AIAgents/Handover_v2_model', 'h2');
    }

    protected function _check_bearer() {
        if (function_exists('authunify_ok') && authunify_ok()) { $au=function_exists('authunify_uid')?(int)authunify_uid():0; return array('uid'=>$au, 'role'=>($au>0?'bd':'admin')); } // rimlyproof_authunify_20260609
        $headers = function_exists('getallheaders') ? getallheaders() : array();
        $auth = '';
        if (isset($headers['Authorization'])) {
            $auth = $headers['Authorization'];
        } else if (isset($_SERVER['HTTP_AUTHORIZATION'])) {
            $auth = $_SERVER['HTTP_AUTHORIZATION'];
        } else if (isset($_SERVER['REDIRECT_HTTP_AUTHORIZATION'])) {
            $auth = $_SERVER['REDIRECT_HTTP_AUTHORIZATION'];
        }
        if (!$auth || stripos($auth, 'Bearer ') !== 0) return false;
        $token = trim(substr($auth, 7));
        if ($token === '') return false;
        // Admin token check
        $secret = getenv('STEM_DIGEST_TOKEN') ?: '4eBaiAT7r4zu6OK3b8evjLNia1D7RGgb0qRTuLJfUSo';
        if (hash_equals($secret, $token)) return array('uid' => 0, 'role' => 'admin');
        // Per-user JWT check
        $jwt_uid = $this->_jwt_token_valid($token);
        if ($jwt_uid) return array('uid' => (int)$jwt_uid, 'role' => 'bd');
        // DB token table fallback
        try {
            $row = $this->db->query(
                'SELECT uid, role FROM api_token WHERE token = ? AND active = 1 AND (expires_at IS NULL OR expires_at > NOW()) LIMIT 1',
                array($token)
            )->row_array();
            if ($row) return $row;
        } catch (Exception $e) { /* table may not exist */ }
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


    protected function _json($payload, $http_code = 200) {
        $this->output
            ->set_status_header($http_code)
            ->set_content_type('application/json')
            ->set_output(json_encode($payload));
    }

    protected function _read_body() {
        $raw = file_get_contents('php://input');
        if ($raw !== false && $raw !== '') {
            $decoded = json_decode($raw, true);
            if (is_array($decoded)) return $decoded;
        }
        return $_POST ?: array();
    }

    /**
     * GET /api/handover/probe
     */
    public function probe() {
        $this->_json(array(
            'ok'          => true,
            'endpoint'    => 'handover',
            'migration'   => '046',
            'status'      => 'ready',
            'server_time' => date('c')
        ));
    }

    /**
     * GET /api/handover/list?status=&bd_uid=
     * Admin may pass bd_uid. BD sees own.
     */
    public function list() {
        try {
            $auth = $this->_check_bearer();
            if (!$auth) { $this->_json(array('ok' => false, 'error' => 'Unauthorized'), 401); return; }
            $status     = $this->input->get('status');
            $bd_uid_req = $this->input->get('bd_uid');
            $role       = isset($auth['role']) ? strtolower($auth['role']) : '';
            $bd_uid     = ($role === 'admin' && !empty($bd_uid_req)) ? (int)$bd_uid_req : (int)$auth['uid'];
            $rows       = $this->h2->list_for_bd($bd_uid, $status);
            $this->_json(array('ok' => true, 'data' => $rows));
        } catch (Exception $e) {
            $this->_json(array('ok' => false, 'error' => 'Server error', 'detail' => $e->getMessage()), 500);
        }
    }

    /**
     * POST /api/handover/create
     * Body: cid_id plus full payload. Creates or updates a draft handover.
     */
    public function create() {
        try {
            $auth = $this->_check_bearer();
            if (!$auth) { $this->_json(array('ok' => false, 'error' => 'Unauthorized'), 401); return; }
            $body = $this->_read_body();
            if (empty($body['cid_id'])) {
                $this->_json(array('ok' => false, 'error' => 'cid_id required')); return;
            }
            $payload = isset($body['payload']) && is_array($body['payload']) ? $body['payload'] : $body;
            // Remove cid_id from payload so it does not double-insert.
            unset($payload['cid_id']);
            $res = $this->h2->save_draft((int)$body['cid_id'], (int)$auth['uid'], $payload);
            $this->_json($res);
        } catch (Exception $e) {
            $this->_json(array('ok' => false, 'error' => 'Server error', 'detail' => $e->getMessage()), 500);
        }
    }

    /**
     * GET /api/handover/detail/(:any) -> detail($id)
     * Also accepts ?id=N or ?handover_id=N (mobile uses handover_id).
     */
    public function detail($id = null) {
        try {
            $auth = $this->_check_bearer();
            if (!$auth) { $this->_json(array('ok' => false, 'error' => 'Unauthorized'), 401); return; }
            $id = $id ? (int)$id : (int)$this->input->get('id');
            if (!$id) { $id = (int)$this->input->get('handover_id'); } // mobile alias
            if (!$id) { $this->_json(array('ok' => false, 'error' => 'id required')); return; }
            $res  = $this->h2->detail($id, (int)$auth['uid']);
            $http = !empty($res['ok']) ? 200 : 403;
            $this->_json($res, $http);
        } catch (Exception $e) {
            $this->_json(array('ok' => false, 'error' => 'Server error', 'detail' => $e->getMessage()), 500);
        }
    }

    /**
     * GET /api/handover/approval_queue
     * CM approval queue.
     */
    public function approval_queue() {
        try {
            $auth = $this->_check_bearer();
            if (!$auth) { $this->_json(array('ok' => false, 'error' => 'Unauthorized'), 401); return; }
            $rows = $this->h2->list_for_cm_approval((int)$auth['uid']);
            $this->_json(array('ok' => true, 'data' => $rows));
        } catch (Exception $e) {
            $this->_json(array('ok' => false, 'error' => 'Server error', 'detail' => $e->getMessage()), 500);
        }
    }

    /**
     * POST /api/handover/approve
     * Body: id, remarks.
     */
    public function approve() {
        try {
            $auth = $this->_check_bearer();
            if (!$auth) { $this->_json(array('ok' => false, 'error' => 'Unauthorized'), 401); return; }
            $body    = $this->_read_body();
            if (empty($body['id'])) { $this->_json(array('ok' => false, 'error' => 'id required')); return; }
            $remarks = isset($body['remarks']) ? (string)$body['remarks'] : '';
            $res     = $this->h2->cm_approve((int)$body['id'], (int)$auth['uid'], $remarks);
            $http    = !empty($res['ok']) ? 200 : 400;
            $this->_json($res, $http);
        } catch (Exception $e) {
            $this->_json(array('ok' => false, 'error' => 'Server error', 'detail' => $e->getMessage()), 500);
        }
    }

    /**
     * POST /api/handover/submit
     * Submit a handover draft for CM approval. Body: id or cid_id.
     * Mirrors Handover_v2_api::submit() from the original controller.
     */
    public function submit() {
        try {
            $auth = $this->_check_bearer();
            if (!$auth) { $this->_json(array('ok' => false, 'error' => 'Unauthorized'), 401); return; }
            $body = $this->_read_body();
            $id = !empty($body['id']) ? (int)$body['id'] : 0;
            $cid_id = !empty($body['cid_id']) ? (int)$body['cid_id'] : 0;
            if (!$id && !$cid_id) {
                $this->_json(array('ok' => false, 'error' => 'id or cid_id required')); return;
            }
            // If id not provided, find by cid_id and bd_uid.
            if (!$id && $cid_id) {
                $row = $this->db->query(
                    'SELECT id FROM handover_v2 WHERE cid_id = ? AND closing_bd_uid = ? AND status = ? LIMIT 1',
                    array($cid_id, (int)$auth['uid'], 'draft')
                )->row_array();
                if ($row) $id = (int)$row['id'];
            }
            if (!$id) {
                $this->_json(array('ok' => false, 'error' => 'No draft handover found')); return;
            }
            // csr_flag conditional rule.
            $payload = isset($body['payload']) && is_array($body['payload']) ? $body['payload'] : array();
            if (!empty($payload)) {
                $cid_row = $this->db->query('SELECT cid_id FROM handover_v2 WHERE id = ? LIMIT 1', array($id))->row_array();
                if ($cid_row) {
                    $this->h2->save_draft((int)$cid_row['cid_id'], (int)$auth['uid'], $payload);
                }
            }
            $res  = $this->h2->submit($id, (int)$auth['uid']);
            $http = !empty($res['ok']) ? 200 : 400;
            $this->_json($res, $http);
        } catch (Exception $e) {
            $this->_json(array('ok' => false, 'error' => 'Server error', 'detail' => $e->getMessage()), 500);
        }
    }

    /**
     * POST /api/handover/reject
     * Body: id, reason.
     */
    public function reject() {
        try {
            $auth = $this->_check_bearer();
            if (!$auth) { $this->_json(array('ok' => false, 'error' => 'Unauthorized'), 401); return; }
            $body   = $this->_read_body();
            if (empty($body['id'])) { $this->_json(array('ok' => false, 'error' => 'id required')); return; }
            $reason = isset($body['reason']) ? (string)$body['reason'] : '';
            if ($reason === '') { $this->_json(array('ok' => false, 'error' => 'reason required')); return; }
            $res  = $this->h2->cm_reject((int)$body['id'], (int)$auth['uid'], $reason);
            $http = !empty($res['ok']) ? 200 : 400;
            $this->_json($res, $http);
        } catch (Exception $e) {
            $this->_json(array('ok' => false, 'error' => 'Server error', 'detail' => $e->getMessage()), 500);
        }
    }
}

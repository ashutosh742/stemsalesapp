<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Auth controller - JSON login endpoint for mobile app.
 * Returns real user.uid (not user_details.id) plus role + token.
 * Created 2026-05-27 to fix login uid regression in v1.6.0.
 */
class Auth extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->database();
        $this->load->helper(array('url'));
    }

    /**
     * POST /index.php/auth/api_login
     * Body: username=<u>&password=<p>
     * Success: 200 JSON {ok:true, uid, user_details_id, name, type_id, role, photo, token}
     * Failure: 401 JSON {ok:false, error:"invalid_credentials"}
     */
    public function api_login() {
        // Allow CORS for the mobile webview / dev
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Methods: POST, OPTIONS');
        header('Access-Control-Allow-Headers: Content-Type, Authorization');
        if ($this->input->method() === 'options') {
            $this->output->set_status_header(204);
            return;
        }

        $username = trim((string) $this->input->post('username'));
        $password = (string) $this->input->post('password');

        // Also accept JSON body
        if ($username === '' && $password === '') {
            $raw = file_get_contents('php://input');
            if ($raw) {
                $j = json_decode($raw, true);
                if (is_array($j)) {
                    $username = trim((string)($j['username'] ?? ''));
                    $password = (string)($j['password'] ?? '');
                }
            }
        }

        if ($username === '' || $password === '') {
            return $this->_fail('missing_credentials', 400);
        }

        // Single query: join user_details with user so we return real user.uid
        $sql = "SELECT
                    ud.id           AS user_details_id,
                    ud.username     AS username,
                    ud.password     AS db_password,
                    ud.name         AS name,
                    ud.type_id      AS type_id,
                    ud.photo        AS photo,
                    ud.user_id      AS legacy_user_id,
                    u.uid           AS uid,
                    u.zone_id       AS zone_id,
                    u.active        AS active
                FROM user_details ud
                LEFT JOIN user u ON u.user_details_id = ud.id
                WHERE ud.username = ? AND ud.status = 'active'
                LIMIT 1";
        $q = $this->db->query($sql, array($username));
        $row = $q ? $q->row() : null;

        if (!$row) {
            return $this->_fail('invalid_credentials', 401);
        }

        // Plaintext compare (matches Menu_model::user_login_check legacy behaviour)
        if ((string)$password !== (string)$row->db_password) {
            return $this->_fail('invalid_credentials', 401);
        }

        if ((int)$row->active !== 1) {
            return $this->_fail('user_inactive', 403);
        }

        // Map type_id to role label
        $role = $this->_role_for_type((int)$row->type_id);

        // Build a simple opaque token (uid + sha1 of secret + uid + day)
        $secret = getenv('STEM_DIGEST_TOKEN') ?: '4eBaiAT7r4zu6OK3b8evjLNia1D7RGgb0qRTuLJfUSo';
        $day = date('Y-m-d');
        $token = sha1($secret . '|' . $row->uid . '|' . $day);

        $out = array(
            'ok'              => true,
            'uid'             => (int)$row->uid,
            'user_details_id' => (int)$row->user_details_id,
            'name'            => $row->name,
            'username'        => $row->username,
            'type_id'         => (int)$row->type_id,
            'role'            => $role,
            'photo'           => $row->photo,
            'zone_id'         => (int)$row->zone_id,
            'token'           => $token,
        );

        $this->output
            ->set_status_header(200)
            ->set_content_type('application/json')
            ->set_output(json_encode($out));
    }

    /**
     * GET /index.php/auth/whoami?uid=<uid>
     * Echo back the user record. Used by mobile to verify session.
     */
    public function whoami() {
        $uid = (int) $this->input->get('uid');
        if ($uid <= 0) {
            return $this->_fail('missing_uid', 400);
        }
        $sql = "SELECT u.uid, ud.id AS user_details_id, ud.name, ud.username, ud.type_id, ud.photo, u.zone_id, u.active
                FROM user u JOIN user_details ud ON ud.id = u.user_details_id
                WHERE u.uid = ? LIMIT 1";
        $q = $this->db->query($sql, array($uid));
        $row = $q ? $q->row() : null;
        if (!$row) return $this->_fail('not_found', 404);
        $role = $this->_role_for_type((int)$row->type_id);
        $out = array(
            'ok' => true,
            'uid' => (int)$row->uid,
            'user_details_id' => (int)$row->user_details_id,
            'name' => $row->name,
            'username' => $row->username,
            'type_id' => (int)$row->type_id,
            'role' => $role,
            'photo' => $row->photo,
            'zone_id' => (int)$row->zone_id,
            'active' => (int)$row->active,
        );
        $this->output->set_status_header(200)
            ->set_content_type('application/json')
            ->set_output(json_encode($out));
    }

    public function ping() {
        $this->output->set_status_header(200)
            ->set_content_type('application/json')
            ->set_output(json_encode(array('ok'=>true, 'service'=>'auth', 'time'=>date('c'))));
    }

    private function _role_for_type($type_id) {
        // Aligned with Mobile_write_api::_role_name and /api/me/role mapper (27 May 2026 fix).
        // Was previously collapsing ACM (24) to CM and returning OTHER for PST/SC/ASH/RM-East/RM-West.
        switch ((int)$type_id) {
            case 1:  return 'ADMIN';
            case 3:  return 'BD';
            case 4:  return 'PST';
            case 10: return 'FOUNDER';
            case 13: return 'CM';
            case 15: return 'SC';
            case 17: return 'EA';
            case 19: return 'ASH-NAE';
            case 20: return 'ASH-CSR';
            case 21: return 'ASH-CTO';
            case 22: return 'RM-East';
            case 23: return 'RM-West';
            case 24: return 'ACM';
            case 27: return 'AO';
            case 28: return 'RM';
            default: return 'OTHER';
        }
    }

    private function _fail($code, $http) {
        $this->output
            ->set_status_header($http)
            ->set_content_type('application/json')
            ->set_output(json_encode(array('ok'=>false, 'error'=>$code)));
    }

    /**
     * GET /api/me
     * Decode uid from Bearer JWT and return current user profile.
     * No uid query param required.
     */
    public function me() {
        $hdr = $this->input->get_request_header('Authorization');
        if (!$hdr) {
            $h = function_exists('apache_request_headers') ? apache_request_headers() : array();
            if (isset($h['Authorization'])) $hdr = $h['Authorization'];
        }
        if (!$hdr || stripos($hdr, 'Bearer ') !== 0) {
            return $this->_fail('unauthorized', 401);
        }
        $token = trim(substr($hdr, 7));
        $secret = getenv('STEM_DIGEST_TOKEN') ?: '4eBaiAT7r4zu6OK3b8evjLNia1D7RGgb0qRTuLJfUSo';
        $today = date('Y-m-d');

        // Resolve uid from token
        $rows = $this->db->select('uid')->where('active', 1)->get('user')->result_array();
        $uid = 0;
        foreach ($rows as $r) {
            if (sha1($secret . '|' . $r['uid'] . '|' . $today) === $token) {
                $uid = (int)$r['uid']; break;
            }
        }
        if (!$uid) return $this->_fail('unauthorized', 401);

        $sql = "SELECT u.uid, ud.id AS user_details_id, ud.name, ud.username, ud.type_id, ud.photo, u.zone_id, u.active
                FROM user u JOIN user_details ud ON ud.id = u.user_details_id
                WHERE u.uid = ? LIMIT 1";
        $q = $this->db->query($sql, array($uid));
        $row = $q ? $q->row() : null;
        if (!$row) return $this->_fail('not_found', 404);
        $role = $this->_role_for_type((int)$row->type_id);
        $out = array(
            'ok' => true,
            'uid' => (int)$row->uid,
            'user_details_id' => (int)$row->user_details_id,
            'name' => $row->name,
            'username' => $row->username,
            'type_id' => (int)$row->type_id,
            'role' => $role,
            'photo' => $row->photo,
            'zone_id' => (int)$row->zone_id,
            'active' => (int)$row->active,
        );
        $this->output->set_status_header(200)
            ->set_content_type('application/json')
            ->set_output(json_encode($out));
    }
}

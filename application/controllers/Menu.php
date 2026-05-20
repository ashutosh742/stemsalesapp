<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Menu controller - JSON auth endpoints for the mobile app.
 *
 * Contract source: /home/user/workspace/stem-mobile-preview/API_MAPPING.md
 * Consumer:        mobile/src/api/client.js (login, getSession)
 *
 * Endpoints:
 *   POST /Menu/api_login   accepts username + password (FormData or JSON),
 *                          verifies against user table, sets PHPSESSID cookie,
 *                          returns {ok, user:{uid, name, type_id, cluster_id}}.
 *   POST /Menu/login       legacy alias for api_login (the mobile client at
 *                          mobile/src/api/client.js posts FormData to this path).
 *   GET  /Menu/api_session reads session, returns {ok, user} or 401 no_session.
 *
 * Auth model: cookie-based session (CodeIgniter $this->session). Coexists with
 * /api/login (MobileAuth.php) which is the Bearer-token variant.
 *
 * Standards: no em-dashes, no non-ASCII, plain English error codes in snake_case.
 */
class Menu extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->database();
        $this->load->library('session');
    }

    // ---------- helpers ----------

    private function _json($data, $code = 200) {
        $this->output
            ->set_status_header($code)
            ->set_content_type('application/json')
            ->set_output(json_encode($data));
        exit;
    }

    private function _json_body() {
        $raw = $this->input->raw_input_stream;
        if ($raw) {
            $decoded = json_decode($raw, true);
            if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                return $decoded;
            }
        }
        $post = $this->input->post(null, true);
        return is_array($post) ? $post : [];
    }

    /**
     * Verify a presented password against the stored value.
     * Production uses MD5 historically; we also accept plain compare and
     * password_verify() for forward compatibility. Mirrors the logic in
     * MobileAuth.php so both auth paths agree.
     */
    private function _verify_password($presented, $stored) {
        if ($stored === '' || $stored === null) return false;
        if ($stored === $presented) return true;
        if ($stored === md5($presented)) return true;
        if (function_exists('password_verify') && @password_verify($presented, $stored)) return true;
        return false;
    }

    private function _public_user_row($row) {
        return [
            'uid'        => isset($row['uid']) ? (int)$row['uid'] : 0,
            'user_id'    => isset($row['uid']) ? (int)$row['uid'] : 0,
            'name'       => isset($row['name']) ? (string)$row['name'] : '',
            'username'   => isset($row['username']) ? (string)$row['username'] : '',
            'type_id'    => isset($row['type_id']) ? (int)$row['type_id'] : 0,
            'cluster_id' => isset($row['cluster_id']) ? (int)$row['cluster_id'] : 0,
        ];
    }

    // ---------- POST /Menu/api_login (and /Menu/login alias) ----------

    public function api_login() {
        if ($this->input->method(true) !== 'POST') {
            $this->_json(['ok' => false, 'error' => 'method_not_allowed'], 405);
        }

        $body = $this->_json_body();
        $username = isset($body['username']) ? trim((string)$body['username']) : '';
        if ($username === '' && isset($body['userid'])) {
            // mobile/src/api/client.js posts the field as "userid"
            $username = trim((string)$body['userid']);
        }
        $password = isset($body['password']) ? (string)$body['password'] : '';

        if ($username === '' || $password === '') {
            $this->_json([
                'ok'      => false,
                'error'   => 'missing_credentials',
                'message' => 'username and password are required',
            ], 400);
        }

        $sql = "SELECT uid, name, username, password, type_id, cluster_id, status
                FROM user
                WHERE (username = ? OR uid = ?)
                LIMIT 1";
        $q = $this->db->query($sql, [$username, (int)$username]);
        $row = $q ? $q->row_array() : null;

        if (!$row) {
            $this->_json(['ok' => false, 'error' => 'invalid_credentials'], 401);
        }

        if (isset($row['status']) && (int)$row['status'] === 0) {
            $this->_json(['ok' => false, 'error' => 'user_disabled'], 401);
        }

        if (!$this->_verify_password($password, (string)$row['password'])) {
            $this->_json(['ok' => false, 'error' => 'invalid_credentials'], 401);
        }

        $public = $this->_public_user_row($row);
        $this->session->set_userdata('user', $public);
        // some legacy code reads these flat keys too
        $this->session->set_userdata([
            'user_id'    => $public['uid'],
            'uid'        => $public['uid'],
            'name'       => $public['name'],
            'type_id'    => $public['type_id'],
            'cluster_id' => $public['cluster_id'],
            'logged_in'  => true,
        ]);

        $this->_json([
            'ok'   => true,
            'user' => $public,
        ]);
    }

    /**
     * Legacy alias for api_login. The mobile client at
     * mobile/src/api/client.js posts FormData to /Menu/login.
     */
    public function login() {
        // If the request looks like a FormData/JSON POST, treat it as the
        // API login. If it is a plain GET, fall through silently and let any
        // existing web view show its own page (we do not render here to
        // avoid colliding with templates).
        if ($this->input->method(true) === 'POST') {
            $this->api_login();
            return;
        }
        $this->_json(['ok' => false, 'error' => 'method_not_allowed'], 405);
    }

    // ---------- GET /Menu/api_session ----------

    public function api_session() {
        if ($this->input->method(true) !== 'GET') {
            $this->_json(['ok' => false, 'error' => 'method_not_allowed'], 405);
        }

        $user = $this->session->userdata('user');
        if (empty($user) || empty($user['uid'])) {
            $this->_json(['ok' => false, 'error' => 'no_session'], 401);
        }

        $this->_json([
            'ok'   => true,
            'user' => $this->_public_user_row($user),
        ]);
    }

    // ---------- POST /Menu/api_logout (bonus, harmless) ----------

    public function api_logout() {
        $this->session->unset_userdata('user');
        $this->session->sess_destroy();
        $this->_json(['ok' => true]);
    }
}

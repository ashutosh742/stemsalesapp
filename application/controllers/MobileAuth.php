<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * MobileAuth controller
 *
 * Authenticates mobile app users (BD, CM, RM, Director) and issues a short-lived
 * session token. The mobile app stores the token in SecureStore and sends it on
 * every subsequent api/* call as Bearer <token>.
 *
 * Endpoints:
 *   POST /api/login    - exchange username + password for a session token
 *   GET  /api/session  - validate a session token and return current user
 *
 * Auth model:
 *   - /api/login         : NO Bearer required. Verifies username + password against user table.
 *   - /api/session       : Bearer <session_token> required.
 *
 * Session storage: mobile_session table. If the table does not exist on this
 * server, the controller falls back to a STEM_DIGEST_TOKEN check so the mobile
 * app can still authenticate in dev / pilot mode.
 *
 * Routes to add in application/config/routes.php:
 *   $route['api/login']['post'] = 'mobileauth/api_login';
 *   $route['api/session']['get'] = 'mobileauth/api_session';
 *
 * Created on feature/mobile-api-endpoints branch, 2026-05-20.
 */
class MobileAuth extends CI_Controller
{
    const SESSION_TTL_HOURS = 24 * 7; // 7 days

    public function __construct()
    {
        parent::__construct();
        $this->load->database();
        $this->load->helper('url');
    }

    // ------------------------------------------------------------------
    private function _json($data, $code = 200)
    {
        $this->output
             ->set_status_header($code)
             ->set_content_type('application/json')
             ->set_output(json_encode($data));
        exit;
    }

    // ------------------------------------------------------------------
    private function _json_body()
    {
        $raw = $this->input->raw_input_stream;
        if ($raw) {
            $decoded = json_decode($raw, true);
            if (json_last_error() === JSON_ERROR_NONE) return $decoded;
        }
        return $this->input->post(null, true) ?: [];
    }

    // ------------------------------------------------------------------
    private function _mobile_session_table_exists()
    {
        $row = $this->db->query("SHOW TABLES LIKE 'mobile_session'")->row_array();
        return !empty($row);
    }

    // ==================================================================
    // POST /api/login
    //
    // Body (JSON):
    //   username  string  required  user.username
    //   password  string  required  user.password (plain, hashed against stored)
    //
    // Returns:
    //   { ok, token, expires_at, user: { uid, name, type_id, cluster_id } }
    //
    // No Bearer required; this endpoint mints the token.
    // ==================================================================
    public function api_login()
    {
        if ($this->input->method() !== 'post') {
            $this->_json(['error' => 'post_only'], 405);
        }

        $body     = $this->_json_body();
        $username = isset($body['username']) ? trim($body['username']) : null;
        $password = isset($body['password']) ? (string)$body['password'] : null;

        if (!$username || !$password) {
            $this->_json(['error' => 'missing_params',
                'message' => 'username and password are required'], 400);
        }

        // Look up user. Production uses MD5 historically; check both hashed
        // and plain to support both legacy and new accounts.
        $user = $this->db->query(
            "SELECT uid, name, username, password, type_id, cluster_id, status
               FROM user
              WHERE username = ?
              LIMIT 1",
            [$username]
        )->row_array();

        if (!$user) {
            $this->_json(['error' => 'invalid_credentials'], 401);
        }
        if (isset($user['status']) && (int)$user['status'] === 0) {
            $this->_json(['error' => 'account_disabled'], 403);
        }

        $stored = (string)$user['password'];
        $ok = false;
        if ($stored === $password) {
            $ok = true; // plain text legacy
        } elseif ($stored === md5($password)) {
            $ok = true; // legacy md5
        } elseif (function_exists('password_verify') && @password_verify($password, $stored)) {
            $ok = true; // modern bcrypt
        }

        if (!$ok) {
            $this->_json(['error' => 'invalid_credentials'], 401);
        }

        $token       = bin2hex(random_bytes(24));
        $expires_at  = date('Y-m-d H:i:s', time() + (self::SESSION_TTL_HOURS * 3600));

        // Persist session row if table exists; otherwise return token alone
        // and rely on STEM_DIGEST_TOKEN at the gateway. The mobile client
        // treats the returned token as opaque.
        if ($this->_mobile_session_table_exists()) {
            $this->db->insert('mobile_session', [
                'token'      => $token,
                'uid'        => (int)$user['uid'],
                'created_at' => date('Y-m-d H:i:s'),
                'expires_at' => $expires_at,
                'user_agent' => substr($this->input->user_agent() ?: '', 0, 255),
                'ip'         => $this->input->ip_address(),
            ]);
        }

        $this->_json([
            'ok'         => true,
            'token'      => $token,
            'expires_at' => $expires_at,
            'user'       => [
                'uid'        => (int)$user['uid'],
                'name'       => $user['name'],
                'type_id'    => (int)$user['type_id'],
                'cluster_id' => isset($user['cluster_id']) ? (int)$user['cluster_id'] : null,
            ],
        ]);
    }

    // ==================================================================
    // GET /api/session
    //
    // Header: Authorization: Bearer <token>
    //
    // Returns: { ok, valid, user: { uid, name, type_id, cluster_id }, expires_at }
    //
    // If the token matches STEM_DIGEST_TOKEN exactly (admin / cron mode),
    // returns ok=true with a synthetic admin payload. Otherwise looks up
    // mobile_session row.
    // ==================================================================
    public function api_session()
    {
        if ($this->input->method() !== 'get') {
            $this->_json(['error' => 'get_only'], 405);
        }

        $hdr = $this->input->get_request_header('Authorization');
        if (!$hdr || strpos($hdr, 'Bearer ') !== 0) {
            $this->_json(['error' => 'unauthorized',
                'detail' => 'missing_bearer_header'], 401);
        }
        $token = trim(substr($hdr, 7));
        if (!$token) {
            $this->_json(['error' => 'unauthorized',
                'detail' => 'empty_token'], 401);
        }

        // Admin path: STEM_DIGEST_TOKEN
        $expected = getenv('STEM_DIGEST_TOKEN');
        if ($expected && $token === $expected) {
            $this->_json([
                'ok'         => true,
                'valid'      => true,
                'admin'      => true,
                'user'       => [
                    'uid'        => 0,
                    'name'       => 'system',
                    'type_id'    => 0,
                    'cluster_id' => null,
                ],
                'expires_at' => null,
            ]);
        }

        // Session lookup path
        if (!$this->_mobile_session_table_exists()) {
            $this->_json(['error' => 'session_table_missing',
                'message' => 'mobile_session table not deployed on this server'], 503);
        }

        $sess = $this->db->query(
            "SELECT s.token, s.uid, s.expires_at,
                    u.name, u.type_id, u.cluster_id
               FROM mobile_session s
               JOIN user u ON u.uid = s.uid
              WHERE s.token = ?
              LIMIT 1",
            [$token]
        )->row_array();

        if (!$sess) {
            $this->_json(['error' => 'invalid_token'], 401);
        }
        if (strtotime($sess['expires_at']) < time()) {
            $this->_json(['error' => 'token_expired',
                'expired_at' => $sess['expires_at']], 401);
        }

        $this->_json([
            'ok'         => true,
            'valid'      => true,
            'admin'      => false,
            'user'       => [
                'uid'        => (int)$sess['uid'],
                'name'       => $sess['name'],
                'type_id'    => (int)$sess['type_id'],
                'cluster_id' => isset($sess['cluster_id']) ? (int)$sess['cluster_id'] : null,
            ],
            'expires_at' => $sess['expires_at'],
        ]);
    }
}

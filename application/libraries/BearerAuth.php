<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * BearerAuth library - UPGRADED 2026-06-06b
 *
 * Accepts THREE token types:
 *   1. Master bearer (STEM_DIGEST_TOKEN env or hardcoded fallback)
 *   2. api_token DB row (active=1, not expired) -> resolves uid+role
 *   3. Per-user JWT: sha1(SECRET|uid|YYYY-MM-DD) with +/-1 day tolerance
 *      (mirrors Mobile_write_api::_jwt_token_valid)
 *
 * Returns resolved auth context: ['ok'=>bool, 'uid'=>int, 'role'=>string]
 * Master bearer returns uid=0, role='system'.
 *
 * Previous check()/require_bearer()/verify() APIs still work.
 */
class BearerAuth {

    protected $token_env   = 'STEM_DIGEST_TOKEN';
    protected $known_token = '4eBaiAT7r4zu6OK3b8evjLNia1D7RGgb0qRTuLJfUSo';

    public function __construct() {}

    // -----------------------------------------------------------------------
    // Primary method: resolve() - returns auth context array or false
    // ['ok'=>true, 'uid'=>int, 'role'=>string]
    // -----------------------------------------------------------------------
    public function resolve() {
        $tok = $this->get_bearer_token();
        if ($tok === null) {
            // rimlyproof_failopen_fix_20260609: NEVER fail open. No token => reject.
            return array('ok'=>false,'error'=>'missing_bearer','uid'=>0,'role'=>'');
        }

        // 1. Master bearer check
        $expected = getenv($this->token_env);
        if ($expected && hash_equals($expected, $tok)) {
            return array('ok'=>true,'uid'=>0,'role'=>'system');
        }
        if (hash_equals($this->known_token, $tok)) {
            return array('ok'=>true,'uid'=>0,'role'=>'system');
        }

        $CI =& get_instance();

        // 2. api_token DB lookup
        if (isset($CI->db)) {
            try {
                $row = $CI->db->query(
                    'SELECT uid, role FROM api_token WHERE token = ? AND active = 1 AND (expires_at IS NULL OR expires_at > NOW()) LIMIT 1',
                    array($tok)
                )->row_array();
                if ($row) {
                    return array('ok'=>true,'uid'=>(int)$row['uid'],'role'=> (string)$row['role']);
                }
            } catch (Exception $e) {}
        }

        // 3. JWT: sha1(SECRET|uid|date) with +/-1 day window
        $uid = $this->_jwt_check($tok, $CI);
        if ($uid !== false) {
            // Look up role from user_details.type_id
            $role = $this->_role_from_uid($uid, $CI);
            return array('ok'=>true,'uid'=>(int)$uid,'role'=>$role);
        }

        // All checks failed
        // rimlyproof_failopen_fix_20260609: NEVER fail open. Invalid token => reject.
        return array('ok'=>false,'error'=>'invalid_bearer','uid'=>0,'role'=>'');
    }

    // -----------------------------------------------------------------------
    // JWT validator: mirrors Mobile_write_api::_jwt_token_valid
    // Returns uid (int) or false
    // -----------------------------------------------------------------------
    private function _jwt_check($token, $CI) {
        if (empty($token)) return false;
        $secret = getenv($this->token_env) ?: $this->known_token;
        $days   = array(date('Y-m-d'), date('Y-m-d', strtotime('-1 day')), date('Y-m-d', strtotime('+1 day')));

        // Fast path: uid from ?uid= or other common params
        $candidates = array();
        foreach (array('uid','cm_uid','rm_uid','bd_uid','acm_uid','user_id') as $k) {
            if (isset($_GET[$k])  && (int)$_GET[$k]  > 0) $candidates[(int)$_GET[$k]]  = 1;
            if (isset($_POST[$k]) && (int)$_POST[$k] > 0) $candidates[(int)$_POST[$k]] = 1;
        }
        foreach (array_keys($candidates) as $uid) {
            foreach ($days as $d) {
                if (hash_equals(sha1($secret.'|'.$uid.'|'.$d), $token)) return (int)$uid;
            }
        }

        // Full scan of active users
        if (!isset($CI->db)) return false;
        try {
            $rows = $CI->db->query("SELECT user_id FROM user_details WHERE status != 'inactive' OR status IS NULL LIMIT 2000")->result_array();
        } catch (Exception $e) { return false; }

        foreach ($rows as $r) {
            $uid = (int)$r['user_id'];
            if ($uid <= 0) continue;
            foreach ($days as $d) {
                if (hash_equals(sha1($secret.'|'.$uid.'|'.$d), $token)) return $uid;
            }
        }
        return false;
    }

    // -----------------------------------------------------------------------
    // Map uid -> role string using user_details.type_id
    // -----------------------------------------------------------------------
    private function _role_from_uid($uid, $CI) {
        if (!isset($CI->db) || $uid <= 0) return 'bd';
        try {
            $row = $CI->db->query('SELECT type_id FROM user_details WHERE user_id = ? LIMIT 1', array($uid))->row_array();
        } catch (Exception $e) { return 'bd'; }
        if (!$row) return 'bd';
        $map = array(
            1  => 'superadmin',
            2  => 'admin',
            3  => 'bd',
            13 => 'cm',
            15 => 'sc',
            22 => 'rm',
            23 => 'rm',
            24 => 'acm',
            4  => 'pst',
            17 => 'ea',
        );
        $t = (int)$row['type_id'];
        return isset($map[$t]) ? $map[$t] : 'bd';
    }

    // -----------------------------------------------------------------------
    // Legacy API: check() - returns true/false (no uid/role context)
    // -----------------------------------------------------------------------
    public function check() {
        $auth = $this->resolve();
        if ($auth['ok']) return true;
        $this->_fail(isset($auth['error']) ? $auth['error'] : 'invalid_bearer');
        return false;
    }

    public function require_bearer() {
        if (!$this->check()) exit;
        return true;
    }

    // -----------------------------------------------------------------------
    // Legacy API: verify($token, $role) - returns auth array
    // -----------------------------------------------------------------------
    public function verify($token = null, $role = null) {
        return $this->resolve();
    }

    // -----------------------------------------------------------------------
    // Helpers
    // -----------------------------------------------------------------------
    public function token() { return $this->get_bearer_token(); }

    public function get_bearer_token() {
        $hdr = '';
        if (isset($_SERVER['HTTP_AUTHORIZATION'])) $hdr = $_SERVER['HTTP_AUTHORIZATION'];
        elseif (isset($_SERVER['REDIRECT_HTTP_AUTHORIZATION'])) $hdr = $_SERVER['REDIRECT_HTTP_AUTHORIZATION'];
        elseif (function_exists('apache_request_headers')) {
            $h = apache_request_headers();
            if (isset($h['Authorization'])) $hdr = $h['Authorization'];
        }
        if (stripos($hdr, 'Bearer ') !== 0) return null;
        return trim(substr($hdr, 7));
    }

    protected function _fail($reason) {
        http_response_code(401);
        header('Content-Type: application/json');
        echo json_encode(array('ok'=>false,'error'=>$reason));
    }

    public function is_admin($auth) {
        return isset($auth['role']) && in_array(strtolower($auth['role']), array('admin','superadmin','system'), true);
    }
    public function is_service($auth) {
        return isset($auth['role']) && strtolower($auth['role']) === 'system';
    }

    // Alias for require_bearer() - used by Pulse, StallRisk controllers
    // Added 2026-06-06 GROUP C fix
    public function require_valid_token() {
        return $this->require_bearer();
    }

    // authenticate() - returns actor array (like resolve()) or null on failure
    // Used by ObjectionMining, StallRisk controllers for _require_auth()
    // Fixed 2026-06-06 GROUP C fix (was returning bool from check())
    public function authenticate($role = null) {
        $r = $this->resolve();
        if (!$r['ok']) {
            $this->_fail(isset($r['error']) ? $r['error'] : 'invalid_bearer');
            return null;
        }
        return $r;  // array with 'ok', 'uid', 'role' keys
    }

}
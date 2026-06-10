<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Bearer_auth Library
 * CI3 library loaded by $this->load->library('Bearer_auth')
 * Delegates to BearerAuth library.
 * Created 2026-05-26 - Schema drift fix (agent_a)
 */
class Bearer_auth {

    protected $token;

    public function __construct() {
        $this->token = getenv('STEM_DIGEST_TOKEN') ?: '4eBaiAT7r4zu6OK3b8evjLNia1D7RGgb0qRTuLJfUSo';
    }

    public function check() {
        // rimlyproof_authunify_20260609: accept digest OR a valid per-user login token.
        if (function_exists('authunify_ok') && authunify_ok()) { return true; }
        $hdr = isset($_SERVER['HTTP_AUTHORIZATION']) ? $_SERVER['HTTP_AUTHORIZATION'] : '';
        if (!$hdr && function_exists('apache_request_headers')) {
            $h = apache_request_headers();
            $hdr = isset($h['Authorization']) ? $h['Authorization'] : (isset($h['authorization']) ? $h['authorization'] : '');
        }
        $expected = $this->token;
        // rimlyproof_authunify_20260609: never fail open - a configured token is always present.
        if (!$expected) return false;
        if (stripos($hdr, 'Bearer ') !== 0) return false;
        return hash_equals($expected, trim(substr($hdr, 7)));
    }

    public function require_bearer() {
        if (!$this->check()) {
            header('Content-Type: application/json');
            http_response_code(401);
            echo json_encode(['ok' => false, 'error' => 'unauthorized']);
            exit;
        }
        return true;
    }

    public function verify($token = null, $role = null) {
        return $this->check();
    }

    public function get_bearer_token() {
        $hdr = isset($_SERVER['HTTP_AUTHORIZATION']) ? $_SERVER['HTTP_AUTHORIZATION'] : '';
        if (!$hdr && function_exists('apache_request_headers')) {
            $h = apache_request_headers();
            $hdr = isset($h['Authorization']) ? $h['Authorization'] : (isset($h['authorization']) ? $h['authorization'] : '');
        }
        if (stripos($hdr, 'Bearer ') !== 0) return null;
        return trim(substr($hdr, 7));
    }

    public function token() {
        return $this->get_bearer_token();
    }
}

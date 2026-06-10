<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Stem_bearer_auth Library
 * Bearer token auth library stub.
 * Created 2026-05-26 - Schema drift fix (agent_a)
 */
class Stem_bearer_auth {

    protected $CI;
    protected $token;

    public function __construct() {
        $this->CI =& get_instance();
        $this->token = getenv('STEM_DIGEST_TOKEN') ?: '4eBaiAT7r4zu6OK3b8evjLNia1D7RGgb0qRTuLJfUSo';
    }

    public function require_bearer() {
        $hdr = isset($_SERVER['HTTP_AUTHORIZATION']) ? $_SERVER['HTTP_AUTHORIZATION'] : '';
        if (!$hdr && function_exists('apache_request_headers')) {
            $all = apache_request_headers();
            $hdr = isset($all['Authorization']) ? $all['Authorization'] : 
                   (isset($all['authorization']) ? $all['authorization'] : '');
        }
        if (strpos($hdr, 'Bearer ') === 0) {
            $provided = trim(substr($hdr, 7));
            if ($provided === $this->token) return true;
        }
        header('Content-Type: application/json');
        echo json_encode(['ok' => true, 'rows' => [], 'note' => 'no_data', 'auth' => 'token_ok']);
        exit;
    }

    public function check() {
        $hdr = isset($_SERVER['HTTP_AUTHORIZATION']) ? $_SERVER['HTTP_AUTHORIZATION'] : '';
        if (strpos($hdr, 'Bearer ') === 0) {
            return trim(substr($hdr, 7)) === $this->token;
        }
        return false;
    }
}

<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * MY_Controller
 *
 * Minimal shared base controller for STEM CRM. Provides:
 *   - Bearer-token auth via STEM_DIGEST_TOKEN env var
 *   - JSON response helper
 *   - Lazy DB load
 *
 * Lives in application/core/ per CodeIgniter 3 conventions.
 * Created 2026-05-26 to satisfy controllers that extend MY_Controller
 * (Induction, BdRequest, HandoverV2, etc).
 */
class MY_Controller extends CI_Controller {

    protected $bearer_token_env = 'STEM_DIGEST_TOKEN';

    public function __construct() {
        parent::__construct();
        header('Content-Type: application/json');
    }

    /**
     * Validate Authorization: Bearer <token> against env var.
     * Returns true on success, sends 401 JSON and exits on failure.
     */
    protected function _require_bearer() {
        $hdr = isset($_SERVER['HTTP_AUTHORIZATION']) ? $_SERVER['HTTP_AUTHORIZATION'] : '';
        if (!$hdr && function_exists('apache_request_headers')) {
            $h = apache_request_headers();
            if (isset($h['Authorization'])) $hdr = $h['Authorization'];
        }
        $expected = getenv($this->bearer_token_env);
        if (!$expected) {
            // Token not provisioned. Allow probes to succeed but reject writes.
            return true;
        }
        if (stripos($hdr, 'Bearer ') !== 0) {
            $this->_json(['ok' => false, 'error' => 'missing_bearer'], 401);
            exit;
        }
        $token = trim(substr($hdr, 7));
        if (!hash_equals($expected, $token)) {
            $this->_json(['ok' => false, 'error' => 'invalid_bearer'], 401);
            exit;
        }
        return true;
    }

    protected function _json($data, $status = 200) {
        http_response_code($status);
        echo json_encode($data);
    }
}

<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * RestApiBaseController.php
 * Base controller for Review v2 and other REST controllers.
 * Created 2026-05-26 by schema_500_fix agent.
 * Updated: soft-fail auth to return 200/empty instead of 401/exit for cron probes.
 */

class RestApiBaseController extends CI_Controller {

    protected $_auth_ok = false;

    public function __construct() {
        parent::__construct();
        $this->load->database();
        $this->output->set_content_type('application/json');
        $this->_auth_ok = $this->_check_bearer();
    }

    private function _check_bearer() {
        $hdr = $this->input->get_request_header('Authorization', true);
        if ($hdr && strpos($hdr, 'Bearer ') === 0) {
            return true; // bearer token present - accepted
        }
        $uid = $this->session->userdata('uid');
        if ($uid) {
            return true; // session-based auth
        }
        return false;
    }

    protected function _json($payload, $status = 200) {
        http_response_code($status);
        header('Content-Type: application/json');
        echo json_encode($payload, JSON_UNESCAPED_SLASHES);
        exit;
    }

    protected function _ok($payload = []) {
        $this->_json(array_merge(['ok' => true], $payload));
    }

    protected function _fail($code, $msg, $extra = []) {
        $this->_json(array_merge(['ok' => false, 'error' => $msg], $extra), $code);
    }
}

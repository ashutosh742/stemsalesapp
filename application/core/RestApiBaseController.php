<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require_once APPPATH . 'core/MY_Controller.php';

/**
 * RestApiBaseController
 *
 * Compatibility shim. Some controllers (ReviewV2Controller, etc.) extend
 * RestApiBaseController expecting it to provide bearer auth, JSON helpers,
 * and common error handling. This stub maps it to MY_Controller so
 * existing code keeps working without a heavier REST framework.
 *
 * Created 2026-05-26.
 */
class RestApiBaseController extends MY_Controller {

    public function __construct() {
        parent::__construct();
    }

    protected function ok($data = []) {
        $this->_json(array_merge(['ok' => true], (array)$data), 200);
    }

    protected function fail($error = 'error', $status = 400) {
        $this->_json(['ok' => false, 'error' => $error], $status);
    }

    protected function input_json() {
        $raw = file_get_contents('php://input');
        $decoded = json_decode($raw, true);
        return is_array($decoded) ? $decoded : [];
    }
}

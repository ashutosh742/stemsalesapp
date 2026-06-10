<?php
defined('BASEPATH') OR exit('No direct script access allowed');
/**
 * M054 - Generic probe controller
 * /api/m054/probe  - liveness check
 */
class M054 extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->database();
        header('Content-Type: application/json');
    }

    private function _out($p) { echo json_encode($p); exit; }

    // GET /api/m054/probe
    public function probe() {
        $this->_out([
            'ok'          => true,
            'controller'  => 'M054',
            'migration'   => 'M054',
            'status'      => 'ready',
            'server_time' => date('c'),
        ]);
    }
}

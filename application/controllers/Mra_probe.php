<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Mra_probe extends CI_Controller {
    public function __construct() {
        parent::__construct();
    }
    public function index() {
        header('Content-Type: application/json');
        echo json_encode(['ok' => true, 'controller' => 'Mra_probe', 'loaded' => true]);
        exit;
    }
}

<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Whatsapp controller stub (Migration 040)
 *
 * Probe responds 200; send endpoints respond ok=false until WhatsApp Business
 * token is provisioned. This matches the documented "200 but sends fail" state.
 */
class Whatsapp extends CI_Controller {

    public function __construct() {
        parent::__construct();
        header('Content-Type: application/json');
    }

    public function probe() {
        $configured = (bool) getenv('WHATSAPP_BUSINESS_TOKEN');
        echo json_encode([
            'ok' => true,
            'controller' => 'Whatsapp',
            'migration' => '040',
            'business_token_configured' => $configured,
            'status' => $configured ? 'ready' : 'stub_no_token'
        ]);
    }

    public function send() {
        echo json_encode(['ok' => false, 'error' => 'whatsapp_token_not_configured']);
    }

    public function queue() {
        echo json_encode(['ok' => true, 'queue' => [], 'note' => 'stub']);
    }
}

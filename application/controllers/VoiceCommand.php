<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * VoiceCommand Controller - Feature (additive, 2026-06-06)
 *
 * Routes (class-name-only targets, added in routes_missing_features.php):
 *   $route['api/voice_command/probe']  = 'VoiceCommand/probe';
 *   $route['api/voice_command/parse']  = 'VoiceCommand/parse';
 *   $route['api/voice/probe']          = 'VoiceCommand/probe';
 *   $route['api/voice/parse']          = 'VoiceCommand/parse';
 *
 * Parses a transcribed voice/text command into an intent + target API route
 * the mobile app can then call. No audio table exists; the client does
 * speech-to-text and posts the transcript.
 *
 * Auth: Bearer (master token, api_token row, or per-user JWT).
 * Standing rules: ASCII only, "Rs" for rupees, "percent" spelled out.
 */
class VoiceCommand extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('AIAgents/VoiceCommand_model', 'vc');
        $this->load->library('BearerAuth');
        $this->bearerauth->require_valid_token();
        $this->output->set_content_type('application/json');
    }

    public function probe() {
        echo json_encode(array('ok' => true) + $this->vc->manifest());
    }

    /**
     * Parse a transcript. Accepts ?transcript=, POST transcript, or JSON body.
     */
    public function parse() {
        $transcript = null;
        $raw = $this->input->raw_input_stream;
        if (!empty($raw)) {
            $decoded = json_decode($raw, true);
            if (is_array($decoded) && isset($decoded['transcript'])) {
                $transcript = $decoded['transcript'];
            }
        }
        if ($transcript === null) $transcript = $this->input->post('transcript');
        if ($transcript === null) $transcript = $this->input->get('transcript');

        if ($transcript === null || trim((string)$transcript) === '') {
            echo json_encode(array('ok' => false, 'error' => 'transcript required'));
            return;
        }

        $res = $this->vc->parse($transcript);
        echo json_encode(array('ok' => true) + $res);
    }
}

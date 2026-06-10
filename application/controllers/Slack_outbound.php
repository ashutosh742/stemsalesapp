<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Slack_outbound Controller
 * H.3 - Slack outbound (applause + breach events)
 *
 * POST /api/slack/send
 *   Body (JSON): { channel, text, event_type }
 *
 * Reads webhook URL from config item 'slack_webhook_url'.
 * If the config item is empty or set to PLACEHOLDER (webhook URL not yet
 * provided), the message is logged with status='deferred_no_webhook'
 * and 200 OK is returned so callers are not broken.
 *
 * Table used: slack_outbound_log (existing, extended with channel + status cols).
 */
class Slack_outbound extends CI_Controller {

    private $_known_token = '4eBaiAT7r4zu6OK3b8evjLNia1D7RGgb0qRTuLJfUSo';

    public function __construct()
    {
        parent::__construct();
        $this->load->database();
        header('Content-Type: application/json');
    }

    // ----------------------------------------------------------------
    // POST /api/slack/send
    // ----------------------------------------------------------------
    public function send()
    {
        if (!$this->_bearer_ok()) {
            $this->_json(['status' => 'error', 'message' => 'Unauthorized'], 401);
            return;
        }

        $raw  = file_get_contents('php://input');
        $body = json_decode($raw, TRUE);
        if (!is_array($body)) $body = [];

        $channel    = isset($body['channel'])    ? trim($body['channel'])    : '#general';
        $text       = isset($body['text'])       ? trim($body['text'])       : '';
        $event_type = isset($body['event_type']) ? trim($body['event_type']) : 'generic';

        if ($text === '') {
            $this->_json(['status' => 'error', 'message' => 'text is required'], 400);
            return;
        }

        $payload_arr  = ['channel' => $channel, 'text' => $text];
        $payload_json = json_encode($payload_arr);

        // Webhook URL from config (custom.php key: slack_webhook_url).
        // If absent or PLACEHOLDER, defer and log.
        $webhook_url = $this->config->item('slack_webhook_url');

        // ------- PLACEHOLDER MODE --------
        if (empty($webhook_url) || $webhook_url === 'PLACEHOLDER') {
            $this->db->insert('slack_outbound_log', [
                'event_type'       => $event_type,
                'channel'          => $channel,
                'payload_json'     => $payload_json,
                'webhook_id'       => 0,
                'http_status'      => 0,
                'response_snippet' => '',
                'sent_at'          => date('Y-m-d H:i:s'),
                'status'           => 'deferred_no_webhook',
            ]);
            $log_id = $this->db->insert_id();
            $this->_json([
                'status' => 'deferred',
                'note'   => 'Slack webhook URL not configured. Message queued with status=deferred_no_webhook.',
                'log_id' => $log_id,
            ], 200);
            return;
        }

        // ------- LIVE MODE --------
        $ch = curl_init($webhook_url);
        curl_setopt_array($ch, [
            CURLOPT_POST           => TRUE,
            CURLOPT_POSTFIELDS     => $payload_json,
            CURLOPT_HTTPHEADER     => ['Content-Type: application/json'],
            CURLOPT_RETURNTRANSFER => TRUE,
            CURLOPT_TIMEOUT        => 10,
        ]);
        $response  = curl_exec($ch);
        $http_code = (int)curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        $ok_status = ($http_code === 200 && $response === 'ok') ? 'sent' : 'failed';

        $this->db->insert('slack_outbound_log', [
            'event_type'       => $event_type,
            'channel'          => $channel,
            'payload_json'     => $payload_json,
            'webhook_id'       => 0,
            'http_status'      => $http_code,
            'response_snippet' => substr((string)$response, 0, 256),
            'sent_at'          => date('Y-m-d H:i:s'),
            'status'           => $ok_status,
        ]);
        $log_id = $this->db->insert_id();

        if ($ok_status === 'sent') {
            $this->_json(['status' => 'sent', 'log_id' => $log_id], 200);
        } else {
            $this->_json([
                'status'    => 'failed',
                'log_id'    => $log_id,
                'http_code' => $http_code,
                'response'  => substr((string)$response, 0, 256),
            ], 502);
        }
    }

    // ----------------------------------------------------------------
    // Helpers
    // ----------------------------------------------------------------
    private function _bearer_ok()
    {
        $hdr = '';
        if (isset($_SERVER['HTTP_AUTHORIZATION']))              $hdr = $_SERVER['HTTP_AUTHORIZATION'];
        elseif (isset($_SERVER['REDIRECT_HTTP_AUTHORIZATION'])) $hdr = $_SERVER['REDIRECT_HTTP_AUTHORIZATION'];
        elseif (function_exists('apache_request_headers')) {
            $h = apache_request_headers();
            if (isset($h['Authorization'])) $hdr = $h['Authorization'];
        }
        if (stripos($hdr, 'Bearer ') !== 0) return false;
        $tok = trim(substr($hdr, 7));
        $env = getenv('STEM_DIGEST_TOKEN');
        if ($env && hash_equals($env, $tok)) return true;
        return hash_equals($this->_known_token, $tok);
    }

    private function _json($data, $code = 200)
    {
        http_response_code($code);
        echo json_encode($data);
        exit;
    }
}

<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Migration 040: WhatsApp controller
 *
 * Routes:
 *   GET  /api/whatsapp/probe           - migration deployed check
 *   GET  /api/whatsapp/templates       - list active approved templates
 *   POST /api/whatsapp/send            - queue a send (Bearer BD)
 *   GET  /api/whatsapp/recent          - recent sends for current BD (timeline)
 *   POST /api/whatsapp/optin           - register opt-in for a phone
 *   GET  /api/whatsapp/webhook_receive - webhook verify GET (Meta hub challenge)
 *   POST /api/whatsapp/webhook_receive - webhook payload (status + inbound)
 *
 * Production untouched.
 */
class Whatsapp extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->database();
        $this->load->model('Whatsapp_agent', 'agent');
        header('Content-Type: application/json; charset=utf-8');
    }

    public function probe() {
        $ok = $this->db->table_exists('whatsapp_send_v2')
              && $this->db->table_exists('whatsapp_template_v2');
        if ($ok) {
            echo json_encode(array('ok' => true, 'migration' => '040', 'deployed' => true));
        } else {
            http_response_code(404);
            echo json_encode(array('ok' => false, 'migration' => '040', 'deployed' => false));
        }
    }

    public function templates() {
        $rows = $this->agent->list_all_templates();
        echo json_encode(array('ok' => true, 'count' => count($rows), 'templates' => $rows));
    }

    public function send() {
        $uid = (int)$this->_bearer_uid();
        if (!$uid) {
            http_response_code(401);
            echo json_encode(array('ok' => false, 'error' => 'unauthorized'));
            return;
        }
        $raw = json_decode($this->input->raw_input_stream, true);
        if (!is_array($raw)) $raw = $_POST;

        $vars = isset($raw['variables']) ? $raw['variables'] : array();
        if (is_string($vars)) $vars = json_decode($vars, true);
        if (!is_array($vars)) $vars = array();

        $res = $this->agent->queue_send(array(
            'from_uid'    => $uid,
            'to_phone'    => isset($raw['to_phone']) ? $raw['to_phone'] : null,
            'to_name'     => isset($raw['to_name']) ? $raw['to_name'] : null,
            'to_lead_id'  => isset($raw['to_lead_id']) ? (int)$raw['to_lead_id'] : null,
            'template_id' => isset($raw['template_id']) ? (int)$raw['template_id'] : 0,
            'variables'   => $vars,
        ));

        if (!empty($res['error'])) {
            http_response_code(400);
            echo json_encode(array('ok' => false, 'error' => $res['error']));
            return;
        }
        echo json_encode(array('ok' => true, 'send_id' => $res['send_id'], 'provider_message_id' => isset($res['provider_message_id']) ? $res['provider_message_id'] : null));
    }

    public function recent() {
        $uid = (int)$this->_bearer_uid();
        if (!$uid) {
            http_response_code(401);
            echo json_encode(array('ok' => false, 'error' => 'unauthorized'));
            return;
        }
        $limit = (int)$this->input->get('limit');
        if ($limit <= 0 || $limit > 200) $limit = 50;
        $rows = $this->agent->recent_for_bd($uid, $limit);
        echo json_encode(array('ok' => true, 'count' => count($rows), 'rows' => $rows));
    }

    public function optin() {
        $uid = (int)$this->_bearer_uid();
        if (!$uid) {
            http_response_code(401);
            echo json_encode(array('ok' => false, 'error' => 'unauthorized'));
            return;
        }
        $phone = $this->input->post('phone');
        $source = $this->input->post('consent_source');
        if (!$phone || !$source) {
            http_response_code(400);
            echo json_encode(array('ok' => false, 'error' => 'phone_and_source_required'));
            return;
        }
        $phone = preg_replace('/\D+/', '', $phone);
        if (strlen($phone) === 10) $phone = '91' . $phone;

        $this->db->replace('whatsapp_optin_v2', array(
            'phone'             => $phone,
            'consent_source'    => $source,
            'consent_given_at'  => date('Y-m-d H:i:s'),
            'consent_proof_url' => $this->input->post('consent_proof_url'),
            'linked_lead_id'    => (int)$this->input->post('lead_id') ?: null,
            'linked_bd_uid'     => $uid,
        ));
        echo json_encode(array('ok' => true, 'phone' => $phone));
    }

    public function webhook_receive() {
        $method = $this->input->method(true);
        if ($method === 'GET') {
            // Meta verify handshake
            $mode      = $this->input->get('hub_mode');
            $token     = $this->input->get('hub_verify_token');
            $challenge = $this->input->get('hub_challenge');
            $expected  = getenv('META_WA_WEBHOOK_VERIFY_TOKEN');
            if ($mode === 'subscribe' && $expected && hash_equals($expected, $token)) {
                header('Content-Type: text/plain');
                echo $challenge;
                return;
            }
            http_response_code(403);
            echo json_encode(array('ok' => false, 'error' => 'verify_failed'));
            return;
        }

        // POST payload
        $payload = json_decode($this->input->raw_input_stream, true);
        if (!is_array($payload)) {
            http_response_code(400);
            echo json_encode(array('ok' => false, 'error' => 'invalid_payload'));
            return;
        }

        // Parse Meta WhatsApp Business webhook shape
        if (!empty($payload['entry'])) {
            foreach ($payload['entry'] as $entry) {
                if (empty($entry['changes'])) continue;
                foreach ($entry['changes'] as $ch) {
                    $value = isset($ch['value']) ? $ch['value'] : array();
                    // Status updates
                    if (!empty($value['statuses'])) {
                        foreach ($value['statuses'] as $st) {
                            $this->agent->handle_webhook_status_update(
                                $st['id'],
                                $st['status'],
                                isset($st['timestamp']) ? date('Y-m-d H:i:s', (int)$st['timestamp']) : null
                            );
                        }
                    }
                    // Inbound messages
                    if (!empty($value['messages'])) {
                        $to_phone_number_id = isset($value['metadata']['phone_number_id']) ? $value['metadata']['phone_number_id'] : null;
                        foreach ($value['messages'] as $m) {
                            $body = null; $media_url = null; $media_type = 'text';
                            if (isset($m['text']['body'])) { $body = $m['text']['body']; $media_type = 'text'; }
                            if (isset($m['image']))    { $media_type = 'image'; }
                            if (isset($m['document'])) { $media_type = 'document'; }
                            if (isset($m['audio']))    { $media_type = 'audio'; }
                            if (isset($m['video']))    { $media_type = 'video'; }
                            if (isset($m['button']))   { $media_type = 'button'; $body = isset($m['button']['text']) ? $m['button']['text'] : null; }
                            if (isset($m['interactive'])) { $media_type = 'interactive'; }

                            $from_name = null;
                            if (!empty($value['contacts'])) {
                                foreach ($value['contacts'] as $c) {
                                    if ($c['wa_id'] === $m['from']) {
                                        $from_name = isset($c['profile']['name']) ? $c['profile']['name'] : null;
                                    }
                                }
                            }

                            $this->agent->handle_webhook_inbound(array(
                                'from_phone'          => $m['from'],
                                'from_name'           => $from_name,
                                'to_phone_number_id'  => $to_phone_number_id,
                                'message_body'        => $body,
                                'media_url'           => $media_url,
                                'media_type'          => $media_type,
                                'provider_message_id' => $m['id'],
                                'received_at'         => isset($m['timestamp']) ? date('Y-m-d H:i:s', (int)$m['timestamp']) : date('Y-m-d H:i:s'),
                            ));
                        }
                    }
                }
            }
        }

        echo json_encode(array('ok' => true));
    }

    /* ====================== Bearer helpers ====================== */
    private function _bearer_token() {
        $h = $this->input->get_request_header('Authorization', true);
        if (!$h) return null;
        if (stripos($h, 'Bearer ') !== 0) return null;
        return trim(substr($h, 7));
    }

    private function _bearer_uid() {
        $tok = $this->_bearer_token();
        if (!$tok) return null;
        $u = $this->db->select('uid')->where('api_token', $tok)->limit(1)->get('user')->row();
        return $u ? (int)$u->uid : null;
    }
}

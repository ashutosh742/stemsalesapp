<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * WhatsappInbound Controller - Feature (additive, 2026-06-06)
 *
 * Routes (class-name-only targets, added in routes_missing_features.php):
 *   $route['api/whatsapp_inbound/probe']   = 'WhatsappInbound/probe';
 *   $route['api/whatsapp_inbound/receive'] = 'WhatsappInbound/receive';
 *   $route['api/whatsapp_inbound/recent']  = 'WhatsappInbound/recent';
 *
 * Additive sibling to the existing Whatsapp.php (which is NOT touched).
 * Receives an inbound message, stores it in whatsapp_inbound_v2, and attempts
 * to match the sender to a real lead via company_contact_master.phoneno.
 *
 * Auth: Bearer (master token, api_token row, or per-user JWT).
 * Standing rules: ASCII only, "Rs" for rupees, "percent" spelled out.
 */
class WhatsappInbound extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('AIAgents/WhatsappInbound_model', 'wai');
        $this->load->library('BearerAuth');
        $this->bearerauth->require_valid_token();
        $this->output->set_content_type('application/json');
    }

    public function probe() {
        echo json_encode(array('ok' => true) + $this->wai->manifest());
    }

    /**
     * Receive an inbound message. Accepts JSON body or form fields:
     *   from_phone (required), from_name, message_body, media_url,
     *   media_type, provider_message_id, to_phone_number_id
     */
    public function receive() {
        $raw = $this->input->raw_input_stream;
        $body = array();
        if (!empty($raw)) {
            $decoded = json_decode($raw, true);
            if (is_array($decoded)) $body = $decoded;
        }
        $get = function($k) use ($body) {
            if (isset($body[$k])) return $body[$k];
            $v = $this->input->post($k);
            if ($v !== null) return $v;
            return $this->input->get($k);
        };

        $from_phone = (string)$get('from_phone');
        if ($from_phone === '') {
            echo json_encode(array('ok' => false, 'error' => 'from_phone required'));
            return;
        }
        $payload = array(
            'from_phone'          => $from_phone,
            'from_name'           => $get('from_name'),
            'to_phone_number_id'  => $get('to_phone_number_id'),
            'message_body'        => $get('message_body'),
            'media_url'           => $get('media_url'),
            'media_type'          => $get('media_type'),
            'provider_message_id' => $get('provider_message_id'),
        );
        $res = $this->wai->store($payload);
        echo json_encode(array(
            'ok'       => true,
            'id'       => $res['id'],
            'matched'  => ($res['match']['lead_id'] !== null),
            'match'    => $res['match'],
        ));
    }

    public function recent() {
        $limit = (int)$this->input->get('limit');
        $rows = $this->wai->recent($limit);
        echo json_encode(array(
            'ok'     => true,
            'count'  => count($rows),
            'recent' => $rows,
        ));
    }
}

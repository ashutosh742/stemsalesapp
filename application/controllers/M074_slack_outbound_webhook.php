<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * M074 Slack Outbound Webhook Controller
 * Routes (no /api/ prefix):
 *   POST /slack/webhook_add
 *   POST /slack/webhook_test
 *   POST /slack/subscription_set
 *   POST /slack/dispatch        (internal, called by other modules)
 *   GET  /slack/log_for_admin
 */
class M074_slack_outbound_webhook extends CI_Controller
{
    private $_bearer = '4eBaiAT7r4zu6OK3b8evjLNia1D7RGgb0qRTuLJfUSo';

    public function __construct()
    {
        parent::__construct();
        $this->load->database();
        $this->load->helper('url');
        $this->_check_auth();
    }
    private function _auth()
    {
        if (function_exists('authunify_ok') && authunify_ok()) { return true; } // rimlyproof_authunify_20260609

        // Load custom config if not loaded
        @$this->config->load('custom', false, true);
        $token = $this->config->item('stem_digest_token');
        if (!$token) { $token = $this->config->item('csr_bearer_token'); }
        if (!$token) { $token = getenv('STEM_DIGEST_TOKEN'); }
        if (!$token) { $token = '4eBaiAT7r4zu6OK3b8evjLNia1D7RGgb0qRTuLJfUSo'; }
        $header = '';
        if (isset($_SERVER['HTTP_AUTHORIZATION'])) { $header = $_SERVER['HTTP_AUTHORIZATION']; }
        if (!$header && isset($_SERVER['REDIRECT_HTTP_AUTHORIZATION'])) { $header = $_SERVER['REDIRECT_HTTP_AUTHORIZATION']; }
        if (!$header && function_exists('getallheaders')) {
            foreach (getallheaders() as $k => $v) {
                if (strcasecmp($k, 'Authorization') === 0) { $header = $v; break; }
            }
        }
        $provided = trim(str_replace(array('Bearer ', 'Bearer'), '', $header));
        if (!$token || $provided !== $token) {
            $this->output->set_status_header(401)->set_content_type('application/json')->set_output(json_encode(array('ok'=>false,'error'=>'unauthorised')));
            return false;
        }
        return true;
    }



    // ------------------------------------------------------------------ auth

    private function _check_auth()
    {
        $header = isset($_SERVER['HTTP_AUTHORIZATION']) ? $_SERVER['HTTP_AUTHORIZATION'] : '';
        if (!$header) {
            $header = isset($_SERVER['REDIRECT_HTTP_AUTHORIZATION']) ? $_SERVER['REDIRECT_HTTP_AUTHORIZATION'] : '';
        }
        if (strpos($header, 'Bearer ') !== 0) {
            $this->_json(array('ok' => false, 'error' => 'unauthorised'), 401);
            exit;
        }
        $token = trim(substr($header, 7));
        if ($token !== $this->_bearer) {
            $this->_json(array('ok' => false, 'error' => 'forbidden'), 403);
            exit;
        }
    }

    // ------------------------------------------------------------------ helpers

    private function _json($data, $code = 200)
    {
        $this->output
             ->set_status_header($code)
             ->set_content_type('application/json', 'utf-8')
             ->set_output(json_encode($data, JSON_UNESCAPED_UNICODE));
    }

    private function _feature_flag($flag)
    {
        $row = $this->db->get_where('feature_flag', array('flag_name' => $flag, 'enabled' => 1))->row_array();
        return !empty($row);
    }

    private function _is_admin($uid)
    {
        if (!$uid) return false;
        $u = $this->db->get_where('user', array('uid' => $uid))->row_array();
        return $u && in_array((int)$u['type_id'], array(1, 24));
    }

    // ------------------------------------------------------------------ POST /slack/webhook_add  (admin only)

    public function webhook_add()
    {
        if ($this->input->server('REQUEST_METHOD') !== 'POST') {
            $this->_json(array('ok' => false, 'error' => 'post_required'), 405);
            return;
        }

        $caller_uid   = (int)$this->input->post('caller_uid');
        if (!$this->_is_admin($caller_uid)) {
            $this->_json(array('ok' => false, 'error' => 'admin_only'), 403);
            return;
        }

        $name         = trim((string)$this->input->post('name'));
        $url          = trim((string)$this->input->post('url'));
        $channel_hint = trim((string)$this->input->post('channel_hint'));

        if (!$name || !$url) {
            $this->_json(array('ok' => false, 'error' => 'missing_name_or_url'), 400);
            return;
        }

        $row = array(
            'name'            => $name,
            'webhook_url'     => $url,
            'channel_hint'    => $channel_hint ?: '',
            'enabled'         => 0,
            'created_by_uid'  => $caller_uid,
            'created_at'      => date('Y-m-d H:i:s'),
        );
        $this->db->insert('slack_webhook', $row);
        $webhook_id = $this->db->insert_id();

        $this->_json(array('ok' => true, 'webhook_id' => $webhook_id, 'message' => 'Webhook added. Enable it when ready.'));
    }

    // ------------------------------------------------------------------ POST /slack/webhook_test

    public function webhook_test()
    {
        if ($this->input->server('REQUEST_METHOD') !== 'POST') {
            $this->_json(array('ok' => false, 'error' => 'post_required'), 405);
            return;
        }

        $webhook_id = (int)$this->input->post('webhook_id');
        if (!$webhook_id) {
            $this->_json(array('ok' => false, 'error' => 'missing_webhook_id'), 400);
            return;
        }

        $webhook = $this->db->get_where('slack_webhook', array('id' => $webhook_id))->row_array();
        if (!$webhook) {
            $this->_json(array('ok' => false, 'error' => 'webhook_not_found'), 404);
            return;
        }

        $payload = json_encode(array(
            'text'    => 'STEM CRM Slack test message',
            'channel' => $webhook['channel_hint'] ?: '#general',
        ));

        $http_status   = 200;
        $response_body = 'demo_mode_no_real_send';

        if ($this->_feature_flag('slack_live') && $webhook['webhook_url'] !== 'REPLACE_ME') {
            // Real HTTP call
            $ch = curl_init($webhook['webhook_url']);
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
            curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_TIMEOUT, 10);
            $response_body = curl_exec($ch);
            $http_status   = (int)curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);
        }

        $log = array(
            'webhook_id'    => $webhook_id,
            'event_type'    => 'test',
            'payload_json'  => $payload,
            'http_status'   => $http_status,
            'response_body' => $response_body,
            'sent_at'       => date('Y-m-d H:i:s'),
        );
        $this->db->insert('slack_send_log', $log);

        $this->_json(array(
            'ok'          => true,
            'http_status' => $http_status,
            'response'    => $response_body,
            'mode'        => $this->_feature_flag('slack_live') ? 'live' : 'demo',
        ));
    }

    // ------------------------------------------------------------------ POST /slack/subscription_set

    public function subscription_set()
    {
        if ($this->input->server('REQUEST_METHOD') !== 'POST') {
            $this->_json(array('ok' => false, 'error' => 'post_required'), 405);
            return;
        }

        $event_type  = trim((string)$this->input->post('event_type'));
        $webhook_id  = (int)$this->input->post('webhook_id');
        $filter_json = trim((string)$this->input->post('filter_json')) ?: '{}';
        $enabled     = (int)$this->input->post('enabled');

        $allowed_events = array('won_closure','grade_d_bd','sla_breach','approval_pending','daily_digest','custom');
        if (!in_array($event_type, $allowed_events)) {
            $this->_json(array('ok' => false, 'error' => 'invalid_event_type', 'allowed' => $allowed_events), 400);
            return;
        }
        if (!$webhook_id) {
            $this->_json(array('ok' => false, 'error' => 'missing_webhook_id'), 400);
            return;
        }

        // Upsert: one subscription per event+webhook combo
        $existing = $this->db->get_where('slack_event_subscription', array(
            'event_type' => $event_type,
            'webhook_id' => $webhook_id,
        ))->row_array();

        if ($existing) {
            $this->db->where('id', $existing['id'])->update('slack_event_subscription', array(
                'filter_json' => $filter_json,
                'enabled'     => $enabled ? 1 : 0,
            ));
            $sub_id = $existing['id'];
        } else {
            $this->db->insert('slack_event_subscription', array(
                'event_type'  => $event_type,
                'webhook_id'  => $webhook_id,
                'filter_json' => $filter_json,
                'enabled'     => $enabled ? 1 : 0,
            ));
            $sub_id = $this->db->insert_id();
        }

        $this->_json(array('ok' => true, 'subscription_id' => $sub_id, 'event_type' => $event_type, 'enabled' => $enabled));
    }

    // ------------------------------------------------------------------ POST /slack/dispatch  (internal)

    public function dispatch()
    {
        if ($this->input->server('REQUEST_METHOD') !== 'POST') {
            $this->_json(array('ok' => false, 'error' => 'post_required'), 405);
            return;
        }

        $event_type   = trim((string)$this->input->post('event_type'));
        $payload_data = $this->input->post('payload') ?: array();
        if (is_string($payload_data)) {
            $payload_data = json_decode($payload_data, true) ?: array();
        }

        if (!$event_type) {
            $this->_json(array('ok' => false, 'error' => 'missing_event_type'), 400);
            return;
        }

        // Get enabled subscriptions for this event
        $subs = $this->db->where('event_type', $event_type)
                         ->where('enabled', 1)
                         ->get('slack_event_subscription')
                         ->result_array();

        $sent  = 0;
        $fails = 0;
        $live  = $this->_feature_flag('slack_live');

        foreach ($subs as $sub) {
            $webhook = $this->db->get_where('slack_webhook', array('id' => $sub['webhook_id'], 'enabled' => 1))->row_array();
            if (!$webhook) continue;

            $slack_payload = json_encode(array(
                'text'        => "[STEM CRM] Event: {$event_type}",
                'channel'     => $webhook['channel_hint'] ?: '#general',
                'attachments' => array(array('text' => json_encode($payload_data))),
            ));

            $http_status   = 200;
            $response_body = 'demo_mode_no_real_send';

            if ($live && $webhook['webhook_url'] !== 'REPLACE_ME') {
                $ch = curl_init($webhook['webhook_url']);
                curl_setopt($ch, CURLOPT_POST, 1);
                curl_setopt($ch, CURLOPT_POSTFIELDS, $slack_payload);
                curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                curl_setopt($ch, CURLOPT_TIMEOUT, 8);
                $response_body = curl_exec($ch);
                $http_status   = (int)curl_getinfo($ch, CURLINFO_HTTP_CODE);
                curl_close($ch);
            }

            $this->db->insert('slack_send_log', array(
                'webhook_id'    => $sub['webhook_id'],
                'event_type'    => $event_type,
                'payload_json'  => $slack_payload,
                'http_status'   => $http_status,
                'response_body' => $response_body,
                'sent_at'       => date('Y-m-d H:i:s'),
            ));

            if ($http_status >= 200 && $http_status < 300) $sent++; else $fails++;
        }

        $this->_json(array('ok' => true, 'sent' => $sent, 'fails' => $fails, 'subscriptions_matched' => count($subs)));
    }

    // ------------------------------------------------------------------ GET /slack/log_for_admin

    public function log_for_admin()
    {
        $rows = $this->db->order_by('sent_at', 'DESC')
                         ->limit(50)
                         ->get('slack_send_log')
                         ->result_array();
        $this->_json(array('ok' => true, 'log' => $rows ?: array()));
    }
}

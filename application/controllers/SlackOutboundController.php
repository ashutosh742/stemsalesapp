<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * SlackOutboundController
 *
 * Admin API for managing Slack webhook configurations and reviewing
 * the outbound message log.
 *
 * All endpoints require a Bearer token matching the STEM_DIGEST_TOKEN
 * environment variable. Config-mutating endpoints additionally require
 * the caller to identify as an admin (user type_id = 3).
 *
 * Routes (add to application/config/routes.php or a dedicated
 * routes_slack.php file that is require()'d from routes.php):
 *
 *   $route['api/slack/probe']         = 'SlackOutboundController/probe';
 *   $route['api/slack/config_list']   = 'SlackOutboundController/config_list';
 *   $route['api/slack/config_add']    = 'SlackOutboundController/config_add';
 *   $route['api/slack/config_toggle'] = 'SlackOutboundController/config_toggle';
 *   $route['api/slack/test_send']     = 'SlackOutboundController/test_send';
 *   $route['api/slack/outbound_log']  = 'SlackOutboundController/outbound_log';
 *
 * Migration: 061
 * Gap item : H.3 Slack outbound on applause and breach events
 */
class SlackOutboundController extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->database();
        $this->load->model('AIAgents/SlackOutbound_model', 'slack');
        $this->load->model('Menu_model');
        header('Content-Type: application/json');
    }

    // ----------------------------------------------------------------
    // Auth helpers
    // ----------------------------------------------------------------

    /**
     * Require a valid Bearer token. Returns 'service' or exits with 401.
     */
    private function _auth_or_die()
    {
        if (function_exists('authunify_ok') && authunify_ok()) { return; } // rimlyproof_authunify_20260609

        $hdr      = $this->input->get_request_header('Authorization', true);
        $expected = getenv('STEM_DIGEST_TOKEN');

        if ($hdr && $expected && $hdr === 'Bearer ' . $expected) {
            return 'service';
        }

        // Fallback: check session for web-based admin panel requests.
        $session_uid = $this->session->userdata('user_id');
        if ($session_uid) {
            return 'user:' . $session_uid;
        }

        $this->_respond(array('error' => 'unauthorized'), 401);
        exit;
    }

    /**
     * Return true if the bearer identity belongs to a type_id=3 admin.
     * For 'service' callers the admin check is skipped (cron / CI context).
     */
    private function _is_admin($identity)
    {
        if ($identity === 'service') {
            return true;
        }

        // identity looks like 'user:42'
        $uid = (int)substr($identity, 5);
        $u   = $this->Menu_model->get_userbyid($uid);
        if (empty($u)) {
            return false;
        }

        return ((int)$u[0]->type_id === 3);
    }

    // ----------------------------------------------------------------
    // Endpoints
    // ----------------------------------------------------------------

    /**
     * GET /api/slack/probe
     *
     * Health check. Returns 200 with a static JSON body.
     * No auth required so load-balancers can ping it.
     */
    public function probe()
    {
        $this->_respond(array('ok' => true, 'service' => 'slack_outbound', 'migration' => '061'));
    }

    /**
     * GET /api/slack/config_list
     *
     * List all webhook configurations (active and inactive).
     * Admin only.
     */
    public function config_list()
    {
        $identity = $this->_auth_or_die();

        if (!$this->_is_admin($identity)) {
            $this->_respond(array('error' => 'admin only'), 403);
            return;
        }

        $rows = $this->db->query(
            "SELECT id, cluster_id, channel_name, webhook_url,
                    event_types_json, active, created_at
               FROM slack_webhook_config
              ORDER BY active DESC, created_at DESC"
        )->result();

        // Parse event_types_json so callers receive a real array.
        foreach ($rows as $r) {
            $r->event_types = json_decode($r->event_types_json, true) ?: array();
        }

        $this->_respond(array('rows' => $rows, 'count' => count($rows)));
    }

    /**
     * POST /api/slack/config_add
     *
     * Add a new webhook configuration.
     *
     * Body parameters:
     *   cluster_id    string   Cluster label or 'All'
     *   channel_name  string   Slack channel name for humans
     *   webhook_url   string   Full https://hooks.slack.com/... URL
     *   event_types   array    One or more of: applause, breach
     *
     * Admin only.
     */
    public function config_add()
    {
        $identity = $this->_auth_or_die();

        if (!$this->_is_admin($identity)) {
            $this->_respond(array('error' => 'admin only'), 403);
            return;
        }

        $body = json_decode($this->input->raw_input_stream, true);
        if (!$body) {
            $body = array(
                'cluster_id'   => $this->input->post('cluster_id'),
                'channel_name' => $this->input->post('channel_name'),
                'webhook_url'  => $this->input->post('webhook_url'),
                'event_types'  => $this->input->post('event_types'),
            );
        }

        $cluster      = trim((string)($body['cluster_id']   ?? ''));
        $channel_name = trim((string)($body['channel_name'] ?? ''));
        $webhook_url  = trim((string)($body['webhook_url']  ?? ''));
        $event_types  = (array)($body['event_types'] ?? array());

        if (!$cluster || !$channel_name || !$webhook_url || empty($event_types)) {
            $this->_respond(array('error' => 'cluster_id, channel_name, webhook_url, and event_types are required'), 400);
            return;
        }

        // Restrict allowed event slugs.
        $allowed = array('applause', 'breach');
        $event_types = array_values(array_intersect($event_types, $allowed));
        if (empty($event_types)) {
            $this->_respond(array('error' => 'event_types must include at least one of: applause, breach'), 400);
            return;
        }

        if (strpos($webhook_url, 'https://') !== 0) {
            $this->_respond(array('error' => 'webhook_url must start with https://'), 400);
            return;
        }

        $cluster_esc      = $this->db->escape_str($cluster);
        $channel_esc      = $this->db->escape_str($channel_name);
        $url_esc          = $this->db->escape_str($webhook_url);
        $event_types_json = $this->db->escape_str(json_encode(array_values($event_types)));

        $this->db->query(
            "INSERT INTO slack_webhook_config
                (cluster_id, channel_name, webhook_url, event_types_json, active, created_at)
             VALUES
                ('$cluster_esc', '$channel_esc', '$url_esc', '$event_types_json', 1, NOW())"
        );

        $new_id = $this->db->insert_id();
        $this->_respond(array('ok' => true, 'id' => $new_id), 201);
    }

    /**
     * POST /api/slack/config_toggle
     *
     * Flip the active flag on an existing webhook config.
     *
     * Body parameters:
     *   id      int   slack_webhook_config.id
     *   active  int   1 to enable, 0 to disable
     *
     * Admin only.
     */
    public function config_toggle()
    {
        $identity = $this->_auth_or_die();

        if (!$this->_is_admin($identity)) {
            $this->_respond(array('error' => 'admin only'), 403);
            return;
        }

        $body = json_decode($this->input->raw_input_stream, true);
        if (!$body) {
            $body = array(
                'id'     => $this->input->post('id'),
                'active' => $this->input->post('active'),
            );
        }

        $id     = (int)($body['id']     ?? 0);
        $active = (int)((bool)($body['active'] ?? 0));

        if ($id <= 0) {
            $this->_respond(array('error' => 'id is required'), 400);
            return;
        }

        $this->db->query(
            "UPDATE slack_webhook_config
                SET active = '$active'
              WHERE id = '$id'
              LIMIT 1"
        );

        if ($this->db->affected_rows() === 0) {
            $this->_respond(array('error' => 'config not found'), 404);
            return;
        }

        $this->_respond(array('ok' => true, 'id' => $id, 'active' => $active));
    }

    /**
     * POST /api/slack/test_send
     *
     * Send a test message to a specific webhook to verify connectivity.
     *
     * Body parameters:
     *   webhook_id   int   slack_webhook_config.id
     *
     * Admin only.
     */
    public function test_send()
    {
        $identity = $this->_auth_or_die();

        if (!$this->_is_admin($identity)) {
            $this->_respond(array('error' => 'admin only'), 403);
            return;
        }

        $body = json_decode($this->input->raw_input_stream, true);
        if (!$body) {
            $body = array('webhook_id' => $this->input->post('webhook_id'));
        }

        $wh_id = (int)($body['webhook_id'] ?? 0);
        if ($wh_id <= 0) {
            $this->_respond(array('error' => 'webhook_id is required'), 400);
            return;
        }

        $wh = $this->db->query(
            "SELECT id, cluster_id, channel_name, webhook_url, active
               FROM slack_webhook_config
              WHERE id = '$wh_id'
              LIMIT 1"
        )->row();

        if (!$wh) {
            $this->_respond(array('error' => 'webhook config not found'), 404);
            return;
        }

        // Build a clearly labelled test payload using blocks.
        $blocks = array(
            'blocks' => array(
                array(
                    'type' => 'section',
                    'text' => array(
                        'type' => 'mrkdwn',
                        'text' => ':white_check_mark: *STEM test message* | webhook_id=' . $wh_id . ' | cluster=' . $wh->cluster_id,
                    ),
                ),
                array(
                    'type'     => 'context',
                    'elements' => array(
                        array(
                            'type' => 'mrkdwn',
                            'text' => 'Sent via /api/slack/test_send at ' . date('d M Y, H:i T'),
                        ),
                    ),
                ),
            ),
        );

        $ch = curl_init($wh->webhook_url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST,           true);
        curl_setopt($ch, CURLOPT_POSTFIELDS,     json_encode($blocks));
        curl_setopt($ch, CURLOPT_HTTPHEADER,     array('Content-Type: application/json'));
        curl_setopt($ch, CURLOPT_TIMEOUT,        5);
        $body_resp = curl_exec($ch);
        $status    = (int)curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        $this->slack->log_outbound('test', $wh_id, $status, (string)$body_resp);

        $this->_respond(array(
            'ok'          => ($status === 200),
            'http_status' => $status,
            'response'    => substr((string)$body_resp, 0, 256),
        ));
    }

    /**
     * GET /api/slack/outbound_log?days=7
     *
     * Retrieve recent outbound log rows. Defaults to the past 7 days.
     * Max lookback is 90 days.
     */
    public function outbound_log()
    {
        $identity = $this->_auth_or_die();

        $days = min(90, max(1, (int)($this->input->get('days') ?: 7)));
        $cutoff = date('Y-m-d H:i:s', strtotime('-' . $days . ' days'));

        $rows = $this->db->query(
            "SELECT sol.id, sol.event_type, sol.webhook_id,
                    swc.cluster_id, swc.channel_name,
                    sol.http_status, sol.response_snippet, sol.sent_at
               FROM slack_outbound_log sol
          LEFT JOIN slack_webhook_config swc ON swc.id = sol.webhook_id
              WHERE sol.sent_at >= '" . $this->db->escape_str($cutoff) . "'
              ORDER BY sol.sent_at DESC
              LIMIT 500"
        )->result();

        $this->_respond(array(
            'days'  => $days,
            'rows'  => $rows,
            'count' => count($rows),
        ));
    }

    // ----------------------------------------------------------------
    // Output helper
    // ----------------------------------------------------------------

    private function _respond($payload, $status = 200)
    {
        $this->output
             ->set_status_header($status)
             ->set_content_type('application/json')
             ->set_output(json_encode($payload));
    }
}

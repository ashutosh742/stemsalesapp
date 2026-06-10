<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * SlackOutbound_model
 *
 * Sends Slack messages via incoming webhooks for two event classes:
 *   - applause : a BD closed or helped close a deal (won_closure)
 *   - breach   : a BD has leads stuck past the allowed threshold
 *
 * Webhook URLs live entirely in the slack_webhook_config table.
 * No URL is ever hardcoded here.
 *
 * Slack message format: a section block with the headline text, followed
 * by a context block carrying the timestamp and event type.
 *
 * All failures are silent (logged to CI log, never thrown).
 *
 * CodeIgniter 3 idioms throughout.
 *
 * Migration: 061
 * Gap item : H.3 Slack outbound on applause and breach events
 */
class SlackOutbound_model extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
        $this->load->database();
    }

    // ----------------------------------------------------------------
    // Public send methods
    // ----------------------------------------------------------------

    /**
     * Send an applause notification for a won closure.
     *
     * Called after the in-app and email notifications have been sent so
     * that Slack is an additive channel, never a blocker.
     *
     * @param int    $applause_id  Row ID from applause_log
     * @param string $bd_name      BD display name
     * @param string $school       School / company name
     * @param float  $amount_rs    Deal value in Rs (0 if unknown)
     */
    public function send_applause($applause_id, $bd_name, $school, $amount_rs)
    {
        $amount_rs = (float)$amount_rs;
        if ($amount_rs > 0) {
            $value_text = ' - Rs ' . number_format($amount_rs, 0, '.', ',');
        } else {
            $value_text = '';
        }

        $headline = ':tada: *' . $bd_name . '* closed *' . $school . '*' . $value_text;

        $context = 'applause #' . (int)$applause_id
            . ' | ' . date('d M Y, H:i T');

        $payload = $this->_build_blocks($headline, $context);

        $webhooks = $this->_get_webhooks_for_event('applause');
        foreach ($webhooks as $wh) {
            $result = $this->_curl_post($wh->webhook_url, $payload);
            $this->log_outbound(
                'applause',
                (int)$wh->id,
                $result['http_status'],
                $result['snippet']
            );
        }
    }

    /**
     * Send a breach notification when a BD has leads stuck past threshold.
     *
     * @param int    $cid_id      init_call_id of the stuck lead
     * @param string $bd_name     BD display name
     * @param string $stage       Stage name (e.g. "Positive")
     * @param int    $days_overdue Days past the allowed threshold
     */
    public function send_breach($cid_id, $bd_name, $stage, $days_overdue)
    {
        $days_overdue = (int)$days_overdue;

        $headline = ':warning: *Breach* | ' . $bd_name
            . ' | Lead #' . (int)$cid_id
            . ' stuck at ' . $stage
            . ' for ' . $days_overdue . ' day'
            . ($days_overdue === 1 ? '' : 's')
            . ' (over threshold)';

        $context = 'breach | cid=' . (int)$cid_id
            . ' | ' . date('d M Y, H:i T');

        $payload = $this->_build_blocks($headline, $context);

        $webhooks = $this->_get_webhooks_for_event('breach');
        foreach ($webhooks as $wh) {
            $result = $this->_curl_post($wh->webhook_url, $payload);
            $this->log_outbound(
                'breach',
                (int)$wh->id,
                $result['http_status'],
                $result['snippet']
            );
        }
    }

    /**
     * Write one row to slack_outbound_log.
     *
     * @param string $event_type  'applause' or 'breach'
     * @param int    $webhook_id  slack_webhook_config.id
     * @param int    $http_status HTTP status returned by Slack
     * @param string $snippet     First 256 chars of Slack response body
     */
    public function log_outbound($event_type, $webhook_id, $http_status, $snippet)
    {
        $this->db->query(
            "INSERT INTO slack_outbound_log
                (event_type, payload_json, webhook_id, http_status, response_snippet, sent_at)
             VALUES (
                '" . $this->db->escape_str($event_type) . "',
                '',
                '" . (int)$webhook_id . "',
                '" . (int)$http_status . "',
                '" . $this->db->escape_str(substr($snippet, 0, 256)) . "',
                NOW()
             )"
        );
    }

    // ----------------------------------------------------------------
    // Internal helpers
    // ----------------------------------------------------------------

    /**
     * Return all active webhook configs that carry the given event slug.
     *
     * The event_types_json column holds a JSON array such as
     * ["applause","breach"]. MySQL JSON_CONTAINS is used when available;
     * the fallback LIKE guard keeps it working on MySQL 5.6.
     *
     * @param  string $event_slug  'applause' or 'breach'
     * @return array  Array of stdClass rows from slack_webhook_config
     */
    private function _get_webhooks_for_event($event_slug)
    {
        $slug = $this->db->escape_str($event_slug);

        // JSON_CONTAINS is reliable on MySQL 5.7+ (production target).
        // The LIKE fallback is an extra safety net for older environments.
        $rows = $this->db->query(
            "SELECT id, cluster_id, channel_name, webhook_url
               FROM slack_webhook_config
              WHERE active = 1
                AND (
                    JSON_CONTAINS(event_types_json, '\"" . $slug . "\"', '$')
                    OR event_types_json LIKE '%" . $slug . "%'
                )"
        )->result();

        if (empty($rows)) {
            log_message('info', '[SlackOutbound] no active webhooks for event=' . $event_slug);
        }

        return $rows;
    }

    /**
     * Build a Slack blocks payload with a section block and a context block.
     *
     * @param  string $headline  Main message text (supports Slack mrkdwn)
     * @param  string $context   Secondary context line
     * @return string            JSON string ready to POST
     */
    private function _build_blocks($headline, $context)
    {
        $body = array(
            'blocks' => array(
                array(
                    'type' => 'section',
                    'text' => array(
                        'type' => 'mrkdwn',
                        'text' => $headline,
                    ),
                ),
                array(
                    'type'     => 'context',
                    'elements' => array(
                        array(
                            'type' => 'mrkdwn',
                            'text' => $context,
                        ),
                    ),
                ),
            ),
        );

        return json_encode($body);
    }

    /**
     * POST JSON to a Slack webhook URL using cURL.
     *
     * Returns an associative array:
     *   'http_status' => int   (0 if cURL itself failed)
     *   'snippet'     => string (first 256 chars of body, or error message)
     *
     * All errors are swallowed; the caller only logs them.
     *
     * @param  string $url      Slack webhook URL (from DB config)
     * @param  string $json     JSON payload string
     * @return array
     */
    private function _curl_post($url, $json)
    {
        $result = array('http_status' => 0, 'snippet' => '');

        if (empty($url)) {
            $result['snippet'] = 'empty webhook url';
            return $result;
        }

        $ch = curl_init($url);
        if ($ch === false) {
            $result['snippet'] = 'curl_init failed';
            return $result;
        }

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST,           true);
        curl_setopt($ch, CURLOPT_POSTFIELDS,     $json);
        curl_setopt($ch, CURLOPT_HTTPHEADER,     array('Content-Type: application/json'));
        curl_setopt($ch, CURLOPT_TIMEOUT,        5);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 3);

        $body   = curl_exec($ch);
        $errno  = curl_errno($ch);
        $status = (int)curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($errno !== 0) {
            $result['snippet'] = 'curl error ' . $errno;
            log_message('error', '[SlackOutbound] curl error ' . $errno . ' posting to ' . $url);
            return $result;
        }

        $result['http_status'] = $status;
        $result['snippet']     = substr((string)$body, 0, 256);

        if ($status !== 200) {
            log_message('error', '[SlackOutbound] HTTP ' . $status . ' from ' . $url . ': ' . $result['snippet']);
        }

        return $result;
    }
}

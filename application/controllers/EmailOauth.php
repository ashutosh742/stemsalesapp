<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * STEM CRM - Migration 030 - Email OAuth Controller
 *
 * REST API endpoints for email OAuth connect, inbox, and AI insights.
 * Follows the same CodeIgniter controller pattern used in mig 027 (Comm Orchestrator).
 *
 * Endpoints:
 *   POST   https://stemapp.in/api/email_oauth/connect          connect Gmail or Outlook
 *   GET    https://stemapp.in/api/email_oauth/callback         OAuth callback from provider
 *   POST   https://stemapp.in/api/email_oauth/disconnect       revoke connection
 *   GET    https://stemapp.in/api/email_oauth/status           connection status for current user
 *   GET    https://stemapp.in/api/email/inbox                  inbox for a lead (lead_id, limit)
 *   GET    https://stemapp.in/api/email/insight                AI insight for a message (message_id)
 *   POST   https://stemapp.in/api/email/insight/action_taken   mark insight acted on
 *
 * Auth: all endpoints require existing STEM CRM session (uid from session).
 *       BDs can only read their own data. CMs can read data for their team leads.
 *
 * Plain English. No em-dashes. No non-ASCII. Rs for rupees.
 *
 * Author: STEM Learning ops
 * Date: 2026-05-19
 */
class Email_oauth extends CI_Controller
{
    private $log_prefix = '[email_oauth_controller]';

    public function __construct()
    {
        parent::__construct();
        $this->load->model('Email_oauth_agent');
        $this->load->model('Email_insight_agent');
        $this->load->library('session');
        $this->load->helper(['url', 'security']);
    }

    // ========================================================================
    // POST https://stemapp.in/api/email_oauth/connect
    // Body: provider=gmail|outlook
    // Returns JSON { ok: true, redirect_url: "..." }
    // or redirects the client directly.
    // ========================================================================
    public function connect()
    {
        $this->_require_post();
        $uid      = $this->_require_auth();
        $provider = $this->_clean_input('provider');

        if (!in_array($provider, ['gmail', 'outlook'])) {
            return $this->_json(['ok' => false, 'error' => 'invalid_provider'], 400);
        }

        // Phase gate check
        if (!$this->_email_feature_enabled($uid)) {
            return $this->_json(['ok' => false, 'error' => 'feature_not_enabled'], 403);
        }

        $auth_url = $this->Email_oauth_agent->oauth_connect($uid, $provider);
        if (!$auth_url) {
            return $this->_json(['ok' => false, 'error' => 'oauth_build_failed'], 500);
        }

        log_message('info', $this->log_prefix . ' connect initiated uid=' . $uid
            . ' provider=' . $provider);

        return $this->_json(['ok' => true, 'redirect_url' => $auth_url]);
    }

    // ========================================================================
    // GET https://stemapp.in/api/email_oauth/callback
    // Query params: code, state (set by Google/Microsoft redirect)
    // Exchanges code, stores tokens, redirects to settings screen.
    // ========================================================================
    public function callback()
    {
        $code  = $this->input->get('code');
        $state = $this->input->get('state');
        $error = $this->input->get('error');

        if ($error) {
            log_message('error', $this->log_prefix . ' OAuth callback error=' . $error);
            redirect('settings/email?status=oauth_error&reason=' . urlencode($error));
            return;
        }

        if (empty($code) || empty($state)) {
            redirect('settings/email?status=oauth_error&reason=missing_code');
            return;
        }

        $result = $this->Email_oauth_agent->oauth_exchange_code($code, $state);

        if (!$result['ok']) {
            log_message('error', $this->log_prefix . ' code exchange failed: '
                . ($result['error'] ?? 'unknown'));
            redirect('settings/email?status=oauth_error&reason=' . urlencode($result['error'] ?? 'exchange_failed'));
            return;
        }

        log_message('info', $this->log_prefix . ' callback success uid=' . $result['uid']
            . ' provider=' . $result['provider']);

        redirect('settings/email?status=connected&provider=' . $result['provider']);
    }

    // ========================================================================
    // POST https://stemapp.in/api/email_oauth/disconnect
    // Body: provider=gmail|outlook
    // Returns JSON { ok: true } or error.
    // ========================================================================
    public function disconnect()
    {
        $this->_require_post();
        $uid      = $this->_require_auth();
        $provider = $this->_clean_input('provider');

        if (!in_array($provider, ['gmail', 'outlook'])) {
            return $this->_json(['ok' => false, 'error' => 'invalid_provider'], 400);
        }

        $result = $this->Email_oauth_agent->disconnect($uid, $provider);
        return $this->_json($result, $result['ok'] ? 200 : 404);
    }

    // ========================================================================
    // GET https://stemapp.in/api/email_oauth/status
    // Returns connection status for current user, both providers.
    // Response: {
    //   ok: true,
    //   uid: 42,
    //   gmail:   { status: "active", last_sync_at: "...", last_cal_sync_at: "..." },
    //   outlook: { status: "not_connected" }
    // }
    // ========================================================================
    public function status()
    {
        $this->_require_get();
        $uid = $this->_require_auth();

        $rows = $this->db->get_where('email_account_oauth', ['uid' => $uid])->result_array();

        $by_provider = ['gmail' => null, 'outlook' => null];
        foreach ($rows as $row) {
            $by_provider[$row['provider']] = $row;
        }

        $response = ['ok' => true, 'uid' => $uid];
        foreach (['gmail', 'outlook'] as $p) {
            $r = $by_provider[$p];
            if (!$r) {
                $response[$p] = ['status' => 'not_connected'];
            } else {
                $response[$p] = [
                    'status'          => $r['status'],
                    'last_sync_at'    => $r['last_sync_at'],
                    'last_cal_sync_at'=> $r['last_cal_sync_at'],
                    'revoked_reason'  => $r['status'] === 'revoked' ? $r['revoked_reason'] : null,
                ];
            }
        }

        return $this->_json($response);
    }

    // ========================================================================
    // GET https://stemapp.in/api/email/inbox?lead_id=&limit=
    // Returns last N emails for a lead, with insight data.
    // Default limit: 5. Max: 50.
    // CMs can request for any lead under their team.
    // BDs can only request for their own leads.
    // ========================================================================
    public function inbox()
    {
        $this->_require_get();
        $uid     = $this->_require_auth();
        $lead_id = (int)$this->input->get('lead_id');
        $limit   = min(50, max(1, (int)($this->input->get('limit') ?: 5)));

        if (!$lead_id) {
            return $this->_json(['ok' => false, 'error' => 'lead_id_required'], 400);
        }

        // Feature gate: UI requires flag >= 2 for insight chips, but inbox list
        // is visible at flag >= 1 (so CM can review during pilot).
        if (!$this->_email_feature_enabled($uid)) {
            return $this->_json(['ok' => false, 'error' => 'feature_not_enabled'], 403);
        }

        // Access check: BD can only see their own lead's emails.
        // CM can see emails for any lead under their team.
        if (!$this->_can_access_lead($uid, $lead_id)) {
            return $this->_json(['ok' => false, 'error' => 'access_denied'], 403);
        }

        $show_insights = $this->_get_user_flag($uid) >= 2;

        $sql = "
            SELECT
              eml.id              AS message_id,
              eml.uid             AS bd_uid,
              eml.provider,
              eml.thread_id,
              eml.direction,
              eml.from_addr,
              eml.to_addr,
              eml.subject,
              eml.body_snippet,
              eml.received_at,
              eml.attached_files_json,
              " . ($show_insights ? "
              ei.sentiment,
              ei.intent,
              ei.suggested_next_action,
              ei.confidence,
              ei.action_taken
              " : "
              NULL AS sentiment,
              NULL AS intent,
              NULL AS suggested_next_action,
              NULL AS confidence,
              NULL AS action_taken
              ") . "
            FROM email_message_log eml
            " . ($show_insights ? "LEFT JOIN email_insight ei ON ei.email_message_log_id = eml.id" : "") . "
            WHERE eml.lead_id = ?
            ORDER BY eml.received_at DESC
            LIMIT ?
        ";

        $messages = $this->db->query($sql, [$lead_id, $limit])->result_array();

        // Reverse to show oldest first in the returned list
        $messages = array_reverse($messages);

        return $this->_json([
            'ok'             => true,
            'lead_id'        => $lead_id,
            'count'          => count($messages),
            'insights_live'  => $show_insights,
            'messages'       => $messages,
        ]);
    }

    // ========================================================================
    // GET https://stemapp.in/api/email/insight?message_id=
    // Returns the AI insight for a single message.
    // Returns 404 if insight has not been generated yet.
    // ========================================================================
    public function insight()
    {
        $this->_require_get();
        $uid        = $this->_require_auth();
        $message_id = (int)$this->input->get('message_id');

        if (!$message_id) {
            return $this->_json(['ok' => false, 'error' => 'message_id_required'], 400);
        }

        if ($this->_get_user_flag($uid) < 2) {
            return $this->_json(['ok' => false, 'error' => 'insights_not_live_yet'], 403);
        }

        // Verify BD owns this message or is a CM over the lead
        $msg = $this->db->get_where('email_message_log', ['id' => $message_id])->row_array();
        if (!$msg) {
            return $this->_json(['ok' => false, 'error' => 'not_found'], 404);
        }
        if ($msg['uid'] != $uid && !$this->_is_cm_over_uid($uid, $msg['uid'])) {
            return $this->_json(['ok' => false, 'error' => 'access_denied'], 403);
        }

        $insight = $this->Email_insight_agent->get_insight_by_message_id($message_id);
        if (!$insight) {
            return $this->_json(['ok' => false, 'error' => 'insight_not_ready'], 404);
        }

        return $this->_json(['ok' => true, 'insight' => $insight]);
    }

    // ========================================================================
    // POST https://stemapp.in/api/email/insight/action_taken
    // Body: message_id=123
    // Marks the AI insight as actioned by the calling BD.
    // ========================================================================
    public function insight_action_taken()
    {
        $this->_require_post();
        $uid        = $this->_require_auth();
        $message_id = (int)$this->input->post('message_id');

        if (!$message_id) {
            return $this->_json(['ok' => false, 'error' => 'message_id_required'], 400);
        }

        // Verify ownership
        $msg = $this->db->get_where('email_message_log', ['id' => $message_id])->row_array();
        if (!$msg) {
            return $this->_json(['ok' => false, 'error' => 'not_found'], 404);
        }
        if ($msg['uid'] != $uid) {
            return $this->_json(['ok' => false, 'error' => 'access_denied'], 403);
        }

        // Get insight id from message
        $insight = $this->db->get_where('email_insight',
            ['email_message_log_id' => $message_id])->row_array();
        if (!$insight) {
            return $this->_json(['ok' => false, 'error' => 'insight_not_found'], 404);
        }

        $result = $this->Email_insight_agent->mark_action_taken($insight['id'], $uid);
        return $this->_json($result);
    }

    // ========================================================================
    // PRIVATE HELPERS
    // ========================================================================

    private function _require_auth()
    {
        $uid = (int)($this->session->userdata('uid') ?? 0);
        if (!$uid) {
            $this->_json(['ok' => false, 'error' => 'unauthenticated'], 401);
            exit;
        }
        return $uid;
    }

    private function _require_post()
    {
        if ($this->input->server('REQUEST_METHOD') !== 'POST') {
            $this->_json(['ok' => false, 'error' => 'method_not_allowed'], 405);
            exit;
        }
    }

    private function _require_get()
    {
        if ($this->input->server('REQUEST_METHOD') !== 'GET') {
            $this->_json(['ok' => false, 'error' => 'method_not_allowed'], 405);
            exit;
        }
    }

    private function _clean_input($field)
    {
        return htmlspecialchars(strip_tags($this->input->post($field) ?? ''), ENT_QUOTES, 'UTF-8');
    }

    private function _email_feature_enabled($uid)
    {
        return $this->_get_user_flag($uid) >= 1;
    }

    private function _get_user_flag($uid)
    {
        // Check per-uid override first
        $override = $this->db->get_where('feature_flag_override',
            ['uid' => $uid, 'flag_name' => 'email_capture_enabled'])->row_array();
        if ($override) {
            return (int)$override['flag_value'];
        }
        // Fall back to global feature_flag
        $global = $this->db->select('email_capture_enabled')->get('feature_flag')->row_array();
        return (int)($global['email_capture_enabled'] ?? 0);
    }

    private function _can_access_lead($requesting_uid, $lead_id)
    {
        // BD owns the lead directly
        $lead = $this->db->select('id, mainbd')
            ->get_where('init_call', ['id' => $lead_id])->row_array();
        if (!$lead) {
            return false;
        }
        if ($lead['mainbd'] == $requesting_uid) {
            return true;
        }
        // CM check: requesting_uid is the parent of the lead's BD
        return $this->_is_cm_over_uid($requesting_uid, $lead['mainbd']);
    }

    private function _is_cm_over_uid($cm_uid, $bd_uid)
    {
        $row = $this->db
            ->get_where('reporting_hierarchy', [
                'parent_uid'   => $cm_uid,
                'employee_uid' => $bd_uid,
                'active'       => 1,
            ])->row_array();
        return !empty($row);
    }

    private function _json($data, $status = 200)
    {
        http_response_code($status);
        header('Content-Type: application/json');
        echo json_encode($data, JSON_UNESCAPED_UNICODE);
    }
}
// End of Email_oauth controller

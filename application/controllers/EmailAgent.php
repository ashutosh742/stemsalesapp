<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * STEM Learning - Email Agent Controller
 * Migration 026 (Phase 1, live 1 Jun 2026)
 *
 * Routes:
 *   GET  /api/email/agent/probe
 *   GET  /api/email/agent/drafts_for_bd?bd_uid=&status=
 *   GET  /api/email/agent/draft/{id}
 *   POST /api/email/agent/draft/approve
 *   POST /api/email/agent/draft/discard
 *   POST /api/email/agent/regenerate                  (BD wants a fresh AI draft)
 *
 * OAuth routes:
 *   GET  /api/email/agent/oauth/start?bd_uid=         (returns Google consent URL)
 *   GET  /api/email/agent/oauth/callback              (Google redirect target)
 *   POST /api/email/agent/oauth/revoke                (BD revokes consent)
 *   GET  /api/email/agent/oauth/status?bd_uid=
 */
class Email_agent extends CI_Controller
{
    protected $token;

    public function __construct()
    {
        parent::__construct();
        $this->load->database();
        $this->load->library('Bearer_auth');
        $this->load->library('thank_you_email_agent');
        $this->load->helper('url');
        $this->config->load('email_agent_config', true, true);
        $this->token = $this->bearer_auth->get_bearer_token();
    }

    public function probe()
    {
        $this->_json($this->thank_you_email_agent->probe(), 200);
    }

    public function drafts_for_bd()
    {
        if (!$this->bearer_auth->verify($this->token)) return $this->_json(['error' => 'unauthorized'], 401);
        $bd_uid = (int)$this->input->get('bd_uid');
        $status = (string)$this->input->get('status');
        if ($bd_uid <= 0) return $this->_json(['error' => 'bd_uid_required'], 400);

        $this->db->select('d.id, d.cid_id, d.meeting_id, d.template_code, d.trigger_reason,
                           d.recipient_email, d.recipient_name, d.recipient_role,
                           d.subject_line, d.status, d.drafted_at, d.expires_at,
                           d.bd_reviewed_at, d.bd_edits_made,
                           ic.school_name')
            ->from('email_agent_draft d')
            ->join('init_call ic', 'ic.id = d.cid_id')
            ->where('d.bd_uid', $bd_uid);
        if ($status) {
            $this->db->where('d.status', $status);
        } else {
            $this->db->where_in('d.status', ['drafted','approved']);
        }
        $rows = $this->db->order_by('d.drafted_at', 'desc')->get()->result_array();
        $this->_json(['bd_uid' => $bd_uid, 'count' => count($rows), 'rows' => $rows], 200);
    }

    public function draft($id)
    {
        if (!$this->bearer_auth->verify($this->token)) return $this->_json(['error' => 'unauthorized'], 401);
        $id = (int)$id;
        $d = $this->db->select('*')->from('email_agent_draft')->where('id', $id)->get()->row_array();
        if (!$d) return $this->_json(['error' => 'not_found'], 404);
        $this->_json($d, 200);
    }

    public function draft_approve()
    {
        if (!$this->bearer_auth->verify($this->token)) return $this->_json(['error' => 'unauthorized'], 401);
        $draft_id = (int)$this->input->post('draft_id');
        $bd_uid   = (int)$this->input->post('bd_uid');
        $edits = [
            'subject_line' => $this->input->post('subject_line'),
            'body_plain'   => $this->input->post('body_plain'),
            'body_html'    => $this->input->post('body_html'),
        ];
        $edits = array_filter($edits, function($v){ return !empty($v); });
        $res = $this->thank_you_email_agent->approve_draft($draft_id, $bd_uid, $edits ?: null);
        $this->_json($res, $res['ok'] ? 200 : 400);
    }

    public function draft_discard()
    {
        if (!$this->bearer_auth->verify($this->token)) return $this->_json(['error' => 'unauthorized'], 401);
        $res = $this->thank_you_email_agent->discard_draft(
            (int)$this->input->post('draft_id'),
            (int)$this->input->post('bd_uid')
        );
        $this->_json($res, $res['ok'] ? 200 : 400);
    }

    public function regenerate()
    {
        if (!$this->bearer_auth->verify($this->token)) return $this->_json(['error' => 'unauthorized'], 401);
        $draft_id = (int)$this->input->post('draft_id');
        $bd_uid   = (int)$this->input->post('bd_uid');
        $extra    = (string)$this->input->post('extra_instructions');

        $d = $this->db->select('*')->from('email_agent_draft')->where('id', $draft_id)->get()->row_array();
        if (!$d) return $this->_json(['error' => 'not_found'], 404);
        if ((int)$d['bd_uid'] !== $bd_uid) return $this->_json(['error' => 'bd_mismatch'], 403);
        if ($d['status'] !== 'drafted') return $this->_json(['error' => 'not_in_drafted_state'], 400);

        $this->db->where('id', $draft_id)->update('email_agent_draft', ['status' => 'discarded']);

        if ($d['meeting_id']) {
            $res = $this->thank_you_email_agent->queue_thanks_for_meeting((int)$d['meeting_id']);
        } else {
            $res = ['ok' => false, 'error' => 'regenerate_for_query_not_supported_without_query_id'];
        }
        $this->_json($res, $res['ok'] ? 200 : 400);
    }

    // ---------- OAuth ----------

    public function oauth_start()
    {
        if (!$this->bearer_auth->verify($this->token)) return $this->_json(['error' => 'unauthorized'], 401);
        $bd_uid = (int)$this->input->get('bd_uid');
        if ($bd_uid <= 0) return $this->_json(['error' => 'bd_uid_required'], 400);

        $client_id    = $this->config->item('google_oauth_client_id', 'email_agent_config');
        $redirect_uri = $this->config->item('google_oauth_redirect_uri', 'email_agent_config');
        $state = bin2hex(random_bytes(16));
        $this->session->set_userdata('oauth_state_' . $state, [
            'bd_uid'  => $bd_uid,
            'created' => time(),
        ]);
        $params = [
            'client_id'     => $client_id,
            'redirect_uri'  => $redirect_uri,
            'response_type' => 'code',
            'scope'         => 'https://www.googleapis.com/auth/gmail.send',
            'access_type'   => 'offline',
            'prompt'        => 'consent',
            'state'         => $state,
        ];
        $url = 'https://accounts.google.com/o/oauth2/v2/auth?' . http_build_query($params);
        $this->_json(['ok' => true, 'consent_url' => $url, 'state' => $state], 200);
    }

    public function oauth_callback()
    {
        $code  = $this->input->get('code');
        $state = $this->input->get('state');
        $error = $this->input->get('error');
        if ($error) return $this->_html_error('Google OAuth error: ' . $error);
        if (!$code || !$state) return $this->_html_error('Missing code or state.');

        $stored = $this->session->userdata('oauth_state_' . $state);
        if (!$stored || (time() - $stored['created']) > 600) {
            return $this->_html_error('OAuth state expired. Restart from app.');
        }
        $this->session->unset_userdata('oauth_state_' . $state);
        $bd_uid = (int)$stored['bd_uid'];

        $client_id     = $this->config->item('google_oauth_client_id',     'email_agent_config');
        $client_secret = $this->config->item('google_oauth_client_secret', 'email_agent_config');
        $redirect_uri  = $this->config->item('google_oauth_redirect_uri',  'email_agent_config');

        $ch = curl_init('https://oauth2.googleapis.com/token');
        curl_setopt_array($ch, [
            CURLOPT_POST => true, CURLOPT_RETURNTRANSFER => true, CURLOPT_TIMEOUT => 15,
            CURLOPT_POSTFIELDS => http_build_query([
                'client_id'     => $client_id,
                'client_secret' => $client_secret,
                'code'          => $code,
                'redirect_uri'  => $redirect_uri,
                'grant_type'    => 'authorization_code',
            ]),
        ]);
        $resp = curl_exec($ch);
        $http = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        if ($http !== 200) return $this->_html_error('Token exchange failed, HTTP ' . $http);
        $body = json_decode($resp, true);
        if (empty($body['refresh_token'])) {
            return $this->_html_error('Google did not return a refresh token. Revoke prior consent at myaccount.google.com and retry.');
        }

        $access_token = $body['access_token'];
        $refresh      = $body['refresh_token'];
        $expires_in   = $body['expires_in'] ?? 3500;
        $expires_at   = date('Y-m-d H:i:s', time() + (int)$expires_in - 60);

        $gmail_address = $this->_fetch_user_email($access_token);
        if (!$gmail_address) return $this->_html_error('Could not read Gmail address.');

        $existing = $this->db->select('id')->from('bd_gmail_oauth_token')->where('bd_uid', $bd_uid)->get()->row_array();
        $payload = [
            'bd_uid'                 => $bd_uid,
            'gmail_address'          => $gmail_address,
            'refresh_token'          => $refresh,
            'access_token'           => $access_token,
            'access_token_expires_at'=> $expires_at,
            'scope'                  => 'https://www.googleapis.com/auth/gmail.send',
            'consent_screen_version' => 'v1',
            'status'                 => 'active',
        ];
        if ($existing) {
            $this->db->where('id', $existing['id'])->update('bd_gmail_oauth_token', $payload);
        } else {
            $payload['enrolled_at'] = date('Y-m-d H:i:s');
            $this->db->insert('bd_gmail_oauth_token', $payload);
        }
        log_message('info', '[email_agent_oauth_callback] enrolled bd=' . $bd_uid . ' gmail=' . $gmail_address);

        $this->output->set_content_type('text/html');
        $this->output->set_output('<html><body style="font-family:system-ui;padding:32px"><h2>Connected</h2><p>Gmail send is now enabled for ' . htmlspecialchars($gmail_address) . '. You can return to the app.</p></body></html>');
    }

    public function oauth_revoke()
    {
        if (!$this->bearer_auth->verify($this->token)) return $this->_json(['error' => 'unauthorized'], 401);
        $bd_uid = (int)$this->input->post('bd_uid');
        $reason = (string)$this->input->post('reason');
        if ($bd_uid <= 0) return $this->_json(['error' => 'bd_uid_required'], 400);

        $row = $this->db->select('*')->from('bd_gmail_oauth_token')->where('bd_uid', $bd_uid)->get()->row_array();
        if (!$row) return $this->_json(['error' => 'no_oauth_row'], 404);

        $ch = curl_init('https://oauth2.googleapis.com/revoke?token=' . urlencode($row['refresh_token']));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);
        curl_exec($ch);
        curl_close($ch);

        $this->db->where('bd_uid', $bd_uid)->update('bd_gmail_oauth_token', [
            'status'       => 'revoked',
            'revoked_at'   => date('Y-m-d H:i:s'),
            'revoke_reason'=> substr($reason, 0, 200),
            'access_token' => null,
        ]);
        $this->_json(['ok' => true], 200);
    }

    public function oauth_status()
    {
        if (!$this->bearer_auth->verify($this->token)) return $this->_json(['error' => 'unauthorized'], 401);
        $bd_uid = (int)$this->input->get('bd_uid');
        $row = $this->db->select('gmail_address, scope, status, enrolled_at, last_used_at')
            ->from('bd_gmail_oauth_token')->where('bd_uid', $bd_uid)->get()->row_array();
        $this->_json([
            'bd_uid'    => $bd_uid,
            'connected' => !empty($row) && $row['status'] === 'active',
            'row'       => $row,
        ], 200);
    }

    protected function _fetch_user_email($access_token)
    {
        $ch = curl_init('https://www.googleapis.com/oauth2/v2/userinfo');
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT        => 10,
            CURLOPT_HTTPHEADER     => ['Authorization: Bearer ' . $access_token],
        ]);
        $resp = curl_exec($ch);
        $http = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        if ($http !== 200) return null;
        $body = json_decode($resp, true);
        return $body['email'] ?? null;
    }

    protected function _json($data, $code)
    {
        $this->output->set_status_header($code)
            ->set_content_type('application/json')
            ->set_output(json_encode($data));
    }

    protected function _html_error($msg)
    {
        $this->output->set_status_header(400)
            ->set_content_type('text/html')
            ->set_output('<html><body style="font-family:system-ui;padding:32px"><h2>Setup failed</h2><p>' . htmlspecialchars($msg) . '</p></body></html>');
    }
}

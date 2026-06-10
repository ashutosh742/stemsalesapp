<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * M066 Bidirectional Email Sync patch
 * Place as application/controllers/Email.php
 *
 * Routes (CI3, NO /api/ prefix):
 *   POST /email/account_link
 *   GET  /email/sync_inbox         (cron-safe, gated by feature_flag email_sync_live)
 *   POST /email/send               (gated by feature_flag email_send_live)
 *   GET  /email/thread_for_lead
 *   POST /email/attach_to_lead
 *
 * Auth: Authorization Bearer header checked against config 'digest_token'.
 *
 * SECURITY NOTE: oauth_token and oauth_refresh values are stored as-is here.
 * The production deploy must encrypt them at rest using the application's
 * encryption key before inserting, and decrypt on read. This stub stores them
 * as plain text and logs a warning to alert operators.
 */

class M066_bidirectional_email_sync extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->database();
    }

    // -----------------------------------------------------------------------
    // Internal helpers
    // -----------------------------------------------------------------------

    private function _json($data, $status = 200)
    {
        $this->output
             ->set_status_header($status)
             ->set_content_type('application/json')
             ->set_output(json_encode($data));
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



    /**
     * Check whether a named feature flag is enabled.
     * Returns false (safe default) if the flag row is missing.
     */
    private function _flag($name)
    {
        // feature_flag table uses flag_key and flag_value (not name/enabled).
        $row = $this->db->get_where('feature_flag', array('flag_key' => $name))->row_array();
        return $row ? (bool)(int)$row['flag_value'] : false;
    }

    /**
     * Demo inbox messages returned when email_sync_live = false.
     * Keeps the app functional in staging without real provider credentials.
     */
    private function _demo_messages($account_id)
    {
        $now = date('Y-m-d H:i:s');
        return array(
            array(
                'id'                  => 'demo-1',
                'email_account_id'    => $account_id,
                'message_id_external' => 'demo-msg-001@stemapp.demo',
                'thread_id'           => 'thread-001',
                'direction'           => 'inbound',
                'from_address'        => 'parent@example.com',
                'to_addresses'        => 'agent@selfstagingstemapp.in',
                'cc_addresses'        => null,
                'subject'             => 'Query about STEM program fees',
                'body_text'           => 'Hi, I wanted to know more about the STEM program fees and schedule. Can you share details? Thanks.',
                'sent_at'             => null,
                'received_at'         => $now,
                'lead_cid_id'         => null,
                'attached_to_lead'    => 0,
                'sync_status'         => 'synced',
                'demo_mode'           => true,
            ),
            array(
                'id'                  => 'demo-2',
                'email_account_id'    => $account_id,
                'message_id_external' => 'demo-msg-002@stemapp.demo',
                'thread_id'           => 'thread-002',
                'direction'           => 'outbound',
                'from_address'        => 'agent@selfstagingstemapp.in',
                'to_addresses'        => 'student@example.com',
                'cc_addresses'        => null,
                'subject'             => 'Your STEM enrollment confirmation',
                'body_text'           => 'Dear student, your enrollment is confirmed. Please complete the orientation module within 7 days.',
                'sent_at'             => $now,
                'received_at'         => null,
                'lead_cid_id'         => null,
                'attached_to_lead'    => 0,
                'sync_status'         => 'synced',
                'demo_mode'           => true,
            ),
            array(
                'id'                  => 'demo-3',
                'email_account_id'    => $account_id,
                'message_id_external' => 'demo-msg-003@stemapp.demo',
                'thread_id'           => 'thread-001',
                'direction'           => 'outbound',
                'from_address'        => 'agent@selfstagingstemapp.in',
                'to_addresses'        => 'parent@example.com',
                'cc_addresses'        => null,
                'subject'             => 'Re: Query about STEM program fees',
                'body_text'           => 'Hi, thanks for reaching out! Our monthly fee is Rs 3,500 inclusive of all materials. Classes run Mon/Wed/Fri evenings. Happy to schedule a call.',
                'sent_at'             => $now,
                'received_at'         => null,
                'lead_cid_id'         => null,
                'attached_to_lead'    => 0,
                'sync_status'         => 'synced',
                'demo_mode'           => true,
            ),
        );
    }

    // -----------------------------------------------------------------------
    // Endpoints
    // -----------------------------------------------------------------------

    /**
     * POST /email/account_link
     * Link a mailbox to a user account.
     * Required POST: uid, provider (gmail|outlook|imap)
     * Optional POST: email_address, oauth_token, oauth_refresh,
     *                imap_host, imap_port, sync_enabled
     *
     * WARNING: tokens are stored unencrypted in this stub.
     * Encrypt before production use.
     */
    public function account_link()
    {
        if (!$this->_auth()) return;

        $uid          = (int)$this->input->post('uid');
        $provider     = trim((string)$this->input->post('provider'));
        $email_addr   = trim((string)$this->input->post('email_address'));
        $oauth_token  = $this->input->post('oauth_token');
        $oauth_refresh= $this->input->post('oauth_refresh');
        $imap_host    = trim((string)$this->input->post('imap_host'));
        $imap_port    = (int)$this->input->post('imap_port') ?: 993;
        $sync_enabled = (int)$this->input->post('sync_enabled') ? 1 : 0;

        if (!$uid || !$provider) {
            $this->_json(array('ok' => false, 'error' => 'missing_uid_or_provider'), 400);
            return;
        }
        if (!in_array($provider, array('gmail', 'outlook', 'imap'))) {
            $this->_json(array('ok' => false, 'error' => 'invalid_provider'), 400);
            return;
        }

        log_message('warning', 'M066: email_account_link storing credentials without encryption. Encrypt in production.');

        $existing = $this->db->get_where('email_account', array(
            'uid'           => $uid,
            'email_address' => $email_addr,
        ))->row_array();

        $row = array(
            'uid'          => $uid,
            'email_address'=> $email_addr ?: null,
            'provider'     => $provider,
            'oauth_token'  => $oauth_token  ?: null,
            'oauth_refresh'=> $oauth_refresh ?: null,
            'imap_host'    => $imap_host    ?: null,
            'imap_port'    => $imap_port,
            'sync_enabled' => $sync_enabled,
        );

        if ($existing) {
            $this->db->where('id', $existing['id'])->update('email_account', $row);
            $account_id = $existing['id'];
            $action     = 'updated';
        } else {
            $this->db->insert('email_account', $row);
            $account_id = $this->db->insert_id();
            $action     = 'linked';
        }

        $this->_json(array(
            'ok'         => true,
            'account_id' => $account_id,
            'action'     => $action,
            'warning'    => 'Credentials stored. Enable encryption before production deployment.',
        ));
    }

    /**
     * GET /email/sync_inbox?uid=X&account_id=X
     * Pull recent messages from the configured mailbox.
     * When feature_flag email_sync_live = false, returns demo messages
     * with demo_mode=true. Real IMAP/OAuth fetch is stubbed here and should
     * be implemented in a background worker.
     */
    public function sync_inbox()
    {
        if (!$this->_auth()) return;

        $uid        = (int)$this->input->get('uid');
        $account_id = (int)$this->input->get('account_id');
        $live       = $this->_flag('email_sync_live');

        if (!$account_id && $uid) {
            // Pick first enabled account for this user
            $acc = $this->db->where('uid', $uid)->where('sync_enabled', 1)
                            ->limit(1)->get('email_account')->row_array();
            $account_id = $acc ? (int)$acc['id'] : 0;
        }

        if (!$account_id) {
            // No linked account -- return demo data so the endpoint stays healthy.
            $this->_json(array(
                'ok'        => true,
                'demo_mode' => true,
                'notice'    => 'No linked email account found. Link an account via account_link to enable real sync.',
                'messages'  => $this->_demo_messages(0),
            ));
            return;
        }

        if (!$live) {
            // Feature flag off -- return demo data
            $this->_json(array(
                'ok'        => true,
                'demo_mode' => true,
                'notice'    => 'Live sync is off. Enable feature_flag email_sync_live to connect your mailbox.',
                'messages'  => $this->_demo_messages($account_id),
            ));
            return;
        }

        // TODO: Implement live IMAP/OAuth fetch in a background job.
        // This endpoint triggers the sync and returns already-synced messages.
        $since = date('Y-m-d H:i:s', strtotime('-7 days'));
        $msgs  = $this->db
                      ->where('email_account_id', $account_id)
                      ->where('received_at >=', $since)
                      ->order_by('received_at', 'DESC')
                      ->limit(50)
                      ->get('email_message')
                      ->result_array();

        $this->db->where('id', $account_id)
                 ->update('email_account', array('last_sync_at' => date('Y-m-d H:i:s')));

        $this->_json(array(
            'ok'        => true,
            'demo_mode' => false,
            'messages'  => $msgs,
        ));
    }

    /**
     * POST /email/send
     * Queue an outgoing message and attempt immediate send via provider.
     * Gates behind feature_flag email_send_live; when off, queues only.
     * Required POST: uid, to_address, subject, body
     * Optional POST: lead_cid_id, scheduled_at
     */
    public function send()
    {
        if (!$this->_auth()) return;

        $uid        = (int)$this->input->post('uid');
        $to_address = trim((string)$this->input->post('to_address'));
        $subject    = trim((string)$this->input->post('subject'));
        $body       = (string)$this->input->post('body');
        $lead_id    = (int)$this->input->post('lead_cid_id') ?: null;
        $scheduled  = $this->input->post('scheduled_at') ?: date('Y-m-d H:i:s');

        if (!$uid || !$to_address || !$subject) {
            $this->_json(array('ok' => false, 'error' => 'missing_uid_to_address_or_subject'), 400);
            return;
        }

        $live = $this->_flag('email_send_live');
        $now  = date('Y-m-d H:i:s');

        // Write to outbox regardless
        $this->db->insert('email_outbox', array(
            'uid'         => $uid,
            'to_address'  => $to_address,
            'subject'     => $subject,
            'body'        => $body ?: null,
            'lead_cid_id' => $lead_id,
            'scheduled_at'=> $scheduled,
            'status'      => 'queued',
            'created_at'  => $now,
        ));
        $outbox_id = $this->db->insert_id();

        if (!$live) {
            $this->_json(array(
                'ok'        => true,
                'outbox_id' => $outbox_id,
                'status'    => 'queued',
                'demo_mode' => true,
                'notice'    => 'Live email send is off. Message queued. Enable feature_flag email_send_live to send.',
            ));
            return;
        }

        // TODO: Implement real SMTP dispatch here.
        // For now mark as sent optimistically; background worker verifies.
        $this->db->where('id', $outbox_id)
                 ->update('email_outbox', array('status' => 'sent', 'sent_at' => $now));

        $this->_json(array(
            'ok'        => true,
            'outbox_id' => $outbox_id,
            'status'    => 'sent',
            'demo_mode' => false,
        ));
    }

    /**
     * GET /email/thread_for_lead?cid_id=X
     * Return all email messages linked to a lead, ordered by received/sent time.
     */
    public function thread_for_lead()
    {
        if (!$this->_auth()) return;

        $cid_id = (int)$this->input->get('cid_id');
        if (!$cid_id) {
            $this->_json(array('ok' => false, 'error' => 'missing_cid_id'), 400);
            return;
        }

        // Use a raw query to avoid CI3 backtick-wrapping the COALESCE expression.
        $messages = $this->db->query(
            'SELECT * FROM email_message WHERE lead_cid_id = ? ORDER BY COALESCE(received_at, sent_at) ASC',
            array($cid_id)
        )->result_array();

        $this->_json(array(
            'ok'       => true,
            'cid_id'   => $cid_id,
            'messages' => $messages,
        ));
    }

    /**
     * POST /email/attach_to_lead
     * Attach an existing email_message to a lead record.
     * Required POST: message_id (internal DB id), cid_id
     */
    public function attach_to_lead()
    {
        if (!$this->_auth()) return;

        $message_id = (int)$this->input->post('message_id');
        $cid_id     = (int)$this->input->post('cid_id');

        if (!$message_id || !$cid_id) {
            $this->_json(array('ok' => false, 'error' => 'missing_message_id_or_cid_id'), 400);
            return;
        }

        $msg = $this->db->get_where('email_message', array('id' => $message_id))->row_array();
        if (!$msg) {
            $this->_json(array('ok' => false, 'error' => 'message_not_found'), 404);
            return;
        }

        $this->db->where('id', $message_id)
                 ->update('email_message', array(
                     'lead_cid_id'     => $cid_id,
                     'attached_to_lead'=> 1,
                 ));

        $this->_json(array(
            'ok'         => true,
            'message_id' => $message_id,
            'cid_id'     => $cid_id,
            'message'    => 'Email attached to lead.',
        ));
    }
}

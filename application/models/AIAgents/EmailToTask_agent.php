<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Migration 039: Email-to-task agent
 *
 * Polls Gmail/IMAP mailboxes, fetches new messages, matches them to init_call/company_master
 * and writes them to inbound_email_v2 with a suggested task. Never writes tblcallevents
 * directly - that only happens when BD taps Accept in the controller.
 *
 * Production untouched. All writes go to inbound_email_v2 / inbound_email_poll_log_v2.
 *
 * Plain English. No em-dashes.
 */
class EmailToTask_agent extends CI_Model {

    public function __construct() {
        parent::__construct();
        $this->load->database();
    }

    /* =====================================================================
     * PUBLIC: poll one mailbox
     * Returns: array(messages_fetched, messages_new, messages_matched, messages_unmatched, error?)
     * ===================================================================== */
    public function poll_mailbox($mailbox_account) {
        $cfg = $this->db->get_where('inbound_email_mailbox_v2',
            array('mailbox_account' => $mailbox_account, 'is_active' => 1))->row();
        if (!$cfg) {
            return array('error' => 'mailbox_not_configured');
        }

        $log_id = $this->_open_poll_log($mailbox_account);

        $messages = $this->_fetch_messages($cfg);
        if (isset($messages['error'])) {
            $this->_close_poll_log($log_id, 0, 0, 0, 0, 'failed', $messages['error']);
            return $messages;
        }

        $new = 0; $matched = 0; $unmatched = 0;
        foreach ($messages as $msg) {
            $exists = $this->db->get_where('inbound_email_v2',
                array('message_id' => $msg['message_id']))->row();
            if ($exists) continue;

            $match = $this->_match_email_to_lead($msg);

            $row = array(
                'message_id'              => $msg['message_id'],
                'mailbox_account'         => $mailbox_account,
                'from_email'              => strtolower(trim($msg['from_email'])),
                'from_name'               => isset($msg['from_name']) ? $msg['from_name'] : null,
                'to_email'                => strtolower(trim($msg['to_email'])),
                'cc_emails'               => isset($msg['cc_emails']) ? $msg['cc_emails'] : null,
                'subject'                 => isset($msg['subject']) ? mb_substr($msg['subject'], 0, 500) : null,
                'body_text'               => isset($msg['body_text']) ? $msg['body_text'] : null,
                'body_html'               => isset($msg['body_html']) ? $msg['body_html'] : null,
                'received_at'             => $msg['received_at'],
                'has_attachment'          => !empty($msg['attachments']) ? 1 : 0,
                'attachment_count'        => !empty($msg['attachments']) ? count($msg['attachments']) : 0,
                'matched_lead_id'         => $match['matched_lead_id'],
                'matched_company_id'      => $match['matched_company_id'],
                'matched_bd_uid'          => $match['matched_bd_uid'],
                'match_confidence'        => $match['confidence'],
                'match_method'            => $match['method'],
                'suggested_action_type_id'=> 2,
                'suggested_purpose_id'    => 21,
                'status'                  => ($match['confidence'] >= 0.7) ? 'pending' : 'no_match',
            );
            $this->db->insert('inbound_email_v2', $row);
            $new++;
            if ($match['confidence'] >= 0.7) $matched++; else $unmatched++;

            // Store attachments separately
            if (!empty($msg['attachments'])) {
                $eid = $this->db->insert_id();
                foreach ($msg['attachments'] as $att) {
                    $this->db->insert('inbound_email_attachment_v2', array(
                        'inbound_email_id' => $eid,
                        'filename'         => isset($att['filename']) ? $att['filename'] : null,
                        'mime_type'        => isset($att['mime_type']) ? $att['mime_type'] : null,
                        'size_bytes'       => isset($att['size_bytes']) ? $att['size_bytes'] : null,
                        'storage_path'     => isset($att['storage_path']) ? $att['storage_path'] : null,
                    ));
                }
            }
        }

        // Update watermark
        $this->db->where('id', $cfg->id)->update('inbound_email_mailbox_v2', array(
            'last_poll_at' => date('Y-m-d H:i:s'),
        ));

        $this->_close_poll_log($log_id, count($messages), $new, $matched, $unmatched, 'success', null);

        return array(
            'messages_fetched'  => count($messages),
            'messages_new'      => $new,
            'messages_matched'  => $matched,
            'messages_unmatched'=> $unmatched,
        );
    }

    /* =====================================================================
     * PUBLIC: list pending inbox for a BD
     * ===================================================================== */
    public function inbox_for_bd($bd_uid, $limit = 50) {
        $cfg = $this->_get_config();
        if ($cfg['pilot_mode'] == '1') {
            $pilot_uids = array_map('intval', explode(',', $cfg['pilot_uids_csv']));
            if (!in_array((int)$bd_uid, $pilot_uids)) {
                return array(); // not in pilot, empty inbox
            }
        }
        return $this->db
            ->where('matched_bd_uid', $bd_uid)
            ->where('status', 'pending')
            ->order_by('received_at', 'DESC')
            ->limit($limit)
            ->get('inbound_email_v2')->result_array();
    }

    /* =====================================================================
     * PUBLIC: accept as task - returns suggested payload BD will commit
     * via standard /api/menu/submit_task. We mark accepted here only after
     * controller confirms the tblcallevents write succeeded.
     * ===================================================================== */
    public function build_accept_payload($inbound_email_id, $bd_uid) {
        $row = $this->db->get_where('inbound_email_v2', array('id' => $inbound_email_id))->row();
        if (!$row) return array('error' => 'not_found');
        if ($row->status !== 'pending') return array('error' => 'not_pending');
        if ((int)$row->matched_bd_uid !== (int)$bd_uid) return array('error' => 'not_your_inbox');

        $brief = $this->_summarize_for_remarks($row);

        return array(
            'cid_id'         => $row->matched_lead_id,
            'actiontype_id'  => $row->suggested_action_type_id,
            'purpose_id'     => $row->suggested_purpose_id,
            'remarks'        => $brief,
            'inbound_email_id'=> $row->id,
            'subject'        => $row->subject,
            'from_email'     => $row->from_email,
            'received_at'    => $row->received_at,
        );
    }

    public function mark_accepted($inbound_email_id, $bd_uid, $event_id) {
        $this->db->where('id', $inbound_email_id)
                 ->where('status', 'pending')
                 ->update('inbound_email_v2', array(
                    'status'              => 'accepted',
                    'accepted_as_event_id'=> $event_id,
                    'accepted_by_uid'     => $bd_uid,
                    'accepted_at'         => date('Y-m-d H:i:s'),
                 ));
        return $this->db->affected_rows() > 0;
    }

    public function mark_dismissed($inbound_email_id, $bd_uid, $reason = null) {
        $this->db->where('id', $inbound_email_id)
                 ->where('status', 'pending')
                 ->update('inbound_email_v2', array(
                    'status'           => 'dismissed',
                    'dismissed_by_uid' => $bd_uid,
                    'dismissed_at'     => date('Y-m-d H:i:s'),
                    'dismissed_reason' => $reason,
                 ));
        return $this->db->affected_rows() > 0;
    }

    /* =====================================================================
     * PRIVATE: matching logic
     * ===================================================================== */
    private function _match_email_to_lead($msg) {
        $from = strtolower(trim($msg['from_email']));
        $domain = (strpos($from, '@') !== false) ? substr($from, strpos($from, '@') + 1) : null;

        // 1. Exact match on init_call.dm_email
        $hit = $this->db->select('cid_id, mainbd')
                        ->where('LOWER(dm_email)', $from)
                        ->where('dm_email IS NOT NULL')
                        ->order_by('createDate', 'DESC')
                        ->limit(1)
                        ->get('init_call')->row();
        if ($hit) {
            return array(
                'matched_lead_id'    => (int)$hit->cid_id,
                'matched_company_id' => null,
                'matched_bd_uid'     => (int)$hit->mainbd,
                'confidence'         => 1.000,
                'method'             => 'dm_email_exact',
            );
        }

        // 2. Exact match on company_master.email
        $hit = $this->db->select('cid')
                        ->where('LOWER(email)', $from)
                        ->where('email IS NOT NULL')
                        ->limit(1)
                        ->get('company_master')->row();
        if ($hit) {
            $lead = $this->db->select('cid_id, mainbd')
                             ->where('cid', $hit->cid)
                             ->order_by('createDate', 'DESC')
                             ->limit(1)
                             ->get('init_call')->row();
            if ($lead) {
                return array(
                    'matched_lead_id'    => (int)$lead->cid_id,
                    'matched_company_id' => (int)$hit->cid,
                    'matched_bd_uid'     => (int)$lead->mainbd,
                    'confidence'         => 0.950,
                    'method'             => 'company_email_exact',
                );
            }
        }

        // 3. Domain match fallback (lower confidence)
        $cfg = $this->_get_config();
        if ($cfg['domain_match_enabled'] == '1' && $domain && !$this->_is_generic_domain($domain)) {
            $hit = $this->db->select('cid_id, mainbd')
                            ->like('dm_email', '@' . $domain, 'before')
                            ->where('dm_email IS NOT NULL')
                            ->order_by('createDate', 'DESC')
                            ->limit(1)
                            ->get('init_call')->row();
            if ($hit) {
                return array(
                    'matched_lead_id'    => (int)$hit->cid_id,
                    'matched_company_id' => null,
                    'matched_bd_uid'     => (int)$hit->mainbd,
                    'confidence'         => 0.720,
                    'method'             => 'domain_match',
                );
            }
        }

        return array(
            'matched_lead_id'    => null,
            'matched_company_id' => null,
            'matched_bd_uid'     => null,
            'confidence'         => 0.000,
            'method'             => 'none',
        );
    }

    private function _is_generic_domain($domain) {
        $generic = array('gmail.com','yahoo.com','yahoo.in','hotmail.com','outlook.com',
                         'rediffmail.com','live.com','icloud.com','protonmail.com');
        return in_array(strtolower($domain), $generic);
    }

    private function _summarize_for_remarks($row) {
        $subj = $row->subject ? $row->subject : '(no subject)';
        $when = $row->received_at;
        $from = $row->from_email;
        return "Inbound email from {$from} on {$when}. Subject: {$subj}. Replied via inbox accept.";
    }

    /* =====================================================================
     * PRIVATE: IMAP fetch wrapper. Production install needs imap extension
     * or Gmail OAuth via google/apiclient. This is the surface only.
     * ===================================================================== */
    private function _fetch_messages($cfg) {
        // Stub implementation. Real fetcher lives in a separate library that
        // either uses imap_open() with app_password or Google_Service_Gmail
        // with refresh_token from secret store. Returns an array of:
        //   message_id, from_email, from_name, to_email, cc_emails,
        //   subject, body_text, body_html, received_at, attachments[]
        // Bearer-protected /api/email_to_task/poll triggers this path.
        if (!function_exists('imap_open') && $cfg->auth_mode !== 'gmail_oauth') {
            return array('error' => 'imap_extension_missing');
        }

        // Delegate to library
        $this->load->library('Email_inbox_fetcher');
        return $this->email_inbox_fetcher->fetch($cfg);
    }

    private function _open_poll_log($mailbox_account) {
        $this->db->insert('inbound_email_poll_log_v2', array(
            'mailbox_account'  => $mailbox_account,
            'poll_started_at'  => date('Y-m-d H:i:s'),
            'status'           => 'running',
        ));
        return $this->db->insert_id();
    }

    private function _close_poll_log($log_id, $fetched, $new, $matched, $unmatched, $status, $err) {
        $this->db->where('id', $log_id)->update('inbound_email_poll_log_v2', array(
            'poll_ended_at'      => date('Y-m-d H:i:s'),
            'messages_fetched'   => $fetched,
            'messages_new'       => $new,
            'messages_matched'   => $matched,
            'messages_unmatched' => $unmatched,
            'status'             => $status,
            'error_message'      => $err,
        ));
    }

    private function _get_config() {
        $rows = $this->db->get('inbound_email_config_v2')->result_array();
        $out = array();
        foreach ($rows as $r) $out[$r['config_key']] = $r['config_value'];
        return $out;
    }
}

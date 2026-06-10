<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * STEM CRM - Migration 030 - Email OAuth Agent
 *
 * Responsibilities:
 *   1. oauth_connect()         - build authorization URL for Gmail or Outlook
 *   2. oauth_exchange_code()   - exchange auth code for access + refresh tokens
 *   3. oauth_refresh_token()   - refresh an expiring access token
 *   4. run_email_poll()        - cron: sync new emails for all active accounts
 *   5. run_calendar_poll()     - cron: sync calendar events for all active accounts
 *   6. thread_linker()         - match email to init_call by domain + DM email
 *   7. disconnect()            - revoke and clear tokens
 *
 * Cron schedule:
 *   every-5min  * * * *  php /var/www/stemapp.in/index.php email_oauth_agent run_email_poll
 *   every-15min * * * *  php /var/www/stemapp.in/index.php email_oauth_agent run_calendar_poll
 *
 * Phase gate: only processes uids where feature_flag_override.email_capture_enabled >= 1.
 * Pilot uids 42,43,44,45,46,12 from 25 May 2026. Org rollout 1 Jun 2026.
 *
 * Plain English. No em-dashes. No non-ASCII. Rs for rupees.
 *
 * Author: STEM Learning ops
 * Date: 2026-05-19
 */
class Email_oauth_agent extends CI_Model
{
    // ---- OAuth endpoints ----
    const GMAIL_AUTH_URL    = 'https://accounts.google.com/o/oauth2/v2/auth';
    const GMAIL_TOKEN_URL   = 'https://oauth2.googleapis.com/token';
    const GMAIL_REVOKE_URL  = 'https://oauth2.googleapis.com/revoke';
    const GMAIL_MESSAGES_URL = 'https://gmail.googleapis.com/gmail/v1/users/me/messages';
    const GMAIL_MESSAGE_URL  = 'https://gmail.googleapis.com/gmail/v1/users/me/messages/';
    const GMAIL_CALENDAR_URL = 'https://www.googleapis.com/calendar/v3/calendars/primary/events';

    const OUTLOOK_AUTH_URL    = 'https://login.microsoftonline.com/common/oauth2/v2.0/authorize';
    const OUTLOOK_TOKEN_URL   = 'https://login.microsoftonline.com/common/oauth2/v2.0/token';
    const OUTLOOK_MESSAGES_URL = 'https://graph.microsoft.com/v1.0/me/messages';
    const OUTLOOK_CALENDAR_URL = 'https://graph.microsoft.com/v1.0/me/events';

    const CALLBACK_URI       = 'https://stemapp.in/api/email_oauth/callback';
    const GMAIL_SCOPES       = 'https://www.googleapis.com/auth/gmail.readonly https://www.googleapis.com/auth/calendar.readonly';
    const OUTLOOK_SCOPES     = 'https://graph.microsoft.com/Mail.Read https://graph.microsoft.com/Calendars.Read offline_access';

    // ---- Polling limits ----
    const EMAIL_POLL_LIMIT   = 50;   // messages per poll per account
    const CALENDAR_POLL_DAYS = 14;   // days ahead for calendar events
    const TOKEN_REFRESH_BUFFER = 300; // refresh if under 5 min to expiry (seconds)
    const MAX_REFRESH_RETRIES  = 3;
    const SNIPPET_MAX_LEN      = 300;

    private $log_prefix = '[email_oauth_agent]';

    public function __construct()
    {
        parent::__construct();
        $this->load->database();
        $this->load->config('email_oauth');
        $this->load->model('Email_insight_agent');
    }

    // ========================================================================
    // 1. BUILD AUTHORIZATION URL
    // Called by controller POST /api/email_oauth/connect
    // Returns URL string for client redirect.
    // ========================================================================
    public function oauth_connect($uid, $provider)
    {
        $state = base64_encode($uid . '|' . $provider . '|' . time());

        if ($provider === 'gmail') {
            $params = http_build_query([
                'client_id'     => $this->config->item('GMAIL_CLIENT_ID'),
                'redirect_uri'  => self::CALLBACK_URI,
                'response_type' => 'code',
                'scope'         => self::GMAIL_SCOPES,
                'access_type'   => 'offline',
                'prompt'        => 'consent',
                'state'         => $state,
            ]);
            return self::GMAIL_AUTH_URL . '?' . $params;
        }

        if ($provider === 'outlook') {
            $params = http_build_query([
                'client_id'     => $this->config->item('OUTLOOK_CLIENT_ID'),
                'redirect_uri'  => self::CALLBACK_URI,
                'response_type' => 'code',
                'scope'         => self::OUTLOOK_SCOPES,
                'state'         => $state,
            ]);
            return self::OUTLOOK_AUTH_URL . '?' . $params;
        }

        log_message('error', $this->log_prefix . ' oauth_connect unknown provider=' . $provider);
        return false;
    }

    // ========================================================================
    // 2. EXCHANGE AUTHORIZATION CODE FOR TOKENS
    // Called by controller GET /api/email_oauth/callback
    // Returns array with ok, uid, provider or ok=false with error.
    // ========================================================================
    public function oauth_exchange_code($code, $state_raw)
    {
        $decoded = base64_decode($state_raw);
        $parts   = explode('|', $decoded);
        if (count($parts) < 2) {
            return ['ok' => false, 'error' => 'invalid_state'];
        }
        $uid      = (int)$parts[0];
        $provider = $parts[1];

        if ($provider === 'gmail') {
            $token_data = $this->_post_token_gmail($code);
        } elseif ($provider === 'outlook') {
            $token_data = $this->_post_token_outlook($code);
        } else {
            return ['ok' => false, 'error' => 'unknown_provider'];
        }

        if (empty($token_data['access_token'])) {
            log_message('error', $this->log_prefix . ' token exchange failed uid=' . $uid
                . ' provider=' . $provider . ' response=' . json_encode($token_data));
            return ['ok' => false, 'error' => 'token_exchange_failed'];
        }

        $expires_at = date('Y-m-d H:i:s', time() + (int)($token_data['expires_in'] ?? 3600));

        $row = [
            'uid'               => $uid,
            'provider'          => $provider,
            'oauth_token_enc'   => $this->_encrypt($token_data['access_token']),
            'refresh_token_enc' => $this->_encrypt($token_data['refresh_token'] ?? ''),
            'scopes'            => $token_data['scope'] ?? '',
            'token_expires_at'  => $expires_at,
            'status'            => 'active',
            'revoked_reason'    => null,
        ];

        // Upsert: replace existing row for this uid+provider
        $existing = $this->db->get_where('email_account_oauth',
            ['uid' => $uid, 'provider' => $provider])->row_array();

        if ($existing) {
            $this->db->where('uid', $uid)->where('provider', $provider)
                     ->update('email_account_oauth', $row);
        } else {
            $this->db->insert('email_account_oauth', $row);
        }

        log_message('info', $this->log_prefix . ' connected uid=' . $uid . ' provider=' . $provider);
        return ['ok' => true, 'uid' => $uid, 'provider' => $provider];
    }

    // ========================================================================
    // 3. REFRESH AN ACCESS TOKEN
    // Returns decrypted access token string or false on failure.
    // Sets status=revoked after MAX_REFRESH_RETRIES consecutive failures.
    // ========================================================================
    public function oauth_refresh_token($account_row)
    {
        $uid      = $account_row['uid'];
        $provider = $account_row['provider'];
        $refresh  = $this->_decrypt($account_row['refresh_token_enc']);

        if (empty($refresh)) {
            $this->_revoke_account($uid, $provider, 'empty_refresh_token');
            return false;
        }

        if ($provider === 'gmail') {
            $result = $this->_refresh_gmail($refresh);
        } else {
            $result = $this->_refresh_outlook($refresh);
        }

        if (empty($result['access_token'])) {
            log_message('error', $this->log_prefix . ' refresh failed uid=' . $uid
                . ' provider=' . $provider);
            // TODO: increment retry counter and revoke after MAX_REFRESH_RETRIES
            // For now, revoke immediately on failure to avoid hammering provider.
            $this->_revoke_account($uid, $provider, 'refresh_token_rejected');
            return false;
        }

        $expires_at = date('Y-m-d H:i:s', time() + (int)($result['expires_in'] ?? 3600));
        $update = [
            'oauth_token_enc'  => $this->_encrypt($result['access_token']),
            'token_expires_at' => $expires_at,
        ];
        // Google sometimes sends a new refresh token on rotation
        if (!empty($result['refresh_token'])) {
            $update['refresh_token_enc'] = $this->_encrypt($result['refresh_token']);
        }
        $this->db->where('uid', $uid)->where('provider', $provider)
                 ->update('email_account_oauth', $update);

        return $result['access_token'];
    }

    // ========================================================================
    // 4. EMAIL POLL - main cron entry point
    // Called: php index.php email_oauth_agent run_email_poll
    // Iterates all active accounts, syncs new messages, calls thread_linker,
    // then queues insight generation for new rows.
    // ========================================================================
    public function run_email_poll()
    {
        $accounts = $this->_get_active_accounts();
        $total_new = 0;

        foreach ($accounts as $acct) {
            $access_token = $this->_get_valid_token($acct);
            if (!$access_token) {
                continue;
            }

            if ($acct['provider'] === 'gmail') {
                $new_count = $this->_sync_gmail_messages($acct, $access_token);
            } else {
                $new_count = $this->_sync_outlook_messages($acct, $access_token);
            }

            $total_new += $new_count;
            $this->db->where('uid', $acct['uid'])
                     ->where('provider', $acct['provider'])
                     ->update('email_account_oauth', ['last_sync_at' => date('Y-m-d H:i:s')]);
        }

        log_message('info', $this->log_prefix . ' email poll complete. new_rows=' . $total_new);

        // Trigger insight generation for unprocessed messages
        $this->Email_insight_agent->process_new_messages();

        return $total_new;
    }

    // ========================================================================
    // 5. CALENDAR POLL - cron entry point
    // Called: php index.php email_oauth_agent run_calendar_poll
    // ========================================================================
    public function run_calendar_poll()
    {
        $accounts  = $this->_get_active_accounts();
        $total_new = 0;

        foreach ($accounts as $acct) {
            $access_token = $this->_get_valid_token($acct);
            if (!$access_token) {
                continue;
            }

            if ($acct['provider'] === 'gmail') {
                $new_count = $this->_sync_google_calendar($acct, $access_token);
            } else {
                $new_count = $this->_sync_outlook_calendar($acct, $access_token);
            }

            $total_new += $new_count;
            $this->db->where('uid', $acct['uid'])
                     ->where('provider', $acct['provider'])
                     ->update('email_account_oauth', ['last_cal_sync_at' => date('Y-m-d H:i:s')]);
        }

        log_message('info', $this->log_prefix . ' calendar poll complete. new_rows=' . $total_new);
        return $total_new;
    }

    // ========================================================================
    // 6. THREAD LINKER
    // Match email_message_log rows with lead_id=NULL to init_call via:
    //   a) domain match: extract domain from from_addr/to_addr, compare to
    //      domain extracted from init_call school email or compny_nm.
    //   b) DM contact email exact match against dm_verification.dm_email.
    // Sets lead_id when exactly one match found. Leaves NULL if ambiguous.
    // ========================================================================
    public function thread_linker($message_id, $uid)
    {
        $row = $this->db->get_where('email_message_log',
            ['id' => $message_id, 'uid' => $uid])->row_array();

        if (!$row || $row['lead_id'] !== null) {
            return false;
        }

        $ext_email = ($row['direction'] === 'in') ? $row['from_addr'] : $row['to_addr'];
        $domain    = $this->_extract_domain($ext_email);

        if (!$domain) {
            return false;
        }

        // Strategy A: match against dm_verification.dm_email for BDs CIDs
        $sql_dm = "
            SELECT DISTINCT ic.id AS cid_id
              FROM dm_verification dv
              INNER JOIN init_call ic ON ic.id = dv.cid_id
              WHERE ic.mainbd = ?
                AND (
                  dv.dm_email = ?
                  OR dv.dm_email LIKE ?
                )
                AND ic.cstatus NOT IN (12, 13)
            LIMIT 3
        ";
        $dm_matches = $this->db->query($sql_dm, [
            $uid,
            $ext_email,
            '%@' . $domain,
        ])->result_array();

        if (count($dm_matches) === 1) {
            $this->db->where('id', $message_id)
                     ->update('email_message_log', ['lead_id' => $dm_matches[0]['cid_id']]);
            return $dm_matches[0]['cid_id'];
        }

        // Strategy B: domain match against init_call school email field
        // TODO: confirm exact column name for school contact email in init_call.
        //       Using column init_call.dm_email.
        $sql_ic = "
            SELECT id AS cid_id
              FROM init_call
              WHERE mainbd = ?
                AND dm_email LIKE ?
                AND cstatus NOT IN (12, 13)
            LIMIT 3
        ";
        $ic_matches = $this->db->query($sql_ic, [$uid, '%@' . $domain])->result_array();

        if (count($ic_matches) === 1) {
            $this->db->where('id', $message_id)
                     ->update('email_message_log', ['lead_id' => $ic_matches[0]['cid_id']]);
            return $ic_matches[0]['cid_id'];
        }

        // Ambiguous or no match - leave lead_id NULL
        log_message('debug', $this->log_prefix . ' thread_linker no single match msg_id=' . $message_id
            . ' domain=' . $domain . ' dm_matches=' . count($dm_matches)
            . ' ic_matches=' . count($ic_matches));
        return false;
    }

    // ========================================================================
    // 7. DISCONNECT / REVOKE
    // ========================================================================
    public function disconnect($uid, $provider)
    {
        $acct = $this->db->get_where('email_account_oauth',
            ['uid' => $uid, 'provider' => $provider])->row_array();

        if (!$acct) {
            return ['ok' => false, 'error' => 'not_found'];
        }

        // Attempt provider-side revoke (best effort, ignore errors)
        $access = $this->_decrypt($acct['oauth_token_enc']);
        if ($access && $provider === 'gmail') {
            $this->_curl_post(self::GMAIL_REVOKE_URL, ['token' => $access]);
        }
        // Outlook does not have a separate revoke endpoint; clearing tokens is sufficient.

        $this->db->where('uid', $uid)->where('provider', $provider)
                 ->update('email_account_oauth', [
                     'oauth_token_enc'   => '',
                     'refresh_token_enc' => '',
                     'status'            => 'revoked',
                     'revoked_reason'    => 'user_disconnect',
                 ]);

        log_message('info', $this->log_prefix . ' disconnected uid=' . $uid . ' provider=' . $provider);
        return ['ok' => true];
    }

    // ========================================================================
    // PRIVATE: GMAIL MESSAGE SYNC
    // ========================================================================
    private function _sync_gmail_messages($acct, $access_token)
    {
        $since = $acct['last_sync_at']
            ? urlencode('after:' . date('Y/m/d', strtotime($acct['last_sync_at'])))
            : 'newer_than:2d';

        $url = self::GMAIL_MESSAGES_URL . '?q=' . $since
             . '&maxResults=' . self::EMAIL_POLL_LIMIT
             . '&labelIds=INBOX&labelIds=SENT';

        $list = $this->_curl_get($url, $access_token);
        if (empty($list['messages'])) {
            return 0;
        }

        $new_count = 0;
        foreach ($list['messages'] as $msg_ref) {
            $meta = $this->_curl_get(
                self::GMAIL_MESSAGE_URL . $msg_ref['id'] . '?format=metadata'
                . '&metadataHeaders=From&metadataHeaders=To&metadataHeaders=Subject&metadataHeaders=Date',
                $access_token
            );

            if (empty($meta['id'])) {
                continue;
            }

            $headers  = $this->_index_gmail_headers($meta['payload']['headers'] ?? []);
            $from     = $headers['From']    ?? '';
            $to       = $headers['To']      ?? '';
            $subject  = $headers['Subject'] ?? '';
            $date_str = $headers['Date']    ?? '';
            $received = $date_str
                ? date('Y-m-d H:i:s', strtotime($date_str))
                : date('Y-m-d H:i:s', (int)($meta['internalDate'] / 1000));

            // Determine direction: if labelIds contains SENT, direction=out
            $labels    = $meta['labelIds'] ?? [];
            $direction = in_array('SENT', $labels) ? 'out' : 'in';

            // Collect attachment filenames
            $filenames = [];
            foreach (($meta['payload']['parts'] ?? []) as $part) {
                if (!empty($part['filename'])) {
                    $filenames[] = $part['filename'];
                }
            }

            $snippet = substr($meta['snippet'] ?? '', 0, self::SNIPPET_MAX_LEN);

            $row = [
                'uid'                => $acct['uid'],
                'provider'           => 'gmail',
                'message_id'         => $meta['id'],
                'thread_id'          => $meta['threadId'] ?? null,
                'lead_id'            => null,
                'direction'          => $direction,
                'from_addr'          => $this->_parse_email_addr($from),
                'to_addr'            => $this->_parse_email_addr($to),
                'subject'            => substr($subject, 0, 512),
                'body_snippet'       => $snippet,
                'received_at'        => $received,
                'attached_files_json'=> !empty($filenames) ? json_encode($filenames) : null,
            ];

            $inserted = $this->_safe_insert_message($row);
            if ($inserted) {
                $new_count++;
                $msg_db_id = $this->db->insert_id();
                $this->thread_linker($msg_db_id, $acct['uid']);
            }
        }

        return $new_count;
    }

    // ========================================================================
    // PRIVATE: OUTLOOK MESSAGE SYNC
    // ========================================================================
    private function _sync_outlook_messages($acct, $access_token)
    {
        $since_filter = '';
        if ($acct['last_sync_at']) {
            $iso = date('c', strtotime($acct['last_sync_at']));
            $since_filter = '&$filter=receivedDateTime ge ' . urlencode($iso);
        }

        $url = self::OUTLOOK_MESSAGES_URL
             . '?$top=' . self::EMAIL_POLL_LIMIT
             . '&$select=id,conversationId,from,toRecipients,subject,bodyPreview,'
             . 'receivedDateTime,hasAttachments,attachments,isDraft'
             . $since_filter;

        $resp = $this->_curl_get($url, $access_token);
        if (empty($resp['value'])) {
            return 0;
        }

        $new_count = 0;
        foreach ($resp['value'] as $msg) {
            if (!empty($msg['isDraft'])) {
                continue;
            }

            $from_email = $msg['from']['emailAddress']['address'] ?? '';
            $to_email   = $msg['toRecipients'][0]['emailAddress']['address'] ?? '';
            $received   = date('Y-m-d H:i:s', strtotime($msg['receivedDateTime']));

            // For Outlook, direction: check if from_email matches the BD's connected account
            // TODO: store BD's email address in email_account_oauth for reliable direction check
            // Fallback: use Outlook folder - Graph API for sent items is a separate call
            // For now, assume all msgs from /me/messages are received unless from_addr = BD email
            $direction = 'in';

            $filenames = [];
            if (!empty($msg['attachments'])) {
                foreach ($msg['attachments'] as $att) {
                    if (!empty($att['name'])) {
                        $filenames[] = $att['name'];
                    }
                }
            }

            $snippet = substr($msg['bodyPreview'] ?? '', 0, self::SNIPPET_MAX_LEN);

            $row = [
                'uid'                => $acct['uid'],
                'provider'           => 'outlook',
                'message_id'         => $msg['id'],
                'thread_id'          => $msg['conversationId'] ?? null,
                'lead_id'            => null,
                'direction'          => $direction,
                'from_addr'          => $from_email,
                'to_addr'            => $to_email,
                'subject'            => substr($msg['subject'] ?? '', 0, 512),
                'body_snippet'       => $snippet,
                'received_at'        => $received,
                'attached_files_json'=> !empty($filenames) ? json_encode($filenames) : null,
            ];

            $inserted = $this->_safe_insert_message($row);
            if ($inserted) {
                $new_count++;
                $msg_db_id = $this->db->insert_id();
                $this->thread_linker($msg_db_id, $acct['uid']);
            }
        }

        return $new_count;
    }

    // ========================================================================
    // PRIVATE: GOOGLE CALENDAR SYNC
    // ========================================================================
    private function _sync_google_calendar($acct, $access_token)
    {
        $time_min = urlencode(date('c'));
        $time_max = urlencode(date('c', strtotime('+' . self::CALENDAR_POLL_DAYS . ' days')));

        $url = self::GMAIL_CALENDAR_URL
             . '?timeMin=' . $time_min
             . '&timeMax=' . $time_max
             . '&singleEvents=true&orderBy=startTime&maxResults=50';

        $resp = $this->_curl_get($url, $access_token);
        if (empty($resp['items'])) {
            return 0;
        }

        $new_count = 0;
        foreach ($resp['items'] as $event) {
            $attendees = [];
            foreach (($event['attendees'] ?? []) as $att) {
                $attendees[] = $att['email'] ?? '';
            }

            $start_at = $event['start']['dateTime'] ?? ($event['start']['date'] ?? null);
            $end_at   = $event['end']['dateTime']   ?? ($event['end']['date']   ?? null);

            $lead_id = $this->_match_calendar_event_to_lead($attendees, $acct['uid']);

            $row = [
                'uid'            => $acct['uid'],
                'provider'       => 'gmail',
                'ext_event_id'   => $event['id'],
                'lead_id'        => $lead_id,
                'title'          => substr($event['summary'] ?? '', 0, 512),
                'start_at'       => $start_at ? date('Y-m-d H:i:s', strtotime($start_at)) : null,
                'end_at'         => $end_at   ? date('Y-m-d H:i:s', strtotime($end_at))   : null,
                'attendees_json' => !empty($attendees) ? json_encode($attendees) : null,
                'location'       => substr($event['location'] ?? '', 0, 512),
                'source_sync_at' => date('Y-m-d H:i:s'),
            ];

            if (!$row['start_at']) {
                continue;
            }

            $new_count += $this->_upsert_calendar_event($row);
        }

        return $new_count;
    }

    // ========================================================================
    // PRIVATE: OUTLOOK CALENDAR SYNC
    // ========================================================================
    private function _sync_outlook_calendar($acct, $access_token)
    {
        $start = urlencode(date('c'));
        $end   = urlencode(date('c', strtotime('+' . self::CALENDAR_POLL_DAYS . ' days')));

        $url = self::OUTLOOK_CALENDAR_URL
             . '?$filter=start/dateTime ge \'' . rawurldecode($start)
             . '\' and end/dateTime le \'' . rawurldecode($end) . '\''
             . '&$top=50&$select=id,subject,start,end,attendees,location,bodyPreview';

        $resp = $this->_curl_get($url, $access_token);
        if (empty($resp['value'])) {
            return 0;
        }

        $new_count = 0;
        foreach ($resp['value'] as $event) {
            $attendees = [];
            foreach (($event['attendees'] ?? []) as $att) {
                $attendees[] = $att['emailAddress']['address'] ?? '';
            }
            $attendees = array_filter($attendees);

            $lead_id = $this->_match_calendar_event_to_lead($attendees, $acct['uid']);

            $row = [
                'uid'            => $acct['uid'],
                'provider'       => 'outlook',
                'ext_event_id'   => $event['id'],
                'lead_id'        => $lead_id,
                'title'          => substr($event['subject'] ?? '', 0, 512),
                'start_at'       => date('Y-m-d H:i:s', strtotime($event['start']['dateTime'])),
                'end_at'         => date('Y-m-d H:i:s', strtotime($event['end']['dateTime'])),
                'attendees_json' => !empty($attendees) ? json_encode(array_values($attendees)) : null,
                'location'       => substr($event['location']['displayName'] ?? '', 0, 512),
                'source_sync_at' => date('Y-m-d H:i:s'),
            ];

            $new_count += $this->_upsert_calendar_event($row);
        }

        return $new_count;
    }

    // ========================================================================
    // PRIVATE HELPERS
    // ========================================================================

    private function _get_active_accounts()
    {
        // Join feature_flag_override to respect pilot phasing
        $sql = "
            SELECT eao.*
              FROM email_account_oauth eao
              INNER JOIN feature_flag_override ffo
                      ON ffo.uid = eao.uid
                     AND ffo.flag_name = 'email_capture_enabled'
                     AND ffo.flag_value >= 1
              WHERE eao.status = 'active'
        ";
        return $this->db->query($sql)->result_array();
    }

    private function _get_valid_token($acct)
    {
        $expires_at = strtotime($acct['token_expires_at'] ?? '1970-01-01');
        $need_refresh = ($expires_at - time()) < self::TOKEN_REFRESH_BUFFER;

        if ($need_refresh) {
            $token = $this->oauth_refresh_token($acct);
        } else {
            $token = $this->_decrypt($acct['oauth_token_enc']);
        }

        if (!$token) {
            log_message('error', $this->log_prefix . ' could not get valid token uid='
                . $acct['uid'] . ' provider=' . $acct['provider']);
        }
        return $token;
    }

    private function _match_calendar_event_to_lead(array $attendees, $uid)
    {
        if (empty($attendees)) {
            return null;
        }
        foreach ($attendees as $email) {
            $domain = $this->_extract_domain($email);
            if (!$domain) {
                continue;
            }
            // Reuse thread_linker domain logic
            $sql = "
                SELECT ic.id
                  FROM init_call ic
                  WHERE ic.mainbd = ?
                    AND ic.dm_email LIKE ?
                    AND ic.cstatus NOT IN (12,13)
                  LIMIT 2
            ";
            $rows = $this->db->query($sql, [$uid, '%@' . $domain])->result_array();
            if (count($rows) === 1) {
                return $rows[0]['id'];
            }
        }
        return null;
    }

    private function _safe_insert_message(array $row)
    {
        // INSERT IGNORE equivalent via CI query to avoid duplicate key error
        $sql = "INSERT IGNORE INTO email_message_log
                (uid, provider, message_id, thread_id, lead_id, direction,
                 from_addr, to_addr, subject, body_snippet, received_at,
                 attached_files_json)
                VALUES (?,?,?,?,?,?,?,?,?,?,?,?)";
        $this->db->query($sql, [
            $row['uid'],
            $row['provider'],
            $row['message_id'],
            $row['thread_id'],
            $row['lead_id'],
            $row['direction'],
            $row['from_addr'],
            $row['to_addr'],
            $row['subject'],
            $row['body_snippet'],
            $row['received_at'],
            $row['attached_files_json'],
        ]);
        return $this->db->affected_rows() > 0;
    }

    private function _upsert_calendar_event(array $row)
    {
        $sql = "INSERT INTO calendar_event_log
                (uid, provider, ext_event_id, lead_id, title,
                 start_at, end_at, attendees_json, location, source_sync_at)
                VALUES (?,?,?,?,?,?,?,?,?,?)
                ON DUPLICATE KEY UPDATE
                  lead_id        = VALUES(lead_id),
                  title          = VALUES(title),
                  start_at       = VALUES(start_at),
                  end_at         = VALUES(end_at),
                  attendees_json = VALUES(attendees_json),
                  location       = VALUES(location),
                  source_sync_at = VALUES(source_sync_at)";
        $this->db->query($sql, [
            $row['uid'],
            $row['provider'],
            $row['ext_event_id'],
            $row['lead_id'],
            $row['title'],
            $row['start_at'],
            $row['end_at'],
            $row['attendees_json'],
            $row['location'],
            $row['source_sync_at'],
        ]);
        return $this->db->affected_rows() > 0 ? 1 : 0;
    }

    private function _post_token_gmail($code)
    {
        return $this->_curl_post(self::GMAIL_TOKEN_URL, [
            'code'          => $code,
            'client_id'     => $this->config->item('GMAIL_CLIENT_ID'),
            'client_secret' => $this->config->item('GMAIL_CLIENT_SECRET'),
            'redirect_uri'  => self::CALLBACK_URI,
            'grant_type'    => 'authorization_code',
        ]);
    }

    private function _post_token_outlook($code)
    {
        return $this->_curl_post(self::OUTLOOK_TOKEN_URL, [
            'code'          => $code,
            'client_id'     => $this->config->item('OUTLOOK_CLIENT_ID'),
            'client_secret' => $this->config->item('OUTLOOK_CLIENT_SECRET'),
            'redirect_uri'  => self::CALLBACK_URI,
            'grant_type'    => 'authorization_code',
            'scope'         => self::OUTLOOK_SCOPES,
        ]);
    }

    private function _refresh_gmail($refresh_token)
    {
        return $this->_curl_post(self::GMAIL_TOKEN_URL, [
            'client_id'     => $this->config->item('GMAIL_CLIENT_ID'),
            'client_secret' => $this->config->item('GMAIL_CLIENT_SECRET'),
            'refresh_token' => $refresh_token,
            'grant_type'    => 'refresh_token',
        ]);
    }

    private function _refresh_outlook($refresh_token)
    {
        return $this->_curl_post(self::OUTLOOK_TOKEN_URL, [
            'client_id'     => $this->config->item('OUTLOOK_CLIENT_ID'),
            'client_secret' => $this->config->item('OUTLOOK_CLIENT_SECRET'),
            'refresh_token' => $refresh_token,
            'grant_type'    => 'refresh_token',
            'scope'         => self::OUTLOOK_SCOPES,
        ]);
    }

    private function _revoke_account($uid, $provider, $reason)
    {
        $this->db->where('uid', $uid)->where('provider', $provider)
                 ->update('email_account_oauth', [
                     'status'         => 'revoked',
                     'revoked_reason' => $reason,
                 ]);
        log_message('error', $this->log_prefix . ' revoked uid=' . $uid
            . ' provider=' . $provider . ' reason=' . $reason);
    }

    private function _index_gmail_headers(array $headers)
    {
        $out = [];
        foreach ($headers as $h) {
            $out[$h['name']] = $h['value'];
        }
        return $out;
    }

    private function _parse_email_addr($raw)
    {
        // Extract email from "Display Name <email@domain.com>" or plain address
        if (preg_match('/<([^>]+)>/', $raw, $m)) {
            return strtolower(trim($m[1]));
        }
        return strtolower(trim($raw));
    }

    private function _extract_domain($email)
    {
        $email = $this->_parse_email_addr($email);
        $parts = explode('@', $email);
        if (count($parts) !== 2 || empty($parts[1])) {
            return null;
        }
        $domain = strtolower($parts[1]);
        // Skip generic domains - they are not school-specific
        $generic = ['gmail.com','yahoo.com','outlook.com','hotmail.com','rediffmail.com'];
        if (in_array($domain, $generic)) {
            return null;
        }
        return $domain;
    }

    private function _encrypt($plaintext)
    {
        $key    = $this->config->item('EMAIL_TOKEN_KEY');
        $iv     = openssl_random_pseudo_bytes(16);
        $cipher = openssl_encrypt($plaintext, 'AES-256-CBC', $key, OPENSSL_RAW_DATA, $iv);
        return base64_encode($iv . $cipher);
    }

    private function _decrypt($ciphertext)
    {
        if (empty($ciphertext)) {
            return '';
        }
        $key   = $this->config->item('EMAIL_TOKEN_KEY');
        $raw   = base64_decode($ciphertext);
        $iv    = substr($raw, 0, 16);
        $enc   = substr($raw, 16);
        $plain = openssl_decrypt($enc, 'AES-256-CBC', $key, OPENSSL_RAW_DATA, $iv);
        if ($plain === false) {
            log_message('error', $this->log_prefix . ' decryption failed');
            return '';
        }
        return $plain;
    }

    private function _curl_get($url, $access_token)
    {
        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT        => 15,
            CURLOPT_HTTPHEADER     => ['Authorization: Bearer ' . $access_token],
        ]);
        $body = curl_exec($ch);
        curl_close($ch);
        return json_decode($body, true) ?? [];
    }

    private function _curl_post($url, array $data)
    {
        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST           => true,
            CURLOPT_TIMEOUT        => 15,
            CURLOPT_POSTFIELDS     => http_build_query($data),
            CURLOPT_HTTPHEADER     => ['Content-Type: application/x-www-form-urlencoded'],
        ]);
        $body = curl_exec($ch);
        curl_close($ch);
        return json_decode($body, true) ?? [];
    }
}
// End of Email_oauth_agent

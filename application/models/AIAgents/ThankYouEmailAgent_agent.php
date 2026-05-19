<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * STEM Learning - Thank You Email Agent
 * Migration 026 (Phase 1, live 1 Jun 2026)
 *
 * Responsibilities:
 *  1. Detect newly-approved MoMs (mom_data.approved_status='1') and draft
 *     a thank-you email per the cstatus_after band
 *  2. Detect newly-raised queries (lead_query_checklist insert) and draft
 *     the matching follow-up email
 *  3. Call GPT-4o-mini to personalise the template body
 *  4. Send via BD Gmail OAuth (gmail.send scope) when BD approves
 *  5. Log every send attempt, retry on token expiry once
 *
 * Founder rule (verbatim): "thank you. Mail has to be sent post meeting,
 * so check with our email agent AI that thank you. Mail is send"
 *
 * Cost ceiling: GPT-4o-mini at Rs 2,800/month for 60 BD org rollout.
 * Roughly 800 input + 400 output tokens per draft, 8 drafts per BD per week.
 *
 * Runs from:
 *   - Hook: after_mom_approved (queue a draft)
 *   - Hook: after_query_raised (queue a draft if BD wants email out)
 *   - CLI: php index.php thank_you_email_agent send_approved_now (every 5 min)
 *   - CLI: php index.php thank_you_email_agent expire_old (daily)
 */
class Thank_you_email_agent
{
    const AI_MODEL                  = 'gpt-4o-mini';
    const AI_MAX_OUTPUT_TOKENS      = 600;
    const AI_TEMPERATURE            = 0.4;
    const DRAFT_EXPIRY_HOURS        = 24;
    const SEND_BATCH_LIMIT          = 100;
    const RETRY_LIMIT               = 1;
    const OPENAI_ENDPOINT           = 'https://api.openai.com/v1/chat/completions';
    const GMAIL_SEND_ENDPOINT       = 'https://gmail.googleapis.com/gmail/v1/users/me/messages/send';
    const OAUTH_REFRESH_ENDPOINT    = 'https://oauth2.googleapis.com/token';

    protected $CI;
    protected $db;
    protected $log_prefix = '[thank_you_email_agent]';

    public function __construct()
    {
        $this->CI =& get_instance();
        $this->CI->load->database();
        $this->db = $this->CI->db;
        $this->CI->load->helper(['url']);
        $this->CI->config->load('email_agent_config', true, true);
    }

    // -----------------------------------------------------------------
    // HOOK: queue a thank-you draft after MoM approval
    // -----------------------------------------------------------------
    public function queue_thanks_for_meeting($meeting_id)
    {
        $meeting = $this->db
            ->select('ev.id AS meeting_id, ev.cid_id, ev.user_id AS bd_uid, ev.event_date, ev.event_time, ev.actiontype_id,
                      ic.school_name, ic.current_status_id AS cstatus,
                      m.id AS mom_id, m.meeting_summary, m.next_meeting_purpose, m.approved_status')
            ->from('tblcallevents ev')
            ->join('init_call ic', 'ic.id = ev.cid_id')
            ->join('mom_data m', 'm.event_id = ev.id', 'left')
            ->where('ev.id', $meeting_id)
            ->get()->row_array();

        if (!$meeting) return ['ok' => false, 'error' => 'meeting_not_found'];

        $cstatus = (int)$meeting['cstatus'];
        $template_code = $this->_pick_template_for_cstatus($cstatus);
        if (!$template_code) {
            return ['ok' => true, 'skipped' => true, 'reason' => 'cstatus_not_eligible'];
        }

        $existing = $this->db->select('id, status')->from('email_agent_draft')
            ->where('meeting_id', $meeting_id)
            ->where_in('status', ['drafted','approved','sent'])
            ->get()->row_array();
        if ($existing) return ['ok' => true, 'skipped' => true, 'existing_draft_id' => $existing['id']];

        $recipient = $this->_resolve_dm_recipient($meeting['cid_id'], $meeting['bd_uid']);
        if (!$recipient['ok']) return ['ok' => false, 'error' => $recipient['error']];

        $draft = $this->_compose_draft([
            'cid_id'         => (int)$meeting['cid_id'],
            'meeting_id'     => (int)$meeting_id,
            'bd_uid'         => (int)$meeting['bd_uid'],
            'template_code'  => $template_code,
            'trigger_reason' => $this->_trigger_for_cstatus($cstatus),
            'recipient'      => $recipient,
            'context_vars'   => $this->_build_context_for_meeting($meeting),
        ]);
        return $draft;
    }

    // -----------------------------------------------------------------
    // HOOK: queue a follow-up draft after a lead query is raised
    // -----------------------------------------------------------------
    public function queue_followup_for_query($query_id)
    {
        $q = $this->db->select('q.*, ic.school_name, ic.mainbd AS bd_uid')
            ->from('lead_query_checklist q')
            ->join('init_call ic', 'ic.id = q.cid_id')
            ->where('q.id', $query_id)
            ->get()->row_array();
        if (!$q) return ['ok' => false, 'error' => 'query_not_found'];

        $template_code = $this->_pick_template_for_query_type($q['query_type']);
        $recipient = $this->_resolve_dm_recipient((int)$q['cid_id'], (int)$q['bd_uid']);
        if (!$recipient['ok']) return ['ok' => false, 'error' => $recipient['error']];

        return $this->_compose_draft([
            'cid_id'         => (int)$q['cid_id'],
            'meeting_id'     => null,
            'bd_uid'         => (int)$q['bd_uid'],
            'template_code'  => $template_code,
            'trigger_reason' => $this->_trigger_for_query_type($q['query_type']),
            'recipient'      => $recipient,
            'context_vars'   => $this->_build_context_for_query($q),
        ]);
    }

    // -----------------------------------------------------------------
    // Internal: pick template by cstatus
    // -----------------------------------------------------------------
    protected function _pick_template_for_cstatus($cstatus)
    {
        if ($cstatus === 3)              return 'tentative_thanks';
        if ($cstatus === 6)              return 'positive_thanks';
        if ($cstatus === 8 || $cstatus === 9) return 'rp_thanks';
        if ($cstatus === 12)             return 'won_handover';
        return null;
    }

    protected function _trigger_for_cstatus($cstatus)
    {
        if ($cstatus === 3)              return 'post_tentative_meeting';
        if ($cstatus === 6)              return 'post_positive_meeting';
        if ($cstatus === 8 || $cstatus === 9) return 'post_rp_meeting';
        if ($cstatus === 12)             return 'post_won_handover';
        return 'post_tentative_meeting';
    }

    protected function _pick_template_for_query_type($type)
    {
        $map = [
            'school_visit_request'   => 'query_followup_visit',
            'documentation_check'    => 'query_followup_documents',
            'budget_clarification'   => 'query_followup_budget',
        ];
        return $map[$type] ?? 'query_followup_generic';
    }

    protected function _trigger_for_query_type($type)
    {
        $map = [
            'school_visit_request' => 'query_followup_visit',
            'documentation_check'  => 'query_followup_documents',
            'budget_clarification' => 'query_followup_budget',
        ];
        return $map[$type] ?? 'query_followup_generic';
    }

    // -----------------------------------------------------------------
    // Resolve DM email (recipient) for a cid
    // -----------------------------------------------------------------
    protected function _resolve_dm_recipient($cid_id, $bd_uid)
    {
        $ic = $this->db->select('id, school_name, dmname, dmemail, dmdesignation, dm_email_verified_at')
            ->from('init_call')->where('id', $cid_id)->get()->row_array();
        if (!$ic) return ['ok' => false, 'error' => 'init_call_not_found'];
        if (empty($ic['dmemail']) || !filter_var($ic['dmemail'], FILTER_VALIDATE_EMAIL)) {
            return ['ok' => false, 'error' => 'dm_email_missing_or_invalid', 'cid_id' => $cid_id];
        }
        return [
            'ok'        => true,
            'email'     => $ic['dmemail'],
            'name'      => $ic['dmname'] ?: 'Sir/Madam',
            'role'      => $this->_designation_to_role($ic['dmdesignation']),
            'verified'  => !empty($ic['dm_email_verified_at']),
        ];
    }

    protected function _designation_to_role($designation)
    {
        $d = strtolower((string)$designation);
        if (strpos($d, 'principal') !== false) return 'principal';
        if (strpos($d, 'director') !== false)  return 'director';
        if (strpos($d, 'vp') !== false || strpos($d, 'vice') !== false) return 'vp';
        if (strpos($d, 'admin') !== false)     return 'admin';
        if (strpos($d, 'procure') !== false)   return 'procurement';
        if (strpos($d, 'csr') !== false)       return 'csr_head';
        if (strpos($d, 'dm') !== false || strpos($d, 'managing') !== false) return 'dm';
        return 'other';
    }

    // -----------------------------------------------------------------
    // Internal: build context vars for meeting-triggered email
    // -----------------------------------------------------------------
    protected function _build_context_for_meeting($meeting)
    {
        $bd = $this->db->select('fname, lname, mobile')
            ->from('user')->where('uid', $meeting['bd_uid'])->get()->row_array();

        return [
            'principal_name'              => '__from_recipient__',
            'school_name'                 => $meeting['school_name'],
            'meeting_recap_one_line'      => $meeting['meeting_summary'] ?: 'We discussed your STEM lab plans.',
            'next_step_one_line'          => $meeting['next_meeting_purpose'] ?: 'I will reach out with next steps.',
            'bd_name'                     => trim(($bd['fname'] ?? '') . ' ' . ($bd['lname'] ?? '')),
            'bd_phone'                    => $bd['mobile'] ?? '',
            'proposal_scope_one_line'     => 'the lab configuration and timeline',
            'queries_pending_one_line'    => '',
            'rp_decision_areas'           => 'lab configuration and rollout',
            'next_step_with_owner_and_date' => 'we will share the formal proposal within 48 hours',
            'cm_mention_one_line'         => '',
            'anchor_name'                 => '',
            'anchor_phone'                => '',
            'anchor_email'                => '',
            'kickoff_date'                => '',
            'project_timeline_one_line'   => '',
        ];
    }

    protected function _build_context_for_query($q)
    {
        $bd = $this->db->select('fname, lname, mobile')
            ->from('user')->where('uid', $q['bd_uid'])->get()->row_array();
        return [
            'principal_name'        => '__from_recipient__',
            'school_name'           => $q['school_name'],
            'bd_name'               => trim(($bd['fname'] ?? '') . ' ' . ($bd['lname'] ?? '')),
            'bd_phone'              => $bd['mobile'] ?? '',
            'query_topic_short'     => str_replace('_', ' ', $q['query_type']),
            'query_topic_full'      => $q['query_text'],
            'response_body'         => 'I will share the requested details in the next working day.',
            'next_step_one_line'    => 'I will follow up by ' . date('d M', strtotime($q['sla_deadline'])) . '.',
            'visit_proposed_dates'  => '',
            'visit_team_one_line'   => '',
            'document_list_bullets' => '',
            'document_list_plain'   => '',
            'additional_context_one_line' => '',
            'indicative_range_rs'   => '',
            'inclusions_one_line'   => '',
            'exclusions_one_line'   => '',
        ];
    }

    // -----------------------------------------------------------------
    // Internal: compose a draft (calls OpenAI then saves to email_agent_draft)
    // -----------------------------------------------------------------
    protected function _compose_draft($args)
    {
        $tpl = $this->db->select('*')->from('email_template')
            ->where('code', $args['template_code'])
            ->where('active', 1)
            ->get()->row_array();
        if (!$tpl) return ['ok' => false, 'error' => 'template_not_found'];

        $args['context_vars']['principal_name'] = $args['recipient']['name'];

        $ai = $this->_call_ai_personalise($tpl, $args['context_vars']);
        if (!$ai['ok']) {
            log_message('warning', $this->log_prefix . ' AI failed, using template raw: ' . $ai['error']);
            $body_html  = $this->_substitute($tpl['body_html_template'],  $args['context_vars']);
            $body_plain = $this->_substitute($tpl['body_plain_template'], $args['context_vars']);
            $subject    = $this->_substitute($tpl['subject_template'],    $args['context_vars']);
            $tokens     = 0;
            $cost_usd   = 0;
        } else {
            $body_html  = $ai['body_html'];
            $body_plain = $ai['body_plain'];
            $subject    = $ai['subject'];
            $tokens     = $ai['tokens_used'];
            $cost_usd   = $ai['cost_usd'];
        }

        $now = date('Y-m-d H:i:s');
        $expires = date('Y-m-d H:i:s', strtotime($now) + (self::DRAFT_EXPIRY_HOURS * 3600));

        $insert = [
            'cid_id'          => $args['cid_id'],
            'meeting_id'      => $args['meeting_id'],
            'bd_uid'          => $args['bd_uid'],
            'recipient_email' => $args['recipient']['email'],
            'recipient_name'  => $args['recipient']['name'],
            'recipient_role'  => $args['recipient']['role'],
            'template_code'   => $args['template_code'],
            'trigger_reason'  => $args['trigger_reason'],
            'subject_line'    => substr($subject, 0, 300),
            'body_html'       => $body_html,
            'body_plain'      => $body_plain,
            'ai_model'        => self::AI_MODEL,
            'ai_tokens_used'  => $tokens,
            'ai_cost_usd'     => $cost_usd,
            'drafted_at'      => $now,
            'status'          => 'drafted',
            'expires_at'      => $expires,
        ];
        $this->db->insert('email_agent_draft', $insert);
        $draft_id = $this->db->insert_id();

        log_message('info', $this->log_prefix . " drafted id={$draft_id} bd={$args['bd_uid']} tpl={$args['template_code']} tokens={$tokens}");
        return ['ok' => true, 'draft_id' => $draft_id, 'subject' => $subject];
    }

    // -----------------------------------------------------------------
    // Internal: call OpenAI to personalise the template
    // -----------------------------------------------------------------
    protected function _call_ai_personalise($tpl, $vars)
    {
        $api_key = getenv('OPENAI_API_KEY') ?: $this->CI->config->item('openai_api_key', 'email_agent_config');
        if (!$api_key) return ['ok' => false, 'error' => 'no_api_key'];

        $system = "You are an email assistant for STEM Learning, an Indian K-12 STEM education company. "
                . "Tone: respectful, concise, plain English. NEVER use em-dashes, NEVER use non-ASCII characters. "
                . "Use 'Rs' for rupees, 'percent' spelled out. "
                . "Persona instructions for this email: " . $tpl['ai_persona_instructions'];

        $user = "Template subject:\n" . $tpl['subject_template'] . "\n\n"
              . "Template body (plain):\n" . $tpl['body_plain_template'] . "\n\n"
              . "Context variables:\n" . json_encode($vars, JSON_PRETTY_PRINT) . "\n\n"
              . "Task: Fill the template with the context variables. If a variable is empty or generic, "
              . "soften the relevant sentence or omit it. Output exactly this JSON shape:\n"
              . '{"subject":"...","body_plain":"...","body_html":"..."}';

        $payload = [
            'model'       => self::AI_MODEL,
            'temperature' => self::AI_TEMPERATURE,
            'max_tokens'  => self::AI_MAX_OUTPUT_TOKENS,
            'response_format' => ['type' => 'json_object'],
            'messages'    => [
                ['role' => 'system', 'content' => $system],
                ['role' => 'user',   'content' => $user],
            ],
        ];

        $ch = curl_init(self::OPENAI_ENDPOINT);
        curl_setopt_array($ch, [
            CURLOPT_POST           => true,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT        => 25,
            CURLOPT_HTTPHEADER     => [
                'Authorization: Bearer ' . $api_key,
                'Content-Type: application/json',
            ],
            CURLOPT_POSTFIELDS => json_encode($payload),
        ]);
        $resp = curl_exec($ch);
        $http = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        if ($http !== 200 || !$resp) return ['ok' => false, 'error' => 'openai_http_' . $http];

        $data = json_decode($resp, true);
        $content = $data['choices'][0]['message']['content'] ?? null;
        if (!$content) return ['ok' => false, 'error' => 'no_content'];

        $parsed = json_decode($content, true);
        if (!is_array($parsed) || empty($parsed['subject']) || empty($parsed['body_plain'])) {
            return ['ok' => false, 'error' => 'bad_json_shape'];
        }

        $tokens_in  = $data['usage']['prompt_tokens'] ?? 0;
        $tokens_out = $data['usage']['completion_tokens'] ?? 0;
        // GPT-4o-mini: $0.15 per 1M input, $0.60 per 1M output (as of 1 May 2026)
        $cost_usd = ($tokens_in / 1000000.0) * 0.15 + ($tokens_out / 1000000.0) * 0.60;

        return [
            'ok'         => true,
            'subject'    => $parsed['subject'],
            'body_plain' => $parsed['body_plain'],
            'body_html'  => $parsed['body_html'] ?? nl2br(htmlspecialchars($parsed['body_plain'])),
            'tokens_used'=> $tokens_in + $tokens_out,
            'cost_usd'   => $cost_usd,
        ];
    }

    // -----------------------------------------------------------------
    // BD review API: approve a draft (BD edits captured by controller)
    // -----------------------------------------------------------------
    public function approve_draft($draft_id, $bd_uid, $edits = null)
    {
        $d = $this->db->select('*')->from('email_agent_draft')->where('id', $draft_id)->get()->row_array();
        if (!$d) return ['ok' => false, 'error' => 'draft_not_found'];
        if ((int)$d['bd_uid'] !== (int)$bd_uid) return ['ok' => false, 'error' => 'bd_mismatch'];
        if (!in_array($d['status'], ['drafted'])) return ['ok' => false, 'error' => 'not_approvable'];
        if (strtotime($d['expires_at']) <= time()) {
            $this->db->where('id', $draft_id)->update('email_agent_draft', ['status' => 'expired']);
            return ['ok' => false, 'error' => 'expired'];
        }

        $update = [
            'bd_reviewed_at' => date('Y-m-d H:i:s'),
            'status'         => 'approved',
        ];
        if (is_array($edits)) {
            if (!empty($edits['subject_line']))  $update['subject_line']  = substr($edits['subject_line'], 0, 300);
            if (!empty($edits['body_plain']))    $update['body_plain']    = $edits['body_plain'];
            if (!empty($edits['body_html']))     $update['body_html']     = $edits['body_html'];
            $update['bd_edits_made'] = 1;
        }
        $this->db->where('id', $draft_id)->update('email_agent_draft', $update);
        return ['ok' => true];
    }

    public function discard_draft($draft_id, $bd_uid)
    {
        $d = $this->db->select('*')->from('email_agent_draft')->where('id', $draft_id)->get()->row_array();
        if (!$d) return ['ok' => false, 'error' => 'draft_not_found'];
        if ((int)$d['bd_uid'] !== (int)$bd_uid) return ['ok' => false, 'error' => 'bd_mismatch'];
        $this->db->where('id', $draft_id)->update('email_agent_draft', ['status' => 'discarded']);
        return ['ok' => true];
    }

    // -----------------------------------------------------------------
    // CLI: send_approved_now - every 5 min
    // -----------------------------------------------------------------
    public function send_approved_now()
    {
        $approved = $this->db->select('*')->from('email_agent_draft')
            ->where('status', 'approved')
            ->order_by('bd_reviewed_at', 'asc')
            ->limit(self::SEND_BATCH_LIMIT)
            ->get()->result_array();

        $log = ['sent' => 0, 'failed' => 0, 'token_expired' => 0, 'started_at' => date('Y-m-d H:i:s')];
        foreach ($approved as $d) {
            $res = $this->_send_one($d);
            if ($res['status'] === 'sent') $log['sent']++;
            elseif ($res['status'] === 'token_expired') $log['token_expired']++;
            else $log['failed']++;
        }
        $log['finished_at'] = date('Y-m-d H:i:s');
        log_message('info', $this->log_prefix . ' send_approved_now ' . json_encode($log));
        return $log;
    }

    // -----------------------------------------------------------------
    // Internal: send one approved draft via BD Gmail OAuth
    // -----------------------------------------------------------------
    protected function _send_one($d)
    {
        $token = $this->_get_access_token($d['bd_uid']);
        if (!$token['ok']) {
            $this->_log_send($d, 'token_expired', null, null, null, $token['error'], 0);
            return ['status' => 'token_expired', 'error' => $token['error']];
        }

        $raw = $this->_build_mime($d, $token['gmail_address']);
        $encoded = rtrim(strtr(base64_encode($raw), '+/', '-_'), '=');

        $ch = curl_init(self::GMAIL_SEND_ENDPOINT);
        curl_setopt_array($ch, [
            CURLOPT_POST           => true,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT        => 20,
            CURLOPT_HTTPHEADER     => [
                'Authorization: Bearer ' . $token['access_token'],
                'Content-Type: application/json',
            ],
            CURLOPT_POSTFIELDS => json_encode(['raw' => $encoded]),
        ]);
        $resp = curl_exec($ch);
        $http = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($http === 200) {
            $body = json_decode($resp, true);
            $msg_id    = $body['id'] ?? null;
            $thread_id = $body['threadId'] ?? null;

            $this->db->where('id', $d['id'])->update('email_agent_draft', [
                'status'             => 'sent',
                'send_attempted_at'  => date('Y-m-d H:i:s'),
            ]);
            $this->_log_send($d, 'sent', $msg_id, $thread_id, 200, null, 0);

            $this->db->where('uid', $d['bd_uid'])->update('bd_gmail_oauth_token', [
                'last_used_at' => date('Y-m-d H:i:s'),
            ]);
            return ['status' => 'sent', 'message_id' => $msg_id];
        }

        if ($http === 401) {
            $this->_invalidate_access_token($d['bd_uid']);
            $this->_log_send($d, 'token_expired', null, null, 401, $resp, 0);
            return ['status' => 'token_expired'];
        }

        $retry_count = (int)$this->db->select('COUNT(*) AS c')->from('email_agent_send_log')
            ->where('draft_id', $d['id'])->get()->row('c');
        $this->_log_send($d, 'failed', null, null, $http, $resp, $retry_count);
        if ($retry_count < self::RETRY_LIMIT) {
            return ['status' => 'failed', 'will_retry' => true];
        }
        $this->db->where('id', $d['id'])->update('email_agent_draft', [
            'status'            => 'failed',
            'send_attempted_at' => date('Y-m-d H:i:s'),
        ]);
        return ['status' => 'failed', 'will_retry' => false];
    }

    // -----------------------------------------------------------------
    // Internal: refresh / fetch access token for a BD
    // -----------------------------------------------------------------
    protected function _get_access_token($bd_uid)
    {
        $row = $this->db->select('*')->from('bd_gmail_oauth_token')
            ->where('bd_uid', $bd_uid)->where('status', 'active')->get()->row_array();
        if (!$row) return ['ok' => false, 'error' => 'no_oauth_row'];

        if (!empty($row['access_token']) && !empty($row['access_token_expires_at'])
            && strtotime($row['access_token_expires_at']) > (time() + 60)) {
            return [
                'ok' => true,
                'access_token'  => $row['access_token'],
                'gmail_address' => $row['gmail_address'],
            ];
        }

        $client_id     = $this->CI->config->item('google_oauth_client_id', 'email_agent_config');
        $client_secret = $this->CI->config->item('google_oauth_client_secret', 'email_agent_config');

        $ch = curl_init(self::OAUTH_REFRESH_ENDPOINT);
        curl_setopt_array($ch, [
            CURLOPT_POST           => true,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT        => 10,
            CURLOPT_POSTFIELDS => http_build_query([
                'client_id'     => $client_id,
                'client_secret' => $client_secret,
                'refresh_token' => $row['refresh_token'],
                'grant_type'    => 'refresh_token',
            ]),
        ]);
        $resp = curl_exec($ch);
        $http = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($http !== 200) {
            $this->db->where('id', $row['id'])->update('bd_gmail_oauth_token', [
                'status' => 'error',
            ]);
            return ['ok' => false, 'error' => 'refresh_failed_http_' . $http];
        }
        $body = json_decode($resp, true);
        $access_token = $body['access_token'] ?? null;
        $expires_in   = $body['expires_in'] ?? 3500;
        if (!$access_token) return ['ok' => false, 'error' => 'no_access_token_in_response'];

        $expires_at = date('Y-m-d H:i:s', time() + (int)$expires_in - 60);
        $this->db->where('id', $row['id'])->update('bd_gmail_oauth_token', [
            'access_token'           => $access_token,
            'access_token_expires_at'=> $expires_at,
        ]);
        return ['ok' => true, 'access_token' => $access_token, 'gmail_address' => $row['gmail_address']];
    }

    protected function _invalidate_access_token($bd_uid)
    {
        $this->db->where('bd_uid', $bd_uid)->update('bd_gmail_oauth_token', [
            'access_token'           => null,
            'access_token_expires_at'=> null,
        ]);
    }

    // -----------------------------------------------------------------
    // Build RFC 2822 MIME message for Gmail send
    // -----------------------------------------------------------------
    protected function _build_mime($d, $from_address)
    {
        $boundary = 'STEM-' . md5(uniqid('', true));
        $to       = $d['recipient_name'] . ' <' . $d['recipient_email'] . '>';
        $subject  = '=?UTF-8?B?' . base64_encode($d['subject_line']) . '?=';

        $lines = [
            'From: ' . $from_address,
            'To: ' . $to,
            'Subject: ' . $subject,
            'MIME-Version: 1.0',
            'Content-Type: multipart/alternative; boundary="' . $boundary . '"',
            '',
            '--' . $boundary,
            'Content-Type: text/plain; charset="UTF-8"',
            'Content-Transfer-Encoding: 7bit',
            '',
            $d['body_plain'],
            '',
            '--' . $boundary,
            'Content-Type: text/html; charset="UTF-8"',
            'Content-Transfer-Encoding: 7bit',
            '',
            $d['body_html'],
            '',
            '--' . $boundary . '--',
        ];
        return implode("\r\n", $lines);
    }

    protected function _log_send($d, $status, $msg_id, $thread_id, $http, $err, $retry_count)
    {
        $this->db->insert('email_agent_send_log', [
            'draft_id'         => (int)$d['id'],
            'bd_uid'           => (int)$d['bd_uid'],
            'recipient_email'  => $d['recipient_email'],
            'cid_id'           => (int)$d['cid_id'],
            'send_status'      => $status,
            'gmail_message_id' => $msg_id,
            'gmail_thread_id'  => $thread_id,
            'http_status'      => $http,
            'error_code'       => $err ? 'http_' . $http : null,
            'error_message'    => $err ? substr($err, 0, 500) : null,
            'retry_count'      => $retry_count,
        ]);
    }

    // -----------------------------------------------------------------
    // CLI: expire_old - mark drafted rows older than expires_at as expired
    // -----------------------------------------------------------------
    public function expire_old()
    {
        $affected = $this->db->where('status', 'drafted')
            ->where('expires_at <=', date('Y-m-d H:i:s'))
            ->update('email_agent_draft', ['status' => 'expired']);
        log_message('info', $this->log_prefix . ' expired ' . $this->db->affected_rows() . ' drafts');
        return ['expired' => $this->db->affected_rows()];
    }

    // -----------------------------------------------------------------
    // Helpers
    // -----------------------------------------------------------------
    protected function _substitute($template_str, $vars)
    {
        $out = $template_str;
        foreach ($vars as $k => $v) {
            $out = str_replace('{{' . $k . '}}', (string)$v, $out);
        }
        return $out;
    }

    public function probe()
    {
        return [
            'migration'   => '026',
            'phase'       => 1,
            'deployed'    => $this->db->table_exists('email_agent_draft'),
            'ai_model'    => self::AI_MODEL,
            'expiry_hrs'  => self::DRAFT_EXPIRY_HOURS,
            'now'         => date('Y-m-d H:i:s'),
        ];
    }
}

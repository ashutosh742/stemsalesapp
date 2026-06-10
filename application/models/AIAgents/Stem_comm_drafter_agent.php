<?php
/**
 * STEM CRM - Migration 027 - Comm Drafter Agent
 *
 * Generates the email subject + plain body + html body for a comm draft.
 *
 * Flow:
 *   1. Build context bundle: lead, last 3 meetings, last 5 callevents, MoM
 *      action items, stakeholder contact, payload from event.
 *   2. Resolve template:
 *        - if inherits_from is set, fetch parent body from email_template
 *          (migration 026) and use as base
 *        - else use template's own subject_template + body_plain_template +
 *          body_html_template
 *   3. Fill required_context_fields from the context bundle. If any required
 *      field is missing, write to comm_draft_queue with status='needs_input'.
 *   4. Call GPT-4o-mini with ai_persona_instructions + filled template. Model
 *      may polish phrasing but must respect max_words and plain-English rules.
 *   5. Run output guards: strip em-dashes, ASCII enforce, Rs symbol replace,
 *      word count cap.
 *   6. Insert into comm_draft_queue with status='pending_review' (or
 *      'auto_approved' if BD has enabled trust-mode).
 *
 * GPT-4o-mini cost target:
 *   - Input  ~800 tokens (context + template + instructions)
 *   - Output ~250 tokens
 *   - At org scale 60 drafts/day -> Rs 60/day -> Rs 1,800/month
 *
 * Plain English. No em-dashes. No non-ASCII.
 *
 * Author: STEM Learning ops
 * Date: 17 May 2026
 */

class Comm_drafter_agent extends CI_Model {

    const MODEL = 'gpt-4o-mini';
    const MAX_OUTPUT_TOKENS = 600;
    const TEMPERATURE = 0.4;

    public function __construct() {
        parent::__construct();
        $this->load->database();
        $this->load->library('openai_client');
        $this->load->model('Stakeholder_contact_book_agent');
    }

    // ========================================================================
    // PUBLIC ENTRY
    // ========================================================================

    /**
     * draft_email - main entry called by Comm_orchestrator_agent.
     *
     * @param int   $event_log_id
     * @param array $template      comm_template_v2 row
     * @param array $recipients    output of resolve_recipients
     * @param array $event_payload from comm_event_log payload_json
     * @return int|false draft_id on success, false on error
     */
    public function draft_email($event_log_id, $template, $recipients, $event_payload) {
        $event = $this->db->get_where('comm_event_log', array('id' => $event_log_id))->row_array();
        if (empty($event)) {
            log_message('error', "[m027 drafter] event_log_id $event_log_id missing");
            return false;
        }

        $cid_id = (int) $event['cid_id'];
        $bd_uid = (int) $event['bd_uid'];

        // Ensure stakeholder book is initialised for this lead
        $this->Stakeholder_contact_book_agent->initialise_book($cid_id);

        // Build context bundle
        $context = $this->build_context($cid_id, $bd_uid, $recipients, $event_payload);
        if (empty($context)) {
            log_message('error', "[m027 drafter] context build failed for event $event_log_id");
            return false;
        }

        // Fill required fields
        $missing = $this->check_required_fields($template, $context);
        $needs_input = !empty($missing);

        // Resolve base body (inherited vs native)
        $base = $this->resolve_base_body($template);
        if (empty($base['body_plain_template']) || empty($base['subject_template'])) {
            log_message('error', "[m027 drafter] template {$template['template_code']} has empty body");
            return false;
        }

        // Pre-fill template variables locally before LLM call (cuts tokens)
        $prefilled = array(
            'subject'    => $this->fill_vars($base['subject_template'], $context),
            'body_plain' => $this->fill_vars($base['body_plain_template'], $context),
            'body_html'  => $this->fill_vars($base['body_html_template'], $context),
        );

        // GPT-4o-mini call
        $llm_start = microtime(true);
        $polished = $this->polish_with_llm($template, $prefilled, $context);
        $llm_ms = (int) ((microtime(true) - $llm_start) * 1000);

        if (empty($polished) || empty($polished['subject']) || empty($polished['body_plain'])) {
            log_message('error', "[m027 drafter] LLM returned empty for event $event_log_id");
            return false;
        }

        // Output guards
        $polished = $this->apply_output_guards($polished, (int) $template['max_words']);

        // Insert draft
        $row = array(
            'event_log_id'    => $event_log_id,
            'template_id'     => (int) $template['id'],
            'template_code'   => $template['template_code'],
            'cid_id'          => $cid_id,
            'bd_uid'          => $bd_uid,
            'recipient_to'    => $recipients['to'],
            'recipient_to_name' => isset($recipients['to_name']) ? $recipients['to_name'] : null,
            'recipient_cc'    => !empty($recipients['cc']) ? json_encode($recipients['cc']) : null,
            'subject'         => $polished['subject'],
            'body_plain'      => $polished['body_plain'],
            'body_html'       => $polished['body_html'],
            'attachment_path' => isset($event_payload['attachment_path']) ? $event_payload['attachment_path'] : null,
            'context_snapshot'=> json_encode($context),
            'ai_model'        => self::MODEL,
            'ai_latency_ms'   => $llm_ms,
            'ai_cost_usd'     => $this->estimate_cost($polished),
            'status'          => $needs_input ? 'needs_input' : 'pending_review',
            'needs_input_fields' => $needs_input ? json_encode($missing) : null,
            'expires_at'      => $this->compute_expiry($template, $event_payload),
            'created_at'      => date('Y-m-d H:i:s'),
        );

        $this->db->insert('comm_draft_queue', $row);
        $draft_id = $this->db->insert_id();

        log_message('info', "[m027 drafter] draft $draft_id created for event $event_log_id status={$row['status']}");
        return $draft_id;
    }

    // ========================================================================
    // CONTEXT
    // ========================================================================

    private function build_context($cid_id, $bd_uid, $recipients, $event_payload) {
        $lead = $this->db->get_where('init_call', array('id' => $cid_id))->row_array();
        if (empty($lead)) return null;

        $bd = $this->db->get_where('user', array('uid' => $bd_uid))->row_array();
        if (empty($bd)) return null;

        $cm = !empty($lead['cm_id']) ? $this->db->get_where('user', array('uid' => $lead['cm_id']))->row_array() : null;

        $last_meetings = $this->db->select('createDate, purpose_id, remarks')
            ->from('tblcallevents')
            ->where('cid_id', $cid_id)
            ->where_in('actiontype_id', array(3, 4))
            ->order_by('createDate', 'DESC')->limit(3)->get()->result_array();

        $last_mom = $this->db->select('id, action_items_text, createDate')
            ->from('mom_data')
            ->where('cid_id', $cid_id)
            ->where('approved_status', '1')
            ->order_by('createDate', 'DESC')->limit(1)->get()->row_array();

        // Compose
        $context = array(
            'bd_name'        => $bd['full_name'],
            'bd_phone'       => isset($bd['phone']) ? $bd['phone'] : '',
            'bd_email'       => isset($bd['email']) ? $bd['email'] : '',
            'cm_name'        => !empty($cm) ? $cm['full_name'] : 'our customer success team',
            'school_name'    => $lead['school_name'],
            'cstatus'        => (int) $lead['cstatus'],
            'stakeholder_first_name' => isset($recipients['to_first_name']) ? $recipients['to_first_name'] : 'Sir',
            'last_meeting_topic'     => !empty($last_meetings) ? $this->lookup_purpose($last_meetings[0]['purpose_id']) : 'our previous conversation',
            'last_meeting_date'      => !empty($last_meetings) ? $last_meetings[0]['createDate'] : null,
            'mom_action_items'       => !empty($last_mom) ? $last_mom['action_items_text'] : '',
            'suggested_slot_one'     => $this->suggest_slot(1),
            'suggested_slot_two'     => $this->suggest_slot(2),
            'social_proof_one_line'  => 'positive outcomes after deploying our integrated STEM lab',
            'followup_date'          => date('Y-m-d', strtotime('+3 days')),
        );

        // Event-specific payload merged in (overrides defaults)
        foreach ($event_payload as $k => $v) {
            $context[$k] = $v;
        }

        // Convenience aliases
        if (isset($context['call_time_iso'])) {
            $context['call_time'] = date('h:i A', strtotime($context['call_time_iso']));
        }
        if (isset($context['purpose_label']) && empty($context['call_purpose'])) {
            $context['call_purpose'] = $context['purpose_label'];
        }

        return $context;
    }

    private function check_required_fields($template, $context) {
        $required = json_decode($template['required_context_fields'], true);
        if (!is_array($required)) return array();

        $missing = array();
        foreach ($required as $field) {
            if (empty($context[$field])) {
                $missing[] = $field;
            }
        }
        return $missing;
    }

    private function resolve_base_body($template) {
        // Native template
        if (empty($template['inherits_from'])) {
            return array(
                'subject_template'   => $template['subject_template'],
                'body_plain_template'=> $template['body_plain_template'],
                'body_html_template' => $template['body_html_template'],
            );
        }

        // Inherited from migration 026
        $parent = $this->db->get_where('email_template', array('id' => $template['inherits_from']))->row_array();
        if (empty($parent)) {
            log_message('error', "[m027 drafter] inherits_from {$template['inherits_from']} not found for {$template['template_code']}");
            return array();
        }

        return array(
            'subject_template'   => $parent['subject_template'],
            'body_plain_template'=> $parent['body_plain_template'],
            'body_html_template' => $parent['body_html_template'],
        );
    }

    // ========================================================================
    // VARIABLE FILL
    // ========================================================================

    private function fill_vars($template_text, $context) {
        if (empty($template_text)) return '';

        return preg_replace_callback('/\{\{([a-zA-Z0-9_]+)\}\}/', function ($matches) use ($context) {
            $key = $matches[1];
            return isset($context[$key]) ? (string) $context[$key] : '[' . $key . ' missing]';
        }, $template_text);
    }

    // ========================================================================
    // LLM POLISH
    // ========================================================================

    private function polish_with_llm($template, $prefilled, $context) {
        $system_prompt = $template['ai_persona_instructions'] . "\n\n"
            . "Hard rules:\n"
            . "- Plain English only\n"
            . "- Never use em-dashes\n"
            . "- Never use non-ASCII characters\n"
            . "- Use 'Rs' for rupees, never the rupee symbol\n"
            . "- Spell out 'percent', do not use %\n"
            . "- Use 'over' for greater than\n"
            . "- Respect max_words on plain body: " . (int) $template['max_words'] . "\n"
            . "- Return JSON only with keys: subject, body_plain, body_html\n";

        $user_prompt = "Polish this draft email. Keep the structure, fix any awkward phrasing, "
            . "ensure the tone matches the persona. Do not invent facts not in the draft.\n\n"
            . "DRAFT SUBJECT:\n" . $prefilled['subject'] . "\n\n"
            . "DRAFT BODY PLAIN:\n" . $prefilled['body_plain'] . "\n\n"
            . "DRAFT BODY HTML:\n" . $prefilled['body_html'] . "\n\n"
            . "Return JSON: { \"subject\": \"...\", \"body_plain\": \"...\", \"body_html\": \"...\" }";

        try {
            $response = $this->openai_client->chat_completion(array(
                'model'       => self::MODEL,
                'temperature' => self::TEMPERATURE,
                'max_tokens'  => self::MAX_OUTPUT_TOKENS,
                'response_format' => array('type' => 'json_object'),
                'messages' => array(
                    array('role' => 'system', 'content' => $system_prompt),
                    array('role' => 'user',   'content' => $user_prompt),
                ),
            ));

            $content = $response['choices'][0]['message']['content'];
            $parsed = json_decode($content, true);

            if (empty($parsed['subject']) || empty($parsed['body_plain'])) {
                log_message('error', "[m027 drafter] LLM returned malformed JSON, falling back to prefilled");
                return $prefilled;
            }

            // body_html optional - reuse prefilled if missing
            if (empty($parsed['body_html'])) {
                $parsed['body_html'] = $prefilled['body_html'];
            }

            return $parsed;
        } catch (Exception $e) {
            log_message('error', "[m027 drafter] LLM exception: " . $e->getMessage() . ", falling back to prefilled");
            return $prefilled;
        }
    }

    // ========================================================================
    // GUARDS
    // ========================================================================

    private function apply_output_guards($draft, $max_words) {
        foreach (array('subject', 'body_plain', 'body_html') as $field) {
            if (empty($draft[$field])) continue;
            // Strip em-dashes, en-dashes
            $draft[$field] = str_replace(array("\xe2\x80\x94", "\xe2\x80\x93", "—", "–"), "-", $draft[$field]);
            // Strip rupee symbol
            $draft[$field] = str_replace(array("\xe2\x82\xb9", "₹"), "Rs ", $draft[$field]);
            // Strip percent symbol in plain body (keep in html for now)
            if ($field === 'body_plain' || $field === 'subject') {
                $draft[$field] = str_replace('%', ' percent', $draft[$field]);
            }
            // ASCII enforce (replace any remaining non-ASCII with space)
            $draft[$field] = preg_replace('/[^\x20-\x7E\n\r\t]/', ' ', $draft[$field]);
            $draft[$field] = preg_replace('/  +/', ' ', $draft[$field]);
        }

        // Word count cap on plain body
        if ($max_words > 0) {
            $words = preg_split('/\s+/', $draft['body_plain']);
            if (count($words) > $max_words + 20) { // 20-word grace
                $draft['body_plain'] = implode(' ', array_slice($words, 0, $max_words)) . ' [truncated]';
            }
        }

        return $draft;
    }

    // ========================================================================
    // HELPERS
    // ========================================================================

    private function lookup_purpose($purpose_id) {
        $row = $this->db->select('purpose_name')->from('purpose_master')
            ->where('id', $purpose_id)->limit(1)->get()->row();
        return !empty($row) ? $row->purpose_name : 'our previous discussion';
    }

    private function suggest_slot($n) {
        // Suggest next 2 working days at 11 AM and 4 PM
        $base = strtotime('+1 day');
        $day = ($n === 1) ? $base : strtotime('+2 days');
        // Skip Sunday
        if (date('N', $day) === '7') $day = strtotime('+1 day', $day);
        $hour = ($n === 1) ? '11:00 AM' : '4:00 PM';
        return date('l j M', $day) . ' at ' . $hour;
    }

    private function estimate_cost($draft) {
        // Rough estimate: input ~800 tokens, output ~250 tokens at gpt-4o-mini
        // rates ($0.150 per 1M input, $0.600 per 1M output)
        $input_cost = 800 / 1000000 * 0.150;
        $output_words = str_word_count($draft['body_plain'] . ' ' . $draft['subject']);
        $output_tokens = (int) ($output_words * 1.33);
        $output_cost = $output_tokens / 1000000 * 0.600;
        return round($input_cost + $output_cost, 6);
    }

    private function compute_expiry($template, $event_payload) {
        // Drafts expire if not reviewed within 24 hours for most events.
        // Dormant re-engage drafts expire after 7 days (low urgency).
        $hours = ($template['event_type'] === 'dormant_re_engage') ? 168 : 24;
        return date('Y-m-d H:i:s', time() + ($hours * 3600));
    }
}

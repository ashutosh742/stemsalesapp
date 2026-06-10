<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * STEM CRM - Migration 030 - Email Insight Agent
 *
 * For each new email_message_log row that has not yet been processed:
 *   1. Call internal LLM endpoint to classify sentiment, intent, next action.
 *   2. Write result to email_insight table.
 *   3. If intent is decision, next_step, or objection AND confidence over 0.75,
 *      write a row to lead_progression_log (trigger also handles this, but
 *      agent writes as a fallback if trigger is disabled).
 *   4. Mark email_message_log.processed_at = NOW().
 *
 * Phase gate:
 *   - Insights are generated and stored from 25 May 2026 (feature flag >= 1).
 *   - Insights are surfaced to the UI only from 1 Jun 2026 (feature flag >= 2).
 *   - The agent runs regardless of UI flag; storage always on if sync is on.
 *
 * LLM endpoint: TODO - confirm with infra. Assumed:
 *   POST https://stemapp.in/internal/llm/classify
 *   Body: { "prompt": "...", "max_tokens": 120 }
 *   Response: { "result": "{\"sentiment\":\"...\", ...}" }
 *
 * Plain English. No em-dashes. No non-ASCII. Rs for rupees.
 *
 * Author: STEM Learning ops
 * Date: 2026-05-19
 */
class Email_insight_agent extends CI_Model
{
    // LLM endpoint
    const LLM_URL        = 'https://stemapp.in/internal/llm/classify'; // TODO: confirm
    const LLM_MAX_TOKENS = 120;
    const LLM_TIMEOUT_S  = 10;

    // High-signal intents that write to lead_progression_log
    const SIGNAL_INTENTS = ['decision', 'next_step', 'objection'];
    const SIGNAL_CONFIDENCE_THRESHOLD = 0.75;

    // Batch size per cron run
    const BATCH_SIZE = 100;

    // Fallback values when LLM fails
    const DEFAULT_SENTIMENT = 'neutral';
    const DEFAULT_INTENT    = 'question';

    private $log_prefix = '[email_insight_agent]';

    public function __construct()
    {
        parent::__construct();
        $this->load->database();
        $this->load->config('email_oauth');
    }

    // ========================================================================
    // MAIN ENTRY POINT: process all unprocessed email_message_log rows
    // Called by Email_oauth_agent after each email poll.
    // Also callable from cron for recovery:
    //   php index.php email_insight_agent process_new_messages
    // ========================================================================
    public function process_new_messages()
    {
        $rows = $this->_get_unprocessed_messages();

        if (empty($rows)) {
            log_message('info', $this->log_prefix . ' no unprocessed messages');
            return 0;
        }

        $processed = 0;
        foreach ($rows as $row) {
            $success = $this->_process_single_message($row);
            if ($success) {
                $processed++;
            }
        }

        log_message('info', $this->log_prefix . ' processed=' . $processed
            . ' of=' . count($rows));
        return $processed;
    }

    // ========================================================================
    // PROCESS A SINGLE MESSAGE (also callable directly for real-time use)
    // ========================================================================
    public function process_single_by_id($message_log_id)
    {
        $row = $this->db->get_where('email_message_log', ['id' => $message_log_id])->row_array();
        if (!$row) {
            return ['ok' => false, 'error' => 'not_found'];
        }
        $ok = $this->_process_single_message($row);
        return ['ok' => $ok];
    }

    // ========================================================================
    // PRIVATE: CORE CLASSIFICATION PIPELINE
    // ========================================================================
    private function _process_single_message(array $row)
    {
        $msg_id = $row['id'];

        // Build prompt
        $prompt = $this->_build_prompt($row);

        // Call LLM
        $raw_result = $this->_call_llm($prompt);

        // Parse LLM response
        $classification = $this->_parse_llm_response($raw_result);

        // Write email_insight row
        $insight_id = $this->_write_insight($msg_id, $classification);

        if (!$insight_id) {
            log_message('error', $this->log_prefix . ' failed to write insight for msg=' . $msg_id);
            return false;
        }

        // Write to lead_progression_log if high-signal intent (fallback - trigger also handles this)
        if (!empty($row['lead_id'])
            && in_array($classification['intent'], self::SIGNAL_INTENTS)
            && $classification['confidence'] >= self::SIGNAL_CONFIDENCE_THRESHOLD) {

            $this->_write_progression_signal($row, $classification, $insight_id);
        }

        // Mark processed
        $this->db->where('id', $msg_id)
                 ->update('email_message_log', ['processed_at' => date('Y-m-d H:i:s')]);

        return true;
    }

    // ========================================================================
    // PRIVATE: BUILD LLM PROMPT
    // ========================================================================
    private function _build_prompt(array $row)
    {
        $direction_label = ($row['direction'] === 'in') ? 'Inbound (from school contact)' : 'Outbound (sent by BD)';
        $subject  = $row['subject']       ?? '(no subject)';
        $snippet  = $row['body_snippet']  ?? '(empty)';

        $system = "You are an email analysis assistant for a B2B sales CRM. "
                . "Analyze the email below and respond ONLY with a JSON object. "
                . "Do not include any text outside the JSON.";

        $user = "Direction: {$direction_label}\n"
              . "Subject: {$subject}\n"
              . "Snippet: {$snippet}\n\n"
              . "Classify the email and return this JSON structure:\n"
              . "{\n"
              . "  \"sentiment\": \"positive|neutral|negative\",\n"
              . "  \"intent\": \"question|objection|decision|next_step|chase\",\n"
              . "  \"suggested_next_action\": \"one plain English sentence under 80 chars\",\n"
              . "  \"confidence\": 0.0\n"
              . "}\n\n"
              . "Intent guide:\n"
              . "  question    - sender is asking for information\n"
              . "  objection   - sender is pushing back or raising a concern\n"
              . "  decision    - sender signals a buying decision (yes or no)\n"
              . "  next_step   - sender is agreeing to a next action (meeting, demo)\n"
              . "  chase       - sender is following up on a pending item\n"
              . "confidence is 0.0 to 1.0 (your certainty in the classification).";

        return $system . "\n\n" . $user;
    }

    // ========================================================================
    // PRIVATE: CALL INTERNAL LLM ENDPOINT
    // Returns raw string from LLM or empty string on failure.
    // ========================================================================
    private function _call_llm($prompt)
    {
        $payload = json_encode([
            'prompt'     => $prompt,
            'max_tokens' => self::LLM_MAX_TOKENS,
        ]);

        $ch = curl_init(self::LLM_URL);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST           => true,
            CURLOPT_TIMEOUT        => self::LLM_TIMEOUT_S,
            CURLOPT_POSTFIELDS     => $payload,
            CURLOPT_HTTPHEADER     => [
                'Content-Type: application/json',
                'Content-Length: ' . strlen($payload),
            ],
        ]);

        $start    = microtime(true);
        $response = curl_exec($ch);
        $elapsed  = round((microtime(true) - $start) * 1000);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($response === false || $http_code !== 200) {
            log_message('error', $this->log_prefix . ' LLM call failed http=' . $http_code
                . ' elapsed=' . $elapsed . 'ms');
            return '';
        }

        log_message('debug', $this->log_prefix . ' LLM call ok elapsed=' . $elapsed . 'ms');

        $decoded = json_decode($response, true);
        return $decoded['result'] ?? $response;
    }

    // ========================================================================
    // PRIVATE: PARSE LLM RESPONSE INTO VALIDATED CLASSIFICATION ARRAY
    // Returns array with sentiment, intent, suggested_next_action, confidence.
    // Falls back to defaults if parsing fails.
    // ========================================================================
    private function _parse_llm_response($raw)
    {
        $defaults = [
            'sentiment'             => self::DEFAULT_SENTIMENT,
            'intent'                => self::DEFAULT_INTENT,
            'suggested_next_action' => null,
            'confidence'            => 0.5,
        ];

        if (empty($raw)) {
            return $defaults;
        }

        // Try to extract JSON block if LLM wrapped it in text
        if (preg_match('/\{[^{}]+\}/s', $raw, $m)) {
            $json_str = $m[0];
        } else {
            $json_str = $raw;
        }

        $parsed = json_decode($json_str, true);
        if (!is_array($parsed)) {
            log_message('error', $this->log_prefix . ' could not parse LLM response: ' . substr($raw, 0, 200));
            return $defaults;
        }

        $valid_sentiments = ['positive', 'neutral', 'negative'];
        $valid_intents    = ['question', 'objection', 'decision', 'next_step', 'chase'];

        $sentiment = in_array($parsed['sentiment'] ?? '', $valid_sentiments)
            ? $parsed['sentiment']
            : self::DEFAULT_SENTIMENT;

        $intent = in_array($parsed['intent'] ?? '', $valid_intents)
            ? $parsed['intent']
            : self::DEFAULT_INTENT;

        $confidence = isset($parsed['confidence'])
            ? min(1.0, max(0.0, (float)$parsed['confidence']))
            : 0.5;

        $action = !empty($parsed['suggested_next_action'])
            ? substr($parsed['suggested_next_action'], 0, 512)
            : null;

        return [
            'sentiment'             => $sentiment,
            'intent'                => $intent,
            'suggested_next_action' => $action,
            'confidence'            => $confidence,
        ];
    }

    // ========================================================================
    // PRIVATE: WRITE email_insight ROW
    // Returns insert id or false.
    // ========================================================================
    private function _write_insight($email_message_log_id, array $classification)
    {
        // Check for existing insight (avoid double-processing)
        $existing = $this->db->get_where('email_insight',
            ['email_message_log_id' => $email_message_log_id])->row_array();
        if ($existing) {
            return $existing['id'];
        }

        $data = [
            'email_message_log_id'  => $email_message_log_id,
            'sentiment'             => $classification['sentiment'],
            'intent'                => $classification['intent'],
            'suggested_next_action' => $classification['suggested_next_action'],
            'confidence'            => number_format($classification['confidence'], 3, '.', ''),
            'generated_at'          => date('Y-m-d H:i:s'),
        ];

        $this->db->insert('email_insight', $data);
        $insert_id = $this->db->insert_id();

        if (!$insert_id) {
            log_message('error', $this->log_prefix . ' insert failed for msg=' . $email_message_log_id);
            return false;
        }

        return $insert_id;
    }

    // ========================================================================
    // PRIVATE: WRITE PROGRESSION SIGNAL (fallback - trigger handles primary)
    // Writes to lead_progression_log for high-signal email intents.
    // Only writes if trigger has not already created the row (check by source_id).
    // ========================================================================
    private function _write_progression_signal(array $msg_row, array $classification, $insight_id)
    {
        // Check if trigger already wrote this row
        $sql = "SELECT id FROM lead_progression_log
                 WHERE event_type = 'email_insight_signal'
                   AND source_id  = ?
                 LIMIT 1";
        $already = $this->db->query($sql, [$insight_id])->row_array();
        if ($already) {
            return;
        }

        $payload = json_encode([
            'insight_id'            => $insight_id,
            'intent'                => $classification['intent'],
            'sentiment'             => $classification['sentiment'],
            'suggested_next_action' => $classification['suggested_next_action'],
            'confidence'            => $classification['confidence'],
            'email_subject'         => $msg_row['subject'],
        ]);

        $this->db->insert('lead_progression_log', [
            'cid_id'      => $msg_row['lead_id'],
            'bd_uid'      => $msg_row['uid'],
            'event_type'  => 'email_insight_signal',
            'source_id'   => $insight_id,
            'payload'     => $payload,
            'created_at'  => date('Y-m-d H:i:s'),
        ]);

        log_message('info', $this->log_prefix . ' progression signal written lead='
            . $msg_row['lead_id'] . ' intent=' . $classification['intent']
            . ' confidence=' . $classification['confidence']);
    }

    // ========================================================================
    // PRIVATE: FETCH UNPROCESSED MESSAGES
    // Only fetches messages for uids with feature_flag_override >= 1.
    // ========================================================================
    private function _get_unprocessed_messages()
    {
        $sql = "
            SELECT eml.*
              FROM email_message_log eml
              INNER JOIN feature_flag_override ffo
                      ON ffo.uid = eml.uid
                     AND ffo.flag_name = 'email_capture_enabled'
                     AND ffo.flag_value >= 1
              WHERE eml.processed_at IS NULL
              ORDER BY eml.received_at ASC
              LIMIT ?
        ";
        return $this->db->query($sql, [self::BATCH_SIZE])->result_array();
    }

    // ========================================================================
    // PUBLIC: MARK ACTION TAKEN ON AN INSIGHT
    // Called by controller POST /api/email/insight/action_taken
    // ========================================================================
    public function mark_action_taken($insight_id, $acting_uid)
    {
        $row = $this->db->get_where('email_insight', ['id' => $insight_id])->row_array();
        if (!$row) {
            return ['ok' => false, 'error' => 'not_found'];
        }

        if ($row['action_taken']) {
            return ['ok' => true, 'already_done' => true];
        }

        $this->db->where('id', $insight_id)->update('email_insight', [
            'action_taken'     => 1,
            'action_taken_at'  => date('Y-m-d H:i:s'),
            'action_taken_uid' => $acting_uid,
        ]);

        log_message('info', $this->log_prefix . ' action_taken insight=' . $insight_id
            . ' by uid=' . $acting_uid);
        return ['ok' => true];
    }

    // ========================================================================
    // PUBLIC: GET INSIGHT FOR A MESSAGE
    // Called by controller GET /api/email/insight?message_id=
    // Returns insight row with message snippet or null if not found.
    // ========================================================================
    public function get_insight_by_message_id($message_log_id)
    {
        $sql = "
            SELECT
              ei.id              AS insight_id,
              ei.sentiment,
              ei.intent,
              ei.suggested_next_action,
              ei.confidence,
              ei.action_taken,
              ei.action_taken_at,
              ei.generated_at,
              eml.subject,
              eml.body_snippet,
              eml.from_addr,
              eml.direction,
              eml.received_at
            FROM email_insight ei
            INNER JOIN email_message_log eml ON eml.id = ei.email_message_log_id
            WHERE ei.email_message_log_id = ?
            LIMIT 1
        ";
        return $this->db->query($sql, [(int)$message_log_id])->row_array();
    }
}
// End of Email_insight_agent

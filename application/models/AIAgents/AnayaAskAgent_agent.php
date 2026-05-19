<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Anaya Ask Agent
 *
 * Conversational natural-language query core for migration 032.
 *
 * Responsibilities:
 *   1. Receive plain-English question plus user context (uid, role, cluster).
 *   2. Classify intent: lookup | aggregate | comparison | action.
 *   3. Build a role-aware prompt with schema documentation and call the LLM.
 *   4. Extract the SQL candidate from the LLM response.
 *   5. Pass SQL candidate to NlToSqlGuard for safety validation.
 *   6. Execute read-only SQL with 10s timeout and 500-row cap.
 *   7. Format the result as a table or single number with follow-up suggestions.
 *   8. Persist session, messages, and audit log rows.
 *
 * This agent is read-only in phase 1 (migration 032).
 * Write actions are deferred to migration 032.1.
 *
 * Used by: AnayaAsk controller (stem_anaya_ask_controller_php.php)
 *
 * LLM endpoint: TODO - set STEM_ASK_LLM_ENDPOINT in environment.
 *               See deploy runbook Section 2 for registration steps.
 *
 * Author: STEM ops
 * Migration: 032
 * Date: 2026-05-20
 */
class Anaya_ask_agent
{
    // -------------------------------------------------------------------------
    // CONSTANTS
    // -------------------------------------------------------------------------
    const MIGRATION         = '032';
    const SQL_ROW_CAP       = 500;
    const SQL_TIMEOUT_MS    = 10000;  // 10 seconds
    const UI_ROW_CAP        = 20;     // max rows shown in chat bubble
    const ALLOWLIST_TTL_SEC = 300;    // cache allowlist for 5 minutes
    const MAX_SESSION_IDLE  = 7200;   // 2 hours in seconds

    // Daily quota per role. Director = 0 means unlimited.
    const QUOTA = [
        'bd'       => 20,
        'cm'       => 50,
        'rm'       => 100,
        'director' => 0,
    ];

    // Intent labels
    const INTENT_LOOKUP     = 'lookup';
    const INTENT_AGGREGATE  = 'aggregate';
    const INTENT_COMPARISON = 'comparison';
    const INTENT_ACTION     = 'action';

    // Pilot uids (25 May 2026 pilot)
    const PILOT_UIDS = [42, 43, 44, 45, 46, 12];

    // Pilot quota override (halved). Read from env if set.
    const PILOT_QUOTA_BD = 10;
    const PILOT_QUOTA_CM = 25;

    protected $CI;
    protected $db;
    protected $guard;
    protected $log_prefix = '[anaya_ask]';

    // -------------------------------------------------------------------------
    // CONSTRUCTOR
    // -------------------------------------------------------------------------
    public function __construct()
    {
        $this->CI =& get_instance();
        $this->CI->load->database();
        $this->db = $this->CI->db;

        require_once APPPATH . 'models/AIAgents/NlToSqlGuard.php';
        $this->guard = new NlToSqlGuard();
    }

    // =========================================================================
    // PUBLIC: handle_query
    // Entry point called by the controller.
    // Returns an array suitable for JSON response.
    // =========================================================================
    public function handle_query($uid, $role, $cluster_id, $region_id, $question, $session_id = null)
    {
        $start_ms = $this->_now_ms();

        // --- 1. Quota check -------------------------------------------------
        $quota_result = $this->_check_quota($uid, $role);
        if ($quota_result['denied']) {
            return [
                'ok'          => false,
                'error'       => 'quota_exceeded',
                'message'     => $quota_result['message'],
                'session_id'  => $session_id,
                'quota_used'  => $quota_result['used'],
                'quota_limit' => $quota_result['limit'],
            ];
        }

        // --- 2. Session management ------------------------------------------
        if (!$session_id) {
            $session_id = $this->_create_session($uid, $role, $cluster_id, $region_id);
        } else {
            $this->_touch_session($session_id);
        }

        // --- 3. Persist user message ----------------------------------------
        $user_msg_id = $this->_save_message($session_id, 'user', $question);

        // --- 4. Classify intent ---------------------------------------------
        $intent = $this->_classify_intent($question);

        if ($intent === self::INTENT_ACTION) {
            $response_text = 'Write actions are not available yet in Ask Anaya. ' .
                'I can look up information, calculate totals, and compare data. ' .
                'To update a lead or log a task, use the main CRM screen.';
            $this->_save_message($session_id, 'assistant', $response_text,
                null, null, $this->_elapsed($start_ms), false, null);
            $this->_audit($uid, $role, $session_id, $user_msg_id, $question,
                null, 'action_intent_phase1_denied', null, $this->_elapsed($start_ms));
            return $this->_ok_response($session_id, $response_text, null, 0,
                $this->_elapsed($start_ms), $this->_suggestions($role, $intent));
        }

        // --- 5. Build prompt and call LLM -----------------------------------
        $schema_doc = $this->_build_schema_doc($role, $uid, $cluster_id, $region_id);
        $prompt     = $this->_build_prompt($question, $intent, $schema_doc, $role, $uid, $cluster_id, $region_id);

        $llm_result = $this->_call_llm($prompt);
        if (!$llm_result['ok']) {
            $this->_audit($uid, $role, $session_id, $user_msg_id, $question,
                null, 'llm_call_failed', null, $this->_elapsed($start_ms));
            $msg = 'Ask Anaya could not reach the query engine right now. ' .
                'Please try again in a moment.';
            $this->_save_message($session_id, 'assistant', $msg,
                null, null, $this->_elapsed($start_ms));
            return $this->_ok_response($session_id, $msg, null, 0,
                $this->_elapsed($start_ms), $this->_suggestions($role, $intent));
        }

        // --- 6. Extract SQL candidate from LLM response ---------------------
        $sql_candidate = $this->_extract_sql($llm_result['text']);
        if (!$sql_candidate) {
            // LLM returned a conversational answer without SQL - return it as-is
            $response_text = $this->_clean_llm_text($llm_result['text']);
            $this->_save_message($session_id, 'assistant', $response_text,
                null, null, $this->_elapsed($start_ms));
            $this->_audit($uid, $role, $session_id, $user_msg_id, $question,
                null, null, null, $this->_elapsed($start_ms));
            return $this->_ok_response($session_id, $response_text, null, 0,
                $this->_elapsed($start_ms), $this->_suggestions($role, $intent));
        }

        // --- 7. Validate SQL through guard ----------------------------------
        $guard_result = $this->guard->validate(
            $sql_candidate, $uid, $role, $cluster_id, $region_id
        );

        if (!$guard_result['ok']) {
            $reason = $guard_result['reason'];
            $response_text = $this->_denial_message($reason, $question);
            $this->_save_message($session_id, 'assistant', $response_text,
                null, null, $this->_elapsed($start_ms), true, $reason);
            $this->_audit($uid, $role, $session_id, $user_msg_id, $question,
                null, $reason, null, $this->_elapsed($start_ms));
            return $this->_ok_response($session_id, $response_text, null, 0,
                $this->_elapsed($start_ms), $this->_suggestions($role, $intent));
        }

        $safe_sql = $guard_result['sql'];

        // --- 8. Execute read-only SQL ---------------------------------------
        $exec_result = $this->_execute_safe_sql($safe_sql);

        if (!$exec_result['ok']) {
            $response_text = 'The query ran into a database error. ' .
                'This has been logged. Please try rephrasing the question.';
            $this->_save_message($session_id, 'assistant', $response_text,
                $safe_sql, null, $this->_elapsed($start_ms), true, 'db_exec_error');
            $this->_audit($uid, $role, $session_id, $user_msg_id, $question,
                $safe_sql, 'db_exec_error', null, $this->_elapsed($start_ms));
            return $this->_ok_response($session_id, $response_text, null, 0,
                $this->_elapsed($start_ms), $this->_suggestions($role, $intent));
        }

        $rows         = $exec_result['rows'];
        $row_count    = count($rows);
        $latency      = $this->_elapsed($start_ms);

        // --- 9. Format result -----------------------------------------------
        $formatted    = $this->_format_result($rows, $question, $intent, $row_count);
        $response_text = $formatted['text'];
        $table_data    = $formatted['table'];    // null or array

        // --- 10. Persist assistant message and audit ------------------------
        $asst_msg_id = $this->_save_message(
            $session_id, 'assistant', $response_text,
            $safe_sql, $row_count, $latency
        );
        $this->_audit($uid, $role, $session_id, $asst_msg_id, $question,
            $safe_sql, null, $row_count, $latency);
        $this->_increment_session_count($session_id);

        return array_merge(
            $this->_ok_response($session_id, $response_text, $table_data,
                $row_count, $latency, $this->_suggestions($role, $intent)),
            ['message_id' => $asst_msg_id, 'sql_executed' => $safe_sql]
        );
    }

    // =========================================================================
    // PUBLIC: get_session_messages
    // Returns all messages in a session ordered by created_at.
    // =========================================================================
    public function get_session_messages($session_id, $uid)
    {
        $rows = $this->db->query(
            "SELECT m.id, m.role, m.text, m.rows_returned, m.latency_ms,
                    m.feedback, m.denied, m.created_at
               FROM ask_message m
               INNER JOIN ask_session s ON s.id = m.session_id
              WHERE m.session_id = ? AND s.uid = ?
              ORDER BY m.created_at ASC",
            [$session_id, $uid]
        )->result_array();

        return ['ok' => true, 'session_id' => $session_id, 'messages' => $rows];
    }

    // =========================================================================
    // PUBLIC: record_feedback
    // =========================================================================
    public function record_feedback($message_id, $session_id, $uid, $feedback)
    {
        if (!in_array($feedback, ['good', 'bad'])) {
            return ['ok' => false, 'error' => 'invalid_feedback'];
        }
        // Verify ownership
        $row = $this->db->query(
            "SELECT m.id FROM ask_message m
               INNER JOIN ask_session s ON s.id = m.session_id
              WHERE m.id = ? AND s.uid = ? AND m.role = 'assistant'",
            [$message_id, $uid]
        )->row_array();

        if (!$row) return ['ok' => false, 'error' => 'not_found'];

        $this->db->query(
            "UPDATE ask_message SET feedback = ? WHERE id = ?",
            [$feedback, $message_id]
        );
        return ['ok' => true, 'message_id' => $message_id, 'feedback' => $feedback];
    }

    // =========================================================================
    // PUBLIC: get_suggestions
    // Role-aware quick-prompt suggestions for the UI.
    // =========================================================================
    public function get_suggestions($role)
    {
        $map = [
            'bd' => [
                'Show me my stuck leads over 30 days',
                'What is my conversion rate this week',
                'How many proposals are pending',
                'Which of my leads have no purpose task',
                'Show me my visits planned for this week',
            ],
            'cm' => [
                'Which BDs missed CM joint visits yesterday',
                'Show me stuck leads in my cluster',
                'What is the cluster conversion rate this week',
                'Which BDs have hygiene breaches this week',
                'Give me a pipeline summary for each BD',
            ],
            'rm' => [
                'What is my total pipeline value this month',
                'Compare CM performance this week',
                'Which clusters have the worst MoM rates',
                'Show stagnant leads over 30 days in my region',
                'Which BDs have the most open leads',
            ],
            'director' => [
                'What is the org conversion rate this week',
                'Show all stuck leads over 30 days in Mumbai cluster',
                'Which BDs missed CM joint visits yesterday',
                'Compare pipeline value across all regions',
                'Which clusters have the most hygiene breaches',
            ],
        ];
        return $map[$role] ?? $map['bd'];
    }

    // =========================================================================
    // PUBLIC: get_usage
    // Admin-only usage summary.
    // =========================================================================
    public function get_usage()
    {
        $rows = $this->db->query(
            "SELECT * FROM v_ask_usage_today ORDER BY query_count DESC"
        )->result_array();
        $denied = $this->db->query(
            "SELECT COUNT(*) AS cnt FROM v_ask_denied_today"
        )->row_array();
        return [
            'ok'             => true,
            'date'           => date('Y-m-d'),
            'users'          => $rows,
            'denied_today'   => (int)($denied['cnt'] ?? 0),
        ];
    }

    // =========================================================================
    // PRIVATE: intent classification
    // =========================================================================
    private function _classify_intent($question)
    {
        $q = strtolower(trim($question));

        // Action keywords first - deny in phase 1
        $action_keywords = ['update', 'change', 'set ', 'mark ', 'add task',
            'create task', 'log a task', 'delete', 'remove', 'reassign',
            'move lead', 'close lead', 'promote'];
        foreach ($action_keywords as $kw) {
            if (strpos($q, $kw) !== false) return self::INTENT_ACTION;
        }

        // Aggregate
        $aggregate_keywords = ['how many', 'count', 'total', 'sum', 'average',
            'rate', 'percent', 'conversion', 'what is my', 'what is the'];
        foreach ($aggregate_keywords as $kw) {
            if (strpos($q, $kw) !== false) return self::INTENT_AGGREGATE;
        }

        // Comparison
        $compare_keywords = ['compare', 'vs ', 'versus', 'rank', 'best', 'worst',
            'top ', 'bottom ', 'which is better', 'most', 'least'];
        foreach ($compare_keywords as $kw) {
            if (strpos($q, $kw) !== false) return self::INTENT_COMPARISON;
        }

        return self::INTENT_LOOKUP;
    }

    // =========================================================================
    // PRIVATE: LLM call
    // =========================================================================
    private function _call_llm($prompt)
    {
        $endpoint = getenv('STEM_ASK_LLM_ENDPOINT');
        $api_key  = getenv('STEM_ASK_LLM_API_KEY');

        // TODO: set STEM_ASK_LLM_ENDPOINT and STEM_ASK_LLM_API_KEY in environment
        // before pilot deploy. See deploy runbook Section 2.
        if (!$endpoint) {
            log_message('error', $this->log_prefix . ' STEM_ASK_LLM_ENDPOINT not set');
            return ['ok' => false, 'text' => null, 'error' => 'endpoint_not_configured'];
        }

        $payload = json_encode([
            'model'      => getenv('STEM_ASK_LLM_MODEL') ?: 'gpt-4o-mini',
            'messages'   => [['role' => 'user', 'content' => $prompt]],
            'max_tokens' => 1024,
            'temperature'=> 0,
        ]);

        $ch = curl_init($endpoint);
        curl_setopt_array($ch, [
            CURLOPT_POST           => true,
            CURLOPT_POSTFIELDS     => $payload,
            CURLOPT_HTTPHEADER     => [
                'Content-Type: application/json',
                'Authorization: Bearer ' . $api_key,
            ],
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT        => 30,
        ]);
        $raw    = curl_exec($ch);
        $status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($status !== 200 || !$raw) {
            log_message('error', $this->log_prefix . ' LLM HTTP ' . $status);
            return ['ok' => false, 'text' => null, 'error' => 'http_' . $status];
        }

        $decoded = json_decode($raw, true);
        $text = $decoded['choices'][0]['message']['content'] ?? null;
        if (!$text) {
            return ['ok' => false, 'text' => null, 'error' => 'empty_response'];
        }
        return ['ok' => true, 'text' => $text];
    }

    // =========================================================================
    // PRIVATE: build prompt
    // =========================================================================
    private function _build_prompt($question, $intent, $schema_doc, $role, $uid, $cluster_id, $region_id)
    {
        $scope_note = $this->_scope_note($role, $uid, $cluster_id, $region_id);

        return <<<PROMPT
You are Anaya, a data assistant for STEM Learning CRM. You answer plain-English
questions by writing safe read-only MySQL SELECT queries.

RULES:
1. Write only SELECT statements. Never INSERT, UPDATE, DELETE, DROP, ALTER,
   TRUNCATE, GRANT, REPLACE, MERGE, or CALL.
2. Only use tables listed in SCHEMA below. Never use system tables.
3. Always apply the scope filter: {$scope_note}
4. Keep results under 500 rows. Add LIMIT 500 if not already present.
5. Wrap your SQL inside a code block like: ```sql ... ```
6. After the SQL, add a one-sentence plain-English summary of what it returns.
7. If the question cannot be answered with the available data, say so clearly
   without generating SQL.
8. Intent detected: {$intent}

SCHEMA:
{$schema_doc}

QUESTION: {$question}
PROMPT;
    }

    // =========================================================================
    // PRIVATE: build schema doc (abbreviated, role-aware)
    // =========================================================================
    private function _build_schema_doc($role, $uid, $cluster_id, $region_id)
    {
        // Load allowlist from cache or DB
        $tables = $this->_load_allowlist();
        $lines  = [];
        foreach ($tables as $t) {
            if ($t['is_view']) {
                $lines[] = "VIEW {$t['table_name']}: {$t['notes']}";
            } else {
                $lines[] = "TABLE {$t['table_name']} columns: {$t['allowed_columns']} -- {$t['notes']}";
            }
        }
        $schema = implode("\n", $lines);

        $scope = $this->_scope_note($role, $uid, $cluster_id, $region_id);
        return $schema . "\n\nMANDATORY SCOPE: {$scope}";
    }

    private function _scope_note($role, $uid, $cluster_id, $region_id)
    {
        switch ($role) {
            case 'bd':
                return "ic.mainbd = {$uid} (BD sees only their own leads)";
            case 'cm':
                return "rh.parent_uid = {$uid} (CM sees only their cluster's BDs via reporting_hierarchy)";
            case 'rm':
                return "rh.skip_parent_uid = {$uid} (RM sees only their region via reporting_hierarchy)";
            case 'director':
                return "no scope restriction (director sees all)";
            default:
                return "ic.mainbd = {$uid}";
        }
    }

    // =========================================================================
    // PRIVATE: extract SQL from LLM response
    // =========================================================================
    private function _extract_sql($llm_text)
    {
        // Try fenced code block ```sql ... ``` first
        if (preg_match('/```(?:sql)?\s*(SELECT[\s\S]+?)```/i', $llm_text, $m)) {
            return trim($m[1]);
        }
        // Fallback: bare SELECT
        if (preg_match('/(SELECT\s+[\s\S]+?)(;|$)/i', $llm_text, $m)) {
            return trim($m[1]);
        }
        return null;
    }

    // =========================================================================
    // PRIVATE: execute safe SQL
    // =========================================================================
    private function _execute_safe_sql($sql)
    {
        try {
            // Set session read-only timeout
            $this->db->query(
                'SET SESSION MAX_EXECUTION_TIME = ' . self::SQL_TIMEOUT_MS
            );

            // Enforce row cap: append LIMIT if none present
            if (!preg_match('/\bLIMIT\b/i', $sql)) {
                $sql .= ' LIMIT ' . self::SQL_ROW_CAP;
            }

            $result = $this->db->query($sql);
            if ($result === false) {
                return ['ok' => false, 'rows' => [], 'error' => 'query_failed'];
            }
            $rows = $result->result_array();
            return ['ok' => true, 'rows' => $rows];
        } catch (Exception $e) {
            log_message('error', $this->log_prefix . ' SQL exec error: ' . $e->getMessage());
            return ['ok' => false, 'rows' => [], 'error' => $e->getMessage()];
        }
    }

    // =========================================================================
    // PRIVATE: format result
    // =========================================================================
    private function _format_result($rows, $question, $intent, $row_count)
    {
        if ($row_count === 0) {
            return [
                'text'  => 'No records found for that query. ' .
                    'Try adjusting the date range or filters.',
                'table' => null,
            ];
        }

        // Single-value aggregate: one row, one column
        if ($row_count === 1 && count($rows[0]) === 1) {
            $val  = reset($rows[0]);
            $key  = key($rows[0]);
            $text = 'Result: ' . $key . ' = ' . $val . '.';
            return ['text' => $text, 'table' => null];
        }

        // Multi-row result: return table + summary text
        $visible      = array_slice($rows, 0, self::UI_ROW_CAP);
        $hidden_count = $row_count - count($visible);
        $text = 'Found ' . $row_count . ' record' . ($row_count !== 1 ? 's' : '') . '.';
        if ($hidden_count > 0) {
            $text .= ' Showing first ' . count($visible) . '. Tap "see all" to load more.';
        }

        return [
            'text'        => $text,
            'table'       => [
                'headers'     => array_keys($rows[0]),
                'rows'        => $visible,
                'total_rows'  => $row_count,
                'has_more'    => $hidden_count > 0,
            ],
        ];
    }

    // =========================================================================
    // PRIVATE: follow-up suggestions
    // =========================================================================
    private function _suggestions($role, $intent)
    {
        $all = $this->get_suggestions($role);
        // Return 3 suggestions, excluding ones that match current intent bucket
        return array_slice($all, 0, 3);
    }

    // =========================================================================
    // PRIVATE: denial message (human-readable)
    // =========================================================================
    private function _denial_message($reason, $question)
    {
        $map = [
            'write_keyword'        => 'That question would require changing data, which is not allowed yet.',
            'non_allowlist_table'  => 'That question references data I do not have access to.',
            'missing_scope_filter' => 'The query did not include the required access filter. Please rephrase.',
            'multi_statement'      => 'Only single queries are supported. Please ask one question at a time.',
            'action_intent_phase1_denied' => 'Write actions are not available yet. I can only look up data.',
        ];
        $default = 'I could not answer that question safely. Please try rephrasing it.';
        return $map[$reason] ?? $default;
    }

    // =========================================================================
    // PRIVATE: quota check
    // =========================================================================
    private function _check_quota($uid, $role)
    {
        $limit = $this->_quota_limit($uid, $role);
        if ($limit === 0) {  // director unlimited
            return ['denied' => false, 'used' => 0, 'limit' => 0, 'message' => null];
        }

        $row = $this->db->query(
            "SELECT COUNT(*) AS cnt FROM ask_audit_log
              WHERE uid = ? AND DATE(executed_at) = CURDATE()",
            [$uid]
        )->row_array();

        $used = (int)($row['cnt'] ?? 0);
        if ($used >= $limit) {
            return [
                'denied'  => true,
                'used'    => $used,
                'limit'   => $limit,
                'message' => 'Daily query limit of ' . $limit . ' reached. Resets at midnight.',
            ];
        }
        return ['denied' => false, 'used' => $used, 'limit' => $limit, 'message' => null];
    }

    private function _quota_limit($uid, $role)
    {
        // Check pilot quota overrides
        $is_pilot = in_array((int)$uid, self::PILOT_UIDS);
        if ($is_pilot) {
            $bd_q = (int)(getenv('STEM_ASK_PILOT_QUOTA_BD') ?: self::PILOT_QUOTA_BD);
            $cm_q = (int)(getenv('STEM_ASK_PILOT_QUOTA_CM') ?: self::PILOT_QUOTA_CM);
            if ($role === 'bd') return $bd_q;
            if ($role === 'cm') return $cm_q;
        }
        return self::QUOTA[$role] ?? self::QUOTA['bd'];
    }

    // =========================================================================
    // PRIVATE: session helpers
    // =========================================================================
    private function _create_session($uid, $role, $cluster_id, $region_id)
    {
        $this->db->query(
            "INSERT INTO ask_session (uid, role, cluster_id, region_id)
             VALUES (?, ?, ?, ?)",
            [$uid, $role, $cluster_id, $region_id]
        );
        return (int)$this->db->insert_id();
    }

    private function _touch_session($session_id)
    {
        $this->db->query(
            "UPDATE ask_session SET last_active_at = NOW() WHERE id = ?",
            [$session_id]
        );
    }

    private function _increment_session_count($session_id)
    {
        $this->db->query(
            "UPDATE ask_session SET message_count = message_count + 1 WHERE id = ?",
            [$session_id]
        );
    }

    // =========================================================================
    // PRIVATE: message persistence
    // =========================================================================
    private function _save_message($session_id, $role, $text,
        $sql = null, $rows = null, $latency = null,
        $denied = false, $denied_reason = null)
    {
        $this->db->query(
            "INSERT INTO ask_message
               (session_id, role, text, sql_generated, rows_returned,
                latency_ms, denied, denied_reason)
             VALUES (?, ?, ?, ?, ?, ?, ?, ?)",
            [$session_id, $role, $text, $sql, $rows,
             $latency, (int)$denied, $denied_reason]
        );
        return (int)$this->db->insert_id();
    }

    // =========================================================================
    // PRIVATE: audit log
    // =========================================================================
    private function _audit($uid, $role, $session_id, $message_id, $query_text,
        $sql_executed, $denied_reason, $rows, $latency)
    {
        $this->db->query(
            "INSERT INTO ask_audit_log
               (uid, role, session_id, message_id, query_text,
                sql_executed, denied_reason, rows_returned, latency_ms)
             VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)",
            [$uid, $role, $session_id, $message_id, $query_text,
             $sql_executed, $denied_reason, $rows, $latency]
        );
    }

    // =========================================================================
    // PRIVATE: allowlist loader (cached)
    // =========================================================================
    private $_allowlist_cache     = null;
    private $_allowlist_cache_at  = 0;

    private function _load_allowlist()
    {
        $now = time();
        if ($this->_allowlist_cache &&
            ($now - $this->_allowlist_cache_at) < self::ALLOWLIST_TTL_SEC) {
            return $this->_allowlist_cache;
        }
        $rows = $this->db->query(
            "SELECT table_name, allowed_columns, is_view, notes
               FROM safe_query_allowlist WHERE active = 1"
        )->result_array();
        $this->_allowlist_cache    = $rows;
        $this->_allowlist_cache_at = $now;
        return $rows;
    }

    // =========================================================================
    // PRIVATE: helpers
    // =========================================================================
    private function _now_ms()    { return (int)(microtime(true) * 1000); }
    private function _elapsed($s) { return (int)(microtime(true) * 1000) - $s; }

    private function _clean_llm_text($text)
    {
        // Remove SQL code blocks from conversational responses
        $text = preg_replace('/```(?:sql)?\s*[\s\S]*?```/i', '', $text);
        return trim($text);
    }

    private function _ok_response($session_id, $text, $table, $row_count, $latency, $suggestions)
    {
        return [
            'ok'          => true,
            'session_id'  => $session_id,
            'text'        => $text,
            'table'       => $table,
            'row_count'   => $row_count,
            'latency_ms'  => $latency,
            'suggestions' => $suggestions,
        ];
    }
}

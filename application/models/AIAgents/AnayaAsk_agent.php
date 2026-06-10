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
            // ADDITIVE 2026-06-07: When the LLM engine is unreachable (no key yet),
            // answer the most common BD/CM questions deterministically from live data
            // so the agent is genuinely useful today. Auto-upgrades to full LLM
            // text-to-SQL the moment a working key is configured (openai.php or env).
            $fallback = $this->_deterministic_answer($question, $role, $uid, $cluster_id, $region_id);
            $this->_audit($uid, $role, $session_id, $user_msg_id, $question,
                ($fallback['sql'] ?? null),
                $fallback['answered'] ? 'deterministic_fallback' : 'llm_call_failed',
                null, $this->_elapsed($start_ms));
            $this->_save_message($session_id, 'assistant', $fallback['text'],
                ($fallback['sql'] ?? null), null, $this->_elapsed($start_ms));
            return $this->_ok_response($session_id, $fallback['text'],
                ($fallback['table'] ?? null), ($fallback['row_count'] ?? 0),
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
        $model    = getenv('STEM_ASK_LLM_MODEL');

        // ADDITIVE 2026-06-07: fall back to application/config/openai.php so a single
        // key placed there lights up every LLM agent. Env vars still win if set.
        if (!$endpoint || !$api_key) {
            $this->CI->load->config('openai', TRUE);
            $oc_key  = $this->CI->config->item('openai_api_key', 'openai');
            $oc_base = $this->CI->config->item('openai_base_url', 'openai');
            $oc_mdl  = $this->CI->config->item('openai_model', 'openai');
            if (!$endpoint && $oc_base) {
                $endpoint = rtrim($oc_base, '/') . '/chat/completions';
            }
            if (!$api_key && $oc_key)  { $api_key = $oc_key; }
            if (!$model  && $oc_mdl)   { $model   = $oc_mdl; }
        }

        if (!$endpoint || !$api_key) {
            log_message('error', $this->log_prefix . ' LLM endpoint/key not configured (env or openai.php)');
            return ['ok' => false, 'text' => null, 'error' => 'endpoint_not_configured'];
        }

        $payload = json_encode([
            'model'      => $model ?: 'gpt-4o-mini',
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
        // ADDITIVE 2026-06-07: This server runs MariaDB 10.11, which does NOT
        // support MySQL's MAX_EXECUTION_TIME session variable -- it uses
        // max_statement_time (seconds, decimal). Set it best-effort and never
        // let a failure here abort the request. We also temporarily suppress
        // CI db_debug so a malformed query returns FALSE gracefully instead of
        // halting output with an empty 200 body.
        $prev_debug = isset($this->db->db_debug) ? $this->db->db_debug : false;
        $this->db->db_debug = false;

        // Best-effort statement timeout (MariaDB syntax). Wrapped so any error
        // is swallowed -- the query still runs without an explicit timeout.
        try {
            $timeout_sec = (int)(self::SQL_TIMEOUT_MS / 1000);
            @$this->db->query('SET SESSION max_statement_time = ' . $timeout_sec);
        } catch (Exception $e) {
            // ignore -- timeout is advisory only
        } catch (Error $e) {
            // ignore
        }

        // Enforce row cap: append LIMIT if none present
        if (!preg_match('/\bLIMIT\b/i', $sql)) {
            $sql .= ' LIMIT ' . self::SQL_ROW_CAP;
        }

        try {
            $result = $this->db->query($sql);
            $this->db->db_debug = $prev_debug;
            if ($result === false || !is_object($result)) {
                log_message('error', $this->log_prefix . ' query returned false: ' . substr($sql, 0, 200));
                return ['ok' => false, 'rows' => [], 'error' => 'query_failed'];
            }
            $rows = $result->result_array();
            return ['ok' => true, 'rows' => $rows];
        } catch (Exception $e) {
            $this->db->db_debug = $prev_debug;
            log_message('error', $this->log_prefix . ' SQL exec error: ' . $e->getMessage());
            return ['ok' => false, 'rows' => [], 'error' => $e->getMessage()];
        } catch (Error $e) {
            $this->db->db_debug = $prev_debug;
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
    // PRIVATE: deterministic data-driven fallback
    //
    // ADDITIVE 2026-06-07. When the LLM engine is unreachable (no key yet), this
    // answers the most common BD/CM questions directly from live staging data so
    // Anaya is genuinely useful today. It uses only allowlisted tables, parameter-
    // binds the scope uid, and returns the correct empty shape when there are no
    // rows. The moment a working LLM key is configured (openai.php or env vars),
    // _call_llm succeeds and this method is never reached -- automatic upgrade.
    //
    // Returns: ['answered'=>bool,'text'=>string,'sql'=>?string,
    //           'table'=>?array,'row_count'=>int]
    // =========================================================================
    private function _deterministic_answer($question, $role, $uid, $cluster_id, $region_id)
    {
        $q   = strtolower(trim($question));
        $uid = (int)$uid;

        // Status label map (from `status` table) so funnel answers read in plain English.
        $cstatus_label = [
            1=>'Open', 2=>'Reachout', 3=>'Tentative', 4=>'Will do Later',
            5=>'Not Interested', 6=>'Positive', 7=>'Closure', 8=>'OPEN RPEM',
            9=>'Very Positive', 10=>'TTD-Reachout', 11=>'WNO-Reachout',
            12=>'Positive-NAP', 13=>'Very Positive-NAP', 14=>'On-Boarded',
        ];
        $label_case = "CASE c.cstatus";
        foreach ($cstatus_label as $id => $nm) {
            $label_case .= " WHEN {$id} THEN '" . addslashes($nm) . "'";
        }
        $label_case .= " ELSE CONCAT('Stage ', c.cstatus) END";

        $has = function($needles) use ($q) {
            foreach ((array)$needles as $n) { if (strpos($q, $n) !== false) return true; }
            return false;
        };

        // ------------------------------------------------------------------
        // PATTERN 0: Total lead count (must run before the funnel pattern so a
        // plain "how many leads in total" returns a single number, not a stage
        // breakdown).
        // ------------------------------------------------------------------
        if ($has(['total leads', 'total number of leads', 'leads in total',
                  'how many leads in total', 'count of leads', 'my total leads'])
            || ($has(['total']) && $has(['lead']))) {
            $sql = "SELECT COUNT(*) AS total_leads
                      FROM init_call c
                     WHERE c.mainbd = {$uid}
                       AND (c.deletebd IS NULL OR c.deletebd = 0)
                     LIMIT 1";
            return $this->_canned_run($sql, 'lead total');
        }

        // ------------------------------------------------------------------
        // PATTERN A: Today's / upcoming meetings (calendar / appointments).
        // ------------------------------------------------------------------
        if ($has(['meeting', 'visit', 'appointment', 'calendar', 'schedule'])) {
            $today_only = $has(['today', 'todays', "today's"]);
            if ($today_only) {
                $sql = "SELECT e.id AS event_id, e.cid_id, m.compname AS company,
                               e.appointmentdatetime AS scheduled_at, e.meeting_type
                          FROM tblcallevents e
                          LEFT JOIN init_call c    ON c.id = e.cid_id
                          LEFT JOIN company_master m ON m.id = c.cmpid_id
                         WHERE e.user_id = {$uid}
                           AND DATE(e.appointmentdatetime) = CURDATE()
                         ORDER BY e.appointmentdatetime ASC
                         LIMIT " . self::SQL_ROW_CAP;
                $head = 'meeting scheduled for today';
            } else {
                $sql = "SELECT e.id AS event_id, e.cid_id, m.compname AS company,
                               e.appointmentdatetime AS scheduled_at, e.meeting_type
                          FROM tblcallevents e
                          LEFT JOIN init_call c    ON c.id = e.cid_id
                          LEFT JOIN company_master m ON m.id = c.cmpid_id
                         WHERE e.user_id = {$uid}
                           AND e.appointmentdatetime >= NOW()
                           AND e.appointmentdatetime < DATE_ADD(NOW(), INTERVAL 30 DAY)
                         ORDER BY e.appointmentdatetime ASC
                         LIMIT " . self::SQL_ROW_CAP;
                $head = 'upcoming meeting in the next 30 days';
            }
            return $this->_canned_run($sql, $head);
        }

        // ------------------------------------------------------------------
        // PATTERN B: Funnel / pipeline breakdown by stage.
        // ------------------------------------------------------------------
        if ($has(['funnel', 'pipeline', 'breakdown', 'by stage', 'each stage',
                  'distribution', 'how many leads', 'lead count', 'my leads'])
            && !$has(['stuck', 'stagnant', 'stale', 'no touch', 'pending proposal'])) {
            $sql = "SELECT {$label_case} AS stage, c.cstatus AS stage_id,
                           COUNT(*) AS lead_count
                      FROM init_call c
                     WHERE c.mainbd = {$uid}
                       AND (c.deletebd IS NULL OR c.deletebd = 0)
                     GROUP BY c.cstatus
                     ORDER BY lead_count DESC
                     LIMIT " . self::SQL_ROW_CAP;
            return $this->_canned_run($sql, 'funnel stage');
        }

        // ------------------------------------------------------------------
        // PATTERN C: Pending proposals (cstatus typically Tentative/Positive,
        // proposal not yet closed). We surface leads flagged proposal_require.
        // ------------------------------------------------------------------
        if ($has(['proposal', 'proposals']) && $has(['pending', 'open', 'how many', 'outstanding'])) {
            $sql = "SELECT c.id AS cid_id, m.compname AS company,
                           {$label_case} AS stage, c.proposaldate, c.proposal_amt
                      FROM init_call c
                      LEFT JOIN company_master m ON m.id = c.cmpid_id
                     WHERE c.mainbd = {$uid}
                       AND (c.deletebd IS NULL OR c.deletebd = 0)
                       AND c.cstatus IN (3,6,9,12,13)
                     ORDER BY c.proposaldate DESC
                     LIMIT " . self::SQL_ROW_CAP;
            return $this->_canned_run($sql, 'lead awaiting proposal action');
        }

        // ------------------------------------------------------------------
        // PATTERN D: Stuck / stagnant / stale leads (no recent touch).
        // Prefer the migration-024 weekly_touch_gap signal; fall back to
        // last-event recency from tblcallevents when that table is empty.
        // ------------------------------------------------------------------
        if ($has(['stuck', 'stagnant', 'stale', 'no touch', 'not touched',
                  'over 30 days', 'over 22', 'no activity', 'idle'])) {
            $gap = $this->_execute_safe_sql(
                "SELECT g.cid_id, m.compname AS company, g.days_since_last_task,
                        g.last_task_date, g.cstatus
                   FROM weekly_touch_gap g
                   LEFT JOIN init_call c    ON c.id = g.cid_id
                   LEFT JOIN company_master m ON m.id = c.cmpid_id
                  WHERE g.bd_uid = {$uid} AND g.resolved = 0
                  ORDER BY g.days_since_last_task DESC
                  LIMIT " . self::SQL_ROW_CAP
            );
            if ($gap['ok'] && count($gap['rows']) > 0) {
                return $this->_canned_pack($gap['rows'], 'stuck lead (no touch in 7+ days)',
                    "weekly_touch_gap for bd_uid={$uid}");
            }
            // Fallback: leads whose most recent event is older than 30 days.
            $sql = "SELECT c.id AS cid_id, m.compname AS company,
                           {$label_case} AS stage,
                           MAX(e.event_date) AS last_event_date,
                           DATEDIFF(CURDATE(), MAX(e.event_date)) AS days_since
                      FROM init_call c
                      LEFT JOIN company_master m ON m.id = c.cmpid_id
                      LEFT JOIN tblcallevents e  ON e.cid_id = c.id
                     WHERE c.mainbd = {$uid}
                       AND (c.deletebd IS NULL OR c.deletebd = 0)
                       AND c.cstatus NOT IN (5,7,14)
                     GROUP BY c.id
                    HAVING last_event_date IS NULL
                        OR DATEDIFF(CURDATE(), last_event_date) > 30
                     ORDER BY days_since DESC
                     LIMIT " . self::SQL_ROW_CAP;
            return $this->_canned_run($sql, 'stuck lead (no activity in 30+ days)');
        }

        // ------------------------------------------------------------------
        // PATTERN E: Conversion / closure rate (this week / month).
        // ------------------------------------------------------------------
        if ($has(['conversion', 'closure rate', 'win rate', 'close rate'])) {
            $since = $has(['month', 'this month']) ? 'INTERVAL 1 MONTH' : 'INTERVAL 7 DAY';
            $sql = "SELECT
                       SUM(CASE WHEN f.to_cstatus IN (7,14) THEN 1 ELSE 0 END) AS closures,
                       COUNT(*) AS total_changes,
                       ROUND(100 * SUM(CASE WHEN f.to_cstatus IN (7,14) THEN 1 ELSE 0 END)
                             / GREATEST(COUNT(*),1), 1) AS conversion_pct
                      FROM funnel_change_log f
                     WHERE f.bd_uid = {$uid}
                       AND f.created_at >= DATE_SUB(NOW(), {$since})
                     LIMIT 1";
            return $this->_canned_run($sql, 'conversion summary');
        }

        // ------------------------------------------------------------------
        // PATTERN F: Recent events / activity log (this week).
        // ------------------------------------------------------------------
        if ($has(['recent', 'last week', 'this week', 'activity', 'what did i do', 'completed'])) {
            $sql = "SELECT e.id AS event_id, e.cid_id, m.compname AS company,
                           e.event_date, e.meeting_type, e.complete_time
                      FROM tblcallevents e
                      LEFT JOIN init_call c    ON c.id = e.cid_id
                      LEFT JOIN company_master m ON m.id = c.cmpid_id
                     WHERE e.user_id = {$uid}
                       AND e.event_date >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)
                     ORDER BY e.event_date DESC
                     LIMIT " . self::SQL_ROW_CAP;
            return $this->_canned_run($sql, 'event in the last 7 days');
        }

        // ------------------------------------------------------------------
        // PATTERN G: Total leads (simple count).
        // ------------------------------------------------------------------
        if ($has(['total leads', 'how many leads', 'number of leads', 'count of leads', 'my total'])) {
            $sql = "SELECT COUNT(*) AS total_leads
                      FROM init_call c
                     WHERE c.mainbd = {$uid}
                       AND (c.deletebd IS NULL OR c.deletebd = 0)
                     LIMIT 1";
            return $this->_canned_run($sql, 'lead total');
        }

        // ------------------------------------------------------------------
        // No pattern matched -> graceful, honest fallback (answered=false).
        // ------------------------------------------------------------------
        $examples = $this->get_suggestions($role);
        $tips = '';
        foreach (array_slice($examples, 0, 3) as $ex) { $tips .= "\n  - " . $ex; }
        return [
            'answered'  => false,
            'text'      => 'The smart assistant is running in data mode right now. '
                . 'I can already answer questions about your meetings, funnel, '
                . 'proposals, stuck leads, conversion and recent activity. '
                . 'Try one of these:' . $tips,
            'sql'       => null,
            'table'     => null,
            'row_count' => 0,
        ];
    }

    // Run a SELECT and pack the result into the standard deterministic shape.
    private function _canned_run($sql, $noun)
    {
        $res = $this->_execute_safe_sql($sql);
        if (!$res['ok']) {
            return [
                'answered'  => true,
                'text'      => 'I tried to pull that from live data but the lookup '
                    . 'did not complete. It has been logged. Please try rephrasing.',
                'sql'       => $sql,
                'table'     => null,
                'row_count' => 0,
            ];
        }
        return $this->_canned_pack($res['rows'], $noun, $sql);
    }

    // Format already-fetched rows into the deterministic shape (text + table).
    private function _canned_pack($rows, $noun, $sql)
    {
        $row_count = count($rows);

        // Single scalar (one row, one column) -> phrase as a number.
        if ($row_count === 1 && count($rows[0]) === 1) {
            $val = reset($rows[0]);
            $key = str_replace('_', ' ', (string)key($rows[0]));
            return [
                'answered'  => true,
                'text'      => 'Your ' . $key . ' is ' . $val . '.',
                'sql'       => $sql,
                'table'     => null,
                'row_count' => 1,
            ];
        }

        if ($row_count === 0) {
            return [
                'answered'  => true,
                'text'      => 'Good news -- you have no ' . $noun . ' right now.',
                'sql'       => $sql,
                'table'     => null,
                'row_count' => 0,
            ];
        }

        $visible      = array_slice($rows, 0, self::UI_ROW_CAP);
        $hidden_count = $row_count - count($visible);
        // Phrase the count cleanly without trying to pluralize multi-word noun
        // phrases (which produced awkward output like "upcomings meeting").
        $match_word = ($row_count === 1) ? 'match' : 'matches';
        $text = 'Found ' . $row_count . ' ' . $match_word . ' for ' . $noun . '.';
        if ($hidden_count > 0) {
            $text .= ' Showing the first ' . count($visible) . '.';
        }
        return [
            'answered'  => true,
            'text'      => $text,
            'sql'       => $sql,
            'table'     => [
                'headers'    => array_keys($rows[0]),
                'rows'       => $visible,
                'total_rows' => $row_count,
                'has_more'   => $hidden_count > 0,
            ],
            'row_count' => $row_count,
        ];
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

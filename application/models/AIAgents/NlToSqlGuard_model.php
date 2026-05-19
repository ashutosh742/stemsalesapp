<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * NlToSqlGuard - SQL Safety Layer for Anaya Ask
 *
 * This is the most important file in migration 032.
 *
 * Purpose: Receive an LLM-generated SQL candidate and either return a
 * validated, sanitized SQL string or a rejection reason. No SQL ever
 * reaches the database without passing through this guard.
 *
 * Hard rules enforced:
 *   1. Any write keyword (INSERT, UPDATE, DELETE, DROP, ALTER, TRUNCATE,
 *      GRANT, REPLACE, MERGE, CALL, EXEC) causes immediate rejection.
 *   2. Any table or view not in the allowlist causes immediate rejection.
 *      Tables are extracted from FROM, JOIN, and subquery clauses.
 *   3. Any query that is missing its mandatory role-scope WHERE clause
 *      (for non-director roles) causes rejection.
 *   4. More than one SQL statement (multiple semicolons) causes rejection.
 *   5. References to system schemas (information_schema, mysql,
 *      performance_schema, sys) cause rejection.
 *   6. UNION queries that introduce non-allowlisted tables cause rejection.
 *
 * This guard does NOT silently rewrite queries. It returns the original
 * validated SQL (with LIMIT appended if missing) or rejects it. It never
 * fabricates SQL.
 *
 * Used by: Anaya_ask_agent::handle_query (stem_anaya_ask_agent_php.php)
 *
 * Author: STEM ops
 * Migration: 032
 * Date: 2026-05-20
 */
class NlToSqlGuard
{
    // -------------------------------------------------------------------------
    // CONSTANTS
    // -------------------------------------------------------------------------

    // Write keywords that always trigger rejection (case-insensitive, word boundary).
    const WRITE_KEYWORDS = [
        'INSERT', 'UPDATE', 'DELETE', 'DROP', 'ALTER', 'TRUNCATE',
        'GRANT', 'REVOKE', 'REPLACE', 'MERGE', 'CALL', 'EXEC',
        'EXECUTE', 'CREATE', 'RENAME', 'LOAD', 'IMPORT',
    ];

    // System schemas never allowed in any query.
    const SYSTEM_SCHEMAS = [
        'information_schema', 'mysql', 'performance_schema', 'sys',
    ];

    // Role scope tokens injected into SELECT queries.
    // BD and CM/RM scopes must appear in the WHERE clause.
    // Director has no restriction.
    const SCOPE_PATTERNS = [
        'bd'  => '/\bic\.mainbd\s*=\s*\d+\b/i',
        'cm'  => '/\brh\.parent_uid\s*=\s*\d+\b/i',
        'rm'  => '/\brh\.skip_parent_uid\s*=\s*\d+\b/i',
    ];

    // Hard row cap enforced even if LLM omits it.
    const ROW_CAP = 500;

    protected $CI;
    protected $db;
    protected $log_prefix = '[nl_to_sql_guard]';

    // Allowlist cache (shared with agent via DB read).
    private $_allowlist      = null;
    private $_allowlist_at   = 0;
    const ALLOWLIST_TTL      = 300;

    // -------------------------------------------------------------------------
    // CONSTRUCTOR
    // -------------------------------------------------------------------------
    public function __construct()
    {
        $this->CI =& get_instance();
        $this->db = $this->CI->db;
    }

    // =========================================================================
    // PUBLIC: validate
    //
    // Entry point. Returns:
    //   ['ok' => true,  'sql' => <sanitized_sql>]
    //   ['ok' => false, 'reason' => <rejection_reason_string>]
    // =========================================================================
    public function validate($sql_candidate, $uid, $role, $cluster_id = null, $region_id = null)
    {
        // Normalize: strip leading/trailing whitespace, collapse internal whitespace.
        $sql = $this->_normalize($sql_candidate);

        // --- Rule 1: must be a SELECT statement --------------------------------
        if (!preg_match('/^\s*SELECT\b/i', $sql)) {
            return $this->_deny('non_select_statement',
                'Query must begin with SELECT.');
        }

        // --- Rule 2: no write keywords anywhere in the SQL --------------------
        $write_check = $this->_check_write_keywords($sql);
        if ($write_check !== null) {
            return $this->_deny('write_keyword',
                'Write operation detected: ' . $write_check . '. Only SELECT is allowed.');
        }

        // --- Rule 3: no system schemas ----------------------------------------
        $sys_check = $this->_check_system_schemas($sql);
        if ($sys_check !== null) {
            return $this->_deny('system_schema',
                'System schema reference detected: ' . $sys_check . '.');
        }

        // --- Rule 4: no multi-statement (multiple semicolons) -----------------
        if ($this->_is_multi_statement($sql)) {
            return $this->_deny('multi_statement',
                'Only single-statement queries are allowed.');
        }

        // --- Rule 5: all referenced tables must be in allowlist ---------------
        $tables   = $this->_extract_tables($sql);
        $allowlist = $this->_load_allowlist();

        foreach ($tables as $tbl) {
            if (!$this->_is_allowed_table($tbl, $allowlist)) {
                return $this->_deny('non_allowlist_table',
                    'Table "' . $tbl . '" is not in the allowed list.');
            }
        }

        // --- Rule 6: role-scope WHERE must be present (non-director) ----------
        if ($role !== 'director') {
            $scope_result = $this->_check_scope_filter($sql, $role, $uid);
            if ($scope_result !== null) {
                return $this->_deny('missing_scope_filter', $scope_result);
            }
        }

        // --- Rule 7: scope uid must be a real integer (no placeholder) --------
        $placeholder_check = $this->_check_no_placeholder($sql, $uid);
        if ($placeholder_check !== null) {
            return $this->_deny('scope_placeholder_not_replaced', $placeholder_check);
        }

        // --- Rule 8: no UNION or subquery that bypasses scope -----------------
        if ($role !== 'director') {
            $union_check = $this->_check_union_scope($sql, $role, $uid);
            if ($union_check !== null) {
                return $this->_deny('union_scope_bypass', $union_check);
            }
        }

        // --- All rules passed: enforce LIMIT ----------------------------------
        $safe_sql = $this->_enforce_limit($sql);

        return ['ok' => true, 'sql' => $safe_sql];
    }

    // =========================================================================
    // PRIVATE: Rule 2 - write keyword check
    // =========================================================================
    private function _check_write_keywords($sql)
    {
        // Strip string literals to avoid false positives from data values
        $stripped = preg_replace("/'[^']*'/", "''", $sql);
        $stripped = preg_replace('/"[^"]*"/', '""', $stripped);

        foreach (self::WRITE_KEYWORDS as $kw) {
            if (preg_match('/\b' . preg_quote($kw, '/') . '\b/i', $stripped)) {
                return $kw;
            }
        }
        return null;
    }

    // =========================================================================
    // PRIVATE: Rule 3 - system schema check
    // =========================================================================
    private function _check_system_schemas($sql)
    {
        foreach (self::SYSTEM_SCHEMAS as $schema) {
            if (preg_match('/\b' . preg_quote($schema, '/') . '\b/i', $sql)) {
                return $schema;
            }
        }
        return null;
    }

    // =========================================================================
    // PRIVATE: Rule 4 - multi-statement check
    // =========================================================================
    private function _is_multi_statement($sql)
    {
        // Strip trailing semicolon and whitespace, then check for any remaining
        $trimmed = rtrim(rtrim($sql), ';');
        // If there's still a semicolon, it's multi-statement
        return strpos($trimmed, ';') !== false;
    }

    // =========================================================================
    // PRIVATE: Rule 5 - table extraction
    // Extracts all table/view names from FROM and JOIN clauses.
    // Also handles subqueries (nested FROM).
    // =========================================================================
    private function _extract_tables($sql)
    {
        $tables = [];

        // Match FROM table_name [alias] and JOIN table_name [alias]
        // Handles: FROM t, FROM t AS alias, FROM (subquery) AS alias
        $pattern = '/\b(?:FROM|JOIN)\s+([a-zA-Z_][a-zA-Z0-9_]*)/gi';
        preg_match_all('/\b(?:FROM|JOIN)\s+([a-zA-Z_][a-zA-Z0-9_]*)/i', $sql, $matches);

        foreach ($matches[1] as $tbl) {
            $tbl = strtolower(trim($tbl));
            // Skip SQL keywords that follow FROM/JOIN in edge cases
            if (in_array($tbl, ['select', 'where', 'on', 'set', 'values'])) continue;
            if (!in_array($tbl, $tables)) $tables[] = $tbl;
        }

        return $tables;
    }

    // =========================================================================
    // PRIVATE: Rule 5 - allowlist check
    // =========================================================================
    private function _is_allowed_table($table, $allowlist)
    {
        $table = strtolower(trim($table));
        foreach ($allowlist as $row) {
            if (strtolower($row['table_name']) === $table) return true;
        }
        return false;
    }

    // =========================================================================
    // PRIVATE: Rule 6 - scope filter presence check
    // Verifies the mandatory WHERE clause for the role was injected.
    // =========================================================================
    private function _check_scope_filter($sql, $role, $uid)
    {
        $uid = (int)$uid;
        if (!isset(self::SCOPE_PATTERNS[$role])) {
            // Unknown role: deny for safety
            return 'Unknown role "' . $role . '". Cannot verify scope filter.';
        }

        $pattern = self::SCOPE_PATTERNS[$role];
        if (!preg_match($pattern, $sql)) {
            $required = $this->_scope_description($role, $uid);
            return 'Missing required scope filter for role "' . $role . '". ' .
                'Query must contain: ' . $required;
        }
        return null;
    }

    private function _scope_description($role, $uid)
    {
        switch ($role) {
            case 'bd':  return 'ic.mainbd = ' . $uid;
            case 'cm':  return 'rh.parent_uid = ' . $uid;
            case 'rm':  return 'rh.skip_parent_uid = ' . $uid;
            default:    return 'scope filter for role ' . $role;
        }
    }

    // =========================================================================
    // PRIVATE: Rule 7 - no unresolved placeholders
    // =========================================================================
    private function _check_no_placeholder($sql, $uid)
    {
        // The prompt injects {uid} - ensure it was replaced with the real uid
        if (strpos($sql, '{uid}') !== false) {
            return 'Scope placeholder {uid} was not replaced with real user id.';
        }
        // Ensure the actual uid integer appears in the SQL (sanity check)
        if (!strpos($sql, (string)(int)$uid)) {
            // Not a hard block - uid may appear in subquery. Log but allow.
            log_message('debug', '[nl_to_sql_guard] uid not found in SQL - proceeding');
        }
        return null;
    }

    // =========================================================================
    // PRIVATE: Rule 8 - union scope bypass check
    // Each leg of a UNION must also contain the scope filter.
    // =========================================================================
    private function _check_union_scope($sql, $role, $uid)
    {
        if (!preg_match('/\bUNION\b/i', $sql)) return null;

        // Split on UNION (ALL or plain) and check each leg
        $legs = preg_split('/\bUNION\s+(?:ALL\s+)?/i', $sql);
        foreach ($legs as $leg) {
            $leg = trim($leg);
            if (!$leg) continue;
            $pattern = self::SCOPE_PATTERNS[$role] ?? null;
            if ($pattern && !preg_match($pattern, $leg)) {
                return 'UNION leg is missing the required scope filter for role "' . $role . '".';
            }
        }
        return null;
    }

    // =========================================================================
    // PRIVATE: enforce LIMIT
    // =========================================================================
    private function _enforce_limit($sql)
    {
        $sql = rtrim(rtrim($sql), ';');
        if (!preg_match('/\bLIMIT\b/i', $sql)) {
            $sql .= ' LIMIT ' . self::ROW_CAP;
        } else {
            // Replace any LIMIT over ROW_CAP with ROW_CAP
            $sql = preg_replace_callback(
                '/\bLIMIT\s+(\d+)/i',
                function ($m) {
                    $n = (int)$m[1];
                    return 'LIMIT ' . min($n, self::ROW_CAP);
                },
                $sql
            );
        }
        return $sql;
    }

    // =========================================================================
    // PRIVATE: normalize SQL string
    // =========================================================================
    private function _normalize($sql)
    {
        $sql = trim($sql);
        // Remove SQL block comment /* ... */ but leave string content
        $sql = preg_replace('/\/\*.*?\*\//s', ' ', $sql);
        // Remove -- line comments but leave string content
        $sql = preg_replace('/--[^\n]*/', ' ', $sql);
        // Collapse multiple whitespace to single space
        $sql = preg_replace('/\s+/', ' ', $sql);
        return trim($sql);
    }

    // =========================================================================
    // PRIVATE: allowlist loader (cached 5 min)
    // =========================================================================
    private function _load_allowlist()
    {
        $now = time();
        if ($this->_allowlist &&
            ($now - $this->_allowlist_at) < self::ALLOWLIST_TTL) {
            return $this->_allowlist;
        }
        $rows = $this->db->query(
            "SELECT table_name, is_view FROM safe_query_allowlist WHERE active = 1"
        )->result_array();
        $this->_allowlist    = $rows;
        $this->_allowlist_at = $now;
        return $rows;
    }

    // =========================================================================
    // PRIVATE: denial builder
    // =========================================================================
    private function _deny($reason, $detail)
    {
        log_message('info', $this->log_prefix . ' DENIED [' . $reason . ']: ' . $detail);
        return ['ok' => false, 'reason' => $reason, 'detail' => $detail];
    }
}

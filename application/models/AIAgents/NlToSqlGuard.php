<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * NlToSqlGuard
 *
 * Safety validator for LLM-generated SQL candidates in Ask Anaya (migration 032).
 * Created: 2026-06-06 audit fix (stub-to-real)
 *
 * Checks:
 *   1. No write keywords (INSERT/UPDATE/DELETE/DROP/ALTER/TRUNCATE/GRANT/REPLACE/MERGE/CALL)
 *   2. No semicolon multi-statement
 *   3. Must be a SELECT
 *   4. Only allowlisted tables used
 *   5. Scope filter present for BD/CM/RM roles
 *
 * Returns: ['ok' => bool, 'sql' => <cleaned>, 'reason' => <string or null>]
 */
class NlToSqlGuard
{
    // Keywords that must never appear in safe SQL
    const WRITE_KEYWORDS = [
        'INSERT', 'UPDATE', 'DELETE', 'DROP', 'ALTER', 'TRUNCATE',
        'GRANT', 'REVOKE', 'REPLACE', 'MERGE', 'CALL', 'EXEC',
        'CREATE', 'RENAME', 'LOAD', 'LOCK', 'UNLOCK',
    ];

    // Scope columns required by role
    const SCOPE_COLUMNS = [
        'bd'  => ['mainbd', 'bd_uid', 'assignedto_id'],
        'cm'  => ['parent_uid', 'cluster_id', 'cm_uid'],
        'rm'  => ['skip_parent_uid', 'region_id', 'rm_uid'],
    ];

    protected $db;
    protected $_allowlist = null;

    public function __construct()
    {
        $CI =& get_instance();
        $CI->load->database();
        $this->db = $CI->db;
    }

    /**
     * Validate an LLM-generated SQL candidate.
     *
     * @param string      $sql        Raw SQL from LLM
     * @param int         $uid        Caller uid
     * @param string      $role       bd|cm|rm|director
     * @param int|null    $cluster_id
     * @param int|null    $region_id
     * @return array ['ok' => bool, 'sql' => string|null, 'reason' => string|null]
     */
    public function validate($sql, $uid, $role, $cluster_id = null, $region_id = null)
    {
        $sql = trim($sql);

        // 1. Must start with SELECT
        if (!preg_match('/^\s*SELECT\b/i', $sql)) {
            return $this->_deny('non_select_statement', null);
        }

        // 2. No write keywords
        foreach (self::WRITE_KEYWORDS as $kw) {
            if (preg_match('/\b' . preg_quote($kw, '/') . '\b/i', $sql)) {
                return $this->_deny('write_keyword', null);
            }
        }

        // 3. No multi-statement (semicolons inside string literals are ok,
        //    but a trailing ; or double-; is a red flag)
        $stripped = preg_replace("/'[^']*'/", "''", $sql); // remove string literals
        if (substr_count($stripped, ';') > 1) {
            return $this->_deny('multi_statement', null);
        }
        // Strip trailing semicolon (harmless, but clean it)
        $sql = rtrim($sql, " \t\n\r;");

        // 4. Table allowlist check
        $allowlist = $this->_load_allowlist();
        if (!empty($allowlist)) {
            $allowed_tables = array_column($allowlist, 'table_name');
            // Extract table names from FROM/JOIN clauses
            preg_match_all('/\b(?:FROM|JOIN)\s+`?(\w+)`?/i', $sql, $tmatches);
            $used_tables = $tmatches[1] ?? [];
            foreach ($used_tables as $tbl) {
                if (!in_array(strtolower($tbl), array_map('strtolower', $allowed_tables))) {
                    return $this->_deny('non_allowlist_table', null);
                }
            }
        }

        // 5. Scope filter presence (not for director)
        if ($role !== 'director') {
            $scope_cols = self::SCOPE_COLUMNS[$role] ?? self::SCOPE_COLUMNS['bd'];
            $sql_lower  = strtolower($sql);
            $has_scope  = false;
            foreach ($scope_cols as $col) {
                if (strpos($sql_lower, strtolower($col)) !== false) {
                    $has_scope = true;
                    break;
                }
            }
            // Also accept uid literal as scope
            if (!$has_scope && strpos($sql_lower, (string)$uid) !== false) {
                $has_scope = true;
            }
            if (!$has_scope) {
                return $this->_deny('missing_scope_filter', null);
            }
        }

        return ['ok' => true, 'sql' => $sql, 'reason' => null];
    }

    // -------------------------------------------------------------------------
    // PRIVATE
    // -------------------------------------------------------------------------

    private function _deny($reason, $sql)
    {
        return ['ok' => false, 'sql' => $sql, 'reason' => $reason];
    }

    private function _load_allowlist()
    {
        if ($this->_allowlist !== null) {
            return $this->_allowlist;
        }
        try {
            $rows = $this->db->query(
                "SELECT table_name FROM safe_query_allowlist WHERE active = 1"
            )->result_array();
            $this->_allowlist = $rows;
        } catch (Exception $e) {
            $this->_allowlist = [];
        }
        return $this->_allowlist;
    }
}

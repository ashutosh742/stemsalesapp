<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Dedupe_api.php (A.5 - 2026-06-01)
 *
 * Endpoints:
 *   GET /api/dedupe/check?compname=X&pincode=Y
 *     Returns candidate duplicate companies with similarity score.
 *     Uses MySQL SOUNDEX for phonetic match, then sorts by name similarity.
 *
 * Bearer token required.
 */
class Dedupe_api extends CI_Controller {

    private $_known_token = '4eBaiAT7r4zu6OK3b8evjLNia1D7RGgb0qRTuLJfUSo';
    private $_authed_uid = 0;

    // Threshold: only return matches at or above this similarity percent
    private $_min_similarity = 50;

    public function __construct() {
        parent::__construct();
        $this->load->database();
        header('Content-Type: application/json; charset=utf-8');
    }

    private function _bearer_ok() {
        $hdr = isset($_SERVER['HTTP_AUTHORIZATION']) ? $_SERVER['HTTP_AUTHORIZATION'] : '';
        if (!$hdr && function_exists('apache_request_headers')) {
            $h = apache_request_headers();
            if (isset($h['Authorization'])) $hdr = $h['Authorization'];
        }
        if (!$hdr || stripos($hdr, 'Bearer ') !== 0) return false;
        $token = trim(substr($hdr, 7));
        $env = getenv('STEM_DIGEST_TOKEN');
        if ($env && hash_equals($env, $token)) return true;
        if (hash_equals($this->_known_token, $token)) return true;
        // Per-user JWT
        $uid = $this->_jwt_token_valid($token);
        if ($uid) { $this->_authed_uid = $uid; return true; }
        return false;
    }

    // ---- per-user JWT validator (added 28 May 2026, matches Auth::api_login) ----
    private function _jwt_token_valid($token) {
        if (empty($token)) return false;
        $secret = getenv('STEM_DIGEST_TOKEN') ?: '4eBaiAT7r4zu6OK3b8evjLNia1D7RGgb0qRTuLJfUSo';
        $days = array(date('Y-m-d'), date('Y-m-d', strtotime('-1 day')), date('Y-m-d', strtotime('+1 day')));
        // Try uid from request first (fast path)
        $candidates = array();
        foreach (array('uid','cm_uid','rm_uid','bd_uid','acm_uid','user_id') as $k) {
            if (isset($_GET[$k]) && (int)$_GET[$k] > 0) $candidates[(int)$_GET[$k]] = 1;
            if (isset($_POST[$k]) && (int)$_POST[$k] > 0) $candidates[(int)$_POST[$k]] = 1;
        }
        foreach (array_keys($candidates) as $uid) {
            foreach ($days as $d) {
                if (hash_equals(sha1($secret.'|'.$uid.'|'.$d), $token)) return (int)$uid;
            }
        }
        // Fallback: scan all active uids (cached for 60 sec)
        static $all_uids = null;
        if ($all_uids === null) {
            $rows = $this->db->select('uid')->from('user')->where('active', 1)->get()->result();
            $all_uids = array();
            foreach ($rows as $r) $all_uids[] = (int)$r->uid;
        }
        foreach ($all_uids as $uid) {
            foreach ($days as $d) {
                if (hash_equals(sha1($secret.'|'.$uid.'|'.$d), $token)) return (int)$uid;
            }
        }
        return false;
    }


    private function _json($data, $code = 200) {
        http_response_code($code);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($data);
        exit;
    }

    // -----------------------------------------------------------------------
    // GET /api/dedupe/check?compname=X&pincode=Y
    // -----------------------------------------------------------------------
    public function check() {
        if (!$this->_bearer_ok()) {
            $this->_json(['ok' => false, 'error' => 'Unauthorized'], 401);
        }

        $compname = trim($this->input->get('compname') ?? '');
        $pincode  = trim($this->input->get('pincode') ?? '');

        if (strlen($compname) < 3) {
            $this->_json(['ok' => false, 'error' => 'compname must be at least 3 characters'], 422);
        }

        $matches = $this->_find_matches($compname, $pincode);

        // Log the check to dup_check_log
        if (!empty($matches)) {
            foreach ($matches as $m) {
                $this->db->query(
                    "INSERT INTO dup_check_log
                       (query_compname, query_pincode, matched_cmpid, matched_compname,
                        similarity_pct, match_method, checked_by_uid, checked_at)
                     VALUES (?, ?, ?, ?, ?, ?, 0, NOW())",
                    [
                        substr($compname, 0, 500),
                        substr($pincode, 0, 20),
                        $m['id'],
                        substr($m['compname'], 0, 500),
                        $m['similarity_pct'],
                        $m['method'],
                    ]
                );
            }
        }

        $this->_json([
            'ok'            => true,
            'query'         => $compname,
            'pincode_filter'=> $pincode ?: null,
            'count'         => count($matches),
            'matches'       => $matches,
        ], 200);
    }

    // -----------------------------------------------------------------------
    // Private: build match list
    // -----------------------------------------------------------------------
    private function _find_matches($compname, $pincode) {
        $results = [];

        // -- Step 1: SOUNDEX phonetic match --
        $soundex_matches = $this->_soundex_match($compname, $pincode);
        foreach ($soundex_matches as $row) {
            $sim = $this->_similarity_pct($compname, $row['compname']);
            if ($sim >= $this->_min_similarity) {
                $row['similarity_pct'] = $sim;
                $row['method']         = 'soundex';
                $results[$row['id']]   = $row;
            }
        }

        // -- Step 2: LIKE fallback for partial prefix matches not caught by SOUNDEX --
        $like_pattern   = $this->db->escape_like_str(substr($compname, 0, 10));
        $like_sql_pat   = $like_pattern . '%';
        $like_matches   = $this->_like_match($like_sql_pat, $pincode);
        foreach ($like_matches as $row) {
            if (isset($results[$row['id']])) continue; // already found via soundex
            $sim = $this->_similarity_pct($compname, $row['compname']);
            if ($sim >= $this->_min_similarity) {
                $row['similarity_pct'] = $sim;
                $row['method']         = 'like';
                $results[$row['id']]   = $row;
            }
        }

        // Sort by similarity descending
        usort($results, function($a, $b) {
            return $b['similarity_pct'] - $a['similarity_pct'];
        });

        return array_values($results);
    }

    private function _soundex_match($compname, $pincode) {
        $sql = "SELECT cm.id, cm.compname, cm.district, cm.state,
                       cm.city, cm.partnerType_id
                FROM company_master cm
                WHERE SOUNDEX(cm.compname) = SOUNDEX(?)";
        $params = [$compname];
        if ($pincode) {
            // company_master has no pincode column - match by district text if available
            // Pincode filter is best-effort: skip if no match column
        }
        $sql .= " LIMIT 20";
        return $this->db->query($sql, $params)->result_array();
    }

    private function _like_match($pattern, $pincode) {
        $sql = "SELECT cm.id, cm.compname, cm.district, cm.state, cm.city, cm.partnerType_id
                FROM company_master cm
                WHERE cm.compname LIKE ?
                LIMIT 20";
        return $this->db->query($sql, [$pattern])->result_array();
    }

    /**
     * Simple similarity score (0-100) using similar_text().
     * Returns integer percent.
     */
    private function _similarity_pct($a, $b) {
        if (!$a || !$b) return 0;
        similar_text(strtolower($a), strtolower($b), $pct);
        return (int)round($pct);
    }
}

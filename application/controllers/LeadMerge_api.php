<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * LeadMerge_api.php  (Phase 1 - Agent A - 2026-06-08)
 *
 * Duplicate company/lead merge endpoints.
 * REUSES Dedupe_api detection logic (soundex + like fuzzy match on compname).
 * Does NOT rewrite Dedupe_api.
 *
 * Endpoints:
 *   GET  /api/merge/candidates?lead_id=N          Returns likely duplicate clusters
 *   POST /api/merge/apply  {survivor_id, loser_ids[], by_uid, dry_run?}
 *                                                 Merges companies; supports dry_run=1
 *
 * Rules:
 *   - Never hard-delete in v1; marks loser company_master rows merged_into=survivor_id
 *   - Idempotent: re-running for already-merged loser is a no-op
 *   - Transaction-wrapped
 *   - dry_run=1 returns what WOULD change without writing
 *
 * Bearer token required; 401 on missing/invalid.
 * ASCII output only.
 */
class LeadMerge_api extends CI_Controller {

    private $_known_token = '4eBaiAT7r4zu6OK3b8evjLNia1D7RGgb0qRTuLJfUSo';
    private $_authed_uid  = 0;
    private $_min_sim     = 50; // same threshold as Dedupe_api

    public function __construct() {
        parent::__construct();
        $this->load->database();
        header('Content-Type: application/json; charset=utf-8');
    }

    // ------------------------------------------------------------------
    // Auth helpers  (mirrors Dedupe_api pattern)
    // ------------------------------------------------------------------
    private function _bearer_ok() {
        $hdr = isset($_SERVER['HTTP_AUTHORIZATION']) ? $_SERVER['HTTP_AUTHORIZATION'] : '';
        if (!$hdr && function_exists('apache_request_headers')) {
            $h = apache_request_headers();
            if (isset($h['Authorization'])) $hdr = $h['Authorization'];
        }
        if (!$hdr || stripos($hdr, 'Bearer ') !== 0) return false;
        $token = trim(substr($hdr, 7));
        $env   = getenv('STEM_DIGEST_TOKEN');
        if ($env && hash_equals($env, $token)) return true;
        if (hash_equals($this->_known_token, $token)) return true;
        $uid = $this->_jwt_valid($token);
        if ($uid) { $this->_authed_uid = $uid; return true; }
        return false;
    }

    private function _jwt_valid($token) {
        if (empty($token)) return false;
        $secret = getenv('STEM_DIGEST_TOKEN') ?: $this->_known_token;
        $days   = [date('Y-m-d'), date('Y-m-d', strtotime('-1 day')), date('Y-m-d', strtotime('+1 day'))];
        $cands  = [];
        foreach (['uid','cm_uid','rm_uid','bd_uid','by_uid','user_id'] as $k) {
            if (!empty($_GET[$k]))  $cands[(int)$_GET[$k]]  = 1;
            if (!empty($_POST[$k])) $cands[(int)$_POST[$k]] = 1;
        }
        foreach (array_keys($cands) as $uid) {
            foreach ($days as $d) {
                if (hash_equals(sha1($secret.'|'.$uid.'|'.$d), $token)) return (int)$uid;
            }
        }
        static $all = null;
        if ($all === null) {
            $rows = $this->db->select('uid')->from('user')->where('active', 1)->get()->result();
            $all  = [];
            foreach ($rows as $r) $all[] = (int)$r->uid;
        }
        foreach ($all as $uid) {
            foreach ($days as $d) {
                if (hash_equals(sha1($secret.'|'.$uid.'|'.$d), $token)) return (int)$uid;
            }
        }
        return false;
    }

    private function _json($data, $code = 200) {
        http_response_code($code);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($data, JSON_UNESCAPED_UNICODE);
        exit;
    }

    private function _post_body() {
        $raw = file_get_contents('php://input');
        if ($raw) {
            $d = json_decode($raw, true);
            if (is_array($d)) return $d;
        }
        return $_POST;
    }

    // ------------------------------------------------------------------
    // Dedupe helpers: REUSE Dedupe_api logic (soundex + like + similarity)
    // ------------------------------------------------------------------
    private function _similarity_pct($a, $b) {
        if (!$a || !$b) return 0;
        similar_text(strtolower(trim($a)), strtolower(trim($b)), $pct);
        return (int)round($pct);
    }

    /**
     * Find duplicate company candidates for a given company.
     * Uses same soundex+like approach as Dedupe_api::_find_matches().
     */
    private function _find_dup_companies($compname, $district) {
        if (strlen(trim($compname)) < 3) return [];
        $results = [];

        // Soundex
        $sql = "SELECT cm.id, cm.compname, cm.district, cm.state,
                       cm.merged_into, cm.merged_at
                FROM company_master cm
                WHERE SOUNDEX(cm.compname) = SOUNDEX(?)
                LIMIT 30";
        $rows = $this->db->query($sql, [$compname])->result_array();
        foreach ($rows as $row) {
            $sim = $this->_similarity_pct($compname, $row['compname']);
            if ($sim >= $this->_min_sim) {
                $row['similarity_pct'] = $sim;
                $row['method']         = 'soundex';
                $results[$row['id']]   = $row;
            }
        }

        // Like prefix fallback
        $like_pat  = $this->db->escape_like_str(substr($compname, 0, 10)) . '%';
        $sql2 = "SELECT cm.id, cm.compname, cm.district, cm.state,
                        cm.merged_into, cm.merged_at
                 FROM company_master cm
                 WHERE cm.compname LIKE ?
                 LIMIT 30";
        $rows2 = $this->db->query($sql2, [$like_pat])->result_array();
        foreach ($rows2 as $row) {
            if (isset($results[$row['id']])) continue;
            $sim = $this->_similarity_pct($compname, $row['compname']);
            if ($sim >= $this->_min_sim) {
                $row['similarity_pct'] = $sim;
                $row['method']         = 'like';
                $results[$row['id']]   = $row;
            }
        }

        // District match boost: if district available, pull district-only matches
        if (!empty($district)) {
            $sql3 = "SELECT cm.id, cm.compname, cm.district, cm.state,
                            cm.merged_into, cm.merged_at
                     FROM company_master cm
                     WHERE cm.district = ?
                     AND cm.compname LIKE ?
                     LIMIT 20";
            $rows3 = $this->db->query($sql3, [$district, $like_pat])->result_array();
            foreach ($rows3 as $row) {
                if (isset($results[$row['id']])) continue;
                $sim = $this->_similarity_pct($compname, $row['compname']);
                if ($sim >= 40) { // slightly lower threshold when district also matches
                    $row['similarity_pct'] = $sim;
                    $row['method']         = 'district+like';
                    $results[$row['id']]   = $row;
                }
            }
        }

        usort($results, function($a, $b) { return $b['similarity_pct'] - $a['similarity_pct']; });
        return array_values($results);
    }

    // ------------------------------------------------------------------
    // GET /api/merge/candidates?lead_id=N
    // ------------------------------------------------------------------
    public function candidates() {
        if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
            $this->_json(['ok' => false, 'error' => 'GET required'], 405);
        }
        if (!$this->_bearer_ok()) {
            $this->_json(['ok' => false, 'error' => 'Unauthorized'], 401);
        }

        $lead_id = (int)$this->input->get('lead_id');
        if ($lead_id <= 0) {
            $this->_json(['ok' => false, 'error' => 'lead_id required'], 422);
        }

        // Fetch lead and its company
        $lead = $this->db->query(
            "SELECT ic.id as lead_id, ic.cmpid_id, ic.cstatus, ic.cluster_id,
                    cm.compname, cm.district, cm.state, cm.merged_into
             FROM init_call ic
             JOIN company_master cm ON cm.id = ic.cmpid_id
             WHERE ic.id = ? LIMIT 1",
            [$lead_id]
        )->row_array();

        if (!$lead) {
            $this->_json(['ok' => false, 'error' => 'lead not found'], 404);
        }

        $compname = trim($lead['compname'] ?? '');
        $district = trim($lead['district'] ?? '');

        $candidates = $this->_find_dup_companies($compname, $district);

        // Remove self from candidates
        $self_id = (int)$lead['cmpid_id'];
        $candidates = array_values(array_filter($candidates, function($c) use ($self_id) {
            return (int)$c['id'] !== $self_id;
        }));

        // Enrich: add lead counts per candidate company
        foreach ($candidates as &$c) {
            $cnt = $this->db->query(
                "SELECT COUNT(*) as cnt FROM init_call WHERE cmpid_id = ?",
                [(int)$c['id']]
            )->row_array();
            $c['lead_count']   = (int)($cnt['cnt'] ?? 0);
            $c['merged_into']  = $c['merged_into'] ? (int)$c['merged_into'] : null;
            $c['id']           = (int)$c['id'];
            $c['similarity_pct'] = (int)$c['similarity_pct'];
        }
        unset($c);

        if (empty($candidates)) {
            $this->_json([
                'ok'        => true,
                'empty'     => true,
                'lead_id'   => $lead_id,
                'cmpid_id'  => $self_id,
                'compname'  => $compname,
                'candidates'=> [],
            ]);
        }

        $this->_json([
            'ok'        => true,
            'lead_id'   => $lead_id,
            'cmpid_id'  => $self_id,
            'compname'  => $compname,
            'district'  => $district,
            'count'     => count($candidates),
            'candidates'=> $candidates,
        ]);
    }

    // ------------------------------------------------------------------
    // POST /api/merge/apply  {survivor_id, loser_ids[], by_uid, dry_run?}
    // ------------------------------------------------------------------
    public function apply() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->_json(['ok' => false, 'error' => 'POST required'], 405);
        }
        if (!$this->_bearer_ok()) {
            $this->_json(['ok' => false, 'error' => 'Unauthorized'], 401);
        }

        $body        = $this->_post_body();
        $survivor_id = (int)($body['survivor_id'] ?? 0);
        $loser_ids   = $body['loser_ids'] ?? [];
        $by_uid      = (int)($body['by_uid']      ?? 0);
        $dry_run     = (int)($body['dry_run']      ?? 0);

        if ($survivor_id <= 0 || empty($loser_ids) || $by_uid <= 0) {
            $this->_json(['ok' => false, 'error' => 'survivor_id, loser_ids[], and by_uid are required'], 422);
        }

        // Normalize loser_ids to int array
        if (!is_array($loser_ids)) {
            $loser_ids = [(int)$loser_ids];
        }
        $loser_ids = array_values(array_unique(array_map('intval', array_filter($loser_ids, function($v){ return (int)$v > 0; }))));

        if (empty($loser_ids)) {
            $this->_json(['ok' => false, 'error' => 'loser_ids must contain at least one valid positive integer'], 422);
        }

        // Remove survivor from loser list (safety)
        $loser_ids = array_values(array_filter($loser_ids, function($id) use ($survivor_id) {
            return $id !== $survivor_id;
        }));

        if (empty($loser_ids)) {
            $this->_json(['ok' => false, 'error' => 'loser_ids must not include the survivor_id'], 422);
        }

        // Verify survivor exists and is not itself merged
        $survivor = $this->db->query(
            "SELECT id, compname, merged_into FROM company_master WHERE id = ? LIMIT 1",
            [$survivor_id]
        )->row_array();
        if (!$survivor) {
            $this->_json(['ok' => false, 'error' => 'survivor company not found'], 404);
        }
        if (!is_null($survivor['merged_into'])) {
            $this->_json(['ok' => false, 'error' => 'survivor company is itself already merged; pick the final survivor'], 409);
        }

        // Build DRY-RUN preview
        $preview = [];
        $total_leads = 0;
        foreach ($loser_ids as $lid) {
            $loser = $this->db->query(
                "SELECT id, compname, merged_into FROM company_master WHERE id = ? LIMIT 1",
                [$lid]
            )->row_array();
            if (!$loser) {
                $preview[] = ['loser_id' => $lid, 'status' => 'not_found', 'leads_to_repoint' => 0];
                continue;
            }
            if (!is_null($loser['merged_into'])) {
                $preview[] = ['loser_id' => $lid, 'loser_compname' => $loser['compname'], 'status' => 'already_merged', 'leads_to_repoint' => 0];
                continue;
            }
            $cnt = $this->db->query(
                "SELECT COUNT(*) as cnt FROM init_call WHERE cmpid_id = ?", [$lid]
            )->row_array();
            $n = (int)($cnt['cnt'] ?? 0);
            $total_leads += $n;
            $preview[] = [
                'loser_id'          => $lid,
                'loser_compname'    => $loser['compname'],
                'status'            => 'will_merge',
                'leads_to_repoint'  => $n,
            ];
        }

        if ($dry_run) {
            $this->_json([
                'ok'                  => true,
                'dry_run'             => true,
                'survivor_id'         => $survivor_id,
                'survivor_compname'   => $survivor['compname'],
                'loser_ids'           => $loser_ids,
                'total_leads_repoint' => $total_leads,
                'preview'             => $preview,
            ]);
        }

        // --- REAL APPLY (transaction) ---
        $this->db->trans_start();

        $rows_repointed = 0;
        $skipped        = [];

        foreach ($loser_ids as $lid) {
            // Idempotency: skip if already merged
            $loser = $this->db->query(
                "SELECT id, compname, merged_into FROM company_master WHERE id = ? LIMIT 1",
                [$lid]
            )->row_array();
            if (!$loser) { $skipped[] = ['loser_id' => $lid, 'reason' => 'not_found']; continue; }
            if (!is_null($loser['merged_into'])) {
                $skipped[] = ['loser_id' => $lid, 'reason' => 'already_merged'];
                continue;
            }

            // Re-point init_call.cmpid_id
            $this->db->query(
                "UPDATE init_call SET cmpid_id = ? WHERE cmpid_id = ?",
                [$survivor_id, $lid]
            );
            $rows_repointed += $this->db->affected_rows();

            // Re-point cluster_company_index.company_id
            $this->db->query(
                "UPDATE cluster_company_index SET company_id = ? WHERE company_id = ?",
                [$survivor_id, $lid]
            );

            // Mark loser as merged (soft-delete v1)
            $this->db->query(
                "UPDATE company_master SET merged_into = ?, merged_at = NOW() WHERE id = ?",
                [$survivor_id, $lid]
            );
        }

        // Log the merge
        $actual_losers = array_values(array_filter($loser_ids, function($lid) use ($skipped) {
            foreach ($skipped as $s) {
                if ($s['loser_id'] === $lid) return false;
            }
            return true;
        }));

        if (!empty($actual_losers)) {
            $this->db->query(
                "INSERT INTO lead_merge_log (survivor_id, loser_ids, by_uid, ts, rows_repointed)
                 VALUES (?, ?, ?, NOW(), ?)",
                [$survivor_id, json_encode($actual_losers), $by_uid, $rows_repointed]
            );
            $merge_log_id = $this->db->insert_id();
        } else {
            $merge_log_id = null;
        }

        $this->db->trans_complete();

        if ($this->db->trans_status() === false) {
            $this->_json(['ok' => false, 'error' => 'Transaction failed; no changes written'], 500);
        }

        $this->_json([
            'ok'                  => true,
            'dry_run'             => false,
            'merge_log_id'        => $merge_log_id ? (int)$merge_log_id : null,
            'survivor_id'         => $survivor_id,
            'survivor_compname'   => $survivor['compname'],
            'loser_ids_processed' => $actual_losers,
            'skipped'             => $skipped,
            'rows_repointed'      => $rows_repointed,
        ]);
    }
}

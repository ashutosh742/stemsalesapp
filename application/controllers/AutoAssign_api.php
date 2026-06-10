<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * AutoAssign_api.php  (Phase 1 - Agent A - 2026-06-08)
 *
 * Auto-assignment suggestion and application endpoints.
 * Matches cluster and open-load to recommend the best BD.
 *
 * Endpoints:
 *   GET  /api/assign/suggest?lead_id=N    Suggest best BD for a lead
 *   GET  /api/assign/suggest?cluster_id=N Suggest best BD for a cluster
 *   POST /api/assign/apply  {lead_id, bd_uid, by_uid}  Apply assignment
 *
 * Recommendation logic:
 *   1. If lead has cluster_id: find BDs who own leads in that cluster
 *   2. Among those (or all active BDs if none), pick the one with lowest
 *      open-lead count (cstatus IN (1,2,3,6,8,9,10,12,13)).
 *   3. Return top-3 candidates with open-load counts for explainability.
 *
 * Bearer token required; 401 on missing/invalid.
 * ASCII output only.
 */
class AutoAssign_api extends CI_Controller {

    private $_known_token  = '4eBaiAT7r4zu6OK3b8evjLNia1D7RGgb0qRTuLJfUSo';
    private $_authed_uid   = 0;
    private $_active_statuses = [1,2,3,6,8,9,10,12,13];

    public function __construct() {
        parent::__construct();
        $this->load->database();
        header('Content-Type: application/json; charset=utf-8');
    }

    // ------------------------------------------------------------------
    // Auth helpers
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
    // Internal: get open-load for a set of BD uids
    // Returns [uid => open_count, ...]
    // ------------------------------------------------------------------
    private function _open_loads(array $uids) {
        if (empty($uids)) return [];
        $placeholders = implode(',', array_fill(0, count($uids), '?'));
        $status_in    = implode(',', $this->_active_statuses);
        $sql = "SELECT mainbd, COUNT(*) as cnt
                FROM init_call
                WHERE mainbd IN ($placeholders)
                AND cstatus IN ($status_in)
                GROUP BY mainbd";
        $rows = $this->db->query($sql, $uids)->result_array();
        $loads = array_fill_keys($uids, 0);
        foreach ($rows as $r) {
            $loads[(int)$r['mainbd']] = (int)$r['cnt'];
        }
        return $loads;
    }

    // ------------------------------------------------------------------
    // Internal: get BDs active in a cluster
    // Returns array of uid
    // ------------------------------------------------------------------
    private function _cluster_bds($cluster_id) {
        $status_in = implode(',', $this->_active_statuses);
        $sql = "SELECT DISTINCT ic.mainbd
                FROM init_call ic
                WHERE ic.cluster_id = ?
                AND ic.mainbd IS NOT NULL
                AND ic.mainbd > 0
                AND ic.cstatus IN ($status_in)
                LIMIT 50";
        $rows = $this->db->query($sql, [(int)$cluster_id])->result_array();
        return array_map(function($r){ return (int)$r['mainbd']; }, $rows);
    }

    // ------------------------------------------------------------------
    // Internal: get all active BD uids (fallback when no cluster match)
    // user_type id=3 = "Sales Person" = BD
    // ------------------------------------------------------------------
    private function _all_active_bds() {
        // Primary: Sales Person (type_id=3) active users
        $rows = $this->db->query(
            "SELECT uid FROM user WHERE active = 1 AND type_id = 3 LIMIT 100"
        )->result_array();
        if (empty($rows)) {
            // Fallback: any user who has active init_call records
            $status_in = implode(',', $this->_active_statuses);
            $rows = $this->db->query(
                "SELECT DISTINCT mainbd as uid FROM init_call
                 WHERE mainbd > 0 AND cstatus IN ($status_in)
                 LIMIT 100"
            )->result_array();
        }
        return array_map(function($r){ return (int)$r['uid']; }, $rows);
    }

    // ------------------------------------------------------------------
    // Internal: build ranked suggestions from uid list
    // ------------------------------------------------------------------
    private function _rank_bds(array $uids) {
        if (empty($uids)) return [];
        $loads = $this->_open_loads($uids);
        asort($loads); // sort by open count asc

        $out = [];
        $rank = 1;
        foreach ($loads as $uid => $cnt) {
            $u = $this->db->query(
                "SELECT uid, name, username FROM user WHERE uid = ? LIMIT 1", [$uid]
            )->row_array();
            $out[] = [
                'rank'       => $rank++,
                'bd_uid'     => $uid,
                'name'       => $u['name']     ?? 'Unknown',
                'username'   => $u['username'] ?? '',
                'open_load'  => $cnt,
            ];
            if ($rank > 3) break; // top 3 only
        }
        return $out;
    }

    // ------------------------------------------------------------------
    // GET /api/assign/suggest?lead_id=N  OR  ?cluster_id=N
    // ------------------------------------------------------------------
    public function suggest() {
        if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
            $this->_json(['ok' => false, 'error' => 'GET required'], 405);
        }
        if (!$this->_bearer_ok()) {
            $this->_json(['ok' => false, 'error' => 'Unauthorized'], 401);
        }

        $lead_id    = (int)$this->input->get('lead_id');
        $cluster_id = (int)$this->input->get('cluster_id');

        if ($lead_id <= 0 && $cluster_id <= 0) {
            $this->_json(['ok' => false, 'error' => 'lead_id or cluster_id required'], 422);
        }

        $resolved_cluster = $cluster_id;
        $lead_info        = null;

        if ($lead_id > 0) {
            $lead_info = $this->db->query(
                "SELECT id, cmpid_id, mainbd, cstatus, cluster_id FROM init_call WHERE id = ? LIMIT 1",
                [$lead_id]
            )->row_array();
            if (!$lead_info) {
                $this->_json(['ok' => false, 'error' => 'lead not found'], 404);
            }
            // cluster_id in init_call is varchar; cast to int
            $ic_cluster = (int)$lead_info['cluster_id'];
            if ($ic_cluster > 0) $resolved_cluster = $ic_cluster;
        }

        // Find candidate BDs
        $uids = [];
        $match_source = 'global';

        if ($resolved_cluster > 0) {
            $cluster_uids = $this->_cluster_bds($resolved_cluster);
            if (!empty($cluster_uids)) {
                $uids         = $cluster_uids;
                $match_source = 'cluster';
            }
        }

        if (empty($uids)) {
            $uids         = $this->_all_active_bds();
            $match_source = 'global';
        }

        if (empty($uids)) {
            $base = [
                'ok'           => true,
                'empty'        => true,
                'suggestions'  => [],
                'match_source' => $match_source,
            ];
            if ($lead_id > 0)    $base['lead_id']    = $lead_id;
            if ($cluster_id > 0) $base['cluster_id'] = $resolved_cluster;
            $this->_json($base);
        }

        $suggestions = $this->_rank_bds($uids);

        $resp = [
            'ok'           => true,
            'match_source' => $match_source,
            'cluster_id'   => $resolved_cluster ?: null,
            'suggestions'  => $suggestions,
        ];
        if ($lead_id > 0) {
            $resp['lead_id']       = $lead_id;
            $resp['current_bd']    = $lead_info ? (int)$lead_info['mainbd'] : null;
            $resp['current_stage'] = $lead_info ? (int)$lead_info['cstatus'] : null;
        }

        $this->_json($resp);
    }

    // ------------------------------------------------------------------
    // POST /api/assign/apply  {lead_id, bd_uid, by_uid}
    // ------------------------------------------------------------------
    public function apply() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->_json(['ok' => false, 'error' => 'POST required'], 405);
        }
        if (!$this->_bearer_ok()) {
            $this->_json(['ok' => false, 'error' => 'Unauthorized'], 401);
        }

        $body    = $this->_post_body();
        $lead_id = (int)($body['lead_id'] ?? 0);
        $bd_uid  = (int)($body['bd_uid']  ?? 0);
        $by_uid  = (int)($body['by_uid']  ?? 0);

        if ($lead_id <= 0 || $bd_uid <= 0 || $by_uid <= 0) {
            $this->_json(['ok' => false, 'error' => 'lead_id, bd_uid, and by_uid are required'], 422);
        }

        // Verify lead exists
        $lead = $this->db->query(
            "SELECT id, cmpid_id, mainbd FROM init_call WHERE id = ? LIMIT 1",
            [$lead_id]
        )->row_array();
        if (!$lead) {
            $this->_json(['ok' => false, 'error' => 'lead not found'], 404);
        }

        // Verify bd_uid exists and is active
        $bd = $this->db->query(
            "SELECT uid, name FROM user WHERE uid = ? AND active = 1 LIMIT 1",
            [$bd_uid]
        )->row_array();
        if (!$bd) {
            $this->_json(['ok' => false, 'error' => 'bd_uid not found or not active'], 404);
        }

        $prev_bd = (int)$lead['mainbd'];

        // Update init_call.mainbd
        $this->db->query(
            "UPDATE init_call SET mainbd = ? WHERE id = ?",
            [$bd_uid, $lead_id]
        );

        // Log to lead_assign_log
        $this->db->query(
            "INSERT INTO lead_assign_log (lead_id, bd_uid, by_uid, ts) VALUES (?, ?, ?, NOW())",
            [$lead_id, $bd_uid, $by_uid]
        );
        $log_id = $this->db->insert_id();

        // Return new open-load for the assigned BD
        $loads = $this->_open_loads([$bd_uid]);

        $this->_json([
            'ok'              => true,
            'assign_log_id'   => (int)$log_id,
            'lead_id'         => $lead_id,
            'bd_uid'          => $bd_uid,
            'bd_name'         => $bd['name'],
            'prev_bd_uid'     => $prev_bd,
            'by_uid'          => $by_uid,
            'bd_open_load_now'=> $loads[$bd_uid] ?? 0,
        ]);
    }
}

<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Lead_score_api.php (A.3 - 2026-06-01)
 *
 * Endpoints:
 *   GET /api/lead_score/recompute?cid_id=X      Recompute score for one lead
 *   GET /api/lead_score/top?uid=X&limit=20       Top N scored leads for a BD user
 *
 * Both require Bearer token.
 */
class Lead_score_api extends CI_Controller {

    private $_known_token = '4eBaiAT7r4zu6OK3b8evjLNia1D7RGgb0qRTuLJfUSo';

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
        // Per-user daily JWT: sha1(secret|uid|date)
        $secret = getenv('STEM_DIGEST_TOKEN') ?: $this->_known_token;
        $candidates = array();
        foreach (array('uid','cm_uid','bd_uid','user_id') as $k) {
            if (isset($_GET[$k]) && (int)$_GET[$k] > 0) $candidates[(int)$_GET[$k]] = 1;
            if (isset($_POST[$k]) && (int)$_POST[$k] > 0) $candidates[(int)$_POST[$k]] = 1;
        }
        foreach (array_keys($candidates) as $uid_candidate) {
            foreach (array(date('Y-m-d'), date('Y-m-d', strtotime('-1 day'))) as $d) {
                if (hash_equals(sha1($secret . '|' . $uid_candidate . '|' . $d), $token)) return true;
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
    // GET /api/lead_score/recompute?cid_id=X
    // -----------------------------------------------------------------------
    public function recompute() {
        if (!$this->_bearer_ok()) {
            $this->_json(['ok' => false, 'error' => 'Unauthorized'], 401);
        }

        $cid_id = (int)$this->input->get('cid_id');
        if ($cid_id <= 0) {
            $this->_json(['ok' => false, 'error' => 'cid_id required and must be positive'], 422);
        }

        require_once(APPPATH . 'agents/LeadScoreAgent.php');
        $agent  = new LeadScoreAgent($this->db);
        $result = $agent->compute($cid_id);

        if (!$result['ok']) {
            $this->_json($result, 404);
        }

        $this->_json($result, 200);
    }

    // -----------------------------------------------------------------------
    // GET /api/lead_score/top?uid=X&limit=20
    // Returns top N leads ordered by base_score DESC for a given BD user.
    // -----------------------------------------------------------------------
    public function top() {
        if (!$this->_bearer_ok()) {
            $this->_json(['ok' => false, 'error' => 'Unauthorized'], 401);
        }

        $uid   = (int)$this->input->get('uid');
        $limit = max(1, min(50, (int)($this->input->get('limit') ?: 20)));

        if ($uid <= 0) {
            // No uid filter - return global top leads
            $rows = $this->db->query(
                "SELECT ls.cid_id, ls.base_score, ls.schedule7_fit,
                        ls.prior_giving_signal, ls.pipeline_strength, ls.recency_score,
                        ls.computed_at,
                        cm.compname AS company_name,
                        ic.cstatus,
                        ic.mainbd AS bd_uid
                 FROM lead_score_v1 ls
                 JOIN init_call ic ON ic.id = ls.cid_id
                 LEFT JOIN company_master cm ON cm.id = ic.cmpid_id
                 ORDER BY ls.base_score DESC
                 LIMIT ?",
                [$limit]
            )->result_array();
        } else {
            $rows = $this->db->query(
                "SELECT ls.cid_id, ls.base_score, ls.schedule7_fit,
                        ls.prior_giving_signal, ls.pipeline_strength, ls.recency_score,
                        ls.computed_at,
                        cm.compname AS company_name,
                        ic.cstatus,
                        ic.mainbd AS bd_uid
                 FROM lead_score_v1 ls
                 JOIN init_call ic ON ic.id = ls.cid_id
                 LEFT JOIN company_master cm ON cm.id = ic.cmpid_id
                 WHERE ic.mainbd = ?
                 ORDER BY ls.base_score DESC
                 LIMIT ?",
                [$uid, $limit]
            )->result_array();
        }

        $this->_json([
            'ok'    => true,
            'uid'   => $uid ?: null,
            'limit' => $limit,
            'count' => count($rows),
            'rows'  => $rows,
        ], 200);
    }

    // GET /api/lead_score?uid=<uid>&limit=20 -- base endpoint, added 28 May 2026
    // Returns top scored leads for a BD user (alias of top() with uid filter)
    public function score() {
        try {
            if (!$this->_bearer_ok()) {
                $this->_json(array('ok' => false, 'error' => 'Unauthorized'), 401);
            }
            $uid   = (int)$this->input->get('uid');
            $limit = max(1, min(100, (int)($this->input->get('limit') ?: 20)));
            // lead_score_v1 table from migration A.3
            if (!$this->db->table_exists('lead_score_v1')) {
                // Fallback: return stage distribution if scoring table absent
                $uid_clause = $uid > 0 ? ' AND ic.mainbd = ' . $uid : '';
                $rows = $this->db->query(
                    "SELECT ic.id AS cid_id, ic.cstatus AS stage,
                            cm.compname AS company_name,
                            ic.fbudget AS rs,
                            ic.mainbd AS bd_uid
                     FROM init_call ic
                     LEFT JOIN company_master cm ON cm.id = ic.cmpid_id
                     WHERE ic.cstatus NOT IN (12,13,1)
                     $uid_clause
                     ORDER BY ic.createDate DESC
                     LIMIT ?",
                    array($limit)
                )->result_array();
                $this->_json(array('ok' => true, 'uid' => $uid ?: null, 'limit' => $limit, 'count' => count($rows), 'rows' => $rows, 'note' => 'scoring_table_absent'));
                return;
            }
            $params = array($limit);
            if ($uid > 0) {
                $where = 'WHERE ic.mainbd = ?';
                array_unshift($params, $uid);
            } else {
                $where = '';
            }
            $rows = $this->db->query(
                "SELECT ls.cid_id, ls.base_score, ls.schedule7_fit,
                        ls.prior_giving_signal, ls.pipeline_strength, ls.recency_score,
                        ls.computed_at,
                        cm.compname AS company_name,
                        ic.cstatus,
                        ic.mainbd AS bd_uid
                 FROM lead_score_v1 ls
                 JOIN init_call ic ON ic.id = ls.cid_id
                 LEFT JOIN company_master cm ON cm.id = ic.cmpid_id
                 $where
                 ORDER BY ls.base_score DESC
                 LIMIT ?",
                $params
            )->result_array();
            $this->_json(array('ok' => true, 'uid' => $uid ?: null, 'limit' => $limit, 'count' => count($rows), 'rows' => $rows));
        } catch (Exception $e) {
            $this->_json(array('ok' => true, 'rows' => array(), 'note' => 'error', 'detail' => $e->getMessage()));
        }
    }


}

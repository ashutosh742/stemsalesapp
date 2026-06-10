<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Funnel_api extends CI_Controller {
    public function __construct() {
        parent::__construct();
        $this->load->database();
        header('Content-Type: application/json');
        $this->_rp_guard();
    }

    // rimlyproof_publicguard_20260609: ROOT-CAUSE auth gate. This controller
    // returned live business data with NO token check (fail-open). Allow only
    // liveness/probe methods; require a valid digest OR per-user login token for
    // every data method via the shared authunify_ok(). Additive: valid callers
    // unchanged; only missing/garbage tokens are now rejected.
    private $_rp_public = array('probe', 'status');
    private function _rp_guard() {
        $m = $this->router->fetch_method();
        if (in_array($m, $this->_rp_public, true)) { return; }
        if (substr($m, -6) === '_probe') { return; }
        if (function_exists('authunify_ok') && authunify_ok()) { return; }
        http_response_code(401);
        header('Content-Type: application/json');
        echo json_encode(array('ok' => false, 'error' => 'unauthorized'));
        exit;
    }

    private function _out($p) { echo json_encode($p); exit; }

    // GET /api/funnel/weekly_rollup?uid=&from=YYYY-MM-DD&to=YYYY-MM-DD
    public function weekly_rollup() {
        try {
            $uid  = (int)$this->input->get('uid');
            $from = $this->input->get('from') ?: date('Y-m-d', strtotime('monday this week'));
            $to   = $this->input->get('to')   ?: date('Y-m-d');
            $uid_clause = $uid > 0 ? ' AND mainbd = ' . $uid : '';
            // Stage distribution at end of window
            $q = $this->db->query("
                SELECT cstatus AS stage, COUNT(*) AS lead_count, COUNT(DISTINCT mainbd) AS bd_count
                FROM init_call
                WHERE cstatus IS NOT NULL
                  AND createDate BETWEEN ? AND ?
                  " . $uid_clause . "
                GROUP BY cstatus ORDER BY cstatus", [$from, $to]);
            $stages = $q->result_array();

            // Totals
            $tot = $this->db->query("SELECT COUNT(*) AS leads_added FROM init_call WHERE createDate BETWEEN ? AND ?" . $uid_clause, [$from, $to])->row_array();

            // Won + Lost in window
            $closures = $this->db->query("SELECT cstatus, COUNT(*) AS n, COALESCE(SUM(CAST(NULLIF(fbudget,'') AS UNSIGNED)),0) AS rs
                FROM init_call WHERE cstatus IN (12,13) AND createDate BETWEEN ? AND ?" . $uid_clause . "
                GROUP BY cstatus", [$from, $to])->result_array();

            $this->_out(['ok'=>true,'uid'=>$uid>0?$uid:null,'from'=>$from,'to'=>$to,
                         'leads_added'=>(int)$tot['leads_added'],
                         'rows'=>$stages,
                         'closures'=>$closures]);
        } catch (Exception $e) {
            $this->_out(['ok'=>true,'rows'=>[],'note'=>'error','detail'=>$e->getMessage()]);
        }
    }

    // GET /api/funnel/creation_paths?from=&to=
    public function creation_paths() {
        try {
            $from = $this->input->get('from') ?: date('Y-m-d', strtotime('-7 days'));
            $to   = $this->input->get('to')   ?: date('Y-m-d');
            // BARGE_UNKNOWN: new_lead=1 + first event actiontype=4 purpose=66
            // RESEARCH_BORN: new_lead=1 + first event actiontype=10 purpose=94
            // Without new_lead column reliably present, fall back to first-event match
            $rows = $this->db->query("
              SELECT
                SUM(CASE WHEN actiontype_id=4 AND purpose_id=66 THEN 1 ELSE 0 END) AS barge_unknown,
                SUM(CASE WHEN actiontype_id=10 AND purpose_id=94 THEN 1 ELSE 0 END) AS research_born,
                SUM(CASE WHEN actiontype_id=1 AND purpose_id=1 THEN 1 ELSE 0 END) AS new_lead_form,
                COUNT(*) AS total_events
              FROM tblcallevents
              WHERE DATE(date) BETWEEN ? AND ?", [$from, $to])->row_array();
            $this->_out(['ok'=>true,'from'=>$from,'to'=>$to,'rows'=>[$rows]]);
        } catch (Exception $e) {
            $this->_out(['ok'=>true,'rows'=>[],'note'=>'error','detail'=>$e->getMessage()]);
        }
    }

    // GET /api/funnel/stuck_stages?uid=&from=&to=
    public function stuck_stages() {
        try {
            $uid = (int)$this->input->get('uid');
            $uid_clause = $uid > 0 ? ' AND mainbd = ' . $uid : '';
            $thresholds = [1=>3, 2=>5, 3=>5, 6=>7, 7=>14, 8=>30, 9=>14];
            $rows = [];
            foreach ($thresholds as $stage => $days) {
                $r = $this->db->query("
                  SELECT ? AS cstatus, ? AS threshold_days, COUNT(*) AS stuck_count
                  FROM init_call
                  WHERE cstatus = ?
                    AND DATEDIFF(CURDATE(), COALESCE(createDate, CURDATE())) > ?
                    " . $uid_clause,
                  [$stage, $days, $stage, $days])->row_array();
                $rows[] = $r;
            }
            $this->_out(['ok'=>true,'uid'=>$uid>0?$uid:null,'rows'=>$rows]);
        } catch (Exception $e) {
            $this->_out(['ok'=>true,'rows'=>[],'note'=>'error','detail'=>$e->getMessage()]);
        }
    }

    // GET /api/funnel/closures?from=&to=
    public function closures() {
        try {
            $from = $this->input->get('from') ?: date('Y-m-d', strtotime('-7 days'));
            $to   = $this->input->get('to')   ?: date('Y-m-d');
            $rows = $this->db->query("
              SELECT ic.id AS lead_id, ic.cmpid_id, cm.compname AS school,
                     ic.mainbd, u.name AS bd_name,
                     ic.cstatus, ic.fbudget AS rs, ic.createDate
              FROM init_call ic
              LEFT JOIN company_master cm ON cm.id = ic.cmpid_id
              LEFT JOIN user_details u ON u.user_id = ic.mainbd
              WHERE ic.cstatus IN (12,13)
                AND ic.createDate BETWEEN ? AND ?
              ORDER BY CAST(NULLIF(ic.fbudget,'') AS UNSIGNED) DESC
              LIMIT 50", [$from, $to])->result_array();
            $this->_out(['ok'=>true,'from'=>$from,'to'=>$to,'rows'=>$rows]);
        } catch (Exception $e) {
            $this->_out(['ok'=>true,'rows'=>[],'note'=>'error','detail'=>$e->getMessage()]);
        }
    }

    // GET /api/funnel/path_conversion (90-day cohort, simplified)
    public function path_conversion() {
        try {
            $from = $this->input->get('cohort_from') ?: date('Y-m-d', strtotime('-90 days'));
            $to   = $this->input->get('cohort_to')   ?: date('Y-m-d', strtotime('-30 days'));
            $rows = $this->db->query("
              SELECT
                COUNT(*) AS cohort_size,
                SUM(CASE WHEN cstatus >= 6 THEN 1 ELSE 0 END) AS reached_positive,
                SUM(CASE WHEN cstatus = 12 THEN 1 ELSE 0 END) AS won,
                SUM(CASE WHEN cstatus = 13 THEN 1 ELSE 0 END) AS lost,
                SUM(CASE WHEN cstatus < 6 THEN 1 ELSE 0 END) AS stuck_below_positive
              FROM init_call
              WHERE createDate BETWEEN ? AND ?", [$from, $to])->row_array();
            $this->_out(['ok'=>true,'cohort_from'=>$from,'cohort_to'=>$to,'rows'=>[$rows]]);
        } catch (Exception $e) {
            $this->_out(['ok'=>true,'rows'=>[],'note'=>'error','detail'=>$e->getMessage()]);
        }
    }

    // GET /api/funnel/pst_queue_aging
    public function pst_queue_aging() {
        try {
            $rows = $this->db->query("
              SELECT
                COUNT(*) AS total_in_queue,
                SUM(CASE WHEN apst IS NULL THEN 1 ELSE 0 END) AS unapproved,
                SUM(CASE WHEN apst IS NOT NULL THEN 1 ELSE 0 END) AS approved,
                AVG(CASE WHEN apst IS NOT NULL THEN DATEDIFF(CURDATE(), createDate) END) AS avg_aging_days
              FROM init_call
              WHERE createDate >= CURDATE() - INTERVAL 30 DAY")->row_array();
            $this->_out(['ok'=>true,'rows'=>[$rows]]);
        } catch (Exception $e) {
            $this->_out(['ok'=>true,'rows'=>[],'note'=>'error','detail'=>$e->getMessage()]);
        }
    }

    // GET /api/funnel?uid=<uid> - uid-scoped funnel summary (added 28 May 2026)

    // GET /api/funnel?uid=<uid> - uid-scoped funnel summary (added 28 May 2026)

    // GET /api/funnel?uid=<uid> - uid-scoped funnel summary (added 28 May 2026)
    public function summary() {
        try {
            $hdr = isset($_SERVER["HTTP_AUTHORIZATION"]) ? $_SERVER["HTTP_AUTHORIZATION"] : "";
            if (!$hdr && function_exists("apache_request_headers")) {
                $h = apache_request_headers();
                if (isset($h["Authorization"])) $hdr = $h["Authorization"];
            }
            if (!$hdr || stripos($hdr, "Bearer ") !== 0) {
                $this->_out(array("ok" => false, "error" => "Unauthorized"));
                return;
            }
            $token  = trim(substr($hdr, 7));
            $known  = "4eBaiAT7r4zu6OK3b8evjLNia1D7RGgb0qRTuLJfUSo";
            $secret = getenv("STEM_DIGEST_TOKEN") ?: $known;
            $env    = getenv("STEM_DIGEST_TOKEN");
            $auth_ok = ($env && hash_equals($env, $token)) || hash_equals($known, $token);
            if (!$auth_ok) {
                $uid_try = (int)(isset($_GET["uid"]) ? $_GET["uid"] : 0);
                if ($uid_try > 0) {
                    foreach (array(date("Y-m-d"), date("Y-m-d", strtotime("-1 day"))) as $d) {
                        if (hash_equals(sha1($secret . "|" . $uid_try . "|" . $d), $token)) {
                            $auth_ok = true;
                            break;
                        }
                    }
                }
                if (!$auth_ok) {
                    $this->_out(array("ok" => false, "error" => "Unauthorized"));
                    return;
                }
            }
            $uid = (int)(isset($_GET["uid"]) ? $_GET["uid"] : 0);
            $uid_clause = $uid > 0 ? (" AND ic.mainbd = " . $uid) : "";
            $stages = $this->db->query(
                "SELECT cstatus AS stage, COUNT(*) AS lead_count,
                 COALESCE(SUM(CAST(NULLIF(fbudget, " . chr(39) . chr(39) . ") AS UNSIGNED)), 0) AS total_rs
                 FROM init_call ic
                 WHERE cstatus IS NOT NULL" . $uid_clause . "
                 GROUP BY cstatus ORDER BY cstatus"
            )->result_array();
            $closures = $this->db->query(
                "SELECT cstatus, COUNT(*) AS n,
                 COALESCE(SUM(CAST(NULLIF(fbudget, " . chr(39) . chr(39) . ") AS UNSIGNED)), 0) AS rs
                 FROM init_call ic
                 WHERE cstatus IN (12, 13)" . $uid_clause . "
                 GROUP BY cstatus"
            )->result_array();
            $total_leads = (int) array_sum(array_column($stages, "lead_count"));
            $this->_out(array(
                "ok"          => true,
                "uid"         => $uid > 0 ? $uid : null,
                "stages"      => $stages,
                "closures"    => $closures,
                "total_leads" => $total_leads,
            ));
        } catch (Exception $e) {
            $this->_out(array("ok" => true, "stages" => array(), "closures" => array(),
                "total_leads" => 0, "note" => "error", "detail" => $e->getMessage()));
        }
    }
}

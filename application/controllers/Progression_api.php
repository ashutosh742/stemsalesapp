<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Progression_api extends CI_Controller {
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

    // Stuck thresholds per migration 012
    private $thresholds = [1=>3, 2=>5, 3=>5, 6=>7, 7=>14, 8=>30, 9=>14];

    // GET /api/progression/stuck?top_n=5
    public function stuck() {
        try {
            $top_n = max(1, min(50, (int)($this->input->get('top_n') ?: 5)));
            $out = [];
            foreach ($this->thresholds as $stage => $days) {
                $r = $this->db->query("
                  SELECT ic.id AS lead_id, ic.cmpid_id, cm.compname AS school,
                         ic.mainbd, u.name AS bd_name,
                         ic.cstatus AS stage,
                         DATEDIFF(CURDATE(), ic.createDate) AS days_in_status,
                         ic.fbudget AS rs
                  FROM init_call ic
                  LEFT JOIN company_master cm ON cm.id = ic.cmpid_id
                  LEFT JOIN user_details u ON u.user_id = ic.mainbd
                  WHERE ic.cstatus = ?
                    AND DATEDIFF(CURDATE(), ic.createDate) > ?
                  ORDER BY days_in_status DESC
                  LIMIT ?", [$stage, $days, $top_n])->result_array();
                foreach ($r as $row) $out[] = $row;
            }
            $this->_out(['ok'=>true,'rows'=>$out,'count'=>count($out)]);
        } catch (Exception $e) {
            $this->_out(['ok'=>true,'rows'=>[],'note'=>'error','detail'=>$e->getMessage()]);
        }
    }

    // GET /api/progression/mom_blockers?days=7
    public function mom_blockers() {
        try {
            $days = max(1, (int)($this->input->get('days') ?: 7));
            $rows = $this->db->query("
              SELECT m.id AS mom_id, m.init_cmpid AS lead_id, m.user_id AS bd_uid,
                     u.name AS bd_name, cm.compname AS school,
                     m.approved_status, m.cdate AS submitted_at,
                     DATEDIFF(CURDATE(), m.cdate) AS age_days
              FROM mom_data m
              LEFT JOIN init_call ic ON ic.id = m.init_cmpid
              LEFT JOIN company_master cm ON cm.id = ic.cmpid_id
              LEFT JOIN user_details u ON u.user_id = m.user_id
              WHERE (m.approved_status IS NULL OR m.approved_status NOT IN ('approved','Approved','1'))
                AND m.cdate >= CURDATE() - INTERVAL ? DAY
              ORDER BY m.cdate ASC
              LIMIT 20", [$days])->result_array();
            $this->_out(['ok'=>true,'rows'=>$rows,'count'=>count($rows)]);
        } catch (Exception $e) {
            $this->_out(['ok'=>true,'rows'=>[],'note'=>'error','detail'=>$e->getMessage()]);
        }
    }

    // GET /api/progression/top_movers?from=&to=&top_n=10
    public function top_movers() {
        try {
            $from = $this->input->get('from') ?: date('Y-m-d', strtotime('monday this week'));
            $to   = $this->input->get('to')   ?: date('Y-m-d');
            $top_n = max(1, min(50, (int)($this->input->get('top_n') ?: 10)));
            // Count meetings per BD in window as proxy for activity score
            $rows = $this->db->query("
              SELECT t.user_id AS bd_uid, u.name AS bd_name,
                     COUNT(*) AS events,
                     SUM(CASE WHEN t.actiontype_id IN (3,4) THEN 1 ELSE 0 END) AS meetings,
                     SUM(CASE WHEN t.actiontype_id = 10 THEN 1 ELSE 0 END) AS research,
                     COUNT(DISTINCT t.cid_id) AS leads_touched
              FROM tblcallevents t
              LEFT JOIN user_details u ON u.user_id = t.user_id
              WHERE DATE(t.date) BETWEEN ? AND ?
                AND u.type_id = 3
              GROUP BY t.user_id, u.name
              ORDER BY events DESC
              LIMIT ?", [$from, $to, $top_n])->result_array();
            $this->_out(['ok'=>true,'rows'=>$rows,'count'=>count($rows)]);
        } catch (Exception $e) {
            $this->_out(['ok'=>true,'rows'=>[],'note'=>'error','detail'=>$e->getMessage()]);
        }
    }

    // GET /api/progression/matrix?from=&to=
    public function matrix() {
        try {
            $from = $this->input->get('from') ?: date('Y-m-d', strtotime('yesterday'));
            $to   = $this->input->get('to')   ?: date('Y-m-d', strtotime('yesterday'));
            // Real transitions live in lead_progression_log when populated; for now derive from cstatus snapshot
            $rows = $this->db->query("
              SELECT from_status, to_status, COUNT(*) AS n
              FROM lead_progression_log
              WHERE DATE(created_at) BETWEEN ? AND ?
              GROUP BY from_status, to_status
              ORDER BY n DESC", [$from, $to])->result_array();
            $this->_out(['ok'=>true,'from'=>$from,'to'=>$to,'rows'=>$rows,'count'=>count($rows)]);
        } catch (Exception $e) {
            $this->_out(['ok'=>true,'rows'=>[],'note'=>'error','detail'=>$e->getMessage()]);
        }
    }

    // GET /api/progression/transitions?from_status=&to_status=&days=1
    public function transitions() {
        try {
            $days = max(1, (int)($this->input->get('days') ?: 1));
            $from_status = $this->input->get('from_status');
            $to_status   = $this->input->get('to_status');
            $where = "DATE(created_at) >= CURDATE() - INTERVAL $days DAY";
            if ($from_status !== null && $from_status !== '') $where .= " AND from_status=" . (int)$from_status;
            if ($to_status   !== null && $to_status   !== '') $where .= " AND to_status="   . (int)$to_status;
            $rows = $this->db->query("
              SELECT id, lead_id, bd_uid, from_status, to_status, progression_type, triggered_by, created_at
              FROM lead_progression_log
              WHERE $where
              ORDER BY created_at DESC LIMIT 100")->result_array();
            $this->_out(['ok'=>true,'rows'=>$rows,'count'=>count($rows)]);
        } catch (Exception $e) {
            $this->_out(['ok'=>true,'rows'=>[],'note'=>'error','detail'=>$e->getMessage()]);
        }
    }

    // GET /api/progression/closure_blockers?days=1
    public function closure_blockers() {
        try {
            $days = max(1, (int)($this->input->get('days') ?: 1));
            $rows = $this->db->query("
              SELECT lpl.id, lpl.lead_id, lpl.bd_uid, u.name AS bd_name,
                     cm.compname AS school,
                     lpl.from_status, lpl.to_status,
                     lpl.created_at,
                     TIMESTAMPDIFF(HOUR, lpl.created_at, NOW()) AS age_hours
              FROM lead_progression_log lpl
              LEFT JOIN init_call ic ON ic.id = lpl.lead_id
              LEFT JOIN company_master cm ON cm.id = ic.cmpid_id
              LEFT JOIN user_details u ON u.user_id = lpl.bd_uid
              WHERE lpl.from_status = 9 AND lpl.to_status = 12
                AND lpl.created_at >= NOW() - INTERVAL ? DAY
              ORDER BY lpl.created_at DESC", [$days])->result_array();
            $this->_out(['ok'=>true,'rows'=>$rows,'count'=>count($rows)]);
        } catch (Exception $e) {
            $this->_out(['ok'=>true,'rows'=>[],'note'=>'error','detail'=>$e->getMessage()]);
        }
    }

    // GET /api/progression/fbudget_mismatch?days=1
    public function fbudget_mismatch() {
        try {
            $days = max(1, (int)($this->input->get('days') ?: 1));
            // Compare fbudget on init_call vs proposal_amt on init_call (real columns)
            $rows = $this->db->query("
              SELECT ic.id AS lead_id, ic.mainbd AS bd_uid, u.name AS bd_name,
                     cm.compname AS school,
                     CAST(NULLIF(ic.fbudget,'') AS UNSIGNED) AS fbudget_now,
                     CAST(NULLIF(ic.proposal_amt,'NA') AS UNSIGNED) AS proposal_amt
              FROM init_call ic
              LEFT JOIN company_master cm ON cm.id = ic.cmpid_id
              LEFT JOIN user_details u ON u.user_id = ic.mainbd
              WHERE ic.cstatus IN (9,12)
                AND ic.fbudget IS NOT NULL AND ic.fbudget != ''
                AND ic.proposal_amt IS NOT NULL AND ic.proposal_amt != 'NA' AND ic.proposal_amt != ''
              ORDER BY ic.id DESC LIMIT 20")->result_array();
            // Compute delta
            foreach ($rows as &$r) {
                $a = (float)$r['fbudget_now']; $b = (float)$r['proposal_amt'];
                $r['delta_pct'] = ($b > 0) ? round(abs($a-$b)/$b*100, 1) : null;
            }
            $rows = array_values(array_filter($rows, function($r){ return $r['delta_pct'] !== null && $r['delta_pct'] > 10; }));
            $this->_out(['ok'=>true,'rows'=>$rows,'count'=>count($rows)]);
        } catch (Exception $e) {
            $this->_out(['ok'=>true,'rows'=>[],'note'=>'error','detail'=>$e->getMessage()]);
        }
    }

    // POST /api/progression/refresh_daily?date=YYYY-MM-DD
    public function refresh_daily() {
        try {
            $date = $this->input->get('date') ?: date('Y-m-d', strtotime('yesterday'));
            $rows = $this->db->query("
              SELECT COUNT(*) AS transitions_processed
              FROM lead_progression_log WHERE DATE(created_at)=?", [$date])->row_array();
            $this->_out(['ok'=>true,'date'=>$date,'rows_refreshed'=>(int)$rows['transitions_processed']]);
        } catch (Exception $e) {
            $this->_out(['ok'=>true,'rows'=>[],'note'=>'error','detail'=>$e->getMessage()]);
        }
    }

    // GET /api/progression/score?uid=<uid> -- added 28 May 2026
    public function score() {
        try {
            $uid = (int)$this->input->get('uid');
            if ($uid <= 0) {
                $this->_out(array('ok' => true, 'rows' => array(), 'note' => 'uid_required'));
                return;
            }
            // Summarise stages for this BD's leads
            $sql = "SELECT ic.cstatus AS stage,
                           COUNT(*) AS lead_count,
                           COALESCE(SUM(CAST(NULLIF(ic.fbudget,'') AS UNSIGNED)),0) AS total_rs,
                           MAX(DATEDIFF(CURDATE(), ic.createDate)) AS max_age_days
                    FROM init_call ic
                    WHERE ic.mainbd = ?
                      AND ic.cstatus IS NOT NULL
                    GROUP BY ic.cstatus
                    ORDER BY ic.cstatus";
            $rows = $this->db->query($sql, array($uid))->result_array();
            // Compute a simple progression score: % of leads in positive or won stages (6,9,12)
            $total = array_sum(array_column($rows, 'lead_count'));
            $positive = 0;
            foreach ($rows as $r) {
                if (in_array((int)$r['stage'], array(6, 9, 12))) $positive += (int)$r['lead_count'];
            }
            $score = $total > 0 ? round(($positive / $total) * 100, 1) : 0;
            $this->_out(array(
                'ok'    => true,
                'uid'   => $uid,
                'score' => $score,
                'total_leads' => $total,
                'positive_leads' => $positive,
                'rows'  => $rows,
            ));
        } catch (Exception $e) {
            $this->_out(array('ok' => true, 'rows' => array(), 'note' => 'error', 'detail' => $e->getMessage()));
        }
    }


}

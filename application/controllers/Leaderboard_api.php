<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Leaderboard_api extends CI_Controller {
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

    // GET /api/leaderboard/daily?date=YYYY-MM-DD
    public function daily() {
        try {
            $date = $this->input->get('date') ?: date('Y-m-d', strtotime('yesterday'));
            $rows = $this->db->query("
              SELECT t.user_id AS bd_uid, u.name AS bd_name, u.user_cluster_zone AS cluster,
                     COUNT(*) AS events,
                     SUM(CASE WHEN t.actiontype_id IN (3,4) THEN 1 ELSE 0 END) AS meetings,
                     SUM(CASE WHEN t.actiontype_id = 10 THEN 1 ELSE 0 END) AS research,
                     SUM(CASE WHEN t.actiontype_id = 1 THEN 1 ELSE 0 END) AS calls,
                     COUNT(DISTINCT t.cid_id) AS leads_touched
              FROM tblcallevents t
              LEFT JOIN user_details u ON u.user_id = t.user_id
              WHERE t.date >= ? AND t.date < DATE_ADD(?, INTERVAL 1 DAY)
                AND u.type_id = 3
              GROUP BY t.user_id, u.name, u.user_cluster_zone
              ORDER BY events DESC
              LIMIT 20", [$date, $date])->result_array();
            $this->_out(['ok'=>true,'date'=>$date,'rows'=>$rows,'count'=>count($rows)]);
        } catch (Exception $e) {
            $this->_out(['ok'=>true,'rows'=>[],'note'=>'error','detail'=>$e->getMessage()]);
        }
    }

    // GET /api/leaderboard/weekly?from=&to=
    public function weekly() {
        try {
            $from = $this->input->get('from') ?: date('Y-m-d', strtotime('monday this week'));
            $to   = $this->input->get('to')   ?: date('Y-m-d');
            $rows = $this->db->query("
              SELECT t.user_id AS bd_uid, u.name AS bd_name, u.user_cluster_zone AS cluster,
                     COUNT(*) AS events,
                     SUM(CASE WHEN t.actiontype_id IN (3,4) THEN 1 ELSE 0 END) AS meetings,
                     COUNT(DISTINCT t.cid_id) AS leads_touched,
                     COUNT(DISTINCT DATE(t.date)) AS active_days
              FROM tblcallevents t
              LEFT JOIN user_details u ON u.user_id = t.user_id
              WHERE t.date >= ? AND t.date < DATE_ADD(?, INTERVAL 1 DAY)
                AND u.type_id = 3
              GROUP BY t.user_id, u.name, u.user_cluster_zone
              ORDER BY events DESC
              LIMIT 20", [$from, $to])->result_array();
            $this->_out(['ok'=>true,'from'=>$from,'to'=>$to,'rows'=>$rows,'count'=>count($rows)]);
        } catch (Exception $e) {
            $this->_out(['ok'=>true,'rows'=>[],'note'=>'error','detail'=>$e->getMessage()]);
        }
    }

    // GET /api/leaderboard/rp?from=&to=  (relationship-priority meetings = actiontype 3,4 with mom)
    public function rp() {
        try {
            $from = $this->input->get('from') ?: date('Y-m-d', strtotime('monday this week'));
            $to   = $this->input->get('to')   ?: date('Y-m-d');
            $rows = $this->db->query("
              SELECT t.user_id AS bd_uid, u.name AS bd_name,
                     COUNT(*) AS rp_meetings,
                     COUNT(DISTINCT t.cid_id) AS schools
              FROM tblcallevents t
              LEFT JOIN user_details u ON u.user_id = t.user_id
              WHERE t.date >= ? AND t.date < DATE_ADD(?, INTERVAL 1 DAY)
                AND t.actiontype_id IN (3,4)
                AND u.type_id = 3
              GROUP BY t.user_id, u.name
              ORDER BY rp_meetings DESC
              LIMIT 20", [$from, $to])->result_array();
            $this->_out(['ok'=>true,'from'=>$from,'to'=>$to,'rows'=>$rows,'count'=>count($rows)]);
        } catch (Exception $e) {
            $this->_out(['ok'=>true,'rows'=>[],'note'=>'error','detail'=>$e->getMessage()]);
        }
    }
}

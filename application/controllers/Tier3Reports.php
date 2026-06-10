<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * STEM CRM v2.8 - Tier-3 strategic reports.
 * Endpoints: quarter_strategy, sales_graph, all_review_planning,
 *            closing_timeline, meeting_detail_new, meeting_vs_proposal.
 * All read-only.  Empty result is a valid answer (do not fabricate).
 */
class Tier3Reports extends CI_Controller {
    public function __construct() {
        parent::__construct();
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


    public function probe() {
        $this->_json(['ok' => true, 'controller' => 'Tier3Reports']);
    }

    private function _bd_uid() {
        $auth = $this->input->get_request_header('Authorization', TRUE);
        if (!$auth || strpos($auth, 'Bearer ') !== 0) {
            http_response_code(401);
            $this->_json(['ok' => false, 'message' => 'Unauthorized']);
            return false;
        }
        return (int) $this->input->get('bd_uid', TRUE);
    }

    // sno 5  -- Quarter strategy: count of open leads per cstatus bucket per BD
    public function quarter_strategy() {
        $bd = $this->_bd_uid(); if ($bd === false) return;
        $sql = "SELECT ic.mainbd AS bd_uid, u.name AS bd_name, ic.cstatus,
                       COUNT(*) AS cnt
                FROM init_call ic
                LEFT JOIN user u ON u.uid = ic.mainbd
                WHERE ic.cstatus IS NOT NULL AND ic.cstatus NOT IN (12, 13) ";
        $bind = [];
        if ($bd > 0) { $sql .= " AND ic.mainbd = ? "; $bind[] = $bd; }
        $sql .= " GROUP BY ic.mainbd, ic.cstatus ORDER BY ic.mainbd, ic.cstatus";
        $q = $this->db->query($sql, $bind);
        $this->_json(['ok' => true, 'rows' => $q ? $q->result_array() : []]);
    }

    // sno 6 -- Sales graph: leads created per day in last 30 days
    public function sales_graph() {
        $bd = $this->_bd_uid(); if ($bd === false) return;
        $from = date('Y-m-d', strtotime('-30 days'));
        $sql = "SELECT DATE(ic.createDate) AS day, COUNT(*) AS leads_created
                FROM init_call ic
                WHERE ic.createDate >= ? ";
        $bind = [$from];
        if ($bd > 0) { $sql .= " AND ic.mainbd = ? "; $bind[] = $bd; }
        $sql .= " GROUP BY DATE(ic.createDate) ORDER BY day ASC";
        $q = $this->db->query($sql, $bind);
        $this->_json(['ok' => true, 'rows' => $q ? $q->result_array() : []]);
    }

    // sno 7 -- All review planning: upcoming reviews (sdatet >= today)
    public function all_review_planning() {
        $bd = $this->_bd_uid(); if ($bd === false) return;
        $today = date('Y-m-d');
        $sql = "SELECT r.id AS review_id, r.sdatet, r.plant, r.uid AS reviewer_uid,
                       u.name AS reviewer_name, r.bdid AS bd_uid, r.reviewtype,
                       r.fixdate, r.review_in_quarter
                FROM allreview r
                LEFT JOIN user u ON u.uid = r.uid
                WHERE DATE(r.sdatet) >= ? AND r.closet IS NULL ";
        $bind = [$today];
        if ($bd > 0) { $sql .= " AND r.bdid = ? "; $bind[] = $bd; }
        $sql .= " ORDER BY r.sdatet ASC LIMIT 200";
        $q = $this->db->query($sql, $bind);
        $this->_json(['ok' => true, 'rows' => $q ? $q->result_array() : []]);
    }

    // sno 8 -- Closing timeline: every lead with an expected_close_date (from init_call closure_pipeline marker)
    public function closing_timeline() {
        $bd = $this->_bd_uid(); if ($bd === false) return;
        $sql = "SELECT ic.id AS cid_id, cm.compname AS company_name, ic.cstatus,
                       ic.fbudget, ic.mainbd AS bd_uid, u.name AS bd_name,
                       ic.closure_pipeline, ic.updated_at
                FROM init_call ic
                LEFT JOIN company_master cm ON cm.id  = ic.cmpid_id
                LEFT JOIN user           u  ON u.uid = ic.mainbd
                WHERE ic.cstatus IN (6, 8, 9) ";
        $bind = [];
        if ($bd > 0) { $sql .= " AND ic.mainbd = ? "; $bind[] = $bd; }
        $sql .= " ORDER BY ic.updated_at DESC LIMIT 200";
        $q = $this->db->query($sql, $bind);
        $this->_json(['ok' => true, 'rows' => $q ? $q->result_array() : []]);
    }

    // sno 9 -- Meeting detail new: meetings (actiontype 3 or 4) in last 30 days
    public function meeting_detail_new() {
        $bd = $this->_bd_uid(); if ($bd === false) return;
        $from = date('Y-m-d', strtotime('-30 days'));
        $sql = "SELECT t.id AS task_id, t.date, t.user_id AS bd_uid, u.name AS bd_name,
                       t.actiontype_id, t.purpose_id, t.cid_id, cm.compname AS company_name,
                       t.meeting_type, t.mom_received, t.mom_approved, t.remarks
                FROM tblcallevents t
                LEFT JOIN user u             ON u.uid = t.user_id
                LEFT JOIN init_call ic       ON ic.id = t.cid_id
                LEFT JOIN company_master cm  ON cm.id = ic.cmpid_id
                WHERE t.actiontype_id IN (3, 4) AND DATE(t.date) >= ? ";
        $bind = [$from];
        if ($bd > 0) { $sql .= " AND t.user_id = ? "; $bind[] = $bd; }
        $sql .= " ORDER BY t.date DESC LIMIT 500";
        $q = $this->db->query($sql, $bind);
        $this->_json(['ok' => true, 'rows' => $q ? $q->result_array() : []]);
    }

    // sno 10 -- Meeting vs proposal: per BD, count of meetings vs count of proposals last 30 days
    public function meeting_vs_proposal() {
        $bd = $this->_bd_uid(); if ($bd === false) return;
        $from = date('Y-m-d', strtotime('-30 days'));
        $sql = "SELECT t.user_id AS bd_uid, u.name AS bd_name,
                       SUM(CASE WHEN t.actiontype_id IN (3,4) THEN 1 ELSE 0 END) AS meetings,
                       SUM(CASE WHEN t.actiontype_id = 11      THEN 1 ELSE 0 END) AS proposals
                FROM tblcallevents t
                LEFT JOIN user u ON u.uid = t.user_id
                WHERE DATE(t.date) >= ? ";
        $bind = [$from];
        if ($bd > 0) { $sql .= " AND t.user_id = ? "; $bind[] = $bd; }
        $sql .= " GROUP BY t.user_id ORDER BY meetings DESC LIMIT 500";
        $q = $this->db->query($sql, $bind);
        $this->_json(['ok' => true, 'rows' => $q ? $q->result_array() : []]);
    }

    private function _json(array $payload) {
        $this->output->set_content_type('application/json')
            ->set_output(json_encode($payload));
    }
}

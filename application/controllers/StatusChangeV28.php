<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * STEM CRM v2.8 - Status change reports (sno 25-26, 30-34).
 * Uses sales_status_change_task_star_rating as the audit log.
 */
class StatusChangeV28 extends CI_Controller {
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
        $this->output->set_content_type('application/json')
            ->set_output(json_encode(['ok' => true, 'controller' => 'StatusChangeV28']));
    }

    private function _auth_uid() {
        $auth = $this->input->get_request_header('Authorization', TRUE);
        if (!$auth || strpos($auth, 'Bearer ') !== 0) {
            http_response_code(401);
            $this->output->set_content_type('application/json')
                ->set_output(json_encode(['ok' => false, 'message' => 'Unauthorized']));
            return false;
        }
        return (int) $this->input->get('bd_uid', TRUE);
    }

    private function _changes_window($bd_uid, $from, $to) {
        $sql = "SELECT
                    s.id, s.date, s.user_id, s.task_id, s.old_status, s.new_status, s.types,
                    u.name AS bd_name,
                    ce.cid_id,
                    cm.compname AS company_name
                FROM sales_status_change_task_star_rating s
                LEFT JOIN user            u  ON u.uid = s.user_id
                LEFT JOIN tblcallevents   ce ON ce.id = CAST(s.task_id AS UNSIGNED)
                LEFT JOIN init_call       ic ON ic.id = ce.cid_id
                LEFT JOIN company_master  cm ON cm.id = ic.cmpid_id
                WHERE DATE(s.date) BETWEEN ? AND ? ";
        $bind = [$from, $to];
        if ($bd_uid > 0) { $sql .= " AND s.user_id = ? "; $bind[] = $bd_uid; }
        $sql .= " ORDER BY s.date DESC LIMIT 500";
        $q = $this->db->query($sql, $bind);
        return $q ? $q->result_array() : [];
    }

    public function today_changes() {
        $bd = $this->_auth_uid(); if ($bd === false) return;
        $today = date('Y-m-d');
        $this->output->set_content_type('application/json')
            ->set_output(json_encode(['ok' => true, 'rows' => $this->_changes_window($bd, $today, $today)]));
    }

    public function week_changes() {
        $bd = $this->_auth_uid(); if ($bd === false) return;
        $from = date('Y-m-d', strtotime('monday this week'));
        $to   = date('Y-m-d');
        $this->output->set_content_type('application/json')
            ->set_output(json_encode(['ok' => true, 'rows' => $this->_changes_window($bd, $from, $to)]));
    }

    public function stuck_below_positive() {
        $bd = $this->_auth_uid(); if ($bd === false) return;
        // Leads currently in cstatus 1/2/3 stuck >=7 days
        $sql = "SELECT ic.id AS cid_id, cm.compname AS company_name, ic.cstatus,
                       ic.mainbd AS bd_uid, u.name AS bd_name,
                       DATEDIFF(NOW(), ic.createDate) AS days_in_stage
                FROM init_call ic
                LEFT JOIN company_master cm ON cm.id  = ic.cmpid_id
                LEFT JOIN user           u  ON u.uid = ic.mainbd
                WHERE ic.cstatus IN (1,2,3)
                  AND DATEDIFF(NOW(), ic.createDate) >= 7 ";
        $bind = [];
        if ($bd > 0) { $sql .= " AND ic.mainbd = ? "; $bind[] = $bd; }
        $sql .= " ORDER BY days_in_stage DESC LIMIT 500";
        $q = $this->db->query($sql, $bind);
        $this->output->set_content_type('application/json')
            ->set_output(json_encode(['ok' => true, 'rows' => $q ? $q->result_array() : []]));
    }

    public function stuck_promoted_contrast() {
        $bd = $this->_auth_uid(); if ($bd === false) return;
        $week_start = date('Y-m-d', strtotime('-7 days'));
        // Leads that were stuck below positive but got promoted to 6+ in last 7 days
        $sql = "SELECT s.id, s.date, s.user_id, u.name AS bd_name,
                       s.old_status, s.new_status, ce.cid_id,
                       cm.compname AS company_name
                FROM sales_status_change_task_star_rating s
                LEFT JOIN user u             ON u.uid = s.user_id
                LEFT JOIN tblcallevents ce   ON ce.id = CAST(s.task_id AS UNSIGNED)
                LEFT JOIN init_call ic       ON ic.id = ce.cid_id
                LEFT JOIN company_master cm  ON cm.id = ic.cmpid_id
                WHERE s.old_status IN (1,2,3)
                  AND s.new_status >= 6
                  AND DATE(s.date) >= ? ";
        $bind = [$week_start];
        if ($bd > 0) { $sql .= " AND s.user_id = ? "; $bind[] = $bd; }
        $sql .= " ORDER BY s.date DESC LIMIT 500";
        $q = $this->db->query($sql, $bind);
        $this->output->set_content_type('application/json')
            ->set_output(json_encode(['ok' => true, 'rows' => $q ? $q->result_array() : []]));
    }

    public function won_today() {
        $bd = $this->_auth_uid(); if ($bd === false) return;
        $today = date('Y-m-d');
        $sql = "SELECT s.id, s.date, s.user_id, u.name AS bd_name,
                       s.old_status, s.new_status, ce.cid_id,
                       cm.compname AS company_name
                FROM sales_status_change_task_star_rating s
                LEFT JOIN user u             ON u.uid = s.user_id
                LEFT JOIN tblcallevents ce   ON ce.id = CAST(s.task_id AS UNSIGNED)
                LEFT JOIN init_call ic       ON ic.id = ce.cid_id
                LEFT JOIN company_master cm  ON cm.id = ic.cmpid_id
                WHERE s.new_status = 12 AND DATE(s.date) = ? ";
        $bind = [$today];
        if ($bd > 0) { $sql .= " AND s.user_id = ? "; $bind[] = $bd; }
        $sql .= " ORDER BY s.date DESC LIMIT 500";
        $q = $this->db->query($sql, $bind);
        $this->output->set_content_type('application/json')
            ->set_output(json_encode(['ok' => true, 'rows' => $q ? $q->result_array() : []]));
    }

    public function lost_today() {
        $bd = $this->_auth_uid(); if ($bd === false) return;
        $today = date('Y-m-d');
        $sql = "SELECT s.id, s.date, s.user_id, u.name AS bd_name,
                       s.old_status, s.new_status, ce.cid_id,
                       cm.compname AS company_name
                FROM sales_status_change_task_star_rating s
                LEFT JOIN user u             ON u.uid = s.user_id
                LEFT JOIN tblcallevents ce   ON ce.id = CAST(s.task_id AS UNSIGNED)
                LEFT JOIN init_call ic       ON ic.id = ce.cid_id
                LEFT JOIN company_master cm  ON cm.id = ic.cmpid_id
                WHERE s.new_status = 13 AND DATE(s.date) = ? ";
        $bind = [$today];
        if ($bd > 0) { $sql .= " AND s.user_id = ? "; $bind[] = $bd; }
        $sql .= " ORDER BY s.date DESC LIMIT 500";
        $q = $this->db->query($sql, $bind);
        $this->output->set_content_type('application/json')
            ->set_output(json_encode(['ok' => true, 'rows' => $q ? $q->result_array() : []]));
    }
}

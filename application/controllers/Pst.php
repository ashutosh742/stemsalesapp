<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require_once APPPATH . 'controllers/RestApiBaseController.php';

/**
 * Pst.php - Migration 049 PST Queue API
 * Staging-compatible: uses company_master for school name, user table (not users),
 * user.name for BD name, init_call.id as lead pk (no cid_id on staging).
 */
class Pst extends RestApiBaseController {

    public function __construct() {
        parent::__construct();
        $this->load->helper(array('url', 'date'));
    }

    public function probe() {
        $this->_json(array(
            'ok'        => true,
            'service'   => 'pst_queue',
            'deployed'  => true,
            'ts'        => date('Y-m-d H:i:s'),
            'auth_ok'   => $this->_auth_ok,
        ));
    }

    public function unassigned() {
        $sql = "SELECT ic.id AS lead_id, cm.compname AS school_name, cm.city,
                    ic.apst, ic.pstadt, ic.fbudget, ic.cstatus,
                    u.name AS bd_name
                FROM init_call ic
                LEFT JOIN company_master cm ON cm.id = ic.cmpid_id
                LEFT JOIN user u ON u.uid = ic.mainbd
                WHERE ic.cstatus = 7
                  AND (ic.apst IS NULL OR ic.apst = 0)
                ORDER BY ic.pstadt ASC LIMIT 200";
        $res = $this->db->query($sql);
        $leads = $res ? $res->result_array() : array();

        $pst_sql = "SELECT u.uid AS uid, u.name,
                           COUNT(ic2.id) AS open_count
                    FROM user u
                    LEFT JOIN init_call ic2 ON ic2.apst = u.uid AND ic2.cstatus = 7
                    WHERE u.type_id IN (2, 13, 3)
                    GROUP BY u.uid, u.name ORDER BY open_count ASC LIMIT 50";
        $pst_res = $this->db->query($pst_sql);
        $pst_users = $pst_res ? $pst_res->result_array() : array();

        $this->_json(array('ok' => true, 'count' => count($leads), 'leads' => $leads, 'pst_users' => $pst_users));
    }

    public function queue() {
        $uid = (int) $this->input->get('uid');
        if ($uid <= 0) {
            $sess = $this->session->userdata('user');
            $uid = $sess ? (int)($sess['user_id'] ?? 0) : 0;
        }
        if ($uid <= 0) { return $this->_fail(400, 'uid required'); }

        $sql = "SELECT ic.id AS lead_id, cm.compname AS school_name, cm.city,
                    ic.apst, ic.pstadt, ic.fbudget, ic.cstatus,
                    u.name AS bd_name,
                    CASE
                        WHEN ic.cstatus IN (12) THEN 'won'
                        WHEN ic.cstatus IN (13) THEN 'lost'
                        WHEN ic.cstatus IN (8, 9, 10, 11) THEN 'follow_up'
                        ELSE 'active'
                    END AS queue_status
                FROM init_call ic
                LEFT JOIN company_master cm ON cm.id = ic.cmpid_id
                LEFT JOIN user u ON u.uid = ic.mainbd
                WHERE ic.apst = ?
                  AND ic.cstatus IN (7, 8, 9, 10, 11, 12, 13)
                ORDER BY ic.pstadt ASC LIMIT 300";

        $rows = $this->db->query($sql, array($uid))->result_array();
        $grouped = array('active' => array(), 'follow_up' => array(), 'won' => array(), 'lost' => array());
        foreach ($rows as $r) {
            $g = isset($r['queue_status']) ? $r['queue_status'] : 'active';
            $grouped[$g][] = $r;
        }
        $this->_json(array('ok' => true, 'uid' => $uid, 'total' => count($rows), 'grouped' => $grouped));
    }

    public function assign() {
        $raw = file_get_contents('php://input');
        $body = @json_decode($raw, true);
        if (!$body) { $body = $_POST; }

        $lead_ids = isset($body['lead_ids']) ? (array)$body['lead_ids'] : array();
        $pst_id   = isset($body['pst_id'])   ? (int)$body['pst_id']    : 0;
        $due_date = isset($body['due_date'])  ? trim($body['due_date']) : '';

        if (empty($lead_ids) || $pst_id <= 0 || $due_date === '') {
            return $this->_fail(400, 'lead_ids, pst_id, and due_date are required');
        }
        if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $due_date)) {
            return $this->_fail(400, 'due_date must be YYYY-MM-DD');
        }

        $updated = 0;
        foreach ($lead_ids as $lead_id) {
            $lead_id = (int)$lead_id;
            if ($lead_id <= 0) continue;
            $this->db->query(
                "UPDATE init_call SET apst = ?, pstadt = ? WHERE id = ? AND (apst IS NULL OR apst = 0)",
                array($pst_id, $due_date, $lead_id)
            );
            $updated++;
        }
        $this->_json(array('ok' => true, 'updated' => $updated, 'pst_id' => $pst_id, 'due_date' => $due_date));
    }

    public function change() {
        $raw = file_get_contents('php://input');
        $body = @json_decode($raw, true);
        if (!$body) { $body = $_POST; }

        $lead_id    = isset($body['lead_id'])    ? (int)$body['lead_id']     : 0;
        $new_pst_id = isset($body['new_pst_id']) ? (int)$body['new_pst_id'] : 0;
        $reason     = isset($body['reason'])     ? trim($body['reason'])     : '';

        if ($lead_id <= 0 || $new_pst_id <= 0) {
            return $this->_fail(400, 'lead_id and new_pst_id are required');
        }
        if (strlen($reason) < 5) {
            return $this->_fail(400, 'reason must be at least 5 characters');
        }

        $old = $this->db->query(
            "SELECT apst FROM init_call WHERE id = ? LIMIT 1",
            array($lead_id)
        )->row_array();
        $old_pst = $old ? (int)$old['apst'] : 0;

        $this->db->query("UPDATE init_call SET apst = ? WHERE id = ?", array($new_pst_id, $lead_id));

        $this->_json(array('ok' => true, 'lead_id' => $lead_id, 'old_pst_id' => $old_pst, 'new_pst_id' => $new_pst_id));
    }

    public function conversions() {
        $from = $this->input->get('from') ?: date('Y-m-01');
        $to   = $this->input->get('to')   ?: date('Y-m-d');
        if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $from)) $from = date('Y-m-01');
        if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $to))   $to   = date('Y-m-d');

        // funnel_transfer_log exists on staging (noted in context)
        $has_ftl = $this->db->table_exists('funnel_transfer_log');
        if ($has_ftl) {
            $sql = "SELECT ic.id AS lead_id, cm.compname AS school_name, cm.city,
                        ic.fbudget, ic.apst, ic.pstadt, ic.cstatus,
                        up.name AS pst_name,
                        ub.name AS bd_name,
                        ftl.created_at AS conversion_date
                    FROM init_call ic
                    LEFT JOIN company_master cm ON cm.id = ic.cmpid_id
                    LEFT JOIN funnel_transfer_log ftl
                        ON ftl.cid = ic.id AND ftl.new_status = 12
                        AND DATE(ftl.created_at) BETWEEN ? AND ?
                    LEFT JOIN user up ON up.uid = ic.apst
                    LEFT JOIN user ub ON ub.uid = ic.mainbd
                    WHERE ic.cstatus = 12 AND ic.apst > 0
                      AND DATE(ftl.created_at) BETWEEN ? AND ?
                    ORDER BY ftl.created_at DESC LIMIT 300";
            $rows = $this->db->query($sql, array($from, $to, $from, $to))->result_array();
        } else {
            $sql = "SELECT ic.id AS lead_id, cm.compname AS school_name, cm.city,
                        ic.fbudget, ic.apst, ic.pstadt, ic.cstatus,
                        up.name AS pst_name, ub.name AS bd_name
                    FROM init_call ic
                    LEFT JOIN company_master cm ON cm.id = ic.cmpid_id
                    LEFT JOIN user up ON up.uid = ic.apst
                    LEFT JOIN user ub ON ub.uid = ic.mainbd
                    WHERE ic.cstatus = 12 AND ic.apst > 0
                    ORDER BY ic.updated_at DESC LIMIT 300";
            $rows = $this->db->query($sql)->result_array();
        }
        $total_rs = 0;
        foreach ($rows as $r) {
            $total_rs += (float)str_replace(',', '', $r['fbudget'] ?? 0);
        }

        $this->_json(array('ok' => true, 'from' => $from, 'to' => $to,
            'total_count' => count($rows), 'total_rs' => $total_rs, 'conversions' => $rows));
    }
}

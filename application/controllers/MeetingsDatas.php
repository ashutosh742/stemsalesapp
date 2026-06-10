<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * STEM CRM v2.8 - Meetings: No RP / New / Barge classification.
 *   actiontype_id 4  -> barge-in meeting
 *   actiontype_id 3  -> review/regular meeting
 *   actiontype_id 10 -> research call
 *
 * Returns last 30 days of meetings by BD with a tag column.
 */
class MeetingsDatas extends CI_Controller {
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
            ->set_output(json_encode(['ok' => true, 'controller' => 'MeetingsDatas']));
    }

    public function no_rp_new_barg() {
        $this->output->set_content_type('application/json');
        $auth = $this->input->get_request_header('Authorization', TRUE);
        if (!$auth || strpos($auth, 'Bearer ') !== 0) {
            http_response_code(401);
            echo json_encode(['ok' => false, 'message' => 'Unauthorized']);
            return;
        }
        $bd_uid = (int) $this->input->get('bd_uid', TRUE);
        $from   = date('Y-m-d', strtotime('-30 days'));

        $sql = "SELECT t.id AS task_id,
                       t.date,
                       t.user_id AS bd_uid,
                       u.name    AS bd_name,
                       t.cid_id,
                       cm.compname AS company_name,
                       t.actiontype_id,
                       t.purpose_id,
                       t.mom_received,
                       t.mom_approved,
                       CASE
                          WHEN t.actiontype_id = 4 AND t.purpose_id = 66 THEN 'barge'
                          WHEN t.actiontype_id = 10                    THEN 'research'
                          WHEN t.actiontype_id IN (1)                  THEN 'new_lead'
                          WHEN t.actiontype_id IN (3,4)                THEN 'meeting'
                          ELSE 'other'
                       END AS tag
                FROM tblcallevents t
                LEFT JOIN user u             ON u.uid = t.user_id
                LEFT JOIN init_call ic       ON ic.id = t.cid_id
                LEFT JOIN company_master cm  ON cm.id = ic.cmpid_id
                WHERE DATE(t.date) >= ?
                  AND t.actiontype_id IN (1, 3, 4, 10) ";
        $bind = [$from];
        if ($bd_uid > 0) { $sql .= " AND t.user_id = ? "; $bind[] = $bd_uid; }
        $sql .= " ORDER BY t.date DESC LIMIT 500";

        $q = $this->db->query($sql, $bind);
        echo json_encode(['ok' => true, 'rows' => $q ? $q->result_array() : []]);
    }
}

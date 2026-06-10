<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * STEM CRM v2.8 - Funnel transfer log listing (sno 56).
 * Real table: funnel_transfer_log(id, cid, from_uid, to_uid, by_uid, remarks,
 *                                  old_status, new_status, created_at)
 */
class FunnelTransferLogs extends CI_Controller {
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
            ->set_output(json_encode(['ok' => true, 'controller' => 'FunnelTransferLogs']));
    }

    public function list_logs() {
        $this->output->set_content_type('application/json');
        $auth = $this->input->get_request_header('Authorization', TRUE);
        if (!$auth || strpos($auth, 'Bearer ') !== 0) {
            http_response_code(401);
            echo json_encode(['ok' => false, 'message' => 'Unauthorized']);
            return;
        }
        $bd_uid = (int) $this->input->get('bd_uid', TRUE);
        $from   = date('Y-m-d', strtotime('-90 days'));

        $sql = "SELECT
                    f.id, f.cid, f.from_uid, f.to_uid, f.by_uid,
                    f.old_status, f.new_status, f.remarks, f.created_at,
                    cm.compname AS company_name,
                    uf.name AS from_name,
                    ut.name AS to_name,
                    ub.name AS by_name
                FROM funnel_transfer_log f
                LEFT JOIN init_call ic  ON ic.id  = f.cid
                LEFT JOIN company_master cm ON cm.id = ic.cmpid_id
                LEFT JOIN user uf ON uf.uid = f.from_uid
                LEFT JOIN user ut ON ut.uid = f.to_uid
                LEFT JOIN user ub ON ub.uid = f.by_uid
                WHERE DATE(f.created_at) >= ? ";
        $bind = [$from];
        if ($bd_uid > 0) { $sql .= " AND (f.from_uid = ? OR f.to_uid = ? OR f.by_uid = ?) "; $bind[] = $bd_uid; $bind[] = $bd_uid; $bind[] = $bd_uid; }
        $sql .= " ORDER BY f.created_at DESC LIMIT 500";

        $q = $this->db->query($sql, $bind);
        echo json_encode(['ok' => true, 'rows' => $q ? $q->result_array() : []]);
    }
}

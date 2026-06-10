<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * STEM CRM v2.8 - MoM status summary.
 * Group mom_data rows by user_id and bucket on approved_status:
 *   pending (NULL/empty), approved ('1'/'approved'/'Yes'),
 *   rejected ('0'/'rejected'/'No'), submitted (anything else not null).
 */
class MomStatusV28 extends CI_Controller {
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
            ->set_output(json_encode(['ok' => true, 'controller' => 'MomStatusV28']));
    }

    public function status_summary() {
        $auth = $this->input->get_request_header('Authorization', TRUE);
        if (!$auth || strpos($auth, 'Bearer ') !== 0) {
            http_response_code(401);
            echo json_encode(['ok' => false, 'message' => 'Unauthorized']);
            return;
        }
        $bd_uid = (int) $this->input->get('bd_uid', TRUE);

        $sql = "SELECT
                    m.user_id                                   AS bd_uid,
                    u.name                                      AS bd_name,
                    COUNT(*)                                    AS total_mom,
                    SUM(CASE WHEN m.approved_status IS NULL OR m.approved_status = '' THEN 1 ELSE 0 END) AS pending_cnt,
                    SUM(CASE WHEN m.approved_status IN ('1','approved','Yes') THEN 1 ELSE 0 END)         AS approved_cnt,
                    SUM(CASE WHEN m.approved_status IN ('0','rejected','No')  THEN 1 ELSE 0 END)         AS rejected_cnt
                FROM mom_data m
                LEFT JOIN user u ON u.uid = m.user_id ";
        $bind = [];
        if ($bd_uid > 0) { $sql .= " WHERE m.user_id = ? "; $bind[] = $bd_uid; }
        $sql .= " GROUP BY m.user_id ORDER BY total_mom DESC LIMIT 500";

        $q = $this->db->query($sql, $bind);
        $rows = $q ? $q->result_array() : [];
        foreach ($rows as &$r) {
            foreach (['bd_uid','total_mom','pending_cnt','approved_cnt','rejected_cnt'] as $k) {
                $r[$k] = (int) $r[$k];
            }
        }
        echo json_encode(['ok' => true, 'rows' => $rows]);
    }
}

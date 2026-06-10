<?php
defined('BASEPATH') OR exit('No direct script access allowed');
/**
 * District_intel - M057
 * /api/district_intel/probe    - liveness check
 * /api/district_intel/summary  - district-level lead/user summary
 */
class District_intel extends CI_Controller {

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

    // GET /api/district_intel/probe
    public function probe() {
        $this->_out([
            'ok'          => true,
            'controller'  => 'District_intel',
            'migration'   => 'M057',
            'status'      => 'ready',
            'server_time' => date('c'),
        ]);
    }

    // GET /api/district_intel/summary
    // Returns distinct base_cluster (district) values with user count and lead count.
    public function summary() {
        try {
            // District breakdown from user_details
            $districts = $this->db->query(
                "SELECT ud.base_cluster AS district,
                        COUNT(DISTINCT ud.user_id) AS bd_count,
                        ud.zone_id
                 FROM user_details ud
                 WHERE ud.base_cluster IS NOT NULL AND ud.base_cluster != ''
                 GROUP BY ud.base_cluster, ud.zone_id
                 ORDER BY bd_count DESC
                 LIMIT 100"
            )->result_array();

            // Lead count per BD cluster (approximate via mainbd join)
            $lead_counts = $this->db->query(
                "SELECT ud.base_cluster AS district,
                        COUNT(ic.id) AS lead_count,
                        SUM(CASE WHEN ic.cstatus >= 5 THEN 1 ELSE 0 END) AS advanced_leads
                 FROM init_call ic
                 LEFT JOIN user_details ud ON ud.user_id = ic.mainbd
                 WHERE ud.base_cluster IS NOT NULL AND ud.base_cluster != ''
                 GROUP BY ud.base_cluster
                 ORDER BY lead_count DESC
                 LIMIT 100"
            )->result_array();

            $this->_out([
                'ok'          => true,
                'districts'   => $districts,
                'lead_counts' => $lead_counts,
                'count'       => count($districts),
            ]);
        } catch (Exception $e) {
            $this->_out(['ok' => true, 'rows' => [], 'note' => 'error', 'detail' => $e->getMessage()]);
        }
    }
}

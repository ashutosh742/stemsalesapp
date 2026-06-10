<?php
defined('BASEPATH') OR exit('No direct script access allowed');
/**
 * Huddle - M035
 * /api/huddle/today  - returns last 20 MoMs (huddle-style meeting summary)
 * /api/huddle/probe  - liveness check
 */
class Huddle extends CI_Controller {

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

    // GET /api/huddle/probe
    public function probe() {
        $this->_out([
            'ok'          => true,
            'controller'  => 'Huddle',
            'migration'   => 'M035',
            'status'      => 'ready',
            'server_time' => date('c'),
        ]);
    }

    // GET /api/huddle/today
    // Returns MoM records created today (or last 20 if none today).
    public function today() {
        try {
            // Try today first
            $rows = $this->db->query(
                "SELECT md.id, md.init_cmpid AS lead_id, md.user_id, md.approved_status,
                        md.cdate, u.name AS bd_name, cm.compname AS school
                 FROM mom_data md
                 LEFT JOIN user_details u  ON u.user_id = md.user_id
                 LEFT JOIN init_call ic    ON ic.id = md.init_cmpid
                 LEFT JOIN company_master cm ON cm.id = ic.cmpid_id
                 WHERE DATE(md.cdate) = CURDATE()
                 ORDER BY md.cdate DESC
                 LIMIT 50"
            )->result_array();

            $note = 'today';
            if (count($rows) === 0) {
                // Fall back to last 20 moms
                $rows = $this->db->query(
                    "SELECT md.id, md.init_cmpid AS lead_id, md.user_id, md.approved_status,
                            md.cdate, u.name AS bd_name, cm.compname AS school
                     FROM mom_data md
                     LEFT JOIN user_details u  ON u.user_id = md.user_id
                     LEFT JOIN init_call ic    ON ic.id = md.init_cmpid
                     LEFT JOIN company_master cm ON cm.id = ic.cmpid_id
                     ORDER BY md.cdate DESC
                     LIMIT 20"
                )->result_array();
                $note = 'fallback_last_20';
            }

            $this->_out([
                'ok'    => true,
                'date'  => date('Y-m-d'),
                'note'  => $note,
                'rows'  => $rows,
                'count' => count($rows),
            ]);
        } catch (Exception $e) {
            $this->_out(['ok' => true, 'rows' => [], 'note' => 'error', 'detail' => $e->getMessage()]);
        }
    }
}

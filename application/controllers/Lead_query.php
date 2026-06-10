<?php
defined('BASEPATH') OR exit('No direct script access allowed');
/**
 * Lead_query - M026
 * /api/lead_query/checklist?days=7  - leads missing DM contact / proposal / MoM in last N days
 * /api/lead_query/probe             - liveness check
 *
 * NOTE: LeadQueryController.php already exists for CRM query tickets.
 * This controller handles the APK checklist endpoint (different path).
 */
class Lead_query extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->database();
        header('Content-Type: application/json');
    }

    private function _out($p) { echo json_encode($p); exit; }

    // GET /api/lead_query/probe
    public function probe() {
        $this->_out([
            'ok'          => true,
            'controller'  => 'Lead_query',
            'migration'   => 'M026',
            'status'      => 'ready',
            'server_time' => date('c'),
        ]);
    }

    // GET /api/lead_query/checklist?days=7
    // Returns active leads with no MoM in the last N days (optimized JOIN version).
    public function checklist() {
        try {
            // rimlyproof_checklist_auth_20260609: this endpoint returned org-wide lead
            // rows (lead_id, company, BD name, proposal_amt) with NO auth. This
            // constructor does not load any auth lib, so gate here using the ONE shared
            // validator: a valid master/digest token OR a valid per-user login token.
            $_authok = function_exists('authunify_ok') ? authunify_ok() : false;
            if (!$_authok) {
                $this->load->library('Bearer_auth');
                $_authok = $this->bearer_auth->verify($this->bearer_auth->get_bearer_token());
            }
            if (!$_authok) {
                header('Content-Type: application/json');
                http_response_code(401);
                echo json_encode(array('ok' => false, 'error' => 'unauthorized'));
                exit;
            }
            $days = max(1, min(90, (int)($this->input->get('days') ?: 7)));
            $cutoff = date('Y-m-d H:i:s', strtotime("-$days days"));

            // Use LEFT JOIN + GROUP BY to find max cdate per lead efficiently
            $rows = $this->db->query(
                "SELECT ic.id AS lead_id,
                        ic.cstatus,
                        ic.createDate,
                        ic.proposal_amt,
                        ic.apst,
                        cm.compname AS school,
                        u.name AS bd_name,
                        MAX(md.cdate) AS last_mom_date,
                        DATEDIFF(NOW(), MAX(md.cdate)) AS days_since_mom
                 FROM init_call ic
                 LEFT JOIN company_master cm ON cm.id = ic.cmpid_id
                 LEFT JOIN user_details u    ON u.user_id = ic.mainbd
                 LEFT JOIN mom_data md       ON md.init_cmpid = ic.id
                 WHERE ic.cstatus BETWEEN 2 AND 10
                 GROUP BY ic.id, ic.cstatus, ic.createDate, ic.proposal_amt, ic.apst,
                          cm.compname, u.name
                 HAVING (last_mom_date IS NULL OR last_mom_date < ?)
                 ORDER BY ic.createDate DESC
                 LIMIT 100",
                [$cutoff]
            )->result_array();

            $this->_out([
                'ok'    => true,
                'days'  => $days,
                'note'  => 'leads_with_no_recent_mom',
                'rows'  => $rows,
                'count' => count($rows),
            ]);
        } catch (Exception $e) {
            $this->_out(['ok' => true, 'rows' => [], 'note' => 'error', 'detail' => $e->getMessage()]);
        }
    }
}

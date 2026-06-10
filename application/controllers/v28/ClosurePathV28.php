<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * ClosurePathV28 Controller
 *
 * Routes:
 *   GET /api/closure_path/aging
 *   GET /api/closure_path/anchor_renewals
 *   GET /api/closure_path/blockers
 *   GET /api/closure_path/probe
 *
 * Real tables: init_call, stuck_leads_daily, company_master, user
 * cstatus enum: 1=Open, 2=Reachout, 3=Tentative, 6=Positive, 8=Open RPEM, 9=Very Positive, 12=Won, 13=Lost
 */
class ClosurePathV28 extends CI_Controller {

    private $token = '4eBaiAT7r4zu6OK3b8evjLNia1D7RGgb0qRTuLJfUSo';

    public function __construct()
    {
        parent::__construct();
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Headers: Authorization, Content-Type');
        $this->output->set_content_type('application/json');
    }

    private function auth()
    {
        if (function_exists('authunify_ok') && authunify_ok()) { return true; } // rimlyproof_authunify_20260609

        $h = $this->input->get_request_header('Authorization', TRUE);
        if (!$h || trim(str_replace('Bearer', '', $h)) !== $this->token) {
            $this->json_out(['ok' => false, 'error' => 'unauthorized'], 401);
            return false;
        }
        return true;
    }

    private function json_out($data, $status = 200)
    {
        $this->output->set_status_header($status)
                     ->set_output(json_encode($data, JSON_UNESCAPED_UNICODE));
    }

    /**
     * GET /api/closure_path/aging
     * Leads in active stages (not Won/Lost), sorted by age descending.
     */
    public function aging()
    {
        if (!$this->auth()) return;
        $rows = $this->db->select('i.id AS lead_id, c.compname, i.cstatus, i.fbudget,
                                   u.name AS bd_name,
                                   DATEDIFF(NOW(), i.createDate) AS age_days,
                                   i.createDate')
                         ->from('init_call i')
                         ->join('company_master c', 'c.id = i.cmpid_id', 'left')
                         ->join('user u', 'u.uid = i.mainbd', 'left')
                         ->where_not_in('i.cstatus', [12, 13])
                         ->order_by('age_days', 'DESC')
                         ->limit(100)
                         ->get()->result_array();
        $this->json_out(['ok' => true, 'success' => true, 'rows' => $rows, 'count' => count($rows)]);
    }

    /**
     * GET /api/closure_path/anchor_renewals
     * Won leads (cstatus=12) - these are the anchor accounts eligible for renewal.
     */
    public function anchor_renewals()
    {
        if (!$this->auth()) return;
        $rows = $this->db->select('i.id AS lead_id, c.compname, i.fbudget, i.closure_pipeline,
                                   u.name AS bd_name, i.updated_at AS won_date')
                         ->from('init_call i')
                         ->join('company_master c', 'c.id = i.cmpid_id', 'left')
                         ->join('user u', 'u.uid = i.mainbd', 'left')
                         ->where('i.cstatus', 12)
                         ->order_by('i.updated_at', 'DESC')
                         ->limit(100)
                         ->get()->result_array();
        $this->json_out(['ok' => true, 'success' => true, 'rows' => $rows, 'count' => count($rows),
                         'note' => 'cstatus 12 = Won accounts eligible for renewal']);
    }

    /**
     * GET /api/closure_path/blockers
     * Leads stuck beyond threshold using stuck_leads_daily (today or latest date).
     */
    public function blockers()
    {
        if (!$this->auth()) return;
        $latest = $this->db->select_max('for_date')->from('stuck_leads_daily')->get()->row_array();
        $date   = $latest['for_date'] ?? date('Y-m-d');
        // Simple query: show all stuck leads for date where days_in_stage >= threshold_days
        $rows = $this->db->select('s.cid_id AS lead_id, c.compname, s.bd_uid, u.name AS bd_name,
                                   s.cstatus, s.days_in_stage, s.threshold_days, s.last_touch_date')
                         ->from('stuck_leads_daily s')
                         ->join('init_call i', 'i.id = s.cid_id', 'left')
                         ->join('company_master c', 'c.id = i.cmpid_id', 'left')
                         ->join('user u', 'u.uid = s.bd_uid', 'left')
                         ->where('s.for_date', $date)
                         ->where('s.days_in_stage >= s.threshold_days', null, false)
                         ->order_by('s.days_in_stage', 'DESC')
                         ->limit(100)
                         ->get()->result_array();
        $this->json_out(['ok' => true, 'success' => true, 'as_of' => $date, 'rows' => $rows, 'count' => count($rows)]);
    }

    /**
     * GET /api/closure_path/probe
     */
    public function probe()
    {
        $this->json_out(['ok' => true, 'success' => true, 'note' => 'ClosurePathV28 online']);
    }
}

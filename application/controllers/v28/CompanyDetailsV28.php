<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * CompanyDetailsV28 Controller
 *
 * Routes:
 *   GET /api/company_details/current_fy
 *   GET /api/company_details/get/(:num)
 *   GET /api/company_details/history
 *   GET /api/company_details/profile
 *   GET /api/company_details/profile/(:num)
 *
 * Real table: company_master (id, compname, createddate)
 * Joins: init_call for pipeline/revenue context.
 */
class CompanyDetailsV28 extends CI_Controller {

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

    private function fy_start()
    {
        $month = (int) date('n');
        $year  = (int) date('Y');
        if ($month >= 4) {
            return $year . '-04-01';
        }
        return ($year - 1) . '-04-01';
    }

    /**
     * GET /api/company_details/current_fy
     * Summary of companies added in the current financial year.
     */
    public function current_fy()
    {
        if (!$this->auth()) return;
        $fy_start = $this->fy_start();
        $rows = $this->db->select('c.id, c.compname, c.createddate,
                                   COUNT(DISTINCT i.id) AS total_leads,
                                   MAX(i.cstatus) AS latest_cstatus')
                         ->from('company_master c')
                         ->join('init_call i', 'i.cmpid_id = c.id', 'left')
                         ->where('c.createddate >=', $fy_start)
                         ->group_by('c.id')
                         ->order_by('c.createddate', 'DESC')
                         ->limit(100)
                         ->get()->result_array();
        $this->json_out(['ok' => true, 'success' => true, 'fy_start' => $fy_start, 'rows' => $rows, 'count' => count($rows)]);
    }

    /**
     * GET /api/company_details/get/(:num)
     * Details for a specific company by id.
     */
    public function get($id = 0)
    {
        if (!$this->auth()) return;
        $id = (int) $id;
        if ($id <= 0) {
            $this->json_out(['ok' => false, 'error' => 'id required'], 400);
            return;
        }
        $row = $this->db->select('c.id, c.compname, c.createddate,
                                  COUNT(DISTINCT i.id) AS total_leads,
                                  SUM(i.fbudget) AS total_budget_rs')
                        ->from('company_master c')
                        ->join('init_call i', 'i.cmpid_id = c.id', 'left')
                        ->where('c.id', $id)
                        ->group_by('c.id')
                        ->limit(1)
                        ->get()->row_array();
        if (!$row) {
            $this->json_out(['ok' => false, 'error' => 'company not found'], 404);
            return;
        }
        $this->json_out(['ok' => true, 'success' => true, 'data' => $row]);
    }

    /**
     * GET /api/company_details/history?id=<cid>
     * Call event history for a company.
     */
    public function history()
    {
        if (!$this->auth()) return;
        $cid = (int) $this->input->get('id');
        if ($cid <= 0) {
            $this->json_out(['ok' => false, 'error' => 'id required'], 400);
            return;
        }
        $rows = $this->db->select('e.id, e.date, e.actiontype_id, e.purpose_id, e.approved_status,
                                   e.plan_time, e.initiate_time, e.complete_time,
                                   u.name AS bd_name')
                         ->from('tblcallevents e')
                         ->join('user u', 'u.uid = e.user_id', 'left')
                         ->where('e.cid_id', $cid)
                         ->order_by('e.date', 'DESC')
                         ->limit(100)
                         ->get()->result_array();
        $this->json_out(['ok' => true, 'success' => true, 'cid_id' => $cid, 'rows' => $rows, 'count' => count($rows)]);
    }

    /**
     * GET /api/company_details/profile
     * List all companies (paginated, 50 per page).
     */
    public function profile($id = null)
    {
        if (!$this->auth()) return;
        if ($id !== null) {
            $this->profile_by_id((int) $id);
            return;
        }
        $page   = max(1, (int) ($this->input->get('page') ?? 1));
        $offset = ($page - 1) * 50;
        $rows = $this->db->select('c.id, c.compname, c.createddate,
                                   COUNT(DISTINCT i.id) AS total_leads')
                         ->from('company_master c')
                         ->join('init_call i', 'i.cmpid_id = c.id', 'left')
                         ->group_by('c.id')
                         ->order_by('c.id', 'DESC')
                         ->limit(50, $offset)
                         ->get()->result_array();
        $total = $this->db->count_all('company_master');
        $this->json_out(['ok' => true, 'success' => true, 'page' => $page, 'total' => $total, 'rows' => $rows, 'count' => count($rows)]);
    }

    private function profile_by_id($id)
    {
        if ($id <= 0) {
            $this->json_out(['ok' => false, 'error' => 'id required'], 400);
            return;
        }
        $row = $this->db->select('c.id, c.compname, c.createddate')
                        ->from('company_master c')
                        ->where('c.id', $id)
                        ->limit(1)
                        ->get()->row_array();
        if (!$row) {
            $this->json_out(['ok' => false, 'error' => 'company not found'], 404);
            return;
        }
        $leads = $this->db->select('i.id, i.cstatus, i.fbudget, i.closure_pipeline, i.createDate, u.name AS bd_name')
                          ->from('init_call i')
                          ->join('user u', 'u.uid = i.mainbd', 'left')
                          ->where('i.cmpid_id', $id)
                          ->order_by('i.createDate', 'DESC')
                          ->limit(20)
                          ->get()->result_array();
        $row['leads'] = $leads;
        $this->json_out(['ok' => true, 'success' => true, 'data' => $row]);
    }
}

<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * LeadsV28 Controller
 *
 * Routes:
 *   GET /api/leads/all
 *   GET /api/leads/by_bd
 *   GET /api/leads/my
 *
 * Real table: init_call (lead), company_master, user
 * init_call columns: id, cmpid_id, mainbd, cstatus, createDate, updated_at, fbudget, closure_pipeline
 * cstatus: 1=Open, 2=Reachout, 3=Tentative, 6=Positive, 8=Open RPEM, 9=Very Positive, 12=Won, 13=Lost
 */
class LeadsV28 extends CI_Controller {

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

    private function base_query()
    {
        return $this->db->select('i.id AS lead_id, c.compname, i.cstatus, i.fbudget,
                                  i.closure_pipeline, i.mainbd AS bd_uid,
                                  u.name AS bd_name, i.createDate, i.updated_at')
                        ->from('init_call i')
                        ->join('company_master c', 'c.id = i.cmpid_id', 'left')
                        ->join('user u', 'u.uid = i.mainbd', 'left');
    }

    /**
     * GET /api/leads/all?cstatus=<int>&limit=<n>
     * All leads, optionally filtered by cstatus.
     */
    public function all()
    {
        if (!$this->auth()) return;
        $cstatus = $this->input->get('cstatus');
        $limit   = min(200, max(1, (int) ($this->input->get('limit') ?? 100)));

        $this->base_query();
        if ($cstatus !== false && $cstatus !== null && $cstatus !== '') {
            $this->db->where('i.cstatus', (int) $cstatus);
        }
        $rows = $this->db->order_by('i.createDate', 'DESC')
                         ->limit($limit)
                         ->get()->result_array();
        $this->json_out(['ok' => true, 'success' => true, 'rows' => $rows, 'count' => count($rows)]);
    }

    /**
     * GET /api/leads/by_bd?bd_uid=<uid>[&cstatus=<int>]
     * Leads for a specific BD user.
     */
    public function by_bd()
    {
        if (!$this->auth()) return;
        $bd_uid  = (int) $this->input->get('bd_uid');
        $cstatus = $this->input->get('cstatus');

        if ($bd_uid <= 0) {
            $this->json_out(['ok' => false, 'error' => 'bd_uid required'], 400);
            return;
        }

        $this->base_query()->where('i.mainbd', $bd_uid);
        if ($cstatus !== false && $cstatus !== null && $cstatus !== '') {
            $this->db->where('i.cstatus', (int) $cstatus);
        }
        $rows = $this->db->order_by('i.createDate', 'DESC')
                         ->limit(100)
                         ->get()->result_array();
        $this->json_out(['ok' => true, 'success' => true, 'bd_uid' => $bd_uid,
                         'rows' => $rows, 'count' => count($rows)]);
    }

    /**
     * GET /api/leads/my?uid=<uid>
     * Alias for by_bd using uid param - leads assigned to the requesting user.
     */
    public function my()
    {
        if (!$this->auth()) return;
        $uid = (int) $this->input->get('uid');
        if ($uid <= 0) {
            $this->json_out(['ok' => false, 'error' => 'uid required'], 400);
            return;
        }
        $rows = $this->base_query()
                     ->where('i.mainbd', $uid)
                     ->where_not_in('i.cstatus', [12, 13])
                     ->order_by('i.updated_at', 'DESC')
                     ->limit(100)
                     ->get()->result_array();
        $this->json_out(['ok' => true, 'success' => true, 'uid' => $uid,
                         'rows' => $rows, 'count' => count($rows),
                         'note' => 'active leads only (excludes Won and Lost)']);
    }
}

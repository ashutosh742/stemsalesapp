<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * PipelineV28 Controller
 *
 * Routes:
 *   GET /api/pipeline
 *   GET /api/pipeline/my
 *   GET /api/pipeline/summary
 *
 * Real tables: pipeline_coverage_snapshot, init_call, company_master, user
 * pipeline_coverage_snapshot: id, scope_type, scope_uid, snapshot_date,
 *   pipeline_rs, target_rs, ratio, band, captured_at
 */
class PipelineV28 extends CI_Controller {

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
     * GET /api/pipeline
     * Org-level pipeline from latest snapshot plus live lead counts by cstatus.
     */
    public function index()
    {
        if (!$this->auth()) return;
        // Latest org snapshot
        $snap = $this->db->where('scope_type', 'org')
                         ->order_by('snapshot_date', 'DESC')
                         ->limit(1)
                         ->get('pipeline_coverage_snapshot')->row_array();

        // Live breakdown by cstatus
        $live = $this->db->select('cstatus, COUNT(*) AS lead_count, SUM(fbudget) AS total_budget_rs,
                                   SUM(closure_pipeline) AS total_pipeline_rs')
                         ->from('init_call')
                         ->where_not_in('cstatus', [12, 13])
                         ->group_by('cstatus')
                         ->order_by('cstatus', 'ASC')
                         ->get()->result_array();

        $cstatus_labels = [1=>'Open',2=>'Reachout',3=>'Tentative',6=>'Positive',
                           8=>'Open RPEM',9=>'Very Positive'];
        foreach ($live as &$r) {
            $r['cstatus_label'] = $cstatus_labels[(int)$r['cstatus']] ?? 'Unknown';
        }
        unset($r);

        $this->json_out(['ok' => true, 'success' => true,
                         'latest_snapshot' => $snap ?: null,
                         'live_by_stage' => $live]);
    }

    /**
     * GET /api/pipeline/my?uid=<uid>
     * Pipeline for a specific BD user.
     */
    public function my()
    {
        if (!$this->auth()) return;
        $uid = (int) $this->input->get('uid');
        if ($uid <= 0) {
            $this->json_out(['ok' => false, 'error' => 'uid required'], 400);
            return;
        }

        // Snapshot for this user
        $snap = $this->db->where('scope_type', 'bd')
                         ->where('scope_uid', $uid)
                         ->order_by('snapshot_date', 'DESC')
                         ->limit(1)
                         ->get('pipeline_coverage_snapshot')->row_array();

        // Live leads
        $rows = $this->db->select('i.id AS lead_id, c.compname, i.cstatus, i.fbudget,
                                   i.closure_pipeline, i.updated_at')
                         ->from('init_call i')
                         ->join('company_master c', 'c.id = i.cmpid_id', 'left')
                         ->where('i.mainbd', $uid)
                         ->where_not_in('i.cstatus', [12, 13])
                         ->order_by('i.fbudget', 'DESC')
                         ->limit(100)
                         ->get()->result_array();

        $total_pipeline = array_sum(array_column($rows, 'closure_pipeline'));
        $this->json_out(['ok' => true, 'success' => true, 'uid' => $uid,
                         'snapshot' => $snap ?: null,
                         'total_pipeline_rs' => $total_pipeline,
                         'rows' => $rows, 'count' => count($rows)]);
    }

    /**
     * GET /api/pipeline/summary
     * All BD-level snapshots from the latest snapshot date.
     */
    public function summary()
    {
        if (!$this->auth()) return;
        $latest = $this->db->select_max('snapshot_date')->from('pipeline_coverage_snapshot')->get()->row_array();
        $date   = $latest['snapshot_date'] ?? date('Y-m-d');

        $rows = $this->db->select('p.scope_type, p.scope_uid, u.name AS scope_name,
                                   p.pipeline_rs, p.target_rs, p.ratio, p.band, p.snapshot_date')
                         ->from('pipeline_coverage_snapshot p')
                         ->join('user u', 'u.uid = p.scope_uid AND p.scope_type = \'bd\'', 'left')
                         ->where('p.snapshot_date', $date)
                         ->order_by('p.pipeline_rs', 'DESC')
                         ->limit(100)
                         ->get()->result_array();

        $this->json_out(['ok' => true, 'success' => true, 'snapshot_date' => $date,
                         'rows' => $rows, 'count' => count($rows)]);
    }
}

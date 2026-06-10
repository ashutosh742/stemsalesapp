<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * LeaderboardV28 Controller
 *
 * Routes:
 *   GET /api/leaderboard/critical_gaps
 *   GET /api/leaderboard/monthly
 *   GET /api/leaderboard/war_points
 *
 * Real tables: bd_productivity_daily, stuck_leads_daily, init_call, user
 * Note: no dedicated leaderboard table found; built from bd_productivity_daily.
 */
class LeaderboardV28 extends CI_Controller {

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
     * GET /api/leaderboard/monthly?month=YYYY-MM
     * Monthly BD leaderboard ranked by average score_pct.
     */
    public function monthly()
    {
        if (!$this->auth()) return;
        $month = $this->input->get('month');
        if (!$month || !preg_match('/^\d{4}-\d{2}$/', $month)) {
            $month = date('Y-m');
        }
        $month_start = $month . '-01';
        $month_end   = date('Y-m-t', strtotime($month_start));

        $rows = $this->db->select('p.bd_uid, u.name AS bd_name,
                                   AVG(p.score_pct) AS avg_score_pct,
                                   SUM(p.executed_min) AS total_executed_min,
                                   SUM(p.tasks_completed) AS total_tasks_done,
                                   COUNT(*) AS days_scored')
                         ->from('bd_productivity_daily p')
                         ->join('user u', 'u.uid = p.bd_uid', 'left')
                         ->where('p.for_date >=', $month_start)
                         ->where('p.for_date <=', $month_end)
                         ->group_by('p.bd_uid')
                         ->order_by('avg_score_pct', 'DESC')
                         ->limit(50)
                         ->get()->result_array();

        // Add rank
        foreach ($rows as $i => &$r) {
            $r['rank'] = $i + 1;
        }
        unset($r);

        $this->json_out(['ok' => true, 'success' => true, 'month' => $month,
                         'rows' => $rows, 'count' => count($rows)]);
    }

    /**
     * GET /api/leaderboard/war_points
     * War points: BDs ranked by Won leads (cstatus=12) + pipeline value in current FY.
     */
    public function war_points()
    {
        if (!$this->auth()) return;
        $month = (int) date('n');
        $year  = (int) date('Y');
        $fy_start = (($month >= 4) ? $year : ($year - 1)) . '-04-01';

        $rows = $this->db->select('i.mainbd AS bd_uid, u.name AS bd_name,
                                   SUM(CASE WHEN i.cstatus = 12 THEN 1 ELSE 0 END) AS wins,
                                   SUM(CASE WHEN i.cstatus NOT IN (12,13) THEN 1 ELSE 0 END) AS active_leads,
                                   SUM(i.fbudget) AS total_budget_rs,
                                   SUM(i.closure_pipeline) AS pipeline_rs')
                         ->from('init_call i')
                         ->join('user u', 'u.uid = i.mainbd', 'left')
                         ->where('i.createDate >=', $fy_start)
                         ->group_by('i.mainbd')
                         ->order_by('wins', 'DESC')
                         ->limit(50)
                         ->get()->result_array();

        foreach ($rows as $i => &$r) {
            $r['rank']       = $i + 1;
            $r['war_points'] = ((int)($r['wins'] ?? 0) * 10) + (int)($r['active_leads'] ?? 0);
        }
        unset($r);

        $this->json_out(['ok' => true, 'success' => true, 'fy_start' => $fy_start,
                         'rows' => $rows, 'count' => count($rows)]);
    }

    /**
     * GET /api/leaderboard/critical_gaps
     * BDs with the lowest scores this month - bottom quartile.
     */
    public function critical_gaps()
    {
        if (!$this->auth()) return;
        $month_start = date('Y-m') . '-01';
        $month_end   = date('Y-m-t');

        $rows = $this->db->select('p.bd_uid, u.name AS bd_name,
                                   AVG(p.score_pct) AS avg_score_pct,
                                   MIN(p.score_pct) AS min_score_pct,
                                   SUM(p.idle_min) AS total_idle_min,
                                   SUM(p.tasks_skipped) AS total_tasks_skipped')
                         ->from('bd_productivity_daily p')
                         ->join('user u', 'u.uid = p.bd_uid', 'left')
                         ->where('p.for_date >=', $month_start)
                         ->where('p.for_date <=', $month_end)
                         ->group_by('p.bd_uid')
                         ->having('avg_score_pct <', 50)
                         ->order_by('avg_score_pct', 'ASC')
                         ->limit(20)
                         ->get()->result_array();

        $this->json_out(['ok' => true, 'success' => true, 'month' => date('Y-m'),
                         'rows' => $rows, 'count' => count($rows),
                         'note' => 'BDs with avg score below 50 percent this month']);
    }
}

<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * EfficiencyV28 Controller
 *
 * Routes:
 *   GET /api/efficiency/api_get_bd_score
 *   GET /api/efficiency/api_get_cluster_rollup
 *   GET /api/efficiency/probe
 *
 * Real tables: route_efficiency_score, bd_productivity_daily, user
 * route_efficiency_score columns: id, bd_uid, score_date, planned_stops, executed_stops,
 *   meeting_minutes_actual, drive_minutes_actual, slack_minutes_actual,
 *   efficiency_actual_pct, efficiency_delta_pct, quality_grade
 */
class EfficiencyV28 extends CI_Controller {

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
     * GET /api/efficiency/api_get_bd_score?bd_uid=<uid>[&date=YYYY-MM-DD]
     * Returns route efficiency score for a BD on a given date.
     * Falls back to bd_productivity_daily if route_efficiency_score is empty.
     */
    public function api_get_bd_score()
    {
        if (!$this->auth()) return;
        $bd_uid = (int) $this->input->get('bd_uid');
        if ($bd_uid <= 0) {
            $this->json_out(['ok' => false, 'error' => 'bd_uid required'], 400);
            return;
        }
        $d_raw = $this->input->get('date');
        $date  = ($d_raw && preg_match('/^\d{4}-\d{2}-\d{2}$/', $d_raw)) ? $d_raw : date('Y-m-d');

        // Try route_efficiency_score first
        $row = $this->db->where('bd_uid', $bd_uid)
                        ->where('score_date', $date)
                        ->limit(1)
                        ->get('route_efficiency_score')->row_array();

        if (!$row) {
            // Fallback to bd_productivity_daily
            $row = $this->db->where('bd_uid', $bd_uid)
                            ->where('for_date', $date)
                            ->limit(1)
                            ->get('bd_productivity_daily')->row_array();
            if (!$row) {
                $this->json_out(['ok' => true, 'success' => true, 'rows' => [], 'count' => 0,
                                 'note' => 'no_data', 'date' => $date, 'bd_uid' => $bd_uid]);
                return;
            }
            $this->json_out(['ok' => true, 'success' => true, 'source' => 'bd_productivity_daily', 'data' => $row]);
            return;
        }

        $this->json_out(['ok' => true, 'success' => true, 'source' => 'route_efficiency_score', 'data' => $row]);
    }

    /**
     * GET /api/efficiency/api_get_cluster_rollup?admin_id=<uid>[&date=YYYY-MM-DD]
     * Rollup of bd_productivity_daily scores for all BDs under a given admin/manager.
     */
    public function api_get_cluster_rollup()
    {
        if (!$this->auth()) return;
        $admin_id = (int) $this->input->get('admin_id');
        $d_raw    = $this->input->get('date');
        $date     = ($d_raw && preg_match('/^\d{4}-\d{2}-\d{2}$/', $d_raw)) ? $d_raw : date('Y-m-d');

        if ($admin_id <= 0) {
            // No filter - return team-wide rollup
            $rows = $this->db->select('p.bd_uid, u.name AS bd_name,
                                       p.for_date, p.planned_min, p.executed_min,
                                       p.score_pct, p.tasks_planned, p.tasks_completed')
                             ->from('bd_productivity_daily p')
                             ->join('user u', 'u.uid = p.bd_uid', 'left')
                             ->where('p.for_date', $date)
                             ->order_by('p.score_pct', 'DESC')
                             ->limit(50)
                             ->get()->result_array();
        } else {
            $rows = $this->db->select('p.bd_uid, u.name AS bd_name,
                                       p.for_date, p.planned_min, p.executed_min,
                                       p.score_pct, p.tasks_planned, p.tasks_completed')
                             ->from('bd_productivity_daily p')
                             ->join('user u', 'u.uid = p.bd_uid', 'left')
                             ->where('p.for_date', $date)
                             ->where('u.admin_id', $admin_id)
                             ->order_by('p.score_pct', 'DESC')
                             ->limit(50)
                             ->get()->result_array();
        }

        $avg_score = count($rows) ? round(array_sum(array_column($rows, 'score_pct')) / count($rows), 2) : 0;
        $this->json_out(['ok' => true, 'success' => true, 'date' => $date, 'admin_id' => $admin_id,
                         'avg_score_pct' => $avg_score, 'rows' => $rows, 'count' => count($rows)]);
    }

    /**
     * GET /api/efficiency/probe
     */
    public function probe()
    {
        $this->json_out(['ok' => true, 'success' => true, 'note' => 'EfficiencyV28 online']);
    }

    /**
     * POST /api/efficiency/api_tag_outcome
     * Body: { planner_id, outcome, notes? }
     * Persists an outcome tag for a planner task into efficiency_outcome_tag.
     * Allowed outcomes: met, no_show, rescheduled, cancelled, pending.
     */
    public function api_tag_outcome()
    {
        if (!$this->auth()) return;

        $raw  = file_get_contents('php://input');
        $body = json_decode($raw, TRUE);
        if (!is_array($body)) $body = $_POST;

        $planner_id = isset($body['planner_id']) ? (int) $body['planner_id'] : 0;
        $outcome    = isset($body['outcome']) ? trim((string) $body['outcome']) : '';
        $notes      = isset($body['notes']) ? trim((string) $body['notes']) : '';

        if ($planner_id <= 0) {
            $this->json_out(['ok' => false, 'error' => 'planner_id required'], 400);
            return;
        }
        $allowed = ['met', 'no_show', 'rescheduled', 'cancelled', 'pending'];
        if ($outcome === '' || !in_array($outcome, $allowed, TRUE)) {
            $this->json_out(['ok' => false, 'error' => 'invalid outcome',
                             'allowed' => $allowed], 422);
            return;
        }

        // Derive bd_uid from the daily_planner row if available (best effort, never fatal).
        $bd_uid = 0;
        $p = $this->db->select('userID')->where('id', $planner_id)
                      ->limit(1)->get('daily_planner')->row_array();
        if ($p && isset($p['userID']) && is_numeric($p['userID'])) {
            $bd_uid = (int) $p['userID'];
        }

        $now = date('Y-m-d H:i:s');
        $this->db->insert('efficiency_outcome_tag', [
            'planner_id' => $planner_id,
            'bd_uid'     => $bd_uid,
            'outcome'    => $outcome,
            'notes'      => ($notes === '' ? NULL : $notes),
            'tagged_at'  => $now,
        ]);
        $tag_id = (int) $this->db->insert_id();

        $this->json_out([
            'ok'         => true,
            'success'    => true,
            'tag_id'     => $tag_id,
            'planner_id' => $planner_id,
            'bd_uid'     => $bd_uid,
            'outcome'    => $outcome,
            'tagged_at'  => $now,
        ]);
    }

}

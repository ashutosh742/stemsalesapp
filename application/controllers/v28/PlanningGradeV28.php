<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * PlanningGradeV28 Controller
 *
 * Provides planning grade views for STEM CRM v2.8.
 * Grade letters derived from bd_productivity_daily.score_pct:
 *   >= 90  -> A+
 *   80-89  -> A
 *   70-79  -> B
 *   60-69  -> C
 *   < 60   -> D
 *
 * Also reads planner_coach_discipline for discipline grade data
 * and planner_coach_day_end for day-end grade data.
 *
 * Routes handled:
 *   GET /api/planning_grade           - list grade rows for a date
 *   GET /api/planning_grade/audit     - full audit rows from coach tables
 *   GET /api/planning_grade/probe     - health check
 *   GET /api/planning_grade/tile      - summary tile for dashboard
 *
 * Also handles planner_v2 group:
 *   GET /api/planner_v2/approval_queue - pending approvals list
 *   GET /api/planner_v2/list           - list BD plans with grade
 *   GET /api/planner_v2/probe          - health check
 */
class PlanningGradeV28 extends CI_Controller {

    /** Bearer token for API auth */
    const BEARER = '4eBaiAT7r4zu6OK3b8evjLNia1D7RGgb0qRTuLJfUSo';

    public function __construct()
    {
        parent::__construct();
        $this->load->database();
        $this->output->set_content_type('application/json');
    }

    // -------------------------------------------------------------------------
    // HELPERS
    // -------------------------------------------------------------------------

    private function json_out($data, $status = 200)
    {
        $this->output
             ->set_status_header($status)
             ->set_output(json_encode($data, JSON_UNESCAPED_UNICODE));
    }

    private function auth_check()
    {
        $header = $this->input->get_request_header('Authorization', TRUE);
        if (!$header) {
            $this->json_out(['ok' => false, 'success' => false, 'error' => 'unauthorized'], 401);
            return false;
        }
        $tok = trim(str_replace('Bearer', '', $header));
        if ($tok === self::BEARER) return true;
        // Per-user daily JWT: sha1(secret|uid|YYYY-MM-DD), accept today and yesterday
        $secret = getenv('STEM_DIGEST_TOKEN') ?: self::BEARER;
        $days = array(date('Y-m-d'), date('Y-m-d', strtotime('-1 day')), date('Y-m-d', strtotime('+1 day')));
        $cands = array();
        foreach (array('uid','bd_uid','cm_uid','user_id') as $k) {
            if (isset($_GET[$k]) && (int)$_GET[$k] > 0)  $cands[(int)$_GET[$k]]  = 1;
            if (isset($_POST[$k]) && (int)$_POST[$k] > 0) $cands[(int)$_POST[$k]] = 1;
        }
        // Also try route segment (planning_grade/bd/<uid>)
        $u = $this->uri->segment(4);
        if ($u && (int)$u > 0) $cands[(int)$u] = 1;
        foreach (array_keys($cands) as $u) {
            foreach ($days as $d) {
                if (hash_equals(sha1($secret.'|'.$u.'|'.$d), $tok)) return true;
            }
        }
        $this->json_out(['ok' => false, 'success' => false, 'error' => 'unauthorized'], 401);
        return false;
    }

    private function resolve_date()
    {
        $d = $this->input->get('date');
        if ($d && preg_match('/^\d{4}-\d{2}-\d{2}$/', $d)) {
            return $d;
        }
        return date('Y-m-d');
    }

    /**
     * score_to_grade
     * Maps a numeric score_pct to a letter grade.
     *
     * @param float $score
     * @return string
     */
    private function score_to_grade($score)
    {
        $s = (float) $score;
        if ($s >= 90) return 'A+';
        if ($s >= 80) return 'A';
        if ($s >= 70) return 'B';
        if ($s >= 60) return 'C';
        return 'D';
    }

    // -------------------------------------------------------------------------
    // PLANNING_GRADE ENDPOINTS
    // -------------------------------------------------------------------------

    /**
     * index
     * GET /api/planning_grade[?date=YYYY-MM-DD][&bd_uid=N]
     *
     * Returns bd_productivity_daily rows with computed grade letters.
     */
    public function index()
    {
        if (!$this->auth_check()) return;

        $date   = $this->resolve_date();
        $bd_uid = (int) $this->input->get('bd_uid');

        $this->db->select('bpd.bd_uid, u.name AS bd_name, bpd.for_date,
                           bpd.planned_min, bpd.executed_min, bpd.idle_min,
                           bpd.budget_min, bpd.score_pct')
                 ->from('bd_productivity_daily bpd')
                 ->join('user u', 'u.uid = bpd.bd_uid', 'left')
                 ->where('bpd.for_date', $date);

        if ($bd_uid > 0) {
            $this->db->where('bpd.bd_uid', $bd_uid);
        }

        $this->db->order_by('bpd.score_pct', 'DESC')
                 ->limit(200);

        $raw  = $this->db->get()->result_array();
        $rows = [];
        foreach ($raw as $r) {
            $r['grade_letter'] = $this->score_to_grade($r['score_pct']);
            $rows[]            = $r;
        }

        if (empty($rows)) {
            $this->json_out(['ok' => true, 'success' => true, 'rows' => [], 'count' => 0, 'note' => 'no_data', 'date' => $date]);
            return;
        }

        $this->json_out([
            'ok'      => true,
            'success' => true,
            'date'    => $date,
            'rows'    => $rows,
            'count'   => count($rows),
        ]);
    }

    /**
     * audit
     * GET /api/planning_grade/audit[?date=YYYY-MM-DD][&bd_uid=N]
     *
     * Returns planner_coach_discipline rows (discipline grade audit).
     * Falls back to bd_productivity_daily if discipline table is empty.
     */
    public function audit()
    {
        if (!$this->auth_check()) return;

        $date   = $this->resolve_date();
        $bd_uid = (int) $this->input->get('bd_uid');

        // Try planner_coach_discipline first
        $this->db->select('pcd.id, pcd.bd_uid, u.name AS bd_name, pcd.plan_date,
                           pcd.computed_at, pcd.submitted_at, pcd.submitted_by_cutoff,
                           pcd.minutes_to_submit, pcd.edit_count, pcd.tasks_planned,
                           pcd.minute_budget_used, pcd.mandatory_coverage_pct,
                           pcd.grade_score, pcd.grade_letter')
                 ->from('planner_coach_discipline pcd')
                 ->join('user u', 'u.uid = pcd.bd_uid', 'left')
                 ->where('pcd.plan_date', $date);

        if ($bd_uid > 0) {
            $this->db->where('pcd.bd_uid', $bd_uid);
        }

        $this->db->order_by('pcd.grade_score', 'DESC')->limit(200);
        $rows = $this->db->get()->result_array();

        $source = 'planner_coach_discipline';

        if (empty($rows)) {
            // Fallback: use bd_productivity_daily with computed grade
            $this->db->select('bpd.bd_uid, u.name AS bd_name, bpd.for_date AS plan_date,
                               bpd.planned_min, bpd.executed_min, bpd.score_pct')
                     ->from('bd_productivity_daily bpd')
                     ->join('user u', 'u.uid = bpd.bd_uid', 'left')
                     ->where('bpd.for_date', $date);

            if ($bd_uid > 0) {
                $this->db->where('bpd.bd_uid', $bd_uid);
            }

            $raw = $this->db->get()->result_array();
            foreach ($raw as $r) {
                $r['grade_letter'] = $this->score_to_grade($r['score_pct']);
                $r['grade_score']  = $r['score_pct'];
                $rows[]            = $r;
            }
            $source = 'bd_productivity_daily';
        }

        if (empty($rows)) {
            $this->json_out(['ok' => true, 'success' => true, 'rows' => [], 'count' => 0, 'note' => 'no_data', 'date' => $date]);
            return;
        }

        $this->json_out([
            'ok'      => true,
            'success' => true,
            'date'    => $date,
            'source'  => $source,
            'rows'    => $rows,
            'count'   => count($rows),
        ]);
    }

    /**
     * probe
     * GET /api/planning_grade/probe
     *
     * Health check.
     */
    public function probe()
    {
        if (!$this->auth_check()) return;
        $this->json_out(['ok' => true, 'success' => true, 'controller' => 'PlanningGradeV28']);
    }

    /**
     * tile
     * GET /api/planning_grade/tile[?date=YYYY-MM-DD]
     *
     * Returns a summary tile: grade distribution, average score,
     * top BD, and bottom BD for dashboard display.
     */
    public function tile()
    {
        if (!$this->auth_check()) return;

        $date = $this->resolve_date();

        $raw = $this->db->select('bpd.bd_uid, u.name AS bd_name, bpd.for_date,
                                  bpd.planned_min, bpd.executed_min, bpd.score_pct')
                        ->from('bd_productivity_daily bpd')
                        ->join('user u', 'u.uid = bpd.bd_uid', 'left')
                        ->where('bpd.for_date', $date)
                        ->order_by('bpd.score_pct', 'DESC')
                        ->limit(200)
                        ->get()->result_array();

        if (empty($raw)) {
            $this->json_out(['ok' => true, 'success' => true, 'date' => $date, 'note' => 'no_data', 'data' => []]);
            return;
        }

        $distribution = ['A+' => 0, 'A' => 0, 'B' => 0, 'C' => 0, 'D' => 0];
        $total_score  = 0.0;
        $count        = count($raw);

        foreach ($raw as &$r) {
            $r['grade_letter'] = $this->score_to_grade($r['score_pct']);
            $distribution[$r['grade_letter']]++;
            $total_score += (float) $r['score_pct'];
        }
        unset($r);

        $avg_score = $count > 0 ? round($total_score / $count, 2) : 0.0;
        $top_bd    = $raw[0];
        $bottom_bd = $raw[$count - 1];

        $this->json_out([
            'ok'           => true,
            'success'      => true,
            'date'         => $date,
            'data'         => [
                'total_bds'    => $count,
                'avg_score'    => $avg_score,
                'avg_grade'    => $this->score_to_grade($avg_score),
                'distribution' => $distribution,
                'top_bd'       => $top_bd,
                'bottom_bd'    => $bottom_bd,
            ],
        ]);
    }

    // -------------------------------------------------------------------------
    // PLANNER_V2 ENDPOINTS
    // -------------------------------------------------------------------------

    /**
     * approval_queue
     * GET /api/planner_v2/approval_queue[?date=YYYY-MM-DD]
     *
     * Returns pending planner_approved rows (approved_status IS NULL)
     * enriched with grade data from planner_coach_discipline if available.
     */
    public function approval_queue()
    {
        if (!$this->auth_check()) return;

        $date = $this->resolve_date();

        $rows = $this->db->select('pa.id, pa.user_id, u.name AS bd_name, pa.request_date,
                                   pa.request_type, pa.request_message,
                                   pa.approved_status, pa.approved_by,
                                   pa.approved_date, pa.created_at')
                         ->from('planner_approved pa')
                         ->join('user u', 'u.uid = pa.user_id', 'left')
                         ->where('pa.approved_status IS NULL', NULL, FALSE)
                         ->where('pa.request_date', $date)
                         ->order_by('pa.created_at', 'ASC')
                         ->limit(200)
                         ->get()->result_array();

        $this->json_out([
            'ok'      => true,
            'success' => true,
            'date'    => $date,
            'rows'    => $rows,
            'count'   => count($rows),
        ]);
    }

    /**
     * list
     * GET /api/planner_v2/list[?date=YYYY-MM-DD][&bd_uid=N]
     *
     * Returns planner_approved rows for the date with grade from
     * bd_productivity_daily joined in.
     */
    public function list_plans()
    {
        if (!$this->auth_check()) return;

        $date   = $this->resolve_date();
        $bd_uid = (int) $this->input->get('bd_uid');

        $this->db->select('pa.id, pa.user_id, u.name AS bd_name, pa.request_date,
                           pa.request_type, pa.approved_status,
                           pa.approved_by, pa.approved_date, pa.created_at,
                           bpd.planned_min, bpd.executed_min,
                           bpd.score_pct')
                 ->from('planner_approved pa')
                 ->join('user u', 'u.uid = pa.user_id', 'left')
                 ->join('bd_productivity_daily bpd',
                        'bpd.bd_uid = pa.user_id AND bpd.for_date = pa.request_date',
                        'left')
                 ->where('pa.request_date', $date);

        if ($bd_uid > 0) {
            $this->db->where('pa.user_id', $bd_uid);
        }

        $this->db->order_by('pa.created_at', 'DESC')->limit(200);
        $raw  = $this->db->get()->result_array();
        $rows = [];

        foreach ($raw as $r) {
            $r['grade_letter'] = $this->score_to_grade($r['score_pct'] ?? 0);
            $rows[]            = $r;
        }

        if (empty($rows)) {
            $this->json_out(['ok' => true, 'success' => true, 'rows' => [], 'count' => 0, 'note' => 'no_data', 'date' => $date]);
            return;
        }

        $this->json_out([
            'ok'      => true,
            'success' => true,
            'date'    => $date,
            'rows'    => $rows,
            'count'   => count($rows),
        ]);
    }

    /**
     * planner_v2_probe
     * GET /api/planner_v2/probe
     *
     * Health check for planner_v2 route group.
     */
    public function planner_v2_probe()
    {
        if (!$this->auth_check()) return;
        $this->json_out(['ok' => true, 'success' => true, 'controller' => 'PlanningGradeV28', 'group' => 'planner_v2']);
    }

    /**
     * bd
     * GET /api/planning_grade/bd/{uid}
     *
     * Re-added 2026-06-06 (F57 regression fix). The prior route pointed to
     * blitz_30may/BlitzPlannerApi/planning_grade which does not exist (404).
     * Returns this BD's planning-grade rows from bd_productivity_daily,
     * most recent dates first. Real data only; empty -> note='no_data'.
     */
    public function bd($uid = 0)
    {
        if (!$this->auth_check()) return;

        $uid = (int) $uid;
        // Additive 2026-06-17: the app also calls GET /api/planning_grade/bd with
        // no uri uid. When the path arg is absent, resolve the BD uid from the
        // query string (?uid= or ?bd_uid=) or from the authenticated user.
        // Existing /api/planning_grade/bd/(:num) route is unaffected ($uid > 0).
        if ($uid <= 0) {
            $q_uid = (int) ($this->input->get('uid') ?: $this->input->get('bd_uid'));
            if ($q_uid > 0) {
                $uid = $q_uid;
            } elseif (function_exists('authunify_uid') && (int) authunify_uid() > 0) {
                $uid = (int) authunify_uid();
            }
        }
        if ($uid <= 0) {
            $this->json_out(['ok' => true, 'success' => true, 'rows' => [], 'count' => 0, 'note' => 'uid_required']);
            return;
        }

        $this->db->select('bpd.bd_uid, u.name AS bd_name, bpd.for_date,
                           bpd.planned_min, bpd.executed_min, bpd.idle_min,
                           bpd.budget_min, bpd.score_pct')
                 ->from('bd_productivity_daily bpd')
                 ->join('user u', 'u.uid = bpd.bd_uid', 'left')
                 ->where('bpd.bd_uid', $uid)
                 ->order_by('bpd.for_date', 'DESC')
                 ->limit(200);

        $raw  = $this->db->get()->result_array();
        $rows = [];
        foreach ($raw as $r) {
            $r['grade_letter'] = $this->score_to_grade($r['score_pct']);
            $rows[]            = $r;
        }

        if (empty($rows)) {
            $this->json_out(['ok' => true, 'success' => true, 'rows' => [], 'count' => 0, 'uid' => $uid, 'note' => 'no_data']);
            return;
        }

        $this->json_out([
            'ok'      => true,
            'success' => true,
            'uid'     => $uid,
            'rows'    => $rows,
            'count'   => count($rows),
        ]);
    }

}

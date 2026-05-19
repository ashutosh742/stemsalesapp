<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * FunnelHygiene controller
 *
 * REST surface for migration 024.
 *
 * Endpoints:
 *   GET  /api/funnel/changes/yesterday
 *   GET  /api/funnel/changes?from=YYYY-MM-DD&to=YYYY-MM-DD
 *   GET  /api/hygiene/inbox?manager_uid=<n>           (CM/RM inbox)
 *   GET  /api/hygiene/no_purpose?days=7
 *   GET  /api/hygiene/phantom?days=7
 *   GET  /api/hygiene/weekly_gap?manager_uid=<n>
 *   GET  /api/hygiene/stagnant_22?manager_uid=<n>
 *   POST /api/hygiene/run_nightly                    (cron-only, token-gated)
 *   GET  /api/dm/verify/queue?verdict=pending&limit=50
 *   GET  /api/dm/verify/cid?cid_id=<n>
 *   POST /api/dm/verify/run_batch                    (cron-only)
 *   POST /api/dm/verify/manual_override              (CM override)
 *   GET  /api/scorecard/v2/manager?manager_uid=<n>   (K1-K8 + deduction)
 *   POST /api/scorecard/v2/recompute_all             (cron-only)
 *
 * Auth: Bearer STEM_DIGEST_TOKEN.
 *
 * Migration 024.
 */
class FunnelHygiene extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->database();
        $this->load->model('FunnelHygiene_model', 'hygiene');
        $this->load->model('DmVerifyAgent_model', 'dm_agent');
        $this->load->model('LineManagerScorecard_v2_K8_patch', 'scorecard');
        $this->_require_bearer();
    }

    // ------------------------------------------------------------------------
    private function _require_bearer()
    {
        $hdr = $this->input->get_request_header('Authorization');
        if (!$hdr || strpos($hdr, 'Bearer ') !== 0) {
            $this->_json(['error' => 'unauthorized'], 401);
        }
        $token = trim(substr($hdr, 7));
        $expected = getenv('STEM_DIGEST_TOKEN');
        if (!$expected || $token !== $expected) {
            $this->_json(['error' => 'invalid_token'], 401);
        }
    }

    private function _json($data, $code = 200)
    {
        $this->output->set_status_header($code)
                     ->set_content_type('application/json')
                     ->set_output(json_encode($data));
        exit;
    }

    // ========================================================================
    // FUNNEL CHANGES
    // ========================================================================
    public function changes_yesterday()
    {
        $rows = $this->db->query("SELECT * FROM v_funnel_changes_yesterday")
                         ->result_array();
        $this->_json(['rows' => $rows, 'count' => count($rows)]);
    }

    public function changes_range()
    {
        $from = $this->input->get('from');
        $to   = $this->input->get('to');
        if (!$from || !$to) $this->_json(['error' => 'missing_range'], 400);

        $rows = $this->db->query("
            SELECT f.*, ic.compny_nm AS school_name,
                   ub.firstName AS bd_name,
                   uc.firstName AS cm_name
              FROM funnel_change_log f
              LEFT JOIN init_call ic ON ic.id = f.cid_id
              LEFT JOIN user ub      ON ub.uid = f.bd_uid
              LEFT JOIN user uc      ON uc.uid = f.cm_uid
             WHERE DATE(f.created_at) BETWEEN ? AND ?
             ORDER BY f.created_at DESC
        ", [$from, $to])->result_array();
        $this->_json(['rows' => $rows, 'count' => count($rows)]);
    }

    // ========================================================================
    // HYGIENE INBOX
    // ========================================================================
    public function inbox()
    {
        $manager_uid = (int)$this->input->get('manager_uid');
        if (!$manager_uid) $this->_json(['error' => 'missing_manager_uid'], 400);

        $rows = $this->db->query("
            SELECT v.*,
                   ic.compny_nm AS school_name,
                   u.firstName AS bd_name
              FROM v_cm_hygiene_inbox v
              LEFT JOIN init_call ic ON ic.id = v.cid_id
              LEFT JOIN user u       ON u.uid = v.bd_uid
             WHERE v.cm_uid = ?
             ORDER BY v.opened_at DESC
        ", [$manager_uid])->result_array();
        $this->_json(['rows' => $rows, 'count' => count($rows)]);
    }

    public function no_purpose()
    {
        $days = (int)($this->input->get('days') ?: 7);
        $rows = $this->db->query("
            SELECT n.*, ic.compny_nm AS school_name, u.firstName AS bd_name
              FROM no_purpose_task_log n
              LEFT JOIN init_call ic ON ic.id = n.cid_id
              LEFT JOIN user u       ON u.uid = n.bd_uid
             WHERE n.resolved = 0
               AND n.detected_at >= DATE_SUB(CURDATE(), INTERVAL ? DAY)
             ORDER BY n.detected_at DESC
        ", [$days])->result_array();
        $this->_json(['rows' => $rows, 'count' => count($rows)]);
    }

    public function phantom()
    {
        $days = (int)($this->input->get('days') ?: 7);
        $rows = $this->db->query("
            SELECT p.*, ic.compny_nm AS school_name, u.firstName AS bd_name
              FROM phantom_task_log p
              LEFT JOIN init_call ic ON ic.id = p.cid_id
              LEFT JOIN user u       ON u.uid = p.bd_uid
             WHERE p.resolved = 0
               AND p.detected_at >= DATE_SUB(CURDATE(), INTERVAL ? DAY)
             ORDER BY p.days_since_planned DESC, p.detected_at DESC
        ", [$days])->result_array();
        $this->_json(['rows' => $rows, 'count' => count($rows)]);
    }

    public function weekly_gap()
    {
        $mgr = (int)$this->input->get('manager_uid');
        $sql = "SELECT w.*, ic.compny_nm AS school_name, u.firstName AS bd_name
                  FROM weekly_touch_gap w
                  LEFT JOIN init_call ic ON ic.id = w.cid_id
                  LEFT JOIN user u       ON u.uid = w.bd_uid
                 WHERE w.resolved = 0";
        $params = [];
        if ($mgr) { $sql .= " AND w.cm_uid = ?"; $params[] = $mgr; }
        $sql .= " ORDER BY w.days_since_last_task DESC";
        $rows = $this->db->query($sql, $params)->result_array();
        $this->_json(['rows' => $rows, 'count' => count($rows)]);
    }

    public function stagnant_22()
    {
        $mgr = (int)$this->input->get('manager_uid');
        $sql = "SELECT s.*, ic.compny_nm AS school_name,
                       u.firstName AS bd_name, ic.fbudget AS pipeline_rs
                  FROM stagnancy_22_log s
                  LEFT JOIN init_call ic ON ic.id = s.cid_id
                  LEFT JOIN user u       ON u.uid = s.bd_uid
                 WHERE s.resolved = 0";
        $params = [];
        if ($mgr) { $sql .= " AND s.cm_uid = ?"; $params[] = $mgr; }
        $sql .= " ORDER BY s.task_count DESC, s.days_in_cstatus DESC";
        $rows = $this->db->query($sql, $params)->result_array();
        $this->_json(['rows' => $rows, 'count' => count($rows)]);
    }

    public function run_nightly()
    {
        if ($this->input->method() !== 'post') $this->_json(['error' => 'post_only'], 405);
        $out = $this->hygiene->run_nightly();
        $this->_json($out);
    }

    // ========================================================================
    // DM VERIFICATION
    // ========================================================================
    public function dm_queue()
    {
        $verdict = $this->input->get('verdict') ?: 'pending';
        $limit   = (int)($this->input->get('limit') ?: 50);
        $rows = $this->db->query("
            SELECT d.*, ic.compny_nm AS school_name, u.firstName AS bd_name
              FROM dm_verification d
              LEFT JOIN init_call ic ON ic.id = d.cid_id
              LEFT JOIN user u       ON u.uid = d.bd_uid
             WHERE d.verdict = ?
             ORDER BY d.created_at DESC
             LIMIT ?
        ", [$verdict, $limit])->result_array();
        $this->_json(['rows' => $rows, 'count' => count($rows)]);
    }

    public function dm_cid()
    {
        $cid = (int)$this->input->get('cid_id');
        if (!$cid) $this->_json(['error' => 'missing_cid_id'], 400);
        $rows = $this->db->query("
            SELECT * FROM dm_verification WHERE cid_id = ? ORDER BY created_at DESC
        ", [$cid])->result_array();
        $this->_json(['rows' => $rows, 'count' => count($rows)]);
    }

    public function dm_run_batch()
    {
        if ($this->input->method() !== 'post') $this->_json(['error' => 'post_only'], 405);
        $limit = (int)($this->input->post('limit') ?: 50);
        $out = $this->dm_agent->run_batch($limit);
        $this->_json($out);
    }

    public function dm_manual_override()
    {
        if ($this->input->method() !== 'post') $this->_json(['error' => 'post_only'], 405);
        $id      = (int)$this->input->post('id');
        $verdict = $this->input->post('verdict');
        $reason  = $this->input->post('reason');
        $by_uid  = (int)$this->input->post('by_uid');

        if (!$id || !in_array($verdict, ['verified','doubtful','not_csr'])) {
            $this->_json(['error' => 'invalid_input'], 400);
        }
        $this->db->query("
            UPDATE dm_verification
               SET verdict = ?, verdict_reason = ?, verdict_at = NOW(),
                   verdict_by = 'cm_manual'
             WHERE id = ?
        ", [$verdict, $reason, $id]);
        $this->_json(['ok' => 1, 'id' => $id, 'verdict' => $verdict,
                      'by_uid' => $by_uid]);
    }

    // ========================================================================
    // SCORECARD V2 (K1 to K8)
    // ========================================================================
    public function scorecard_manager()
    {
        $mgr = (int)$this->input->get('manager_uid');
        if (!$mgr) $this->_json(['error' => 'missing_manager_uid'], 400);

        $week_start = $this->input->get('week_start')
                      ?: date('Y-m-d', strtotime('monday this week'));
        $row = $this->db->query("
            SELECT l.*, q.fiscal_year, q.quarter_number, q.chip_color,
                   q.k1_weight, q.k2_weight, q.k3_weight, q.k4_weight,
                   q.k5_weight, q.k6_weight, q.k7_weight, q.k8_weight
              FROM line_manager_scorecard l
              INNER JOIN quarter_config q ON q.id = l.quarter_config_id
             WHERE l.manager_uid = ? AND l.week_start = ?
             ORDER BY l.id DESC LIMIT 1
        ", [$mgr, $week_start])->row_array();
        $this->_json($row ?: ['error' => 'no_scorecard']);
    }

    public function scorecard_recompute_all()
    {
        if ($this->input->method() !== 'post') $this->_json(['error' => 'post_only'], 405);
        $out = $this->scorecard->recompute_all_this_week();
        $this->_json($out);
    }

    // ========================================================================
    // LEADERBOARD (for CM of the week cron 891ca261)
    // ========================================================================
    public function scorecard_leaderboard()
    {
        $period = $this->input->get('period') ?: 'week';
        $week_start = date('Y-m-d', strtotime('monday this week'));
        $rows = $this->db->query("
            SELECT l.manager_uid, u.firstName AS manager_name,
                   u.type_id, l.day_score, l.grade,
                   l.k1_mom_sla_pct, l.k3_signoff_avg_hours,
                   l.k8_funnel_hygiene_pct,
                   l.incentive_deduction_rs,
                   l.k8_stagnant_22_count + l.k8_weekly_gap_count
                      + l.k8_no_purpose_count + l.k8_phantom_count
                      AS total_breaches
              FROM line_manager_scorecard l
              INNER JOIN user u ON u.uid = l.manager_uid
             WHERE l.week_start = ?
             ORDER BY l.day_score DESC
        ", [$week_start])->result_array();
        $this->_json(['rows' => $rows, 'count' => count($rows),
                      'period' => $period, 'week_start' => $week_start]);
    }
}

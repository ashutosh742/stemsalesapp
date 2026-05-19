<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * FunnelHygiene_model
 *
 * Detects funnel hygiene breaches nightly. Writes to:
 *   - no_purpose_task_log
 *   - phantom_task_log
 *   - weekly_touch_gap
 *   - stagnancy_22_log
 *   - incentive_deduction
 *
 * Reads funnel_change_log (populated by trigger trg_init_call_funnel_change).
 *
 * Scope: All checks apply to CIDs in cstatus 3, 6, 7, 8, 9.
 * cstatus 1 and 2 are BD-only and skipped.
 * cstatus 12 (Won) and 13 (Lost) are closed and skipped.
 *
 * Migration 024.
 * Author: STEM ops, 2026-05-17.
 */
class FunnelHygiene_model extends CI_Model
{
    /** Stages where manager is accountable. */
    private $active_stages = [3, 6, 7, 8, 9];

    /** CSR keyword set used by DM agent and surfaced here for joint scoring. */
    private $csr_keywords = [
        'csr', 'sustainability', 'foundation', 'esg',
        'social impact', 'community', 'philanthropy'
    ];

    /** Per-breach deduction in rupees. */
    private $deduction_rs = [
        'CM' => 500,
        'RM' => 1000,
    ];

    public function __construct()
    {
        parent::__construct();
        $this->load->database();
    }

    // ------------------------------------------------------------------------
    // RULE 2: NO-PURPOSE TASKS
    // tblcallevents rows where purpose_id is null or 0.
    // Run nightly.
    // ------------------------------------------------------------------------
    public function detect_no_purpose_tasks($since_days = 1)
    {
        $sql = "
            INSERT IGNORE INTO no_purpose_task_log
                (event_id, cid_id, bd_uid, cm_uid, actiontype_id, event_date)
            SELECT
                t.id, t.cid_id, t.user_id,
                (SELECT parent_uid FROM reporting_hierarchy
                  WHERE employee_uid = t.user_id AND active = 1 LIMIT 1),
                t.actiontype_id, DATE(t.event_date)
            FROM tblcallevents t
            INNER JOIN init_call ic ON ic.id = t.cid_id
            WHERE DATE(t.event_date) >= DATE_SUB(CURDATE(), INTERVAL ? DAY)
              AND (t.purpose_id IS NULL OR t.purpose_id = 0)
              AND ic.cstatus IN (3, 6, 7, 8, 9)
        ";
        $this->db->query($sql, [(int)$since_days]);
        return $this->db->affected_rows();
    }

    // ------------------------------------------------------------------------
    // RULE 3: PHANTOM TASKS
    // Task planned, no MoM, no photo, no GPS, no completion, more than
    // 1 day past planned date.
    // ------------------------------------------------------------------------
    public function detect_phantom_tasks($lookback_days = 7)
    {
        $sql = "
            INSERT IGNORE INTO phantom_task_log
                (event_id, cid_id, bd_uid, cm_uid, actiontype_id,
                 planned_date, has_mom, has_photo, has_gps, has_completion,
                 days_since_planned)
            SELECT
                t.id, t.cid_id, t.user_id,
                (SELECT parent_uid FROM reporting_hierarchy
                  WHERE employee_uid = t.user_id AND active = 1 LIMIT 1),
                t.actiontype_id, DATE(t.event_date),
                CASE WHEN EXISTS(SELECT 1 FROM mom_data m
                                  WHERE m.event_id = t.id) THEN 1 ELSE 0 END,
                CASE WHEN t.photo_url IS NOT NULL
                          AND t.photo_url <> '' THEN 1 ELSE 0 END,
                CASE WHEN t.lat IS NOT NULL
                          AND t.lng IS NOT NULL
                          AND t.lat <> 0 THEN 1 ELSE 0 END,
                CASE WHEN t.completed_at IS NOT NULL THEN 1 ELSE 0 END,
                DATEDIFF(CURDATE(), DATE(t.event_date))
            FROM tblcallevents t
            INNER JOIN init_call ic ON ic.id = t.cid_id
            WHERE DATE(t.event_date) BETWEEN
                    DATE_SUB(CURDATE(), INTERVAL ? DAY)
                AND DATE_SUB(CURDATE(), INTERVAL 1 DAY)
              AND ic.cstatus IN (3, 6, 7, 8, 9)
              AND NOT EXISTS(SELECT 1 FROM mom_data m WHERE m.event_id = t.id)
              AND (t.photo_url IS NULL OR t.photo_url = '')
              AND (t.lat IS NULL OR t.lat = 0)
              AND t.completed_at IS NULL
        ";
        $this->db->query($sql, [(int)$lookback_days]);
        return $this->db->affected_rows();
    }

    // ------------------------------------------------------------------------
    // RULE 4: WEEKLY TOUCH GAP
    // CID in cstatus 3+, zero tblcallevents in last 7 days.
    // ------------------------------------------------------------------------
    public function detect_weekly_touch_gaps()
    {
        $sql = "
            INSERT IGNORE INTO weekly_touch_gap
                (cid_id, bd_uid, cm_uid, cstatus,
                 last_task_date, days_since_last_task, detected_at)
            SELECT
                ic.id, ic.mainbd,
                (SELECT parent_uid FROM reporting_hierarchy
                  WHERE employee_uid = ic.mainbd AND active = 1 LIMIT 1),
                ic.cstatus,
                last_t.last_task_date,
                COALESCE(
                    DATEDIFF(CURDATE(), last_t.last_task_date),
                    DATEDIFF(CURDATE(), ic.createDate)
                ),
                CURDATE()
            FROM init_call ic
            LEFT JOIN (
                SELECT cid_id, MAX(DATE(event_date)) AS last_task_date
                  FROM tblcallevents
                 GROUP BY cid_id
            ) last_t ON last_t.cid_id = ic.id
            WHERE ic.cstatus IN (3, 6, 7, 8, 9)
              AND (last_t.last_task_date IS NULL
                   OR last_t.last_task_date < DATE_SUB(CURDATE(), INTERVAL 7 DAY))
        ";
        $this->db->query($sql);
        return $this->db->affected_rows();
    }

    // ------------------------------------------------------------------------
    // RULE 5: 22-TASK STAGNANCY
    // 22 or more tblcallevents on a CID, and cstatus did not move since the
    // first task. Flag and email only. No block.
    // ------------------------------------------------------------------------
    public function detect_22_task_stagnancy()
    {
        $sql = "
            INSERT IGNORE INTO stagnancy_22_log
                (cid_id, bd_uid, cm_uid, rm_uid, cstatus, task_count,
                 days_in_cstatus, cstatus_locked_at, first_task_date, detected_at)
            SELECT
                ic.id, ic.mainbd,
                (SELECT parent_uid FROM reporting_hierarchy
                  WHERE employee_uid = ic.mainbd AND active = 1 LIMIT 1),
                (SELECT skip_parent_uid FROM reporting_hierarchy
                  WHERE employee_uid = ic.mainbd AND active = 1 LIMIT 1),
                ic.cstatus,
                stats.task_count,
                DATEDIFF(CURDATE(), COALESCE(last_change.changed_at, ic.createDate)),
                last_change.changed_at,
                stats.first_task_date,
                CURDATE()
            FROM init_call ic
            INNER JOIN (
                SELECT cid_id,
                       COUNT(*) AS task_count,
                       MIN(DATE(event_date)) AS first_task_date
                  FROM tblcallevents
                 GROUP BY cid_id
                HAVING COUNT(*) >= 22
            ) stats ON stats.cid_id = ic.id
            LEFT JOIN (
                SELECT cid_id, MAX(created_at) AS changed_at
                  FROM funnel_change_log
                 GROUP BY cid_id
            ) last_change ON last_change.cid_id = ic.id
            WHERE ic.cstatus IN (3, 6, 7, 8, 9)
              AND (last_change.changed_at IS NULL
                   OR DATE(last_change.changed_at) <= stats.first_task_date)
        ";
        $this->db->query($sql);
        return $this->db->affected_rows();
    }

    // ------------------------------------------------------------------------
    // AUTO-RESOLVE: if cstatus moved after the breach was logged, mark resolved.
    // Called nightly after detection passes.
    // ------------------------------------------------------------------------
    public function auto_resolve_on_cstatus_move()
    {
        $this->db->query("
            UPDATE stagnancy_22_log s
            INNER JOIN funnel_change_log f
                    ON f.cid_id = s.cid_id
                   AND f.created_at > s.detected_at
               SET s.resolved = 1,
                   s.resolved_at = f.created_at,
                   s.resolution_reason = 'cstatus_moved'
             WHERE s.resolved = 0
        ");

        $this->db->query("
            UPDATE weekly_touch_gap w
            INNER JOIN tblcallevents t
                    ON t.cid_id = w.cid_id
                   AND DATE(t.event_date) > w.detected_at
               SET w.resolved = 1,
                   w.resolved_at = NOW()
             WHERE w.resolved = 0
        ");
    }

    // ------------------------------------------------------------------------
    // INCENTIVE DEDUCTION
    // For each open breach this week, write a deduction row for the CM and RM.
    // CM Rs 500 per breach, RM Rs 1000 per breach.
    // ------------------------------------------------------------------------
    public function compute_incentive_deductions()
    {
        $week_start = date('Y-m-d', strtotime('monday this week'));
        $week_end   = date('Y-m-d', strtotime('sunday this week'));

        // Pull every unresolved breach this week joined to manager chain.
        $rows = $this->db->query("
            SELECT 'stagnant_22' AS breach_type, s.id AS breach_ref_id,
                   s.cid_id, s.bd_uid, s.cm_uid, s.rm_uid
              FROM stagnancy_22_log s
             WHERE s.resolved = 0
               AND s.detected_at BETWEEN ? AND ?
            UNION ALL
            SELECT 'weekly_gap', w.id, w.cid_id, w.bd_uid, w.cm_uid,
                   (SELECT skip_parent_uid FROM reporting_hierarchy
                     WHERE employee_uid = w.bd_uid AND active = 1 LIMIT 1)
              FROM weekly_touch_gap w
             WHERE w.resolved = 0
               AND w.detected_at BETWEEN ? AND ?
            UNION ALL
            SELECT 'no_purpose', n.id, n.cid_id, n.bd_uid, n.cm_uid,
                   (SELECT skip_parent_uid FROM reporting_hierarchy
                     WHERE employee_uid = n.bd_uid AND active = 1 LIMIT 1)
              FROM no_purpose_task_log n
             WHERE n.resolved = 0
               AND DATE(n.detected_at) BETWEEN ? AND ?
            UNION ALL
            SELECT 'phantom_task', p.id, p.cid_id, p.bd_uid, p.cm_uid,
                   (SELECT skip_parent_uid FROM reporting_hierarchy
                     WHERE employee_uid = p.bd_uid AND active = 1 LIMIT 1)
              FROM phantom_task_log p
             WHERE p.resolved = 0
               AND DATE(p.detected_at) BETWEEN ? AND ?
        ", [$week_start, $week_end, $week_start, $week_end,
            $week_start, $week_end, $week_start, $week_end])->result_array();

        $written = 0;
        foreach ($rows as $r) {
            // CM deduction
            if (!empty($r['cm_uid'])) {
                $ok = $this->db->query("
                    INSERT IGNORE INTO incentive_deduction
                        (manager_uid, manager_role, breach_type, breach_ref_id,
                         cid_id, bd_uid, deduction_rs, week_start, week_end)
                    VALUES (?, 'CM', ?, ?, ?, ?, ?, ?, ?)
                ", [$r['cm_uid'], $r['breach_type'], $r['breach_ref_id'],
                    $r['cid_id'], $r['bd_uid'], $this->deduction_rs['CM'],
                    $week_start, $week_end]);
                if ($this->db->affected_rows() > 0) $written++;
            }
            // RM deduction (only for stagnant_22, the high-value breach)
            if (!empty($r['rm_uid']) && $r['breach_type'] === 'stagnant_22') {
                $this->db->query("
                    INSERT IGNORE INTO incentive_deduction
                        (manager_uid, manager_role, breach_type, breach_ref_id,
                         cid_id, bd_uid, deduction_rs, week_start, week_end)
                    VALUES (?, 'RM', ?, ?, ?, ?, ?, ?, ?)
                ", [$r['rm_uid'], $r['breach_type'], $r['breach_ref_id'],
                    $r['cid_id'], $r['bd_uid'], $this->deduction_rs['RM'],
                    $week_start, $week_end]);
                if ($this->db->affected_rows() > 0) $written++;
            }
        }
        return $written;
    }

    // ------------------------------------------------------------------------
    // K8 COMPUTATION (used by LineManagerScorecard_v2_model)
    // K8 = (active CIDs without breaches in week) / (active CIDs in cstatus 3+) * 100
    // ------------------------------------------------------------------------
    public function compute_k8_for_manager($manager_uid)
    {
        $row = $this->db->query("
            SELECT k8_pct, active_cid_count, breach_count
              FROM v_k8_per_manager_week
             WHERE manager_uid = ?
             LIMIT 1
        ", [$manager_uid])->row_array();
        return $row ?: ['k8_pct' => null, 'active_cid_count' => 0, 'breach_count' => 0];
    }

    public function breach_breakdown_for_manager($manager_uid, $week_start, $week_end)
    {
        return $this->db->query("
            SELECT breach_type, COUNT(*) AS cnt
              FROM v_cm_hygiene_inbox
             WHERE cm_uid = ?
               AND opened_at BETWEEN ? AND ?
             GROUP BY breach_type
        ", [$manager_uid, $week_start, $week_end])->result_array();
    }

    // ------------------------------------------------------------------------
    // ENTRY POINT: nightly run, called by cron at 1 AM IST after detection window.
    // ------------------------------------------------------------------------
    public function run_nightly()
    {
        $out = [];
        $out['no_purpose_added']   = $this->detect_no_purpose_tasks(1);
        $out['phantom_added']      = $this->detect_phantom_tasks(7);
        $out['weekly_gap_added']   = $this->detect_weekly_touch_gaps();
        $out['stagnant_22_added']  = $this->detect_22_task_stagnancy();
        $this->auto_resolve_on_cstatus_move();
        $out['deductions_written'] = $this->compute_incentive_deductions();
        $out['ran_at']             = date('c');
        return $out;
    }
}

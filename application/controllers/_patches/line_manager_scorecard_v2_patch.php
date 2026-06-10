<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * LineManagerScorecard_v2_K8_patch
 *
 * Patch over LineManagerScorecard_v2_model to add K8 Funnel Hygiene.
 * Apply after migration 024 SQL is in place.
 *
 * Rebalanced weights (from quarter_config, default for FY27+):
 *   K1 MoM SLA             13 percent
 *   K2 Coaching ratio      13 percent
 *   K3 Signoff speed       13 percent
 *   K4 R2B follow-through  13 percent
 *   K5 Stuck closure        9 percent
 *   K6 Coaching notes       9 percent
 *   K7 Escalation pre-SLA  15 percent
 *   K8 Funnel Hygiene      15 percent  (NEW)
 *   Total                 100 percent
 *
 * K8 = percent of CIDs under manager in cstatus 3+ without any hygiene breach
 *      this week.
 *
 * Incentive deduction stacks on top of the K8 grade hit:
 *   CM Rs 500 per breach in cluster
 *   RM Rs 1000 per stagnant_22 breach in cluster
 *
 * Migration 024.
 * Author: STEM ops, 2026-05-17.
 */
class LineManagerScorecard_v2_K8_patch extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
        $this->load->database();
        $this->load->model('FunnelHygiene_model', 'hygiene');
    }

    // ------------------------------------------------------------------------
    // RECOMPUTE one manager for a given week (Mon - Sun).
    // Called by cron 891ca261 (Friday CM of the week) and by 023.3 scorecard v2.
    // ------------------------------------------------------------------------
    public function recompute_manager($manager_uid, $quarter_config_id, $week_start = null)
    {
        $week_start = $week_start ?: date('Y-m-d', strtotime('monday this week'));
        $week_end   = date('Y-m-d', strtotime($week_start . ' +6 days'));

        // K1 to K7 already computed by LineManagerScorecard_v2_model.
        // We append K8 and the rs deduction here.
        $k8 = $this->hygiene->compute_k8_for_manager($manager_uid);
        $breakdown = $this->hygiene->breach_breakdown_for_manager(
            $manager_uid, $week_start, $week_end
        );

        $counts = [
            'stagnant_22' => 0, 'weekly_gap' => 0,
            'no_purpose'  => 0, 'phantom_task' => 0,
        ];
        foreach ($breakdown as $b) {
            $counts[$b['breach_type']] = (int)$b['cnt'];
        }

        // Weekly incentive deduction for this manager
        $row = $this->db->query("
            SELECT SUM(deduction_rs) AS total_rs
              FROM incentive_deduction
             WHERE manager_uid = ?
               AND week_start = ?
               AND applied_to_payout = 0
        ", [$manager_uid, $week_start])->row_array();
        $deduction = (float)($row['total_rs'] ?? 0);

        $this->db->query("
            UPDATE line_manager_scorecard
               SET k8_funnel_hygiene_pct = ?,
                   k8_stagnant_22_count  = ?,
                   k8_weekly_gap_count   = ?,
                   k8_no_purpose_count   = ?,
                   k8_phantom_count      = ?,
                   incentive_deduction_rs = ?
             WHERE manager_uid = ?
               AND quarter_config_id = ?
               AND week_start = ?
        ", [
            $k8['k8_pct'],
            $counts['stagnant_22'], $counts['weekly_gap'],
            $counts['no_purpose'], $counts['phantom_task'],
            $deduction,
            $manager_uid, $quarter_config_id, $week_start,
        ]);

        // Recompute day_score with K8 included.
        $this->recompute_day_score($manager_uid, $quarter_config_id, $week_start);

        return [
            'manager_uid'         => $manager_uid,
            'week_start'          => $week_start,
            'k8_pct'              => $k8['k8_pct'],
            'k8_breaches'         => $counts,
            'incentive_deduction' => $deduction,
        ];
    }

    // ------------------------------------------------------------------------
    // RECOMPUTE day_score with K8 added.
    // Reads K1-K8 plus weights from quarter_config.
    // ------------------------------------------------------------------------
    private function recompute_day_score($manager_uid, $qc_id, $week_start)
    {
        $weights = $this->db->query("
            SELECT k1_weight, k2_weight, k3_weight, k4_weight,
                   k5_weight, k6_weight, k7_weight, k8_weight
              FROM quarter_config WHERE id = ?
        ", [$qc_id])->row_array();
        if (!$weights) return;

        $score_row = $this->db->query("
            SELECT k1_mom_sla_pct, k2_coaching_ratio_pct,
                   k3_signoff_avg_hours, k4_r2b_followthrough_pct,
                   k5_stuck_closure_pct, k6_coaching_notes_count,
                   k7_escalation_pre_sla_pct, k8_funnel_hygiene_pct
              FROM line_manager_scorecard
             WHERE manager_uid = ? AND quarter_config_id = ?
               AND week_start = ?
        ", [$manager_uid, $qc_id, $week_start])->row_array();
        if (!$score_row) return;

        // K3 is hours, not percent: convert 0h=100, 24h=50, 48h+=0
        $k3_pct = max(0, min(100, 100 - ((float)$score_row['k3_signoff_avg_hours'] * 100 / 48)));
        // K6 is count, cap at 10 = 100 percent
        $k6_pct = min(100, (float)$score_row['k6_coaching_notes_count'] * 10);
        // K5 is bad-is-high, invert
        $k5_inv = max(0, 100 - (float)$score_row['k5_stuck_closure_pct']);

        $components = [
            (float)$score_row['k1_mom_sla_pct']         * $weights['k1_weight'],
            (float)$score_row['k2_coaching_ratio_pct']  * $weights['k2_weight'],
            $k3_pct                                      * $weights['k3_weight'],
            (float)$score_row['k4_r2b_followthrough_pct'] * $weights['k4_weight'],
            $k5_inv                                      * $weights['k5_weight'],
            $k6_pct                                      * $weights['k6_weight'],
            (float)$score_row['k7_escalation_pre_sla_pct'] * $weights['k7_weight'],
            (float)($score_row['k8_funnel_hygiene_pct'] ?? 0) * $weights['k8_weight'],
        ];
        $sum_weights = array_sum($weights);
        $day_score = $sum_weights > 0 ? round(array_sum($components) / $sum_weights, 2) : null;

        // Grade band
        $grade = 'D';
        if ($day_score >= 90)      $grade = 'A+';
        elseif ($day_score >= 75)  $grade = 'A';
        elseif ($day_score >= 60)  $grade = 'B';
        elseif ($day_score >= 40)  $grade = 'C';

        $this->db->query("
            UPDATE line_manager_scorecard
               SET day_score = ?, grade = ?
             WHERE manager_uid = ? AND quarter_config_id = ?
               AND week_start = ?
        ", [$day_score, $grade, $manager_uid, $qc_id, $week_start]);
    }

    // ------------------------------------------------------------------------
    // APPLY DEDUCTIONS TO PAYOUT (called by IncentiveEngine after weekly close).
    // ------------------------------------------------------------------------
    public function apply_deductions_to_payout($payout_log_id, $manager_uid, $week_start)
    {
        $this->db->query("
            UPDATE incentive_deduction
               SET applied_to_payout = 1,
                   payout_log_id = ?
             WHERE manager_uid = ?
               AND week_start = ?
               AND applied_to_payout = 0
        ", [$payout_log_id, $manager_uid, $week_start]);
        return $this->db->affected_rows();
    }

    // ------------------------------------------------------------------------
    // BATCH: recompute all managers for current week.
    // Called by cron at 1 AM IST nightly.
    // ------------------------------------------------------------------------
    public function recompute_all_this_week()
    {
        $qc_row = $this->db->query("
            SELECT id FROM v_current_quarter LIMIT 1
        ")->row_array();
        if (!$qc_row) return ['error' => 'no_current_quarter'];

        $managers = $this->db->query("
            SELECT DISTINCT parent_uid AS manager_uid
              FROM reporting_hierarchy
             WHERE active = 1 AND parent_uid IS NOT NULL
        ")->result_array();

        $out = [];
        foreach ($managers as $m) {
            $out[] = $this->recompute_manager((int)$m['manager_uid'], (int)$qc_row['id']);
        }
        return ['count' => count($out), 'ran_at' => date('c'), 'rows' => $out];
    }
}

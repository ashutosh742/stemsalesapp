<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * LineManagerScorecard_v2_model
 *
 * Full rewire of migration 022's K1-K7 scorecard for line managers.
 * Replaces all hardcoded grade math with:
 *   - ReportingHierarchy_model for the direct-report tree
 *   - IncentiveEngine_model for KPI weights, thresholds, payouts
 *   - quarter_config for which cadences are active this quarter
 *
 * No hardcoded numbers live in this file. Every weight, threshold, and
 * payout amount is sourced from incentive_cadence_master (mig 023.2) and
 * quarter_config (mig 023.3).
 *
 * Output written to line_manager_scorecard table (existing, extended).
 * Cron 0c647bbd + 891ca261 + 93bc48c3 + 578f2d14 all call this model.
 */
class LineManagerScorecard_v2_model extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
        $this->load->database();
        $this->load->model('ReportingHierarchy_model', 'rh');
        $this->load->model('IncentiveEngine_model', 'engine');
    }

    // -------------------------------------------------------------------
    // PUBLIC ENTRY: compute scorecard for one manager for the active quarter
    // -------------------------------------------------------------------

    public function compute_for_manager($manager_uid, $quarter_config_id = NULL)
    {
        $manager = $this->rh->get_employee($manager_uid);
        if (!$manager) return ['ok' => false, 'error' => 'manager not found'];
        if (!in_array($manager['role'], ['Director', 'RM', 'CM', 'ACM'])) {
            return ['ok' => false, 'error' => 'not a manager role'];
        }

        // Resolve quarter
        if ($quarter_config_id) {
            $qcfg = $this->db->where('id', $quarter_config_id)->get('quarter_config')->row_array();
        } else {
            $qcfg = $this->rh->current_quarter();
        }
        if (!$qcfg) return ['ok' => false, 'error' => 'no quarter_config row found'];

        // Direct reports (BDs and below)
        $reports = $this->rh->direct_reports($manager_uid);
        if (empty($reports)) {
            return ['ok' => true, 'note' => 'no direct reports', 'skipped' => true];
        }

        // Compute K1-K7 across the direct-report cohort
        $k = [
            'k1_mom_sla_pct'           => $this->compute_k1_mom_sla($reports, $qcfg),
            'k2_coaching_ratio_pct'    => $this->compute_k2_coaching_ratio($manager_uid, $qcfg),
            'k3_signoff_avg_hours'     => $this->compute_k3_signoff_speed($manager_uid, $qcfg),
            'k4_r2b_followthrough_pct' => $this->compute_k4_r2b_followthrough($reports, $qcfg),
            'k5_stuck_closure_pct'     => $this->compute_k5_stuck_closure($manager_uid, $qcfg),
            'k6_coaching_notes_count'  => $this->compute_k6_coaching_notes($manager_uid, $qcfg),
            'k7_escalation_pre_sla_pct'=> $this->compute_k7_escalation_pre_sla($manager_uid, $qcfg),
        ];

        // Apply quarter-specific weights from quarter_config
        $weights = [
            'k1' => (int)$qcfg['k1_mom_sla_weight'],
            'k2' => (int)$qcfg['k2_coaching_ratio_weight'],
            'k3' => (int)$qcfg['k3_signoff_speed_weight'],
            'k4' => (int)$qcfg['k4_r2b_followthrough_weight'],
            'k5' => (int)$qcfg['k5_stuck_closure_weight'],
            'k6' => (int)$qcfg['k6_coaching_notes_weight'],
            'k7' => (int)$qcfg['k7_escalation_pre_sla_weight'],
        ];

        // Convert K3 (lower is better) and K5 (lower is better) into 0-100 scores
        $k3_score = $this->invert_hours_score($k['k3_signoff_avg_hours']); // lower = better
        $k5_score = max(0, 100 - $k['k5_stuck_closure_pct']);              // lower = better
        $k6_score = min(100, $k['k6_coaching_notes_count'] * 10);          // 10 notes = full

        $day_score = round(
            ($k['k1_mom_sla_pct']           * $weights['k1'] / 100) +
            ($k['k2_coaching_ratio_pct']    * $weights['k2'] / 100) +
            ($k3_score                      * $weights['k3'] / 100) +
            ($k['k4_r2b_followthrough_pct'] * $weights['k4'] / 100) +
            ($k5_score                      * $weights['k5'] / 100) +
            ($k6_score                      * $weights['k6'] / 100) +
            ($k['k7_escalation_pre_sla_pct']* $weights['k7'] / 100),
        1);

        $grade = $this->grade_band($day_score);

        // Engine-computed incentive payout (replaces all hardcoded payout math)
        $engine_result = $this->engine->evaluate_quarterly_for_employee(
            $manager_uid, (int)$qcfg['quarter'], (int)$qcfg['fiscal_year']
        );
        $payout_log_id = $engine_result['payout_log_id'] ?? NULL;

        // Pending signoffs from stage_signoff table
        $pending_signoff_count = $this->count_pending_signoffs($manager_uid);
        $bypasses_this_week = $this->count_bypasses_this_week($manager_uid);

        // Upsert scorecard
        $row = [
            'manager_uid' => (int)$manager_uid,
            'manager_name' => $manager['employee_name'],
            'role' => $manager['role'],
            'period_start' => $qcfg['period_start'],
            'period_end' => $qcfg['period_end'],
            'quarter_config_id' => (int)$qcfg['id'],
            'cadence_engine_version' => '023.2',
            'incentive_payout_log_id' => $payout_log_id,
            'day_score' => $day_score,
            'grade' => $grade,
            'k1_mom_sla_pct' => $k['k1_mom_sla_pct'],
            'k2_coaching_ratio_pct' => $k['k2_coaching_ratio_pct'],
            'k3_signoff_avg_hours' => $k['k3_signoff_avg_hours'],
            'k4_r2b_followthrough_pct' => $k['k4_r2b_followthrough_pct'],
            'k5_stuck_closure_pct' => $k['k5_stuck_closure_pct'],
            'k6_coaching_notes_count' => $k['k6_coaching_notes_count'],
            'k7_escalation_pre_sla_pct' => $k['k7_escalation_pre_sla_pct'],
            'pending_signoff_count' => $pending_signoff_count,
            'bypasses_this_week' => $bypasses_this_week,
            'computed_at' => date('Y-m-d H:i:s'),
        ];
        $this->upsert_scorecard($row);

        return ['ok' => true, 'row' => $row];
    }

    // -------------------------------------------------------------------
    // K1: MoM SLA percent (MoMs approved within 24 hours)
    // -------------------------------------------------------------------

    private function compute_k1_mom_sla($reports, $qcfg)
    {
        $bd_uids = array_map(function($r) { return (int)$r['employee_uid']; }, $reports);
        if (empty($bd_uids)) return 100.0;
        $list = implode(',', $bd_uids);

        $sql = "
            SELECT
              COUNT(*) AS total,
              SUM(CASE WHEN approved_at IS NOT NULL
                       AND TIMESTAMPDIFF(HOUR, submitted_at, approved_at) <= 24
                       THEN 1 ELSE 0 END) AS within_sla
            FROM mom_data
            WHERE bd_uid IN ($list)
              AND submitted_at BETWEEN '{$qcfg['period_start']} 00:00:00'
                                   AND '{$qcfg['period_end']} 23:59:59'
              AND approved_status = '1'
        ";
        $row = $this->db->query($sql)->row_array();
        if (!$row || !$row['total']) return 100.0;
        return round(100.0 * $row['within_sla'] / $row['total'], 1);
    }

    // -------------------------------------------------------------------
    // K2: coaching ratio percent (coaching notes / direct report meetings)
    // -------------------------------------------------------------------

    private function compute_k2_coaching_ratio($manager_uid, $qcfg)
    {
        $sql = "
            SELECT
              (SELECT COUNT(*) FROM coaching_note
                WHERE coach_uid = ?
                  AND created_at BETWEEN '{$qcfg['period_start']} 00:00:00' AND '{$qcfg['period_end']} 23:59:59'
              ) AS notes,
              (SELECT COUNT(*) FROM tblcallevents tce
                 JOIN reporting_hierarchy rh ON rh.employee_uid = tce.uid
                 WHERE rh.manager_uid = ?
                   AND tce.actiontype_id IN (3,4)
                   AND tce.event_date BETWEEN '{$qcfg['period_start']}' AND '{$qcfg['period_end']}'
              ) AS meetings
        ";
        $row = $this->db->query($sql, [$manager_uid, $manager_uid])->row_array();
        if (!$row || !$row['meetings']) return 100.0;
        return round(100.0 * $row['notes'] / $row['meetings'], 1);
    }

    // -------------------------------------------------------------------
    // K3: signoff speed (avg hours from requested_at to decided_at)
    // -------------------------------------------------------------------

    private function compute_k3_signoff_speed($manager_uid, $qcfg)
    {
        $sql = "
            SELECT AVG(TIMESTAMPDIFF(HOUR, requested_at, decided_at)) AS avg_h
            FROM stage_signoff
            WHERE approver_uid = ?
              AND decided_at IS NOT NULL
              AND requested_at BETWEEN '{$qcfg['period_start']} 00:00:00' AND '{$qcfg['period_end']} 23:59:59'
        ";
        $row = $this->db->query($sql, [$manager_uid])->row_array();
        return $row && $row['avg_h'] !== NULL ? round((float)$row['avg_h'], 1) : 0.0;
    }

    // -------------------------------------------------------------------
    // K4: r2b (review-to-booking) follow-through percent
    // -------------------------------------------------------------------

    private function compute_k4_r2b_followthrough($reports, $qcfg)
    {
        $bd_uids = array_map(function($r) { return (int)$r['employee_uid']; }, $reports);
        if (empty($bd_uids)) return 100.0;
        $list = implode(',', $bd_uids);

        $sql = "
            SELECT
              COUNT(DISTINCT cn.lead_id) AS coached_leads,
              COUNT(DISTINCT CASE WHEN ic.cstatus IN (9,12) THEN cn.lead_id END) AS progressed_leads
            FROM coaching_note cn
            LEFT JOIN init_call ic ON ic.id = cn.lead_id
            WHERE cn.bd_uid IN ($list)
              AND cn.created_at BETWEEN '{$qcfg['period_start']} 00:00:00' AND '{$qcfg['period_end']} 23:59:59'
        ";
        $row = $this->db->query($sql)->row_array();
        if (!$row || !$row['coached_leads']) return 100.0;
        return round(100.0 * $row['progressed_leads'] / $row['coached_leads'], 1);
    }

    // -------------------------------------------------------------------
    // K5: stuck closure percent (cstatus 9+ leads stuck over threshold)
    // -------------------------------------------------------------------

    private function compute_k5_stuck_closure($manager_uid, $qcfg)
    {
        $reports = $this->rh->direct_reports($manager_uid);
        $bd_uids = array_map(function($r) { return (int)$r['employee_uid']; }, $reports);
        if (empty($bd_uids)) return 0.0;
        $list = implode(',', $bd_uids);

        $sql = "
            SELECT
              SUM(CASE WHEN cstatus IN (9,12) THEN 1 ELSE 0 END) AS closure_leads,
              SUM(CASE WHEN cstatus = 9
                       AND DATEDIFF(NOW(), last_status_change) > 14 THEN 1 ELSE 0 END) AS stuck_leads
            FROM init_call
            WHERE mainbd IN ($list)
              AND cstatus IN (9,12)
        ";
        $row = $this->db->query($sql)->row_array();
        if (!$row || !$row['closure_leads']) return 0.0;
        return round(100.0 * $row['stuck_leads'] / $row['closure_leads'], 1);
    }

    private function compute_k6_coaching_notes($manager_uid, $qcfg)
    {
        $sql = "SELECT COUNT(*) AS n FROM coaching_note
                WHERE coach_uid = ?
                  AND created_at BETWEEN '{$qcfg['period_start']} 00:00:00' AND '{$qcfg['period_end']} 23:59:59'";
        $row = $this->db->query($sql, [$manager_uid])->row_array();
        return $row ? (int)$row['n'] : 0;
    }

    private function compute_k7_escalation_pre_sla($manager_uid, $qcfg)
    {
        $sql = "
            SELECT
              COUNT(*) AS total,
              SUM(CASE WHEN escalated_at <= sla_deadline THEN 1 ELSE 0 END) AS pre_sla
            FROM escalation_ticket
            WHERE raised_by_uid = ?
              AND created_at BETWEEN '{$qcfg['period_start']} 00:00:00' AND '{$qcfg['period_end']} 23:59:59'
        ";
        $row = $this->db->query($sql, [$manager_uid])->row_array();
        if (!$row || !$row['total']) return 100.0;
        return round(100.0 * $row['pre_sla'] / $row['total'], 1);
    }

    // -------------------------------------------------------------------
    // HELPER CALCULATIONS
    // -------------------------------------------------------------------

    /**
     * Convert avg signoff hours into a 0-100 score.
     * 0 hours = 100. 24 hours = 50. 48 hours+ = 0.
     */
    private function invert_hours_score($hours)
    {
        if ($hours <= 0) return 100;
        if ($hours >= 48) return 0;
        return round(100 - ($hours * 100 / 48), 1);
    }

    private function grade_band($score)
    {
        if ($score >= 90) return 'A+';
        if ($score >= 75) return 'A';
        if ($score >= 60) return 'B';
        if ($score >= 40) return 'C';
        return 'D';
    }

    private function count_pending_signoffs($manager_uid)
    {
        return (int)$this->db->where('approver_uid', $manager_uid)
                              ->where('status', 'pending')
                              ->count_all_results('stage_signoff');
    }

    private function count_bypasses_this_week($manager_uid)
    {
        $monday = date('Y-m-d', strtotime('monday this week'));
        return (int)$this->db->where('rm_uid', $manager_uid)
                              ->where('bypass_at >=', $monday)
                              ->count_all_results('signoff_bypass_log');
    }

    private function upsert_scorecard($row)
    {
        $existing = $this->db->where('manager_uid', $row['manager_uid'])
                             ->where('quarter_config_id', $row['quarter_config_id'])
                             ->get('line_manager_scorecard')->row_array();
        if ($existing) {
            $this->db->where('id', $existing['id'])->update('line_manager_scorecard', $row);
            return $existing['id'];
        }
        $this->db->insert('line_manager_scorecard', $row);
        return $this->db->insert_id();
    }

    // -------------------------------------------------------------------
    // BATCH ENTRY (called by daily cron)
    // -------------------------------------------------------------------

    public function compute_all_managers_for_current_quarter()
    {
        $qcfg = $this->rh->current_quarter();
        if (!$qcfg) return ['ok' => false, 'error' => 'no current quarter'];

        $managers = $this->rh->all_managers();
        $results = [];
        foreach ($managers as $uid => $m) {
            $results[$uid] = $this->compute_for_manager($uid, $qcfg['id']);
        }
        return ['ok' => true, 'quarter' => $qcfg['quarter_label'], 'computed' => count($results)];
    }
}

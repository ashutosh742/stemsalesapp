<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * LineManagerScorecard_model
 *
 * Migration 022 - daily and weekly KPI rollup for CM, ACM, RM, SH.
 *
 * Reads from: lead_stage_signoff, mom_data, init_call, escalation_ticket,
 *             signoff_bypass_log, tblcallevents.
 * Writes to:  line_manager_scorecard_daily.
 *
 * Author: STEM Build Agent. Date: 16 May 2026. Staging only until 18 May.
 */
class LineManagerScorecard_model extends CI_Model {

    // ---- KPI thresholds (founder-locked, do NOT change without runbook update)
    const MOM_CUTOFF_HOUR_IST = 19;     // 19:00 IST is the same-day cutoff
    const SIGNOFF_BREACH_HOURS = 48;
    const R2B_FOLLOW_DAYS = 7;
    const VP_STUCK_DAYS = 14;
    const COACHING_TARGET_PCT = 80;
    const PRE_SLA_TARGET_PCT = 90;

    public function __construct() {
        parent::__construct();
        $this->load->database();
    }

    /**
     * Refresh daily scorecard for a single manager and date.
     * Idempotent - upserts on (manager_uid, score_date).
     */
    public function refresh_daily($manager_uid, $score_date) {
        $manager = $this->_load_manager($manager_uid);
        if (!$manager) {
            return ['ok' => false, 'error' => 'manager_not_found'];
        }
        $role = $manager['role'];   // CM, ACM, RM, SH
        $cluster_id = $manager['cluster_id'];

        // K1 MoM SLA - approved by 19:00 IST same day
        $k1 = $this->_k1_mom_sla($manager_uid, $score_date);

        // K2 + K6 coaching ratio
        $k26 = $this->_k2_k6_coaching($manager_uid, $score_date);

        // K3 signoff turnaround on G1-G4
        $k3 = $this->_k3_signoff_turnaround($manager_uid, $score_date);

        // K4 R2B follow-through for cstatus 6 leads in cluster
        $k4 = $this->_k4_r2b_follow($manager_uid, $cluster_id, $score_date);

        // K5 stuck closure ratio - cstatus 9 over 14d no next_decision_date
        $k5 = $this->_k5_stuck_closure($manager_uid, $cluster_id, $score_date);

        // K7 escalation pre-SLA rate
        $k7 = $this->_k7_escalation_pre_sla($manager_uid, $score_date);

        // RM bypass count (only counted if manager is RM)
        $bypasses = ($role === 'RM') ? $this->_bypass_count_for_rm($manager_uid, $score_date) : 0;

        $day_score = $this->_compute_day_score(array_merge(
            $k1, $k26, $k3, $k4, $k5, $k7, ['bypasses_today' => $bypasses]
        ));

        $row = array_merge([
            'manager_uid'  => (int)$manager_uid,
            'manager_role' => $role,
            'cluster_id'   => $cluster_id,
            'score_date'   => $score_date,
            'bypasses_today' => $bypasses,
            'day_score'    => $day_score,
            'computed_at'  => date('Y-m-d H:i:s'),
        ], $k1, $k26, $k3, $k4, $k5, $k7);

        $this->_upsert_daily($row);
        return ['ok' => true, 'day_score' => $day_score, 'row' => $row];
    }

    /**
     * Refresh all active managers for a date.
     * Called by 7:30 audit cron 0c647bbd.
     */
    public function refresh_all($score_date) {
        $managers = $this->db->select('uid')
            ->from('user')
            ->where_in('type_id', [13, 22, 23, 27]) // CM, ACM, RM, SH
            ->where('active', 1)
            ->get()
            ->result_array();
        $results = [];
        foreach ($managers as $m) {
            $results[$m['uid']] = $this->refresh_daily((int)$m['uid'], $score_date);
        }
        return $results;
    }

    /**
     * Scorecard for one manager, with optional date range.
     */
    public function scorecard($manager_uid, $from = null, $to = null) {
        if (!$from) $from = date('Y-m-d', strtotime('monday this week'));
        if (!$to)   $to   = date('Y-m-d');
        $rows = $this->db->where('manager_uid', $manager_uid)
            ->where('score_date >=', $from)
            ->where('score_date <=', $to)
            ->order_by('score_date', 'ASC')
            ->get('line_manager_scorecard_daily')
            ->result_array();
        $weekly = $this->_summarise_week($rows);
        return [
            'manager_uid' => (int)$manager_uid,
            'from'        => $from,
            'to'          => $to,
            'days'        => $rows,
            'weekly'      => $weekly,
        ];
    }

    /**
     * RM view - all CMs in their cluster, weekly grades.
     */
    public function team_scorecard($rm_uid, $from = null, $to = null) {
        if (!$from) $from = date('Y-m-d', strtotime('monday this week'));
        if (!$to)   $to   = date('Y-m-d');
        $cluster = $this->_cluster_for_rm($rm_uid);
        if (!$cluster) return ['rm_uid' => $rm_uid, 'cms' => [], 'error' => 'no_cluster'];
        $cm_rows = $this->db->select('uid, fname, type_id')
            ->from('user')
            ->where('cluster_id', $cluster)
            ->where_in('type_id', [13, 22, 27]) // CM, ACM, SH within cluster
            ->where('active', 1)
            ->get()->result_array();
        $out = ['rm_uid' => $rm_uid, 'cluster_id' => $cluster, 'cms' => []];
        foreach ($cm_rows as $cm) {
            $sc = $this->scorecard((int)$cm['uid'], $from, $to);
            $sc['name'] = $cm['fname'];
            $out['cms'][] = $sc;
        }
        return $out;
    }

    /**
     * Org leaderboard sorted by weekly score.
     */
    public function leaderboard($from = null, $to = null, $limit = 50) {
        if (!$from) $from = date('Y-m-d', strtotime('monday this week'));
        if (!$to)   $to   = date('Y-m-d');
        $sql = "
            SELECT s.manager_uid, s.manager_role, s.cluster_id, u.fname AS manager_name,
                   ROUND(AVG(s.day_score), 2) AS weekly_avg_score,
                   CASE
                     WHEN AVG(s.day_score) >= 90 THEN 'A+'
                     WHEN AVG(s.day_score) >= 75 THEN 'A'
                     WHEN AVG(s.day_score) >= 60 THEN 'B'
                     WHEN AVG(s.day_score) >= 40 THEN 'C'
                     ELSE 'D'
                   END AS weekly_grade,
                   SUM(s.mom_sla_breaches) AS sla_breaches,
                   SUM(s.signoffs_over_48h) AS signoffs_over_48h,
                   SUM(s.bypasses_today) AS bypasses
            FROM line_manager_scorecard_daily s
            LEFT JOIN user u ON u.uid = s.manager_uid
            WHERE s.score_date BETWEEN ? AND ?
            GROUP BY s.manager_uid, s.manager_role, s.cluster_id, u.fname
            ORDER BY weekly_avg_score DESC
            LIMIT ?
        ";
        return $this->db->query($sql, [$from, $to, (int)$limit])->result_array();
    }

    // -----------------------------------------------------------------
    // KPI computations
    // -----------------------------------------------------------------

    private function _k1_mom_sla($manager_uid, $date) {
        // Decided MoMs: approved_status IS NOT NULL on the score date for this CM
        $cutoff = $date . ' 19:00:00';
        $total = (int)$this->db->where('approved_by', $manager_uid)
            ->where("DATE(approved_at) = ", $date, false)
            ->count_all_results('mom_data');
        $by_1900 = (int)$this->db->where('approved_by', $manager_uid)
            ->where("DATE(approved_at) = ", $date, false)
            ->where('approved_at <=', $cutoff)
            ->count_all_results('mom_data');
        $breaches = $total - $by_1900;
        $pct = $total > 0 ? round(($by_1900 / $total) * 100, 2) : null;
        return [
            'moms_decided_total'  => $total,
            'moms_decided_by_1900'=> $by_1900,
            'mom_sla_pct'         => $pct,
            'mom_sla_breaches'    => max(0, $breaches),
        ];
    }

    private function _k2_k6_coaching($manager_uid, $date) {
        // C and D grade MoMs decided today and whether coaching_note exists
        $sql = "
            SELECT m.mom_quality_grade,
                   m.approved_status,
                   CASE WHEN m.cm_coaching_note IS NOT NULL AND CHAR_LENGTH(m.cm_coaching_note) > 10 THEN 1 ELSE 0 END AS has_note
            FROM mom_data m
            WHERE m.approved_by = ?
              AND DATE(m.approved_at) = ?
              AND m.mom_quality_grade IN ('C','D')
        ";
        $rows = $this->db->query($sql, [$manager_uid, $date])->result_array();
        $cd_total = count($rows);
        $with_note = 0; $approved_no_note = 0; $sent_back_with_note = 0;
        foreach ($rows as $r) {
            if ($r['has_note']) $with_note++;
            // approved_status = 'NO RP' is the production typo for rejected/sent-back
            if ($r['approved_status'] === '1' && !$r['has_note']) $approved_no_note++;
            if ($r['approved_status'] === 'NO RP' && $r['has_note']) $sent_back_with_note++;
        }
        $pct = $cd_total > 0 ? round(($with_note / $cd_total) * 100, 2) : null;
        return [
            'cd_moms_total'          => $cd_total,
            'cd_moms_with_coaching_note' => $with_note,
            'cd_moms_approved_no_note'   => $approved_no_note,
            'coaching_ratio_pct'     => $pct,
            'moms_sent_back_with_note' => $sent_back_with_note,
        ];
    }

    private function _k3_signoff_turnaround($manager_uid, $date) {
        // Signoffs decided today by this CM
        $sql = "
            SELECT TIMESTAMPDIFF(HOUR, requested_at, decided_at) AS dur_hours
            FROM lead_stage_signoff
            WHERE decided_by_uid = ?
              AND DATE(decided_at) = ?
              AND status IN ('approved','rejected','request_edit')
        ";
        $rows = $this->db->query($sql, [$manager_uid, $date])->result_array();
        $count = count($rows);
        $over_48 = 0; $sum = 0;
        foreach ($rows as $r) {
            $h = (int)$r['dur_hours'];
            $sum += $h;
            if ($h > self::SIGNOFF_BREACH_HOURS) $over_48++;
        }
        $avg = $count > 0 ? round($sum / $count, 2) : null;
        return [
            'signoffs_decided'   => $count,
            'signoffs_over_48h'  => $over_48,
            'signoff_avg_hours'  => $avg,
        ];
    }

    private function _k4_r2b_follow($manager_uid, $cluster_id, $date) {
        // Cstatus 6 leads in this CM's cluster as of $date
        // R2B shared within 7 days of entering cstatus 6
        if (!$cluster_id) return $this->_k4_zero();
        $sql = "
            SELECT ic.id,
                   ic.r2b_status,
                   ic.r2b_shared_at,
                   COALESCE(lpl.created_at, ic.createDate) AS entered_cstatus6_at
            FROM init_call ic
            LEFT JOIN (
                SELECT cid_id, MIN(created_at) AS created_at
                FROM lead_progression_log
                WHERE to_cstatus = 6
                GROUP BY cid_id
            ) lpl ON lpl.cid_id = ic.id
            WHERE ic.cstatus = 6
              AND ic.cluster_id = ?
              AND DATE(COALESCE(lpl.created_at, ic.createDate)) <= ?
        ";
        $rows = $this->db->query($sql, [$cluster_id, $date])->result_array();
        $total = count($rows);
        $shared_within_7d = 0; $stuck_over_7d = 0;
        foreach ($rows as $r) {
            $entered = strtotime($r['entered_cstatus6_at']);
            if (in_array($r['r2b_status'], ['shared','accepted_with_changes','accepted'])
                && $r['r2b_shared_at']
                && (strtotime($r['r2b_shared_at']) - $entered) <= 7 * 86400) {
                $shared_within_7d++;
            } else if ((time() - $entered) > 7 * 86400 && empty($r['r2b_shared_at'])) {
                $stuck_over_7d++;
            }
        }
        $pct = $total > 0 ? round(($shared_within_7d / $total) * 100, 2) : null;
        return [
            'cstatus6_leads_in_cluster'      => $total,
            'cstatus6_r2b_shared_within_7d'  => $shared_within_7d,
            'r2b_follow_through_pct'         => $pct,
            'cstatus6_stuck_over_7d'         => $stuck_over_7d,
        ];
    }

    private function _k4_zero() {
        return [
            'cstatus6_leads_in_cluster' => 0,
            'cstatus6_r2b_shared_within_7d' => 0,
            'r2b_follow_through_pct' => null,
            'cstatus6_stuck_over_7d' => 0,
        ];
    }

    private function _k5_stuck_closure($manager_uid, $cluster_id, $date) {
        if (!$cluster_id) return [
            'cstatus9_leads_in_cluster' => 0,
            'cstatus9_over_14d_no_date' => 0,
            'stuck_closure_pct' => null,
        ];
        $sql = "
            SELECT ic.id, ic.next_decision_date,
                   COALESCE(lpl.created_at, ic.createDate) AS entered_at
            FROM init_call ic
            LEFT JOIN (
                SELECT cid_id, MAX(created_at) AS created_at
                FROM lead_progression_log
                WHERE to_cstatus = 9
                GROUP BY cid_id
            ) lpl ON lpl.cid_id = ic.id
            WHERE ic.cstatus = 9
              AND ic.cluster_id = ?
        ";
        $rows = $this->db->query($sql, [$cluster_id])->result_array();
        $total = count($rows);
        $stuck = 0;
        foreach ($rows as $r) {
            $age_days = (time() - strtotime($r['entered_at'])) / 86400;
            if ($age_days > self::VP_STUCK_DAYS && empty($r['next_decision_date'])) {
                $stuck++;
            }
        }
        $pct = $total > 0 ? round(($stuck / $total) * 100, 2) : null;
        return [
            'cstatus9_leads_in_cluster' => $total,
            'cstatus9_over_14d_no_date' => $stuck,
            'stuck_closure_pct' => $pct,
        ];
    }

    private function _k7_escalation_pre_sla($manager_uid, $date) {
        $sql = "
            SELECT pre_sla_resolved, status
            FROM escalation_ticket
            WHERE current_handler_uid = ?
              AND DATE(COALESCE(resolved_at, NOW())) = ?
              AND status IN ('resolved','escalated_up','breached')
        ";
        $rows = $this->db->query($sql, [$manager_uid, $date])->result_array();
        $total = count($rows); $pre = 0; $post = 0;
        foreach ($rows as $r) {
            if ((int)$r['pre_sla_resolved'] === 1) $pre++;
            else $post++;
        }
        $pct = $total > 0 ? round(($pre / $total) * 100, 2) : null;
        return [
            'escalations_resolved_or_up' => $total,
            'escalations_resolved_pre_sla' => $pre,
            'escalations_post_breach' => $post,
            'pre_sla_pct' => $pct,
        ];
    }

    private function _bypass_count_for_rm($rm_uid, $date) {
        return (int)$this->db->where('rm_uid', $rm_uid)
            ->where("DATE(bypassed_at) = ", $date, false)
            ->count_all_results('signoff_bypass_log');
    }

    // -----------------------------------------------------------------
    // Day score formula (locked - see spec Section 4)
    // -----------------------------------------------------------------
    private function _compute_day_score($r) {
        $s = 100;
        $s -= 5 * (int)($r['mom_sla_breaches'] ?? 0);
        $s += 2 * (int)($r['moms_sent_back_with_note'] ?? 0);
        $s -= 3 * (int)($r['cd_moms_approved_no_note'] ?? 0);
        $s -= 5 * (int)($r['signoffs_over_48h'] ?? 0);
        $s -= 3 * (int)($r['cstatus6_stuck_over_7d'] ?? 0);
        $s -= 5 * (int)($r['cstatus9_over_14d_no_date'] ?? 0);
        $s -= 3 * (int)($r['escalations_post_breach'] ?? 0);
        $s -= 3 * (int)($r['bypasses_today'] ?? 0);
        return max(0, min(100, $s));
    }

    private function _summarise_week($rows) {
        if (empty($rows)) return null;
        $sum = 0; $n = 0;
        $totals = [
            'mom_sla_breaches' => 0,
            'signoffs_over_48h' => 0,
            'cd_moms_approved_no_note' => 0,
            'cstatus6_stuck_over_7d' => 0,
            'cstatus9_over_14d_no_date' => 0,
            'escalations_post_breach' => 0,
            'bypasses_today' => 0,
        ];
        foreach ($rows as $r) {
            $sum += (int)$r['day_score'];
            $n++;
            foreach ($totals as $k => $v) $totals[$k] += (int)$r[$k];
        }
        $avg = $n > 0 ? round($sum / $n, 2) : 0;
        $grade = 'D';
        if ($avg >= 90) $grade = 'A+';
        elseif ($avg >= 75) $grade = 'A';
        elseif ($avg >= 60) $grade = 'B';
        elseif ($avg >= 40) $grade = 'C';
        return array_merge([
            'weekly_avg_score' => $avg,
            'weekly_grade'     => $grade,
            'days_recorded'    => $n,
        ], $totals);
    }

    private function _load_manager($uid) {
        $row = $this->db->select('uid, type_id, cluster_id, fname')
            ->where('uid', $uid)
            ->get('user')
            ->row_array();
        if (!$row) return null;
        $role_map = [13 => 'CM', 22 => 'ACM', 23 => 'RM', 27 => 'SH'];
        return [
            'uid'        => (int)$row['uid'],
            'role'       => $role_map[(int)$row['type_id']] ?? 'CM',
            'cluster_id' => $row['cluster_id'] ? (int)$row['cluster_id'] : null,
            'fname'      => $row['fname'],
        ];
    }

    private function _cluster_for_rm($rm_uid) {
        $r = $this->db->select('cluster_id')->where('uid', $rm_uid)->get('user')->row_array();
        return $r ? (int)$r['cluster_id'] : null;
    }

    private function _upsert_daily($row) {
        $this->db->replace('line_manager_scorecard_daily', $row);
    }
}

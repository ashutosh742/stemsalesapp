<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * PlanExecutionAnalysis_model - Agent (additive, 2026-06-06)
 *
 * Analyses the gap between what was PLANNED and what was EXECUTED, plus the
 * resulting status changes. Built entirely on real tables:
 *   - planner_log: planned tasks, "Plan But Not Initiated" / "Because of Plan
 *     Change" / "Future Task" remarks, org_task_date vs new_task_date (slippage).
 *   - sales_status_change_task_star_rating (scc): real old_status -> new_status
 *     transitions with date and user.
 *   - status: status code -> name master.
 *
 * No mock data. ASCII only, "Rs" for rupees, "percent" spelled out.
 */
class PlanExecutionAnalysis_model extends CI_Model {

    public function manifest() {
        $pl = (int)$this->db->query("SELECT COUNT(*) c FROM planner_log")->row()->c;
        $sc = (int)$this->db->query("SELECT COUNT(*) c FROM sales_status_change_task_star_rating")->row()->c;
        return array(
            'feature'       => 'plan_execution_analysis',
            'source_tables' => array('planner_log','sales_status_change_task_star_rating','status'),
            'planner_rows'  => $pl,
            'status_change_rows' => $sc,
            'deployed_at'   => '2026-06-06',
        );
    }

    private function _date_filter($alias, $col, $sdate, $edate, &$params) {
        $f = '';
        if ($sdate) { $f .= " AND {$alias}.{$col} >= ? "; $params[] = $sdate . ' 00:00:00'; }
        if ($edate) { $f .= " AND {$alias}.{$col} <= ? "; $params[] = $edate . ' 23:59:59'; }
        return $f;
    }

    /**
     * Headline plan-vs-execution metrics. Optional date range + bd (to_user).
     */
    public function summary($sdate = null, $edate = null, $bd_uid = 0) {
        $bd_uid = (int)$bd_uid;
        $params = array();
        $bdf = ''; if ($bd_uid > 0) { $bdf = " AND pl.to_user = ? "; }
        $datef = $this->_date_filter('pl', 're_created_at', $sdate, $edate, $params);
        if ($bd_uid > 0) $params[] = $bd_uid;

        $sql = "SELECT
                  COUNT(*) AS planned_total,
                  SUM(pl.remarks = 'Plan But Not Initiated') AS not_initiated,
                  SUM(pl.remarks = 'Because of Plan Change') AS plan_changed,
                  SUM(pl.remarks = 'Future Task') AS future_task,
                  SUM(pl.org_task_date IS NOT NULL AND pl.new_task_date IS NOT NULL
                      AND DATEDIFF(pl.new_task_date, pl.org_task_date) > 0) AS rescheduled_later,
                  AVG(CASE WHEN pl.org_task_date IS NOT NULL AND pl.new_task_date IS NOT NULL
                      THEN GREATEST(DATEDIFF(pl.new_task_date, pl.org_task_date),0) END) AS avg_slip_days
                FROM planner_log pl
                WHERE 1=1 " . $datef . $bdf;
        $r = $this->db->query($sql, $params)->row_array();

        $planned = (int)$r['planned_total'];
        $not_init = (int)$r['not_initiated'];
        $initiated = max($planned - $not_init, 0);
        $exec_rate = $planned > 0 ? round($initiated * 100.0 / $planned, 1) : 0.0;

        return array(
            'planned_total'      => $planned,
            'initiated'          => $initiated,
            'not_initiated'      => $not_init,
            'plan_changed'       => (int)$r['plan_changed'],
            'future_task'        => (int)$r['future_task'],
            'rescheduled_later'  => (int)$r['rescheduled_later'],
            'avg_slip_days'      => $r['avg_slip_days'] === null ? 0.0 : (float)number_format((float)$r['avg_slip_days'],1,'.',''),
            'execution_rate_pct' => (float)number_format($exec_rate,1,'.',''),
            'not_initiated_pct'  => $planned > 0 ? (float)number_format($not_init * 100.0 / $planned, 1,'.','') : 0.0,
            'insight'            => $this->_insight($exec_rate, (float)($r['avg_slip_days'] ?? 0)),
        );
    }

    private function _insight($exec_rate, $slip) {
        $msgs = array();
        if ($exec_rate < 20) $msgs[] = 'Execution rate is very low (' . $exec_rate . ' percent). Most planned tasks are not being initiated.';
        else if ($exec_rate < 50) $msgs[] = 'Execution rate is below half (' . $exec_rate . ' percent). Plan discipline needs attention.';
        else $msgs[] = 'Execution rate is ' . $exec_rate . ' percent.';
        if ($slip >= 7) $msgs[] = 'Average reschedule slippage is ' . round($slip,1) . ' days, indicating chronic deferral.';
        return implode(' ', $msgs);
    }

    /**
     * Status-change analysis: real old_status -> new_status transitions.
     */
    public function status_changes($sdate = null, $edate = null, $bd_uid = 0, $limit = 30) {
        $bd_uid = (int)$bd_uid; $limit = (int)$limit;
        if ($limit <= 0 || $limit > 200) $limit = 30;
        $params = array();
        $datef = $this->_date_filter('sc', 'date', $sdate, $edate, $params);
        $bdf = ''; if ($bd_uid > 0) { $bdf = " AND sc.user_id = ? "; $params[] = $bd_uid; }
        $sql = "SELECT sc.old_status, sc.new_status,
                       COALESCE(so.name, CONCAT('status ', sc.old_status)) AS from_status,
                       COALESCE(sn.name, CONCAT('status ', sc.new_status)) AS to_status,
                       COUNT(*) AS cnt
                FROM sales_status_change_task_star_rating sc
                LEFT JOIN status so ON so.id = sc.old_status
                LEFT JOIN status sn ON sn.id = sc.new_status
                WHERE sc.old_status IS NOT NULL AND sc.new_status IS NOT NULL " . $datef . $bdf . "
                GROUP BY sc.old_status, sc.new_status
                ORDER BY cnt DESC LIMIT " . $limit;
        $rows = $this->db->query($sql, $params)->result_array();
        $out = array();
        $forward = 0; $backward = 0; $total = 0;
        foreach ($rows as $r) {
            $c = (int)$r['cnt']; $total += $c;
            $dir = ((int)$r['new_status'] > (int)$r['old_status']) ? 'forward' : (((int)$r['new_status'] < (int)$r['old_status']) ? 'backward' : 'same');
            if ($dir === 'forward') $forward += $c; else if ($dir === 'backward') $backward += $c;
            $out[] = array(
                'from'        => $r['from_status'],
                'to'          => $r['to_status'],
                'count'       => $c,
                'direction'   => $dir,
            );
        }
        return array(
            'transitions'    => $out,
            'total_changes'  => $total,
            'forward'        => $forward,
            'backward'       => $backward,
            'progression_pct'=> $total > 0 ? (float)number_format($forward * 100.0 / $total, 1,'.','') : 0.0,
        );
    }

    /**
     * Per-lead plan-to-execution trail (uses planner_log.init_id).
     */
    public function for_lead($init_id, $limit = 50) {
        $init_id = (int)$init_id; $limit = (int)$limit;
        if ($limit <= 0 || $limit > 200) $limit = 50;
        $rows = $this->db->query("
            SELECT pl.task_id, pl.remarks, pl.org_task_date, pl.new_task_date, pl.re_created_at,
                   CASE WHEN pl.org_task_date IS NOT NULL AND pl.new_task_date IS NOT NULL
                        THEN GREATEST(DATEDIFF(pl.new_task_date, pl.org_task_date),0) END AS slip_days
            FROM planner_log pl
            WHERE pl.init_id = ?
            ORDER BY pl.id DESC LIMIT " . $limit, array($init_id))->result_array();
        return $rows;
    }
}

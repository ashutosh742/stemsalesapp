<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * AnomalyWatchdog_model - Agent (additive, 2026-06-06)
 *
 * Flags anomalous BD productivity days from the real bd_productivity_daily
 * table (85 rows). No mock data.
 *
 * Anomaly rules (each row evaluated):
 *   - score_pct = 0 with budget_min > 0  -> "Zero productivity score"
 *   - idle_min  >= 0.6 * budget_min      -> "Excessive idle time"
 *   - tasks_planned > 0 and tasks_completed = 0 -> "Planned but completed none"
 *   - executed_min = 0 with budget_min > 0 -> "No executed minutes"
 *
 * Severity = number of rules tripped.
 * Standing rules: ASCII only, "Rs" for rupees, "percent" spelled out.
 */
class AnomalyWatchdog_model extends CI_Model {

    public function manifest() {
        $n = (int)$this->db->query("SELECT COUNT(*) c FROM bd_productivity_daily")->row()->c;
        $latest = $this->db->query("SELECT MAX(for_date) d FROM bd_productivity_daily")->row();
        return array(
            'feature'       => 'anomaly_watchdog',
            'source_table'  => 'bd_productivity_daily',
            'rows'          => $n,
            'latest_date'   => $latest ? $latest->d : null,
            'rules'         => array('zero_score','excessive_idle','zero_completion','no_execution'),
            'deployed_at'   => '2026-06-06',
        );
    }

    /** Detect anomalies. Optional date filter (YYYY-MM-DD) and bd_uid. */
    public function detect($for_date = null, $bd_uid = 0, $limit = 100) {
        $bd_uid = (int)$bd_uid; $limit = (int)$limit;
        $where = "1=1"; $params = array();
        if ($for_date) { $where .= " AND for_date = ?"; $params[] = $for_date; }
        if ($bd_uid > 0) { $where .= " AND bd_uid = ?"; $params[] = $bd_uid; }
        $rows = $this->db->query("
            SELECT bd_uid, for_date, planned_min, executed_min, idle_min, budget_min,
                   score_pct, tasks_planned, tasks_completed, tasks_skipped
            FROM bd_productivity_daily
            WHERE $where
            ORDER BY for_date DESC, bd_uid ASC
            LIMIT 2000", $params)->result_array();

        $out = array();
        foreach ($rows as $r) {
            $flags = array();
            $budget = (int)$r['budget_min'];
            if ((float)$r['score_pct'] == 0 && $budget > 0) $flags[] = 'Zero productivity score';
            if ($budget > 0 && (int)$r['idle_min'] >= 0.6 * $budget) $flags[] = 'Excessive idle time';
            if ((int)$r['tasks_planned'] > 0 && (int)$r['tasks_completed'] == 0) $flags[] = 'Planned but completed none';
            if ((int)$r['executed_min'] == 0 && $budget > 0) $flags[] = 'No executed minutes';
            if (empty($flags)) continue;
            $out[] = array(
                'bd_uid'          => (int)$r['bd_uid'],
                'for_date'        => $r['for_date'],
                'score_pct'       => (float)$r['score_pct'],
                'idle_min'        => (int)$r['idle_min'],
                'budget_min'      => $budget,
                'executed_min'    => (int)$r['executed_min'],
                'tasks_planned'   => (int)$r['tasks_planned'],
                'tasks_completed' => (int)$r['tasks_completed'],
                'severity'        => count($flags),
                'anomalies'       => $flags,
            );
        }
        usort($out, function($a, $b) { return $b['severity'] - $a['severity']; });
        return array_slice($out, 0, $limit);
    }

    public function summary($for_date = null) {
        $rows = $this->detect($for_date, 0, 5000);
        $bySev = array(1=>0,2=>0,3=>0,4=>0);
        foreach ($rows as $r) { $bySev[$r['severity']] = ($bySev[$r['severity']] ?? 0) + 1; }
        return array(
            'total_anomalies' => count($rows),
            'by_severity'     => $bySev,
        );
    }
}

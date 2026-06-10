<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * TargetScenarios_model - Migration 059
 *
 * Forecast scenario support for TargetDashboardScreen gap item I.4.
 *
 * Scenarios:
 *   best     - 1.15x of scenario_expected (stretch / upside)
 *   expected - base plan, mirrors target_rs at migration time
 *   commit   - 0.85x of scenario_expected (floor / conservative)
 *
 * Pace band thresholds (consistent with migration 023 and v_target_scenarios_current):
 *   on_pace  - achieved_pct >= elapsed_pct - 5
 *   behind   - achieved_pct >= elapsed_pct - 20
 *   critical - otherwise
 *
 * CodeIgniter 3. Plain English. Rs for rupees. No em-dashes. No non-ASCII.
 *
 * Location: ./application/models/AIAgents/TargetScenarios_model.php
 */
class TargetScenarios_model extends CI_Model {

    const BEST_MULTIPLIER   = 1.15;
    const COMMIT_MULTIPLIER = 0.85;

    public function __construct() {
        parent::__construct();
        // RevenueTarget_model carries current_fy_quarter() which we reuse.
        $this->load->model('AIAgents/RevenueTarget_model', 'rtgt');
    }

    // -------------------------------------------------------------------------
    // PUBLIC: compute_scenarios
    // -------------------------------------------------------------------------

    /**
     * Compute aggregate scenarios for a given quarter and level.
     *
     * @param string $quarter  Fiscal quarter key such as 'FY27Q1', or 'current'
     *                         to resolve automatically.
     * @param string $level    Aggregation scope: 'org' (national), 'cluster',
     *                         'BD', 'RM', 'CM'. Only 'org' aggregates all rows;
     *                         other values are accepted for symmetry with the
     *                         burn_down API but still return org-level totals
     *                         unless a uid filter is supplied (see
     *                         get_scenarios_burndown).
     * @return array {
     *   quarter, level, actual_rs, elapsed_pct,
     *   best, expected, commit,
     *   pace_band_best, pace_band_expected, pace_band_commit
     * }
     */
    public function compute_scenarios($quarter, $level) {
        $q_key = $this->_resolve_quarter($quarter);
        $cur   = $this->rtgt->current_fy_quarter();
        $elapsed_pct = $this->_quarter_elapsed_pct($q_key, $cur);

        // Sum scenario targets across all matrix rows for the resolved quarter.
        $agg_sql = "
            SELECT
                SUM(scenario_best)     AS sum_best,
                SUM(scenario_expected) AS sum_expected,
                SUM(scenario_commit)   AS sum_commit
            FROM revenue_target_matrix
            WHERE fiscal_quarter = ?
        ";
        $row = $this->db->query($agg_sql, array($q_key))->row_array();

        $sum_best     = (float)($row['sum_best']     ?? 0);
        $sum_expected = (float)($row['sum_expected'] ?? 0);
        $sum_commit   = (float)($row['sum_commit']   ?? 0);

        // Actual closed revenue for that quarter.
        $actual_sql = "
            SELECT COALESCE(SUM(contract_value_rs), 0) AS actual_rs
            FROM revenue_actual_ledger
            WHERE fiscal_quarter = ?
        ";
        $actual_rs = (float)$this->db->query($actual_sql, array($q_key))->row()->actual_rs;

        $pct_of_best     = ($sum_best     > 0) ? round(100 * $actual_rs / $sum_best,     2) : 0.0;
        $pct_of_expected = ($sum_expected > 0) ? round(100 * $actual_rs / $sum_expected, 2) : 0.0;
        $pct_of_commit   = ($sum_commit   > 0) ? round(100 * $actual_rs / $sum_commit,   2) : 0.0;

        return array(
            'quarter'            => $q_key,
            'level'              => $level,
            'actual_rs'          => $actual_rs,
            'actual_rs_cr'       => $this->_to_cr($actual_rs),
            'elapsed_pct'        => $elapsed_pct,
            'best'               => array(
                'scenario_rs'    => $sum_best,
                'scenario_rs_cr' => $this->_to_cr($sum_best),
                'achieved_pct'   => $pct_of_best,
            ),
            'expected'           => array(
                'scenario_rs'    => $sum_expected,
                'scenario_rs_cr' => $this->_to_cr($sum_expected),
                'achieved_pct'   => $pct_of_expected,
            ),
            'commit'             => array(
                'scenario_rs'    => $sum_commit,
                'scenario_rs_cr' => $this->_to_cr($sum_commit),
                'achieved_pct'   => $pct_of_commit,
            ),
            'pace_band_best'     => $this->_pace_band($pct_of_best,     $elapsed_pct),
            'pace_band_expected' => $this->_pace_band($pct_of_expected, $elapsed_pct),
            'pace_band_commit'   => $this->_pace_band($pct_of_commit,   $elapsed_pct),
        );
    }

    // -------------------------------------------------------------------------
    // PUBLIC: get_scenarios_burndown
    // -------------------------------------------------------------------------

    /**
     * Weekly burn-down series, identical in structure to
     * RevenueTarget_model::burn_down(), but the scenario target column for
     * each week is scaled by the appropriate multiplier so the mobile chart
     * receives a straight-line reference for whichever scenario is active.
     *
     * @param string $level    'BD', 'RM', 'CM', or 'org'. Controls optional
     *                         uid filter on revenue_actual_ledger.
     * @param int|null $rm_uid RM uid for filtering. Pass null for org rollup.
     * @param string $scenario 'best' | 'expected' | 'commit'.
     *                         Determines the target reference line multiplier.
     * @return array {
     *   scenario, level, rm_uid, multiplier,
     *   weeks: [ {iso_yw, week_start, week_actual_rs, week_scenario_target_rs} ]
     * }
     */
    public function get_scenarios_burndown($level, $rm_uid, $scenario = 'expected') {
        $scenario = $this->_clean_scenario($scenario);
        $mult     = $this->_scenario_multiplier($scenario);
        $cur      = $this->rtgt->current_fy_quarter();

        // Base weekly actuals query. Filter by rm_uid when provided.
        if (!empty($rm_uid) && strtolower($level) !== 'org') {
            $col_map = array(
                'RM' => 'rm_uid',
                'CM' => 'cm_uid',
                'BD' => 'bd_uid',
            );
            $uid_col = $col_map[strtoupper($level)] ?? 'bd_uid';

            $weeks_sql = "
                SELECT
                    YEARWEEK(won_at, 3)    AS iso_yw,
                    MIN(DATE(won_at))      AS week_start,
                    SUM(contract_value_rs) AS week_actual_rs
                FROM revenue_actual_ledger
                WHERE fiscal_quarter = ?
                  AND {$uid_col} = ?
                  AND won_at >= DATE_SUB(NOW(), INTERVAL 13 WEEK)
                GROUP BY YEARWEEK(won_at, 3)
                ORDER BY iso_yw ASC
            ";
            $weeks = $this->db->query($weeks_sql, array($cur['fy'] . 'Q' . $cur['quarter'], (int)$rm_uid))->result_array();
        } else {
            $weeks_sql = "
                SELECT
                    YEARWEEK(won_at, 3)    AS iso_yw,
                    MIN(DATE(won_at))      AS week_start,
                    SUM(contract_value_rs) AS week_actual_rs
                FROM revenue_actual_ledger
                WHERE fiscal_quarter = ?
                  AND won_at >= DATE_SUB(NOW(), INTERVAL 13 WEEK)
                GROUP BY YEARWEEK(won_at, 3)
                ORDER BY iso_yw ASC
            ";
            $weeks = $this->db->query($weeks_sql, array($cur['fy'] . 'Q' . $cur['quarter']))->result_array();
        }

        // Compute scenario target for the whole quarter then divide by
        // the number of weeks in the quarter to get a per-week reference.
        // 13 weeks per quarter is the working assumption (standard ISO).
        $q_key = $cur['fy'] . 'Q' . $cur['quarter'];

        if (!empty($rm_uid) && strtolower($level) !== 'org') {
            $uid_col = $col_map[strtoupper($level)] ?? 'bd_uid';
            $qsql = "
                SELECT SUM(scenario_expected) AS sum_exp
                FROM revenue_target_matrix
                WHERE fiscal_quarter = ?
                  AND owner_rm_uid = ?
            ";
            $q_exp = (float)$this->db->query($qsql, array($q_key, (int)$rm_uid))->row()->sum_exp;
        } else {
            $qsql = "
                SELECT SUM(scenario_expected) AS sum_exp
                FROM revenue_target_matrix
                WHERE fiscal_quarter = ?
            ";
            $q_exp = (float)$this->db->query($qsql, array($q_key))->row()->sum_exp;
        }

        $q_scenario_target = round($q_exp * $mult, 2);
        $weeks_in_quarter  = 13;
        $week_scenario_rs  = round($q_scenario_target / $weeks_in_quarter, 2);

        // Annotate each week with the per-week scenario reference line.
        foreach ($weeks as &$w) {
            $w['week_actual_rs']          = (float)$w['week_actual_rs'];
            $w['week_scenario_target_rs'] = $week_scenario_rs;
        }
        unset($w);

        return array(
            'scenario'               => $scenario,
            'level'                  => $level,
            'rm_uid'                 => $rm_uid,
            'multiplier'             => $mult,
            'q_scenario_target_rs'   => $q_scenario_target,
            'q_scenario_target_rs_cr'=> $this->_to_cr($q_scenario_target),
            'weeks'                  => $weeks,
        );
    }

    // -------------------------------------------------------------------------
    // PRIVATE helpers
    // -------------------------------------------------------------------------

    /**
     * Resolve 'current' to the actual FY+Q key string, e.g. 'FY27Q2'.
     */
    private function _resolve_quarter($quarter) {
        if ($quarter === 'current' || empty($quarter)) {
            $cur = $this->rtgt->current_fy_quarter();
            return $cur['fy'] . 'Q' . $cur['quarter'];
        }
        return strtoupper(trim($quarter));
    }

    /**
     * Elapsed percent of the given quarter as of today.
     * Uses the same day-count rules as migration 023.
     */
    private function _quarter_elapsed_pct($q_key, $cur) {
        $bounds = array(
            'FY27Q1' => array('2026-04-01', 91),
            'FY27Q2' => array('2026-07-01', 92),
            'FY27Q3' => array('2026-10-01', 92),
            'FY27Q4' => array('2027-01-01', 90),
        );
        if (!isset($bounds[$q_key])) {
            // Fall through to live calculation for non-FY27 quarters.
            $q_start_ts = strtotime($cur['q_start']);
            $q_end_ts   = strtotime($cur['q_end']);
            $now_ts     = time();
            return max(0, min(100, round(100 * ($now_ts - $q_start_ts) / max(1, $q_end_ts - $q_start_ts + 86400), 2)));
        }
        list($start_str, $days) = $bounds[$q_key];
        $elapsed = max(0, min($days, (int)floor((time() - strtotime($start_str)) / 86400)));
        return round(100 * $elapsed / $days, 2);
    }

    /**
     * Pace band: on_pace, behind, or critical.
     */
    private function _pace_band($achieved_pct, $elapsed_pct) {
        $gap = $achieved_pct - $elapsed_pct;
        if ($gap >= -5)  return 'on_pace';
        if ($gap >= -20) return 'behind';
        return 'critical';
    }

    /**
     * Validate and normalise scenario string.
     */
    private function _clean_scenario($scenario) {
        $allowed = array('best', 'expected', 'commit');
        $s = strtolower(trim($scenario));
        return in_array($s, $allowed) ? $s : 'expected';
    }

    /**
     * Return the Rs multiplier for a named scenario.
     */
    private function _scenario_multiplier($scenario) {
        switch ($scenario) {
            case 'best':   return self::BEST_MULTIPLIER;
            case 'commit': return self::COMMIT_MULTIPLIER;
            default:       return 1.0;
        }
    }

    /**
     * Convert Rs integer to crore string with 2 dp.
     */
    private function _to_cr($rs) {
        return number_format((float)$rs / 10000000, 2);
    }
}

/* End of file TargetScenarios_model.php */
/* Location: ./application/models/AIAgents/TargetScenarios_model.php */

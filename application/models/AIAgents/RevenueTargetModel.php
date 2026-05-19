<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * RevenueTarget_model - Migration 023
 *
 * Rs 200 crore organisation target for FY27 (Apr 2026 - Mar 2027).
 *
 * Matrix is 128 rows: 8 clusters x 4 categories (PSU, ANCHOR, DMFT, STANDARD) x 4 quarters.
 *
 * Quarterly split (locked):
 *   Q1 15.0 percent (Rs 30 cr) - Apr-Jun
 *   Q2 25.0 percent (Rs 50 cr) - Jul-Sep
 *   Q3 30.5 percent (Rs 61 cr) - Oct-Dec
 *   Q4 29.5 percent (Rs 59 cr) - Jan-Mar
 *
 * Category split (locked, of Rs 200 cr):
 *   PSU      Rs 80 cr (40 percent)
 *   ANCHOR   Rs 60 cr (30 percent)
 *   DMFT     Rs 30 cr (15 percent)
 *   STANDARD Rs 30 cr (15 percent)
 *
 * Cluster split (locked annual share):
 *   Mumbai     Rs 50 cr (25 percent) - pilot anchor
 *   Delhi      Rs 30 cr (15 percent)
 *   Pune       Rs 24 cr (12 percent)
 *   Bangalore  Rs 24 cr (12 percent)
 *   Hyderabad  Rs 20 cr (10 percent)
 *   Chennai    Rs 20 cr (10 percent)
 *   Kolkata    Rs 16 cr (8 percent)
 *   Ahmedabad  Rs 16 cr (8 percent)
 *
 * Pacing flag rule (computed at read time):
 *   pct_of_quarter_elapsed = days_elapsed_in_quarter / total_days_in_quarter
 *   if achieved_pct >= pct_of_quarter_elapsed - 0.05 then on_pace
 *   elseif achieved_pct >= pct_of_quarter_elapsed - 0.20 then behind
 *   else critical
 */
class RevenueTarget_model extends CI_Model {

    const FISCAL_YEAR_START_MONTH = 4; // April

    public function __construct() {
        parent::__construct();
    }

    /**
     * Compute current fiscal quarter (1-4) and fiscal year string.
     * @return array {fy: 'FY27', quarter: 1, q_start: 'YYYY-MM-DD', q_end: 'YYYY-MM-DD'}
     */
    public function current_fy_quarter() {
        $now = time();
        $month = (int)date('n', $now);
        $year  = (int)date('Y', $now);
        // Fiscal year is Apr-Mar. FY27 = Apr 2026 to Mar 2027.
        $fy_start_year = ($month >= self::FISCAL_YEAR_START_MONTH) ? $year : $year - 1;
        $fy_label = 'FY' . substr((string)($fy_start_year + 1), -2);

        $months_into_fy = ($month - self::FISCAL_YEAR_START_MONTH + 12) % 12;
        $quarter = intval($months_into_fy / 3) + 1;

        $q_start_month = self::FISCAL_YEAR_START_MONTH + ($quarter - 1) * 3;
        $q_start_year = $fy_start_year;
        if ($q_start_month > 12) {
            $q_start_month -= 12;
            $q_start_year += 1;
        }
        $q_start = sprintf('%04d-%02d-01', $q_start_year, $q_start_month);
        $q_end_dt = strtotime($q_start . ' +3 months -1 day');
        $q_end = date('Y-m-d', $q_end_dt);

        return array(
            'fy'       => $fy_label,
            'quarter'  => $quarter,
            'q_start'  => $q_start,
            'q_end'    => $q_end,
        );
    }

    /**
     * Full Rs 200 cr matrix with actuals joined.
     */
    public function full_matrix($fy = null) {
        if ($fy === null) {
            $cur = $this->current_fy_quarter();
            $fy = $cur['fy'];
        }
        $sql = "
            SELECT
                m.cluster_id, m.category_code, m.fiscal_quarter,
                m.target_rs, m.cluster_name,
                COALESCE(actual.actual_rs, 0) AS actual_rs,
                ROUND(COALESCE(actual.actual_rs,0) / NULLIF(m.target_rs,0) * 100, 2) AS achieved_pct
            FROM revenue_target_matrix m
            LEFT JOIN (
                SELECT cluster_id, category_code, fiscal_quarter,
                       SUM(closed_value_rs) AS actual_rs
                FROM revenue_actual_ledger
                WHERE fiscal_year = ?
                GROUP BY cluster_id, category_code, fiscal_quarter
            ) actual ON actual.cluster_id = m.cluster_id
                    AND actual.category_code = m.category_code
                    AND actual.fiscal_quarter = m.fiscal_quarter
            WHERE m.fiscal_year = ?
            ORDER BY m.cluster_id, FIELD(m.category_code,'PSU','ANCHOR','DMFT','STANDARD'), m.fiscal_quarter
        ";
        return $this->db->query($sql, array($fy, $fy))->result_array();
    }

    /**
     * Roll up by cluster (for cluster x fy view).
     */
    public function by_cluster($cluster_id, $fy = null) {
        if ($fy === null) $fy = $this->current_fy_quarter()['fy'];
        $sql = "
            SELECT category_code, fiscal_quarter, target_rs,
                   (SELECT COALESCE(SUM(closed_value_rs),0)
                    FROM revenue_actual_ledger r
                    WHERE r.cluster_id = m.cluster_id
                      AND r.category_code = m.category_code
                      AND r.fiscal_quarter = m.fiscal_quarter
                      AND r.fiscal_year = m.fiscal_year) AS actual_rs
            FROM revenue_target_matrix m
            WHERE m.cluster_id = ? AND m.fiscal_year = ?
            ORDER BY FIELD(category_code,'PSU','ANCHOR','DMFT','STANDARD'), fiscal_quarter
        ";
        return $this->db->query($sql, array((int)$cluster_id, $fy))->result_array();
    }

    /**
     * Roll up by category (national view).
     */
    public function by_category($category_code, $fy = null) {
        if ($fy === null) $fy = $this->current_fy_quarter()['fy'];
        $category_code = strtoupper($category_code);
        if (!in_array($category_code, array('PSU','ANCHOR','DMFT','STANDARD'))) {
            return array();
        }
        $sql = "
            SELECT m.cluster_id, m.cluster_name, m.fiscal_quarter, m.target_rs,
                   COALESCE(actual.actual_rs, 0) AS actual_rs
            FROM revenue_target_matrix m
            LEFT JOIN (
                SELECT cluster_id, fiscal_quarter, SUM(closed_value_rs) AS actual_rs
                FROM revenue_actual_ledger
                WHERE category_code = ? AND fiscal_year = ?
                GROUP BY cluster_id, fiscal_quarter
            ) actual ON actual.cluster_id = m.cluster_id AND actual.fiscal_quarter = m.fiscal_quarter
            WHERE m.category_code = ? AND m.fiscal_year = ?
            ORDER BY m.cluster_id, m.fiscal_quarter
        ";
        return $this->db->query($sql, array($category_code, $fy, $category_code, $fy))->result_array();
    }

    /**
     * National headline: total target, total actual, pacing band, headline string.
     * Used by 77b08026 morning brief, 578f2d14 weekly.
     */
    public function national_headline($fy = null) {
        $cur = $this->current_fy_quarter();
        if ($fy === null) $fy = $cur['fy'];

        $sql = "
            SELECT
                SUM(target_rs) AS total_target_rs,
                (SELECT COALESCE(SUM(closed_value_rs),0)
                 FROM revenue_actual_ledger WHERE fiscal_year = ?) AS total_actual_rs
            FROM revenue_target_matrix WHERE fiscal_year = ?
        ";
        $row = $this->db->query($sql, array($fy, $fy))->row_array();

        $target_rs = (float)$row['total_target_rs'];
        $actual_rs = (float)$row['total_actual_rs'];
        $achieved_pct = ($target_rs > 0) ? round(100 * $actual_rs / $target_rs, 2) : 0.0;

        // FY-elapsed pct vs achieved pct = pacing
        $fy_start = strtotime($fy === $cur['fy']
            ? date('Y-04-01', strtotime($cur['q_start']) - 86400 * 90)
            : '2026-04-01');
        $fy_start_str = $fy === $cur['fy']
            ? sprintf('%04d-04-01', (int)date('Y', $fy_start))
            : '2026-04-01';
        $fy_start_ts = strtotime($fy_start_str);
        $fy_end_ts   = strtotime($fy_start_str . ' +1 year -1 day');
        $now_ts      = time();
        $fy_elapsed_pct = max(0, min(100, round(100 * ($now_ts - $fy_start_ts) / max(1, $fy_end_ts - $fy_start_ts), 2)));

        $pacing = $this->_pacing_flag($achieved_pct, $fy_elapsed_pct);

        // Quarter-level
        $q_target_sql = "SELECT SUM(target_rs) AS t FROM revenue_target_matrix WHERE fiscal_year=? AND fiscal_quarter=?";
        $q_target = (float)$this->db->query($q_target_sql, array($fy, $cur['quarter']))->row()->t;
        $q_actual_sql = "SELECT COALESCE(SUM(closed_value_rs),0) AS a FROM revenue_actual_ledger WHERE fiscal_year=? AND fiscal_quarter=?";
        $q_actual = (float)$this->db->query($q_actual_sql, array($fy, $cur['quarter']))->row()->a;
        $q_achieved_pct = ($q_target > 0) ? round(100 * $q_actual / $q_target, 2) : 0.0;

        $q_start_ts = strtotime($cur['q_start']);
        $q_end_ts   = strtotime($cur['q_end']);
        $q_elapsed_pct = max(0, min(100, round(100 * ($now_ts - $q_start_ts) / max(1, $q_end_ts - $q_start_ts + 86400), 2)));
        $q_pacing = $this->_pacing_flag($q_achieved_pct, $q_elapsed_pct);

        // Critical cells in current quarter (achieved_pct - elapsed_pct < -20)
        $critical_sql = "
            SELECT m.cluster_name, m.category_code, m.target_rs,
                   COALESCE(r.actual_rs, 0) AS actual_rs,
                   ROUND(COALESCE(r.actual_rs,0)/NULLIF(m.target_rs,0)*100, 2) AS achieved_pct
            FROM revenue_target_matrix m
            LEFT JOIN (
                SELECT cluster_id, category_code, SUM(closed_value_rs) AS actual_rs
                FROM revenue_actual_ledger
                WHERE fiscal_year=? AND fiscal_quarter=?
                GROUP BY cluster_id, category_code
            ) r ON r.cluster_id=m.cluster_id AND r.category_code=m.category_code
            WHERE m.fiscal_year=? AND m.fiscal_quarter=?
              AND (COALESCE(r.actual_rs,0)/NULLIF(m.target_rs,0)*100) < ? - 20
            ORDER BY (m.target_rs - COALESCE(r.actual_rs,0)) DESC
            LIMIT 12
        ";
        $critical = $this->db->query($critical_sql, array($fy, $cur['quarter'], $fy, $cur['quarter'], $q_elapsed_pct))->result_array();

        return array(
            'fy'                  => $fy,
            'current_quarter'     => $cur['quarter'],
            'total_target_rs'     => $target_rs,
            'total_actual_rs'     => $actual_rs,
            'achieved_pct'        => $achieved_pct,
            'fy_elapsed_pct'      => $fy_elapsed_pct,
            'pacing'              => $pacing,
            'q_target_rs'         => $q_target,
            'q_actual_rs'         => $q_actual,
            'q_achieved_pct'      => $q_achieved_pct,
            'q_elapsed_pct'       => $q_elapsed_pct,
            'q_pacing'            => $q_pacing,
            'critical_cells'      => $critical,
            'headline_line'       => sprintf(
                'FY %s burn: Rs %s cr of %s cr (%s percent). Q%d at %s percent. %d cells critical.',
                $fy,
                $this->_to_cr($actual_rs),
                $this->_to_cr($target_rs),
                $achieved_pct,
                $cur['quarter'],
                $q_achieved_pct,
                count($critical)
            ),
        );
    }

    /**
     * Weekly burn-down for 578f2d14 Monday cron.
     * Last 8 weeks of actuals vs straight-line target.
     */
    public function burn_down($fy = null) {
        if ($fy === null) $fy = $this->current_fy_quarter()['fy'];
        $sql = "
            SELECT
                YEARWEEK(signoff_at, 3) AS iso_yw,
                MIN(DATE(signoff_at)) AS week_start,
                SUM(closed_value_rs) AS week_actual_rs
            FROM revenue_actual_ledger
            WHERE fiscal_year = ?
              AND signoff_at >= DATE_SUB(NOW(), INTERVAL 8 WEEK)
            GROUP BY YEARWEEK(signoff_at, 3)
            ORDER BY iso_yw ASC
        ";
        return $this->db->query($sql, array($fy))->result_array();
    }

    /**
     * Category gap RED flag - cells under 70 percent of pro-rated quarterly target.
     */
    public function critical_category_gaps($fy = null) {
        $cur = $this->current_fy_quarter();
        if ($fy === null) $fy = $cur['fy'];
        $q_start_ts = strtotime($cur['q_start']);
        $q_end_ts   = strtotime($cur['q_end']);
        $now_ts     = time();
        $q_elapsed_pct = max(0, min(100, round(100 * ($now_ts - $q_start_ts) / max(1, $q_end_ts - $q_start_ts + 86400), 2)));

        $sql = "
            SELECT m.cluster_id, m.cluster_name, m.category_code, m.target_rs,
                   COALESCE(r.actual_rs, 0) AS actual_rs,
                   ROUND(COALESCE(r.actual_rs,0)/NULLIF(m.target_rs,0)*100, 2) AS achieved_pct,
                   ? AS elapsed_pct
            FROM revenue_target_matrix m
            LEFT JOIN (
                SELECT cluster_id, category_code, SUM(closed_value_rs) AS actual_rs
                FROM revenue_actual_ledger
                WHERE fiscal_year=? AND fiscal_quarter=?
                GROUP BY cluster_id, category_code
            ) r ON r.cluster_id=m.cluster_id AND r.category_code=m.category_code
            WHERE m.fiscal_year=? AND m.fiscal_quarter=?
              AND (COALESCE(r.actual_rs,0)/NULLIF(m.target_rs,0)*100) < (? * 0.7)
            ORDER BY (m.target_rs - COALESCE(r.actual_rs,0)) DESC
        ";
        return $this->db->query($sql, array(
            $q_elapsed_pct, $fy, $cur['quarter'], $fy, $cur['quarter'], $q_elapsed_pct
        ))->result_array();
    }

    private function _pacing_flag($achieved_pct, $elapsed_pct) {
        $gap = $achieved_pct - $elapsed_pct;
        if ($gap >= -5)  return 'on_pace';
        if ($gap >= -20) return 'behind';
        return 'critical';
    }

    private function _to_cr($rs) {
        return number_format($rs / 10000000, 2);
    }
}

/* End of file RevenueTarget_model.php */
/* Location: ./application/models/AIAgents/RevenueTarget_model.php */

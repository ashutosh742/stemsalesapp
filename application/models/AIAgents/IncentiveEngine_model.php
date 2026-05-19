<?php
// ============================================================================
// STEM CRM Incentive Engine (Migration 023.2)
// ============================================================================
// File: application/models/AIAgents/IncentiveEngine_model.php
//
// PURPOSE: Single source of truth for computing incentive payouts from the
// incentive_cadence_master and incentive_split_rule tables seeded by
// Final-Incentive-Sheet.xlsx. Crons call this. Never hard-code grade math.
//
// All amounts in Rs. Plain English. No em-dashes. No non-ASCII.
// Preserves production typos: budgt, Compny, Quater, "Barg in Meeting".
// ============================================================================

defined('BASEPATH') OR exit('No direct script access allowed');

class IncentiveEngine_model extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
        $this->load->database();
    }

    // ------------------------------------------------------------------
    // PUBLIC API
    // ------------------------------------------------------------------

    /**
     * Compute Sales Closure incentive for one closed deal.
     * Called by ConversionApi on cstatus -> 12 (Won) transition.
     *
     * @param int   $lead_id      init_call.id
     * @param float $stem_booking Amount qualifying for incentive (post-GST, post-NGO)
     * @param array $chain        ['bd'=>uid,'acm'=>uid,'cm'=>uid,'rm'=>uid]
     * @param string $deal_type   'new_client'|'upsell'|'dmft'|'psu'|'anchor'
     * @return array              ['split_rule'=>..., 'payouts'=>[uid=>rs,...]]
     */
    public function compute_sales_closure_incentive($lead_id, $stem_booking, $chain, $deal_type='new_client')
    {
        $rule = $this->_resolve_split_rule($chain, $deal_type);
        if (!$rule) {
            log_message('error', 'IncentiveEngine: no split rule for deal_type='.$deal_type);
            return ['split_rule' => null, 'payouts' => []];
        }

        $qualifying = floatval($stem_booking);
        $payouts = [];

        if (!empty($chain['bd']) && $rule['bd_pct'] > 0) {
            $payouts[$chain['bd']] = round($qualifying * $rule['bd_pct'], 2);
        }
        if (!empty($chain['acm']) && $rule['acm_pct'] > 0) {
            $payouts[$chain['acm']] = round($qualifying * $rule['acm_pct'], 2);
        }
        if (!empty($chain['cm']) && $rule['cm_pct'] > 0) {
            $payouts[$chain['cm']] = round($qualifying * $rule['cm_pct'], 2);
        }
        if (!empty($chain['rm']) && $rule['rm_pct'] > 0) {
            $payouts[$chain['rm']] = round($qualifying * $rule['rm_pct'], 2);
        }

        $cadence_id = $this->_find_cadence_id_for_closure();
        foreach ($payouts as $uid => $rs) {
            $this->_log_payout([
                'employee_uid'      => $uid,
                'role_code'         => $this->_role_from_chain($uid, $chain),
                'cluster_id'        => $this->_cluster_of_lead($lead_id),
                'cadence_id'        => $cadence_id,
                'fy_year'           => $this->_current_fy(),
                'quarter'           => $this->_current_quarter(),
                'threshold_required'=> 1,
                'threshold_achieved'=> 1,
                'achieved_pct'      => 100,
                'is_eligible'       => 1,
                'amount_eligible_rs'=> $rs,
                'amount_paid_rs'    => 0,
                'payout_status'     => 'computed',
                'notes'             => 'Lead '.$lead_id.' booking Rs '.$qualifying.' rule '.$rule['rule_code'],
            ]);
        }

        return ['split_rule' => $rule, 'payouts' => $payouts];
    }

    /**
     * Evaluate quarterly performance cadences for one employee.
     * Called by Q3/Q4 end-of-quarter crons.
     *
     * @param int    $uid
     * @param string $role     BD|CM|ACM|RM|SC|PC
     * @param string $fy_year
     * @param string $quarter
     * @return array of cadence results
     */
    public function evaluate_quarterly_for_employee($uid, $role, $fy_year, $quarter)
    {
        $cadences = $this->_load_cadences_for_role($role);
        $results = [];

        foreach ($cadences as $cad) {
            // Skip cadences not active in this quarter
            if (!$this->_is_cadence_in_window($cad, $quarter)) {
                continue;
            }

            $achieved = $this->_measure_achievement($uid, $cad, $fy_year, $quarter);
            $eligible = $this->_is_threshold_met($cad, $achieved);

            $payout_rs = $eligible ? floatval($cad['payout_amount_rs']) : 0;

            $this->_log_payout([
                'employee_uid'      => $uid,
                'role_code'         => $role,
                'cluster_id'        => $this->_cluster_of_user($uid),
                'cadence_id'        => $cad['cadence_id'],
                'fy_year'           => $fy_year,
                'quarter'           => $quarter,
                'threshold_required'=> $cad['threshold_value'],
                'threshold_achieved'=> $achieved['value'],
                'achieved_pct'      => $achieved['pct'],
                'is_eligible'       => $eligible ? 1 : 0,
                'amount_eligible_rs'=> $payout_rs,
                'amount_paid_rs'    => 0,
                'payout_status'     => 'computed',
                'notes'             => 'Quarterly evaluation. Cadence '.$cad['cadence_code'],
            ]);

            $results[] = [
                'cadence_code'  => $cad['cadence_code'],
                'cadence_label' => $cad['cadence_label'],
                'threshold'     => $cad['threshold_value'].' '.$cad['threshold_unit'],
                'achieved'      => $achieved['value'],
                'achieved_pct'  => $achieved['pct'],
                'eligible'      => $eligible,
                'payout_rs'     => $payout_rs,
            ];
        }

        return $results;
    }

    /**
     * Evaluate daily discipline + huddle cadences (SC + BD).
     * Called by 7:30 BD audit cron.
     */
    public function evaluate_daily_for_employee($uid, $role, $date_yyyymmdd)
    {
        $cadences = $this->_load_cadences_for_role($role, 'daily');
        $results = [];

        foreach ($cadences as $cad) {
            $achieved = $this->_measure_daily($uid, $cad, $date_yyyymmdd);
            $eligible = $this->_is_threshold_met($cad, $achieved);

            // Daily readings accumulate in incentive_daily_log; only the
            // quarterly aggregate writes to incentive_payout_log.
            $this->db->insert('incentive_daily_log', [
                'employee_uid'  => $uid,
                'cadence_id'    => $cad['cadence_id'],
                'reading_date'  => $date_yyyymmdd,
                'achieved_pct'  => $achieved['pct'],
                'is_eligible'   => $eligible ? 1 : 0,
                'created_at'    => date('Y-m-d H:i:s'),
            ]);

            $results[] = [
                'cadence_code' => $cad['cadence_code'],
                'achieved_pct' => $achieved['pct'],
                'eligible'     => $eligible,
            ];
        }

        return $results;
    }

    /**
     * Used by 0c647bbd 7:30 BD audit cron for the "Planning grade" block.
     * Returns the cadence label and Rs amount a BD is currently tracking
     * toward, plus how close they are.
     */
    public function get_employee_current_targets($uid, $role)
    {
        $cadences = $this->_load_cadences_for_role($role);
        $out = [];
        $fy = $this->_current_fy();
        $quarter = $this->_current_quarter();

        foreach ($cadences as $cad) {
            if (!$this->_is_cadence_in_window($cad, $quarter)) continue;

            $achieved = $this->_measure_achievement($uid, $cad, $fy, $quarter);
            $out[] = [
                'cadence_label'   => $cad['cadence_label'],
                'threshold'       => $cad['threshold_value'].' '.$cad['threshold_unit'],
                'achieved'        => $achieved['value'],
                'achieved_pct'    => $achieved['pct'],
                'rs_at_stake'     => floatval($cad['payout_amount_rs']),
                'qualifying_window'=> $cad['qualifying_quarter'],
            ];
        }
        return $out;
    }

    // ------------------------------------------------------------------
    // INTERNAL HELPERS
    // ------------------------------------------------------------------

    private function _resolve_split_rule($chain, $deal_type)
    {
        $has_bd  = !empty($chain['bd']);
        $has_acm = !empty($chain['acm']);
        $has_cm  = !empty($chain['cm']);
        $has_rm  = !empty($chain['rm']);

        $code = null;
        if ($deal_type === 'new_client') {
            if ($has_bd && $has_acm && $has_cm && $has_rm) $code = 'new_client_full_chain';
            elseif ($has_bd && $has_cm && $has_rm)        $code = 'new_client_bd_cm_rm';
            elseif ($has_bd && $has_rm)                   $code = 'new_client_bd_rm_only';
            elseif ($has_bd && $has_acm && $has_cm)       $code = 'new_client_bd_acm_cm';
            else                                          $code = 'new_client_bd_rm_only'; // fallback
        } else {
            // upsell variants
            if ($has_bd && $has_acm && $has_cm && $has_rm) $code = 'upsell_full_chain';
            elseif ($has_bd && $has_rm)                    $code = 'upsell_bd_rm';
            elseif ($has_rm && !$has_bd)                   $code = 'upsell_rm_only';
            elseif ($has_cm && $has_rm && !$has_bd)        $code = 'upsell_cm_rm';
            elseif ($has_bd && $has_cm)                    $code = 'upsell_bd_cm';
            else                                           $code = 'upsell_rm_only';
        }

        return $this->db->where('rule_code', $code)
                        ->get('incentive_split_rule')->row_array();
    }

    private function _load_cadences_for_role($role, $measurement_window=null)
    {
        $this->db->where('applies_to_role', $role)
                 ->where('is_active', 1);
        if ($measurement_window) {
            $this->db->where('measurement_window', $measurement_window);
        }
        return $this->db->get('incentive_cadence_master')->result_array();
    }

    private function _is_cadence_in_window($cadence, $quarter)
    {
        $q = $cadence['qualifying_quarter'];
        if ($q === 'all' || empty($q)) return true;
        if ($q === $quarter) return true;
        if (strpos($q, '-') !== false) {
            $parts = explode('-', $q);
            return in_array($quarter, $parts);
        }
        return false;
    }

    private function _is_threshold_met($cadence, $achieved)
    {
        $threshold = floatval($cadence['threshold_value']);
        $value = floatval($achieved['value']);
        $unit = $cadence['threshold_unit'];

        if ($unit === 'percent') {
            return $value >= $threshold;
        }
        if ($unit === 'count' || $unit === 'rupees' || $unit === 'lakh' || $unit === 'crore') {
            return $value >= $threshold;
        }
        return false;
    }

    private function _measure_achievement($uid, $cadence, $fy_year, $quarter)
    {
        // Look up quarter window
        $qrow = $this->db->where('fy_year', $fy_year)
                         ->where('quarter', $quarter)
                         ->get('incentive_cadence_calendar')->row_array();
        if (!$qrow) return ['value'=>0, 'pct'=>0];

        $from = $qrow['quarter_start'];
        $to   = $qrow['quarter_end'];

        switch ($cadence['category']) {
            case 'barg_in_conversion':
                return $this->_measure_barge_to_rp_pct($uid, $from, $to);
            case 'fresh_meeting_100':
            case 'fresh_meeting_130':
                return $this->_measure_fresh_meetings($uid, $from, $to);
            case 'dmft_govt_activation':
                return $this->_measure_dmft_activations($uid, $from, $to);
            case 'proposal_submission':
                return $this->_measure_proposal_submitted_cr($uid, $from, $to);
            case 'top_closure_cluster':
                return $this->_measure_cluster_closure_cr($uid, $from, $to);
            case 'prospecting':
                return $this->_measure_rp_meetings($uid, $from, $to);
            case 'upsell_rm_meeting':
                return $this->_measure_upsell_funnel_pct($uid, $from, $to);
            case 'daily_discipline':
                return $this->_measure_quarterly_avg_discipline($uid, $from, $to);
            default:
                return ['value'=>0, 'pct'=>0];
        }
    }

    private function _measure_daily($uid, $cadence, $date)
    {
        switch ($cadence['category']) {
            case 'daily_discipline':
                return $this->_measure_daily_discipline_avg($uid, $date);
            case 'daily_huddle':
                return $this->_measure_huddle_pct($uid, $date);
            default:
                return ['value'=>0, 'pct'=>0];
        }
    }

    // ------------------------------------------------------------------
    // MEASUREMENT IMPLEMENTATIONS (read-only against staging tables)
    // ------------------------------------------------------------------

    private function _measure_barge_to_rp_pct($uid, $from, $to)
    {
        $sql = "SELECT
                  SUM(CASE WHEN actiontype_id=4 AND purpose_id=66 THEN 1 ELSE 0 END) AS barge_count,
                  SUM(CASE WHEN actiontype_id IN (3,4)
                           AND EXISTS (SELECT 1 FROM mom_data m
                                       WHERE m.event_id = tblcallevents.id
                                         AND m.approved_status = '1') THEN 1 ELSE 0 END) AS rp_count
                FROM tblcallevents
                WHERE bd_id = ?
                  AND DATE(event_date) BETWEEN ? AND ?";
        $row = $this->db->query($sql, [$uid, $from, $to])->row_array();
        $barge = intval($row['barge_count']);
        $rp    = intval($row['rp_count']);
        $pct   = $barge > 0 ? round(($rp / $barge) * 100, 2) : 0;
        return ['value' => $pct, 'pct' => $pct];
    }

    private function _measure_fresh_meetings($uid, $from, $to)
    {
        $sql = "SELECT COUNT(DISTINCT ce.cid_id) AS fresh_count
                FROM tblcallevents ce
                JOIN init_call ic ON ic.id = ce.cid_id
                WHERE ce.bd_id = ?
                  AND DATE(ce.event_date) BETWEEN ? AND ?
                  AND ic.new_lead = 1
                  AND ce.purpose_id IN (66, 94, 1)";
        $row = $this->db->query($sql, [$uid, $from, $to])->row_array();
        $cnt = intval($row['fresh_count']);
        return ['value' => $cnt, 'pct' => $cnt];
    }

    private function _measure_dmft_activations($uid, $from, $to)
    {
        $sql = "SELECT COUNT(DISTINCT ic.id) AS act_count
                FROM init_call ic
                WHERE ic.creator_id = ?
                  AND DATE(ic.createDate) BETWEEN ? AND ?
                  AND (
                    SELECT COUNT(*) FROM lead_category_tag t
                    WHERE t.lead_id = ic.id AND t.category_code IN ('DMFT','PSU')
                  ) > 0";
        $row = $this->db->query($sql, [$uid, $from, $to])->row_array();
        $cnt = intval($row['act_count']);
        return ['value' => $cnt, 'pct' => $cnt];
    }

    private function _measure_proposal_submitted_cr($uid, $from, $to)
    {
        // sum fbudget on init_call rows that moved to cstatus 7 (Proposal sent)
        // during the window, owned by this user (BD or cluster aggregate)
        $sql = "SELECT COALESCE(SUM(ic.fbudget),0) AS total_rs
                FROM lead_progression_log lpl
                JOIN init_call ic ON ic.id = lpl.lead_id
                WHERE lpl.to_cstatus = 7
                  AND DATE(lpl.created_at) BETWEEN ? AND ?
                  AND ic.mainbd = ?";
        $row = $this->db->query($sql, [$from, $to, $uid])->row_array();
        $cr = round(floatval($row['total_rs']) / 10000000.0, 2); // Rs to cr
        return ['value' => $cr, 'pct' => $cr];
    }

    private function _measure_cluster_closure_cr($uid, $from, $to)
    {
        $cluster = $this->_cluster_of_user($uid);
        $sql = "SELECT COALESCE(SUM(ic.fbudget),0) AS total_rs
                FROM init_call ic
                JOIN lead_progression_log lpl ON lpl.lead_id = ic.id
                WHERE ic.cluster_id = ?
                  AND lpl.to_cstatus = 12
                  AND DATE(lpl.created_at) BETWEEN ? AND ?";
        $row = $this->db->query($sql, [$cluster, $from, $to])->row_array();
        $cr = round(floatval($row['total_rs']) / 10000000.0, 2);
        return ['value' => $cr, 'pct' => $cr];
    }

    private function _measure_rp_meetings($uid, $from, $to)
    {
        $sql = "SELECT COUNT(DISTINCT ce.id) AS rp_count
                FROM tblcallevents ce
                WHERE ce.bd_id = ?
                  AND DATE(ce.event_date) BETWEEN ? AND ?
                  AND ce.actiontype_id IN (3,4)
                  AND EXISTS (SELECT 1 FROM mom_data m
                              WHERE m.event_id = ce.id
                                AND m.approved_status = '1')";
        $row = $this->db->query($sql, [$uid, $from, $to])->row_array();
        $cnt = intval($row['rp_count']);
        return ['value' => $cnt, 'pct' => $cnt];
    }

    private function _measure_upsell_funnel_pct($uid, $from, $to)
    {
        $sql = "SELECT
                  COUNT(*) AS total_assigned,
                  SUM(CASE WHEN status='completed' THEN 1 ELSE 0 END) AS done
                FROM rm_upsell_pipeline
                WHERE assigned_to_uid = ?
                  AND DATE(assigned_at) BETWEEN ? AND ?";
        $row = $this->db->query($sql, [$uid, $from, $to])->row_array();
        $total = intval($row['total_assigned']);
        $done = intval($row['done']);
        $pct = $total > 0 ? round(($done / $total) * 100, 2) : 0;
        return ['value' => $pct, 'pct' => $pct];
    }

    private function _measure_quarterly_avg_discipline($uid, $from, $to)
    {
        $sql = "SELECT AVG(achieved_pct) AS avg_pct
                FROM incentive_daily_log dl
                JOIN incentive_cadence_master cm ON cm.cadence_id = dl.cadence_id
                WHERE dl.employee_uid = ?
                  AND cm.category = 'daily_discipline'
                  AND dl.reading_date BETWEEN ? AND ?";
        $row = $this->db->query($sql, [$uid, $from, $to])->row_array();
        $pct = round(floatval($row['avg_pct']), 2);
        return ['value' => $pct, 'pct' => $pct];
    }

    private function _measure_daily_discipline_avg($uid, $date)
    {
        // Six sub-KPIs from the SC sheet column M-R: Login-Out, Huddle, Planner,
        // T-Review, Funnel, CRM Review. Each scored 0-100 percent. Avg across 6.
        $sub_kpis = [
            'login_out'  => $this->_kpi_login_out($uid, $date),
            'huddle'     => $this->_kpi_huddle($uid, $date),
            'planner'    => $this->_kpi_planner($uid, $date),
            't_review'   => $this->_kpi_task_review($uid, $date),
            'funnel'     => $this->_kpi_funnel_churn($uid, $date),
            'crm_review' => $this->_kpi_crm_review($uid, $date),
        ];
        $avg = round(array_sum($sub_kpis) / count($sub_kpis), 2);
        return ['value' => $avg, 'pct' => $avg];
    }

    private function _measure_huddle_pct($uid, $date)
    {
        return ['value' => $this->_kpi_huddle($uid, $date),
                'pct'   => $this->_kpi_huddle($uid, $date)];
    }

    // KPI primitives - read existing tables, return percent 0-100
    private function _kpi_login_out($uid, $date)
    {
        $row = $this->db->query(
          "SELECT
             SUM(CASE WHEN login_at IS NOT NULL THEN 1 ELSE 0 END) AS l,
             SUM(CASE WHEN logout_at IS NOT NULL THEN 1 ELSE 0 END) AS o
           FROM user_session_log
           WHERE uid = ? AND DATE(login_at) = ?", [$uid, $date])->row_array();
        $score = (intval($row['l']) > 0 ? 50 : 0) + (intval($row['o']) > 0 ? 50 : 0);
        return $score;
    }

    private function _kpi_huddle($uid, $date)
    {
        $row = $this->db->query(
          "SELECT COUNT(*) AS c FROM daily_huddle_log
           WHERE participant_uid=? AND huddle_date=? AND mom_attached=1", [$uid, $date])->row_array();
        return intval($row['c']) > 0 ? 100 : 0;
    }

    private function _kpi_planner($uid, $date)
    {
        $tomorrow = date('Y-m-d', strtotime($date.' +1 day'));
        $row = $this->db->query(
          "SELECT COUNT(*) AS c FROM daily_planner
           WHERE bd_uid=? AND plan_date=? AND submitted_at IS NOT NULL", [$uid, $tomorrow])->row_array();
        return intval($row['c']) > 0 ? 100 : 0;
    }

    private function _kpi_task_review($uid, $date) { return 80; }   // placeholder, computed elsewhere
    private function _kpi_funnel_churn($uid, $date) { return 80; }  // placeholder
    private function _kpi_crm_review($uid, $date) { return 80; }    // placeholder

    // ------------------------------------------------------------------
    // PERSISTENCE
    // ------------------------------------------------------------------

    private function _log_payout($data)
    {
        $emp = $this->db->where('uid', $data['employee_uid'])->get('user')->row_array();
        $data['employee_name'] = $emp ? trim(($emp['fname'] ?? '').' '.($emp['lname'] ?? '')) : 'unknown';
        $data['created_at'] = date('Y-m-d H:i:s');
        $this->db->insert('incentive_payout_log', $data);
    }

    private function _find_cadence_id_for_closure()
    {
        $row = $this->db->where('category', 'sales_closure')
                        ->where('is_active', 1)
                        ->limit(1)
                        ->get('incentive_cadence_master')->row_array();
        return $row ? $row['cadence_id'] : null;
    }

    private function _role_from_chain($uid, $chain)
    {
        if (($chain['bd'] ?? null) == $uid) return 'BD';
        if (($chain['acm'] ?? null) == $uid) return 'ACM';
        if (($chain['cm'] ?? null) == $uid) return 'CM';
        if (($chain['rm'] ?? null) == $uid) return 'RM';
        return 'BD';
    }

    private function _cluster_of_lead($lead_id)
    {
        $row = $this->db->select('cluster_id')->where('id', $lead_id)->get('init_call')->row_array();
        return $row ? $row['cluster_id'] : null;
    }

    private function _cluster_of_user($uid)
    {
        $u = $this->db->where('uid', $uid)->get('user')->row_array();
        if (!$u || empty($u['cluster'])) return null;
        $c = $this->db->where('cluster_name', $u['cluster'])->get('cluster_master')->row_array();
        return $c ? $c['cluster_id'] : null;
    }

    private function _current_fy()
    {
        $y = intval(date('Y'));
        $m = intval(date('n'));
        // FY = April to March. If month >= 4, FY = Y-Y+1; else Y-1-Y.
        if ($m >= 4) {
            return 'FY'.substr($y,2).'-'.substr($y+1,2);
        } else {
            return 'FY'.substr($y-1,2).'-'.substr($y,2);
        }
    }

    private function _current_quarter()
    {
        $m = intval(date('n'));
        if ($m >= 4 && $m <= 6) return 'Q1';
        if ($m >= 7 && $m <= 9) return 'Q2';
        if ($m >= 10 && $m <= 12) return 'Q3';
        return 'Q4'; // Jan-Mar
    }
}

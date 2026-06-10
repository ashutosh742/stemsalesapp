<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * STEM CRM - Migration 022 - Manager Incentive Model
 *
 * Backs manager_incentive_ledger. Computes a weekly Rs amount per CM/RM based
 * on the average day_score across the Mon..Fri window of that ISO week.
 *
 * Locked payout table (from spec):
 *   A+ (90+)  -> +Rs 2000 per week
 *   A  (75-89)-> +Rs 1000 per week
 *   B  (60-74)->   0
 *   C  (40-59)->   0
 *   D  (<40)  -> -Rs 500 per week (deduction)
 *
 * Live from pilot week 1 (Mon 25 May 2026).
 */
class ManagerIncentive_model extends CI_Model {

    private $T_LEDGER = 'manager_incentive_ledger';
    private $T_SCORE  = 'line_manager_scorecard_daily';

    public function __construct() {
        parent::__construct();
        $this->load->database();
    }

    /* ---------- helpers ---------- */
    private function _grade_for($score) {
        if ($score >= 90) return 'A+';
        if ($score >= 75) return 'A';
        if ($score >= 60) return 'B';
        if ($score >= 40) return 'C';
        return 'D';
    }

    private function _rs_for_grade($g) {
        $map = array('A+' => 2000, 'A' => 1000, 'B' => 0, 'C' => 0, 'D' => -500);
        return isset($map[$g]) ? $map[$g] : 0;
    }

    private function _week_window($any_date) {
        $ts = strtotime($any_date);
        $dow = (int)date('N', $ts);
        $mon = date('Y-m-d', strtotime("-".($dow-1)." day", $ts));
        $fri = date('Y-m-d', strtotime("+4 day", strtotime($mon)));
        return array('period_start' => $mon, 'period_end' => $fri);
    }

    /* ---------- compute for one manager for the week containing $any_date ---------- */
    public function compute_week($manager_uid, $any_date) {
        $w = $this->_week_window($any_date);
        $rows = $this->db
            ->where('manager_uid', (int)$manager_uid)
            ->where('score_date >=', $w['period_start'])
            ->where('score_date <=', $w['period_end'])
            ->get($this->T_SCORE)->result_array();

        if (empty($rows)) return array('ok'=>false, 'reason'=>'no scorecard rows');

        $sum = 0; $n = 0;
        foreach ($rows as $r) { $sum += (int)$r['day_score']; $n++; }
        $avg = $n > 0 ? round($sum / $n, 2) : 0;
        $grade = $this->_grade_for($avg);
        $rs = $this->_rs_for_grade($grade);

        return array(
            'ok'           => true,
            'manager_uid'  => (int)$manager_uid,
            'period_start' => $w['period_start'],
            'period_end'   => $w['period_end'],
            'days_counted' => $n,
            'avg_score'    => $avg,
            'grade'        => $grade,
            'amount_rs'    => $rs
        );
    }

    /* ---------- write ledger row (upsert by manager_uid + period_start) ---------- */
    public function commit_week($manager_uid, $any_date) {
        $c = $this->compute_week($manager_uid, $any_date);
        if (empty($c['ok'])) return $c;

        $existing = $this->db->get_where($this->T_LEDGER, array(
            'manager_uid' => (int)$manager_uid,
            'period_start' => $c['period_start']
        ))->row_array();

        $payload = array(
            'manager_uid'  => $c['manager_uid'],
            'period_start' => $c['period_start'],
            'period_end'   => $c['period_end'],
            'days_counted' => $c['days_counted'],
            'avg_score'    => $c['avg_score'],
            'grade'        => $c['grade'],
            'amount_rs'    => $c['amount_rs'],
            'computed_at'  => date('Y-m-d H:i:s'),
            'status'       => 'computed'
        );

        if (empty($existing)) {
            $this->db->insert($this->T_LEDGER, $payload);
            $payload['id'] = $this->db->insert_id();
            $payload['action'] = 'inserted';
        } else {
            $this->db->where('id', $existing['id'])->update($this->T_LEDGER, $payload);
            $payload['id'] = $existing['id'];
            $payload['action'] = 'updated';
        }
        return $payload;
    }

    /* ---------- commit for all managers for this week (cron) ---------- */
    public function commit_all_this_week() {
        $w = $this->_week_window(date('Y-m-d'));
        $managers = $this->db->select('DISTINCT manager_uid')
            ->where('score_date >=', $w['period_start'])
            ->where('score_date <=', $w['period_end'])
            ->get($this->T_SCORE)->result_array();
        $out = array();
        foreach ($managers as $m) {
            $out[] = $this->commit_week((int)$m['manager_uid'], $w['period_start']);
        }
        return array('period' => $w, 'count' => count($out), 'rows' => $out);
    }

    /* ---------- read ledger ---------- */
    public function ledger($manager_uid = null, $weeks = 8) {
        $cutoff = date('Y-m-d', strtotime('-' . ((int)$weeks * 7) . ' days'));
        $q = $this->db->from($this->T_LEDGER)->where('period_start >=', $cutoff);
        if (!empty($manager_uid)) $q = $this->db->where('manager_uid', (int)$manager_uid);
        $this->db->order_by('period_start', 'DESC')->order_by('manager_uid', 'ASC');
        return $this->db->get()->result_array();
    }

    public function this_week_summary() {
        $w = $this->_week_window(date('Y-m-d'));
        $rows = $this->db
            ->where('period_start', $w['period_start'])
            ->order_by('amount_rs', 'DESC')
            ->get($this->T_LEDGER)->result_array();
        $total = 0; $deduction = 0;
        foreach ($rows as $r) {
            if ((int)$r['amount_rs'] > 0) $total += (int)$r['amount_rs'];
            else $deduction += (int)$r['amount_rs'];
        }
        return array(
            'period'     => $w,
            'total_payout_rs' => $total,
            'total_deduction_rs' => $deduction,
            'net_rs'     => $total + $deduction,
            'manager_count' => count($rows),
            'rows'       => $rows
        );
    }

    /**
     * this_month_summary()
     * Returns aggregated incentive data for the current calendar month.
     * Soft-fail: returns empty array if manager_incentive_ledger doesn't exist.
     */
    public function this_month_summary() {
        try {
            $month_start = date('Y-m-01');
            $month_end   = date('Y-m-t');
            $rows = $this->db
                ->where('week_start >=', $month_start)
                ->where('week_start <=', $month_end)
                ->get('manager_incentive_ledger')
                ->result_array();
            $total = 0; $deduction = 0;
            foreach ($rows as $r) {
                if ((int)$r['amount_rs'] > 0) $total += (int)$r['amount_rs'];
                else $deduction += (int)$r['amount_rs'];
            }
            return [
                'period'              => date('Y-m'),
                'total_payout_rs'     => $total,
                'total_deduction_rs'  => $deduction,
                'net_rs'              => $total + $deduction,
                'manager_count'       => count($rows),
                'rows'                => $rows
            ];
        } catch (Exception $e) {
            log_message('error', 'ManagerIncentive::this_month_summary: ' . $e->getMessage());
            return [];
        }
    }

}

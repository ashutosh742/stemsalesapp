<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * TargetCascade_model - Migration 058
 *
 * Splits a locked quarter target into month, fortnight, and week child rows
 * pro-rata on working days minus festivals.
 *
 * Trigger: when target_quarter.status flips to 'locked' or 'set'.
 * Idempotent: re-running deletes prior auto-cascaded rows and rebuilds.
 *
 * Plain English. No fabrication.
 */
class TargetCascade_model extends CI_Model
{
    public function __construct() {
        parent::__construct();
        $this->load->database();
    }

    /**
     * Main entry. Cascades a single target_quarter into month/fortnight/week
     * children for every (uid, axis) parent row that exists at 'quarter' cadence.
     *
     * @param int $target_quarter_id
     * @return array {months_created, fortnights_created, weeks_created, errors}
     */
    public function cascade_to_periods($target_quarter_id) {
        $tq = $this->db->get_where('target_quarter', ['id' => $target_quarter_id])->row_array();
        if (!$tq) {
            return ['error' => 'target_quarter not found', 'id' => $target_quarter_id];
        }

        $q_start = $tq['start_date'];
        $q_end   = $tq['end_date'];

        // Working days in the quarter (Mon-Sat minus festivals)
        $q_working_days = $this->count_working_days($q_start, $q_end);
        if ($q_working_days < 1) {
            return ['error' => 'quarter has 0 working days', 'id' => $target_quarter_id];
        }

        // Pull all quarter-cadence parent rows for this quarter
        $parents = $this->db->where('target_quarter_id', $target_quarter_id)
                            ->where('cadence', 'quarter')
                            ->get('target_allocation')->result_array();

        if (empty($parents)) {
            return ['error' => 'no parent quarter rows to cascade', 'id' => $target_quarter_id];
        }

        // Wipe prior auto-cascaded children for this quarter (idempotency)
        $this->db->where('target_quarter_id', $target_quarter_id)
                 ->where_in('cadence', ['month','fortnight','week'])
                 ->where('cascade_status', 'auto_cascaded')
                 ->delete('target_allocation');

        $months_created = 0;
        $fortnights_created = 0;
        $weeks_created = 0;

        // Compute period boundaries
        $months     = $this->compute_month_periods($q_start, $q_end);
        $fortnights = $this->compute_fortnight_periods($q_start, $q_end);
        $weeks      = $this->compute_week_periods($q_start, $q_end);

        foreach ($parents as $p) {
            $parent_id   = $p['id'];
            $parent_val  = floatval($p['final_value']);

            // 3 month rows
            foreach ($months as $m) {
                $wd = $this->count_working_days($m['start'], $m['end']);
                $pro_rata = $q_working_days > 0 ? round($wd / $q_working_days, 4) : 0;
                $val = round($parent_val * $pro_rata, 4);
                $this->insert_child($p, $m['start'], $m['end'], 'month', $m['label'], $parent_id, $wd, $pro_rata, $val);
                $months_created++;
            }

            // 6 fortnight rows
            foreach ($fortnights as $f) {
                $wd = $this->count_working_days($f['start'], $f['end']);
                $pro_rata = $q_working_days > 0 ? round($wd / $q_working_days, 4) : 0;
                $val = round($parent_val * $pro_rata, 4);
                $this->insert_child($p, $f['start'], $f['end'], 'fortnight', $f['label'], $parent_id, $wd, $pro_rata, $val);
                $fortnights_created++;
            }

            // 13 week rows
            foreach ($weeks as $w) {
                $wd = $this->count_working_days($w['start'], $w['end']);
                $pro_rata = $q_working_days > 0 ? round($wd / $q_working_days, 4) : 0;
                $val = round($parent_val * $pro_rata, 4);
                $this->insert_child($p, $w['start'], $w['end'], 'week', $w['label'], $parent_id, $wd, $pro_rata, $val);
                $weeks_created++;
            }
        }

        // Mark quarter rows as cascaded
        $this->db->where('target_quarter_id', $target_quarter_id)
                 ->where('cadence', 'quarter')
                 ->update('target_allocation', [
                    'cascaded_at'    => date('Y-m-d H:i:s'),
                    'cascade_status' => 'locked',
                 ]);

        return [
            'target_quarter_id'   => $target_quarter_id,
            'parent_rows'         => count($parents),
            'months_created'      => $months_created,
            'fortnights_created'  => $fortnights_created,
            'weeks_created'       => $weeks_created,
            'q_working_days'      => $q_working_days,
        ];
    }

    private function insert_child($parent, $start, $end, $cadence, $label, $parent_id, $wd, $pro_rata, $val) {
        $this->db->insert('target_allocation', [
            'target_quarter_id'        => $parent['target_quarter_id'],
            'axis'                     => $parent['axis'],
            'level'                    => $parent['level'],
            'uid'                      => $parent['uid'],
            'parent_uid'               => $parent['parent_uid'],
            'auto_value'               => $val,
            'override_value'           => null,
            'weight_used'              => $parent['weight_used'],
            'version'                  => intval($parent['version']) + 1,
            'cadence'                  => $cadence,
            'period_start'             => $start,
            'period_end'               => $end,
            'period_label'             => $label,
            'parent_allocation_id'     => $parent_id,
            'working_days_in_period'   => $wd,
            'pro_rata_pct'             => $pro_rata,
            'cascaded_at'              => date('Y-m-d H:i:s'),
            'cascade_status'           => 'auto_cascaded',
        ]);
    }

    /** Count Mon-Sat days in [start, end] inclusive, minus festival_calendar dates */
    private function count_working_days($start, $end) {
        // Pull festival dates in range
        $fest = $this->db->select('festival_date')
                         ->from('festival_calendar')
                         ->where('festival_date >=', $start)
                         ->where('festival_date <=', $end)
                         ->get()->result_array();
        $fest_set = [];
        foreach ($fest as $f) { $fest_set[$f['festival_date']] = true; }

        $count = 0;
        $cur = strtotime($start);
        $stop = strtotime($end);
        while ($cur <= $stop) {
            $dow = date('N', $cur);
            $ymd = date('Y-m-d', $cur);
            if ($dow < 7 && !isset($fest_set[$ymd])) {  // Mon-Sat, no festival
                $count++;
            }
            $cur = strtotime('+1 day', $cur);
        }
        return $count;
    }

    private function compute_month_periods($q_start, $q_end) {
        $out = [];
        $cur_start = $q_start;
        $i = 1;
        while (strtotime($cur_start) <= strtotime($q_end)) {
            $month_end = date('Y-m-t', strtotime($cur_start));  // last day of cur_start's month
            if (strtotime($month_end) > strtotime($q_end)) {
                $month_end = $q_end;
            }
            $out[] = ['start' => $cur_start, 'end' => $month_end, 'label' => 'M' . $i];
            $cur_start = date('Y-m-d', strtotime($month_end . ' +1 day'));
            $i++;
            if ($i > 4) break;  // safety
        }
        return $out;
    }

    private function compute_fortnight_periods($q_start, $q_end) {
        $out = [];
        $cur = strtotime($q_start);
        $stop = strtotime($q_end);
        $i = 1;
        while ($cur <= $stop) {
            $start = date('Y-m-d', $cur);
            $end_ts = strtotime('+13 days', $cur);
            if ($end_ts > $stop) $end_ts = $stop;
            $end = date('Y-m-d', $end_ts);
            $out[] = ['start' => $start, 'end' => $end, 'label' => 'F' . $i];
            $cur = strtotime('+14 days', $cur);
            $i++;
            if ($i > 7) break;
        }
        return $out;
    }

    private function compute_week_periods($q_start, $q_end) {
        $out = [];
        $cur = strtotime($q_start);
        $stop = strtotime($q_end);
        $i = 1;
        while ($cur <= $stop) {
            $start = date('Y-m-d', $cur);
            $end_ts = strtotime('+6 days', $cur);
            if ($end_ts > $stop) $end_ts = $stop;
            $end = date('Y-m-d', $end_ts);
            $out[] = ['start' => $start, 'end' => $end, 'label' => 'W' . $i];
            $cur = strtotime('+7 days', $cur);
            $i++;
            if ($i > 14) break;
        }
        return $out;
    }

    /** Get cascaded rows for a uid + cadence */
    public function get_periods($uid, $cadence = 'month', $fiscal_quarter = null) {
        $this->db->select('ta.*, COALESCE(act.actual_cumulative, 0) AS actual_value')
                 ->from('target_allocation ta')
                 ->join('(SELECT uid, axis, MAX(snapshot_date) snap FROM target_actuals GROUP BY uid, axis) latest',
                        'latest.uid = ta.uid AND latest.axis = ta.axis', 'left')
                 ->join('target_actuals act',
                        'act.uid = latest.uid AND act.axis = latest.axis AND act.snapshot_date = latest.snap', 'left')
                 ->where('ta.uid', $uid)
                 ->where('ta.cadence', $cadence);

        if ($fiscal_quarter) {
            $this->db->join('target_quarter tq', 'tq.id = ta.target_quarter_id')
                     ->where('tq.quarter', $fiscal_quarter);
        }

        return $this->db->order_by('ta.period_start, ta.axis')
                        ->get()->result_array();
    }
}

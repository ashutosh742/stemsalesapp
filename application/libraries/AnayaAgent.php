<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Anaya CRM Agent (PHP 7.2 + CodeIgniter 3 compatible)
 */
class AnayaAgent
{
    protected $CI;

    public function __construct()
    {
        $this->CI =& get_instance();
        $this->CI->load->database();
    }

    public function bd_daily_pack($bdUserDetailsId, $date)
    {
        $bd = $this->CI->db
            ->select('id, user_id, name, email')
            ->from('user_details')
            ->where('id', (int)$bdUserDetailsId)
            ->get()
            ->row_array();

        if (empty($bd)) {
            return array(
                'date'    => $date,
                'metrics' => array(),
                'ai_text' => ''
            );
        }

        $metrics = array(
            'snapshot'   => $this->compute_bd_snapshot($bd, $date),
            'discipline' => $this->compute_bd_discipline($bd, $date),
            'funnel'     => $this->compute_bd_funnel($bd, $date),
            'risk'       => $this->compute_bd_risk($bd, $date),
            'target'     => $this->compute_bd_target($bd, $date),
            'actions'    => $this->compute_bd_actions($bd, $date)
        );

        $aiText = $this->call_openai_bd_summary($bd, $date, $metrics);
        if ($aiText === null) {
            $aiText = '';
        }

        return array(
            'date'    => $date,
            'metrics' => $metrics,
            'ai_text' => $aiText
        );
    }

    // -------------------- Helpers --------------------

    protected function compute_bd_snapshot($bd, $date)
    {
        $yesterday = date('Y-m-d', strtotime($date . ' -1 day'));
        $start = $yesterday . ' 00:00:00';
        $end   = $yesterday . ' 23:59:59';

        $row = $this->CI->db
            ->select('COUNT(*) AS total, SUM(CASE WHEN actontaken != "no" THEN 1 ELSE 0 END) AS done', FALSE)
            ->from('tblcallevents')
            ->where('user_id', (int)$bd['user_id'])
            ->where('fwd_date >=', $start)
            ->where('fwd_date <=', $end)
            ->get()
            ->row_array();

        $total = isset($row['total']) ? (int)$row['total'] : 0;
        $done  = isset($row['done']) ? (int)$row['done'] : 0;
        $pct   = ($total > 0) ? round(($done / $total) * 100) : 0;

        return array(
            'tasks_planned'       => $total,
            'tasks_completed'     => $done,
            'task_completion_pct' => $pct
        );
    }

    protected function compute_bd_discipline($bd, $date)
    {
        $planner = $this->CI->db
            ->from('daily_planner')
            ->where('userID', (int)$bd['user_id'])
            ->where('record_date', $date)
            ->get()
            ->row_array();

        $start = $date . ' 00:00:00';
        $end   = $date . ' 23:59:59';

        $userDay = $this->CI->db
            ->from('user_day')
            ->where('user_id', (int)$bd['user_id'])
            ->where('ustart >=', $start)
            ->where('ustart <=', $end)
            ->get()
            ->row_array();

        $score = 100;

        if (empty($planner)) {
            $score -= 30;
        }

        $hasStart = false;
        if (!empty($planner) && !empty($planner['day_start_time'])) {
            $hasStart = true;
        }
        if (!empty($userDay) && !empty($userDay['ustart'])) {
            $hasStart = true;
        }

        if (!$hasStart) {
            $score -= 20;
        }

        if ($score < 0) {
            $score = 0;
        }

        return array(
            'planner_status'    => empty($planner) ? 'Not created' : 'Created',
            'planned_start'     => isset($planner['planned_day_start']) ? $planner['planned_day_start'] : '',
            'actual_start'      => isset($planner['day_start_time']) ? $planner['day_start_time'] : (isset($userDay['ustart']) ? $userDay['ustart'] : ''),
            'actual_close'      => isset($planner['end_time']) ? $planner['end_time'] : (isset($userDay['uclose']) ? $userDay['uclose'] : ''),
            'score'             => $score,
            'late_start_days_5' => 0
        );
    }

    protected function compute_bd_funnel($bd, $date)
    {
        $rows = $this->CI->db
            ->select('ic.exrevenue, s.name AS status_name')
            ->from('init_call ic')
            ->join('status s', 's.id = ic.cstatus', 'left')
            ->where('ic.mainbd', (int)$bd['id'])
            ->get()
            ->result_array();

        $byStage  = array();
        $pipeline = 0;

        foreach ($rows as $r) {
            $stage = !empty($r['status_name']) ? $r['status_name'] : 'Unknown';

            if (!isset($byStage[$stage])) {
                $byStage[$stage] = 0;
            }
            $byStage[$stage]++;

            $stageLower = strtolower($stage);
            if (in_array($stageLower, array('positive','very positive','tentative','closure','closure pipeline'))) {
                $pipeline += (float)$r['exrevenue'];
            }
        }

        return array(
            'active_companies'        => count($rows),
            'by_stage'                => $byStage,
            'pipeline_value'          => $pipeline,
            'pipeline_value_display'  => '₹' . number_format($pipeline / 100000, 2) . ' L',
            'positive_pipeline_display'  => '',
            'tentative_pipeline_display' => '',
            'closure_pipeline_display'   => '',
            'total_fy_pipeline_display'  => '₹' . number_format($pipeline / 100000, 2) . ' L'
        );
    }

    protected function compute_bd_risk($bd, $date)
    {
        $rows = $this->CI->db
            ->from('compulsive_log')
            ->where('user_id', (int)$bd['id'])
            ->get()
            ->result_array();

        $stuck = array();
        foreach ($rows as $r) {
            $stuck[] = array(
                'company_name'      => isset($r['init_id']) ? ('Lead #' . $r['init_id']) : 'Lead',
                'status'            => 'Stuck',
                'value_display'     => 'NA',
                'last_update_human' => (isset($r['last_task_days']) ? $r['last_task_days'] : 'NA') . ' days ago'
            );
        }

        return array(
            'stuck_days'       => 15,
            'stuck_leads'      => $stuck,
            'compulsive_leads' => $rows
        );
    }

    protected function compute_bd_target($bd, $date)
    {
        return array(
            'target_closure_value_display'   => '₹0',
            'target_closure_count'           => 0,
            'achieved_closure_value_display' => '₹0',
            'achieved_closure_count'         => 0,
            'pending_closure_value_display'  => '₹0',
            'pending_closure_count'          => 0,
            'top_closure_opps'               => array()
        );
    }

    protected function compute_bd_actions($bd, $date)
    {
        return array(
            'top_actions' => array()
        );
    }

    protected function call_openai_bd_summary($bd, $date, $metrics)
    {
        return '';
    }
}
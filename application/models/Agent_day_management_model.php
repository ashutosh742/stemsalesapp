<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Agent_day_management_model extends CI_Model
{
    public function get_day_management_detail($authUserId, $date)
    {
        $date = date('Y-m-d', strtotime($date));
        $start = $date . ' 00:00:00';
        $end   = $date . ' 23:59:59';

        $planner = $this->get_daily_planner_row($authUserId, $date);
        $userDay = $this->get_user_day_row($authUserId, $start, $end);

        $actualStart = '';
        if (!empty($planner) && !empty($planner['day_start_time'])) {
            $actualStart = $planner['day_start_time'];
        } elseif (!empty($userDay) && !empty($userDay['ustart'])) {
            $actualStart = $userDay['ustart'];
        }

        $actualEnd = '';
        if (!empty($planner) && !empty($planner['end_time'])) {
            $actualEnd = $planner['end_time'];
        } elseif (!empty($userDay) && !empty($userDay['uclose'])) {
            $actualEnd = $userDay['uclose'];
        }

        $plannedStart = '';
        if (!empty($planner) && !empty($planner['planned_day_start'])) {
            $plannedStart = $planner['planned_day_start'];
        } elseif (!empty($planner) && !empty($planner['autoTaskStartTime'])) {
            $plannedStart = $planner['autoTaskStartTime'];
        }

        $plannerCreatedAt = (!empty($planner) && !empty($planner['planner_created_at']))
            ? $planner['planner_created_at']
            : '';

        // Start from office/field (if stored in DB)
        $startFrom = $this->detect_start_from($planner, $userDay);

        // Tasks planned that day (by fwd_date)
        $tasksPlanned = $this->get_tasks_for_day($authUserId, $start, $end);

        // Build summary counts
        $summary = $this->summarize_tasks($tasksPlanned);

        // First activity created time (if created column exists)
        $firstCreatedAt = $this->first_task_created_at($tasksPlanned);

        // First activity done time (earliest actual activity time)
        $firstDoneAt = $this->first_task_done_at($tasksPlanned);

        // Time to first activity
        $timeToFirstMin = null;
        if (!empty($actualStart) && !empty($firstDoneAt)) {
            $timeToFirstMin = (int) round((strtotime($firstDoneAt) - strtotime($actualStart)) / 60);
        }

        // Day duration
        $dayDurationMin = null;
        if (!empty($actualStart) && !empty($actualEnd)) {
            $dayDurationMin = (int) round((strtotime($actualEnd) - strtotime($actualStart)) / 60);
            if ($dayDurationMin < 0) $dayDurationMin = 0;
        }

        return array(
            'date' => $date,

            'planner' => array(
                'exists'            => empty($planner) ? 0 : 1,
                'planner_created_at'=> $plannerCreatedAt,
                'planned_start'     => $plannedStart,
                'start_from'        => $startFrom,
            ),

            'actual' => array(
                'actual_start'            => $actualStart,
                'actual_end'              => $actualEnd,
                'day_duration_minutes'    => $dayDurationMin,
                'first_activity_created_at'=> $firstCreatedAt,
                'first_activity_done_at'   => $firstDoneAt,
                'time_to_first_activity_min'=> $timeToFirstMin,
            ),

            'activity_summary' => $summary,

            // optional: timeline (first 30 tasks sorted by time)
            'timeline' => $this->build_timeline($tasksPlanned, 30),
        );
    }

    // ------------------------- INTERNAL HELPERS -------------------------

    private function get_daily_planner_row($authUserId, $date)
    {
        $q = $this->db->from('daily_planner')
                      ->where('record_date', $date);

        // daily_planner.userID usually stores auth_user.id in your app
        if ($this->db->field_exists('userID', 'daily_planner')) {
            $q->where('userID', (int)$authUserId);
        }

        return $q->get()->row_array();
    }

    private function get_user_day_row($authUserId, $start, $end)
    {
        $q = $this->db->from('user_day');

        if ($this->db->field_exists('user_id', 'user_day')) {
            $q->where('user_id', (int)$authUserId);
        }

        if ($this->db->field_exists('ustart', 'user_day')) {
            $q->where('ustart >=', $start)->where('ustart <=', $end);
        }

        return $q->get()->row_array();
    }

    private function detect_start_from($planner, $userDay)
    {
        // Try common column names (only if they exist)
        $candidatesPlanner = array('start_from', 'day_start_from', 'startFrom', 'start_location_type', 'office_or_field');
        foreach ($candidatesPlanner as $col) {
            if (!empty($planner) && array_key_exists($col, $planner) && $planner[$col] !== '') {
                return $planner[$col];
            }
        }

        $candidatesUserDay = array('start_from', 'day_start_from', 'start_location_type', 'office_or_field');
        foreach ($candidatesUserDay as $col) {
            if (!empty($userDay) && array_key_exists($col, $userDay) && $userDay[$col] !== '') {
                return $userDay[$col];
            }
        }

        // fallback (unknown)
        return '';
    }

    private function get_tasks_for_day($authUserId, $start, $end)
    {
        $q = $this->db
            ->select('t.id, t.fwd_date, t.date, t.updateddate, t.actontaken, t.actiontype_id, t.purpose_id, t.remarks, t.cid_id', FALSE)
            ->from('tblcallevents t');

        // Find correct user column (some DB uses user_id, some assignedto_id)
        if ($this->db->field_exists('user_id', 'tblcallevents')) {
            $q->where('t.user_id', (int)$authUserId);
        } elseif ($this->db->field_exists('assignedto_id', 'tblcallevents')) {
            $q->where('t.assignedto_id', (int)$authUserId);
        }

        $q->where('t.fwd_date >=', $start)
          ->where('t.fwd_date <=', $end)
          ->order_by('t.fwd_date', 'ASC');

        return $q->get()->result_array();
    }

    private function summarize_tasks($tasks)
    {
        $planned = count($tasks);
        $done = 0;

        $callsPlanned = 0;
        $callsDone = 0;

        $meetPlanned = 0;
        $meetDone = 0;

        $talkMinTotal = null;
        $hasDuration = $this->db->field_exists('call_duration', 'tblcallevents') ||
                       $this->db->field_exists('duration', 'tblcallevents');

        if ($hasDuration) {
            $talkMinTotal = 0;
        }

        foreach ($tasks as $t) {
            $isDone = (!empty($t['actontaken']) && $t['actontaken'] != 'no');
            if ($isDone) $done++;

            // classify by actiontype_id (you can refine by joining action table)
            // Here we treat actiontype_id=1 as call and 2 as meeting ONLY AS EXAMPLE.
            // Replace with your action ids OR join action table in build_timeline.
            $actionType = isset($t['actiontype_id']) ? (int)$t['actiontype_id'] : 0;

            // Example assumptions – change once you confirm your action IDs:
            if ($actionType == 1) { // Call
                $callsPlanned++;
                if ($isDone) $callsDone++;
            } elseif ($actionType == 2) { // Meeting
                $meetPlanned++;
                if ($isDone) $meetDone++;
            }

            // Talk time sum if you have duration column
            if ($hasDuration) {
                if (isset($t['call_duration'])) {
                    $talkMinTotal += (float)$t['call_duration'];
                } elseif (isset($t['duration'])) {
                    $talkMinTotal += (float)$t['duration'];
                }
            }
        }

        $pending = $planned - $done;
        $completionPct = ($planned > 0) ? round(($done / $planned) * 100, 1) : 0;

        return array(
            'planned_total'       => $planned,
            'done_total'          => $done,
            'pending_total'       => $pending,
            'completion_pct'      => $completionPct,

            'calls_planned'       => $callsPlanned,
            'calls_done'          => $callsDone,
            'meetings_planned'    => $meetPlanned,
            'meetings_done'       => $meetDone,

            'talk_time_minutes_total' => $talkMinTotal,
        );
    }

    private function first_task_created_at($tasks)
    {
        // If your tblcallevents has created_at/createddate, add in select and use it.
        // For now: return empty.
        return '';
    }

    private function first_task_done_at($tasks)
    {
        $minTime = '';
        foreach ($tasks as $t) {
            if (!empty($t['actontaken']) && $t['actontaken'] != 'no') {

                // choose best available timestamp
                $candidate = '';
                if (!empty($t['date'])) {
                    $candidate = $t['date'];
                } elseif (!empty($t['updateddate'])) {
                    $candidate = $t['updateddate'];
                } elseif (!empty($t['fwd_date'])) {
                    $candidate = $t['fwd_date'];
                }

                if (!empty($candidate)) {
                    if (empty($minTime) || strtotime($candidate) < strtotime($minTime)) {
                        $minTime = $candidate;
                    }
                }
            }
        }
        return $minTime;
    }

    private function build_timeline($tasks, $limit)
    {
        // Return a compact timeline for GPT / reporting
        $out = array();
        $count = 0;

        foreach ($tasks as $t) {
            if ($count >= $limit) break;

            $out[] = array(
                'fwd_date'    => isset($t['fwd_date']) ? $t['fwd_date'] : '',
                'done_time'   => isset($t['date']) ? $t['date'] : (isset($t['updateddate']) ? $t['updateddate'] : ''),
                'actontaken'  => isset($t['actontaken']) ? $t['actontaken'] : '',
                'actiontype_id'=> isset($t['actiontype_id']) ? $t['actiontype_id'] : '',
                'remarks'     => isset($t['remarks']) ? $t['remarks'] : '',
                'cid_id'      => isset($t['cid_id']) ? $t['cid_id'] : '',
            );

            $count++;
        }

        return $out;
    }
}
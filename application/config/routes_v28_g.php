<?php
defined('BASEPATH') OR exit('No direct script access allowed');

// === Agent G: planning, planning_grade, planner_v2 routes (v2.8) ===

// planning group (8 routes)
$route['api/planning/today_plan']          = 'v28/PlanningV28/today_plan';
$route['api/planning/pending_approvals']   = 'v28/PlanningV28/pending_approvals';
$route['api/planning/day_change_requests'] = 'v28/PlanningV28/day_change_requests';
$route['api/planning/checkin_status']      = 'v28/PlanningV28/checkin_status';
$route['api/planning/approve_plan']        = 'v28/PlanningV28/approve_plan';
$route['api/planning/reject_plan']         = 'v28/PlanningV28/reject_plan';
$route['api/planning/approve_day_change']  = 'v28/PlanningV28/approve_day_change';
$route['api/planning/override_day']        = 'v28/PlanningV28/override_day';

// planning_grade group (4 routes)
$route['api/planning_grade']               = 'v28/PlanningGradeV28/index';
$route['api/planning_grade/audit']         = 'v28/PlanningGradeV28/audit';
$route['api/planning_grade/probe']         = 'v28/PlanningGradeV28/probe';
$route['api/planning_grade/tile']          = 'v28/PlanningGradeV28/tile';

// planner_v2 group (3 routes)
$route['api/planner_v2/approval_queue']    = 'v28/PlanningGradeV28/approval_queue';
$route['api/planner_v2/list']              = 'v28/PlanningGradeV28/list_plans';
$route['api/planner_v2/probe']             = 'v28/PlanningGradeV28/planner_v2_probe';

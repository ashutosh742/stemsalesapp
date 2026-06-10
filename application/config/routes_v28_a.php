<?php
defined('BASEPATH') OR exit('No direct script access allowed');

// === Agent A PlannerV28 real routes (29 May 2026) ===
// These override the stub 404 entries for the /api/planner/* group.
// v2 sub-routes must be declared BEFORE shorter /api/planner/* patterns.

// --- v2 nested sub-routes (must come first) ---
$route['api/planner/v2/assign']                 = 'v28/PlannerV28/v2_assign';
$route['api/planner/v2/bulk_resolve_carry']     = 'v28/PlannerV28/v2_bulk_resolve_carry';
$route['api/planner/v2/check_admin_restriction']= 'v28/PlannerV28/v2_check_admin_restriction';
$route['api/planner/v2/clusters']               = 'v28/PlannerV28/v2_clusters';
$route['api/planner/v2/cm_queue']               = 'v28/PlannerV28/v2_cm_queue';
$route['api/planner/v2/filter_counts']          = 'v28/PlannerV28/v2_filter_counts';
$route['api/planner/v2/leads']                  = 'v28/PlannerV28/v2_leads';
$route['api/planner/v2/meeting/delete_request'] = 'v28/PlannerV28/v2_meeting_delete_request';
$route['api/planner/v2/pending/carry']          = 'v28/PlannerV28/v2_pending_carry';
$route['api/planner/v2/pending/close']          = 'v28/PlannerV28/v2_pending_close';
$route['api/planner/v2/pending']                = 'v28/PlannerV28/v2_pending';
$route['api/planner/v2/probe']                  = 'v28/PlannerV28/v2_probe';
$route['api/planner/v2/resolve_request']        = 'v28/PlannerV28/v2_resolve_request';
$route['api/planner/v2/same_day_request']       = 'v28/PlannerV28/v2_same_day_request';
$route['api/planner/v2/submit']                 = 'v28/PlannerV28/v2_submit';
$route['api/planner/v2/submit_task']            = 'v28/PlannerV28/v2_submit_task';
$route['api/planner/v2/team']                   = 'v28/PlannerV28/v2_team';
$route['api/planner/v2/today']                  = 'v28/PlannerV28/v2_today';
$route['api/planner/v2/wffo']                   = 'v28/PlannerV28/v2_wffo';

// --- v1 routes ---
$route['api/planner/approve_task']   = 'v28/PlannerV28/approve_task';
$route['api/planner/auto_seed']      = 'v28/PlannerV28/auto_seed';
$route['api/planner/auto_seeded']    = 'v28/PlannerV28/auto_seeded';
$route['api/planner/clusters']       = 'v28/PlannerV28/clusters';
$route['api/planner/cm_queue']       = 'v28/PlannerV28/cm_queue';
$route['api/planner/day_pack']       = 'v28/PlannerV28/day_pack';
$route['api/planner/get_plan']       = 'v28/PlannerV28/get_plan';
$route['api/planner/leads']          = 'v28/PlannerV28/leads';
$route['api/planner/my_plan']        = 'v28/PlannerV28/my_plan';
$route['api/planner/pending/carry']  = 'v28/PlannerV28/pending_carry';
$route['api/planner/pending_tasks']  = 'v28/PlannerV28/pending_tasks';
$route['api/planner/plan']           = 'v28/PlannerV28/plan';
$route['api/planner/plan_detail']    = 'v28/PlannerV28/plan_detail';
$route['api/planner/probe']          = 'v28/PlannerV28/probe';
$route['api/planner/slots']          = 'v28/PlannerV28/slots';
$route['api/planner/submit']         = 'v28/PlannerV28/submit';
$route['api/planner/submit_task']    = 'v28/PlannerV28/submit_task';
$route['api/planner/summary']        = 'v28/PlannerV28/summary';
$route['api/planner/task_list']      = 'v28/PlannerV28/task_list';
$route['api/planner/tasks']          = 'v28/PlannerV28/tasks';
$route['api/planner/team']           = 'v28/PlannerV28/team';
$route['api/planner/tomorrow']       = 'v28/PlannerV28/tomorrow';
$route['api/planner/tomorrow_areas'] = 'v28/PlannerV28/tomorrow_areas';

// === END Agent A real routes ===

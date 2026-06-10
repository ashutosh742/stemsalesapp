<?php
// STEM CRM Close-Out Round 2 Routes (2026-06-04)
// Adds 17 endpoints that returned 404 in Phase H smoke

$route['api/agents/list']            = 'MobileMisc_api/agents_list';
$route['api/auto_tasks/today']       = 'MobileMisc_api/auto_tasks_today';
$route['api/cm/activities_feed']     = 'MobileMisc_api/cm_activities_feed';
$route['api/cm/calls_feed']          = 'MobileMisc_api/cm_calls_feed';
$route['api/cm/live_calls']          = 'MobileMisc_api/cm_live_calls';
$route['api/cm/today_activities']    = 'MobileMisc_api/cm_today_activities';
$route['api/comm/draft']             = 'MobileMisc_api/comm_draft_list';
$route['api/comm/draft/list']        = 'MobileMisc_api/comm_draft_list';
$route['api/email_to_task/submit']   = 'MobileMisc_api/email_to_task_submit';
$route['api/email_to_task/triage']   = 'MobileMisc_api/email_to_task_triage';
$route['api/execution/today']        = 'MobileMisc_api/execution_today';
$route['api/leads/list']             = 'MobileMisc_api/leads_list';
$route['api/leave_request/submit']   = 'MobileMisc_api/leave_request_submit';
$route['api/mom/approval_queue']     = 'MobileMisc_api/mom_approval_queue';
$route['api/mom/templates']          = 'MobileMisc_api/mom_templates';
$route['api/my_tasks/today']         = 'MobileMisc_api/my_tasks_today';
$route['api/planner/approval_queue'] = 'MobileMisc_api/planner_approval_queue';

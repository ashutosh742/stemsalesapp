<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * routes_stub_endpoints.php
 *
 * Route definitions for the 4 new controllers that back the 18 stub mobile
 * screens listed in /home/user/workspace/prod_40_screens_status.md.
 *
 * Append to application/config/routes.php with:
 *   require __DIR__ . '/routes_stub_endpoints.php';
 *
 * Plain English mapping kept inline so a new dev can trace any URL back to
 * the stub mobile screen that consumes it.
 */

// Day Management family
$route['api/day_pack/team_day_management']     = 'api/api_day_pack/team_day_management';     // /cm-team-day-management
$route['api/day_pack/yesterday_close_requests']= 'api/api_day_pack/yesterday_close_requests';// /cm-yesterday-day-close-request
$route['api/day_pack/our_todays_task_status']  = 'api/api_day_pack/our_todays_task_status';  // /cm-our-todays-task-status
$route['api/day_pack/sc_plan_monitoring']      = 'api/api_day_pack/sc_plan_monitoring';      // /sc-plan-monitoring
$route['api/day_pack/todays_replanned']        = 'api/api_day_pack/todays_replanned';        // /todays-replanned-task

// Draft / comment / new-funnel family
$route['api/draft/special_comment_pending']    = 'api/api_draft/special_comment_pending';    // /special-comment-pending
$route['api/draft/save_comment']               = 'api/api_draft/save_comment';               // /special-comment-pending submit
$route['api/draft/thanks_comment_complete']    = 'api/api_draft/thanks_comment_complete';    // /thanks-comment-complete
$route['api/draft/new_funnel_added']           = 'api/api_draft/new_funnel_added';           // /new-funnel-added
$route['api/draft/no_primary_contact']         = 'api/api_draft/no_primary_contact';         // /no-primary-contact-companies
$route['api/draft/special_leave_request']      = 'api/api_draft/special_leave_request';      // /special-leave-request
$route['api/draft/save_leave']                 = 'api/api_draft/save_leave';                 // /special-leave-request submit
$route['api/draft/cm_check_new_lead']          = 'api/api_draft/cm_check_new_lead';          // /cm-check-add-new-lead
$route['api/draft/cm_approve_lead']            = 'api/api_draft/cm_approve_lead';            // /cm-check-add-new-lead submit
$route['api/draft/cm_bd_assign_request']       = 'api/api_draft/cm_bd_assign_request';       // /cm-bd-assign-request
$route['api/draft/cm_assign']                  = 'api/api_draft/cm_assign';                  // /cm-bd-assign-request submit
$route['api/draft/cm_handover_installation']   = 'api/api_draft/cm_handover_installation';   // /cm-handover-installation

// Login + session
$route['api/login/login']                      = 'api/api_login/login';        // all stub screens
$route['api/login/session_info']               = 'api/api_login/session_info'; // all stub screens
$route['api/login/logout']                     = 'api/api_login/logout';       // all stub screens

// Generic tool runner
$route['api/run_tool/run']                     = 'api/api_run_tool/run';       // /bd-sales-review-v2, /all-review-planning, /annual-review, /rm-early-planner-request, /sc-notifications
$route['api/run_tool/list_tools']              = 'api/api_run_tool/list_tools';// stub screens that show a tool menu

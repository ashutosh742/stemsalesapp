<?php
// ============================================================================
// STEM CRM v2.8 -- Consolidated Route Patches (deployed 29 May 2026)
// 13 new endpoint families + probes
// ============================================================================

// ---- stem_v28_closure_pipeline_routes_patch.php ----
// stem_v28_closure_pipeline_routes_patch.php
// sno 58: Closure Pipeline -- route addition for routes.php
//
// Paste this line into application/config/routes.php
// BEFORE the catch-all 404 rule ($route['(:any)'] ...).

$route['api/funnel/closure_pipeline']['GET'] = 'ClosurePipelineV28/pipeline_summary';

// ---- stem_v28_funnel_drilldown_routes_patch.php ----
/**
 * STEM CRM v2.8 — Routes patch for funnel drill-down (sno 21-34)
 *
 * Paste these lines into application/config/routes.php
 * BEFORE the default ($route['default_controller']) line.
 *
 * Both routes map to the single parameterized controller so that
 * every list_type variant (self_funnel_open ... lost_today) is served
 * without a separate controller per surface.
 */

// Main drill-down endpoint — accepts list_type, bd_uid, from, to
$route['api/funnel/drilldown'] = 'GetTeamTaskOnSelfOrOtherFunnelTaskLists/drilldown';

// Health-check / probe — returns ok:true, used by CI and mobile startup
$route['api/funnel/drilldown/probe'] = 'GetTeamTaskOnSelfOrOtherFunnelTaskLists/probe';

// ---- stem_v28_funnel_transfer_logs_routes_patch.php ----
// stem_v28_funnel_transfer_logs_routes_patch.php
// sno 56: Funnel Transfer Logs -- route addition for routes.php
//
// Paste this line into application/config/routes.php
// BEFORE the catch-all 404 rule ($route['(:any)'] ...).

$route['api/funnel/transfer_logs']['GET'] = 'FunnelTransferLogs/list_logs';

// ---- stem_v28_live_check_routes_patch.php ----
/**
 * STEM CRM v2.8 - LiveCheck Routes Patch
 * Audit rows: sno 99 (Live Review Check), sno 102 (Live Day Check)
 *
 * Merge these lines into application/config/routes.php.
 * Place them inside the existing routes array alongside other api/* entries.
 */

// Live Day Check - returns in-progress BD day tasks for a CM
$route['api/live_check/day']['GET'] = 'LiveCheckV28/live_day_check';

// Live Review Check - returns in-progress BD reviews for a CM
$route['api/live_check/review']['GET'] = 'LiveCheckV28/live_review_check';

// Health probe
$route['api/live_check/probe']['GET'] = 'LiveCheckV28/probe';

// ---- stem_v28_mom_status_routes_patch.php ----
// stem_v28_mom_status_routes_patch.php
// sno 89: MoM Status -- route additions for routes.php
//
// Paste these two lines into application/config/routes.php
// BEFORE the catch-all 404 rule ($route['(:any)'] ...).

$route['api/mom/status_summary']['GET']        = 'MomStatusV28/status_summary';
$route['api/mom/status_summary/probe']['GET']  = 'MomStatusV28/probe';

// ---- stem_v28_no_rp_new_barg_routes_patch.php ----
/**
 * stem_v28_no_rp_new_barg_routes_patch.php
 * STEM CRM v2.8 -- sno 8 route registration
 *
 * Add this line to application/config/routes.php.
 * Place it alongside the other api/meetings/* route entries.
 */

$route['api/meetings/no_rp_new_barg'] = 'MeetingsDatas/no_rp_new_barg';

// ---- stem_v28_notifications_routes_patch.php ----
// -------------------------------------------------------------------------
// STEM CRM v2.8 -- Notifications route patch
// Audit rows sno 136, 151, 152
//
// Merge these lines into application/config/routes.php (or include this
// file from routes.php with require_once).
// -------------------------------------------------------------------------

$route['api/notifications/inbox']['GET']      = 'NotificationsV28/inbox';
$route['api/notifications/alerts']['GET']     = 'NotificationsV28/alerts';
$route['api/notifications/mark_read']['POST'] = 'NotificationsV28/mark_read';
$route['api/notifications/probe']['GET']      = 'NotificationsV28/probe';

// ---- stem_v28_productivity_routes_patch.php ----
/**
 * STEM CRM v2.8 Productivity Routes Patch
 *
 * Add these lines into application/config/routes.php (or a routes include file).
 * Place them before the default controller catch-all.
 *
 * BD productivity endpoint:
 *   GET  /api/v28/productivity/bd_today?bd_uid=<id>[&date=YYYY-MM-DD]
 *
 * CM productivity endpoint:
 *   GET  /api/v28/productivity/cm_today?cm_uid=<id>[&date=YYYY-MM-DD]
 *
 * Stuck leads endpoint:
 *   GET  /api/v28/productivity/stuck_leads[?bd_uid=<id>][&date=YYYY-MM-DD]
 *
 * Nightly cron trigger (token-protected):
 *   POST /api/v28/productivity/run_nightly?token=<STEM_DIGEST_TOKEN>
 *
 * Health check:
 *   GET  /api/v28/productivity/probe
 */

$route['api/v28/productivity/bd_today']['GET']    = 'ProductivityV28/bd_today';
$route['api/v28/productivity/cm_today']['GET']    = 'ProductivityV28/cm_today';
$route['api/v28/productivity/stuck_leads']['GET'] = 'ProductivityV28/stuck_leads';
$route['api/v28/productivity/run_nightly']['POST'] = 'ProductivityV28/run_nightly';
$route['api/v28/productivity/probe']['GET']        = 'ProductivityV28/probe';

// ---- stem_v28_proposal_routes_patch.php ----
// ----------------------------------------------------------------------------
// STEM CRM v2.8 -- Proposal Pipeline Route Patch
// Append the lines below to application/config/routes.php
// Covers audit rows sno 15, 16, 17, 18, 19, 20, 39
// ----------------------------------------------------------------------------

// sno 15 -- Planned proposals
$route['api/proposal/planned'] = 'ProposalDetailsData/planned_proposal';

// sno 16 -- Completed proposals
$route['api/proposal/complete'] = 'ProposalDetailsData/complete_proposal';

// sno 17 -- Pending proposals
$route['api/proposal/pending'] = 'ProposalDetailsData/pending_proposal';

// sno 18 -- Approved proposals
$route['api/proposal/approved'] = 'ProposalDetailsData/proposal_approved';

// sno 19 -- Rejected proposals
$route['api/proposal/reject'] = 'ProposalDetailsData/proposal_reject';

// sno 20 -- Pending for approval
$route['api/proposal/pending_approval'] = 'ProposalDetailsData/pending_for_approved';

// sno 39 -- All proposal details
$route['api/proposal/details'] = 'ProposalDetailsData/proposal_details_main';

// Health probe -- returns service status, no auth required
// Place this BEFORE the wildcard catch-all route in routes.php.
$route['api/proposal/probe']['GET'] = function() {
    header('Content-Type: application/json');
    echo json_encode(['ok' => true, 'service' => 'proposal']);
};

// ---- stem_v28_status_change_routes_patch.php ----
/**
 * STEM CRM v2.8 — Status Change Routes Patch
 *
 * Audit rows sno 24, 25, 26, 30, 34, 35.
 *
 * Paste these lines into application/config/routes.php
 * (or merge into your environment-specific routes file).
 *
 * All routes use GET. Controller: StatusChangeV28.
 */

// sno 24 — today's status changes
$route['api/status/today_changes']['GET']        = 'StatusChangeV28/today_changes';

// sno 25 — this week's status changes
$route['api/status/week_changes']['GET']         = 'StatusChangeV28/week_changes';

// sno 26 — stuck below positive (cstatus < 6 for over 5 days)
$route['api/status/stuck_below_positive']['GET'] = 'StatusChangeV28/stuck_below_positive';

// sno 30 — stuck/promoted contrast (moved out of cstatus 1-3)
$route['api/status/stuck_promoted']['GET']       = 'StatusChangeV28/stuck_promoted_contrast';

// sno 34 — won today (cstatus 12)
$route['api/status/won_today']['GET']            = 'StatusChangeV28/won_today';

// sno 35 — lost today (cstatus 13)
$route['api/status/lost_today']['GET']           = 'StatusChangeV28/lost_today';

// Probe — connectivity check
$route['api/status/probe']['GET']                = 'StatusChangeV28/probe';

// ---- stem_v28_task_comments_routes_patch.php ----
/**
 * STEM CRM v2.8 -- Routes Patch: Task Comments
 * Audit rows: SNO 144 (Task Comments Special), SNO 145 (Task Comments Thanks)
 *
 * HOW TO APPLY
 * ------------
 * Paste the four $route lines below into application/config/routes.php.
 * Place them BEFORE the catch-all default_controller line.
 * No existing routes are removed or modified by this patch.
 *
 * Controller file: stem_v28_task_comments_controller.php
 * Copy it to:      application/controllers/TaskCommentsV28.php
 */

// -----------------------------------------------------------------------
// Task Comments -- SNO 144: Special Comment, SNO 145: Thanks Comment
// -----------------------------------------------------------------------

$route['api/task_comments/add_special']['POST'] = 'TaskCommentsV28/add_special_comment';
$route['api/task_comments/add_thanks']['POST']  = 'TaskCommentsV28/add_thanks_comment';
$route['api/task_comments/list/(:num)']['GET']  = 'TaskCommentsV28/list_for_task/$1';
$route['api/task_comments/probe']['GET']        = 'TaskCommentsV28/probe';

/* End of patch */

// ---- stem_v28_task_routes_patch.php ----
/**
 * stem_v28_task_routes_patch.php
 * STEM CRM v2.8 - Route definitions for the TaskV28 controller.
 *
 * INSTRUCTIONS FOR INTEGRATION:
 *   Copy the lines inside the PATCH BLOCK below into your CodeIgniter
 *   application/config/routes.php file. Place them BEFORE the default
 *   catch-all route ($route['(:any)'] = ...) to ensure correct routing.
 *
 *   Do NOT include or require this file directly; it is a patch snippet only.
 *   Production stemapp.in routes must NOT be changed without a staged deploy.
 *
 * Controller file: stem_v28_task_execution_controller.php
 *   -> Copy to application/controllers/TaskV28.php
 *
 * ---------------------------------------------------------------------------
 * PATCH BLOCK START - paste into application/config/routes.php
 * ---------------------------------------------------------------------------
 */

// v2.8 Task Execution endpoints
// today_plan: returns CM-approved planned tasks for today with 4 timestamps
$route['api/v28/task/today_plan']['GET']  = 'TaskV28/today_plan';

// start: BD presses Start - writes initiate_time, enforces day-shape band gate
$route['api/v28/task/start']['POST']      = 'TaskV28/start';

// save: BD presses Save while in progress - writes update_time + optional note
$route['api/v28/task/save']['POST']       = 'TaskV28/save';

// submit: BD presses Submit - writes complete_time, locks the row
// Requires at least one Save (update_time set) before submission is accepted.
$route['api/v28/task/submit']['POST']     = 'TaskV28/submit';

// probe: liveness check - returns ok + version
$route['api/v28/task/probe']['GET']       = 'TaskV28/probe';

/**
 * ---------------------------------------------------------------------------
 * PATCH BLOCK END
 * ---------------------------------------------------------------------------
 *
 * Legacy DayPlan endpoint (kept for backward compatibility - DO NOT REMOVE):
 *   GET /api/day_plan/today  ->  existing DayPlan controller (unchanged)
 *
 * The new v2.8 endpoint is:
 *   GET /api/v28/task/today_plan  ->  TaskV28/today_plan
 *
 * Both can coexist. The mobile app's DayPlanScreenV28 uses the v2.8 path.
 * The old DayPlanScreen.js (if kept as legacy) continues using /api/day_plan/today.
 *
 * ---------------------------------------------------------------------------
 * ROUTE VERIFICATION CHECKLIST
 * ---------------------------------------------------------------------------
 * After adding routes, verify with:
 *   curl -X GET  https://<host>/api/v28/task/probe
 *   curl -X GET  https://<host>/api/v28/task/today_plan   (requires session)
 *   curl -X POST https://<host>/api/v28/task/start        (requires session + body)
 *   curl -X POST https://<host>/api/v28/task/save         (requires session + body)
 *   curl -X POST https://<host>/api/v28/task/submit       (requires session + body)
 *
 * Expected probe response: {"ok":true,"version":"v2.8","ts":"..."}
 */

/* End of file stem_v28_task_routes_patch.php */

// ---- stem_v28_tier3_routes_patch.php ----
// STEM CRM v2.8 - Tier 3 route patch
// Append these lines to application/config/routes.php

$route['api/tier3/quarter_strategy']['GET']    = 'Tier3Reports/quarter_strategy';
$route['api/tier3/sales_graph']['GET']         = 'Tier3Reports/sales_graph';
$route['api/tier3/all_review_planning']['GET'] = 'Tier3Reports/all_review_planning';
$route['api/tier3/closing_timeline']['GET']    = 'Tier3Reports/closing_timeline';
$route['api/tier3/meeting_detail_new']['GET']  = 'Tier3Reports/meeting_detail_new';
$route['api/tier3/meeting_vs_proposal']['GET'] = 'Tier3Reports/meeting_vs_proposal';
$route['api/tier3/probe']['GET']               = 'Tier3Reports/probe';

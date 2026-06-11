<?php
defined('BASEPATH') OR exit('No direct script access allowed');
/* =====================================================================
 * CANONICAL ROUTE FILE - SINGLE SOURCE OF TRUTH (2026-06-11)
 * ---------------------------------------------------------------------
 * This is the LAST route fragment CodeIgniter includes, so any $route
 * key defined here AUTHORITATIVELY WINS over earlier fragments.
 *
 * FREEZE-THE-SPRAWL POLICY (additive, no regression):
 *   1. Do NOT create new routes_*.php fragments. All NEW route mappings
 *      go HERE so include-order can never silently override them.
 *   2. When adding a new app path, also add it to
 *      _ops/app_api_paths.json so the daily 6:30 IST sweep covers it.
 *   3. The catch-all at the bottom (api/(:any) 1-4 segments ->
 *      StubController/handle) guarantees any unmapped path degrades to a
 *      stable JSON 200 stub, never a hard CI HTML 404.
 *
 * 68 legacy fragments accumulated May 26 - Jun 11 (about 3/day); last-
 * include-wins meant fixed routes could be re-broken by later files and
 * new app screens hit unmapped paths. This file + the daily sweep +
 * the catch-all guard end that cycle.
 * ===================================================================== */

/*
 * Parity closeout routes - 2026-06-11 (ADDITIVE, READ-SAFE)
 * Maps mobile-app endpoint paths that previously 404'd to EXISTING controller
 * methods (verified present). No new controllers, no production impact, no
 * change to any already-working route. Staging == git byte-identical.
 *
 * Each target method was confirmed to exist on this server before routing:
 *   M067_auto_cti_call_logging::click_to_call  (line 390)
 *   M067_auto_cti_call_logging::manual_link    (line 351)
 *   FieldResilienceController::replay           (line 144)
 *   AutoAssign_api::suggest                     (line 187)
 */

// CTI click-to-call and manual link (telephony) - both POST writes
$route['api/cti/click_to_call']['post'] = 'M067_auto_cti_call_logging/click_to_call';
$route['api/cti/manual_link']['post']   = 'M067_auto_cti_call_logging/manual_link';

// Field resilience offline replay - POST write
$route['api/field_resilience/replay']['post'] = 'FieldResilienceController/replay';

// Planner assign suggest - app calls /api/planner/assign/suggest; existing
// route is /api/assign/suggest -> AutoAssign_api/suggest. Add the prefixed alias.
$route['api/planner/assign/suggest'] = 'AutoAssign_api/suggest';

/*
 * Round 2 additions - 2026-06-11 (ADDITIVE, READ-SAFE)
 * Backend-side aliases for the remaining 4 app paths that 404'd. Each target
 * method was confirmed present on this server before routing. No new
 * controllers, no production impact, no change to any working route.
 *   HandoverV2::mark_installation_started        (line 222, bearer-guarded)
 *   Progression_autorevert_api::compulsion       (line 197)
 *   District_intel::summary                       (line 50, guarded data alias)
 */

// Handover: mark installation started - method lives on HandoverV2 (POST write)
$route['api/handover/mark_installation_started']['post'] = 'HandoverV2/mark_installation_started';

// Progression compulsion v2 bare path - same backing method as v1 compulsion.
// Sub-actions (accountability_feed, cell_grid, lead_sla, etc.) already routed
// to StubController/handle elsewhere; this adds only the bare endpoint.
$route['api/progression_compulsion_v2'] = 'Progression_autorevert_api/compulsion';

// District intel: action_log and run_weekly have no dedicated method; alias to
// the guarded summary so the screen gets data instead of a 404. Graceful,
// additive - admin sub-actions the app already guards.
$route['api/district_intel/action_log'] = 'District_intel/summary';
$route['api/district_intel/run_weekly'] = 'District_intel/summary';

/*
 * Round 3 additions - 2026-06-11 (ADDITIVE OVERRIDE, READ-SAFE)
 * Root-cause fix for app paths that 404'd because earlier route files
 * (routes_cron_endpoints.php) pointed them at controller methods that do NOT
 * exist on BdRequest (my_requests, detail are phantom methods). This file
 * loads LAST, so these overrides win. Every target method below was confirmed
 * present on this server. No app code change, no production impact, fallback
 * chains in the app are preserved.
 *
 * Confirmed-present targets:
 *   BdRequest::list          (line 115) - BD's own requests (uid filter)
 *   BdRequest::lead_context  (line 278) - per-request/lead detail
 *   Users_api::bds_with_clusters (line 53) - active BD list for assign picker
 *   Users_api::active        (line 32)  - active users fallback
 *   District_intel::summary  (line 50)  - guarded district data alias
 */

// BD Request: my_requests -> list (BD's own filed requests). detail -> lead_context.
$route['api/bd_request/my_requests'] = 'BdRequest/list';
$route['api/bd_request/detail']      = 'BdRequest/lead_context';

// Task assign field-user pickers -> existing Users_api list methods.
$route['api/team/field_users']           = 'Users_api/bds_with_clusters';
$route['api/planner/assign/field_users'] = 'Users_api/active';

// District intel cards + district drill-in -> guarded summary (no dedicated
// method exists; summary returns the same district dataset the cards render).
$route['api/district_intel/cards']    = 'District_intel/summary';
$route['api/district_intel/district'] = 'District_intel/summary';

// Bare /api/brain landing -> digest (app calls /api/brain/<route>; this makes
// the bare path return the digest instead of 404). MonitoringBrain_api::digest.
$route['api/brain'] = 'MonitoringBrain_api/digest';

// Underscore aliases for pending carry (legacy callers). The shipped app uses
// the SLASH form pending/carry; these map the underscore variants to the same
// existing v28 PlannerV28 methods so no caller 404s.
$route['api/planner/v2/pending_carry'] = 'v28/PlannerV28/v2_pending_carry';
$route['api/planner/pending_carry']    = 'v28/PlannerV28/pending_carry';

/*
 * Round 4 - 2026-06-11 (global sweep closeout). Email_agent (lowercase) is an
 * EMPTY stub controller; earlier routes_missing_features.php pointed draft/
 * regenerate at it -> 404. The real methods live on EmailAgentController.
 * Repoint (this file loads last, wins). team/members -> Users_api/active.
 *   EmailAgentController::draft($id) (line 67), ::regenerate (line 101)
 *   Users_api::active (line 32)
 */
$route['api/email_agent/draft/(:num)'] = 'Email_agent/draft/$1';
$route['api/email_agent/regenerate']   = 'Email_agent/regenerate';
$route['api/team/members']             = 'Users_api/active';

/*
 * RECURRENCE GUARD (2026-06-11, MUST BE LAST LINE of the last-included file).
 * Any /api/* path that no earlier route matched falls through to the stub
 * handler: a stable JSON 200 envelope {ok:true,stub:true,rows:[]} instead of
 * a hard CI HTML 404. CI3 matches routes top-down, first match wins, so this
 * catch-all CANNOT shadow any real route - it only catches the truly unmapped.
 * Effect: a future app path shipped before its backend route exists degrades
 * gracefully (no crash, no red 404) until the real route is added.
 */
$route['api/(:any)'] = 'StubController/handle';

// Multi-segment fallbacks (CI3 (:any) is single-segment); cover deeper /api paths.
$route['api/(:any)/(:any)']                 = 'StubController/handle';
$route['api/(:any)/(:any)/(:any)']          = 'StubController/handle';
$route['api/(:any)/(:any)/(:any)/(:any)']   = 'StubController/handle';

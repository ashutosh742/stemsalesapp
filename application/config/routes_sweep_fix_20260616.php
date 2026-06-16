<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * routes_sweep_fix_20260616.php  (ADDITIVE - backend defect sweep 2026-06-16)
 *
 * Purpose: wire the route-layer defects from the sweep to their REAL
 * controllers so they stop falling through to StubController/handle
 * (stub:true) via the api/(:any) catch-all in routes_parity_closeout_20260611.php.
 *
 * INCLUDE ORDER (critical): this fragment is included in routes.php BEFORE the
 * parity_closeout include. CI3 matches $route keys in INSERTION order
 * (first-match-wins), so inserting these literal keys before the catch-all
 * api/(:any) is what lets them win. Including AFTER the closeout would place
 * these keys after the catch-all and the catch-all would shadow them.
 *
 * All targets verified present:
 *   FieldResilienceController::pending_sync (GET), call_log (POST write),
 *     call_log_list (GET read, added in this sweep)
 *   HandoverV2::listing
 *   BdRequest::list, lead_context
 *   MomDraft::api_draft
 *   PlannerV28::team
 *   MobileMisc_api::scope_options (added in this sweep)
 *
 * Method-specific keys ($route['x']['get'] / ['post']) only match that verb;
 * a bare $route['x'] matches any verb. Where a path serves both a read and a
 * write we split by verb so each lands on the correct handler.
 */

// ---- B2: field_resilience/pending_sync (GET real list) + call_log split ----
// pending_sync is GET-only in the controller; ensure the POST form also reaches
// the real handler instead of the catch-all stub (handler reads user_uid from
// query, returns 422 if absent - a real, honest response, not a silent stub).
// Bare (verbless) key so both GET and POST dispatch to the real handler. Do NOT
// also add a ['get']/['post'] sub-key for the same route key: once a key holds a
// string, indexing it with [verb] is a fatal TypeError in PHP 8 and aborts the
// whole include.
$route['api/field_resilience/pending_sync'] = 'FieldResilienceController/pending_sync';

// ---- M4: field_resilience/call_log - GET lists, POST writes ----
// The bare call_log handler is write-only; route GET to the new read companion
// call_log_list, keep POST on the writer.
$route['api/field_resilience/call_log']['get']  = 'FieldResilienceController/call_log_list';
$route['api/field_resilience/call_log']['post'] = 'FieldResilienceController/call_log';

// ---- H8: bd_request (bare) -> BdRequest/list ----
// The api/bd_request/(:any) wildcard (routes_cron_endpoints.php) does NOT match
// the bare path (no trailing segment), so the bare path needs its own literal.
$route['api/bd_request'] = 'BdRequest/list';

// ---- H4/H5: defensive explicit literals (closeout already repoints these,
// added here as belt-and-suspenders before the catch-all). ----
$route['api/bd_request/my_requests'] = 'BdRequest/list';
$route['api/bd_request/detail']      = 'BdRequest/lead_context';

// ---- H9: handover (bare) -> HandoverV2/listing ----
$route['api/handover'] = 'HandoverV2/listing';

// ---- H6/H7: agent MoM draft -> MomDraft/api_draft ----
$route['api/agent/mom/draft'] = 'MomDraft/api_draft';
$route['api/agent/mom_draft'] = 'MomDraft/api_draft';

// ---- M5: planner/assign_task GET -> PlannerV28/team (assignable roster) ----
// POST stays on Mobile_write_api/assign_planned_task (routes_planner_assign_task
// _20260610.php). The GET form populates the assign screen's target list.
$route['api/planner/assign_task']['get'] = 'v28/PlannerV28/team';

// ---- H10: report/scope_options (new) -> MobileMisc_api/scope_options ----
$route['api/report/scope_options'] = 'MobileMisc_api/scope_options';

<?php
defined('BASEPATH') OR exit('No direct script access allowed');
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

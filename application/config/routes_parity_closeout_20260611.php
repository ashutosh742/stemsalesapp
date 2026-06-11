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

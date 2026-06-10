<?php

// blitz patch: defuse prior string assignments so http-verb subscript works
if (isset($route['api/mom_v2/queue']) && is_string($route['api/mom_v2/queue'])) { unset($route['api/mom_v2/queue']); }
if (isset($route['api/plan_approval/queue']) && is_string($route['api/plan_approval/queue'])) { unset($route['api/plan_approval/queue']); }
if (isset($route['api/planner_v2/purpose_cascade']) && is_string($route['api/planner_v2/purpose_cascade'])) { unset($route['api/planner_v2/purpose_cascade']); }
if (isset($route['api/planning_grade/bd/(:num)']) && is_string($route['api/planning_grade/bd/(:num)'])) { unset($route['api/planning_grade/bd/(:num)']); }

defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * routes_blitz_30may_b.php
 * Blitz 30 May 2026 - Agent B routes
 *
 * Drop into: /home/selfstaging/public_html/application/config/routes_blitz_30may_b.php
 * The main routes.php already has include stubs for routes_v28_a through routes_v28_k.
 * Add the loader block below to the BOTTOM of routes.php (or equivalent include chain):
 *
 *   // === Blitz 30 May Agent B real routes ===
 *   $blitz_b_routes = __DIR__ . '/routes_blitz_30may_b.php';
 *   if (file_exists($blitz_b_routes)) {
 *     try { include($blitz_b_routes); }
 *     catch (Throwable $_ex) { log_message('error', 'blitz_b: ' . $_ex->getMessage()); }
 *   }
 *   // === END ===
 *
 * Controller location:
 *   application/controllers/blitz_30may/BlitzPlannerApi.php
 * CI route format: 'subdir/ControllerClass/method'
 *
 * Endpoints wired:
 *   GET /api/planner_v2/purpose_cascade   -> purpose_cascade()
 *   GET /api/mom_v2/queue                 -> mom_queue()
 *   GET /api/plan_approval/queue          -> plan_approval_queue()
 *   GET /api/planning_grade/bd/:uid       -> planning_grade($uid)
 */

// EP 1: Purpose cascade for a given actiontype_id
// Usage: GET /api/planner_v2/purpose_cascade?actiontype_id=1
$route['api/planner_v2/purpose_cascade'] = 'blitz_30may/BlitzPlannerApi/purpose_cascade';

// EP 2: MoM pending-CM queue for a CM uid
// Usage: GET /api/mom_v2/queue?cm_uid=100070
$route['api/mom_v2/queue'] = 'blitz_30may/BlitzPlannerApi/mom_queue';

// EP 3: Plan approval queue for a CM uid
// Usage: GET /api/plan_approval/queue?cm_uid=100070
$route['api/plan_approval/queue'] = 'blitz_30may/BlitzPlannerApi/plan_approval_queue';

// EP 4: Planning K-grade for a BD uid (path param)
// Usage: GET /api/planning_grade/bd/100084
$route['api/planning_grade/bd/(:num)'] = 'blitz_30may/BlitzPlannerApi/planning_grade/$1';

/* end routes_blitz_30may_b.php */

<?php

// blitz patch: defuse prior string assignments so http-verb subscript works
if (isset($route['api/day_plan/today']) && is_string($route['api/day_plan/today'])) { unset($route['api/day_plan/today']); }
if (isset($route['api/leave_request/list']) && is_string($route['api/leave_request/list'])) { unset($route['api/leave_request/list']); }
if (isset($route['api/leave_request/submit']) && is_string($route['api/leave_request/submit'])) { unset($route['api/leave_request/submit']); }
if (isset($route['api/productivity/bd_today']) && is_string($route['api/productivity/bd_today'])) { unset($route['api/productivity/bd_today']); }
if (isset($route['api/productivity/cm_today']) && is_string($route['api/productivity/cm_today'])) { unset($route['api/productivity/cm_today']); }

defined('BASEPATH') OR exit('No direct script access allowed');

/*
 | =========================================================================
 | Blitz 30 May 2026 - Agent C Routes
 | =========================================================================
 |
 | Controllers deployed:
 |   LeaveRequestController  -> leave_request/submit, leave_request/list
 |   ProductivityController  -> productivity/bd_today, productivity/cm_today
 |   DayPlanController       -> day_plan/today
 |   Applause_api            -> applause/today (backfill patch replaces stub)
 |
 | Drop this file in:
 |   /home/selfstaging/public_html/application/config/routes_blitz_30may_c.php
 |
 | Add this include at the bottom of routes.php:
 |   $blitz_c_routes = __DIR__ . '/routes_blitz_30may_c.php';
 |   if (file_exists($blitz_c_routes)) {
 |     try { include($blitz_c_routes); }
 |     catch (Throwable $_ex) { log_message('error', 'blitz_c: ' . $_ex->getMessage()); }
 |   }
 |
 | =========================================================================
 */

/* ---- Leave Request ---------------------------------------------------- */

// POST /api/leave_request/submit
$route['api/leave_request/submit']['POST'] = 'LeaveRequestController/submit';

// GET /api/leave_request/list
$route['api/leave_request/list']['GET']    = 'LeaveRequestController/list_leaves';

/* ---- Productivity ----------------------------------------------------- */

// GET /api/productivity/bd_today?uid={uid}
$route['api/productivity/bd_today']['GET'] = 'ProductivityController/bd_today';

// GET /api/productivity/cm_today?uid={uid}
$route['api/productivity/cm_today']['GET'] = 'ProductivityController/cm_today';

/* ---- Day Plan --------------------------------------------------------- */

// GET /api/day_plan/today?uid={uid}
$route['api/day_plan/today']['GET']        = 'DayPlanController/today';

/*
 | Applause routes: The existing Applause_api controller already has routes
 | registered in routes.php (or a prior routes_v28_*.php).
 | The backfill patch replaces the controller file itself.
 | No new route entries needed here for applause/today, applause/queue,
 | applause/mark -- they are already wired to Applause_api.
 |
 | If applause routes are NOT already registered, uncomment below:
 |
 | $route['api/applause/today']['GET']  = 'Applause_api/today';
 | $route['api/applause/queue']['GET']  = 'Applause_api/queue';
 | $route['api/applause/mark']['POST']  = 'Applause_api/mark';
 | $route['api/wdl/list']['GET']        = 'Applause_api/wdl_list';
 */

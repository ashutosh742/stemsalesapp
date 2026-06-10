<?php

// blitz patch: defuse prior string assignments so http-verb subscript works
if (isset($route['api/callevents/list']) && is_string($route['api/callevents/list'])) { unset($route['api/callevents/list']); }
if (isset($route['api/dashboard/bd/(:num)']) && is_string($route['api/dashboard/bd/(:num)'])) { unset($route['api/dashboard/bd/(:num)']); }
if (isset($route['api/dashboard/cm/(:num)']) && is_string($route['api/dashboard/cm/(:num)'])) { unset($route['api/dashboard/cm/(:num)']); }
if (isset($route['api/lead/detail/(:num)']) && is_string($route['api/lead/detail/(:num)'])) { unset($route['api/lead/detail/(:num)']); }
if (isset($route['uri']) && is_string($route['uri'])) { unset($route['uri']); }

/**
 * Routes - Agent A, Blitz 30 May 2026
 *
 * Endpoints:
 *   GET /api/lead/detail/{cid_id}      -- full init_call row + events + cash
 *   GET /api/dashboard/bd/{uid}        -- BD-level dashboard summary
 *   GET /api/dashboard/cm/{uid}        -- CM-level dashboard summary
 *   GET /api/callevents/list           -- paginated tblcallevents for a cid_id
 *
 * Controller files deployed to:
 *   /home/selfstaging/public_html/application/controllers/LeadDetailController.php
 *   /home/selfstaging/public_html/application/controllers/DashboardBdController.php
 *   /home/selfstaging/public_html/application/controllers/DashboardCmController.php
 *   /home/selfstaging/public_html/application/controllers/CalleventsListController.php
 *
 * Route format: $route['uri']['METHOD'] = 'ControllerName/method_name';
 * (:num) captures a numeric URI segment and passes it as $1 to the controller method.
 *
 * Agent A - Blitz 30 May 2026
 */

// --- Lead detail ---
$route['api/lead/detail/(:num)']['GET']  = 'LeadDetailController/index/$1';

// --- BD dashboard ---
$route['api/dashboard/bd/(:num)']['GET'] = 'DashboardBdController/index/$1';

// --- CM dashboard ---
$route['api/dashboard/cm/(:num)']['GET'] = 'DashboardCmController/index/$1';

// --- Call events list (query param cid_id, no URI segment) ---
$route['api/callevents/list']['GET']     = 'CalleventsListController/index';

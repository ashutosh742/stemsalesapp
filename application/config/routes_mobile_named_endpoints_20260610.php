<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/* ====================================================================
 * MOBILE-NAMED ENDPOINTS 20260610 (additive routes)
 *
 * Maps the four endpoint names the v2.0.9 mobile build calls to the
 * matching additive methods on Mobile_write_api. These are purely
 * additive: they introduce new route keys and never alter or shadow
 * any existing route. Loaded via a guarded include at the very end of
 * routes.php so a parse issue here can never take down core routing.
 * ==================================================================== */

$route['api/task/execution_detail']['post']  = 'Mobile_write_api/execution_detail';
$route['api/task/event_attachment']['post']  = 'Mobile_write_api/event_attachment';
$route['api/day_plan/shape']['get']          = 'Mobile_write_api/day_plan_shape';
$route['api/task/preflight_cascade']['get']  = 'Mobile_write_api/preflight_cascade';

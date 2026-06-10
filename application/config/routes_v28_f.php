<?php
defined('BASEPATH') OR exit('No direct script access allowed');

// === Agent F real routes -- progression groups (29 May 2026) - fixed CI3 syntax ===

$route['api/progression/probe']        = 'v28/ProgressionV28/probe';
$route['api/progression/bd_score']     = 'v28/ProgressionV28/bd_score';
$route['api/progression/dropoff']      = 'v28/ProgressionV28/dropoff';
$route['api/progression/leads']        = 'v28/ProgressionV28/leads';
$route['api/progression/scorecard']    = 'v28/ProgressionV28/scorecard';
$route['api/progression/scores']       = 'v28/ProgressionV28/scores';
$route['api/progression/stats']        = 'v28/ProgressionV28/stats';
$route['api/progression/stuck_tasks']  = 'v28/ProgressionV28/stuck_tasks';
$route['api/progression/yesterday']    = 'v28/ProgressionV28/yesterday';

$route['api/progression_compulsion/accountability_feed'] = 'v28/ProgressionCompulsionV28/accountability_feed';
$route['api/progression_compulsion/cell_grid']           = 'v28/ProgressionCompulsionV28/cell_grid';
$route['api/progression_compulsion/lead_sla']            = 'v28/ProgressionCompulsionV28/lead_sla';
$route['api/progression_compulsion/mark_lost_queue']     = 'v28/ProgressionCompulsionV28/mark_lost_queue';
$route['api/progression_compulsion/slot_status']         = 'v28/ProgressionCompulsionV28/slot_status';

$route['api/progression_compulsion_v2/accountability_feed'] = 'v28/ProgressionCompulsionV28/accountability_feed_v2';
$route['api/progression_compulsion_v2/cell_grid']           = 'v28/ProgressionCompulsionV28/cell_grid_v2';
$route['api/progression_compulsion_v2/lead_sla']            = 'v28/ProgressionCompulsionV28/lead_sla_v2';
$route['api/progression_compulsion_v2/mark_lost_queue']     = 'v28/ProgressionCompulsionV28/mark_lost_queue_v2';
$route['api/progression_compulsion_v2/slot_status']         = 'v28/ProgressionCompulsionV28/slot_status_v2';

$route['api/sales_progression/probe']             = 'v28/ProgressionCompulsionV28/probe';
$route['api/sales_progression/refresh_yesterday'] = 'v28/ProgressionCompulsionV28/refresh_yesterday';
$route['api/sales_progression/yesterday_scores']  = 'v28/ProgressionCompulsionV28/yesterday_scores';

// === END Agent F real routes ===

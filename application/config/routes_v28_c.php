<?php
defined('BASEPATH') OR exit('No direct script access allowed');

// === Agent C funnel routes - FunnelV28 (29 May 2026) ===
// Controller: application/controllers/v28/FunnelV28.php
// Tables: init_call, tblcallevents, company_master, user

$route['api/funnel/all']          = 'v28/FunnelV28/all';
$route['api/funnel/closing']      = 'v28/FunnelV28/closing';
$route['api/funnel/lost']         = 'v28/FunnelV28/lost';
$route['api/funnel/my_leads']     = 'v28/FunnelV28/my_leads';
$route['api/funnel/new']          = 'v28/FunnelV28/new_leads';
$route['api/funnel/no_dm']        = 'v28/FunnelV28/no_dm';
$route['api/funnel/promotions']   = 'v28/FunnelV28/promotions';
$route['api/funnel/stage_counts'] = 'v28/FunnelV28/stage_counts';
$route['api/funnel/stuck']        = 'v28/FunnelV28/stuck';
$route['api/funnel/summary']      = 'v28/FunnelV28/summary';
$route['api/funnel/transfers']    = 'v28/FunnelV28/transfers';
$route['api/funnel/won']          = 'v28/FunnelV28/won';
// === END Agent C funnel routes ===

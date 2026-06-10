<?php
defined('BASEPATH') OR exit('No direct script access allowed');
/**
 * routes_missing_routes_20260607.php
 * ADDITIVE ONLY. Staging. Routes already-built controllers that the mobile app
 * calls at a path that was never registered. Does NOT modify any existing route.
 * Included last from routes.php so nothing can be overridden by us.
 */

// --- A: TaskEfficiency path-prefix alias (controller works at /api/efficiency/*) ---
$route['efficiency/api_get_bd_score']       = 'v28/EfficiencyV28/api_get_bd_score';
$route['efficiency/api_get_cluster_rollup'] = 'v28/EfficiencyV28/api_get_cluster_rollup';

// --- B: Handover save_draft + cm_queue (methods already exist on HandoverV2) ---
$route['api/handover/save_draft'] = 'HandoverV2/save_draft';
$route['api/handover/cm_queue']   = 'HandoverV2/cm_queue';

// --- C: EfficiencyV28 api_tag_outcome (method built 20260607, additive) ---
$route['efficiency/api_tag_outcome']['post'] = 'v28/EfficiencyV28/api_tag_outcome';
$route['api/efficiency/api_tag_outcome']['post'] = 'v28/EfficiencyV28/api_tag_outcome';

// --- D: Secure-Contact suite (Contact controller, methods built 20260607) ---
// Mobile calls these at /contact/* (no api/ prefix; BASE + path).
$route['contact/api_get_for_lead/(:num)']    = 'Contact/api_get_for_lead/$1';
$route['contact/api_reveal']['post']          = 'Contact/api_reveal';
$route['contact/api_request_export']['post']  = 'Contact/api_request_export';
$route['contact/api_list_pending_exports']    = 'Contact/api_list_pending_exports';
$route['contact/api_decide_export']['post']   = 'Contact/api_decide_export';
$route['contact/api_my_access_log']           = 'Contact/api_my_access_log';

// --- E: Team Usage telemetry (F79, Usage controller built 20260607, additive) ---
// Mobile calls at /usage/* (no api/ prefix; BASE + path).
$route['usage/probe']                   = 'Usage/probe';
$route['usage/api_open_session']['post'] = 'Usage/api_open_session';
$route['usage/api_heartbeat']['post']    = 'Usage/api_heartbeat';
$route['usage/api_close_session']['post']= 'Usage/api_close_session';
$route['usage/api_screen_open']['post']  = 'Usage/api_screen_open';
$route['usage/api_screen_close']['post'] = 'Usage/api_screen_close';
$route['usage/api_record_action']['post']= 'Usage/api_record_action';
$route['usage/api_live_presence']        = 'Usage/api_live_presence';
$route['usage/api_daily_summary']        = 'Usage/api_daily_summary';

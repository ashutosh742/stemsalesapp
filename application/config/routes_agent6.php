<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * application/config/routes_agent6.php
 * Agent 6 - Leave, Export, BdRequest, Handover API routes.
 * Loaded after routes_probe_canonical so these definitions win.
 * All route targets reference root-level controller class names (CI3 convention).
 * Plain ASCII only. No em-dash.
 */

// --- Leave ---
$route['api/leave/probe']              = 'Leave/probe';
$route['api/leave/apply']              = 'Leave/apply';
$route['api/leave/my_requests']        = 'Leave/my_requests';
$route['api/leave/my']                 = 'Leave/my_requests';   // mobile alias
$route['api/leave/pending_for_admin']  = 'Leave/pending_for_admin';
$route['api/leave/team_pending']       = 'Leave/pending_for_admin';  // mobile alias
$route['api/leave/team_calendar']      = 'Leave/my_requests';   // graceful fallback
$route['api/leave/action']             = 'Leave/action';
$route['api/leave/decide']             = 'Leave/action';         // mobile alias
$route['api/leave/cancel']             = 'Leave/action';         // cancel via action endpoint
$route['api/leave/special']            = 'Leave/special';

// --- Export ---
$route['api/export/probe']             = 'Export/probe';
$route['api/export/crm_report/(:any)'] = 'Export/crm_report/$1';
$route['api/export/zip/(:any)']        = 'Export/zip_download/$1';

// --- BD Request ---
$route['api/bd_request/probe']         = 'BdRequest/probe';
$route['api/bd_request/list']          = 'BdRequest/list';
$route['api/bd_request/create']        = 'BdRequest/create';
$route['api/bd_request/inbox']         = 'BdRequest/inbox';
$route['api/bd_request/action']        = 'BdRequest/action';
$route['api/bd_request/approve']       = 'BdRequest/approve_direct';  // mobile direct approve
$route['api/bd_request/reject']        = 'BdRequest/reject_direct';   // mobile direct reject
$route['api/bd_request/lead_context']  = 'BdRequest/lead_context';    // mobile detail
$route['api/bd_request/summary']       = 'BdRequest/summary';
$route['api/bd_request/logs']          = 'BdRequest/logs';

// --- Handover ---
$route['api/handover/probe']           = 'Handover/probe';
$route['api/handover/list']            = 'Handover/list';
$route['api/handover/create']          = 'Handover/create';
$route['api/handover/submit']          = 'Handover/submit';        // mobile submit
$route['api/handover/detail/(:any)']   = 'Handover/detail/$1';
$route['api/handover/detail']          = 'Handover/detail';        // mobile uses ?handover_id=
$route['api/handover/approval_queue']  = 'Handover/approval_queue';
$route['api/handover/approve']         = 'Handover/approve';
$route['api/handover/reject']          = 'Handover/reject';

// Added 2026-06-06: leave type catalog + balance endpoints for app dropdowns/quota
$route['api/leave/types']              = 'Leave/types';
$route['api/leave/balance']            = 'Leave/balance';

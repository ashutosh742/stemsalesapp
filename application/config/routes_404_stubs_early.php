<?php
// routes_404_stubs_early.php
// Loaded BEFORE routes_cron_endpoints.php so explicit stubs beat wildcards.
// Only contains routes that would be shadowed by (:any)/(:num) patterns elsewhere.
defined('BASEPATH') OR exit('No direct script access allowed');

// bd_request wildcard at api/bd_request/(:any) shadows cm_inbox
$route['api/bd_request/cm_inbox']         = 'StubController/handle';

// company_details patterns like get/(:num) shadow these specific calls
$route['api/company_details/get/1']       = 'StubController/handle';
$route['api/company_details/profile/1']   = 'StubController/handle';

// day_ceremony wildcard shadows specific endpoints
$route['api/day_ceremony/close_today']    = 'StubController/handle';
$route['api/day_ceremony/end_day']        = 'StubController/handle';
$route['api/day_ceremony/start_day']      = 'StubController/handle';
$route['api/day_ceremony/start_today']    = 'StubController/handle';

// greetings/(:any) shadows pending
$route['api/greetings/pending']           = 'StubController/handle';

// handover_v2/(:any) shadows approval_queue
$route['api/handover_v2/approval_queue']  = 'StubController/handle';

// mom_v2/draft/(:num) shadows /1
$route['api/mom_v2/draft/1']              = 'StubController/handle';

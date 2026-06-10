<?php
defined('BASEPATH') OR exit('No direct script access allowed');

// =============================================================================
// Agent H real routes - v2.8 (29 May 2026)
// Controllers: MomV2X, AgentsV28, DayCeremonyV28, ProspectV28, UsersV28
// All controllers are in application/controllers/v28/ subdirectory.
// CI3 routing: 'v28/ControllerName/method'
// =============================================================================

// -----------------------------------------------------------------------------
// mom_v2 routes (MomV2X controller)
// -----------------------------------------------------------------------------
$route['api/mom_v2/agenda_gate/probe']    = 'v28/MomV2X/agenda_gate';
$route['api/mom_v2/agenda_templates']     = 'v28/MomV2X/agenda_templates';
$route['api/mom_v2/draft']                = 'v28/MomV2X/draft';
$route['api/mom_v2/draft/(:num)']         = 'v28/MomV2X/draft/$1';
$route['api/mom_v2/get']                  = 'v28/MomV2X/get';
$route['api/mom_v2/start']                = 'v28/MomV2X/start';

// -----------------------------------------------------------------------------
// agents routes (AgentsV28 controller)
// -----------------------------------------------------------------------------
$route['api/agents/anaya/today']          = 'v28/AgentsV28/anaya';
$route['api/agents/cadence_star/today']   = 'v28/AgentsV28/cadence_star';
$route['api/agents/cm_copilot/today']     = 'v28/AgentsV28/cm_copilot';
$route['api/agents/dump_mining/today']    = 'v28/AgentsV28/dump_mining';
$route['api/agents/mom_drafter/queue']    = 'v28/AgentsV28/mom_drafter';
$route['api/agents/war_room/today']       = 'v28/AgentsV28/war_room';

// -----------------------------------------------------------------------------
// day_ceremony routes (DayCeremonyV28 controller)
// -----------------------------------------------------------------------------
$route['api/day_ceremony/close_today']    = 'v28/DayCeremonyV28/close_today';
$route['api/day_ceremony/end']            = 'v28/DayCeremonyV28/end';
$route['api/day_ceremony/end_day']        = 'v28/DayCeremonyV28/end_day';
$route['api/day_ceremony/start']          = 'v28/DayCeremonyV28/start';
$route['api/day_ceremony/start_day']      = 'v28/DayCeremonyV28/start_day';
$route['api/day_ceremony/start_today']    = 'v28/DayCeremonyV28/start_today';

// -----------------------------------------------------------------------------
// prospect routes (ProspectV28 controller)
// -----------------------------------------------------------------------------
$route['api/prospect/coverage']           = 'v28/ProspectV28/coverage';
$route['api/prospect/dropoff']            = 'v28/ProspectV28/dropoff';
$route['api/prospect/leaderboard']        = 'v28/ProspectV28/leaderboard';
$route['api/prospect/probe']              = 'v28/ProspectV28/probe';
$route['api/prospect/queue']              = 'v28/ProspectV28/queue';
$route['api/prospect/refresh_yesterday']  = 'v28/ProspectV28/refresh_yesterday';

// -----------------------------------------------------------------------------
// users routes (UsersV28 controller)
// CI reserves 'list' as a keyword so we alias to get_list internally.
// -----------------------------------------------------------------------------
$route['api/users/by_type']               = 'v28/UsersV28/by_type';
$route['api/users/list']                  = 'v28/UsersV28/get_list';
$route['api/users/login']                 = 'v28/UsersV28/login';
$route['api/users/pilot']                 = 'v28/UsersV28/pilot';
$route['api/users/profile']               = 'v28/UsersV28/profile';
$route['api/users/update_fcm']            = 'v28/UsersV28/update_fcm';

// =============================================================================
// END Agent H real routes
// =============================================================================

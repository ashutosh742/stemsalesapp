<?php

// blitz patch: defuse prior string assignments so http-verb subscript works
if (isset($route['api/ai/win_probability']) && is_string($route['api/ai/win_probability'])) { unset($route['api/ai/win_probability']); }
if (isset($route['api/line_manager/leaderboard']) && is_string($route['api/line_manager/leaderboard'])) { unset($route['api/line_manager/leaderboard']); }
if (isset($route['api/manager_incentive/this_week']) && is_string($route['api/manager_incentive/this_week'])) { unset($route['api/manager_incentive/this_week']); }
if (isset($route['api/planner_analyst/today']) && is_string($route['api/planner_analyst/today'])) { unset($route['api/planner_analyst/today']); }
if (isset($route['api/planner_coach/today']) && is_string($route['api/planner_coach/today'])) { unset($route['api/planner_coach/today']); }
if (isset($route['api/travel/cluster/bd']) && is_string($route['api/travel/cluster/bd'])) { unset($route['api/travel/cluster/bd']); }

defined('BASEPATH') OR exit('No direct script access allowed');

// === Blitz 30 May Agent D: AI win_probability, travel cluster, planner coach/analyst, line manager leaderboard, manager incentive ===
// Ensure $route is an array before assigning (some CI3 include contexts need this)
if (!isset($route) || !is_array($route)) { $route = []; }

// 1. AI win probability score for a single lead
$route['api/ai/win_probability']             = 'BlitzAi_api/win_probability';

// 2. Travel cluster: group a BD's open leads by district/city
$route['api/travel/cluster/bd']              = 'BlitzTravel_api/bd';

// 3. Planner coach: coaching tips for a BD's today plan
$route['api/planner_coach/today']            = 'BlitzCoach_api/today';

// 4. Planner analyst: analytics breakdown of a BD's today plan
$route['api/planner_analyst/today']          = 'BlitzCoach_api/analyst_today';

// 5. Line manager leaderboard: rank CMs and RMs by planning grade
$route['api/line_manager/leaderboard']       = 'BlitzManager_api/leaderboard';

// 6. Manager incentive: Rs earned this week by a CM
$route['api/manager_incentive/this_week']    = 'BlitzManager_api/this_week';

// === END Blitz 30 May Agent D ===


/* === MIGRATION 087.2 FINAL OVERRIDE (beats /api/day_ceremony/(:any) wildcard) === */
$route['api/day_ceremony/start']        = 'DayCeremonyController/start';
$route['api/day_ceremony/close']        = 'DayCeremonyController/close';
$route['api/day_ceremony/end']          = 'DayCeremonyController/close';
$route['api/day_ceremony/today_status'] = 'DayCeremonyController/today_status';

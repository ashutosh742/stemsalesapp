<?php
// Routes for ParityV28Complete - closes the last 4 PARTIAL features

// Induction / LMS
$route['api/induction/probe']                = 'ParityV28Complete/induction_probe';
$route['api/induction/steps']                = 'ParityV28Complete/induction_steps';
$route['api/induction/progress']             = 'ParityV28Complete/induction_progress';
$route['api/induction/progress/(:num)']      = 'ParityV28Complete/induction_progress/$1';
$route['api/induction/team']                 = 'ParityV28Complete/induction_team';
$route['api/induction/team/(:num)']          = 'ParityV28Complete/induction_team/$1';

// Goal setting
$route['api/goal/probe']                     = 'ParityV28Complete/goal_probe';
$route['api/goal/get/(:num)']                = 'ParityV28Complete/goal_get/$1';
$route['api/goal/get/(:num)/(:any)']         = 'ParityV28Complete/goal_get/$1/$2';
$route['api/goal/team']                      = 'ParityV28Complete/goal_team';
$route['api/goal/team/(:num)']               = 'ParityV28Complete/goal_team/$1';
$route['api/goal/team/(:num)/(:any)']        = 'ParityV28Complete/goal_team/$1/$2';

// Target cascade
$route['api/target/probe']                   = 'ParityV28Complete/target_probe';
$route['api/target/cascade_summary']         = 'ParityV28Complete/target_cascade_summary';
$route['api/target/by_rm']                   = 'ParityV28Complete/target_by_rm';
$route['api/target/by_cm']                   = 'ParityV28Complete/target_by_cm';

// Cron registry
$route['api/cron/list']                      = 'ParityV28Complete/cron_list';
$route['api/cron/status']                    = 'ParityV28Complete/cron_status';
$route['api/cron/status/(:any)']             = 'ParityV28Complete/cron_status/$1';

// Self-probe
$route['api/parity_v28_complete/probe']      = 'ParityV28Complete/probe';

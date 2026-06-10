<?php
// TargetMonitorAgentV28 routes
$route['api/target_monitor/probe']                     = 'TargetMonitorAgentV28/probe';
$route['api/target_monitor/bd/(:num)']                 = 'TargetMonitorAgentV28/bd/$1';
$route['api/target_monitor/bd/(:num)/(:any)']          = 'TargetMonitorAgentV28/bd/$1/$2';
$route['api/target_monitor/sweep']                     = 'TargetMonitorAgentV28/sweep';
$route['api/target_monitor/sweep/(:any)']              = 'TargetMonitorAgentV28/sweep/$1';
$route['api/target_monitor/cm/(:num)']                 = 'TargetMonitorAgentV28/cm/$1';
$route['api/target_monitor/cm/(:num)/(:any)']          = 'TargetMonitorAgentV28/cm/$1/$2';
$route['api/target_monitor/review_flags/(:num)']       = 'TargetMonitorAgentV28/review_flags/$1';

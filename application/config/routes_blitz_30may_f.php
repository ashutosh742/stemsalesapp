<?php
defined('BASEPATH') OR exit('No direct script access allowed');

// === Blitz 30 May Agent F real routes ===
// ConfigApi    : currency, custom_field
// ExpenseApi   : expense list
// GamificationApi : badges
// AuditApi     : field_history
// IncentiveApi : summary
// DistrictIntelApi : digest
// RouteBrainApi : efficiency

// Config endpoints (new tables created by migration_093 and migration_094)
$route['api/config/currency']              = 'ConfigApi/currency';
$route['api/config/custom_field']          = 'ConfigApi/custom_field';

// Expense - unset prior string-type route from routes_mobile_pilot before setting ours
unset($route['api/expense/list']);
$route['api/expense/list']                 = 'ExpenseApi/listing';

// Gamification
$route['api/gamification/badges']          = 'GamificationApi/badges';

// Audit field history
$route['api/audit/field_history']          = 'AuditApi/field_history';

// Incentive summary
$route['api/incentive/summary']            = 'IncentiveApi/summary';

// District Intel digest
$route['api/district_intel/digest']        = 'DistrictIntelApi/digest';

// Route Brain efficiency
$route['api/route_brain/efficiency']       = 'RouteBrainApi/efficiency';

// === END Blitz 30 May Agent F ===

<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * routes_mobile_extras.php - Cards 4,6,8,10,12,14,20
 * Included from routes.php
 * Note: api/day_ceremony/probe is already handled by routes_cron_endpoints.php
 */

// Card 8 - Day Ceremony simple endpoints (supplements existing DayCeremony.php)
// probe already defined in routes_cron_endpoints.php - skip to avoid conflict
$route['api/day_ceremony/start_simple']['post']  = 'MobileExtrasController/day_ceremony_start_simple';
$route['api/day_ceremony/end_simple']['post']    = 'MobileExtrasController/day_ceremony_end_simple';

// Card 10 - Team task check + status change task check
$route['api/check_management/team_task_check']['get']          = 'MobileExtrasController/team_task_check';
$route['api/check_management/status_change_task_check']['get'] = 'MobileExtrasController/status_change_task_check';

// Card 12 - Live BD map
$route['api/team_location/probe']['get'] = 'MobileExtrasController/team_location_probe';
$route['api/team_location/live']['get']  = 'MobileExtrasController/team_location_live';

// Card 14 - Special remarks stream
$route['api/special_remarks/stream']['get'] = 'MobileExtrasController/special_remarks_stream';
$route['api/special_remarks/flag']['post']  = 'MobileExtrasController/special_remarks_flag';

// Card 20 - BD profile drill-down
$route['api/bd_profile/detail']['get']           = 'MobileExtrasController/bd_profile_detail';
$route['api/bd_profile/recent_activity']['get']  = 'MobileExtrasController/bd_profile_recent_activity';

// Card 4 - Review report daily + annual
$route['api/review_report/daily']['get']  = 'MobileExtrasController/review_report_daily';
$route['api/review_report/annual']['get'] = 'MobileExtrasController/review_report_annual';

// Card 6 - App usage drill-down
$route['api/app_usage/per_bd']['get'] = 'MobileExtrasController/app_usage_per_bd';

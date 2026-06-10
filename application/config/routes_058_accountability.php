<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * routes_058_accountability.php - Migration 058 Sales Accountability routes
 * Target cascade + monthly lead review controllers.
 */

// Target cascade
$route['api/target/cascade/probe']    = 'TargetCascadeController/probe';
$route['api/target/cascade/lock']     = 'TargetCascadeController/lock';
$route['api/target/cascade/periods']  = 'TargetCascadeController/periods';

// Monthly lead review (020.1)
$route['api/review/monthly/probe']            = 'MonthlyLeadReviewController/probe';
$route['api/review/monthly/list_audience']    = 'MonthlyLeadReviewController/list_for_audience';
$route['api/review/monthly/manifest']         = 'MonthlyLeadReviewController/manifest';
$route['api/review/monthly/lead/(:num)']      = 'MonthlyLeadReviewController/lead_onepager/$1';
$route['api/review/monthly/bd/(:num)']        = 'MonthlyLeadReviewController/bd_pdf/$1';
$route['api/review/monthly/cm/(:num)']        = 'MonthlyLeadReviewController/cm_pdf/$1';

// ReviewSchedule (058)
$route['api/review_schedule/probe']        = 'ReviewScheduleController/probe';
$route['api/review_schedule/due_today']    = 'ReviewScheduleController/due_today';
$route['api/review_schedule/overdue']      = 'ReviewScheduleController/overdue';
$route['api/review_schedule/seed_week']    = 'ReviewScheduleController/seed_week';
$route['api/review_schedule/for_bd']       = 'ReviewScheduleController/for_bd';
$route['api/review_schedule/for_manager']  = 'ReviewScheduleController/for_manager';


// Target check-in (058 Patch G)
$route['api/target/cascade/checkin_current_week'] = 'TargetCascadeController/checkin_current_week';
$route['api/target/cascade/checkin_submit']       = 'TargetCascadeController/checkin_submit';


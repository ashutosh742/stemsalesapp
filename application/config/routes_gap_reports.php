<?php
// -----------------------------------------------------------------------
// GAP Reports – route additions for Gap_reports_api controller
// Sprint: gap_fix_sprint_2026-06-04
//
// Append (or require) this file inside application/config/routes.php
// BEFORE the catch-all / default_controller line.
// -----------------------------------------------------------------------

$route['api/report/review']           = 'Gap_reports_api/review_report';
$route['api/report/app_usage_time']   = 'Gap_reports_api/app_usage_time';
$route['api/report/leave_management'] = 'Gap_reports_api/leave_management';
$route['api/report/inside_sales']     = 'Gap_reports_api/inside_sales_report';
$route['api/report/star_rating']      = 'Gap_reports_api/star_rating_report';
$route['api/report/location']         = 'Gap_reports_api/location_report';
$route['api/report/special_remarks']  = 'Gap_reports_api/special_remarks_report';
$route['api/report/travel_advance']   = 'Gap_reports_api/travel_advance_report';
$route['api/report/graph_analysis']   = 'Gap_reports_api/graph_analysis_summary';
$route['api/report/travel_cluster']   = 'Gap_reports_api/travel_cluster_report';
$route['api/report/handover_bd_detail'] = 'Gap_reports_api/handover_bd_detail';
$route['api/report/upsell_client']    = 'Gap_reports_api/upsell_client_report';

<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Sales Monitoring Brain - routes (additive, read-only)
 * Built 2026-06-08. Include only; never edits routes.php.
 * All map to MonitoringBrain_api (GET only).
 */
$route['api/brain/probe']         = 'MonitoringBrain_api/probe';
$route['api/brain/kpi']           = 'MonitoringBrain_api/kpi';
$route['api/brain/scorecard']     = 'MonitoringBrain_api/scorecard';
$route['api/brain/send_now']      = 'MonitoringBrain_api/send_now';
$route['api/brain/bottleneck']    = 'MonitoringBrain_api/bottleneck';
$route['api/brain/concentration'] = 'MonitoringBrain_api/concentration';
$route['api/brain/capacity']      = 'MonitoringBrain_api/capacity';
$route['api/brain/flags']         = 'MonitoringBrain_api/flags';
$route['api/brain/data_quality']  = 'MonitoringBrain_api/data_quality';
$route['api/brain/alerts']        = 'MonitoringBrain_api/alerts';
$route['api/brain/digest']        = 'MonitoringBrain_api/digest';

<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * routes_meeting_prep.php - Migration 042 routes for the
 * Corporate Meeting Prep Agent.
 *
 * Append to application/config/routes.php with:
 *   require __DIR__ . '/routes_meeting_prep.php';
 */

$route['api/meeting_prep/probe']       = 'corporatemeetingprepcontroller/probe';
$route['api/meeting_prep/generate']    = 'corporatemeetingprepcontroller/generate';
$route['api/meeting_prep/auto_scan']   = 'corporatemeetingprepcontroller/auto_scan';
$route['api/meeting_prep/artifact']    = 'corporatemeetingprepcontroller/artifact';
$route['api/meeting_prep/runs_today']  = 'corporatemeetingprepcontroller/runs_today';

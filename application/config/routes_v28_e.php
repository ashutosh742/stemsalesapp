<?php
defined('BASEPATH') OR exit('No direct script access allowed');

// === Agent E Meeting Economics real routes (29 May 2026) ===
// Controller: MeetingEconomicsV28
// Tables: tblcallevents, cron_action_minutes, bd_productivity_daily, user

$route['api/meeting_economics/probe']           = 'MeetingEconomicsV28/probe';
$route['api/meeting_economics/today']           = 'MeetingEconomicsV28/today';
$route['api/meeting_economics/weekly']          = 'MeetingEconomicsV28/weekly';
$route['api/meeting_economics/baseline_7d']     = 'MeetingEconomicsV28/baseline_7d';
$route['api/meeting_economics/capture_baseline'] = 'MeetingEconomicsV28/capture_baseline';
$route['api/meeting_economics/capture_today']   = 'MeetingEconomicsV28/capture_today';
$route['api/meeting_economics/cluster_view']    = 'MeetingEconomicsV28/cluster_view';
$route['api/meeting_economics/mix_today']       = 'MeetingEconomicsV28/mix_today';
$route['api/meeting_economics/team_roll_up']    = 'MeetingEconomicsV28/team_roll_up';

// === END Agent E Meeting Economics real routes ===

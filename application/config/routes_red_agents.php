<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * routes_red_agents.php — Route mappings for RED → GREEN agent sprint
 * Gap Fix Sprint 2026-06-04
 *
 * To activate: paste these lines into application/config/routes.php
 * BEFORE the default_controller and 404 lines, OR use CI3's
 * $this->router->route() mechanism to include this file.
 *
 * Controller files:
 *   Anaya_reports  → gap_fix_sprint_2026-06-04/Anaya_reports_patched.php
 *   Proposal_sla   → gap_fix_sprint_2026-06-04/Proposal_sla.php
 *   Greetings      → gap_fix_sprint_2026-06-04/Greetings.php
 */

// ---------------------------------------------------------------------------
// 1. ANAYA REPORTS — /api/anaya_reports/*
// ---------------------------------------------------------------------------
$route['api/anaya_reports/probe']           = 'Anaya_reports/probe';
$route['api/anaya_reports/status']          = 'Anaya_reports/status';
$route['api/anaya_reports/run']             = 'Anaya_reports/run';
$route['api/anaya_reports/weekly_summary']  = 'Anaya_reports/weekly_summary';
$route['api/anaya_reports/monthly_report']  = 'Anaya_reports/monthly_report';
$route['api/anaya_reports/daily_bd']        = 'Anaya_reports/daily_bd';
$route['api/anaya_reports/daily_owner']     = 'Anaya_reports/daily_owner';

// ---------------------------------------------------------------------------
// 2. PROPOSAL SLA — /api/proposal_sla_gap/*
// ---------------------------------------------------------------------------
$route['api/proposal_sla_gap/probe']            = 'Proposal_sla_gap/probe';
$route['api/proposal_sla_gap/status']           = 'Proposal_sla_gap/status';
$route['api/proposal_sla_gap/run']              = 'Proposal_sla_gap/run';
$route['api/proposal_sla_gap/pending']          = 'Proposal_sla_gap/pending';
$route['api/proposal_sla_gap/escalate']         = 'Proposal_sla_gap/escalate';
$route['api/proposal_sla_gap/ack']              = 'Proposal_sla_gap/ack';
$route['api/proposal_sla_gap/open_for_bd']      = 'Proposal_sla_gap/open_for_bd';
$route['api/proposal_sla_gap/breaches_today']   = 'Proposal_sla_gap/breaches_today';

// ---------------------------------------------------------------------------
// 3. GREETINGS — /api/greetings/*
// ---------------------------------------------------------------------------
$route['api/greetings/probe']               = 'Greetings/probe';
$route['api/greetings/status']              = 'Greetings/status';
$route['api/greetings/run']                 = 'Greetings/run';
$route['api/greetings/queue']               = 'Greetings/queue';
$route['api/greetings/send']                = 'Greetings/send';
$route['api/greetings/approve']             = 'Greetings/approve';
$route['api/greetings/today']               = 'Greetings/today';

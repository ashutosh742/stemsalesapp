<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * routes_dashboard_summary_20260610.php  (ADDITIVE - Commit 5, 2026-06-10)
 *
 * READ-ONLY dashboard mirror endpoint. Mirrors Menu::Dashboard() gates + payload.
 * Additive only; included last so it cannot be overridden. Production untouched.
 */
$route['api/dashboard/summary']['get'] = 'Mobile_dashboard_api/summary';

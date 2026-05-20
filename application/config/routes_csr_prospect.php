<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * routes_csr_prospect.php - Migration 041 routes for the
 * Corporate CSR Prospecting Agent.
 *
 * Append to application/config/routes.php with:
 *   require __DIR__ . '/routes_csr_prospect.php';
 */

$route['api/csr_prospect/probe']             = 'corporatecsrprospectcontroller/probe';
$route['api/csr_prospect/refresh_for_bd']    = 'corporatecsrprospectcontroller/refresh_for_bd';
$route['api/csr_prospect/today_for_bd']      = 'corporatecsrprospectcontroller/today_for_bd';
$route['api/csr_prospect/today_summary']     = 'corporatecsrprospectcontroller/today_summary';
$route['api/csr_prospect/accept_and_seed']   = 'corporatecsrprospectcontroller/accept_and_seed';
$route['api/csr_prospect/link_init_call']    = 'corporatecsrprospectcontroller/link_init_call';
$route['api/csr_prospect/dismiss']           = 'corporatecsrprospectcontroller/dismiss';
$route['api/csr_prospect/sync_csr_gov']      = 'corporatecsrprospectcontroller/sync_csr_gov';
$route['api/csr_prospect/sync_apollo']       = 'corporatecsrprospectcontroller/sync_apollo';
$route['api/csr_prospect/corporate/(:num)']  = 'corporatecsrprospectcontroller/corporate/$1';
$route['api/csr_prospect/influencers']       = 'corporatecsrprospectcontroller/influencers';

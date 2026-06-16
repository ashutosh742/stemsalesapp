<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * routes_status_transitions_20260610.php  (ADDITIVE - Area A, 2026-06-10)
 *
 * READ-ONLY status-transition mirror endpoint. Mirrors Menu::getstatusbd() legal
 * cstatus->ystatus transitions. Additive only; included last so it cannot be
 * overridden. Production untouched.
 */
$route['api/status/transitions']['get'] = 'Mobile_status_api/transitions';

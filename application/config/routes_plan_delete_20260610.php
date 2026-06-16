<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * routes_plan_delete_20260610.php  (ADDITIVE - Area D, 2026-06-10)
 *
 * Server DELETE for a planned cell, used by the planner WFFO conflict-modal
 * "Remove" action. Additive only; included last so it cannot be overridden.
 * Production untouched.
 */
$route['api/task/plan_delete']['post'] = 'Mobile_write_api/delete_plan_task';

<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * routes_planner_request_approval_20260610.php  (ADDITIVE - approval chain Step 1)
 *
 * BD submits the day plan for approval. Mirrors Menu::RequestForPlannerApproval:
 * inserts a PENDING planner_approved row for the authed BD + request_date.
 * Additive only; included last so it cannot be overridden. Production untouched.
 */
$route['api/planner/submit_for_approval']['post'] = 'Mobile_write_api/submit_planner_approval';

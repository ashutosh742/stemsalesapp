<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * routes_planner_assign_task_20260610.php  (ADDITIVE - approval chain Step 4)
 *
 * Line manager / CM (re)assigns a planned task in the REAL tblcallevents ledger
 * down to a target BD. Mirrors the production "Assign Task By" write semantics.
 * Additive only; included last so it cannot be overridden. Production untouched.
 */
$route['api/planner/assign_task']['post'] = 'Mobile_write_api/assign_planned_task';

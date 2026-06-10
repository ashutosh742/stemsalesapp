<?php
defined('BASEPATH') OR exit('No direct script access allowed');

// === Agent B DisciplineV28 real routes (29 May 2026) ===
// These override the stub 404 entries for the discipline group.

// Advance sub-routes (must come before the shorter patterns)
$route['api/discipline/advance/list']           = 'v28/DisciplineV28/advance_list';
$route['api/discipline/advance/probe']          = 'v28/DisciplineV28/advance_probe';

// Named discipline routes
$route['api/discipline/advance_aging']          = 'v28/DisciplineV28/advance_aging';
$route['api/discipline/approval_gap']           = 'v28/DisciplineV28/approval_gap';
$route['api/discipline/band_violations']        = 'v28/DisciplineV28/band_violations';
$route['api/discipline/cancellation_advance']   = 'v28/DisciplineV28/cancellation_advance';
$route['api/discipline/execution_gap']          = 'v28/DisciplineV28/execution_gap';
$route['api/discipline/expense_actuals']        = 'v28/DisciplineV28/expense_actuals';
$route['api/discipline/meeting_expense_trail']  = 'v28/DisciplineV28/meeting_expense_trail';
$route['api/discipline/plan_submission']        = 'v28/DisciplineV28/plan_submission';
$route['api/discipline/probe']                  = 'v28/DisciplineV28/probe';
$route['api/discipline/wallet']                 = 'v28/DisciplineV28/wallet';
// === END Agent B DisciplineV28 routes ===

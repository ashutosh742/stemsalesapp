<?php
// STEM CRM Close-Out Routes (2026-06-04)
// Adds routes for 21 stub controllers covering 29 previously-stub endpoints
// Loaded from application/config/routes.php via include() at the end

// === Wallet ===
$route['api/wallet/balance']                 = 'WalletApi/balance_index';
$route['api/wallet/history']                 = 'WalletApi/history_index';

// === Applause ===
$route['api/applause/today']                 = 'Applause_api/today';

// === Config ===
$route['api/config/currency']                = 'ConfigApi/currency';
$route['api/config/custom_field']            = 'ConfigApi/custom_field';

// === Day Plan ===
$route['api/day_plan/today']                 = 'DayPlanController/today';

// === Blitz AI / Coach / Manager / Travel ===
$route['api/ai/win_probability']             = 'BlitzAi_api/win_probability';
$route['api/planner_coach/today']            = 'BlitzCoach_api/today';
$route['api/planner_analyst/today']          = 'BlitzCoach_api/analyst_today';
$route['api/line_manager/leaderboard']       = 'BlitzManager_api/leaderboard';
$route['api/manager_incentive/this_week']    = 'BlitzManager_api/this_week';
$route['api/travel/cluster/bd']              = 'BlitzTravel_api/bd';

// === Callevents ===
$route['api/callevents/list']                = 'CalleventsListController/index';

// === Comm Orchestrator (light wrapper) ===
$route['api/comm/probe']                     = 'CommOrchestratorController/probe';
$route['api/comm/inbox']                     = 'CommOrchestratorController/inbox';

// === CSR Prospect ===
$route['api/csr_prospect/list']              = 'CsrProspect/list_index';

// === Email To Task ===
$route['api/email_to_task/probe']            = 'EmailToTask_api/probe_index';
$route['api/email_to_task/queue']            = 'EmailToTask_api/queue_index';

// === Expense (via Mobile_read_api) ===
$route['api/expense/list']                   = 'Mobile_read_api/expense_list';

// === Gamification ===
$route['api/gamification/badges']            = 'GamificationApi/badges';

// === Handover ===
$route['api/handover/list']                  = 'HandoverList/list_index';

// === Incentive ===
$route['api/incentive/summary']              = 'IncentiveApi/summary';

// === Leave Request ===
$route['api/leave_request/list']             = 'LeaveRequestController/list_index';

// === Productivity ===
$route['api/productivity/bd_today']          = 'ProductivityController/bd_today';
$route['api/productivity/cm_today']          = 'ProductivityController/cm_today';

// === Route Brain ===
$route['api/route_brain/efficiency']         = 'RouteBrainApi/efficiency';

// === Stakeholder Book ===
$route['api/stakeholder/book']               = 'StakeholderBook/book_index';

// === Planner v2 (Blitz 30 May namespace) ===
$route['api/planner_v2/purpose_cascade']     = 'blitz_30may/BlitzPlannerApi/purpose_cascade';
$route['api/mom_v2/queue']                   = 'blitz_30may/BlitzPlannerApi/mom_queue';
$route['api/plan_approval/queue']            = 'blitz_30may/BlitzPlannerApi/plan_approval_queue';

<?php
defined('BASEPATH') OR exit('No direct script access allowed');

// === Agent I real routes (v2.8) - applause, company_details, closure_path,
//     knowledge, efficiency, expense, leaderboard, leads, manager, pipeline, role_play ===

// --- applause (5 routes) ---
$route['api/applause/feed']         = 'v28/ApplauseV28/feed';
$route['api/applause/leaderboard']  = 'v28/ApplauseV28/leaderboard';
$route['api/applause/my_received']  = 'v28/ApplauseV28/my_received';
$route['api/applause/probe']        = 'v28/ApplauseV28/probe';
$route['api/applause/send']         = 'v28/ApplauseV28/send';

// --- company_details (5 routes) ---
$route['api/company_details/current_fy']      = 'v28/CompanyDetailsV28/current_fy';
$route['api/company_details/get/(:num)']      = 'v28/CompanyDetailsV28/get/$1';
$route['api/company_details/history']         = 'v28/CompanyDetailsV28/history';
$route['api/company_details/profile/(:num)']  = 'v28/CompanyDetailsV28/profile/$1';
$route['api/company_details/profile']         = 'v28/CompanyDetailsV28/profile';

// --- closure_path (4 routes) ---
$route['api/closure_path/aging']            = 'v28/ClosurePathV28/aging';
$route['api/closure_path/anchor_renewals']  = 'v28/ClosurePathV28/anchor_renewals';
$route['api/closure_path/blockers']         = 'v28/ClosurePathV28/blockers';
$route['api/closure_path/probe']            = 'v28/ClosurePathV28/probe';

// --- knowledge (4 routes) ---
$route['api/knowledge/library']  = 'v28/KnowledgeV28/library';
$route['api/knowledge/list']     = 'v28/KnowledgeV28/list';
$route['api/knowledge/probe']    = 'v28/KnowledgeV28/probe';
$route['api/knowledge']          = 'v28/KnowledgeV28/index';

// --- efficiency (3 routes) ---
$route['api/efficiency/api_get_bd_score']       = 'v28/EfficiencyV28/api_get_bd_score';
$route['api/efficiency/api_get_cluster_rollup'] = 'v28/EfficiencyV28/api_get_cluster_rollup';
$route['api/efficiency/probe']                  = 'v28/EfficiencyV28/probe';

// --- expense (3 routes) ---
$route['api/expense/bd_summary']  = 'v28/ExpenseV28/bd_summary';
$route['api/expense/probe']       = 'v28/ExpenseV28/probe';
$route['api/expense/submit']      = 'v28/ExpenseV28/submit';

// --- leaderboard (3 routes) ---
$route['api/leaderboard/critical_gaps']  = 'v28/LeaderboardV28/critical_gaps';
$route['api/leaderboard/monthly']        = 'v28/LeaderboardV28/monthly';
$route['api/leaderboard/war_points']     = 'v28/LeaderboardV28/war_points';

// --- leads (3 routes) ---
$route['api/leads/all']    = 'v28/LeadsV28/all';
$route['api/leads/by_bd']  = 'v28/LeadsV28/by_bd';
$route['api/leads/my']     = 'v28/LeadsV28/my';

// --- manager (3 routes) ---
$route['api/manager/incentive/current']  = 'v28/ManagerV28/incentive_current';
$route['api/manager/incentive/history']  = 'v28/ManagerV28/incentive_history';
$route['api/manager/incentive/weekly']   = 'v28/ManagerV28/incentive_weekly';

// --- pipeline (3 routes) ---
$route['api/pipeline/my']       = 'v28/PipelineV28/my';
$route['api/pipeline/summary']  = 'v28/PipelineV28/summary';
$route['api/pipeline']          = 'v28/PipelineV28/index';

// --- role_play (3 routes) ---
$route['api/role_play/list_scenarios']  = 'v28/RolePlayV28/list_scenarios';
$route['api/role_play/sessions']        = 'v28/RolePlayV28/sessions';
$route['api/role_play/start']           = 'v28/RolePlayV28/start';

// === END Agent I real routes ===

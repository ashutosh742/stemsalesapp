<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * routes_parity.php  (Parity Build 2026-06-06, staging only)
 * Additive route shard. Plain-string routes (CI3 filename == classname).
 * Loaded last from routes.php via file_exists+include. Production untouched.
 *
 * Controllers:
 *   application/controllers/Report_api.php          (class Report_api)
 *   application/controllers/api/AnnualReview_api.php (class AnnualReview_api)
 *   application/controllers/api/BulkImport_api.php   (class BulkImport_api)
 *   application/controllers/api/MasterReset_api.php  (class MasterReset_api)
 */

// ---- Reports (Report_api in controllers/ root) ----
$route['api/report/probe']        = 'Report_api/probe';
$route['api/report/funnel']       = 'Report_api/funnel';
$route['api/report/daily']        = 'Report_api/daily';
$route['api/report/review']       = 'Report_api/review';
$route['api/report/cash_expense'] = 'Report_api/cash_expense';
$route['api/report/planner']      = 'Report_api/planner';

// ---- Annual Review (controllers/api/AnnualReview_api.php; CI3 targets class name only) ----
$route['api/annual_review/probe']   = 'AnnualReview_api/probe';
$route['api/annual_review/pending'] = 'AnnualReview_api/pending';
// App calls 'list' -> alias to pending (the awaiting-approval list)
$route['api/annual_review/list']    = 'AnnualReview_api/pending';
$route['api/annual_review/detail']  = 'AnnualReview_api/detail';
$route['api/annual_review/approve'] = 'AnnualReview_api/approve';
$route['api/annual_review/reject']  = 'AnnualReview_api/reject';

// ---- Bulk Import (controllers/api/BulkImport_api.php) ----
$route['api/import/probe']     = 'BulkImport_api/probe';
$route['api/import/validate']  = 'BulkImport_api/validate';
$route['api/import/commit']    = 'BulkImport_api/commit';
// App calls bulk_import/upload + bulk_import/commit -> alias to validate/commit
$route['api/bulk_import/probe']    = 'BulkImport_api/probe';
$route['api/bulk_import/upload']   = 'BulkImport_api/validate';
$route['api/bulk_import/validate'] = 'BulkImport_api/validate';
$route['api/bulk_import/commit']   = 'BulkImport_api/commit';

// ---- Master Reset (controllers/api/MasterReset_api.php) ----
$route['api/master_reset/probe']   = 'MasterReset_api/probe';
$route['api/master_reset/preview'] = 'MasterReset_api/preview';
$route['api/master_reset/execute'] = 'MasterReset_api/execute';

// ---- PlannerExtra_api (Agent H, 2026-06-06) ----
$route['api/planner/v2/time_budget']       = 'PlannerExtra_api/time_budget';
$route['api/planner/time_budget']          = 'PlannerExtra_api/time_budget';  // rimlyproof 20260608: bare endpoint parity
$route['api/planner/time_budget/request']  = 'PlannerExtra_api/time_budget_request';
$route['api/planner/extra/probe']          = 'PlannerExtra_api/probe';

// ---- DayMgmtExtra_api (Agent H, 2026-06-06) ----
$route['api/day_management/yesterday_close_status']  = 'DayMgmtExtra_api/yesterday_close_status';
$route['api/day_management/yesterday_close_request'] = 'DayMgmtExtra_api/yesterday_close_request';
$route['api/day_management/yesterday_day_close']     = 'DayMgmtExtra_api/yesterday_day_close';
$route['api/day_management/change_start_request']    = 'DayMgmtExtra_api/change_start_request';
$route['api/day_management/probe']                   = 'DayMgmtExtra_api/probe';

// ---- Day Ceremony Advanced — geo_context (Agent G, 2026-06-06) ----
// Explicit route for GET /api/day_ceremony/geo_context?uid=X
// Controller: Day_ceremony_api (application/controllers/Day_ceremony_api.php)
// Note: routes_cron_endpoints.php wildcard api/day_ceremony/(:any) also covers this;
//       explicit route documented here per additive-shard convention.
$route["api/day_ceremony/geo_context"] = "Day_ceremony_api/geo_context";

// ---- CompanyDetail_api (Company Details mirror, 2026-06-06) ----
$route["api/company/detail"] = "CompanyDetail_api/detail";
$route["api/company/probe"]  = "CompanyDetail_api/probe";

// ---- Audit D fixes 2026-06-06 ----
// Annual review: start (POST) - missing in original parity routes
$route["api/annual_review/start"]  = "AnnualReview_api/start";

// Review schedule: expose start_session and close_session for daily review flow
// These map to Review_api methods for mobile start/close actions
$route["api/review_schedule/start_session"]  = "ReviewScheduleController/start_session";
$route["api/review_schedule/close_session"]  = "ReviewScheduleController/close_session";
$route["api/review_schedule/action_detail"]  = "ReviewScheduleController/action_detail";

// Cash expense approve/reject for manager (CashExpenseReport flow)
$route["api/expense/approve"]  = "ExpenseApi/approve_expense";
$route["api/expense/reject"]   = "ExpenseApi/reject_expense";

// Review detail by schedule id (ReviewDetail production equivalent)
$route["api/review_schedule/detail"]         = "ReviewScheduleController/detail";

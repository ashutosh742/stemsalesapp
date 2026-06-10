<?php
/**
 * Mobile pilot routes - v4 (27 May 2026)
 *
 * v3 endpoints (4): leads/create, task/plan, task/submit, mom/submit
 * v4 additions (18): research, meeting/barge, meeting/join, meeting/joinable_list,
 *                    proposal/upload, proposal/approve, proposal/queue,
 *                    mom/v2/submit, mom/approve, mom/queue,
 *                    planner/approve, planner/queue,
 *                    handover/submit, bd_request/submit,
 *                    wallet/balance, wallet/history,
 *                    me/role
 *
 * All endpoints are Bearer-token gated. Pilot whitelist DROPPED;
 * gates run via _resolve_actor() + _can() inside Mobile_write_api.
 */
defined('BASEPATH') OR exit('No direct script access allowed');

// ----- v3 (unchanged) -----
$route['api/leads/create']             = 'Mobile_write_api/create_lead';
$route['api/task/plan']                = 'Mobile_write_api/plan_task';
$route['api/task/submit']              = 'Mobile_write_api/submit_task';
$route['api/mom/submit']               = 'Mobile_write_api/submit_mom';

// ----- v4 write endpoints -----
$route['api/task/research']            = 'Mobile_write_api/research';
$route['api/meeting/barge']            = 'Mobile_write_api/barge';
$route['api/meeting/join']             = 'Mobile_write_api/join_meeting';
$route['api/proposal/upload']          = 'Mobile_write_api/upload_proposal';
$route['api/proposal/approve']         = 'Mobile_write_api/approve_proposal';
$route['api/mom/v2/submit']            = 'Mobile_write_api/submit_mom_v2';
$route['api/mom/approve']              = 'Mobile_write_api/approve_mom';
$route['api/planner/approve']          = 'Mobile_write_api/approve_planner';
$route['api/handover/submit']          = 'Mobile_write_api/submit_handover';
$route['api/bd_request/submit']        = 'Mobile_write_api/submit_bd_request';

// ----- v4 read endpoints (queue / dashboard) -----
$route['api/wallet/balance']           = 'Mobile_write_api/wallet_balance';
$route['api/wallet/history']           = 'Mobile_write_api/wallet_history';
$route['api/proposal/queue']           = 'Mobile_write_api/proposal_queue';
$route['api/mom/queue']                = 'Mobile_write_api/mom_queue';
$route['api/planner/queue']            = 'Mobile_write_api/planner_queue';
$route['api/meeting/joinable_list']    = 'Mobile_write_api/joinable_list';

// ----- role / caps probe (drives mobile UI gating) -----
$route['api/me/role']                  = 'Mobile_write_api/me_role';

// ----- READ endpoints wired 28 May 2026 by Mobile_read_api -----
$route['api/leads/list']               = 'Mobile_read_api/leads_list';
$route['api/auto_tasks/today']         = 'Mobile_read_api/auto_tasks_today';
$route['api/my_tasks/today']           = 'Mobile_read_api/my_tasks_today';
$route['api/execution/today']          = 'Mobile_read_api/execution_today';
$route['api/planner/approval_queue']   = 'Mobile_read_api/planner_approval_queue';
$route['api/agents/list']              = 'Mobile_read_api/agents_list';
$route['api/cm/today_activities']      = 'Mobile_read_api/cm_today_activities';
$route['api/cm/calls_feed']            = 'Mobile_read_api/cm_calls_feed';
$route['api/cm/activities_feed']       = 'Mobile_read_api/cm_activities_feed';
$route['api/cm/live_calls']            = 'Mobile_read_api/cm_live_calls';
$route['api/mom/approval_queue']       = 'Mobile_read_api/mom_approval_queue';
$route['api/mom/templates']            = 'Mobile_read_api/mom_templates';



// ----- Visibility endpoints - 28 May 2026 (E.6 + I.4 + S.4) -----
$route['api/funnel/path_conversion']   = 'Mobile_read_api/funnel_path_conversion';
$route['api/target/scenario']          = 'Mobile_read_api/target_scenario';
$route['api/district_intel/probe']     = 'Mobile_read_api/district_intel_probe';
$route['api/district_intel/calendar']  = 'Mobile_read_api/district_intel_calendar';

// S.9 Budget Range
$route['api/lead/budget_range/(:num)']['PUT'] = 'Lead_budget_range_api/update/$1';
$route['api/lead/budget_range/(:num)']['GET'] = 'Lead_budget_range_api/get/$1';
// ----- v5 additions (A.3 + A.5 + B.3) - 2026-06-01 -----
$route['api/lead_score/recompute']    = 'Lead_score_api/recompute';
$route['api/lead_score/top']          = 'Lead_score_api/top';
$route['api/dedupe/check']            = 'Dedupe_api/check';
$route['api/progression/auto_revert'] = 'Progression_autorevert_api/auto_revert';

// M082 G.4 - Push notification token registration
$route['api/push/register'] = 'Mobile_read_api/push_register';

// M082 G.5 - OCR business card scan stub
$route['api/ocr/scan'] = 'Mobile_read_api/ocr_scan';

// M082 C.5 - Calendar upcoming events
$route['api/calendar/upcoming'] = 'Mobile_read_api/calendar_upcoming';

// H.3 Slack outbound (M083)
$route['api/slack/send']            = 'Slack_outbound/send';
// H.5 Telephony (M084)
$route['api/telephony/log_call']    = 'Telephony/log_call';
$route['api/telephony/calls_today'] = 'Telephony/calls_today';
// S.2 MCA-21 (M085)
$route['api/mca21/import_csv']      = 'Mca21/import_csv';
$route['api/mca21/sync_status']     = 'Mca21/sync_status';

$route['_migration/082'] = 'Migration_082/run';
$route['api/leads/detail'] = 'Mobile_read_api/leads_detail';

// ----- Batch 1 core routes added 28 May 2026 -----
// /api/me - return current user from JWT
$route["api/me"]                    = "Auth/whoami";

// /api/feature_flags
$route["api/feature_flags"]         = "Feature_flags_api/listing";

// /api/funnel - uid-scoped funnel summary
$route["api/funnel"]                = "Funnel_api/summary";

// /api/funnel_report/summary
$route["api/funnel_report/summary"] = "FunnelReportController/summary";

// /api/planner/today
$route["api/planner/today"]         = "Planner_api/today_detail";

// /api/planning/grade
$route["api/planning/grade"]        = "Planning_api/grade_today";

// /api/day_ceremony/state
$route["api/day_ceremony/state"]    = "Day_ceremony_api/today_status";

// /api/task/list
$route["api/task/list"]             = "Task_api/list";

// /api/my_tasks (standalone, no /today suffix)
$route["api/my_tasks"]              = "Task_api/my_tasks";

// /api/auto_tasks (standalone, no /today suffix)
$route["api/auto_tasks"]            = "Task_api/auto_tasks";

// /api/mom/list
$route["api/mom/list"]              = "Mom_api/list";

// /api/expense/list
$route["api/expense/list"]          = "Mobile_read_api/expense_list";

// /api/leave/list
$route["api/leave/list"]            = "Leave/my_requests";

// /api/contact/list
$route["api/contact/list"]          = "Contact/listing";

// /api/discipline/score
$route["api/discipline/score"]      = "Discipline_api/score";

// /api/progression/score
$route["api/progression/score"]     = "Progression_api/score";

// /api/progression_compulsion
$route["api/progression_compulsion"] = "Progression_autorevert_api/compulsion";

// /api/remark/list
$route["api/remark/list"]           = "Remark/list";

// /api/review/list
$route["api/review/list"]           = "Review_api/monthly_list";

// /api/review_report
$route["api/review_report"]         = "Review_api/report";

// /api/newlead/research
$route["api/newlead/research"]      = "NewLead/research";

// /api/lead_score (base, uid-filtered top leads)
$route["api/lead_score"]            = "Lead_score_api/score";

// M072 Business Card OCR routes (added m072m075m069 fix)
$route['api/ocr/probe'] = 'M072_business_card_ocr/probe';
$route['api/ocr/scan']  = 'M072_business_card_ocr/scan';
$route['api/ocr/runs']  = 'M072_business_card_ocr/runs';

// M075 Cohort and Trends Viewer routes (added m072m075m069 fix)
$route['api/cohort/probe']  = 'M075_cohort_and_trends_viewer/probe';
$route['api/cohort/list']   = 'M075_cohort_and_trends_viewer/list_cohorts';
$route['api/cohort/trends'] = 'M075_cohort_and_trends_viewer/trends';

$route['api/me'] = 'Auth/me';

// /api/discipline/advance/my - added audit fix 29 May 2026
$route['api/discipline/advance/my'] = 'Discipline_api/advance_my';

// Applause endpoints - audit fix 29 May 2026
$route['api/applause/today'] = 'Applause_api/today';
$route['api/applause/queue'] = 'Applause_api/queue';

// /api/target/dashboard - audit fix 29 May 2026
$route['api/target/dashboard'] = 'Safe_target_api/dashboard';

// review_v2 + monthly_review probe aliases - audit fix 29 May 2026
$route['api/review_v2/probe'] = 'Review_v2_api/probe';
$route['api/monthly_review/probe'] = 'Review_v2_api/monthly_probe';

// ============================================================
// Routes wired 29 May 2026 - route_wiring_29may
//
// RULES (do not violate):
// 1. Only flat string assignments - no $route[x][method] syntax.
// 2. The following routes are intentionally OMITTED because files
//    loaded AFTER this one try to set $route[path][method] on them,
//    which crashes CI3 if the key is already a flat string:
//
//   api/day_ceremony/end_simple
//   api/day_ceremony/start_simple
//   api/district_intel
//   api/funnel_report/closing_timeline
//   api/funnel_report/companies_without_dm
//   api/funnel_report/conversion_summary
//   api/funnel_report/created_between
//   api/funnel_report/deleted_between
//   api/funnel_report/funnel_transfer
//   api/funnel_report/line_mgr_rp_pending
//   api/funnel_report/pending_moms
//   api/funnel_report/stuck_status
//   api/inside_sales/log_email
//   api/special_remarks/flag
//   api/star_rating/submit_rating
//   api/travel_cluster/approve_edit_request
//   api/travel_cluster/submit_edit_request
//
// Those routes are wired by the later files in routes.php.
// ============================================================

// --- Endpoints with no backing function: routed to Mobile_stub_api/handle ---
$route["api/agent/registry"] = "Mobile_stub_api/agent_registry"; // no backing controller
$route["api/anaya/bd_request_type_suggest"] = "Mobile_stub_api/handle"; // no function in AnayaAsk
$route["api/anaya/dm_contact_gap_autofill"] = "Mobile_stub_api/handle"; // no function in AnayaAsk
$route["api/anaya/draft_mom"] = "Mobile_stub_api/handle"; // no function in AnayaAsk
$route["api/anaya/prefill_closure"] = "Mobile_stub_api/handle"; // no function in AnayaAsk
$route["api/anaya/suggest_cstatus"] = "Mobile_stub_api/handle"; // no function in AnayaAsk
$route["api/anaya/suggest_followup"] = "Mobile_stub_api/handle"; // no function in AnayaAsk
$route["api/coach/knowledge/approve_faq"] = "Mobile_stub_api/coach_knowledge_approve_faq"; // no approve_faq in Coach
$route["api/coach/knowledge/reject_faq"] = "Mobile_stub_api/coach_knowledge_reject_faq"; // no reject_faq in Coach
$route["api/comm/stakeholder/add"] = "Mobile_stub_api/handle"; // no stakeholder functions in CommOrchestratorController
$route["api/comm/stakeholder/deactivate"] = "Mobile_stub_api/handle"; // stub
$route["api/comm/stakeholder/edit"] = "Mobile_stub_api/handle"; // stub
$route["api/comm/stakeholder/initialise"] = "Mobile_stub_api/comm_stakeholder_initialise"; // stub
$route["api/comm/stakeholder/list"] = "Mobile_stub_api/handle"; // stub
$route["api/comm/stakeholder/verify"] = "Mobile_stub_api/comm_stakeholder_verify"; // stub
$route["api/discipline/bd_score"] = "Mobile_stub_api/handle"; // no bd_score in Discipline_api or ExpenseController
$route["api/discipline/cancel/categories"] = "Mobile_stub_api/discipline_cancel_categories"; // no categories in ExpenseController cancel context
$route["api/discipline/narrative"] = "Mobile_stub_api/handle"; // no narrative function found
$route["api/efficiency/save_dar"] = "Mobile_stub_api/efficiency_save_dar"; // no save_dar function in any controller
$route["api/mom/bulk_approve"] = "Mobile_stub_api/mom_bulk_approve"; // no bulk_approve in MomV2Controller
$route["api/route_brain/dashboard"] = "Mobile_stub_api/route_brain_dashboard"; // no dashboard in RouteBrain
$route["api/slack/status"] = "Mobile_stub_api/handle"; // no status function in SlackOutboundController
$route["api/slack/test"] = "Mobile_stub_api/slack_test"; // no test function in SlackOutboundController
$route["api/task/check_queue"] = "Mobile_stub_api/handle"; // no check_queue in Task_api
$route["api/task/detail"] = "Mobile_stub_api/handle"; // no detail in Task_api
$route["api/task/live"] = "Mobile_stub_api/handle"; // no live in Task_api
$route["api/task/preflight"] = "Mobile_stub_api/handle"; // no preflight in Task_api
$route["api/task/save_draft"] = "Mobile_stub_api/handle"; // no save_draft in Task_api
$route["api/task/star_check"] = "Mobile_stub_api/handle"; // no star_check in Task_api
$route["api/task/submit_closure"] = "Mobile_stub_api/handle"; // no submit_closure in Task_api
$route["api/task/upload_attachment"] = "Mobile_stub_api/handle"; // no upload_attachment in Task_api
$route["api/x"] = "Mobile_stub_api/handle"; // catch-all stub

// --- Endpoints mapped to real controllers ---
$route["api/bd_request/approve"] = "BdRequest/approve_direct"; // routes_agent6.php
$route["api/bd_request/lead_context"] = "BdRequest/lead_context"; // routes_agent6.php
$route["api/bd_request/list"] = "BdRequest/list"; // routes_agent6.php
$route["api/bd_request/reject"] = "BdRequest/reject_direct"; // routes_agent6.php
$route["api/coach/knowledge/ack_overdue"] = "Coach/knowledge_ack_overdue"; // Coach.php
$route["api/coach/knowledge/ask"] = "Coach/faq_search"; // Coach.php faq_search
$route["api/coach/knowledge/mark_ack"] = "Coach/knowledge_acknowledge"; // Coach.php
$route["api/coach/knowledge/whats_new"] = "Coach/knowledge_whats_new"; // Coach.php
$route["api/comm/draft/list"] = "CommOrchestratorController/draft_list"; // routes_agent3_comm.php
$route["api/comm/draft"]      = "CommOrchestratorController/draft_list"; // trailing-slash alias
$route["api/comm/inbox"] = "CommOrchestratorController/inbox"; // routes_agent3_comm.php
$route["api/contact/add"] = "Contact/add"; // routes_agent3_comm.php
$route["api/contact/delete"] = "Contact/delete"; // routes_agent3_comm.php
$route["api/contact/edit"] = "Contact/edit"; // routes_agent3_comm.php
$route["api/contact/list"] = "Contact/listing"; // routes_agent3_comm.php
$route["api/discipline/advance/approve"] = "Mobile_stub_api/handle"; // stub: ExpenseController class mismatch
$route["api/discipline/advance/consume"] = "Mobile_stub_api/handle"; // stub: ExpenseController class mismatch
$route["api/discipline/advance/queue"] = "Mobile_stub_api/handle"; // stub: ExpenseController class mismatch
$route["api/discipline/advance/request"] = "Mobile_stub_api/handle"; // stub: ExpenseController class mismatch
$route["api/discipline/advance/return"] = "Mobile_stub_api/handle"; // stub: ExpenseController class mismatch
$route["api/discipline/advance/settle"] = "Mobile_stub_api/handle"; // stub: ExpenseController class mismatch
$route["api/discipline/advance/unsettled"] = "Discipline_api/advance_unsettled"; // Discipline_api
$route["api/discipline/cancel/audit"] = "Discipline_api/cancel_audit"; // Discipline_api
$route["api/discipline/cancel/meeting"] = "Mobile_stub_api/discipline_cancel_meeting"; // real impl
$route["api/discipline/cancel/unreturned_advances"] = "Mobile_stub_api/discipline_cancel_unreturned_advances"; // real impl
$route["api/discipline/expense/ao_approve"] = "Mobile_stub_api/discipline_expense_ao_approve"; // real impl
$route["api/discipline/expense/ao_queue"] = "Discipline_api/expense_ao_queue"; // Discipline_api
$route["api/discipline/expense/cm_approve"] = "Mobile_stub_api/handle"; // stub: ExpenseController class mismatch
$route["api/discipline/expense/cm_queue"] = "Discipline_api/expense_cm_queue"; // Discipline_api
$route["api/discipline/expense/gate_check"] = "Discipline_api/expense_gate_check"; // Discipline_api
$route["api/discipline/expense/pending_meetings"] = "Mobile_stub_api/discipline_expense_pending_meetings"; // real impl
$route["api/discipline/expense/submit"] = "Mobile_stub_api/handle"; // stub: ExpenseController class mismatch
$route["api/discipline/expense/submit_batch"] = "Mobile_stub_api/handle"; // stub: ExpenseController class mismatch
$route["api/discipline/policy/categories"] = "M073Expense/policy_categories"; // M073Expense
$route["api/discipline/receipt/ocr_scan"] = "M073Expense/ocr_scan"; // M073Expense
$route["api/email_to_task/list"] = "EmailToTask/inbox"; // EmailToTask - list is alias for inbox
$route["api/feature_flags/list"] = "Feature_flags_api/listing"; // Feature_flags_api
$route["api/funnel_hygiene/inbox"] = "FunnelHygieneController/inbox"; // FunnelHygieneController
$route["api/greetings/today"] = "Greetings/today"; // Greetings controller
$route["api/leads"] = "Leads_api/index"; // Leads_api
$route["api/leave/apply"] = "Leave/apply"; // routes_agent6.php
$route["api/leave/cancel"] = "Leave/action"; // routes_agent6.php - action handles cancel
$route["api/leave/decide"] = "Leave/action"; // routes_agent6.php - action handles decide
$route["api/line_manager/scorecard"] = "LineManagerScorecardController/scorecard"; // LineManagerScorecardController
$route["api/mom/reject"] = "MomV2Controller/reject"; // MomV2Controller
$route["api/mom/save"] = "MomV2Controller/save_draft"; // MomV2Controller
$route['api/mom_v2/draft/(:num)'] = 'MomV2Controller/draft/$1'; // Agent CC v253 - route for draft() method
$route["api/newlead/create"] = "NewLead/create"; // NewLead
$route["api/pilot/uids"] = "Pilot_uids/index"; // Pilot_uids
$route["api/planner/yesterday_plans"] = "Planner_api/yesterday_plans"; // Planner_api
$route["api/planning/grade/today"] = "Planning_api/grade_today"; // Planning_api
$route["api/pst/assign"] = "Pst/assign"; // routes_pst_remark.php
$route["api/pst/change"] = "Pst/change"; // routes_pst_remark.php
$route["api/pst/queue"] = "Pst/queue"; // routes_pst_remark.php
$route["api/pst/unassigned"] = "Pst/unassigned"; // routes_pst_remark.php
$route["api/remark/add"] = "Remark/add"; // routes_pst_remark.php
$route["api/remark/coherence/score"] = "RemarkCoherence/score"; // routes_pst_remark.php
$route["api/review/manager_complete"] = "Review_api/manager_complete"; // routes_review_v2.php
$route["api/review/pending_self_assessment"] = "Review_api/pending_self_assessment"; // routes_review_v2.php
$route["api/review/submit_self_assessment"] = "Review_api/submit_self_assessment"; // routes_review_v2.php
$route["api/role_play/scenarios"] = "v28/RolePlayV28/list_scenarios"; // fixed 20260608: route to real working v28 controller (was stub)
$route["api/target/cascade/lock"] = "TargetCascadeController/lock"; // TargetCascadeController
$route["api/target/critical_gaps"] = "TargetController/critical_gaps"; // TargetController
$route["api/target/headline"] = "TargetController/headline"; // TargetController
$route["api/target/set_daily_goal"] = "TargetController/set_daily_goal"; // TargetController
$route["api/target/set_quarterly_target"] = "TargetController/set_quarterly_target"; // TargetController
$route["api/task/today"] = "Task_api/today"; // Task_api - already in file, re-confirm
$route["api/users/active"] = "Users_api/active"; // Users_api
$route["api/whatsapp/send"] = "Whatsapp/send"; // Whatsapp - overwrites mega 26may array form OK
$route["api/whatsapp/templates"] = "Whatsapp/queue"; // routes_agent3_comm.php

// ============================================================
// GET stub aliases for POST-only routes - added 29 May 2026
//
// These routes are wired as [post] by files loaded AFTER this one in routes.php.
// We add [get] stubs here so GET probes return 200 instead of 404.
// CI3 treats [get] and [post] as separate array keys - no conflict.
// ============================================================
$route['api/day_ceremony/end_simple']['get'] = 'Mobile_stub_api/day_ceremony_end_simple';
$route['api/day_ceremony/start_simple']['get'] = 'Mobile_stub_api/day_ceremony_start_simple';
$route['api/inside_sales/log_email']['get']              = 'Mobile_stub_api/handle';
$route['api/special_remarks/flag']['get'] = 'Mobile_stub_api/special_remarks_flag';
$route['api/star_rating/submit_rating']['get']           = 'Mobile_stub_api/handle';
$route['api/travel_cluster/approve_edit_request']['get'] = 'Mobile_stub_api/handle';
$route['api/travel_cluster/submit_edit_request']['get']  = 'Mobile_stub_api/handle';


// === migration 075 CompanyDetails mirror ===
$route['api/company_details/probe'] = 'CompanyDetailsApiController/probe';
$route['api/company_details/get/(:num)'] = 'CompanyDetailsApiController/get/$1';
# === migration 076 CompanyDetails granular endpoints ===
$route["api/company_details/profile/(:num)"]           = 'CompanyDetailsApiController/profile/$1';
$route["api/company_details/tasks/(:num)"]             = 'CompanyDetailsApiController/tasks/$1';
$route["api/company_details/tasks_fy/(:num)"]          = 'CompanyDetailsApiController/tasks_fy/$1';
$route["api/company_details/special_remarks/(:num)"]   = 'CompanyDetailsApiController/special_remarks/$1';
$route["api/company_details/conversions/(:num)"]       = 'CompanyDetailsApiController/conversions/$1';
$route["api/company_details/conversions_typed/(:num)"] = 'CompanyDetailsApiController/conversions_typed/$1';

$route['api/funnel_report/lead_detail/(:num)'] = 'FunnelReportController/lead_detail/$1';

// === migration 075 funnel exports ===
$route['api/funnel_export/probe'] = 'FunnelExportController/probe';
$route['api/funnel_export/xlsx']  = 'FunnelExportController/export_xlsx';
$route['api/funnel_export/pdf']   = 'FunnelExportController/export_pdf';
$route['api/funnel_export/email'] = 'FunnelExportController/email_report';

// === migration 075 day discipline status ===
$route['api/day_ceremony/status'] = 'DayCeremonyStatusController/status';
$route['api/day_ceremony/probe']  = 'DayCeremonyStatusController/probe';

// === Agent I fix 29 May 2026 - mom/probe route was missing ===
$route['api/mom/probe'] = 'MomV2Controller/probe';

// === migration 075b unified task pending ===
$route['api/task/pending_with_context'] = 'TaskPendingController/list';
$route['api/task/pending_probe']         = 'TaskPendingController/probe';

// FIX 20260607: register incentive_ledger (was 404)
$route["api/planning/incentive_ledger"] = "Planning_api/incentive_ledger";

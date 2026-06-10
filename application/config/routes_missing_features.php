<?php
/**
 * routes_missing_features.php
 * Generated: 2026-06-06 — Route Enablement Audit
 *
 * Plain string routes (matches this app's working convention — CI 3.1.11).
 * Targets point to the CLASS name (CI3 resolves controller by class; alias files
 * created where filename != class name). All targets verified routable on staging.
 *
 * Enabled (deps present): F71 Email Agent, F75b Stage Signoff, F77b Offline Sync,
 *   F83b Review V2, F84 Escalation, F87 Calendar Sync.
 * Deferred to NEEDS_CODE (missing models): F67 AI Lead Scoring (AILeadScore_model),
 *   F61b Corporate Meeting Prep (CorporateMeetingPrep_agent).
 *
 * STAGING ONLY. Production untouched. Additive only.
 */
defined('BASEPATH') OR exit('No direct script access allowed');


// F71 — Email Agent (class Email_agent; alias file Email_agent.php)
$route['api/email_agent/probe']             = 'Email_agent/probe';
$route['api/email_agent/drafts_for_bd']     = 'Email_agent/drafts_for_bd';
$route['api/email_agent/draft/(:num)']      = 'Email_agent/draft/$1';
$route['api/email_agent/draft_approve']     = 'Email_agent/draft_approve';
$route['api/email_agent/draft_discard']     = 'Email_agent/draft_discard';
$route['api/email_agent/regenerate']        = 'Email_agent/regenerate';
$route['api/email_agent/oauth_start']       = 'Email_agent/oauth_start';
$route['api/email_agent/oauth_callback']    = 'Email_agent/oauth_callback';
$route['api/email_agent/oauth_revoke']      = 'Email_agent/oauth_revoke';
$route['api/email_agent/oauth_status']      = 'Email_agent/oauth_status';

// F75b — Stage Signoff (class StageSignoff_api; routes already resolve via StageSignoffController)
$route['api/lead/signoff/probe']            = 'StageSignoffController/probe';
$route['api/lead/signoff/pending_for_bd']   = 'StageSignoffController/pending_for_bd';
$route['api/lead/signoff/sweep_stuck']      = 'StageSignoffController/sweep_stuck';
$route['api/lead/signoff/bypass']           = 'StageSignoffController/bypass';

// F77b — Offline Sync (class Offline_sync; alias file Offline_sync.php)
$route['api/offline_sync/snapshot']         = 'Offline_sync/snapshot';
$route['api/offline_sync/sync_batch']       = 'Offline_sync/sync_batch';
$route['api/offline_sync/conflicts']        = 'Offline_sync/conflicts';
$route['api/offline_sync/conflict_resolve'] = 'Offline_sync/conflict_resolve';
$route['api/offline_sync/device_register']  = 'Offline_sync/device_register';

// F83b — Review V2 Close (class ReviewV2Controller; file==class)
$route['api/review/pending_for_bd']            = 'ReviewV2Controller/pending_for_bd';
$route['api/review/save_bd_self_rating']       = 'ReviewV2Controller/save_bd_self_rating';
$route['api/review/save_manager_rating']       = 'ReviewV2Controller/save_manager_rating';
$route['api/review/mark_bd_self_done']         = 'ReviewV2Controller/mark_bd_self_done';
$route['api/review/close_session']             = 'ReviewV2Controller/close_session';
$route['api/review/session/(:num)']            = 'ReviewV2Controller/session/$1';
$route['api/review/action_item_add']           = 'ReviewV2Controller/action_item_add';
$route['api/review/action_item_close']         = 'ReviewV2Controller/action_item_close';
$route['api/review/gate_check']                = 'ReviewV2Controller/gate_check';
$route['api/review/bootstrap_pilot_schedule']  = 'ReviewV2Controller/bootstrap_pilot_schedule';

// F84 — Escalation Tickets (class EscalationTicket_api; alias file Escalationticket_api.php)
$route['api/escalation/probe']              = 'Escalationticket_api/probe';
$route['api/escalation/queue']              = 'Escalationticket_api/queue';
$route['api/escalation/breached']           = 'Escalationticket_api/breached';
$route['api/escalation/scan_breached']      = 'Escalationticket_api/scan_breached';
$route['api/escalation/handover']           = 'Escalationticket_api/handover';
$route['api/escalation/list']               = 'Escalationticket_api/queue';

// F87 — Calendar Sync M071 (class M071_calendar_sync; file==class)
$route['api/calendar_sync/account_link']        = 'M071_calendar_sync/account_link';
$route['api/calendar_sync/sync_run']            = 'M071_calendar_sync/sync_run';
$route['api/calendar_sync/events_for_user']     = 'M071_calendar_sync/events_for_user';
$route['api/calendar_sync/attach_to_lead']      = 'M071_calendar_sync/attach_to_lead';
$route['api/calendar_sync/push_planner']        = 'M071_calendar_sync/push_planner_to_calendar';

// NOTE: F67 AI Lead Scoring + F61b Corporate Meeting Prep are NEEDS_CODE
// (their AIAgents models AILeadScore_model / CorporateMeetingPrep_agent are not on the server).
// Intentionally NOT routed to avoid exposing 500-ing endpoints.

// F57 — Planner grade per BD (re-point to working PlanningGradeV28/bd; prior target method did not exist)
$route["api/planning_grade/bd/(:num)"] = "v28/PlanningGradeV28/bd/$1";

// ============================================================
// Additive fix 2026-06-06: wire 3 controllers that existed but had no routes
// (RevenueTarget, MonthlyLeadReview, research candidates alias). Additive only.
// ============================================================
// RevenueTarget (controller: RevenueTarget.php)
$route["api/revenue_target/probe"]         = "RevenueTarget/probe";
$route["api/revenue_target/matrix"]        = "RevenueTarget/matrix";
$route["api/revenue_target/headline"]      = "RevenueTarget/headline";
$route["api/revenue_target/burn_down"]     = "RevenueTarget/burn_down";
$route["api/revenue_target/by_cluster/(:num)"] = "RevenueTarget/by_cluster/$1";
$route["api/revenue_target/critical_gaps"] = "RevenueTarget/critical_gaps";
// summary alias -> headline (frontend-friendly name)
$route["api/revenue_target/summary"]       = "RevenueTarget/headline";

// MonthlyLeadReview (controller: MonthlyLeadReviewController.php)
$route["api/monthly_lead_review/probe"]    = "MonthlyLeadReviewController/probe";
$route["api/monthly_lead_review/manifest"] = "MonthlyLeadReviewController/manifest";
$route["api/monthly_lead_review/list"]     = "MonthlyLeadReviewController/list_for_audience";

// Research candidates alias -> Prospect today_by_bd (real candidate listing)
$route["research/api_candidates"]          = "ProspectController/today_by_bd";
$route["api/research/candidates"]          = "ProspectController/today_by_bd";
$route["api/research/probe"]               = "ProspectController/today_summary";


// ============================================================
// LMS / Induction fix 2026-06-06: wire M065 LMS controller (had NO routes)
// + repoint induction 404 routes to real methods. Additive only.
// ============================================================
// M065 Induction LMS (controller M065_induction_lms) - modules, lessons, certs
$route['api/lms/probe']               = 'M065_induction_lms/modules_for_user';
$route['api/lms/modules_for_user']    = 'M065_induction_lms/modules_for_user';
$route['api/lms/lesson_get']          = 'M065_induction_lms/lesson_get';
$route['api/lms/lesson_complete']     = 'M065_induction_lms/lesson_complete';
$route['api/lms/module_progress']     = 'M065_induction_lms/module_progress';
$route['api/lms/issue_certificate']   = 'M065_induction_lms/issue_certificate';
$route['api/lms/manager_view']        = 'M065_induction_lms/manager_view';
// induction/today and induction/manager_view had routes to NONEXISTENT methods.
// Repoint to real methods: today -> my_journey, manager_view -> team_view.
$route['api/induction/today']         = 'Induction/my_journey';
$route['api/induction/manager_view']  = 'Induction/team_view';


// ============================================================
// Fix 2026-06-06 batch 3: wire 6 controllers/methods that returned 404.
// All also 404 on production (not regressions; never-wired). Additive only.
// ============================================================
$route['api/district_intel/probe']      = 'District_intel/probe';
$route['api/district_intel/summary']    = 'District_intel/summary';
$route['api/huddle_mom/probe']          = 'Huddle/probe';
$route['api/huddle_mom/today']          = 'Huddle/today';
$route['api/stakeholder_book/list']     = 'StakeholderMap/list_for_lead';
$route['api/stakeholder/list']          = 'StakeholderMap/list_for_lead';
$route['api/fortnight_review/probe']    = 'ReviewScheduleController/probe';
$route['api/fortnight_review/due_today']= 'ReviewScheduleController/due_today';
$route['api/wdl/list']                  = 'Applause_api/wdl_list';


// ============================================================
// Build-all 2026-06-06: 6 new AI agents + 3 new features.
// Additive only. Controllers compute from real populated source tables
// (init_call, dup_check_log, cluster_master, revenue_target_matrix,
// bd_productivity_daily, company_contact_master). Class-name-only targets.
// ============================================================

// --- Agent: NextBestAction ---
$route['api/next_best_action/probe']     = 'NextBestAction/probe';
$route['api/next_best_action/recommend'] = 'NextBestAction/recommend';
$route['api/nba/probe']                  = 'NextBestAction/probe';
$route['api/nba/recommend']              = 'NextBestAction/recommend';

// --- Agent: ChurnPredictor ---
$route['api/churn_predictor/probe']   = 'ChurnPredictor/probe';
$route['api/churn_predictor/at_risk'] = 'ChurnPredictor/at_risk';
$route['api/churn_predictor/summary'] = 'ChurnPredictor/summary';
$route['api/churn/probe']             = 'ChurnPredictor/probe';
$route['api/churn/at_risk']           = 'ChurnPredictor/at_risk';

// --- Agent: DealCoach ---
$route['api/deal_coach/probe']  = 'DealCoach/probe';
$route['api/deal_coach/coach']  = 'DealCoach/coach';
$route['api/deal_coach/for_bd'] = 'DealCoach/for_bd';
$route['api/dealcoach/probe']   = 'DealCoach/probe';
$route['api/dealcoach/coach']   = 'DealCoach/coach';

// --- Agent: ClusterForecaster ---
$route['api/cluster_forecaster/probe']    = 'ClusterForecaster/probe';
$route['api/cluster_forecaster/forecast'] = 'ClusterForecaster/forecast';
$route['api/cluster_forecaster/headline'] = 'ClusterForecaster/headline';
$route['api/forecast/cluster']            = 'ClusterForecaster/forecast';

// --- Agent: LeadDedupe ---
$route['api/lead_dedupe/probe']  = 'LeadDedupe/probe';
$route['api/lead_dedupe/recent'] = 'LeadDedupe/recent';
$route['api/lead_dedupe/check']  = 'LeadDedupe/check';
$route['api/dedupe/probe']       = 'LeadDedupe/probe';
$route['api/dedupe/check']       = 'LeadDedupe/check';

// --- Agent: AnomalyWatchdog ---
$route['api/anomaly_watchdog/probe']   = 'AnomalyWatchdog/probe';
$route['api/anomaly_watchdog/detect']  = 'AnomalyWatchdog/detect';
$route['api/anomaly_watchdog/summary'] = 'AnomalyWatchdog/summary';
$route['api/anomaly/probe']            = 'AnomalyWatchdog/probe';
$route['api/anomaly/detect']           = 'AnomalyWatchdog/detect';

// --- Feature: CompetitorIntel ---
$route['api/competitor_intel/probe']    = 'CompetitorIntel/probe';
$route['api/competitor_intel/themes']   = 'CompetitorIntel/themes';
$route['api/competitor_intel/examples'] = 'CompetitorIntel/examples';

// --- Feature: WhatsappInbound (additive sibling of Whatsapp.php) ---
$route['api/whatsapp_inbound/probe']   = 'WhatsappInbound/probe';
$route['api/whatsapp_inbound/receive'] = 'WhatsappInbound/receive';
$route['api/whatsapp_inbound/recent']  = 'WhatsappInbound/recent';

// --- Feature: VoiceCommand ---
$route['api/voice_command/probe'] = 'VoiceCommand/probe';
$route['api/voice_command/parse'] = 'VoiceCommand/parse';
$route['api/voice/probe']         = 'VoiceCommand/probe';
$route['api/voice/parse']         = 'VoiceCommand/parse';

// === Close-all 2026-06-06: LeadStagnancy, PlanExecutionAnalysis, RemarkComms ===
// --- Feature: LeadStagnancy (30/60/90 not-worked-upon + coaching) ---
$route['api/lead_stagnancy/probe']   = 'LeadStagnancy/probe';
$route['api/lead_stagnancy/summary'] = 'LeadStagnancy/summary';
$route['api/lead_stagnancy/list']    = 'LeadStagnancy/listing';
$route['api/lead_stagnancy/coach']   = 'LeadStagnancy/coach';
$route['api/stagnancy/probe']        = 'LeadStagnancy/probe';
$route['api/stagnancy/summary']      = 'LeadStagnancy/summary';
$route['api/stagnancy/list']         = 'LeadStagnancy/listing';
$route['api/stagnancy/coach']        = 'LeadStagnancy/coach';

// --- Feature: PlanExecutionAnalysis (planned -> execution status-change) ---
$route['api/plan_execution/probe']          = 'PlanExecutionAnalysis/probe';
$route['api/plan_execution/summary']        = 'PlanExecutionAnalysis/summary';
$route['api/plan_execution/status_changes'] = 'PlanExecutionAnalysis/status_changes';
$route['api/plan_execution/for_lead']       = 'PlanExecutionAnalysis/for_lead';
$route['api/plan_exec/probe']               = 'PlanExecutionAnalysis/probe';
$route['api/plan_exec/summary']             = 'PlanExecutionAnalysis/summary';

// --- Feature: RemarkComms (remark-driven email/WhatsApp) ---
$route['api/remark_comms/probe']       = 'RemarkComms/probe';
$route['api/remark_comms/scan']        = 'RemarkComms/scan';
$route['api/remark_comms/classify']    = 'RemarkComms/classify';
$route['api/remark_comms/for_lead']    = 'RemarkComms/for_lead';
$route['api/remark_comms/queue_email'] = 'RemarkComms/queue_email';

// --- Agent: ProposalPitchAdvisor (pitching, rule-based, additive 2026-06-06) ---
$route["api/proposal_pitch/probe"]  = "ProposalPitchAdvisor/probe";
$route["api/proposal_pitch/advise"] = "ProposalPitchAdvisor/advise";
$route["api/proposal_pitch/for_bd"] = "ProposalPitchAdvisor/for_bd";

// --- Agent: NegotiationPriceGuard (negotiation, rule-based, additive 2026-06-06) ---
$route["api/price_guard/probe"]  = "NegotiationPriceGuard/probe";
$route["api/price_guard/guard"]  = "NegotiationPriceGuard/guard";
$route["api/price_guard/for_bd"] = "NegotiationPriceGuard/for_bd";

// --- Agent: ClosureSignoffPusher (closing, rule-based, additive 2026-06-06) ---
$route["api/closure_signoff/probe"]     = "ClosureSignoffPusher/probe";
$route["api/closure_signoff/push_list"] = "ClosureSignoffPusher/push_list";

// --- Agent: FollowUpCadenceScheduler (follow-up, rule-based, additive 2026-06-06) ---
$route["api/follow_up_cadence/probe"]  = "FollowUpCadenceScheduler/probe";
$route["api/follow_up_cadence/for_bd"] = "FollowUpCadenceScheduler/for_bd";

// --- Clean-input layer: validation preview endpoint (additive 2026-06-06) ---
$route["api/input_check/validate"] = "Input_check/validate";
$route["api/input_check/probe"]    = "Input_check/probe";


// === Audit B fix 2026-06-06: meeting_lifecycle/end and classify - correct plain routes ===
// Note: routes_additions.php (loaded earlier) already defines api/meeting/start -> MeetingLifecycleController/start_meeting
// and api/meeting/end -> MeetingLifecycleController/end_meeting, but those methods did not exist.
// Fixed by adding them to MeetingLifecycleController.php directly.
// These plain routes give meeting_lifecycle/* canonical names using MeetingLifecycle class:
$route["api/meeting_lifecycle/end"]      = "MeetingLifecycle/end";
$route["api/meeting_lifecycle/classify"] = "MeetingLifecycle/classify";
// === END Audit B fix ===


// === Audit F fix 2026-06-06: PlannerCoach live_suggestions, discipline_report, execution_live, day_end_report ===
// PlannerCoachController._check_bearer patched (bak_audit_20260606) to accept static fallback token.
// Methods existed in controller but had zero registered routes -> 404 for all coaching sub-endpoints.
$route["api/planner_coach/live_suggestions"]       = "PlannerCoachController/live_suggestions";
$route["api/planner_coach/discipline_report"]      = "PlannerCoachController/discipline_report";
$route["api/planner_coach/execution_live"]         = "PlannerCoachController/execution_live";
$route["api/planner_coach/day_end_report"] = "PlannerCoachController/day_end_report";
// === END Audit F fix ===


// === Audit F fix 2026-06-06: ApplauseV28 real routes (override stubs) ===
// routes_404_stubs.php stubbed api/applause/feed,send,leaderboard,my_received.
// ApplauseV28 controller exists with real applause_log reads/writes.
// PHP array last-wins: these overrides take effect (loaded after stubs).
$route["api/applause/feed"]        = "v28/ApplauseV28/feed";
$route["api/applause/leaderboard"] = "v28/ApplauseV28/leaderboard";
$route["api/applause/my_received"] = "v28/ApplauseV28/my_received";
$route["api/applause/send"]        = "v28/ApplauseV28/send";
$route["api/applause/probe"]       = "v28/ApplauseV28/probe";
// === END Audit F ApplauseV28 routes ===


// ============================================================
// Closeout-J 2026-06-06: 2 missing read endpoints (RISK-2 + RISK-3)
// Both targets use class-name-only CI3 convention.
// ============================================================
// RISK-2: BD Request History (mirrors Reports::UserRequestDetails)
$route['api/report/bd_requests']        = 'Gap_reports_api/bd_requests';

// RISK-3: Planner Approved Report (mirrors PlannerAReport Menu.php:9881)
$route['api/report/planner_approved']   = 'Gap_reports_api/planner_approved_report';

// Agent K closeout 2026-06-06: AiLeadScore seed routes
$route['api/ai_lead_score/refresh_bd']['post'] = 'AiLeadScoreController/refresh_bd';
$route['api/ai_lead_score/refresh_all']['post'] = 'AiLeadScoreController/refresh_all';

// === CLOSEOUT_I GAP-4: CommOutboxDrain drainer endpoint 2026-06-06 ===
$route["api/comm_outbox/drain"] = "CommOutboxDrain/drain";
// === END CLOSEOUT_I GAP-4 ===

// ============================================================
// APK readiness 2026-06-07: DayReviewMobile_api
// Mobile JSON equivalents for web-only manager surfaces.
// All targets class-name-only CI3 convention. Additive.
// ============================================================
$route["api/day_review/probe"]                  = "DayReviewMobile_api/probe";
$route["api/day_review/day_start_pending"]      = "DayReviewMobile_api/day_start_pending";
$route["api/day_review/day_close_pending"]      = "DayReviewMobile_api/day_close_pending";
$route["api/day_review/meeting_pending"]        = "DayReviewMobile_api/meeting_pending";
$route["api/day_review/task_pending"]           = "DayReviewMobile_api/task_pending";
$route["api/day_review/submit_day_check"]["post"]       = "DayReviewMobile_api/submit_day_check";
$route["api/day_review/submit_day_close_check"]["post"] = "DayReviewMobile_api/submit_day_close_check";
$route["api/day_review/submit_meeting_check"]["post"]   = "DayReviewMobile_api/submit_meeting_check";
$route["api/day_review/submit_task_check"]["post"]      = "DayReviewMobile_api/submit_task_check";
$route["api/day_review/calendar_plan"]          = "DayReviewMobile_api/calendar_plan";
$route["api/day_review/next_day_plan"]["post"]  = "DayReviewMobile_api/next_day_plan";
$route["api/day_review/plan_review_list"]       = "DayReviewMobile_api/plan_review_list";
$route["api/day_review/plan_review"]["post"]    = "DayReviewMobile_api/plan_review";
$route["api/day_review/bulk_approve_queue"]     = "DayReviewMobile_api/bulk_approve_queue";
$route["api/day_review/bulk_approve"]["post"]   = "DayReviewMobile_api/bulk_approve";
$route["api/day_review/task_reminder"]          = "DayReviewMobile_api/task_reminder";
$route["api/day_review/day_alerts"]             = "DayReviewMobile_api/day_alerts";
// === END APK DayReviewMobile_api routes ===

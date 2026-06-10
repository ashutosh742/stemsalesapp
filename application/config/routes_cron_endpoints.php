<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * routes_cron_endpoints.php
 * Generated 2026-05-26 - Agent B route fix
 *
 * Wires all cron-required endpoints that were returning 404 or 500.
 * Loaded LAST in routes.php so it wins over any earlier partial definitions.
 *
 * Controller-to-route mapping legend:
 *   CATEGORY A: Controller + method existed, just route was missing
 *   CATEGORY B: Method added to existing controller
 *   CATEGORY C: New stub controller created
 */

// ============================================================
// /api/planning/* - Category C: Planning_api (new stub)
// ============================================================
$route['api/planning/refresh_daily']          = 'Planning_api/refresh_daily';
$route['api/planning/leaderboard']            = 'Planning_api/leaderboard';
$route['api/planning/grade/today']            = 'Planning_api/grade_today';

// ============================================================
// /api/discipline/* - Category C: Discipline_api (new stub)
// ============================================================
$route['api/discipline/expense/sweep']        = 'Discipline_api/expense_sweep';
$route['api/discipline/expense/gate_check']   = 'Discipline_api/expense_gate_check';
$route['api/discipline/expense/cm_queue']     = 'Discipline_api/expense_cm_queue';
$route['api/discipline/expense/ao_queue']     = 'Discipline_api/expense_ao_queue';
$route['api/discipline/cancel/audit']         = 'Discipline_api/cancel_audit';
$route['api/discipline/advance/unsettled']    = 'Discipline_api/advance_unsettled';

// ============================================================
// /api/progression/* - Category C: Progression_api (new stub)
// ============================================================
$route['api/progression/stuck']               = 'Progression_api/stuck';
$route['api/progression/mom_blockers']        = 'Progression_api/mom_blockers';
$route['api/progression/top_movers']          = 'Progression_api/top_movers';
$route['api/progression/matrix']              = 'Progression_api/matrix';
$route['api/progression/refresh_daily']       = 'Progression_api/refresh_daily';
$route['api/progression/transitions']         = 'Progression_api/transitions';
$route['api/progression/closure_blockers']    = 'Progression_api/closure_blockers';
$route['api/progression/fbudget_mismatch']    = 'Progression_api/fbudget_mismatch';

// ============================================================
// /api/applause/* - Category C: Applause_api (new stub)
// ============================================================
$route['api/applause/today']                  = 'Applause_api/today';
$route['api/applause/queue']                  = 'Applause_api/queue';
$route['api/applause/mark']                   = 'Applause_api/mark';

// ============================================================
// /api/prospect/* - Category A+B: ProspectController (existed, methods added)
// ============================================================
$route['api/prospect/today_summary']          = 'ProspectController/today_summary';
$route['api/prospect/today_by_bd']            = 'ProspectController/today_by_bd';
$route['api/prospect/runs']                   = 'ProspectController/runs';
$route['api/prospect/seeded_for_date']        = 'ProspectController/seeded_for_date';
$route['api/prospect/seed_gap']               = 'ProspectController/seed_gap';
$route['api/prospect/suggest_area']           = 'ProspectController/suggest_area';
$route['api/prospect/accept_and_seed']        = 'ProspectController/accept_and_seed';
$route['api/prospect/refresh_all']            = 'ProspectController/refresh_all';

// ============================================================
// /api/funnel/* - Category C: Funnel_api (new stub)
// ============================================================
$route['api/funnel/weekly_rollup']            = 'Funnel_api/weekly_rollup';
$route['api/funnel/creation_paths']           = 'Funnel_api/creation_paths';
$route['api/funnel/pst_queue_aging']          = 'Funnel_api/pst_queue_aging';
$route['api/funnel/stuck_stages']             = 'Funnel_api/stuck_stages';
$route['api/funnel/closures']                 = 'Funnel_api/closures';
$route['api/funnel/path_conversion']          = 'Funnel_api/path_conversion';

// ============================================================
// /api/review/* - Category A: ReviewV2Controller + MonthlyLeadReviewController
// ============================================================
$route['api/review/pending_for_manager']      = 'Safe_review_api/pending_for_manager';
$route['api/review/refresh_skip_register']    = 'Safe_review_api/refresh_skip_register';
$route['api/review/skip_level_dashboard']     = 'Safe_review_api/skip_level_dashboard';
$route['api/review/monthly/generate']         = 'Safe_review_api/monthly_generate';
$route['api/review/monthly/list']             = 'MonthlyLeadReviewController/list_for_audience';
$route['api/review/monthly/record_pdf']       = 'MonthlyLeadReviewController/record_pdf';

// ============================================================
// /api/leaderboard/* - Category C: Leaderboard_api (new stub)
// ============================================================
$route['api/leaderboard/daily']               = 'Leaderboard_api/daily';
$route['api/leaderboard/weekly']              = 'Leaderboard_api/weekly';
$route['api/leaderboard/rp']                  = 'Leaderboard_api/rp';

// ============================================================
// /api/meeting_economics/* - Category C: Meeting_economics_api (new stub)
// ============================================================
$route['api/meeting_economics/scoreboard']    = 'Meeting_economics_api/scoreboard';
$route['api/meeting_economics/mix']           = 'Meeting_economics_api/mix';
$route['api/meeting_economics/capture']       = 'Meeting_economics_api/capture';

// ============================================================
// /api/feature_flags/* - Category C: Feature_flags_api (new stub)
// ============================================================
$route['api/feature_flags/list']              = 'Feature_flags_api/listing';

// ============================================================
// /api/users/* - Category C: Users_api (new stub)
// ============================================================
$route['api/users/active']                    = 'Users_api/active';
$route['api/users/bds_with_clusters']         = 'Users_api/bds_with_clusters';

// ============================================================
// /api/planner/* - Category C: Planner_api (new stub)
// ============================================================
$route['api/planner/yesterday_plans']         = 'Planner_api/yesterday_plans';
$route['api/planner/areas_for_date']          = 'Planner_api/areas_for_date';

// ============================================================
// EXPANDED ENDPOINTS (directive update)
// ============================================================

// /api/target/* - Category A+B: TargetController (burn_down alias added)
$route['api/target/headline']                 = 'Safe_target_api/headline';
$route['api/target/burn_down']                = 'Safe_target_api/burn_down';
$route['api/target/burndown']                 = 'Safe_target_api/burndown';
$route['api/target/critical_gaps']            = 'Safe_target_api/critical_gaps';
$route['api/target/war_points']               = 'Safe_target_api/war_points';

// /api/line_manager/* - Category A: LineManagerScorecardController
$route['api/line_manager/leaderboard']        = 'LineManagerScorecardController/leaderboard';

// /api/lead/signoff/* - Category A: StageSignoff_api
$route['api/lead/signoff/queue']              = 'StageSignoffController/queue';
$route['api/lead/signoff/bypass_log']         = 'StageSignoffController/bypass_log';

// /api/manager_incentive/* - Category A+B: ManagerIncentive_api
$route['api/manager_incentive/this_week']     = 'ManagerIncentiveController/this_week';

// /api/rm_upsell/* - Category A: RmUpsell
$route['api/rm_upsell/pipeline']              = 'RmUpsellController/pipeline';
$route['api/rm_upsell/scorecard']             = 'RmUpsellController/scorecard';
$route['api/rm_upsell/anchor_renewals_due']   = 'RmUpsellController/anchor_renewals_due';

// /api/cm_planner/* - Category A: CmPlanner
$route['api/cm_planner/missed_mandatory']     = 'CmPlannerController/missed_mandatory';

// /api/mom_v2/* - Category A: MomV2Controller
$route['api/mom_v2/approval_queue']           = 'MomV2Controller/approval_queue';

// /api/csr/* - Category A: CsrController
$route['api/csr/quota']                       = 'CsrController/quota';

// /api/upstream_hygiene/* - Category A: UpstreamHygiene (stub already had methods)
$route['api/upstream_hygiene/stagnant_open_45']   = 'UpstreamHygiene/stagnant_open_45';
$route['api/upstream_hygiene/stagnant_reachout_30'] = 'UpstreamHygiene/stagnant_reachout_30';
$route['api/upstream_hygiene/wallet_triggers']    = 'UpstreamHygiene/wallet_triggers';

// /api/proposal/sla/* - Category A+B: Proposal_sla (backlog added)
$route['api/proposal/sla/backlog']            = 'Safe_proposal_api/backlog';

// /api/coach/knowledge/* - Category A: Coach controller methods exist
$route['api/coach/knowledge/whats_new']           = 'Coach/knowledge_whats_new';
$route['api/coach/knowledge/candidate_faqs']      = 'Coach/knowledge_candidate_faqs';
$route['api/coach/knowledge/ack_overdue']         = 'Coach/knowledge_ack_overdue';
$route['api/coach/knowledge/distribution_gaps']   = 'Coach/knowledge_distribution_gaps';
$route['api/coach/knowledge/expiring']            = 'Coach/knowledge_expiring';
$route['api/coach/knowledge/unanswered_top']      = 'Coach/knowledge_unanswered_top';

// /api/meeting_prep/* - Category A: MeetingPrep
$route['api/meeting_prep/trigger']            = 'MeetingPrep/trigger';
$route['api/meeting_prep/runs']               = 'MeetingPrep/runs';

// /api/day_ceremony/* - Category A: DayCeremonyController
$route['api/day_ceremony/rollup']             = 'Day_ceremony_api/rollup';

// /api/activity/* - Category C: Activity_api (new stub)
$route['api/activity/events_for_day']         = 'Activity_api/events_for_day';

// /api/mom/* - Category C: Mom_api (new stub)
$route['api/mom/written_for_day']             = 'Mom_api/written_for_day';


// /api/meeting_prep/checklist - MeetingPrep controller
$route['api/meeting_prep/checklist'] = 'MeetingPrep/checklist';

// === FIX: csr_prospect routes - correct CI3 controller casing ===
// routes_csr_prospect.php uses all-lowercase which CI3 can't resolve on Linux
// Override here (last wins) with proper ucfirst controller name
$route['api/csr_prospect/today_summary']     = 'CorporateCsrProspectController/today_summary';
$route['api/csr_prospect/today_for_bd']      = 'CorporateCsrProspectController/today_for_bd';
$route['api/csr_prospect/refresh_for_bd']    = 'CorporateCsrProspectController/refresh_for_bd';
$route['api/csr_prospect/accept_and_seed']   = 'CorporateCsrProspectController/accept_and_seed';
$route['api/csr_prospect/link_init_call']    = 'CorporateCsrProspectController/link_init_call';
$route['api/csr_prospect/dismiss']           = 'CorporateCsrProspectController/dismiss';
$route['api/csr_prospect/sync_csr_gov']      = 'CorporateCsrProspectController/sync_csr_gov';
$route['api/csr_prospect/sync_apollo']       = 'CorporateCsrProspectController/sync_apollo';
$route['api/csr_prospect/influencers']       = 'CorporateCsrProspectController/influencers';

// === FIX: target cascade/discipline routes → Safe_target_api (stubs, no 400/500) ===
// routes_additions.php points to TargetController which lacks these methods
// Override here (last wins) with Safe_target_api which has safe stubs
$route['api/target/cascade/refresh']         = 'Safe_target_api/cascade_refresh';
$route['api/target/cascade/set']             = 'Safe_target_api/cascade_set';
$route['api/target/weekly_checkin']          = 'Safe_target_api/weekly_checkin';
$route['api/target/discipline_score']        = 'Safe_target_api/discipline_score';

// === FIX: mom_v2 routes - mega_26may uses 'momv2' lowercase, override with correct controller ===
$route['api/mom_v2/probe']             = 'MomV2Controller/probe';
$route['api/mom_v2/agenda_gate']       = 'MomV2Controller/agenda_gate';
$route['api/mom_v2/voice_coverage']    = 'MomV2Controller/voice_coverage';
$route['api/mom_v2/save_draft']        = 'MomV2Controller/save_draft';
$route['api/mom_v2/submit']            = 'MomV2Controller/submit';
$route['api/mom_v2/approve']           = 'MomV2Controller/approve';
$route['api/mom_v2/reject']            = 'MomV2Controller/reject';
$route['api/mom_v2/approval_queue']    = 'MomV2Controller/approval_queue';

// === FIX: StageSignoff - approve/reject route names vs method names ===
// Controller has decide() not approve()/reject() - route to existing decide() which takes action
$route['api/lead/signoff/approve']     = 'StageSignoffController/decide';
$route['api/lead/signoff/reject']      = 'StageSignoffController/decide';
$route['api/lead/signoff/request']     = 'StageSignoffController/request_signoff';

// === FIX: CmPlanner - my_day→today, joint_meetings→joint_meetings_today ===
$route['api/cm_planner/my_day']            = 'CmPlannerController/today';
$route['api/cm_planner/joint_meetings']    = 'CmPlannerController/joint_meetings_today';

// === FIX: LineManager scorecard - needs uid param, add safe wrapper ===
// Returns 400 on missing uid - use Safe_line_manager fallback (will add)
$route['api/line_manager/scorecard']   = 'LineManagerScorecardController/scorecard';

// === 2026-05-26 wire-up additions: dashboard methods + review_api ===
$route['api/planner/events_for_day']          = 'Planner_api/events_for_day';
$route['api/planner/tasks_assigned']          = 'Planner_api/tasks_assigned';
$route['api/planner/calendar']                = 'Planner_api/calendar';
$route['api/planner/task_completion']         = 'Planner_api/task_completion';
$route['api/review/pending_for_manager']      = 'Review_api/pending_for_manager';
$route['api/review/skip_level_dashboard']     = 'Review_api/skip_level_dashboard';
$route['api/review/monthly/list']             = 'Review_api/monthly_list';
$route['api/review/monthly/generate']         = 'Review_api/monthly_generate';
// === end 2026-05-26 wire-up additions ===

// === STREAM A 2026-05-26: add missing controller routes ===

// /api/induction/* - InductionController (class InductionController, no 'current' method found)
// NOTE: 'current' method does not exist in Induction.php - routing all known methods + wildcard
$route['api/induction/probe']               = 'Induction/probe';
$route['api/induction/enroll']              = 'Induction/enroll';
$route['api/induction/my_journey']          = 'Induction/my_journey';
$route['api/induction/my_unacked_docs']     = 'Induction/my_unacked_docs';
$route['api/induction/start_step']          = 'Induction/start_step';
$route['api/induction/complete_step']       = 'Induction/complete_step';
$route['api/induction/share_doc']           = 'Induction/share_doc';
$route['api/induction/ack_doc']             = 'Induction/ack_doc';
$route['api/induction/team_view']           = 'Induction/team_view';
$route['api/induction/stalled']             = 'Induction/stalled';
$route['api/induction/unread_docs']         = 'Induction/unread_docs';
$route['api/induction/failed_scores']       = 'Induction/failed_scores';
$route['api/induction/leaderboard']         = 'Induction/leaderboard';
$route['api/induction/optin']               = 'Induction/optin';
$route['api/induction/(:any)']              = 'Induction/$1';

// /api/greetings/* - GreetingsController (no 'today' method - routing all known methods + wildcard)
$route['api/greetings/probe']                   = 'Greetings/probe';
$route['api/greetings/queue']                   = 'Greetings/queue';
$route['api/greetings/queue_run']               = 'Greetings/queue_run';
$route['api/greetings/approve_and_send']        = 'Greetings/approve_and_send';
$route['api/greetings/skip']                    = 'Greetings/skip';
$route['api/greetings/edit_draft']              = 'Greetings/edit_draft';
$route['api/greetings/log']                     = 'Greetings/log';
$route['api/greetings/stakeholder_dob_upsert']  = 'Greetings/stakeholder_dob_upsert';
$route['api/greetings/stakeholder_dob_list']    = 'Greetings/stakeholder_dob_list';
$route['api/greetings/stakeholder_dob_coverage'] = 'Greetings/stakeholder_dob_coverage';
$route['api/greetings/(:any)']                  = 'Greetings/$1';

// /api/handover_v2/* - Handover_v2_api (method is 'listing' not 'list')
$route['api/handover_v2/save_draft']            = 'HandoverV2/save_draft';
$route['api/handover_v2/submit']                = 'HandoverV2/submit';
$route['api/handover_v2/list']                  = 'HandoverV2/listing';
$route['api/handover_v2/listing']               = 'HandoverV2/listing';
$route['api/handover_v2/cm_queue']              = 'HandoverV2/cm_queue';
$route['api/handover_v2/detail']                = 'HandoverV2/detail';
$route['api/handover_v2/approve']               = 'HandoverV2/approve';
$route['api/handover_v2/reject']                = 'HandoverV2/reject';
$route['api/handover_v2/mark_installation_started'] = 'HandoverV2/mark_installation_started';
$route['api/handover_v2/(:any)']                = 'HandoverV2/$1';

// /api/bd_request/* - BDRequest_api (separate controller BdRequest.php)
$route['api/bd_request/create']                 = 'BdRequest/create';
$route['api/bd_request/inbox']                  = 'BdRequest/inbox';
$route['api/bd_request/my_requests']            = 'BdRequest/my_requests';
$route['api/bd_request/detail']                 = 'BdRequest/detail';
$route['api/bd_request/approve']                = 'BdRequest/approve';
$route['api/bd_request/reject']                 = 'BdRequest/reject';
$route['api/bd_request/logs']                   = 'BdRequest/logs';
$route['api/bd_request/(:any)']                 = 'BdRequest/$1';

// /api/mom_v2/mandatory - MomV2Controller has no 'mandatory' method - NOT added (see Issues)
// Additional MomV2Controller methods not yet routed:
$route['api/mom_v2/request_edit']               = 'MomV2Controller/request_edit';

// /api/day_ceremony/* - Day_ceremony_api: 'today_status' was missing
$route['api/day_ceremony/today_status']         = 'Day_ceremony_api/today_status';
$route['api/day_ceremony/(:any)']               = 'Day_ceremony_api/$1';

// NewLead.php does not exist on this server - NOT added (see Issues)

// === END STREAM A 2026-05-26 ===

// === STREAM A: class aliases so CI3 can instantiate mismatched controller classes ===
// CI3 does: load file, then class_exists(ucfirst($route_segment)) - the segment must match a class name.
// Greetings.php has class GreetingsController - alias it so 'Greetings' resolves.
if (!class_exists('Greetings', FALSE) && class_exists('GreetingsController', FALSE)) {
    class_alias('GreetingsController', 'Greetings');
}
// === END STREAM A class aliases ===

// === STREAM A: explicit overrides for routes where mega_26may set lowercase targets ===
// greetings/today was greetings/today (lowercase) in mega_26may - override with proper case
$route['api/greetings/today']               = 'Greetings/today';
$route['api/greetings/dismiss']             = 'Greetings/dismiss';
$route['api/greetings/draft']               = 'Greetings/draft';
$route['api/greetings/send']                = 'Greetings/send';

// mom_v2/mandatory: MomV2Controller has no 'mandatory' method - route added as requested,
// will 404 at method level (correct controller, missing method)
$route['api/mom_v2/mandatory']              = 'MomV2Controller/mandatory';

// induction/current: InductionController has no 'current' method - route added as requested,
// also override mega_26may's lowercase induction/today
$route['api/induction/current']             = 'Induction/current';
$route['api/induction/today']               = 'Induction/today';
$route['api/induction/steps']               = 'Induction/steps';
$route['api/induction/mark_done']           = 'Induction/mark_done';
$route['api/induction/manager_view']        = 'Induction/manager_view';
// === END STREAM A explicit overrides ===

// ============================================================
// STREAM D: Wire Stub Controllers - new data endpoints
// Added 2026-05-26 - these are the real-DB methods added to
// stub controllers. Probe routes already exist in
// routes_probe_canonical.php and are NOT repeated here.
// ============================================================

// M025 MeetingLifecycle: whats_active reads tblcallevents last 7 days
$route['api/meeting_lifecycle/whats_active']   = 'MeetingLifecycle/whats_active';
$route['api/meeting_lifecycle/state']          = 'MeetingLifecycle/state';
$route['api/meeting_lifecycle/start']          = 'MeetingLifecycle/start';

// M027 CommOrchestrator: inbox reads comm_draft_queue; outbox_log reads comm_send_log
$route['api/comm/inbox']                       = 'CommOrchestratorApi/inbox';
$route['api/comm/outbox_log']                  = 'CommOrchestratorApi/outbox_log';
$route['api/comm/draft_list']                  = 'CommOrchestratorApi/draft_list';
$route['api/comm/process_pending']             = 'CommOrchestratorApi/process_pending';

// M036 Coach: knowledge endpoints with direct DB wire
// (whats_new and candidate_faqs already routed above - override here
//  to point directly to Coach controller not agents)
$route['api/coach/knowledge/whats_new']        = 'Coach/knowledge_whats_new';
$route['api/coach/knowledge/candidate_faqs']   = 'Coach/knowledge_candidate_faqs';
$route['api/coach/knowledge/list']             = 'Coach/knowledge_list';
$route['api/coach/faq/search']                 = 'Coach/faq_search';
$route['api/coach/faq/candidates']             = 'Coach/faq_candidates';

// M039 EmailToTask: inbox + stats read inbound_email_v2
$route['api/email_to_task/inbox']              = 'EmailToTask/inbox';
$route['api/email_to_task/stats']              = 'EmailToTask/stats';
$route['api/email_to_task/convert']            = 'EmailToTask/convert';

// M041 CorporateCsrProspect: candidates reads csr_corporate_master
$route['api/csr_prospect/candidates']          = 'CorporateCsrProspectController/candidates';
$route['api/csr_prospect/corporate']           = 'CorporateCsrProspectController/corporate';

// M042 MeetingPrep: runs + checklist read tblcallevents fallback
$route['api/meeting_prep/runs']                = 'MeetingPrep/runs';
$route['api/meeting_prep/checklist']           = 'MeetingPrep/checklist';

// M049 RemarkCoherence: flagged + run_log read remark_coherence_score/run_log
$route['api/remark_coherence/flagged']         = 'RemarkCoherenceController/flagged';
$route['api/remark_coherence/run_log']         = 'RemarkCoherenceController/run_log';
$route['api/remark_coherence/yesterday_summary'] = 'RemarkCoherenceController/yesterday_summary';
$route['api/remark_coherence/run_batch']       = 'RemarkCoherenceController/run_batch';
$route['api/pushback/inbox']                   = 'RemarkCoherenceController/inbox';
$route['api/pushback/get']                     = 'RemarkCoherenceController/get_pushback';
$route['api/pushback/respond']                 = 'RemarkCoherenceController/respond';
$route['api/pushback/override']                = 'RemarkCoherenceController/override';
$route['api/pushback/cm_queue']                = 'RemarkCoherenceController/cm_queue';

// === END STREAM D ROUTES ===

// ============================================================
// === STREAM E ROUTES - New controllers built 2026-05-26 ===
// ============================================================

// M017_4 day_shape: Planner_api extended with day_shape_today method
$route['api/planner/day_shape/today']           = 'Planner_api/day_shape_today';

// M024 funnel_hygiene: new Funnel_hygiene controller
$route['api/funnel_hygiene/dm_verify_queue']    = 'Funnel_hygiene/dm_verify_queue';
$route['api/funnel_hygiene/probe']              = 'Funnel_hygiene/probe';

// M026 lead_query: new Lead_query controller
$route['api/lead_query/checklist']              = 'Lead_query/checklist';
$route['api/lead_query/probe']                  = 'Lead_query/probe';

// M035 huddle: new Huddle controller
$route['api/huddle/today']                      = 'Huddle/today';
$route['api/huddle/probe']                      = 'Huddle/probe';

// M037 mom_v2/mandatory: MomV2Controller extended with mandatory method
$route['api/mom_v2/mandatory']                  = 'MomV2Controller/mandatory';

// M044 newlead: new NewLead controller
$route['api/newlead/create']                    = 'NewLead/create';
$route['api/newlead/probe']                     = 'NewLead/probe';

// M047 calendar/execution_gap: new Calendar controller
$route['api/calendar/execution_gap']            = 'Calendar/execution_gap';
$route['api/calendar/probe']                    = 'Calendar/probe';

// M050 relationship_map: new Relationship_map controller
$route['api/relationship_map/probe']            = 'Relationship_map/probe';
$route['api/relationship_map/for_lead']         = 'Relationship_map/for_lead';

// M054 generic probe: new M054 controller
$route['api/m054/probe']                        = 'M054/probe';

// M057 district_intel: new District_intel controller
$route['api/district_intel/probe']              = 'District_intel/probe';
$route['api/district_intel/summary']            = 'District_intel/summary';

// === END STREAM E ROUTES ===

// STREAM D ROUTE FIX: After class-name corrections
// MeetingLifecycle class is now MeetingLifecycleController (file: MeetingLifecycleController.php)
$route['api/meeting_lifecycle/whats_active']   = 'MeetingLifecycleController/whats_active';
// CommOrchestratorApi class is now CommOrchestratorController (file: CommOrchestratorController.php)
$route['api/comm/inbox']                       = 'CommOrchestratorController/inbox';
$route['api/comm/outbox_log']                  = 'CommOrchestratorController/outbox_log';
$route['api/comm/draft_list']                  = 'CommOrchestratorController/draft_list';
$route['api/comm/process_pending']             = 'CommOrchestratorController/process_pending';
// RemarkCoherenceController class is now RemarkCoherence (file: RemarkCoherence.php)
$route['api/remark_coherence/flagged']         = 'RemarkCoherence/flagged';
$route['api/remark_coherence/run_log']         = 'RemarkCoherence/run_log';
$route['api/remark_coherence/yesterday_summary'] = 'RemarkCoherence/yesterday_summary';
$route['api/remark_coherence/run_batch']       = 'RemarkCoherence/run_batch';
$route['api/pushback/inbox']                   = 'RemarkCoherence/inbox';
$route['api/pushback/get']                     = 'RemarkCoherence/get_pushback';
$route['api/pushback/respond']                 = 'RemarkCoherence/respond';
$route['api/pushback/override']                = 'RemarkCoherence/override';
$route['api/pushback/cm_queue']                = 'RemarkCoherence/cm_queue';
// === END STREAM D ROUTE FIX ===
$route['api/pilot/uids'] = 'Pilot_uids/index';

/* === M073 Concur-class Advance Settlement routes (added auto) === */
$route['api/discipline/policy/categories']       = 'M073Expense/policy_categories';
$route['api/discipline/advance/settle_v2']       = 'M073Expense/settle_v2';
$route['api/discipline/receipt/ocr_scan']        = 'M073Expense/ocr_scan';
$route['api/discipline/accounting/sync_pending'] = 'M073Expense/sync_pending';
$route['api/discipline/accounting/sync_retry']   = 'M073Expense/sync_retry';
/* === END M073 routes === */


// === Migration 081 calendar routes (added 30 May 2026) ===
$route['api/calendar/month']       = 'Calendar/month';
$route['api/calendar/day']         = 'Calendar/day';
$route['api/calendar/range']       = 'Calendar/range';
$route['api/calendar/team_month']  = 'Calendar/team_month';
$route['api/calendar/team_day']    = 'Calendar/team_day';
// === END Migration 081 ===

// F11: MOM check endpoint (v290 helpers-only deploy)
$route['api/planner/v2/create_mom_check'] = 'Planner_api/create_mom_check';

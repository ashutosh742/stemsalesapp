<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/*
| -------------------------------------------------------------------------
| URI ROUTING
| -------------------------------------------------------------------------
| This file lets you re-map URI requests to specific controller functions.
|
| Typically there is a one-to-one relationship between a URL string
| and its corresponding controller class/method. The segments in a
| URL normally follow this pattern:
|
|	example.com/class/method/id/
|
| In some instances, however, you may want to remap this relationship
| so that a different class/function is called than the one
| corresponding to the URL.
|
| Please see the user guide for complete details:
|
|	https://codeigniter.com/user_guide/general/routing.html
|
| -------------------------------------------------------------------------
| RESERVED ROUTES
| -------------------------------------------------------------------------
|
| There are three reserved routes:
|
|	$route['default_controller'] = 'welcome';

|
| This route indicates which controller class should be loaded if the
| URI contains no data. In the above example, the "welcome" class
| would be loaded.
|
|	$route['404_override'] = 'errors/page_missing';
|
| This route will tell the Router which controller/method to use if those
| provided in the URL cannot be matched to a valid route.
|
|	$route['translate_uri_dashes'] = FALSE;
|
| This is not exactly a route, but allows you to automatically route
| controller and method names that contain dashes. '-' isn't a valid
| class or method name character, so it requires translation.
| When you set this option to TRUE, it will replace ALL dashes in the
| controller and method URI segments.
|
| Examples:	my-controller/index	-> my_controller/index
|		my-controller/my-method	-> my_controller/my_method
*/
$route['default_controller'] = 'Menu/main';

// m079.1 hotpatch routes
$route['api/dashboard/header_counts']        = 'DashboardHeader/header_counts';
$route['api/dashboard/header_counts/probe']  = 'DashboardHeader/probe';
$route['api/anaya/briefing']                 = 'AnayaBriefing/briefing';
$route['api/anaya/leads_to_push']            = 'AnayaBriefing/leads_to_push';
$route['api/anaya/probe']                    = 'AnayaBriefing/probe';

$route['default_controller'] = 'index';
$route['404_override'] = '';
$route['translate_uri_dashes'] = FALSE;
$route['xyz-test'] = 'Menu/home';
// $route['404_override'] = 'errorpage/show404';


// AI Query Routes
$route['ai_query'] = 'ai_query/index';
$route['ai_query/process_query'] = 'ai_query/process_query';
$route['ai_query/smart_search'] = 'ai_query/smart_search';
$route['ai_query/execute_predefined'] = 'ai_query/execute_predefined';
$route['ai_query/view_table/(:any)'] = 'ai_query/view_table/$1';
$route['ai_query/view_table'] = 'ai_query/view_table';
$route['ai_query/clear_recent'] = 'ai_query/clear_recent';



$route['chat'] = 'chat/index';
$route['chat/send_message'] = 'chat/send_message';
$route['chat/get_chat_history'] = 'chat/get_chat_history';



// API 
$route['api/login'] = 'api/auth/login';
// Review v2 probe - hardcoded (agent2_review_session)
$route['api/review/probe'] = 'Review_api/probe';
$route['api/review/status'] = 'Review_api/status';



// === Review v2 routes (Migration 020, agent2_review_session build) - LOADED FIRST ===
$review_v2_routes = __DIR__ . '/routes_review_v2.php';
if (file_exists($review_v2_routes)) { include($review_v2_routes); }
// === END Review v2 routes ===




// === Blitz 30 May Agent E PRIORITY: wins over mega_26may, agent6, mobile_pilot, cron ===
$blitz_e_priority = __DIR__ . '/routes_blitz_30may_e.php';
if (file_exists($blitz_e_priority)) { try { include($blitz_e_priority); } catch (Throwable $ex_ep) { log_message('error', 'blitz_e_priority: ' . $ex_ep->getMessage()); } }
// === END Blitz 30 May Agent E PRIORITY ===

// === MEGA DEPLOY 26 MAY 2026 - load additional route files ===
$mega_routes = __DIR__ . '/routes_mega_26may.php';
if (file_exists($mega_routes)) { include($mega_routes); }
$csr_routes = __DIR__ . '/routes_csr_prospect.php';
if (file_exists($csr_routes)) { include($csr_routes); }
// === END MEGA DEPLOY 26 MAY 2026 ===



// === R4 routes_additions wiring ===
$r4_routes = __DIR__ . '/routes_additions.php';
if (file_exists($r4_routes)) { include($r4_routes); }

// === Blitz 30 May Agent D real routes ===
$blitz_d_routes = __DIR__ . "/routes_blitz_30may_d.php";
if (file_exists($blitz_d_routes)) { try { include($blitz_d_routes); } catch (Throwable $_ex) { log_message("error", "blitz_d: " . $_ex->getMessage()); } }
// === END Blitz 30 May Agent D ===

// === API Keys admin routes (credential wiring 2026-05-27) ===
$apikeys_routes = __DIR__ . '/routes_api_keys.php';
if (file_exists($apikeys_routes)) { include($apikeys_routes); }

// === Canonical probe routes (loaded last to override conflicts) 2026-05-26 ===
$probe_routes = __DIR__ . '/routes_probe_canonical.php';
if (file_exists($probe_routes)) { include($probe_routes); }

// === CRON ENDPOINT ROUTES - loaded after probe_canonical (Agent B, 2026-05-26) ===
// Card 8 day_ceremony simple routes (must be before cron catchall)
$route['api/day_ceremony/start_simple']['post'] = 'MobileExtrasController/day_ceremony_start_simple';
$route['api/day_ceremony/end_simple']['post']   = 'MobileExtrasController/day_ceremony_end_simple';
// migration 075 day discipline status
$route['api/day_ceremony/status'] = 'DayCeremonyStatusController/status';

// === 404 stub early-loaded (must precede cron_endpoints to beat wildcards) ===
$stub_404_early = __DIR__ . "/routes_404_stubs_early.php";
if (file_exists($stub_404_early)) { include($stub_404_early); }
// === END 404 stub early-loaded ===

$cron_routes = __DIR__ . '/routes_cron_endpoints.php';
if (file_exists($cron_routes)) { include($cron_routes); }
// === END CRON ENDPOINT ROUTES ===


// === School Logo Routes (Agent C) ===
$logo_routes = __DIR__ . '/routes_school_logo.php';
if (file_exists($logo_routes)) { include($logo_routes); }
// === End School Logo Routes ===

// === Agent 3 Comm Inbox + Stakeholder routes (27 May 2026) ===
$agent3_routes = __DIR__ . '/routes_agent3_comm.php';
if (file_exists($agent3_routes)) { include($agent3_routes); }
// === END Agent 3 routes ===

// === Agent 6 Leave + Export + BdRequest + Handover routes (loaded last) ===
$agent6_routes = __DIR__ . '/routes_agent6.php'; if (file_exists($agent6_routes)) { include($agent6_routes); }
// === END Agent 6 routes ===

// === Agent 5 PST Queue + Remark Coherence routes (Migration 049, 27 May 2026) ===
$pst_remark_routes = __DIR__ . '/routes_pst_remark.php';
if (file_exists($pst_remark_routes)) { include($pst_remark_routes); }
// === END Agent 5 routes ===

// === M058-M062 routes added 27 May 2026 ===
$route['api/lead_intelligence/probe']['get'] = 'LeadIntelligenceController/probe';
$route['api/lead_intelligence/score']['get'] = 'LeadIntelligenceController/score';
$route['api/lead_intelligence/cohort']['get'] = 'LeadIntelligenceController/cohort';
$route['api/lead_intelligence/breach_queue']['get'] = 'LeadIntelligenceController/breach_queue';
$route['api/lead_intelligence/breach_override']['post'] = 'LeadIntelligenceController/breach_override';
$route['api/lead_intelligence/range_estimator']['get'] = 'LeadIntelligenceController/range_estimator';
$route['api/lead_intelligence/range_update']['post'] = 'LeadIntelligenceController/range_update';

$route['api/target/scenarios']['get'] = 'TargetScenarios/scenarios';
$route['api/target/scenario_burndown']['get'] = 'TargetScenarios/scenario_burndown';

$route['api/field_resilience/probe']['get'] = 'FieldResilienceController/probe';
$route['api/field_resilience/queue']['post'] = 'FieldResilienceController/queue';
$route['api/field_resilience/replay']['post'] = 'FieldResilienceController/replay';
$route['api/field_resilience/fcm_register']['post'] = 'FieldResilienceController/fcm_register';
$route['api/field_resilience/call_log']['post'] = 'FieldResilienceController/call_log';
$route['api/field_resilience/ocr_save']['post'] = 'FieldResilienceController/ocr_save';
$route['api/field_resilience/calendar_sync']['post'] = 'FieldResilienceController/calendar_sync';
$route['api/field_resilience/pending_sync']['get'] = 'FieldResilienceController/pending_sync';

$route['api/slack/probe']['get'] = 'SlackOutboundController/probe';
$route['api/slack/config_list']['get'] = 'SlackOutboundController/config_list';
$route['api/slack/config_add']['post'] = 'SlackOutboundController/config_add';
$route['api/slack/config_toggle']['post'] = 'SlackOutboundController/config_toggle';
$route['api/slack/test_send']['post'] = 'SlackOutboundController/test_send';
$route['api/slack/outbound_log']['get'] = 'SlackOutboundController/outbound_log';

$route['api/csr_closure/probe']['get'] = 'CsrClosureController/probe';
$route['api/csr_closure/lookup']['get'] = 'CsrClosureController/lookup';
$route['api/csr_closure/top_spenders']['get'] = 'CsrClosureController/top_spenders';
$route['api/csr_closure/dmft_calendar']['get'] = 'CsrClosureController/dmft_calendar';
$route['api/csr_closure/import_csr']['post'] = 'CsrClosureController/import_csr';
$route['api/csr_closure/import_dmft']['post'] = 'CsrClosureController/import_dmft';
$route['api/csr_closure/apollo_status']['get'] = 'CsrClosureController/apollo_status';
// === end M058-M062 ===

// Migration 058 + 059 routes (added by deploy_2_controllers.py)
$route['api/partner_master/types']    = 'partner_master_api/types';
$route['api/path_c/eligibility']      = 'path_c_api/eligibility';

// === Mobile pilot endpoints (27 May 2026) ===
$mobile_pilot_routes = __DIR__ . '/routes_mobile_pilot.php';
if (file_exists($mobile_pilot_routes)) { include($mobile_pilot_routes); }
// === END Mobile pilot endpoints ===


// === Migration 058 + 020.1 routes (added 27 May 2026) ===
$m058_routes = __DIR__ . '/routes_058_accountability.php';
if (file_exists($m058_routes)) { include($m058_routes); }
// === END Migration 058 + 020.1 routes ===

/* M069 Route Brain */
$route['api/route_brain/probe']['get'] = 'RouteBrain/probe';
$route['api/route_brain/preview']['get'] = 'RouteBrain/preview';
$route['api/route_brain/submit_plan']['post'] = 'RouteBrain/submit_plan';
$route['api/route_brain/meeting_end']['post'] = 'RouteBrain/meeting_end';
$route['api/route_brain/walkin_act']['post'] = 'RouteBrain/walkin_act';
$route['api/route_brain/day_close']['post'] = 'RouteBrain/day_close';
$route['api/route_brain/efficiency']['get'] = 'RouteBrain/efficiency';
$route['api/route_brain/opportunity_vs_execution']['get'] = 'RouteBrain/opportunity_vs_execution';
$route['api/route_brain/preview_named']['get'] = 'RouteBrain/preview_named';

// M071 company_geotag routes

$route['api/company_geotag/probe']   = 'CompanyGeoTag/probe';
$route['api/company_geotag/log']     = 'CompanyGeoTag/log';
$route['api/company_geotag/anchor']  = 'CompanyGeoTag/anchor';

// === GAP 9 Inside Sales routes ===
$route['api/inside_sales/probe']['get']            = 'InsideSalesController/probe';
$route['api/inside_sales/my_queue']['get']         = 'InsideSalesController/my_queue';
$route['api/inside_sales/email_task_check']['get'] = 'InsideSalesController/email_task_check';
$route['api/inside_sales/log_email']['post']       = 'InsideSalesController/log_email';
// === END GAP 9 routes ===

// === M075 Funnel Report + Graph Analysis routes (Card 3 + Card 18) ===
$route['api/funnel_report/probe']['get']               = 'FunnelReportController/probe';
$route['api/funnel_report/stuck_status']['get']        = 'FunnelReportController/stuck_status';
$route['api/funnel_report/companies_without_dm']['get']= 'FunnelReportController/companies_without_dm';
$route['api/funnel_report/closing_timeline']['get']    = 'FunnelReportController/closing_timeline';
$route['api/funnel_report/funnel_transfer']['get']     = 'FunnelReportController/funnel_transfer';
$route['api/funnel_report/created_between']['get']     = 'FunnelReportController/created_between';
$route['api/funnel_report/deleted_between']['get']     = 'FunnelReportController/deleted_between';
$route['api/funnel_report/conversion_summary']['get']  = 'FunnelReportController/conversion_summary';
$route['api/funnel_report/pending_moms']['get']        = 'FunnelReportController/pending_moms';
$route['api/funnel_report/line_mgr_rp_pending']['get'] = 'FunnelReportController/line_mgr_rp_pending';
$route['api/graph_analysis/status_distribution']['get']   = 'FunnelReportController/status_distribution';
$route['api/graph_analysis/day_of_week_meetings']['get']  = 'FunnelReportController/day_of_week_meetings';
$route['api/graph_analysis/planner_adherence']['get']     = 'FunnelReportController/planner_adherence';
// === END M075 routes ===



// === GAP 19 Travel Cluster routes ===
$route['api/travel_cluster/probe']['get']               = 'TravelClusterController/probe';
$route['api/travel_cluster/my_cluster']['get']          = 'TravelClusterController/my_cluster';
$route['api/travel_cluster/details']['get']             = 'TravelClusterController/details';
$route['api/travel_cluster/edit_requests']['get']       = 'TravelClusterController/edit_requests';
$route['api/travel_cluster/submit_edit_request']['post'] = 'TravelClusterController/submit_edit_request';
$route['api/travel_cluster/approve_edit_request']['post'] = 'TravelClusterController/approve_edit_request';
// === END GAP 19 ===

// === GAP 21 Upsell Client routes ===
$route['api/upsell/probe']['get']               = 'UpsellClientController/probe';
$route['api/upsell/handover_approval']['get']   = 'UpsellClientController/handover_approval';
$route['api/upsell/artwork_pending']['get']     = 'UpsellClientController/artwork_pending';
$route['api/upsell/artwork_done']['get']        = 'UpsellClientController/artwork_done';
$route['api/upsell/total_summary']['get']       = 'UpsellClientController/total_summary';
// === END GAP 21 ===
// === Mobile Extras Cards 4,6,8,10,12,14,20 ===
$mobile_extras = __DIR__ . '/routes_mobile_extras.php';
if (file_exists($mobile_extras)) { include($mobile_extras); }
// === END Mobile Extras ===

// === GAP 11 Star Rating routes ===
$route['api/star_rating/probe']['get']          = 'StarRatingController/probe';
$route['api/star_rating/my_ratings']['get']     = 'StarRatingController/my_ratings';
$route['api/star_rating/summary']['get']        = 'StarRatingController/summary';
$route['api/star_rating/submit_rating']['post'] = 'StarRatingController/submit_rating';
// === END GAP 11 routes ===

// === Agent C Batch 2 routes (28 May 2026) ===
$agentC_batch2_routes = __DIR__ . '/routes_agentC_batch2.php';
if (file_exists($agentC_batch2_routes)) { include($agentC_batch2_routes); }
// === END Agent C Batch 2 routes ===

// === STEM CRM v2.8 routes (deployed 29 May 2026) ===
$v28_routes = __DIR__ . '/routes_v28.php';
if (file_exists($v28_routes)) { include($v28_routes); }
// === END v2.8 routes ===

// === 404 stub safety net (29 May 2026 - parallel agent campaign) ===
$stub_404_routes = __DIR__ . '/routes_404_stubs.php';
if (file_exists($stub_404_routes)) { include($stub_404_routes); }
// === END 404 stub safety net ===

// === Agent B real routes (29 May 2026) ===
$agent_b_routes = __DIR__ . '/routes_v28_b.php';
if (file_exists($agent_b_routes)) { try { include($agent_b_routes); } catch (Throwable $_ex_b) { log_message('error', 'routes_v28_b: ' . $_ex_b->getMessage()); } }
// === END Agent B real routes ===

// === Agent C real routes (29 May 2026) ===
$agent_c_routes = __DIR__ . "/routes_v28_c.php";
if (file_exists($agent_c_routes)) { try { include($agent_c_routes); } catch (Throwable $_ex_c) { log_message('error', 'routes_v28_c: ' . $_ex_c->getMessage()); } }
// === END Agent C real routes ===

// === Agent D real routes (29 May 2026) ===
$agent_d_routes = __DIR__ . '/routes_v28_d.php';
if (file_exists($agent_d_routes)) { try { include($agent_d_routes); } catch (Throwable $_e) { log_message('error', 'routes_v28_d failed: ' . $_e->getMessage()); } }
// === END Agent D real routes ===

// === Agent G real routes (29 May 2026) ===
$agent_g_routes = __DIR__ . '/routes_v28_g.php';
if (file_exists($agent_g_routes)) { try { include($agent_g_routes); } catch (Throwable $_ex_g) { log_message('error', 'routes_v28_g: ' . $_ex_g->getMessage()); } }
// === END Agent G real routes ===

// === Agent E real routes (29 May 2026) ===
$agent_e_routes = __DIR__ . "/routes_v28_e.php";
if (file_exists($agent_e_routes)) { try { include($agent_e_routes); } catch (Throwable $_ex_e) { log_message('error', 'routes_v28_e: ' . $_ex_e->getMessage()); } }
// === END Agent E real routes ===

// === Agent F real routes (29 May 2026) ===
$agent_f_routes = __DIR__ . "/routes_v28_f.php";
if (file_exists($agent_f_routes)) { try { include($agent_f_routes); } catch (Throwable $_ex_f) { log_message('error', 'routes_v28_f: ' . $_ex_f->getMessage()); } }
// === END Agent F real routes ===

// === Agent A real routes (29 May 2026) ===
$agent_a_routes = __DIR__ . '/routes_v28_a.php';
if (file_exists($agent_a_routes)) { try { include($agent_a_routes); } catch (Throwable $_ex_a) { log_message('error', 'routes_v28_a: ' . $_ex_a->getMessage()); } }
// === END Agent A real routes ===

// === Agent H real routes (29 May 2026) ===
$agent_h_routes = __DIR__ . '/routes_v28_h.php';
if (file_exists($agent_h_routes)) { try { include($agent_h_routes); } catch (Throwable $_ex_h) { log_message('error', 'routes_v28_h: ' . $_ex_h->getMessage()); } }
// === END Agent H real routes ===

// === Agent I real routes (29 May 2026) ===
$agent_i_routes = __DIR__ . '/routes_v28_i.php';
if (file_exists($agent_i_routes)) { try { include($agent_i_routes); } catch (Throwable $_ex_i) { log_message('error', 'routes_v28_i: ' . $_ex_i->getMessage()); } }
// === END Agent I real routes ===

// === Agent J real routes (29 May 2026) ===
$agent_j_routes = __DIR__ . "/routes_v28_j.php";
if (file_exists($agent_j_routes)) { try { include($agent_j_routes); } catch (Throwable $_ex_j) { log_message('error', 'routes_v28_j: ' . $_ex_j->getMessage()); } }
// === END Agent J real routes ===

// === Agent K real routes (29 May 2026) ===
$agent_k_routes = __DIR__ . '/routes_v28_k.php';
if (file_exists($agent_k_routes)) { include($agent_k_routes); }
// === END Agent K real routes ===

// === Parity V28: AI scoring, e-sign, custom fields, forecast, multi-lang (50 routes) ===
$parity_v28_routes = __DIR__ . '/routes_parity_v28.php';
if (file_exists($parity_v28_routes)) { try { include($parity_v28_routes); } catch (Throwable $_ex_p) { log_message('error', 'routes_parity_v28: ' . $_ex_p->getMessage()); } }
// === END Parity V28 ===

// === Parity V28 Complete: induction, goal, target cascade, cron registry ===
$parity_v28_complete_routes = __DIR__ . '/routes_parity_v28_complete.php';
if (file_exists($parity_v28_complete_routes)) { try { include($parity_v28_complete_routes); } catch (Throwable $_ex_pc) { log_message('error', 'routes_parity_v28_complete: ' . $_ex_pc->getMessage()); } }
// === END Parity V28 Complete ===

// === Target Monitor Agent V28: per-BD target vs achievement ===
$target_monitor_routes = __DIR__ . '/routes_target_monitor.php';
if (file_exists($target_monitor_routes)) { try { include($target_monitor_routes); } catch (Throwable $_ex_tm) { log_message('error', 'routes_target_monitor: ' . $_ex_tm->getMessage()); } }
$review_target_bridge_routes = __DIR__ . '/routes_review_target_bridge.php';
if (file_exists($review_target_bridge_routes)) { try { include($review_target_bridge_routes); } catch (Throwable $_ex_rtb) { log_message('error', 'routes_review_target_bridge: ' . $_ex_rtb->getMessage()); } }

// === END Target Monitor ===




// === Blitz 30 May Agent E: comm, wallet, handover, csr_prospect, stakeholder, email_to_task ===
$blitz_e_routes = __DIR__ . '/routes_blitz_30may_e.php';
if (file_exists($blitz_e_routes)) { try { include($blitz_e_routes); } catch (Throwable $_ex_e) { log_message('error', 'blitz_e: ' . $_ex_e->getMessage()); } }
// === END Blitz 30 May Agent E ===

// === Blitz 30 May Agent F real routes ===
$blitz_f_routes = __DIR__ . '/routes_blitz_30may_f.php';
if (file_exists($blitz_f_routes)) { try { include($blitz_f_routes); } catch (Throwable $_ex) { log_message('error', 'blitz_f: ' . $_ex->getMessage()); } }
// === END ===


// === Blitz 30 May Agent D FINAL OVERRIDE (after all other route files) ===
if (file_exists(__DIR__ . "/routes_blitz_30may_d.php")) {
    include(__DIR__ . "/routes_blitz_30may_d.php");
}
// === END Agent D override ===

/* blitz 30may loader a */
@include(APPPATH.'config/routes_blitz_30may_a.php');

/* blitz 30may loader b */
@include(APPPATH.'config/routes_blitz_30may_b.php');

/* blitz 30may loader c */
@include(APPPATH.'config/routes_blitz_30may_c.php');

/* === MIGRATION 087 - Discipline v1 (20260531_081006) === */
$route['api/discipline/state'] = 'DisciplineApi/discipline_state';
$route['api/planner/same_day_request']  = 'PlannerRequestApi/same_day_request';
$route['api/planner/same_day_decision'] = 'PlannerRequestApi/same_day_decision';
$route['api/planner/yesterday_request'] = 'PlannerRequestApi/yesterday_request';
$route['api/planner/yesterday_decision'] = 'PlannerRequestApi/yesterday_decision';
$route['api/day_close/override_request'] = 'DayCloseOverrideApi/override_request';
$route['api/day_close/override_decision'] = 'DayCloseOverrideApi/override_decision';
/* === MIGRATION 087.1c - day_ceremony routes (plain, no [post] nesting) === */
$route['api/day_ceremony/start']        = 'DayCeremonyController/start';
$route['api/day_ceremony/close']        = 'DayCeremonyController/close';
$route['api/day_ceremony/end']          = 'DayCeremonyController/close';
$route['api/day_ceremony/today_status'] = 'DayCeremonyController/today_status';


// === Close-Out 2026-06-04 routes ===
if (file_exists(__DIR__ . '/routes_closeout.php')) { include(__DIR__ . '/routes_closeout.php'); }
if (file_exists(__DIR__ . '/routes_closeout_round2.php')) { include(__DIR__ . '/routes_closeout_round2.php'); }

// === Gap-Fix 2026-06-04 routes ===
if (file_exists(__DIR__ . '/routes_real_29.php')) { try { include(__DIR__ . '/routes_real_29.php'); } catch (Throwable $_ex) { log_message('error', 'gapfix_real_29: ' . $_ex->getMessage()); } }
if (file_exists(__DIR__ . '/routes_target_fix.php')) { try { include(__DIR__ . '/routes_target_fix.php'); } catch (Throwable $_ex) { log_message('error', 'gapfix_target: ' . $_ex->getMessage()); } }
if (file_exists(__DIR__ . '/routes_red_agents.php')) { try { include(__DIR__ . '/routes_red_agents.php'); } catch (Throwable $_ex) { log_message('error', 'gapfix_red_agents: ' . $_ex->getMessage()); } }
if (file_exists(__DIR__ . '/routes_gap_reports.php')) { try { include(__DIR__ . '/routes_gap_reports.php'); } catch (Throwable $_ex) { log_message('error', 'gapfix_reports: ' . $_ex->getMessage()); } }
// === END Gap-Fix 2026-06-04 ===

// === Data-Wire 2026-06-04 (mobile param-name + route-path shims) ===
if (file_exists(__DIR__ . '/routes_data_wire.php')) { try { include(__DIR__ . '/routes_data_wire.php'); } catch (Throwable $_ex) { log_message('error', 'datawire: ' . $_ex->getMessage()); } }
// === END Data-Wire ===

// === Missing Feature Routes (Route Enablement Audit 2026-06-06) ===
if (file_exists(__DIR__ . '/routes_missing_features.php')) {
    try { include(__DIR__ . '/routes_missing_features.php'); }
    catch (Throwable $_ex_mf) { log_message('error', 'routes_missing_features: ' . $_ex_mf->getMessage()); }
}
// === END Missing Feature Routes ===

// === Parity Build 2026-06-06 routes ===
if (file_exists(__DIR__ . '/routes_parity.php')) {
    try { include(__DIR__ . '/routes_parity.php'); }
    catch (Throwable $_ex_pb) { log_message('error', 'routes_parity: ' . $_ex_pb->getMessage()); }
}
// === END Parity Build routes ===

// === GROUP C AI/Agent Fix Routes (2026-06-06) ===
// C1 Pulse health+score, C2 ObjectionMining dump, C3 StallRisk score,
// C4 StakeholderMap list, C6 MeetingPrep artifact+runs_today, C7 ai/* routes
if (file_exists(__DIR__ . '/routes_parity_fix.php')) {
    try { include(__DIR__ . '/routes_parity_fix.php'); }
    catch (Throwable $_ex_gpfix) { log_message('error', 'routes_parity_fix: ' . $_ex_gpfix->getMessage()); }
}
// === END GROUP C Fix Routes ===

// Additive alias 2026-06-06: legacy Anaya funnel path -> AnayaBriefing/briefing (param: uid)
$route['agent/anaya/funnel'] = 'AnayaBriefing/briefing';
$route['api/agent/anaya/funnel'] = 'AnayaBriefing/briefing';

// === Audit fix pass 2026-06-06: ObjectionMining, RolePlay, CorporateMeetingPrep, CardOcr, AiLeadScore routes ===
$_audit_routes = APPPATH . "config/routes_audit_20260606.php";
if (file_exists($_audit_routes)) {
    try { include($_audit_routes); } catch (Throwable $_ex) { log_message("error", "audit_20260606: " . $_ex->getMessage()); }
}

// === ChartData 25 chart endpoints (added 2026-06-07) ===
$route["chartdata/planner_approval"]   = "ChartData/planner_approval";
$route["chartdata/avg_tasks"]          = "ChartData/avg_tasks";
$route["chartdata/plan_health"]        = "ChartData/plan_health";
$route["chartdata/task_star"]          = "ChartData/task_star";
$route["chartdata/call_star"]          = "ChartData/call_star";
$route["chartdata/exec_by_action"]     = "ChartData/exec_by_action";
$route["chartdata/funnel_stage"]       = "ChartData/funnel_stage";
$route["chartdata/funnel_monthly"]     = "ChartData/funnel_monthly";
$route["chartdata/lead_source"]        = "ChartData/lead_source";
$route["chartdata/closure_timeline"]   = "ChartData/closure_timeline";
$route["chartdata/closure_level"]      = "ChartData/closure_level";
$route["chartdata/proposal_type"]      = "ChartData/proposal_type";
$route["chartdata/proposal_status"]    = "ChartData/proposal_status";
$route["chartdata/proposal_sla"]       = "ChartData/proposal_sla";
$route["chartdata/mom_status"]         = "ChartData/mom_status";
$route["chartdata/mom_volume"]         = "ChartData/mom_volume";
$route["chartdata/mom_quality"]        = "ChartData/mom_quality";
$route["chartdata/rp_share"]           = "ChartData/rp_share";
$route["chartdata/rp_outcome"]         = "ChartData/rp_outcome";
$route["chartdata/rp_radar"]           = "ChartData/rp_radar";
$route["chartdata/expense_type"]       = "ChartData/expense_type";
$route["chartdata/expense_pipeline"]   = "ChartData/expense_pipeline";
$route["chartdata/pipeline_coverage"]  = "ChartData/pipeline_coverage";
$route["chartdata/ai_lead_band"]       = "ChartData/ai_lead_band";
$route["chartdata/day_ceremony"]       = "ChartData/day_ceremony";
$route["chartdata/action_type_distribution"] = "ChartData/action_type_distribution";
$route["chartdata/plan_vs_completed"]          = "ChartData/plan_vs_completed";
// === END ChartData routes ===

// === WS-B Travel-Cluster Hub routes (2026-06-07) ===
$route["travelcluster/clusters_for_user"] = "TravelClusterApi/clusters_for_user";
$route["travelcluster/prospectable"]      = "TravelClusterApi/prospectable";
$route["travelcluster/create"]            = "TravelClusterApi/create";
$route["travelcluster/list"]              = "TravelClusterApi/list";
$route["travelcluster/apollo_status"]     = "TravelClusterApi/apollo_status";
$route["travelcluster/linkedin_status"]   = "TravelClusterApi/linkedin_status";
$route["travelcluster/linkedin_enrich"]   = "TravelClusterApi/linkedin_enrich";
// === END WS-B Travel-Cluster Hub routes ===

// === WS-D Parity Approvals routes (2026-06-07) ===
$route["parityapprovals/pending_summary"] = "ParityApprovals/pending_summary";
$route["parityapprovals/planner_pending"] = "ParityApprovals/planner_pending";
$route["parityapprovals/meeting_pending"] = "ParityApprovals/meeting_pending";
// === END WS-D Parity Approvals routes ===

// === WS-C Live Monitor Agent routes (2026-06-07) ===
$route["api/livemonitor/scan"]         = "LiveMonitorAgent/scan";
$route["api/livemonitor/efficiency"]   = "LiveMonitorAgent/efficiency";
$route["api/livemonitor/raise_alerts"] = "LiveMonitorAgent/raise_alerts";
// === END WS-C Live Monitor Agent routes ===

// === WS-T Target vs Achievement routes (2026-06-07) ===
$route["api/target/vs_achievement"]  = "TargetAchievement/vs_achievement";
$route["api/target/review_link"]     = "TargetAchievement/review_link";
$route["api/target/leaderboard"]     = "TargetAchievement/leaderboard";
$route["api/target/export_pdf"]      = "TargetAchievement/export_pdf";
$route["api/target/export_excel"]    = "TargetAchievement/export_excel";
// === END WS-T Target vs Achievement routes ===

/* === missing-routes fix 2026-06-07 (additive, last include so it cannot be overridden) === */
if (file_exists(__DIR__ . "/routes_missing_routes_20260607.php")) {
    try { include(__DIR__ . "/routes_missing_routes_20260607.php"); }
    catch (Throwable $_ex_mr) { log_message("error", "routes_missing_routes_20260607: " . $_ex_mr->getMessage()); }
}

/* === task detail/save_draft real handler (additive, 2026-06-07, very last include) === */
if (file_exists(__DIR__ . "/routes_task_detail_real_20260607.php")) {
    try { include(__DIR__ . "/routes_task_detail_real_20260607.php"); }
    catch (Throwable $_ex_td) { log_message("error", "routes_task_detail_real_20260607: " . $_ex_td->getMessage()); }
}

/* === planner v2 extra endpoints (additive, 2026-06-07, very last include) === */
if (file_exists(__DIR__ . "/routes_planner_v2_extra_20260607.php")) {
    try { include(__DIR__ . "/routes_planner_v2_extra_20260607.php"); }
    catch (Throwable $_ex) { log_message("error", "planner_v2_extra: " . $_ex->getMessage()); }
}

/* === missing-method repoint fix 2026-06-07b (additive, very last include) === */
if (file_exists(__DIR__ . "/routes_missing_fix_20260607b.php")) {
    try { include(__DIR__ . "/routes_missing_fix_20260607b.php"); }
    catch (Throwable $_ex_mf) { log_message("error", "missing_fix_b: " . $_ex_mf->getMessage()); }
}

/* === gapfix2 last 4 mobile endpoints (additive, 2026-06-07, very last include) === */
if (file_exists(__DIR__ . "/routes_gapfix2_20260607.php")) {
    try { include(__DIR__ . "/routes_gapfix2_20260607.php"); }
    catch (Throwable $_ex_gf2) { log_message("error", "gapfix2: " . $_ex_gf2->getMessage()); }
}

/* === agents wire (additive, 2026-06-07, very last include) === */
if (file_exists(__DIR__ . "/routes_agents_wire_20260607.php")) {
    try { include(__DIR__ . "/routes_agents_wire_20260607.php"); }
    catch (Throwable $_ex_aw) { log_message("error", "agents_wire: " . $_ex_aw->getMessage()); }
}

// --- Sales Monitoring Brain (additive, read-only) added 2026-06-08 ---
$brain_routes = __DIR__ . '/routes_monitoring_brain_20260608.php';
if (file_exists($brain_routes)) { try { include($brain_routes); } catch (Throwable $_ex_brain) { log_message('error', 'routes_monitoring_brain: ' . $_ex_brain->getMessage()); } }

/* === Phase 1 feature routes 2026-06-08 (additive, agent B+A shared fragment, very last include) === */
if (file_exists(__DIR__ . "/routes_phase1_20260608.php")) {
    try { include(__DIR__ . "/routes_phase1_20260608.php"); }
    catch (Throwable $_ex_p1) { log_message("error", "routes_phase1_20260608: " . $_ex_p1->getMessage()); }
}



/* === Phase 2+3 feature routes 2026-06-08 (additive, one include only; agents append to fragment) === */
if (file_exists(__DIR__ . "/routes_phase23_20260608.php")) {
    try { include(__DIR__ . "/routes_phase23_20260608.php"); }
    catch (Throwable $_ex_p23) { log_message("error", "routes_phase23_20260608: " . $_ex_p23->getMessage()); }
}

/* === Schema guard route 2026-06-08 (additive, read-only) === */
if (file_exists(__DIR__ . '/routes_schema_guard_20260608.php')) {
    try { include(__DIR__ . '/routes_schema_guard_20260608.php'); }
    catch (Throwable $_ex_sg) { log_message('error', 'routes_schema_guard: ' . $_ex_sg->getMessage()); }
}

/* === dashboard summary mirror 2026-06-10 (additive, read-only, very last include) === */
if (file_exists(__DIR__ . '/routes_dashboard_summary_20260610.php')) {
    try { include(__DIR__ . '/routes_dashboard_summary_20260610.php'); }
    catch (Throwable $_ex_ds) { log_message('error', 'routes_dashboard_summary: ' . $_ex_ds->getMessage()); }
}

/* === status transition mirror 2026-06-10 (additive, read-only, very last include) === */
if (file_exists(__DIR__ . '/routes_status_transitions_20260610.php')) {
    try { include(__DIR__ . '/routes_status_transitions_20260610.php'); }
    catch (Throwable $_ex_st) { log_message('error', 'routes_status_transitions: ' . $_ex_st->getMessage()); }
}

/* === plan-cell delete 2026-06-10 (additive, Area D, very last include) === */
if (file_exists(__DIR__ . '/routes_plan_delete_20260610.php')) {
    try { include(__DIR__ . '/routes_plan_delete_20260610.php'); }
    catch (Throwable $_ex_pd) { log_message('error', 'routes_plan_delete: ' . $_ex_pd->getMessage()); }
}

/* === planner submit-for-approval 2026-06-10 (additive, approval chain Step 1, very last include) === */
if (file_exists(__DIR__ . '/routes_planner_request_approval_20260610.php')) {
    try { include(__DIR__ . '/routes_planner_request_approval_20260610.php'); }
    catch (Throwable $_ex_pra) { log_message('error', 'routes_planner_request_approval: ' . $_ex_pra->getMessage()); }
}

/* === planner manager task-assign 2026-06-10 (additive, approval chain Step 4, very last include) === */
if (file_exists(__DIR__ . '/routes_planner_assign_task_20260610.php')) {
    try { include(__DIR__ . '/routes_planner_assign_task_20260610.php'); }
    catch (Throwable $_ex_pat) { log_message('error', 'routes_planner_assign_task: ' . $_ex_pat->getMessage()); }
}


/* === task execution parity 2026-06-10 (additive, action-type schema + per-stage writers + delay remarks, very last include) === */
if (file_exists(__DIR__ . '/routes_exec_parity_20260610.php')) {
    try { include(__DIR__ . '/routes_exec_parity_20260610.php'); }
    catch (Throwable $_ex_ep) { log_message('error', 'routes_exec_parity: ' . $_ex_ep->getMessage()); }
}


/* === mobile-named endpoints 2026-06-10 (additive, exact mobile contract names, very last include) === */
if (file_exists(__DIR__ . '/routes_mobile_named_endpoints_20260610.php')) {
    try { include(__DIR__ . '/routes_mobile_named_endpoints_20260610.php'); }
    catch (Throwable $_ex_mne) { log_message('error', 'routes_mobile_named_endpoints: ' . $_ex_mne->getMessage()); }
}


/* === reminder endpoints 2026-06-11 (additive, v2144 mobile parity, very last include so it wins) === */
$__rrv = __DIR__ . '/routes_reminder_v2144.php';
if (file_exists($__rrv)) {
    try { include($__rrv); }
    catch (Throwable $_ex_rrv) { log_message('error', 'routes_reminder_v2144: ' . $_ex_rrv->getMessage()); }
}


/* === backend defect sweep 2026-06-16 (additive). MUST be included BEFORE the
   parity_closeout include below: its literal keys must be inserted ahead of the
   api/(:any) catch-all (defined in routes_parity_closeout_20260611.php) so
   first-match-wins routes them to real controllers instead of StubController. === */
$__rsf = __DIR__ . '/routes_sweep_fix_20260616.php';
if (file_exists($__rsf)) {
    try { include($__rsf); }
    catch (Throwable $_ex_rsf) { log_message('error', 'routes_sweep_fix_20260616: ' . $_ex_rsf->getMessage()); }
}


/* === M2M Assurance routes 2026-06-16 (additive; included BEFORE parity_closeout
 * so the literal /api/m2m/* routes are inserted into the $route table ahead of
 * the StubController api/(:any) catch-all that parity_closeout appends, and
 * therefore win under CI3 first-match-wins). New controllers only; no existing
 * route or method touched. Guarded try/catch include like the other fragments. */
$__rm2m = __DIR__ . '/routes_m2m_assurance_20260616.php';
if (file_exists($__rm2m)) {
    try { include($__rm2m); }
    catch (Throwable $_ex_m2m) { log_message('error', 'routes_m2m_assurance: ' . $_ex_m2m->getMessage()); }
}

/* === parity closeout routes 2026-06-11 (additive, maps app paths to existing methods, very last include) === */
$__rc = __DIR__ . '/routes_parity_closeout_20260611.php';
if (file_exists($__rc)) {
    try { include($__rc); }
    catch (Throwable $_ex_rc) { log_message('error', 'routes_parity_closeout: ' . $_ex_rc->getMessage()); }
}


/* === CM Day Management fix 2026-06-16 (additive literal; included AFTER parity
 * closeout so api/planner/pbni_list beats the StubController catch-all) === */
$__rcdm = __DIR__ . '/routes_cm_daymgmt_fix_20260616.php';
if (file_exists($__rcdm)) {
    try { include($__rcdm); }
    catch (Throwable $_ex_rcdm) { log_message('error', 'routes_cm_daymgmt_fix: ' . $_ex_rcdm->getMessage()); }
}

<?php
/**
 * routes_agentC_batch2.php
 * Agent C - Batch 2 (Advanced) - 28 May 2026
 *
 * Wires all 23 missing mobile endpoints.
 * See /home/user/workspace/agent_C_routes_batch2_28may.md for full log.
 *
 * Included from routes.php.
 */
defined('BASEPATH') OR exit('No direct script access allowed');

// -------------------------------------------------------------------------
// 1. /api/cm/myday  -> CmPlannerController/today
//    CM day plan for today. CmPlannerController accepts all tokens when
//    STEM_DIGEST_TOKEN env var is not set (dev/staging mode).
// -------------------------------------------------------------------------
$route['api/cm/myday']['GET'] = 'CmPlannerController/today';
$route['api/cm/myday']        = 'CmPlannerController/today';

// -------------------------------------------------------------------------
// 2. /api/check_management/today  -> MobileExtrasController/check_management_today
//    BD task completion summary for today under a CM.
// -------------------------------------------------------------------------
$route['api/check_management/today']['GET'] = 'MobileExtrasController/check_management_today';
$route['api/check_management/today']        = 'MobileExtrasController/check_management_today';

// -------------------------------------------------------------------------
// 3. /api/inside_sales/list  -> InsideSalesController/list
//    Inside sales queue (alias for my_queue). Accepts per-user JWT.
// -------------------------------------------------------------------------
$route['api/inside_sales/list']['GET'] = 'InsideSalesController/list';
$route['api/inside_sales/list']        = 'InsideSalesController/list';

// -------------------------------------------------------------------------
// 4. /api/star_rating/list  -> StarRatingController/list
//    Star ratings for a BD (alias for my_ratings). Accepts per-user JWT.
// -------------------------------------------------------------------------
$route['api/star_rating/list']['GET'] = 'StarRatingController/list';
$route['api/star_rating/list']        = 'StarRatingController/list';

// -------------------------------------------------------------------------
// 5. /api/travel_cluster/list  -> TravelClusterController/list
//    Travel cluster for calling user (alias for my_cluster). Accepts per-user JWT.
// -------------------------------------------------------------------------
$route['api/travel_cluster/list']['GET'] = 'TravelClusterController/list';
$route['api/travel_cluster/list']        = 'TravelClusterController/list';

// -------------------------------------------------------------------------
// 6. /api/upsell/list  -> UpsellClientController/list
//    Upsell pipeline summary (alias for total_summary). Accepts per-user JWT.
// -------------------------------------------------------------------------
$route['api/upsell/list']['GET'] = 'UpsellClientController/list';
$route['api/upsell/list']        = 'UpsellClientController/list';

// -------------------------------------------------------------------------
// 7. /api/anaya/ask  -> AnayaAsk/ask
//    Anaya AI ask endpoint. GET returns status; POST submits query.
//    _require_bearer() now accepts per-user JWT (patched 28 May 2026).
// -------------------------------------------------------------------------
$route['api/anaya/ask']['GET']  = 'AnayaAsk/ask_mobile';
$route['api/anaya/ask']['POST'] = 'AnayaAsk/ask_mobile';
$route['api/anaya/ask']         = 'AnayaAsk/ask_mobile';

// -------------------------------------------------------------------------
// 8. /api/csr_closure  -> CsrClosureController/index
//    CSR closure summary. index() has per-user JWT gate (added 28 May 2026).
// -------------------------------------------------------------------------
$route['api/csr_closure']['GET'] = 'CsrClosureController/index';
$route['api/csr_closure']        = 'CsrClosureController/index';

// -------------------------------------------------------------------------
// 9. /api/district_intel  -> District_intel/summary
//    District breakdown (no auth required on this controller).
// -------------------------------------------------------------------------
$route['api/district_intel']['GET'] = 'District_intel/summary';
$route['api/district_intel']        = 'District_intel/summary';

// -------------------------------------------------------------------------
// 10. /api/field_resilience  -> FieldResilienceController/probe
//     Field resilience health probe (no auth required on probe()).
// -------------------------------------------------------------------------
$route['api/field_resilience']['GET'] = 'FieldResilienceController/probe';
$route['api/field_resilience']        = 'FieldResilienceController/probe';

// -------------------------------------------------------------------------
// 11. /api/graph_analysis  -> FunnelReportController/probe
//     Graph analysis probe. FunnelReportController accepts per-user JWT.
// -------------------------------------------------------------------------
$route['api/graph_analysis']['GET'] = 'FunnelReportController/probe';
$route['api/graph_analysis']        = 'FunnelReportController/probe';

// -------------------------------------------------------------------------
// 12. /api/mca  -> Mca21/status
//     MCA-21 sync status. Mca21._bearer_ok() now accepts per-user JWT.
// -------------------------------------------------------------------------
$route['api/mca']['GET'] = 'Mca21/status';
$route['api/mca']        = 'Mca21/status';

// -------------------------------------------------------------------------
// 13. /api/meeting_economics/summary  -> Meeting_economics_api/summary
//     Meeting quality summary (added 28 May 2026). Accepts per-user JWT.
// -------------------------------------------------------------------------
$route['api/meeting_economics/summary']['GET'] = 'Meeting_economics_api/summary';
$route['api/meeting_economics/summary']        = 'Meeting_economics_api/summary';

// -------------------------------------------------------------------------
// 14. /api/partner_master  -> Partner_master_api/types
//     Partner/buyer type master list (no auth required).
// -------------------------------------------------------------------------
$route['api/partner_master']['GET'] = 'Partner_master_api/types';
$route['api/partner_master']        = 'Partner_master_api/types';

// -------------------------------------------------------------------------
// 15. /api/path_c  -> Path_c_api/index
//     Path-C eligibility check. index() resolves uid from JWT.
// -------------------------------------------------------------------------
$route['api/path_c']['GET'] = 'Path_c_api/index';
$route['api/path_c']        = 'Path_c_api/index';

// -------------------------------------------------------------------------
// 16. /api/planner_analytics  -> Planner_api/planner_analytics
//     Planner submission analytics (added 28 May 2026). Accepts per-user JWT.
// -------------------------------------------------------------------------
$route['api/planner_analytics']['GET'] = 'Planner_api/planner_analytics';
$route['api/planner_analytics']        = 'Planner_api/planner_analytics';

// -------------------------------------------------------------------------
// 17. /api/team_location  -> MobileExtrasController/team_location_probe
//     Live BD location probe (quick probe; detailed data at /live). JWT-gated.
// -------------------------------------------------------------------------
$route['api/team_location']['GET'] = 'MobileExtrasController/team_location_probe';
$route['api/team_location']        = 'MobileExtrasController/team_location_probe';

// -------------------------------------------------------------------------
// 18. /api/special_remarks  -> MobileExtrasController/special_remarks_index
//     Special remarks stream. JWT-gated. special_remarks_index translates uid->user_id.
// -------------------------------------------------------------------------
$route['api/special_remarks']['GET'] = 'MobileExtrasController/special_remarks_index';
$route['api/special_remarks']        = 'MobileExtrasController/special_remarks_index';

// -------------------------------------------------------------------------
// 19. /api/wdl/list  -> Applause_api/wdl_list
//     WDL (Work Day Leave) request list, scoped to uid via JWT.
// -------------------------------------------------------------------------
$route['api/wdl/list']['GET'] = 'Applause_api/wdl_list';
$route['api/wdl/list']        = 'Applause_api/wdl_list';

// -------------------------------------------------------------------------
// 20. /api/bd_performance  -> Mobile_read_api/bd_performance
//     BD task execution performance. Accepts per-user JWT.
// -------------------------------------------------------------------------
$route['api/bd_performance']['GET'] = 'Mobile_read_api/bd_performance';
$route['api/bd_performance']        = 'Mobile_read_api/bd_performance';

// -------------------------------------------------------------------------
// 21. /api/bd_profile  -> Mobile_read_api/bd_profile
//     BD profile card. Accepts per-user JWT.
// -------------------------------------------------------------------------
$route['api/bd_profile']['GET'] = 'Mobile_read_api/bd_profile';
$route['api/bd_profile']        = 'Mobile_read_api/bd_profile';

// -------------------------------------------------------------------------
// 22. /api/efficiency  -> Mobile_read_api/efficiency
//     Route/field efficiency metrics. Accepts per-user JWT.
// -------------------------------------------------------------------------
$route['api/efficiency']['GET'] = 'Mobile_read_api/efficiency';
$route['api/efficiency']        = 'Mobile_read_api/efficiency';

// -------------------------------------------------------------------------
// 23. /api/app_usage  -> Mobile_read_api/app_usage
//     App session and activity stats. Accepts per-user JWT.
// -------------------------------------------------------------------------
$route['api/app_usage']['GET'] = 'Mobile_read_api/app_usage';
$route['api/app_usage']        = 'Mobile_read_api/app_usage';

// === END Agent C Batch 2 routes ===

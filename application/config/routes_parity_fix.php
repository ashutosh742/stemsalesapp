<?php
/**
 * routes_parity_fix.php
 * Created: 2026-06-06 — GROUP C AI/Agent endpoint fixes
 *
 * Additive only. Never break working routes.
 * CI3 rule: route target must be CLASS-NAME (filename prefix) not lowercased alias.
 *
 * Fixes applied:
 *   C1  Pulse health+score — class is PulseController (file Pulse.php)
 *   C2  ObjectionMining dump — class is ObjectionMiningController (file ObjectionMining.php)
 *         No "dump" method; route to top_themes_week (the real dump equivalent)
 *   C3  StallRisk score — class StallRiskController; method is score_one() not score()
 *   C4  StakeholderMap list — class StakeholderMapController; method is list_for_lead()
 *   C6  CorporateMeetingPrepController artifact+runs_today — was unrouted
 *   C7  M073 AiAssistant /ai/* — class M073_ai_assistant
 */
defined('BASEPATH') OR exit('No direct script access allowed');

// -----------------------------------------------------------------------
// C1: Pulse health + score (class PulseController, file Pulse.php)
// CI3 resolves by filename: "Pulse" maps to Pulse.php which declares PulseController
// -----------------------------------------------------------------------
$route['api/pulse/health'] = 'Pulse/health';
$route['api/pulse/score']  = 'Pulse/score';

// -----------------------------------------------------------------------
// C2: ObjectionMining — top_themes_week as the real "dump" equivalent
//     Class ObjectionMiningController (file ObjectionMining.php)
//     Mobile screen and live check call /api/objection_mining/dump
//     No dump() method exists; route to top_themes_week which returns all weekly objection rows
// -----------------------------------------------------------------------
$route['api/objection_mining/dump']         = 'ObjectionMining/top_themes_week';
$route['api/objection_mining/top_themes']   = 'ObjectionMining/top_themes_week';
$route['api/objection_mining/lead_blockers']= 'ObjectionMining/lead_blockers';
$route['api/objection_mining/extract']      = 'ObjectionMining/extract_for_meeting';
$route['api/objection_mining/by_bd']        = 'ObjectionMining/by_bd';

// -----------------------------------------------------------------------
// C3: StallRisk score — class StallRiskController (file StallRisk.php)
//     Method is score_one(), not score()
// -----------------------------------------------------------------------
$route['api/stall_risk/score']          = 'StallRisk/score_one';
$route['api/stall_risk/critical_today'] = 'StallRisk/critical_today';
$route['api/stall_risk/by_bd']          = 'StallRisk/by_bd';
$route['api/stall_risk/run_batch']      = 'StallRisk/run_batch';

// -----------------------------------------------------------------------
// C4: StakeholderMap list — class StakeholderMapController (file StakeholderMap.php)
//     Method is list_for_lead(), not list()
// -----------------------------------------------------------------------
$route['api/stakeholder_map/list']          = 'StakeholderMap/list_for_lead';
$route['api/stakeholder_map/add']           = 'StakeholderMap/add';
$route['api/stakeholder_map/update']        = 'StakeholderMap/update';
$route['api/stakeholder_map/missing_dm']    = 'StakeholderMap/missing_dm_today';
$route['api/stakeholder_map/summary']       = 'StakeholderMap/summary_by_bd';

// -----------------------------------------------------------------------
// C6: CorporateMeetingPrepController artifact+runs_today (was unrouted)
//     Class CorporateMeetingPrepController (file CorporateMeetingPrepController.php)
//     NOTE: controller loads AIAgents/CorporateMeetingPrep_agent model which is absent.
//     Routes added; controller will 500 if agent model is missing, but endpoint is reachable.
//     artifact() and runs_today() have DB queries that do NOT require the agent.
// -----------------------------------------------------------------------
$route['api/meeting_prep/artifact']    = 'CorporateMeetingPrepController/artifact';
$route['api/meeting_prep/runs_today']  = 'CorporateMeetingPrepController/runs_today';

// -----------------------------------------------------------------------
// C7: M073 AiAssistant /ai/* routes — class M073_ai_assistant (file M073_ai_assistant.php)
//     Screen calls /ai/recommendations_for_user, /ai/refresh_all_for_user, /ai/explain
//     (no /api/ prefix — these match CI3 base URL directly)
// -----------------------------------------------------------------------
$route['ai/recommendations_for_user']       = 'M073_ai_assistant/recommendations_for_user';
$route['ai/refresh_all_for_user']           = 'M073_ai_assistant/refresh_all_for_user';
$route['ai/explain']                        = 'M073_ai_assistant/explain';
$route['ai/score_lead']                     = 'M073_ai_assistant/score_lead';
$route['ai/deal_health']                    = 'M073_ai_assistant/deal_health';


// -----------------------------------------------------------------------
// C9: Add missing routes for cohort create/members/snapshot_run
//     Mobile screen calls these paths after C9 fix (api/ prefix added)
//     M075_cohort_and_trends_viewer has create(), members(), snapshot_run() methods
// -----------------------------------------------------------------------
$route['api/cohort/create']        = 'M075_cohort_and_trends_viewer/create';
$route['api/cohort/members']       = 'M075_cohort_and_trends_viewer/members';
$route['api/cohort/snapshot_run']  = 'M075_cohort_and_trends_viewer/snapshot_run';

// -----------------------------------------------------------------------
// C9: OCR create_lead_from_scan route
//     Mobile screen calls /api/ocr/create_lead_from_scan after C9 fix
//     M072_business_card_ocr has create_lead_from_scan() method
// -----------------------------------------------------------------------
$route['api/ocr/create_lead_from_scan'] = 'M072_business_card_ocr/create_lead_from_scan';


// -----------------------------------------------------------------------
// C12: /api/mom/transcribe — needed by tools.transcribeAudio in client.js
//      MomV2Controller has api_transcribe() method
// -----------------------------------------------------------------------
$route['api/mom/transcribe'] = 'MomV2Controller/api_transcribe';


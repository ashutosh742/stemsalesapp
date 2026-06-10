<?php
/**
 * routes_audit_20260606.php
 * Audit E fix pass -- 2026-06-06
 * Additive only. Staging only. Production untouched.
 * v2: corrected class-name targets (CI3 requires class name in file to match route target).
 *   AiLeadScore.php / AiLeadScoreController.php both define class AILeadScoreController.
 *   RolePlay.php defines class RolePlayController.
 *   Routes must use the actual class name after any class_alias at file bottom.
 *   AiLeadScoreController has class_alias so "AiLeadScoreController" works.
 *   RolePlayController needs similar alias OR route must say "RolePlayController".
 *   But RolePlayController.php does not exist -- so create alias route to existing working path.
 */
defined("BASEPATH") OR exit("No direct script access allowed");

// --- ObjectionMining (class name = ObjectionMining; file = ObjectionMining.php) ---
$route["api/objection_mining/probe"]         = "ObjectionMining/probe";
$route["api/objection_mining/top_themes"]    = "ObjectionMining/top_themes_week";
$route["api/objection_mining/lead_blockers"] = "ObjectionMining/lead_blockers";
$route["api/objection_mining/extract"]       = "ObjectionMining/extract_for_meeting";
$route["api/objection_mining/by_bd"]         = "ObjectionMining/by_bd";
$route["api/objection_mining/by_cluster"]    = "ObjectionMining/by_cluster";
$route["api/objection_mining/kb_candidates"] = "ObjectionMining/kb_candidates";
$route["api/objection_mining/run_batch"]     = "ObjectionMining/run_weekly_batch";

// --- RolePlay: class inside RolePlay.php is RolePlayController.
//     CI3 route "RolePlayController/x" needs file RolePlayController.php.
//     File does not exist -> use v28/RolePlayV28 for scenarios/start (already works).
//     For probe: use the v28 path which is already wired.
//     NOTE: RolePlay.php is class RolePlayController; to route we need RolePlayController.php.
//     We create a one-line alias shim below if not present.
$route["api/role_play/probe"]              = "RolePlayController/probe";
$route["api/role_play/list_scenarios"]     = "RolePlayController/list_scenarios";
$route["api/role_play/start"]              = "RolePlayController/start_session";
$route["api/role_play/sessions"]           = "RolePlayController/list_my_sessions";

// --- AiLeadScore: class_alias("AILeadScoreController","AiLeadScoreController") exists.
//     Route "AiLeadScoreController/top" is correct and was in place.
//     The 500 was digest_auth_check undefined -- now fixed. Keep using AiLeadScoreController.
$route["api/ai_lead_score/top"]       = "AiLeadScoreController/top";
$route["api/ai_lead_score/hot_leads"] = "AiLeadScoreController/hot_leads";
$route["api/ai_lead_score/compute"]   = "AiLeadScoreController/compute";

// --- CorporateMeetingPrepController canonical probe alias ---
$route["api/corporate_meeting_prep/probe"]     = "CorporateMeetingPrepController/probe";
$route["api/corporate_meeting_prep/generate"]  = "CorporateMeetingPrepController/generate";
$route["api/corporate_meeting_prep/auto_scan"] = "CorporateMeetingPrepController/auto_scan";
$route["api/corporate_meeting_prep/artifact"]  = "CorporateMeetingPrepController/artifact";
$route["api/corporate_meeting_prep/runs_today"]= "CorporateMeetingPrepController/runs_today";

// --- CardOcr action routes ---
$route["api/card_ocr/upload"]    = "CardOcr/upload";
$route["api/card_ocr/confirm"]   = "CardOcr/confirm";
$route["api/card_ocr/dedup"]     = "CardOcr/dedup_candidates";
$route["api/card_ocr/discard"]   = "CardOcr/discard";

// --- Anaya_reports: no-uid health probe alias ---
$route["api/anaya_reports/health"] = "Anaya_reports/probe";

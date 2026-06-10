<?php
// ============================================================
// Phase 2 + 3 routes - 2026-06-08 (additive only)
// First agent to write this file creates it; subsequent agents APPEND.
// DO NOT add a second include in routes.php - one include only.
// ============================================================

// ---- Agent G: D6 No-Code Trigger Builder ----
$route['api/trigger/rules']        = 'TriggerBuilder_api/rules';
$route['api/trigger/rule/save']    = 'TriggerBuilder_api/rule_save';
$route['api/trigger/rule/delete']  = 'TriggerBuilder_api/rule_delete';
$route['api/trigger/evaluate']     = 'TriggerBuilder_api/evaluate';

// ---- Agent G: E6 Bulk Broadcast Scaffold ----
$route['api/broadcast/create']     = 'Broadcast_api/create';
$route['api/broadcast/list']       = 'Broadcast_api/list_index';
$route['api/broadcast/get']        = 'Broadcast_api/get';

// ---- Agent G: G5 Multi-Language Proposal Fields ----
$route['api/i18n/get']             = 'ProposalI18n_api/get';
$route['api/i18n/set']             = 'ProposalI18n_api/set';

// ---- Agent G: G6 Grant/Sanction Lifecycle ----
$route['api/grant/stages']         = 'GrantLifecycle_api/stages';
$route['api/grant/list']           = 'GrantLifecycle_api/list_index';
$route['api/grant/save']           = 'GrantLifecycle_api/save';

// ---- Agent G: H6 Proposal-Draft Assist ----
$route['api/proposal/draft']       = 'ProposalDraft_api/draft';

// ---- Agent E: C5a Opportunity Value ----
$route["api/oppvalue/set"]     = "OpportunityValue_api/set";
$route["api/oppvalue/get"]     = "OpportunityValue_api/get";
$route["api/oppvalue/pending"] = "OpportunityValue_api/pending";

// ---- Agent E: C5b Stage Probability ----
$route["api/probability/config"] = "WeightedPipeline_api/config";
$route["api/probability/set"]    = "WeightedPipeline_api/set_prob";

// ---- Agent E: C5c + F3 Weighted Pipeline + Backplan ----
$route["api/pipeline/weighted"]  = "WeightedPipeline_api/weighted";
$route["api/pipeline/backplan"]  = "WeightedPipeline_api/backplan";
$route["api/pipeline/target"]      = "WeightedPipeline_api/target_get";
$route["api/pipeline/target/set"]  = "WeightedPipeline_api/target_set";

// ---- Agent E: F6 Win/Loss Analytics ----
$route["api/winloss/summary"]    = "WinLoss_api/summary";
$route["api/winloss/by_cluster"] = "WinLoss_api/by_cluster";
$route["api/winloss/by_bd"]      = "WinLoss_api/by_bd";

// ---- Agent F: E4 Stage-Triggered WhatsApp Nudges ----
$route['api/nudge/rules']        = 'StageNudge_api/rules';
$route['api/nudge/rule/save']    = 'StageNudge_api/rule_save';
$route['api/nudge/due']          = 'StageNudge_api/due';
$route['api/nudge/fire']         = 'StageNudge_api/fire';

// ---- Agent F: A4 Section-135 Auto-Enrich ----
$route['api/enrich/company']     = 'LeadEnrich_api/company';
$route['api/enrich/preview']     = 'LeadEnrich_api/preview';

// ---- Agent F: G3 Gov Directory ----
$route['api/govdir/list']        = 'GovDirectory_api/list_index';
$route['api/govdir/get']         = 'GovDirectory_api/get';
$route['api/govdir/save']        = 'GovDirectory_api/save';

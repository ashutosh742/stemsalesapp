<?php
// ============================================================
// Phase 1 routes - 2026-06-08 (additive only)
// Agent A routes will also append to this same file.
// ============================================================

// ---- Agent B: G1 Tender/RFP/EOI Tracker ----
$route['api/tender/list']      = 'Tender_api/list_index';
$route['api/tender/get']       = 'Tender_api/get';
$route['api/tender/save']      = 'Tender_api/save';
$route['api/tender/doc/add']   = 'Tender_api/doc_add';

// ---- Agent B: G2 CSR Funding-Source Mapping ----
$route['api/funding/list']     = 'FundingSource_api/list_index';
$route['api/funding/save']     = 'FundingSource_api/save';

// ---- Agent B: A5 Saved Smart Lists ----
$route['api/segment/list']     = 'SavedSegment_api/list_index';
$route['api/segment/save']     = 'SavedSegment_api/save';
$route['api/segment/delete']   = 'SavedSegment_api/delete';

// ---- Agent B: C6 Reason-for-Loss ----
$route['api/loss/reasons']     = 'LossReason_api/reasons';
$route['api/loss/capture']     = 'LossReason_api/capture';
$route['api/loss/report']      = 'LossReason_api/report';

// ---- Agent B: I2 Audit Hook Helper ----
$route['api/audit/log']        = 'AuditExt_api/log';
$route['api/audit/recent']     = 'AuditExt_api/recent';


// ---- Agent A: C2+I3 Stage Gate Enforcement ----
$route["api/gate/config"]   = "StageGate_api/config";
$route["api/gate/check"]    = "StageGate_api/check";
$route["api/gate/override"] = "StageGate_api/override";

// ---- Agent A: A2 Duplicate Merge ----
$route["api/merge/candidates"] = "LeadMerge_api/candidates";
$route["api/merge/apply"]      = "LeadMerge_api/apply";

// ---- Agent A: D1 Auto-Assign ----
$route["api/assign/suggest"] = "AutoAssign_api/suggest";
$route["api/assign/apply"]   = "AutoAssign_api/apply";

// ---- Agent D: Mobile Push Token Registry ----
$route["api/push/register"]   = "Push_api/register";
$route["api/push/unregister"] = "Push_api/unregister";
$route["api/push/tokens"]     = "Push_api/tokens";

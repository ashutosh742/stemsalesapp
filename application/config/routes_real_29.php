<?php
defined("BASEPATH") OR exit("No direct script access allowed");

// Routes for the 29 new real implementations in Mobile_stub_real
// Add these lines to application/config/routes.php

$route["api/anaya/prefill_closure"]          = "Mobile_stub_real/anaya_prefill_closure";
$route["api/anaya/draft_mom"]                = "Mobile_stub_real/anaya_draft_mom";
$route["api/anaya/dm_contact_gap_autofill"]  = "Mobile_stub_real/anaya_dm_contact_gap_autofill";
$route["api/anaya/suggest_cstatus"]          = "Mobile_stub_real/anaya_suggest_cstatus";
$route["api/anaya/bd_request_type_suggest"]  = "Mobile_stub_real/anaya_bd_request_type_suggest";
$route["api/anaya/suggest_followup"]         = "Mobile_stub_real/anaya_suggest_followup";
$route["api/comm/stakeholder/add"]           = "Mobile_stub_real/stakeholder_add";
$route["api/comm/stakeholder/edit"]          = "Mobile_stub_real/stakeholder_edit";
$route["api/comm/stakeholder/deactivate"]    = "Mobile_stub_real/stakeholder_deactivate";
$route["api/comm/stakeholder/list"]          = "Mobile_stub_real/stakeholder_list";
$route["api/discipline/advance/request"]     = "Mobile_stub_real/advance_request";
$route["api/discipline/advance/approve"]     = "Mobile_stub_real/advance_approve";
$route["api/discipline/advance/consume"]     = "Mobile_stub_real/advance_consume";
$route["api/discipline/advance/queue"]       = "Mobile_stub_real/advance_queue";
$route["api/discipline/advance/return"]      = "Mobile_stub_real/advance_return";
$route["api/discipline/advance/settle"]      = "Mobile_stub_real/advance_settle";
$route["api/discipline/expense/submit"]      = "Mobile_stub_real/expense_submit";
$route["api/discipline/expense/submit_batch"]= "Mobile_stub_real/expense_submit_batch";
$route["api/discipline/expense/cm_approve"]  = "Mobile_stub_real/expense_cm_approve";
$route["api/discipline/bd_score"]            = "Mobile_stub_real/discipline_bd_score";
$route["api/discipline/narrative"]           = "Mobile_stub_real/discipline_narrative";
$route["api/task/check_queue"]               = "Mobile_stub_real/task_check_queue";
$route["api/task/detail"]                    = "Mobile_stub_real/task_detail";
$route["api/task/live"]                      = "Mobile_stub_real/task_live";
$route["api/task/preflight"]                 = "Mobile_stub_real/task_preflight";
$route["api/task/save_draft"]                = "Mobile_stub_real/task_save_draft";
$route["api/task/star_check"]                = "Mobile_stub_real/task_star_check";
$route["api/task/submit_closure"]            = "Mobile_stub_real/task_submit_closure";
$route["api/task/upload_attachment"]         = "Mobile_stub_real/task_upload_attachment";

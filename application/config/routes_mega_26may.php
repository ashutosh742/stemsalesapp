<?php
defined('BASEPATH') OR exit('No direct script access allowed');

// =============================================================================
// MEGA DEPLOY 26 MAY 2026 — consolidated route file
// Covers migrations 024, 025, 026, 027, 029-035, 037, 038, 039, 040, 041, 042,
// 044.1, 045, 046, 048, 049, 050, 051, 052, 054, 055, 056, 057
// =============================================================================
// Already shipped in R4: coach/*, target/*, line_manager/*, upstream_hygiene/*
// This file ONLY adds new routes. Safe to load alongside existing route files.
// =============================================================================

// ---- Migration 024: Funnel hygiene ----
$route['api/funnel_hygiene/probe']            = 'funnelhygiene/probe';
$route['api/funnel_hygiene/inbox']            = 'funnelhygiene/inbox';
$route['api/funnel_hygiene/dm_verify']        = 'Funnel_hygiene/dm_verify_queue'; // rimlyproof_hygiene_v2_20260608
$route['api/funnel_hygiene/resolve']['post']  = 'Funnel_hygiene/resolve'; // rimlyproof_hygiene_v2_20260608

// ---- Migration 025: Universal meeting lifecycle ----
$route['api/meeting_lifecycle/probe']         = 'meetinglifecycle/probe';
$route['api/meeting_lifecycle/start']['post'] = 'meetinglifecycle/start';
$route['api/meeting_lifecycle/end']['post']   = 'meetinglifecycle/end';
$route['api/meeting_lifecycle/agenda']        = 'meetinglifecycle/agenda';
$route['api/meeting_lifecycle/followup']      = 'meetinglifecycle/followup';
$route['api/universal_mom/probe']             = 'universalmom/probe';
$route['api/universal_mom/start']['post']     = 'universalmom/start';
$route['api/universal_mom/save']['post']      = 'universalmom/save';
$route['api/universal_mom/submit']['post']    = 'universalmom/submit';

// ---- Migration 026: Proposal SLA + comm orchestrator ----
$route['api/proposal/sla/probe']              = 'proposalsla/probe';
$route['api/proposal/sla/queue']              = 'proposalsla/queue';
$route['api/proposal/sla/escalate']['post']   = 'proposalsla/escalate';
$route['api/lead_query/probe']                = 'leadquery/probe';
$route['api/lead_query/checklist']            = 'leadquery/checklist';
$route['api/email_agent/probe']               = 'emailagent/probe';
$route['api/email_agent/thank_you']['post']   = 'emailagent/thank_you';

// ---- Migration 027: Comm orchestrator ----
$route['api/comm_orchestrator/probe']         = 'commorchestrator/probe';
$route['api/comm_orchestrator/inbox']         = 'commorchestrator/inbox';
$route['api/comm_orchestrator/draft']['post'] = 'commorchestrator/draft';
$route['api/stakeholder_book/probe']          = 'stakeholdermap/probe';
$route['api/stakeholder_book/list']           = 'stakeholdermap/list';

// ---- Migration 029: Photo upload + S3 ----
$route['api/photo/upload']['post']            = 'photo/upload';

// ---- Migrations 030-034: anaya_ask, card_ocr, lead_heatmap, objection_mining, stall_risk ----
$route['api/anaya_ask/probe']                 = 'anayaask/probe';
$route['api/anaya_ask/ask']['post']           = 'anayaask/ask';
$route['api/card_ocr/probe']                  = 'cardocr/probe';
$route['api/card_ocr/scan']['post']           = 'cardocr/scan';
$route['api/lead_heatmap/probe']              = 'leadheatmap/probe';
$route['api/lead_heatmap/grid']               = 'leadheatmap/grid';
$route['api/objection_mining/probe']          = 'objectionmining/probe';
$route['api/objection_mining/dump']           = 'objectionmining/dump';
$route['api/stall_risk/probe']                = 'stallrisk/probe';
$route['api/stall_risk/score']                = 'stallrisk/score';

// ---- Migration 035: Huddle MoM + fortnight review ----
$route['api/huddle_mom/probe']                = 'huddlemom/probe';
$route['api/huddle_mom/save']['post']         = 'huddlemom/save';
$route['api/fortnight_review/probe']          = 'fortnightreview/probe';
$route['api/fortnight_review/save']['post']   = 'fortnightreview/save';

// ---- Migration 037: MoM v2 agenda gate ----
$route['api/mom_v2/probe']                    = 'momv2/probe';
$route['api/mom_v2/agenda_gate']              = 'momv2/agenda_gate';
$route['api/mom_v2/voice_coverage']           = 'momv2/voice_coverage';
$route['api/mom_v2/approval_queue']           = 'momv2/approval_queue';

// ---- Migration 038: Day ceremony ----
$route['api/day_ceremony/probe']              = 'dayceremony/probe';
$route['api/day_ceremony/start']['post']      = 'dayceremony/start';
$route['api/day_ceremony/end']['post']        = 'dayceremony/end';
$route['api/day_ceremony/rollup']             = 'dayceremony/rollup';

// ---- Migration 039: Email to task ----
$route['api/email_to_task/probe']             = 'emailtotask/probe';
$route['api/email_to_task/inbox']             = 'emailtotask/inbox';
$route['api/email_to_task/convert']['post']   = 'emailtotask/convert';

// ---- Migration 040: WhatsApp agent ----
$route['api/whatsapp/probe']                  = 'whatsapp/probe';
$route['api/whatsapp/send']['post']           = 'whatsapp/send';
$route['api/whatsapp/inbox']                  = 'whatsapp/inbox';

// ---- Migration 041: CSR prospect (already routed via routes_csr_prospect.php) ----
// kept here for reference; do not duplicate
// $route['api/csr_prospect/probe'] = 'corporatecsrprospect/probe';

// ---- Migration 042: Corporate meeting prep ----
$route['api/meeting_prep/probe']              = 'meetingprep/probe';
$route['api/meeting_prep/generate']['post']   = 'meetingprep/generate';
$route['api/meeting_prep/runs']               = 'meetingprep/runs';

// ---- Migration 044.1: New lead 044.1 patches (no new routes, in-place) ----

// ---- Migration 045: Induction ----
$route['api/induction/probe']                 = 'induction/probe';
$route['api/induction/today']                 = 'induction/today';
$route['api/induction/steps']                 = 'induction/steps';
$route['api/induction/mark_done']['post']     = 'induction/mark_done';
$route['api/induction/manager_view']          = 'induction/manager_view';

// ---- Migration 046: BD request + Handover v2 ----
$route['api/bd_request/probe']                = 'bdrequest/probe';
$route['api/bd_request/list']                 = 'bdrequest/list';
$route['api/bd_request/create']['post']       = 'bdrequest/create';
$route['api/bd_request/inbox']                = 'bdrequest/inbox';
$route['api/bd_request/approve']['post']      = 'bdrequest/approve';
$route['api/bd_request/reject']['post']       = 'bdrequest/reject';
$route['api/bd_request/logs']                 = 'bdrequest/logs';
$route['api/handover_v2/probe']               = 'handoverv2/probe';
$route['api/handover_v2/list']                = 'handoverv2/list';
$route['api/handover_v2/create']['post']      = 'handoverv2/create';
$route['api/handover_v2/detail']              = 'handoverv2/detail';
$route['api/handover_v2/approve']['post']     = 'handoverv2/approve';
$route['api/handover_v2/reject']['post']      = 'handoverv2/reject';

// ---- Migration 048: Greetings ----
$route['api/greetings/probe']                 = 'greetings/probe';
$route['api/greetings/today']                 = 'greetings/today';
$route['api/greetings/dismiss']['post']       = 'greetings/dismiss';
$route['api/greetings/draft']['post']         = 'greetings/draft';
$route['api/greetings/send']['post']          = 'greetings/send';

// ---- Migration 049: Remark coherence ----
$route['api/remark_coherence/probe']          = 'remarkcoherence/probe';
$route['api/remark_coherence/check']['post']  = 'remarkcoherence/check';
$route['api/remark_coherence/blockers']       = 'remarkcoherence/blockers';

// ---- Migration 050: Pulse ----
$route['api/pulse/probe']                     = 'pulse/probe';
$route['api/pulse/health']                    = 'pulse/health';
$route['api/pulse/score']                     = 'pulse/score';

// ---- Migration 051: Role play ----
$route['api/role_play/probe']                 = 'roleplay/probe';
$route['api/role_play/start']['post']         = 'roleplay/start';
$route['api/role_play/score']['post']         = 'roleplay/score';

// ---- Migration 052: Relationship map ----
$route['api/relationship_map/probe']          = 'stakeholdermap/probe';
$route['api/relationship_map/graph']          = 'stakeholdermap/graph';

// ---- Migration 054: Stakeholder map ----
$route['api/stakeholder_map/probe']           = 'stakeholdermap/probe';
$route['api/stakeholder_map/list']            = 'stakeholdermap/list';
$route['api/stakeholder_map/update']['post']  = 'stakeholdermap/update';

// ---- Migration 055-057: Email OAuth, District intel, Card OCR routes ----
$route['api/email_oauth/probe']               = 'emailoauth/probe';
$route['api/email_oauth/start']['post']       = 'emailoauth/start';
$route['api/email_oauth/callback']            = 'emailoauth/callback';
$route['api/district_intel/probe']            = 'districtintel/probe';
$route['api/district_intel/report']           = 'districtintel/report';

// ---- Offline sync (general infra) ----
$route['api/offline_sync/probe']              = 'offlinesync/probe';
$route['api/offline_sync/pull']               = 'offlinesync/pull';
$route['api/offline_sync/push']['post']       = 'offlinesync/push';

// ---- Planner coach ----
$route['api/planner_coach/probe']             = 'plannercoach/probe';
$route['api/planner_coach/tile']              = 'plannercoach/tile';
$route['api/planner_coach/nudge']             = 'plannercoach/nudge';

// ---- CSR controller (legacy / Linkedin-based) ----
$route['api/csr/probe']                       = 'csr/probe';
$route['api/csr/quota']                       = 'csr/quota';
$route['api/csr/check']['post']               = 'csr/check';

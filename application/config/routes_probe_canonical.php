<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * routes_probe_canonical.php
 *
 * Maps every documented STEM probe endpoint to the Probes master controller.
 * Loaded LAST in routes.php so it wins over earlier conflicting definitions.
 *
 * Generated 2026-05-26 to satisfy "remove 404 get all deployed wired" directive.
 */

// 31 canonical probe endpoints
$route['api/coach/probe']              = 'probes/coach';
$route['api/target/probe']             = 'probes/target';
$route['api/line_manager/probe']       = 'probes/line_manager';
$route['api/upstream_hygiene/probe']   = 'probes/upstream_hygiene';
$route['api/funnel_hygiene/probe']     = 'probes/funnel_hygiene';
$route['api/meeting_lifecycle/probe']  = 'probes/meeting_lifecycle';
$route['api/universal_mom/probe']      = 'probes/universal_mom';
$route['api/proposal/sla/probe']       = 'probes/proposal_sla';
$route['api/proposal_sla/probe']       = 'probes/proposal_sla';
$route['api/comm_orchestrator/probe']  = 'probes/comm_orchestrator';
$route['api/comm/probe']               = 'probes/comm_orchestrator';
$route['api/anaya_ask/probe']          = 'probes/anaya_ask';
$route['api/card_ocr/probe']           = 'probes/card_ocr';
$route['api/lead_heatmap/probe']       = 'probes/lead_heatmap';
$route['api/objection_mining/probe']   = 'probes/objection_mining';
$route['api/stall_risk/probe']         = 'probes/stall_risk';
$route['api/mom_v2/probe']             = 'probes/mom_v2';
$route['api/day_ceremony/probe']       = 'probes/day_ceremony';
$route['api/email_to_task/probe']      = 'probes/email_to_task';
$route['api/whatsapp/probe']           = 'probes/whatsapp';
$route['api/csr_prospect/probe']       = 'probes/csr_prospect';
$route['api/meeting_prep/probe']       = 'probes/meeting_prep';
$route['api/induction/probe']          = 'probes/induction';
$route['api/bd_request/probe']         = 'probes/bd_request';
$route['api/handover_v2/probe']        = 'probes/handover_v2';
$route['api/greetings/probe']          = 'probes/greetings';
$route['api/remark_coherence/probe']   = 'probes/remark_coherence';
$route['api/pulse/probe']              = 'probes/pulse';
$route['api/role_play/probe']          = 'probes/role_play';
$route['api/stakeholder_map/probe']    = 'probes/stakeholder_map';
$route['api/stakeholder_book/probe']   = 'probes/stakeholder_map';
$route['api/email_oauth/probe']        = 'probes/email_oauth';
$route['api/planner_coach/probe']      = 'probes/planner_coach';
$route['api/offline_sync/probe']       = 'probes/offline_sync';

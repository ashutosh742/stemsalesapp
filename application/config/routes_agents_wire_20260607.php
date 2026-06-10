<?php
defined('BASEPATH') OR exit('No direct script access allowed');
/**
 * routes_agents_wire_20260607.php  (ADDITIVE, appended LAST)
 * Wires the 5 registry agents via the self-contained AgentRunner_api controller.
 * Production is never touched; staging only.
 */

// Universal runner: POST /api/agent/run { agent_key, params }
$route['api/agent/run']['POST'] = 'AgentRunner_api/run';
$route['api/agent/run']         = 'AgentRunner_api/run';

// MOM Drafter (app calls POST /agent/mom/draft {lead_id,transcript,template})
$route['agent/mom/draft']['POST']   = 'AgentRunner_api/mom_draft';
$route['agent/mom/draft']           = 'AgentRunner_api/mom_draft';
$route['api/mom_drafter/draft']['POST'] = 'AgentRunner_api/mom_draft';
$route['api/mom_drafter/draft']         = 'AgentRunner_api/mom_draft';

// Dump Mining
$route['api/dump_mining/run']['POST'] = 'AgentRunner_api/dump_mining';
$route['api/dump_mining/run']         = 'AgentRunner_api/dump_mining';

// War Room
$route['api/war_room/run']['POST'] = 'AgentRunner_api/war_room';
$route['api/war_room/run']         = 'AgentRunner_api/war_room';

// CM Copilot
$route['api/cm_copilot/run']['POST'] = 'AgentRunner_api/cm_copilot';
$route['api/cm_copilot/run']         = 'AgentRunner_api/cm_copilot';

// Cadence Star
$route['api/cadence_star/run']['POST'] = 'AgentRunner_api/cadence_star';
$route['api/cadence_star/run']         = 'AgentRunner_api/cadence_star';

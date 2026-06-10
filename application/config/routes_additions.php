<?php
// =============================================================================
// STEM Learning - routes.php additions
// =============================================================================
// Append these route entries to application/config/routes.php BEFORE the
// catch-all fallback (typically the last line, e.g. $route['(.+)'] = ...).
//
// Each block is gated by its migration. If a migration's PHP files are not
// copied yet, omit that block (the routes will 404 cleanly).
//
// After editing routes.php:
//   sudo systemctl reload php-fpm   (or php7.4-fpm depending on version)
//   sudo systemctl reload apache2   (only if .htaccess changed)
// =============================================================================

defined('BASEPATH') OR exit('No direct script access allowed');


// -----------------------------------------------------------------------------
// MIGRATION 019 + 019.2 - Prospecting agent
// -----------------------------------------------------------------------------
$route['api/prospect/probe']                            = 'ProspectController/probe';
$route['api/prospect/today_summary']                    = 'ProspectController/today_summary';
$route['api/prospect/today_by_bd']                      = 'ProspectController/today_by_bd';
$route['api/prospect/runs']                             = 'ProspectController/runs';
$route['api/prospect/suggest_area']                     = 'ProspectController/suggest_area';
$route['api/prospect/refresh_all']                      = 'ProspectController/refresh_all';
$route['api/prospect/accept']                           = 'ProspectController/accept';
$route['api/prospect/accept_and_seed']                  = 'ProspectController/accept_and_seed';   // 019.2
$route['api/prospect/dismiss']                          = 'ProspectController/dismiss';
$route['api/prospect/seeded_for_date']                  = 'ProspectController/seeded_for_date';   // 019.2
$route['api/prospect/seed_gap']                         = 'ProspectController/seed_gap';         // 019.2


// -----------------------------------------------------------------------------
// MIGRATION 020 - Review v2 (BD-level review discipline)
// -----------------------------------------------------------------------------
$route['api/review/probe']                              = 'ReviewV2Controller/probe';
$route['api/review/pending_for_manager']                = 'ReviewV2Controller/pending_for_manager';
$route['api/review/refresh_skip_register']              = 'ReviewV2Controller/refresh_skip_register';
$route['api/review/skip_level_dashboard']               = 'ReviewV2Controller/skip_level_dashboard';
$route['api/review/start_session']                      = 'ReviewV2Controller/start_session';
$route['api/review/save_self_assessment']               = 'ReviewV2Controller/save_self_assessment';
$route['api/review/finalize_session']                   = 'ReviewV2Controller/finalize_session';

// MIGRATION 020.1 - Monthly per-lead review
$route['api/review/monthly/generate']                   = 'MonthlyLeadReviewController/generate';
$route['api/review/monthly/list']                       = 'MonthlyLeadReviewController/list_for_audience';
$route['api/review/monthly/record_pdf']                 = 'MonthlyLeadReviewController/record_pdf';


// -----------------------------------------------------------------------------
// MIGRATION 021 - MoM v2 + CSR (LinkedIn check)
// -----------------------------------------------------------------------------
$route['api/mom_v2/probe']                              = 'MomV2Controller/probe';
$route['api/mom_v2/approval_queue']                     = 'MomV2Controller/approval_queue';
$route['api/mom_v2/save_draft']                         = 'MomV2Controller/save_draft';
$route['api/mom_v2/submit']                             = 'MomV2Controller/submit';
$route['api/mom_v2/approve']                            = 'MomV2Controller/approve';
$route['api/mom_v2/reject']                             = 'MomV2Controller/reject';

$route['api/csr/probe']                                 = 'CsrController/probe';
$route['api/csr/check']                                 = 'CsrController/check';
$route['api/csr/quota']                                 = 'CsrController/quota';


// -----------------------------------------------------------------------------
// MIGRATION 022 - Line manager scorecard + stage signoff + escalation
// -----------------------------------------------------------------------------
$route['api/line_manager/probe']                        = 'LineManagerScorecardController/probe';
$route['api/line_manager/leaderboard']                  = 'LineManagerScorecardController/leaderboard';
$route['api/line_manager/scorecard']                    = 'LineManagerScorecardController/scorecard';

$route['api/lead/signoff/queue']                        = 'StageSignoffController/queue';
$route['api/lead/signoff/request']                      = 'StageSignoffController/request_signoff';
$route['api/lead/signoff/approve']                      = 'StageSignoffController/approve';
$route['api/lead/signoff/reject']                       = 'StageSignoffController/reject';
$route['api/lead/signoff/bypass_log']                   = 'StageSignoffController/bypass_log';

$route['api/escalation/open']                           = 'Escalationticket_api/open_ticket';
$route['api/escalation/list']                           = 'Escalationticket_api/queue';
$route['api/escalation/resolve']                        = 'Escalationticket_api/resolve';

$route['api/manager_incentive/this_week']               = 'ManagerIncentiveController/this_week';
$route['api/manager_incentive/this_month']              = 'ManagerIncentiveController/this_month';


// -----------------------------------------------------------------------------
// MIGRATION 023 + 023.1/2/3 - CM activity, RM upsell, Rs 200 cr target
// -----------------------------------------------------------------------------
$route['api/cm_planner/probe']                          = 'CmPlannerController/probe';
$route['api/cm_planner/my_day']                         = 'CmPlannerController/my_day';
$route['api/cm_planner/missed_mandatory']               = 'CmPlannerController/missed_mandatory';
$route['api/cm_planner/joint_meetings']                 = 'CmPlannerController/joint_meetings';

$route['api/rm_upsell/probe']                           = 'RmUpsellController/probe';
$route['api/rm_upsell/pipeline']                        = 'RmUpsellController/pipeline';
$route['api/rm_upsell/scorecard']                       = 'RmUpsellController/scorecard';
$route['api/rm_upsell/anchor_renewals_due']             = 'RmUpsellController/anchor_renewals_due';

$route['api/target/probe']                              = 'TargetController/probe';
$route['api/target/headline']                           = 'TargetController/headline';
$route['api/target/burn_down']                          = 'TargetController/burn_down';
$route['api/target/burndown']                           = 'TargetController/burn_down';   // alias
$route['api/target/critical_gaps']                      = 'TargetController/critical_gaps';
$route['api/target/war_points']                         = 'TargetController/war_points';


// -----------------------------------------------------------------------------
// MIGRATION 024 - Funnel hygiene + DM verify
// -----------------------------------------------------------------------------
$route['api/funnel_hygiene/probe']                      = 'FunnelHygieneController/probe';
$route['api/funnel_hygiene/inbox']                      = 'FunnelHygieneController/inbox';
$route['api/funnel_hygiene/resolve']                    = 'Funnel_hygiene/resolve'; // rimlyproof_hygiene_v2_20260608
$route['api/dm_verify/check']                           = 'FunnelHygieneController/dm_verify_check';


// -----------------------------------------------------------------------------
// MIGRATION 025 - Universal meeting lifecycle
// -----------------------------------------------------------------------------
$route['api/meeting/probe']                             = 'MeetingLifecycleController/probe';
$route['api/meeting/start']                             = 'MeetingLifecycleController/start_meeting';
$route['api/meeting/end']                               = 'MeetingLifecycleController/end_meeting';
$route['api/meeting/agenda_template']                   = 'MeetingLifecycleController/agenda_template';
$route['api/meeting/followups']                         = 'MeetingLifecycleController/followups';

$route['api/universal_mom/save']                        = 'UniversalMomController/save_mom';
$route['api/universal_mom/submit']                      = 'UniversalMomController/submit_mom';
$route['api/universal_mom/by_meeting']                  = 'UniversalMomController/by_meeting';


// -----------------------------------------------------------------------------
// MIGRATION 026 - Email + SLA + lead query tracker + cadence
// -----------------------------------------------------------------------------
$route['api/proposal_sla/probe']                        = 'ProposalSlaController/probe';
$route['api/proposal_sla/queue']                        = 'ProposalSlaController/queue';
$route['api/proposal_sla/mark_sent']                    = 'ProposalSlaController/mark_sent';

$route['api/lead_query/probe']                          = 'LeadQueryController/probe';
$route['api/lead_query/checklist']                      = 'LeadQueryController/checklist';
$route['api/lead_query/answer']                         = 'LeadQueryController/answer';

$route['api/email_agent/probe']                         = 'EmailAgentController/probe';
$route['api/email_agent/draft_thank_you']               = 'EmailAgentController/draft_thank_you';
$route['api/email_agent/draft_cadence']                 = 'EmailAgentController/draft_cadence';
$route['api/email_agent/send']                          = 'EmailAgentController/send_email';


// -----------------------------------------------------------------------------
// MIGRATION 027 - Comm orchestrator + stakeholder contact book
// -----------------------------------------------------------------------------
$route['api/comm/probe']                                = 'CommOrchestratorController/probe';
$route['api/comm/inbox']                                = 'CommOrchestratorController/inbox';
$route['api/comm/timeline']                             = 'CommOrchestratorController/timeline';
$route['api/comm/draft']                                = 'CommOrchestratorController/draft';
$route['api/comm/send']                                 = 'CommOrchestratorController/send';

$route['api/stakeholder/list']                          = 'StakeholderContactController/list_contacts';
$route['api/stakeholder/save']                          = 'StakeholderContactController/save_contact';
$route['api/stakeholder/by_lead']                       = 'StakeholderContactController/by_lead';


// -----------------------------------------------------------------------------
// MIGRATION 028 - Rs 200 cr target cascade + war points dashboard
// (Most routes overlap with mig 023 TargetController. Add only new ones below.)
// -----------------------------------------------------------------------------
$route['api/target/cascade/refresh']                    = 'TargetController/cascade_refresh';
$route['api/target/cascade/set']                        = 'TargetController/cascade_set';
$route['api/target/weekly_checkin']                     = 'TargetController/weekly_checkin';
$route['api/target/discipline_score']                   = 'TargetController/discipline_score';


// -----------------------------------------------------------------------------
// MIGRATION 029 - Prospecting Discipline Audit (photo + GPS + day-shape)
// -----------------------------------------------------------------------------
$route['api/prospecting_discipline/probe']              = 'ProspectingDisciplineController/probe';
$route['api/prospecting_discipline/refresh_daily']      = 'ProspectingDisciplineController/refresh_daily';
$route['api/prospecting_discipline/yesterday']          = 'ProspectingDisciplineController/yesterday';
$route['api/prospecting_discipline/event_audit']        = 'ProspectingDisciplineController/event_audit';
$route['api/prospecting_discipline/weekly']             = 'ProspectingDisciplineController/weekly';
$route['api/prospecting_discipline/spoof_log']          = 'ProspectingDisciplineController/spoof_log';


// -----------------------------------------------------------------------------
// AI lead score (advanced features migration)
// -----------------------------------------------------------------------------
$route['api/ai_lead_score/probe']                       = 'AiLeadScoreController/probe';
$route['api/ai_lead_score/compute']                     = 'AiLeadScoreController/compute';
$route['api/ai_lead_score/top']                         = 'AiLeadScoreController/top';


// -----------------------------------------------------------------------------
// Planner coach
// -----------------------------------------------------------------------------
$route['api/planner_coach/probe']                       = 'PlannerCoachController/probe';
$route['api/planner_coach/today']                       = 'PlannerCoachController/today';



// -----------------------------------------------------------------------------
// MIGRATION 023 - Mobile target form endpoints (Agent 1, 2026-05-26)
// -----------------------------------------------------------------------------
$route['api/target/set_quarterly_target'] = 'TargetController/set_quarterly_target';
$route['api/target/set_daily_goal']       = 'TargetController/set_daily_goal';

// END routes additions

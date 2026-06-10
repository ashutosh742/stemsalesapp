<?php
// ParityV28 routes - eye-opener parity features
// Loaded AFTER stubs and AFTER agent routes so it can claim its own namespace cleanly.

// 1) AI lead scoring
$route['api/ai/lead_score/probe']                  = 'ParityV28/lead_score_probe';
$route['api/ai/lead_score/for/(:num)']             = 'ParityV28/lead_score_for/$1';
$route['api/ai/lead_score/top']                    = 'ParityV28/lead_score_top';
$route['api/ai/lead_score/top/(:num)']             = 'ParityV28/lead_score_top/$1';
$route['api/ai/next_action/(:num)']                = 'ParityV28/ai_next_action/$1';

// 2) Discount approval
$route['api/discount/probe']                       = 'ParityV28/discount_probe';
$route['api/discount/pending']                     = 'ParityV28/discount_pending';
$route['api/discount/pending/(:num)']              = 'ParityV28/discount_pending/$1';
$route['api/discount/thresholds']                  = 'ParityV28/discount_thresholds';

// 3) Multi-language
$route['api/lang/list']                            = 'ParityV28/lang_list';
$route['api/lang/strings']                         = 'ParityV28/lang_strings';
$route['api/lang/strings/(:any)']                  = 'ParityV28/lang_strings/$1';

// 4) Multi-currency
$route['api/currency/list']                        = 'ParityV28/currency_list';

// 5) Custom field engine
$route['api/custom_field/list']                    = 'ParityV28/custom_field_list';
$route['api/custom_field/list/(:any)']             = 'ParityV28/custom_field_list/$1';
$route['api/custom_field/get/(:any)/(:num)']       = 'ParityV28/custom_field_get/$1/$2';

// 6) Sandbox marker
$route['api/sandbox/probe']                        = 'ParityV28/sandbox_probe';

// 7) Notification prefs (quiet hours)
$route['api/notification_prefs/get']               = 'ParityV28/notification_prefs_get';
$route['api/notification_prefs/get/(:num)']        = 'ParityV28/notification_prefs_get/$1';

// 8) Field service (post-sale)
$route['api/field_service/probe']                  = 'ParityV28/field_service_probe';
$route['api/field_service/tickets']                = 'ParityV28/field_service_tickets';

// 9) E-signature
$route['api/esign/probe']                          = 'ParityV28/esign_probe';
$route['api/esign/pending']                        = 'ParityV28/esign_pending';
$route['api/esign/pending/(:num)']                 = 'ParityV28/esign_pending/$1';

// 10) Lead routing - round robin
$route['api/lead_routing/probe']                   = 'ParityV28/lead_routing_probe';
$route['api/lead_routing/next_bd']                 = 'ParityV28/lead_routing_next_bd';
$route['api/lead_routing/next_bd/(:num)']          = 'ParityV28/lead_routing_next_bd/$1';

// Bonus eye-opener endpoints (read-only)
$route['api/forecast/summary']                     = 'ParityV28/forecast_summary';
$route['api/forecast/summary/(:any)']              = 'ParityV28/forecast_summary/$1';
$route['api/duplicate/detect/(:num)']              = 'ParityV28/duplicate_detect/$1';
$route['api/coverage_ratio']                       = 'ParityV28/coverage_ratio';
$route['api/coverage_ratio/(:num)']                = 'ParityV28/coverage_ratio/$1';
$route['api/cohort/trends']                        = 'ParityV28/cohort_trends';
$route['api/cohort/trends/(:num)']                 = 'ParityV28/cohort_trends/$1';
$route['api/team_live_map/list']                   = 'ParityV28/team_live_map';
$route['api/gamification/badges/(:num)']           = 'ParityV28/gamification_badges/$1';
$route['api/email/open_track/(:num)']              = 'ParityV28/email_open_track/$1';
$route['api/calendar_sync/status/(:num)']          = 'ParityV28/calendar_sync_status/$1';
$route['api/slack/webhook_status']                 = 'ParityV28/slack_webhook_status';
$route['api/audit/field_history/(:any)/(:num)']    = 'ParityV28/audit_field_history/$1/$2';
$route['api/sla/overdue']                          = 'ParityV28/sla_overdue';
$route['api/incentive/summary/(:num)']             = 'ParityV28/incentive_summary/$1';

// Self-probe
$route['api/parity_v28/probe']                     = 'ParityV28/probe';

<?php
/**
 * routes_review_v2.php - Review v2 API routes (Migration 020)
 * Deployed by agent2_review_session build.
 * Loaded first in routes.php to take precedence.
 *
 * Controllers:
 *   ReviewV2Controller -> probe
 *   Review_api -> pending_self_assessment, submit_self_assessment, pending_for_manager,
 *                 manager_complete, monthly_list, monthly_generate, refresh_skip_register,
 *                 skip_level_dashboard
 */
defined('BASEPATH') OR exit('No direct script access allowed');

// Probe - wired to ReviewV2Controller which has probe() method
$route['api/review/probe']                    = 'Review_api/probe';

// BD self-assessment (NEW - Migration 020 v2)
$route['api/review/pending_self_assessment']  = 'Review_api/pending_self_assessment';
$route['api/review/submit_self_assessment']   = 'Review_api/submit_self_assessment';

// Manager session (NEW - Migration 020 v2)
$route['api/review/pending_for_manager']      = 'Review_api/pending_for_manager';
$route['api/review/manager_complete']         = 'Review_api/manager_complete';

// Monthly list/generate
$route['api/review/monthly/list']             = 'Review_api/monthly_list';
$route['api/review/monthly/generate']         = 'Review_api/monthly_generate';

// Cron / admin
$route['api/review/refresh_skip_register']    = 'Review_api/refresh_skip_register';
$route['api/review/skip_level_dashboard']     = 'Review_api/skip_level_dashboard';

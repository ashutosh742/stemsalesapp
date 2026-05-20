<?php
/**
 * Mobile API routes - paste these lines into application/config/routes.php
 * on production cPanel, before the default $route['default_controller'] line.
 *
 * Created on feature/mobile-api-endpoints branch, 2026-05-20.
 *
 * Endpoints:
 *   GET  /api/day_pack    -> AnayaAsk::api_day_pack
 *   POST /api/draft       -> MomDraft::api_draft
 *   POST /api/login       -> MobileAuth::api_login
 *   GET  /api/session     -> MobileAuth::api_session
 *   POST /api/run_tool    -> Coach::api_run_tool
 */

$route['api/day_pack']         = 'anayaask/api_day_pack';
$route['api/draft']['post']    = 'momdraft/api_draft';
$route['api/login']['post']    = 'mobileauth/api_login';
$route['api/session']['get']   = 'mobileauth/api_session';
$route['api/run_tool']['post'] = 'coach/api_run_tool';

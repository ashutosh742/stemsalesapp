<?php
/**
 * JSON mobile endpoint routes - load this from routes.php on cPanel by adding
 *   require_once APPPATH.'config/routes_json_endpoints.php';
 * before the $route['default_controller'] line. Or copy the lines below
 * directly into the main routes.php.
 *
 * Created on feature/json-mobile-endpoints branch, 2026-05-20.
 *
 * These routes wire the 4 new controllers (Anaya_reports, MomController,
 * Menu, Chat) into the URL paths that the mobile client at
 * mobile/src/api/client.js expects. They coexist with the older /api/*
 * routes in routes_mobile_api.php (MobileAuth + MomDraft + Coach +
 * AnayaAsk Bearer-token variant).
 *
 * Endpoints:
 *   GET  /Anaya_reports/api_day_pack   -> Anaya_reports::api_day_pack
 *   POST /MomController/api_draft      -> MomController::api_draft
 *   POST /Menu/login                   -> Menu::api_login   (legacy alias)
 *   POST /Menu/api_login               -> Menu::api_login
 *   GET  /Menu/api_session             -> Menu::api_session
 *   POST /Menu/api_logout              -> Menu::api_logout
 *   POST /chat/api_run_tool            -> Chat::api_run_tool
 */

$route['Anaya_reports/api_day_pack']         = 'anaya_reports/api_day_pack';
$route['anaya_reports/api_day_pack']         = 'anaya_reports/api_day_pack';

$route['MomController/api_draft']['post']    = 'momcontroller/api_draft';
$route['momcontroller/api_draft']['post']    = 'momcontroller/api_draft';

$route['Menu/login']['post']                 = 'menu/api_login';
$route['Menu/api_login']['post']             = 'menu/api_login';
$route['Menu/api_session']['get']            = 'menu/api_session';
$route['Menu/api_logout']['post']            = 'menu/api_logout';
$route['menu/login']['post']                 = 'menu/api_login';
$route['menu/api_login']['post']             = 'menu/api_login';
$route['menu/api_session']['get']            = 'menu/api_session';
$route['menu/api_logout']['post']            = 'menu/api_logout';

$route['chat/api_run_tool']['post']          = 'chat/api_run_tool';
$route['Chat/api_run_tool']['post']          = 'chat/api_run_tool';

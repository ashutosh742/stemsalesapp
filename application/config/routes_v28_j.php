<?php
defined('BASEPATH') OR exit('No direct script access allowed');

// ============================================================
// Agent J real routes - auth group + m047 group (29 May 2026)
// Controllers: v28/AuthV28, v28/M047V28
// ============================================================

// --- auth group ---
$route['api/auth/me']           = 'v28/AuthV28/me';
$route['api/auth/api_me']       = 'v28/AuthV28/api_me';
$route['api/auth/login']        = 'v28/AuthV28/login';
$route['api/auth/request_otp']  = 'v28/AuthV28/request_otp';

// --- m047 group ---
$route['api/m047/dashboard']    = 'v28/M047V28/dashboard';
$route['api/m047/today']        = 'v28/M047V28/today';
$route['api/m047/task/today']   = 'v28/M047V28/task_today';

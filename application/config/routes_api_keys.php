<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * API Keys admin routes
 * Wired 2026-05-27 by credential wiring agent.
 *
 * POST /api/api_keys/set  -> Api_keys::set()
 * GET  /api/api_keys/list -> Api_keys::list()
 */
$route['api/api_keys/set']  = 'api_keys/set';
$route['api/api_keys/list'] = 'api_keys/list';

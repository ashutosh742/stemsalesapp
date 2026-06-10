<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * routes_data_wire.php  -  2026-06-04
 *
 * Mobile app v2.11.0 calls these paths but they 404 or hit auto-routed empty stubs.
 * This file aliases them to working backend controllers via a small shim controller.
 *
 * Additive only. Production stemapp.in does NOT load this file (only present on
 * selfstaging via routes.php conditional include).
 *
 * Wires:
 *   GET  /api/leads/list?bd_uid=X    -> DataWireShim/leads_list      (remaps bd_uid -> uid)
 *   GET  /api/leads/detail?lead_id=X -> DataWireShim/leads_detail    (remaps to /api/lead/detail/{id})
 *   GET  /api/auto_tasks/list?uid=X  -> Task_api/auto_tasks          (direct alias)
 *   GET  /api/auto_tasks/today?uid=X -> Task_api/auto_tasks          (alias of above)
 *   GET  /api/task/auto_tasks?uid=X  -> Task_api/auto_tasks          (alias)
 */

$route['api/leads/list']        = 'DataWireShim/leads_list';
$route['api/leads/detail']      = 'DataWireShim/leads_detail';
$route['api/auto_tasks/list']   = 'Task_api/auto_tasks';
$route['api/auto_tasks/today']  = 'Task_api/auto_tasks';
$route['api/task/auto_tasks']   = 'Task_api/auto_tasks';

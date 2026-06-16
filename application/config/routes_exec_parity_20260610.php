<?php
defined('BASEPATH') OR exit('No direct script access allowed');
/* EXEC-PARITY 20260610 (additive, very last include) */
$route['api/task/action_schema']['get']    = 'Mobile_write_api/task_action_schema';
$route['api/task/stage_write']['post']     = 'Mobile_write_api/stage_write';
$route['api/task/stage_attachment']['post']= 'Mobile_write_api/stage_attachment';
$route['api/task/delay_remarks']['post']   = 'Mobile_write_api/delay_remarks';

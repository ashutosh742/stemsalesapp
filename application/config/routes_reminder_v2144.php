<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/* === reminder endpoints for v2144 mobile parity (additive, last-include-wins) ===
 * New self-service reminder surface backed by app_reminder. Strictly additive;
 * mapped to the Reminder_api controller (controllers root, same pattern as the
 * existing AnnualReview_api and Pst routes) so they sit alongside the other
 * mobile endpoints.
 */
$route['api/reminder/list']   = 'Reminder_api/list_reminders';
$route['api/reminder/create'] = 'Reminder_api/create_reminder';
$route['api/reminder/ack']    = 'Reminder_api/ack_reminder';

<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * routes_agent3_comm.php
 *
 * Agent 3 - Comm Inbox + Stakeholder routes.
 * Migration 027 (Contact book) + 039 (EmailToTask) + 040 (WhatsApp).
 *
 * Contact controller placed in root controllers dir (not api/ subdir)
 * to match CI3 routing conventions used by all other controllers here.
 *
 * Created: 26 May 2026
 */

// --- Contact book (per-school stakeholder contacts) ---
$route['api/contact/probe']               = 'Contact/probe';
$route['api/contact/list']                = 'Contact/list_contacts';
$route['api/contact/add']                 = 'Contact/add';
$route['api/contact/edit']                = 'Contact/edit';
$route['api/contact/delete']              = 'Contact/delete';

// --- Comm inbox (draft queue for BD) - additional paths ---
$route['api/comm/send']                   = 'CommOrchestratorController/draft_send';
$route['api/comm/timeline/(:any)']        = 'CommOrchestratorController/timeline/$1';

// --- EmailToTask (email_task shortform path) ---
$route['api/email_task/probe']            = 'EmailToTask/probe';
$route['api/email_task/parse']            = 'EmailToTask/convert';
$route['api/email_task/inbox']            = 'EmailToTask/inbox';

// --- WhatsApp templates endpoint ---
$route['api/whatsapp/templates']          = 'Whatsapp/queue';

// --- Comm inbox + draft list (added 27 May 2026) ---
$route['api/comm/draft/list']             = 'CommOrchestratorController/draft_list';
$route['api/comm/inbox']                  = 'CommOrchestratorController/inbox';

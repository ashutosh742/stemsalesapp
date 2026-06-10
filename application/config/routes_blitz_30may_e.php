<?php

// blitz patch: defuse prior string assignments so http-verb subscript works
if (isset($route['api/comm/inbox']) && is_string($route['api/comm/inbox'])) { unset($route['api/comm/inbox']); }
if (isset($route['api/csr_prospect/list']) && is_string($route['api/csr_prospect/list'])) { unset($route['api/csr_prospect/list']); }
if (isset($route['api/email/send']) && is_string($route['api/email/send'])) { unset($route['api/email/send']); }
if (isset($route['api/email_to_task/probe']) && is_string($route['api/email_to_task/probe'])) { unset($route['api/email_to_task/probe']); }
if (isset($route['api/email_to_task/queue']) && is_string($route['api/email_to_task/queue'])) { unset($route['api/email_to_task/queue']); }
if (isset($route['api/email_to_task/submit']) && is_string($route['api/email_to_task/submit'])) { unset($route['api/email_to_task/submit']); }
if (isset($route['api/email_to_task/triage']) && is_string($route['api/email_to_task/triage'])) { unset($route['api/email_to_task/triage']); }
if (isset($route['api/handover/list']) && is_string($route['api/handover/list'])) { unset($route['api/handover/list']); }
if (isset($route['api/stakeholder/book']) && is_string($route['api/stakeholder/book'])) { unset($route['api/stakeholder/book']); }
if (isset($route['api/wallet/balance']) && is_string($route['api/wallet/balance'])) { unset($route['api/wallet/balance']); }
if (isset($route['api/wallet/history']) && is_string($route['api/wallet/history'])) { unset($route['api/wallet/history']); }

/**
 * Routes - Agent E, Blitz 30 May 2026
 *
 * Using string (method-agnostic) format so these definitions
 * OVERWRITE any earlier string definitions from mega_26may, agent6, etc.
 * Auth enforcement and HTTP method validation is done inside each controller.
 *
 * Endpoints:
 *   GET  /api/handover/list
 *   POST /api/email/send
 *   GET  /api/wallet/balance
 *   GET  /api/wallet/history
 *   GET  /api/stakeholder/book
 *   GET  /api/csr_prospect/list
 *   GET  /api/comm/inbox
 *   POST /api/email_to_task/submit
 *   GET  /api/email_to_task/queue
 *   POST /api/email_to_task/triage
 *   GET  /api/email_to_task/probe
 */

// --- Handover ---
$route['api/handover/list']            = 'HandoverList/list_index';

// --- Email send (queue only, no SMTP) ---
$route['api/email/send']               = 'EmailSend/send_index';

// --- Wallet ---
$route['api/wallet/balance']           = 'WalletApi/balance_index';
$route['api/wallet/history']           = 'WalletApi/history_index';

// --- Stakeholder contact book ---
$route['api/stakeholder/book']         = 'StakeholderBook/book_index';

// --- CSR prospect list ---
$route['api/csr_prospect/list']        = 'CsrProspect/list_index';

// --- Communication inbox ---
$route['api/comm/inbox']               = 'CommInbox/inbox_index';

// --- Email-to-Task (headline feature) ---
$route['api/email_to_task/submit']     = 'EmailToTask_api/submit_index';
$route['api/email_to_task/queue']      = 'EmailToTask_api/queue_index';
$route['api/email_to_task/triage']     = 'EmailToTask_api/triage_index';
$route['api/email_to_task/probe']      = 'EmailToTask_api/probe_index';

<?php
defined('BASEPATH') OR exit('No direct script access allowed');
/* =====================================================================
 * STEM Meeting-to-Money (M2M) Assurance routes - 2026-06-16
 * ADDITIVE, READ-SAFE. New literal routes for the 3 new gate controllers
 * plus the guarded MoM approve endpoint.
 *
 * ORDER NOTE (important): this fragment is included from routes.php BEFORE
 * routes_parity_closeout_20260611.php. At include time the StubController
 * catch-all (api/(:any)[/(:any)...]) is NOT yet in the $route table, so
 * these literal keys are inserted first. CI3 matches $route in INSERTION
 * order, first match wins, so every literal below is reached before the
 * catch-all that parity_closeout appends afterwards. This mirrors the
 * literal-beats-catch-all pattern used by the dated fix fragments.
 *
 * No existing route is modified. No existing controller method is touched.
 * New controllers only: M2mGateA / M2mGateB / M2mGateC / M2mMomGuard.
 * ===================================================================== */

// ---- Gate A: Meeting Quality (M2mGateA / class M2m_gate_a) ----
$route['api/m2m/gatea/probe']        = 'M2mGateA/probe';
$route['api/m2m/gatea/capture']['post'] = 'M2mGateA/capture';
$route['api/m2m/gatea/grade']['post']   = 'M2mGateA/grade';
$route['api/m2m/gatea/check']        = 'M2mGateA/check';
$route['api/m2m/gatea/quality_log']  = 'M2mGateA/quality_log';

// ---- Gate B: Proposal Commitment SLA (M2mGateB / class M2m_gate_b) ----
$route['api/m2m/gateb/probe']             = 'M2mGateB/probe';
$route['api/m2m/gateb/committed_not_sent'] = 'M2mGateB/committed_not_sent';
$route['api/m2m/gateb/mark_sent']['post']  = 'M2mGateB/mark_sent';

// ---- Gate C: Manager Closure Ownership (M2mGateC / class M2m_gate_c) ----
$route['api/m2m/gatec/probe']      = 'M2mGateC/probe';
$route['api/m2m/gatec/touch']['post'] = 'M2mGateC/touch';
$route['api/m2m/gatec/adherence'] = 'M2mGateC/adherence';
$route['api/m2m/gatec/scorecard'] = 'M2mGateC/scorecard';

// ---- Guarded MoM approve (M2mMomGuard / class M2m_mom_guard) ----
// Wraps the Gate A mandatory-field check, then delegates to the existing,
// unchanged MomV2_model::approve. Existing approve endpoints are untouched.
$route['api/m2m/mom/approve_guarded']['post'] = 'M2mMomGuard/approve_guarded';

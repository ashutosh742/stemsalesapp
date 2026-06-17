<?php
defined('BASEPATH') OR exit('No direct script access allowed');
/* =====================================================================
 * Full-app stub closeout - 2026-06-17 (ADDITIVE, READ-SAFE)
 * ---------------------------------------------------------------------
 * Root cause: a full audit of all 346 app-called /api paths on staging
 * found exactly 6 that still returned the StubController catch-all JSON
 * ({"ok":true,"stub":true,...}) from routes_parity_closeout_20260611.php.
 * In every case the REAL controller method already exists; the stub fired
 * only because the app calls a path shape or HTTP method that was never
 * routed literally, so it fell through to api/(:any) = StubController/handle.
 *
 * This fragment maps the EXACT path + method the app calls to the existing
 * real controller method. No new controller is added; the four uid/id
 * controllers were given an additive query/auth fallback so they keep
 * working both with the uri arg (existing /(:num) routes) and without it.
 *
 * The 6 closed endpoints:
 *   1. GET  /api/dashboard/cm        -> DashboardCmController/index
 *        (uid from ?cm_uid= / ?uid= / authenticated user when uri arg absent)
 *   2. GET  /api/planning_grade/bd   -> v28/PlanningGradeV28/bd
 *        (uid from ?uid= / ?bd_uid= / authenticated user when uri arg absent)
 *   3. GET  /api/lead/detail         -> LeadDetailController/index
 *        (lead id from ?id= / ?cid= when uri arg absent)
 *   4. GET+POST /api/email_agent/draft -> Email_agent/draft
 *        (draft id from ?id= / ?draft_id= / ?cid=; always HTTP 200, no 5xx)
 *   5. GET  /api/cti/click_to_call   -> M067_auto_cti_call_logging/click_to_call
 *        (previously POST-only; POST route kept intact in parity_closeout)
 *   6. GET  /api/cti/manual_link     -> M067_auto_cti_call_logging/manual_link
 *        (previously POST-only; POST route kept intact in parity_closeout)
 *
 * ORDER NOTE: CI3 matches $route in INSERTION order, first match wins.
 * routes_parity_closeout already inserted the StubController catch-alls
 * (api/(:any)[/(:any)...]). A literal added now is a NEW key, so it would
 * append AFTER those catch-alls and lose. To make the literals win we add
 * them first, then unset + re-add the catch-alls so they move to the very
 * end of the route table. Re-adding identical values keeps behavior byte
 * identical for every other path; only ordering changes, and only so these
 * six real endpoints are reached before the stub. This is the same pattern
 * used by routes_cm_daymgmt_fix_20260616.php and
 * routes_roleplay_wireup_20260617.php. This fragment is included LAST in
 * routes.php (after the role-play wireup include).
 *
 * Never touches the existing /(:num) routes. No production impact.
 * ASCII only. No em-dashes. Rs spelled out where money is shown. percent
 * spelled out.
 * ===================================================================== */

// 1. CM dashboard, no uri uid (controller resolves cm_uid/uid/auth).
$route['api/dashboard/cm'] = 'DashboardCmController/index';

// 2. Planning grade for a BD, no uri uid (controller resolves uid/auth).
$route['api/planning_grade/bd'] = 'v28/PlanningGradeV28/bd';

// 3. Lead detail, lead id sent as query param (controller reads ?id= / ?cid=).
$route['api/lead/detail'] = 'LeadDetailController/index';

// 4. Generic email draft lookup, no uri id, both GET and POST.
//    Targets Email_agent (file Email_agent.php -> EmailAgentController.php).
$route['api/email_agent/draft']['get']  = 'Email_agent/draft';
$route['api/email_agent/draft']['post'] = 'Email_agent/draft';

// 5 + 6. CTI endpoints the app calls as GET. The POST routes already exist in
//        routes_parity_closeout_20260611.php and stay untouched; we only add
//        the GET verb so both methods reach the same real controller method.
$route['api/cti/click_to_call']['get'] = 'M067_auto_cti_call_logging/click_to_call';
$route['api/cti/manual_link']['get']   = 'M067_auto_cti_call_logging/manual_link';

// Re-float the StubController catch-alls to the very end so all the literals
// above (and every earlier real route) are matched first. Same targets as
// routes_parity_closeout_20260611.php; re-adding identical values only moves
// them to the end of the table and is otherwise behavior-neutral.
foreach (array(
    'api/(:any)',
    'api/(:any)/(:any)',
    'api/(:any)/(:any)/(:any)',
    'api/(:any)/(:any)/(:any)/(:any)',
) as $__catchall) {
    if (isset($route[$__catchall])) {
        unset($route[$__catchall]);
    }
    $route[$__catchall] = 'StubController/handle';
}
unset($__catchall);

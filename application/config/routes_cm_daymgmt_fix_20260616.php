<?php
defined('BASEPATH') OR exit('No direct script access allowed');
/* =====================================================================
 * CM Day Management fix - 2026-06-16 (ADDITIVE, READ-SAFE)
 * ---------------------------------------------------------------------
 * Root cause: the mobile DayManagementScreen's primary feed calls
 * /api/planner/pbni_list. No real route matched it, so the catch-all in
 * routes_parity_closeout_20260611.php sent it to StubController/handle,
 * which returns {ok:true,stub:true,rows:[]} with no DB read. The screen
 * bound an empty, non-clickable list and looked dead ("nothing works").
 *
 * Fix: route the LITERAL path to the REAL method PlannerV28::pbni_list,
 * which UNIONs the same real sources the working endpoints already use
 * (pending_carry + auto_seeded + this CM's cm_daily_plan pending/approved
 * rows). This fragment is included AFTER routes_parity_closeout (last-
 * include-wins), so this literal is matched before the stub catch-all.
 *
 * Mirrors the include pattern of the other dated fix fragments. No new
 * controller, no production impact, no change to any working route.
 *
 * ORDER NOTE (important): CI3 matches $route in INSERTION order, first regex
 * wins. routes_parity_closeout already inserted the StubController catch-alls
 * (api/(:any)[/(:any)...]). A literal added now is a NEW key, so it would
 * append AFTER those catch-alls and lose. To make the literal win we add it
 * first, then unset+re-add the catch-alls so they move to the very end of the
 * route table. Re-adding identical values keeps behavior byte-identical for
 * every other path; only ordering changes, and only so the real endpoint is
 * reached before the stub.
 * ===================================================================== */

// CM Day Management primary feed - real plan-but-not-initiated list.
$route['api/planner/pbni_list'] = 'v28/PlannerV28/pbni_list';

// Re-float the StubController catch-alls to the end so the literal above (and
// every earlier real route) is matched first. Same targets as parity_closeout.
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

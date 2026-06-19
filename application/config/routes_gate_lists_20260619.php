<?php
defined('BASEPATH') OR exit('No direct script access allowed');
/* =====================================================================
 * Gate-hardening list routes - 2026-06-19 (ADDITIVE, READ-SAFE)
 * ---------------------------------------------------------------------
 * Three discipline gates counted blocking items but had NO endpoint that
 * returned those exact rows, so the mobile screens they route to could not
 * render an actionable list (the same dead-end class as the autotask gate):
 *   update_research -> GET /api/planner/research_pending
 *   write_mom       -> GET /api/planner/pending_moms
 *   fill_expense    -> GET /api/planner/expense_pending
 *
 * Each maps to a NEW read-only PlannerV28 method whose WHERE clause mirrors the
 * matching DisciplineState_model count query byte-for-byte, so the list a
 * screen shows always equals the gate count (no count/list mismatch).
 *
 * pending_moms note: the LITERAL api/planner/pending_moms is mapped here to
 * v28/PlannerV28/pending_moms (the gate-true mirror). A different, non-gate
 * endpoint FunnelReportController::pending_moms exists at api/funnel_report/
 * pending_moms and is untouched.
 *
 * ORDER NOTE: CI3 matches $route in INSERTION order, first match wins. Earlier
 * fragments insert the StubController catch-alls (api/(:any)[/(:any)...]). A
 * literal added now is a NEW key, so it would append AFTER those catch-alls and
 * lose. To make the literals win we add them first, then unset + re-add the
 * catch-alls so they move to the very end of the route table. Re-adding
 * identical values keeps behavior byte-identical for every other path; only
 * ordering changes. Same pattern as routes_autotask_list_20260618.php. Included
 * LAST in routes.php (after the autotask list include), so these literals are
 * the final definitions for their paths.
 *
 * Never touches the existing /(:num) routes. No production impact.
 * ASCII only. No em-dashes.
 * ===================================================================== */

// Gate-counted row lists - exact gate WHERE clauses (read-only).
$route['api/planner/research_pending'] = 'v28/PlannerV28/research_pending';
$route['api/planner/pending_moms']     = 'v28/PlannerV28/pending_moms';
$route['api/planner/expense_pending']  = 'v28/PlannerV28/expense_pending';

// Re-float the StubController catch-alls to the very end so the literals above
// (and every earlier real route) are matched first. Same targets as the
// earlier fragments; re-adding identical values only moves them to the end of
// the table and is otherwise behavior-neutral.
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

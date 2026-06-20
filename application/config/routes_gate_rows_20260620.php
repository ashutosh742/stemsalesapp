<?php
defined('BASEPATH') OR exit('No direct script access allowed');
/* =====================================================================
 * Discipline gate-row routes - 2026-06-20 (ADDITIVE, READ-ONLY)
 * ---------------------------------------------------------------------
 * Root cause: write_mom / fill_expense / update_research gates counted rows
 * via DisciplineState_model but had NO endpoint returning the EXACT rows the
 * count counts (the pre-existing pending_moms / expense_list use different
 * WHERE clauses). clear_pbni and clear_autotask already have contract-true
 * row endpoints. These three literals route to DisciplineGateRows, whose
 * queries mirror the gate count WHERE clauses EXACTLY so count == gate count.
 *
 * Same last-wins ordering trick as routes_autotask_list_20260618.php: add the
 * literals, then re-float the StubController catch-alls to the end so these
 * literals are matched before the api/(:any) stub. Behavior-neutral for every
 * other path. Included LAST in routes.php. ASCII only. No em-dashes.
 * ===================================================================== */

$route['api/discipline/mom_pending']      = 'DisciplineGateRows/mom_pending';
$route['api/discipline/expense_pending']  = 'DisciplineGateRows/expense_pending';
$route['api/discipline/research_pending'] = 'DisciplineGateRows/research_pending';

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

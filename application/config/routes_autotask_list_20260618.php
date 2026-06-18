<?php
defined('BASEPATH') OR exit('No direct script access allowed');
/* =====================================================================
 * Pending auto-task list route - 2026-06-18 (ADDITIVE, READ-SAFE)
 * ---------------------------------------------------------------------
 * Root cause: the clear_autotask discipline gate counts pending auto-tasks
 * via DisciplineState_model::get_pending_autotask_count (tblcallevents
 * nextCFID=0, autotask=1, plan=1, appointmentdatetime < today). No endpoint
 * returned those exact rows, so the mobile DayManagementScreen bounced the
 * user to the read-only telemetry AutoTasksScreen (today's captured comms),
 * which is empty and unrelated. Dead-end: the user could not clear the gate.
 *
 * Fix: route the LITERAL path /api/planner/pending_autotasks to the new
 * read-only method PlannerV28::pending_autotasks, which SELECTs the exact
 * gate-counted rows joined to init_call + company_master for lead/company
 * names so the screen can render and tap-through to the follow-up flow.
 *
 * ORDER NOTE: CI3 matches $route in INSERTION order, first match wins.
 * routes_parity_closeout_20260611.php (and later fragments) insert the
 * StubController catch-alls (api/(:any)[/(:any)...]). A literal added now is
 * a NEW key, so it would append AFTER those catch-alls and lose. To make the
 * literal win we add it first, then unset + re-add the catch-alls so they move
 * to the very end of the route table. Re-adding identical values keeps behavior
 * byte-identical for every other path; only ordering changes, and only so the
 * real endpoint is reached before the stub. Same pattern as
 * routes_cm_daymgmt_fix_20260616.php and routes_stub_closeout_20260617.php.
 * This fragment is included LAST in routes.php (after the stub closeout
 * include), so this literal is the final definition for the path.
 *
 * Never touches the existing /(:num) routes. No production impact.
 * ASCII only. No em-dashes.
 * ===================================================================== */

// Pending auto-task list - exact gate-counted rows (read-only).
$route['api/planner/pending_autotasks'] = 'v28/PlannerV28/pending_autotasks';

// Re-float the StubController catch-alls to the very end so the literal above
// (and every earlier real route) is matched first. Same targets as
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

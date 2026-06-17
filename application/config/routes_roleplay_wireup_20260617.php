<?php
defined('BASEPATH') OR exit('No direct script access allowed');
/* =====================================================================
 * Role-Play turn wireup - 2026-06-17 (ADDITIVE, READ-SAFE)
 * ---------------------------------------------------------------------
 * Root cause: /api/role_play/reply and /api/role_play/end had NO literal
 * route. The catch-all StubController routes (api/(:any)[/(:any)...]) in
 * routes_parity_closeout_20260611.php therefore matched them and returned
 * {ok:true,stub:true,note:"endpoint stub - not built yet (migration
 * pending)"}. So a session could /start but the AI never replied and the
 * session never scored - broken for every role.
 *
 * Fix: route the LITERAL paths to the REAL controller actions
 * RolePlayV28::reply and RolePlayV28::end, which load the existing
 * RolePlay_model (alias of RolePlay_agent) and call post_turn / end_session
 * (the same turn + scoring logic that already exists). RolePlayV28 is the
 * SAME controller that serves the working live /start, so auth and style
 * stay consistent (no pilot feature-flag gate, works for all roles).
 *
 * This fragment is included AFTER routes_parity_closeout (last-include-
 * wins). CI3 matches $route in INSERTION order, first match wins. A literal
 * added now is a NEW key that would otherwise append AFTER the catch-alls
 * and lose, so - exactly like routes_cm_daymgmt_fix_20260616.php - we add
 * the literals first, then unset+re-add the catch-alls so they float to the
 * very end of the route table. Re-adding identical values keeps behavior
 * byte-identical for every other path; only ordering changes so the real
 * endpoints are reached before the stub. No existing route or method is
 * touched. No production impact.
 * ===================================================================== */

// Real role-play turn + end-of-session scoring.
$route['api/role_play/reply'] = 'v28/RolePlayV28/reply';
$route['api/role_play/end']   = 'v28/RolePlayV28/end';

// Re-float the StubController catch-alls to the end so the literals above
// (and every earlier real route) are matched first. Same targets as
// routes_parity_closeout_20260611.php.
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

<?php
defined('BASEPATH') OR exit('No direct script access allowed');
/* =====================================================================
 * Close-out routes - 2026-06-20 (ADDITIVE, READ-SAFE)
 * ---------------------------------------------------------------------
 * Two app-called WRITE paths were not reaching a real handler at runtime:
 *
 *   POST api/coach/knowledge/upload_artifact
 *     A real handler already exists (MobileGapFix2_api::upload_artifact,
 *     mapped in routes_gapfix2_20260607.php). That mapping is inserted
 *     BEFORE the StubController api/(:any) catch-alls that later fragments
 *     re-float to the end, so on a fresh route table it could lose to the
 *     catch-all. Re-asserting the literal here (included LAST) guarantees
 *     the real controller wins.
 *
 *   POST api/proposal/sla/escalate
 *     The route in routes_mega_26may.php targeted proposalsla/escalate, which
 *     resolves to controller file ProposalSla.php. That file declares the same
 *     class name (Proposal_sla) as ProposalSlaController.php and is NOT the
 *     loadable copy at runtime (even its existing probe/queue 404 via that
 *     path), so CI3 returned a routing 404. The working, loadable class is
 *     ProposalSlaController (api/proposal_sla/queue etc. all resolve to it).
 *     A real escalate() handler is now added to ProposalSlaController.php, and
 *     this fragment re-points the literal to ProposalSlaController/escalate so
 *     it reaches the loadable class and beats the catch-all.
 *
 * ORDER NOTE: CI3 matches $route in INSERTION order, first match wins.
 * Earlier fragments insert the StubController catch-alls. A literal added
 * now is a NEW key, so it would append AFTER those catch-alls and lose. To
 * make the literals win we add them first, then unset + re-add the
 * catch-alls so they move to the very end of the route table. Re-adding
 * identical values keeps behavior byte-identical for every other path;
 * only ordering changes. Same pattern as routes_gate_lists_20260619.php.
 * Included LAST in routes.php (after the gate-lists include).
 *
 * Never touches production. ASCII only. No em-dashes.
 * ===================================================================== */

// Re-assert the two app-called write literals to their real controllers.
$route['api/coach/knowledge/upload_artifact']['post'] = 'MobileGapFix2_api/upload_artifact';
$route['api/proposal/sla/escalate']['post']           = 'ProposalSlaController/escalate';

// Re-float the StubController catch-alls to the very end so the literals
// above (and every earlier real route) are matched first. Same targets as
// the earlier fragments; re-adding identical values only moves them to the
// end of the table and is otherwise behavior-neutral.
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

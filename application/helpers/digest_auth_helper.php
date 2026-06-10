<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * digest_auth_helper.php
 *
 * ROOT-CAUSE FIX 2026-06-09 (marker: rimlyproof_digestauth_failopen_20260609):
 *   The previous version accepted ANY string beginning with "Bearer " as valid
 *   ("Accept any Bearer token"). That was fail-open: a garbage token returned
 *   real per-user data (e.g. /api/dashboard/header_counts?uid=<any> leaked any
 *   user's counts; /api/lead_query/checklist leaked org-wide lead rows).
 *
 *   This is now repaired ADDITIVELY by delegating the SAME single decision used
 *   by every other unified controller: authunify_resolve() -> BearerAuth::resolve().
 *
 * ADDITIVE GUARANTEE (nothing that worked is removed):
 *   - Valid master/digest token   -> passes, uid 0  (exactly as before).
 *   - Valid per-user login token   -> passes, resolved uid recorded + defaulted
 *                                     into ?uid / body uid when caller omitted it,
 *                                     so digest-era controllers expecting an
 *                                     explicit uid keep working unchanged.
 *   - Missing OR garbage token     -> returns false (controller emits its own
 *                                     401/400 exactly as before). The ONLY change
 *                                     is that a garbage Bearer no longer passes.
 *
 * Self-staging only. Production (stemapp.in) never touched. ASCII only.
 */

if (!function_exists('digest_auth_require')) {
    function digest_auth_require() {
        // Delegate to the one shared validator. authunify_ok() returns true only
        // for a valid master/digest token OR a valid per-user login token, and on
        // a login token it defaults ?uid / body uid to the resolved uid.
        if (function_exists('authunify_ok')) {
            return authunify_ok();
        }
        // Defensive fallback: if the unify helper is somehow unavailable, fail
        // CLOSED (return false) rather than fail-open. A missing helper must never
        // re-open the leak. Controllers will emit their own 401.
        return false;
    }
}

if (!function_exists('digest_auth_uid')) {
    function digest_auth_uid() {
        // Real resolved uid: 0 for master/digest (system) callers, >0 for a
        // valid per-user login token. Previously hardcoded to 0.
        if (function_exists('authunify_uid')) {
            return (int) authunify_uid();
        }
        return 0;
    }
}

if (!function_exists('digest_auth_check')) {
    /**
     * digest_auth_check($ci_instance)
     * Same yes/no as digest_auth_require(), but additionally emits a 401 JSON body
     * when a CI instance is supplied and the token is missing/invalid. Used by
     * AiLeadScoreController, AnayaBriefing, DashboardHeader, etc.
     */
    function digest_auth_check($ci = null) {
        $ok = function_exists('authunify_ok') ? authunify_ok() : false;
        if ($ok) {
            return true;
        }
        // rimlyproof_empty200_20260609: emit a REAL 401 with a JSON body.
        // CI output->set_status_header() did not reliably flush here (callers
        // do 'if(!digest_auth_check($this)) return;' which left an empty 200,
        // a fail-open-looking response). Use native PHP so the 401 always lands.
        if (!headers_sent()) {
            http_response_code(401);
            header('Content-Type: application/json');
        }
        echo json_encode(array('ok' => false, 'error' => 'bearer_required'));
        if ($ci !== null && method_exists($ci, 'output')) {
            // keep CI state consistent for any post-hooks
            $ci->output->set_status_header(401);
        }
        exit;
    }
}

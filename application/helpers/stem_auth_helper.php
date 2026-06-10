<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * stem_auth_helper.php
 * Created 2026-05-26 by schema_500_fix agent.
 */

if (!function_exists('stem_auth_bearer_ok')) {
    function stem_auth_bearer_ok($auth_header) {
        if (!$auth_header) return false;
        if (strpos($auth_header, 'Bearer ') !== 0) return false;
        // rimlyproof_failopen_fix_20260609: was fail-open (accepted ANY Bearer
        // token > 10 chars, so garbage passed and leaked data). Now delegates to
        // the single shared validator authunify_ok(), which accepts only a valid
        // master/digest token OR a valid per-user login token. Additive: real
        // callers unchanged; only missing/garbage tokens are now rejected.
        if (function_exists('authunify_ok')) {
            return authunify_ok();
        }
        // Defensive fallback: fail CLOSED if the unify helper is unavailable.
        return false;
    }
}

<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * authunify_helper - ONE shared auth language for ALL controllers.
 * Installed 2026-06-09 as the permanent root-cause fix for the auth-class bug:
 *   ~109 controllers validated ONLY the master/digest token and rejected a real
 *   user's per-user login token. Instead of 18 bespoke per-shape rewrites, every
 *   controller now delegates the SAME single decision to BearerAuth::resolve().
 *
 * ADDITIVE GUARANTEE:
 *   - Master/digest token  -> still passes (uid 0, role system), exactly as before.
 *   - Valid per-user login token (api_token row OR JWT sha1(SECRET|uid|YYYY-MM-DD)
 *     +/- 1 day) -> now ALSO passes, and the resolved uid is recorded + defaulted
 *     into ?uid / body uid only when the caller did not supply one.
 *   - Missing/invalid token -> returns false; the controller keeps emitting its own
 *     401 exactly as before. Nothing is weakened, nothing that worked is removed.
 *
 * Works regardless of how the host controller reads headers, because BearerAuth
 * reads the raw request itself ($_SERVER / REDIRECT_ / apache_request_headers).
 *
 * Self-staging only. Production (stemapp.in) never touched. ASCII only.
 */

if (!function_exists('authunify_resolve')) {
    /**
     * Returns the BearerAuth resolution array: ['ok'=>bool,'uid'=>int,'role'=>str]
     * Never throws. Caches per-request so repeated calls are cheap.
     */
    function authunify_resolve() {
        static $cached = null;
        if ($cached !== null) return $cached;
        $cached = array('ok' => false, 'uid' => 0, 'role' => '');
        if (!function_exists('get_instance')) return $cached;
        $CI =& get_instance();
        if (!$CI) return $cached;
        try {
            if (!isset($CI->bearerauth)) { @$CI->load->library('BearerAuth'); }
            if (isset($CI->bearerauth)) {
                $r = $CI->bearerauth->resolve();
                if (is_array($r)) { $cached = $r; }
            }
        } catch (Throwable $e) {
            // never break a request because of auth-helper internals
        }
        return $cached;
    }

    /**
     * Single yes/no the controllers ask. TRUE when the request carries a valid
     * digest token OR a valid per-user login token. On a valid LOGIN token it also
     * defaults ?uid / body uid to the resolved uid when the caller omitted it, so
     * digest-era controllers that expect an explicit uid keep working unchanged.
     */
    function authunify_ok() {
        $r = authunify_resolve();
        if (empty($r['ok'])) return false;
        $uid = isset($r['uid']) ? (int)$r['uid'] : 0;
        if ($uid > 0) {
            foreach (array('uid', 'user_id') as $k) {
                if (!isset($_GET[$k])     || (int)$_GET[$k]     <= 0) { $_GET[$k]     = $uid; }
                if (!isset($_POST[$k])    || (int)$_POST[$k]    <= 0) { $_POST[$k]    = $uid; }
                if (!isset($_REQUEST[$k]) || (int)$_REQUEST[$k] <= 0) { $_REQUEST[$k] = $uid; }
            }
        }
        return true;
    }

    /**
     * Resolved uid for the current request (0 for master/digest, >0 for a login
     * token). Controllers that want the acting user can call this.
     */
    function authunify_uid() {
        $r = authunify_resolve();
        return isset($r['uid']) ? (int)$r['uid'] : 0;
    }

    /**
     * rimlyproof_dayguard_20260609: canonical day-start guard for FIELD users.
     *
     * A field user (BD type_id=3, ACM type_id=24) must have an OPEN started day
     * TODAY before performing in-field mutations (plan/submit/create/research/etc).
     * Non-field roles (managers, admin, system) are NOT day-gated and always pass.
     *
     * Returns TRUE if the uid MAY perform field actions, FALSE if it must be blocked.
     * Fail-closed for field users: any DB error or no open day => blocked.
     * Single source of truth so every field-write controller asks the SAME question.
     *
     * @param int $uid acting user id
     * @return bool
     */
    function field_day_started($uid) {
        $uid = (int)$uid;
        if ($uid <= 0) return false; // no actor => cannot act in field
        if (!function_exists('get_instance')) return false;
        $CI =& get_instance();
        if (!$CI || !isset($CI->db)) return false;
        try {
            // role lookup: only BD(3) / ACM(24) are field users that need a started day
            $u = $CI->db->query('SELECT type_id FROM user WHERE uid = ? LIMIT 1', array($uid))->row_array();
            if (!$u) {
                // fall back to user_details.type_id if user row missing the col
                $ud = $CI->db->query('SELECT type_id FROM user_details WHERE user_id = ? LIMIT 1', array($uid))->row_array();
                $t = $ud ? (int)$ud['type_id'] : 0;
            } else {
                $t = (int)$u['type_id'];
            }
            $is_field_user = ($t === 3 || $t === 24);
            if (!$is_field_user) return true; // managers/admin/system: not day-gated

            // field user: must have an OPEN started day today
            $row = $CI->db->query(
                'SELECT id FROM user_day
                 WHERE user_id = ?
                   AND ustart IS NOT NULL
                   AND DATE(ustart) = CURDATE()
                   AND uclose IS NULL
                 ORDER BY id DESC LIMIT 1',
                array($uid)
            )->row_array();
            return !empty($row);
        } catch (Exception $e) {
            return false; // fail-closed
        } catch (Throwable $e) {
            return false;
        }
    }

    /**
     * rimlyproof_leadscope_20260609: canonical lead-scope resolver.
     *
     * Returns the effective owner uid whose leads the CURRENT caller is allowed
     * to see, given a requested bd_uid/uid. Single source of truth so every
     * lead-listing/detail path enforces the SAME visibility rule.
     *
     * Rules:
     *   - No valid auth                       => 0 (caller must 401 before this).
     *   - Field user (BD type_id=3, ACM=24)   => FORCED to their OWN uid. Any
     *                                            requested bd_uid that differs is
     *                                            ignored (no cross-BD leak).
     *   - Manager/admin/system/superadmin and
     *     other non-field roles (cm,rm,sc,
     *     pst,ea)                             => may use the requested uid (broad
     *                                            scope); falls back to own uid when
     *                                            no specific owner requested.
     *
     * @param int $requested the bd_uid/uid the caller asked for (0 if none)
     * @return int effective owner uid to scope the query by (0 => return empty)
     */
    function authunify_lead_scope_uid($requested) {
        $requested = (int)$requested;
        $r = authunify_resolve();
        if (empty($r['ok'])) return 0;

        $auth_uid = isset($r['uid'])  ? (int)$r['uid']            : 0;
        $role     = isset($r['role']) ? strtolower((string)$r['role']) : '';

        // Master digest / system / superadmin / admin: full visibility, honour request.
        if ($auth_uid <= 0 || in_array($role, array('system','superadmin','admin'), true)) {
            return $requested > 0 ? $requested : 0;
        }

        // Field users (BD / ACM): hard-locked to their own leads.
        if ($role === 'bd' || $role === 'acm') {
            return $auth_uid;
        }

        // Other roles (cm, rm, sc, pst, ea, unknown): may query a specific owner;
        // default to own uid when none requested.
        return $requested > 0 ? $requested : $auth_uid;
    }

    /**
     * rimlyproof_leadscope_20260609: may the CURRENT caller view a SPECIFIC lead?
     *
     * Given a lead's owner uid (init_call.mainbd) and creator id, returns TRUE if
     * the authed caller is allowed to see that lead's detail.
     *   - master/system/superadmin/admin           => yes (all).
     *   - field user (BD/ACM)                       => only if they own/created it.
     *   - other roles (cm,rm,sc,pst,ea)             => yes (team/manager scope).
     *
     * @param int $owner_uid   init_call.mainbd of the lead
     * @param int $creator_uid init_call.creator_id of the lead
     * @return bool
     */
    function authunify_lead_can_view($owner_uid, $creator_uid) {
        $owner_uid   = (int)$owner_uid;
        $creator_uid = (int)$creator_uid;
        $r = authunify_resolve();
        if (empty($r['ok'])) return false;

        $auth_uid = isset($r['uid'])  ? (int)$r['uid']            : 0;
        $role     = isset($r['role']) ? strtolower((string)$r['role']) : '';

        if ($auth_uid <= 0 || in_array($role, array('system','superadmin','admin'), true)) {
            return true;
        }
        if ($role === 'bd' || $role === 'acm') {
            return ($auth_uid === $owner_uid) || ($auth_uid === $creator_uid);
        }
        // managers and other non-field roles: team-wide visibility allowed
        return true;
    }
}

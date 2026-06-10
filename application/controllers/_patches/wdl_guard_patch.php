<?php
/**
 * Migration 012.2 - WDL approval gate
 *
 * Paste the helper method below into class Menu_model once.
 * Then add a one-line guard at the 8 cstatus write sites listed in
 * stem_progression_submit_task_patch_v2.php.
 *
 * Behaviour: any attempt to set cstatus=4 is rejected unless a wdl_request
 * row exists with request_status='approved' for the same init_call_id
 * within the last 7 days. If no approved request exists, an auto-pending
 * row is inserted and the BD is told to follow up with their admin.
 */

// ============================================================
// STEP 1 - paste this helper into class Menu_model
// ============================================================

/**
 * Guard cstatus=4 (Will Do Later) transitions.
 * Returns array with keys: allowed (bool), reason (string), request_id (int|null).
 * Call this BEFORE every "UPDATE init_call SET cstatus=" line.
 *
 * If allowed=false, the calling code MUST NOT update cstatus. Caller should
 * return the reason to the form so the BD sees why the transition was blocked.
 *
 * @param int    $inid           init_call.id
 * @param int    $uid            BD user_id requesting the transition
 * @param int    $to_status      proposed new cstatus
 * @param int    $from_status    current cstatus
 * @param string $reason_hint    optional - BD's typed reason from the form
 * @return array
 */
private function _check_wdl_guard($inid, $uid, $to_status, $from_status, $reason_hint = '') {
    // Only intercept WDL. Every other transition passes through.
    if ((int)$to_status !== 4) {
        return array('allowed' => true, 'reason' => '', 'request_id' => null);
    }

    // Look for an approved wdl_request in the last 7 days.
    $approved = $this->db->query(
        "SELECT id FROM wdl_request
          WHERE init_call_id = '$inid'
            AND requested_by = '$uid'
            AND request_status = 'approved'
            AND decided_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)
          ORDER BY decided_at DESC
          LIMIT 1"
    )->row();

    if ($approved && !empty($approved->id)) {
        return array(
            'allowed'    => true,
            'reason'     => 'admin-approved wdl request ' . $approved->id,
            'request_id' => (int)$approved->id,
        );
    }

    // No approval. Auto-create a pending request so the admin sees it,
    // and refuse the cstatus change.
    $reason = $this->db->escape_str($reason_hint ?: 'BD attempted WDL transition without admin approval');
    $this->db->query(
        "INSERT INTO wdl_request
            (init_call_id, requested_by, from_cstatus, reason, request_status, created_at)
         VALUES
            ('$inid', '$uid', '$from_status', '$reason', 'pending', NOW())"
    );
    $pending_id = $this->db->insert_id();

    log_message('info', "wdl_blocked uid=$uid inid=$inid from=$from_status request_id=$pending_id");

    return array(
        'allowed'    => false,
        'reason'     => 'WDL requires admin approval. Pending request id ' . $pending_id . ' created.',
        'request_id' => $pending_id,
    );
}

/**
 * Public helper for the form layer to check status before submitting.
 * Mobile app calls this via /api/progression/wdl_check.
 */
public function api_wdl_check($inid, $uid, $to_status, $from_status, $reason = '') {
    return $this->_check_wdl_guard($inid, $uid, $to_status, $from_status, $reason);
}

/**
 * Admin approval flow. Called from the admin queue UI.
 * Only type_id=3 may invoke. Caller must verify privilege.
 */
public function approve_wdl_request($request_id, $admin_uid, $decision, $note = '') {
    $decision_esc = $this->db->escape_str($decision); // 'approved' or 'rejected'
    $note_esc     = $this->db->escape_str($note);
    $this->db->query(
        "UPDATE wdl_request
            SET request_status = '$decision_esc',
                decided_by     = '$admin_uid',
                decided_at     = NOW(),
                decision_note  = '$note_esc'
          WHERE id = '$request_id'"
    );
    return $this->db->affected_rows() > 0;
}

// ============================================================
// STEP 2 - insertion at each of the 8 cstatus write sites
// Paste these BEFORE the existing "UPDATE init_call SET cstatus=..." line.
// ============================================================

/*
SITE 1 - line 2663 (FY rollover with RP) - SKIP, this is an automated reset.
SITE 2 - line 2668 (FY rollover no RP)    - SKIP, this is an automated reset.

SITE 3 - line 8535 (closeBmeetingWithRP)
$_wdl = $this->_check_wdl_guard($inid, $uid, $status, $cs, $letmeetingsremarks);
if (!$_wdl['allowed']) {
    return array('error' => 1, 'message' => $_wdl['reason']);
}

SITE 4 - line 8667 (closeBmeetingForOnlyGotDetailWithRP)
$_wdl = $this->_check_wdl_guard($inid, $uid, $status, $cs, $letmeetingsremarks);
if (!$_wdl['allowed']) {
    return array('error' => 1, 'message' => $_wdl['reason']);
}

SITE 5 - line 8694 (closeBmeetingForOnlyGotDetail)
$_wdl = $this->_check_wdl_guard($inid, $uid, $status, $cs, $letmeetingsremarks);
if (!$_wdl['allowed']) {
    return array('error' => 1, 'message' => $_wdl['reason']);
}

SITE 6 - line 9540 (submit_task legacy) - place BEFORE the UPDATE
$_wdl = $this->_check_wdl_guard($inid, $uid, $status, $cs, $remark);
if (!$_wdl['allowed']) {
    return array('error' => 1, 'message' => $_wdl['reason'], 'wdl_request_id' => $_wdl['request_id']);
}

SITE 7 - line 10146 (submit_task1 MoM)
$_wdl = $this->_check_wdl_guard($inid, $uid, $status, $cs, $remark);
if (!$_wdl['allowed']) {
    return array('error' => 1, 'message' => $_wdl['reason'], 'wdl_request_id' => $_wdl['request_id']);
}

SITE 8 - line 10157 (submit_task1 default)
$_wdl = $this->_check_wdl_guard($inid, $uid, $status, $cs, $remark);
if (!$_wdl['allowed']) {
    return array('error' => 1, 'message' => $_wdl['reason'], 'wdl_request_id' => $_wdl['request_id']);
}
*/

// ============================================================
// STEP 3 - admin queue endpoint (mounted at /api/progression/wdl_queue)
// ============================================================

/**
 * List pending wdl_requests for an admin's BDs.
 * @param int $admin_uid
 */
public function get_wdl_pending_for_admin($admin_uid) {
    $q = $this->db->query(
        "SELECT wr.id, wr.init_call_id, wr.requested_by, wr.from_cstatus, wr.reason,
                wr.next_followup, wr.created_at,
                ud.name AS bd_name, ud.aadmin AS cluster_admin,
                ic.cmpid_id, cm.name AS school_name, ic.fbudget
           FROM wdl_request wr
      LEFT JOIN user_details ud ON ud.user_id = wr.requested_by
      LEFT JOIN init_call ic    ON ic.id      = wr.init_call_id
      LEFT JOIN company_master cm ON cm.id    = ic.cmpid_id
          WHERE wr.request_status = 'pending'
            AND (ud.aadmin = '$admin_uid' OR ud.admin_id = '$admin_uid' OR ud.sadmin_id = '$admin_uid')
          ORDER BY wr.created_at ASC"
    );
    return $q->result();
}

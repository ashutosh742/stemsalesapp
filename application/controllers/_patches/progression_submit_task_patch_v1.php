<?php
/**
 * Patch snippet for Menu_model::submit_task and submit_task1
 *
 * Adds live capture into lead_progression_log on every cstatus jump.
 * Drop-in addition. Zero change to existing behaviour.
 *
 * Where to apply (production source paths):
 *   application/models/Menu_model.php
 *
 * Three insertion points:
 *
 * A) submit_task() - around line 9540, RIGHT AFTER:
 *      $this->db->query("UPDATE init_call SET lstatus=cstatus,cstatus='$status' WHERE id='$inid'");
 *
 * B) submit_task1() action_id=6 branch - around line 10147, RIGHT AFTER:
 *      $this->db->query("UPDATE init_call SET lstatus=cstatus,cstatus='$status' WHERE id='$inid'");
 *
 * C) submit_task1() generic branch - around line 10157, RIGHT AFTER:
 *      $this->db->query("UPDATE init_call SET lstatus=cstatus,cstatus='$status' WHERE id='$inid'");
 *
 * Insert this block in all three spots. The if($status != $cs) guard
 * ensures only real transitions are logged (the discipline rules say
 * a same-status update is not a transition).
 */

// -------- PASTE EXACTLY THIS BLOCK (8 lines) --------
if (isset($status) && isset($cs) && $status != $cs && (int)$status > 0) {
    $this->db->insert('lead_progression_log', [
        'init_call_id'    => $inid,
        'bd_uid'          => $uid,
        'from_status'     => (int)$cs,
        'to_status'       => (int)$status,
        'tblcallevent_id' => $tid,
        'action_id'       => (int)$action_id,
        'purpose_id'      => isset($purpose) ? (int)$purpose : 0,
        'transition_at'   => $date,
    ]);
}
// -------- END PASTE --------

/**
 * Notes
 * -----
 * 1. $cs is the OLD cstatus, read at the top of both methods from
 *    the SELECT join init_call. $status is the new value coming from
 *    the form. $inid, $uid, $tid, $action_id, $purpose, $date are all
 *    already in scope at the insertion points.
 *
 * 2. submit_task1() action_id=6 (MoM done) branch has its OWN update,
 *    so paste the block twice in that method (once per update path).
 *
 * 3. The insert is fire-and-forget. If lead_progression_log INSERT
 *    fails (e.g. table missing during rollout), the main transaction
 *    continues. Wrap in try/catch if you want belt-and-suspenders:
 *
 *    try {
 *        $this->db->insert('lead_progression_log', [...]);
 *    } catch (Exception $e) {
 *        log_message('error', 'lead_progression_log insert failed: ' . $e->getMessage());
 *    }
 *
 * 4. days_in_previous_status is left NULL on live inserts. The
 *    nightly refresh_daily cron back-fills it using the LAG window
 *    function (see compute_daily_bd_scores() helper).
 *
 * 5. creation_path is left NULL on live inserts. It is back-filled
 *    weekly by the cohort refresh.
 */

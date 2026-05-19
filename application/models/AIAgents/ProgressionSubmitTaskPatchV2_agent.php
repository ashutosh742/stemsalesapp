<?php
/**
 * Migration 012 - Sales Progression event capture
 * Patch v2 - extends v1 from 3 sites to all 8 cstatus writers in Menu_model.php
 *
 * Drop the helper method below into Menu_model.php once. Then paste the
 * 1-line call at each of the 8 insertion points listed at the bottom.
 *
 * Safe: never alters existing behaviour. Only inserts an audit row into
 * lead_progression_log. Wrapped in try/catch so a failed log write never
 * blocks the original transition.
 */

// ============================================================
// STEP 1 - paste this helper method into class Menu_model
// ============================================================

/**
 * Log a single cstatus transition into the migration 012 ledger.
 * Called from every place that writes init_call.cstatus.
 *
 * @param int    $inid              init_call.id
 * @param int    $uid               user_id performing the transition
 * @param int|string $from_status   previous cstatus value
 * @param int|string $to_status     new cstatus value
 * @param string $source_hint       which code path fired (see list at bottom)
 * @param int|null $task_id         tblcallevents.id when triggered by a task submit
 * @param int|null $cmpid_id        company_master.id for fast joins
 */
private function _log_progression_transition($inid, $uid, $from_status, $to_status, $source_hint, $task_id = null, $cmpid_id = null) {
    try {
        if ($from_status === $to_status || $from_status === '' || $to_status === '') {
            // No-op transitions still log if to_status is a closure (12 or 13) so we
            // capture suspicious zero-delta closures for CM review.
            if (!in_array((int)$to_status, array(12, 13), true)) {
                return;
            }
        }

        // Pull lead's fbudget at the moment of transition - this is the
        // canonical closure value (work_order.revenue is unreliable, see
        // stem_closure_path_analysis.md).
        $closed_value_rs = null;
        if (in_array((int)$to_status, array(12, 13), true)) {
            $row = $this->db->query("SELECT fbudget FROM init_call WHERE id='$inid' LIMIT 1")->row();
            if ($row && isset($row->fbudget)) {
                $closed_value_rs = (float)$row->fbudget;
            }
        }

        // CM-review gate: 9 to 12 jumps without a CM touch in last 7 days.
        $requires_cm_review = 0;
        if ((int)$from_status === 9 && (int)$to_status === 12) {
            $touch = $this->db->query(
                "SELECT COUNT(*) AS n
                   FROM tblcallevents tce
                   LEFT JOIN user_details ud ON ud.user_id = tce.user_id
                  WHERE tce.cid_id = '$inid'
                    AND ud.type_id IN (13, 27)
                    AND tce.updateddate >= DATE_SUB(NOW(), INTERVAL 7 DAY)"
            )->row();
            if (!$touch || (int)$touch->n === 0) {
                $requires_cm_review = 1;
            }
        }

        $data = array(
            'init_call_id'        => $inid,
            'cmpid_id'            => $cmpid_id,
            'transitioned_by_uid' => $uid,
            'from_cstatus'        => $from_status,
            'to_cstatus'          => $to_status,
            'task_id'             => $task_id,
            'creation_path_hint'  => $source_hint,
            'closed_value_rs'     => $closed_value_rs,
            'requires_cm_review'  => $requires_cm_review,
            'created_at'          => date('Y-m-d H:i:s'),
        );
        $this->db->insert('lead_progression_log', $data);
    } catch (Exception $e) {
        // Swallow. Logging must never block the original transition.
        log_message('error', 'progression_log_failed: ' . $e->getMessage());
    }
}

// ============================================================
// STEP 2 - insertion points. Place each call IMMEDIATELY AFTER
// the existing "UPDATE init_call SET lstatus=cstatus,cstatus=..."
// line at each site.
// ============================================================

/*
SITE 1 - line 2663 (financial year rollover, lead had RP meetings)
$this->_log_progression_transition(
    $init_call_id, $uid, null, $new_updated_status, 'fy_rollover_rp', null, $cmpid
);

SITE 2 - line 2668 (financial year rollover, no RP meetings)
$this->_log_progression_transition(
    $init_call_id, $uid, null, $new_updated_status, 'fy_rollover_no_rp', null, $cmpid
);

SITE 3 - line 8535 (closeBmeetingWithRP - barge meeting close with RP)
$this->_log_progression_transition(
    $inid, $uid, $cs, $status, 'barge_close_rp', $bmtid, null
);

SITE 4 - line 8667 (closeBmeetingForOnlyGotDetailWithRP - met RP only got detail)
$this->_log_progression_transition(
    $inid, $uid, $cs, $status, 'barge_close_only_detail_rp', $bmtid, null
);

SITE 5 - line 8694 (closeBmeetingForOnlyGotDetail - did not meet RP)
$this->_log_progression_transition(
    $inid, $uid, $cs, $status, 'barge_close_only_detail', $bmtid, null
);

SITE 6 - line 9540 (submit_task legacy - end of all action_id branches)
$this->_log_progression_transition(
    $inid, $uid, $cs, $status, 'task_submit_legacy_action_' . $action_id, $tid, $cmpid_id
);

SITE 7 - line 10146 (submit_task1 MoM branch with mom_id set)
$this->_log_progression_transition(
    $inid, $uid, $cs, $status, 'task_submit_v1_mom', $tid, null
);

SITE 8 - line 10157 (submit_task1 default branch, all other action types)
$this->_log_progression_transition(
    $inid, $uid, $cs, $status, 'task_submit_v1_action_' . $action_id, $tid, null
);
*/

// ============================================================
// STEP 3 - migration 012.1 - extend lead_progression_log
// (run this only if you already deployed migration 012 v1)
// ============================================================

/*
ALTER TABLE lead_progression_log
    ADD COLUMN cmpid_id INT NULL AFTER init_call_id,
    ADD COLUMN creation_path_hint VARCHAR(50) NULL AFTER task_id,
    ADD COLUMN closed_value_rs DECIMAL(14,2) NULL AFTER creation_path_hint,
    ADD COLUMN requires_cm_review TINYINT(1) NOT NULL DEFAULT 0 AFTER closed_value_rs,
    ADD INDEX idx_path_hint (creation_path_hint),
    ADD INDEX idx_cm_review (requires_cm_review, created_at);
*/

<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * PlanSubmitGate review-overlap patch - Migration 058
 *
 * Hooks into the existing PlanSubmitGate to refuse adding daily_planner rows
 * that overlap an active review block in bd_planner_block_log.
 *
 * Usage: include from PlanSubmitGate_model::check_can_add() or equivalent.
 * Returns ['ok'=>true] or ['ok'=>false, 'reason'=>'review_overlap', 'block'=>row].
 */
class PlanSubmitGate_review_overlap
{
    /**
     * @param CI_DB $db                Active DB instance.
     * @param int   $bd_uid            The BD whose plan is being checked.
     * @param string $plan_date        Y-m-d.
     * @param string $start_time       H:i:s of the task being added.
     * @param string $end_time         H:i:s.
     * @return array                   ['ok'=>bool, 'reason'=>string|null, 'block'=>array|null]
     */
    public static function check($db, $bd_uid, $plan_date, $start_time, $end_time)
    {
        $sql = "
            SELECT bpbl.*, rs.review_type_id, rs.status AS review_status,
                   u_cm.name AS cm_name, u_rm.name AS rm_name
            FROM bd_planner_block_log bpbl
            LEFT JOIN review_schedule rs ON rs.id = bpbl.review_schedule_id
            LEFT JOIN user u_cm ON u_cm.uid = rs.cm_uid
            LEFT JOIN user u_rm ON u_rm.uid = rs.rm_uid
            WHERE bpbl.bd_uid = ?
              AND bpbl.block_date = ?
              AND bpbl.block_reason IN ('review_scheduled','review_in_progress','review_overdue')
              AND bpbl.block_end_time > ?
              AND bpbl.block_start_time < ?
            ORDER BY bpbl.block_start_time
            LIMIT 1
        ";
        $row = $db->query($sql, [(int)$bd_uid, $plan_date, $start_time, $end_time])->row_array();

        if (!$row) {
            return ['ok' => true, 'reason' => null, 'block' => null];
        }

        $partner = $row['cm_name'] ?: ($row['rm_name'] ?: 'line manager');
        $reason  = sprintf(
            'Review block %s-%s with %s is locked. Move the task or reschedule the review.',
            substr($row['block_start_time'], 0, 5),
            substr($row['block_end_time'], 0, 5),
            $partner
        );

        return [
            'ok' => false,
            'reason' => 'review_overlap',
            'message' => $reason,
            'block' => $row,
        ];
    }
}

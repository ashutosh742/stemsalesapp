<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * ReviewSchedule_model - Migration 058
 *
 * On every INSERT into review_schedule we also seed:
 *   1. A calendar_event row so the BD's calendar shows the review block.
 *   2. A bd_planner_block_log row so PlanSubmitGate refuses to add tasks during the slot.
 *
 * Also handles cancel + reschedule which must clean up both child rows.
 * Plain English. No em-dashes. No non-ASCII. Rs for rupees.
 */
class ReviewSchedule_model extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
        $this->load->database();
    }

    /**
     * Create a review_schedule row and seed calendar + planner block in one transaction.
     * Expected $data keys: bd_uid, cm_uid (nullable for self), rm_uid (nullable),
     *                      review_type_id (1=Weekly..6=Annual), scheduled_date (Y-m-d),
     *                      scheduled_start_time, scheduled_end_time, created_by_uid.
     * Returns ['review_schedule_id'=>N, 'calendar_event_id'=>N, 'planner_block_log_id'=>N].
     */
    public function create_with_blocks($data)
    {
        $required = ['bd_uid', 'review_type_id', 'scheduled_date'];
        foreach ($required as $k) {
            if (empty($data[$k])) {
                throw new Exception("Missing required field: $k");
            }
        }

        $start_time = $data['scheduled_start_time'] ?? '17:30:00';
        $end_time   = $data['scheduled_end_time']   ?? '18:00:00';

        $this->db->trans_start();

        // 1. Insert review_schedule (parent row)
        // Live schema uses manager_uid (not cm_uid); status has no 'cancelled' (use 'rescheduled' + missed_reason)
        $manager_uid = !empty($data['cm_uid']) ? (int)$data['cm_uid'] : (!empty($data['manager_uid']) ? (int)$data['manager_uid'] : (int)$data['bd_uid']);
        $rs_row = [
            'bd_uid' => (int)$data['bd_uid'],
            'manager_uid' => $manager_uid,
            'rm_uid' => !empty($data['rm_uid']) ? (int)$data['rm_uid'] : null,
            'review_type_id' => (int)$data['review_type_id'],
            'scheduled_date' => $data['scheduled_date'],
            'scheduled_start_time' => $start_time,
            'scheduled_end_time'   => $end_time,
            'status' => 'pending',
            'created_at' => date('Y-m-d H:i:s'),
        ];
        $this->db->insert('review_schedule', $rs_row);
        $review_schedule_id = (int)$this->db->insert_id();

        // 2. Insert calendar_event row.
        // Live schema requires calendar_account_id (BD's primary calendar) and event_id_external (unique).
        $cal_acct_id = $this->_primary_calendar_account($data['bd_uid']);
        $attendees = array_filter([
            (int)$data['bd_uid'],
            $manager_uid,
            !empty($data['rm_uid']) ? (int)$data['rm_uid'] : null,
        ]);
        $cal_row = [
            'calendar_account_id' => $cal_acct_id,
            'event_id_external' => 'stem_review_' . $review_schedule_id . '_' . uniqid(),
            'title' => $this->_review_title($data['review_type_id']),
            'description' => 'Auto-scheduled review block from Migration 058.',
            'start_at' => $data['scheduled_date'] . ' ' . $start_time,
            'end_at'   => $data['scheduled_date'] . ' ' . $end_time,
            'attendees' => json_encode(array_values($attendees)),
            'sync_direction' => 'outbound',
            'created_at' => date('Y-m-d H:i:s'),
        ];
        $calendar_event_id = 0;
        if ($cal_acct_id && $this->db->table_exists('calendar_event')) {
            $this->db->insert('calendar_event', $cal_row);
            $calendar_event_id = (int)$this->db->insert_id();
        }

        // 3. Insert bd_planner_block_log row (PlanSubmitGate reads this).
        // Live schema column is plan_date (not block_date), and blocking_cid_ids/blocking_count are NOT NULL.
        $block_row = [
            'bd_uid' => (int)$data['bd_uid'],
            'plan_date' => $data['scheduled_date'],
            'blocked_at' => date('Y-m-d H:i:s'),
            'block_start_time' => $start_time,
            'block_end_time'   => $end_time,
            'block_reason' => 'review_scheduled',
            'blocking_cid_ids' => '',
            'blocking_count' => 0,
            'review_schedule_id' => $review_schedule_id,
            'created_at' => date('Y-m-d H:i:s'),
        ];
        $this->db->insert('bd_planner_block_log', $block_row);
        $planner_block_log_id = (int)$this->db->insert_id();

        // 4. Back-fill the review_schedule row with the child IDs
        $this->db->where('id', $review_schedule_id)
                 ->update('review_schedule', [
                     'calendar_event_id' => $calendar_event_id ?: null,
                     'planner_block_log_id' => $planner_block_log_id,
                 ]);

        $this->db->trans_complete();

        if (!$this->db->trans_status()) {
            throw new Exception('Failed to seed review schedule with calendar and planner block');
        }

        return [
            'review_schedule_id' => $review_schedule_id,
            'calendar_event_id'  => $calendar_event_id,
            'planner_block_log_id' => $planner_block_log_id,
        ];
    }

    /**
     * Cancel a review schedule and remove the calendar + planner block.
     */
    public function cancel($review_schedule_id, $cancelled_by_uid, $reason = null)
    {
        $rs = $this->db->where('id', $review_schedule_id)->get('review_schedule')->row_array();
        if (!$rs) {
            throw new Exception('Review schedule not found');
        }
        if ($rs['status'] === 'completed') {
            throw new Exception('Cannot cancel a completed review');
        }

        $this->db->trans_start();

        // ENUM has no 'cancelled' state - use 'rescheduled' and stash reason in missed_reason
        $this->db->where('id', $review_schedule_id)->update('review_schedule', [
            'status' => 'rescheduled',
            'missed_reason' => 'cancelled by uid ' . (int)$cancelled_by_uid . ': ' . ($reason ?: 'no reason'),
        ]);

        if (!empty($rs['calendar_event_id']) && $this->db->table_exists('calendar_event')) {
            $this->db->where('id', $rs['calendar_event_id'])->delete('calendar_event');
        }
        if (!empty($rs['planner_block_log_id'])) {
            $this->db->where('id', $rs['planner_block_log_id'])->delete('bd_planner_block_log');
        }

        $this->db->trans_complete();
        return $this->db->trans_status();
    }

    /**
     * Reschedule by cancelling the existing block and creating a fresh one.
     */
    public function reschedule($review_schedule_id, $new_date, $new_start, $new_end, $by_uid)
    {
        $rs = $this->db->where('id', $review_schedule_id)->get('review_schedule')->row_array();
        if (!$rs) {
            throw new Exception('Review schedule not found');
        }
        $this->cancel($review_schedule_id, $by_uid, 'rescheduled');
        return $this->create_with_blocks([
            'bd_uid' => $rs['bd_uid'],
            'cm_uid' => $rs['cm_uid'],
            'rm_uid' => $rs['rm_uid'],
            'review_type_id' => $rs['review_type_id'],
            'scheduled_date' => $new_date,
            'scheduled_start_time' => $new_start,
            'scheduled_end_time' => $new_end,
            'created_by_uid' => $by_uid,
        ]);
    }

    /**
     * Returns reviews where any active planner block overlaps the given time window.
     * Used by PlanSubmitGate to refuse task additions during a scheduled review.
     */
    public function active_blocks_in_window($bd_uid, $date, $start_time, $end_time)
    {
        return $this->db->select('bpbl.*, rs.review_type_id, rs.status as review_status')
            ->from('bd_planner_block_log bpbl')
            ->join('review_schedule rs', 'rs.id = bpbl.review_schedule_id', 'left')
            ->where('bpbl.bd_uid', (int)$bd_uid)
            ->where('bpbl.plan_date', $date)
            ->where("bpbl.block_reason IN ('review_scheduled','review_in_progress','review_overdue')")
            ->where('bpbl.block_end_time >', $start_time)
            ->where('bpbl.block_start_time <', $end_time)
            ->get()->result_array();
    }

    /**
     * Lookup the BD's primary calendar_account_id. Returns 0 if none configured.
     */
    private function _primary_calendar_account($uid)
    {
        if (!$this->db->table_exists('calendar_account')) {
            return 0;
        }
        $row = $this->db->where("uid", (int)$uid)
            ->order_by('id', 'asc')
            ->limit(1)
            ->get('calendar_account')->row_array();
        return $row ? (int)$row['id'] : 0;
    }

    private function _review_title($review_type_id)
    {
        $map = [
            1 => 'Weekly review',
            2 => 'Fortnightly review',
            3 => 'Monthly review',
            4 => 'Quarterly review (RM joint)',
            5 => 'Half-yearly review',
            6 => 'Annual review',
        ];
        return $map[(int)$review_type_id] ?? 'Review';
    }
}

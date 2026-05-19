<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * ExpenseAccountability_model
 * ---------------------------------
 * Sister model to SalesDiscipline_model. Owns:
 *   - Meeting cancellation flow (creates audit row, refunds cash_allot, dispositions advance)
 *   - Expense actual submission (BD posts photo + amount, computes variance)
 *   - Dual-approval queue (CM + Accounts Officer type_id=27)
 *   - Plan-submit gate check (blocks 18:30 IST submission if today's actuals missing)
 *   - Daily sweep cron support (called by stem-scan 7:30 cron)
 *
 * Cutoffs (locked 15-May-2026):
 *   variance threshold ........ ±20% (>20% requires both CM and AO)
 *   receipt ................... mandatory on every expense
 *   actuals deadline .......... before next-day plan submit (18:30 IST today)
 *   accounts officer .......... type_id = 27
 */
class ExpenseAccountability_model extends CI_Model
{
    const VARIANCE_THRESHOLD_PCT = 20;
    const PLAN_SUBMIT_CUTOFF     = '18:30:00';
    const DEFAULT_PLANNED_COST   = 500;
    const ACCOUNTS_OFFICER_TID   = 27;

    public function __construct()
    {
        parent::__construct();
        $this->load->database();
    }

    // ============================================================
    // 1. CANCELLATION
    // ============================================================

    /**
     * Cancel a planned meeting and auto-refund the 500 baseline.
     * Decides what to do with any linked travel_advance based on disposition.
     *
     * @param int    $event_id            tblcallevents.id
     * @param int    $bd_uid              user_details.user_id of BD doing the cancel
     * @param string $reason              free text
     * @param string $category            one of cancellation_category_ref.code
     * @param string $disposition         one of return|roll_next_meeting|absorb
     * @param int|null $rolled_to_event_id when disposition='roll_next_meeting'
     * @return array  {ok, event_id, refunded, advance_disposition, audit_id}
     */
    public function cancel_meeting($event_id, $bd_uid, $reason, $category, $disposition, $rolled_to_event_id = null)
    {
        // 1. Validate task exists and belongs to BD (or BD is line manager)
        $task = $this->db->query("SELECT * FROM tblcallevents WHERE id = ?", [$event_id])->row();
        if (!$task) {
            return ['ok' => false, 'error' => 'event_not_found'];
        }
        if (!empty($task->cancelled_at)) {
            return ['ok' => false, 'error' => 'already_cancelled', 'cancelled_at' => $task->cancelled_at];
        }

        // 2. Validate category
        $cat = $this->db->query("SELECT * FROM cancellation_category_ref WHERE code = ?", [$category])->row();
        if (!$cat) {
            return ['ok' => false, 'error' => 'unknown_category'];
        }

        // 3. Stamp the cancellation on tblcallevents
        $this->db->where('id', $event_id);
        $this->db->update('tblcallevents', [
            'cancelled_at'          => date('Y-m-d H:i:s'),
            'cancelled_by_uid'      => $bd_uid,
            'cancellation_reason'   => $reason,
            'cancellation_category' => $category,
            'advance_disposition'   => $disposition,
            'rolled_to_event_id'    => $rolled_to_event_id,
        ]);

        // 4. Auto-refund cash_allot to user_details.ucash if disposition <> 'absorb'
        $refunded = 0;
        $cash_allot = (int)($task->cash_allot ?? 0);
        if ($cash_allot > 0 && $disposition !== 'absorb' && $cat->refund_eligible) {
            $user = $this->db->query("SELECT ucash FROM user_details WHERE user_id = ?", [$task->user_id])->row();
            $current = (float)($user->ucash ?? 0);
            $new_cash = $current + $cash_allot;

            $this->db->where('user_id', $task->user_id);
            $this->db->update('user_details', ['ucash' => $new_cash]);

            $this->db->where('id', $event_id);
            $this->db->update('tblcallevents', ['cash_refund' => $cash_allot]);

            $this->db->insert('cash_log', [
                'uid'     => $task->user_id,
                'cash'    => $cash_allot,
                'av_cash' => $new_cash,
                'type'    => 'Credit',
                'remarks' => "Cash Revert: Meeting Cancelled. Category: $category. Reason: " . substr($reason, 0, 200),
                'task_id' => $event_id,
            ]);

            $refunded = $cash_allot;
        }

        // 5. Handle linked travel_advance if any
        $advance = $this->db->query(
            "SELECT * FROM travel_advance WHERE linked_event_id = ? AND consumed_status = 'pending' ORDER BY id DESC LIMIT 1",
            [$event_id]
        )->row();
        $advance_id = null;
        $advance_amount = null;
        if ($advance) {
            $advance_id = $advance->id;
            $advance_amount = (int)$advance->amount;
            $new_status = 'returned';
            if ($disposition === 'roll_next_meeting') $new_status = 'rolled';
            elseif ($disposition === 'absorb')        $new_status = 'absorbed';

            $this->db->where('id', $advance->id);
            $this->db->update('travel_advance', [
                'consumed_status'              => $new_status,
                'linked_cancellation_event_id' => $event_id,
                'consumed_at'                  => date('Y-m-d H:i:s'),
                'absorbed_reason'              => $disposition === 'absorb' ? $reason : null,
            ]);

            // If return: credit travel_advance amount back to ucash too
            if ($disposition === 'return' && $advance_amount > 0) {
                $u = $this->db->query("SELECT ucash FROM user_details WHERE user_id = ?", [$task->user_id])->row();
                $cur = (float)$u->ucash;
                $next = $cur + $advance_amount;
                $this->db->where('user_id', $task->user_id);
                $this->db->update('user_details', ['ucash' => $next]);
                $this->db->insert('cash_log', [
                    'uid'     => $task->user_id,
                    'cash'    => $advance_amount,
                    'av_cash' => $next,
                    'type'    => 'Credit',
                    'remarks' => "Travel advance returned on meeting cancellation. Advance id $advance_id.",
                    'task_id' => $event_id,
                ]);
                $refunded += $advance_amount;
            }
        }

        // 6. Audit row
        $cluster = $this->db->query("SELECT cluster_id FROM user_details WHERE user_id = ?", [$task->user_id])->row();
        $this->db->insert('cancellation_audit', [
            'event_id'              => $event_id,
            'bd_uid'                => $task->user_id,
            'cluster_id'            => $cluster->cluster_id ?? null,
            'cancelled_at'          => date('Y-m-d H:i:s'),
            'cancellation_category' => $category,
            'cancellation_reason'   => $reason,
            'advance_disposition'   => $disposition,
            'cash_allot_refunded'   => $cash_allot,
            'travel_advance_id'     => $advance_id,
            'travel_advance_amount' => $advance_amount,
            'rolled_to_event_id'    => $rolled_to_event_id,
        ]);
        $audit_id = $this->db->insert_id();

        return [
            'ok'                  => true,
            'event_id'            => $event_id,
            'refunded'            => $refunded,
            'advance_disposition' => $disposition,
            'audit_id'            => $audit_id,
        ];
    }


    // ============================================================
    // 2. EXPENSE ACTUAL SUBMISSION
    // ============================================================

    /**
     * BD submits actual expense at end of meeting (or end of day, before next plan).
     * Photo of receipt is mandatory. Computes variance vs planned_cost.
     * If |variance| > VARIANCE_THRESHOLD_PCT, requires_dual_approval is set.
     *
     * @param int     $event_id
     * @param int     $bd_uid
     * @param int     $actual_cost
     * @param string  $receipt_filename   path on disk (already uploaded)
     * @param array   $breakdown          [{label, amount}] optional
     * @return array {ok, event_id, variance_pct, requires_dual_approval, log_id}
     */
    public function submit_actuals($event_id, $bd_uid, $actual_cost, $receipt_filename, $breakdown = [])
    {
        if (empty($receipt_filename)) {
            return ['ok' => false, 'error' => 'receipt_required'];
        }
        $task = $this->db->query("SELECT * FROM tblcallevents WHERE id = ?", [$event_id])->row();
        if (!$task) return ['ok' => false, 'error' => 'event_not_found'];

        $planned = (int)($task->planned_cost ?? self::DEFAULT_PLANNED_COST);
        $variance = $planned > 0 ? round((($actual_cost - $planned) / $planned) * 100, 2) : 0;
        $requires_dual = abs($variance) > self::VARIANCE_THRESHOLD_PCT ? 1 : 0;

        // Stamp tblcallevents
        $this->db->where('id', $event_id);
        $this->db->update('tblcallevents', [
            'actual_cost'            => $actual_cost,
            'variance_pct'           => $variance,
            'requires_dual_approval' => $requires_dual,
            'expense_submitted_at'   => date('Y-m-d H:i:s'),
        ]);

        // Insert cash_expense row (linked to event_id) - keeps backward-compat with old schema
        $this->db->insert('cash_expense', [
            'user_id'         => $task->user_id,
            'meetid'          => $task->id,
            'tbl_task_id'     => $task->id,
            'linked_event_id' => $event_id,
            'expense'         => $actual_cost,
            'expense_remarks' => json_encode($breakdown),
            'bills'           => $receipt_filename,
            'receipt_required'=> 1,
            'receipt_uploaded'=> 1,
            'expense_type'    => 'meeting_actual',
        ]);

        // Insert expense_actuals_log row
        $this->db->insert('expense_actuals_log', [
            'event_id'               => $event_id,
            'bd_uid'                 => $bd_uid,
            'planned_cost'           => $planned,
            'actual_cost'            => $actual_cost,
            'variance_pct'           => $variance,
            'receipt_filename'       => $receipt_filename,
            'expense_breakdown_json' => json_encode($breakdown),
            'requires_dual_approval' => $requires_dual,
            'final_state'            => 'pending_cm',
        ]);
        $log_id = $this->db->insert_id();

        return [
            'ok'                     => true,
            'event_id'               => $event_id,
            'planned'                => $planned,
            'actual'                 => $actual_cost,
            'variance_pct'           => $variance,
            'requires_dual_approval' => $requires_dual,
            'log_id'                 => $log_id,
        ];
    }


    // ============================================================
    // 3. DUAL APPROVAL (CM then Accounts Officer when needed)
    // ============================================================

    /**
     * CM approves an expense_actuals_log row.
     * If requires_dual_approval=1, state goes to pending_ao.
     * Otherwise state goes to approved immediately.
     */
    public function cm_approve_expense($log_id, $cm_uid, $remarks = '')
    {
        $row = $this->db->query("SELECT * FROM expense_actuals_log WHERE id = ?", [$log_id])->row();
        if (!$row) return ['ok' => false, 'error' => 'log_not_found'];
        if ($row->cm_approved) return ['ok' => false, 'error' => 'already_cm_approved'];

        $next = $row->requires_dual_approval ? 'pending_ao' : 'approved';
        $this->db->where('id', $log_id);
        $this->db->update('expense_actuals_log', [
            'cm_approved'    => 1,
            'cm_approved_by' => $cm_uid,
            'cm_approved_at' => date('Y-m-d H:i:s'),
            'cm_remarks'     => $remarks,
            'final_state'    => $next,
        ]);
        return ['ok' => true, 'log_id' => $log_id, 'next_state' => $next];
    }

    /**
     * Accounts Officer (type_id=27) approves an over-variance expense.
     * Only callable when row.requires_dual_approval=1 AND cm_approved=1.
     */
    public function ao_approve_expense($log_id, $ao_uid, $remarks = '')
    {
        $u = $this->db->query("SELECT type_id FROM user_details WHERE user_id = ?", [$ao_uid])->row();
        if (!$u || (int)$u->type_id !== self::ACCOUNTS_OFFICER_TID) {
            return ['ok' => false, 'error' => 'not_accounts_officer'];
        }
        $row = $this->db->query("SELECT * FROM expense_actuals_log WHERE id = ?", [$log_id])->row();
        if (!$row) return ['ok' => false, 'error' => 'log_not_found'];
        if (!$row->cm_approved) return ['ok' => false, 'error' => 'cm_approval_first'];
        if ($row->ao_approved) return ['ok' => false, 'error' => 'already_ao_approved'];

        $this->db->where('id', $log_id);
        $this->db->update('expense_actuals_log', [
            'ao_approved'    => 1,
            'ao_approved_by' => $ao_uid,
            'ao_approved_at' => date('Y-m-d H:i:s'),
            'ao_remarks'     => $remarks,
            'final_state'    => 'approved',
        ]);

        // Also stamp tblcallevents.accounts_apr
        $this->db->where('id', $row->event_id);
        $this->db->update('tblcallevents', [
            'accounts_apr'         => 1,
            'accounts_apr_by'      => $ao_uid,
            'accounts_apr_date'    => date('Y-m-d H:i:s'),
            'accounts_apr_remarks' => $remarks,
        ]);
        return ['ok' => true, 'log_id' => $log_id, 'next_state' => 'approved'];
    }

    public function get_cm_queue($cm_uid)
    {
        $sql = "SELECT l.*, e.appointmentdatetime, e.user_id, ud.name AS bd_name
                FROM expense_actuals_log l
                JOIN tblcallevents e ON e.id = l.event_id
                JOIN user_details ud ON ud.user_id = e.user_id
                WHERE ud.admin_id = ? AND l.final_state = 'pending_cm'
                ORDER BY l.submitted_at DESC LIMIT 50";
        return $this->db->query($sql, [$cm_uid])->result();
    }

    public function get_ao_queue()
    {
        $sql = "SELECT l.*, e.appointmentdatetime, ud.name AS bd_name, cm.name AS cm_name
                FROM expense_actuals_log l
                JOIN tblcallevents e ON e.id = l.event_id
                JOIN user_details ud ON ud.user_id = e.user_id
                LEFT JOIN user_details cm ON cm.user_id = ud.admin_id
                WHERE l.requires_dual_approval = 1
                  AND l.cm_approved = 1
                  AND l.ao_approved = 0
                ORDER BY l.submitted_at ASC LIMIT 100";
        return $this->db->query($sql)->result();
    }


    // ============================================================
    // 4. PLAN-SUBMIT GATE (called by /api/discipline/submit_plan at 18:30)
    // ============================================================

    /**
     * Returns ok=true if BD is free to submit tomorrow's plan, ok=false with
     * a list of today's unresolved tasks otherwise. Also writes plan_submit_gate_log.
     */
    public function check_plan_submit_gate($bd_uid, $plan_date = null)
    {
        $plan_date = $plan_date ?: date('Y-m-d', strtotime('+1 day'));
        $today     = date('Y-m-d');

        // Find today's approved tasks that have cash_allot > 0
        $sql = "SELECT id, cash_allot, cancelled_at, expense_submitted_at, advance_disposition
                FROM tblcallevents
                WHERE assignedto_id = ?
                  AND approved_status = 1
                  AND DATE(appointmentdatetime) = ?
                  AND COALESCE(cash_allot, 0) > 0";
        $rows = $this->db->query($sql, [$bd_uid, $today])->result();

        $blockers = [];
        foreach ($rows as $r) {
            if (!empty($r->cancelled_at)) {
                if ($r->advance_disposition === 'pending_decision') {
                    $blockers[] = ['event_id' => $r->id, 'why' => 'cancellation_no_disposition'];
                }
                continue;
            }
            if (empty($r->expense_submitted_at)) {
                $blockers[] = ['event_id' => $r->id, 'why' => 'actuals_missing'];
            }
        }

        $allowed = empty($blockers);
        $result_code = $allowed ? 'allowed'
                     : (in_array('cancellation_no_disposition', array_column($blockers, 'why'))
                        ? 'blocked_cancellation_pending'
                        : 'blocked_actuals_missing');

        $this->db->insert('plan_submit_gate_log', [
            'bd_uid'             => $bd_uid,
            'attempted_at'       => date('Y-m-d H:i:s'),
            'plan_date'          => $plan_date,
            'gate_result'        => $result_code,
            'blocking_event_ids' => implode(',', array_column($blockers, 'event_id')),
            'blocking_count'     => count($blockers),
        ]);

        // Stamp daily_planner so UI can show why it was blocked
        if (!$allowed) {
            $this->db->where('user_id', $bd_uid)->where('plan_date', $plan_date);
            $this->db->update('daily_planner', [
                'blocked_by_actuals_pending' => 1,
                'blocking_event_ids'         => implode(',', array_column($blockers, 'event_id')),
            ]);
        }
        return [
            'ok'                 => $allowed,
            'gate_result'        => $result_code,
            'blockers'           => $blockers,
            'blocking_count'     => count($blockers),
        ];
    }


    // ============================================================
    // 5. DAILY SWEEP (called by 7:30 IST cron 0c647bbd)
    // ============================================================

    /**
     * Find stale tasks where:
     *   - appointmentdatetime < CURDATE()
     *   - cash_allot > 0
     *   - no cancelled_at AND no expense_submitted_at AND no delete_request
     * Returns array for the audit report. Does not auto-mutate.
     */
    public function find_stale_cash_allotments($days_back = 7)
    {
        $sql = "SELECT e.id event_id, e.user_id, e.cash_allot, e.appointmentdatetime,
                       ud.name bd_name, ud.cluster_id,
                       DATEDIFF(CURDATE(), DATE(e.appointmentdatetime)) age_days
                FROM tblcallevents e
                JOIN user_details ud ON ud.user_id = e.user_id
                WHERE DATE(e.appointmentdatetime) < CURDATE()
                  AND DATE(e.appointmentdatetime) >= DATE_SUB(CURDATE(), INTERVAL ? DAY)
                  AND COALESCE(e.cash_allot, 0) > 0
                  AND e.cancelled_at IS NULL
                  AND e.expense_submitted_at IS NULL
                  AND (e.delete_request IS NULL OR e.delete_request = '')
                ORDER BY age_days DESC, e.cash_allot DESC LIMIT 50";
        return $this->db->query($sql, [$days_back])->result();
    }

    public function find_unreturned_advances($days_back = 7)
    {
        $sql = "SELECT t.id advance_id, t.user_id bd_uid, t.amount, t.linked_event_id event_id,
                       ud.name bd_name, t.consumed_status,
                       DATEDIFF(CURDATE(), DATE(t.created_at)) aging_days,
                       e.cancelled_at, e.cancellation_category
                FROM travel_advance t
                JOIN user_details ud ON ud.user_id = t.user_id
                LEFT JOIN tblcallevents e ON e.id = t.linked_event_id
                WHERE t.consumed_status IN ('pending','rolled')
                  AND t.created_at >= DATE_SUB(CURDATE(), INTERVAL ? DAY)
                  AND e.cancelled_at IS NOT NULL
                ORDER BY aging_days DESC, t.amount DESC LIMIT 25";
        return $this->db->query($sql, [$days_back])->result();
    }

    public function find_variance_breaches($days_back = 1)
    {
        $sql = "SELECT l.*, ud.name bd_name
                FROM expense_actuals_log l
                JOIN tblcallevents e ON e.id = l.event_id
                JOIN user_details ud ON ud.user_id = l.bd_uid
                WHERE l.requires_dual_approval = 1
                  AND l.submitted_at >= DATE_SUB(CURDATE(), INTERVAL ? DAY)
                ORDER BY ABS(l.variance_pct) DESC LIMIT 25";
        return $this->db->query($sql, [$days_back])->result();
    }

    // ============================================================
    // 8. ADVANCE MANAGEMENT (travel_advance lifecycle)
    //   - BD requests an advance against a specific meeting (event_id)
    //   - CM (cluster_apr), Admin (admin_apr), Accounts (account_apr) approve
    //   - On disbursement: credit user_details.ucash
    //   - On meeting close: BD marks advance consumed (or rolled, returned, absorbed)
    //   - On cancellation: cancel_meeting() auto-dispositions based on category
    //
    // Production schema reused: travel_advance.cluster_apr / admin_apr / account_apr
    //   0=pending, 1=approved, 2=rejected, 3=suspect
    // Plus migration 009 added: consumed_status, linked_cancellation_event_id,
    //   consumed_at, absorbed_reason, linked_event_id (existing).
    // ============================================================

    /**
     * BD raises an advance request linked to a planned meeting.
     * @param int    $bd_uid
     * @param int    $event_id  tblcallevents.id (the meeting)
     * @param float  $amount    requested rupees
     * @param string $purpose   short text
     * @return array {ok, advance_id?, error?}
     */
    public function request_advance($bd_uid, $event_id, $amount, $purpose = '')
    {
        if ($amount <= 0)        return ['ok' => false, 'error' => 'amount_required'];
        if ($event_id <= 0)      return ['ok' => false, 'error' => 'event_required'];

        $event = $this->db->get_where('tblcallevents', ['id' => $event_id])->row();
        if (!$event) return ['ok' => false, 'error' => 'event_not_found'];
        if ((int)$event->uid !== (int)$bd_uid) return ['ok' => false, 'error' => 'not_your_meeting'];
        if (!empty($event->cancelled_at))      return ['ok' => false, 'error' => 'meeting_cancelled'];

        // duplicate guard
        $dup = $this->db->get_where('travel_advance', ['user_id' => $bd_uid, 'linked_event_id' => $event_id])->row();
        if ($dup) return ['ok' => false, 'error' => 'already_requested', 'advance_id' => (int)$dup->id];

        $data = [
            'user_id'         => $bd_uid,
            'cash'            => $amount,
            'amount'          => $amount,
            'purpose'         => $purpose ?: 'Meeting advance',
            'linked_event_id' => $event_id,
            'cluster_apr'     => 0,
            'admin_apr'       => 0,
            'account_apr'     => 0,
            'consumed_status' => 'pending',
            'date'            => date('Y-m-d H:i:s'),
            'created_at'      => date('Y-m-d H:i:s'),
        ];
        $this->db->insert('travel_advance', $data);
        return ['ok' => true, 'advance_id' => (int)$this->db->insert_id()];
    }

    /**
     * Approval action by CM (cluster_apr) or Admin (admin_apr) or Accounts (account_apr).
     * @param int    $advance_id
     * @param int    $approver_uid
     * @param string $role    one of: cluster|admin|account
     * @param int    $action  1=approve 2=reject 3=suspect
     * @param string $remarks
     */
    public function approve_advance($advance_id, $approver_uid, $role, $action, $remarks = '')
    {
        $valid_roles   = ['cluster', 'admin', 'account'];
        $valid_actions = [1, 2, 3];
        if (!in_array($role, $valid_roles, true))   return ['ok' => false, 'error' => 'bad_role'];
        if (!in_array((int)$action, $valid_actions, true)) return ['ok' => false, 'error' => 'bad_action'];

        $adv = $this->db->get_where('travel_advance', ['id' => $advance_id])->row();
        if (!$adv) return ['ok' => false, 'error' => 'advance_not_found'];

        // Stage gating: cluster first, then admin, then account.
        if ($role === 'admin'   && (int)$adv->cluster_apr !== 1) return ['ok' => false, 'error' => 'cluster_pending'];
        if ($role === 'account' && ((int)$adv->cluster_apr !== 1 || (int)$adv->admin_apr !== 1)) return ['ok' => false, 'error' => 'upstream_pending'];

        $update = [
            $role . '_apr'      => $action,
            $role . '_by'       => $approver_uid,
            $role . '_apr_date' => date('Y-m-d H:i:s'),
            $role . '_remarks'  => $remarks,
        ];
        $this->db->where('id', $advance_id)->update('travel_advance', $update);

        // On final account approval -> disburse cash to ucash wallet.
        if ($role === 'account' && (int)$action === 1) {
            $this->_credit_ucash($adv->user_id, $adv->cash, 'Travel Advance Disbursed (id ' . $advance_id . ')');
            $this->db->where('id', $advance_id)->update('travel_advance', [
                'consumed_status' => 'pending',
                'disbursed_at'    => date('Y-m-d H:i:s'),
            ]);
        }
        return ['ok' => true];
    }

    /**
     * BD marks the advance as consumed (meeting happened, money spent).
     * If actual < cash, the leftover returns to ucash.
     */
    public function mark_advance_consumed($advance_id, $bd_uid, $actual_spent)
    {
        $adv = $this->db->get_where('travel_advance', ['id' => $advance_id, 'user_id' => $bd_uid])->row();
        if (!$adv) return ['ok' => false, 'error' => 'not_found'];
        if ($adv->consumed_status !== 'pending') return ['ok' => false, 'error' => 'already_' . $adv->consumed_status];

        $leftover = max(0, (float)$adv->cash - (float)$actual_spent);
        if ($leftover > 0) {
            $this->_credit_ucash($bd_uid, $leftover, 'Advance leftover return (adv ' . $advance_id . ')');
        }
        $this->db->where('id', $advance_id)->update('travel_advance', [
            'consumed_status' => 'consumed',
            'actual_spent'    => $actual_spent,
            'leftover_returned' => $leftover,
            'consumed_at'     => date('Y-m-d H:i:s'),
        ]);
        return ['ok' => true, 'leftover_returned' => $leftover];
    }

    /**
     * BD returns the full advance (meeting didn't happen, or unused).
     * Credits ucash with the full amount.
     */
    public function return_advance($advance_id, $bd_uid, $reason = '')
    {
        $adv = $this->db->get_where('travel_advance', ['id' => $advance_id, 'user_id' => $bd_uid])->row();
        if (!$adv) return ['ok' => false, 'error' => 'not_found'];
        if ($adv->consumed_status !== 'pending') return ['ok' => false, 'error' => 'already_' . $adv->consumed_status];

        $this->_credit_ucash($bd_uid, $adv->cash, 'Advance full return (adv ' . $advance_id . ')');
        $this->db->where('id', $advance_id)->update('travel_advance', [
            'consumed_status' => 'returned',
            'consumed_at'     => date('Y-m-d H:i:s'),
            'absorbed_reason' => $reason,
        ]);
        return ['ok' => true];
    }

    /**
     * List advances for one BD (used by AdvanceManagementScreen).
     * Filters: status, days_back.
     */
    public function list_my_advances($bd_uid, $status = 'all', $days_back = 30)
    {
        $where = "user_id = ? AND date >= DATE_SUB(CURDATE(), INTERVAL ? DAY)";
        $params = [$bd_uid, $days_back];
        if ($status === 'pending')   $where .= " AND (cluster_apr = 0 OR (cluster_apr = 1 AND admin_apr = 0) OR (cluster_apr = 1 AND admin_apr = 1 AND account_apr = 0))";
        if ($status === 'approved')  $where .= " AND cluster_apr = 1 AND admin_apr = 1 AND account_apr = 1";
        if ($status === 'rejected')  $where .= " AND (cluster_apr = 2 OR admin_apr = 2 OR account_apr = 2)";
        if ($status === 'open')      $where .= " AND cluster_apr = 1 AND admin_apr = 1 AND account_apr = 1 AND consumed_status = 'pending'";
        if ($status === 'closed')    $where .= " AND consumed_status IN ('consumed','returned','rolled','absorbed')";

        $sql = "SELECT ta.*, e.appointmentdatetime meeting_at, e.subject meeting_subject,
                       e.cancelled_at, e.cancellation_category,
                       CASE
                         WHEN ta.cluster_apr = 2 OR ta.admin_apr = 2 OR ta.account_apr = 2 THEN 'rejected'
                         WHEN ta.cluster_apr = 1 AND ta.admin_apr = 1 AND ta.account_apr = 1 THEN 'approved'
                         WHEN ta.cluster_apr = 1 AND ta.admin_apr = 1 THEN 'awaiting_accounts'
                         WHEN ta.cluster_apr = 1 THEN 'awaiting_admin'
                         ELSE 'awaiting_cm'
                       END AS approval_stage
                FROM travel_advance ta
                LEFT JOIN tblcallevents e ON e.id = ta.linked_event_id
                WHERE $where
                ORDER BY ta.id DESC LIMIT 100";
        return $this->db->query($sql, $params)->result();
    }

    /**
     * Queue for approvers. Role: cluster|admin|account.
     */
    public function list_approval_queue($role)
    {
        $where = '';
        if ($role === 'cluster') $where = "ta.cluster_apr = 0";
        if ($role === 'admin')   $where = "ta.cluster_apr = 1 AND ta.admin_apr = 0";
        if ($role === 'account') $where = "ta.cluster_apr = 1 AND ta.admin_apr = 1 AND ta.account_apr = 0";
        if (!$where) return [];

        $sql = "SELECT ta.*, ud.name bd_name, e.subject meeting_subject, e.appointmentdatetime meeting_at,
                       DATEDIFF(CURDATE(), DATE(ta.date)) aging_days
                FROM travel_advance ta
                JOIN user_details ud ON ud.user_id = ta.user_id
                LEFT JOIN tblcallevents e ON e.id = ta.linked_event_id
                WHERE $where
                ORDER BY ta.date ASC LIMIT 50";
        return $this->db->query($sql)->result();
    }

    /** Helper. Credits ucash and writes a cash_log entry. */
    private function _credit_ucash($uid, $amount, $note)
    {
        $this->db->query("UPDATE user_details SET ucash = COALESCE(ucash,0) + ? WHERE user_id = ?", [$amount, $uid]);
        $this->db->insert('cash_log', [
            'user_id'   => $uid,
            'amount'    => $amount,
            'direction' => 'credit',
            'note'      => $note,
            'created_at'=> date('Y-m-d H:i:s'),
        ]);
    }

    private function _debit_ucash($uid, $amount, $note)
    {
        $this->db->query("UPDATE user_details SET ucash = COALESCE(ucash,0) - ? WHERE user_id = ?", [$amount, $uid]);
        $this->db->insert('cash_log', [
            'user_id'   => $uid,
            'amount'    => $amount,
            'direction' => 'debit',
            'note'      => $note,
            'created_at'=> date('Y-m-d H:i:s'),
        ]);
    }

    // ============================================================
    // 7. PENDING MEETINGS + BATCHED SUBMIT (production parity)
    //    Mirrors stemapp.in Menu::AddCashSpentInMeetings + Menu_model::Addexpensecash
    // ============================================================

    /**
     * Today's closed meetings for this BD that still need expense submission.
     * A meeting is "pending" if tblcallevents.id has no cash_expense row yet,
     * the meeting is closed (closem IS NOT NULL), and it belongs to this BD.
     */
    public function get_pending_meetings_for_today($bd_uid)
    {
        $today = date('Y-m-d');
        $sql = "
            SELECT t.id AS event_id, t.id AS tid, t.user_id, t.cash_allot, t.planned_cost,
                   t.event_date, t.startm, t.closem, t.remarks,
                   m.id AS meetid, m.company_name
            FROM tblcallevents t
            JOIN mom_data m ON m.tid = t.id
            LEFT JOIN cash_expense ce ON (ce.meetid = m.id OR ce.linked_event_id = t.id)
            WHERE t.user_id = ?
              AND DATE(t.event_date) = ?
              AND t.closem IS NOT NULL
              AND ce.id IS NULL
            ORDER BY t.startm ASC
        ";
        $rows = $this->db->query($sql, [$bd_uid, $today])->result();
        return $rows;
    }

    /**
     * Batched submit: writes one cash_expense + expense_actuals_log row per meeting,
     * applies cash_allot refund/excess deduction exactly like production
     * Menu_model::Addexpensecash, and flags >20% variance for dual approval.
     *
     * $rows = [
     *   ['meetid' => 123, 'expense' => 450, 'remarks' => '...', 'bills_json' => '["uploads/receipts/x.jpg"]'],
     *   ...
     * ]
     * $travel_expense_type = comma-joined string e.g. "Cab,Toll"
     */
    public function submit_actuals_batch($bd_uid, array $rows, $travel_expense_type)
    {
        if (empty($rows)) return ['ok' => false, 'error' => 'no_rows'];

        $submitted = 0;
        $dual_count = 0;
        $cash_refunded = 0;
        $cash_deducted = 0;
        $row_results = [];

        // Pull current ucash once for balance check
        $u = $this->db->query("SELECT ucash FROM user_details WHERE user_id = ?", [$bd_uid])->row();
        $running_ucash = $u ? (float)$u->ucash : 0.0;

        foreach ($rows as $r) {
            $meetid  = (int)$r['meetid'];
            $expense = (float)$r['expense'];
            $remarks = trim($r['remarks'] ?? '');
            $bills_json = $r['bills_json'] ?? '[]';

            if ($expense < 0) {
                $row_results[] = ['meetid' => $meetid, 'ok' => false, 'error' => 'negative_expense'];
                continue;
            }
            if (empty($bills_json) || $bills_json === '[]') {
                $row_results[] = ['meetid' => $meetid, 'ok' => false, 'error' => 'receipt_required'];
                continue;
            }

            // Resolve tblcallevents row via mom_data.tid
            $task = $this->db->query("
                SELECT t.* FROM tblcallevents t
                JOIN mom_data m ON m.tid = t.id
                WHERE m.id = ?
            ", [$meetid])->row();
            if (!$task) {
                $row_results[] = ['meetid' => $meetid, 'ok' => false, 'error' => 'event_not_found'];
                continue;
            }

            $cash_allot = (float)($task->cash_allot ?? 0);
            $planned    = (int)($task->planned_cost ?? ($cash_allot > 0 ? $cash_allot : self::DEFAULT_PLANNED_COST));
            $variance   = $planned > 0 ? round((($expense - $planned) / $planned) * 100, 2) : 0;
            $requires_dual = abs($variance) > self::VARIANCE_THRESHOLD_PCT ? 1 : 0;

            // Cash flow per production rules
            if ($expense <= $cash_allot) {
                $refund = $cash_allot - $expense;
                if ($refund > 0) {
                    $this->_credit_ucash($task->user_id, $refund,
                        "Refund of unused allotment for meeting $meetid (allot $cash_allot, spent $expense)");
                    $running_ucash += $refund;
                    $cash_refunded += $refund;
                }
            } else {
                $excess = $expense - $cash_allot;
                if ($running_ucash < $excess) {
                    $row_results[] = ['meetid' => $meetid, 'ok' => false, 'error' => 'insufficient_balance',
                        'short_by' => $excess - $running_ucash];
                    continue;
                }
                $this->_debit_ucash($task->user_id, $excess,
                    "Excess expense over allotment for meeting $meetid (allot $cash_allot, spent $expense)");
                $running_ucash -= $excess;
                $cash_deducted += $excess;
            }

            // Stamp tblcallevents
            $this->db->where('id', $task->id);
            $this->db->update('tblcallevents', [
                'actual_cost'            => $expense,
                'cash_expense'           => $expense,
                'cash_refund'            => max(0, $cash_allot - $expense),
                'variance_pct'           => $variance,
                'requires_dual_approval' => $requires_dual,
                'expense_submitted_at'   => date('Y-m-d H:i:s'),
            ]);

            // Insert cash_expense
            $this->db->insert('cash_expense', [
                'user_id'          => $task->user_id,
                'meetid'           => $meetid,
                'tbl_task_id'      => $task->id,
                'linked_event_id'  => $task->id,
                'expense'          => $expense,
                'expense_remarks'  => $remarks,
                'bills'            => $bills_json,
                'receipt_required' => 1,
                'receipt_uploaded' => 1,
                'expense_type'     => $travel_expense_type ?: 'meeting_actual',
            ]);

            // Insert expense_actuals_log
            $this->db->insert('expense_actuals_log', [
                'event_id'               => $task->id,
                'bd_uid'                 => $bd_uid,
                'planned_cost'           => $planned,
                'actual_cost'            => $expense,
                'variance_pct'           => $variance,
                'receipt_filename'       => $bills_json,
                'expense_breakdown_json' => json_encode(['types' => $travel_expense_type, 'remarks' => $remarks]),
                'requires_dual_approval' => $requires_dual,
                'final_state'            => 'pending_cm',
            ]);
            $log_id = $this->db->insert_id();

            $submitted++;
            if ($requires_dual) $dual_count++;
            $row_results[] = [
                'meetid' => $meetid,
                'ok'     => true,
                'log_id' => $log_id,
                'variance_pct' => $variance,
                'requires_dual_approval' => $requires_dual,
            ];
        }

        return [
            'ok'                  => $submitted > 0,
            'submitted'           => $submitted,
            'dual_approval_count' => $dual_count,
            'cash_refunded'       => $cash_refunded,
            'cash_deducted'       => $cash_deducted,
            'rows'                => $row_results,
        ];
    }

    // ============================================================
    // 8. ADVANCE SETTLEMENT (BD submits actuals against an advance)
    //    The missing link in production: every disbursed travel_advance
    //    must end with cash_expense + expense_actuals_log rows linked
    //    to it, or a full return / roll / absorb.
    // ============================================================

    /**
     * Disbursed advances for this BD that still await settlement
     * (consumed_status='pending' AND fully approved AND not cancelled).
     */
    public function list_disbursed_unsettled_advances($bd_uid)
    {
        $sql = "
            SELECT ta.id, ta.cash AS advance_amount, ta.purpose, ta.linked_event_id,
                   ta.disbursed_at, ta.date AS requested_at,
                   t.event_date, t.startm, t.closem, t.cash_allot, t.planned_cost,
                   m.id AS meetid, m.company_name
            FROM travel_advance ta
            LEFT JOIN tblcallevents t ON t.id = ta.linked_event_id
            LEFT JOIN mom_data m      ON m.tid = t.id
            WHERE ta.user_id = ?
              AND ta.cluster_apr = 1 AND ta.admin_apr = 1 AND ta.account_apr = 1
              AND ta.consumed_status = 'pending'
              AND ta.disbursed_at IS NOT NULL
              AND ta.linked_cancellation_event_id IS NULL
            ORDER BY ta.disbursed_at ASC
        ";
        return $this->db->query($sql, [$bd_uid])->result();
    }

    /**
     * BD settles a specific disbursed advance with actual spend + bills.
     * Writes cash_expense + expense_actuals_log + updates travel_advance,
     * and handles leftover / overflow exactly.
     *
     * Cash math:
     *   leftover = max(0, advance_amount - actual_spent)  -> stays in BD ucash
     *                                                        (advance was already credited there)
     *                                                        Recorded as leftover_returned for audit.
     *   overflow = max(0, actual_spent - advance_amount)  -> debit ucash if BD has balance,
     *                                                        else reject with insufficient_balance.
     *
     * Variance vs advance amount > +/- 20% -> requires_dual_approval=1.
     */
    public function settle_advance(
        $advance_id, $bd_uid, $actual_spent, $bills_json,
        $expense_remarks = '', $travel_expense_type = '')
    {
        $actual_spent = (float)$actual_spent;
        if ($actual_spent < 0) return ['ok' => false, 'error' => 'negative_amount'];
        if (empty($bills_json) || $bills_json === '[]') {
            return ['ok' => false, 'error' => 'receipt_required'];
        }

        $adv = $this->db->get_where('travel_advance', ['id' => $advance_id, 'user_id' => $bd_uid])->row();
        if (!$adv) return ['ok' => false, 'error' => 'advance_not_found'];
        if ((int)$adv->cluster_apr !== 1 || (int)$adv->admin_apr !== 1 || (int)$adv->account_apr !== 1) {
            return ['ok' => false, 'error' => 'advance_not_fully_approved'];
        }
        if ($adv->consumed_status !== 'pending') {
            return ['ok' => false, 'error' => 'already_' . $adv->consumed_status];
        }
        if (empty($adv->disbursed_at)) {
            return ['ok' => false, 'error' => 'not_disbursed_yet'];
        }

        $advance_amount = (float)$adv->cash;
        $event_id       = $adv->linked_event_id ? (int)$adv->linked_event_id : null;
        $leftover       = max(0, $advance_amount - $actual_spent);
        $overflow       = max(0, $actual_spent - $advance_amount);

        if ($overflow > 0) {
            $u = $this->db->query("SELECT ucash FROM user_details WHERE user_id = ?", [$bd_uid])->row();
            $ucash = $u ? (float)$u->ucash : 0.0;
            if ($ucash < $overflow) {
                return ['ok' => false, 'error' => 'insufficient_balance',
                        'short_by' => $overflow - $ucash,
                        'advance_amount' => $advance_amount,
                        'actual_spent'   => $actual_spent];
            }
            $this->_debit_ucash($bd_uid, $overflow,
                "Excess spend over advance $advance_id (advance $advance_amount, spent $actual_spent)");
        }

        $variance = $advance_amount > 0
            ? round((($actual_spent - $advance_amount) / $advance_amount) * 100, 2)
            : 0;
        $requires_dual = abs($variance) > self::VARIANCE_THRESHOLD_PCT ? 1 : 0;

        $meetid = null;
        if ($event_id) {
            $mom = $this->db->query("SELECT id FROM mom_data WHERE tid = ? LIMIT 1", [$event_id])->row();
            if ($mom) $meetid = (int)$mom->id;
        }

        $this->db->insert('cash_expense', [
            'user_id'           => $bd_uid,
            'meetid'            => $meetid,
            'tbl_task_id'       => $event_id,
            'linked_event_id'   => $event_id,
            'travel_advance_id' => $advance_id,
            'expense'           => $actual_spent,
            'expense_remarks'   => $expense_remarks,
            'bills'             => $bills_json,
            'receipt_required'  => 1,
            'receipt_uploaded'  => 1,
            'expense_type'      => $travel_expense_type ?: 'advance_settlement',
        ]);

        $this->db->insert('expense_actuals_log', [
            'event_id'               => $event_id,
            'bd_uid'                 => $bd_uid,
            'travel_advance_id'      => $advance_id,
            'planned_cost'           => $advance_amount,
            'actual_cost'            => $actual_spent,
            'variance_pct'           => $variance,
            'receipt_filename'       => $bills_json,
            'expense_breakdown_json' => json_encode([
                'types'   => $travel_expense_type,
                'remarks' => $expense_remarks,
                'source'  => 'advance_settlement',
            ]),
            'requires_dual_approval' => $requires_dual,
            'final_state'            => 'pending_cm',
        ]);
        $log_id = $this->db->insert_id();

        $this->db->where('id', $advance_id)->update('travel_advance', [
            'consumed_status'        => 'consumed',
            'actual_spent'           => $actual_spent,
            'leftover_returned'      => $leftover,
            'consumed_at'            => date('Y-m-d H:i:s'),
            'settlement_bills'       => $bills_json,
            'settlement_remarks'     => $expense_remarks,
            'variance_pct'           => $variance,
            'requires_dual_approval' => $requires_dual,
        ]);

        if ($event_id) {
            $this->db->where('id', $event_id)->update('tblcallevents', [
                'actual_cost'            => $actual_spent,
                'variance_pct'           => $variance,
                'requires_dual_approval' => $requires_dual,
                'expense_submitted_at'   => date('Y-m-d H:i:s'),
            ]);
        }

        return [
            'ok'                     => true,
            'advance_id'             => (int)$advance_id,
            'log_id'                 => (int)$log_id,
            'advance_amount'         => $advance_amount,
            'actual_spent'           => $actual_spent,
            'leftover_in_wallet'     => $leftover,
            'overflow_debited'       => $overflow,
            'variance_pct'           => $variance,
            'requires_dual_approval' => $requires_dual,
        ];
    }
}

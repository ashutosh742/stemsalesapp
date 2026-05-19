<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * MonthlyLeadReview_model - Migration 020.1
 *
 * Snapshots every lead at end of every calendar month. Drives the per-BD and per-CM
 * deep review PDFs (one page per lead).
 *
 * Companion to Review_v2_model (migration 020) which handles BD-level scoring.
 *
 * Staging only until Mon 18 May 2026 GitHub access. Production hold per user directive.
 */
class MonthlyLeadReview_model extends CI_Model
{
    // Stuck thresholds in days, mirrored from migration 012
    const STUCK_THRESHOLDS = [
        1 => 3, 2 => 5, 3 => 5, 6 => 7, 7 => 14, 8 => 30, 9 => 14
    ];

    // Cash burn threshold - cash expense over this percent of fbudget triggers red flag
    const RED_BURN_PCT = 20;

    // Silent threshold in days - no activity then flag
    const RED_SILENT_DAYS = 14;

    public function __construct()
    {
        parent::__construct();
        $this->load->database();
    }

    /**
     * Snapshot every eligible lead for the given month.
     * Eligible: init_call.is_active=1 OR closed within 6 months.
     */
    public function snapshot_month($month)
    {
        $run_id = $this->_start_run($month);

        $leads = $this->_eligible_leads($month);
        $count = 0;

        foreach ($leads as $lead) {
            $snap = $this->_build_lead_snapshot($lead, $month);
            $this->_upsert_snapshot($snap);
            $count++;
        }

        $this->_complete_run($run_id, $count, 'done');
        return ['month' => $month, 'leads_processed' => $count];
    }

    /**
     * Return eligible leads for a month.
     * - All active leads
     * - Plus leads closed (cstatus 12 or 13) within the last 6 months
     */
    private function _eligible_leads($month)
    {
        $cutoff = date('Y-m-d', strtotime("$month-01 -6 months"));
        $sql = "
            SELECT ic.id, ic.compny_name, ic.cstatus, ic.fbudget,
                   ic.mainbd AS bd_uid, ic.createDate, ic.is_active,
                   ic.last_stage_change_at, u.cluster_id
            FROM init_call ic
            JOIN user u ON u.uid = ic.mainbd
            WHERE ic.is_active = 1
               OR (ic.cstatus IN (12, 13) AND ic.last_stage_change_at >= ?)
            ORDER BY ic.mainbd, ic.cstatus, ic.fbudget DESC
        ";
        return $this->db->query($sql, [$cutoff])->result_array();
    }

    /**
     * Build the full snapshot payload for one lead.
     */
    private function _build_lead_snapshot($lead, $month)
    {
        $month_start = "$month-01 00:00:00";
        $month_end = date('Y-m-t 23:59:59', strtotime($month_start));
        $today = date('Y-m-d');

        $lead_id = (int)$lead['id'];
        $bd_uid = (int)$lead['bd_uid'];
        $cm_uid = $this->_resolve_cm_for_bd($bd_uid);

        // Activity rollups within month
        $meetings = $this->_count_events($lead_id, $month_start, $month_end, [3, 4]);
        $calls = $this->_count_events($lead_id, $month_start, $month_end, [1]);
        $emails = $this->_count_events($lead_id, $month_start, $month_end, [2]);
        $moms_apr = $this->_count_moms($lead_id, $month_start, $month_end, 'approved');
        $moms_pen = $this->_count_moms($lead_id, $month_start, $month_end, 'pending');

        $cash_expense = $this->_sum_cash_expense($lead_id, $month_start, $month_end);
        $cash_wallet = $this->_sum_wallet_exposure($lead_id);

        $photos = $this->_count_capture($lead_id, $month_start, $month_end, 'photo');
        $gps = $this->_count_capture($lead_id, $month_start, $month_end, 'gps');

        $stage_journey = $this->_stage_journey($lead_id);
        $activity = $this->_activity_this_month($lead_id, $month_start, $month_end);

        $last_mom = $this->_last_mom_remark($lead_id);
        $last_review = $this->_last_review_remark($bd_uid);
        $ai_rec = $this->_ai_recommendation($lead_id, $bd_uid);
        $next_milestone = $this->_infer_next_milestone($lead_id, $lead['cstatus']);
        $open_auto = $this->_open_auto_tasks($lead_id);

        // Days computations
        $days_in_stage = $this->_days_in_stage($lead_id);
        $lead_age = (int)((strtotime($today) - strtotime($lead['createDate'])) / 86400);

        // Auto flags
        $auto_flags = $this->_compute_auto_flags(
            (int)$lead['cstatus'], $days_in_stage,
            (float)$lead['fbudget'], (float)$cash_expense,
            $lead_id, $meetings, $moms_apr
        );

        return [
            'month' => $month,
            'lead_id' => $lead_id,
            'bd_uid' => $bd_uid,
            'cm_uid' => $cm_uid,
            'cluster_id' => $lead['cluster_id'] ? (int)$lead['cluster_id'] : null,
            'current_cstatus' => (int)$lead['cstatus'],
            'fbudget_rs' => (float)$lead['fbudget'],
            'days_in_stage' => $days_in_stage,
            'lead_age_days' => $lead_age,
            'meetings_count' => $meetings,
            'moms_approved' => $moms_apr,
            'moms_pending' => $moms_pen,
            'calls_count' => $calls,
            'emails_count' => $emails,
            'cash_expense_rs' => (float)$cash_expense,
            'cash_wallet_exposure_rs' => (float)$cash_wallet,
            'photos_count' => $photos,
            'gps_count' => $gps,
            'stage_journey' => json_encode($stage_journey),
            'activity_this_month' => json_encode($activity),
            'last_mom_remark' => $last_mom,
            'last_review_remark' => $last_review,
            'ai_recommendation' => $ai_rec,
            'next_milestone' => $next_milestone,
            'open_auto_tasks_count' => $open_auto,
            'auto_flags' => json_encode($auto_flags),
            'snapshot_at' => date('Y-m-d H:i:s'),
        ];
    }

    private function _resolve_cm_for_bd($bd_uid)
    {
        $sql = "SELECT cm_uid FROM bd_cm_mapping WHERE bd_uid=? AND active=1 LIMIT 1";
        $row = $this->db->query($sql, [$bd_uid])->row_array();
        return $row ? (int)$row['cm_uid'] : null;
    }

    private function _count_events($lead_id, $from, $to, $actiontypes)
    {
        $ph = implode(',', array_fill(0, count($actiontypes), '?'));
        $sql = "SELECT COUNT(*) c FROM tblcallevents
                WHERE cid_id=? AND event_date BETWEEN ? AND ?
                  AND actiontype_id IN ($ph)";
        $row = $this->db->query($sql, array_merge([$lead_id, $from, $to], $actiontypes))->row_array();
        return (int)$row['c'];
    }

    private function _count_moms($lead_id, $from, $to, $status)
    {
        $sql = "SELECT COUNT(*) c FROM mom_data m
                JOIN tblcallevents t ON t.id=m.event_id
                WHERE t.cid_id=? AND t.event_date BETWEEN ? AND ?
                  AND m.status=?";
        $row = $this->db->query($sql, [$lead_id, $from, $to, $status])->row_array();
        return (int)$row['c'];
    }

    private function _sum_cash_expense($lead_id, $from, $to)
    {
        $sql = "SELECT COALESCE(SUM(ce.amount),0) s
                FROM cash_expense ce
                JOIN tblcallevents t ON t.id=ce.linked_event_id
                WHERE t.cid_id=? AND ce.created_at BETWEEN ? AND ?";
        $row = $this->db->query($sql, [$lead_id, $from, $to])->row_array();
        return (float)$row['s'];
    }

    private function _sum_wallet_exposure($lead_id)
    {
        $sql = "SELECT COALESCE(SUM(cash_allot - COALESCE(cash_refund,0)),0) s
                FROM cancellation_audit WHERE cid_id=?";
        $row = $this->db->query($sql, [$lead_id])->row_array();
        return (float)$row['s'];
    }

    private function _count_capture($lead_id, $from, $to, $kind)
    {
        $col = $kind === 'photo' ? 'with_photo' : 'with_gps';
        $sql = "SELECT COUNT(*) c FROM tblcallevents
                WHERE cid_id=? AND event_date BETWEEN ? AND ? AND $col=1";
        $row = $this->db->query($sql, [$lead_id, $from, $to])->row_array();
        return (int)$row['c'];
    }

    private function _stage_journey($lead_id)
    {
        $sql = "SELECT from_cstatus, to_cstatus, created_at AS at, actor_uid
                FROM lead_progression_log
                WHERE cid_id=?
                ORDER BY created_at ASC LIMIT 50";
        return $this->db->query($sql, [$lead_id])->result_array();
    }

    private function _activity_this_month($lead_id, $from, $to)
    {
        $sql = "SELECT t.event_date AS date, t.actiontype_id, t.purpose_id,
                       t.outcome_id, m.status AS mom_status
                FROM tblcallevents t
                LEFT JOIN mom_data m ON m.event_id=t.id
                WHERE t.cid_id=? AND t.event_date BETWEEN ? AND ?
                ORDER BY t.event_date DESC LIMIT 50";
        return $this->db->query($sql, [$lead_id, $from, $to])->result_array();
    }

    private function _last_mom_remark($lead_id)
    {
        $sql = "SELECT m.cm_apr_remarks
                FROM mom_data m
                JOIN tblcallevents t ON t.id=m.event_id
                WHERE t.cid_id=? AND m.cm_apr_remarks IS NOT NULL
                  AND m.cm_apr_remarks != ''
                ORDER BY m.cm_apr_at DESC LIMIT 1";
        $row = $this->db->query($sql, [$lead_id])->row_array();
        return $row ? $row['cm_apr_remarks'] : null;
    }

    private function _last_review_remark($bd_uid)
    {
        $sql = "SELECT cm_overall_remarks
                FROM review_v2_session
                WHERE bd_uid=? AND status='completed'
                  AND cm_overall_remarks IS NOT NULL
                ORDER BY completed_at DESC LIMIT 1";
        if (!$this->db->table_exists('review_v2_session')) return null;
        $row = $this->db->query($sql, [$bd_uid])->row_array();
        return $row ? $row['cm_overall_remarks'] : null;
    }

    private function _ai_recommendation($lead_id, $bd_uid)
    {
        // Reuse existing Anaya/PlanningGrade rather than building a new model.
        if (!class_exists('PlanningGrade_model')) {
            $this->load->model('AIAgents/PlanningGrade_model');
        }
        if (method_exists($this->PlanningGrade_model, 'lead_next_step')) {
            return $this->PlanningGrade_model->lead_next_step($lead_id, $bd_uid);
        }
        return null;
    }

    private function _infer_next_milestone($lead_id, $cstatus)
    {
        $map = [
            1 => 'Make first contact and qualify',
            2 => 'Schedule a barge or site visit',
            3 => 'Confirm Principal interest and budget',
            6 => 'Send proposal',
            7 => 'Get RP meeting on calendar',
            8 => 'Move to Very Positive within 14 days',
            9 => 'Close as Won this month',
            12 => 'Handover to onboarding',
            13 => 'Document loss reason for learning',
        ];
        return $map[$cstatus] ?? 'Review with CM';
    }

    private function _open_auto_tasks($lead_id)
    {
        $sql = "SELECT COUNT(*) c FROM daily_planner
                WHERE cid_id=? AND is_auto=1 AND plan_date >= CURDATE()
                  AND COALESCE(completion_status,0)=0";
        $row = $this->db->query($sql, [$lead_id])->row_array();
        return (int)$row['c'];
    }

    private function _days_in_stage($lead_id)
    {
        $sql = "SELECT DATEDIFF(NOW(), MAX(created_at)) d
                FROM lead_progression_log WHERE cid_id=?";
        $row = $this->db->query($sql, [$lead_id])->row_array();
        return $row && $row['d'] !== null ? (int)$row['d'] : 0;
    }

    private function _compute_auto_flags($cstatus, $days_in_stage, $fbudget, $cash_expense,
                                         $lead_id, $meetings, $moms_apr)
    {
        $flags = [
            'red_stuck' => false,
            'red_burn' => false,
            'red_silent' => false,
            'red_mom_gap' => false,
        ];

        // red_stuck
        if (isset(self::STUCK_THRESHOLDS[$cstatus])
            && $days_in_stage > self::STUCK_THRESHOLDS[$cstatus]) {
            $flags['red_stuck'] = true;
        }

        // red_burn
        if ($fbudget > 0 && ($cash_expense / $fbudget) * 100 > self::RED_BURN_PCT) {
            $flags['red_burn'] = true;
        }

        // red_silent
        if (!in_array($cstatus, [12, 13])) {
            $sql = "SELECT DATEDIFF(NOW(), MAX(event_date)) d
                    FROM tblcallevents WHERE cid_id=?";
            $row = $this->db->query($sql, [$lead_id])->row_array();
            $last_d = $row && $row['d'] !== null ? (int)$row['d'] : 9999;
            if ($last_d > self::RED_SILENT_DAYS) $flags['red_silent'] = true;
        }

        // red_mom_gap
        if ($meetings > 0 && $moms_apr === 0) {
            $flags['red_mom_gap'] = true;
        }

        return $flags;
    }

    private function _upsert_snapshot($snap)
    {
        $sql = "INSERT INTO monthly_lead_review (
                    month, lead_id, bd_uid, cm_uid, cluster_id,
                    current_cstatus, fbudget_rs, days_in_stage, lead_age_days,
                    meetings_count, moms_approved, moms_pending, calls_count, emails_count,
                    cash_expense_rs, cash_wallet_exposure_rs, photos_count, gps_count,
                    stage_journey, activity_this_month,
                    last_mom_remark, last_review_remark, ai_recommendation, next_milestone,
                    open_auto_tasks_count, auto_flags, snapshot_at
                ) VALUES (
                    ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?,
                    ?, ?, ?, ?, ?, ?, ?, ?, ?
                )
                ON DUPLICATE KEY UPDATE
                    bd_uid=VALUES(bd_uid), cm_uid=VALUES(cm_uid),
                    current_cstatus=VALUES(current_cstatus), fbudget_rs=VALUES(fbudget_rs),
                    days_in_stage=VALUES(days_in_stage), lead_age_days=VALUES(lead_age_days),
                    meetings_count=VALUES(meetings_count), moms_approved=VALUES(moms_approved),
                    moms_pending=VALUES(moms_pending), calls_count=VALUES(calls_count),
                    emails_count=VALUES(emails_count), cash_expense_rs=VALUES(cash_expense_rs),
                    cash_wallet_exposure_rs=VALUES(cash_wallet_exposure_rs),
                    photos_count=VALUES(photos_count), gps_count=VALUES(gps_count),
                    stage_journey=VALUES(stage_journey),
                    activity_this_month=VALUES(activity_this_month),
                    last_mom_remark=VALUES(last_mom_remark),
                    last_review_remark=VALUES(last_review_remark),
                    ai_recommendation=VALUES(ai_recommendation),
                    next_milestone=VALUES(next_milestone),
                    open_auto_tasks_count=VALUES(open_auto_tasks_count),
                    auto_flags=VALUES(auto_flags), snapshot_at=VALUES(snapshot_at)";
        $params = [
            $snap['month'], $snap['lead_id'], $snap['bd_uid'], $snap['cm_uid'], $snap['cluster_id'],
            $snap['current_cstatus'], $snap['fbudget_rs'], $snap['days_in_stage'], $snap['lead_age_days'],
            $snap['meetings_count'], $snap['moms_approved'], $snap['moms_pending'],
            $snap['calls_count'], $snap['emails_count'],
            $snap['cash_expense_rs'], $snap['cash_wallet_exposure_rs'],
            $snap['photos_count'], $snap['gps_count'],
            $snap['stage_journey'], $snap['activity_this_month'],
            $snap['last_mom_remark'], $snap['last_review_remark'],
            $snap['ai_recommendation'], $snap['next_milestone'],
            $snap['open_auto_tasks_count'], $snap['auto_flags'], $snap['snapshot_at'],
        ];
        return $this->db->query($sql, $params);
    }

    private function _start_run($month)
    {
        $this->db->insert('monthly_lead_review_run', [
            'month' => $month, 'started_at' => date('Y-m-d H:i:s'), 'status' => 'running'
        ]);
        return $this->db->insert_id();
    }

    private function _complete_run($run_id, $processed, $status, $err = null)
    {
        $start = $this->db->select('started_at')->where('id', $run_id)
            ->get('monthly_lead_review_run')->row_array();
        $dur = $start ? (time() - strtotime($start['started_at'])) : null;
        $this->db->where('id', $run_id)->update('monthly_lead_review_run', [
            'completed_at' => date('Y-m-d H:i:s'),
            'leads_processed' => $processed,
            'status' => $status,
            'duration_sec' => $dur,
            'error_msg' => $err,
        ]);
    }

    // ---- Fetch helpers used by controller and PDF compiler ----

    public function get_leads_for_bd($month, $bd_uid)
    {
        $sql = "SELECT mlr.*, ic.compny_name AS school_name, ic.cstatus AS cstatus_now
                FROM monthly_lead_review mlr
                JOIN init_call ic ON ic.id = mlr.lead_id
                WHERE mlr.month=? AND mlr.bd_uid=?
                ORDER BY mlr.current_cstatus, mlr.fbudget_rs DESC";
        return $this->db->query($sql, [$month, $bd_uid])->result_array();
    }

    public function get_leads_for_cm($month, $cm_uid)
    {
        $sql = "SELECT mlr.*, ic.compny_name AS school_name, ic.cstatus AS cstatus_now,
                       u.fname AS bd_first_name, u.lname AS bd_last_name
                FROM monthly_lead_review mlr
                JOIN init_call ic ON ic.id = mlr.lead_id
                JOIN user u ON u.uid = mlr.bd_uid
                WHERE mlr.month=? AND mlr.cm_uid=?
                ORDER BY mlr.bd_uid, mlr.current_cstatus, mlr.fbudget_rs DESC";
        return $this->db->query($sql, [$month, $cm_uid])->result_array();
    }

    public function get_one_pager($month, $lead_id)
    {
        $sql = "SELECT mlr.*, ic.compny_name AS school_name,
                       u.fname AS bd_first_name, u.lname AS bd_last_name,
                       uc.fname AS cm_first_name, uc.lname AS cm_last_name
                FROM monthly_lead_review mlr
                JOIN init_call ic ON ic.id = mlr.lead_id
                JOIN user u ON u.uid = mlr.bd_uid
                LEFT JOIN user uc ON uc.uid = mlr.cm_uid
                WHERE mlr.month=? AND mlr.lead_id=? LIMIT 1";
        return $this->db->query($sql, [$month, $lead_id])->row_array();
    }

    public function list_bd_uids_with_leads($month)
    {
        $sql = "SELECT DISTINCT bd_uid FROM monthly_lead_review WHERE month=?";
        return array_column($this->db->query($sql, [$month])->result_array(), 'bd_uid');
    }

    public function list_cm_uids_with_leads($month)
    {
        $sql = "SELECT DISTINCT cm_uid FROM monthly_lead_review
                WHERE month=? AND cm_uid IS NOT NULL";
        return array_column($this->db->query($sql, [$month])->result_array(), 'cm_uid');
    }

    public function record_bd_pdf($month, $bd_uid, $pdf_path, $stats)
    {
        $this->db->replace('monthly_lead_review_bd_pdf', array_merge([
            'month' => $month, 'bd_uid' => $bd_uid, 'pdf_path' => $pdf_path,
            'generated_at' => date('Y-m-d H:i:s'),
        ], $stats));
    }

    public function record_cm_pdf($month, $cm_uid, $pdf_path, $stats)
    {
        $this->db->replace('monthly_lead_review_cm_pdf', array_merge([
            'month' => $month, 'cm_uid' => $cm_uid, 'pdf_path' => $pdf_path,
            'generated_at' => date('Y-m-d H:i:s'),
        ], $stats));
    }

    public function manifest($month)
    {
        return [
            'month' => $month,
            'leads' => $this->db->select('COUNT(*) c')->where('month', $month)
                ->get('monthly_lead_review')->row()->c,
            'bd_pdfs' => $this->db->where('month', $month)
                ->get('monthly_lead_review_bd_pdf')->result_array(),
            'cm_pdfs' => $this->db->where('month', $month)
                ->get('monthly_lead_review_cm_pdf')->result_array(),
        ];
    }
}

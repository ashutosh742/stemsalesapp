<?php
defined('BASEPATH') OR exit('No direct script access allowed');
date_default_timezone_set('Asia/Kolkata');

/**
 * DisciplineState_model
 *
 * Provides all data queries needed by the DisciplineApi controller and the
 * PlannerRequestApi / DayCloseOverrideApi controllers. Every public method
 * returns either a plain value (int, bool, string) or a keyed array so that
 * callers never have to dereference raw result objects.
 *
 * Table references (all added in migration 081 unless noted otherwise):
 *   user_day            - existing
 *   autotask_time       - existing
 *   tblcallevents       - existing
 *   user_details        - existing
 *   user_type           - existing
 *   notify              - existing
 *   task_plan_for_today - existing + migration 081 adds approver_name, approver_role, header_label
 *   request_old_pend_task - existing + migration 081 adds approver_name, approver_role, pbni_count
 *   approver_override   - NEW in 081
 *   day_close_override  - NEW in 081
 *   pbni_alert          - NEW in 081
 *   discipline_audit    - NEW in 081
 */
class DisciplineState_model extends CI_Model {

    public function __construct() {
        parent::__construct();
    }

    // -------------------------------------------------------------------------
    // Day state helpers
    // -------------------------------------------------------------------------

    /**
     * get_day_state
     *
     * Returns an associative array describing whether the user started their
     * day today and whether it is already closed.
     *
     * Keys: day_started (bool), day_start_time (string|null), day_closed (bool)
     */
    public function get_day_state($uid) {
        $today = date('Y-m-d');
        $query = $this->db->query(
            "SELECT sdatet, ustart, uclose
             FROM user_day
             WHERE user_id = '$uid'
               AND CAST(sdatet AS DATE) = '$today'
             ORDER BY id DESC
             LIMIT 1"
        );
        $rows = $query->result();
        if (empty($rows)) {
            return [
                'day_started'    => false,
                'day_start_time' => null,
                'day_closed'     => false,
            ];
        }
        $row = $rows[0];
        return [
            'day_started'    => true,
            'day_start_time' => $row->ustart,
            'day_closed'     => !empty($row->uclose),
        ];
    }

    // -------------------------------------------------------------------------
    // Count helpers (each returns an integer)
    // -------------------------------------------------------------------------

    /**
     * get_pbni_count
     *
     * Count of tasks that were planned for a date before today but were never
     * initiated (nextCFID = 0). These are "Plan But Not Initiated" rows.
     * Mirrors get_all_old_cmp_planbutnotinited in Menu_model.
     */
    public function get_pbni_count($uid) {
        $query = $this->db->query(
            "SELECT COUNT(*) AS cnt
             FROM tblcallevents
             WHERE assignedto_id = '$uid'
               AND actiontype_id != ''
               AND plan = 1
               AND nextCFID = 0
               AND DATE(appointmentdatetime) < CURDATE()
               AND appointmentdatetime != '0000-00-00 00:00:00'
               AND (delete_request = '' OR delete_request IS NULL)"
        );
        $rows = $query->result();
        return (int) ($rows[0]->cnt ?? 0);
    }

    /**
     * get_pending_autotask_count
     *
     * Count of autotask rows from prior days that are still incomplete.
     * Mirrors get_PendingAutoTask in Menu_model.
     */
    public function get_pending_autotask_count($uid) {
        $query = $this->db->query(
            "SELECT COUNT(*) AS cnt
             FROM tblcallevents
             WHERE assignedto_id = '$uid'
               AND actiontype_id != ''
               AND nextCFID = 0
               AND autotask = 1
               AND plan = 1
               AND DATE(appointmentdatetime) < CURDATE()
               AND appointmentdatetime != '0000-00-00 00:00:00'"
        );
        $rows = $query->result();
        return (int) ($rows[0]->cnt ?? 0);
    }

    /**
     * get_rp_mom_count
     *
     * Count of RP meetings for which a MoM has not been written yet.
     * Mirrors GetPendingForWriteMomMeetingList for a single user.
     */
    public function get_rp_mom_count($uid) {
        $query = $this->db->query(
            "SELECT COUNT(*) AS cnt
             FROM tblcallevents
             LEFT JOIN barginmeeting ON barginmeeting.tid = tblcallevents.id
             WHERE tblcallevents.assignedto_id = '$uid'
               AND tblcallevents.actiontype_id IN (3, 4, 17)
               AND tblcallevents.nextCFID != 0
               AND tblcallevents.plan = 1
               AND tblcallevents.approved_status = 1
               AND (barginmeeting.status = 'Close' OR barginmeeting.status = 'RPClose')
               AND tblcallevents.mom IS NULL"
        );
        $rows = $query->result();
        return (int) ($rows[0]->cnt ?? 0);
    }

    /**
     * get_meeting_expense_count
     *
     * Count of today's meetings whose expense entry has not been filled.
     * Mirrors GetTodaysMeetingsDetails in Menu_model (non-null check).
     */
    public function get_meeting_expense_count($uid) {
        $today = date('Y-m-d');
        $query = $this->db->query(
            "SELECT COUNT(*) AS cnt
             FROM barginmeeting
             LEFT JOIN tblcallevents AS tcl ON tcl.id = barginmeeting.tid
             WHERE barginmeeting.user_id = '$uid'
               AND tcl.actiontype_id IN (3, 4, 17)
               AND tcl.nextCFID != 0
               AND tcl.plan = 1
               AND DATE(tcl.appointmentdatetime) = '$today'
               AND tcl.approved_status = 1
               AND NOT EXISTS (
                   SELECT 1 FROM cash_expense
                   WHERE cash_expense.meetid = barginmeeting.id
               )"
        );
        $rows = $query->result();
        return (int) ($rows[0]->cnt ?? 0);
    }

    /**
     * get_research_not_updated_count
     *
     * Count of research tasks completed but company data not yet updated.
     * Mirrors ForGetToUpdateLeadsAfterDoneResearch for a direct BD uid.
     */
    public function get_research_not_updated_count($uid) {
        $query = $this->db->query(
            "SELECT COUNT(*) AS cnt
             FROM tblcallevents
             LEFT JOIN init_call ON init_call.id = tblcallevents.cid_id
             LEFT JOIN company_master ON company_master.id = init_call.cmpid_id
             WHERE tblcallevents.user_id = '$uid'
               AND tblcallevents.actiontype_id = 10
               AND tblcallevents.nextCFID != 0
               AND init_call.new_lead = 1
               AND init_call.is_admin_approved = 0
               AND company_master.compname = 'Unknown'
               AND tblcallevents.self_assign = ''"
        );
        $rows = $query->result();
        return (int) ($rows[0]->cnt ?? 0);
    }

    /**
     * get_new_lead_reupdate_count
     *
     * Count of new leads that the BD created today requiring a re-update.
     * Mirrors GetReUpdateNewLeadComapny for a direct BD uid.
     */
    public function get_new_lead_reupdate_count($uid) {
        $query = $this->db->query(
            "SELECT COUNT(*) AS cnt
             FROM init_call
             LEFT JOIN tblcallevents ON tblcallevents.id = init_call.after_task
             WHERE tblcallevents.user_id = '$uid'
               AND tblcallevents.nextCFID != 0
               AND init_call.new_lead = 1
               AND init_call.is_admin_approved = 2
               AND init_call.mainbd = '$uid'"
        );
        $rows = $query->result();
        return (int) ($rows[0]->cnt ?? 0);
    }

    // -------------------------------------------------------------------------
    // Cutoff helpers
    // -------------------------------------------------------------------------

    /**
     * get_cutoff_state
     *
     * Reads autotask_time for the given uid and date (uses today's row).
     * Returns an array: cutoff_time (string HH:MM:SS or null), cutoff_passed (bool),
     * planner_locked (bool). planner_locked is true when cutoff has passed and
     * no approved same-day request exists.
     */
    public function get_cutoff_state($uid, $date) {
        $query = $this->db->query(
            "SELECT start_tttpft, end_tttpft
             FROM autotask_time
             WHERE user_id = '$uid'
               AND date = '$date'
             LIMIT 1"
        );
        $rows = $query->result();
        if (empty($rows)) {
            return [
                'cutoff_time'   => null,
                'cutoff_passed' => false,
                'planner_locked' => false,
            ];
        }
        $cutoff_time   = $rows[0]->start_tttpft;
        $current_time  = date('H:i:s');
        $cutoff_passed = ($current_time >= $cutoff_time);
        return [
            'cutoff_time'    => $cutoff_time,
            'cutoff_passed'  => $cutoff_passed,
            'planner_locked' => $cutoff_passed,
        ];
    }

    // -------------------------------------------------------------------------
    // Line manager resolution
    // -------------------------------------------------------------------------

    /**
     * get_line_manager
     *
     * Resolves the approver for a given uid using the spec-mandated hierarchy:
     *   1. Check approver_override table first. If a row exists, use override_to_uid.
     *   2. Otherwise read user_details.type_id and pick the approver column:
     *      type_id 4 (PST)  -> admin_id
     *      type_id 5 (PC)   -> aadmin
     *      type_id 3 (BD)   -> aadmin
     *      type_id 13 (CM)  -> pst_co
     *      else             -> aadmin
     *
     * Returns an array {name, role, uid} or null when the approver cannot be found.
     */
    public function get_line_manager($uid) {
        // Step 1: check approver_override.
        $override_query = $this->db->query(
            "SELECT override_to_uid
             FROM approver_override
             WHERE uid = '$uid'
             LIMIT 1"
        );
        $override_rows = $override_query->result();

        if (!empty($override_rows)) {
            $approver_uid = (int) $override_rows[0]->override_to_uid;
        } else {
            // Step 2: read the requester's user_details row.
            $ud_query = $this->db->query(
                "SELECT type_id, admin_id, aadmin, pst_co
                 FROM user_details
                 WHERE user_id = '$uid'
                 LIMIT 1"
            );
            $ud_rows = $ud_query->result();
            if (empty($ud_rows)) {
                return null;
            }
            $ud       = $ud_rows[0];
            $type_id  = (int) $ud->type_id;

            if ($type_id === 4) {
                $approver_uid = (int) $ud->admin_id;
            } elseif ($type_id === 13) {
                // Cluster Manager approver: pst_co is the spec-primary, but some
                // CMs have pst_co = 0. Fall back to admin_id then aadmin so the
                // approver always resolves and the clear-request action stays
                // usable. Additive: CMs that already resolve via pst_co are
                // unchanged because pst_co is tried first.
                $approver_uid = (int) $ud->pst_co;
                if (empty($approver_uid)) { $approver_uid = (int) $ud->admin_id; }
                if (empty($approver_uid)) { $approver_uid = (int) $ud->aadmin; }
            } elseif (in_array($type_id, [3, 5], true)) {
                $approver_uid = (int) $ud->aadmin;
            } else {
                $approver_uid = (int) $ud->aadmin;
            }
        }

        if (empty($approver_uid)) {
            return null;
        }

        // Step 3: fetch name and role for the resolved approver.
        $lm_query = $this->db->query(
            "SELECT ud.name, ut.name AS role_name
             FROM user_details ud
             LEFT JOIN user_type ut ON ut.id = ud.type_id
             WHERE ud.user_id = '$approver_uid'
             LIMIT 1"
        );
        $lm_rows = $lm_query->result();
        if (empty($lm_rows)) {
            return null;
        }
        return [
            'name' => $lm_rows[0]->name,
            'role' => $lm_rows[0]->role_name,
            'uid'  => $approver_uid,
        ];
    }

    // -------------------------------------------------------------------------
    // Request row lookups
    // -------------------------------------------------------------------------

    /**
     * get_today_same_day_request
     *
     * Returns the latest same-day planning request row for the given uid and date,
     * or null if none exists.
     */
    public function get_today_same_day_request($uid, $date) {
        $query = $this->db->query(
            "SELECT id, approvel_status, approver_name, approver_role, admin_id,
                    apr_time, remarks
             FROM task_plan_for_today
             WHERE user_id = '$uid'
               AND date = '$date'
             ORDER BY id DESC
             LIMIT 1"
        );
        $rows = $query->result();
        if (empty($rows)) {
            return null;
        }
        return $rows[0];
    }

    /**
     * get_today_yesterday_request
     *
     * Returns the most recent yesterday-request row for the given uid where
     * the req_date falls on yesterday, or null if none.
     */
    public function get_today_yesterday_request($uid) {
        $yesterday = date('Y-m-d', strtotime('-1 day'));
        $query = $this->db->query(
            "SELECT id, approvel_status, approver_name, approver_role,
                    pbni_count, req_date
             FROM request_old_pend_task
             WHERE user_id = '$uid'
               AND CAST(req_date AS DATE) = '$yesterday'
             ORDER BY id DESC
             LIMIT 1"
        );
        $rows = $query->result();
        if (empty($rows)) {
            return null;
        }
        return $rows[0];
    }

    /**
     * get_today_day_close_override
     *
     * Returns the most recent day_close_override row for the given uid and
     * req_date, or null if none exists.
     */
    public function get_today_day_close_override($uid, $date) {
        $query = $this->db->query(
            "SELECT id, approvel_status, remarks, apr_time
             FROM day_close_override
             WHERE user_id = '$uid'
               AND req_date = '$date'
             ORDER BY id DESC
             LIMIT 1"
        );
        $rows = $query->result();
        if (empty($rows)) {
            return null;
        }
        return $rows[0];
    }

    // -------------------------------------------------------------------------
    // PBNI alert helpers
    // -------------------------------------------------------------------------

    /**
     * get_pbni_alert_state
     *
     * Returns an array with pbni_alert_pending (bool) and pbni_alert_approved (bool)
     * based on the most recent pbni_alert row for today.
     */
    public function get_pbni_alert_state($uid) {
        $uid = (int) $uid;
        // Authoritative read: the gate is released when ANY pbni_alert row for
        // this user today is Approved. Counting Approved and Pending separately
        // (instead of reading the latest row by id) means a newer duplicate
        // Pending row can no longer hide an Approved row and re-block the gate.
        $query = $this->db->query(
            "SELECT
                 SUM(CASE WHEN approval_status = 'Approved' THEN 1 ELSE 0 END) AS approved_cnt,
                 SUM(CASE WHEN approval_status = 'Pending'  THEN 1 ELSE 0 END) AS pending_cnt
             FROM pbni_alert
             WHERE user_id = '$uid'
               AND DATE(notified_at) = CURDATE()"
        );
        $row = $query->row();
        $approved_cnt = ($row && $row->approved_cnt !== null) ? (int) $row->approved_cnt : 0;
        $pending_cnt  = ($row && $row->pending_cnt  !== null) ? (int) $row->pending_cnt  : 0;

        // Approved wins: once today has an Approved row the gate stays released
        // even if a stray Pending row also exists for today.
        return [
            'pbni_alert_pending'  => ($approved_cnt === 0 && $pending_cnt > 0),
            'pbni_alert_approved' => ($approved_cnt > 0),
        ];
    }

    // -------------------------------------------------------------------------
    // next_required_screen computation
    // -------------------------------------------------------------------------

    /**
     * compute_next_required_screen
     *
     * Accepts a state dictionary (the assembled response array from the controller)
     * and returns a two-element array: [screen_name, action_label, block_reason].
     *
     * Routing order mirrors spec section 5 exactly.
     */
    public function compute_next_required_screen($s) {
        // Gate 1: day not started.
        if (!$s['day_started']) {
            return ['DayCeremonyV2', 'start_day', null];
        }

        // Gate 2: PBNI hard block (planned-but-not-initiated rows exist and LM
        //         has not approved the pbni_alert yet).
        if ($s['pbni_count'] > 0 && !$s['pbni_alert_approved']) {
            return ['DayManagement', 'clear_pbni', 'PBNI block: ' . $s['pbni_count'] . ' planned tasks from previous days were never started. LM approval required.'];
        }

        // Gate 3: pending autotask rows.
        if ($s['pending_autotask_count'] > 0) {
            return ['DayManagement', 'clear_autotask', 'Pending autotask block: ' . $s['pending_autotask_count'] . ' autotask(s) from previous days.'];
        }

        // Gate 4: research tasks not updated.
        if ($s['research_not_updated_count'] > 0) {
            return ['Dashboard', 'update_research', 'Research update required for ' . $s['research_not_updated_count'] . ' lead(s).'];
        }

        // Gate 5: RP meeting MoM pending.
        if ($s['rp_mom_pending_count'] > 0) {
            return ['PendingForWriteMomMeetingList', 'write_mom', 'MoM required for ' . $s['rp_mom_pending_count'] . ' RP meeting(s).'];
        }

        // Gate 6: meeting expense not filled.
        if ($s['meeting_expense_pending_count'] > 0) {
            return ['UpdateTodaysMeetingsDetails', 'fill_expense', 'Meeting expense entry required for ' . $s['meeting_expense_pending_count'] . ' meeting(s).'];
        }

        // Gate 7: planner locked and same-day request not yet approved.
        $sd_req = $s['same_day_request'];
        if ($s['today_planner_locked'] && (!$sd_req['exists'] || $sd_req['status'] !== 'Approved')) {
            return ['SameDayRequestScreen', 'request_same_day', 'Planner is locked. A same-day request must be approved before planning.'];
        }

        // Gate 8: cutoff passed with no same-day request at all.
        if ($s['cutoff_passed'] && !$sd_req['exists']) {
            return ['SameDayRequestScreen', 'request_same_day', 'Planner cutoff passed. Raise a same-day request to unlock planning.'];
        }

        // All clear: open the planner.
        return ['PlannerV2', 'planner_open', null];
    }

    // -------------------------------------------------------------------------
    // Approver resolution (shared by all request controllers)
    // -------------------------------------------------------------------------

    /**
     * resolve_approver
     *
     * Same logic as get_line_manager but intended to be called from controllers
     * before an INSERT so they get back a flat array ready to denormalize into
     * the request row.
     *
     * Returns ['uid' => int, 'name' => string, 'role' => string] or null.
     */
    public function resolve_approver($uid) {
        return $this->get_line_manager($uid);
    }

    // -------------------------------------------------------------------------
    // Audit helper
    // -------------------------------------------------------------------------

    /**
     * insert_audit
     *
     * Writes one row to discipline_audit. payload_json is a PHP array that
     * gets JSON-encoded here.
     */
    public function insert_audit($user_id, $event_type, $payload_array) {
        $payload_json = $this->db->escape(json_encode($payload_array));
        $event_date   = date('Y-m-d H:i:s');
        $this->db->query(
            "INSERT INTO discipline_audit (user_id, event_type, event_date, payload_json)
             VALUES ('$user_id', '$event_type', '$event_date', $payload_json)"
        );
    }

    // -------------------------------------------------------------------------
    // Notify helper
    // -------------------------------------------------------------------------

    /**
     * insert_notify
     *
     * Writes one row to the existing notify table for the given recipient uid.
     */
    public function insert_notify($recipient_uid, $message) {
        $message_esc = $this->db->escape($message);
        $now         = date('Y-m-d H:i:s');
        // notify table schema: uid (recipient), admin_id NOT NULL, sms, sdatet, view.
        // Set admin_id = recipient_uid as a self-target so the NOT NULL constraint is satisfied.
        $this->db->query(
            "INSERT INTO notify (uid, admin_id, sms, sdatet, view)
             VALUES ('$recipient_uid', '$recipient_uid', $message_esc, '$now', 0)"
        );
    }
}

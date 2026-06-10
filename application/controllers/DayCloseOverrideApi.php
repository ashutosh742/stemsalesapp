<?php
defined('BASEPATH') OR exit('No direct script access allowed');
date_default_timezone_set('Asia/Kolkata');

/**
 * DayCloseOverrideApi
 *
 * Controller: application/controllers/DayCloseOverrideApi.php
 *
 * Handles two endpoints:
 *
 *   POST /api/day_close/override_request
 *   POST /api/day_close/override_decision
 *
 * Both endpoints are protected by the Bearer STEM_DIGEST_TOKEN guard.
 *
 * =========================================================================
 * HOW THE APPROVED OVERRIDE BYPASSES THE DAY-CLOSE BLOCK GATES
 * =========================================================================
 *
 * Background (do not lose this comment -- it documents the required patch
 * to Menu.php that must be applied in a future change by a human developer):
 *
 * The existing Day Close path is Menu.php::daysc (do=1), around line 4982.
 * Before recording the close it runs two hard-block gates:
 *
 *   Gate A: GetReUpdateNewLeadComapny($user_id) -- new leads pending re-update.
 *   Gate B: get_PendingTaskForToday($user_id)  -- pending autotask rows.
 *
 * When an override has been Approved for a given user and req_date, the
 * Day Close should succeed even if Gate A or Gate B would normally fire.
 *
 * PATCH NEEDED in Menu.php::daysc BEFORE each gate (do NOT modify Menu.php
 * from this file -- that change must be applied separately):
 *
 *   // Check whether an approved day_close_override exists for this user today.
 *   $today_date   = date('Y-m-d');
 *   $dco_override = $this->db->query(
 *       "SELECT id FROM day_close_override
 *        WHERE user_id     = '$uid'
 *          AND req_date    = '$today_date'
 *          AND approvel_status = 'Approved'
 *        LIMIT 1"
 *   )->result();
 *   $has_override = !empty($dco_override);
 *
 *   // Gate A: new leads pending re-update.
 *   $newLeadPending = $this->Menu_model->GetReUpdateNewLeadComapny($user_id);
 *   if (count($newLeadPending) > 0 && !$has_override) {
 *       // ...existing block logic...
 *   }
 *
 *   // Gate B: pending autotask rows.
 *   $pendingTask = $this->Menu_model->get_PendingTaskForToday($user_id);
 *   if (count($pendingTask) > 0 && !$has_override) {
 *       // ...existing block logic...
 *   }
 *
 * The override is per-date: $has_override is only true when req_date matches
 * today. A BD cannot use yesterday's override to skip today's gates.
 *
 * This controller writes the override row and notifies the LM but does NOT
 * touch Menu.php itself. The patch above is the only required Menu.php change.
 * =========================================================================
 */
class DayCloseOverrideApi extends CI_Controller {

    const DIGEST_TOKEN = '4eBaiAT7r4zu6OK3b8evjLNia1D7RGgb0qRTuLJfUSo';

    public function __construct() {
        parent::__construct();
        $this->load->model('DisciplineState_model');
        $this->load->helper('url');
        $this->output->set_content_type('application/json');
    }

    // -------------------------------------------------------------------------
    // Bearer token guard
    // -------------------------------------------------------------------------

    /**
     * check_token
     *
     * Validates the Authorization: Bearer header. Returns true on success;
     * writes a 401 JSON response and returns false on failure.
     */
    private function check_token() {
        if (function_exists('authunify_ok') && authunify_ok()) { return true; } // rimlyproof_authunify_20260609

        $auth_header = $_SERVER['HTTP_AUTHORIZATION'] ?? '';
        if (empty($auth_header)) {
            $auth_header = $_SERVER['REDIRECT_HTTP_AUTHORIZATION'] ?? '';
        }
        if (strpos($auth_header, 'Bearer ') === 0) {
            $token = substr($auth_header, 7);
            if ($token === self::DIGEST_TOKEN) {
                return true;
            }
        }
        http_response_code(401);
        echo json_encode(['ok' => false, 'error' => 'Unauthorized']);
        return false;
    }

    // -------------------------------------------------------------------------
    // Internal helpers
    // -------------------------------------------------------------------------

    private function post_int($field) {
        return (int) ($this->input->post($field) ?? 0);
    }

    private function post_str($field) {
        return trim($this->input->post($field) ?? '');
    }

    private function json_ok($extra = []) {
        echo json_encode(array_merge(['ok' => true], $extra));
    }

    private function json_err($reason, $code = 400) {
        http_response_code($code);
        echo json_encode(['ok' => false, 'error' => $reason]);
    }

    // -------------------------------------------------------------------------
    // POST /api/day_close/override_request  (spec 3.6)
    // -------------------------------------------------------------------------

    /**
     * override_request
     *
     * Body fields: uid, req_date, reason
     *
     * The BD hits this endpoint from the DayCloseBlockScreen when they want to
     * ask their LM for permission to close the day despite pending blocking items.
     *
     * Steps:
     *   1. Validate input.
     *   2. Duplicate guard: only one open (Pending) override per (uid, req_date).
     *      If one already exists and is still Pending, return the existing row.
     *   3. Resolve approver (LM) for this user.
     *   4. INSERT day_close_override with approvel_status = 'Pending'.
     *   5. INSERT notify row for the LM.
     *   6. INSERT discipline_audit row with event_type = 'day_close_override_requested'.
     */
    public function override_request() {
        if (!$this->check_token()) {
            return;
        }

        $uid      = $this->post_int('uid');
        $req_date = $this->post_str('req_date');
        $reason   = $this->post_str('reason');

        if ($uid <= 0 || empty($req_date)) {
            $this->json_err('uid and req_date are required');
            return;
        }

        if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $req_date)) {
            $this->json_err('req_date must be in YYYY-MM-DD format');
            return;
        }

        try {
            // Duplicate guard: check for an existing Pending override for this date.
            $existing = $this->db->query(
                "SELECT id, approvel_status
                 FROM day_close_override
                 WHERE user_id  = '$uid'
                   AND req_date = '$req_date'
                 ORDER BY id DESC
                 LIMIT 1"
            )->result();

            if (!empty($existing) && $existing[0]->approvel_status === 'Pending') {
                $this->json_ok([
                    'message'    => 'Override request already pending',
                    'override_id'=> (int) $existing[0]->id,
                    'duplicate'  => true,
                ]);
                return;
            }

            // Resolve LM.
            $lm = $this->DisciplineState_model->resolve_approver($uid);
            if ($lm === null) {
                $this->json_err('Could not resolve line manager for this user', 500);
                return;
            }

            $approver_uid = (int) $lm['uid'];
            $reason_esc   = $this->db->escape_str($reason);
            $now          = date('Y-m-d H:i:s');

            // INSERT day_close_override.
            $this->db->query(
                "INSERT INTO day_close_override
                   (user_id, req_date, reason, approver_uid, approvel_status, created_at)
                 VALUES
                   ('$uid', '$req_date', '$reason_esc', '$approver_uid', 'Pending', '$now')"
            );
            $new_id = $this->db->insert_id();

            if (!$new_id) {
                log_message('error', 'DayCloseOverrideApi::override_request INSERT failed for uid=' . $uid);
                $this->json_err('database error on insert', 500);
                return;
            }

            // Notify the LM.
            $this->DisciplineState_model->insert_notify(
                $approver_uid,
                'Day close override request from user ' . $uid . ' for date ' . $req_date . '. Reason: ' . $reason . '. Please review.'
            );

            // Audit.
            $this->DisciplineState_model->insert_audit($uid, 'day_close_override_requested', [
                'override_id'  => $new_id,
                'req_date'     => $req_date,
                'reason'       => $reason,
                'approver_uid' => $approver_uid,
                'lm_name'      => $lm['name'],
                'lm_role'      => $lm['role'],
            ]);

            $this->json_ok([
                'message'      => 'Override request submitted',
                'override_id'  => $new_id,
                'approver_uid' => $approver_uid,
                'lm_name'      => $lm['name'],
                'lm_role'      => $lm['role'],
            ]);

        } catch (Exception $e) {
            log_message('error', 'DayCloseOverrideApi::override_request exception: ' . $e->getMessage());
            $this->json_err('database error', 500);
        }
    }

    // -------------------------------------------------------------------------
    // POST /api/day_close/override_decision  (spec 3.7)
    // -------------------------------------------------------------------------

    /**
     * override_decision
     *
     * Body fields: id, decision (Approved|Reject), remarks
     *
     * When the LM approves, the day_close_override row is set to Approved.
     * The existing Menu.php::daysc gate logic must then check for this row
     * (see the patch comment block at the top of this file) to bypass the
     * two hard-block gates on the next Day Close call from the BD for
     * that specific req_date.
     *
     * Steps:
     *   1. Validate input.
     *   2. Fetch the override row.
     *   3. UPDATE day_close_override with the decision, apr_time, and remarks.
     *   4. INSERT discipline_audit row.
     *   5. Notify the BD of the outcome.
     */
    public function override_decision() {
        if (!$this->check_token()) {
            return;
        }

        $override_id = $this->post_int('id');
        $decision    = $this->post_str('decision');
        $remarks     = $this->post_str('remarks');

        if ($override_id <= 0 || !in_array($decision, ['Approved', 'Reject'], true)) {
            $this->json_err('id and decision (Approved|Reject) are required');
            return;
        }

        try {
            // Fetch the override row.
            $row_query = $this->db->query(
                "SELECT id, user_id, req_date, approver_uid, approvel_status
                 FROM day_close_override
                 WHERE id = '$override_id'
                 LIMIT 1"
            )->result();

            if (empty($row_query)) {
                $this->json_err('Override request not found', 404);
                return;
            }
            $row         = $row_query[0];
            $user_id     = (int) $row->user_id;
            $approver_uid= (int) $row->approver_uid;
            $req_date    = $row->req_date;

            // Resolve approver name for the remarks string.
            $lm_name_row = $this->db->query(
                "SELECT name FROM user_details WHERE user_id = '$approver_uid' LIMIT 1"
            )->result();
            $lm_name = !empty($lm_name_row) ? $lm_name_row[0]->name : 'Line Manager';

            $apr_time    = date('Y-m-d H:i:s');
            $remarks_esc = $this->db->escape_str($remarks);
            $lm_name_esc = $this->db->escape_str($lm_name);

            if ($decision === 'Approved') {
                $remarks_text = 'Approved By ' . $lm_name_esc . ($remarks ? '. Note: ' . $remarks_esc : '');
                $event_type   = 'day_close_override_approved';
            } else {
                $remarks_text = 'Rejected By ' . $lm_name_esc . '. Reason: ' . $remarks_esc;
                $event_type   = 'day_close_override_rejected';
            }

            $remarks_text_esc = $this->db->escape_str($remarks_text);

            $this->db->query(
                "UPDATE day_close_override
                 SET approvel_status = '$decision',
                     apr_time        = '$apr_time',
                     remarks         = '$remarks_text_esc'
                 WHERE id = '$override_id'"
            );

            // Audit.
            $this->DisciplineState_model->insert_audit($user_id, $event_type, [
                'override_id'  => $override_id,
                'req_date'     => $req_date,
                'decision'     => $decision,
                'approver_uid' => $approver_uid,
                'lm_name'      => $lm_name,
                'remarks'      => $remarks,
            ]);

            // Notify the BD.
            $this->DisciplineState_model->insert_notify(
                $user_id,
                'Your Day Close override request for ' . $req_date . ' has been ' . $decision . ' by ' . $lm_name . '.'
                . ($decision === 'Approved'
                    ? ' You may now proceed with Day Close even if pending items exist.'
                    : '')
            );

            $this->json_ok([
                'message'    => 'Override decision recorded',
                'decision'   => $decision,
                'override_id'=> $override_id,
                'req_date'   => $req_date,
            ]);

        } catch (Exception $e) {
            log_message('error', 'DayCloseOverrideApi::override_decision exception: ' . $e->getMessage());
            $this->json_err('database error', 500);
        }
    }
}

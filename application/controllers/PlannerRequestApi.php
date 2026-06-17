<?php
defined('BASEPATH') OR exit('No direct script access allowed');
date_default_timezone_set('Asia/Kolkata');

/**
 * PlannerRequestApi
 *
 * Controller: application/controllers/PlannerRequestApi.php
 *
 * Handles the four planning-approval endpoints:
 *
 *   POST /api/planner/same_day_request
 *   POST /api/planner/same_day_decision
 *   POST /api/planner/yesterday_request
 *   POST /api/planner/yesterday_decision
 *
 * All routes are protected by the Bearer STEM_DIGEST_TOKEN guard.
 *
 * Approver resolution mirrors the legacy production path in
 * Menu.php::RequestForTodaysTaskPlan (line 6263) and
 * Menu.php::RequestForYestTaskPlan (line 6315). The approver_override table
 * (migration 081) is consulted first; if no override row exists, resolution
 * falls back to the type_id hierarchy described in spec section 3.2.
 *
 * Every mutating action writes a row to discipline_audit so that the test
 * matrix (spec section 7) can verify the event_type and payload.
 */
class PlannerRequestApi extends CI_Controller {

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
     * Reads Authorization header and validates the Bearer value. Returns true
     * on success; writes a 401 JSON response and returns false on failure.
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
    // Shared internal helpers
    // -------------------------------------------------------------------------

    /**
     * post_int
     *
     * Reads a POST field and casts it to int. Returns 0 when missing or empty.
     */
    private function post_int($field) {
        return (int) ($this->input->post($field) ?? 0);
    }

    /**
     * post_str
     *
     * Reads a POST field and returns a trimmed string. Returns '' when missing.
     */
    private function post_str($field) {
        return trim($this->input->post($field) ?? '');
    }

    /**
     * json_ok
     *
     * Outputs a success JSON envelope merged with the provided array.
     */
    private function json_ok($extra = []) {
        echo json_encode(array_merge(['ok' => true], $extra));
    }

    /**
     * json_err
     *
     * Outputs an error JSON envelope with the provided short reason string.
     */
    private function json_err($reason, $code = 400) {
        http_response_code($code);
        echo json_encode(['ok' => false, 'error' => $reason]);
    }

    // -------------------------------------------------------------------------
    // POST /api/planner/same_day_request  (spec 3.2)
    // -------------------------------------------------------------------------

    /**
     * same_day_request
     *
     * Body fields: uid, date, taskcnt, request_remarks, would_you_want
     *
     * Steps:
     *   1. Validate input.
     *   2. Duplicate guard: if a request already exists for (uid, date), return
     *      the existing row without a second INSERT (mirrors legacy line 6304).
     *   3. Resolve approver via approver_override -> type_id hierarchy.
     *   4. Fetch approver name and role for denormalization.
     *   5. INSERT task_plan_for_today with approver_name and approver_role.
     *   6. INSERT notify row for the approver.
     *   7. INSERT discipline_audit row with event_type = 'same_day_request_created'.
     */
    public function same_day_request() {
        if (!$this->check_token()) {
            return;
        }

        $uid             = $this->post_int('uid');
        $date            = $this->post_str('date');
        $taskcnt         = $this->post_int('taskcnt');
        $request_remarks = $this->post_str('request_remarks');
        $would_you_want  = $this->post_str('would_you_want');

        if ($uid <= 0 || empty($date)) {
            $this->json_err('uid and date are required');
            return;
        }

        // Validate date format (YYYY-MM-DD).
        if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $date)) {
            $this->json_err('date must be in YYYY-MM-DD format');
            return;
        }

        try {
            // Step 2: duplicate guard.
            $existing = $this->db->query(
                "SELECT id, approvel_status, approver_name, approver_role, admin_id
                 FROM task_plan_for_today
                 WHERE user_id = '$uid'
                   AND date = '$date'
                 LIMIT 1"
            )->result();

            if (!empty($existing)) {
                $this->json_ok([
                    'message'     => 'Request already submitted',
                    'request_id'  => (int) $existing[0]->id,
                    'status'      => $existing[0]->approvel_status,
                    'duplicate'   => true,
                ]);
                return;
            }

            // Step 3: resolve approver.
            $approver = $this->DisciplineState_model->resolve_approver($uid);
            if ($approver === null) {
                $this->json_err('Could not resolve approver for this user', 500);
                return;
            }

            $approver_uid  = (int) $approver['uid'];
            $approver_name = $this->db->escape_str($approver['name']);
            $approver_role = $this->db->escape_str($approver['role']);
            $remarks_esc   = $this->db->escape_str($request_remarks);

            // Step 5: INSERT task_plan_for_today.
            // approvel_status = 0 means Pending (legacy convention).
            $this->db->query(
                "INSERT INTO task_plan_for_today
                   (user_id, admin_id, date, request_remarks, taskcnt,
                    would_you_want, approvel_status, approver_name, approver_role)
                 VALUES
                   ('$uid', '$approver_uid', '$date', '$remarks_esc', '$taskcnt',
                    '$would_you_want', '0', '$approver_name', '$approver_role')"
            );
            $new_id = $this->db->insert_id();

            if (!$new_id) {
                log_message('error', 'PlannerRequestApi::same_day_request INSERT failed for uid=' . $uid);
                $this->json_err('database error on insert', 500);
                return;
            }

            // Step 6: notify the approver.
            $this->DisciplineState_model->insert_notify(
                $approver_uid,
                'New same-day planning request from user ' . $uid . ' for date ' . $date . '. Please review.'
            );

            // Step 7: audit row.
            $this->DisciplineState_model->insert_audit($uid, 'same_day_request_created', [
                'request_id'   => $new_id,
                'date'         => $date,
                'taskcnt'      => $taskcnt,
                'approver_uid' => $approver_uid,
                'approver_name'=> $approver['name'],
                'approver_role'=> $approver['role'],
            ]);

            $this->json_ok([
                'message'      => 'Same-day request submitted successfully',
                'request_id'   => $new_id,
                'approver_uid' => $approver_uid,
                'approver_name'=> $approver['name'],
                'approver_role'=> $approver['role'],
            ]);

        } catch (Exception $e) {
            log_message('error', 'PlannerRequestApi::same_day_request exception: ' . $e->getMessage());
            $this->json_err('database error', 500);
        }
    }

    // -------------------------------------------------------------------------
    // POST /api/planner/same_day_decision  (spec 3.3)
    // -------------------------------------------------------------------------

    /**
     * same_day_decision
     *
     * Body fields: request_id, decision (Approved|Reject), remarks
     *
     * Steps:
     *   1. Validate input.
     *   2. Look up the request row to get the user_id for audit.
     *   3. UPDATE task_plan_for_today:
     *      - On Approved: set approvel_status = 'Approved', action_by = approver uid
     *        (derived from the token user; not passed in body since this is an LM
     *        action -- the LM uid must come from the notify flow or a separate auth
     *        mechanism; for now we record the approver as the admin_id stored on the
     *        request row because the mobile LM flow does not send a separate uid),
     *        apr_time = now(), remarks = 'Approved By <approver_name>'.
     *      - On Reject: set approvel_status = 'Reject', remarks = 'Rejected By <name>. Reason: <remarks>'.
     *   4. INSERT discipline_audit row.
     *
     * Note on the approver identity: the legacy web path reads the LM from the
     * PHP session (line 6394: $user['name']). In the API path the approver is
     * identified by the admin_id stored on the request row (which was denormalized
     * at creation time). The approver_name from the stored row is used in the
     * remarks string so the audit trail is consistent.
     */
    public function same_day_decision() {
        if (!$this->check_token()) {
            return;
        }

        $request_id = $this->post_int('request_id');
        $decision   = $this->post_str('decision');
        $remarks    = $this->post_str('remarks');

        if ($request_id <= 0 || !in_array($decision, ['Approved', 'Reject'], true)) {
            $this->json_err('request_id and decision (Approved|Reject) are required');
            return;
        }

        try {
            // Fetch the request row.
            $row_query = $this->db->query(
                "SELECT id, user_id, admin_id, approver_name, approver_role, date, approvel_status
                 FROM task_plan_for_today
                 WHERE id = '$request_id'
                 LIMIT 1"
            )->result();

            if (empty($row_query)) {
                $this->json_err('Request not found', 404);
                return;
            }
            $req       = $row_query[0];
            $user_id   = (int) $req->user_id;
            $admin_id  = (int) $req->admin_id;
            $lm_name   = $req->approver_name ?? 'Line Manager';

            if ($decision === 'Approved') {
                // Mirror the legacy TodaysTaskapprove path (line 6391-6397).
                $apr_time     = date('Y-m-d H:i:s');
                $remarks_text = 'Approved By ' . $this->db->escape_str($lm_name);
                $this->db->query(
                    "UPDATE task_plan_for_today
                     SET approvel_status = 'Approved',
                         action_by       = '$admin_id',
                         apr_time        = '$apr_time',
                         remarks         = '$remarks_text'
                     WHERE id = '$request_id'"
                );
                $event_type = 'same_day_request_approved';
            } else {
                // Mirror TodaysTaskReject (line 6418-6430).
                $reject_name  = $lm_name;
                $remarks_text = 'Rejected By ' . $this->db->escape_str($reject_name) . '. Reason: ' . $this->db->escape_str($remarks);
                $this->db->query(
                    "UPDATE task_plan_for_today
                     SET approvel_status = 'Reject',
                         action_by       = '$admin_id',
                         remarks         = '$remarks_text'
                     WHERE id = '$request_id'"
                );
                $event_type = 'same_day_request_rejected';
            }

            // Audit.
            $this->DisciplineState_model->insert_audit($user_id, $event_type, [
                'request_id'  => $request_id,
                'decision'    => $decision,
                'decided_by'  => $admin_id,
                'lm_name'     => $lm_name,
                'remarks'     => $remarks,
            ]);

            // Notify the BD.
            $this->DisciplineState_model->insert_notify(
                $user_id,
                'Your same-day planning request for ' . $req->date . ' has been ' . $decision . ' by ' . $lm_name . '.'
            );

            $this->json_ok(['message' => 'Decision recorded', 'decision' => $decision]);

        } catch (Exception $e) {
            log_message('error', 'PlannerRequestApi::same_day_decision exception: ' . $e->getMessage());
            $this->json_err('database error', 500);
        }
    }

    // -------------------------------------------------------------------------
    // POST /api/planner/yesterday_request  (spec 3.4)
    // -------------------------------------------------------------------------

    /**
     * yesterday_request
     *
     * Body fields: uid, req_date, taskcnt, request_remarks
     *
     * Yesterday request flow: the BD could not plan tasks for yesterday.
     * They submit this request so the LM can approve letting them plan retroactively.
     *
     * Steps:
     *   1. Validate input.
     *   2. Resolve approver.
     *   3. Compute pbni_count using DisciplineState_model::get_pbni_count (the
     *      get_all_old_cmp_planbutnotinited logic).
     *   4. INSERT request_old_pend_task with approver_name, approver_role, pbni_count.
     *      req_date column is stored with a timestamp appended (legacy convention
     *      from line 6325: $setdatebyuser = $setdatebyuser.' '.date('H:i:s')).
     *   5. INSERT pbni_alert row for the approver.
     *   6. INSERT notify row for the approver.
     *   7. INSERT discipline_audit row with event_type = 'yesterday_request_created'.
     */
    public function yesterday_request() {
        if (!$this->check_token()) {
            return;
        }

        $uid             = $this->post_int('uid');
        $req_date        = $this->post_str('req_date');
        $taskcnt         = $this->post_int('taskcnt');
        $request_remarks = $this->post_str('request_remarks');

        if ($uid <= 0 || empty($req_date)) {
            $this->json_err('uid and req_date are required');
            return;
        }

        if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $req_date)) {
            $this->json_err('req_date must be in YYYY-MM-DD format');
            return;
        }

        try {
            // Resolve approver.
            $approver = $this->DisciplineState_model->resolve_approver($uid);
            if ($approver === null) {
                $this->json_err('Could not resolve approver for this user', 500);
                return;
            }

            $approver_uid  = (int) $approver['uid'];
            $approver_name = $this->db->escape_str($approver['name']);
            $approver_role = $this->db->escape_str($approver['role']);
            $remarks_esc   = $this->db->escape_str($request_remarks);

            // Fetch PBNI count for this user before inserting.
            $pbni_count = $this->DisciplineState_model->get_pbni_count($uid);

            // Legacy convention: store req_date with the current time appended so
            // the existing CAST(req_date AS DATE) filter still works.
            $req_date_with_time = $req_date . ' ' . date('H:i:s');

            // Idempotent request_old_pend_task raise: one OPEN (approvel_status='0')
            // request per user per req_date. A retry refreshes the open row instead
            // of stacking duplicate pending requests. $req_date is the bare date;
            // the stored column carries a time suffix, so match on the date part.
            $req_date_esc = $this->db->escape_str($req_date);
            $existing_req = $this->db->query(
                "SELECT id FROM request_old_pend_task
                 WHERE user_id = '$uid'
                   AND CAST(req_date AS DATE) = '$req_date_esc'
                   AND approvel_status = '0'
                 ORDER BY id DESC
                 LIMIT 1"
            )->row();

            if ($existing_req) {
                $new_id = (int) $existing_req->id;
                $this->db->query(
                    "UPDATE request_old_pend_task
                     SET req_date        = '$req_date_with_time',
                         taskcnt         = '$taskcnt',
                         request_remarks = '$remarks_esc',
                         approver_name   = '$approver_name',
                         approver_role   = '$approver_role',
                         pbni_count      = '$pbni_count'
                     WHERE id = '$new_id'"
                );
            } else {
                $this->db->query(
                    "INSERT INTO request_old_pend_task
                       (user_id, req_date, taskcnt, request_remarks,
                        approvel_status, approver_name, approver_role, pbni_count)
                     VALUES
                       ('$uid', '$req_date_with_time', '$taskcnt', '$remarks_esc',
                        '0', '$approver_name', '$approver_role', '$pbni_count')"
                );
                $new_id = $this->db->insert_id();
            }

            if (!$new_id) {
                log_message('error', 'PlannerRequestApi::yesterday_request INSERT failed for uid=' . $uid);
                $this->json_err('database error on insert', 500);
                return;
            }

            // Idempotent pbni_alert raise: one OPEN (Pending) row per user per day.
            // If an open Pending row already exists for today, refresh it instead
            // of inserting a duplicate. Duplicate Pending rows are exactly what
            // defeated the approval gate (a newer Pending row hid the Approved one).
            $now = date('Y-m-d H:i:s');
            $existing_pending = $this->db->query(
                "SELECT id FROM pbni_alert
                 WHERE user_id = '$uid'
                   AND DATE(notified_at) = CURDATE()
                   AND approval_status = 'Pending'
                 ORDER BY id DESC
                 LIMIT 1"
            )->row();

            if ($existing_pending) {
                $pbni_alert_id = (int) $existing_pending->id;
                $this->db->query(
                    "UPDATE pbni_alert
                     SET pbni_count  = '$pbni_count',
                         lm_uid      = '$approver_uid',
                         notified_at = '$now'
                     WHERE id = '$pbni_alert_id'"
                );
            } else {
                $this->db->query(
                    "INSERT INTO pbni_alert
                       (user_id, pbni_count, lm_uid, notified_at, approval_status)
                     VALUES
                       ('$uid', '$pbni_count', '$approver_uid', '$now', 'Pending')"
                );
                $pbni_alert_id = $this->db->insert_id();
            }

            // Notify the approver.
            $this->DisciplineState_model->insert_notify(
                $approver_uid,
                'Yesterday plan request from user ' . $uid . ' for date ' . $req_date . '. PBNI count: ' . $pbni_count . '. Please review.'
            );

            // Audit.
            $this->DisciplineState_model->insert_audit($uid, 'yesterday_request_created', [
                'request_id'    => $new_id,
                'pbni_alert_id' => $pbni_alert_id,
                'req_date'      => $req_date,
                'taskcnt'       => $taskcnt,
                'pbni_count'    => $pbni_count,
                'approver_uid'  => $approver_uid,
                'approver_name' => $approver['name'],
                'approver_role' => $approver['role'],
            ]);

            $this->json_ok([
                'message'       => 'Yesterday request submitted successfully',
                'request_id'    => $new_id,
                'pbni_alert_id' => $pbni_alert_id,
                'pbni_count'    => $pbni_count,
                'approver_uid'  => $approver_uid,
                'approver_name' => $approver['name'],
                'approver_role' => $approver['role'],
            ]);

        } catch (Exception $e) {
            log_message('error', 'PlannerRequestApi::yesterday_request exception: ' . $e->getMessage());
            $this->json_err('database error', 500);
        }
    }

    // -------------------------------------------------------------------------
    // POST /api/planner/yesterday_decision  (spec 3.5)
    // -------------------------------------------------------------------------

    /**
     * yesterday_decision
     *
     * Body fields: request_id, decision (Approved|Reject), remarks
     *
     * Updates both request_old_pend_task and the matching pbni_alert row.
     *
     * Legacy approve path (line 6404): set approvel_status = 1, approvel_by, approvel_reamrks.
     * Legacy reject path (line 6435): set approvel_status = 0, approvel_reamrks, approvel_by.
     *
     * For the new API path:
     *   Approved -> approvel_status = 1, update pbni_alert.approval_status = 'Approved'
     *   Reject   -> approvel_status = 2 (distinguish from pending=0), pbni_alert unchanged
     */
    public function yesterday_decision() {
        if (!$this->check_token()) {
            return;
        }

        $request_id = $this->post_int('request_id');
        $decision   = $this->post_str('decision');
        $remarks    = $this->post_str('remarks');

        if ($request_id <= 0 || !in_array($decision, ['Approved', 'Reject'], true)) {
            $this->json_err('request_id and decision (Approved|Reject) are required');
            return;
        }

        try {
            // Fetch the request row.
            $row_query = $this->db->query(
                "SELECT id, user_id, approver_name, req_date, pbni_count
                 FROM request_old_pend_task
                 WHERE id = '$request_id'
                 LIMIT 1"
            )->result();

            if (empty($row_query)) {
                $this->json_err('Request not found', 404);
                return;
            }
            $req       = $row_query[0];
            $user_id   = (int) $req->user_id;
            $lm_name   = $req->approver_name ?? 'Line Manager';
            $remarks_esc = $this->db->escape_str($remarks);
            $lm_name_esc = $this->db->escape_str($lm_name);

            if ($decision === 'Approved') {
                // Mirror TodaysPendingTaskapprove (line 6404-6413).
                $remarks_text = 'Approved By ' . $lm_name_esc;
                $this->db->query(
                    "UPDATE request_old_pend_task
                     SET approvel_status  = '1',
                         approvel_by      = (SELECT id FROM user_details WHERE name = '$lm_name_esc' LIMIT 1),
                         approvel_reamrks = '$remarks_text'
                     WHERE id = '$request_id'"
                );

                // Flip TODAY's pbni_alert rows for this user to Approved. Approve
                // ALL of today's Pending rows (not just the latest by id) so any
                // pre-existing duplicate Pending rows cannot later defeat the gate.
                $approved_at = date('Y-m-d H:i:s');
                $this->db->query(
                    "UPDATE pbni_alert
                     SET approval_status = 'Approved',
                         approved_at     = '$approved_at'
                     WHERE user_id = '$user_id'
                       AND DATE(notified_at) = CURDATE()
                       AND approval_status = 'Pending'"
                );

                // Ensure a today-Approved pbni_alert row exists so Gate 2 releases.
                // The existing UPDATE above only matches pre-existing Pending rows;
                // if none existed, pbni_alert_approved stays false. Fix: guarantee
                // DATE(notified_at)=CURDATE() AND approval_status='Approved' for this user.
                try {
                    $ensure_check = $this->db->query(
                        "SELECT COUNT(*) AS c
                         FROM pbni_alert
                         WHERE user_id = '$user_id'
                           AND DATE(notified_at) = CURDATE()
                           AND approval_status = 'Approved'"
                    )->row();
                    if ($ensure_check && (int)$ensure_check->c === 0) {
                        $pbni_cnt = isset($req->pbni_count) ? (int)$req->pbni_count : 0;
                        $this->db->query(
                            "INSERT INTO pbni_alert
                             (user_id, pbni_count, lm_uid, notified_at, approval_status, approved_at)
                             VALUES ('$user_id', '$pbni_cnt', NULL, NOW(), 'Approved', '$approved_at')"
                        );
                    }
                } catch (Exception $ensure_ex) {
                    log_message('error',
                        'PlannerRequestApi::yesterday_decision ensure-pbni_alert failed for '
                        . $user_id . ': ' . $ensure_ex->getMessage()
                    );
                    // Non-fatal: the decision itself already succeeded above.
                }

                $event_type = 'yesterday_request_approved';
            } else {
                // Mirror TodaysPendingsTaskRequestReject (line 6435-6443).
                $reject_text = 'Rejected By ' . $lm_name_esc . '. Reason: ' . $remarks_esc;
                $this->db->query(
                    "UPDATE request_old_pend_task
                     SET approvel_status  = '2',
                         approvel_reamrks = '$reject_text'
                     WHERE id = '$request_id'"
                );
                // pbni_alert stays Pending when rejected (LM may re-review later).
                $event_type = 'yesterday_request_rejected';
            }

            // Audit.
            $this->DisciplineState_model->insert_audit($user_id, $event_type, [
                'request_id' => $request_id,
                'decision'   => $decision,
                'lm_name'    => $lm_name,
                'remarks'    => $remarks,
            ]);

            // Notify BD.
            $this->DisciplineState_model->insert_notify(
                $user_id,
                'Your yesterday plan request has been ' . $decision . ' by ' . $lm_name . '.'
            );

            $this->json_ok(['message' => 'Decision recorded', 'decision' => $decision]);

        } catch (Exception $e) {
            log_message('error', 'PlannerRequestApi::yesterday_decision exception: ' . $e->getMessage());
            $this->json_err('database error', 500);
        }
    }
}

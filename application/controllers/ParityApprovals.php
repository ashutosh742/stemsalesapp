<?php
defined('BASEPATH') OR exit('No direct script access allowed');
date_default_timezone_set('Asia/Kolkata');

/**
 * ParityApprovals
 *
 * Production-parity approval-flow endpoints for STEM CRM mobile (WS-D, 2026-06-07).
 *
 * Mirrors the live approval pipeline:
 *   same-day login -> planner approval pending -> meeting approval pending
 *
 * Routes (class-name-only per CI3 spec):
 *   GET /parityapprovals/pending_summary?user_id=  -> pending_summary()
 *   GET /parityapprovals/planner_pending?user_id=  -> planner_pending()
 *   GET /parityapprovals/meeting_pending?user_id=  -> meeting_pending()
 *
 * DB sources (verified live):
 *   Planner/same-day : task_plan_for_today (admin_id=approver, approvel_status IN ('pending',''))
 *   Meeting pending  : pending_meetings_request (apr_status=0)
 *                      joined via user_details FK column matching approver type_id
 *                      (mirrors Menu_model::GetPendingMeetingTaskDekleteRequest)
 *
 * Auth: Bearer STEM_DIGEST_TOKEN (master key or per-user daily JWT)
 * PHP output: ASCII only, no em/en dashes, Rs for rupees, "percent" spelled out.
 */
class ParityApprovals extends CI_Controller {

    const MASTER_TOKEN = '4eBaiAT7r4zu6OK3b8evjLNia1D7RGgb0qRTuLJfUSo';

    public function __construct() {
        parent::__construct();
        $this->load->database();
        header('Content-Type: application/json; charset=utf-8');
    }

    // -------------------------------------------------------------------------
    // Bearer token guard
    // Returns true on valid token; writes 401 and returns false otherwise.
    // Accepts master token OR per-user daily JWT sha1(secret|uid|YYYY-MM-DD).
    // -------------------------------------------------------------------------
    private function _auth() {
        $hdr = '';
        if (function_exists('apache_request_headers')) {
            $aph = apache_request_headers();
            if (!empty($aph['Authorization']))   $hdr = $aph['Authorization'];
            elseif (!empty($aph['authorization'])) $hdr = $aph['authorization'];
        }
        if (empty($hdr) && !empty($_SERVER['HTTP_AUTHORIZATION']))  $hdr = $_SERVER['HTTP_AUTHORIZATION'];
        if (empty($hdr) && !empty($_SERVER['REDIRECT_HTTP_AUTHORIZATION'])) $hdr = $_SERVER['REDIRECT_HTTP_AUTHORIZATION'];

        if (empty($hdr) || stripos($hdr, 'Bearer ') !== 0) {
            http_response_code(401);
            echo json_encode(array('ok' => false, 'error' => 'unauthorized'));
            return false;
        }

        $token   = trim(substr($hdr, 7));
        $secret  = getenv('STEM_DIGEST_TOKEN') ?: self::MASTER_TOKEN;

        // Master token check
        if (hash_equals($secret, $token)) return true;

        // Per-user JWT: sha1(secret|uid|YYYY-MM-DD), today and yesterday
        $days = array(date('Y-m-d'), date('Y-m-d', strtotime('-1 day')), date('Y-m-d', strtotime('+1 day')));
        $cands = array();
        foreach (array('user_id','uid','bd_uid','cm_uid','acm_uid') as $k) {
            if (isset($_GET[$k]) && (int)$_GET[$k] > 0) $cands[(int)$_GET[$k]] = 1;
        }
        foreach (array_keys($cands) as $uid) {
            foreach ($days as $d) {
                if (hash_equals(sha1($secret . '|' . $uid . '|' . $d), $token)) return true;
            }
        }

        http_response_code(401);
        echo json_encode(array('ok' => false, 'error' => 'bad_token'));
        return false;
    }

    // Output helpers
    private function _ok($data) {
        echo json_encode(array_merge(array('ok' => true), $data));
        exit;
    }
    private function _err($msg, $code = 400) {
        http_response_code($code);
        echo json_encode(array('ok' => false, 'error' => $msg));
        exit;
    }

    // -------------------------------------------------------------------------
    // Sanitize a DB string to ASCII only (no em/en dashes, no non-ASCII chars).
    // -------------------------------------------------------------------------
    private function _ascii($str) {
        if ($str === null) return '';
        // Replace em-dash, en-dash, hyphen variants with plain hyphen
        $str = str_replace(array("\xe2\x80\x93", "\xe2\x80\x94", "\xc2\xad"), '-', $str);
        // Strip any remaining non-ASCII
        return preg_replace('/[^\x20-\x7E]/', '', $str);
    }

    // -------------------------------------------------------------------------
    // Resolve approver's pending queue FK from user_details based on type_id.
    // Mirrors Menu_model::GetPendingMeetingTaskDekleteRequest logic.
    // Returns a SQL fragment for WHERE clause to filter user_details rows
    // whose meetings belong to this approver.
    // -------------------------------------------------------------------------
    private function _meeting_where_clause($uid, $type_id) {
        $uid = (int)$uid;
        switch ((int)$type_id) {
            case 1:  return "ud.sadmin_id = $uid";
            case 2:  return "ud.admin_id = $uid";
            case 4:  return "ud.pst_co = $uid";
            case 13: return "ud.aadmin = $uid";
            case 15: return "ud.sales_co = $uid";
            case 19: return "ud.ash_nae_co = $uid";
            case 20: return "ud.ash_w_co = $uid";
            case 21: return "ud.ash_s_co = $uid";
            case 22: return "ud.rm_east_co = $uid";
            case 23: return "ud.rm_north_co = $uid";
            case 24: return "ud.acm_co = $uid";
            default: return "ud.admin_id = $uid";
        }
    }

    // -------------------------------------------------------------------------
    // GET /parityapprovals/pending_summary?user_id=
    //
    // Returns live counts of all three approval queues for the given approver.
    //
    // Response shape:
    // {
    //   "ok": true,
    //   "user_id": 2,
    //   "counts": {
    //     "same_day_login_pending": 5,
    //     "planner_approval_pending": 27,
    //     "meeting_approval_pending": 40
    //   },
    //   "empty": false
    // }
    // -------------------------------------------------------------------------
    public function pending_summary() {
        if (!$this->_auth()) return;

        $user_id = (int)$this->input->get('user_id');
        if ($user_id <= 0) {
            $this->_err('user_id query param is required');
        }

        // Fetch approver type_id from user table
        $urow = $this->db->query(
            "SELECT u.uid, u.name, u.type_id FROM user u WHERE u.uid = ? LIMIT 1",
            array($user_id)
        )->row_array();

        if (empty($urow)) {
            $this->_err('user_id not found', 404);
        }

        $type_id = (int)$urow['type_id'];

        // --- 1. same_day_login_pending ---
        // Rows in task_plan_for_today with approvel_status='pending' (explicit request awaiting action)
        $same_day_row = $this->db->query(
            "SELECT COUNT(*) AS cnt FROM task_plan_for_today
             WHERE admin_id = ? AND approvel_status = 'pending'",
            array($user_id)
        )->row_array();
        $same_day_count = (int)($same_day_row['cnt'] ?? 0);

        // --- 2. planner_approval_pending ---
        // Rows in task_plan_for_today with approvel_status IN ('pending','') (all unresolved)
        $planner_row = $this->db->query(
            "SELECT COUNT(*) AS cnt FROM task_plan_for_today
             WHERE admin_id = ? AND (approvel_status = 'pending' OR approvel_status = '')",
            array($user_id)
        )->row_array();
        $planner_count = (int)($planner_row['cnt'] ?? 0);

        // --- 3. meeting_approval_pending ---
        // pending_meetings_request with apr_status=0 for BD users under this approver
        $meet_where = $this->_meeting_where_clause($user_id, $type_id);
        $meet_row = $this->db->query(
            "SELECT COUNT(*) AS cnt
             FROM pending_meetings_request pmr
             LEFT JOIN user_details ud ON ud.user_id = pmr.user_uid
             WHERE $meet_where AND pmr.apr_status = 0"
        )->row_array();
        $meet_count = (int)($meet_row['cnt'] ?? 0);

        $total = $same_day_count + $planner_count + $meet_count;

        $this->_ok(array(
            'user_id' => $user_id,
            'approver_name' => $this->_ascii($urow['name']),
            'counts' => array(
                'same_day_login_pending'   => $same_day_count,
                'planner_approval_pending' => $planner_count,
                'meeting_approval_pending' => $meet_count,
            ),
            'empty' => ($total === 0),
            'generated_at' => date('Y-m-d H:i:s'),
        ));
    }

    // -------------------------------------------------------------------------
    // GET /parityapprovals/planner_pending?user_id=
    //
    // List of planner/same-day plans awaiting this approver's action.
    // Source: task_plan_for_today WHERE approvel_status IN ('pending','')
    //         AND admin_id = user_id
    // JOINs user_details for BD name.
    //
    // Response shape:
    // {
    //   "ok": true,
    //   "user_id": 2,
    //   "rows": [ { "id", "bd_uid", "bd_name", "date", "approvel_status",
    //               "created_at", "request_remarks", "would_you_want", "taskcnt" } ],
    //   "count": N,
    //   "empty": false
    // }
    // -------------------------------------------------------------------------
    public function planner_pending() {
        if (!$this->_auth()) return;

        $user_id = (int)$this->input->get('user_id');
        if ($user_id <= 0) {
            $this->_err('user_id query param is required');
        }

        // Verify approver exists
        $urow = $this->db->query(
            "SELECT uid, name FROM user WHERE uid = ? LIMIT 1",
            array($user_id)
        )->row_array();
        if (empty($urow)) {
            $this->_err('user_id not found', 404);
        }

        $rows_raw = $this->db->query(
            "SELECT
                t.id,
                t.user_id   AS bd_uid,
                ud.name     AS bd_name,
                t.date,
                t.approvel_status,
                t.created_at,
                t.request_remarks,
                t.would_you_want,
                t.taskcnt,
                t.header_label
             FROM task_plan_for_today t
             LEFT JOIN user_details ud ON ud.user_id = t.user_id
             WHERE t.admin_id = ?
               AND (t.approvel_status = 'pending' OR t.approvel_status = '')
             ORDER BY t.created_at DESC
             LIMIT 500",
            array($user_id)
        )->result_array();

        if (empty($rows_raw)) {
            $this->_ok(array(
                'user_id' => $user_id,
                'rows'    => array(),
                'count'   => 0,
                'empty'   => true,
            ));
        }

        $rows = array();
        foreach ($rows_raw as $r) {
            $rows[] = array(
                'id'              => (int)$r['id'],
                'bd_uid'          => (int)$r['bd_uid'],
                'bd_name'         => $this->_ascii($r['bd_name']),
                'date'            => $r['date'],
                'approvel_status' => $r['approvel_status'],
                'created_at'      => $r['created_at'],
                'request_remarks' => $this->_ascii($r['request_remarks']),
                'would_you_want'  => $this->_ascii($r['would_you_want']),
                'taskcnt'         => (int)$r['taskcnt'],
                'header_label'    => $this->_ascii($r['header_label']),
            );
        }

        $this->_ok(array(
            'user_id' => $user_id,
            'rows'    => $rows,
            'count'   => count($rows),
            'empty'   => false,
        ));
    }

    // -------------------------------------------------------------------------
    // GET /parityapprovals/meeting_pending?user_id=
    //
    // List of meeting approval requests awaiting this approver's action.
    // Source: pending_meetings_request WHERE apr_status=0
    //         joined via user_details FK matching approver type_id
    //         (mirrors Menu_model::GetPendingMeetingTaskDekleteRequest)
    //
    // Response shape:
    // {
    //   "ok": true,
    //   "user_id": 2,
    //   "rows": [ { "id", "bd_uid", "bd_name", "request_date", "task_ids",
    //               "request_task_count", "remarks", "apr_status", "created_at" } ],
    //   "count": N,
    //   "empty": false
    // }
    // -------------------------------------------------------------------------
    public function meeting_pending() {
        if (!$this->_auth()) return;

        $user_id = (int)$this->input->get('user_id');
        if ($user_id <= 0) {
            $this->_err('user_id query param is required');
        }

        // Fetch approver type_id
        $urow = $this->db->query(
            "SELECT u.uid, u.name, u.type_id FROM user u WHERE u.uid = ? LIMIT 1",
            array($user_id)
        )->row_array();
        if (empty($urow)) {
            $this->_err('user_id not found', 404);
        }

        $type_id = (int)$urow['type_id'];
        $meet_where = $this->_meeting_where_clause($user_id, $type_id);

        $rows_raw = $this->db->query(
            "SELECT
                pmr.id,
                pmr.user_uid          AS bd_uid,
                ud.name               AS bd_name,
                pmr.request_date,
                pmr.task_ids,
                pmr.request_task_count,
                pmr.remarks,
                pmr.apr_status,
                pmr.admin_apr_status,
                pmr.created_at
             FROM pending_meetings_request pmr
             LEFT JOIN user_details ud ON ud.user_id = pmr.user_uid
             WHERE $meet_where
               AND pmr.apr_status = 0
             ORDER BY pmr.created_at DESC
             LIMIT 500"
        )->result_array();

        if (empty($rows_raw)) {
            $this->_ok(array(
                'user_id'     => $user_id,
                'source_table'=> 'pending_meetings_request',
                'rows'        => array(),
                'count'       => 0,
                'empty'       => true,
            ));
        }

        $rows = array();
        foreach ($rows_raw as $r) {
            $rows[] = array(
                'id'                 => (int)$r['id'],
                'bd_uid'             => (int)$r['bd_uid'],
                'bd_name'            => $this->_ascii($r['bd_name']),
                'request_date'       => $r['request_date'],
                'task_ids'           => $this->_ascii($r['task_ids']),
                'request_task_count' => (int)$r['request_task_count'],
                'remarks'            => $this->_ascii($r['remarks']),
                'apr_status'         => (int)$r['apr_status'],
                'admin_apr_status'   => (int)$r['admin_apr_status'],
                'created_at'         => $r['created_at'],
            );
        }

        $this->_ok(array(
            'user_id'      => $user_id,
            'source_table' => 'pending_meetings_request',
            'rows'         => $rows,
            'count'        => count($rows),
            'empty'        => false,
        ));
    }
}

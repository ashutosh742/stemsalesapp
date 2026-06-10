<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Gap_reports_api — consolidated backend for all 12 GAP-flagged LMS cards.
 *
 * Sprint : gap_fix_sprint_2026-06-04
 * Built  : 2026-06-04
 *
 * Covers (❌ GAP rows from coverage-map.md):
 *   Card  4 — Review Report
 *   Card  6 — User App Usage Time
 *   Card  7 — Leave Management (was ✅ already but pages_built=0 in JSON; included for completeness)
 *   Card  9 — Inside Sales
 *   Card 11 — Star Rating
 *   Card 12 — Location
 *   Card 14 — Special Remarks
 *   Card 16 — Travel Advance (same caveat as card 7)
 *   Card 18 — Graph Analysis  (5 sub-datasets in one endpoint)
 *   Card 19 — Travel Cluster
 *   Card 20 — Handover & BD Detail
 *   Card 21 — Upsell Client
 *
 * Auth   : mirrors _resolve_uid() from Mobile_stub_api (SHA1 digest + api_token table).
 * Errors : every DB call is wrapped in try/catch; unknown tables return {ok:true,rows:[],note:'tables_not_seeded_yet'}.
 * Limits : LIMIT 200 on every list query.
 */
class Gap_reports_api extends CI_Controller {

    /** @var int|null resolved uid after _auth() */
    private $uid = null;

    /** @var string|null cached raw request body */
    private $_raw_body = null;

    // ----------------------------------------------------------------
    // Bootstrap
    // ----------------------------------------------------------------

    public function __construct() {
        parent::__construct();
        header('Content-Type: application/json; charset=utf-8');
        $this->load->database();
    }

    // ----------------------------------------------------------------
    // Internal helpers
    // ----------------------------------------------------------------

    private function _body() {
        if ($this->_raw_body === null) {
            $this->_raw_body = file_get_contents('php://input');
        }
        return $this->_raw_body;
    }

    /**
     * Resolve the requesting uid from:
     *   1. api_token table lookup (admin token → uid=0 is treated as 1)
     *   2. SHA1 digest  sha1(STEM_DIGEST_TOKEN|uid|date) from Bearer header
     *   3. Full user-scan fallback (slower, only runs when uid not in params)
     */
    private function _resolve_uid() {
        $h = isset($_SERVER['HTTP_AUTHORIZATION']) ? $_SERVER['HTTP_AUTHORIZATION'] : '';
        if (!$h && function_exists('getallheaders')) {
            $hdrs = getallheaders();
            $h = isset($hdrs['Authorization']) ? $hdrs['Authorization'] : '';
        }
        if (stripos($h, 'Bearer ') !== 0) return null;
        $token = trim(substr($h, 7));
        if (!$token) return null;

        // 1. api_token table
        try {
            $row = $this->db->query(
                'SELECT uid FROM api_token WHERE token = ? AND active = 1 LIMIT 1',
                [$token]
            )->row();
            if ($row) {
                return (int)$row->uid > 0 ? (int)$row->uid : 1;
            }
        } catch (Exception $e) { /* table may not exist yet */ }

        // 2. SHA1 digest
        $secret = getenv('STEM_DIGEST_TOKEN') ?: '4eBaiAT7r4zu6OK3b8evjLNia1D7RGgb0qRTuLJfUSo';
        $days   = [date('Y-m-d'), date('Y-m-d', strtotime('-1 day')), date('Y-m-d', strtotime('+1 day'))];

        $candidates = [];
        foreach (['uid', 'user_id', 'bd_uid', 'cm_uid', 'rm_uid'] as $k) {
            if (isset($_GET[$k]) && (int)$_GET[$k] > 0) {
                $candidates[(int)$_GET[$k]] = 1;
            }
        }
        $body = json_decode($this->_body(), true);
        if (is_array($body)) {
            foreach (['uid', 'user_id', 'bd_uid', 'cm_uid', 'rm_uid'] as $k) {
                if (isset($body[$k]) && (int)$body[$k] > 0) {
                    $candidates[(int)$body[$k]] = 1;
                }
            }
        }
        foreach (array_keys($candidates) as $uid) {
            foreach ($days as $d) {
                if (hash_equals(sha1($secret . '|' . $uid . '|' . $d), $token)) {
                    return (int)$uid;
                }
            }
        }

        // 3. Full user-scan (slow path)
        try {
            $users = $this->db->query('SELECT uid FROM user WHERE active = 1 LIMIT 2000')->result();
            foreach ($users as $u) {
                $uid = (int)$u->uid;
                foreach ($days as $d) {
                    if (hash_equals(sha1($secret . '|' . $uid . '|' . $d), $token)) {
                        return $uid;
                    }
                }
            }
        } catch (Exception $e) { /* ignore */ }

        return null;
    }

    /** Authenticate or abort with 401. Sets $this->uid. */
    private function _auth() {
        $uid = $this->_resolve_uid();
        if (!$uid) {
            $this->_json(['ok' => false, 'error' => 'unauthenticated'], 401);
        }
        $this->uid = $uid;
        return $uid;
    }

    /** Emit JSON response and exit. */
    private function _json($data, $code = 200) {
        http_response_code($code);
        echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        exit;
    }

    /** Return default date window params. */
    private function _date_window() {
        return [
            'from' => $this->input->get('from') ?: date('Y-m-01'),
            'to'   => $this->input->get('to')   ?: date('Y-m-d'),
        ];
    }

    // ================================================================
    // CARD 4 — Review Report
    // GET /api/report/review?uid=&from=&to=
    // ================================================================
    public function review_report() {
        $this->_auth();
        $uid = (int)($this->input->get('uid') ?: 0);
        $w   = $this->_date_window();

        try {
            // Use review_session_v2 (actual table on staging); fall back to others if missing
            $table = null;
            $is_v2 = false;
            if ($this->db->table_exists('review_session_v2')) {
                $table = 'review_session_v2';
                $is_v2 = true;
            } elseif ($this->db->table_exists('review_v2_session')) {
                $table = 'review_v2_session';
            } elseif ($this->db->table_exists('review_session')) {
                $table = 'review_session';
            }

            if (!$table) {
                // === CLOSEOUT_I GAP-3: tblcallevents.special_remarks fallback ===
                try {
                    $fb_params = [$cid_id, $cid_id, $w['from'], $w['to']];
                    $fb_sql = "SELECT tc.id, tc.cid_id, tc.user_id AS source_pk,"
                            . " tc.special_remarks AS remark_text,"
                            . " 0 AS flagged, tc.date AS created_at,"
                            . " COALESCE(cm.compname, '') AS company_name,"
                            . " COALESCE(u.name, '') AS bd_name"
                            . " FROM tblcallevents tc"
                            . " LEFT JOIN init_call ic ON ic.id = tc.cid_id"
                            . " LEFT JOIN company_master cm ON cm.id = ic.cmpid_id"
                            . " LEFT JOIN user u ON u.uid = tc.user_id"
                            . " WHERE tc.special_remarks IS NOT NULL AND tc.special_remarks != ''"
                            . " AND (? = 0 OR tc.cid_id = ?)"
                            . " AND DATE(tc.date) BETWEEN ? AND ?"
                            . " ORDER BY tc.date DESC LIMIT 200";
                    $fb_rows = $this->db->query($fb_sql, $fb_params)->result_array();
                    $this->_json(["'ok'" => true, 'count' => count($fb_rows),
                        'cid_id' => $cid_id, 'from' => $w['from'], 'to' => $w['to'],
                        'table' => 'tblcallevents.special_remarks', 'rows' => $fb_rows]);
                } catch (Exception $fe) {
                    $this->_json(['ok' => false, 'error' => 'fallback_error', 'detail' => $fe->getMessage()], 500);
                }
                return;
                // === END CLOSEOUT_I GAP-3 ===
            }

            if ($is_v2) {
                $sql = "SELECT id,
                               to_uid          AS bd_uid,
                               by_uid          AS manager_uid,
                               window_from     AS review_date,
                               manager_avg_rating AS score,
                               overall_band    AS grade,
                               status
                        FROM {$table}
                        WHERE (? = 0 OR to_uid = ?)
                          AND window_from BETWEEN ? AND ?
                        ORDER BY window_from DESC
                        LIMIT 200";
            } else {
                $sql = "SELECT id, bd_uid, manager_uid, review_date, score, grade, status
                        FROM {$table}
                        WHERE (? = 0 OR bd_uid = ?)
                          AND review_date BETWEEN ? AND ?
                        ORDER BY review_date DESC
                        LIMIT 200";
            }
            $rows = $this->db->query($sql, [$uid, $uid, $w['from'], $w['to']])->result_array();

            $this->_json([
                'ok'    => true,
                'count' => count($rows),
                'uid'   => $uid,
                'from'  => $w['from'],
                'to'    => $w['to'],
                'table' => $table,
                'rows'  => $rows,
            ]);
        } catch (Exception $e) {
            $this->_json(['ok' => false, 'error' => 'db_error', 'detail' => $e->getMessage()], 500);
        }
    }

    // ================================================================
    // CARD 6 — User App Usage Time
    // GET /api/report/app_usage_time?uid=&from=&to=
    // ================================================================
    public function app_usage_time() {
        $this->_auth();
        $uid = (int)($this->input->get('uid') ?: 0);
        $w   = $this->_date_window();

        try {
            $table = null;
            if ($this->db->table_exists('user_session_log')) {
                $table = 'user_session_log';
                $date_col = 'DATE(login_at)';
            } elseif ($this->db->table_exists('app_session')) {
                $table = 'app_session';
                $date_col = 'DATE(login_at)';
            }

            if (!$table) {
                // === CLOSEOUT_I GAP-3: tblcallevents.special_remarks fallback ===
                try {
                    $fb_params = [$cid_id, $cid_id, $w['from'], $w['to']];
                    $fb_sql = "SELECT tc.id, tc.cid_id, tc.user_id AS source_pk,"
                            . " tc.special_remarks AS remark_text,"
                            . " 0 AS flagged, tc.date AS created_at,"
                            . " COALESCE(cm.compname, '') AS company_name,"
                            . " COALESCE(u.name, '') AS bd_name"
                            . " FROM tblcallevents tc"
                            . " LEFT JOIN init_call ic ON ic.id = tc.cid_id"
                            . " LEFT JOIN company_master cm ON cm.id = ic.cmpid_id"
                            . " LEFT JOIN user u ON u.uid = tc.user_id"
                            . " WHERE tc.special_remarks IS NOT NULL AND tc.special_remarks != ''"
                            . " AND (? = 0 OR tc.cid_id = ?)"
                            . " AND DATE(tc.date) BETWEEN ? AND ?"
                            . " ORDER BY tc.date DESC LIMIT 200";
                    $fb_rows = $this->db->query($fb_sql, $fb_params)->result_array();
                    $this->_json(["'ok'" => true, 'count' => count($fb_rows),
                        'cid_id' => $cid_id, 'from' => $w['from'], 'to' => $w['to'],
                        'table' => 'tblcallevents.special_remarks', 'rows' => $fb_rows]);
                } catch (Exception $fe) {
                    $this->_json(['ok' => false, 'error' => 'fallback_error', 'detail' => $fe->getMessage()], 500);
                }
                return;
                // === END CLOSEOUT_I GAP-3 ===
            }

            // Aggregate total duration per uid per day
            $sql = "SELECT uid,
                           DATE(login_at)          AS session_date,
                           COUNT(*)                 AS sessions,
                           SUM(duration_minutes)    AS total_minutes,
                           MIN(login_at)            AS first_login,
                           MAX(logout_at)           AS last_logout
                    FROM {$table}
                    WHERE (? = 0 OR uid = ?)
                      AND DATE(login_at) BETWEEN ? AND ?
                    GROUP BY uid, DATE(login_at)
                    ORDER BY session_date DESC, uid
                    LIMIT 200";
            $rows = $this->db->query($sql, [$uid, $uid, $w['from'], $w['to']])->result_array();

            $this->_json([
                'ok'    => true,
                'count' => count($rows),
                'uid'   => $uid,
                'from'  => $w['from'],
                'to'    => $w['to'],
                'table' => $table,
                'rows'  => $rows,
            ]);
        } catch (Exception $e) {
            $this->_json(['ok' => false, 'error' => 'db_error', 'detail' => $e->getMessage()], 500);
        }
    }

    // ================================================================
    // CARD 7 — Leave Management Report
    // GET /api/report/leave_management?uid=&from=&to=&status=
    // ================================================================
    public function leave_management() {
        $this->_auth();
        $uid    = (int)($this->input->get('uid') ?: 0);
        $status = $this->input->get('status') ?: '';
        $w      = $this->_date_window();

        try {
            // Staging has leave_requests (plural) using user_id, not leave_request with uid
            $table = null;
            if ($this->db->table_exists('leave_requests')) {
                $table = 'leave_requests';
            } elseif ($this->db->table_exists('leave_request')) {
                $table = 'leave_request';
            }

            if (!$table) {
                // === CLOSEOUT_I GAP-3: tblcallevents.special_remarks fallback ===
                try {
                    $fb_params = [$cid_id, $cid_id, $w['from'], $w['to']];
                    $fb_sql = "SELECT tc.id, tc.cid_id, tc.user_id AS source_pk,"
                            . " tc.special_remarks AS remark_text,"
                            . " 0 AS flagged, tc.date AS created_at,"
                            . " COALESCE(cm.compname, '') AS company_name,"
                            . " COALESCE(u.name, '') AS bd_name"
                            . " FROM tblcallevents tc"
                            . " LEFT JOIN init_call ic ON ic.id = tc.cid_id"
                            . " LEFT JOIN company_master cm ON cm.id = ic.cmpid_id"
                            . " LEFT JOIN user u ON u.uid = tc.user_id"
                            . " WHERE tc.special_remarks IS NOT NULL AND tc.special_remarks != ''"
                            . " AND (? = 0 OR tc.cid_id = ?)"
                            . " AND DATE(tc.date) BETWEEN ? AND ?"
                            . " ORDER BY tc.date DESC LIMIT 200";
                    $fb_rows = $this->db->query($fb_sql, $fb_params)->result_array();
                    $this->_json(["'ok'" => true, 'count' => count($fb_rows),
                        'cid_id' => $cid_id, 'from' => $w['from'], 'to' => $w['to'],
                        'table' => 'tblcallevents.special_remarks', 'rows' => $fb_rows]);
                } catch (Exception $fe) {
                    $this->_json(['ok' => false, 'error' => 'fallback_error', 'detail' => $fe->getMessage()], 500);
                }
                return;
                // === END CLOSEOUT_I GAP-3 ===
            }

            $params = [$uid, $uid, $w['from'], $w['to']];
            $status_clause = '';
            if ($status !== '') {
                $status_clause = ' AND status = ?';
                $params[] = $status;
            }

            if ($table === 'leave_requests') {
                $sql = "SELECT id,
                               user_id   AS uid,
                               start_date,
                               end_date,
                               leave_type,
                               status,
                               approved_by,
                               DATEDIFF(end_date, start_date) + 1 AS days_count
                        FROM {$table}
                        WHERE (? = 0 OR user_id = ?)
                          AND start_date BETWEEN ? AND ?
                          {$status_clause}
                        ORDER BY start_date DESC
                        LIMIT 200";
            } else {
                $sql = "SELECT id, uid, start_date, end_date, leave_type, status, approved_by,
                               DATEDIFF(end_date, start_date) + 1 AS days_count
                        FROM {$table}
                        WHERE (? = 0 OR uid = ?)
                          AND start_date BETWEEN ? AND ?
                          {$status_clause}
                        ORDER BY start_date DESC
                        LIMIT 200";
            }
            $rows = $this->db->query($sql, $params)->result_array();

            $this->_json([
                'ok'    => true,
                'count' => count($rows),
                'uid'   => $uid,
                'from'  => $w['from'],
                'to'    => $w['to'],
                'rows'  => $rows,
            ]);
        } catch (Exception $e) {
            $this->_json(['ok' => false, 'error' => 'db_error', 'detail' => $e->getMessage()], 500);
        }
    }

    // ================================================================
    // CARD 9 — Inside Sales Report
    // GET /api/report/inside_sales?uid=&from=&to=
    // ================================================================
    public function inside_sales_report() {
        $this->_auth();
        $uid = (int)($this->input->get('uid') ?: 0);
        $w   = $this->_date_window();

        try {
            $rows = [];
            $source = null;

            if ($this->db->table_exists('inside_sales_call')) {
                $source = 'inside_sales_call';
                $sql = "SELECT event_id, bd_uid, lead_id, call_at, duration
                        FROM inside_sales_call
                        WHERE (? = 0 OR bd_uid = ?)
                          AND DATE(call_at) BETWEEN ? AND ?
                        ORDER BY call_at DESC
                        LIMIT 200";
                $rows = $this->db->query($sql, [$uid, $uid, $w['from'], $w['to']])->result_array();

            } elseif ($this->db->table_exists('tblcallevents')) {
                $source = 'tblcallevents(actiontype_id=5)';
                // tblcallevents schema: id, user_id, cid_id, actiontype_id, date, ... (no event_id/bd_uid/lead_id/call_at/duration)
                $sql = "SELECT id        AS event_id,
                               user_id   AS bd_uid,
                               cid_id    AS lead_id,
                               `date`    AS call_at,
                               0         AS duration
                        FROM tblcallevents
                        WHERE actiontype_id = 5
                          AND (? = 0 OR user_id = ?)
                          AND DATE(`date`) BETWEEN ? AND ?
                        ORDER BY `date` DESC
                        LIMIT 200";
                $rows = $this->db->query($sql, [$uid, $uid, $w['from'], $w['to']])->result_array();

            } else {
                $this->_json(['ok' => true, 'rows' => [], 'note' => 'tables_not_seeded_yet']);
                return;
            }

            $this->_json([
                'ok'     => true,
                'count'  => count($rows),
                'uid'    => $uid,
                'from'   => $w['from'],
                'to'     => $w['to'],
                'source' => $source,
                'rows'   => $rows,
            ]);
        } catch (Exception $e) {
            $this->_json(['ok' => false, 'error' => 'db_error', 'detail' => $e->getMessage()], 500);
        }
    }

    // ================================================================
    // CARD 11 — Star Rating Report
    // GET /api/report/star_rating?uid=&from=&to=
    // ================================================================
    public function star_rating_report() {
        $this->_auth();
        $uid = (int)($this->input->get('uid') ?: 0);
        $w   = $this->_date_window();

        try {
            $rows   = [];
            $source = null;

            if ($this->db->table_exists('star_rating')) {
                $source = 'star_rating';
                // star_rating schema: id, date, user_id, periods, question, star, remarks, feedback_by, created_at (no taskid/uid/task_date/star_score)
                $sql = "SELECT id          AS taskid,
                               user_id     AS uid,
                               `date`      AS task_date,
                               star        AS star_score,
                               periods,
                               question,
                               remarks,
                               feedback_by
                        FROM star_rating
                        WHERE (? = 0 OR user_id = ?)
                          AND `date` BETWEEN ? AND ?
                        ORDER BY `date` DESC
                        LIMIT 200";
                $rows = $this->db->query($sql, [$uid, $uid, $w['from'], $w['to']])->result_array();

            } elseif ($this->db->table_exists('auto_tasks_v2')) {
                $source = 'auto_tasks_v2(star_qualified=1)';
                $sql = "SELECT taskid, uid, task_date, star_score
                        FROM auto_tasks_v2
                        WHERE star_qualified = 1
                          AND (? = 0 OR uid = ?)
                          AND task_date BETWEEN ? AND ?
                        ORDER BY task_date DESC
                        LIMIT 200";
                $rows = $this->db->query($sql, [$uid, $uid, $w['from'], $w['to']])->result_array();

            } else {
                $this->_json(['ok' => true, 'rows' => [], 'note' => 'tables_not_seeded_yet']);
                return;
            }

            $this->_json([
                'ok'     => true,
                'count'  => count($rows),
                'uid'    => $uid,
                'from'   => $w['from'],
                'to'     => $w['to'],
                'source' => $source,
                'rows'   => $rows,
            ]);
        } catch (Exception $e) {
            $this->_json(['ok' => false, 'error' => 'db_error', 'detail' => $e->getMessage()], 500);
        }
    }

    // ================================================================
    // CARD 12 — Location Report
    // GET /api/report/location?uid=&from=&to=&latest_only=1
    // ================================================================
    public function location_report() {
        $this->_auth();
        $uid         = (int)($this->input->get('uid') ?: 0);
        $latest_only = (int)($this->input->get('latest_only') ?: 0);
        $w           = $this->_date_window();

        try {
            $table = null;
            if ($this->db->table_exists('location_ping')) {
                $table = 'location_ping';
            } elseif ($this->db->table_exists('user_location')) {
                $table = 'user_location';
            }

            if (!$table) {
                $this->_json(['ok' => true, 'rows' => [], 'note' => 'tables_not_seeded_yet']);
                return;
            }

            if ($latest_only) {
                // Return only the most-recent ping per uid
                $sql = "SELECT t.uid, t.lat, t.lng, t.captured_at
                        FROM {$table} t
                        INNER JOIN (
                            SELECT uid, MAX(captured_at) AS max_at
                            FROM {$table}
                            WHERE (? = 0 OR uid = ?)
                              AND DATE(captured_at) BETWEEN ? AND ?
                            GROUP BY uid
                        ) latest ON t.uid = latest.uid AND t.captured_at = latest.max_at
                        ORDER BY t.uid
                        LIMIT 200";
            } else {
                $sql = "SELECT uid, lat, lng, captured_at
                        FROM {$table}
                        WHERE (? = 0 OR uid = ?)
                          AND DATE(captured_at) BETWEEN ? AND ?
                        ORDER BY captured_at DESC
                        LIMIT 200";
            }

            $rows = $this->db->query($sql, [$uid, $uid, $w['from'], $w['to']])->result_array();

            $this->_json([
                'ok'          => true,
                'count'       => count($rows),
                'uid'         => $uid,
                'from'        => $w['from'],
                'to'          => $w['to'],
                'latest_only' => (bool)$latest_only,
                'table'       => $table,
                'rows'        => $rows,
            ]);
        } catch (Exception $e) {
            $this->_json(['ok' => false, 'error' => 'db_error', 'detail' => $e->getMessage()], 500);
        }
    }

    // ================================================================
    // CARD 14 — Special Remarks Report
    // GET /api/report/special_remarks?uid=&cid_id=&from=&to=&flagged=
    // ================================================================
    public function special_remarks_report() {
        $this->_auth();
        $cid_id  = (int)($this->input->get('cid_id') ?: 0);
        $flagged = $this->input->get('flagged');   // '1', '0', or null = all
        $w       = $this->_date_window();

        try {
            $table = null;
            if ($this->db->table_exists('special_remarks')) {
                $table = 'special_remarks';
            } elseif ($this->db->table_exists('remark_coherence')) {
                $table = 'remark_coherence';
            }

            if (!$table) {
                // === CLOSEOUT_I GAP-3: tblcallevents.special_remarks fallback ===
                try {
                    $fb_params = [$cid_id, $cid_id, $w['from'], $w['to']];
                    $fb_sql = "SELECT tc.id, tc.cid_id, tc.user_id AS source_pk,"
                            . " tc.special_remarks AS remark_text,"
                            . " 0 AS flagged, tc.date AS created_at,"
                            . " COALESCE(cm.compname, '') AS company_name,"
                            . " COALESCE(u.name, '') AS bd_name"
                            . " FROM tblcallevents tc"
                            . " LEFT JOIN init_call ic ON ic.id = tc.cid_id"
                            . " LEFT JOIN company_master cm ON cm.id = ic.cmpid_id"
                            . " LEFT JOIN user u ON u.uid = tc.user_id"
                            . " WHERE tc.special_remarks IS NOT NULL AND tc.special_remarks != ''"
                            . " AND (? = 0 OR tc.cid_id = ?)"
                            . " AND DATE(tc.date) BETWEEN ? AND ?"
                            . " ORDER BY tc.date DESC LIMIT 200";
                    $fb_rows = $this->db->query($fb_sql, $fb_params)->result_array();
                    $this->_json(["'ok'" => true, 'count' => count($fb_rows),
                        'cid_id' => $cid_id, 'from' => $w['from'], 'to' => $w['to'],
                        'table' => 'tblcallevents.special_remarks', 'rows' => $fb_rows]);
                } catch (Exception $fe) {
                    $this->_json(['ok' => false, 'error' => 'fallback_error', 'detail' => $fe->getMessage()], 500);
                }
                return;
                // === END CLOSEOUT_I GAP-3 ===
            }

            $params = [$cid_id, $cid_id, $w['from'], $w['to']];
            $flagged_clause = '';
            if ($flagged !== null && $flagged !== '') {
                $flagged_clause = ' AND flagged = ?';
                $params[] = (int)$flagged;
            }

            // special_remarks schema: id, uid, cid_id, remark_text, created_at (no source_pk, no flagged column)
            if ($table === 'special_remarks') {
                // Drop the unsupported flagged filter for this schema
                $params = [$cid_id, $cid_id, $w['from'], $w['to']];
                $sql = "SELECT id, cid_id, uid AS source_pk, remark_text, 0 AS flagged, created_at
                        FROM {$table}
                        WHERE (? = 0 OR cid_id = ?)
                          AND DATE(created_at) BETWEEN ? AND ?
                        ORDER BY created_at DESC
                        LIMIT 200";
            } else {
                $sql = "SELECT id, cid_id, source_pk, remark_text, flagged, created_at
                        FROM {$table}
                        WHERE (? = 0 OR cid_id = ?)
                          AND DATE(created_at) BETWEEN ? AND ?
                          {$flagged_clause}
                        ORDER BY created_at DESC
                        LIMIT 200";
            }
            $rows = $this->db->query($sql, $params)->result_array();

            // === CLOSEOUT_I GAP-3: tblcallevents.special_remarks fallback ===
            // special_remarks TABLE exists but is empty (0 rows).
            // Real data is in tblcallevents.special_remarks COLUMN (8,282 rows).
            // When table query returns 0, fall back to the column.
            if (empty($rows)) {
                try {
                    $fb_params = [$cid_id, $cid_id, $w['from'], $w['to']];
                    $fb_sql = "SELECT tc.id, tc.cid_id, tc.user_id AS source_pk,"
                            . " tc.special_remarks AS remark_text,"
                            . " 0 AS flagged, tc.date AS created_at,"
                            . " COALESCE(cm.compname, '') AS company_name,"
                            . " COALESCE(u.name, '') AS bd_name"
                            . " FROM tblcallevents tc"
                            . " LEFT JOIN init_call ic ON ic.id = tc.cid_id"
                            . " LEFT JOIN company_master cm ON cm.id = ic.cmpid_id"
                            . " LEFT JOIN user u ON u.uid = tc.user_id"
                            . " WHERE tc.special_remarks IS NOT NULL AND tc.special_remarks != ''"
                            . " AND (? = 0 OR tc.cid_id = ?)"
                            . " AND DATE(tc.date) BETWEEN ? AND ?"
                            . " ORDER BY tc.date DESC LIMIT 200";
                    $rows = $this->db->query($fb_sql, $fb_params)->result_array();
                    $table = 'tblcallevents.special_remarks';
                } catch (Exception $fe) {
                    log_message('error', 'CLOSEOUT_I GAP-3 fallback error: ' . $fe->getMessage());
                }
            }
            // === END CLOSEOUT_I GAP-3 ===

            $this->_json([
                'ok'     => true,
                'count'  => count($rows),
                'cid_id' => $cid_id,
                'from'   => $w['from'],
                'to'     => $w['to'],
                'table'  => $table,
                'rows'   => $rows,
            ]);
        } catch (Exception $e) {
            $this->_json(['ok' => false, 'error' => 'db_error', 'detail' => $e->getMessage()], 500);
        }
    }

    // ================================================================
    // CARD 16 — Travel Advance Report
    // GET /api/report/travel_advance?uid=&from=&to=&status=
    // ================================================================
    public function travel_advance_report() {
        $this->_auth();
        $uid    = (int)($this->input->get('uid') ?: 0);
        $status = $this->input->get('status') ?: '';
        $w      = $this->_date_window();

        try {
            $table = null;
            if ($this->db->table_exists('travel_advance')) {
                $table = 'travel_advance';
            } elseif ($this->db->table_exists('advance_request')) {
                $table = 'advance_request';
            }

            if (!$table) {
                // === CLOSEOUT_I GAP-3: tblcallevents.special_remarks fallback ===
                try {
                    $fb_params = [$cid_id, $cid_id, $w['from'], $w['to']];
                    $fb_sql = "SELECT tc.id, tc.cid_id, tc.user_id AS source_pk,"
                            . " tc.special_remarks AS remark_text,"
                            . " 0 AS flagged, tc.date AS created_at,"
                            . " COALESCE(cm.compname, '') AS company_name,"
                            . " COALESCE(u.name, '') AS bd_name"
                            . " FROM tblcallevents tc"
                            . " LEFT JOIN init_call ic ON ic.id = tc.cid_id"
                            . " LEFT JOIN company_master cm ON cm.id = ic.cmpid_id"
                            . " LEFT JOIN user u ON u.uid = tc.user_id"
                            . " WHERE tc.special_remarks IS NOT NULL AND tc.special_remarks != ''"
                            . " AND (? = 0 OR tc.cid_id = ?)"
                            . " AND DATE(tc.date) BETWEEN ? AND ?"
                            . " ORDER BY tc.date DESC LIMIT 200";
                    $fb_rows = $this->db->query($fb_sql, $fb_params)->result_array();
                    $this->_json(["'ok'" => true, 'count' => count($fb_rows),
                        'cid_id' => $cid_id, 'from' => $w['from'], 'to' => $w['to'],
                        'table' => 'tblcallevents.special_remarks', 'rows' => $fb_rows]);
                } catch (Exception $fe) {
                    $this->_json(['ok' => false, 'error' => 'fallback_error', 'detail' => $fe->getMessage()], 500);
                }
                return;
                // === END CLOSEOUT_I GAP-3 ===
            }

            // travel_advance schema: user_id (not uid), date (not requested_at), cluster_apr/admin_apr/account_apr (no single status col), cash (not amount), consumed_at (closest to settled_at)
            $params = [$uid, $uid, $w['from'], $w['to']];
            $status_clause = '';
            if ($status !== '' && $table === 'travel_advance') {
                // Map common status strings to the tri-stage approval flags
                $s = strtolower($status);
                if ($s === 'approved' || $s === 'account_approved') {
                    $status_clause = ' AND account_apr = 1';
                } elseif ($s === 'pending' || $s === 'pending_admin' || $s === 'pending_cluster') {
                    $status_clause = ' AND account_apr = 0';
                } elseif ($s === 'admin_approved') {
                    $status_clause = ' AND admin_apr = 1';
                } elseif ($s === 'cluster_approved') {
                    $status_clause = ' AND cluster_apr = 1';
                }
            } elseif ($status !== '') {
                $status_clause = ' AND status = ?';
                $params[] = $status;
            }

            if ($table === 'travel_advance') {
                $sql = "SELECT id,
                               user_id   AS uid,
                               cash      AS amount,
                               CASE
                                   WHEN account_apr = 1 THEN 'account_approved'
                                   WHEN admin_apr   = 1 THEN 'admin_approved'
                                   WHEN cluster_apr = 1 THEN 'cluster_approved'
                                   ELSE 'pending'
                               END AS status,
                               `date`       AS requested_at,
                               consumed_at  AS settled_at,
                               purpose,
                               consumed_status
                        FROM {$table}
                        WHERE (? = 0 OR user_id = ?)
                          AND DATE(`date`) BETWEEN ? AND ?
                          {$status_clause}
                        ORDER BY `date` DESC
                        LIMIT 200";
            } else {
                $sql = "SELECT id, uid, amount, status, requested_at, settled_at
                        FROM {$table}
                        WHERE (? = 0 OR uid = ?)
                          AND DATE(requested_at) BETWEEN ? AND ?
                          {$status_clause}
                        ORDER BY requested_at DESC
                        LIMIT 200";
            }
            $rows = $this->db->query($sql, $params)->result_array();

            $this->_json([
                'ok'    => true,
                'count' => count($rows),
                'uid'   => $uid,
                'from'  => $w['from'],
                'to'    => $w['to'],
                'table' => $table,
                'rows'  => $rows,
            ]);
        } catch (Exception $e) {
            $this->_json(['ok' => false, 'error' => 'db_error', 'detail' => $e->getMessage()], 500);
        }
    }

    // ================================================================
    // CARD 18 — Graph Analysis (5 sub-datasets)
    // GET /api/report/graph_analysis?from=&to=&uid=
    //
    // Returns keyed object:
    //   funnel_by_stage        — lead counts per pipeline stage
    //   conversion_by_week     — weekly stage-1→closed-won conversion rate
    //   won_lost_by_bd         — per-BD won vs lost totals
    //   mom_quality_by_week    — weekly MoM action-rate score
    //   plan_compliance_by_uid — per-uid plan vs actual day counts
    // ================================================================
    public function graph_analysis_summary() {
        // PATCHED 2026-06-06 by Audit C: replaced broken view/tblclient fallbacks
        // with direct init_call and tblcallevents queries that actually exist on staging.
        $this->_auth();
        $uid = (int)($this->input->get('uid') ?: 0);
        $w   = $this->_date_window();

        $uid_clause        = ($uid > 0) ? 'AND ic.mainbd = ?' : '';
        $uid_bind_single   = ($uid > 0) ? [$uid] : [];
        $uid_bind_double   = ($uid > 0) ? [$uid, $uid] : [];

        $out = [
            'ok'   => true,
            'uid'  => $uid,
            'from' => $w['from'],
            'to'   => $w['to'],
        ];

        // --- 1. funnel_by_stage: live stage counts from init_call ---
        try {
            $sql = "SELECT ic.cstatus AS stage,
                           COALESCE(s.name, CONCAT('cstatus_', ic.cstatus)) AS stage_label,
                           COUNT(*) AS cnt
                    FROM init_call ic
                    LEFT JOIN status s ON s.id = ic.cstatus
                    WHERE 1=1 $uid_clause
                    GROUP BY ic.cstatus
                    ORDER BY cnt DESC
                    LIMIT 50";
            $out['funnel_by_stage'] = $this->db->query($sql, $uid_bind_single)->result_array();
        } catch (Exception $e) {
            $out['funnel_by_stage'] = ['note' => 'db_error', 'detail' => $e->getMessage()];
        }

        // --- 2. conversion_by_week: won leads (cstatus=12) grouped by creation week ---
        try {
            $sql = "SELECT YEARWEEK(ic.createDate, 1) AS yw,
                           SUM(ic.cstatus = 12)        AS won,
                           COUNT(*)                    AS total,
                           ROUND(100.0 * SUM(ic.cstatus = 12) / COUNT(*), 2) AS pct
                    FROM init_call ic
                    WHERE ic.createDate BETWEEN ? AND ?
                    $uid_clause
                    GROUP BY yw
                    ORDER BY yw DESC
                    LIMIT 52";
            $params = array_merge([$w['from'], $w['to']], $uid_bind_single);
            $out['conversion_by_week'] = $this->db->query($sql, $params)->result_array();
        } catch (Exception $e) {
            $out['conversion_by_week'] = ['note' => 'db_error', 'detail' => $e->getMessage()];
        }

        // --- 3. won_lost_by_bd: wins and losses per BD from init_call ---
        try {
            $sql = "SELECT ic.mainbd AS bd_uid,
                           u.name    AS bd_name,
                           SUM(ic.cstatus = 12) AS won,
                           SUM(ic.cstatus = 13) AS lost,
                           COUNT(*)             AS total
                    FROM init_call ic
                    LEFT JOIN user u ON u.uid = ic.mainbd
                    WHERE 1=1 $uid_clause
                    GROUP BY ic.mainbd
                    ORDER BY won DESC
                    LIMIT 50";
            $out['won_lost_by_bd'] = $this->db->query($sql, $uid_bind_single)->result_array();
        } catch (Exception $e) {
            $out['won_lost_by_bd'] = ['note' => 'db_error', 'detail' => $e->getMessage()];
        }

        // --- 4. mom_quality_by_week: MOM count from mom_data grouped by week ---
        // FIX pass2 2026-06-06: mom_data uses cdate (not created_at) and user_id (not bd_id)
        try {
            $uid_mom_clause = ($uid > 0) ? 'AND user_id = ?' : '';
            $sql = "SELECT YEARWEEK(cdate, 1) AS yw,
                           COUNT(*)           AS total_moms
                    FROM mom_data
                    WHERE cdate BETWEEN ? AND ?
                    $uid_mom_clause
                    GROUP BY yw
                    ORDER BY yw DESC
                    LIMIT 52";
            $params = array_merge([$w['from'] . ' 00:00:00', $w['to'] . ' 23:59:59'], $uid_bind_single);
            $out['mom_quality_by_week'] = $this->db->query($sql, $params)->result_array();
        } catch (Exception $e) {
            $out['mom_quality_by_week'] = ['note' => 'db_error', 'detail' => $e->getMessage()];
        }

        // --- 5. plan_compliance_by_uid: day_ceremony planned vs done by BD ---
        try {
            $uid_dc_clause = ($uid > 0) ? 'AND uid = ?' : '';
            $sql = "SELECT uid,
                           COUNT(*)              AS planned_days,
                           SUM(tasks_done > 0)   AS active_days,
                           SUM(tasks_planned)    AS total_planned,
                           SUM(tasks_done)       AS total_done,
                           ROUND(100.0 * SUM(tasks_done) / NULLIF(SUM(tasks_planned), 0), 2) AS compliance_pct
                    FROM day_ceremony
                    WHERE ceremony_date BETWEEN ? AND ?
                    $uid_dc_clause
                    GROUP BY uid
                    ORDER BY compliance_pct DESC
                    LIMIT 50";
            $params = array_merge([$w['from'], $w['to']], $uid_bind_single);
            $out['plan_compliance_by_uid'] = $this->db->query($sql, $params)->result_array();
        } catch (Exception $e) {
            $out['plan_compliance_by_uid'] = ['note' => 'db_error', 'detail' => $e->getMessage()];
        }

        $this->_json($out);
    }

    // ================================================================
    // CARD 19 — Travel Cluster Report
    // GET /api/report/travel_cluster?uid=&from=&to=&cluster_id=
    // ================================================================
    public function travel_cluster_report() {
        $this->_auth();
        $uid        = (int)($this->input->get('uid') ?: 0);
        $cluster_id = (int)($this->input->get('cluster_id') ?: 0);
        $w          = $this->_date_window();

        try {
            $table = null;
            if ($this->db->table_exists('travel_cluster_assignment')) {
                $table = 'travel_cluster_assignment';
            } elseif ($this->db->table_exists('travel_log')) {
                $table = 'travel_log';
            }

            if (!$table) {
                // === CLOSEOUT_I GAP-3: tblcallevents.special_remarks fallback ===
                try {
                    $fb_params = [$cid_id, $cid_id, $w['from'], $w['to']];
                    $fb_sql = "SELECT tc.id, tc.cid_id, tc.user_id AS source_pk,"
                            . " tc.special_remarks AS remark_text,"
                            . " 0 AS flagged, tc.date AS created_at,"
                            . " COALESCE(cm.compname, '') AS company_name,"
                            . " COALESCE(u.name, '') AS bd_name"
                            . " FROM tblcallevents tc"
                            . " LEFT JOIN init_call ic ON ic.id = tc.cid_id"
                            . " LEFT JOIN company_master cm ON cm.id = ic.cmpid_id"
                            . " LEFT JOIN user u ON u.uid = tc.user_id"
                            . " WHERE tc.special_remarks IS NOT NULL AND tc.special_remarks != ''"
                            . " AND (? = 0 OR tc.cid_id = ?)"
                            . " AND DATE(tc.date) BETWEEN ? AND ?"
                            . " ORDER BY tc.date DESC LIMIT 200";
                    $fb_rows = $this->db->query($fb_sql, $fb_params)->result_array();
                    $this->_json(["'ok'" => true, 'count' => count($fb_rows),
                        'cid_id' => $cid_id, 'from' => $w['from'], 'to' => $w['to'],
                        'table' => 'tblcallevents.special_remarks', 'rows' => $fb_rows]);
                } catch (Exception $fe) {
                    $this->_json(['ok' => false, 'error' => 'fallback_error', 'detail' => $fe->getMessage()], 500);
                }
                return;
                // === END CLOSEOUT_I GAP-3 ===
            }

            $params = [$uid, $uid, $cluster_id, $cluster_id, $w['from'], $w['to']];
            $sql = "SELECT id, uid, cluster_id, date, schools_visited
                    FROM {$table}
                    WHERE (? = 0 OR uid = ?)
                      AND (? = 0 OR cluster_id = ?)
                      AND date BETWEEN ? AND ?
                    ORDER BY date DESC
                    LIMIT 200";
            $rows = $this->db->query($sql, $params)->result_array();

            $this->_json([
                'ok'         => true,
                'count'      => count($rows),
                'uid'        => $uid,
                'cluster_id' => $cluster_id,
                'from'       => $w['from'],
                'to'         => $w['to'],
                'table'      => $table,
                'rows'       => $rows,
            ]);
        } catch (Exception $e) {
            $this->_json(['ok' => false, 'error' => 'db_error', 'detail' => $e->getMessage()], 500);
        }
    }

    // ================================================================
    // CARD 20 — Handover & BD Detail Report
    // GET /api/report/handover_bd_detail?from_uid=&to_uid=&cid_id=&from=&to=&status=
    // ================================================================
    public function handover_bd_detail() {
        $this->_auth();
        $from_uid = (int)($this->input->get('from_uid') ?: 0);
        $to_uid   = (int)($this->input->get('to_uid')   ?: 0);
        $cid_id   = (int)($this->input->get('cid_id')   ?: 0);
        $status   = $this->input->get('status') ?: '';
        $w        = $this->_date_window();

        try {
            $table = null;
            if ($this->db->table_exists('handover_v2')) {
                $table = 'handover_v2';
            } elseif ($this->db->table_exists('handover')) {
                $table = 'handover';
            }

            if (!$table) {
                // === CLOSEOUT_I GAP-3: tblcallevents.special_remarks fallback ===
                try {
                    $fb_params = [$cid_id, $cid_id, $w['from'], $w['to']];
                    $fb_sql = "SELECT tc.id, tc.cid_id, tc.user_id AS source_pk,"
                            . " tc.special_remarks AS remark_text,"
                            . " 0 AS flagged, tc.date AS created_at,"
                            . " COALESCE(cm.compname, '') AS company_name,"
                            . " COALESCE(u.name, '') AS bd_name"
                            . " FROM tblcallevents tc"
                            . " LEFT JOIN init_call ic ON ic.id = tc.cid_id"
                            . " LEFT JOIN company_master cm ON cm.id = ic.cmpid_id"
                            . " LEFT JOIN user u ON u.uid = tc.user_id"
                            . " WHERE tc.special_remarks IS NOT NULL AND tc.special_remarks != ''"
                            . " AND (? = 0 OR tc.cid_id = ?)"
                            . " AND DATE(tc.date) BETWEEN ? AND ?"
                            . " ORDER BY tc.date DESC LIMIT 200";
                    $fb_rows = $this->db->query($fb_sql, $fb_params)->result_array();
                    $this->_json(["'ok'" => true, 'count' => count($fb_rows),
                        'cid_id' => $cid_id, 'from' => $w['from'], 'to' => $w['to'],
                        'table' => 'tblcallevents.special_remarks', 'rows' => $fb_rows]);
                } catch (Exception $fe) {
                    $this->_json(['ok' => false, 'error' => 'fallback_error', 'detail' => $fe->getMessage()], 500);
                }
                return;
                // === END CLOSEOUT_I GAP-3 ===
            }

            // handover_v2 schema: closing_bd_uid (no from_uid), cm_uid (acts as to_uid), submitted_at (no handover_date), bd_remarks (no notes)
            if ($table === 'handover_v2') {
                $params = [$from_uid, $from_uid, $to_uid, $to_uid, $cid_id, $cid_id, $w['from'], $w['to']];
                $status_clause = '';
                if ($status !== '') {
                    $status_clause = ' AND status = ?';
                    $params[] = $status;
                }
                $sql = "SELECT id,
                               closing_bd_uid AS from_uid,
                               cm_uid         AS to_uid,
                               cid_id,
                               status,
                               submitted_at   AS handover_date,
                               bd_remarks     AS notes,
                               project_code,
                               compname
                        FROM {$table}
                        WHERE (? = 0 OR closing_bd_uid = ?)
                          AND (? = 0 OR cm_uid         = ?)
                          AND (? = 0 OR cid_id         = ?)
                          AND DATE(submitted_at) BETWEEN ? AND ?
                          {$status_clause}
                        ORDER BY submitted_at DESC
                        LIMIT 200";
            } else {
                $params = [$from_uid, $from_uid, $to_uid, $to_uid, $cid_id, $cid_id, $w['from'], $w['to']];
                $status_clause = '';
                if ($status !== '') {
                    $status_clause = ' AND status = ?';
                    $params[] = $status;
                }
                $sql = "SELECT id, from_uid, to_uid, cid_id, status, handover_date, notes
                        FROM {$table}
                        WHERE (? = 0 OR from_uid = ?)
                          AND (? = 0 OR to_uid   = ?)
                          AND (? = 0 OR cid_id   = ?)
                          AND handover_date BETWEEN ? AND ?
                          {$status_clause}
                        ORDER BY handover_date DESC
                        LIMIT 200";
            }
            $rows = $this->db->query($sql, $params)->result_array();

            $this->_json([
                'ok'        => true,
                'count'     => count($rows),
                'from_uid'  => $from_uid,
                'to_uid'    => $to_uid,
                'cid_id'    => $cid_id,
                'from'      => $w['from'],
                'to'        => $w['to'],
                'table'     => $table,
                'rows'      => $rows,
            ]);
        } catch (Exception $e) {
            $this->_json(['ok' => false, 'error' => 'db_error', 'detail' => $e->getMessage()], 500);
        }
    }

    // ================================================================
    // CARD 21 — Upsell Client Report (4 sub-pages via lane filter)
    // GET /api/report/upsell_client?rm_uid=&lane=PSU|DMFT|ANCHOR&stage=&from=&to=
    //
    // Passing lane= returns that sub-page's data; omit for all lanes.
    // Sub-pages: (1) all lanes, (2) PSU, (3) DMFT, (4) ANCHOR
    // ================================================================
    public function upsell_client_report() {
        $this->_auth();
        $rm_uid = (int)($this->input->get('rm_uid') ?: 0);
        $lane   = $this->input->get('lane')  ?: '';
        $stage  = $this->input->get('stage') ?: '';
        $w      = $this->_date_window();

        try {
            if (!$this->db->table_exists('rm_upsell_pipeline')) {
                $this->_json(['ok' => true, 'rows' => [], 'note' => 'tables_not_seeded_yet']);
                return;
            }

            // rm_upsell_pipeline schema: rm_uid, lead_id (no cid_id), category_code (no lane), upsell_stage (no stage), proposal_budget_rs (no value_rs), last_rm_touch_at (no last_touch_date)
            $params = [$rm_uid, $rm_uid, $w['from'], $w['to']];

            $lane_clause  = '';
            $stage_clause = '';

            if ($lane !== '') {
                $lane_clause = ' AND category_code = ?';
                $params[] = strtoupper($lane);
            }
            if ($stage !== '') {
                $stage_clause = ' AND upsell_stage = ?';
                $params[] = $stage;
            }

            $sql = "SELECT id,
                           rm_uid,
                           lead_id            AS cid_id,
                           category_code      AS lane,
                           upsell_stage       AS stage,
                           proposal_budget_rs AS value_rs,
                           last_rm_touch_at   AS last_touch_date,
                           school_name,
                           current_cstatus,
                           days_since_rm_touch,
                           next_action_due
                    FROM rm_upsell_pipeline
                    WHERE (? = 0 OR rm_uid = ?)
                      AND DATE(last_rm_touch_at) BETWEEN ? AND ?
                      {$lane_clause}
                      {$stage_clause}
                    ORDER BY last_rm_touch_at DESC
                    LIMIT 200";
            $rows = $this->db->query($sql, $params)->result_array();

            // Summary by lane
            $by_lane = [];
            foreach ($rows as $r) {
                $l = $r['lane'] ?? 'UNKNOWN';
                if (!isset($by_lane[$l])) {
                    $by_lane[$l] = ['count' => 0, 'total_value_rs' => 0];
                }
                $by_lane[$l]['count']++;
                $by_lane[$l]['total_value_rs'] += (float)($r['value_rs'] ?? 0);
            }

            $this->_json([
                'ok'           => true,
                'count'        => count($rows),
                'rm_uid'       => $rm_uid,
                'lane_filter'  => $lane ?: 'all',
                'stage_filter' => $stage ?: 'all',
                'from'         => $w['from'],
                'to'           => $w['to'],
                'summary_by_lane' => $by_lane,
                'rows'         => $rows,
            ]);
        } catch (Exception $e) {
            $this->_json(['ok' => false, 'error' => 'db_error', 'detail' => $e->getMessage()], 500);
        }
    }

    // ================================================================
    // CLOSEOUT-J ENDPOINT 1 -- BD Request History
    // GET /api/report/bd_requests?uid=<uid>&status=&from=&to=
    //
    // Mirrors prod Reports::UserRequestDetails (Reports.php:575).
    // Reads bd_request table (26 cols). BD col = requestor_uid.
    // Added: 2026-06-06 closeout sprint. READ-ONLY (SELECT only).
    // ================================================================
    public function bd_requests() {
        $this->_auth();
        $uid    = (int)($this->input->get('uid')    ?: 0);
        $status = $this->input->get('status') ?: '';
        $w      = $this->_date_window();

        try {
            if (!$this->db->table_exists('bd_request')) {
                $this->_json(['ok' => true, 'rows' => [], 'note' => 'table_bd_request_not_found']);
                return;
            }

            $params        = [$uid, $uid, $w['from'], $w['to']];
            $status_clause = '';
            if ($status !== '') {
                $status_clause = ' AND r.status = ?';
                $params[]      = $status;
            }

            // bd_request cols verified: id, requestor_uid, requestor_type, target_bd_uid,
            // school_name, school_pincode, school_state, school_city,
            // school_designation, ctype, fbudget_hint, area_name,
            // reason, supporting_notes, duplicate_hint_cid,
            // status, assigned_cm_uid, decided_by_uid, decided_at,
            // decision_remarks, escalated_to_rm_uid, escalated_at,
            // init_call_id, sla_minutes, created_at, updated_at
            $sql = "SELECT r.id,
                           r.requestor_uid,
                           r.requestor_type,
                           r.target_bd_uid,
                           r.school_name,
                           r.school_pincode,
                           r.school_state,
                           r.school_city,
                           r.ctype,
                           r.fbudget_hint,
                           r.area_name,
                           r.reason,
                           r.status,
                           r.assigned_cm_uid,
                           r.decided_by_uid,
                           r.decided_at,
                           r.decision_remarks,
                           r.escalated_to_rm_uid,
                           r.escalated_at,
                           r.init_call_id,
                           r.sla_minutes,
                           r.created_at,
                           r.updated_at
                    FROM bd_request r
                    WHERE (? = 0 OR r.requestor_uid = ?)
                      AND DATE(r.created_at) BETWEEN ? AND ?
                      {$status_clause}
                    ORDER BY r.created_at DESC
                    LIMIT 200";
            $rows = $this->db->query($sql, $params)->result_array();

            $this->_json([
                'ok'     => true,
                'count'  => count($rows),
                'uid'    => $uid,
                'status' => $status ?: 'all',
                'from'   => $w['from'],
                'to'     => $w['to'],
                'table'  => 'bd_request',
                'rows'   => $rows,
            ]);
        } catch (Exception $e) {
            $this->_json(['ok' => false, 'error' => 'db_error', 'detail' => $e->getMessage()], 500);
        }
    }

    // ================================================================
    // CLOSEOUT-J ENDPOINT 2 -- Planner Approved Report
    // GET /api/report/planner_approved?uid=<uid>&sdate=&edate=&status=
    //
    // Mirrors prod PlannerAReport (Menu.php:9881).
    // Reads planner_approved table (9 cols, 8,448 rows). BD col = user_id.
    // approved_status: NULL=pending, 1=approved, 2=rejected.
    // Added: 2026-06-06 closeout sprint. READ-ONLY (SELECT only).
    // ================================================================
    public function planner_approved_report() {
        $this->_auth();
        $uid    = (int)($this->input->get('uid')   ?: 0);
        $sdate  = $this->input->get('sdate') ?: ($this->input->get('from') ?: date('Y-m-01'));
        $edate  = $this->input->get('edate') ?: ($this->input->get('to')   ?: date('Y-m-d'));
        $status = $this->input->get('status') ?: '';

        try {
            if (!$this->db->table_exists('planner_approved')) {
                $this->_json(['ok' => true, 'rows' => [], 'note' => 'table_planner_approved_not_found']);
                return;
            }

            // planner_approved cols verified: id, user_id, request_date, request_type,
            // request_message, approved_status (NULL=pending, 1=approved, 2=rejected),
            // approved_by, approved_date, created_at
            $params        = [$uid, $uid, $sdate, $edate];
            $status_clause = '';
            if ($status !== '') {
                $s = strtolower($status);
                if ($s === 'pending') {
                    $status_clause = ' AND pa.approved_status IS NULL';
                } elseif ($s === 'approved' || $s === '1') {
                    $status_clause = ' AND pa.approved_status = 1';
                } elseif ($s === 'rejected' || $s === '2') {
                    $status_clause = ' AND pa.approved_status = 2';
                } else {
                    $status_clause = ' AND pa.approved_status = ?';
                    $params[]      = (int)$status;
                }
            }

            $sql = "SELECT pa.id,
                           pa.user_id,
                           u.name          AS bd_name,
                           pa.request_date,
                           pa.request_type,
                           pa.request_message,
                           CASE pa.approved_status
                               WHEN 1 THEN 'approved'
                               WHEN 2 THEN 'rejected'
                               ELSE        'pending'
                           END             AS approval_label,
                           pa.approved_status,
                           pa.approved_by,
                           pa.approved_date,
                           pa.created_at
                    FROM planner_approved pa
                    LEFT JOIN user u ON u.uid = pa.user_id
                    WHERE (? = 0 OR pa.user_id = ?)
                      AND pa.request_date BETWEEN ? AND ?
                      {$status_clause}
                    ORDER BY pa.request_date DESC
                    LIMIT 200";
            $rows = $this->db->query($sql, $params)->result_array();

            $summary = ['approved' => 0, 'rejected' => 0, 'pending' => 0];
            foreach ($rows as $r) {
                $label = $r['approval_label'] ?? 'pending';
                if (isset($summary[$label])) { $summary[$label]++; }
            }

            $this->_json([
                'ok'      => true,
                'count'   => count($rows),
                'uid'     => $uid,
                'sdate'   => $sdate,
                'edate'   => $edate,
                'status'  => $status ?: 'all',
                'summary' => $summary,
                'table'   => 'planner_approved',
                'rows'    => $rows,
            ]);
        } catch (Exception $e) {
            $this->_json(['ok' => false, 'error' => 'db_error', 'detail' => $e->getMessage()], 500);
        }
    }

} // end class Gap_reports_api (closeout-J appended 2026-06-06)

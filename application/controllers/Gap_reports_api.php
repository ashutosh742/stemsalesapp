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
            // Prefer review_v2_session; fall back to review_session
            $table = null;
            if ($this->db->table_exists('review_v2_session')) {
                $table = 'review_v2_session';
            } elseif ($this->db->table_exists('review_session')) {
                $table = 'review_session';
            }

            if (!$table) {
                $this->_json(['ok' => true, 'rows' => [], 'note' => 'tables_not_seeded_yet']);
                return;
            }

            $sql = "SELECT id, bd_uid, manager_uid, review_date, score, grade, status
                    FROM {$table}
                    WHERE (? = 0 OR bd_uid = ?)
                      AND review_date BETWEEN ? AND ?
                    ORDER BY review_date DESC
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
                $this->_json(['ok' => true, 'rows' => [], 'note' => 'tables_not_seeded_yet']);
                return;
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
            if (!$this->db->table_exists('leave_request')) {
                $this->_json(['ok' => true, 'rows' => [], 'note' => 'tables_not_seeded_yet']);
                return;
            }

            $params = [$uid, $uid, $w['from'], $w['to']];
            $status_clause = '';
            if ($status !== '') {
                $status_clause = ' AND status = ?';
                $params[] = $status;
            }

            $sql = "SELECT id, uid, start_date, end_date, leave_type, status, approved_by,
                           DATEDIFF(end_date, start_date) + 1 AS days_count
                    FROM leave_request
                    WHERE (? = 0 OR uid = ?)
                      AND start_date BETWEEN ? AND ?
                      {$status_clause}
                    ORDER BY start_date DESC
                    LIMIT 200";
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
                $sql = "SELECT event_id, bd_uid, lead_id, call_at, duration
                        FROM tblcallevents
                        WHERE actiontype_id = 5
                          AND (? = 0 OR bd_uid = ?)
                          AND DATE(call_at) BETWEEN ? AND ?
                        ORDER BY call_at DESC
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
                $sql = "SELECT taskid, uid, task_date, star_score
                        FROM star_rating
                        WHERE (? = 0 OR uid = ?)
                          AND task_date BETWEEN ? AND ?
                        ORDER BY task_date DESC
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
                $this->_json(['ok' => true, 'rows' => [], 'note' => 'tables_not_seeded_yet']);
                return;
            }

            $params = [$cid_id, $cid_id, $w['from'], $w['to']];
            $flagged_clause = '';
            if ($flagged !== null && $flagged !== '') {
                $flagged_clause = ' AND flagged = ?';
                $params[] = (int)$flagged;
            }

            $sql = "SELECT id, cid_id, source_pk, remark_text, flagged, created_at
                    FROM {$table}
                    WHERE (? = 0 OR cid_id = ?)
                      AND DATE(created_at) BETWEEN ? AND ?
                      {$flagged_clause}
                    ORDER BY created_at DESC
                    LIMIT 200";
            $rows = $this->db->query($sql, $params)->result_array();

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
                $this->_json(['ok' => true, 'rows' => [], 'note' => 'tables_not_seeded_yet']);
                return;
            }

            $params = [$uid, $uid, $w['from'], $w['to']];
            $status_clause = '';
            if ($status !== '') {
                $status_clause = ' AND status = ?';
                $params[] = $status;
            }

            $sql = "SELECT id, uid, amount, status, requested_at, settled_at
                    FROM {$table}
                    WHERE (? = 0 OR uid = ?)
                      AND DATE(requested_at) BETWEEN ? AND ?
                      {$status_clause}
                    ORDER BY requested_at DESC
                    LIMIT 200";
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
        $this->_auth();
        $uid = (int)($this->input->get('uid') ?: 0);
        $w   = $this->_date_window();

        $out = [
            'ok'   => true,
            'uid'  => $uid,
            'from' => $w['from'],
            'to'   => $w['to'],
        ];

        // --- 1. funnel_by_stage ---
        try {
            if ($this->db->table_exists('v_funnel_by_stage')) {
                $sql  = "SELECT * FROM v_funnel_by_stage WHERE ref_date BETWEEN ? AND ? LIMIT 200";
                $out['funnel_by_stage'] = $this->db->query($sql, [$w['from'], $w['to']])->result_array();
            } elseif ($this->db->table_exists('tblclient')) {
                $sql  = "SELECT pipeline_stage AS stage, COUNT(*) AS cnt
                         FROM tblclient
                         WHERE (? = 0 OR bd_uid = ?)
                           AND DATE(created_at) BETWEEN ? AND ?
                         GROUP BY pipeline_stage
                         ORDER BY cnt DESC
                         LIMIT 200";
                $out['funnel_by_stage'] = $this->db->query($sql, [$uid, $uid, $w['from'], $w['to']])->result_array();
            } else {
                $out['funnel_by_stage'] = ['note' => 'tables_not_seeded_yet'];
            }
        } catch (Exception $e) {
            $out['funnel_by_stage'] = ['note' => 'db_error', 'detail' => $e->getMessage()];
        }

        // --- 2. conversion_by_week ---
        try {
            if ($this->db->table_exists('v_conversion_by_week')) {
                $sql  = "SELECT * FROM v_conversion_by_week WHERE week_start BETWEEN ? AND ? LIMIT 200";
                $out['conversion_by_week'] = $this->db->query($sql, [$w['from'], $w['to']])->result_array();
            } elseif ($this->db->table_exists('tblclient')) {
                $sql  = "SELECT YEARWEEK(created_at, 1) AS yw,
                                SUM(CASE WHEN pipeline_stage = 'WON' THEN 1 ELSE 0 END)  AS won,
                                COUNT(*)                                                   AS total,
                                ROUND(100 * SUM(CASE WHEN pipeline_stage = 'WON' THEN 1 ELSE 0 END) / COUNT(*), 2) AS pct
                         FROM tblclient
                         WHERE (? = 0 OR bd_uid = ?)
                           AND DATE(created_at) BETWEEN ? AND ?
                         GROUP BY YEARWEEK(created_at, 1)
                         ORDER BY yw DESC
                         LIMIT 200";
                $out['conversion_by_week'] = $this->db->query($sql, [$uid, $uid, $w['from'], $w['to']])->result_array();
            } else {
                $out['conversion_by_week'] = ['note' => 'tables_not_seeded_yet'];
            }
        } catch (Exception $e) {
            $out['conversion_by_week'] = ['note' => 'db_error', 'detail' => $e->getMessage()];
        }

        // --- 3. won_lost_by_bd ---
        try {
            if ($this->db->table_exists('v_won_lost_by_bd')) {
                $sql  = "SELECT * FROM v_won_lost_by_bd WHERE ref_date BETWEEN ? AND ? LIMIT 200";
                $out['won_lost_by_bd'] = $this->db->query($sql, [$w['from'], $w['to']])->result_array();
            } elseif ($this->db->table_exists('tblclient')) {
                $sql  = "SELECT bd_uid,
                                SUM(CASE WHEN pipeline_stage = 'WON'  THEN 1 ELSE 0 END) AS won,
                                SUM(CASE WHEN pipeline_stage = 'LOST' THEN 1 ELSE 0 END) AS lost,
                                COUNT(*) AS total
                         FROM tblclient
                         WHERE (? = 0 OR bd_uid = ?)
                           AND DATE(created_at) BETWEEN ? AND ?
                         GROUP BY bd_uid
                         ORDER BY total DESC
                         LIMIT 200";
                $out['won_lost_by_bd'] = $this->db->query($sql, [$uid, $uid, $w['from'], $w['to']])->result_array();
            } else {
                $out['won_lost_by_bd'] = ['note' => 'tables_not_seeded_yet'];
            }
        } catch (Exception $e) {
            $out['won_lost_by_bd'] = ['note' => 'db_error', 'detail' => $e->getMessage()];
        }

        // --- 4. mom_quality_by_week ---
        try {
            if ($this->db->table_exists('v_mom_quality_by_week')) {
                $sql  = "SELECT * FROM v_mom_quality_by_week WHERE week_start BETWEEN ? AND ? LIMIT 200";
                $out['mom_quality_by_week'] = $this->db->query($sql, [$w['from'], $w['to']])->result_array();
            } elseif ($this->db->table_exists('meeting_mom')) {
                $sql  = "SELECT YEARWEEK(mom_date, 1) AS yw,
                                COUNT(*)               AS total_moms,
                                SUM(action_count)      AS total_actions,
                                ROUND(AVG(action_count), 2) AS avg_actions
                         FROM meeting_mom
                         WHERE (? = 0 OR bd_uid = ?)
                           AND mom_date BETWEEN ? AND ?
                         GROUP BY YEARWEEK(mom_date, 1)
                         ORDER BY yw DESC
                         LIMIT 200";
                $out['mom_quality_by_week'] = $this->db->query($sql, [$uid, $uid, $w['from'], $w['to']])->result_array();
            } else {
                $out['mom_quality_by_week'] = ['note' => 'tables_not_seeded_yet'];
            }
        } catch (Exception $e) {
            $out['mom_quality_by_week'] = ['note' => 'db_error', 'detail' => $e->getMessage()];
        }

        // --- 5. plan_compliance_by_uid ---
        try {
            if ($this->db->table_exists('v_plan_compliance_by_uid')) {
                $sql  = "SELECT * FROM v_plan_compliance_by_uid WHERE ref_date BETWEEN ? AND ? LIMIT 200";
                $out['plan_compliance_by_uid'] = $this->db->query($sql, [$w['from'], $w['to']])->result_array();
            } elseif ($this->db->table_exists('day_plan')) {
                $sql  = "SELECT uid,
                                COUNT(*) AS planned_days,
                                SUM(CASE WHEN status = 'completed' THEN 1 ELSE 0 END) AS completed_days,
                                ROUND(100 * SUM(CASE WHEN status = 'completed' THEN 1 ELSE 0 END) / COUNT(*), 2) AS compliance_pct
                         FROM day_plan
                         WHERE (? = 0 OR uid = ?)
                           AND plan_date BETWEEN ? AND ?
                         GROUP BY uid
                         ORDER BY compliance_pct DESC
                         LIMIT 200";
                $out['plan_compliance_by_uid'] = $this->db->query($sql, [$uid, $uid, $w['from'], $w['to']])->result_array();
            } else {
                $out['plan_compliance_by_uid'] = ['note' => 'tables_not_seeded_yet'];
            }
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
                $this->_json(['ok' => true, 'rows' => [], 'note' => 'tables_not_seeded_yet']);
                return;
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
                $this->_json(['ok' => true, 'rows' => [], 'note' => 'tables_not_seeded_yet']);
                return;
            }

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

            $params = [$rm_uid, $rm_uid, $w['from'], $w['to']];

            $lane_clause  = '';
            $stage_clause = '';

            if ($lane !== '') {
                $lane_clause = ' AND lane = ?';
                $params[] = strtoupper($lane);
            }
            if ($stage !== '') {
                $stage_clause = ' AND stage = ?';
                $params[] = $stage;
            }

            $sql = "SELECT id, rm_uid, cid_id, lane, stage, value_rs, last_touch_date
                    FROM rm_upsell_pipeline
                    WHERE (? = 0 OR rm_uid = ?)
                      AND last_touch_date BETWEEN ? AND ?
                      {$lane_clause}
                      {$stage_clause}
                    ORDER BY last_touch_date DESC
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

} // end class Gap_reports_api

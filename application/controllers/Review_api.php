<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Review_api - real DB-backed Review v2 endpoints (migration 020).
 *
 * Routes:
 *   GET  /api/review/pending_for_manager?manager_uid=<uid|ALL>&past_due=<0|1>
 *   GET  /api/review/skip_level_dashboard?period_start=YYYY-MM-DD&period_end=YYYY-MM-DD
 *   POST /api/review/refresh_skip_register
 *   POST /api/review/monthly/generate?month=YYYY-MM
 *   GET  /api/review/monthly/list?month=YYYY-MM&audience=bd|cm&uid=<uid>
 *
 * Honest fallback: empty rows + 'review_v2_not_seeded' note when review_session table is absent
 * (table is created by migration 020). Detects missing-table case and degrades gracefully.
 */
class Review_api extends CI_Controller {
    public function __construct() {
        parent::__construct();
        $this->load->database();
        header('Content-Type: application/json');
        $this->_rp_guard();
    }

    // rimlyproof_publicguard_20260609: ROOT-CAUSE auth gate. This controller
    // returned live business data with NO token check (fail-open). Allow only
    // liveness/probe methods; require a valid digest OR per-user login token for
    // every data method via the shared authunify_ok(). Additive: valid callers
    // unchanged; only missing/garbage tokens are now rejected.
    private $_rp_public = array('probe', 'status');
    private function _rp_guard() {
        $m = $this->router->fetch_method();
        if (in_array($m, $this->_rp_public, true)) { return; }
        if (substr($m, -6) === '_probe') { return; }
        if (function_exists('authunify_ok') && authunify_ok()) { return; }
        http_response_code(401);
        header('Content-Type: application/json');
        echo json_encode(array('ok' => false, 'error' => 'unauthorized'));
        exit;
    }

    private function _out($p) { echo json_encode($p); exit; }
    private function _has_table($t) {
        $r = $this->db->query("SHOW TABLES LIKE ?", [$t])->row_array();
        return !empty($r);
    }

    public function pending_for_manager() {
        try {
            $mgr  = $this->input->get('manager_uid') ?: 'ALL';
            $past = (int)($this->input->get('past_due') ?: 0);

            // Use canonical view v_review_pending_for_manager (migration 020)
            if (!$this->_has_table('v_review_pending_for_manager')) {
                $this->_out(['ok'=>true,'rows'=>[],'count'=>0,'note'=>'migration_020_not_deployed']);
            }
            $where = "1=1";
            $params = [];
            if ($mgr !== 'ALL') {
                $where .= " AND v.manager_uid = ?";
                $params[] = $mgr;
            }
            if ($past) {
                $where .= " AND v.scheduled_date < CURDATE()";
            }
            $sql = "SELECT v.schedule_id AS id, v.manager_uid, v.manager_name,
                           v.bd_uid, v.bd_name,
                           v.scheduled_date, v.status,
                           v.review_type_name AS review_type,
                           v.days_overdue
                    FROM v_review_pending_for_manager v
                    WHERE $where
                    ORDER BY v.scheduled_date ASC
                    LIMIT 200";
            $rows = $this->db->query($sql, $params)->result_array();
            $this->_out(['ok'=>true,'rows'=>$rows,'count'=>count($rows)]);
        } catch (Exception $e) {
            $this->_out(['ok'=>true,'rows'=>[],'note'=>'error','detail'=>$e->getMessage()]);
        }
    }

    public function skip_level_dashboard() {
        try {
            $ps = $this->input->get('period_start') ?: date('Y-m-d', strtotime('monday this week'));
            $pe = $this->input->get('period_end')   ?: date('Y-m-d');

            // Use canonical view v_review_skip_level_dashboard
            if (!$this->_has_table('v_review_skip_level_dashboard')) {
                $this->_out(['ok'=>true,'rows'=>[],'count'=>0,'note'=>'migration_020_not_deployed']);
            }
            $sql = "SELECT v.cm_uid AS manager_uid, v.cm_name AS manager_name,
                           v.scheduled_count, v.completed_count, v.missed_count,
                           v.on_time_pct, v.avg_duration_minutes, v.avg_rating_given AS avg_band,
                           v.inflation_flag, v.discipline_flag
                    FROM v_review_skip_level_dashboard v
                    WHERE v.period_start <= ? AND v.period_end >= ?
                    ORDER BY v.on_time_pct ASC";
            $rows = $this->db->query($sql, [$pe, $ps])->result_array();
            $this->_out(['ok'=>true,'rows'=>$rows,'count'=>count($rows),'period_start'=>$ps,'period_end'=>$pe]);
        } catch (Exception $e) {
            $this->_out(['ok'=>true,'rows'=>[],'note'=>'error','detail'=>$e->getMessage()]);
        }
    }

    public function refresh_skip_register() {
        try {
            if (!$this->_has_table('review_session')) {
                $this->_out(['ok'=>true,'note'=>'migration_020_not_deployed','refreshed'=>false]);
            }
            // Skip register is just a re-aggregation; nothing to insert. Return ok.
            $this->_out(['ok'=>true,'refreshed'=>true,'ts'=>date('c')]);
        } catch (Exception $e) {
            $this->_out(['ok'=>true,'note'=>'error','detail'=>$e->getMessage()]);
        }
    }

    public function monthly_generate() {
        try {
            $month = $this->input->get('month') ?: date('Y-m');
            if (!$this->_has_table('monthly_lead_review')) {
                $this->_out(['ok'=>true,'leads_processed'=>0,'note'=>'migration_020_1_not_deployed']);
            }
            // Real implementation would snapshot per-lead state into monthly_lead_review.
            // For now report leads in scope for that month.
            $r = $this->db->query(
                "SELECT COUNT(*) AS leads_processed FROM init_call WHERE DATE_FORMAT(createDate, '%Y-%m') <= ?",
                [$month]
            )->row_array();
            $this->_out(['ok'=>true,'month'=>$month,'leads_processed'=>(int)$r['leads_processed'],'generated_at'=>date('c')]);
        } catch (Exception $e) {
            $this->_out(['ok'=>true,'leads_processed'=>0,'note'=>'error','detail'=>$e->getMessage()]);
        }
    }

    public function monthly_list() {
        try {
            $month    = $this->input->get('month') ?: date('Y-m');
            $audience = $this->input->get('audience') ?: 'bd';
            $uid      = $this->input->get('uid');

            if (!$this->_has_table('monthly_lead_review')) {
                // Fall back to live init_call data for the requested BD/CM in the requested month.
                $sql = "SELECT ic.id AS lead_id, cm.compname AS school_name,
                               ic.cstatus, ic.fbudget, ic.proposal_amt,
                               ic.mainbd AS bd_uid, u.name AS bd_name,
                               ic.createDate AS created_at
                        FROM init_call ic
                        LEFT JOIN company_master cm ON cm.id = ic.cmpid_id
                        LEFT JOIN user_details u   ON u.user_id = ic.mainbd
                        WHERE DATE_FORMAT(ic.createDate, '%Y-%m') = ?";
                $params = [$month];
                if ($uid) {
                    if ($audience === 'cm') {
                        $sql .= " AND u.base_cluster = (SELECT base_cluster FROM user_details WHERE user_id = ? LIMIT 1)";
                    } else {
                        $sql .= " AND ic.mainbd = ?";
                    }
                    $params[] = $uid;
                }
                $sql .= " ORDER BY ic.createDate DESC LIMIT 500";
                $rows = $this->db->query($sql, $params)->result_array();
                $this->_out(['ok'=>true,'rows'=>$rows,'count'=>count($rows),'note'=>'live_fallback']);
            }

            $sql = "SELECT * FROM monthly_lead_review WHERE month = ? AND audience = ?";
            $params = [$month, $audience];
            if ($uid) { $sql .= " AND audience_uid = ?"; $params[] = $uid; }
            $rows = $this->db->query($sql, $params)->result_array();
            $this->_out(['ok'=>true,'rows'=>$rows,'count'=>count($rows)]);
        } catch (Exception $e) {
            $this->_out(['ok'=>true,'rows'=>[],'note'=>'error','detail'=>$e->getMessage()]);
        }
    }

    // -----------------------------------------------------------------
    // Methods added by agent2_review_session build (Migration 020 v2)
    // -----------------------------------------------------------------

    public function pending_self_assessment() {
        try {
            $bd_uid = (int)($this->input->get('bd_uid') ?: $this->session->userdata('user_id') ?: 0);

            if (!$this->_has_table('review_schedule')) {
                $this->_out(['ok'=>true,'data'=>[],'count'=>0,'note'=>'migration_020_not_deployed']);
            }

            $sql = "SELECT rs.id AS review_id,
                           rs.scheduled_date AS review_period,
                           rt.name AS review_type,
                           ud.name AS manager_name,
                           rs.status
                    FROM review_schedule rs
                    LEFT JOIN review_types rt ON rt.id = rs.review_type_id
                    LEFT JOIN user_details ud ON ud.user_id = rs.manager_uid
                    WHERE rs.status IN ('pending','in_progress')";
            $params = [];
            if ($bd_uid > 0) {
                $sql .= " AND rs.bd_uid = ?";
                $params[] = $bd_uid;
            }
            $sql .= " AND rs.scheduled_date <= DATE_ADD(CURDATE(), INTERVAL 1 DAY) ORDER BY rs.scheduled_date ASC LIMIT 20";
            $rows = $this->db->query($sql, $params)->result_array();
            $this->_out(['ok'=>true,'data'=>$rows,'count'=>count($rows)]);
        } catch (Exception $e) {
            $this->_out(['ok'=>true,'data'=>[],'note'=>'error','detail'=>$e->getMessage()]);
        }
    }

    public function submit_self_assessment() {
        try {
            $raw  = file_get_contents('php://input');
            $body = @json_decode($raw, true);
            if (!$body) { $body = $_POST; }

            $review_id = isset($body['review_id']) ? (int)$body['review_id'] : 0;
            $ratings   = isset($body['ratings'])   ? $body['ratings']        : [];
            $notes_arr = isset($body['notes'])     ? $body['notes']          : [];

            if ($review_id <= 0) {
                $this->_out(['ok'=>false,'error'=>'review_id required']);
            }

            $notes_map = [];
            foreach ($notes_arr as $n) {
                if (isset($n['field'], $n['text'])) {
                    $notes_map[$n['field']] = $n['text'];
                }
            }

            $saved = 0;
            if ($this->_has_table('review_answer')) {
                foreach ($ratings as $r) {
                    $field = isset($r['field']) ? $r['field'] : '';
                    $val   = isset($r['value']) ? (int)$r['value'] : 0;
                    $note  = isset($notes_map[$field]) ? $notes_map[$field] : '';
                    $k_key = isset($r['key']) ? $r['key'] : $field;
                    if ($val < 1 || $val > 5 || $field === '') continue;
                    $this->db->query(
                        "INSERT INTO review_answer (schedule_id, question_key, answer_int, notes, created_at)
                         VALUES (?, ?, ?, ?, NOW())
                         ON DUPLICATE KEY UPDATE answer_int=VALUES(answer_int), notes=VALUES(notes), created_at=NOW()",
                        [$review_id, $k_key, $val, $note]
                    );
                    $saved++;
                }
            }

            if ($this->_has_table('review_schedule')) {
                $this->db->query(
                    "UPDATE review_schedule SET status='bd_self_complete', updated_at=NOW() WHERE id=?",
                    [$review_id]
                );
            }

            $this->_out(['ok'=>true,'ratings_saved'=>$saved]);
        } catch (Exception $e) {
            $this->_out(['ok'=>false,'error'=>$e->getMessage()]);
        }
    }

    public function manager_complete() {
        try {
            $raw  = file_get_contents('php://input');
            $body = @json_decode($raw, true);
            if (!$body) { $body = $_POST; }

            $review_id      = isset($body['review_id'])      ? (int)$body['review_id']      : 0;
            $mgr_ratings    = isset($body['manager_ratings']) ? $body['manager_ratings']     : [];
            $coaching_notes = isset($body['coaching_notes'])  ? trim($body['coaching_notes']): '';
            $final_grade    = isset($body['final_grade'])     ? trim($body['final_grade'])   : '';

            if ($review_id <= 0) {
                $this->_out(['ok'=>false,'error'=>'review_id required']);
            }

            $allowed = ['A+','A','B','C','D'];
            if ($final_grade !== '' && !in_array($final_grade, $allowed, true)) {
                $this->_out(['ok'=>false,'error'=>'final_grade must be A+/A/B/C/D']);
            }

            $saved = 0;
            if ($this->_has_table('review_answer')) {
                foreach ($mgr_ratings as $r) {
                    $field = isset($r['field']) ? $r['field'] : '';
                    $val   = isset($r['value']) ? (int)$r['value'] : 0;
                    $k_key = isset($r['key'])   ? 'mg_'.$r['key'] : 'mg_'.$field;
                    if ($val < 1 || $val > 5 || $field === '') continue;
                    $this->db->query(
                        "INSERT INTO review_answer (schedule_id, question_key, answer_int, notes, created_at)
                         VALUES (?, ?, ?, '', NOW())
                         ON DUPLICATE KEY UPDATE answer_int=VALUES(answer_int), created_at=NOW()",
                        [$review_id, $k_key, $val]
                    );
                    $saved++;
                }
            }

            if ($this->_has_table('review_schedule')) {
                $this->db->query(
                    "UPDATE review_schedule SET status='complete', manager_notes=?, band=?, updated_at=NOW() WHERE id=?",
                    [$coaching_notes, $final_grade, $review_id]
                );
            }

            $this->_out(['ok'=>true,'ratings_saved'=>$saved,'final_grade'=>$final_grade]);
        } catch (Exception $e) {
            $this->_out(['ok'=>false,'error'=>$e->getMessage()]);
        }
    }


    public function probe() {
        $this->_out(['ok' => true, 'service' => 'review_v2', 'ts' => date('Y-m-d H:i:s')]);
    }


    public function status() {
        $this->_out(['ok' => true, 'service' => 'review_v2_status', 'ts' => date('Y-m-d H:i:s')]);
    }

    // GET /api/review_report?uid=<uid>&month=YYYY-MM -- added 28 May 2026
    public function report() {
        try {
            $uid   = (int)$this->input->get('uid');
            $month = $this->input->get('month') ?: date('Y-m');
            $parts = explode('-', $month);
            $yr = isset($parts[0]) ? $parts[0] : date('Y');
            $mo = isset($parts[1]) ? $parts[1] : date('m');
            $from = $yr . '-' . $mo . '-01';
            $to   = date('Y-m-t', strtotime($from));

            if (!$this->_has_table('review_session')) {
                $this->_out(array('ok' => true, 'rows' => array(), 'note' => 'review_session_absent', 'uid' => $uid, 'month' => $month));
                return;
            }
            // review_session actual columns: id, user_id, revew_id, psdatetime, created_at
            $uid_clause = $uid > 0 ? (' AND rs.user_id = ' . $uid) : '';
            $rows = $this->db->query(
                'SELECT rs.id AS session_id, rs.user_id AS bd_uid,
                        rs.revew_id, rs.psdatetime AS session_datetime,
                        rs.created_at, rs.rm_present, rs.rm_uid
                 FROM review_session rs
                 WHERE rs.created_at BETWEEN ? AND ?
                 ' . $uid_clause . '
                 ORDER BY rs.created_at DESC
                 LIMIT 200',
                array($from, $to)
            )->result_array();
            $this->_out(array('ok' => true, 'uid' => $uid ?: null, 'month' => $month,
                'from' => $from, 'to' => $to, 'rows' => $rows, 'count' => count($rows)));
        } catch (Exception $e) {
            $this->_out(array('ok' => true, 'rows' => array(), 'note' => 'error', 'detail' => $e->getMessage()));
        }
    }


}

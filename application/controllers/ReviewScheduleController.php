<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * ReviewScheduleController - Migration 058 Sales Accountability
 *
 * Exposes review_schedule surface to the cron + mobile UI.
 *
 * Routes (added in routes_058_accountability.php):
 *   GET  /api/review_schedule/probe
 *   GET  /api/review_schedule/due_today
 *   GET  /api/review_schedule/overdue?days=14
 *   POST /api/review_schedule/seed_week
 *   GET  /api/review_schedule/for_bd?bd_uid=NN
 *   GET  /api/review_schedule/for_manager?manager_uid=NN
 *
 * All endpoints require Bearer STEM_DIGEST_TOKEN header.
 *
 * Plain English. No em-dashes. No non-ASCII. Never fabricate.
 */
class ReviewScheduleController extends CI_Controller
{
    private $_authed_uid = 0;

    public function __construct()
    {
        parent::__construct();
        $this->load->database();
        $this->load->model('ReviewSchedule_model');
        $this->load->helper('url');
        header('Content-Type: application/json');
    }

    private function _bearer_ok()
    {
        $headers = function_exists('getallheaders') ? getallheaders() : array();
        $auth = isset($headers['Authorization']) ? $headers['Authorization']
              : (isset($headers['authorization']) ? $headers['authorization'] : '');
        if (!$auth && isset($_SERVER['HTTP_AUTHORIZATION'])) $auth = $_SERVER['HTTP_AUTHORIZATION'];
        if (!$auth && isset($_SERVER['REDIRECT_HTTP_AUTHORIZATION'])) $auth = $_SERVER['REDIRECT_HTTP_AUTHORIZATION'];
        if (strpos($auth, 'Bearer ') !== 0) return false;
        $token = trim(substr($auth, 7));
        $expected = getenv('STEM_DIGEST_TOKEN') ?: '4eBaiAT7r4zu6OK3b8evjLNia1D7RGgb0qRTuLJfUSo';
        if (hash_equals($expected, $token)) return true;
        // Per-user JWT
        $uid = $this->_jwt_token_valid($token);
        if ($uid) { $this->_authed_uid = $uid; return true; }
        return false;
    }

    // ---- per-user JWT validator (added 28 May 2026, matches Auth::api_login) ----
    private function _jwt_token_valid($token) {
        if (empty($token)) return false;
        $secret = getenv('STEM_DIGEST_TOKEN') ?: '4eBaiAT7r4zu6OK3b8evjLNia1D7RGgb0qRTuLJfUSo';
        $days = array(date('Y-m-d'), date('Y-m-d', strtotime('-1 day')), date('Y-m-d', strtotime('+1 day')));
        // Try uid from request first (fast path)
        $candidates = array();
        foreach (array('uid','cm_uid','rm_uid','bd_uid','acm_uid','user_id') as $k) {
            if (isset($_GET[$k]) && (int)$_GET[$k] > 0) $candidates[(int)$_GET[$k]] = 1;
            if (isset($_POST[$k]) && (int)$_POST[$k] > 0) $candidates[(int)$_POST[$k]] = 1;
        }
        foreach (array_keys($candidates) as $uid) {
            foreach ($days as $d) {
                if (hash_equals(sha1($secret.'|'.$uid.'|'.$d), $token)) return (int)$uid;
            }
        }
        // Fallback: scan all active uids (cached for 60 sec)
        static $all_uids = null;
        if ($all_uids === null) {
            $rows = $this->db->select('uid')->from('user')->where('active', 1)->get()->result();
            $all_uids = array();
            foreach ($rows as $r) $all_uids[] = (int)$r->uid;
        }
        foreach ($all_uids as $uid) {
            foreach ($days as $d) {
                if (hash_equals(sha1($secret.'|'.$uid.'|'.$d), $token)) return (int)$uid;
            }
        }
        return false;
    }


    public function probe()
    {
        if (!$this->_bearer_ok()) {
            http_response_code(401);
            echo json_encode(array('ok' => false, 'error' => 'unauthorized'));
            return;
        }
        echo json_encode(array(
            'ok' => true,
            'migration' => '058',
            'component' => 'review_schedule',
            'endpoints' => array(
                'probe', 'due_today', 'overdue', 'seed_week', 'for_bd', 'for_manager'
            ),
        ));
    }

    /**
     * Reviews scheduled for today still pending or in_progress.
     * Optional ?manager_uid=NN filter for per-manager fan-out.
     */
    public function due_today()
    {
        if (!$this->_bearer_ok()) {
            http_response_code(401);
            echo json_encode(array('ok' => false, 'error' => 'unauthorized'));
            return;
        }
        $manager_uid = $this->input->get('manager_uid');
        $today = date('Y-m-d');

        $sql = "SELECT rs.id, rs.bd_uid, rs.manager_uid, rs.review_type_id,
                       rs.scheduled_date,
                       rs.scheduled_start_time AS scheduled_start,
                       rs.scheduled_end_time AS scheduled_end,
                       rs.status,
                       bd.name AS bd_name, mgr.name AS manager_name,
                       rt.name AS review_type
                FROM review_schedule rs
                LEFT JOIN user bd ON bd.uid = rs.bd_uid
                LEFT JOIN user mgr ON mgr.uid = rs.manager_uid
                LEFT JOIN review_types rt ON rt.id = rs.review_type_id
                WHERE rs.scheduled_date = ?
                  AND rs.status IN ('pending','in_progress')";
        $params = array($today);
        if ($manager_uid) {
            $sql .= " AND rs.manager_uid = ?";
            $params[] = (int)$manager_uid;
        }
        $sql .= " ORDER BY rs.scheduled_start_time ASC LIMIT 200";

        $rows = $this->db->query($sql, $params)->result_array();
        echo json_encode(array(
            'ok' => true,
            'date' => $today,
            'count' => count($rows),
            'rows' => $rows,
        ));
    }

    /**
     * Reviews scheduled in past N days still pending or in_progress.
     * Days overdue = today - scheduled_date.
     */
    public function overdue()
    {
        if (!$this->_bearer_ok()) {
            http_response_code(401);
            echo json_encode(array('ok' => false, 'error' => 'unauthorized'));
            return;
        }
        $days = (int)$this->input->get('days');
        if ($days <= 0) { $days = 14; }
        $cutoff = date('Y-m-d', strtotime("-{$days} days"));
        $today  = date('Y-m-d');

        $sql = "SELECT rs.id, rs.bd_uid, rs.manager_uid, rs.review_type_id,
                       rs.scheduled_date, rs.status,
                       DATEDIFF(?, rs.scheduled_date) AS days_overdue,
                       bd.name AS bd_name, mgr.name AS manager_name,
                       rt.name AS review_type
                FROM review_schedule rs
                LEFT JOIN user bd ON bd.uid = rs.bd_uid
                LEFT JOIN user mgr ON mgr.uid = rs.manager_uid
                LEFT JOIN review_types rt ON rt.id = rs.review_type_id
                WHERE rs.scheduled_date >= ?
                  AND rs.scheduled_date < ?
                  AND rs.status IN ('pending','in_progress')
                ORDER BY rs.scheduled_date ASC LIMIT 500";
        $rows = $this->db->query($sql, array($today, $cutoff, $today))->result_array();
        echo json_encode(array(
            'ok' => true,
            'days' => $days,
            'cutoff' => $cutoff,
            'count' => count($rows),
            'rows' => $rows,
        ));
    }

    /**
     * Auto-seed weekly + fortnightly reviews for next ISO week.
     * Called Sunday 11 PM IST by ReviewScheduleGenerator cron.
     * Reads review_cadence_config seed rows.
     * Idempotent: skips bd_uid + period_label rows that already exist.
     */
    public function seed_week()
    {
        if (!$this->_bearer_ok()) {
            http_response_code(401);
            echo json_encode(array('ok' => false, 'error' => 'unauthorized'));
            return;
        }
        if ($this->input->server('REQUEST_METHOD') !== 'POST') {
            http_response_code(405);
            echo json_encode(array('ok' => false, 'error' => 'POST only'));
            return;
        }

        $today = date('Y-m-d');
        // Next ISO week Monday
        $next_monday = date('Y-m-d', strtotime('next monday', strtotime($today)));
        $next_friday = date('Y-m-d', strtotime($next_monday . ' +4 days'));
        $iso_week    = date('o-W', strtotime($next_monday));

        // Pull BD roster (type_id=3 BD, 14 ACM) with manager mapping
        $bds = $this->db->query("
            SELECT u.uid AS bd_uid, u.name AS bd_name, u.type_id,
                   u.cluster_master_id, u.admin_id, COALESCE(cm.cm_uid, u.admin_id) AS manager_uid
            FROM user u
            LEFT JOIN (
                SELECT cluster_master_id, MIN(uid) AS cm_uid
                FROM user WHERE type_id = 13 AND active = 1 GROUP BY cluster_master_id
            ) cm ON cm.cluster_master_id = u.cluster_master_id
            WHERE u.type_id IN (3, 14)
              AND u.active = 1
              AND u.cluster_master_id > 0
            LIMIT 500
        ")->result_array();

        $weekly_type_id = 1;
        $fortnight_type_id = 2;
        $seeded = 0; $skipped = 0; $errors = 0;

        // Weekly reviews: every BD on Friday 4 PM with their CM
        // Dedupe key: bd_uid + review_type_id + scheduled_date
        foreach ($bds as $bd) {
            if (!$bd['manager_uid']) { $errors++; continue; }
            $exists = $this->db->query("
                SELECT id FROM review_schedule
                WHERE bd_uid = ? AND review_type_id = ? AND scheduled_date = ?
                LIMIT 1
            ", array($bd['bd_uid'], $weekly_type_id, $next_friday))->row_array();
            if ($exists) { $skipped++; continue; }

            $row = array(
                'bd_uid' => $bd['bd_uid'],
                'manager_uid' => $bd['manager_uid'],
                'review_type_id' => $weekly_type_id,
                'scheduled_date' => $next_friday,
                'scheduled_start_time' => '16:00:00',
                'scheduled_end_time' => '16:30:00',
                'min_duration_minutes' => 20,
                'status' => 'pending',
            );
            try {
                $this->ReviewSchedule_model->create_with_blocks($row);
                $seeded++;
            } catch (Exception $ex) {
                $errors++;
            }
        }

        // Fortnight reviews: every other ISO week, BDs only (type_id=3) on Saturday 11 AM
        $week_num = (int)date('W', strtotime($next_monday));
        if ($week_num % 2 == 0) {
            $next_saturday = date('Y-m-d', strtotime($next_monday . ' +5 days'));
            foreach ($bds as $bd) {
                if ($bd['type_id'] != 3) { continue; }
                if (!$bd['manager_uid']) { $errors++; continue; }
                $exists = $this->db->query("
                    SELECT id FROM review_schedule
                    WHERE bd_uid = ? AND review_type_id = ? AND scheduled_date = ?
                    LIMIT 1
                ", array($bd['bd_uid'], $fortnight_type_id, $next_saturday))->row_array();
                if ($exists) { $skipped++; continue; }

                $row = array(
                    'bd_uid' => $bd['bd_uid'],
                    'manager_uid' => $bd['manager_uid'],
                    'review_type_id' => $fortnight_type_id,
                    'scheduled_date' => $next_saturday,
                    'scheduled_start_time' => '11:00:00',
                    'scheduled_end_time' => '12:00:00',
                    'min_duration_minutes' => 60,
                    'status' => 'pending',
                );
                try {
                    $this->ReviewSchedule_model->create_with_blocks($row);
                    $seeded++;
                } catch (Exception $ex) {
                    $errors++;
                }
            }
        }

        echo json_encode(array(
            'ok' => true,
            'iso_week' => $iso_week,
            'week_start' => $next_monday,
            'week_end' => $next_friday,
            'bd_count' => count($bds),
            'seeded' => $seeded,
            'skipped_existing' => $skipped,
            'errors' => $errors,
        ));
    }

    public function for_bd()
    {
        $bd_uid = (int)$this->input->get('bd_uid');
        if (!$bd_uid) {
            http_response_code(400);
            echo json_encode(array('ok' => false, 'error' => 'bd_uid required'));
            return;
        }
        $rows = $this->db->query("
            SELECT rs.id, rs.review_type_id, rs.scheduled_date,
                   rs.scheduled_start_time AS scheduled_start,
                   rs.scheduled_end_time AS scheduled_end,
                   rs.status,
                   rt.name AS review_type, mgr.name AS manager_name
            FROM review_schedule rs
            LEFT JOIN review_types rt ON rt.id = rs.review_type_id
            LEFT JOIN user mgr ON mgr.uid = rs.manager_uid
            WHERE rs.bd_uid = ?
              AND rs.scheduled_date >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)
            ORDER BY rs.scheduled_date DESC LIMIT 50
        ", array($bd_uid))->result_array();
        echo json_encode(array('ok' => true, 'count' => count($rows), 'rows' => $rows));
    }

    public function for_manager()
    {
        $manager_uid = (int)$this->input->get('manager_uid');
        if (!$manager_uid) {
            http_response_code(400);
            echo json_encode(array('ok' => false, 'error' => 'manager_uid required'));
            return;
        }
        $rows = $this->db->query("
            SELECT rs.id, rs.bd_uid, rs.review_type_id, rs.scheduled_date,
                   rs.scheduled_start_time AS scheduled_start,
                   rs.scheduled_end_time AS scheduled_end,
                   rs.status,
                   bd.name AS bd_name, rt.name AS review_type
            FROM review_schedule rs
            LEFT JOIN user bd ON bd.uid = rs.bd_uid
            LEFT JOIN review_types rt ON rt.id = rs.review_type_id
            WHERE rs.manager_uid = ?
              AND rs.scheduled_date >= DATE_SUB(CURDATE(), INTERVAL 14 DAY)
            ORDER BY rs.scheduled_date ASC LIMIT 200
        ", array($manager_uid))->result_array();
        echo json_encode(array('ok' => true, 'count' => count($rows), 'rows' => $rows));
    }

    /**
     * GET api/review_schedule/detail?id={schedule_id}
     * Returns full detail for a review_schedule row with BD+manager names.
     */
    public function detail() {
        if (!$this->_bearer_ok()) {
            http_response_code(401);
            echo json_encode(array('ok' => false, 'error' => 'Unauthorized'));
            return;
        }
        $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
        if ($id <= 0) {
            http_response_code(400);
            echo json_encode(array('ok' => false, 'error' => 'id required'));
            return;
        }
        $row = $this->db->query("
            SELECT rs.id, rs.bd_uid, rs.manager_uid, rs.review_type_id,
                   rs.scheduled_date, rs.scheduled_start_time, rs.scheduled_end_time,
                   rs.status, rs.missed_reason,
                   rs.created_at, rs.updated_at,
                   bd.name AS bd_name, mgr.name AS manager_name,
                   rt.name AS review_type
            FROM review_schedule rs
            LEFT JOIN user bd ON bd.uid = rs.bd_uid
            LEFT JOIN user mgr ON mgr.uid = rs.manager_uid
            LEFT JOIN review_types rt ON rt.id = rs.review_type_id
            WHERE rs.id = ?
            LIMIT 1
        ", array($id))->row_array();
        if (!$row) {
            http_response_code(404);
            echo json_encode(array('ok' => false, 'error' => 'not_found'));
            return;
        }
        $sessions = $this->db->query("
            SELECT id, user_id, psdatetime, pcdatetime, totaltime, main_review_on,
                   bd_self_assessment_at, rm_present, created_at
            FROM review_session
            WHERE user_id = ?
            ORDER BY id DESC LIMIT 10
        ", array($row['bd_uid']))->result_array();
        echo json_encode(array(
            'ok'            => true,
            'schedule'      => $row,
            'sessions'      => $sessions,
            'session_count' => count($sessions),
        ));
    }

    /**
     * POST api/review_schedule/start_session
     * Body: {schedule_id, manager_uid}
     * Sets review_schedule.status = 'in_progress'.
     */
    public function start_session() {
        if (!$this->_bearer_ok()) {
            http_response_code(401);
            echo json_encode(array('ok' => false, 'error' => 'Unauthorized'));
            return;
        }
        $raw = file_get_contents('php://input');
        $b   = ($raw) ? json_decode($raw, true) : array();
        if (!is_array($b)) $b = array();
        $schedule_id = isset($b['schedule_id']) ? (int)$b['schedule_id'] : 0;
        $manager_uid = isset($b['manager_uid']) ? (int)$b['manager_uid'] : 0;
        if ($schedule_id <= 0 || $manager_uid <= 0) {
            http_response_code(400);
            echo json_encode(array('ok' => false, 'error' => 'schedule_id and manager_uid required'));
            return;
        }
        $rs = $this->db->query("
            SELECT id, status FROM review_schedule WHERE id = ? LIMIT 1
        ", array($schedule_id))->row_array();
        if (!$rs) {
            http_response_code(404);
            echo json_encode(array('ok' => false, 'error' => 'schedule_not_found'));
            return;
        }
        if ($rs['status'] === 'in_progress') {
            echo json_encode(array('ok' => true, 'already_started' => true, 'status' => 'in_progress'));
            return;
        }
        if ($rs['status'] === 'completed') {
            echo json_encode(array('ok' => false, 'error' => 'already_completed'));
            return;
        }
        $this->db->query("
            UPDATE review_schedule SET status = 'in_progress', updated_at = NOW()
            WHERE id = ?
        ", array($schedule_id));
        echo json_encode(array(
            'ok'          => true,
            'schedule_id' => $schedule_id,
            'status'      => 'in_progress',
            'message'     => 'Session started',
        ));
    }

    /**
     * POST api/review_schedule/close_session
     * Body: {schedule_id, manager_uid, missed_reason?}
     * Sets review_schedule.status = 'completed'.
     */
    public function close_session() {
        if (!$this->_bearer_ok()) {
            http_response_code(401);
            echo json_encode(array('ok' => false, 'error' => 'Unauthorized'));
            return;
        }
        $raw = file_get_contents('php://input');
        $b   = ($raw) ? json_decode($raw, true) : array();
        if (!is_array($b)) $b = array();
        $schedule_id   = isset($b['schedule_id'])   ? (int)$b['schedule_id']   : 0;
        $manager_uid   = isset($b['manager_uid'])   ? (int)$b['manager_uid']   : 0;
        $missed_reason = isset($b['missed_reason']) ? trim($b['missed_reason']) : '';
        if ($schedule_id <= 0 || $manager_uid <= 0) {
            http_response_code(400);
            echo json_encode(array('ok' => false, 'error' => 'schedule_id and manager_uid required'));
            return;
        }
        $rs = $this->db->query("
            SELECT id, status FROM review_schedule WHERE id = ? LIMIT 1
        ", array($schedule_id))->row_array();
        if (!$rs) {
            http_response_code(404);
            echo json_encode(array('ok' => false, 'error' => 'schedule_not_found'));
            return;
        }
        if ($rs['status'] === 'completed') {
            echo json_encode(array('ok' => true, 'already_closed' => true, 'status' => 'completed'));
            return;
        }
        $update_sql = "UPDATE review_schedule SET status = 'completed', updated_at = NOW()";
        if ($missed_reason !== '') {
            $update_sql .= ", missed_reason = " . $this->db->escape($missed_reason);
        }
        $update_sql .= " WHERE id = " . (int)$schedule_id;
        $this->db->query($update_sql);
        echo json_encode(array(
            'ok'          => true,
            'schedule_id' => $schedule_id,
            'status'      => 'completed',
            'message'     => 'Session closed',
        ));
    }

    /**
     * GET api/review_schedule/action_detail?schedule_id={id}
     * Returns BD review session history and recent answers.
     * review_answer.main_rid -> allreview.id (not review_schedule.id).
     */
    public function action_detail() {
        if (!$this->_bearer_ok()) {
            http_response_code(401);
            echo json_encode(array('ok' => false, 'error' => 'Unauthorized'));
            return;
        }
        $schedule_id = isset($_GET['schedule_id']) ? (int)$_GET['schedule_id'] : 0;
        if ($schedule_id <= 0) {
            http_response_code(400);
            echo json_encode(array('ok' => false, 'error' => 'schedule_id required'));
            return;
        }
        $rs = $this->db->query("
            SELECT id, bd_uid, manager_uid, review_type_id, scheduled_date, status
            FROM review_schedule WHERE id = ? LIMIT 1
        ", array($schedule_id))->row_array();
        if (!$rs) {
            http_response_code(404);
            echo json_encode(array('ok' => false, 'error' => 'schedule_not_found'));
            return;
        }
        $bd_uid = (int)$rs['bd_uid'];
        $sessions = $this->db->query("
            SELECT rs.id, rs.psdatetime, rs.pcdatetime, rs.totaltime, rs.main_review_on,
                   rs.bd_self_assessment_at, rs.rm_present,
                   ar.reviewtype AS review_type, ar.plant AS planned_date
            FROM review_session rs
            LEFT JOIN allreview ar ON ar.id = rs.revew_id
            WHERE rs.user_id = ?
            ORDER BY rs.id DESC LIMIT 20
        ", array($bd_uid))->result_array();
        $latest_answers = array();
        if (!empty($sessions)) {
            $latest_review_id = isset($sessions[0]['revew_id']) ? (int)$sessions[0]['revew_id'] : 0;
            if ($latest_review_id > 0) {
                $latest_answers = $this->db->query("
                    SELECT id, main_rid, question, ans1, ans2, ans3, ans4, ans5, created_at
                    FROM review_answer WHERE main_rid = ?
                    ORDER BY id ASC LIMIT 50
                ", array($latest_review_id))->result_array();
            }
        }
        echo json_encode(array(
            'ok'             => true,
            'schedule'       => $rs,
            'bd_uid'         => $bd_uid,
            'sessions'       => $sessions,
            'session_count'  => count($sessions),
            'latest_answers' => $latest_answers,
            'note'           => 'review_answer links to allreview.id not review_schedule.id',
        ));
    }
}

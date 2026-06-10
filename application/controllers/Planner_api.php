<?php
defined('BASEPATH') OR exit('No direct script access allowed');
/**
 * Planner_api - powers Dashboard tiles:
 *   Task Assigned / Task Planner / Calendar / Task Completion
 *   + M017_4: day_shape_today (day-shape band by time of day)
 *
 * Source data:
 *   - tblcallevents = the real task ledger (714,696 rows). Each row = 1 task/event.
 *     - actiontype_id  : 1=call, 2=email, 3=meeting, 4=barge meeting, 5=followup, 10=research
 *     - autotask=1     : system-seeded auto-task
 *     - planner_status : task lifecycle (planned/completed/...)
 *     - assignedto_id  : who is supposed to do it (BD user_id)
 *     - user_id        : who actually owns the task
 *     - appointmentdatetime : scheduled time
 *     - date           : event/created time
 *     - actontaken (non-empty) OR status_id=2 => completed proxy
 *   - daily_planner = next-day plan submissions (header records)
 *   - init_call     = the lead the task is tied to
 *   - company_master= school for that lead
 */
class Planner_api extends CI_Controller {
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

    // GET /api/planner/day_shape/today
    // M017_4: Returns the current day-shape band based on server time.
    // Bands:
    //   manual     : 10:00 - 14:59  (manual calling window)
    //   auto       : 15:00 - 17:29  (auto-task window)
    //   plan_window: 17:30 - 18:29  (next-day planning window)
    //   closed     : 18:30+  or before 10:00
    public function day_shape_today() {
        try {
            $now_row = $this->db->query("SELECT NOW() AS now, HOUR(NOW()) AS h, MINUTE(NOW()) AS m")->row_array();
            $h = (int)$now_row['h'];
            $m = (int)$now_row['m'];
            $total_min = $h * 60 + $m;

            // Band thresholds in minutes-since-midnight
            // manual:     10:00 (600) - 14:59 (899)
            // auto:       15:00 (900) - 17:29 (1049)
            // plan_window:17:30 (1050) - 18:29 (1109)
            // closed:     18:30+ (1110) or < 600
            if ($total_min >= 600 && $total_min <= 899) {
                $band = 'manual';
                $band_label = 'Manual Calling Window (10:00 - 15:00)';
            } elseif ($total_min >= 900 && $total_min <= 1049) {
                $band = 'auto';
                $band_label = 'Auto-Task Window (15:00 - 17:30)';
            } elseif ($total_min >= 1050 && $total_min <= 1109) {
                $band = 'plan_window';
                $band_label = 'Planning Window (17:30 - 18:30)';
            } else {
                $band = 'closed';
                $band_label = 'Day Closed (before 10:00 or after 18:30)';
            }

            // Today's planner stats
            $today = date('Y-m-d');
            $planner_count = (int)$this->db->query(
                "SELECT COUNT(*) AS cnt FROM daily_planner WHERE record_date = ?", [$today]
            )->row()->cnt;

            $task_stats = $this->db->query(
                "SELECT COUNT(*) AS total,
                        SUM(CASE WHEN actontaken IS NOT NULL AND actontaken!='' THEN 1 ELSE 0 END) AS completed
                 FROM tblcallevents
                 WHERE DATE(appointmentdatetime) = ?",
                [$today]
            )->row_array();

            $this->_out([
                'ok'           => true,
                'migration'    => 'M017_4',
                'server_time'  => $now_row['now'],
                'band'         => $band,
                'band_label'   => $band_label,
                'today'        => $today,
                'planner_submissions_today' => $planner_count,
                'tasks_today'  => [
                    'total'     => (int)$task_stats['total'],
                    'completed' => (int)$task_stats['completed'],
                ],
            ]);
        } catch (Exception $e) {
            $this->_out(['ok' => true, 'note' => 'error', 'detail' => $e->getMessage()]);
        }
    }

    // GET /api/planner/yesterday_plans?date=YYYY-MM-DD
    public function yesterday_plans() {
        try {
            $date = $this->input->get('date') ?: date('Y-m-d', strtotime('yesterday'));
            // Daily planner headers
            $headers = $this->db->query("
              SELECT id, userID AS bd_uid, record_date, planner_approvel_status,
                     planned_day_start, actual_day_start, planner_created_at,
                     dayCloseApproveStatus, dayCloseRemark
              FROM daily_planner WHERE record_date = ?
              ORDER BY id DESC", [$date])->result_array();
            // Tasks actually scheduled for that date (from tblcallevents)
            $tasks = $this->db->query("
              SELECT t.id AS task_id, t.cid_id AS lead_id, t.user_id AS bd_uid,
                     u.name AS bd_name, cm.compname AS school,
                     t.actiontype_id, t.purpose_id, t.appointmentdatetime,
                     t.autotask, t.status_id,
                     (CASE WHEN t.actontaken IS NOT NULL AND t.actontaken!='' THEN 1 ELSE 0 END) AS completed
              FROM tblcallevents t
              LEFT JOIN user_details u ON u.user_id = t.user_id
              LEFT JOIN init_call ic ON ic.id = t.cid_id
              LEFT JOIN company_master cm ON cm.id = ic.cmpid_id
              WHERE DATE(t.appointmentdatetime) = ?
              ORDER BY t.appointmentdatetime LIMIT 500", [$date])->result_array();
            $this->_out(['ok'=>true,'date'=>$date,
                         'headers'=>$headers,'header_count'=>count($headers),
                         'rows'=>$tasks,'task_count'=>count($tasks)]);
        } catch (Exception $e) {
            $this->_out(['ok'=>true,'rows'=>[],'note'=>'error','detail'=>$e->getMessage()]);
        }
    }

    // GET /api/planner/areas_for_date?date=YYYY-MM-DD
    public function areas_for_date() {
        try {
            $date = $this->input->get('date') ?: date('Y-m-d', strtotime('tomorrow'));
            $rows = $this->db->query("
              SELECT t.user_id AS bd_uid, u.name AS bd_name,
                     cm.address AS area,
                     COUNT(*) AS task_count
              FROM tblcallevents t
              LEFT JOIN user_details u ON u.user_id = t.user_id
              LEFT JOIN init_call ic ON ic.id = t.cid_id
              LEFT JOIN company_master cm ON cm.id = ic.cmpid_id
              WHERE DATE(t.appointmentdatetime) = ?
                AND u.type_id = 3
              GROUP BY t.user_id, u.name, cm.address
              HAVING task_count > 0
              ORDER BY task_count DESC LIMIT 100", [$date])->result_array();
            $this->_out(['ok'=>true,'date'=>$date,'rows'=>$rows,'count'=>count($rows)]);
        } catch (Exception $e) {
            $this->_out(['ok'=>true,'rows'=>[],'note'=>'error','detail'=>$e->getMessage()]);
        }
    }

    // GET /api/activity/events_for_day?date=YYYY-MM-DD
    public function events_for_day() {
        try {
            $date = $this->input->get('date') ?: date('Y-m-d');
            $rows = $this->db->query("
              SELECT t.id, t.cid_id AS lead_id, t.user_id AS bd_uid,
                     u.name AS bd_name, cm.compname AS school,
                     t.actiontype_id, t.purpose_id,
                     t.appointmentdatetime, t.date AS event_date,
                     t.autotask, t.status_id,
                     (CASE WHEN t.actontaken IS NOT NULL AND t.actontaken!='' THEN 1 ELSE 0 END) AS completed,
                     (CASE WHEN t.mom IS NOT NULL AND t.mom!='' THEN 1 ELSE 0 END) AS has_mom,
                     (CASE WHEN t.attech IS NOT NULL AND t.attech!='' THEN 1 ELSE 0 END) AS has_photo,
                     (CASE WHEN t.live_loaction IS NOT NULL AND t.live_loaction!='' THEN 1 ELSE 0 END) AS has_gps
              FROM tblcallevents t
              LEFT JOIN user_details u ON u.user_id = t.user_id
              LEFT JOIN init_call ic ON ic.id = t.cid_id
              LEFT JOIN company_master cm ON cm.id = ic.cmpid_id
              WHERE DATE(t.date) = ?
              ORDER BY t.date DESC LIMIT 500", [$date])->result_array();
            $this->_out(['ok'=>true,'date'=>$date,'rows'=>$rows,'count'=>count($rows)]);
        } catch (Exception $e) {
            $this->_out(['ok'=>true,'rows'=>[],'note'=>'error','detail'=>$e->getMessage()]);
        }
    }

    // GET /api/planner/tasks_assigned?bd_uid=&from=&to=
    // Dashboard tile: Task Assigned per BD
    public function tasks_assigned() {
        try {
            $bd_uid = $this->input->get('bd_uid');
            $from = $this->input->get('from') ?: date('Y-m-d');
            $to   = $this->input->get('to')   ?: date('Y-m-d');
            $where = "DATE(t.appointmentdatetime) BETWEEN ? AND ?";
            $params = [$from, $to];
            if ($bd_uid) { $where .= " AND t.user_id = ?"; $params[] = $bd_uid; }
            $rows = $this->db->query("
              SELECT t.user_id AS bd_uid, u.name AS bd_name,
                     COUNT(*) AS assigned,
                     SUM(CASE WHEN t.actontaken IS NOT NULL AND t.actontaken!='' THEN 1 ELSE 0 END) AS completed,
                     SUM(CASE WHEN t.autotask = 1 THEN 1 ELSE 0 END) AS auto_tasks,
                     SUM(CASE WHEN t.actiontype_id IN (3,4) THEN 1 ELSE 0 END) AS meetings,
                     SUM(CASE WHEN t.actiontype_id = 1 THEN 1 ELSE 0 END) AS calls,
                     SUM(CASE WHEN t.actiontype_id = 10 THEN 1 ELSE 0 END) AS research
              FROM tblcallevents t
              LEFT JOIN user_details u ON u.user_id = t.user_id
              WHERE $where
              GROUP BY t.user_id, u.name
              ORDER BY assigned DESC LIMIT 100", $params)->result_array();
            foreach ($rows as &$r) {
                $r['completion_pct'] = ($r['assigned'] > 0)
                    ? round(100*$r['completed']/$r['assigned'], 1) : 0;
            }
            $this->_out(['ok'=>true,'from'=>$from,'to'=>$to,'rows'=>$rows,'count'=>count($rows)]);
        } catch (Exception $e) {
            $this->_out(['ok'=>true,'rows'=>[],'note'=>'error','detail'=>$e->getMessage()]);
        }
    }

    // GET /api/planner/calendar?bd_uid=&from=&to=
    // Dashboard tile: Calendar (date -> task list)
    public function calendar() {
        try {
            $bd_uid = $this->input->get('bd_uid');
            $from = $this->input->get('from') ?: date('Y-m-d');
            $to   = $this->input->get('to')   ?: date('Y-m-d', strtotime('+7 days'));
            $where = "DATE(t.appointmentdatetime) BETWEEN ? AND ?";
            $params = [$from, $to];
            if ($bd_uid) { $where .= " AND t.user_id = ?"; $params[] = $bd_uid; }
            $rows = $this->db->query("
              SELECT DATE(t.appointmentdatetime) AS task_date,
                     t.id, t.user_id AS bd_uid, u.name AS bd_name,
                     t.cid_id AS lead_id, cm.compname AS school,
                     t.actiontype_id, t.purpose_id,
                     TIME(t.appointmentdatetime) AS task_time,
                     t.autotask,
                     (CASE WHEN t.actontaken IS NOT NULL AND t.actontaken!='' THEN 1 ELSE 0 END) AS completed
              FROM tblcallevents t
              LEFT JOIN user_details u ON u.user_id = t.user_id
              LEFT JOIN init_call ic ON ic.id = t.cid_id
              LEFT JOIN company_master cm ON cm.id = ic.cmpid_id
              WHERE $where
              ORDER BY t.appointmentdatetime LIMIT 1000", $params)->result_array();
            $this->_out(['ok'=>true,'from'=>$from,'to'=>$to,'rows'=>$rows,'count'=>count($rows)]);
        } catch (Exception $e) {
            $this->_out(['ok'=>true,'rows'=>[],'note'=>'error','detail'=>$e->getMessage()]);
        }
    }

    // GET /api/planner/task_completion?bd_uid=&days=7
    // Dashboard tile: Task Completion logic (completion rate per BD over N days)
    public function task_completion() {
        try {
            $bd_uid = $this->input->get('bd_uid');
            $days = max(1, (int)($this->input->get('days') ?: 7));
            $where = "t.appointmentdatetime >= NOW() - INTERVAL $days DAY
                      AND t.appointmentdatetime <= NOW()";
            $params = [];
            if ($bd_uid) { $where .= " AND t.user_id = ?"; $params[] = $bd_uid; }
            $rows = $this->db->query("
              SELECT t.user_id AS bd_uid, u.name AS bd_name, u.user_cluster_zone AS cluster,
                     COUNT(*) AS planned,
                     SUM(CASE WHEN t.actontaken IS NOT NULL AND t.actontaken!='' THEN 1 ELSE 0 END) AS completed,
                     SUM(CASE WHEN (t.actontaken IS NULL OR t.actontaken='')
                              AND t.appointmentdatetime < NOW() THEN 1 ELSE 0 END) AS missed,
                     SUM(CASE WHEN t.mom IS NOT NULL AND t.mom!='' THEN 1 ELSE 0 END) AS mom_done,
                     SUM(CASE WHEN t.attech IS NOT NULL AND t.attech!='' THEN 1 ELSE 0 END) AS photo_done,
                     SUM(CASE WHEN t.live_loaction IS NOT NULL AND t.live_loaction!='' THEN 1 ELSE 0 END) AS gps_done
              FROM tblcallevents t
              LEFT JOIN user_details u ON u.user_id = t.user_id
              WHERE $where
                AND u.type_id = 3
              GROUP BY t.user_id, u.name, u.user_cluster_zone
              ORDER BY planned DESC LIMIT 100", $params)->result_array();
            foreach ($rows as &$r) {
                $p = (int)$r['planned'];
                $r['completion_pct'] = $p>0 ? round(100*$r['completed']/$p, 1) : 0;
                $r['mom_pct']        = $p>0 ? round(100*$r['mom_done']/$p, 1)  : 0;
                $r['gps_pct']        = $p>0 ? round(100*$r['gps_done']/$p, 1)  : 0;
                $r['photo_pct']      = $p>0 ? round(100*$r['photo_done']/$p, 1): 0;
                // Grade: A+ 90+, A 75+, B 60+, C 40+, D <40
                $g = $r['completion_pct'];
                $r['grade'] = $g >= 90 ? 'A+' : ($g >= 75 ? 'A' : ($g >= 60 ? 'B' : ($g >= 40 ? 'C' : 'D')));
            }
            $this->_out(['ok'=>true,'days'=>$days,'rows'=>$rows,'count'=>count($rows)]);
        } catch (Exception $e) {
            $this->_out(['ok'=>true,'rows'=>[],'note'=>'error','detail'=>$e->getMessage()]);
        }
    }

    // GET /api/planner/today_detail?uid=<uid>
    // Returns the daily_planner header for today plus its associated task rows from tblcallevents.
    // If no header exists for today, returns {ok:true, header:null, tasks:[]}.
    // Auth: same bearer pattern as /api/planner/* (no bearer enforced in Planner_api - open within LAN).
    // Added: mobile pilot endpoints build (27 May 2026).
    public function today_detail() {
        try {
            $uid = (int)$this->input->get('uid');
            if ($uid <= 0) {
                $this->_out(array('ok' => false, 'error' => 'uid param required'));
            }

            // Bearer check using same digest token pattern
            $hdr = isset($_SERVER['HTTP_AUTHORIZATION']) ? $_SERVER['HTTP_AUTHORIZATION'] : '';
            if (!$hdr && function_exists('apache_request_headers')) {
                $h = apache_request_headers();
                if (isset($h['Authorization'])) $hdr = $h['Authorization'];
            }
            $_known = '4eBaiAT7r4zu6OK3b8evjLNia1D7RGgb0qRTuLJfUSo';
            if ($hdr && stripos($hdr, 'Bearer ') === 0) {
                $tok = trim(substr($hdr, 7));
                $env = getenv('STEM_DIGEST_TOKEN');
                $valid = ($env && hash_equals($env, $tok)) || hash_equals($_known, $tok);
                if (!$valid) {
                    // Try per-user daily JWT
                    $secret = $env ?: $_known;
                    $uid_try = $uid > 0 ? $uid : 0;
                    if ($uid_try > 0) {
                        foreach (array(date('Y-m-d'), date('Y-m-d', strtotime('-1 day'))) as $d) {
                            if (hash_equals(sha1($secret . '|' . $uid_try . '|' . $d), $tok)) {
                                $valid = true;
                                break;
                            }
                        }
                    }
                }
                if (!$valid) {
                    http_response_code(401);
                    $this->_out(array('ok' => false, 'error' => 'Unauthorized'));
                }
            }

            $today = date('Y-m-d');

            // Fetch daily_planner header for this uid and today
            $header_row = $this->db->query("
                SELECT id, userID AS bd_uid, record_date,
                       planner_approvel_status, planned_day_start, actual_day_start,
                       planner_created_at, dayCloseApproveStatus, dayCloseRemark
                FROM daily_planner
                WHERE userID = ? AND record_date = ?
                LIMIT 1
            ", array($uid, $today))->row_array();

            if (!$header_row) {
                $this->_out(array('ok' => true, 'header' => null, 'tasks' => array()));
            }

            // Fetch tasks for this uid today
            $task_rows = $this->db->query("
                SELECT
                    t.id,
                    t.actiontype_id,
                    t.cid_id,
                    t.purpose_id,
                    t.autotask,
                    t.status_id,
                    t.actontaken,
                    t.appointmentdatetime,
                    t.startm,
                    COALESCE(cm.compname, p.name) AS title
                FROM tblcallevents t
                LEFT JOIN init_call ic ON ic.id = t.cid_id
                LEFT JOIN company_master cm ON cm.id = ic.cmpid_id
                LEFT JOIN purpose p ON p.id = t.purpose_id
                WHERE t.user_id = ?
                  AND (
                    (t.event_date IS NOT NULL AND t.event_date = CURDATE())
                    OR
                    (t.event_date IS NULL AND DATE(t.appointmentdatetime) = CURDATE())
                  )
                ORDER BY t.appointmentdatetime ASC, t.id ASC
                LIMIT 500
            ", array($uid))->result_array();

            $dur_map = array(
                1 => 5, 5 => 5, 8 => 5, 9 => 5, 10 => 5, 15 => 5,
                2 => 10, 6 => 10,
                3 => 30, 4 => 30, 12 => 30,
                7 => 15,
                11 => 2, 13 => 2, 14 => 2,
            );

            $tasks = array();
            foreach ($task_rows as $r) {
                $aid = (int)$r['actiontype_id'];
                $appt = $r['appointmentdatetime'];
                if (!empty($r['startm']) && $r['startm'] !== '00:00:00') {
                    $time_str = substr($r['startm'], 0, 5);
                } elseif ($appt) {
                    $time_str = date('H:i', strtotime($appt));
                } else {
                    $time_str = '00:00';
                }
                $done = (!empty($r['actontaken']) && $r['actontaken'] !== 'no')
                     || (int)$r['status_id'] === 2;
                $tasks[] = array(
                    'id'           => (int)$r['id'],
                    'time'         => $time_str,
                    'dur'          => isset($dur_map[$aid]) ? $dur_map[$aid] : 5,
                    'type'         => $aid,
                    'title'        => $r['title'] ?: '',
                    'status'       => $done ? 'done' : 'pending',
                    'actionTypeId' => $aid,
                    'leadId'       => (int)$r['cid_id'],
                    'auto'         => (int)$r['autotask'],
                );
            }

            $this->_out(array(
                'ok'     => true,
                'header' => $header_row,
                'tasks'  => $tasks,
            ));
        } catch (Exception $e) {
            $this->_out(array('ok' => true, 'header' => null, 'tasks' => array(),
                              'note' => 'error', 'detail' => $e->getMessage()));
        }
    }



    // ---- per-user JWT validator (added AgentC 28 May 2026) ----
    private $_authed_uid_pa = 0;
    private function _jwt_valid_pa($token) {
        if (empty($token)) return false;
        $secret = getenv('STEM_DIGEST_TOKEN') ?: '4eBaiAT7r4zu6OK3b8evjLNia1D7RGgb0qRTuLJfUSo';
        $days = array(date('Y-m-d'), date('Y-m-d', strtotime('-1 day')), date('Y-m-d', strtotime('+1 day')));
        $candidates = array();
        foreach (array('uid','cm_uid','rm_uid','bd_uid','acm_uid','user_id') as $k) {
            if (isset($_GET[$k]) && (int)$_GET[$k] > 0) $candidates[(int)$_GET[$k]] = 1;
            if (isset($_POST[$k]) && (int)$_POST[$k] > 0) $candidates[(int)$_POST[$k]] = 1;
        }
        foreach (array_keys($candidates) as $cuid) {
            foreach ($days as $d) {
                if (hash_equals(sha1($secret.'|'.$cuid.'|'.$d), $token)) return (int)$cuid;
            }
        }
        static $all_uids = null;
        if ($all_uids === null) {
            $rows = $this->db->select('uid')->from('user')->where('active', 1)->get()->result();
            $all_uids = array();
            foreach ($rows as $r) $all_uids[] = (int)$r->uid;
        }
        foreach ($all_uids as $cuid) {
            foreach ($days as $d) {
                if (hash_equals(sha1($secret.'|'.$cuid.'|'.$d), $token)) return (int)$cuid;
            }
        }
        return false;
    }
    private function _bearer_ok_pa() {
        $hdr = isset($_SERVER['HTTP_AUTHORIZATION']) ? $_SERVER['HTTP_AUTHORIZATION'] : '';
        if (!$hdr && function_exists('apache_request_headers')) {
            $h = apache_request_headers(); if (isset($h['Authorization'])) $hdr = $h['Authorization'];
        }
        if (!$hdr || stripos($hdr, 'Bearer ') !== 0) return false;
        $token = trim(substr($hdr, 7));
        $known = getenv('STEM_DIGEST_TOKEN') ?: '4eBaiAT7r4zu6OK3b8evjLNia1D7RGgb0qRTuLJfUSo';
        if (hash_equals($known, $token)) return true;
        $uid = $this->_jwt_valid_pa($token);
        if ($uid) { $this->_authed_uid_pa = $uid; return true; }
        return false;
    }

    // GET /api/planner_analytics?uid=X&days=30
    // Returns planner submission analytics: streak, on-time rate, daily plan quality.
    // Scoped to uid via JWT.
    public function planner_analytics() {
        if (!$this->_bearer_ok_pa()) { http_response_code(401); $this->_out(array('ok'=>false,'error'=>'Unauthorized')); }
        $uid  = (int)(isset($_GET['uid']) ? $_GET['uid'] : ($this->_authed_uid_pa ?: 0));
        if ($uid <= 0) { http_response_code(400); $this->_out(array('ok'=>false,'error'=>'uid required')); }
        $days = isset($_GET['days']) ? max(1, min(90, (int)$_GET['days'])) : 30;
        $from = date('Y-m-d', strtotime("-{$days} days"));
        $to   = date('Y-m-d');
        try {
            // Planner submissions from daily_planner
            $submissions = $this->db->query("
                SELECT DATE(record_date) AS plan_date,
                       SUM(taskcnt) AS task_count,
                       created_at
                FROM daily_planner
                WHERE user_id = ? AND DATE(record_date) BETWEEN ? AND ?
                ORDER BY plan_date DESC
                LIMIT 30
            ", array($uid, $from, $to))->result_array();

            // Actual completions from tblcallevents
            $completions = $this->db->query("
                SELECT DATE(date) AS exec_date,
                       COUNT(*) AS done_count,
                       SUM(CASE WHEN actiontype_id IN (3,4) THEN 1 ELSE 0 END) AS meeting_count
                FROM tblcallevents
                WHERE user_id = ? AND DATE(date) BETWEEN ? AND ?
                  AND (actontaken='yes' OR status_id=2)
                GROUP BY DATE(date)
                ORDER BY exec_date DESC
                LIMIT 30
            ", array($uid, $from, $to))->result_array();

            // Build completion map
            $done_map = array();
            foreach ($completions as $c) {
                $done_map[$c['exec_date']] = array('done'=>(int)$c['done_count'],'meetings'=>(int)$c['meeting_count']);
            }

            // Streak: consecutive days with submissions ending today
            $streak = 0;
            $today = date('Y-m-d');
            $sub_dates = array_column($submissions, 'plan_date');
            for ($i = 0; $i < 30; $i++) {
                $d = date('Y-m-d', strtotime("-{$i} days", strtotime($today)));
                if (in_array($d, $sub_dates)) { $streak++; } else { break; }
            }

            // Days submitted vs working days
            $total_submitted = count($submissions);
            $total_tasks_planned = 0;
            foreach ($submissions as $s) { $total_tasks_planned += (int)$s['task_count']; }

            // Merge plan vs done per day
            $merged = array();
            foreach ($submissions as $s) {
                $d = $s['plan_date'];
                $planned = (int)$s['task_count'];
                $done    = isset($done_map[$d]) ? $done_map[$d]['done'] : 0;
                $merged[] = array(
                    'date'    => $d,
                    'planned' => $planned,
                    'done'    => $done,
                    'pct_done'=> $planned > 0 ? round(($done/$planned)*100) : 0,
                    'meetings'=> isset($done_map[$d]) ? $done_map[$d]['meetings'] : 0,
                );
            }

            $this->_out(array(
                'ok'                  => true,
                'uid'                 => $uid,
                'from'                => $from,
                'to'                  => $to,
                'days_requested'      => $days,
                'current_streak'      => $streak,
                'total_days_submitted'=> $total_submitted,
                'total_tasks_planned' => $total_tasks_planned,
                'daily'               => $merged,
                'count'               => count($merged),
            ));
        } catch (Exception $e) {
            $this->_out(array('ok'=>true,'uid'=>$uid,'current_streak'=>0,'total_days_submitted'=>0,'daily'=>array(),'note'=>'no_data','detail'=>$e->getMessage()));
        }
    }



    // === F11: create_mom_check -- helpers-only deploy 30may ===


    // ----------------------------------------------------------
    // F11: POST /api/planner/v2/create_mom_check
    //
    // Purpose: Create a MOM-check task via Menu_model::CreateTaskForMOMCheck.
    //          Called from NextDayPlannerV2 when a CM/BM schedules a
    //          MOM review for the following day.
    //
    // Request:
    //   Method : POST
    //   Auth   : Bearer token (same digest token as other Planner_api endpoints)
    //   Body   : application/x-www-form-urlencoded OR application/json
    //     cid_id    (required) -- init_call / mom_data ID to review.
    //                            Single integer. The model loops internally;
    //                            we wrap it in an array here.
    //                            TODO-CONFIRM: if caller sends a JSON array
    //                            of mom_data IDs, replace scalar cast with
    //                            json_decode($raw_cid_id, true).
    //     uid       (required) -- BD user_id who will execute the check task.
    //     plan_date (required) -- ISO date string, e.g. '2026-06-02'.
    //                            Passed as $bmdate to the model.
    //
    // Response (JSON):
    //   Success: {"success": true, "tid": <new_task_id>}
    //   Error:   {"success": false, "error": "<message>"}
    //
    // Notes:
    //   - The model does not return a task ID; it uses $this->db->insert()
    //     internally. We capture insert_id() immediately after the call.
    //     TODO-CONFIRM: if CreateTaskForMOMCheck loops over multiple IDs
    //     and inserts multiple rows, the tid returned here is the LAST
    //     inserted row id. Adjust if the caller needs all row IDs.
    // ----------------------------------------------------------
    public function create_mom_check()
    {
        if (!$this->_bearer_ok_pa()) {
            http_response_code(401);
            $this->_out(array('ok' => false, 'error' => 'Unauthorized'));
            return;
        }

        $raw = file_get_contents('php://input');
        $json_body = array();
        if (!empty($raw)) {
            $decoded = json_decode($raw, true);
            if (is_array($decoded)) { $json_body = $decoded; }
        }

        $cid_id    = isset($_POST['cid_id'])    ? trim($_POST['cid_id'])    : (isset($json_body['cid_id'])    ? trim($json_body['cid_id'])    : '');
        $uid       = isset($_POST['uid'])       ? trim($_POST['uid'])       : (isset($json_body['uid'])       ? trim($json_body['uid'])       : '');
        $plan_date = isset($_POST['plan_date']) ? trim($_POST['plan_date']) : (isset($json_body['plan_date']) ? trim($json_body['plan_date']) : '');
        $mom_ids_in = isset($_POST['mom_ids']) ? $_POST['mom_ids'] : (isset($json_body['mom_ids']) ? $json_body['mom_ids'] : '');

        if ($plan_date === '' || ($cid_id === '' && empty($mom_ids_in))) {
            http_response_code(422);
            $this->_out(array('success' => false, 'error' => 'plan_date and (cid_id or mom_ids) are required'));
            return;
        }

        // Resolve mom_ids: explicit list OR derived from cid_id.
        $mom_ids = array();
        if (!empty($mom_ids_in)) {
            $mom_ids = is_array($mom_ids_in) ? $mom_ids_in : array_filter(array_map('intval', explode(',', $mom_ids_in)));
        } else {
            $this->load->database();
            $q = $this->db->query("SELECT id FROM mom_data WHERE init_cmpid = ? AND approved_status IS NULL AND NOT EXISTS (SELECT 1 FROM tblcallevents WHERE mom_data.id = tblcallevents.reviewtype)", array($cid_id));
            foreach ($q->result() as $r) { $mom_ids[] = (int)$r->id; }
        }

        if (empty($mom_ids)) {
            $this->_out(array('success' => true, 'tid' => null, 'note' => 'No pending mom_data rows for this cid_id; nothing to schedule.'));
            return;
        }

        $this->load->model('Menu_model');
        try {
            $tid = $this->Menu_model->CreateTaskForMOMCheck($uid, $plan_date, $mom_ids, 'Auto Assign');
            $this->_out(array('success' => true, 'tid' => $tid, 'mom_count' => count($mom_ids)));
        } catch (Exception $e) {
            http_response_code(500);
            $this->_out(array('success' => false, 'error' => $e->getMessage()));
        }
    }


}
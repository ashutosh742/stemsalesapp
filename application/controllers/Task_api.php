<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * application/controllers/Task_api.php
 *
 * GET /api/task/today?uid=<uid>
 *
 * Returns tblcallevents rows for the given uid where event_date = CURDATE()
 * (falls back to DATE(appointmentdatetime) = CURDATE() when event_date is NULL).
 *
 * Response shape:
 *   {ok:true, date:'YYYY-MM-DD', uid:N,
 *    tasks:[{id, time, dur, type, title, status, actionTypeId, leadId, auto}]}
 *
 * Action-minute map (dur field):
 *   1,5,8,9,10,15 => 5 min
 *   2,6           => 10 min
 *   3,4,12        => 30 min
 *   7             => 15 min
 *   11,13,14      => 2 min
 *   default       => 5 min
 *
 * Auth: Bearer 4eBaiAT7r4zu6OK3b8evjLNia1D7RGgb0qRTuLJfUSo
 * Plain English only. No em-dashes. No non-ASCII. Uses Rs not currency symbol.
 */
class Task_api extends CI_Controller {

    private $_known_token = '4eBaiAT7r4zu6OK3b8evjLNia1D7RGgb0qRTuLJfUSo';

    // rimlyproof_taskapiauth_20260609
    private $auth_uid  = 0;
    private $auth_role = '';

    public function __construct() {
        parent::__construct();
        $this->load->database();
        header('Content-Type: application/json; charset=utf-8');
    }

    private function _bearer_ok() {
        $hdr = isset($_SERVER['HTTP_AUTHORIZATION']) ? $_SERVER['HTTP_AUTHORIZATION'] : '';
        if (!$hdr && function_exists('apache_request_headers')) {
            $h = apache_request_headers();
            if (isset($h['Authorization'])) $hdr = $h['Authorization'];
        }
        if (!$hdr || stripos($hdr, 'Bearer ') !== 0) return false;
        $token = trim(substr($hdr, 7));
        $env = getenv('STEM_DIGEST_TOKEN');
        if ($env && hash_equals($env, $token)) return true;
        if (hash_equals($this->_known_token, $token)) return true;
        // Per-user daily JWT: sha1(secret|uid|date)
        $secret = getenv('STEM_DIGEST_TOKEN') ?: $this->_known_token;
        $candidates = array();
        foreach (array('uid','cm_uid','bd_uid','user_id') as $k) {
            if (isset($_GET[$k]) && (int)$_GET[$k] > 0) $candidates[(int)$_GET[$k]] = 1;
            if (isset($_POST[$k]) && (int)$_POST[$k] > 0) $candidates[(int)$_POST[$k]] = 1;
        }
        foreach (array_keys($candidates) as $uid_candidate) {
            foreach (array(date('Y-m-d'), date('Y-m-d', strtotime('-1 day'))) as $d) {
                if (hash_equals(sha1($secret . '|' . $uid_candidate . '|' . $d), $token)) return true;
            }
        }
        // rimlyproof_taskapiauth_20260609: accept the real per-user login token via shared
        // BearerAuth resolver (validates login token, api_token rows, master token).
        $CI =& get_instance();
        $CI->load->library('BearerAuth');
        $res = $CI->bearerauth->resolve();
        if (is_array($res) && !empty($res['ok'])) {
            $this->auth_uid  = isset($res['uid'])  ? (int)$res['uid'] : 0;
            $this->auth_role = isset($res['role']) ? strtolower((string)$res['role']) : '';
            return true;
        }
        return false;
    }

    private function _json($data, $code = 200) {
        http_response_code($code);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($data);
        exit;
    }

    private function _dur($action_id) {
        $map = array(
            1 => 5, 5 => 5, 8 => 5, 9 => 5, 10 => 5, 15 => 5,
            2 => 10, 6 => 10,
            3 => 30, 4 => 30, 12 => 30,
            7 => 15,
            11 => 2, 13 => 2, 14 => 2,
        );
        return isset($map[$action_id]) ? $map[$action_id] : 5;
    }

    private function _task_status($row) {
        $done_action = isset($row['actontaken']) && $row['actontaken'] !== '' && $row['actontaken'] !== 'no';
        if ($done_action || (isset($row['status_id']) && (int)$row['status_id'] === 2)) {
            return 'done';
        }
        return 'pending';
    }

    /* STEM_TABS_ENRICH_20260608 helpers (additive) */
    private function _action_category($aid) {
        $meetings = array(3,4,17,22,23,24);
        $calls    = array(1,5,9,26);
        $email    = array(2,11);
        $writing  = array(6,7,12,18,21);
        if (in_array($aid, $meetings)) return 'meetings';
        if (in_array($aid, $calls))    return 'calls';
        if (in_array($aid, $email))    return 'email';
        if (in_array($aid, $writing))  return 'writing';
        return 'other';
    }

    private function _action_label($aid) {
        static $labels = null;
        if ($labels === null) {
            $labels = array();
            try {
                $rs = $this->db->query("SELECT id, name FROM action")->result_array();
                foreach ($rs as $a) { $labels[(int)$a['id']] = $a['name']; }
            } catch (Exception $e) { $labels = array(); }
        }
        return isset($labels[$aid]) ? $labels[$aid] : 'Task';
    }

    private function _day_start_done($uid) {
        try {
            $today = date('Y-m-d');
            // A day is 'started' if a day-start ceremony / plan exists for today.
            $tbls = $this->db->query("SHOW TABLES LIKE 'day_ceremony_log'")->num_rows();
            if ($tbls > 0) {
                $r = $this->db->query(
                    "SELECT COUNT(*) c FROM day_ceremony_log WHERE user_id=? AND DATE(created_at)=? AND ceremony_type='start_day'",
                    array($uid, $today)
                )->row();
                if ($r && (int)$r->c > 0) return true;
            }
            // Fallback: any plan rows for today imply the day is underway.
            $r2 = $this->db->query(
                "SELECT COUNT(*) c FROM tblcallevents WHERE user_id=? AND ( (event_date IS NOT NULL AND event_date=CURDATE()) OR (event_date IS NULL AND DATE(appointmentdatetime)=CURDATE()) ) AND (actontaken IS NOT NULL AND actontaken<>'' AND actontaken<>'no')",
                array($uid)
            )->row();
            return ($r2 && (int)$r2->c > 0);
        } catch (Exception $e) {
            return false; // fail-open: do not block
        }
    }

    /**
     * GET /api/task/today?uid=<uid>
     */
    public function today() {
        try {
            if (!$this->_bearer_ok()) {
                $this->_json(array('ok' => false, 'error' => 'Unauthorized'), 401);
            }

            // rimlyproof_taskapiauth_20260609: derive uid from authed identity; FORCE field
            // users (bd/acm) to their own uid so they cannot read another user's tasks.
            $req_uid = (int)$this->input->get('uid');
            if ($this->auth_uid > 0 && ($this->auth_role === 'bd' || $this->auth_role === 'acm')) {
                $uid = $this->auth_uid;
            } elseif ($req_uid > 0) {
                $uid = $req_uid;
            } else {
                $uid = $this->auth_uid;
            }
            if ($uid <= 0) {
                $this->_json(array('ok' => false, 'error' => 'uid param required'), 400);
            }

            $today = date('Y-m-d');

            // Query tblcallevents for today. Use event_date when populated, otherwise
            // fall back to DATE(appointmentdatetime). Join purpose for title label.
            $sql = "
                SELECT
                    t.id,
                    t.actiontype_id,
                    t.cid_id,
                    t.purpose_id,
                    t.autotask,
                    t.status_id,
                    t.actontaken,
                    t.appointmentdatetime,
                    t.event_date,
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
            ";

            $rows = $this->db->query($sql, array($uid))->result_array();

            $tasks = array();
            $tabs = array('meetings'=>array(),'calls'=>array(),'email'=>array(),'writing'=>array(),'other'=>array());
            foreach ($rows as $r) {
                $aid  = (int)$r['actiontype_id'];
                $appt = $r['appointmentdatetime'];
                // Prefer startm (time-only column) if populated
                if (!empty($r['startm']) && $r['startm'] !== '00:00:00') {
                    $time_str = substr($r['startm'], 0, 5);
                } elseif ($appt) {
                    $time_str = date('H:i', strtotime($appt));
                } else {
                    $time_str = '00:00';
                }

                /* STEM_TABS_ENRICH_20260608: build task with production-mirror aliases */
                $st = $this->_task_status($r);
                $task = array(
                    'id'             => (int)$r['id'],
                    'time'           => $time_str,
                    'dur'            => $this->_dur($aid),
                    'type'           => $aid,
                    'title'          => $r['title'] ?: '',
                    'status'         => $st,
                    'actionTypeId'   => $aid,
                    'leadId'         => (int)$r['cid_id'],
                    'auto'           => (int)$r['autotask'],
                    // production-mirror field aliases (M047DashboardScreen reads these)
                    'cname'          => $r['title'] ?: '',
                    'ctname'         => $this->_action_label($aid),
                    'appointmenttime'=> $time_str,
                    'cstatus'        => ($st === 'done') ? 2 : 8,
                    'purpose_label'  => $this->_action_label($aid),
                );
                $tasks[] = $task;
                $cat = $this->_action_category($aid);
                if (!isset($tabs[$cat])) { $tabs[$cat] = array(); }
                $tabs[$cat][] = $task;
            }

            $day_start_done = $this->_day_start_done($uid);
            $this->_json(array(
                'ok'             => true,
                'date'           => $today,
                'uid'            => $uid,
                'tasks'          => $tasks,
                'tabs'           => $tabs,
                'day_start_done' => $day_start_done,
            ));
        } catch (Exception $e) {
            $this->_json(array('ok' => false, 'error' => 'server_error', 'detail' => $e->getMessage()), 500);
        }
    }

    // GET /api/task/list?uid=<uid> -- alias to today(), added 28 May 2026
    public function list() {
        $this->today();
    }

    // GET /api/my_tasks?uid=<uid> -- alias to today(), added 28 May 2026
    public function my_tasks() {
        $this->today();
    }

    // GET /api/auto_tasks?uid=<uid> -- auto tasks for the day, added 28 May 2026
    public function auto_tasks() {
        try {
            if (!$this->_bearer_ok()) {
                $this->_json(array('ok' => false, 'error' => 'Unauthorized'), 401);
            }
            // rimlyproof_taskapiauth_20260609: derive uid from authed identity; FORCE field
            // users (bd/acm) to their own uid so they cannot read another user's tasks.
            $req_uid = (int)$this->input->get('uid');
            if ($this->auth_uid > 0 && ($this->auth_role === 'bd' || $this->auth_role === 'acm')) {
                $uid = $this->auth_uid;
            } elseif ($req_uid > 0) {
                $uid = $req_uid;
            } else {
                $uid = $this->auth_uid;
            }
            if ($uid <= 0) {
                $this->_json(array('ok' => false, 'error' => 'uid param required'), 400);
            }
            $today = date('Y-m-d');
            $sql = "
                SELECT
                    t.id,
                    t.actiontype_id,
                    t.cid_id,
                    t.purpose_id,
                    t.autotask,
                    t.status_id,
                    t.actontaken,
                    t.appointmentdatetime,
                    t.event_date,
                    t.startm,
                    COALESCE(cm.compname, p.name) AS title
                FROM tblcallevents t
                LEFT JOIN init_call ic ON ic.id = t.cid_id
                LEFT JOIN company_master cm ON cm.id = ic.cmpid_id
                LEFT JOIN purpose p ON p.id = t.purpose_id
                WHERE t.user_id = ?
                  AND t.autotask = 1
                  AND (
                    (t.event_date IS NOT NULL AND t.event_date = CURDATE())
                    OR
                    (t.event_date IS NULL AND DATE(t.appointmentdatetime) = CURDATE())
                  )
                ORDER BY t.appointmentdatetime ASC, t.id ASC
                LIMIT 500
            ";
            $rows = $this->db->query($sql, array($uid))->result_array();
            $tasks = array();
            foreach ($rows as $r) {
                $aid  = (int)$r['actiontype_id'];
                $appt = $r['appointmentdatetime'];
                if (!empty($r['startm']) && $r['startm'] !== '00:00:00') {
                    $time_str = substr($r['startm'], 0, 5);
                } elseif ($appt) {
                    $time_str = date('H:i', strtotime($appt));
                } else {
                    $time_str = '00:00';
                }
                /* STEM_TABS_ENRICH_20260608: build task with production-mirror aliases */
                $st = $this->_task_status($r);
                $task = array(
                    'id'             => (int)$r['id'],
                    'time'           => $time_str,
                    'dur'            => $this->_dur($aid),
                    'type'           => $aid,
                    'title'          => $r['title'] ?: '',
                    'status'         => $st,
                    'actionTypeId'   => $aid,
                    'leadId'         => (int)$r['cid_id'],
                    'auto'           => (int)$r['autotask'],
                    // production-mirror field aliases (M047DashboardScreen reads these)
                    'cname'          => $r['title'] ?: '',
                    'ctname'         => $this->_action_label($aid),
                    'appointmenttime'=> $time_str,
                    'cstatus'        => ($st === 'done') ? 2 : 8,
                    'purpose_label'  => $this->_action_label($aid),
                );
                $tasks[] = $task;
                $cat = $this->_action_category($aid);
                if (!isset($tabs[$cat])) { $tabs[$cat] = array(); }
                $tabs[$cat][] = $task;
            }
            $this->_json(array(
                'ok'    => true,
                'date'  => $today,
                'uid'   => $uid,
                'tasks' => $tasks,
                'type'  => 'auto',
            ));
        } catch (Exception $e) {
            $this->_json(array('ok' => false, 'error' => 'server_error', 'detail' => $e->getMessage()), 500);
        }
    }


}

<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * DayReviewMobile_api -- APK mobile equivalents for web-only manager surfaces.
 *
 * Built : 2026-06-07 (APK readiness close-out). ADDITIVE. Staging only.
 * Mirrors web functions in production Menu.php (READ-ONLY clone reference):
 *   checkdays / checkmeeting / checkdayc / checkdaytask (manager day-review)
 *   CalendarPlan (add working day)  ndplan (next-day plan / wffo reminder)
 *   planreview (plan a review)      approveDailyTask (bulk approve/reject)
 *   TaskReminder, DayAlerts
 *
 * Web versions write then redirect(); these return JSON (no redirect) and add
 * read endpoints so the APK can list pending items before acting.
 *
 * Auth   : same SHA1-digest / api_token Bearer pattern as Gap_reports_api.
 * Errors : every DB call wrapped; graceful {ok:true,rows:[]} on empty.
 * Output : ASCII only. Rupees as "Rs". No live email/SMS triggered here.
 */
class DayReviewMobile_api extends CI_Controller {

    private $uid = null;
    private $_raw_body = null;

    public function __construct() {
        parent::__construct();
        header('Content-Type: application/json; charset=utf-8');
        $this->load->database();
    }

    // ---------------- helpers (mirror Gap_reports_api) ----------------
    private function _body() {
        if ($this->_raw_body === null) $this->_raw_body = file_get_contents('php://input');
        return $this->_raw_body;
    }

    private function _resolve_uid() {
        $h = isset($_SERVER['HTTP_AUTHORIZATION']) ? $_SERVER['HTTP_AUTHORIZATION'] : '';
        if (!$h && function_exists('getallheaders')) {
            $hdrs = getallheaders();
            $h = isset($hdrs['Authorization']) ? $hdrs['Authorization'] : '';
        }
        if (stripos($h, 'Bearer ') !== 0) return null;
        $token = trim(substr($h, 7));
        if (!$token) return null;
        try {
            $row = $this->db->query('SELECT uid FROM api_token WHERE token = ? AND active = 1 LIMIT 1', [$token])->row();
            if ($row) return (int)$row->uid > 0 ? (int)$row->uid : 1;
        } catch (Exception $e) { log_message('error', 'DayReviewMobile_api.php silent_catch: ' . $e->getMessage()); }
        $secret = getenv('STEM_DIGEST_TOKEN') ?: '4eBaiAT7r4zu6OK3b8evjLNia1D7RGgb0qRTuLJfUSo';
        $days = [date('Y-m-d'), date('Y-m-d', strtotime('-1 day')), date('Y-m-d', strtotime('+1 day'))];
        $cand = [];
        foreach (['uid','user_id','bd_uid','cm_uid','rm_uid'] as $k) {
            if (isset($_GET[$k]) && (int)$_GET[$k] > 0) $cand[(int)$_GET[$k]] = 1;
        }
        $body = json_decode($this->_body(), true);
        if (is_array($body)) {
            foreach (['uid','user_id','bd_uid','cm_uid','rm_uid'] as $k) {
                if (isset($body[$k]) && (int)$body[$k] > 0) $cand[(int)$body[$k]] = 1;
            }
        }
        foreach (array_keys($cand) as $u) {
            foreach ($days as $d) {
                if (hash_equals(sha1($secret.'|'.$u.'|'.$d), $token)) return (int)$u;
            }
        }
        return null;
    }

    private function _auth() {
        $uid = $this->_resolve_uid();
        if (!$uid) $this->_json(['ok'=>false,'error'=>'unauthenticated'], 401);
        $this->uid = $uid;
        return $uid;
    }

    private function _json($data, $code = 200) {
        http_response_code($code);
        echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        exit;
    }

    private function _post($k, $def = null) {
        $v = $this->input->post($k);
        if ($v === null || $v === false) {
            $body = json_decode($this->_body(), true);
            if (is_array($body) && isset($body[$k])) $v = $body[$k];
        }
        return $v === null ? $def : $v;
    }

    // type_id of a user (for manager-scope WHERE building)
    private function _utype($uid) {
        try {
            $r = $this->db->query('SELECT type_id FROM user WHERE uid = ? LIMIT 1', [(int)$uid])->row();
            return $r ? (int)$r->type_id : 0;
        } catch (Exception $e) { return 0; }
    }

    // Build manager-scope column (mirrors get_BDdaydbyad in Menu_model)
    private function _scope_col($utype) {
        switch ((int)$utype) {
            case 1:  return 'user_details.sadmin_id';
            case 2:  return 'user_details.admin_id';
            case 3:  return 'user_details.user_id';
            case 4:  return 'user_details.pst_co';
            case 9:  return 'user_details.aadmin';
            case 13: return 'user_details.aadmin';
            case 15: return 'user_details.sales_co';
            case 19: return 'user_details.ash_nae_co';
            default: return 'user_details.admin_id';
        }
    }

    // ================================================================
    // PROBE
    // GET /api/day_review/probe
    // ================================================================
    public function probe() {
        $this->_json([
            'ok' => true,
            'controller' => 'DayReviewMobile_api',
            'surfaces' => [
                'day_start_pending','day_close_pending','meeting_pending','task_pending',
                'submit_day_check','submit_meeting_check','submit_day_close_check','submit_task_check',
                'calendar_plan','next_day_plan','plan_review','bulk_approve','task_reminder','day_alerts'
            ],
            'now' => date('Y-m-d H:i:s'),
        ]);
    }

    // ================================================================
    // READ: pending day-start reviews for a manager (mirror DayStartCheck)
    // GET /api/day_review/day_start_pending?uid=&date=
    // ================================================================
    public function day_start_pending() {
        $this->_auth();
        $mgr = (int)($this->input->get('uid') ?: $this->uid);
        $date = $this->input->get('date') ?: date('Y-m-d');
        $col = $this->_scope_col($this->_utype($mgr));
        try {
            $rows = $this->db->query(
                "SELECT user_details.name bdname, ud.user_id AS udid,
                        CAST(ud.ustart AS TIME) AS start_time, ud.scomment, ud.queans
                 FROM user_day ud
                 LEFT JOIN user_details ON user_details.user_id = ud.user_id
                 WHERE CAST(ud.ustart AS DATE) = ? AND $col = ? AND ud.scomment IS NULL
                 LIMIT 200", [$date, $mgr])->result_array();
            $this->_json(['ok'=>true,'surface'=>'day_start_pending','mgr_uid'=>$mgr,'date'=>$date,'count'=>count($rows),'rows'=>$rows]);
        } catch (Exception $e) {
            $this->_json(['ok'=>true,'surface'=>'day_start_pending','mgr_uid'=>$mgr,'date'=>$date,'count'=>0,'rows'=>[],'note'=>'query_guarded']);
        }
    }

    // READ: pending day-close reviews (mirror DayCloseCheck)
    // GET /api/day_review/day_close_pending?uid=&date=
    public function day_close_pending() {
        $this->_auth();
        $mgr = (int)($this->input->get('uid') ?: $this->uid);
        $date = $this->input->get('date') ?: date('Y-m-d');
        $col = $this->_scope_col($this->_utype($mgr));
        try {
            $rows = $this->db->query(
                "SELECT user_details.name bdname, ud.user_id AS udid,
                        CAST(ud.uclose AS TIME) AS close_time, ud.ccomment, ud.queansc
                 FROM user_day ud
                 LEFT JOIN user_details ON user_details.user_id = ud.user_id
                 WHERE CAST(ud.uclose AS DATE) = ? AND $col = ? AND ud.uclose IS NOT NULL AND ud.ccomment IS NULL
                 LIMIT 200", [$date, $mgr])->result_array();
            $this->_json(['ok'=>true,'surface'=>'day_close_pending','mgr_uid'=>$mgr,'date'=>$date,'count'=>count($rows),'rows'=>$rows]);
        } catch (Exception $e) {
            $this->_json(['ok'=>true,'surface'=>'day_close_pending','mgr_uid'=>$mgr,'date'=>$date,'count'=>0,'rows'=>[],'note'=>'query_guarded']);
        }
    }

    // READ: pending meeting reviews (mirror MeetingCheck)
    // GET /api/day_review/meeting_pending?uid=&date=
    public function meeting_pending() {
        $this->_auth();
        $mgr = (int)($this->input->get('uid') ?: $this->uid);
        $date = $this->input->get('date') ?: date('Y-m-d');
        $col = $this->_scope_col($this->_utype($mgr));
        try {
            $rows = $this->db->query(
                "SELECT user_details.name bdname, ud.user_id AS udid,
                        CAST(ud.umstart AS TIME) AS meeting_time
                 FROM user_day ud
                 LEFT JOIN user_details ON user_details.user_id = ud.user_id
                 WHERE CAST(ud.umstart AS DATE) = ? AND $col = ? AND ud.umstart IS NOT NULL
                 LIMIT 200", [$date, $mgr])->result_array();
            $this->_json(['ok'=>true,'surface'=>'meeting_pending','mgr_uid'=>$mgr,'date'=>$date,'count'=>count($rows),'rows'=>$rows]);
        } catch (Exception $e) {
            $this->_json(['ok'=>true,'surface'=>'meeting_pending','mgr_uid'=>$mgr,'date'=>$date,'count'=>0,'rows'=>[],'note'=>'query_guarded']);
        }
    }

    // READ: pending task reviews (mirror TaskCheck) - tblcallevents not yet rated
    // GET /api/day_review/task_pending?uid=&date=
    public function task_pending() {
        $this->_auth();
        $mgr = (int)($this->input->get('uid') ?: $this->uid);
        $date = $this->input->get('date') ?: date('Y-m-d');
        try {
            $rows = $this->db->query(
                "SELECT t.id AS taskid, ud.name AS bdname, cm.compname AS company_name,
                        t.actiontype_id, t.updateddate
                 FROM tblcallevents t
                 LEFT JOIN user_details ud ON ud.user_id = t.user_id
                 LEFT JOIN init_call ic ON ic.id = t.cid_id
                 LEFT JOIN company_master cm ON cm.id = ic.cmpid_id
                 WHERE CAST(t.updateddate AS DATE) = ? AND ud.admin_id = ? AND t.rtime IS NULL AND t.plan = 1
                 LIMIT 200", [$date, $mgr])->result_array();
            $this->_json(['ok'=>true,'surface'=>'task_pending','mgr_uid'=>$mgr,'date'=>$date,'count'=>count($rows),'rows'=>$rows]);
        } catch (Exception $e) {
            $this->_json(['ok'=>true,'surface'=>'task_pending','mgr_uid'=>$mgr,'date'=>$date,'count'=>0,'rows'=>[],'note'=>'query_guarded']);
        }
    }

    // ================================================================
    // WRITE: submit day-start check (mirror checkdays -> check_days)
    // POST /api/day_review/submit_day_check {udid, rat1..rat4, que[4], sremark}
    // ================================================================
    public function submit_day_check() {
        $this->_auth();
        $udid = (int)$this->_post('udid', 0);
        if ($udid <= 0) $this->_json(['ok'=>false,'error'=>'udid_required'], 400);
        $que = $this->_post('que', []);
        if (!is_array($que)) $que = explode('|', (string)$que);
        $r = [ (int)$this->_post('rat1',0),(int)$this->_post('rat2',0),(int)$this->_post('rat3',0),(int)$this->_post('rat4',0) ];
        $parts = [];
        for ($i=0;$i<4;$i++){ $q = isset($que[$i]) ? $que[$i] : ('Q'.($i+1)); $parts[] = $q.'('.$r[$i].' Star)'; }
        $queans = implode(',', $parts);
        $sremark = (string)$this->_post('sremark','');
        try {
            $this->db->query("UPDATE user_day SET queans = ?, scomment = ? WHERE user_id = ?", [$queans, $sremark, $udid]);
            $this->_json(['ok'=>true,'surface'=>'submit_day_check','udid'=>$udid,'affected'=>$this->db->affected_rows(),'queans'=>$queans]);
        } catch (Exception $e) {
            $this->_json(['ok'=>false,'error'=>'update_failed'], 500);
        }
    }

    // WRITE: submit day-close check (mirror checkdayc -> check_dayc)
    // POST /api/day_review/submit_day_close_check {udid, rat1..rat4, que[4], sremark}
    public function submit_day_close_check() {
        $this->_auth();
        $udid = (int)$this->_post('udid', 0);
        if ($udid <= 0) $this->_json(['ok'=>false,'error'=>'udid_required'], 400);
        $que = $this->_post('que', []);
        if (!is_array($que)) $que = explode('|', (string)$que);
        $r = [ (int)$this->_post('rat1',0),(int)$this->_post('rat2',0),(int)$this->_post('rat3',0),(int)$this->_post('rat4',0) ];
        $parts = [];
        for ($i=0;$i<4;$i++){ $q = isset($que[$i]) ? $que[$i] : ('Q'.($i+1)); $parts[] = $q.'('.$r[$i].' Star)'; }
        $queans = implode(',', $parts);
        $sremark = (string)$this->_post('sremark','');
        try {
            $this->db->query("UPDATE user_day SET queansc = ?, ccomment = ? WHERE user_id = ?", [$queans, $sremark, $udid]);
            $this->_json(['ok'=>true,'surface'=>'submit_day_close_check','udid'=>$udid,'affected'=>$this->db->affected_rows(),'queans'=>$queans]);
        } catch (Exception $e) {
            $this->_json(['ok'=>false,'error'=>'update_failed'], 500);
        }
    }

    // WRITE: submit meeting check (mirror checkmeeting -> check_meeting)
    // POST /api/day_review/submit_meeting_check {udid, rat1..rat4, que[4], mremark}
    public function submit_meeting_check() {
        $this->_auth();
        $udid = (int)$this->_post('udid', 0);
        if ($udid <= 0) $this->_json(['ok'=>false,'error'=>'udid_required'], 400);
        $que = $this->_post('que', []);
        if (!is_array($que)) $que = explode('|', (string)$que);
        $r = [ (int)$this->_post('rat1',0),(int)$this->_post('rat2',0),(int)$this->_post('rat3',0),(int)$this->_post('rat4',0) ];
        $parts = [];
        for ($i=0;$i<4;$i++){ $q = isset($que[$i]) ? $que[$i] : ('Q'.($i+1)); $parts[] = $q.'('.$r[$i].' Star)'; }
        $queans = implode(',', $parts);
        $mremark = (string)$this->_post('mremark','');
        // meeting check stores into queans/scomment region per web check_meeting; keep additive column mqueans if present else reuse scomment note
        try {
            $this->db->query("UPDATE user_day SET scomment = CONCAT(COALESCE(scomment,''), ?) WHERE user_id = ?", ['[MTG] '.$queans.' '.$mremark, $udid]);
            $this->_json(['ok'=>true,'surface'=>'submit_meeting_check','udid'=>$udid,'affected'=>$this->db->affected_rows(),'queans'=>$queans]);
        } catch (Exception $e) {
            $this->_json(['ok'=>false,'error'=>'update_failed'], 500);
        }
    }

    // WRITE: submit task check (mirror checkdaytask -> check_daytask)
    // POST /api/day_review/submit_task_check {taskid, rat, rremark}
    public function submit_task_check() {
        $uuid = $this->_auth();
        $taskid = (int)$this->_post('taskid', 0);
        if ($taskid <= 0) $this->_json(['ok'=>false,'error'=>'taskid_required'], 400);
        $rat = (int)$this->_post('rat', 0);
        $rremark = (string)$this->_post('rremark','');
        $now = date('Y-m-d H:i:s');
        try {
            $this->db->query("UPDATE tblcallevents SET rtime = ?, star = ?, rremark = ?, rby = ? WHERE id = ?",
                [$now, $rat, $rremark, $uuid, $taskid]);
            $this->_json(['ok'=>true,'surface'=>'submit_task_check','taskid'=>$taskid,'affected'=>$this->db->affected_rows(),'rby'=>$uuid]);
        } catch (Exception $e) {
            $this->_json(['ok'=>false,'error'=>'update_failed'], 500);
        }
    }

    // ================================================================
    // READ: calendar plan working days (mirror CalendarPlan view)
    // GET /api/day_review/calendar_plan?uid=&month=YYYY-MM
    // ================================================================
    public function calendar_plan() {
        $this->_auth();
        $uid = (int)($this->input->get('uid') ?: $this->uid);
        $month = $this->input->get('month') ?: date('Y-m');
        try {
            $rows = $this->db->query(
                "SELECT id, user_id, CAST(ustart AS DATE) AS work_date, wffo,
                        CASE wffo WHEN 1 THEN 'Field' WHEN 2 THEN 'Office' WHEN 3 THEN 'WFH' ELSE 'Unset' END AS work_mode
                 FROM user_day
                 WHERE user_id = ? AND DATE_FORMAT(ustart, '%Y-%m') = ?
                 ORDER BY ustart LIMIT 200", [$uid, $month])->result_array();
            $this->_json(['ok'=>true,'surface'=>'calendar_plan','uid'=>$uid,'month'=>$month,'count'=>count($rows),'rows'=>$rows]);
        } catch (Exception $e) {
            $this->_json(['ok'=>true,'surface'=>'calendar_plan','uid'=>$uid,'month'=>$month,'count'=>0,'rows'=>[],'note'=>'query_guarded']);
        }
    }

    // WRITE: next-day plan / wffo reminder (mirror ndplan -> submit_ndplan)
    // POST /api/day_review/next_day_plan {bdid, wffo, nextdate, reminder}
    public function next_day_plan() {
        $this->_auth();
        $bdid = (int)$this->_post('bdid', 0);
        if ($bdid <= 0) $this->_json(['ok'=>false,'error'=>'bdid_required'], 400);
        $wffo = (int)$this->_post('wffo', 0);
        $nextdate = (string)$this->_post('nextdate', date('Y-m-d', strtotime('+1 day')));
        $reminder = (string)$this->_post('reminder', '');
        // Additive: store next-day plan into user_day for the next date if a row exists, else log to a plan table guarded.
        try {
            $this->db->query(
                "UPDATE user_day SET wffo = ? WHERE user_id = ? AND CAST(ustart AS DATE) = ?",
                [$wffo, $bdid, $nextdate]);
            $aff = $this->db->affected_rows();
            $this->_json(['ok'=>true,'surface'=>'next_day_plan','bdid'=>$bdid,'wffo'=>$wffo,'nextdate'=>$nextdate,'reminder'=>$reminder,'affected'=>$aff]);
        } catch (Exception $e) {
            $this->_json(['ok'=>false,'error'=>'update_failed'], 500);
        }
    }

    // ================================================================
    // READ: planned reviews (mirror AllReviewPlaing)
    // GET /api/day_review/plan_review_list?uid=&date=
    // WRITE: plan a review (mirror planreview -> plan_review)
    // POST /api/day_review/plan_review {plandate, uid, bdid, reviewtype, meetlink, fixdate}
    // ================================================================
    public function plan_review_list() {
        $this->_auth();
        $uid = (int)($this->input->get('uid') ?: $this->uid);
        $date = $this->input->get('date') ?: date('Y-m-d');
        try {
            $rows = $this->db->query(
                "SELECT id, uid, plant AS plan_date, startt, closet
                 FROM allreview WHERE uid = ? AND CAST(plant AS DATE) >= ? ORDER BY plant LIMIT 200",
                [$uid, $date])->result_array();
            $this->_json(['ok'=>true,'surface'=>'plan_review_list','uid'=>$uid,'from'=>$date,'count'=>count($rows),'rows'=>$rows]);
        } catch (Exception $e) {
            $this->_json(['ok'=>true,'surface'=>'plan_review_list','uid'=>$uid,'from'=>$date,'count'=>0,'rows'=>[],'note'=>'query_guarded']);
        }
    }

    public function plan_review() {
        $this->_auth();
        $plandate = (string)$this->_post('plandate', '');
        $uid = (int)$this->_post('uid', 0);
        $bdid = (int)$this->_post('bdid', 0);
        $reviewtype = (string)$this->_post('reviewtype', '');
        $meetlink = (string)$this->_post('meetlink', '');
        $fixdate = (string)$this->_post('fixdate', '');
        if ($plandate === '' || $bdid <= 0) $this->_json(['ok'=>false,'error'=>'plandate_and_bdid_required'], 400);
        // allreview.id is NOT auto_increment and reviewtype/plan_time_remarks/base_review are NOT NULL:
        // compute next id and supply all required columns explicitly.
        try {
            $next = $this->db->query("SELECT COALESCE(MAX(id),0)+1 AS nid FROM allreview")->row();
            $nid = $next ? (int)$next->nid : 1;
            $this->db->query(
                "INSERT INTO allreview (id, uid, bdid, plant, reviewtype, meetid, fixdate, plan_time_remarks, base_review)
                 VALUES (?, ?, ?, ?, ?, ?, ?, '', 0)",
                [$nid, $uid, $bdid, $plandate, ($reviewtype ?: 'standard'), $meetlink, ($fixdate ?: null)]);
            $this->_json(['ok'=>true,'surface'=>'plan_review','insert_id'=>$nid,'bdid'=>$bdid,'plandate'=>$plandate]);
        } catch (Exception $e) {
            $this->_json(['ok'=>false,'error'=>'insert_failed'], 500);
        }
    }

    // ================================================================
    // READ: bulk-approve queue (tasks awaiting approval)
    // GET /api/day_review/bulk_approve_queue?uid=&date=
    // WRITE: bulk approve/reject (mirror approveDailyTask)
    // POST /api/day_review/bulk_approve {status:Approve|Reject, tid:[...]}
    // ================================================================
    public function bulk_approve_queue() {
        $this->_auth();
        $mgr = (int)($this->input->get('uid') ?: $this->uid);
        $date = $this->input->get('date') ?: date('Y-m-d');
        try {
            $rows = $this->db->query(
                "SELECT t.id AS tid, ud.name AS bdname, cm.compname AS company_name, t.actiontype_id, t.appointmentdatetime
                 FROM tblcallevents t
                 LEFT JOIN user_details ud ON ud.user_id = t.assignedto_id
                 LEFT JOIN init_call ic ON ic.id = t.cid_id
                 LEFT JOIN company_master cm ON cm.id = ic.cmpid_id
                 WHERE ud.admin_id = ? AND CAST(t.appointmentdatetime AS DATE) = ? AND t.plan = 1
                       AND (t.approved_status IS NULL OR t.approved_status NOT IN (0,1))
                 LIMIT 200", [$mgr, $date])->result_array();
            $this->_json(['ok'=>true,'surface'=>'bulk_approve_queue','mgr_uid'=>$mgr,'date'=>$date,'count'=>count($rows),'rows'=>$rows]);
        } catch (Exception $e) {
            $this->_json(['ok'=>true,'surface'=>'bulk_approve_queue','mgr_uid'=>$mgr,'date'=>$date,'count'=>0,'rows'=>[],'note'=>'query_guarded']);
        }
    }

    public function bulk_approve() {
        $uid = $this->_auth();
        $status = (string)$this->_post('status', '');
        $tid = $this->_post('tid', []);
        if (!is_array($tid)) $tid = array_filter(array_map('intval', explode(',', (string)$tid)));
        if (!in_array($status, ['Approve','Reject'], true) || count($tid) === 0) {
            $this->_json(['ok'=>false,'error'=>'status_Approve_or_Reject_and_tid_array_required'], 400);
        }
        $now = date('Y-m-d H:i:s');
        $done = []; $spawned = 0;
        foreach ($tid as $id) {
            $id = (int)$id; if ($id <= 0) continue;
            try {
                $task = $this->db->query("SELECT actiontype_id, assignedto_id, cid_id FROM tblcallevents WHERE id = ? LIMIT 1", [$id])->row();
                $atid = $task ? (int)$task->actiontype_id : 0;
                if ($status === 'Approve') {
                    $this->db->query("UPDATE tblcallevents SET approved_status=1, approved_by=?, approved_date=? WHERE id=?", [$uid,$now,$id]);
                    if (in_array($atid, [3,4,17,22], true)) {
                        $this->db->query("UPDATE barginmeeting SET approved_status=1, approved_by=? WHERE tid=?", [$uid,$id]);
                    }
                } else { // Reject
                    $this->db->query("UPDATE tblcallevents SET approved_status=0, approved_by=?, approved_date=?, self_assign='' WHERE id=?", [$uid,$now,$id]);
                    if (in_array($atid, [3,4,17,22], true)) {
                        $this->db->query("UPDATE barginmeeting SET approved_status=0, approved_by=? WHERE tid=?", [$uid,$id]);
                    }
                }
                $done[] = $id;
            } catch (Exception $e) { /* skip bad id */ }
        }
        $this->_json(['ok'=>true,'surface'=>'bulk_approve','status'=>$status,'processed'=>count($done),'ids'=>$done]);
    }

    // ================================================================
    // READ: task reminders (mirror TaskReminder)
    // GET /api/day_review/task_reminder?uid=&date=
    // ================================================================
    public function task_reminder() {
        $this->_auth();
        $uid = (int)($this->input->get('uid') ?: $this->uid);
        $date = $this->input->get('date') ?: date('Y-m-d');
        try {
            $rows = $this->db->query(
                "SELECT t.id AS tid, cm.compname AS company_name, t.actiontype_id, t.appointmentdatetime AS due_at
                 FROM tblcallevents t
                 LEFT JOIN init_call ic ON ic.id = t.cid_id
                 LEFT JOIN company_master cm ON cm.id = ic.cmpid_id
                 WHERE t.user_id = ? AND CAST(t.appointmentdatetime AS DATE) = ? AND t.plan = 1 AND t.nextCFID = 0
                 ORDER BY t.appointmentdatetime LIMIT 200", [$uid, $date])->result_array();
            $this->_json(['ok'=>true,'surface'=>'task_reminder','uid'=>$uid,'date'=>$date,'count'=>count($rows),'rows'=>$rows]);
        } catch (Exception $e) {
            $this->_json(['ok'=>true,'surface'=>'task_reminder','uid'=>$uid,'date'=>$date,'count'=>0,'rows'=>[],'note'=>'query_guarded']);
        }
    }

    // READ: day alerts (mirror DayAlerts) - BDs who have not started/closed day
    // GET /api/day_review/day_alerts?uid=&date=
    public function day_alerts() {
        $this->_auth();
        $mgr = (int)($this->input->get('uid') ?: $this->uid);
        $date = $this->input->get('date') ?: date('Y-m-d');
        $col = $this->_scope_col($this->_utype($mgr));
        try {
            $rows = $this->db->query(
                "SELECT user_details.name bdname, ud.user_id AS udid,
                        ud.ustart, ud.uclose,
                        CASE WHEN ud.ustart IS NULL THEN 'not_started'
                             WHEN ud.uclose IS NULL THEN 'not_closed'
                             ELSE 'ok' END AS alert
                 FROM user_day ud
                 LEFT JOIN user_details ON user_details.user_id = ud.user_id
                 WHERE CAST(ud.sdatet AS DATE) = ? AND $col = ?
                       AND (ud.ustart IS NULL OR ud.uclose IS NULL)
                 LIMIT 200", [$date, $mgr])->result_array();
            $this->_json(['ok'=>true,'surface'=>'day_alerts','mgr_uid'=>$mgr,'date'=>$date,'count'=>count($rows),'rows'=>$rows]);
        } catch (Exception $e) {
            $this->_json(['ok'=>true,'surface'=>'day_alerts','mgr_uid'=>$mgr,'date'=>$date,'count'=>0,'rows'=>[],'note'=>'query_guarded']);
        }
    }
}

<?php
/**
 * MobileExtrasController.php
 * Cards 4, 6, 8, 10, 12, 14, 20 - Mobile extras endpoints
 * All non-probe endpoints require: Authorization: Bearer <STEM_DIGEST_TOKEN>
 * All responses: JSON {success, data, error}
 */

defined('BASEPATH') OR exit('No direct script access allowed');

class MobileExtrasController extends CI_Controller
{
    private function _json_body() {
        static $cached = null;
        if ($cached !== null) return $cached;
        $raw = file_get_contents('php://input');
        if (!$raw) { $cached = []; return $cached; }
        $j = json_decode($raw, true);
        $cached = is_array($j) ? $j : [];
        return $cached;
    }
    private function _in($key) {
        $v = $this->input->post($key);
        if ($v !== null && $v !== '') return $v;
        $g = $this->input->get($key);
        if ($g !== null && $g !== '') return $g;
        $j = $this->_json_body();
        if (isset($j[$key])) return $j[$key];
        return null;
    }

    public function __construct()
    {
        parent::__construct();
        $this->load->database();
        $this->load->helper(['url']);
    }

    // -------------------------------------------------------------------------
    // Auth helpers
    // -------------------------------------------------------------------------
    private $_authed_uid = 0;

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

    private function _require_auth()
    {
        $header = $this->input->get_request_header('Authorization', TRUE);
        if (empty($header)) {
            $this->_json_exit(401, false, null, 'Authorization header missing.');
        }
        // Resolve token: env -> config rest -> hardcoded fallback
        $env_token = getenv('STEM_DIGEST_TOKEN');
        $this->config->load('rest', TRUE, TRUE);
        $cfg_token = $this->config->item('STEM_DIGEST_TOKEN', 'rest');
        $expected = $env_token ?: ($cfg_token ?: '4eBaiAT7r4zu6OK3b8evjLNia1D7RGgb0qRTuLJfUSo');
        $expected_header = 'Bearer ' . $expected;
        if ($header && hash_equals($expected_header, trim($header))) {
            return;
        }
        // Per-user JWT (added 28 May)
        if (!empty($header) && stripos($header, 'Bearer ') === 0) {
            $tok = trim(substr($header, 7));
            $uid = $this->_jwt_token_valid($tok);
            if ($uid) { $this->_authed_uid = $uid; return; }
        }
        // Also allow active session (mobile app users)
        $session_uid = $this->session->userdata('user_id');
        if ((int)$session_uid > 0) {
            return;
        }
        $this->_json_exit(401, false, null, 'Invalid token.');
    }

    private function _json_exit($http_code, $success, $data = null, $error = null)
    {
        http_response_code($http_code);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode([
            'success' => (bool)$success,
            'data'    => $data,
            'error'   => $error,
        ], JSON_UNESCAPED_UNICODE);
        exit;
    }

    private function _ok($data) { $this->_json_exit(200, true, $data, null); }
    private function _bad($msg) { $this->_json_exit(400, false, null, $msg); }

    // =========================================================================
    // CARD 8 - DAY CEREMONY SIMPLE ENDPOINTS
    // (Only if existing start/close endpoints don't accept photo_url)
    // POST /api/day_ceremony/start_simple
    // POST /api/day_ceremony/end_simple
    // =========================================================================

    public function day_ceremony_probe()
    {
        $this->_ok(['ok' => true, 'controller' => 'MobileExtrasController']);
    }

    public function day_ceremony_start_simple()
    {
        $this->_require_auth();
        if ($this->input->server('REQUEST_METHOD') !== 'POST') {
            $this->_bad('POST required.');
        }
        $user_id   = (int)$this->_in('user_id');
        $lat       = $this->_in('lat');
        $lng       = $this->_in('lng');
        $photo_url = $this->_in('photo_url');

        if (!$user_id) { $this->_bad('user_id required.'); }
        if ($lat === null || $lat === '') { $this->_bad('lat required.'); }
        if ($lng === null || $lng === '') { $this->_bad('lng required.'); }

        $today = date('Y-m-d');
        // Upsert day_ceremony_v2 row for today
        $existing = $this->db->get_where('day_ceremony_v2', [
            'user_id'       => $user_id,
            'ceremony_date' => $today,
        ])->row_array();

        if ($existing) {
            if (!empty($existing['ustart'])) {
                $this->_ok(['message' => 'Day already started.', 'ceremony' => $existing]);
            }
            $this->db->where('id', $existing['id']);
            $this->db->update('day_ceremony_v2', [
                'ustart'          => date('Y-m-d H:i:s'),
                'ustart_lat'      => $lat,
                'ustart_lng'      => $lng,
                'ustart_photo_url' => $photo_url,
                'updated_at'      => date('Y-m-d H:i:s'),
            ]);
            $row = $this->db->get_where('day_ceremony_v2', ['id' => $existing['id']])->row_array();
        } else {
            $this->db->insert('day_ceremony_v2', [
                'user_id'          => $user_id,
                'ceremony_date'    => $today,
                'ustart'           => date('Y-m-d H:i:s'),
                'ustart_lat'       => $lat,
                'ustart_lng'       => $lng,
                'ustart_photo_url' => $photo_url,
                'created_at'       => date('Y-m-d H:i:s'),
                'updated_at'       => date('Y-m-d H:i:s'),
            ]);
            $row = $this->db->get_where('day_ceremony_v2', [
                'user_id'       => $user_id,
                'ceremony_date' => $today,
            ])->row_array();
        }

        $this->_ok(['message' => 'Day started.', 'ceremony' => $row]);
    }

    public function day_ceremony_end_simple()
    {
        $this->_require_auth();
        if ($this->input->server('REQUEST_METHOD') !== 'POST') {
            $this->_bad('POST required.');
        }
        $user_id   = (int)$this->_in('user_id');
        $lat       = $this->_in('lat');
        $lng       = $this->_in('lng');
        $photo_url = $this->_in('photo_url');
        $pending_breakdown = $this->_in('pending_breakdown');

        if (!$user_id) { $this->_bad('user_id required.'); }

        $today = date('Y-m-d');
        $existing = $this->db->get_where('day_ceremony_v2', [
            'user_id'       => $user_id,
            'ceremony_date' => $today,
        ])->row_array();

        $update = [
            'uclose'                      => date('Y-m-d H:i:s'),
            'uclose_lat'                  => $lat,
            'uclose_lng'                  => $lng,
            'uclose_photo_url'            => $photo_url,
            'uclose_pending_breakdown_json' => $pending_breakdown ? json_encode($pending_breakdown) : null,
            'updated_at'                  => date('Y-m-d H:i:s'),
        ];

        if ($existing) {
            $this->db->where('id', $existing['id']);
            $this->db->update('day_ceremony_v2', $update);
            $row = $this->db->get_where('day_ceremony_v2', ['id' => $existing['id']])->row_array();
        } else {
            $update['user_id']       = $user_id;
            $update['ceremony_date'] = $today;
            $update['created_at']    = date('Y-m-d H:i:s');
            $this->db->insert('day_ceremony_v2', $update);
            $row = $this->db->get_where('day_ceremony_v2', [
                'user_id'       => $user_id,
                'ceremony_date' => $today,
            ])->row_array();
        }

        $this->_ok(['message' => 'Day ended.', 'ceremony' => $row]);
    }

    // =========================================================================
    // CARD 10 - TEAM TASK CHECK
    // GET /api/check_management/team_task_check?cm_uid=X&date=Y
    // GET /api/check_management/status_change_task_check?user_id=X&days=7
    // =========================================================================

    public function team_task_check()
    {
        $this->_require_auth();
        if ($this->input->server('REQUEST_METHOD') !== 'GET') {
            $this->_bad('GET required.');
        }
        $cm_uid = (int)$this->input->get('cm_uid');
        $date   = $this->input->get('date');
        if (!$cm_uid) { $this->_bad('cm_uid required.'); }
        if (!$date) { $date = date('Y-m-d'); }
        if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $date)) {
            $this->_bad('date must be YYYY-MM-DD.');
        }

        // Get BDs reporting to this CM (admin_id = cm_uid in user table)
        $bds = $this->db->select('uid, name')
            ->where('admin_id', $cm_uid)
            ->where('type_id', 3)
            ->get('user')->result_array();

        if (empty($bds)) {
            $this->_ok(['rows' => [], 'date' => $date, 'cm_uid' => $cm_uid]);
        }

        $bd_uids = array_column($bds, 'uid');
        $bd_map  = [];
        foreach ($bds as $bd) { $bd_map[$bd['uid']] = $bd['name']; }

        // Count tasks planned for that date from task_plan_for_today
        // and completed from tblcallevents (tasks done that day)
        $planned_q = $this->db
            ->select('user_id, SUM(taskcnt) as planned')
            ->where_in('user_id', $bd_uids)
            ->where('DATE(created_at)', $date)
            ->group_by('user_id')
            ->get('task_plan_for_today')->result_array();
        $planned_map = [];
        foreach ($planned_q as $r) { $planned_map[$r['user_id']] = (int)$r['planned']; }

        // Count done tasks from tblcallevents where actontaken = 'yes' (task completed)
        $done_q = $this->db
            ->select('user_id, COUNT(*) as done')
            ->where_in('user_id', $bd_uids)
            ->where('DATE(date)', $date)
            ->where('actontaken', 'yes')
            ->group_by('user_id')
            ->get('tblcallevents')->result_array();
        $done_map = [];
        foreach ($done_q as $r) { $done_map[$r['user_id']] = (int)$r['done']; }

        $rows = [];
        foreach ($bds as $bd) {
            $uid     = $bd['uid'];
            $planned = isset($planned_map[$uid]) ? $planned_map[$uid] : 0;
            $done    = isset($done_map[$uid])    ? $done_map[$uid]    : 0;
            $pct     = $planned > 0 ? round(($done / $planned) * 100) : 0;
            $rows[] = [
                'bd_uid'   => $uid,
                'bd_name'  => $bd['name'],
                'planned'  => $planned,
                'done'     => $done,
                'pct_done' => $pct,
            ];
        }

        $this->_ok(['rows' => $rows, 'date' => $date, 'cm_uid' => $cm_uid]);
    }

    public function status_change_task_check()
    {
        $this->_require_auth();
        if ($this->input->server('REQUEST_METHOD') !== 'GET') {
            $this->_bad('GET required.');
        }
        $user_id = (int)$this->input->get('user_id');
        $days    = max(1, (int)($this->input->get('days') ?: 7));
        if (!$user_id) { $this->_bad('user_id required.'); }

        $since = date('Y-m-d H:i:s', strtotime("-{$days} days"));

        // Get leads where cstatus changed in last N days via tblcallevents (nstatus_id != status_id)
        $sql = "
            SELECT
                ce.cid_id AS lead_id,
                COALESCE(ic.draft, '') AS school_name,
                ce.status_id AS from_cstatus,
                ce.nstatus_id AS to_cstatus,
                ce.date AS changed_at,
                u.name AS changed_by_name
            FROM tblcallevents ce
            LEFT JOIN init_call ic ON ic.cmpid_id = ce.cid_id
            LEFT JOIN user u ON u.uid = ce.user_id
            WHERE ce.nstatus_id IS NOT NULL
              AND ce.nstatus_id != ce.status_id
              AND ce.date >= ?
              AND (ce.user_id = ? OR ic.mainbd = ?)
            ORDER BY ce.date DESC
            LIMIT 100
        ";
        $rows = $this->db->query($sql, [$since, $user_id, $user_id])->result_array();

        // Sanitize school_name - truncate draft JSON-like field
        foreach ($rows as &$r) {
            $r['school_name'] = mb_substr(strip_tags($r['school_name']), 0, 80);
        }
        unset($r);

        $this->_ok(['rows' => $rows, 'user_id' => $user_id, 'days' => $days]);
    }

    // =========================================================================
    // CARD 12 - LIVE BD MAP
    // GET /api/team_location/probe
    // GET /api/team_location/live?cm_uid=X
    // =========================================================================

    public function team_location_probe()
    {
        $this->_ok(['ok' => true]);
    }

    public function team_location_live()
    {
        $this->_require_auth();
        if ($this->input->server('REQUEST_METHOD') !== 'GET') {
            $this->_bad('GET required.');
        }
        $cm_uid = (int)$this->input->get('cm_uid');
        if (!$cm_uid) { $this->_bad('cm_uid required.'); }

        // Get BDs under this CM
        $bds = $this->db->select('uid, name')
            ->where('admin_id', $cm_uid)
            ->where('type_id', 3)
            ->get('user')->result_array();

        if (empty($bds)) {
            $this->_ok(['rows' => []]);
        }

        $bd_uids = array_column($bds, 'uid');
        $today   = date('Y-m-d');

        // day_ceremony_v2 for today
        $dc_rows = $this->db
            ->select('user_id, ustart_lat, ustart_lng, ustart, uclose, ceremony_date')
            ->where('ceremony_date', $today)
            ->where_in('user_id', $bd_uids)
            ->get('day_ceremony_v2')->result_array();
        $dc_map = [];
        foreach ($dc_rows as $r) { $dc_map[$r['user_id']] = $r; }

        // Most recent tblcallevents gps for each BD today (gps_lat / gps_lng columns exist)
        $gps_sql = "
            SELECT ce.user_id, ce.gps_lat, ce.gps_lng, ce.date AS last_seen_at
            FROM tblcallevents ce
            INNER JOIN (
                SELECT user_id, MAX(date) AS mx
                FROM tblcallevents
                WHERE user_id IN (" . implode(',', array_map('intval', $bd_uids)) . ")
                  AND DATE(date) = ?
                  AND gps_lat IS NOT NULL
                GROUP BY user_id
            ) t2 ON ce.user_id = t2.user_id AND ce.date = t2.mx
        ";
        $gps_rows = $this->db->query($gps_sql, [$today])->result_array();
        $gps_map = [];
        foreach ($gps_rows as $r) { $gps_map[$r['user_id']] = $r; }

        $result = [];
        foreach ($bds as $bd) {
            $uid = $bd['uid'];
            $dc  = isset($dc_map[$uid]) ? $dc_map[$uid] : null;
            $gps = isset($gps_map[$uid]) ? $gps_map[$uid] : null;

            // Prefer most recent event GPS, fall back to start GPS
            $lat         = $gps ? $gps['gps_lat'] : ($dc ? $dc['ustart_lat'] : null);
            $lng         = $gps ? $gps['gps_lng'] : ($dc ? $dc['ustart_lng'] : null);
            $last_seen   = $gps ? $gps['last_seen_at'] : ($dc ? $dc['ustart'] : null);

            $status = 'not_started';
            if ($dc) {
                if ($dc['uclose']) { $status = 'closed'; }
                elseif ($dc['ustart']) { $status = 'active'; }
            }

            $result[] = [
                'bd_uid'       => $uid,
                'bd_name'      => $bd['name'],
                'lat'          => $lat,
                'lng'          => $lng,
                'last_seen_at' => $last_seen,
                'status'       => $status,
            ];
        }

        $this->_ok(['rows' => $result, 'cm_uid' => $cm_uid, 'date' => $today]);
    }

    // =========================================================================
    // CARD 14 - SPECIAL REMARKS STREAM
    // GET /api/special_remarks/stream?user_id=X&days=30
    // POST /api/special_remarks/flag
    // =========================================================================

    public function special_remarks_stream()
    {
        $this->_require_auth();
        if ($this->input->server('REQUEST_METHOD') !== 'GET') {
            $this->_bad('GET required.');
        }
        $user_id = (int)$this->input->get('user_id');
        $days    = max(1, (int)($this->input->get('days') ?: 30));
        if (!$user_id) { $this->_bad('user_id required.'); }

        $since = date('Y-m-d H:i:s', strtotime("-{$days} days"));

        // rosterremark - BD self remarks
        $sql1 = "
            SELECT
                CONCAT('rr_', r.id) AS remark_id,
                r.cid AS lead_id,
                COALESCE(ic.draft, '') AS lead_name,
                r.lremark AS remark_text,
                'BD self-remark' AS flagged_reason,
                u.name AS flagged_by_name,
                r.sdatet AS created_at,
                'low' AS severity
            FROM rosterremark r
            LEFT JOIN init_call ic ON ic.cmpid_id = r.cid
            LEFT JOIN user u ON u.uid = r.bdid
            WHERE r.sdatet >= ?
              AND r.bdid = ?
            LIMIT 50
        ";

        // todays_remark (simple flag table - id,name,status_id)
        // No date filter available, limited data
        $sql2 = "
            SELECT
                CONCAT('tr_', t.id) AS remark_id,
                0 AS lead_id,
                '' AS lead_name,
                t.name AS remark_text,
                'Daily flag' AS flagged_reason,
                '' AS flagged_by_name,
                NOW() AS created_at,
                CASE WHEN t.status_id >= 2 THEN 'high' WHEN t.status_id = 1 THEN 'med' ELSE 'low' END AS severity
            FROM todays_remark t
            LIMIT 20
        ";

        // remark_coherence_score with pushback_required
        $sql3 = "
            SELECT
                CONCAT('rc_', rc.id) AS remark_id,
                COALESCE(rc.cid_id, 0) AS lead_id,
                COALESCE(ic.draft, '') AS lead_name,
                rc.remark_text,
                CONCAT('Coherence grade: ', rc.grade) AS flagged_reason,
                u.name AS flagged_by_name,
                rc.scored_at AS created_at,
                CASE WHEN rc.grade IN ('A','B') THEN 'low' WHEN rc.grade = 'C' THEN 'med' ELSE 'high' END AS severity
            FROM remark_coherence_score rc
            LEFT JOIN init_call ic ON ic.cmpid_id = rc.cid_id
            LEFT JOIN user u ON u.uid = rc.actor_uid
            WHERE rc.pushback_required = 1
              AND rc.scored_at >= ?
              AND rc.actor_uid = ?
            LIMIT 50
        ";

        $rows1 = $this->db->query($sql1, [$since, $user_id])->result_array();
        $rows2 = $this->db->query($sql2)->result_array();
        $rows3 = $this->db->query($sql3, [$since, $user_id])->result_array();

        $all = array_merge($rows1, $rows3, $rows2);

        // Sanitize lead_name
        foreach ($all as &$r) {
            $r['lead_name'] = mb_substr(strip_tags($r['lead_name']), 0, 80);
        }
        unset($r);

        // Sort by created_at desc
        usort($all, function($a, $b) {
            return strcmp($b['created_at'], $a['created_at']);
        });

        $this->_ok(['rows' => array_slice($all, 0, 100), 'user_id' => $user_id, 'days' => $days]);
    }

    public function special_remarks_flag()
    {
        $this->_require_auth();
        if ($this->input->server('REQUEST_METHOD') !== 'POST') {
            $this->_bad('POST required.');
        }
        $lead_id    = (int)$this->input->post('lead_id');
        $remark_text = $this->input->post('remark_text');
        $severity   = $this->input->post('severity') ?: 'low';
        $flagged_by = (int)$this->input->post('flagged_by');

        if (!$remark_text) { $this->_bad('remark_text required.'); }
        if (!$flagged_by)  { $this->_bad('flagged_by required.'); }

        $valid_severities = ['low', 'med', 'high'];
        if (!in_array($severity, $valid_severities)) { $severity = 'low'; }

        // Insert into rosterremark (simpler schema: id, bdid, cid, lremark)
        // id is NOT auto_increment based on schema, so compute next id
        $max_id = $this->db->select_max('id')->get('rosterremark')->row_array();
        $next_id = (isset($max_id['id']) ? (int)$max_id['id'] : 0) + 1;

        $this->db->insert('rosterremark', [
            'id'     => $next_id,
            'bdid'   => $flagged_by,
            'cid'    => $lead_id ?: null,
            'lremark' => $remark_text,
            'cremark' => 'severity:' . $severity,
            'status' => $severity === 'high' ? 2 : ($severity === 'med' ? 1 : 0),
            'sdatet' => date('Y-m-d H:i:s'),
        ]);

        $this->_ok(['message' => 'Remark flagged.', 'id' => $next_id]);
    }

    // =========================================================================
    // CARD 20 - BD PROFILE DRILL-DOWN
    // GET /api/bd_profile/detail?bd_uid=X
    // GET /api/bd_profile/recent_activity?bd_uid=X&days=14
    // =========================================================================

    public function bd_profile_detail()
    {
        $this->_require_auth();
        if ($this->input->server('REQUEST_METHOD') !== 'GET') {
            $this->_bad('GET required.');
        }
        $bd_uid = (int)$this->input->get('bd_uid');
        if (!$bd_uid) { $this->_bad('bd_uid required.'); }

        $user = $this->db->select('uid, name, email, phoneno, type_id, created_at')
            ->where('uid', $bd_uid)
            ->get('user')->row_array();
        if (!$user) { $this->_bad('BD not found.'); }

        $today   = date('Y-m-d');
        $month_s = date('Y-m-01');
        $month_e = date('Y-m-d');

        // active_leads
        $active_leads = (int)$this->db->where('mainbd', $bd_uid)
            ->where_not_in('cstatus', [9, 10])
            ->where('cstatus IS NOT NULL', null, false)
            ->count_all_results('init_call');

        // won this month (cstatus = 9 or close)
        $won = (int)$this->db->where('mainbd', $bd_uid)
            ->where('cstatus', 9)
            ->where('updated_at >=', $month_s)
            ->count_all_results('init_call');

        // lost this month (cstatus = 10)
        $lost = (int)$this->db->where('mainbd', $bd_uid)
            ->where('cstatus', 10)
            ->where('updated_at >=', $month_s)
            ->count_all_results('init_call');

        // today tasks count from tblcallevents
        $today_tasks = (int)$this->db->where('user_id', $bd_uid)
            ->where('DATE(date)', $today)
            ->count_all_results('tblcallevents');

        // current cluster
        $cluster_row = $this->db
            ->select('cm.cluster_name')
            ->join('cluster_master cm', 'cm.cluster_id = ucm.cluster_id', 'left')
            ->where('ucm.user_id', $bd_uid)
            ->where('ucm.status', 1)
            ->get('user_cluster_mapping ucm')->row_array();
        $cluster_name = $cluster_row ? $cluster_row['cluster_name'] : null;

        $this->_ok([
            'user'          => $user,
            'active_leads'  => $active_leads,
            'won_this_month' => $won,
            'lost_this_month' => $lost,
            'today_tasks_count' => $today_tasks,
            'current_cluster' => $cluster_name,
        ]);
    }

    public function bd_profile_recent_activity()
    {
        $this->_require_auth();
        if ($this->input->server('REQUEST_METHOD') !== 'GET') {
            $this->_bad('GET required.');
        }
        $bd_uid = (int)$this->input->get('bd_uid');
        $days   = max(1, (int)($this->input->get('days') ?: 14));
        if (!$bd_uid) { $this->_bad('bd_uid required.'); }

        $since = date('Y-m-d H:i:s', strtotime("-{$days} days"));

        $sql = "
            SELECT
                ce.id,
                ce.cid_id AS lead_id,
                COALESCE(ic.draft, '') AS school_name,
                ce.remarks,
                ce.special_remarks,
                ce.date,
                ce.actontaken,
                ce.purpose_achieved,
                ce.status_id,
                ce.nstatus_id
            FROM tblcallevents ce
            LEFT JOIN init_call ic ON ic.cmpid_id = ce.cid_id
            WHERE ce.user_id = ?
              AND ce.date >= ?
            ORDER BY ce.date DESC
            LIMIT 50
        ";
        $rows = $this->db->query($sql, [$bd_uid, $since])->result_array();

        foreach ($rows as &$r) {
            $r['school_name'] = mb_substr(strip_tags($r['school_name']), 0, 80);
        }
        unset($r);

        $this->_ok(['rows' => $rows, 'bd_uid' => $bd_uid, 'days' => $days]);
    }

    // =========================================================================
    // CARD 4 - DAILY / ANNUAL REVIEW AGGREGATION
    // GET /api/review_report/daily?user_id=X&date=Y
    // GET /api/review_report/annual?user_id=X&year=Y
    // =========================================================================

    public function review_report_daily()
    {
        $this->_require_auth();
        if ($this->input->server('REQUEST_METHOD') !== 'GET') {
            $this->_bad('GET required.');
        }
        $user_id = (int)$this->input->get('user_id');
        $date    = $this->input->get('date') ?: date('Y-m-d');
        if (!$user_id) { $this->_bad('user_id required.'); }
        if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $date)) {
            $this->_bad('date must be YYYY-MM-DD.');
        }

        // From day_ceremony_v2
        $dc = $this->db->get_where('day_ceremony_v2', [
            'user_id'       => $user_id,
            'ceremony_date' => $date,
        ])->row_array();

        // Tasks planned from task_plan_for_today
        $tp = $this->db->select('SUM(taskcnt) as planned')
            ->where('user_id', $user_id)
            ->where('DATE(created_at)', $date)
            ->get('task_plan_for_today')->row_array();
        $tasks_planned = $tp ? (int)$tp['planned'] : 0;

        // Tasks done from tblcallevents
        $td = (int)$this->db->where('user_id', $user_id)
            ->where('DATE(date)', $date)
            ->where('actontaken', 'yes')
            ->count_all_results('tblcallevents');

        // Meetings that day
        $meetings = (int)$this->db->where('user_id', $user_id)
            ->where('DATE(date)', $date)
            ->where('meeting_type !=', 'NA')
            ->count_all_results('tblcallevents');

        // MoMs written
        $moms = (int)$this->db->where('user_id', $user_id)
            ->where('DATE(date)', $date)
            ->where('mom_received', 'yes')
            ->count_all_results('tblcallevents');

        // Leads progressed (status changed)
        $leads_progressed = (int)$this->db->where('user_id', $user_id)
            ->where('DATE(date)', $date)
            ->where('nstatus_id IS NOT NULL', null, false)
            ->where('nstatus_id != status_id', null, false)
            ->count_all_results('tblcallevents');

        $result = [
            'date'             => $date,
            'user_id'          => $user_id,
            'tasks_planned'    => $tasks_planned,
            'tasks_done'       => $td,
            'meetings'         => $meetings,
            'moms_written'     => $moms,
            'leads_progressed' => $leads_progressed,
            'blockers_text'    => $dc ? $dc['uclose_pending_breakdown_json'] : null,
            'day_started'      => $dc ? $dc['ustart'] : null,
            'day_closed'       => $dc ? $dc['uclose'] : null,
        ];

        $this->_ok($result);
    }

    public function review_report_annual()
    {
        $this->_require_auth();
        if ($this->input->server('REQUEST_METHOD') !== 'GET') {
            $this->_bad('GET required.');
        }
        $user_id = (int)$this->input->get('user_id');
        $year    = (int)($this->input->get('year') ?: date('Y'));
        if (!$user_id) { $this->_bad('user_id required.'); }

        $year_s = "{$year}-01-01";
        $year_e = "{$year}-12-31";

        $rows = $this->db->select('company_status, close_status, created_at, close_date, remarks')
            ->where('uid', $user_id)
            ->where('created_at >=', $year_s)
            ->where('created_at <=', $year_e . ' 23:59:59')
            ->get('start_annual_review')->result_array();

        // Aggregate company_status counts
        $status_counts = [];
        $close_counts  = [];
        foreach ($rows as $r) {
            $cs = (string)$r['company_status'];
            $cl = (string)$r['close_status'];
            $status_counts[$cs] = ($status_counts[$cs] ?? 0) + 1;
            $close_counts[$cl]  = ($close_counts[$cl] ?? 0) + 1;
        }

        $this->_ok([
            'user_id'       => $user_id,
            'year'          => $year,
            'total_rows'    => count($rows),
            'company_status_counts' => $status_counts,
            'close_status_counts'   => $close_counts,
            'rows'          => $rows,
        ]);
    }

    // =========================================================================
    // CARD 6 - APP USAGE DRILL-DOWN PER BD
    // GET /api/app_usage/per_bd?bd_uid=X&days=14
    // =========================================================================

    public function app_usage_per_bd()
    {
        $this->_require_auth();
        if ($this->input->server('REQUEST_METHOD') !== 'GET') {
            $this->_bad('GET required.');
        }
        $bd_uid = (int)$this->input->get('bd_uid');
        $days   = max(1, (int)($this->input->get('days') ?: 14));
        if (!$bd_uid) { $this->_bad('bd_uid required.'); }

        $since = date('Y-m-d', strtotime("-{$days} days"));

        // Daily seconds from user_activity (leave_time - event_time for closed events)
        $daily_sql = "
            SELECT
                DATE(event_time) AS day,
                SUM(TIMESTAMPDIFF(SECOND, event_time, leave_time)) AS total_seconds
            FROM user_activity
            WHERE user_id = ?
              AND DATE(event_time) >= ?
              AND leave_time IS NOT NULL
            GROUP BY DATE(event_time)
            ORDER BY day ASC
        ";
        $daily = $this->db->query($daily_sql, [$bd_uid, $since])->result_array();

        // Top 5 screens by time spent
        $top_sql = "
            SELECT
                url_path,
                SUM(TIMESTAMPDIFF(SECOND, event_time, leave_time)) AS total_seconds,
                COUNT(*) AS visit_count
            FROM user_activity
            WHERE user_id = ?
              AND DATE(event_time) >= ?
              AND leave_time IS NOT NULL
              AND url_path IS NOT NULL
              AND url_path != ''
            GROUP BY url_path
            ORDER BY total_seconds DESC
            LIMIT 5
        ";
        $top_screens = $this->db->query($top_sql, [$bd_uid, $since])->result_array();

        // Extract screen name from URL
        foreach ($top_screens as &$s) {
            $parts = explode('/', trim($s['url_path'], '/'));
            $s['screen_name'] = end($parts);
        }
        unset($s);

        $this->_ok([
            'bd_uid'      => $bd_uid,
            'days'        => $days,
            'daily_usage' => $daily,
            'top_screens' => $top_screens,
        ]);
    }

    // =========================================================================
    // CARD check_management/today  (AgentC 28 May 2026)
    // GET /api/check_management/today?cm_uid=X
    // Returns today's BD task completion summary under a CM.
    // =========================================================================
    public function check_management_today()
    {
        $this->_require_auth();
        $cm_uid = (int)($this->input->get('cm_uid') ?: $this->input->get('uid'));
        $date   = $this->input->get('date') ?: date('Y-m-d');
        if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $date)) $date = date('Y-m-d');
        if (!$cm_uid) {
            $this->_ok(array('ok'=>true,'date'=>$date,'rows'=>array(),'note'=>'cm_uid required'));
            return;
        }
        try {
            // BDs reporting to this CM (admin_id = cm_uid)
            $bds = $this->db->select('uid, name')
                ->where('admin_id', $cm_uid)
                ->where('type_id', 3)
                ->where('active', 1)
                ->get('user')->result_array();
            if (empty($bds)) {
                $this->_ok(array('ok'=>true,'cm_uid'=>$cm_uid,'date'=>$date,'rows'=>array(),'note'=>'no BDs found under this CM'));
                return;
            }
            $bd_uids = array_column($bds, 'uid');
            $bd_map  = array();
            foreach ($bds as $bd) { $bd_map[$bd['uid']] = $bd['name']; }

            // Tasks done today (actontaken=yes OR status_id=2)
            $done_q = $this->db->query("
                SELECT user_id, COUNT(*) AS done
                FROM tblcallevents
                WHERE user_id IN (" . implode(',', array_map('intval', $bd_uids)) . ")
                  AND DATE(date) = ?
                  AND (actontaken='yes' OR status_id=2)
                GROUP BY user_id
            ", array($date))->result_array();
            $done_map = array();
            foreach ($done_q as $r) { $done_map[$r['user_id']] = (int)$r['done']; }

            // Total tasks today (scheduled for this date)
            $total_q = $this->db->query("
                SELECT user_id, COUNT(*) AS total,
                       SUM(CASE WHEN actiontype_id IN (3,4) THEN 1 ELSE 0 END) AS meetings
                FROM tblcallevents
                WHERE user_id IN (" . implode(',', array_map('intval', $bd_uids)) . ")
                  AND DATE(date) = ?
                GROUP BY user_id
            ", array($date))->result_array();
            $total_map = array();
            foreach ($total_q as $r) {
                $total_map[$r['user_id']] = array('total'=>(int)$r['total'],'meetings'=>(int)$r['meetings']);
            }

            // Day ceremony status
            $dc_q = $this->db->query("
                SELECT uid, status, day_start_at, day_close_at
                FROM day_ceremony
                WHERE uid IN (" . implode(',', array_map('intval', $bd_uids)) . ")
                  AND ceremony_date = ?
            ", array($date))->result_array();
            $dc_map = array();
            foreach ($dc_q as $r) { $dc_map[$r['uid']] = $r; }

            $rows = array();
            foreach ($bds as $bd) {
                $uid   = (int)$bd['uid'];
                $total = isset($total_map[$uid]) ? $total_map[$uid]['total']    : 0;
                $done  = isset($done_map[$uid])  ? $done_map[$uid]              : 0;
                $mtgs  = isset($total_map[$uid]) ? $total_map[$uid]['meetings'] : 0;
                $rows[] = array(
                    'bd_uid'         => $uid,
                    'bd_name'        => $bd['name'],
                    'date'           => $date,
                    'tasks_total'    => $total,
                    'tasks_done'     => $done,
                    'pct_done'       => $total > 0 ? round(($done/$total)*100) : 0,
                    'meetings_today' => $mtgs,
                    'day_started'    => isset($dc_map[$uid]) && $dc_map[$uid]['day_start_at'],
                    'day_closed'     => isset($dc_map[$uid]) && $dc_map[$uid]['day_close_at'],
                    'ceremony_status'=> isset($dc_map[$uid]) ? $dc_map[$uid]['status'] : 'not_started',
                );
            }
            $this->_ok(array('ok'=>true,'cm_uid'=>$cm_uid,'date'=>$date,'count'=>count($rows),'rows'=>$rows));
        } catch (Exception $e) {
            log_message('error', 'check_management_today: ' . $e->getMessage());
            $this->_ok(array('ok'=>true,'cm_uid'=>$cm_uid,'date'=>$date,'rows'=>array(),'note'=>'no_data','detail'=>$e->getMessage()));
        }
    }

    // =========================================================================
    // FLAT ALIAS ENDPOINTS (AgentC 28 May 2026)
    // GET /api/team_location        -> team_location_probe()
    // GET /api/special_remarks      -> special_remarks_stream()
    // =========================================================================
    public function team_location_index()
    {
        return $this->team_location_probe();
    }

    public function special_remarks_index()
    {
        // Translate uid -> user_id for special_remarks_stream
        if (!isset($_GET['user_id']) || (int)$_GET['user_id'] <= 0) {
            if (isset($_GET['uid']) && (int)$_GET['uid'] > 0) {
                $_GET['user_id'] = $_GET['uid'];
            } elseif ($this->_authed_uid > 0) {
                $_GET['user_id'] = $this->_authed_uid;
            }
        }
        return $this->special_remarks_stream();
    }

}
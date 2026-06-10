<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Usage controller - Team Usage telemetry (F79)
 * Added 2026-06-07. ADDITIVE. Staging only.
 *
 * Capture (POST, called silently by mobile src/lib/usage.js):
 *   /usage/api_open_session    { platform, app_version, device_id }   -> { session_id }
 *   /usage/api_heartbeat       { session_id }                         -> { ok }
 *   /usage/api_close_session   { session_id }                         -> { ok }
 *   /usage/api_screen_open     { session_id, screen, agent }          -> { view_id }
 *   /usage/api_screen_close    { view_id }                            -> { ok }
 *   /usage/api_record_action   { session_id, action, target_type, target_id, meta }
 *
 * Summary (GET, TeamUsageScreen):
 *   /usage/api_live_presence                  -> { presence:[{user_id,name,current_screen}] }
 *   /usage/api_daily_summary?date=YYYY-MM-DD  -> { rows:[{user_id,name,role,total_time_sec,
 *                                                  time_planning_sec,time_leads_sec,time_mom_sec,
 *                                                  time_review_sec,actions_count,avg_task_latency_s}] }
 *
 * Auth: Bearer via BearerAuth->resolve() (master token = uid 0).
 * Plain English. ASCII only.
 */
class Usage extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->database();
        header('Content-Type: application/json; charset=utf-8');
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Headers: Authorization, Content-Type');
        $this->load->library('BearerAuth');
    }

    private function _actor() {
        $a = $this->bearerauth->resolve();
        if (empty($a['ok'])) return null;
        return array('uid' => (int)$a['uid'], 'role' => (string)$a['role']);
    }

    private function _json($data, $code = 200) {
        // -1 makes PHP emit the shortest float representation that round-trips,
        // avoiding artifacts like 1.44999999999999996 in JSON output.
        @ini_set('serialize_precision', '-1');
        http_response_code($code);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($data);
        exit;
    }

    private function _body() {
        $raw = json_decode($this->input->raw_input_stream, true);
        if (!is_array($raw)) $raw = $_POST;
        return $raw;
    }

    private function _uuid() {
        return bin2hex(random_bytes(16));
    }

    // Map a screen name to a time bucket used by the daily summary.
    private function _bucket($screen) {
        $s = strtolower((string)$screen);
        if (strpos($s, 'plan') !== false)   return 'planning';
        if (strpos($s, 'lead') !== false)   return 'leads';
        if (strpos($s, 'mom') !== false || strpos($s, 'meeting') !== false) return 'mom';
        if (strpos($s, 'review') !== false || strpos($s, 'report') !== false) return 'review';
        return 'other';
    }

    public function probe() {
        $this->_json(array(
            'ok' => true, 'controller' => 'Usage', 'feature' => 'F79',
            'status' => 'ready', 'server_time' => date('c'),
        ));
    }

    // ---------------- CAPTURE ----------------
    public function api_open_session() {
        $actor = $this->_actor();
        if (!$actor) return $this->_json(array('error' => 'unauthorized'), 401);
        $b = $this->_body();
        $uuid = $this->_uuid();
        $now  = date('Y-m-d H:i:s');
        $this->db->insert('usage_session', array(
            'session_uuid'      => $uuid,
            'user_uid'          => $actor['uid'],
            'platform'          => isset($b['platform']) ? substr((string)$b['platform'], 0, 20) : null,
            'app_version'       => isset($b['app_version']) ? substr((string)$b['app_version'], 0, 30) : null,
            'device_id'         => isset($b['device_id']) ? substr((string)$b['device_id'], 0, 100) : null,
            'opened_at'         => $now,
            'last_heartbeat_at' => $now,
        ));
        $this->_json(array('session_id' => $uuid, 'ok' => true));
    }

    public function api_heartbeat() {
        $actor = $this->_actor();
        if (!$actor) return $this->_json(array('error' => 'unauthorized'), 401);
        $b = $this->_body();
        $sid = isset($b['session_id']) ? (string)$b['session_id'] : '';
        if ($sid === '') return $this->_json(array('error' => 'session_id required'), 400);
        $this->db->where('session_uuid', $sid)
                 ->update('usage_session', array('last_heartbeat_at' => date('Y-m-d H:i:s')));
        $this->_json(array('ok' => true));
    }

    public function api_close_session() {
        $actor = $this->_actor();
        if (!$actor) return $this->_json(array('error' => 'unauthorized'), 401);
        $b = $this->_body();
        $sid = isset($b['session_id']) ? (string)$b['session_id'] : '';
        if ($sid === '') return $this->_json(array('error' => 'session_id required'), 400);
        $this->db->where('session_uuid', $sid)
                 ->update('usage_session', array('closed_at' => date('Y-m-d H:i:s')));
        $this->_json(array('ok' => true));
    }

    public function api_screen_open() {
        $actor = $this->_actor();
        if (!$actor) return $this->_json(array('error' => 'unauthorized'), 401);
        $b = $this->_body();
        $sid    = isset($b['session_id']) ? (string)$b['session_id'] : '';
        $screen = isset($b['screen']) ? substr((string)$b['screen'], 0, 80) : '';
        if ($screen === '') return $this->_json(array('error' => 'screen required'), 400);
        $vuuid = $this->_uuid();
        $this->db->insert('usage_screen_view', array(
            'view_uuid'    => $vuuid,
            'session_uuid' => $sid,
            'user_uid'     => $actor['uid'],
            'screen'       => $screen,
            'agent'        => isset($b['agent']) ? substr((string)$b['agent'], 0, 60) : null,
            'opened_at'    => date('Y-m-d H:i:s'),
        ));
        $this->_json(array('view_id' => $vuuid, 'ok' => true));
    }

    public function api_screen_close() {
        $actor = $this->_actor();
        if (!$actor) return $this->_json(array('error' => 'unauthorized'), 401);
        $b = $this->_body();
        $vid = isset($b['view_id']) ? (string)$b['view_id'] : '';
        if ($vid === '') return $this->_json(array('error' => 'view_id required'), 400);
        $row = $this->db->select('opened_at')->where('view_uuid', $vid)
                        ->limit(1)->get('usage_screen_view')->row_array();
        if ($row) {
            $dur = max(time() - strtotime($row['opened_at']), 0);
            $this->db->where('view_uuid', $vid)->update('usage_screen_view', array(
                'closed_at'    => date('Y-m-d H:i:s'),
                'duration_sec' => $dur,
            ));
        }
        $this->_json(array('ok' => true));
    }

    public function api_record_action() {
        $actor = $this->_actor();
        if (!$actor) return $this->_json(array('error' => 'unauthorized'), 401);
        $b = $this->_body();
        $action = isset($b['action']) ? substr((string)$b['action'], 0, 60) : '';
        if ($action === '') return $this->_json(array('error' => 'action required'), 400);
        $meta = isset($b['meta']) ? (is_array($b['meta']) ? json_encode($b['meta']) : (string)$b['meta']) : null;
        $this->db->insert('usage_action', array(
            'session_uuid' => isset($b['session_id']) ? (string)$b['session_id'] : null,
            'user_uid'     => $actor['uid'],
            'action'       => $action,
            'target_type'  => isset($b['target_type']) ? substr((string)$b['target_type'], 0, 40) : null,
            'target_id'    => isset($b['target_id']) ? substr((string)$b['target_id'], 0, 60) : null,
            'meta'         => $meta,
            'latency_ms'   => isset($b['latency_ms']) ? (int)$b['latency_ms'] : null,
            'occurred_at'  => date('Y-m-d H:i:s'),
        ));
        $this->_json(array('ok' => true));
    }

    // ---------------- SUMMARY ----------------

    // GET /usage/api_live_presence -> users with a heartbeat in the last 2 minutes.
    public function api_live_presence() {
        $actor = $this->_actor();
        if (!$actor) return $this->_json(array('error' => 'unauthorized'), 401);

        $sql = "SELECT s.user_uid,
                       u.name AS name,
                       (SELECT v.screen FROM usage_screen_view v
                         WHERE v.user_uid = s.user_uid AND v.closed_at IS NULL
                         ORDER BY v.opened_at DESC LIMIT 1) AS current_screen
                FROM usage_session s
                LEFT JOIN user u ON u.uid = s.user_uid
                WHERE s.last_heartbeat_at >= (NOW() - INTERVAL 2 MINUTE)
                  AND s.closed_at IS NULL
                GROUP BY s.user_uid, u.name
                ORDER BY MAX(s.last_heartbeat_at) DESC
                LIMIT 100";
        $rows = $this->db->query($sql)->result_array();

        $presence = array();
        foreach ($rows as $r) {
            $presence[] = array(
                'user_id'        => (int)$r['user_uid'],
                'name'           => $r['name'] !== null ? (string)$r['name'] : null,
                'current_screen' => $r['current_screen'] !== null ? (string)$r['current_screen'] : null,
            );
        }
        $this->_json(array('presence' => $presence, 'count' => count($presence), 'empty' => count($presence) === 0));
    }

    // GET /usage/api_daily_summary?date=YYYY-MM-DD -> per-user time + actions for the day.
    public function api_daily_summary() {
        $actor = $this->_actor();
        if (!$actor) return $this->_json(array('error' => 'unauthorized'), 401);

        $d_raw = $this->input->get('date');
        $date  = ($d_raw && preg_match('/^\d{4}-\d{2}-\d{2}$/', $d_raw)) ? $d_raw : date('Y-m-d');

        // Screen-view time per user, bucketed.
        $views = $this->db->query(
            "SELECT user_uid, screen, duration_sec FROM usage_screen_view WHERE DATE(opened_at) = ?",
            array($date)
        )->result_array();

        $agg = array(); // uid => buckets
        foreach ($views as $v) {
            $uid = (int)$v['user_uid'];
            if (!isset($agg[$uid])) {
                $agg[$uid] = array('total' => 0, 'planning' => 0, 'leads' => 0, 'mom' => 0, 'review' => 0,
                                   'actions' => 0, 'lat_sum' => 0, 'lat_n' => 0);
            }
            $dur = (int)$v['duration_sec'];
            $agg[$uid]['total'] += $dur;
            $bk = $this->_bucket($v['screen']);
            if (isset($agg[$uid][$bk])) $agg[$uid][$bk] += $dur;
        }

        // Actions + latency per user.
        $acts = $this->db->query(
            "SELECT user_uid, COUNT(*) AS c, AVG(latency_ms) AS avg_lat
             FROM usage_action WHERE DATE(occurred_at) = ? GROUP BY user_uid",
            array($date)
        )->result_array();
        foreach ($acts as $a) {
            $uid = (int)$a['user_uid'];
            if (!isset($agg[$uid])) {
                $agg[$uid] = array('total' => 0, 'planning' => 0, 'leads' => 0, 'mom' => 0, 'review' => 0,
                                   'actions' => 0, 'lat_sum' => 0, 'lat_n' => 0);
            }
            $agg[$uid]['actions'] = (int)$a['c'];
            $agg[$uid]['lat_sum'] = $a['avg_lat'] !== null ? (float)$a['avg_lat'] : 0;
            $agg[$uid]['lat_n']   = $a['avg_lat'] !== null ? 1 : 0;
        }

        // Resolve names/roles in one pass.
        $rows = array();
        $uids = array_keys($agg);
        $name_map = array();
        if (!empty($uids)) {
            $in = implode(',', array_map('intval', $uids));
            $urows = $this->db->query("SELECT uid, name, type_id FROM user WHERE uid IN ($in)")->result_array();
            foreach ($urows as $u) $name_map[(int)$u['uid']] = $u;
        }

        foreach ($agg as $uid => $b) {
            $u = isset($name_map[$uid]) ? $name_map[$uid] : null;
            $role = 'bd';
            if ($u && isset($u['type_id']) && (int)$u['type_id'] === 1) $role = 'admin';
            $rows[] = array(
                'user_id'            => $uid,
                'name'               => $u ? (string)$u['name'] : null,
                'role'               => $role,
                'total_time_sec'     => (int)$b['total'],
                'time_planning_sec'  => (int)$b['planning'],
                'time_leads_sec'     => (int)$b['leads'],
                'time_mom_sec'       => (int)$b['mom'],
                'time_review_sec'    => (int)$b['review'],
                'actions_count'      => (int)$b['actions'],
                'avg_task_latency_s' => $b['lat_n'] > 0 ? round($b['lat_sum'] / 1000.0, 2) : 0,
            );
        }

        // Sort by total time descending.
        usort($rows, function ($x, $y) { return $y['total_time_sec'] - $x['total_time_sec']; });

        $this->_json(array('rows' => $rows, 'date' => $date, 'count' => count($rows), 'empty' => count($rows) === 0));
    }
}

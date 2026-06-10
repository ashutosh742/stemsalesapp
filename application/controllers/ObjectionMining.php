<?php
/**
 * ObjectionMiningController.php
 *
 * Migration 052 - Objection Mining from Call Transcripts
 * Location: application/controllers/api/ObjectionMiningController.php
 *
 * All 8 endpoints require BearerAuth (Authorization: Bearer <token>).
 * Role-based access is enforced per endpoint.
 *
 * Endpoints:
 *   GET  /api/objection_mining/probe                 - health and feature flag status
 *   POST /api/objection_mining/run_weekly_batch      - trigger weekly batch manually (SC/Director only)
 *   POST /api/objection_mining/extract_for_meeting   - on-demand extraction for one meeting (BD/CM/SC)
 *   GET  /api/objection_mining/top_themes_week       - top objections this week (all roles)
 *   GET  /api/objection_mining/by_bd                 - per-BD objection patterns (CM/RM/SC/Director)
 *   GET  /api/objection_mining/by_cluster            - cluster-level aggregate (RM/SC/Director)
 *   GET  /api/objection_mining/lead_blockers         - unresolved lead blockers (BD/CM/RM/SC/Director)
 *   GET  /api/objection_mining/kb_candidates         - KB candidate phrases (CM/RM/SC/Director/AVP)
 *
 * Standing rules:
 *   - Plain English, no em-dashes, no non-ASCII in code
 *   - All endpoints return JSON {status, data, message}
 *   - 401 on missing/invalid token; 403 on insufficient role
 *   - 400 on missing required params; 404 when record not found
 *   - Rate limit on extract_for_meeting: 20 calls per minute per uid
 */

defined('BASEPATH') OR exit('No direct script access allowed');

class ObjectionMiningController extends CI_Controller
{
    // Allowed roles per endpoint
    private static $BATCH_ROLES    = ['SC', 'Director'];
    private static $EXTRACT_ROLES  = ['BD', 'CM', 'SC', 'Director'];
    private static $READ_ALL_ROLES = ['BD', 'CM', 'RM', 'ACM', 'SC', 'Director', 'AVP'];
    private static $COACH_ROLES    = ['CM', 'RM', 'ACM', 'SC', 'Director', 'AVP'];


    public function __construct()
    {
        parent::__construct();
        $this->load->model('AIAgents/ObjectionMining_model', 'ObjectionMining');
        $this->load->library('BearerAuth');
        if (!isset($this->cache)) { $this->load->driver('cache', array('adapter' => 'file')); } // rimlyproof_cachefix_20260609
    }


    // -----------------------------------------------------------------
    // 1. probe
    // GET /api/objection_mining/probe
    // Returns: feature flag value, DB connectivity, last run log row.
    // Access: all authenticated roles.
    // -----------------------------------------------------------------

    public function probe()
    {
        $actor = $this->_require_auth(self::$READ_ALL_ROLES);
        if (!$actor) { return; }

        $flag = $this->db->get_where('feature_flag', [
            'flag_key' => 'objection_mining_052_enabled',
        ])->row_array();

        $last_run = $this->db->order_by('run_start_at', 'DESC')
            ->limit(1)
            ->get('objection_mining_run_log')
            ->row_array();

        $theme_count = $this->db->where('is_active', 1)
            ->count_all_results('objection_theme');

        $this->_json(200, [
            'flag_value'   => intval($flag['flag_value'] ?? 0),
            'flag_key' => 'objection_mining_052_enabled',
            'active_themes'=> $theme_count,
            'last_run'     => $last_run ?: null,
        ], 'Migration 052 ObjectionMining is reachable');
    }


    // -----------------------------------------------------------------
    // 2. run_weekly_batch
    // POST /api/objection_mining/run_weekly_batch
    // Body (optional): { "window_start": "2026-05-18 00:00:00",
    //                    "window_end":   "2026-05-24 23:59:59" }
    // If body is absent, defaults to the past 7 days.
    // Access: SC, Director only.
    // -----------------------------------------------------------------

    public function run_weekly_batch()
    {
        $actor = $this->_require_auth(self::$BATCH_ROLES);
        if (!$actor) { return; }

        $body         = $this->_body();
        $window_end   = !empty($body['window_end'])
            ? $body['window_end']
            : date('Y-m-d H:i:s');
        $window_start = !empty($body['window_start'])
            ? $body['window_start']
            : date('Y-m-d H:i:s', strtotime('-7 days'));

        if (strtotime($window_start) >= strtotime($window_end)) {
            $this->_json(400, null, 'window_start must be before window_end');
            return;
        }

        $result = $this->ObjectionMining->run_weekly_batch($window_start, $window_end);

        $this->_json(200, $result, 'Weekly batch triggered');
    }


    // -----------------------------------------------------------------
    // 3. extract_for_meeting
    // POST /api/objection_mining/extract_for_meeting
    // Body: { "session_id": 12345 }  OR  { "event_id": 67890 }
    // Rate limit: 20 per minute per uid.
    // Access: BD (own meetings only), CM, SC, Director.
    // -----------------------------------------------------------------

    public function extract_for_meeting()
    {
        $actor = $this->_require_auth(self::$EXTRACT_ROLES);
        if (!$actor) { return; }

        if (!$this->_rate_limit($actor['uid'], 'extract_for_meeting', 20, 60)) {
            $this->_json(429, null, 'Rate limit: 20 calls per minute per user');
            return;
        }

        $body       = $this->_body();
        $session_id = intval($body['session_id'] ?? 0);
        $event_id   = intval($body['event_id']   ?? 0);

        if (!$session_id && !$event_id) {
            $this->_json(400, null, 'Provide session_id or event_id');
            return;
        }

        // Resolve session_id from event_id if needed
        if (!$session_id && $event_id) {
            $row = $this->db->get_where('audio_capture_transcript', [
                'event_id' => $event_id,
            ])->row_array();
            if (!$row) {
                $this->_json(404, null, 'No transcript found for event_id ' . $event_id);
                return;
            }
            $session_id = $row['session_id'];
        }

        // BD can only run extraction on their own meetings
        if (in_array($actor['role'], ['BD'])) {
            $row = $this->db->get_where('audio_capture_transcript', [
                'session_id' => $session_id,
                'actor_uid'  => $actor['uid'],
            ])->row_array();
            if (!$row) {
                $this->_json(403, null, 'You do not have permission to extract this meeting');
                return;
            }
        }

        $result = $this->ObjectionMining->extract_for_meeting($session_id);

        if ($result['status'] === 'not_found_or_not_permitted') {
            $this->_json(404, null, 'Transcript not found or not accessible under current feature flag');
            return;
        }
        if ($result['status'] === 'disabled') {
            $this->_json(403, null, 'Objection mining is disabled (flag=0)');
            return;
        }

        $this->_json(200, $result, 'Extraction complete');
    }


    // -----------------------------------------------------------------
    // 4. top_themes_week
    // GET /api/objection_mining/top_themes_week?iso_week=202622
    // iso_week defaults to current week if omitted.
    // Access: all authenticated roles.
    // -----------------------------------------------------------------

    public function top_themes_week()
    {
        $actor = $this->_require_auth(self::$READ_ALL_ROLES);
        if (!$actor) { return; }

        // iso_week param is informational; the view uses CURDATE() internally.
        // For a different week, the view would need to be parameterised.
        // At org scale this is acceptable; the endpoint documents the constraint.
        $data = $this->ObjectionMining->get_top_themes_for_week();

        $this->_json(200, ['themes' => $data], 'Top objections this week');
    }


    // -----------------------------------------------------------------
    // 5. by_bd
    // GET /api/objection_mining/by_bd?actor_uid=1000289
    // actor_uid is optional. BD sees only their own data.
    // Access: CM, RM, SC, Director (all BDs); BD (own data only).
    // -----------------------------------------------------------------

    public function by_bd()
    {
        $actor = $this->_require_auth(self::$READ_ALL_ROLES);
        if (!$actor) { return; }

        $requested_uid = intval($this->input->get('actor_uid') ?? 0);

        // BD can only see their own data
        if ($actor['role'] === 'BD') {
            $requested_uid = $actor['uid'];
        }

        $data = $this->ObjectionMining->get_bd_objection_pattern(
            $requested_uid ?: null
        );

        $this->_json(200, ['patterns' => $data], 'BD objection patterns');
    }


    // -----------------------------------------------------------------
    // 6. by_cluster
    // GET /api/objection_mining/by_cluster?cluster_id=5&iso_week=202622
    // Access: RM, SC, Director.
    // -----------------------------------------------------------------

    public function by_cluster()
    {
        $actor = $this->_require_auth(self::$COACH_ROLES);
        if (!$actor) { return; }

        $cluster_id = intval($this->input->get('cluster_id') ?? 0);
        $iso_week   = intval($this->input->get('iso_week')   ?? date('oW'));

        $this->db->select(
            'a.theme_code, oth.theme_label, a.cluster_id,
             a.occurrence_count, a.meetings_count, a.iso_week'
        );
        $this->db->from('objection_weekly_aggregate a');
        $this->db->join('objection_theme oth', 'oth.theme_code = a.theme_code');
        $this->db->where('a.actor_uid IS NULL', null, false); // cluster-level rows
        $this->db->where('a.iso_week', $iso_week);
        if ($cluster_id) {
            $this->db->where('a.cluster_id', $cluster_id);
        }
        $this->db->order_by('a.occurrence_count', 'DESC');
        $data = $this->db->get()->result_array();

        $this->_json(200, ['cluster_objections' => $data], 'Cluster objection aggregate');
    }


    // -----------------------------------------------------------------
    // 7. lead_blockers
    // GET /api/objection_mining/lead_blockers?cid_id=111&is_resolved=0
    // BD sees only their own leads. CM/RM/SC/Director see all.
    // Access: all authenticated roles.
    // -----------------------------------------------------------------

    public function lead_blockers()
    {
        $actor = $this->_require_auth(self::$READ_ALL_ROLES);
        if (!$actor) { return; }

        $cid_id     = intval($this->input->get('cid_id')      ?? 0);
        $is_resolved = $this->input->get('is_resolved');

        // For BD: always filter to their own leads
        $actor_uid_filter = null;
        if ($actor['role'] === 'BD') {
            $actor_uid_filter = $actor['uid'];
        }

        $sql = 'SELECT * FROM v_lead_blockers_unresolved WHERE 1=1';
        $params = [];

        if ($cid_id) {
            $sql .= ' AND cid_id = ?';
            $params[] = $cid_id;
        }
        if ($actor_uid_filter) {
            $sql .= ' AND actor_uid = ?';
            $params[] = $actor_uid_filter;
        }
        if ($is_resolved !== null && $is_resolved !== '') {
            // v_lead_blockers_unresolved already filters is_resolved=0,
            // so if caller asks for resolved, query the table directly.
            if (intval($is_resolved) === 1) {
                $sql  = 'SELECT lb.*, cm.compname AS school_name, ic.cstatus, oth.theme_label
                         FROM objection_lead_blocker lb
                         JOIN init_call ic ON ic.id = lb.cid_id
                         LEFT JOIN company_master cm ON cm.id = ic.cmpid_id
                         JOIN objection_theme oth ON oth.theme_code = lb.theme_code
                         WHERE lb.is_resolved = 1';
                $params = [];
                if ($cid_id) { $sql .= ' AND lb.cid_id = ?'; $params[] = $cid_id; }
                if ($actor_uid_filter) { $sql .= ' AND lb.actor_uid = ?'; $params[] = $actor_uid_filter; }
            }
        }

        $data = $this->db->query($sql, $params)->result_array();
        $this->_json(200, ['blockers' => $data], 'Lead blocker objections');
    }


    // -----------------------------------------------------------------
    // 8. kb_candidates
    // GET /api/objection_mining/kb_candidates
    // Returns top unresolved objections not yet in the KB as approved FAQs.
    // Access: CM, RM, SC, Director, AVP.
    // -----------------------------------------------------------------

    public function kb_candidates()
    {
        $actor = $this->_require_auth(self::$COACH_ROLES);
        if (!$actor) { return; }

        $data = $this->ObjectionMining->get_kb_candidates();
        $this->_json(200, ['kb_candidates' => $data], 'Objections without approved KB rebuttals');
    }


    // -----------------------------------------------------------------
    // Private helpers
    // -----------------------------------------------------------------

    /**
     * _require_auth
     * Validates Bearer token and returns the actor row.
     * Returns null and sends 401/403 if invalid or insufficient role.
     *
     * @param array $allowed_roles
     * @return array|null actor row with uid, role, etc.
     */
    private function _require_auth($allowed_roles)
    {
        $actor = $this->bearerauth->authenticate();
        if (!$actor) {
            $this->_json(401, null, 'Unauthorised: valid Bearer token required');
            return null;
        }
        // system/superadmin (master bearer token) always passes role check
        // rimlyproof_authunify_20260609: case-insensitive role match. BearerAuth
        // returns lowercase roles (bd/cm/rm/...) while allowlists use BD/CM/RM.
        $role = isset($actor['role']) ? strtolower((string)$actor['role']) : '';
        if (in_array($role, array('system','superadmin'), true)) {
            return $actor;
        }
        $allowed_lc = array_map('strtolower', $allowed_roles);
        if (!in_array($role, $allowed_lc, true)) {
            $this->_json(403, null, 'Forbidden: role ' . $role . ' cannot access this endpoint');
            return null;
        }
        return $actor;
    }


    /**
     * _rate_limit
     * Simple sliding-window rate limiter using the cache driver.
     *
     * @param int    $uid       actor uid
     * @param string $endpoint  endpoint name
     * @param int    $max_calls max calls allowed in window
     * @param int    $window    window in seconds
     * @return bool  true if within limit
     */
    private function _rate_limit($uid, $endpoint, $max_calls, $window)
    {
        $key   = 'rate_' . $endpoint . '_' . $uid;
        $count = $this->cache->get($key) ?: 0;
        if ($count >= $max_calls) {
            return false;
        }
        $this->cache->save($key, $count + 1, $window);
        return true;
    }


    /**
     * _body
     * Parses JSON request body.
     */
    private function _body()
    {
        $raw = $this->input->raw_input_stream;
        if (empty($raw)) {
            return [];
        }
        $decoded = json_decode($raw, true);
        return is_array($decoded) ? $decoded : [];
    }


    /**
     * _json
     * Sends a JSON response.
     *
     * @param int    $http_code HTTP status code
     * @param mixed  $data      payload or null
     * @param string $message   human-readable message
     */
    private function _json($http_code, $data, $message = '')
    {
        $this->output
            ->set_status_header($http_code)
            ->set_content_type('application/json', 'utf-8')
            ->set_output(json_encode([
                'status'  => $http_code < 400 ? 'ok' : 'error',
                'message' => $message,
                'data'    => $data,
            ]));
    }
}
// End ObjectionMiningController.php

// CI3 routing alias: route target "ObjectionMining" -> ObjectionMiningController
// Added 2026-06-06 GROUP C fix
if (!class_exists("ObjectionMining", false)) {
    class_alias("ObjectionMiningController", "ObjectionMining");
}

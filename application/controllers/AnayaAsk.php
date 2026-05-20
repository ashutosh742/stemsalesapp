<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * AnayaAsk controller
 *
 * REST surface for migration 032 (Anaya Ask Conversational Query Agent).
 *
 * Endpoints:
 *   POST /api/ask                               - submit a natural-language query
 *   GET  /api/ask/session/<id>                  - retrieve session messages
 *   GET  /api/ask/suggestions?role=<role>       - role-aware quick prompts
 *   POST /api/ask/feedback                      - thumbs up/down on a response
 *   GET  /api/ask/usage                         - admin only, usage summary today
 *
 * All endpoints require Bearer STEM_DIGEST_TOKEN except where noted.
 *
 * Routes to add in application/config/routes.php:
 *   $route['api/ask']                = 'anayaask/ask';
 *   $route['api/ask/session/(:num)'] = 'anayaask/session/$1';
 *   $route['api/ask/suggestions']    = 'anayaask/suggestions';
 *   $route['api/ask/feedback']       = 'anayaask/feedback';
 *   $route['api/ask/usage']          = 'anayaask/usage';
 *
 * Auth: Bearer STEM_DIGEST_TOKEN.
 * Read-only: no write actions in phase 1.
 *
 * Author: STEM ops
 * Migration: 032
 * Date: 2026-05-20
 */
class AnayaAsk extends CI_Controller
{
    // Valid roles
    const VALID_ROLES = ['bd', 'cm', 'rm', 'director'];

    // Admin roles that can access /api/ask/usage
    const ADMIN_ROLES = ['rm', 'director'];

    // Max question length in characters
    const MAX_QUESTION_LEN = 500;

    public function __construct()
    {
        parent::__construct();
        $this->load->database();

        // Check master enable flag
        if (getenv('STEM_ASK_ENABLED') === '0') {
            $this->_json([
                'error'   => 'service_unavailable',
                'message' => 'Ask Anaya is temporarily offline. Try again later.',
            ], 503);
        }

        require_once APPPATH . 'models/AIAgents/Anaya_ask_agent.php';
        $this->agent = new Anaya_ask_agent();
        $this->_require_bearer();
    }

    // -------------------------------------------------------------------------
    // AUTH
    // -------------------------------------------------------------------------
    private function _require_bearer()
    {
        $hdr = $this->input->get_request_header('Authorization');
        if (!$hdr || strpos($hdr, 'Bearer ') !== 0) {
            $this->_json(['error' => 'unauthorized'], 401);
        }
        $token    = trim(substr($hdr, 7));
        $expected = getenv('STEM_DIGEST_TOKEN');
        if (!$expected || $token !== $expected) {
            $this->_json(['error' => 'invalid_token'], 401);
        }
    }

    private function _json($data, $code = 200)
    {
        $this->output
             ->set_status_header($code)
             ->set_content_type('application/json')
             ->set_output(json_encode($data));
        exit;
    }

    // =========================================================================
    // POST /api/ask
    //
    // Body (JSON):
    //   text        string  required  plain-English question
    //   uid         int     required  caller user id
    //   role        string  required  bd | cm | rm | director
    //   session_id  int     optional  existing session to continue
    //   cluster_id  int     optional  cluster if role=bd or cm
    //   region_id   int     optional  region if role=rm
    // =========================================================================
    public function ask()
    {
        if ($this->input->method() !== 'post') {
            $this->_json(['error' => 'post_only'], 405);
        }

        $body = $this->_json_body();

        $text       = isset($body['text'])       ? trim($body['text'])       : null;
        $uid        = isset($body['uid'])        ? (int)$body['uid']        : null;
        $role       = isset($body['role'])       ? strtolower($body['role']) : null;
        $session_id = isset($body['session_id']) ? (int)$body['session_id'] : null;
        $cluster_id = isset($body['cluster_id']) ? (int)$body['cluster_id'] : null;
        $region_id  = isset($body['region_id'])  ? (int)$body['region_id']  : null;

        // Validate required fields
        if (!$text) {
            $this->_json(['error' => 'missing_text',
                'message' => 'text is required'], 400);
        }
        if (!$uid) {
            $this->_json(['error' => 'missing_uid',
                'message' => 'uid is required'], 400);
        }
        if (!$role || !in_array($role, self::VALID_ROLES)) {
            $this->_json(['error' => 'invalid_role',
                'message' => 'role must be one of: ' . implode(', ', self::VALID_ROLES)], 400);
        }
        if (strlen($text) > self::MAX_QUESTION_LEN) {
            $this->_json(['error' => 'question_too_long',
                'message' => 'Question must be under ' . self::MAX_QUESTION_LEN . ' characters'], 400);
        }

        // Validate session ownership if provided
        if ($session_id) {
            $owner = $this->_validate_session_owner($session_id, $uid);
            if (!$owner) {
                $this->_json(['error' => 'invalid_session',
                    'message' => 'session_id not found or does not belong to this user'], 403);
            }
        }

        // Delegate to agent
        $result = $this->agent->handle_query(
            $uid, $role, $cluster_id, $region_id, $text, $session_id
        );

        $http_code = ($result['ok'] ?? true) ? 200 : 422;
        $this->_json($result, $http_code);
    }

    // =========================================================================
    // GET /api/ask/session/<id>
    //
    // Returns all messages in a session.
    // Only the session owner can retrieve it.
    // Query params: uid (required)
    // =========================================================================
    public function session($session_id)
    {
        if ($this->input->method() !== 'get') {
            $this->_json(['error' => 'get_only'], 405);
        }

        $session_id = (int)$session_id;
        $uid        = (int)$this->input->get('uid');

        if (!$session_id || !$uid) {
            $this->_json(['error' => 'missing_params',
                'message' => 'session_id and uid are required'], 400);
        }

        // Verify ownership
        if (!$this->_validate_session_owner($session_id, $uid)) {
            $this->_json(['error' => 'not_found'], 404);
        }

        $result = $this->agent->get_session_messages($session_id, $uid);
        $this->_json($result);
    }

    // =========================================================================
    // GET /api/ask/suggestions?role=<role>
    //
    // Returns 5 role-appropriate quick prompts.
    // No uid required - role is enough.
    // =========================================================================
    public function suggestions()
    {
        if ($this->input->method() !== 'get') {
            $this->_json(['error' => 'get_only'], 405);
        }

        $role = strtolower($this->input->get('role') ?: 'bd');
        if (!in_array($role, self::VALID_ROLES)) {
            $role = 'bd';
        }

        $items = $this->agent->get_suggestions($role);
        $this->_json([
            'ok'          => true,
            'role'        => $role,
            'suggestions' => $items,
        ]);
    }

    // =========================================================================
    // POST /api/ask/feedback
    //
    // Body (JSON):
    //   message_id  int     required  ask_message.id (assistant turn)
    //   session_id  int     required  parent session id
    //   uid         int     required  caller user id (must be session owner)
    //   feedback    string  required  good | bad
    // =========================================================================
    public function feedback()
    {
        if ($this->input->method() !== 'post') {
            $this->_json(['error' => 'post_only'], 405);
        }

        $body       = $this->_json_body();
        $message_id = isset($body['message_id']) ? (int)$body['message_id'] : null;
        $session_id = isset($body['session_id']) ? (int)$body['session_id'] : null;
        $uid        = isset($body['uid'])        ? (int)$body['uid']        : null;
        $feedback   = isset($body['feedback'])   ? strtolower($body['feedback']) : null;

        if (!$message_id || !$session_id || !$uid || !$feedback) {
            $this->_json(['error' => 'missing_params',
                'message' => 'message_id, session_id, uid, and feedback are required'], 400);
        }

        if (!in_array($feedback, ['good', 'bad'])) {
            $this->_json(['error' => 'invalid_feedback',
                'message' => 'feedback must be good or bad'], 400);
        }

        // Verify session ownership
        if (!$this->_validate_session_owner($session_id, $uid)) {
            $this->_json(['error' => 'not_found'], 404);
        }

        $result = $this->agent->record_feedback($message_id, $session_id, $uid, $feedback);
        $this->_json($result, $result['ok'] ? 200 : 422);
    }

    // =========================================================================
    // GET /api/ask/usage
    //
    // Admin only (role must be rm or director).
    // Query params: uid, role
    // Returns v_ask_usage_today + denied count.
    // =========================================================================
    public function usage()
    {
        if ($this->input->method() !== 'get') {
            $this->_json(['error' => 'get_only'], 405);
        }

        $uid  = (int)$this->input->get('uid');
        $role = strtolower($this->input->get('role') ?: '');

        if (!$uid || !$role) {
            $this->_json(['error' => 'missing_params',
                'message' => 'uid and role are required'], 400);
        }

        if (!in_array($role, self::ADMIN_ROLES)) {
            $this->_json(['error' => 'forbidden',
                'message' => 'usage endpoint requires role rm or director'], 403);
        }

        $result = $this->agent->get_usage();
        $this->_json($result);
    }

    // =========================================================================
    // GET /api/day_pack?uid=<uid>&role=<role>&date=<YYYY-MM-DD optional>
    //
    // Mobile day pack endpoint. Returns the consolidated morning-brief payload
    // that the mobile home screen renders: planned tasks for today, top
    // applause, top alerts, manager scorecard if role is cm or rm.
    //
    // Query params:
    //   uid    int     required  caller user id
    //   role   string  required  bd | cm | rm | director
    //   date   string  optional  YYYY-MM-DD, defaults to today (server time)
    //
    // Inlined here on mobile-api-endpoints branch, 2026-05-20.
    // =========================================================================
    public function api_day_pack()
    {
        if ($this->input->method() !== 'get') {
            $this->_json(['error' => 'get_only'], 405);
        }

        $uid  = (int)$this->input->get('uid');
        $role = strtolower($this->input->get('role') ?: '');
        $date = $this->input->get('date') ?: date('Y-m-d');

        if (!$uid) {
            $this->_json(['error' => 'missing_uid',
                'message' => 'uid is required'], 400);
        }
        if (!$role || !in_array($role, self::VALID_ROLES)) {
            $this->_json(['error' => 'invalid_role',
                'message' => 'role must be one of: ' . implode(', ', self::VALID_ROLES)], 400);
        }

        // Validate date format YYYY-MM-DD
        $d = DateTime::createFromFormat('Y-m-d', $date);
        if (!$d || $d->format('Y-m-d') !== $date) {
            $this->_json(['error' => 'invalid_date',
                'message' => 'date must be YYYY-MM-DD'], 400);
        }

        // Confirm the user exists
        $user = $this->db->query(
            "SELECT uid, name, type_id FROM user WHERE uid = ? LIMIT 1",
            [$uid]
        )->row_array();
        if (!$user) {
            $this->_json(['error' => 'user_not_found'], 404);
        }

        // Planned tasks for the day
        $planned = $this->db->query("
            SELECT id, cid_id, actiontype_id, purpose_id, plan_date,
                   slot_start, slot_end, is_auto, status
              FROM daily_planner
             WHERE uid = ?
               AND plan_date = ?
             ORDER BY slot_start ASC
             LIMIT 50
        ", [$uid, $date])->result_array();

        // Top 3 applause items in last 24 hours for this user
        $applause = $this->db->query("
            SELECT id, event_type, bd_uid, related_id, message_short, created_at
              FROM applause_log
             WHERE bd_uid = ?
               AND created_at >= DATE_SUB(NOW(), INTERVAL 24 HOUR)
             ORDER BY created_at DESC
             LIMIT 3
        ", [$uid])->result_array();

        // Top 5 stuck leads for this BD (cstatus, days_in_status)
        $stuck = [];
        if ($role === 'bd') {
            $stuck = $this->db->query("
                SELECT cid_id, current_status_id, days_in_status, school_name
                  FROM init_call
                 WHERE mainbd = ?
                   AND days_in_status >= 5
                 ORDER BY days_in_status DESC
                 LIMIT 5
            ", [$uid])->result_array();
        }

        // Manager scorecard placeholder if role is cm or rm (real numbers
        // come from line_manager_scorecard table when migration 022 is live).
        $scorecard = null;
        if (in_array($role, ['cm', 'rm'])) {
            $row = $this->db->query("
                SELECT day_score, grade, k1_mom_sla_pct,
                       k3_signoff_avg_hours, pending_signoff_count
                  FROM line_manager_scorecard
                 WHERE manager_uid = ?
                   AND scorecard_date = ?
                 LIMIT 1
            ", [$uid, $date])->row_array();
            $scorecard = $row ?: null;
        }

        $this->_json([
            'ok'        => true,
            'uid'       => $uid,
            'role'      => $role,
            'date'      => $date,
            'planned'   => $planned,
            'applause'  => $applause,
            'stuck'     => $stuck,
            'scorecard' => $scorecard,
            'generated_at' => date('c'),
        ]);
    }

    // =========================================================================
    // PRIVATE HELPERS
    // =========================================================================

    /**
     * Parse JSON request body.
     * Falls back to POST fields if Content-Type is not application/json.
     */
    private function _json_body()
    {
        $raw = $this->input->raw_input_stream;
        if ($raw) {
            $decoded = json_decode($raw, true);
            if (json_last_error() === JSON_ERROR_NONE) return $decoded;
        }
        // Fallback to POST fields
        return $this->input->post(null, true) ?: [];
    }

    /**
     * Validate that a session belongs to the given uid.
     * Returns true if valid, false otherwise.
     */
    private function _validate_session_owner($session_id, $uid)
    {
        $row = $this->db->query(
            "SELECT id FROM ask_session WHERE id = ? AND uid = ?",
            [(int)$session_id, (int)$uid]
        )->row_array();
        return !empty($row);
    }
}

<?php
/**
 * RolePlayController.php
 * Location: application/controllers/api/RolePlayController.php
 *
 * Migration 054 - AI Role-Play Coaching for Business Development.
 *
 * All 10 endpoints are BearerAuth protected.
 * Feature flag role_play_054_enabled must be 1 (pilot) or 2 (org-wide).
 * When flag = 1, only WB pilot uids are served.
 *
 * Endpoints:
 *  GET  /api/role_play/probe
 *  GET  /api/role_play/list_scenarios
 *  POST /api/role_play/start_session
 *  POST /api/role_play/post_turn
 *  POST /api/role_play/end_session
 *  GET  /api/role_play/get_session/{session_id}
 *  GET  /api/role_play/list_my_sessions
 *  POST /api/role_play/assign_drill          (CM/RM only)
 *  GET  /api/role_play/list_my_drills
 *  POST /api/role_play/coach_review          (CM/RM only)
 *
 * Type IDs: 1=BD, 13=CM, 25=SH, 26=ACM, 27=AO, 28=RM
 * WB pilot uids: 1000289, 1000351, 1000305, 1000269, 1000356
 */

defined('BASEPATH') OR exit('No direct script access allowed');

class RolePlayController extends CI_Controller
{

    /** Pilot uid set. Cross-checked when flag = 1. */
    private static $PILOT_UIDS = [1000289, 1000351, 1000305, 1000269, 1000356];

    /** Type IDs allowed to call coach-only endpoints (assign, review). */
    private static $COACH_TYPE_IDS = [13, 25, 26, 27, 28]; // CM, SH, ACM, AO, RM


    public function __construct()
    {
        parent::__construct();
        $this->load->model('AIAgents/RolePlay_model', 'rp');
        $this->load->helper('url');
    }


    // ==================================================================
    // ENDPOINT 1: GET /api/role_play/probe
    // ==================================================================
    // Returns the next meeting within 15 minutes for this BD, if any.
    // Frontend uses this to surface the "Rehearse this meeting" CTA.
    // ==================================================================

    public function probe()
    {
        if ($this->input->server('REQUEST_METHOD') !== 'GET') {
            return $this->_respond(405, ['error' => 'method_not_allowed']);
        }

        $uid = $this->_auth_and_flag_check();
        if (!$uid) return;

        $upcoming = $this->rp->get_upcoming_meeting($uid);

        if (empty($upcoming)) {
            return $this->_respond(200, [
                'has_upcoming_meeting' => false,
                'upcoming_meeting'     => null,
            ]);
        }

        // Suggest the best scenario for pre-meeting rehearsal based on
        // the cstatus of the lead. For now default to DISCOVERY_FRESH_LEAD
        // unless a more targeted mapping is defined.
        $suggested_scenario = $this->_suggest_scenario_for_event(
            $upcoming['cid_id']
        );

        return $this->_respond(200, [
            'has_upcoming_meeting' => true,
            'upcoming_meeting'     => [
                'event_id'          => $upcoming['event_id'],
                'cid_id'            => $upcoming['cid_id'],
                'event_date'        => $upcoming['event_date'],
                'event_purpose'     => $upcoming['event_purpose'],
            ],
            'suggested_scenario' => $suggested_scenario,
        ]);
    }


    // ==================================================================
    // ENDPOINT 2: GET /api/role_play/list_scenarios
    // ==================================================================
    // Returns all active scenarios. Any authenticated user can call this.
    // ==================================================================

    public function list_scenarios()
    {
        if ($this->input->server('REQUEST_METHOD') !== 'GET') {
            return $this->_respond(405, ['error' => 'method_not_allowed']);
        }

        $uid = $this->_auth_and_flag_check();
        if (!$uid) return;

        $scenarios = $this->rp->list_scenarios();
        return $this->_respond(200, ['scenarios' => $scenarios]);
    }


    // ==================================================================
    // ENDPOINT 3: POST /api/role_play/start_session
    // ==================================================================
    // Body: { scenario_code, mode, cid_id?, event_id?,
    //         drill_assignment_id? }
    // Returns: session_id, persona_role, persona_name, school_name,
    //          opening_message (first AI persona line)
    // ==================================================================

    public function start_session()
    {
        if ($this->input->server('REQUEST_METHOD') !== 'POST') {
            return $this->_respond(405, ['error' => 'method_not_allowed']);
        }

        $uid = $this->_auth_and_flag_check();
        if (!$uid) return;

        $body = json_decode($this->input->raw_input_stream, true);
        if (empty($body)) {
            $body = $this->input->post();
        }

        $scenario_code = $this->_require($body, 'scenario_code');
        $mode          = isset($body['mode']) ? $body['mode'] : 'drill';
        $cid_id        = isset($body['cid_id']) ? (int) $body['cid_id'] : null;
        $event_id      = isset($body['event_id']) ? (int) $body['event_id'] : null;
        $assignment_id = isset($body['drill_assignment_id'])
                         ? (int) $body['drill_assignment_id'] : null;

        if (!$scenario_code) {
            return $this->_respond(400, ['error' => 'scenario_code_required']);
        }

        $allowed_modes = ['pre_meeting', 'drill', 'induction'];
        if (!in_array($mode, $allowed_modes)) {
            return $this->_respond(400, ['error' => 'invalid_mode']);
        }

        $result = $this->rp->start_session(
            $uid, $scenario_code, $mode, $cid_id, $event_id, $assignment_id
        );

        if (!empty($result['error'])) {
            return $this->_respond(422, $result);
        }

        // Generate the opening AI message so the chat starts with the
        // persona speaking, not waiting for the BD.
        $opening = $this->_get_opening_message($result, $scenario_code, $mode);

        return $this->_respond(201, [
            'session_id'      => $result['session_id'],
            'persona_role'    => $result['persona_role'],
            'persona_name'    => $result['persona_name'],
            'school_name'     => $result['school_name'],
            'mode'            => $mode,
            'opening_message' => $opening,
            'max_turns'       => \RolePlay_model::MAX_TURNS_PER_SESSION ?? 20,
        ]);
    }


    // ==================================================================
    // ENDPOINT 4: POST /api/role_play/post_turn
    // ==================================================================
    // Body: { session_id, message }
    // Returns: ai_reply, turn_number, session_limit_reached, cost_rs
    // ==================================================================

    public function post_turn()
    {
        if ($this->input->server('REQUEST_METHOD') !== 'POST') {
            return $this->_respond(405, ['error' => 'method_not_allowed']);
        }

        $uid = $this->_auth_and_flag_check();
        if (!$uid) return;

        $body = json_decode($this->input->raw_input_stream, true);
        if (empty($body)) {
            $body = $this->input->post();
        }

        $session_id = $this->_require_int($body, 'session_id');
        $message    = isset($body['message']) ? trim($body['message']) : '';

        if (!$session_id) {
            return $this->_respond(400, ['error' => 'session_id_required']);
        }
        if ($message === '') {
            return $this->_respond(400, ['error' => 'message_required']);
        }

        $result = $this->rp->post_turn($session_id, $uid, $message);

        if (!empty($result['error'])) {
            $code = strpos($result['error'], 'not_found') !== false ? 404 : 422;
            return $this->_respond($code, $result);
        }

        return $this->_respond(200, $result);
    }


    // ==================================================================
    // ENDPOINT 5: POST /api/role_play/end_session
    // ==================================================================
    // Body: { session_id, satisfaction_stars? }
    // Returns: session_id, status, score, induction_status
    // ==================================================================

    public function end_session()
    {
        if ($this->input->server('REQUEST_METHOD') !== 'POST') {
            return $this->_respond(405, ['error' => 'method_not_allowed']);
        }

        $uid = $this->_auth_and_flag_check();
        if (!$uid) return;

        $body = json_decode($this->input->raw_input_stream, true);
        if (empty($body)) {
            $body = $this->input->post();
        }

        $session_id   = $this->_require_int($body, 'session_id');
        $stars        = isset($body['satisfaction_stars'])
                        ? (int) $body['satisfaction_stars'] : null;

        if (!$session_id) {
            return $this->_respond(400, ['error' => 'session_id_required']);
        }

        $result = $this->rp->end_session($session_id, $uid, $stars);

        if (!empty($result['error'])) {
            $code = strpos($result['error'], 'not_found') !== false ? 404 : 422;
            return $this->_respond($code, $result);
        }

        return $this->_respond(200, $result);
    }


    // ==================================================================
    // ENDPOINT 6: GET /api/role_play/get_session/{session_id}
    // ==================================================================
    // Returns full session detail including turns and score.
    // BD can only access their own sessions. CM/RM can access any
    // session in their cluster.
    // ==================================================================

    public function get_session($session_id = null)
    {
        if ($this->input->server('REQUEST_METHOD') !== 'GET') {
            return $this->_respond(405, ['error' => 'method_not_allowed']);
        }

        $uid = $this->_auth_and_flag_check();
        if (!$uid) return;

        if (empty($session_id)) {
            return $this->_respond(400, ['error' => 'session_id_required']);
        }

        $data = $this->rp->get_session_with_score((int) $session_id);

        if (!empty($data['error'])) {
            return $this->_respond(404, $data);
        }

        // BD can only see their own session
        $type_id = $this->_get_type_id($uid);
        if ($type_id === 1 && (int) $data['session']['bd_uid'] !== (int) $uid) {
            return $this->_respond(403, ['error' => 'access_denied']);
        }

        return $this->_respond(200, $data);
    }


    // ==================================================================
    // ENDPOINT 7: GET /api/role_play/list_my_sessions
    // ==================================================================
    // Returns the last 20 sessions for the requesting BD.
    // CM/RM pass ?bd_uid=X to see a specific BD's history.
    // ==================================================================

    public function list_my_sessions()
    {
        if ($this->input->server('REQUEST_METHOD') !== 'GET') {
            return $this->_respond(405, ['error' => 'method_not_allowed']);
        }

        $uid = $this->_auth_and_flag_check();
        if (!$uid) return;

        $type_id    = $this->_get_type_id($uid);
        $target_uid = $uid;

        if (in_array($type_id, self::$COACH_TYPE_IDS)) {
            $qs_uid = $this->input->get('bd_uid');
            if (!empty($qs_uid)) {
                $target_uid = (int) $qs_uid;
            }
        }

        $sessions = $this->rp->list_my_sessions($target_uid);
        return $this->_respond(200, [
            'bd_uid'   => $target_uid,
            'sessions' => $sessions,
        ]);
    }


    // ==================================================================
    // ENDPOINT 8: POST /api/role_play/assign_drill
    // ==================================================================
    // CM/RM only.
    // Body: { bd_uid, scenario_code, due_date? }
    // ==================================================================

    public function assign_drill()
    {
        if ($this->input->server('REQUEST_METHOD') !== 'POST') {
            return $this->_respond(405, ['error' => 'method_not_allowed']);
        }

        $uid = $this->_auth_and_flag_check();
        if (!$uid) return;

        $type_id = $this->_get_type_id($uid);
        if (!in_array($type_id, self::$COACH_TYPE_IDS)) {
            return $this->_respond(403, ['error' => 'cm_rm_only']);
        }

        $body = json_decode($this->input->raw_input_stream, true);
        if (empty($body)) {
            $body = $this->input->post();
        }

        $bd_uid        = $this->_require_int($body, 'bd_uid');
        $scenario_code = $this->_require($body, 'scenario_code');
        $due_date      = isset($body['due_date']) ? $body['due_date'] : null;

        if (!$bd_uid || !$scenario_code) {
            return $this->_respond(400,
                ['error' => 'bd_uid and scenario_code required']);
        }

        $scenario = $this->rp->get_scenario($scenario_code);
        if (empty($scenario)) {
            return $this->_respond(404, ['error' => 'scenario_not_found']);
        }

        $result = $this->rp->assign_drill($uid, $bd_uid, $scenario_code, $due_date);
        return $this->_respond(201, $result);
    }


    // ==================================================================
    // ENDPOINT 9: GET /api/role_play/list_my_drills
    // ==================================================================
    // Returns pending + in-progress drills for the requesting BD, or
    // for a BD specified by ?bd_uid= when called by CM/RM.
    // ==================================================================

    public function list_my_drills()
    {
        if ($this->input->server('REQUEST_METHOD') !== 'GET') {
            return $this->_respond(405, ['error' => 'method_not_allowed']);
        }

        $uid = $this->_auth_and_flag_check();
        if (!$uid) return;

        $type_id    = $this->_get_type_id($uid);
        $target_uid = $uid;

        if (in_array($type_id, self::$COACH_TYPE_IDS)) {
            $qs_uid = $this->input->get('bd_uid');
            if (!empty($qs_uid)) {
                $target_uid = (int) $qs_uid;
            }
        }

        $drills           = $this->rp->list_my_drills($target_uid);
        $induction_status = $this->rp->check_induction_gate($target_uid);

        return $this->_respond(200, [
            'bd_uid'           => $target_uid,
            'drills'           => $drills,
            'induction_status' => $induction_status,
        ]);
    }


    // ==================================================================
    // ENDPOINT 10: POST /api/role_play/coach_review
    // ==================================================================
    // CM/RM only.
    // Body: { assignment_id, cm_note }
    // ==================================================================

    public function coach_review()
    {
        if ($this->input->server('REQUEST_METHOD') !== 'POST') {
            return $this->_respond(405, ['error' => 'method_not_allowed']);
        }

        $uid = $this->_auth_and_flag_check();
        if (!$uid) return;

        $type_id = $this->_get_type_id($uid);
        if (!in_array($type_id, self::$COACH_TYPE_IDS)) {
            return $this->_respond(403, ['error' => 'cm_rm_only']);
        }

        $body = json_decode($this->input->raw_input_stream, true);
        if (empty($body)) {
            $body = $this->input->post();
        }

        $assignment_id = $this->_require_int($body, 'assignment_id');
        $cm_note       = isset($body['cm_note']) ? trim($body['cm_note']) : '';

        if (!$assignment_id) {
            return $this->_respond(400, ['error' => 'assignment_id_required']);
        }
        if ($cm_note === '') {
            return $this->_respond(400, ['error' => 'cm_note_required']);
        }

        $ok = $this->rp->coach_review($assignment_id, $uid, $cm_note);
        if (!$ok) {
            return $this->_respond(404, ['error' => 'assignment_not_found']);
        }

        return $this->_respond(200, ['message' => 'review_saved']);
    }


    // ==================================================================
    // PRIVATE: Auth and feature flag guard
    // ==================================================================

    /**
     * _auth_and_flag_check
     *
     * 1. Validates the Bearer token and extracts uid.
     * 2. Checks feature_flag.role_play_054_enabled.
     *    - 0: returns 503.
     *    - 1: pilot mode, only WB uids are served.
     *    - 2: org-wide, all authenticated users served.
     *
     * Returns uid on success, null and sends response on failure.
     *
     * @return int|null
     */
    private function _auth_and_flag_check()
    {
        // Bearer token check
        $auth_header = $this->input->server('HTTP_AUTHORIZATION');
        if (empty($auth_header) || stripos($auth_header, 'bearer ') !== 0) {
            $this->_respond(401, ['error' => 'bearer_token_required']);
            return null;
        }

        $token = trim(substr($auth_header, 7));
        $uid   = $this->_resolve_token($token);

        if ($uid === null) {
            $this->_respond(401, ['error' => 'invalid_or_expired_token']);
            return null;
        }

        // Feature flag check
        $flag = $this->_get_flag('role_play_054_enabled');

        if ($flag === '0' || $flag === 0) {
            $this->_respond(503, ['error' => 'feature_not_enabled']);
            return null;
        }

        if ($flag === '1' || $flag === 1) {
            if (!in_array((int) $uid, self::$PILOT_UIDS)) {
                $this->_respond(403, [
                    'error' => 'pilot_only',
                    'message' => 'Role-play coaching is currently available '
                               . 'to the WB pilot group only.',
                ]);
                return null;
            }
        }

        return (int) $uid;
    }


    /**
     * _resolve_token
     *
     * Validates the Bearer token against the session store.
     * Pattern from other STEM controllers - looks up user_sessions
     * or equivalent table.
     *
     * @param  string $token
     * @return int|null  uid on success
     */
    private function _resolve_token($token)
    {
        // Accept master bearer token (uid=-1 = system/admin; -1 is truthy unlike 0)
        $master = '4eBaiAT7r4zu6OK3b8evjLNia1D7RGgb0qRTuLJfUSo';
        if ($token === $master) {
            return -1;
        }
        // Column is session_token (not token) - fixed audit_20260606
        $row = $this->db
            ->select('uid, expires_at')
            ->where('session_token', $token)
            ->where('expires_at >', date('Y-m-d H:i:s'))
            ->get('user_sessions')
            ->row_array();

        return !empty($row) ? (int) $row['uid'] : null;
    }


    /**
     * _get_type_id
     *
     * Returns the type_id from the user table for the given uid.
     *
     * @param  int $uid
     * @return int
     */
    private function _get_type_id($uid)
    {
        $row = $this->db
            ->select('type_id')
            ->where('uid', (int) $uid)
            ->get('user')
            ->row_array();
        return !empty($row) ? (int) $row['type_id'] : 0;
    }


    /**
     * _get_flag
     *
     * @param  string $flag_code
     * @return string|int
     */
    private function _get_flag($flag_code)
    {
        $row = $this->db
            ->select('flag_value')
            ->where('flag_key', $flag_code)
            ->get('feature_flag')
            ->row_array();
        return !empty($row) ? $row['flag_value'] : '0';
    }


    /**
     * _suggest_scenario_for_event
     *
     * Returns the most relevant scenario_code for a pre-meeting
     * rehearsal based on the lead's cstatus.
     *
     * @param  int $cid_id
     * @return string scenario_code
     */
    private function _suggest_scenario_for_event($cid_id)
    {
        $lead = $this->db
            ->select('cstatus')
            ->where('cid_id', (int) $cid_id)
            ->get('init_call')
            ->row_array();

        $cstatus = !empty($lead['cstatus']) ? (int) $lead['cstatus'] : 1;

        if ($cstatus <= 3)  return 'DISCOVERY_FRESH_LEAD';
        if ($cstatus === 4) return 'PRICE_OBJECTION_PRINCIPAL';
        if ($cstatus === 5) return 'TRUSTEE_MEETING_FIRST_TIME';
        if ($cstatus === 6) return 'BUDGET_TIMING_DEFER';
        if ($cstatus === 7) return 'RP_MEETING_PROPOSAL_PUSH';
        if ($cstatus === 8) return 'TRUSTEE_MEETING_FIRST_TIME';
        if ($cstatus === 9) return 'CLOSING_VERY_POSITIVE';
        return 'DISCOVERY_FRESH_LEAD';
    }


    /**
     * _get_opening_message
     *
     * Returns a short opening line from the AI persona to kick off
     * the chat. This is a static lookup based on persona role to
     * avoid an extra LLM call at session start.
     *
     * @param  array  $session_result  From start_session
     * @param  string $scenario_code
     * @param  string $mode
     * @return string
     */
    private function _get_opening_message($session_result, $scenario_code, $mode)
    {
        $name  = !empty($session_result['persona_name'])
                 ? $session_result['persona_name']
                 : '';
        $role  = $session_result['persona_role'];
        $school = $session_result['school_name'];

        $greeting = $name
            ? 'Good morning, I am ' . $name . ', ' . $role . ' at ' . $school . '.'
            : 'Good morning. I am the ' . $role . ' here at ' . $school . '.';

        $openers = [
            'DISCOVERY_FRESH_LEAD'          => $greeting . ' You wanted to speak to me? I have about 15 minutes.',
            'PRICE_OBJECTION_PRINCIPAL'      => $greeting . ' We saw your demo. Honestly, my first concern is the price. It seems quite steep for us.',
            'TRUSTEE_MEETING_FIRST_TIME'     => $greeting . ' The Principal told me about you. I should say upfront, I am the one who signs the cheques, so I need to understand why we need this.',
            'INCUMBENT_VENDOR_DISPLACE'      => $greeting . ' I appreciate you coming in, but I want to be straight with you - we are happy with what we have. What makes you think we should switch?',
            'BUDGET_TIMING_DEFER'            => $greeting . ' Look, I like what you showed me. My honest concern is timing. Our budget is set for this year. Can we talk again in April?',
            'GRADE_FIT_DOUBT'               => $greeting . ' I have been asked to check if your content actually covers our CBSE grade 6 to 8 syllabus. Walk me through that.',
            'INFRASTRUCTURE_READINESS_PUSHBACK' => $greeting . ' Before we go further - our Wi-Fi in classrooms drops all the time and the tablets are old. I do not see how this is going to work.',
            'AFTER_SALES_CONCERN'           => $greeting . ' A friend who runs a school told me your support went quiet after the first month. How do I know that will not happen here?',
            'RP_MEETING_PROPOSAL_PUSH'      => $greeting . ' I do not have time for another demo. Just send me a proper written proposal with pricing and I will look at it.',
            'CLOSING_VERY_POSITIVE'         => $greeting . ' We are close to a decision. But I need to know: when exactly does this go live and what disruption should I expect in the first term?',
            'REOPEN_LOST_DEAL'              => $greeting . ' We went with someone else last time. I am curious why you are back, but I am not going to give you a lot of time.',
            'BARGE_MEETING_COLD'            => 'I am the PTA Head. Who are you and do you have an appointment? The Principal is in a meeting.',
        ];

        return isset($openers[$scenario_code])
            ? $openers[$scenario_code]
            : $greeting . ' How can I help you today?';
    }


    // ==================================================================
    // PRIVATE: Response helpers
    // ==================================================================

    /**
     * _respond
     *
     * @param  int   $http_code
     * @param  array $data
     * @return void
     */
    private function _respond($http_code, $data)
    {
        http_response_code($http_code);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($data, JSON_UNESCAPED_UNICODE);
    }


    /**
     * _require
     *
     * @param  array  $body
     * @param  string $key
     * @return string|null
     */
    private function _require($body, $key)
    {
        return (!empty($body[$key])) ? trim($body[$key]) : null;
    }


    /**
     * _require_int
     *
     * @param  array  $body
     * @param  string $key
     * @return int|null
     */
    private function _require_int($body, $key)
    {
        return (!empty($body[$key])) ? (int) $body[$key] : null;
    }

}
// End RolePlayController.php

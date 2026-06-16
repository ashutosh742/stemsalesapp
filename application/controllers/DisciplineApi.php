<?php
defined('BASEPATH') OR exit('No direct script access allowed');
date_default_timezone_set('Asia/Kolkata');

/**
 * DisciplineApi
 *
 * Controller: application/controllers/DisciplineApi.php
 *
 * Exposes one endpoint:
 *   GET /api/discipline/state?uid=<uid>
 *
 * The response is the live discipline snapshot used by the mobile app on every
 * screen mount. The mobile app routes users based on the "next_required_screen"
 * field returned here -- it does NOT re-derive routing logic itself.
 *
 * Bearer token guard: every request must send the header
 *   Authorization: Bearer <STEM_DIGEST_TOKEN>
 *
 * All responses are JSON with the envelope {"ok": bool, ...}.
 */
class DisciplineApi extends CI_Controller {

    // Token value is defined in the frozen spec (section 9).
    const DIGEST_TOKEN = '4eBaiAT7r4zu6OK3b8evjLNia1D7RGgb0qRTuLJfUSo';

    // rimlyproof_disciplineauth_20260609
    private $auth_uid  = 0;
    private $auth_role = '';

    public function __construct() {
        parent::__construct();
        $this->load->model('DisciplineState_model');
        $this->load->helper('url');
        // Prevent any view from being loaded accidentally.
        $this->output->set_content_type('application/json');
    }

    // -------------------------------------------------------------------------
    // Bearer token guard
    // -------------------------------------------------------------------------

    /**
     * check_token
     *
     * Reads the Authorization header and verifies the Bearer token. Returns
     * true when valid. On failure it writes a 401 JSON response and returns false.
     */
    private function check_token() {
        $auth_header = $_SERVER['HTTP_AUTHORIZATION'] ?? '';
        if (empty($auth_header)) {
            // Some PHP setups strip the header; try the Apache redirect env var.
            $auth_header = $_SERVER['REDIRECT_HTTP_AUTHORIZATION'] ?? '';
        }
        if (strpos($auth_header, 'Bearer ') === 0) {
            $token = substr($auth_header, 7);
            if ($token === self::DIGEST_TOKEN) {
                return true;
            }
            // Per-user daily JWT: sha1(secret|uid|YYYY-MM-DD), accept today and yesterday
            $secret = getenv('STEM_DIGEST_TOKEN') ?: self::DIGEST_TOKEN;
            $days = array(date('Y-m-d'), date('Y-m-d', strtotime('-1 day')), date('Y-m-d', strtotime('+1 day')));
            $cands = array();
            foreach (array('uid','bd_uid','cm_uid','user_id') as $k) {
                if (isset($_GET[$k]) && (int)$_GET[$k] > 0)  $cands[(int)$_GET[$k]]  = 1;
                if (isset($_POST[$k]) && (int)$_POST[$k] > 0) $cands[(int)$_POST[$k]] = 1;
            }
            foreach (array_keys($cands) as $u) {
                foreach ($days as $d) {
                    if (hash_equals(sha1($secret.'|'.$u.'|'.$d), $token)) return true;
                }
            }
        }
        // rimlyproof_disciplineauth_20260609: accept the real per-user login token by
        // delegating to the shared BearerAuth resolver (instance method, reads header itself).
        $CI =& get_instance();
        $CI->load->library('BearerAuth');
        $res = $CI->bearerauth->resolve();
        if (is_array($res) && !empty($res['ok'])) {
            $this->auth_uid  = isset($res['uid'])  ? (int)$res['uid'] : 0;
            $this->auth_role = isset($res['role']) ? strtolower((string)$res['role']) : '';
            return true;
        }
        http_response_code(401);
        echo json_encode(['ok' => false, 'error' => 'Unauthorized']);
        return false;
    }

    // -------------------------------------------------------------------------
    // GET /api/discipline/state
    // -------------------------------------------------------------------------

    /**
     * discipline_state
     *
     * Assembles the full discipline snapshot for a single user.
     *
     * Query parameter: uid (integer, required)
     *
     * The exact JSON shape is defined in frozen spec section 3.1. Mobile clients
     * read "next_required_screen" and navigate accordingly without re-deriving
     * any routing logic.
     *
     * Routing logic implemented here follows spec section 5:
     *
     *   if day_started == false               -> DayCeremonyV2
     *   elif pbni_count > 0 and not approved  -> DayManagement  (PBNI hard block)
     *   elif pending_autotask_count > 0       -> DayManagement
     *   elif research_not_updated_count > 0   -> Dashboard
     *   elif rp_mom_pending_count > 0         -> PendingForWriteMomMeetingList
     *   elif meeting_expense_pending_count > 0-> UpdateTodaysMeetingsDetails
     *   elif planner locked and no approval   -> SameDayRequestScreen
     *   elif cutoff passed and no request     -> SameDayRequestScreen
     *   else                                  -> PlannerV2
     */
    public function discipline_state() {
        if (!$this->check_token()) {
            return;
        }

        $req_uid = (int) $this->input->get('uid');
        // rimlyproof_disciplineauth_20260609: field users (bd/acm) are forced to their OWN
        // authed uid; managers/system may query any uid; fall back to authed uid if no param.
        if ($this->auth_uid > 0 && ($this->auth_role === 'bd' || $this->auth_role === 'acm')) {
            $uid = $this->auth_uid;
        } elseif ($req_uid > 0) {
            $uid = $req_uid;
        } else {
            $uid = $this->auth_uid;
        }
        if ($uid <= 0) {
            http_response_code(400);
            echo json_encode(['ok' => false, 'error' => 'uid is required and must be a positive integer']);
            return;
        }

        $today = date('Y-m-d');
        $m     = $this->DisciplineState_model;

        // Gather individual state pieces.
        try {
            $day_state               = $m->get_day_state($uid);
            $pbni_count              = $m->get_pbni_count($uid);
            $pbni_alert_state        = $m->get_pbni_alert_state($uid);
            $pending_autotask_count  = $m->get_pending_autotask_count($uid);
            $rp_mom_count            = $m->get_rp_mom_count($uid);
            $meeting_expense_count   = $m->get_meeting_expense_count($uid);
            $research_not_updated    = $m->get_research_not_updated_count($uid);
            $new_lead_reupdate       = $m->get_new_lead_reupdate_count($uid);
            $cutoff_state            = $m->get_cutoff_state($uid, $today);
            $line_manager            = $m->get_line_manager($uid);

            $same_day_row            = $m->get_today_same_day_request($uid, $today);
            $yesterday_row           = $m->get_today_yesterday_request($uid);
            $day_close_override_row  = $m->get_today_day_close_override($uid, $today);
        } catch (Exception $e) {
            log_message('error', 'DisciplineApi::discipline_state DB error for uid=' . $uid . ': ' . $e->getMessage());
            http_response_code(500);
            echo json_encode(['ok' => false, 'error' => 'database error']);
            return;
        }

        // Build same_day_request sub-object.
        if ($same_day_row !== null) {
            $sd_status      = $same_day_row->approvel_status;  // 0, 'Approved', or 'Reject'
            $same_day_block = [
                'exists'        => true,
                'status'        => ($sd_status === 'Approved' ? 'Approved' : ($sd_status === 'Reject' ? 'Reject' : 'Pending')),
                'approver_name' => $same_day_row->approver_name ?? null,
                'approver_role' => $same_day_row->approver_role ?? null,
                'approver_uid'  => (int) ($same_day_row->admin_id ?? 0),
                'approved_at'   => $same_day_row->apr_time ?? null,
            ];
        } else {
            $same_day_block = ['exists' => false];
        }

        // Build yesterday_request sub-object.
        if ($yesterday_row !== null) {
            $yesterday_block = [
                'exists'     => true,
                'status'     => ($yesterday_row->approvel_status == 1 ? 'Approved' : ($yesterday_row->approvel_status == 0 ? 'Pending' : 'Reject')),
                'pbni_count' => (int) ($yesterday_row->pbni_count ?? 0),
            ];
        } else {
            $yesterday_block = ['exists' => false];
        }

        // Build day_close_override sub-object.
        if ($day_close_override_row !== null) {
            $dco_block = [
                'exists'  => true,
                'status'  => $day_close_override_row->approvel_status,
                'remarks' => $day_close_override_row->remarks ?? null,
            ];
        } else {
            $dco_block = ['exists' => false];
        }

        // Build the top-level state dict for routing computation.
        // This mirrors the full JSON shape the model's routing method expects.
        $state_dict = [
            'day_started'                 => $day_state['day_started'],
            'pbni_count'                  => $pbni_count,
            'pbni_alert_approved'         => $pbni_alert_state['pbni_alert_approved'],
            'pending_autotask_count'      => $pending_autotask_count,
            'research_not_updated_count'  => $research_not_updated,
            'rp_mom_pending_count'        => $rp_mom_count,
            'meeting_expense_pending_count' => $meeting_expense_count,
            'today_planner_locked'        => $cutoff_state['planner_locked'],
            'cutoff_passed'               => $cutoff_state['cutoff_passed'],
            'same_day_request'            => $same_day_block,
        ];

        list($next_screen, $next_action, $block_reason) = $m->compute_next_required_screen($state_dict);

        // Assemble the final response matching spec section 3.1 exactly.
        $response = [
            'ok'                            => true,
            'uid'                           => $uid,
            'today'                         => $today,
            'day_started'                   => $day_state['day_started'],
            'day_start_time'                => $day_state['day_start_time'],
            'day_closed'                    => $day_state['day_closed'],
            'pending_autotask_count'        => $pending_autotask_count,
            'pbni_count'                    => $pbni_count,
            'pbni_alert_pending'            => $pbni_alert_state['pbni_alert_pending'],
            'pbni_alert_approved'           => $pbni_alert_state['pbni_alert_approved'],
            'rp_mom_pending_count'          => $rp_mom_count,
            'meeting_expense_pending_count' => $meeting_expense_count,
            'research_not_updated_count'    => $research_not_updated,
            'new_lead_reupdate_count'       => $new_lead_reupdate,
            'today_planner_locked'          => $cutoff_state['planner_locked'],
            'cutoff_time'                   => $cutoff_state['cutoff_time'],
            'cutoff_passed'                 => $cutoff_state['cutoff_passed'],
            'same_day_request'              => $same_day_block,
            'yesterday_request'             => $yesterday_block,
            'day_close_override'            => $dco_block,
            'line_manager'                  => $line_manager ?? (object) [],
            'next_required_action'          => $next_action,
            'next_required_screen'          => $next_screen,
            'block_reason'                  => $block_reason,
        ];

        echo json_encode($response);
    }
}

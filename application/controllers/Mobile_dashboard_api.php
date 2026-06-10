<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Mobile_dashboard_api Controller  (ADDITIVE - staging fix Commit 5, 2026-06-10)
 *
 * GET /api/dashboard/summary?uid=<uid>
 *
 * READ-ONLY mirror of Menu::Dashboard() (Menu.php line 7175). Surfaces the SAME
 * gates and payload the production dashboard enforces, so the mobile dashboard
 * can route the user the same way instead of silently failing.
 *
 * GATES (returned as a structured "gates" object - mobile decides routing):
 *   - day_not_started : roles type_id in [3,4,5,7,8,9,11,12,13,15] and
 *                       get_daydetail(uid, today) is empty.
 *   - pending_autotask: get_PendingAutoTask(uid) count > 0 (blocks Task Planner).
 *   - new_leads       : roles [4,13] and GetOldAddNewLeadComapny(uid) count > 0.
 *   - manager (role 15): day_check / task_check / status_task_check pending counts.
 *
 * PAYLOAD PARITY:
 *   pendingt, totalt, barg (advance), vm_meetings (virtual meetings),
 *   autotasktimenew, plus per-type counts (call/email/meeting via get_callingr
 *   1/2/3; patc/tatc/pate/tate/patm/tatm via get_pat/get_tat 1/2/3).
 *
 * STRICTLY ADDITIVE - touches no existing file, no schema changes, reads real
 * staging data, reuses existing Menu_model methods. Empty results degrade to 0
 * (never 500), matching production behaviour. Auth via BearerAuth (same library
 * the other mobile read endpoints use); field users (BD/ACM) are hard-locked to
 * their own uid. Production stemapp.in is NOT touched.
 */
class Mobile_dashboard_api extends CI_Controller {

    private $auth_uid  = 0;
    private $auth_role = '';

    public function __construct()
    {
        parent::__construct();
        $this->output->set_content_type('application/json');
        $this->load->library('BearerAuth');
        $this->load->model('Menu_model');
    }

    private function json_out($data, $status = 200)
    {
        $this->output
             ->set_status_header($status)
             ->set_output(json_encode($data, JSON_UNESCAPED_UNICODE));
    }

    private function auth_check()
    {
        $auth = $this->bearerauth->resolve();
        if (empty($auth['ok'])) {
            $this->json_out(array('ok' => false, 'error' => 'unauthorized'), 401);
            return false;
        }
        $this->auth_uid  = isset($auth['uid'])  ? (int)$auth['uid']                 : 0;
        $this->auth_role = isset($auth['role']) ? strtolower((string)$auth['role']) : '';
        return true;
    }

    /** Reads uid (or user_id) from GET. Field users hard-locked to own uid. */
    private function resolve_uid()
    {
        $uid = $this->input->get('uid');
        if ( ! $uid) { $uid = $this->input->get('user_id'); }
        $uid = (int) $uid;
        if ($this->auth_uid > 0 && ($this->auth_role === 'bd' || $this->auth_role === 'acm')) {
            return (int) $this->auth_uid;
        }
        if ($uid <= 0 && $this->auth_uid > 0) {
            return (int) $this->auth_uid;
        }
        return $uid;
    }

    /** count() helper tolerant of null / non-array model returns. */
    private function _cnt($v)
    {
        return is_array($v) ? count($v) : 0;
    }

    /**
     * Replicates Menu::CheckDaysCheckPendingByUser / CheckTaskCheckPendingByUser /
     * CheckStatusChangeTaskCheckPendingByUser without depending on session userdata.
     * Returns array('pending'=>int, 'username'=>string). Any failure -> pending 0.
     */
    private function _manager_pending($completeUID, $teamUID)
    {
        try {
            $complete = is_array($completeUID) ? $completeUID : array();
            $team     = is_array($teamUID)     ? $teamUID     : array();
            $names1 = array_map(function($obj) {
                return isset($obj->name) ? $obj->name : null;
            }, $complete);
            $array3 = array_filter($team, function($obj) use ($names1) {
                return !in_array(isset($obj->name) ? $obj->name : null, $names1);
            });
            $array3 = array_values($array3);
            $names = array_map(function($obj) {
                return isset($obj->name) ? $obj->name : '';
            }, $array3);
            return array('pending' => sizeof($array3), 'username' => implode(', ', $names));
        } catch (Exception $e) {
            return array('pending' => 0, 'username' => '');
        }
    }

    /**
     * GET /api/dashboard/summary?uid=<uid>
     */
    public function summary()
    {
        if ( ! $this->auth_check()) { return; }

        try {
            $uid = $this->resolve_uid();
            if ($uid <= 0) {
                $this->json_out(array('ok' => false, 'error' => 'uid required'), 400);
                return;
            }

            date_default_timezone_set('Asia/Kolkata');
            $tdate = $this->input->get('tdate');
            if (!$tdate || !preg_match('/^\d{4}-\d{2}-\d{2}$/', $tdate)) {
                $tdate = date('Y-m-d');
            }
            $today = date('Y-m-d');

            // Resolve role (type_id). Fall back to 0 if user not found.
            $type_id = 0;
            try {
                $u = $this->Menu_model->get_userbyid($uid);
                if (is_array($u) && isset($u[0]) && isset($u[0]->type_id)) {
                    $type_id = (int) $u[0]->type_id;
                }
            } catch (Exception $e) { $type_id = 0; }

            // ----- GATE 1: day-start (roles 3,4,5,7,8,9,11,12,13,15) -----
            $day_roles = array(3,4,5,7,8,9,11,12,13,15);
            $day_started = true;
            $day_gate_applies = in_array($type_id, $day_roles);
            if ($day_gate_applies) {
                $ud = $this->Menu_model->get_daydetail($uid, $today);
                $day_started = ($this->_cnt($ud) > 0);
            }

            // ----- GATE 2: pending auto-task -----
            $pending_autotask = 0;
            try {
                $pending_autotask = $this->_cnt($this->Menu_model->get_PendingAutoTask($uid));
            } catch (Exception $e) { $pending_autotask = 0; }

            // ----- GATE 3: new-leads (roles 4,13) -----
            $new_leads = 0;
            if (in_array($type_id, array(4, 13))) {
                try {
                    $new_leads = $this->_cnt($this->Menu_model->GetOldAddNewLeadComapny($uid));
                } catch (Exception $e) { $new_leads = 0; }
            }

            // ----- GATE 4: manager checks (role 15) -----
            $manager = array(
                'applies'           => ($type_id == 15),
                'day_check'         => array('pending' => 0, 'username' => ''),
                'task_check'        => array('pending' => 0, 'username' => ''),
                'status_task_check' => array('pending' => 0, 'username' => ''),
            );
            if ($type_id == 15) {
                try {
                    $checkDate = $this->Menu_model->findSpecialDate($today);
                    $manager['day_check'] = $this->_manager_pending(
                        $this->Menu_model->CheckUserDayCheckCompleteOrNot($uid, $checkDate),
                        $this->Menu_model->CheckTeamMappingsDatas($uid, $checkDate)
                    );
                    $manager['task_check'] = $this->_manager_pending(
                        $this->Menu_model->CheckUserTaskCheckCompleteOrNot($uid, $checkDate),
                        $this->Menu_model->GetAllBDTaskTodaysOnMandate($uid, $checkDate)
                    );
                    $manager['status_task_check'] = $this->_manager_pending(
                        $this->Menu_model->CheckUserStatusChnageTaskCheckCompleteOrNot($uid, $checkDate),
                        $this->Menu_model->StatusChnageTaskCheckBYLM($uid, $checkDate)
                    );
                } catch (Exception $e) {
                    // graceful: leave zeros
                }
            }

            // ----- GATE 5: planner-approval state (approvalchain_20260610) -----
            // Read the BD's own planner_approved row for tdate. status is the
            // production gate: pending (NULL/0) blocks the day's tasks from going
            // active (production INNER JOINs planner_approved.approved_status=1),
            // approved (1) clears it, rejected (2) sends it back. Read-only.
            $planner_approval = array(
                'applies'   => in_array($type_id, array(3, 24)),
                'planner_id'=> 0,
                'status'    => 'none',
                'pending'   => false,
            );
            if ($planner_approval['applies']) {
                try {
                    $pa = $this->db->query(
                        "SELECT id, approved_status FROM planner_approved" .
                        " WHERE user_id = ? AND request_date = ? ORDER BY id DESC LIMIT 1",
                        array($uid, $tdate)
                    )->row_array();
                    if ($pa) {
                        $st = $pa['approved_status'];
                        if ($st === null || (int)$st === 0) {
                            $sn = 'pending';
                        } else if ((int)$st === 1) {
                            $sn = 'approved';
                        } else {
                            $sn = 'rejected';
                        }
                        $planner_approval['planner_id'] = (int)$pa['id'];
                        $planner_approval['status']     = $sn;
                        $planner_approval['pending']    = ($sn === 'pending');
                    }
                } catch (Exception $e) {
                    // graceful: leave defaults (status none)
                }
            }

            // ----- PAYLOAD PARITY -----
            $pendingt = $totalt = $barg = $vm_meetings = $autotasktimenew = 0;
            try { $pendingt        = $this->_cnt($this->Menu_model->get_pendingt($uid, $tdate)); } catch (Exception $e) {}
            try { $totalt          = $this->_cnt($this->Menu_model->get_totalt($uid, $tdate)); } catch (Exception $e) {}
            try { $barg            = $this->_cnt($this->Menu_model->get_bargdetail($uid, $tdate)); } catch (Exception $e) {}
            try { $vm_meetings     = $this->_cnt($this->Menu_model->GetTodaysVirtualMeeting($uid, $tdate)); } catch (Exception $e) {}
            try { $autotasktimenew = $this->_cnt($this->Menu_model->autotasktimenew($uid, $tdate)); } catch (Exception $e) {}

            // per-type counts: 1=call, 2=email, 3=meeting
            $callr = $emailr = $meetingr = 0;
            try { $callr    = $this->_cnt($this->Menu_model->get_callingr(1, $uid, $tdate)); } catch (Exception $e) {}
            try { $emailr   = $this->_cnt($this->Menu_model->get_callingr(2, $uid, $tdate)); } catch (Exception $e) {}
            try { $meetingr = $this->_cnt($this->Menu_model->get_callingr(3, $uid, $tdate)); } catch (Exception $e) {}

            // pat/tat per type (planned vs total): patc/tatc=call, pate/tate=email, patm/tatm=meeting
            $patc = $tatc = $pate = $tate = $patm = $tatm = 0;
            try { $patc = $this->_cnt($this->Menu_model->get_pat(1, $uid, $tdate)); } catch (Exception $e) {}
            try { $tatc = $this->_cnt($this->Menu_model->get_tat(1, $uid, $tdate)); } catch (Exception $e) {}
            try { $pate = $this->_cnt($this->Menu_model->get_pat(2, $uid, $tdate)); } catch (Exception $e) {}
            try { $tate = $this->_cnt($this->Menu_model->get_tat(2, $uid, $tdate)); } catch (Exception $e) {}
            try { $patm = $this->_cnt($this->Menu_model->get_pat(3, $uid, $tdate)); } catch (Exception $e) {}
            try { $tatm = $this->_cnt($this->Menu_model->get_tat(3, $uid, $tdate)); } catch (Exception $e) {}

            $this->json_out(array(
                'ok'      => true,
                'uid'     => $uid,
                'type_id' => $type_id,
                'date'    => $tdate,
                'gates'   => array(
                    'day_not_started'  => ($day_gate_applies && !$day_started),
                    'day_gate_applies' => $day_gate_applies,
                    'pending_autotask' => $pending_autotask,
                    'new_leads'        => $new_leads,
                    'manager'          => $manager,
                    'planner_approval' => $planner_approval,
                ),
                'payload' => array(
                    'pendingt'        => $pendingt,
                    'totalt'          => $totalt,
                    'barg'            => $barg,
                    'vm_meetings'     => $vm_meetings,
                    'autotasktimenew' => $autotasktimenew,
                    'callr'           => $callr,
                    'emailr'          => $emailr,
                    'meetingr'        => $meetingr,
                    'patc'            => $patc,
                    'tatc'            => $tatc,
                    'pate'            => $pate,
                    'tate'            => $tate,
                    'patm'            => $patm,
                    'tatm'            => $tatm,
                ),
            ));
        } catch (Exception $e) {
            // never 500 on the happy path; degrade to an honest error envelope.
            $this->json_out(array('ok' => false, 'error' => 'server_error', 'detail' => $e->getMessage()), 200);
        }
    }
}

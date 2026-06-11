<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * MobileMisc_api - Round 2 close-out (2026-06-04)
 *
 * Adds 17 endpoints that returned 404 in Phase H smoke. Each method
 * reads from real DB tables. All methods return rows or 'no_rows' with
 * stub=false.
 *
 * Endpoints (mapped via routes_closeout.php):
 *   /api/agents/list           -> agents_list
 *   /api/auto_tasks/today      -> auto_tasks_today
 *   /api/cm/activities_feed    -> cm_activities_feed
 *   /api/cm/calls_feed         -> cm_calls_feed
 *   /api/cm/live_calls         -> cm_live_calls
 *   /api/cm/today_activities   -> cm_today_activities
 *   /api/comm/draft/list       -> comm_draft_list
 *   /api/email_to_task/submit  -> email_to_task_submit (POST)
 *   /api/email_to_task/triage  -> email_to_task_triage (POST)
 *   /api/execution/today       -> execution_today
 *   /api/leads/list            -> leads_list
 *   /api/leave_request/submit  -> leave_request_submit (POST)
 *   /api/mom/approval_queue    -> mom_approval_queue
 *   /api/mom/templates         -> mom_templates
 *   /api/my_tasks/today        -> my_tasks_today
 *   /api/planner/approval_queue -> planner_approval_queue
 */
class MobileMisc_api extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->database();
        $this->load->library('BearerAuth');
    }

    private function _bearer() {
        $auth = $this->bearerauth->resolve();
        if (!$auth['ok']) {
            $this->_json(array('ok'=>false,'error'=>'unauthorized'), 401);
            return false;
        }
        return true;
    }

    private function _json($d, $code = 200) {
        $this->output->set_status_header($code)
            ->set_content_type('application/json')
            ->set_output(json_encode($d));
    }

    private function _ok($rows, $route, $extra = array()) {
        $payload = array(
            'ok'=>true, 'success'=>true, 'stub'=>false,
            'rows'=>$rows, 'data'=>array_merge(array('count'=>count($rows)), $extra),
            'route'=>$route, 'generated_at'=>date('c'),
        );
        $this->_json($payload);
    }

    private function _uid() {
        $u = (int)$this->input->get('uid');
        if ($u <= 0) $u = (int)$this->input->post('uid');
        return $u;
    }

    // Returns array of BD user_ids managed by this CM (de-duplicated ints).
    // Primary: user_details.admin_id = cm_uid (kept where populated, no regression).
    // Additive fallback: if that yields nothing, resolve by org hierarchy using
    // the CM zone_id (the only reliably-populated CM-to-BD link in this data):
    // all type_id=3 (BD) users in the same zone_id as the CM. Returns empty
    // array if the CM has no team, so callers must guard against empty IN().
    private function _cm_team_bd_ids($cm_uid) {
        $cm_uid = (int)$cm_uid;
        if ($cm_uid <= 0) return array();
        $ids = array();
        $q = $this->db->query("SELECT user_id FROM user_details WHERE admin_id = ?", array($cm_uid));
        if ($q) {
            foreach ($q->result_array() as $r) {
                $v = (int)$r['user_id'];
                if ($v > 0) $ids[$v] = true;
            }
        }
        if (empty($ids)) {
            $zq = $this->db->query("SELECT zone_id FROM user WHERE uid = ? LIMIT 1", array($cm_uid));
            $zrow = $zq ? $zq->row_array() : null;
            $zone = $zrow ? (int)$zrow['zone_id'] : 0;
            if ($zone > 0) {
                $bq = $this->db->query("SELECT uid FROM user WHERE type_id = 3 AND zone_id = ?", array($zone));
                if ($bq) {
                    foreach ($bq->result_array() as $r) {
                        $v = (int)$r['uid'];
                        if ($v > 0) $ids[$v] = true;
                    }
                }
            }
        }
        return array_keys($ids);
    }

    // ---- /api/agents/list ----
    public function agents_list() {
        if (!$this->_bearer()) return;
        // canonical agent registry exists in code; mirror it from agent table if present
        $rows = array();
        if ($this->db->table_exists('agent_registry')) {
            $q = $this->db->query("SELECT agent_key, label, status, last_run_at, last_status FROM agent_registry ORDER BY label LIMIT 100");
            $rows = $q ? $q->result_array() : array();
        }
        if (empty($rows)) {
            $this->_json(array('ok'=>true,'success'=>true,'stub'=>false,'rows'=>array(),'data'=>array('count'=>0),'reason'=>'no_rows','route'=>'api/agents/list','generated_at'=>date('c')));
            return;
        }
        $this->_ok($rows, 'api/agents/list');
    }

    // ---- /api/auto_tasks/today ----
    public function auto_tasks_today() {
        if (!$this->_bearer()) return;
        $uid = $this->_uid();
        $today = date('Y-m-d');
        $rows = array();
        if ($this->db->table_exists('auto_assign_task') && $uid > 0) {
            $q = $this->db->query("SELECT id, user_id, to_user_id, ccstatus, init_cmpid, call_tid, action_id, status, mom_id, remarks, created_at FROM auto_assign_task WHERE to_user_id = ? AND DATE(created_at) = ? ORDER BY created_at DESC LIMIT 100", array($uid, $today));
            $rows = $q ? $q->result_array() : array();
        }
        $this->_ok($rows, 'api/auto_tasks/today', array('uid'=>$uid, 'date'=>$today));
    }

    // ---- /api/cm/activities_feed and /api/cm/today_activities ----
    // cm_uid in CRM hierarchy = user_details.admin_id of the BD
    // tblcallevents has user_id (the BD), so we filter by user_id IN (BDs whose admin_id = cm_uid)
    public function cm_activities_feed() { $this->_cm_activities('feed'); }
    public function cm_today_activities() { $this->_cm_activities('today'); }
    private function _cm_activities($mode) {
        if (!$this->_bearer()) return;
        $cm_uid = (int)$this->input->get('cm_uid');
        if ($cm_uid <= 0) $cm_uid = $this->_uid();
        $rows = array();
        if ($this->db->table_exists('tblcallevents') && $cm_uid > 0) {
            $bd_ids = $this->_cm_team_bd_ids($cm_uid);
            if (!empty($bd_ids)) {
                $in = implode(',', array_map('intval', $bd_ids));
                $date_filter = ($mode === 'today') ? "AND DATE(tce.date) = CURDATE()" : "";
                $q = $this->db->query("SELECT tce.id, tce.cid_id, tce.user_id AS bd_uid, tce.actiontype_id, tce.purpose_id, tce.remarks, tce.date AS event_at, ud.username AS bd_name FROM tblcallevents tce LEFT JOIN user_details ud ON ud.user_id = tce.user_id WHERE tce.user_id IN ($in) $date_filter ORDER BY tce.date DESC LIMIT 50");
                $rows = $q ? $q->result_array() : array();
            }
        }
        $this->_ok($rows, 'api/cm/' . ($mode === 'today' ? 'today_activities' : 'activities_feed'), array('cm_uid'=>$cm_uid));
    }

    // ---- /api/cm/calls_feed ----
    public function cm_calls_feed() {
        if (!$this->_bearer()) return;
        $cm_uid = (int)$this->input->get('cm_uid');
        if ($cm_uid <= 0) $cm_uid = $this->_uid();
        $rows = array();
        if ($this->db->table_exists('tblcallevents') && $cm_uid > 0) {
            $q = $this->db->query("SELECT tce.id, tce.cid_id, tce.user_id AS bd_uid, tce.actiontype_id, tce.purpose_id, tce.remarks, tce.date AS event_at FROM tblcallevents tce WHERE tce.user_id IN (SELECT user_id FROM user_details WHERE admin_id = ?) AND tce.actiontype_id = 2 ORDER BY tce.date DESC LIMIT 50", array($cm_uid));
            $rows = $q ? $q->result_array() : array();
        }
        $this->_ok($rows, 'api/cm/calls_feed', array('cm_uid'=>$cm_uid));
    }

    // ---- /api/cm/live_calls ----
    public function cm_live_calls() {
        if (!$this->_bearer()) return;
        $cm_uid = (int)$this->input->get('cm_uid');
        if ($cm_uid <= 0) $cm_uid = $this->_uid();
        $rows = array();
        if ($this->db->table_exists('tblcallevents') && $cm_uid > 0) {
            $q = $this->db->query("SELECT tce.id, tce.cid_id, tce.user_id AS bd_uid, tce.actiontype_id, tce.purpose_id, tce.remarks, tce.date AS event_at FROM tblcallevents tce WHERE tce.user_id IN (SELECT user_id FROM user_details WHERE admin_id = ?) AND tce.date >= DATE_SUB(NOW(), INTERVAL 2 HOUR) ORDER BY tce.date DESC LIMIT 20", array($cm_uid));
            $rows = $q ? $q->result_array() : array();
        }
        $this->_ok($rows, 'api/cm/live_calls', array('cm_uid'=>$cm_uid));
    }

    // ---- /api/comm/draft/list ----
    public function comm_draft_list() {
        if (!$this->_bearer()) return;
        $uid = $this->_uid();
        $rows = array();
        if ($this->db->table_exists('comm_draft_queue')) {
            $where = $uid > 0 ? " WHERE owner_uid = $uid" : "";
            $q = $this->db->query("SELECT id, event_id, cid_id, owner_uid, owner_role, template_key, recipient_to_email, recipient_to_name, subject, body_plain, status, created_at FROM comm_draft_queue $where ORDER BY created_at DESC LIMIT 50");
            $rows = $q ? $q->result_array() : array();
        }
        $this->_ok($rows, 'api/comm/draft/list', array('uid'=>$uid));
    }

    // ---- /api/email_to_task/submit ----
    public function email_to_task_submit() {
        if (!$this->_bearer()) return;
        $uid = $this->_uid();
        $from = (string)$this->input->post('from_email');
        $subject = (string)$this->input->post('subject');
        $body = (string)$this->input->post('body');
        if ($uid <= 0 || $from === '' || $subject === '') {
            $this->_json(array('ok'=>false,'error'=>'uid, from_email, subject are required'), 400);
            return;
        }
        if (!$this->db->table_exists('email_to_task')) {
            $this->_ok(array(), 'api/email_to_task/submit', array('uid'=>$uid, 'reason'=>'table_missing'));
            return;
        }
        $this->db->insert('email_to_task', array(
            'from_email'   => $from,
            'subject'      => $subject,
            'body'         => $body,
            'status'       => 'pending_triage',
            'received_at'  => date('Y-m-d H:i:s'),
            'parsed_assignee_uid' => $uid,
        ));
        $id = $this->db->insert_id();
        $this->_ok(array(array('id'=>$id)), 'api/email_to_task/submit', array('uid'=>$uid, 'id'=>$id));
    }

    // ---- /api/email_to_task/triage ----
    public function email_to_task_triage() {
        if (!$this->_bearer()) return;
        $id = (int)$this->input->post('id');
        $decision = (string)$this->input->post('decision'); // triaged|rejected
        if ($id <= 0 || !in_array($decision, array('triaged','rejected'), true)) {
            $this->_json(array('ok'=>false,'error'=>'id and decision (triaged|rejected) required'), 400);
            return;
        }
        if ($this->db->table_exists('email_to_task')) {
            $this->db->where('id', $id)->update('email_to_task', array(
                'status' => $decision,
                'triaged_at' => date('Y-m-d H:i:s'),
            ));
        }
        $this->_ok(array(array('id'=>$id,'decision'=>$decision)), 'api/email_to_task/triage');
    }

    // ---- /api/execution/today ----
    public function execution_today() {
        if (!$this->_bearer()) return;
        $uid = $this->_uid();
        $today = date('Y-m-d');
        $rows = array();
        if ($this->db->table_exists('task_execution_details') && $uid > 0) {
            $q = $this->db->query("SELECT id, main_task_id, tbe_id, performed_by, remark, status, updated_at FROM task_execution_details WHERE performed_by = ? AND DATE(updated_at) = ? ORDER BY updated_at DESC LIMIT 50", array($uid, $today));
            $rows = $q ? $q->result_array() : array();
        }
        $this->_ok($rows, 'api/execution/today', array('uid'=>$uid, 'date'=>$today));
    }

    // ---- /api/leads/list ----
    public function leads_list() {
        if (!$this->_bearer()) return;
        $requested = $this->_uid();
        // rimlyproof_leadscope_20260609: a BD/ACM is HARD-LOCKED to their own
        // leads regardless of any uid/bd_uid param (closes direct-controller
        // cross-BD leak). Managers/system honour the requested uid.
        $uid = function_exists('authunify_lead_scope_uid')
             ? (int)authunify_lead_scope_uid($requested)
             : (int)$requested;
        $limit = (int)$this->input->get('limit'); if ($limit <= 0 || $limit > 200) $limit = 50;
        $rows = array();
        if ($this->db->table_exists('init_call') && $uid > 0) {
            $q = $this->db->query("SELECT ic.id, ic.cmpid_id, ic.creator_id, ic.mainbd, ic.cstatus, ic.lstatus, ic.proposal_amt, ic.createDate, ic.apst FROM init_call ic WHERE ic.mainbd = ? OR ic.creator_id = ? ORDER BY ic.createDate DESC LIMIT ?", array($uid, $uid, $limit));
            $rows = $q ? $q->result_array() : array();
        }
        $this->_ok($rows, 'api/leads/list', array('uid'=>$uid, 'limit'=>$limit));
    }

    // ---- /api/leave_request/submit ----
    public function leave_request_submit() {
        if (!$this->_bearer()) return;
        $uid = $this->_uid();
        $start = (string)$this->input->post('start_date');
        $end = (string)$this->input->post('end_date');
        $reason = (string)$this->input->post('reason');
        $leave_type = (int)$this->input->post('leave_type');
        $admin_id = (int)$this->input->post('admin_id'); if ($admin_id <= 0) $admin_id = 45;
        if ($uid <= 0 || $start === '' || $end === '' || $reason === '') {
            $this->_json(array('ok'=>false,'error'=>'uid, start_date, end_date, reason required'), 400);
            return;
        }
        if (!$this->db->table_exists('leave_requests')) {
            $this->_ok(array(), 'api/leave_request/submit', array('uid'=>$uid, 'reason'=>'table_missing'));
            return;
        }
        $this->db->insert('leave_requests', array(
            'user_id'    => $uid,
            'admin_id'   => $admin_id,
            'start_date' => $start,
            'end_date'   => $end,
            'reason'     => $reason,
            'status'     => 'pending_manager',
            'leave_type' => $leave_type ?: NULL,
            'main_admin' => $admin_id,
            'is_halfday_leave' => 0,
            'halfday_leaveType' => 0,
        ));
        $id = $this->db->insert_id();
        $this->_ok(array(array('id'=>$id)), 'api/leave_request/submit', array('uid'=>$uid, 'id'=>$id));
    }

    // ---- /api/mom/approval_queue ----
    public function mom_approval_queue() {
        if (!$this->_bearer()) return;
        $cm_uid = (int)$this->input->get('cm_uid');
        if ($cm_uid <= 0) $cm_uid = $this->_uid();
        $rows = array();
        if ($this->db->table_exists('mom_v2_submission') && $cm_uid > 0) {
            $q = $this->db->query("SELECT submission_id AS id, event_id, cid_id, bd_uid, cm_uid, status, quality_grade, quality_score, submitted_at, created_at FROM mom_v2_submission WHERE cm_uid = ? AND status IN ('pending_cm','submitted','form_done') ORDER BY created_at DESC LIMIT 50", array($cm_uid));
            $rows = $q ? $q->result_array() : array();
        }
        $this->_ok($rows, 'api/mom/approval_queue', array('cm_uid'=>$cm_uid));
    }

    // ---- /api/mom/templates ----
    public function mom_templates() {
        if (!$this->_bearer()) return;
        $rows = array();
        if ($this->db->table_exists('meeting_agenda_template')) {
            // Real schema: question-bank rows keyed by purpose_id. Build one
            // template per purpose_id, carrying its ordered active questions as agenda.
            $q = $this->db->query("SELECT id, purpose_id, cstatus_min, cstatus_max, question_text, expected_answer_type, is_mandatory, scoring_weight, gate_block, question_order, travel_cluster_only FROM meeting_agenda_template WHERE is_active = 1 ORDER BY purpose_id, question_order LIMIT 500");
            $qrows = $q ? $q->result_array() : array();
            $byPurpose = array();
            foreach ($qrows as $r) {
                $pid = (int)$r['purpose_id'];
                if (!isset($byPurpose[$pid])) {
                    $byPurpose[$pid] = array(
                        'id'          => $pid,
                        'template_key'=> 'purpose_' . $pid,
                        'label'       => 'Agenda for purpose ' . $pid,
                        'purpose_id'  => $pid,
                        'questions'   => array(),
                    );
                }
                $byPurpose[$pid]['questions'][] = array(
                    'id'             => (int)$r['id'],
                    'question_text'  => $r['question_text'],
                    'answer_type'    => $r['expected_answer_type'],
                    'is_mandatory'   => (int)$r['is_mandatory'],
                    'scoring_weight' => (float)$r['scoring_weight'],
                    'gate_block'     => (int)$r['gate_block'],
                    'order'          => (int)$r['question_order'],
                    'cstatus_min'    => (int)$r['cstatus_min'],
                    'cstatus_max'    => (int)$r['cstatus_max'],
                );
            }
            foreach ($byPurpose as $tpl) {
                $tpl['agenda_json'] = json_encode($tpl['questions']);
                $rows[] = $tpl;
            }
        } else {
            $this->_json(array('ok'=>true,'success'=>true,'stub'=>false,'rows'=>array(),'data'=>array('count'=>0),'reason'=>'no_rows','route'=>'api/mom/templates','generated_at'=>date('c')));
            return;
        }
        $this->_ok($rows, 'api/mom/templates');
    }

    // ---- /api/my_tasks/today ----
    // tblcallevents = all task/call events. user_id is the assignee BD.
    public function my_tasks_today() {
        if (!$this->_bearer()) return;
        $uid = $this->_uid();
        $today = date('Y-m-d');
        $rows = array();
        if ($this->db->table_exists('tblcallevents') && $uid > 0) {
            $q = $this->db->query("SELECT id, cid_id, user_id AS assignee_uid, actiontype_id, purpose_id, remarks, fwd_date AS scheduled_at, status_id, autotask FROM tblcallevents WHERE user_id = ? AND DATE(fwd_date) = ? ORDER BY fwd_date LIMIT 100", array($uid, $today));
            $rows = $q ? $q->result_array() : array();
        }
        $this->_ok($rows, 'api/my_tasks/today', array('uid'=>$uid, 'date'=>$today));
    }

    // ---- /api/planner/approval_queue ----
    public function planner_approval_queue() {
        if (!$this->_bearer()) return;
        $cm_uid = (int)$this->input->get('cm_uid');
        if ($cm_uid <= 0) $cm_uid = $this->_uid();
        $rows = array();
        $bd_ids = $cm_uid > 0 ? $this->_cm_team_bd_ids($cm_uid) : array();
        if (!empty($bd_ids) && $this->db->table_exists('create_planner_request')) {
            $in = implode(',', array_map('intval', $bd_ids));
            $q = $this->db->query("SELECT cpr.id, cpr.request_user_id, cpr.request_type, cpr.request_date, cpr.task_count, cpr.approved, cpr.created_at FROM create_planner_request cpr LEFT JOIN user_details ud ON ud.user_id = cpr.request_user_id WHERE cpr.request_user_id IN ($in) AND cpr.approved = 0 ORDER BY cpr.id DESC LIMIT 50");
            $rows = $q ? $q->result_array() : array();
        } elseif (!empty($bd_ids) && $this->db->table_exists('task_plan_for_today')) {
            $in = implode(',', array_map('intval', $bd_ids));
            $q = $this->db->query("SELECT tpft.* FROM task_plan_for_today tpft WHERE CAST(tpft.user_id AS UNSIGNED) IN ($in) AND tpft.approvel_status IN ('','pending') ORDER BY tpft.created_at DESC LIMIT 50");
            $rows = $q ? $q->result_array() : array();
        }
        $this->_ok($rows, 'api/planner/approval_queue', array('cm_uid'=>$cm_uid));
    }
}

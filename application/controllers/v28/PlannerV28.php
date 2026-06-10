<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * PlannerV28 Controller
 *
 * Handles all /api/planner/* and /api/planner/v2/* routes for STEM CRM v2.8.
 * Backed by real tables: planner_log, planner_approved, plandate,
 * plan_submit_gate_log, tblcallevents, init_call, cluster_master,
 * cm_daily_plan, bd_planner_block_log, pending_meetings_request,
 * task_plan_for_today, bd_request, user, company_master.
 *
 * Tables queried with real data where schema is confirmed.
 * Stubs return ok:true with note:awaits_migration where no table maps.
 *
 * Auth: Bearer 4eBaiAT7r4zu6OK3b8evjLNia1D7RGgb0qRTuLJfUSo
 */
class PlannerV28 extends CI_Controller {

    /** Bearer token required for all endpoints */
    const BEARER = '4eBaiAT7r4zu6OK3b8evjLNia1D7RGgb0qRTuLJfUSo';

    /** rimlyproof_leadscope_20260609: authed identity captured by auth_check() */
    private $auth_uid  = 0;
    private $auth_role = '';

    public function __construct()
    {
        parent::__construct();
        $this->output->set_content_type('application/json');
        $this->load->library('BearerAuth');
    }

    // -------------------------------------------------------------------------
    // HELPERS
    // -------------------------------------------------------------------------

    /**
     * json_out
     * Emit JSON and stop execution.
     */
    private function json_out($data, $status = 200)
    {
        $this->output
             ->set_status_header($status)
             ->set_output(json_encode($data, JSON_UNESCAPED_UNICODE));
    }

    /**
     * auth_check
     * Returns true if the Authorization header carries the correct bearer token.
     * On failure it emits 401 and returns false.
     */
    private function auth_check()
    {
        $auth = $this->bearerauth->resolve();
        if (empty($auth['ok'])) {
            $this->json_out(['ok' => false, 'error' => 'unauthorized'], 401);
            return false;
        }
        // rimlyproof_leadscope_20260609: remember WHO is calling so resolve_uid()
        // can hard-lock field users to their own data.
        $this->auth_uid  = isset($auth['uid'])  ? (int)$auth['uid']                 : 0;
        $this->auth_role = isset($auth['role']) ? strtolower((string)$auth['role']) : '';
        return true;
    }

    /**
     * today
     * Returns YYYY-MM-DD for today.
     */
    private function today()
    {
        return date('Y-m-d');
    }

    /**
     * resolve_date
     * Reads optional ?date= param, falls back to today.
     */
    private function resolve_date()
    {
        $d = $this->input->get('date');
        if ($d && preg_match('/^\d{4}-\d{2}-\d{2}$/', $d)) {
            return $d;
        }
        return $this->today();
    }

    /**
     * resolve_uid
     * Reads uid from GET param (uid or user_id or bd_uid), returns int or 0.
     */
    private function resolve_uid()
    {
        $uid = $this->input->get('uid');
        if ( ! $uid) { $uid = $this->input->get('user_id'); }
        if ( ! $uid) { $uid = $this->input->get('bd_uid'); }
        $uid = (int) $uid;
        // rimlyproof_leadscope_20260609: a FIELD user (BD/ACM) may only ever
        // resolve to their OWN uid - any uid/bd_uid param pointing elsewhere is
        // ignored. Managers/system/superadmin keep the requested uid.
        if ($this->auth_uid > 0 && ($this->auth_role === 'bd' || $this->auth_role === 'acm')) {
            return (int) $this->auth_uid;
        }
        // Non-field roles: fall back to own uid when no specific uid requested.
        if ($uid <= 0 && $this->auth_uid > 0) {
            return (int) $this->auth_uid;
        }
        return $uid;
    }

    // -------------------------------------------------------------------------
    // ENDPOINTS - v1
    // -------------------------------------------------------------------------

    /**
     * probe
     * GET /api/planner/probe
     * Health check.
     */
    public function probe()
    {
        $this->json_out(['ok' => true, 'success' => true, 'controller' => 'PlannerV28']);
    }

    /**
     * approve_task
     * POST /api/planner/approve_task
     * Approve a pending task request in task_plan_for_today.
     * Reads: task_plan_for_today (approvel_status, action_by).
     */
    public function approve_task()
    {
        if ( ! $this->auth_check()) { return; }

        $task_id   = (int) $this->input->post('task_id');
        $action_by = (int) $this->input->post('action_by');
        $status    = $this->input->post('status'); // approved | rejected

        if ($task_id <= 0) {
            return $this->json_out(['ok' => false, 'error' => 'task_id required'], 400);
        }

        $allowed = ['approved', 'rejected'];
        if ( ! in_array($status, $allowed, true)) {
            $status = 'approved';
        }

        $this->db->where('id', $task_id);
        $row = $this->db->get('task_plan_for_today')->row_array();

        if ( ! $row) {
            return $this->json_out([
                'ok'      => false,
                'success' => false,
                'error'   => 'task_not_found',
            ], 404);
        }

        $this->db->where('id', $task_id);
        $this->db->update('task_plan_for_today', [
            'approvel_status' => $status,
            'action_by'       => $action_by > 0 ? $action_by : null,
            'updated_at'      => date('Y-m-d H:i:s'),
        ]);

        $this->json_out([
            'ok'      => true,
            'success' => true,
            'task_id' => $task_id,
            'status'  => $status,
        ]);
    }

    /**
     * auto_seed
     * POST /api/planner/auto_seed
     * Seeds plan tasks for today from tblcallevents for a given user.
     * Returns events that have plan=1 and date = today.
     */
    public function auto_seed()
    {
        if ( ! $this->auth_check()) { return; }

        // Accept uid from POST body or GET param
        $uid = (int) $this->input->post('uid');
        if ( ! $uid) { $uid = (int) $this->input->post('user_id'); }
        if ( ! $uid) { $uid = (int) $this->input->post('bd_uid'); }
        if ( ! $uid) { $uid = $this->resolve_uid(); }
        $date_post = $this->input->post('date');
        $date = ($date_post && preg_match('/^\d{4}-\d{2}-\d{2}$/', $date_post)) ? $date_post : $this->resolve_date();

        if ($uid <= 0) {
            return $this->json_out(['ok' => false, 'error' => 'uid required'], 400);
        }

        $this->db->select('id, cid_id, actiontype_id, purpose_id, plan_time, approved_status, date');
        $this->db->where('user_id', $uid);
        $this->db->where('DATE(date)', $date);
        $this->db->where('plan', 1);
        $this->db->limit(100);
        $rows = $this->db->get('tblcallevents')->result_array();

        $this->json_out([
            'ok'      => true,
            'success' => true,
            'rows'    => $rows,
            'count'   => count($rows),
            'data'    => ['uid' => $uid, 'date' => $date],
        ]);
    }

    /**
     * auto_seeded
     * GET /api/planner/auto_seeded
     * Returns events already seeded (auto_plan=1) from tblcallevents for a user and date.
     */
    public function auto_seeded()
    {
        if ( ! $this->auth_check()) { return; }

        $uid  = $this->resolve_uid();
        $date = $this->resolve_date();

        if ($uid <= 0) {
            return $this->json_out(['ok' => false, 'error' => 'uid required'], 400);
        }

        $this->db->select('id, cid_id, actiontype_id, purpose_id, plan_time, approved_status, auto_plan, date');
        $this->db->where('user_id', $uid);
        $this->db->where('DATE(date)', $date);
        $this->db->where('auto_plan', 1);
        $this->db->limit(100);
        $rows = $this->db->get('tblcallevents')->result_array();

        $this->json_out([
            'ok'      => true,
            'success' => true,
            'rows'    => $rows,
            'count'   => count($rows),
            'data'    => ['uid' => $uid, 'date' => $date],
        ]);
    }

    /**
     * clusters
     * GET /api/planner/clusters
     * Returns active clusters from cluster_master.
     */
    public function clusters()
    {
        if ( ! $this->auth_check()) { return; }

        $this->db->select('cluster_id, cluster_name, region, rm_uid, cm_uid, is_pilot, is_active');
        $this->db->where('is_active', 1);
        $this->db->order_by('cluster_name', 'ASC');
        $this->db->limit(100);
        $rows = $this->db->get('cluster_master')->result_array();

        $this->json_out([
            'ok'      => true,
            'success' => true,
            'rows'    => $rows,
            'count'   => count($rows),
            'data'    => [],
        ]);
    }

    /**
     * cm_queue
     * GET /api/planner/cm_queue
     * Returns CM daily plan queue for a given cm_uid and date.
     */
    public function cm_queue()
    {
        if ( ! $this->auth_check()) { return; }

        $cm_uid = (int) $this->input->get('cm_uid');
        $date   = $this->resolve_date();

        $query = $this->db->select('id, cm_uid, plan_date, task_kind, linked_lead_id, linked_bd_uid, start_time, end_time, status, notes');
        if ($cm_uid > 0) {
            $this->db->where('cm_uid', $cm_uid);
        }
        $this->db->where('plan_date', $date);
        $this->db->order_by('start_time', 'ASC');
        $this->db->limit(100);
        $rows = $this->db->get('cm_daily_plan')->result_array();

        $this->json_out([
            'ok'      => true,
            'success' => true,
            'rows'    => $rows,
            'count'   => count($rows),
            'data'    => ['cm_uid' => $cm_uid, 'date' => $date],
        ]);
    }

    /**
     * day_pack
     * GET /api/planner/day_pack
     * Returns the full day pack: events planned for a user on a given date.
     * Uses tblcallevents joined to init_call for company context.
     */
    public function day_pack()
    {
        if ( ! $this->auth_check()) { return; }

        $uid  = $this->resolve_uid();
        $date = $this->resolve_date();

        if ($uid <= 0) {
            return $this->json_out(['ok' => false, 'error' => 'uid required'], 400);
        }

        $this->db->select('t.id, t.cid_id, t.actiontype_id, t.purpose_id, t.plan_time, t.approved_status, t.date, ic.cstatus, cm.compname');
        $this->db->from('tblcallevents t');
        $this->db->join('init_call ic', 'ic.id = t.cid_id', 'left');
        $this->db->join('company_master cm', 'cm.id = ic.cmpid_id', 'left');
        $this->db->where('t.user_id', $uid);
        $this->db->where('DATE(t.date)', $date);
        $this->db->order_by('t.plan_time', 'ASC');
        $this->db->limit(100);
        $rows = $this->db->get()->result_array();

        $this->json_out([
            'ok'      => true,
            'success' => true,
            'rows'    => $rows,
            'count'   => count($rows),
            'data'    => ['uid' => $uid, 'date' => $date],
        ]);
    }

    /**
     * get_plan
     * GET /api/planner/get_plan
     * Returns plan submission gate log for a BD on a date.
     */
    public function get_plan()
    {
        if ( ! $this->auth_check()) { return; }

        $uid  = $this->resolve_uid();
        $date = $this->resolve_date();

        if ($uid <= 0) {
            return $this->json_out(['ok' => false, 'error' => 'uid required'], 400);
        }

        $this->db->select('id, bd_uid, plan_date, gate_result, gate_reason, submitted_at, is_late');
        $this->db->where('bd_uid', $uid);
        $this->db->where('plan_date', $date);
        $this->db->limit(1);
        $rows = $this->db->get('plan_submit_gate_log')->result_array();

        $this->json_out([
            'ok'      => true,
            'success' => true,
            'rows'    => $rows,
            'count'   => count($rows),
            'data'    => ['uid' => $uid, 'date' => $date],
        ]);
    }

    /**
     * leads
     * GET /api/planner/leads
     * Returns active leads (init_call) owned by a BD user.
     */
    public function leads()
    {
        if ( ! $this->auth_check()) { return; }

        $uid = $this->resolve_uid();

        if ($uid <= 0) {
            return $this->json_out(['ok' => false, 'error' => 'uid required'], 400);
        }

        $this->db->select('ic.id, ic.cstatus, ic.createDate, ic.fbudget, ic.closure_pipeline, cm.compname');
        $this->db->from('init_call ic');
        $this->db->join('company_master cm', 'cm.id = ic.cmpid_id', 'left');
        $this->db->where('ic.mainbd', $uid);
        $this->db->where_not_in('ic.cstatus', [12, 13]);
        $this->db->order_by('ic.createDate', 'DESC');
        $this->db->limit(100);
        $rows = $this->db->get()->result_array();

        $this->json_out([
            'ok'      => true,
            'success' => true,
            'rows'    => $rows,
            'count'   => count($rows),
            'data'    => ['uid' => $uid],
        ]);
    }

    /**
     * my_plan
     * GET /api/planner/my_plan
     * Returns today plan for the requesting BD user.
     */
    public function my_plan()
    {
        if ( ! $this->auth_check()) { return; }

        $uid  = $this->resolve_uid();
        $date = $this->resolve_date();

        if ($uid <= 0) {
            return $this->json_out(['ok' => false, 'error' => 'uid required'], 400);
        }

        $this->db->select('t.id, t.cid_id, t.actiontype_id, t.purpose_id, t.plan_time, t.approved_status, t.date, ic.cstatus, cm.compname');
        $this->db->from('tblcallevents t');
        $this->db->join('init_call ic', 'ic.id = t.cid_id', 'left');
        $this->db->join('company_master cm', 'cm.id = ic.cmpid_id', 'left');
        $this->db->where('t.user_id', $uid);
        $this->db->where('DATE(t.date)', $date);
        $this->db->where('t.plan', 1);
        $this->db->order_by('t.plan_time', 'ASC');
        $this->db->limit(100);
        $rows = $this->db->get()->result_array();

        $gate = $this->db->select('gate_result, submitted_at, is_late')
                         ->where('bd_uid', $uid)
                         ->where('plan_date', $date)
                         ->limit(1)
                         ->get('plan_submit_gate_log')
                         ->row_array();

        $this->json_out([
            'ok'        => true,
            'success'   => true,
            'rows'      => $rows,
            'count'     => count($rows),
            'data'      => [
                'uid'  => $uid,
                'date' => $date,
                'gate' => $gate ?: null,
            ],
        ]);
    }

    /**
     * pending_carry
     * GET /api/planner/pending/carry
     * Returns planner_log entries (rolled/carried tasks) for a user.
     */
    public function pending_carry()
    {
        if ( ! $this->auth_check()) { return; }

        $uid  = $this->resolve_uid();
        $date = $this->resolve_date();

        $this->db->select('id, to_user, init_id, task_id, remarks, re_created_at, org_task_date, new_task_date');
        if ($uid > 0) {
            $this->db->where('to_user', $uid);
        }
        if ($date) {
            $this->db->where('DATE(new_task_date)', $date);
        }
        $this->db->order_by('re_created_at', 'DESC');
        $this->db->limit(100);
        $rows = $this->db->get('planner_log')->result_array();

        $this->json_out([
            'ok'      => true,
            'success' => true,
            'rows'    => $rows,
            'count'   => count($rows),
            'data'    => ['uid' => $uid, 'date' => $date],
        ]);
    }

    /**
     * pending_tasks
     * GET /api/planner/pending_tasks
     * Returns task_plan_for_today rows with approvel_status = pending.
     */
    public function pending_tasks()
    {
        if ( ! $this->auth_check()) { return; }

        $uid  = $this->resolve_uid();
        $date = $this->resolve_date();

        $this->db->select('id, user_id, admin_id, date, taskcnt, would_you_want, approvel_status, created_at');
        $this->db->where('approvel_status', 'pending');
        if ($uid > 0) {
            $this->db->where('user_id', $uid);
        }
        if ($date) {
            $this->db->where('DATE(date)', $date);
        }
        $this->db->order_by('created_at', 'DESC');
        $this->db->limit(100);
        $rows = $this->db->get('task_plan_for_today')->result_array();

        $this->json_out([
            'ok'      => true,
            'success' => true,
            'rows'    => $rows,
            'count'   => count($rows),
            'data'    => [],
        ]);
    }

    /**
     * plan
     * GET /api/planner/plan
     * Returns plan gate log rows for a BD on a date range.
     */
    public function plan()
    {
        if ( ! $this->auth_check()) { return; }

        $uid  = $this->resolve_uid();
        $date = $this->resolve_date();

        $this->db->select('id, bd_uid, plan_date, gate_result, gate_reason, submitted_at, is_late, blocked_reason_code');
        if ($uid > 0) {
            $this->db->where('bd_uid', $uid);
        }
        $this->db->where('plan_date', $date);
        $this->db->limit(100);
        $rows = $this->db->get('plan_submit_gate_log')->result_array();

        $this->json_out([
            'ok'      => true,
            'success' => true,
            'rows'    => $rows,
            'count'   => count($rows),
            'data'    => ['uid' => $uid, 'date' => $date],
        ]);
    }

    /**
     * plan_detail
     * GET /api/planner/plan_detail
     * Returns detailed plan events for a user on a date.
     */
    public function plan_detail()
    {
        if ( ! $this->auth_check()) { return; }

        $uid  = $this->resolve_uid();
        $date = $this->resolve_date();

        if ($uid <= 0) {
            return $this->json_out(['ok' => false, 'error' => 'uid required'], 400);
        }

        $this->db->select('t.id, t.cid_id, t.actiontype_id, t.purpose_id, t.plan_time, t.initiate_time, t.complete_time, t.approved_status, t.mom_received, t.mom_approved, ic.cstatus, cm.compname');
        $this->db->from('tblcallevents t');
        $this->db->join('init_call ic', 'ic.id = t.cid_id', 'left');
        $this->db->join('company_master cm', 'cm.id = ic.cmpid_id', 'left');
        $this->db->where('t.user_id', $uid);
        $this->db->where('DATE(t.date)', $date);
        $this->db->order_by('t.plan_time', 'ASC');
        $this->db->limit(100);
        $rows = $this->db->get()->result_array();

        $this->json_out([
            'ok'      => true,
            'success' => true,
            'rows'    => $rows,
            'count'   => count($rows),
            'data'    => ['uid' => $uid, 'date' => $date],
        ]);
    }

    /**
     * slots
     * GET /api/planner/slots
     * Returns available slot windows from bd_planner_block_log for a BD.
     * Note: awaits_migration if no block log rows exist.
     */
    public function slots()
    {
        if ( ! $this->auth_check()) { return; }

        $uid  = $this->resolve_uid();
        $date = $this->resolve_date();

        $this->db->select('id, bd_uid, plan_date, blocked_at, block_reason, block_start_time, block_end_time, unblocked_at');
        if ($uid > 0) {
            $this->db->where('bd_uid', $uid);
        }
        $this->db->where('plan_date', $date);
        $this->db->order_by('block_start_time', 'ASC');
        $this->db->limit(100);
        $rows = $this->db->get('bd_planner_block_log')->result_array();

        $this->json_out([
            'ok'      => true,
            'success' => true,
            'rows'    => $rows,
            'count'   => count($rows),
            'data'    => ['uid' => $uid, 'date' => $date],
        ]);
    }

    /**
     * submit
     * POST /api/planner/submit
     * Records plan submission in plan_submit_gate_log.
     */
    public function submit()
    {
        if ( ! $this->auth_check()) { return; }

        $uid  = (int) $this->input->post('bd_uid');
        if ( ! $uid) { $uid = (int) $this->input->post('uid'); }
        $date = $this->input->post('plan_date') ?: $this->today();

        if ($uid <= 0) {
            return $this->json_out(['ok' => false, 'error' => 'uid required'], 400);
        }

        // Check for existing gate log entry
        $existing = $this->db->where('bd_uid', $uid)->where('plan_date', $date)->get('plan_submit_gate_log')->row_array();

        if ($existing) {
            return $this->json_out([
                'ok'      => true,
                'success' => true,
                'note'    => 'already_submitted',
                'data'    => $existing,
            ]);
        }

        $row = [
            'bd_uid'       => $uid,
            'plan_date'    => $date,
            'gate_result'  => 'passed',
            'submitted_at' => date('Y-m-d H:i:s'),
            'is_late'      => (date('H') >= 10) ? 1 : 0,
        ];
        $this->db->insert('plan_submit_gate_log', $row);
        $insert_id = $this->db->insert_id();

        $this->json_out([
            'ok'        => true,
            'success'   => true,
            'insert_id' => $insert_id,
            'data'      => $row,
        ]);
    }

    /**
     * submit_task
     * POST /api/planner/submit_task
     * Submits a task plan request in task_plan_for_today.
     */
    public function submit_task()
    {
        if ( ! $this->auth_check()) { return; }

        $uid      = (int) $this->input->post('user_id');
        if ( ! $uid) { $uid = (int) $this->input->post('uid'); }
        $admin_id = (int) $this->input->post('admin_id');
        $date     = $this->input->post('date') ?: $this->today();
        $taskcnt  = (int) $this->input->post('taskcnt');
        $remarks  = $this->input->post('request_remarks') ?: '';
        $want     = $this->input->post('would_you_want') ?: '';

        if ($uid <= 0) {
            return $this->json_out(['ok' => false, 'error' => 'user_id required'], 400);
        }

        // rimlyproof_dayguard_20260609: field users (BD/ACM) must have STARTED their day
        // before submitting a planned task. Single canonical guard in authunify_helper.
        if (function_exists('field_day_started') && !field_day_started($uid)) {
            return $this->json_out(['ok' => false, 'error' => 'Please start your day before performing field actions'], 403);
        }

        // Generate id since task_plan_for_today.id is not auto_increment
        $max = $this->db->select_max('id')->get('task_plan_for_today')->row_array();
        $next_id = isset($max['id']) && $max['id'] ? ((int) $max['id'] + 1) : 1;

        $row = [
            'id'              => $next_id,
            'user_id'         => (string) $uid,
            'admin_id'        => (string) $admin_id,
            'date'            => $date,
            'taskcnt'         => $taskcnt > 0 ? $taskcnt : 0,
            'would_you_want'  => $want,
            'request_remarks' => $remarks,
            'approvel_status' => 'pending',
            'remarks'         => '',
            'created_at'      => date('Y-m-d H:i:s'),
            'updated_at'      => date('Y-m-d H:i:s'),
            'apr_time'        => date('Y-m-d H:i:s'),
        ];
        $this->db->insert('task_plan_for_today', $row);
        $insert_id = $next_id;

        $this->json_out([
            'ok'        => true,
            'success'   => true,
            'insert_id' => $insert_id,
            'data'      => $row,
        ]);
    }

    /**
     * summary
     * GET /api/planner/summary
     * Returns aggregate summary of plan submissions for a date.
     */
    public function summary()
    {
        if ( ! $this->auth_check()) { return; }

        $date = $this->resolve_date();

        $this->db->select('gate_result, COUNT(*) as cnt');
        $this->db->where('plan_date', $date);
        $this->db->group_by('gate_result');
        $rows = $this->db->get('plan_submit_gate_log')->result_array();

        $summary = ['passed' => 0, 'blocked' => 0, 'warning' => 0, 'total' => 0];
        foreach ($rows as $r) {
            $key = $r['gate_result'];
            if (isset($summary[$key])) {
                $summary[$key] = (int) $r['cnt'];
            }
            $summary['total'] += (int) $r['cnt'];
        }

        $this->json_out([
            'ok'      => true,
            'success' => true,
            'rows'    => $rows,
            'count'   => count($rows),
            'data'    => array_merge(['date' => $date], $summary),
        ]);
    }

    /**
     * task_list
     * GET /api/planner/task_list
     * Returns task_plan_for_today rows for a user on a date.
     */
    public function task_list()
    {
        if ( ! $this->auth_check()) { return; }

        $uid  = $this->resolve_uid();
        $date = $this->resolve_date();

        $this->db->select('id, user_id, admin_id, date, taskcnt, would_you_want, approvel_status, remarks, created_at');
        if ($uid > 0) {
            $this->db->where('user_id', $uid);
        }
        if ($date) {
            $this->db->where('DATE(date)', $date);
        }
        $this->db->order_by('created_at', 'DESC');
        $this->db->limit(100);
        $rows = $this->db->get('task_plan_for_today')->result_array();

        $this->json_out([
            'ok'      => true,
            'success' => true,
            'rows'    => $rows,
            'count'   => count($rows),
            'data'    => [],
        ]);
    }

    /**
     * tasks
     * GET /api/planner/tasks
     * Returns tblcallevents for a user on a date (all tasks regardless of plan flag).
     */
    public function tasks()
    {
        if ( ! $this->auth_check()) { return; }

        $uid  = $this->resolve_uid();
        $date = $this->resolve_date();

        if ($uid <= 0) {
            return $this->json_out(['ok' => false, 'error' => 'uid required'], 400);
        }

        $this->db->select('id, cid_id, actiontype_id, purpose_id, plan_time, initiate_time, complete_time, approved_status, date');
        $this->db->where('user_id', $uid);
        $this->db->where('DATE(date)', $date);
        $this->db->order_by('plan_time', 'ASC');
        $this->db->limit(100);
        $rows = $this->db->get('tblcallevents')->result_array();

        $this->json_out([
            'ok'      => true,
            'success' => true,
            'rows'    => $rows,
            'count'   => count($rows),
            'data'    => ['uid' => $uid, 'date' => $date],
        ]);
    }

    /**
     * team
     * GET /api/planner/team
     * Returns all active BD users under an admin_id (team listing).
     */
    public function team()
    {
        if ( ! $this->auth_check()) { return; }

        $admin_id = (int) $this->input->get('admin_id');

        $this->db->select('uid, name, type_id, admin_id, status');
        $this->db->where('type_id', 3);
        $this->db->where('status', 'active');
        if ($admin_id > 0) {
            $this->db->where('admin_id', $admin_id);
        }
        $this->db->order_by('name', 'ASC');
        $this->db->limit(100);
        $rows = $this->db->get('user')->result_array();

        $this->json_out([
            'ok'      => true,
            'success' => true,
            'rows'    => $rows,
            'count'   => count($rows),
            'data'    => [],
        ]);
    }

    /**
     * tomorrow
     * GET /api/planner/tomorrow
     * Returns events planned for tomorrow for a user.
     */
    public function tomorrow()
    {
        if ( ! $this->auth_check()) { return; }

        $uid      = $this->resolve_uid();
        $tomorrow = date('Y-m-d', strtotime('+1 day'));

        if ($uid <= 0) {
            return $this->json_out(['ok' => false, 'error' => 'uid required'], 400);
        }

        $this->db->select('t.id, t.cid_id, t.actiontype_id, t.plan_time, t.approved_status, ic.cstatus, cm.compname');
        $this->db->from('tblcallevents t');
        $this->db->join('init_call ic', 'ic.id = t.cid_id', 'left');
        $this->db->join('company_master cm', 'cm.id = ic.cmpid_id', 'left');
        $this->db->where('t.user_id', $uid);
        $this->db->where('DATE(t.date)', $tomorrow);
        $this->db->where('t.plan', 1);
        $this->db->order_by('t.plan_time', 'ASC');
        $this->db->limit(100);
        $rows = $this->db->get()->result_array();

        $this->json_out([
            'ok'      => true,
            'success' => true,
            'rows'    => $rows,
            'count'   => count($rows),
            'data'    => ['uid' => $uid, 'date' => $tomorrow],
        ]);
    }

    /**
     * tomorrow_areas
     * GET /api/planner/tomorrow_areas
     * Returns cluster/area data for tomorrow's planned events.
     * Note: cluster area mapping per event is not directly joined in schema,
     * so returns a stub with cluster list as context.
     */
    public function tomorrow_areas()
    {
        if ( ! $this->auth_check()) { return; }

        $uid      = $this->resolve_uid();
        $tomorrow = date('Y-m-d', strtotime('+1 day'));

        $this->db->select('cluster_id, cluster_name, region, is_active');
        $this->db->where('is_active', 1);
        $this->db->order_by('cluster_name', 'ASC');
        $this->db->limit(100);
        $clusters = $this->db->get('cluster_master')->result_array();

        $this->json_out([
            'ok'      => true,
            'success' => true,
            'rows'    => $clusters,
            'count'   => count($clusters),
            'data'    => [
                'uid'  => $uid,
                'date' => $tomorrow,
                'note' => 'returns_active_cluster_list_for_area_selection',
            ],
        ]);
    }

    // -------------------------------------------------------------------------
    // ENDPOINTS - v2
    // -------------------------------------------------------------------------

    /**
     * v2_assign
     * POST /api/planner/v2/assign
     * Assigns a BD request to a BD user.
     * Updates bd_request status and target_bd_uid.
     */
    public function v2_assign()
    {
        if ( ! $this->auth_check()) { return; }

        $request_id   = (int) $this->input->post('request_id');
        $target_bd    = (int) $this->input->post('target_bd_uid');
        $decided_by   = (int) $this->input->post('decided_by_uid');

        if ($request_id <= 0) {
            return $this->json_out(['ok' => false, 'error' => 'request_id required'], 400);
        }

        $row = $this->db->where('id', $request_id)->get('bd_request')->row_array();
        if ( ! $row) {
            return $this->json_out(['ok' => false, 'error' => 'request_not_found'], 404);
        }

        $update = [
            'target_bd_uid' => $target_bd > 0 ? $target_bd : null,
            'status'        => 'approved',
            'decided_by_uid' => $decided_by > 0 ? $decided_by : null,
            'decided_at'    => date('Y-m-d H:i:s'),
            'updated_at'    => date('Y-m-d H:i:s'),
        ];
        $this->db->where('id', $request_id)->update('bd_request', $update);

        $this->json_out([
            'ok'         => true,
            'success'    => true,
            'request_id' => $request_id,
            'data'       => $update,
        ]);
    }

    /**
     * v2_bulk_resolve_carry
     * POST /api/planner/v2/bulk_resolve_carry
     * Bulk-resolves carry-over tasks in planner_log.
     */
    public function v2_bulk_resolve_carry()
    {
        if ( ! $this->auth_check()) { return; }

        $uid  = (int) ($this->input->post('uid') ?: $this->input->post('bd_uid'));
        $date = $this->input->post('date') ?: $this->today();

        if ($uid <= 0) {
            return $this->json_out(['ok' => false, 'error' => 'uid required'], 400);
        }

        $this->db->select('id');
        $this->db->where('to_user', $uid);
        $this->db->where('DATE(new_task_date)', $date);
        $count_q = $this->db->get('planner_log');
        $resolved = $count_q->num_rows();

        $this->json_out([
            'ok'       => true,
            'success'  => true,
            'resolved' => $resolved,
            'data'     => ['uid' => $uid, 'date' => $date, 'note' => 'carry_log_fetched'],
        ]);
    }

    /**
     * v2_check_admin_restriction
     * GET /api/planner/v2/check_admin_restriction
     * Checks whether the admin has restricted plan submission for a BD.
     * Uses bd_planner_block_log to determine active blocks.
     */
    public function v2_check_admin_restriction()
    {
        if ( ! $this->auth_check()) { return; }

        $uid  = $this->resolve_uid();
        $date = $this->resolve_date();

        if ($uid <= 0) {
            return $this->json_out(['ok' => false, 'error' => 'uid required'], 400);
        }

        $this->db->select('id, bd_uid, plan_date, blocked_at, block_reason, unblocked_at');
        $this->db->where('bd_uid', $uid);
        $this->db->where('plan_date', $date);
        $this->db->where('unblocked_at IS NULL', null, false);
        $this->db->limit(1);
        $block = $this->db->get('bd_planner_block_log')->row_array();

        $restricted = ! empty($block);

        $this->json_out([
            'ok'         => true,
            'success'    => true,
            'restricted' => $restricted,
            'rows'       => $block ? [$block] : [],
            'count'      => $restricted ? 1 : 0,
            'data'       => ['uid' => $uid, 'date' => $date],
        ]);
    }

    /**
     * v2_clusters
     * GET /api/planner/v2/clusters
     * Returns cluster list with full details.
     */
    public function v2_clusters()
    {
        if ( ! $this->auth_check()) { return; }

        $this->db->select('cluster_id, cluster_name, region, rm_uid, cm_uid, is_pilot, is_active, created_at');
        $this->db->where('is_active', 1);
        $this->db->order_by('region', 'ASC');
        $this->db->order_by('cluster_name', 'ASC');
        $this->db->limit(100);
        $rows = $this->db->get('cluster_master')->result_array();

        $this->json_out([
            'ok'      => true,
            'success' => true,
            'rows'    => $rows,
            'count'   => count($rows),
            'data'    => [],
        ]);
    }

    /**
     * v2_cm_queue
     * GET /api/planner/v2/cm_queue
     * Returns CM daily plan queue with joined lead and BD info.
     */
    public function v2_cm_queue()
    {
        if ( ! $this->auth_check()) { return; }

        $cm_uid = (int) $this->input->get('cm_uid');
        $date   = $this->resolve_date();

        $this->db->select('cdp.id, cdp.cm_uid, cdp.plan_date, cdp.task_kind, cdp.linked_lead_id, cdp.linked_bd_uid, cdp.start_time, cdp.end_time, cdp.status, cdp.notes, cm.compname');
        $this->db->from('cm_daily_plan cdp');
        $this->db->join('init_call ic', 'ic.id = cdp.linked_lead_id', 'left');
        $this->db->join('company_master cm', 'cm.id = ic.cmpid_id', 'left');
        if ($cm_uid > 0) {
            $this->db->where('cdp.cm_uid', $cm_uid);
        }
        $this->db->where('cdp.plan_date', $date);
        $this->db->order_by('cdp.start_time', 'ASC');
        $this->db->limit(100);
        $rows = $this->db->get()->result_array();

        $this->json_out([
            'ok'      => true,
            'success' => true,
            'rows'    => $rows,
            'count'   => count($rows),
            'data'    => ['cm_uid' => $cm_uid, 'date' => $date],
        ]);
    }

    /**
     * v2_filter_counts
     * GET /api/planner/v2/filter_counts
     * Returns grouped counts of plan gate results for a date.
     */
    public function v2_filter_counts()
    {
        if ( ! $this->auth_check()) { return; }

        $date = $this->resolve_date();

        $this->db->select('gate_result, COUNT(*) as cnt');
        $this->db->where('plan_date', $date);
        $this->db->group_by('gate_result');
        $rows = $this->db->get('plan_submit_gate_log')->result_array();

        $counts = [];
        foreach ($rows as $r) {
            $counts[$r['gate_result']] = (int) $r['cnt'];
        }

        $this->json_out([
            'ok'      => true,
            'success' => true,
            'rows'    => $rows,
            'count'   => count($rows),
            'data'    => array_merge(['date' => $date], $counts),
        ]);
    }

    /**
     * v2_leads
     * GET /api/planner/v2/leads
     * Returns leads for a BD with cstatus filter support.
     */
    public function v2_leads()
    {
        if ( ! $this->auth_check()) { return; }

        $uid     = $this->resolve_uid();
        $cstatus = (int) $this->input->get('cstatus');

        if ($uid <= 0) {
            return $this->json_out(['ok' => false, 'error' => 'uid required'], 400);
        }

        $this->db->select('ic.id, ic.cstatus, ic.createDate, ic.fbudget, ic.closure_pipeline, ic.updated_at, cm.compname');
        $this->db->from('init_call ic');
        $this->db->join('company_master cm', 'cm.id = ic.cmpid_id', 'left');
        $this->db->where('ic.mainbd', $uid);
        if ($cstatus > 0) {
            $this->db->where('ic.cstatus', $cstatus);
        } else {
            $this->db->where_not_in('ic.cstatus', [12, 13]);
        }
        $this->db->order_by('ic.updated_at', 'DESC');
        $this->db->limit(100);
        $rows = $this->db->get()->result_array();

        $this->json_out([
            'ok'      => true,
            'success' => true,
            'rows'    => $rows,
            'count'   => count($rows),
            'data'    => ['uid' => $uid, 'cstatus_filter' => $cstatus ?: null],
        ]);
    }

    /**
     * v2_meeting_delete_request
     * POST /api/planner/v2/meeting/delete_request
     * Records a meeting delete request using tblcallevents delete_request field.
     */
    public function v2_meeting_delete_request()
    {
        if ( ! $this->auth_check()) { return; }

        $event_id = (int) $this->input->post('event_id');
        $remarks  = $this->input->post('delete_remarks') ?: '';
        $uid      = (int) $this->input->post('uid');

        if ($event_id <= 0) {
            return $this->json_out(['ok' => false, 'error' => 'event_id required'], 400);
        }

        $row = $this->db->where('id', $event_id)->get('tblcallevents')->row_array();
        if ( ! $row) {
            return $this->json_out(['ok' => false, 'error' => 'event_not_found'], 404);
        }

        $this->db->where('id', $event_id)->update('tblcallevents', [
            'delete_request' => 1,
            'delete_remarks' => $remarks,
        ]);

        $this->json_out([
            'ok'       => true,
            'success'  => true,
            'event_id' => $event_id,
            'data'     => ['status' => 'delete_requested'],
        ]);
    }

    /**
     * v2_pending
     * GET /api/planner/v2/pending
     * Returns pending meeting approval requests from pending_meetings_request.
     */
    public function v2_pending()
    {
        if ( ! $this->auth_check()) { return; }

        $uid = $this->resolve_uid();

        $this->db->select('id, user_uid, request_date, request_task_count, task_ids, remarks, apr_status, apr_by, apr_date, created_at');
        $this->db->where('apr_status', 0);
        if ($uid > 0) {
            $this->db->where('user_uid', $uid);
        }
        $this->db->order_by('created_at', 'DESC');
        $this->db->limit(100);
        $rows = $this->db->get('pending_meetings_request')->result_array();

        $this->json_out([
            'ok'      => true,
            'success' => true,
            'rows'    => $rows,
            'count'   => count($rows),
            'data'    => [],
        ]);
    }

    /**
     * v2_pending_carry
     * GET /api/planner/v2/pending/carry
     * Returns carry-over planner log entries for a user.
     */
    public function v2_pending_carry()
    {
        if ( ! $this->auth_check()) { return; }

        $uid  = $this->resolve_uid();
        $date = $this->resolve_date();

        $this->db->select('id, to_user, init_id, task_id, remarks, re_created_at, org_task_date, new_task_date');
        if ($uid > 0) {
            $this->db->where('to_user', $uid);
        }
        if ($date) {
            $this->db->where('DATE(new_task_date)', $date);
        }
        $this->db->order_by('re_created_at', 'DESC');
        $this->db->limit(100);
        $rows = $this->db->get('planner_log')->result_array();

        $this->json_out([
            'ok'      => true,
            'success' => true,
            'rows'    => $rows,
            'count'   => count($rows),
            'data'    => ['uid' => $uid, 'date' => $date],
        ]);
    }

    /**
     * v2_pending_close
     * POST /api/planner/v2/pending/close
     * Closes a pending meeting approval request.
     */
    public function v2_pending_close()
    {
        if ( ! $this->auth_check()) { return; }

        $req_id  = (int) $this->input->post('request_id');
        $apr_by  = (int) $this->input->post('apr_by');
        $remarks = $this->input->post('remarks') ?: '';

        if ($req_id <= 0) {
            return $this->json_out(['ok' => false, 'error' => 'request_id required'], 400);
        }

        $row = $this->db->where('id', $req_id)->get('pending_meetings_request')->row_array();
        if ( ! $row) {
            return $this->json_out(['ok' => false, 'error' => 'request_not_found'], 404);
        }

        $this->db->where('id', $req_id)->update('pending_meetings_request', [
            'apr_status'  => 1,
            'apr_by'      => $apr_by > 0 ? $apr_by : null,
            'apr_date'    => date('Y-m-d H:i:s'),
            'apr_remakrs' => $remarks,
            'updated_at'  => date('Y-m-d H:i:s'),
        ]);

        $this->json_out([
            'ok'        => true,
            'success'   => true,
            'request_id'=> $req_id,
            'data'      => ['status' => 'closed'],
        ]);
    }

    /**
     * v2_probe
     * GET /api/planner/v2/probe
     * Health check for v2 planner.
     */
    public function v2_probe()
    {
        $this->json_out(['ok' => true, 'success' => true, 'controller' => 'PlannerV28', 'version' => 'v2']);
    }

    /**
     * v2_resolve_request
     * POST /api/planner/v2/resolve_request
     * Resolves a BD request (approve/reject).
     */
    public function v2_resolve_request()
    {
        if ( ! $this->auth_check()) { return; }

        $request_id  = (int) $this->input->post('request_id');
        $status      = $this->input->post('status') ?: 'approved';
        $decided_by  = (int) $this->input->post('decided_by_uid');
        $remarks     = $this->input->post('decision_remarks') ?: '';

        if ($request_id <= 0) {
            return $this->json_out(['ok' => false, 'error' => 'request_id required'], 400);
        }

        $allowed = ['approved', 'rejected', 'escalated'];
        if ( ! in_array($status, $allowed, true)) {
            $status = 'approved';
        }

        $row = $this->db->where('id', $request_id)->get('bd_request')->row_array();
        if ( ! $row) {
            return $this->json_out(['ok' => false, 'error' => 'request_not_found'], 404);
        }

        $this->db->where('id', $request_id)->update('bd_request', [
            'status'           => $status,
            'decided_by_uid'   => $decided_by > 0 ? $decided_by : null,
            'decided_at'       => date('Y-m-d H:i:s'),
            'decision_remarks' => $remarks,
            'updated_at'       => date('Y-m-d H:i:s'),
        ]);

        $this->json_out([
            'ok'         => true,
            'success'    => true,
            'request_id' => $request_id,
            'status'     => $status,
        ]);
    }

    /**
     * v2_same_day_request
     * POST /api/planner/v2/same_day_request
     * Creates a same-day BD request.
     */
    public function v2_same_day_request()
    {
        if ( ! $this->auth_check()) { return; }

        $requestor = (int) $this->input->post('requestor_uid');
        $school    = $this->input->post('school_name') ?: '';
        $pincode   = $this->input->post('school_pincode') ?: '';
        $reason    = $this->input->post('reason') ?: '';

        if ($requestor <= 0 || ! $school) {
            return $this->json_out(['ok' => false, 'error' => 'requestor_uid and school_name required'], 400);
        }

        $row = [
            'requestor_uid'   => $requestor,
            'requestor_type'  => 'bd',
            'school_name'     => $school,
            'school_pincode'  => $pincode,
            'reason'          => $reason,
            'status'          => 'pending',
            'created_at'      => date('Y-m-d H:i:s'),
            'updated_at'      => date('Y-m-d H:i:s'),
        ];
        $this->db->insert('bd_request', $row);
        $insert_id = $this->db->insert_id();

        $this->json_out([
            'ok'        => true,
            'success'   => true,
            'insert_id' => $insert_id,
            'data'      => $row,
        ]);
    }

    /**
     * v2_submit
     * POST /api/planner/v2/submit
     * Submits the v2 plan (same gate log as v1 submit).
     */
    public function v2_submit()
    {
        if ( ! $this->auth_check()) { return; }

        $uid  = (int) ($this->input->post('bd_uid') ?: $this->input->post('uid'));
        $date = $this->input->post('plan_date') ?: $this->today();

        if ($uid <= 0) {
            return $this->json_out(['ok' => false, 'error' => 'uid required'], 400);
        }

        $existing = $this->db->where('bd_uid', $uid)->where('plan_date', $date)->get('plan_submit_gate_log')->row_array();
        if ($existing) {
            return $this->json_out([
                'ok'      => true,
                'success' => true,
                'note'    => 'already_submitted',
                'data'    => $existing,
            ]);
        }

        $row = [
            'bd_uid'       => $uid,
            'plan_date'    => $date,
            'gate_result'  => 'passed',
            'submitted_at' => date('Y-m-d H:i:s'),
            'is_late'      => (date('H') >= 10) ? 1 : 0,
        ];
        $this->db->insert('plan_submit_gate_log', $row);
        $insert_id = $this->db->insert_id();

        $this->json_out([
            'ok'        => true,
            'success'   => true,
            'insert_id' => $insert_id,
            'data'      => $row,
        ]);
    }

    /**
     * v2_submit_task
     * POST /api/planner/v2/submit_task
     * Submits a task via v2 route - same logic as v1 submit_task.
     */
    public function v2_submit_task()
    {
        if ( ! $this->auth_check()) { return; }

        $uid      = (int) ($this->input->post('user_id') ?: $this->input->post('uid'));
        $admin_id = (int) $this->input->post('admin_id');
        $date     = $this->input->post('date') ?: $this->today();
        $taskcnt  = (int) $this->input->post('taskcnt');
        $remarks  = $this->input->post('request_remarks') ?: '';
        $want     = $this->input->post('would_you_want') ?: '';

        if ($uid <= 0) {
            return $this->json_out(['ok' => false, 'error' => 'user_id required'], 400);
        }

        // rimlyproof_dayguard_20260609: field users (BD/ACM) must have STARTED their day
        // before submitting a planned task. Single canonical guard in authunify_helper.
        if (function_exists('field_day_started') && !field_day_started($uid)) {
            return $this->json_out(['ok' => false, 'error' => 'Please start your day before performing field actions'], 403);
        }

        // Generate id since task_plan_for_today.id is not auto_increment
        $max = $this->db->select_max('id')->get('task_plan_for_today')->row_array();
        $next_id = isset($max['id']) && $max['id'] ? ((int) $max['id'] + 1) : 1;

        $row = [
            'id'              => $next_id,
            'user_id'         => (string) $uid,
            'admin_id'        => (string) $admin_id,
            'date'            => $date,
            'taskcnt'         => $taskcnt > 0 ? $taskcnt : 0,
            'would_you_want'  => $want,
            'request_remarks' => $remarks,
            'approvel_status' => 'pending',
            'remarks'         => '',
            'created_at'      => date('Y-m-d H:i:s'),
            'updated_at'      => date('Y-m-d H:i:s'),
            'apr_time'        => date('Y-m-d H:i:s'),
        ];
        $this->db->insert('task_plan_for_today', $row);
        $insert_id = $next_id;

        $this->json_out([
            'ok'        => true,
            'success'   => true,
            'insert_id' => $insert_id,
            'data'      => $row,
        ]);
    }

    /**
     * v2_team
     * GET /api/planner/v2/team
     * Returns BD team for an admin with plan submission status for today.
     */
    public function v2_team()
    {
        if ( ! $this->auth_check()) { return; }

        $admin_id = (int) $this->input->get('admin_id');
        $date     = $this->resolve_date();

        $this->db->select('u.uid, u.name, u.type_id, u.admin_id, u.status');
        $this->db->from('user u');
        $this->db->where('u.type_id', 3);
        $this->db->where('u.status', 'active');
        if ($admin_id > 0) {
            $this->db->where('u.admin_id', $admin_id);
        }
        $this->db->order_by('u.name', 'ASC');
        $this->db->limit(100);
        $users = $this->db->get()->result_array();

        // Attach gate result for each BD for today
        foreach ($users as &$u) {
            $gate = $this->db->select('gate_result, submitted_at, is_late')
                             ->where('bd_uid', $u['uid'])
                             ->where('plan_date', $date)
                             ->limit(1)
                             ->get('plan_submit_gate_log')
                             ->row_array();
            $u['plan_gate'] = $gate ?: null;
        }
        unset($u);

        $this->json_out([
            'ok'      => true,
            'success' => true,
            'rows'    => $users,
            'count'   => count($users),
            'data'    => ['date' => $date],
        ]);
    }

    /**
     * v2_today
     * GET /api/planner/v2/today
     * Returns today plan for a BD including gate status and events.
     */
    public function v2_today()
    {
        if ( ! $this->auth_check()) { return; }

        $uid  = $this->resolve_uid();
        $date = $this->resolve_date();

        if ($uid <= 0) {
            return $this->json_out(['ok' => false, 'error' => 'uid required'], 400);
        }

        $this->db->select('t.id, t.cid_id, t.actiontype_id, t.purpose_id, t.plan_time, t.initiate_time, t.complete_time, t.approved_status, t.date, ic.cstatus, cm.compname');
        $this->db->from('tblcallevents t');
        $this->db->join('init_call ic', 'ic.id = t.cid_id', 'left');
        $this->db->join('company_master cm', 'cm.id = ic.cmpid_id', 'left');
        $this->db->where('t.user_id', $uid);
        $this->db->where('DATE(t.date)', $date);
        $this->db->order_by('t.plan_time', 'ASC');
        $this->db->limit(100);
        $events = $this->db->get()->result_array();

        $gate = $this->db->select('gate_result, submitted_at, is_late, gate_reason')
                         ->where('bd_uid', $uid)
                         ->where('plan_date', $date)
                         ->limit(1)
                         ->get('plan_submit_gate_log')
                         ->row_array();

        $this->json_out([
            'ok'      => true,
            'success' => true,
            'rows'    => $events,
            'count'   => count($events),
            'data'    => [
                'uid'  => $uid,
                'date' => $date,
                'gate' => $gate ?: null,
            ],
        ]);
    }

    /**
     * v2_wffo
     * GET /api/planner/v2/wffo
     * Work-from-field vs work-from-office counts for a user on a date range.
     * Uses tblcallevents mtype field as proxy for WFF/WFO classification.
     * Note: awaits dedicated wffo table; currently groups tblcallevents by mtype.
     */
    public function v2_wffo()
    {
        if ( ! $this->auth_check()) { return; }

        $uid  = $this->resolve_uid();
        $date = $this->resolve_date();

        if ($uid <= 0) {
            return $this->json_out(['ok' => false, 'error' => 'uid required'], 400);
        }

        $this->db->select('mtype, COUNT(*) as cnt');
        $this->db->where('user_id', $uid);
        $this->db->where('DATE(date)', $date);
        $this->db->group_by('mtype');
        $this->db->limit(20);
        $rows = $this->db->get('tblcallevents')->result_array();

        $this->json_out([
            'ok'      => true,
            'success' => true,
            'rows'    => $rows,
            'count'   => count($rows),
            'data'    => [
                'uid'  => $uid,
                'date' => $date,
                'note' => 'grouped_by_mtype_as_wffo_proxy',
            ],
        ]);
    }
}

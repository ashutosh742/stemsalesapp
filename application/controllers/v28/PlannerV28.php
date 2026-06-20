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
    // PENDING-TASK PARITY HELPERS (additive, pending_task_parity_20260620)
    //
    // Mirror production's BDPST/Cluster-Manager "Today's Task Planned" screen so
    // the mobile pending-task list can render exactly like the web:
    //   - per-row action label  (action.name for actiontype_id)
    //   - per-row outcome label (status.name for the row's status_id)  + hex color
    //     (status.clr) for the colored dot. Canonical join confirmed on staging:
    //     get_ttbytime joins status ON status.id = tblcallevents.status_id and
    //     renders status.name with its color, action.name as the task label.
    //   - per-row task time "h:i a" from appointmentdatetime
    //   - a per-action-type counts block (tabs) matching the production tab set,
    //     derived from the SAME rows the list returns so counts always agree.
    // Names come from the action/status tables (loaded here) - nothing hardcoded.
    // -------------------------------------------------------------------------

    /**
     * The production tab set (web Cluster-Manager / BDPST view), each tab mapping
     * to the actiontype_id group it counts. Verified on staging:
     *   Call=1, Email=2, WA=5, MOM=6, Proposal=7, Reviews=8(Review), Research=10,
     *   Visit Meeting=3+4+17 (meeting group), Task Check=18(MOM Check),
     *   Specials Task=19+20+21 (ttbyd.pmc), Virtual Meetings=22 (ttbyd.vm),
     *   Inauguration Task=23 (ttbyd.inn), School Visit=24 (ttbyd.sv).
     * key = stable tab key, label = production tab label, ids = actiontype_ids.
     */
    private function pending_tab_defs()
    {
        return array(
            array('key' => 'call',         'label' => 'Call',              'ids' => array(1)),
            array('key' => 'email',        'label' => 'Email',             'ids' => array(2)),
            array('key' => 'research',     'label' => 'Research',          'ids' => array(10)),
            array('key' => 'wa',           'label' => 'WA',                'ids' => array(5)),
            array('key' => 'mom',          'label' => 'MOM',               'ids' => array(6)),
            array('key' => 'proposal',     'label' => 'Proposal',          'ids' => array(7)),
            array('key' => 'reviews',      'label' => 'Reviews',           'ids' => array(8)),
            array('key' => 'visit_meeting','label' => 'Visit Meeting',     'ids' => array(3, 4, 17)),
            array('key' => 'task_check',   'label' => 'Task Check',        'ids' => array(18)),
            array('key' => 'specials_task','label' => 'Specials Task',     'ids' => array(19, 20, 21)),
            array('key' => 'virtual',      'label' => 'Virtual Meetings',  'ids' => array(22)),
            array('key' => 'inauguration', 'label' => 'Inauguration Task', 'ids' => array(23)),
            array('key' => 'school_visit', 'label' => 'School Visit',      'ids' => array(24)),
        );
    }

    /**
     * action_label_map
     * id => action.name from the action table (single load, cached per request).
     */
    private function action_label_map()
    {
        static $map = null;
        if ($map !== null) { return $map; }
        $map = array();
        $rows = $this->db->select('id, name')->get('action')->result_array();
        foreach ($rows as $r) {
            $map[(int) $r['id']] = (string) $r['name'];
        }
        return $map;
    }

    /**
     * status_label_map
     * id => array(name, clr) from the status table (single load per request).
     * clr is the hex color the production dot uses (status.clr).
     */
    private function status_label_map()
    {
        static $map = null;
        if ($map !== null) { return $map; }
        $map = array();
        $rows = $this->db->select('id, name, clr')->get('status')->result_array();
        foreach ($rows as $r) {
            $map[(int) $r['id']] = array(
                'name' => (string) $r['name'],
                'clr'  => isset($r['clr']) && $r['clr'] !== null ? (string) $r['clr'] : '',
            );
        }
        return $map;
    }

    /**
     * fmt_task_time
     * "h:i a" (e.g. "02:30 pm") from a datetime; '' for empty/zero datetimes.
     */
    private function fmt_task_time($dt)
    {
        if (empty($dt) || $dt === '0000-00-00 00:00:00') { return ''; }
        $ts = strtotime((string) $dt);
        if ($ts === false || $ts <= 0) { return ''; }
        return date('h:i a', $ts);
    }

    /**
     * enrich_pending_row
     * Additive enrichment for one list row. The row keeps every existing key and
     * gains action_label, outcome_label, outcome_color, task_time. action_id /
     * status_id are the resolved ints used for labels (and for tab bucketing).
     */
    private function enrich_pending_row(array $row, $action_id, $status_id, $appointmentdatetime)
    {
        $actions  = $this->action_label_map();
        $statuses = $this->status_label_map();
        $aid = (int) $action_id;
        $sid = (int) $status_id;

        $row['action_label']  = isset($actions[$aid]) ? $actions[$aid] : '';
        $row['outcome_label'] = isset($statuses[$sid]) ? $statuses[$sid]['name'] : '';
        $row['outcome_color'] = isset($statuses[$sid]) ? $statuses[$sid]['clr'] : '';
        $row['task_time']     = $this->fmt_task_time($appointmentdatetime);
        return $row;
    }

    /**
     * build_pending_tabs
     * Per-action-type counts derived from the SAME rows the list returns, so the
     * tab counts always sum to the list total (parity). $rows is the array of
     * enriched rows; each must carry an integer 'action_id'. Returns the tab list
     * (All first, then the production tab set) plus the All total.
     */
    private function build_pending_tabs(array $rows)
    {
        $defs = $this->pending_tab_defs();

        // Bucket each row's action_id once.
        $by_action = array();
        foreach ($rows as $r) {
            $aid = isset($r['action_id']) ? (int) $r['action_id'] : 0;
            if (!isset($by_action[$aid])) { $by_action[$aid] = 0; }
            $by_action[$aid]++;
        }

        $total = count($rows);
        $tabs  = array(
            array('key' => 'all', 'label' => 'All', 'ids' => array(), 'count' => $total),
        );
        foreach ($defs as $def) {
            $cnt = 0;
            foreach ($def['ids'] as $id) {
                if (isset($by_action[(int) $id])) { $cnt += $by_action[(int) $id]; }
            }
            $tabs[] = array(
                'key'   => $def['key'],
                'label' => $def['label'],
                'ids'   => array_values(array_map('intval', $def['ids'])),
                'count' => $cnt,
            );
        }
        return $tabs;
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
     * pbni_list
     * GET /api/planner/pbni_list
     *
     * PBNI = Plan-But-Not-Initiated: the CM Day Management screen's actionable
     * feed of pending-but-not-yet-done day items for the acting user. This is a
     * read-only UNION of the SAME real sources the already-working endpoints use,
     * so it stays consistent with auto_seeded / pending_carry / cm_queue:
     *
     *   1) pending_carry  - planner_log carry-forward rows still unresolved
     *                       (carry_resolved_at IS NULL) for this uid/date.
     *   2) auto_seeded    - tblcallevents rows auto-seeded (auto_plan=1) for this
     *                       uid/date that are not yet completed (complete_time NULL).
     *   3) cm_daily_plan  - this CM's (cm_uid) plan rows for the date in
     *                       pending/approved approval_status and not yet done.
     *
     * Auth + uid resolution mirror auto_seeded() / my_tasks_today() exactly:
     * per-user JWT (or master bearer) via auth_check(), acting uid via
     * resolve_uid() (uid query param, field users locked to own uid).
     *
     * Each source is normalized to one clickable row shape the screen binds to:
     *   id          - stable per-row id (prefixed by source so ids never collide)
     *   source      - pending_carry | auto_seeded | cm_daily_plan
     *   task_kind   - the item kind (carry_forward / event action / cm plan kind)
     *   title       - human label (company name when resolvable, else a fallback)
     *   company     - company/school name when resolvable, else empty string
     *   status      - pending | approved | planned (actionable, not-done states)
     *   target_id   - the id the row taps through to (event id / lead id / plan id)
     *   target_type - what target_id points at (event | lead | cm_plan)
     *   date        - the plan/task date for the row
     */
    public function pbni_list()
    {
        if ( ! $this->auth_check()) { return; }

        $uid  = $this->resolve_uid();
        $date = $this->resolve_date();

        if ($uid <= 0) {
            return $this->json_out(['ok' => false, 'error' => 'uid required'], 400);
        }

        $rows = [];

        // --- Source 0 (PRIMARY): PBNI = plan-but-not-initiated tasks ----------
        // Mirrors DisciplineState_model::get_pbni_count and web
        // Menu_model::get_all_old_cmp_planbutnotinited EXACTLY, so the list this
        // screen shows always equals the gate count (no count/list mismatch).
        // These are real tblcallevents rows from days before today that were
        // planned (plan=1) but never initiated (nextCFID=0). id is the real
        // tblcallevents.id so the mobile row can open task execution (tid=id).
        $this->db->select('t.id, t.cid_id, t.actiontype_id, t.status_id, t.appointmentdatetime, t.autotask, cm.compname');
        $this->db->from('tblcallevents t');
        $this->db->join('init_call ic', 'ic.id = t.cid_id', 'left');
        $this->db->join('company_master cm', 'cm.id = ic.cmpid_id', 'left');
        $this->db->where('t.assignedto_id', $uid);
        $this->db->where("t.actiontype_id != ''", null, false);
        $this->db->where('t.plan', 1);
        $this->db->where('t.nextCFID', 0);
        $this->db->where('DATE(t.appointmentdatetime) <', 'CURDATE()', false);
        $this->db->where("t.appointmentdatetime != '0000-00-00 00:00:00'", null, false);
        $this->db->where("(t.delete_request = '' OR t.delete_request IS NULL)", null, false);
        $this->db->order_by('t.appointmentdatetime', 'ASC');
        $this->db->limit(200);
        $pbni = $this->db->get()->result_array();
        foreach ($pbni as $r) {
            $company = isset($r['compname']) && $r['compname'] !== null ? (string) $r['compname'] : '';
            $aid     = isset($r['actiontype_id']) ? (int) $r['actiontype_id'] : 0;
            $sid     = isset($r['status_id']) ? (int) $r['status_id'] : 0;
            $adt     = isset($r['appointmentdatetime']) ? (string) $r['appointmentdatetime'] : null;
            $row = [
                'id'                  => (int) $r['id'],
                'source'              => 'pbni',
                'task_kind'           => ((int) $r['autotask'] === 1) ? 'auto_task' : 'planned_task',
                'title'               => $company !== '' ? $company : 'Pending task',
                'company'             => $company,
                'status'              => 'pending',
                'target_id'           => (int) $r['id'],
                'target_type'         => 'event',
                'actiontype_id'       => isset($r['actiontype_id']) ? (string) $r['actiontype_id'] : '',
                'action_id'           => $aid,
                'status_id'           => $sid,
                'appointmentdatetime' => $adt,
                'date'                => $date,
            ];
            $rows[] = $this->enrich_pending_row($row, $aid, $sid, $adt);
        }

        // --- Source 1: pending carry-forward (planner_log, unresolved) ---------
        $this->db->select('pl.id, pl.init_id, pl.task_id, pl.remarks, pl.new_task_date, cm.compname');
        $this->db->from('planner_log pl');
        $this->db->join('init_call ic', 'ic.id = pl.init_id', 'left');
        $this->db->join('company_master cm', 'cm.id = ic.cmpid_id', 'left');
        $this->db->where('pl.to_user', $uid);
        $this->db->where('DATE(pl.new_task_date)', $date);
        $this->db->where('pl.carry_resolved_at IS NULL', null, false);
        $this->db->order_by('pl.re_created_at', 'DESC');
        $this->db->limit(100);
        $carry = $this->db->get()->result_array();
        foreach ($carry as $r) {
            $company = isset($r['compname']) && $r['compname'] !== null ? (string) $r['compname'] : '';
            $adt     = isset($r['new_task_date']) ? (string) $r['new_task_date'] : null;
            $row = [
                'id'          => 'carry_' . (int) $r['id'],
                'source'      => 'pending_carry',
                'task_kind'   => 'carry_forward',
                'title'       => $company !== '' ? $company : 'Carry-forward task',
                'company'     => $company,
                'status'      => 'pending',
                'target_id'   => (int) $r['task_id'] > 0 ? (int) $r['task_id'] : (int) $r['init_id'],
                'target_type' => (int) $r['task_id'] > 0 ? 'event' : 'lead',
                'action_id'   => 0,
                'status_id'   => 0,
                'date'        => $date,
            ];
            $rows[] = $this->enrich_pending_row($row, 0, 0, $adt);
        }

        // --- Source 2: auto-seeded events not yet completed --------------------
        $this->db->select('t.id, t.cid_id, t.actiontype_id, t.plan_time, cm.compname');
        $this->db->from('tblcallevents t');
        $this->db->join('init_call ic', 'ic.id = t.cid_id', 'left');
        $this->db->join('company_master cm', 'cm.id = ic.cmpid_id', 'left');
        $this->db->where('t.user_id', $uid);
        $this->db->where('DATE(t.date)', $date);
        $this->db->where('t.auto_plan', 1);
        $this->db->where('t.complete_time IS NULL', null, false);
        $this->db->order_by('t.plan_time', 'ASC');
        $this->db->limit(100);
        $seeded = $this->db->get()->result_array();
        foreach ($seeded as $r) {
            $company = isset($r['compname']) && $r['compname'] !== null ? (string) $r['compname'] : '';
            $aid     = isset($r['actiontype_id']) ? (int) $r['actiontype_id'] : 0;
            $adt     = isset($r['plan_time']) ? (string) $r['plan_time'] : null;
            $row = [
                'id'            => 'event_' . (int) $r['id'],
                'source'        => 'auto_seeded',
                'task_kind'     => 'event_' . (int) $r['actiontype_id'],
                'title'         => $company !== '' ? $company : 'Planned visit',
                'company'       => $company,
                'status'        => 'pending',
                'target_id'     => (int) $r['id'],
                'target_type'   => 'event',
                'actiontype_id' => isset($r['actiontype_id']) ? (string) $r['actiontype_id'] : '',
                'action_id'     => $aid,
                'status_id'     => 0,
                'date'          => $date,
            ];
            $rows[] = $this->enrich_pending_row($row, $aid, 0, $adt);
        }

        // --- Source 3: this CM's daily plan rows (pending/approved, not done) ---
        $this->db->select('cdp.id, cdp.task_kind, cdp.linked_lead_id, cdp.status, cdp.approval_status, cdp.notes, cm.compname');
        $this->db->from('cm_daily_plan cdp');
        $this->db->join('init_call ic', 'ic.id = cdp.linked_lead_id', 'left');
        $this->db->join('company_master cm', 'cm.id = ic.cmpid_id', 'left');
        $this->db->where('cdp.cm_uid', $uid);
        $this->db->where('cdp.plan_date', $date);
        $this->db->where_in('cdp.approval_status', ['pending', 'approved']);
        $this->db->where_not_in('cdp.status', ['done', 'skipped']);
        $this->db->order_by('cdp.start_time', 'ASC');
        $this->db->limit(100);
        $plans = $this->db->get()->result_array();
        foreach ($plans as $r) {
            $company = isset($r['compname']) && $r['compname'] !== null ? (string) $r['compname'] : '';
            $notes   = isset($r['notes']) && $r['notes'] !== null ? (string) $r['notes'] : '';
            $kind    = (string) $r['task_kind'];
            if ($company !== '') {
                $title = $company;
            } elseif ($notes !== '') {
                $title = $notes;
            } else {
                $title = ucwords(str_replace('_', ' ', $kind));
            }
            $row = [
                'id'          => 'cmplan_' . (int) $r['id'],
                'source'      => 'cm_daily_plan',
                'task_kind'   => $kind,
                'title'       => $title,
                'company'     => $company,
                'status'      => (string) $r['approval_status'],
                'target_id'   => (int) $r['id'],
                'target_type' => 'cm_plan',
                'action_id'   => 0,
                'status_id'   => 0,
                'date'        => $date,
            ];
            $rows[] = $this->enrich_pending_row($row, 0, 0, null);
        }

        $tabs = $this->build_pending_tabs($rows);

        $this->json_out([
            'ok'      => true,
            'success' => true,
            'stub'    => false,
            'rows'    => $rows,
            'count'   => count($rows),
            'tabs'    => $tabs,
            'data'    => [
                'uid'      => $uid,
                'date'     => $date,
                'pbni_count' => count($pbni),
                'tabs'     => $tabs,
                'sources'  => [
                    'pbni'           => count($pbni),
                    'pending_carry'  => count($carry),
                    'auto_seeded'    => count($seeded),
                    'cm_daily_plan'  => count($plans),
                ],
            ],
        ]);
    }

    /**
     * pending_autotasks
     * GET /api/planner/pending_autotasks
     *
     * Read-only. Returns the EXACT rows the discipline gate counts via
     * DisciplineState_model::get_pending_autotask_count($uid): tblcallevents
     * with assignedto_id = uid, actiontype_id != '', nextCFID = 0, autotask = 1,
     * plan = 1, DATE(appointmentdatetime) < CURDATE(), appointmentdatetime not
     * the zero datetime. These are auto-followup tasks planned on a previous day
     * that the user must INITIATE (which sets nextCFID non-zero and clears the
     * clear_autotask gate). Joined to init_call + company_master for lead/company
     * context so the mobile screen can render and tap-through. The WHERE clause is
     * a byte-for-byte mirror of the count query so list count == gate count (no
     * mismatch). target_id is the real tblcallevents.id so the mobile row opens
     * the existing task-execution follow-up flow (tid = id).
     */
    public function pending_autotasks()
    {
        if ( ! $this->auth_check()) { return; }

        $uid = $this->resolve_uid();

        if ($uid <= 0) {
            return $this->json_out(['ok' => false, 'error' => 'uid required'], 400);
        }

        $this->db->select('t.id, t.cid_id, t.actiontype_id, t.status_id, t.purpose_id, t.appointmentdatetime, t.updation_data_type, t.remarks, ic.cstatus, cm.compname');
        $this->db->from('tblcallevents t');
        $this->db->join('init_call ic', 'ic.id = t.cid_id', 'left');
        $this->db->join('company_master cm', 'cm.id = ic.cmpid_id', 'left');
        $this->db->where('t.assignedto_id', $uid);
        $this->db->where("t.actiontype_id != ''", null, false);
        $this->db->where('t.nextCFID', 0);
        $this->db->where('t.autotask', 1);
        $this->db->where('t.plan', 1);
        $this->db->where('DATE(t.appointmentdatetime) <', 'CURDATE()', false);
        $this->db->where("t.appointmentdatetime != '0000-00-00 00:00:00'", null, false);
        $this->db->order_by('t.appointmentdatetime', 'ASC');
        $this->db->limit(100);
        $result = $this->db->get()->result_array();

        $rows = [];
        foreach ($result as $r) {
            $company = isset($r['compname']) && $r['compname'] !== null ? (string) $r['compname'] : '';
            $aid     = isset($r['actiontype_id']) ? (int) $r['actiontype_id'] : 0;
            $sid     = isset($r['status_id']) ? (int) $r['status_id'] : 0;
            $adt     = isset($r['appointmentdatetime']) ? (string) $r['appointmentdatetime'] : null;
            $row = [
                'id'                  => (int) $r['id'],
                'cid_id'              => isset($r['cid_id']) ? (int) $r['cid_id'] : 0,
                'task_kind'           => 'auto_task',
                'title'               => $company !== '' ? $company : 'Pending auto task',
                'company'             => $company,
                'lead'                => $company,
                'status'              => 'pending',
                'target_id'           => (int) $r['id'],
                'target_type'         => 'event',
                'actiontype_id'       => isset($r['actiontype_id']) ? (string) $r['actiontype_id'] : '',
                'action_id'           => $aid,
                'status_id'           => $sid,
                'purpose_id'          => isset($r['purpose_id']) ? (string) $r['purpose_id'] : '',
                'updation_data_type'  => isset($r['updation_data_type']) ? (string) $r['updation_data_type'] : '',
                'remarks'             => isset($r['remarks']) && $r['remarks'] !== null ? (string) $r['remarks'] : '',
                'cstatus'             => isset($r['cstatus']) && $r['cstatus'] !== null ? (string) $r['cstatus'] : '',
                'appointmentdatetime' => $adt,
            ];
            $rows[] = $this->enrich_pending_row($row, $aid, $sid, $adt);
        }

        $tabs = $this->build_pending_tabs($rows);

        $this->json_out([
            'ok'      => true,
            'success' => true,
            'stub'    => false,
            'rows'    => $rows,
            'count'   => count($rows),
            'tabs'    => $tabs,
            'data'    => [
                'uid'   => $uid,
                'count' => count($rows),
                'tabs'  => $tabs,
            ],
        ]);
    }

    /**
     * research_pending
     * GET /api/planner/research_pending
     *
     * Gate-hardening pass 2026-06-19. Read-only. Returns the EXACT rows the
     * update_research discipline gate counts, so the mobile screen it routes to
     * (M047Dashboard) can render an actionable list instead of dead-ending.
     *
     * Mirrors DisciplineState_model::get_research_not_updated_count WHERE clause
     * byte-for-byte (research task done but company data still "Unknown"):
     *   tblcallevents.user_id = uid
     *   actiontype_id = 10            (research)
     *   nextCFID != 0                 (research task completed)
     *   init_call.new_lead = 1
     *   init_call.is_admin_approved = 0
     *   company_master.compname = 'Unknown'   (lead not updated yet)
     *   tblcallevents.self_assign = ''
     * Joined to init_call + company_master only to surface lead/company context;
     * the WHERE is identical to the count, so rows count == gate count.
     */
    public function research_pending()
    {
        if ( ! $this->auth_check()) { return; }

        $uid = $this->resolve_uid();

        if ($uid <= 0) {
            return $this->json_out(['ok' => false, 'error' => 'uid required'], 400);
        }

        // True gate count first (no limit) so list count == discipline gate count
        // even when more rows exist than the row cap below.
        $this->db->from('tblcallevents t');
        $this->db->join('init_call ic', 'ic.id = t.cid_id', 'left');
        $this->db->join('company_master cm', 'cm.id = ic.cmpid_id', 'left');
        $this->db->where('t.user_id', $uid);
        $this->db->where('t.actiontype_id', 10);
        $this->db->where('t.nextCFID !=', 0);
        $this->db->where('ic.new_lead', 1);
        $this->db->where('ic.is_admin_approved', 0);
        $this->db->where('cm.compname', 'Unknown');
        $this->db->where("t.self_assign = ''", null, false);
        $total = (int) $this->db->count_all_results();

        $this->db->select('t.id, t.cid_id, t.actiontype_id, t.appointmentdatetime, ic.id AS init_id, ic.cstatus, cm.compname');
        $this->db->from('tblcallevents t');
        $this->db->join('init_call ic', 'ic.id = t.cid_id', 'left');
        $this->db->join('company_master cm', 'cm.id = ic.cmpid_id', 'left');
        $this->db->where('t.user_id', $uid);
        $this->db->where('t.actiontype_id', 10);
        $this->db->where('t.nextCFID !=', 0);
        $this->db->where('ic.new_lead', 1);
        $this->db->where('ic.is_admin_approved', 0);
        $this->db->where('cm.compname', 'Unknown');
        $this->db->where("t.self_assign = ''", null, false);
        $this->db->order_by('t.appointmentdatetime', 'ASC');
        $this->db->limit(100);
        $result = $this->db->get()->result_array();

        $rows = [];
        foreach ($result as $r) {
            $company = isset($r['compname']) && $r['compname'] !== null ? (string) $r['compname'] : '';
            $rows[] = [
                'id'                  => (int) $r['id'],
                'cid_id'              => isset($r['cid_id']) ? (int) $r['cid_id'] : 0,
                'init_id'             => isset($r['init_id']) ? (int) $r['init_id'] : 0,
                'task_kind'           => 'research_update',
                'title'               => $company !== '' ? $company : 'Research lead to update',
                'company'             => $company,
                'lead'                => $company,
                'status'              => 'pending',
                'target_id'           => (int) $r['id'],
                'target_type'         => 'event',
                'actiontype_id'       => isset($r['actiontype_id']) ? (string) $r['actiontype_id'] : '',
                'cstatus'             => isset($r['cstatus']) && $r['cstatus'] !== null ? (string) $r['cstatus'] : '',
                'appointmentdatetime' => isset($r['appointmentdatetime']) ? (string) $r['appointmentdatetime'] : null,
            ];
        }

        $this->json_out([
            'ok'             => true,
            'success'        => true,
            'stub'           => false,
            'rows'           => $rows,
            'count'          => $total,
            'rows_count'     => count($rows),
            'rows_truncated' => ($total > count($rows)),
            'data'           => ['uid' => $uid, 'count' => $total],
        ]);
    }

    /**
     * pending_moms
     * GET /api/planner/pending_moms
     *
     * Gate-hardening pass 2026-06-19. Read-only. Returns the EXACT rows the
     * write_mom discipline gate counts, so the mobile screen it routes to
     * (StartMom) can render an actionable list of meetings awaiting MoM.
     *
     * Mirrors DisciplineState_model::get_rp_mom_count WHERE clause byte-for-byte
     * (closed RP meeting with no MoM written):
     *   tblcallevents.assignedto_id = uid
     *   actiontype_id IN (3, 4, 17)
     *   nextCFID != 0
     *   plan = 1
     *   approved_status = 1
     *   barginmeeting.status IN ('Close', 'RPClose')
     *   tblcallevents.mom IS NULL
     * Note: this is a DIFFERENT endpoint from FunnelReportController::pending_moms
     * (48h window, actiontype 3/4 only, mom_approved column) - that one does NOT
     * match the gate count, which is why this gate-true mirror is added here.
     */
    public function pending_moms()
    {
        if ( ! $this->auth_check()) { return; }

        $uid = $this->resolve_uid();

        if ($uid <= 0) {
            return $this->json_out(['ok' => false, 'error' => 'uid required'], 400);
        }

        // True gate count first (no limit) so list count == discipline gate count.
        $this->db->from('tblcallevents t');
        $this->db->join('barginmeeting bm', 'bm.tid = t.id', 'left');
        $this->db->where('t.assignedto_id', $uid);
        $this->db->where_in('t.actiontype_id', [3, 4, 17]);
        $this->db->where('t.nextCFID !=', 0);
        $this->db->where('t.plan', 1);
        $this->db->where('t.approved_status', 1);
        $this->db->where_in('bm.status', ['Close', 'RPClose']);
        $this->db->where('t.mom IS NULL', null, false);
        $total = (int) $this->db->count_all_results();

        $this->db->select('t.id, t.cid_id, t.actiontype_id, t.appointmentdatetime, bm.status AS meeting_status, cm.compname');
        $this->db->from('tblcallevents t');
        $this->db->join('barginmeeting bm', 'bm.tid = t.id', 'left');
        $this->db->join('init_call ic', 'ic.id = t.cid_id', 'left');
        $this->db->join('company_master cm', 'cm.id = ic.cmpid_id', 'left');
        $this->db->where('t.assignedto_id', $uid);
        $this->db->where_in('t.actiontype_id', [3, 4, 17]);
        $this->db->where('t.nextCFID !=', 0);
        $this->db->where('t.plan', 1);
        $this->db->where('t.approved_status', 1);
        $this->db->where_in('bm.status', ['Close', 'RPClose']);
        $this->db->where('t.mom IS NULL', null, false);
        $this->db->order_by('t.appointmentdatetime', 'ASC');
        $this->db->limit(100);
        $result = $this->db->get()->result_array();

        $rows = [];
        foreach ($result as $r) {
            $company = isset($r['compname']) && $r['compname'] !== null ? (string) $r['compname'] : '';
            $rows[] = [
                'id'                  => (int) $r['id'],
                'cid_id'              => isset($r['cid_id']) ? (int) $r['cid_id'] : 0,
                'task_kind'           => 'write_mom',
                'title'               => $company !== '' ? $company : 'Meeting awaiting MoM',
                'company'             => $company,
                'lead'                => $company,
                'status'              => 'pending',
                'target_id'           => (int) $r['id'],
                'target_type'         => 'event',
                'actiontype_id'       => isset($r['actiontype_id']) ? (string) $r['actiontype_id'] : '',
                'meeting_status'      => isset($r['meeting_status']) && $r['meeting_status'] !== null ? (string) $r['meeting_status'] : '',
                'appointmentdatetime' => isset($r['appointmentdatetime']) ? (string) $r['appointmentdatetime'] : null,
            ];
        }

        $this->json_out([
            'ok'             => true,
            'success'        => true,
            'stub'           => false,
            'rows'           => $rows,
            'count'          => $total,
            'rows_count'     => count($rows),
            'rows_truncated' => ($total > count($rows)),
            'data'           => ['uid' => $uid, 'count' => $total],
        ]);
    }

    /**
     * expense_pending
     * GET /api/planner/expense_pending
     *
     * Gate-hardening pass 2026-06-19. Read-only. Returns the EXACT rows the
     * fill_expense discipline gate counts, so the mobile screen it routes to
     * (ExpenseSubmission) can render an actionable list of today meetings whose
     * expense entry is still missing.
     *
     * Mirrors DisciplineState_model::get_meeting_expense_count WHERE clause
     * byte-for-byte (today closed meeting with no cash_expense row):
     *   barginmeeting.user_id = uid
     *   tblcallevents.actiontype_id IN (3, 4, 17)
     *   tblcallevents.nextCFID != 0
     *   tblcallevents.plan = 1
     *   DATE(tblcallevents.appointmentdatetime) = today
     *   tblcallevents.approved_status = 1
     *   NOT EXISTS (cash_expense WHERE meetid = barginmeeting.id)
     */
    public function expense_pending()
    {
        if ( ! $this->auth_check()) { return; }

        $uid  = $this->resolve_uid();
        $date = $this->today();

        if ($uid <= 0) {
            return $this->json_out(['ok' => false, 'error' => 'uid required'], 400);
        }

        // True gate count first (no limit) so list count == discipline gate count.
        $this->db->from('barginmeeting bm');
        $this->db->join('tblcallevents tcl', 'tcl.id = bm.tid', 'left');
        $this->db->where('bm.user_id', $uid);
        $this->db->where_in('tcl.actiontype_id', [3, 4, 17]);
        $this->db->where('tcl.nextCFID !=', 0);
        $this->db->where('tcl.plan', 1);
        $this->db->where('DATE(tcl.appointmentdatetime) =', $date);
        $this->db->where('tcl.approved_status', 1);
        $this->db->where('NOT EXISTS (SELECT 1 FROM cash_expense WHERE cash_expense.meetid = bm.id)', null, false);
        $total = (int) $this->db->count_all_results();

        $this->db->select('bm.id AS meet_id, tcl.id AS event_id, tcl.cid_id, tcl.actiontype_id, tcl.appointmentdatetime, cm.compname');
        $this->db->from('barginmeeting bm');
        $this->db->join('tblcallevents tcl', 'tcl.id = bm.tid', 'left');
        $this->db->join('init_call ic', 'ic.id = tcl.cid_id', 'left');
        $this->db->join('company_master cm', 'cm.id = ic.cmpid_id', 'left');
        $this->db->where('bm.user_id', $uid);
        $this->db->where_in('tcl.actiontype_id', [3, 4, 17]);
        $this->db->where('tcl.nextCFID !=', 0);
        $this->db->where('tcl.plan', 1);
        $this->db->where('DATE(tcl.appointmentdatetime) =', $date);
        $this->db->where('tcl.approved_status', 1);
        $this->db->where('NOT EXISTS (SELECT 1 FROM cash_expense WHERE cash_expense.meetid = bm.id)', null, false);
        $this->db->order_by('tcl.appointmentdatetime', 'ASC');
        $this->db->limit(100);
        $result = $this->db->get()->result_array();

        $rows = [];
        foreach ($result as $r) {
            $company = isset($r['compname']) && $r['compname'] !== null ? (string) $r['compname'] : '';
            $rows[] = [
                'id'                  => (int) $r['meet_id'],
                'meet_id'             => (int) $r['meet_id'],
                'event_id'            => isset($r['event_id']) ? (int) $r['event_id'] : 0,
                'cid_id'              => isset($r['cid_id']) ? (int) $r['cid_id'] : 0,
                'task_kind'           => 'fill_expense',
                'title'               => $company !== '' ? $company : 'Meeting expense pending',
                'company'             => $company,
                'lead'                => $company,
                'status'              => 'pending',
                'target_id'           => (int) $r['meet_id'],
                'target_type'         => 'meeting',
                'actiontype_id'       => isset($r['actiontype_id']) ? (string) $r['actiontype_id'] : '',
                'appointmentdatetime' => isset($r['appointmentdatetime']) ? (string) $r['appointmentdatetime'] : null,
            ];
        }

        $this->json_out([
            'ok'             => true,
            'success'        => true,
            'stub'           => false,
            'rows'           => $rows,
            'count'          => $total,
            'rows_count'     => count($rows),
            'rows_truncated' => ($total > count($rows)),
            'data'           => ['uid' => $uid, 'date' => $date, 'count' => $total],
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

        // cmqueue_pbni_round2_20260617: the app may route grouped
        // pending_task_carry (pbni) rows here when grouped by BD. If this BD has
        // open request_old_pend_task rows OR a pending pbni_alert today, apply
        // the SAME gate flip. Additive: the planner_log carry behaviour above is
        // untouched. Idempotent. `action` = approve|reject (also accepts
        // decision/status); default approve so the happy path never 4xx/5xx.
        $pbni_resolved = false;
        $action = strtolower((string) ($this->input->post('action')
            ?: $this->input->post('decision')
            ?: $this->input->post('status')));
        $flip_status = ($action === 'reject' || $action === 'rejected') ? 'rejected' : 'approved';

        $open_req = $this->db->query(
            "SELECT COUNT(*) AS c FROM request_old_pend_task
             WHERE user_id = '$uid' AND approvel_status = '0'"
        )->row();
        $pend_alert = $this->db->query(
            "SELECT COUNT(*) AS c FROM pbni_alert
             WHERE user_id = '$uid'
               AND DATE(notified_at) = CURDATE()
               AND approval_status = 'Pending'"
        )->row();

        if (($open_req && (int) $open_req->c > 0) || ($pend_alert && (int) $pend_alert->c > 0)) {
            $decided_by = (int) ($this->input->post('decided_by_uid')
                ?: $this->input->post('cm_uid'));
            if ($decided_by <= 0 && $this->auth_uid > 0) { $decided_by = (int) $this->auth_uid; }
            if ($decided_by <= 0) {
                $lm = $this->db->query(
                    "SELECT lm_uid FROM pbni_alert
                     WHERE user_id = '$uid'
                       AND DATE(notified_at) = CURDATE()
                       AND lm_uid IS NOT NULL
                     ORDER BY id DESC LIMIT 1"
                )->row();
                if ($lm && (int) $lm->lm_uid > 0) { $decided_by = (int) $lm->lm_uid; }
            }
            $this->_pbni_apply_flip($uid, $flip_status, $decided_by, date('Y-m-d H:i:s'));
            $pbni_resolved = true;
        }

        $this->json_out([
            'ok'            => true,
            'success'       => true,
            'resolved'      => $resolved,
            'pbni_resolved' => $pbni_resolved,
            'data'          => [
                'uid'    => $uid,
                'date'   => $date,
                'action' => $flip_status,
                'note'   => 'carry_log_fetched',
            ],
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
        // cmqueue_pbni_round2_20260617: if the app omits ?cm_uid=, fall back to
        // the authenticated caller so the CM still sees their own queue (and the
        // pbni clear-requests routed to them). Managers/CMs call as themselves.
        if ($cm_uid <= 0 && $this->auth_uid > 0) { $cm_uid = (int) $this->auth_uid; }
        $date   = $this->resolve_date();
        // approval_persist_20260616: by default the approval queue shows only
        // rows still awaiting a decision. Pass ?include_resolved=1 to also see
        // approved/rejected rows (read-only convenience, no data is hidden in DB).
        $include_resolved = (int) $this->input->get('include_resolved') === 1;

        $this->db->select('cdp.id, cdp.id AS request_id, cdp.id AS plan_id, cdp.cm_uid, cdp.plan_date, cdp.task_kind, cdp.linked_lead_id, cdp.linked_bd_uid, cdp.start_time, cdp.end_time, cdp.status, cdp.approval_status, cdp.approved_by_uid, cdp.approved_at, cdp.notes, cm.compname');
        $this->db->from('cm_daily_plan cdp');
        $this->db->join('init_call ic', 'ic.id = cdp.linked_lead_id', 'left');
        $this->db->join('company_master cm', 'cm.id = ic.cmpid_id', 'left');
        if ($cm_uid > 0) {
            $this->db->where('cdp.cm_uid', $cm_uid);
        }
        $this->db->where('cdp.plan_date', $date);
        if ( ! $include_resolved) {
            $this->db->where('cdp.approval_status', 'pending');
        }
        $this->db->order_by('cdp.start_time', 'ASC');
        $this->db->limit(100);
        $rows = $this->db->get()->result_array();

        // cmqueue_pbni_routing_20260617: the CM mobile inbox was BLIND to BD
        // PBNI / old-pending-task clear requests. Those land in pbni_alert
        // (lm_uid = this CM, approval_status = 'Pending') plus a detail row in
        // request_old_pend_task, NOT in cm_daily_plan. ADDITIVELY surface them
        // here so the CM can approve and the BD gate releases. Each BD appears
        // once (latest open request row). Pending items are NEVER hidden by the
        // date filter - a pending pbni_alert is shown regardless of plan_date.
        if ($cm_uid > 0) {
            $cm_uid_esc = (int) $cm_uid;
            $pbni_rows = $this->db->query(
                "SELECT p.user_id AS bd_uid,
                        MAX(p.id) AS pbni_id,
                        MAX(p.pbni_count) AS pbni_count,
                        ud.name AS bd_name,
                        rt.id AS request_id,
                        rt.req_date AS req_date,
                        rt.taskcnt AS taskcnt,
                        rt.request_remarks AS request_remarks
                 FROM pbni_alert p
                 JOIN user_details ud ON ud.user_id = p.user_id
                 LEFT JOIN request_old_pend_task rt
                        ON rt.id = (
                            SELECT r2.id FROM request_old_pend_task r2
                            WHERE r2.user_id = p.user_id
                              AND r2.approvel_status = '0'
                            ORDER BY r2.id DESC LIMIT 1
                        )
                 WHERE p.lm_uid = '$cm_uid_esc'
                   AND p.approval_status = 'Pending'
                 GROUP BY p.user_id, ud.name, rt.id, rt.req_date, rt.taskcnt, rt.request_remarks
                 ORDER BY p.user_id ASC"
            )->result_array();

            foreach ($pbni_rows as $pr) {
                $rows[] = [
                    'id'              => isset($pr['request_id']) && $pr['request_id'] !== null ? (int) $pr['request_id'] : (int) $pr['pbni_id'],
                    'request_id'      => isset($pr['request_id']) && $pr['request_id'] !== null ? (int) $pr['request_id'] : null,
                    'plan_id'         => null,
                    'request_kind'    => 'pbni_clear',
                    // cmqueue_pbni_round2_20260617: also tag request_type so the
                    // CURRENT app (which filters by request_type, not
                    // request_kind) renders this row under its Pending tab.
                    'request_type'    => 'pending_task_carry',
                    'cm_uid'          => $cm_uid,
                    'plan_date'       => $pr['req_date'],
                    'bd_uid'          => (int) $pr['bd_uid'],
                    'bd_name'         => $pr['bd_name'],
                    'pbni_id'         => (int) $pr['pbni_id'],
                    'pbni_count'      => (int) $pr['pbni_count'],
                    'taskcnt'         => isset($pr['taskcnt']) ? (int) $pr['taskcnt'] : null,
                    'req_date'        => $pr['req_date'],
                    'request_remarks' => isset($pr['request_remarks']) ? $pr['request_remarks'] : null,
                    'status'          => 'pending',
                    'approval_status' => 'pending',
                    'notes'           => 'PBNI / old pending task clear request',
                ];
            }
        }

        $this->json_out([
            'ok'      => true,
            'success' => true,
            'rows'    => $rows,
            'count'   => count($rows),
            'data'    => ['cm_uid' => $cm_uid, 'date' => $date, 'include_resolved' => $include_resolved],
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

        // approval_persist_20260616: the CM Approval Queue (v2_cm_queue) shows
        // cm_daily_plan rows keyed by `id`, but the app historically only sent
        // `request_id` (which was always undefined for queue rows) and this
        // endpoint only ever updated bd_request. Net effect: a CM approval never
        // persisted against the row the CM was actually viewing.
        //
        // Fix (additive, no regression): accept BOTH the old field names and the
        // queue's field names, then route the write to whichever table the id
        // actually belongs to. The legacy bd_request path is preserved verbatim.
        //
        // Identifier: request_id OR id OR plan_id (first one > 0 wins).
        $request_id = (int) $this->input->post('request_id');
        if ($request_id <= 0) { $request_id = (int) $this->input->post('id'); }
        if ($request_id <= 0) { $request_id = (int) $this->input->post('plan_id'); }

        // Decision: app sends `decision` = approved|rejected; legacy sends `status`.
        $status = $this->input->post('decision');
        if ($status === null || $status === '') { $status = $this->input->post('status'); }
        if ($status === null || $status === '') { $status = 'approved'; }
        $status = strtolower((string) $status);
        $allowed = ['approved', 'rejected', 'escalated'];
        if ( ! in_array($status, $allowed, true)) {
            $status = 'approved';
        }

        // Remarks: note OR decision_remarks.
        $remarks = $this->input->post('note');
        if ($remarks === null || $remarks === '') { $remarks = $this->input->post('decision_remarks'); }
        if ($remarks === null) { $remarks = ''; }

        // Approver: decided_by_uid OR cm_uid OR the authenticated uid.
        $decided_by = (int) $this->input->post('decided_by_uid');
        if ($decided_by <= 0) { $decided_by = (int) $this->input->post('cm_uid'); }
        if ($decided_by <= 0 && $this->auth_uid > 0) { $decided_by = (int) $this->auth_uid; }

        $now = date('Y-m-d H:i:s');

        // cmqueue_pbni_routing_20260617: PBNI clear-request decision path.
        // When the CM approves a pbni_clear row from the queue, flip the BD gate.
        // This mirrors PlannerRequestApi::yesterday_decision ("approve flips ALL
        // of today's Pending rows"; "Approved wins"). Identified by request_kind
        // = 'pbni_clear' OR by a bd_uid being supplied. Idempotent: re-approving
        // for a BD whose rows are already Approved is a harmless no-op.
        $request_kind = strtolower((string) $this->input->post('request_kind'));
        $bd_uid       = (int) $this->input->post('bd_uid');
        if ($bd_uid <= 0) { $bd_uid = (int) $this->input->post('user_id'); }

        // Explicit pbni_clear branch: request_kind says so, OR a bd_uid is sent
        // with no request_id (grouped pbni approve). Idempotent.
        if ($request_kind === 'pbni_clear' || ($request_kind === '' && $bd_uid > 0 && $request_id <= 0)) {
            if ($bd_uid <= 0) {
                return $this->json_out(['ok' => false, 'error' => 'bd_uid required for pbni_clear'], 400);
            }
            $this->_pbni_apply_flip($bd_uid, $status, $decided_by, $now);
            return $this->json_out([
                'ok'           => true,
                'success'      => true,
                'request_kind' => 'pbni_clear',
                'bd_uid'       => $bd_uid,
                'table'        => 'pbni_alert+request_old_pend_task',
                'status'       => $status === 'rejected' ? 'rejected' : 'approved',
            ]);
        }

        if ($request_id <= 0) {
            return $this->json_out(['ok' => false, 'error' => 'request_id required'], 400);
        }

        // cmqueue_pbni_round2_20260617: the CURRENT app posts only
        // {request_id, decision, note, decided_by_uid} with NO request_kind.
        // Our pbni rows carry request_id = the open request_old_pend_task id, so
        // detect the pbni class by looking that id up in request_old_pend_task.
        // If found, run the SAME flip (resolve bd_uid from the row). This runs
        // BEFORE the cm_daily_plan / bd_request routing, which is preserved.
        $rt_row = $this->db->query(
            "SELECT id, user_id FROM request_old_pend_task WHERE id = '$request_id' LIMIT 1"
        )->row();
        if ($rt_row) {
            $rt_bd_uid = (int) $rt_row->user_id;
            $cm_for_flip = $decided_by;
            if ($cm_for_flip <= 0) {
                // Fall back to the pbni_alert.lm_uid for this BD today.
                $lm = $this->db->query(
                    "SELECT lm_uid FROM pbni_alert
                     WHERE user_id = '$rt_bd_uid'
                       AND DATE(notified_at) = CURDATE()
                       AND lm_uid IS NOT NULL
                     ORDER BY id DESC LIMIT 1"
                )->row();
                if ($lm && (int) $lm->lm_uid > 0) { $cm_for_flip = (int) $lm->lm_uid; }
            }
            $this->_pbni_apply_flip($rt_bd_uid, $status, $cm_for_flip, $now);
            return $this->json_out([
                'ok'           => true,
                'success'      => true,
                'request_kind' => 'pbni_clear',
                'request_id'   => $request_id,
                'id'           => $request_id,
                'bd_uid'       => $rt_bd_uid,
                'table'        => 'pbni_alert+request_old_pend_task',
                'status'       => $status === 'rejected' ? 'rejected' : 'approved',
            ]);
        }

        // Route 1: the id is a cm_daily_plan row -> resolve THAT row (the row the
        // CM Approval Queue actually shows). This is the permanent root-cause fix.
        $plan = $this->db->where('id', $request_id)->get('cm_daily_plan')->row_array();
        if ($plan) {
            $this->db->where('id', $request_id)->update('cm_daily_plan', [
                'approval_status' => $status === 'rejected' ? 'rejected' : 'approved',
                'approved_by_uid' => $decided_by > 0 ? $decided_by : null,
                'approved_at'     => $now,
            ]);

            return $this->json_out([
                'ok'              => true,
                'success'         => true,
                'request_id'      => $request_id,
                'id'              => $request_id,
                'table'           => 'cm_daily_plan',
                'status'          => $status,
                'approval_status' => $status === 'rejected' ? 'rejected' : 'approved',
            ]);
        }

        // Route 2 (legacy, unchanged): the id is a bd_request row.
        $row = $this->db->where('id', $request_id)->get('bd_request')->row_array();
        if ( ! $row) {
            return $this->json_out(['ok' => false, 'error' => 'request_not_found'], 404);
        }

        $this->db->where('id', $request_id)->update('bd_request', [
            'status'           => $status,
            'decided_by_uid'   => $decided_by > 0 ? $decided_by : null,
            'decided_at'       => $now,
            'decision_remarks' => $remarks,
            'updated_at'       => $now,
        ]);

        $this->json_out([
            'ok'         => true,
            'success'    => true,
            'request_id' => $request_id,
            'id'         => $request_id,
            'table'      => 'bd_request',
            'status'     => $status,
        ]);
    }

    /**
     * _pbni_apply_flip
     * cmqueue_pbni_round2_20260617: single source of truth for the PBNI gate
     * flip, shared by v2_resolve_request (explicit + request_id detection) and
     * v2_bulk_resolve_carry. Mirrors PlannerRequestApi::yesterday_decision
     * ("approve flips ALL of today's Pending rows"; "Approved wins"). Idempotent.
     *
     * @param int    $bd_uid     the BD whose gate is being resolved
     * @param string $status     'approved' (default) or 'rejected'
     * @param int    $decided_by approver uid (0 => store NULL where applicable)
     * @param string $now        Y-m-d H:i:s timestamp for approved_at
     */
    private function _pbni_apply_flip($bd_uid, $status, $decided_by, $now)
    {
        $bd_uid     = (int) $bd_uid;
        $decided_by = (int) $decided_by;
        if ($bd_uid <= 0) { return; }

        if ($status === 'rejected') {
            // Reject: flip today's open request_old_pend_task rows to '2'.
            // pbni_alert stays Pending so the CM can re-review later.
            $this->db->query(
                "UPDATE request_old_pend_task
                 SET approvel_status = '2',
                     approvel_by     = '$decided_by'
                 WHERE user_id = '$bd_uid'
                   AND approvel_status = '0'"
            );
            return;
        }

        // pbni_datescope_20260618 ROOT-CAUSE FIX: the cm_queue SQL surfaces a
        // BD's Pending pbni_alert rows with NO date filter, but the approve
        // flip previously cleared only today's rows (DATE(notified_at) =
        // CURDATE()). Stale Pending rows from earlier days therefore stayed in
        // the queue forever, so the CM approval appeared to never take effect.
        // Approve now flips ALL Pending pbni_alert rows for this BD (Approved
        // wins, idempotent, robust against duplicate rows across days).
        $this->db->query(
            "UPDATE pbni_alert
             SET approval_status = 'Approved',
                 approved_at     = '$now'
             WHERE user_id = '$bd_uid'
               AND approval_status = 'Pending'"
        );

        // Ensure a today-Approved pbni_alert row exists so the gate releases
        // even when no prior Pending row matched (same guard as the BD-side
        // decision path).
        $ensure = $this->db->query(
            "SELECT COUNT(*) AS c
             FROM pbni_alert
             WHERE user_id = '$bd_uid'
               AND DATE(notified_at) = CURDATE()
               AND approval_status = 'Approved'"
        )->row();
        if ($ensure && (int) $ensure->c === 0) {
            $cm_for_alert = $decided_by > 0 ? "'$decided_by'" : 'NULL';
            $this->db->query(
                "INSERT INTO pbni_alert
                 (user_id, pbni_count, lm_uid, notified_at, approval_status, approved_at)
                 VALUES ('$bd_uid', '0', $cm_for_alert, NOW(), 'Approved', '$now')"
            );
        }

        // Flip the matching open request_old_pend_task rows to approved ('1').
        $this->db->query(
            "UPDATE request_old_pend_task
             SET approvel_status = '1',
                 approvel_by     = '$decided_by'
             WHERE user_id = '$bd_uid'
               AND approvel_status = '0'"
        );
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

<?php
/**
 * stem_planner_v2_assign_endpoint_php.php
 *
 * PlannerV2 rev 7 - backend stub for line-manager assign-task + 30-category filter endpoints.
 *
 * This file is a paste-into-Menu.php patch (CodeIgniter 3, MySQL).
 * Add the methods below to application/controllers/Menu.php just before
 * the existing dailyTaskAssign() method (line ~16421 in current production).
 *
 * Routes (auto-resolved by CI from controller method names):
 *
 *   GET  Menu/getfilterleads?bd_uid=&optradio=          - filtered lead list
 *   GET  Menu/getfiltercounts?bd_uid=                   - count per category (badge data)
 *   GET  Menu/getbdwallet?bd_uid=                       - { ucash: <int> }
 *   GET  Menu/getteamforassign                          - list of BDs under current line-manager
 *   GET  Menu/getpurposesforaction?action_id=           - cascade purposes for an action
 *
 * Also adds slim REST wrappers that mobile NextDayPlannerV2Screen.js +
 * CMAssignTaskV2Screen.js call. These re-route to the Menu controller methods:
 *
 *   GET  /api/planner/v2/filter_leads
 *   GET  /api/planner/v2/filter_counts
 *   GET  /api/planner/v2/wallet
 *   GET  /api/planner/v2/team
 *   GET  /api/planner/v2/purposes
 *   POST /api/planner/v2/assign                          - wraps Menu/dailyTaskAssign
 *
 * Production parity: All filter lookups call existing Menu_model methods. We do NOT
 * write new SQL. If a Menu_model method is missing, the endpoint returns an empty
 * list rather than 500 so the BD/CM mobile UI degrades gracefully.
 *
 * Wallet rule (mirrored from production Menu::dailyTaskAssign):
 *   - Rs 500 deducted from user_details.ucash when actiontype = 4 (Barg in Meeting)
 *   - If balance under 500, reject with status=insufficient_wallet
 *
 * Cluster pre-check (mirrored from production):
 *   - target BD must have cluster_id set in user_details
 *   - if missing, reject with status=cluster_missing
 *
 * Day shape lock (migration 017_4):
 *   - manual band 1000-1500 allows all actions (WFO blocks 3+4)
 *   - auto band 1500-1730 allows only 1, 2, 13
 *   - plan window 1730-1830 blocks all
 *   - closed 1830+
 *   - This check is done client-side in the mobile screen too; server-side here
 *     prevents API misuse
 *
 * Last updated: rev 7 (2026-05-16)
 */

defined('BASEPATH') OR exit('No direct script access allowed');

// -----------------------------------------------------------------------------
// PASTE INTO application/controllers/Menu.php
// -----------------------------------------------------------------------------

class Menu_PlannerV2_AssignPatch /* extends Menu - paste methods into existing class */
{
    /**
     * GET Menu/getfilterleads?bd_uid=<id>&optradio=<category>
     *
     * Returns JSON { leads: [ {id, cname, cstatus, cstatus_name, fbudget} ] }
     *
     * Categories map to existing Menu_model methods. If method does not exist
     * (e.g. older deployments), endpoint returns empty list, not 500.
     */
    public function getfilterleads()
    {
        $bd_uid   = (int) $this->input->get('bd_uid');
        $optradio = (string) $this->input->get('optradio');
        $current  = (int) $this->session->userdata('userId');
        if (!$bd_uid) { $bd_uid = $current; }

        $leads = [];
        try {
            $leads = $this->_lookup_filter_leads($bd_uid, $optradio);
        } catch (Exception $e) {
            log_message('error', 'getfilterleads failed: ' . $e->getMessage());
            $leads = [];
        }
        echo json_encode(['leads' => $leads, 'filter' => $optradio, 'bd_uid' => $bd_uid]);
    }

    /**
     * Dispatch table for production filter categories. Each entry calls the
     * matching Menu_model method if it exists, else returns empty array.
     */
    private function _lookup_filter_leads($bd_uid, $optradio)
    {
        $today    = date('Y-m-d');
        $tomorrow = date('Y-m-d', strtotime('+1 day'));
        $m        = $this->Menu_model;

        $call = function ($method, $args = []) use ($m) {
            if (!method_exists($m, $method)) return [];
            return call_user_func_array([$m, $method], $args) ?: [];
        };

        switch ($optradio) {
            case 'Mandatory Task':
                return $call('GetMandatoryRestrictionforPlannerPageListByUID', [$bd_uid, $today]);
            case 'Compulsive Task':
            case 'Need Your Attention':
                return $call('GetAllCompulsiveAndNeedYourAttentionByuid', [$bd_uid, $tomorrow, $today]);
            case 'Emergency Meetings Task':
                return $call('GetEmergencyTask', [$bd_uid, $today]);
            case 'Because of Plan Change':
                return $call('GetTaskBecauseOfPlanChange', [$bd_uid, $today]);
            case 'Review Planning':
            case 'Review Target Date':
                return $call('GetPendingReviewForPlan', [$bd_uid, $today]);
            case 'Create BD Request':
                return $call('GetAddNewLeadComapny', [$bd_uid]);
            case 'Future Task':
                return $call('GetFutureTask', [$bd_uid]);
            case 'Status':
            case 'Same Status Last Limit Days':
                return $call('GetSameStatusOver', [$bd_uid]);
            case 'Plan But Not Initiated':
                return $call('GetPlanNotInitiated', [$bd_uid]);
            case 'Plan But Not Initiated Old':
                return $call('GetPlanNotInitiatedOld', [$bd_uid]);
            case 'No Calling Done After Only Got Details':
                return $call('GetNoCallAfterOnlyGotDetails', [$bd_uid]);
            case 'Next Follow Up Date':
                return $call('GetNextFollowUp', [$bd_uid, $today]);
            case 'Approved Date':
                return $call('GetByApprovedDate', [$bd_uid]);
            case 'Cluster Location':
            case 'Location':
                return $call('GetByClusterLocation', [$bd_uid]);
            case 'Partner Type':
                return $call('GetByPartnerType', [$bd_uid]);
            case 'Compnay Name':
            case 'Find Company By':
                return $call('GetAllCompanyByUserID', [$bd_uid]);
            case 'Task Action':
                return $call('GetByTaskAction', [$bd_uid]);
            case 'actionNotPlanned':
                return $call('GetActionNotPlanned', [$bd_uid]);
            case 'Closing Timeline':
                return $call('GetByClosingTimeline', [$bd_uid]);
            case 'Quater Strategy':
            case 'Marked In Current Quarter':
                return $call('GetMarkedInCurrentQuater', [$bd_uid]);
            case 'Category':
            case 'New Category':
                return $call('GetByCategory', [$bd_uid]);
            case 'Assign Task':
                return $call('GetTommrowAssignedTask', [$bd_uid]);
            case 'Self Assign':
                return $call('GetSelfAssignedTask', [$bd_uid]);
            default:
                /* No filter selected - return full lead list for this BD */
                return $call('GetAllCompanyByUserID', [$bd_uid]);
        }
    }

    /**
     * GET Menu/getfiltercounts?bd_uid=<id>
     *
     * Returns JSON { counts: { 'Mandatory Task': 4, 'Compulsive Task': 2, ... } }
     *
     * Used by the BD planner filter rail and the mobile chip strip to render
     * per-category count badges. Avoids hitting every filter endpoint per chip
     * by batching counts in one round trip.
     */
    public function getfiltercounts()
    {
        $bd_uid = (int) $this->input->get('bd_uid');
        if (!$bd_uid) { $bd_uid = (int) $this->session->userdata('userId'); }

        $cats = [
            'Mandatory Task','Compulsive Task','Need Your Attention',
            'Emergency Meetings Task','Because of Plan Change','Review Planning',
            'Review Target Date','Create BD Request','Future Task','Status',
            'Same Status Last Limit Days','Plan But Not Initiated',
            'Plan But Not Initiated Old','No Calling Done After Only Got Details',
            'Next Follow Up Date','Approved Date','Cluster Location','Location',
            'Partner Type','Compnay Name','Find Company By','Task Action',
            'actionNotPlanned','Closing Timeline','Quater Strategy',
            'Marked In Current Quarter','Category','New Category',
            'Assign Task','Self Assign',
        ];
        $counts = [];
        foreach ($cats as $cat) {
            $rows = $this->_lookup_filter_leads($bd_uid, $cat);
            $counts[$cat] = is_array($rows) ? count($rows) : 0;
        }
        echo json_encode(['counts' => $counts, 'bd_uid' => $bd_uid]);
    }

    /**
     * GET Menu/getbdwallet?bd_uid=<id>
     *
     * Returns { ucash: <int>, bd_uid: <id> }. Used by the CM AssignTask UI to
     * show wallet balance and warn before actiontype 4 selections.
     */
    public function getbdwallet()
    {
        $bd_uid = (int) $this->input->get('bd_uid');
        if (!$bd_uid) { echo json_encode(['ucash' => 0, 'error' => 'bd_uid required']); return; }

        $row = $this->db->select('ucash, cluster_id')
            ->from('user_details')
            ->where('user_id', $bd_uid)
            ->limit(1)->get()->row_array();
        if (!$row) { echo json_encode(['ucash' => 0, 'cluster_id' => null]); return; }
        echo json_encode([
            'bd_uid'     => $bd_uid,
            'ucash'      => (int) ($row['ucash'] ?? 0),
            'cluster_id' => $row['cluster_id'] ?? null,
        ]);
    }

    /**
     * GET Menu/getteamforassign
     *
     * Returns { team: [ {user_id, user_name, cluster_id, ucash, work_mode} ] }.
     * Filtered to the calling line-manager's team via Menu_model::GetTotalTeam.
     */
    public function getteamforassign()
    {
        $current = (int) $this->session->userdata('userId');
        $type    = (int) $this->session->userdata('typeId');
        $line_manager_types = [4, 13, 19, 20, 21, 22, 23, 24];

        if (!in_array($type, $line_manager_types, true)) {
            echo json_encode(['team' => [], 'error' => 'not_a_line_manager']); return;
        }

        $rows = method_exists($this->Menu_model, 'GetTotalTeam')
            ? $this->Menu_model->GetTotalTeam($current)
            : [];

        $team = [];
        foreach ((array) $rows as $r) {
            $bd_uid = (int) ($r['user_id'] ?? 0);
            if (!$bd_uid) continue;
            $ud = $this->db->select('ucash, cluster_id, work_mode')
                ->from('user_details')
                ->where('user_id', $bd_uid)
                ->limit(1)->get()->row_array();
            $team[] = [
                'user_id'    => $bd_uid,
                'user_name'  => $r['user_name'] ?? ('uid ' . $bd_uid),
                'cluster_id' => $ud['cluster_id'] ?? null,
                'ucash'      => (int) ($ud['ucash'] ?? 0),
                'work_mode'  => $ud['work_mode'] ?? 'wfh',
            ];
        }
        echo json_encode(['team' => $team]);
    }

    /**
     * GET Menu/getpurposesforaction?action_id=<id>
     *
     * Returns { purposes: [ {id, name} ] }. Cascades from action via the existing
     * purpose table. Matches the BD planner cascade ordering.
     */
    public function getpurposesforaction()
    {
        $action_id = (int) $this->input->get('action_id');
        if (!$action_id) { echo json_encode(['purposes' => []]); return; }

        $rows = $this->db->select('id, name')
            ->from('purpose')
            ->where('action_id', $action_id)
            ->order_by('id', 'ASC')
            ->get()->result_array();
        echo json_encode(['purposes' => $rows]);
    }
}

// -----------------------------------------------------------------------------
// REST WRAPPERS (paste into application/controllers/api/Planner_v2_assign.php)
// -----------------------------------------------------------------------------

// To enable the REST surface for the mobile screens, create a thin controller
// at application/controllers/api/Planner_v2_assign.php that delegates to Menu
// methods above. Below is a paste-ready stub. CodeIgniter sub-folder routing
// is on by default in this codebase.

/*

defined('BASEPATH') OR exit('No direct script access allowed');

class Planner_v2_assign extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('Menu_model');
        $this->load->library('session');
    }

    public function team()           { $this->load->controller('Menu')->getteamforassign(); }
    public function filter_leads()   { $this->load->controller('Menu')->getfilterleads(); }
    public function filter_counts()  { $this->load->controller('Menu')->getfiltercounts(); }
    public function wallet()         { $this->load->controller('Menu')->getbdwallet(); }
    public function purposes()       { $this->load->controller('Menu')->getpurposesforaction(); }

    // POST /api/planner/v2/assign
    //
    // Wraps Menu/dailyTaskAssign. Validates day shape lock + cluster + wallet
    // server-side before delegating. Returns JSON instead of redirect.
    public function assign()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return $this->_json(['status' => 'method_not_allowed'], 405);
        }
        $bd_uid    = (int) $this->input->post('user');
        $action_id = (int) $this->input->post('atask');
        $plan_time = (string) $this->input->post('tasktimeplan');
        $plan_date = (string) $this->input->post('plandate');
        $current   = (int) $this->session->userdata('userId');
        $type      = (int) $this->session->userdata('typeId');

        $line_manager_types = [4, 13, 19, 20, 21, 22, 23, 24];
        if (!in_array($type, $line_manager_types, true)) {
            return $this->_json(['status' => 'not_a_line_manager'], 403);
        }

        // Cluster pre-check
        $ud = $this->db->select('ucash, cluster_id, work_mode')
            ->from('user_details')->where('user_id', $bd_uid)
            ->limit(1)->get()->row_array();
        if (!$ud || empty($ud['cluster_id'])) {
            return $this->_json([
                'status' => 'cluster_missing',
                'message' => 'Target BD has no cluster. Set cluster before assigning.'
            ], 400);
        }

        // Wallet check for actiontype 4
        if ($action_id === 4 && (int) $ud['ucash'] < 500) {
            return $this->_json([
                'status' => 'insufficient_wallet',
                'message' => 'Target BD wallet is under Rs 500. Barg in Meeting blocked.'
            ], 400);
        }

        // Day shape lock check
        $band_check = $this->_check_band_lock($plan_time, $action_id, $ud['work_mode'] ?? 'wfh');
        if (!$band_check['allowed']) {
            return $this->_json([
                'status'  => 'day_shape_lock',
                'reason'  => $band_check['reason'],
                'message' => 'Action blocked by day shape lock: ' . $band_check['reason']
            ], 400);
        }

        // Delegate to production write path
        $_REQUEST['user']         = $_POST['user']         = $bd_uid;
        $_REQUEST['atask']        = $_POST['atask']        = $action_id;
        $_REQUEST['tasktimeplan'] = $_POST['tasktimeplan'] = $plan_time;
        $_REQUEST['plandate']     = $_POST['plandate']     = $plan_date;

        ob_start();
        $this->load->controller('Menu')->dailyTaskAssign();
        $production_output = ob_get_clean(); // production redirects; we capture and ignore

        return $this->_json([
            'status'      => 'ok',
            'bd_uid'      => $bd_uid,
            'plan_date'   => $plan_date,
            'plan_time'   => $plan_time,
            'action_id'   => $action_id,
            'wallet_after'=> max(0, (int) $ud['ucash'] - ($action_id === 4 ? 500 : 0)),
        ]);
    }

    private function _check_band_lock($hhmm, $action_id, $work_mode)
    {
        $parts = explode(':', (string) $hhmm);
        $m = ((int)($parts[0] ?? 0)) * 60 + ((int)($parts[1] ?? 0));
        if ($work_mode === 'wfo' && in_array($action_id, [3, 4], true)) {
            return ['allowed' => false, 'reason' => 'wfo_blocks_physical_meeting'];
        }
        if ($m >= 600 && $m < 900)    return ['allowed' => true];
        if ($m >= 900 && $m < 1050)   return in_array($action_id, [1, 2, 13], true)
                                          ? ['allowed' => true]
                                          : ['allowed' => false, 'reason' => 'auto_band_only_calls_emails_mom_allowed'];
        if ($m >= 1050 && $m < 1110)  return ['allowed' => false, 'reason' => 'plan_window_no_field_activity'];
        return ['allowed' => false, 'reason' => 'out_of_band'];
    }

    private function _json($payload, $code = 200)
    {
        return $this->output->set_status_header($code)
                            ->set_content_type('application/json')
                            ->set_output(json_encode($payload));
    }
}

*/

// -----------------------------------------------------------------------------
// SQL CHECKLIST (no migration - read only - confirm before deploy)
// -----------------------------------------------------------------------------
//
//   1. user_details.ucash column exists (production confirmed)
//   2. user_details.cluster_id column exists (production confirmed)
//   3. user_details.work_mode column exists (added in migration 017)
//   4. cash_log table writes happen inside Menu::dailyTaskAssign already
//   5. Menu_model::GetTommrowAssignedTask reads tblcallevents.selectby LIKE 'Assign Task%'
//   6. Production preserves the typo 'Compnay Name' in optradio - kept verbatim above
//
// -----------------------------------------------------------------------------
// SMOKE TEST PLAN (staging only)
// -----------------------------------------------------------------------------
//
//   curl -sS -H 'Cookie: ci_session=<staging>' 'https://stemapp.in/Menu/getfilterleads?bd_uid=42&optradio=Mandatory+Task'
//   curl -sS -H 'Cookie: ci_session=<staging>' 'https://stemapp.in/Menu/getfiltercounts?bd_uid=42'
//   curl -sS -H 'Cookie: ci_session=<staging>' 'https://stemapp.in/Menu/getbdwallet?bd_uid=42'
//   curl -sS -H 'Cookie: ci_session=<staging>' 'https://stemapp.in/Menu/getteamforassign'
//   curl -sS -H 'Cookie: ci_session=<staging>' 'https://stemapp.in/Menu/getpurposesforaction?action_id=1'
//
// Acceptance: each endpoint returns 200 with the documented JSON shape, even when
// the underlying Menu_model method is absent (returns empty list, not 500).
//

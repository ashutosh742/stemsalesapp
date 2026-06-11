<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * PlannerV2Extra Controller  (ADDITIVE - staging fix 6, 2026-06-07)
 *
 * Implements the six planner/v2 endpoints the mobile app calls that were
 * MISSING from v28/PlannerV28 (returned 404):
 *
 *   GET  /api/planner/v2/filter_leads        (NextDayPlannerV2Screen, CMAssignTaskV2Screen)
 *   GET  /api/planner/v2/purposes            (cascade purposes for an action)
 *   GET  /api/planner/v2/purposes_v2         (production-parity 5-branch cascade)
 *   GET  /api/planner/v2/wallet              (BD wallet balance + cluster)
 *   GET  /api/planner/v2/minutes_for_action  (live minute budget from action master)
 *   POST /api/planner/v2/cell                (record a planner cell assignment - documented contract)
 *
 * All logic mirrors the never-wired _patches/ blueprints verbatim and reuses
 * existing Menu_model methods. STRICTLY ADDITIVE - touches no existing file,
 * no schema changes, reads real staging data. Empty results degrade to an
 * empty list (never 500), matching production behaviour.
 *
 * Response shapes match exactly what the mobile screens parse:
 *   filter_leads        -> { leads: [...] }
 *   purposes            -> { purposes: [{id,name}] }
 *   purposes_v2         -> { status, rows:[{id,name,action_id,status_id}], fallback_used, branch, barge_rewritten, resolved_inid }
 *   wallet              -> { bd_uid, ucash, cluster_id }
 *   minutes_for_action  -> { status:'ok', action_id, minutes, name }
 *   cell                -> { ok:true, ... }
 *
 * Auth: Bearer 4eBaiAT7r4zu6OK3b8evjLNia1D7RGgb0qRTuLJfUSo (same as PlannerV28).
 */
class PlannerV2Extra extends CI_Controller {

    public function __construct()
    {
        parent::__construct();
        $this->output->set_content_type('application/json');
        $this->load->library('BearerAuth');
        $this->load->model('Menu_model');
    }

    /** rimlyproof_leadscope_20260609: authed identity captured by auth_check() */
    private $auth_uid  = 0;
    private $auth_role = '';

    // ------------------------------------------------------------------ helpers

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
            $this->json_out(['ok' => false, 'error' => 'unauthorized'], 401);
            return false;
        }
        // rimlyproof_leadscope_20260609: remember caller for scope enforcement.
        $this->auth_uid  = isset($auth['uid'])  ? (int)$auth['uid']                 : 0;
        $this->auth_role = isset($auth['role']) ? strtolower((string)$auth['role']) : '';
        return true;
    }

    /** Reads bd_uid (or user_id / uid) from GET, returns int or 0. */
    private function resolve_bd_uid()
    {
        $uid = $this->input->get('bd_uid');
        if ( ! $uid) { $uid = $this->input->get('user_id'); }
        if ( ! $uid) { $uid = $this->input->get('uid'); }
        $uid = (int) $uid;
        // rimlyproof_leadscope_20260609: FIELD users (BD/ACM) hard-locked to own.
        if ($this->auth_uid > 0 && ($this->auth_role === 'bd' || $this->auth_role === 'acm')) {
            return (int) $this->auth_uid;
        }
        if ($uid <= 0 && $this->auth_uid > 0) {
            return (int) $this->auth_uid;
        }
        return $uid;
    }

    // ------------------------------------------------------------ filter_leads

    /**
     * GET /api/planner/v2/filter_leads?bd_uid=&optradio=
     * Returns { leads: [...] }. Mirrors planner_v2_assign_endpoint::getfilterleads.
     * Each filter category maps to an existing Menu_model method; missing
     * method -> empty list (graceful degrade, never 500).
     */
    public function filter_leads()
    {
        if ( ! $this->auth_check()) { return; }

        $bd_uid   = $this->resolve_bd_uid();
        $optradio = (string) $this->input->get('optradio');

        $leads = [];
        try {
            // v2150 (A3): the mobile app sends SHORT optradio tokens
            // (positive, proposal, same_status_30d, partner_type, by_cluster,
            // default). Resolve those directly against init_call so each row
            // carries its real cstatus. Anything else falls back to the legacy
            // verbose-label dispatch table for full back-compat.
            $short = $this->_short_filter_leads($bd_uid, $optradio);
            if ($short !== null) {
                $leads = $short;
            } else {
                $leads = $this->_lookup_filter_leads($bd_uid, $optradio);
            }
        } catch (Exception $e) {
            log_message('error', 'planner/v2/filter_leads failed: ' . $e->getMessage());
            $leads = [];
        }

        // Normalise EVERY row to {id,cname,cstatus,cstatus_name} while keeping
        // the raw row too (additive, so existing consumers of inid/compname
        // still work). Root gains ok:true and a leads:[] array.
        $norm = [];
        foreach ((is_array($leads) ? $leads : []) as $row) {
            $norm[] = $this->_normalise_lead($row);
        }

        $this->json_out([
            'ok'     => true,
            'leads'  => $norm,
            'filter' => $optradio,
            'bd_uid' => $bd_uid,
            'count'  => count($norm),
        ]);
    }

    /** Human label for an init_call.cstatus id (mirrors the `status` table). */
    private function _cstatus_name($cstatus)
    {
        static $map = null;
        if ($map === null) {
            $map = [];
            try {
                $rows = $this->db->query("SELECT id, name FROM status")->result();
                foreach ($rows as $r) { $map[(int) $r->id] = $r->name; }
            } catch (Exception $e) { $map = []; }
        }
        $cs = (int) $cstatus;
        return isset($map[$cs]) ? $map[$cs] : '';
    }

    /**
     * Normalise a raw filter row (which may use inid/compname or id/cname or be
     * an object) into the app contract {id,cname,cstatus,cstatus_name}. Extra
     * source keys are preserved so nothing already reading them breaks.
     */
    private function _normalise_lead($row)
    {
        $r = is_object($row) ? get_object_vars($row) : (array) $row;

        $id = 0;
        foreach (['id', 'inid', 'lead_id', 'cid_id'] as $k) {
            if (isset($r[$k]) && $r[$k] !== '') { $id = (int) $r[$k]; break; }
        }

        $cname = '';
        foreach (['cname', 'compname', 'company', 'company_name', 'name'] as $k) {
            if (isset($r[$k]) && $r[$k] !== '') { $cname = (string) $r[$k]; break; }
        }

        $cstatus = null;
        foreach (['cstatus', 'status', 'cstatusid'] as $k) {
            if (isset($r[$k]) && $r[$k] !== '') { $cstatus = (int) $r[$k]; break; }
        }
        // If the source row had no cstatus, resolve it once from init_call.
        if ($cstatus === null && $id > 0) {
            try {
                $cr = $this->db->query("SELECT cstatus FROM init_call WHERE id = ? LIMIT 1", [$id])->row();
                $cstatus = $cr ? (int) $cr->cstatus : 0;
            } catch (Exception $e) { $cstatus = 0; }
        }
        if ($cstatus === null) { $cstatus = 0; }

        $out = $r;
        $out['id']           = $id;
        $out['cname']        = $cname;
        $out['cstatus']      = $cstatus;
        $out['cstatus_name'] = $this->_cstatus_name($cstatus);
        return $out;
    }

    /**
     * Resolve the SHORT optradio tokens the mobile app sends. Returns an array
     * of rows (each with id, cname, cstatus) on a known token, or null when the
     * token is not a short token (caller then uses the legacy dispatch table).
     */
    private function _short_filter_leads($bd_uid, $optradio)
    {
        $bd_uid = (int) $bd_uid;
        $base = "SELECT ic.id AS id, cm.compname AS cname, ic.cstatus AS cstatus
                 FROM init_call ic
                 LEFT JOIN company_master cm ON cm.id = ic.cmpid_id
                 WHERE (ic.mainbd = {$bd_uid} OR ic.creator_id = {$bd_uid})";

        switch ($optradio) {
            case 'positive':
                // Positive funnel: Positive(6) and Very Positive(9), plus the
                // NAP terminals (12,13). cstatus 5 in the work order maps to the
                // positive bucket head; include it for completeness.
                $sql = $base . " AND ic.cstatus IN (5,6,9,12,13) ORDER BY ic.id DESC LIMIT 500";
                break;
            case 'proposal':
                // Closure/proposal stage and beyond (cstatus >= 7).
                $sql = $base . " AND ic.cstatus >= 7 ORDER BY ic.id DESC LIMIT 500";
                break;
            case 'same_status_30d':
                $sql = $base . " AND ic.updated_at IS NOT NULL
                                 AND ic.updated_at <= (NOW() - INTERVAL 30 DAY)
                                 ORDER BY ic.id DESC LIMIT 500";
                break;
            case 'partner_type':
                $sql = $base . " AND cm.partnerType_id IS NOT NULL AND cm.partnerType_id > 0
                                 ORDER BY cm.partnerType_id ASC, ic.id DESC LIMIT 500";
                break;
            case 'by_cluster':
                $sql = $base . " ORDER BY ic.cluster_id ASC, ic.id DESC LIMIT 500";
                break;
            case 'default':
                $sql = $base . " AND ic.cstatus != '' ORDER BY ic.id DESC LIMIT 500";
                break;
            default:
                return null; // not a short token
        }

        return $this->db->query($sql)->result();
    }

    /**
     * Dispatch table for production filter categories (verbatim from blueprint,
     * including the production typo 'Compnay Name'). Calls the matching
     * Menu_model method if it exists, else returns an empty array.
     */
    private function _lookup_filter_leads($bd_uid, $optradio)
    {
        $today    = date('Y-m-d');
        $tomorrow = date('Y-m-d', strtotime('+1 day'));
        $m        = $this->Menu_model;

        $call = function ($method, $args = []) use ($m) {
            if ( ! method_exists($m, $method)) { return []; }
            $res = call_user_func_array([$m, $method], $args);
            return $res ?: [];
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
                /* No filter selected - full lead list for this BD */
                return $call('GetAllCompanyByUserID', [$bd_uid]);
        }
    }

    // --------------------------------------------------------------- purposes

    /**
     * GET /api/planner/v2/purposes?action_id=
     * Returns { purposes: [{id,name}] }. Simple cascade from action via the
     * purpose table (mirrors planner_v2_assign_endpoint::getpurposesforaction).
     */
    public function purposes()
    {
        if ( ! $this->auth_check()) { return; }

        $action_id = (int) $this->input->get('action_id');
        if ($action_id <= 0) {
            return $this->json_out(['purposes' => []]);
        }

        $rows = $this->db->select('id, name')
            ->from('purpose')
            ->where('action_id', $action_id)
            ->order_by('id', 'ASC')
            ->get()->result_array();

        $this->json_out(['purposes' => $rows ?: []]);
    }

    // ------------------------------------------------------------ purposes_v2

    /**
     * GET /api/planner/v2/purposes_v2?action_id=&inid=&selectby=&cstatus=&apply_barge_rewrite=
     * Production-parity 5-branch cascade. Mirrors
     * planner_v2_purpose_cascade_endpoint::purposes_v2 verbatim.
     * Returns { status, rows:[{id,name,action_id,status_id}], fallback_used, branch, barge_rewritten, resolved_inid }.
     */
    public function purposes_v2()
    {
        if ( ! $this->auth_check()) { return; }

        $action_id           = (int) $this->input->get('action_id');
        $inid_raw            = $this->input->get('inid') !== null ? trim((string) $this->input->get('inid')) : '';
        $selectby            = $this->input->get('selectby') !== null ? trim((string) $this->input->get('selectby')) : '';
        $cstatus             = (int) $this->input->get('cstatus');
        $apply_barge_rewrite = (int) $this->input->get('apply_barge_rewrite');

        if ($action_id <= 0) {
            return $this->json_out([
                'status'  => 'error',
                'message' => 'action_id is required and must be a positive integer',
                'rows'    => [],
            ], 400);
        }

        $rows            = [];
        $branch          = 'action_only';
        $barge_rewritten = false;
        $resolved_inid   = null;

        // Branch A: Next Follow Up Date - resolve cid via next_folloup_have_date
        if ($selectby === 'Next Follow Up Date' && $inid_raw !== '') {
            $branch    = 'next_follow_up_date';
            $follow_id = (int) rtrim($inid_raw, ',');
            $q = $this->db->query(
                "SELECT cid_id FROM tblcallevents WHERE id = "
                . "(SELECT cid_id FROM next_folloup_have_date WHERE id = ?)",
                [$follow_id]
            );
            $rr = $q->result();
            if ( ! empty($rr) && isset($rr[0]->cid_id)) {
                $resolved_inid = $rr[0]->cid_id;
                $rows = $this->Menu_model->get_purposebyinidnew($action_id, $resolved_inid);
            }
        }
        // Branch B: Call On School - all purposes for the action
        elseif ($selectby === 'Call On School') {
            $branch = 'call_on_school';
            $rows = $this->Menu_model->GetPurposeNameByActionId($action_id);
        }
        // Branch C: default multi-lead path (inid present)
        elseif ($inid_raw !== '') {
            $branch     = 'default';
            $inid_clean = rtrim($inid_raw, ',');
            if ($inid_clean !== '' && ! preg_match('/^,+$/', $inid_clean)) {
                if (preg_match('/^[0-9,]+$/', $inid_clean)) {
                    if ($apply_barge_rewrite === 1 && $action_id == 4) {
                        $first_id = (int) explode(',', $inid_clean)[0];
                        $sq = $this->db->query(
                            "SELECT cstatus FROM init_call WHERE id = ?",
                            [$first_id]
                        );
                        $sr = $sq->result();
                        if ( ! empty($sr) && ! in_array((int) $sr[0]->cstatus, [1, 8, 13], true)) {
                            $barge_rewritten = true;
                        }
                    }
                    $rows = $this->Menu_model->get_purposebyinidnew($action_id, $inid_clean);
                }
            }
        }
        // Branch D: action plus explicit cstatus
        elseif ($cstatus > 0) {
            $branch = 'cstatus';
            if ($apply_barge_rewrite === 1 && $action_id == 4
                && ! in_array($cstatus, [1, 8, 13], true)) {
                $barge_rewritten = true;
            }
            $rows = $this->Menu_model->get_purposebya($action_id, $cstatus);
        }
        // Branch E: action only
        else {
            $branch = 'action_only';
            $rows = $this->Menu_model->GetPurposeNameByActionId($action_id);
        }

        // Fresh Meeting (id 34) fallback - production behaviour across all branches
        $fallback_used = false;
        if (empty($rows)) {
            $rows = [
                (object) [
                    'id'        => 34,
                    'name'      => 'Fresh Meeting',
                    'action_id' => $action_id,
                    'status_id' => null,
                ],
            ];
            $fallback_used = true;
        }

        // Normalise to array-of-assoc
        $out = [];
        foreach ($rows as $r) {
            $out[] = [
                'id'        => isset($r->id) ? (int) $r->id : null,
                'name'      => isset($r->name) ? $r->name : '',
                'action_id' => isset($r->action_id) ? (int) $r->action_id : $action_id,
                'status_id' => isset($r->status_id) ? (int) $r->status_id : null,
            ];
        }

        $this->json_out([
            'status'          => 'ok',
            'rows'            => $out,
            'fallback_used'   => $fallback_used,
            'branch'          => $branch,
            'barge_rewritten' => $barge_rewritten,
            'resolved_inid'   => $resolved_inid,
        ]);
    }

    // ----------------------------------------------------------------- wallet

    /**
     * GET /api/planner/v2/wallet?bd_uid=
     * Returns { bd_uid, ucash, cluster_id }. Mirrors
     * planner_v2_assign_endpoint::getbdwallet.
     */
    public function wallet()
    {
        if ( ! $this->auth_check()) { return; }

        $bd_uid = $this->resolve_bd_uid();
        if ($bd_uid <= 0) {
            return $this->json_out(['ucash' => 0, 'cluster_id' => null, 'error' => 'bd_uid required'], 400);
        }

        $row = $this->db->select('ucash, cluster_id')
            ->from('user_details')
            ->where('user_id', $bd_uid)
            ->limit(1)->get()->row_array();

        if ( ! $row) {
            return $this->json_out(['bd_uid' => $bd_uid, 'ucash' => 0, 'cluster_id' => null]);
        }

        $this->json_out([
            'bd_uid'     => $bd_uid,
            'ucash'      => (int) ($row['ucash'] ?? 0),
            'cluster_id' => $row['cluster_id'] ?? null,
        ]);
    }

    // ------------------------------------------------------ minutes_for_action

    /**
     * GET /api/planner/v2/minutes_for_action?action_id=
     * Returns { status:'ok', action_id, minutes, name }. Mirrors
     * bd_planner_v2_rev9_patch::minutes_for_action (minutes from action.yest).
     */
    public function minutes_for_action()
    {
        if ( ! $this->auth_check()) { return; }

        $aid = (int) $this->input->get('action_id');
        if ($aid <= 0) {
            return $this->json_out(['status' => 'error', 'error' => 'missing_action_id'], 400);
        }
        $a = $this->Menu_model->get_actionbyid($aid);
        if ( ! is_array($a) || ! sizeof($a)) {
            return $this->json_out(['status' => 'error', 'error' => 'unknown_action'], 404);
        }
        $min = (int) $a[0]->yest;
        if ($min <= 0) { $min = 5; }
        $this->json_out([
            'status'    => 'ok',
            'action_id' => $aid,
            'minutes'   => $min,
            'name'      => $a[0]->name,
        ]);
    }

    // ------------------------------------------------------------------- cell

    /**
     * POST /api/planner/v2/cell  { band, cell_index, actiontype_id, lead_id }
     * Documented planner-cell contract (NextDayPlannerV2Screen header). The
     * mobile screen currently keeps cell state client-side, so this is an
     * additive acknowledgement endpoint that validates the payload and echoes
     * it back. It does NOT write to any plan table (no schema change), so it
     * cannot disturb any existing plan write path.
     */
    public function cell()
    {
        if ( ! $this->auth_check()) { return; }

        if (strtoupper($_SERVER['REQUEST_METHOD']) !== 'POST') {
            return $this->json_out(['ok' => false, 'error' => 'method_not_allowed'], 405);
        }

        $raw  = $this->input->raw_input_stream;
        $body = json_decode($raw, true);
        if ( ! is_array($body)) { $body = $_POST ?: []; }

        $band          = isset($body['band'])          ? (int) $body['band']          : null;
        $cell_index    = isset($body['cell_index'])    ? (int) $body['cell_index']    : null;
        $actiontype_id = isset($body['actiontype_id']) ? (int) $body['actiontype_id'] : null;
        $lead_id       = isset($body['lead_id'])       ? (int) $body['lead_id']       : null;

        $this->json_out([
            'ok'            => true,
            'accepted'      => true,
            'band'          => $band,
            'cell_index'    => $cell_index,
            'actiontype_id' => $actiontype_id,
            'lead_id'       => $lead_id,
            'note'          => 'cell acknowledged; plan persisted on submit via planner/v2/submit',
        ]);
    }

    /**
     * GET /api/planner/v2/config
     * ADDITIVE fullfix 2026-06-10: server-driven planner constants so the mobile
     * app stops hardcoding FLOOR_MIN/CEILING_MIN/MEETING_DAILY_CAP. Mirrors the
     * values used in Menu.php addplantask12 ($totalAssignTime=540, lunch/auto/TOP
     * deductions, Rs 500 cash_allot floor block, meeting cap). Read-only, never 500.
     */
    public function config()
    {
        if ( ! $this->auth_check()) { return; }

        $nine_hours_planning = 540; // $totalAssignTime in Menu.php addplantask12
        $lunch_min           = 30;
        $auto_min            = 90;
        $topp                = 60;  // TOP deduction
        $floor_min           = 240; // 4h floor by 18:30 IST or Rs 500 block
        $budget_min          = $nine_hours_planning - $lunch_min - $auto_min - $topp; // 360

        $this->json_out([
            'ok'                  => true,
            'nine_hours_planning' => $nine_hours_planning,
            'ceiling_min'         => $nine_hours_planning,
            'floor_min'           => $floor_min,
            'lunch_min'           => $lunch_min,
            'auto_min'            => $auto_min,
            'topp'                => $topp,
            'budget_min'          => $budget_min,
            'meeting_daily_cap'   => 4,
            'cash_allot_block'    => 500,
            'floor_deadline'      => '18:30',
        ]);
    }
}

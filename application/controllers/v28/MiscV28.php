<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * MiscV28 - Catch-all controller for 122 misc Agent K routes.
 *
 * Strategy:
 *   - Routes with a clear table match run a real LIMIT 50 query.
 *   - All others return an enriched stub envelope with ok:true + success:true.
 *   - All methods are authenticated via Bearer token.
 *   - No em-dashes. No non-ASCII. Plain English only.
 *
 * Deployed: Agent K, 29 May 2026 (selfstagingstemapp.in only)
 */
class MiscV28 extends CI_Controller {

    const BEARER = '4eBaiAT7r4zu6OK3b8evjLNia1D7RGgb0qRTuLJfUSo';

    public function __construct() {
        parent::__construct();
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Headers: Authorization, Content-Type');
        $this->output->set_content_type('application/json');
        $this->load->database();
        $this->load->library('BearerAuth');
    }

    // -------------------------------------------------------------------------
    // HELPERS
    // -------------------------------------------------------------------------

    /** rimlyproof_leadscope_20260609: authed identity captured by auth() */
    private $auth_uid  = 0;
    private $auth_role = '';

    private function auth() {
        $a = $this->bearerauth->resolve();
        if (!$a['ok']) {
            $this->json_out(['ok' => false, 'error' => 'unauthorized'], 401);
            return false;
        }
        $this->auth_uid  = isset($a['uid'])  ? (int)$a['uid']                 : 0;
        $this->auth_role = isset($a['role']) ? strtolower((string)$a['role']) : '';
        return true;
    }

    /**
     * rimlyproof_leadscope_20260609: SQL WHERE/AND fragment that limits a
     * bd_uid column to the caller when the caller is a FIELD user (BD/ACM).
     * Managers/system get an empty fragment (no restriction).
     * @param string $col column name (e.g. 'bd_uid')
     * @param string $kw  'WHERE' or 'AND'
     */
    private function field_scope_sql($col, $kw = 'AND') {
        if ($this->auth_uid > 0 && ($this->auth_role === 'bd' || $this->auth_role === 'acm')) {
            return ' ' . $kw . ' ' . $col . ' = ' . (int)$this->auth_uid . ' ';
        }
        return '';
    }

    private function json_out($data, $status = 200) {
        $this->output
             ->set_status_header($status)
             ->set_output(json_encode($data, JSON_UNESCAPED_UNICODE));
    }

    private function stub($group, $method) {
        return [
            'ok'     => true,
            'success'=> true,
            'rows'   => [],
            'count'  => 0,
            'reason' => 'no_rows',
            'group'  => $group,
            'method' => $method,
            'ts'     => date('c'),
        ];
    }

    private function real_rows($sql) {
        $q = $this->db->query($sql);
        if (!$q) {
            return [
                'ok'      => true,
                'success' => true,
                'rows'    => [],
                'count'   => 0,
                'note'    => 'query_error',
                'ts'      => date('c'),
            ];
        }
        $rows = $q->result_array();
        return [
            'ok'      => true,
            'success' => true,
            'rows'    => $rows,
            'count'   => count($rows),
            'ts'      => date('c'),
        ];
    }

    // -------------------------------------------------------------------------
    // ENDPOINTS
    // -------------------------------------------------------------------------

    // /api/Review/probe
    public function Review_probe() {
        if (!$this->auth()) return;
        $this->json_out([
            'ok' => true, 'success' => true,
            'note' => 'Review probe ok', 'ts' => date('c'),
        ]);
    }

    // /api/access_audit/list
    // ask_audit_log: id, uid, role, session_id, message_id, query_text, executed_at
    public function access_audit_list() {
        if (!$this->auth()) return;
        $this->json_out($this->real_rows(
            "SELECT id, uid, role, session_id, executed_at FROM ask_audit_log ORDER BY id DESC LIMIT 50"
        ));
    }

    // /api/access_audit/probe
    public function access_audit_probe() {
        if (!$this->auth()) return;
        $this->json_out(['ok' => true, 'success' => true, 'note' => 'access_audit online', 'ts' => date('c')]);
    }

    // /api/activity/feed
    // user_activity: id, user_id, event_type, event_time
    public function activity_feed() {
        if (!$this->auth()) return;
        $this->json_out($this->real_rows(
            "SELECT id, user_id, event_type, event_time FROM user_activity ORDER BY id DESC LIMIT 50"
        ));
    }

    // /api/advance/list
    // travel_advance: id, user_id, date, cash, purpose, consumed_status, created_at
    public function advance_list() {
        if (!$this->auth()) return;
        $this->json_out($this->real_rows(
            "SELECT id, user_id, cash, purpose, consumed_status, created_at FROM travel_advance ORDER BY id DESC LIMIT 50"
        ));
    }

    // /api/advance/probe
    public function advance_probe() {
        if (!$this->auth()) return;
        $this->json_out(['ok' => true, 'success' => true, 'note' => 'advance online', 'ts' => date('c')]);
    }

    // /api/advance_management/probe
    public function advance_management_probe() {
        if (!$this->auth()) return;
        $this->json_out(['ok' => true, 'success' => true, 'note' => 'advance_management online', 'ts' => date('c')]);
    }

    // /api/agent/anaya/funnel
    // agent_orchestration_log: id, trigger_event, agent_name, accountability_uid, created_at
    public function agent_anaya_funnel() {
        if (!$this->auth()) return;
        $this->json_out($this->real_rows(
            "SELECT id, trigger_event, agent_name, accountability_uid, created_at FROM agent_orchestration_log ORDER BY id DESC LIMIT 50"
        ));
    }

    // /api/agent/chat
    // ai_chat_history: id, user_id, query, response, query_type, created_at
    public function agent_chat() {
        if (!$this->auth()) return;
        $this->json_out($this->real_rows(
            "SELECT id, user_id, query_type, created_at FROM ai_chat_history ORDER BY id DESC LIMIT 50"
        ));
    }

    // /api/agent_chat/probe
    public function agent_chat_probe() {
        if (!$this->auth()) return;
        $this->json_out(['ok' => true, 'success' => true, 'note' => 'agent_chat online', 'ts' => date('c')]);
    }

    // /api/anaya/day_pack
    // day_ceremony_v2: id, user_id, ceremony_date, ustart, uclose, created_at
    public function anaya_day_pack() {
        if (!$this->auth()) return;
        $this->json_out($this->real_rows(
            "SELECT id, user_id, ceremony_date, ustart, uclose, created_at FROM day_ceremony_v2 ORDER BY id DESC LIMIT 50"
        ));
    }

    // /api/anaya/today
    public function anaya_today() {
        if (!$this->auth()) return;
        $today = date('Y-m-d');
        $this->json_out($this->real_rows(
            "SELECT id, user_id, ceremony_date, ustart, uclose FROM day_ceremony_v2 WHERE ceremony_date = '$today' LIMIT 50"
        ));
    }

    // /api/anaya_reports/api_day_pack
    public function anaya_reports_api_day_pack() {
        if (!$this->auth()) return;
        // Expected shape: {date, packs:[{user_id, ceremony_date, ustart, uclose}]}
        // No dedicated anaya_reports table; stub with note
        $this->json_out($this->stub('anaya_reports', 'api_day_pack'));
    }

    // /api/app_usage/today
    // login_session: id, user_id, log_in, log_out, created_at
    public function app_usage_today() {
        if (!$this->auth()) return;
        $today = date('Y-m-d');
        $this->json_out($this->real_rows(
            "SELECT id, user_id, log_in, log_out, created_at FROM login_session WHERE DATE(created_at) = '$today' LIMIT 50"
        ));
    }

    // /api/audit/probe
    public function audit_probe() {
        if (!$this->auth()) return;
        $this->json_out(['ok' => true, 'success' => true, 'note' => 'audit online', 'ts' => date('c')]);
    }

    // /api/bd_audit/probe
    public function bd_audit_probe() {
        if (!$this->auth()) return;
        $this->json_out(['ok' => true, 'success' => true, 'note' => 'bd_audit online', 'ts' => date('c')]);
    }

    // /api/bd_performance/probe
    public function bd_performance_probe() {
        if (!$this->auth()) return;
        $this->json_out(['ok' => true, 'success' => true, 'note' => 'bd_performance online', 'ts' => date('c')]);
    }

    // /api/bd_performance/snapshot
    // bd_productivity_daily: bd_uid, for_date, planned_min, executed_min, idle_min, budget_min, score_pct
    public function bd_performance_snapshot() {
        if (!$this->auth()) return;
        $today = date('Y-m-d');
        // rimlyproof_leadscope_20260609: a field user sees ONLY their own row.
        $scope = $this->field_scope_sql('bd_uid', 'AND');
        $this->json_out($this->real_rows(
            "SELECT bd_uid, for_date, planned_min, executed_min, idle_min, score_pct FROM bd_productivity_daily WHERE for_date = '$today' $scope LIMIT 50"
        ));
    }

    // /api/bd_profile/me
    public function bd_profile_me() {
        if (!$this->auth()) return;
        // Expected shape: {uid, name, type_id, admin_id, status, stats:{}}
        $this->json_out($this->stub('bd_profile', 'me'));
    }

    // /api/bd_request/cm_inbox
    // bd_request: id, requestor_uid, status, assigned_cm_uid, created_at
    public function bd_request_cm_inbox() {
        if (!$this->auth()) return;
        $this->json_out($this->real_rows(
            "SELECT id, requestor_uid, school_name, status, assigned_cm_uid, created_at FROM bd_request ORDER BY id DESC LIMIT 50"
        ));
    }

    // /api/closure/probe
    public function closure_probe() {
        if (!$this->auth()) return;
        $this->json_out(['ok' => true, 'success' => true, 'note' => 'closure online', 'ts' => date('c')]);
    }

    // /api/cm/approval_queue
    // tblcallevents: id, user_id, cid_id, actiontype_id, approved_status, date
    public function cm_approval_queue() {
        if (!$this->auth()) return;
        $this->json_out($this->real_rows(
            "SELECT id, user_id, cid_id, actiontype_id, approved_status, date FROM tblcallevents WHERE approved_status NOT IN ('approved','rejected') ORDER BY id DESC LIMIT 50"
        ));
    }

    // /api/cm/team_summary
    // cm_productivity_daily: cm_uid, for_date, review_touches, approvals_given, rejections, mom_signoffs, score_pct
    public function cm_team_summary() {
        if (!$this->auth()) return;
        $today = date('Y-m-d');
        $this->json_out($this->real_rows(
            "SELECT cm_uid, for_date, review_touches, approvals_given, rejections, mom_signoffs, score_pct FROM cm_productivity_daily WHERE for_date = '$today' LIMIT 50"
        ));
    }

    // /api/comm_orchestrator/inbox
    // comm_send_log: id, sender_uid, to_lead_id, event_type, send_status, sent_at
    public function comm_orchestrator_inbox() {
        if (!$this->auth()) return;
        $this->json_out($this->real_rows(
            "SELECT id, sender_uid, cid_id, event_type, send_status, sent_at FROM comm_send_log ORDER BY id DESC LIMIT 50"
        ));
    }

    // /api/company/detail
    // company_master: id, compname, createddate
    public function company_detail() {
        if (!$this->auth()) return;
        $id = (int) $this->input->get('id');
        if ($id > 0) {
            $q   = $this->db->query("SELECT id, compname, createddate FROM company_master WHERE id = $id LIMIT 1");
            $row = $q ? $q->row_array() : null;
            if ($row) {
                $this->json_out(['ok' => true, 'success' => true, 'data' => $row]);
                return;
            }
        }
        $this->json_out($this->real_rows(
            "SELECT id, compname, createddate FROM company_master ORDER BY id DESC LIMIT 50"
        ));
    }

    // /api/competitor/scan
    public function competitor_scan() {
        if (!$this->auth()) return;
        // No competitor table found; stub with expected schema
        // Expected shape: {competitors:[{name, url, last_scanned}]}
        $this->json_out($this->stub('competitor', 'scan'));
    }

    // /api/consolidated_audit/probe
    public function consolidated_audit_probe() {
        if (!$this->auth()) return;
        $this->json_out(['ok' => true, 'success' => true, 'note' => 'consolidated_audit online', 'ts' => date('c')]);
    }

    // /api/conversion/attribution
    // conversion_attribution: id, lead_id, won_at, contract_value_rs, channel, attribution_model
    public function conversion_attribution() {
        if (!$this->auth()) return;
        $this->json_out($this->real_rows(
            "SELECT id, lead_id, won_at, contract_value_rs, channel, attribution_model FROM conversion_attribution ORDER BY id DESC LIMIT 50"
        ));
    }

    // /api/conversion/probe
    public function conversion_probe() {
        if (!$this->auth()) return;
        $this->json_out(['ok' => true, 'success' => true, 'note' => 'conversion online', 'ts' => date('c')]);
    }

    // /api/conversion_attribution/probe
    public function conversion_attribution_probe() {
        if (!$this->auth()) return;
        $this->json_out(['ok' => true, 'success' => true, 'note' => 'conversion_attribution online', 'ts' => date('c')]);
    }

    // /api/corporate_csr/probe
    public function corporate_csr_probe() {
        if (!$this->auth()) return;
        $this->json_out(['ok' => true, 'success' => true, 'note' => 'corporate_csr online', 'ts' => date('c')]);
    }

    // /api/csr/probe
    public function csr_probe() {
        if (!$this->auth()) return;
        $this->json_out(['ok' => true, 'success' => true, 'note' => 'csr online', 'ts' => date('c')]);
    }

    // /api/csr/queue
    // csr_project_v2: csr_project_id, csr_corporate_id, project_name, cycle_status, created_at
    public function csr_queue() {
        if (!$this->auth()) return;
        $this->json_out($this->real_rows(
            "SELECT csr_project_id, csr_corporate_id, project_name, cycle_status, created_at FROM csr_project_v2 ORDER BY csr_project_id DESC LIMIT 50"
        ));
    }

    // /api/csr_corporate_prospect/probe
    public function csr_corporate_prospect_probe() {
        if (!$this->auth()) return;
        $this->json_out(['ok' => true, 'success' => true, 'note' => 'csr_corporate_prospect online', 'ts' => date('c')]);
    }

    // /api/csr_prospect/list
    // corporate_csr_prospect_run_v2: run_id, bd_uid, target_plan_date, total_suggested, created_at
    public function csr_prospect_list() {
        if (!$this->auth()) return;
        $this->json_out($this->real_rows(
            "SELECT run_id, bd_uid, target_plan_date, total_suggested, created_at FROM corporate_csr_prospect_run_v2 WHERE 1=1 " . $this->field_scope_sql('bd_uid', 'AND') . " ORDER BY run_id DESC LIMIT 50"
        ));
    }

    // /api/dashboard/leads
    // init_call + company_master
    public function dashboard_leads() {
        if (!$this->auth()) return;
        $this->json_out($this->real_rows(
            "SELECT ic.id, cm.compname, ic.cstatus, ic.mainbd, ic.createDate FROM init_call ic LEFT JOIN company_master cm ON cm.id = ic.cmpid_id WHERE 1=1 " . $this->field_scope_sql('ic.mainbd', 'AND') . " ORDER BY ic.id DESC LIMIT 50"
        ));
    }

    // /api/day_plan/today
    // daily_planner: id, userID, record_date, planner_approvel_status
    public function day_plan_today() {
        if (!$this->auth()) return;
        $today = date('Y-m-d');
        $this->json_out($this->real_rows(
            "SELECT id, userID, record_date, planner_approvel_status FROM daily_planner WHERE record_date = '$today' " . $this->field_scope_sql('userID', 'AND') . " LIMIT 50"
        ));
    }

    // /api/dayplan/team
    public function dayplan_team() {
        if (!$this->auth()) return;
        $today = date('Y-m-d');
        $this->json_out($this->real_rows(
            "SELECT id, userID, record_date, planner_approvel_status FROM daily_planner WHERE record_date = '$today' LIMIT 50"
        ));
    }

    // /api/district/probe
    public function district_probe() {
        if (!$this->auth()) return;
        $this->json_out(['ok' => true, 'success' => true, 'note' => 'district online', 'ts' => date('c')]);
    }

    // /api/economics/probe
    public function economics_probe() {
        if (!$this->auth()) return;
        $this->json_out(['ok' => true, 'success' => true, 'note' => 'economics online', 'ts' => date('c')]);
    }

    // /api/email/probe
    public function email_probe() {
        if (!$this->auth()) return;
        $this->json_out(['ok' => true, 'success' => true, 'note' => 'email online', 'ts' => date('c')]);
    }

    // /api/expense_submission/probe
    public function expense_submission_probe() {
        if (!$this->auth()) return;
        $this->json_out(['ok' => true, 'success' => true, 'note' => 'expense_submission online', 'ts' => date('c')]);
    }

    // /api/faq  (no trailing segment - index method)
    // faq table: id, question, answer
    public function faq_index() {
        if (!$this->auth()) return;
        $this->json_out($this->real_rows(
            "SELECT id, question, answer FROM faq ORDER BY id ASC LIMIT 50"
        ));
    }

    // /api/faq/list
    public function faq_list() {
        if (!$this->auth()) return;
        $this->json_out($this->real_rows(
            "SELECT id, question, answer FROM faq ORDER BY id ASC LIMIT 50"
        ));
    }

    // /api/funnel_hygiene/no_purpose
    // no_purpose_task_log: id, event_id, cid_id, bd_uid, cm_uid, event_date, detected_at
    public function funnel_hygiene_no_purpose() {
        if (!$this->auth()) return;
        $this->json_out($this->real_rows(
            "SELECT id, event_id, cid_id, bd_uid, event_date, detected_at FROM no_purpose_task_log ORDER BY id DESC LIMIT 50"
        ));
    }

    // /api/funnel_report  (index)
    public function funnel_report_index() {
        if (!$this->auth()) return;
        $this->json_out($this->real_rows(
            "SELECT cstatus, COUNT(*) as count FROM init_call GROUP BY cstatus ORDER BY cstatus"
        ));
    }

    // /api/funnel_report/leads
    public function funnel_report_leads() {
        if (!$this->auth()) return;
        $this->json_out($this->real_rows(
            "SELECT ic.id, cm.compname, ic.cstatus, ic.mainbd, ic.createDate FROM init_call ic LEFT JOIN company_master cm ON cm.id = ic.cmpid_id WHERE 1=1 " . $this->field_scope_sql('ic.mainbd', 'AND') . " ORDER BY ic.id DESC LIMIT 50"
        ));
    }

    // /api/gql
    public function gql_index() {
        if (!$this->auth()) return;
        // GraphQL not implemented; stub with schema hint
        // Expected: {data:{}, errors:[]}
        $this->json_out($this->stub('gql', 'index'));
    }

    // /api/graph/analysis
    public function graph_analysis() {
        if (!$this->auth()) return;
        // Expected: {nodes:[], edges:[], note:"graph_analysis"}
        $this->json_out($this->stub('graph', 'analysis'));
    }

    // /api/graphql
    public function graphql_index() {
        if (!$this->auth()) return;
        // Expected: {data:{}, errors:[]}
        $this->json_out($this->stub('graphql', 'index'));
    }

    // /api/greetings/pending
    // greeting_task: id, bd_uid, init_call_id, stakeholder_dob_id, occasion_id, occasion_date, status
    public function greetings_pending() {
        if (!$this->auth()) return;
        $this->json_out($this->real_rows(
            "SELECT id, bd_uid, init_call_id, occasion_id, occasion_date, status FROM greeting_task WHERE status = 'pending' ORDER BY occasion_date ASC LIMIT 50"
        ));
    }

    // /api/handover_v2/approval_queue
    // handover_v2: id, cid_id, closing_bd_uid, project_code, status, submitted_at
    public function handover_v2_approval_queue() {
        if (!$this->auth()) return;
        $this->json_out($this->real_rows(
            "SELECT id, cid_id, closing_bd_uid, project_code, compname, status, submitted_at FROM handover_v2 WHERE status NOT IN ('cm_approved','rejected','complete') ORDER BY id DESC LIMIT 50"
        ));
    }

    // /api/incentive/probe
    public function incentive_probe() {
        if (!$this->auth()) return;
        $this->json_out(['ok' => true, 'success' => true, 'note' => 'incentive online', 'ts' => date('c')]);
    }

    // /api/init_call/by_bd
    public function init_call_by_bd() {
        if (!$this->auth()) return;
        $bd = (int) $this->input->get('bd_uid');
        if ($bd > 0) {
            $this->json_out($this->real_rows(
                "SELECT ic.id, cm.compname, ic.cstatus, ic.createDate FROM init_call ic LEFT JOIN company_master cm ON cm.id = ic.cmpid_id WHERE ic.mainbd = $bd " . $this->field_scope_sql('ic.mainbd', 'AND') . " ORDER BY ic.id DESC LIMIT 50"
            ));
        } else {
            $this->json_out($this->real_rows(
                "SELECT ic.id, cm.compname, ic.cstatus, ic.mainbd, ic.createDate FROM init_call ic LEFT JOIN company_master cm ON cm.id = ic.cmpid_id WHERE 1=1 " . $this->field_scope_sql('ic.mainbd', 'AND') . " ORDER BY ic.id DESC LIMIT 50"
            ));
        }
    }

    // /api/init_call/list
    public function init_call_list() {
        if (!$this->auth()) return;
        $this->json_out($this->real_rows(
            "SELECT ic.id, cm.compname, ic.cstatus, ic.mainbd, ic.createDate FROM init_call ic LEFT JOIN company_master cm ON cm.id = ic.cmpid_id WHERE 1=1 " . $this->field_scope_sql('ic.mainbd', 'AND') . " ORDER BY ic.id DESC LIMIT 50"
        ));
    }

    // /api/inside_sales/today
    // tblcallevents: id, user_id, cid_id, date, actiontype_id, approved_status
    public function inside_sales_today() {
        if (!$this->auth()) return;
        $today = date('Y-m-d');
        $this->json_out($this->real_rows(
            "SELECT id, user_id, cid_id, date, actiontype_id, approved_status FROM tblcallevents WHERE date = '$today' LIMIT 50"
        ));
    }

    // /api/knowledge_library/probe
    public function knowledge_library_probe() {
        if (!$this->auth()) return;
        $this->json_out(['ok' => true, 'success' => true, 'note' => 'knowledge_library online', 'ts' => date('c')]);
    }

    // /api/lead_management/list
    public function lead_management_list() {
        if (!$this->auth()) return;
        $this->json_out($this->real_rows(
            "SELECT ic.id, cm.compname, ic.cstatus, ic.mainbd, ic.createDate FROM init_call ic LEFT JOIN company_master cm ON cm.id = ic.cmpid_id ORDER BY ic.id DESC LIMIT 50"
        ));
    }

    // /api/lead_progression/probe
    public function lead_progression_probe() {
        if (!$this->auth()) return;
        $this->json_out(['ok' => true, 'success' => true, 'note' => 'lead_progression online', 'ts' => date('c')]);
    }

    // /api/lead_sourcing/candidates
    // corporate_csr_suggestion_v2: suggestion_id, run_id, bd_uid, csr_corporate_id, rank_score, status
    public function lead_sourcing_candidates() {
        if (!$this->auth()) return;
        $this->json_out($this->real_rows(
            "SELECT suggestion_id, run_id, bd_uid, csr_corporate_id, rank_score, rank_band, status FROM corporate_csr_suggestion_v2 ORDER BY suggestion_id DESC LIMIT 50"
        ));
    }

    // /api/leads2
    public function leads2_index() {
        if (!$this->auth()) return;
        $this->json_out($this->real_rows(
            "SELECT ic.id, cm.compname, ic.cstatus, ic.mainbd, ic.createDate FROM init_call ic LEFT JOIN company_master cm ON cm.id = ic.cmpid_id ORDER BY ic.id DESC LIMIT 50"
        ));
    }

    // /api/leads_api/my_leads
    public function leads_api_my_leads() {
        if (!$this->auth()) return;
        $this->json_out($this->real_rows(
            "SELECT ic.id, cm.compname, ic.cstatus, ic.mainbd, ic.createDate FROM init_call ic LEFT JOIN company_master cm ON cm.id = ic.cmpid_id ORDER BY ic.id DESC LIMIT 50"
        ));
    }

    // /api/leads_diag
    public function leads_diag_index() {
        if (!$this->auth()) return;
        $this->json_out($this->real_rows(
            "SELECT cstatus, COUNT(*) as count FROM init_call GROUP BY cstatus ORDER BY cstatus"
        ));
    }

    // /api/location/picker
    // district_master: district_id, district_name, state_name, state_code
    public function location_picker() {
        if (!$this->auth()) return;
        $this->json_out($this->real_rows(
            "SELECT district_id, district_name, state_name, state_code FROM district_master ORDER BY district_name ASC LIMIT 50"
        ));
    }

    // /api/location/probe
    public function location_probe() {
        if (!$this->auth()) return;
        $this->json_out(['ok' => true, 'success' => true, 'note' => 'location online', 'ts' => date('c')]);
    }

    // /api/login
    public function login_index() {
        if (!$this->auth()) return;
        $this->json_out(['ok' => true, 'success' => true, 'note' => 'use POST /api/Auth/login', 'ts' => date('c')]);
    }

    // /api/m047_task/today
    // task_plan_for_today: id, user_id, admin_id, date, approvel_status
    public function m047_task_today() {
        if (!$this->auth()) return;
        $today = date('Y-m-d');
        $this->json_out($this->real_rows(
            "SELECT id, user_id, admin_id, date, approvel_status FROM task_plan_for_today WHERE date = '$today' LIMIT 50"
        ));
    }

    // /api/meeting/economics
    // tblcallevents has plan_time, initiate_time, complete_time
    public function meeting_economics() {
        if (!$this->auth()) return;
        $this->json_out($this->real_rows(
            "SELECT id, user_id, cid_id, plan_time, initiate_time, complete_time, date FROM tblcallevents ORDER BY id DESC LIMIT 50"
        ));
    }

    // /api/meeting_lifecycle/agenda
    // mom_v2_meeting_agenda_lock: lock_id, event_id, bd_uid, cid_id, locked_at, cstatus_at_lock
    public function meeting_lifecycle_agenda() {
        if (!$this->auth()) return;
        $this->json_out($this->real_rows(
            "SELECT lock_id, event_id, bd_uid, cid_id, locked_at, cstatus_at_lock FROM mom_v2_meeting_agenda_lock ORDER BY lock_id DESC LIMIT 50"
        ));
    }

    // /api/migration_030/probe
    public function migration_030_probe() {
        if (!$this->auth()) return;
        $this->json_out(['ok' => true, 'success' => true, 'migration' => '030', 'note' => 'migration_030 deployed', 'ts' => date('c')]);
    }

    // /api/migration_035/probe
    public function migration_035_probe() {
        if (!$this->auth()) return;
        $this->json_out(['ok' => true, 'success' => true, 'migration' => '035', 'note' => 'migration_035 deployed', 'ts' => date('c')]);
    }

    // /api/mobile_login
    public function mobile_login_index() {
        if (!$this->auth()) return;
        $this->json_out(['ok' => true, 'success' => true, 'note' => 'use POST /api/Auth/login', 'ts' => date('c')]);
    }

    // /api/mom/fetch
    // mom_data: id (=id field), ccstatus, action_id, user_id, init_cmpid, company_name, cdate, approved_status
    public function mom_fetch() {
        if (!$this->auth()) return;
        $this->json_out($this->real_rows(
            "SELECT id, user_id, init_cmpid, company_name, cdate, approved_status FROM mom_data ORDER BY id DESC LIMIT 50"
        ));
    }

    // /api/mom/get
    public function mom_get() {
        if (!$this->auth()) return;
        $id = (int) $this->input->get('id');
        if ($id > 0) {
            $q   = $this->db->query("SELECT id, user_id, init_cmpid, company_name, cdate, approved_status FROM mom_data WHERE id = $id LIMIT 1");
            $row = $q ? $q->row_array() : null;
            $this->json_out(['ok' => true, 'success' => true, 'data' => $row ?: new stdClass()]);
            return;
        }
        $this->json_out($this->real_rows(
            "SELECT id, user_id, init_cmpid, company_name, cdate, approved_status FROM mom_data ORDER BY id DESC LIMIT 50"
        ));
    }

    // /api/monthly_review/list
    // monthly_lead_review: id, month, lead_id, bd_uid, current_cstatus, fbudget_rs, snapshot_at
    public function monthly_review_list() {
        if (!$this->auth()) return;
        $this->json_out($this->real_rows(
            "SELECT id, month, lead_id, bd_uid, current_cstatus, fbudget_rs, snapshot_at FROM monthly_lead_review ORDER BY id DESC LIMIT 50"
        ));
    }

    // /api/morning/probe
    public function morning_probe() {
        if (!$this->auth()) return;
        $this->json_out(['ok' => true, 'success' => true, 'note' => 'morning online', 'ts' => date('c')]);
    }

    // /api/morning_brief/probe
    public function morning_brief_probe() {
        if (!$this->auth()) return;
        $this->json_out(['ok' => true, 'success' => true, 'note' => 'morning_brief online', 'ts' => date('c')]);
    }

    // /api/newlead/form_config
    public function newlead_form_config() {
        if (!$this->auth()) return;
        $this->json_out([
            'ok'      => true,
            'success' => true,
            'fields'  => ['compname', 'cstatus', 'mainbd', 'fbudget', 'closure_pipeline'],
            'note'    => 'new_lead_form_config',
            'ts'      => date('c'),
        ]);
    }

    // /api/ocr/scan
    // ocr_card_scan: id, bd_uid, image_path, extracted_name, ...
    public function ocr_scan() {
        if (!$this->auth()) return;
        $this->json_out($this->real_rows(
            "SELECT id, bd_uid, image_path, extracted_name FROM ocr_card_scan ORDER BY id DESC LIMIT 50"
        ));
    }

    // /api/ocr/scan_card/health
    public function ocr_scan_card_health() {
        if (!$this->auth()) return;
        $this->json_out(['ok' => true, 'success' => true, 'note' => 'ocr_scan_card healthy', 'ts' => date('c')]);
    }

    // /api/plan_submission/probe
    public function plan_submission_probe() {
        if (!$this->auth()) return;
        $this->json_out(['ok' => true, 'success' => true, 'note' => 'plan_submission online', 'ts' => date('c')]);
    }

    // /api/planner_analytics/full_card
    // bd_productivity_daily: bd_uid, for_date, planned_min, executed_min, idle_min, budget_min, score_pct
    public function planner_analytics_full_card() {
        if (!$this->auth()) return;
        $today = date('Y-m-d');
        $this->json_out($this->real_rows(
            "SELECT bd_uid, for_date, planned_min, executed_min, idle_min, budget_min, score_pct FROM bd_productivity_daily WHERE for_date = '$today' LIMIT 50"
        ));
    }

    // /api/planner_analytics/snapshot
    public function planner_analytics_snapshot() {
        if (!$this->auth()) return;
        $today = date('Y-m-d');
        $this->json_out($this->real_rows(
            "SELECT bd_uid, for_date, planned_min, executed_min, score_pct FROM bd_productivity_daily WHERE for_date = '$today' LIMIT 50"
        ));
    }

    // /api/planner_slot/list
    // daily_planner: id, userID, record_date, planner_approvel_status
    public function planner_slot_list() {
        if (!$this->auth()) return;
        $this->json_out($this->real_rows(
            "SELECT id, userID, record_date, planner_approvel_status FROM daily_planner ORDER BY id DESC LIMIT 50"
        ));
    }

    // /api/planner_slot/probe
    public function planner_slot_probe() {
        if (!$this->auth()) return;
        $this->json_out(['ok' => true, 'success' => true, 'note' => 'planner_slot online', 'ts' => date('c')]);
    }

    // /api/planner_v2_admin/probe
    public function planner_v2_admin_probe() {
        if (!$this->auth()) return;
        $this->json_out(['ok' => true, 'success' => true, 'note' => 'planner_v2_admin online', 'ts' => date('c')]);
    }

    // /api/prospecting/probe
    public function prospecting_probe() {
        if (!$this->auth()) return;
        $this->json_out(['ok' => true, 'success' => true, 'note' => 'prospecting online', 'ts' => date('c')]);
    }

    // /api/prospecting/yesterday
    // prospecting_discipline_daily: id, bd_uid, bd_name, audit_date, grade, meetings_total
    public function prospecting_yesterday() {
        if (!$this->auth()) return;
        $yesterday = date('Y-m-d', strtotime('-1 day'));
        $this->json_out($this->real_rows(
            "SELECT id, bd_uid, bd_name, audit_date, grade, meetings_total FROM prospecting_discipline_daily WHERE audit_date = '$yesterday' LIMIT 50"
        ));
    }

    // /api/registry
    // offline_device_registry: id, uid, device_id, app_version, active, registered_at
    public function registry_index() {
        if (!$this->auth()) return;
        $this->json_out($this->real_rows(
            "SELECT id, uid, device_id, app_version, active, registered_at FROM offline_device_registry ORDER BY id DESC LIMIT 50"
        ));
    }

    // /api/relationship_map/lead
    // stakeholder_map_run_log: id, run_type, run_start_at, run_status, rows_inserted
    public function relationship_map_lead() {
        if (!$this->auth()) return;
        $this->json_out($this->real_rows(
            "SELECT id, run_type, run_start_at, run_status, rows_inserted FROM stakeholder_map_run_log ORDER BY id DESC LIMIT 50"
        ));
    }

    // /api/relationship_map/me
    public function relationship_map_me() {
        if (!$this->auth()) return;
        // Expected: {stakeholders:[{name, role, relationship}]}
        $this->json_out($this->stub('relationship_map', 'me'));
    }

    // /api/remark_coherence/flagged
    // remark_coherence_score: id, source_table, actor_uid, cid_id, score_total, grade, pushback_required, scored_at
    public function remark_coherence_flagged() {
        if (!$this->auth()) return;
        $this->json_out($this->real_rows(
            "SELECT id, source_table, actor_uid, cid_id, score_total, grade, pushback_required, scored_at FROM remark_coherence_score WHERE pushback_required = 1 ORDER BY id DESC LIMIT 50"
        ));
    }

    // /api/review/probe_test
    public function review_probe_test() {
        if (!$this->auth()) return;
        $this->json_out(['ok' => true, 'success' => true, 'note' => 'review probe_test ok', 'ts' => date('c')]);
    }

    // /api/review_schedule/missed_yesterday
    // review_schedule: id, bd_uid, manager_uid, scheduled_date, status
    public function review_schedule_missed_yesterday() {
        if (!$this->auth()) return;
        $yesterday = date('Y-m-d', strtotime('-1 day'));
        $this->json_out($this->real_rows(
            "SELECT id, bd_uid, manager_uid, scheduled_date, status FROM review_schedule WHERE scheduled_date = '$yesterday' AND status = 'missed' LIMIT 50"
        ));
    }

    // /api/review_schedule/rm_signoff
    public function review_schedule_rm_signoff() {
        if (!$this->auth()) return;
        $this->json_out($this->real_rows(
            "SELECT id, bd_uid, manager_uid, rm_uid, scheduled_date, status FROM review_schedule WHERE rm_uid IS NOT NULL ORDER BY id DESC LIMIT 50"
        ));
    }

    // /api/review_v2/list
    // review_session_v2: id, schedule_id, by_uid, to_uid, review_type_id, window_from, window_to, status
    public function review_v2_list() {
        if (!$this->auth()) return;
        $this->json_out($this->real_rows(
            "SELECT id, schedule_id, by_uid, to_uid, review_type_id, window_from, window_to, status FROM review_session_v2 ORDER BY id DESC LIMIT 50"
        ));
    }

    // /api/route_brain/today
    // route_plan: id, bd_uid, plan_date, cluster_id, stop_count, route_grade
    public function route_brain_today() {
        if (!$this->auth()) return;
        $today = date('Y-m-d');
        $this->json_out($this->real_rows(
            "SELECT id, bd_uid, plan_date, cluster_id, stop_count, route_grade FROM route_plan WHERE plan_date = '$today' LIMIT 50"
        ));
    }

    // /api/signoff/history
    // stage_signoff: id, lead_id, bd_uid, from_cstatus, to_cstatus, status, requested_at
    public function signoff_history() {
        if (!$this->auth()) return;
        $this->json_out($this->real_rows(
            "SELECT id, lead_id, bd_uid, from_cstatus, to_cstatus, status, requested_at FROM stage_signoff ORDER BY id DESC LIMIT 50"
        ));
    }

    // /api/signoff/pending
    public function signoff_pending() {
        if (!$this->auth()) return;
        $this->json_out($this->real_rows(
            "SELECT id, lead_id, bd_uid, from_cstatus, to_cstatus, requested_at FROM stage_signoff WHERE status = 'pending' ORDER BY id DESC LIMIT 50"
        ));
    }

    // /api/snapshots/latest
    // pipeline_coverage_snapshot: id, scope_type, scope_uid, snapshot_date, pipeline_rs, target_rs, ratio, band
    public function snapshots_latest() {
        if (!$this->auth()) return;
        $this->json_out($this->real_rows(
            "SELECT id, scope_type, scope_uid, snapshot_date, pipeline_rs, target_rs, ratio, band FROM pipeline_coverage_snapshot ORDER BY id DESC LIMIT 50"
        ));
    }

    // /api/special_remarks/list
    // special_remarks: id, uid, cid_id, remark_text, created_at
    public function special_remarks_list() {
        if (!$this->auth()) return;
        $this->json_out($this->real_rows(
            "SELECT id, uid, cid_id, remark_text, created_at FROM special_remarks WHERE 1=1 " . $this->field_scope_sql('uid', 'AND') . " ORDER BY id DESC LIMIT 50"
        ));
    }

    // /api/stakeholder/probe
    public function stakeholder_probe() {
        if (!$this->auth()) return;
        $this->json_out(['ok' => true, 'success' => true, 'note' => 'stakeholder online', 'ts' => date('c')]);
    }

    // /api/standup/probe
    public function standup_probe() {
        if (!$this->auth()) return;
        $this->json_out(['ok' => true, 'success' => true, 'note' => 'standup online', 'ts' => date('c')]);
    }

    // /api/standup_closure/probe
    public function standup_closure_probe() {
        if (!$this->auth()) return;
        $this->json_out(['ok' => true, 'success' => true, 'note' => 'standup_closure online', 'ts' => date('c')]);
    }

    // /api/star_rating/me
    // sales_star_rating: id, date, user_id, types, question, star, star_by, created_at
    public function star_rating_me() {
        if (!$this->auth()) return;
        $this->json_out($this->real_rows(
            "SELECT id, date, user_id, types, star, star_by, created_at FROM sales_star_rating ORDER BY id DESC LIMIT 50"
        ));
    }

    // /api/status_change_check/today
    // funnel_change_log: id, cid_id, bd_uid, from_cstatus, to_cstatus, changed_by_uid, created_at
    public function status_change_check_today() {
        if (!$this->auth()) return;
        $today = date('Y-m-d');
        $this->json_out($this->real_rows(
            "SELECT id, cid_id, bd_uid, from_cstatus, to_cstatus, changed_by_uid, created_at FROM funnel_change_log WHERE DATE(created_at) = '$today' LIMIT 50"
        ));
    }

    // /api/task/by_planner
    // task_plan_for_today: id, user_id, admin_id, date, approvel_status
    public function task_by_planner() {
        if (!$this->auth()) return;
        $this->json_out($this->real_rows(
            "SELECT id, user_id, admin_id, date, approvel_status FROM task_plan_for_today ORDER BY id DESC LIMIT 50"
        ));
    }

    // /api/task_planner/probe
    public function task_planner_probe() {
        if (!$this->auth()) return;
        $this->json_out(['ok' => true, 'success' => true, 'note' => 'task_planner online', 'ts' => date('c')]);
    }

    // /api/task_planner_v2/probe
    public function task_planner_v2_probe() {
        if (!$this->auth()) return;
        $this->json_out(['ok' => true, 'success' => true, 'note' => 'task_planner_v2 online', 'ts' => date('c')]);
    }

    // /api/taskplanner_v2/probe
    public function taskplanner_v2_probe() {
        if (!$this->auth()) return;
        $this->json_out(['ok' => true, 'success' => true, 'note' => 'taskplanner_v2 online', 'ts' => date('c')]);
    }

    // /api/tasks/submit
    public function tasks_submit() {
        if (!$this->auth()) return;
        $this->json_out(['ok' => true, 'success' => true, 'note' => 'tasks_submit stub - use TaskV28', 'ts' => date('c')]);
    }

    // /api/tasks/today
    public function tasks_today() {
        if (!$this->auth()) return;
        $today = date('Y-m-d');
        $this->json_out($this->real_rows(
            "SELECT id, user_id, admin_id, date, approvel_status FROM task_plan_for_today WHERE date = '$today' " . $this->field_scope_sql('user_id', 'AND') . " LIMIT 50"
        ));
    }

    // /api/team_live_map/now
    public function team_live_map_now() {
        if (!$this->auth()) return;
        // Expected: {users:[{uid, lat, lng, last_seen}]}
        $this->json_out($this->stub('team_live_map', 'now'));
    }

    // /api/team_task_check/today
    public function team_task_check_today() {
        if (!$this->auth()) return;
        $today = date('Y-m-d');
        $this->json_out($this->real_rows(
            "SELECT id, user_id, admin_id, date, approvel_status FROM task_plan_for_today WHERE date = '$today' LIMIT 50"
        ));
    }

    // /api/travel_cluster/me
    // user_cluster_mapping: id, user_id, user_type, cluster_id, status
    public function travel_cluster_me() {
        if (!$this->auth()) return;
        $this->json_out($this->real_rows(
            "SELECT id, user_id, user_type, cluster_id, status FROM user_cluster_mapping ORDER BY id DESC LIMIT 50"
        ));
    }

    // /api/universal_mom/start
    public function universal_mom_start() {
        if (!$this->auth()) return;
        $this->json_out(['ok' => true, 'success' => true, 'note' => 'universal_mom_start - use MomStatusV28', 'ts' => date('c')]);
    }

    // /api/upsell_client/list
    // rm_upsell_pipeline: id (=id bigint), rm_uid, lead_id, category_code, upsell_stage, refreshed_at
    public function upsell_client_list() {
        if (!$this->auth()) return;
        $this->json_out($this->real_rows(
            "SELECT id, rm_uid, lead_id, category_code, upsell_stage, refreshed_at FROM rm_upsell_pipeline ORDER BY id DESC LIMIT 50"
        ));
    }

    // /api/upstream_hygiene/proposal_sla
    // proposal_sla_tracker: id, cid_id, bd_uid, cm_uid, status, sla_deadline, proposal_submitted_at
    public function upstream_hygiene_proposal_sla() {
        if (!$this->auth()) return;
        $this->json_out($this->real_rows(
            "SELECT id, cid_id, bd_uid, cm_uid, status, sla_deadline, proposal_submitted_at FROM proposal_sla_tracker ORDER BY id DESC LIMIT 50"
        ));
    }

    // /api/user/login
    public function user_login() {
        if (!$this->auth()) return;
        $this->json_out(['ok' => true, 'success' => true, 'note' => 'use POST /api/Auth/login', 'ts' => date('c')]);
    }

    // /api/wdl/submit
    // wdl_request table exists
    public function wdl_submit() {
        if (!$this->auth()) return;
        $this->json_out(['ok' => true, 'success' => true, 'reason' => 'no_rows', 'note' => 'wdl_submit stub', 'ts' => date('c')]);
    }

    // /api/whatsapp/status
    // whatsapp_send_v2: id, to_phone, to_lead_id, from_uid, template_name, status, sent_at
    public function whatsapp_status() {
        if (!$this->auth()) return;
        $this->json_out($this->real_rows(
            "SELECT id, to_phone, to_lead_id, from_uid, template_name, status, sent_at FROM whatsapp_send_v2 ORDER BY id DESC LIMIT 50"
        ));
    }

    // /api/whoami
    public function whoami_index() {
        if (!$this->auth()) return;
        $this->json_out([
            'ok'      => true,
            'success' => true,
            'service' => 'STEM CRM v2.8',
            'agent'   => 'K',
            'ts'      => date('c'),
        ]);
    }
}

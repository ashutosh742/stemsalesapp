<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * CompanyDetail_api  --  GET /api/company/detail?cid=<cid>
 *
 * Production-mirror of Menu::CompanyDetails($cid).
 * Calls the SAME Menu_model methods; returns one JSON object with 16 keys.
 *
 * Auth  : Bearer <STEM_DIGEST_TOKEN>  (master)  OR per-user JWT
 *         sha1(SECRET|uid|YYYY-MM-DD)  (same as MobileExtrasController).
 * Scoping: BD (type_id 3) sees only companies where init_call.mainbd = uid.
 *          All other roles (Admin/CM/PST/RM/etc.) see any cid the model returns.
 *
 * Routes (add to application/config/routes_parity.php):
 *   $route['api/company/detail'] = 'CompanyDetail_api/detail';
 *   $route['api/company/probe']  = 'CompanyDetail_api/probe';
 *
 * File  : application/controllers/CompanyDetail_api.php
 * Backed: application/controllers/CompanyDetail_api.php.bak.20260606
 * Built : 2026-06-06 -- STAGING ONLY, never touches production stemapp.in
 */
class CompanyDetail_api extends CI_Controller {

    private $_known_token = '4eBaiAT7r4zu6OK3b8evjLNia1D7RGgb0qRTuLJfUSo';
    private $_authed_uid  = 0;
    private $_authed_type = 0; // user type_id resolved after auth

    // ------------------------------------------------------------------
    public function __construct() {
        parent::__construct();
        $this->load->database();
        $this->load->model('Menu_model');
        header('Content-Type: application/json; charset=utf-8');
    }

    // ------------------------------------------------------------------
    // JSON helpers
    // ------------------------------------------------------------------
    private function _ok($data) {
        http_response_code(200);
        echo json_encode(['ok' => true, 'data' => $data], JSON_UNESCAPED_UNICODE);
        exit;
    }
    private function _err($msg, $code = 400) {
        http_response_code($code);
        echo json_encode(['ok' => false, 'error' => $msg], JSON_UNESCAPED_UNICODE);
        exit;
    }
    private function _no_rows() {
        return ['rows' => [], 'reason' => 'no_rows'];
    }
    private function _rows($arr) {
        if (empty($arr)) return $this->_no_rows();
        // CI3 query result() returns objects; cast each to assoc array for JSON
        $out = [];
        foreach ($arr as $row) {
            $out[] = (array)$row;
        }
        return ['rows' => $out];
    }

    // ------------------------------------------------------------------
    // JWT / master-token auth  (mirrors MobileExtrasController exactly)
    // ------------------------------------------------------------------
    private function _jwt_token_valid($token) {
        if (empty($token)) return false;
        $secret = getenv('STEM_DIGEST_TOKEN') ?: $this->_known_token;
        $days   = [date('Y-m-d'), date('Y-m-d', strtotime('-1 day')), date('Y-m-d', strtotime('+1 day'))];

        // Fast path: uid from request params
        $candidates = [];
        foreach (['uid', 'cm_uid', 'rm_uid', 'bd_uid', 'acm_uid', 'user_id'] as $k) {
            if (isset($_GET[$k])  && (int)$_GET[$k]  > 0) $candidates[(int)$_GET[$k]]  = 1;
            if (isset($_POST[$k]) && (int)$_POST[$k] > 0) $candidates[(int)$_POST[$k]] = 1;
        }
        foreach (array_keys($candidates) as $uid) {
            foreach ($days as $d) {
                if (hash_equals(sha1($secret . '|' . $uid . '|' . $d), $token)) return (int)$uid;
            }
        }

        // Fallback: scan all active uids
        static $all_uids = null;
        if ($all_uids === null) {
            $rows     = $this->db->select("uid")->from("user")->get()->result();
            $all_uids = [];
            foreach ($rows as $r) $all_uids[] = (int)$r->uid;
        }
        foreach ($all_uids as $uid) {
            foreach ($days as $d) {
                if (hash_equals(sha1($secret . '|' . $uid . '|' . $d), $token)) return (int)$uid;
            }
        }
        return false;
    }

    private function _require_auth() {
        $hdr = isset($_SERVER['HTTP_AUTHORIZATION']) ? $_SERVER['HTTP_AUTHORIZATION'] : '';
        if (!$hdr && function_exists('apache_request_headers')) {
            $ah = apache_request_headers();
            if (isset($ah['Authorization']))       $hdr = $ah['Authorization'];
            elseif (isset($ah['authorization']))   $hdr = $ah['authorization'];
        }
        if (empty($hdr)) $this->_err('Authorization header missing.', 401);

        $secret  = getenv('STEM_DIGEST_TOKEN') ?: $this->_known_token;
        $master  = 'Bearer ' . $secret;

        // Master token check
        if (hash_equals($master, trim($hdr))) {
            $this->_authed_uid  = 0;    // 0 = master / admin context
            $this->_authed_type = 1;    // treat as SuperAdmin
            return;
        }

        // Per-user JWT
        if (stripos($hdr, 'Bearer ') === 0) {
            $tok = trim(substr($hdr, 7));
            $uid = $this->_jwt_token_valid($tok);
            if ($uid) {
                $this->_authed_uid = $uid;
                // Resolve type_id from DB
                $ur = $this->db->select('type_id')->from('user')
                               ->where('uid', $uid)->limit(1)->get()->row();
                $this->_authed_type = $ur ? (int)$ur->type_id : 3;
                return;
            }
        }

        // Session fallback (web callers)
        $session_uid = $this->session->userdata('user_id');
        if ((int)$session_uid > 0) {
            $this->_authed_uid  = (int)$session_uid;
            $this->_authed_type = 1;
            return;
        }

        $this->_err('Invalid or missing bearer token.', 401);
    }

    // ------------------------------------------------------------------
    // BD visibility scope check
    // A BD (type_id 3) may only view a cid if init_call.mainbd = their uid.
    // Admins / CM / PST / RM / all other roles: no restriction beyond model.
    // ------------------------------------------------------------------
    private function _bd_may_view($ids) {
        // rimlyproof_acmscope_20260609: BD (type_id 3) and ACM (type_id 24) are FIELD users that
        // may only view companies in their own funnel. Field users are scoped by their real
        // ownership column: BD -> init_call.mainbd; ACM -> init_call.acm_co_id. All other roles
        // (Admin/CM/PST/RM/SuperAdmin/system) are unrestricted beyond what the model returns.
        $type = (int)$this->_authed_type;
        $uid  = (int)$this->_authed_uid;
        if ($uid <= 0) return true;                 // master / admin context
        if ($type === 3) {                          // BD
            $r = $this->db->query(
                "SELECT 1 FROM init_call WHERE (cmpid_id = ? OR id = ?) AND mainbd = ? LIMIT 1",
                [(int)$ids['cm_id'], (int)$ids['ic_id'], $uid]
            )->row();
            return !empty($r);
        }
        if ($type === 24) {                         // ACM
            $r = $this->db->query(
                "SELECT 1 FROM init_call WHERE (cmpid_id = ? OR id = ?) AND acm_co_id = ? LIMIT 1",
                [(int)$ids['cm_id'], (int)$ids['ic_id'], $uid]
            )->row();
            return !empty($r);
        }
        return true;                                // managers / system: unrestricted
    }

    // ------------------------------------------------------------------
    // ID NORMALIZATION  (root-cause fix for the 404 bug)
    //
    // Production overloads $cid: some listing pages link with
    //   cid = company_master.id   (init_call.cmpid_id)   -> GetCompanyInfo etc.
    //   cid = init_call.id        (tblcallevents.cid_id) -> task / conversion methods
    // The incoming value may be EITHER. We resolve BOTH ids here so each
    // model method receives the id semantics it actually filters on.
    //
    // Returns ['cm_id'=>company_master.id, 'ic_id'=>init_call.id] or null if
    // the value matches neither.
    // ------------------------------------------------------------------
    private function _resolve_ids($cid) {
        $cid = (int)$cid;
        if ($cid <= 0) return null;

        // Case A: value is an init_call.id
        $r = $this->db->query(
            "SELECT id AS ic_id, cmpid_id AS cm_id FROM init_call WHERE id = ? LIMIT 1",
            [$cid]
        )->row();
        if ($r && (int)$r->cm_id > 0) {
            return ['cm_id' => (int)$r->cm_id, 'ic_id' => (int)$r->ic_id];
        }

        // Case B: value is a company_master.id (cmpid_id). Pick the latest
        // init_call row for that company (mirrors how production lists resolve).
        $r = $this->db->query(
            "SELECT id AS ic_id, cmpid_id AS cm_id FROM init_call WHERE cmpid_id = ? ORDER BY id DESC LIMIT 1",
            [$cid]
        )->row();
        if ($r) {
            return ['cm_id' => (int)$r->cm_id, 'ic_id' => (int)$r->ic_id];
        }

        // Case C: company_master row with no init_call yet -> still valid company
        $r = $this->db->query(
            "SELECT id FROM company_master WHERE id = ? LIMIT 1",
            [$cid]
        )->row();
        if ($r) {
            return ['cm_id' => (int)$r->id, 'ic_id' => 0];
        }

        return null;
    }

    // ------------------------------------------------------------------
    // GET /api/company/probe
    // ------------------------------------------------------------------
    public function probe() {
        $this->_require_auth();
        $this->_ok([
            'endpoint'  => 'CompanyDetail_api',
            'version'   => '1.0.0',
            'date'      => date('Y-m-d'),
            'auth_uid'  => $this->_authed_uid,
            'auth_type' => $this->_authed_type,
        ]);
    }

    // ------------------------------------------------------------------
    // GET /api/company/detail?cid=<cid>
    // ------------------------------------------------------------------
    public function detail() {
        $this->_require_auth();

        $cid = isset($_GET['cid']) ? (int)$_GET['cid'] : 0;
        if ($cid <= 0) $this->_err('cid param required and must be a positive integer.');

        // Normalize the overloaded cid into BOTH id semantics (root-cause fix).
        $ids = $this->_resolve_ids($cid);
        if ($ids === null) {
            $this->_err('Company not found for cid=' . $cid, 404);
        }
        $cm_id = $ids['cm_id'];  // company_master.id  (init_call.cmpid_id)
        $ic_id = $ids['ic_id'];  // init_call.id       (tblcallevents.cid_id)

        // Visibility gate for BD role
        if (!$this->_bd_may_view($ids)) {
            $this->_err('Access denied: this company is not in your funnel.', 403);
        }

        // ------- Reproduce production Menu::CompanyDetails logic exactly -------
        // VERIFIED against Menu_model: ALL nine methods filter on
        // init_call.cmpid_id (= company_master.id), so every method receives cm_id.
        // (The earlier assumption that task methods key on tblcallevents.cid_id
        //  was wrong: they JOIN init_call.id = tbcl.cid_id but WHERE on cmpid_id.)
        // contacts -> company_contact_master.company_id = company_master.id.
        // funnel_transfer_log keyed on init_call.id -> uses ic_id.
        $curFY               = $this->Menu_model->getFinancialYearRange();
        $start_financial_date = $curFY['start_date'];
        $targetCurDate       = date('Y-m-d');

        $cmpDatas            = $this->Menu_model->GetCompanyInfo($cm_id);
        $cmpContactsDatas    = $this->Menu_model->getCompanyContactBYCID($cm_id);
        $cmpTasksDatas       = $this->Menu_model->GetTaskOnCompanyByCID($cm_id);
        $cmpReviewsDatas     = $this->Menu_model->GetAllReviewDatasOnCompanyByCID($cm_id);
        $cmpPositiveTaskDatas= $this->Menu_model->GetTaskConversionByCID($cm_id, 'positive_conversions');
        $cmpNegativeTaskDatas= $this->Menu_model->GetTaskConversionByCID($cm_id, 'negative_conversions');
        $cmpOtherTaskDatas   = $this->Menu_model->GetTaskConversionByCID($cm_id, 'other_conversions');
        $bdconversionsAlltimes = $this->Menu_model->GetTodaysConversionDatasAlltimes($cm_id);
        $bdconversionsCFyear   = $this->Menu_model->GetTodaysConversionDatasCFyear($cm_id, $start_financial_date, $targetCurDate);

        // Old main BD (previous owner) - funnel_transfer_log keyed on init_call.id
        $oldBDs         = $ic_id ? $this->Menu_model->GetOldMainBD($ic_id) : [];
        $old_user_name  = (!empty($oldBDs)) ? $oldBDs[0]->old_user : '';

        // Guard: company must exist
        if (empty($cmpDatas)) {
            $this->_err('Company not found or no data for cid=' . $cid . ' (cm_id=' . $cm_id . ', ic_id=' . $ic_id . ')', 404);
        }

        $cmpData = (array)$cmpDatas[0];

        // ---- Task / status summary aggregation (mirrors view PHP) ----
        $task_counts           = [];
        $taskTimeStatusDatas   = [];
        foreach ($cmpTasksDatas as $task) {
            $tn = $task->task_name;
            $ts = $task->task_time_status;
            $task_counts[$tn]         = isset($task_counts[$tn])         ? $task_counts[$tn] + 1         : 1;
            $taskTimeStatusDatas[$ts] = isset($taskTimeStatusDatas[$ts]) ? $taskTimeStatusDatas[$ts] + 1 : 1;
        }

        // ---- Completed vs Pending split (mirror production view logic EXACTLY) ----
        // VERIFIED against Functions/CompanyDetails.php:
        //   Completed All Time : nextCFID != 0          (view: `if(nextCFID==0) continue;`)
        //   Completed CFY      : nextCFID != 0  AND appointment-date within current FY
        //   Pending All Time   : nextCFID == 0          (view: `if(nextCFID!=0) continue;`)
        // (filter_by is a JSON UI-filter blob, NOT a completed/pending flag.)
        $fy_start = $start_financial_date;            // e.g. 2026-04-01
        $fy_end   = $curFY['end_date'];               // FY end, not today

        $completed_cfy     = [];
        $completed_alltime = [];
        $pending_alltime   = [];

        foreach ($cmpTasksDatas as $task) {
            $ncf = (int)$task->nextCFID;
            if ($ncf != 0) {
                // Completed
                $completed_alltime[] = $task;
                $appt = (string)$task->appointmentdatetime;
                if ($appt !== '' && $appt !== '0000-00-00 00:00:00') {
                    $appt_date = date('Y-m-d', strtotime($appt));
                    if ($appt_date >= $fy_start && $appt_date <= $fy_end) {
                        $completed_cfy[] = $task;
                    }
                }
            } else {
                // Pending
                $pending_alltime[] = $task;
            }
        }

        // Build response keys matching spec
        $response = [
            'cid'                     => $cid,           // echo back what caller sent
            'company_master_id'       => $cm_id,         // canonical company id
            'init_call_id'            => $ic_id,         // canonical funnel/task id
            'compname'                => $cmpData['compname'] ?? '',
            'fy_range'                => ['start' => $fy_start, 'end' => $fy_end],
            'old_main_bd'             => $old_user_name,

            // Section 1 - Company Overview fields
            'overview'                => $cmpData,

            // Section 2 - Team Members (subset of overview)
            'team'                    => [
                'mainbd_name'          => $cmpData['mainbd_name']       ?? '',
                'cluster_manager_name' => $cmpData['cluster_manager_name'] ?? '',
                'pst_name'             => $cmpData['pst_name']          ?? '',
                'ash_nae_co_id_name'   => $cmpData['ash_nae_co_id_name'] ?? '',
                'ash_w_co_id_name'     => $cmpData['ash_w_co_id_name']  ?? '',
                'ash_s_co_id_name'     => $cmpData['ash_s_co_id_name']  ?? '',
                'rm_east_co_id_name'   => $cmpData['rm_east_co_id_name'] ?? '',
                'rm_north_co_id_name'  => $cmpData['rm_north_co_id_name'] ?? '',
                'acm_co_id_name'       => $cmpData['acm_co_id_name']   ?? '',
                'inside_sales_name'    => $cmpData['inside_sales_name'] ?? '',
                'old_user_name'        => $old_user_name,
                'user_cluster_zone'    => $cmpData['user_cluster_zone'] ?? '',
                'company_creater_name' => $cmpData['company_creater_name'] ?? '',
            ],

            // Section 3 - Classification
            'classification'          => [
                'topspender'        => $cmpData['topspender']      ?? '',
                'upsell_client'     => $cmpData['upsell_client']   ?? '',
                'focus_funnel'      => $cmpData['focus_funnel']    ?? '',
                'anchor_clients'    => $cmpData['anchor_clients']  ?? '',
                'keycompany'        => $cmpData['keycompany']      ?? '',
                'pkclient'          => $cmpData['pkclient']        ?? '',
                'priorityc'         => $cmpData['priorityc']       ?? '',
                'potential'         => $cmpData['potential']       ?? '',
                'in_quarter'        => $cmpData['in_quarter']      ?? '',
            ],

            // Section 4 - Funnel Insights
            'funnel'                  => [
                'q1_twetenty_closure_funnel' => $cmpData['q1_twetenty_closure_funnel'] ?? '',
                'potential_funnel_for_fy'    => $cmpData['potential_funnel_for_fy']    ?? '',
                'to_be_nurtured_for_fy'      => $cmpData['to_be_nurtured_for_fy']      ?? '',
                'fifity_new_lead_funnel'     => $cmpData['fifity_new_lead_funnel']      ?? '',
            ],

            // Section 7 - Contacts
            'contacts'                => $this->_rows($cmpContactsDatas),

            // Section 8 - Task Activity Summary (aggregated counts)
            'task_activity_summary'   => !empty($task_counts)
                ? ['rows' => $task_counts]
                : $this->_no_rows(),

            // Section 9 - Status Activity Summary
            'status_activity_summary' => !empty($taskTimeStatusDatas)
                ? ['rows' => $taskTimeStatusDatas]
                : $this->_no_rows(),

            // Section 10 - Next-Step CFY
            'nextstep_cfy'            => $this->_rows($bdconversionsCFyear),

            // Section 10 - Next-Step All Time
            'nextstep_alltime'        => $this->_rows($bdconversionsAlltimes),

            // Section 11 - Completed CFY
            'completed_cfy'           => $this->_rows($completed_cfy),

            // Section 11 - Completed All Time
            'completed_alltime'       => $this->_rows($completed_alltime),

            // Section 12 - Pending All Time
            'pending_alltime'         => $this->_rows($pending_alltime),

            // Section 13 - Conversions
            'positive'                => $this->_rows($cmpPositiveTaskDatas),
            'negative'                => $this->_rows($cmpNegativeTaskDatas),
            'other'                   => $this->_rows($cmpOtherTaskDatas),

            // Section 14 - Completed Under Review
            'under_review'            => $this->_rows($cmpReviewsDatas),
        ];

        $this->_ok($response);
    }
}

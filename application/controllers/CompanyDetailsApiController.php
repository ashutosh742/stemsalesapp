<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * CompanyDetailsApiController
 *
 * Migration 075 — Agent C: CompanyDetails Mirror
 * Migration 076 — Agent P: 6 new granular endpoints
 *
 * Mirrors the production Menu/CompanyDetails/<cid> page by calling the exact
 * same 9 Menu_model functions, exposed as JWT-authenticated JSON endpoints
 * for the mobile app.
 *
 * Auth: Bearer STEM_DIGEST_TOKEN header required on all endpoints except probe.
 *
 * Routes to add in application/config/routes_mobile_pilot.php:
 *   // === migration 075 CompanyDetails mirror ===
 *   $route['api/company_details/probe'] = 'CompanyDetailsApiController/probe';
 *   $route['api/company_details/get/(:num)'] = 'CompanyDetailsApiController/get/$1';
 *   // === migration 076 granular endpoints ===
 *   $route['api/company_details/profile/(:num)']          = 'CompanyDetailsApiController/profile/$1';
 *   $route['api/company_details/tasks/(:num)']            = 'CompanyDetailsApiController/tasks/$1';
 *   $route['api/company_details/tasks_fy/(:num)']         = 'CompanyDetailsApiController/tasks_fy/$1';
 *   $route['api/company_details/special_remarks/(:num)']  = 'CompanyDetailsApiController/special_remarks/$1';
 *   $route['api/company_details/conversions/(:num)']      = 'CompanyDetailsApiController/conversions/$1';
 *   $route['api/company_details/conversions_typed/(:num)']= 'CompanyDetailsApiController/conversions_typed/$1';
 */
class CompanyDetailsApiController extends CI_Controller
{
    const MIGRATION = '076';

    public function __construct()
    {
        parent::__construct();
        $this->load->database();
        $this->load->helper('url');
        $this->config->load('rest',   true, true);
        $this->config->load('custom', true, true);
        $this->load->model('Menu_model');
        header('Content-Type: application/json; charset=utf-8');
    }

    // -----------------------------------------------------------------------
    // Auth guard — Bearer token or active session (mirrors FunnelReportController)
    // -----------------------------------------------------------------------
    private $_authed_uid = 0;

    /**
     * Per-user JWT validator — matches Auth::api_login token generation.
     * Returns uid (int > 0) on success, false on failure.
     */
    private function _jwt_token_valid($token)
    {
        if (empty($token)) return false;
        $secret = getenv('STEM_DIGEST_TOKEN') ?: '4eBaiAT7r4zu6OK3b8evjLNia1D7RGgb0qRTuLJfUSo';
        $days = array(
            date('Y-m-d'),
            date('Y-m-d', strtotime('-1 day')),
            date('Y-m-d', strtotime('+1 day'))
        );
        // Fast path: try uid candidates from request params
        $candidates = array();
        foreach (array('uid', 'cm_uid', 'rm_uid', 'bd_uid', 'acm_uid', 'user_id') as $k) {
            if (isset($_GET[$k])  && (int)$_GET[$k]  > 0) $candidates[(int)$_GET[$k]]  = 1;
            if (isset($_POST[$k]) && (int)$_POST[$k] > 0) $candidates[(int)$_POST[$k]] = 1;
        }
        foreach (array_keys($candidates) as $uid) {
            foreach ($days as $d) {
                if (hash_equals(sha1($secret . '|' . $uid . '|' . $d), $token)) return (int)$uid;
            }
        }
        // Fallback: scan all active uids (cached for request lifetime)
        static $all_uids = null;
        if ($all_uids === null) {
            $rows = $this->db->select('uid')->from('user')->where('active', 1)->get()->result();
            $all_uids = array();
            foreach ($rows as $r) $all_uids[] = (int)$r->uid;
        }
        foreach ($all_uids as $uid) {
            foreach ($days as $d) {
                if (hash_equals(sha1($secret . '|' . $uid . '|' . $d), $token)) return (int)$uid;
            }
        }
        return false;
    }

    private function _auth_or_die()
    {
        // Read Authorization header — try multiple methods for Apache compatibility
        $hdr = $this->input->get_request_header('Authorization', true);
        if (empty($hdr) && function_exists('apache_request_headers')) {
            $hdrs = apache_request_headers();
            if (isset($hdrs['Authorization'])) {
                $hdr = $hdrs['Authorization'];
            } elseif (isset($hdrs['authorization'])) {
                $hdr = $hdrs['authorization'];
            }
        }
        if (empty($hdr) && isset($_SERVER['HTTP_AUTHORIZATION'])) {
            $hdr = $_SERVER['HTTP_AUTHORIZATION'];
        }

        // Resolve expected static token from multiple sources
        $expected = getenv('STEM_DIGEST_TOKEN');
        if (empty($expected)) $expected = $this->config->item('stem_digest_token');
        if (empty($expected)) $expected = $this->config->item('STEM_DIGEST_TOKEN');
        if (empty($expected)) $expected = '4eBaiAT7r4zu6OK3b8evjLNia1D7RGgb0qRTuLJfUSo';

        // Static bearer token check
        if (!empty($hdr) && $hdr === 'Bearer ' . $expected) {
            return true;
        }
        // Per-user JWT check
        if (!empty($hdr) && stripos($hdr, 'Bearer ') === 0) {
            $tok = trim(substr($hdr, 7));
            $uid = $this->_jwt_token_valid($tok);
            if ($uid) {
                $this->_authed_uid = $uid;
                return true;
            }
        }
        // Active web session also accepted
        $session_uid = $this->session->userdata('user_id');
        if ((int)$session_uid > 0) {
            $this->_authed_uid = (int)$session_uid;
            return true;
        }

        http_response_code(401);
        echo json_encode(array('ok' => false, 'error' => 'unauthorized', 'hdr_received' => !empty($hdr)));
        exit;
    }

    // -----------------------------------------------------------------------
    // FY helper — returns ['start_date' => ..., 'end_date' => ...]
    // Mirrors getFinancialYearRange() from Menu_model (no SQL, pure PHP)
    // -----------------------------------------------------------------------
    private function _fy_range()
    {
        $year  = (int)date('Y');
        $month = (int)date('n');
        if ($month < 4) {
            $start = ($year - 1) . '-04-01';
            $end   = $year       . '-03-31';
        } else {
            $start = $year       . '-04-01';
            $end   = ($year + 1) . '-03-31';
        }
        return array('start_date' => $start, 'end_date' => $end);
    }

    // -----------------------------------------------------------------------
    // Task row formatter — shared by tasks() and tasks_fy()
    // Maps raw tblcallevents row (object) to the 29-column spec.
    // -----------------------------------------------------------------------
    private function _format_task_row($row, $sr_no)
    {
        // Mom link: only for meeting action types 3, 4, 17, 22
        $meeting_actions = array(3, 4, 17, 22);
        $act_id = isset($row->actiontype_id) ? (int)$row->actiontype_id : 0;

        // Resolve mom_id via model helper; graceful null if unavailable
        $mom_id = null;
        try {
            if (in_array($act_id, $meeting_actions) && isset($row->task_id)) {
                $mom_task = $this->Menu_model->GetTBLMomTaskByTaskId($row->task_id);
                if (!empty($mom_task) && isset($mom_task[0]->id)) {
                    $mom_id = (int)$mom_task[0]->id;
                }
            }
        } catch (Exception $e) {
            $mom_id = null;
        }

        // Total star — graceful null
        $total_star = null;
        try {
            if (isset($row->task_id)) {
                $star_val = $this->Menu_model->GetTotalStarFoundAfterCheck($row->task_id);
                $total_star = ($star_val !== false && $star_val !== null) ? $star_val : null;
            }
        } catch (Exception $e) {
            $total_star = null;
        }

        // special_remarks presence check
        $has_special = (!empty($row->special_remarks) && $row->special_remarks !== 'null');

        // nextCFID: 0 = pending, non-zero = complete
        $next_cf = isset($row->nextCFID) ? (int)$row->nextCFID : 0;
        $task_status = ($next_cf != 0) ? 'complete' : 'pending';

        // cid from the row (init_call.cmpid_id reflected via join)
        $cid_val = isset($row->cid_id) ? $row->cid_id : null;

        return array(
            'sr_no'                  => $sr_no,
            'username'               => isset($row->task_username)        ? $row->task_username        : null,
            'cid'                    => $cid_val,
            'company'                => isset($row->compname)             ? $row->compname             : null,
            'current_status'         => isset($row->current_status)       ? $row->current_status       : null,
            'task_name'              => isset($row->task_name)            ? $row->task_name            : null,
            'task_status'            => $task_status,
            'action'                 => isset($row->actontaken)           ? $row->actontaken           : null,
            'purpose'                => isset($row->purpose_achieved)     ? $row->purpose_achieved     : null,
            'planned_on_status'      => isset($row->task_time_status)     ? $row->task_time_status     : null,
            'change_on_status'       => isset($row->task_time_new_status) ? $row->task_time_new_status : null,
            'original_date'          => isset($row->fwd_date)            ? $row->fwd_date             : null,
            'appointment_datetime'   => isset($row->appointmentdatetime)  ? $row->appointmentdatetime  : null,
            'initiated_dt'           => isset($row->initiateddt)          ? $row->initiateddt          : null,
            'updated_dt'             => isset($row->updated_at)           ? $row->updated_at           : null,
            'time_taken'             => isset($row->total_time_taken)     ? $row->total_time_taken     : null,
            'late_remarks'           => isset($row->late_remarks_message) ? $row->late_remarks_message : null,
            'approved_by'            => isset($row->task_approved_by)     ? $row->task_approved_by     : null,
            'remarks'                => isset($row->remarks)              ? $row->remarks              : null,
            'has_special_remarks'    => $has_special,
            'special_remarks_task_id'=> ($has_special && isset($row->task_id)) ? (int)$row->task_id : null,
            'next_step_confirmation' => isset($row->next_step_confirmation) ? $row->next_step_confirmation : null,
            'closing_timeline'       => isset($row->closing_timeline)    ? $row->closing_timeline     : null,
            'proposal_required'      => isset($row->proposal_require)    ? $row->proposal_require     : null,
            'deleted_flag'           => isset($row->delete_request)      ? $row->delete_request       : null,
            'replan_count'           => isset($row->plan_count)          ? (int)$row->plan_count      : 0,
            'meeting_type'           => isset($row->mtype)               ? $row->mtype                : null,
            'mom_link'               => ($mom_id !== null && $cid_val !== null)
                                            ? array('mom_id' => $mom_id, 'cid' => $cid_val)
                                            : null,
            'view_details_link'      => isset($row->task_id)            ? (int)$row->task_id         : null,
            'total_star'             => $total_star,
        );
    }

    // -----------------------------------------------------------------------
    // GET /api/company_details/probe
    // Health-check — no auth required
    // -----------------------------------------------------------------------
    public function probe()
    {
        echo json_encode(array(
            'ok'         => true,
            'controller' => 'CompanyDetailsApiController',
            'migration'  => self::MIGRATION,
            'ts'         => date('Y-m-d H:i:s'),
        ));
    }

    // -----------------------------------------------------------------------
    // GET /api/company_details/get/<cid>
    // Main endpoint: mirrors production Menu/CompanyDetails/<cid>
    // Calls all 9 Menu_model functions and returns structured JSON.
    // Each section is wrapped in try/catch so one failure doesn't kill the
    // whole response.
    // -----------------------------------------------------------------------
    public function get($cid = 0)
    {
        $this->_auth_or_die();

        $cid = (int)$cid;
        if ($cid <= 0) {
            http_response_code(400);
            echo json_encode(array('ok' => false, 'error' => 'cid must be a positive integer'));
            return;
        }

        // ---- 8: getFinancialYearRange ----------------------------------------
        $fy_start = null;
        $fy_end   = null;
        $fy_raw   = null;
        try {
            $fy_raw   = $this->Menu_model->getFinancialYearRange();
            $fy_start = isset($fy_raw['start_date']) ? $fy_raw['start_date'] : null;
            $fy_end   = isset($fy_raw['end_date'])   ? $fy_raw['end_date']   : null;
        } catch (Exception $e) {
            $fy_raw = array('error' => 'function_unavailable', 'msg' => $e->getMessage());
        }

        // ---- 1: GetCompanyInfo ----------------------------------------------
        $header_raw = null;
        $header_err = null;
        try {
            $header_raw = $this->Menu_model->GetCompanyInfo($cid);
        } catch (Exception $e) {
            $header_err = array('error' => 'function_unavailable', 'msg' => $e->getMessage());
        }

        // If company not found, return 404 early
        if ($header_err === null && (empty($header_raw) || !is_array($header_raw) || count($header_raw) === 0)) {
            http_response_code(404);
            echo json_encode(array('ok' => false, 'error' => 'cid_not_found', 'cid' => $cid));
            return;
        }

        $header = ($header_err !== null) ? $header_err : (isset($header_raw[0]) ? $header_raw[0] : $header_raw);

        // Extract ownership fields from header for convenience
        $ownership = null;
        $flags     = null;
        if (is_object($header)) {
            $ownership = array(
                'mainbd'          => isset($header->mainbd_name)           ? $header->mainbd_name          : null,
                'cluster_manager' => isset($header->cluster_manager_name)  ? $header->cluster_manager_name : null,
                'cluster'         => isset($header->clustername)           ? $header->clustername          : null,
                'partner'         => isset($header->partner_name)          ? $header->partner_name         : null,
                'inside_sales'    => isset($header->inside_sales_name)     ? $header->inside_sales_name    : null,
                'acm'             => isset($header->acm_co_id_name)        ? $header->acm_co_id_name       : null,
                'pst'             => isset($header->pst_name)              ? $header->pst_name             : null,
            );
            $flags = array(
                'topspender'          => isset($header->topspender)              ? $header->topspender              : null,
                'upsell_client'       => isset($header->upsell_client)           ? $header->upsell_client           : null,
                'focus_funnel'        => isset($header->focus_funnel)            ? $header->focus_funnel            : null,
                'anchor_clients'      => isset($header->anchor_clients)          ? $header->anchor_clients          : null,
                'in_quarter'          => isset($header->in_quarter)              ? $header->in_quarter              : null,
                'keycompany'          => isset($header->keycompany)              ? $header->keycompany              : null,
                'pkclient'            => isset($header->pkclient)                ? $header->pkclient                : null,
                'priorityc'           => isset($header->priorityc)              ? $header->priorityc               : null,
                'new_lead'            => isset($header->create_after_task)       ? $header->create_after_task       : null,
                'fifity_new_lead'     => isset($header->fifity_new_lead_funnel)  ? $header->fifity_new_lead_funnel  : null,
                'need_to_be_monitored'=> isset($header->need_to_be_monitored)    ? $header->need_to_be_monitored    : null,
            );
        }

        // ---- 2: getCompanyContactBYCID --------------------------------------
        $contacts = null;
        try {
            $contacts = $this->Menu_model->getCompanyContactBYCID($cid);
        } catch (Exception $e) {
            $contacts = array('error' => 'function_unavailable', 'msg' => $e->getMessage());
        }

        // ---- 3: GetTaskOnCompanyByCID ---------------------------------------
        $activities = null;
        try {
            $activities = $this->Menu_model->GetTaskOnCompanyByCID($cid);
        } catch (Exception $e) {
            $activities = array('error' => 'function_unavailable', 'msg' => $e->getMessage());
        }

        // ---- 4: GetAllReviewDatasOnCompanyByCID ----------------------------
        $reviews = null;
        try {
            $reviews = $this->Menu_model->GetAllReviewDatasOnCompanyByCID($cid);
        } catch (Exception $e) {
            $reviews = array('error' => 'function_unavailable', 'msg' => $e->getMessage());
        }

        // ---- 5: GetTaskConversionByCID (positive, negative, other) ---------
        $conv_positive = null;
        try {
            $conv_positive = $this->Menu_model->GetTaskConversionByCID($cid, 'positive_conversions');
        } catch (Exception $e) {
            $conv_positive = array('error' => 'function_unavailable', 'msg' => $e->getMessage());
        }

        $conv_negative = null;
        try {
            $conv_negative = $this->Menu_model->GetTaskConversionByCID($cid, 'negative_conversions');
        } catch (Exception $e) {
            $conv_negative = array('error' => 'function_unavailable', 'msg' => $e->getMessage());
        }

        $conv_other = null;
        try {
            $conv_other = $this->Menu_model->GetTaskConversionByCID($cid, 'other_conversions');
        } catch (Exception $e) {
            $conv_other = array('error' => 'function_unavailable', 'msg' => $e->getMessage());
        }

        $conversions = array(
            'positive' => $conv_positive,
            'negative' => $conv_negative,
            'other'    => $conv_other,
        );

        // ---- 6: GetTodaysConversionDatasAlltimes ----------------------------
        $lifetime = null;
        try {
            $lifetime = $this->Menu_model->GetTodaysConversionDatasAlltimes($cid);
        } catch (Exception $e) {
            $lifetime = array('error' => 'function_unavailable', 'msg' => $e->getMessage());
        }

        // ---- 7: GetTodaysConversionDatasCFyear (uses FY from #8) -----------
        $fy_conversions = null;
        try {
            if ($fy_start !== null && $fy_end !== null) {
                $fy_conversions = $this->Menu_model->GetTodaysConversionDatasCFyear($cid, $fy_start, $fy_end);
            } else {
                $fy_conversions = array('error' => 'fy_range_unavailable');
            }
        } catch (Exception $e) {
            $fy_conversions = array('error' => 'function_unavailable', 'msg' => $e->getMessage());
        }

        // ---- Slip / Stagnancy computation (same logic as Agent B) ----------
        $status_section = array('slip_days' => null, 'stagnant_days' => null, 'stagnant_since' => null, 'current_status' => null, 'current_status_id' => null);
        try {
            $init_call_id = null;
            if (is_object($header) && isset($header->init_call_id)) {
                $init_call_id = (int)$header->init_call_id;
            }

            if ($init_call_id > 0) {
                $slip_row = $this->db->query("
                    SELECT
                        ic.cstatus,
                        ic.proposaldate,
                        s1.name AS current_status,
                        CASE
                            WHEN ic.proposaldate IS NULL OR ic.proposaldate = '0000-00-00' THEN NULL
                            WHEN ic.cstatus IN (12, 13, 14) THEN 0
                            WHEN ic.proposaldate < CURDATE() THEN DATEDIFF(CURDATE(), ic.proposaldate)
                            ELSE NULL
                        END AS slip_days,
                        COALESCE(
                            (SELECT MAX(fcl.created_at)
                               FROM funnel_change_log fcl
                              WHERE fcl.cid_id = ic.id),
                            ic.updated_at
                        ) AS stagnant_since,
                        DATEDIFF(CURDATE(),
                            COALESCE(
                                (SELECT MAX(fcl.created_at)
                                   FROM funnel_change_log fcl
                                  WHERE fcl.cid_id = ic.id),
                                ic.updated_at
                            )
                        ) AS stagnant_days
                    FROM init_call ic
                    LEFT JOIN status s1 ON s1.id = ic.cstatus
                    WHERE ic.id = ?
                    LIMIT 1
                ", array($init_call_id))->row();

                if ($slip_row) {
                    $status_section = array(
                        'current_status'    => $slip_row->current_status,
                        'current_status_id' => $slip_row->cstatus,
                        'slip_days'         => $slip_row->slip_days,
                        'stagnant_since'    => $slip_row->stagnant_since,
                        'stagnant_days'     => $slip_row->stagnant_days,
                    );
                }
            } else {
                // Fallback: extract status info from header
                if (is_object($header)) {
                    $status_section['current_status']    = isset($header->current_status)    ? $header->current_status    : null;
                    $status_section['current_status_id'] = isset($header->current_status_id) ? $header->current_status_id : null;
                }
            }
        } catch (Exception $e) {
            $status_section['error'] = $e->getMessage();
        }

        // ---- Assemble final response ----------------------------------------
        $response = array(
            'ok'           => true,
            'cid'          => $cid,
            'header'       => $header,
            'status'       => $status_section,
            'ownership'    => $ownership,
            'flags'        => $flags,
            'contacts'     => $contacts,
            'activities'   => $activities,
            'reviews'      => $reviews,
            'conversions'  => $conversions,
            'lifetime'     => $lifetime,
            'fy'           => $fy_conversions,
            'financial_year' => array(
                'start' => $fy_start,
                'end'   => $fy_end,
            ),
            'generated_at' => date('Y-m-d H:i:s'),
        );

        echo json_encode($response);
    }

    // -----------------------------------------------------------------------
    // === MIGRATION 076 — 6 GRANULAR ENDPOINTS ===
    // -----------------------------------------------------------------------

    // -----------------------------------------------------------------------
    // A) GET /api/company_details/profile/<cid>
    // Returns company profile: all fields from GetCompanyInfo + contacts from
    // getCompanyContactBYCID. ~58 fields total per prod map.
    // -----------------------------------------------------------------------
    public function profile($cid = 0)
    {
        $this->_auth_or_die();
        $cid = (int)$cid;
        if ($cid <= 0) {
            http_response_code(400);
            echo json_encode(array('ok' => false, 'error' => 'cid must be a positive integer'));
            return;
        }

        // GetCompanyInfo
        $info_raw = null;
        try {
            $info_raw = $this->Menu_model->GetCompanyInfo($cid);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(array('ok' => false, 'error' => 'GetCompanyInfo failed', 'msg' => $e->getMessage()));
            return;
        }

        if (empty($info_raw)) {
            http_response_code(404);
            echo json_encode(array('ok' => false, 'error' => 'cid_not_found', 'cid' => $cid));
            return;
        }

        $r = isset($info_raw[0]) ? $info_raw[0] : $info_raw;

        // Build company object with all 58 fields per prod map spec
        $company = array(
            // Identifiers
            'id'                        => $cid,
            'cmpid_id'                  => isset($r->cmpid_id)                    ? $r->cmpid_id                    : null,
            'init_call_id'              => isset($r->init_call_id)                ? $r->init_call_id                : null,
            // Company Overview card
            'compname'                  => isset($r->compname)                    ? $r->compname                    : null,
            'address'                   => isset($r->company_address)             ? $r->company_address             : null,
            'company_draft'             => isset($r->company_draft)               ? $r->company_draft               : null,
            'company_budget'            => isset($r->company_budget)              ? $r->company_budget              : null,
            'company_website'           => isset($r->company_website)             ? $r->company_website             : null,
            'company_country'           => isset($r->company_country)             ? $r->company_country             : null,
            'company_state'             => isset($r->company_state)               ? $r->company_state               : null,
            'company_district'          => isset($r->company_district)            ? $r->company_district            : null,
            'company_city'              => isset($r->company_city)                ? $r->company_city                : null,
            'created_date'              => isset($r->created_date)                ? $r->created_date                : null,
            'company_creater_name'      => isset($r->company_creater_name)        ? $r->company_creater_name        : null,
            'current_status'            => isset($r->current_status)              ? $r->current_status              : null,
            'current_status_id'         => isset($r->current_status_id)          ? $r->current_status_id          : null,
            'last_status'               => isset($r->last_status)                 ? $r->last_status                 : null,
            'need_to_be_monitored'      => isset($r->need_to_be_monitored)        ? $r->need_to_be_monitored        : null,
            'init_created_at'           => isset($r->init_created_at)             ? $r->init_created_at             : null,
            'last_updated_at'           => isset($r->last_updated_at)             ? $r->last_updated_at             : null,
            'company_is_admin_approved' => isset($r->company_is_admin_approved)  ? $r->company_is_admin_approved  : null,
            'company_apr_date'          => isset($r->company_apr_date)            ? $r->company_apr_date            : null,
            // Company Team Members card
            'mainbd_name'               => isset($r->mainbd_name)                 ? $r->mainbd_name                 : null,
            'mainbd_uid'                => isset($r->mainbd_uid)                  ? $r->mainbd_uid                  : null,
            'cluster_manager_name'      => isset($r->cluster_manager_name)        ? $r->cluster_manager_name        : null,
            'pst_name'                  => isset($r->pst_name)                    ? $r->pst_name                    : null,
            'ash_nae_co_id_name'        => isset($r->ash_nae_co_id_name)          ? $r->ash_nae_co_id_name          : null,
            'ash_w_co_id_name'          => isset($r->ash_w_co_id_name)            ? $r->ash_w_co_id_name            : null,
            'ash_s_co_id_name'          => isset($r->ash_s_co_id_name)            ? $r->ash_s_co_id_name            : null,
            'rm_east_co_id_name'        => isset($r->rm_east_co_id_name)          ? $r->rm_east_co_id_name          : null,
            'rm_north_co_id_name'       => isset($r->rm_north_co_id_name)         ? $r->rm_north_co_id_name         : null,
            'acm_co_id_name'            => isset($r->acm_co_id_name)              ? $r->acm_co_id_name              : null,
            'travel_cluster_create_name'=> isset($r->travel_cluster_create_name)  ? $r->travel_cluster_create_name  : null,
            'inside_sales_name'         => isset($r->inside_sales_name)           ? $r->inside_sales_name           : null,
            'company_apr_by'            => isset($r->company_apr_by)              ? $r->company_apr_by              : null,
            'user_cluster_zone'         => isset($r->user_cluster_zone)           ? $r->user_cluster_zone           : null,
            // Company Classification Details card
            'type'                      => isset($r->company_type)                ? $r->company_type                : null,
            'ownership'                 => isset($r->ownership)                   ? $r->ownership                   : null,
            'topspender'                => isset($r->topspender)                  ? $r->topspender                  : null,
            'upsell_client'             => isset($r->upsell_client)               ? $r->upsell_client               : null,
            'focus_funnel'              => isset($r->focus_funnel)                ? $r->focus_funnel                : null,
            'anchor_clients'            => isset($r->anchor_clients)              ? $r->anchor_clients              : null,
            'keycompany'                => isset($r->keycompany)                  ? $r->keycompany                  : null,
            'pkclient'                  => isset($r->pkclient)                    ? $r->pkclient                    : null,
            'priorityc'                 => isset($r->priorityc)                   ? $r->priorityc                   : null,
            'potential'                 => isset($r->potential)                   ? $r->potential                   : null,
            // Funnel Insights card
            'q1_twetenty_closure_funnel'=> isset($r->q1_twetenty_closure_funnel)  ? $r->q1_twetenty_closure_funnel  : null,
            'potential_funnel_for_fy'   => isset($r->potential_funnel_for_fy)     ? $r->potential_funnel_for_fy     : null,
            'to_be_nurtured_for_fy'     => isset($r->to_be_nurtured_for_fy)       ? $r->to_be_nurtured_for_fy       : null,
            'fifity_new_lead_funnel'    => isset($r->fifity_new_lead_funnel)      ? $r->fifity_new_lead_funnel      : null,
            'in_quarter'                => isset($r->in_quarter)                  ? $r->in_quarter                  : null,
            // Cluster and Travel Details card
            'cluster_id'                => isset($r->cluster_id)                  ? $r->cluster_id                  : null,
            'clustername'               => isset($r->clustername)                 ? $r->clustername                 : null,
            'travelType'                => isset($r->travelType)                  ? $r->travelType                  : null,
            // Partner
            'partner_name'              => isset($r->partner_name)                ? $r->partner_name                : null,
        );

        // Old BD name
        $old_main_bd = null;
        try {
            $old_bd_raw = $this->Menu_model->GetOldMainBD($cid);
            if (!empty($old_bd_raw) && isset($old_bd_raw[0])) {
                $old_main_bd = isset($old_bd_raw[0]->name) ? $old_bd_raw[0]->name : null;
            }
        } catch (Exception $e) {
            $old_main_bd = null; // field does not break response
        }
        $company['old_main_bd_name'] = $old_main_bd;

        // Contacts
        $contacts = array();
        try {
            $contact_raw = $this->Menu_model->getCompanyContactBYCID($cid);
            if (!empty($contact_raw) && is_array($contact_raw)) {
                foreach ($contact_raw as $c) {
                    $contacts[] = array(
                        'name'        => isset($c->contactperson) ? $c->contactperson : null,
                        'type'        => isset($c->type)          ? $c->type          : null,
                        'designation' => isset($c->designation)   ? $c->designation   : null,
                        'phone'       => isset($c->phoneno)       ? $c->phoneno       : null,
                        'email'       => isset($c->emailid)       ? $c->emailid       : null,
                        'linkedin'    => isset($c->linked_in)     ? $c->linked_in     : null,
                        'created_at'  => isset($c->createddate)   ? $c->createddate   : null,
                    );
                }
            }
        } catch (Exception $e) {
            $contacts = array(); // contacts not fatal
        }

        echo json_encode(array(
            'ok'           => true,
            'cid'          => $cid,
            'company'      => $company,
            'contacts'     => $contacts,
            'generated_at' => date('Y-m-d H:i:s'),
        ));
    }

    // -----------------------------------------------------------------------
    // B) GET /api/company_details/tasks/<cid>
    // All-time task history: 29-column rows, nextCFID != 0 (completed tasks).
    // Ordered by appointmentdatetime DESC. Cap 500 rows.
    // -----------------------------------------------------------------------
    public function tasks($cid = 0)
    {
        $this->_auth_or_die();
        $cid = (int)$cid;
        if ($cid <= 0) {
            http_response_code(400);
            echo json_encode(array('ok' => false, 'error' => 'cid must be a positive integer'));
            return;
        }

        $raw = null;
        try {
            $raw = $this->Menu_model->GetTaskOnCompanyByCID($cid);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(array('ok' => false, 'error' => 'GetTaskOnCompanyByCID failed', 'msg' => $e->getMessage()));
            return;
        }

        $rows = array();
        $sr   = 1;
        if (!empty($raw) && is_array($raw)) {
            // Sort by appointmentdatetime DESC (model may return by id DESC, but we enforce spec sort)
            usort($raw, function($a, $b) {
                $ta = isset($a->appointmentdatetime) ? strtotime($a->appointmentdatetime) : 0;
                $tb = isset($b->appointmentdatetime) ? strtotime($b->appointmentdatetime) : 0;
                return $tb - $ta;
            });
            foreach ($raw as $row) {
                // Only completed tasks (nextCFID != 0) per prod Section I spec
                $next_cf = isset($row->nextCFID) ? (int)$row->nextCFID : 0;
                if ($next_cf == 0) continue;
                $rows[] = $this->_format_task_row($row, $sr++);
                if ($sr > 500) break;
            }
        }

        echo json_encode(array(
            'ok'           => true,
            'cid'          => $cid,
            'total'        => count($rows),
            'tasks'        => $rows,
            'generated_at' => date('Y-m-d H:i:s'),
        ));
    }

    // -----------------------------------------------------------------------
    // C) GET /api/company_details/tasks_fy/<cid>
    // Same as tasks() but filtered to current FY by appointmentdatetime.
    // FY computed in PHP: April 1 to March 31.
    // -----------------------------------------------------------------------
    public function tasks_fy($cid = 0)
    {
        $this->_auth_or_die();
        $cid = (int)$cid;
        if ($cid <= 0) {
            http_response_code(400);
            echo json_encode(array('ok' => false, 'error' => 'cid must be a positive integer'));
            return;
        }

        $fy = $this->_fy_range();

        $raw = null;
        try {
            $raw = $this->Menu_model->GetTaskOnCompanyByCID($cid);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(array('ok' => false, 'error' => 'GetTaskOnCompanyByCID failed', 'msg' => $e->getMessage()));
            return;
        }

        $fy_start_ts = strtotime($fy['start_date']);
        $fy_end_ts   = strtotime($fy['end_date']);

        $rows = array();
        $sr   = 1;
        if (!empty($raw) && is_array($raw)) {
            usort($raw, function($a, $b) {
                $ta = isset($a->appointmentdatetime) ? strtotime($a->appointmentdatetime) : 0;
                $tb = isset($b->appointmentdatetime) ? strtotime($b->appointmentdatetime) : 0;
                return $tb - $ta;
            });
            foreach ($raw as $row) {
                // Only completed tasks
                $next_cf = isset($row->nextCFID) ? (int)$row->nextCFID : 0;
                if ($next_cf == 0) continue;
                // FY filter on appointmentdatetime date part (mirrors prod PHP view logic)
                if (!empty($row->appointmentdatetime)) {
                    $appt_ts = strtotime(date('Y-m-d', strtotime($row->appointmentdatetime)));
                    if ($appt_ts < $fy_start_ts || $appt_ts > $fy_end_ts) continue;
                } else {
                    continue; // no appointment date, skip
                }
                $rows[] = $this->_format_task_row($row, $sr++);
                if ($sr > 500) break;
            }
        }

        echo json_encode(array(
            'ok'             => true,
            'cid'            => $cid,
            'fy_start'       => $fy['start_date'],
            'fy_end'         => $fy['end_date'],
            'total'          => count($rows),
            'tasks'          => $rows,
            'generated_at'   => date('Y-m-d H:i:s'),
        ));
    }

    // -----------------------------------------------------------------------
    // D) GET /api/company_details/special_remarks/<task_id>
    // Mirrors Menu/GetSpecialRemarksUsingTaskID (POST in prod).
    // Returns structured JSON of the special_remarks JSON field from
    // tblcallevents so mobile can render its own modal.
    // -----------------------------------------------------------------------
    public function special_remarks($task_id = 0)
    {
        $this->_auth_or_die();
        $task_id = (int)$task_id;
        if ($task_id <= 0) {
            http_response_code(400);
            echo json_encode(array('ok' => false, 'error' => 'task_id must be a positive integer'));
            return;
        }

        // Fetch raw special_remarks JSON + metadata from tblcallevents
        // Column names per prod schema: tblcallevents.special_remarks, initiateddt, updated_at
        // Joined with user_details for created_by name
        $row = null;
        try {
            $row = $this->db->query("
                SELECT
                    tbcl.id            AS task_id,
                    tbcl.special_remarks,
                    tbcl.initiateddt   AS created_at,
                    tbcl.updated_at,
                    ud.name            AS created_by
                FROM tblcallevents tbcl
                LEFT JOIN user_details ud ON ud.user_id = tbcl.assignedto_id
                WHERE tbcl.id = ?
                LIMIT 1
            ", array($task_id))->row();
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(array('ok' => false, 'error' => 'query_failed', 'msg' => $e->getMessage()));
            return;
        }

        if (empty($row)) {
            http_response_code(404);
            echo json_encode(array('ok' => false, 'error' => 'task_not_found', 'task_id' => $task_id));
            return;
        }

        // Parse special_remarks JSON into structured array
        $remarks_data = null;
        $plain_text   = null;
        $raw_json     = isset($row->special_remarks) ? $row->special_remarks : null;

        if (!empty($raw_json) && $raw_json !== 'null') {
            $decoded = json_decode($raw_json, true);
            if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                $remarks_data = $decoded;
                // Build plain text version: "key: value\n" for each pair
                $lines = array();
                foreach ($decoded as $key => $val) {
                    if (is_array($val)) {
                        $val = implode(', ', $val);
                    }
                    $lines[] = trim($key) . ': ' . trim((string)$val);
                }
                $plain_text = implode("\n", $lines);
            } else {
                // Not valid JSON - treat as plain text string
                $plain_text   = $raw_json;
                $remarks_data = null;
            }
        }

        echo json_encode(array(
            'ok'           => true,
            'task_id'      => $task_id,
            'remarks_html' => null, // mobile renders its own; not returning HTML
            'remarks_data' => $remarks_data,
            'plain_text'   => $plain_text,
            'created_by'   => isset($row->created_by) ? $row->created_by : null,
            'created_at'   => isset($row->created_at) ? $row->created_at : null,
            'updated_at'   => isset($row->updated_at) ? $row->updated_at : null,
        ));
    }

    // -----------------------------------------------------------------------
    // E) GET /api/company_details/conversions/<cid>?period=all|cfy
    // Status change counts grouped by from_status -> to_status.
    // period=all  -> GetTodaysConversionDatasAlltimes
    // period=cfy  -> GetTodaysConversionDatasCFyear (FY start to today)
    // Returns: status_change, total_changes, status_change_id
    // -----------------------------------------------------------------------
    public function conversions($cid = 0)
    {
        $this->_auth_or_die();
        $cid = (int)$cid;
        if ($cid <= 0) {
            http_response_code(400);
            echo json_encode(array('ok' => false, 'error' => 'cid must be a positive integer'));
            return;
        }

        $period = $this->input->get('period');
        if (empty($period)) $period = 'all';
        $period = strtolower(trim($period));
        if (!in_array($period, array('all', 'cfy'))) {
            http_response_code(400);
            echo json_encode(array('ok' => false, 'error' => 'period must be all or cfy'));
            return;
        }

        $data = null;
        $fy   = $this->_fy_range();
        try {
            if ($period === 'cfy') {
                // GetTodaysConversionDatasCFyear uses start_date to today (not FY end)
                $today = date('Y-m-d');
                $data  = $this->Menu_model->GetTodaysConversionDatasCFyear($cid, $fy['start_date'], $today);
            } else {
                $data = $this->Menu_model->GetTodaysConversionDatasAlltimes($cid);
            }
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(array('ok' => false, 'error' => 'model_function_failed', 'msg' => $e->getMessage()));
            return;
        }

        // Normalise to plain array
        $rows = array();
        if (!empty($data) && is_array($data)) {
            foreach ($data as $r) {
                $rows[] = array(
                    'status_change'    => isset($r->status_change)    ? $r->status_change    : null,
                    'total_changes'    => isset($r->total_changes)    ? (int)$r->total_changes : 0,
                    'status_change_id' => isset($r->status_change_id) ? $r->status_change_id  : null,
                );
            }
        }

        echo json_encode(array(
            'ok'           => true,
            'cid'          => $cid,
            'period'       => $period,
            'fy_start'     => ($period === 'cfy') ? $fy['start_date'] : null,
            'total'        => count($rows),
            'conversions'  => $rows,
            'generated_at' => date('Y-m-d H:i:s'),
        ));
    }

    // -----------------------------------------------------------------------
    // F) GET /api/company_details/conversions_typed/<cid>?type=positive|negative|other
    // Same shape as conversions() but returns task rows bucketed by
    // conversion direction (uses GetTaskConversionByCID).
    // Positive: moving toward Won (1->2, 2->3, 3->6, 6->8, 8->9, 9->11, 11->12)
    // Negative: moving toward Lost/Drop (anything to 13 or 14)
    // Other: lateral or back-moves
    // -----------------------------------------------------------------------
    public function conversions_typed($cid = 0)
    {
        $this->_auth_or_die();
        $cid = (int)$cid;
        if ($cid <= 0) {
            http_response_code(400);
            echo json_encode(array('ok' => false, 'error' => 'cid must be a positive integer'));
            return;
        }

        $type = $this->input->get('type');
        if (empty($type)) $type = 'positive';
        $type = strtolower(trim($type));
        if (!in_array($type, array('positive', 'negative', 'other'))) {
            http_response_code(400);
            echo json_encode(array('ok' => false, 'error' => 'type must be positive, negative, or other'));
            return;
        }

        // Map to model param
        $conversion_param = $type . '_conversions'; // e.g. 'positive_conversions'

        $raw = null;
        try {
            $raw = $this->Menu_model->GetTaskConversionByCID($cid, $conversion_param);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(array('ok' => false, 'error' => 'GetTaskConversionByCID failed', 'msg' => $e->getMessage()));
            return;
        }

        // Count grouped transitions: status_change -> count
        $grouped = array();
        $task_rows = array();
        $sr = 1;
        if (!empty($raw) && is_array($raw)) {
            foreach ($raw as $row) {
                // Build status_change label (same format as GetTodaysConversionDatasAlltimes)
                $from_s = isset($row->task_time_status)     ? $row->task_time_status     : 'Unknown';
                $to_s   = isset($row->task_time_new_status) ? $row->task_time_new_status : 'Unknown';
                $key    = $from_s . ' -> ' . $to_s;
                if (!isset($grouped[$key])) {
                    $grouped[$key] = 0;
                }
                $grouped[$key]++;
                $task_rows[] = $this->_format_task_row($row, $sr++);
                if ($sr > 500) break;
            }
        }

        // Build summary array
        $summary = array();
        foreach ($grouped as $change => $count) {
            $summary[] = array(
                'status_change' => $change,
                'total_changes' => $count,
            );
        }
        // Sort by count desc
        usort($summary, function($a, $b) { return $b['total_changes'] - $a['total_changes']; });

        echo json_encode(array(
            'ok'           => true,
            'cid'          => $cid,
            'type'         => $type,
            'summary'      => $summary,
            'total'        => count($task_rows),
            'tasks'        => $task_rows,
            'generated_at' => date('Y-m-d H:i:s'),
        ));
    }
}

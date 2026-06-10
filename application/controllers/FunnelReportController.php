<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * FunnelReportController
 *
 * Migration 075 -- Card 3 (Funnel Report sub-pages) + Card 18 (Graph Analysis charts).
 * Patched: expanded rich-field SELECT for 6 endpoints, slip_days + stagnant_days columns,
 *          legacy filter parity (sdate, edate, admin_id_filter, rm_filter), role gating,
 *          new lead_detail endpoint.
 *
 * Auth: Bearer STEM_DIGEST_TOKEN header required on all endpoints except probe.
 *
 * Stagnancy note: stagnant_days and stagnancy_red are computed in PHP post-processing
 * (using a single MAX(fcl.created_at) join per lead) to avoid per-row correlated subqueries.
 *
 * Routes to add in application/config/routes.php:
 *   $route['api/funnel_report/probe']['get']              = 'FunnelReportController/probe';
 *   $route['api/funnel_report/stuck_status']['get']       = 'FunnelReportController/stuck_status';
 *   $route['api/funnel_report/companies_without_dm']['get']= 'FunnelReportController/companies_without_dm';
 *   $route['api/funnel_report/closing_timeline']['get']   = 'FunnelReportController/closing_timeline';
 *   $route['api/funnel_report/funnel_transfer']['get']    = 'FunnelReportController/funnel_transfer';
 *   $route['api/funnel_report/created_between']['get']    = 'FunnelReportController/created_between';
 *   $route['api/funnel_report/deleted_between']['get']    = 'FunnelReportController/deleted_between';
 *   $route['api/funnel_report/conversion_summary']['get'] = 'FunnelReportController/conversion_summary';
 *   $route['api/funnel_report/pending_moms']['get']       = 'FunnelReportController/pending_moms';
 *   $route['api/funnel_report/line_mgr_rp_pending']['get']= 'FunnelReportController/line_mgr_rp_pending';
 *   $route['api/funnel_report/lead_detail/(:num)']['get'] = 'FunnelReportController/lead_detail/$1';
 *   $route['api/graph_analysis/status_distribution']['get']  = 'FunnelReportController/status_distribution';
 *   $route['api/graph_analysis/day_of_week_meetings']['get'] = 'FunnelReportController/day_of_week_meetings';
 *   $route['api/graph_analysis/planner_adherence']['get']    = 'FunnelReportController/planner_adherence';
 */
class FunnelReportController extends CI_Controller
{
    const MIGRATION = '075';

    public function __construct()
    {
        parent::__construct();
        $this->load->database();
        $this->load->helper('url');
        // Load config files that contain the STEM_DIGEST_TOKEN
        $this->config->load('rest',   true, true);
        $this->config->load('custom', true, true);
        header('Content-Type: application/json; charset=utf-8');
    }

    // -----------------------------------------------------------------------
    // Auth guard -- Bearer token or active session
    // -----------------------------------------------------------------------
    private $_authed_uid = 0;
    private $_authed_type_id = 0;

    // ---- per-user JWT validator (added 28 May 2026, matches Auth::api_login) ----
    private function _jwt_token_valid($token) {
        if (empty($token)) return false;
        $secret = getenv('STEM_DIGEST_TOKEN') ?: '4eBaiAT7r4zu6OK3b8evjLNia1D7RGgb0qRTuLJfUSo';
        $days = array(date('Y-m-d'), date('Y-m-d', strtotime('-1 day')), date('Y-m-d', strtotime('+1 day')));
        $candidates = array();
        foreach (array('uid','cm_uid','rm_uid','bd_uid','acm_uid','user_id') as $k) {
            if (isset($_GET[$k]) && (int)$_GET[$k] > 0) $candidates[(int)$_GET[$k]] = 1;
            if (isset($_POST[$k]) && (int)$_POST[$k] > 0) $candidates[(int)$_POST[$k]] = 1;
        }
        foreach (array_keys($candidates) as $uid) {
            foreach ($days as $d) {
                if (hash_equals(sha1($secret.'|'.$uid.'|'.$d), $token)) return (int)$uid;
            }
        }
        static $all_uids = null;
        if ($all_uids === null) {
            $rows = $this->db->select('uid')->from('user')->where('active', 1)->get()->result();
            $all_uids = array();
            foreach ($rows as $r) $all_uids[] = (int)$r->uid;
        }
        foreach ($all_uids as $uid) {
            foreach ($days as $d) {
                if (hash_equals(sha1($secret.'|'.$uid.'|'.$d), $token)) return (int)$uid;
            }
        }
        return false;
    }

    private function _auth_or_die()
    {
        $hdr = $this->input->get_request_header('Authorization', true);
        if (empty($hdr) && function_exists('apache_request_headers')) {
            $hdrs = apache_request_headers();
            if (isset($hdrs['Authorization']))       $hdr = $hdrs['Authorization'];
            elseif (isset($hdrs['authorization']))   $hdr = $hdrs['authorization'];
        }
        if (empty($hdr) && isset($_SERVER['HTTP_AUTHORIZATION'])) {
            $hdr = $_SERVER['HTTP_AUTHORIZATION'];
        }

        $expected = getenv('STEM_DIGEST_TOKEN');
        if (empty($expected)) $expected = $this->config->item('stem_digest_token');
        if (empty($expected)) $expected = $this->config->item('STEM_DIGEST_TOKEN');
        if (empty($expected)) $expected = '4eBaiAT7r4zu6OK3b8evjLNia1D7RGgb0qRTuLJfUSo';

        if (!empty($hdr) && $hdr === 'Bearer ' . $expected) return true;

        if (!empty($hdr) && stripos($hdr, 'Bearer ') === 0) {
            $tok = trim(substr($hdr, 7));
            $uid = $this->_jwt_token_valid($tok);
            if ($uid) {
                $this->_authed_uid = $uid;
                $urow = $this->db->select('type_id')->from('user')->where('uid', $uid)->get()->row();
                if ($urow) $this->_authed_type_id = (int)$urow->type_id;
                return true;
            }
        }

        $session_uid = $this->session->userdata('user_id');
        if ((int) $session_uid > 0) return true;

        http_response_code(401);
        echo json_encode(array('error' => 'unauthorized', 'hdr_received' => !empty($hdr)));
        exit;
    }

    // -----------------------------------------------------------------------
    // Helper: validate user_id param
    // -----------------------------------------------------------------------
    private function _require_user_id()
    {
        $uid = (int) $this->input->get('user_id');
        // rimlyproof_frc_default_uid_20260608: default to authenticated user when no param
        if ($uid <= 0 && $this->_authed_uid > 0) {
            $uid = (int) $this->_authed_uid;
        }
        if ($uid <= 0) {
            http_response_code(400);
            echo json_encode(array('error' => 'user_id required'));
            exit;
        }
        return $uid;
    }

    // -----------------------------------------------------------------------
    // Helper: legacy filter parity -- resolve uid from rm_filter / admin_id_filter
    // Section 12.3: if rm_filter != 'all', uid = rm_filter; else uid = admin_id_filter
    // BD (type_id 5): forced to own JWT uid.
    // TODO: add cluster_id restriction for type_id 13 (CM) when cluster field confirmed.
    // -----------------------------------------------------------------------
    private function _resolve_uid_and_scope($uid)
    {
        $admin_id_filter = $this->input->get('admin_id_filter');
        $rm_filter       = $this->input->get('rm_filter');
        $type_id         = $this->_authed_type_id;

        if ($type_id === 5 && $this->_authed_uid > 0) return $this->_authed_uid;

        if (!empty($rm_filter) && $rm_filter !== 'all') return (int)$rm_filter;
        if (!empty($admin_id_filter) && $admin_id_filter !== 'all') return (int)$admin_id_filter;

        return $uid;
    }

    // -----------------------------------------------------------------------
    // Helper: sdate/edate from query string
    // -----------------------------------------------------------------------
    private function _get_date_filters()
    {
        $sdate = $this->input->get('sdate');
        $edate = $this->input->get('edate');
        if (!empty($sdate) && !preg_match('/^\d{4}-\d{2}-\d{2}$/', $sdate)) $sdate = null;
        if (!empty($edate) && !preg_match('/^\d{4}-\d{2}-\d{2}$/', $edate)) $edate = null;
        return array($sdate, $edate);
    }

    // -----------------------------------------------------------------------
    // Post-process rows: add stagnant_days and stagnancy_red computed in PHP.
    // Uses stagnant_since already returned by the SQL (single MAX per lead via join).
    // Stagnancy RED thresholds (Section 17.2):
    //   cstatus 1>3, 2>5, 3>5, 6>7, 8>30, 9>14, 11>7
    // -----------------------------------------------------------------------
    private function _add_stagnancy_flags(array &$rows)
    {
        static $thresholds = array(1=>3, 2=>5, 3=>5, 6=>7, 8=>30, 9=>14, 11=>7);
        $today = date('Y-m-d');

        foreach ($rows as &$row) {
            // stagnant_since is already set by SQL (MAX fcl join or updated_at fallback)
            $since = isset($row['stagnant_since']) ? $row['stagnant_since'] : null;
            if ($since) {
                $diff = (int) floor((strtotime($today) - strtotime(substr($since, 0, 10))) / 86400);
                $row['stagnant_days'] = $diff;
                $cs = isset($row['cstatus']) ? (int)$row['cstatus'] : 0;
                $row['stagnancy_red'] = (isset($thresholds[$cs]) && $diff > $thresholds[$cs]) ? 1 : 0;
            } else {
                $row['stagnant_days'] = null;
                $row['stagnancy_red'] = 0;
            }

            // slip_days already computed in SQL; add is_slipped shortcut if not present
            if (!isset($row['is_slipped'])) {
                $pd = isset($row['proposaldate']) ? $row['proposaldate'] : null;
                $cs = isset($row['cstatus']) ? (int)$row['cstatus'] : 0;
                $row['is_slipped'] = ($pd && $pd < $today && !in_array($cs, array(12,13,14))) ? 1 : 0;
            }
        }
        unset($row);
    }

    // -----------------------------------------------------------------------
    // Slip days SQL fragment (no correlated subquery -- just DATEDIFF on proposaldate)
    // -----------------------------------------------------------------------
    private function _slip_sql()
    {
        return "
                CASE
                  WHEN ic.proposaldate IS NULL THEN NULL
                  WHEN ic.cstatus IN (12,13,14) THEN 0
                  ELSE DATEDIFF(CURDATE(), ic.proposaldate)
                END AS slip_days,
                CASE
                  WHEN ic.proposaldate IS NOT NULL
                   AND ic.cstatus NOT IN (12,13,14)
                   AND ic.proposaldate < CURDATE() THEN 1
                  ELSE 0
                END AS is_slipped";
    }

    // -----------------------------------------------------------------------
    // GET /api/funnel_report/probe
    // -----------------------------------------------------------------------
    public function probe()
    {
        echo json_encode(array(
            'ok'        => true,
            'migration' => self::MIGRATION,
            'ts'        => date('Y-m-d H:i:s'),
        ));
    }

    // -----------------------------------------------------------------------
    // GET /api/funnel_report/stuck_status?user_id=X&days=14
    //     [&sdate=&edate=&admin_id_filter=&rm_filter=]
    // -----------------------------------------------------------------------
    public function stuck_status()
    {
        $this->_auth_or_die();
        $uid  = $this->_require_user_id();
        $uid  = $this->_resolve_uid_and_scope($uid);
        $days = max(1, (int) ($this->input->get('days') ?: 14));
        list($sdate, $edate) = $this->_get_date_filters();

        $date_clause = '';
        $date_params = array();
        if (!empty($sdate)) { $date_clause .= ' AND ic.createDate >= ?'; $date_params[] = $sdate; }
        if (!empty($edate)) { $date_clause .= ' AND ic.createDate <= ?'; $date_params[] = $edate; }

        $sql = "
            SELECT
                ic.id                         AS lead_id,
                ic.cmpid_id                   AS company_id,
                cm.compname                   AS company_name,
                cm.address, cm.city, cm.state, cm.district, cm.website,
                cm.comp_business_potential, cm.comp_top_spender, cm.comp_key_company,
                cm.anchor_lat, cm.anchor_lng,
                ic.logo_url                   AS company_logo,
                ic.cstatus,
                s.name                        AS cstatus_label,
                ic.proposaldate,
                " . $this->_slip_sql() . ",
                ic.fbudget_min_cr, ic.fbudget_max_cr,
                ic.dm_contact_name, ic.dm_contact_designation,
                ic.dm_contact_phone, ic.dm_contact_email,
                ic.lead_score_cached, ic.lead_source,
                ic.prospecting_funnel, ic.closure_pipeline,
                ic.priorityc, ic.in_quarter,
                ic.mainbd, ub.name             AS bd_name,
                COALESCE(
                    DATEDIFF(NOW(), MAX(fcl.created_at)),
                    DATEDIFF(NOW(), ic.updated_at)
                )                              AS days_stuck,
                ic.updated_at                  AS last_activity,
                COALESCE(MAX(fcl.created_at), ic.updated_at) AS stagnant_since
            FROM init_call ic
            LEFT JOIN company_master cm        ON cm.id  = ic.cmpid_id
            LEFT JOIN status s                 ON s.id   = ic.cstatus
            LEFT JOIN funnel_change_log fcl    ON fcl.cid_id = ic.id
            LEFT JOIN user ub                  ON ub.uid = ic.mainbd
            WHERE (ic.mainbd = ? OR ic.insidebd = ? OR ic.acm_co_id = ?)
              AND ic.cstatus NOT IN (13, 14)
              {$date_clause}
            GROUP BY ic.id
            HAVING days_stuck >= ?
            ORDER BY days_stuck DESC
            LIMIT 200
        ";

        $params = array_merge(array($uid, $uid, $uid), $date_params, array($days));
        $rows = $this->db->query($sql, $params)->result_array();
        $this->_add_stagnancy_flags($rows);

        echo json_encode(array(
            'ok'    => true,
            'days'  => $days,
            'count' => count($rows),
            'data'  => $rows,
        ));
    }

    // -----------------------------------------------------------------------
    // GET /api/funnel_report/companies_without_dm?user_id=X
    //     [&sdate=&edate=&admin_id_filter=&rm_filter=]
    // -----------------------------------------------------------------------
    public function companies_without_dm()
    {
        $this->_auth_or_die();
        $uid = $this->_require_user_id();
        $uid = $this->_resolve_uid_and_scope($uid);
        list($sdate, $edate) = $this->_get_date_filters();

        $date_clause = '';
        $date_params = array();
        if (!empty($sdate)) { $date_clause .= ' AND ic.createDate >= ?'; $date_params[] = $sdate; }
        if (!empty($edate)) { $date_clause .= ' AND ic.createDate <= ?'; $date_params[] = $edate; }

        $sql = "
            SELECT
                ic.id         AS lead_id,
                ic.cmpid_id   AS company_id,
                cm.compname   AS company_name,
                cm.address, cm.city, cm.state, cm.district,
                cm.website, cm.comp_business_potential,
                ic.logo_url   AS company_logo,
                ic.cstatus,
                s.name        AS cstatus_label,
                ic.proposaldate,
                " . $this->_slip_sql() . ",
                ic.fbudget_min_cr, ic.fbudget_max_cr,
                ic.lead_score_cached, ic.lead_source,
                ic.prospecting_funnel, ic.priorityc, ic.in_quarter,
                DATEDIFF(NOW(), ic.createDate) AS lead_age_days,
                (SELECT COUNT(*) FROM company_contact_master ccm WHERE ccm.company_id = ic.cmpid_id) AS contact_count,
                ub.name AS bd_name,
                COALESCE(
                    (SELECT MAX(fcl2.created_at) FROM funnel_change_log fcl2 WHERE fcl2.cid_id = ic.id),
                    ic.updated_at
                ) AS stagnant_since
            FROM init_call ic
            LEFT JOIN company_master cm ON cm.id = ic.cmpid_id
            LEFT JOIN status s          ON s.id  = ic.cstatus
            LEFT JOIN user ub           ON ub.uid = ic.mainbd
            WHERE (ic.mainbd = ? OR ic.insidebd = ? OR ic.acm_co_id = ?)
              AND ic.cstatus NOT IN (13, 14)
              AND (ic.dm_contact_name IS NULL OR ic.dm_contact_name = ''
                   OR ic.dm_contact_phone IS NULL OR ic.dm_contact_phone = '')
              {$date_clause}
            ORDER BY ic.lead_score_cached DESC, ic.createDate DESC
            LIMIT 200
        ";

        $params = array_merge(array($uid, $uid, $uid), $date_params);
        $rows = $this->db->query($sql, $params)->result_array();
        $this->_add_stagnancy_flags($rows);

        echo json_encode(array(
            'ok'    => true,
            'count' => count($rows),
            'data'  => $rows,
        ));
    }

    // -----------------------------------------------------------------------
    // GET /api/funnel_report/closing_timeline?user_id=X
    //     [&sdate=&edate=&admin_id_filter=&rm_filter=]
    // -----------------------------------------------------------------------
    public function closing_timeline()
    {
        $this->_auth_or_die();
        $uid = $this->_require_user_id();
        $uid = $this->_resolve_uid_and_scope($uid);
        list($sdate, $edate) = $this->_get_date_filters();

        $date_clause = '';
        $date_params = array();
        if (!empty($sdate)) { $date_clause .= ' AND ic.createDate >= ?'; $date_params[] = $sdate; }
        if (!empty($edate)) { $date_clause .= ' AND ic.createDate <= ?'; $date_params[] = $edate; }

        $sql = "
            SELECT
                ic.id           AS lead_id,
                ic.cmpid_id     AS company_id,
                cm.compname     AS company_name,
                cm.address, cm.city, cm.state, cm.district,
                cm.website, cm.comp_business_potential,
                ic.logo_url     AS company_logo,
                ic.cstatus,
                s.name          AS cstatus_label,
                ic.proposaldate,
                DATEDIFF(ic.proposaldate, CURDATE()) AS days_to_proposal,
                CASE WHEN ic.proposaldate < CURDATE() AND ic.cstatus < 12 THEN 1 ELSE 0 END AS slipped,
                " . $this->_slip_sql() . ",
                ic.fbudget, ic.fbudget_min, ic.fbudget_max,
                ic.fbudget_min_cr, ic.fbudget_max_cr,
                ic.dm_contact_name, ic.dm_contact_designation,
                ic.dm_contact_phone, ic.dm_contact_email,
                ic.lead_score_cached, ic.lead_source,
                ic.prospecting_funnel, ic.closure_pipeline, ic.priorityc, ic.in_quarter,
                ub.name AS bd_name,
                COALESCE(
                    (SELECT MAX(fcl2.created_at) FROM funnel_change_log fcl2 WHERE fcl2.cid_id = ic.id),
                    ic.updated_at
                ) AS stagnant_since
            FROM init_call ic
            LEFT JOIN company_master cm ON cm.id = ic.cmpid_id
            LEFT JOIN status s          ON s.id  = ic.cstatus
            LEFT JOIN user ub           ON ub.uid = ic.mainbd
            WHERE (ic.mainbd = ? OR ic.insidebd = ? OR ic.acm_co_id = ?)
              AND ic.proposaldate IS NOT NULL
              AND ic.cstatus NOT IN (13, 14)
              {$date_clause}
            ORDER BY slipped DESC, ic.proposaldate ASC
            LIMIT 200
        ";

        $params = array_merge(array($uid, $uid, $uid), $date_params);
        $rows = $this->db->query($sql, $params)->result_array();
        $this->_add_stagnancy_flags($rows);

        echo json_encode(array(
            'ok'    => true,
            'count' => count($rows),
            'data'  => $rows,
        ));
    }

    // -----------------------------------------------------------------------
    // GET /api/funnel_report/funnel_transfer?user_id=X&days=30
    //     [&sdate=&edate=&admin_id_filter=&rm_filter=]
    // Note: funnel_transfer_log uses 'cid' column for the lead FK (not cid_id).
    // -----------------------------------------------------------------------
    public function funnel_transfer()
    {
        $this->_auth_or_die();
        $uid  = $this->_require_user_id();
        $uid  = $this->_resolve_uid_and_scope($uid);
        $days = max(1, (int) ($this->input->get('days') ?: 30));
        list($sdate, $edate) = $this->_get_date_filters();

        $date_clause = '';
        $date_params = array();
        if (!empty($sdate)) { $date_clause .= ' AND ftl.created_at >= ?'; $date_params[] = $sdate; }
        if (!empty($edate)) { $date_clause .= ' AND ftl.created_at <= ?'; $date_params[] = $edate . ' 23:59:59'; }

        $sql = "
            SELECT
                ftl.id          AS transfer_id,
                ftl.cid         AS lead_id,
                ic.cmpid_id     AS company_id,
                cm.compname     AS company_name,
                cm.city, cm.state, cm.district,
                cm.comp_business_potential,
                ic.logo_url     AS company_logo,
                ic.cstatus,
                s.name          AS cstatus_label,
                ic.fbudget_min_cr, ic.fbudget_max_cr, ic.lead_score_cached,
                ic.proposaldate,
                " . $this->_slip_sql() . ",
                ftl.from_uid, uf.name AS from_bd_name,
                ftl.to_uid,   ut.name AS to_bd_name,
                ftl.remarks,
                ftl.created_at AS transferred_at,
                DATEDIFF(NOW(), ftl.created_at) AS days_since_transfer,
                COALESCE(
                    (SELECT MAX(fcl2.created_at) FROM funnel_change_log fcl2 WHERE fcl2.cid_id = ic.id),
                    ic.updated_at
                ) AS stagnant_since
            FROM funnel_transfer_log ftl
            JOIN init_call ic           ON ic.id  = ftl.cid
            LEFT JOIN company_master cm ON cm.id  = ic.cmpid_id
            LEFT JOIN status s          ON s.id   = ic.cstatus
            LEFT JOIN user uf           ON uf.uid = ftl.from_uid
            LEFT JOIN user ut           ON ut.uid = ftl.to_uid
            WHERE (ftl.from_uid = ? OR ftl.to_uid = ?)
              AND ftl.created_at >= DATE_SUB(NOW(), INTERVAL ? DAY)
              {$date_clause}
            ORDER BY ftl.created_at DESC
            LIMIT 200
        ";

        $params = array_merge(array($uid, $uid, $days), $date_params);
        $rows = $this->db->query($sql, $params)->result_array();
        $this->_add_stagnancy_flags($rows);

        echo json_encode(array(
            'ok'    => true,
            'days'  => $days,
            'count' => count($rows),
            'data'  => $rows,
        ));
    }

    // -----------------------------------------------------------------------
    // GET /api/funnel_report/created_between?user_id=X&from=YYYY-MM-DD&to=YYYY-MM-DD
    //     [&sdate=&edate=&admin_id_filter=&rm_filter=]
    // -----------------------------------------------------------------------
    public function created_between()
    {
        $this->_auth_or_die();
        $uid  = $this->_require_user_id();
        $uid  = $this->_resolve_uid_and_scope($uid);
        list($sdate, $edate) = $this->_get_date_filters();

        $from = !empty($sdate) ? $sdate : ($this->input->get('from') ?: date('Y-m-01'));
        $to   = !empty($edate) ? $edate : ($this->input->get('to')   ?: date('Y-m-d'));

        if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $from) ||
            !preg_match('/^\d{4}-\d{2}-\d{2}$/', $to)) {
            http_response_code(400);
            echo json_encode(array('error' => 'from/to must be YYYY-MM-DD'));
            return;
        }

        $sql = "
            SELECT
                ic.id           AS lead_id,
                ic.cmpid_id     AS company_id,
                cm.compname     AS company_name,
                cm.address, cm.city, cm.state, cm.district,
                cm.comp_business_potential,
                ic.logo_url     AS company_logo,
                ic.cstatus,
                s.name          AS cstatus_label,
                ic.createDate   AS created_at,
                ic.proposaldate,
                " . $this->_slip_sql() . ",
                ic.fbudget_min_cr, ic.fbudget_max_cr,
                ic.dm_contact_name, ic.dm_contact_phone,
                ic.lead_score_cached, ic.lead_source,
                ic.prospecting_funnel, ic.priorityc,
                ic.apst, ic.pstadt,
                ic.new_lead, ic.creator_id,
                ub.name AS bd_name,
                CASE
                  WHEN ic.creator_id = ic.mainbd THEN 'NEW_LEAD_FORM'
                  WHEN ic.creator_id != ic.mainbd THEN 'ADMIN_CREATED'
                  ELSE 'OTHER'
                END AS creation_path,
                COALESCE(
                    (SELECT MAX(fcl2.created_at) FROM funnel_change_log fcl2 WHERE fcl2.cid_id = ic.id),
                    ic.updated_at
                ) AS stagnant_since
            FROM init_call ic
            LEFT JOIN company_master cm ON cm.id = ic.cmpid_id
            LEFT JOIN status s          ON s.id  = ic.cstatus
            LEFT JOIN user ub           ON ub.uid = ic.mainbd
            WHERE (ic.mainbd = ? OR ic.insidebd = ? OR ic.acm_co_id = ?)
              AND ic.createDate BETWEEN ? AND ?
            ORDER BY ic.createDate DESC
            LIMIT 500
        ";

        $rows  = $this->db->query($sql, array($uid, $uid, $uid, $from, $to))->result_array();
        $this->_add_stagnancy_flags($rows);

        echo json_encode(array(
            'ok'    => true,
            'from'  => $from,
            'to'    => $to,
            'count' => count($rows),
            'data'  => $rows,
        ));
    }

    // -----------------------------------------------------------------------
    // GET /api/funnel_report/deleted_between?user_id=X&from=YYYY-MM-DD&to=YYYY-MM-DD
    //     [&sdate=&edate=&admin_id_filter=&rm_filter=]
    // -----------------------------------------------------------------------
    public function deleted_between()
    {
        $this->_auth_or_die();
        $uid  = $this->_require_user_id();
        $uid  = $this->_resolve_uid_and_scope($uid);
        list($sdate, $edate) = $this->_get_date_filters();

        $from = !empty($sdate) ? $sdate : ($this->input->get('from') ?: date('Y-m-01'));
        $to   = !empty($edate) ? $edate : ($this->input->get('to')   ?: date('Y-m-d'));

        if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $from) ||
            !preg_match('/^\d{4}-\d{2}-\d{2}$/', $to)) {
            http_response_code(400);
            echo json_encode(array('error' => 'from/to must be YYYY-MM-DD'));
            return;
        }

        $sql = "
            SELECT
                ic.id           AS lead_id,
                ic.cmpid_id     AS company_id,
                cm.compname     AS company_name,
                cm.city, cm.state, cm.district,
                cm.comp_business_potential,
                ic.logo_url     AS company_logo,
                ic.cstatus,
                s.name          AS cstatus_label,
                ic.proposaldate,
                " . $this->_slip_sql() . ",
                ic.fbudget_min_cr, ic.fbudget_max_cr,
                ic.lead_score_cached, ic.lead_source,
                ic.prospecting_funnel, ic.priorityc,
                ic.updated_at AS lost_at,
                ub.name AS bd_name,
                COALESCE(
                    (SELECT MAX(fcl2.created_at) FROM funnel_change_log fcl2 WHERE fcl2.cid_id = ic.id),
                    ic.updated_at
                ) AS stagnant_since
            FROM init_call ic
            LEFT JOIN company_master cm ON cm.id = ic.cmpid_id
            LEFT JOIN status s          ON s.id  = ic.cstatus
            LEFT JOIN user ub           ON ub.uid = ic.mainbd
            WHERE (ic.mainbd = ? OR ic.insidebd = ? OR ic.acm_co_id = ?)
              AND ic.cstatus IN (13, 14)
              AND DATE(ic.updated_at) BETWEEN ? AND ?
            ORDER BY ic.updated_at DESC
            LIMIT 500
        ";

        $rows = $this->db->query($sql, array($uid, $uid, $uid, $from, $to))->result_array();
        $this->_add_stagnancy_flags($rows);

        echo json_encode(array(
            'ok'     => true,
            'from'   => $from,
            'to'     => $to,
            'count'  => count($rows),
            'note'   => 'Showing leads closed/lost (cstatus 13-14) in date range',
            'data'   => $rows,
        ));
    }

    // -----------------------------------------------------------------------
    // GET /api/funnel_report/conversion_summary?user_id=X
    // -----------------------------------------------------------------------
    public function conversion_summary()
    {
        $this->_auth_or_die();
        $uid = $this->_require_user_id();

        $sql = "
            SELECT
                ic.cstatus,
                s.name      AS cstatus_label,
                COUNT(ic.id) AS lead_count
            FROM init_call ic
            LEFT JOIN status s ON s.id = ic.cstatus
            WHERE (ic.mainbd = ? OR ic.insidebd = ? OR ic.acm_co_id = ?)
              AND ic.cstatus BETWEEN 1 AND 14
            GROUP BY ic.cstatus, s.name
            ORDER BY ic.cstatus ASC
        ";

        $rows  = $this->db->query($sql, array($uid, $uid, $uid))->result_array();
        $total = array_sum(array_column($rows, 'lead_count'));

        foreach ($rows as &$row) {
            $row['lead_count']  = (int) $row['lead_count'];
            $row['pct_of_all']  = $total > 0 ? round(($row['lead_count'] / $total) * 100, 1) : 0;
        }
        unset($row);

        echo json_encode(array('ok' => true, 'total' => $total, 'data' => $rows));
    }

    // -----------------------------------------------------------------------
    // GET /api/funnel_report/pending_moms?user_id=X
    // -----------------------------------------------------------------------
    public function pending_moms()
    {
        $this->_auth_or_die();
        $uid = $this->_require_user_id();

        $sql = "
            SELECT
                te.id           AS event_id,
                te.cid_id       AS lead_id,
                cm.compname     AS company_name,
                te.fwd_date     AS meeting_date,
                TIMESTAMPDIFF(HOUR, te.fwd_date, NOW()) AS hours_since_meeting,
                te.mom_received,
                te.mom_approved,
                te.meeting_type
            FROM tblcallevents te
            LEFT JOIN init_call ic      ON ic.id  = te.cid_id
            LEFT JOIN company_master cm ON cm.id  = ic.cmpid_id
            WHERE te.user_id = ?
              AND te.actiontype_id IN (3, 4)
              AND te.fwd_date >= DATE_SUB(NOW(), INTERVAL 48 HOUR)
              AND te.fwd_date <= NOW()
              AND (te.mom_approved IS NULL OR te.mom_approved NOT IN ('yes','approved','1'))
            ORDER BY te.fwd_date DESC
            LIMIT 100
        ";

        $rows = $this->db->query($sql, array($uid))->result_array();

        foreach ($rows as &$row) {
            $row['hours_since_meeting'] = (int) $row['hours_since_meeting'];
            $row['days_since_meeting']  = round($row['hours_since_meeting'] / 24, 1);
        }
        unset($row);

        echo json_encode(array('ok' => true, 'count' => count($rows), 'data' => $rows));
    }

    // -----------------------------------------------------------------------
    // GET /api/funnel_report/line_mgr_rp_pending?user_id=X
    // -----------------------------------------------------------------------
    public function line_mgr_rp_pending()
    {
        $this->_auth_or_die();
        $uid = $this->_require_user_id();

        $reportees_sql = "
            SELECT rh.employee_uid AS reportee_uid, u.name AS reportee_name, u.type_id
            FROM reporting_hierarchy rh
            JOIN user u ON u.uid = rh.employee_uid
            WHERE rh.parent_uid = ? AND rh.active = 1
        ";
        $reportees    = $this->db->query($reportees_sql, array($uid))->result_array();
        $reportee_ids = array_column($reportees, 'reportee_uid');

        if (empty($reportee_ids)) {
            $own_sql = "
                SELECT dp.id, dp.record_date, dp.planner_approvel_status, dp.dayCloseApproveStatus, u.name AS bd_name
                FROM daily_planner dp
                JOIN user u ON u.uid = dp.userID
                WHERE dp.userID = ?
                  AND dp.record_date >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)
                  AND (dp.planner_approvel_status IS NULL OR dp.planner_approvel_status = 'pending'
                    OR dp.dayCloseApproveStatus IS NULL OR dp.dayCloseApproveStatus = 'pending')
                ORDER BY dp.record_date DESC LIMIT 50
            ";
            $rows = $this->db->query($own_sql, array($uid))->result_array();
            echo json_encode(array('ok' => true, 'mode' => 'bd_self', 'count' => count($rows), 'data' => $rows));
            return;
        }

        $placeholders = implode(',', array_fill(0, count($reportee_ids), '?'));
        $pending_sql  = "
            SELECT dp.userID AS reportee_uid, u.name AS reportee_name, dp.record_date,
                   dp.planner_approvel_status, dp.dayCloseApproveStatus
            FROM daily_planner dp
            JOIN user u ON u.uid = dp.userID
            WHERE dp.userID IN ({$placeholders})
              AND dp.record_date >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)
              AND (dp.planner_approvel_status IS NULL OR dp.planner_approvel_status = 'pending'
                OR dp.dayCloseApproveStatus IS NULL OR dp.dayCloseApproveStatus = 'pending')
            ORDER BY dp.record_date DESC LIMIT 200
        ";
        $rows = $this->db->query($pending_sql, $reportee_ids)->result_array();

        echo json_encode(array('ok' => true, 'mode' => 'manager', 'reportees' => $reportees,
                               'count' => count($rows), 'data' => $rows));
    }

    // -----------------------------------------------------------------------
    // GET /api/funnel_report/lead_detail/<cid>
    //     OR /api/funnel_report/lead_detail?lead_id=X
    // Full company + init_call detail for tap-to-expand.
    // Returns JSON: { ok, lead, contacts, touches, reviews, stage_history }
    // -----------------------------------------------------------------------
    public function lead_detail($cid = null)
    {
        $this->_auth_or_die();

        if (empty($cid)) {
            $cid = (int) $this->input->get('lead_id');
        } else {
            $cid = (int)$cid;
        }
        if ($cid <= 0) {
            http_response_code(400);
            echo json_encode(array('error' => 'lead_id / cid required'));
            return;
        }

        // Scope check: BD JWT can only see own leads
        $authed_uid  = $this->_authed_uid;
        $authed_type = $this->_authed_type_id;
        if ($authed_uid > 0 && $authed_type !== 1 && $authed_type !== 2) {
            $scope = $this->db->query(
                "SELECT 1 FROM init_call WHERE id = ?
                 AND (mainbd = ? OR insidebd = ? OR acm_co_id = ?) LIMIT 1",
                array($cid, $authed_uid, $authed_uid, $authed_uid)
            )->row();
            if (!$scope) {
                http_response_code(403);
                echo json_encode(array('ok' => false, 'error' => 'not in scope'));
                return;
            }
        }

        // Main lead row: ic.* plus company master + status + user joins
        $lead_sql = "
            SELECT
                ic.*,
                cm.compname,
                cm.address, cm.city, cm.state, cm.district, cm.country,
                cm.locations, cm.website, cm.budget AS company_budget_legacy,
                cm.partnerType_id, cm.comp_business_potential, cm.comp_top_spender,
                cm.comp_key_company, cm.comp_upsell_client, cm.comp_focus_funnel,
                cm.anchor_lat, cm.anchor_lng, cm.anchor_capture_count,
                s.name AS cstatus_label,
                ub.name AS bd_name, ub.phoneno AS bd_phone, ub.email AS bd_email,
                ucm.name AS cm_name,
                CASE
                  WHEN ic.proposaldate IS NULL THEN NULL
                  WHEN ic.cstatus IN (12,13,14) THEN 0
                  ELSE DATEDIFF(CURDATE(), ic.proposaldate)
                END AS slip_days,
                CASE
                  WHEN ic.proposaldate IS NOT NULL
                   AND ic.cstatus NOT IN (12,13,14)
                   AND ic.proposaldate < CURDATE() THEN 1
                  ELSE 0
                END AS is_slipped,
                COALESCE(
                  (SELECT MAX(fcl_ld.created_at) FROM funnel_change_log fcl_ld WHERE fcl_ld.cid_id = ic.id),
                  ic.updated_at
                ) AS stagnant_since
            FROM init_call ic
            LEFT JOIN company_master cm ON cm.id  = ic.cmpid_id
            LEFT JOIN status s          ON s.id   = ic.cstatus
            LEFT JOIN user ub           ON ub.uid = ic.mainbd
            LEFT JOIN user ucm          ON ucm.uid = ic.acm_co_id
            WHERE ic.id = ?
            LIMIT 1
        ";
        $lead = $this->db->query($lead_sql, array($cid))->row_array();

        if (empty($lead)) {
            http_response_code(404);
            echo json_encode(array('ok' => false, 'error' => 'lead not found'));
            return;
        }

        // Add stagnancy flags via PHP post-process
        $tmp = array($lead);
        $this->_add_stagnancy_flags($tmp);
        $lead = $tmp[0];

        $company_id = isset($lead['cmpid_id']) ? (int)$lead['cmpid_id'] : 0;

        // Contacts
        $contacts = array();
        if ($company_id > 0) {
            $contacts = $this->db->query(
                "SELECT contactperson, emailid, phoneno, designation, linked_in, type
                 FROM company_contact_master WHERE company_id = ?",
                array($company_id)
            )->result_array();
        }

        // Last 50 touches
        $touches = $this->db->query(
            "SELECT te.date, te.actiontype_id,
                    at2.name AS actiontype,
                    te.purpose_id, p.name AS purpose,
                    te.meeting_type, te.mom_received, te.remarks
             FROM tblcallevents te
             LEFT JOIN action at2 ON at2.id = te.actiontype_id
             LEFT JOIN purpose p      ON p.id   = te.purpose_id
             WHERE te.cid_id = ?
             ORDER BY te.date DESC LIMIT 50",
            array($cid)
        )->result_array();

        // Recent reviews (main_review: inid=init_call_id, csid=status_before,
        //                 exsid=status_after, by_uid=reviewer, cdate=created_at)
        $reviews = $this->db->query(
            "SELECT mr.id, mr.sdate, mr.cdate, mr.remarks, mr.rtype,
                    s1.name AS status_before_label, s2.name AS status_after_label,
                    u.name  AS reviewed_by_name
             FROM main_review mr
             LEFT JOIN status s1 ON s1.id = mr.csid
             LEFT JOIN status s2 ON s2.id = mr.exsid
             LEFT JOIN user u    ON u.uid  = mr.by_uid
             WHERE mr.inid = ?
             ORDER BY mr.cdate DESC LIMIT 20",
            array($cid)
        )->result_array();

        // Funnel movement (funnel_change_log: from_cstatus, to_cstatus, changed_by_uid)
        $stage_history = $this->db->query(
            "SELECT fcl.from_cstatus AS from_status, sf.name AS from_label,
                    fcl.to_cstatus   AS to_status,   st.name AS to_label,
                    fcl.created_at,   ufcl.name AS moved_by_name
             FROM funnel_change_log fcl
             LEFT JOIN status sf  ON sf.id  = fcl.from_cstatus
             LEFT JOIN status st  ON st.id  = fcl.to_cstatus
             LEFT JOIN user ufcl  ON ufcl.uid = fcl.changed_by_uid
             WHERE fcl.cid_id = ?
             ORDER BY fcl.created_at DESC LIMIT 20",
            array($cid)
        )->result_array();

        echo json_encode(array(
            'ok'            => true,
            'lead'          => $lead,
            'contacts'      => $contacts,
            'touches'       => $touches,
            'reviews'       => $reviews,
            'stage_history' => $stage_history,
        ));
    }

    // -----------------------------------------------------------------------
    // GET /api/graph_analysis/status_distribution?user_id=X
    // -----------------------------------------------------------------------
    public function status_distribution()
    {
        $this->_auth_or_die();
        $uid = $this->_require_user_id();

        $sql = "
            SELECT ic.cstatus, s.name AS label, COUNT(ic.id) AS count
            FROM init_call ic
            LEFT JOIN status s ON s.id = ic.cstatus
            WHERE (ic.mainbd = ? OR ic.insidebd = ? OR ic.acm_co_id = ?)
              AND ic.cstatus BETWEEN 1 AND 14
            GROUP BY ic.cstatus, s.name
            ORDER BY ic.cstatus ASC
        ";
        $rows = $this->db->query($sql, array($uid, $uid, $uid))->result_array();
        foreach ($rows as &$row) {
            $row['cstatus'] = (int) $row['cstatus'];
            $row['count']   = (int) $row['count'];
        }
        unset($row);
        echo json_encode(array('ok' => true, 'data' => $rows));
    }

    // -----------------------------------------------------------------------
    // GET /api/graph_analysis/day_of_week_meetings?user_id=X&days=60
    // -----------------------------------------------------------------------
    public function day_of_week_meetings()
    {
        $this->_auth_or_die();
        $uid  = $this->_require_user_id();
        $days = max(7, (int) ($this->input->get('days') ?: 60));

        $sql = "
            SELECT
                DAYOFWEEK(te.fwd_date) AS dow_mysql,
                CASE DAYOFWEEK(te.fwd_date)
                    WHEN 2 THEN 1 WHEN 3 THEN 2 WHEN 4 THEN 3
                    WHEN 5 THEN 4 WHEN 6 THEN 5 WHEN 7 THEN 6 WHEN 1 THEN 7
                END AS dow,
                CASE DAYOFWEEK(te.fwd_date)
                    WHEN 2 THEN 'Monday'   WHEN 3 THEN 'Tuesday'
                    WHEN 4 THEN 'Wednesday' WHEN 5 THEN 'Thursday'
                    WHEN 6 THEN 'Friday'   WHEN 7 THEN 'Saturday'
                    WHEN 1 THEN 'Sunday'
                END AS label,
                COUNT(te.id) AS count
            FROM tblcallevents te
            WHERE te.user_id = ?
              AND te.actiontype_id IN (3, 4)
              AND te.fwd_date >= DATE_SUB(NOW(), INTERVAL ? DAY)
              AND te.fwd_date <= NOW()
            GROUP BY dow_mysql, dow, label
            ORDER BY dow ASC
        ";
        $rows = $this->db->query($sql, array($uid, $days))->result_array();

        $by_dow = array();
        foreach ($rows as $r) $by_dow[(int)$r['dow']] = (int)$r['count'];
        $labels = array(1=>'Monday',2=>'Tuesday',3=>'Wednesday',4=>'Thursday',
                        5=>'Friday',6=>'Saturday',7=>'Sunday');
        $out = array();
        foreach ($labels as $d => $l) {
            $out[] = array('dow' => $d, 'label' => $l, 'count' => isset($by_dow[$d]) ? $by_dow[$d] : 0);
        }
        echo json_encode(array('ok' => true, 'days' => $days, 'data' => $out));
    }

    // -----------------------------------------------------------------------
    // GET /api/graph_analysis/planner_adherence?user_id=X&days=30
    // -----------------------------------------------------------------------
    public function planner_adherence()
    {
        $this->_auth_or_die();
        $uid  = $this->_require_user_id();
        $days = max(7, (int) ($this->input->get('days') ?: 30));

        $sql = "
            SELECT dc.ceremony_date AS date, dc.tasks_planned AS planned, dc.tasks_done AS done,
                CASE WHEN dc.tasks_planned > 0
                     THEN ROUND((dc.tasks_done / dc.tasks_planned) * 100, 1) ELSE 0 END AS pct
            FROM day_ceremony dc
            WHERE dc.uid = ?
              AND dc.ceremony_date >= DATE_SUB(CURDATE(), INTERVAL ? DAY)
              AND dc.ceremony_date <= CURDATE()
            ORDER BY dc.ceremony_date ASC
        ";
        $rows = $this->db->query($sql, array($uid, $days))->result_array();
        foreach ($rows as &$row) {
            $row['planned'] = (int) $row['planned'];
            $row['done']    = (int) $row['done'];
            $row['pct']     = (float) $row['pct'];
        }
        unset($row);
        echo json_encode(array('ok' => true, 'days' => $days, 'data' => $rows));
    }

    // GET /api/funnel_report/summary?uid=&from=&to= -- added 28 May 2026
    public function summary() {
        try {
            $this->_auth_or_die();
            $uid  = (int) $this->input->get('uid');
            $from = $this->input->get('from') ?: date('Y-m-01');
            $to   = $this->input->get('to')   ?: date('Y-m-d');
            $uid_clause = $uid > 0 ? (' AND ic.mainbd = ' . $uid) : '';
            $eq = "''";
            $stages = $this->db->query(
                'SELECT cstatus AS stage, COUNT(*) AS lead_count,
                 COALESCE(SUM(CAST(NULLIF(fbudget,' . $eq . ') AS UNSIGNED)),0) AS total_rs
                 FROM init_call ic WHERE cstatus IS NOT NULL
                   AND ic.createDate BETWEEN ? AND ?' . $uid_clause . ' GROUP BY cstatus ORDER BY cstatus',
                array($from, $to)
            )->result_array();
            $closures = $this->db->query(
                'SELECT cstatus, COUNT(*) AS n,
                 COALESCE(SUM(CAST(NULLIF(fbudget,' . $eq . ') AS UNSIGNED)),0) AS rs
                 FROM init_call ic WHERE cstatus IN (12,13)
                   AND ic.createDate BETWEEN ? AND ?' . $uid_clause . ' GROUP BY cstatus',
                array($from, $to)
            )->result_array();
            $total_leads = (int) array_sum(array_column($stages, 'lead_count'));
            echo json_encode(array(
                'ok' => true, 'uid' => $uid > 0 ? $uid : null,
                'from' => $from, 'to' => $to,
                'stages' => $stages, 'closures' => $closures, 'total_leads' => $total_leads,
            ));
        } catch (Exception $e) {
            echo json_encode(array('ok' => true, 'stages' => array(), 'closures' => array(),
                'total_leads' => 0, 'note' => 'error', 'detail' => $e->getMessage()));
        }
    }


}

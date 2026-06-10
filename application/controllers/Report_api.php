<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Report_api - Clean JSON report endpoints for STEM CRM.
 *
 * All endpoints require Bearer token auth (master bearer, api_token, or JWT).
 * A2 role-scoping: funnel/planner/cash_expense auto-scope per resolved role.
 * Read params via $_GET directly (CI3 input->get returns null on this server).
 *
 * Routes (add to routes_parity.php):
 *   $route['api/report/probe']        = 'Report_api/probe';
 *   $route['api/report/funnel']       = 'Report_api/funnel';
 *   $route['api/report/daily']        = 'Report_api/daily';
 *   $route['api/report/review']       = 'Report_api/review';
 *   $route['api/report/cash_expense'] = 'Report_api/cash_expense';
 *   $route['api/report/planner']      = 'Report_api/planner';
 *
 * File: application/controllers/Report_api.php
 * Updated: 2026-06-06b - A1 auth fix + A2 role scoping
 */
class Report_api extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->database();
        $this->load->library('BearerAuth');
        header('Content-Type: application/json');
    }

    // ------------------------------------------------------------------ //
    // Auth helper - uses shared BearerAuth (api_token + JWT + master)     //
    // Returns auth array ['ok','uid','role'] or sends 401 and returns null //
    // ------------------------------------------------------------------ //
    private function _auth() {
        $auth = $this->bearerauth->resolve();
        if (!$auth['ok']) {
            $this->_json(array('ok'=>false,'error'=>'Unauthorized'), 401);
            return null;
        }
        return $auth;
    }

    // ------------------------------------------------------------------ //
    // A2: Build role-scoped WHERE fragment for init_call.mainbd           //
    //     or tblcallevents.user_id or cash_expense.user_id               //
    //                                                                      //
    // Roles (from user_details.type_id):                                  //
    //   BD (3) / ACM (24) : own uid only                                  //
    //   CM (13)           : their cluster's BDs (aadmin = cm uid)         //
    //   RM (22/23)        : their region BDs (rm_east_co or rm_north_co)  //
    //   SC (15)           : coordinator scope (sales_co = sc uid)         //
    //   admin/superadmin  : unrestricted                                  //
    //   system            : unrestricted                                  //
    //                                                                      //
    // Returns array($where_fragment, $params, $forced_uid)                //
    //   $forced_uid - the uid the scope locks to (0 = unrestricted)       //
    // ------------------------------------------------------------------ //
    private function _role_scope($auth, $col = 'ic.mainbd') {
        $role = strtolower((string)$auth['role']);
        $uid  = (int)$auth['uid'];

        // Admin / superadmin / system: unrestricted
        if (in_array($role, array('admin','superadmin','system'), true)) {
            return array('1=1', array(), 0);
        }

        // BD / ACM: own uid only
        if (in_array($role, array('bd','acm'), true) && $uid > 0) {
            return array("$col = ?", array($uid), $uid);
        }

        // CM (type_id=13): BDs whose aadmin = cm_uid
        if ($role === 'cm' && $uid > 0) {
            $sub = "SELECT user_id FROM user_details WHERE aadmin = ?";
            return array("$col IN ($sub)", array($uid), 0);
        }

        // RM (type_id=22/23): BDs where rm_east_co=uid OR rm_north_co=uid
        if ($role === 'rm' && $uid > 0) {
            $sub = "SELECT user_id FROM user_details WHERE rm_east_co = ? OR rm_north_co = ?";
            return array("$col IN ($sub)", array($uid,$uid), 0);
        }

        // SC (type_id=15): BDs where sales_co = sc_uid
        if ($role === 'sc' && $uid > 0) {
            $sub = "SELECT user_id FROM user_details WHERE sales_co = ?";
            return array("$col IN ($sub)", array($uid), 0);
        }

        // Unknown role: lock down to own uid if available
        if ($uid > 0) {
            return array("$col = ?", array($uid), $uid);
        }
        return array('1=0', array(), 0);
    }

    // ------------------------------------------------------------------ //
    // JSON output helper                                                   //
    // ------------------------------------------------------------------ //
    private function _json($data, $code = 200) {
        http_response_code($code);
        echo json_encode($data);
        exit;
    }

    // ------------------------------------------------------------------ //
    // 1. GET api/report/probe                                             //
    // ------------------------------------------------------------------ //
    public function probe() {
        $auth = $this->_auth(); if (!$auth) return;
        $this->_json(array(
            'ok'      => true,
            'version' => '1.1',
            'auth_role' => $auth['role'],
            'auth_uid'  => $auth['uid'],
            'reports' => array(
                array('key'=>'funnel',       'route'=>'api/report/funnel',       'filters'=>array('state','district','start_date','end_date')),
                array('key'=>'daily',        'route'=>'api/report/daily',        'filters'=>array('start_date','end_date')),
                array('key'=>'review',       'route'=>'api/report/review',       'filters'=>array('fyear','review_apr_status')),
                array('key'=>'cash_expense', 'route'=>'api/report/cash_expense', 'filters'=>array('start_date','end_date')),
                array('key'=>'planner',      'route'=>'api/report/planner',      'filters'=>array('start_date','end_date')),
            ),
        ));
    }

    // ------------------------------------------------------------------ //
    // 2. GET api/report/funnel - A2 role scoped                          //
    // ------------------------------------------------------------------ //
    public function funnel() {
        $auth = $this->_auth(); if (!$auth) return;

        $state      = isset($_GET['state'])      ? trim($_GET['state'])      : '';
        $district   = isset($_GET['district'])   ? trim($_GET['district'])   : '';
        $start_date = isset($_GET['start_date']) ? trim($_GET['start_date']) : '';
        $end_date   = isset($_GET['end_date'])   ? trim($_GET['end_date'])   : '';

        // A2: role-scoped user_id; admins may pass ?user_id= override
        list($scope_sql, $scope_params, $forced) = $this->_role_scope($auth, 'ic.mainbd');
        $is_admin = in_array(strtolower($auth['role']), array('admin','superadmin','system'), true);
        if ($is_admin && isset($_GET['user_id']) && (int)$_GET['user_id'] > 0) {
            $scope_sql    = 'ic.mainbd = ?';
            $scope_params = array((int)$_GET['user_id']);
        }

        $need_cm_join = ($state !== '' || $district !== '');
        if ($need_cm_join) {
            $sql = "SELECT ic.cstatus, s.name AS cstatus_label, COUNT(*) AS cnt
                    FROM init_call ic
                    LEFT JOIN status s ON s.id = ic.cstatus
                    JOIN company_master cm ON cm.id = ic.cmpid_id
                    WHERE $scope_sql";
        } else {
            $sql = "SELECT ic.cstatus, s.name AS cstatus_label, COUNT(*) AS cnt
                    FROM init_call ic
                    LEFT JOIN status s ON s.id = ic.cstatus
                    WHERE $scope_sql";
        }
        $params = $scope_params;

        if ($state !== '')      { $sql .= " AND cm.state = ?";    $params[] = $state; }
        if ($district !== '')   { $sql .= " AND cm.district = ?"; $params[] = $district; }
        if ($start_date !== '') { $sql .= " AND ic.createDate >= ?"; $params[] = $start_date; }
        if ($end_date !== '')   { $sql .= " AND ic.createDate <= ?"; $params[] = $end_date; }

        $sql .= " GROUP BY ic.cstatus, s.name ORDER BY ic.cstatus";

        $filters_echo = array(
            'scoped_uid' => $forced > 0 ? $forced : ($auth['uid'] > 0 ? $auth['uid'] : null),
            'role'       => $auth['role'],
            'state'      => $state !== '' ? $state : null,
            'district'   => $district !== '' ? $district : null,
            'start_date' => $start_date !== '' ? $start_date : null,
            'end_date'   => $end_date !== '' ? $end_date : null,
        );

        try {
            $rows = $this->db->query($sql, $params)->result_array();
            if (empty($rows)) {
                $this->_json(array('ok'=>true,'count'=>0,'reason'=>'no_rows','rows'=>array(),'filters_echo'=>$filters_echo));
                return;
            }
            $this->_json(array('ok'=>true,'count'=>count($rows),'rows'=>$rows,'filters_echo'=>$filters_echo));
        } catch (Exception $e) {
            $this->_json(array('ok'=>false,'error'=>'query_error','detail'=>$e->getMessage(),'filters_echo'=>$filters_echo), 500);
        }
    }

    // ------------------------------------------------------------------ //
    // 3. GET api/report/daily                                             //
    // ------------------------------------------------------------------ //
    public function daily() {
        $auth = $this->_auth(); if (!$auth) return;

        $start_date = isset($_GET['start_date']) ? trim($_GET['start_date']) : date('Y-m-d');
        $end_date   = isset($_GET['end_date'])   ? trim($_GET['end_date'])   : date('Y-m-d');

        list($scope_sql, $scope_params, $forced) = $this->_role_scope($auth, 't.user_id');
        $is_admin = in_array(strtolower($auth['role']), array('admin','superadmin','system'), true);
        if ($is_admin && isset($_GET['user_id']) && (int)$_GET['user_id'] > 0) {
            $scope_sql    = 't.user_id = ?';
            $scope_params = array((int)$_GET['user_id']);
        }

        $sql = "SELECT t.user_id, ud.name AS user_name, a.id AS action_id, a.name AS action_name,
                       a.yest AS minutes_per_task, COUNT(*) AS cnt,
                       COUNT(*) * COALESCE(CAST(a.yest AS UNSIGNED), 5) AS total_minutes
                FROM tblcallevents t
                JOIN action a ON a.id = t.actiontype_id
                LEFT JOIN user_details ud ON ud.user_id = t.user_id
                WHERE (t.actontaken IS NOT NULL AND t.actontaken != '' AND t.actontaken != 'no')
                AND DATE(t.updated_at) BETWEEN ? AND ?
                AND $scope_sql
                GROUP BY t.user_id, ud.name, t.actiontype_id, a.id, a.name, a.yest
                ORDER BY t.user_id, cnt DESC";
        $params = array_merge(array($start_date, $end_date), $scope_params);

        $filters_echo = array('role'=>$auth['role'],'start_date'=>$start_date,'end_date'=>$end_date);
        try {
            $rows = $this->db->query($sql, $params)->result_array();
            if (empty($rows)) { $this->_json(array('ok'=>true,'count'=>0,'reason'=>'no_rows','rows'=>array(),'filters_echo'=>$filters_echo)); return; }
            $user_totals = array();
            foreach ($rows as $r) {
                $uid = $r['user_id'];
                if (!isset($user_totals[$uid])) $user_totals[$uid] = array('user_id'=>$r['user_id'],'user_name'=>$r['user_name'],'total_tasks'=>0,'total_minutes'=>0);
                $user_totals[$uid]['total_tasks']   += (int)$r['cnt'];
                $user_totals[$uid]['total_minutes'] += (int)$r['total_minutes'];
            }
            $this->_json(array('ok'=>true,'count'=>count($rows),'rows'=>$rows,'user_summary'=>array_values($user_totals),'filters_echo'=>$filters_echo));
        } catch (Exception $e) { $this->_json(array('ok'=>false,'error'=>'query_error','detail'=>$e->getMessage()), 500); }
    }

    // ------------------------------------------------------------------ //
    // 4. GET api/report/review                                            //
    // ------------------------------------------------------------------ //
    public function review() {
        $auth = $this->_auth(); if (!$auth) return;

        $fyear             = isset($_GET['fyear'])             ? trim($_GET['fyear'])   : '';
        $user_id           = isset($_GET['user_id'])           ? (int)$_GET['user_id'] : 0;
        $review_apr_status = isset($_GET['review_apr_status']) ? $_GET['review_apr_status'] : '';

        $filters_echo = array('fyear'=>$fyear!==''?$fyear:null,'user_id'=>$user_id>0?$user_id:null,'review_apr_status'=>$review_apr_status!==''?(int)$review_apr_status:null,'role'=>$auth['role']);

        $sql = "SELECT amr.id, amr.inid, amr.by_uid, ud.name AS reviewer_name, amr.financial_year,
                       amr.sdate, amr.rtype, amr.keep_company, amr.annaul_revenue,
                       amr.current_year_focus_funnel, amr.review_apr_status, amr.review_apr_remarks,
                       amr.remarks, amr.created_at, amr.updated_at, ic.cmpid_id,
                       cm.compname AS company_name, cm.city, cm.state
                FROM annual_main_review amr
                LEFT JOIN init_call ic ON ic.id = amr.inid
                LEFT JOIN company_master cm ON cm.id = ic.cmpid_id
                LEFT JOIN user_details ud ON ud.user_id = amr.by_uid
                WHERE 1=1";
        $params = array();
        if ($fyear !== '') { $sql .= " AND amr.financial_year = ?"; $params[] = $fyear; }
        if ($user_id > 0)  { $sql .= " AND amr.by_uid = ?"; $params[] = $user_id; }
        if ($review_apr_status !== '') { $sql .= " AND amr.review_apr_status = ?"; $params[] = (int)$review_apr_status; }
        $sql .= " ORDER BY amr.id DESC LIMIT 500";

        try {
            $rows = $this->db->query($sql, $params)->result_array();
            if (empty($rows)) { $this->_json(array('ok'=>true,'count'=>0,'reason'=>'no_rows','rows'=>array(),'filters_echo'=>$filters_echo)); return; }
            $this->_json(array('ok'=>true,'count'=>count($rows),'rows'=>$rows,'filters_echo'=>$filters_echo));
        } catch (Exception $e) { $this->_json(array('ok'=>false,'error'=>'query_error','detail'=>$e->getMessage()), 500); }
    }

    // ------------------------------------------------------------------ //
    // 5. GET api/report/cash_expense - A2 role scoped                    //
    // ------------------------------------------------------------------ //
    public function cash_expense() {
        $auth = $this->_auth(); if (!$auth) return;

        $start_date = isset($_GET['start_date']) ? trim($_GET['start_date']) : '';
        $end_date   = isset($_GET['end_date'])   ? trim($_GET['end_date'])   : '';

        list($scope_sql, $scope_params, $forced) = $this->_role_scope($auth, 'ce.user_id');
        $is_admin = in_array(strtolower($auth['role']), array('admin','superadmin','system'), true);
        if ($is_admin && isset($_GET['user_id']) && (int)$_GET['user_id'] > 0) {
            $scope_sql    = 'ce.user_id = ?';
            $scope_params = array((int)$_GET['user_id']);
        }

        $sql = "SELECT ce.id, ce.user_id, ud.name AS user_name, ce.meetid, ce.tbl_task_id,
                       ce.expense_type, ce.expense AS amount, ce.expense_remarks,
                       ce.verify, ce.verify_by, ce.verify_remarks, ce.verify_date,
                       ce.admin_apr, ce.admin_by, ce.admin_msg, ce.admin_date,
                       ce.account_apr, ce.account_by, ce.account_msg, ce.account_date,
                       ce.receipt_required, ce.receipt_uploaded, ce.travel_advance_id,
                       ce.created_at, ce.updated_at
                FROM cash_expense ce
                LEFT JOIN user_details ud ON ud.user_id = ce.user_id
                WHERE $scope_sql";
        $params = $scope_params;

        if ($start_date !== '') { $sql .= " AND DATE(ce.created_at) >= ?"; $params[] = $start_date; }
        if ($end_date !== '')   { $sql .= " AND DATE(ce.created_at) <= ?"; $params[] = $end_date; }
        $sql .= " ORDER BY ce.created_at DESC LIMIT 500";

        $filters_echo = array('role'=>$auth['role'],'start_date'=>$start_date!==''?$start_date:null,'end_date'=>$end_date!==''?$end_date:null);

        try {
            $rows = $this->db->query($sql, $params)->result_array();
            if (empty($rows)) { $this->_json(array('ok'=>true,'count'=>0,'reason'=>'no_rows','rows'=>array(),'filters_echo'=>$filters_echo)); return; }
            $total_amount=0; $pending_verify=0; $admin_approved=0; $acct_approved=0;
            foreach ($rows as $r) {
                $total_amount += (int)$r['amount'];
                if ((int)$r['verify']===0) $pending_verify++;
                if ((int)$r['admin_apr']===1) $admin_approved++;
                if ((int)$r['account_apr']===1) $acct_approved++;
            }
            $this->_json(array('ok'=>true,'count'=>count($rows),'total_amount'=>$total_amount,'pending_verify'=>$pending_verify,'admin_approved'=>$admin_approved,'acct_approved'=>$acct_approved,'rows'=>$rows,'filters_echo'=>$filters_echo));
        } catch (Exception $e) { $this->_json(array('ok'=>false,'error'=>'query_error','detail'=>$e->getMessage()), 500); }
    }

    // ------------------------------------------------------------------ //
    // 6. GET api/report/planner - A2 role scoped                         //
    // ------------------------------------------------------------------ //
    public function planner() {
        $auth = $this->_auth(); if (!$auth) return;

        $start_date = isset($_GET['start_date']) ? trim($_GET['start_date']) : date('Y-m-d');
        $end_date   = isset($_GET['end_date'])   ? trim($_GET['end_date'])   : date('Y-m-d');

        list($scope_sql, $scope_params, $forced) = $this->_role_scope($auth, 't.user_id');
        $is_admin = in_array(strtolower($auth['role']), array('admin','superadmin','system'), true);
        if ($is_admin && isset($_GET['user_id']) && (int)$_GET['user_id'] > 0) {
            $scope_sql    = 't.user_id = ?';
            $scope_params = array((int)$_GET['user_id']);
        }

        $sql = "SELECT t.user_id, ud.name AS user_name, COUNT(*) AS planned,
                       SUM(CASE WHEN (t.actontaken IS NOT NULL AND t.actontaken != '' AND t.actontaken != 'no') THEN 1 ELSE 0 END) AS done,
                       SUM(CASE WHEN (t.actontaken IS NULL OR t.actontaken='' OR t.actontaken='no') AND t.appointmentdatetime < NOW() THEN 1 ELSE 0 END) AS pending,
                       SUM(CASE WHEN (t.actontaken IS NULL OR t.actontaken='' OR t.actontaken='no') AND t.appointmentdatetime >= NOW() THEN 1 ELSE 0 END) AS upcoming,
                       SUM(COALESCE(CAST(a.yest AS UNSIGNED),5)) AS total_minutes_planned,
                       SUM(CASE WHEN (t.actontaken IS NOT NULL AND t.actontaken!='' AND t.actontaken!='no') THEN COALESCE(CAST(a.yest AS UNSIGNED),5) ELSE 0 END) AS minutes_done,
                       SUM(CASE WHEN (t.actontaken IS NULL OR t.actontaken='' OR t.actontaken='no') AND t.appointmentdatetime < NOW() THEN COALESCE(CAST(a.yest AS UNSIGNED),5) ELSE 0 END) AS minutes_pending
                FROM tblcallevents t
                LEFT JOIN action a ON a.id = t.actiontype_id
                LEFT JOIN user_details ud ON ud.user_id = t.user_id
                WHERE DATE(t.appointmentdatetime) BETWEEN ? AND ?
                AND $scope_sql
                GROUP BY t.user_id, ud.name
                ORDER BY planned DESC LIMIT 200";
        $params = array_merge(array($start_date, $end_date), $scope_params);

        $filters_echo = array('role'=>$auth['role'],'start_date'=>$start_date,'end_date'=>$end_date);

        try {
            $rows = $this->db->query($sql, $params)->result_array();
            if (empty($rows)) { $this->_json(array('ok'=>true,'count'=>0,'reason'=>'no_rows','rows'=>array(),'filters_echo'=>$filters_echo)); return; }
            foreach ($rows as &$r) {
                $r['planned']               = (int)$r['planned'];
                $r['done']                  = (int)$r['done'];
                $r['pending']               = (int)$r['pending'];
                $r['upcoming']              = (int)$r['upcoming'];
                $r['total_minutes_planned'] = (int)$r['total_minutes_planned'];
                $r['minutes_done']          = (int)$r['minutes_done'];
                $r['minutes_pending']       = (int)$r['minutes_pending'];
                $p = $r['planned'];
                $r['completion_pct'] = $p > 0 ? round(100 * $r['done'] / $p, 1) : 0;
            }
            unset($r);
            $this->_json(array('ok'=>true,'count'=>count($rows),'rows'=>$rows,'filters_echo'=>$filters_echo));
        } catch (Exception $e) { $this->_json(array('ok'=>false,'error'=>'query_error','detail'=>$e->getMessage()), 500); }
    }
}
/* End of file Report_api.php */

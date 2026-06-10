<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * TaskPendingController
 *
 * Migration 075b — Agent L: Unified Task Pending endpoint.
 * Source of truth for the single merged counter Mobile will show.
 *
 * Auth: Bearer STEM_DIGEST_TOKEN header required on all endpoints except probe.
 *
 * Routes to add in application/config/routes_mobile_pilot.php:
 *   // === migration 075b unified task pending ===
 *   $route['api/task/pending_with_context'] = 'TaskPendingController/list';
 *   $route['api/task/pending_probe']         = 'TaskPendingController/probe';
 */
class TaskPendingController extends CI_Controller
{
    const MIGRATION = '075b';

    public function __construct()
    {
        parent::__construct();
        $this->load->database();
        $this->load->helper('url');
        $this->config->load('rest',   true, true);
        $this->config->load('custom', true, true);
        header('Content-Type: application/json; charset=utf-8');
    }

    // -----------------------------------------------------------------------
    // Auth guard — Bearer token or active session (mirrors FunnelReportController)
    // -----------------------------------------------------------------------
    private $_authed_uid = 0;
    private $_authed_type_id = 0;

    // ---- per-user JWT validator (matches Auth::api_login token generation) ----
    private function _jwt_token_valid($token)
    {
        if (empty($token)) return false;
        $secret = getenv('STEM_DIGEST_TOKEN') ?: '4eBaiAT7r4zu6OK3b8evjLNia1D7RGgb0qRTuLJfUSo';
        $days = array(
            date('Y-m-d'),
            date('Y-m-d', strtotime('-1 day')),
            date('Y-m-d', strtotime('+1 day'))
        );
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
                $this->_authed_uid      = $uid;
                $urow = $this->db->select('type_id')->from('user')->where('uid', $uid)->get()->row();
                if ($urow) $this->_authed_type_id = (int)$urow->type_id;
                return true;
            }
        }

        $session_uid = $this->session->userdata('user_id');
        if ((int)$session_uid > 0) return true;

        http_response_code(401);
        echo json_encode(array('error' => 'unauthorized', 'hdr_received' => !empty($hdr)));
        exit;
    }

    // -----------------------------------------------------------------------
    // probe — health check, no auth required
    // -----------------------------------------------------------------------
    public function probe()
    {
        echo json_encode(array(
            'ok'         => true,
            'controller' => 'TaskPendingController',
            'migration'  => '075b',
            'ts'         => date('Y-m-d H:i:s'),
        ));
    }

    // -----------------------------------------------------------------------
    // list — main endpoint: GET /api/task/pending_with_context
    //   ?mom_only=1         → filter to actiontype_id = 6 (Write MoM)
    //   ?days_overdue_min=N → only rows where days_overdue >= N (default 0)
    //   ?limit=N            → max rows returned (default 100)
    // -----------------------------------------------------------------------
    public function list()
    {
        $this->_auth_or_die();

        // --- params ---
        $mom_only         = (int)$this->input->get('mom_only');
        $days_overdue_min = max(0, (int)$this->input->get('days_overdue_min'));
        $limit            = max(1, min(1000, (int)$this->input->get('limit') ?: 100));

        // uid scoping: use JWT uid if set, else require user_id param
        $bd_uid = $this->_authed_uid;
        if ($bd_uid <= 0) {
            $bd_uid = (int)$this->input->get('user_id');
        }
        if ($bd_uid <= 0) {
            http_response_code(400);
            echo json_encode(array('ok' => false, 'error' => 'user_id required or send per-user JWT'));
            return;
        }

        try {
            // --- self-check: DB connectivity ---
            $ping = $this->db->query('SELECT 1 AS ping');
            if (!$ping) {
                echo json_encode(array('ok' => false, 'error' => 'db_ping_failed'));
                return;
            }

            // ---------------------------------------------------------------
            // Main SQL
            // Verified column names (29 May 2026):
            //   tblcallevents: fwd_date (followup), ntdate (cdate equiv)
            //   action.name, purpose.name, company_master.compname
            //   init_call.cmpid_id -> company_master.id
            //   user_details.id    -> init_call.mainbd
            //   status.name        -> cstatus label
            // ---------------------------------------------------------------
            $sql = "
                SELECT
                    ce.id                                                           AS task_id,
                    COALESCE(ce.fwd_date, ce.ntdate)                                AS due_at,
                    ce.actiontype_id,
                    a.name                                                          AS actiontype_name,
                    ce.purpose_id,
                    p.name                                                          AS purpose_name,
                    ic.cmpid_id                                                     AS cid,
                    cm.compname                                                     AS company_name,
                    ic.id                                                           AS init_call_id,
                    ic.cstatus,
                    s.name                                                          AS cstatus_label,
                    GREATEST(0, DATEDIFF(CURDATE(), DATE(COALESCE(ce.fwd_date, ce.ntdate)))) AS days_overdue,
                    CASE WHEN ce.actiontype_id = 6 THEN 1 ELSE 0 END               AS is_mom_pending,
                    ic.mainbd                                                       AS bd_uid,
                    ud.name                                                         AS bd_name
                FROM tblcallevents ce
                INNER JOIN init_call ic
                    ON ic.id = ce.cid_id
                LEFT JOIN company_master cm
                    ON cm.id = ic.cmpid_id
                LEFT JOIN status s
                    ON s.id = ic.cstatus
                LEFT JOIN action a
                    ON a.id = ce.actiontype_id
                LEFT JOIN purpose p
                    ON p.id = ce.purpose_id
                LEFT JOIN user_details ud
                    ON ud.id = ic.mainbd
                WHERE
                    (ce.actontaken IS NULL OR ce.actontaken = '' OR ce.actontaken = 'no')
                    AND COALESCE(ce.fwd_date, ce.ntdate) <= NOW()
                    AND ic.mainbd = ?
            ";

            $binds = array($bd_uid);

            if ($mom_only) {
                $sql .= " AND ce.actiontype_id = 6 ";
            }

            if ($days_overdue_min > 0) {
                $sql .= " AND GREATEST(0, DATEDIFF(CURDATE(), DATE(COALESCE(ce.fwd_date, ce.ntdate)))) >= ? ";
                $binds[] = $days_overdue_min;
            }

            $sql .= " ORDER BY days_overdue DESC LIMIT ? ";
            $binds[] = $limit;

            $result = $this->db->query($sql, $binds);

            if ($result === false) {
                echo json_encode(array(
                    'ok'    => false,
                    'error' => 'query_failed',
                    'db'    => $this->db->error(),
                ));
                return;
            }

            $rows = $result->result_array();

            // --- aggregate counts ---
            $mom_pending_count = 0;
            $overdue_count     = 0;
            foreach ($rows as $row) {
                if ((int)$row['is_mom_pending'] === 1) $mom_pending_count++;
                if ((int)$row['days_overdue']   >  0)  $overdue_count++;
            }

            echo json_encode(array(
                'ok'               => true,
                'rows'             => $rows,
                'total'            => count($rows),
                'mom_pending_count'=> $mom_pending_count,
                'overdue_count'    => $overdue_count,
                'bd_uid_scoped'    => $bd_uid,
                'migration'        => self::MIGRATION,
                'generated_at'     => date('Y-m-d H:i:s'),
            ));

        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(array(
                'ok'    => false,
                'error' => 'exception',
                'msg'   => $e->getMessage(),
            ));
        }
    }
}

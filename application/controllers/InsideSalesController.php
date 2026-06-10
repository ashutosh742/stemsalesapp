<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * InsideSalesController
 *
 * HTTP surface for GAP 9: Inside Sales queue + email task check.
 * Inside Sales BDs: user.type_id = 4 OR init_call.insidebd IS NOT NULL.
 *
 * Auth: Bearer STEM_DIGEST_TOKEN header required for protected endpoints.
 *
 * Routes to add in application/config/routes.php:
 *   $route['api/inside_sales/probe']['get']           = 'InsideSalesController/probe';
 *   $route['api/inside_sales/my_queue']['get']         = 'InsideSalesController/my_queue';
 *   $route['api/inside_sales/email_task_check']['get'] = 'InsideSalesController/email_task_check';
 *   $route['api/inside_sales/log_email']['post']       = 'InsideSalesController/log_email';
 */
class InsideSalesController extends CI_Controller
{
    const MIGRATION = 'GAP9';

    // Bearer token loaded from CI config (application/config/custom.php)
    private $bearer_token;

    // actiontype_id used when logging an inside-sales email event
    const EMAIL_ACTIONTYPE_ID = 10;
    // purpose_id: research path
    const EMAIL_PURPOSE_ID    = 94;
    // cstatus values treated as active for inside-sales queue
    const ACTIVE_CSTATUS      = array(1, 2, 3);
    // hours within which an email event counts as "done"
    const EMAIL_WINDOW_HOURS  = 48;

    public function __construct()
    {
        parent::__construct();
        $this->load->helper('url');
        $this->load->config('custom', TRUE);
        $config_token = $this->config->item('stem_digest_token', 'custom');
        $this->bearer_token = $config_token ?: getenv('STEM_DIGEST_TOKEN');
        header('Content-Type: application/json; charset=utf-8');
    }

    // -----------------------------------------------------------------------
    // Auth guard. Matches Authorization: Bearer against STEM_DIGEST_TOKEN env.
    // Falls back to an active CI session. Returns 401 + exits on failure.
    // -----------------------------------------------------------------------
    private $_authed_uid = 0;

    // ---- per-user JWT validator (added 28 May 2026) ----
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
            if (!isset($this->db) || !is_object($this->db)) { $this->load->database(); }
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
        $hdr      = $this->input->get_request_header('Authorization', true);
        $expected = $this->bearer_token;

        if (empty($expected)) {
            http_response_code(503);
            echo json_encode(array(
                'error'  => 'server_misconfiguration',
                'detail' => 'Bearer token not configured on this host',
            ));
            exit;
        }

        if (!empty($hdr) && hash_equals('Bearer ' . $expected, $hdr)) {
            return true;
        }
        // Per-user JWT (added 28 May)
        if (!empty($hdr) && stripos($hdr, 'Bearer ') === 0) {
            $tok = trim(substr($hdr, 7));
            $uid = $this->_jwt_token_valid($tok);
            if ($uid) { $this->_authed_uid = $uid; return true; }
        }

        $session_uid = $this->session->userdata('user_id');
        if ((int) $session_uid > 0) {
            return true;
        }

        http_response_code(401);
        echo json_encode(array('error' => 'unauthorized'));
        exit;
    }

    // -----------------------------------------------------------------------
    // GET /api/inside_sales/probe
    // Health check. No auth required.
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
    // GET /api/inside_sales/my_queue?user_id=X
    // Returns init_call rows where insidebd=X AND cstatus IN (1,2,3).
    // Each row includes email_task_check_status: pending or done.
    // Fields: lead_id, company_name, cmpid_id, cstatus, last_event_at,
    //         days_idle, email_task_check_status
    // -----------------------------------------------------------------------
    public function my_queue()
    {
        $this->_auth_or_die();

        $user_id = (int) $this->input->get('user_id');
        if ($user_id <= 0) {
            http_response_code(400);
            echo json_encode(array('error' => 'user_id is required'));
            return;
        }

        $status_in = implode(',', array_map('intval', self::ACTIVE_CSTATUS));
        $window_ts = date('Y-m-d H:i:s', time() - (self::EMAIL_WINDOW_HOURS * 3600));

        // Fetch queue rows with last event timestamp
        $sql = "
            SELECT
                ic.id               AS lead_id,
                cm.compname         AS company_name,
                ic.cmpid_id,
                ic.cstatus,
                (
                    SELECT MAX(e.date)
                    FROM tblcallevents e
                    WHERE e.cid_id = ic.id
                ) AS last_event_at,
                DATEDIFF(NOW(),
                    COALESCE(
                        (SELECT MAX(e2.date) FROM tblcallevents e2 WHERE e2.cid_id = ic.id),
                        ic.created_at
                    )
                ) AS days_idle,
                (
                    SELECT COUNT(*)
                    FROM tblcallevents em
                    WHERE em.cid_id = ic.id
                      AND em.actiontype_id = " . (int) self::EMAIL_ACTIONTYPE_ID . "
                      AND em.purpose_id    = " . (int) self::EMAIL_PURPOSE_ID . "
                      AND em.date         >= '" . $this->db->escape_str($window_ts) . "'
                ) AS email_recent_count
            FROM init_call ic
            LEFT JOIN company_master cm ON cm.id = ic.cmpid_id
            WHERE ic.insidebd = " . $user_id . "
              AND ic.cstatus  IN (" . $status_in . ")
            ORDER BY days_idle DESC
        ";

        $query = $this->db->query($sql);
        if (!$query) {
            http_response_code(500);
            echo json_encode(array('error' => 'db_query_failed'));
            return;
        }

        $rows = array();
        foreach ($query->result_array() as $r) {
            $r['email_task_check_status'] = ((int) $r['email_recent_count'] > 0) ? 'done' : 'pending';
            unset($r['email_recent_count']);
            $rows[] = $r;
        }

        echo json_encode(array(
            'ok'       => true,
            'user_id'  => $user_id,
            'count'    => count($rows),
            'rows'     => $rows,
        ));
    }

    // -----------------------------------------------------------------------
    // GET /api/inside_sales/email_task_check?user_id=X&date=YYYY-MM-DD
    // Returns pending vs done counts of email tasks for the given day.
    // -----------------------------------------------------------------------
    public function email_task_check()
    {
        $this->_auth_or_die();

        $user_id = (int) $this->input->get('user_id');
        if ($user_id <= 0) {
            http_response_code(400);
            echo json_encode(array('error' => 'user_id is required'));
            return;
        }

        $date = $this->input->get('date');
        if (empty($date) || !preg_match('/^\d{4}-\d{2}-\d{2}$/', $date)) {
            $date = date('Y-m-d');
        }

        $date_esc = $this->db->escape_str($date);

        // Total leads in queue for this inside BD
        $status_in = implode(',', array_map('intval', self::ACTIVE_CSTATUS));
        $total_sql = "SELECT COUNT(*) AS total FROM init_call WHERE insidebd = $user_id AND cstatus IN ($status_in)";
        $total_q   = $this->db->query($total_sql);
        $total     = $total_q ? (int) $total_q->row_array()['total'] : 0;

        // Count of leads that had an email event on the requested day
        $done_sql = "
            SELECT COUNT(DISTINCT ic.id) AS done
            FROM init_call ic
            WHERE ic.insidebd = $user_id
              AND ic.cstatus IN ($status_in)
              AND EXISTS (
                  SELECT 1
                  FROM tblcallevents em
                  WHERE em.cid_id         = ic.id
                    AND em.actiontype_id  = " . (int) self::EMAIL_ACTIONTYPE_ID . "
                    AND em.purpose_id     = " . (int) self::EMAIL_PURPOSE_ID . "
                    AND DATE(em.date)    = '$date_esc'
              )
        ";
        $done_q = $this->db->query($done_sql);
        $done   = $done_q ? (int) $done_q->row_array()['done'] : 0;

        echo json_encode(array(
            'ok'      => true,
            'date'    => $date,
            'user_id' => $user_id,
            'total'   => $total,
            'done'    => $done,
            'pending' => max(0, $total - $done),
        ));
    }

    // -----------------------------------------------------------------------
    // POST /api/inside_sales/log_email
    // Body: lead_id, email_to, subject, body_snippet
    // Inserts into tblcallevents with actiontype_id=10, purpose_id=94.
    // Returns: {ok:true, event_id:N}
    // -----------------------------------------------------------------------
    public function log_email()
    {
        $this->_auth_or_die();

        if ($this->input->method(true) !== 'POST') {
            http_response_code(405);
            echo json_encode(array('error' => 'POST required'));
            return;
        }

        $lead_id      = (int) $this->input->post('lead_id');
        $email_to     = $this->input->post('email_to');
        $subject      = $this->input->post('subject');
        $body_snippet = $this->input->post('body_snippet');

        if ($lead_id <= 0) {
            http_response_code(400);
            echo json_encode(array('error' => 'lead_id is required'));
            return;
        }

        // Verify lead exists and get cmpid_id
        $lead_q = $this->db->query("SELECT id, cmpid_id, insidebd FROM init_call WHERE id = $lead_id LIMIT 1");
        if (!$lead_q || $lead_q->num_rows() === 0) {
            http_response_code(404);
            echo json_encode(array('error' => 'lead not found'));
            return;
        }
        $lead = $lead_q->row_array();

        // Compose event remark from subject + snippet
        $remark_parts = array();
        if (!empty($email_to))     $remark_parts[] = 'To: ' . $email_to;
        if (!empty($subject))      $remark_parts[] = 'Subject: ' . $subject;
        if (!empty($body_snippet)) $remark_parts[] = $body_snippet;
        $remark = implode(' | ', $remark_parts);

        $now = date('Y-m-d H:i:s');

        $uid     = (int) $lead['insidebd'] > 0 ? (int) $lead['insidebd'] : 1;
        $cid_id  = (int) $lead['cmpid_id'];

        $remark_esc   = $this->db->escape_str($remark);
        $now_esc      = $this->db->escape_str($now);

        // tblcallevents.id has no AUTO_INCREMENT; compute next id using MAX(id)+1
        $insert_sql = "
            INSERT INTO tblcallevents(
                id, lastCFID, nextCFID, fwd_date, actontaken,
                meeting_type, mom_received,
                actiontype_id, assignedto_id, cid_id, purpose_id,
                remarks, status_id, user_id, date, updateddate,
                updation_data_type,
                targetstatus, selectby, filter_by,
                approved_status, approved_by, self_assign,
                thnkscomments, late_remarks_message, init_remarks,
                emergency, assignedto_by, aftertask, follow_up_id,
                plan_count, delete_remarks
            )
            SELECT
                COALESCE(MAX(id),0)+1, '0', '0', '$now_esc', 'no',
                'NA', 'no',
                " . (int) self::EMAIL_ACTIONTYPE_ID . ", $uid, $cid_id, " . (int) self::EMAIL_PURPOSE_ID . ",
                '$remark_esc', 1, $uid, '$now_esc', '$now_esc',
                'inside_sales_email',
                0, 'inside_sales', '',
                1, '$uid', 'no',
                '', '', '',
                0, $uid, 0, 0,
                0, ''
            FROM tblcallevents
        ";

        $inserted = $this->db->query($insert_sql);
        if (!$inserted) {
            http_response_code(500);
            echo json_encode(array('error' => 'insert_failed'));
            return;
        }

        // Fetch the event_id we just inserted
        $max_q    = $this->db->query('SELECT MAX(id) AS eid FROM tblcallevents');
        $event_id = $max_q ? (int) $max_q->row_array()['eid'] : 0;

        echo json_encode(array(
            'ok'       => true,
            'event_id' => (int) $event_id,
            'lead_id'  => $lead_id,
            'logged_at'=> $now,
        ));
    }

    // -----------------------------------------------------------------------
    // GET /api/inside_sales/list?user_id=X  (added AgentC 28 May 2026)
    // Alias for my_queue. Mobile app uses /list path.
    // -----------------------------------------------------------------------
    public function list()
    {
        // Accept uid OR user_id query param so mobile {uid} usage works
        $uid = $this->input->get('uid');
        $user_id = $this->input->get('user_id');
        if ($uid && !$user_id) { $_GET['user_id'] = $uid; }
        return $this->my_queue();
    }

}
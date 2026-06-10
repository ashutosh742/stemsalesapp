<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Mobile_read_api
 * Patched: expense_list() method wired to real DB (expense_actuals_log).
 *
 * Endpoint: GET /api/expense/list?uid=
 *
 * Reads from expense_actuals_log.
 * If expense_actuals_log is empty (0 rows on staging), returns empty array
 * with reason='no_rows', not a stub.
 *
 * Also falls back to cash_expense table if actuals log is empty.
 *
 * Route: routes_mobile_pilot.php -> Mobile_read_api/expense_list
 *
 * All other existing public methods in this file are preserved below.
 * Only expense_list() is patched here.
 */
class Mobile_read_api extends CI_Controller {

    private $uid  = null;
    private $_raw = null;

    public function __construct() {
        parent::__construct();
        $this->load->database();
        header('Content-Type: application/json');
        $this->load->library('BearerAuth');
    }

    // -------------------------------------------------------------------------
    // Auth helpers (preserved from original)
    // -------------------------------------------------------------------------
    private function _bearer_ok() {
        $auth = $this->bearerauth->resolve();
        return !empty($auth['ok']);
    }

    private function _uid() {
        if ($this->uid) return $this->uid;
        $sess = $this->session->userdata('user');
        if ($sess && isset($sess['user_id'])) return (int)$sess['user_id'];
        return 0;
    }

    private function _json($data, $code = 200) {
        http_response_code($code);
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }

    // -------------------------------------------------------------------------
    // GET /api/expense/list?uid=
    //
    // Reads from expense_actuals_log. If empty, reads from cash_expense as
    // a fallback.
    // Returns empty array with reason='no_rows' if both tables have no rows
    // for this uid. Never returns a stub response.
    // -------------------------------------------------------------------------
    public function expense_list() {
        if (!$this->_bearer_ok()) {
            $this->_json(array('ok' => false, 'error' => 'Unauthorized'), 401);
        }

        $uid = $this->_uid();
        if ($uid <= 0) $uid = (int)(isset($_GET['uid']) ? $_GET['uid'] : 0);
        if ($uid <= 0) {
            $this->_json(array(
                'ok'           => true,
                'success'      => true,
                'stub'         => false,
                'rows'         => array(),
                'data'         => array('count' => 0, 'reason' => 'uid_required'),
                'route'        => 'api/expense/list',
                'generated_at' => date('c'),
            ));
        }

        $this->load->database();
        $rows = array();

        // Primary: expense_actuals_log
        if ($this->db->table_exists('expense_actuals_log')) {
            $q = $this->db->query(
                "SELECT id,
                        bd_uid           AS user_id,
                        planned_amount,
                        actual_amount,
                        expense_type     AS category,
                        status,
                        submitted_at,
                        final_state,
                        cm_approved,
                        ao_approved,
                        notes            AS description
                 FROM expense_actuals_log
                 WHERE bd_uid = ?
                 ORDER BY submitted_at DESC
                 LIMIT 200",
                array($uid)
            );
            $rows = $q ? $q->result_array() : array();
        }

        // Fallback: cash_expense if primary was empty
        if (empty($rows) && $this->db->table_exists('cash_expense')) {
            $q = $this->db->query(
                "SELECT id,
                        user_id,
                        expense          AS actual_amount,
                        expense_type     AS category,
                        CASE WHEN admin_apr=1 THEN 'approved'
                             WHEN admin_apr=2 THEN 'rejected'
                             ELSE 'pending' END AS status,
                        created_at       AS submitted_at,
                        expense_remarks  AS description
                 FROM cash_expense
                 WHERE user_id = ?
                 ORDER BY created_at DESC
                 LIMIT 200",
                array($uid)
            );
            $rows = $q ? $q->result_array() : array();
        }

        if (empty($rows)) {
            $this->_json(array(
                'ok'           => true,
                'success'      => true,
                'stub'         => false,
                'rows'         => array(),
                'data'         => array('count' => 0, 'uid' => $uid, 'reason' => 'no_rows'),
                'route'        => 'api/expense/list',
                'generated_at' => date('c'),
            ));
        }

        $this->_json(array(
            'ok'           => true,
            'success'      => true,
            'stub'         => false,
            'rows'         => $rows,
            'data'         => array('count' => count($rows), 'uid' => $uid),
            'route'        => 'api/expense/list',
            'generated_at' => date('c'),
        ));
    }

    // -------------------------------------------------------------------------
    // GET /api/calendar/upcoming?uid=&days=
    //
    // Re-added 2026-06-06 (F12/F87 regression fix). Route already existed in
    // routes_mobile_pilot.php -> Mobile_read_api/calendar_upcoming, but the
    // method was missing (caused 404). Reads real calendar_event rows for the
    // BD. If no rows, returns empty array with reason='no_rows' — never a stub.
    // -------------------------------------------------------------------------
    public function calendar_upcoming() {
        if (!$this->_bearer_ok()) {
            $this->_json(array('ok' => false, 'error' => 'Unauthorized'), 401);
        }

        $uid = $this->_uid();
        if ($uid <= 0) $uid = (int)(isset($_GET['uid']) ? $_GET['uid'] : 0);

        $days = (int)(isset($_GET['days']) ? $_GET['days'] : 14);
        if ($days <= 0 || $days > 90) $days = 14;

        if ($uid <= 0) {
            $this->_json(array(
                'ok'           => true,
                'success'      => true,
                'stub'         => false,
                'rows'         => array(),
                'data'         => array('count' => 0, 'reason' => 'uid_required'),
                'route'        => 'api/calendar/upcoming',
                'generated_at' => date('c'),
            ));
        }

        $this->load->database();
        $rows = array();

        if ($this->db->table_exists('calendar_event')) {
            // Link calendar_event -> calendar_account -> bd via account ownership.
            // calendar_account holds the per-user link; filter upcoming window.
            $q = $this->db->query(
                "SELECT ce.id,
                        ce.title,
                        ce.description,
                        ce.location,
                        ce.start_at,
                        ce.end_at,
                        ce.is_all_day,
                        ce.lead_cid_id,
                        ce.sync_direction
                 FROM calendar_event ce
                 LEFT JOIN calendar_account ca ON ca.id = ce.calendar_account_id
                 WHERE ca.uid = ?
                   AND ce.start_at >= NOW()
                   AND ce.start_at <= DATE_ADD(NOW(), INTERVAL ? DAY)
                 ORDER BY ce.start_at ASC
                 LIMIT 200",
                array($uid, $days)
            );
            $rows = $q ? $q->result_array() : array();
        }

        if (empty($rows)) {
            $this->_json(array(
                'ok'           => true,
                'success'      => true,
                'stub'         => false,
                'rows'         => array(),
                'data'         => array('count' => 0, 'uid' => $uid, 'days' => $days, 'reason' => 'no_rows'),
                'route'        => 'api/calendar/upcoming',
                'generated_at' => date('c'),
            ));
        }

        $this->_json(array(
            'ok'           => true,
            'success'      => true,
            'stub'         => false,
            'rows'         => $rows,
            'data'         => array('count' => count($rows), 'uid' => $uid, 'days' => $days),
            'route'        => 'api/calendar/upcoming',
            'generated_at' => date('c'),
        ));
    }

}

<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * application/controllers/api/Leave.php
 * Leave management JSON API - Migration Leave (production LEAVE.txt source).
 * Bearer token authentication. All responses are JSON.
 * Plain ASCII only. No em-dash. Uses Rs for rupees.
 * Tables: leave_requests, leave_management.
 */
class Leave extends MY_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->database();
        $this->load->model('Leave_request_model', 'lrm');
    }

    protected function _check_bearer() {
        $headers = function_exists('getallheaders') ? getallheaders() : array();
        $auth = '';
        if (isset($headers['Authorization'])) {
            $auth = $headers['Authorization'];
        } else if (isset($_SERVER['HTTP_AUTHORIZATION'])) {
            $auth = $_SERVER['HTTP_AUTHORIZATION'];
        } else if (isset($_SERVER['REDIRECT_HTTP_AUTHORIZATION'])) {
            $auth = $_SERVER['REDIRECT_HTTP_AUTHORIZATION'];
        }
        if (!$auth || stripos($auth, 'Bearer ') !== 0) return false;
        $token = trim(substr($auth, 7));
        if ($token === '') return false;
        $row = $this->db->query(
            'SELECT uid, role FROM api_token WHERE token = ? AND active = 1 AND (expires_at IS NULL OR expires_at > NOW()) LIMIT 1',
            array($token)
        )->row_array();
        if (!$row) return false;
        return $row;
    }

    protected function _json($payload, $http_code = 200) {
        $this->output
            ->set_status_header($http_code)
            ->set_content_type('application/json')
            ->set_output(json_encode($payload));
    }

    protected function _post($key, $default = '') {
        $val = $this->input->post($key);
        return ($val === false || $val === null) ? $default : $val;
    }

    protected function _get($key, $default = '') {
        $val = $this->input->get($key);
        return ($val === false || $val === null) ? $default : $val;
    }

    /**
     * GET /api/leave/probe
     * Liveness check. Always returns 200 JSON.
     */
    public function probe() {
        $this->_json(array(
            'ok'          => true,
            'endpoint'    => 'leave',
            'migration'   => 'leave_001',
            'status'      => 'ready',
            'server_time' => date('c')
        ));
    }

    /**
     * POST /api/leave/apply
     * Fields: user_id, leave_type, admin_id (optional), start_date, end_date, reason.
     * Mirrors production leaveApply. Dry_run=1 echoes payload without insert.
     */
    public function apply() {
        try {
            $auth = $this->_check_bearer();
            if (!$auth) { $this->_json(array('ok' => false, 'error' => 'Unauthorized'), 401); return; }

            $uid        = (int)$this->_post('user_id', (int)$auth['uid']);
            $leave_type = trim($this->_post('leave_type'));
            $start_date = trim($this->_post('start_date'));
            // FIX audit_D 2026-06-06: removed stale duplicate end_date=reason line
            $end_date   = trim($this->_post('end_date'));
            $reason     = trim($this->_post('reason'));
            $admin_id   = (int)$this->_post('admin_id', 0);
            $dry_run    = (int)$this->_post('dry_run', 0);

            if (!$leave_type || !$start_date || !$end_date) {
                $this->_json(array('ok' => false, 'error' => 'leave_type, start_date and end_date are required')); return;
            }

            $payload = array(
                'user_id'    => $uid,
                'leave_type' => $leave_type,
                'admin_id'   => $admin_id ?: null,
                'start_date' => $start_date,
                'end_date'   => $end_date,
                'reason'     => $reason,
            );

            if ($dry_run) {
                $this->_json(array('ok' => true, 'dry_run' => true, 'payload' => $payload));
                return;
            }

            // FIX audit_D 2026-06-06 v2: leave_requests.id has no AUTO_INCREMENT; generate id manually
            // Use row_array() for safe access. Also add halfday_leaveType (NOT NULL, no default).
            $max_res = $this->db->query("SELECT COALESCE(MAX(id),0)+1 AS next_id FROM leave_requests")->row_array();
            $next_id = isset($max_res['next_id']) ? (int)$max_res['next_id'] : 12;
            if ($next_id <= 0) $next_id = 12;
            $payload['id'] = $next_id;
            if (!isset($payload['halfday_leaveType'])) $payload['halfday_leaveType'] = 0;
            $this->db->insert('leave_requests', $payload);
            $new_id = (int)$payload['id'];
            if ($new_id) {
                $this->_json(array('ok' => true, 'id' => $new_id, 'message' => 'Leave request submitted successfully'));
            } else {
                $this->_json(array('ok' => false, 'error' => 'Insert failed'), 500);
            }
        } catch (Exception $e) {
            $this->_json(array('ok' => false, 'error' => 'Server error', 'detail' => $e->getMessage()), 500);
        }
    }

    /**
     * GET /api/leave/my_requests?user_id=&status=
     * Returns leave requests for a user. user_id defaults to bearer token uid.
     */
    public function my_requests() {
        try {
            $auth = $this->_check_bearer();
            if (!$auth) { $this->_json(array('ok' => false, 'error' => 'Unauthorized'), 401); return; }

            $uid    = (int)$this->_get('user_id', (int)$auth['uid']);
            $status = $this->_get('status', '');

            $sql    = 'SELECT id, leave_type, start_date, end_date, reason, status, admin_id, created_at FROM leave_requests WHERE user_id = ?';
            $params = array($uid);
            if ($status !== '') {
                $sql    .= ' AND status = ?';
                $params[] = $status;
            }
            $sql .= ' ORDER BY created_at DESC';

            $rows = $this->db->query($sql, $params)->result_array();
            $this->_json(array('ok' => true, 'data' => $rows));
        } catch (Exception $e) {
            $this->_json(array('ok' => false, 'error' => 'Server error', 'detail' => $e->getMessage()), 500);
        }
    }

    /**
     * GET /api/leave/pending_for_admin
     * Returns pending leave requests assigned to the token user as admin.
     */
    public function pending_for_admin() {
        try {
            $auth = $this->_check_bearer();
            if (!$auth) { $this->_json(array('ok' => false, 'error' => 'Unauthorized'), 401); return; }

            $admin_uid = (int)$auth['uid'];
            $status    = $this->_get('status', 'pending');

            $rows = $this->db->query(
                'SELECT lr.id, lr.user_id, lr.leave_type, lr.start_date, lr.end_date, lr.reason, lr.status, lr.created_at FROM leave_requests lr WHERE lr.admin_id = ? AND lr.status = ? ORDER BY lr.created_at ASC',
                array($admin_uid, $status)
            )->result_array();

            $this->_json(array('ok' => true, 'data' => $rows));
        } catch (Exception $e) {
            $this->_json(array('ok' => false, 'error' => 'Server error', 'detail' => $e->getMessage()), 500);
        }
    }

    /**
     * POST /api/leave/action
     * Fields: id, action (approve|reject|cancel), remark (optional).
     * Mirrors production leaveAction.
     */
    public function action() {
        try {
            $auth = $this->_check_bearer();
            if (!$auth) { $this->_json(array('ok' => false, 'error' => 'Unauthorized'), 401); return; }

            $id     = (int)$this->_post('id');
            $action = trim($this->_post('action'));
            $remark = trim($this->_post('remark', ''));

            if (!$id || !$action) {
                $this->_json(array('ok' => false, 'error' => 'id and action are required')); return;
            }

            $status_map = array(
                'approve'  => 'approved',
                'reject'   => 'rejected',
                'cancel'   => 'cancelled',
            );

            if (!array_key_exists($action, $status_map)) {
                $this->_json(array('ok' => false, 'error' => 'Invalid action. Use approve, reject or cancel')); return;
            }

            $new_status = $status_map[$action];
            $update     = array('status' => $new_status);
            if ($remark !== '') {
                $update['remark'] = $remark;
            }

            $this->db->where('id', $id);
            $this->db->update('leave_requests', $update);
            $affected = $this->db->affected_rows();

            if ($affected > 0) {
                $this->_json(array('ok' => true, 'id' => $id, 'new_status' => $new_status));
            } else {
                $this->_json(array('ok' => false, 'error' => 'No record found or status unchanged'), 404);
            }
        } catch (Exception $e) {
            $this->_json(array('ok' => false, 'error' => 'Server error', 'detail' => $e->getMessage()), 500);
        }
    }

    /**
     * POST /api/leave/special
     * Special leave request. Fields: user_id, leave_zone, leave_sdate, leave_edate, holiday_name.
     * Mirrors production AddLeaveManagement / SpecialRequestForLeave flow (admin side).
     */
    public function special() {
        try {
            $auth = $this->_check_bearer();
            if (!$auth) { $this->_json(array('ok' => false, 'error' => 'Unauthorized'), 401); return; }

            $uid          = (int)$auth['uid'];
            $leave_zone   = trim($this->_post('leave_zone'));
            $holiday_name = trim($this->_post('holiday_name'));
            $leave_sdate  = trim($this->_post('leave_sdate'));
            $leave_edate  = trim($this->_post('leave_edate'));
            $dry_run      = (int)$this->_post('dry_run', 0);

            if (!$leave_zone || !$leave_sdate || !$leave_edate) {
                $this->_json(array('ok' => false, 'error' => 'leave_zone, leave_sdate and leave_edate are required')); return;
            }

            $payload = array(
                'leave_name'                => $holiday_name ?: 'Special Leave',
                'leave_type'                => 'Holiday',
                'leave_sdate'               => $leave_sdate,
                'leave_edate'               => $leave_edate,
                'leave_reson'               => 'Special Request',
                'leave_zone'                => $leave_zone,
                'leave_by'                  => $uid,
                'add_type'                  => 'API',
                'leave_apr_by'              => $uid,
                'leave_apr_date'            => date('Y-m-d H:i:s'),
                'leave_apr_status'          => 1,
                'leave_apr_by_admin'        => $uid,
                'leave_apr_date_by_admin'   => date('Y-m-d H:i:s'),
                'leave_apr_status_by_admin' => 1,
            );

            if ($dry_run) {
                $this->_json(array('ok' => true, 'dry_run' => true, 'payload' => $payload));
                return;
            }

            // Check if leave already exists for zone+date range.
            $exists = $this->db->query(
                'SELECT id FROM leave_management WHERE leave_zone = ? AND leave_sdate = ? AND leave_edate = ? LIMIT 1',
                array($leave_zone, $leave_sdate, $leave_edate)
            )->row();

            if ($exists) {
                $this->_json(array('ok' => false, 'error' => 'Special leave already exists for this zone and date range'));
                return;
            }

            $this->db->insert('leave_management', $payload);
            $new_id = (int)$this->db->insert_id();
            if ($new_id) {
                $this->_json(array('ok' => true, 'id' => $new_id, 'message' => 'Special leave added'));
            } else {
                $this->_json(array('ok' => false, 'error' => 'Insert failed'), 500);
            }
        } catch (Exception $e) {
            $this->_json(array('ok' => false, 'error' => 'Server error', 'detail' => $e->getMessage()), 500);
        }
    }
}

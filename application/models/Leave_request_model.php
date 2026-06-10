<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * application/models/Leave_request_model.php
 * Leave management model. Wraps leave_requests and leave_management tables.
 * Plain ASCII only. No em-dash. Uses Rs for rupees.
 */
class Leave_request_model extends CI_Model {

    public function __construct() {
        parent::__construct();
        $this->load->database();
    }

    /**
     * Insert a leave request. Returns new row id or false.
     */
    public function apply($data) {
        $allowed = array('user_id','leave_type','admin_id','start_date','end_date','reason');
        $insert  = array();
        foreach ($allowed as $f) {
            if (isset($data[$f]) && $data[$f] !== null) {
                $insert[$f] = $data[$f];
            }
        }
        if (empty($insert['user_id']) || empty($insert['leave_type']) || empty($insert['start_date']) || empty($insert['end_date'])) {
            return false;
        }
        $this->db->insert('leave_requests', $insert);
        return $this->db->affected_rows() > 0 ? (int)$this->db->insert_id() : false;
    }

    /**
     * Fetch leave requests for a user. Optionally filter by status.
     */
    public function get_by_user($user_id, $status = null) {
        $user_id = (int)$user_id;
        $sql     = 'SELECT id, leave_type, start_date, end_date, reason, status, admin_id, created_at FROM leave_requests WHERE user_id = ?';
        $params  = array($user_id);
        if ($status !== null && $status !== '') {
            $sql    .= ' AND status = ?';
            $params[] = $status;
        }
        $sql .= ' ORDER BY created_at DESC';
        return $this->db->query($sql, $params)->result_array();
    }

    /**
     * Fetch pending requests for an admin.
     */
    public function get_pending_for_admin($admin_id, $status = 'pending') {
        return $this->db->query(
            'SELECT id, user_id, leave_type, start_date, end_date, reason, status, created_at FROM leave_requests WHERE admin_id = ? AND status = ? ORDER BY created_at ASC',
            array((int)$admin_id, $status)
        )->result_array();
    }

    /**
     * Update leave request status. Returns true on success.
     */
    public function update_status($id, $new_status, $remark = '') {
        $id     = (int)$id;
        $update = array('status' => $new_status);
        if ($remark !== '') {
            $update['remark'] = $remark;
        }
        $this->db->where('id', $id);
        $this->db->update('leave_requests', $update);
        return $this->db->affected_rows() > 0;
    }

    /**
     * Get leave balance JSON for a user (stored in user_details table).
     */
    public function get_leave_balance($user_id) {
        $user_id = (int)$user_id;
        $row = $this->db->query(
            'SELECT leave_balance FROM user_details WHERE user_id = ? LIMIT 1',
            array($user_id)
        )->row_array();
        if (!$row || empty($row['leave_balance'])) {
            return array();
        }
        $decoded = json_decode($row['leave_balance'], true);
        return is_array($decoded) ? $decoded : array();
    }

    /**
     * Check if a special leave (holiday) already exists for a zone and date range.
     */
    public function special_exists($leave_zone, $sdate, $edate) {
        $row = $this->db->query(
            'SELECT id FROM leave_management WHERE leave_zone = ? AND leave_sdate = ? AND leave_edate = ? LIMIT 1',
            array($leave_zone, $sdate, $edate)
        )->row();
        return $row !== null;
    }

    /**
     * Insert a special leave into leave_management.
     */
    public function add_special($payload) {
        $this->db->insert('leave_management', $payload);
        return $this->db->affected_rows() > 0 ? (int)$this->db->insert_id() : false;
    }

    /**
     * Get all leave management entries (admin view).
     */
    public function get_all_management($filters = array()) {
        $sql    = 'SELECT id, leave_name, leave_type, leave_sdate, leave_edate, leave_zone, leave_apr_status FROM leave_management WHERE 1=1';
        $params = array();
        if (!empty($filters['leave_zone'])) {
            $sql    .= ' AND leave_zone = ?';
            $params[] = $filters['leave_zone'];
        }
        $sql .= ' ORDER BY leave_sdate DESC';
        return $this->db->query($sql, $params)->result_array();
    }
}

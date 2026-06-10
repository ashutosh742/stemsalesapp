<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * application/models/AIAgents/BDRequest_model.php
 * Migration 046 BD Request v2 model.
 * Plain ASCII only. No em-dash. Uses Rs for rupees.
 */
class BDRequest_model extends CI_Model {

    public function __construct() {
        parent::__construct();
        $this->load->database();
    }

    /**
     * Create a new BD request.
     * Runs duplicate detection within the last 90 days. Auto routes to a CM via reporting chain.
     */
    public function create_request($payload) {
        $required = array('requestor_uid','school_name','school_pincode','reason');
        foreach ($required as $f) {
            if (!isset($payload[$f]) || $payload[$f] === '' || $payload[$f] === null) {
                return array('ok' => false, 'error' => 'Missing required field ' . $f);
            }
        }

        $requestor_uid = (int)$payload['requestor_uid'];
        $school_name = (string)$payload['school_name'];
        $school_pincode = (string)$payload['school_pincode'];

        $duplicate_cid = $this->_duplicate_detect($school_name, $school_pincode);
        $assigned_cm = $this->_resolve_cm($requestor_uid);

        $cols = array('requestor_uid','school_name','school_pincode','reason');
        $vals = array($requestor_uid, $school_name, $school_pincode, (string)$payload['reason']);

        $optional = array('requestor_type','target_bd_uid','school_state','school_city','school_designation','ctype','fbudget_hint','area_name','supporting_notes','sla_minutes');
        foreach ($optional as $f) {
            if (array_key_exists($f, $payload) && $payload[$f] !== null && $payload[$f] !== '') {
                $cols[] = $f;
                $vals[] = $payload[$f];
            }
        }
        if ($duplicate_cid !== null) {
            $cols[] = 'duplicate_hint_cid';
            $vals[] = $duplicate_cid;
        }
        if ($assigned_cm !== null) {
            $cols[] = 'assigned_cm_uid';
            $vals[] = $assigned_cm;
        }

        $placeholders = array_fill(0, count($cols), '?');
        $sql = 'INSERT INTO bd_request (' . implode(',', $cols) . ') VALUES (' . implode(',', $placeholders) . ')';
        $this->db->query($sql, $vals);
        $new_id = (int)$this->db->insert_id();

        $this->log_action($new_id, $requestor_uid, 'created', null, 'pending', 'Request created');
        if ($assigned_cm !== null) {
            $this->log_action($new_id, $assigned_cm, 'assigned', 'pending', 'pending', 'Auto routed via reporting chain');
        }

        return array(
            'ok' => true,
            'id' => $new_id,
            'duplicate_hint_cid' => $duplicate_cid,
            'assigned_cm_uid' => $assigned_cm
        );
    }

    /**
     * CM inbox via v_bd_request_inbox.
     */
    public function inbox_for_cm($cm_uid, $status = null) {
        $cm_uid = (int)$cm_uid;
        if ($status !== null && $status !== '') {
            $sql = 'SELECT * FROM v_bd_request_inbox WHERE assigned_cm_uid = ? AND status = ? ORDER BY age_minutes DESC';
            return $this->db->query($sql, array($cm_uid, $status))->result_array();
        }
        $sql = 'SELECT * FROM v_bd_request_inbox WHERE assigned_cm_uid = ? ORDER BY age_minutes DESC';
        return $this->db->query($sql, array($cm_uid))->result_array();
    }

    /**
     * Requestor view of own requests.
     */
    public function inbox_for_requestor($uid) {
        $uid = (int)$uid;
        $sql = 'SELECT * FROM v_bd_request_inbox WHERE requestor_uid = ? ORDER BY age_minutes ASC';
        return $this->db->query($sql, array($uid))->result_array();
    }

    /**
     * Full detail with logs. RBAC enforced. Requestor sees own. Assigned CM sees own. Admin sees all.
     */
    public function detail($id, $requesting_uid) {
        $id = (int)$id;
        $requesting_uid = (int)$requesting_uid;

        $row = $this->db->query('SELECT * FROM bd_request WHERE id = ? LIMIT 1', array($id))->row_array();
        if (!$row) {
            return array('ok' => false, 'error' => 'Request not found');
        }

        $req_user = $this->db->query('SELECT uid, type_id FROM user WHERE uid = ? LIMIT 1', array($requesting_uid))->row_array();
        $req_type = ($req_user && isset($req_user['type_id'])) ? (int)$req_user['type_id'] : 0;

        $allowed = false;
        if ($req_type === 1 || $req_type === 2) $allowed = true;
        if ((int)$row['requestor_uid'] === $requesting_uid) $allowed = true;
        if ((int)$row['assigned_cm_uid'] === $requesting_uid) $allowed = true;
        if ((int)$row['escalated_to_rm_uid'] === $requesting_uid) $allowed = true;
        if (!$allowed) {
            return array('ok' => false, 'error' => 'Access denied');
        }

        $logs = $this->db->query(
            'SELECT * FROM bd_request_log WHERE request_id = ? ORDER BY created_at ASC',
            array($id)
        )->result_array();

        return array('ok' => true, 'data' => $row, 'logs' => $logs);
    }

    public function approve($id, $cm_uid, $remarks) {
        $id = (int)$id;
        $cm_uid = (int)$cm_uid;
        $row = $this->db->query('SELECT status FROM bd_request WHERE id = ? LIMIT 1', array($id))->row_array();
        if (!$row) return array('ok' => false, 'error' => 'Request not found');
        if ($row['status'] !== 'pending' && $row['status'] !== 'escalated') {
            return array('ok' => false, 'error' => 'Only pending or escalated requests can be approved');
        }
        $from = $row['status'];
        $this->db->query(
            'UPDATE bd_request SET status = ?, decided_by_uid = ?, decided_at = NOW(), decision_remarks = ? WHERE id = ?',
            array('approved', $cm_uid, $remarks, $id)
        );
        $this->log_action($id, $cm_uid, 'approved', $from, 'approved', $remarks);
        return array('ok' => true, 'id' => $id, 'status' => 'approved');
    }

    public function reject($id, $cm_uid, $remarks) {
        $id = (int)$id;
        $cm_uid = (int)$cm_uid;
        $row = $this->db->query('SELECT status FROM bd_request WHERE id = ? LIMIT 1', array($id))->row_array();
        if (!$row) return array('ok' => false, 'error' => 'Request not found');
        if ($row['status'] !== 'pending' && $row['status'] !== 'escalated') {
            return array('ok' => false, 'error' => 'Only pending or escalated requests can be rejected');
        }
        $from = $row['status'];
        $this->db->query(
            'UPDATE bd_request SET status = ?, decided_by_uid = ?, decided_at = NOW(), decision_remarks = ? WHERE id = ?',
            array('rejected', $cm_uid, $remarks, $id)
        );
        $this->log_action($id, $cm_uid, 'rejected', $from, 'rejected', $remarks);
        return array('ok' => true, 'id' => $id, 'status' => 'rejected');
    }

    /**
     * Cron callable. Escalates a single request to its RM via chain walk.
     */
    public function escalate_to_rm($id) {
        $id = (int)$id;
        $row = $this->db->query('SELECT * FROM bd_request WHERE id = ? LIMIT 1', array($id))->row_array();
        if (!$row) return array('ok' => false, 'error' => 'Request not found');
        if ($row['status'] !== 'pending') {
            return array('ok' => false, 'error' => 'Only pending requests escalate');
        }

        $rm_uid = $this->_resolve_rm((int)$row['assigned_cm_uid']);
        if ($rm_uid === null) {
            return array('ok' => false, 'error' => 'No RM found in chain');
        }
        $this->db->query(
            'UPDATE bd_request SET status = ?, escalated_to_rm_uid = ?, escalated_at = NOW() WHERE id = ?',
            array('escalated', $rm_uid, $id)
        );
        $this->log_action($id, $rm_uid, 'escalated', 'pending', 'escalated', 'Auto escalated by SLA cron');
        return array('ok' => true, 'id' => $id, 'escalated_to_rm_uid' => $rm_uid);
    }

    /**
     * Called by Lead_model after a real init_call is created from this request.
     */
    public function mark_init_call_created($id, $init_call_id) {
        $id = (int)$id;
        $init_call_id = (int)$init_call_id;
        $row = $this->db->query('SELECT status FROM bd_request WHERE id = ? LIMIT 1', array($id))->row_array();
        if (!$row) return array('ok' => false, 'error' => 'Request not found');
        $from = $row['status'];
        $this->db->query(
            'UPDATE bd_request SET status = ?, init_call_id = ? WHERE id = ?',
            array('init_call_created', $init_call_id, $id)
        );
        $this->log_action($id, 0, 'init_call_created', $from, 'init_call_created', 'Linked to init_call ' . $init_call_id);
        return array('ok' => true, 'id' => $id, 'init_call_id' => $init_call_id);
    }

    public function log_action($request_id, $actor_uid, $action, $from_status, $to_status, $remarks) {
        $sql = 'INSERT INTO bd_request_log (request_id, actor_uid, action, from_status, to_status, remarks) VALUES (?, ?, ?, ?, ?, ?)';
        $this->db->query($sql, array((int)$request_id, (int)$actor_uid, (string)$action, $from_status, $to_status, $remarks));
        return true;
    }

    /**
     * Cron job. Finds pending requests older than sla_minutes and escalates each.
     */
    public function compute_sla_breaches() {
        $rows = $this->db->query(
            'SELECT id FROM bd_request WHERE status = ? AND TIMESTAMPDIFF(MINUTE, created_at, NOW()) > sla_minutes',
            array('pending')
        )->result_array();
        $count = 0;
        foreach ($rows as $r) {
            $res = $this->escalate_to_rm((int)$r['id']);
            if (!empty($res['ok'])) $count++;
        }
        return array('ok' => true, 'escalated' => $count);
    }

    // Private helpers

    /**
     * Fuzzy match on school name plus exact pincode within last 90 days.
     */
    private function _duplicate_detect($school_name, $pincode) {
        $sql = 'SELECT cid_id FROM init_call '
             . 'WHERE compname LIKE CONCAT(?, ?, ?) '
             . 'AND pincode = ? '
             . 'AND createDate > DATE_SUB(NOW(), INTERVAL 90 DAY) '
             . 'LIMIT 1';
        $row = $this->db->query($sql, array('%', $school_name, '%', $pincode))->row_array();
        return $row ? (int)$row['cid_id'] : null;
    }

    /**
     * Walks user.reporting_cm_uid for the requestor and returns the first CM uid.
     */
    private function _resolve_cm($requestor_uid) {
        $row = $this->db->query('SELECT reporting_cm_uid FROM user WHERE uid = ? LIMIT 1', array((int)$requestor_uid))->row_array();
        if ($row && !empty($row['reporting_cm_uid'])) {
            return (int)$row['reporting_cm_uid'];
        }
        return null;
    }

    /**
     * Walks chain upward from a CM uid to find an RM uid.
     */
    private function _resolve_rm($cm_uid) {
        if (!$cm_uid) return null;
        $row = $this->db->query('SELECT reporting_rm_uid FROM user WHERE uid = ? LIMIT 1', array((int)$cm_uid))->row_array();
        if ($row && !empty($row['reporting_rm_uid'])) {
            return (int)$row['reporting_rm_uid'];
        }
        return null;
    }
}

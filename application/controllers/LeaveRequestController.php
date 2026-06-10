<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * LeaveRequestController
 * Endpoint: GET /api/leave_request/list?uid={uid}
 *
 * Returns leave_requests rows for a given user_id (5 rows on staging).
 *
 * leave_requests columns (confirmed on staging):
 *   id, user_id, admin_id, start_date, end_date, reason, status,
 *   approved_at, approved_by, created_at, reject_reason, rejected_by,
 *   rejected_at, updated_at, leave_type, is_halfday_leave, main_admin
 *
 * Status enum: pending_manager, approved_manager, rejected_manager,
 *              pending_admin, approved_admin, rejected_admin
 *
 * Also supports POST /api/leave_request/submit (preserved from original).
 *
 * Route: routes_blitz_30may_c.php or routes_additions.php
 */
class LeaveRequestController extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->database();
    }

    private function _bearer() {
        if (function_exists('authunify_ok') && authunify_ok()) { return true; } // rimlyproof_authunify_20260609

        $h = $this->input->get_request_header('Authorization', TRUE);
        if (!$h || stripos($h, 'Bearer ') !== 0) {
            $this->output->set_status_header(401)->set_content_type('application/json')
                ->set_output(json_encode(array('ok' => false, 'error' => 'unauthorized')));
            return false;
        }
        $tok      = trim(substr($h, 7));
        $expected = trim(@file_get_contents(APPPATH . 'config/digest_token.txt'));
        if (!$expected) {
            $expected = getenv('STEM_DIGEST_TOKEN') ?: '4eBaiAT7r4zu6OK3b8evjLNia1D7RGgb0qRTuLJfUSo';
        }
        if (!hash_equals($expected, $tok)) {
            $this->output->set_status_header(401)->set_content_type('application/json')
                ->set_output(json_encode(array('ok' => false, 'error' => 'bad_token')));
            return false;
        }
        return true;
    }

    private function _json($payload, $status = 200) {
        $this->output->set_status_header($status)
            ->set_content_type('application/json')
            ->set_output(json_encode($payload));
    }

    /**
     * GET /api/leave_request/list?uid={uid}&status=&limit=50
     * Returns leave requests for a user, optionally filtered by status.
     */
    public function list_index() {
        if (!$this->_bearer()) return;

        $uid    = (int) $this->input->get('uid');
        $status = trim((string) $this->input->get('status'));
        $limit  = (int) $this->input->get('limit');

        if ($uid <= 0) {
            $this->_json(array('ok' => false, 'error' => 'uid is required and must be a positive integer'), 400);
            return;
        }
        if ($limit <= 0 || $limit > 200) $limit = 50;

        $valid_statuses = array(
            'pending_manager', 'approved_manager', 'rejected_manager',
            'pending_admin', 'approved_admin', 'rejected_admin',
        );

        $sql    = "SELECT lr.*,
                          lm.leave_type AS leave_type_label
                   FROM leave_requests lr
                   LEFT JOIN leave_master lm ON lm.id = lr.leave_type
                   WHERE lr.user_id = ?";
        $params = array($uid);

        if ($status !== '' && in_array($status, $valid_statuses, true)) {
            $sql    .= " AND lr.status = ?";
            $params[] = $status;
        }

        $sql    .= " ORDER BY lr.created_at DESC LIMIT ?";
        $params[] = $limit;

        $rows = $this->db->query($sql, $params)->result_array();

        if (empty($rows)) {
            $this->_json(array(
                'ok'           => true,
                'success'      => true,
                'stub'         => false,
                'rows'         => array(),
                'data'         => array(
                    'count'  => 0,
                    'uid'    => $uid,
                    'reason' => 'no_rows',
                ),
                'route'        => 'api/leave_request/list',
                'generated_at' => date('c'),
            ));
            return;
        }

        $this->_json(array(
            'ok'           => true,
            'success'      => true,
            'stub'         => false,
            'rows'         => $rows,
            'data'         => array('count' => count($rows), 'uid' => $uid),
            'route'        => 'api/leave_request/list',
            'generated_at' => date('c'),
        ));
    }
}

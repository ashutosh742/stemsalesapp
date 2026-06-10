<?php
/**
 * LeadBudgetRange Controller
 * Route: PUT /api/lead/budget_range/:cid
 * Patch for S.9 Rs Cr range estimator
 * Date: 2025-05-28
 */

defined('BASEPATH') OR exit('No direct script access allowed');

class LeadBudgetRange extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('Lead_model');
        $this->load->library('jwt_auth');
        $this->load->helper('jwt');
    }

    /**
     * PUT /api/lead/budget_range/:cid
     * Body (JSON): { "min_cr": float, "max_cr": float, "assumptions": string }
     * Auth: Bearer token required
     * Access: owners only (role_id = owner or CID owner match)
     */
    public function update($cid = null) {

        // --- Auth ---
        $bearer = $this->input->get_request_header('Authorization', TRUE);
        if (!$bearer || !preg_match('/^Bearer\s+(.+)$/i', $bearer, $m)) {
            return $this->_respond(401, 'Unauthorized', 'Missing or invalid Authorization header');
        }
        $token = $m[1];
        $payload = decode_jwt($token);
        if (!$payload) {
            return $this->_respond(401, 'Unauthorized', 'Invalid or expired token');
        }
        $uid = (int)($payload['uid'] ?? 0);
        if (!$uid) {
            return $this->_respond(401, 'Unauthorized', 'Token missing uid');
        }

        // --- CID ---
        $cid = (int)$cid;
        if (!$cid) {
            return $this->_respond(400, 'Bad Request', 'Missing or invalid cid in URL');
        }

        // --- Body ---
        $body = json_decode($this->input->raw_input_stream, true);
        if (!$body) {
            return $this->_respond(400, 'Bad Request', 'Invalid JSON body');
        }

        $min_cr       = isset($body['min_cr'])       ? (float)$body['min_cr']       : null;
        $max_cr       = isset($body['max_cr'])       ? (float)$body['max_cr']       : null;
        $assumptions  = isset($body['assumptions'])  ? trim((string)$body['assumptions']) : null;

        if ($min_cr === null && $max_cr === null) {
            return $this->_respond(422, 'Unprocessable Entity', 'At least one of min_cr or max_cr is required');
        }

        if ($min_cr !== null && $max_cr !== null && $min_cr > $max_cr) {
            return $this->_respond(422, 'Unprocessable Entity', 'min_cr must not exceed max_cr');
        }

        // --- Ownership check ---
        // Only the owner of the lead (matched by cid/uid) or users with role_id in (1,2) may edit
        $lead = $this->db
            ->where('cid', $cid)
            ->get('init_call')
            ->row_array();

        if (!$lead) {
            return $this->_respond(404, 'Not Found', 'Lead not found for cid ' . $cid);
        }

        $is_owner = ((int)($lead['owner_uid'] ?? 0) === $uid);
        $this->db->where('uid', $uid)->where('role_id <', 3);
        $is_admin = ($this->db->get('users')->num_rows() > 0);

        if (!$is_owner && !$is_admin) {
            return $this->_respond(403, 'Forbidden', 'Only the lead owner or an admin may edit the budget range');
        }

        // --- Update ---
        $update_data = [];
        if ($min_cr !== null)      $update_data['fbudget_min_cr']    = $min_cr;
        if ($max_cr !== null)      $update_data['fbudget_max_cr']    = $max_cr;
        if ($assumptions !== null) $update_data['fbudget_assumptions'] = $assumptions;

        $this->db->where('cid', $cid)->update('init_call', $update_data);

        if ($this->db->affected_rows() < 1) {
            // No-op update (values unchanged) is still a success
        }

        // --- Return updated row ---
        $updated = $this->db
            ->select('cid, fbudget, fbudget_min_cr, fbudget_max_cr, fbudget_assumptions')
            ->where('cid', $cid)
            ->get('init_call')
            ->row_array();

        return $this->_respond(200, 'OK', 'Budget range updated', $updated);
    }

    // ------------------------------------------------------------------
    // GET /api/lead/budget_range/:cid  -- read-only fetch
    // ------------------------------------------------------------------
    public function get($cid = null) {
        $bearer = $this->input->get_request_header('Authorization', TRUE);
        if (!$bearer || !preg_match('/^Bearer\s+(.+)$/i', $bearer, $m)) {
            return $this->_respond(401, 'Unauthorized', 'Missing Authorization header');
        }
        $token = $m[1];
        if (!decode_jwt($token)) {
            return $this->_respond(401, 'Unauthorized', 'Invalid token');
        }

        $cid = (int)$cid;
        if (!$cid) {
            return $this->_respond(400, 'Bad Request', 'Missing cid');
        }

        $row = $this->db
            ->select('cid, fbudget, fbudget_min_cr, fbudget_max_cr, fbudget_assumptions')
            ->where('cid', $cid)
            ->get('init_call')
            ->row_array();

        if (!$row) {
            return $this->_respond(404, 'Not Found', 'Lead not found for cid ' . $cid);
        }

        return $this->_respond(200, 'OK', 'Budget range data', $row);
    }

    // ------------------------------------------------------------------
    private function _respond($code, $status, $message, $data = null) {
        $this->output
            ->set_status_header($code)
            ->set_content_type('application/json');
        $payload = ['status' => $status, 'message' => $message];
        if ($data !== null) $payload['data'] = $data;
        echo json_encode($payload);
    }
}
/* End of file LeadBudgetRange.php */

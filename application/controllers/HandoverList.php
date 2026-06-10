<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * HandoverList
 * Endpoint: GET /api/handover/list?uid={uid}
 *
 * Returns handover_v2 rows for a given closing_bd_uid or cm_uid (4 rows on staging).
 *
 * handover_v2 columns (confirmed on staging):
 *   id, cid_id, closing_bd_uid, project_code, compname, ctype, fbudget,
 *   designation, dispatch_address, dispatch_pincode, dispatch_state, dispatch_city,
 *   expected_install_date, artwork_required, billing_entity, payment_terms, csr_flag,
 *   dm_email, status, bd_signoff_at, cm_approval_at, cm_uid, cm_remarks,
 *   rejection_reason, submitted_at, approved_at, sla_breach_flag,
 *   created_at, updated_at
 *
 * Route: routes_agent6.php -> Handover/list (class is HandoverList)
 */
class HandoverList extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->database();
        $this->load->library('BearerAuth');
    }

    private function _bearer() {
        $auth = $this->bearerauth->resolve();
        if (empty($auth['ok'])) {
            $this->_json(array('ok' => false, 'error' => 'bad_token'), 401);
            return false;
        }
        return true;
    }

    /**
     * GET /api/handover/list?uid={uid}
     * Also accepts cm_uid param (defaults to uid).
     */
    public function list_index() {
        if (!$this->_bearer()) return;

        $uid = (int) $this->input->get('uid');
        if ($uid <= 0) {
            $this->_json(array('ok' => false, 'error' => 'uid is required and must be a positive integer'), 400);
            return;
        }

        $sql = "SELECT
                    h.id,
                    h.cid_id,
                    h.closing_bd_uid,
                    h.project_code,
                    h.compname,
                    h.ctype,
                    CAST(h.fbudget AS CHAR) AS fbudget,
                    h.designation,
                    h.dispatch_state,
                    h.dispatch_city,
                    h.expected_install_date,
                    h.artwork_required,
                    h.billing_entity,
                    h.payment_terms,
                    h.csr_flag,
                    h.dm_email,
                    h.status,
                    h.bd_signoff_at,
                    h.cm_approval_at,
                    h.cm_uid,
                    h.cm_remarks,
                    h.rejection_reason,
                    h.submitted_at,
                    h.approved_at,
                    h.sla_breach_flag,
                    h.created_at,
                    h.updated_at,
                    bd.name  AS bd_name,
                    bd.email AS bd_email,
                    cm.name  AS cm_name,
                    cm.email AS cm_email
                FROM handover_v2 h
                LEFT JOIN user bd ON bd.uid = h.closing_bd_uid
                LEFT JOIN user cm ON cm.uid = h.cm_uid
                WHERE h.closing_bd_uid = ?
                   OR h.cm_uid         = ?
                ORDER BY h.created_at DESC";

        $rows = $this->db->query($sql, array($uid, $uid))->result_array();

        if (empty($rows)) {
            $this->_json(array(
                'ok'           => true,
                'success'      => true,
                'stub'         => false,
                'rows'         => array(),
                'data'         => array('count' => 0, 'uid' => $uid, 'reason' => 'no_rows'),
                'route'        => 'api/handover/list',
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
            'route'        => 'api/handover/list',
            'generated_at' => date('c'),
        ));
    }

    private function _json($payload, $status = 200) {
        $this->output->set_status_header($status)
            ->set_content_type('application/json')
            ->set_output(json_encode($payload));
    }
}

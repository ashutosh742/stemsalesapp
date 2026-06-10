<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * CalleventsListController
 * Endpoint: GET /api/callevents/list?cid_id={id}&limit=50&offset=0
 *
 * Returns paginated tblcallevents rows for a given cid_id.
 * Joins: action (actiontype_name), purpose (purpose_name), user_details (createdby_name).
 *
 * tblcallevents columns confirmed on staging:
 *   id, cid_id, actiontype_id, purpose_id, date, fwd_date, remarks, special_remarks,
 *   actontaken, status_id, user_id, plan, purpose_achieved, meeting_type, mom,
 *   cancelled_at, planned_cost, actual_cost, updated_at, etc.
 *
 * Route: routes_blitz_30may_a.php -> CalleventsListController/index
 */
class CalleventsListController extends CI_Controller {

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

    private function _json($data, $code = 200) {
        $this->output
            ->set_status_header($code)
            ->set_content_type('application/json')
            ->set_output(json_encode($data));
    }

    /**
     * GET /api/callevents/list?cid_id={id}&limit=50&offset=0
     */
    public function index() {
        if (!$this->_bearer()) return;

        $cid_id = (int) $this->input->get('cid_id');
        $limit  = (int) $this->input->get('limit');
        $offset = (int) $this->input->get('offset');

        if ($cid_id <= 0) {
            $this->_json(array(
                'ok'           => false,
                'success'      => false,
                'stub'         => false,
                'error'        => 'cid_id is required and must be a positive integer',
                'rows'         => array(),
                'data'         => array('count' => 0),
                'route'        => 'api/callevents/list',
                'generated_at' => date('c'),
            ));
            return;
        }

        if ($limit <= 0)  $limit  = 50;
        if ($limit > 200) $limit  = 200;
        if ($offset < 0)  $offset = 0;

        $sql = "
            SELECT
                ce.id,
                ce.cid_id,
                ce.actiontype_id,
                a.name               AS actiontype_name,
                ce.purpose_id,
                p.name               AS purpose_name,
                ce.date              AS createDate,
                ce.fwd_date,
                ce.remarks,
                ce.special_remarks,
                ce.actontaken,
                ce.purpose_achieved,
                ce.status_id,
                ce.user_id,
                ud.name              AS createdby_name,
                ce.plan,
                ce.meeting_type,
                ce.mom,
                ce.mom_approved,
                ce.cancelled_at,
                ce.planned_cost,
                ce.actual_cost,
                ce.updated_at
            FROM tblcallevents ce
            LEFT JOIN action a ON a.id = ce.actiontype_id
            LEFT JOIN purpose p ON p.id = ce.purpose_id
            LEFT JOIN user_details ud ON ud.user_id = ce.user_id
            WHERE ce.cid_id = ?
            ORDER BY ce.date DESC, ce.id DESC
            LIMIT ? OFFSET ?
        ";

        $rows = $this->db->query($sql, array($cid_id, $limit, $offset))->result_array();

        // Total count for pagination
        $count_row = $this->db->query(
            "SELECT COUNT(*) AS total FROM tblcallevents WHERE cid_id = ?",
            array($cid_id)
        )->row_array();
        $total = isset($count_row['total']) ? (int) $count_row['total'] : 0;

        if (empty($rows)) {
            $this->_json(array(
                'ok'           => true,
                'success'      => true,
                'stub'         => false,
                'rows'         => array(),
                'data'         => array(
                    'count'  => 0,
                    'total'  => $total,
                    'cid_id' => $cid_id,
                    'reason' => 'no_rows',
                ),
                'route'        => 'api/callevents/list',
                'generated_at' => date('c'),
            ));
            return;
        }

        $this->_json(array(
            'ok'           => true,
            'success'      => true,
            'stub'         => false,
            'rows'         => $rows,
            'data'         => array(
                'count'  => count($rows),
                'total'  => $total,
                'cid_id' => $cid_id,
                'limit'  => $limit,
                'offset' => $offset,
            ),
            'route'        => 'api/callevents/list',
            'generated_at' => date('c'),
        ));
    }
}

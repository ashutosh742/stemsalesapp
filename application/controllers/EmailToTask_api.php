<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * EmailToTask_api
 * Endpoints:
 *   GET  /api/email_to_task/probe  - health probe (no auth required)
 *   GET  /api/email_to_task/queue  - pending triage queue from email_to_task table
 *
 * email_to_task table columns (confirmed on staging, 6 rows):
 *   id, from_email, subject, body, received_at, parsed_school, parsed_cid_id,
 *   parsed_action, parsed_date, parsed_assignee_uid, original_email, status,
 *   triaged_by, triaged_at, target_task_id, created_at
 *
 * status enum: pending_triage, triaged, rejected
 *
 * Route: routes_blitz_30may_e.php -> EmailToTask_api/probe_index and queue_index
 */
class EmailToTask_api extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->database();
    }

    private function _bearer() {
        if (function_exists('authunify_ok') && authunify_ok()) { return true; } // rimlyproof_authunify_20260609

        $h = $this->input->get_request_header('Authorization', TRUE);
        if (!$h || stripos($h, 'Bearer ') !== 0) {
            $this->_json(array('ok' => false, 'error' => 'unauthorized'), 401);
            return false;
        }
        $tok      = trim(substr($h, 7));
        $expected = trim(@file_get_contents(APPPATH . 'config/digest_token.txt'));
        if (!$expected) {
            $expected = getenv('STEM_DIGEST_TOKEN') ?: '4eBaiAT7r4zu6OK3b8evjLNia1D7RGgb0qRTuLJfUSo';
        }
        if (!hash_equals($expected, $tok)) {
            $this->_json(array('ok' => false, 'error' => 'bad_token'), 401);
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
     * GET /api/email_to_task/probe
     * Returns health info: table exists, row counts by status.
     * No auth required (probe endpoint).
     */
    public function probe_index() {
        $tbl_check = $this->db->query("SHOW TABLES LIKE 'email_to_task'")->num_rows() > 0;

        if (!$tbl_check) {
            $this->_json(array(
                'ok'           => true,
                'stub'         => false,
                'table_exists' => false,
                'queue_count'  => 0,
                'triaged_count'=> 0,
                'route'        => 'api/email_to_task/probe',
                'generated_at' => date('c'),
            ));
            return;
        }

        $counts_row = $this->db->query(
            "SELECT
                SUM(CASE WHEN status='pending_triage' THEN 1 ELSE 0 END) AS queue_count,
                SUM(CASE WHEN status='triaged' THEN 1 ELSE 0 END)        AS triaged_count,
                COUNT(*) AS total
             FROM email_to_task"
        )->row_array();

        $this->_json(array(
            'ok'            => true,
            'stub'          => false,
            'table_exists'  => true,
            'queue_count'   => (int)($counts_row ? $counts_row['queue_count']   : 0),
            'triaged_count' => (int)($counts_row ? $counts_row['triaged_count'] : 0),
            'total'         => (int)($counts_row ? $counts_row['total']         : 0),
            'route'         => 'api/email_to_task/probe',
            'generated_at'  => date('c'),
        ));
    }

    /**
     * GET /api/email_to_task/queue?status=pending_triage&limit=50
     * Returns email_to_task rows filtered by status.
     */
    public function queue_index() {
        if (!$this->_bearer()) return;

        $status = $this->input->get('status') ?: 'pending_triage';
        $limit  = (int) $this->input->get('limit');
        if ($limit <= 0 || $limit > 200) $limit = 50;

        $valid_statuses = array('pending_triage', 'triaged', 'rejected');
        if (!in_array($status, $valid_statuses, true)) {
            $status = 'pending_triage';
        }

        $sql = "SELECT
                    et.id,
                    et.from_email,
                    et.subject,
                    et.body,
                    et.received_at,
                    et.parsed_school,
                    et.parsed_cid_id,
                    et.parsed_action,
                    et.parsed_date,
                    et.parsed_assignee_uid,
                    et.status,
                    et.triaged_by,
                    et.triaged_at,
                    et.target_task_id,
                    et.created_at,
                    ud.name AS assignee_name,
                    ic.exschool AS matched_school_name
                FROM email_to_task et
                LEFT JOIN user_details ud ON ud.user_id = et.parsed_assignee_uid
                LEFT JOIN init_call ic    ON ic.id = et.parsed_cid_id
                WHERE et.status = ?
                ORDER BY et.received_at DESC
                LIMIT ?";

        $rows = $this->db->query($sql, array($status, $limit))->result_array();

        if (empty($rows)) {
            $this->_json(array(
                'ok'           => true,
                'success'      => true,
                'stub'         => false,
                'rows'         => array(),
                'data'         => array(
                    'count'  => 0,
                    'status' => $status,
                    'reason' => 'no_rows',
                ),
                'route'        => 'api/email_to_task/queue',
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
                'status' => $status,
            ),
            'route'        => 'api/email_to_task/queue',
            'generated_at' => date('c'),
        ));
    }
}

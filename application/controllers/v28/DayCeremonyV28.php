<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * DayCeremonyV28 Controller
 *
 * Handles /api/day_ceremony/* routes for STEM CRM v2.8 staging.
 *
 * Table used (verified on staging):
 *   day_ceremony_log  (id, uid, ceremony_date, start_ts, end_ts, summary_json, created_at)
 *
 * Routes:
 *   POST api/day_ceremony/close_today
 *   POST api/day_ceremony/end
 *   POST api/day_ceremony/end_day
 *   POST api/day_ceremony/start
 *   POST api/day_ceremony/start_day
 *   POST api/day_ceremony/start_today
 *
 * start / start_day / start_today all write start_ts.
 * end / end_day / close_today all write end_ts + summary_json.
 */
class DayCeremonyV28 extends CI_Controller {

    private $bearer = '4eBaiAT7r4zu6OK3b8evjLNia1D7RGgb0qRTuLJfUSo';

    public function __construct()
    {
        parent::__construct();
        $this->load->database();
        $this->load->helper('url');
        $this->output->set_content_type('application/json');
    }

    private function _check_auth()
    {
        if (function_exists('authunify_ok') && authunify_ok()) { return true; } // rimlyproof_authunify_20260609

        $hdr = $this->input->get_request_header('Authorization');
        if (!$hdr || trim(str_replace('Bearer', '', $hdr)) !== $this->bearer) {
            $this->output->set_status_header(401);
            echo json_encode(['ok' => false, 'error' => 'unauthorized']);
            return false;
        }
        return true;
    }

    private function _json($data, $status = 200)
    {
        $this->output->set_status_header($status);
        echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    }

    /**
     * _get_uid_and_date
     * Reads uid from POST or GET, defaults ceremony_date to today.
     */
    private function _get_uid_and_date()
    {
        $uid = (int) ($this->input->post('uid') ?: $this->input->get('uid'));
        $d   = $this->input->post('ceremony_date') ?: $this->input->get('ceremony_date');
        if (!$d || !preg_match('/^\d{4}-\d{2}-\d{2}$/', $d)) {
            $d = date('Y-m-d');
        }
        return [$uid, $d];
    }

    /**
     * _do_start
     * Shared logic for start / start_day / start_today.
     * Creates a day_ceremony_log row with start_ts set.
     * Idempotent: if row already exists for (uid, ceremony_date), returns existing.
     */
    private function _do_start($action_label)
    {
        if (!$this->_check_auth()) return;

        list($uid, $ceremony_date) = $this->_get_uid_and_date();
        if ($uid <= 0) {
            return $this->_json(['ok' => false, 'error' => 'uid required'], 400);
        }

        $existing = $this->db->get_where('day_ceremony_log', ['uid' => $uid, 'ceremony_date' => $ceremony_date], 1)->row_array();
        if ($existing) {
            return $this->_json([
                'ok'        => true,
                'success'   => true,
                'action'    => $action_label,
                'message'   => 'already_started',
                'data'      => $existing,
            ]);
        }

        $now = date('Y-m-d H:i:s');
        $this->db->insert('day_ceremony_log', [
            'uid'            => $uid,
            'ceremony_date'  => $ceremony_date,
            'start_ts'       => $now,
            'created_at'     => $now,
        ]);
        $new_id = $this->db->insert_id();

        $this->_json([
            'ok'          => true,
            'success'     => true,
            'action'      => $action_label,
            'message'     => 'started',
            'log_id'      => $new_id,
            'uid'         => $uid,
            'ceremony_date' => $ceremony_date,
            'start_ts'    => $now,
        ]);
    }

    /**
     * _do_end
     * Shared logic for end / end_day / close_today.
     * Writes end_ts and optional summary_json.
     */
    private function _do_end($action_label)
    {
        if (!$this->_check_auth()) return;

        list($uid, $ceremony_date) = $this->_get_uid_and_date();
        if ($uid <= 0) {
            return $this->_json(['ok' => false, 'error' => 'uid required'], 400);
        }

        $row = $this->db->get_where('day_ceremony_log', ['uid' => $uid, 'ceremony_date' => $ceremony_date], 1)->row_array();
        if (!$row) {
            return $this->_json(['ok' => false, 'error' => 'no_start_record_found', 'hint' => 'Call start first'], 404);
        }

        if (!empty($row['end_ts'])) {
            return $this->_json([
                'ok'      => true,
                'success' => true,
                'action'  => $action_label,
                'message' => 'already_closed',
                'data'    => $row,
            ]);
        }

        $now          = date('Y-m-d H:i:s');
        $summary_raw  = $this->input->post('summary') ?: $this->input->get('summary');
        $summary_json = null;
        if ($summary_raw) {
            // Accept either JSON string or plain text
            $decoded = json_decode($summary_raw, true);
            $summary_json = ($decoded !== null) ? json_encode($decoded) : json_encode(['note' => $summary_raw]);
        }

        $update = ['end_ts' => $now];
        if ($summary_json !== null) {
            $update['summary_json'] = $summary_json;
        }

        $this->db->where('id', (int) $row['id'])->update('day_ceremony_log', $update);

        $this->_json([
            'ok'          => true,
            'success'     => true,
            'action'      => $action_label,
            'message'     => 'closed',
            'log_id'      => (int) $row['id'],
            'uid'         => $uid,
            'ceremony_date' => $ceremony_date,
            'end_ts'      => $now,
        ]);
    }

    // -----------------------------------------------------------------------
    // POST api/day_ceremony/start
    // -----------------------------------------------------------------------
    public function start()
    {
        $this->_do_start('start');
    }

    // -----------------------------------------------------------------------
    // POST api/day_ceremony/start_day
    // -----------------------------------------------------------------------
    public function start_day()
    {
        $this->_do_start('start_day');
    }

    // -----------------------------------------------------------------------
    // POST api/day_ceremony/start_today
    // -----------------------------------------------------------------------
    public function start_today()
    {
        $this->_do_start('start_today');
    }

    // -----------------------------------------------------------------------
    // POST api/day_ceremony/end
    // -----------------------------------------------------------------------
    public function end()
    {
        $this->_do_end('end');
    }

    // -----------------------------------------------------------------------
    // POST api/day_ceremony/end_day
    // -----------------------------------------------------------------------
    public function end_day()
    {
        $this->_do_end('end_day');
    }

    // -----------------------------------------------------------------------
    // POST api/day_ceremony/close_today
    // -----------------------------------------------------------------------
    public function close_today()
    {
        $this->_do_end('close_today');
    }
}

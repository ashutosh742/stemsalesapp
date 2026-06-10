<?php
/**
 * EmailSend
 * POST /api/email/send
 *
 * Queues an outbound email into comm_outbox (created by migration_092).
 * NO real SMTP is triggered. Status is set to 'queued'.
 *
 * Body (JSON or form-data):
 *   from_uid   INT    required  - sender user.uid
 *   to_email   STRING required  - recipient address
 *   subject    STRING required
 *   body_text  STRING required
 *   cid_id     INT    optional  - related init_call.id
 *
 * Returns: { ok, success, stub:false, queue_id, route, generated_at }
 *
 * Agent E, Blitz 30 May 2026
 */
defined('BASEPATH') OR exit('No direct script access allowed');

class EmailSend extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->database();
    }

    // -------------------------------------------------------------------------
    private function _bearer() {
        if (function_exists('authunify_ok') && authunify_ok()) { return true; } // rimlyproof_authunify_20260609

        $h = $this->input->get_request_header('Authorization', TRUE);
        if (!$h || stripos($h, 'Bearer ') !== 0) {
            $this->_json(['ok' => false, 'error' => 'unauthorized'], 401);
            return false;
        }
        $tok      = trim(substr($h, 7));
        $expected = trim(@file_get_contents(APPPATH . 'config/digest_token.txt'));
        if (!$expected || !hash_equals($expected, $tok)) {
            $this->_json(['ok' => false, 'error' => 'bad_token'], 401);
            return false;
        }
        return true;
    }

    // -------------------------------------------------------------------------
    // POST /api/email/send
    // -------------------------------------------------------------------------
    public function send_index() {
        if (!$this->_bearer()) return;

        // Accept JSON body or form POST
        $raw  = $this->input->raw_input_stream;
        $body = @json_decode($raw, true);
        if (!is_array($body)) {
            // Fall back to form POST
            $body = $this->input->post(null, true);
        }

        // --- Input validation ---
        $from_uid  = isset($body['from_uid'])  ? (int)   $body['from_uid']            : 0;
        $to_email  = isset($body['to_email'])  ? trim(   $body['to_email'])            : '';
        $subject   = isset($body['subject'])   ? trim(   $body['subject'])             : '';
        $body_text = isset($body['body_text']) ? trim(   $body['body_text'])           : '';
        $cid_id    = isset($body['cid_id'])    ? (int)   $body['cid_id']              : null;

        $errors = [];
        if ($from_uid <= 0)   $errors[] = 'from_uid is required (positive integer)';
        if ($to_email === '')  $errors[] = 'to_email is required';
        if (filter_var($to_email, FILTER_VALIDATE_EMAIL) === false && $to_email !== '') {
            $errors[] = 'to_email must be a valid email address';
        }
        if ($subject === '')   $errors[] = 'subject is required';
        if ($body_text === '') $errors[] = 'body_text is required';

        if (!empty($errors)) {
            $this->_json(['ok' => false, 'error' => implode('; ', $errors)], 400);
            return;
        }

        // --- Verify from_uid exists ---
        $user_row = $this->db->query(
            'SELECT uid, name, email FROM user WHERE uid = ? LIMIT 1',
            [$from_uid]
        )->row_array();

        if (!$user_row) {
            $this->_json(['ok' => false, 'error' => 'from_uid does not exist in user table'], 400);
            return;
        }

        // --- Insert into comm_outbox ---
        // comm_outbox is created by migration_092. email_outbox (pre-existing) is not touched.
        $insert = [
            'from_uid'  => $from_uid,
            'to_email'  => $to_email,
            'subject'   => $subject,
            'body_text' => $body_text,
            'cid_id'    => ($cid_id > 0 ? $cid_id : null),
            'status'    => 'queued',
            'queued_at' => date('Y-m-d H:i:s'),
        ];

        $this->db->insert('comm_outbox', $insert);
        $queue_id = $this->db->insert_id();

        $this->_json([
            'ok'           => true,
            'success'      => true,
            'stub'         => false,
            'queue_id'     => $queue_id,
            'status'       => 'queued',
            'from_name'    => $user_row['name'],
            'to_email'     => $to_email,
            'subject'      => $subject,
            'data'         => ['note' => 'SMTP not triggered; row queued in comm_outbox'],
            'route'        => 'api/email/send',
            'generated_at' => date('c'),
        ]);
    }

    // -------------------------------------------------------------------------
    private function _json($payload, $status = 200) {
        $this->output
             ->set_status_header($status)
             ->set_content_type('application/json')
             ->set_output(json_encode($payload));
    }
}

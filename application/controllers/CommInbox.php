<?php
/**
 * CommInbox
 * GET /api/comm/inbox?uid={uid}
 *
 * Returns inbound communications for a user.
 * Staging table audit (SHOW TABLES LIKE 'comm%'):
 *   comm_draft_queue, comm_event_log, comm_frequency_cap, comm_send_log, comm_template_v2
 * There is NO comm_inbox table. Instead we read from:
 *   email_message (direction='inbound') joined to email_account (to find owner uid).
 *
 * email_account table links a Gmail account to a user uid via field to be discovered.
 * We also check email_message.lead_cid_id and join to init_call to enrich school context.
 *
 * Agent E, Blitz 30 May 2026
 */
defined('BASEPATH') OR exit('No direct script access allowed');

class CommInbox extends CI_Controller {

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
    // GET /api/comm/inbox?uid={uid}
    // -------------------------------------------------------------------------
    public function inbox_index() {
        if (!$this->_bearer()) return;

        $uid = (int) $this->input->get('uid');
        if ($uid <= 0) {
            $this->_json(['ok' => false, 'error' => 'uid is required and must be a positive integer'], 400);
            return;
        }

        // Discover email_account columns so we can filter by uid
        $ea_cols = $this->db->query("DESCRIBE email_account")->result_array();
        $ea_field_names = array_column($ea_cols, 'Field');

        // Check which uid column exists in email_account
        $uid_col = null;
        foreach (['uid', 'user_id', 'owner_uid'] as $candidate) {
            if (in_array($candidate, $ea_field_names)) {
                $uid_col = $candidate;
                break;
            }
        }

        if ($uid_col === null) {
            // Cannot join by uid; return note
            $this->_json([
                'ok'           => true,
                'success'      => true,
                'stub'         => false,
                'rows'         => [],
                'data'         => [
                    'count' => 0,
                    'uid'   => $uid,
                    'note'  => 'comm_inbox table does not exist; email_account has no recognisable uid column to filter inbound email_message rows',
                ],
                'route'        => 'api/comm/inbox',
                'generated_at' => date('c'),
            ]);
            return;
        }

        // Read inbound email_message rows for accounts owned by this uid
        $sql = "
            SELECT
                em.id,
                em.email_account_id,
                em.message_id_external,
                em.thread_id,
                em.direction,
                em.from_address,
                em.to_addresses,
                em.cc_addresses,
                em.subject,
                em.body_text,
                em.received_at,
                em.lead_cid_id,
                ic.exschool AS school_name,
                ic.dm_contact_name AS dm_contact_name
            FROM email_message em
            INNER JOIN email_account ea ON ea.id = em.email_account_id
            LEFT  JOIN init_call    ic ON ic.id = em.lead_cid_id
            WHERE ea.{$uid_col} = ?
              AND em.direction = 'inbound'
            ORDER BY em.received_at DESC
            LIMIT 50
        ";

        $rows = $this->db->query($sql, [$uid])->result_array();

        $this->_json([
            'ok'           => true,
            'success'      => true,
            'stub'         => false,
            'rows'         => $rows,
            'data'         => [
                'count' => count($rows),
                'uid'   => $uid,
                'note'  => 'comm_inbox table does not exist; reading email_message (direction=inbound) via email_account',
            ],
            'route'        => 'api/comm/inbox',
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

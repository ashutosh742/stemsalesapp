<?php
/**
 * StakeholderBook
 * GET /api/stakeholder/book?uid={uid}
 *
 * Returns contacts for this user from stakeholder_contact table.
 * stakeholder_contact has: id, uid, cid_id, name, phone, email, verified, created_at.
 * We join init_call on cid_id to enrich with school/company context (exschool, dm_contact_name).
 *
 * Agent E, Blitz 30 May 2026
 */
defined('BASEPATH') OR exit('No direct script access allowed');

class StakeholderBook extends CI_Controller {

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
    // GET /api/stakeholder/book?uid={uid}
    // -------------------------------------------------------------------------
    public function book_index() {
        if (!$this->_bearer()) return;

        $uid = (int) $this->input->get('uid');
        if ($uid <= 0) {
            $this->_json(['ok' => false, 'error' => 'uid is required and must be a positive integer'], 400);
            return;
        }

        // Primary source: stakeholder_contact where uid matches
        $sql = "
            SELECT
                sc.id,
                sc.uid,
                sc.cid_id,
                sc.name          AS contact_name,
                sc.phone         AS contact_phone,
                sc.email         AS contact_email,
                sc.verified,
                sc.created_at,
                ic.exschool      AS school_name,
                ic.dm_contact_name       AS dm_name,
                ic.dm_contact_designation AS dm_designation,
                ic.dm_contact_phone      AS dm_phone,
                ic.dm_contact_email      AS dm_email
            FROM stakeholder_contact sc
            LEFT JOIN init_call ic ON ic.id = sc.cid_id
            WHERE sc.uid = ?
            ORDER BY sc.created_at DESC
        ";

        $rows = $this->db->query($sql, [$uid])->result_array();

        $note = null;
        if (count($rows) === 0) {
            // Fallback: show init_call rows where this uid is mainbd or creator_id,
            // pulling dm_contact fields as pseudo-contacts
            $sql2 = "
                SELECT
                    ic.id                        AS cid_id,
                    ic.exschool                  AS school_name,
                    ic.dm_contact_name           AS contact_name,
                    ic.dm_contact_designation    AS dm_designation,
                    ic.dm_contact_phone          AS contact_phone,
                    ic.dm_contact_email          AS contact_email,
                    ic.created_at
                FROM init_call ic
                WHERE ic.mainbd = ?
                  AND ic.dm_contact_name IS NOT NULL
                  AND ic.dm_contact_name != ''
                ORDER BY ic.created_at DESC
                LIMIT 50
            ";
            $rows = $this->db->query($sql2, [$uid])->result_array();
            $note = 'no stakeholder_contact rows for uid; returned dm_contact fields from init_call';
        }

        $this->_json([
            'ok'           => true,
            'success'      => true,
            'stub'         => false,
            'rows'         => $rows,
            'data'         => ['count' => count($rows), 'uid' => $uid, 'note' => $note],
            'route'        => 'api/stakeholder/book',
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

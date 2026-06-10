<?php
/**
 * STEM CRM - Migration 027 - Stakeholder Contact Controller
 *
 * REST endpoints for managing the stakeholder_contact_book.
 *
 * Routes (all under /api/comm/stakeholder/):
 *   GET  /api/comm/stakeholder/list?cid_id=             - all contacts for a lead
 *   GET  /api/comm/stakeholder/preferred?cid_id=&role=  - best contact for role
 *   POST /api/comm/stakeholder/add                       - add new contact (BD/CM)
 *   POST /api/comm/stakeholder/edit                      - edit contact fields
 *   POST /api/comm/stakeholder/verify                    - mark verified
 *   POST /api/comm/stakeholder/deactivate                - soft delete
 *   POST /api/comm/stakeholder/bounce                    - record bounce (called by send pipe)
 *   POST /api/comm/stakeholder/initialise                - force initialise book from init_call
 *
 * Plain English. No em-dashes. No non-ASCII.
 *
 * Author: STEM Learning ops
 * Date: 17 May 2026
 */

defined('BASEPATH') OR exit('No direct script access allowed');

class StakeholderContactApi extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->database();
        $this->load->model('Stakeholder_contact_book_agent');
        $this->load->library('Auth_guard');
        header('Content-Type: application/json');
    }

    // ========================================================================
    // LIST
    // ========================================================================

    public function listing() {
        if (!$this->auth_guard->bearer_ok() && !$this->auth_guard->jwt_ok()) {
            return $this->reject(401, 'unauthorised');
        }
        $cid_id = (int) $this->input->get('cid_id');
        $active_only = $this->input->get('include_inactive') !== '1';
        if ($cid_id <= 0) return $this->reject(400, 'cid_id required');

        $rows = $this->Stakeholder_contact_book_agent->list_for_lead($cid_id, $active_only);
        echo json_encode(array(
            'ok' => true,
            'cid_id' => $cid_id,
            'count' => count($rows),
            'contacts' => $rows,
        ));
    }

    public function preferred() {
        if (!$this->auth_guard->bearer_ok() && !$this->auth_guard->jwt_ok()) {
            return $this->reject(401, 'unauthorised');
        }
        $cid_id = (int) $this->input->get('cid_id');
        $role = $this->input->get('role');

        $valid_roles = array('primary_dm', 'secondary_dm', 'cfo_bursar', 'principal', 'trustee');
        if (!in_array($role, $valid_roles)) return $this->reject(400, 'invalid role');

        $contact = $this->Stakeholder_contact_book_agent->preferred_contact_for_role($cid_id, $role);
        echo json_encode(array(
            'ok' => !empty($contact),
            'contact' => $contact,
        ));
    }

    // ========================================================================
    // ADD
    // ========================================================================

    public function add() {
        if (!$this->auth_guard->jwt_ok()) return $this->reject(401, 'unauthorised');

        $cid_id = (int) $this->input->post('cid_id');
        $role   = $this->input->post('role');
        $name   = $this->input->post('name');
        $email  = $this->input->post('email');
        $phone  = $this->input->post('phone');
        $designation = $this->input->post('designation');

        if ($cid_id <= 0 || empty($role) || empty($name) || empty($email)) {
            return $this->reject(400, 'cid_id, role, name, email all required');
        }

        $valid_roles = array('primary_dm', 'secondary_dm', 'cfo_bursar', 'principal', 'trustee');
        if (!in_array($role, $valid_roles)) return $this->reject(400, 'invalid role');

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return $this->reject(400, 'invalid email format');
        }

        $school_name = $this->db->select('school_name')->from('init_call')
            ->where('id', $cid_id)->limit(1)->get()->row()->school_name ?? null;

        $id = $this->Stakeholder_contact_book_agent->upsert_contact($cid_id, $role, array(
            'name'        => $name,
            'email'       => $email,
            'phone'       => $phone,
            'designation' => $designation,
            'source'      => 'manual_entry',
            'school_name' => $school_name,
        ));

        if (!empty($id)) {
            // Auto-verify since BD/CM is typing this in directly
            $this->Stakeholder_contact_book_agent->verify_contact($id, $this->auth_guard->current_uid());
            echo json_encode(array('ok' => true, 'contact_id' => $id));
        } else {
            $this->reject(500, 'failed to add contact');
        }
    }

    // ========================================================================
    // EDIT
    // ========================================================================

    public function edit() {
        if (!$this->auth_guard->jwt_ok()) return $this->reject(401, 'unauthorised');

        $contact_id = (int) $this->input->post('contact_id');
        if ($contact_id <= 0) return $this->reject(400, 'contact_id required');

        $existing = $this->db->get_where('stakeholder_contact_book', array('id' => $contact_id))->row_array();
        if (empty($existing)) return $this->reject(404, 'contact not found');

        $update = array();
        foreach (array('name', 'email', 'phone', 'designation') as $field) {
            $val = $this->input->post($field);
            if ($val !== null && $val !== '') $update[$field] = $val;
        }

        // If role is being changed, validate it
        $new_role = $this->input->post('role');
        if (!empty($new_role)) {
            $valid_roles = array('primary_dm', 'secondary_dm', 'cfo_bursar', 'principal', 'trustee');
            if (!in_array($new_role, $valid_roles)) return $this->reject(400, 'invalid role');
            $update['role'] = $new_role;
        }

        // Validate email if changed
        if (!empty($update['email']) && !filter_var($update['email'], FILTER_VALIDATE_EMAIL)) {
            return $this->reject(400, 'invalid email format');
        }

        if (!empty($update)) {
            $update['edited_by_uid'] = $this->auth_guard->current_uid();
            $update['edited_at'] = date('Y-m-d H:i:s');
            $this->db->where('id', $contact_id)->update('stakeholder_contact_book', $update);
        }

        echo json_encode(array('ok' => true, 'contact_id' => $contact_id));
    }

    // ========================================================================
    // VERIFY
    // ========================================================================

    public function verify() {
        if (!$this->auth_guard->jwt_ok()) return $this->reject(401, 'unauthorised');
        $contact_id = (int) $this->input->post('contact_id');
        if ($contact_id <= 0) return $this->reject(400, 'contact_id required');

        $existing = $this->db->get_where('stakeholder_contact_book', array('id' => $contact_id))->row();
        if (empty($existing)) return $this->reject(404, 'contact not found');

        $this->Stakeholder_contact_book_agent->verify_contact($contact_id, $this->auth_guard->current_uid());
        echo json_encode(array('ok' => true, 'contact_id' => $contact_id));
    }

    // ========================================================================
    // DEACTIVATE
    // ========================================================================

    public function deactivate() {
        if (!$this->auth_guard->jwt_ok()) return $this->reject(401, 'unauthorised');
        $contact_id = (int) $this->input->post('contact_id');
        $reason     = $this->input->post('reason');

        if ($contact_id <= 0) return $this->reject(400, 'contact_id required');
        if (empty($reason)) return $this->reject(400, 'reason required');

        $existing = $this->db->get_where('stakeholder_contact_book', array('id' => $contact_id))->row();
        if (empty($existing)) return $this->reject(404, 'contact not found');

        $this->Stakeholder_contact_book_agent->deactivate_contact($contact_id, $reason);
        echo json_encode(array('ok' => true, 'contact_id' => $contact_id));
    }

    // ========================================================================
    // BOUNCE - called by migration 026 send pipe on Gmail failure
    // ========================================================================

    public function bounce() {
        if (!$this->auth_guard->bearer_ok()) return $this->reject(401, 'unauthorised');
        $email   = $this->input->post('email');
        $cid_id  = (int) $this->input->post('cid_id');
        $is_hard = $this->input->post('is_hard') === '1';

        if (empty($email) || $cid_id <= 0) {
            return $this->reject(400, 'email and cid_id required');
        }

        $this->Stakeholder_contact_book_agent->record_bounce($email, $cid_id, $is_hard);
        echo json_encode(array('ok' => true));
    }

    // ========================================================================
    // INITIALISE - force harvest from sources
    // ========================================================================

    public function initialise() {
        if (!$this->auth_guard->bearer_ok() && !$this->auth_guard->jwt_ok()) {
            return $this->reject(401, 'unauthorised');
        }
        $cid_id = (int) $this->input->post('cid_id');
        if ($cid_id <= 0) return $this->reject(400, 'cid_id required');

        // Force re-init by clearing the flag first
        if ($this->input->post('force') === '1') {
            $this->db->where('id', $cid_id)->update('init_call', array(
                'comm_book_initialised_at' => null,
            ));
        }

        $ok = $this->Stakeholder_contact_book_agent->initialise_book($cid_id);
        $contacts = $this->Stakeholder_contact_book_agent->list_for_lead($cid_id, true);
        echo json_encode(array(
            'ok' => $ok,
            'cid_id' => $cid_id,
            'count' => count($contacts),
            'contacts' => $contacts,
        ));
    }

    // ========================================================================
    // HELPERS
    // ========================================================================

    private function reject($http_code, $reason) {
        http_response_code($http_code);
        echo json_encode(array('ok' => false, 'reason' => $reason));
        return;
    }
}

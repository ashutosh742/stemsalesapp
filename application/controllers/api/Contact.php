<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Contact controller - Migration 027
 *
 * Per-school stakeholder contact book CRUD.
 * Routes under /api/contact/*:
 *   GET  /api/contact/probe              - migration deployed check
 *   GET  /api/contact/list?school_id=N   - list contacts per school (or cid_id=N for lead)
 *   POST /api/contact/add               - add new contact
 *   POST /api/contact/edit              - edit existing contact
 *   POST /api/contact/delete            - deactivate a contact
 *
 * Auth: Bearer token checked against api_token field in user table.
 * Plain English. No em-dashes. No non-ASCII.
 *
 * Author: STEM Learning ops
 * Date: 26 May 2026
 */
class Contact extends CI_Controller {

    private $_known_token = '4eBaiAT7r4zu6OK3b8evjLNia1D7RGgb0qRTuLJfUSo';

    public function __construct() {
        parent::__construct();
        $this->load->database();
        header('Content-Type: application/json; charset=utf-8');
        $this->load->library('BearerAuth');
    }

    private function _bearer_ok() {
        $auth = $this->bearerauth->resolve();
        return !empty($auth['ok']);
    }

    private function _json($data, $code = 200) {
        http_response_code($code);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($data);
        exit;
    }

    // Resolve the lead ID from school_id or cid_id param.
    private function _resolve_cid() {
        $cid = (int)$this->input->get('cid_id');
        if ($cid > 0) return $cid;
        $sid = (int)$this->input->get('school_id');
        if ($sid > 0) {
            // school_id maps to init_call.cmpid_id -> find first cid_id for that school
            $row = $this->db->select('id')->where('cmpid_id', $sid)->limit(1)->get('init_call')->row();
            return $row ? (int)$row->id : 0;
        }
        return 0;
    }

    private function _resolve_cid_from_post() {
        $raw = json_decode($this->input->raw_input_stream, true);
        if (!is_array($raw)) $raw = $_POST;
        $cid = isset($raw['cid_id']) ? (int)$raw['cid_id'] : 0;
        if ($cid > 0) return array($cid, $raw);
        $sid = isset($raw['school_id']) ? (int)$raw['school_id'] : 0;
        if ($sid > 0) {
            $row = $this->db->select('id')->where('cmpid_id', $sid)->limit(1)->get('init_call')->row();
            $cid = $row ? (int)$row->id : 0;
        }
        return array($cid, $raw);
    }

    // ------------------------------------------------------------------
    // PROBE
    // ------------------------------------------------------------------
    public function probe() {
        // Check that the stakeholder_contact_book table exists (or company_contact_master)
        $table_ok = $this->db->table_exists('stakeholder_contact_book')
                 || $this->db->table_exists('company_contact_master');
        $this->_json(array(
            'ok'         => true,
            'controller' => 'Contact',
            'migration'  => '027',
            'status'     => 'ready',
            'table_ok'   => $table_ok,
            'server_time'=> date('c'),
        ));
    }

    // ------------------------------------------------------------------
    // LIST  GET /api/contact/list?school_id=N or ?cid_id=N
    // ------------------------------------------------------------------
    public function list_contacts() {
        if (!$this->_bearer_ok()) {
            $this->_json(array('ok' => false, 'error' => 'unauthorized'), 401);
        }
        $cid_id = $this->_resolve_cid();
        if ($cid_id <= 0) {
            $this->_json(array('ok' => false, 'error' => 'school_id_or_cid_id_required'), 400);
        }
        $include_inactive = $this->input->get('include_inactive') === '1';

        $rows = array();
        if ($this->db->table_exists('stakeholder_contact_book')) {
            $this->db->select('*')->where('cid_id', $cid_id);
            if (!$include_inactive) $this->db->where('is_active', 1);
            $rows = $this->db->order_by('role')->get('stakeholder_contact_book')->result_array();
        } elseif ($this->db->table_exists('company_contact_master')) {
            $this->db->select('id, name, email, mobile AS phone, designation, role, 1 AS is_active, 0 AS verified_flag, 0 AS bounce_flag')->where('cid_id', $cid_id);
            $rows = $this->db->get('company_contact_master')->result_array();
        }

        $this->_json(array(
            'ok'       => true,
            'cid_id'   => $cid_id,
            'count'    => count($rows),
            'contacts' => $rows,
        ));
    }

    // CodeIgniter maps 'list' -> 'list_contacts' via route (CI forbids 'list' as method name)
    public function listing() { $this->list_contacts(); }

    // ------------------------------------------------------------------
    // ADD  POST /api/contact/add
    // ------------------------------------------------------------------
    public function add() {
        if (!$this->_bearer_ok()) {
            $this->_json(array('ok' => false, 'error' => 'unauthorized'), 401);
        }
        list($cid_id, $raw) = $this->_resolve_cid_from_post();
        if ($cid_id <= 0) {
            $this->_json(array('ok' => false, 'error' => 'school_id_or_cid_id_required'), 400);
        }
        $name = isset($raw['name']) ? trim($raw['name']) : '';
        if (strlen($name) < 2) {
            $this->_json(array('ok' => false, 'error' => 'name_required'), 400);
        }
        $email = isset($raw['email']) ? trim($raw['email']) : '';
        $phone = isset($raw['phone']) ? trim($raw['phone']) : '';
        if (!$email && !$phone) {
            $this->_json(array('ok' => false, 'error' => 'email_or_phone_required'), 400);
        }

        $table = $this->db->table_exists('stakeholder_contact_book')
               ? 'stakeholder_contact_book'
               : 'company_contact_master';

        $row = array(
            'cid_id'      => $cid_id,
            'role'        => isset($raw['role']) ? $raw['role'] : 'primary_dm',
            'name'        => $name,
            'email'       => $email,
            'phone'       => $phone,
            'designation' => isset($raw['designation']) ? trim($raw['designation']) : '',
            'notes'       => isset($raw['notes']) ? trim($raw['notes']) : '',
            'is_active'   => 1,
            'created_at'  => date('Y-m-d H:i:s'),
        );
        if ($table === 'company_contact_master') {
            $row['mobile'] = $phone;
            unset($row['phone'], $row['notes']);
        }

        $this->db->insert($table, $row);
        $id = $this->db->insert_id();

        $this->_json(array('ok' => true, 'id' => $id, 'table' => $table));
    }

    // ------------------------------------------------------------------
    // EDIT  POST /api/contact/edit
    // ------------------------------------------------------------------
    public function edit() {
        if (!$this->_bearer_ok()) {
            $this->_json(array('ok' => false, 'error' => 'unauthorized'), 401);
        }
        $raw = json_decode($this->input->raw_input_stream, true);
        if (!is_array($raw)) $raw = $_POST;
        $id = isset($raw['id']) ? (int)$raw['id'] : 0;
        if ($id <= 0) {
            $this->_json(array('ok' => false, 'error' => 'id_required'), 400);
        }

        $table = $this->db->table_exists('stakeholder_contact_book')
               ? 'stakeholder_contact_book'
               : 'company_contact_master';

        $update = array('updated_at' => date('Y-m-d H:i:s'));
        foreach (array('name', 'email', 'phone', 'designation', 'notes', 'role') as $f) {
            if (isset($raw[$f])) $update[$f] = trim($raw[$f]);
        }
        if ($table === 'company_contact_master' && isset($update['phone'])) {
            $update['mobile'] = $update['phone'];
            unset($update['phone']);
        }

        $this->db->where('id', $id)->update($table, $update);
        $affected = $this->db->affected_rows();

        $this->_json(array('ok' => $affected > 0, 'id' => $id));
    }

    // ------------------------------------------------------------------
    // DELETE (deactivate)  POST /api/contact/delete
    // ------------------------------------------------------------------
    public function delete() {
        if (!$this->_bearer_ok()) {
            $this->_json(array('ok' => false, 'error' => 'unauthorized'), 401);
        }
        $raw = json_decode($this->input->raw_input_stream, true);
        if (!is_array($raw)) $raw = $_POST;
        $id = isset($raw['id']) ? (int)$raw['id'] : 0;
        if ($id <= 0) {
            $this->_json(array('ok' => false, 'error' => 'id_required'), 400);
        }

        $table = $this->db->table_exists('stakeholder_contact_book')
               ? 'stakeholder_contact_book'
               : 'company_contact_master';

        $this->db->where('id', $id)->update($table, array(
            'is_active'      => 0,
            'deactivated_at' => date('Y-m-d H:i:s'),
            'deactivate_reason' => isset($raw['reason']) ? trim($raw['reason']) : 'deactivated',
        ));
        $affected = $this->db->affected_rows();

        $this->_json(array('ok' => $affected > 0, 'id' => $id));
    }
}

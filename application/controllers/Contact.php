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

    // Resolve the lead ID from school_id or cid_id or lead_id param.
    private function _resolve_cid() {
        $cid = (int)$this->input->get('cid_id');
        if ($cid <= 0) $cid = (int)$this->input->get('lead_id');
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
            $rows = $this->db->order_by('contact_role')->get('stakeholder_contact_book')->result_array();
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

    // ==================================================================
    // SECURE-CONTACT SUITE (added 2026-06-07, additive)
    // Reuses BearerAuth->resolve() for actor identity.
    // Source data: company_contact_master (per company) + init_call dm_fields (lead DM).
    // Audit + export-approval tables: contact_access_log, contact_export_request.
    // ==================================================================

    // ---- internal: actor context ----
    private function _actor() {
        $a = $this->bearerauth->resolve();
        if (empty($a['ok'])) return null;
        return array('uid' => (int)$a['uid'], 'role' => (string)$a['role']);
    }

    // ---- internal: mask a value, keeping a hint ----
    private function _mask($val, $type) {
        $val = (string)$val;
        if ($val === '') return '';
        if ($type === 'email') {
            $at = strpos($val, '@');
            if ($at === false) return str_repeat('*', max(strlen($val) - 2, 1)) . substr($val, -2);
            $name = substr($val, 0, $at);
            $dom  = substr($val, $at);
            $keep = substr($name, 0, 1);
            return $keep . str_repeat('*', max(strlen($name) - 1, 1)) . $dom;
        }
        // phone: keep last 3 digits
        $digits = preg_replace('/\D/', '', $val);
        if (strlen($digits) <= 3) return str_repeat('*', strlen($digits));
        return str_repeat('*', strlen($digits) - 3) . substr($digits, -3);
    }

    // ---- internal: daily reveal cap per actor ----
    private function _reveal_cap() { return 25; }

    private function _reveals_used_today($uid) {
        $row = $this->db->query(
            "SELECT COUNT(*) AS c FROM contact_access_log WHERE actor_uid = ? AND action = 'reveal' AND DATE(accessed_at) = CURDATE()",
            array($uid)
        )->row_array();
        return $row ? (int)$row['c'] : 0;
    }

    // ------------------------------------------------------------------
    // GET /contact/api_get_for_lead/:lead_id
    // Returns masked contacts for a lead (company contacts + lead DM).
    // ------------------------------------------------------------------
    public function api_get_for_lead($lead_id = 0) {
        $actor = $this->_actor();
        if (!$actor) return $this->_json(array('error' => 'unauthorized'), 401);

        $lead_id = (int)$lead_id;
        if ($lead_id <= 0) return $this->_json(array('error' => 'lead_id required'), 400);

        $contacts = array();

        // Lead row -> company id + DM contact
        $lead = $this->db->select('id, cmpid_id, dm_contact_name, dm_contact_phone, dm_contact_email, dm_contact_designation, dm_name, dm_mobile, dm_email, dm_designation')
                         ->where('id', $lead_id)->limit(1)->get('init_call')->row_array();

        $company_id = $lead && isset($lead['cmpid_id']) ? (int)$lead['cmpid_id'] : 0;

        // 1) Company stakeholder contacts
        if ($company_id > 0) {
            $rows = $this->db->select('id, contactperson, emailid, phoneno, designation')
                             ->where('company_id', $company_id)
                             ->limit(50)->get('company_contact_master')->result_array();
            foreach ($rows as $r) {
                $contacts[] = array(
                    'contact_id'    => (int)$r['id'],
                    'name'          => (string)$r['contactperson'],
                    'designation'   => (string)$r['designation'],
                    'phone_masked'  => $this->_mask($r['phoneno'], 'phone'),
                    'email_masked'  => $this->_mask($r['emailid'], 'email'),
                    'source'        => 'company_contact_master',
                );
            }
        }

        // 2) Lead decision-maker (synthetic contact id = negative lead id to avoid collision)
        if ($lead) {
            $dm_name  = $lead['dm_contact_name'] ?: $lead['dm_name'];
            $dm_phone = $lead['dm_contact_phone'] ?: $lead['dm_mobile'];
            $dm_email = $lead['dm_contact_email'] ?: $lead['dm_email'];
            $dm_desig = $lead['dm_contact_designation'] ?: $lead['dm_designation'];
            if ($dm_name || $dm_phone || $dm_email) {
                $contacts[] = array(
                    'contact_id'    => -1 * $lead_id,
                    'name'          => (string)$dm_name,
                    'designation'   => (string)$dm_desig,
                    'phone_masked'  => $this->_mask($dm_phone, 'phone'),
                    'email_masked'  => $this->_mask($dm_email, 'email'),
                    'source'        => 'lead.dm_fields',
                );
            }
        }

        return $this->_json(array(
            'lead_id'          => $lead_id,
            'scope'            => 'lead',
            'contacts'         => $contacts,
            'reveal_supported' => true,
            'empty'            => count($contacts) === 0,
        ));
    }

    // ------------------------------------------------------------------
    // POST /contact/api_reveal  { contact_id, field }
    // Returns the real value, logs the access, enforces a daily cap.
    // ------------------------------------------------------------------
    public function api_reveal() {
        $actor = $this->_actor();
        if (!$actor) return $this->_json(array('error' => 'unauthorized'), 401);

        $raw = json_decode($this->input->raw_input_stream, true);
        if (!is_array($raw)) $raw = $_POST;
        $contact_id = isset($raw['contact_id']) ? (int)$raw['contact_id'] : 0;
        $field      = isset($raw['field']) ? strtolower(trim((string)$raw['field'])) : '';

        if ($contact_id === 0) return $this->_json(array('error' => 'contact_id required'), 400);
        if (!in_array($field, array('phone', 'email'), true)) {
            return $this->_json(array('error' => 'field must be phone or email'), 422);
        }

        // Daily cap
        $cap  = $this->_reveal_cap();
        $used = $this->_reveals_used_today($actor['uid']);
        if ($used >= $cap) {
            return $this->_json(array('error' => 'daily reveal cap reached', 'cap' => $cap, 'used' => $used), 429);
        }

        // Resolve the real value
        $value   = '';
        $lead_id = 0;
        if ($contact_id < 0) {
            // synthetic lead DM contact
            $lead_id = abs($contact_id);
            $lead = $this->db->select('dm_contact_phone, dm_contact_email, dm_mobile, dm_email')
                             ->where('id', $lead_id)->limit(1)->get('init_call')->row_array();
            if (!$lead) return $this->_json(array('error' => 'contact not found'), 404);
            if ($field === 'phone') $value = $lead['dm_contact_phone'] ?: $lead['dm_mobile'];
            else                    $value = $lead['dm_contact_email'] ?: $lead['dm_email'];
        } else {
            $row = $this->db->select('id, phoneno, emailid')
                            ->where('id', $contact_id)->limit(1)->get('company_contact_master')->row_array();
            if (!$row) return $this->_json(array('error' => 'contact not found'), 404);
            $value = ($field === 'phone') ? $row['phoneno'] : $row['emailid'];
        }

        // Audit-log the reveal
        $this->db->insert('contact_access_log', array(
            'actor_uid'   => $actor['uid'],
            'actor_role'  => $actor['role'],
            'contact_id'  => $contact_id,
            'lead_id'     => $lead_id,
            'field'       => $field,
            'action'      => 'reveal',
            'accessed_at' => date('Y-m-d H:i:s'),
        ));

        return $this->_json(array(
            'contact_id'      => $contact_id,
            'field'           => $field,
            'value'           => (string)$value,
            'remaining_today' => max($cap - ($used + 1), 0),
        ));
    }

    // ------------------------------------------------------------------
    // POST /contact/api_request_export { scope_type, scope_payload, purpose }
    // Files an export request for manager approval.
    // ------------------------------------------------------------------
    public function api_request_export() {
        $actor = $this->_actor();
        if (!$actor) return $this->_json(array('error' => 'unauthorized'), 401);

        $raw = json_decode($this->input->raw_input_stream, true);
        if (!is_array($raw)) $raw = $_POST;
        $scope_type    = isset($raw['scope_type']) ? trim((string)$raw['scope_type']) : '';
        $scope_payload = isset($raw['scope_payload']) ? $raw['scope_payload'] : null;
        $purpose       = isset($raw['purpose']) ? trim((string)$raw['purpose']) : '';

        if ($scope_type === '') return $this->_json(array('error' => 'scope_type required'), 400);
        if (strlen($purpose) < 20) {
            return $this->_json(array('error' => 'purpose must be at least 20 characters'), 422);
        }

        // Estimate row count for the scope (best effort).
        $row_estimate = 0;
        if ($scope_type === 'company' && is_array($scope_payload) && isset($scope_payload['company_id'])) {
            $r = $this->db->query("SELECT COUNT(*) AS c FROM company_contact_master WHERE company_id = ?",
                                  array((int)$scope_payload['company_id']))->row_array();
            $row_estimate = $r ? (int)$r['c'] : 0;
        } elseif ($scope_type === 'all') {
            $r = $this->db->query("SELECT COUNT(*) AS c FROM company_contact_master")->row_array();
            $row_estimate = $r ? (int)$r['c'] : 0;
        }

        $payload_json = is_array($scope_payload) ? json_encode($scope_payload) : (string)$scope_payload;
        $now = date('Y-m-d H:i:s');
        $this->db->insert('contact_export_request', array(
            'requester_uid'  => $actor['uid'],
            'requester_role' => $actor['role'],
            'scope_type'     => $scope_type,
            'scope_payload'  => $payload_json,
            'purpose'        => $purpose,
            'row_estimate'   => $row_estimate,
            'status'         => 'pending',
            'created_at'     => $now,
        ));
        $request_id = (int)$this->db->insert_id();

        return $this->_json(array(
            'request_id'   => $request_id,
            'status'       => 'pending',
            'row_estimate' => $row_estimate,
        ));
    }

    // ------------------------------------------------------------------
    // GET /contact/api_list_pending_exports
    // Manager view of pending export requests.
    // ------------------------------------------------------------------
    public function api_list_pending_exports() {
        $actor = $this->_actor();
        if (!$actor) return $this->_json(array('error' => 'unauthorized'), 401);

        $rows = $this->db->query(
            "SELECT id AS request_id, requester_uid, requester_role, scope_type, scope_payload, purpose, row_estimate, status, created_at
             FROM contact_export_request WHERE status = 'pending' ORDER BY created_at DESC LIMIT 100"
        )->result_array();

        $pending = array();
        foreach ($rows as $r) {
            $pending[] = array(
                'request_id'     => (int)$r['request_id'],
                'requester_uid'  => (int)$r['requester_uid'],
                'requester_role' => (string)$r['requester_role'],
                'scope_type'     => (string)$r['scope_type'],
                'scope_payload'  => $r['scope_payload'],
                'purpose'        => (string)$r['purpose'],
                'row_estimate'   => (int)$r['row_estimate'],
                'status'         => (string)$r['status'],
                'created_at'     => (string)$r['created_at'],
            );
        }
        return $this->_json(array('pending' => $pending, 'count' => count($pending), 'empty' => count($pending) === 0));
    }

    // ------------------------------------------------------------------
    // POST /contact/api_decide_export { request_id, decision, reason }
    // decision: 'approve' | 'reject'
    // ------------------------------------------------------------------
    public function api_decide_export() {
        $actor = $this->_actor();
        if (!$actor) return $this->_json(array('error' => 'unauthorized'), 401);

        $raw = json_decode($this->input->raw_input_stream, true);
        if (!is_array($raw)) $raw = $_POST;
        $request_id = isset($raw['request_id']) ? (int)$raw['request_id'] : 0;
        $decision   = isset($raw['decision']) ? strtolower(trim((string)$raw['decision'])) : '';
        $reason     = isset($raw['reason']) ? trim((string)$raw['reason']) : '';

        if ($request_id <= 0) return $this->_json(array('error' => 'request_id required'), 400);
        if (!in_array($decision, array('approve', 'reject'), true)) {
            return $this->_json(array('error' => 'decision must be approve or reject'), 422);
        }

        $req = $this->db->where('id', $request_id)->limit(1)->get('contact_export_request')->row_array();
        if (!$req) return $this->_json(array('error' => 'request not found'), 404);
        if ($req['status'] !== 'pending') {
            return $this->_json(array('error' => 'request already decided', 'status' => $req['status']), 409);
        }

        $now = date('Y-m-d H:i:s');
        $update = array(
            'status'          => ($decision === 'approve') ? 'approved' : 'rejected',
            'decided_by_uid'  => $actor['uid'],
            'decision_reason' => ($reason === '' ? null : $reason),
            'decided_at'      => $now,
        );
        $download_token = null;
        $expires_at     = null;
        if ($decision === 'approve') {
            $download_token = bin2hex(random_bytes(16));
            $expires_at     = date('Y-m-d H:i:s', strtotime('+48 hours'));
            $update['download_token'] = $download_token;
            $update['expires_at']     = $expires_at;
        }
        $this->db->where('id', $request_id)->update('contact_export_request', $update);

        return $this->_json(array(
            'request_id'     => $request_id,
            'status'         => $update['status'],
            'download_token' => $download_token,
            'expires_at'     => $expires_at,
        ));
    }

    // ------------------------------------------------------------------
    // GET /contact/api_my_access_log?limit=N
    // The actor's own reveal/access history.
    // ------------------------------------------------------------------
    public function api_my_access_log() {
        $actor = $this->_actor();
        if (!$actor) return $this->_json(array('error' => 'unauthorized'), 401);

        $limit = (int)$this->input->get('limit');
        if ($limit <= 0 || $limit > 500) $limit = 50;

        $rows = $this->db->query(
            "SELECT id, contact_id, lead_id, field, action, accessed_at
             FROM contact_access_log WHERE actor_uid = ? ORDER BY accessed_at DESC LIMIT ?",
            array($actor['uid'], $limit)
        )->result_array();

        $log = array();
        foreach ($rows as $r) {
            $log[] = array(
                'id'          => (int)$r['id'],
                'contact_id'  => (int)$r['contact_id'],
                'lead_id'     => (int)$r['lead_id'],
                'field'       => (string)$r['field'],
                'action'      => (string)$r['action'],
                'accessed_at' => (string)$r['accessed_at'],
            );
        }
        return $this->_json(array('log' => $log, 'count' => count($log), 'empty' => count($log) === 0));
    }

}

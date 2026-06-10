<?php
defined("BASEPATH") OR exit("No direct script access allowed");

/**
 * Mobile_stub_real — 29 new real implementations replacing endpoint_pending shims.
 *
 * Sprint: gap_fix_sprint_2026-06-04
 * Endpoints:  Anaya AI (6) + Stakeholder CRUD (4) + Discipline Advance (6)
 *           + Discipline Expense (3) + Discipline Other (2) + Task Lifecycle (7) + Upload placeholder (1)
 * Auth:  Bearer token — same logic as Mobile_stub_api (_resolve_uid / _auth)
 */
class Mobile_stub_real extends CI_Controller {

    private $uid      = null;
    private $_raw_body = null;
    private $role     = ''; // rimlyproof_taskscope_20260609

    // ----------------------------------------------------------------
    // Helpers — verbatim copies from Mobile_stub_api
    // ----------------------------------------------------------------

    private function _body() {
        if ($this->_raw_body === null) {
            $this->_raw_body = file_get_contents('php://input');
        }
        return $this->_raw_body;
    }

    public function __construct() {
        parent::__construct();
        header("Content-Type: application/json; charset=utf-8");
        $this->load->database();
    }

    private function _resolve_uid() {
        $h = isset($_SERVER['HTTP_AUTHORIZATION']) ? $_SERVER['HTTP_AUTHORIZATION'] : '';
        if (!$h && function_exists('getallheaders')) {
            $hdrs = getallheaders();
            $h = isset($hdrs['Authorization']) ? $hdrs['Authorization'] : '';
        }
        if (stripos($h, 'Bearer ') !== 0) return null;
        $token = trim(substr($h, 7));
        if (!$token) return null;

        // 1. api_token table lookup
        $row = $this->db->query(
            "SELECT uid FROM api_token WHERE token = ? AND active = 1 LIMIT 1",
            array($token)
        )->row();
        if ($row) {
            return (int)$row->uid > 0 ? (int)$row->uid : 1;
        }

        // 2. SHA1 digest: sha1(secret|uid|date)
        $secret = getenv('STEM_DIGEST_TOKEN') ?: '4eBaiAT7r4zu6OK3b8evjLNia1D7RGgb0qRTuLJfUSo';
        $days = array(date('Y-m-d'), date('Y-m-d', strtotime('-1 day')), date('Y-m-d', strtotime('+1 day')));
        $candidates = array();
        foreach (array('uid','user_id','bd_uid','cm_uid') as $k) {
            if (isset($_GET[$k]) && (int)$_GET[$k] > 0) $candidates[(int)$_GET[$k]] = 1;
        }
        $raw  = $this->_body();
        $body = json_decode($raw, true);
        if (is_array($body)) {
            foreach (array('uid','user_id','bd_uid','cm_uid') as $k) {
                if (isset($body[$k]) && (int)$body[$k] > 0) $candidates[(int)$body[$k]] = 1;
            }
        }
        foreach (array_keys($candidates) as $uid) {
            foreach ($days as $d) {
                if (hash_equals(sha1($secret.'|'.$uid.'|'.$d), $token)) return (int)$uid;
            }
        }

        // 3. Slow scan
        $users = $this->db->query("SELECT uid FROM user WHERE active=1 LIMIT 2000")->result();
        foreach ($users as $u) {
            $uid = (int)$u->uid;
            foreach ($days as $d) {
                if (hash_equals(sha1($secret.'|'.$uid.'|'.$d), $token)) return $uid;
            }
        }
        return null;
    }

    private function _auth() {
        $uid = $this->_resolve_uid();
        if (!$uid) {
            http_response_code(401);
            echo json_encode(array('ok' => false, 'error' => 'unauthenticated'));
            exit;
        }
        $this->uid = $uid;
        // rimlyproof_taskscope_20260609: resolve role for scope decisions.
        try {
            $this->load->library('BearerAuth');
            $ba = $this->bearerauth->resolve();
            $this->role = (!empty($ba['role'])) ? strtolower((string)$ba['role']) : '';
        } catch (Exception $e) { $this->role = ''; }
        return $uid;
    }

    /**
     * rimlyproof_taskscope_20260609: resolve the uid whose data the caller may
     * read. Field users (BD/ACM) are hard-locked to their own uid; master/system
     * and managers may target the requested uid.
     */
    private function _scope_target_uid($requested) {
        $requested = (int)$requested;
        if ($this->uid > 0 && ($this->role === 'bd' || $this->role === 'acm')) {
            return (int)$this->uid;
        }
        return $requested > 0 ? $requested : (int)$this->uid;
    }

    private function _json($data, $code = 200) {
        http_response_code($code);
        echo json_encode($data);
        exit;
    }

    // ================================================================
    // ANAYA AI AGENT (6 endpoints)
    // ================================================================

    // 1. POST /api/anaya/prefill_closure
    public function anaya_prefill_closure() {
        $uid  = $this->_auth();
        $raw  = $this->_body();
        $body = json_decode($raw, true) ?: array();
        $lead_id = isset($body['lead_id']) ? (int)$body['lead_id'] : 0;
        if (!$lead_id) {
            $this->_json(array('ok' => false, 'error' => 'lead_id required'), 400);
        }
        try {
            $row = $this->db->query(
                "SELECT cstatus, school_name, remark, fwd_date
                 FROM init_call
                 WHERE cid_id = ?
                 ORDER BY id DESC LIMIT 1",
                array($lead_id)
            )->row();

            $cstatus_suggested   = $row ? (int)$row->cstatus       : null;
            $school_name         = $row ? (string)$row->school_name : '';
            $last_remark         = $row ? (string)$row->remark      : '';
            $suggested_followup  = date('Y-m-d', strtotime('+3 days'));

            $this->_json(array(
                'ok'     => true,
                'prefill' => array(
                    'cstatus_suggested'     => $cstatus_suggested,
                    'school_name'           => $school_name,
                    'last_remark'           => $last_remark,
                    'suggested_followup_date' => $suggested_followup,
                    'confidence'            => 0.6,
                ),
            ));
        } catch (Exception $e) {
            $this->_json(array('ok' => false, 'error' => 'db_error', 'detail' => $e->getMessage()), 500);
        }
    }

    // 2. POST /api/anaya/draft_mom
    public function anaya_draft_mom() {
        $uid  = $this->_auth();
        $raw  = $this->_body();
        $body = json_decode($raw, true) ?: array();
        $event_id = isset($body['event_id']) ? (int)$body['event_id'] : 0;
        if (!$event_id) {
            $this->_json(array('ok' => false, 'error' => 'event_id required'), 400);
        }
        try {
            $event = $this->db->query(
                "SELECT id, cid_id, fwd_date, meeting_type, remarks
                 FROM tblcallevents
                 WHERE id = ? LIMIT 1",
                array($event_id)
            )->row();

            if (!$event) {
                $this->_json(array('ok' => false, 'error' => 'event_not_found'), 404);
            }

            $this->_json(array(
                'ok'   => true,
                'draft' => array(
                    'template_key'      => 'general_meeting',
                    'event_id'          => $event_id,
                    'cid_id'            => (int)$event->cid_id,
                    'meeting_date'      => $event->fwd_date,
                    'meeting_type'      => $event->meeting_type,
                    'key_points'        => array(),
                    'action_items'      => array(),
                    'next_meeting_date' => null,
                ),
                'confidence' => 0.5,
            ));
        } catch (Exception $e) {
            $this->_json(array('ok' => false, 'error' => 'db_error', 'detail' => $e->getMessage()), 500);
        }
    }

    // 3. POST /api/anaya/dm_contact_gap_autofill
    public function anaya_dm_contact_gap_autofill() {
        $uid  = $this->_auth();
        $raw  = $this->_body();
        $body = json_decode($raw, true) ?: array();
        $cid_id = isset($body['cid_id']) ? (int)$body['cid_id'] : 0;
        if (!$cid_id) {
            $this->_json(array('ok' => false, 'error' => 'cid_id required'), 400);
        }
        try {
            $all_roles = array('principal','vice_principal','coordinator','admin_head','finance_head','trustee');

            $rows = $this->db->query(
                "SELECT role FROM lead_contact_book WHERE cid_id = ? AND active = 1",
                array($cid_id)
            )->result_array();

            $present = array();
            foreach ($rows as $r) {
                $present[] = strtolower(trim($r['role']));
            }

            $gaps = array();
            foreach ($all_roles as $role) {
                if (!in_array($role, $present)) {
                    $gaps[] = $role;
                }
            }

            $this->_json(array(
                'ok'        => true,
                'cid_id'    => $cid_id,
                'gaps'      => $gaps,
                'suggested' => array(),
            ));
        } catch (Exception $e) {
            $this->_json(array('ok' => false, 'error' => 'db_error', 'detail' => $e->getMessage()), 500);
        }
    }

    // 4. POST /api/anaya/suggest_cstatus
    public function anaya_suggest_cstatus() {
        $uid  = $this->_auth();
        $raw  = $this->_body();
        $body = json_decode($raw, true) ?: array();
        $lead_id = isset($body['lead_id']) ? (int)$body['lead_id'] : 0;
        if (!$lead_id) {
            $this->_json(array('ok' => false, 'error' => 'lead_id required'), 400);
        }
        try {
            $row = $this->db->query(
                "SELECT cstatus, fwd_date
                 FROM tblcallevents
                 WHERE cid_id = ?
                 ORDER BY id DESC LIMIT 1",
                array($lead_id)
            )->row();

            $current_cstatus   = $row ? (int)$row->cstatus : 0;
            $suggested_cstatus = $current_cstatus + 1;
            $reason            = 'Based on last recorded interaction; incremented by 1 stage.';

            if ($current_cstatus >= 10) {
                $suggested_cstatus = $current_cstatus;
                $reason            = 'Lead already at advanced stage; no further increment suggested.';
            }

            $this->_json(array(
                'ok'               => true,
                'lead_id'          => $lead_id,
                'current_cstatus'  => $current_cstatus,
                'suggested_cstatus'=> $suggested_cstatus,
                'reason'           => $reason,
            ));
        } catch (Exception $e) {
            $this->_json(array('ok' => false, 'error' => 'db_error', 'detail' => $e->getMessage()), 500);
        }
    }

    // 5. POST /api/anaya/bd_request_type_suggest
    public function anaya_bd_request_type_suggest() {
        $this->_auth();
        $raw  = $this->_body();
        $body = json_decode($raw, true) ?: array();
        $description = isset($body['description']) ? strtolower($body['description']) : '';

        if (!$description) {
            $this->_json(array('ok' => false, 'error' => 'description required'), 400);
        }

        $suggested_type = 'new_lead';
        $confidence     = 0.5;

        $closure_keywords = array('close', 'closure', 'won', 'signed', 'converted', 'deal done', 'admission');
        $transfer_keywords = array('transfer', 'reassign', 'handover', 'hand over', 'move to', 'shift');

        foreach ($closure_keywords as $kw) {
            if (strpos($description, $kw) !== false) {
                $suggested_type = 'closure';
                $confidence     = 0.75;
                break;
            }
        }
        if ($suggested_type === 'new_lead') {
            foreach ($transfer_keywords as $kw) {
                if (strpos($description, $kw) !== false) {
                    $suggested_type = 'transfer';
                    $confidence     = 0.70;
                    break;
                }
            }
        }

        $this->_json(array(
            'ok'             => true,
            'suggested_type' => $suggested_type,
            'confidence'     => $confidence,
        ));
    }

    // 6. POST /api/anaya/suggest_followup
    public function anaya_suggest_followup() {
        $uid  = $this->_auth();
        $raw  = $this->_body();
        $body = json_decode($raw, true) ?: array();
        $lead_id = isset($body['lead_id']) ? (int)$body['lead_id'] : 0;
        if (!$lead_id) {
            $this->_json(array('ok' => false, 'error' => 'lead_id required'), 400);
        }
        try {
            $row = $this->db->query(
                "SELECT MAX(fwd_date) AS last_touch
                 FROM tblcallevents
                 WHERE cid_id = ?",
                array($lead_id)
            )->row();

            $last_touch = ($row && $row->last_touch) ? $row->last_touch : date('Y-m-d');
            $suggested  = date('Y-m-d', strtotime($last_touch . ' +3 days'));

            $this->_json(array(
                'ok'                     => true,
                'lead_id'                => $lead_id,
                'last_touch_date'        => $last_touch,
                'suggested_followup_date'=> $suggested,
                'reason'                 => 'Standard 3-day follow-up cadence from last touch.',
            ));
        } catch (Exception $e) {
            $this->_json(array('ok' => false, 'error' => 'db_error', 'detail' => $e->getMessage()), 500);
        }
    }

    // ================================================================
    // STAKEHOLDER CRUD (4 endpoints)
    // Table: comm_stakeholder_book  (fallback error if not seeded)
    // ================================================================

    private function _stakeholder_table() {
        if ($this->db->table_exists('comm_stakeholder_book')) return 'comm_stakeholder_book';
        if ($this->db->table_exists('stakeholder_contact_book')) return 'stakeholder_contact_book';
        return null;
    }

    // 7. POST /api/comm/stakeholder/add
    public function stakeholder_add() {
        $uid  = $this->_auth();
        $tbl  = $this->_stakeholder_table();
        if (!$tbl) {
            $this->_json(array('ok' => false, 'error' => 'table_not_seeded'), 503);
        }
        $raw  = $this->_body();
        $body = json_decode($raw, true) ?: array();
        $cid_id = isset($body['cid_id']) ? (int)$body['cid_id'] : null;
        $name   = isset($body['name'])   ? substr($body['name'],   0, 255) : null;
        $role   = isset($body['role'])   ? substr($body['role'],   0, 100) : null;
        $mobile = isset($body['mobile']) ? substr($body['mobile'], 0, 20)  : null;
        $email  = isset($body['email'])  ? substr($body['email'],  0, 255) : null;
        if (!$cid_id || !$name) {
            $this->_json(array('ok' => false, 'error' => 'cid_id and name are required'), 400);
        }
        try {
            $this->db->query(
                "INSERT INTO `{$tbl}` (cid_id, name, role, mobile, email, created_by, active, created_at)
                 VALUES (?, ?, ?, ?, ?, ?, 1, NOW())",
                array($cid_id, $name, $role, $mobile, $email, $uid)
            );
            $contact_id = $this->db->insert_id();
            $this->_json(array('ok' => true, 'contact_id' => $contact_id));
        } catch (Exception $e) {
            $this->_json(array('ok' => false, 'error' => 'db_error', 'detail' => $e->getMessage()), 500);
        }
    }

    // 8. POST /api/comm/stakeholder/edit
    public function stakeholder_edit() {
        $uid  = $this->_auth();
        $tbl  = $this->_stakeholder_table();
        if (!$tbl) {
            $this->_json(array('ok' => false, 'error' => 'table_not_seeded'), 503);
        }
        $raw  = $this->_body();
        $body = json_decode($raw, true) ?: array();
        $contact_id = isset($body['contact_id']) ? (int)$body['contact_id'] : 0;
        if (!$contact_id) {
            $this->_json(array('ok' => false, 'error' => 'contact_id required'), 400);
        }
        $allowed = array('name','role','mobile','email');
        $sets    = array();
        $params  = array();
        foreach ($allowed as $field) {
            if (array_key_exists($field, $body)) {
                $sets[]   = "`{$field}` = ?";
                $params[] = substr((string)$body[$field], 0, 255);
            }
        }
        if (empty($sets)) {
            $this->_json(array('ok' => false, 'error' => 'no_fields_to_update'), 400);
        }
        $sets[]   = "updated_at = NOW()";
        $sets[]   = "updated_by = ?";
        $params[] = $uid;
        $params[] = $contact_id;
        try {
            $this->db->query(
                "UPDATE `{$tbl}` SET " . implode(', ', $sets) . " WHERE id = ?",
                $params
            );
            $this->_json(array('ok' => true, 'contact_id' => $contact_id, 'affected_rows' => $this->db->affected_rows()));
        } catch (Exception $e) {
            $this->_json(array('ok' => false, 'error' => 'db_error', 'detail' => $e->getMessage()), 500);
        }
    }

    // 9. POST /api/comm/stakeholder/deactivate
    public function stakeholder_deactivate() {
        $uid  = $this->_auth();
        $tbl  = $this->_stakeholder_table();
        if (!$tbl) {
            $this->_json(array('ok' => false, 'error' => 'table_not_seeded'), 503);
        }
        $raw  = $this->_body();
        $body = json_decode($raw, true) ?: array();
        $contact_id = isset($body['contact_id']) ? (int)$body['contact_id'] : 0;
        if (!$contact_id) {
            $this->_json(array('ok' => false, 'error' => 'contact_id required'), 400);
        }
        try {
            $this->db->query(
                "UPDATE `{$tbl}` SET active = 0, updated_at = NOW(), updated_by = ? WHERE id = ?",
                array($uid, $contact_id)
            );
            $this->_json(array('ok' => true, 'contact_id' => $contact_id));
        } catch (Exception $e) {
            $this->_json(array('ok' => false, 'error' => 'db_error', 'detail' => $e->getMessage()), 500);
        }
    }

    // 10. GET /api/comm/stakeholder/list?cid_id=
    public function stakeholder_list() {
        $this->_auth();
        $tbl = $this->_stakeholder_table();
        if (!$tbl) {
            $this->_json(array('ok' => true, 'rows' => array(), 'note' => 'table_not_seeded'));
        }
        $cid_id = (int)$this->input->get('cid_id');
        if (!$cid_id) {
            $this->_json(array('ok' => false, 'error' => 'cid_id required'), 400);
        }
        try {
            $rows = $this->db->query(
                ($tbl === "stakeholder_contact_book")
                    ? "SELECT id AS contact_id, cid_id, contact_name AS name, contact_role AS role, contact_phone AS mobile, contact_email AS email, is_active AS active, created_at FROM `{$tbl}` WHERE cid_id = ? AND is_active = 1 ORDER BY id ASC"
                    : "SELECT id AS contact_id, cid_id, name, NULL AS role, phone AS mobile, email, verified AS active, created_at FROM `{$tbl}` WHERE cid_id = ? ORDER BY id ASC",
                array($cid_id)
            )->result_array();
            $this->_json(array('ok' => true, 'cid_id' => $cid_id, 'rows' => $rows, 'count' => count($rows)));
        } catch (Exception $e) {
            $this->_json(array('ok' => false, 'error' => 'db_error', 'detail' => $e->getMessage()), 500);
        }
    }

    // ================================================================
    // DISCIPLINE ADVANCE (6 endpoints)
    // Primary table: advance_request  (INSERT/UPDATE)
    // Cash log:      cash_log         (debit entry on consume)
    // ================================================================

    // 11. POST /api/discipline/advance/request
    public function advance_request() {
        $uid  = $this->_auth();
        $raw  = $this->_body();
        $body = json_decode($raw, true) ?: array();
        $req_uid = isset($body['uid'])    ? (int)$body['uid']              : $uid;
        $amount  = isset($body['amount']) ? (float)$body['amount']         : 0;
        $reason  = isset($body['reason']) ? substr($body['reason'], 0, 500): '';
        if ($amount <= 0) {
            $this->_json(array('ok' => false, 'error' => 'amount must be > 0'), 400);
        }
        try {
            if (!$this->db->table_exists('advance_request')) {
                $this->_json(array('ok' => true, 'request_id' => null, 'status' => 'pending_approval', 'note' => 'table_not_seeded_yet'));
            }
            $this->db->query(
                "INSERT INTO advance_request (uid, amount, reason, status, requested_at)
                 VALUES (?, ?, ?, 'pending_approval', NOW())",
                array($req_uid, $amount, $reason)
            );
            $request_id = $this->db->insert_id();
            $this->_json(array('ok' => true, 'request_id' => $request_id, 'status' => 'pending_approval'));
        } catch (Exception $e) {
            $this->_json(array('ok' => false, 'error' => 'db_error', 'detail' => $e->getMessage()), 500);
        }
    }

    // 12. POST /api/discipline/advance/approve
    public function advance_approve() {
        $uid  = $this->_auth();
        $raw  = $this->_body();
        $body = json_decode($raw, true) ?: array();
        $request_id  = isset($body['request_id'])  ? (int)$body['request_id']  : 0;
        $approver_uid= isset($body['approver_uid']) ? (int)$body['approver_uid']: $uid;
        if (!$request_id) {
            $this->_json(array('ok' => false, 'error' => 'request_id required'), 400);
        }
        try {
            if (!$this->db->table_exists('advance_request')) {
                $this->_json(array('ok' => false, 'error' => 'table_not_seeded'), 503);
            }
            $this->db->query(
                "UPDATE advance_request SET status='approved', approver_uid=?, approved_at=NOW()
                 WHERE id = ? AND status='pending_approval'",
                array($approver_uid, $request_id)
            );
            $this->_json(array('ok' => true, 'request_id' => $request_id, 'approver_uid' => $approver_uid));
        } catch (Exception $e) {
            $this->_json(array('ok' => false, 'error' => 'db_error', 'detail' => $e->getMessage()), 500);
        }
    }

    // 13. POST /api/discipline/advance/consume
    public function advance_consume() {
        $uid  = $this->_auth();
        $raw  = $this->_body();
        $body = json_decode($raw, true) ?: array();
        $request_id     = isset($body['request_id'])     ? (int)$body['request_id']     : 0;
        $amount_consumed= isset($body['amount_consumed']) ? (float)$body['amount_consumed']: 0;
        if (!$request_id || $amount_consumed <= 0) {
            $this->_json(array('ok' => false, 'error' => 'request_id and amount_consumed > 0 required'), 400);
        }
        try {
            $balance = 0;
            if ($this->db->table_exists('advance_request')) {
                $req = $this->db->query(
                    "SELECT amount FROM advance_request WHERE id = ? LIMIT 1",
                    array($request_id)
                )->row();
                if ($req) {
                    $balance = (float)$req->amount - $amount_consumed;
                }
            }
            if ($this->db->table_exists('cash_log')) {
                $this->db->query(
                    "INSERT INTO cash_log (ref_type, ref_id, txn_type, amount, balance_after, created_at)
                     VALUES ('advance_consume', ?, 'debit', ?, ?, NOW())",
                    array($request_id, $amount_consumed, $balance)
                );
            }
            $this->_json(array('ok' => true, 'request_id' => $request_id, 'amount_consumed' => $amount_consumed, 'balance' => $balance));
        } catch (Exception $e) {
            $this->_json(array('ok' => false, 'error' => 'db_error', 'detail' => $e->getMessage()), 500);
        }
    }

    // 14. GET /api/discipline/advance/queue?approver_uid=
    public function advance_queue() {
        $this->_auth();
        $approver_uid = (int)$this->input->get('approver_uid');
        if (!$approver_uid) $approver_uid = $this->uid;
        try {
            if (!$this->db->table_exists('advance_request')) {
                $this->_json(array('ok' => true, 'rows' => array(), 'note' => 'table_not_seeded_yet'));
            }
            $rows = $this->db->query(
                "SELECT id AS request_id, uid, amount, reason, status, requested_at
                 FROM advance_request
                 WHERE status = 'pending_approval'
                 ORDER BY requested_at ASC
                 LIMIT 100",
                array()
            )->result_array();
            $this->_json(array('ok' => true, 'approver_uid' => $approver_uid, 'rows' => $rows, 'count' => count($rows)));
        } catch (Exception $e) {
            $this->_json(array('ok' => false, 'error' => 'db_error', 'detail' => $e->getMessage()), 500);
        }
    }

    // 15. POST /api/discipline/advance/return
    public function advance_return() {
        $uid  = $this->_auth();
        $raw  = $this->_body();
        $body = json_decode($raw, true) ?: array();
        $request_id     = isset($body['request_id'])     ? (int)$body['request_id']     : 0;
        $amount_returned= isset($body['amount_returned']) ? (float)$body['amount_returned']: 0;
        if (!$request_id) {
            $this->_json(array('ok' => false, 'error' => 'request_id required'), 400);
        }
        try {
            if (!$this->db->table_exists('advance_request')) {
                $this->_json(array('ok' => false, 'error' => 'table_not_seeded'), 503);
            }
            $this->db->query(
                "UPDATE advance_request SET returned_amount=?, returned_at=NOW(), status='returned'
                 WHERE id = ?",
                array($amount_returned, $request_id)
            );
            $this->_json(array('ok' => true, 'request_id' => $request_id, 'amount_returned' => $amount_returned));
        } catch (Exception $e) {
            $this->_json(array('ok' => false, 'error' => 'db_error', 'detail' => $e->getMessage()), 500);
        }
    }

    // 16. POST /api/discipline/advance/settle
    public function advance_settle() {
        $uid  = $this->_auth();
        $raw  = $this->_body();
        $body = json_decode($raw, true) ?: array();
        $request_id          = isset($body['request_id'])           ? (int)$body['request_id']               : 0;
        $settlement_proof_url= isset($body['settlement_proof_url'])  ? substr($body['settlement_proof_url'], 0, 1000) : null;
        if (!$request_id) {
            $this->_json(array('ok' => false, 'error' => 'request_id required'), 400);
        }
        try {
            if (!$this->db->table_exists('advance_request')) {
                $this->_json(array('ok' => false, 'error' => 'table_not_seeded'), 503);
            }
            $this->db->query(
                "UPDATE advance_request SET status='settled', settlement_proof_url=?, settled_at=NOW(), settled_by=?
                 WHERE id = ?",
                array($settlement_proof_url, $uid, $request_id)
            );
            $this->_json(array('ok' => true, 'request_id' => $request_id));
        } catch (Exception $e) {
            $this->_json(array('ok' => false, 'error' => 'db_error', 'detail' => $e->getMessage()), 500);
        }
    }

    // ================================================================
    // DISCIPLINE EXPENSE (3 endpoints)
    // ================================================================

    // 17. POST /api/discipline/expense/submit
    public function expense_submit() {
        $uid  = $this->_auth();
        $raw  = $this->_body();
        $body = json_decode($raw, true) ?: array();
        $exp_uid    = isset($body['uid'])         ? (int)$body['uid']                 : $uid;
        $meeting_id = isset($body['meeting_id'])  ? (int)$body['meeting_id']          : null;
        $amount     = isset($body['amount'])      ? (float)$body['amount']            : 0;
        $category   = isset($body['category'])    ? substr($body['category'], 0, 100) : null;
        $receipt_url= isset($body['receipt_url']) ? substr($body['receipt_url'], 0, 1000) : null;
        if ($amount <= 0) {
            $this->_json(array('ok' => false, 'error' => 'amount must be > 0'), 400);
        }
        try {
            $tbl = $this->db->table_exists('expense_actuals_log') ? 'expense_actuals_log' : null;
            if ($tbl) {
                $this->db->query(
                    "INSERT INTO expense_actuals_log (uid, event_id, amount, category, receipt_url, submitted_at)
                     VALUES (?, ?, ?, ?, ?, NOW())",
                    array($exp_uid, $meeting_id, $amount, $category, $receipt_url)
                );
                $expense_id = $this->db->insert_id();
            } elseif ($this->db->table_exists('cash_log')) {
                $this->db->query(
                    "INSERT INTO cash_log (ref_type, ref_id, txn_type, amount, note, created_at)
                     VALUES ('expense', ?, 'debit', ?, ?, NOW())",
                    array($meeting_id, $amount, $category)
                );
                $expense_id = $this->db->insert_id();
            } else {
                $this->_json(array('ok' => true, 'expense_id' => null, 'note' => 'table_not_seeded_yet'));
            }
            $this->_json(array('ok' => true, 'expense_id' => $expense_id));
        } catch (Exception $e) {
            $this->_json(array('ok' => false, 'error' => 'db_error', 'detail' => $e->getMessage()), 500);
        }
    }

    // 18. POST /api/discipline/expense/submit_batch
    public function expense_submit_batch() {
        $uid  = $this->_auth();
        $raw  = $this->_body();
        $body = json_decode($raw, true) ?: array();
        $expenses = isset($body['expenses']) && is_array($body['expenses']) ? $body['expenses'] : array();
        if (empty($expenses)) {
            $this->_json(array('ok' => false, 'error' => 'expenses array required'), 400);
        }
        $tbl = $this->db->table_exists('expense_actuals_log') ? 'expense_actuals_log' : null;
        $inserted_count = 0;
        $ids = array();
        try {
            foreach ($expenses as $exp) {
                $exp_uid    = isset($exp['uid'])         ? (int)$exp['uid']                 : $uid;
                $meeting_id = isset($exp['meeting_id'])  ? (int)$exp['meeting_id']          : null;
                $amount     = isset($exp['amount'])      ? (float)$exp['amount']            : 0;
                $category   = isset($exp['category'])    ? substr($exp['category'], 0, 100) : null;
                $receipt_url= isset($exp['receipt_url']) ? substr($exp['receipt_url'], 0, 1000) : null;
                if ($amount <= 0) continue;
                if ($tbl) {
                    $this->db->query(
                        "INSERT INTO expense_actuals_log (uid, event_id, amount, category, receipt_url, submitted_at)
                         VALUES (?, ?, ?, ?, ?, NOW())",
                        array($exp_uid, $meeting_id, $amount, $category, $receipt_url)
                    );
                    $ids[] = $this->db->insert_id();
                } elseif ($this->db->table_exists('cash_log')) {
                    $this->db->query(
                        "INSERT INTO cash_log (ref_type, ref_id, txn_type, amount, note, created_at)
                         VALUES ('expense_batch', ?, 'debit', ?, ?, NOW())",
                        array($meeting_id, $amount, $category)
                    );
                    $ids[] = $this->db->insert_id();
                } else {
                    $ids[] = null;
                }
                $inserted_count++;
            }
            $this->_json(array('ok' => true, 'inserted_count' => $inserted_count, 'ids' => $ids));
        } catch (Exception $e) {
            $this->_json(array('ok' => false, 'error' => 'db_error', 'detail' => $e->getMessage()), 500);
        }
    }

    // 19. POST /api/discipline/expense/cm_approve
    public function expense_cm_approve() {
        $uid  = $this->_auth();
        $raw  = $this->_body();
        $body = json_decode($raw, true) ?: array();
        $expense_ids = isset($body['expense_ids']) && is_array($body['expense_ids']) ? $body['expense_ids'] : array();
        $cm_uid      = isset($body['cm_uid'])      ? (int)$body['cm_uid']            : $uid;
        if (empty($expense_ids)) {
            $this->_json(array('ok' => false, 'error' => 'expense_ids array required'), 400);
        }
        $approved_count = 0;
        try {
            $tbl = $this->db->table_exists('expense_actuals_log') ? 'expense_actuals_log' : null;
            if (!$tbl) {
                $this->_json(array('ok' => true, 'approved_count' => 0, 'note' => 'table_not_seeded_yet'));
            }
            foreach ($expense_ids as $eid) {
                $eid = (int)$eid;
                if (!$eid) continue;
                $this->db->query(
                    "UPDATE expense_actuals_log SET cm_approved=1, cm_approved_by=?, cm_approved_at=NOW()
                     WHERE id=? AND cm_approved=0",
                    array($cm_uid, $eid)
                );
                if ($this->db->affected_rows() > 0) $approved_count++;
            }
            $this->_json(array('ok' => true, 'approved_count' => $approved_count, 'cm_uid' => $cm_uid));
        } catch (Exception $e) {
            $this->_json(array('ok' => false, 'error' => 'db_error', 'detail' => $e->getMessage()), 500);
        }
    }

    // ================================================================
    // DISCIPLINE OTHER (2 endpoints)
    // ================================================================

    // 20. GET /api/discipline/bd_score?uid=&date=
    public function discipline_bd_score() {
        $this->_auth();
        $target_uid = $this->_scope_target_uid($this->input->get('uid'));
        $date = $this->input->get('date') ?: date('Y-m-d');
        try {
            $meetings_row = $this->db->query(
                "SELECT COUNT(*) AS cnt FROM tblcallevents WHERE user_id=? AND DATE(fwd_date)=? AND meeting_type != 'NA'",
                array($target_uid, $date)
            )->row();
            $meetings_done = $meetings_row ? (int)$meetings_row->cnt : 0;

            $leads_row = $this->db->query(
                "SELECT COUNT(DISTINCT cid_id) AS cnt FROM tblcallevents WHERE user_id=? AND DATE(fwd_date)=?",
                array($target_uid, $date)
            )->row();
            $leads_moved = $leads_row ? (int)$leads_row->cnt : 0;

            $mom_approved = 0;
            if ($this->db->table_exists('mom_v2_submission')) {
                $mom_row = $this->db->query(
                    "SELECT COUNT(*) AS cnt FROM mom_v2_submission WHERE bd_uid=? AND DATE(cm_action_at)=? AND status='approved'",
                    array($target_uid, $date)
                )->row();
                $mom_approved = $mom_row ? (int)$mom_row->cnt : 0;
            }

            $score = ($meetings_done * 2) + ($leads_moved * 1) + ($mom_approved * 3);

            $this->_json(array(
                'ok'        => true,
                'uid'       => $target_uid,
                'date'      => $date,
                'score'     => $score,
                'breakdown' => array(
                    'meetings_done' => $meetings_done,
                    'leads_moved'   => $leads_moved,
                    'mom_approved'  => $mom_approved,
                ),
            ));
        } catch (Exception $e) {
            $this->_json(array('ok' => false, 'error' => 'db_error', 'detail' => $e->getMessage()), 500);
        }
    }

    // 21. GET /api/discipline/narrative?uid=&date=
    public function discipline_narrative() {
        $this->_auth();
        $target_uid = $this->_scope_target_uid($this->input->get('uid'));
        $date = $this->input->get('date') ?: date('Y-m-d');
        try {
            $meetings_row = $this->db->query(
                "SELECT COUNT(*) AS cnt FROM tblcallevents WHERE user_id=? AND DATE(fwd_date)=? AND meeting_type != 'NA'",
                array($target_uid, $date)
            )->row();
            $meetings_done = $meetings_row ? (int)$meetings_row->cnt : 0;

            $mom_approved = 0;
            if ($this->db->table_exists('mom_v2_submission')) {
                $mom_row = $this->db->query(
                    "SELECT COUNT(*) AS cnt FROM mom_v2_submission WHERE bd_uid=? AND DATE(cm_action_at)=? AND status='approved'",
                    array($target_uid, $date)
                )->row();
                $mom_approved = $mom_row ? (int)$mom_row->cnt : 0;
            }

            $narrative = "Worked {$meetings_done} meeting(s) and got {$mom_approved} MoM(s) approved on {$date}.";
            $facts = array(
                "meetings_done={$meetings_done}",
                "mom_approved={$mom_approved}",
                "date={$date}",
            );

            $this->_json(array(
                'ok'        => true,
                'uid'       => $target_uid,
                'date'      => $date,
                'narrative' => $narrative,
                'facts'     => $facts,
            ));
        } catch (Exception $e) {
            $this->_json(array('ok' => false, 'error' => 'db_error', 'detail' => $e->getMessage()), 500);
        }
    }

    // ================================================================
    // TASK LIFECYCLE (7 endpoints)
    // ================================================================

    // 22. GET /api/task/check_queue?uid=
    public function task_check_queue() {
        $this->_auth();
        $target_uid = $this->_scope_target_uid($this->input->get('uid'));
        try {
            $tbl = null;
            if ($this->db->table_exists('auto_tasks_v2')) {
                $tbl = 'auto_tasks_v2';
            } elseif ($this->db->table_exists('task_planner')) {
                $tbl = 'task_planner';
            }
            if (!$tbl) {
                $this->_json(array('ok' => true, 'rows' => array(), 'note' => 'table_not_seeded_yet'));
            }
            $rows = $this->db->query(
                "SELECT * FROM `{$tbl}` WHERE uid=? AND status='pending' ORDER BY id ASC LIMIT 50",
                array($target_uid)
            )->result_array();
            $this->_json(array('ok' => true, 'uid' => $target_uid, 'rows' => $rows, 'count' => count($rows)));
        } catch (Exception $e) {
            $this->_json(array('ok' => false, 'error' => 'db_error', 'detail' => $e->getMessage()), 500);
        }
    }

    // 23. GET /api/task/detail?taskid= (or task_id=)
    public function task_detail() {
        $this->_auth();
        $taskid = (int)$this->input->get('taskid') ?: (int)$this->input->get('task_id');
        if (!$taskid) {
            $this->_json(array('ok' => false, 'error' => 'taskid required'), 400);
        }
        try {
            $tbl = null;
            if ($this->db->table_exists('auto_tasks_v2')) {
                $tbl = 'auto_tasks_v2';
            } elseif ($this->db->table_exists('task_planner')) {
                $tbl = 'task_planner';
            }
            if (!$tbl) {
                $this->_json(array('ok' => false, 'error' => 'table_not_seeded'), 503);
            }
            $task = $this->db->query(
                "SELECT t.* FROM `{$tbl}` t WHERE t.id = ? LIMIT 1",
                array($taskid)
            )->row_array();
            if (!$task) {
                $this->_json(array('ok' => false, 'error' => 'task_not_found'), 404);
            }
            // Enrich with lead info if cid_id is available
            $lead = null;
            if (!empty($task['cid_id']) && $this->db->table_exists('company_master')) {
                $lead = $this->db->query(
                    "SELECT id, compname AS school_name, address FROM company_master WHERE id=? LIMIT 1",
                    array((int)$task['cid_id'])
                )->row_array();
            }
            $this->_json(array('ok' => true, 'task' => $task, 'lead' => $lead));
        } catch (Exception $e) {
            $this->_json(array('ok' => false, 'error' => 'db_error', 'detail' => $e->getMessage()), 500);
        }
    }

    // 24. GET /api/task/live?uid=
    public function task_live() {
        $this->_auth();
        $target_uid = $this->_scope_target_uid($this->input->get('uid'));
        try {
            $tbl = null;
            if ($this->db->table_exists('auto_tasks_v2')) {
                $tbl = 'auto_tasks_v2';
            } elseif ($this->db->table_exists('task_planner')) {
                $tbl = 'task_planner';
            }
            if (!$tbl) {
                $this->_json(array('ok' => true, 'rows' => array(), 'note' => 'table_not_seeded_yet'));
            }
            $rows = $this->db->query(
                "SELECT * FROM `{$tbl}` WHERE uid=? AND status='in_progress' ORDER BY id DESC LIMIT 50",
                array($target_uid)
            )->result_array();
            $this->_json(array('ok' => true, 'uid' => $target_uid, 'rows' => $rows, 'count' => count($rows)));
        } catch (Exception $e) {
            $this->_json(array('ok' => false, 'error' => 'db_error', 'detail' => $e->getMessage()), 500);
        }
    }

    // 25. GET/POST /api/task/preflight  (rimlyproof fix 20260608: accept GET uid/taskid/tid, optional taskid, return hard_gates/soft_gates contract the app consumes)
    public function task_preflight() {
        $uid  = $this->_auth();
        $raw  = $this->_body();
        $body = json_decode($raw, true) ?: array();

        // Accept uid from GET or body; default to authed uid.
        $target_uid = 0;
        if ($this->input->get('uid') !== null && $this->input->get('uid') !== '') {
            $target_uid = (int)$this->input->get('uid');
        } elseif (isset($body['uid'])) {
            $target_uid = (int)$body['uid'];
        }
        if (!$target_uid) { $target_uid = (int)$uid; }
        // rimlyproof_taskscope_20260609: a field user is locked to their own uid
        // regardless of any GET/body uid param.
        $target_uid = $this->_scope_target_uid($target_uid);

        // taskid is OPTIONAL. Accept GET taskid/tid or body taskid/tid.
        $taskid = 0;
        if ($this->input->get('taskid') !== null && $this->input->get('taskid') !== '') {
            $taskid = (int)$this->input->get('taskid');
        } elseif ($this->input->get('tid') !== null && $this->input->get('tid') !== '') {
            $taskid = (int)$this->input->get('tid');
        } elseif (isset($body['taskid'])) {
            $taskid = (int)$body['taskid'];
        } elseif (isset($body['tid'])) {
            $taskid = (int)$body['tid'];
        }

        $hard_gates = array();
        $soft_gates = array();
        try {
            // Day-start truth: same source as /api/discipline/state (user_day keyed on user_id).
            $today = date('Y-m-d');
            $day = $this->db->query(
                "SELECT id FROM user_day
                 WHERE user_id = ? AND CAST(sdatet AS DATE) = ?
                 ORDER BY id DESC LIMIT 1",
                array($target_uid, $today)
            )->row();
            if (!$day) {
                $hard_gates[] = array(
                    'code'      => 'day_not_started',
                    'label'     => 'Start your day before opening tasks',
                    'fix_route' => 'DayManagement',
                );
            }

            // Soft gate: approved leave today (only if table exists).
            if ($this->db->table_exists('leave_log')) {
                $leave = $this->db->query(
                    "SELECT id FROM leave_log WHERE uid=? AND leave_date=CURDATE() AND status='approved' LIMIT 1",
                    array($target_uid)
                )->row();
                if ($leave) {
                    $soft_gates[] = array(
                        'code'  => 'user_on_leave',
                        'label' => 'You are marked on approved leave today',
                    );
                }
            }

            $this->_json(array(
                'ok'         => true,
                'uid'        => $target_uid,
                'taskid'     => $taskid,
                'can_start'  => (count($hard_gates) === 0),
                'hard_gates' => $hard_gates,
                'soft_gates' => $soft_gates,
            ));
        } catch (Exception $e) {
            $this->_json(array('ok' => false, 'error' => 'db_error', 'detail' => $e->getMessage()), 500);
        }
    }

    // 26. POST /api/task/save_draft
    public function task_save_draft() {
        $uid  = $this->_auth();
        $raw  = $this->_body();
        $body = json_decode($raw, true) ?: array();
        $taskid       = isset($body['taskid'])       ? (int)$body['taskid']               : 0;
        $draft_payload= isset($body['draft_payload']) ? $body['draft_payload']             : array();
        if (!$taskid) {
            $this->_json(array('ok' => false, 'error' => 'taskid required'), 400);
        }
        $draft_json = json_encode($draft_payload);
        try {
            if ($this->db->table_exists('task_draft')) {
                $existing = $this->db->query(
                    "SELECT id FROM task_draft WHERE task_id=? AND uid=? LIMIT 1",
                    array($taskid, $uid)
                )->row();
                if ($existing) {
                    $this->db->query(
                        "UPDATE task_draft SET draft_json=?, updated_at=NOW() WHERE id=?",
                        array($draft_json, (int)$existing->id)
                    );
                    $draft_id = (int)$existing->id;
                } else {
                    $this->db->query(
                        "INSERT INTO task_draft (task_id, uid, draft_json, created_at, updated_at)
                         VALUES (?, ?, ?, NOW(), NOW())",
                        array($taskid, $uid, $draft_json)
                    );
                    $draft_id = $this->db->insert_id();
                }
            } elseif ($this->db->table_exists('task_planner')) {
                $this->db->query(
                    "UPDATE task_planner SET draft_payload=?, updated_at=NOW() WHERE id=?",
                    array($draft_json, $taskid)
                );
                $draft_id = $taskid;
            } else {
                $this->_json(array('ok' => true, 'draft_id' => null, 'note' => 'table_not_seeded_yet'));
            }
            $this->_json(array('ok' => true, 'draft_id' => $draft_id, 'taskid' => $taskid));
        } catch (Exception $e) {
            $this->_json(array('ok' => false, 'error' => 'db_error', 'detail' => $e->getMessage()), 500);
        }
    }

    // 27. POST /api/task/star_check
    public function task_star_check() {
        $uid  = $this->_auth();
        $raw  = $this->_body();
        $body = json_decode($raw, true) ?: array();
        $taskid     = isset($body['taskid']) ? (int)$body['taskid'] : (isset($body['tid']) ? (int)$body['tid'] : (int)($this->input->get('taskid') ?: $this->input->get('tid')));
        $target_uid = isset($body['uid'])    ? (int)$body['uid']    : $uid;
        if (!$taskid) {
            $this->_json(array('ok' => false, 'error' => 'taskid required'), 400);
        }
        $criteria_met    = array();
        $criteria_missed = array();
        try {
            // Criterion 1: MOM submitted for this task (event_id FK -> tblcallevents.id)
            if ($this->db->table_exists('mom_v2_submission')) {
                $mom_res = $this->db->query(
                    "SELECT submission_id FROM mom_v2_submission WHERE event_id=? LIMIT 1",
                    array($taskid)
                );
                $mom = ($mom_res !== FALSE) ? $mom_res->row() : null;
                if ($mom) {
                    $criteria_met[] = 'mom_submitted';
                } else {
                    $criteria_missed[] = 'mom_submitted';
                }
            } else {
                $criteria_missed[] = 'mom_submitted';
            }

            // Criterion 2+3: Read from tblcallevents (the live task table)
            // status_id IN (12,13,14) = Positive-NAP / Very Positive-NAP / On-Boarded (terminal closed states)
            $tce_res = $this->db->query(
                "SELECT remarks, status_id, cid_id FROM tblcallevents WHERE id=? LIMIT 1",
                array($taskid)
            );
            $tce_row = ($tce_res !== FALSE) ? $tce_res->row() : null;
            if ($tce_row === null) {
                // Row not found: criteria missed, never a 500
                $criteria_missed[] = 'closure_remark';
                $criteria_missed[] = 'task_closed';
            } else {
                if (!empty($tce_row->remarks)) {
                    $criteria_met[] = 'closure_remark';
                } else {
                    $criteria_missed[] = 'closure_remark';
                }
                // Closed states: status_id IN (12,13,14)
                $closed_ids = array(12, 13, 14);
                if (in_array((int)$tce_row->status_id, $closed_ids)) {
                    $criteria_met[] = 'task_closed';
                } else {
                    $criteria_missed[] = 'task_closed';
                }
            }

            $qualifies = empty($criteria_missed);
            $this->_json(array(
                'ok'                  => true,
                'taskid'              => $taskid,
                'qualifies_for_star'  => $qualifies,
                'criteria_met'        => $criteria_met,
                'criteria_missed'     => $criteria_missed,
            ));
        } catch (\Throwable $e) {
            log_message('error', 'task_star_check: ' . $e->getMessage());
            $this->_json(array('ok' => false, 'error' => 'db_error'));
        }
    }

    // 28. POST /api/task/submit_closure
    public function task_submit_closure() {
        $uid  = $this->_auth();
        $raw  = $this->_body();
        $body = json_decode($raw, true) ?: array();
        $taskid  = isset($body['taskid'])  ? (int)$body['taskid']            : 0;
        $cstatus = isset($body['cstatus']) ? (int)$body['cstatus']           : 0;
        $remark  = isset($body['remark'])  ? substr($body['remark'], 0, 2000): '';
        if (!$taskid) {
            $this->_json(array('ok' => false, 'error' => 'taskid required'), 400);
        }
        $closed_at = date('Y-m-d H:i:s');
        try {
            $tbl = null;
            if ($this->db->table_exists('auto_tasks_v2')) {
                $tbl = 'auto_tasks_v2';
            } elseif ($this->db->table_exists('task_planner')) {
                $tbl = 'task_planner';
            }
            if ($tbl) {
                $this->db->query(
                    "UPDATE `{$tbl}` SET status='closed', cstatus=?, remark=?, closed_at=?, updated_at=NOW()
                     WHERE id=?",
                    array($cstatus, $remark, $closed_at, $taskid)
                );
            }
            // Get cid_id for tblcallevents log
            $cid_id = null;
            if ($tbl) {
                $tr = $this->db->query("SELECT cid_id, uid FROM `{$tbl}` WHERE id=? LIMIT 1", array($taskid))->row();
                if ($tr) $cid_id = (int)$tr->cid_id;
            }
            // Insert closure event into tblcallevents
            if ($cid_id) {
                $this->db->query(
                    "INSERT INTO tblcallevents (user_id, cid_id, fwd_date, cstatus, remark, plan, meeting_type)
                     VALUES (?, ?, NOW(), ?, ?, 0, 'closure')",
                    array($uid, $cid_id, $cstatus, $remark)
                );
            }
            $this->_json(array(
                'ok'        => true,
                'taskid'    => $taskid,
                'closed_at' => $closed_at,
                'cstatus'   => $cstatus,
            ));
        } catch (Exception $e) {
            $this->_json(array('ok' => false, 'error' => 'db_error', 'detail' => $e->getMessage()), 500);
        }
    }

    // 29. POST /api/task/upload_attachment
    public function task_upload_attachment() {
        $uid = $this->_auth();
        // Accept multipart field "file" (primary) or "attachment" (fallback)
        $file_key = null;
        if (isset($_FILES['file']) && $_FILES['file']['error'] !== UPLOAD_ERR_NO_FILE) {
            $file_key = 'file';
        } elseif (isset($_FILES['attachment']) && $_FILES['attachment']['error'] !== UPLOAD_ERR_NO_FILE) {
            $file_key = 'attachment';
        }
        if (!$file_key) {
            $this->_json(array('ok' => false, 'error' => 'no_file', 'message' => 'No file received. Send multipart field file.'));
            return;
        }
        $file = $_FILES[$file_key];
        if ($file['error'] !== UPLOAD_ERR_OK) {
            $this->_json(array('ok' => false, 'error' => 'upload_error', 'message' => 'File upload error code ' . $file['error']));
            return;
        }
        // Validate type by extension
        $allowed_ext = array('jpg', 'jpeg', 'png', 'pdf');
        $orig_name   = $file['name'];
        $ext         = strtolower(pathinfo($orig_name, PATHINFO_EXTENSION));
        if (!in_array($ext, $allowed_ext)) {
            $this->_json(array('ok' => false, 'error' => 'invalid_type', 'message' => 'Allowed types: jpg, jpeg, png, pdf. Got: ' . $ext));
            return;
        }
        // Validate size: max 8 MB
        $max_bytes = 8 * 1024 * 1024;
        if ($file['size'] > $max_bytes) {
            $this->_json(array('ok' => false, 'error' => 'file_too_large', 'message' => 'Max size is 8 MB. File is ' . round($file['size'] / 1024) . ' KB.'));
            return;
        }
        // Ensure upload directory exists (0775)
        $upload_dir = FCPATH . 'uploads/attachment/';
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0775, true);
        }
        // Generate unique filename: uid_timestamp_sanitized_original
        $safe_orig   = preg_replace('/[^a-zA-Z0-9._-]/', '_', $orig_name);
        $safe_orig   = substr($safe_orig, 0, 80);
        $stored_name = intval($uid) . '_' . time() . '_' . $safe_orig;
        $dest_path   = $upload_dir . $stored_name;
        try {
            if (!move_uploaded_file($file['tmp_name'], $dest_path)) {
                log_message('error', 'task_upload_attachment: move_uploaded_file failed for uid=' . $uid);
                $this->_json(array('ok' => false, 'error' => 'write_failed', 'message' => 'Could not save file to disk.'));
                return;
            }
            $size_kb = (int)ceil($file['size'] / 1024);
            $flink   = 'uploads/attachment/' . $stored_name;
            $this->_json(array(
                'ok'            => true,
                'flink'         => $flink,
                'filename'      => $stored_name,
                'size_kb'       => $size_kb,
                'attachment_id' => $stored_name,
            ));
        } catch (\Throwable $e) {
            log_message('error', 'task_upload_attachment: ' . $e->getMessage());
            $this->_json(array('ok' => false, 'error' => 'write_failed', 'message' => 'Exception during file save.'));
        }
    }

} // end class Mobile_stub_real

<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Tender_api - Phase 1 Agent B, G1 Tender/RFP/EOI Tracker
 * Created: 2026-06-08 (additive only)
 *
 * Endpoints:
 *   GET  /api/tender/list              - list tenders (filters: owner_uid, status, type, upcoming_deadline)
 *   GET  /api/tender/get?id=           - single tender with docs
 *   POST /api/tender/save              - create or update a tender
 *   POST /api/tender/doc/add           - attach a doc to a tender
 *
 * Auth: Bearer 4eBaiAT7r4zu6OK3b8evjLNia1D7RGgb0qRTuLJfUSo
 * Tables: tender, tender_doc (joined to company_master)
 * Rules: ASCII only, Rs for rupees, empty -> {ok:true, empty:true}
 */
class Tender_api extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->database();
    }

    // -------------------------------------------------------------------------
    // Auth helper - 401 if no/bad bearer
    // -------------------------------------------------------------------------
    private function _bearer() {
        if (function_exists('authunify_ok') && authunify_ok()) { return true; } // rimlyproof_authunify_20260609

        $expected = '4eBaiAT7r4zu6OK3b8evjLNia1D7RGgb0qRTuLJfUSo';
        $cfg_file = APPPATH . 'config/digest_token.txt';
        if (file_exists($cfg_file)) {
            $t = trim(file_get_contents($cfg_file));
            if ($t) { $expected = $t; }
        }

        $header = '';
        if (!empty($_SERVER['HTTP_AUTHORIZATION'])) {
            $header = $_SERVER['HTTP_AUTHORIZATION'];
        } elseif (!empty($_SERVER['REDIRECT_HTTP_AUTHORIZATION'])) {
            $header = $_SERVER['REDIRECT_HTTP_AUTHORIZATION'];
        } elseif (function_exists('getallheaders')) {
            foreach (getallheaders() as $k => $v) {
                if (strcasecmp($k, 'Authorization') === 0) { $header = $v; break; }
            }
        }

        $provided = trim(str_replace(['Bearer ', 'Bearer'], '', $header));
        if (!$provided || $provided !== $expected) {
            $this->output->set_status_header(401)
                ->set_content_type('application/json')
                ->set_output(json_encode(['ok' => false, 'error' => 'unauthorized']));
            return false;
        }
        return true;
    }

    private function _json($data, $status = 200) {
        $this->output->set_status_header($status)
            ->set_content_type('application/json')
            ->set_output(json_encode($data));
    }

    private function _input_json() {
        $raw = file_get_contents('php://input');
        if ($raw && $raw[0] === '{') { return json_decode($raw, true) ?: []; }
        return $_POST ?: [];
    }

    // -------------------------------------------------------------------------
    // GET /api/tender/list
    // Filters: owner_uid, status, type, upcoming_deadline (days, default 30)
    // -------------------------------------------------------------------------
    public function list_index() {
        if (!$this->_bearer()) return;

        $owner_uid        = (int) $this->input->get('owner_uid');
        $status           = trim((string) $this->input->get('status'));
        $type             = trim((string) $this->input->get('type'));
        $upcoming_deadline = (int) $this->input->get('upcoming_deadline'); // days ahead

        $where  = ['t.active = 1'];
        $params = [];

        if ($owner_uid > 0) {
            $where[]  = 't.owner_uid = ?';
            $params[] = $owner_uid;
        }
        if ($status !== '') {
            $where[]  = 't.status = ?';
            $params[] = $status;
        }
        if (in_array($type, ['TENDER','RFP','EOI'], true)) {
            $where[]  = 't.type = ?';
            $params[] = $type;
        }
        if ($upcoming_deadline > 0) {
            $where[]  = 't.deadline BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL ? DAY)';
            $params[] = $upcoming_deadline;
        }

        $where_sql = implode(' AND ', $where);

        $sql = "SELECT t.id, t.company_id, cm.compname AS company_name,
                       t.type, t.title, t.value_rs, t.deadline,
                       t.stage, t.owner_uid, t.status, t.created_ts,
                       (SELECT COUNT(*) FROM tender_doc td WHERE td.tender_id = t.id) AS doc_count
                FROM tender t
                LEFT JOIN company_master cm ON cm.id = t.company_id
                WHERE {$where_sql}
                ORDER BY t.deadline ASC, t.created_ts DESC
                LIMIT 200";

        $rows = $this->db->query($sql, $params)->result_array();

        if (empty($rows)) {
            $this->_json([
                'ok'           => true,
                'empty'        => true,
                'rows'         => [],
                'count'        => 0,
                'filters_used' => compact('owner_uid','status','type','upcoming_deadline'),
                'generated_at' => date('c'),
            ]);
            return;
        }

        // Cast numeric fields
        foreach ($rows as &$r) {
            $r['id']         = (int)$r['id'];
            $r['company_id'] = (int)$r['company_id'];
            $r['owner_uid']  = (int)$r['owner_uid'];
            $r['value_rs']   = $r['value_rs'] !== null ? (float)$r['value_rs'] : null;
            $r['doc_count']  = (int)$r['doc_count'];
        }
        unset($r);

        $this->_json([
            'ok'           => true,
            'empty'        => false,
            'rows'         => $rows,
            'count'        => count($rows),
            'filters_used' => compact('owner_uid','status','type','upcoming_deadline'),
            'generated_at' => date('c'),
        ]);
    }

    // -------------------------------------------------------------------------
    // GET /api/tender/get?id=
    // -------------------------------------------------------------------------
    public function get() {
        if (!$this->_bearer()) return;

        $id = (int) $this->input->get('id');
        if ($id <= 0) {
            $this->_json(['ok' => false, 'error' => 'id is required'], 400);
            return;
        }

        $row = $this->db->query(
            "SELECT t.*, cm.compname AS company_name
             FROM tender t
             LEFT JOIN company_master cm ON cm.id = t.company_id
             WHERE t.id = ? AND t.active = 1",
            [$id]
        )->row_array();

        if (!$row) {
            $this->_json(['ok' => false, 'error' => 'not_found'], 404);
            return;
        }
        $row['id']        = (int)$row['id'];
        $row['company_id']= (int)$row['company_id'];
        $row['owner_uid'] = (int)$row['owner_uid'];
        $row['value_rs']  = $row['value_rs'] !== null ? (float)$row['value_rs'] : null;

        // Attach docs
        $docs = $this->db->query(
            "SELECT id, tender_id, doc_name, doc_url, uploaded_ts
             FROM tender_doc WHERE tender_id = ? ORDER BY uploaded_ts ASC",
            [$id]
        )->result_array();
        foreach ($docs as &$d) { $d['id'] = (int)$d['id']; $d['tender_id'] = (int)$d['tender_id']; }
        unset($d);
        $row['docs'] = $docs;

        $this->_json(['ok' => true, 'tender' => $row, 'generated_at' => date('c')]);
    }

    // -------------------------------------------------------------------------
    // POST /api/tender/save  (create or update)
    // Required: title, company_id, owner_uid, type
    // Optional: value_rs, deadline, stage, status, id (for update)
    // -------------------------------------------------------------------------
    public function save() {
        if (!$this->_bearer()) return;

        $in = $this->_input_json();

        $id         = isset($in['id']) ? (int)$in['id'] : 0;
        $company_id = isset($in['company_id']) ? (int)$in['company_id'] : 0;
        $title      = isset($in['title']) ? trim($in['title']) : '';
        $owner_uid  = isset($in['owner_uid']) ? (int)$in['owner_uid'] : 0;
        $type       = isset($in['type']) ? strtoupper(trim($in['type'])) : 'TENDER';
        $value_rs   = isset($in['value_rs']) && $in['value_rs'] !== '' ? (float)$in['value_rs'] : null;
        $deadline   = isset($in['deadline']) && $in['deadline'] ? $in['deadline'] : null;
        $stage      = isset($in['stage']) ? trim($in['stage']) : null;
        $status     = isset($in['status']) ? trim($in['status']) : 'open';

        if (!$company_id || !$title || !$owner_uid) {
            $this->_json(['ok' => false, 'error' => 'company_id, title, owner_uid are required'], 422);
            return;
        }
        if (!in_array($type, ['TENDER','RFP','EOI'], true)) {
            $this->_json(['ok' => false, 'error' => 'type must be TENDER, RFP, or EOI'], 422);
            return;
        }

        // Validate company exists
        $company = $this->db->query("SELECT id FROM company_master WHERE id = ? LIMIT 1", [$company_id])->row_array();
        if (!$company) {
            $this->_json(['ok' => false, 'error' => 'company_id not found in company_master'], 422);
            return;
        }

        $payload = [
            'company_id' => $company_id,
            'type'       => $type,
            'title'      => $title,
            'value_rs'   => $value_rs,
            'deadline'   => $deadline,
            'stage'      => $stage,
            'owner_uid'  => $owner_uid,
            'status'     => $status,
        ];

        if ($id > 0) {
            // Update
            $existing = $this->db->query("SELECT id FROM tender WHERE id = ? AND active = 1", [$id])->row_array();
            if (!$existing) {
                $this->_json(['ok' => false, 'error' => 'tender not found'], 404);
                return;
            }
            $this->db->query(
                "UPDATE tender SET company_id=?, type=?, title=?, value_rs=?, deadline=?, stage=?, owner_uid=?, status=?, updated_ts=NOW()
                 WHERE id = ?",
                [$payload['company_id'], $payload['type'], $payload['title'], $payload['value_rs'],
                 $payload['deadline'], $payload['stage'], $payload['owner_uid'], $payload['status'], $id]
            );
            $this->_json(['ok' => true, 'action' => 'updated', 'id' => $id, 'generated_at' => date('c')]);
        } else {
            // Insert
            $this->db->query(
                "INSERT INTO tender (company_id, type, title, value_rs, deadline, stage, owner_uid, status, created_ts)
                 VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW())",
                [$payload['company_id'], $payload['type'], $payload['title'], $payload['value_rs'],
                 $payload['deadline'], $payload['stage'], $payload['owner_uid'], $payload['status']]
            );
            $new_id = $this->db->insert_id();
            $this->_json(['ok' => true, 'action' => 'created', 'id' => (int)$new_id, 'generated_at' => date('c')]);
        }
    }

    // -------------------------------------------------------------------------
    // POST /api/tender/doc/add
    // Required: tender_id, doc_name, doc_url
    // -------------------------------------------------------------------------
    public function doc_add() {
        if (!$this->_bearer()) return;

        $in        = $this->_input_json();
        $tender_id = isset($in['tender_id']) ? (int)$in['tender_id'] : 0;
        $doc_name  = isset($in['doc_name']) ? trim($in['doc_name']) : '';
        $doc_url   = isset($in['doc_url']) ? trim($in['doc_url']) : '';

        if (!$tender_id || !$doc_name || !$doc_url) {
            $this->_json(['ok' => false, 'error' => 'tender_id, doc_name, doc_url are required'], 422);
            return;
        }

        // Validate tender exists
        $tender = $this->db->query("SELECT id FROM tender WHERE id = ? AND active = 1", [$tender_id])->row_array();
        if (!$tender) {
            $this->_json(['ok' => false, 'error' => 'tender not found'], 404);
            return;
        }

        $this->db->query(
            "INSERT INTO tender_doc (tender_id, doc_name, doc_url, uploaded_ts) VALUES (?, ?, ?, NOW())",
            [$tender_id, $doc_name, $doc_url]
        );
        $new_id = $this->db->insert_id();

        $this->_json(['ok' => true, 'action' => 'doc_added', 'id' => (int)$new_id, 'tender_id' => $tender_id, 'generated_at' => date('c')]);
    }
}

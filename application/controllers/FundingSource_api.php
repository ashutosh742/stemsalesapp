<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * FundingSource_api - Phase 1 Agent B, G2 CSR Funding-Source Mapping
 * Created: 2026-06-08 (additive only; extends, does not duplicate CsrController/CsrProspect/Mca21)
 *
 * Endpoints:
 *   GET  /api/funding/list             - filter by company_id or source_type
 *   POST /api/funding/save             - create or update a funding source record
 *
 * Auth: Bearer 4eBaiAT7r4zu6OK3b8evjLNia1D7RGgb0qRTuLJfUSo
 * Table: funding_source (joined to company_master and optionally csr_corporate_master_v2)
 * Rules: ASCII only, Rs for rupees, empty -> {ok:true, empty:true}
 *
 * CSR NOTES: CsrController handles LinkedIn verification (mom_csr_check).
 *            CsrProspect handles csr_corporate_master_v2 list.
 *            This endpoint maps FUNDING SOURCES to companies - a complementary, non-overlapping concern.
 *            Where company_id matches a csr_corporate_master_v2 row, we join for context.
 */
class FundingSource_api extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->database();
    }

    // -------------------------------------------------------------------------
    // Auth helper
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
    // GET /api/funding/list?company_id= or ?source_type=
    // Both filters are optional; if neither given, returns recent 100 rows
    // -------------------------------------------------------------------------
    public function list_index() {
        if (!$this->_bearer()) return;

        $company_id  = (int) $this->input->get('company_id');
        $source_type = trim((string) $this->input->get('source_type'));

        $valid_types = ['S135','DMFT','MINISTRY','PSU'];

        $where  = ['fs.active = 1'];
        $params = [];

        if ($company_id > 0) {
            $where[]  = 'fs.company_id = ?';
            $params[] = $company_id;
        }
        if ($source_type !== '' && in_array($source_type, $valid_types, true)) {
            $where[]  = 'fs.source_type = ?';
            $params[] = $source_type;
        }

        $where_sql = implode(' AND ', $where);

        $sql = "SELECT fs.id, fs.company_id, cm.compname AS company_name,
                       fs.source_type, fs.source_name, fs.fund_rs,
                       fs.district, fs.notes, fs.created_ts,
                       cm.state AS company_state, cm.district AS company_district
                FROM funding_source fs
                LEFT JOIN company_master cm ON cm.id = fs.company_id
                WHERE {$where_sql}
                ORDER BY fs.created_ts DESC
                LIMIT 100";

        $rows = $this->db->query($sql, $params)->result_array();

        if (empty($rows)) {
            $this->_json([
                'ok'           => true,
                'empty'        => true,
                'rows'         => [],
                'count'        => 0,
                'filters_used' => ['company_id' => $company_id, 'source_type' => $source_type],
                'valid_source_types' => $valid_types,
                'generated_at' => date('c'),
            ]);
            return;
        }

        foreach ($rows as &$r) {
            $r['id']         = (int)$r['id'];
            $r['company_id'] = (int)$r['company_id'];
            $r['fund_rs']    = $r['fund_rs'] !== null ? (float)$r['fund_rs'] : null;
        }
        unset($r);

        $this->_json([
            'ok'           => true,
            'empty'        => false,
            'rows'         => $rows,
            'count'        => count($rows),
            'filters_used' => ['company_id' => $company_id, 'source_type' => $source_type],
            'generated_at' => date('c'),
        ]);
    }

    // -------------------------------------------------------------------------
    // POST /api/funding/save
    // Required: company_id, source_type, source_name
    // Optional: fund_rs, district, notes, id (for update)
    // -------------------------------------------------------------------------
    public function save() {
        if (!$this->_bearer()) return;

        $in          = $this->_input_json();
        $id          = isset($in['id']) ? (int)$in['id'] : 0;
        $company_id  = isset($in['company_id']) ? (int)$in['company_id'] : 0;
        $source_type = isset($in['source_type']) ? strtoupper(trim($in['source_type'])) : '';
        $source_name = isset($in['source_name']) ? trim($in['source_name']) : '';
        $fund_rs     = isset($in['fund_rs']) && $in['fund_rs'] !== '' ? (float)$in['fund_rs'] : null;
        $district    = isset($in['district']) ? trim($in['district']) : null;
        $notes       = isset($in['notes']) ? trim($in['notes']) : null;

        $valid_types = ['S135','DMFT','MINISTRY','PSU'];

        if (!$company_id || !$source_type || !$source_name) {
            $this->_json(['ok' => false, 'error' => 'company_id, source_type, source_name are required'], 422);
            return;
        }
        if (!in_array($source_type, $valid_types, true)) {
            $this->_json(['ok' => false, 'error' => 'source_type must be one of: ' . implode(', ', $valid_types)], 422);
            return;
        }

        // Validate company_id
        $company = $this->db->query("SELECT id FROM company_master WHERE id = ? LIMIT 1", [$company_id])->row_array();
        if (!$company) {
            $this->_json(['ok' => false, 'error' => 'company_id not found in company_master'], 422);
            return;
        }

        if ($id > 0) {
            $existing = $this->db->query("SELECT id FROM funding_source WHERE id = ? AND active = 1", [$id])->row_array();
            if (!$existing) {
                $this->_json(['ok' => false, 'error' => 'funding_source record not found'], 404);
                return;
            }
            $this->db->query(
                "UPDATE funding_source SET company_id=?, source_type=?, source_name=?, fund_rs=?, district=?, notes=?, updated_ts=NOW()
                 WHERE id = ?",
                [$company_id, $source_type, $source_name, $fund_rs, $district, $notes, $id]
            );
            $this->_json(['ok' => true, 'action' => 'updated', 'id' => $id, 'generated_at' => date('c')]);
        } else {
            $this->db->query(
                "INSERT INTO funding_source (company_id, source_type, source_name, fund_rs, district, notes, created_ts)
                 VALUES (?, ?, ?, ?, ?, ?, NOW())",
                [$company_id, $source_type, $source_name, $fund_rs, $district, $notes]
            );
            $new_id = $this->db->insert_id();
            $this->_json(['ok' => true, 'action' => 'created', 'id' => (int)$new_id, 'generated_at' => date('c')]);
        }
    }
}

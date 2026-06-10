<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * LeadEnrich_api.php  (Phase 2 - Agent F - 2026-06-08)
 *
 * A4 Section-135 Auto-Enrich
 *
 * Reads MCA21 / csr.gov.in data from existing csr_gov_in_master table
 * (populated by the Mca21 controller's import pipeline).
 *
 * ADDITIVE ONLY: does NOT overwrite company_master.
 * Writes enrichable fields to new enrich_log table.
 *
 * Endpoints:
 *   POST /api/enrich/company     Enrich by company_id or compname (writes to log)
 *   GET  /api/enrich/preview     Preview enrichable fields without writing
 *
 * Auth: Bearer 4eBaiAT7r4zu6OK3b8evjLNia1D7RGgb0qRTuLJfUSo
 * Output: ASCII only. Rs for rupees. No em/en-dashes.
 *
 * Author: STEM Phase 2 Agent F  2026-06-08
 */
class LeadEnrich_api extends CI_Controller {

    private $_known_token = '4eBaiAT7r4zu6OK3b8evjLNia1D7RGgb0qRTuLJfUSo';
    private $_authed_uid  = 0;

    public function __construct() {
        parent::__construct();
        $this->load->database();
        header('Content-Type: application/json; charset=utf-8');
        $this->_ensure_table();
    }

    // -------------------------------------------------------------------------
    // TABLE BOOTSTRAP
    // -------------------------------------------------------------------------
    private function _ensure_table() {
        $this->db->query("
            CREATE TABLE IF NOT EXISTS enrich_log (
                id          BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
                company_id  INT NOT NULL COMMENT 'company_master.id',
                compname    VARCHAR(500) NULL,
                fields_json LONGTEXT     NOT NULL COMMENT 'JSON of enriched fields',
                source      VARCHAR(80)  NOT NULL DEFAULT 'mca21_csr_gov_in',
                enriched_by INT UNSIGNED NULL COMMENT 'uid who triggered enrich, 0=system',
                ts          DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
                INDEX idx_company_id (company_id),
                INDEX idx_ts (ts)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
        ");
    }

    // -------------------------------------------------------------------------
    // POST /api/enrich/company
    // Body: company_id (int) OR compname (string). Writes to enrich_log.
    // -------------------------------------------------------------------------
    public function company() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return $this->_json(array('ok' => false, 'error' => 'POST required'), 405);
        }
        if (!$this->_bearer_ok()) {
            return $this->_json(array('ok' => false, 'error' => 'Unauthorized'), 401);
        }

        $body       = $this->_post_body();
        $company_id = isset($body['company_id']) ? (int) $body['company_id'] : 0;
        $compname   = isset($body['compname'])   ? trim($body['compname'])   : '';

        if ($company_id <= 0 && empty($compname)) {
            return $this->_json(array('ok' => false, 'error' => 'company_id or compname is required'), 422);
        }

        // Resolve company_master record
        $company = $this->_resolve_company($company_id, $compname);
        if (empty($company)) {
            return $this->_json(array('ok' => false, 'error' => 'company not found in company_master'), 404);
        }
        $company_id = (int) $company['id'];

        // Look up MCA21 / CSR data
        $enrich = $this->_mca21_lookup($company['compname']);

        if (empty($enrich)) {
            return $this->_json(array(
                'ok'       => true,
                'enriched' => false,
                'company'  => array('id' => $company_id, 'compname' => trim($company['compname'])),
                'note'     => 'MCA21 access pending or no match found for this company. No enrich_log entry written.',
            ));
        }

        // Write to enrich_log (additive - never overwrite company_master)
        $log_data = array(
            'company_id'  => $company_id,
            'compname'    => trim($company['compname']),
            'fields_json' => json_encode($enrich, JSON_UNESCAPED_UNICODE),
            'source'      => 'mca21_csr_gov_in',
            'enriched_by' => $this->_authed_uid ?: 0,
            'ts'          => date('Y-m-d H:i:s'),
        );
        $this->db->insert('enrich_log', $log_data);
        $log_id = $this->db->insert_id();

        $this->_json(array(
            'ok'            => true,
            'enriched'      => true,
            'enrich_log_id' => $log_id,
            'company'       => array('id' => $company_id, 'compname' => trim($company['compname'])),
            'fields'        => $enrich,
            'note'          => 'Enriched fields written to enrich_log. company_master was NOT modified (additive only).',
        ));
    }

    // -------------------------------------------------------------------------
    // GET /api/enrich/preview?company_id=
    // Preview enrichable fields without writing to log.
    // -------------------------------------------------------------------------
    public function preview() {
        if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
            return $this->_json(array('ok' => false, 'error' => 'GET required'), 405);
        }
        if (!$this->_bearer_ok()) {
            return $this->_json(array('ok' => false, 'error' => 'Unauthorized'), 401);
        }

        $company_id = (int) $this->input->get('company_id');
        $compname   = trim($this->input->get('compname') ?: '');

        if ($company_id <= 0 && empty($compname)) {
            return $this->_json(array('ok' => false, 'error' => 'company_id or compname is required'), 422);
        }

        $company = $this->_resolve_company($company_id, $compname);
        if (empty($company)) {
            return $this->_json(array('ok' => false, 'error' => 'company not found in company_master'), 404);
        }
        $company_id = (int) $company['id'];

        $enrich = $this->_mca21_lookup($company['compname']);

        if (empty($enrich)) {
            return $this->_json(array(
                'ok'       => true,
                'enriched' => false,
                'company'  => array('id' => $company_id, 'compname' => trim($company['compname'])),
                'note'     => 'MCA21 access pending or no match found. No data written.',
            ));
        }

        // Also show previous enrich_log entries for this company
        $prev_logs = $this->db->select('id, source, ts')
            ->from('enrich_log')
            ->where('company_id', $company_id)
            ->order_by('ts', 'DESC')
            ->limit(5)
            ->get()->result_array();

        $this->_json(array(
            'ok'            => true,
            'enriched'      => true,
            'company'       => array('id' => $company_id, 'compname' => trim($company['compname'])),
            'fields'        => $enrich,
            'previous_logs' => $prev_logs,
            'note'          => 'Preview only. Use POST /api/enrich/company to write to enrich_log.',
        ));
    }

    // -------------------------------------------------------------------------
    // INTERNAL: resolve company from company_master
    // -------------------------------------------------------------------------
    private function _resolve_company($company_id, $compname) {
        if ($company_id > 0) {
            return $this->db->get_where('company_master', array('id' => $company_id))->row_array();
        }
        // Name match - try exact first, then LIKE
        $row = $this->db->where('compname', $compname)->get('company_master')->row_array();
        if (!empty($row)) return $row;
        // LIKE search
        $row = $this->db->like('compname', $compname)->limit(1)->get('company_master')->row_array();
        return $row ?: null;
    }

    // -------------------------------------------------------------------------
    // INTERNAL: MCA21 / csr_gov_in lookup
    //
    // Reads from csr_gov_in_master (populated by Mca21 controller / CSV import).
    // Attempts fuzzy name match.
    // Returns array of enrichable fields, or empty array if no data.
    //
    // NOTE: Live csr.gov.in API access is deferred (apollo_csr_api_key placeholder).
    // This reads the locally ingested dataset only.
    // -------------------------------------------------------------------------
    private function _mca21_lookup($compname) {
        if (empty($compname)) return array();

        $name_clean = trim($compname);

        // Try exact match first
        $rows = $this->db->where('company_name', $name_clean)
            ->order_by('fy_year', 'DESC')
            ->limit(5)
            ->get('csr_gov_in_master')->result_array();

        if (empty($rows)) {
            // LIKE match - partial name
            $rows = $this->db->like('company_name', $name_clean)
                ->order_by('fy_year', 'DESC')
                ->limit(5)
                ->get('csr_gov_in_master')->result_array();
        }

        if (empty($rows)) {
            // Keyword match on first significant word (>= 4 chars)
            $words = preg_split('/\s+/', $name_clean);
            $kw = '';
            foreach ($words as $w) {
                if (strlen($w) >= 4) { $kw = $w; break; }
            }
            if (!empty($kw)) {
                $rows = $this->db->like('company_name', $kw)
                    ->order_by('fy_year', 'DESC')
                    ->limit(5)
                    ->get('csr_gov_in_master')->result_array();
            }
        }

        if (empty($rows)) return array(); // no match

        // Build enrichable fields from the best row(s)
        $latest = $rows[0];
        $all_fy = array();
        foreach ($rows as $r) {
            $all_fy[] = array(
                'fy_year'             => $r['fy_year'],
                'csr_spent_rs_cr'     => $r['csr_spent_rs_cr'],
                'csr_obligation_rs_cr'=> $r['csr_obligation_rs_cr'],
                'sector'              => $r['sector'],
                'state'               => $r['state'],
            );
        }

        // Decode themes JSON safely
        $themes = array();
        if (!empty($latest['schedule_vii_themes_json'])) {
            $t = json_decode($latest['schedule_vii_themes_json'], true);
            if (is_array($t)) $themes = $t;
        }

        return array(
            'source'               => 'csr_gov_in_master',
            'matched_name'         => $latest['company_name'],
            'cin'                  => $latest['cin'],
            'latest_fy'            => $latest['fy_year'],
            'csr_spent_rs_cr'      => $latest['csr_spent_rs_cr'],
            'csr_obligation_rs_cr' => $latest['csr_obligation_rs_cr'],
            'sector'               => $latest['sector'],
            'state'                => $latest['state'],
            'schedule_vii_themes'  => $themes,
            'fy_history'           => $all_fy,
            'source_url'           => isset($latest['source_url']) ? $latest['source_url'] : 'https://www.csr.gov.in/',
            'ingested_at'          => $latest['ingested_at'],
        );
    }

    // -------------------------------------------------------------------------
    // Auth helpers
    // -------------------------------------------------------------------------
    private function _bearer_ok() {
        $hdr = isset($_SERVER['HTTP_AUTHORIZATION']) ? $_SERVER['HTTP_AUTHORIZATION'] : '';
        if (!$hdr && function_exists('apache_request_headers')) {
            $h = apache_request_headers();
            if (isset($h['Authorization'])) $hdr = $h['Authorization'];
        }
        if (!$hdr || stripos($hdr, 'Bearer ') !== 0) return false;
        $token = trim(substr($hdr, 7));
        $env   = getenv('STEM_DIGEST_TOKEN');
        if ($env && hash_equals($env, $token)) return true;
        if (hash_equals($this->_known_token, $token)) return true;
        $uid = $this->_jwt_valid($token);
        if ($uid) { $this->_authed_uid = $uid; return true; }
        // rimlyproof_bearerdelegate_20260608: also accept per-user login token via shared BearerAuth library (additive)
        try {
            $CI =& get_instance();
            if (!isset($CI->bearerauth)) { $CI->load->library('BearerAuth'); }
            $___ba = $CI->bearerauth->resolve();
            if (!empty($___ba['ok']) && !empty($___ba['uid'])) {
                if (property_exists($this, '_authed_uid')) { $this->_authed_uid = (int)$___ba['uid']; }
                return true;
            }
        } catch (Exception $e) {}
        return false;
    }

    private function _jwt_valid($token) {
        if (empty($token)) return false;
        $secret = getenv('STEM_DIGEST_TOKEN') ?: $this->_known_token;
        $days   = array(date('Y-m-d'), date('Y-m-d', strtotime('-1 day')), date('Y-m-d', strtotime('+1 day')));
        $cands  = array();
        foreach (array('uid','cm_uid','rm_uid','bd_uid','by_uid','user_id') as $k) {
            if (!empty($_GET[$k]))  $cands[(int)$_GET[$k]]  = 1;
            if (!empty($_POST[$k])) $cands[(int)$_POST[$k]] = 1;
        }
        foreach (array_keys($cands) as $uid) {
            foreach ($days as $d) {
                if (hash_equals(sha1($secret.'|'.$uid.'|'.$d), $token)) return (int)$uid;
            }
        }
        static $all = null;
        if ($all === null) {
            $rows = $this->db->select('uid')->from('user')->where('active', 1)->get()->result();
            $all  = array();
            foreach ($rows as $r) $all[] = (int)$r->uid;
        }
        foreach ($all as $uid) {
            foreach ($days as $d) {
                if (hash_equals(sha1($secret.'|'.$uid.'|'.$d), $token)) return (int)$uid;
            }
        }
        return false;
    }

    private function _json($data, $code = 200) {
        http_response_code($code);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($data, JSON_UNESCAPED_UNICODE);
        exit;
    }

    private function _post_body() {
        $raw = file_get_contents('php://input');
        if ($raw) {
            $d = json_decode($raw, true);
            if (is_array($d)) return $d;
        }
        return $_POST;
    }
}

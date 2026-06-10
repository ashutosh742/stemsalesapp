<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Mca21 Controller
 * S.2 - MCA-21 CSR sync (CSV seed + Apollo placeholder)
 *
 * POST /api/mca21/import_csv
 *   Multipart file upload (field name: csv_file).
 *   Parses CSV and upserts into existing csr_prospect table.
 *   Apollo CSR API key is a placeholder -- live fetch is deferred.
 *
 * GET /api/mca21/sync_status
 *   Returns last run summary from mca21_csr_sync_log.
 *
 * NOTE: Live csr.gov.in fetch is PARTIAL / deferred until the
 * Apollo CSR API key is provided. The import_csv endpoint accepts
 * a human-supplied CSV file in the interim.
 */
class Mca21 extends CI_Controller {

    private $_known_token = '4eBaiAT7r4zu6OK3b8evjLNia1D7RGgb0qRTuLJfUSo';

    private $CSV_COLUMNS = [
        'company_name',
        'cin',
        'pan',
        'csr_year',
        'avg_net_profit',
        'prescribed_csr_amount',
        'amount_spent',
        'contact_email',
        'contact_phone',
        'city',
        'state',
    ];

    public function __construct()
    {
        parent::__construct();
        $this->load->database();
        header('Content-Type: application/json');
    }

    // ----------------------------------------------------------------
    // POST /api/mca21/import_csv
    // ----------------------------------------------------------------
    public function import_csv()
    {
        if (!$this->_bearer_ok()) {
            $this->_json(['status' => 'error', 'message' => 'Unauthorized'], 401);
            return;
        }

        $apollo_key  = $this->config->item('apollo_csr_api_key');
        $apollo_mode = (!empty($apollo_key) && $apollo_key !== 'PLACEHOLDER') ? 'live' : 'csv_only';

        if (empty($_FILES['csv_file']['tmp_name'])) {
            $this->_json([
                'status'      => 'error',
                'message'     => 'No file uploaded. Send multipart/form-data with field csv_file.',
                'apollo_mode' => $apollo_mode,
            ], 400);
            return;
        }

        $tmp    = $_FILES['csv_file']['tmp_name'];
        $handle = fopen($tmp, 'r');
        if ($handle === FALSE) {
            $this->_json(['status' => 'error', 'message' => 'Cannot read uploaded file'], 500);
            return;
        }

        $header = fgetcsv($handle);
        if ($header === FALSE) {
            fclose($handle);
            $this->_json(['status' => 'error', 'message' => 'CSV is empty or unreadable'], 400);
            return;
        }
        $header = array_map('strtolower', array_map('trim', $header));

        $rows_imported = 0;
        $rows_skipped  = 0;
        $csr_year_seen = '';

        while (($row = fgetcsv($handle)) !== FALSE) {
            if (count($row) !== count($header)) {
                $rows_skipped++;
                continue;
            }
            $record = array_combine($header, $row);

            if (empty($record['cin']) && empty($record['company_name'])) {
                $rows_skipped++;
                continue;
            }

            $csr_year_seen = isset($record['csr_year']) ? trim($record['csr_year']) : '';

            $existing = NULL;
            if (!empty($record['cin'])) {
                $this->db->where('cin', $record['cin']);
                $q = $this->db->get('csr_prospect');
                $existing = $q->row();
            }

            $data = [];
            foreach ($this->CSV_COLUMNS as $col) {
                if (isset($record[$col])) {
                    $data[$col] = trim($record[$col]);
                }
            }
            $data['updated_at'] = date('Y-m-d H:i:s');

            if ($existing) {
                $this->db->where('id', $existing->id);
                $this->db->update('csr_prospect', $data);
            } else {
                $data['created_at'] = date('Y-m-d H:i:s');
                $this->db->insert('csr_prospect', $data);
            }
            $rows_imported++;
        }
        fclose($handle);

        $this->db->insert('mca21_csr_sync_log', [
            'csr_year'      => $csr_year_seen,
            'file_url'      => 'manual_csv_upload',
            'rows_imported' => $rows_imported,
            'last_run_at'   => date('Y-m-d H:i:s'),
        ]);
        $sync_log_id = $this->db->insert_id();

        $this->_json([
            'status'        => 'ok',
            'rows_imported' => $rows_imported,
            'rows_skipped'  => $rows_skipped,
            'sync_log_id'   => $sync_log_id,
            'apollo_mode'   => $apollo_mode,
            'note'          => $apollo_mode === 'csv_only'
                ? 'Apollo CSR API key not configured. Only manual CSV import is active.'
                : 'Apollo live sync enabled.',
        ], 200);
    }

    // ----------------------------------------------------------------
    // GET /api/mca21/sync_status
    // ----------------------------------------------------------------
    public function sync_status()
    {
        if (!$this->_bearer_ok()) {
            $this->_json(['status' => 'error', 'message' => 'Unauthorized'], 401);
            return;
        }

        $this->db->order_by('last_run_at', 'DESC');
        $this->db->limit(10);
        $query = $this->db->get('mca21_csr_sync_log');
        $rows  = $query->result_array();
        $last  = !empty($rows) ? $rows[0] : NULL;

        $apollo_key  = $this->config->item('apollo_csr_api_key');
        $apollo_mode = (!empty($apollo_key) && $apollo_key !== 'PLACEHOLDER') ? 'live' : 'csv_only';

        $this->_json([
            'status'      => 'ok',
            'apollo_mode' => $apollo_mode,
            'last_run'    => $last,
            'recent_runs' => $rows,
        ], 200);
    }

    // ----------------------------------------------------------------
    // Helpers
    // ----------------------------------------------------------------
    private function _bearer_ok()
    {
        $hdr = '';
        if (isset($_SERVER['HTTP_AUTHORIZATION']))              $hdr = $_SERVER['HTTP_AUTHORIZATION'];
        elseif (isset($_SERVER['REDIRECT_HTTP_AUTHORIZATION'])) $hdr = $_SERVER['REDIRECT_HTTP_AUTHORIZATION'];
        elseif (function_exists('apache_request_headers')) {
            $h = apache_request_headers();
            if (isset($h['Authorization'])) $hdr = $h['Authorization'];
        }
        if (stripos($hdr, 'Bearer ') !== 0) return false;
        $tok    = trim(substr($hdr, 7));
        $secret = getenv('STEM_DIGEST_TOKEN') ?: $this->_known_token;
        if (hash_equals($secret, $tok)) return true;
        // Per-user JWT - added AgentC 28 May 2026
        $days = array(date('Y-m-d'), date('Y-m-d', strtotime('-1 day')), date('Y-m-d', strtotime('+1 day')));
        $candidates = array();
        foreach (array('uid','cm_uid','rm_uid','bd_uid','acm_uid','user_id') as $k) {
            if (isset($_GET[$k]) && (int)$_GET[$k] > 0) $candidates[(int)$_GET[$k]] = 1;
            if (isset($_POST[$k]) && (int)$_POST[$k] > 0) $candidates[(int)$_POST[$k]] = 1;
        }
        foreach (array_keys($candidates) as $cuid) {
            foreach ($days as $d) {
                if (hash_equals(sha1($secret.'|'.$cuid.'|'.$d), $tok)) return true;
            }
        }
        static $all_uids = null;
        if ($all_uids === null) {
            $rows = $this->db->select('uid')->from('user')->where('active', 1)->get()->result();
            $all_uids = array();
            foreach ($rows as $r) $all_uids[] = (int)$r->uid;
        }
        foreach ($all_uids as $cuid) {
            foreach ($days as $d) {
                if (hash_equals(sha1($secret.'|'.$cuid.'|'.$d), $tok)) return true;
            }
        }
        return false;
    }

    private function _json($data, $code = 200)
    {
        http_response_code($code);
        echo json_encode($data);
        exit;
    }

    // GET /api/mca  (AgentC 28 May 2026 - alias for sync_status)
    public function status()
    {
        return $this->sync_status();
    }

}
<?php
/**
 * CsrClosureController.php
 *
 * Migration 062 - CSR Intelligence Closure
 * REST API controller for CSR data, DMFT calendar, and Apollo sync status.
 *
 * CodeIgniter 3 controller. Plain English. No em-dashes. No non-ASCII characters.
 * Bearer token authentication on all endpoints.
 *
 * Routes to add in application/config/routes.php:
 *   $route['api/csr_closure/probe']                = 'CsrClosureController/probe';
 *   $route['api/csr_closure/lookup']               = 'CsrClosureController/lookup';
 *   $route['api/csr_closure/top_spenders']         = 'CsrClosureController/top_spenders';
 *   $route['api/csr_closure/dmft_calendar']        = 'CsrClosureController/dmft_calendar';
 *   $route['api/csr_closure/import_csr']           = 'CsrClosureController/import_csr';
 *   $route['api/csr_closure/import_dmft']          = 'CsrClosureController/import_dmft';
 *   $route['api/csr_closure/apollo_status']        = 'CsrClosureController/apollo_status';
 */
defined('BASEPATH') OR exit('No direct script access allowed');

class CsrClosureController extends CI_Controller
{
    // Expected Bearer token; set via CI config or environment.
    // Store in application/config/custom.php as $config['csr_bearer_token'] = 'YOUR_TOKEN';
    private $bearer_token;

    // Workspace seeds directory
    const SEEDS_DIR = '/home/user/workspace/seeds/';

    // ---------------------------------------------------------------------------
    // Constructor
    // ---------------------------------------------------------------------------
    public function __construct()
    {
        parent::__construct();
        $this->load->model('CsrClosure_model');
        $this->load->config('custom', TRUE);

        // Retrieve bearer token from config; fall back to environment variable
        $config_token = $this->config->item('csr_bearer_token', 'custom');
        $this->bearer_token = $config_token ?: getenv('CSR_BEARER_TOKEN');
    }

    // ---------------------------------------------------------------------------
    // _verify_auth  (private)
    //
    // Validates the Authorization header against the configured Bearer token.
    // Sends 401 and halts execution if auth fails.
    // ---------------------------------------------------------------------------
    private function _verify_auth()
    {
        $auth_header = $this->input->get_request_header('Authorization', TRUE);

        if (empty($auth_header)) {
            $this->_json_error(401, 'Authorization header missing');
        }

        if (stripos($auth_header, 'Bearer ') !== 0) {
            $this->_json_error(401, 'Authorization header must use Bearer scheme');
        }

        $token = trim(substr($auth_header, 7));

        if (empty($this->bearer_token) || ! hash_equals($this->bearer_token, $token)) {
            $this->_json_error(401, 'Invalid or expired Bearer token');
        }
    }

    // ---------------------------------------------------------------------------
    // _json_response  (private)
    //
    // Sends a JSON response with the given HTTP status code and payload.
    // ---------------------------------------------------------------------------
    private function _json_response($status_code, $data)
    {
        $this->output->set_status_header($status_code);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        exit;
    }

    // ---------------------------------------------------------------------------
    // _json_error  (private)
    //
    // Sends a standardised JSON error response and halts.
    // ---------------------------------------------------------------------------
    private function _json_error($status_code, $message)
    {
        $this->_json_response($status_code, [
            'status'  => 'error',
            'code'    => $status_code,
            'message' => $message,
        ]);
    }

    // ---------------------------------------------------------------------------
    // _require_method  (private)
    // ---------------------------------------------------------------------------
    private function _require_method($method)
    {
        if ($_SERVER['REQUEST_METHOD'] !== strtoupper($method)) {
            $this->_json_error(405, 'Method Not Allowed. Expected: ' . strtoupper($method));
        }
    }

    // ---------------------------------------------------------------------------
    // GET /api/csr_closure/probe
    //
    // Health check endpoint. Returns 200 with service info.
    // Does NOT require Bearer auth so load balancers can use it.
    // ---------------------------------------------------------------------------
    public function probe()
    {
        $this->_require_method('GET');

        $this->_json_response(200, [
            'status'     => 'ok',
            'service'    => 'CSR Closure Intelligence API',
            'migration'  => 'M062',
            'timestamp'  => date('Y-m-d H:i:s'),
        ]);
    }

    // ---------------------------------------------------------------------------
    // GET /api/csr_closure/lookup?cin=X
    //
    // Returns all CSR disclosure records for a given CIN, plus the latest
    // Apollo sync log entry.
    //
    // Query params:
    //   cin  (required)  Corporate Identification Number
    // ---------------------------------------------------------------------------
    public function lookup()
    {
        $this->_require_method('GET');
        $this->_verify_auth();

        $cin = strtoupper(trim($this->input->get('cin', TRUE)));

        if (empty($cin)) {
            $this->_json_error(400, 'Query parameter "cin" is required');
        }

        // Basic CIN format validation (21 chars, starts with L or U, or known PSU patterns)
        if (strlen($cin) > 25) {
            $this->_json_error(400, 'CIN value is too long');
        }

        $data = $this->CsrClosure_model->get_csr_for_company($cin);

        if (empty($data['csr_records'])) {
            $this->_json_response(404, [
                'status'  => 'not_found',
                'message' => 'No CSR records found for CIN: ' . $cin,
                'cin'     => $cin,
            ]);
        }

        $this->_json_response(200, [
            'status' => 'ok',
            'data'   => $data,
        ]);
    }

    // ---------------------------------------------------------------------------
    // GET /api/csr_closure/top_spenders?fy=FY26&limit=50
    //
    // Returns top CSR spenders for a given financial year, ordered by spend
    // descending.
    //
    // Query params:
    //   fy     (optional)  Financial year string e.g. FY2026. Defaults to latest.
    //   limit  (optional)  Number of rows to return. Default 50, max 200.
    // ---------------------------------------------------------------------------
    public function top_spenders()
    {
        $this->_require_method('GET');
        $this->_verify_auth();

        $fy    = trim($this->input->get('fy', TRUE));
        $limit = (int) $this->input->get('limit', TRUE);

        if ($limit <= 0 || $limit > 200) {
            $limit = 50;
        }

        // Normalise short-form FY26 to FY2026
        if (preg_match('/^FY(\d{2})$/', strtoupper($fy), $m)) {
            $fy = 'FY20' . $m[1];
        }

        $this->load->database();

        if ( ! empty($fy)) {
            $this->db->where('fy_year', strtoupper($fy));
        } else {
            // Use the most recent year available
            $max_fy = $this->db->select_max('fy_year')->get('csr_gov_in_master')->row_array();
            if ( ! empty($max_fy['fy_year'])) {
                $this->db->where('fy_year', $max_fy['fy_year']);
                $fy = $max_fy['fy_year'];
            }
        }

        $rows = $this->db
            ->order_by('csr_spent_rs_cr', 'DESC')
            ->limit($limit)
            ->get('csr_gov_in_master')
            ->result_array();

        // Decode themes JSON
        foreach ($rows as &$row) {
            if ( ! empty($row['schedule_vii_themes_json'])) {
                $row['themes'] = json_decode($row['schedule_vii_themes_json'], TRUE);
            } else {
                $row['themes'] = [];
            }
            unset($row['schedule_vii_themes_json']);
        }
        unset($row);

        $this->_json_response(200, [
            'status'     => 'ok',
            'fy_year'    => $fy,
            'count'      => count($rows),
            'spenders'   => $rows,
        ]);
    }

    // ---------------------------------------------------------------------------
    // GET /api/csr_closure/dmft_calendar?state=X&within_days=180
    //
    // Returns DMFT tranche records grouped by calendar month for the UI calendar view.
    //
    // Query params:
    //   state        (optional)  Filter by state name
    //   within_days  (optional)  Days window from today. Default 180, max 730.
    // ---------------------------------------------------------------------------
    public function dmft_calendar()
    {
        $this->_require_method('GET');
        $this->_verify_auth();

        $state       = trim($this->input->get('state', TRUE));
        $within_days = (int) $this->input->get('within_days', TRUE);

        if ($within_days <= 0 || $within_days > 730) {
            $within_days = 180;
        }

        $state = $state ?: NULL;

        $rows = $this->CsrClosure_model->get_dmft_calendar($state, $within_days);

        // Group by calendar month for the React Native calendar UI
        $grouped = [];
        foreach ($rows as $row) {
            $month_key = substr($row['due_date'], 0, 7); // YYYY-MM
            if ( ! isset($grouped[$month_key])) {
                $grouped[$month_key] = [];
            }
            $grouped[$month_key][] = $row;
        }

        // Convert to ordered array for the API consumer
        ksort($grouped);
        $months_array = [];
        foreach ($grouped as $month => $tranches) {
            $months_array[] = [
                'month'    => $month,
                'tranches' => $tranches,
            ];
        }

        $this->_json_response(200, [
            'status'       => 'ok',
            'state_filter' => $state,
            'within_days'  => $within_days,
            'months'       => $months_array,
            'total_rows'   => count($rows),
        ]);
    }

    // ---------------------------------------------------------------------------
    // POST /api/csr_closure/import_csr
    //
    // Admin endpoint: triggers a bulk import of the CSR seed CSV from the
    // workspace seeds directory. Requires Bearer auth.
    //
    // Reads: /home/user/workspace/seeds/csr_gov_in_fy25_fy26_seed.csv
    //        (or a path provided in the JSON body as 'csv_path')
    // ---------------------------------------------------------------------------
    public function import_csr()
    {
        $this->_require_method('POST');
        $this->_verify_auth();

        $body     = json_decode($this->input->raw_input_stream, TRUE);
        $csv_path = isset($body['csv_path']) ? $body['csv_path'] : NULL;

        if (empty($csv_path)) {
            $csv_path = self::SEEDS_DIR . 'csr_gov_in_fy25_fy26_seed.csv';
        }

        // Restrict to workspace directory for security
        if (strpos(realpath(dirname($csv_path)), '/home/user/workspace') !== 0) {
            $this->_json_error(403, 'csv_path must reside within /home/user/workspace/');
        }

        $result = $this->CsrClosure_model->import_csr_csv($csv_path);

        $this->_json_response(200, [
            'status'   => 'ok',
            'csv_path' => $csv_path,
            'result'   => $result,
        ]);
    }

    // ---------------------------------------------------------------------------
    // POST /api/csr_closure/import_dmft
    //
    // Admin endpoint: triggers a bulk import of the DMFT seed CSV.
    //
    // Reads: /home/user/workspace/seeds/dmft_seed.csv
    //        (or a path provided in the JSON body as 'csv_path')
    // ---------------------------------------------------------------------------
    public function import_dmft()
    {
        $this->_require_method('POST');
        $this->_verify_auth();

        $body     = json_decode($this->input->raw_input_stream, TRUE);
        $csv_path = isset($body['csv_path']) ? $body['csv_path'] : NULL;

        if (empty($csv_path)) {
            $csv_path = self::SEEDS_DIR . 'dmft_seed.csv';
        }

        // Restrict to workspace directory for security
        if (strpos(realpath(dirname($csv_path)), '/home/user/workspace') !== 0) {
            $this->_json_error(403, 'csv_path must reside within /home/user/workspace/');
        }

        $result = $this->CsrClosure_model->import_dmft_csv($csv_path);

        $this->_json_response(200, [
            'status'   => 'ok',
            'csv_path' => $csv_path,
            'result'   => $result,
        ]);
    }

    // ---------------------------------------------------------------------------
    // GET /api/csr_closure/apollo_status
    //
    // Returns whether the APOLLO_API_KEY environment variable is provisioned.
    // Does not reveal the key value.
    //
    // Also checks for the marker file at /home/user/workspace/seeds/apollo_key.done
    // ---------------------------------------------------------------------------
    public function apollo_status()
    {
        $this->_require_method('GET');
        $this->_verify_auth();

        $api_key     = getenv('APOLLO_API_KEY');
        $key_wired   = ! empty($api_key);
        $marker_path = '/home/user/workspace/seeds/apollo_key.done';
        $marker_exists = file_exists($marker_path);

        // Count recent successful Apollo lookups (last 7 days)
        $seven_days_ago = date('Y-m-d H:i:s', strtotime('-7 days'));
        $this->load->database();
        $recent_success = $this->db
            ->where('http_status', 200)
            ->where('fetched_at >=', $seven_days_ago)
            ->count_all_results('apollo_sync_log');

        $recent_zero = $this->db
            ->where('http_status', 0)
            ->where('fetched_at >=', $seven_days_ago)
            ->count_all_results('apollo_sync_log');

        $this->_json_response(200, [
            'status'                    => 'ok',
            'apollo_api_key_wired'      => $key_wired,
            'apollo_key_marker_present' => $marker_exists,
            'marker_path'               => $marker_path,
            'provision_note'            => 'Set APOLLO_API_KEY env var on server and touch ' . $marker_path . ' to activate Apollo sync',
            'recent_7_days'             => [
                'successful_lookups'  => $recent_success,
                'not_attempted_count' => $recent_zero,
            ],
        ]);
    }

    // -------------------------------------------------------------------------
    // GET /api/csr_closure  (AgentC 28 May 2026 - JWT-aware probe alias)
    // -------------------------------------------------------------------------
    private function _jwt_ok_csr() {
        $hdr = isset($_SERVER['HTTP_AUTHORIZATION']) ? $_SERVER['HTTP_AUTHORIZATION'] : '';
        if (!$hdr && function_exists('apache_request_headers')) {
            $h = apache_request_headers(); if (isset($h['Authorization'])) $hdr = $h['Authorization'];
        }
        if (!$hdr || stripos($hdr, 'Bearer ') !== 0) return false;
        $token  = trim(substr($hdr, 7));
        $known  = getenv('STEM_DIGEST_TOKEN') ?: '4eBaiAT7r4zu6OK3b8evjLNia1D7RGgb0qRTuLJfUSo';
        if (hash_equals($known, $token)) return true;
        // Per-user JWT
        $secret = $known;
        $days   = array(date('Y-m-d'), date('Y-m-d', strtotime('-1 day')), date('Y-m-d', strtotime('+1 day')));
        $candidates = array();
        foreach (array('uid','cm_uid','rm_uid','bd_uid','acm_uid','user_id') as $k) {
            if (isset($_GET[$k]) && (int)$_GET[$k] > 0) $candidates[(int)$_GET[$k]] = 1;
            if (isset($_POST[$k]) && (int)$_POST[$k] > 0) $candidates[(int)$_POST[$k]] = 1;
        }
        foreach (array_keys($candidates) as $cuid) {
            foreach ($days as $d) {
                if (hash_equals(sha1($secret.'|'.$cuid.'|'.$d), $token)) return true;
            }
        }
        if (!isset($this->db) || !is_object($this->db)) { $this->load->database(); }
        static $all_uids = null;
        if ($all_uids === null) {
            $rows = $this->db->select('uid')->from('user')->where('active', 1)->get()->result();
            $all_uids = array();
            foreach ($rows as $r) $all_uids[] = (int)$r->uid;
        }
        foreach ($all_uids as $cuid) {
            foreach ($days as $d) {
                if (hash_equals(sha1($secret.'|'.$cuid.'|'.$d), $token)) return true;
            }
        }
        return false;
    }

    public function index()
    {
        if (!$this->_jwt_ok_csr()) {
            $this->_json_error(401, 'Unauthorized');
        }
        // Return probe-style summary for the CSR closure feature
        try {
            if (!isset($this->db) || !is_object($this->db)) { $this->load->database(); }
            $stats = $this->db->query("
                SELECT COUNT(*) AS total_closures FROM csr_closure_log
                WHERE YEAR(created_at) = YEAR(NOW())
            ")->row_array();
        } catch (Exception $e) { $stats = array('total_closures' => 0); }
        $this->_json_response(200, array(
            'ok'             => true,
            'service'        => 'csr_closure',
            'ts'             => date('c'),
            'total_closures' => isset($stats['total_closures']) ? (int)$stats['total_closures'] : 0,
        ));
    }

}
<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Lead_budget_range_api.php
 * S.9 Rs Cr Budget Range Estimator
 * Routes:
 *   GET /api/lead/budget_range/:cid   - read budget range (cid = init_call.id)
 *   PUT /api/lead/budget_range/:cid   - update budget range
 * Requires Bearer token.
 * Date: 2025-05-28
 */
class Lead_budget_range_api extends CI_Controller {

    private $_known_token = '4eBaiAT7r4zu6OK3b8evjLNia1D7RGgb0qRTuLJfUSo';

    public function __construct() {
        parent::__construct();
        $this->load->database();
        header('Content-Type: application/json; charset=utf-8');
    }

    private function _bearer_ok() {
        if (function_exists('authunify_ok') && authunify_ok()) { return true; } // rimlyproof_authunify_20260609

        $hdr = isset($_SERVER['HTTP_AUTHORIZATION']) ? $_SERVER['HTTP_AUTHORIZATION'] : '';
        if (!$hdr && function_exists('apache_request_headers')) {
            $h = apache_request_headers();
            if (isset($h['Authorization'])) $hdr = $h['Authorization'];
        }
        if (!$hdr || stripos($hdr, 'Bearer ') !== 0) return false;
        $token = trim(substr($hdr, 7));
        $env = getenv('STEM_DIGEST_TOKEN');
        if ($env && hash_equals($env, $token)) return true;
        return hash_equals($this->_known_token, $token);
    }

    private function _json($data, $code = 200) {
        http_response_code($code);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($data);
        exit;
    }

    // -----------------------------------------------------------------------
    // GET /api/lead/budget_range/:cid  (cid = init_call.id)
    // -----------------------------------------------------------------------
    public function get($cid = null) {
        if (!$this->_bearer_ok()) {
            $this->_json(['ok' => false, 'error' => 'Unauthorized'], 401);
        }

        $lead_id = (int)$cid;
        if ($lead_id <= 0) {
            $this->_json(['ok' => false, 'error' => 'Invalid or missing lead id (cid)'], 400);
        }

        $row = $this->db
            ->select('id, fbudget, fbudget_min_cr, fbudget_max_cr, fbudget_assumptions')
            ->where('id', $lead_id)
            ->get('init_call')
            ->row_array();

        if (!$row) {
            $this->_json(['ok' => false, 'error' => 'Lead not found', 'cid' => $lead_id], 404);
        }

        $min = $row['fbudget_min_cr'] !== null ? (float)$row['fbudget_min_cr'] : null;
        $max = $row['fbudget_max_cr'] !== null ? (float)$row['fbudget_max_cr'] : null;

        $this->_json([
            'ok'   => true,
            'cid'  => (int)$row['id'],
            'data' => [
                'fbudget'             => $row['fbudget'],
                'fbudget_min_cr'      => $min,
                'fbudget_max_cr'      => $max,
                'fbudget_assumptions' => $row['fbudget_assumptions'],
                'display'             => ($min !== null && $max !== null)
                    ? 'Rs ' . number_format($min, 2) . ' cr - Rs ' . number_format($max, 2) . ' cr'
                    : null,
            ],
        ]);
    }

    // -----------------------------------------------------------------------
    // PUT /api/lead/budget_range/:cid
    // Body (JSON): { "min_cr": float, "max_cr": float, "assumptions": string }
    // -----------------------------------------------------------------------
    public function update($cid = null) {
        if (!$this->_bearer_ok()) {
            $this->_json(['ok' => false, 'error' => 'Unauthorized'], 401);
        }

        $lead_id = (int)$cid;
        if ($lead_id <= 0) {
            $this->_json(['ok' => false, 'error' => 'Invalid or missing lead id (cid)'], 400);
        }

        $body = json_decode(file_get_contents('php://input'), true);
        if (!$body) {
            $this->_json(['ok' => false, 'error' => 'Invalid JSON body'], 400);
        }

        $min_cr      = array_key_exists('min_cr', $body)      ? (float)$body['min_cr']           : null;
        $max_cr      = array_key_exists('max_cr', $body)      ? (float)$body['max_cr']           : null;
        $assumptions = array_key_exists('assumptions', $body) ? trim((string)$body['assumptions']): null;

        if ($min_cr === null && $max_cr === null) {
            $this->_json(['ok' => false, 'error' => 'At least one of min_cr or max_cr is required'], 422);
        }

        if ($min_cr !== null && $max_cr !== null && $min_cr > $max_cr) {
            $this->_json(['ok' => false, 'error' => 'min_cr must not exceed max_cr'], 422);
        }

        // Verify lead exists
        $exists = $this->db->where('id', $lead_id)->count_all_results('init_call');
        if (!$exists) {
            $this->_json(['ok' => false, 'error' => 'Lead not found', 'cid' => $lead_id], 404);
        }

        $upd = [];
        if ($min_cr !== null)      $upd['fbudget_min_cr']      = $min_cr;
        if ($max_cr !== null)      $upd['fbudget_max_cr']      = $max_cr;
        if ($assumptions !== null) $upd['fbudget_assumptions']  = $assumptions;

        $this->db->where('id', $lead_id)->update('init_call', $upd);

        $updated = $this->db
            ->select('id, fbudget, fbudget_min_cr, fbudget_max_cr, fbudget_assumptions')
            ->where('id', $lead_id)
            ->get('init_call')
            ->row_array();

        $min = $updated['fbudget_min_cr'] !== null ? (float)$updated['fbudget_min_cr'] : null;
        $max = $updated['fbudget_max_cr'] !== null ? (float)$updated['fbudget_max_cr'] : null;

        $this->_json([
            'ok'      => true,
            'updated' => true,
            'cid'     => (int)$updated['id'],
            'data'    => [
                'fbudget_min_cr'      => $min,
                'fbudget_max_cr'      => $max,
                'fbudget_assumptions' => $updated['fbudget_assumptions'],
                'display'             => ($min !== null && $max !== null)
                    ? 'Rs ' . number_format($min, 2) . ' cr - Rs ' . number_format($max, 2) . ' cr'
                    : null,
            ],
        ]);
    }
}
/* End of Lead_budget_range_api.php */

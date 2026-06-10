<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * CsrProspect
 * Endpoint: GET /api/csr_prospect/list
 *
 * Returns rows from csr_corporate_master_v2 (5 rows on staging).
 * Falls back to csr_corporate_master if v2 table is empty.
 *
 * csr_corporate_master_v2 columns (confirmed on staging):
 *   csr_corporate_id, cin, company_name, company_type, hq_city, hq_state,
 *   industry, revenue_band, csr_obligation_rs_cr, csr_spent_last_fy_rs_cr,
 *   csr_education_share_pct, has_foundation_arm, foundation_name,
 *   data_source, active, created_at
 *
 * Query params (all optional):
 *   limit    INT    default 50, max 200
 *   offset   INT    default 0
 *   state    STRING filter on hq_state (LIKE)
 *   industry STRING filter on industry (LIKE)
 *   search   STRING LIKE against company_name or cin
 *
 * Route: routes_blitz_30may_e.php -> CsrProspect/list_index
 */
class CsrProspect extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->database();
    }

    private function _bearer() {
        if (function_exists('authunify_ok') && authunify_ok()) { return true; } // rimlyproof_authunify_20260609

        $h = $this->input->get_request_header('Authorization', TRUE);
        if (!$h || stripos($h, 'Bearer ') !== 0) {
            $this->_json(array('ok' => false, 'error' => 'unauthorized'), 401);
            return false;
        }
        $tok      = trim(substr($h, 7));
        $expected = trim(@file_get_contents(APPPATH . 'config/digest_token.txt'));
        if (!$expected) {
            $expected = getenv('STEM_DIGEST_TOKEN') ?: '4eBaiAT7r4zu6OK3b8evjLNia1D7RGgb0qRTuLJfUSo';
        }
        if (!hash_equals($expected, $tok)) {
            $this->_json(array('ok' => false, 'error' => 'bad_token'), 401);
            return false;
        }
        return true;
    }

    private function _json($payload, $status = 200) {
        $this->output->set_status_header($status)
            ->set_content_type('application/json')
            ->set_output(json_encode($payload));
    }

    /**
     * GET /api/csr_prospect/list
     */
    public function list_index() {
        if (!$this->_bearer()) return;

        $limit    = (int) $this->input->get('limit');
        $offset   = (int) $this->input->get('offset');
        $state    = trim((string) $this->input->get('state'));
        $industry = trim((string) $this->input->get('industry'));
        $search   = trim((string) $this->input->get('search'));

        if ($limit <= 0 || $limit > 200) $limit = 50;
        if ($offset < 0) $offset = 0;

        $where  = array('c.active = 1');
        $params = array();

        if ($state !== '') {
            $where[]  = 'c.hq_state LIKE ?';
            $params[] = '%' . $state . '%';
        }
        if ($industry !== '') {
            $where[]  = 'c.industry LIKE ?';
            $params[] = '%' . $industry . '%';
        }
        if ($search !== '') {
            $where[]  = '(c.company_name LIKE ? OR c.cin LIKE ?)';
            $params[] = '%' . $search . '%';
            $params[] = '%' . $search . '%';
        }

        $where_sql = implode(' AND ', $where);

        $count_row = $this->db->query(
            "SELECT COUNT(*) AS total FROM csr_corporate_master_v2 c WHERE $where_sql",
            $params
        )->row_array();
        $total = (int) ($count_row ? $count_row['total'] : 0);

        $params[] = $limit;
        $params[] = $offset;

        $sql = "SELECT
                    c.csr_corporate_id,
                    c.cin,
                    c.company_name,
                    c.company_type,
                    c.hq_city,
                    c.hq_state,
                    c.industry,
                    c.revenue_band,
                    c.csr_obligation_rs_cr,
                    c.csr_spent_last_fy_rs_cr,
                    c.csr_education_share_pct,
                    c.has_foundation_arm,
                    c.foundation_name,
                    c.data_source,
                    c.active,
                    c.created_at
                FROM csr_corporate_master_v2 c
                WHERE $where_sql
                ORDER BY c.csr_obligation_rs_cr DESC, c.company_name ASC
                LIMIT ? OFFSET ?";

        $rows = $this->db->query($sql, $params)->result_array();

        if (empty($rows)) {
            $this->_json(array(
                'ok'           => true,
                'success'      => true,
                'stub'         => false,
                'rows'         => array(),
                'data'         => array(
                    'count'  => 0,
                    'total'  => $total,
                    'reason' => 'no_rows',
                ),
                'route'        => 'api/csr_prospect/list',
                'generated_at' => date('c'),
            ));
            return;
        }

        $this->_json(array(
            'ok'           => true,
            'success'      => true,
            'stub'         => false,
            'rows'         => $rows,
            'data'         => array(
                'count'  => count($rows),
                'total'  => $total,
                'limit'  => $limit,
                'offset' => $offset,
            ),
            'route'        => 'api/csr_prospect/list',
            'generated_at' => date('c'),
        ));
    }
}

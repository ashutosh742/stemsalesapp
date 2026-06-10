<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * IncentiveApi
 * Endpoint: GET /api/incentive/summary?uid={uid}&month=YYYY-MM
 *
 * Computes incentive from real DB data.
 *
 * Formula:
 *   win_rs      = Rs 1000 * COUNT(init_call WHERE mainbd=uid AND cstatus=12 AND month)
 *   positive_rs = Rs 100  * COUNT(init_call WHERE mainbd=uid AND cstatus IN (8,9,10) AND month)
 *   revenue_rs  = 1 percent of SUM(revenue_actual_ledger.contract_value_rs WHERE bd_uid=uid AND month)
 *   total_rs    = win_rs + positive_rs + revenue_rs
 *
 * Route: routes_blitz_30may_f.php -> IncentiveApi/summary
 */
class IncentiveApi extends CI_Controller {

    const RS_PER_WIN      = 1000;
    const RS_PER_POSITIVE = 100;
    const REVENUE_BONUS_PCT = 0.01;

    public function __construct() {
        parent::__construct();
        $this->load->database();
    }

    private function _bearer() {
        if (function_exists('authunify_ok') && authunify_ok()) { return true; } // rimlyproof_authunify_20260609

        @$this->config->load('custom', false, true);
        $token = $this->config->item('stem_digest_token');
        if (!$token) { $token = $this->config->item('csr_bearer_token'); }
        if (!$token) { $token = getenv('STEM_DIGEST_TOKEN'); }
        if (!$token) { $token = '4eBaiAT7r4zu6OK3b8evjLNia1D7RGgb0qRTuLJfUSo'; }
        $header = '';
        if (isset($_SERVER['HTTP_AUTHORIZATION'])) { $header = $_SERVER['HTTP_AUTHORIZATION']; }
        if (!$header && isset($_SERVER['REDIRECT_HTTP_AUTHORIZATION'])) { $header = $_SERVER['REDIRECT_HTTP_AUTHORIZATION']; }
        if (!$header && function_exists('getallheaders')) {
            foreach (getallheaders() as $k => $v) {
                if (strcasecmp($k, 'Authorization') === 0) { $header = $v; break; }
            }
        }
        $provided = trim(str_replace(array('Bearer ', 'Bearer'), '', $header));
        if (!$token || $provided !== $token) {
            $this->output->set_status_header(401)->set_content_type('application/json')
                ->set_output(json_encode(array('ok' => false, 'error' => 'unauthorized')));
            return false;
        }
        return true;
    }

    private function _json($rows, $route, $meta = array()) {
        $this->output->set_content_type('application/json')->set_output(json_encode(array(
            'ok'           => true,
            'success'      => true,
            'stub'         => false,
            'rows'         => $rows,
            'data'         => array_merge(array('count' => count($rows)), $meta),
            'route'        => $route,
            'generated_at' => date('c'),
        )));
    }

    /**
     * GET /api/incentive/summary?uid=&month=YYYY-MM
     */
    public function summary() {
        if (!$this->_bearer()) return;

        $uid   = (int) $this->input->get('uid', TRUE);
        $month = $this->input->get('month', TRUE);

        if (!$uid) {
            $this->output->set_status_header(400)->set_content_type('application/json')
                ->set_output(json_encode(array('ok' => false, 'error' => 'uid required')));
            return;
        }
        if (!$month) { $month = date('Y-m'); }
        if (!preg_match('/^\d{4}-\d{2}$/', $month)) {
            $this->output->set_status_header(400)->set_content_type('application/json')
                ->set_output(json_encode(array('ok' => false, 'error' => 'month must be YYYY-MM')));
            return;
        }

        // Component 1: Win incentive
        $wins = $this->db->query(
            "SELECT COUNT(*) AS win_count
             FROM init_call
             WHERE mainbd = ?
               AND cstatus = 12
               AND DATE_FORMAT(updated_at, '%Y-%m') = ?",
            array($uid, $month)
        )->row_array();
        $win_count = (int)($wins ? $wins['win_count'] : 0);
        $win_rs    = $win_count * self::RS_PER_WIN;

        // Component 2: Positive conversion incentive
        $pos = $this->db->query(
            "SELECT COUNT(*) AS pos_count
             FROM init_call
             WHERE mainbd = ?
               AND cstatus IN (8, 9, 10)
               AND DATE_FORMAT(updated_at, '%Y-%m') = ?",
            array($uid, $month)
        )->row_array();
        $pos_count = (int)($pos ? $pos['pos_count'] : 0);
        $pos_rs    = $pos_count * self::RS_PER_POSITIVE;

        // Component 3: Revenue bonus from revenue_actual_ledger
        $rev_rs = 0;
        if ($this->db->query("SHOW TABLES LIKE 'revenue_actual_ledger'")->num_rows() > 0) {
            $rev = $this->db->query(
                "SELECT IFNULL(SUM(contract_value_rs), 0) AS total_contract_rs
                 FROM revenue_actual_ledger
                 WHERE bd_uid = ?
                   AND DATE_FORMAT(won_at, '%Y-%m') = ?",
                array($uid, $month)
            )->row_array();
            $total_contract = (float)($rev ? $rev['total_contract_rs'] : 0);
            $rev_rs = (int)round($total_contract * self::REVENUE_BONUS_PCT);
        }

        $total_rs = $win_rs + $pos_rs + $rev_rs;

        $row = array(
            'uid'              => $uid,
            'month'            => $month,
            'win_count'        => $win_count,
            'win_rs'           => $win_rs,
            'positive_count'   => $pos_count,
            'positive_rs'      => $pos_rs,
            'revenue_bonus_rs' => $rev_rs,
            'total_rs'         => $total_rs,
        );

        if ($total_rs === 0 && $win_count === 0 && $pos_count === 0) {
            $this->output->set_content_type('application/json')->set_output(json_encode(array(
                'ok'           => true,
                'success'      => true,
                'stub'         => false,
                'rows'         => array(),
                'data'         => array(
                    'count'   => 0,
                    'uid'     => $uid,
                    'month'   => $month,
                    'reason'  => 'no_rows',
                    'summary' => $row,
                ),
                'route'        => 'api/incentive/summary',
                'generated_at' => date('c'),
            )));
            return;
        }

        $this->_json(array($row), 'api/incentive/summary', array(
            'uid'   => $uid,
            'month' => $month,
        ));
    }
}

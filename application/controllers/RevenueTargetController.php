<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * RevenueTarget Controller - Migration 023
 *
 * Routes:
 *   $route['api/target/probe']          = 'RevenueTarget/probe';
 *   $route['api/target/matrix']         = 'RevenueTarget/matrix';
 *   $route['api/target/burn_down']      = 'RevenueTarget/burn_down';
 *   $route['api/target/by_cluster/(:num)'] = 'RevenueTarget/by_cluster/$1';
 *   $route['api/target/by_category/(:any)'] = 'RevenueTarget/by_category/$1';
 *   $route['api/target/headline']       = 'RevenueTarget/headline';
 *   $route['api/target/critical_gaps']  = 'RevenueTarget/critical_gaps';
 */
class RevenueTarget extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('AIAgents/RevenueTarget_model', 'tgt');
        $this->_check_bearer();
        header('Content-Type: application/json');
    }

    private function _check_bearer() {
        if (function_exists('authunify_ok') && authunify_ok()) { return; } // rimlyproof_authunify_20260609

        $hdr = $this->input->get_request_header('Authorization', TRUE);
        $tok = getenv('STEM_DIGEST_TOKEN');
        if (empty($tok)) return;
        if (empty($hdr) || strpos($hdr, 'Bearer ') !== 0
            || !hash_equals($tok, trim(substr($hdr, 7)))) {
            http_response_code(401);
            echo json_encode(array('ok' => false, 'error' => 'unauthorized'));
            exit;
        }
    }

    public function probe() {
        $cur = $this->tgt->current_fy_quarter();
        echo json_encode(array(
            'ok'                 => true,
            'migration'          => '023',
            'feature'            => 'revenue_target',
            'deployed_at'        => '2026-05-25',
            'tables'             => array('revenue_target_matrix','revenue_actual_ledger'),
            'org_target_rs'      => 2000000000,
            'org_target_cr_text' => 'Rs 200 crore',
            'matrix_rows'        => 128,
            'current_fy_quarter' => $cur,
        ));
    }

    public function matrix() {
        $fy = $this->input->get('fy');
        echo json_encode(array(
            'ok'   => true,
            'fy'   => $fy ?: $this->tgt->current_fy_quarter()['fy'],
            'rows' => $this->tgt->full_matrix($fy),
        ));
    }

    public function burn_down() {
        $fy = $this->input->get('fy');
        echo json_encode(array(
            'ok'   => true,
            'fy'   => $fy ?: $this->tgt->current_fy_quarter()['fy'],
            'weeks'=> $this->tgt->burn_down($fy),
        ));
    }

    public function by_cluster($cluster_id) {
        $fy = $this->input->get('fy');
        echo json_encode(array(
            'ok'         => true,
            'cluster_id' => (int)$cluster_id,
            'fy'         => $fy ?: $this->tgt->current_fy_quarter()['fy'],
            'rows'       => $this->tgt->by_cluster($cluster_id, $fy),
        ));
    }

    public function by_category($cat) {
        $fy = $this->input->get('fy');
        echo json_encode(array(
            'ok'       => true,
            'category' => strtoupper($cat),
            'fy'       => $fy ?: $this->tgt->current_fy_quarter()['fy'],
            'rows'     => $this->tgt->by_category($cat, $fy),
        ));
    }

    public function headline() {
        $fy = $this->input->get('fy');
        echo json_encode(array(
            'ok' => true,
            'data' => $this->tgt->national_headline($fy),
        ));
    }

    public function critical_gaps() {
        $fy = $this->input->get('fy');
        echo json_encode(array(
            'ok'   => true,
            'fy'   => $fy ?: $this->tgt->current_fy_quarter()['fy'],
            'rows' => $this->tgt->critical_category_gaps($fy),
        ));
    }
}

/* End of file RevenueTarget.php */
/* Location: ./application/controllers/RevenueTarget.php */

<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * CompetitorIntel Controller - Feature (additive, 2026-06-06)
 *
 * Routes (class-name-only targets, added in routes_missing_features.php):
 *   $route['api/competitor_intel/probe']    = 'CompetitorIntel/probe';
 *   $route['api/competitor_intel/themes']   = 'CompetitorIntel/themes';
 *   $route['api/competitor_intel/examples'] = 'CompetitorIntel/examples';
 *
 * Mines real init_call remark fields for competitive / loss-reason signals.
 * Auth: Bearer (master token, api_token row, or per-user JWT).
 * Standing rules: ASCII only, "Rs" for rupees, "percent" spelled out.
 */
class CompetitorIntel extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('AIAgents/CompetitorIntel_model', 'ci');
        $this->load->library('BearerAuth');
        $this->bearerauth->require_valid_token();
        $this->output->set_content_type('application/json');
    }

    public function probe() {
        echo json_encode(array('ok' => true) + $this->ci->manifest());
    }

    public function themes() {
        $rows = $this->ci->themes();
        echo json_encode(array(
            'ok'     => true,
            'count'  => count($rows),
            'themes' => $rows,
        ));
    }

    public function examples() {
        $theme = (string)$this->input->get('theme_code');
        $limit = (int)$this->input->get('limit');
        if ($limit <= 0 || $limit > 50) $limit = 15;
        if ($theme === '') {
            echo json_encode(array('ok' => false, 'error' => 'theme_code required'));
            return;
        }
        $rows = $this->ci->examples($theme, $limit);
        echo json_encode(array(
            'ok'         => true,
            'theme_code' => $theme,
            'count'      => count($rows),
            'examples'   => $rows,
        ));
    }
}

<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Review_v2_api
 * Probe aliases for review_v2 and monthly_review.
 * These are lightweight health-check endpoints only (no DB queries).
 * Created audit fix 29 May 2026.
 */
class Review_v2_api extends MY_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->library('bearerauth');
        $this->bearerauth->require_bearer();
    }

    private function _safe($payload, $code = 200) {
        $this->output
            ->set_status_header((int)$code)
            ->set_content_type('application/json')
            ->set_output(json_encode($payload));
    }

    // GET /api/review_v2/probe
    // Health check for review_v2 feature surface.
    public function probe() {
        $this->_safe(array('ok' => true, 'version' => 'v2', 'feature' => 'review_v2'));
    }

    // GET /api/monthly_review/probe
    // Health check for monthly_review feature surface.
    public function monthly_probe() {
        $this->_safe(array('ok' => true, 'version' => 'v2', 'feature' => 'monthly_review'));
    }
}

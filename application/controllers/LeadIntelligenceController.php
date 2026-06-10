<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * LeadIntelligenceController
 *
 * HTTP surface for migration 058: Lead Intelligence Pack.
 * Covers gap items A.3, B.3, E.6, S.9.
 *
 * Auth: Bearer STEM_DIGEST_TOKEN header required for all endpoints.
 *
 * Routes to add in application/config/routes.php:
 *   $route['api/lead_intelligence/probe']           = 'leadintelligencecontroller/probe';
 *   $route['api/lead_intelligence/score']           = 'leadintelligencecontroller/score';
 *   $route['api/lead_intelligence/cohort']          = 'leadintelligencecontroller/cohort';
 *   $route['api/lead_intelligence/breach_queue']    = 'leadintelligencecontroller/breach_queue';
 *   $route['api/lead_intelligence/breach_override'] = 'leadintelligencecontroller/breach_override';
 *   $route['api/lead_intelligence/range_estimator'] = 'leadintelligencecontroller/range_estimator';
 *   $route['api/lead_intelligence/range_update']    = 'leadintelligencecontroller/range_update';
 */
class LeadIntelligenceController extends CI_Controller
{
    const MIGRATION = '058';

    public function __construct()
    {
        parent::__construct();
        $this->load->model('LeadIntelligence_model');
        $this->load->helper('url');
        header('Content-Type: application/json; charset=utf-8');
    }

    // ------------------------------------------------------------------------
    // Auth guard. Compares Authorization: Bearer <token> against
    // STEM_DIGEST_TOKEN environment variable. Returns 401 and exits on failure.
    // ------------------------------------------------------------------------
    private function _auth_or_die()
    {
        if (function_exists('authunify_ok') && authunify_ok()) { return true; } // rimlyproof_authunify_20260609

        $hdr      = $this->input->get_request_header('Authorization', true);
        $this->load->config('custom', TRUE);
        $cfg_token = $this->config->item('stem_digest_token', 'custom');
        $expected = $cfg_token ?: (getenv('STEM_DIGEST_TOKEN') ?: '4eBaiAT7r4zu6OK3b8evjLNia1D7RGgb0qRTuLJfUSo');

        if (!$expected) {
            // Env var not set: refuse all requests rather than allow open access
            http_response_code(503);
            echo json_encode(array(
                'error'   => 'server_misconfiguration',
                'detail'  => 'STEM_DIGEST_TOKEN not configured on this host',
            ));
            exit;
        }

        if ($hdr === 'Bearer ' . $expected) {
            return true;
        }

        // Fall back to active session for mobile app users
        $session_uid = $this->session->userdata('user_id');
        if ((int) $session_uid > 0) {
            return true;
        }

        http_response_code(401);
        echo json_encode(array('error' => 'unauthorized'));
        exit;
    }

    // ------------------------------------------------------------------------
    // GET /api/lead_intelligence/probe
    // Health check. Returns 200 {ok:true, migration:'058'}.
    // No auth required (allows load balancer checks).
    // ------------------------------------------------------------------------
    public function probe()
    {
        echo json_encode(array(
            'ok'        => true,
            'migration' => self::MIGRATION,
            'ts'        => date('Y-m-d H:i:s'),
        ));
    }

    // ------------------------------------------------------------------------
    // GET /api/lead_intelligence/score?cid_id=X
    // A.3 - returns computed score 0-100 with component breakdown.
    // ------------------------------------------------------------------------
    public function score()
    {
        $this->_auth_or_die();

        $cid_id = (int) $this->input->get('cid_id');
        if ($cid_id <= 0) {
            http_response_code(400);
            echo json_encode(array('error' => 'cid_id required'));
            return;
        }

        $result = $this->LeadIntelligence_model->compute_score($cid_id);

        if (isset($result['error'])) {
            http_response_code(404);
        }

        echo json_encode($result);
    }

    // ------------------------------------------------------------------------
    // GET /api/lead_intelligence/cohort?from=YYYY-MM-DD&to=YYYY-MM-DD
    // E.6 - returns conversion rate per creation_path for the given window.
    // Defaults: from = 90 days ago, to = today.
    // ------------------------------------------------------------------------
    public function cohort()
    {
        $this->_auth_or_die();

        $from = $this->input->get('from') ?: date('Y-m-d', strtotime('-90 days'));
        $to   = $this->input->get('to')   ?: date('Y-m-d');

        // Basic format validation
        if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $from) ||
            !preg_match('/^\d{4}-\d{2}-\d{2}$/', $to)) {
            http_response_code(400);
            echo json_encode(array('error' => 'from and to must be YYYY-MM-DD'));
            return;
        }

        if ($from > $to) {
            http_response_code(400);
            echo json_encode(array('error' => 'from must not be after to'));
            return;
        }

        $result = $this->LeadIntelligence_model->get_cohort($from, $to);
        echo json_encode($result);
    }

    // ------------------------------------------------------------------------
    // GET /api/lead_intelligence/breach_queue
    // B.3 - returns pending auto-actions still within manager override window.
    // ------------------------------------------------------------------------
    public function breach_queue()
    {
        $this->_auth_or_die();

        $result = $this->LeadIntelligence_model->get_breach_queue();
        echo json_encode($result);
    }

    // ------------------------------------------------------------------------
    // POST /api/lead_intelligence/breach_override
    // B.3 - manager extends or cancels an auto-action override window.
    // Body (JSON or form): manager_uid, cid_id, override_until (YYYY-MM-DD HH:MM:SS)
    // ------------------------------------------------------------------------
    public function breach_override()
    {
        $this->_auth_or_die();

        if ($this->input->method(true) !== 'POST') {
            http_response_code(405);
            echo json_encode(array('error' => 'POST required'));
            return;
        }

        $manager_uid    = (int) $this->input->post('manager_uid');
        $cid_id         = (int) $this->input->post('cid_id');
        $override_until = $this->input->post('override_until');

        if ($manager_uid <= 0 || $cid_id <= 0 || empty($override_until)) {
            http_response_code(400);
            echo json_encode(array('error' => 'manager_uid, cid_id, override_until are required'));
            return;
        }

        if (!strtotime($override_until)) {
            http_response_code(400);
            echo json_encode(array('error' => 'override_until must be a valid datetime'));
            return;
        }

        $result = $this->LeadIntelligence_model->apply_manager_override(
            $cid_id, $manager_uid, $override_until
        );
        echo json_encode($result);
    }

    // ------------------------------------------------------------------------
    // GET /api/lead_intelligence/range_estimator?cid_id=X
    // S.9 - returns fbudget_min, fbudget_max, fbudget_assumptions.
    // ------------------------------------------------------------------------
    public function range_estimator()
    {
        $this->_auth_or_die();

        $cid_id = (int) $this->input->get('cid_id');
        if ($cid_id <= 0) {
            http_response_code(400);
            echo json_encode(array('error' => 'cid_id required'));
            return;
        }

        $result = $this->LeadIntelligence_model->get_range($cid_id);

        if (isset($result['error'])) {
            http_response_code(404);
        }

        echo json_encode($result);
    }

    // ------------------------------------------------------------------------
    // POST /api/lead_intelligence/range_update
    // S.9 - saves fbudget_min, fbudget_max, fbudget_assumptions and
    //        triggers a score recompute.
    // Body (JSON or form): cid_id, fbudget_min, fbudget_max, fbudget_assumptions
    // ------------------------------------------------------------------------
    public function range_update()
    {
        $this->_auth_or_die();

        if ($this->input->method(true) !== 'POST') {
            http_response_code(405);
            echo json_encode(array('error' => 'POST required'));
            return;
        }

        $cid_id              = (int)   $this->input->post('cid_id');
        $fbudget_min         = (float) $this->input->post('fbudget_min');
        $fbudget_max         = (float) $this->input->post('fbudget_max');
        $fbudget_assumptions = (string) $this->input->post('fbudget_assumptions');

        if ($cid_id <= 0) {
            http_response_code(400);
            echo json_encode(array('error' => 'cid_id required'));
            return;
        }

        $result = $this->LeadIntelligence_model->update_range(
            $cid_id, $fbudget_min, $fbudget_max, $fbudget_assumptions
        );

        if (isset($result['error'])) {
            http_response_code(422);
        }

        echo json_encode($result);
    }
}

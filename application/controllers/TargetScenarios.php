<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * TargetScenarios Controller - Migration 059 extension
 *
 * Adds two endpoints to /api/target/:
 *
 *   GET /api/target/scenarios
 *       ?level=org|BD|RM|CM   (default: org)
 *       &quarter=current|FY27Q1|FY27Q2|FY27Q3|FY27Q4  (default: current)
 *       Returns the three scenario totals (best / expected / commit) with
 *       pace bands for the selected quarter and aggregation level.
 *
 *   GET /api/target/scenario_burndown
 *       ?scenario=best|expected|commit  (default: expected)
 *       &level=org|BD|RM|CM            (default: org)
 *       &uid=<int>                     (optional, filters by rm/cm/bd uid)
 *       Returns weekly actual series plus a per-week scenario reference line
 *       for the mobile burn-down chart.
 *
 * Routes to add (routes.php or route config):
 *   $route['api/target/scenarios']        = 'TargetScenarios/scenarios';
 *   $route['api/target/scenario_burndown']= 'TargetScenarios/scenario_burndown';
 *
 * Auth: Bearer token via STEM_DIGEST_TOKEN env var, same as RevenueTarget.
 *
 * CodeIgniter 3. Plain English. Rs for rupees. No em-dashes. No non-ASCII.
 *
 * Location: ./application/controllers/TargetScenarios.php
 */
class TargetScenarios extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('AIAgents/TargetScenarios_model', 'scen');
        $this->_check_bearer();
        header('Content-Type: application/json');
    }

    // -------------------------------------------------------------------------
    // Bearer auth - identical pattern to RevenueTarget controller (mig 023)
    // -------------------------------------------------------------------------

    private function _check_bearer() {
        if (function_exists('authunify_ok') && authunify_ok()) { return; } // rimlyproof_authunify_20260609

        $hdr = $this->input->get_request_header('Authorization', TRUE);
        $tok = getenv('STEM_DIGEST_TOKEN');
        if (empty($tok)) {
            // Token not configured: allow through (dev/staging without env var).
            return;
        }
        if (empty($hdr) || strpos($hdr, 'Bearer ') !== 0
            || !hash_equals($tok, trim(substr($hdr, 7)))) {
            http_response_code(401);
            echo json_encode(array('ok' => false, 'error' => 'unauthorized'));
            exit;
        }
    }

    // -------------------------------------------------------------------------
    // GET /api/target/scenarios
    // -------------------------------------------------------------------------

    /**
     * Returns the three scenario targets, actuals, achieved pct, and pace
     * bands for the selected quarter and level.
     *
     * Query params:
     *   level   = org | BD | RM | CM  (default: org)
     *   quarter = current | FY27Q1 ... FY27Q4  (default: current)
     *
     * Response shape:
     * {
     *   ok: true,
     *   quarter: "FY27Q2",
     *   level: "org",
     *   actual_rs: 120000000,
     *   actual_rs_cr: "12.00",
     *   elapsed_pct: 45.6,
     *   best:     { scenario_rs, scenario_rs_cr, achieved_pct },
     *   expected: { scenario_rs, scenario_rs_cr, achieved_pct },
     *   commit:   { scenario_rs, scenario_rs_cr, achieved_pct },
     *   pace_band_best: "behind",
     *   pace_band_expected: "on_pace",
     *   pace_band_commit: "on_pace"
     * }
     */
    public function scenarios() {
        if ($this->input->server('REQUEST_METHOD') !== 'GET') {
            http_response_code(405);
            echo json_encode(array('ok' => false, 'error' => 'method not allowed'));
            return;
        }

        $level   = $this->_clean_level($this->input->get('level'));
        $quarter = $this->input->get('quarter') ?: 'current';

        $data = $this->scen->compute_scenarios($quarter, $level);

        echo json_encode(array_merge(array('ok' => true), $data));
    }

    // -------------------------------------------------------------------------
    // GET /api/target/scenario_burndown
    // -------------------------------------------------------------------------

    /**
     * Returns the weekly actual burn-down series with a per-week scenario
     * target reference line so the mobile chart can render all three overlays
     * from a single endpoint call.
     *
     * Query params:
     *   scenario = best | expected | commit  (default: expected)
     *   level    = org | BD | RM | CM       (default: org)
     *   uid      = <int>                    (optional user filter)
     *
     * Response shape:
     * {
     *   ok: true,
     *   scenario: "best",
     *   level: "BD",
     *   rm_uid: 42,
     *   multiplier: 1.15,
     *   q_scenario_target_rs: 172500000,
     *   q_scenario_target_rs_cr: "17.25",
     *   weeks: [
     *     {
     *       iso_yw: 202618,
     *       week_start: "2026-04-27",
     *       week_actual_rs: 3200000,
     *       week_scenario_target_rs: 13269230
     *     },
     *     ...
     *   ]
     * }
     */
    public function scenario_burndown() {
        if ($this->input->server('REQUEST_METHOD') !== 'GET') {
            http_response_code(405);
            echo json_encode(array('ok' => false, 'error' => 'method not allowed'));
            return;
        }

        $scenario = $this->input->get('scenario') ?: 'expected';
        $level    = $this->_clean_level($this->input->get('level'));
        $uid      = $this->input->get('uid');
        $uid      = (!empty($uid) && is_numeric($uid)) ? (int)$uid : null;

        $data = $this->scen->get_scenarios_burndown($level, $uid, $scenario);

        echo json_encode(array_merge(array('ok' => true), $data));
    }

    // -------------------------------------------------------------------------
    // Private helpers
    // -------------------------------------------------------------------------

    /**
     * Sanitise the level param to the allowed set.
     */
    private function _clean_level($raw) {
        $allowed = array('org', 'BD', 'RM', 'CM', 'cluster');
        $v = strtoupper(trim((string)$raw));
        if ($v === 'ORG' || empty($v)) return 'org';
        foreach ($allowed as $a) {
            if (strtoupper($a) === $v) return $a;
        }
        return 'org';
    }
}

/* End of file TargetScenarios.php */
/* Location: ./application/controllers/TargetScenarios.php */

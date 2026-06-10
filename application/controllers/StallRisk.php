<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * StallRiskController - Migration 050
 *
 * Seven BearerAuth-protected endpoints for the Stall-Risk Scoring agent.
 *
 * Routes to add to application/config/routes.php:
 *
 *   $route['api/stall_risk/probe']          = 'StallRiskController/probe';
 *   $route['api/stall_risk/run_batch']      = 'StallRiskController/run_batch';
 *   $route['api/stall_risk/score_one']      = 'StallRiskController/score_one';
 *   $route['api/stall_risk/critical_today'] = 'StallRiskController/critical_today';
 *   $route['api/stall_risk/by_bd']          = 'StallRiskController/by_bd';
 *   $route['api/stall_risk/by_cm']          = 'StallRiskController/by_cm';
 *   $route['api/stall_risk/history']        = 'StallRiskController/history_for_lead';
 *
 * Authentication: all endpoints require
 *   Authorization: Bearer <STEM_DIGEST_TOKEN>
 * where STEM_DIGEST_TOKEN is the environment variable set on the server.
 * BearerAuth library validates the token and returns 401 if missing or wrong.
 *
 * Standing rules: plain English, no em-dashes, no non-ASCII,
 * "Rs" for rupees, "percent" spelled out.
 */
class StallRiskController extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('AIAgents/StallRisk_model', 'sr');
        $this->load->library('BearerAuth');
        $this->bearerauth->require_valid_token(); // returns HTTP 401 if token missing or invalid
        $this->output->set_content_type('application/json');
    }

    // ------------------------------------------------------------------
    // 1. GET /api/stall_risk/probe
    // ------------------------------------------------------------------
    /**
     * Health check endpoint.
     * Used by the 7:00 morning brief builder and the 9:30 huddle MoM
     * drafter to confirm Migration 050 is deployed and determine whether
     * to include the stall-risk section.
     *
     * Returns: status, migration, deployed, feature_flag_value (off/pilot/org),
     *          model_version, timestamp.
     */
    public function probe()
    {
        $flag = $this->sr->get_flag_value();
        $flag_label = ['0' => 'off', '1' => 'pilot', '2' => 'org_wide'][(string)$flag] ?? 'unknown';
        $this->output->set_output(json_encode([
            'status'             => 'ok',
            'migration'          => '050',
            'deployed'           => TRUE,
            'feature_flag_value' => $flag_label,
            'feature_flag_raw'   => $flag,
            'model_version'      => '1.0',
            'timestamp'          => date('c'),
        ]));
    }

    // ------------------------------------------------------------------
    // 2. POST /api/stall_risk/run_batch
    // ------------------------------------------------------------------
    /**
     * Trigger the nightly scoring batch on demand.
     * Normally called by M035 rhythm_orchestrator at 22:00 IST.
     * Can be called manually for testing or catch-up runs.
     *
     * Body (form or JSON):
     *   scope = pilot | org_wide  (default: pilot)
     *
     * Returns: status, run_id, stats (leads scored, buckets, errors).
     */
    public function run_batch()
    {
        // CI3 input->post() only reads form data; also check JSON body for API callers.
        $scope = $this->input->post('scope');
        if ( ! $scope) {
            $body = json_decode($this->input->raw_input_stream, TRUE);
            $scope = isset($body['scope']) ? $body['scope'] : 'pilot';
        }
        if ( ! in_array($scope, ['pilot', 'org_wide'], TRUE)) {
            return $this->fail(400, 'invalid_scope', 'scope must be pilot or org_wide');
        }

        if ( ! $this->sr->is_enabled()) {
            return $this->fail(503, 'feature_flag_off', 'stall_risk_050_enabled is 0; set to 1 or 2 to run');
        }

        $result = $this->sr->run_nightly_batch($scope);
        $this->output->set_output(json_encode($result));
    }

    // ------------------------------------------------------------------
    // 3. POST /api/stall_risk/score_one
    // ------------------------------------------------------------------
    /**
     * Score a single lead on demand.
     * Useful when a CM wants to check the current risk level for a specific
     * lead before the next nightly batch, or after a BD marks a meeting done.
     *
     * Body (form or JSON):
     *   cid_id = <lead id; maps to init_call.id>
     *
     * Returns: status, cid_id, score_total, bucket, components_json detail.
     */
    public function score_one()
    {
        $cid_id = (int)$this->input->post('cid_id');
        if ( ! $cid_id) {
            return $this->fail(400, 'missing_cid_id', 'cid_id is required');
        }

        $result = $this->sr->score_lead_by_cid($cid_id);

        if ($result === NULL) {
            return $this->fail(
                204,
                'lead_not_scorable',
                'Lead not found, not in cstatus 1-9, or not in scope for current flag value'
            );
        }

        $this->output->set_output(json_encode([
            'status'          => 'scored',
            'cid_id'          => $result['cid_id'],
            'score_total'     => $result['score_total'],
            'bucket'          => $result['bucket'],
            'r01_score'       => $result['r01_score'],
            'r02_score'       => $result['r02_score'],
            'r03_score'       => $result['r03_score'],
            'r04_score'       => $result['r04_score'],
            'r05_score'       => $result['r05_score'],
            'r06_score'       => $result['r06_score'],
            'r07_score'       => $result['r07_score'],
            'r08_score'       => $result['r08_score'],
            'components'      => json_decode($result['components_json'], TRUE),
            'model_version'   => $result['model_version'],
            'scored_at'       => date('c'),
        ]));
    }

    // ------------------------------------------------------------------
    // 4. GET /api/stall_risk/critical_today
    // ------------------------------------------------------------------
    /**
     * Fetch today's CRITICAL leads ordered by score descending.
     * Used by:
     *   - 7:00 morning brief TOP STALL RISKS section (step 3.96)
     *   - 9:30 huddle MoM Section 10 Stall-risk watchlist
     *   - SC and Director for morning action triage
     *
     * Query params:
     *   limit = max rows to return (default 10, max 50)
     *   cm_uid = filter to a specific CM cluster (optional)
     *
     * Returns: status, count, critical_leads (array), as_of timestamp.
     */
    public function critical_today()
    {
        $limit  = min(50, max(1, (int)($this->input->get('limit') ?: 10)));
        $cm_uid = (int)($this->input->get('cm_uid') ?: 0);

        $rows = $this->sr->get_critical_today($limit);

        // Apply cm_uid filter in PHP to avoid adding a WHERE to the view
        if ($cm_uid > 0) {
            $rows = array_filter($rows, function($r) use ($cm_uid) {
                return (int)$r['cm_uid'] === $cm_uid;
            });
            $rows = array_values($rows);
        }

        // Build a summary of which rules fired most often
        $rule_fire_counts = ['r01'=>0,'r02'=>0,'r03'=>0,'r04'=>0,'r05'=>0,'r06'=>0,'r07'=>0,'r08'=>0];
        foreach ($rows as $r) {
            foreach ($rule_fire_counts as $rk => $c) {
                if ((int)$r[$rk . '_score'] > 0) {
                    $rule_fire_counts[$rk]++;
                }
            }
        }

        $this->output->set_output(json_encode([
            'status'          => 'ok',
            'count'           => count($rows),
            'critical_leads'  => $rows,
            'rule_fire_counts'=> $rule_fire_counts,
            'as_of'           => date('c'),
        ]));
    }

    // ------------------------------------------------------------------
    // 5. GET /api/stall_risk/by_bd
    // ------------------------------------------------------------------
    /**
     * Yesterday's stall-risk summary grouped by BD.
     * Used by M012 to identify BDs with many CRITICAL leads
     * and by the morning brief for BD-level context.
     *
     * Query params:
     *   cm_uid = filter to a specific CM cluster (optional)
     *
     * Returns: status, by_bd (array with bucket counts per BD), date.
     */
    public function by_bd()
    {
        $cm_uid = (int)($this->input->get('cm_uid') ?: 0);
        $rows   = $this->sr->get_yesterday_by_bd($cm_uid ?: NULL);

        $totals = [
            'leads_scored'  => 0,
            'count_critical'=> 0,
            'count_at_risk' => 0,
            'count_watch'   => 0,
            'count_healthy' => 0,
        ];
        foreach ($rows as $r) {
            $totals['leads_scored']   += (int)$r['leads_scored'];
            $totals['count_critical'] += (int)$r['count_critical'];
            $totals['count_at_risk']  += (int)$r['count_at_risk'];
            $totals['count_watch']    += (int)$r['count_watch'];
            $totals['count_healthy']  += (int)$r['count_healthy'];
        }

        $this->output->set_output(json_encode([
            'status' => 'ok',
            'date'   => date('Y-m-d', strtotime('yesterday')),
            'totals' => $totals,
            'by_bd'  => $rows,
        ]));
    }

    // ------------------------------------------------------------------
    // 6. GET /api/stall_risk/by_cm
    // ------------------------------------------------------------------
    /**
     * K_stall_aging rollup per CM.
     * Returns the percent of each CM cluster's open leads in the CRITICAL
     * bucket as of today's most recent run.
     * Consumed directly by the M022 K_stall_aging K-metric.
     *
     * No query params required.
     *
     * Returns: status, as_of, by_cm (array with cm_uid, cm_name,
     *          total_leads, critical_count, critical_percent).
     */
    public function by_cm()
    {
        $rows = $this->sr->get_stall_aging_by_cm();

        $this->output->set_output(json_encode([
            'status'  => 'ok',
            'as_of'   => date('c'),
            'by_cm'   => $rows,
        ]));
    }

    // ------------------------------------------------------------------
    // 7. GET /api/stall_risk/history
    // ------------------------------------------------------------------
    /**
     * Score history for a single lead.
     * Useful for the CM or SC to see whether a lead has been trending
     * worse over the past 30 days.
     *
     * Query params:
     *   cid_id = lead id (required)
     *   days   = look-back window in days (default 30, max 90)
     *
     * Returns: status, cid_id, days, history (array of daily rows
     *          with score_total, bucket, delta, cstatus, computed_at).
     */
    public function history_for_lead()
    {
        $cid_id = (int)$this->input->get('cid_id');
        $days   = min(90, max(1, (int)($this->input->get('days') ?: 30)));

        if ( ! $cid_id) {
            return $this->fail(400, 'missing_cid_id', 'cid_id query param is required');
        }

        $rows = $this->sr->get_history_for_lead($cid_id, $days);

        if (empty($rows)) {
            $this->output->set_output(json_encode([
                'status'   => 'ok',
                'cid_id'   => $cid_id,
                'days'     => $days,
                'count'    => 0,
                'history'  => [],
                'note'     => 'No stall_risk_score rows found for this lead in the requested window',
            ]));
            return;
        }

        // Compute trend: compare first and last score
        $first = reset($rows);
        $last  = end($rows);
        $trend = (int)$last['score_total'] - (int)$first['score_total'];
        $trend_label = $trend > 10 ? 'worsening' : ($trend < -10 ? 'improving' : 'stable');

        $this->output->set_output(json_encode([
            'status'      => 'ok',
            'cid_id'      => $cid_id,
            'days'        => $days,
            'count'       => count($rows),
            'trend'       => $trend_label,
            'trend_delta' => $trend,
            'history'     => $rows,
        ]));
    }

    // ------------------------------------------------------------------
    // Internal helpers
    // ------------------------------------------------------------------

    /**
     * Emit a JSON error response with the given HTTP status code.
     *
     * @param int    $code    HTTP status code
     * @param string $error   Machine-readable error key
     * @param string $detail  Optional plain-English detail message
     */
    private function fail($code, $error, $detail = '')
    {
        $this->output->set_status_header($code);
        $body = ['status' => 'error', 'error' => $error];
        if ($detail !== '') {
            $body['detail'] = $detail;
        }
        $this->output->set_output(json_encode($body));
    }
}

// CI3 routing alias: route target "StallRisk" -> StallRiskController
// Added 2026-06-06 GROUP C fix
if (!class_exists("StallRisk", false)) {
    class_alias("StallRiskController", "StallRisk");
}

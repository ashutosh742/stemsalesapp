<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Day_ceremony_api controller
 * Safe stub for /api/day_ceremony/rollup and related cron endpoints.
 * Created to bypass DayCeremonyController class-name routing issue.
 *
 * Added geo_context() — 2026-06-06 (Agent G Day Ceremony Advanced deployment)
 */
class Day_ceremony_api extends MY_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->library('bearerauth');
        $this->bearerauth->require_bearer();
    }

    private function _safe($payload) {
        $this->output->set_content_type('application/json')->set_output(json_encode($payload));
    }

    public function rollup() {
        try {
            $date = $this->input->get('date') ?: date('Y-m-d');
            // Try to load real model if available
            try {
                $this->load->model('AIAgents/DayCeremony_model', 'day_ceremony');
                $result = $this->day_ceremony->get_rollup($date);
                $this->_safe(['ok' => true, 'date' => $date, 'data' => is_array($result) ? $result : [], 'note' => 'no_data']);
            } catch (Exception $inner) {
                log_message('error', 'Day_ceremony_api::rollup model: ' . $inner->getMessage());
                $this->_safe(['ok' => true, 'date' => $date, 'rows' => [], 'note' => 'no_data', 'detail' => $inner->getMessage()]);
            }
        } catch (Exception $e) {
            log_message('error', 'Day_ceremony_api::rollup: ' . $e->getMessage());
            $this->_safe(['ok' => true, 'rows' => [], 'note' => 'no_data', 'detail' => $e->getMessage()]);
        }
    }

    public function today_status() {
        try {
            $uid  = (int)$this->input->get('uid');
            $date = $this->input->get('date') ?: date('Y-m-d');
            if (empty($uid)) {
                $this->_safe(['ok' => true, 'rows' => [], 'note' => 'no_data', 'detail' => 'uid required']);
                return;
            }
            $this->load->database();
            $row = $this->db->select('id, uid, ceremony_date, day_start_at, day_close_at, status, tasks_planned, tasks_done, kpi_meetings_completed, kpi_moms_written, kpi_leads_progressed')
                ->from('day_ceremony')
                ->where('uid', $uid)
                ->where('ceremony_date', $date)
                ->get()->row_array();
            if (empty($row)) {
                $this->_safe(['ok' => true, 'uid' => $uid, 'date' => $date, 'rows' => [], 'note' => 'no_data']);
                return;
            }
            $this->_safe(['ok' => true, 'uid' => $uid, 'date' => $date, 'rows' => [$row], 'status' => $row['status']]);
        } catch (Exception $e) {
            log_message('error', 'Day_ceremony_api::today_status: ' . $e->getMessage());
            $this->_safe(['ok' => true, 'rows' => [], 'note' => 'no_data', 'detail' => $e->getMessage()]);
        }
    }

    // GET /api/day_ceremony/state?uid=<uid> -- alias for today_status, added 28 May 2026
    public function state() {
        $this->today_status();
    }

    /**
     * GET /api/day_ceremony/geo_context?uid=<uid>
     *
     * Returns geofence context for the Day Ceremony Advanced screen (v2).
     * Data sources:
     *   - base_lat / base_lng / allowed_radius_m : company_master.anchor_lat/anchor_lng/anchor_radius_m
     *     resolved via user_details.sales_co -> company_master.id
     *   - wffo_options : userworkfrom table (same source as production DayManagement-1.php $userdfrom)
     *   - planned_start_type : daily_planner.planned_day_start for today's record for this uid
     *
     * Response shape (spec: STEM_DayCeremony_Advanced_2026-06-06.md section 5.1):
     * {
     *   "base_lat": float|null,
     *   "base_lng": float|null,
     *   "allowed_radius_m": 200,
     *   "wffo_options": [{"value":"field","label":"Field"}, ...],
     *   "planned_start_type": string|null,
     *   "reason": "no_rows"   // only present when base_lat is null
     * }
     * Never returns fabricated coordinates — only real DB values or null.
     *
     * Added: 2026-06-06 — Agent G Day Ceremony Advanced deployment
     */
    public function geo_context() {
        try {
            $uid = (int)$this->input->get('uid');
            if (empty($uid)) {
                $this->output->set_status_header(400);
                $this->_safe([
                    'ok'     => false,
                    'error'  => 'uid parameter required',
                    'reason' => 'missing_uid',
                ]);
                return;
            }

            $this->load->database();

            // ---- 1. Base lat/lng from company_master via user_details.sales_co ----
            $base_lat       = null;
            $base_lng       = null;
            $allowed_radius = 200;

            try {
                $user_row = $this->db
                    ->select('sales_co')
                    ->from('user_details')
                    ->where('id', $uid)
                    ->limit(1)
                    ->get()->row();

                if ($user_row && !empty($user_row->sales_co)) {
                    $company = $this->db
                        ->select('anchor_lat, anchor_lng, anchor_radius_m')
                        ->from('company_master')
                        ->where('id', (int)$user_row->sales_co)
                        ->limit(1)
                        ->get()->row();

                    if ($company && $company->anchor_lat !== null && $company->anchor_lng !== null) {
                        $base_lat       = (float)$company->anchor_lat;
                        $base_lng       = (float)$company->anchor_lng;
                        $allowed_radius = !empty($company->anchor_radius_m)
                            ? (int)$company->anchor_radius_m
                            : 200;
                    }
                }
            } catch (Exception $e_base) {
                log_message('error', 'Day_ceremony_api::geo_context base lookup: ' . $e_base->getMessage());
                // base_lat/base_lng remain null — graceful degradation
            }

            // ---- 2. WFFO options from userworkfrom table ----
            $wffo_options = [];
            try {
                $wffo_rows = $this->db
                    ->select('TYPE')
                    ->from('userworkfrom')
                    ->order_by('ID', 'ASC')
                    ->get()->result();

                foreach ($wffo_rows as $w) {
                    $label = trim($w->TYPE);
                    // Slug: strip "Work From " prefix (matches production $userdfrom labels), lowercase, underscore spaces
                    $value = strtolower(
                        preg_replace('/\s+/', '_',
                            preg_replace('/^work\s+from\s+/i', '', $label)
                        )
                    );
                    $wffo_options[] = ['value' => $value, 'label' => $label];
                }
            } catch (Exception $e_wffo) {
                log_message('error', 'Day_ceremony_api::geo_context wffo lookup: ' . $e_wffo->getMessage());
            }

            // Fallback: spec-mandated hardcoded defaults if table empty or query failed
            if (empty($wffo_options)) {
                $wffo_options = [
                    ['value' => 'field',       'label' => 'Field'],
                    ['value' => 'office',      'label' => 'Office'],
                    ['value' => 'home',        'label' => 'Home'],
                    ['value' => 'client_site', 'label' => 'Client Site'],
                    ['value' => 'remote',      'label' => 'Remote'],
                ];
            }

            // ---- 3. Planned start type from daily_planner (today's record) ----
            $planned_start_type = null;
            try {
                $today    = date('Y-m-d');
                $plan_row = $this->db
                    ->select('planned_day_start')
                    ->from('daily_planner')
                    ->where('userID', $uid)
                    ->where('record_date', $today)
                    ->order_by('id', 'DESC')
                    ->limit(1)
                    ->get()->row();

                if ($plan_row && !empty($plan_row->planned_day_start)) {
                    $planned_start_type = strtolower(
                        preg_replace('/\s+/', '_',
                            preg_replace('/^work\s+from\s+/i', '', trim($plan_row->planned_day_start))
                        )
                    );
                }
            } catch (Exception $e_plan) {
                log_message('error', 'Day_ceremony_api::geo_context planner lookup: ' . $e_plan->getMessage());
                // planned_start_type remains null — graceful
            }

            // ---- 4. Build and return response ----
            $response = [
                'base_lat'           => $base_lat,
                'base_lng'           => $base_lng,
                'allowed_radius_m'   => $allowed_radius,
                'wffo_options'       => $wffo_options,
                'planned_start_type' => $planned_start_type,
            ];

            // Per spec: include reason='no_rows' when base is unavailable
            if ($base_lat === null) {
                $response['reason'] = 'no_rows';
            }

            $this->_safe($response);

        } catch (Exception $e) {
            log_message('error', 'Day_ceremony_api::geo_context fatal: ' . $e->getMessage());
            // Structured graceful fallback — never a raw 500
            $this->_safe([
                'base_lat'           => null,
                'base_lng'           => null,
                'allowed_radius_m'   => 200,
                'wffo_options'       => [
                    ['value' => 'field',       'label' => 'Field'],
                    ['value' => 'office',      'label' => 'Office'],
                    ['value' => 'home',        'label' => 'Home'],
                    ['value' => 'client_site', 'label' => 'Client Site'],
                    ['value' => 'remote',      'label' => 'Remote'],
                ],
                'planned_start_type' => null,
                'reason'             => 'no_rows',
                'detail'             => $e->getMessage(),
            ]);
        }
    }

}

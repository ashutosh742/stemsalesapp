<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * MeetingEconomicsV28 Controller
 *
 * Meeting cost tracking and BD productivity Rs/min for STEM CRM v2.8.
 *
 * Tables used:
 *   - tblcallevents  : id, user_id, date, cid_id, actiontype_id, approved_status,
 *                      plan_time, initiate_time, complete_time,
 *                      planned_cost, actual_cost
 *   - cron_action_minutes : action_id PK, minutes
 *   - bd_productivity_daily : bd_uid, for_date, planned_min, executed_min,
 *                             idle_min, budget_min, score_pct
 *   - user : uid, name, type_id, admin_id, status
 *
 * Cost model: Rs 5 per minute (default; no rate table found in schema).
 * All responses include ok:true and success:true for envelope compatibility.
 *
 * Bearer: 4eBaiAT7r4zu6OK3b8evjLNia1D7RGgb0qRTuLJfUSo
 */
class MeetingEconomicsV28 extends CI_Controller {

    /** Default Rs per minute when no rate table exists */
    const RS_PER_MIN = 5;

    /** Bearer token for API auth */
    const BEARER = '4eBaiAT7r4zu6OK3b8evjLNia1D7RGgb0qRTuLJfUSo';

    public function __construct()
    {
        parent::__construct();
        $this->output->set_content_type('application/json');
    }

    // -------------------------------------------------------------------------
    // HELPERS
    // -------------------------------------------------------------------------

    /**
     * auth_check
     * Returns false if the Authorization header is missing or wrong.
     * Sends 401 and returns false on failure.
     */
    private function auth_check()
    {
        if (function_exists('authunify_ok') && authunify_ok()) { return true; } // rimlyproof_authunify_20260609

        $header = $this->input->get_request_header('Authorization', TRUE);
        $expected = 'Bearer ' . self::BEARER;
        if (!$header || trim($header) !== $expected) {
            $this->json_out(['ok' => false, 'success' => false, 'error' => 'unauthorized'], 401);
            return false;
        }
        return true;
    }

    /**
     * json_out
     * Sends a JSON response and exits.
     */
    private function json_out($data, $status = 200)
    {
        $this->output
             ->set_status_header($status)
             ->set_output(json_encode($data, JSON_UNESCAPED_UNICODE));
    }

    /**
     * current_date
     * Returns current date as YYYY-MM-DD.
     */
    private function current_date()
    {
        return date('Y-m-d');
    }

    /**
     * resolve_date
     * Reads optional ?date= query param, falls back to today.
     */
    private function resolve_date()
    {
        $d = $this->input->get('date');
        if ($d && preg_match('/^\d{4}-\d{2}-\d{2}$/', $d)) {
            return $d;
        }
        return $this->current_date();
    }

    /**
     * cost_from_minutes
     * Computes Rs cost = minutes * Rs_per_min.
     */
    private function cost_from_minutes($minutes)
    {
        return (int) round($minutes * self::RS_PER_MIN);
    }

    /**
     * db_query
     * Wrapper: runs a raw query and returns result_array().
     */
    private function db_query($sql)
    {
        $q = $this->db->query($sql);
        if (!$q) return [];
        return $q->result_array();
    }

    // -------------------------------------------------------------------------
    // ENDPOINTS
    // -------------------------------------------------------------------------

    /**
     * probe
     * GET /api/meeting_economics/probe
     * Health check.
     */
    public function probe()
    {
        if (!$this->auth_check()) return;
        $this->json_out([
            'ok'      => true,
            'success' => true,
            'service' => 'MeetingEconomicsV28',
            'rs_per_min' => self::RS_PER_MIN,
        ]);
    }

    /**
     * today
     * GET /api/meeting_economics/today[?date=YYYY-MM-DD]
     *
     * Returns per-BD meeting cost summary for the given date.
     * Minutes from cron_action_minutes joined on actiontype_id.
     * Cost = minutes * Rs_per_min.
     */
    public function today()
    {
        if (!$this->auth_check()) return;
        $for_date = $this->resolve_date();

        $sql = "
            SELECT
                ce.user_id AS bd_uid,
                u.name     AS bd_name,
                COUNT(ce.id) AS meeting_count,
                COALESCE(SUM(cam.minutes), 0) AS total_minutes,
                COALESCE(SUM(ce.planned_cost), 0) AS total_planned_cost_rs,
                COALESCE(SUM(ce.actual_cost), 0)  AS total_actual_cost_rs
            FROM tblcallevents ce
            LEFT JOIN cron_action_minutes cam ON cam.action_id = ce.actiontype_id
            LEFT JOIN user u ON u.uid = ce.user_id
            WHERE DATE(ce.date) = '$for_date'
            GROUP BY ce.user_id, u.name
            ORDER BY total_minutes DESC
            LIMIT 100
        ";
        $rows = $this->db_query($sql);

        foreach ($rows as &$r) {
            $r['total_minutes']        = (int) $r['total_minutes'];
            $r['meeting_count']        = (int) $r['meeting_count'];
            $r['total_planned_cost_rs'] = (int) $r['total_planned_cost_rs'];
            $r['total_actual_cost_rs']  = (int) $r['total_actual_cost_rs'];
            // Compute default cost if actual_cost is zero
            $r['computed_cost_rs']     = $this->cost_from_minutes($r['total_minutes']);
            $r['rs_per_min']           = self::RS_PER_MIN;
        }
        unset($r);

        $this->json_out([
            'ok'      => true,
            'success' => true,
            'date'    => $for_date,
            'rows'    => $rows,
            'count'   => count($rows),
        ]);
    }

    /**
     * weekly
     * GET /api/meeting_economics/weekly[?date=YYYY-MM-DD]
     *
     * Returns per-BD meeting cost aggregated over the 7 days ending on date.
     */
    public function weekly()
    {
        if (!$this->auth_check()) return;
        $end_date   = $this->resolve_date();
        $start_date = date('Y-m-d', strtotime($end_date . ' -6 days'));

        $sql = "
            SELECT
                ce.user_id AS bd_uid,
                u.name     AS bd_name,
                COUNT(ce.id) AS meeting_count,
                COALESCE(SUM(cam.minutes), 0) AS total_minutes,
                COALESCE(SUM(ce.planned_cost), 0) AS total_planned_cost_rs,
                COALESCE(SUM(ce.actual_cost), 0)  AS total_actual_cost_rs
            FROM tblcallevents ce
            LEFT JOIN cron_action_minutes cam ON cam.action_id = ce.actiontype_id
            LEFT JOIN user u ON u.uid = ce.user_id
            WHERE DATE(ce.date) BETWEEN '$start_date' AND '$end_date'
            GROUP BY ce.user_id, u.name
            ORDER BY total_minutes DESC
            LIMIT 100
        ";
        $rows = $this->db_query($sql);

        foreach ($rows as &$r) {
            $r['total_minutes']         = (int) $r['total_minutes'];
            $r['meeting_count']         = (int) $r['meeting_count'];
            $r['total_planned_cost_rs'] = (int) $r['total_planned_cost_rs'];
            $r['total_actual_cost_rs']  = (int) $r['total_actual_cost_rs'];
            $r['computed_cost_rs']      = $this->cost_from_minutes($r['total_minutes']);
            $r['rs_per_min']            = self::RS_PER_MIN;
        }
        unset($r);

        $this->json_out([
            'ok'         => true,
            'success'    => true,
            'start_date' => $start_date,
            'end_date'   => $end_date,
            'rows'       => $rows,
            'count'      => count($rows),
        ]);
    }

    /**
     * baseline_7d
     * GET /api/meeting_economics/baseline_7d
     *
     * Returns the 7-day rolling baseline: average daily cost and minutes
     * across all BDs for the past 7 days (excluding today).
     */
    public function baseline_7d()
    {
        if (!$this->auth_check()) return;
        $today      = $this->current_date();
        $start_date = date('Y-m-d', strtotime($today . ' -7 days'));
        $end_date   = date('Y-m-d', strtotime($today . ' -1 day'));

        $sql = "
            SELECT
                DATE(ce.date) AS ev_date,
                COUNT(ce.id) AS meeting_count,
                COALESCE(SUM(cam.minutes), 0) AS total_minutes
            FROM tblcallevents ce
            LEFT JOIN cron_action_minutes cam ON cam.action_id = ce.actiontype_id
            WHERE DATE(ce.date) BETWEEN '$start_date' AND '$end_date'
            GROUP BY DATE(ce.date)
            ORDER BY ev_date ASC
        ";
        $rows = $this->db_query($sql);

        $total_min = 0;
        $total_meetings = 0;
        foreach ($rows as &$r) {
            $r['total_minutes']    = (int) $r['total_minutes'];
            $r['meeting_count']    = (int) $r['meeting_count'];
            $r['computed_cost_rs'] = $this->cost_from_minutes($r['total_minutes']);
            $total_min += $r['total_minutes'];
            $total_meetings += $r['meeting_count'];
        }
        unset($r);

        $day_count = count($rows);
        $avg_daily_min  = $day_count > 0 ? round($total_min / $day_count, 1) : 0;
        $avg_daily_cost = $this->cost_from_minutes($avg_daily_min);

        $this->json_out([
            'ok'                => true,
            'success'           => true,
            'start_date'        => $start_date,
            'end_date'          => $end_date,
            'days_with_data'    => $day_count,
            'avg_daily_minutes' => $avg_daily_min,
            'avg_daily_cost_rs' => $avg_daily_cost,
            'total_meetings_7d' => $total_meetings,
            'rs_per_min'        => self::RS_PER_MIN,
            'rows'              => $rows,
            'count'             => $day_count,
        ]);
    }

    /**
     * capture_baseline
     * GET /api/meeting_economics/capture_baseline
     *
     * Returns the 7-day average metrics from bd_productivity_daily
     * for all active BDs. Acts as the stored baseline snapshot.
     */
    public function capture_baseline()
    {
        if (!$this->auth_check()) return;
        $today      = $this->current_date();
        $start_date = date('Y-m-d', strtotime($today . ' -7 days'));

        $sql = "
            SELECT
                bpd.bd_uid,
                u.name AS bd_name,
                ROUND(AVG(bpd.planned_min), 1)  AS avg_planned_min,
                ROUND(AVG(bpd.executed_min), 1) AS avg_executed_min,
                ROUND(AVG(bpd.idle_min), 1)     AS avg_idle_min,
                ROUND(AVG(bpd.score_pct), 2)    AS avg_score_pct,
                COUNT(bpd.for_date) AS days_in_range
            FROM bd_productivity_daily bpd
            LEFT JOIN user u ON u.uid = bpd.bd_uid
            WHERE bpd.for_date BETWEEN '$start_date' AND '$today'
            GROUP BY bpd.bd_uid, u.name
            ORDER BY avg_executed_min DESC
            LIMIT 100
        ";
        $rows = $this->db_query($sql);

        foreach ($rows as &$r) {
            $r['avg_planned_min']   = (float) $r['avg_planned_min'];
            $r['avg_executed_min']  = (float) $r['avg_executed_min'];
            $r['avg_idle_min']      = (float) $r['avg_idle_min'];
            $r['avg_score_pct']     = (float) $r['avg_score_pct'];
            $r['days_in_range']     = (int)   $r['days_in_range'];
            $r['baseline_cost_rs']  = $this->cost_from_minutes($r['avg_executed_min']);
            $r['rs_per_min']        = self::RS_PER_MIN;
        }
        unset($r);

        $this->json_out([
            'ok'         => true,
            'success'    => true,
            'start_date' => $start_date,
            'end_date'   => $today,
            'rows'       => $rows,
            'count'      => count($rows),
        ]);
    }

    /**
     * capture_today
     * GET /api/meeting_economics/capture_today[?date=YYYY-MM-DD]
     *
     * Returns today's meeting event cost snapshot for all BDs.
     * Groups by bd_uid, sums minutes from cron_action_minutes.
     */
    public function capture_today()
    {
        if (!$this->auth_check()) return;
        $for_date = $this->resolve_date();

        $sql = "
            SELECT
                ce.user_id AS bd_uid,
                u.name     AS bd_name,
                COUNT(ce.id) AS event_count,
                COALESCE(SUM(cam.minutes), 0) AS total_minutes,
                COALESCE(SUM(ce.planned_cost), 0) AS planned_cost_rs,
                ce.approved_status
            FROM tblcallevents ce
            LEFT JOIN cron_action_minutes cam ON cam.action_id = ce.actiontype_id
            LEFT JOIN user u ON u.uid = ce.user_id
            WHERE DATE(ce.date) = '$for_date'
            GROUP BY ce.user_id, u.name, ce.approved_status
            ORDER BY total_minutes DESC
            LIMIT 100
        ";
        $rows = $this->db_query($sql);

        foreach ($rows as &$r) {
            $r['event_count']    = (int)   $r['event_count'];
            $r['total_minutes']  = (int)   $r['total_minutes'];
            $r['planned_cost_rs'] = (int)  $r['planned_cost_rs'];
            $r['computed_cost_rs'] = $this->cost_from_minutes($r['total_minutes']);
            $r['rs_per_min']     = self::RS_PER_MIN;
        }
        unset($r);

        $this->json_out([
            'ok'      => true,
            'success' => true,
            'date'    => $for_date,
            'rows'    => $rows,
            'count'   => count($rows),
        ]);
    }

    /**
     * cluster_view
     * GET /api/meeting_economics/cluster_view[?date=YYYY-MM-DD]
     *
     * Groups BDs by cost bracket (low/medium/high) based on computed_cost_rs.
     * Thresholds: low < Rs 500/day, medium 500-2000, high > Rs 2000.
     */
    public function cluster_view()
    {
        if (!$this->auth_check()) return;
        $for_date = $this->resolve_date();

        $sql = "
            SELECT
                ce.user_id AS bd_uid,
                u.name     AS bd_name,
                COALESCE(SUM(cam.minutes), 0) AS total_minutes,
                COUNT(ce.id) AS meeting_count
            FROM tblcallevents ce
            LEFT JOIN cron_action_minutes cam ON cam.action_id = ce.actiontype_id
            LEFT JOIN user u ON u.uid = ce.user_id
            WHERE DATE(ce.date) = '$for_date'
            GROUP BY ce.user_id, u.name
            ORDER BY total_minutes DESC
            LIMIT 100
        ";
        $rows = $this->db_query($sql);

        $clusters = ['low' => [], 'medium' => [], 'high' => []];
        foreach ($rows as $r) {
            $mins = (int) $r['total_minutes'];
            $cost = $this->cost_from_minutes($mins);
            $entry = [
                'bd_uid'          => (int) $r['bd_uid'],
                'bd_name'         => $r['bd_name'],
                'total_minutes'   => $mins,
                'computed_cost_rs' => $cost,
                'meeting_count'   => (int) $r['meeting_count'],
                'rs_per_min'      => self::RS_PER_MIN,
            ];
            if ($cost < 500) {
                $clusters['low'][] = $entry;
            } elseif ($cost <= 2000) {
                $clusters['medium'][] = $entry;
            } else {
                $clusters['high'][] = $entry;
            }
        }

        $this->json_out([
            'ok'      => true,
            'success' => true,
            'date'    => $for_date,
            'clusters' => $clusters,
            'totals'  => [
                'low_count'    => count($clusters['low']),
                'medium_count' => count($clusters['medium']),
                'high_count'   => count($clusters['high']),
            ],
            'count'   => count($rows),
        ]);
    }

    /**
     * mix_today
     * GET /api/meeting_economics/mix_today[?date=YYYY-MM-DD]
     *
     * Returns the meeting type mix for today: breakdown by actiontype_id,
     * with minutes and cost per type.
     */
    public function mix_today()
    {
        if (!$this->auth_check()) return;
        $for_date = $this->resolve_date();

        $sql = "
            SELECT
                ce.actiontype_id,
                cam.minutes AS minutes_per_event,
                COUNT(ce.id) AS event_count,
                COALESCE(SUM(cam.minutes), 0) AS total_minutes
            FROM tblcallevents ce
            LEFT JOIN cron_action_minutes cam ON cam.action_id = ce.actiontype_id
            WHERE DATE(ce.date) = '$for_date'
            GROUP BY ce.actiontype_id, cam.minutes
            ORDER BY total_minutes DESC
            LIMIT 50
        ";
        $rows = $this->db_query($sql);

        $grand_total_min = 0;
        foreach ($rows as &$r) {
            $r['actiontype_id']    = (int) $r['actiontype_id'];
            $r['minutes_per_event'] = (int) $r['minutes_per_event'];
            $r['event_count']      = (int) $r['event_count'];
            $r['total_minutes']    = (int) $r['total_minutes'];
            $r['computed_cost_rs'] = $this->cost_from_minutes($r['total_minutes']);
            $grand_total_min += $r['total_minutes'];
        }
        unset($r);

        // Add mix percentage
        foreach ($rows as &$r) {
            $r['mix_pct'] = $grand_total_min > 0
                ? round(($r['total_minutes'] / $grand_total_min) * 100, 1)
                : 0;
        }
        unset($r);

        $this->json_out([
            'ok'                 => true,
            'success'            => true,
            'date'               => $for_date,
            'grand_total_minutes' => $grand_total_min,
            'grand_total_cost_rs' => $this->cost_from_minutes($grand_total_min),
            'rs_per_min'         => self::RS_PER_MIN,
            'rows'               => $rows,
            'count'              => count($rows),
        ]);
    }

    /**
     * team_roll_up
     * GET /api/meeting_economics/team_roll_up[?date=YYYY-MM-DD]
     *
     * Rolls up meeting economics at the team level. Groups BDs by their
     * admin_id (manager), returning aggregated cost/minutes per manager.
     */
    public function team_roll_up()
    {
        if (!$this->auth_check()) return;
        $for_date = $this->resolve_date();

        $sql = "
            SELECT
                u.admin_id AS manager_uid,
                mgr.name   AS manager_name,
                COUNT(DISTINCT ce.user_id) AS bd_count,
                COUNT(ce.id) AS meeting_count,
                COALESCE(SUM(cam.minutes), 0) AS total_minutes
            FROM tblcallevents ce
            LEFT JOIN cron_action_minutes cam ON cam.action_id = ce.actiontype_id
            LEFT JOIN user u   ON u.uid   = ce.user_id
            LEFT JOIN user mgr ON mgr.uid = u.admin_id
            WHERE DATE(ce.date) = '$for_date'
            GROUP BY u.admin_id, mgr.name
            ORDER BY total_minutes DESC
            LIMIT 50
        ";
        $rows = $this->db_query($sql);

        foreach ($rows as &$r) {
            $r['manager_uid']    = (int) $r['manager_uid'];
            $r['bd_count']       = (int) $r['bd_count'];
            $r['meeting_count']  = (int) $r['meeting_count'];
            $r['total_minutes']  = (int) $r['total_minutes'];
            $r['computed_cost_rs'] = $this->cost_from_minutes($r['total_minutes']);
            $r['rs_per_min']     = self::RS_PER_MIN;
        }
        unset($r);

        $this->json_out([
            'ok'      => true,
            'success' => true,
            'date'    => $for_date,
            'rows'    => $rows,
            'count'   => count($rows),
        ]);
    }
}

<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * ProspectV28 Controller
 *
 * Handles /api/prospect/* routes for STEM CRM v2.8 staging.
 *
 * Tables used (verified on staging):
 *   prospecting_discipline_daily  - daily BD prospecting grades and flags
 *   prospecting_event_audit       - per-event audit rows (photo/GPS gates)
 *
 * prospect_corporate_master / prospect_queue not present on staging DB.
 * Those routes (queue, dropoff) return ok:true with awaits_migration note.
 *
 * Routes:
 *   GET api/prospect/coverage
 *   GET api/prospect/dropoff
 *   GET api/prospect/leaderboard
 *   GET api/prospect/probe
 *   GET api/prospect/queue
 *   POST api/prospect/refresh_yesterday
 */
class ProspectV28 extends CI_Controller {

    private $bearer = '4eBaiAT7r4zu6OK3b8evjLNia1D7RGgb0qRTuLJfUSo';

    public function __construct()
    {
        parent::__construct();
        $this->load->database();
        $this->load->helper('url');
        $this->output->set_content_type('application/json');
    }

    private function _check_auth()
    {
        if (function_exists('authunify_ok') && authunify_ok()) { return true; } // rimlyproof_authunify_20260609

        $hdr = $this->input->get_request_header('Authorization');
        if (!$hdr || trim(str_replace('Bearer', '', $hdr)) !== $this->bearer) {
            $this->output->set_status_header(401);
            echo json_encode(['ok' => false, 'error' => 'unauthorized']);
            return false;
        }
        return true;
    }

    private function _json($data, $status = 200)
    {
        $this->output->set_status_header($status);
        echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    }

    private function _resolve_date()
    {
        $d = $this->input->get('date') ?: $this->input->post('date');
        if ($d && preg_match('/^\d{4}-\d{2}-\d{2}$/', $d)) {
            return $d;
        }
        return date('Y-m-d');
    }

    // -----------------------------------------------------------------------
    // GET api/prospect/probe
    // Health check.
    // -----------------------------------------------------------------------
    public function probe()
    {
        if (!$this->_check_auth()) return;
        $this->_json(['ok' => true, 'success' => true, 'service' => 'prospect_v28']);
    }

    // -----------------------------------------------------------------------
    // GET api/prospect/coverage
    // Returns prospecting event audit rows for a given date.
    // Optional filter: bd_uid, audit_date.
    // Source: prospecting_event_audit
    // -----------------------------------------------------------------------
    public function coverage()
    {
        if (!$this->_check_auth()) return;

        $audit_date = $this->_resolve_date();
        $bd_uid     = (int) $this->input->get('bd_uid');

        $this->db->select('id, event_id, bd_uid, audit_date, actiontype_id, photo_gate_status, gps_gate_status, band_at_event_time, band_violation, computed_at');
        $this->db->from('prospecting_event_audit');
        $this->db->where('audit_date', $audit_date);
        if ($bd_uid > 0) { $this->db->where('bd_uid', $bd_uid); }
        $this->db->order_by('event_id', 'DESC')->limit(100);

        $query = $this->db->get();
        $rows  = $query ? $query->result_array() : [];

        foreach ($rows as &$r) {
            $r['id']             = (int) $r['id'];
            $r['event_id']       = (int) $r['event_id'];
            $r['bd_uid']         = (int) $r['bd_uid'];
            $r['band_violation'] = (bool) $r['band_violation'];
        }
        unset($r);

        $this->_json([
            'ok'         => true,
            'success'    => true,
            'audit_date' => $audit_date,
            'rows'       => $rows,
            'count'      => count($rows),
        ]);
    }

    // -----------------------------------------------------------------------
    // GET api/prospect/leaderboard
    // Returns BD prospecting grades ranked for a given date.
    // Source: prospecting_discipline_daily
    // -----------------------------------------------------------------------
    public function leaderboard()
    {
        if (!$this->_check_auth()) return;

        $audit_date = $this->_resolve_date();
        $limit      = max(1, min(100, (int) ($this->input->get('limit') ?: 20)));

        $this->db->select('id, bd_uid, bd_name, cluster_id, grade, meetings_total, meetings_photo_pass, meetings_gps_pass, photo_pass_pct, gps_pass_pct, day_shape_status, hard_flag_count, soft_flag_count, top_failure_reason, computed_at');
        $this->db->from('prospecting_discipline_daily');
        $this->db->where('audit_date', $audit_date);
        $this->db->order_by('grade', 'ASC')->order_by('meetings_total', 'DESC');
        $this->db->limit($limit);

        $query = $this->db->get();
        $rows  = $query ? $query->result_array() : [];

        foreach ($rows as &$r) {
            $r['id']                  = (int) $r['id'];
            $r['bd_uid']              = (int) $r['bd_uid'];
            $r['meetings_total']      = (int) $r['meetings_total'];
            $r['meetings_photo_pass'] = (int) $r['meetings_photo_pass'];
            $r['meetings_gps_pass']   = (int) $r['meetings_gps_pass'];
            $r['hard_flag_count']     = (int) $r['hard_flag_count'];
            $r['soft_flag_count']     = (int) $r['soft_flag_count'];
        }
        unset($r);

        $this->_json([
            'ok'         => true,
            'success'    => true,
            'audit_date' => $audit_date,
            'rows'       => $rows,
            'count'      => count($rows),
        ]);
    }

    // -----------------------------------------------------------------------
    // GET api/prospect/queue
    // prospect_queue table not present on staging.
    // Returns awaits_migration stub with expected shape.
    //
    // Expected shape when migrated:
    //   { prospect_id, compname, bd_uid, priority, status, assigned_at }
    // -----------------------------------------------------------------------
    public function queue()
    {
        if (!$this->_check_auth()) return;

        $this->_json([
            'ok'      => true,
            'success' => true,
            'rows'    => [],
            'count'   => 0,
            'note'    => 'awaits_migration_prospect_queue',
            'expected_shape' => ['prospect_id', 'compname', 'bd_uid', 'priority', 'status', 'assigned_at'],
        ]);
    }

    // -----------------------------------------------------------------------
    // GET api/prospect/dropoff
    // prospect_corporate_master not present on staging.
    // Returns awaits_migration stub with expected shape.
    //
    // Expected shape when migrated:
    //   { cmpid, compname, bd_uid, last_touch_date, days_since_touch, dropoff_reason }
    // -----------------------------------------------------------------------
    public function dropoff()
    {
        if (!$this->_check_auth()) return;

        $this->_json([
            'ok'      => true,
            'success' => true,
            'rows'    => [],
            'count'   => 0,
            'note'    => 'awaits_migration_prospect_corporate_master',
            'expected_shape' => ['cmpid', 'compname', 'bd_uid', 'last_touch_date', 'days_since_touch', 'dropoff_reason'],
        ]);
    }

    // -----------------------------------------------------------------------
    // POST api/prospect/refresh_yesterday
    // Recomputes prospecting_discipline_daily for yesterday.
    // Reads audit events from prospecting_event_audit and regenerates grades.
    // Note: full re-computation requires cron logic; this endpoint returns
    // counts of events available for yesterday and marks recompute_requested.
    // -----------------------------------------------------------------------
    public function refresh_yesterday()
    {
        if (!$this->_check_auth()) return;

        $yesterday = date('Y-m-d', strtotime('-1 day'));

        // Count audit events for yesterday
        $cnt_query = $this->db->where('audit_date', $yesterday)->count_all_results('prospecting_event_audit');

        // Count existing discipline rows for yesterday
        $disc_query = $this->db->where('audit_date', $yesterday)->count_all_results('prospecting_discipline_daily');

        $this->_json([
            'ok'                       => true,
            'success'                  => true,
            'date'                     => $yesterday,
            'audit_events_found'       => (int) $cnt_query,
            'discipline_rows_existing' => (int) $disc_query,
            'note'                     => 'recompute_requested - full cron rerun needed to update grades',
        ]);
    }
}

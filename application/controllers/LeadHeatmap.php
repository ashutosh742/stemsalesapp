<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * LeadHeatmap controller
 *
 * REST surface for migration 031 - Lead Activity Heatmap.
 *
 * Endpoints:
 *   GET  /api/lead/activity_heatmap
 *        params: bd_uid (optional), cluster (optional), band (optional),
 *                cstatus (optional), limit (default 200), offset (default 0)
 *        returns: rows from v_lead_activity_heatmap, sorted score_30d DESC.
 *
 *   GET  /api/lead/heatmap_detail
 *        params: lead_id (required)
 *        returns: full signal breakdown + 30-day daily array for sparkline.
 *
 *   POST /api/lead/heatmap/refresh
 *        requires: STEM_CRON_TOKEN header (stricter than DIGEST_TOKEN).
 *        body (optional): lead_id for single-lead refresh; omit for full run.
 *        body (optional): backfill_days=30 triggers backfill mode.
 *        returns: summary dict.
 *
 *   GET  /api/lead/heatmap/band_summary
 *        params: cm_uid (optional), bd_uid (optional)
 *        returns: band count breakdown from v_band_summary_by_bd.
 *
 *   GET  /api/lead/heatmap/on_fire_leaders
 *        params: cm_uid (optional), limit (default 10)
 *        returns: top BDs by on_fire count from v_on_fire_leaders.
 *
 * Auth:
 *   All GET endpoints: Bearer STEM_DIGEST_TOKEN.
 *   POST /heatmap/refresh: Bearer STEM_CRON_TOKEN (service token).
 *
 * CodeIgniter style. Migration 031.
 * Author: STEM ops
 * Date: 2026-05-19
 */
class LeadHeatmap extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->database();
        $this->load->model('LeadHeatmap_model', 'heatmap');
        $this->_require_bearer();
    }

    // ------------------------------------------------------------------------
    private function _require_bearer()
    {
        $hdr = $this->input->get_request_header('Authorization');
        if (!$hdr || strpos($hdr, 'Bearer ') !== 0) {
            $this->_json(['error' => 'unauthorized'], 401);
        }
        $token    = trim(substr($hdr, 7));
        $expected = getenv('STEM_DIGEST_TOKEN');
        if (!$expected || $token !== $expected) {
            // Allow CRON_TOKEN as well for flexibility
            $cron_expected = getenv('STEM_CRON_TOKEN');
            if (!$cron_expected || $token !== $cron_expected) {
                $this->_json(['error' => 'invalid_token'], 401);
            }
        }
    }

    // Stricter auth: only CRON_TOKEN accepted.
    private function _require_cron_token()
    {
        $hdr = $this->input->get_request_header('Authorization');
        if (!$hdr || strpos($hdr, 'Bearer ') !== 0) {
            $this->_json(['error' => 'unauthorized'], 401);
        }
        $token    = trim(substr($hdr, 7));
        $expected = getenv('STEM_CRON_TOKEN');
        if (!$expected || $token !== $expected) {
            $this->_json(['error' => 'cron_token_required'], 403);
        }
    }

    private function _json($data, $code = 200)
    {
        $this->output->set_status_header($code)
                     ->set_content_type('application/json')
                     ->set_output(json_encode($data));
        exit;
    }

    // ========================================================================
    // GET /api/lead/activity_heatmap
    // Returns the heatmap list for a BD, cluster, or band filter.
    // Pilot toggle: if HEATMAP_ENABLED env is not set, only pilot UIDs get data.
    // ========================================================================
    public function activity_heatmap()
    {
        $bd_uid  = (int)$this->input->get('bd_uid');
        $cluster = $this->input->get('cluster');
        $band    = $this->input->get('band');
        $cstatus = (int)$this->input->get('cstatus');
        $limit   = min((int)($this->input->get('limit') ?: 200), 500);
        $offset  = (int)($this->input->get('offset') ?: 0);

        // Pilot gate
        if (!$this->_heatmap_enabled_for($bd_uid)) {
            $this->_json(['error' => 'feature_not_enabled', 'rows' => []], 403);
        }

        $sql    = "SELECT * FROM v_lead_activity_heatmap WHERE 1=1";
        $params = [];

        if ($bd_uid) {
            $sql .= " AND bd_uid = ?";
            $params[] = $bd_uid;
        }
        if ($cluster) {
            $sql .= " AND cluster = ?";
            $params[] = $cluster;
        }
        if ($band && in_array($band, ['cold','warm','hot','on_fire'])) {
            $sql .= " AND band = ?";
            $params[] = $band;
        }
        if ($cstatus) {
            $sql .= " AND cstatus = ?";
            $params[] = $cstatus;
        }

        $sql .= " ORDER BY score_30d DESC LIMIT ? OFFSET ?";
        $params[] = $limit;
        $params[] = $offset;

        $rows = $this->db->query($sql, $params)->result_array();
        $this->_json([
            'rows'   => $rows,
            'count'  => count($rows),
            'limit'  => $limit,
            'offset' => $offset,
        ]);
    }

    // ========================================================================
    // GET /api/lead/heatmap_detail
    // Full breakdown for one lead: signal counts, last dates, daily array.
    // ========================================================================
    public function heatmap_detail()
    {
        $lead_id = (int)$this->input->get('lead_id');
        if (!$lead_id) {
            $this->_json(['error' => 'missing_lead_id'], 400);
        }

        // Score row
        $score_row = $this->db->query("
            SELECT las.*,
                   ic.compny_nm AS school,
                   ic.cstatus,
                   COALESCE(ic.fbudget, 0) AS fbudget_rs,
                   CONCAT(ub.firstName, ' ', COALESCE(ub.lastName,'')) AS bd_name
              FROM lead_activity_score las
              INNER JOIN init_call ic ON ic.id = las.lead_id
              LEFT  JOIN user ub      ON ub.uid = las.bd_uid
             WHERE las.lead_id = ?
             LIMIT 1
        ", [$lead_id])->row_array();

        if (!$score_row) {
            $this->_json(['error' => 'no_score_for_lead', 'lead_id' => $lead_id], 404);
        }

        // Signal type counts for the 30-day window (event-level)
        $signal_counts = $this->_signal_counts($lead_id);

        // 30-day daily breakdown for sparkline
        $daily = $this->heatmap->daily_breakdown($lead_id);

        // MoM count in window
        $mom_count = (int)$this->db->query("
            SELECT COUNT(*) AS cnt
              FROM mom_data m
              INNER JOIN tblcallevents t ON t.id = m.event_id
             WHERE t.cid_id = ?
               AND m.approval_status = 'approved'
               AND m.approved_at >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)
        ", [$lead_id])->row_array()['cnt'];

        $this->_json([
            'lead_id'       => $lead_id,
            'score'         => $score_row,
            'signal_counts' => $signal_counts,
            'mom_count_30d' => $mom_count,
            'daily'         => $daily,
        ]);
    }

    // ========================================================================
    // POST /api/lead/heatmap/refresh
    // Cron-triggered. Requires STEM_CRON_TOKEN.
    // Optional body: lead_id (single refresh), backfill_days (int, default 0).
    // ========================================================================
    public function refresh()
    {
        if ($this->input->method() !== 'post') {
            $this->_json(['error' => 'post_only'], 405);
        }
        $this->_require_cron_token();

        $lead_id      = (int)$this->input->post('lead_id');
        $backfill_days = (int)($this->input->post('backfill_days') ?: 0);

        if ($lead_id) {
            $out = $this->heatmap->refresh_single($lead_id);
        } else {
            $out = $this->heatmap->run_nightly($backfill_days);
        }
        $this->_json($out);
    }

    // ========================================================================
    // GET /api/lead/heatmap/band_summary
    // Band count breakdown per BD (or filtered to cm_uid or bd_uid).
    // ========================================================================
    public function band_summary()
    {
        $cm_uid = (int)$this->input->get('cm_uid');
        $bd_uid = (int)$this->input->get('bd_uid');

        $sql    = "SELECT * FROM v_band_summary_by_bd WHERE 1=1";
        $params = [];
        if ($cm_uid) { $sql .= " AND cm_uid = ?"; $params[] = $cm_uid; }
        if ($bd_uid) { $sql .= " AND bd_uid = ?"; $params[] = $bd_uid; }
        $sql .= " ORDER BY on_fire_count DESC, avg_score_30d DESC";

        $rows = $this->db->query($sql, $params)->result_array();
        $this->_json(['rows' => $rows, 'count' => count($rows)]);
    }

    // ========================================================================
    // GET /api/lead/heatmap/on_fire_leaders
    // Top BDs by on_fire lead count. Used for the applause callout.
    // ========================================================================
    public function on_fire_leaders()
    {
        $cm_uid = (int)$this->input->get('cm_uid');
        $limit  = min((int)($this->input->get('limit') ?: 10), 50);

        $sql    = "SELECT * FROM v_on_fire_leaders WHERE 1=1";
        $params = [];
        if ($cm_uid) { $sql .= " AND cm_uid = ?"; $params[] = $cm_uid; }
        $sql .= " ORDER BY on_fire_count DESC LIMIT ?";
        $params[] = $limit;

        $rows = $this->db->query($sql, $params)->result_array();
        $this->_json(['rows' => $rows, 'count' => count($rows)]);
    }

    // ------------------------------------------------------------------------
    // PRIVATE: count events per signal type for one lead in 30-day window.
    // ------------------------------------------------------------------------
    private function _signal_counts($lead_id)
    {
        $rows = $this->db->query("
            SELECT
              SUM(CASE WHEN actiontype_id IN (1,2) THEN 1 ELSE 0 END)                             AS meeting_count,
              SUM(CASE WHEN actiontype_id = 5 THEN 1 ELSE 0 END)                                  AS call_count,
              SUM(CASE WHEN cm_uid IS NOT NULL AND cm_uid <> 0 THEN 1 ELSE 0 END)                 AS cm_touch_count,
              SUM(CASE WHEN photo_url IS NOT NULL AND photo_url <> '' THEN 1 ELSE 0 END)           AS photo_count,
              SUM(CASE WHEN lat IS NOT NULL AND lat <> 0 THEN 1 ELSE 0 END)                        AS gps_count
            FROM tblcallevents
            WHERE cid_id = ?
              AND DATE(event_date) >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)
        ", [$lead_id])->row_array();

        return $rows ?: [
            'meeting_count'  => 0, 'call_count' => 0, 'cm_touch_count' => 0,
            'photo_count'    => 0, 'gps_count'  => 0,
        ];
    }

    // ------------------------------------------------------------------------
    // PRIVATE: pilot toggle check.
    // Returns true if heatmap is globally enabled or if uid is in pilot list.
    // ------------------------------------------------------------------------
    private function _heatmap_enabled_for($bd_uid)
    {
        if (getenv('HEATMAP_ENABLED') === '1') {
            return true;
        }
        $pilot_uids_raw = getenv('HEATMAP_PILOT_UIDS') ?: '12,42,43,44,45,46';
        $pilot_uids     = array_map('intval', explode(',', $pilot_uids_raw));
        // If no bd_uid supplied (CM or RM fetching full cluster), allow.
        if (!$bd_uid) return true;
        return in_array($bd_uid, $pilot_uids);
    }
}

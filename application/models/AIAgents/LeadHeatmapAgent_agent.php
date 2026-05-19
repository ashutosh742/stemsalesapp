<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * LeadHeatmap_model
 *
 * Nightly refresh agent for lead_activity_score.
 * Computes 30-day rolling activity intensity per lead using linear decay.
 * Called by cron at 2 AM IST via POST /api/lead/heatmap/refresh.
 *
 * Scoring weights:
 *   meeting (actiontype 1,2)         10
 *   MoM approved                     15
 *   CM touch (cm_uid present)         8
 *   email inbound                     6
 *   email outbound                    4
 *   call (actiontype 5)               5
 *   photo captured (photo_url set)    3
 *   GPS check-in (lat not zero)       2
 *
 * Decay: linear 30 days.
 *   factor = (30 - days_ago) / 30
 *   Events 30+ days ago contribute 0.
 *
 * Bands:
 *   cold      score_30d under 20
 *   warm      20 to 49
 *   hot       50 to 89
 *   on_fire   90 and over
 *
 * Dependencies:
 *   migration 024: tblcallevents, mom_data, reporting_hierarchy
 *   migration 026: lead_email_log (skipped gracefully if absent)
 *   migration 029: tblcallevents.photo_url, lat, lng
 *   migration 031: lead_activity_score (this migration)
 *
 * Author: STEM ops
 * Migration: 031
 * Date: 2026-05-19
 */
class LeadHeatmap_model extends CI_Model
{
    /** Scoring weights per signal type. */
    private $weights = [
        'meeting'    => 10,
        'mom'        => 15,
        'cm_touch'   => 8,
        'email_in'   => 6,
        'email_out'  => 4,
        'call'       => 5,
        'photo'      => 3,
        'gps'        => 2,
    ];

    /** Band thresholds (lower bound inclusive). */
    private $bands = [
        'on_fire' => 90,
        'hot'     => 50,
        'warm'    => 20,
        'cold'    => 0,
    ];

    /** Actiontype IDs that count as a meeting/visit. */
    private $meeting_types = [1, 2];

    /** Actiontype ID for a call. */
    private $call_type = 5;

    /** Batch size for lead processing. */
    private $batch_size = 500;

    /** Whether lead_email_log table is available. */
    private $email_log_available = false;

    public function __construct()
    {
        parent::__construct();
        $this->load->database();
        $this->_detect_email_log();
    }

    // ------------------------------------------------------------------------
    // Detect whether lead_email_log table exists (migration 026).
    // Graceful skip if not yet deployed.
    // ------------------------------------------------------------------------
    private function _detect_email_log()
    {
        $result = $this->db->query(
            "SELECT COUNT(*) AS cnt FROM information_schema.tables
              WHERE table_schema = DATABASE()
                AND table_name = 'lead_email_log'"
        )->row_array();
        $this->email_log_available = ($result['cnt'] > 0);
    }

    // ------------------------------------------------------------------------
    // ENTRY POINT: nightly run.
    // Processes all active leads in batches of 500.
    // Returns a summary dict.
    // ------------------------------------------------------------------------
    public function run_nightly($backfill_days = 0)
    {
        $out = [
            'mode'            => $backfill_days > 0 ? 'backfill' : 'nightly',
            'backfill_days'   => $backfill_days,
            'email_log_used'  => $this->email_log_available,
            'leads_processed' => 0,
            'rows_upserted'   => 0,
            'on_fire_count'   => 0,
            'hot_count'       => 0,
            'warm_count'      => 0,
            'cold_count'      => 0,
            'errors'          => [],
            'ran_at'          => date('c'),
        ];

        $lead_ids = $this->_fetch_active_lead_ids();
        $total    = count($lead_ids);
        $out['leads_processed'] = $total;

        if ($total === 0) {
            return $out;
        }

        // Process in batches to avoid memory pressure.
        foreach (array_chunk($lead_ids, $this->batch_size) as $batch) {
            try {
                $upserted = $this->_process_batch($batch, $out);
                $out['rows_upserted'] += $upserted;
            } catch (Exception $e) {
                $out['errors'][] = $e->getMessage();
            }
        }

        return $out;
    }

    // ------------------------------------------------------------------------
    // Fetch IDs of all active leads.
    // Active = cstatus 1,2,3,6,7,8,9 plus recently closed (within 30 days).
    // ------------------------------------------------------------------------
    private function _fetch_active_lead_ids()
    {
        $rows = $this->db->query("
            SELECT id
              FROM init_call
             WHERE cstatus IN (1, 2, 3, 6, 7, 8, 9)
                OR (cstatus IN (12, 13)
                    AND createDate >= DATE_SUB(CURDATE(), INTERVAL 30 DAY))
             ORDER BY id
        ")->result_array();
        return array_column($rows, 'id');
    }

    // ------------------------------------------------------------------------
    // Process one batch of lead IDs.
    // Returns count of upserted rows.
    // ------------------------------------------------------------------------
    private function _process_batch(array $lead_ids, array &$out)
    {
        if (empty($lead_ids)) return 0;

        $placeholders = implode(',', array_fill(0, count($lead_ids), '?'));
        $upserted     = 0;

        // --- Step 1: pull event scores from tblcallevents ---
        $event_sql = "
            SELECT
              t.cid_id                                                AS lead_id,
              ic.mainbd                                               AS bd_uid,
              (SELECT parent_uid FROM reporting_hierarchy
                WHERE employee_uid = ic.mainbd AND active = 1 LIMIT 1) AS cm_uid,

              -- score_today (day 0 only)
              SUM(
                CASE WHEN DATEDIFF(CURDATE(), DATE(t.event_date)) = 0 THEN
                  ((CASE WHEN t.actiontype_id IN ({$this->_meeting_type_csv()}) THEN {$this->weights['meeting']} ELSE 0 END)
                   + (CASE WHEN t.cm_uid IS NOT NULL AND t.cm_uid <> 0 THEN {$this->weights['cm_touch']} ELSE 0 END)
                   + (CASE WHEN t.photo_url IS NOT NULL AND t.photo_url <> '' THEN {$this->weights['photo']} ELSE 0 END)
                   + (CASE WHEN t.lat IS NOT NULL AND t.lat <> 0 THEN {$this->weights['gps']} ELSE 0 END)
                   + (CASE WHEN t.actiontype_id = {$this->call_type} THEN {$this->weights['call']} ELSE 0 END))
                ELSE 0 END
              ) AS score_today,

              -- score_7d (days 0-6)
              SUM(
                CASE WHEN DATEDIFF(CURDATE(), DATE(t.event_date)) BETWEEN 0 AND 6 THEN
                  ((CASE WHEN t.actiontype_id IN ({$this->_meeting_type_csv()}) THEN {$this->weights['meeting']} ELSE 0 END)
                   + (CASE WHEN t.cm_uid IS NOT NULL AND t.cm_uid <> 0 THEN {$this->weights['cm_touch']} ELSE 0 END)
                   + (CASE WHEN t.photo_url IS NOT NULL AND t.photo_url <> '' THEN {$this->weights['photo']} ELSE 0 END)
                   + (CASE WHEN t.lat IS NOT NULL AND t.lat <> 0 THEN {$this->weights['gps']} ELSE 0 END)
                   + (CASE WHEN t.actiontype_id = {$this->call_type} THEN {$this->weights['call']} ELSE 0 END))
                  * ((30 - DATEDIFF(CURDATE(), DATE(t.event_date))) / 30.0)
                ELSE 0 END
              ) AS score_7d,

              -- score_30d (days 0-29)
              SUM(
                CASE WHEN DATEDIFF(CURDATE(), DATE(t.event_date)) BETWEEN 0 AND 29 THEN
                  ((CASE WHEN t.actiontype_id IN ({$this->_meeting_type_csv()}) THEN {$this->weights['meeting']} ELSE 0 END)
                   + (CASE WHEN t.cm_uid IS NOT NULL AND t.cm_uid <> 0 THEN {$this->weights['cm_touch']} ELSE 0 END)
                   + (CASE WHEN t.photo_url IS NOT NULL AND t.photo_url <> '' THEN {$this->weights['photo']} ELSE 0 END)
                   + (CASE WHEN t.lat IS NOT NULL AND t.lat <> 0 THEN {$this->weights['gps']} ELSE 0 END)
                   + (CASE WHEN t.actiontype_id = {$this->call_type} THEN {$this->weights['call']} ELSE 0 END))
                  * ((30 - DATEDIFF(CURDATE(), DATE(t.event_date))) / 30.0)
                ELSE 0 END
              ) AS score_30d_events,

              -- Last-touch timestamps
              MAX(CASE WHEN t.actiontype_id IN ({$this->_meeting_type_csv()}) THEN t.event_date END) AS last_meeting_at,
              MAX(CASE WHEN t.actiontype_id = {$this->call_type} THEN t.event_date END)              AS last_call_at,
              MAX(CASE WHEN t.cm_uid IS NOT NULL AND t.cm_uid <> 0 THEN t.event_date END)            AS last_cm_touch_at,
              MAX(CASE WHEN t.photo_url IS NOT NULL AND t.photo_url <> '' THEN t.event_date END)      AS last_photo_at,
              MAX(CASE WHEN t.lat IS NOT NULL AND t.lat <> 0 THEN t.event_date END)                  AS last_gps_at,
              COALESCE(DATEDIFF(CURDATE(), MAX(DATE(t.event_date))), 99)                             AS days_since_touch

            FROM tblcallevents t
            INNER JOIN init_call ic ON ic.id = t.cid_id
            WHERE t.cid_id IN ({$placeholders})
            GROUP BY t.cid_id
        ";
        $event_rows = $this->db->query($event_sql, $lead_ids)->result_array();
        $event_map  = array_column($event_rows, null, 'lead_id');

        // --- Step 2: MoM approval scores ---
        $mom_sql = "
            SELECT
              t.cid_id AS lead_id,
              SUM(
                CASE WHEN DATEDIFF(CURDATE(), DATE(m.approved_at)) BETWEEN 0 AND 29 THEN
                  {$this->weights['mom']} * ((30 - DATEDIFF(CURDATE(), DATE(m.approved_at))) / 30.0)
                ELSE 0 END
              ) AS mom_score,
              MAX(m.approved_at) AS last_mom_at
            FROM mom_data m
            INNER JOIN tblcallevents t ON t.id = m.event_id
            WHERE t.cid_id IN ({$placeholders})
              AND m.approval_status = 'approved'
            GROUP BY t.cid_id
        ";
        $mom_rows = $this->db->query($mom_sql, $lead_ids)->result_array();
        $mom_map  = array_column($mom_rows, null, 'lead_id');

        // --- Step 3: Email scores (if table exists) ---
        $email_map = [];
        if ($this->email_log_available) {
            $email_sql = "
                SELECT
                  lead_id,
                  SUM(
                    CASE WHEN direction = 'in' AND DATEDIFF(CURDATE(), DATE(sent_at)) BETWEEN 0 AND 29
                      THEN {$this->weights['email_in']} * ((30 - DATEDIFF(CURDATE(), DATE(sent_at))) / 30.0)
                    ELSE 0 END
                  ) AS email_in_score,
                  SUM(
                    CASE WHEN direction = 'out' AND DATEDIFF(CURDATE(), DATE(sent_at)) BETWEEN 0 AND 29
                      THEN {$this->weights['email_out']} * ((30 - DATEDIFF(CURDATE(), DATE(sent_at))) / 30.0)
                    ELSE 0 END
                  ) AS email_out_score,
                  MAX(sent_at) AS last_email_at
                FROM lead_email_log
                WHERE lead_id IN ({$placeholders})
                GROUP BY lead_id
            ";
            $email_rows = $this->db->query($email_sql, $lead_ids)->result_array();
            $email_map  = array_column($email_rows, null, 'lead_id');
        }

        // --- Step 4: For each lead, assemble final score and upsert ---
        foreach ($lead_ids as $lead_id) {
            $ev  = $event_map[$lead_id] ?? [];
            $mom = $mom_map[$lead_id]   ?? [];
            $em  = $email_map[$lead_id] ?? [];

            $bd_uid  = $ev['bd_uid']  ?? null;
            $cm_uid  = $ev['cm_uid']  ?? null;

            // If no events at all, fetch bd/cm from init_call
            if (!$bd_uid) {
                $ic = $this->db->query(
                    "SELECT mainbd FROM init_call WHERE id = ? LIMIT 1",
                    [$lead_id]
                )->row_array();
                $bd_uid = $ic['mainbd'] ?? null;
                if ($bd_uid) {
                    $rh = $this->db->query(
                        "SELECT parent_uid FROM reporting_hierarchy
                          WHERE employee_uid = ? AND active = 1 LIMIT 1",
                        [$bd_uid]
                    )->row_array();
                    $cm_uid = $rh['parent_uid'] ?? null;
                }
            }

            $score_30d = ((float)($ev['score_30d_events'] ?? 0))
                       + ((float)($mom['mom_score'] ?? 0))
                       + ((float)($em['email_in_score'] ?? 0))
                       + ((float)($em['email_out_score'] ?? 0));

            $score_7d    = (float)($ev['score_7d'] ?? 0);
            $score_today = (float)($ev['score_today'] ?? 0);
            $band        = $this->_band($score_30d);

            // Determine top signal
            $signal_scores = [
                'meeting'   => (float)($ev['score_30d_events'] ?? 0),
                'mom'       => (float)($mom['mom_score'] ?? 0),
                'email_in'  => (float)($em['email_in_score'] ?? 0),
                'email_out' => (float)($em['email_out_score'] ?? 0),
            ];
            arsort($signal_scores);
            $top_signal = key($signal_scores) ?: null;

            $days_touch = (int)($ev['days_since_touch'] ?? 99);

            // Update email last_touch for days_since if email was more recent
            $last_email_at = $em['last_email_at'] ?? null;
            if ($last_email_at) {
                $email_days = (int)$this->db->query(
                    "SELECT DATEDIFF(CURDATE(), ?) AS d",
                    [$last_email_at]
                )->row_array()['d'];
                if ($email_days < $days_touch) {
                    $days_touch = $email_days;
                }
            }

            // UPSERT
            $this->db->query("
                INSERT INTO lead_activity_score
                  (lead_id, bd_uid, cm_uid,
                   score_today, score_7d, score_30d,
                   last_meeting_at, last_mom_at, last_email_at,
                   last_call_at, last_cm_touch_at, last_photo_at, last_gps_at,
                   heatmap_band, top_signal_type, days_since_touch, computed_at)
                VALUES
                  (?, ?, ?,
                   ?, ?, ?,
                   ?, ?, ?,
                   ?, ?, ?, ?,
                   ?, ?, ?, NOW())
                ON DUPLICATE KEY UPDATE
                  bd_uid           = VALUES(bd_uid),
                  cm_uid           = VALUES(cm_uid),
                  score_today      = VALUES(score_today),
                  score_7d         = VALUES(score_7d),
                  score_30d        = VALUES(score_30d),
                  last_meeting_at  = VALUES(last_meeting_at),
                  last_mom_at      = VALUES(last_mom_at),
                  last_email_at    = VALUES(last_email_at),
                  last_call_at     = VALUES(last_call_at),
                  last_cm_touch_at = VALUES(last_cm_touch_at),
                  last_photo_at    = VALUES(last_photo_at),
                  last_gps_at      = VALUES(last_gps_at),
                  heatmap_band     = VALUES(heatmap_band),
                  top_signal_type  = VALUES(top_signal_type),
                  days_since_touch = VALUES(days_since_touch),
                  computed_at      = NOW()
            ", [
                $lead_id, $bd_uid, $cm_uid,
                round($score_today, 2), round($score_7d, 2), round($score_30d, 2),
                $ev['last_meeting_at'] ?? null,
                $mom['last_mom_at']    ?? null,
                $last_email_at,
                $ev['last_call_at']    ?? null,
                $ev['last_cm_touch_at'] ?? null,
                $ev['last_photo_at']   ?? null,
                $ev['last_gps_at']     ?? null,
                $band, $top_signal, $days_touch,
            ]);

            if ($this->db->affected_rows() > 0) {
                $upserted++;
                $out[$band . '_count'] = ($out[$band . '_count'] ?? 0) + 1;
            }
        }

        return $upserted ?? count($lead_ids);
    }

    // ------------------------------------------------------------------------
    // PUBLIC: refresh a single lead on demand.
    // Used by POST /api/lead/heatmap/refresh with lead_id param.
    // ------------------------------------------------------------------------
    public function refresh_single($lead_id)
    {
        $lead_id = (int)$lead_id;
        if (!$lead_id) return ['error' => 'invalid_lead_id'];
        $dummy = [
            'on_fire_count' => 0, 'hot_count' => 0,
            'warm_count' => 0, 'cold_count' => 0,
        ];
        $n = $this->_process_batch([$lead_id], $dummy);
        return [
            'lead_id'   => $lead_id,
            'upserted'  => $n,
            'band'      => $this->db->query(
                "SELECT heatmap_band FROM lead_activity_score WHERE lead_id = ? LIMIT 1",
                [$lead_id]
            )->row_array()['heatmap_band'] ?? null,
            'refreshed_at' => date('c'),
        ];
    }

    private function _band($score)
    {
        $score = (float)$score;
        if ($score >= 90) return 'on_fire';
        if ($score >= 50) return 'hot';
        if ($score >= 20) return 'warm';
        return 'cold';
    }

    private function _meeting_type_csv()
    {
        return implode(',', $this->meeting_types);
    }

    // Returns 30-day daily raw score array for sparkline. day_offset 0=today.
    public function daily_breakdown($lead_id)
    {
        $lead_id = (int)$lead_id;
        $rows = $this->db->query("
            SELECT
              DATEDIFF(CURDATE(), DATE(event_date)) AS day_offset,
              SUM(
                (CASE WHEN actiontype_id IN ({$this->_meeting_type_csv()}) THEN {$this->weights['meeting']} ELSE 0 END)
                + (CASE WHEN cm_uid IS NOT NULL AND cm_uid <> 0 THEN {$this->weights['cm_touch']} ELSE 0 END)
                + (CASE WHEN photo_url IS NOT NULL AND photo_url <> '' THEN {$this->weights['photo']} ELSE 0 END)
                + (CASE WHEN lat IS NOT NULL AND lat <> 0 THEN {$this->weights['gps']} ELSE 0 END)
                + (CASE WHEN actiontype_id = {$this->call_type} THEN {$this->weights['call']} ELSE 0 END)
              ) AS raw_score
            FROM tblcallevents
            WHERE cid_id = ?
              AND DATE(event_date) >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)
            GROUP BY day_offset
            ORDER BY day_offset
        ", [$lead_id])->result_array();
        $map = array_column($rows, 'raw_score', 'day_offset');
        $out = [];
        for ($d = 29; $d >= 0; $d--) {
            $out[] = [
                'day_offset' => $d,
                'day_label'  => date('d M', strtotime("-{$d} days")),
                'raw_score'  => (float)($map[$d] ?? 0),
            ];
        }
        return $out;
    }
}

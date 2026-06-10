<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * LeadIntelligence_model
 *
 * Model for migration 058: lead scoring, cohort analysis, budget range,
 * and breach auto-action. CodeIgniter 3 style.
 *
 * Score weights (must sum to 100):
 *   Schedule VII fit      30 points
 *   Prior giving signal   20 points
 *   Partner multiplier    20 points
 *   Recency decay         15 points
 *   fbudget size          15 points
 */
class LeadIntelligence_model extends CI_Model
{
    const MODEL_VERSION = 'v1.0';

    // Score weight constants
    const W_SCHEDULE_VII  = 30;
    const W_PRIOR_GIVING  = 20;
    const W_PARTNER_MULT  = 20;
    const W_RECENCY       = 15;
    const W_FBUDGET       = 15;

    // Budget thresholds in Rs for fbudget component
    const FBUDGET_HIGH    = 2500000;  // 25 lakh - full 15 points
    const FBUDGET_MID     = 500000;   // 5 lakh  - 8 points
    const FBUDGET_LOW     = 100000;   // 1 lakh  - 3 points

    // Recency: days since last meaningful activity
    const RECENCY_HOT     = 7;   // within 7 days  - full 15 points
    const RECENCY_WARM    = 21;  // within 21 days - 8 points
    const RECENCY_COLD    = 45;  // within 45 days - 3 points

    // Breach threshold in days per cstatus bucket
    const BREACH_DAYS_EARLY  = 14;
    const BREACH_DAYS_ACTIVE = 21;
    const BREACH_DAYS_LATE   = 30;

    // Override window hours
    const OVERRIDE_WINDOW_HOURS = 24;

    public function __construct()
    {
        parent::__construct();
    }

    // ------------------------------------------------------------------------
    // A.3  LEAD SCORING
    // compute_score($cid_id)
    // Returns score 0-100. Writes a row to lead_score_log and updates
    // lead_score_cached + lead_score_updated_at on init_call.
    // ------------------------------------------------------------------------
    public function compute_score($cid_id)
    {
        $cid_id = (int) $cid_id;

        // Fetch lead row
        $row = $this->db->query(
            "SELECT ic.id, ic.cstatus, ic.creation_path,
                    ic.fbudget_min, ic.fbudget_max,
                    ic.program_id, ic.created_at,
                    ic.mainbd AS assigned_to,
                    (SELECT MAX(tce.event_time)
                     FROM tblcallevents tce
                     WHERE tce.cid_id = ic.id) AS last_activity_at
             FROM init_call ic
             WHERE ic.id = '$cid_id'
             LIMIT 1"
        )->row();

        if (!$row) {
            return array('error' => 'lead_not_found', 'cid_id' => $cid_id);
        }

        // --- Component 1: Schedule VII fit (0-30) ---
        // Proxied by cstatus: higher stages = stronger fit signal
        // cstatus range: 1-13 in STEM pipeline
        $cstatus = (int) $row->cstatus;
        if ($cstatus >= 9) {
            $schedule_vii_fit = self::W_SCHEDULE_VII;          // Won / RP / Very Positive
        } elseif ($cstatus >= 6) {
            $schedule_vii_fit = (int) round(self::W_SCHEDULE_VII * 0.7);  // Positive / Engaged
        } elseif ($cstatus >= 3) {
            $schedule_vii_fit = (int) round(self::W_SCHEDULE_VII * 0.4);  // Initial contact made
        } else {
            $schedule_vii_fit = (int) round(self::W_SCHEDULE_VII * 0.15); // Raw / cold
        }

        // --- Component 2: Prior giving signal (0-20) ---
        // Determined by whether the program_id maps to a fee-paying history proxy.
        // We use creation_path as a proxy: partner > inbound > outbound > cold_call
        $creation_path = strtolower((string) $row->creation_path);
        if (strpos($creation_path, 'referral') !== false || strpos($creation_path, 'alumni') !== false) {
            $prior_giving_signal = self::W_PRIOR_GIVING;
        } elseif (strpos($creation_path, 'inbound') !== false || strpos($creation_path, 'web') !== false) {
            $prior_giving_signal = (int) round(self::W_PRIOR_GIVING * 0.7);
        } elseif (strpos($creation_path, 'partner') !== false) {
            $prior_giving_signal = (int) round(self::W_PRIOR_GIVING * 0.5);
        } else {
            $prior_giving_signal = (int) round(self::W_PRIOR_GIVING * 0.2);
        }

        // --- Component 3: Partner multiplier (0-20) ---
        // Check if there is a linked partner record via creation_path tag
        $has_partner = (
            strpos($creation_path, 'partner') !== false ||
            strpos($creation_path, 'channel') !== false ||
            strpos($creation_path, 'agent')   !== false
        );
        if ($has_partner) {
            $partner_multiplier = self::W_PARTNER_MULT;
        } else {
            // Still award partial points for assigned BD with an RP meeting on record
            $rp_check = $this->db->query(
                "SELECT COUNT(*) AS cnt
                 FROM tblcallevents
                 WHERE cid_id = '$cid_id'
                   AND event_type IN ('rp_meeting','rp_call')
                 LIMIT 1"
            )->row();
            $has_rp = ($rp_check && (int) $rp_check->cnt > 0);
            $partner_multiplier = $has_rp ? (int) round(self::W_PARTNER_MULT * 0.5) : 0;
        }

        // --- Component 4: Recency decay (0-15) ---
        if (!empty($row->last_activity_at)) {
            $days_since = (int) ceil(
                (time() - strtotime($row->last_activity_at)) / 86400
            );
        } else {
            $days_since = 9999;
        }

        if ($days_since <= self::RECENCY_HOT) {
            $recency_decay = self::W_RECENCY;
        } elseif ($days_since <= self::RECENCY_WARM) {
            $recency_decay = (int) round(self::W_RECENCY * 0.55);
        } elseif ($days_since <= self::RECENCY_COLD) {
            $recency_decay = (int) round(self::W_RECENCY * 0.2);
        } else {
            $recency_decay = 0;
        }

        // --- Component 5: fbudget size (0-15) ---
        $budget_mid = 0.0;
        if (!empty($row->fbudget_min) && !empty($row->fbudget_max)) {
            $budget_mid = ((float) $row->fbudget_min + (float) $row->fbudget_max) / 2.0;
        } elseif (!empty($row->fbudget_max)) {
            $budget_mid = (float) $row->fbudget_max;
        } elseif (!empty($row->fbudget_min)) {
            $budget_mid = (float) $row->fbudget_min;
        }

        if ($budget_mid >= self::FBUDGET_HIGH) {
            $fbudget_component = self::W_FBUDGET;
        } elseif ($budget_mid >= self::FBUDGET_MID) {
            $fbudget_component = (int) round(self::W_FBUDGET * 0.55);
        } elseif ($budget_mid >= self::FBUDGET_LOW) {
            $fbudget_component = (int) round(self::W_FBUDGET * 0.2);
        } else {
            $fbudget_component = 0;
        }

        // --- Total ---
        $score_total = min(100, max(0,
            $schedule_vii_fit +
            $prior_giving_signal +
            $partner_multiplier +
            $recency_decay +
            $fbudget_component
        ));

        // Persist to lead_score_log
        $this->db->query(
            "INSERT INTO lead_score_log
                (cid_id, score_total, schedule_vii_fit, prior_giving_signal,
                 partner_multiplier, recency_decay, fbudget_component,
                 computed_at, model_version)
             VALUES (
                '$cid_id', '$score_total', '$schedule_vii_fit', '$prior_giving_signal',
                '$partner_multiplier', '$recency_decay', '$fbudget_component',
                NOW(), '" . self::MODEL_VERSION . "'
             )"
        );

        // Update cache on init_call
        $this->db->query(
            "UPDATE init_call
             SET lead_score_cached     = '$score_total',
                 lead_score_updated_at = NOW()
             WHERE id = '$cid_id'
             LIMIT 1"
        );

        return array(
            'cid_id'              => $cid_id,
            'score_total'         => $score_total,
            'schedule_vii_fit'    => $schedule_vii_fit,
            'prior_giving_signal' => $prior_giving_signal,
            'partner_multiplier'  => $partner_multiplier,
            'recency_decay'       => $recency_decay,
            'fbudget_component'   => $fbudget_component,
            'model_version'       => self::MODEL_VERSION,
            'computed_at'         => date('Y-m-d H:i:s'),
        );
    }

    // ------------------------------------------------------------------------
    // E.6  COHORT VIEWER
    // get_cohort($cohort_from, $cohort_to)
    // Returns conversion rate per creation_path for leads created in range.
    // Conversion = reached cstatus >= 9 (Positive / Won tier).
    // ------------------------------------------------------------------------
    public function get_cohort($cohort_from, $cohort_to)
    {
        $from = $this->db->escape_str($cohort_from);
        $to   = $this->db->escape_str($cohort_to);

        $rows = $this->db->query(
            "SELECT
                COALESCE(ic.lead_source, 'unknown')    AS creation_path,
                COUNT(*)                                 AS total_leads,
                SUM(CASE WHEN ic.cstatus >= 9  THEN 1 ELSE 0 END) AS reached_positive,
                SUM(CASE WHEN ic.cstatus = 13 THEN 1 ELSE 0 END)  AS won_count,
                SUM(CASE WHEN ic.cstatus IN (11,12) THEN 1 ELSE 0 END) AS lost_count,
                ROUND(
                    100.0 * SUM(CASE WHEN ic.cstatus >= 9 THEN 1 ELSE 0 END) /
                    NULLIF(COUNT(*), 0),
                    1
                ) AS conversion_rate_pct
             FROM init_call ic
             WHERE DATE(ic.created_at) BETWEEN '$from' AND '$to'
             GROUP BY ic.lead_source
             ORDER BY conversion_rate_pct DESC"
        )->result_array();

        return array(
            'from'  => $cohort_from,
            'to'    => $cohort_to,
            'rows'  => $rows,
            'count' => count($rows),
        );
    }

    // ------------------------------------------------------------------------
    // B.3  BREACH AUTO-ACTION
    // queue_breach_action($cid_id, $reason)
    // Inserts a pending breach action with a 24-hour manager override window.
    // Determines to_cstatus based on current cstatus.
    // ------------------------------------------------------------------------
    public function queue_breach_action($cid_id, $reason)
    {
        $cid_id = (int) $cid_id;
        $reason = $this->db->escape_str((string) $reason);

        // Fetch current status and days stuck
        $row = $this->db->query(
            "SELECT ic.cstatus,
                    DATEDIFF(NOW(), ic.samestatustilldate) AS breach_days
             FROM init_call ic
             WHERE ic.id = '$cid_id'
             LIMIT 1"
        )->row();

        if (!$row) {
            return array('error' => 'lead_not_found');
        }

        $from_cstatus = (int) $row->cstatus;
        $breach_days  = (int) $row->breach_days;

        // Determine auto target: move down one meaningful step
        // Mapping: if stuck in early stage move to Not Pursued (10),
        // if in active stages flag for manager review via cstatus 14 (custom hold).
        // For safety we always keep within known range 1-13.
        if ($from_cstatus <= 4) {
            $to_cstatus = max(1, $from_cstatus - 1);
        } elseif ($from_cstatus <= 8) {
            $to_cstatus = 10; // Not pursued
        } else {
            $to_cstatus = $from_cstatus; // Do not auto-move high-value leads; let manager decide
        }

        $override_ends = date('Y-m-d H:i:s', time() + self::OVERRIDE_WINDOW_HOURS * 3600);

        // Guard: do not queue duplicate pending action for same lead
        $existing = $this->db->query(
            "SELECT id FROM breach_auto_action_log
             WHERE cid_id = '$cid_id'
               AND action_taken = 'pending'
             LIMIT 1"
        )->row();

        if ($existing) {
            return array(
                'ok'        => false,
                'message'   => 'pending_action_already_exists',
                'action_id' => (int) $existing->id,
            );
        }

        $this->db->query(
            "INSERT INTO breach_auto_action_log
                (cid_id, from_cstatus, to_cstatus, breach_days, action_taken,
                 override_window_ends_at, queued_at)
             VALUES (
                '$cid_id', '$from_cstatus', '$to_cstatus', '$breach_days', 'pending',
                '$override_ends', NOW()
             )"
        );
        $action_id = $this->db->insert_id();

        return array(
            'ok'                    => true,
            'action_id'             => $action_id,
            'cid_id'                => $cid_id,
            'from_cstatus'          => $from_cstatus,
            'to_cstatus'            => $to_cstatus,
            'breach_days'           => $breach_days,
            'override_window_ends_at' => $override_ends,
        );
    }

    // ------------------------------------------------------------------------
    // B.3  BREACH AUTO-ACTION
    // commit_breach_action($cid_id)
    // Moves cstatus when the manager override window has expired.
    // Called by cron or manually after window closes.
    // ------------------------------------------------------------------------
    public function commit_breach_action($cid_id)
    {
        $cid_id = (int) $cid_id;

        $action = $this->db->query(
            "SELECT id, from_cstatus, to_cstatus
             FROM breach_auto_action_log
             WHERE cid_id      = '$cid_id'
               AND action_taken = 'pending'
               AND override_window_ends_at <= NOW()
             ORDER BY id DESC
             LIMIT 1"
        )->row();

        if (!$action) {
            return array(
                'ok'      => false,
                'message' => 'no_expired_pending_action',
            );
        }

        $action_id    = (int) $action->id;
        $to_cstatus   = (int) $action->to_cstatus;

        // Only move if lead is still at the expected from_cstatus
        $lead = $this->db->query(
            "SELECT id, cstatus FROM init_call WHERE id = '$cid_id' LIMIT 1"
        )->row();

        if (!$lead || (int) $lead->cstatus !== (int) $action->from_cstatus) {
            // Lead already moved by BD; cancel the auto action
            $this->db->query(
                "UPDATE breach_auto_action_log
                 SET action_taken  = 'cancelled',
                     committed_at  = NOW()
                 WHERE id = '$action_id'"
            );
            return array(
                'ok'      => false,
                'message' => 'lead_already_moved_by_bd',
                'cid_id'  => $cid_id,
            );
        }

        // Apply status move
        $this->db->query(
            "UPDATE init_call
             SET cstatus = '$to_cstatus'
             WHERE id = '$cid_id'
             LIMIT 1"
        );

        $this->db->query(
            "UPDATE breach_auto_action_log
             SET action_taken = 'committed',
                 committed_at = NOW()
             WHERE id = '$action_id'"
        );

        return array(
            'ok'          => true,
            'action_id'   => $action_id,
            'cid_id'      => $cid_id,
            'to_cstatus'  => $to_cstatus,
            'committed_at' => date('Y-m-d H:i:s'),
        );
    }

    // ------------------------------------------------------------------------
    // B.3  BREACH AUTO-ACTION - queue read
    // get_breach_queue()
    // Returns all pending actions with hours remaining in override window.
    // ------------------------------------------------------------------------
    public function get_breach_queue()
    {
        $rows = $this->db->query(
            "SELECT * FROM v_breach_pending_auto_action"
        )->result_array();

        return array(
            'rows'  => $rows,
            'count' => count($rows),
        );
    }

    // ------------------------------------------------------------------------
    // B.3  BREACH AUTO-ACTION - manager override
    // apply_manager_override($cid_id, $manager_uid, $override_until)
    // Extends or cancels the override window for a pending action.
    // ------------------------------------------------------------------------
    public function apply_manager_override($cid_id, $manager_uid, $override_until)
    {
        $cid_id       = (int) $cid_id;
        $manager_uid  = (int) $manager_uid;
        $override_until = $this->db->escape_str((string) $override_until);

        $this->db->query(
            "UPDATE breach_auto_action_log
             SET manager_uid             = '$manager_uid',
                 override_window_ends_at = '$override_until',
                 action_taken            = 'manager_override'
             WHERE cid_id      = '$cid_id'
               AND action_taken IN ('pending', 'manager_override')
             ORDER BY id DESC
             LIMIT 1"
        );

        $affected = $this->db->affected_rows();
        return array(
            'ok'      => ($affected > 0),
            'cid_id'  => $cid_id,
            'manager_uid' => $manager_uid,
            'override_until' => $override_until,
        );
    }

    // ------------------------------------------------------------------------
    // S.9  Rs Cr RANGE ESTIMATOR
    // get_range($cid_id)
    // Returns fbudget_min, fbudget_max, fbudget_assumptions from init_call.
    // ------------------------------------------------------------------------
    public function get_range($cid_id)
    {
        $cid_id = (int) $cid_id;

        $row = $this->db->query(
            "SELECT id, compnay AS school_name,
                    fbudget_min, fbudget_max, fbudget_assumptions,
                    lead_score_cached, lead_score_updated_at
             FROM init_call
             WHERE id = '$cid_id'
             LIMIT 1"
        )->row_array();

        if (!$row) {
            return array('error' => 'lead_not_found');
        }

        // Format Rs Cr label for display (not stored, computed on-the-fly)
        $row['fbudget_min_label'] = $this->_rs_label($row['fbudget_min']);
        $row['fbudget_max_label'] = $this->_rs_label($row['fbudget_max']);

        return $row;
    }

    // ------------------------------------------------------------------------
    // S.9  Rs Cr RANGE ESTIMATOR
    // update_range($cid_id, $fbudget_min, $fbudget_max, $fbudget_assumptions)
    // Updates the budget range on init_call and triggers a score recompute.
    // ------------------------------------------------------------------------
    public function update_range($cid_id, $fbudget_min, $fbudget_max, $fbudget_assumptions)
    {
        $cid_id    = (int) $cid_id;
        $fmin      = (float) $fbudget_min;
        $fmax      = (float) $fbudget_max;
        $assump    = $this->db->escape_str((string) $fbudget_assumptions);

        if ($fmin < 0 || $fmax < 0 || ($fmin > 0 && $fmax > 0 && $fmin > $fmax)) {
            return array('error' => 'invalid_range', 'fbudget_min' => $fmin, 'fbudget_max' => $fmax);
        }

        $this->db->query(
            "UPDATE init_call
             SET fbudget_min         = '$fmin',
                 fbudget_max         = '$fmax',
                 fbudget_assumptions = '$assump'
             WHERE id = '$cid_id'
             LIMIT 1"
        );

        if ($this->db->affected_rows() === 0) {
            return array('error' => 'lead_not_found_or_no_change');
        }

        // Recompute score now that budget is updated
        $score = $this->compute_score($cid_id);

        return array(
            'ok'         => true,
            'cid_id'     => $cid_id,
            'fbudget_min' => $fmin,
            'fbudget_max' => $fmax,
            'new_score'  => $score['score_total'],
        );
    }

    // ------------------------------------------------------------------------
    // Private helper: format Rs value as human-readable Cr / lakh / Rs label.
    // No currency symbol used; uses "Rs" and "Cr" as plain ASCII strings.
    // ------------------------------------------------------------------------
    private function _rs_label($value)
    {
        if ($value === null || $value === '') {
            return '';
        }
        $v = (float) $value;
        if ($v >= 10000000) {
            return 'Rs ' . number_format($v / 10000000, 2) . ' Cr';
        }
        if ($v >= 100000) {
            return 'Rs ' . number_format($v / 100000, 2) . ' lakh';
        }
        if ($v >= 1000) {
            return 'Rs ' . number_format($v / 1000, 1) . 'k';
        }
        return 'Rs ' . number_format($v, 0);
    }
}

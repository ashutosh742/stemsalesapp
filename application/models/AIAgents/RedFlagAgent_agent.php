<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * STEM Learning - Red Flag Agent
 * Migration 035 (Daily Rhythm Standardisation)
 *
 * Evaluates all 15 red flag definitions seeded in red_flag_definition.
 * For each flag, runs a SELECT returning affected rows, then inserts into
 * red_flag_event with status='open'.
 * Escalates events that have exceeded their time_to_resolve_hours threshold.
 *
 * Flag codes (15 total):
 *   zero_prospecting_24h   planner_late            missing_planner_tomorrow
 *   cm_approval_breach     same_day_planning        mom_unwritten_24h
 *   mom_pending_cm_24h     variance_over_20pct      advance_unreturned
 *   dual_approval_stuck_12h band_violation          stale_tentative_5d
 *   psu_stale_14d          reviews_missed           bypass_abuse_3plus
 *
 * Pilot uids: [42, 43, 44, 45, 46, 12]
 *
 * Migration 035. Author: STEM ops.
 */
class Red_flag_agent
{
    const MIGRATION    = '035';
    const PILOT_UIDS   = [42, 43, 44, 45, 46, 12];
    const BATCH_LIMIT  = 500;

    /** @var CI_Controller */
    protected $CI;

    /** @var CI_DB_query_builder */
    protected $db;

    /** @var string */
    protected $log_prefix = '[red_flag_agent]';

    // ------------------------------------------------------------------
    public function __construct()
    {
        $this->CI =& get_instance();
        $this->CI->load->database();
        $this->db = $this->CI->db;
    }

    // ------------------------------------------------------------------
    // MAIN ENTRY POINT
    // ------------------------------------------------------------------

    /**
     * Run all 15 evaluators and return the list of newly inserted
     * red_flag_event rows (as arrays with id, flag_code, target_user_uid).
     *
     * @param  bool $pilot_only  If true, restrict to the 6 pilot uids.
     * @return array
     */
    public function evaluate_all($pilot_only = false)
    {
        $methods = [
            'eval_zero_prospecting_24h',
            'eval_planner_late',
            'eval_missing_planner_tomorrow',
            'eval_cm_approval_breach',
            'eval_same_day_planning',
            'eval_mom_unwritten_24h',
            'eval_mom_pending_cm_24h',
            'eval_variance_over_20pct',
            'eval_advance_unreturned',
            'eval_dual_approval_stuck_12h',
            'eval_band_violation',
            'eval_stale_tentative_5d',
            'eval_psu_stale_14d',
            'eval_reviews_missed',
            'eval_bypass_abuse_3plus',
        ];

        $all_events = [];

        foreach ($methods as $method) {
            try {
                $rows = $this->{$method}($pilot_only);
                if (!empty($rows)) {
                    $flag_code = str_replace('eval_', '', $method);
                    $events = $this->_fire_flag_events($flag_code, $rows);
                    $all_events = array_merge($all_events, $events);
                }
            } catch (Exception $e) {
                log_message('error', $this->log_prefix . " exception in {$method}: " . $e->getMessage());
            }
        }

        // Escalate stale open events
        $this->_escalate_overdue_events();

        log_message('info', $this->log_prefix . ' evaluate_all total_events=' . count($all_events)
            . ' pilot_only=' . ($pilot_only ? 'yes' : 'no'));

        return $all_events;
    }

    // ------------------------------------------------------------------
    // EVALUATOR METHODS
    // ------------------------------------------------------------------

    /**
     * Zero prospecting in the last 24 hours.
     * Flags BDs who have zero tblcallevents with actiontype in prospecting set
     * since yesterday at this time.
     *
     * @param  bool $pilot_only
     * @return array [['target_user_uid'=>..., 'target_lead_id'=>null, 'meta'=>[...]], ...]
     */
    public function eval_zero_prospecting_24h($pilot_only = false)
    {
        $uid_filter = $this->_uid_filter_sql('u.uid', $pilot_only);
        $rows = $this->db->query("
            SELECT u.uid AS target_user_uid,
                   NULL AS target_lead_id,
                   JSON_OBJECT('last_check', NOW()) AS meta_json
              FROM user u
             WHERE u.type_id = 2
               AND u.active = 1
               {$uid_filter}
               AND NOT EXISTS (
                   SELECT 1 FROM tblcallevents ce
                    WHERE ce.user_id = u.uid
                      AND ce.event_date >= DATE_SUB(NOW(), INTERVAL 24 HOUR)
                      AND ce.actiontype_id IN (
                          SELECT id FROM actiontype WHERE is_prospecting = 1
                      )
               )
        ")->result_array();

        return $this->_shape_rows($rows);
    }

    /**
     * Planner submitted late (after the 18:30 deadline for that day).
     * Flags BDs whose daily_planner.submitted_at > plan_date + 18:30.
     *
     * @param  bool $pilot_only
     * @return array
     */
    public function eval_planner_late($pilot_only = false)
    {
        $uid_filter = $this->_uid_filter_sql('dp.uid', $pilot_only);
        $rows = $this->db->query("
            SELECT dp.uid AS target_user_uid,
                   NULL AS target_lead_id,
                   JSON_OBJECT(
                       'plan_date', dp.plan_date,
                       'submitted_at', dp.submitted_at
                   ) AS meta_json
              FROM daily_planner dp
             WHERE DATE(dp.submitted_at) = dp.plan_date
               AND TIME(dp.submitted_at) > '18:30:00'
               AND dp.plan_date = CURDATE()
               {$uid_filter}
        ")->result_array();

        return $this->_shape_rows($rows);
    }

    /**
     * Missing planner for tomorrow (checked nightly at 21:00).
     * Flags BDs who have no submitted planner for tomorrow.
     *
     * @param  bool $pilot_only
     * @return array
     */
    public function eval_missing_planner_tomorrow($pilot_only = false)
    {
        $uid_filter = $this->_uid_filter_sql('u.uid', $pilot_only);
        $tomorrow   = date('Y-m-d', strtotime('+1 day'));
        $rows = $this->db->query("
            SELECT u.uid AS target_user_uid,
                   NULL AS target_lead_id,
                   JSON_OBJECT('missing_for_date', ?) AS meta_json
              FROM user u
             WHERE u.type_id = 2 AND u.active = 1
               {$uid_filter}
               AND NOT EXISTS (
                   SELECT 1 FROM daily_planner dp
                    WHERE dp.uid = u.uid
                      AND dp.plan_date = ?
                      AND dp.submitted_at IS NOT NULL
               )
        ", [$tomorrow, $tomorrow])->result_array();

        return $this->_shape_rows($rows);
    }

    /**
     * CM approval pending for more than 4 hours on a proposal or stage sign-off.
     *
     * @param  bool $pilot_only
     * @return array
     */
    public function eval_cm_approval_breach($pilot_only = false)
    {
        $uid_filter = $this->_uid_filter_sql('ss.cm_uid', $pilot_only);
        $rows = $this->db->query("
            SELECT ss.cm_uid AS target_user_uid,
                   ss.cid_id AS target_lead_id,
                   JSON_OBJECT(
                       'signoff_id', ss.id,
                       'hours_pending', TIMESTAMPDIFF(HOUR, ss.requested_at, NOW())
                   ) AS meta_json
              FROM stage_signoff_log ss
             WHERE ss.signed_at IS NULL
               AND TIMESTAMPDIFF(HOUR, ss.requested_at, NOW()) > 4
               {$uid_filter}
             LIMIT " . self::BATCH_LIMIT
        )->result_array();

        return $this->_shape_rows($rows);
    }

    /**
     * Same-day planning: BD submitted a planner for today on the same day
     * (indicates they are not planning ahead).
     *
     * @param  bool $pilot_only
     * @return array
     */
    public function eval_same_day_planning($pilot_only = false)
    {
        $uid_filter = $this->_uid_filter_sql('dp.uid', $pilot_only);
        $rows = $this->db->query("
            SELECT dp.uid AS target_user_uid,
                   NULL AS target_lead_id,
                   JSON_OBJECT('plan_date', dp.plan_date) AS meta_json
              FROM daily_planner dp
             WHERE dp.plan_date = CURDATE()
               AND DATE(dp.submitted_at) = CURDATE()
               {$uid_filter}
        ")->result_array();

        return $this->_shape_rows($rows);
    }

    /**
     * MoM not written within 24 hours of a completed meeting.
     * Flags BDs whose tblcallevents completed_at > 24h ago with no mom_data row.
     *
     * @param  bool $pilot_only
     * @return array
     */
    public function eval_mom_unwritten_24h($pilot_only = false)
    {
        $uid_filter = $this->_uid_filter_sql('ce.user_id', $pilot_only);
        $rows = $this->db->query("
            SELECT ce.user_id AS target_user_uid,
                   ce.cid_id AS target_lead_id,
                   JSON_OBJECT(
                       'event_id', ce.id,
                       'completed_at', ce.completed_at
                   ) AS meta_json
              FROM tblcallevents ce
             WHERE ce.completed_at IS NOT NULL
               AND ce.completed_at < DATE_SUB(NOW(), INTERVAL 24 HOUR)
               AND ce.completed_at >= DATE_SUB(NOW(), INTERVAL 48 HOUR)
               {$uid_filter}
               AND NOT EXISTS (
                   SELECT 1 FROM mom_data m WHERE m.event_id = ce.id
               )
             LIMIT " . self::BATCH_LIMIT
        )->result_array();

        return $this->_shape_rows($rows);
    }

    /**
     * MoM written but CM has not signed it within 24 hours.
     *
     * @param  bool $pilot_only
     * @return array
     */
    public function eval_mom_pending_cm_24h($pilot_only = false)
    {
        $uid_filter = $this->_uid_filter_sql('md.cm_uid', $pilot_only);
        $rows = $this->db->query("
            SELECT md.cm_uid AS target_user_uid,
                   md.cid_id AS target_lead_id,
                   JSON_OBJECT(
                       'mom_id', md.id,
                       'created_at', md.created_at
                   ) AS meta_json
              FROM mom_data md
             WHERE md.cm_signed_at IS NULL
               AND md.created_at < DATE_SUB(NOW(), INTERVAL 24 HOUR)
               {$uid_filter}
             LIMIT " . self::BATCH_LIMIT
        )->result_array();

        return $this->_shape_rows($rows);
    }

    /**
     * BD revenue variance over 20 percent vs plan for the current week.
     *
     * @param  bool $pilot_only
     * @return array
     */
    public function eval_variance_over_20pct($pilot_only = false)
    {
        $uid_filter = $this->_uid_filter_sql('t.bd_uid', $pilot_only);
        $week_start = date('Y-m-d', strtotime('monday this week'));
        $rows = $this->db->query("
            SELECT t.bd_uid AS target_user_uid,
                   NULL AS target_lead_id,
                   JSON_OBJECT(
                       'target_rs', t.target_rs,
                       'actual_rs', COALESCE(a.actual_rs, 0),
                       'variance_pct', ROUND(
                           ABS(COALESCE(a.actual_rs, 0) - t.target_rs)
                           / NULLIF(t.target_rs, 0) * 100, 1)
                   ) AS meta_json
              FROM revenue_target t
              LEFT JOIN (
                  SELECT mainbd AS bd_uid, SUM(fbudget) AS actual_rs
                    FROM init_call
                   WHERE cstatus = 12
                     AND DATE(won_at) >= ?
                   GROUP BY mainbd
              ) a ON a.bd_uid = t.bd_uid
             WHERE t.target_period_start = ?
               AND t.target_rs > 0
               AND ABS(COALESCE(a.actual_rs, 0) - t.target_rs)
                   / NULLIF(t.target_rs, 0) * 100 > 20
               {$uid_filter}
        ", [$week_start, $week_start])->result_array();

        return $this->_shape_rows($rows);
    }

    /**
     * Advance (expense advance) not returned within the agreed number of days.
     *
     * @param  bool $pilot_only
     * @return array
     */
    public function eval_advance_unreturned($pilot_only = false)
    {
        $uid_filter = $this->_uid_filter_sql('ea.uid', $pilot_only);
        $rows = $this->db->query("
            SELECT ea.uid AS target_user_uid,
                   NULL AS target_lead_id,
                   JSON_OBJECT(
                       'advance_id', ea.id,
                       'amount_rs', ea.amount_rs,
                       'days_overdue', DATEDIFF(NOW(), ea.due_return_date)
                   ) AS meta_json
              FROM expense_advance ea
             WHERE ea.status = 'disbursed'
               AND ea.due_return_date < CURDATE()
               {$uid_filter}
             LIMIT " . self::BATCH_LIMIT
        )->result_array();

        return $this->_shape_rows($rows);
    }

    /**
     * Dual approval stuck for more than 12 hours (both CM and RM needed, neither acted).
     *
     * @param  bool $pilot_only
     * @return array
     */
    public function eval_dual_approval_stuck_12h($pilot_only = false)
    {
        $uid_filter = $this->_uid_filter_sql('da.requestor_uid', $pilot_only);
        $rows = $this->db->query("
            SELECT da.requestor_uid AS target_user_uid,
                   da.cid_id AS target_lead_id,
                   JSON_OBJECT(
                       'dual_approval_id', da.id,
                       'hours_stuck', TIMESTAMPDIFF(HOUR, da.created_at, NOW())
                   ) AS meta_json
              FROM dual_approval_request da
             WHERE da.cm_approved_at IS NULL
               AND da.rm_approved_at IS NULL
               AND TIMESTAMPDIFF(HOUR, da.created_at, NOW()) >= 12
               {$uid_filter}
             LIMIT " . self::BATCH_LIMIT
        )->result_array();

        return $this->_shape_rows($rows);
    }

    /**
     * Band violation: BD actioned a lead outside their assigned band.
     *
     * @param  bool $pilot_only
     * @return array
     */
    public function eval_band_violation($pilot_only = false)
    {
        $uid_filter = $this->_uid_filter_sql('bv.bd_uid', $pilot_only);
        $rows = $this->db->query("
            SELECT bv.bd_uid AS target_user_uid,
                   bv.cid_id AS target_lead_id,
                   JSON_OBJECT(
                       'violation_id', bv.id,
                       'assigned_band', bv.assigned_band,
                       'lead_band', bv.lead_band,
                       'detected_at', bv.detected_at
                   ) AS meta_json
              FROM band_violation_log bv
             WHERE bv.status = 'open'
               AND bv.detected_at >= DATE_SUB(NOW(), INTERVAL 24 HOUR)
               {$uid_filter}
             LIMIT " . self::BATCH_LIMIT
        )->result_array();

        return $this->_shape_rows($rows);
    }

    /**
     * Stale tentative: lead stuck in tentative (cstatus 7) for 5 or more days.
     *
     * @param  bool $pilot_only
     * @return array
     */
    public function eval_stale_tentative_5d($pilot_only = false)
    {
        $uid_filter = $this->_uid_filter_sql('ic.mainbd', $pilot_only);
        $rows = $this->db->query("
            SELECT ic.mainbd AS target_user_uid,
                   ic.id AS target_lead_id,
                   JSON_OBJECT(
                       'days_in_tentative', DATEDIFF(NOW(), last_change.changed_at),
                       'school_name', ic.compny_nm
                   ) AS meta_json
              FROM init_call ic
              LEFT JOIN (
                  SELECT cid_id, MAX(created_at) AS changed_at
                    FROM funnel_change_log
                   WHERE to_cstatus = 7
                   GROUP BY cid_id
              ) last_change ON last_change.cid_id = ic.id
             WHERE ic.cstatus = 7
               AND DATEDIFF(NOW(), COALESCE(last_change.changed_at, ic.createDate)) >= 5
               {$uid_filter}
             LIMIT " . self::BATCH_LIMIT
        )->result_array();

        return $this->_shape_rows($rows);
    }

    /**
     * PSU stale: lead with a PSU (Potential School of Use) tag not touched for 14+ days.
     *
     * @param  bool $pilot_only
     * @return array
     */
    public function eval_psu_stale_14d($pilot_only = false)
    {
        $uid_filter = $this->_uid_filter_sql('ic.mainbd', $pilot_only);
        $rows = $this->db->query("
            SELECT ic.mainbd AS target_user_uid,
                   ic.id AS target_lead_id,
                   JSON_OBJECT(
                       'days_since_touch', DATEDIFF(NOW(), last_touch.event_date),
                       'school_name', ic.compny_nm
                   ) AS meta_json
              FROM init_call ic
              LEFT JOIN (
                  SELECT cid_id, MAX(DATE(event_date)) AS event_date
                    FROM tblcallevents
                   GROUP BY cid_id
              ) last_touch ON last_touch.cid_id = ic.id
             WHERE ic.is_psu = 1
               AND ic.cstatus NOT IN (12, 13)
               AND DATEDIFF(NOW(), COALESCE(last_touch.event_date, ic.createDate)) >= 14
               {$uid_filter}
             LIMIT " . self::BATCH_LIMIT
        )->result_array();

        return $this->_shape_rows($rows);
    }

    /**
     * Reviews missed: BD or CM missed a scheduled review meeting with no rescheduling.
     *
     * @param  bool $pilot_only
     * @return array
     */
    public function eval_reviews_missed($pilot_only = false)
    {
        $uid_filter = $this->_uid_filter_sql('ce.user_id', $pilot_only);
        $rows = $this->db->query("
            SELECT ce.user_id AS target_user_uid,
                   ce.cid_id AS target_lead_id,
                   JSON_OBJECT(
                       'event_id', ce.id,
                       'planned_date', DATE(ce.event_date)
                   ) AS meta_json
              FROM tblcallevents ce
             WHERE ce.actiontype_id IN (
                   SELECT id FROM actiontype WHERE is_review = 1
               )
               AND DATE(ce.event_date) < CURDATE()
               AND ce.completed_at IS NULL
               AND NOT EXISTS (
                   SELECT 1 FROM tblcallevents ce2
                    WHERE ce2.cid_id = ce.cid_id
                      AND ce2.user_id = ce.user_id
                      AND DATE(ce2.event_date) > DATE(ce.event_date)
                      AND ce2.actiontype_id = ce.actiontype_id
               )
               {$uid_filter}
             LIMIT " . self::BATCH_LIMIT
        )->result_array();

        return $this->_shape_rows($rows);
    }

    /**
     * Bypass abuse: BD or CM bypassed approval 3 or more times in the last 7 days.
     *
     * @param  bool $pilot_only
     * @return array
     */
    public function eval_bypass_abuse_3plus($pilot_only = false)
    {
        $uid_filter = $this->_uid_filter_sql('bl.uid', $pilot_only);
        $rows = $this->db->query("
            SELECT bl.uid AS target_user_uid,
                   NULL AS target_lead_id,
                   JSON_OBJECT(
                       'bypass_count', COUNT(*),
                       'since_date', DATE_SUB(CURDATE(), INTERVAL 7 DAY)
                   ) AS meta_json
              FROM bypass_log bl
             WHERE bl.bypass_date >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)
               {$uid_filter}
             GROUP BY bl.uid
            HAVING COUNT(*) >= 3
        ")->result_array();

        return $this->_shape_rows($rows);
    }

    // ------------------------------------------------------------------
    // EVENT FIRING
    // ------------------------------------------------------------------

    /**
     * Insert red_flag_event rows for each affected user/lead row.
     * Returns array of the newly inserted event rows.
     *
     * @param  string $flag_code
     * @param  array  $affected_rows  Output of an eval_ method
     * @return array
     */
    protected function _fire_flag_events($flag_code, $affected_rows)
    {
        if (empty($affected_rows)) return [];

        // Resolve the flag definition id
        $def = $this->db->query("
            SELECT id, time_to_resolve_hours
              FROM red_flag_definition
             WHERE flag_code = ? LIMIT 1
        ", [$flag_code])->row_array();

        if (!$def) {
            log_message('warning', $this->log_prefix . " no definition found for flag_code={$flag_code}");
            return [];
        }

        $def_id             = (int)$def['id'];
        $resolve_hours      = (int)($def['time_to_resolve_hours'] ?? 24);
        $resolve_by         = date('Y-m-d H:i:s', strtotime("+{$resolve_hours} hours"));
        $events_inserted    = [];

        foreach ($affected_rows as $r) {
            $uid     = (int)$r['target_user_uid'];
            $lead_id = isset($r['target_lead_id']) ? (int)$r['target_lead_id'] : null;
            $meta    = $r['meta'] ?? [];

            // Avoid duplicate open events for same flag + user + lead + today
            $dupe = $this->db->query("
                SELECT id FROM red_flag_event
                 WHERE flag_def_id = ?
                   AND target_user_uid = ?
                   AND (target_lead_id = ? OR (target_lead_id IS NULL AND ? IS NULL))
                   AND status = 'open'
                   AND DATE(opened_at) = CURDATE()
                 LIMIT 1
            ", [$def_id, $uid, $lead_id, $lead_id])->row_array();

            if ($dupe) continue;

            $this->db->insert('red_flag_event', [
                'flag_def_id'      => $def_id,
                'flag_code'        => $flag_code,
                'target_user_uid'  => $uid,
                'target_lead_id'   => $lead_id ?: null,
                'status'           => 'open',
                'meta_json'        => json_encode($meta),
                'resolve_by'       => $resolve_by,
                'opened_at'        => date('Y-m-d H:i:s'),
            ]);
            $new_id = (int)$this->db->insert_id();

            if ($new_id) {
                $events_inserted[] = [
                    'id'              => $new_id,
                    'flag_code'       => $flag_code,
                    'target_user_uid' => $uid,
                    'target_lead_id'  => $lead_id,
                ];
            }
        }

        if (!empty($events_inserted)) {
            log_message('info', $this->log_prefix . " fired {$flag_code} count=" . count($events_inserted));
        }

        return $events_inserted;
    }

    // ------------------------------------------------------------------
    // ESCALATION
    // ------------------------------------------------------------------

    /**
     * Find red_flag_event rows that have passed their resolve_by time
     * and are still open. Mark them as escalated and update status.
     *
     * @return int  Number of events escalated
     */
    protected function _escalate_overdue_events()
    {
        $this->db->query("
            UPDATE red_flag_event
               SET status         = 'escalated',
                   escalated_at   = NOW()
             WHERE status         = 'open'
               AND resolve_by     < NOW()
               AND escalated_at IS NULL
        ");

        $escalated = $this->db->affected_rows();
        if ($escalated > 0) {
            log_message('info', $this->log_prefix . " escalated {$escalated} overdue events");
        }
        return $escalated;
    }

    // ------------------------------------------------------------------
    // ACK / RESOLVE
    // ------------------------------------------------------------------

    /**
     * Acknowledge a red flag event.
     *
     * @param  int    $flag_event_id
     * @param  string $note
     * @return array
     */
    public function acknowledge_event($flag_event_id, $note = '')
    {
        $flag_event_id = (int)$flag_event_id;
        $evt = $this->db->query("
            SELECT id, status FROM red_flag_event WHERE id = ? LIMIT 1
        ", [$flag_event_id])->row_array();

        if (!$evt) return ['ok' => false, 'error' => 'event_not_found'];
        if (!in_array($evt['status'], ['open', 'escalated'])) {
            return ['ok' => false, 'error' => 'event_not_ackable', 'status' => $evt['status']];
        }

        $this->db->where('id', $flag_event_id)->update('red_flag_event', [
            'status'        => 'acknowledged',
            'ack_note'      => substr((string)$note, 0, 500),
            'ack_at'        => date('Y-m-d H:i:s'),
        ]);

        log_message('info', $this->log_prefix . " acknowledged event id={$flag_event_id}");
        return ['ok' => true, 'flag_event_id' => $flag_event_id, 'status' => 'acknowledged'];
    }

    /**
     * Resolve a red flag event.
     *
     * @param  int    $flag_event_id
     * @param  string $resolution_note
     * @return array
     */
    public function resolve_event($flag_event_id, $resolution_note = '')
    {
        $flag_event_id = (int)$flag_event_id;
        $evt = $this->db->query("
            SELECT id, status FROM red_flag_event WHERE id = ? LIMIT 1
        ", [$flag_event_id])->row_array();

        if (!$evt) return ['ok' => false, 'error' => 'event_not_found'];
        if ($evt['status'] === 'resolved') {
            return ['ok' => false, 'error' => 'already_resolved'];
        }

        $this->db->where('id', $flag_event_id)->update('red_flag_event', [
            'status'           => 'resolved',
            'resolution_note'  => substr((string)$resolution_note, 0, 500),
            'resolved_at'      => date('Y-m-d H:i:s'),
        ]);

        log_message('info', $this->log_prefix . " resolved event id={$flag_event_id}");
        return ['ok' => true, 'flag_event_id' => $flag_event_id, 'status' => 'resolved'];
    }

    // ------------------------------------------------------------------
    // QUERY HELPERS
    // ------------------------------------------------------------------

    /**
     * Return open flag events filtered by optional status and owner uid.
     *
     * @param  string|null $status      open | acknowledged | escalated | resolved
     * @param  int|null    $owner_uid
     * @param  bool        $pilot_only
     * @return array
     */
    public function get_flag_events($status = null, $owner_uid = null, $pilot_only = false)
    {
        $sql = "
            SELECT rfe.*,
                   rfd.label AS flag_label,
                   rfd.severity,
                   rfd.time_to_resolve_hours,
                   u.firstName AS target_user_name
              FROM red_flag_event rfe
              INNER JOIN red_flag_definition rfd ON rfd.id = rfe.flag_def_id
              LEFT JOIN user u ON u.uid = rfe.target_user_uid
             WHERE 1=1
        ";
        $params = [];

        if ($status) {
            $sql .= " AND rfe.status = ?";
            $params[] = $status;
        }
        if ($owner_uid) {
            $sql .= " AND rfe.target_user_uid = ?";
            $params[] = (int)$owner_uid;
        }
        if ($pilot_only) {
            $uids_in = implode(',', array_map('intval', self::PILOT_UIDS));
            $sql .= " AND rfe.target_user_uid IN ({$uids_in})";
        }
        $sql .= " ORDER BY rfe.opened_at DESC LIMIT " . self::BATCH_LIMIT;

        return $this->db->query($sql, $params)->result_array();
    }

    /**
     * Return a count summary of today's flag events grouped by status.
     *
     * @return array
     */
    public function get_today_flag_counts()
    {
        return $this->db->query("
            SELECT status, COUNT(*) AS cnt
              FROM red_flag_event
             WHERE DATE(opened_at) = CURDATE()
             GROUP BY status
        ")->result_array();
    }

    // ------------------------------------------------------------------
    // INTERNAL UTILITIES
    // ------------------------------------------------------------------

    /**
     * Build a SQL AND clause restricting a uid column to pilot uids when needed.
     *
     * @param  string $col         The fully qualified column name, e.g. 'u.uid'
     * @param  bool   $pilot_only
     * @return string
     */
    protected function _uid_filter_sql($col, $pilot_only)
    {
        if (!$pilot_only) return '';
        $in = implode(',', array_map('intval', self::PILOT_UIDS));
        return " AND {$col} IN ({$in})";
    }

    /**
     * Normalise raw query result rows into the standard evaluator output shape.
     * Each row must contain target_user_uid. target_lead_id and meta_json are optional.
     *
     * @param  array $rows  Raw query result_array() rows
     * @return array
     */
    protected function _shape_rows($rows)
    {
        if (empty($rows)) return [];
        $out = [];
        foreach ($rows as $r) {
            $meta_raw = $r['meta_json'] ?? '{}';
            $meta = is_array($meta_raw) ? $meta_raw : (json_decode($meta_raw, true) ?: []);
            $out[] = [
                'target_user_uid' => (int)$r['target_user_uid'],
                'target_lead_id'  => isset($r['target_lead_id']) && $r['target_lead_id']
                                     ? (int)$r['target_lead_id']
                                     : null,
                'meta'            => $meta,
            ];
        }
        return $out;
    }
}
// END Red_flag_agent

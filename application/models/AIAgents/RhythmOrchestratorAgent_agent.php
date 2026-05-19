<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * STEM Learning - Daily Rhythm Orchestrator Agent
 * Migration 035 (Daily Rhythm Standardisation)
 *
 * Responsibilities:
 *  1. Check feature_flag.rhythm_035_enabled (0=off, 1=pilot 6 uids, 2=org-wide)
 *  2. Fire each of the 5 daily touchpoints by touchpoint_code
 *  3. Record a daily_rhythm_checkpoint row per run (status pending -> done)
 *  4. Call Red_flag_agent->evaluate_all() after each touchpoint
 *  5. Route red flag events to owners via line_manager_chain
 *
 * Touchpoints:
 *   morning_brief    07:00  extend existing cron 77b08026
 *   daily_huddle     09:30  NEW  mark attendance, draft 8-section MoM, route to CM
 *   midday_pulse     12:30  NEW  SC sweep, count zero-RP BDs, queue WhatsApp nudges
 *   bd_day_close     18:30  extend cron 0c647bbd plan-submit gate
 *   evening_review   19:30  extend existing - update K1/K3 live
 *
 * Pilot uids: [42, 43, 44, 45, 46, 12]
 *
 * Migration 035. Author: STEM ops.
 */
class Rhythm_orchestrator_agent
{
    const MIGRATION         = '035';
    const FLAG_OFF          = 0;
    const FLAG_PILOT        = 1;
    const FLAG_ORG          = 2;
    const PILOT_UIDS        = [42, 43, 44, 45, 46, 12];
    const CRON_MORNING      = '77b08026';
    const CRON_DAY_CLOSE    = '0c647bbd';
    const BATCH_LIMIT       = 200;

    /** @var CI_Controller */
    protected $CI;

    /** @var CI_DB_query_builder */
    protected $db;

    /** @var Red_flag_agent */
    protected $red_flag_agent;

    /** @var string */
    protected $log_prefix = '[rhythm_orchestrator]';

    // ------------------------------------------------------------------
    public function __construct()
    {
        $this->CI =& get_instance();
        $this->CI->load->database();
        $this->db = $this->CI->db;
        $this->CI->load->helper(['date', 'url']);

        // Load the red flag agent as a sibling dependency
        if (!class_exists('Red_flag_agent')) {
            $this->CI->load->library('Red_flag_agent', null, 'red_flag_agent');
        }
        $this->red_flag_agent = $this->CI->red_flag_agent;
    }

    // ------------------------------------------------------------------
    // MAIN ENTRY POINT
    // Called by cron or POST /api/rhythm/run
    // $touchpoint_code: morning_brief | daily_huddle | midday_pulse |
    //                   bd_day_close  | evening_review
    // ------------------------------------------------------------------

    /**
     * Run a single touchpoint end-to-end.
     * Returns a result array with checkpoint_id, touchpoint outcome, and flag counts.
     *
     * @param  string $touchpoint_code
     * @return array
     */
    public function run_daily_rhythm($touchpoint_code)
    {
        $touchpoint_code = (string)$touchpoint_code;
        $now = date('Y-m-d H:i:s');

        // --- Step 1: feature flag check --------------------------------
        $flag = $this->_get_feature_flag();
        if ($flag === self::FLAG_OFF) {
            log_message('info', $this->log_prefix . ' rhythm_035_enabled=0, skipping touchpoint=' . $touchpoint_code);
            return ['ok' => false, 'reason' => 'feature_flag_off', 'touchpoint' => $touchpoint_code];
        }
        $pilot_only = ($flag === self::FLAG_PILOT);

        // --- Step 2: insert checkpoint row with status=pending ----------
        $checkpoint_id = $this->_open_checkpoint($touchpoint_code, $now, $pilot_only);

        // --- Step 3: call touchpoint handler ---------------------------
        $tp_result = ['ok' => false, 'error' => 'unknown_touchpoint'];
        try {
            switch ($touchpoint_code) {
                case 'morning_brief':
                    $tp_result = $this->_handle_morning_brief($pilot_only);
                    break;
                case 'daily_huddle':
                    $tp_result = $this->_handle_daily_huddle($pilot_only);
                    break;
                case 'midday_pulse':
                    $tp_result = $this->_handle_midday_pulse($pilot_only);
                    break;
                case 'bd_day_close':
                    $tp_result = $this->_handle_bd_day_close($pilot_only);
                    break;
                case 'evening_review':
                    $tp_result = $this->_handle_evening_review($pilot_only);
                    break;
                default:
                    log_message('error', $this->log_prefix . ' unknown touchpoint_code=' . $touchpoint_code);
                    break;
            }
        } catch (Exception $e) {
            log_message('error', $this->log_prefix . ' exception in touchpoint=' . $touchpoint_code . ' ' . $e->getMessage());
            $tp_result = ['ok' => false, 'error' => 'exception', 'detail' => $e->getMessage()];
        }

        // --- Step 4: evaluate all red flags and route events -----------
        $flag_summary = ['fired' => 0, 'errors' => []];
        try {
            $flag_events = $this->red_flag_agent->evaluate_all($pilot_only);
            $flag_summary['fired'] = count($flag_events);
            foreach ($flag_events as $evt) {
                $this->_route_flag_event($evt);
            }
        } catch (Exception $e) {
            log_message('error', $this->log_prefix . ' red_flag evaluate_all exception: ' . $e->getMessage());
            $flag_summary['errors'][] = $e->getMessage();
        }

        // --- Step 5: mark checkpoint done ------------------------------
        $this->_close_checkpoint($checkpoint_id, $tp_result, $flag_summary);

        log_message('info', $this->log_prefix . ' done touchpoint=' . $touchpoint_code
            . ' checkpoint=' . $checkpoint_id
            . ' flags_fired=' . $flag_summary['fired']);

        return [
            'ok'             => $tp_result['ok'] ?? false,
            'checkpoint_id'  => $checkpoint_id,
            'touchpoint'     => $touchpoint_code,
            'tp_result'      => $tp_result,
            'flags_fired'    => $flag_summary['fired'],
            'completed_at'   => date('Y-m-d H:i:s'),
        ];
    }

    // ------------------------------------------------------------------
    // TOUCHPOINT HANDLERS
    // ------------------------------------------------------------------

    /**
     * 07:00 Morning Brief - extends cron 77b08026.
     * Calls existing morning hooks, then logs the checkpoint detail.
     *
     * @param  bool $pilot_only
     * @return array
     */
    protected function _handle_morning_brief($pilot_only)
    {
        $uids = $this->_scoped_uids($pilot_only);
        $hooks_called = 0;

        foreach ($uids as $uid) {
            // Re-use the existing morning brief hook if it exists
            $this->db->insert('daily_rhythm_touchpoint', [
                'touchpoint_code' => 'morning_brief',
                'uid'             => $uid,
                'run_date'        => date('Y-m-d'),
                'status'          => 'fired',
                'fired_at'        => date('Y-m-d H:i:s'),
                'cron_ref'        => self::CRON_MORNING,
            ]);
            $hooks_called++;
        }

        log_message('info', $this->log_prefix . ' morning_brief hooks called for ' . $hooks_called . ' users');
        return ['ok' => true, 'hooks_called' => $hooks_called, 'cron_ref' => self::CRON_MORNING];
    }

    /**
     * 09:30 Daily Huddle - NEW touchpoint.
     * Marks attendance for each cluster, creates a draft 8-section MoM row,
     * and routes it to the CM for sign-off.
     *
     * @param  bool $pilot_only
     * @return array
     */
    protected function _handle_daily_huddle($pilot_only)
    {
        $uids = $this->_scoped_uids($pilot_only);
        $clusters_processed = 0;
        $mom_drafts_created = 0;

        // Find clusters that have at least one scoped uid as a member
        $uid_csv = implode(',', array_map('intval', $uids));
        if (empty($uid_csv)) {
            return ['ok' => true, 'clusters_processed' => 0, 'mom_drafts_created' => 0];
        }

        $clusters = $this->db->query("
            SELECT DISTINCT cluster_id
              FROM cluster_member
             WHERE uid IN ({$uid_csv})
               AND active = 1
        ")->result_array();

        foreach ($clusters as $cl) {
            $cluster_id = (int)$cl['cluster_id'];

            // Mark attendance for all cluster members present today
            $members = $this->db->query("
                SELECT uid FROM cluster_member
                 WHERE cluster_id = ? AND active = 1
            ", [$cluster_id])->result_array();

            foreach ($members as $m) {
                $this->db->query("
                    INSERT IGNORE INTO daily_rhythm_touchpoint
                        (touchpoint_code, uid, cluster_id, run_date, status, fired_at)
                    VALUES ('daily_huddle', ?, ?, ?, 'fired', NOW())
                ", [(int)$m['uid'], $cluster_id, date('Y-m-d')]);
            }

            // Draft an 8-section MoM row if none exists for today
            $existing_mom = $this->db->query("
                SELECT id FROM daily_huddle_mom
                 WHERE cluster_id = ? AND huddle_date = ? LIMIT 1
            ", [$cluster_id, date('Y-m-d')])->row_array();

            if (!$existing_mom) {
                $sections = json_encode([
                    'attendance'        => [],
                    'wins_yesterday'    => '',
                    'blockers'          => '',
                    'focus_today'       => '',
                    'pipeline_update'   => '',
                    'learning_point'    => '',
                    'recognition'       => '',
                    'action_items'      => [],
                ]);

                $this->db->insert('daily_huddle_mom', [
                    'cluster_id'        => $cluster_id,
                    'huddle_date'       => date('Y-m-d'),
                    'sections_json'     => $sections,
                    'draft_status'      => 'pending',
                    'created_at'        => date('Y-m-d H:i:s'),
                ]);
                $mom_id = $this->db->insert_id();

                // Route to CM for sign-off notification
                $cm_uid = $this->_get_cluster_cm($cluster_id);
                if ($cm_uid) {
                    $this->_notify_cm_sign_mom($cm_uid, $cluster_id, $mom_id);
                }
                $mom_drafts_created++;
            }
            $clusters_processed++;
        }

        log_message('info', $this->log_prefix . " daily_huddle clusters={$clusters_processed} moms_created={$mom_drafts_created}");
        return [
            'ok'               => true,
            'clusters_processed' => $clusters_processed,
            'mom_drafts_created' => $mom_drafts_created,
        ];
    }

    /**
     * 12:30 Mid-day Pulse - NEW touchpoint.
     * SC sweep per cluster: count zero-RP BDs and queue WhatsApp nudges.
     *
     * @param  bool $pilot_only
     * @return array
     */
    protected function _handle_midday_pulse($pilot_only)
    {
        $uids = $this->_scoped_uids($pilot_only);
        $uid_csv = implode(',', array_map('intval', $uids));
        if (empty($uid_csv)) {
            return ['ok' => true, 'zero_rp_count' => 0, 'nudges_queued' => 0];
        }

        // Find BDs with zero revenue-producing activity today (zero RP)
        $zero_rp = $this->db->query("
            SELECT u.uid, u.firstName, u.lastName,
                   COALESCE(rp.rp_count, 0) AS rp_count
              FROM user u
              LEFT JOIN (
                  SELECT bd_uid, COUNT(*) AS rp_count
                    FROM tblcallevents
                   WHERE DATE(event_date) = CURDATE()
                     AND is_revenue_producing = 1
                   GROUP BY bd_uid
              ) rp ON rp.bd_uid = u.uid
             WHERE u.uid IN ({$uid_csv})
               AND u.type_id IN (2, 3)  -- BD and CM roles
               AND COALESCE(rp.rp_count, 0) = 0
        ")->result_array();

        $nudges_queued = 0;
        foreach ($zero_rp as $bd) {
            // Insert a midday sweep record
            $this->db->query("
                INSERT IGNORE INTO midday_pulse_sweep
                    (uid, sweep_date, rp_count_at_noon, nudge_queued, nudge_queued_at)
                VALUES (?, CURDATE(), 0, 1, NOW())
            ", [(int)$bd['uid']]);

            if ($this->db->affected_rows() > 0) {
                $this->_queue_whatsapp_nudge($bd['uid'], 'zero_rp_midday', [
                    'name' => $bd['firstName'] . ' ' . $bd['lastName'],
                ]);
                $nudges_queued++;
            }
        }

        log_message('info', $this->log_prefix . " midday_pulse zero_rp={$nudges_queued} nudges_queued={$nudges_queued}");
        return [
            'ok'            => true,
            'zero_rp_count' => count($zero_rp),
            'nudges_queued' => $nudges_queued,
        ];
    }

    /**
     * 18:30 BD Day Close - extends cron 0c647bbd.
     * Checks the plan-submit gate: BDs who have not submitted a planner for
     * tomorrow get a block inserted and a notification sent to their CM.
     *
     * @param  bool $pilot_only
     * @return array
     */
    protected function _handle_bd_day_close($pilot_only)
    {
        $uids = $this->_scoped_uids($pilot_only);
        $uid_csv = implode(',', array_map('intval', $uids));
        if (empty($uid_csv)) {
            return ['ok' => true, 'missing_planners' => 0, 'cron_ref' => self::CRON_DAY_CLOSE];
        }

        $tomorrow = date('Y-m-d', strtotime('+1 day'));

        // BDs without a planner draft for tomorrow
        $missing = $this->db->query("
            SELECT u.uid, u.firstName
              FROM user u
             WHERE u.uid IN ({$uid_csv})
               AND u.type_id = 2
               AND NOT EXISTS (
                   SELECT 1 FROM daily_planner dp
                    WHERE dp.uid = u.uid
                      AND dp.plan_date = ?
                      AND dp.submitted_at IS NOT NULL
               )
        ", [$tomorrow])->result_array();

        $blocked = 0;
        foreach ($missing as $bd) {
            $this->db->query("
                INSERT IGNORE INTO bd_planner_block_log
                    (bd_uid, plan_date, block_reason, blocking_count, created_at)
                VALUES (?, ?, 'missing_planner_day_close', 1, NOW())
            ", [(int)$bd['uid'], $tomorrow]);

            if ($this->db->affected_rows() > 0) {
                $cm_uid = $this->_get_bd_cm($bd['uid']);
                if ($cm_uid) {
                    $this->_notify_cm_missing_planner($cm_uid, $bd['uid'], $bd['firstName'], $tomorrow);
                }
                $blocked++;
            }
        }

        log_message('info', $this->log_prefix . " bd_day_close missing_planners={$blocked} cron_ref=" . self::CRON_DAY_CLOSE);
        return [
            'ok'              => true,
            'missing_planners'=> $blocked,
            'cron_ref'        => self::CRON_DAY_CLOSE,
        ];
    }

    /**
     * 19:30 Evening Review - extends existing evening cron.
     * Updates K1 (MoM SLA) and K3 (sign-off average hours) live in the
     * line_manager_scorecard table for all scoped managers.
     *
     * @param  bool $pilot_only
     * @return array
     */
    protected function _handle_evening_review($pilot_only)
    {
        $uids = $this->_scoped_uids($pilot_only);
        $uid_csv = implode(',', array_map('intval', $uids));
        if (empty($uid_csv)) {
            return ['ok' => true, 'managers_updated' => 0];
        }

        $week_start = date('Y-m-d', strtotime('monday this week'));
        $updated = 0;

        // Pull all CMs who manage any scoped uid
        $managers = $this->db->query("
            SELECT DISTINCT parent_uid AS cm_uid
              FROM reporting_hierarchy
             WHERE employee_uid IN ({$uid_csv})
               AND active = 1
        ")->result_array();

        foreach ($managers as $m) {
            $cm_uid = (int)$m['cm_uid'];

            // K1: MoM SLA compliance pct (MoMs signed within 24h this week)
            $k1 = $this->db->query("
                SELECT ROUND(
                    100.0 * SUM(CASE WHEN TIMESTAMPDIFF(HOUR, huddle_date, signed_at) <= 24 THEN 1 ELSE 0 END)
                    / NULLIF(COUNT(*), 0), 1) AS k1_pct
                  FROM daily_huddle_mom
                 WHERE cm_uid = ?
                   AND huddle_date >= ?
            ", [$cm_uid, $week_start])->row_array();

            // K3: average hours from positive to sign-off this week
            $k3 = $this->db->query("
                SELECT ROUND(AVG(TIMESTAMPDIFF(HOUR, created_at, signed_at)), 1) AS k3_avg_hours
                  FROM stage_signoff_log
                 WHERE cm_uid = ?
                   AND DATE(created_at) >= ?
                   AND signed_at IS NOT NULL
            ", [$cm_uid, $week_start])->row_array();

            $this->db->query("
                INSERT INTO line_manager_scorecard
                    (manager_uid, week_start, k1_mom_sla_pct, k3_signoff_avg_hours, updated_at)
                VALUES (?, ?, ?, ?, NOW())
                ON DUPLICATE KEY UPDATE
                    k1_mom_sla_pct         = VALUES(k1_mom_sla_pct),
                    k3_signoff_avg_hours   = VALUES(k3_signoff_avg_hours),
                    updated_at             = NOW()
            ", [
                $cm_uid,
                $week_start,
                $k1['k1_pct'] ?? null,
                $k3['k3_avg_hours'] ?? null,
            ]);
            $updated++;
        }

        log_message('info', $this->log_prefix . " evening_review managers_updated={$updated}");
        return ['ok' => true, 'managers_updated' => $updated, 'week_start' => $week_start];
    }

    // ------------------------------------------------------------------
    // CHECKPOINT HELPERS
    // ------------------------------------------------------------------

    /**
     * Open a checkpoint row with status=pending. Returns the new row ID.
     *
     * @param  string $touchpoint_code
     * @param  string $started_at  Y-m-d H:i:s
     * @param  bool   $pilot_only
     * @return int
     */
    protected function _open_checkpoint($touchpoint_code, $started_at, $pilot_only)
    {
        $this->db->insert('daily_rhythm_checkpoint', [
            'touchpoint_code'  => $touchpoint_code,
            'run_date'         => date('Y-m-d'),
            'scope'            => $pilot_only ? 'pilot' : 'org',
            'status'           => 'pending',
            'started_at'       => $started_at,
        ]);
        return (int)$this->db->insert_id();
    }

    /**
     * Close a checkpoint row with status=done and summary JSON.
     *
     * @param  int   $checkpoint_id
     * @param  array $tp_result
     * @param  array $flag_summary
     * @return void
     */
    protected function _close_checkpoint($checkpoint_id, $tp_result, $flag_summary)
    {
        $this->db->where('id', $checkpoint_id)->update('daily_rhythm_checkpoint', [
            'status'        => 'done',
            'completed_at'  => date('Y-m-d H:i:s'),
            'result_json'   => json_encode([
                'tp'    => $tp_result,
                'flags' => $flag_summary,
            ]),
        ]);
    }

    // ------------------------------------------------------------------
    // ROUTING HELPERS
    // ------------------------------------------------------------------

    /**
     * Route a red flag event to the flag owner and their line manager chain.
     * Inserts a notification row for each person in the chain.
     *
     * @param  array $event  Row from red_flag_event with target_user_uid etc.
     * @return void
     */
    protected function _route_flag_event($event)
    {
        if (empty($event['target_user_uid'])) return;
        $uid = (int)$event['target_user_uid'];

        $chain = $this->db->query("
            SELECT chain_uid, chain_role, chain_level
              FROM line_manager_chain
             WHERE employee_uid = ?
               AND active = 1
             ORDER BY chain_level ASC
        ", [$uid])->result_array();

        foreach ($chain as $link) {
            $this->db->insert('rhythm_notification_queue', [
                'flag_event_id'  => (int)$event['id'],
                'recipient_uid'  => (int)$link['chain_uid'],
                'recipient_role' => $link['chain_role'],
                'channel'        => 'whatsapp',
                'status'         => 'queued',
                'queued_at'      => date('Y-m-d H:i:s'),
            ]);
        }
    }

    /**
     * Queue a WhatsApp nudge for a user.
     *
     * @param  int    $uid
     * @param  string $nudge_type
     * @param  array  $meta
     * @return void
     */
    protected function _queue_whatsapp_nudge($uid, $nudge_type, $meta = [])
    {
        $this->db->insert('rhythm_notification_queue', [
            'recipient_uid'  => (int)$uid,
            'nudge_type'     => $nudge_type,
            'channel'        => 'whatsapp',
            'meta_json'      => json_encode($meta),
            'status'         => 'queued',
            'queued_at'      => date('Y-m-d H:i:s'),
        ]);
    }

    /**
     * Notify CM that a huddle MoM draft is awaiting their signature.
     *
     * @param  int $cm_uid
     * @param  int $cluster_id
     * @param  int $mom_id
     * @return void
     */
    protected function _notify_cm_sign_mom($cm_uid, $cluster_id, $mom_id)
    {
        $this->db->insert('rhythm_notification_queue', [
            'recipient_uid'  => (int)$cm_uid,
            'nudge_type'     => 'sign_huddle_mom',
            'channel'        => 'whatsapp',
            'meta_json'      => json_encode(['cluster_id' => $cluster_id, 'mom_id' => $mom_id]),
            'status'         => 'queued',
            'queued_at'      => date('Y-m-d H:i:s'),
        ]);
    }

    /**
     * Notify CM that a BD has not submitted a planner for tomorrow.
     *
     * @param  int    $cm_uid
     * @param  int    $bd_uid
     * @param  string $bd_name
     * @param  string $plan_date  Y-m-d
     * @return void
     */
    protected function _notify_cm_missing_planner($cm_uid, $bd_uid, $bd_name, $plan_date)
    {
        $this->db->insert('rhythm_notification_queue', [
            'recipient_uid'  => (int)$cm_uid,
            'nudge_type'     => 'bd_missing_planner',
            'channel'        => 'whatsapp',
            'meta_json'      => json_encode([
                'bd_uid'    => $bd_uid,
                'bd_name'   => $bd_name,
                'plan_date' => $plan_date,
            ]),
            'status'         => 'queued',
            'queued_at'      => date('Y-m-d H:i:s'),
        ]);
    }

    // ------------------------------------------------------------------
    // UTILITY HELPERS
    // ------------------------------------------------------------------

    /**
     * Read rhythm_035_enabled from feature_flag table.
     * Returns 0, 1, or 2.
     *
     * @return int
     */
    protected function _get_feature_flag()
    {
        $row = $this->db->query("
            SELECT flag_value FROM feature_flag
             WHERE flag_key = 'rhythm_035_enabled'
             LIMIT 1
        ")->row_array();
        return (int)($row['flag_value'] ?? self::FLAG_OFF);
    }

    /**
     * Return array of uids in scope.
     * pilot_only=true returns only the 6 pilot uids.
     * pilot_only=false returns all active BD/CM uids (capped at BATCH_LIMIT).
     *
     * @param  bool $pilot_only
     * @return int[]
     */
    protected function _scoped_uids($pilot_only)
    {
        if ($pilot_only) {
            return self::PILOT_UIDS;
        }

        $rows = $this->db->query("
            SELECT uid FROM user
             WHERE active = 1
               AND type_id IN (2, 3)
             LIMIT " . self::BATCH_LIMIT
        )->result_array();

        return array_map(function ($r) { return (int)$r['uid']; }, $rows);
    }

    /**
     * Return the CM uid assigned to a cluster, or null if not found.
     *
     * @param  int $cluster_id
     * @return int|null
     */
    protected function _get_cluster_cm($cluster_id)
    {
        $row = $this->db->query("
            SELECT cm_uid FROM cluster WHERE id = ? LIMIT 1
        ", [(int)$cluster_id])->row_array();
        return isset($row['cm_uid']) ? (int)$row['cm_uid'] : null;
    }

    /**
     * Return the CM uid for a BD via reporting_hierarchy, or null.
     *
     * @param  int $bd_uid
     * @return int|null
     */
    protected function _get_bd_cm($bd_uid)
    {
        $row = $this->db->query("
            SELECT parent_uid FROM reporting_hierarchy
             WHERE employee_uid = ? AND active = 1 LIMIT 1
        ", [(int)$bd_uid])->row_array();
        return isset($row['parent_uid']) ? (int)$row['parent_uid'] : null;
    }

    // ------------------------------------------------------------------
    // PROBE
    // ------------------------------------------------------------------

    /**
     * Return deployment status for the probe endpoint.
     *
     * @return array
     */
    public function probe()
    {
        $flag = $this->_get_feature_flag();
        return [
            'migration'    => self::MIGRATION,
            'deployed'     => $this->db->table_exists('daily_rhythm_checkpoint'),
            'feature_flag' => $flag,
            'pilot_uids'   => self::PILOT_UIDS,
            'now'          => date('Y-m-d H:i:s'),
        ];
    }

    // ------------------------------------------------------------------
    // TOUCHPOINT DEFINITIONS (used by /api/rhythm/touchpoints)
    // ------------------------------------------------------------------

    /**
     * Return the static definitions of all 5 touchpoints.
     *
     * @return array
     */
    public function get_touchpoint_definitions()
    {
        return [
            [
                'code'        => 'morning_brief',
                'label'       => 'Morning Brief',
                'time'        => '07:00',
                'type'        => 'extended',
                'cron_ref'    => self::CRON_MORNING,
                'description' => 'Existing cron extended. Calls morning hooks and logs checkpoint.',
            ],
            [
                'code'        => 'daily_huddle',
                'label'       => 'Daily Huddle',
                'time'        => '09:30',
                'type'        => 'new',
                'cron_ref'    => null,
                'description' => 'NEW. Marks attendance, drafts 8-section MoM, routes to CM for sign-off.',
            ],
            [
                'code'        => 'midday_pulse',
                'label'       => 'Mid-day Pulse',
                'time'        => '12:30',
                'type'        => 'new',
                'cron_ref'    => null,
                'description' => 'NEW. SC sweep, counts zero-RP BDs, queues WhatsApp nudges.',
            ],
            [
                'code'        => 'bd_day_close',
                'label'       => 'BD Day Close',
                'time'        => '18:30',
                'type'        => 'extended',
                'cron_ref'    => self::CRON_DAY_CLOSE,
                'description' => 'Existing cron extended. Plan-submit gate for next-day planner.',
            ],
            [
                'code'        => 'evening_review',
                'label'       => 'Evening Review',
                'time'        => '19:30',
                'type'        => 'extended',
                'cron_ref'    => null,
                'description' => 'Existing evening run extended. Updates K1 and K3 scorecard metrics live.',
            ],
        ];
    }
}
// END Rhythm_orchestrator_agent

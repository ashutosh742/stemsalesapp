<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * STEM Learning - CM Call Cadence Agent (K17)
 * Migration 026 (Phase 2, live 1 Jul 2026)
 *
 * Responsibilities:
 *  1. Auto-detect CM direct calls from tblcallevents (actiontype_id=1,
 *     caller is CM type_id=13, duration > 120 seconds)
 *  2. Accept manual calls from CM direct call logger UI
 *  3. Compute K17 metric per CM weekly: percent of cstatus 6 and 7 leads
 *     in cluster where CM called fewer than 2 times this ISO week
 *  4. Surface gaps to /api/cm_planner endpoints and 7:30 audit cron
 *
 * Founder rule (verbatim): "Once the cid converted to tentative status,
 * mostly observation is that CM doesn't do the effective calling and CM
 * doesn't do the lead nurturing"
 *
 * K17 threshold: under 30 percent gap = healthy. Over 30 percent = flagged.
 *
 * Runs from:
 *   - Hook: after_tblcallevents_insert (auto-detect)
 *   - API: /api/cm_planner/call/log (manual logger)
 *   - CLI: php index.php cm_call_cadence_agent rebuild_week (Mon 6:00 AM IST)
 *   - CLI: php index.php cm_call_cadence_agent compute_k17 (every weekday 7:00 AM IST)
 */
class Cm_call_cadence_agent
{
    const MIN_CALL_DURATION_SECONDS = 120;
    const TARGET_CALLS_PER_WEEK     = 2;
    const HEALTHY_GAP_PERCENT       = 30;
    const TYPE_ID_CM                = 13;
    const ACTIONTYPE_CALL           = 1;
    const TARGET_CSTATUS_FROM       = 6;
    const TARGET_CSTATUS_TO         = 7;
    const CRON_BATCH_LIMIT          = 1000;

    protected $CI;
    protected $db;
    protected $log_prefix = '[cm_call_cadence]';

    public function __construct()
    {
        $this->CI =& get_instance();
        $this->CI->load->database();
        $this->db = $this->CI->db;
        $this->CI->load->model('line_manager_scorecard_model');
    }

    // -----------------------------------------------------------------
    // HOOK: after tblcallevents insert - auto-log if it qualifies
    // -----------------------------------------------------------------
    public function auto_log_from_event($event_id)
    {
        $ev = $this->db->select('ev.id, ev.cid_id, ev.user_id, ev.actiontype_id, ev.event_date, ev.event_time, ev.duration_seconds,
                                  ic.mainbd AS bd_uid, ic.current_status_id AS cstatus,
                                  u.type_id AS user_type_id')
            ->from('tblcallevents ev')
            ->join('init_call ic', 'ic.id = ev.cid_id')
            ->join('user u', 'u.uid = ev.user_id')
            ->where('ev.id', $event_id)
            ->get()->row_array();

        if (!$ev) return ['ok' => false, 'error' => 'event_not_found'];

        if ((int)$ev['actiontype_id'] !== self::ACTIONTYPE_CALL)        return ['ok' => true, 'skipped' => 'not_a_call'];
        if ((int)$ev['user_type_id']  !== self::TYPE_ID_CM)             return ['ok' => true, 'skipped' => 'not_a_cm'];
        if ((int)$ev['duration_seconds'] < self::MIN_CALL_DURATION_SECONDS) return ['ok' => true, 'skipped' => 'duration_below_threshold'];
        $cstatus = (int)$ev['cstatus'];
        if ($cstatus < self::TARGET_CSTATUS_FROM || $cstatus > self::TARGET_CSTATUS_TO) {
            return ['ok' => true, 'skipped' => 'cstatus_out_of_band'];
        }

        $existing = $this->db->select('id')->from('cm_lead_call_log')
            ->where('source', 'auto_tblcallevents')
            ->where('source_event_id', (int)$event_id)
            ->get()->row_array();
        if ($existing) return ['ok' => true, 'already_logged' => true, 'call_id' => $existing['id']];

        $call_at = $ev['event_date'] . ' ' . ($ev['event_time'] ?: '00:00:00');
        $iso_week = date('o-\WW', strtotime($call_at));

        $row = [
            'cm_uid'           => (int)$ev['user_id'],
            'cid_id'           => (int)$ev['cid_id'],
            'bd_uid'           => (int)$ev['bd_uid'],
            'cstatus_at_call'  => $cstatus,
            'call_at'          => $call_at,
            'duration_seconds' => (int)$ev['duration_seconds'],
            'source'           => 'auto_tblcallevents',
            'source_event_id'  => (int)$event_id,
            'iso_week'         => $iso_week,
            'counted_for_k17'  => 1,
        ];
        $this->db->insert('cm_lead_call_log', $row);
        $id = $this->db->insert_id();

        log_message('info', $this->log_prefix . " auto-logged id={$id} cm={$ev['user_id']} cid={$ev['cid_id']} duration={$ev['duration_seconds']}s");
        return ['ok' => true, 'call_id' => $id];
    }

    // -----------------------------------------------------------------
    // API: manual call log entry from CM direct call logger UI
    // -----------------------------------------------------------------
    public function log_manual_call($payload)
    {
        $required = ['cm_uid','cid_id','call_at','duration_seconds'];
        foreach ($required as $f) {
            if (!isset($payload[$f]) || $payload[$f] === '') return ['ok' => false, 'error' => 'missing_' . $f];
        }
        if ((int)$payload['duration_seconds'] < self::MIN_CALL_DURATION_SECONDS) {
            return ['ok' => false, 'error' => 'duration_too_short', 'min_seconds' => self::MIN_CALL_DURATION_SECONDS];
        }

        $cm = $this->db->select('type_id')->from('user')->where('uid', $payload['cm_uid'])->get()->row_array();
        if (!$cm || (int)$cm['type_id'] !== self::TYPE_ID_CM) return ['ok' => false, 'error' => 'not_a_cm'];

        $ic = $this->db->select('mainbd, current_status_id')->from('init_call')->where('id', $payload['cid_id'])->get()->row_array();
        if (!$ic) return ['ok' => false, 'error' => 'init_call_not_found'];

        $cstatus = (int)$ic['current_status_id'];
        $counted = ($cstatus >= self::TARGET_CSTATUS_FROM && $cstatus <= self::TARGET_CSTATUS_TO) ? 1 : 0;

        $iso_week = date('o-\WW', strtotime($payload['call_at']));

        $row = [
            'cm_uid'           => (int)$payload['cm_uid'],
            'cid_id'           => (int)$payload['cid_id'],
            'bd_uid'           => (int)$ic['mainbd'],
            'cstatus_at_call'  => $cstatus,
            'call_at'          => $payload['call_at'],
            'duration_seconds' => (int)$payload['duration_seconds'],
            'source'           => 'manual_logger',
            'source_event_id'  => null,
            'call_purpose'     => isset($payload['call_purpose']) ? substr($payload['call_purpose'], 0, 200) : null,
            'next_action'      => isset($payload['next_action']) ? substr($payload['next_action'], 0, 200) : null,
            'notes_text'       => isset($payload['notes_text']) ? $payload['notes_text'] : null,
            'iso_week'         => $iso_week,
            'counted_for_k17'  => $counted,
        ];
        $this->db->insert('cm_lead_call_log', $row);
        $id = $this->db->insert_id();
        log_message('info', $this->log_prefix . " manual-logged id={$id} cm={$payload['cm_uid']} cid={$payload['cid_id']}");
        return ['ok' => true, 'call_id' => $id, 'counted_for_k17' => $counted];
    }

    // -----------------------------------------------------------------
    // CLI: rebuild current week's auto entries from tblcallevents
    // Run Monday 6:00 AM IST as belt-and-braces (in case hook missed events)
    // -----------------------------------------------------------------
    public function rebuild_week()
    {
        $week_start = date('Y-m-d', strtotime('monday this week'));
        $week_end   = date('Y-m-d');

        $candidates = $this->db
            ->select('ev.id AS event_id')
            ->from('tblcallevents ev')
            ->join('user u', 'u.uid = ev.user_id')
            ->join('init_call ic', 'ic.id = ev.cid_id')
            ->where('ev.actiontype_id', self::ACTIONTYPE_CALL)
            ->where('u.type_id', self::TYPE_ID_CM)
            ->where('ev.duration_seconds >=', self::MIN_CALL_DURATION_SECONDS)
            ->where('ev.event_date >=', $week_start)
            ->where('ev.event_date <=', $week_end)
            ->where('ic.current_status_id >=', self::TARGET_CSTATUS_FROM)
            ->where('ic.current_status_id <=', self::TARGET_CSTATUS_TO)
            ->get()->result_array();

        $log = ['scanned' => count($candidates), 'inserted' => 0, 'skipped' => 0];
        foreach ($candidates as $c) {
            $res = $this->auto_log_from_event($c['event_id']);
            if (!empty($res['call_id']) && empty($res['already_logged'])) {
                $log['inserted']++;
            } else {
                $log['skipped']++;
            }
        }
        log_message('info', $this->log_prefix . ' rebuild_week ' . json_encode($log));
        return $log;
    }

    // -----------------------------------------------------------------
    // CLI: compute K17 per CM for this ISO week and update scorecard
    // Run every weekday 7:00 AM IST
    // -----------------------------------------------------------------
    public function compute_k17()
    {
        $today = date('Y-m-d');
        $iso_week = date('o-\WW');

        $cms = $this->db->select('uid AS cm_uid')->from('user')
            ->where('type_id', self::TYPE_ID_CM)
            ->where('status', 1)
            ->get()->result_array();

        $log = ['cms_scored' => 0, 'cms_flagged' => 0];

        foreach ($cms as $cm) {
            $cm_uid = (int)$cm['cm_uid'];

            $leads = $this->db->select('id, current_status_id')
                ->from('init_call')
                ->where('cm_uid', $cm_uid)
                ->where_in('current_status_id', [self::TARGET_CSTATUS_FROM, self::TARGET_CSTATUS_TO])
                ->where('archived IS NULL')
                ->get()->result_array();

            $total_leads = count($leads);
            if ($total_leads === 0) {
                $this->_update_scorecard($cm_uid, $today, 0.0, 0, 0);
                continue;
            }

            $gap_count = 0;
            foreach ($leads as $lead) {
                $calls = (int)$this->db
                    ->where('cm_uid', $cm_uid)
                    ->where('cid_id', $lead['id'])
                    ->where('iso_week', $iso_week)
                    ->where('counted_for_k17', 1)
                    ->from('cm_lead_call_log')
                    ->count_all_results();
                if ($calls < self::TARGET_CALLS_PER_WEEK) $gap_count++;
            }

            $gap_percent = round(($gap_count / $total_leads) * 100, 2);
            $this->_update_scorecard($cm_uid, $today, $gap_percent, $total_leads, $gap_count);
            $log['cms_scored']++;
            if ($gap_percent > self::HEALTHY_GAP_PERCENT) $log['cms_flagged']++;
        }

        log_message('info', $this->log_prefix . ' compute_k17 ' . json_encode($log));
        return $log;
    }

    // -----------------------------------------------------------------
    // Internal: write K17 onto line_manager_scorecard_daily
    // -----------------------------------------------------------------
    protected function _update_scorecard($cm_uid, $score_date, $gap_percent, $total_leads, $gap_count)
    {
        $existing = $this->db->select('id')->from('line_manager_scorecard_daily')
            ->where('manager_uid', $cm_uid)
            ->where('score_date', $score_date)
            ->get()->row_array();

        $payload = [
            'k17_cm_call_gap' => $gap_percent,
        ];

        if ($existing) {
            $this->db->where('id', $existing['id'])->update('line_manager_scorecard_daily', $payload);
        } else {
            $payload['manager_uid'] = $cm_uid;
            $payload['score_date']  = $score_date;
            $this->db->insert('line_manager_scorecard_daily', $payload);
        }
    }

    // -----------------------------------------------------------------
    // API: per-CM cadence digest for /api/cm_planner/cadence
    // -----------------------------------------------------------------
    public function cadence_for_cm($cm_uid, $iso_week = null)
    {
        $iso_week = $iso_week ?: date('o-\WW');

        $leads = $this->db->select('id, school_name, current_status_id, mainbd')
            ->from('init_call')
            ->where('cm_uid', $cm_uid)
            ->where_in('current_status_id', [self::TARGET_CSTATUS_FROM, self::TARGET_CSTATUS_TO])
            ->where('archived IS NULL')
            ->order_by('current_status_id', 'desc')
            ->get()->result_array();

        $out = [];
        foreach ($leads as $l) {
            $calls = (int)$this->db
                ->where('cm_uid', $cm_uid)
                ->where('cid_id', $l['id'])
                ->where('iso_week', $iso_week)
                ->where('counted_for_k17', 1)
                ->from('cm_lead_call_log')
                ->count_all_results();
            $gap = $calls < self::TARGET_CALLS_PER_WEEK;
            $out[] = [
                'cid_id'        => (int)$l['id'],
                'school_name'   => $l['school_name'],
                'cstatus'       => (int)$l['current_status_id'],
                'bd_uid'        => (int)$l['mainbd'],
                'calls_this_week' => $calls,
                'target'        => self::TARGET_CALLS_PER_WEEK,
                'gap_flag'      => $gap ? 1 : 0,
            ];
        }
        $gap_count = array_sum(array_column($out, 'gap_flag'));
        $total = count($out);
        $gap_pct = $total ? round(($gap_count / $total) * 100, 2) : 0;

        return [
            'cm_uid'          => (int)$cm_uid,
            'iso_week'        => $iso_week,
            'total_leads'     => $total,
            'gap_leads'       => $gap_count,
            'gap_percent'     => $gap_pct,
            'health'          => $gap_pct <= self::HEALTHY_GAP_PERCENT ? 'healthy' : 'flagged',
            'target_per_week' => self::TARGET_CALLS_PER_WEEK,
            'leads'           => $out,
        ];
    }

    public function probe()
    {
        return [
            'migration' => '026',
            'phase'     => 2,
            'deployed'  => $this->db->table_exists('cm_lead_call_log'),
            'min_duration_seconds' => self::MIN_CALL_DURATION_SECONDS,
            'target_calls_per_week' => self::TARGET_CALLS_PER_WEEK,
            'healthy_gap_percent' => self::HEALTHY_GAP_PERCENT,
        ];
    }
}

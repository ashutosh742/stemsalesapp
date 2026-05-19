<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Stem_prospecting_discipline_scorer
 * Migration 029 - Prospecting Discipline Audit
 *
 * Scores each BD per day on three gates:
 *   A. Backdrop photo - present, sized, distinct, EXIF-aligned
 *   B. GPS location - non-null, within 200 m of school, accuracy good, reading time aligned
 *   C. Day-shape rhythm - day_start ceremony, band-correct task placement, plan submitted on time
 *
 * Output:
 *   prospecting_discipline_daily (one row per bd per day)
 *   prospecting_event_audit (one row per meeting event)
 *
 * Reads from:
 *   tblcallevents (the event rows being audited)
 *   init_call (school_lat, school_lng for distance check)
 *   daily_planner (band placement check)
 *   daymanagementapprovalrequest (day_start ceremony check)
 *   location_prospect_suggestion (seed_gap check from mig 019.2)
 *   user (bd_name and cluster_id resolution)
 *
 * Place at: application/models/AIAgents/Stem_prospecting_discipline_scorer.php
 */
class Stem_prospecting_discipline_scorer extends CI_Model
{
    // Tuning constants. Override in application/config/config.php if needed.
    const GPS_RADIUS_METERS_DEFAULT = 200;
    const GPS_ACCURACY_THRESHOLD_M = 50;
    const GPS_TIME_WINDOW_MIN = 10;
    const PHOTO_SIZE_MIN_KB = 30;
    const PHOTO_TIME_WINDOW_MIN = 30;
    const PHOTO_PHASH_HISTORY_DAYS = 30;
    const SEED_EXECUTION_THRESHOLD_PCT = 60;

    public function __construct()
    {
        parent::__construct();
        $this->load->database();
    }

    // ----------------------------------------------------------------- public

    /**
     * Score every BD who had any meeting event on the given date.
     * Returns ['rows_processed' => N, 'bds_graded' => N, 'photo_passes' => N, 'gps_passes' => N].
     */
    public function score_for_date($audit_date)
    {
        $audit_date = $this->_normalize_date($audit_date);
        if (!$audit_date) {
            return ['error' => 'invalid date'];
        }

        // Reset prior runs for this date (re-runs are common)
        $this->db->where('audit_date', $audit_date)->delete('prospecting_event_audit');
        $this->db->where('audit_date', $audit_date)->delete('prospecting_discipline_daily');

        $events = $this->_pull_meeting_events($audit_date);
        $rows_processed = 0;
        $photo_passes = 0;
        $gps_passes = 0;

        // Group events by bd_uid
        $by_bd = [];
        foreach ($events as $ev) {
            $by_bd[$ev->mainbd][] = $ev;
        }

        foreach ($by_bd as $bd_uid => $bd_events) {
            foreach ($bd_events as $ev) {
                $audit_row = $this->_score_one_event($ev, $bd_uid, $audit_date);
                $this->db->insert('prospecting_event_audit', $audit_row);
                $rows_processed++;
                if ($audit_row['photo_gate_status'] === 'pass') $photo_passes++;
                if ($audit_row['gps_gate_status'] === 'pass') $gps_passes++;
            }
        }

        // Now grade each BD. Include BDs who had zero events too - they may flag.
        $all_active_bds = $this->_pull_active_bds($audit_date);
        $bds_graded = 0;
        foreach ($all_active_bds as $bd) {
            $bd_events = isset($by_bd[$bd->uid]) ? $by_bd[$bd->uid] : [];
            $grade_row = $this->_grade_bd_day($bd, $audit_date, $bd_events);
            $this->db->insert('prospecting_discipline_daily', $grade_row);
            $bds_graded++;
        }

        return [
            'audit_date'     => $audit_date,
            'rows_processed' => $rows_processed,
            'bds_graded'     => $bds_graded,
            'photo_passes'   => $photo_passes,
            'gps_passes'     => $gps_passes,
        ];
    }

    /** Fetch yesterday's per-BD discipline rows. */
    public function get_yesterday()
    {
        return $this->db->query("SELECT * FROM v_prospecting_discipline_yesterday")->result();
    }

    /** Spoof and mocked-time event log over the last N days. */
    public function get_spoof_log($days = 7)
    {
        $days = (int)$days;
        return $this->db
            ->query("SELECT * FROM v_prospecting_discipline_spoof_log WHERE audit_date >= CURDATE() - INTERVAL ? DAY", [$days])
            ->result();
    }

    /** Per-event detail for one BD on one date. */
    public function get_event_audit($bd_uid, $audit_date)
    {
        return $this->db
            ->where('bd_uid', (int)$bd_uid)
            ->where('audit_date', $this->_normalize_date($audit_date))
            ->order_by('event_time ASC')
            ->get('prospecting_event_audit')
            ->result();
    }

    /** Weekly roll-up between two dates inclusive. */
    public function get_weekly($from, $to)
    {
        return $this->db
            ->query("
                SELECT * FROM v_prospecting_discipline_weekly
                WHERE iso_week BETWEEN YEARWEEK(?, 3) AND YEARWEEK(?, 3)
                ORDER BY iso_week DESC, d_days DESC, bd_name ASC
            ", [$from, $to])
            ->result();
    }

    // -------------------------------------------------------------- per-event

    private function _score_one_event($ev, $bd_uid, $audit_date)
    {
        $photo_status = $this->_check_photo_gate($ev, $bd_uid, $audit_date);
        $gps_status   = $this->_check_gps_gate($ev);
        $band         = $this->_band_for_time($ev->event_time);
        $band_violation = $this->_is_band_violation($ev, $band) ? 1 : 0;

        return [
            'event_id'                     => $ev->id,
            'bd_uid'                       => $bd_uid,
            'audit_date'                   => $audit_date,
            'event_time'                   => $ev->event_time,
            'actiontype_id'                => $ev->actiontype_id,
            'purpose_id'                   => $ev->purpose_id,
            'photo_gate_status'            => $photo_status['status'],
            'photo_url'                    => $ev->photo_url,
            'photo_size_kb'                => $ev->photo_size_kb,
            'photo_phash'                  => $ev->photo_phash,
            'photo_phash_dup_of_event_id'  => $photo_status['dup_of'],
            'gps_gate_status'              => $gps_status['status'],
            'gps_distance_meters'          => $gps_status['distance_m'],
            'gps_accuracy_meters'          => $ev->gps_accuracy_meters,
            'band_at_event_time'           => $band,
            'band_violation'               => $band_violation,
        ];
    }

    private function _check_photo_gate($ev, $bd_uid, $audit_date)
    {
        if (empty($ev->photo_url)) {
            return ['status' => 'missing', 'dup_of' => null];
        }
        if (!empty($ev->photo_size_kb) && (int)$ev->photo_size_kb < self::PHOTO_SIZE_MIN_KB) {
            return ['status' => 'low_quality', 'dup_of' => null];
        }
        // Reuse check: same phash in the BD's last 30 days excluding this event
        if (!empty($ev->photo_phash)) {
            $dup = $this->db
                ->select('id')
                ->from('tblcallevents')
                ->where('mainbd', $bd_uid)
                ->where('photo_phash', $ev->photo_phash)
                ->where('id !=', $ev->id)
                ->where('event_date >=', date('Y-m-d', strtotime("$audit_date -" . self::PHOTO_PHASH_HISTORY_DAYS . " days")))
                ->limit(1)
                ->get()
                ->row();
            if ($dup) {
                return ['status' => 'reused', 'dup_of' => (int)$dup->id];
            }
        }
        // EXIF time check is done at upload time. Here we trust photo_size_kb + phash signal.
        // If both are null the upload pipeline patch was not applied - mark spoof_suspect to surface it.
        if (empty($ev->photo_size_kb) && empty($ev->photo_phash)) {
            return ['status' => 'spoof_suspect', 'dup_of' => null];
        }
        return ['status' => 'pass', 'dup_of' => null];
    }

    private function _check_gps_gate($ev)
    {
        if ($ev->gps_lat === null || $ev->gps_lng === null) {
            return ['status' => 'missing', 'distance_m' => null];
        }
        // School location may not yet be geocoded
        if ($ev->school_lat === null || $ev->school_lng === null) {
            return ['status' => 'school_ungeocoded', 'distance_m' => null];
        }
        $distance_m = $this->_haversine_meters(
            (float)$ev->gps_lat, (float)$ev->gps_lng,
            (float)$ev->school_lat, (float)$ev->school_lng
        );
        $radius = $this->_radius_for_cluster($ev->cluster_id);
        if ($distance_m > $radius) {
            return ['status' => 'out_of_range', 'distance_m' => (int)$distance_m];
        }
        if (!empty($ev->gps_accuracy_meters) && (int)$ev->gps_accuracy_meters > self::GPS_ACCURACY_THRESHOLD_M) {
            return ['status' => 'low_accuracy', 'distance_m' => (int)$distance_m];
        }
        if (!empty($ev->gps_reading_time) && !empty($ev->event_time)) {
            $event_ts = strtotime($ev->event_date . ' ' . $ev->event_time);
            $reading_ts = strtotime($ev->gps_reading_time);
            $delta_min = abs($event_ts - $reading_ts) / 60;
            if ($delta_min > self::GPS_TIME_WINDOW_MIN) {
                return ['status' => 'mocked_time', 'distance_m' => (int)$distance_m];
            }
        }
        return ['status' => 'pass', 'distance_m' => (int)$distance_m];
    }

    private function _band_for_time($t)
    {
        if (!$t) return null;
        $parts = explode(':', $t);
        $h = (int)$parts[0]; $m = isset($parts[1]) ? (int)$parts[1] : 0;
        $mins = $h * 60 + $m;
        if ($mins < 600) return 'pre_manual';    // before 10:00
        if ($mins < 900) return 'manual';        // 10:00 - 15:00
        if ($mins < 1050) return 'auto';         // 15:00 - 17:30
        if ($mins < 1110) return 'plan_window';  // 17:30 - 18:30
        return 'closed';
    }

    private function _is_band_violation($ev, $band)
    {
        if ($band === 'plan_window') return true; // no task adds in plan window
        if ($band === 'closed') return true;      // no task adds after 18:30
        if ($band === 'auto' && !in_array($ev->actiontype_id, [1, 2, 13])) return true; // only call/email/note in auto band
        return false;
    }

    // -------------------------------------------------------------- per-BD-day

    private function _grade_bd_day($bd, $audit_date, $bd_events)
    {
        // Excused check
        $excuse = $this->_check_excused($bd->uid, $audit_date);
        if ($excuse) {
            return [
                'bd_uid'             => $bd->uid,
                'bd_name'            => $bd->name,
                'cluster_id'         => $bd->cluster_id,
                'audit_date'         => $audit_date,
                'grade'              => 'EXCUSED',
                'meetings_total'     => 0,
                'meetings_photo_pass'=> 0,
                'meetings_gps_pass'  => 0,
                'photo_pass_pct'     => null,
                'gps_pass_pct'       => null,
                'day_shape_status'   => 'clean',
                'hard_flag_count'    => 0,
                'soft_flag_count'    => 0,
                'top_failure_reason' => null,
                'excuse_reason'      => $excuse,
            ];
        }

        // Tally per-event results we just inserted
        $audited = $this->db
            ->where('bd_uid', $bd->uid)
            ->where('audit_date', $audit_date)
            ->get('prospecting_event_audit')
            ->result();
        $meetings_total = count($audited);
        $photo_pass = 0; $gps_pass = 0;
        $hard_flags = 0; $soft_flags = 0;
        $reasons = [];
        foreach ($audited as $a) {
            if ($a->photo_gate_status === 'pass') $photo_pass++;
            if ($a->gps_gate_status === 'pass')   $gps_pass++;
            if (in_array($a->photo_gate_status, ['missing','spoof_suspect','reused'])) { $hard_flags++; $reasons[] = 'photo_' . $a->photo_gate_status; }
            elseif (in_array($a->photo_gate_status, ['stale','low_quality']))         { $soft_flags++; $reasons[] = 'photo_' . $a->photo_gate_status; }
            if (in_array($a->gps_gate_status, ['missing','out_of_range','mocked_time'])) { $hard_flags++; $reasons[] = 'gps_' . $a->gps_gate_status; }
            elseif (in_array($a->gps_gate_status, ['low_accuracy','school_ungeocoded'])) { $soft_flags++; $reasons[] = 'gps_' . $a->gps_gate_status; }
            if ($a->band_violation) { $hard_flags++; $reasons[] = 'band_violation'; }
        }

        // Day-shape gate C
        $day_shape = $this->_check_day_shape($bd->uid, $audit_date, $bd_events);
        if ($day_shape !== 'clean') {
            if (in_array($day_shape, ['plan_late','out_of_band','seed_gap'])) $hard_flags++;
            else $soft_flags++;
            $reasons[] = $day_shape;
        }

        // Grade rules
        $hard_pct = $meetings_total > 0 ? ($hard_flags / max(1, $meetings_total)) * 100 : 0;
        $photo_pct = $meetings_total > 0 ? round(($photo_pass / $meetings_total) * 100, 2) : null;
        $gps_pct   = $meetings_total > 0 ? round(($gps_pass / $meetings_total) * 100, 2)   : null;

        if ($meetings_total === 0 && $day_shape !== 'clean') {
            $grade = 'D';
        } elseif ($hard_flags === 0 && $soft_flags === 0 && $day_shape === 'clean') {
            $grade = 'A';
        } elseif ($hard_flags === 0 && $soft_flags <= 1) {
            $grade = 'B';
        } elseif ($hard_flags <= 1 && $hard_pct < 30) {
            $grade = 'C';
        } else {
            $grade = 'D';
        }

        $top_reason = !empty($reasons) ? $reasons[0] : null;

        return [
            'bd_uid'              => $bd->uid,
            'bd_name'             => $bd->name,
            'cluster_id'          => $bd->cluster_id,
            'audit_date'          => $audit_date,
            'grade'               => $grade,
            'meetings_total'      => $meetings_total,
            'meetings_photo_pass' => $photo_pass,
            'meetings_gps_pass'   => $gps_pass,
            'photo_pass_pct'      => $photo_pct,
            'gps_pass_pct'        => $gps_pct,
            'day_shape_status'    => $day_shape,
            'hard_flag_count'     => $hard_flags,
            'soft_flag_count'     => $soft_flags,
            'top_failure_reason'  => $top_reason,
            'excuse_reason'       => null,
        ];
    }

    private function _check_day_shape($bd_uid, $audit_date, $bd_events)
    {
        $flags = [];

        // Day start ceremony
        $started = $this->db
            ->from('daymanagementapprovalrequest')
            ->where('bd_uid', $bd_uid)
            ->where('plan_date', $audit_date)
            ->where('status', 'approved')
            ->count_all_results();
        if (!$started) $flags[] = 'research_gap';

        // Plan-window submit on time for tomorrow
        $tomorrow = date('Y-m-d', strtotime("$audit_date +1 day"));
        $plan = $this->db
            ->from('daily_planner')
            ->where('bd_uid', $bd_uid)
            ->where('plan_date', $tomorrow)
            ->where('submitted_by_cutoff', 0)
            ->count_all_results();
        if ($plan > 0) $flags[] = 'plan_late';

        // Out-of-band events
        $out_of_band = 0;
        foreach ($bd_events as $ev) {
            $b = $this->_band_for_time($ev->event_time);
            if (in_array($b, ['plan_window','closed'])) $out_of_band++;
        }
        if ($out_of_band > 0) $flags[] = 'out_of_band';

        // Seed gap from mig 019.2 - accepted suggestions today that never seeded
        if ($this->db->table_exists('location_prospect_suggestion')) {
            $seed_row = $this->db
                ->select("SUM(CASE WHEN seed_status='seeded' THEN 1 ELSE 0 END) AS seeded,
                          SUM(CASE WHEN status='accepted' THEN 1 ELSE 0 END) AS accepted")
                ->from('location_prospect_suggestion')
                ->where('bd_uid', $bd_uid)
                ->where('for_plan_date', $audit_date)
                ->get()->row();
            if ($seed_row && (int)$seed_row->accepted > 0 && (int)$seed_row->seeded === 0) {
                $flags[] = 'seed_gap';
            }
        }

        // Seed execution gap - of today's seeded planner rows, percent completed
        $seeded_total = $this->db
            ->from('daily_planner')
            ->where('bd_uid', $bd_uid)
            ->where('plan_date', $audit_date)
            ->where('seeded_from_prospect', 1)
            ->count_all_results();
        if ($seeded_total > 0) {
            $completed = $this->db
                ->from('daily_planner')
                ->where('bd_uid', $bd_uid)
                ->where('plan_date', $audit_date)
                ->where('seeded_from_prospect', 1)
                ->where('task_completed', 1)
                ->count_all_results();
            $exec_pct = ($completed / $seeded_total) * 100;
            if ($exec_pct < self::SEED_EXECUTION_THRESHOLD_PCT) $flags[] = 'seed_execution_gap';
        }

        if (empty($flags)) return 'clean';
        if (count($flags) === 1) return $flags[0];
        return 'multiple';
    }

    private function _check_excused($bd_uid, $audit_date)
    {
        // WFH approved
        $wfh = $this->db
            ->from('leave_request')
            ->where('user_id', $bd_uid)
            ->where('leave_date', $audit_date)
            ->where('status', 'approved')
            ->where_in('type', ['wfh','leave','sick','training'])
            ->limit(1)
            ->get()->row();
        if ($wfh) return $wfh->type;
        return null;
    }

    // ----------------------------------------------------------------- pulls

    private function _pull_meeting_events($audit_date)
    {
        // Pull meeting + barge meeting events joined with init_call for school coords
        return $this->db->query("
            SELECT
              e.id, e.mainbd, e.cid_id, e.event_date, e.event_time,
              e.actiontype_id, e.purpose_id,
              e.photo_url, e.photo_size_kb, e.photo_phash,
              e.gps_lat, e.gps_lng, e.gps_accuracy_meters, e.gps_reading_time,
              ic.school_lat, ic.school_lng,
              u.cluster_id
            FROM tblcallevents e
            LEFT JOIN init_call ic ON ic.id = e.cid_id
            LEFT JOIN user u ON u.uid = e.mainbd
            WHERE DATE(e.event_date) = ?
              AND e.actiontype_id IN (3, 4)
            ORDER BY e.mainbd, e.event_time
        ", [$audit_date])->result();
    }

    private function _pull_active_bds($audit_date)
    {
        // Any user with type_id=2 (BD) who is active. Excludes users created after audit_date.
        return $this->db->query("
            SELECT uid, name, cluster_id
            FROM user
            WHERE type_id = 2
              AND is_active = 1
              AND (DATE(createDate) <= ? OR createDate IS NULL)
        ", [$audit_date])->result();
    }

    // ---------------------------------------------------------------- helpers

    private function _haversine_meters($lat1, $lng1, $lat2, $lng2)
    {
        $earth = 6371000; // meters
        $dlat = deg2rad($lat2 - $lat1);
        $dlng = deg2rad($lng2 - $lng1);
        $a = sin($dlat/2) * sin($dlat/2)
           + cos(deg2rad($lat1)) * cos(deg2rad($lat2))
           * sin($dlng/2) * sin($dlng/2);
        $c = 2 * atan2(sqrt($a), sqrt(1-$a));
        return $earth * $c;
    }

    private function _radius_for_cluster($cluster_id)
    {
        // Future: per-cluster override table. For now, single default radius.
        return self::GPS_RADIUS_METERS_DEFAULT;
    }

    private function _normalize_date($d)
    {
        if (!$d) return null;
        $ts = strtotime($d);
        if (!$ts) return null;
        return date('Y-m-d', $ts);
    }
}

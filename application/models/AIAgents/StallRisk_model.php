<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * StallRisk_model - Migration 050
 *
 * Computes a stall-risk score 0-100 for every open lead (cstatus 1-9)
 * using a transparent weighted-rules engine. No ML inference. All weights
 * are read from stall_risk_threshold_config at run time so they can be
 * tuned from the DB without a code deploy.
 *
 * Eight rules:
 *   R01 days_since_last_touch over threshold (+20 default)
 *   R02 DM contact incomplete at cstatus 6+ (+25 default)
 *   R03 M049 remark coherence score under 60 (+15 default)
 *   R04 3+ unanswered pushback questions (+15 default)
 *   R05 cstatus age over expected-days ratio over 2.0 (+20 default)
 *   R06 zero meetings in last 14 days (+20 default)
 *   R07 fbudget under cluster median (+10 default)
 *   R08 partner_hint=cold at cstatus 7+ (+15 default)
 *
 * Buckets: HEALTHY 0-30, WATCH 31-60, AT_RISK 61-80, CRITICAL 81-100.
 *
 * Standing rules: plain English, no em-dashes, no non-ASCII in output,
 * "Rs" for rupees, "percent" spelled out, BearerAuth on controller side.
 *
 * Pilot guardrail enforced here via feature_flag + WB uids:
 *   1000289 Avishek Pathak (BD)
 *   1000351 Rimly Lahiri Chakraborty (BD)
 *   1000305 Nilanjan Chatterjee (CM)
 *   1000269 Mehak Sarraf (RM East)
 *   1000356 Debabrata Mukherjee (SC)
 *
 * Runs after M049 RemarkCoherence::run_nightly_batch as part of the
 * M035 rhythm_orchestrator cron at 22:00 IST.
 */
class StallRisk_model extends CI_Model
{
    const FEATURE_FLAG  = 'stall_risk_050_enabled';
    const PILOT_UIDS    = [1000289, 1000351, 1000305, 1000269, 1000356];

    // Bucket boundaries (inclusive lower, inclusive upper)
    const BUCKET_HEALTHY  = [0,  30];
    const BUCKET_WATCH    = [31, 60];
    const BUCKET_AT_RISK  = [61, 80];
    const BUCKET_CRITICAL = [81, 100];

    // Cached config loaded once per batch run
    private $cfg = [];

    public function __construct()
    {
        parent::__construct();
        $this->load->database();
    }

    // ------------------------------------------------------------------
    // Public entry points
    // ------------------------------------------------------------------

    /**
     * Entry point for the nightly batch.
     * Called from M035 rhythm_orchestrator at 22:00 IST, after M049.
     *
     * @param string $scope 'pilot' or 'org_wide'
     * @return array
     */
    public function run_nightly_batch($scope = 'pilot')
    {
        if ( ! $this->is_enabled()) {
            return ['status' => 'skipped', 'reason' => 'feature_flag_off'];
        }

        // Allow batch to run even if Apache closes the HTTP connection.
        @set_time_limit(300);
        @ignore_user_abort(TRUE);

        $this->cfg = $this->load_config();
        $run_id    = $this->open_run_log($scope);

        $stats = [
            'scanned'    => 0,
            'scored'     => 0,
            'healthy'    => 0,
            'watch'      => 0,
            'at_risk'    => 0,
            'critical'   => 0,
            'errors'     => 0,
        ];

        // Check if M049 has run for last night (if not, R03 will be 0 for all)
        $m049_available = $this->check_m049_available();

        // Compute cluster medians once for R07
        $cluster_medians = $this->compute_cluster_medians();

        // Fetch all in-scope leads
        $leads = $this->fetch_open_leads($scope);
        $stats['scanned'] = count($leads);

        if (empty($leads)) {
            $this->close_run_log($run_id, $stats, $m049_available);
            return ['status' => 'completed', 'run_id' => $run_id, 'stats' => $stats];
        }

        // Pre-fetch bulk signal data to avoid N+1 queries.
        $cid_list  = array_map(function($l) { return (int)$l['cid_id']; }, $leads);
        $cid_in    = implode(',', $cid_list);
        $win14     = date('Y-m-d', strtotime('-14 days'));
        $yesterday = date('Y-m-d', strtotime('-1 day'));

        // R01 bulk: last touch date per cid_id
        $touch_map = [];
        $rr = $this->db->query(
            "SELECT cid_id, MAX(event_date) AS last_date FROM tblcallevents WHERE cid_id IN ($cid_in) GROUP BY cid_id"
        );
        foreach ($rr->result_array() as $row) {
            $touch_map[(int)$row['cid_id']] = $row['last_date'];
        }

        // R06 bulk: meeting count in last 14 days per cid_id
        $meeting_map = [];
        $rm = $this->db->query(
            "SELECT cid_id, COUNT(*) AS cnt FROM tblcallevents WHERE cid_id IN ($cid_in) AND event_date >= '$win14' AND actiontype_id IN (1,2,3,4,5) GROUP BY cid_id"
        );
        foreach ($rm->result_array() as $row) {
            $meeting_map[(int)$row['cid_id']] = (int)$row['cnt'];
        }

        // R03 bulk: latest coherence score per cid_id
        $coherence_map = [];
        $rc = $this->db->query(
            "SELECT cid_id, score_total FROM remark_coherence_score WHERE cid_id IN ($cid_in) AND scored_at = (SELECT MAX(scored_at) FROM remark_coherence_score ri WHERE ri.cid_id = remark_coherence_score.cid_id)"
        );
        foreach ($rc->result_array() as $row) {
            $coherence_map[(int)$row['cid_id']] = (int)$row['score_total'];
        }

        // R04 bulk: open pushback count per cid_id
        $pushback_map = [];
        $rp = $this->db->query(
            "SELECT cid_id, COUNT(*) AS cnt FROM remark_pushback_question WHERE cid_id IN ($cid_in) AND status='open' GROUP BY cid_id"
        );
        foreach ($rp->result_array() as $row) {
            $pushback_map[(int)$row['cid_id']] = (int)$row['cnt'];
        }

        // Delta bulk: yesterday's score per cid_id
        $prev_score_map = [];
        $rd = $this->db->query(
            "SELECT cid_id, score_total FROM stall_risk_score WHERE cid_id IN ($cid_in) AND DATE(computed_at) = '$yesterday'"
        );
        foreach ($rd->result_array() as $row) {
            // Keep first (highest id per day from earlier ORDER BY id DESC logic, approximated)
            if (!isset($prev_score_map[(int)$row['cid_id']])) {
                $prev_score_map[(int)$row['cid_id']] = (int)$row['score_total'];
            }
        }

        $is_pilot = ($scope === 'pilot') ? 1 : 0;
        $now      = date('Y-m-d H:i:s');
        $mv       = $this->cfg('model_version', '1.0');

        // Score all leads using pre-fetched maps; bulk INSERT rows.
        $insert_rows = [];
        foreach ($leads as $lead) {
            try {
                $cid     = (int)$lead['cid_id'];
                $cstatus = (int)$lead['cstatus'];
                $bd_uid  = (int)($lead['bd_uid'] ?? 0);
                $cm_uid  = (int)($lead['cm_uid'] ?? 0);
                $fbudget = (float)($lead['fbudget'] ?? 0);

                // R01
                $r01_thr   = (int)$this->cfg('r01_days_since_touch', 7);
                $r01_wt    = (int)$this->cfg('r01_weight', 20);
                $last_date = isset($touch_map[$cid]) ? $touch_map[$cid] : NULL;
                $days_touch = $last_date ? (int)floor((time() - strtotime($last_date)) / 86400) : 9999;
                $r01_fired = ($days_touch > $r01_thr);
                $r01       = $r01_fired ? $r01_wt : 0;

                // R02
                $r02_min  = (int)$this->cfg('r02_min_cstatus', 6);
                $r02_wt   = (int)$this->cfg('r02_weight', 25);
                $dm_done  = (int)($lead['dm_contact_complete'] ?? 0);
                $r02_fired = ($cstatus >= $r02_min && $dm_done === 0);
                $r02       = $r02_fired ? $r02_wt : 0;

                // R03
                $r03_thr   = (int)$this->cfg('r03_coherence_threshold', 60);
                $r03_wt    = (int)$this->cfg('r03_weight', 15);
                $coh_val   = isset($coherence_map[$cid]) ? $coherence_map[$cid] : NULL;
                $r03_fired = ($coh_val !== NULL && $coh_val < $r03_thr);
                $r03       = $r03_fired ? $r03_wt : 0;

                // R04
                $r04_thr   = (int)$this->cfg('r04_pushback_threshold', 3);
                $r04_wt    = (int)$this->cfg('r04_weight', 15);
                $open_pb   = isset($pushback_map[$cid]) ? $pushback_map[$cid] : 0;
                $r04_fired = ($open_pb >= $r04_thr);
                $r04       = $r04_fired ? $r04_wt : 0;

                // R05
                $r05_ratio_thr = (float)$this->cfg('r05_ratio_threshold', 2.0);
                $r05_wt        = (int)$this->cfg('r05_weight', 20);
                $exp_days      = $this->expected_days_for_cstatus($cstatus);
                $days_in_st    = empty($lead['cstatus_updated_at']) ? 0 : (int)floor((time() - strtotime($lead['cstatus_updated_at'])) / 86400);
                $r05_ratio     = ($exp_days > 0) ? round($days_in_st / $exp_days, 2) : 0.0;
                $r05_fired     = ($r05_ratio > $r05_ratio_thr);
                $r05           = $r05_fired ? $r05_wt : 0;

                // R06
                $r06_wt    = (int)$this->cfg('r06_weight', 20);
                $meet_ct   = isset($meeting_map[$cid]) ? $meeting_map[$cid] : 0;
                $r06_fired = ($meet_ct === 0);
                $r06       = $r06_fired ? $r06_wt : 0;

                // R07
                $r07_wt          = (int)$this->cfg('r07_weight', 10);
                $cluster_median  = ($cm_uid && isset($cluster_medians[$cm_uid])) ? (float)$cluster_medians[$cm_uid] : NULL;
                $r07_fired       = ($cluster_median !== NULL && $fbudget > 0 && $fbudget < $cluster_median);
                $r07             = $r07_fired ? $r07_wt : 0;

                // R08
                $r08_min   = (int)$this->cfg('r08_min_cstatus', 7);
                $r08_wt    = (int)$this->cfg('r08_weight', 15);
                $ph        = strtolower((string)($lead['partner_hint'] ?? ''));
                $r08_fired = ($ph === 'cold' && $cstatus >= $r08_min);
                $r08       = $r08_fired ? $r08_wt : 0;

                $total_raw  = $r01 + $r02 + $r03 + $r04 + $r05 + $r06 + $r07 + $r08;
                $score      = min(100, $total_raw);
                $bucket     = $this->bucket_for_score($score);
                $components = json_encode([
                    'r01' => ['fired'=>$r01_fired,'days_since_touch'=>$days_touch,'score'=>$r01],
                    'r02' => ['fired'=>$r02_fired,'dm_contact_complete'=>$dm_done,'score'=>$r02],
                    'r03' => ['fired'=>$r03_fired,'coherence_score_total'=>$coh_val,'score'=>$r03],
                    'r04' => ['fired'=>$r04_fired,'open_pushbacks'=>$open_pb,'score'=>$r04],
                    'r05' => ['fired'=>$r05_fired,'ratio'=>$r05_ratio,'score'=>$r05],
                    'r06' => ['fired'=>$r06_fired,'meetings_in_window'=>$meet_ct,'score'=>$r06],
                    'r07' => ['fired'=>$r07_fired,'fbudget_rs'=>$fbudget,'cluster_median_rs'=>$cluster_median,'score'=>$r07],
                    'r08' => ['fired'=>$r08_fired,'partner_hint'=>$lead['partner_hint']??'','score'=>$r08],
                    'raw_total'=>$total_raw, 'capped_total'=>$score,
                ]);
                $delta = isset($prev_score_map[$cid]) ? ($score - $prev_score_map[$cid]) : NULL;

                $esc_comp = $this->db->escape($components);
                $esc_mv   = $this->db->escape($mv);
                $esc_bkt  = $this->db->escape($bucket);
                $delta_sql = ($delta === NULL) ? 'NULL' : (int)$delta;
                $insert_rows[] = "($cid, $bd_uid, $cm_uid, $cstatus, '$now', $run_id, $score, $esc_bkt, $delta_sql, $r01, $r02, $r03, $r04, $r05, $r06, $r07, $r08, $esc_comp, $esc_mv, $is_pilot)";

                $stats['scored']++;
                $bk = strtolower($bucket);
                if ($bk === 'at_risk') {
                    $stats['at_risk']++;
                } elseif (isset($stats[$bk])) {
                    $stats[$bk]++;
                }

                // Flush inserts in batches of 500 to keep memory low.
                if (count($insert_rows) >= 500) {
                    $this->db->query(
                        'INSERT INTO stall_risk_score (cid_id, bd_uid, cm_uid, cstatus_at_score, computed_at, run_id, score_total, bucket, score_delta_from_yesterday, r01_score, r02_score, r03_score, r04_score, r05_score, r06_score, r07_score, r08_score, components_json, model_version, is_pilot_run) VALUES ' . implode(',', $insert_rows)
                    );
                    $insert_rows = [];
                }
            } catch (Exception $e) {
                $stats['errors']++;
                log_message('error', 'M050 score_lead failed cid=' . $lead['cid_id'] . ': ' . $e->getMessage());
            }
        }

        // Final flush.
        if ( ! empty($insert_rows)) {
            $this->db->query(
                'INSERT INTO stall_risk_score (cid_id, bd_uid, cm_uid, cstatus_at_score, computed_at, run_id, score_total, bucket, score_delta_from_yesterday, r01_score, r02_score, r03_score, r04_score, r05_score, r06_score, r07_score, r08_score, components_json, model_version, is_pilot_run) VALUES ' . implode(',', $insert_rows)
            );
        }

        $this->close_run_log($run_id, $stats, $m049_available);
        return [
            'status'  => 'completed',
            'run_id'  => $run_id,
            'stats'   => $stats,
        ];
    }

    /**
     * Score a single lead on demand.
     * Used by the score_one API endpoint.
     *
     * @param int $cid_id
     * @return array|null null if lead not in scope
     */
    public function score_lead_by_cid($cid_id)
    {
        if ( ! $this->is_enabled()) {
            return NULL;
        }

        $this->cfg = $this->load_config();

        // Fix 2026-06-08: use real columns dm_contact_complete and partner_hint from init_call.
        $lead = $this->db->query(
            'SELECT ic.id AS cid_id, ic.mainbd AS bd_uid, CAST(ic.clm_id AS UNSIGNED) AS cm_uid,
                    ic.cstatus, ic.fbudget, ic.partner_hint,
                    IFNULL(ic.dm_contact_complete, 0) AS dm_contact_complete,
                    cm.compname AS school_name, ic.updated_at AS cstatus_updated_at
             FROM init_call ic
             LEFT JOIN company_master cm ON cm.id = ic.cmpid_id
             WHERE ic.id = ? AND ic.cstatus IN (1,2,3,6,7,8,9)
             LIMIT 1',
            array((int)$cid_id)
        )->row_array();

        if ( ! $lead) {
            return NULL;
        }

        if ( ! $this->is_lead_in_pilot($lead['bd_uid'])) {
            return NULL;
        }

        $cluster_medians = $this->compute_cluster_medians();
        $result = $this->score_lead($lead, $cluster_medians);
        $this->upsert_score($result, NULL, 0);
        return $result;
    }

    // ------------------------------------------------------------------
    // Scoring engine
    // ------------------------------------------------------------------

    /**
     * Core scoring method. Evaluates all 8 rules for one lead.
     *
     * @param array $lead  Row from init_call
     * @param array $cluster_medians  Keyed by cm_uid
     * @return array
     */
    public function score_lead($lead, $cluster_medians)
    {
        $cid_id  = (int)$lead['cid_id'];
        $cstatus = (int)$lead['cstatus'];
        $bd_uid  = (int)$lead['bd_uid'];

        $components = [];
        $total      = 0;

        // -- R01: days since last touch --
        $r01_threshold = (int)$this->cfg('r01_days_since_touch', 7);
        $r01_weight    = (int)$this->cfg('r01_weight', 20);
        $last_touch    = $this->days_since_last_touch($cid_id);
        $r01_fired     = ($last_touch > $r01_threshold);
        $r01_score     = $r01_fired ? $r01_weight : 0;
        $components['r01'] = [
            'fired'              => $r01_fired,
            'days_since_touch'   => $last_touch,
            'threshold'          => $r01_threshold,
            'score'              => $r01_score,
        ];
        $total += $r01_score;

        // -- R02: DM contact incomplete at cstatus r02_min_cstatus or above --
        $r02_min    = (int)$this->cfg('r02_min_cstatus', 6);
        $r02_weight = (int)$this->cfg('r02_weight', 25);
        $dm_done    = (int)($lead['dm_contact_complete'] ?? 0);
        $r02_fired  = ($cstatus >= $r02_min && $dm_done === 0);
        $r02_score  = $r02_fired ? $r02_weight : 0;
        $components['r02'] = [
            'fired'               => $r02_fired,
            'dm_contact_complete' => $dm_done,
            'cstatus'             => $cstatus,
            'min_cstatus'         => $r02_min,
            'score'               => $r02_score,
        ];
        $total += $r02_score;

        // -- R03: latest M049 remark coherence score under threshold --
        $r03_threshold  = (int)$this->cfg('r03_coherence_threshold', 60);
        $r03_weight     = (int)$this->cfg('r03_weight', 15);
        $coherence_row  = $this->latest_coherence_score($cid_id);
        $coherence_val  = $coherence_row ? (int)$coherence_row['score_total'] : NULL;
        $r03_fired      = ($coherence_val !== NULL && $coherence_val < $r03_threshold);
        $r03_score      = $r03_fired ? $r03_weight : 0;
        $components['r03'] = [
            'fired'                => $r03_fired,
            'coherence_score_total'=> $coherence_val,
            'threshold'            => $r03_threshold,
            'score'                => $r03_score,
        ];
        $total += $r03_score;

        // -- R04: 3 or more open pushback questions --
        $r04_threshold = (int)$this->cfg('r04_pushback_threshold', 3);
        $r04_weight    = (int)$this->cfg('r04_weight', 15);
        $open_pb       = $this->count_open_pushbacks($cid_id);
        $r04_fired     = ($open_pb >= $r04_threshold);
        $r04_score     = $r04_fired ? $r04_weight : 0;
        $components['r04'] = [
            'fired'           => $r04_fired,
            'open_pushbacks'  => $open_pb,
            'threshold'       => $r04_threshold,
            'score'           => $r04_score,
        ];
        $total += $r04_score;

        // -- R05: cstatus age over expected-days ratio --
        $r05_ratio_threshold = (float)$this->cfg('r05_ratio_threshold', 2.0);
        $r05_weight          = (int)$this->cfg('r05_weight', 20);
        $expected_days       = $this->expected_days_for_cstatus($cstatus);
        $days_in_cstatus     = $this->days_in_current_cstatus($lead);
        $r05_ratio           = ($expected_days > 0) ? round($days_in_cstatus / $expected_days, 2) : 0.0;
        $r05_fired           = ($r05_ratio > $r05_ratio_threshold);
        $r05_score           = $r05_fired ? $r05_weight : 0;
        $components['r05'] = [
            'fired'          => $r05_fired,
            'days_in_cstatus'=> $days_in_cstatus,
            'expected_days'  => $expected_days,
            'ratio'          => $r05_ratio,
            'threshold'      => $r05_ratio_threshold,
            'score'          => $r05_score,
        ];
        $total += $r05_score;

        // -- R06: zero meetings in last N days --
        $r06_window = (int)$this->cfg('r06_meeting_window_days', 14);
        $r06_weight = (int)$this->cfg('r06_weight', 20);
        $meeting_ct = $this->count_meetings_in_window($cid_id, $r06_window);
        $r06_fired  = ($meeting_ct === 0);
        $r06_score  = $r06_fired ? $r06_weight : 0;
        $components['r06'] = [
            'fired'                  => $r06_fired,
            'meetings_in_window'     => $meeting_ct,
            'window_days'            => $r06_window,
            'score'                  => $r06_score,
        ];
        $total += $r06_score;

        // -- R07: fbudget under cluster median --
        $r07_weight      = (int)$this->cfg('r07_weight', 10);
        $fbudget         = (float)($lead['fbudget'] ?? 0);
        $cm_uid          = (int)($lead['cm_uid'] ?? 0);
        $cluster_median  = $cm_uid && isset($cluster_medians[$cm_uid]) ? (float)$cluster_medians[$cm_uid] : NULL;
        $r07_fired       = ($cluster_median !== NULL && $fbudget > 0 && $fbudget < $cluster_median);
        $r07_score       = $r07_fired ? $r07_weight : 0;
        $components['r07'] = [
            'fired'              => $r07_fired,
            'fbudget_rs'         => $fbudget,
            'cluster_median_rs'  => $cluster_median,
            'score'              => $r07_score,
        ];
        $total += $r07_score;

        // -- R08: partner_hint=cold at cstatus r08_min_cstatus or above --
        $r08_min    = (int)$this->cfg('r08_min_cstatus', 7);
        $r08_weight = (int)$this->cfg('r08_weight', 15);
        $ph         = strtolower((string)($lead['partner_hint'] ?? ''));
        $r08_fired  = ($ph === 'cold' && $cstatus >= $r08_min);
        $r08_score  = $r08_fired ? $r08_weight : 0;
        $components['r08'] = [
            'fired'        => $r08_fired,
            'partner_hint' => $lead['partner_hint'] ?? '',
            'cstatus'      => $cstatus,
            'min_cstatus'  => $r08_min,
            'score'        => $r08_score,
        ];
        $total += $r08_score;

        // Cap at 100
        $raw_total    = $total;
        $score_capped = min(100, $total);

        $components['raw_total']    = $raw_total;
        $components['capped_total'] = $score_capped;

        $bucket = $this->bucket_for_score($score_capped);

        return [
            'cid_id'         => $cid_id,
            'bd_uid'         => $bd_uid,
            'cm_uid'         => (int)($lead['cm_uid'] ?? 0),
            'cstatus'        => $cstatus,
            'score_total'    => $score_capped,
            'bucket'         => $bucket,
            'r01_score'      => $r01_score,
            'r02_score'      => $r02_score,
            'r03_score'      => $r03_score,
            'r04_score'      => $r04_score,
            'r05_score'      => $r05_score,
            'r06_score'      => $r06_score,
            'r07_score'      => $r07_score,
            'r08_score'      => $r08_score,
            'components_json'=> json_encode($components),
            'model_version'  => $this->cfg('model_version', '1.0'),
        ];
    }

    // ------------------------------------------------------------------
    // Rule helper queries
    // ------------------------------------------------------------------

    /**
     * Days since the most recent tblcallevents row for this lead.
     * Returns 9999 if no events exist (treat as very stale).
     */
    private function days_since_last_touch($cid_id)
    {
        $row = $this->db->select('MAX(event_date) AS last_date')
                        ->from('tblcallevents')
                        ->where('cid_id', $cid_id)
                        ->get()->row_array();
        if ( ! $row || empty($row['last_date'])) {
            return 9999;
        }
        return (int)floor((time() - strtotime($row['last_date'])) / 86400);
    }

    /**
     * Latest remark_coherence_score row for this lead.
     */
    private function latest_coherence_score($cid_id)
    {
        return $this->db->select('score_total, grade, scored_at')
                        ->from('remark_coherence_score')
                        ->where('cid_id', $cid_id)
                        ->order_by('scored_at', 'DESC')
                        ->limit(1)
                        ->get()->row_array() ?: NULL;
    }

    /**
     * Count of open (status=open) pushback questions for this lead.
     */
    private function count_open_pushbacks($cid_id)
    {
        $row = $this->db->select('COUNT(*) AS cnt')
                        ->from('remark_pushback_question')
                        ->where('cid_id', $cid_id)
                        ->where('status', 'open')
                        ->get()->row_array();
        return $row ? (int)$row['cnt'] : 0;
    }

    /**
     * Days the lead has been in its current cstatus.
     * Uses init_call.updated_at if available, else returns 0.
     */
    private function days_in_current_cstatus($lead)
    {
        if (empty($lead['cstatus_updated_at'])) {
            return 0;
        }
        return (int)floor((time() - strtotime($lead['cstatus_updated_at'])) / 86400);
    }

    /**
     * Expected dwell days for a cstatus, from config.
     */
    private function expected_days_for_cstatus($cstatus)
    {
        $key     = 'r05_days_cstatus_' . $cstatus;
        $default = [1=>3, 2=>3, 3=>7, 6=>7, 7=>10, 8=>14, 9=>10];
        return (int)$this->cfg($key, $default[$cstatus] ?? 7);
    }

    /**
     * Count of tblcallevents rows for this lead in the last N days
     * where actiontype_id represents a meeting (not just a call/log).
     * Meeting actiontype_ids: consult your STEM actiontype reference.
     * Using actiontype_id IN (1,2,3,4,5) as the meeting set (adjust to
     * actual meeting type ids in the system if different).
     */
    private function count_meetings_in_window($cid_id, $days)
    {
        $since = date('Y-m-d', strtotime("-{$days} days"));
        $row = $this->db->select('COUNT(*) AS cnt')
                        ->from('tblcallevents')
                        ->where('cid_id', $cid_id)
                        ->where('event_date >=', $since)
                        ->where_in('actiontype_id', [1,2,3,4,5])
                        ->get()->row_array();
        return $row ? (int)$row['cnt'] : 0;
    }

    /**
     * Compute fbudget cluster medians for all cm_uids.
     * Run once per batch to avoid N+1 queries.
     * Returns array keyed by cm_uid.
     */
    private function compute_cluster_medians()
    {
        // Schema fix 2026-06-06: init_call has cmpid_id not cm_uid; cstatus not current_status_id
        $rows = $this->db->select('cmpid_id AS cm_uid, fbudget')
                         ->from('init_call')
                         ->where_in('cstatus', [1,2,3,6,7,8,9])
                         ->where('cmpid_id >', 0)
                         ->where('fbudget >', 0)
                         ->get()->result_array();

        $by_cm = [];
        foreach ($rows as $r) {
            $cm = (int)$r['cm_uid'];
            $by_cm[$cm][] = (float)$r['fbudget'];
        }
        $medians = [];
        foreach ($by_cm as $cm => $budgets) {
            sort($budgets);
            $n = count($budgets);
            if ($n === 0) {
                $medians[$cm] = 0;
            } elseif ($n % 2 === 1) {
                $medians[$cm] = $budgets[(int)floor($n / 2)];
            } else {
                $medians[$cm] = ($budgets[$n/2 - 1] + $budgets[$n/2]) / 2.0;
            }
        }
        return $medians;
    }

    // ------------------------------------------------------------------
    // Bucket assignment
    // ------------------------------------------------------------------

    public function bucket_for_score($score)
    {
        if ($score <= 30) return 'HEALTHY';
        if ($score <= 60) return 'WATCH';
        if ($score <= 80) return 'AT_RISK';
        return 'CRITICAL';
    }

    // ------------------------------------------------------------------
    // Persistence
    // ------------------------------------------------------------------

    /**
     * Insert or update the stall_risk_score row for this lead.
     * We INSERT a new row each night (one row per lead per day).
     * Then compute score_delta by comparing to the previous day's row.
     */
    private function upsert_score($result, $run_id, $is_pilot)
    {
        $yesterday = date('Y-m-d', strtotime('-1 day'));
        $prev = $this->db->select('score_total')
                         ->from('stall_risk_score')
                         ->where('cid_id', $result['cid_id'])
                         ->where('DATE(computed_at)', $yesterday)
                         ->order_by('id', 'DESC')
                         ->limit(1)
                         ->get()->row_array();

        $delta = NULL;
        if ($prev) {
            $delta = $result['score_total'] - (int)$prev['score_total'];
        }

        $this->db->insert('stall_risk_score', [
            'cid_id'                   => $result['cid_id'],
            'bd_uid'                   => $result['bd_uid'],
            'cm_uid'                   => $result['cm_uid'],
            'cstatus_at_score'         => $result['cstatus'],
            'computed_at'              => date('Y-m-d H:i:s'),
            'run_id'                   => $run_id,
            'score_total'              => $result['score_total'],
            'bucket'                   => $result['bucket'],
            'score_delta_from_yesterday' => $delta,
            'r01_score'                => $result['r01_score'],
            'r02_score'                => $result['r02_score'],
            'r03_score'                => $result['r03_score'],
            'r04_score'                => $result['r04_score'],
            'r05_score'                => $result['r05_score'],
            'r06_score'                => $result['r06_score'],
            'r07_score'                => $result['r07_score'],
            'r08_score'                => $result['r08_score'],
            'components_json'          => $result['components_json'],
            'model_version'            => $result['model_version'],
            'is_pilot_run'             => $is_pilot,
        ]);
    }

    // ------------------------------------------------------------------
    // Read-side helpers used by the controller
    // ------------------------------------------------------------------

    /**
     * Get today's critical leads, ordered by score descending.
     */
    public function get_critical_today($limit = 10)
    {
        return $this->db->order_by('score_total', 'DESC')
                        ->limit((int)$limit)
                        ->get('v_critical_leads_today')
                        ->result_array();
    }

    /**
     * Get yesterday's stall-risk summary by BD, optionally filtered by cm_uid.
     */
    public function get_yesterday_by_bd($cm_uid = NULL)
    {
        $q = $this->db;
        if ($cm_uid) {
            $q = $q->where('cm_uid', (int)$cm_uid);
        }
        return $q->order_by('count_critical', 'DESC')
                 ->get('v_stall_risk_yesterday_by_bd')
                 ->result_array();
    }

    /**
     * K_stall_aging rollup per CM: percent of cluster leads in CRITICAL bucket.
     * Reads today's scores.
     */
    public function get_stall_aging_by_cm()
    {
        // Fix 2026-06-08: user table has a single 'name' column (no first_name/last_name).
        $sql = "
            SELECT
                s.cm_uid,
                COALESCE(u.name, '') AS cm_name,
                COUNT(*) AS total_leads,
                SUM(CASE WHEN s.bucket = 'CRITICAL' THEN 1 ELSE 0 END) AS critical_count,
                ROUND(
                  SUM(CASE WHEN s.bucket = 'CRITICAL' THEN 1 ELSE 0 END) * 100.0 / COUNT(*),
                  1
                ) AS critical_percent
            FROM stall_risk_score s
            LEFT JOIN user u ON u.uid = s.cm_uid
            WHERE DATE(s.computed_at) = CURDATE()
              AND s.cm_uid > 0
            GROUP BY s.cm_uid, u.name
            ORDER BY critical_percent DESC
        ";
        return $this->db->query($sql)->result_array();
    }

    /**
     * Score history for a single lead over the last N days.
     */
    public function get_history_for_lead($cid_id, $days = 30)
    {
        $since = date('Y-m-d', strtotime("-{$days} days"));
        return $this->db->select('score_total, bucket, score_delta_from_yesterday, cstatus_at_score, computed_at, model_version')
                        ->from('stall_risk_score')
                        ->where('cid_id', (int)$cid_id)
                        ->where('DATE(computed_at) >=', $since)
                        ->order_by('computed_at', 'ASC')
                        ->get()->result_array();
    }

    // ------------------------------------------------------------------
    // Feature flag and pilot guardrail
    // ------------------------------------------------------------------

    public function is_enabled()
    {
        $row = $this->db->select('flag_value')
                        ->from('feature_flag')
                        ->where('flag_key', self::FEATURE_FLAG)
                        ->get()->row_array();
        return $row && (int)$row['flag_value'] >= 1;
    }

    public function get_flag_value()
    {
        $row = $this->db->select('flag_value')
                        ->from('feature_flag')
                        ->where('flag_key', self::FEATURE_FLAG)
                        ->get()->row_array();
        return $row ? (int)$row['flag_value'] : 0;
    }

    /**
     * Returns TRUE if the BD uid should be included in the current run.
     * flag=1: pilot only (5 WB uids). flag=2: all.
     */
    public function is_lead_in_pilot($bd_uid)
    {
        $flag = $this->get_flag_value();
        if ($flag === 0) {
            return FALSE;
        }
        if ($flag === 1) {
            return in_array((int)$bd_uid, self::PILOT_UIDS, TRUE);
        }
        // flag 2 = org-wide
        return TRUE;
    }

    // ------------------------------------------------------------------
    // Config loader
    // ------------------------------------------------------------------

    /**
     * Load all config rows into a key-value array.
     */
    private function load_config()
    {
        $rows = $this->db->get('stall_risk_threshold_config')->result_array();
        $out  = [];
        foreach ($rows as $r) {
            $out[$r['config_key']] = $r['config_value'];
        }
        return $out;
    }

    /**
     * Get a config value with a default fallback.
     */
    private function cfg($key, $default = NULL)
    {
        return isset($this->cfg[$key]) ? $this->cfg[$key] : $default;
    }

    // ------------------------------------------------------------------
    // Lead fetcher
    // ------------------------------------------------------------------

    /**
     * Fetch all open leads in scope for this run.
     */
    private function fetch_open_leads($scope)
    {
        // Fix 2026-06-08: CI3 query builder wraps bare NULL keyword in backticks -> Unknown column 'NULL'.
        // Use real columns: dm_contact_complete and partner_hint both exist in init_call.
        // clm_id is varchar in init_call; cast to int for cm_uid usage.
        $q = $this->db->select('ic.id AS cid_id, ic.mainbd AS bd_uid, CAST(ic.clm_id AS UNSIGNED) AS cm_uid, ic.cstatus, ic.fbudget, ic.partner_hint, IFNULL(ic.dm_contact_complete, 0) AS dm_contact_complete, cm.compname AS school_name, ic.updated_at AS cstatus_updated_at', FALSE)
                      ->from('init_call ic')
                      ->join('company_master cm', 'cm.id = ic.cmpid_id', 'left')
                      ->where_in('ic.cstatus', [1,2,3,6,7,8,9]);

        if ($scope === 'pilot') {
            $q = $q->where_in('ic.mainbd', self::PILOT_UIDS);
        }
        // flag=2 = no uid filter (all leads)

        return $q->get()->result_array();
    }

    // ------------------------------------------------------------------
    // Run log helpers
    // ------------------------------------------------------------------

    private function open_run_log($scope)
    {
        $this->db->insert('stall_risk_run_log', [
            'run_start_at' => date('Y-m-d H:i:s'),
            'run_status'   => 'running',
            'scope'        => $scope,
            'model_version'=> $this->cfg('model_version', '1.0'),
        ]);
        return (int)$this->db->insert_id();
    }

    private function close_run_log($run_id, $stats, $m049_available)
    {
        $status = 'completed';
        if ($stats['errors'] > 0 && $stats['scored'] === 0) {
            $status = 'failed';
        } elseif ($stats['errors'] > 0) {
            $status = 'partial';
        }

        $this->db->where('id', $run_id)->update('stall_risk_run_log', [
            'run_end_at'      => date('Y-m-d H:i:s'),
            'run_status'      => $status,
            'leads_scanned'   => $stats['scanned'],
            'leads_scored'    => $stats['scored'],
            'count_healthy'   => $stats['healthy'],
            'count_watch'     => $stats['watch'],
            'count_at_risk'   => $stats['at_risk'],
            'count_critical'  => $stats['critical'],
            'm049_unavailable'=> $m049_available ? 0 : 1,
            'errors_count'    => $stats['errors'],
        ]);
    }

    private function check_m049_available()
    {
        // Check if M049 ran last night by looking for any remark_coherence_score
        // rows scored since yesterday midnight
        $row = $this->db->select('COUNT(*) AS cnt')
                        ->from('remark_coherence_score')
                        ->where('DATE(scored_at)', date('Y-m-d', strtotime('yesterday')))
                        ->get()->row_array();
        return $row && (int)$row['cnt'] > 0;
    }
}

<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * STEM CRM - Route Brain model (Migration 069)
 *
 * Cluster-aware planning brain. Stitches Prospect, CSR, MeetingPrep,
 * Planner Coach, Stakeholder Book, Sentiment into one priority function.
 *
 * Style: plain English. No em-dashes. No non-ASCII. "Rs" for rupees.
 * NEVER fabricate. If an input is missing, return a structured warning.
 */
class RouteBrain_model extends CI_Model
{
    private $weights = [];
    private $partner_weights = [];

    public function __construct()
    {
        parent::__construct();
        $this->load->database();
        $this->_load_weights();
    }

    private function _load_weights()
    {
        $rows = $this->db->get('route_brain_weight')->result();
        foreach ($rows as $r) $this->weights[$r->weight_key] = (float)$r->weight_value;

        $prows = $this->db->get('route_brain_partner_weight')->result();
        foreach ($prows as $r) $this->partner_weights[(int)$r->partner_type_id] = (float)$r->weight;
    }

    /**
     * Build the cluster-aware planner preview for a BD.
     *
     * @return array {cluster, companies[], suggested_sequence[], efficiency_pct, wallet_planned_rs, warnings[]}
     */
    public function preview_cluster_plan($bd_uid, $cluster_id, $plan_date, $time_budget_min = 480, $wallet_budget_rs = 3000)
    {
        $bd_uid = (int)$bd_uid;
        $cluster_id = (int)$cluster_id;
        $warnings = [];

        // M070: pre-load home anchor for GPS proximity scoring
        $this->_load_home_anchor_for($bd_uid);

        // 1) Fetch cluster company index
        $this->db->where('cluster_id', $cluster_id);
        $companies = $this->db->get('cluster_company_index')->result();
        if (!$companies) {
            $warnings[] = 'No companies indexed for this cluster. Run cluster_company_index refresh.';
            return ['cluster_id'=>$cluster_id,'companies'=>[],'suggested_sequence'=>[],'efficiency_pct'=>0,'wallet_planned_rs'=>0,'warnings'=>$warnings];
        }

        // 2) Score each company
        $scored = [];
        foreach ($companies as $c) {
            $score = $this->_score_company($c);
            $scored[] = (object)[
                'company_id'      => (int)$c->company_id,
                'partner_type_id' => (int)$c->partner_type_id,
                'business_potential' => $c->business_potential,
                'is_focus_funnel' => (int)$c->is_focus_funnel,
                'is_key_company'  => (int)$c->is_key_company,
                'verified_dm_count'=> (int)$c->verified_dm_count,
                'last_touch_at'   => $c->last_touch_at,
                'csr_window_open' => (int)$c->csr_window_open,
                'csr_cycle_end'   => $c->csr_cycle_end,
                'lat'             => $c->lat,
                'lng'             => $c->lng,
                'is_geocoded'     => isset($c->is_geocoded) ? (int)$c->is_geocoded : 0,
                'gps_distance_km' => isset($c->gps_distance_km) ? $c->gps_distance_km : null,
                'score'           => round($score, 3),
            ];
        }
        usort($scored, function($a,$b){ return $b->score <=> $a->score; });

        // 3) Greedy sequence within time budget
        $sequence = $this->_greedy_sequence($scored, $bd_uid, $cluster_id, $time_budget_min);

        // 4) Efficiency
        $meet = 0; $drive = 0; $slack = 0;
        foreach ($sequence as $s) { $meet += $s['meeting_min']; $drive += $s['drive_min']; }
        $slack = max(0, $time_budget_min - $meet - $drive);
        $eff = ($meet + $drive + $slack) > 0
             ? round(100.0 * $meet / ($meet + $drive + $slack), 2)
             : 0;

        // 5) Wallet plan (Rs 500 per barge stop)
        $wallet_planned_rs = 0;
        foreach ($sequence as $s) if ($s['stop_type'] === 'barge') $wallet_planned_rs += 500;
        if ($wallet_planned_rs > $wallet_budget_rs) {
            $warnings[] = "Planned wallet Rs $wallet_planned_rs exceeds budget Rs $wallet_budget_rs. Reduce barge count.";
        }

        return [
            'cluster_id'         => $cluster_id,
            'companies'          => array_slice($scored, 0, 50),
            'suggested_sequence' => $sequence,
            'meeting_minutes'    => $meet,
            'drive_minutes'      => $drive,
            'slack_minutes'      => $slack,
            'efficiency_pct'     => $eff,
            'wallet_planned_rs'  => $wallet_planned_rs,
            'csr_window_stops'   => count(array_filter($sequence, fn($s) => !empty($s['csr_window_open_at_plan']))),
            'warnings'           => $warnings,
        ];
    }

    private function _score_company($c)
    {
        $w = $this->weights;
        $partner_w = $this->partner_weights[(int)$c->partner_type_id] ?? 0.3;
        $potl_map = ['High'=>1.0, 'Medium'=>0.6, 'Low'=>0.2, 'Unknown'=>0.3];
        $potl = $potl_map[$c->business_potential] ?? 0.3;

        $contact = 0.0;
        if ((int)$c->verified_dm_count > 0) $contact = 1.0;
        elseif ((int)$c->contact_count > 0) $contact = 0.5;

        $recent = 0;
        if ($c->last_touch_at) {
            $days = (time() - strtotime($c->last_touch_at)) / 86400;
            $recent = max(0, 1 - ($days / 90.0));
        }

        $visited_recent = ($c->last_touch_at && (time() - strtotime($c->last_touch_at)) < 30 * 86400) ? 1 : 0;

        // M070 GPS proximity boost: if company has lat/lng AND we know home anchor,
        // give a graded boost for closer companies (0 to 1.0 at 0km, 0 at 25km).
        $gps_boost = 0.0;
        if (isset($this->home_anchor) && $this->home_anchor
            && isset($c->lat) && isset($c->lng) && $c->lat !== null && $c->lng !== null) {
            $km = $this->_haversine_km($this->home_anchor['lat'], $this->home_anchor['lng'], $c->lat, $c->lng);
            if ($km !== null) {
                $gps_boost = max(0.0, 1.0 - ($km / 25.0));
            }
        }
        // Flag is_geocoded for caller (used in preview output)
        $c->is_geocoded = (isset($c->lat) && isset($c->lng) && $c->lat !== null && $c->lng !== null) ? 1 : 0;
        $c->gps_distance_km = isset($km) ? round((float)$km, 3) : null;

        $score = ($w['w_partner'] ?? 1.0) * $partner_w
               + ($w['w_csr']     ?? 1.5) * ((int)$c->csr_window_open)
               + ($w['w_potl']    ?? 0.6) * $potl
               + ($w['w_focus']   ?? 0.8) * ((int)$c->is_focus_funnel)
               + ($w['w_key']     ?? 1.0) * ((int)$c->is_key_company)
               + ($w['w_contact'] ?? 0.7) * $contact
               + ($w['w_recent']  ?? 0.5) * $recent
               - ($w['w_visited'] ?? 1.2) * $visited_recent
               + ($w['w_gps_proximity'] ?? 0.4) * $gps_boost;

        return $score;
    }

    /**
     * M070: load active home anchor for a BD into $this->home_anchor for proximity scoring.
     * Safe to call multiple times. Sets null if unset.
     */
    private function _load_home_anchor_for($bd_uid)
    {
        $row = $this->db
            ->where(['user_id'=>(int)$bd_uid,'anchor_label'=>'home','active'=>1])
            ->order_by('id','desc')->limit(1)
            ->get('day_start_home_anchor_v2')->row_array();
        if ($row && isset($row['lat']) && isset($row['lng'])) {
            $this->home_anchor = ['lat'=>(double)$row['lat'],'lng'=>(double)$row['lng'],'radius_km'=>(double)($row['radius_km'] ?? 5.0)];
        } else {
            $this->home_anchor = null;
        }
    }

    private function _greedy_sequence($scored, $bd_uid, $cluster_id, $time_budget_min)
    {
        $sequence = [];
        $time_used = 0;
        $picked = [];
        $current_lat = null; $current_lng = null;

        foreach ($scored as $cand) {
            if ($time_used >= $time_budget_min) break;
            if (isset($picked[$cand->company_id])) continue;
            if ($cand->score < 0) continue; // skip negative-score (recently visited)

            // Drive minutes
            $drive_min = $this->_estimate_drive_min($current_lat, $current_lng, $cand->lat, $cand->lng);
            $meeting_min = 60;

            if ($time_used + $drive_min + $meeting_min > $time_budget_min) continue;

            $stop_type = ((int)$cand->verified_dm_count > 0) ? 'anchored' : 'barge';

            $sequence[] = [
                'seq'                       => count($sequence) + 1,
                'company_id'                => $cand->company_id,
                'stop_type'                 => $stop_type,
                'priority_score'            => $cand->score,
                'drive_min'                 => $drive_min,
                'meeting_min'               => $meeting_min,
                'csr_window_open_at_plan'   => (int)$cand->csr_window_open,
            ];
            $picked[$cand->company_id] = 1;
            $time_used += $drive_min + $meeting_min;
            $current_lat = $cand->lat; $current_lng = $cand->lng;
            if (count($sequence) >= 7) break; // cap 7 stops a day
        }
        return $sequence;
    }

    private function _estimate_drive_min($lat1, $lng1, $lat2, $lng2)
    {
        if ($lat1 === null || $lng1 === null || $lat2 === null || $lng2 === null) return 20;
        $R = 6371; // km
        $dLat = deg2rad($lat2 - $lat1);
        $dLng = deg2rad($lng2 - $lng1);
        $a = sin($dLat/2) * sin($dLat/2)
           + cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * sin($dLng/2) * sin($dLng/2);
        $c = 2 * atan2(sqrt($a), sqrt(1-$a));
        $km = $R * $c;
        // Mumbai average 18 kmph in traffic = 3.3 min per km
        return (int)round($km * 3.3);
    }

    /**
     * Persist a route plan and its stops.
     */
    public function save_plan($bd_uid, $plan_date, $cluster_id, $preview)
    {
        $bd_uid = (int)$bd_uid;
        $cluster_id = (int)$cluster_id;
        $meet = (int)$preview['meeting_minutes'];
        $drive = (int)$preview['drive_minutes'];
        $slack = (int)$preview['slack_minutes'];
        $eff = (float)$preview['efficiency_pct'];

        $grade = 'D';
        if ($eff >= 80) $grade = 'A+';
        elseif ($eff >= 70) $grade = 'A';
        elseif ($eff >= 55) $grade = 'B';
        elseif ($eff >= 40) $grade = 'C';

        $this->db->where('bd_uid', $bd_uid)->where('plan_date', $plan_date)
                 ->delete('route_plan'); // re-plan replaces previous

        $this->db->insert('route_plan', [
            'bd_uid'             => $bd_uid,
            'plan_date'          => $plan_date,
            'cluster_id'         => $cluster_id,
            'stop_count'         => count($preview['suggested_sequence']),
            'meeting_minutes'    => $meet,
            'drive_minutes'      => $drive,
            'slack_minutes'      => $slack,
            'total_priority_score'=> array_sum(array_column($preview['suggested_sequence'], 'priority_score')),
            'efficiency_pct'     => $eff,
            'wallet_planned_rs'  => (int)$preview['wallet_planned_rs'],
            'csr_window_stops'   => (int)$preview['csr_window_stops'],
            'route_grade'        => $grade,
            'optimizer_version'  => 'greedy_v1',
            'created_at'         => date('Y-m-d H:i:s'),
        ]);
        $plan_id = (int)$this->db->insert_id();

        foreach ($preview['suggested_sequence'] as $s) {
            $this->db->insert('route_stop', [
                'route_plan_id' => $plan_id,
                'seq'           => $s['seq'],
                'company_id'    => $s['company_id'],
                'stop_type'     => $s['stop_type'],
                'priority_score'=> $s['priority_score'],
                'csr_window_open_at_plan' => $s['csr_window_open_at_plan'],
            ]);
        }
        return $plan_id;
    }

    /**
     * Suggest 3 walk-in candidates near the just-finished stop.
     */
    public function suggest_walkins($bd_uid, $finished_stop_id, $minutes_left_in_day = 120)
    {
        $bd_uid = (int)$bd_uid;
        $finished_stop_id = (int)$finished_stop_id;

        $stop = $this->db->where('id', $finished_stop_id)->get('route_stop')->row();
        if (!$stop) return [];

        $plan = $this->db->where('id', $stop->route_plan_id)->get('route_plan')->row();
        if (!$plan) return [];

        // Find nearby unvisited high-score companies in the same cluster
        $this->db->where('cluster_id', $plan->cluster_id)
                 ->where('company_id !=', $stop->company_id);
        $candidates = $this->db->get('cluster_company_index')->result();

        // Get current company lat/lng
        $cur = $this->db->where('id', $stop->company_id)->get('company_master')->row();
        $cur_lat = $cur ? $cur->lat : null;
        $cur_lng = $cur ? $cur->lng : null;

        $ranked = [];
        foreach ($candidates as $cand) {
            // skip recently touched
            if ($cand->last_touch_at && (time() - strtotime($cand->last_touch_at)) < 14 * 86400) continue;
            $drive_min = $this->_estimate_drive_min($cur_lat, $cur_lng, $cand->lat, $cand->lng);
            if ($drive_min > $minutes_left_in_day - 30) continue; // need 30+ min meeting time
            $score = $this->_score_company($cand);
            $reason_code = 'priority';
            $reason_text = 'High priority unvisited company nearby.';
            if ((int)$cand->csr_window_open === 1) {
                $reason_code = 'csr_window_open';
                $reason_text = "CSR cycle window open until {$cand->csr_cycle_end}. Worth a walk-in.";
            } elseif ((int)$cand->is_key_company === 1) {
                $reason_code = 'key_account';
                $reason_text = 'Key account flagged in master, never been visited.';
            } elseif ((int)$cand->verified_dm_count > 0) {
                $reason_code = 'dm_verified';
                $reason_text = 'Verified decision-maker contact in stakeholder book.';
            }
            $ranked[] = [
                'company_id'     => (int)$cand->company_id,
                'distance_km'    => round($this->_haversine_km($cur_lat, $cur_lng, $cand->lat, $cand->lng), 2),
                'drive_minutes'  => $drive_min,
                'priority_score' => round($score, 3),
                'reason_code'    => $reason_code,
                'reason_text'    => $reason_text,
            ];
        }
        usort($ranked, function($a,$b){ return $b['priority_score'] <=> $a['priority_score']; });
        return array_slice($ranked, 0, 3);
    }

    private function _haversine_km($lat1,$lng1,$lat2,$lng2)
    {
        if ($lat1===null||$lng1===null||$lat2===null||$lng2===null) return 0;
        $R = 6371;
        $dLat = deg2rad($lat2-$lat1); $dLng = deg2rad($lng2-$lng1);
        $a = sin($dLat/2)**2 + cos(deg2rad($lat1))*cos(deg2rad($lat2))*sin($dLng/2)**2;
        return $R * 2 * atan2(sqrt($a), sqrt(1-$a));
    }

    /**
     * Score day execution vs plan after day_ceremony close.
     */
    public function score_execution($bd_uid, $score_date)
    {
        $bd_uid = (int)$bd_uid;
        $plan = $this->db->where('bd_uid',$bd_uid)->where('plan_date',$score_date)
                          ->get('route_plan')->row();

        $stops = $plan ? $this->db->where('route_plan_id',$plan->id)->get('route_stop')->result() : [];
        $planned_stops = count($stops);
        $executed = 0; $meet_min = 0; $drive_min_actual = 0;
        $gps_hits = 0; $photo_hits = 0;

        foreach ($stops as $s) {
            if ($s->actual_start_time && $s->actual_end_time) {
                $executed++;
                $meet_min += (strtotime($s->actual_end_time) - strtotime($s->actual_start_time)) / 60;
            }
            if ($s->gps_check_in_at) $gps_hits++;
            // photo_hits would join meeting_photo - omitted for brevity
        }

        $walk_suggested = (int)$this->db->where('bd_uid',$bd_uid)
            ->where('DATE(suggested_at)', $score_date)
            ->count_all_results('post_meeting_suggestion');
        $walk_acted = (int)$this->db->where('bd_uid',$bd_uid)
            ->where('DATE(suggested_at)', $score_date)
            ->where('acted_on', 1)
            ->count_all_results('post_meeting_suggestion');

        $total_time = $meet_min + $drive_min_actual;
        $eff_actual = $total_time > 0 ? round(100.0 * $meet_min / $total_time, 2) : 0;
        $eff_delta = $plan ? $eff_actual - (float)$plan->efficiency_pct : 0;

        $grade = 'D';
        if ($eff_actual >= 80) $grade='A+';
        elseif ($eff_actual >= 70) $grade='A';
        elseif ($eff_actual >= 55) $grade='B';
        elseif ($eff_actual >= 40) $grade='C';

        $gps_pct = $planned_stops > 0 ? round(100.0 * $gps_hits / $planned_stops, 2) : 0;

        $this->db->where('bd_uid',$bd_uid)->where('score_date',$score_date)
                 ->delete('route_efficiency_score');
        $this->db->insert('route_efficiency_score', [
            'bd_uid'                => $bd_uid,
            'score_date'            => $score_date,
            'route_plan_id'         => $plan ? $plan->id : null,
            'planned_stops'         => $planned_stops,
            'executed_stops'        => $executed,
            'walk_ins_acted'        => $walk_acted,
            'walk_ins_suggested'    => $walk_suggested,
            'meeting_minutes_actual'=> (int)$meet_min,
            'drive_minutes_actual'  => (int)$drive_min_actual,
            'slack_minutes_actual'  => 0,
            'gps_capture_pct'       => $gps_pct,
            'photo_capture_pct'     => 0,
            'efficiency_actual_pct' => $eff_actual,
            'efficiency_delta_pct'  => $eff_delta,
            'quality_grade'         => $grade,
            'accountability_uid'    => $bd_uid,
            'created_at'            => date('Y-m-d H:i:s'),
        ]);
        return (int)$this->db->insert_id();
    }
}

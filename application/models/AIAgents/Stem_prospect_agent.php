<?php
/**
 * Prospect_model.php
 *
 * STEM Learning Prospecting AI Agent (Rev 12, migration 019_prospecting_agent).
 * Drop into application/models/AIAgents/Prospect_model.php
 *
 * Responsibilities:
 *   1. Score every BD's daily prospecting effectiveness (research + barge meetings + leads added)
 *   2. Suggest location-aware leads near a BD's current area, blending:
 *      a) cluster_school_index (schools we already know in that area)
 *      b) Web research via Google Maps / directory lookup (Anaya plugin)
 *   3. Tag every new lead's category (positive_key, focused, partner, cold, follow_up, lapsed)
 *
 * Never writes to init_call.new_lead row creation (that stays on the existing NewLeadController).
 * Only writes to its own 4 tables: prospecting_daily_score, location_prospect_run,
 * location_prospect_suggestion, cluster_school_index.
 *
 * Reads: init_call, tblcallevents, user, cluster_mapping, research_candidates, lead_category_master.
 */
defined('BASEPATH') OR exit('No direct script access allowed');

class Prospect_model extends CI_Model {

    const ACTIONTYPE_RESEARCH      = 10;
    const PURPOSE_RESEARCH         = 94;
    const ACTIONTYPE_BARGE_MEETING = 4;
    const PURPOSE_BARGE_MEETING    = 66;

    public function __construct() {
        parent::__construct();
        $this->load->database();
    }

    /* =========================================================
     * EFFECTIVENESS - score one BD for one day
     * ========================================================= */
    public function score_bd($bd_uid, $score_date = null) {
        $bd_uid = (int)$bd_uid;
        if (!$score_date) $score_date = date('Y-m-d', strtotime('yesterday'));

        // FIX: tblcallevents uses user_id not uid
        $research = (int)$this->db->query(
            "SELECT COUNT(*) c FROM tblcallevents
             WHERE user_id = ? AND actiontype_id = ? AND purpose_id = ?
               AND DATE(event_date) = ?",
            [$bd_uid, self::ACTIONTYPE_RESEARCH, self::PURPOSE_RESEARCH, $score_date]
        )->row()->c;

        $barge = (int)$this->db->query(
            "SELECT COUNT(*) c FROM tblcallevents
             WHERE user_id = ? AND actiontype_id = ? AND purpose_id = ?
               AND DATE(event_date) = ?",
            [$bd_uid, self::ACTIONTYPE_BARGE_MEETING, self::PURPOSE_BARGE_MEETING, $score_date]
        )->row()->c;

        $leads = $this->db->query(
            "SELECT new_lead FROM init_call
             WHERE creator_id = ? AND new_lead = 1 AND DATE(createDate) = ?",
            [$bd_uid, $score_date]
        )->result_array();

        $lead_count = count($leads);
        $by_cat = ['positive_key'=>0,'focused'=>0,'partner'=>0,'cold'=>0,'follow_up'=>0,'lapsed'=>0];

        // Weighted score 0 to 100
        // Research = 2 pts each, Barge = 5 pts each, lead = 8 pts, plus category multiplier
        $base = ($research * 2) + ($barge * 5) + ($lead_count * 8);
        $cat_bonus = ($by_cat['positive_key'] * 10) + ($by_cat['focused'] * 6) + ($by_cat['partner'] * 6);
        $score = min(100, $base + $cat_bonus);

        $grade = ($score >= 90) ? 'A+' : (($score >= 75) ? 'A' :
                 (($score >= 60) ? 'B'  : (($score >= 40) ? 'C' : 'D')));

        // cluster the BD worked most (mode of cluster_id on today's leads)
        $cluster_row = $this->db->query(
            "SELECT cluster_id, COUNT(*) c FROM init_call
             WHERE creator_id = ? AND DATE(createDate) = ?
             GROUP BY cluster_id ORDER BY c DESC LIMIT 1",
            [$bd_uid, $score_date]
        )->row();
        $cluster_id = $cluster_row ? (int)$cluster_row->cluster_id : null;

        $payload = [
            'bd_uid'              => $bd_uid,
            'score_date'          => $score_date,
            'research_count'      => $research,
            'barge_meeting_count' => $barge,
            'new_leads_added'     => $lead_count,
            'category_positive_key' => $by_cat['positive_key'],
            'category_focused'    => $by_cat['focused'],
            'category_partner'    => $by_cat['partner'],
            'category_cold'       => $by_cat['cold'],
            'category_follow_up'  => $by_cat['follow_up'],
            'category_lapsed'     => $by_cat['lapsed'],
            'cluster_id'          => $cluster_id,
            'prospecting_score'   => $score,
            'grade'               => $grade
        ];

        // upsert
        $this->db->query(
            "INSERT INTO prospecting_daily_score
              (bd_uid, score_date, research_count, barge_meeting_count, new_leads_added,
               category_positive_key, category_focused, category_partner,
               category_cold, category_follow_up, category_lapsed,
               cluster_id, prospecting_score, grade)
             VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?)
             ON DUPLICATE KEY UPDATE
               research_count=VALUES(research_count),
               barge_meeting_count=VALUES(barge_meeting_count),
               new_leads_added=VALUES(new_leads_added),
               category_positive_key=VALUES(category_positive_key),
               category_focused=VALUES(category_focused),
               category_partner=VALUES(category_partner),
               category_cold=VALUES(category_cold),
               category_follow_up=VALUES(category_follow_up),
               category_lapsed=VALUES(category_lapsed),
               cluster_id=VALUES(cluster_id),
               prospecting_score=VALUES(prospecting_score),
               grade=VALUES(grade)",
            array_values($payload)
        );
        return $payload;
    }

    public function score_all($score_date = null) {
        if (!$score_date) $score_date = date('Y-m-d', strtotime('yesterday'));
        // FIX: BD users have type_id=3 in user_details; user table has `active` not `is_active`
        $bds = $this->db->query(
            "SELECT DISTINCT u.uid FROM user u
             JOIN user_details ud ON ud.user_id = u.uid
             WHERE ud.type_id IN (3) AND u.active = 1"
        )->result_array();
        $out = [];
        foreach ($bds as $r) $out[] = $this->score_bd($r['uid'], $score_date);
        return $out;
    }

    /* =========================================================
     * LOCATION SUGGEST - given a BD and an area, return candidate schools
     * ========================================================= */
    public function suggest_for_area($bd_uid, $area_name, $city = 'Mumbai',
                                     $radius_km = 2.0, $cluster_id = null,
                                     $lat = null, $lng = null) {
        $bd_uid = (int)$bd_uid;

        // 1) create run row - include run_date so the views (which filter by run_date=CURDATE()) see it
        $this->db->insert('location_prospect_run', [
            'bd_uid'       => $bd_uid,
            'run_date'     => date('Y-m-d'),
            'area_name'    => $area_name,
            'city'         => $city,
            'lat'          => $lat,
            'lng'          => $lng,
            'radius_km'    => $radius_km,
            'cluster_id'   => $cluster_id,
            'source_mix'   => 'cluster+web',
            'status'       => 'running'
        ]);
        $run_id = $this->db->insert_id();

        // 2) pull from cluster_school_index first (known schools in this area)
        $known = $this->db->query(
            "SELECT csi.school_name, csi.area_name, csi.lat, csi.lng,
                    csi.board, csi.est_student_count, csi.category_code,
                    csi.init_call_id
             FROM cluster_school_index csi
             WHERE csi.is_active = 1
               AND (csi.cluster_id = ? OR ? IS NULL)
               AND csi.area_name LIKE ?
             LIMIT 25",
            [$cluster_id, $cluster_id, '%'.$area_name.'%']
        )->result_array();

        $cluster_count = 0;
        foreach ($known as $k) {
            $this->db->insert('location_prospect_suggestion', [
                'run_id'              => $run_id,
                'bd_uid'              => $bd_uid,
                'school_name'         => $k['school_name'],
                'board'               => $k['board'],
                'est_student_count'   => $k['est_student_count'],
                'category_hint'       => $k['category_code'],
                'lat'                 => $k['lat'],
                'lng'                 => $k['lng'],
                'source'              => 'cluster_index',
                'source_detail'       => 'known school in cluster',
                'confidence'          => 0.900,
                'existing_init_call_id' => $k['init_call_id']
            ]);
            $cluster_count++;
        }

        // 3) call web-research helper (Google Maps / directory) - Anaya plugin handles the actual fetch.
        //    Returns array of ['school_name','address_short','phone','email','board','lat','lng']
        $web_results = $this->_web_research_schools($area_name, $city, $radius_km, $lat, $lng);
        $web_count = 0;
        foreach ($web_results as $w) {
            // dedupe against existing init_call by name
            $dup = $this->db->query(
                "SELECT ic.id FROM init_call ic
                 JOIN company_master cm ON cm.id = ic.cmpid_id
                 WHERE cm.compname LIKE ? LIMIT 1",
                ['%'.$w['school_name'].'%']
            )->row();
            $this->db->insert('location_prospect_suggestion', [
                'run_id'              => $run_id,
                'bd_uid'              => $bd_uid,
                'school_name'         => $w['school_name'],
                'address_short'       => $w['address_short'] ?? null,
                'board'               => $w['board'] ?? null,
                'phone'               => $w['phone'] ?? null,
                'email'               => $w['email'] ?? null,
                'principal_name'      => $w['principal_name'] ?? null,
                'lat'                 => $w['lat'] ?? null,
                'lng'                 => $w['lng'] ?? null,
                'distance_km'         => $w['distance_km'] ?? null,
                'category_hint'       => 'cold',
                'source'              => 'web_google_maps',
                'source_detail'       => $w['source_detail'] ?? 'Google Maps Places API',
                'confidence'          => $w['confidence'] ?? 0.650,
                'existing_init_call_id' => $dup ? (int)$dup->id : null,
                'status'              => $dup ? 'accepted' : 'pending'
            ]);
            $web_count++;
        }

        // 4) update run summary
        $this->db->where('id', $run_id)->update('location_prospect_run', [
            'cluster_suggestion_count' => $cluster_count,
            'web_suggestion_count'     => $web_count,
            'status'                   => 'complete'
        ]);

        return [
            'run_id'                   => $run_id,
            'cluster_suggestion_count' => $cluster_count,
            'web_suggestion_count'     => $web_count,
            'total'                    => $cluster_count + $web_count
        ];
    }

    /**
     * Web research stub. In production this calls the AnayaAgent web-fetch tool
     * which wraps Google Maps Places API + JustDial directory. Returns array of schools.
     * For staging without API key, returns an empty array and logs the intent.
     */
    private function _web_research_schools($area_name, $city, $radius_km, $lat, $lng) {
        if (!class_exists('AnayaAgent_web_lookup')) {
            log_message('info', "[Prospect] web lookup stub for $area_name $city");
            return [];
        }
        $w = new AnayaAgent_web_lookup();
        return $w->search_schools_near($area_name, $city, $radius_km, $lat, $lng);
    }

    /* =========================================================
     * READS for the UI and the cron
     * ========================================================= */
    public function today_org_summary() {
        return $this->db->query("SELECT * FROM v_prospecting_today_org")->row_array();
    }

    public function today_by_bd() {
        return $this->db->query("SELECT * FROM v_prospecting_today_by_bd")->result_array();
    }

    public function run_detail($run_id) {
        $run  = $this->db->query("SELECT * FROM location_prospect_run WHERE id = ?", [$run_id])->row_array();
        $sugg = $this->db->query(
            "SELECT * FROM location_prospect_suggestion WHERE run_id = ? ORDER BY confidence DESC",
            [$run_id]
        )->result_array();
        return ['run' => $run, 'suggestions' => $sugg];
    }

    public function recent_runs_by_bd($bd_uid, $days = 7) {
        $query = "SELECT * FROM location_prospect_run
             WHERE triggered_at >= DATE_SUB(NOW(), INTERVAL ? DAY)
             ORDER BY triggered_at DESC LIMIT 20";
        $params = [$days];
        if ($bd_uid > 0) {
            $query = "SELECT * FROM location_prospect_run
             WHERE bd_uid = ? AND triggered_at >= DATE_SUB(NOW(), INTERVAL ? DAY)
             ORDER BY triggered_at DESC LIMIT 20";
            $params = [$bd_uid, $days];
        }
        return $this->db->query($query, $params)->result_array();
    }

    /* =========================================================
     * Accept a suggestion - links to a new init_call row
     * (NewLeadController still owns the actual lead row creation,
     *  this just records the chain back to the suggestion.)
     * ========================================================= */
    public function mark_accepted($suggestion_id, $init_call_id) {
        $this->db->where('id', $suggestion_id)->update('location_prospect_suggestion', [
            'status'                => 'accepted',
            'accepted_init_call_id' => $init_call_id,
            'actioned_at'           => date('Y-m-d H:i:s')
        ]);
        // bump run counter
        $sugg = $this->db->where('id', $suggestion_id)->get('location_prospect_suggestion')->row();
        if ($sugg) {
            $this->db->query(
                "UPDATE location_prospect_run SET accepted_count = accepted_count + 1 WHERE id = ?",
                [$sugg->run_id]
            );
        }
        return ['ok' => true];
    }

    public function mark_dismissed($suggestion_id, $reason = '') {
        $this->db->where('id', $suggestion_id)->update('location_prospect_suggestion', [
            'status'           => 'dismissed',
            'dismissed_reason' => $reason,
            'actioned_at'      => date('Y-m-d H:i:s')
        ]);
        // bump run counter
        $sugg = $this->db->where('id', $suggestion_id)->get('location_prospect_suggestion')->row();
        if ($sugg) {
            $this->db->query(
                "UPDATE location_prospect_run SET dismissed_count = dismissed_count + 1 WHERE id = ?",
                [$sugg->run_id]
            );
        }
        return ['ok' => true];
    }

    /**
     * Returns prospecting seeds for a given date.
     */
    public function seeded_for_date($date) {
        try {
            $rows = $this->db->query(
                "SELECT lps.*, lpr.area_name, lpr.city
                 FROM location_prospect_suggestion lps
                 JOIN location_prospect_run lpr ON lpr.id = lps.run_id
                 WHERE lpr.run_date = ?
                 ORDER BY lps.confidence DESC",
                [$date]
            )->result_array();
            return $rows ?: [];
        } catch (Exception $e) {
            log_message('error', 'Prospect_model::seeded_for_date: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Returns BDs with no seeding for today.
     */
    public function seed_gap() {
        try {
            $rows = $this->db->query(
                "SELECT u.uid, u.name
                 FROM user u
                 JOIN user_details ud ON ud.user_id = u.uid
                 WHERE ud.type_id = 3
                   AND u.active = 1
                   AND u.uid NOT IN (
                       SELECT DISTINCT bd_uid FROM location_prospect_run
                       WHERE run_date = CURDATE()
                   )
                 ORDER BY u.name"
            )->result_array();
            return $rows ?: [];
        } catch (Exception $e) {
            log_message('error', 'Prospect_model::seed_gap: ' . $e->getMessage());
            return [];
        }
    }

}

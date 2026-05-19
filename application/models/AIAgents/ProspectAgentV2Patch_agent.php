<?php
/**
 * Prospect_model_v2_patch.php
 *
 * STEM Learning Prospecting AI Agent - Migration 019.2 PATCH.
 * APPLIED AS MERGE on top of the Rev 12 Prospect_model.php from migration 019.
 *
 * This file shows the new methods and signature changes that must be merged
 * into application/models/AIAgents/Prospect_model.php. Each block has a
 * MERGE LOCATION comment pointing to where in the original file it goes.
 *
 * Wires prospecting to STEM's day-before planning rhythm:
 *  - suggest_for_area() now accepts a target_plan_date (default = tomorrow)
 *  - mark_accepted() is wrapped by new accept_and_seed() that ALSO inserts
 *    into daily_planner for the target_plan_date
 *  - All seed attempts are logged to prospect_seed_audit
 *
 * Plan cutoff awareness: if the call comes in after 18:30 IST, target_plan_date
 * defaults to day-after-tomorrow (since today's 18:30 plan-submit gate has closed).
 *
 * Status: STAGING ONLY until Mon 18 May 2026 GitHub access lands.
 */
defined('BASEPATH') OR exit('No direct script access allowed');

// ============================================================
// MERGE LOCATION: replace existing suggest_for_area() signature at line 145
// ============================================================
class Prospect_model_v2_patch {

    /**
     * UPDATED METHOD: suggest_for_area now accepts target_plan_date.
     * Default behavior:
     *   - If current IST time < 18:30: target = tomorrow (BD has the rest of
     *     the day to accept and seed before the plan-submit gate).
     *   - If current IST time >= 18:30: target = day-after-tomorrow (today's
     *     gate has closed, so seeded suggestions go to the next planning window).
     *
     * This default protects against seeding into a plan_date that is already
     * locked. The tr_prospect_seed_guard trigger is the belt-and-braces backstop.
     */
    public function suggest_for_area($bd_uid, $area_name, $city = 'Mumbai',
                                     $radius_km = 2.0, $cluster_id = null,
                                     $lat = null, $lng = null,
                                     $target_plan_date = null) {
        $bd_uid = (int)$bd_uid;

        if (!$target_plan_date) {
            $target_plan_date = $this->_default_target_plan_date();
        }

        // 1) create run row with target_plan_date
        $this->db->insert('location_prospect_run', [
            'bd_uid'           => $bd_uid,
            'target_plan_date' => $target_plan_date,
            'area_name'        => $area_name,
            'city'             => $city,
            'lat'              => $lat,
            'lng'              => $lng,
            'radius_km'        => $radius_km,
            'cluster_id'       => $cluster_id,
            'source_mix'       => 'cluster+web'
        ]);
        $run_id = $this->db->insert_id();

        // 2) pull from cluster_school_index (same as before, but stamp for_plan_date)
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
                'run_id'                => $run_id,
                'school_name'           => $k['school_name'],
                'board'                 => $k['board'],
                'est_student_count'     => $k['est_student_count'],
                'category_hint'         => $k['category_code'],
                'lat'                   => $k['lat'],
                'lng'                   => $k['lng'],
                'source'                => 'cluster_index',
                'source_detail'         => 'known school in cluster',
                'confidence'            => 0.900,
                'existing_init_call_id' => $k['init_call_id'],
                'for_plan_date'         => $target_plan_date
            ]);
            $cluster_count++;
        }

        // 3) web research (unchanged) - stamp for_plan_date
        $web_results = $this->_web_research_schools($area_name, $city, $radius_km, $lat, $lng);
        $web_count = 0;
        foreach ($web_results as $w) {
            $dup = $this->db->query(
                "SELECT id FROM init_call WHERE compnay LIKE ? LIMIT 1",
                ['%'.$w['school_name'].'%']
            )->row();
            $this->db->insert('location_prospect_suggestion', [
                'run_id'                => $run_id,
                'school_name'           => $w['school_name'],
                'address_short'         => $w['address_short'] ?? null,
                'board'                 => $w['board'] ?? null,
                'phone'                 => $w['phone'] ?? null,
                'email'                 => $w['email'] ?? null,
                'principal_name'        => $w['principal_name'] ?? null,
                'lat'                   => $w['lat'] ?? null,
                'lng'                   => $w['lng'] ?? null,
                'distance_km'           => $w['distance_km'] ?? null,
                'category_hint'         => 'cold',
                'source'                => 'web_google_maps',
                'source_detail'         => $w['source_detail'] ?? 'Google Maps Places API',
                'confidence'            => $w['confidence'] ?? 0.650,
                'existing_init_call_id' => $dup ? (int)$dup->id : null,
                'status'                => $dup ? 'duplicate' : 'suggested',
                'for_plan_date'         => $target_plan_date
            ]);
            $web_count++;
        }

        $this->db->where('id', $run_id)->update('location_prospect_run', [
            'cluster_suggestion_count' => $cluster_count,
            'web_suggestion_count'     => $web_count,
            'status'                   => 'complete'
        ]);

        return [
            'run_id'                   => $run_id,
            'target_plan_date'         => $target_plan_date,
            'cluster_suggestion_count' => $cluster_count,
            'web_suggestion_count'     => $web_count,
            'total'                    => $cluster_count + $web_count
        ];
    }

    // ============================================================
    // NEW METHOD: accept_and_seed
    // MERGE LOCATION: insert after mark_accepted() around line 303
    // ============================================================

    /**
     * Accept a suggestion AND auto-seed tomorrow's daily_planner row.
     *
     * This is the day-before linkage that closes the gap:
     *   suggest -> accept -> seed daily_planner -> 18:30 BD review -> CM approve -> visit
     *
     * Behavior:
     *   - Inserts an init_call row (calls existing NewLead_model::create_from_suggestion)
     *   - Inserts a daily_planner row for the suggestion's for_plan_date
     *   - Stamps suggestion.status='accepted', seeded_planner_id, seed_status='seeded'
     *   - Writes a prospect_seed_audit row (append-only)
     *   - Returns init_call_id and seeded_planner_id
     *
     * Guards:
     *   - Rejects if for_plan_date < CURDATE() (the trigger also enforces this)
     *   - Rejects if BD already has a daily_planner row for this school + plan_date
     *     (avoids double-seeding the same lead into the same plan)
     *
     * Failure modes:
     *   - If init_call create fails: seed_status='seed_failed', error logged, no planner row
     *   - If planner insert fails: init_call still committed, seed_status='seed_failed',
     *     BD can manually add via Day Planner screen as fallback
     */
    public function accept_and_seed($suggestion_id, $bd_uid, $source_channel = 'app') {
        $suggestion_id = (int)$suggestion_id;
        $bd_uid        = (int)$bd_uid;

        $s = $this->db->where('id', $suggestion_id)
                      ->get('location_prospect_suggestion')->row_array();
        if (!$s) {
            return ['ok' => false, 'error' => 'suggestion not found'];
        }
        if ($s['status'] === 'accepted' && $s['seeded_planner_id']) {
            return ['ok' => true, 'already_seeded' => true,
                    'init_call_id' => $s['accepted_init_call_id'],
                    'seeded_planner_id' => $s['seeded_planner_id']];
        }

        $for_plan_date = $s['for_plan_date'];
        if (!$for_plan_date || $for_plan_date < date('Y-m-d')) {
            $err = 'for_plan_date is past or null';
            $this->_audit_seed($suggestion_id, $s['run_id'], $bd_uid, null, null,
                               $for_plan_date ?: '0000-00-00', 'seed_failed', $err, $source_channel);
            return ['ok' => false, 'error' => $err];
        }

        $this->db->trans_start();

        // 1) create init_call row via existing NewLead_model
        //    (if NewLead_model not loaded, fall back to direct insert with creator_id=bd_uid, new_lead=1)
        $init_call_id = null;
        if (method_exists($this->load->library('AIAgents/NewLead_model'), 'create_from_suggestion')) {
            $init_call_id = $this->newlead->create_from_suggestion($s, $bd_uid);
        } else {
            $this->db->insert('init_call', [
                'compnay'      => $s['school_name'],
                'creator_id'   => $bd_uid,
                'mainbd'       => $bd_uid,
                'new_lead'     => 1,
                'category'     => $s['category_hint'] ?: 'cold',
                'category_code'    => $s['category_hint'] ?: 'cold',
                'partner_type_code'=> $s['partner_hint'],
                'lat'          => $s['lat'],
                'lng'          => $s['lng'],
                'board'        => $s['board'],
                'city'         => 'Mumbai',
                'createDate'   => date('Y-m-d H:i:s'),
                'current_status_id' => 1
            ]);
            $init_call_id = (int)$this->db->insert_id();
        }

        if (!$init_call_id) {
            $this->db->trans_rollback();
            $this->_audit_seed($suggestion_id, $s['run_id'], $bd_uid, null, null,
                               $for_plan_date, 'seed_failed', 'init_call insert failed', $source_channel);
            return ['ok' => false, 'error' => 'init_call insert failed'];
        }

        // 2) dedupe check - same lead already in this BD's plan for this date?
        $dup_plan = $this->db->query(
            "SELECT id FROM daily_planner
             WHERE bd_uid = ? AND plan_date = ? AND init_call_id = ? LIMIT 1",
            [$bd_uid, $for_plan_date, $init_call_id]
        )->row();
        if ($dup_plan) {
            $this->db->trans_complete();
            $this->db->where('id', $suggestion_id)->update('location_prospect_suggestion', [
                'status'                => 'accepted',
                'accepted_init_call_id' => $init_call_id,
                'seeded_planner_id'     => (int)$dup_plan->id,
                'seed_status'           => 'seed_skipped',
                'seed_error'            => 'already in plan',
                'actioned_at'           => date('Y-m-d H:i:s')
            ]);
            $this->_audit_seed($suggestion_id, $s['run_id'], $bd_uid, $init_call_id,
                               (int)$dup_plan->id, $for_plan_date, 'seed_dup', null, $source_channel);
            return ['ok' => true, 'init_call_id' => $init_call_id,
                    'seeded_planner_id' => (int)$dup_plan->id, 'note' => 'already in plan'];
        }

        // 3) insert daily_planner row for tomorrow.
        //    atid 3 = meeting (default for a fresh school visit). Slot left null
        //    for BD to set in the planner UI when reviewing at 18:30.
        $this->db->insert('daily_planner', [
            'bd_uid'           => $bd_uid,
            'plan_date'        => $for_plan_date,
            'init_call_id'     => $init_call_id,
            'atid'             => 3,
            'purpose_id'       => 1,
            'is_auto'          => 1,
            'auto_source'      => 'prospect_seed',
            'auto_source_ref'  => $suggestion_id,
            'is_same_day_plan' => 0,
            'created_at'       => date('Y-m-d H:i:s'),
            'submitted_by_cutoff' => 0,
            'status'           => 'draft'
        ]);
        $planner_id = (int)$this->db->insert_id();

        $this->db->trans_complete();
        if (!$this->db->trans_status() || !$planner_id) {
            $this->_audit_seed($suggestion_id, $s['run_id'], $bd_uid, $init_call_id, null,
                               $for_plan_date, 'seed_failed', 'daily_planner insert failed', $source_channel);
            return ['ok' => false, 'init_call_id' => $init_call_id,
                    'error' => 'daily_planner insert failed'];
        }

        // 4) stamp the suggestion
        $this->db->where('id', $suggestion_id)->update('location_prospect_suggestion', [
            'status'                => 'accepted',
            'accepted_init_call_id' => $init_call_id,
            'seeded_planner_id'     => $planner_id,
            'seed_status'           => 'seeded',
            'actioned_at'           => date('Y-m-d H:i:s')
        ]);

        // 5) bump run.accepted_count
        $this->db->query(
            "UPDATE location_prospect_run SET accepted_count = accepted_count + 1 WHERE id = ?",
            [$s['run_id']]
        );

        // 6) audit
        $this->_audit_seed($suggestion_id, $s['run_id'], $bd_uid, $init_call_id, $planner_id,
                           $for_plan_date, 'seeded', null, $source_channel);

        return [
            'ok'                => true,
            'init_call_id'      => $init_call_id,
            'seeded_planner_id' => $planner_id,
            'for_plan_date'     => $for_plan_date
        ];
    }

    /* =========================================================
     * PRIVATE HELPERS
     * MERGE LOCATION: append at bottom of class, before closing brace
     * ========================================================= */

    /**
     * Default target plan_date based on IST cutoff awareness.
     * Before 18:30 IST -> tomorrow.
     * After 18:30 IST  -> day after tomorrow.
     * Saturday or Sunday -> next Monday.
     */
    private function _default_target_plan_date() {
        $tz = new DateTimeZone('Asia/Kolkata');
        $now = new DateTime('now', $tz);
        $cutoff = (clone $now)->setTime(18, 30, 0);
        $offset_days = ($now >= $cutoff) ? 2 : 1;
        $target = (clone $now)->modify("+{$offset_days} days");
        // skip weekends
        $w = (int)$target->format('N'); // 1=Mon, 7=Sun
        if ($w === 6) $target->modify('+2 days');
        if ($w === 7) $target->modify('+1 day');
        return $target->format('Y-m-d');
    }

    /**
     * Append-only audit row. Never throws.
     */
    private function _audit_seed($suggestion_id, $run_id, $bd_uid,
                                 $init_call_id, $planner_id,
                                 $for_plan_date, $result, $error, $source_channel) {
        try {
            $this->db->insert('prospect_seed_audit', [
                'suggestion_id'     => (int)$suggestion_id,
                'run_id'            => (int)$run_id,
                'bd_uid'            => (int)$bd_uid,
                'init_call_id'      => $init_call_id ? (int)$init_call_id : null,
                'seeded_planner_id' => $planner_id ? (int)$planner_id : null,
                'for_plan_date'     => $for_plan_date,
                'seed_result'       => $result,
                'seed_error'        => $error,
                'source_channel'    => $source_channel
            ]);
        } catch (Exception $e) {
            log_message('error', '[Prospect] audit row insert failed: '.$e->getMessage());
        }
    }

    /* =========================================================
     * NEW READ HELPERS for the 7:00 Morning Brief and 7:30 BD Audit
     * MERGE LOCATION: append near other read helpers around line 280
     * ========================================================= */

    public function seeded_for_date($plan_date = null) {
        if (!$plan_date) $plan_date = date('Y-m-d', strtotime('tomorrow'));
        return $this->db->query(
            "SELECT * FROM v_prospect_seeded_tomorrow WHERE plan_date = ?",
            [$plan_date]
        )->result_array();
    }

    public function seed_gap_recent($days = 7) {
        return $this->db->query(
            "SELECT * FROM v_prospect_seed_gap
             WHERE plan_date >= DATE_SUB(CURDATE(), INTERVAL ? DAY)
             ORDER BY plan_date DESC, accept_minus_seed DESC",
            [(int)$days]
        )->result_array();
    }
}
// END PATCH FILE - merge contents into application/models/AIAgents/Prospect_model.php

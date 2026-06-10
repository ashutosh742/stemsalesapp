<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * CorporateCsrProspect_agent.php
 *
 * STEM Learning Corporate CSR Prospecting Agent (Migration 041).
 *
 * Responsibilities:
 *   1. For each BD, given their cluster/area for tomorrow, surface corporates
 *      doing CSR in that geography.
 *   2. Pull data from 4 lanes:
 *        L1: csr.gov.in (MCA Section 135 registry)
 *        L2: Apollo.io (firmographics + decision maker contacts)
 *        L3: LinkedIn snippet verification via existing LinkedinCsr_model
 *        L4: political_influencer_master_v2 (MP / MLA / Collector mapping)
 *   3. Rank by csr spend + education share + project geography + renewal window
 *      + DM confidence + influencer presence
 *   4. Write to corporate_csr_suggestion_v2. Never touches init_call directly.
 *      init_call write happens in accept_and_seed via the existing NewLead path.
 *
 * Parallel to existing Prospect_model (school-side). Both can run in same brief.
 *
 * Staging only until production go-live 1 Jun 2026.
 */

class CorporateCsrProspect_model extends CI_Model {

    const APOLLO_DAILY_CAP        = 80;
    const LINKEDIN_DAILY_CAP      = 200;
    const TOP_N_PER_CLUSTER       = 5;
    const TOP_N_PER_BD            = 10;
    const RENEWAL_WINDOW_DAYS     = 90;

    private $apollo_endpoint      = 'https://api.apollo.io/v1/mixed_people/search';
    private $csr_gov_endpoint     = 'https://www.csr.gov.in/content/csr/global/master/home/ExploreCsrData/csr-projects.html';

    public function __construct() {
        parent::__construct();
        $this->load->database();
    }

    // ============================================================
    // PROBE (cron-friendly endpoint)
    // ============================================================
    public function probe() {
        // Quick existence check on key tables
        $tables = ['csr_corporate_master_v2', 'csr_project_v2', 'corporate_csr_suggestion_v2'];
        foreach ($tables as $t) {
            $r = $this->db->query("SHOW TABLES LIKE '" . $this->db->escape_like_str($t) . "'");
            if ($r->num_rows() === 0) {
                return ['ok' => false, 'missing' => $t];
            }
        }
        return ['ok' => true, 'migration' => '041', 'version' => 1];
    }

    // ============================================================
    // REFRESH SUGGESTIONS for one BD for a target plan date
    // ============================================================
    public function refresh_for_bd($bd_uid, $target_plan_date = null) {
        $bd_uid = (int)$bd_uid;
        if (!$target_plan_date) {
            $target_plan_date = date('Y-m-d', strtotime('tomorrow'));
        }

        $clusters = $this->_clusters_for_bd($bd_uid, $target_plan_date);
        if (empty($clusters)) {
            return ['ok' => true, 'bd_uid' => $bd_uid, 'suggestions' => 0, 'note' => 'no clusters for BD'];
        }

        $total_suggested = 0;
        $apollo_calls    = 0;
        $linkedin_calls  = 0;

        // Open a run row
        $this->db->insert('corporate_csr_prospect_run_v2', [
            'bd_uid'           => $bd_uid,
            'target_plan_date' => $target_plan_date,
            'triggered_by'     => 'cron',
        ]);
        $run_id = $this->db->insert_id();

        foreach ($clusters as $c) {
            $candidates = $this->_candidates_in_cluster($c, $bd_uid);
            $candidates = $this->_rank_candidates($candidates, $c);

            $count_for_cluster = 0;
            foreach ($candidates as $row) {
                if ($total_suggested >= self::TOP_N_PER_BD) break 2;
                if ($count_for_cluster >= self::TOP_N_PER_CLUSTER) break;

                // Decision maker enrichment (Apollo gated by quota)
                $dm_id = $this->_ensure_decision_maker($row['csr_corporate_id'], $apollo_calls);

                // LinkedIn verification gated by quota
                if ($dm_id && $linkedin_calls < self::LINKEDIN_DAILY_CAP) {
                    $this->_verify_dm_via_linkedin($dm_id);
                    $linkedin_calls++;
                }

                // Influencer attachment
                $infl_id = $this->_influencer_for_district($row['project_district'], $row['project_state']);

                // Outreach angle + blurb
                $angle = $this->_pick_outreach_angle($row, $dm_id, $infl_id);
                $blurb = $this->_build_blurb($row, $dm_id, $infl_id, $angle);

                $rank_reasons = [
                    'csr_spend_pts'         => $row['_csr_spend_pts'],
                    'education_share_pts'   => $row['_education_share_pts'],
                    'project_in_cluster_pts'=> $row['_project_in_cluster_pts'],
                    'renewal_window_pts'    => $row['_renewal_window_pts'],
                    'dm_confidence_pts'     => $row['_dm_confidence_pts'],
                    'influencer_bonus_pts'  => $infl_id ? 5 : 0,
                ];

                $this->db->insert('corporate_csr_suggestion_v2', [
                    'run_id'            => $run_id,
                    'bd_uid'            => $bd_uid,
                    'csr_corporate_id'  => $row['csr_corporate_id'],
                    'csr_project_id'    => $row['csr_project_id'],
                    'csr_dm_id'         => $dm_id,
                    'influencer_id'     => $infl_id,
                    'rank_score'        => $row['rank_score'],
                    'rank_band'         => $this->_band($row['rank_score']),
                    'rank_reasons'      => json_encode($rank_reasons),
                    'outreach_angle'    => $angle,
                    'outreach_blurb'    => $blurb,
                    'status'            => 'suggested',
                    'for_plan_date'     => $target_plan_date,
                ]);

                $total_suggested++;
                $count_for_cluster++;
            }
        }

        $this->db->where('run_id', $run_id)->update('corporate_csr_prospect_run_v2', [
            'total_suggested'    => $total_suggested,
            'apollo_calls_made'  => $apollo_calls,
            'linkedin_calls_made'=> $linkedin_calls,
        ]);

        return [
            'ok'              => true,
            'run_id'          => $run_id,
            'bd_uid'          => $bd_uid,
            'target_plan_date'=> $target_plan_date,
            'suggestions'     => $total_suggested,
            'apollo_calls'    => $apollo_calls,
            'linkedin_calls'  => $linkedin_calls,
        ];
    }

    // ============================================================
    // FETCH for BD app
    // ============================================================
    public function today_for_bd($bd_uid, $plan_date = null) {
        $bd_uid = (int)$bd_uid;
        if (!$plan_date) $plan_date = date('Y-m-d', strtotime('tomorrow'));

        $sql = "SELECT * FROM v_corp_csr_suggestions_today
                WHERE bd_uid = ? AND for_plan_date = ?
                  AND status IN ('suggested','accepted')
                ORDER BY rank_score DESC";
        return $this->db->query($sql, [$bd_uid, $plan_date])->result_array();
    }

    public function today_org_summary() {
        $sql = "SELECT
                  COUNT(*) AS total_suggested,
                  COUNT(DISTINCT bd_uid) AS bds_covered,
                  COUNT(DISTINCT csr_corporate_id) AS unique_corporates,
                  SUM(CASE WHEN status='accepted' THEN 1 ELSE 0 END) AS accepted_count,
                  SUM(CASE WHEN status='dismissed' THEN 1 ELSE 0 END) AS dismissed_count,
                  AVG(rank_score) AS avg_rank_score
                FROM corporate_csr_suggestion_v2
                WHERE for_plan_date = DATE_ADD(CURDATE(), INTERVAL 1 DAY)";
        $org = $this->db->query($sql)->row_array();

        $apollo = $this->db->query("SELECT * FROM v_apollo_quota_today")->row_array();
        $top = $this->db->query(
            "SELECT s.bd_uid, c.company_name, p.project_district, s.rank_score, s.outreach_angle
             FROM corporate_csr_suggestion_v2 s
             JOIN csr_corporate_master_v2 c ON c.csr_corporate_id = s.csr_corporate_id
             LEFT JOIN csr_project_v2 p ON p.csr_project_id = s.csr_project_id
             WHERE s.for_plan_date = DATE_ADD(CURDATE(), INTERVAL 1 DAY)
             ORDER BY s.rank_score DESC LIMIT 10"
        )->result_array();

        return ['org' => $org, 'apollo_quota' => $apollo, 'top_10' => $top];
    }

    // ============================================================
    // ACCEPT (reshaped per PR #7 reshape plan)
    //
    // Production discipline: this method does NOT write to init_call or
    // daily_planner directly. Those tables are owned by the 6 frozen prod
    // creation paths (BARGE_UNKNOWN, RESEARCH_BORN, NEW_LEAD_FORM,
    // ADMIN_CREATED, EXCEL_IMPORTED, BARGE_FROM_FUNNEL).
    //
    // Instead this method:
    //   1. Marks the suggestion as accepted in corporate_csr_suggestion_v2
    //   2. Writes a row to corporate_csr_lead_link_v2 with the BD intent
    //   3. Returns a redirect_hint payload telling the client which prod
    //      creation path to launch (research_born by default) and
    //      pre-fills the company_name + lead_source_tag for the form.
    //
    // The BD then completes the prod creation flow in the app. When the
    // resulting init_call lands, a separate hook (link_init_call below)
    // is called to bind init_call_id back into corporate_csr_lead_link_v2.
    // ============================================================
    public function accept_and_seed($suggestion_id, $bd_uid, $target_plan_date = null) {
        $suggestion_id = (int)$suggestion_id;
        $bd_uid = (int)$bd_uid;

        $sug = $this->db->where('suggestion_id', $suggestion_id)
                        ->where('bd_uid', $bd_uid)
                        ->get('corporate_csr_suggestion_v2')->row_array();
        if (!$sug) return ['ok' => false, 'error' => 'suggestion not found or not owned by BD'];
        if ($sug['status'] !== 'suggested') {
            return ['ok' => false, 'error' => 'already in status ' . $sug['status']];
        }

        if (!$target_plan_date) $target_plan_date = $sug['for_plan_date'];

        $corp = $this->db->where('csr_corporate_id', $sug['csr_corporate_id'])
                         ->get('csr_corporate_master_v2')->row_array();

        // Mark suggestion accepted (no init_call write)
        $this->db->where('suggestion_id', $suggestion_id)->update('corporate_csr_suggestion_v2', [
            'status'      => 'accepted',
            'accepted_at' => date('Y-m-d H:i:s'),
        ]);

        // Write the lead-link intent row (init_call_id filled in later by link_init_call)
        $this->db->insert('corporate_csr_lead_link_v2', [
            'suggestion_id'    => $suggestion_id,
            'bd_uid'           => $bd_uid,
            'csr_corporate_id' => $sug['csr_corporate_id'],
            'company_name'     => $corp['company_name'],
            'target_plan_date' => $target_plan_date,
            'init_call_id'     => null,
            'link_status'      => 'pending',
            'created_at'       => date('Y-m-d H:i:s'),
        ]);
        $link_id = $this->db->insert_id();

        // Return redirect_hint instead of writing to prod tables
        return [
            'ok'           => true,
            'link_id'      => $link_id,
            'redirect_hint'=> [
                'creation_path' => 'research_born',
                'screen'        => 'NewLeadScreen',
                'prefill'       => [
                    'company_name'        => $corp['company_name'],
                    'lead_type'           => 'corporate',
                    'lead_source_tag'     => 'marketing_csr_prospect',
                    'lead_source_campaign_ref' => 'csr_suggestion_' . $suggestion_id,
                    'target_plan_date'    => $target_plan_date,
                ],
                'link_id'       => $link_id,
            ],
        ];
    }

    // ============================================================
    // LINK init_call back to a suggestion (called after BD completes
    // the prod creation flow in the app). Idempotent.
    // ============================================================
    public function link_init_call($link_id, $init_call_id) {
        $link_id = (int)$link_id;
        $init_call_id = (int)$init_call_id;
        if (!$link_id || !$init_call_id) return ['ok' => false, 'error' => 'missing ids'];

        $link = $this->db->where('link_id', $link_id)
                         ->get('corporate_csr_lead_link_v2')->row_array();
        if (!$link) return ['ok' => false, 'error' => 'link not found'];
        if ($link['link_status'] === 'linked' && (int)$link['init_call_id'] === $init_call_id) {
            return ['ok' => true, 'already_linked' => true];
        }

        $this->db->where('link_id', $link_id)->update('corporate_csr_lead_link_v2', [
            'init_call_id' => $init_call_id,
            'link_status'  => 'linked',
            'linked_at'    => date('Y-m-d H:i:s'),
        ]);

        // Also stamp suggestion with init_call_id for read paths
        $this->db->where('suggestion_id', $link['suggestion_id'])->update('corporate_csr_suggestion_v2', [
            'init_call_id_seeded' => $init_call_id,
        ]);

        return ['ok' => true, 'link_id' => $link_id, 'init_call_id' => $init_call_id];
    }

    public function dismiss($suggestion_id, $bd_uid, $reason = '') {
        $this->db->where('suggestion_id', (int)$suggestion_id)
                 ->where('bd_uid', (int)$bd_uid)
                 ->update('corporate_csr_suggestion_v2', [
                     'status'        => 'dismissed',
                     'dismissed_at'  => date('Y-m-d H:i:s'),
                     'dismiss_reason'=> substr($reason, 0, 120),
                 ]);
        return ['ok' => true];
    }

    // ============================================================
    // PRIVATE: candidate pull and ranking
    // ============================================================
    private function _clusters_for_bd($bd_uid, $target_plan_date) {
        // Tomorrow's planned areas
        $rows = $this->db->query(
            "SELECT DISTINCT cm.address AS area_name, ic.cluster_id, cm.city AS district, cm.state AS state
             FROM daily_planner dp
             JOIN init_call ic ON ic.id = dp.cid_id
             LEFT JOIN company_master cm ON cm.id = ic.cmpid_id
             WHERE dp.uid = ? AND dp.plan_date = ?",
            [$bd_uid, $target_plan_date]
        )->result_array();

        if (!empty($rows)) return $rows;

        // Fallback: BD home cluster + districts touched in last 7 days
        $fallback = $this->db->query(
            "SELECT DISTINCT ic.cluster_id, cm.city AS district, cm.state AS state
             FROM tblcallevents ev
             JOIN init_call ic ON ic.id = ev.cid_id
             LEFT JOIN company_master cm ON cm.id = ic.cmpid_id
             WHERE ev.uid = ?
               AND ev.event_date >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)
             LIMIT 3",
            [$bd_uid]
        )->result_array();

        return $fallback;
    }

    private function _candidates_in_cluster($cluster, $bd_uid) {
        $district = $cluster['district'] ?? null;
        $state    = $cluster['state'] ?? null;
        if (!$district && !$state) return [];

        $sql = "
            SELECT
              c.csr_corporate_id, c.company_name, c.csr_spent_last_fy_rs_cr,
              c.csr_education_share_pct, c.has_foundation_arm,
              p.csr_project_id, p.project_district, p.project_state,
              p.project_theme, p.cycle_status, p.end_year,
              (SELECT cid_id FROM init_call WHERE company_name = c.company_name
                 AND cstatus IN (6,8,9,12) LIMIT 1) AS existing_init_call_id
            FROM csr_corporate_master_v2 c
            JOIN csr_project_v2 p ON p.csr_corporate_id = c.csr_corporate_id
            WHERE c.active = 1
              AND p.cycle_status IN ('active','last_quarter','renewing')
              AND (p.project_district = ? OR p.project_state = ?)
            ORDER BY c.csr_spent_last_fy_rs_cr DESC
            LIMIT 50";
        $rows = $this->db->query($sql, [$district, $state])->result_array();

        // Filter out already-in-funnel corporates
        return array_values(array_filter($rows, function($r) { return empty($r['existing_init_call_id']); }));
    }

    private function _rank_candidates($candidates, $cluster) {
        foreach ($candidates as &$r) {
            // 1. CSR spend points 0-30 (log scale)
            $spend = (float)($r['csr_spent_last_fy_rs_cr'] ?? 0);
            $r['_csr_spend_pts'] = min(30, max(0, log10(max(1, $spend)) * 10));

            // 2. Education share points 0-25
            $edu = (float)($r['csr_education_share_pct'] ?? 0);
            $r['_education_share_pts'] = min(25, $edu / 4);

            // 3. Project in cluster 0-20 (hard 20 if district match)
            $r['_project_in_cluster_pts'] = ($r['project_district'] === ($cluster['district'] ?? null)) ? 20 : 10;

            // 4. Renewal window 0-15
            $r['_renewal_window_pts'] = in_array($r['cycle_status'], ['last_quarter','renewing']) ? 15 : 0;

            // 5. DM confidence (filled later, default 0 here)
            $r['_dm_confidence_pts'] = 0;

            $r['rank_score'] = round(
                $r['_csr_spend_pts'] + $r['_education_share_pts'] +
                $r['_project_in_cluster_pts'] + $r['_renewal_window_pts'] +
                $r['_dm_confidence_pts'], 2
            );
        }
        unset($r);

        usort($candidates, function($a, $b) { return $b['rank_score'] <=> $a['rank_score']; });
        return $candidates;
    }

    private function _band($score) {
        if ($score >= 80) return 'A';
        if ($score >= 60) return 'B';
        if ($score >= 40) return 'C';
        return 'D';
    }

    // ============================================================
    // DECISION MAKER enrichment via Apollo
    // ============================================================
    private function _ensure_decision_maker($corp_id, &$apollo_calls) {
        // Reuse existing DM if any
        $existing = $this->db->where('csr_corporate_id', $corp_id)
                             ->where('active', 1)
                             ->order_by('csr_confidence_score', 'DESC')
                             ->get('csr_decision_maker_v2')->row_array();
        if ($existing) return $existing['csr_dm_id'];

        // Quota check
        $quota = $this->db->where('quota_date', date('Y-m-d'))
                          ->get('apollo_daily_quota_v2')->row_array();
        if (!$quota) {
            $this->db->insert('apollo_daily_quota_v2', [
                'quota_date' => date('Y-m-d'),
                'calls_made' => 0,
                'daily_cap'  => self::APOLLO_DAILY_CAP,
            ]);
            $quota = ['calls_made' => 0, 'daily_cap' => self::APOLLO_DAILY_CAP];
        }
        if ($quota['calls_made'] >= $quota['daily_cap']) return null;

        // Call Apollo
        $dm = $this->_apollo_find_csr_head($corp_id);
        $apollo_calls++;

        $this->db->set('calls_made', 'calls_made + 1', false)
                 ->set('credits_used', 'credits_used + 1', false)
                 ->set('last_call_at', date('Y-m-d H:i:s'))
                 ->where('quota_date', date('Y-m-d'))
                 ->update('apollo_daily_quota_v2');

        if (!$dm) return null;

        $this->db->insert('csr_decision_maker_v2', array_merge($dm, [
            'csr_corporate_id'  => $corp_id,
            'source'            => 'apollo',
            'last_synced_at'    => date('Y-m-d H:i:s'),
        ]));
        return $this->db->insert_id();
    }

    private function _apollo_find_csr_head($corp_id) {
        $key = getenv('APOLLO_API_KEY');
        if (!$key) return null;

        $corp = $this->db->where('csr_corporate_id', $corp_id)
                         ->get('csr_corporate_master_v2')->row_array();
        if (!$corp) return null;

        $payload = [
            'q_organization_name' => $corp['company_name'],
            'person_titles'       => ['CSR Head','Head of Sustainability','Foundation Trustee','CSR Manager','Head CSR'],
            'page'                => 1,
            'per_page'            => 5,
        ];

        $ch = curl_init($this->apollo_endpoint);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST           => true,
            CURLOPT_TIMEOUT        => 6,
            CURLOPT_HTTPHEADER     => [
                'Content-Type: application/json',
                'Cache-Control: no-cache',
                'X-Api-Key: ' . $key,
            ],
            CURLOPT_POSTFIELDS     => json_encode($payload),
        ]);
        $resp = curl_exec($ch);
        $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        $this->db->insert('apollo_lookup_log_v2', [
            'csr_corporate_id' => $corp_id,
            'query_payload'    => json_encode($payload),
            'response_status'  => $code,
            'contacts_returned'=> 0,
            'credits_used'     => 1,
        ]);

        if ($code !== 200 || !$resp) return null;
        $j = json_decode($resp, true);
        if (empty($j['people'][0])) return null;

        $p = $j['people'][0];
        return [
            'full_name'        => trim(($p['first_name'] ?? '') . ' ' . ($p['last_name'] ?? '')),
            'designation'      => $p['title'] ?? null,
            'designation_role' => 'csr_head',
            'email'            => $p['email'] ?? null,
            'phone'            => $p['phone_numbers'][0]['raw_number'] ?? null,
            'linkedin_url'     => $p['linkedin_url'] ?? null,
            'apollo_person_id' => $p['id'] ?? null,
        ];
    }

    private function _verify_dm_via_linkedin($dm_id) {
        // Reuse existing LinkedinCsr_model agent (migration 021)
        $this->load->model('AIAgents/LinkedinCsr_model', 'lci');

        $dm = $this->db->where('csr_dm_id', $dm_id)->get('csr_decision_maker_v2')->row_array();
        if (!$dm) return null;

        if (!empty($dm['linkedin_verified_at'])) return $dm['csr_confidence_score'];

        $res = $this->lci->verify_sync([
            'mom_id'                 => 0,
            'cid_id'                 => 0,
            'dm_contact_name'        => $dm['full_name'],
            'dm_contact_designation' => $dm['designation'],
            'dm_contact_org_type'    => 'corporate',
            'school_name'            => null,
        ]);
        $score = (int)($res['csr_intent_confidence'] ?? 0);
        $this->db->where('csr_dm_id', $dm_id)->update('csr_decision_maker_v2', [
            'csr_confidence_score' => $score,
            'linkedin_verified_at' => date('Y-m-d H:i:s'),
        ]);
        return $score;
    }

    // ============================================================
    // INFLUENCER mapping
    // ============================================================
    private function _influencer_for_district($district, $state) {
        if (!$district && !$state) return null;
        $row = $this->db->where('active', 1)
                        ->where('district', $district)
                        ->where_in('role', ['district_collector','mp','mla','education_secretary'])
                        ->order_by('FIELD(role, "district_collector","mp","mla","education_secretary")', '', false)
                        ->limit(1)
                        ->get('political_influencer_master_v2')->row_array();
        if ($row) return $row['influencer_id'];

        // Fall back to state-level
        $row = $this->db->where('active', 1)
                        ->where('state', $state)
                        ->where('role', 'education_secretary')
                        ->limit(1)
                        ->get('political_influencer_master_v2')->row_array();
        return $row ? $row['influencer_id'] : null;
    }

    // ============================================================
    // OUTREACH ANGLE selection
    // ============================================================
    private function _pick_outreach_angle($row, $dm_id, $infl_id) {
        if (in_array($row['cycle_status'], ['last_quarter','renewing'])) return 'renewal_hot';
        if ($infl_id) return 'influencer_intro';
        if ($row['has_foundation_arm']) return 'foundation_aligned';
        if (($row['csr_education_share_pct'] ?? 0) >= 40) return 'channel_partner';
        return 'new_relationship';
    }

    private function _build_blurb($row, $dm_id, $infl_id, $angle) {
        $parts = [];
        $parts[] = 'CSR spend last FY Rs ' . number_format((float)$row['csr_spent_last_fy_rs_cr'], 2) . ' cr.';
        if (!empty($row['csr_education_share_pct'])) {
            $parts[] = 'Education share ' . round($row['csr_education_share_pct']) . ' percent.';
        }
        $parts[] = 'Active project in ' . $row['project_district'] . ', theme ' . $row['project_theme'] . '.';

        switch ($angle) {
            case 'renewal_hot':
                $parts[] = 'Renewal window open. Time the pitch for next FY.';
                break;
            case 'influencer_intro':
                $parts[] = 'Warm intro via local influencer for the district.';
                break;
            case 'foundation_aligned':
                $parts[] = 'Pitch through their foundation arm.';
                break;
            case 'channel_partner':
                $parts[] = 'Heavy education CSR, propose channel partnership.';
                break;
            default:
                $parts[] = 'Cold outreach. Lead with case study.';
        }
        return implode(' ', $parts);
    }

    // ============================================================
    // SYNC csr.gov.in (called by weekly cron)
    // ============================================================
    public function sync_csr_gov($cin = null) {
        $this->db->insert('csr_gov_sync_log_v2', [
            'sync_type'  => $cin ? 'one_corporate' : 'incremental',
            'cin'        => $cin,
            'started_at' => date('Y-m-d H:i:s'),
        ]);
        $sync_id = $this->db->insert_id();

        // NOTE: full csr.gov.in scraper lives in a separate worker process due to rate limits.
        // This stub just records the sync attempt. Actual ingestion uses /api/csr_prospect/upload_csr_csv
        // or a queue worker running outside the web request lifecycle.

        $this->db->where('sync_id', $sync_id)->update('csr_gov_sync_log_v2', [
            'rows_inserted' => 0,
            'rows_updated'  => 0,
            'rows_skipped'  => 0,
            'finished_at'   => date('Y-m-d H:i:s'),
            'errors'        => 'sync queued to worker',
        ]);

        return ['ok' => true, 'sync_id' => $sync_id, 'note' => 'queued, worker handles actual fetch'];
    }
}

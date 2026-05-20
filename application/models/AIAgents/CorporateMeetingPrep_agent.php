<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * CorporateMeetingPrep_agent.php
 *
 * STEM Learning Corporate Meeting Prep Agent (Migration 042).
 *
 * Purpose:
 *   For an upcoming corporate meeting (actiontype_id 3 or 4 with a corporate lead),
 *   build a 6-lane enriched brief: 1-2 page PDF + 6-slide PPT + WhatsApp cheat sheet.
 *   The PPT is corporate-centric (about THEIR CSR thesis), STEM appears only on
 *   alignment + ask slides.
 *
 * Triggers:
 *   - Auto: cron 2h before meeting_scheduled_at (separate cron, calls auto_scan)
 *   - On-demand: BD taps a button on LeadDetailScreen or DayPlanScreen
 *
 * Six-lane enrichment waterfall:
 *   L1 internal CRM (init_call, tblcallevents, mom_data, daily_planner)
 *   L2 enrichment_cache_v2 (30-day TTL)
 *   L3 LinkedIn (via existing LinkedinCsr_model from 021)
 *   L4 Apollo.io (max plan, daily quota tracked in apollo_daily_quota_v2 from 041)
 *   L5 political_influencer_master_v2 (MP/MLA/Collector mapping)
 *   L6 csr_project_v2 (from 041 csr.gov.in seed)
 *
 * Parallel to production. Reads from existing tables, writes ONLY to _v2 tables.
 *
 * Staging only until 1 Jun 2026. Pilot 6 uids from 25 May 2026.
 */

class CorporateMeetingPrep_model extends CI_Model {

    const CACHE_TTL_DAYS         = 30;
    const TALKING_POINTS_TARGET  = 5;
    const PAST_EVENTS_LIMIT      = 6;
    const PDF_BUILDER_SCRIPT     = '/home/user/workspace/build_meeting_prep_pdf.py';
    const PPTX_BUILDER_SCRIPT    = '/home/user/workspace/build_meeting_prep_pptx.py';
    const ARTIFACT_DIR_BASE      = '/var/www/stem-meeting-prep-artifacts';
    const PILOT_UIDS             = [42, 43, 44, 45, 46, 12];
    const PILOT_START_DATE       = '2026-05-25';
    const ORG_GO_LIVE_DATE       = '2026-06-01';

    public function __construct() {
        parent::__construct();
        $this->load->database();
    }

    // ============================================================
    // PROBE (cron-friendly endpoint)
    // ============================================================
    public function probe() {
        $tables = ['meeting_prep_run_v2', 'meeting_prep_artifact_v2', 'enrichment_cache_v2'];
        foreach ($tables as $t) {
            $r = $this->db->query("SHOW TABLES LIKE '" . $this->db->escape_like_str($t) . "'");
            if ($r->num_rows() === 0) {
                return ['ok' => false, 'missing' => $t];
            }
        }
        // Also confirm 041 dependency tables exist
        $dep_tables = ['csr_corporate_master_v2', 'csr_decision_maker_v2', 'csr_project_v2'];
        foreach ($dep_tables as $t) {
            $r = $this->db->query("SHOW TABLES LIKE '" . $this->db->escape_like_str($t) . "'");
            if ($r->num_rows() === 0) {
                return ['ok' => false, 'missing_dep' => $t, 'note' => 'Migration 041 not deployed'];
            }
        }
        return ['ok' => true, 'migration' => '042', 'version' => 1];
    }

    // ============================================================
    // GENERATE one prep run for an event
    // ============================================================
    public function generate_for_event($event_id, $trigger_type = 'on_demand') {
        $event_id = (int)$event_id;
        $t_start  = microtime(true);

        // ---- Load event + lead context (L1 internal) ----
        $ctx = $this->_load_event_context($event_id);
        if (!$ctx['ok']) {
            return ['ok' => false, 'error' => $ctx['error']];
        }

        // ---- Pilot gate ----
        $today = date('Y-m-d');
        if ($today < self::ORG_GO_LIVE_DATE) {
            if ($today < self::PILOT_START_DATE) {
                return ['ok' => false, 'error' => 'pre_pilot', 'note' => 'Migration 042 dormant until 25 May 2026'];
            }
            if (!in_array((int)$ctx['bd_uid'], self::PILOT_UIDS, true)) {
                return ['ok' => false, 'error' => 'not_in_pilot', 'bd_uid' => $ctx['bd_uid']];
            }
        }

        // ---- Open run row ----
        $this->db->insert('meeting_prep_run_v2', [
            'event_id'             => $event_id,
            'cid_id'               => $ctx['cid_id'],
            'corporate_id'         => $ctx['corporate_id'],
            'bd_uid'               => $ctx['bd_uid'],
            'dm_id'                => $ctx['dm_id'],
            'trigger_type'         => $trigger_type,
            'meeting_scheduled_at' => $ctx['meeting_scheduled_at'],
            'status'               => 'running',
            'internal_pulled'      => 1,
        ]);
        $run_id = (int)$this->db->insert_id();

        $stats = [
            'internal_pulled'    => 1,
            'linkedin_used'      => 0,
            'apollo_used'        => 0,
            'influencer_matched' => 0,
            'limit_reason'       => null,
        ];

        try {
            // ---- L1 internal already in $ctx ----
            $internal = $ctx;

            // ---- L2 cache check + L3/L4 enrichment ----
            $dm_enrich = $this->_enrich_dm($ctx['dm_id'], $stats);
            $corp_enrich = $this->_enrich_corporate($ctx['corporate_id'], $stats);

            // ---- L5 political influencer match ----
            $influencer = $this->_match_influencer($ctx['corporate_id'], $stats);

            // ---- L6 CSR project history ----
            $csr_projects = $this->_csr_projects($ctx['corporate_id']);

            // ---- Compose brief structure ----
            $brief = $this->_compose_brief(
                $internal, $dm_enrich, $corp_enrich, $influencer, $csr_projects
            );

            // ---- Save brief JSON for builder scripts ----
            $brief_dir  = self::ARTIFACT_DIR_BASE . '/' . $run_id;
            @mkdir($brief_dir, 0775, true);
            $brief_json = $brief_dir . '/brief.json';
            file_put_contents($brief_json, json_encode($brief, JSON_PRETTY_PRINT));

            // ---- Generate PDF ----
            $pdf_path = $brief_dir . '/brief.pdf';
            $pdf_ok   = $this->_run_python(self::PDF_BUILDER_SCRIPT, [
                '--in', $brief_json, '--out', $pdf_path,
            ]);
            if ($pdf_ok && file_exists($pdf_path)) {
                $this->_record_artifact($run_id, 'pdf', $pdf_path);
            }

            // ---- Generate PPTX ----
            $pptx_path = $brief_dir . '/deck.pptx';
            $pptx_ok   = $this->_run_python(self::PPTX_BUILDER_SCRIPT, [
                '--in', $brief_json, '--out', $pptx_path,
            ]);
            if ($pptx_ok && file_exists($pptx_path)) {
                $this->_record_artifact($run_id, 'pptx', $pptx_path);
            }

            // ---- WhatsApp cheat sheet ----
            $wa_text = $this->_compose_whatsapp($brief);
            $wa_path = $brief_dir . '/whatsapp.txt';
            file_put_contents($wa_path, $wa_text);
            $this->_record_artifact($run_id, 'whatsapp_text', $wa_path);

            // ---- Status ----
            $final_status = ($pdf_ok && $pptx_ok) ? 'succeeded' : 'partial';
            $runtime_ms   = (int)((microtime(true) - $t_start) * 1000);
            $this->db->where('id', $run_id)->update('meeting_prep_run_v2', [
                'status'                    => $final_status,
                'internal_pulled'           => $stats['internal_pulled'],
                'linkedin_used'             => $stats['linkedin_used'],
                'apollo_used'               => $stats['apollo_used'],
                'influencer_matched'        => $stats['influencer_matched'],
                'enrichment_limited_reason' => $stats['limit_reason'],
                'runtime_ms'                => $runtime_ms,
                'completed_at'              => date('Y-m-d H:i:s'),
            ]);

            return [
                'ok'           => true,
                'run_id'       => $run_id,
                'status'       => $final_status,
                'artifacts'    => ['pdf' => $pdf_path, 'pptx' => $pptx_path, 'whatsapp' => $wa_path],
                'stats'        => $stats,
                'runtime_ms'   => $runtime_ms,
            ];

        } catch (Exception $e) {
            $this->db->where('id', $run_id)->update('meeting_prep_run_v2', [
                'status'       => 'failed',
                'error_log'    => substr($e->getMessage(), 0, 8000),
                'runtime_ms'   => (int)((microtime(true) - $t_start) * 1000),
                'completed_at' => date('Y-m-d H:i:s'),
            ]);
            return ['ok' => false, 'run_id' => $run_id, 'error' => $e->getMessage()];
        }
    }

    // ============================================================
    // AUTO SCAN: look ahead window, fire prep for meetings inside it
    // ============================================================
    public function auto_scan($lookahead_minutes = 150, $cap = 20) {
        $lookahead_minutes = (int)$lookahead_minutes;
        $cap               = max(1, min(50, (int)$cap));

        $now   = date('Y-m-d H:i:s');
        $until = date('Y-m-d H:i:s', time() + ($lookahead_minutes * 60));

        // Corporate meetings (actiontype_id 3 or 4) scheduled in the window,
        // for which we have NOT already fired a successful run.
        $sql = "
            SELECT dp.id AS planner_id, dp.event_id, dp.uid AS bd_uid,
                   dp.scheduled_at, dp.cid_id
            FROM daily_planner dp
            JOIN init_call ic ON ic.id = dp.cid_id
            WHERE dp.scheduled_at BETWEEN ? AND ?
              AND dp.actiontype_id IN (3, 4)
              AND ic.lead_type = 'corporate'
              AND NOT EXISTS (
                SELECT 1 FROM meeting_prep_run_v2 r
                WHERE r.event_id = dp.event_id AND r.status IN ('succeeded','partial','running')
              )
            ORDER BY dp.scheduled_at ASC
            LIMIT ?
        ";
        $q = $this->db->query($sql, [$now, $until, $cap]);

        $fired   = [];
        $skipped = [];
        foreach ($q->result_array() as $row) {
            $r = $this->generate_for_event((int)$row['event_id'], 'auto');
            if (!empty($r['ok'])) {
                $fired[] = ['event_id' => (int)$row['event_id'], 'run_id' => $r['run_id'], 'status' => $r['status']];
            } else {
                $skipped[] = ['event_id' => (int)$row['event_id'], 'reason' => $r['error'] ?? 'unknown'];
            }
        }
        return [
            'ok'                => true,
            'window_minutes'    => $lookahead_minutes,
            'fired_count'       => count($fired),
            'skipped_count'     => count($skipped),
            'fired'             => $fired,
            'skipped'           => $skipped,
        ];
    }

    // ============================================================
    // ARTIFACT lookup for an event (latest)
    // ============================================================
    public function artifact_for_event($event_id) {
        $event_id = (int)$event_id;
        $q = $this->db->query(
            "SELECT artifact_type, file_path, size_bytes, generated_at, run_status, bd_uid
             FROM v_latest_artifact_per_event WHERE event_id = ?",
            [$event_id]
        );
        $out = [];
        foreach ($q->result_array() as $r) {
            $out[$r['artifact_type']] = $r;
        }
        return ['ok' => true, 'event_id' => $event_id, 'artifacts' => $out];
    }

    // ============================================================
    // RUNS TODAY (per BD), for morning brief consumption
    // ============================================================
    public function runs_today($bd_uid = null) {
        if ($bd_uid) {
            $q = $this->db->query("SELECT * FROM v_meeting_prep_today WHERE bd_uid = ?", [(int)$bd_uid]);
        } else {
            $q = $this->db->query("SELECT * FROM v_meeting_prep_today");
        }
        return ['ok' => true, 'rows' => $q->result_array()];
    }

    // ============================================================
    // INTERNAL: load event context (L1)
    // ============================================================
    private function _load_event_context($event_id) {
        // From daily_planner + tblcallevents + init_call
        $q = $this->db->query("
            SELECT dp.event_id, dp.uid AS bd_uid, dp.scheduled_at, dp.cid_id, dp.purpose_id,
                   ic.corporate_id_v2 AS corporate_id, ic.dm_id_v2 AS dm_id,
                   ic.lead_type, ic.school_name, ic.fbudget,
                   u.username AS bd_name, u.uid_email AS bd_email
            FROM daily_planner dp
            JOIN init_call ic ON ic.id = dp.cid_id
            JOIN user u ON u.uid = dp.uid
            WHERE dp.event_id = ?
            LIMIT 1
        ", [$event_id]);
        if ($q->num_rows() === 0) {
            return ['ok' => false, 'error' => 'event_not_found'];
        }
        $row = $q->row_array();

        // Past events (last 6)
        $past = $this->db->query("
            SELECT event_date, actiontype_id, purpose_id, remarks, status_id
            FROM tblcallevents
            WHERE cid_id = ? AND event_date < CURDATE()
            ORDER BY event_date DESC LIMIT ?
        ", [$row['cid_id'], self::PAST_EVENTS_LIMIT])->result_array();

        // Past MoMs (last 3)
        $moms = $this->db->query("
            SELECT mom_id, mom_date, summary, approved
            FROM mom_data
            WHERE cid_id = ? ORDER BY mom_date DESC LIMIT 3
        ", [$row['cid_id']])->result_array();

        return [
            'ok'                   => true,
            'event_id'             => (int)$event_id,
            'cid_id'               => (int)$row['cid_id'],
            'corporate_id'         => $row['corporate_id'] ? (int)$row['corporate_id'] : null,
            'dm_id'                => $row['dm_id'] ? (int)$row['dm_id'] : null,
            'bd_uid'               => (int)$row['bd_uid'],
            'bd_name'              => $row['bd_name'],
            'bd_email'             => $row['bd_email'],
            'meeting_scheduled_at' => $row['scheduled_at'],
            'purpose_id'           => $row['purpose_id'],
            'lead_type'            => $row['lead_type'],
            'school_name'          => $row['school_name'],
            'fbudget'              => $row['fbudget'],
            'past_events'          => $past,
            'past_moms'            => $moms,
        ];
    }

    // ============================================================
    // INTERNAL: enrich DM (L2 cache -> L3 LinkedIn -> L4 Apollo)
    // ============================================================
    private function _enrich_dm($dm_id, &$stats) {
        if (!$dm_id) {
            return ['ok' => false, 'reason' => 'no_dm_id'];
        }

        // L2 cache check
        $cached = $this->_cache_get('dm', $dm_id);
        $merged = ['cached_sources' => array_keys($cached)];

        // Base DM row from 041
        $dm_row = $this->db->query(
            "SELECT * FROM csr_decision_maker_v2 WHERE id = ? LIMIT 1", [$dm_id]
        )->row_array();
        if ($dm_row) {
            $merged['base'] = $dm_row;
        }

        // L3 LinkedIn (if not cached) -- use existing LinkedinCsr_model
        if (!isset($cached['linkedin']) && $dm_row && !empty($dm_row['linkedin_url'])) {
            $li = $this->_call_linkedin_model($dm_row);
            if ($li && !empty($li['ok'])) {
                $merged['linkedin'] = $li['profile'];
                $this->_cache_put('dm', $dm_id, 'linkedin', $li['profile'], $li['confidence'] ?? null);
                $stats['linkedin_used'] = 1;
            }
        } elseif (isset($cached['linkedin'])) {
            $merged['linkedin'] = $cached['linkedin'];
        }

        // L4 Apollo (if no email/phone yet, and quota allows)
        $need_apollo = empty($dm_row['email']) || empty($dm_row['phone']);
        if ($need_apollo && !isset($cached['apollo'])) {
            if ($this->_apollo_quota_available()) {
                $apo = $this->_call_apollo($dm_row);
                if ($apo && !empty($apo['ok'])) {
                    $merged['apollo'] = $apo['data'];
                    $this->_cache_put('dm', $dm_id, 'apollo', $apo['data'], $apo['confidence'] ?? null);
                    $stats['apollo_used'] = 1;
                    $this->_apollo_quota_consume();
                }
            } else {
                $stats['limit_reason'] = 'apollo_quota_exhausted';
            }
        } elseif (isset($cached['apollo'])) {
            $merged['apollo'] = $cached['apollo'];
        }

        return ['ok' => true, 'data' => $merged];
    }

    // ============================================================
    // INTERNAL: enrich corporate (cache + base)
    // ============================================================
    private function _enrich_corporate($corporate_id, &$stats) {
        if (!$corporate_id) {
            return ['ok' => false, 'reason' => 'no_corporate_id'];
        }
        $cached = $this->_cache_get('corporate', $corporate_id);
        $merged = [];

        $row = $this->db->query(
            "SELECT * FROM csr_corporate_master_v2 WHERE id = ? LIMIT 1", [$corporate_id]
        )->row_array();
        if ($row) {
            $merged['base'] = $row;
        }
        if (isset($cached['linkedin'])) {
            $merged['linkedin'] = $cached['linkedin'];
        }
        return ['ok' => true, 'data' => $merged];
    }

    // ============================================================
    // INTERNAL: political influencer match (L5)
    // ============================================================
    private function _match_influencer($corporate_id, &$stats) {
        if (!$corporate_id) return null;
        $r = $this->db->query("
            SELECT pi.id, pi.name, pi.designation, pi.constituency, pi.party, pi.state
            FROM political_influencer_master_v2 pi
            JOIN csr_corporate_master_v2 c ON c.id = ?
            WHERE (pi.state = c.hq_state OR pi.constituency LIKE CONCAT('%', c.hq_city, '%'))
            ORDER BY
              CASE pi.designation
                WHEN 'collector' THEN 1
                WHEN 'mp' THEN 2
                WHEN 'mla' THEN 3
                ELSE 9 END
            LIMIT 3
        ", [$corporate_id])->result_array();
        if (!empty($r)) {
            $stats['influencer_matched'] = 1;
        }
        return $r;
    }

    // ============================================================
    // INTERNAL: CSR projects history (L6)
    // ============================================================
    private function _csr_projects($corporate_id) {
        if (!$corporate_id) return [];
        return $this->db->query("
            SELECT fy, project_name, sector, state, district, spend_rs, beneficiaries
            FROM csr_project_v2
            WHERE corporate_id = ?
            ORDER BY fy DESC, spend_rs DESC LIMIT 10
        ", [$corporate_id])->result_array();
    }

    // ============================================================
    // INTERNAL: compose brief structure (consumed by PDF + PPTX scripts)
    // ============================================================
    private function _compose_brief($internal, $dm_enrich, $corp_enrich, $influencer, $csr_projects) {
        $corp_base = $corp_enrich['data']['base'] ?? [];
        $dm_base   = $dm_enrich['data']['base']   ?? [];
        $dm_li     = $dm_enrich['data']['linkedin'] ?? [];

        // CSR thesis: derive from top sectors in their project history
        $sector_spend = [];
        foreach ($csr_projects as $p) {
            $s = $p['sector'] ?: 'unspecified';
            $sector_spend[$s] = ($sector_spend[$s] ?? 0) + (float)$p['spend_rs'];
        }
        arsort($sector_spend);
        $top_sectors = array_slice(array_keys($sector_spend), 0, 3);

        // STEM alignment angles based on top sector
        $alignment_angles = $this->_alignment_angles($top_sectors, $corp_base);

        // 5 talking points: question + so-what each
        $talking_points = $this->_talking_points(
            $corp_base, $dm_base, $dm_li, $csr_projects, $influencer
        );

        // Red flags
        $red_flags = $this->_red_flags($internal, $corp_base, $dm_base);

        return [
            'generated_at'   => date('c'),
            'event_id'       => $internal['event_id'],
            'meeting_at'     => $internal['meeting_scheduled_at'],
            'bd_name'        => $internal['bd_name'],
            'corporate'      => [
                'name'            => $corp_base['name'] ?? $internal['school_name'],
                'sector'          => $corp_base['sector'] ?? null,
                'hq_city'         => $corp_base['hq_city'] ?? null,
                'hq_state'        => $corp_base['hq_state'] ?? null,
                'csr_spend_fy25'  => $corp_base['csr_spend_fy25_rs'] ?? null,
                'csr_spend_fy26'  => $corp_base['csr_spend_fy26_rs'] ?? null,
                'education_share' => $corp_base['education_share_pct'] ?? null,
                'top_sectors'     => $top_sectors,
            ],
            'dm'             => [
                'name'        => $dm_base['name'] ?? null,
                'designation' => $dm_base['designation'] ?? null,
                'tenure'      => $dm_li['tenure_years'] ?? null,
                'background'  => $dm_li['summary'] ?? null,
                'recent_post' => $dm_li['recent_post'] ?? null,
                'email'       => $dm_enrich['data']['apollo']['email'] ?? ($dm_base['email'] ?? null),
                'phone'       => $dm_enrich['data']['apollo']['phone'] ?? ($dm_base['phone'] ?? null),
            ],
            'csr_projects'      => $csr_projects,
            'past_events'       => $internal['past_events'],
            'past_moms'         => $internal['past_moms'],
            'influencers'       => $influencer ?: [],
            'alignment_angles'  => $alignment_angles,
            'talking_points'    => $talking_points,
            'the_ask'           => $this->_compose_ask($internal, $corp_base, $csr_projects),
            'proposal_shape'    => $this->_compose_proposal($corp_base, $csr_projects, $top_sectors),
            'red_flags'         => $red_flags,
        ];
    }

    private function _alignment_angles($top_sectors, $corp_base) {
        $angles = [];
        foreach ($top_sectors as $s) {
            $key = strtolower($s);
            if (strpos($key, 'education') !== false || strpos($key, 'school') !== false) {
                $angles[] = 'STEM labs slot directly into their existing education vertical. No new sector commitment needed on their side.';
            } elseif (strpos($key, 'skill') !== false || strpos($key, 'livelihood') !== false) {
                $angles[] = 'STEM ATL/STEM lab outcomes feed their skilling thesis with measurable student counts and project completion data.';
            } elseif (strpos($key, 'health') !== false) {
                $angles[] = 'Adjacent fit: STEM hygiene + health-tech modules can ride alongside their health spend in the same school network.';
            } elseif (strpos($key, 'rural') !== false || strpos($key, 'community') !== false) {
                $angles[] = 'STEM lab in a rural school becomes a community anchor asset, multiplies visibility of their CSR footprint.';
            } else {
                $angles[] = 'STEM lab is a multi-year, photogenic, measurable asset that compounds their existing CSR narrative.';
            }
        }
        if (empty($angles)) {
            $angles[] = 'Position STEM lab as a discrete, measurable CSR asset that pairs with whatever sector they prioritise.';
        }
        return array_slice($angles, 0, 4);
    }

    private function _talking_points($corp, $dm, $dm_li, $projects, $influencer) {
        $tps = [];

        // 1. Their CSR thesis recap
        if (!empty($projects)) {
            $top = $projects[0];
            $tps[] = [
                'point'    => 'You spent Rs ' . $this->_lakh($top['spend_rs']) . ' on ' . $top['project_name'] . ' in ' . $top['state'] . ' in FY' . $top['fy'] . '.',
                'so_what'  => 'Anchors the conversation in their language. Shows we did our homework.',
                'question' => 'What did you learn from ' . $top['project_name'] . ' that shapes where you go next?',
            ];
        }

        // 2. Education share angle
        if (!empty($corp['education_share_pct'])) {
            $tps[] = [
                'point'    => 'Education is ' . round($corp['education_share_pct']) . ' percent of your CSR mix.',
                'so_what'  => 'Either grow that share (if low) or deepen impact within it (if high).',
                'question' => 'What is the gap you most want to close in your education spend this year?',
            ];
        }

        // 3. Geography / influencer hook
        if (!empty($influencer)) {
            $inf = $influencer[0];
            $tps[] = [
                'point'    => $inf['designation'] . ' ' . $inf['name'] . ' in ' . ($inf['constituency'] ?: $inf['state']) . ' is a known supporter of STEM lab installs in district schools.',
                'so_what'  => 'A named-lab announcement becomes a political win as well as a CSR win.',
                'question' => 'Do you have an existing relationship with ' . $inf['name'] . ' that we could ride?',
            ];
        }

        // 4. DM tenure / recent post hook
        if (!empty($dm_li['recent_post'])) {
            $tps[] = [
                'point'    => 'You posted about ' . substr($dm_li['recent_post'], 0, 80) . ' recently.',
                'so_what'  => 'Personal signal of what they care about right now.',
                'question' => 'How does that thread connect to where you want CSR to land in the next 18 months?',
            ];
        }

        // 5. Default: scale / replication
        $tps[] = [
            'point'    => 'STEM has installed labs in ' . ($corp['hq_state'] ?? 'multiple states') . ' that are still producing student outcomes 3 to 5 years on.',
            'so_what'  => 'Pre-empts the durability question.',
            'question' => 'What does success look like to you 24 months from now?',
        ];

        return array_slice($tps, 0, self::TALKING_POINTS_TARGET);
    }

    private function _compose_ask($internal, $corp, $csr_projects) {
        // Default ask is a site visit at a STEM reference school + budget signal
        return [
            'one_line' => 'Site visit to a working STEM lab within the next 30 days plus an indicative budget conversation.',
            'fallback' => 'If site visit is not possible, ask for an introduction to the CSR committee secretary.',
        ];
    }

    private function _compose_proposal($corp, $csr_projects, $top_sectors) {
        $typical_spend = 0;
        if (!empty($csr_projects)) {
            $vals = array_map(function ($p) { return (float)$p['spend_rs']; }, $csr_projects);
            sort($vals);
            $typical_spend = $vals[(int)(count($vals) / 2)];
        }
        $lab_count = $typical_spend > 0 ? max(1, (int)round($typical_spend / 1500000.0)) : 5;
        return [
            'shape'     => $lab_count . ' STEM labs in ' . ($corp['hq_state'] ?? 'a target district') . ' over 12 months.',
            'rs_total'  => $lab_count * 1500000,
            'rationale' => 'Sized to match their typical per-project ticket. Lab count adjustable. Includes teacher training and 2-year impact reporting.',
        ];
    }

    private function _red_flags($internal, $corp, $dm) {
        $flags = [];
        // Past meeting without MoM
        if (count($internal['past_events']) > 0 && empty($internal['past_moms'])) {
            $flags[] = 'Prior meetings logged but no MoM on record. Possible coverage gap; lead with curiosity, not assumptions.';
        }
        // High CSR spend but low education share
        if (!empty($corp['csr_spend_fy26_rs']) && !empty($corp['education_share_pct']) && $corp['education_share_pct'] < 10) {
            $flags[] = 'Education is under 10 percent of their CSR mix. Do not pitch education as their priority; pitch it as adjacency.';
        }
        // DM contact missing
        if (empty($dm['email']) && empty($dm['phone'])) {
            $flags[] = 'No verified DM contact on file. Confirm spelling of name + designation in opening minute.';
        }
        return $flags;
    }

    private function _compose_whatsapp($brief) {
        $lines = [];
        $corp  = $brief['corporate']['name'] ?? 'Corporate';
        $dm    = $brief['dm']['name'] ?? 'DM';
        $when  = date('h:i A', strtotime($brief['meeting_at']));
        $lines[] = 'Meet ' . $dm . ' at ' . $corp . ' today ' . $when . '.';
        if (!empty($brief['talking_points'][0]['point'])) {
            $lines[] = 'Open: ' . $brief['talking_points'][0]['point'];
        }
        if (!empty($brief['the_ask']['one_line'])) {
            $lines[] = 'Ask: ' . $brief['the_ask']['one_line'];
        }
        if (!empty($brief['red_flags'][0])) {
            $lines[] = 'Watch: ' . $brief['red_flags'][0];
        }
        $text = implode("\n", $lines);
        return substr($text, 0, 300);
    }

    // ============================================================
    // Helpers: cache, Apollo quota, external calls
    // ============================================================
    private function _cache_get($entity_type, $entity_id) {
        $q = $this->db->query("
            SELECT source, payload_json
            FROM enrichment_cache_v2
            WHERE entity_type = ? AND entity_id = ? AND expires_at > NOW()
        ", [$entity_type, $entity_id]);
        $out = [];
        foreach ($q->result_array() as $r) {
            $out[$r['source']] = json_decode($r['payload_json'], true);
        }
        return $out;
    }

    private function _cache_put($entity_type, $entity_id, $source, $payload, $confidence = null) {
        $expires = date('Y-m-d H:i:s', time() + (self::CACHE_TTL_DAYS * 86400));
        $this->db->query("
            INSERT INTO enrichment_cache_v2 (entity_type, entity_id, source, payload_json, confidence, expires_at)
            VALUES (?, ?, ?, ?, ?, ?)
            ON DUPLICATE KEY UPDATE
              payload_json = VALUES(payload_json),
              confidence   = VALUES(confidence),
              fetched_at   = NOW(),
              expires_at   = VALUES(expires_at)
        ", [$entity_type, $entity_id, $source, json_encode($payload), $confidence, $expires]);
    }

    private function _apollo_quota_available() {
        // Migration 041 manages apollo_daily_quota_v2. Max plan; consumed counter only.
        $r = $this->db->query("
            SELECT used_today, daily_quota
            FROM apollo_daily_quota_v2
            WHERE quota_date = CURDATE() LIMIT 1
        ");
        if ($r->num_rows() === 0) return true;
        $row = $r->row_array();
        return ((int)$row['used_today'] < (int)$row['daily_quota']);
    }

    private function _apollo_quota_consume() {
        $this->db->query("
            INSERT INTO apollo_daily_quota_v2 (quota_date, used_today, daily_quota)
            VALUES (CURDATE(), 1, 600)
            ON DUPLICATE KEY UPDATE used_today = used_today + 1
        ");
    }

    private function _call_linkedin_model($dm_row) {
        if (!class_exists('LinkedinCsr_model')) {
            @$this->load->model('AIAgents/LinkedinCsr_model');
        }
        if (!method_exists($this->LinkedinCsr_model ?? null, 'fetch_profile')) {
            return null;
        }
        try {
            return $this->LinkedinCsr_model->fetch_profile($dm_row['linkedin_url']);
        } catch (Exception $e) {
            return null;
        }
    }

    private function _call_apollo($dm_row) {
        // Delegate to existing 041 helper if present
        if (!class_exists('CorporateCsrProspect_model')) {
            @$this->load->model('AIAgents/CorporateCsrProspect_agent', 'CorporateCsrProspect_model');
        }
        if (method_exists($this->CorporateCsrProspect_model ?? null, 'apollo_lookup_person')) {
            try {
                return $this->CorporateCsrProspect_model->apollo_lookup_person(
                    $dm_row['name'], $dm_row['company_name'] ?? null
                );
            } catch (Exception $e) {
                return null;
            }
        }
        return null;
    }

    private function _record_artifact($run_id, $type, $path) {
        $size = file_exists($path) ? filesize($path) : 0;
        $this->db->insert('meeting_prep_artifact_v2', [
            'run_id'        => $run_id,
            'artifact_type' => $type,
            'file_path'     => $path,
            'size_bytes'    => $size,
        ]);
    }

    private function _run_python($script, $args) {
        $cmd = 'python3 ' . escapeshellarg($script);
        foreach ($args as $a) {
            $cmd .= ' ' . escapeshellarg($a);
        }
        $cmd .= ' 2>&1';
        exec($cmd, $out, $rc);
        if ($rc !== 0) {
            log_message('error', '[042] python script failed: ' . $cmd . ' rc=' . $rc . ' out=' . implode("\n", $out));
            return false;
        }
        return true;
    }

    private function _lakh($rs) {
        $rs = (float)$rs;
        if ($rs >= 10000000) return number_format($rs / 10000000, 2) . ' cr';
        if ($rs >= 100000)   return number_format($rs / 100000, 2) . ' lakh';
        return number_format($rs);
    }
}

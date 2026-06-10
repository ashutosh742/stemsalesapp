<?php
/**
 * ObjectionMining_model.php
 *
 * Migration 052 - Objection Mining from Call Transcripts
 * Location: application/models/AIAgents/ObjectionMining_model.php
 *
 * Responsibilities:
 *   1. Weekly batch: pull M025 transcripts for the past 7 days (pilot-filtered).
 *   2. Extract objection phrases via LLM (batch up to 50 transcripts per call,
 *      temperature=0, cost target under Rs 2 per transcript).
 *   3. Assign each phrase to a canonical objection theme using the
 *      objection_phrase_cache first (determinism guarantee) then LLM fallback.
 *   4. Aggregate weekly counts into objection_weekly_aggregate.
 *   5. Upsert objection_lead_blocker rows for leads at cstatus 6+.
 *   6. Auto-create top-5 KB candidates in M036 knowledge_repository_row.
 *   7. Push BD coaching cards via M027 comm_orchestrator.
 *   8. Write objection_mining_run_log.
 *
 * Cron: rhythm_orchestrator, Sundays 23:00 IST (new weekly slot).
 * On-demand: called by ObjectionMiningController::extract_for_meeting().
 *
 * Standing rules:
 *   - Plain English, no em-dashes, no non-ASCII in code
 *   - All LLM calls use temperature=0 for determinism
 *   - Batch size max 50 for both extraction and assignment
 *   - Feature flag: 0 off, 1 pilot WB BDs only, 2 org-wide
 *   - Pilot BD uids: 1000289, 1000351
 */

defined('BASEPATH') OR exit('No direct script access allowed');

class ObjectionMining_model extends CI_Model
{
    // -----------------------------------------------------------------
    // Constants
    // -----------------------------------------------------------------

    const FLAG_CODE      = 'objection_mining_052_enabled';
    const FLAG_OFF       = 0;
    const FLAG_PILOT     = 1;
    const FLAG_ORG       = 2;

    const PILOT_BD_UIDS  = [1000289, 1000351];
    const BATCH_SIZE     = 50;       // max transcripts or phrases per LLM call
    const MIN_CONFIDENCE = 50;       // phrases below this extraction confidence are skipped
    const COACHING_FLAG_PCT = 60;    // BD coaching flag threshold: 60 percent meetings with theme
    const BLOCKER_MIN_CSTATUS = 6;   // only track blockers at Positive or above
    const KB_CANDIDATE_TOP_N = 5;    // auto-create top N KB candidates per run

    // LLM model and cost config
    const LLM_MODEL          = 'gpt-4o-mini'; // fallback default; override via config
    const LLM_TEMP           = 0;
    const MAX_TRANSCRIPT_CHARS = 4000; // truncate long transcripts before sending to LLM
    const MAX_PHRASES_PER_MEETING = 20;

    // -----------------------------------------------------------------
    // Public entry points
    // -----------------------------------------------------------------

    /**
     * run_weekly_batch
     * Called by rhythm_orchestrator on Sundays 23:00 IST.
     *
     * @param string $window_start  MySQL datetime, start of 7-day window
     * @param string $window_end    MySQL datetime, end of 7-day window
     * @return array run summary
     */
    public function run_weekly_batch($window_start, $window_end)
    {
        $flag = $this->_get_flag();
        if ($flag === self::FLAG_OFF) {
            return ['status' => 'skipped', 'reason' => 'feature flag is 0'];
        }

        $iso_week  = intval(date('oW', strtotime($window_start)));
        $run_id    = $this->_open_run_log($iso_week, $flag);
        $stats     = $this->_fresh_stats();

        try {
            // Step 1: pull transcripts
            $transcripts = $this->_fetch_transcripts($window_start, $window_end, $flag);
            $stats['transcripts_scanned'] = count($transcripts);

            // Step 2: extract objection phrases in batches
            $all_phrases = [];
            foreach (array_chunk($transcripts, self::BATCH_SIZE) as $batch) {
                $extracted = $this->_extract_phrases_batch($batch, $run_id, $stats);
                $all_phrases = array_merge($all_phrases, $extracted);
            }

            // Step 3: assign themes (cache first, then LLM)
            $this->_assign_themes_batch($all_phrases, $run_id, $stats);

            // Step 4: aggregate weekly counts
            $this->_upsert_weekly_aggregate($iso_week, $run_id);

            // Step 5: upsert lead blockers
            $this->_upsert_lead_blockers($all_phrases, $iso_week);

            // Step 6: auto-create KB candidates (top 5)
            $stats['kb_candidates_created'] = $this->_auto_create_kb_candidates();

            // Step 7: push BD coaching cards via M027
            $stats['coaching_cards_sent'] = $this->_push_coaching_cards();

            // Step 8: mark transcripts processed
            $this->_mark_transcripts_done($transcripts);

            $this->_close_run_log($run_id, 'completed', $stats);

        } catch (Exception $e) {
            $stats['errors_count']++;
            $stats['errors'][] = $e->getMessage();
            $this->_close_run_log($run_id, 'failed', $stats);
            log_message('error', 'ObjectionMining run_weekly_batch failed: ' . $e->getMessage());
        }

        return ['status' => 'completed', 'run_id' => $run_id, 'stats' => $stats];
    }


    /**
     * extract_for_meeting
     * On-demand extraction for a single transcript, called from the controller.
     *
     * @param int $session_id  audio_capture_transcript session_id
     * @return array extracted phrases with theme assignments
     */
    public function extract_for_meeting($session_id)
    {
        $flag = $this->_get_flag();
        if ($flag === self::FLAG_OFF) {
            return ['status' => 'disabled'];
        }

        $transcript = $this->_fetch_single_transcript($session_id, $flag);
        if (empty($transcript)) {
            return ['status' => 'not_found_or_not_permitted'];
        }

        $stats   = $this->_fresh_stats();
        $run_id  = null; // on-demand run, no run log row
        $phrases = $this->_extract_phrases_batch([$transcript], $run_id, $stats);
        $this->_assign_themes_batch($phrases, $run_id, $stats);

        $iso_week = intval(date('oW'));
        $this->_upsert_weekly_aggregate($iso_week, $run_id);
        $this->_upsert_lead_blockers($phrases, $iso_week);
        $this->_mark_transcripts_done([$transcript]);

        return [
            'status'  => 'ok',
            'phrases' => $phrases,
            'stats'   => $stats,
        ];
    }


    // -----------------------------------------------------------------
    // Step 1: Fetch transcripts
    // -----------------------------------------------------------------

    private function _fetch_transcripts($window_start, $window_end, $flag)
    {
        $this->db->select(
            'act.session_id, act.event_id, act.cid_id, act.actor_uid,
             act.transcript_text, act.transcript_created_at'
        );
        $this->db->from('audio_capture_transcript act');
        $this->db->where('act.transcript_created_at >=', $window_start);
        $this->db->where('act.transcript_created_at <=', $window_end);
        $this->db->where('act.is_mining_done', 0);
        $this->db->where('act.transcript_text !=', '');

        if ($flag === self::FLAG_PILOT) {
            $this->db->where_in('act.actor_uid', self::PILOT_BD_UIDS);
        }

        return $this->db->get()->result_array();
    }


    private function _fetch_single_transcript($session_id, $flag)
    {
        $this->db->select(
            'act.session_id, act.event_id, act.cid_id, act.actor_uid,
             act.transcript_text, act.transcript_created_at'
        );
        $this->db->from('audio_capture_transcript act');
        $this->db->where('act.session_id', $session_id);

        if ($flag === self::FLAG_PILOT) {
            $this->db->where_in('act.actor_uid', self::PILOT_BD_UIDS);
        }

        return $this->db->get()->row_array();
    }


    // -----------------------------------------------------------------
    // Step 2: Extract objection phrases (batch LLM call)
    // -----------------------------------------------------------------

    /**
     * _extract_phrases_batch
     * Sends up to 50 transcripts in a single LLM call.
     * Returns array of phrase rows suitable for DB insert.
     */
    private function _extract_phrases_batch($transcripts, $run_id, &$stats)
    {
        if (empty($transcripts)) {
            return [];
        }

        $payload_items = [];
        foreach ($transcripts as $t) {
            $payload_items[] = [
                'session_id'   => $t['session_id'],
                'transcript'   => mb_substr($t['transcript_text'], 0, self::MAX_TRANSCRIPT_CHARS),
            ];
        }

        $system_prompt = $this->_extraction_system_prompt();
        $user_content  = json_encode(['meetings' => $payload_items], JSON_UNESCAPED_UNICODE);

        $llm_result = $this->_call_llm($system_prompt, $user_content, $stats, 'extraction');
        if ($llm_result === null) {
            return [];
        }

        $phrase_rows = [];
        $decoded     = json_decode($llm_result['content'], true);
        if (!is_array($decoded) || empty($decoded['results'])) {
            return [];
        }

        // Build a session_id-to-transcript lookup
        $meta = [];
        foreach ($transcripts as $t) {
            $meta[$t['session_id']] = $t;
        }

        foreach ($decoded['results'] as $result) {
            $sid   = intval($result['session_id'] ?? 0);
            $tdata = $meta[$sid] ?? null;
            if (!$tdata) {
                continue;
            }
            $phrases = $result['phrases'] ?? [];
            foreach ($phrases as $p) {
                $confidence = intval($p['extraction_confidence'] ?? 0);
                if ($confidence < self::MIN_CONFIDENCE) {
                    continue;
                }
                $phrase_rows[] = [
                    'session_id'            => $sid,
                    'event_id'              => $tdata['event_id'],
                    'cid_id'                => $tdata['cid_id'],
                    'actor_uid'             => $tdata['actor_uid'],
                    'iso_week'              => intval(date('oW', strtotime($tdata['transcript_created_at']))),
                    'raw_phrase'            => mb_substr($p['phrase'] ?? '', 0, 255),
                    'speaker_role_inferred' => $p['speaker_role'] ?? 'UNKNOWN',
                    'extraction_confidence' => $confidence,
                    'is_valid'              => 1,
                    'run_id'                => $run_id,
                ];
            }
        }

        // Bulk insert phrase rows
        $inserted_ids = [];
        foreach ($phrase_rows as &$row) {
            $this->db->insert('objection_phrase', $row);
            $row['id'] = $this->db->insert_id();
            $inserted_ids[] = $row['id'];
        }
        unset($row);

        $stats['phrases_extracted'] += count($phrase_rows);
        return $phrase_rows;
    }


    private function _extraction_system_prompt()
    {
        return <<<'PROMPT'
You are an objection-extraction AI for a STEM education sales CRM.
You will receive a JSON object with a "meetings" array. Each element has
"session_id" (integer) and "transcript" (text of an ASR meeting transcript).

For each meeting, identify sentences or fragments where a PROSPECT (not the BD)
raises a resistance, concern, hesitation, condition, or refusal.

Rules:
1. Extract the verbatim phrase (max 60 chars) or a one-sentence paraphrase.
2. Do NOT extract BD statements, even if the BD paraphrases an objection.
3. Limit to 20 phrases per meeting. If no objection is found, return an empty array.
4. For each phrase provide:
   - phrase: string (max 60 chars)
   - speaker_role: PROSPECT | DM | PRINCIPAL | UNKNOWN
   - extraction_confidence: integer 0-100 (your confidence this is a genuine objection)
5. Return ONLY valid JSON in this format:
{
  "results": [
    {
      "session_id": <integer>,
      "phrases": [
        { "phrase": "...", "speaker_role": "...", "extraction_confidence": 85 }
      ]
    }
  ]
}
PROMPT;
    }


    // -----------------------------------------------------------------
    // Step 3: Assign themes (cache first, LLM fallback)
    // -----------------------------------------------------------------

    private function _assign_themes_batch($phrase_rows, $run_id, &$stats)
    {
        if (empty($phrase_rows)) {
            return;
        }

        $need_llm = [];
        foreach ($phrase_rows as $row) {
            if (empty($row['id'])) {
                continue;
            }
            $normalised = $this->_normalise_phrase($row['raw_phrase']);
            $cached     = $this->_cache_lookup($normalised);

            if ($cached !== null) {
                // Use cache
                $this->db->insert('objection_theme_assignment', [
                    'phrase_id'           => $row['id'],
                    'theme_code'          => $cached['theme_code'],
                    'assignment_confidence' => 100,
                    'assigned_from_cache' => 1,
                    'llm_model'           => '',
                    'llm_cost_inr'        => 0,
                ]);
                $this->db->set('use_count', 'use_count + 1', false);
                $this->db->where('id', $cached['id']);
                $this->db->update('objection_phrase_cache');
                $stats['phrases_cache_hit']++;

            } else {
                $need_llm[] = [
                    'phrase_id'   => $row['id'],
                    'raw_phrase'  => $row['raw_phrase'],
                    'normalised'  => $normalised,
                ];
            }
        }

        // Batch LLM assignment for uncached phrases
        foreach (array_chunk($need_llm, self::BATCH_SIZE) as $batch) {
            $this->_assign_themes_llm_batch($batch, $run_id, $stats);
        }
    }


    private function _assign_themes_llm_batch($batch, $run_id, &$stats)
    {
        $themes   = $this->_get_active_themes();
        $theme_descs = [];
        foreach ($themes as $t) {
            $theme_descs[] = $t['theme_code'] . ': ' . $t['theme_label'] . '. Examples: ' . $t['example_phrases'];
        }

        $system_prompt = "You are a theme-classification AI. Assign each objection phrase to exactly one theme code.\n"
            . "Available themes:\n" . implode("\n", $theme_descs) . "\n"
            . "If no theme fits, use UNCATEGORISED.\n"
            . "Return ONLY valid JSON: { \"assignments\": [ { \"phrase_id\": <int>, \"theme_code\": \"...\", \"confidence\": 0-100 } ] }";

        $phrases_input = array_map(function($b) {
            return ['phrase_id' => $b['phrase_id'], 'phrase' => $b['raw_phrase']];
        }, $batch);

        $user_content = json_encode(['phrases' => $phrases_input]);
        $llm_result   = $this->_call_llm($system_prompt, $user_content, $stats, 'assignment');

        if ($llm_result === null) {
            return;
        }

        $decoded = json_decode($llm_result['content'], true);
        if (!is_array($decoded) || empty($decoded['assignments'])) {
            return;
        }

        // Build lookup for normalised phrases
        $normalised_map = [];
        foreach ($batch as $b) {
            $normalised_map[$b['phrase_id']] = $b['normalised'];
        }

        $cost_per_phrase = $llm_result['cost_inr'] / max(count($batch), 1);

        foreach ($decoded['assignments'] as $a) {
            $phrase_id   = intval($a['phrase_id']   ?? 0);
            $theme_code  = $a['theme_code']          ?? 'UNCATEGORISED';
            $confidence  = intval($a['confidence']   ?? 0);

            $this->db->insert('objection_theme_assignment', [
                'phrase_id'             => $phrase_id,
                'theme_code'            => $theme_code,
                'assignment_confidence' => $confidence,
                'assigned_from_cache'   => 0,
                'llm_model'             => self::LLM_MODEL,
                'llm_cost_inr'          => round($cost_per_phrase, 4),
            ]);

            // Populate cache for future runs
            if (isset($normalised_map[$phrase_id])) {
                $normalised = $normalised_map[$phrase_id];
                $this->db->insert_or_update('objection_phrase_cache', [
                    'normalised_phrase' => $normalised,
                    'theme_code'        => $theme_code,
                    'first_seen_at'     => date('Y-m-d H:i:s'),
                    'last_seen_at'      => date('Y-m-d H:i:s'),
                    'use_count'         => 1,
                ]);
            }

            if ($theme_code === 'UNCATEGORISED') {
                $stats['uncategorised_count']++;
            }
            $stats['themes_assigned']++;
            $stats['phrases_llm_called']++;
        }

        $stats['llm_assignment_calls']++;
    }


    // -----------------------------------------------------------------
    // Step 4: Upsert weekly aggregate
    // -----------------------------------------------------------------

    private function _upsert_weekly_aggregate($iso_week, $run_id)
    {
        // Aggregate per (iso_week, theme_code, actor_uid)
        $sql = "
            INSERT INTO objection_weekly_aggregate
                (iso_week, theme_code, actor_uid, cluster_id, occurrence_count, meetings_count, run_id)
            SELECT
                op.iso_week,
                ota.theme_code,
                op.actor_uid,
                u.cluster_master_id,
                COUNT(op.id)                       AS occurrence_count,
                COUNT(DISTINCT op.session_id)       AS meetings_count,
                ?
            FROM objection_phrase op
            JOIN objection_theme_assignment ota ON ota.phrase_id = op.id
            LEFT JOIN user u ON u.uid = op.actor_uid
            WHERE op.iso_week = ?
              AND op.is_valid = 1
            GROUP BY op.iso_week, ota.theme_code, op.actor_uid, u.cluster_master_id
            ON DUPLICATE KEY UPDATE
                occurrence_count = VALUES(occurrence_count),
                meetings_count   = VALUES(meetings_count),
                run_id           = VALUES(run_id),
                updated_at       = NOW()
        ";
        $this->db->query($sql, [$run_id, $iso_week]);

        // Also write cluster-level rollup rows (actor_uid IS NULL)
        $sql_cluster = "
            INSERT INTO objection_weekly_aggregate
                (iso_week, theme_code, actor_uid, cluster_id, occurrence_count, meetings_count, run_id)
            SELECT
                op.iso_week,
                ota.theme_code,
                NULL,
                u.cluster_master_id,
                COUNT(op.id),
                COUNT(DISTINCT op.session_id),
                ?
            FROM objection_phrase op
            JOIN objection_theme_assignment ota ON ota.phrase_id = op.id
            LEFT JOIN user u ON u.uid = op.actor_uid
            WHERE op.iso_week = ?
              AND op.is_valid = 1
              AND u.cluster_master_id IS NOT NULL
            GROUP BY op.iso_week, ota.theme_code, u.cluster_master_id
            ON DUPLICATE KEY UPDATE
                occurrence_count = VALUES(occurrence_count),
                meetings_count   = VALUES(meetings_count),
                run_id           = VALUES(run_id),
                updated_at       = NOW()
        ";
        $this->db->query($sql_cluster, [$run_id, $iso_week]);
    }


    // -----------------------------------------------------------------
    // Step 5: Upsert lead blockers
    // -----------------------------------------------------------------

    private function _upsert_lead_blockers($phrase_rows, $iso_week)
    {
        if (empty($phrase_rows)) {
            return;
        }

        // Get cstatus for each cid_id that appears in this batch
        $cid_ids = array_unique(array_filter(array_column($phrase_rows, 'cid_id')));
        if (empty($cid_ids)) {
            return;
        }

        $this->db->select('cid_id, cstatus');
        $this->db->from('init_call');
        $this->db->where_in('cid_id', $cid_ids);
        $leads = $this->db->get()->result_array();
        $cstatus_map = array_column($leads, 'cstatus', 'cid_id');

        // Get theme assignments for these phrase rows
        $phrase_ids = array_filter(array_column($phrase_rows, 'id'));
        if (empty($phrase_ids)) {
            return;
        }
        $this->db->select('ota.phrase_id, ota.theme_code, op.cid_id, op.actor_uid');
        $this->db->from('objection_theme_assignment ota');
        $this->db->join('objection_phrase op', 'op.id = ota.phrase_id');
        $this->db->where_in('ota.phrase_id', $phrase_ids);
        $assignments = $this->db->get()->result_array();

        foreach ($assignments as $a) {
            $cid    = $a['cid_id'];
            $status = $cstatus_map[$cid] ?? 0;
            if ($status < self::BLOCKER_MIN_CSTATUS) {
                continue;
            }

            // UPSERT: increment occurrence count, update last_seen_at
            $existing = $this->db->get_where('objection_lead_blocker', [
                'cid_id'     => $cid,
                'theme_code' => $a['theme_code'],
            ])->row_array();

            if ($existing) {
                $this->db->where('id', $existing['id']);
                $this->db->set([
                    'occurrence_count_total' => $existing['occurrence_count_total'] + 1,
                    'last_seen_at'           => date('Y-m-d H:i:s'),
                ]);
                $this->db->update('objection_lead_blocker');
            } else {
                $this->db->insert('objection_lead_blocker', [
                    'cid_id'                => $cid,
                    'actor_uid'             => $a['actor_uid'],
                    'theme_code'            => $a['theme_code'],
                    'cstatus_at_first_seen' => $status,
                ]);
            }
        }
    }


    // -----------------------------------------------------------------
    // Step 6: Auto-create KB candidates
    // -----------------------------------------------------------------

    private function _auto_create_kb_candidates()
    {
        // Read v_unresolved_objections_for_kb_candidates (top 5)
        $rows = $this->db->query(
            'SELECT theme_code, theme_label, sample_phrases, total_occurrences
             FROM v_unresolved_objections_for_kb_candidates
             LIMIT ' . self::KB_CANDIDATE_TOP_N
        )->result_array();

        $created = 0;
        foreach ($rows as $row) {
            // Check if a candidate already exists for this theme this week
            $exists = $this->db->get_where('knowledge_repository_row', [
                'faq_type'   => 'objection_rebuttal',
                'theme_code' => $row['theme_code'],
                'is_approved' => 0,
            ])->row();

            if (!$exists) {
                $this->db->insert('knowledge_repository_row', [
                    'faq_type'            => 'objection_rebuttal',
                    'theme_code'          => $row['theme_code'],
                    'question_draft'      => 'Common objection this week: ' . $row['theme_label']
                                            . '. Top phrases: ' . mb_substr($row['sample_phrases'], 0, 200),
                    'answer_draft'        => '',
                    'is_approved'         => 0,
                    'requires_avp_review' => 1,
                    'source'              => 'objection_mining_052',
                    'created_at'          => date('Y-m-d H:i:s'),
                ]);
                $created++;
            }
        }
        return $created;
    }


    // -----------------------------------------------------------------
    // Step 7: Push BD coaching cards via M027
    // -----------------------------------------------------------------

    private function _push_coaching_cards()
    {
        // Find lead blockers where counter_message_sent=0 AND an approved KB rebuttal exists
        $sql = "
            SELECT lb.id AS blocker_id, lb.actor_uid, lb.cid_id,
                   lb.theme_code, oth.theme_label,
                   cm.compname AS school_name,
                   krr.answer_draft AS rebuttal_text,
                   (SELECT u2.name
                    FROM user u2
                    WHERE u2.type_id = 15 LIMIT 1) AS avp_name
            FROM objection_lead_blocker lb
            JOIN objection_theme oth ON oth.theme_code = lb.theme_code
            JOIN init_call ic ON ic.id = lb.cid_id
            LEFT JOIN company_master cm ON cm.id = ic.cmpid_id
            JOIN knowledge_repository_row krr ON krr.theme_code = lb.theme_code
              AND krr.faq_type = 'objection_rebuttal'
              AND krr.is_approved = 1
            WHERE lb.counter_message_sent = 0
              AND lb.is_resolved = 0
        ";
        $blockers = $this->db->query($sql)->result_array();

        $sent = 0;
        $comm_model_path = APPPATH . 'models/AIAgents/CommOrchestrator_model.php';
        if (!file_exists($comm_model_path)) {
            log_message('error', 'ObjectionMining coaching cards skipped: CommOrchestrator_model not found at ' . $comm_model_path);
            return 0;
        }
        $this->load->model('AIAgents/CommOrchestrator_model');

        foreach ($blockers as $b) {
            $message = 'You heard the ' . $b['theme_label'] . ' objection in your meeting with '
                     . $b['school_name'] . '. Here is the approved counter from '
                     . ($b['avp_name'] ?: 'your AVP') . ': '
                     . mb_substr($b['rebuttal_text'], 0, 300);

            $this->CommOrchestrator_model->send_card([
                'recipient_uid'  => $b['actor_uid'],
                'card_type'      => 'objection_coaching',
                'message'        => $message,
                'source_ref'     => 'objection_mining_052',
                'cid_id'         => $b['cid_id'],
            ]);

            $this->db->where('id', $b['blocker_id']);
            $this->db->update('objection_lead_blocker', [
                'counter_message_sent'    => 1,
                'counter_message_sent_at' => date('Y-m-d H:i:s'),
            ]);
            $sent++;
        }
        return $sent;
    }


    // -----------------------------------------------------------------
    // Helper: get top objections for a week
    // -----------------------------------------------------------------

    public function get_top_themes_for_week($iso_week = null)
    {
        if ($iso_week === null) {
            $iso_week = intval(date('oW'));
        }
        // Try the view first; fall back to direct aggregate table query if view absent
        try {
            $rows = $this->db->query('SELECT * FROM v_top_objections_this_week')->result_array();
            return $rows;
        } catch (Exception $e) { /* view missing — use table fallback */ }

        // Direct fallback: aggregate from objection_weekly_aggregate + objection_theme
        try {
            $rows = $this->db->query(
                'SELECT owa.iso_week, owa.theme_code,
                        COALESCE(ot.theme_label, owa.theme_code) AS theme_label,
                        SUM(owa.occurrence_count) AS total_occurrences,
                        SUM(owa.meetings_count)   AS total_meetings
                 FROM objection_weekly_aggregate owa
                 LEFT JOIN objection_theme ot ON ot.theme_code = owa.theme_code
                 WHERE owa.iso_week = ?
                 GROUP BY owa.iso_week, owa.theme_code
                 ORDER BY total_occurrences DESC
                 LIMIT 20',
                array($iso_week)
            )->result_array();
            return $rows;
        } catch (Exception $e) {
            log_message('error', 'ObjectionMining_model::get_top_themes_for_week: ' . $e->getMessage());
            return array();
        }
    }


    public function get_emerging_objections()
    {
        return $this->db->query(
            'SELECT * FROM v_emerging_objections_50pct_growth'
        )->result_array();
    }


    public function get_bd_objection_pattern($actor_uid = null)
    {
        $sql = 'SELECT * FROM v_per_bd_objection_pattern';
        if ($actor_uid) {
            $sql .= ' WHERE actor_uid = ' . intval($actor_uid);
        }
        return $this->db->query($sql)->result_array();
    }


    public function get_lead_blockers($cid_id = null)
    {
        $sql = 'SELECT * FROM v_lead_blockers_unresolved';
        if ($cid_id) {
            $sql .= ' WHERE cid_id = ' . intval($cid_id);
        }
        return $this->db->query($sql)->result_array();
    }


    public function get_kb_candidates()
    {
        return $this->db->query(
            'SELECT * FROM v_unresolved_objections_for_kb_candidates'
        )->result_array();
    }


    // -----------------------------------------------------------------
    // Helper: LLM call wrapper
    // -----------------------------------------------------------------

    /**
     * _call_llm
     * Wraps the STEM LLM client. Returns ['content' => '...', 'cost_inr' => 0.0]
     * or null on failure.
     */
    private function _call_llm($system_prompt, $user_content, &$stats, $call_type)
    {
        $this->load->library('StemLlmClient');

        try {
            $response = $this->stemlllmclient->chat([
                'model'       => self::LLM_MODEL,
                'temperature' => self::LLM_TEMP,
                'messages'    => [
                    ['role' => 'system',  'content' => $system_prompt],
                    ['role' => 'user',    'content' => $user_content],
                ],
            ]);

            $cost_inr = $response['cost_inr'] ?? 0;
            $stats['llm_total_cost_inr'] += $cost_inr;

            if ($call_type === 'extraction') {
                $stats['llm_extraction_calls']++;
            }

            return [
                'content'  => $response['choices'][0]['message']['content'] ?? '',
                'cost_inr' => $cost_inr,
            ];

        } catch (Exception $e) {
            $stats['errors_count']++;
            $stats['errors'][] = 'LLM call failed (' . $call_type . '): ' . $e->getMessage();
            log_message('error', 'ObjectionMining LLM error: ' . $e->getMessage());
            return null;
        }
    }


    // -----------------------------------------------------------------
    // Helper: phrase normalisation for cache
    // -----------------------------------------------------------------

    private function _normalise_phrase($raw)
    {
        $s = mb_strtolower($raw, 'UTF-8');
        $s = preg_replace('/[^a-z0-9\s]/u', '', $s);
        $s = preg_replace('/\s+/', ' ', trim($s));
        return mb_substr($s, 0, 191);
    }


    private function _cache_lookup($normalised)
    {
        return $this->db->get_where('objection_phrase_cache', [
            'normalised_phrase' => $normalised,
        ])->row_array() ?: null;
    }


    // -----------------------------------------------------------------
    // Helper: active themes
    // -----------------------------------------------------------------

    private function _get_active_themes()
    {
        return $this->db->get_where('objection_theme', ['is_active' => 1])->result_array();
    }


    // -----------------------------------------------------------------
    // Helper: feature flag
    // -----------------------------------------------------------------

    private function _get_flag()
    {
        $row = $this->db->get_where('feature_flag', [
            'flag_key' => self::FLAG_CODE,
        ])->row_array();
        return intval($row['flag_value'] ?? 0);
    }


    // -----------------------------------------------------------------
    // Helper: run log management
    // -----------------------------------------------------------------

    private function _open_run_log($iso_week, $flag)
    {
        $this->db->insert('objection_mining_run_log', [
            'run_start_at'  => date('Y-m-d H:i:s'),
            'run_status'    => 'running',
            'scope'         => $flag === self::FLAG_PILOT ? 'pilot' : 'org_wide',
            'iso_week'      => $iso_week,
        ]);
        return $this->db->insert_id();
    }


    private function _close_run_log($run_id, $status, $stats)
    {
        $this->db->where('id', $run_id);
        $this->db->update('objection_mining_run_log', [
            'run_end_at'             => date('Y-m-d H:i:s'),
            'run_status'             => $status,
            'transcripts_scanned'    => $stats['transcripts_scanned'],
            'phrases_extracted'      => $stats['phrases_extracted'],
            'phrases_cache_hit'      => $stats['phrases_cache_hit'],
            'phrases_llm_called'     => $stats['phrases_llm_called'],
            'themes_assigned'        => $stats['themes_assigned'],
            'uncategorised_count'    => $stats['uncategorised_count'],
            'theme_drift_count'      => 0, // computed by separate audit job
            'llm_extraction_calls'   => $stats['llm_extraction_calls'],
            'llm_assignment_calls'   => $stats['llm_assignment_calls'],
            'llm_total_cost_inr'     => round($stats['llm_total_cost_inr'], 2),
            'kb_candidates_created'  => $stats['kb_candidates_created'],
            'coaching_cards_sent'    => $stats['coaching_cards_sent'],
            'errors_count'           => $stats['errors_count'],
            'errors_json'            => json_encode($stats['errors']),
        ]);
    }


    private function _fresh_stats()
    {
        return [
            'transcripts_scanned'  => 0,
            'phrases_extracted'    => 0,
            'phrases_cache_hit'    => 0,
            'phrases_llm_called'   => 0,
            'themes_assigned'      => 0,
            'uncategorised_count'  => 0,
            'llm_extraction_calls' => 0,
            'llm_assignment_calls' => 0,
            'llm_total_cost_inr'   => 0.0,
            'kb_candidates_created'=> 0,
            'coaching_cards_sent'  => 0,
            'errors_count'         => 0,
            'errors'               => [],
        ];
    }


    // -----------------------------------------------------------------
    // Helper: mark transcripts done
    // -----------------------------------------------------------------

    private function _mark_transcripts_done($transcripts)
    {
        $ids = array_column($transcripts, 'session_id');
        if (empty($ids)) {
            return;
        }
        $this->db->where_in('session_id', $ids);
        $this->db->update('audio_capture_transcript', ['is_mining_done' => 1]);
    }
}
// End ObjectionMining_model.php

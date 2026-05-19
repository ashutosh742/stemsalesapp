<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * STEM Learning - FAQ Agent
 * Migration 036 (BD Coach + Greetings + Knowledge Repository)
 *
 * Responsibilities:
 *  1. Semantic search across faq_entry using GPT-5.4 mini
 *  2. Voice query via Whisper transcription (Mig 025 endpoint) then search
 *  3. Log unanswered queries; auto-promote if asked 3+ times in 7 days
 *  4. Moderation: promote from unanswered queue to knowledge_candidate_faq
 *  5. Publish candidate FAQs to live faq_entry (Director + AVP in pilot,
 *     CMs and senior BDs after coach_036_enabled=2)
 *
 * Publish permission:
 *  - Pilot (coach_036_enabled=1): type_id IN (4 Director, 29 AVP)
 *  - Org rollout (coach_036_enabled=2): also type_id=13 CM and senior BDs
 *    (join_date > 1825 days ago)
 *
 * LLM: GPT-5.4 mini for FAQ semantic search via $this->llm->call() placeholder.
 * Whisper: Mig 025 endpoint via $this->whisper->transcribe() placeholder.
 *
 * Migration 036. Author: STEM ops, 2026-05-18.
 */
class Faq_agent extends CI_Model
{
    // Days to look back when counting repeat unanswered queries.
    const UNANSWERED_LOOKBACK_DAYS = 7;

    // Minimum repeat count to auto-promote to knowledge_candidate_faq.
    const AUTO_PROMOTE_MIN_COUNT = 3;

    // Senior BD minimum tenure in days.
    const SENIOR_BD_MIN_DAYS = 1825;

    // GPT model for search.
    const LLM_SEARCH_MODEL = 'gpt-5.4-mini';

    // Type IDs allowed to publish FAQs in pilot.
    const PUBLISH_ALLOWED_PILOT = [4, 29];

    // Additional type IDs allowed in org rollout.
    const PUBLISH_ALLOWED_ORG   = [13];

    public function __construct()
    {
        parent::__construct();
        $this->load->database();
    }

    // ==========================================================================
    // SEARCH
    // ==========================================================================

    /**
     * Semantic search across published FAQ entries.
     * Uses GPT-5.4 mini to rank candidates by relevance.
     *
     * @param  string $query_text Free-text query from the BD
     * @param  int    $uid        BD user id (for logging)
     * @param  int    $top_k      Number of results to return
     * @return array  ['ok', 'results', 'query_log_id', 'matched']
     */
    public function search($query_text, $uid, $top_k = 5)
    {
        $uid        = (int)$uid;
        $top_k      = max(1, min(20, (int)$top_k));
        $query_text = substr((string)$query_text, 0, 500);

        if (!$query_text) return ['ok' => false, 'error' => 'empty_query'];

        // Pull all published FAQ entries (embedding-based semantic search via LLM).
        $faqs = $this->db->query("
            SELECT id, question, answer, category, source_url, upvotes, downvotes
              FROM faq_entry
             WHERE status = 'published'
             ORDER BY upvotes DESC, last_used_at DESC
             LIMIT 200
        ")->result_array();

        $matched_faq_id   = null;
        $match_score      = 0.0;
        $results          = [];

        if (!empty($faqs)) {
            // Build FAQ context for LLM ranking.
            $faq_list = '';
            foreach ($faqs as $f) {
                $faq_list .= 'ID:' . $f['id'] . ' Q:' . $f['question'] . "\n";
            }

            $prompt = <<<PROMPT
You are a STEM Learning internal FAQ search engine.

User query: "{$query_text}"

Available FAQs (ID: Question):
{$faq_list}

Return a JSON array of the top {$top_k} most relevant FAQ IDs with confidence scores (0.0 to 1.0).
Format: [{"id": 123, "score": 0.95}, ...]
Return only the JSON array, no other text.
PROMPT;

            $llm_result = $this->llm->call(self::LLM_SEARCH_MODEL, $prompt, [
                'max_tokens'  => 300,
                'temperature' => 0.0,
            ]);

            $ranked = $this->_parse_ranked_ids($llm_result);

            // Join ranked IDs back to FAQ rows.
            $faq_map = [];
            foreach ($faqs as $f) $faq_map[(int)$f['id']] = $f;

            foreach ($ranked as $rank) {
                $fid   = (int)($rank['id'] ?? 0);
                $score = (float)($rank['score'] ?? 0);
                if (isset($faq_map[$fid])) {
                    $row            = $faq_map[$fid];
                    $row['score']   = $score;
                    $results[]      = $row;
                    if ($score > $match_score) {
                        $match_score  = $score;
                        $matched_faq_id = $fid;
                    }
                }
            }

            // Update last_used_at for top result.
            if ($matched_faq_id && $match_score >= 0.5) {
                $this->db->query("
                    UPDATE faq_entry SET last_used_at = NOW() WHERE id = ?
                ", [$matched_faq_id]);
            }
        }

        // Log the query.
        $this->db->query("
            INSERT INTO faq_query_log
                (asker_uid, query_text, matched_faq_id, match_score, asked_at)
            VALUES (?, ?, ?, ?, NOW())
        ", [$uid, $query_text, $matched_faq_id, $match_score]);
        $query_log_id = $this->db->insert_id();

        // If no match above threshold, log to unanswered queue.
        if (empty($results) || $match_score < 0.4) {
            $this->log_unanswered($query_text, $uid);
        }

        return [
            'ok'           => true,
            'query'        => $query_text,
            'results'      => $results,
            'matched'      => !empty($results),
            'query_log_id' => $query_log_id,
        ];
    }

    // ------------------------------------------------------------------

    /**
     * Voice query: transcribe via Whisper then call search().
     *
     * @param  string $audio_url
     * @param  int    $uid
     * @return array
     */
    public function voice_search($audio_url, $uid)
    {
        $uid       = (int)$uid;
        $audio_url = (string)$audio_url;

        // Whisper placeholder - Mig 025 endpoint.
        $transcript = $this->whisper->transcribe($audio_url);

        if (empty($transcript['text'])) {
            return ['ok' => false, 'error' => 'transcription_failed'];
        }

        $query_text = substr((string)$transcript['text'], 0, 500);
        $result     = $this->search($query_text, $uid);

        return array_merge($result, ['transcribed_query' => $query_text]);
    }

    // ==========================================================================
    // UNANSWERED QUEUE
    // ==========================================================================

    /**
     * Insert into faq_unanswered_queue.
     * If same query_text seen 3+ times in 7 days, auto-promote to knowledge_candidate_faq.
     *
     * @param  string $query_text
     * @param  int    $uid
     * @return array  ['ok', 'queue_id', 'auto_promoted']
     */
    public function log_unanswered($query_text, $uid)
    {
        $uid        = (int)$uid;
        $query_text = substr((string)$query_text, 0, 500);
        if (!$query_text) return ['ok' => false, 'error' => 'empty_query'];

        $this->db->trans_start();
        $this->db->query("
            INSERT INTO faq_unanswered_queue
                (asker_uid, query_text, asked_at, status)
            VALUES (?, ?, NOW(), 'open')
        ", [$uid, $query_text]);
        $queue_id = $this->db->insert_id();
        $this->db->trans_complete();

        if ($this->db->trans_status() === false) {
            return ['ok' => false, 'error' => 'db_transaction_failed'];
        }

        // Check repeat count.
        $repeat_count = (int)$this->db->query("
            SELECT COUNT(*) AS cnt FROM faq_unanswered_queue
             WHERE query_text = ?
               AND asked_at >= DATE_SUB(NOW(), INTERVAL ? DAY)
               AND status = 'open'
        ", [$query_text, self::UNANSWERED_LOOKBACK_DAYS])->row('cnt');

        $auto_promoted = false;
        if ($repeat_count >= self::AUTO_PROMOTE_MIN_COUNT) {
            // Auto-promote: check if not already promoted.
            $already = (int)$this->db->query("
                SELECT COUNT(*) AS cnt FROM knowledge_candidate_faq
                 WHERE candidate_question = ?
                   AND status = 'pending'
            ", [$query_text])->row('cnt');

            if (!$already) {
                $this->db->query("
                    INSERT INTO knowledge_candidate_faq
                        (candidate_question, candidate_answer, generated_at, status)
                    VALUES (?, '[Awaiting expert answer]', NOW(), 'pending')
                ", [$query_text]);
                $auto_promoted = true;
            }
        }

        return [
            'ok'            => true,
            'queue_id'      => $queue_id,
            'repeat_count'  => $repeat_count,
            'auto_promoted' => $auto_promoted,
        ];
    }

    // ------------------------------------------------------------------

    /**
     * Return aggregated top unanswered queries.
     *
     * @param  int $min_asks Minimum ask count to include
     * @param  int $days     Lookback window
     * @return array
     */
    public function get_top_unanswered($min_asks = 3, $days = 7)
    {
        $min_asks = max(1, (int)$min_asks);
        $days     = max(1, (int)$days);

        $rows = $this->db->query("
            SELECT query_text,
                   COUNT(*) AS ask_count,
                   MIN(asked_at) AS first_asked,
                   MAX(asked_at) AS last_asked,
                   COUNT(DISTINCT asker_uid) AS unique_askers
              FROM faq_unanswered_queue
             WHERE asked_at >= DATE_SUB(NOW(), INTERVAL ? DAY)
               AND status = 'open'
             GROUP BY query_text
            HAVING COUNT(*) >= ?
             ORDER BY ask_count DESC, last_asked DESC
             LIMIT 50
        ", [$days, $min_asks])->result_array();

        return ['ok' => true, 'rows' => $rows, 'count' => count($rows)];
    }

    // ------------------------------------------------------------------

    /**
     * Promote an unanswered queue entry to knowledge_candidate_faq.
     *
     * @param  int $queue_id           faq_unanswered_queue.id
     * @param  int $director_or_avp_uid Moderator uid (type_id 4 or 29)
     * @return array ['ok', 'candidate_id']
     */
    public function promote_to_candidate($queue_id, $director_or_avp_uid)
    {
        $queue_id  = (int)$queue_id;
        $mod_uid   = (int)$director_or_avp_uid;

        // Permission check.
        if (!$this->_is_allowed_moderator($mod_uid, [4, 29])) {
            return ['ok' => false, 'error' => 'permission_denied', 'required_type_ids' => [4, 29]];
        }

        $queue_row = $this->db->query("
            SELECT id, query_text FROM faq_unanswered_queue
             WHERE id = ? AND status = 'open' LIMIT 1
        ", [$queue_id])->row_array();

        if (empty($queue_row)) return ['ok' => false, 'error' => 'queue_row_not_found_or_closed'];

        $this->db->trans_start();

        $this->db->query("
            INSERT INTO knowledge_candidate_faq
                (candidate_question, candidate_answer, generated_at,
                 status, reviewed_by_uid, reviewed_at)
            VALUES (?, '[Awaiting expert answer]', NOW(), 'pending', ?, NOW())
        ", [$queue_row['query_text'], $mod_uid]);
        $candidate_id = $this->db->insert_id();

        $this->db->query("
            UPDATE faq_unanswered_queue SET status = 'archived' WHERE id = ?
        ", [$queue_id]);

        $this->db->trans_complete();

        if ($this->db->trans_status() === false) {
            return ['ok' => false, 'error' => 'db_transaction_failed'];
        }

        return ['ok' => true, 'candidate_id' => $candidate_id];
    }

    // ==========================================================================
    // PUBLISH CANDIDATE
    // ==========================================================================

    /**
     * Publish a candidate FAQ into the live faq_entry table.
     *
     * Permission:
     *  - Pilot (coach_036_enabled=1): type_id IN (4, 29)
     *  - Org rollout (coach_036_enabled=2): also type_id=13 CM and senior BDs
     *
     * @param  int    $candidate_id
     * @param  int    $moderator_uid
     * @param  string $final_answer
     * @param  int    $source_artifact_id Optional artifact FK
     * @return array  ['ok', 'faq_id']
     */
    public function publish_candidate($candidate_id, $moderator_uid, $final_answer, $source_artifact_id = null)
    {
        $candidate_id      = (int)$candidate_id;
        $moderator_uid     = (int)$moderator_uid;
        $final_answer      = substr((string)$final_answer, 0, 65000);
        $source_artifact_id = $source_artifact_id ? (int)$source_artifact_id : null;

        if (!$candidate_id || !$moderator_uid || !$final_answer) {
            return ['ok' => false, 'error' => 'missing_required_fields'];
        }

        // Permission: check flag and user type.
        if (!$this->_can_publish($moderator_uid)) {
            return ['ok' => false, 'error' => 'permission_denied'];
        }

        $candidate = $this->db->query("
            SELECT id, candidate_question, source_artifact_id AS art_id
              FROM knowledge_candidate_faq
             WHERE id = ? AND status = 'pending'
             LIMIT 1
        ", [$candidate_id])->row_array();

        if (empty($candidate)) return ['ok' => false, 'error' => 'candidate_not_found_or_not_pending'];

        $artifact_id = $source_artifact_id ?: ($candidate['art_id'] ?? null);

        $this->db->trans_start();

        // Insert into faq_entry.
        $this->db->query("
            INSERT INTO faq_entry
                (question, answer, category, source_artifact_id,
                 created_by_uid, status, last_used_at)
            VALUES (?, ?, 'general', ?, ?, 'published', NOW())
        ", [$candidate['candidate_question'], $final_answer, $artifact_id, $moderator_uid]);
        $faq_id = $this->db->insert_id();

        // Update candidate.
        $this->db->query("
            UPDATE knowledge_candidate_faq
               SET status = 'published_to_faq',
                   reviewed_by_uid = ?,
                   reviewed_at = NOW(),
                   published_faq_id = ?
             WHERE id = ?
        ", [$moderator_uid, $faq_id, $candidate_id]);

        $this->db->trans_complete();

        if ($this->db->trans_status() === false) {
            return ['ok' => false, 'error' => 'db_transaction_failed'];
        }

        return ['ok' => true, 'faq_id' => $faq_id, 'candidate_id' => $candidate_id];
    }

    // ==========================================================================
    // PRIVATE HELPERS
    // ==========================================================================

    /**
     * Parse LLM-ranked ID list.
     *
     * @param  mixed $llm_result
     * @return array [{id, score}, ...]
     */
    private function _parse_ranked_ids($llm_result)
    {
        $text = is_string($llm_result) ? $llm_result : (string)($llm_result['content'] ?? '');
        $text = preg_replace('/```json\s*|\s*```/', '', $text);
        $decoded = json_decode($text, true);
        return is_array($decoded) ? $decoded : [];
    }

    // ------------------------------------------------------------------

    /**
     * Check if a user is an allowed moderator type.
     *
     * @param  int   $uid
     * @param  array $allowed_types
     * @return bool
     */
    private function _is_allowed_moderator($uid, $allowed_types)
    {
        $user = $this->db->query("
            SELECT type_id FROM user WHERE uid = ? LIMIT 1
        ", [$uid])->row_array();
        return $user && in_array((int)$user['type_id'], $allowed_types);
    }

    // ------------------------------------------------------------------

    /**
     * Check publish permission based on feature flag and user type.
     *
     * @param  int $uid
     * @return bool
     */
    private function _can_publish($uid)
    {
        $user = $this->db->query("
            SELECT u.type_id, DATEDIFF(NOW(), u.joined_at) AS tenure_days
              FROM user u WHERE u.uid = ? LIMIT 1
        ", [$uid])->row_array();

        if (empty($user)) return false;

        $type_id = (int)$user['type_id'];

        // Director or AVP: always allowed.
        if (in_array($type_id, self::PUBLISH_ALLOWED_PILOT)) return true;

        // Check feature flag for org rollout.
        $flag = (int)$this->db->query("
            SELECT flag_value FROM feature_flag
             WHERE flag_key = 'coach_036_enabled'
               AND entity_type = 'global'
             LIMIT 1
        ")->row('flag_value');

        if ($flag >= 2) {
            // CM allowed.
            if (in_array($type_id, self::PUBLISH_ALLOWED_ORG)) return true;
            // Senior BD: any type_id with tenure > 1825 days.
            if ((int)$user['tenure_days'] > self::SENIOR_BD_MIN_DAYS) return true;
        }

        return false;
    }
}

<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * STEM Learning - Asset Review Agent
 * Migration 036 (BD Coach + Greetings + Knowledge Repository)
 *
 * Responsibilities:
 *  1. Accept BD asset submissions (proposal, pitch deck, email, followup seq, referral script)
 *  2. Run AI rubric review via Claude Sonnet 4.6 and compute A+/A/B/C/D grade
 *  3. Transcribe audio submissions via Whisper (Mig 025 endpoint placeholder)
 *  4. Unlock proposal SLA gate for BDs with 3+ consecutive A/A+ grades
 *  5. Surface grade distribution stats for CM/AVP view
 *
 * Asset types: proposal | pitch_text | pitch_deck | email | followup_seq | referral_script
 *
 * Grading: 5 rubric criteria x 0-3 points each = 15 max
 *  >= 13 = A+
 *  >= 11 = A
 *  >=  8 = B
 *  >=  5 = C
 *  < 5   = D
 *
 * LLM: Claude Sonnet 4.6 via $this->llm->call() placeholder.
 * Whisper: Mig 025 endpoint via $this->whisper->transcribe() placeholder.
 *
 * Migration 036. Author: STEM ops, 2026-05-18.
 */
class Asset_review_agent extends CI_Model
{
    // Allowed asset types.
    const ALLOWED_TYPES = ['proposal', 'pitch_text', 'pitch_deck', 'email', 'followup_seq', 'referral_script'];

    // Grade thresholds (out of 15).
    const GRADE_THRESHOLDS = [
        'A+' => 13,
        'A'  => 11,
        'B'  => 8,
        'C'  => 5,
        'D'  => 0,
    ];

    // Consecutive A/A+ reviews to unlock proposal SLA gate.
    const SLA_GATE_CONSECUTIVE = 3;

    // Claude model for review.
    const LLM_MODEL = 'claude-sonnet-4-6';

    public function __construct()
    {
        parent::__construct();
        $this->load->database();
    }

    // ==========================================================================
    // SUBMIT
    // ==========================================================================

    /**
     * BD submits an asset for review.
     *
     * @param  int    $uid            BD user id
     * @param  string $asset_type     See ALLOWED_TYPES
     * @param  string $file_url       Uploaded file URL (nullable if text provided)
     * @param  string $transcript_text Pasted or transcribed text (nullable)
     * @return array  ['ok', 'asset_review_id', 'status']
     */
    public function submit_asset($uid, $asset_type, $file_url, $transcript_text = null)
    {
        $uid        = (int)$uid;
        $asset_type = $this->db->escape_str($asset_type);
        $file_url   = $file_url   ? $this->db->escape_str($file_url) : null;
        $text       = $transcript_text ? substr((string)$transcript_text, 0, 65000) : null;

        if (!$uid) return ['ok' => false, 'error' => 'missing_uid'];
        if (!in_array($asset_type, self::ALLOWED_TYPES)) {
            return ['ok' => false, 'error' => 'invalid_asset_type', 'allowed' => self::ALLOWED_TYPES];
        }
        if (!$file_url && !$text) {
            return ['ok' => false, 'error' => 'file_url_or_text_required'];
        }

        $this->db->trans_start();
        $this->db->query("
            INSERT INTO asset_review
                (bd_uid, asset_type, input_file_url, input_text, status, submitted_at)
            VALUES (?, ?, ?, ?, 'pending_review', NOW())
        ", [$uid, $asset_type, $file_url, $text]);
        $asset_review_id = $this->db->insert_id();
        $this->db->trans_complete();

        if ($this->db->trans_status() === false) {
            return ['ok' => false, 'error' => 'db_transaction_failed'];
        }

        return [
            'ok'              => true,
            'asset_review_id' => $asset_review_id,
            'status'          => 'pending_review',
        ];
    }

    // ==========================================================================
    // RUN REVIEW (cron entry)
    // ==========================================================================

    /**
     * Pull rubric for asset_type, call Claude Sonnet 4.6, parse scores,
     * compute grade, save result.
     *
     * @param  int $asset_review_id
     * @return array ['ok', 'grade', 'overall_score']
     */
    public function run_review($asset_review_id)
    {
        $asset_review_id = (int)$asset_review_id;

        $review = $this->db->query("
            SELECT ar.*, u.firstName AS bd_name
              FROM asset_review ar
              LEFT JOIN user u ON u.uid = ar.bd_uid
             WHERE ar.id = ?
               AND ar.status = 'pending_review'
             LIMIT 1
        ", [$asset_review_id])->row_array();

        if (empty($review)) {
            return ['ok' => false, 'error' => 'review_not_found_or_not_pending'];
        }

        // Fetch rubric for this asset type from skill_definition.
        $rubric = $this->_get_rubric($review['asset_type']);
        $text   = $review['input_text'] ?: '[Refer to file: ' . ($review['input_file_url'] ?? 'none') . ']';

        // Build structured prompt.
        $prompt = $this->_build_review_prompt($review['asset_type'], $text, $rubric, $review['bd_name']);

        $llm_result = $this->llm->call(self::LLM_MODEL, $prompt, [
            'max_tokens'  => 1500,
            'temperature' => 0.2,
        ]);

        $parsed         = $this->_parse_review_response($llm_result);
        $overall_score  = $this->_sum_rubric_scores($parsed['rubric_scores'] ?? []);
        $grade          = $this->_compute_grade($overall_score);
        $approved       = in_array($grade, ['A+', 'A']) ? 1 : 0;

        $strengths_json    = json_encode($parsed['strengths']    ?? []);
        $improvements_json = json_encode($parsed['improvements'] ?? []);
        $redflags_json     = json_encode($parsed['redflags']     ?? []);
        $rubric_scores_json = json_encode($parsed['rubric_scores'] ?? []);

        $this->db->trans_start();
        $this->db->query("
            UPDATE asset_review
               SET grade = ?,
                   rubric_scores_json = ?,
                   strengths_json = ?,
                   improvements_json = ?,
                   redflags_json = ?,
                   overall_score = ?,
                   approved_for_send = ?,
                   status = 'reviewed',
                   reviewed_at = NOW()
             WHERE id = ?
        ", [$grade, $rubric_scores_json, $strengths_json, $improvements_json,
           $redflags_json, $overall_score, $approved, $asset_review_id]);
        $this->db->trans_complete();

        if ($this->db->trans_status() === false) {
            return ['ok' => false, 'error' => 'db_transaction_failed'];
        }

        // Check SLA gate eligibility if grade is A or A+.
        if ($approved) {
            $this->unlock_proposal_sla_gate((int)$review['bd_uid']);
        }

        // Fire red flag if grade is D (flag 17).
        if ($grade === 'D') {
            $this->_fire_grade_d_flag((int)$review['bd_uid'], $asset_review_id);
        }

        return [
            'ok'              => true,
            'asset_review_id' => $asset_review_id,
            'grade'           => $grade,
            'overall_score'   => $overall_score,
            'approved_for_send' => (bool)$approved,
            'strengths'       => $parsed['strengths']    ?? [],
            'improvements'    => $parsed['improvements'] ?? [],
            'redflags'        => $parsed['redflags']     ?? [],
        ];
    }

    // ==========================================================================
    // AUDIO TRANSCRIBE THEN REVIEW
    // ==========================================================================

    /**
     * Transcribe audio via Whisper (Mig 025), then trigger run_review.
     *
     * @param  int    $asset_review_id
     * @param  string $audio_url
     * @return array  ['ok', 'transcript_length', 'grade']
     */
    public function transcribe_audio($asset_review_id, $audio_url)
    {
        $asset_review_id = (int)$asset_review_id;
        $audio_url       = $this->db->escape_str($audio_url);

        // Whisper placeholder - Mig 025 endpoint.
        $transcript = $this->whisper->transcribe($audio_url);

        if (empty($transcript) || empty($transcript['text'])) {
            return ['ok' => false, 'error' => 'transcription_failed'];
        }

        $text = substr((string)$transcript['text'], 0, 65000);

        $this->db->trans_start();
        $this->db->query("
            UPDATE asset_review
               SET input_text = ?,
                   input_file_url = ?,
                   status = 'pending_review'
             WHERE id = ?
               AND status IN ('pending_review', 'transcription_pending')
        ", [$text, $audio_url, $asset_review_id]);
        $this->db->trans_complete();

        $review_result = $this->run_review($asset_review_id);

        return array_merge(
            ['transcript_length' => strlen($text)],
            $review_result
        );
    }

    // ==========================================================================
    // STATS
    // ==========================================================================

    /**
     * Aggregate grade distribution for CM/AVP view.
     *
     * @param  string $period_start Y-m-d
     * @param  string $period_end   Y-m-d
     * @return array  ['ok', 'distribution', 'total']
     */
    public function get_grade_distribution($period_start, $period_end)
    {
        $period_start = $this->db->escape_str($period_start);
        $period_end   = $this->db->escape_str($period_end);

        $rows = $this->db->query("
            SELECT grade,
                   asset_type,
                   COUNT(*) AS count,
                   AVG(overall_score) AS avg_score
              FROM asset_review
             WHERE status = 'reviewed'
               AND DATE(reviewed_at) BETWEEN ? AND ?
             GROUP BY grade, asset_type
             ORDER BY grade ASC, asset_type ASC
        ", [$period_start, $period_end])->result_array();

        $total = array_sum(array_column($rows, 'count'));

        return [
            'ok'           => true,
            'period_start' => $period_start,
            'period_end'   => $period_end,
            'distribution' => $rows,
            'total'        => $total,
        ];
    }

    // ==========================================================================
    // SLA GATE UNLOCK
    // ==========================================================================

    /**
     * If the BD's last 3 proposal-type reviews are graded A or A+,
     * signal to Mig 026 that the proposal_sla_gate can be auto-unlocked.
     *
     * @param  int $uid BD user id
     * @return array ['ok', 'gate_unlocked', 'consecutive_passing']
     */
    public function unlock_proposal_sla_gate($uid)
    {
        $uid = (int)$uid;

        $recent = $this->db->query("
            SELECT grade FROM asset_review
             WHERE bd_uid = ?
               AND asset_type = 'proposal'
               AND status = 'reviewed'
             ORDER BY reviewed_at DESC
             LIMIT ?
        ", [$uid, self::SLA_GATE_CONSECUTIVE])->result_array();

        if (count($recent) < self::SLA_GATE_CONSECUTIVE) {
            return ['ok' => true, 'gate_unlocked' => false, 'consecutive_passing' => count($recent)];
        }

        $all_passing = true;
        foreach ($recent as $r) {
            if (!in_array($r['grade'], ['A+', 'A'])) {
                $all_passing = false;
                break;
            }
        }

        if (!$all_passing) {
            return ['ok' => true, 'gate_unlocked' => false, 'consecutive_passing' => 0];
        }

        // Write gate signal.
        $this->db->trans_start();
        $this->db->query("
            INSERT INTO coach_gate_signal
                (bd_uid, signal_type, signal_value, note, created_at)
            VALUES (?, 'proposal_sla_gate_unlocked', 1,
                    'BD passed 3 consecutive proposal reviews at grade A or higher', NOW())
            ON DUPLICATE KEY UPDATE signal_value = 1, created_at = NOW()
        ", [$uid]);

        // Also update feature_flag or bd_capability table if it exists.
        $this->db->query("
            INSERT INTO feature_flag
                (flag_key, entity_type, entity_id, flag_value, updated_at)
            VALUES ('proposal_sla_gate', 'bd_uid', ?, 1, NOW())
            ON DUPLICATE KEY UPDATE flag_value = 1, updated_at = NOW()
        ", [$uid]);
        $this->db->trans_complete();

        return ['ok' => true, 'gate_unlocked' => true, 'consecutive_passing' => self::SLA_GATE_CONSECUTIVE];
    }

    // ==========================================================================
    // PRIVATE HELPERS
    // ==========================================================================

    /**
     * Get rubric criteria for an asset type.
     *
     * @param  string $asset_type
     * @return array  [{criterion, max_score}, ...]
     */
    private function _get_rubric($asset_type)
    {
        $rubrics = [
            'proposal' => [
                ['criterion' => 'Named-lab configuration referenced with specific pricing tier', 'max_score' => 3],
                ['criterion' => 'School pain point addressed with measurable outcome', 'max_score' => 3],
                ['criterion' => 'Competitor differentiation stated without naming competitor', 'max_score' => 3],
                ['criterion' => 'Pricing fence intact (no unauthorised discount implied)', 'max_score' => 3],
                ['criterion' => 'Clear next step with decision-maker named and date set', 'max_score' => 3],
            ],
            'pitch_text' => [
                ['criterion' => 'Opens with school-specific pain from research', 'max_score' => 3],
                ['criterion' => 'Walks named-lab demo before pricing', 'max_score' => 3],
                ['criterion' => 'Pre-empts top 3 objections', 'max_score' => 3],
                ['criterion' => 'Anchors on outcome not feature', 'max_score' => 3],
                ['criterion' => 'Closes with named next step and date', 'max_score' => 3],
            ],
            'pitch_deck' => [
                ['criterion' => 'Slide 1 names the school pain clearly', 'max_score' => 3],
                ['criterion' => 'Named-lab visual with configuration included', 'max_score' => 3],
                ['criterion' => 'Impact data or case study included', 'max_score' => 3],
                ['criterion' => 'Pricing slide fenced without list rates', 'max_score' => 3],
                ['criterion' => 'CTA slide with specific next step', 'max_score' => 3],
            ],
            'email' => [
                ['criterion' => 'Subject line references specific occasion or pain', 'max_score' => 3],
                ['criterion' => 'Body opens with value not product', 'max_score' => 3],
                ['criterion' => 'Single clear ask stated', 'max_score' => 3],
                ['criterion' => 'No unverified claims or promises', 'max_score' => 3],
                ['criterion' => 'Professional sign-off with contact details', 'max_score' => 3],
            ],
            'followup_seq' => [
                ['criterion' => '3-touch cadence defined with days between each touch', 'max_score' => 3],
                ['criterion' => 'Each touch adds new value (reference story, data, offer)', 'max_score' => 3],
                ['criterion' => 'Soft close ask included in touch 2 or 3', 'max_score' => 3],
                ['criterion' => 'Escalation path defined if no reply after touch 3', 'max_score' => 3],
                ['criterion' => 'No desperate or pressuring language', 'max_score' => 3],
            ],
            'referral_script' => [
                ['criterion' => 'Asks for specific referral (2 sister schools) not generic', 'max_score' => 3],
                ['criterion' => 'Ties referral ask to relationship moment (post-Win)', 'max_score' => 3],
                ['criterion' => 'Provides easy intro mechanism (email draft offered)', 'max_score' => 3],
                ['criterion' => 'No transactional quid pro quo language', 'max_score' => 3],
                ['criterion' => 'Thanks the referrer and closes with next step', 'max_score' => 3],
            ],
        ];

        return $rubrics[$asset_type] ?? $rubrics['proposal'];
    }

    // ------------------------------------------------------------------

    /**
     * Build structured review prompt for Claude.
     *
     * @param  string $asset_type
     * @param  string $text
     * @param  array  $rubric
     * @param  string $bd_name
     * @return string
     */
    private function _build_review_prompt($asset_type, $text, $rubric, $bd_name)
    {
        $rubric_lines = '';
        foreach ($rubric as $i => $r) {
            $num = $i + 1;
            $rubric_lines .= "Criterion {$num}: {$r['criterion']} (0 to {$r['max_score']} points)\n";
        }

        $type_label = str_replace('_', ' ', $asset_type);

        return <<<PROMPT
You are a STEM Learning sales coach reviewing a {$type_label} submitted by BD {$bd_name}.

ASSET TEXT:
{$text}

RUBRIC (score each criterion 0 to 3):
{$rubric_lines}

Return a JSON object with exactly these keys:
{
  "rubric_scores": [
    {"criterion": "...", "score": 0-3, "comment": "brief reason"}
  ],
  "strengths": ["strength 1", "strength 2", "strength 3"],
  "improvements": [
    {"issue": "...", "rewritten_line": "..."},
    {"issue": "...", "rewritten_line": "..."},
    {"issue": "...", "rewritten_line": "..."}
  ],
  "redflags": ["redflag 1 if any"]
}

Rules:
- Be specific and constructive
- Improvements must include a rewritten line the BD can use directly
- Red flags: pricing fence missing, named-lab promise unfunded, competitor named without context
- No em-dashes in your output
- Return only the JSON object
PROMPT;
    }

    // ------------------------------------------------------------------

    /**
     * Parse LLM JSON review response.
     *
     * @param  mixed $llm_result
     * @return array
     */
    private function _parse_review_response($llm_result)
    {
        $text = is_string($llm_result) ? $llm_result : (string)($llm_result['content'] ?? '');
        $text = preg_replace('/```json\s*|\s*```/', '', $text);
        $decoded = json_decode($text, true);

        if (is_array($decoded)) return $decoded;

        return [
            'rubric_scores' => [],
            'strengths'     => [],
            'improvements'  => [],
            'redflags'      => ['LLM response parse error'],
        ];
    }

    // ------------------------------------------------------------------

    /**
     * Sum rubric scores from parsed result.
     *
     * @param  array $rubric_scores
     * @return int
     */
    private function _sum_rubric_scores($rubric_scores)
    {
        $total = 0;
        foreach ($rubric_scores as $r) {
            $total += (int)($r['score'] ?? 0);
        }
        return $total;
    }

    // ------------------------------------------------------------------

    /**
     * Map score to letter grade.
     *
     * @param  int $score
     * @return string A+|A|B|C|D
     */
    private function _compute_grade($score)
    {
        foreach (self::GRADE_THRESHOLDS as $grade => $min) {
            if ($score >= $min) return $grade;
        }
        return 'D';
    }

    // ------------------------------------------------------------------

    /**
     * Fire red flag 17: grade D submitted for send.
     *
     * @param  int $bd_uid
     * @param  int $asset_review_id
     * @return void
     */
    private function _fire_grade_d_flag($bd_uid, $asset_review_id)
    {
        $cm_uid = $this->db->query("
            SELECT parent_uid FROM reporting_hierarchy
             WHERE employee_uid = ? AND active = 1 LIMIT 1
        ", [$bd_uid])->row('parent_uid');

        $this->db->query("
            INSERT INTO red_flag_event
                (flag_type_id, bd_uid, cm_uid, ref_id, ref_type,
                 severity, hours_sla, status, created_at)
            VALUES (17, ?, ?, ?, 'asset_review', 'amber', 2, 'open', NOW())
        ", [$bd_uid, (int)$cm_uid, $asset_review_id]);
    }
}

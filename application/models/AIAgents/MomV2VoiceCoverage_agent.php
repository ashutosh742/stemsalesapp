<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * MomV2_Voice_Coverage_Agent
 *
 * Migration 037 - MoM v2 Voice Coverage Agent
 * Date: 2026-05-19
 *
 * Scans a Whisper transcript against the 15 required MoM questions and
 * records per-question coverage results in mom_v2_voice_coverage.
 *
 * Coverage strategy (in priority order per question):
 *   1. Keyword scan  - case-insensitive substring search using voice_keywords_json
 *   2. ChatGPT fallback - calls ChatAI_model::call_chatgpt_api when keyword
 *      scan finds no evidence. Capped at 5 fallback calls per scan() to keep
 *      OpenAI cost under control.
 *
 * PARALLEL DEMO ONLY. Does NOT read or write to the production mom_data table.
 */
class MomV2_Voice_Coverage_Agent extends CI_Model {

    /**
     * Maximum number of ChatGPT fallback calls allowed per single scan_transcript
     * invocation. Prevents runaway OpenAI spend on long meetings with many
     * unanswered questions.
     */
    const MAX_GPT_FALLBACKS = 5;

    /**
     * Minimum coverage percent required for coverage_passed = 1.
     */
    const COVERAGE_PASS_THRESHOLD = 60;

    public function __construct() {
        parent::__construct();
        $this->load->database();
    }

    // =========================================================================
    // PUBLIC API
    // =========================================================================

    /**
     * Scan a Whisper transcript against all required questions for an event
     * and persist the result to mom_v2_voice_coverage.
     *
     * Steps:
     *   1. Load the agenda lock to find out which question_ids are required.
     *   2. Load those question rows so we have the keyword lists.
     *   3. For each required question, try keyword match first. On miss, use
     *      ChatGPT (capped at MAX_GPT_FALLBACKS total).
     *   4. Compute coverage_pct and coverage_passed.
     *   5. Insert into mom_v2_voice_coverage (recording_attempt incremented).
     *   6. Return the inserted row.
     *
     * @param int    $event_id
     * @param int    $bd_uid
     * @param string $transcript_text    Raw Whisper transcript
     * @param float  $whisper_confidence Overall Whisper confidence 0-100
     * @param string $lang               Language code e.g. 'en', 'hi', 'en-hi'
     * @return array  The inserted coverage row, or ['error' => ...] on failure.
     */
    public function scan_transcript($event_id, $bd_uid, $transcript_text, $whisper_confidence, $lang) {
        $event_id          = (int)$event_id;
        $bd_uid            = (int)$bd_uid;
        $whisper_confidence= (float)$whisper_confidence;

        // Load the existing agenda lock for this event.
        $this->load->model('MomV2_Agenda_Gate_model');
        $lock = $this->MomV2_Agenda_Gate_model->get_lock($event_id);
        if (empty($lock)) {
            return ['error' => 'no_agenda_lock', 'message' => 'Agenda must be locked before voice coverage can be scanned.'];
        }

        $required_ids = json_decode($lock['required_questions_json'], true);
        if (!is_array($required_ids) || empty($required_ids)) {
            return ['error' => 'empty_required_questions'];
        }

        // Load question schema rows for the required questions only.
        $this->db->where_in('question_id', $required_ids);
        $this->db->where('is_active', 1);
        $questions = $this->db->get('mom_v2_question_schema')->result_array();

        // Index questions by id for O(1) lookup.
        $questions_by_id = [];
        foreach ($questions as $q) {
            $questions_by_id[(int)$q['question_id']] = $q;
        }

        // Sentence-split the transcript once so every question can reuse it.
        $sentences = $this->_split_sentences($transcript_text);

        $per_question_coverage = [];
        $gpt_fallback_count    = 0;

        foreach ($required_ids as $qid) {
            $qid = (int)$qid;
            $q   = $questions_by_id[$qid] ?? null;

            if (!$q) {
                // Question exists in lock but not in schema (should not happen).
                $per_question_coverage[$qid] = [
                    'covered'          => false,
                    'confidence'       => 0,
                    'extracted_answer' => null
                ];
                continue;
            }

            $keywords = json_decode($q['voice_keywords_json'] ?? '[]', true);
            if (!is_array($keywords)) $keywords = [];

            // Step 1: keyword scan.
            $keyword_result = $this->_keyword_scan($transcript_text, $sentences, $keywords);

            if ($keyword_result['covered']) {
                $per_question_coverage[$qid] = $keyword_result;
            } elseif ($gpt_fallback_count < self::MAX_GPT_FALLBACKS) {
                // Step 2: ChatGPT fallback when keyword scan finds nothing.
                $gpt_fallback_count++;
                $gpt_result = $this->call_chatgpt_extract($transcript_text, $q['question_text'], $qid);
                $per_question_coverage[$qid] = $gpt_result;
            } else {
                // Cap reached - mark as not covered without another GPT call.
                $per_question_coverage[$qid] = [
                    'covered'          => false,
                    'confidence'       => 0,
                    'extracted_answer' => null
                ];
            }
        }

        // Compute coverage percent and pass/fail.
        $coverage_pct    = $this->compute_coverage_pct($per_question_coverage, $required_ids);
        $coverage_passed = ($coverage_pct >= self::COVERAGE_PASS_THRESHOLD) ? 1 : 0;

        // Determine recording_attempt for this event.
        $attempt = $this->mark_coverage_attempt($event_id);

        // Persist the coverage row.
        $row = [
            'event_id'                   => $event_id,
            'bd_uid'                     => $bd_uid,
            'transcript_text'            => $transcript_text,
            'transcript_lang'            => (string)$lang,
            'whisper_confidence'         => $whisper_confidence,
            'per_question_coverage_json' => json_encode($per_question_coverage),
            'coverage_pct'               => $coverage_pct,
            'coverage_passed'            => $coverage_passed,
            'recording_attempt'          => $attempt
        ];

        $this->db->insert('mom_v2_voice_coverage', $row);
        $coverage_id = $this->db->insert_id();

        $row['coverage_id']              = $coverage_id;
        $row['per_question_coverage']    = $per_question_coverage;

        log_message('info', 'mom_v2 scan_transcript: event_id=' . $event_id
            . ' coverage_pct=' . $coverage_pct . ' passed=' . $coverage_passed
            . ' attempt=' . $attempt . ' gpt_fallbacks=' . $gpt_fallback_count);

        return $row;
    }

    /**
     * Compute what percentage of REQUIRED questions were covered in the scan.
     *
     * @param array $per_question_coverage  Associative array keyed by question_id.
     *                                      Each value must have a 'covered' bool key.
     * @param array $required_question_ids  Flat array of required question_id integers.
     * @return float  0-100 rounded to 2 decimal places.
     */
    public function compute_coverage_pct($per_question_coverage, $required_question_ids) {
        $total   = count($required_question_ids);
        if ($total === 0) return 0.0;

        $covered = 0;
        foreach ($required_question_ids as $qid) {
            $qid = (int)$qid;
            if (!empty($per_question_coverage[$qid]['covered'])) {
                $covered++;
            }
        }

        return round(($covered / $total) * 100, 2);
    }

    /**
     * Call ChatGPT to extract a specific answer from the transcript.
     *
     * Caches by md5(transcript_text + question_id) so repeated calls for the
     * same transcript do not hit the API again.
     *
     * @param string $transcript    Full transcript text.
     * @param string $question_text The question to look for.
     * @param int    $question_id   Used as the cache key suffix.
     * @return array  {covered: bool, confidence: 0-100, extracted_answer: string|null}
     */
    public function call_chatgpt_extract($transcript, $question_text, $question_id) {
        $question_id = (int)$question_id;

        // Build a stable cache key from transcript content + question.
        $cache_key = md5($transcript . '|' . $question_id);

        // Check CI cache (file-based fallback if not configured).
        $this->load->driver('cache', ['adapter' => 'file']);
        $cached = $this->cache->get('mom_v2_gpt_extract_' . $cache_key);
        if ($cached !== false) {
            log_message('debug', 'mom_v2 call_chatgpt_extract: cache hit for question_id=' . $question_id);
            return $cached;
        }

        // Build the extraction prompt.
        $prompt = 'You are reviewing a meeting transcript. '
            . 'Given this meeting transcript, what is the answer to the following question? '
            . 'Reply with only the most relevant sentence or phrase from the transcript, '
            . 'or "NOT MENTIONED" if the topic was not discussed.' . "\n\n"
            . 'Question: ' . $question_text . "\n\n"
            . 'Transcript:' . "\n"
            . mb_substr($transcript, 0, 4000); // Trim to avoid token overflow.

        // Load ChatAI_model and call the API.
        $this->load->model('ChatAI_model');

        // ChatAI_model::call_chatgpt_api expects the message plus an array of
        // prior context messages (empty for a standalone extraction call).
        $api_key = stem_secret('openai_api_key');

        // We replicate the call pattern from ChatAI_model directly because we
        // need the raw answer text and a lower temperature for precision.
        $result_text = $this->_call_openai_for_extraction($api_key, $prompt);

        // Decide if the model found an answer.
        $not_mentioned = (stripos($result_text, 'NOT MENTIONED') !== false || trim($result_text) === '');

        $result = [
            'covered'          => !$not_mentioned,
            'confidence'       => $not_mentioned ? 0 : 55, // GPT match is lower confidence than keyword
            'extracted_answer' => $not_mentioned ? null : trim($result_text)
        ];

        // Cache for 24 hours to avoid repeat costs on the same transcript.
        $this->cache->save('mom_v2_gpt_extract_' . $cache_key, $result, 86400);

        return $result;
    }

    /**
     * Increment the recording_attempt counter for this event, capped at 3.
     *
     * Returns the attempt number that should be stored in the next coverage row.
     * This is calculated from the count of existing rows, not from an explicit
     * counter column, so it is always consistent even if rows are deleted.
     *
     * Cap: if 3 attempts already exist, still returns 3 (the row will overwrite
     * semantics are handled by the controller if needed; we simply cap the stored
     * value at 3 per the spec).
     *
     * @param int $event_id
     * @return int  1, 2, or 3
     */
    public function mark_coverage_attempt($event_id) {
        $event_id = (int)$event_id;
        $this->db->where('event_id', $event_id);
        $count = $this->db->count_all_results('mom_v2_voice_coverage');
        $attempt = min(3, $count + 1);
        return $attempt;
    }

    /**
     * Return the most recent coverage row for an event, or null if none.
     *
     * @param int $event_id
     * @return array|null
     */
    public function get_latest_coverage($event_id) {
        $this->db->where('event_id', (int)$event_id);
        $this->db->order_by('recording_attempt', 'DESC');
        $this->db->limit(1);
        $row = $this->db->get('mom_v2_voice_coverage')->row_array();
        return $row ?: null;
    }

    // =========================================================================
    // PRIVATE HELPERS
    // =========================================================================

    /**
     * Try to match any of the keyword phrases against the transcript.
     * Uses case-insensitive substring matching (equivalent to lemma match for
     * the English/Hindi domain words in the seed data).
     *
     * Returns the nearest sentence containing the first matching keyword as the
     * extracted_answer.
     *
     * @param string $transcript_text  Full transcript.
     * @param array  $sentences        Pre-split sentence array.
     * @param array  $keywords         Keyword/phrase list from voice_keywords_json.
     * @return array {covered, confidence, extracted_answer}
     */
    private function _keyword_scan($transcript_text, $sentences, $keywords) {
        if (empty($keywords)) {
            return ['covered' => false, 'confidence' => 0, 'extracted_answer' => null];
        }

        $transcript_lc = mb_strtolower($transcript_text);

        foreach ($keywords as $kw) {
            $kw_lc = mb_strtolower(trim($kw));
            if ($kw_lc === '') continue;

            if (mb_strpos($transcript_lc, $kw_lc) !== false) {
                // Keyword found. Find the sentence that contains it.
                $nearest = $this->_find_sentence_with_keyword($sentences, $kw_lc);

                // Confidence scales with keyword length: longer keywords are more
                // specific so we assign slightly higher confidence.
                $confidence = min(95, 70 + min(25, strlen($kw_lc) * 2));

                return [
                    'covered'          => true,
                    'confidence'       => $confidence,
                    'extracted_answer' => $nearest
                ];
            }
        }

        return ['covered' => false, 'confidence' => 0, 'extracted_answer' => null];
    }

    /**
     * Split a transcript into sentences for extracted_answer lookup.
     * Simple split on '.', '!', '?', and newlines.
     *
     * @param string $text
     * @return array  Array of trimmed non-empty sentence strings.
     */
    private function _split_sentences($text) {
        $parts = preg_split('/(?<=[.!?])\s+|[\n\r]+/', $text ?? '', -1, PREG_SPLIT_NO_EMPTY);
        return array_values(array_filter(array_map('trim', $parts), function($s){ return strlen($s) > 0; }));
    }

    /**
     * Return the first sentence that contains the keyword (case-insensitive).
     * Falls back to the first 200 characters of the transcript if no sentence
     * matches (guards against sentence-splitting edge cases).
     *
     * @param array  $sentences
     * @param string $keyword_lc  Lowercase keyword.
     * @return string
     */
    private function _find_sentence_with_keyword($sentences, $keyword_lc) {
        foreach ($sentences as $s) {
            if (mb_strpos(mb_strtolower($s), $keyword_lc) !== false) {
                return $s;
            }
        }
        // Fallback: return first sentence or truncated transcript.
        return !empty($sentences[0]) ? $sentences[0] : '';
    }

    /**
     * Make a focused OpenAI chat-completions call for transcript extraction.
     * Mirrors the cURL pattern in ChatAI_model but with lower temperature for
     * factual extraction.
     *
     * @param string $api_key  From stem_secret('openai_api_key').
     * @param string $prompt   Full prompt string.
     * @return string  The model's reply text, or '' on error.
     */
    private function _call_openai_for_extraction($api_key, $prompt) {
        $url  = 'https://api.openai.com/v1/chat/completions';
        $data = [
            'model'       => 'gpt-4o',
            'messages'    => [
                ['role' => 'system', 'content' => 'You extract concise factual answers from meeting transcripts. Reply only with the answer phrase or NOT MENTIONED.'],
                ['role' => 'user',   'content' => $prompt]
            ],
            'max_tokens'  => 150,
            'temperature' => 0.1
        ];

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'Authorization: Bearer ' . $api_key
        ]);
        curl_setopt($ch, CURLOPT_TIMEOUT, 20);

        $raw  = curl_exec($ch);
        $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($code !== 200 || !$raw) {
            log_message('error', 'mom_v2 _call_openai_for_extraction: HTTP ' . $code);
            return '';
        }

        $resp = json_decode($raw, true);
        return trim($resp['choices'][0]['message']['content'] ?? '');
    }

    // =========================================================================
    // PUBLIC HELPER: voice_to_typed_value
    // -------------------------------------------------------------------------
    // Coerce a raw voice transcript snippet to a typed answer_value based on
    // the question schema's answer_type. Used by /api/mom_v2_mandatory/voice_field_edit
    // for per-field voice edits on the Stage 3 structured form.
    //
    // Returns: ['answer_value' => string, 'confidence' => float (0..1), 'notes' => string]
    //
    // answer_type values handled (matches migration 037 schema):
    //   yes_no, rs_amount, dropdown, text, date, integer
    // =========================================================================
    public function voice_to_typed_value($transcript, $answer_type, $options_json = null, $voice_keywords_json = null) {
        $transcript = trim((string)$transcript);
        if ($transcript === '') {
            return ['answer_value' => '', 'confidence' => 0.0, 'notes' => 'empty_transcript'];
        }

        $tl = strtolower($transcript);

        switch ($answer_type) {
            case 'yes_no':
                return $this->_parse_yes_no($tl);
            case 'rs_amount':
            case 'amount':
            case 'integer':
            case 'number':
                return $this->_parse_amount($tl, $answer_type);
            case 'dropdown':
            case 'enum':
                return $this->_parse_dropdown($tl, $options_json, $voice_keywords_json);
            case 'date':
                return $this->_parse_date($tl);
            case 'text':
            case 'free_text':
            default:
                // Free text - light cleanup, trim filler.
                $cleaned = $this->_clean_free_text($transcript);
                return ['answer_value' => $cleaned, 'confidence' => 0.85, 'notes' => 'free_text'];
        }
    }

    private function _parse_yes_no($tl) {
        $yes = ['yes', 'yeah', 'yep', 'haan', 'ji haan', 'haa', 'correct', 'true', 'confirmed', 'positive', 'sure', 'absolutely', 'definitely'];
        $no  = ['no', 'nope', 'nahi', 'nahin', 'na', 'never', 'false', 'incorrect', 'negative', 'not yet'];
        foreach ($no as $n) {
            if (strpos($tl, $n) !== false) {
                return ['answer_value' => 'No', 'confidence' => 0.9, 'notes' => 'matched_no:' . $n];
            }
        }
        foreach ($yes as $y) {
            if (strpos($tl, $y) !== false) {
                return ['answer_value' => 'Yes', 'confidence' => 0.9, 'notes' => 'matched_yes:' . $y];
            }
        }
        return ['answer_value' => '', 'confidence' => 0.0, 'notes' => 'no_yes_no_match'];
    }

    private function _parse_amount($tl, $answer_type) {
        // Strip currency words to ease number-word parse.
        $clean = preg_replace('/\b(rs|rupees|rupee|inr|approximately|about|around|roughly)\b/i', ' ', $tl);
        $clean = preg_replace('/\s+/', ' ', trim($clean));

        // 1. Direct digits with optional commas and optional lakh/crore/k suffix.
        if (preg_match('/(\d{1,3}(?:[,\d]*)(?:\.\d+)?)\s*(crore|cr|lakh|lac|lakhs|lacs|thousand|k|million|mn)?/i', $clean, $m)) {
            $num = (float)str_replace(',', '', $m[1]);
            $unit = strtolower($m[2] ?? '');
            $mult = $this->_unit_multiplier($unit);
            $value = (int)round($num * $mult);
            if ($value > 0) {
                return ['answer_value' => (string)$value, 'confidence' => 0.92, 'notes' => 'digit_parse:' . $num . '*' . $mult];
            }
        }

        // 2. Word-form numbers like 'one crore', 'twenty five lakh', 'fifty thousand'.
        $value = $this->_word_to_number($clean);
        if ($value > 0) {
            return ['answer_value' => (string)$value, 'confidence' => 0.8, 'notes' => 'word_parse'];
        }

        return ['answer_value' => '', 'confidence' => 0.0, 'notes' => 'no_amount_found'];
    }

    private function _unit_multiplier($unit) {
        switch ($unit) {
            case 'crore':
            case 'cr':       return 10000000;
            case 'lakh':
            case 'lac':
            case 'lakhs':
            case 'lacs':     return 100000;
            case 'thousand':
            case 'k':        return 1000;
            case 'million':
            case 'mn':       return 1000000;
            default:         return 1;
        }
    }

    private function _word_to_number($tl) {
        $units = [
            'zero'=>0,'one'=>1,'two'=>2,'three'=>3,'four'=>4,'five'=>5,'six'=>6,'seven'=>7,'eight'=>8,'nine'=>9,
            'ten'=>10,'eleven'=>11,'twelve'=>12,'thirteen'=>13,'fourteen'=>14,'fifteen'=>15,
            'sixteen'=>16,'seventeen'=>17,'eighteen'=>18,'nineteen'=>19,
            'twenty'=>20,'thirty'=>30,'forty'=>40,'fifty'=>50,'sixty'=>60,'seventy'=>70,'eighty'=>80,'ninety'=>90,
            'hundred'=>100
        ];
        $tokens = preg_split('/[\s\-]+/', $tl);
        $base = 0;
        $current = 0;
        $unit_mult = 1;
        $found_any = false;
        $unit_word = '';
        foreach ($tokens as $t) {
            if (isset($units[$t])) {
                if ($units[$t] === 100) {
                    $current = ($current === 0 ? 1 : $current) * 100;
                } else {
                    $current += $units[$t];
                }
                $found_any = true;
            } elseif (in_array($t, ['crore','cr','lakh','lac','lakhs','lacs','thousand','k','million','mn'])) {
                $mult = $this->_unit_multiplier($t);
                $base += ($current === 0 ? 1 : $current) * $mult;
                $current = 0;
                $unit_mult = $mult;
                $unit_word = $t;
            }
        }
        $base += $current;
        return $found_any ? $base : 0;
    }

    private function _parse_dropdown($tl, $options_json, $voice_keywords_json) {
        $options = json_decode($options_json ?? '[]', true);
        if (!is_array($options) || empty($options)) {
            return ['answer_value' => '', 'confidence' => 0.0, 'notes' => 'no_options_defined'];
        }
        // Try exact substring match against each option label.
        foreach ($options as $opt) {
            $opt_lc = strtolower((string)$opt);
            if ($opt_lc !== '' && strpos($tl, $opt_lc) !== false) {
                return ['answer_value' => (string)$opt, 'confidence' => 0.9, 'notes' => 'dropdown_exact:' . $opt];
            }
        }
        // Try voice_keywords map - many keywords can route to the canonical option.
        $kw_map = json_decode($voice_keywords_json ?? '[]', true);
        if (is_array($kw_map)) {
            foreach ($kw_map as $entry) {
                // Accept either ['option'=>'X','keywords'=>[...]] or flat list of strings.
                if (is_array($entry) && !empty($entry['option']) && !empty($entry['keywords'])) {
                    foreach ((array)$entry['keywords'] as $kw) {
                        if (strpos($tl, strtolower($kw)) !== false) {
                            return ['answer_value' => (string)$entry['option'], 'confidence' => 0.75, 'notes' => 'dropdown_keyword:' . $kw];
                        }
                    }
                }
            }
        }
        return ['answer_value' => '', 'confidence' => 0.0, 'notes' => 'no_dropdown_match'];
    }

    private function _parse_date($tl) {
        // Try several common formats. PHP strtotime handles 'next monday', 'tomorrow',
        // '25 May', '25/05/2026' etc.
        $ts = @strtotime($tl);
        if ($ts !== false && $ts > 0) {
            return ['answer_value' => date('Y-m-d', $ts), 'confidence' => 0.85, 'notes' => 'strtotime'];
        }
        return ['answer_value' => '', 'confidence' => 0.0, 'notes' => 'no_date_parsed'];
    }

    private function _clean_free_text($s) {
        // Remove obvious leading filler like 'umm', 'so', 'okay', 'yeah so'.
        $s = preg_replace('/^(umm|uhh|so|okay|ok|yeah|right|like|well)[,\s]+/i', '', trim($s));
        return trim($s);
    }
}

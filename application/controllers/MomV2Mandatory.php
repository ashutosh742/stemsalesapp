<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * MomV2_Mandatory
 *
 * Migration 037 - MoM v2 Mandatory Schema + Brian Tracy Agenda Gate
 * Date: 2026-05-19
 *
 * All endpoints live under /api/mom_v2_mandatory/.
 * Every endpoint first checks the app_config 'mom_v2_mandatory_enabled' flag
 * (returns 503 if false) and then validates the Bearer token against the
 * STEM_DIGEST_TOKEN environment variable (returns 401 on mismatch).
 *
 * Routes to add in config/routes.php:
 *   POST  api/mom_v2_mandatory/lock_agenda     -> MomV2_Mandatory/lock_agenda
 *   POST  api/mom_v2_mandatory/voice_coverage  -> MomV2_Mandatory/voice_coverage
 *   POST  api/mom_v2_mandatory/submit_answers     -> MomV2_Mandatory/submit_answers
 *   POST  api/mom_v2_mandatory/voice_field_edit  -> MomV2_Mandatory/voice_field_edit
 *   GET   api/mom_v2_mandatory/cm_queue           -> MomV2_Mandatory/cm_queue
 *   POST  api/mom_v2_mandatory/cm_approve      -> MomV2_Mandatory/cm_approve
 *   GET   api/mom_v2_mandatory/probe           -> MomV2_Mandatory/probe
 *
 * PARALLEL DEMO ONLY. Does NOT modify the production mom_data table.
 */
class MomV2_Mandatory extends CI_Controller {

    // Quality grade thresholds. Voice pct is coverage_pct from voice_coverage.
    // All grade conditions assume all required questions are confirmed in form
    // unless noted.
    //   A+: all required confirmed + voice >= 90
    //   A : all required confirmed + voice 75-89
    //   B : all required confirmed + voice 60-74
    //   C : >= 80 percent answered + voice >= 50 (required NOT fully met)
    //   D : anything else

    public function __construct() {
        parent::__construct();
        $this->load->database();
        $this->load->model('MomV2_Agenda_Gate_model');
        $this->load->model('MomV2_Voice_Coverage_Agent');
        $this->load->helper('url');
        // Guard on every request before anything else runs.
        $this->_guard();
    }

    // =========================================================================
    // ENDPOINTS
    // =========================================================================

    /**
     * POST /api/mom_v2_mandatory/lock_agenda
     * Body: event_id, bd_uid, cid_id
     *
     * Locks the Brian Tracy agenda gate for the given event. Returns the lock_id
     * and the list of required questions the BD committed to ask.
     */
    public function lock_agenda() {
        $event_id = (int)$this->input->post('event_id');
        $bd_uid   = (int)$this->input->post('bd_uid');
        $cid_id   = (int)$this->input->post('cid_id');

        if (!$event_id || !$bd_uid || !$cid_id) {
            $this->_resp(['ok' => false, 'error' => 'missing_params', 'required' => ['event_id', 'bd_uid', 'cid_id']], 422);
        }

        $lock_id = $this->MomV2_Agenda_Gate_model->lock_agenda($event_id, $bd_uid, $cid_id);

        if ($lock_id === false) {
            // Already locked or lookup failed.
            $existing = $this->MomV2_Agenda_Gate_model->get_lock($event_id);
            if ($existing) {
                $this->_resp(['ok' => false, 'error' => 'already_locked', 'lock_id' => (int)$existing['lock_id']], 409);
            }
            $this->_resp(['ok' => false, 'error' => 'lock_failed'], 500);
        }

        // Return the required questions so the mobile app can build the pre-meeting
        // checklist immediately.
        $required_questions = $this->MomV2_Agenda_Gate_model->get_required_questions($cid_id, 0, null, 0);

        $this->_resp([
            'ok'                 => true,
            'lock_id'            => $lock_id,
            'required_questions' => $this->_format_questions_for_client($required_questions)
        ]);
    }

    /**
     * POST /api/mom_v2_mandatory/voice_coverage
     * Body: event_id, bd_uid, voice_clip_url, transcript_text, whisper_confidence, lang
     *
     * Scans the Whisper transcript against required questions and stores the
     * result. Returns per-question coverage detail and overall pass/fail.
     */
    public function voice_coverage() {
        $event_id          = (int)$this->input->post('event_id');
        $bd_uid            = (int)$this->input->post('bd_uid');
        $voice_clip_url    = $this->input->post('voice_clip_url');
        $transcript_text   = $this->input->post('transcript_text');
        $whisper_confidence= (float)$this->input->post('whisper_confidence');
        $lang              = $this->input->post('lang') ?: 'en';

        if (!$event_id || !$bd_uid) {
            $this->_resp(['ok' => false, 'error' => 'missing_params', 'required' => ['event_id', 'bd_uid']], 422);
        }

        if (empty($transcript_text)) {
            $this->_resp(['ok' => false, 'error' => 'transcript_text_required'], 422);
        }

        // Store the voice_clip_url on the coverage row via a quick pre-insert.
        // The agent will compute the rest.
        $result = $this->MomV2_Voice_Coverage_Agent->scan_transcript(
            $event_id, $bd_uid, $transcript_text, $whisper_confidence, $lang
        );

        if (!empty($result['error'])) {
            $this->_resp(['ok' => false, 'error' => $result['error'], 'message' => $result['message'] ?? null], 422);
        }

        // If a voice_clip_url was supplied, update the coverage row now.
        if (!empty($voice_clip_url) && !empty($result['coverage_id'])) {
            $this->db->where('coverage_id', (int)$result['coverage_id']);
            $this->db->update('mom_v2_voice_coverage', ['voice_clip_url' => $voice_clip_url]);
        }

        // Update the submission row with the latest voice coverage pct.
        $this->_upsert_submission_voice($event_id, (float)$result['coverage_pct'], (int)$result['recording_attempt']);

        $this->_resp([
            'ok'               => true,
            'coverage_pct'     => $result['coverage_pct'],
            'coverage_passed'  => (bool)$result['coverage_passed'],
            'per_question'     => $result['per_question_coverage'] ?? [],
            'recording_attempt'=> $result['recording_attempt']
        ]);
    }

    /**
     * POST /api/mom_v2_mandatory/submit_answers
     * Body: event_id, answers_json (array of {question_id, answer_value, auto_filled, voice_confidence})
     *
     * Validates all required questions are answered, writes mom_v2_answers rows,
     * upserts the mom_v2_submission row, computes quality grade, and returns the
     * submission result.
     */
    public function submit_answers() {
        $event_id    = (int)$this->input->post('event_id');
        $answers_raw = $this->input->post('answers_json');

        if (!$event_id || empty($answers_raw)) {
            $this->_resp(['ok' => false, 'error' => 'missing_params', 'required' => ['event_id', 'answers_json']], 422);
        }

        // Decode answers_json - accepts both JSON string and already-decoded array.
        if (is_string($answers_raw)) {
            $answers = json_decode($answers_raw, true);
        } else {
            $answers = $answers_raw;
        }

        if (!is_array($answers)) {
            $this->_resp(['ok' => false, 'error' => 'answers_json_invalid'], 422);
        }

        // Load the agenda lock to know required question ids and context.
        $lock = $this->MomV2_Agenda_Gate_model->get_lock($event_id);
        if (empty($lock)) {
            $this->_resp(['ok' => false, 'error' => 'no_agenda_lock', 'message' => 'Agenda must be locked before submitting answers.'], 422);
        }

        $required_ids     = json_decode($lock['required_questions_json'], true) ?: [];
        $bd_uid           = (int)$lock['bd_uid'];
        $cid_id           = (int)$lock['cid_id'];
        $answers_required = count($required_ids);

        // Index submitted answers by question_id for fast lookup.
        $submitted = [];
        foreach ($answers as $a) {
            $qid = (int)($a['question_id'] ?? 0);
            if ($qid > 0) {
                $submitted[$qid] = $a;
            }
        }

        // Check that every required question has a non-empty answer.
        $missing_required = [];
        foreach ($required_ids as $qid) {
            $qid = (int)$qid;
            if (empty($submitted[$qid]['answer_value'])) {
                $missing_required[] = $qid;
            }
        }

        if (!empty($missing_required)) {
            $this->_resp([
                'ok'               => false,
                'error'            => 'required_questions_unanswered',
                'missing_ids'      => array_values($missing_required),
                'message'          => 'All required questions must be answered before submitting.'
            ], 422);
        }

        // Upsert mom_v2_answers rows for every submitted answer.
        $answers_completed = 0;
        foreach ($submitted as $qid => $a) {
            $confirmed = !empty($a['answer_value']) ? 1 : 0;

            // Use INSERT ... ON DUPLICATE KEY to handle re-submissions gracefully.
            $row = [
                'event_id'       => $event_id,
                'bd_uid'         => $bd_uid,
                'cid_id'         => $cid_id,
                'question_id'    => $qid,
                'answer_value'   => (string)$a['answer_value'],
                'auto_filled'    => (int)(!empty($a['auto_filled'])),
                'bd_confirmed'   => $confirmed,
                'voice_confidence'=> isset($a['voice_confidence']) ? (float)$a['voice_confidence'] : null,
                'submitted_at'   => date('Y-m-d H:i:s')
            ];

            // Attempt insert; on duplicate event_id+question_id update the value.
            $this->db->query(
                'INSERT INTO mom_v2_answers
                    (event_id, bd_uid, cid_id, question_id, answer_value, auto_filled, bd_confirmed, voice_confidence, submitted_at)
                 VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
                 ON DUPLICATE KEY UPDATE
                    answer_value    = VALUES(answer_value),
                    auto_filled     = VALUES(auto_filled),
                    bd_confirmed    = VALUES(bd_confirmed),
                    voice_confidence= VALUES(voice_confidence),
                    submitted_at    = VALUES(submitted_at)',
                [
                    $event_id, $bd_uid, $cid_id, $qid,
                    $row['answer_value'], $row['auto_filled'], $confirmed,
                    $row['voice_confidence'], $row['submitted_at']
                ]
            );

            if ($confirmed) $answers_completed++;
        }

        // Fetch the latest voice coverage for this event.
        $coverage      = $this->MomV2_Voice_Coverage_Agent->get_latest_coverage($event_id);
        $voice_pct     = isset($coverage['coverage_pct']) ? (float)$coverage['coverage_pct'] : null;
        $answers_pct   = ($answers_required > 0) ? (($answers_completed / $answers_required) * 100) : 100;

        // Compute quality grade per migration 037 rules.
        $quality = $this->_compute_quality_grade($answers_completed, $answers_required, $voice_pct, $answers_pct);

        // Upsert the top-level mom_v2_submission row.
        $submission_id = $this->_upsert_submission_form($event_id, $bd_uid, $cid_id, $lock, $answers_completed, $answers_required, $voice_pct, $quality);

        $this->_resp([
            'ok'            => true,
            'submission_id' => $submission_id,
            'status'        => 'submitted',
            'quality_grade' => $quality['grade'],
            'quality_score' => $quality['score']
        ]);
    }

    /**
     * POST /api/mom_v2_mandatory/voice_field_edit
     * Body: event_id, bd_uid, question_id, transcript, whisper_confidence (optional)
     *
     * Per-field voice edit on the Stage 3 structured form. BD taps the mic on one
     * question row, speaks the new value, Whisper transcribes, and this endpoint
     * coerces the transcript to the typed value (yes_no, Rs amount, dropdown, etc.)
     * and overwrites the mom_v2_answers row for that question. No versioning per
     * product decision (last voice wins). Returns the parsed answer_value plus
     * voice_confidence so the mobile UI can update the field in place.
     */
    public function voice_field_edit() {
        $event_id           = (int)$this->input->post('event_id');
        $bd_uid             = (int)$this->input->post('bd_uid');
        $question_id        = (int)$this->input->post('question_id');
        $transcript         = (string)$this->input->post('transcript');
        $whisper_confidence = $this->input->post('whisper_confidence');
        $whisper_confidence = ($whisper_confidence === null || $whisper_confidence === '') ? null : (float)$whisper_confidence;

        if (!$event_id || !$bd_uid || !$question_id) {
            $this->_resp(['ok' => false, 'error' => 'missing_params', 'required' => ['event_id', 'bd_uid', 'question_id']], 422);
        }
        if (trim($transcript) === '') {
            $this->_resp(['ok' => false, 'error' => 'transcript_required'], 422);
        }

        // Lookup the agenda lock so we can confirm cid_id and that this event is
        // legitimately in flight. Voice edits only work after agenda lock.
        $lock = $this->MomV2_Agenda_Gate_model->get_lock($event_id);
        if (empty($lock)) {
            $this->_resp(['ok' => false, 'error' => 'no_agenda_lock', 'message' => 'Agenda must be locked before voice edits.'], 422);
        }
        $cid_id = (int)$lock['cid_id'];

        // Fetch question schema row so we know answer_type plus voice_keywords_json
        // plus any dropdown options.
        $q = $this->db->query(
            'SELECT question_id, answer_type, options_json, voice_keywords_json FROM mom_v2_question_schema WHERE question_id = ? LIMIT 1',
            [$question_id]
        )->row_array();

        if (empty($q)) {
            $this->_resp(['ok' => false, 'error' => 'unknown_question_id'], 422);
        }

        // Coerce transcript to typed answer_value.
        $coerced = $this->MomV2_Voice_Coverage_Agent->voice_to_typed_value(
            $transcript,
            $q['answer_type'],
            $q['options_json'],
            $q['voice_keywords_json']
        );

        $answer_value     = $coerced['answer_value'];
        $parse_confidence = (float)$coerced['confidence'];
        // Combine Whisper confidence (if present) with the parse confidence using
        // a simple multiplicative blend, capped at 1.0.
        $voice_confidence = $parse_confidence;
        if ($whisper_confidence !== null) {
            $voice_confidence = min(1.0, $whisper_confidence * $parse_confidence);
        }

        if ($answer_value === '' || $answer_value === null) {
            $this->_resp([
                'ok'              => false,
                'error'           => 'transcript_could_not_be_parsed',
                'transcript_heard'=> $transcript,
                'answer_type'     => $q['answer_type'],
                'parse_notes'     => $coerced['notes'] ?? ''
            ], 422);
        }

        // Overwrite the mom_v2_answers row (no versioning - last voice wins).
        $this->db->query(
            'INSERT INTO mom_v2_answers
                (event_id, bd_uid, cid_id, question_id, answer_value, auto_filled, bd_confirmed, voice_confidence, submitted_at)
             VALUES (?, ?, ?, ?, ?, 1, 1, ?, ?)
             ON DUPLICATE KEY UPDATE
                answer_value     = VALUES(answer_value),
                auto_filled      = 1,
                bd_confirmed     = 1,
                voice_confidence = VALUES(voice_confidence),
                submitted_at     = VALUES(submitted_at)',
            [
                $event_id, $bd_uid, $cid_id, $question_id,
                $answer_value, $voice_confidence, date('Y-m-d H:i:s')
            ]
        );

        $this->_resp([
            'ok'               => true,
            'question_id'      => $question_id,
            'answer_value'     => $answer_value,
            'answer_type'      => $q['answer_type'],
            'voice_confidence' => round($voice_confidence, 3),
            'parse_confidence' => round($parse_confidence, 3),
            'transcript_heard' => $transcript,
            'parse_notes'      => $coerced['notes'] ?? ''
        ]);
    }

    /**
     * GET /api/mom_v2_mandatory/cm_queue?cm_uid=X&status=pending_cm
     *
     * Returns all submissions assigned to the CM for review. Joins the full
     * 15-field structured answer set alongside submission metadata.
     */
    public function cm_queue() {
        $cm_uid = (int)$this->input->get('cm_uid');
        $status = $this->input->get('status') ?: 'pending_cm';

        if (!$cm_uid) {
            $this->_resp(['ok' => false, 'error' => 'cm_uid_required'], 422);
        }

        // Build the queue query. We join init_call and user for school and BD info.
        $this->db->select(
            's.submission_id, s.event_id, s.bd_uid, s.cid_id,
             s.cm_uid, s.agenda_locked, s.voice_coverage_pct,
             s.answers_completed, s.answers_required, s.quality_grade, s.quality_score,
             s.status, s.submitted_at, s.cm_action_at, s.rejected_question_ids_json,
             u.firstname AS bd_firstname, u.lastname AS bd_lastname,
             ic.company_name AS school_name, ic.current_status_id AS cstatus,
             ic.partner_type,
             al.cstatus_at_lock, al.actiontype_id,
             al.required_questions_json'
        );
        $this->db->from('mom_v2_submission s');
        $this->db->join('user u',                        'u.uid = s.bd_uid',               'left');
        $this->db->join('init_call ic',                  'ic.id = s.cid_id',               'left');
        $this->db->join('mom_v2_meeting_agenda_lock al', 'al.event_id = s.event_id',       'left');
        $this->db->where('s.cm_uid', $cm_uid);
        $this->db->where('s.status', $status);
        $this->db->order_by('s.submitted_at', 'ASC');
        $this->db->limit(100);
        $rows = $this->db->get()->result_array();

        // For each submission row, attach the structured answers.
        foreach ($rows as &$row) {
            $row['answers'] = $this->_load_answers_for_submission((int)$row['event_id']);
        }
        unset($row);

        $this->_resp(['ok' => true, 'rows' => $rows, 'count' => count($rows)]);
    }

    /**
     * POST /api/mom_v2_mandatory/cm_approve
     * Body: submission_id, cm_uid, action (approve|reject), reason, rejected_question_ids
     *
     * CM approves or rejects a submission. Updates mom_v2_submission status and
     * timestamps. Returns the updated status and action timestamp.
     */
    public function cm_approve() {
        $submission_id        = (int)$this->input->post('submission_id');
        $cm_uid               = (int)$this->input->post('cm_uid');
        $action               = $this->input->post('action');
        $reason               = $this->input->post('reason');
        $rejected_question_raw= $this->input->post('rejected_question_ids');

        if (!$submission_id || !$cm_uid || !in_array($action, ['approve', 'reject'], true)) {
            $this->_resp(['ok' => false, 'error' => 'missing_or_invalid_params', 'required' => ['submission_id', 'cm_uid', 'action(approve|reject)']], 422);
        }

        // Confirm the submission exists and belongs to this CM.
        $this->db->where('submission_id', $submission_id);
        $sub = $this->db->get('mom_v2_submission')->row_array();
        if (empty($sub)) {
            $this->_resp(['ok' => false, 'error' => 'submission_not_found'], 404);
        }
        if ((int)$sub['cm_uid'] !== $cm_uid) {
            $this->_resp(['ok' => false, 'error' => 'submission_not_assigned_to_this_cm'], 403);
        }

        $new_status   = ($action === 'approve') ? 'approved' : 'rejected';
        $cm_action_at = date('Y-m-d H:i:s');

        // Decode rejected_question_ids if supplied (only meaningful on reject).
        $rejected_ids = null;
        if ($action === 'reject' && !empty($rejected_question_raw)) {
            if (is_string($rejected_question_raw)) {
                $rejected_ids = json_decode($rejected_question_raw, true);
            } else {
                $rejected_ids = $rejected_question_raw;
            }
            if (!is_array($rejected_ids)) $rejected_ids = [];
        }

        $update = [
            'status'                    => $new_status,
            'cm_action_at'              => $cm_action_at,
            'cm_action_reason'          => (string)$reason,
            'rejected_question_ids_json'=> ($rejected_ids !== null) ? json_encode($rejected_ids) : null
        ];

        $this->db->where('submission_id', $submission_id);
        $this->db->update('mom_v2_submission', $update);

        log_message('info', 'mom_v2 cm_approve: submission_id=' . $submission_id . ' action=' . $action . ' cm_uid=' . $cm_uid);

        $this->_resp([
            'ok'           => true,
            'submission_id'=> $submission_id,
            'status'       => $new_status,
            'cm_action_at' => $cm_action_at
        ]);
    }

    /**
     * GET /api/mom_v2_mandatory/probe
     *
     * Health check endpoint. Returns HTTP 200 if migration 037 schema is present
     * (checks that at least 1 question exists in mom_v2_question_schema).
     * Does NOT require Bearer auth - used by deployment verification scripts.
     */
    public function probe() {
        // probe is exempt from the Bearer check but must still pass the enabled
        // flag so that ops can confirm the flag is set before switching traffic.
        $count = $this->db->count_all('mom_v2_question_schema');
        $this->_resp([
            'ok'             => true,
            'migration'      => '037',
            'question_count' => (int)$count,
            'deployed'       => $count >= 1
        ]);
    }

    // =========================================================================
    // PRIVATE: GUARDS, RESPONSE HELPERS, QUALITY GRADING
    // =========================================================================

    /**
     * Run all pre-flight guards on every request.
     *   1. app_config mom_v2_mandatory_enabled must be 'true'.
     *   2. Authorization header must carry a valid Bearer STEM_DIGEST_TOKEN.
     *
     * probe() is excluded from the Bearer check (it is a status-only endpoint)
     * but still requires the feature flag to be true.
     */
    private function _guard() {
        // Flag check.
        $this->db->where('config_key', 'mom_v2_mandatory_enabled');
        $flag = $this->db->get('app_config')->row_array();
        if (empty($flag) || strtolower($flag['config_value']) !== 'true') {
            $this->_resp(['ok' => false, 'error' => 'MoM v2 disabled by config'], 503);
        }

        // probe() endpoint skips the Bearer check.
        $method = $this->router->fetch_method();
        if ($method === 'probe') return;

        // Bearer token check.
        $hdr = $this->input->get_request_header('Authorization');
        if (!$hdr || strpos($hdr, 'Bearer ') !== 0) {
            $this->_resp(['ok' => false, 'error' => 'unauthorized', 'message' => 'Bearer token required.'], 401);
        }

        $supplied_token = substr($hdr, 7);
        $expected_token = getenv('STEM_DIGEST_TOKEN');

        if (empty($expected_token) || !hash_equals($expected_token, $supplied_token)) {
            $this->_resp(['ok' => false, 'error' => 'unauthorized', 'message' => 'Invalid token.'], 401);
        }
    }

    /**
     * Emit a JSON response and halt.
     *
     * @param array $data
     * @param int   $code  HTTP status code.
     */
    private function _resp($data, $code = 200) {
        http_response_code($code);
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }

    /**
     * Compute the quality grade for a submission per migration 037 rules.
     *
     * Rules (evaluated in order, first match wins):
     *   A+: all required questions confirmed AND voice_pct >= 90
     *   A : all required questions confirmed AND voice_pct 75-89
     *   B : all required questions confirmed AND voice_pct 60-74
     *   C : answers_pct >= 80 AND voice_pct >= 50 (required not fully met)
     *   D : everything else
     *
     * @param int        $answers_completed  Count of bd_confirmed answers.
     * @param int        $answers_required   Count of required questions.
     * @param float|null $voice_pct          Voice coverage percent (null if no recording).
     * @param float      $answers_pct        (answers_completed / answers_required) * 100.
     * @return array {grade, score}
     */
    private function _compute_quality_grade($answers_completed, $answers_required, $voice_pct, $answers_pct) {
        $all_required_confirmed = ($answers_required > 0 && $answers_completed >= $answers_required);
        $vp = ($voice_pct === null) ? 0.0 : (float)$voice_pct;

        if ($all_required_confirmed && $vp >= 90) {
            $grade = 'A+'; $score = 100;
        } elseif ($all_required_confirmed && $vp >= 75) {
            $grade = 'A';  $score = round(75 + (($vp - 75) / 15) * 15, 1);
        } elseif ($all_required_confirmed && $vp >= 60) {
            $grade = 'B';  $score = round(60 + (($vp - 60) / 15) * 15, 1);
        } elseif ($answers_pct >= 80 && $vp >= 50) {
            $grade = 'C';  $score = round(($answers_pct * 0.5) + ($vp * 0.3), 1);
        } else {
            $grade = 'D';  $score = round(($answers_pct * 0.4) + ($vp * 0.2), 1);
        }

        return ['grade' => $grade, 'score' => min(100, max(0, $score))];
    }

    /**
     * Upsert a mom_v2_submission row with voice coverage data only (called after
     * a voice_coverage POST before the form is submitted).
     *
     * @param int   $event_id
     * @param float $coverage_pct
     * @param int   $attempt
     */
    private function _upsert_submission_voice($event_id, $coverage_pct, $attempt) {
        $lock = $this->MomV2_Agenda_Gate_model->get_lock($event_id);
        if (empty($lock)) return;

        $this->db->where('event_id', $event_id);
        $existing = $this->db->get('mom_v2_submission')->row_array();

        if ($existing) {
            $this->db->where('event_id', $event_id);
            $this->db->update('mom_v2_submission', [
                'voice_coverage_pct' => $coverage_pct,
                'status'             => 'voice_done'
            ]);
        } else {
            $required_ids = json_decode($lock['required_questions_json'], true) ?: [];
            $this->db->insert('mom_v2_submission', [
                'event_id'          => $event_id,
                'bd_uid'            => (int)$lock['bd_uid'],
                'cid_id'            => (int)$lock['cid_id'],
                'agenda_locked'     => 1,
                'voice_coverage_pct'=> $coverage_pct,
                'answers_required'  => count($required_ids),
                'answers_completed' => 0,
                'status'            => 'voice_done'
            ]);
        }
    }

    /**
     * Upsert the mom_v2_submission row with final form submission data and
     * quality grade. Returns the submission_id.
     *
     * @param int   $event_id
     * @param int   $bd_uid
     * @param int   $cid_id
     * @param array $lock            Agenda lock row.
     * @param int   $answers_completed
     * @param int   $answers_required
     * @param float|null $voice_pct
     * @param array $quality         {grade, score} from _compute_quality_grade.
     * @return int  submission_id
     */
    private function _upsert_submission_form($event_id, $bd_uid, $cid_id, $lock, $answers_completed, $answers_required, $voice_pct, $quality) {
        $this->db->where('event_id', $event_id);
        $existing = $this->db->get('mom_v2_submission')->row_array();

        $data = [
            'bd_uid'            => $bd_uid,
            'cid_id'            => $cid_id,
            'agenda_locked'     => 1,
            'voice_coverage_pct'=> $voice_pct,
            'answers_completed' => $answers_completed,
            'answers_required'  => $answers_required,
            'quality_grade'     => $quality['grade'],
            'quality_score'     => $quality['score'],
            'status'            => 'submitted',
            'submitted_at'      => date('Y-m-d H:i:s')
        ];

        if ($existing) {
            $this->db->where('event_id', $event_id);
            $this->db->update('mom_v2_submission', $data);
            return (int)$existing['submission_id'];
        } else {
            $data['event_id'] = $event_id;
            $this->db->insert('mom_v2_submission', $data);
            return $this->db->insert_id();
        }
    }

    /**
     * Load the structured answer rows for a given event and join with question
     * metadata so the CM review surface gets full context.
     *
     * @param int $event_id
     * @return array
     */
    private function _load_answers_for_submission($event_id) {
        $this->db->select(
            'a.answer_id, a.question_id, a.answer_value, a.auto_filled,
             a.bd_confirmed, a.voice_confidence, a.submitted_at,
             q.sr_no, q.question_text, q.answer_type, q.options_json,
             q.required_always, q.required_rp_only, q.sort_order'
        );
        $this->db->from('mom_v2_answers a');
        $this->db->join('mom_v2_question_schema q', 'q.question_id = a.question_id', 'left');
        $this->db->where('a.event_id', $event_id);
        $this->db->order_by('q.sort_order', 'ASC');
        return $this->db->get()->result_array();
    }

    /**
     * Format question rows for the client-facing lock_agenda response.
     * Returns only the fields the mobile app needs.
     *
     * @param array $questions  Rows from mom_v2_question_schema.
     * @return array
     */
    private function _format_questions_for_client($questions) {
        $out = [];
        foreach ($questions as $q) {
            $out[] = [
                'question_id' => (int)$q['question_id'],
                'sr_no'       => (int)$q['sr_no'],
                'question_text'=> $q['question_text'],
                'answer_type' => $q['answer_type'],
                'options'     => !empty($q['options_json']) ? json_decode($q['options_json'], true) : null
            ];
        }
        return $out;
    }
}

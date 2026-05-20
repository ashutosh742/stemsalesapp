<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * MomController controller
 *
 * JSON entry point for the mobile MoM voice-to-draft flow. Wraps the existing
 * chatai_model::call_openai pipeline and returns a structured MoM payload that
 * the BD can review on screen before saving via the legacy save_mom path.
 *
 * Endpoints:
 *   POST /MomController/api_draft   - convert a transcript to structured MoM
 *
 * Auth: session cookie (PHPSESSID) from /Menu/api_login. Falls back to
 * Bearer STEM_DIGEST_TOKEN for admin/QA callers.
 *
 * Created on feature/json-mobile-endpoints branch, 2026-05-20.
 */
class MomController extends CI_Controller
{
    const MAX_TRANSCRIPT_LEN = 12000; // chars, ~2500 spoken words

    public function __construct()
    {
        parent::__construct();
        $this->load->database();
        $this->load->library('session');
    }

    // ------------------------------------------------------------------
    private function _json($data, $code = 200)
    {
        $this->output
             ->set_status_header($code)
             ->set_content_type('application/json')
             ->set_output(json_encode($data));
        exit;
    }

    // ------------------------------------------------------------------
    private function _json_body()
    {
        $raw = $this->input->raw_input_stream;
        if ($raw) {
            $decoded = json_decode($raw, true);
            if (json_last_error() === JSON_ERROR_NONE) return $decoded;
        }
        return $this->input->post(null, true) ?: [];
    }

    // ------------------------------------------------------------------
    private function _auth_uid()
    {
        $user = $this->session->userdata('user');
        if (!empty($user['user_id'])) return (int)$user['user_id'];

        $hdr = $this->input->get_request_header('Authorization');
        if ($hdr && strpos($hdr, 'Bearer ') === 0) {
            $token    = trim(substr($hdr, 7));
            $expected = getenv('STEM_DIGEST_TOKEN');
            if ($expected && hash_equals($expected, $token)) {
                return (int)$this->input->get_post('uid') ?: 0;
            }
        }
        return null;
    }

    // ==================================================================
    // POST /MomController/api_draft
    //
    // Body (JSON):
    //   task_id     int     required  daily_planner task id this MoM belongs to
    //   transcript  string  required  raw transcript text from Whisper
    //
    // Returns:
    //   { ok, task_id, summary, facts[], next_action, quality_score,
    //     drafted_at }
    // ==================================================================
    public function api_draft()
    {
        if (strtolower($this->input->method()) !== 'post') {
            $this->_json(['ok' => false, 'error' => 'method_not_allowed'], 405);
        }

        $uid = $this->_auth_uid();
        if (!$uid) {
            $this->_json(['ok' => false, 'error' => 'unauthorized'], 401);
        }

        $body       = $this->_json_body();
        $task_id    = isset($body['task_id']) ? (int)$body['task_id'] : 0;
        $transcript = isset($body['transcript']) ? trim($body['transcript']) : '';

        if (!$task_id) {
            $this->_json(['ok' => false, 'error' => 'missing_task_id'], 400);
        }
        if (!$transcript) {
            $this->_json(['ok' => false, 'error' => 'missing_transcript'], 400);
        }
        if (strlen($transcript) > self::MAX_TRANSCRIPT_LEN) {
            $this->_json(['ok' => false, 'error' => 'transcript_too_long',
                          'message' => 'transcript must be under '
                                       . self::MAX_TRANSCRIPT_LEN . ' chars'], 413);
        }

        // Build the prompt. Keep it short - the existing chatai pipeline
        // already handles the system message and JSON-mode response.
        $prompt = "Convert this STEM Learning sales meeting transcript into a "
                . "structured MoM. Return strict JSON with keys: summary "
                . "(2-3 sentences), facts (array of short bullet strings), "
                . "next_action (one sentence), quality_score (0-100 integer "
                . "estimating MoM clarity). Transcript:\n\n" . $transcript;

        // Delegate to the shared chatai_model. The model is loaded by the
        // Chat controller bootstrap; load it here defensively.
        $resp = null;
        if (!isset($this->chatai_model)) {
            $model_path = APPPATH . 'models/Chatai_model.php';
            if (file_exists($model_path)) {
                $this->load->model('Chatai_model', 'chatai_model');
            }
        }
        if (isset($this->chatai_model) && method_exists($this->chatai_model, 'call_openai')) {
            $resp = $this->chatai_model->call_openai($prompt);
        }

        if ($resp === null) {
            $this->_json(['ok' => false, 'error' => 'chatai_unavailable'], 503);
        }

        // Normalize the LLM response. Accept either a JSON string or an
        // already-decoded array.
        $parsed = is_array($resp) ? $resp : json_decode($resp, true);
        if (!is_array($parsed)) {
            $this->_json(['ok' => false, 'error' => 'invalid_llm_response',
                          'raw'   => is_string($resp) ? substr($resp, 0, 500) : null], 502);
        }

        $this->_json([
            'ok'            => true,
            'task_id'       => $task_id,
            'summary'       => isset($parsed['summary'])       ? (string)$parsed['summary'] : '',
            'facts'         => isset($parsed['facts'])         ? (array)$parsed['facts']    : [],
            'next_action'   => isset($parsed['next_action'])   ? (string)$parsed['next_action'] : '',
            'quality_score' => isset($parsed['quality_score']) ? (int)$parsed['quality_score'] : 0,
            'drafted_at'    => date('c'),
        ]);
    }
}

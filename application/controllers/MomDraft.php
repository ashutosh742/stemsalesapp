<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * MomDraft controller
 *
 * Mobile-facing MoM draft generator. Wraps MomV2_model::draft so the mobile
 * app can request a fresh draft for a given lead and meeting context without
 * going through the full MoM v2 submission flow.
 *
 * Endpoint:
 *   POST /api/draft   - generate a MoM draft for a lead
 *
 * Auth: Bearer STEM_DIGEST_TOKEN.
 * Read-only (does not persist anything to mom_v2_draft until /api/mom/v2/save_draft).
 *
 * Routes to add in application/config/routes.php:
 *   $route['api/draft']['post'] = 'momdraft/api_draft';
 *
 * Created on feature/mobile-api-endpoints branch, 2026-05-20.
 */
class MomDraft extends CI_Controller
{
    const MAX_TRANSCRIPT_LEN = 20000;

    public function __construct()
    {
        parent::__construct();
        $this->load->database();
        $this->load->helper('url');
        $this->_auth_bearer();
    }

    // ------------------------------------------------------------------
    private function _auth_bearer()
    {
        $hdr = $this->input->get_request_header('Authorization');
        if (!$hdr || strpos($hdr, 'Bearer ') !== 0) {
            $this->_json(['error' => 'unauthorized', 'detail' => 'missing_bearer_header'], 401);
        }
        $token    = trim(substr($hdr, 7));
        $expected = getenv('STEM_DIGEST_TOKEN');
        if (!$expected || $token !== $expected) {
            $this->_json(['error' => 'unauthorized', 'detail' => 'invalid_token'], 401);
        }
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

    // ==================================================================
    // POST /api/draft
    //
    // Body (JSON):
    //   uid           int     required  caller BD uid
    //   cid_id        int     required  init_call.cid_id the meeting is about
    //   event_id      int     optional  tblcallevents.id if meeting already logged
    //   transcript    string  optional  raw text from voice-to-text (max 20000 chars)
    //   meeting_type  string  optional  fresh | rp | no_rp | barge (default fresh)
    //
    // Returns:
    //   { ok, draft: { summary, key_points[], action_items[], suggested_cstatus,
    //                  suggested_purpose_id, dm_contact_present, generated_at } }
    // ==================================================================
    public function api_draft()
    {
        if ($this->input->method() !== 'post') {
            $this->_json(['error' => 'post_only'], 405);
        }

        $body = $this->_json_body();

        $uid          = isset($body['uid'])          ? (int)$body['uid']    : null;
        $cid_id       = isset($body['cid_id'])       ? (int)$body['cid_id'] : null;
        $event_id     = isset($body['event_id'])     ? (int)$body['event_id'] : null;
        $transcript   = isset($body['transcript'])   ? trim($body['transcript']) : '';
        $meeting_type = isset($body['meeting_type']) ? strtolower($body['meeting_type']) : 'fresh';

        if (!$uid) {
            $this->_json(['error' => 'missing_uid', 'message' => 'uid is required'], 400);
        }
        if (!$cid_id) {
            $this->_json(['error' => 'missing_cid_id', 'message' => 'cid_id is required'], 400);
        }

        $valid_types = ['fresh', 'rp', 'no_rp', 'barge'];
        if (!in_array($meeting_type, $valid_types)) {
            $this->_json(['error' => 'invalid_meeting_type',
                'message' => 'meeting_type must be one of: ' . implode(', ', $valid_types)], 400);
        }

        if (strlen($transcript) > self::MAX_TRANSCRIPT_LEN) {
            $this->_json(['error' => 'transcript_too_long',
                'message' => 'transcript max ' . self::MAX_TRANSCRIPT_LEN . ' chars'], 400);
        }

        // Confirm caller exists and owns this lead (or is a CM type_id=13)
        $caller = $this->db->query(
            "SELECT uid, type_id FROM user WHERE uid = ? LIMIT 1",
            [$uid]
        )->row_array();
        if (!$caller) {
            $this->_json(['error' => 'caller_not_found'], 404);
        }

        $lead = $this->db->query(
            "SELECT cid_id, mainbd, school_name, current_status_id
               FROM init_call WHERE cid_id = ? LIMIT 1",
            [$cid_id]
        )->row_array();
        if (!$lead) {
            $this->_json(['error' => 'lead_not_found'], 404);
        }
        if ((int)$lead['mainbd'] !== $uid && (int)$caller['type_id'] !== 13) {
            $this->_json(['error' => 'forbidden',
                'message' => 'caller is not the lead owner or a CM'], 403);
        }

        // Delegate to MomV2 model if available, else return a structured stub
        $model_path = APPPATH . 'models/MomV2_model.php';
        if (file_exists($model_path)) {
            $this->load->model('MomV2_model');
            if (method_exists($this->MomV2_model, 'draft')) {
                $result = $this->MomV2_model->draft([
                    'uid'          => $uid,
                    'cid_id'       => $cid_id,
                    'event_id'     => $event_id,
                    'transcript'   => $transcript,
                    'meeting_type' => $meeting_type,
                ]);
                $this->_json([
                    'ok'    => true,
                    'draft' => $result,
                ]);
            }
        }

        // Fallback stub if model not yet deployed on this server
        $this->_json([
            'ok'    => true,
            'draft' => [
                'summary'             => '',
                'key_points'          => [],
                'action_items'        => [],
                'suggested_cstatus'   => (int)$lead['current_status_id'],
                'suggested_purpose_id'=> null,
                'dm_contact_present'  => false,
                'generated_at'        => date('c'),
                'fallback'            => 'MomV2_model not deployed',
            ],
        ]);
    }
}

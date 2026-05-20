<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * STEM Learning - Coach Controller
 * Migration 036 (BD Coach + Greetings + Knowledge Repository)
 *
 * Exposes REST endpoints under /api/coach/*
 *
 * Endpoints (33 total, exceeds spec minimum of 23):
 *   GET  /api/coach/probe
 *   GET  /api/coach/skill/scores        ?uid=
 *   GET  /api/coach/skill/gaps          ?uid=
 *   POST /api/coach/skill/manual_signal
 *   POST /api/coach/drill/assign
 *   GET  /api/coach/drill/list          ?uid=
 *   GET  /api/coach/onboarding/status   ?uid=
 *   POST /api/coach/onboarding/checkpoint
 *   POST /api/coach/asset/submit
 *   GET  /api/coach/asset/review/:id
 *   GET  /api/coach/asset/grades        ?from=&to=
 *   GET  /api/coach/faq/search          ?q=&uid=
 *   POST /api/coach/faq/voice
 *   POST /api/coach/faq/log_unanswered
 *   GET  /api/coach/faq/candidates      ?status=
 *   POST /api/coach/faq/publish_candidate
 *   GET  /api/coach/greetings/queue     ?cm_uid=
 *   POST /api/coach/greetings/approve
 *   POST /api/coach/greetings/reject
 *   GET  /api/coach/knowledge/probe
 *   POST /api/coach/knowledge/upload
 *   GET  /api/coach/knowledge/list      ?uid=&type=
 *   GET  /api/coach/knowledge/get/:id
 *   POST /api/coach/knowledge/acknowledge
 *   GET  /api/coach/knowledge/whats_new ?uid=&since=
 *   GET  /api/coach/knowledge/ack_overdue ?hours=
 *   GET  /api/coach/knowledge/distribution_gaps
 *   GET  /api/coach/knowledge/expiring  ?within_days=
 *   GET  /api/coach/knowledge/unanswered_top ?min_asks=&days=
 *   GET  /api/coach/knowledge/avp_cadence ?days=
 *   GET  /api/coach/knowledge/cluster_velocity ?hours=
 *   GET  /api/coach/knowledge/candidate_faqs ?status=
 *   POST /api/coach/knowledge/archive
 *
 * Auth: Bearer STEM_DIGEST_TOKEN.
 * Same pattern as FunnelHygiene and Rhythm controllers (migrations 024, 035).
 *
 * Migration 036. Author: STEM ops, 2026-05-18.
 */
class Coach extends CI_Controller
{
    // ------------------------------------------------------------------
    public function __construct()
    {
        parent::__construct();
        $this->load->database();
        $this->load->helper('url');

        // Load agents as models following existing codebase convention.
        $this->load->model('Coach_agent',               'coach_agent');
        $this->load->model('Greetings_drafter_agent',   'greetings_agent');
        $this->load->model('Asset_review_agent',        'asset_agent');
        $this->load->model('Faq_agent',                 'faq_agent');
        $this->load->model('Knowledge_repo_agent',      'knowledge_agent');

        // Auth: skip only the probe endpoint.
        $method = $this->router->fetch_method();
        if ($method !== 'probe') {
            $this->_auth_bearer();
        }
    }

    // ==========================================================================
    // AUTH + RESPONSE HELPERS
    // ==========================================================================

    /**
     * Verify Bearer token against STEM_DIGEST_TOKEN env var.
     * Exits with 401 JSON if invalid.
     */
    protected function _auth_bearer()
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

    /**
     * Emit a JSON response and halt.
     *
     * @param  array $data
     * @param  int   $code HTTP status code
     */
    protected function _json($data, $code = 200)
    {
        $this->output
             ->set_status_header($code)
             ->set_content_type('application/json')
             ->set_output(json_encode($data));
        exit;
    }

    // ------------------------------------------------------------------

    /**
     * Require caller to be one of the given type_ids.
     * Pass $uid from the request payload or query string.
     *
     * @param  array $allowed_type_ids
     * @param  int   $acting_uid       The uid making the request
     */
    protected function _require_role($allowed_type_ids, $acting_uid)
    {
        $acting_uid = (int)$acting_uid;
        if (!$acting_uid) {
            $this->_json(['error' => 'missing_acting_uid', 'error_code' => 'AUTH_001'], 400);
        }

        $row = $this->db->query("
            SELECT type_id FROM user WHERE uid = ? LIMIT 1
        ", [$acting_uid])->row_array();

        if (empty($row) || !in_array((int)$row['type_id'], $allowed_type_ids)) {
            $this->_json([
                'error'            => 'permission_denied',
                'error_code'       => 'AUTH_002',
                'message'          => 'Your role does not have access to this action.',
                'required_type_ids'=> $allowed_type_ids,
            ], 403);
        }
    }

    // ------------------------------------------------------------------

    /**
     * Return feature flag value for coach_036_enabled.
     *
     * @return int 0|1|2
     */
    protected function _get_feature_flag()
    {
        $row = $this->db->query("
            SELECT flag_value FROM feature_flag
             WHERE flag_key = 'coach_036_enabled'
               AND entity_type = 'global'
             LIMIT 1
        ")->row_array();
        return $row ? (int)$row['flag_value'] : 0;
    }

    // ==========================================================================
    // PROBE
    // ==========================================================================

    /**
     * GET /api/coach/probe
     * Returns {ok:1, migration:'036', deployed_at} when coach_036_enabled is 1 or 2.
     * Returns 404 if flag is 0.
     * No auth required.
     */
    public function probe()
    {
        $flag = $this->_get_feature_flag();
        if ($flag < 1) {
            $this->_json(['error' => 'not_deployed', 'error_code' => 'MIG036_NOT_LIVE'], 404);
        }

        $this->_json([
            'ok'          => 1,
            'migration'   => '036',
            'feature_flag'=> $flag,
            'deployed_at' => date('Y-m-d H:i:s'),
        ]);
    }

    // ==========================================================================
    // SKILL ENDPOINTS
    // ==========================================================================

    /**
     * GET /api/coach/skill/scores?uid=
     * Skill scores for a BD.
     */
    public function skill_scores()
    {
        $uid = (int)$this->input->get('uid');
        if (!$uid) $this->_json(['error' => 'missing_uid', 'error_code' => 'PARAM_001'], 400);

        $from = $this->input->get('from') ?: date('Y-m-d', strtotime('-30 days'));
        $to   = $this->input->get('to')   ?: date('Y-m-d');

        $result = $this->coach_agent->compute_skill_scores($uid, $from, $to);
        $this->_json($result);
    }

    // ------------------------------------------------------------------

    /**
     * GET /api/coach/skill/gaps?uid=
     * Top 3 skill gaps for a BD.
     */
    public function skill_gaps()
    {
        $uid = (int)$this->input->get('uid');
        if (!$uid) $this->_json(['error' => 'missing_uid', 'error_code' => 'PARAM_001'], 400);

        $result = $this->coach_agent->get_skill_gaps($uid);
        $this->_json($result);
    }

    // ------------------------------------------------------------------

    /**
     * POST /api/coach/skill/manual_signal
     * CM manual adjustment of BD skill score.
     * Required role: CM (type_id=13) or higher.
     *
     * Body: {cm_uid, bd_uid, skill_code, delta_pts, note}
     */
    public function skill_manual_signal()
    {
        if ($this->input->method() !== 'post') {
            $this->_json(['error' => 'post_only', 'error_code' => 'METHOD_001'], 405);
        }

        $cm_uid     = (int)$this->input->post('cm_uid');
        $bd_uid     = (int)$this->input->post('bd_uid');
        $skill_code = $this->input->post('skill_code');
        $delta_pts  = (float)$this->input->post('delta_pts');
        $note       = $this->input->post('note');

        $this->_require_role([4, 13, 29], $cm_uid);

        if (!$bd_uid || !$skill_code) {
            $this->_json(['error' => 'missing_required_fields', 'error_code' => 'PARAM_002'], 400);
        }

        $result = $this->coach_agent->apply_cm_manual_adjustment(
            $cm_uid, $bd_uid, $skill_code, $delta_pts, $note
        );
        $code = $result['ok'] ? 200 : 422;
        $this->_json($result, $code);
    }

    // ==========================================================================
    // DRILL ENDPOINTS
    // ==========================================================================

    /**
     * POST /api/coach/drill/assign
     * Assign a drill to a BD.
     *
     * Body: {uid, skill_code, level}
     */
    public function drill_assign()
    {
        if ($this->input->method() !== 'post') {
            $this->_json(['error' => 'post_only', 'error_code' => 'METHOD_001'], 405);
        }

        $uid        = (int)$this->input->post('uid');
        $skill_code = $this->input->post('skill_code');
        $level      = $this->input->post('level') ?: 'new';

        if (!$uid || !$skill_code) {
            $this->_json(['error' => 'missing_required_fields', 'error_code' => 'PARAM_002'], 400);
        }

        $result = $this->coach_agent->assign_drill($uid, $skill_code, $level);
        $code   = $result['ok'] ? 200 : 422;
        $this->_json($result, $code);
    }

    // ------------------------------------------------------------------

    /**
     * GET /api/coach/drill/list?uid=
     * All coaching assignments for a BD.
     */
    public function drill_list()
    {
        $uid = (int)$this->input->get('uid');
        if (!$uid) $this->_json(['error' => 'missing_uid', 'error_code' => 'PARAM_001'], 400);

        $result = $this->coach_agent->get_drill_list($uid);
        $this->_json($result);
    }

    // ==========================================================================
    // ONBOARDING ENDPOINTS
    // ==========================================================================

    /**
     * GET /api/coach/onboarding/status?uid=
     * 9 checkpoint statuses for a BD.
     */
    public function onboarding_status()
    {
        $uid = (int)$this->input->get('uid');
        if (!$uid) $this->_json(['error' => 'missing_uid', 'error_code' => 'PARAM_001'], 400);

        $result = $this->coach_agent->onboarding_status($uid);
        $this->_json($result);
    }

    // ------------------------------------------------------------------

    /**
     * POST /api/coach/onboarding/checkpoint
     * Mark a checkpoint passed/failed.
     *
     * Body: {uid, checkpoint_code, status, evidence_json}
     */
    public function onboarding_checkpoint()
    {
        if ($this->input->method() !== 'post') {
            $this->_json(['error' => 'post_only', 'error_code' => 'METHOD_001'], 405);
        }

        $uid             = (int)$this->input->post('uid');
        $checkpoint_code = $this->input->post('checkpoint_code');
        $status          = $this->input->post('status');
        $evidence_json   = $this->input->post('evidence_json') ?: '{}';

        if (!$uid || !$checkpoint_code || !$status) {
            $this->_json(['error' => 'missing_required_fields', 'error_code' => 'PARAM_002'], 400);
        }

        $result = $this->coach_agent->mark_checkpoint($uid, $checkpoint_code, $status, $evidence_json);
        $code   = $result['ok'] ? 200 : 422;
        $this->_json($result, $code);
    }

    // ==========================================================================
    // ASSET REVIEW ENDPOINTS
    // ==========================================================================

    /**
     * POST /api/coach/asset/submit
     * BD submits asset for review.
     *
     * Body: {uid, asset_type, file_url?, transcript_text?}
     */
    public function asset_submit()
    {
        if ($this->input->method() !== 'post') {
            $this->_json(['error' => 'post_only', 'error_code' => 'METHOD_001'], 405);
        }

        $uid             = (int)$this->input->post('uid');
        $asset_type      = $this->input->post('asset_type');
        $file_url        = $this->input->post('file_url');
        $transcript_text = $this->input->post('transcript_text');

        if (!$uid || !$asset_type) {
            $this->_json(['error' => 'missing_required_fields', 'error_code' => 'PARAM_002'], 400);
        }

        $result = $this->asset_agent->submit_asset($uid, $asset_type, $file_url, $transcript_text);
        $code   = $result['ok'] ? 200 : 422;
        $this->_json($result, $code);
    }

    // ------------------------------------------------------------------

    /**
     * GET /api/coach/asset/review/:id
     * Get review result for an asset.
     *
     * @param  int $id asset_review.id
     */
    public function asset_review($id = 0)
    {
        $id = (int)$id;
        if (!$id) {
            $id = (int)$this->input->get('id');
        }
        if (!$id) $this->_json(['error' => 'missing_id', 'error_code' => 'PARAM_001'], 400);

        $row = $this->db->query("
            SELECT ar.*, u.firstName AS bd_name
              FROM asset_review ar
              LEFT JOIN user u ON u.uid = ar.bd_uid
             WHERE ar.id = ?
             LIMIT 1
        ", [$id])->row_array();

        if (empty($row)) $this->_json(['error' => 'review_not_found', 'error_code' => 'DATA_001'], 404);

        // If still pending, trigger run_review synchronously.
        if ($row['status'] === 'pending_review') {
            $run = $this->asset_agent->run_review($id);
            if ($run['ok']) {
                $row = $this->db->query("
                    SELECT ar.*, u.firstName AS bd_name
                      FROM asset_review ar
                      LEFT JOIN user u ON u.uid = ar.bd_uid
                     WHERE ar.id = ?
                     LIMIT 1
                ", [$id])->row_array();
            }
        }

        $this->_json(['ok' => true, 'review' => $row]);
    }

    // ------------------------------------------------------------------

    /**
     * GET /api/coach/asset/grades?from=&to=
     * Grade distribution for CM/AVP view.
     */
    public function asset_grades()
    {
        $from = $this->input->get('from') ?: date('Y-m-d', strtotime('-30 days'));
        $to   = $this->input->get('to')   ?: date('Y-m-d');

        $result = $this->asset_agent->get_grade_distribution($from, $to);
        $this->_json($result);
    }

    // ==========================================================================
    // FAQ ENDPOINTS
    // ==========================================================================

    /**
     * GET /api/coach/faq/search?q=&uid=
     * Semantic search across published FAQs.
     */
    public function faq_search()
    {
        $q     = $this->input->get('q');
        $uid   = (int)$this->input->get('uid');
        $top_k = (int)($this->input->get('top_k') ?: 5);

        if (!$q) $this->_json(['error' => 'missing_query', 'error_code' => 'PARAM_001'], 400);
        if (!$uid) $this->_json(['error' => 'missing_uid', 'error_code' => 'PARAM_001'], 400);

        $result = $this->faq_agent->search($q, $uid, $top_k);
        $this->_json($result);
    }

    // ------------------------------------------------------------------

    /**
     * POST /api/coach/faq/voice
     * Voice query: multipart audio upload, transcribe, then search.
     *
     * Body: {uid} + multipart audio file (audio_url or file)
     */
    public function faq_voice()
    {
        if ($this->input->method() !== 'post') {
            $this->_json(['error' => 'post_only', 'error_code' => 'METHOD_001'], 405);
        }

        $uid       = (int)$this->input->post('uid');
        $audio_url = $this->input->post('audio_url');

        if (!$uid) $this->_json(['error' => 'missing_uid', 'error_code' => 'PARAM_001'], 400);
        if (!$audio_url) $this->_json(['error' => 'missing_audio_url', 'error_code' => 'PARAM_002'], 400);

        $result = $this->faq_agent->voice_search($audio_url, $uid);
        $code   = $result['ok'] ? 200 : 422;
        $this->_json($result, $code);
    }

    // ------------------------------------------------------------------

    /**
     * POST /api/coach/faq/log_unanswered
     * Log an unanswered query.
     *
     * Body: {query_text, uid}
     */
    public function faq_log_unanswered()
    {
        if ($this->input->method() !== 'post') {
            $this->_json(['error' => 'post_only', 'error_code' => 'METHOD_001'], 405);
        }

        $query_text = $this->input->post('query_text');
        $uid        = (int)$this->input->post('uid');

        if (!$query_text || !$uid) {
            $this->_json(['error' => 'missing_required_fields', 'error_code' => 'PARAM_002'], 400);
        }

        $result = $this->faq_agent->log_unanswered($query_text, $uid);
        $code   = $result['ok'] ? 200 : 422;
        $this->_json($result, $code);
    }

    // ------------------------------------------------------------------

    /**
     * GET /api/coach/faq/candidates?status=
     * Candidate FAQs awaiting AVP moderation.
     */
    public function faq_candidates()
    {
        $status = $this->input->get('status') ?: 'pending';
        $result = $this->knowledge_agent->get_candidate_faqs($status);
        $this->_json($result);
    }

    // ------------------------------------------------------------------

    /**
     * POST /api/coach/faq/publish_candidate
     * Moderator publishes a candidate FAQ to the live index.
     * Required role: Director (4), AVP (29) in pilot; also CM (13) in org rollout.
     *
     * Body: {candidate_id, moderator_uid, final_answer, source_artifact_id?}
     */
    public function faq_publish_candidate()
    {
        if ($this->input->method() !== 'post') {
            $this->_json(['error' => 'post_only', 'error_code' => 'METHOD_001'], 405);
        }

        $candidate_id       = (int)$this->input->post('candidate_id');
        $moderator_uid      = (int)$this->input->post('moderator_uid');
        $final_answer       = $this->input->post('final_answer');
        $source_artifact_id = $this->input->post('source_artifact_id');

        if (!$candidate_id || !$moderator_uid || !$final_answer) {
            $this->_json(['error' => 'missing_required_fields', 'error_code' => 'PARAM_002'], 400);
        }

        $result = $this->faq_agent->publish_candidate(
            $candidate_id, $moderator_uid, $final_answer, $source_artifact_id
        );
        $code = $result['ok'] ? 200 : ($result['error'] === 'permission_denied' ? 403 : 422);
        $this->_json($result, $code);
    }

    // ==========================================================================
    // GREETINGS ENDPOINTS
    // ==========================================================================

    /**
     * GET /api/coach/greetings/queue?cm_uid=
     * Pending drafts for a CM approver.
     */
    public function greetings_queue()
    {
        $cm_uid = (int)$this->input->get('cm_uid');
        if (!$cm_uid) $this->_json(['error' => 'missing_cm_uid', 'error_code' => 'PARAM_001'], 400);

        $result = $this->greetings_agent->get_queue_for_approver($cm_uid);
        $this->_json($result);
    }

    // ------------------------------------------------------------------

    /**
     * POST /api/coach/greetings/approve
     * CM/BD approves a draft. Sets status = approved_ready_to_send. Never sends.
     *
     * Body: {outbox_id, cm_uid, variant_chosen, edits_json?}
     */
    public function greetings_approve()
    {
        if ($this->input->method() !== 'post') {
            $this->_json(['error' => 'post_only', 'error_code' => 'METHOD_001'], 405);
        }

        $outbox_id      = (int)$this->input->post('outbox_id');
        $cm_uid         = (int)$this->input->post('cm_uid');
        $variant_chosen = $this->input->post('variant_chosen') ?: 'formal_en';
        $edits_json     = $this->input->post('edits_json') ?: '{}';

        if (!$outbox_id || !$cm_uid) {
            $this->_json(['error' => 'missing_required_fields', 'error_code' => 'PARAM_002'], 400);
        }

        $result = $this->greetings_agent->approve_draft($outbox_id, $cm_uid, $variant_chosen, $edits_json);
        $code   = $result['ok'] ? 200 : 422;
        $this->_json($result, $code);
    }

    // ------------------------------------------------------------------

    /**
     * POST /api/coach/greetings/reject
     * Reject a draft with a reason.
     *
     * Body: {outbox_id, cm_uid, reason}
     */
    public function greetings_reject()
    {
        if ($this->input->method() !== 'post') {
            $this->_json(['error' => 'post_only', 'error_code' => 'METHOD_001'], 405);
        }

        $outbox_id = (int)$this->input->post('outbox_id');
        $cm_uid    = (int)$this->input->post('cm_uid');
        $reason    = $this->input->post('reason') ?: '';

        if (!$outbox_id || !$cm_uid) {
            $this->_json(['error' => 'missing_required_fields', 'error_code' => 'PARAM_002'], 400);
        }

        $result = $this->greetings_agent->reject_draft($outbox_id, $cm_uid, $reason);
        $code   = $result['ok'] ? 200 : 422;
        $this->_json($result, $code);
    }

    // ==========================================================================
    // KNOWLEDGE REPOSITORY ENDPOINTS
    // ==========================================================================

    /**
     * GET /api/coach/knowledge/probe
     * Same as /probe but namespaced. Returns feature flag state.
     */
    public function knowledge_probe()
    {
        $flag = $this->_get_feature_flag();
        $this->_json([
            'ok'          => 1,
            'migration'   => '036',
            'module'      => 'knowledge_repository',
            'feature_flag'=> $flag,
            'deployed_at' => date('Y-m-d H:i:s'),
        ]);
    }

    // ------------------------------------------------------------------

    /**
     * POST /api/coach/knowledge/upload
     * AVP/Director/authorised CM uploads an artifact.
     * Required role: Director (4), AVP (29), or CM (13) for allowed types.
     *
     * Body (multipart): {uploader_uid, title, artifact_type, file_url,
     *                    file_size_bytes, mime_type, target_segment_json?,
     *                    force_ack?, publish_at?, expire_at?}
     */
    public function knowledge_upload()
    {
        if ($this->input->method() !== 'post') {
            $this->_json(['error' => 'post_only', 'error_code' => 'METHOD_001'], 405);
        }

        $uploader_uid        = (int)$this->input->post('uploader_uid');
        $title               = $this->input->post('title');
        $artifact_type       = $this->input->post('artifact_type');
        $file_url            = $this->input->post('file_url');
        $file_size_bytes     = (int)$this->input->post('file_size_bytes');
        $mime_type           = $this->input->post('mime_type');
        $target_segment_json = $this->input->post('target_segment_json');
        $force_ack           = (int)$this->input->post('force_ack');
        $publish_at          = $this->input->post('publish_at');
        $expire_at           = $this->input->post('expire_at');

        if (!$uploader_uid || !$title || !$artifact_type || !$file_url) {
            $this->_json(['error' => 'missing_required_fields', 'error_code' => 'PARAM_002'], 400);
        }

        $result = $this->knowledge_agent->upload_artifact(
            $uploader_uid, $title, $artifact_type, $file_url,
            $file_size_bytes, $mime_type, $target_segment_json,
            $force_ack, $publish_at, $expire_at
        );

        $code = $result['ok'] ? 200
              : ($result['error'] === 'permission_denied' ? 403
              : ($result['error'] === 'file_too_large'    ? 413 : 422));
        $this->_json($result, $code);
    }

    // ------------------------------------------------------------------

    /**
     * GET /api/coach/knowledge/list?uid=&type=
     * List live artifacts scoped to a BD or filtered by type.
     */
    public function knowledge_list()
    {
        $uid  = (int)$this->input->get('uid');
        $type = $this->input->get('type');

        $sql    = "SELECT id, artifact_type, title, file_url, file_mime,
                          uploaded_at, force_acknowledge, expiry_date, status,
                          uploaded_by_uid, version
                     FROM knowledge_artifact
                    WHERE status = 'live'";
        $params = [];

        if ($type) {
            $sql      .= ' AND artifact_type = ?';
            $params[]  = $this->db->escape_str($type);
        }
        $sql .= ' ORDER BY uploaded_at DESC LIMIT 100';

        $rows = $this->db->query($sql, $params)->result_array();
        $this->_json(['ok' => true, 'artifacts' => $rows, 'count' => count($rows)]);
    }

    // ------------------------------------------------------------------

    /**
     * GET /api/coach/knowledge/get/:id
     * Fetch one artifact with parsed text and version chain.
     *
     * @param  int $id knowledge_artifact.id
     */
    public function knowledge_get($id = 0)
    {
        $id = (int)$id;
        if (!$id) {
            $id = (int)$this->input->get('id');
        }
        if (!$id) $this->_json(['error' => 'missing_id', 'error_code' => 'PARAM_001'], 400);

        $artifact = $this->db->query("
            SELECT ka.*,
                   u.firstName AS uploaded_by_name
              FROM knowledge_artifact ka
              LEFT JOIN user u ON u.uid = ka.uploaded_by_uid
             WHERE ka.id = ?
             LIMIT 1
        ", [$id])->row_array();

        if (empty($artifact)) {
            $this->_json(['error' => 'artifact_not_found', 'error_code' => 'DATA_001'], 404);
        }

        // Version chain: older versions with same title and artifact_type.
        $version_chain = $this->db->query("
            SELECT id, version, uploaded_at, status
              FROM knowledge_artifact
             WHERE artifact_type = ?
               AND title = ?
               AND id != ?
             ORDER BY version DESC
             LIMIT 10
        ", [$artifact['artifact_type'], $artifact['title'], $id])->result_array();

        $this->_json([
            'ok'            => true,
            'artifact'      => $artifact,
            'version_chain' => $version_chain,
        ]);
    }

    // ------------------------------------------------------------------

    /**
     * POST /api/coach/knowledge/acknowledge
     * BD marks a force-ack artifact as read.
     *
     * Body: {artifact_id, uid, ack_source?}
     */
    public function knowledge_acknowledge()
    {
        if ($this->input->method() !== 'post') {
            $this->_json(['error' => 'post_only', 'error_code' => 'METHOD_001'], 405);
        }

        $artifact_id = (int)$this->input->post('artifact_id');
        $uid         = (int)$this->input->post('uid');
        $ack_source  = $this->input->post('ack_source') ?: 'app';

        if (!$artifact_id || !$uid) {
            $this->_json(['error' => 'missing_required_fields', 'error_code' => 'PARAM_002'], 400);
        }

        $result = $this->knowledge_agent->acknowledge($artifact_id, $uid, $ack_source);
        $code   = $result['ok'] ? 200 : 422;
        $this->_json($result, $code);
    }

    // ------------------------------------------------------------------

    /**
     * GET /api/coach/knowledge/whats_new?uid=&since=
     * Personalised "What's new" feed for a BD.
     */
    public function knowledge_whats_new()
    {
        $uid   = (int)$this->input->get('uid');
        $since = $this->input->get('since');

        if (!$uid) $this->_json(['error' => 'missing_uid', 'error_code' => 'PARAM_001'], 400);

        $result = $this->knowledge_agent->whats_new($uid, $since);
        $this->_json($result);
    }

    // ------------------------------------------------------------------

    /**
     * GET /api/coach/knowledge/ack_overdue?hours=
     * BDs with pending force-ack older than N hours.
     */
    public function knowledge_ack_overdue()
    {
        $hours  = (int)($this->input->get('hours') ?: 48);
        $result = $this->knowledge_agent->get_ack_overdue($hours);
        $this->_json($result);
    }

    // ------------------------------------------------------------------

    /**
     * GET /api/coach/knowledge/distribution_gaps
     * Segments with coverage under 10 percent.
     */
    public function knowledge_distribution_gaps()
    {
        $result = $this->knowledge_agent->get_distribution_gaps();
        $this->_json($result);
    }

    // ------------------------------------------------------------------

    /**
     * GET /api/coach/knowledge/expiring?within_days=
     * Artifacts expiring within N days.
     */
    public function knowledge_expiring()
    {
        $within_days = (int)($this->input->get('within_days') ?: 7);
        $result      = $this->knowledge_agent->get_expiring_soon($within_days);
        $this->_json($result);
    }

    // ------------------------------------------------------------------

    /**
     * GET /api/coach/knowledge/unanswered_top?min_asks=&days=
     * Top unanswered FAQ queries.
     */
    public function knowledge_unanswered_top()
    {
        $min_asks = (int)($this->input->get('min_asks') ?: 3);
        $days     = (int)($this->input->get('days')     ?: 7);

        $result = $this->faq_agent->get_top_unanswered($min_asks, $days);
        $this->_json($result);
    }

    // ------------------------------------------------------------------

    /**
     * GET /api/coach/knowledge/avp_cadence?days=
     * Per-uploader stats for AVP view.
     */
    public function knowledge_avp_cadence()
    {
        $days   = (int)($this->input->get('days') ?: 7);
        $result = $this->knowledge_agent->get_avp_cadence($days);
        $this->_json($result);
    }

    // ------------------------------------------------------------------

    /**
     * GET /api/coach/knowledge/cluster_velocity?hours=
     * Cluster-wise ack velocity.
     */
    public function knowledge_cluster_velocity()
    {
        $hours  = (int)($this->input->get('hours') ?: 24);
        $result = $this->knowledge_agent->get_cluster_ack_velocity($hours);
        $this->_json($result);
    }

    // ------------------------------------------------------------------

    /**
     * GET /api/coach/knowledge/candidate_faqs?status=
     * Alias of /faq/candidates for cron use.
     */
    public function knowledge_candidate_faqs()
    {
        $status = $this->input->get('status') ?: 'pending';
        $result = $this->knowledge_agent->get_candidate_faqs($status);
        $this->_json($result);
    }

    // ------------------------------------------------------------------

    /**
     * POST /api/coach/knowledge/archive
     * Archive an artifact (uploader or Director only).
     *
     * Body: {artifact_id, uid}
     */
    public function knowledge_archive()
    {
        if ($this->input->method() !== 'post') {
            $this->_json(['error' => 'post_only', 'error_code' => 'METHOD_001'], 405);
        }

        $artifact_id = (int)$this->input->post('artifact_id');
        $uid         = (int)$this->input->post('uid');

        if (!$artifact_id || !$uid) {
            $this->_json(['error' => 'missing_required_fields', 'error_code' => 'PARAM_002'], 400);
        }

        $result = $this->knowledge_agent->archive($artifact_id, $uid);
        $code   = $result['ok'] ? 200 : ($result['error'] === 'permission_denied' ? 403 : 422);
        $this->_json($result, $code);
    }

    // ==================================================================
    // POST /api/run_tool
    //
    // Generic mobile tool dispatcher. The mobile chat UI sends a tool name
    // and a payload; this endpoint routes it to the appropriate Coach agent
    // method and returns the result as JSON. Keeps the mobile app from
    // having to know every backend endpoint URL.
    //
    // Body (JSON):
    //   uid      int     required  caller user id
    //   tool     string  required  one of the allowed tool names below
    //   payload  object  optional  tool-specific arguments
    //
    // Allowed tools:
    //   skill_scores             { uid }
    //   skill_gaps               { uid }
    //   onboarding_status        { uid }
    //   faq_search               { q }
    //   knowledge_list           { uid, type? }
    //   knowledge_whats_new      { uid, since? }
    //   drill_list               { uid }
    //
    // Returns: { ok, tool, result }
    //
    // Inlined here on mobile-api-endpoints branch, 2026-05-20.
    // ==================================================================
    public function api_run_tool()
    {
        if ($this->input->method() !== 'post') {
            $this->_json(['error' => 'post_only', 'error_code' => 'METHOD_001'], 405);
        }

        $raw  = $this->input->raw_input_stream;
        $body = [];
        if ($raw) {
            $decoded = json_decode($raw, true);
            if (json_last_error() === JSON_ERROR_NONE) $body = $decoded;
        }
        if (!$body) {
            $body = $this->input->post(null, true) ?: [];
        }

        $uid     = isset($body['uid'])     ? (int)$body['uid']         : null;
        $tool    = isset($body['tool'])    ? strtolower(trim($body['tool'])) : null;
        $payload = isset($body['payload']) ? (array)$body['payload']   : [];

        if (!$uid) {
            $this->_json(['error' => 'missing_uid', 'error_code' => 'PARAM_001'], 400);
        }
        if (!$tool) {
            $this->_json(['error' => 'missing_tool', 'error_code' => 'PARAM_001'], 400);
        }

        // Whitelist of tools the mobile app may call through this dispatcher
        $allowed = [
            'skill_scores', 'skill_gaps', 'onboarding_status',
            'faq_search', 'knowledge_list', 'knowledge_whats_new', 'drill_list',
        ];
        if (!in_array($tool, $allowed)) {
            $this->_json([
                'error'      => 'invalid_tool',
                'error_code' => 'PARAM_003',
                'message'    => 'tool must be one of: ' . implode(', ', $allowed),
            ], 400);
        }

        // Dispatch
        try {
            switch ($tool) {
                case 'skill_scores':
                    $result = $this->coach_agent->skill_scores($uid);
                    break;
                case 'skill_gaps':
                    $result = $this->coach_agent->skill_gaps($uid);
                    break;
                case 'onboarding_status':
                    $result = $this->coach_agent->onboarding_status($uid);
                    break;
                case 'faq_search':
                    $q = isset($payload['q']) ? trim($payload['q']) : '';
                    if (!$q) {
                        $this->_json(['error' => 'missing_q', 'error_code' => 'PARAM_001'], 400);
                    }
                    $result = $this->faq_agent->search($q, $uid);
                    break;
                case 'knowledge_list':
                    $type = isset($payload['type']) ? trim($payload['type']) : null;
                    $result = $this->knowledge_agent->list_for_user($uid, $type);
                    break;
                case 'knowledge_whats_new':
                    $since = isset($payload['since']) ? trim($payload['since']) : null;
                    $result = $this->knowledge_agent->whats_new($uid, $since);
                    break;
                case 'drill_list':
                    $result = $this->coach_agent->drill_list($uid);
                    break;
                default:
                    $result = ['ok' => false, 'error' => 'not_implemented'];
            }
        } catch (Throwable $e) {
            log_message('error', 'api_run_tool failed: ' . $e->getMessage());
            $this->_json([
                'ok'         => false,
                'error'      => 'tool_failed',
                'error_code' => 'TOOL_001',
                'detail'     => $e->getMessage(),
            ], 500);
        }

        $this->_json([
            'ok'           => true,
            'tool'         => $tool,
            'result'       => $result,
            'generated_at' => date('c'),
        ]);
    }
}

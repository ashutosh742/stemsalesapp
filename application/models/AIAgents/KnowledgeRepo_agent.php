<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * STEM Learning - Knowledge Repository Agent
 * Migration 036 (BD Coach + Greetings + Knowledge Repository)
 *
 * Responsibilities:
 *  1. Upload artifacts with permission checks, file size cap, and mime whitelist
 *  2. Distribute artifacts via sp_distribute_knowledge_artifact
 *  3. Acknowledge force-ack artifacts (idempotent)
 *  4. Personalised "What's new" feed for BDs
 *  5. Overdue ack detection, distribution gap reporting, expiry sweep
 *  6. Candidate FAQ retrieval and artifact archiving
 *  7. AVP cadence stats and cluster ack velocity
 *
 * Permissions:
 *  - type_id=4 (Director): all artifact types, global scope
 *  - type_id=29 (AVP): all artifact types, global scope
 *  - type_id=13 (CM): competitor_battlecard, case_study, regional_content for own cluster
 *  - Senior BDs (org rollout, by Director invite): case_study from own Won deals, AVP-moderated
 *
 * File cap: 25 MB
 * Mime whitelist: pdf, docx, pptx, xlsx, png, jpg, mp4, txt, md
 * MP4 duration limit: 300 seconds (5 minutes) via ffprobe placeholder
 *
 * LLM: Claude Sonnet 4.6 for candidate FAQ generation via $this->llm->call() placeholder.
 *
 * Migration 036. Author: STEM ops, 2026-05-18.
 */
class Knowledge_repo_agent extends CI_Model
{
    // Allowed artifact types.
    const ALLOWED_ARTIFACT_TYPES = [
        'product_brochure', 'pricing_update', 'marketing_campaign',
        'competitor_battlecard', 'policy_update', 'case_study',
        'training_video', 'govt_scheme_circular', 'regional_content', 'internal_memo',
    ];

    // Artifact types CMs can upload (own cluster only).
    const CM_ALLOWED_TYPES = ['competitor_battlecard', 'case_study', 'regional_content'];

    // Mime whitelist keyed by extension.
    const ALLOWED_MIMES = [
        'pdf'  => 'application/pdf',
        'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
        'pptx' => 'application/vnd.openxmlformats-officedocument.presentationml.presentation',
        'xlsx' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        'png'  => 'image/png',
        'jpg'  => 'image/jpeg',
        'mp4'  => 'video/mp4',
        'txt'  => 'text/plain',
        'md'   => 'text/markdown',
    ];

    // File size cap in bytes (25 MB).
    const MAX_FILE_SIZE_BYTES = 26214400;

    // MP4 max duration in seconds.
    const MAX_MP4_DURATION_SEC = 300;

    // Claude model for candidate FAQ generation.
    const LLM_MODEL = 'claude-sonnet-4-6';

    // Candidate FAQs to generate per artifact.
    const CANDIDATE_FAQ_COUNT = 8;

    // Hours before force-ack is considered overdue.
    const ACK_OVERDUE_HOURS = 48;

    // Coverage pct below which a segment is flagged.
    const COVERAGE_GAP_PCT = 10.0;

    public function __construct()
    {
        parent::__construct();
        $this->load->database();
    }

    // ==========================================================================
    // UPLOAD
    // ==========================================================================

    /**
     * Upload and publish a knowledge artifact.
     *
     * @param  int    $uploader_uid
     * @param  string $title
     * @param  string $artifact_type
     * @param  string $file_url
     * @param  int    $file_size_bytes
     * @param  string $mime_type
     * @param  string $target_segment_json  JSON array of cluster ids or bd_uids
     * @param  int    $force_ack           0 or 1
     * @param  string $publish_at          Y-m-d H:i:s or null (defaults to now)
     * @param  string $expire_at           Y-m-d H:i:s or null
     * @return array  ['ok', 'artifact_id', 'candidate_faqs_queued']
     */
    public function upload_artifact(
        $uploader_uid,
        $title,
        $artifact_type,
        $file_url,
        $file_size_bytes,
        $mime_type,
        $target_segment_json,
        $force_ack,
        $publish_at,
        $expire_at
    ) {
        $uploader_uid     = (int)$uploader_uid;
        $title            = substr((string)$title, 0, 300);
        $artifact_type    = $this->db->escape_str($artifact_type);
        $file_url         = $this->db->escape_str($file_url);
        $file_size_bytes  = (int)$file_size_bytes;
        $mime_type        = $this->db->escape_str($mime_type);
        $target_segment   = $target_segment_json ?: 'null';
        $force_ack        = $force_ack ? 1 : 0;
        $publish_at       = $publish_at ?: date('Y-m-d H:i:s');
        $expire_at        = $expire_at ?: null;

        // --- Permission check ---
        $perm = $this->_check_upload_permission($uploader_uid, $artifact_type);
        if (!$perm['allowed']) {
            return ['ok' => false, 'error' => 'permission_denied', 'detail' => $perm['reason']];
        }

        // --- File size cap ---
        if ($file_size_bytes > self::MAX_FILE_SIZE_BYTES) {
            return [
                'ok'    => false,
                'error' => 'file_too_large',
                'max_mb'=> self::MAX_FILE_SIZE_BYTES / 1048576,
                'got_mb'=> round($file_size_bytes / 1048576, 2),
            ];
        }

        // --- Mime whitelist ---
        $ext = strtolower(pathinfo($file_url, PATHINFO_EXTENSION));
        if (!array_key_exists($ext, self::ALLOWED_MIMES)) {
            return ['ok' => false, 'error' => 'mime_type_not_allowed', 'extension' => $ext];
        }

        // --- MP4 duration check (ffprobe placeholder) ---
        if ($ext === 'mp4') {
            $duration = $this->ffprobe->get_duration($file_url);
            if ($duration !== null && (int)$duration > self::MAX_MP4_DURATION_SEC) {
                return [
                    'ok'              => false,
                    'error'           => 'mp4_too_long',
                    'max_seconds'     => self::MAX_MP4_DURATION_SEC,
                    'detected_seconds'=> (int)$duration,
                    'note'            => 'For videos over 5 minutes, use an external link (YouTube unlisted or Drive)',
                ];
            }
        }

        // --- Scope enforcement for CM: target must be own cluster ---
        $cluster_id = null;
        if ($perm['scope'] === 'cluster') {
            $cluster_id = $this->_get_cluster_for_user($uploader_uid);
        }

        $this->db->trans_start();

        $this->db->query("
            INSERT INTO knowledge_artifact
                (artifact_type, title, file_url, file_mime, version,
                 uploaded_by_uid, uploaded_at, expiry_date, force_acknowledge,
                 target_clusters, status)
            VALUES (?, ?, ?, ?, 1, ?, NOW(), ?, ?, ?, 'live')
        ", [
            $artifact_type, $title, $file_url, $mime_type,
            $uploader_uid, $expire_at, $force_ack,
            $cluster_id ? json_encode([$cluster_id]) : $target_segment,
        ]);
        $artifact_id = $this->db->insert_id();

        $this->db->trans_complete();

        if ($this->db->trans_status() === false) {
            return ['ok' => false, 'error' => 'db_transaction_failed'];
        }

        // Distribute via stored procedure.
        $this->db->query('CALL sp_distribute_knowledge_artifact(?)', [$artifact_id]);

        // Generate candidate FAQs asynchronously (synchronous here for simplicity).
        $faq_count = $this->generate_candidate_faqs($artifact_id, self::CANDIDATE_FAQ_COUNT);

        return [
            'ok'                    => true,
            'artifact_id'           => $artifact_id,
            'candidate_faqs_queued' => $faq_count,
            'distributed'           => true,
        ];
    }

    // ==========================================================================
    // ACKNOWLEDGE
    // ==========================================================================

    /**
     * BD marks a force-ack artifact as read. Idempotent.
     *
     * @param  int    $artifact_id
     * @param  int    $uid         BD user id
     * @param  string $ack_source  app|push|email
     * @return array  ['ok', 'already_acked']
     */
    public function acknowledge($artifact_id, $uid, $ack_source)
    {
        $artifact_id = (int)$artifact_id;
        $uid         = (int)$uid;
        $ack_source  = in_array($ack_source, ['app', 'push', 'email']) ? $ack_source : 'app';

        if (!$artifact_id || !$uid) {
            return ['ok' => false, 'error' => 'missing_required_fields'];
        }

        $this->db->trans_start();

        // INSERT IGNORE on UNIQUE(artifact_id, uid) - idempotent.
        $this->db->query("
            INSERT IGNORE INTO knowledge_acknowledgement
                (artifact_id, bd_uid, acknowledged_at, status)
            VALUES (?, ?, NOW(), 'acknowledged')
        ", [$artifact_id, $uid]);
        $inserted = $this->db->affected_rows();

        // Update status if row already existed but was pending.
        if (!$inserted) {
            $this->db->query("
                UPDATE knowledge_acknowledgement
                   SET acknowledged_at = COALESCE(acknowledged_at, NOW()),
                       status = 'acknowledged'
                 WHERE artifact_id = ? AND bd_uid = ?
                   AND status != 'acknowledged'
            ", [$artifact_id, $uid]);
        }

        $this->db->trans_complete();

        return [
            'ok'          => true,
            'artifact_id' => $artifact_id,
            'uid'         => $uid,
            'already_acked' => ($inserted === 0),
        ];
    }

    // ==========================================================================
    // WHAT'S NEW FEED
    // ==========================================================================

    /**
     * Return artifacts published after $since for the BD's segments.
     *
     * @param  int    $uid
     * @param  string $since  Y-m-d H:i:s or null (defaults to 14 days ago)
     * @return array  ['ok', 'artifacts']
     */
    public function whats_new($uid, $since = null)
    {
        $uid   = (int)$uid;
        $since = $since ?: date('Y-m-d H:i:s', strtotime('-14 days'));

        if (!$uid) return ['ok' => false, 'error' => 'missing_uid'];

        $rows = $this->db->query("
            SELECT ka.id, ka.artifact_type, ka.title, ka.file_url, ka.file_mime,
                   ka.uploaded_at, ka.force_acknowledge, ka.expiry_date,
                   ka.status,
                   CASE WHEN ack.acknowledged_at IS NOT NULL THEN 1 ELSE 0 END AS is_acked,
                   ack.acknowledged_at
              FROM knowledge_artifact ka
              LEFT JOIN knowledge_acknowledgement ack
                     ON ack.artifact_id = ka.id AND ack.bd_uid = ?
             WHERE ka.status = 'live'
               AND ka.uploaded_at >= ?
             ORDER BY ka.uploaded_at DESC
             LIMIT 50
        ", [$uid, $since])->result_array();

        return ['ok' => true, 'uid' => $uid, 'since' => $since, 'artifacts' => $rows];
    }

    // ==========================================================================
    // OVERDUE ACK
    // ==========================================================================

    /**
     * Return BDs with pending force-ack older than N hours.
     *
     * @param  int $hours
     * @return array
     */
    public function get_ack_overdue($hours = 48)
    {
        $hours = max(1, (int)$hours);

        $rows = $this->db->query("
            SELECT v.*
              FROM v_knowledge_ack_overdue v
             WHERE v.hours_pending >= ?
             ORDER BY v.hours_pending DESC
        ", [$hours])->result_array();

        return ['ok' => true, 'hours' => $hours, 'overdue' => $rows, 'count' => count($rows)];
    }

    // ==========================================================================
    // DISTRIBUTION GAPS
    // ==========================================================================

    /**
     * Cross-reference distribution vs acknowledgements.
     * Flag segments with coverage under 10 percent.
     *
     * @return array ['ok', 'gaps']
     */
    public function get_distribution_gaps()
    {
        $rows = $this->db->query("
            SELECT kd.artifact_id, kd.channel,
                   ka.title,
                   kd.target_count,
                   COUNT(ack.id) AS ack_count,
                   CASE WHEN kd.target_count > 0
                        THEN ROUND(COUNT(ack.id) / kd.target_count * 100, 1)
                        ELSE 0 END AS coverage_pct
              FROM knowledge_distribution kd
              INNER JOIN knowledge_artifact ka ON ka.id = kd.artifact_id
                     AND ka.status = 'live'
              LEFT JOIN knowledge_acknowledgement ack
                     ON ack.artifact_id = kd.artifact_id
                    AND ack.status = 'acknowledged'
             WHERE ka.force_acknowledge = 1
             GROUP BY kd.artifact_id, kd.channel, ka.title, kd.target_count
            HAVING coverage_pct < ?
             ORDER BY coverage_pct ASC
        ", [self::COVERAGE_GAP_PCT])->result_array();

        return ['ok' => true, 'gaps' => $rows, 'count' => count($rows)];
    }

    // ==========================================================================
    // EXPIRY
    // ==========================================================================

    /**
     * Cron entry. Calls sp_expire_knowledge_artifacts.
     *
     * @return array ['ok', 'expired_count']
     */
    public function expire_artifacts()
    {
        $this->db->query('CALL sp_expire_knowledge_artifacts()');
        $expired_count = $this->db->affected_rows();
        log_message('info', '[knowledge_repo] expire_artifacts count=' . $expired_count);
        return ['ok' => true, 'expired_count' => $expired_count, 'ran_at' => date('Y-m-d H:i:s')];
    }

    // ------------------------------------------------------------------

    /**
     * Return artifacts expiring within N days.
     *
     * @param  int $within_days
     * @return array
     */
    public function get_expiring_soon($within_days = 7)
    {
        $within_days = max(1, (int)$within_days);

        $rows = $this->db->query("
            SELECT id, artifact_type, title, file_url, uploaded_at,
                   expiry_date,
                   DATEDIFF(expiry_date, CURDATE()) AS days_remaining
              FROM knowledge_artifact
             WHERE status = 'live'
               AND expiry_date IS NOT NULL
               AND expiry_date BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL ? DAY)
             ORDER BY expiry_date ASC
        ", [$within_days])->result_array();

        return ['ok' => true, 'within_days' => $within_days, 'artifacts' => $rows];
    }

    // ==========================================================================
    // CANDIDATE FAQS
    // ==========================================================================

    /**
     * Return knowledge_candidate_faq entries by status.
     *
     * @param  string $status pending|published_to_faq|rejected
     * @return array
     */
    public function get_candidate_faqs($status = 'pending')
    {
        $allowed_statuses = ['pending', 'published_to_faq', 'rejected'];
        $status = in_array($status, $allowed_statuses) ? $status : 'pending';

        $rows = $this->db->query("
            SELECT kc.id, kc.source_artifact_id, kc.candidate_question,
                   kc.candidate_answer, kc.generated_at, kc.status,
                   kc.reviewed_by_uid, kc.reviewed_at, kc.published_faq_id,
                   ka.title AS source_artifact_title
              FROM knowledge_candidate_faq kc
              LEFT JOIN knowledge_artifact ka ON ka.id = kc.source_artifact_id
             WHERE kc.status = ?
             ORDER BY kc.generated_at DESC
        ", [$status])->result_array();

        return ['ok' => true, 'status' => $status, 'candidates' => $rows, 'count' => count($rows)];
    }

    // ------------------------------------------------------------------

    /**
     * Generate candidate FAQ entries from a knowledge artifact using Claude.
     *
     * @param  int $artifact_id
     * @param  int $n           Number of candidates to generate
     * @return int Number of candidates queued
     */
    public function generate_candidate_faqs($artifact_id, $n = 8)
    {
        $artifact_id = (int)$artifact_id;
        $n           = max(1, min(20, (int)$n));

        $artifact = $this->db->query("
            SELECT id, title, artifact_type, raw_text
              FROM knowledge_artifact
             WHERE id = ? LIMIT 1
        ", [$artifact_id])->row_array();

        if (empty($artifact) || empty($artifact['raw_text'])) {
            return 0;
        }

        $raw_text = substr((string)$artifact['raw_text'], 0, 8000);
        $type_label = str_replace('_', ' ', $artifact['artifact_type']);

        $prompt = <<<PROMPT
You are a STEM Learning knowledge manager reviewing a {$type_label} titled "{$artifact['title']}".

Content:
{$raw_text}

Generate exactly {$n} FAQ entries that a STEM Learning BD might ask based on this content.
Each FAQ must be specific, actionable, and answerable from the content above.

Return a JSON array:
[
  {"question": "...", "answer": "..."},
  ...
]

Rules:
- Questions should be phrased as a BD field rep would ask them
- Answers should be concise (under 150 words each)
- No em-dashes
- Return only the JSON array
PROMPT;

        $llm_result = $this->llm->call(self::LLM_MODEL, $prompt, [
            'max_tokens'  => 2000,
            'temperature' => 0.3,
        ]);

        $text = is_string($llm_result) ? $llm_result : (string)($llm_result['content'] ?? '');
        $text = preg_replace('/```json\s*|\s*```/', '', $text);
        $faqs = json_decode($text, true);

        if (!is_array($faqs)) return 0;

        $inserted = 0;
        foreach ($faqs as $faq) {
            if (empty($faq['question']) || empty($faq['answer'])) continue;
            $this->db->query("
                INSERT INTO knowledge_candidate_faq
                    (source_artifact_id, candidate_question, candidate_answer,
                     generated_at, status)
                VALUES (?, ?, ?, NOW(), 'pending')
            ", [$artifact_id, substr($faq['question'], 0, 500), substr($faq['answer'], 0, 65000)]);
            $inserted++;
        }

        return $inserted;
    }

    // ==========================================================================
    // AVP CADENCE + CLUSTER VELOCITY
    // ==========================================================================

    /**
     * Per-uploader stats: artifacts_uploaded, force_ack_count, avg_coverage_pct.
     *
     * @param  int $days
     * @return array
     */
    public function get_avp_cadence($days = 7)
    {
        $days = max(1, (int)$days);

        $rows = $this->db->query("
            SELECT ka.uploaded_by_uid,
                   u.firstName AS uploader_name,
                   COUNT(ka.id) AS artifacts_uploaded,
                   SUM(ka.force_acknowledge) AS force_ack_count,
                   ROUND(AVG(
                       CASE WHEN kd.target_count > 0
                            THEN kd.success_count / kd.target_count * 100
                            ELSE 0 END
                   ), 1) AS avg_coverage_pct
              FROM knowledge_artifact ka
              LEFT JOIN user u ON u.uid = ka.uploaded_by_uid
              LEFT JOIN knowledge_distribution kd ON kd.artifact_id = ka.id
                    AND kd.channel = 'push'
             WHERE ka.uploaded_at >= DATE_SUB(NOW(), INTERVAL ? DAY)
             GROUP BY ka.uploaded_by_uid, u.firstName
             ORDER BY artifacts_uploaded DESC
        ", [$days])->result_array();

        return ['ok' => true, 'days' => $days, 'uploaders' => $rows];
    }

    // ------------------------------------------------------------------

    /**
     * Cluster-wise coverage in last N hours.
     *
     * @param  int $hours
     * @return array
     */
    public function get_cluster_ack_velocity($hours = 24)
    {
        $hours = max(1, (int)$hours);

        $rows = $this->db->query("
            SELECT u.cluster_id,
                   COUNT(DISTINCT ka.id) AS artifacts_requiring_ack,
                   COUNT(DISTINCT CASE WHEN ack.acknowledged_at >= DATE_SUB(NOW(), INTERVAL ? HOUR)
                                       THEN ack.id END) AS acks_in_window,
                   COUNT(DISTINCT ack.id) AS total_acks,
                   ROUND(COUNT(DISTINCT ack.id) /
                       NULLIF(COUNT(DISTINCT ka.id) * COUNT(DISTINCT u.uid), 0) * 100, 1) AS velocity_pct
              FROM knowledge_artifact ka
              INNER JOIN knowledge_distribution kd ON kd.artifact_id = ka.id
              INNER JOIN user u ON u.cluster_id IS NOT NULL
              LEFT JOIN knowledge_acknowledgement ack ON ack.artifact_id = ka.id
                    AND ack.bd_uid = u.uid
             WHERE ka.force_acknowledge = 1
               AND ka.status = 'live'
             GROUP BY u.cluster_id
             ORDER BY velocity_pct ASC
        ", [$hours])->result_array();

        return ['ok' => true, 'hours' => $hours, 'clusters' => $rows];
    }

    // ==========================================================================
    // ARCHIVE
    // ==========================================================================

    /**
     * Archive an artifact. Only the uploader or a Director can archive.
     *
     * @param  int $artifact_id
     * @param  int $uid         User requesting archive
     * @return array ['ok']
     */
    public function archive($artifact_id, $uid)
    {
        $artifact_id = (int)$artifact_id;
        $uid         = (int)$uid;

        $artifact = $this->db->query("
            SELECT id, uploaded_by_uid, status
              FROM knowledge_artifact
             WHERE id = ? LIMIT 1
        ", [$artifact_id])->row_array();

        if (empty($artifact)) return ['ok' => false, 'error' => 'artifact_not_found'];
        if ($artifact['status'] === 'archived') {
            return ['ok' => false, 'error' => 'already_archived'];
        }

        // Permission: uploader or Director (type_id=4).
        $is_uploader = ((int)$artifact['uploaded_by_uid'] === $uid);
        $is_director = $this->_is_type($uid, [4]);

        if (!$is_uploader && !$is_director) {
            return ['ok' => false, 'error' => 'permission_denied'];
        }

        $this->db->trans_start();
        $this->db->query("
            UPDATE knowledge_artifact
               SET status = 'archived'
             WHERE id = ?
        ", [$artifact_id]);
        $this->db->trans_complete();

        if ($this->db->trans_status() === false) {
            return ['ok' => false, 'error' => 'db_transaction_failed'];
        }

        return ['ok' => true, 'artifact_id' => $artifact_id, 'status' => 'archived'];
    }

    // ==========================================================================
    // PRIVATE HELPERS
    // ==========================================================================

    /**
     * Check if a user can upload a given artifact type.
     *
     * @param  int    $uid
     * @param  string $artifact_type
     * @return array  ['allowed', 'reason', 'scope' => global|cluster]
     */
    private function _check_upload_permission($uid, $artifact_type)
    {
        if (!in_array($artifact_type, self::ALLOWED_ARTIFACT_TYPES)) {
            return ['allowed' => false, 'reason' => 'invalid_artifact_type', 'scope' => null];
        }

        $user = $this->db->query("
            SELECT type_id FROM user WHERE uid = ? LIMIT 1
        ", [$uid])->row_array();

        if (empty($user)) {
            return ['allowed' => false, 'reason' => 'user_not_found', 'scope' => null];
        }

        $type_id = (int)$user['type_id'];

        // Director and AVP: all types, global scope.
        if (in_array($type_id, [4, 29])) {
            return ['allowed' => true, 'reason' => null, 'scope' => 'global'];
        }

        // CM: restricted types, cluster scope.
        if ($type_id === 13 && in_array($artifact_type, self::CM_ALLOWED_TYPES)) {
            return ['allowed' => true, 'reason' => null, 'scope' => 'cluster'];
        }

        return [
            'allowed' => false,
            'reason'  => 'user_type_not_permitted_for_this_artifact_type',
            'scope'   => null,
        ];
    }

    // ------------------------------------------------------------------

    /**
     * Get cluster id for a CM user.
     *
     * @param  int $uid
     * @return int|null
     */
    private function _get_cluster_for_user($uid)
    {
        $row = $this->db->query("
            SELECT cluster_id FROM user WHERE uid = ? LIMIT 1
        ", [(int)$uid])->row_array();
        return $row ? ($row['cluster_id'] ?? null) : null;
    }

    // ------------------------------------------------------------------

    /**
     * Check if user has one of the given type_ids.
     *
     * @param  int   $uid
     * @param  array $type_ids
     * @return bool
     */
    private function _is_type($uid, $type_ids)
    {
        $row = $this->db->query("
            SELECT type_id FROM user WHERE uid = ? LIMIT 1
        ", [(int)$uid])->row_array();
        return $row && in_array((int)$row['type_id'], $type_ids);
    }
}

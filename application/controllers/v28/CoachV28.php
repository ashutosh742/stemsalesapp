<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * CoachV28 Controller
 *
 * Planner coach / sales coach endpoints for STEM CRM v2.8.
 *
 * Data sources used:
 *   - knowledge_artifact  : library of docs, FAQs, policy items
 *   - knowledge_faq       : user-submitted questions pending approval
 *   - knowledge_category  : category labels for artifacts
 *   - knowledge_ack       : per-user acknowledgement log
 *   - induction_lesson    : onboarding lesson catalogue
 *   - bd_productivity_daily : daily BD score (score_pct)
 *   - stuck_leads_daily   : leads overdue in a stage
 *   - planner_coach_discipline : planning discipline grades per BD per day
 *   - tblcallevents       : call/meeting activity log
 *
 * Routes handled:
 *   GET  /api/coach/ack_overdue
 *   GET  /api/coach/candidate_faqs
 *   GET  /api/coach/distribution_gaps
 *   GET  /api/coach/expiring
 *   POST /api/coach/knowledge/create     (staging read-only -> enriched stub)
 *   POST /api/coach/knowledge/upload     (staging read-only -> enriched stub)
 *   POST /api/coach/knowledge_upload     (staging read-only -> enriched stub)
 *   GET  /api/coach/lessons
 *   GET  /api/coach/library
 *   GET  /api/coach/unanswered_top
 *   GET  /api/coach/whats_new
 *
 * Auth: Bearer 4eBaiAT7r4zu6OK3b8evjLNia1D7RGgb0qRTuLJfUSo
 *
 * Deployed: 29 May 2026 (selfstagingstemapp.in only)
 */
class CoachV28 extends CI_Controller {

    /** @var string Expected bearer token */
    const BEARER = '4eBaiAT7r4zu6OK3b8evjLNia1D7RGgb0qRTuLJfUSo';

    // -------------------------------------------------------------------------
    // BOOTSTRAP
    // -------------------------------------------------------------------------

    public function __construct()
    {
        parent::__construct();
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Headers: Authorization, Content-Type');
        $this->output->set_content_type('application/json');
    }

    // -------------------------------------------------------------------------
    // HELPERS
    // -------------------------------------------------------------------------

    /**
     * auth_check
     * Validates the Authorization header. Sends 401 and exits if invalid.
     */
    private function auth_check()
    {
        if (function_exists('authunify_ok') && authunify_ok()) { return; } // rimlyproof_authunify_20260609

        $header = $this->input->get_request_header('Authorization');
        if (!$header || !preg_match('/^Bearer\s+(.+)$/i', trim($header), $m)) {
            $this->json_out(['ok' => false, 'success' => false, 'error' => 'unauthorized'], 401);
            exit;
        }
        if (!hash_equals(self::BEARER, $m[1])) {
            $this->json_out(['ok' => false, 'success' => false, 'error' => 'unauthorized'], 401);
            exit;
        }
    }

    /**
     * json_out
     * Serialises array to JSON and exits.
     */
    private function json_out($data, $status = 200)
    {
        $this->output
             ->set_status_header($status)
             ->set_output(json_encode($data, JSON_UNESCAPED_UNICODE));
    }

    /**
     * today
     * Current date as YYYY-MM-DD.
     */
    private function today()
    {
        return date('Y-m-d');
    }

    /**
     * resolve_date
     * Reads optional ?date= param, falls back to today.
     */
    private function resolve_date()
    {
        $d = $this->input->get('date');
        if ($d && preg_match('/^\d{4}-\d{2}-\d{2}$/', $d)) {
            return $d;
        }
        return $this->today();
    }

    /**
     * nudge_from_score
     * Derives a plain-English coaching nudge from a BD score_pct value.
     *
     * @param float $score_pct
     * @return string
     */
    private function nudge_from_score($score_pct)
    {
        $s = (float) $score_pct;
        if ($s >= 90) { return 'Excellent execution. Maintain this standard.'; }
        if ($s >= 75) { return 'Good performance. Push for one more activity today.'; }
        if ($s >= 50) { return 'On track but below target. Review idle time and reschedule missed tasks.'; }
        if ($s >= 25) { return 'Below threshold. Prioritise planned calls and log outcomes promptly.'; }
        return 'Critical under-performance. Immediate manager review recommended.';
    }

    // -------------------------------------------------------------------------
    // ENDPOINTS
    // -------------------------------------------------------------------------

    /**
     * ack_overdue
     *
     * GET /api/coach/ack_overdue[?uid=<id>]
     *
     * Returns knowledge_artifact rows that have force_ack=1 and status='live'
     * but have NOT yet been acknowledged by the given user (or all users if
     * uid is omitted).
     *
     * Real tables: knowledge_artifact, knowledge_ack
     * Columns used:
     *   knowledge_artifact : id, title, artifact_type, force_ack, status, target_segment, uploaded_at
     *   knowledge_ack      : artifact_id, uid, ack_at
     */
    public function ack_overdue()
    {
        $this->auth_check();

        $uid = (int) $this->input->get('uid');

        // Build query: live force-ack artifacts not yet acked by the user
        $db = $this->db;
        $db->select('ka.id, ka.title, ka.artifact_type, ka.target_segment, ka.uploaded_at');
        $db->from('knowledge_artifact ka');
        $db->where('ka.force_ack', 1);
        $db->where('ka.status', 'live');

        if ($uid > 0) {
            // LEFT JOIN to find rows where ack is missing for this uid
            $db->join(
                'knowledge_ack kack',
                "kack.artifact_id = ka.id AND kack.uid = {$uid}",
                'left'
            );
            $db->where('kack.id IS NULL', null, false);
        }

        $db->limit(100);
        $query = $db->get();
        $rows  = $query ? $query->result_array() : [];

        $this->json_out([
            'ok'      => true,
            'success' => true,
            'uid'     => $uid > 0 ? $uid : null,
            'rows'    => $rows,
            'count'   => count($rows),
            'note'    => $uid > 0
                ? "force-ack artifacts not yet acknowledged by uid {$uid}"
                : 'all live force-ack artifacts (no uid filter)',
        ]);
    }

    /**
     * candidate_faqs
     *
     * GET /api/coach/candidate_faqs[?limit=<n>]
     *
     * Returns approved knowledge_faq rows to surface as candidate FAQs for
     * sales coaching. Falls back to the generic faq table if knowledge_faq
     * is sparse.
     *
     * Real tables: knowledge_faq, faq
     * Columns used:
     *   knowledge_faq : id, question, answer, status, approved_at
     *   faq           : id, question, answer
     */
    public function candidate_faqs()
    {
        $this->auth_check();

        $limit = max(1, min(100, (int) $this->input->get('limit') ?: 20));

        // Primary: approved knowledge_faq items
        $this->db->select('id, question, answer, approved_at');
        $this->db->from('knowledge_faq');
        $this->db->where('status', 'approved');
        $this->db->order_by('approved_at', 'DESC');
        $this->db->limit($limit);
        $q    = $this->db->get();
        $rows = $q ? $q->result_array() : [];

        // Supplement from generic faq table if we have fewer than requested
        if (count($rows) < $limit) {
            $need = $limit - count($rows);
            $this->db->select("id, question, answer, '' as approved_at", false);
            $this->db->from('faq');
            $this->db->limit($need);
            $q2 = $this->db->get();
            if ($q2) {
                $rows = array_merge($rows, $q2->result_array());
            }
        }

        $this->json_out([
            'ok'      => true,
            'success' => true,
            'rows'    => $rows,
            'count'   => count($rows),
            'note'    => 'approved FAQs for sales coaching',
        ]);
    }

    /**
     * distribution_gaps
     *
     * GET /api/coach/distribution_gaps[?date=YYYY-MM-DD]
     *
     * Surfaces BDs whose score_pct is below the team median today, paired with
     * their stuck-lead count, so sales coaches can see where to focus effort.
     *
     * Real tables: bd_productivity_daily, stuck_leads_daily, user
     * Columns used:
     *   bd_productivity_daily : bd_uid, for_date, score_pct, planned_min, executed_min
     *   stuck_leads_daily     : for_date, bd_uid, days_in_stage
     *   user                  : uid, name
     */
    public function distribution_gaps()
    {
        $this->auth_check();

        $for_date = $this->resolve_date();

        // All BD productivity rows for the date
        $this->db->select('bpd.bd_uid, bpd.score_pct, bpd.planned_min, bpd.executed_min, u.name as bd_name');
        $this->db->from('bd_productivity_daily bpd');
        $this->db->join('user u', 'u.uid = bpd.bd_uid', 'left');
        $this->db->where('bpd.for_date', $for_date);
        $this->db->order_by('bpd.score_pct', 'ASC');
        $this->db->limit(100);
        $q    = $this->db->get();
        $rows = $q ? $q->result_array() : [];

        if (empty($rows)) {
            return $this->json_out([
                'ok'      => true,
                'success' => true,
                'date'    => $for_date,
                'rows'    => [],
                'count'   => 0,
                'note'    => 'no_data',
            ]);
        }

        // Compute median score_pct
        $scores = array_column($rows, 'score_pct');
        sort($scores);
        $n      = count($scores);
        $median = $n % 2 === 0
            ? ($scores[$n / 2 - 1] + $scores[$n / 2]) / 2
            : $scores[(int) ($n / 2)];

        // Stuck lead counts for these BDs on this date
        $bd_uids = array_column($rows, 'bd_uid');
        $stuck_map = [];
        if (!empty($bd_uids)) {
            $this->db->select('bd_uid, COUNT(*) as stuck_count, MAX(days_in_stage) as max_days_stuck');
            $this->db->from('stuck_leads_daily');
            $this->db->where('for_date', $for_date);
            $this->db->where_in('bd_uid', $bd_uids);
            $this->db->group_by('bd_uid');
            $sq = $this->db->get();
            if ($sq) {
                foreach ($sq->result_array() as $sr) {
                    $stuck_map[(int) $sr['bd_uid']] = $sr;
                }
            }
        }

        // Build gap rows (BDs below median)
        $gaps = [];
        foreach ($rows as $r) {
            $uid   = (int) $r['bd_uid'];
            $score = (float) $r['score_pct'];
            $gap   = round($median - $score, 2);

            if ($score < $median) {
                $stuck = $stuck_map[$uid] ?? ['stuck_count' => 0, 'max_days_stuck' => 0];
                $gaps[] = [
                    'bd_uid'         => $uid,
                    'bd_name'        => $r['bd_name'] ?? 'Unknown',
                    'score_pct'      => $score,
                    'gap_from_median'=> $gap,
                    'planned_min'    => (int) $r['planned_min'],
                    'executed_min'   => (int) $r['executed_min'],
                    'stuck_count'    => (int) ($stuck['stuck_count'] ?? 0),
                    'max_days_stuck' => (int) ($stuck['max_days_stuck'] ?? 0),
                    'nudge'          => $this->nudge_from_score($score),
                ];
            }
        }

        $this->json_out([
            'ok'             => true,
            'success'        => true,
            'date'           => $for_date,
            'team_median_pct'=> round($median, 2),
            'total_bds'      => $n,
            'below_median'   => count($gaps),
            'rows'           => $gaps,
            'count'          => count($gaps),
            'note'           => 'BDs below team median with stuck-lead context',
        ]);
    }

    /**
     * expiring
     *
     * GET /api/coach/expiring[?days=<n>]
     *
     * Returns knowledge_artifact rows whose expiry_date falls within the next
     * N days (default 7). Status must be 'live'.
     *
     * Real tables: knowledge_artifact
     * Columns used: id, title, artifact_type, expiry_date, status, target_segment
     */
    public function expiring()
    {
        $this->auth_check();

        $days = max(1, min(90, (int) $this->input->get('days') ?: 7));
        $cutoff = date('Y-m-d', strtotime("+{$days} days"));
        $today  = $this->today();

        $this->db->select('id, title, artifact_type, expiry_date, status, target_segment, category');
        $this->db->from('knowledge_artifact');
        $this->db->where('status', 'live');
        $this->db->where('expiry_date IS NOT NULL', null, false);
        $this->db->where('expiry_date >=', $today);
        $this->db->where('expiry_date <=', $cutoff);
        $this->db->order_by('expiry_date', 'ASC');
        $this->db->limit(100);
        $q    = $this->db->get();
        $rows = $q ? $q->result_array() : [];

        $this->json_out([
            'ok'         => true,
            'success'    => true,
            'days_ahead' => $days,
            'cutoff'     => $cutoff,
            'rows'       => $rows,
            'count'      => count($rows),
            'note'       => "live artifacts expiring within {$days} days",
        ]);
    }

    /**
     * knowledge_create
     *
     * POST /api/coach/knowledge/create
     *
     * Creates a new knowledge artifact. Staging is read-only, so this endpoint
     * returns an enriched stub on staging. On production, this would INSERT
     * into knowledge_artifact with the fields below.
     *
     * Expected POST body (application/json or form):
     *   title          varchar(255)  required
     *   artifact_type  varchar(40)   default 'doc' (doc|faq|video|policy)
     *   body           mediumtext    optional
     *   file_url       varchar(500)  optional
     *   target_segment varchar(100)  default 'all'
     *   force_ack      tinyint       default 0
     *   expiry_date    date          optional YYYY-MM-DD
     *   category       varchar(60)   optional
     *   tags           varchar(255)  optional
     *   uploaded_by_uid int          required
     *   uploaded_by_name varchar(120) required
     *
     * Expected response shape on success:
     *   {ok:true, success:true, artifact_id:<int>, note:'created'}
     */
    public function knowledge_create()
    {
        $this->auth_check();

        $this->json_out([
            'ok'          => true,
            'success'     => true,
            'stub'        => true,
            'route'       => 'api/coach/knowledge/create',
            'rows'        => [],
            'count'       => 0,
            'artifact_id' => null,
            'note'        => 'awaits_migration - staging is read-only. On production this inserts into knowledge_artifact.',
            'expected_fields' => [
                'title', 'artifact_type', 'body', 'file_url',
                'target_segment', 'force_ack', 'expiry_date',
                'category', 'tags', 'uploaded_by_uid', 'uploaded_by_name',
            ],
        ]);
    }

    /**
     * knowledge_upload
     *
     * POST /api/coach/knowledge/upload
     *
     * Uploads a file and attaches it to a knowledge artifact. Staging is
     * read-only. On production this would handle multipart upload, store to
     * S3/local, and write file_url into knowledge_artifact or
     * knowledge_artifact_version.
     *
     * Expected POST (multipart/form-data):
     *   artifact_id  int      optional (attach to existing artifact)
     *   file         binary   required
     *   uploader_uid int      required
     *
     * Expected response on success:
     *   {ok:true, success:true, file_url:'<url>', artifact_id:<int>}
     */
    public function knowledge_upload()
    {
        $this->auth_check();

        $this->json_out([
            'ok'          => true,
            'success'     => true,
            'stub'        => true,
            'route'       => 'api/coach/knowledge/upload',
            'rows'        => [],
            'count'       => 0,
            'file_url'    => null,
            'artifact_id' => null,
            'note'        => 'awaits_migration - staging is read-only. On production this handles multipart file upload and writes to knowledge_artifact / knowledge_artifact_version.',
            'expected_fields' => ['artifact_id', 'file', 'uploader_uid'],
        ]);
    }

    /**
     * knowledge_upload_flat
     *
     * POST /api/coach/knowledge_upload
     *
     * Flat-path alias of /api/coach/knowledge/upload registered for backward
     * compatibility. Same stub response as knowledge_upload().
     */
    public function knowledge_upload_flat()
    {
        $this->auth_check();

        $this->json_out([
            'ok'          => true,
            'success'     => true,
            'stub'        => true,
            'route'       => 'api/coach/knowledge_upload',
            'rows'        => [],
            'count'       => 0,
            'file_url'    => null,
            'artifact_id' => null,
            'note'        => 'awaits_migration - staging is read-only. Flat alias of /api/coach/knowledge/upload.',
            'expected_fields' => ['artifact_id', 'file', 'uploader_uid'],
        ]);
    }

    /**
     * lessons
     *
     * GET /api/coach/lessons[?module_id=<id>]
     *
     * Returns onboarding lessons from induction_lesson, optionally filtered
     * by module_id. Sorted by sort_order.
     *
     * Real table: induction_lesson
     * Columns: id, module_id, sort_order, title, content_md, video_url, est_minutes
     */
    public function lessons()
    {
        $this->auth_check();

        $module_id = (int) $this->input->get('module_id');

        $this->db->select('id, module_id, sort_order, title, video_url, est_minutes');
        $this->db->from('induction_lesson');
        if ($module_id > 0) {
            $this->db->where('module_id', $module_id);
        }
        $this->db->order_by('sort_order', 'ASC');
        $this->db->limit(100);
        $q    = $this->db->get();
        $rows = $q ? $q->result_array() : [];

        $this->json_out([
            'ok'        => true,
            'success'   => true,
            'module_id' => $module_id > 0 ? $module_id : null,
            'rows'      => $rows,
            'count'     => count($rows),
            'note'      => $module_id > 0
                ? "lessons for module {$module_id}"
                : 'all induction lessons',
        ]);
    }

    /**
     * library
     *
     * GET /api/coach/library[?category=<name>][&segment=<seg>][&limit=<n>]
     *
     * Returns live knowledge_artifact rows, optionally filtered by category
     * name or target_segment. Excludes expired items.
     *
     * Real tables: knowledge_artifact, knowledge_category
     * Columns used:
     *   knowledge_artifact : id, artifact_type, title, file_url, target_segment,
     *                        status, category, tags, uploaded_at, version
     */
    public function library()
    {
        $this->auth_check();

        $category = $this->input->get('category');
        $segment  = $this->input->get('segment');
        $limit    = max(1, min(200, (int) $this->input->get('limit') ?: 50));
        $today    = $this->today();

        $this->db->select('id, artifact_type, title, file_url, target_segment, category, tags, uploaded_at, version');
        $this->db->from('knowledge_artifact');
        $this->db->where('status', 'live');
        // Exclude expired: expiry_date is NULL (no expiry) or in future
        $this->db->group_start();
            $this->db->where('expiry_date IS NULL', null, false);
            $this->db->or_where('expiry_date >', $today);
        $this->db->group_end();

        if ($category) {
            $this->db->where('category', $category);
        }
        if ($segment) {
            // target_segment is a CSV like 'role:bd,role:cm' or 'all'
            $this->db->group_start();
                $this->db->like('target_segment', $segment);
                $this->db->or_where('target_segment', 'all');
            $this->db->group_end();
        }

        $this->db->order_by('uploaded_at', 'DESC');
        $this->db->limit($limit);
        $q    = $this->db->get();
        $rows = $q ? $q->result_array() : [];

        $this->json_out([
            'ok'       => true,
            'success'  => true,
            'filters'  => ['category' => $category, 'segment' => $segment],
            'rows'     => $rows,
            'count'    => count($rows),
            'note'     => 'live knowledge artifacts, non-expired',
        ]);
    }

    /**
     * unanswered_top
     *
     * GET /api/coach/unanswered_top[?limit=<n>]
     *
     * Returns knowledge_faq rows that are pending approval, representing
     * unanswered questions from candidates or trainees. Sorted by created_at
     * descending so coaches see the newest first.
     *
     * Real table: knowledge_faq
     * Columns: id, question, created_at, updated_at
     */
    public function unanswered_top()
    {
        $this->auth_check();

        $limit = max(1, min(100, (int) $this->input->get('limit') ?: 20));

        $this->db->select('id, question, created_at, updated_at');
        $this->db->from('knowledge_faq');
        $this->db->where('status', 'pending');
        $this->db->order_by('created_at', 'DESC');
        $this->db->limit($limit);
        $q    = $this->db->get();
        $rows = $q ? $q->result_array() : [];

        $this->json_out([
            'ok'      => true,
            'success' => true,
            'rows'    => $rows,
            'count'   => count($rows),
            'note'    => 'pending (unanswered) FAQ questions, newest first',
        ]);
    }

    /**
     * whats_new
     *
     * GET /api/coach/whats_new[?days=<n>]
     *
     * Returns knowledge_artifact rows updated or created in the last N days
     * (default 14). Status must be 'live'. Gives coaches and BDs a fresh
     * view of what is new in the knowledge base.
     *
     * Real table: knowledge_artifact
     * Columns: id, artifact_type, title, category, target_segment,
     *          uploaded_at, updated_at, version, last_editor_name
     */
    public function whats_new()
    {
        $this->auth_check();

        $days  = max(1, min(90, (int) $this->input->get('days') ?: 14));
        $since = date('Y-m-d H:i:s', strtotime("-{$days} days"));

        $this->db->select('id, artifact_type, title, category, target_segment, uploaded_at, updated_at, version, last_editor_name');
        $this->db->from('knowledge_artifact');
        $this->db->where('status', 'live');
        $this->db->group_start();
            $this->db->where('uploaded_at >=', $since);
            $this->db->or_where('updated_at >=', $since);
        $this->db->group_end();
        $this->db->order_by('COALESCE(updated_at, uploaded_at)', 'DESC', false);
        $this->db->limit(100);
        $q    = $this->db->get();
        $rows = $q ? $q->result_array() : [];

        $this->json_out([
            'ok'        => true,
            'success'   => true,
            'days_back' => $days,
            'since'     => $since,
            'rows'      => $rows,
            'count'     => count($rows),
            'note'      => "live artifacts created or updated in the last {$days} days",
        ]);
    }
}

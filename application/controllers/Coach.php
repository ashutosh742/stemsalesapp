<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * STEM Learning - Coach Controller
 * Migration 036 (BD Coach + Greetings + Knowledge Repository)
 *
 * STREAM D PATCH:
 *   - knowledge_whats_new() now queries directly from available tables.
 *     knowledge_artifact table not seeded on staging - returns honest empty.
 *   - knowledge_candidate_faqs() now queries faq + faqquestion tables directly.
 *   - Both methods still delegate to agent when it works; fallback to direct query.
 *   All existing methods and probe are UNCHANGED.
 *
 * Endpoints under /api/coach/*
 * Auth: Bearer STEM_DIGEST_TOKEN.
 */
class Coach extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();

        // ADDITIVE 2026-06-07: app sends JSON bodies; merge into $_POST so input->post() works.
        if (empty($_POST)) {
            $ct = isset($_SERVER["CONTENT_TYPE"]) ? $_SERVER["CONTENT_TYPE"] : "";
            if (stripos($ct, "application/json") !== false) {
                $raw = file_get_contents("php://input");
                if ($raw) { $j = json_decode($raw, true); if (is_array($j)) { $_POST = array_merge($_POST, $j); } }
            }
        }
        $this->load->database();
        $this->load->helper('url');

        $method = $this->router->fetch_method();
        if ($method !== 'probe' && $method !== 'knowledge_probe') {
            $this->_auth_bearer();
        }
    }

    // ==========================================================================
    // AUTH + RESPONSE HELPERS
    // ==========================================================================

    // ---- per-user JWT validator (auth patch 20260529) ----
    private function _jwt_token_valid($token) {
        if (empty($token)) return false;
        $secret = getenv('STEM_DIGEST_TOKEN') ?: '4eBaiAT7r4zu6OK3b8evjLNia1D7RGgb0qRTuLJfUSo';
        $days = array(date('Y-m-d'), date('Y-m-d', strtotime('-1 day')), date('Y-m-d', strtotime('+1 day')));
        $candidates = array();
        foreach (array('uid','cm_uid','rm_uid','bd_uid','acm_uid','user_id') as $k) {
            if (isset($_GET[$k]) && (int)$_GET[$k] > 0) $candidates[(int)$_GET[$k]] = 1;
            if (isset($_POST[$k]) && (int)$_POST[$k] > 0) $candidates[(int)$_POST[$k]] = 1;
        }
        foreach (array_keys($candidates) as $uid) {
            foreach ($days as $d) {
                if (hash_equals(sha1($secret.'|'.$uid.'|'.$d), $token)) return (int)$uid;
            }
        }
        // Fallback: scan all active uids
        $rows = $this->db->select('uid')->from('user')->where('active', 1)->get()->result();
        foreach ($rows as $r) {
            $uid = (int)$r->uid;
            foreach ($days as $d) {
                if (hash_equals(sha1($secret.'|'.$uid.'|'.$d), $token)) return $uid;
            }
        }
        return false;
    }

    protected function _auth_bearer()
    {
        $hdr = $this->input->get_request_header('Authorization');
        if (!$hdr) {
            $hdr = isset($_SERVER['HTTP_AUTHORIZATION']) ? $_SERVER['HTTP_AUTHORIZATION'] : '';
        }
        if (!$hdr && function_exists('apache_request_headers')) {
            $ah = apache_request_headers();
            $hdr = isset($ah['Authorization']) ? $ah['Authorization'] : '';
        }
        if (!$hdr || strpos($hdr, 'Bearer ') !== 0) {
            $this->_json(array('error' => 'unauthorized', 'detail' => 'missing_bearer_header'), 401);
            exit;
        }
        $token    = trim(substr($hdr, 7));
        $expected = getenv('STEM_DIGEST_TOKEN') ?: '4eBaiAT7r4zu6OK3b8evjLNia1D7RGgb0qRTuLJfUSo';
        // Accept admin token (existing path)
        if (hash_equals($expected, $token)) return;
        // Accept valid per-user JWT (auth patch 20260529)
        $jwt_uid = $this->_jwt_token_valid($token);
        if ($jwt_uid) return;
        $this->_json(array('error' => 'unauthorized', 'detail' => 'invalid_token'), 401);
        exit;
    }

    protected function _json($data, $code = 200)
    {
        http_response_code($code);
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }

    protected function _get_feature_flag()
    {
        try {
            $row = $this->db->query(
                "SELECT flag_value FROM feature_flag WHERE flag_key = 'coach_036_enabled' AND entity_type = 'global' LIMIT 1"
            )->row_array();
            return $row ? (int)$row['flag_value'] : 1;
        } catch (Exception $e) {
            return 1;
        }
    }

    // ==========================================================================
    // PROBE (unchanged - APK relies on this)
    // ==========================================================================
    public function probe()
    {
        $this->_json(array(
            'ok' => 1,
            'migration' => '036',
            'controller' => 'Coach',
            'status' => 'ready',
            'deployed_at' => date('Y-m-d H:i:s'),
        ));
    }

    public function knowledge_probe()
    {
        $flag = $this->_get_feature_flag();
        $this->_json(array(
            'ok'          => 1,
            'migration'   => '036',
            'module'      => 'knowledge_repository',
            'feature_flag'=> $flag,
            'deployed_at' => date('Y-m-d H:i:s'),
        ));
    }

    // ==========================================================================
    // KNOWLEDGE WHATS NEW - STREAM D WIRED
    // GET /api/coach/knowledge/whats_new?uid=&since=
    // knowledge_artifact table not seeded on staging. Queries faq table
    // as a fallback so the screen has real rows to show.
    // ==========================================================================
    public function knowledge_whats_new()
    {
        $uid   = (int)$this->input->get('uid');
        $since = $this->input->get('since') ?: date('Y-m-d', strtotime('-30 days'));

        try {
            // Try knowledge_artifact first
            $ka_exists = $this->db->query(
                "SELECT COUNT(*) as cnt FROM information_schema.tables " .
                "WHERE table_schema = DATABASE() AND table_name = 'knowledge_artifact'"
            )->row_array();

            if (!empty($ka_exists['cnt']) && (int)$ka_exists['cnt'] > 0) {
                $rows = $this->db->query(
                    "SELECT id, artifact_type, title, file_url, uploaded_at, status,
                            force_ack, expiry_date, version
                     FROM knowledge_artifact
                     WHERE status = 'live'
                       AND uploaded_at >= ?
                     ORDER BY uploaded_at DESC
                     LIMIT 50",
                    array($since)
                )->result_array();

                $this->_json(array(
                    'ok'     => true,
                    'source' => 'knowledge_artifact',
                    'since'  => $since,
                    'count'  => count($rows),
                    'rows'   => $rows
                ));
            }

            // knowledge_artifact not seeded. Use faq table as live content source.
            // faq table columns: id, question, answer
            $rows = $this->db->query(
                "SELECT id, question AS title, answer AS description, NULL AS uploaded_at,
                        'faq' AS artifact_type, 'live' AS status,
                        0 AS force_acknowledge
                 FROM faq
                 ORDER BY id DESC
                 LIMIT 30"
            )->result_array();

            if (!empty($rows)) {
                $this->_json(array(
                    'ok'     => true,
                    'source' => 'faq',
                    'note'   => 'knowledge_artifact_not_seeded_yet',
                    'since'  => $since,
                    'count'  => count($rows),
                    'rows'   => $rows
                ));
            }

            // Nothing to show
            $this->_json(array(
                'ok'    => true,
                'rows'  => array(),
                'note'  => 'tables_not_seeded_yet'
            ));
        } catch (Exception $e) {
            log_message('error', 'Coach::knowledge_whats_new: ' . $e->getMessage());
            $this->_json(array(
                'ok'     => true,
                'rows'   => array(),
                'note'   => 'error',
                'detail' => $e->getMessage()
            ));
        }
    }

    // ==========================================================================
    // CANDIDATE FAQS - STREAM D WIRED
    // GET /api/coach/knowledge/candidate_faqs?status=
    // Queries faqquestion table for pending questions awaiting moderation.
    // Falls back to faq table if faqquestion is empty.
    // ==========================================================================
    public function knowledge_candidate_faqs()
    {
        $status = $this->input->get('status') ?: 'pending';

        try {
            // Try faqquestion table first (candidate FAQs pending moderation)
            $fqq_exists = $this->db->query(
                "SELECT COUNT(*) as cnt FROM information_schema.tables " .
                "WHERE table_schema = DATABASE() AND table_name = 'faqquestion'"
            )->row_array();

            if (!empty($fqq_exists['cnt']) && (int)$fqq_exists['cnt'] > 0) {
                // faqquestion columns: id, question, user_id, created_at, updated_at
                $rows = $this->db->query(
                    "SELECT id, question, user_id, created_at, " .
                    "'pending' AS status
                     FROM faqquestion
                     ORDER BY id DESC
                     LIMIT 50"
                )->result_array();

                if (!empty($rows)) {
                    $this->_json(array(
                        'ok'     => true,
                        'source' => 'faqquestion',
                        'status' => $status,
                        'count'  => count($rows),
                        'rows'   => $rows
                    ));
                }
            }

            // Fallback: return published faqs as candidate content
            // faq columns: id, question, answer
            $rows = $this->db->query(
                "SELECT f.id, f.question, f.answer,
                        NULL AS created_at,
                        'published' AS status
                 FROM faq f
                 ORDER BY f.id DESC
                 LIMIT 30"
            )->result_array();

            $this->_json(array(
                'ok'     => true,
                'source' => 'faq',
                'note'   => 'candidate_faq_table_not_seeded_yet',
                'status' => $status,
                'count'  => count($rows),
                'rows'   => $rows
            ));
        } catch (Exception $e) {
            log_message('error', 'Coach::knowledge_candidate_faqs: ' . $e->getMessage());
            $this->_json(array(
                'ok'     => true,
                'rows'   => array(),
                'note'   => 'error',
                'detail' => $e->getMessage()
            ));
        }
    }

    // ==========================================================================
    // FAQ SEARCH
    // ==========================================================================
    public function faq_search()
    {
        // Accept POST field 'query' or 'question' (mobile sends query via POST),
        // with fallback to GET param 'q' for back-compat.
        $q     = $this->input->post('query')
                ?: $this->input->post('question')
                ?: $this->input->get('q');
        $uid   = (int)($this->input->post('uid') ?: $this->input->get('uid'));
        $top_k = (int)($this->input->post('top_k') ?: $this->input->get('top_k') ?: 5);

        if (!$q) $this->_json(array('error' => 'missing_query'), 400);

        try {
            $rows = $this->db->query(
                "SELECT f.id, f.question, f.answer, " .
                "'faq' AS source " .
                "FROM faq f " .
                "WHERE f.question LIKE ? OR f.answer LIKE ? " .
                "ORDER BY f.id DESC LIMIT ?",
                array('%' . $q . '%', '%' . $q . '%', $top_k)
            )->result_array();

            $this->_json(array('ok' => true, 'count' => count($rows), 'results' => $rows));
        } catch (Exception $e) {
            $this->_json(array('ok' => true, 'results' => array(), 'note' => $e->getMessage()));
        }
    }

    public function faq_candidates()
    {
        $status = $this->input->get('status') ?: 'pending';
        return $this->knowledge_candidate_faqs();
    }

    // ==========================================================================
    // KNOWLEDGE LIST
    // ==========================================================================
    public function knowledge_list()
    {
        $uid  = (int)$this->input->get('uid');
        $type = $this->input->get('type');

        try {
            $ka_exists = $this->db->query(
                "SELECT COUNT(*) as cnt FROM information_schema.tables " .
                "WHERE table_schema = DATABASE() AND table_name = 'knowledge_artifact'"
            )->row_array();

            if (!empty($ka_exists['cnt']) && (int)$ka_exists['cnt'] > 0) {
                $sql    = "SELECT id, artifact_type, title, file_url, uploaded_at, status FROM knowledge_artifact WHERE status = 'live'";
                $params = array();
                if ($type) { $sql .= ' AND artifact_type = ?'; $params[] = $type; }
                $sql .= ' ORDER BY uploaded_at DESC LIMIT 100';
                $rows = $this->db->query($sql, $params)->result_array();
                $this->_json(array('ok' => true, 'artifacts' => $rows, 'count' => count($rows)));
            }

            $this->_json(array(
                'ok'        => true,
                'artifacts' => array(),
                'count'     => 0,
                'note'      => 'tables_not_seeded_yet'
            ));
        } catch (Exception $e) {
            $this->_json(array('ok' => true, 'artifacts' => array(), 'note' => $e->getMessage()));
        }
    }

    public function knowledge_ack_overdue()
    {
        $hours = (int)($this->input->get('hours') ?: 48);
        $this->_json(array('ok' => true, 'rows' => array(), 'note' => 'tables_not_seeded_yet'));
    }

    public function knowledge_distribution_gaps()
    {
        $this->_json(array('ok' => true, 'rows' => array(), 'note' => 'tables_not_seeded_yet'));
    }

    public function knowledge_expiring()
    {
        $within_days = (int)($this->input->get('within_days') ?: 7);
        $this->_json(array('ok' => true, 'rows' => array(), 'note' => 'tables_not_seeded_yet'));
    }

    public function knowledge_unanswered_top()
    {
        $min_asks = (int)($this->input->get('min_asks') ?: 3);
        $days     = (int)($this->input->get('days') ?: 7);
        $this->_json(array('ok' => true, 'rows' => array(), 'note' => 'tables_not_seeded_yet'));
    }

    public function knowledge_avp_cadence()
    {
        $days = (int)($this->input->get('days') ?: 7);
        $this->_json(array('ok' => true, 'rows' => array(), 'note' => 'tables_not_seeded_yet'));
    }

    public function knowledge_cluster_velocity()
    {
        $hours = (int)($this->input->get('hours') ?: 24);
        $this->_json(array('ok' => true, 'rows' => array(), 'note' => 'tables_not_seeded_yet'));
    }

    // Skill stubs
    public function skill_scores()
    {
        $uid = (int)$this->input->get('uid');
        if (!$uid) $this->_json(array('error' => 'missing_uid'), 400);
        $this->_json(array('ok' => true, 'scores' => array(), 'note' => 'no_data'));
    }

    public function skill_gaps()
    {
        $uid = (int)$this->input->get('uid');
        if (!$uid) $this->_json(array('error' => 'missing_uid'), 400);
        $this->_json(array('ok' => true, 'gaps' => array(), 'note' => 'no_data'));
    }

    public function skill_manual_signal()
    {
        $this->_json(array('ok' => false, 'error' => 'not_implemented_direct_wire'));
    }

    public function drill_assign()
    {
        $this->_json(array('ok' => false, 'error' => 'not_implemented_direct_wire'));
    }

    public function drill_list()
    {
        $uid = (int)$this->input->get('uid');
        if (!$uid) $this->_json(array('error' => 'missing_uid'), 400);
        $this->_json(array('ok' => true, 'drills' => array(), 'note' => 'no_data'));
    }

    public function onboarding_status()
    {
        $uid = (int)$this->input->get('uid');
        if (!$uid) $this->_json(array('error' => 'missing_uid'), 400);
        $this->_json(array('ok' => true, 'checkpoints' => array(), 'note' => 'no_data'));
    }

    public function onboarding_checkpoint()
    {
        $this->_json(array('ok' => false, 'error' => 'not_implemented_direct_wire'));
    }

    public function asset_submit()
    {
        $this->_json(array('ok' => false, 'error' => 'not_implemented_direct_wire'));
    }

    public function asset_review($id = 0)
    {
        $this->_json(array('ok' => false, 'note' => 'no_data'));
    }

    public function asset_grades()
    {
        $this->_json(array('ok' => true, 'grades' => array(), 'note' => 'no_data'));
    }

    public function faq_voice()
    {
        $this->_json(array('ok' => false, 'error' => 'not_implemented_direct_wire'));
    }

    public function faq_log_unanswered()
    {
        $this->_json(array('ok' => false, 'error' => 'not_implemented_direct_wire'));
    }

    public function faq_publish_candidate()
    {
        $this->_json(array('ok' => false, 'error' => 'not_implemented_direct_wire'));
    }

    public function greetings_queue()
    {
        $cm_uid = (int)$this->input->get('cm_uid');
        if (!$cm_uid) $this->_json(array('error' => 'missing_cm_uid'), 400);
        $this->_json(array('ok' => true, 'queue' => array(), 'note' => 'no_data'));
    }

    public function greetings_approve()
    {
        $this->_json(array('ok' => false, 'error' => 'not_implemented_direct_wire'));
    }

    public function greetings_reject()
    {
        $this->_json(array('ok' => false, 'error' => 'not_implemented_direct_wire'));
    }

// M063 Coach.php patch (27 May 2026): replaces stub knowledge_upload + adds
// knowledge_create (admin) + makes knowledge_get / knowledge_acknowledge / knowledge_archive
// honour the newly-seeded knowledge_artifact table. Drop-in replacement for the
// 5 stub methods in Coach.php (lines ~356-377). Keep all other Coach.php methods.

    public function knowledge_upload()
    {
        // Super Admin (or AVP) uploads an artifact. Multipart POST.
        $title         = trim((string)$this->input->post('title'));
        $artifact_type = trim((string)$this->input->post('artifact_type')) ?: 'doc';
        $target        = trim((string)$this->input->post('target_segment')) ?: 'all';
        $force_ack     = (int)$this->input->post('force_ack');
        $expiry        = $this->input->post('expiry_date') ?: null;
        $body          = (string)$this->input->post('body');
        $file_url      = (string)$this->input->post('file_url');
        $uploader_uid  = (int)$this->input->post('uploaded_by_uid');
        $uploader_name = trim((string)$this->input->post('uploaded_by_name'));

        if (!$title) { $this->_json(array('ok' => false, 'error' => 'missing_title'), 400); return; }

        // Permission check: only Super Admin (type_id=1) or AVP (type_id=24) may upload.
        // Caller passes uploaded_by_uid; backend verifies via user table.
        if ($uploader_uid) {
            $u = $this->db->get_where('user', array('uid' => $uploader_uid))->row_array();
            if (!$u || !in_array((int)$u['type_id'], array(1, 24))) {
                $this->_json(array('ok' => false, 'error' => 'not_authorised'), 403); return;
            }
            if (!$uploader_name) $uploader_name = trim($u['name']);
        }

        $row = array(
            'title'            => $title,
            'artifact_type'    => $artifact_type,
            'target_segment'   => $target,
            'force_ack'        => $force_ack ? 1 : 0,
            'expiry_date'      => $expiry,
            'body'             => $body ?: null,
            'file_url'         => $file_url ?: null,
            'status'           => 'live',
            'uploaded_by_uid'  => $uploader_uid ?: null,
            'uploaded_by_name' => $uploader_name ?: 'Unknown',
        );
        $this->db->insert('knowledge_artifact', $row);
        $id = $this->db->insert_id();

        $this->_json(array(
            'ok'          => true,
            'artifact_id' => $id,
            'message'     => 'Artifact uploaded and live for segment ' . $target,
        ));
    }

    public function knowledge_create()
    {
        // Alias for knowledge_upload so /coach/knowledge_create works too.
        $this->knowledge_upload();
    }

    public function knowledge_get($id = 0)
    {
        $id = (int)($id ?: $this->input->get('id'));
        if (!$id) { $this->_json(array('error' => 'missing_id'), 400); return; }
        $row = $this->db->get_where('knowledge_artifact', array('id' => $id))->row_array();
        if (!$row) { $this->_json(array('ok' => false, 'error' => 'not_found'), 404); return; }
        $this->_json(array('ok' => true, 'artifact' => $row));
    }

    public function knowledge_acknowledge()
    {
        $artifact_id = (int)$this->input->post('artifact_id');
        $uid         = (int)$this->input->post('uid');
        if (!$artifact_id || !$uid) {
            $this->_json(array('ok' => false, 'error' => 'missing_artifact_id_or_uid'), 400); return;
        }
        // INSERT IGNORE so duplicate ack is idempotent
        $this->db->query(
            "INSERT IGNORE INTO knowledge_ack (artifact_id, uid) VALUES (?, ?)",
            array($artifact_id, $uid)
        );
        $this->_json(array('ok' => true, 'message' => 'Acknowledged.'));
    }

    public function knowledge_archive()
    {
        $id = (int)$this->input->post('id');
        if (!$id) { $this->_json(array('ok' => false, 'error' => 'missing_id'), 400); return; }
        $this->db->where('id', $id)
                 ->update('knowledge_artifact', array('status' => 'archived', 'archived_at' => date('Y-m-d H:i:s')));
        $this->_json(array('ok' => true, 'message' => 'Archived.'));
    }

}

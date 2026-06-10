<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Broadcast_api.php  (Phase 3 - Agent G - E6 - 2026-06-08)
 *
 * Bulk Broadcast Scaffold: create broadcast jobs targeting lead segments.
 * Reads comm_template_v2 for template validation.
 *
 * COMPLIANCE GUARD: status is ALWAYS kept as 'pending'. Never mark as sent.
 * Real sends require Meta-approved templates + opt-in compliance + a
 * controlled send worker (out of scope for this scaffold).
 *
 * Tables:
 *   broadcast_job       (id, name, template_code, segment_json, status,
 *                        created_by_uid, created_ts, total_recipients)
 *   broadcast_recipient (id, job_id, lead_id, status)
 *
 * Endpoints:
 *   POST /api/broadcast/create   Create job + resolve recipients from init_call
 *   GET  /api/broadcast/list     List jobs
 *   GET  /api/broadcast/get?id=  Job detail + recipients
 *
 * Bearer token required. 401 on missing token. ASCII output.
 */
class Broadcast_api extends CI_Controller {

    private $_known_token = '4eBaiAT7r4zu6OK3b8evjLNia1D7RGgb0qRTuLJfUSo';

    public function __construct() {
        parent::__construct();
        $this->load->database();
        header('Content-Type: application/json; charset=utf-8');
    }

    // ------------------------------------------------------------------
    // Auth
    // ------------------------------------------------------------------
    private function _bearer_ok() {
        // rimlyproof_authunify_20260609: delegate to canonical fail-closed validator.
        // Replaces malformed if/try control flow that rejected valid Bearer tokens.
        if (function_exists('authunify_ok')) {
            return authunify_ok() ? true : false;
        }
        // Fallback: direct BearerAuth resolve (still fail-closed)
        try {
            $CI =& get_instance();
            if (!isset($CI->bearerauth)) { $CI->load->library('BearerAuth'); }
            $___ba = $CI->bearerauth->resolve();
            if (!empty($___ba['ok'])) {
                if (property_exists($this, '_authed_uid')) { $this->_authed_uid = (int)$___ba['uid']; }
                return true;
            }
        } catch (Exception $e) {}
        return false;
    }

    private function _require_auth() {
        if (!$this->_bearer_ok()) {
            http_response_code(401);
            echo json_encode(['ok' => false, 'error' => 'Unauthorized']);
            exit;
        }
    }

    private function _json($data, $code = 200) {
        http_response_code($code);
        echo json_encode($data, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
        exit;
    }

    // ------------------------------------------------------------------
    // Schema bootstrap
    // ------------------------------------------------------------------
    private function _ensure_tables() {
        $this->db->query("
            CREATE TABLE IF NOT EXISTS broadcast_job (
                id               INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
                name             VARCHAR(200) NOT NULL,
                template_code    VARCHAR(80)  NOT NULL COMMENT 'references comm_template_v2.template_key',
                segment_json     TEXT         NOT NULL DEFAULT '{}',
                status           VARCHAR(30)  NOT NULL DEFAULT 'pending'
                                 COMMENT 'ALWAYS pending - real sends require a controlled send worker',
                created_by_uid   INT UNSIGNED NOT NULL DEFAULT 0,
                created_ts       DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
                total_recipients INT UNSIGNED NOT NULL DEFAULT 0,
                INDEX idx_status (status),
                INDEX idx_created (created_ts)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
        ");
        $this->db->query("
            CREATE TABLE IF NOT EXISTS broadcast_recipient (
                id        INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
                job_id    INT UNSIGNED NOT NULL,
                lead_id   INT UNSIGNED NOT NULL,
                status    VARCHAR(30)  NOT NULL DEFAULT 'pending',
                INDEX idx_job    (job_id),
                INDEX idx_lead   (lead_id),
                INDEX idx_status (status)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
        ");
    }

    // ------------------------------------------------------------------
    // Segment resolver: resolve lead IDs from init_call by segment_json
    // Supported filters: cstatus (array or single), mainbd, cluster_id
    // ------------------------------------------------------------------
    private function _resolve_segment($seg) {
        $where  = ['1=1'];
        $params = [];

        if (!empty($seg['cstatus'])) {
            $cs = is_array($seg['cstatus']) ? $seg['cstatus'] : [(int)$seg['cstatus']];
            $cs = array_map('intval', $cs);
            if ($cs) {
                $ph     = implode(',', array_fill(0, count($cs), '?'));
                $where[]  = "ic.cstatus IN ($ph)";
                $params   = array_merge($params, $cs);
            }
        }
        if (!empty($seg['mainbd'])) {
            $where[]  = 'ic.mainbd = ?';
            $params[] = (int)$seg['mainbd'];
        }
        if (!empty($seg['cluster_id'])) {
            $where[]  = 'ic.cluster_id = ?';
            $params[] = (int)$seg['cluster_id'];
        }

        $sql = "SELECT ic.id AS lead_id FROM init_call ic WHERE "
             . implode(' AND ', $where)
             . " LIMIT 5000";

        $rows = $this->db->query($sql, $params)->result_array();
        return array_column($rows, 'lead_id');
    }

    // ------------------------------------------------------------------
    // POST /api/broadcast/create
    // Body: { name, template_code, segment_json, created_by_uid? }
    // ------------------------------------------------------------------
    public function create() {
        $this->_require_auth();
        $this->_ensure_tables();

        $body = json_decode(file_get_contents('php://input'), true) ?: [];
        $name          = isset($body['name']) ? trim($body['name']) : '';
        $template_code = isset($body['template_code']) ? trim($body['template_code']) : '';
        $seg_raw       = isset($body['segment_json']) ? $body['segment_json'] : [];
        $by_uid        = isset($body['created_by_uid']) ? (int)$body['created_by_uid'] : 0;

        if (!$name || !$template_code) {
            $this->_json(['ok' => false, 'error' => 'name and template_code required'], 400);
        }

        // Validate template exists in comm_template_v2
        $tpl = $this->db->query(
            "SELECT id, template_key, event_type, is_active FROM comm_template_v2 WHERE template_key = ? LIMIT 1",
            [$template_code]
        )->row_array();

        $template_note = null;
        if (!$tpl) {
            $template_note = 'template_code not found in comm_template_v2 - job created but template validation pending';
        } elseif (!$tpl['is_active']) {
            $template_note = 'template exists but is not active in comm_template_v2';
        }

        $seg = is_array($seg_raw) ? $seg_raw : [];
        $seg_json = json_encode($seg);

        // Insert job with status=pending (COMPLIANCE: never change to sent)
        $this->db->query(
            "INSERT INTO broadcast_job (name, template_code, segment_json, status, created_by_uid, created_ts, total_recipients)
             VALUES (?, ?, ?, 'pending', ?, NOW(), 0)",
            [$name, $template_code, $seg_json, $by_uid]
        );
        $job_id = $this->db->insert_id();

        // Resolve recipients
        $lead_ids = $this->_resolve_segment($seg);
        $count    = count($lead_ids);

        // Bulk insert recipients
        if ($count > 0) {
            $chunks = array_chunk($lead_ids, 500);
            foreach ($chunks as $chunk) {
                $vals = [];
                foreach ($chunk as $lid) {
                    $vals[] = '(' . (int)$job_id . ',' . (int)$lid . ",'pending')";
                }
                $this->db->query(
                    "INSERT INTO broadcast_recipient (job_id, lead_id, status) VALUES "
                    . implode(',', $vals)
                );
            }
            // Update total_recipients
            $this->db->query(
                "UPDATE broadcast_job SET total_recipients = ? WHERE id = ?",
                [$count, $job_id]
            );
        }

        $resp = [
            'ok'               => true,
            'job_id'           => $job_id,
            'status'           => 'pending',
            'total_recipients' => $count,
            'compliance_note'  => 'Status kept as pending. Real sends require Meta-approved templates, opt-in compliance, and a controlled send worker.',
        ];
        if ($template_note) $resp['template_note'] = $template_note;

        $this->_json($resp);
    }

    // ------------------------------------------------------------------
    // GET /api/broadcast/list
    // ------------------------------------------------------------------
    public function list_index() {
        $this->_require_auth();
        $this->_ensure_tables();

        $rows = $this->db->query(
            "SELECT id, name, template_code, status, created_by_uid, created_ts, total_recipients
             FROM broadcast_job
             ORDER BY id DESC
             LIMIT 100"
        )->result_array();

        if (empty($rows)) {
            $this->_json(['ok' => true, 'empty' => true, 'jobs' => []]);
        }
        $this->_json(['ok' => true, 'jobs' => $rows, 'count' => count($rows)]);
    }

    // ------------------------------------------------------------------
    // GET /api/broadcast/get?id=
    // ------------------------------------------------------------------
    public function get() {
        $this->_require_auth();
        $this->_ensure_tables();

        $id = (int)$this->input->get('id');
        if (!$id) {
            $this->_json(['ok' => false, 'error' => 'id required'], 400);
        }

        $job = $this->db->query(
            "SELECT * FROM broadcast_job WHERE id = ? LIMIT 1",
            [$id]
        )->row_array();

        if (!$job) {
            $this->_json(['ok' => true, 'empty' => true, 'job' => null]);
        }

        $job['segment_json'] = json_decode($job['segment_json'], true) ?: [];

        // Recipient sample (first 50)
        $recips = $this->db->query(
            "SELECT br.id, br.lead_id, br.status, cm.compname
             FROM broadcast_recipient br
             LEFT JOIN init_call ic ON ic.id = br.lead_id
             LEFT JOIN company_master cm ON cm.id = ic.cmpid_id
             WHERE br.job_id = ?
             ORDER BY br.id ASC
             LIMIT 50",
            [$id]
        )->result_array();

        $this->_json([
            'ok'         => true,
            'job'        => $job,
            'recipients' => $recips,
            'recipient_sample_limit' => 50,
            'total_recipients' => (int)$job['total_recipients'],
        ]);
    }
}

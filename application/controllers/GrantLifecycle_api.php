<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * GrantLifecycle_api.php  (Phase 3 - Agent G - G6 - 2026-06-08)
 *
 * Grant/Sanction Lifecycle: tracks grant applications from Applied to Closed.
 *
 * Tables:
 *   grant_stage_master  (id, code, label, sort_order) - seeded on first use
 *   grant_lifecycle     (id, company_id, tender_id NULL, current_stage_id,
 *                        amount_rs, last_updated, notes)
 *
 * Stages (seeded): Applied, Under-Review, Sanctioned, Disbursed, Utilized, Closed
 * Links to company_master. tender_id optionally links to Phase 1 tender table.
 *
 * Endpoints:
 *   GET  /api/grant/stages              List all stage masters
 *   GET  /api/grant/list?company_id=    List grants for a company
 *   POST /api/grant/save                Create or update a grant lifecycle entry
 *
 * Bearer token required. 401 on missing token. ASCII output; Rs for amounts.
 */
class GrantLifecycle_api extends CI_Controller {

    private $_known_token = '4eBaiAT7r4zu6OK3b8evjLNia1D7RGgb0qRTuLJfUSo';

    // Canonical stage seeds
    private $_stage_seeds = [
        ['code' => 'applied',     'label' => 'Applied',      'sort_order' => 10],
        ['code' => 'under_review','label' => 'Under-Review', 'sort_order' => 20],
        ['code' => 'sanctioned',  'label' => 'Sanctioned',   'sort_order' => 30],
        ['code' => 'disbursed',   'label' => 'Disbursed',    'sort_order' => 40],
        ['code' => 'utilized',    'label' => 'Utilized',     'sort_order' => 50],
        ['code' => 'closed',      'label' => 'Closed',       'sort_order' => 60],
    ];

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
    // Schema bootstrap + seed
    // ------------------------------------------------------------------
    private function _ensure_tables() {
        $this->db->query("
            CREATE TABLE IF NOT EXISTS grant_stage_master (
                id         INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
                code       VARCHAR(40)  NOT NULL,
                label      VARCHAR(80)  NOT NULL,
                sort_order SMALLINT     NOT NULL DEFAULT 0,
                UNIQUE KEY uk_code (code)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
        ");

        $this->db->query("
            CREATE TABLE IF NOT EXISTS grant_lifecycle (
                id               INT UNSIGNED   NOT NULL AUTO_INCREMENT PRIMARY KEY,
                company_id       INT UNSIGNED   NOT NULL COMMENT 'FK company_master.id',
                tender_id        INT UNSIGNED   NULL     COMMENT 'FK tender.id (Phase 1, optional)',
                current_stage_id INT UNSIGNED   NOT NULL COMMENT 'FK grant_stage_master.id',
                amount_rs        DECIMAL(15,2)  NOT NULL DEFAULT 0.00,
                last_updated     DATETIME       NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                notes            TEXT           NULL,
                created_ts       DATETIME       NOT NULL DEFAULT CURRENT_TIMESTAMP,
                INDEX idx_company (company_id),
                INDEX idx_stage   (current_stage_id),
                INDEX idx_tender  (tender_id)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
        ");

        // Seed stages if not present
        foreach ($this->_stage_seeds as $s) {
            $this->db->query(
                "INSERT IGNORE INTO grant_stage_master (code, label, sort_order) VALUES (?, ?, ?)",
                [$s['code'], $s['label'], $s['sort_order']]
            );
        }
    }

    // ------------------------------------------------------------------
    // GET /api/grant/stages
    // ------------------------------------------------------------------
    public function stages() {
        $this->_require_auth();
        $this->_ensure_tables();

        $rows = $this->db->query(
            "SELECT id, code, label, sort_order FROM grant_stage_master ORDER BY sort_order ASC"
        )->result_array();

        $this->_json(['ok' => true, 'stages' => $rows, 'count' => count($rows)]);
    }

    // ------------------------------------------------------------------
    // GET /api/grant/list?company_id=
    // ------------------------------------------------------------------
    public function list_index() {
        $this->_require_auth();
        $this->_ensure_tables();

        $company_id = (int)$this->input->get('company_id');
        if (!$company_id) {
            $this->_json(['ok' => false, 'error' => 'company_id required'], 400);
        }

        // Verify company exists
        $co = $this->db->query(
            "SELECT id, compname FROM company_master WHERE id = ? LIMIT 1",
            [$company_id]
        )->row_array();

        if (!$co) {
            $this->_json([
                'ok'         => true,
                'empty'      => true,
                'company_id' => $company_id,
                'grants'     => [],
                'note'       => 'company_id not found in company_master',
            ]);
        }

        $rows = $this->db->query(
            "SELECT gl.id, gl.company_id, cm.compname, gl.tender_id,
                    gl.current_stage_id, gsm.code AS stage_code, gsm.label AS stage_label,
                    gsm.sort_order AS stage_sort,
                    gl.amount_rs, gl.last_updated, gl.notes, gl.created_ts
             FROM grant_lifecycle gl
             JOIN company_master cm   ON cm.id  = gl.company_id
             JOIN grant_stage_master gsm ON gsm.id = gl.current_stage_id
             WHERE gl.company_id = ?
             ORDER BY gl.id DESC",
            [$company_id]
        )->result_array();

        // Format amount as Rs string in output while keeping numeric for processing
        foreach ($rows as &$row) {
            $row['amount_rs']         = (float)$row['amount_rs'];
            $row['amount_display']    = 'Rs ' . number_format((float)$row['amount_rs'], 2);
            $row['stage_sort']        = (int)$row['stage_sort'];
            $row['current_stage_id']  = (int)$row['current_stage_id'];
        }
        unset($row);

        if (empty($rows)) {
            $this->_json([
                'ok'          => true,
                'empty'       => true,
                'company_id'  => $company_id,
                'compname'    => $co['compname'],
                'grants'      => [],
            ]);
        }

        $this->_json([
            'ok'         => true,
            'company_id' => $company_id,
            'compname'   => $co['compname'],
            'grants'     => $rows,
            'count'      => count($rows),
        ]);
    }

    // ------------------------------------------------------------------
    // POST /api/grant/save
    // Body: { id?, company_id, tender_id?, current_stage_id OR stage_code,
    //         amount_rs, notes? }
    // Creates new grant entry or advances stage / updates existing.
    // ------------------------------------------------------------------
    public function save() {
        $this->_require_auth();
        $this->_ensure_tables();

        $body = json_decode(file_get_contents('php://input'), true) ?: [];
        $id          = isset($body['id'])          ? (int)$body['id']          : 0;
        $company_id  = isset($body['company_id'])  ? (int)$body['company_id']  : 0;
        $tender_id   = isset($body['tender_id'])   ? (int)$body['tender_id']   : null;
        $amount_rs   = isset($body['amount_rs'])   ? (float)$body['amount_rs'] : 0.0;
        $notes       = isset($body['notes'])       ? trim($body['notes'])       : null;

        // Resolve stage_id: accept either stage_code or current_stage_id
        $stage_id = 0;
        if (!empty($body['stage_code'])) {
            $sm = $this->db->query(
                "SELECT id FROM grant_stage_master WHERE code = ? LIMIT 1",
                [trim($body['stage_code'])]
            )->row_array();
            if ($sm) $stage_id = (int)$sm['id'];
        }
        if (!$stage_id && !empty($body['current_stage_id'])) {
            $stage_id = (int)$body['current_stage_id'];
        }

        if (!$company_id) {
            $this->_json(['ok' => false, 'error' => 'company_id required'], 400);
        }
        if (!$stage_id) {
            $this->_json(['ok' => false, 'error' => 'current_stage_id or stage_code required'], 400);
        }

        // Verify company_id
        $co = $this->db->query(
            "SELECT id FROM company_master WHERE id = ? LIMIT 1",
            [$company_id]
        )->row_array();
        if (!$co) {
            $this->_json(['ok' => false, 'error' => 'company_id not found'], 400);
        }

        // Verify stage_id
        $sm = $this->db->query(
            "SELECT id, code, label FROM grant_stage_master WHERE id = ? LIMIT 1",
            [$stage_id]
        )->row_array();
        if (!$sm) {
            $this->_json(['ok' => false, 'error' => 'stage_id not found in grant_stage_master'], 400);
        }

        // Optional: verify tender_id if provided
        if ($tender_id) {
            $t = $this->db->query(
                "SELECT id FROM tender WHERE id = ? LIMIT 1",
                [$tender_id]
            )->row_array();
            if (!$t) {
                $tender_id = null; // Clear invalid tender_id silently with note
            }
        }

        if ($id) {
            // Update existing
            $this->db->query(
                "UPDATE grant_lifecycle
                 SET current_stage_id = ?, amount_rs = ?, notes = ?, tender_id = ?, last_updated = NOW()
                 WHERE id = ? AND company_id = ?",
                [$stage_id, $amount_rs, $notes, $tender_id ?: null, $id, $company_id]
            );
            $affected = $this->db->affected_rows();
            if (!$affected) {
                $this->_json(['ok' => false, 'error' => 'Grant lifecycle record not found or company_id mismatch'], 404);
            }
        } else {
            // Create new
            $this->db->query(
                "INSERT INTO grant_lifecycle (company_id, tender_id, current_stage_id, amount_rs, notes, created_ts, last_updated)
                 VALUES (?, ?, ?, ?, ?, NOW(), NOW())",
                [$company_id, $tender_id ?: null, $stage_id, $amount_rs, $notes]
            );
            $id = $this->db->insert_id();
        }

        $this->_json([
            'ok'              => true,
            'grant_id'        => $id,
            'company_id'      => $company_id,
            'current_stage'   => $sm['label'],
            'stage_code'      => $sm['code'],
            'amount_rs'       => $amount_rs,
            'amount_display'  => 'Rs ' . number_format($amount_rs, 2),
        ]);
    }
}

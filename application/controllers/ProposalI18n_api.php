<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * ProposalI18n_api.php  (Phase 3 - Agent G - G5 - 2026-06-08)
 *
 * Multi-Language Proposal Fields: store Hindi/Marathi/English translations
 * for any entity's proposal fields.
 *
 * Table: proposal_i18n
 *   id, entity_type, entity_id, lang ENUM('en','hi','mr'),
 *   field_key, field_value TEXT  (utf8mb4)
 *
 * Endpoints:
 *   GET  /api/i18n/get?entity_type=&entity_id=&lang=   Fetch fields for entity+lang
 *   POST /api/i18n/set                                  Upsert a field value
 *
 * Notes:
 *   - Table and columns are utf8mb4 (handles Devanagari).
 *   - API response structure is ASCII-safe; field_value content may be Unicode
 *     (that is data, not system output - allowed per coordination rules).
 *   - Bearer token required. 401 on missing token.
 */
class ProposalI18n_api extends CI_Controller {

    private $_known_token = '4eBaiAT7r4zu6OK3b8evjLNia1D7RGgb0qRTuLJfUSo';
    private $_valid_langs = ['en', 'hi', 'mr'];

    public function __construct() {
        parent::__construct();
        $this->load->database();
        header('Content-Type: application/json; charset=utf-8');
    }

    // ------------------------------------------------------------------
    // Auth
    // ------------------------------------------------------------------
    private function _bearer_ok() {
        $hdr = isset($_SERVER['HTTP_AUTHORIZATION']) ? $_SERVER['HTTP_AUTHORIZATION'] : '';
        if (!$hdr && function_exists('apache_request_headers')) {
            $h = apache_request_headers();
            if (isset($h['Authorization'])) $hdr = $h['Authorization'];
        }
        if (!$hdr || stripos($hdr, 'Bearer ') !== 0) return false;
        $token = trim(substr($hdr, 7));
        $env   = getenv('STEM_DIGEST_TOKEN');
        if ($env && hash_equals($env, $token)) return true;
        if (hash_equals($this->_known_token, $token)) return true;
        // rimlyproof_bearerdelegate_20260608: also accept per-user login token via shared BearerAuth library (additive)
        try {
            $CI =& get_instance();
            if (!isset($CI->bearerauth)) { $CI->load->library('BearerAuth'); }
            $___ba = $CI->bearerauth->resolve();
            if (!empty($___ba['ok']) && !empty($___ba['uid'])) {
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
        // JSON_UNESCAPED_UNICODE: field_value (Devanagari) passes through as data
        echo json_encode($data, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
        exit;
    }

    // ------------------------------------------------------------------
    // Schema bootstrap
    // ------------------------------------------------------------------
    private function _ensure_table() {
        // Ensure DB connection is using utf8mb4 for this session
        $this->db->query("SET NAMES utf8mb4");

        $this->db->query("
            CREATE TABLE IF NOT EXISTS proposal_i18n (
                id          INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
                entity_type VARCHAR(60)  NOT NULL COMMENT 'e.g. lead, company, proposal',
                entity_id   INT UNSIGNED NOT NULL,
                lang        ENUM('en','hi','mr') NOT NULL DEFAULT 'en',
                field_key   VARCHAR(100) NOT NULL COMMENT 'e.g. proposal_title, summary, objective',
                field_value TEXT         NOT NULL,
                updated_ts  DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                UNIQUE KEY uk_entity_lang_field (entity_type(40), entity_id, lang, field_key(80)),
                INDEX idx_entity (entity_type(40), entity_id),
                INDEX idx_lang   (lang)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ");
    }

    // ------------------------------------------------------------------
    // GET /api/i18n/get?entity_type=&entity_id=&lang=
    // ------------------------------------------------------------------
    public function get() {
        $this->_require_auth();
        $this->_ensure_table();

        $entity_type = trim($this->input->get('entity_type') ?: '');
        $entity_id   = (int)$this->input->get('entity_id');
        $lang        = trim($this->input->get('lang') ?: 'en');

        if (!$entity_type || !$entity_id) {
            $this->_json(['ok' => false, 'error' => 'entity_type and entity_id required'], 400);
        }
        if (!in_array($lang, $this->_valid_langs, true)) {
            $this->_json(['ok' => false, 'error' => 'lang must be one of: en, hi, mr'], 400);
        }

        $rows = $this->db->query(
            "SELECT field_key, field_value, updated_ts
             FROM proposal_i18n
             WHERE entity_type = ? AND entity_id = ? AND lang = ?
             ORDER BY field_key ASC",
            [$entity_type, $entity_id, $lang]
        )->result_array();

        if (empty($rows)) {
            $this->_json([
                'ok'          => true,
                'empty'       => true,
                'entity_type' => $entity_type,
                'entity_id'   => $entity_id,
                'lang'        => $lang,
                'fields'      => [],
            ]);
        }

        // Build key->value map as well as array form
        $fields_map = [];
        foreach ($rows as $row) {
            $fields_map[$row['field_key']] = $row['field_value'];
        }

        $this->_json([
            'ok'          => true,
            'entity_type' => $entity_type,
            'entity_id'   => $entity_id,
            'lang'        => $lang,
            'fields'      => $fields_map,
            'rows'        => $rows,
            'count'       => count($rows),
        ]);
    }

    // ------------------------------------------------------------------
    // POST /api/i18n/set
    // Body: { entity_type, entity_id, lang, field_key, field_value }
    // Upsert: insert or update on duplicate key
    // ------------------------------------------------------------------
    public function set() {
        $this->_require_auth();
        $this->_ensure_table();

        $body = json_decode(file_get_contents('php://input'), true) ?: [];
        $entity_type = isset($body['entity_type']) ? trim($body['entity_type']) : '';
        $entity_id   = isset($body['entity_id'])   ? (int)$body['entity_id']   : 0;
        $lang        = isset($body['lang'])         ? trim($body['lang'])        : 'en';
        $field_key   = isset($body['field_key'])   ? trim($body['field_key'])   : '';
        $field_value = isset($body['field_value']) ? $body['field_value']       : '';

        if (!$entity_type || !$entity_id || !$field_key) {
            $this->_json(['ok' => false, 'error' => 'entity_type, entity_id, and field_key required'], 400);
        }
        if (!in_array($lang, $this->_valid_langs, true)) {
            $this->_json(['ok' => false, 'error' => 'lang must be one of: en, hi, mr'], 400);
        }

        $this->db->query(
            "INSERT INTO proposal_i18n (entity_type, entity_id, lang, field_key, field_value, updated_ts)
             VALUES (?, ?, ?, ?, ?, NOW())
             ON DUPLICATE KEY UPDATE field_value = VALUES(field_value), updated_ts = NOW()",
            [$entity_type, $entity_id, $lang, $field_key, (string)$field_value]
        );

        $this->_json([
            'ok'          => true,
            'entity_type' => $entity_type,
            'entity_id'   => $entity_id,
            'lang'        => $lang,
            'field_key'   => $field_key,
            'saved'       => true,
        ]);
    }
}

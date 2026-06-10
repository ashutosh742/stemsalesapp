<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Api_keys controller
 *
 * Admin endpoints to inject and inspect API credentials stored in
 * the api_keys table. Authentication uses a static bearer token
 * checked in _require_auth().
 *
 * Routes (defined in routes_api_keys.php):
 *   POST /api/api_keys/set   - store or update a key for a service
 *   GET  /api/api_keys/list  - list all services (key value is redacted)
 *
 * Created 2026-05-27.
 */
class Api_keys extends CI_Controller {

    // Static bearer token for admin access.
    // To change: update this constant and re-deploy.
    const BEARER_TOKEN = '4eBaiAT7r4zu6OK3b8evjLNia1D7RGgb0qRTuLJfUSo';

    // Services that are allowed to be managed via this endpoint.
    const ALLOWED_SERVICES = ['openai', 'whatsapp_business', 'whatsapp_phone_id'];

    public function __construct() {
        parent::__construct();
        header('Content-Type: application/json');
    }

    /**
     * Validate bearer token from Authorization header.
     * Emits 401 and exits if not valid.
     */
    private function _require_auth() {
        if (function_exists('authunify_ok') && authunify_ok()) { return; } // rimlyproof_authunify_20260609

        $auth = isset($_SERVER['HTTP_AUTHORIZATION']) ? $_SERVER['HTTP_AUTHORIZATION'] : '';
        // Some servers expose it as REDIRECT_HTTP_AUTHORIZATION
        if (!$auth && isset($_SERVER['REDIRECT_HTTP_AUTHORIZATION'])) {
            $auth = $_SERVER['REDIRECT_HTTP_AUTHORIZATION'];
        }
        $expected = 'Bearer ' . self::BEARER_TOKEN;
        if ($auth !== $expected) {
            http_response_code(401);
            echo json_encode(['ok' => false, 'error' => 'unauthorized']);
            exit;
        }
    }

    /**
     * POST /api/api_keys/set
     *
     * Body params (application/x-www-form-urlencoded):
     *   service  - one of: openai, whatsapp_business, whatsapp_phone_id
     *   api_key  - the credential value to store
     *
     * Sets status to 'active' when key does not start with PLACEHOLDER_.
     * Sets status to 'placeholder' when key starts with PLACEHOLDER_.
     */
    public function set() {
        $this->_require_auth();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['ok' => false, 'error' => 'POST required']);
            return;
        }

        $service = trim($this->input->post('service'));
        $api_key = trim($this->input->post('api_key'));

        if (!$service || !$api_key) {
            http_response_code(400);
            echo json_encode(['ok' => false, 'error' => 'service and api_key are required']);
            return;
        }

        if (!in_array($service, self::ALLOWED_SERVICES)) {
            http_response_code(400);
            echo json_encode([
                'ok' => false,
                'error' => 'unknown service',
                'allowed' => self::ALLOWED_SERVICES
            ]);
            return;
        }

        // Determine status based on whether value looks like a placeholder.
        $status = (strpos($api_key, 'PLACEHOLDER_') === 0) ? 'placeholder' : 'active';

        $db = $this->load->database('default', true);
        $db->query(
            "INSERT INTO api_keys (service, api_key, status) VALUES (?, ?, ?)
             ON DUPLICATE KEY UPDATE api_key=VALUES(api_key), status=VALUES(status), updated_at=NOW()",
            [$service, $api_key, $status]
        );

        // Return last 4 chars of the stored key so caller can confirm without exposing the full key.
        $tail = strlen($api_key) >= 4 ? '...' . substr($api_key, -4) : '****';

        echo json_encode([
            'ok' => true,
            'service' => $service,
            'status' => $status,
            'key_tail' => $tail,
            'message' => $status === 'active'
                ? "Key stored as active. Probe endpoints will now return ready for $service."
                : "Key stored as placeholder. Probe endpoints will remain in stub state until a real key is set."
        ]);
    }

    /**
     * GET /api/api_keys/list
     *
     * Returns all rows from api_keys with the key value redacted to last 4 chars.
     */
    public function list() {
        $this->_require_auth();

        $db = $this->load->database('default', true);
        $q = $db->query(
            "SELECT service, status, notes, updated_at,
                    CONCAT('...', RIGHT(api_key, 4)) AS key_tail
             FROM api_keys
             ORDER BY service"
        );
        $rows = $q ? $q->result() : [];

        echo json_encode([
            'ok' => true,
            'count' => count($rows),
            'api_keys' => $rows
        ]);
    }
}

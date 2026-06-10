<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * ProposalDraft_api.php  (Phase 3 - Agent G - H6 - 2026-06-08)
 *
 * Proposal-Draft Assist: uses OpenAI to generate a short CSR/govt proposal
 * blurb for a company + context, using the company's profile from company_master.
 *
 * Key from: application/config/openai.php -> $config['openai_api_key']
 * Model:     $config['openai_model'] (default gpt-4o-mini)
 *
 * If key is empty/missing: returns {"ok":true,"draft":null,"note":"openai key pending - wire when key provided"}
 * NEVER 500. NEVER block. Defensive timeout + try/catch around OpenAI call.
 *
 * Endpoint:
 *   POST /api/proposal/draft
 *   Body: { company_id, context }
 *   Returns: { ok, draft, company, note? }
 *
 * Bearer token required. 401 on missing token. ASCII structural output.
 * (Draft text content from OpenAI may contain unicode - that is data, allowed.)
 */
class ProposalDraft_api extends CI_Controller {

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
        echo json_encode($data, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
        exit;
    }

    // ------------------------------------------------------------------
    // POST /api/proposal/draft
    // ------------------------------------------------------------------
    public function draft() {
        $this->_require_auth();

        $body       = json_decode(file_get_contents('php://input'), true) ?: [];
        // Accept company_id OR lead_id (init_call.id / cid_id) - resolve to company_master.id
        $company_id = isset($body['company_id']) ? (int)$body['company_id'] : 0;
        if (!$company_id && isset($body['lead_id'])) {
            $cid_id = (int)$body['lead_id'];
            if ($cid_id > 0) {
                $ic = $this->db->query("SELECT cmpid_id FROM init_call WHERE id = ? LIMIT 1", [$cid_id])->row_array();
                if ($ic) $company_id = (int)$ic['cmpid_id'];
            }
        }
        $context    = isset($body['context'])    ? trim($body['context'])    : '';

        if (!$company_id) {
            $this->_json(['ok' => false, 'error' => 'company_id required'], 400);
        }

        // Fetch company profile
        $company = $this->db->query(
            "SELECT cm.id, cm.compname, cm.state, cm.district,
                    ptm.display_name AS partner_type
             FROM company_master cm
             LEFT JOIN partner_type_master ptm ON ptm.id = cm.partnerType_id
             WHERE cm.id = ? LIMIT 1",
            [$company_id]
        )->row_array();

        if (!$company) {
            $this->_json(['ok' => false, 'error' => 'company_id not found'], 404);
        }

        // ------------------------------------------------------------------
        // Load OpenAI config
        // ------------------------------------------------------------------
        $openai_key   = '';
        $openai_model = 'gpt-4o-mini';
        $openai_base  = 'https://api.openai.com/v1/';
        $openai_to    = 30; // seconds

        try {
            $cfg_path = APPPATH . 'config/openai.php';
            if (file_exists($cfg_path)) {
                $config = [];
                include $cfg_path;
                if (!empty($config['openai_api_key'])) {
                    $openai_key = trim($config['openai_api_key']);
                }
                if (!empty($config['openai_model'])) {
                    $openai_model = $config['openai_model'];
                }
                if (!empty($config['openai_base_url'])) {
                    $openai_base = rtrim($config['openai_base_url'], '/') . '/';
                }
                if (!empty($config['openai_timeout'])) {
                    $openai_to = (int)$config['openai_timeout'];
                    if ($openai_to > 30) $openai_to = 30; // cap for API response time
                }
            }
        } catch (Throwable $ex) {
            log_message('error', 'ProposalDraft: config load error: ' . $ex->getMessage());
        }

        // Graceful no-key fallback
        if (empty($openai_key)) {
            $this->_json([
                'ok'      => true,
                'draft'   => null,
                'company' => $company['compname'],
                'note'    => 'openai key pending - wire when key provided',
            ]);
        }

        // ------------------------------------------------------------------
        // Build prompt
        // ------------------------------------------------------------------
        $company_line = $company['compname'];
        if ($company['state'])        $company_line .= ', ' . $company['state'];
        if ($company['district'])     $company_line .= ' (' . $company['district'] . ')';
        if ($company['partner_type']) $company_line .= ' [' . $company['partner_type'] . ']';

        $prompt = "You are a professional proposal writer for an Indian social impact CRM. "
                . "Write a concise 3-4 sentence CSR or government grant proposal blurb for the following organization. "
                . "Be specific, professional, and avoid generic filler. "
                . "Output only the proposal text, no headings or labels.\n\n"
                . "Organization: " . $company_line . "\n"
                . "Context/Program: " . ($context ?: 'CSR/community development initiative') . "\n";

        // ------------------------------------------------------------------
        // Call OpenAI
        // ------------------------------------------------------------------
        $draft      = null;
        $error_note = null;

        try {
            $payload = json_encode([
                'model'       => $openai_model,
                'messages'    => [
                    ['role' => 'system', 'content' => 'You write concise, professional Indian CSR and government grant proposal blurbs.'],
                    ['role' => 'user',   'content' => $prompt],
                ],
                'max_tokens'  => 300,
                'temperature' => 0.4,
            ]);

            $ch = curl_init($openai_base . 'chat/completions');
            curl_setopt_array($ch, [
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_POST           => true,
                CURLOPT_POSTFIELDS     => $payload,
                CURLOPT_TIMEOUT        => $openai_to,
                CURLOPT_CONNECTTIMEOUT => 10,
                CURLOPT_HTTPHEADER     => [
                    'Content-Type: application/json',
                    'Authorization: Bearer ' . $openai_key,
                ],
                CURLOPT_SSL_VERIFYPEER => true,
            ]);

            $resp     = curl_exec($ch);
            $curl_err = curl_error($ch);
            $http_code= curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);

            if ($curl_err) {
                $error_note = 'OpenAI request error: ' . $curl_err;
                log_message('error', 'ProposalDraft: curl error: ' . $curl_err);
            } elseif ($http_code !== 200) {
                $error_note = 'OpenAI returned HTTP ' . $http_code;
                log_message('error', 'ProposalDraft: OpenAI HTTP ' . $http_code . ' body: ' . substr($resp, 0, 300));
            } else {
                $parsed = json_decode($resp, true);
                if (isset($parsed['choices'][0]['message']['content'])) {
                    $draft = trim($parsed['choices'][0]['message']['content']);
                } else {
                    $error_note = 'OpenAI response parse error';
                    log_message('error', 'ProposalDraft: unexpected response: ' . substr($resp, 0, 300));
                }
            }
        } catch (Throwable $ex) {
            $error_note = 'OpenAI call exception: ' . $ex->getMessage();
            log_message('error', 'ProposalDraft: exception: ' . $ex->getMessage());
        }

        $result = [
            'ok'      => true,
            'draft'   => $draft,
            'company' => $company['compname'],
        ];
        if ($error_note) {
            $result['note']  = $error_note;
            $result['draft'] = null;
        }

        $this->_json($result);
    }
}

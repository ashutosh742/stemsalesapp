<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * ChatAI_model with 1-hour TTL response cache.
 *
 * Drop-in replacement for the original ChatAI_model. Same public surface:
 *   call_chatgpt_api($message, $previousMessages)
 *   generate_fallback_response($message)
 *
 * All 22 production AIAgents in application/models/AIAgents/ funnel through
 * call_chatgpt_api, so this single wrapper applies a cache layer across the
 * entire analyst surface.
 *
 * Behaviour:
 *   1. Build cache key = sha1(message . json_encode(previousMessages))
 *   2. Look up chatai_cache where cache_key matches AND created_at within last 3600s
 *   3. On hit: increment hit_count, update last_hit_at, return cached response
 *   4. On miss: call OpenAI exactly as the original, then write the row
 *   5. On API failure: return generate_fallback_response (do not cache fallbacks)
 *
 * Configuration in application/config/config.php (optional):
 *   $config['chatai_cache_enabled'] = TRUE;       // master switch
 *   $config['chatai_cache_ttl_seconds'] = 3600;   // default 1 hour
 *   $config['chatai_cache_log_misses'] = FALSE;   // optional verbose log
 *
 * Migration: stem_chatai_cache_migration.sql creates the chatai_cache table.
 *
 * Author: STEM Learning ops (parallel to production, staging first)
 * Date: 2026-05-19
 */
class ChatAI_model extends CI_Model {

    /** @var bool runtime flag, defaults to TRUE if config missing */
    private $cache_enabled = TRUE;

    /** @var int seconds, defaults to 3600 if config missing */
    private $cache_ttl = 3600;

    /** @var bool optional verbose miss logging */
    private $log_misses = FALSE;

    public function __construct()
    {
        parent::__construct();
        $this->load->model('Menu_model');
        $this->load->database();

        // Optional overrides from config; safe defaults if unset.
        $cfg_enabled = $this->config->item('chatai_cache_enabled');
        if ($cfg_enabled !== NULL && $cfg_enabled !== '') {
            $this->cache_enabled = (bool) $cfg_enabled;
        }
        $cfg_ttl = $this->config->item('chatai_cache_ttl_seconds');
        if (is_numeric($cfg_ttl) && (int) $cfg_ttl > 0) {
            $this->cache_ttl = (int) $cfg_ttl;
        }
        $cfg_log = $this->config->item('chatai_cache_log_misses');
        if ($cfg_log !== NULL && $cfg_log !== '') {
            $this->log_misses = (bool) $cfg_log;
        }
    }

    // *********************************************************************************************************************************

    /**
     * Cached entry point. Identical signature and return contract as the
     * original ChatAI_model::call_chatgpt_api.
     *
     * @param string $message
     * @param array  $previousMessages
     * @return string
     */
    public function call_chatgpt_api($message, $previousMessages)
    {
        // Cache disabled at runtime: behave exactly like the original.
        if (!$this->cache_enabled) {
            return $this->_call_openai($message, $previousMessages);
        }

        $cache_key = $this->_build_cache_key($message, $previousMessages);

        // 1. Try cache hit within TTL.
        $cached = $this->_lookup_cache($cache_key);
        if ($cached !== NULL) {
            $this->_mark_hit($cache_key);
            return $cached;
        }

        // 2. Miss: call OpenAI.
        if ($this->log_misses) {
            log_message('debug', 'ChatAI cache MISS key=' . $cache_key . ' preview=' . substr($message, 0, 80));
        }

        $response = $this->_call_openai($message, $previousMessages);

        // 3. Only cache real responses, never the fallback string.
        if ($this->_is_real_response($response)) {
            $this->_store_cache($cache_key, $message, $response);
        }

        return $response;
    }

    /**
     * Public fallback kept identical to original so callers that invoke it
     * directly (none known) keep working.
     */
    public function generate_fallback_response($message)
    {
        $responses = [
            "I've analyzed the data for the selected period. Here are the key insights based on available metrics.",
            "Based on the business data analysis, here are the performance highlights and recommendations.",
            "The analysis reveals important trends and patterns in the selected time period. Here's a summary.",
            "Here's my analysis of the business metrics for the specified date range."
        ];

        return $responses[array_rand($responses)] . "\n\nPlease ensure your ChatGPT API key is properly configured for more detailed analysis.";
    }

    // =================================================================
    // Internals
    // =================================================================

    /**
     * Build a stable cache key from message + prior context.
     * Order matters in the conversation, so we keep the array as-is.
     */
    private function _build_cache_key($message, $previousMessages)
    {
        $payload = [
            'm' => (string) $message,
            'p' => is_array($previousMessages) ? $previousMessages : [],
        ];
        return sha1(json_encode($payload));
    }

    /**
     * Return cached response string if a row exists within TTL, otherwise NULL.
     */
    private function _lookup_cache($cache_key)
    {
        $cutoff = date('Y-m-d H:i:s', time() - $this->cache_ttl);

        $row = $this->db
            ->select('response')
            ->from('chatai_cache')
            ->where('cache_key', $cache_key)
            ->where('created_at >=', $cutoff)
            ->order_by('id', 'DESC')
            ->limit(1)
            ->get()
            ->row_array();

        if ($row && isset($row['response'])) {
            return $row['response'];
        }
        return NULL;
    }

    /**
     * Persist a fresh API response. Uses INSERT ... ON DUPLICATE KEY UPDATE
     * via raw query so a parallel writer cannot violate the unique key.
     */
    private function _store_cache($cache_key, $message, $response)
    {
        $now = date('Y-m-d H:i:s');
        $preview = substr((string) $message, 0, 240);

        $sql = "INSERT INTO chatai_cache (cache_key, prompt_preview, response, model, created_at, hit_count)
                VALUES (?, ?, ?, 'gpt-4o', ?, 0)
                ON DUPLICATE KEY UPDATE
                    response = VALUES(response),
                    created_at = VALUES(created_at),
                    hit_count = 0,
                    last_hit_at = NULL";

        try {
            $this->db->query($sql, [$cache_key, $preview, $response, $now]);
        } catch (Exception $e) {
            // Never let cache writes break the API flow.
            log_message('error', 'ChatAI cache write failed: ' . $e->getMessage());
        }
    }

    /**
     * Bump hit_count and last_hit_at on a cache hit. Best effort, never block.
     */
    private function _mark_hit($cache_key)
    {
        $now = date('Y-m-d H:i:s');
        try {
            $this->db->query(
                "UPDATE chatai_cache SET hit_count = hit_count + 1, last_hit_at = ? WHERE cache_key = ? LIMIT 1",
                [$now, $cache_key]
            );
        } catch (Exception $e) {
            log_message('error', 'ChatAI cache hit update failed: ' . $e->getMessage());
        }
    }

    /**
     * Detect whether a response is the canned fallback. We refuse to cache
     * those so the next caller still gets a real attempt.
     */
    private function _is_real_response($response)
    {
        if (!is_string($response) || $response === '') {
            return FALSE;
        }
        // The canned fallback always ends with this exact suffix.
        $fallback_suffix = "Please ensure your ChatGPT API key is properly configured for more detailed analysis.";
        if (strpos($response, $fallback_suffix) !== FALSE) {
            return FALSE;
        }
        return TRUE;
    }

    /**
     * Live OpenAI call. Identical to the original ChatAI_model body so the
     * behavioural contract (model gpt-4o, temperature 0.3, system prompt,
     * fallback on missing content) is preserved exactly.
     */
    private function _call_openai($message, $previousMessages)
    {
        $api_key = $this->config->item('openai_api_key');
        $api_url = 'https://api.openai.com/v1/chat/completions';

        $messages = [];

        $messages[] = [
            'role' => 'system',
            'content' => 'You are a Business Analytics AI assistant. Provide detailed, insightful analysis based on the data provided. Format your response with clear sections, bullet points where appropriate, and actionable recommendations.'
        ];

        if (is_array($previousMessages)) {
            foreach ($previousMessages as $msg) {
                if (isset($msg['role']) && isset($msg['content'])) {
                    $messages[] = [
                        'role'    => $msg['role'],
                        'content' => $msg['content']
                    ];
                }
            }
        }

        $messages[] = ['role' => 'user', 'content' => $message];

        $data = [
            'model'             => 'gpt-4o',
            'messages'          => $messages,
            'max_tokens'        => 8192,
            'temperature'       => 0.3,
            'top_p'             => 0.9,
            'presence_penalty'  => 0.2,
            'frequency_penalty' => 0.2,
        ];

        $ch = curl_init($api_url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'Authorization: Bearer ' . $api_key
        ]);

        $result = curl_exec($ch);
        curl_close($ch);

        $result_data = json_decode($result, true);

        if (isset($result_data['choices'][0]['message']['content'])) {
            return $result_data['choices'][0]['message']['content'];
        }

        return $this->generate_fallback_response($message);
    }
}

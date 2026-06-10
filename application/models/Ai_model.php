<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Ai_model extends CI_Model {
    private function _key() {
        if (!empty($this->apiKey)) return $this->apiKey;
        $ci = function_exists('get_instance') ? get_instance() : null;
        if ($ci) { $ci->load->config('openai', TRUE); $k = $ci->config->item('openai_api_key','openai'); if (!empty($k)) { $this->apiKey=$k; return $k; } }
        $e = getenv('STEM_OPENAI_KEY') ?: getenv('STEM_ASK_LLM_API_KEY'); if (!empty($e)) { $this->apiKey=$e; return $e; }
        return '';
    }
    private $apiKey = ""; // centralized 2026-06-10 - resolved lazily from config/env
    private $apiUrl = "https://api.openai.com/v1/chat/completions";

    public function ask_ai($message) {
        $data = [
            "model" => "gpt-3.5-turbo", // or "gpt-4"
            "messages" => [
                ["role" => "system", "content" => "You are a helpful assistant."],
                ["role" => "user", "content" => $message]
            ],
            "max_tokens" => 200,
            "temperature" => 0.7
        ];

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->apiUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            "Content-Type: application/json",
            "Authorization: Bearer {$this->_key()}"
        ]);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));

        $response = curl_exec($ch);

        if (curl_errno($ch)) {
            $error_msg = curl_error($ch);
            curl_close($ch);
            return "cURL error: " . $error_msg;
        }

        curl_close($ch);

        $result = json_decode($response, true);

        // Debug: Uncomment temporarily to see full API output
        // echo "<pre>"; print_r($result); echo "</pre>"; exit;

        if (isset($result['error'])) {
            return "API Error: " . $result['error']['message'];
        }

        return $result['choices'][0]['message']['content'] ?? 'No response from AI.';
    }
}

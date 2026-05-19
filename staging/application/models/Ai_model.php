<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Ai_model extends CI_Model {

    /** @var string OpenAI API key, loaded from secrets at runtime. Never hardcoded. */
    private $apiKey;

    /** @var string */
    private $apiUrl = "https://api.openai.com/v1/chat/completions";

    public function __construct()
    {
        parent::__construct();
        // openai.php config loads via secrets helper from env or /etc/stemapp/secrets.env
        $this->config->load('openai', TRUE);
        $this->apiKey = $this->config->item('openai_api_key', 'openai');
        if (!$this->apiKey) {
            // Fallback to global config if loaded elsewhere.
            $this->apiKey = $this->config->item('openai_api_key');
        }
    }

    public function ask_ai($message) {
        if (!$this->apiKey) {
            return "OpenAI key not configured. Set openai_api_key in /etc/stemapp/secrets.env.";
        }

        $data = [
            "model" => "gpt-3.5-turbo",
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
            "Authorization: Bearer {$this->apiKey}"
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

        if (isset($result['error'])) {
            return "API Error: " . $result['error']['message'];
        }

        return $result['choices'][0]['message']['content'] ?? 'No response from AI.';
    }
}

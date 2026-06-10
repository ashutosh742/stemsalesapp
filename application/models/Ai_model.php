<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Ai_model extends CI_Model {

    // private $apiKey = "REDACTED_KEY_SEE_SERVER_CONFIG";
    private $apiKey ="REDACTED_KEY_SEE_SERVER_CONFIG";
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

        // Debug: Uncomment temporarily to see full API output
        // echo "<pre>"; print_r($result); echo "</pre>"; exit;

        if (isset($result['error'])) {
            return "API Error: " . $result['error']['message'];
        }

        return $result['choices'][0]['message']['content'] ?? 'No response from AI.';
    }
}

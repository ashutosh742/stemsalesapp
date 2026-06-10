dssd<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class OpenAI_library {
    private $api_key;
    private $base_url;
    private $ci;
    
    public function __construct() {
        $this->ci =& get_instance();
        $this->ci->config->load('config', TRUE);
        $this->api_key = $this->ci->config->item('openai_api_key');
        $this->base_url = $this->ci->config->item('openai_base_url');
    }
    
    /**
     * Send request to OpenAI API
     */
    private function send_request($endpoint, $data) {
        $url = $this->base_url . $endpoint;
        
        $headers = [
            'Authorization: Bearer ' . $this->api_key,
            'Content-Type: application/json'
        ];
        
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // For testing only
        
        $response = curl_exec($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        
        if (curl_errno($ch)) {
            $error = curl_error($ch);
            curl_close($ch);
            return ['error' => "cURL Error: $error"];
        }
        
        curl_close($ch);
        
        $decoded_response = json_decode($response, true);
        
        if ($http_code != 200) {
            return [
                'error' => "HTTP $http_code",
                'response' => $decoded_response
            ];
        }
        
        return $decoded_response;
    }
    
    /**
     * Generate SQL query from natural language
     */
    public function generate_sql_from_text($user_input, $table_schema) {
        $prompt = "You are a MySQL expert. Given the following table schema:\n\n";
        $prompt .= $table_schema . "\n\n";
        $prompt .= "Generate a valid MySQL SELECT query for: " . $user_input . "\n";
        $prompt .= "Return ONLY the SQL query without any explanations or markdown formatting.\n";
        $prompt .= "If the query needs specific fields, include them. If not sure, use *.\n";
        $prompt .= "Make sure the query is safe and valid.";
        
        $data = [
            'model' => 'gpt-3.5-turbo',
            'messages' => [
                ['role' => 'system', 'content' => 'You are a helpful SQL query generator.'],
                ['role' => 'user', 'content' => $prompt]
            ],
            'temperature' => 0.1,
            'max_tokens' => 500
        ];
        
        $response = $this->send_request('chat/completions', $data);
        
        if (isset($response['choices'][0]['message']['content'])) {
            $sql = trim($response['choices'][0]['message']['content']);
            
            // Clean up the response (remove backticks if present)
            $sql = str_replace('```sql', '', $sql);
            $sql = str_replace('```', '', $sql);
            
            return ['success' => true, 'sql' => $sql];
        }
        
        return ['success' => false, 'error' => 'Failed to generate SQL'];
    }
    
    /**
     * Explain query results in natural language
     */
    public function explain_results($query, $results) {
        $prompt = "Explain the following database query results in simple, natural language:\n\n";
        $prompt .= "Query: " . $query . "\n\n";
        $prompt .= "Results (as JSON): " . json_encode($results) . "\n\n";
        $prompt .= "Provide a concise summary of what these results show.";
        
        $data = [
            'model' => 'gpt-3.5-turbo',
            'messages' => [
                ['role' => 'system', 'content' => 'You are a helpful data analyst.'],
                ['role' => 'user', 'content' => $prompt]
            ],
            'temperature' => 0.7,
            'max_tokens' => 300
        ];
        
        $response = $this->send_request('chat/completions', $data);
        
        if (isset($response['choices'][0]['message']['content'])) {
            return trim($response['choices'][0]['message']['content']);
        }
        
        return "Could not generate explanation.";
    }
}
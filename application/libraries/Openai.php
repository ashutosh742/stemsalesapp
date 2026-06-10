<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Openai {
    private $api_key;
    private $ci;
    
    public function __construct() {
        $this->ci =& get_instance();
        
        // Get API key from config
        $this->api_key = $this->ci->config->item('openai_api_key');
        
        if (empty($this->api_key)) {
            log_message('error', 'OpenAI API key is not configured in config.php');
            show_error('OpenAI API key is not configured. Please add $config[\'openai_api_key\'] to config.php');
        }
        
        // Log that library was loaded (for debugging)
        log_message('debug', 'OpenAI library loaded with API key: ' . substr($this->api_key, 0, 8) . '...');
    }
    
    /**
     * Send request to OpenAI API
     */
    private function call_openai($messages, $model = 'gpt-3.5-turbo', $max_tokens = 500) {
        $url = 'https://api.openai.com/v1/chat/completions';
        
        $headers = [
            'Authorization: Bearer ' . $this->api_key,
            'Content-Type: application/json'
        ];
        
        $data = [
            'model' => $model,
            'messages' => $messages,
            'temperature' => 0.1,
            'max_tokens' => $max_tokens
        ];
        
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        
        $response = curl_exec($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        
        if (curl_errno($ch)) {
            $error = curl_error($ch);
            curl_close($ch);
            log_message('error', 'OpenAI cURL Error: ' . $error);
            return ['error' => 'cURL Error: ' . $error];
        }
        
        curl_close($ch);
        
        log_message('debug', 'OpenAI Response HTTP Code: ' . $http_code);
        log_message('debug', 'OpenAI Response: ' . substr($response, 0, 200));
        
        if ($http_code != 200) {
            log_message('error', 'OpenAI API Error: HTTP ' . $http_code . ' - ' . $response);
            return ['error' => 'HTTP ' . $http_code, 'response' => $response];
        }
        
        return json_decode($response, true);
    }
    
    /**
     * Generate SQL query from natural language - UPDATED METHOD SIGNATURE
     */
    public function generate_sql($user_input, $database_summary = null, $all_schemas = null, $table_schemas = null) {
        // Handle different parameter scenarios
        if ($all_schemas === null && $table_schemas !== null) {
            $all_schemas = $table_schemas;
        }
        
        // Build schema text
        $schema_text = "Available tables:\n";
        
        if (is_array($all_schemas) && !empty($all_schemas)) {
            $table_count = 0;
            foreach ($all_schemas as $table_name => $schema) {
                if ($table_count < 10) { // Limit to 10 tables
                    if (is_string($schema)) {
                        $schema_text .= substr($schema, 0, 1000) . "\n\n";
                    } else {
                        $schema_text .= $table_name . " table\n\n";
                    }
                    $table_count++;
                }
            }
        } elseif ($database_summary) {
            $schema_text .= $database_summary;
        }
        
        // Add database summary if available
        if ($database_summary && is_string($database_summary)) {
            $schema_text = $database_summary . "\n\n" . $schema_text;
        }
        
        // Simple prompt
        $prompt = "You are a MySQL expert. Based on these tables:\n\n" . 
                 $schema_text . 
                 "\nWrite a SELECT query for: " . $user_input . 
                 "\n\nReturn ONLY the SQL query, nothing else.";
        
        $messages = [
            ['role' => 'system', 'content' => 'You generate MySQL SELECT queries. Return only SQL.'],
            ['role' => 'user', 'content' => $prompt]
        ];
        
        $response = $this->call_openai($messages, 'gpt-3.5-turbo', 300);
        
        if (isset($response['error'])) {
            return [
                'success' => false, 
                'error' => 'OpenAI API Error: ' . $response['error']
            ];
        }
        
        if (isset($response['choices'][0]['message']['content'])) {
            $sql = trim($response['choices'][0]['message']['content']);
            
            // Clean up the response
            $sql = preg_replace('/^```sql\s*/', '', $sql);
            $sql = preg_replace('/\s*```$/', '', $sql);
            $sql = preg_replace('/^```\s*/', '', $sql);
            $sql = preg_replace('/\s*```$/', '', $sql);
            $sql = trim($sql, " \n\r\t\v\x00;");
            
            // Basic validation
            if (empty($sql)) {
                return [
                    'success' => false, 
                    'error' => 'Empty SQL generated'
                ];
            }
            
            // Ensure it starts with SELECT (but not for UNION queries)
            if (stripos($sql, 'SELECT') !== 0 && stripos($sql, 'WITH') !== 0) {
                $sql = "SELECT " . $sql;
            }
            
            log_message('debug', 'Generated SQL: ' . $sql);
            
            return [
                'success' => true, 
                'sql' => $sql
            ];
        }
        
        return [
            'success' => false, 
            'error' => 'No SQL generated from AI response'
        ];
    }
    
    /**
     * Simple explanation - UPDATED METHOD SIGNATURE
     */
    public function explain_results($query, $results, $count, $user_input = null) {
        $sample = array_slice($results, 0, 3);
        
        $prompt = "Explain this query result in simple terms:\n" .
                 "User question: " . ($user_input ?: "N/A") . "\n" .
                 "Query: " . $query . "\n" .
                 "Rows returned: " . $count . "\n" .
                 "Sample data: " . json_encode($sample) . "\n" .
                 "Explain what this data shows in business terms.";
        
        $messages = [
            ['role' => 'system', 'content' => 'You explain database query results in simple business terms.'],
            ['role' => 'user', 'content' => $prompt]
        ];
        
        $response = $this->call_openai($messages, 'gpt-3.5-turbo', 200);
        
        if (isset($response['error'])) {
            return "Query returned " . $count . " records." . ($user_input ? " This answers: " . $user_input : "");
        }
        
        if (isset($response['choices'][0]['message']['content'])) {
            return trim($response['choices'][0]['message']['content']);
        }
        
        return "Query returned " . $count . " records." . ($user_input ? " This answers: " . $user_input : "");
    }
    
    /**
     * Suggest related queries - NEW METHOD
     */
    public function suggest_related_queries($user_input, $all_schemas = null) {
        $schema_text = "Available database tables: ";
        if (is_array($all_schemas)) {
            $schema_text .= implode(', ', array_keys(array_slice($all_schemas, 0, 5)));
        }
        
        $prompt = "Based on this user question about a database: '" . $user_input . "'\n" .
                 $schema_text . "\n" .
                 "Suggest 3 related questions the user might want to ask. Return as a bulleted list.";
        
        $messages = [
            ['role' => 'system', 'content' => 'You suggest related database questions.'],
            ['role' => 'user', 'content' => $prompt]
        ];
        
        $response = $this->call_openai($messages, 'gpt-3.5-turbo', 150);
        
        if (isset($response['error']) || !isset($response['choices'][0]['message']['content'])) {
            return [
                "• Show me recent user activity",
                "• List all active tasks",
                "• Count records in main tables"
            ];
        }
        
        $suggestions = trim($response['choices'][0]['message']['content']);
        // Convert to array
        $suggestions_array = [];
        $lines = explode("\n", $suggestions);
        foreach ($lines as $line) {
            $line = trim($line, " -*•\t\n\r\0\x0B");
            if (!empty($line)) {
                $suggestions_array[] = $line;
            }
        }
        
        return array_slice($suggestions_array, 0, 3);
    }
}
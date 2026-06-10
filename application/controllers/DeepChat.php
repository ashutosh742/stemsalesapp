<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class DeepChat extends CI_Controller {

    public  $uid;
    public  $sdate;
    public  $edate;
    public  $task_action_id;
    public  $admin_id_filter;
    public  $rm_filter;
    public  $userwise;
    public  $target_uid;
    public  $mtypes;
    public  $plannerDate;
    
    public function __construct() {
        parent::__construct();
        $this->load->helper('url');
        $this->load->library('session');
        
        // Initialize default parameters
        $this->uid = $this->session->userdata('user')['user_id'] ?? 0;
        $this->type_id = $this->session->userdata('user')['type_id'] ?? 0;
        $this->user = $this->session->userdata('user');
        $this->sdate = date('Y-m-01'); // First day of current month
        $this->edate = date('Y-m-t'); // Last day of current month
        $this->task_action_id = 'all'; // Default: all task actions
        $this->admin_id_filter = $this->uid; // Default: all admins
        $this->rm_filter = $this->uid; // Default: all RMs
        $this->userwise = 'userwise'; // Default: all users
        $this->target_uid = 0; // Default: target user
        $this->mtypes = ''; // Default: all mtypes
        $this->plannerDate = date('Y-m-d'); // Current date for planner
        
        // Load models
        $this->load->model('Menu_model');
        $this->load->model('Report_model');
        $this->load->model('ChatAI_model');
        $this->load->model('AIAgents/TeamDetails_model');
        $this->load->model('AIAgents/TaskAnalysis_model');
        $this->load->model('AIAgents/TaskDetailsAnalysis_model');
        $this->load->model('AIAgents/Meeting_Details_model');
        $this->load->model('AIAgents/Meeting_Details_With_Company_Name_model');
        $this->load->model('AIAgents/ProposalDetails_model');
        $this->load->model('AIAgents/MeetingVsProposalSummary_model');
        $this->load->model('AIAgents/FutureTaskDetailsSummary_model');
        $this->load->model('AIAgents/TaskConversionSummary_model');
        $this->load->model('AIAgents/PendingMoMWriteAfterRPMeetings_model');
        $this->load->model('AIAgents/LineManagerCallingonRPLeads_model');
        
        // Load configuration
        $this->load->config('deepseek', TRUE);
    }
    
    public function index() {
        $data['projects'] = [
            'Task Analysis',
            'Task Planning',
            'Task Execution',
            'Status Change',
            'Funnel Analysis',
            'Action Analysis',
            'Day in Day Out',
            'Efficiency & Productivity',
            'Daily Performance',
            'BD Performance',
            'CM Performance'
        ];
        $data['users'] = [$this->user];

        $this->load->view('Chat/DeepChatIindex', $data);
    }
    
    public function process() {
        // Check if it's an AJAX request
        if (!$this->input->is_ajax_request()) {
            show_error('Direct access not allowed', 403);
        }
        
        // Get POST data
        $post_data = json_decode(file_get_contents('php://input'), true);
        $message = $post_data['message'] ?? '';
        $analysisType = $post_data['analysisType'] ?? '';
        $chatId = $post_data['chatId'] ?? '';
        $previousMessages = $post_data['previousMessages'] ?? [];
        $dateRange = $post_data['dateRange'] ?? null;
        
        // Set date range
        if ($dateRange) {
            $this->sdate = $dateRange['startDate'] ?? $this->sdate;
            $this->edate = $dateRange['endDate'] ?? $this->edate;
        }
        
        // Process the request based on analysis type
        $rawResponse = $this->process_analysis($message, $analysisType, $previousMessages, $this->sdate, $this->edate);
        
        // Process the raw response through DeepSeek
        $processedResponse = $this->process_response_with_deepseek($rawResponse, $message, $analysisType, $previousMessages);

        // Clean the response
        $processedResponse = $this->replaceAsteriskWithSpace($processedResponse);

        // Return JSON response
        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode([
                'success' => true,
                'data' => $processedResponse
            ]));
    }
    
    public function process_analysis($message, $analysisType, $previousMessages, $sdate, $edate) {
        $response = [
            'content' => '',
            'data' => null
        ];
        
        // Process based on analysis type
        switch($analysisType) {
            case 'Team Detail':
                $response = $this->TeamDetails_model->process_Team_Details_analysis($message, $analysisType, $sdate, $edate);
                break;
            case 'Task Analysis':
                $response = $this->TaskAnalysis_model->process_task_analysis($message, $analysisType,$sdate, $edate);
                break;
            case 'Task Details By Company':
                $response = $this->TaskDetailsAnalysis_model->process_total_task_details_analysis($message, $analysisType,$sdate, $edate);
                break;
            case 'Meeting Details':
                $response = $this->Meeting_Details_model->process_Meeting_Details($message,$analysisType,$sdate, $edate);
                break;
            case 'Meeting Details With Company Name':
                $response = $this->Meeting_Details_With_Company_Name_model->process_Meeting_Details_With_Company_Name($message,$analysisType,$sdate, $edate);
                break;
            case 'Proposal Details':
                $response = $this->ProposalDetails_model->process_ProposalDetails_execution($message,$analysisType,$sdate, $edate);
                break;
            case 'Meeting v/s Proposal Summary':
                $response = $this->MeetingVsProposalSummary_model->process_MeetingVsProposalSummary($message,$analysisType,$sdate, $edate);
                break;
            case 'Future Task Details':
                $response = $this->FutureTaskDetailsSummary_model->process_FutureTaskDetailsSummary_model($message,$analysisType,$sdate, $edate);
                break;
            case 'Task Conversion Summary':
                $response = $this->TaskConversionSummary_model->process_TaskConversionSummary_model($message,$analysisType,$sdate, $edate);
                break;
            case 'Pending MoM Write After RP Meetings':
                $response = $this->PendingMoMWriteAfterRPMeetings_model->process_PendingMoMWriteAfterRPMeetings_analysis($message,$analysisType,$sdate, $edate);
                break;
            case 'Line Manager Calling on RP Leads':
                $response = $this->LineManagerCallingonRPLeads_model->process_LineManagerCallingonRPLeads_analysis($message,$analysisType,$sdate, $edate);
                break;
            case 'Status Change':
                $response = $this->process_status_change($message);
                break;
            case 'Funnel Analysis':
                $response = $this->process_funnel_analysis($message);
                break;
            case 'Action Analysis':
                $response = $this->process_action_analysis($message);
                break;
            case 'Day in Day Out':
                $response = $this->process_day_in_day_out($message);
                break;
            case 'Efficiency & Productivity':
                $response = $this->process_efficiency_productivity($message);
                break;
            case 'Daily Performance':
                $response = $this->process_daily_performance($message);
                break;
            case 'BD Performance':
                $response = $this->process_bd_performance($message);
                break;
            case 'CM Performance':
                $response = $this->process_cm_performance($message);
                break;
            default:
                // Use DeepSeek API for general queries
                $response['content'] = $this->call_deepseek_api($message, $previousMessages);
                break;
        }
        
        return $response;
    }
    
    public function process_response_with_deepseek($rawResponse, $originalMessage, $analysisType, $previousMessages) {
        // Prepare context for DeepSeek
        $context = $this->prepare_deepseek_context($rawResponse, $originalMessage, $analysisType);
        
        // Build prompt for DeepSeek
        $prompt = $this->build_deepseek_prompt($context, $analysisType);
        
        // Call DeepSeek API
        $deepseekResponse = $this->call_deepseek_api_with_context($prompt, $previousMessages, $context);
        
        // Enhance response with data if available
        if (isset($rawResponse['data']) && !empty($rawResponse['data'])) {
            $deepseekResponse['data'] = $rawResponse['data'];
        }
        
        return $deepseekResponse;
    }
    
    public function prepare_deepseek_context($rawResponse, $originalMessage, $analysisType) {
        $context = [
            'analysis_type' => $analysisType,
            'original_message' => $originalMessage,
            'date_range' => [
                'start_date' => $this->sdate,
                'end_date' => $this->edate
            ],
            'raw_response' => $rawResponse
        ];
        
        // Add specific context based on analysis type
        switch($analysisType) {
            case 'Team Detail':
                $context['analysis_description'] = 'Team performance and details analysis';
                break;
            case 'Task Analysis':
                $context['analysis_description'] = 'Task completion and efficiency analysis';
                break;
            case 'Meeting Details':
                $context['analysis_description'] = 'Meeting statistics and details';
                break;
            case 'Proposal Details':
                $context['analysis_description'] = 'Proposal tracking and analysis';
                break;
            case 'Meeting v/s Proposal Summary':
                $context['analysis_description'] = 'Meeting to proposal conversion analysis';
                break;
            case 'Future Task Details':
                $context['analysis_description'] = 'Upcoming tasks and planning analysis';
                break;
            case 'Task Conversion Summary':
                $context['analysis_description'] = 'Task completion and conversion rates analysis';
                break;
            case 'Pending MoM Write After RP Meetings':
                $context['analysis_description'] = 'Pending meeting minutes analysis';
                break;
            case 'Line Manager Calling on RP Leads':
                $context['analysis_description'] = 'Manager lead follow-up analysis';
                break;
            default:
                $context['analysis_description'] = 'Business performance analysis';
                break;
        }
        
        return $context;
    }
    
    public function build_deepseek_prompt($context, $analysisType) {
        // Limit data size for API (DeepSeek has token limits)
        $dataForAnalysis = $this->prepare_data_for_deepseek($context['raw_response']['data'] ?? []);
        
        $prompt = "You are a business analytics assistant. Analyze the following data and provide insights in a clear, concise, and actionable format.\n\n";
        
        $prompt .= "ANALYSIS REQUEST DETAILS:\n";
        $prompt .= "Analysis Type: {$context['analysis_type']}\n";
        $prompt .= "User Query: {$context['original_message']}\n";
        $prompt .= "Date Range: {$context['date_range']['start_date']} to {$context['date_range']['end_date']}\n";
        $prompt .= "Analysis Description: {$context['analysis_description']}\n\n";
        
        if (isset($context['raw_response']['content']) && !empty($context['raw_response']['content'])) {
            $prompt .= "PRELIMINARY ANALYSIS:\n{$context['raw_response']['content']}\n\n";
        }
        
        if (!empty($dataForAnalysis)) {
            $prompt .= "DATA FOR ANALYSIS:\n";
            if (is_array($dataForAnalysis) && count($dataForAnalysis) <= 10) {
                $prompt .= json_encode($dataForAnalysis, JSON_PRETTY_PRINT) . "\n\n";
            } else {
                $dataSummary = $this->summarize_data_for_deepseek($dataForAnalysis);
                $prompt .= $dataSummary . "\n\n";
            }
        }
        
        $prompt .= "RESPONSE REQUIREMENTS:\n";
        $prompt .= "1. Provide a comprehensive analysis answering: {$context['original_message']}\n";
        $prompt .= "2. Include key metrics and statistics\n";
        $prompt .= "3. Identify trends and patterns\n";
        $prompt .= "4. Provide actionable recommendations\n";
        $prompt .= "5. Highlight areas needing attention\n";
        $prompt .= "6. Format response in clear sections\n";
        $prompt .= "7. Use bullet points for lists\n";
        $prompt .= "8. Keep response professional but accessible\n";
        
        return $prompt;
    }
    
    public function prepare_data_for_deepseek($data) {
        if (empty($data)) {
            return [];
        }
        
        // If data is too large, sample it
        if (is_array($data) && count($data) > 50) {
            // Take first 50 records for analysis
            $sample = array_slice($data, 0, 50);
            
            // Add metadata about sampling
            return [
                'sample_size' => 50,
                'total_records' => count($data),
                'data' => $sample,
                'note' => 'Showing 50 sample records from total dataset'
            ];
        }
        
        return $data;
    }
    
    public function summarize_data_for_deepseek($data) {
        $summary = "";
        
        if (is_array($data)) {
            if (isset($data['sample_size'])) {
                // Handle sampled data
                $summary .= "Data Summary: {$data['sample_size']} sample records from {$data['total_records']} total records\n";
                if (isset($data['data']) && is_array($data['data'])) {
                    $sampleData = $data['data'];
                    $recordCount = count($sampleData);
                    $summary .= "Sample contains {$recordCount} records\n";
                    
                    if ($recordCount > 0) {
                        // Get columns from first record
                        $firstRecord = $sampleData[0];
                        if (is_array($firstRecord)) {
                            $columns = array_keys($firstRecord);
                            $summary .= "Columns: " . implode(', ', array_slice($columns, 0, 10)) . "\n";
                            if (count($columns) > 10) {
                                $summary .= "... and " . (count($columns) - 10) . " more columns\n";
                            }
                        }
                        
                        // Show key metrics if available
                        $summary .= "\nSample Records Preview:\n";
                        for ($i = 0; $i < min(3, $recordCount); $i++) {
                            $summary .= "Record " . ($i + 1) . ": " . 
                                      json_encode(array_slice($sampleData[$i], 0, 5)) . 
                                      (count($sampleData[$i]) > 5 ? " ..." : "") . "\n";
                        }
                    }
                }
            } elseif (isset($data[0]) && is_array($data[0])) {
                // Array of records
                $recordCount = count($data);
                $summary .= "Total Records: {$recordCount}\n";
                
                if ($recordCount > 0) {
                    $firstRecord = $data[0];
                    $columns = array_keys($firstRecord);
                    $summary .= "Columns: " . implode(', ', array_slice($columns, 0, 10)) . "\n";
                    if (count($columns) > 10) {
                        $summary .= "... and " . (count($columns) - 10) . " more columns\n";
                    }
                    
                    // Calculate basic statistics for numeric columns
                    $numericStats = $this->calculate_numeric_stats($data, $columns);
                    if (!empty($numericStats)) {
                        $summary .= "\nNumeric Statistics:\n";
                        foreach ($numericStats as $column => $stats) {
                            $summary .= "- {$column}: Min={$stats['min']}, Max={$stats['max']}, Avg={$stats['avg']}\n";
                        }
                    }
                }
            } else {
                // Single record or simple array
                $summary .= "Data: " . json_encode($data) . "\n";
            }
        } else {
            $summary .= "Data: " . substr((string)$data, 0, 500) . (strlen((string)$data) > 500 ? "..." : "") . "\n";
        }
        
        return $summary;
    }
    
    public function calculate_numeric_stats($data, $columns) {
        $stats = [];
        
        foreach ($columns as $column) {
            $values = [];
            foreach ($data as $record) {
                if (isset($record[$column]) && is_numeric($record[$column])) {
                    $values[] = $record[$column];
                }
            }
            
            if (count($values) > 0) {
                $stats[$column] = [
                    'min' => min($values),
                    'max' => max($values),
                    'avg' => round(array_sum($values) / count($values), 2),
                    'count' => count($values)
                ];
            }
        }
        
        return $stats;
    }
    
    public function call_deepseek_api_with_context($prompt, $previousMessages, $context) {
        // Get API configuration
        $api_key = $this->config->item('deepseek_api_key', 'deepseek');
        if (empty($api_key)) {
            // Fallback to environment variable or default
            $api_key = getenv('DEEPSEEK_API_KEY') ?: 'your-deepseek-api-key-here';
        }
        
        $api_url = 'https://api.deepseek.com/chat/completions';
        
        // Build messages array
        $messages = [];
        
        // Add system message
        $messages[] = [
            'role' => 'system',
            'content' => "You are a business analytics assistant specializing in sales, marketing, and team performance analysis. 
                        Provide data-driven insights, actionable recommendations, and clear explanations. 
                        Format your response with proper sections, use bullet points for lists, and highlight key findings. 
                        Be professional, concise, and focus on business impact."
        ];
        
        // Add previous messages if available (limit to last 5 for context)
        if (is_array($previousMessages) && !empty($previousMessages)) {
            $recentMessages = array_slice($previousMessages, -5); // Last 5 messages for context
            foreach ($recentMessages as $msg) {
                if (isset($msg['role']) && isset($msg['content'])) {
                    $messages[] = [
                        'role' => $msg['role'],
                        'content' => substr($msg['content'], 0, 1000) // Limit message size
                    ];
                }
            }
        }
        
        // Add current prompt
        $messages[] = [
            'role' => 'user',
            'content' => $prompt
        ];
        
        // Prepare API request data for DeepSeek
        $requestData = [
            'model' => 'deepseek-chat', // or 'deepseek-coder' for code analysis
            'messages' => $messages,
            'temperature' => 0.7,
            'max_tokens' => 4000,
            'top_p' => 0.9,
            'frequency_penalty' => 0.1,
            'presence_penalty' => 0.1,
            'stream' => false
        ];
        
        // Initialize cURL
        $ch = curl_init();
        
        curl_setopt_array($ch, [
            CURLOPT_URL => $api_url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => json_encode($requestData),
            CURLOPT_HTTPHEADER => [
                'Content-Type: application/json',
                'Authorization: Bearer ' . $api_key,
                'Accept: application/json'
            ],
            CURLOPT_TIMEOUT => 60,
            CURLOPT_SSL_VERIFYPEER => true,
            CURLOPT_SSL_VERIFYHOST => 2
        ]);
        
        // Execute request
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curlError = curl_error($ch);
        
        curl_close($ch);
        
        // Log API call for debugging
        $this->log_api_call($api_url, $httpCode, $curlError);
        
        // Process response
        if ($response === false) {
            error_log("DeepSeek API cURL Error: " . $curlError);
            return $this->generate_fallback_response($context);
        }
        
        $decodedResponse = json_decode($response, true);
        
        if ($httpCode !== 200 || !isset($decodedResponse['choices'][0]['message']['content'])) {
            error_log("DeepSeek API Error - HTTP Code: $httpCode, Response: " . json_encode($decodedResponse));
            return $this->generate_fallback_response($context);
        }
        
        // Extract and format the response
        $content = $decodedResponse['choices'][0]['message']['content'];
        
        // Try to parse JSON if response contains JSON
        $jsonResponse = $this->extract_json_from_response($content);
        if ($jsonResponse !== null) {
            $content = $jsonResponse;
        }
        
        // Return formatted response
        return [
            'content' => $content,
            'analysis_type' => $context['analysis_type'],
            'date_range' => $context['date_range'],
            'model_used' => 'deepseek-chat',
            'tokens_used' => $decodedResponse['usage']['total_tokens'] ?? null
        ];
    }
    
    public function call_deepseek_api($message, $previousMessages = []) {
        $api_key = $this->config->item('deepseek_api_key', 'deepseek');
        if (empty($api_key)) {
            $api_key = getenv('DEEPSEEK_API_KEY') ?: 'your-deepseek-api-key-here';
        }
        
        $api_url = 'https://api.deepseek.com/chat/completions';
        
        // Build messages array
        $messages = [];
        
        // Add system message
        $messages[] = [
            'role' => 'system',
            'content' => "You are a helpful business analytics assistant."
        ];
        
        // Add previous messages
        if (is_array($previousMessages) && !empty($previousMessages)) {
            $recentMessages = array_slice($previousMessages, -3);
            foreach ($recentMessages as $msg) {
                if (isset($msg['role']) && isset($msg['content'])) {
                    $messages[] = [
                        'role' => $msg['role'],
                        'content' => $msg['content']
                    ];
                }
            }
        }
        
        // Add current message
        $messages[] = [
            'role' => 'user',
            'content' => $message
        ];
        
        // Prepare request
        $requestData = [
            'model' => 'deepseek-chat',
            'messages' => $messages,
            'temperature' => 0.7,
            'max_tokens' => 2000,
            'stream' => false
        ];
        
        // Make API call
        $ch = curl_init();
        curl_setopt_array($ch, [
            CURLOPT_URL => $api_url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => json_encode($requestData),
            CURLOPT_HTTPHEADER => [
                'Content-Type: application/json',
                'Authorization: Bearer ' . $api_key
            ],
            CURLOPT_TIMEOUT => 30
        ]);
        
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        
        if ($response && $httpCode === 200) {
            $decoded = json_decode($response, true);
            return $decoded['choices'][0]['message']['content'] ?? 'No response from AI';
        }
        
        return "Unable to process request. Please try again.";
    }
    
    public function analyze_with_deepseek_free($json_data) {
        // Get API key from config
        $api_key = $this->config->item('deepseek_api_key', 'deepseek');
        if (empty($api_key)) {
            $api_key = getenv('DEEPSEEK_API_KEY') ?: 'your-deepseek-api-key-here';
        }
        
        // Limit data for free tier (DeepSeek is currently free with limits)
        $sample_data = array_slice($json_data, 0, 100); // Increased to 100 for better analysis
        
        $payload = [
            'model' => 'deepseek-chat',
            'messages' => [
                [
                    'role' => 'system',
                    'content' => "You are a business analytics expert. Analyze the provided data and return a comprehensive JSON analysis."
                ],
                [
                    'role' => 'user',
                    'content' => "Analyze this business data sample (100 records from larger dataset): " . 
                                json_encode($sample_data) . "
                                
                                Return a JSON object with the following structure:
                                {
                                    \"summary\": \"Brief overall summary\",
                                    \"key_metrics\": {\"metric1\": value, \"metric2\": value},
                                    \"trends\": [\"trend1\", \"trend2\"],
                                    \"insights\": [\"insight1\", \"insight2\"],
                                    \"recommendations\": [\"rec1\", \"rec2\"],
                                    \"risks\": [\"risk1\", \"risk2\"],
                                    \"opportunities\": [\"opp1\", \"opp2\"]
                                }
                                
                                Focus on business performance, efficiency, and actionable insights."
                ]
            ],
            'max_tokens' => 3000,
            'temperature' => 0.3,
            'stream' => false,
            'response_format' => ['type' => 'json_object']
        ];
        
        $ch = curl_init('https://api.deepseek.com/chat/completions');
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => json_encode($payload),
            CURLOPT_HTTPHEADER => [
                'Content-Type: application/json',
                'Authorization: Bearer ' . $api_key,
                'Accept: application/json'
            ],
            CURLOPT_TIMEOUT => 60
        ]);
        
        $response = curl_exec($ch);
        $curlError = curl_error($ch);
        curl_close($ch);
        
        if ($response === false) {
            error_log("DeepSeek API Error: " . $curlError);
            return ['error' => 'API call failed', 'details' => $curlError];
        }
        
        $result = json_decode($response, true);
        
        if (isset($result['choices'][0]['message']['content'])) {
            $content = $result['choices'][0]['message']['content'];
            
            // Try to parse JSON from response
            $jsonResponse = json_decode($content, true);
            if ($jsonResponse !== null) {
                return $jsonResponse;
            }
            
            // If not valid JSON, extract JSON part
            $jsonStart = strpos($content, '{');
            $jsonEnd = strrpos($content, '}');
            
            if ($jsonStart !== false && $jsonEnd !== false) {
                $jsonStr = substr($content, $jsonStart, $jsonEnd - $jsonStart + 1);
                $parsedJson = json_decode($jsonStr, true);
                if ($parsedJson !== null) {
                    return $parsedJson;
                }
            }
            
            // Return as analysis text if JSON parsing fails
            return ['analysis' => $content];
        }
        
        return $result;
    }
    
    public function extract_json_from_response($content) {
        // Try to parse as JSON directly
        $json = json_decode($content, true);
        if ($json !== null) {
            return $json;
        }
        
        // Try to extract JSON from markdown or text
        $jsonStart = strpos($content, '```json');
        if ($jsonStart !== false) {
            $jsonStart += 7; // Length of ```json
            $jsonEnd = strpos($content, '```', $jsonStart);
            if ($jsonEnd !== false) {
                $jsonStr = substr($content, $jsonStart, $jsonEnd - $jsonStart);
                $json = json_decode(trim($jsonStr), true);
                if ($json !== null) {
                    return $json;
                }
            }
        }
        
        // Try to find any JSON object
        $jsonStart = strpos($content, '{');
        $jsonEnd = strrpos($content, '}');
        if ($jsonStart !== false && $jsonEnd !== false && $jsonEnd > $jsonStart) {
            $jsonStr = substr($content, $jsonStart, $jsonEnd - $jsonStart + 1);
            $json = json_decode($jsonStr, true);
            if ($json !== null) {
                return $json;
            }
        }
        
        return null;
    }
    
    public function generate_fallback_response($context) {
        // Generate a fallback response when DeepSeek API is not available
        $analysisType = $context['analysis_type'];
        $dateRange = $context['date_range'];
        $originalMessage = $context['original_message'];
        
        $fallbackResponses = [
            'Team Detail' => "Team analysis for period {$dateRange['start_date']} to {$dateRange['end_date']}. ",
            'Task Analysis' => "Task analysis completed for the specified period. ",
            'Meeting Details' => "Meeting analysis results available. ",
            'Proposal Details' => "Proposal tracking data analyzed. ",
            'Meeting v/s Proposal Summary' => "Meeting to proposal conversion analysis completed. ",
            'Future Task Details' => "Upcoming tasks analysis available. ",
            'Task Conversion Summary' => "Task conversion rates analyzed. ",
            'Pending MoM Write After RP Meetings' => "Pending meeting minutes analysis completed. ",
            'Line Manager Calling on RP Leads' => "Manager lead follow-up analysis available. ",
            'default' => "Analysis completed for {$analysisType}. "
        ];
        
        $content = $fallbackResponses[$analysisType] ?? $fallbackResponses['default'];
        $content .= "Query: {$originalMessage}\n";
        $content .= "Date Range: {$dateRange['start_date']} to {$dateRange['end_date']}\n\n";
        
        if (isset($context['raw_response']['content'])) {
            $content .= "Raw Analysis:\n" . $context['raw_response']['content'] . "\n\n";
        }
        
        if (isset($context['raw_response']['data']) && !empty($context['raw_response']['data'])) {
            $dataSummary = $this->summarize_data_for_deepseek($context['raw_response']['data']);
            $content .= "Data Summary:\n" . $dataSummary . "\n\n";
            $content .= "Note: AI analysis temporarily unavailable. Please review the raw data above.";
        } else {
            $content .= "No data available for analysis.";
        }
        
        return [
            'content' => $content,
            'analysis_type' => $analysisType,
            'date_range' => $dateRange,
            'model_used' => 'fallback',
            'note' => 'AI service temporarily unavailable'
        ];
    }
    
    public function log_api_call($url, $httpCode, $error = null) {
        // Simple logging function
        $logMessage = date('Y-m-d H:i:s') . " - DeepSeek API Call - URL: $url, HTTP Code: $httpCode";
        if ($error) {
            $logMessage .= ", Error: $error";
        }
        
        // Log to file (adjust path as needed)
        $logFile = APPPATH . 'logs/deepseek_api.log';
        file_put_contents($logFile, $logMessage . PHP_EOL, FILE_APPEND);
    }
    
    public function replaceAsteriskWithSpace($data) {
        if (is_array($data)) {
            foreach ($data as $key => $value) {
                $data[$key] = $this->replaceAsteriskWithSpace($value);
            }
            return $data;
        }

        if (is_string($data)) {
            // Remove markdown formatting
            $clean = str_replace(['*', '#', '**', '__', '~~'], '', $data);
            // Clean up multiple spaces
            $clean = preg_replace('/\s+/', ' ', $clean);
            return trim($clean);
        }

        return $data;
    }
    
    public function beautify_ai_response($deepseekResponse) {
        if (is_array($deepseekResponse)) {
            return $deepseekResponse;
        }
        
        // Remove markdown symbols
        $cleanResponse = str_replace(['#', '*', '**', '__', '~~'], '', $deepseekResponse);
        
        // Convert markdown lists to clean bullet points
        $cleanResponse = preg_replace('/^\s*[-+]\s+/m', '• ', $cleanResponse);
        $cleanResponse = preg_replace('/^\s*\d+\.\s+/m', '', $cleanResponse);
        
        // Clean up multiple spaces
        $cleanResponse = preg_replace('/\s+/', ' ', $cleanResponse);
        
        // Clean up multiple empty lines
        $cleanResponse = preg_replace('/\n{3,}/', "\n\n", $cleanResponse);
        
        // Remove empty lines at the beginning and end
        $cleanResponse = trim($cleanResponse);
        
        return $cleanResponse;
    }
    
    public function get_date_ranges() {
        if (!$this->input->is_ajax_request()) {
            show_error('Direct access not allowed', 403);
        }
        
        $ranges = [
            ['label' => 'Today', 'value' => date('Y-m-d') . ' to ' . date('Y-m-d')],
            ['label' => 'Yesterday', 'value' => date('Y-m-d', strtotime('-1 day')) . ' to ' . date('Y-m-d', strtotime('-1 day'))],
            ['label' => 'Last 7 Days', 'value' => date('Y-m-d', strtotime('-7 days')) . ' to ' . date('Y-m-d')],
            ['label' => 'This Month', 'value' => date('Y-m-01') . ' to ' . date('Y-m-t')],
            ['label' => 'Last Month', 'value' => date('Y-m-01', strtotime('-1 month')) . ' to ' . date('Y-m-t', strtotime('-1 month'))],
            ['label' => 'Last 30 Days', 'value' => date('Y-m-d', strtotime('-30 days')) . ' to ' . date('Y-m-d')]
        ];
        
        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode([
                'success' => true,
                'data' => $ranges
            ]));
    }
    
    public function update_settings() {
        if (!$this->input->is_ajax_request()) {
            show_error('Direct access not allowed', 403);
        }
        
        $settings = $this->input->post();
        
        // Update session or database with settings
        // $this->session->set_userdata($settings);
        
        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode([
                'success' => true,
                'message' => 'Settings updated successfully'
            ]));
    }
    
    // Stub methods for different analysis types
    public function process_status_change($message) {
        return ['content' => 'Status Change analysis not yet implemented', 'data' => null];
    }
    
    public function process_funnel_analysis($message) {
        return ['content' => 'Funnel Analysis not yet implemented', 'data' => null];
    }
    
    public function process_action_analysis($message) {
        return ['content' => 'Action Analysis not yet implemented', 'data' => null];
    }
    
    public function process_day_in_day_out($message) {
        return ['content' => 'Day in Day Out analysis not yet implemented', 'data' => null];
    }
    
    public function process_efficiency_productivity($message) {
        return ['content' => 'Efficiency & Productivity analysis not yet implemented', 'data' => null];
    }
    
    public function process_daily_performance($message) {
        return ['content' => 'Daily Performance analysis not yet implemented', 'data' => null];
    }
    
    public function process_bd_performance($message) {
        return ['content' => 'BD Performance analysis not yet implemented', 'data' => null];
    }
    
    public function process_cm_performance($message) {
        return ['content' => 'CM Performance analysis not yet implemented', 'data' => null];
    }
}
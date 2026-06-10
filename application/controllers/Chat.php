



<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Chat extends CI_Controller {

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
        $this->load->model('AIAgents/FunnelsReport_model');
        $this->load->model('AIAgents/CompaniesWithoutPrimaryContact_model');
        $this->load->model('AIAgents/SameStatusSinceDays_model');
        $this->load->model('AIAgents/AfterAssignSameStatusSinceDays_model');
        $this->load->model('AIAgents/AfterRPMeetingsAssignSameStatus_model');
        $this->load->model('AIAgents/SameStatusWithTaskCount_model');
        $this->load->model('AIAgents/CompanyCreatedBetweenDates_model');
        $this->load->model('AIAgents/DeletedCompanyBetweenDates_model');
        $this->load->model('AIAgents/FunnelTransferLogs_model');
        $this->load->model('AIAgents/ClosurePipeLine_model');



        


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

        // dd($data);
        $this->load->view('Chat/ChatIindex', $data);
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

        // dd($rawResponse);
        
        // Process the raw response through ChatGPT
        $processedResponse = $this->process_response_with_chatgpt($rawResponse, $message, $analysisType, $previousMessages);

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
            case 'Funnel Details':
                $response = $this->FunnelsReport_model->process_FunnelsReport_model_analysis($message,$analysisType,$sdate, $edate);
                break;
            case 'Companies without primary contact':
                $response = $this->CompaniesWithoutPrimaryContact_model->process_CompaniesWithoutPrimaryContact_analysis($message,$analysisType,$sdate, $edate);
                break;
            case 'Same Status Since (n) Days':
                $response = $this->SameStatusSinceDays_model->process_SameStatusSinceDays_analysis($message,$analysisType,$sdate, $edate);
                break;
            case 'After Assign Same Status Since (n) Days':
                $response = $this->AfterAssignSameStatusSinceDays_model->process_AfterAssignSameStatusSinceDays_analysis($message,$analysisType,$sdate, $edate);
                break;
            case 'After RP Meetings Assign Same Status':
                $response = $this->AfterRPMeetingsAssignSameStatus_model->process_AfterRPMeetingsAssignSameStatus_analysis($message,$analysisType,$sdate, $edate);
                break;
            case 'Same Status With Task Count (On RP Leads)':
                $response = $this->SameStatusWithTaskCount_model->process_SameStatusWithTaskCount_analysis($message,$analysisType,$sdate, $edate);
                break;
            case 'Company Created Between Dates':
                $response = $this->CompanyCreatedBetweenDates_model->process_CompanyCreatedBetweenDates_analysis($message,$analysisType,$sdate, $edate);
                break;
            case 'Deleted Company Between Dates':
                $response = $this->DeletedCompanyBetweenDates_model->process_DeletedCompanyBetweenDates_analysis($message,$analysisType,$sdate, $edate);
                break;
            case 'Funnel Transfer Logs':
                $response = $this->FunnelTransferLogs_model->process_FunnelTransferLogs_analysis($message,$analysisType,$sdate, $edate);
                break;
            case 'Closure PipeLine':
                $response = $this->ClosurePipeLine_model->process_ClosurePipeLine_analysis($message,$analysisType,$sdate, $edate);
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
                // Use ChatGPT API for general queries
                $response['content'] = $this->call_chatgpt_api($message, $previousMessages);
                break;
        }
        
        return $response;
    }
    
    public function process_response_with_chatgpt($rawResponse, $originalMessage, $analysisType, $previousMessages) {
        // Prepare context for ChatGPT
        $context = $this->prepare_chatgpt_context($rawResponse, $originalMessage, $analysisType);
        
        // Build prompt for ChatGPT
        $prompt = $this->build_chatgpt_prompt($context, $analysisType);
        
        // Call ChatGPT API
        $chatgptResponse = $this->call_chatgpt_api_with_context($prompt, $previousMessages, $context);
        
        // Enhance response with data if available
        if (isset($rawResponse['data']) && !empty($rawResponse['data'])) {
            $chatgptResponse['data'] = $rawResponse['data'];
        }
        
        return $chatgptResponse;
    }
    
    public function prepare_chatgpt_context($rawResponse, $originalMessage, $analysisType) {
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
            default:
                $context['analysis_description'] = 'Business performance analysis';
                break;
        }
        
        return $context;
    }
    
    public function build_chatgpt_prompt($context, $analysisType) {
        $prompt = "You are a business analytics assistant. Analyze the following data and provide insights in a clear, concise, and actionable format.\n\n";
        
        $prompt .= "Analysis Type: {$context['analysis_type']}\n";
        $prompt .= "Date Range: {$context['date_range']['start_date']} to {$context['date_range']['end_date']}\n";
        $prompt .= "User Query: {$context['original_message']}\n\n";
        
        if (isset($context['raw_response']['content'])) {
            $prompt .= "Raw Analysis Results:\n{$context['raw_response']['content']}\n\n";
        }
        
        if (isset($context['raw_response']['data']) && is_array($context['raw_response']['data'])) {
            $dataSummary = $this->summarize_data_for_prompt($context['raw_response']['data']);
            $prompt .= "Data Summary:\n{$dataSummary}\n\n";
        }
        
        $prompt .= "Please provide:\n";
        $prompt .= "1. ".$context['original_message'].":\n";

        $prompt .= "Format the response in a professional, easy-to-read manner. Use bullet points where appropriate but avoid markdown formatting. Be specific and data-driven.";
        
        return $prompt;
    }
    
    public function summarize_data_for_prompt($data) {
        $summary = "";
        
        // Handle different data structures
        if (is_array($data)) {
            if (isset($data[0]) && is_array($data[0])) {
                // Array of records
                $recordCount = count($data);
                $summary .= "Total Records: {$recordCount}\n";
                
                // Get column names from first record
                if ($recordCount > 0) {
                    $columns = array_keys($data[0]);
                    $summary .= "Columns: " . implode(', ', $columns) . "\n";
                    
                    // Add sample data (first 3 records)
                    $summary .= "Sample Data (first 3 records):\n";
                    for ($i = 0; $i < min(3, $recordCount); $i++) {
                        $summary .= "Record " . ($i + 1) . ": " . json_encode($data[$i]) . "\n";
                    }
                }
            } else {
                // Single record or simple array
                $summary .= "Data Structure: " . json_encode($data) . "\n";
            }
        } else {
            $summary .= "Data: " . $data . "\n";
        }
        
        return $summary;
    }
    
    public function call_chatgpt_api_with_context($prompt, $previousMessages, $context) {
        // Configuration for OpenAI API
        $this->load->config('openai', TRUE); $api_key = $this->config->item('openai_api_key','openai') ?: (getenv('STEM_OPENAI_KEY') ?: '');// centralized 2026-06-10
        $api_url = 'https://api.openai.com/v1/chat/completions';
        
        // Build messages array
        $messages = [];
        
        // Add system message
        $messages[] = [
            'role' => 'system',
            'content' => "You are a helpful business analytics assistant that provides clear, actionable insights based on data analysis. Focus on being specific, data-driven, and professional."
        ];
        
        // Add previous messages if available
        if (is_array($previousMessages) && !empty($previousMessages)) {
            foreach ($previousMessages as $msg) {
                if (isset($msg['role']) && isset($msg['content'])) {
                    $messages[] = [
                        'role' => $msg['role'],
                        'content' => $msg['content']
                    ];
                }
            }
        }
        
        // Add current prompt
        $messages[] = [
            'role' => 'user',
            'content' => $prompt
        ];
        
        // Prepare API request data
        $requestData = [
            'model' => 'gpt-3.5-turbo', // or 'gpt-4' if available
            'messages' => $messages,
            'temperature' => 0.7,
            'max_tokens' => 4000,
            'top_p' => 1,
            'frequency_penalty' => 0,
            'presence_penalty' => 0
        ];
        
        // Initialize cURL
        $ch = curl_init();
        
        curl_setopt($ch, CURLOPT_URL, $api_url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($requestData));
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'Authorization: Bearer ' . $api_key
        ]);
        
        // Execute request
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curlError = curl_error($ch);
        
        curl_close($ch);
        
        // Process response
        if ($response === false) {
            // Fallback to mock response if API call fails
            return $this->generate_fallback_response($context);
        }
        
        $decodedResponse = json_decode($response, true);
        
        if ($httpCode !== 200 || !isset($decodedResponse['choices'][0]['message']['content'])) {
            // Fallback if API response is invalid
            return $this->generate_fallback_response($context);
        }
        
        // Return formatted response
        return [
            'content' => $decodedResponse['choices'][0]['message']['content'],
            'analysis_type' => $context['analysis_type'],
            'date_range' => $context['date_range']
        ];
    }
    
    public function generate_fallback_response($context) {
        // Generate a fallback response when ChatGPT API is not available
        $analysisType = $context['analysis_type'];
        $dateRange = $context['date_range'];
        
        $fallbackResponses = [
            'Team Detail' => "Based on team analysis for {$dateRange['start_date']} to {$dateRange['end_date']}, here are the key findings from the raw data.",
            'Task Analysis' => "Task analysis completed for the specified period. The raw data shows task completion rates and performance metrics.",
            'Meeting Details' => "Meeting analysis results are available. Review the detailed meeting statistics for insights.",
            'Proposal Details' => "Proposal tracking data analyzed. Check the proposal metrics and conversion rates.",
            'default' => "Analysis completed for {$analysisType}. Here are the results based on the data from {$dateRange['start_date']} to {$dateRange['end_date']}."
        ];
        
        $content = $fallbackResponses[$analysisType] ?? $fallbackResponses['default'];
        
        if (isset($context['raw_response']['content'])) {
            $content .= "\n\n" . $context['raw_response']['content'];
        }
        
        return [
            'content' => $content,
            'analysis_type' => $analysisType,
            'date_range' => $dateRange
        ];
    }
    
    public function replaceAsteriskWithSpace($data) {
        if (is_array($data)) {
            foreach ($data as $key => $value) {
                $data[$key] = $this->replaceAsteriskWithSpace($value);
            }
            return $data;
        }

        if (is_string($data)) {
            return str_replace(['*', '#'], '', $data);
        }

        return $data;
    }
    
    public function beautify_ai_response($chatgptResponse) {
        // Remove all # characters
        $cleanResponse = str_replace('#', '', $chatgptResponse);
        
        // Remove all * characters (both single and double)
        $cleanResponse = str_replace('*', '', $cleanResponse);
        
        // Remove any remaining markdown symbols
        $cleanResponse = str_replace(['**', '__', '~~'], '', $cleanResponse);
        
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
    
    // Stub methods for different analysis types (you need to implement these based on your business logic)
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

    // -----------------------------------------------------------------------
    // api_run_tool() — POST /index.php/chat/api_run_tool
    // Added 2026-06-06: fix C6. Dispatches tool calls from mobile tools.runTool().
    // Body: { tool: "tool_name", params: {...} }
    // -----------------------------------------------------------------------
    public function api_run_tool()
    {
        // Accept bearer auth
        $header = "";
        if (isset($_SERVER["HTTP_AUTHORIZATION"])) { $header = $_SERVER["HTTP_AUTHORIZATION"]; }
        if (!$header && isset($_SERVER["REDIRECT_HTTP_AUTHORIZATION"])) { $header = $_SERVER["REDIRECT_HTTP_AUTHORIZATION"]; }
        if (!$header && function_exists("getallheaders")) {
            foreach (getallheaders() as $k => $v) {
                if (strcasecmp($k, "Authorization") === 0) { $header = $v; break; }
            }
        }
        // Minimal auth: accept master bearer or valid api_token
        $token = trim(str_replace(["Bearer ", "Bearer"], "", $header));
        $master = "4eBaiAT7r4zu6OK3b8evjLNia1D7RGgb0qRTuLJfUSo";
        if ($token !== $master) {
            $user = $this->db->where("api_token", $token)->get("user")->row_array();
            if (!$user) {
                $this->output->set_status_header(401)->set_content_type("application/json")
                    ->set_output(json_encode(["ok" => false, "error" => "unauthorized"]));
                return;
            }
        }

        $raw    = file_get_contents("php://input");
        $body   = $raw ? json_decode($raw, true) : [];
        if (!$body) $body = [];
        $tool   = isset($body["tool"])   ? $body["tool"]   : "";
        $params = isset($body["params"]) ? $body["params"] : [];

        switch ($tool) {
            case "meeting_prep_artifact":
                $event_id = isset($params["event_id"]) ? (int)$params["event_id"] : 0;
                if (!$event_id) {
                    $this->output->set_status_header(422)->set_content_type("application/json")
                        ->set_output(json_encode(["ok" => false, "error" => "event_id required"]));
                    return;
                }
                $this->load->model("AIAgents/CorporateMeetingPrep_agent", "mprep");
                $r = $this->mprep->artifact_for_event($event_id);
                $this->output->set_status_header(200)->set_content_type("application/json")
                    ->set_output(json_encode($r));
                break;

            case "meeting_prep_runs_today":
                $bd_uid = isset($params["bd_uid"]) ? (int)$params["bd_uid"] : null;
                $this->load->model("AIAgents/CorporateMeetingPrep_agent", "mprep");
                $r = $this->mprep->runs_today($bd_uid);
                $this->output->set_status_header(200)->set_content_type("application/json")
                    ->set_output(json_encode($r));
                break;

            // --- Registry agents wired 2026-06-07 ---
            // Delegate to the self-contained AgentRunner_api HTTP endpoint via
            // an internal loopback request. Avoids double-instantiating a CI
            // controller (which breaks Session/autoload mid-request).
            case "mom_drafter":
            case "dump_mining":
            case "war_room":
            case "cm_copilot":
            case "cadence_star":
                $payload = json_encode(["agent_key" => $tool, "params" => (is_array($params) ? $params : [])]);
                $ch = curl_init("https://selfstagingstemapp.in/api/agent/run");
                curl_setopt_array($ch, [
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_POST => true,
                    CURLOPT_TIMEOUT => 60,
                    CURLOPT_HTTPHEADER => [
                        "Content-Type: application/json",
                        "Authorization: Bearer " . $master,
                    ],
                    CURLOPT_POSTFIELDS => $payload,
                ]);
                $resp = curl_exec($ch);
                $hc   = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                curl_close($ch);
                if ($resp === false || $resp === "") {
                    $resp = json_encode(["ok" => false, "error" => "agent_unreachable", "tool" => $tool]);
                    $hc = 502;
                }
                $this->output->set_status_header($hc ? $hc : 200)->set_content_type("application/json")
                    ->set_output($resp);
                break;

            default:
                $this->output->set_status_header(404)->set_content_type("application/json")
                    ->set_output(json_encode(["ok" => false, "error" => "unknown_tool", "tool" => $tool]));
        }
    }


}
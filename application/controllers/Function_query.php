<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Function_query extends CI_Controller {
    
    public function __construct() {
        parent::__construct();
        
        // Load helpers and libraries
        $this->load->helper(['url', 'form']);
        $this->load->library('session');
        
        // Load model
        $this->load->model('report_model');
        
        // Get current user ID from session
        $this->user_id = $this->session->userdata('user_id') ?: 1;
    }
    
    /**
     * Main dashboard - Show function keywords table
     */
    public function index() {
        $data = [];
        
        try {
            // Get function keywords from model
            $data['function_categories'] = $this->report_model->get_function_keywords();
            
            // Get all functions for search
            $data['all_functions'] = $this->report_model->get_all_functions();
            
            // Get recent queries from session
            $data['recent_queries'] = $this->session->userdata('recent_functions') ?: [];
            
        } catch (Exception $e) {
            $data['error'] = 'Error: ' . $e->getMessage();
        }
        
        $this->load->view('function_dashboard', $data);
    }
    
    /**
     * Execute function by keyword
     */
    public function execute_function() {
        $keyword = $this->input->post('keyword');
        $function_label = $this->input->post('function_label');
        
        if (empty($keyword) && empty($function_label)) {
            $this->session->set_flashdata('error', 'Please select or type a function keyword');
            redirect('function_query');
            return;
        }
        
        // Use function_label if provided (from table click), otherwise use keyword
        $selected_label = !empty($function_label) ? $function_label : $keyword;
        
        log_message('debug', 'Executing function for: ' . $selected_label);
        
        try {
            // Get all functions
            $all_functions = $this->report_model->get_all_functions();
            
            if (!isset($all_functions[$selected_label])) {
                $this->session->set_flashdata('error', 'Function not found for: ' . $selected_label);
                redirect('function_query');
                return;
            }
            
            $function_name = $all_functions[$selected_label];
            
            // Prepare parameters based on function
            $params = $this->prepare_function_parameters($function_name);
            
            // Execute the function
            $result = $this->report_model->execute_function($function_name, $params);
            
            if (isset($result['error'])) {
                $this->session->set_flashdata('error', 'Function Error: ' . $result['error']);
                redirect('function_query');
                return;
            }
            
            // Store in session for recent queries
            $recent = $this->session->userdata('recent_functions') ?: [];
            array_unshift($recent, [
                'label' => $selected_label,
                'function' => $function_name,
                'timestamp' => date('Y-m-d H:i:s'),
                'record_count' => is_array($result) ? count($result) : 0
            ]);
            
            // Keep only last 10 queries
            $recent = array_slice($recent, 0, 10);
            $this->session->set_userdata('recent_functions', $recent);
            
            // Prepare data for view
            $data = [
                'function_label' => $selected_label,
                'function_name' => $function_name,
                'result_data' => $result,
                'result_count' => is_array($result) ? count($result) : 0,
                'function_categories' => $this->report_model->get_function_keywords(),
                'recent_queries' => $recent
            ];
            
            // Generate table view
            $this->generate_result_view($data);
            
        } catch (Exception $e) {
            log_message('error', 'System Error: ' . $e->getMessage());
            $this->session->set_flashdata('error', 'System Error: ' . $e->getMessage());
            redirect('function_query');
        }
    }
    
    /**
     * Prepare parameters for function
     */
    private function prepare_function_parameters($function_name) {
        $params = [];
        
        // User ID parameter for user-specific functions
        $user_id_functions = [
            'GetAllUserByReportingManager',
            'GetTodaysMeetings',
            'GetPendingLeaveRequests',
            'GetTodaysTasks',
            'GetUserActivityLogs',
            'GetLeaveBalance',
            'GetUserProductivity'
        ];
        
        if (in_array($function_name, $user_id_functions)) {
            // Add user_id as first parameter
            array_push($params, $this->user_id);
            
            // Add additional parameters based on function
            switch ($function_name) {
                case 'GetAllUserByReportingManager':
                    // You can get these from POST/GET if needed
                    array_push($params, null, null, null); // admin_id_filter, rm_filter, sdate
                    break;
                case 'GetTodaysMeetings':
                case 'GetPendingLeaveRequests':
                case 'GetTodaysTasks':
                case 'GetUserActivityLogs':
                case 'GetLeaveBalance':
                    // Just user_id is enough
                    break;
                case 'GetUserProductivity':
                    array_push($params, 30); // days parameter
                    break;
            }
        }
        
        // Other functions with parameters
        switch ($function_name) {
            case 'GetRecentReviews':
                array_push($params, 50); // limit
                break;
            case 'GetRecentCashExpenses':
                array_push($params, 50); // limit
                break;
            case 'GetRecentLoginSessions':
                array_push($params, 50); // limit
                break;
            case 'GetUpcomingEvents':
                array_push($params, 7); // days
                break;
        }
        
        return $params;
    }
    
    /**
     * Generate result view with table
     */
    private function generate_result_view($data) {
        $this->load->view('function_result', $data);
    }
    
    /**
     * Execute multiple functions
     */
    public function execute_multiple() {
        $function_labels = $this->input->post('function_labels');
        
        if (empty($function_labels)) {
            $this->session->set_flashdata('error', 'Please select at least one function');
            redirect('function_query');
            return;
        }
        
        $function_labels = is_array($function_labels) ? $function_labels : explode(',', $function_labels);
        $all_results = [];
        
        foreach ($function_labels as $label) {
            $all_functions = $this->report_model->get_all_functions();
            
            if (isset($all_functions[$label])) {
                $function_name = $all_functions[$label];
                $params = $this->prepare_function_parameters($function_name);
                
                $result = $this->report_model->execute_function($function_name, $params);
                
                if (!isset($result['error'])) {
                    $all_results[$label] = [
                        'function' => $function_name,
                        'data' => $result,
                        'count' => is_array($result) ? count($result) : 0
                    ];
                }
            }
        }
        
        if (empty($all_results)) {
            $this->session->set_flashdata('error', 'No functions executed successfully');
            redirect('function_query');
            return;
        }
        
        // Store in session
        $recent = $this->session->userdata('recent_functions') ?: [];
        array_unshift($recent, [
            'label' => 'Multiple: ' . implode(', ', $function_labels),
            'timestamp' => date('Y-m-d H:i:s'),
            'record_count' => array_sum(array_column($all_results, 'count'))
        ]);
        $recent = array_slice($recent, 0, 10);
        $this->session->set_userdata('recent_functions', $recent);
        
        // Prepare data for view
        $data = [
            'multiple_results' => $all_results,
            'function_labels' => $function_labels,
            'function_categories' => $this->report_model->get_function_keywords(),
            'recent_queries' => $recent
        ];
        
        $this->load->view('multiple_results', $data);
    }
    
    /**
     * Clear recent queries
     */
    public function clear_recent() {
        $this->session->unset_userdata('recent_functions');
        redirect('function_query');
    }
    
    /**
     * Search functions
     */
    public function search_functions() {
        $search_term = $this->input->get('q');
        
        if (empty($search_term)) {
            echo json_encode([]);
            return;
        }
        
        $all_functions = $this->report_model->get_all_functions();
        $results = [];
        
        foreach ($all_functions as $label => $function) {
            if (stripos($label, $search_term) !== false) {
                $results[] = [
                    'label' => $label,
                    'function' => $function
                ];
            }
        }
        
        echo json_encode(array_slice($results, 0, 10));
    }
}
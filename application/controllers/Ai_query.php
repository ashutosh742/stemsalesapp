<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Ai_query extends CI_Controller {
    
    public function __construct() {
        parent::__construct();
        
        // Load helpers and libraries
        $this->load->helper(['url', 'form']);
        $this->load->library('session');
        
        // Load model
        $this->load->model('ai_query_model');
        
        // Load OpenAI library
        $this->load->library('openai');
    }
    
    /**
     * Main dashboard with keyword-based interface
     */
    public function index() {
        $data = [];
        
        try {
            $data['table_names'] = $this->ai_query_model->get_table_names();
            $data['total_tables'] = count($data['table_names']);
            
            // Get predefined queries
            $data['predefined_queries'] = $this->ai_query_model->get_predefined_queries();
            
            // Get recent queries from session
            $data['recent_queries'] = $this->session->userdata('recent_queries') ?: [];
            
            // Get some sample table data for preview
            $sample_tables = ['user_details', 'company_master', 'main_task', 'leave_requests'];
            $data['sample_data'] = [];
            foreach ($sample_tables as $table) {
                if (in_array($table, $data['table_names'])) {
                    $data['sample_data'][$table] = $this->ai_query_model->get_table_sample($table, 3);
                }
            }
            
        } catch (Exception $e) {
            $data['error'] = 'Error: ' . $e->getMessage();
        }
        
        $this->load->view('ai_query_dashboard', $data);
    }
    
    /**
     * Process natural language query with keyword support
     */
    public function process_query() {
        if (!$this->input->post('query')) {
            $this->session->set_flashdata('error', 'Please enter a query');
            redirect('ai_query');
            return;
        }
        
        $user_input = trim($this->input->post('query'));
        
        // Log the query
        log_message('debug', 'User query: ' . $user_input);
        
        try {
            // Get database information
            $all_schemas = $this->ai_query_model->get_all_table_schemas();
            $database_summary = $this->ai_query_model->get_database_summary();
            
            // Generate SQL using OpenAI - FIXED CALL
            $result = $this->openai->generate_sql($user_input, $database_summary, $all_schemas);
            
            if (!$result['success']) {
                // Try a fallback - use predefined queries based on keywords
                $fallback_sql = $this->get_fallback_query($user_input);
                if ($fallback_sql) {
                    $result = ['success' => true, 'sql' => $fallback_sql];
                } else {
                    $this->session->set_flashdata('error', 'AI Error: ' . $result['error'] . '. Please try rephrasing your question.');
                    redirect('ai_query');
                    return;
                }
            }
            
            $sql = $result['sql'];
            log_message('debug', 'Generated SQL: ' . $sql);
            
            // Execute the query
            $query_result = $this->ai_query_model->execute_query($sql);
            
            if (isset($query_result['error'])) {
                // Try to fix common SQL errors
                $fixed_sql = $this->fix_sql_errors($sql, $query_result['error']);
                if ($fixed_sql && $fixed_sql != $sql) {
                    $query_result = $this->ai_query_model->execute_query($fixed_sql);
                    $sql = $fixed_sql;
                }
                
                if (isset($query_result['error'])) {
                    $this->session->set_flashdata('error', 'Query Error: ' . $query_result['error']);
                    redirect('ai_query');
                    return;
                }
            }
            
            // Get AI explanation with business context - FIXED CALL
            $explanation = $this->openai->explain_results(
                $sql, 
                $query_result['data'], 
                $query_result['count'],
                $user_input
            );
            
            // Get related query suggestions - FIXED CALL
            $related_queries = $this->openai->suggest_related_queries($user_input, $all_schemas);
            
            // Store in session for recent queries
            $recent = $this->session->userdata('recent_queries') ?: [];
            array_unshift($recent, [
                'query' => $user_input,
                'sql' => $sql,
                'timestamp' => date('Y-m-d H:i:s'),
                'row_count' => $query_result['count']
            ]);
            
            // Keep only last 10 queries
            $recent = array_slice($recent, 0, 10);
            $this->session->set_userdata('recent_queries', $recent);
            
            // Prepare data for view
            $data = [
                'user_input' => $user_input,
                'generated_sql' => $sql,
                'query_results' => $query_result['data'],
                'result_count' => $query_result['count'],
                'columns' => $query_result['columns'],
                'ai_explanation' => $explanation,
                'related_queries' => $related_queries,
                'table_names' => $this->ai_query_model->get_table_names(),
                'recent_queries' => $recent,
                'predefined_queries' => $this->ai_query_model->get_predefined_queries()
            ];
            
            $this->load->view('ai_query_results', $data);
            
        } catch (Exception $e) {
            log_message('error', 'System Error in process_query: ' . $e->getMessage());
            $this->session->set_flashdata('error', 'System Error: ' . $e->getMessage());
            redirect('ai_query');
        }
    }
    
    /**
     * Get fallback query based on keywords
     */
    private function get_fallback_query($user_input) {
        $input_lower = strtolower($user_input);
        
        // Get predefined queries from model
        $predefined = $this->ai_query_model->get_predefined_queries();
        
        // Simple keyword matching
        $keywords = [
            'user' => ['user', 'employee', 'staff', 'login'],
            'task' => ['task', 'assignment', 'todo'],
            'meeting' => ['meeting', 'event', 'appointment'],
            'leave' => ['leave', 'holiday', 'vacation'],
            'company' => ['company', 'client', 'customer'],
            'sales' => ['sales', 'revenue', 'deal']
        ];
        
        // Find matching keyword
        foreach ($keywords as $category => $kw_list) {
            foreach ($kw_list as $keyword) {
                if (strpos($input_lower, $keyword) !== false) {
                    // Return a simple fallback query
                    switch($category) {
                        case 'user':
                            return "SELECT * FROM user_details LIMIT 10";
                        case 'task':
                            return "SELECT * FROM main_task WHERE status = 'active' LIMIT 10";
                        case 'meeting':
                            return "SELECT * FROM meeting_master ORDER BY meeting_date DESC LIMIT 10";
                        case 'leave':
                            return "SELECT * FROM leave_requests WHERE status = 'approved' ORDER BY start_date DESC LIMIT 10";
                        default:
                            return "SELECT * FROM {$category} LIMIT 10";
                    }
                }
            }
        }
        
        return null;
    }
    
    /**
     * Attempt to fix common SQL errors
     */
    private function fix_sql_errors($sql, $error) {
        // Fix unknown column errors
        if (strpos($error, 'Unknown column') !== false) {
            preg_match("/Unknown column '([^']+)'/", $error, $matches);
            if (isset($matches[1])) {
                $bad_column = $matches[1];
                // Remove the problematic column
                $sql = preg_replace('/,\s*' . preg_quote($bad_column) . '\b/i', '', $sql);
                $sql = preg_replace('/\b' . preg_quote($bad_column) . '\s*,/i', '', $sql);
                $sql = preg_replace('/SELECT\s+' . preg_quote($bad_column) . '\b/i', 'SELECT *', $sql);
                return $sql;
            }
        }
        
        // Fix syntax errors near quotes
        if (strpos($error, 'syntax') !== false) {
            // Remove any quotes that might be problematic
            $sql = str_replace('"', "'", $sql);
            return $sql;
        }
        
        return null;
    }
    
    /**
     * Smart search across all tables
     */
    public function smart_search() {
        $keyword = $this->input->post('search_keyword');
        
        if (empty($keyword)) {
            $this->session->set_flashdata('error', 'Please enter a search keyword');
            redirect('ai_query');
            return;
        }
        
        $results = $this->ai_query_model->smart_search($keyword, 5);
        
        $data = [
            'search_keyword' => $keyword,
            'search_results' => $results,
            'total_results' => 0,
            'table_names' => $this->ai_query_model->get_table_names()
        ];
        
        foreach ($results as $table => $table_results) {
            $data['total_results'] += $table_results['count'];
        }
        
        $this->load->view('search_results', $data);
    }
    
    /**
     * Execute predefined query
     */
    public function execute_predefined() {
        $query = $this->input->get('query');
        
        if (empty($query)) {
            $this->session->set_flashdata('error', 'No query specified');
            redirect('ai_query');
            return;
        }
        
        // Decode if encoded
        $query = urldecode($query);
        
        $query_result = $this->ai_query_model->execute_query($query);
        
        if (isset($query_result['error'])) {
            $this->session->set_flashdata('error', 'Query Error: ' . $query_result['error']);
            redirect('ai_query');
            return;
        }
        
        // Store in session
        $recent = $this->session->userdata('recent_queries') ?: [];
        array_unshift($recent, [
            'query' => "Predefined Query",
            'sql' => $query,
            'timestamp' => date('Y-m-d H:i:s'),
            'row_count' => $query_result['count']
        ]);
        $recent = array_slice($recent, 0, 10);
        $this->session->set_userdata('recent_queries', $recent);
        
        // Get explanation
        $explanation = $this->openai->explain_results($query, $query_result['data'], $query_result['count'], "Predefined query");
        
        $data = [
            'user_input' => "Predefined Query",
            'generated_sql' => $query,
            'query_results' => $query_result['data'],
            'result_count' => $query_result['count'],
            'columns' => $query_result['columns'],
            'ai_explanation' => $explanation,
            'related_queries' => [],
            'table_names' => $this->ai_query_model->get_table_names(),
            'recent_queries' => $recent,
            'predefined_queries' => $this->ai_query_model->get_predefined_queries()
        ];
        
        $this->load->view('ai_query_results', $data);
    }
    
    /**
     * View table data
     */
    public function view_table($table_name = null) {
        if (!$table_name) {
            $table_name = $this->input->post('table_name');
        }
        
        $tables = $this->ai_query_model->get_table_names();
        
        if (!in_array($table_name, $tables)) {
            $this->session->set_flashdata('error', 'Table not found: ' . $table_name);
            redirect('ai_query');
            return;
        }
        
        $page = $this->input->get('page') ?: 1;
        $limit = 50;
        $offset = ($page - 1) * $limit;
        
        // Get table data
        $this->db->limit($limit, $offset);
        $data['table_data'] = $this->db->get($table_name)->result_array();
        $data['table_name'] = $table_name;
        $data['columns'] = $this->db->list_fields($table_name);
        $data['row_count'] = count($data['table_data']);
        $data['total_rows'] = $this->get_table_row_count($table_name);
        $data['current_page'] = $page;
        $data['total_pages'] = ceil($data['total_rows'] / $limit);
        
        $this->load->view('table_view', $data);
    }
    
    private function get_table_row_count($table) {
        $result = $this->db->query("SELECT COUNT(*) as total FROM " . $this->db->escape_identifiers($table))->row();
        return $result->total;
    }
    
    /**
     * Clear recent queries
     */
    public function clear_recent() {
        $this->session->unset_userdata('recent_queries');
        redirect('ai_query');
    }
}
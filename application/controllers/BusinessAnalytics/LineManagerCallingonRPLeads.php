<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class LineManagerCallingonRPLeads extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('AIAgents/LineManagerCallingonRPLeads_model');
        $this->load->model('Report_model');
        $this->load->helper('url');
        $this->load->library('session');
        
        // Check authentication
        if (!$this->session->userdata('user')) {
            redirect('login');
        }
    }

    /**
     * Main analysis page
     */
    public function index() {
        $data = [
            'page_title' => 'Line Manager Calling on RP Leads Analysis',
            'user_id' => $this->session->userdata('user_id'),
            'username' => $this->session->userdata('username'),
            'user_type' => $this->session->userdata('type_id')
        ];
        
        $this->load->view('BusinessAnalyticsView/line_manager_calling_analysis', $data);

    }

    /**
     * Get analysis data via AJAX
     */
    public function get_analysis_data() {
        // Set JSON header
        header('Content-Type: application/json');
        
        // Get request parameters
        $message = $this->input->post('message') ?? 'Show me the line manager calling analysis';
        $analysisType = 'Line Manager Calling on RP Leads';
        $sdate = $this->input->post('start_date') ?? date('Y-m-d', strtotime('-30 days'));
        $edate = $this->input->post('end_date') ?? date('Y-m-d');
        $specific_user_id = $this->input->post('specific_user_id');
        
        // Validate dates
        if (!strtotime($sdate) || !strtotime($edate)) {
            echo json_encode([
                'success' => false,
                'message' => 'Invalid date format. Use YYYY-MM-DD'
            ]);
            return;
        }
        
        // Add specific user to message if provided
        if ($specific_user_id) {
            // Get username from user_id
            $this->load->model('User_model');
            $user_info = $this->User_model->get_user_by_id($specific_user_id);
            if ($user_info) {
                $message .= " for user " . $user_info->username;
            }
        }
        
        try {
            // Process analysis
            $analysisResult = $this->LineManagerCallingonRPLeads_model->process_LineManagerCallingonRPLeads_analysis(
                $message,
                $analysisType,
                $sdate,
                $edate
            );
            
            // Add success flag and timestamp
            $analysisResult['success'] = true;
            $analysisResult['timestamp'] = date('Y-m-d H:i:s');
            $analysisResult['date_range'] = $sdate . ' to ' . $edate;
            
            echo json_encode($analysisResult);
            
        } catch (Exception $e) {
            echo json_encode([
                'success' => false,
                'message' => 'Error processing analysis: ' . $e->getMessage(),
                'timestamp' => date('Y-m-d H:i:s')
            ]);
        }
    }

    /**
     * Get available users for dropdown
     */
    public function get_available_users() {
        header('Content-Type: application/json');
        
        try {
            // Load User model
            $this->load->model('User_model');
            
            // Get current user's team or all users based on permissions
            $current_user_id = $this->session->userdata('user_id');
            $user_type = $this->session->userdata('type_id');
            
            // Adjust based on user permissions
            if (in_array($user_type, [1, 2])) { // Admin/Manager
                $users = $this->User_model->get_active_users();
            } else {
                // Get team members or reporting line
                $users = $this->User_model->get_team_members($current_user_id);
            }
            
            // Format for dropdown
            $user_list = [];
            foreach ($users as $user) {
                $user_list[] = [
                    'id' => $user->id,
                    'username' => $user->username,
                    'full_name' => $user->full_name ?? $user->username
                ];
            }
            
            echo json_encode([
                'success' => true,
                'users' => $user_list
            ]);
            
        } catch (Exception $e) {
            echo json_encode([
                'success' => false,
                'message' => 'Error fetching users: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Export analysis data to CSV/Excel
     */
    public function export_analysis() {
        // Get request parameters
        $format = $this->input->get('format') ?? 'csv';
        $analysis_type = $this->input->get('analysis_type') ?? 'team';
        $sdate = $this->input->get('start_date') ?? date('Y-m-d', strtotime('-30 days'));
        $edate = $this->input->get('end_date') ?? date('Y-m-d');
        
        // Process analysis
        $message = "Export line manager calling analysis";
        $analysisResult = $this->LineManagerCallingonRPLeads_model->process_LineManagerCallingonRPLeads_analysis(
            $message,
            $analysis_type,
            $sdate,
            $edate
        );
        
        if ($format == 'excel') {
            $this->export_to_excel($analysisResult, $sdate, $edate);
        } else {
            $this->export_to_csv($analysisResult, $sdate, $edate);
        }
    }

    /**
     * Export to CSV
     */
    private function export_to_csv($analysisResult, $start_date, $end_date) {
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="line_manager_calling_analysis_' . date('Ymd_His') . '.csv"');
        
        $output = fopen('php://output', 'w');
        
        // Write header
        fputcsv($output, ['Line Manager Calling on RP Leads Analysis']);
        fputcsv($output, ['Date Range', $start_date . ' to ' . $end_date]);
        fputcsv($output, ['Generated On', date('Y-m-d H:i:s')]);
        fputcsv($output, []); // Empty line
        
        // Write AI analysis summary
        if (isset($analysisResult['content'])) {
            fputcsv($output, ['AI Analysis Summary']);
            fputcsv($output, [strip_tags($analysisResult['content'])]);
            fputcsv($output, []);
        }
        
        // Write table data if available
        if (isset($analysisResult['data']['tableData'])) {
            $tableData = $analysisResult['data']['tableData'];
            fputcsv($output, ['Performance Analysis']);
            fputcsv($output, $tableData['headers']);
            
            foreach ($tableData['rows'] as $row) {
                fputcsv($output, $row);
            }
            fputcsv($output, []);
        }
        
        // Write summary data
        if (isset($analysisResult['data']['summaryData'])) {
            $summary = $analysisResult['data']['summaryData'];
            fputcsv($output, ['Summary Statistics']);
            
            foreach ($summary as $key => $value) {
                if ($key == 'key_insights' || $key == 'sales_insights') {
                    fputcsv($output, [ucfirst(str_replace('_', ' ', $key))]);
                    if (is_array($value)) {
                        foreach ($value as $insight) {
                            fputcsv($output, ['- ' . $insight]);
                        }
                    }
                } else {
                    fputcsv($output, [ucfirst(str_replace('_', ' ', $key)), $value]);
                }
            }
        }
        
        fclose($output);
    }

    /**
     * Export to Excel (simplified - using CSV with .xls extension)
     */
    private function export_to_excel($analysisResult, $start_date, $end_date) {
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment; filename="line_manager_calling_analysis_' . date('Ymd_His') . '.xls"');
        
        echo '<html>';
        echo '<head>';
        echo '<meta charset="UTF-8">';
        echo '<style>';
        echo 'table { border-collapse: collapse; width: 100%; }';
        echo 'th { background-color: #4CAF50; color: white; padding: 8px; text-align: left; }';
        echo 'td { border: 1px solid #ddd; padding: 8px; }';
        echo 'tr:nth-child(even) { background-color: #f2f2f2; }';
        echo '.summary { background-color: #e8f5e8; padding: 10px; margin: 10px 0; }';
        echo '</style>';
        echo '</head>';
        echo '<body>';
        
        echo '<h1>Line Manager Calling on RP Leads Analysis</h1>';
        echo '<p><strong>Date Range:</strong> ' . $start_date . ' to ' . $end_date . '</p>';
        echo '<p><strong>Generated On:</strong> ' . date('Y-m-d H:i:s') . '</p>';
        echo '<hr>';
        
        // AI Analysis
        if (isset($analysisResult['content'])) {
            echo '<div class="summary">';
            echo '<h2>AI Analysis Summary</h2>';
            echo '<p>' . nl2br(strip_tags($analysisResult['content'])) . '</p>';
            echo '</div>';
            echo '<hr>';
        }
        
        // Performance Table
        if (isset($analysisResult['data']['tableData'])) {
            $tableData = $analysisResult['data']['tableData'];
            echo '<h2>Performance Analysis</h2>';
            echo '<table>';
            echo '<tr>';
            foreach ($tableData['headers'] as $header) {
                echo '<th>' . $header . '</th>';
            }
            echo '</tr>';
            
            foreach ($tableData['rows'] as $row) {
                echo '<tr>';
                foreach ($row as $cell) {
                    echo '<td>' . $cell . '</td>';
                }
                echo '</tr>';
            }
            echo '</table>';
            echo '<hr>';
        }
        
        // Summary Data
        if (isset($analysisResult['data']['summaryData'])) {
            $summary = $analysisResult['data']['summaryData'];
            echo '<h2>Summary Statistics</h2>';
            echo '<table>';
            
            foreach ($summary as $key => $value) {
                if ($key == 'key_insights' || $key == 'sales_insights') {
                    echo '<tr><td colspan="2"><strong>' . ucfirst(str_replace('_', ' ', $key)) . '</strong></td></tr>';
                    if (is_array($value)) {
                        foreach ($value as $insight) {
                            echo '<tr><td colspan="2">- ' . $insight . '</td></tr>';
                        }
                    }
                } else {
                    echo '<tr>';
                    echo '<td><strong>' . ucfirst(str_replace('_', ' ', $key)) . '</strong></td>';
                    echo '<td>' . $value . '</td>';
                    echo '</tr>';
                }
            }
            echo '</table>';
        }
        
        echo '</body>';
        echo '</html>';
    }
}
<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class ProposalDetails extends CI_Controller {

    public function __construct()
    {
        parent::__construct();
        $this->load->model('AIAgents/ProposalDetails_model');
        $this->load->helper('url');
        $this->load->library('session');

        $this->uid = $this->session->userdata('user')['user_id'] ?? 0;
        $this->sdate = date('Y-m-01'); // First day of current month
        $this->edate = date('Y-m-t'); // Last day of current month
        $this->task_action_id = 'all'; // Default: all task actions
        $this->admin_id_filter = $this->uid; // Default: all admins
        $this->rm_filter = $this->uid; // Default: all RMs
        $this->userwise = 'userwise'; // Default: all users
        $this->target_uid = 0; // Default: target user
        $this->mtypes = ''; // Default: all mtypes
        $this->plannerDate = date('Y-m-d'); // Current date for planner
       
    }

    /**
     * Main dashboard page
     */
    public function index()
    {

        // Check if user is logged in
        // if (!$this->session->userdata('user_id')) {
        //     redirect('login');
        // }

        $data['title'] = 'Proposal Details Dashboard - Business Analytics AI';
        $data['active_menu'] = 'proposal-details';
        $data['user_id'] = $this->session->userdata('user_id');
        
        $this->load->view('BusinessAnalyticsView/ProposalDetailsDashboard', $data);

    }

    /**
     * Get proposal analysis data
     */
    public function get_proposal_analysis()
    {
        header('Content-Type: application/json');
        


        try {
            // Get parameters from request
            $message = $this->input->post('message') ?? 'Show me detailed proposal analysis with top 10';
            $analysisType = $this->input->post('analysis_type') ?? 'comprehensive';
            $sdate = $this->input->post('start_date') ?? date('Y-m-d', strtotime('-30 days'));
            $edate = $this->input->post('end_date') ?? date('Y-m-d');
            


            // Set user ID from session
            $this->ProposalDetails_model->uid = $this->uid;
            
            // Process proposal analysis
            $result = $this->ProposalDetails_model->process_ProposalDetails_execution(
                $message,
                $analysisType,
                $sdate,
                $edate
            );
            
            // Add metadata
            $result['metadata'] = [
                'generated_at' => date('Y-m-d H:i:s'),
                'period' => "$sdate to $edate",
                'analysis_type' => $analysisType,
                'user_message' => $message
            ];
            
            echo json_encode([
                'status' => 'success',
                'data' => $result
            ]);
            
        } catch (Exception $e) {
            echo json_encode([
                'status' => 'error',
                'message' => 'Analysis failed: ' . $e->getMessage(),
                'data' => null
            ]);
        }
    }

    /**
     * Quick analysis endpoints
     */
    public function quick_top_companies($count = 10)
    {
        header('Content-Type: application/json');
        
        try {
            $this->ProposalDetails_model->uid = $this->session->userdata('user_id');
            $sdate = date('Y-m-d', strtotime('-30 days'));
            $edate = date('Y-m-d');
            
            $result = $this->ProposalDetails_model->process_ProposalDetails_execution(
                "Show me top {$count} companies",
                'companies',
                $sdate,
                $edate
            );
            
            echo json_encode([
                'status' => 'success',
                'data' => $result
            ]);
            
        } catch (Exception $e) {
            echo json_encode([
                'status' => 'error',
                'message' => $e->getMessage()
            ]);
        }
    }

    public function quick_top_users($count = 10)
    {
        header('Content-Type: application/json');
        
        try {
            $this->ProposalDetails_model->uid = $this->session->userdata('user_id');
            $sdate = date('Y-m-d', strtotime('-30 days'));
            $edate = date('Y-m-d');
            
            $result = $this->ProposalDetails_model->process_ProposalDetails_execution(
                "Show me top {$count} users",
                'users',
                $sdate,
                $edate
            );
            
            echo json_encode([
                'status' => 'success',
                'data' => $result
            ]);
            
        } catch (Exception $e) {
            echo json_encode([
                'status' => 'error',
                'message' => $e->getMessage()
            ]);
        }
    }

    public function quick_financial_summary()
    {
        header('Content-Type: application/json');
        
        try {
            $this->ProposalDetails_model->uid = $this->session->userdata('user_id');
            $sdate = date('Y-m-d', strtotime('-30 days'));
            $edate = date('Y-m-d');
            
            $result = $this->ProposalDetails_model->process_ProposalDetails_execution(
                "Show me financial analysis",
                'Proposal Details',
                $sdate,
                $edate
            );
            
            echo json_encode([
                'status' => 'success',
                'data' => $result
            ]);
            
        } catch (Exception $e) {
            echo json_encode([
                'status' => 'error',
                'message' => $e->getMessage()
            ]);
        }
    }

    /**
     * Export data to different formats
     */
    public function export($format = 'json')
    {
        try {
            $this->ProposalDetails_model->uid = $this->session->userdata('user_id');
            $sdate = $this->input->get('start_date') ?? date('Y-m-d', strtotime('-30 days'));
            $edate = $this->input->get('end_date') ?? date('Y-m-d');
            
            $result = $this->ProposalDetails_model->process_ProposalDetails_execution(
                "Show me comprehensive proposal analysis with all details",
                'export',
                $sdate,
                $edate
            );
            
            switch ($format) {
                case 'csv':
                    $this->export_csv($result);
                    break;
                case 'excel':
                    $this->export_excel($result);
                    break;
                case 'pdf':
                    $this->export_pdf($result);
                    break;
                default:
                    $this->export_json($result);
            }
            
        } catch (Exception $e) {
            echo "Export failed: " . $e->getMessage();
        }
    }

    private function export_json($data)
    {
        header('Content-Type: application/json');
        header('Content-Disposition: attachment; filename="proposal_analysis_' . date('Ymd_His') . '.json"');
        echo json_encode($data, JSON_PRETTY_PRINT);
    }

    private function export_csv($data)
    {
        // CSV export logic here
        // This is a simplified version - you'd need to implement full CSV generation
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="proposal_analysis_' . date('Ymd_His') . '.csv"');
        
        // Add CSV headers and data
        echo "Category,Value\n";
        if (isset($data['data']['summary'])) {
            foreach ($data['data']['summary'] as $key => $value) {
                echo "$key,$value\n";
            }
        }
    }

    private function export_excel($data)
    {
        // Excel export would require PHPExcel or similar library
        // Placeholder implementation
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment; filename="proposal_analysis_' . date('Ymd_His') . '.xls"');
        echo "<table border='1'>";
        // Add table rows from data
        echo "</table>";
    }

    private function export_pdf($data)
    {
        // PDF export would require TCPDF or similar library
        // Placeholder implementation
        header('Content-Type: application/pdf');
        header('Content-Disposition: attachment; filename="proposal_analysis_' . date('Ymd_His') . '.pdf"');
        echo "PDF Export - Implementation required";
    }
}
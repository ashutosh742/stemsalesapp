<?php
defined('BASEPATH') OR exit('No direct script access allowed');
date_default_timezone_set("Asia/Kolkata");
/**
 * @property CI_Config $config
 * @property CI_Session $session
 * @property CI_Email $email
 * @property CI_Input $input
 * @property CI_DB_query_builder $db
 * @property Menu_model $Menu_model
 * @property Report_model $Report_model
 */
class MasterReset extends CI_Controller {
/**
 * Constructor
 *
 * This constructor is used to load models, libraries, helpers, etc. that are required by the MasterReset controller.
 */
    public function __construct() {
        parent::__construct();
        // Load models, libraries, helpers, etc.
        $this->load->model('Menu_model');
        $this->load->model('Report_model');
        $this->load->model('MasterReset_model');
        $this->load->helper('common_helper');
        $this->load->helper('samestatustilldate_helper');
        $this->load->helper('taskPlanner_helper');
    }
  
/**
 * Index
 *
 * This function is used to render the Master Reset view.
 * It checks if the user has permission to access the page and if not,
 * it redirects to the dashboard with an error message.
 *
 * @return void
 */
    public function index() {
        $user = $this->session->userdata('user');
        if (empty($user)) {redirect('Menu/main');}
        $uid        = $user['user_id'];
        $uyid       = $user['type_id'];
        $this->load->library('session');

        if(!in_array($uyid,[1])){

            $this->session->set_flashdata('error_message'," * You don't have permission to access this page.");
            redirect('Menu/Dashboard');

        }

       $month_number = date("n"); // 1 to 12
       if ($month_number !== 4) {
            $this->session->set_flashdata('error_message'," * You can only access this page in Only April Month.");
            redirect('Menu/Dashboard');
       }

        $dt         = $this->Menu_model->get_utype($uyid);
        $dep_name   = isset($dt[0]->name) ? $dt[0]->name : 'default';

        $cdate = date("Y-m-d");
        $this->load->view('MasterReset/index', [
            'uid'           => $uid,
            'user'          => $user,
            'dep_name'      => $dep_name,
            'cdate'         => $cdate,
        ]);

    }

/**
 * ZoneWiseReset
 *
 * This function is used to render the ZoneWiseReset view. It checks
 * if the user has permission to access the page and if not,
 * it redirects to the dashboard with an error message.
 *
 * @return void
 */
    public function ZoneWiseReset() {
        $user = $this->session->userdata('user');
        if (empty($user)) {redirect('Menu/main');}
        $uid        = $user['user_id'];
        $uyid       = $user['type_id'];

        if(!in_array($uyid,[1])){
            $this->load->library('session');
            $this->session->set_flashdata('error_message'," * You don't have permission to access this page.");
            redirect('Menu/Dashboard');
        }

        $month_number = date("n"); // 1 to 12
        if ($month_number !== 4) {
                $this->session->set_flashdata('error_message'," * You can only access this page in Only April Month.");
                redirect('Menu/Dashboard');
        }

        $dt                         = $this->Menu_model->get_utype($uyid);
        $dep_name                   = isset($dt[0]->name) ? $dt[0]->name : 'default';
        $getLastFinancialYear       = $this->MasterReset_model->getLastFinancialYear();
        $funnelDatas                = $this->MasterReset_model->GetBDFunnelsDatas_ZoneWiseReset($getLastFinancialYear);

        $cdate = date("Y-m-d");
        $this->load->view('MasterReset/ZoneWiseReset', [
            'uid'           => $uid,
            'user'          => $user,
            'dep_name'      => $dep_name,
            'cdate'         => $cdate,
            'funnelDatas'   => $funnelDatas
        ]);
    }

/**
 * BDWiseReset
 *
 * This function is used to render the BDWiseReset view. It checks
 * if the user has permission to access the page and if not,
 * it redirects to the dashboard with an error message.
 *
 * @return void
 */
    public function BDWiseReset() {
        $user = $this->session->userdata('user');
        if (empty($user)) {redirect('Menu/main');}
        $uid        = $user['user_id'];
        $uyid       = $user['type_id'];

        if(!in_array($uyid,[1])){
            $this->load->library('session');
            $this->session->set_flashdata('error_message'," * You don't have permission to access this page.");
            redirect('Menu/Dashboard');
        }

        $month_number = date("n"); // 1 to 12
        if ($month_number !== 4) {
                $this->session->set_flashdata('error_message'," * You can only access this page in Only April Month.");
                redirect('Menu/Dashboard');
        }

        $dt         = $this->Menu_model->get_utype($uyid);
        $dep_name   = isset($dt[0]->name) ? $dt[0]->name : 'default';

        $getLastFinancialYear       = $this->MasterReset_model->getLastFinancialYear();
        $funnelDatas                = $this->MasterReset_model->GetBDFunnelsDatas_BDWiseReset($getLastFinancialYear);

        $cdate = date("Y-m-d");
        $this->load->view('MasterReset/BDWiseReset', [
            'uid'           => $uid,
            'user'          => $user,
            'dep_name'      => $dep_name,
            'cdate'         => $cdate,
            'funnelDatas'   => $funnelDatas
        ]);
    }


    // ************************************** Master Reset Start Here **************************************

/**
 * RestFunnel
 *
 * Resets the specified type of data and returns a success message and
 * the new total count if the reset is successful.
 *
 * @param string $type The type of data to reset (e.g. 'zone' or 'bd')
 * @param int $id The ID of the data to reset
 *
 * @return void
 */
/*
       public function RestFunnel() {
        // Check if it's an AJAX request (optional)
        if (!$this->input->is_ajax_request()) {
            show_404();
        }

        // Validate CSRF token if enabled
        if ($this->config->item('csrf_protection')) {
            $csrf_token = $this->security->get_csrf_token_name();
            $csrf_hash = $this->security->get_csrf_hash();
            $posted_token = $this->input->post($csrf_token);
            if ($posted_token !== $csrf_hash) {
                $this->output
                    ->set_content_type('application/json')
                    ->set_status_header(400)
                    ->set_output(json_encode(['success' => false, 'message' => 'Invalid CSRF token']));
                return;
            }
        }

        // Get POST data
        $type = $this->input->post('type');
        $id = $this->input->post('id');

        // Validate input
        if (empty($type) || empty($id)) {
            $this->output
                ->set_content_type('application/json')
                ->set_status_header(400)
                ->set_output(json_encode(['success' => false, 'message' => 'Missing parameters']));
            return;
        }

        // Perform reset based on type
        $result = false;
        $new_total = 0;

        if ($type === 'zone') {
            // Example: reset something in zones table
          
             $result    = true;
             $new_total = 100;


            $funnelDatas = $this->MasterReset_model->GetBDFunnelsDatas_zoneID($id);
            $new_total=1;
            foreach($funnelDatas as $funnelDatas){
                
                $main_bd_id     = $funnelDatas->main_bd_id;
                $bd_name        = $funnelDatas->bd_name;
                $init_id        = $funnelDatas->init_id;
                $cid            = $funnelDatas->cid;
                $cstatus        = $funnelDatas->cstatus;
                $lstatus        = $funnelDatas->lstatus;

                $new_total++;
            }

        } elseif ($type === 'bd') {
            // Example: reset something in bd table

            $result     = true;
            $new_total  = 100;


            $funnelDatas = $this->MasterReset_model->GetBDFunnelsDatas_BDID($id);
            $new_total=1;
            foreach($funnelDatas as $funnelDatas){
                
                $main_bd_id     = $funnelDatas->main_bd_id;
                $bd_name        = $funnelDatas->bd_name;
                $init_id        = $funnelDatas->init_id;
                $cid            = $funnelDatas->cid;
                $cstatus        = $funnelDatas->cstatus;
                $lstatus        = $funnelDatas->lstatus;

                $new_total++;
            }

        } else {
            $this->output
                ->set_content_type('application/json')
                ->set_status_header(400)
                ->set_output(json_encode(['success' => false, 'message' => 'Invalid type']));
            return;
        }

        if ($result) {
            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'success' => true,
                    'message' => ucfirst($type) . ' reset successfully',
                    'new_total' => $new_total
                ]));
        } else {
            $this->output
                ->set_content_type('application/json')
                ->set_status_header(500)
                ->set_output(json_encode(['success' => false, 'message' => 'Reset failed']));
        }
    }

    */


    public function RestFunnel() {

    // Remove time limit
    set_time_limit(0);
    ini_set('max_execution_time', 0);

    $user       = $this->session->userdata('user');
    if (empty($user)) {redirect('Menu/main');}
    $uid        = $user['user_id'];

    // Only accept AJAX requests
    if (!$this->input->is_ajax_request()) {
        show_404();
    }

    // Validate CSRF token automatically (if enabled)
    if ($this->config->item('csrf_protection')) {
        if (!$this->security->csrf_verify()) {
            $this->output
                ->set_content_type('application/json')
                ->set_status_header(400)
                ->set_output(json_encode([
                    'success' => false,
                    'message' => 'Invalid CSRF token'
                ]));
            return;
        }
    }

    $this->load->library('session');

    // Get POST data
    $type = $this->input->post('type');
    $id   = $this->input->post('id');

    if (empty($type) || empty($id)) {
        $this->output
            ->set_content_type('application/json')
            ->set_status_header(400)
            ->set_output(json_encode([
                'success' => false,
                'message' => 'Missing parameters'
            ]));
        return;
    }

    $result = false;
    $processed = 0;
    $total = 0;
    $message = '';

    // Load the model if not already loaded
    $this->load->model('MasterReset_model');

    if ($type === 'zone') {

        $zone_id        = $id;
        $funnelEntries  = $this->MasterReset_model->GetBDFunnelsDatas_zoneID($zone_id);
        $total          = sizeof($funnelEntries);
        $result         = $total;

        if ($total > 0) {

            foreach($funnelEntries as $funnelDatas){
                
                // *********** Get Company OwenShip ********** //
                $mainbd                 = $funnelDatas->main_bd_id;
                $bd_name                = $funnelDatas->bd_name;

                // *********** Get Company Mapping ID ********** //
                $init_id                = $funnelDatas->init_id;
                $cid                    = $funnelDatas->cid;
                // ************** Get Company Status ************ //
                $cstatus                = $funnelDatas->cstatus;
                $lstatus                = $funnelDatas->lstatus;

                // ********** Get All User Mapped Data ********** //
                $clm_id                 = $funnelDatas->clm_id;
                $apst                   = $funnelDatas->apst;
                $rm_east_co_id          = $funnelDatas->rm_east_co_id;
                $rm_north_co_id         = $funnelDatas->rm_north_co_id;
                $ash_nae_co_id          = $funnelDatas->ash_nae_co_id;
                $ash_w_co_id            = $funnelDatas->ash_w_co_id;
                $ash_s_co_id            = $funnelDatas->ash_s_co_id;
                $acm_co_id              = $funnelDatas->acm_co_id;

                // Get Main BD User Details
                $mainBDUserDetail       = $this->Menu_model->get_userbyid($mainbd); 
                $rm_user_id             = 	$mainBDUserDetail[0]->rm_east_co;       // Regional Manager
                $acm_user_id            = 	$mainBDUserDetail[0]->acm_co;           // Assistant Cluster Manager
                $cm_user_id             = 	$mainBDUserDetail[0]->aadmin;           // Cluster Manager

                // Check Primary Contact
                $primaryContact         = $this->MasterReset_model->CheckPrimaryContactExistsOrNotinTable($cid);
 
                if(sizeof($primaryContact) > 0){
                    $resetCStatus = 8;
                }else{
                    $resetCStatus = 1;
                }

                // Check Current Financial Year RP Meeting
                $checkCFRPMeeting       = $this->MasterReset_model->CheckCurrentFinancialYearRPMeetingDoneOrNot($init_id,$sdate,$edate);
                if(sizeof($checkCFRPMeeting) > 0){
                    $resetCStatus = 3;

                    $mappedUserInitialData = array(
                        'clm_id'                        => "$cm_user_id",
                        'acm_co_id'                     => "$acm_user_id",
                    );
                    $this->db->where('id', $init_id); 
                    $this->db->update('init_call_bkp', $mappedUserInitialData);

                }

                // Check Current Financial Year Proposal
                $checkCFProposal        = $this->MasterReset_model->CheckCurrentFinancialYearProposalDoneOrNot($init_id,$sdate,$edate,$mainbd);
                if(sizeof($checkCFProposal) > 0){
                    $resetCStatus = 6;

                    $mappedUserInitialData = array(
                        'rm_east_co_id'                 => "$rm_user_id",
                        'clm_id'                        => "$cm_user_id",
                        'acm_co_id'                     => "$acm_user_id",
                    );
                    $this->db->where('id', $init_id); 
                    $this->db->update('init_call_bkp', $mappedUserInitialData);

                }
                 // Reset Company Funnel
                $initResetData = array(
                    'cstatus'                       => $resetCStatus,
                    'lstatus'                       => $cstatus,
                    'insidebd'                      => 0,
                    'bdid'                          => 0,
                    'abd'                           => 0,
                    'apst'                          => 0,
                    'ash_nae_co_id'                 => 0,
                    'ash_w_co_id'                   => 0,
                    'ash_s_co_id'                   => 0,
                    'rm_east_co_id'                 => 0,
                    'rm_north_co_id'                => 0,
                    'acm_co_id'                     => 0,
                    'bpst'                          => 0,
                    'cpst'                          => 0,
                    'clm_id'                        => 0,
                    'super_admin'                   => 0,
                );

                $this->db->where('id', $init_id); 
                $this->db->update('init_call_bkp', $initResetData);

                if ($this->db->affected_rows() > 0) {
                     // Insert Reset Logs
                        $resetlogsdata = array(
                            'reset_type'                      => $type,
                            'init_id'                         => $init_id,
                            'cid'                             => $cid,
                            'old_clm_bd'                      => "$clm_id",
                            'old_status'                      => $cstatus,
                            'bd_id'                           => "$bd_id",
                            'new_status'                      => $resetCStatus,
                            'new_clm_bd'                      => 0,
                            'old_apst'                        => "$apst",
                            'new_apst'                        => 0,
                            'old_rm_east'                     => "$rm_east_co_id",
                            'new_rm_east'                     => 0,
                            'old_rm_north'                    => "$rm_north_co_id",
                            'new_rm_north'                    => 0,
                            'old_ash_nae'                     => "$ash_nae_co_id",
                            'new_ash_nae'                     => 0,
                            'old_ash_w'                       => "$ash_w_co_id",
                            'new_ash_w'                       => 0,
                            'old_ash_s'                       => "$ash_s_co_id",
                            'new_ash_s'                       => 0,
                            'old_acm'                         => "$acm_co_id",
                            'new_acm'                         => 0,
                            'update_by'                       => "$uid",
                        );

                        $this->db->insert('reset_company_log', $resetlogsdata);
                        if ($this->db->affected_rows() > 0) {
                            $processed++;
                        }
                }
            }
        } 

        $getLastFinancialYear    = $this->MasterReset_model->getLastFinancialYear();
        $annualResetLogDatas = [
            'fyear'      => $getLastFinancialYear,
            'by_type'    => $type,
            'zone_id'    => $zone_id,
            'reset_by'   => $uid,
            'status'     =>    1,
        ];
        $this->db->insert('annual_reset_status', $annualResetLogDatas);

        $message = "Reset successful";
        $this->session->set_flashdata('success_message'," * $bd_name - Company Funnel Reset - Completed Successfully !");
    }elseif ($type === 'bd') {

        $curFinancialDate       = $this->Menu_model->getFinancialYearRange();
        $sdate                  = $curFinancialDate['start_date'];
        $edate                  = $curFinancialDate['end_date'];

        $bd_id          = $id;
        $funnelEntries  = $this->MasterReset_model->GetBDFunnelsDatas_BDID($bd_id);
        $total          = sizeof($funnelEntries);
        $result         = $total;

        if ($total > 0) {

            foreach($funnelEntries as $funnelDatas){
                
                // *********** Get Company OwenShip ********** //
                $mainbd                 = $funnelDatas->main_bd_id;
                $bd_name                = $funnelDatas->bd_name;

                // *********** Get Company Mapping ID ********** //
                $init_id                = $funnelDatas->init_id;
                $cid                    = $funnelDatas->cid;
                // ************** Get Company Status ************ //
                $cstatus                = $funnelDatas->cstatus;
                $lstatus                = $funnelDatas->lstatus;

                // ********** Get All User Mapped Data ********** //
                $clm_id                 = $funnelDatas->clm_id;
                $apst                   = $funnelDatas->apst;
                $rm_east_co_id          = $funnelDatas->rm_east_co_id;
                $rm_north_co_id         = $funnelDatas->rm_north_co_id;
                $ash_nae_co_id          = $funnelDatas->ash_nae_co_id;
                $ash_w_co_id            = $funnelDatas->ash_w_co_id;
                $ash_s_co_id            = $funnelDatas->ash_s_co_id;
                $acm_co_id              = $funnelDatas->acm_co_id;

                // Get Main BD User Details
                $mainBDUserDetail   = $this->Menu_model->get_userbyid($mainbd); 
                $rm_user_id         = 	$mainBDUserDetail[0]->rm_east_co;       // Regional Manager
                $acm_user_id        = 	$mainBDUserDetail[0]->acm_co;           // Assistant Cluster Manager
                $cm_user_id         = 	$mainBDUserDetail[0]->aadmin;           // Cluster Manager

                // Check Primary Contact
                $primaryContact         = $this->MasterReset_model->CheckPrimaryContactExistsOrNotinTable($cid);

                if(sizeof($primaryContact) > 0){
                    $resetCStatus = 8;
                }else{
                    $resetCStatus = 1;
                }

                // Check Current Financial Year RP Meeting
                $checkCFRPMeeting       = $this->MasterReset_model->CheckCurrentFinancialYearRPMeetingDoneOrNot($init_id,$sdate,$edate);
                if(sizeof($checkCFRPMeeting) > 0){

                    $resetCStatus          = 3;
                    $mappedUserInitialData = array(
                        'clm_id'                        => "$cm_user_id",
                        'acm_co_id'                     => "$acm_user_id",
                    );
                    $this->db->where('id', $init_id); 
                    $this->db->update('init_call_bkp', $mappedUserInitialData);

                }

                // Check Current Financial Year Proposal
                $checkCFProposal        = $this->MasterReset_model->CheckCurrentFinancialYearProposalDoneOrNot($init_id,$sdate,$edate,$mainbd);
                if(sizeof($checkCFProposal) > 0){

                    $resetCStatus          = 6;
                    $mappedUserInitialData = array(
                        'rm_east_co_id'                 => "$rm_user_id",
                        'clm_id'                        => "$cm_user_id",
                        'acm_co_id'                     => "$acm_user_id",
                    );
                    $this->db->where('id', $init_id); 
                    $this->db->update('init_call_bkp', $mappedUserInitialData);

                }

                // Reset Company Funnel
                $initResetData = array(
                    'cstatus'                       => $resetCStatus,
                    'lstatus'                       => $cstatus,
                    'insidebd'                      => 0,
                    'bdid'                          => 0,
                    'abd'                           => 0,
                    'apst'                          => 0,
                    'ash_nae_co_id'                 => 0,
                    'ash_w_co_id'                   => 0,
                    'ash_s_co_id'                   => 0,
                    'rm_east_co_id'                 => 0,
                    'rm_north_co_id'                => 0,
                    'acm_co_id'                     => 0,
                    'bpst'                          => 0,
                    'cpst'                          => 0,
                    'clm_id'                        => 0,
                    'super_admin'                   => 0,
                );

                $this->db->where('id', $init_id); 
                $this->db->update('init_call_bkp', $initResetData);

                if ($this->db->affected_rows() > 0) {

                    // Insert Reset Logs
                    $resetlogsdata = array(
                            'reset_type'                      => $type,
                            'init_id'                         => $init_id,
                            'cid'                             => $cid,
                            'old_clm_bd'                      => "$clm_id",
                            'old_status'                      => $cstatus,
                            'bd_id'                           => "$bd_id",
                            'new_status'                      => $resetCStatus,
                            'new_clm_bd'                      => 0,
                            'old_apst'                        => "$apst",
                            'new_apst'                        => 0,
                            'old_rm_east'                     => "$rm_east_co_id",
                            'new_rm_east'                     => 0,
                            'old_rm_north'                    => "$rm_north_co_id",
                            'new_rm_north'                    => 0,
                            'old_ash_nae'                     => "$ash_nae_co_id",
                            'new_ash_nae'                     => 0,
                            'old_ash_w'                       => "$ash_w_co_id",
                            'new_ash_w'                       => 0,
                            'old_ash_s'                       => "$ash_s_co_id",
                            'new_ash_s'                       => 0,
                            'old_acm'                         => "$acm_co_id",
                            'new_acm'                         => 0,
                            'update_by'                       => "$uid",
                        );

                        $this->db->insert('reset_company_log', $resetlogsdata);
                        
                        if ($this->db->affected_rows() > 0) {
                            $processed++;
                        }
                }
            }
        } 
        $getLastFinancialYear    = $this->MasterReset_model->getLastFinancialYear();

        // Insert Reset Logs
        $annualResetLogDatas = [
            'fyear'      => $getLastFinancialYear,
            'by_type'    => $type,
            'bd_id'      => $bd_id,
            'reset_by'   => $uid,
            'status'     =>    1,
        ];
        $this->db->insert('annual_reset_status', $annualResetLogDatas);

        $message = "BD reset completed";
        $this->session->set_flashdata('success_message'," * $bd_name - BD Funnel Reset - Completed Successfully !");
    } 
    else {
        $this->session->set_flashdata('error_message'," * Funnel Reset - Failed !");
        $this->output
            ->set_content_type('application/json')
            ->set_status_header(400)
            ->set_output(json_encode([
                'success' => false,
                'message' => 'Invalid type'
            ]));
        return;
    }

    if ($result) {
        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode([
                'success'         => true,
                'message'         => $message,
                'processed_count' => $processed,
                'total_count'     => $total
            ]));
    } else {
        $this->session->set_flashdata('error_message'," * Funnel Reset - Failed !");
        $this->output
            ->set_content_type('application/json')
            ->set_status_header(500)
            ->set_output(json_encode([
                'success' => false,
                'message' => 'Reset operation failed. No records were affected.'
            ]));
    }
}

    // ************************************** Master Reset End Here ****************************************

}
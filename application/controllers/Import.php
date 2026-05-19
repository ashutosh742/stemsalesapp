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
class Import extends CI_Controller {
    public function __construct() {
        parent::__construct();
        // Load models, libraries, helpers, etc.
        $this->load->model('Menu_model');
        $this->load->model('Report_model');
        $this->load->helper('common_helper');
        $this->load->helper('samestatustilldate_helper');
        $this->load->helper('taskPlanner_helper');

        $config = $this->config->load('email', true);
        $this->load->library('email');
        $this->email->initialize($this->config->item('email'));

    }
    public function phpinfoprint() {
        phpinfo();
        exit;
    }
    public function Dashboard() {
        $user       = $this->session->userdata('user');
        if(empty($user)) {redirect('Menu/main'); }
        $uid        = $user['user_id'];
        $uyid       = $user['type_id'];
        $dt         = $this->Menu_model->get_utype($uyid);
        $dep_name   = isset($dt[0]->name) ? $dt[0]->name : 'default';
        $cdate      = date("Y-m-d");

        $udetail = $this->Menu_model->get_userbyid($uid);


        dd($udetail);



    }





    
    public function PermanentDeletionofLeads457()
    {

        $user           = $this->session->userdata('user');
        $data['user']   = $user;
        $uid            = $user['user_id'];

        $this->load->model('Menu_model');

        $file_path = 'https://stemapp.in/uploads/CompanyCSV/Permanent_Deletion_of_Leads_457.csv';
          if (($handle = fopen($file_path, "r")) !== FALSE) {
                $csv_data = array();
                while (($row = fgetcsv($handle, 1000, ",")) !== FALSE) {
                    $csv_data[] = $row;
                }
                fclose($handle);
                unset($csv_data[0]);

                dd("Not Allowed !");
                // dd($csv_data);

                $k=1;
                foreach($csv_data as $cdata){

                    $cid        =     $cdata[1];
                    $cmpDatas   = $this->Menu_model->getCompanyStatus($cid);

                    if(sizeof($cmpDatas) > 0){

                        $init_id        = $cmpDatas[0]->id;
                        $mainbd         = $cmpDatas[0]->mainbd;
                        $apst           = $cmpDatas[0]->apst;
                        $clm_id         = $cmpDatas[0]->clm_id;
                        $ash_nae_co_id  = $cmpDatas[0]->ash_nae_co_id;
                        $ash_w_co_id    = $cmpDatas[0]->ash_w_co_id;
                        $ash_s_co_id    = $cmpDatas[0]->ash_s_co_id;
                        $rm_east_co_id  = $cmpDatas[0]->rm_east_co_id;
                        $rm_north_co_id = $cmpDatas[0]->rm_north_co_id;
                        $acm_co_id      = $cmpDatas[0]->acm_co_id;
                        $cluster_id     = $cmpDatas[0]->cluster_id;
                        $cmpid_id       = $cmpDatas[0]->cmpid_id;

                        $taskQuery=$this->db->query("SELECT * FROM `tblcallevents` WHERE cid_id = '$init_id' AND nextCFID = 0 AND ( delete_request = '' OR delete_request IS NULL ) AND ( approved_status = 1 OR approved_status = '' )");
                        $taskQueryDatas = $taskQuery->result();

                          if($taskQueryDatas > 0){
                                foreach($taskQueryDatas as $taskQueryData){
                                    $taskID         = $taskQueryData->id;
                                    $taskactionID   = $taskQueryData->actiontype_id;

                                    if(in_array($taskactionID,[3,4])){
                                        $query = $this->db->query("DELETE FROM `barginmeeting` WHERE tid = '$taskID'");
                                        $query = $this->db->query("DELETE FROM `tblcallevents` WHERE id = '$taskID'");

                                        echo $k.' = '.$cid ." => Pending Task Deleted successfully.<br/> ";
                                    }else{
                                        $query = $this->db->query("DELETE FROM `tblcallevents` WHERE id = '$taskID'");
                                        echo $k.' = '.$cid ." => Pending Task Deleted successfully.<br/> ";
                                    }
                                }
                          }


                        // $data = array(
                        //     'mainbd'                        => 1000334,
                        //     'insidebd'                      => 0,
                        //     'bdid'                          => 0,
                        //     'abd'                           => 0,
                        //     'apst'                          => 0,
                        //     'ash_nae_co_id'                 => 0,
                        //     'ash_w_co_id'                   => 0,
                        //     'ash_s_co_id'                   => 0,
                        //     'rm_east_co_id'                 => 0,
                        //     'rm_north_co_id'                => 0,
                        //     'acm_co_id'                     => 0,
                        //     'bpst'                          => 0,
                        //     'cpst'                          => 0,
                        //     'clm_id'                        => 0,
                        //     'super_admin'                   => 0,
                        //     'cluster_id'                    => 0,
                        // );

                        // $this->db->where('cmpid_id', $cid); 
                        // $this->db->update('init_call', $data);

                        // if ($this->db->affected_rows() > 0) {

                        //     $data = array(
                        //             'init_id'                         => $init_id,
                        //             'cid'                             => $cid,
                        //             'old_main_bd'                     => $mainbd,
                        //             'new_main_bd'                     => 1000334,
                        //             'old_clm_bd'                      => $clm_id,
                        //             'new_clm_bd'                      => 0,
                        //             'old_apst'                        => $apst,
                        //             'new_apst'                        => 0,
                        //             'old_rm_east'                     => $rm_east_co_id,
                        //             'new_rm_east'                     => 0,
                        //             'old_rm_north'                    => $rm_north_co_id,
                        //             'new_rm_north'                    => 0,
                        //             'old_ash_nae'                     => $ash_nae_co_id,
                        //             'new_ash_nae'                     => 0,
                        //             'old_ash_w'                       => $ash_w_co_id,
                        //             'new_ash_w'                       => 0,
                        //             'old_ash_s'                       => $ash_s_co_id,
                        //             'new_ash_s'                       => 0,
                        //             'old_acm'                         => $acm_co_id,
                        //             'new_acm'                         => 0,
                        //             'old_cluster_id'                  => $cluster_id,
                        //             'new_cluster_id'                  => 0,
                        //             'update_by'                       => $uid,
                        //         );

                        //         $this->db->insert('company_log', $data);

                        //    echo $k.' = '.$cid ." => Leads transfer successfully.<br/> ";
                        // }else{
                        //     echo $k.' = '.$cid ." => Leads Not Deleted <br/>";
                        // }
                    }
                    $k++;
                }
                 dd("STEM LEARNING PVT LTD");
            }
    }




    public function RemoveListFromActiveFunnel()
    {

        $user           = $this->session->userdata('user');
        $data['user']   = $user;
        $uid            = $user['user_id'];

        $this->load->model('Menu_model');

        // $file_path = 'https://stemapp.in/uploads/CompanyCSV/delete_cin.csv';
        $file_path = 'https://stemapp.in/uploads/CompanyCSV/West_Funnel_to_be_Deleted.csv';
          if (($handle = fopen($file_path, "r")) !== FALSE) {
                $csv_data = array();
                while (($row = fgetcsv($handle, 1000, ",")) !== FALSE) {
                    $csv_data[] = $row;
                }
                fclose($handle);
                unset($csv_data[0]);

               
                dd("Not Allowed !");
                dd($csv_data);
                

                $k=1;
                foreach($csv_data as $cdata){

                    $cid        =     $cdata[0];
                    $cmpDatas   = $this->Menu_model->getCompanyStatus($cid);



                    // echo $cid;
                    // dd($cmpDatas);

                    // echo $k.' - '.$cid."<br/>";




                    if(sizeof($cmpDatas) > 0){

                        $init_id        = $cmpDatas[0]->id;
                        $mainbd         = $cmpDatas[0]->mainbd;
                        $apst           = $cmpDatas[0]->apst;
                        $clm_id         = $cmpDatas[0]->clm_id;
                        $ash_nae_co_id  = $cmpDatas[0]->ash_nae_co_id;
                        $ash_w_co_id    = $cmpDatas[0]->ash_w_co_id;
                        $ash_s_co_id    = $cmpDatas[0]->ash_s_co_id;
                        $rm_east_co_id  = $cmpDatas[0]->rm_east_co_id;
                        $rm_north_co_id = $cmpDatas[0]->rm_north_co_id;
                        $acm_co_id      = $cmpDatas[0]->acm_co_id;
                        $cluster_id     = $cmpDatas[0]->cluster_id;
                        $cmpid_id       = $cmpDatas[0]->cmpid_id;

                        
                        $taskQuery=$this->db->query("SELECT * FROM `tblcallevents` WHERE  cid_id = '$init_id' AND nextCFID = 0 AND ( delete_request = '' OR delete_request IS NULL ) AND (approved_status = 1 OR approved_status = '')");
                        $taskQueryDatas = $taskQuery->result();

                          if($taskQueryDatas > 0){
                                foreach($taskQueryDatas as $taskQueryData){
                                    $taskID         = $taskQueryData->id;
                                    $taskactionID   = $taskQueryData->actiontype_id;

                                    if(in_array($taskactionID,[3,4])){
                                        $query = $this->db->query("DELETE FROM `barginmeeting` WHERE tid = '$taskID'");
                                        $query = $this->db->query("DELETE FROM `tblcallevents` WHERE id = '$taskID'");

                                        echo $k.' = '.$cid ." => Pending Task Deleted successfully.<br/> ";
                                    }else{
                                        $query = $this->db->query("DELETE FROM `tblcallevents` WHERE id = '$taskID'");
                                        echo $k.' = '.$cid ." => Pending Task Deleted successfully.<br/> ";
                                    }
                                }
                          }

                      $upcmpdata = [
                            'mainbd'          => 1000334,
                            'insidebd'        => 0,
                            'bdid'            => 0,
                            'abd'             => 0,
                            'apst'            => 0,
                            'ash_nae_co_id'   => 0,
                            'ash_w_co_id'     => 0,
                            'ash_s_co_id'     => 0,
                            'rm_east_co_id'   => 0,
                            'rm_north_co_id'  => 0,
                            'acm_co_id'       => 0,
                            'bpst'            => 0,
                            'cpst'            => 0,
                            'clm_id'          => 0,
                            'super_admin'     => 0,
                            'cluster_id'      => 0,
                            'assign_support'  => 0
                        ];

                        if (!empty($cid)) {
                            $this->db->where('cmpid_id', $cid);
                            $this->db->update('init_call', $upcmpdata);

                        if ($this->db->affected_rows() > 0) {

                            $data = array(
                                    'init_id'                         => $init_id,
                                    'cid'                             => $cid,
                                    'old_main_bd'                     => $mainbd,
                                    'new_main_bd'                     => 1000334,
                                    'old_clm_bd'                      => $clm_id,
                                    'new_clm_bd'                      => 0,
                                    'old_apst'                        => $apst,
                                    'new_apst'                        => 0,
                                    'old_rm_east'                     => $rm_east_co_id,
                                    'new_rm_east'                     => 0,
                                    'old_rm_north'                    => $rm_north_co_id,
                                    'new_rm_north'                    => 0,
                                    'old_ash_nae'                     => $ash_nae_co_id,
                                    'new_ash_nae'                     => 0,
                                    'old_ash_w'                       => $ash_w_co_id,
                                    'new_ash_w'                       => 0,
                                    'old_ash_s'                       => $ash_s_co_id,
                                    'new_ash_s'                       => 0,
                                    'old_acm'                         => $acm_co_id,
                                    'new_acm'                         => 0,
                                    'old_cluster_id'                  => $cluster_id,
                                    'new_cluster_id'                  => 0,
                                    'update_by'                       => $uid,
                                );

                                $this->db->insert('company_log', $data);

                           echo $k.' = '.$cid ." => Leads transfer successfully.<br/> ";
                        }else{
                            echo $cid." Leads not transfer.<br/>";
                        }
                    }else{
                         echo $k.' = '.$cid ." => Invalid Company ID <br/>";
                    }
                }
                    $k++;
                }
                 dd("STEM LEARNING PVT LTD");
            }
    }



    public function UpdateClusterData()
    {

        $user           = $this->session->userdata('user');
        $data['user']   = $user;
        $uid            = $user['user_id'];

        $this->load->model('Menu_model');

        $file_path = 'https://stemapp.in/uploads/CompanyCSV/Mumbai_Cluster_Leads_with_Travel_Cluster.csv';
          if (($handle = fopen($file_path, "r")) !== FALSE) {
                $csv_data = array();
                while (($row = fgetcsv($handle, 1000, ",")) !== FALSE) {
                    $csv_data[] = $row;
                }
                fclose($handle);
                unset($csv_data[0]);

                dd("Not Allowed !");
                // dd($csv_data);

                $k=1;
                foreach($csv_data as $cdata){

                    $cid         =     $cdata[1];
                    $sclusterID  =     $cdata[4];

                
                    $cmpDatas   = $this->Menu_model->getCompanyStatus($cid);

                    if(sizeof($cmpDatas) > 0){

                        $init_id        = $cmpDatas[0]->id;
                        $cluster_id     = $cmpDatas[0]->cluster_id;

                        $data = array(
                            'cluster_id'  => $sclusterID
                        );

                        $this->db->where('cmpid_id', $cid); 
                        $this->db->update('init_call', $data);

                        if ($this->db->affected_rows() > 0) {

                            $data = array(
                                    'init_id'                         => $init_id,
                                    'cid'                             => $cid,
                                    'old_cluster_id'                  => $cluster_id,
                                    'new_cluster_id'                  => $sclusterID,
                                    'update_by'                       => $uid,
                                );

                                $this->db->insert('company_log', $data);

                           echo $k.' = '.$cid ." => Travel Cluster Update successfully.<br/> ";
                        }else{
                            echo $k.' = '.$cid ." => Failed To Update Travel Cluster <br/>";
                        }
                    }
                    $k++;
                }
                 dd("STEM LEARNING PVT LTD");
            }
    }








    public function ChangeMainBDActiveFunnel()
    {

        $user           = $this->session->userdata('user');
        $data['user']   = $user;
        $uid            = $user['user_id'];

        $this->load->model('Menu_model');

        // $file_path = 'https://stemapp.in/uploads/CompanyCSV/delete_cin.csv';
        $file_path = 'https://stemapp.in/uploads/CompanyCSV/mehak_stem.csv';
          if (($handle = fopen($file_path, "r")) !== FALSE) {
                $csv_data = array();
                while (($row = fgetcsv($handle, 1000, ",")) !== FALSE) {
                    $csv_data[] = $row;
                }
                fclose($handle);
                unset($csv_data[0]);

               
                dd("Not Allowed !");
                dd($csv_data);
               

                $k=1;
                foreach($csv_data as $cdata){

                    $cid            =     $cdata[1];
                    $new_main_bd    =     $cdata[7];
                    $cmpDatas   = $this->Menu_model->getCompanyStatus($cid);
                    // echo $cid;
                    // echo "new_main_bd = ".$new_main_bd;
                    dd($csv_data);

                    // echo $k.' - '.$cid."<br/>";

                    if(sizeof($cmpDatas) > 0){

                        $init_id        = $cmpDatas[0]->id;
                        $mainbd         = $cmpDatas[0]->mainbd;

                        $apst           = $cmpDatas[0]->apst;
                        $clm_id         = $cmpDatas[0]->clm_id;
                        $ash_nae_co_id  = $cmpDatas[0]->ash_nae_co_id;
                        $ash_w_co_id    = $cmpDatas[0]->ash_w_co_id;
                        $ash_s_co_id    = $cmpDatas[0]->ash_s_co_id;
                        $rm_east_co_id  = $cmpDatas[0]->rm_east_co_id;
                        $rm_north_co_id = $cmpDatas[0]->rm_north_co_id;
                        $acm_co_id      = $cmpDatas[0]->acm_co_id;
                        $cluster_id     = $cmpDatas[0]->cluster_id;
                        $cmpid_id       = $cmpDatas[0]->cmpid_id;

                        
                        $taskQuery=$this->db->query("SELECT * FROM `tblcallevents` WHERE  cid_id = '$init_id' AND nextCFID = 0 AND ( delete_request = '' OR delete_request IS NULL ) AND (approved_status = 1 OR approved_status = '')");
                        $taskQueryDatas = $taskQuery->result();

                          if($taskQueryDatas > 0){
                                foreach($taskQueryDatas as $taskQueryData){
                                    $taskID         = $taskQueryData->id;
                                    $taskactionID   = $taskQueryData->actiontype_id;

                                    if(in_array($taskactionID,[3,4])){
                                        $query = $this->db->query("DELETE FROM `barginmeeting` WHERE tid = '$taskID'");
                                        $query = $this->db->query("DELETE FROM `tblcallevents` WHERE id = '$taskID'");

                                        // echo $k.' = '.$cid ." => Pending Task Deleted successfully.<br/> ";
                                    }else{
                                        $query = $this->db->query("DELETE FROM `tblcallevents` WHERE id = '$taskID'");
                                        echo $k.' = '.$cid ." => Pending Task Deleted successfully.<br/> ";
                                    }
                                }
                          }

                      $upcmpdata = [
                            'mainbd' => $new_main_bd,
                        ];

                        if (!empty($cid)) {
                            $this->db->where('cmpid_id', $cid);
                            $this->db->update('init_call', $upcmpdata);

                        if ($this->db->affected_rows() > 0) {

                            $data = array(
                                    'init_id'                         => $init_id,
                                    'cid'                             => $cid,
                                    'old_main_bd'                     => $mainbd,
                                    'new_main_bd'                     => $new_main_bd,
                                    'update_by'                       => $uid,
                                );

                                $this->db->insert('company_log', $data);

                           echo $k.' = '.$cid ." => Leads transfer successfully.<br/> ";
                        }else{
                            echo $cid." Leads not transfer.<br/>";
                        }
                    }else{
                         echo $k.' = '.$cid ." => Invalid Company ID <br/>";
                    }
                }
                    $k++;
                }
                 dd("STEM LEARNING PVT LTD");
            }
    }






    public function ImportQuarterPlannedStrategy()
    {

        $user           = $this->session->userdata('user');
        $data['user']   = $user;
        $uid            = $user['user_id'];

        $this->load->model('Menu_model');

        $file_path = 'https://stemapp.in/uploads/CompanyCSV/Quater_3_Strategy.csv';
          if (($handle = fopen($file_path, "r")) !== FALSE) {
                $csv_data = array();
                while (($row = fgetcsv($handle, 1000, ",")) !== FALSE) {
                    $csv_data[] = $row;
                }
                fclose($handle);
                unset($csv_data[0]);

                $cfData     = $this->Menu_model->getCurrentFinancialYearAndQuarter();
                $cfData1    = $this->Menu_model->getAllCurrentFinancialYearAndQuarter();
                $cquarter   = 'Q'.$cfData['quarter'];

                $cq_start   = $cfData1['all_quarters'][$cquarter]['start'];
                $cq_end     = $cfData1['all_quarters'][$cquarter]['end'];

                dd($cfData1);
                // dd($csv_data);
               
                $k=1;
                foreach($csv_data as $cdata){

                    $sr_no                      = $cdata[0];
                    $cid                        = $cdata[1];
                    $company_name               = $cdata[2];
                    $main_bd                    = $cdata[3];
                    $cluster_manager            = $cdata[4];
                    $cluster                    = $cdata[5];
                    $prospecting_funnel         = $cdata[6];
                    $proposal_to_be_sent_review = $cdata[7];
                    $proposal_to_be_sent_target = $cdata[8];
                    $closure_pipeline_october   = $cdata[9];


                    $financial_Year_date    = $this->Menu_model->getFinancialYearRange(); 
                    $start_date             = $financial_Year_date['start_date'];
                    $end_date               = $financial_Year_date['end_date'];
                    $cfyear                 = (new DateTime($start_date))->format('Y');

                
                    $cmpDatas   = $this->Menu_model->getCompanyStatus($cid);

                    if(sizeof($cmpDatas) > 0){

                        $data = [
                            'fyear'                      => $cfyear,
                            'in_quater'                  => "Q3",
                            'cid'                        => $cid,
                            'company_name'               => $company_name,
                            'main_bd'                    => $main_bd,
                            'cluster_manager'            => $cluster_manager,
                            'cluster'                    => $cluster,
                            'prospecting_funnel'         => $prospecting_funnel,
                            'proposal_to_be_sent_review' => $proposal_to_be_sent_review,
                            'proposal_to_be_sent_target' => $proposal_to_be_sent_target,
                            'closure_pipeline_october'   => $closure_pipeline_october
                        ];
                        $this->db->insert('quater_strategy', $data);
                        $insert_id = $this->db->insert_id();

                    }else{
                         echo $k.' = '.$cid ." => Invalid Company ID <br/>";
                    }
                }
                    $k++;
                }
                 dd("STEM LEARNING PVT LTD");
            }




    public function ImportQuarterPlannedStrategyInitCall()
    {

        $user           = $this->session->userdata('user');
        $data['user']   = $user;
        $uid            = $user['user_id'];

        $this->load->model('Menu_model');

        $file_path = 'https://stemapp.in/uploads/CompanyCSV/Quater_3_Strategy.csv';
          if (($handle = fopen($file_path, "r")) !== FALSE) {
                $csv_data = array();
                while (($row = fgetcsv($handle, 1000, ",")) !== FALSE) {
                    $csv_data[] = $row;
                }
                fclose($handle);
                unset($csv_data[0]);

                $cfData     = $this->Menu_model->getCurrentFinancialYearAndQuarter();
                $cfData1    = $this->Menu_model->getAllCurrentFinancialYearAndQuarter();
                $cquarter   = 'Q'.$cfData['quarter'];

                $cq_start   = $cfData1['all_quarters'][$cquarter]['start'];
                $cq_end     = $cfData1['all_quarters'][$cquarter]['end'];

                dd("Not Allowed");

                $counts = [];
                $cidCounts = [];

                foreach ($csv_data as $ccdata) {
                    $main_bd = $ccdata[3]; // Main BD

                    // Initialize if not set
                    if (!isset($counts[$main_bd])) {
                        $counts[$main_bd] = [
                            'prospecting_funnel' => 0,
                            'proposal_to_be_sent_review' => 0,
                            'proposal_to_be_sent_target' => 0,
                            'closure_pipeline_october' => 0,
                            'total_yes' => 0
                        ];
                    }

                    // Check each field
                    if ($ccdata[6] === 'Yes') $counts[$main_bd]['prospecting_funnel']++;
                    if ($ccdata[7] === 'Yes') $counts[$main_bd]['proposal_to_be_sent_review']++;
                    if ($ccdata[8] === 'Yes') $counts[$main_bd]['proposal_to_be_sent_target']++;
                    if ($ccdata[9] === 'Yes') $counts[$main_bd]['closure_pipeline_october']++;

                    // Count total "Yes" across all 4 for this BD
                        $counts[$main_bd]['total_yes'] =
                        $counts[$main_bd]['prospecting_funnel'] +
                        $counts[$main_bd]['proposal_to_be_sent_review'] +
                        $counts[$main_bd]['proposal_to_be_sent_target'] +
                        $counts[$main_bd]['closure_pipeline_october'];
                }


                foreach ($csv_data as $ccdata) {
                    $main_bd = $ccdata[3]; // Main BD

                    // Initialize if not set
                    if (!isset($cidCounts[$main_bd])) {
                        $cidCounts[$main_bd] = [
                            'total_cid' => 0
                        ];
                    }

                     if ($ccdata[6] === 'Yes') $cidCounts[$main_bd]['total_cid']++;
                    
                }


                dd($csv_data);

                // foreach ($counts as $key => $count) {
                //         $data = array(
                //             'sdatet'             => $cq_start." 10:00:00",
                //             'plant'              => $cq_start." 10:00:00",
                //             'uid'                => $key,
                //             'bdid'               => $key,
                //             'meetid'             => "www.google.com",
                //             'startt'             => $cq_start." 10:00:00",
                //             'closet'             => $cq_start." 10:00:00",
                //             'cremark'            => "This Review is Created, start and closed From Backend",
                //             'reviewtype'         => "Quarterly",
                //             'fixdate'            => "2025-04-01",
                //             'review_close_time'  => $cq_start." 10:00:00",
                //             'plan_time_remarks'  => "This Review is Created, start and closed From Backend",
                //             'base_review'        => "0",
                //             'target_settings'    => "no",
                //             'review_in_quarter'  => "$cquarter"
                //         );
                //         $this->db->insert('allreview', $data);
                //         $review_insert_id = $this->db->insert_id();
                // }


                $k=1;
                foreach($csv_data as $cdata){

                    $sr_no                      = $cdata[0]; // SR NO
                    $cid                        = $cdata[1]; // CID
                    $company_name               = $cdata[2]; // Company Name
                    $main_bd                    = $cdata[3]; // Main BD
                    $cluster_manager            = $cdata[4]; // Cluster Manager
                    $cluster                    = $cdata[5]; // Cluster
                    $prospecting_funnel         = $cdata[6]; // Prospecting Funnel
                    $proposal_to_be_sent_review = $cdata[7]; // Proposal to Be Sent(Review)
                    $proposal_to_be_sent_target = $cdata[8]; // Proposal to Be Sent - Target
                    $closure_pipeline_october   = $cdata[9]; // Closure Pipeline - October

                    $getReviewDetails           =  $this->Menu_model->GetReviewDetailsUsingUidQuarter($main_bd,$cquarter,'Quarterly');
                    $review_id                          =  $getReviewDetails[0]->id;

                    $mainBDCountsDetails                = $counts[$main_bd];
                    $prospecting_funnel_counts          = $mainBDCountsDetails['prospecting_funnel'];
                    $proposal_to_be_sent_review_counts  = $mainBDCountsDetails['proposal_to_be_sent_review'];
                    $proposal_to_be_sent_target_counts  = $mainBDCountsDetails['proposal_to_be_sent_target'];
                    $closure_pipeline_october_counts    = $mainBDCountsDetails['closure_pipeline_october'];

                    $total_cid_count                    = $cidCounts[$main_bd];
                    $total_cid                          = $total_cid_count['total_cid'];

                    // $getThisTargetSetOrNot = $this->Menu_model->CheckTargetSettingListsDoneOrNot($review_id,$main_bd,$cquarter);
                    // if(sizeof($getThisTargetSetOrNot) == 0){
                    //   $data = [
                    //             'currentQuarter'                        => "$cquarter",
                    //             'user_id'                               => $main_bd,
                    //             'no_of_meeting'                         => $total_cid,
                    //             'no_of_proposal'                        => $proposal_to_be_sent_review_counts + $proposal_to_be_sent_target_counts,
                    //             'no_of_prospective'                     => $prospecting_funnel_counts,
                    //             'revenue'                               => 0,
                    //             'start_date'                            => $cq_start,
                    //             'end_date'                              => $cq_end,
                    //             'target_by'                             => $main_bd,
                    //             'school'                                => 0,
                    //             'partner_id'                            => "{}",
                    //             'category_id'                           => "{}",
                    //             'out_station_meeting'                   => 0,
                    //             'local_station_meeting'                 => 0,
                    //             'num_of_sheduled_meeting'               => 0,
                    //             'num_of_barg_meeting'                   => 0,
                    //             'review_id'                             => "$review_id",
                    //             'twetenty_closure_funnel'               => "$closure_pipeline_october_counts",
                    //             'potential_funnel_for_fy'               => "0",
                    //             'to_be_nurtured_for_fy'                 => 0,
                    //             'fifity_new_lead_funnel'                => "$prospecting_funnel_counts",
                    //             'anchor_client_meeting'                 => 0,
                    //             'proposal_send'                         => $proposal_to_be_sent_review_counts + $proposal_to_be_sent_target_counts,
                    //             'bd_potentional_client_proposal_send'   => "$proposal_to_be_sent_target_counts",
                    //             'proposals_sent_and_closure'            => "$closure_pipeline_october_counts",
                    //             'proposal_to_be_sent_review'            => "$proposal_to_be_sent_review_counts",
                    //             'proposal_to_be_sent_target'            => "$proposal_to_be_sent_target_counts",
                    //             'closure_pipeline'                      => "$closure_pipeline_october_counts"
                    //         ];
                    //         $this->db->insert('target_vs_achievement', $data);
                    //         $insert_id = $this->db->insert_id();
                            
                    //         if($review_id !==''){
                    //             $query=$this->db->query("update allreview set target_settings='$insert_id' WHERE id='$review_id'");
                    //             if(isset($review_id) && !empty($review_id)){
                    //                 $closeremark = "$cid Target Setting Done And Review Closed Successfully <br/><hr/>";
                    //                 echo $closeremark;
                    //             }
                    //         }
                    //     }

                        if($prospecting_funnel == 'Yes'){
                            
                            $this->db->set('prospecting_funnel', $cquarter);
                            $this->db->where('cmpid_id', $cid);
                            $this->db->update('init_call');
                        }
                        if($proposal_to_be_sent_review == 'Yes'){
                           
                            $proposal_to_be_sent_review = 'Proposal to Be Sent After Review In '. $cquarter; // Potential Funnel For Q3
                            $this->db->set('proposal_to_be_sent_review', $cquarter);
                            $this->db->set('in_quarter ', $proposal_to_be_sent_review);
                            $this->db->where('cmpid_id', $cid);
                            $this->db->update('init_call');

                        }
                        if($proposal_to_be_sent_target == 'Yes'){
                            $proposal_to_be_sent_target = 'Potential Funnel For '. $cquarter; // Potential Funnel For Q3
                            $this->db->set('proposal_to_be_sent_target', $cquarter);
                            $this->db->set('in_quarter ', $proposal_to_be_sent_target);
                            $this->db->where('cmpid_id', $cid);
                            $this->db->update('init_call');
                        }

                        if($closure_pipeline_october == 'Yes'){
                            $closure_pipeline = '20 Closure Funnel in '. $cquarter; // 20 Closure Funnel in Q3
                            $this->db->set('closure_pipeline', $cquarter);
                            $this->db->set('in_quarter ', $closure_pipeline);
                            $this->db->where('cmpid_id', $cid);
                            $this->db->update('init_call');
                        }
                      
                    // echo "main_bd - ".$main_bd."<br/>";
                    // echo "cid - ".$cid."<br/>";
                    // dd($mainBDCountsDetails);
                    // dd($csv_data);
                    // dd($cdata);

                   echo $k.' = '.$cid ." => Company Updated <br/>";     
                }
                    $k++;
                }
                 dd("STEM LEARNING PVT LTD");
            }
    

public function UpdateUserPasswordandSendMail()
    {

        $user           = $this->session->userdata('user');
        $data['user']   = $user;
        $uid            = $user['user_id'];
        $this->load->model('Menu_model');

        $userData       = $this->Menu_model->get_userbyid(1000267);
        $email          = $userData[0]->email;

        $userName       = 'Deepak';
        $userEmail      = 'teclwork@gmail.com';
        $newPassword    = 'Deepak@123';

        $userName       = $userData[0]->name ?? 'User';
        $userEmail      = $userData[0]->email;
        $newPassword    = 'Deepak@123';  // set whatever your management assigns


        dd("Not Allowed");
        die;
        die;
        die;
        die;
        die;

        $this->email->initialize();
        $this->email->from('crm.help@stemapp.in', 'CRM Help');
        $logoPath = 'http://stemapp.in/uploads/clogo/logo.png';
        $cid = $this->email->attachment_cid($logoPath);

        $data = [];

        $file_path = 'https://stemapp.in/uploads/CompanyCSV/Mail_Id_of_Sales_Team.csv';
          if (($handle = fopen($file_path, "r")) !== FALSE) {
                $csv_data = array();
                while (($row = fgetcsv($handle, 1000, ",")) !== FALSE) {
                    $csv_data[] = $row;
                }

                fclose($handle);
                unset($csv_data[0]);

                $specialChars = [
                    '!', '@', '#', '$', '%', '&', '*',
                     '+',
                     '.'
                ];

                $i=1;
                foreach ($csv_data as $ccdata) {

                        $target_user_name       = $ccdata[0];   // Name
                        $target_user_id         = $ccdata[1];   // user ID
                        $target_user_status     = $ccdata[7];   // Status
                        $target_user_mail_id    = $ccdata[8];   // Mail ID
                        
                        if($target_user_id !== 'Not Found'){
                            

                            // $query = $this->db->query("UPDATE user_details SET email='$target_user_mail_id'WHERE user_id = '$target_user_id'");

                            $targetUserDetail       = $this->Menu_model->get_userbyid($target_user_id);
                            $name                   = $targetUserDetail[0]->name;
                            $ttype_id               = $targetUserDetail[0]->type_id;
                            $username               = $targetUserDetail[0]->username;
                            $userEmail              = trim($targetUserDetail[0]->email);
                            $old_password           = $targetUserDetail[0]->password;
                           
                            $new_password           = "STEM".$specialChars[rand(1,10)].rand(2013,2028).$specialChars[rand(1,10)];

                            $aadmin                 = $targetUserDetail[0]->aadmin;

                            // $query = $this->db->query("UPDATE user_details SET password = '$new_password'WHERE user_id = '$target_user_id'");

                           $plain = <<<TXT
Hello {$userName},

🔐 Your password has been updated by the management team.

Your new login details:
• Name: {$name}
• Email: {$userEmail}
• User Name: {$username}
• Old Password: {$old_password}
• New Password: {$new_password}

For security, please log in and update this password immediately.

Regards,
Your Company
TXT;

$html = <<<HTML
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
</head>
<body style="font-family:Arial, sans-serif; background:#f6f9fc; padding:20px;">

<table width="100%" cellpadding="0" cellspacing="0" style="max-width:650px; margin:0 auto; background:#fff; border-radius:10px; padding:25px; border:1px solid #e5e7eb;">

    <tr>
        <td align="center" style="padding-bottom:20px;">
            <span style="
                display:inline-block;
                font-size:28px;
                font-weight:bold;
                color:#1600da;
                letter-spacing:1px;
            ">
                <a target="_blank" style="text-decoration: none;" href="https://stemapp.in/">📚 Stem Learning Pvt Ltd</a>
            </span>
            <hr>
        </td>
    </tr>

    <tr>
        <td>
            <h2 style="margin:0 0 15px 0; font-size:22px; color:#333;">🔐 Password Updated</h2>

            <p style="font-size:16px; color:#444;">
                Hello <strong>{$name}</strong>,
            </p>

            <p style="font-size:15px; color:#444;">
                Sales CRM account {$name} password has been updated by the management team.
            </p>

            <div style="background:#f3f4f6; padding:12px; border-radius:6px; border:1px solid #ddd; margin:20px 0; font-family:monospace; font-size:15px;">
                <strong style="color:navy">Name:</strong> {$name}<br>
                <strong style="color:navy">Email:</strong> {$userEmail}<br>
                <strong style="color:navy">Username:</strong> {$username}<br>
                <strong style="color:navy">Old Password:</strong> {$old_password}<br>
                <strong style="color:navy">New Password:</strong> {$new_password}
            </div>

            <p style="font-size:15px; color:#444;">
                For your security, please log in using this new password immediately.
            </p>

            <p style="font-size:15px; color:#444;">
                <a target="_blank" style="text-decoration: none;" href="https://stemapp.in/">📚 Login Stem Sales CRM</a>
            </p>

            <p style="font-size:15px; color:#444;">
                Regards,<br>
                <strong>STEM Team</strong>
            </p>
        </td>
    </tr>

</table>

</body>
</html>
HTML;


                                            $data['subject']   = "$name - Sales CRM account password has been changed by management team";
                                            $data['message']   = "$html";
                                            $data['plain']     = "$plain";
                                            $data['tomailID']  = "$userEmail";
                                            // $data['tbccmailID']  = "$userManagerEmail";

                                            $this->sendMail($data);

                                            // die;
                         echo $i.") Name -".$target_user_name."  Paswword Changes Successfully<br/>";
                        }
                     $i++;   
                    }
                }
            



        dd("STEM PVT LTD");
    // --- Plain text version ---

    }


public function UpdateUserCRMChangesSendMail()
{
    $user = $this->session->userdata('user');
    if (!$user) {
        dd("User session not found");
        return;
    }

    $this->load->model('Menu_model');
    $this->load->library('email');

    // Fetch user data
    $userData = $this->Menu_model->get_userbyid(1000267);
    if (empty($userData)) {
        dd("User not found");
        return;
    }

    $name         = $userData[0]->name ?? 'User';
    $userEmail    = $userData[0]->email;
    $username     = $userData[0]->email;   // If username is email, adjust if needed
    $old_password = "********";            // You should fetch old password if needed
    $new_password = "Deepak@123";          // Set whatever your rules permit

    // Safety lock - remove when testing is complete
    dd("Not Allowed"); 
    return;

    // Email init
    $this->email->initialize();
    $this->email->from('crm.help@stemapp.in', 'CRM Help');

    // Plain text version
    $plain = <<<TXT
Hello {$name},

Your password has been updated by the management team.

Your new login details:
• Name: {$name}
• Email: {$userEmail}
• Username: {$username}
• Old Password: {$old_password}
• New Password: {$new_password}

For security, please log in and update this password immediately.

Regards,
STEM Team
TXT;

    // HTML version
    $html = <<<HTML
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
</head>
<body style="font-family:Arial, sans-serif; background:#f6f9fc; padding:20px;">

<table width="100%" cellpadding="0" cellspacing="0" style="max-width:650px; margin:0 auto; background:#fff; border-radius:10px; padding:25px; border:1px solid #e5e7eb;">

    <tr>
        <td align="center" style="padding-bottom:20px;">
            <span style="
                display:inline-block;
                font-size:28px;
                font-weight:bold;
                color:#1600da;
                letter-spacing:1px;
            ">
                <a target="_blank" style="text-decoration: none;" href="https://stemapp.in/">📚 Stem Learning Pvt Ltd</a>
            </span>
            <hr>
        </td>
    </tr>

    <tr>
        <td>
            <h2 style="margin:0 0 15px 0; font-size:22px; color:#333;">🔐 Password Updated</h2>

            <p style="font-size:16px; color:#444;">
                Hello <strong>{$name}</strong>,
            </p>

            <p style="font-size:15px; color:#444;">
                Your Sales CRM account password has been updated by the management team.
            </p>

            <div style="background:#f3f4f6; padding:12px; border-radius:6px; border:1px solid #ddd; margin:20px 0; font-family:monospace; font-size:15px;">
                <strong style="color:navy">Name:</strong> {$name}<br>
                <strong style="color:navy">Email:</strong> {$userEmail}<br>
                <strong style="color:navy">Username:</strong> {$username}<br>
                <strong style="color:navy">Old Password:</strong> {$old_password}<br>
                <strong style="color:navy">New Password:</strong> {$new_password}
            </div>

            <p style="font-size:15px; color:#444;">
                For your security, please log in using this new password immediately.
            </p>

            <p style="font-size:15px; color:#444;">
                <a target="_blank" style="text-decoration: none;" href="https://stemapp.in/">📚 Login Stem Sales CRM</a>
            </p>

            <p style="font-size:15px; color:#444;">
                Regards,<br>
                <strong>STEM Team</strong>
            </p>
        </td>
    </tr>

</table>

</body>
</html>
HTML;

    // Send mail
    $data = [
        'subject'  => "{$name} - Sales CRM account password updated",
        'message'  => $html,
        'plain'    => $plain,
        'tomailID' => $userEmail
    ];

    $this->sendMail($data);

    dd("Email Sent Successfully");
}




 public function sendMail($data){

        $this->email->initialize();
        $this->email->from('crm.help@stemapp.in', 'Sales CRM Help');
 
        $this->email->to($data['tomailID']);
        // $this->email->bcc($data['userManagerEmail'],"Sales CRM");
        $this->email->subject($data['subject']);
        $this->email->message($data['message']);
        $this->email->set_alt_message($data['plain']);
        $this->email->attach($data['logoPath'], 'inline');

        // Send the emai    l
        if ($this->email->send()) {
            echo $data['tomailID'].' - Email sent successfully!';
        } else {
            echo $this->email->print_debugger(['headers', 'subject']);
        }

    }
 public function UpdateUserCashNewDetails(){


    dd("Not Allowed");

         $this->load->model('Menu_model');

        $user               = $this->session->userdata('user');
        $data['user']       = $user;
        $uid                = $user['user_id'];

        $userDatasQuery       = $this->db->query("SELECT
                    cash_log.id,
                    u1.name as bdname,
                    cash_log.cash,
                    cash_log.av_cash,
                    cash_log.task_id,
                    tblcallevents.cash_allot,
                    tblcallevents.cash_expense,
                    tblcallevents.cash_refund,
                    cash_expense.id as rexpense_id,
                    cash_expense.expense as rexpense,
                    u1.ucash as bd_current_cash,
                    u1.user_id as bd_user_id
                FROM cash_log
                LEFT JOIN tblcallevents ON tblcallevents.id = cash_log.task_id
                LEFT JOIN barginmeeting ON barginmeeting.tid = tblcallevents.id
                LEFT JOIN cash_expense ON cash_expense.meetid = barginmeeting.id
                LEFT JOIN user_details u1 ON u1.user_id = cash_log.uid
                WHERE cash_log.type = 'Add'
                AND tblcallevents.cash_allot = 500");

                $userDatas       = $userDatasQuery->result();
dd("Not Allowed");
                foreach ($userDatas as $row) {

                    $cashlog_id          = $row->id;
                    $bdname      = $row->bdname;
                    $cash        = $row->cash;
                    $av_cash     = $row->av_cash;
                    $task_id     = $row->task_id;
                    $cash_allot  = $row->cash_allot;
                    $cash_expense = $row->cash_expense;
                    $cash_refund  = $row->cash_refund;
                    $rexpense_id  = $row->rexpense_id;
                    $rexpense     = $row->rexpense;
                    $bd_user_id   = $row->bd_user_id;

                    $new_user_cash = $cash_allot - $rexpense;

                    $maxUserQuery       = $this->db->query("SELECT ucash FROM `user_details` WHERE user_id = '$bd_user_id'");
                    $maxUserDatas       = $maxUserQuery->result();
                    $bd_current_cash    = $maxUserDatas[0]->ucash;

                    $new_update_user_cash = $bd_current_cash + $new_user_cash;

                    // $userDatas = array(
                    //     'ucash' => $new_update_user_cash
                    // );
                    // $this->db->where('user_id', $bd_user_id);
                    // $this->db->update('user_details', $userDatas);

                    // $userTblCallDatas = array(
                    //     'cash_expense' => $rexpense,
                    //     'cash_refund'  => $new_user_cash
                    // );
                    // $this->db->where('id', $task_id);
                    // $this->db->update('tblcallevents', $userTblCallDatas);

                    // $cash_log_Data = array(
                    //     'cash'      => $new_user_cash,
                    //     'av_cash'   => $new_update_user_cash
                    // );
                    // $this->db->where('id', $cashlog_id);
                    // $this->db->update('cash_log', $cash_log_Data);


                

                    // $maxUserQuery       = $this->db->query("SELECT MAX(user_id) as last_user_id FROM `user_details`");

                    
                }


        dd($userDatas);

        

    }
 public function UpdateUserCashNewDetailsNew(){

         $this->load->model('Menu_model');

        $user               = $this->session->userdata('user');
        $data['user']       = $user;
        $uid                = $user['user_id'];

        dd("Not Allowed");

        $userDatasQuery       = $this->db->query("SELECT
                    cash_log.id,
                    u1.name as bdname,
                    cash_log.id as cash_log_id,
                    cash_log.cash,
                    cash_log.av_cash,
                    tblcallevents.id as task_id,
                    tblcallevents.cash_allot,
                    tblcallevents.cash_expense,
                    tblcallevents.cash_refund,
                    tblcallevents.delete_request,
                    u1.ucash as bd_current_cash,
                    u1.user_id as bd_user_id
                FROM tblcallevents
                LEFT JOIN cash_log ON cash_log.task_id = tblcallevents.id AND cash_log.type =  'Refilled'
                LEFT JOIN user_details u1 ON u1.user_id = tblcallevents.user_id
                WHERE 
                 tblcallevents.delete_request IS NOT NULL
                AND tblcallevents.cash_allot = 500
                AND tblcallevents.cash_refund IS NULL");

                $userDatas       = $userDatasQuery->result();

                foreach ($userDatas as $row) {

                    $cashlog_id  = $row->id;
                    $bdname      = $row->bdname;
                    $cash        = $row->cash;
                    $av_cash     = $row->av_cash;
                    $task_id     = $row->task_id;
                    $cash_allot  = $row->cash_allot;
                    $rexpense_id  = $row->rexpense_id;
                    $rexpense     = $row->rexpense;
                    $bd_user_id   = $row->bd_user_id;

                    $maxUserQuery       = $this->db->query("SELECT ucash FROM `user_details` WHERE user_id = '$bd_user_id'");
                    $maxUserDatas       = $maxUserQuery->result();
                    $bd_current_cash    = $maxUserDatas[0]->ucash;

                    $new_update_user_cash = $bd_current_cash + $cash_allot;

                    // $userDatas = array(
                    //     'ucash' => $new_update_user_cash
                    // );
                    // $this->db->where('user_id', $bd_user_id);
                    // $this->db->update('user_details', $userDatas);

                    $userTblCallDatas = array(
                        'cash_refund'  => $cash_allot
                    );
                    $this->db->where('id', $task_id);
                    $this->db->update('tblcallevents', $userTblCallDatas);

                    $cashlogdata = array(
                        'remarks'    => "Pending Meeting Task Approved By Admin & Cash Refilled. Task ID: $task_id, Cash Refilled On User Wallet : $cash_allot "
                    );
                    $this->db->where('task_id', $task_id);
                    $this->db->update('cash_log', $cashlogdata);

                    // $cashlogdata = array(
                    //     'uid'        => $bd_user_id,
                    //     'cash'       => $cash_allot,
                    //     'av_cash'    => $new_update_user_cash,
                    //     'task_id'    => $task_id,
                    //     'type'       => "Credit",
                    //     'remarks'    => "Pending Meeting Task Approved By Admin & Cash Refilled. Task ID: $task_id, Cash Refilled On User Wallet : $cash_allot",
                    //     'task_id'    => $task_id
                    // );
                    // $this->db->insert('cash_log', $cashlogdata);

                }


        dd($userDatas);

        

    }





    public function NewUserCreation()
    {

        $this->load->model('Menu_model');

        die("Not Allowed");

        $user               = $this->session->userdata('user');
        $data['user']       = $user;
        $uid                = $user['user_id'];

        $firstname          = "Operation";
        $lastname           = "Team";
        $fullname           = $firstname." ".$lastname;

        // $username           = "stem_".strtolower(str_replace(' ', '.', trim($firstname)));
        $username           = "stem_".strtolower(str_replace(' ', '.', trim($fullname)));
        $email              = "operation@stemlearning.in";
        $password           = 'Operation@1149';
        $hashedPassword     = password_hash($password, PASSWORD_BCRYPT);
        $roll               = 13;           // Department
        $admin_id           = 2;            // Admin ID
        $sales_co           = 0;       // Sales Coordinator
        $aadmin             = 0;       // Cluster Manager
        $rm_east_co         = 0;       // Cluster Manager
        $pst_co             = 0;            // PST
        $phoneno            = '8369000263';
        $dob                = '2010-01-01';
        $createDate         = date("Y-m-d");


        echo $hashedPassword;
        die("Not Allowed");


        $maxUserQuery       = $this->db->query("SELECT MAX(user_id) as last_user_id FROM `user_details`");
        $maxUserDatas       = $maxUserQuery->result();
        $last_user_id       = $maxUserDatas[0]->last_user_id;
        $new_user_id        = $last_user_id + 1;

        $scUserDetail       = $this->Menu_model->get_userbyid($sales_co);
        $admin_id           = $scUserDetail[0]->admin_id;
        $zone_id            = $scUserDetail[0]->zone_id;

        $admin_id           = 2;
        $zone_id            = 4;


    // Start To Create New User On Organations
    $authData = array(
        'password'     => $hashedPassword,
        'is_superuser' => 0,
        'username'     => $username,
        'first_name'   => $firstname,
        'last_name'    => $lastname,
        'email'        => $email,
        'is_staff'     => 1,
        'is_active'    => 1,
        'date_joined'  => $createDate
    );
    
    if ($this->db->insert('auth_user', $authData)) {
    $authUserId = $this->db->insert_id();


        $userData = array(
            'email'          => $email,
            'name'           => $fullname,
            'phoneno'        => $phoneno,
            'dob'            => "$dob",
            'username'       => $username,
            'password'       => $password,
            'usercreateDate' => $createDate,
            'type_id'        => $roll,
            'user_id'        => $authUserId,
            'zone_id'        => $zone_id,
            'sadmin_id'      => 100000,
            'admin_id'       => $admin_id,
            'sales_co'       => $sales_co,
            'aadmin'         => $aadmin,
            'pst_co'         => $pst_co,
            'rm_east_co'     => $rm_east_co,
            'status'         => "active",
            'create_by'      => $uid
        );

if ($this->db->insert('user_details', $userData)) {

    $insertu_id = $this->db->insert_id();


    echo " Name: ". $fullname."<br>";
    echo " UserID: ". $new_user_id."<br>";
    echo " User_Name: ". $username."<br>";
    echo " password: ". $password."<br>";
    echo " Login URL: ". "https://stemapp.in/";


} else {

    $maxUserQuery       = $this->db->query("DELETE FROM `auth_user`  WHERE id = '$authUserId' ");
    $error              = $this->db->error();
    dd($error);

}

}else{
    echo "ID is not created";
}


    }




    public function DayImageCompressorLogs()
    {

        $this->load->model('Menu_model');

        $user               = $this->session->userdata('user');
        $data['user']       = $user;
        $uid                = $user['user_id'];

        $createDate         = date("Y-m-d");

        $dayQuery       = $this->db->query("SELECT * FROM `user_day` WHERE Cast(sdatet as Date) BETWEEN '2025-10-11' AND '2025-10-20'");
        $dayQueryDatas  = $dayQuery->result();

        foreach($dayQueryDatas as $dayQueryData){

            $usimg  = "https://stemapp.in/".$dayQueryData->usimg;
            $ucimg  = "https://stemapp.in/".$dayQueryData->ucimg;

            $usimg_headers = @get_headers($usimg);
            $ucimg_headers = @get_headers($ucimg);

            if($dayQueryData->usimg != 'uploads/day/'){

                if ($usimg_headers && strpos($usimg_headers[0], '200') !== false) {
                
                    $source         = $dayQueryData->usimg;
                    echo "<hr>";
                    echo $this->compress_any_image_replace($source, 20);
                    echo "<br/>$usimg";
                }

            }
          
            if($dayQueryData->ucimg != 'uploads/day/'){

                if ($ucimg_headers && strpos($ucimg_headers[0], '200') !== false) {
                    
                    $source         = $dayQueryData->ucimg;
                    echo "<hr>";
                    echo $this->compress_any_image_replace($source, 20);
                    echo "<br/>$usimg";
                }

            }
        }
    }
    public function DayImageCompressorLogsByDate($sdate,$edate)
    {

        $this->load->model('Menu_model');

        $user               = $this->session->userdata('user');
        $data['user']       = $user;
        $uid                = $user['user_id'];

        $createDate         = date("Y-m-d");

        $dayQuery       = $this->db->query("SELECT * FROM `user_day` WHERE Cast(sdatet as Date) BETWEEN '$sdate' AND '$edate'");
        $dayQueryDatas  = $dayQuery->result();

        foreach($dayQueryDatas as $dayQueryData){

            $usimg  = "https://stemapp.in/".$dayQueryData->usimg;
            $ucimg  = "https://stemapp.in/".$dayQueryData->ucimg;

            $usimg_headers = @get_headers($usimg);
            $ucimg_headers = @get_headers($ucimg);

            if($dayQueryData->usimg != 'uploads/day/'){

                if ($usimg_headers && strpos($usimg_headers[0], '200') !== false) {
                
                    $source         = $dayQueryData->usimg;
                    echo "<hr>";
                    echo $this->compress_any_image_replace($source, 20);
                    echo "<br/>$usimg";
                }

            }
          
            if($dayQueryData->ucimg != 'uploads/day/'){

                if ($ucimg_headers && strpos($ucimg_headers[0], '200') !== false) {
                    
                    $source         = $dayQueryData->ucimg;
                    echo "<hr>";
                    echo $this->compress_any_image_replace($source, 20);
                    echo "<br/>$usimg";
                }

            }
        }
    }

    
    public function MeetingDayImageCompressorLogs()
    {

        $this->load->model('Menu_model');

        $user               = $this->session->userdata('user');
        $data['user']       = $user;
        $uid                = $user['user_id'];
        ini_set('max_execution_time', 18000);

        $createDate         = date("Y-m-d");

        $dayQuery       = $this->db->query("SELECT * FROM `barginmeeting` WHERE Cast(storedt as Date) BETWEEN '2025-09-01' AND '2025-10-01'");
        $dayQueryDatas  = $dayQuery->result();

        foreach($dayQueryDatas as $dayQueryData){

            $cphoto  = "https://stemapp.in/".$dayQueryData->cphoto;
            $initphoto  = "https://stemapp.in/uploads/day/".$dayQueryData->initphoto;

            $cphoto_headers = @get_headers($cphoto);
            $initphoto_headers = @get_headers($initphoto);

            if($dayQueryData->cphoto != 'uploads/day/'){

                if ($cphoto_headers && strpos($cphoto_headers[0], '200') !== false) {
                
                    $source         = $dayQueryData->cphoto;
                    echo "<hr>";
                    echo $this->compress_any_image_replace($source, 20);
                    echo "<br/>$cphoto";
                }

            }
          
            if($dayQueryData->initphoto != 'uploads/day/'){

                if ($initphoto_headers && strpos($initphoto_headers[0], '200') !== false) {
                    
                    $source         = "uploads/day/".$dayQueryData->initphoto;
                    echo "<hr>";
                    echo $this->compress_any_image_replace($source, 20);
                    echo "<br/>$usimg";
                }

            }
        }
    }
    public function MeetingDayImageCompressorLogsByDate($sdate,$edate)
    {

        $this->load->model('Menu_model');

        $user               = $this->session->userdata('user');
        $data['user']       = $user;
        $uid                = $user['user_id'];
        ini_set('max_execution_time', 18000);

        $createDate         = date("Y-m-d");

        $dayQuery       = $this->db->query("SELECT * FROM `barginmeeting` WHERE Cast(storedt as Date) BETWEEN '$sdate' AND '$edate'");
        $dayQueryDatas  = $dayQuery->result();

        foreach($dayQueryDatas as $dayQueryData){

            $cphoto  = "https://stemapp.in/".$dayQueryData->cphoto;
            $initphoto  = "https://stemapp.in/uploads/day/".$dayQueryData->initphoto;

            $cphoto_headers = @get_headers($cphoto);
            $initphoto_headers = @get_headers($initphoto);

            if($dayQueryData->cphoto != 'uploads/day/'){

                if ($cphoto_headers && strpos($cphoto_headers[0], '200') !== false) {
                
                    $source         = $dayQueryData->cphoto;
                    echo "<hr>";
                    echo $this->compress_any_image_replace($source, 20);
                    echo "<br/>$cphoto";
                }

            }
          
            if($dayQueryData->initphoto != 'uploads/day/'){

                if ($initphoto_headers && strpos($initphoto_headers[0], '200') !== false) {
                    
                    $source         = "uploads/day/".$dayQueryData->initphoto;
                    echo "<hr>";
                    echo $this->compress_any_image_replace($source, 20);
                    echo "<br/>$usimg";
                }

            }
        }
    }



    public function DayImageDeletedLogs()
    {

        $this->load->model('Menu_model');
        ini_set('max_execution_time', 18000);
        $user               = $this->session->userdata('user');
        $data['user']       = $user;
        $uid                = $user['user_id'];

        $createDate         = date("Y-m-d");

        $dayQuery       = $this->db->query("SELECT * FROM `user_day` WHERE Cast(sdatet as Date) BETWEEN '2025-10-01' AND '2025-11-30'");
        // $dayQuery       = $this->db->query("SELECT * FROM `user_day` WHERE Cast(sdatet as Date) BETWEEN '2025-11-15' AND '2025-11-30'");
        $dayQueryDatas  = $dayQuery->result();

        // dd($dayQueryDatas);

        foreach($dayQueryDatas as $dayQueryData){

            $usimg  = "https://stemapp.in/".$dayQueryData->usimg;
            $ucimg  = "https://stemapp.in/".$dayQueryData->ucimg;

            $usimg_headers = @get_headers($usimg);
            $ucimg_headers = @get_headers($ucimg);

            if($dayQueryData->usimg != 'uploads/day/'){

                if ($usimg_headers && strpos($usimg_headers[0], '200') !== false) {
                
                    $cfullPath = FCPATH . $dayQueryData->usimg;

                    if (unlink($cfullPath)) {
                        echo "Satrt Image deleted <br/> $usimg <hr>";
                        // die;
                    } 
                       
                }
            }
          
            if($dayQueryData->ucimg != 'uploads/day/'){

                if ($ucimg_headers && strpos($ucimg_headers[0], '200') !== false) {
                    
                    $sfullPath = FCPATH . $dayQueryData->ucimg;

                    if (unlink($sfullPath)) {
                         echo "Closed Image deleted <br/> $ucimg <hr>";

                    } 
                   
                }
            }
        }

        dd("Day Images Deleted Successfully");
    }


    public function compress_any_image_replace($relativePath, $quality = 60)
{
    $source = FCPATH . $relativePath;

    if (!file_exists($source)) {
        return "File not found: " . $source;
    }

    if (!is_writable(dirname($source))) {
        return "Directory not writable: " . dirname($source);
    }

    $beforeSize = filesize($source);
    $ext = strtolower(pathinfo($source, PATHINFO_EXTENSION));

    // load image based on type
    switch ($ext) {
        case 'jpg':
        case 'jpeg':
            $img = imagecreatefromjpeg($source);
            break;

        case 'png':
            $img = imagecreatefrompng($source);
            break;

        case 'gif':
            $img = imagecreatefromgif($source);
            break;

        case 'webp':
            if (function_exists('imagecreatefromwebp')) {
                $img = imagecreatefromwebp($source);
            } else {
                return "WEBP not supported by GD on your server.";
            }
            break;

        default:
            return "Unsupported format: " . $ext;
    }

    if (!$img) {
        return "Unable to read image.";
    }

    // convert everything to JPG so size actually reduces
    $width = imagesx($img);
    $height = imagesy($img);

    $bg = imagecreatetruecolor($width, $height);
    $white = imagecolorallocate($bg, 255, 255, 255);
    imagefilledrectangle($bg, 0, 0, $width, $height, $white);
    imagecopy($bg, $img, 0, 0, 0, 0, $width, $height);

    // new path will always be JPG
    $newPath = preg_replace('/\.(jpg|jpeg|png|gif|webp)$/i', '.jpg', $source);

    imagejpeg($bg, $newPath, $quality);

    imagedestroy($img);
    imagedestroy($bg);

    // delete old file if path changed
    if ($newPath !== $source) {
        unlink($source);
    }

    clearstatcache();
    $afterSize = filesize($newPath);

    $result  = "Converted and compressed to JPG.\n";
    $result .= "Before: " . round($beforeSize / 1024, 2) . " KB\n";
    $result .= "After:  " . round($afterSize / 1024, 2) . " KB\n";
    $result .= "Replaced file: " . $newPath . "\n";

    return nl2br($result);
}

public function compress_replace_same_path($relativePath, $quality = 60)
{
    $source = FCPATH . $relativePath;

    if (!file_exists($source)) {
        return "File not found: " . $source;
    }

    if (!is_writable(dirname($source))) {
        return "Directory not writable: " . dirname($source);
    }

    $beforeSize = filesize($source);

    // get image size so GD is forced to process it
    list($width, $height) = getimagesize($source);

    $this->load->library('image_lib');

   

    $config = array(
        'image_library'  => 'gd2',
        'source_image'   => $source,
        'new_image'      => $source,     // overwrite same file
        'maintain_ratio' => true,
        'width'          => $width,
        'height'         => $height,
        'quality'        => $quality
    );
    //  dd($config);

    $this->image_lib->clear();
    $this->image_lib->initialize($config);

    if (!$this->image_lib->resize()) {
        return "Compression failed: " . $this->image_lib->display_errors();
    }else{
        echo "Compression Done. ";
    }

    clearstatcache();
    $afterSize = filesize($source);

    $result  = "Replaced file with compressed version\n";
    $result .= "Before: " . round($beforeSize / 1024, 2) . " KB\n";
    $result .= "After:  " . round($afterSize / 1024, 2) . " KB\n";

    if ($afterSize >= $beforeSize) {
        $result .= "Note: PNG barely reduces with GD. JPG reduces more.\n";
    }

    return nl2br($result);
}

    public function compress_to_new_path($relativePath, $newFolder, $quality = 60)
{
    $source = FCPATH . $relativePath;

    if (!file_exists($source)) {
        return "File not found: " . $source;
    }

    // ensure trailing slash
    $newFolder = rtrim($newFolder, '/') . '/';
    $destinationDir = FCPATH . $newFolder;

    if (!is_dir($destinationDir)) {
        mkdir($destinationDir, 0777, true);
    }

    if (!is_writable($destinationDir)) {
        return "New directory not writable: " . $destinationDir;
    }

    $filename = basename($source);
    $destination = $destinationDir . $filename;

    $beforeSize = filesize($source);

    $this->load->library('image_lib');

    $config = array(
        'image_library' => 'gd2',
        'source_image'  => $source,
        'new_image'     => $destination,
        'quality'       => $quality
    );

    $this->image_lib->clear();
    $this->image_lib->initialize($config);

    if (!$this->image_lib->resize()) {
        return "Compression failed: " . $this->image_lib->display_errors();
    }

    clearstatcache();
    $afterSize = filesize($destination);

    $result  = "Compressed saved to new path.\n";
    $result .= "Before: " . round($beforeSize / 1024, 2) . " KB\n";
    $result .= "After:  " . round($afterSize / 1024, 2) . " KB\n";
    $result .= "New file: " . $destination . "\n";

    return nl2br($result);
}


public function compress_single_image($relativePath, $quality = 60)
{
    $source = FCPATH . $relativePath;

    if (!file_exists($source)) {
        return "File not found: " . $source;
    }

    if (!is_writable(dirname($source))) {
        return "Directory not writable: " . dirname($source);
    }

    $beforeSize = filesize($source);

    $this->load->library('image_lib');

    $config = array(
        'image_library' => 'gd2',
        'source_image'  => $source,
        'new_image'     => $source,
        'quality'       => $quality
    );

    $this->image_lib->clear();
    $this->image_lib->initialize($config);

    if (!$this->image_lib->resize()) {
        return "Compression failed: " . $this->image_lib->display_errors();
    }

    clearstatcache();
    $afterSize = filesize($source);

    $result  = "Compressed and replaced.\n";
    $result .= "Before: " . round($beforeSize / 1024, 2) . " KB\n";
    $result .= "After:  " . round($afterSize / 1024, 2) . " KB\n";

    if ($afterSize >= $beforeSize) {
        $result .= "Note: PNG often shows zero reduction using GD.\n";
    }

    return nl2br($result);
}

public function compress_image($relativePath, $quality = 60)
{
    // build full upload path properly in CI3
    $relativePath = ltrim($relativePath, '/'); // avoid leading slash issue
    $source = FCPATH . $relativePath;          // full absolute path

    // verify file exists
    if (!file_exists($source)) {
        echo "file not found | " . $source;
        return;
    }

    $before = filesize($source);

    // verify it's an image
    $info = getimagesize($source);
    if ($info === false) {
        echo "not an image | " . $source;
        return;
    }

    $mime = $info['mime'];

    // load image based on type
    switch ($mime) {
        case 'image/jpeg':
            $image = imagecreatefromjpeg($source);
            break;

        case 'image/png':
            $image = imagecreatefrompng($source);
            imagealphablending($image, true);
            imagesavealpha($image, true);
            break;

        case 'image/gif':
            $image = imagecreatefromgif($source);
            break;

        case 'image/webp':
            if (function_exists('imagecreatefromwebp')) {
                $image = imagecreatefromwebp($source);
            } else {
                echo "webp not supported on server";
                return;
            }
            break;

        default:
            echo "unsupported format: " . $mime;
            return;
    }

    // create temp file in same directory
    $temp = $source . ".tmp";

    // compress + save temp
    $saved = false;

    if ($mime == 'image/png') {
        $pngCompression = 9 - floor($quality / 10);
        $saved = imagepng($image, $temp, $pngCompression);
    } elseif ($mime == 'image/gif') {
        $saved = imagegif($image, $temp);
    } elseif ($mime == 'image/webp') {
        $saved = imagewebp($image, $temp, $quality);
    } else {
        $saved = imagejpeg($image, $temp, $quality);
    }

    if (!$saved) {
        echo "compression failed";
        return;
    }

    // unlock file
    imagedestroy($image);

    // ensure file is writable before overwrite
    @chmod($source, 0666);

        // try replace
            if (!rename($temp, $source)) {

                $lastError = error_get_last();
                $reason = "";

                if (!is_writable($source)) {
                    $reason = "file not writable";
                } elseif (!is_writable(dirname($source))) {
                    $reason = "directory not writable";
                } elseif (!file_exists($temp)) {
                    $reason = "temp file missing";
                } else {
                    $reason = "file lock or OS restriction";
                }

                unlink($temp);

                echo "failed to replace: " . $reason . 
                    " | system says: " . ($lastError['message'] ?? 'no message') .
                    " | path: " . $source;

                return;
            }

    // recalc final size
    clearstatcache(true, $source);
    $after = filesize($source);

    echo "replaced successfully | before: "
        . round($before/1024,2) . " KB | after: "
        . round($after/1024,2) . " KB";
}



// Start New Update Released in Sales CRM

public function NewUpdateReleasedinSalesCRM()
{
    $user = $this->session->userdata('user');
    if (!$user) {
        dd("User session not found");
        return;
    }

    $this->load->model('Menu_model');
    $this->load->library('email');


    $allUserQuery = $this->db->query("SELECT * FROM user_details WHERE status = 'active' AND (email IS NOT NULL AND email != '') AND type_id IN (3,4,13,24,15,22)");
    $allUserDatas = $allUserQuery->result();

    foreach($allUserDatas as $allUserData){

        $name         = $allUserData->name;
        $userEmail    = $allUserData->email;

        echo $name;
        die("Not Allowed");
        die;
    
    // Email init
    $this->email->initialize();
    $this->email->from('crm.help@stemapp.in', 'CRM Help');

    // Plain text version
$plain = <<<TXT
Hello Team,

A new update has been released in the Sales CRM regarding Meeting Plan cash handling and task workflow improvements.

Key updates:

1. Cash Deduction & Refund Logic
• When a Meeting Plan is created, 500 is deducted.
• If an expense of 300 is added, the remaining 200 is credited.
• If the meeting is planned but not completed, it will follow a two-level approval process (Line Manager and Admin).
• When approved by Admin, the full 500 is credited back.
• Meeting tasks that remain incomplete in the “Planned” state will be removed and the cash refunded.

2. Section-wise Enhancements Added:
• Planner Page
• Assign Task
• Line Manager Assign Task (After Rejection)
• Admin Assign Task (After LM Rejection)
• User Assign Task (After Admin Creation)
• Review Page - Task Creation
• BD Assign Request by Inside Sales - After Find Meeting & Line Manager Approvel

These updates are now active in the system. Kindly review and inform your teams.

Regards,
STEM Team
TXT;


    // HTML version
    $html = <<<HTML
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
</head>
<body style="font-family:Arial, sans-serif; background:#f6f9fc; padding:20px;">

<table width="100%" cellpadding="0" cellspacing="0" style="max-width:650px; margin:0 auto; background:#fff; border-radius:10px; padding:25px; border:1px solid #e5e7eb;">
    <tr>
        <td align="center" style="padding-bottom:20px;">
            <span style="
                display:inline-block;
                font-size:28px;
                font-weight:bold;
                color:#1600da;
                letter-spacing:1px;
            ">
                <a target="_blank" style="text-decoration: none;" href="https://stemapp.in/">📚 Stem Learning Pvt Ltd</a>
            </span>
            <hr>
        </td>
    </tr>

    <tr>
        <td>
            <h2 style="margin:0 0 15px 0; font-size:22px; color:#333;">📢 New Sales CRM Update Released</h2>

            <p style="font-size:16px; color:#444;">
                Hello Team,
            </p>

            <p style="font-size:15px; color:#444;">
                A new update has been released in the Sales CRM regarding Meeting Plan cash handling and task workflow improvements. Please review the details below.
            </p>

            <h3 style="color:#111; margin-top:25px;">1. Cash Deduction & Refund Logic</h3>
            <ul style="font-size:15px; color:#444; line-height:1.6;">
                <li>When a Meeting Plan is created, 500 is deducted.</li>
                <li>If an expense of 300 is added, the remaining 200 is credited.</li>
                <li>Non-completed meetings will follow a two-level approval process (Line Manager and Admin).</li>
                <li>When approved by Admin, the full 500 is credited back.</li>
                <li>Incomplete "Planned" tasks will be removed and cash refunded.</li>
            </ul>

            <p>When a Meeting Plan is created, 500 is deducted.</p>
            <p>If your expense is more than 500:</p>
            <ul>
                <li><code>extra_amount = expense - 500</code></li>
                <li>The extra amount will be deducted from your cash wallet.</li>
            </ul>
            <p>If your cash wallet does not have enough balance to cover the extra amount, you will not be able to update or submit the expense.</p>

            <h3>Impact on Users</h3>
            <ul style="font-size:15px; color:#444; line-height:1.6;">
                <li>Users will experience a more transparent and automated cash deduction and refund system during Meeting Plan activities.</li>
                <li>Line Managers and Admins will follow a clear two-level approval flow when meetings are not completed.</li>
                <li>Task movement between LM, Admin, and User becomes smoother with fewer manual follow-ups.</li>
                <li>Incomplete or abandoned “Planned” meetings will be removed automatically, reducing clutter and confusion.</li>
            </ul>

            <h3>Benefits for Users</h3>
            <ul style="font-size:15px; color:#444; line-height:1.6;">
                <li>Prevents unnecessary cash blockage because refunds now follow automated, rule-based logic.</li>
                <li>Reduces manual errors and miscommunication among users, Line Managers, and Admins.</li>
                <li>Improves accountability with a clearly trackable approval path at every stage.</li>
                <li>Speeds up daily operations by removing unused tasks and reducing pending items.</li>
                <li>Ensures financial accuracy by maintaining correct cash balance at all times.</li>
                <li>Overall increases user efficiency, clarity, and workflow reliability.</li>
            </ul>

            <h3 style="color:#111; margin-top:25px;">2. Section-wise Enhancements Added</h3>
            <ul style="font-size:15px; color:#444; line-height:1.6;">
                <li>Planner Page</li>
                <li>Assign Task</li>
                <li>Line Manager Assign Task (After Rejection)</li>
                <li>User Assign Task (After Admin Creation)</li>
                <li>Review Page - Task Creation</li>
                <li>BD Assign Request by Inside Sales - After Find Meeting & Line Manager Approvel</li>
            </ul>

            <p style="font-size:15px; color:#444; margin-top:20px;">
                These updates are now active. Kindly inform your respective teams.
            </p>

            <p style="font-size:15px; color:#444;">
                Regards,<br>
                <strong>STEM SALES CRM Team</strong><hr>
                <strong>Deepak Kumar Bind</strong>
            </p>
        </td>
    </tr>

</table>

</body>
</html>

HTML;

    // Send mail
    $data = [
        'subject'  => "New Update Released in Sales CRM – Meeting Plan Cash Handling & Task Flow Improvements",
        'message'  => $html,
        'plain'    => $plain,
        // 'tomailID' => $userEmail
        'tomailID' => 'teclwork@gmail.com'
        // 'tomailID' => 'ashutosh@stemlearning.in'
    ];

    $this->sendMail($data);

    die;

}

    die("<br/>STEM PVT LTD");
    // dd("Email Sent Successfully");
}
public function NewUpdateReminderReleasedinSalesCRM()
{
    $user = $this->session->userdata('user');
    if (!$user) {
        dd("User session not found");
        return;
    }

    $this->load->model('Menu_model');
    $this->load->library('email');

die("Not Allowed!");


    $allUserQuery = $this->db->query("SELECT * FROM user_details WHERE status = 'active' AND (email IS NOT NULL AND email != '') AND type_id IN (3,4,13,24,15,22)");
    $allUserDatas = $allUserQuery->result();

    $i=1;
    foreach($allUserDatas as $allUserData){

        $name         = $allUserData->name;
        $userEmail    = $allUserData->email;

        // echo $name;
        // die("Not Allowed");
    
    // Email init
    $this->email->initialize();
    $this->email->from('crm.help@stemapp.in', 'CRM Help');

    // Plain text version
$plain = <<<TXT
Hello Team,

We are excited to announce an upcoming update in the Sales CRM, focusing on Meeting Plan cash handling and task workflow improvements. Below are the key changes:

**Key Updates:**
1. **Cash Deduction & Refund Logic**
   • When a Meeting Plan is created, ₹500 will be deducted.
   • If an expense of ₹300 is added, the remaining ₹200 will be credited.
   • If the meeting is planned but not completed, it will follow a two-level approval process (Line Manager and Admin).
   • When approved by Admin, the full ₹500 will be credited back.
   • Meeting tasks that remain incomplete in the “Planned” state will be removed, and the cash refunded.

2. **Section-wise Enhancements to be Added:**
   • Planner Page
   • Assign Task
   • Line Manager Assign Task (After Rejection)
   • Admin Assign Task (After LM Rejection)
   • User Assign Task (After Admin Creation)
   • Review Page - Task Creation
   • BD Assign Request by Inside Sales - After Find Meeting & Line Manager Approval

These updates will be rolled out soon. We will notify you once they are live. Please prepare your teams accordingly.

Regards,
STEM Team

TXT;


    // HTML version
    $html = <<<HTML
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
</head>
<body style="font-family:Arial, sans-serif; background:#f6f9fc; padding:20px;">
<table width="100%" cellpadding="0" cellspacing="0" style="max-width:650px; margin:0 auto; background:#fff; border-radius:10px; padding:25px; border:1px solid #e5e7eb;">
    <tr>
        <td align="center" style="padding-bottom:20px;">
            <span style="
                display:inline-block;
                font-size:28px;
                font-weight:bold;
                color:#1600da;
                letter-spacing:1px;
            ">
                <a target="_blank" style="text-decoration: none;" href="https://stemapp.in/">📚 Stem Learning Pvt Ltd</a>
            </span>
            <hr>
        </td>
    </tr>
    <tr>
        <td>
            <h2 style="margin:0 0 15px 0; font-size:22px; color:#333;">📢 Upcoming Sales CRM Update</h2>
            <p style="font-size:16px; color:#444;">
                Hello Team,
            </p>
            <p style="font-size:15px; color:#444;">
                We are excited to announce an upcoming update in the Sales CRM, focusing on Meeting Plan cash handling and task workflow improvements. Below are the key changes:
            </p>
            <h3 style="color:#111; margin-top:25px;">1. Cash Deduction & Refund Logic</h3>
            <ul style="font-size:15px; color:#444; line-height:1.6;">
                <li>When a Meeting Plan is created, ₹500 will be deducted.</li>
                <li>If an expense of ₹300 is added, the remaining ₹200 will be credited.</li>
                <li>Non-completed meetings will follow a two-level approval process (Line Manager and Admin).</li>
                <li>When approved by Admin, the full ₹500 will be credited back.</li>
                <li>Incomplete "Planned" tasks will be removed, and cash refunded.</li>
            </ul>
            <p>If your expense is more than ₹500:</p>
            <ul>
                <li><code>extra_amount = expense - 500</code></li>
                <li>The extra amount will be deducted from your cash wallet.</li>
            </ul>
            <p>If your cash wallet does not have enough balance to cover the extra amount, you will not be able to update or submit the expense.</p>

            <h3>Impact on Users</h3>
            <ul style="font-size:15px; color:#444; line-height:1.6;">
                <li>Users will experience a more transparent and automated cash deduction and refund system during Meeting Plan activities.</li>
                <li>Line Managers and Admins will follow a clear two-level approval flow when meetings are not completed.</li>
                <li>Task movement between LM, Admin, and User becomes smoother with fewer manual follow-ups.</li>
                <li>Incomplete or abandoned “Planned” meetings will be removed automatically, reducing clutter and confusion.</li>
            </ul>

            <h3>Benefits for Users</h3>
            <ul style="font-size:15px; color:#444; line-height:1.6;">
                <li>Prevents unnecessary cash blockage because refunds now follow automated, rule-based logic.</li>
                <li>Reduces manual errors and miscommunication among users, Line Managers, and Admins.</li>
                <li>Improves accountability with a clearly trackable approval path at every stage.</li>
                <li>Speeds up daily operations by removing unused tasks and reducing pending items.</li>
                <li>Ensures financial accuracy by maintaining correct cash balance at all times.</li>
                <li>Overall increases user efficiency, clarity, and workflow reliability.</li>
            </ul>

            <h3 style="color:#111; margin-top:25px;">2. Section-wise Enhancements to be Added</h3>
            <ul style="font-size:15px; color:#444; line-height:1.6;">
                <li>Planner Page</li>
                <li>Assign Task</li>
                <li>Line Manager Assign Task (After Rejection)</li>
                <li>Admin Assign Task (After LM Rejection)</li>
                <li>User Assign Task (After Admin Creation)</li>
                <li>Review Page - Task Creation</li>
                <li>BD Assign Request by Inside Sales - After Find Meeting & Line Manager Approval</li>
            </ul>

            <p style="font-size:15px; color:#444; margin-top:20px;">
                These updates will be rolled out soon. We will notify you once they are live. Please prepare your teams accordingly.
            </p>
            <p style="font-size:15px; color:#333; font-family:Arial, sans-serif;">
                Regards ✨<br>
                <strong style="color:#1a73e8;">STEM Learning Pvt Ltd</strong>
                <hr style="border:0; border-top:1px solid #ccc;">

                <strong style="color:#0b8043;">
                    Deepak Kumar Bind (Full Stack Developer) 👨‍💻
                </strong>
                <hr style="border:0; border-top:1px solid #ccc;">

                <strong style="color:#d93025;">
                    Mobile Number: <a href="tel:8369000263" style="color:#d93025; text-decoration:none;">📞 +91 8369000263</a>
                </strong>
            </p>

        </td>
    </tr>
</table>
</body>
</html>


HTML;

    // Send mail
    $data = [
        'subject'  => "New Upcoming Sales CRM Update – Meeting Plan Cash Handling & Task Flow Improvements",
        'message'  => $html,
        'plain'    => $plain,
        // 'tomailID' => $userEmail
        'tomailID' => 'teclwork@gmail.com'
    ];

    

    echo $i.')'.$this->sendMail($data).'<hr/>';

    die;
$i++;
}

    die("<br/>STEM PVT LTD");
    // dd("Email Sent Successfully");
}


// Closed New Update Released in Sales CRM


















    public function PermanentDeletionofLeadsFromDatabase()
    {


        dd("NOT ALLOWED");

        $user           = $this->session->userdata('user');
        $data['user']   = $user;
        $uid            = $user['user_id'];

        $this->load->model('Menu_model');


        $taskQuery      = $this->db->query("SELECT 
    init_call.id AS init_call_id,
    company_master.id AS cid,
    company_master.compname,
    init_call.mainbd
FROM 
    init_call
LEFT JOIN company_master ON company_master.id = init_call.cmpid_id
LEFT JOIN user_details u1 ON u1.user_id = init_call.mainbd

WHERE 
  
init_call.cmpid_id IN (72006, 72007, 72008, 72009, 72010, 72011, 72012, 72013, 72014, 72015, 72016, 72017, 72018, 72019, 72020, 72021, 72022, 72023, 72024, 72025, 72085, 72086, 72087, 72088, 72089, 72090, 72091, 72092, 72093, 72094, 72095, 72096, 72097, 72098, 72099, 72100, 72101, 72102, 72103, 72104, 72105, 72106, 72107, 72108, 72109, 72110)");
        $csv_data = $taskQuery->result();

        // dd($taskQueryDatas);

          foreach($csv_data as $cdata){

                    $cid        = $cdata->cid;
                    $cmpDatas   = $this->Menu_model->getCompanyStatus($cid);

                    if(sizeof($cmpDatas) > 0){

                        $init_id        = $cdata->init_call_id;
                        $mainbd         = $cdata->mainbd;
                        $cmpid_id       = $cdata->cid;
                        

                        $taskQuery=$this->db->query("SELECT * FROM `tblcallevents` WHERE cid_id = '$init_id' AND nextCFID = 0 AND ( delete_request = '' OR delete_request IS NULL ) AND ( approved_status = 1 OR approved_status = '' )");
                        $taskQueryDatas = $taskQuery->result();

                          if($taskQueryDatas > 0){
                                foreach($taskQueryDatas as $taskQueryData){
                                    $taskID         = $taskQueryData->id;
                                    $taskactionID   = $taskQueryData->actiontype_id;

                                    if(in_array($taskactionID,[3,4])){
                                        $query = $this->db->query("DELETE FROM `barginmeeting` WHERE tid = '$taskID'");
                                        $query = $this->db->query("DELETE FROM `tblcallevents` WHERE id = '$taskID'");

                                        echo $k.' = '.$cid ." => Pending Task Deleted successfully.<br/> ";
                                    }else{
                                        $query = $this->db->query("DELETE FROM `tblcallevents` WHERE id = '$taskID'");
                                        echo $k.' = '.$cid ." => Pending Task Deleted successfully.<br/> ";
                                    }
                                }
                          }

                        $data = array(
                            'mainbd'                        => 1000334,
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
                            'cluster_id'                    => 0,
                        );

                        $this->db->where('cmpid_id', $cid); 
                        $this->db->update('init_call', $data);

                        if ($this->db->affected_rows() > 0) {

                            $data = array(
                                    'init_id'                         => $init_id,
                                    'cid'                             => $cid,
                                    'old_main_bd'                     => $mainbd,
                                    'new_main_bd'                     => 1000334,
                                    'old_clm_bd'                      => 0,
                                    'new_clm_bd'                      => 0,
                                    'old_apst'                        => 0,
                                    'new_apst'                        => 0,
                                    'old_rm_east'                     => 0,
                                    'new_rm_east'                     => 0,
                                    'old_rm_north'                    => 0,
                                    'new_rm_north'                    => 0,
                                    'old_ash_nae'                     => 0,
                                    'new_ash_nae'                     => 0,
                                    'old_ash_w'                       => 0,
                                    'new_ash_w'                       => 0,
                                    'old_ash_s'                       => 0,
                                    'new_ash_s'                       => 0,
                                    'old_acm'                         => 0,
                                    'new_acm'                         => 0,
                                    'old_cluster_id'                  => 0,
                                    'new_cluster_id'                  => 0,
                                    'update_by'                       => $uid,
                                );

                                $this->db->insert('company_log', $data);

                           echo $k.' = '.$cid ." => Leads transfer successfully.<br/> ";
                        }else{
                            echo $k.' = '.$cid ." => Leads Not Deleted <br/>";
                        }
                    }
                    $k++;
                }
                 dd("STEM LEARNING PVT LTD");




    }













    public function MarkedWDL_Leads()
    {
         dd("Not Allowed !");

        $user           = $this->session->userdata('user');
        $data['user']   = $user;
        $uid            = $user['user_id'];

        $this->load->model('Menu_model');

        $file_path = 'https://stemapp.in/uploads/CompanyCSV/request_marking_CRM_WDL.csv';

          if (($handle = fopen($file_path, "r")) !== FALSE) {
                $csv_data = array();
                while (($row = fgetcsv($handle, 1000, ",")) !== FALSE) {
                    $csv_data[] = $row;
                }
                fclose($handle);
                unset($csv_data[0]);

                dd("Not Allowed !");
                dd($csv_data);

                $k=1;
                foreach($csv_data as $cdata){

                    $cid            = $cdata[6]; // CID
                    $remarks        = $cdata[7]; // Remarks
                    $remarks        = str_replace(["`", "'"], "",$remarks);
                    $cmpDatas       = $this->Menu_model->getCompanyStatus($cid);

                    if(!empty($cid) && sizeof($cmpDatas) > 0){

                        $init_id        = $cmpDatas[0]->id;
                        $mainbd         = $cmpDatas[0]->mainbd;
                        $apst           = $cmpDatas[0]->apst;
                        $clm_id         = $cmpDatas[0]->clm_id;
                        $ash_nae_co_id  = $cmpDatas[0]->ash_nae_co_id;
                        $ash_w_co_id    = $cmpDatas[0]->ash_w_co_id;
                        $ash_s_co_id    = $cmpDatas[0]->ash_s_co_id;
                        $rm_east_co_id  = $cmpDatas[0]->rm_east_co_id;
                        $rm_north_co_id = $cmpDatas[0]->rm_north_co_id;
                        $acm_co_id      = $cmpDatas[0]->acm_co_id;
                        $cluster_id     = $cmpDatas[0]->cluster_id;
                        $cmpid_id       = $cmpDatas[0]->cmpid_id;

                        $cstatus        = $cmpDatas[0]->cstatus;  // Current Status
                        $lstatus        = $cmpDatas[0]->lstatus;  // Last Status

                        $new_staus      = 4; // Will do Later
                        $date           = date("Y-m-d H:i:s");

                        // dd($cmpDatas);

                        $data = [
                            'cid_id'                => $init_id,
                            'user_id'               => 100000,
                            'purpose_achieved'      => "yes",
                            'actontaken'            => "yes",
                            'assignedto_id'         => 100000,
                            'actiontype_id'         => 1,
                            'updateddate'           => $date,
                            'date'                  => $date,
                            'fwd_date'              => $date,
                            'appointmentdatetime'   => $date,
                            'plandt'                => $date,
                            'plan'                  => '1',
                            'status_id'             => $cstatus,
                            'lastCFID'              => 0,
                            'nextCFID'              => 0,
                            'nstatus_id'            => $new_staus,
                            'purpose_id'            => 77,
                            'remarks'               => "$remarks",
                            'selectby'              => "Status",
                            'filter_by'             => '{"comp_status":"4"}',
                            'assignedto_by'         => 100000,
                            'approved_status'       => 1,
                            'approved_by'           => 100000,
                            'approved_date'         => "$date"
                        ];

                        $this->db->insert('tblcallevents',$data);
                        $tid = $this->db->insert_id();

                        if($tid){

                            $data_init = array(
                                'cstatus'           => $new_staus,
                                'lstatus'           => $cstatus
                            );

                            $this->db->where('id', $init_id); 
                            $this->db->update('init_call', $data_init);

                            $data_tbl = array(
                                'lastCFID'              => $tid,
                                'nextCFID'              => $tid
                            );

                            $this->db->where('id', $tid); 
                            $this->db->update('tblcallevents', $data_tbl);
                            echo $k.' = '.$cid ." => Leads Status Updated successfully.<br/> ";
                            
                        }
                        
                    }else{
                        echo $k." => $CID CID Not Found <br/>";
                        continue;
                    }
                    $k++;

                    // die("Deepak");
                }

                }
                 dd("STEM LEARNING PVT LTD");
            }







   public function CRMCashMaapingEqualToWallet()
    {


        dd("Not Allowed !");    

        $user           = $this->session->userdata('user');
        $data['user']   = $user;
        $uid            = $user['user_id'];
        $this->load->model('Menu_model');
        
        $bd_user_id     = 1000237;
        $taskQuery      = $this->db->query("SELECT * FROM `user_details` WHERE user_id = '$bd_user_id'");
        $taskQueryDatas = $taskQuery->result();

        $bd_ucash       = $taskQueryDatas[0]->ucash;

        $cashExpenseQuery       = $this->db->query("SELECT * FROM `cash_expense` WHERE user_id = '$bd_user_id' AND expense > 50");
        $cashExpenseDatas       = $cashExpenseQuery->result();

        foreach($cashExpenseDatas as $cashExpenseData){
            
            $cashExpenseID  = $cashExpenseData->id;
            $cashExpense    = $cashExpenseData->expense;

            $bd_ucash += $cashExpense;

            echo "bd_ucash = ".$bd_ucash."<br/>";

            if($bd_ucash >= 6000){

                if($bd_ucash > 6000){
                    $big_ucash      = $bd_ucash - 6000;
                    $new_ucash      = $bd_ucash - $big_ucash;
                    
                    $ncashExpenseQuery          = $this->db->query("SELECT * FROM `cash_expense` WHERE user_id = '$bd_user_id'");
                    $ncashExpenseDatas          = $ncashExpenseQuery->result();
                    $ncashExpenseID             = $ncashExpenseDatas[0]->id;
                    $ncashExpense               = $ncashExpenseDatas[0]->expense;
                    $ncashExpense               = $ncashExpense + $big_ucash;

                    //  $this->db->query("UPDATE `cash_expense` SET `expense` = '$ncashExpense' WHERE id = '$ncashExpenseID'");
                }
                // $this->db->query("UPDATE `user_details` SET `ucash` = '$new_ucash' WHERE user_id = '$bd_user_id'");

                echo "completed = ".$bd_ucash." => ".$big_ucash." => ".$new_ucash."<br/>";
                break; // stop loop immediately
            }else{
                // $this->db->query("UPDATE `cash_expense` SET `expense` = '0' WHERE id = '$cashExpenseID'");
            }
        }

    }





   public function MinusCRMCashMaapingEqualToWallet()
    {


        dd("Not Allowed !");    

        $user           = $this->session->userdata('user');
        $data['user']   = $user;
        $uid            = $user['user_id'];
        $this->load->model('Menu_model');
        
        $bd_user_id     = 1000237;
        $taskQuery      = $this->db->query("SELECT * FROM `user_details` WHERE user_id = '$bd_user_id'");
        $taskQueryDatas = $taskQuery->result();

        $bd_ucash       = $taskQueryDatas[0]->ucash;

        $totalAdvancedAmmount   = 22000;

        $cashExpenseDatas      = $this->Report_model->GetTravelExpenseTotalSumBySingleUser($bd_user_id);
        $total_expense_by_user = 0;

        foreach ($cashExpenseDatas as $item) {
            $total_expense_by_user += $item->expense;
        }

        foreach($cashExpenseDatas as $cashExpenseData){
            
            $cashExpenseID  = $cashExpenseData->id;
            $cashExpense    = $cashExpenseData->expense;

            if($cashExpense == 0){
                continue;
            }

            if($totalAdvancedAmmount < $total_expense_by_user){
                $total_expense_by_user -= $cashExpense;
                echo $total_expense_by_user." => ".$cashExpense."<br/>";
                // $this->db->query("UPDATE `cash_expense` SET `expense` = '0' WHERE id = '$cashExpenseID'");
            }else{

                if($total_expense_by_user < $totalAdvancedAmmount){
                    $reming             = $totalAdvancedAmmount - $total_expense_by_user;
                    // $this->db->query("UPDATE `cash_expense` SET `expense` = '$reming' WHERE id = '$cashExpenseID'");
                }
                // $this->db->query("UPDATE `user_details` SET `ucash` = '0' WHERE user_id = '$bd_user_id'");
                dd("total_expense_by_user = ".$total_expense_by_user);
            }



        }

    }












    }









 

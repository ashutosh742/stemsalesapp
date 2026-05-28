<?php
date_default_timezone_set("Asia/Calcutta");
defined('BASEPATH') OR exit('No direct script access allowed');
require_once('Menu.php');
class Management extends Menu {

    private $user;
    private $uid;
    private $uyid;
    private $dep_name;
    private $dt;

    public function __construct() {
        parent::__construct();
        // Load common libraries, helpers, or models here
        $this->load->helper('url');
        $this->load->library('session');
        $this->load->model('Menu_model');
        $this->load->model('Graph_Model');

        $this->load->model('Management_model');
        $this->load->helper('samestatustilldate_helper');


        $this->user = $this->session->userdata('user');
        $this->uid = !empty($this->user) ? $this->user['user_id'] : null;
        $this->uyid = !empty($this->user) ? $this->user['type_id'] : null;

        $this->dt = $this->uyid ? $this->Menu_model->get_utype($this->uyid) : [];

        $this->dep_name = (is_array($this->dt) && !empty($this->dt) && isset($this->dt[0]->name)) ? $this->dt[0]->name : 'home';
   
        if ($this->uyid !== null && in_array(!$this->uyid, [15, 13, 2, 4])) {
            echo "Stem Learning Pvt Ltd";
            echo "<br/>";
            exit;
        }
        
    }

    public function Manage() {

        if(!empty($this->user)){
            $this->load->view($this->dep_name.'/Manage',['uid'=>$this->uid,'user'=>$this->user]);
        }else{
            redirect('Menu/main');
        }
    }

    public function CheckingDayManagement(){

        $cdate = date("Y-m-d");
        $sdate = new DateTime($cdate);
        $sdate->modify('-1 day');
        $previousDate = $sdate->format('Y-m-d');

        $dayData = $this->Management_model->CheckingDayManage($this->uid,$cdate);
        
        if(!empty($this->user)){
            $this->load->view($this->dep_name.'/CheckingDayManagement',['uid'=>$this->uid,'user'=>$this->user,'dayData'=>$dayData,'cdate'=>$cdate,'previousDate'=>$previousDate]);
        }else{
            redirect('Menu/main');
        }
    }
  
    public function CheckingYesterDayTask($type,$userid,$sdate){
        
        // $sdate = new DateTime($sdate);
        // $sdate->modify('-1 day');
        $previousDate = $sdate;

        $dayData = $this->Management_model->CheckingTotalYestTask($userid,$previousDate,$type);
        
       $cdate = date("Y-m-d");
        if(!empty($this->user)){
            $this->load->view($this->dep_name.'/CheckingYesterTask',['uid'=>$this->uid,'user'=>$this->user,'dayData'=>$dayData,'cdate'=>$cdate,'previousDate'=>$previousDate]);
        }else{
            redirect('Menu/main');
        }
    }


    public function checkdayswithStar(){
        $periods = $_POST['periods'];
        $suser_id = $_POST['udid'];
        $cdate = $_POST['cdate'];
        $previousDate = $_POST['previousDate'];

        $rat1 = $_POST['rat1'];
        $rat2 = $_POST['rat2'];
        $rat3 = $_POST['rat3'];
        $rat4 = $_POST['rat4'];
       
        $que = $_POST['que'];
        $remarks = $_POST['sremark'];
        
        if($periods == 'Mornings'){
            $rat5 = $_POST['rat5'];
            $rat6 = $_POST['rat6'];
            $star = [$rat1,$rat2,$rat3,$rat4,$rat5,$rat6];
            $i=0;
            foreach($que as $q){
                $this->Management_model->AddStarRating($cdate,$suser_id,$periods,$q,$star[$i],$remarks,$this->uid);
                $i++;
            }
        }

        if($periods == 'Yesterday Evening' || $periods == 'Yesterday Task'){
            $star = [$rat1,$rat2,$rat3,$rat4];
            $i=0;
            foreach($que as $q){
                $this->Management_model->AddStarRating($previousDate,$suser_id,$periods,$q,$star[$i],$remarks,$this->uid);
                $i++;
            }
        }

        $this->session->set_flashdata('success_message', 'Star Rating Added Successfully');
        redirect('Management/CheckingDayManagement');
    }




    
    // MOM Start Here

    public function MoMApprovedStatus(){

        $user = $this->session->userdata('user');
        if(empty($user) || empty($user['user_id'])){ redirect('login'); return; }
        // session guard added 28 May 2026 by parallel-fix agent D

        if(isset($_POST['sdate'])){
            $cdate = $_POST['sdate'];
           
        }else{
            $cdate = date("Y-m-d");
        }
        if(isset($_POST['edate'])){
            $edate = $_POST['edate'];
        }else{
            $edate = date("Y-m-d");
        }

        $sdate = new DateTime($cdate);
        $sdate->modify('-1 day');

        $previousDate = $sdate->format('Y-m-d');
    
        if(!empty($this->user)){
                $this->load->view('Functions/MoMApprovedStatus',['uid'=>$this->uid,'user'=>$this->user,'cdate'=>$cdate,'edate'=>$edate,'previousDate'=>$previousDate,'dep_name'=>$this->dep_name]);
            }else{
                redirect('Menu/main');
            }
    }
    public function OurMoMApprovedStatus(){

        if(isset($_POST['sdate'])){
            $cdate = $_POST['sdate'];
           
        }else{
            $cdate = date("Y-m-d");
        }
        if(isset($_POST['edate'])){
            $edate = $_POST['edate'];
        }else{
            $edate = date("Y-m-d");
        }

        $sdate = new DateTime($cdate);
        $sdate->modify('-1 day');

        $previousDate = $sdate->format('Y-m-d');
    
        if(!empty($this->user)){
                $this->load->view($this->dep_name.'/OurMoMApprovedStatus',['uid'=>$this->uid,'user'=>$this->user,'cdate'=>$cdate,'edate'=>$edate,'previousDate'=>$previousDate]);
            }else{
                redirect('Menu/main');
            }
    }

    public function MomData($suid,$tdate){

        $cdate = date("Y-m-d");
        $sdate = new DateTime($cdate);
        $sdate->modify('-1 day');
        $previousDate = $sdate->format('Y-m-d');
        $momdata = $this->Management_model->getBDMoMData($suid,$tdate);

        if(!empty($this->user)){
                $this->load->view($this->dep_name.'/MomData',['uid'=>$this->uid,'user'=>$this->user,'cdate'=>$cdate,'previousDate'=>$previousDate,'suid'=>$suid,'momdata'=>$momdata,'tardate'=>$tdate]);
            }else{
                redirect('Menu/main');
            }
    }


     public function PendingTeamMomData($suid,$tdate){

        $cdate = date("Y-m-d");
        $sdate = new DateTime($cdate);
        $sdate->modify('-1 day');
        $previousDate = $sdate->format('Y-m-d');
        $momdata = $this->Management_model->getBDMoMData($suid,$tdate);

        if(!empty($this->user)){
                $this->load->view('Functions/PendingTeamMomData',['uid'=>$this->uid,'user'=>$this->user,'cdate'=>$cdate,'previousDate'=>$previousDate,'suid'=>$suid,'momdata'=>$momdata,'tardate'=>$tdate,'dep_name'=>$this->dep_name]);
            }else{
                redirect('Menu/main');
            }
    }

    public function OurPendingMomData($suid,$tdate){

        $cdate = date("Y-m-d");
        $sdate = new DateTime($cdate);
        $sdate->modify('-1 day');
        $previousDate = $sdate->format('Y-m-d');
        $momdata = $this->Management_model->getBDMoMData($suid,$tdate);

        if(!empty($this->user)){
                $this->load->view($this->dep_name.'/OurPendingMomData',['uid'=>$this->uid,'user'=>$this->user,'cdate'=>$cdate,'previousDate'=>$previousDate,'suid'=>$suid,'momdata'=>$momdata,'tardate'=>$tdate]);
            }else{
                redirect('Menu/main');
            }
    }


    public function DayManagementReportPerAdmin($adminid)
    {
       $getAllBDArray     = $this->Menu_model->getAllBD($adminid);

        $sdate = date('Y-m-d');

       foreach($getAllBDArray as $key=>$val){

		$this->DayManagementReportPerUser($val['user_id'],$adminid);

           }

    }

    public function DayManagementReportPerUser($userid,$adminid)
    {  
       $sdate = date("Y-m-d");
       echo "test 1 :-".time("h:i:s");
        $starRatingQuery            = $this->db->query("SELECT * FROM `star_rating` WHERE user_id= '".$userid."' AND date = '".$sdate."' ");
        $starRatingReportdata       = $starRatingQuery->result();
       
        $dayData                    = $this->Management_model->CheckingDayManage_New($userid,$sdate);
        echo "test 3 :-".time("h:i:s");
        $previousDate               = date('Y-m-d',strtotime('-1 day',strtotime($sdate)));
        $yesterdayData              = $this->Management_model->CheckingYesterdyDayManage_New($userid,$previousDate);
        echo "test 4 :-".time("h:i:s");
        $star_rating                = $this->Management_model->CheckStarRatingsExistorNot($userid,$sdate);


        foreach($starRatingReportdata as $key=>$val){
          if($val->periods == 'Mornings'){
                $newarray[$userid][$val->periods]       = $dayData[0];
               $newarray[$userid][$val->periods]->$key  = $val;
          }
          else if($val->periods == 'Yesterday Evening'){
            $newarray[$userid][$val->periods]           = $yesterdayData[0];
            $newarray[$userid][$val->periods]->$key     = $val;
          }
          else if($val->periods == 'Yesterday Task'){
            $newarray[$userid][$val->periods]           = $yesterdayData[0];
            $newarray[$userid][$val->periods]->$key     = $val;
          }
        }

        $user                       = $this->session->userdata('user');
        $data['user']               = $user;
        $data['uid']                = $user['user_id'];
        $data['userTypeid']         = $user['type_id'];
        $userTypeid                 = $user['type_id'];
        $data['getReportbyUser']    = $newarray;

        $dt         = $this->Graph_Model->get_utype($userTypeid);
       
        $dep_name   = $dt[0]->name;
        $getUsers   = $this->Management_model->getUsers($uid,$userTypeid);
        $cdate      = "$sdate";

        if (!empty($user)){
            $this->load->view('Admin/Daymanagement3',$data);
        } else {
            redirect('Menu/main');
        }
    }


    public function TotalMomData($suid,$tdate){

        $cdate = date("Y-m-d");
        $sdate = new DateTime($cdate);
        $sdate->modify('-1 day');
        $previousDate = $sdate->format('Y-m-d');
        $momdata = $this->Management_model->getBDMoMData($suid,$tdate);

        if(!empty($this->user)){
                $this->load->view('Functions/TotalMomData',['uid'=>$this->uid,'user'=>$this->user,'cdate'=>$cdate,'previousDate'=>$previousDate,'suid'=>$suid,'momdata'=>$momdata,'tardate'=>$tdate,'dep_name'=>$this->dep_name]);
            }else{
                redirect('Menu/main');
            }
    }
    public function TotalApproveMomData($suid,$tdate){

        $cdate = date("Y-m-d");
        $sdate = new DateTime($cdate);
        $sdate->modify('-1 day');
        $previousDate = $sdate->format('Y-m-d');
        $momdata = $this->Management_model->getBDMoMData($suid,$tdate);

        if(!empty($this->user)){
                $this->load->view('Functions/TotalApproveMomData',['uid'=>$this->uid,'user'=>$this->user,'cdate'=>$cdate,'previousDate'=>$previousDate,'suid'=>$suid,'momdata'=>$momdata,'tardate'=>$tdate,'dep_name'=>$this->dep_name]);
            }else{
                redirect('Menu/main');
            }
    }


    public function MomDataReject($suid,$tdate){

        $cdate = date("Y-m-d");
        $sdate = new DateTime($cdate);
        $sdate->modify('-1 day');
        $previousDate = $sdate->format('Y-m-d');
        $momdata = $this->Management_model->getBDMoMData($suid,$tdate);

        if(!empty($this->user)){
                $this->load->view('Functions/MomDataReject',['uid'=>$this->uid,'user'=>$this->user,'cdate'=>$cdate,'previousDate'=>$previousDate,'suid'=>$suid,'momdata'=>$momdata,'tardate'=>$tdate,'dep_name'=>$this->dep_name]);
            }else{
                redirect('Menu/main');
            }
    }

    public function OurMomDataReject($suid,$tdate){

        $cdate = date("Y-m-d");
        $sdate = new DateTime($cdate);
        $sdate->modify('-1 day');
        $previousDate = $sdate->format('Y-m-d');
        $momdata = $this->Management_model->getBDMoMData($suid,$tdate);

        if(!empty($this->user)){
                $this->load->view($this->dep_name.'/OurMomDataReject',['uid'=>$this->uid,'user'=>$this->user,'cdate'=>$cdate,'previousDate'=>$previousDate,'suid'=>$suid,'momdata'=>$momdata,'tardate'=>$tdate]);
            }else{
                redirect('Menu/main');
            }
    }

    public function MomDataDisplay($suid,$tdate,$t_id){

        $cdate = date("Y-m-d");
        $sdate = new DateTime($cdate);
        $sdate->modify('-1 day');
        $previousDate = $sdate->format('Y-m-d');
        $momdata = $this->Management_model->getBDMoMDataWthTid($suid,$t_id);
        // echo $str = $this->db->last_query(); 

        if(!empty($this->user)){
                $this->load->view($this->dep_name.'/MomDataDisplay',['uid'=>$this->uid,'user'=>$this->user,'cdate'=>$cdate,'previousDate'=>$previousDate,'momdata'=>$momdata,'t_id'=>$t_id,'tdate'=>$tdate]);
            }else{
                redirect('Menu/main');
            }
    }

    public function MomRejectByUserAdmin(){

        $rejectId = $_POST['reject'];
        $rejectreamrk = $_POST['rejectreamrk'];
       
        $rejectDate = date("y-m-d H:i:s");
        $approved_status = 'Reject';

        $suid = $_POST['suid'];
        $tardate = $_POST['tardate'];

        $momdata = $this->Management_model->MomRejectByUserAdminInsert($approved_status,$rejectId,$rejectreamrk,$rejectDate,$this->uid);
      
        redirect('Management/MomData/'.$suid.'/'.$tardate);
      
    }

    // public function MomApprovedByUserAdmin($id,$status,$suid,$tardate){
    public function MomApprovedByUserAdmin(){

        $id                 = $_POST['mom_id'];
        $suid               = $_POST['suid'];
        $tardate            = $_POST['tardate'];
        $right_remarks      = $_POST['right_remarks'];
        $mom_yes_no         = $_POST['mom_yes_no'];
        $finalRemarks       = $right_remarks.' - '.$mom_yes_no;
        $approvedtDate      = date("y-m-d H:i:s");
        $approved_status    = 'Approved';
        $approvedreamrk     = $finalRemarks;
        $momdata            = $this->Management_model->getMomByID($id);
        $init_cmpid         = $momdata[0]->init_cmpid;
        $tid_calleve        = $momdata[0]->tid;
        $ccstatus           = $momdata[0]->ccstatus;
        $mom_user_id        = $momdata[0]->user_id;
        $fwd_date           = '';
        // $fwd_date = date("Y-m-d h:i:s");
        $actiontype_id      = 1;
        $init_id            = $init_cmpid;
        $nextaction         = 1;
        $ass_user_id        = $this->uid;
        $purpose_id         = 1;
        $autotask           = 0;
        $auto_plan          = 0;


        $this->load->model('Menu_model');
        $nextWorkingDate            = $this->Menu_model->findNextSpecialDate(date("Y-m-d"));
        
        $checktaskexistsornot       = $this->Management_model->GetLastTaskAppointmentdatetime($ass_user_id,$nextWorkingDate);
        if(sizeof($checktaskexistsornot) > 0){
            $checktaskexistsornot   = $checktaskexistsornot[0]->appointmentdatetime;
            $fwd_date               = date('Y-m-d H:i:s', strtotime($checktaskexistsornot . ' +5 minutes'));
        }else{
            $fwd_date               = $nextWorkingDate." 16:00:00";  
        }
        
        $this->Management_model->MomApprovedByUserAdminInsert($approved_status,$id,$approvedreamrk,$approvedtDate,$this->uid,$finalRemarks);

        $task_remarks       = "Task Create After MOM Approved";
        $insert_id          = $this->Management_model->CreateTask($fwd_date,$actiontype_id,$init_id,$nextaction,$ass_user_id,$purpose_id,$autotask,$auto_plan,$ccstatus,$task_remarks);
       
        $this->Management_model->CreateTaskForAutoAssign($ass_user_id,$ass_user_id,$ccstatus,$init_id,$insert_id,$actiontype_id,$id,$task_remarks);
      
        $cudetail           = $this->Menu_model->get_userbyid($mom_user_id);
        $get_pst_co         = $cudetail[0]->pst_co;
        $acm_co             = $cudetail[0]->acm_co;
        $get_utype_id       = $cudetail[0]->type_id;

        if($get_utype_id == 13){
            $clm_aadmin     = $cudetail[0]->user_id;
        }else{
            $clm_aadmin     = $cudetail[0]->aadmin;
        }
        
        
        $this->Management_model->AssignCLMAfterMomApproved($init_cmpid,$clm_aadmin);

        if($get_pst_co !=''){
            $pstdetail              = $this->Menu_model->get_userbyid($get_pst_co);
            if(sizeof($pstdetail) > 0){
                $get_pst_type_id    = $pstdetail[0]->type_id;
                if($get_pst_type_id == 4){
                    $this->Management_model->AssignPSTAfterMomApproved($init_cmpid,$get_pst_co);
                }
            }
        }

        if($acm_co !=''){
            $acmdetail              = $this->Menu_model->get_userbyid($acm_co);
            if(sizeof($acmdetail) > 0){
                $get_acm_type_id    = $acmdetail[0]->type_id;
                if($get_acm_type_id == 24){
                    $this->Management_model->Assign_ACM_After_MomApproved($init_cmpid,$acm_co);
                }
            }
        }

        $insert_id = $this->Management_model->CreateTask($fwd_date,$actiontype_id,$init_id,$nextaction,$get_pst_co,$purpose_id,$autotask,$auto_plan,$ccstatus,$task_remarks);
        $this->Management_model->CreateTaskForAutoAssign($ass_user_id,$get_pst_co,$ccstatus,$init_id,$insert_id,$actiontype_id,$id,$task_remarks);

        redirect('Management/MomData/'.$suid.'/'.$tardate);

    }



    public function MomApprovedAndPSTAssign(){
        
        // PST Assign After 48 Hours 
        $approvedtDate = date("y-m-d H:i:s");
        $cur_user = $this->uid;

        // $momdata = $this->Management_model->AssignPSTAfterMomApproved();
        $momdata = $this->Management_model->getApprovedMom_notAssignPST();
        foreach($momdata as $mdata){

            $mom_user_id = $mdata->user_id;
            $init_cmpid = $mdata->init_cmpid;
            $tid_calleve = $mdata->tid;

            $cudetail = $this->Menu_model->get_userbyid($mom_user_id);
            $get_pst_co = $cudetail[0]->pst_co;

            // $get_cmp = $this->Menu_model->get_cmpbyinid($init_cmpid);
            // $this->Management_model->AssignPSTAfterMomApprTwoHours($get_pst_co,$init_cmpid);

        }

        die;
       

    }


    public function MomEditByUser($stid,$page_status,$suid,$tardate){

        $momdata = $this->Management_model->getBDMoMDataWthTid($suid,$stid);
     
        if(!empty($this->user)){
                $this->load->view('Functions/MomEditByUser',['uid'=>$this->uid,'user'=>$this->user,'stid'=>$stid,'page_status'=>$page_status,'momdata'=>$momdata,'tardate'=>$tardate,'suid'=>$suid,'dep_name'=>$this->dep_name]);
            }else{
                redirect('Menu/main');
            }

    }

    
    public function UpdateEditMomData(){

    $tbl_id = $this->input->post('id');
    $tardate = $this->input->post('tardate');
    $user_id = $this->input->post('user_id');
    //  presentation Start
     $presentation = $_POST['presentation'];
     $presentationdata = '';
     foreach($presentation as $prs){
         $presentationdata .=$prs.',';
     }
     $presentationdata = rtrim($presentationdata, ',');
     //  presentation End

    //  identify_school_state Start
     $ischool_state = $_POST['identify_school_state'];
     $ischoolstate = '';
     foreach($ischool_state as $state){
        $ischoolstate .=$state.',';
     }
     $ischoolstate = rtrim($ischoolstate, ',');
    //  identify_school_state End

    //  identify_school_district Start
     $ischool_district = $_POST['identify_school_district'];
     $ischooldistrict = '';
     foreach($ischool_district as $district){
        $ischooldistrict .=$district.',';
     }
     $ischooldistrict = rtrim($ischooldistrict, ',');
    //  identify_school_district End

     //  no_of_school Start
     $ino_of_school = $_POST['no_of_school'];
     $ischoolcnt = '';
     foreach($ino_of_school as $school){
        $ischoolcnt .=$school.',';
     }
     $ischoolcnt = rtrim($ischoolcnt, ',');
    //  no_of_school End

    $client_int_type_project = $this->input->post('client_int_type_project');
    if($client_int_type_project == ''){$client_int_type_project = '';}

     $data = array(
         'ccstatus' => $this->input->post('ccstatus'),
         'action_id' => $this->input->post('action_id'),
         'user_id' => $this->input->post('user_id'),
         'init_cmpid' => $this->input->post('init_cmpid'),
         'tid' => $this->input->post('tid'),
         'actontaken' => $this->input->post('actontaken'),
         'meetingdonewinitiator' => $this->input->post('meetingdonewinitiator'),
         'presentation' => $presentationdata,
         'project_intervention_select' => $this->input->post('project_intervention_select'),
         'project_intervention' => $this->input->post('project_intervention'),
         'client_has_adopted_select' => $this->input->post('client_has_adopted_select'),
         'client_has_adopted' => $this->input->post('client_has_adopted'),
         'approving_autorities' => $this->input->post('approving_autorities'),
         'budget_for_cfyear' => $this->input->post('budget_for_cfyear'),
         'fund_sanstion_limit' => $this->input->post('fund_sanstion_limit'),
         'other_specific_remarks' => $this->input->post('other_specific_remarks'),
         'submit_proposal' => $this->input->post('submit_proposal'),
         'proposal_no_of_school' => $this->input->post('proposal_no_of_school'),
         'proposal_of_budget' => $this->input->post('proposal_of_budget'),
         'proposal_of_location' => $this->input->post('proposal_of_location'),
         'identify_school' => $this->input->post('identify_school'),
         'identify_school_state' => $ischoolstate,
         'identify_school_district' =>$ischooldistrict,
         'no_of_school' => $ischoolcnt,
         'permission_letter' => $this->input->post('permission_letter'),
         'permission_letter_rech' => $this->input->post('permission_letter_rech'),
         'Letter_organization_name' => $this->input->post('Letter_organization_name'),
         'Letter_organization_designation' => $this->input->post('Letter_organization_designation'),
         'Letter_organization_location' => $this->input->post('Letter_organization_location'),
         'client_int_school_visit' => $this->input->post('client_int_school_visit'),
         'client_int_type_project' => $client_int_type_project,
         'client_int_school_date' => $this->input->post('client_int_school_date'),
         'client_int_school_state' => $this->input->post('client_int_school_state'),
         'client_int_school_district' => $this->input->post('client_int_school_district'),
         'client_int_no_of_school' => $this->input->post('client_int_no_of_school'),
         'intervention_cm_pst_sh' => $this->input->post('intervention_cm_pst_sh'),
         'rpmmom' => $this->input->post('rpmmom'),
         'partner' => $this->input->post('partner'),
     );

    // Pass data to model to insert into the database
    $this->db->insert('mom_data', $data);
    $insert_id = $this->db->insert_id();
    $query =  $this->db->query("UPDATE `mom_data` SET `edit_cnt`='$insert_id' WHERE id = $tbl_id");

    redirect('Management/PendingTeamMomData/'.$user_id.'/'.$tardate);

    }


// MOM END HERE


// Special Restrication on Task Planner in Admin
public function SpecialRestrictionOnTaskPlanner(){

    $user                   =   $this->session->userdata('user');
    $data['user']           =   $user;
    $uid                    =   $user['user_id'];
    $uyid                   =   $user['type_id'];
    $this->load->model('Menu_model');
    $dt                     =   $this->Menu_model->get_utype($uyid);
    $dep_name               =   $dt[0]->name;

    if(!empty($this->user)){

            $this->load->view('Functions/SpecialRestrictionOnTaskPlanner',['uid'=>$this->uid,'user'=>$this->user,'dep_name'          =>  $dep_name]);
            // $this->load->view($this->dep_name.'/SpecialRestrictionOnTaskPlanner',['uid'=>$this->uid,'user'=>$this->user]);
    }else{
        redirect('Menu/main');
    }

}
public function AddTaskPlannerRestrication(){

    $action_id          = $_POST['action']; 
    $user_type          = $_POST['user_type']; 
    $user_ids           = $_POST['user_id'];

    $company_status     = $_POST['company_status']; 
    $partner_type       = $_POST['partner_type']; 
    $category           = $_POST['category']; 
   

    $sdate              = $_POST['sdate'];
    $edate              = $_POST['edate'];
    $status             = $_POST['status'];
    $new_category       = $_POST['new_category_filter_select'];



    // Start combine action_id 
    if($action_id[0] !=='all'){
        $action_ids = implode(',', $action_id);
        $action_ids = rtrim($action_ids, ",");
        $action_ids  = rtrim($action_ids, ",");
        // $action_ids = '';
        // foreach($action_id as $adata){
        //     $action_ids .=$adata.',';
        // }
        // $action_ids = rtrim($action_ids, ',');

    }else{
        $action_ids =  $action_id[0];
        $action_ids  = rtrim($action_ids, ",");
    }
     // End combine action_id 

    // Start combine user_type 
    if($user_type[0] !=='all'){
        $user_types = '';
        $user_types = implode(',', $user_type);
        $user_types = rtrim($user_types, ",");
        // foreach($user_type as $auser_type){
        //     $user_types .=$auser_type.',';
        // }
        // $user_types = rtrim($user_types, ',');
    }else{
        $user_types =  $user_type[0];
    }
    // End combine user_type 

    // Start combine company_status 
    if($company_status[0] !=='all'){
        $company_statuss = '';
        $company_statuss = implode(',', $company_status);
        $company_statuss = rtrim($company_statuss, ",");
        // foreach($company_status as $acompany_status){
        //     $company_statuss .=$acompany_status.',';
        // }
        // $company_statuss = rtrim($company_statuss, ',');
    }else{
        $company_statuss =  $company_status[0];
    }
    // End combine company_status 

    // Start combine partner_types 
    if($partner_type[0] !=='all'){
        $partner_types = '';
        $partner_types = implode(',', $partner_type);
        $partner_types = rtrim($partner_types, ",");

        // foreach($partner_type as $apartner_type){
        //     $partner_types .=$apartner_type.',';
        // }
        // $partner_types = rtrim($partner_types, ',');

    }else{
        $partner_types =  $partner_type[0];
    }
    // End combine partner_types 

     // Start combine categorys 
     if($category[0] !=='all'){
        $categorys = '';
        $categorys = implode(',', $category);
        $categorys = rtrim($categorys, ",");
        // foreach($category as $acategory){
        //     $categorys .=$acategory.',';
        // }
        // $categorys = rtrim($categorys, ',');

    }else{
        $categorys =  $category[0];
    }
    // End combine categorys 

    // Start combine user_id 
        $user_id = '';
        foreach($user_ids as $u_id){
            $user_id .=$u_id.',';
        }
        $user_id = rtrim($user_id, ',');
    // end combine user_id 


    if(!isset($user_ids)){
        $user_id = '';
    }
    if(!isset($company_status)){
        $company_statuss = '';
    }
    if(!isset($partner_type)){
        $partner_types = '';
    }
    if(!isset($category)){
        $categorys = '';
    }


    $this->Management_model->AddTaskPlannerRestricationInTable($action_ids,$user_types,$sdate,$edate,$status,$company_statuss,$partner_types,$categorys,$user_id,$this->uid,$new_category);

    $this->session->set_flashdata('success_message', 'Restrication Add Successfully !');

    redirect('Management/SpecialRestrictionOnTaskPlanner');
}
public function ChangeStatusofRestrication(){
    $res_id             = $_POST['res_id'];
    $active_diactive    = $_POST['active_diactive'];
    $start_date         = $_POST['start_date'];
    $end_date           = $_POST['end_date'];

    $this->Management_model->ChangeTaskPlannerRestricationStatus($res_id,$active_diactive,$start_date,$end_date);
    $this->session->set_flashdata('success_message', 'Restrication Update Successfully !');

    redirect('Management/SpecialRestrictionOnTaskPlanner');
}



public function SpecialRestricationonDelete($id){
   
    $this->Management_model->DeleteSpecialRestrication($id);
    $this->session->set_flashdata('success_message', 'Restrication Delete Successfully !');

    redirect('Management/SpecialRestrictionOnTaskPlanner');
}



public function getAllActiveUserInDepartment(){

    $cur_user       = $this->session->userdata('user');
    $data['user']   = $cur_user;
    $cur_userid     = $cur_user['user_id'];
    $userTypeid     = $cur_user['type_id'];
   
    $user_type_id   = $this->input->post('user_type_id');
    $user_type_ids  = implode(", ", $user_type_id);
    
    if($userTypeid ==1){
        $filter  = "AND type_id IN ($user_type_ids) and sadmin_id = '$cur_userid'";
    }else if($userTypeid ==2){
        $filter  = "AND type_id IN ($user_type_ids) and admin_id = '$cur_userid'";
    }elseif($userTypeid == 3){
        $filter  = "AND type_id IN ($user_type_ids) and user_id = '$cur_userid'";
    }elseif($userTypeid == 4){
        $filter  = "AND type_id IN ($user_type_ids) and pst_co = '$cur_userid'";
    }elseif($userTypeid == 13){
        $filter  = "AND type_id IN ($user_type_ids) and aadmin = '$cur_userid'";
    }elseif($userTypeid ==15){
        $filter  = "AND type_id IN ($user_type_ids) and sales_co = '$cur_userid'";
    }elseif($userTypeid ==19){
        $filter  = "AND type_id IN ($user_type_ids) and ash_nae_co = '$cur_userid'";
    }elseif($userTypeid ==20){
        $filter  = "AND type_id IN ($user_type_ids) and ash_w_co = '$cur_userid'";
    }elseif($userTypeid ==21){
        $filter  = "AND type_id IN ($user_type_ids) and ash_s_co = '$cur_userid'";
    }elseif($userTypeid ==22){
        $filter  = "AND type_id IN ($user_type_ids) and rm_east_co = '$cur_userid'";
    }elseif($userTypeid ==23){
        $filter  = "AND type_id IN ($user_type_ids) and rm_north_co = '$cur_userid'";
    }elseif($userTypeid ==24){
        $filter  = "AND type_id IN ($user_type_ids) and acm_co = '$cur_userid'";
    }else{
         $filter  = "AND user_id = '$cur_userid'";
    }

     $query = $this->db->query("SELECT * FROM `user_details` WHERE status = 'active' $filter order by user_id");
       
       $selectusers = $query->result();
         $data = '';
          foreach($selectusers as $user){
            $data .= '<option value='.$user->user_id.'>'.$user->name.'</option>';
            }
             echo $data;
 }
    // New Daymanagement changes <======== START =======>
    public function CheckingDayManagement_New_1(){
        $cdate = date("Y-m-d");
        $sdate = new DateTime($cdate);
        $sdate->modify('-1 day');
        $previousDate = $sdate->format('Y-m-d');

        ini_set('max_execution_time', 1800); // 300 seconds
        ini_set('memory_limit', '512M');

        $user = $this->session->userdata('user');
        $data['user'] = $user;
        $uid = $user['user_id'];
        $uyid =  $user['type_id'];
        $this->load->model('Menu_model');
        $dt=$this->Menu_model->get_utype($uyid);
    // echo "hii2";die;
    // $tdate='2024-07-18';

        $userList = $this->Menu_model->get_userForTask($uid,$uyid);
        // var_dump($userList);die;
        $cdate = '2024-10-08';
        // $dayData = $this->Management_model->CheckingDayManage_New($this->uid,$cdate,$user='');
        // echo $this->db->last_query();die;
        // $yesterdayData = $this->Management_model->CheckingYesterdyDayManage_New($this->uid,$previousDate);
        $dayData  =  $yesterdayData = [];
        // var_dump($yesterdayData);die;
        $RequestApprovals = $this->Management_model->RequestApprovals($this->uid,$cdate);
        $ApprovedRequests = $this->Management_model->ApprovedRequests($this->uid,$cdate);

        $user = $this->session->userdata('user');
        $data['user'] = $user;
        $uid = $user['user_id'];
        $typeID =  (int) $user['type_id'];
        $currentHour = (int) (new DateTime())->format('H:mm');
        // var_dump($RequestApprovals);die;
        if(!empty($this->user)){
            if($currentHour >= 11 && $typeID != 2) {
                if (sizeof($ApprovedRequests) > 0) {
                    
                    $this->load->view($this->dep_name.'/CheckingDayManagement_New1',['uid'=>$this->uid,'user'=>$this->user,'typeID'=>$typeID,'dayData'=>$dayData,'cdate'=>$cdate,'previousDate'=>$previousDate,'userList'=>$userList]);
                }else{
                    $this->load->view($this->dep_name.'/RequestForDayCheckApproval',['uid'=>$this->uid,'user'=>$this->user,'cdate'=>$cdate,'RequestApprovals'=>$RequestApprovals]);
                }
            }else{
                $this->load->view($this->dep_name.'/CheckingDayManagement_New1',['uid'=>$this->uid,'user'=>$this->user,'dayData'=>$dayData,'yesterdayData'=>$yesterdayData,'cdate'=>$cdate,'previousDate'=>$previousDate,'userList'=>$userList]);
            }
        }else{
            redirect('Menu/main');
        }
    }
    
    
      public function CheckingDayManagement_New($taskid = NULL){
        $cdate = date("Y-m-d");
        $sdate = new DateTime($cdate);
        $sdate->modify('-1 day');
        $previousDate = $sdate->format('Y-m-d');

        $yesterday = new DateTime('yesterday');

        if ($yesterday->format('l') === 'Sunday') {
            // Get the day of the month
            $dayOfMonth = $yesterday->format('j');
            
            // Find the Sundays in the current month
            $firstDayOfMonth = (clone $yesterday)->modify('first day of this month');
            $sundaysInMonth = [];
        
            // Start from the first Sunday of the month
            $currentSunday = (clone $firstDayOfMonth)->modify('Sunday');
        
            while ($currentSunday->format('n') === $yesterday->format('n')) {
                $sundaysInMonth[] = $currentSunday->format('j');
                $currentSunday->modify('+1 week');
            }
        
            // Ensure there are enough Sundays in the month before accessing indices
            if (isset($sundaysInMonth[1], $sundaysInMonth[3]) && in_array($dayOfMonth, [$sundaysInMonth[1], $sundaysInMonth[3]])) {
                $date = new DateTime();
                $date->modify('-3 day');
            } else {
                $date = new DateTime();
                $date->modify('-2 day');
            }
            $previousDate = $date->format('Y-m-d');
        
        } else {
            $holy_date = new DateTime();
            $holy_date->modify('-1 day');
            $holy_tdate = $holy_date->format('Y-m-d');
        
            $checkforHoliday = checkforHoliday($holy_tdate);
            if (!empty($checkforHoliday)) {
                $previousDate = date("Y-m-d", strtotime($checkforHoliday[0]->holiday_date . " -1 day"));
        
                // Check if the previous day is also a holiday
                $tcheckforHoliday = checkforHoliday($previousDate);
                if (!empty($tcheckforHoliday)) {
                    $previousDate = date("Y-m-d", strtotime($tcheckforHoliday[0]->holiday_date . " -1 day"));
                }
            } else {
                $previousDate = $holy_tdate;
            }
        }


        $dayData = $this->Management_model->CheckingDayManage_New($this->uid,$cdate);
        
        $yesterdayData = $this->Management_model->CheckingYesterdyDayManage_New($this->uid,$previousDate);
       
        $RequestApprovals = $this->Management_model->RequestApprovals($this->uid,$cdate);
        $ApprovedRequests = $this->Management_model->ApprovedRequests($this->uid,$cdate);

        $user = $this->session->userdata('user');
        $data['user'] = $user;
        $uid = $user['user_id'];
        $typeID =  (int) $user['type_id'];
        $currentHour = (int) (new DateTime())->format('H:mm');
        
        if(!is_null($taskid)){
            $taskData = $this->Menu_model->getTBLSCTaskByID($taskid);
            if (!empty($taskData) && isset($taskData[0]->initiated_datetime)) {
                $taskDatainit = $taskData[0]->initiated_datetime;
                if ($taskDatainit == '0000-00-00 00:00:00' || empty($taskDatainit)) {
                    $initiateddttime = date("Y-m-d H:i:s");
                    $this->db->set('initiated_datetime', $initiateddttime);
                    $this->db->where('id', $taskid);
                    $this->db->update('sc_task_events');
                    $appointment_datetime = $taskData[0]->appointment_datetime;
                    $appointment_datetime = $date = date("Y-m-d", strtotime($appointment_datetime));
                }
            }
        }

        if(!empty($this->user)){
            if($currentHour >= 11 && $typeID != 2) {
                if (sizeof($ApprovedRequests) > 0) {
                    $this->load->view($this->dep_name.'/CheckingDayManagement_New',['uid'=>$this->uid,'user'=>$this->user,'typeID'=>$typeID,'dayData'=>$dayData,'cdate'=>$cdate,'previousDate'=>$previousDate,'ctaskid'=>$taskid,'yesterdayData'=>$yesterdayData]);
                }else{
                    $this->load->view($this->dep_name.'/RequestForDayCheckApproval',['uid'=>$this->uid,'user'=>$this->user,'cdate'=>$cdate,'RequestApprovals'=>$RequestApprovals]);
                }
            }else{
                $this->load->view($this->dep_name.'/CheckingDayManagement_New',['uid'=>$this->uid,'user'=>$this->user,'typeID'=>$typeID,'dayData'=>$dayData,'yesterdayData'=>$yesterdayData,'cdate'=>$cdate,'previousDate'=>$previousDate,'ctaskid'=>$taskid]);
            }
        }else{
            redirect('Menu/main');
        }
    }

    public function checkdayswithStarNew(){
        
        // var_dump($_POST);die;
        $cdate = $_POST['cdate'];
        $sdate = new DateTime($cdate);
        $sdate->modify('-1 day');
        $previousDate = $sdate->format('Y-m-d');

        $periods = $_POST['period'];
        $userId = $_POST['userId'];
        $rating = $_POST['rating'];
        $question = $_POST['question'];

        if($periods == 'Yesterday Evening' || $periods == 'Yesterday Task'){

            $date = $previousDate;

        }else{
            $date = $cdate;
        }
        $data = [
            'date' => $date,
            'user_id' => $userId,
            'periods' => $periods,
            'question' => $question,
            'star'=>$rating,
            // 'previousDate'=>$previousDate,
            'feedback_by'=>$this->uid,
        ];

        $result = $this->Management_model->AddStarRatingNew($data);
        // var_dump($result);die;
        echo json_encode($result);

        // $this->session->set_flashdata('success_message', 'Star Rating Added Successfully');
        // redirect('Management/CheckingDayManagement');
    }

    public function updateStarRemark() {
        // var_dump($_POST);die;
        $remark = $_POST['remark'];
        $starID = $_POST['starID'];

        $result = $this->Management_model->updateStarRemark($remark,$starID);
    }
      public function RequestForDayManagementApproval(){

        $request = $this->input->post('remark');

        $dayData = $this->Management_model->RequestForDayManagementApproval_Model($this->uid,$request);

        

    }
    public function ApproveDayCheckRequest(){


        $getRequests = $this->Management_model->getRequests($this->uid);

        $user = $this->session->userdata('user');
        $data['user'] = $user;
        $uid = $user['user_id'];
        $typeID =  $user['type_id'];
        // var_dump($getRequests);die;
        if(!empty($this->user)){

            $this->load->view($this->dep_name.'/ApproveDayCheckRequest',['uid'=>$this->uid,'user'=>$this->user,'getRequests'=>$getRequests]);

        }else{

            redirect('Menu/main');
        }
    }

    public function ApproveRequest() {

        $id = $_POST['id'];
        $action = $_POST['action'];

        if ($action == 'approve') {
            $action = 'Approved';
        }
        if ($action == 'reject') {
            $action = 'Rejected';
        }

        $result = $this->Management_model->ApproveRequest($id,$action,$this->uid);
    }
public function DayManagementReport_1()
    {
        if (isset($_POST['startDate']) && isset($_POST['endDate'])) {

            $sdate = $_POST['startDate'];
            $edate = $_POST['endDate'];
        } else {

            $sdate = date('Y-m-d');
            $edate = date('Y-m-d');
        }

        if (isset($_POST['user'])) {

            $selected_user = array_filter($_POST['user'], function ($value) {
                return $value !== 'select_all';
            });
        } else {

            $selected_user = [];
        }

        // var_dump($_POST);die;
        // $selected_user = [];

        $user = $this->session->userdata('user');
        $data['user'] = $user;
        $uid = $user['user_id'];
        $userTypeid = $user['type_id'];
        $this->load->model('Graph_Model');
        $dt = $this->Graph_Model->get_utype($userTypeid);
        $dep_name = $dt[0]->name;

        $getUsers = $this->Management_model->getUsers($uid,$userTypeid);

        $cdate = "2024-10-01";
       
        $getReportbyUser = $this->Management_model->getReportbyUser($selected_user,$sdate,$edate);
        $dayData         = $this->Management_model->CheckingDayManage_New($this->uid,$cdate);
        array_merge($getReportbyUser,$dayData);

        
        // var_dump($getReportbyUser);die;

        $groupedByDate = [];

        // Transform the data structure
        foreach ($getReportbyUser as $record) {
            $date = $record->date;
            $period = $record->periods;
        
            // Check if date key exists, if not, initialize it
            if (!isset($groupedByDate[$date])) {
                $groupedByDate[$date] = [];
            }
        
            // Check if period key exists under the current date, if not, initialize it
            if (!isset($groupedByDate[$date][$period])) {
                $groupedByDate[$date][$period] = [];
            }
        
            // Append the record to the appropriate period under the correct date
            $groupedByDate[$date][$period][] = $record;
        }
        
        if (!empty($user)) {
            // $this->load->view('include/header');
            // $this->load->view($dep_name . '/nav', ['uid' => $uid, 'user' => $user]);
            $this->load->view($this->dep_name.'/DayManagementReport',['uid'=>$this->uid,'user'=>$this->user,'sdate'=>$sdate,'edate'=>$edate,'users'=>$getUsers,'selected_user'=>$selected_user,'getReportbyUser'=>$groupedByDate]);
        } else {
            redirect('Menu/main');
        }
    }

  
    public function DayManagementReport()
    {

        if (isset($_POST['startDate']) && isset($_POST['endDate'])) {

            $sdate = $_POST['startDate'];
            $edate = $_POST['endDate'];
        } else {

            $sdate = date('Y-m-d');
            $edate = date('Y-m-d');
        }

        if (isset($_POST['user'])) {

            $selected_user = array_filter($_POST['user'], function ($value) {
                return $value !== 'select_all';
            });
        } else {

            $selected_user = [];
        }

        // var_dump($_POST);die;
        // $selected_user = [];

        $user = $this->session->userdata('user');
        $data['user'] = $user;
        $uid = $user['user_id'];
        $userTypeid = $user['type_id'];
        $this->load->model('Graph_Model');
        $dt = $this->Graph_Model->get_utype($userTypeid);
        $dep_name = $dt[0]->name;

        $getUsers = $this->Management_model->getUsers($uid,$userTypeid);

        $getReportbyUser = $this->Management_model->getReportbyUser($selected_user,$sdate,$edate);
        
        // var_dump($getReportbyUser);die;

        $groupedByDate = [];

        // Transform the data structure
        foreach ($getReportbyUser as $record) {
            $date = $record->date;
            $period = $record->periods;
        
            // Check if date key exists, if not, initialize it
            if (!isset($groupedByDate[$date])) {
                $groupedByDate[$date] = [];
            }
        
            // Check if period key exists under the current date, if not, initialize it
            if (!isset($groupedByDate[$date][$period])) {
                $groupedByDate[$date][$period] = [];
            }
        
            // Append the record to the appropriate period under the correct date
            $groupedByDate[$date][$period][] = $record;
        }
        
        if (!empty($user)) {
            // $this->load->view('include/header');
            // $this->load->view($dep_name . '/nav', ['uid' => $uid, 'user' => $user]);
            $this->load->view($this->dep_name.'/DayManagementReport',['uid'=>$this->uid,'user'=>$this->user,'sdate'=>$sdate,'edate'=>$edate,'users'=>$getUsers,'selected_user'=>$selected_user,'getReportbyUser'=>$groupedByDate]);
        } else {
            redirect('Menu/main');
        }
    }

    // New Daymanagement changes <======== END =======>


    // MOM Start Here







public function Change_RP_To_No_RP(){

    $mom_id= $this->input->post('mom_id');
    $tid= $this->input->post('tid');
    
    $this->Menu_model->change_norp($tid);

    $return = $this->Management_model->UpdateMOM_DataTo_NORP($mom_id,$this->uid,$tid);
    echo $return;

}

// MOM Check BY RESPECTIVE MANAGER : 
public function MomDataCheck($mon_tid,$ce_id){

    $cdate = date("Y-m-d");
    $momdata = $this->Menu_model->getRequestMOMBYID($mon_tid);
    $companyContactData = $this->Menu_model->getCompanyContactDetailsByTask($ce_id);


    $taskid = $ce_id;
    $taskData = $this->Menu_model->getTBLTaskByID($taskid);
    $taskDatainit = $taskData[0]->initiateddt;
    if (is_null($taskDatainit)) {
        $initiateddttime = date("Y-m-d H:i:s");
        $this->db->set('initiateddt', $initiateddttime);
        $this->db->where('id', $taskid);
        $this->db->update('tblcallevents');
    }

    if(!empty($this->user)){
            $this->load->view($this->dep_name.'/MomDataCheck',['uid'=>$this->uid,'user'=>$this->user,'cdate'=>$cdate,'momdata'=>$momdata,'t_id'=>$mon_tid,'ce_id'=>$ce_id,'companyContactData'=>$companyContactData]);
        }else{
            redirect('Menu/main');
        }
}
public function MomDataCheckonLivePage($mon_tid,$ce_id){

    $cdate = date("Y-m-d");
    $momdata = $this->Menu_model->getRequestMOMBYID($mon_tid);
    $companyContactData = $this->Menu_model->getCompanyContactDetailsByTask($ce_id);

    $taskid = $ce_id;
    $momChecktaskData = $this->Menu_model->getTBLTaskByID($taskid);
    if(!empty($this->user)){
            $this->load->view('Functions/MomDataCheckonLivePage',['uid'=>$this->uid,'user'=>$this->user,'cdate'=>$cdate,'momdata'=>$momdata,'t_id'=>$mon_tid,'ce_id'=>$ce_id,'companyContactData'=>$companyContactData,'dep_name'=>$this->dep_name,'momChecktaskData'=>$momChecktaskData ]);
        }else{
            redirect('Menu/main');
        }
}

public function MomDataCheckInAdmin($mon_tid,$ce_id){

    $cdate = date("Y-m-d");
    $momdata = $this->Menu_model->getRequestMOMBYID($mon_tid);

    $taskid = $ce_id;
    $taskData = $this->Menu_model->getTBLTaskByID($taskid);
    $taskDatainit = $taskData[0]->initiateddt;
    if (is_null($taskDatainit)) {
        $initiateddttime = date("Y-m-d H:i:s");
        $this->db->set('initiateddt', $initiateddttime);
        $this->db->where('id', $taskid);
        $this->db->update('tblcallevents');
    }


    if(!empty($this->user)){
            // $this->load->view($this->dep_name.'/MomDataCheckInAdmin',['uid'=>$this->uid,'user'=>$this->user,'cdate'=>$cdate,'momdata'=>$momdata,'t_id'=>$mon_tid,'ce_id'=>$ce_id]);

            $this->load->view('Functions/MomDataCheckInAdmin',['uid'=>$this->uid,'user'=>$this->user,'cdate'=>$cdate,'momdata'=>$momdata,'t_id'=>$mon_tid,'ce_id'=>$ce_id,'dep_name'=>$this->dep_name]);
        }else{
            redirect('Menu/main');
        }
}




public function MomDataCheckInAnnualReviewTime($mon_tid,$ce_id){

    $cdate = date("Y-m-d");
    $momdata = $this->Menu_model->getRequestMOMBYID($mon_tid);

    $taskid = $ce_id;
    $taskData = $this->Menu_model->getTBLTaskByID($taskid);
    $taskDatainit = $taskData[0]->initiateddt;
    if (is_null($taskDatainit)) {
        $initiateddttime = date("Y-m-d H:i:s");
        $this->db->set('initiateddt', $initiateddttime);
        $this->db->where('id', $taskid);
        $this->db->update('tblcallevents');
    }


    if(!empty($this->user)){
            $this->load->view($this->dep_name.'/MomDataCheckInAnnualReviewTime',['uid'=>$this->uid,'user'=>$this->user,'cdate'=>$cdate,'momdata'=>$momdata,'t_id'=>$mon_tid,'ce_id'=>$ce_id]);
        }else{
            redirect('Menu/main');
        }
}

// Update After MOM Check 

public function MomApprovedByUserAdminAfterCheck(){

    $id             = $_POST['mom_id'];
    $ntid           = $_POST['ntid'];
    $right_remarks  = $_POST['right_remarks'];
    $mom_yes_no     = $_POST['mom_yes_no'];

    $finalRemarks   = $right_remarks.' - '.$mom_yes_no;
   
    $approvedtDate  = date("y-m-d H:i:s");
    $approved_status= 'Approved';
    $approvedreamrk = $finalRemarks;

    $momdata        = $this->Management_model->getMomByID($id);
    $init_cmpid     = $momdata[0]->init_cmpid;
    $tid_calleve    = $momdata[0]->tid;
    $ccstatus       = $momdata[0]->ccstatus;
    $mom_user_id    = $momdata[0]->user_id;

    $fwd_date       = '';
    // $fwd_date = date("Y-m-d h:i:s");

    $nextWorkingpDate   = $this->Menu_model->findNextSpecialDate(date("Y-m-d"));
    $fwd_date           = $nextWorkingpDate." 10:00:00";  


    $actiontype_id  = 1;
    $init_id        = $init_cmpid;
    $nextaction     = 1;
    $ass_user_id    = $this->uid;
    $purpose_id     = 1;
    $autotask       = 1;
    $auto_plan      = 1;

    $slsctcuruser       = $this->Menu_model->get_userbyid($this->uid);
    $slsctcurusertype   = $slsctcuruser[0]->type_id;

    $initData = $this->Menu_model->get_cmpbyinid($init_id);
    $initData_id = $initData[0]->id;
    $initDatacstatus = $initData[0]->cstatus;
    $initData_mainbd = $initData[0]->mainbd;

    $utype                   = $this->Menu_model->get_userbyid($initData_mainbd);
    $user_type_id            = $utype[0]->type_id;
    $user_ash_nae_co_id      = $utype[0]->ash_nae_co;
    $user_ash_w_co_id        = $utype[0]->ash_w_co;
    $user_ash_s_co_id        = $utype[0]->ash_s_co;
    $user_rm_east_co_id      = $utype[0]->rm_east_co;
    $user_rm_north_co_id     = $utype[0]->rm_north_co;
    $user_acm_co_id          = $utype[0]->acm_co;
    $user_admin_id           = $utype[0]->admin_id;
    $user_zone_id            = $utype[0]->zone_id; 

    if($user_admin_id == 45){
        // 3 - North Zone | 8 East Zone
        if($user_zone_id == 3){

            if($initDatacstatus == 3){
                $this->db->query("UPDATE init_call SET rm_north_co_id = '$user_rm_north_co_id' WHERE id = '$initData_id'");
                $data11 = [
                    'init_id'     => $initData_id,
                    'column_name' => 'rm_north_co_id',
                    'status_id'   => $initDatacstatus,
                    'task_id'     => $ntid,
                    'user_id'     => $user_rm_north_co_id
                ];
                $this->db->insert('assign_user_by_date',$data11);
            }else if($initDatacstatus == 6){
                $this->db->query("UPDATE init_call SET ash_nae_co_id = '$user_ash_nae_co_id' WHERE id = '$initData_id'");
                $data12 = [
                    'init_id'     => $initData_id,
                    'column_name' => 'ash_nae_co_id',
                    'status_id'   => $initDatacstatus,
                    'task_id'     => $ntid,
                    'user_id'     => $user_ash_nae_co_id
                ];
                $this->db->insert('assign_user_by_date',$data12);
            }

        }else if($user_zone_id  == 8){

            if($initDatacstatus == 3){
                $this->db->query("UPDATE init_call SET rm_east_co_id = '$user_rm_east_co_id' WHERE id = '$initData_id'");
                $data11 = [
                    'init_id'     => $initData_id,
                    'column_name' => 'rm_east_co_id',
                    'status_id'   => $initDatacstatus,
                    'task_id'     => $ntid,
                    'user_id'     => $user_rm_east_co_id
                ];
                $this->db->insert('assign_user_by_date',$data11);
            }else if($initDatacstatus == 6){
                $this->db->query("UPDATE init_call SET ash_nae_co_id = '$user_ash_nae_co_id' WHERE id = '$initData_id'");
                $data12 = [
                    'init_id'     => $initData_id,
                    'column_name' => 'ash_nae_co_id',
                    'status_id'   => $initDatacstatus,
                    'task_id'     => $ntid,
                    'user_id'     => $user_ash_nae_co_id
                ];
                $this->db->insert('assign_user_by_date',$data12);
            }
        }

    }else if($user_admin_id == 2){

        //  4 -  South Zone | 5 - West Zone
        if($user_zone_id == 4){
            if($initDatacstatus == 6){
                $this->db->query("UPDATE init_call SET ash_s_co_id = '$user_ash_s_co_id' WHERE id = '$initData_id'");
                $data11 = [
                    'init_id'     => $initData_id,
                    'column_name' => 'ash_s_co_id',
                    'status_id'   => $initDatacstatus,
                    'task_id'     => $ntid,
                    'user_id'     => $user_ash_s_co_id
                ];
                $this->db->insert('assign_user_by_date',$data11);
            }
            
        }else if($user_zone_id  == 5){
            if($initDatacstatus == 6){
                $this->db->query("UPDATE init_call SET ash_w_co_id = '$user_ash_w_co_id' WHERE id = '$initData_id'");
                $data11 = [
                    'init_id'     => $initData_id,
                    'column_name' => 'ash_w_co_id',
                    'status_id'   => $initDatacstatus,
                    'task_id'     => $ntid,
                    'user_id'     => $user_ash_w_co_id
                ];
                $this->db->insert('assign_user_by_date',$data11);
            }
        }
    }

    $this->Management_model->MomApprovedByUserAdminInsert($approved_status,$id,$approvedreamrk,$approvedtDate,$this->uid,$finalRemarks);

    $task_remarks = "Task Create After MOM Approved";

    // if($slsctcurusertype == 13){
        $ftaskudetail   = $this->Menu_model->get_userbyid($ass_user_id);
        $ftype_id       = $ftaskudetail[0]->type_id;
        
        if(!in_array($ftype_id,[15])){
            $insert_id = $this->Management_model->CreateTask($fwd_date,$actiontype_id,$init_id,$nextaction,$ass_user_id,$purpose_id,$autotask,$auto_plan,$ccstatus,$task_remarks,$ntid);
            $this->Management_model->CreateTaskForAutoAssign($ass_user_id,$ass_user_id,$ccstatus,$init_id,$insert_id,$actiontype_id,$id,$task_remarks);
        }
        


    // }
 
    $cudetail       = $this->Menu_model->get_userbyid($mom_user_id);
    $get_pst_co     = $cudetail[0]->pst_co;
    $get_utype_id   = $cudetail[0]->type_id;

    if($get_utype_id == 13){
        $clm_aadmin = $cudetail[0]->user_id;
    }else{
        $clm_aadmin = $cudetail[0]->aadmin;
    }
    
    if(!empty($get_pst_co)){$this->Management_model->AssignPSTAfterMomApproved($init_cmpid,$get_pst_co);}
    if(!empty($clm_aadmin)){$this->Management_model->AssignCLMAfterMomApproved($init_cmpid,$clm_aadmin); }
    if(!empty($user_acm_co_id)){ $this->Management_model->Assign_ACM_After_MomApproved($init_cmpid,$user_acm_co_id);}


    if($slsctcurusertype == 4){
        $insert_id = $this->Management_model->CreateTask($fwd_date,$actiontype_id,$init_id,$nextaction,$get_pst_co,$purpose_id,$autotask,$auto_plan,$ccstatus,$task_remarks,$ntid);

        $this->Management_model->CreateTaskForAutoAssign($ass_user_id,$get_pst_co,$ccstatus,$init_id,$insert_id,$actiontype_id,$id,$task_remarks);
     }



    // if(!empty($user_rm_east_co_id) && $user_rm_east_co_id !=0){
    //     $get_rm_east_co_id = $user_rm_east_co_id;
    //     $insert_id = $this->Management_model->CreateTask($fwd_date,$actiontype_id,$init_id,$nextaction,$get_rm_east_co_id,$purpose_id,$autotask,$auto_plan,$ccstatus,$task_remarks,$ntid);

    //     $this->Management_model->CreateTaskForAutoAssign($ass_user_id,$get_rm_east_co_id,$ccstatus,$init_id,$insert_id,$actiontype_id,$id,$task_remarks);
    //  }


    $remark = 'Mom Approved - RP FOUND';
    $cur_date = date("Y-m-d H:i:s");
    $dataup = array(
        'remarks' => $remark,
        'nextCFID' => $tid_calleve,
        'updateddate' => $cur_date,
        'actontaken' => 'yes',
        'purpose_achieved' => 'yes',
        'updation_data_type' => 'update',
        'mtype' => 'RP',
        'nstatus_id' =>$initDatacstatus
    );
    
    $this->db->where('id', $ntid);
    $this->db->update('tblcallevents', $dataup);

    $this->session->set_flashdata('success_message', 'MOM Approved Successfully !');
    redirect('Menu/Dashboard');

}

public function MomRejectByUserAdminAfterCheck(){

    $rejectId           = $_POST['reject'];
    $mom_otid           = $_POST['mom_otid'];
    $ntid               = $_POST['ntid'];
    $rejectreamrk       = $_POST['rejectreamrk'];
    $rejectDate         = date("y-m-d H:i:s");
    $approved_status    = 'Reject';
    $momdata            = $this->Management_model->MomRejectByUserAdminInsert($approved_status,$rejectId,$rejectreamrk,$rejectDate,$this->uid);

    $remark = 'Reject RP For Reupdate';
    $cur_date = date("Y-m-d H:i:s");
    $dataup = array(
        'remarks' => $remark,
        'nextCFID' => $mom_otid,
        'updateddate' => $cur_date,
        'actontaken' => 'yes',
        'purpose_achieved' => 'no',
        'updation_data_type' => 'update'
    );
    
    $this->db->where('id', $ntid);
    $this->db->update('tblcallevents', $dataup);

    $this->session->set_flashdata('success_message', 'MOM Reject Successfully !');
    redirect('Menu/Dashboard');
  
}

public function Change_RP_To_No_RP_ACHECK(){

    $mom_id= $this->input->post('mom_id');
    $tid= $this->input->post('tid');
    $ntid= $this->input->post('ntid');

    $query    = $this->db->query("SELECT * FROM `tblcallevents` WHERE id = '$tid'");
    $momDatas =  $query->result();
    $meet_id  =  $momDatas[0]->aftertask;

    //  $dataup = array(
    //     'mtype' => 'NO RP',
    //     'comments' => 'Converted RP TO NO After Check',
    //     'comment_by' => $this->uid
    // );

    // $this->db->where('id', $meet_id);
    // $this->db->update('tblcallevents', $dataup);
    
    $this->Menu_model->change_norp($tid);

    $return = $this->Management_model->UpdateMOMAterCheck_DataTo_NORP($mom_id,$this->uid,$tid,$ntid);
    echo $return;

}


public function Change_RP_To_No_RP_ACHECKNew(){

    $mom_id = $this->input->post('mom_id');
    $tid    = $this->input->post('tid');
    $ntid   = $this->input->post('ntid');
    $changestatus  = $this->input->post('changestatus');

    $query    = $this->db->query("SELECT * FROM `tblcallevents` WHERE id = '$tid'");
    $momDatas =  $query->result();
    $meet_id  =  $momDatas[0]->aftertask;
   
    // $dataup = array(
    //     'mtype' => 'NO RP',
    //     'comments' => 'Converted RP TO NO After Check',
    //     'comment_by' => $this->uid
    // );

    // $this->db->where('id', $meet_id);
    // $this->db->update('tblcallevents', $dataup);

    $this->Menu_model->change_norp($tid);
    $return = $this->Management_model->UpdateMOMAterCheck_DataTo_NORP($mom_id,$this->uid,$tid,$ntid);
    $this->session->set_flashdata('success_message', 'MOM Convert RP to No RP Meeting Successfully.');
    redirect('Menu/Dashboard');
}

public function SendReminder(){
  
    $rtype       = $this->input->post('reminder_type');
    $rmessage    = $this->input->post('reminder_message');

    $this->Management_model->StoreReminder($rtype,$rmessage,$this->uid);

    $this->session->set_flashdata('success_message', 'Reminder Send Successfully !');
    redirect('Menu/Dashboard');
}


public function HolidayList(){

    // echo "hii";die;
    $user = $this->session->userdata('user');
    $data['user'] = $user;
    $uid = $user['user_id'];
    $typeID =  (int) $user['type_id'];

    if (isset($_POST['holiday_name'])) {
        
        $holiday_name = $this->input->post('holiday_name');
        $holiday_date = $this->input->post('holiday_date');
        $data['uid'] = $uid;

        $addHoliday = $this->Management_model->addHoliday($holiday_name,$holiday_date,$uid);

        if($addHoliday){
            
            $this->session->set_flashdata('success_msg', 'Holiday added successfully..!!');
            redirect('management/HolidayList');

        }else{

            $this->session->set_flashdata('error_msg', 'Unable to add Holiday..!!');
            redirect('management/HolidayList');
        }
    }


    $getHolidayList =  $this->Management_model->getHolidayList();

    $this->load->view($this->dep_name.'/HolidayList',['uid'=>$this->uid,'user'=>$this->user,'getHolidayList'=>$getHolidayList]);

}


  public function CheckingDayManagement_New1(){

        $cdate = $this->input->post('date');
        $userid = $this->input->post('user_id');
        $period = $this->input->post('period');
        // var_dump($period);die;
        if($period == 'today'){

            // var_dump($period);die;
            // $cdate = '2024-10-08';
            $dayData = $this->Management_model->CheckingDayManage_New($this->uid,$cdate,$userid);
            echo json_encode($dayData);

        }elseif ($period == 'yesterday') {
            
            // var_dump($period);die;
            $cdate = date("Y-m-d");
            // $cdate = '2024-10-08';
            $sdate = new DateTime($cdate);
            $sdate->modify('-1 day');
            $previousDate = $sdate->format('Y-m-d');

            $dayData = $this->Management_model->CheckingYesterdyDayManage_New($this->uid,$previousDate,$userid);
            // var_dump($dayData);die;
            echo json_encode($dayData);
        }

        
    }


 public function submitDayCheck(){//
        // var_dump($_POST);die;
        $data = array(
            'record_date' => date('Y-m-d'),  // e.g., '2024-10-08'
            'userID' => $this->input->post('userID'),            // e.g., '123'
            'planned_day_start' => $this->input->post('planned_day_start'), // e.g., 'Work From Field'
            'actual_day_start' => $this->input->post('actual_day_start'),   // e.g., 'Work From Office'
            // 'Did_the_day_started_as_planned' => $this->input->post('Did_the_day_started_as_planned'),
            'day_start_time' => $this->input->post('day_start_time'),  // e.g., '2024-10-08 10:32:12'
            // 'Did_the_day_started_on_right_time' => $this->input->post('Did_the_day_started_on_right_time'),
            // 'Did_the_task_started_on_right_time' => $this->input->post('Did_the_task_started_on_right_time'),
            // 'Is_Day_start_image_good' => $this->input->post('Is_Day_start_image_good'),
            // 'Day_Start_Location_as_per_Plan' => $this->input->post('Day_Start_Location_as_per_Plan'),
            'autoTaskStartTime' => $this->input->post('autoTaskStartTime'), // e.g., '17:00:00'
            'autoTaskEndTime' => $this->input->post('autoTaskEndTime'),   // e.g., '18:00:00'
            // 'Auto_task_time_entered_correctly' => $this->input->post('Auto_task_time_entered_correctly'),
            'planner_created_at' => $this->input->post('planner_created_at'), // e.g., '2024-10-08 14:42:51'
            'planner_request_remarks' => $this->input->post('planner_request_remarks'),
            'planner_approvel_status' => $this->input->post('planner_approvel_status'),
            'planner_approvel_time' => $this->input->post('planner_approvel_time'), // e.g., '2024-10-08 11:42:51'
            'approver_Name' => $this->input->post('approver_Name'),
            // 'Planner_requested_correctly' => $this->input->post('Planner_requested_correctly'),
            'end_time' => $this->input->post('end_time'), // e.g., '2024-10-07 10:31:45'
            // 'The_Day_Ended_at_a_good_time' => $this->input->post('The_Day_Ended_at_a_good_time'),
            // 'Did_the_yesterday_day_close_image_was_right' => $this->input->post('Did_the_yesterday_day_close_image_was_right'),
            'yesterday_autotaskstartTime' => $this->input->post('yesterday_autotaskstartTime'), // e.g., '17:00:00'
            'yesterday_autotaskendTime' => $this->input->post('yesterday_autotaskendTime'),   // e.g., '18:00:00'
            // 'Autotask_added_on_correct_time' => $this->input->post('Autotask_added_on_correct_time'),
            'total_timeTakeFor_planner' => $this->input->post('total_timeTakeFor_planner'), // e.g., '07:40:10'
            // 'Time_taken_to_plan_the_planner' => $this->input->post('Time_taken_to_plan_the_planner'),
            'dayCloseRemark' => $this->input->post('dayCloseRemark'),
            'dayCloseApproveStatus' => $this->input->post('dayCloseApproveStatus'),
            'dayCloseApproveRemark' => $this->input->post('dayCloseApproveRemark'),
            // 'Was_day_ended_on_good_time' => $this->input->post('Was_day_ended_on_good_time'),
            // 'Day_Start_Location_as_per_Plan_yesterday' => $this->input->post('Day_Start_Location_as_per_Plan_yesterday'),
            'specialRequest_created_at' => $this->input->post('specialRequest_created_at'),
            'specialRequest_end_at' => $this->input->post('specialRequest_end_at'),
            'specialRequest_request_remarks' => $this->input->post('specialRequest_request_remarks'),
            'specialRequest_approvel_status' => $this->input->post('specialRequest_approvel_status'),
            // 'Planner_requested_correctly_yesterday' => $this->input->post('Planner_requested_correctly_yesterday')
        );


        $this->db->insert('daily_planner', $data);

        $user = $this->session->userdata('user');
        $data['user'] = $user;
        $uid = $user['user_id'];

        $fields_to_insert = [
            'Planner_requested_correctly',
            'Auto_task_time_entered_correctly',
            'Day_Start_Location_as_per_Plan',
            'Is_Day_start_image_good',
            'Did_the_task_started_on_right_time',
            'Did_the_day_started_on_right_time',
            'Did_the_day_started_as_planned',
            'Did_the_yesterday_day_close_image_was_right',
            'Autotask_added_on_correct_time',
            'Time_taken_to_plan_the_planner',
            'Was_day_ended_on_good_time',
            'Day_Start_Location_as_per_Plan_yesterday',
            'Planner_requested_correctly_yesterday'
        ];

        $this->db->trans_start();

    // Loop through the fields and insert the question name and rating
        foreach ($fields_to_insert as $field) {
            // Check if the field exists in the POST request
            if (!empty($_POST[$field])) {
                // Prepare data for insertion
                $data = array(
                    'question' => $field,
                    'user_id' => $_POST['userID'],
                    'date' => date('Y-m-d'),
                    'feedback_by' => $uid,
                    'star' => $_POST[$field],  // Field value from POST request
                );
                // Insert into the 'ratings' table
                $this->db->insert('star_rating', $data);
            }
            // echo $this->db->last_query(); die;
            // echo ";";
        }

        // Complete the transaction
        $this->db->trans_complete();


// die;
        redirect('management/CheckingDayManagement_New');
    }






}
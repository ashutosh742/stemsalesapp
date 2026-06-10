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
class Reports extends CI_Controller {
    public function __construct() {
        parent::__construct();
        // Load models, libraries, helpers, etc.
        $this->load->model('Menu_model');
        $this->load->model('Report_model');
        $this->load->helper('common_helper');
        $this->load->helper('samestatustilldate_helper');
        $this->load->helper('taskPlanner_helper');
    }
    public function phpinfoprint() {
        phpinfo();
        exit;
    }
    public function Dashboard() {
        $user = $this->session->userdata('user');
        if (empty($user)) {
            redirect('Menu/main');
        }
        $uid  = $user['user_id'];
        $uyid = $user['type_id'];
        $dt = $this->Menu_model->get_utype($uyid);
        $dep_name = isset($dt[0]->name) ? $dt[0]->name : 'default';
        $cdate = date("Y-m-d");
        $this->load->view($dep_name.'/Reports/ReportIndex', [
            'uid'       => $uid,
            'user'      => $user,
            'dep_name'  =>$dep_name,
            'cdate'     =>$cdate
        ]);
    }
 
  public function BDDetails() {
        $user = $this->session->userdata('user');
        if (empty($user)) {
            redirect('Menu/main');
        }
        $uid        = $user['user_id'];
        $uyid       = $user['type_id'];
        $dt         = $this->Menu_model->get_utype($uyid);
        $dep_name   = isset($dt[0]->name) ? $dt[0]->name : 'default';
        $cdate      = date("Y-m-d");
        $admin_id_filter    = $this->input->Post('admin_id_filter');
        $rm_filter          = $this->input->Post('rm_filter');
    if(isset($_POST['admin_id_filter'])){
        $sdate                  = $this->input->Post('sdate');
        $edate                  = $this->input->Post('edate');
        if(isset($admin_id_filter)){
            if($admin_id_filter == 'all'){
                $admin_id_filter = 'all';
            }else{
                $uid = $admin_id_filter;
            }
            if(isset($rm_filter)){
                if($rm_filter =='all'){
                    $rm_filter = 'all';
                }else{
                    $uid = $rm_filter;
                }
            }
        }
    }else{
        $sdate              = date('Y-m-d');
        $edate              = date('Y-m-d');
        $rm_filter          = 'all';
        $admin_id_filter    = $uid;
    }
        if($rm_filter !=='all'){
           $uid = $rm_filter;
        }else{
          $uid = $admin_id_filter;
        }
        $mdata      = $this->Report_model->GetAllUserByReportingManager($uid,$admin_id_filter,$rm_filter,$sdate);
      
        $this->load->view('Reports/BDDetails', [
            'uid'       => $uid,
            'dep_name'  =>$dt,
            'ctype_id'  =>$uyid,
            'user'      => $user,
            'dep_name'  =>$dep_name,
            'cdate'     =>$cdate,
            'mdata'     =>$mdata,
            'sdate'     =>$sdate,
            'admin_id_filter'=>$admin_id_filter,
            'rm_filter'=>$rm_filter,
            
        ]);
    }



  public function TodaysBDDetails($mtypes,$target_userid,$date) {
        $user = $this->session->userdata('user');
        if (empty($user)) {
            redirect('Menu/main');
        }
        $uid        = $user['user_id'];
        $uyid       = $user['type_id'];
        $dt         = $this->Menu_model->get_utype($uyid);
        $dep_name   = isset($dt[0]->name) ? $dt[0]->name : 'default';
        $cdate      = date("Y-m-d");
        $mdata      = $this->Report_model->GetTodaysAllUserByReportingManager($mtypes,$target_userid,$date);
       
        $this->load->view('Reports/TodaysBDDetails', [
            'uid'       => $uid,
            'dep_name'  =>$dt,
            'ctype_id'  =>$uyid,
            'user'      => $user,
            'dep_name'  =>$dep_name,
            'cdate'     =>$cdate,
            'mdata'     =>$mdata,
            'sdate'     =>$date
            
        ]);
    }
    //  New Meeting Details Page Developed By Deepak
public function MeetingDetailNew(){
    $user           = $this->session->userdata('user');
    if(empty($user) || empty($user['user_id'])){ redirect('login'); return; }
    // session guard added 28 May 2026 to fix HTTP 500 on null session
    $data['user']   = $user;
    $uid            = $user['user_id'];
    $uyid           =  $user['type_id'];
    $this->load->model('Menu_model');
    $dt = $this->Menu_model->get_utype($uyid);
    $dep_name = (is_array($dt) && !empty($dt) && isset($dt[0]->name)) ? $dt[0]->name : 'home';
    // if(isset($_POST['sdate'])){
    // $sdate = $_POST['sdate'];
    // $edate = $_POST['edate'];
    // }else{
    //     $sdate = date('Y-m-d');
    //     $edate = date('Y-m-d');
    // }
    $admin_id_filter    = $this->input->Post('admin_id_filter');
    $rm_filter          = $this->input->Post('rm_filter');
    if(isset($_POST['sdate'])){
        $sdate                  = $this->input->Post('sdate');
        $edate                  = $this->input->Post('edate');
        if(isset($admin_id_filter)){
            if($admin_id_filter == 'all'){
                $admin_id_filter = 'all';
            }else{
                $uid = $admin_id_filter;
            }
            if(isset($rm_filter)){
                if($rm_filter =='all'){
                    $rm_filter = 'all';
                }else{
                    $uid = $rm_filter;
                }
            }
        }
    }else{
        $sdate              = date('Y-m-d');
        $edate              = date('Y-m-d');
        $rm_filter          = 'all';
        $admin_id_filter    = $uid;
    }
        if($rm_filter !=='all'){
           $uid = $rm_filter;
        }else{
          $uid = $admin_id_filter;
        }
    $totalMeetingsUserByDatas               = $this->Menu_model->TotalMeetingDetailsByRolesUid($uid,$sdate,$edate,$admin_id_filter,$rm_filter);
    $meetingsUserByDatas                    = $this->Menu_model->MeetingDetailsByRolesUid($uid,$sdate,$edate,$admin_id_filter,$rm_filter);
    $totalMeetingsUserByDatasTTime          = $this->Menu_model->TotalsMeetingUpdateDetailByTime($uid,$sdate,$edate,$admin_id_filter,$rm_filter);
    $totalMeetingsUserByDatasCategory       = $this->Menu_model->TotalsMeetingUpdateDetailByCategory($uid,$sdate,$edate,$admin_id_filter,$rm_filter);
    $totalMeetingsUserByDatasStatus         = $this->Menu_model->TotalsMeetingUpdateDetailByStatus($uid,$sdate,$edate,$admin_id_filter,$rm_filter);
    $totalOtherFunnelMeetingsUser           = $this->Menu_model->TotalsOtherFunnelMeetingUpdateDetail($uid,$sdate,$edate,$admin_id_filter,$rm_filter);
    $totalNewCompanyMeetingsUser            = $this->Menu_model->TotalsNewCompanyMeetings($uid,$sdate,$edate,$admin_id_filter,$rm_filter);
    $toalNoRPMeetDatas                      = $this->Menu_model->NoRPMeetingDetailsByRolesUid($uid,$sdate,$edate,$admin_id_filter,$rm_filter);
    
    if(!empty($user)){
        $this->load->view('Reports/MeetingDetailNew',['uid'=>$uid,'user'=>$user,'sdate'=>$sdate,'edate'=>$edate,'sd'=>$sdate,'ed'=>$edate,'meetingsUserByDatas'=>$meetingsUserByDatas,'totalMeetingsUserByDatas'=>$totalMeetingsUserByDatas,'totalMeetingsUserByDatasTTime'=>$totalMeetingsUserByDatasTTime,'totalMeetingsUserByDatasCategory'=>$totalMeetingsUserByDatasCategory,'totalMeetingsUserByDatasStatus'=>$totalMeetingsUserByDatasStatus,'totalNewCompanyMeetingsUser'=>$totalNewCompanyMeetingsUser,'admin_id_filter'=>$admin_id_filter,'rm_filter'=>$rm_filter,'dep_name'=>$dep_name,'totalOtherFunnelMeetingsUser'=>$totalOtherFunnelMeetingsUser,'toalNoRPMeetDatas'=>$toalNoRPMeetDatas]);
    }else{
        redirect('Menu/main');
    }
}
public function MeetingsDatas($mtypes,$target_uid,$sdate,$edate,$userwise =NULL){
    $user           = $this->session->userdata('user');
    $data['user']   = $user;
    $uid            = $user['user_id'];
    $uyid           =  $user['type_id'];
    $this->load->model('Menu_model');
    $dt=$this->Menu_model->get_utype($uyid);
    $dep_name = (is_array($dt) && !empty($dt) && isset($dt[0]->name)) ? $dt[0]->name : 'home';
    // if(empty($user)){
    //     redirect('Menu/main');
    // }
    $dep_name   = isset($dt[0]->name) ? $dt[0]->name : 'Guest';

    if(isset($_POST['sdate'])){
    $sdate = $_POST['sdate'];
    $edate = $_POST['edate'];
    }
    if(is_null($userwise)){
        $userwise = '';
    }
    $totalMeetingsUserByDatas   = $this->Menu_model->TotalMeetingDetailsDatas($target_uid,$sdate,$edate,$mtypes,$uid,$userwise);
    $this->load->view('Reports/MeetingsDatas',['uid'=>$uid,'user'=>$user,'sdate'=>$sdate,'edate'=>$edate,'sd'=>$sdate,'ed'=>$edate,'totalMeetingsUserByDatas'=>$totalMeetingsUserByDatas,'mtypes'=>$mtypes,'comming_user_types'=>$uyid,'mtypes'=>$mtypes,'target_uid'=>$target_uid,'dep_name'=>$dep_name]);
    
}
public function MeetingsDatasNewBargMeetingNORP($mtypes,$target_uid,$sdate,$edate,$userwise =NULL){
    $user           = $this->session->userdata('user');
    if(empty($user) || empty($user['user_id'])){ redirect('login'); return; }
    // session guard added 28 May 2026 to fix HTTP 500 on null session
    $data['user']   = $user;
    $uid            = $user['user_id'];
    $uyid           =  $user['type_id'];
    $this->load->model('Menu_model');
    $dt=$this->Menu_model->get_utype($uyid);
    $dep_name = (is_array($dt) && !empty($dt) && isset($dt[0]->name)) ? $dt[0]->name : 'home';
    if(isset($_POST['sdate'])){
    $sdate = $_POST['sdate'];
    $edate = $_POST['edate'];
    }
    if(is_null($userwise)){
        $userwise = '';
    }
    $totalMeetingsUserByDatas   = $this->Menu_model->TotalMeetingDetailsDatas_NewBarg($target_uid,$sdate,$edate,$mtypes,$uid,$userwise);
    if(!empty($user)){
        $this->load->view('Reports/MeetingsDatasNewBargMeetingNORP',['uid'=>$uid,'user'=>$user,'sdate'=>$sdate,'edate'=>$edate,'sd'=>$sdate,'ed'=>$edate,'totalMeetingsUserByDatas'=>$totalMeetingsUserByDatas,'mtypes'=>$mtypes,'comming_user_types'=>$uyid,'mtypes'=>$mtypes,'target_uid'=>$target_uid,'dep_name'=>$dep_name]);
    }else{
        redirect('Menu/main');
    }
}
public function ViewMeetingDetails($taskid){
    $user           = $this->session->userdata('user');
    if(empty($user) || empty($user['user_id'])){ redirect('login'); return; }
    // session guard added 28 May 2026 to fix HTTP 500 on null session
    $data['user']   = $user;
    $uid            = $user['user_id'];
    $uyid           =  $user['type_id'];
    $this->load->model('Menu_model');
    $dt=$this->Menu_model->get_utype($uyid);
    $dep_name = (is_array($dt) && !empty($dt) && isset($dt[0]->name)) ? $dt[0]->name : 'home';
    $totalMeetingsUserByDatas   = $this->Menu_model->GetMeetingDetailsByTaskID($taskid);
    
        $this->load->view('Reports/ViewMeetingDetails',['uid'=>$uid,'user'=>$user,'totalMeetingsUserByDatas'=>$totalMeetingsUserByDatas,'tmeeting_taskid'=>$taskid,'dep_name'=>$dep_name]);
//    if(!empty($user)){ }else{
//         redirect('Menu/main');
//     }
}
public function ReportNoRPConvertAfterChecking(){
    $user               = $this->session->userdata('user');
    if(empty($user) || empty($user['user_id'])){ redirect('login'); return; }
    // session guard added 28 May 2026 to fix HTTP 500 on null session
    $data['user']       = $user;
    $uid                = $user['user_id'];
    $uyid               =  $user['type_id'];
    $this->load->model('Menu_model');
    $this->load->library('session');
    $dt                 = $this->Menu_model->get_utype($uyid);
    $dep_name = (is_array($dt) && !empty($dt) && isset($dt[0]->name)) ? $dt[0]->name : 'home';
    $no_rp_task_id      = $this->input->post('no_rp_task_id');
    $no_rp_remarks      = $this->input->post('no_rp_remarks');
    $norp_date          = date('Y-m-d H:i:s');
    // Rediections Data
    $mtypes             = $this->input->post('mtypes');
    $target_uid         = $this->input->post('target_uid');
    $sdate              = $this->input->post('sdate');
    $edate              = $this->input->post('edate');
    $udetail            = $this->Menu_model->get_userbyid($uid); 
    $current_username   = $udetail[0]->name;
    if($uid == 2 || $uid == 45 || $uid == 100000){
        $meetingTaskData    = $this->Menu_model->get_tbldata($no_rp_task_id);
        if(sizeof($meetingTaskData) > 0){
            $init_cid_id        = $meetingTaskData[0]->cid_id;
            $tbl_status_id      = $meetingTaskData[0]->status_id;
            $tbl_nstatus_id     = $meetingTaskData[0]->nstatus_id;
            $remarks            = $no_rp_remarks." - This Meetings RP to No RP conversion by $current_username";
           
            $query  = $this->db->query("UPDATE tblcallevents 
                            SET mtype = 'NO RP',
                                norp_by = '$uid',
                                norp_date = '$norp_date',
                                remarks = '$remarks'
                            WHERE id = '$no_rp_task_id'");
            $query = $this->db->query("update init_call set apst = null, bpst = null, acm_co_id = null where id='$init_cid_id'");
            // $query = $this->db->query("update init_call set cstatus =' $tbl_status_id' where id='$init_cid_id'");
            $meetingMomData      = $this->Menu_model->GetTBLMomTaskByTaskId($no_rp_task_id);
            if(sizeof($meetingMomData) > 0){
                $writeMomTaskId  = $meetingMomData[0]->id;
                $momDatas        = $this->Menu_model->GetMomDataByTaskId($writeMomTaskId);
                if(sizeof($momDatas) > 0){
                $momDatas_id = $momDatas[0]->id;
                   $query = $this->db->query("update mom_data set approved_status = 'NO RP', approved_by = '$uid', approved_date = '$norp_date',reject_remarks= '$remarks' where id='$momDatas_id'");
                }
            }
            $this->session->set_flashdata('success_message', '* No RP Converted Successfully.');
        }else{
            $this->session->set_flashdata('error_message', '* Task Not Exists Please Try Again.');
        }
    }else{
        $this->session->set_flashdata('error_message', '* You are Not Authorized To Convert Meetings in No RP');
    }
   
    redirect('Reports/MeetingsDatas/'.$mtypes.'/'.$target_uid.'/'.$sdate.'/'.$edate);
}
public function ReportNoRPToRPConvertAfterChecking(){
    $user               = $this->session->userdata('user');
    if(empty($user) || empty($user['user_id'])){ redirect('login'); return; }
    // session guard added 28 May 2026 to fix HTTP 500 on null session
    $data['user']       = $user;
    $uid                = $user['user_id'];
    $uyid               =  $user['type_id'];
    $this->load->model('Menu_model');
    $this->load->library('session');
    $dt                 = $this->Menu_model->get_utype($uyid);
    $dep_name = (is_array($dt) && !empty($dt) && isset($dt[0]->name)) ? $dt[0]->name : 'home';
    $no_rp_task_id      = $this->input->post('no_rp_task_id');
    $no_rp_remarks      = $this->input->post('no_rp_remarks');
    $norp_date          = date('Y-m-d H:i:s');
    // Rediections Data
    $mtypes             = $this->input->post('mtypes');
    $target_uid         = $this->input->post('target_uid');
    $sdate              = $this->input->post('sdate');
    $edate              = $this->input->post('edate');
    $udetail            = $this->Menu_model->get_userbyid($uid); 
    $current_username   = $udetail[0]->name;
    if($uid == 2 || $uid == 45 || $uid == 100000){
        $meetingTaskData    = $this->Menu_model->get_tbldata($no_rp_task_id);
        if(sizeof($meetingTaskData) > 0){
            $tbl_task_user_id   = $meetingTaskData[0]->user_id;
            $init_cid_id        = $meetingTaskData[0]->cid_id;
            $tbl_status_id      = $meetingTaskData[0]->status_id;
            $tbl_nstatus_id     = $meetingTaskData[0]->nstatus_id;
            $taskUserMapDatas   = $this->Menu_model->get_userbyid($tbl_task_user_id);
            $aadmin             = $taskUserMapDatas[0]->aadmin;
            $pst_co             = $taskUserMapDatas[0]->pst_co;
            $acm_co             = $taskUserMapDatas[0]->acm_co;
            $remarks            = "Meeting Close With RP";
           
            $query  = $this->db->query("UPDATE tblcallevents 
                            SET mtype = 'RP',
                                norp_by = '0',
                                norp_date = '',
                                remarks = '$remarks'
                            WHERE id = '$no_rp_task_id'");
            if(in_array($tbl_status_id, [3,6,9,12,13])){
                $query = $this->db->query("update init_call set apst = '$pst_co', acm_co_id ='$acm_co', clm_id = '$aadmin' where id='$init_cid_id'");
            }   
            // $query = $this->db->query("update init_call set cstatus =' $tbl_status_id' where id='$init_cid_id'");
            $meetingMomData      = $this->Menu_model->GetTBLMomTaskByTaskId($no_rp_task_id);
            if(sizeof($meetingMomData) > 0){
                $writeMomTaskId  = $meetingMomData[0]->id;
                $momDatas        = $this->Menu_model->GetMomDataByTaskId($writeMomTaskId);
                if(sizeof($momDatas) > 0){
                $momDatas_id = $momDatas[0]->id;
                   $query = $this->db->query("update mom_data set approved_status = 'RP', approved_by = '$uid', approved_date = '$norp_date',reject_remarks= '$remarks' where id='$momDatas_id'");
                }
            }
            $this->session->set_flashdata('success_message', '* No RP TO RP Converted Successfully.');
        }else{
            $this->session->set_flashdata('error_message', '* Task Not Exists Please Try Again.');
        }
    }else{
        $this->session->set_flashdata('error_message', '* You are Not Authorized To Convert Meetings in No RP');
    }
   
    redirect('Reports/MeetingsDatas/'.$mtypes.'/'.$target_uid.'/'.$sdate.'/'.$edate);
}
public function GetTeamTaskOnSelfOrOtherFunnelTask(){
    $user           = $this->session->userdata('user');
    if(empty($user) || empty($user['user_id'])){ redirect('login'); return; }
    // session guard added 28 May 2026 to fix HTTP 500 on null session
    $data['user']   = $user;
    $uid            = $user['user_id'];
    $uyid           =  $user['type_id'];
    $this->load->model('Menu_model');
    $dt=$this->Menu_model->get_utype($uyid);
    $dep_name = (is_array($dt) && !empty($dt) && isset($dt[0]->name)) ? $dt[0]->name : 'home';
    $admin_id_filter    = $this->input->Post('admin_id_filter');
    $rm_filter          = $this->input->Post('rm_filter');
    $task_action_id     = $this->input->Post('task_action');
    if(isset($_POST['sdate'])){
        $sdate                  = $this->input->Post('sdate');
        $edate                  = $this->input->Post('edate');
        if(isset($admin_id_filter)){
            if($admin_id_filter == 'all'){
                $admin_id_filter = 'all';
            }else{
                $uid = $admin_id_filter;
            }
            if(isset($rm_filter)){
                if($rm_filter =='all'){
                    $rm_filter = 'all';
                }else{
                    $uid = $rm_filter;
                }
            }
        }
    }else{
        $sdate              = date('Y-m-d');
        $edate              = date('Y-m-d');
        $rm_filter          = 'all';
        $task_action_id     = 'all';
        $admin_id_filter    = $uid;
    }
        if($rm_filter !=='all'){
           $uid = $rm_filter;
        }else{
          $uid = $admin_id_filter;
        }
        if($task_action_id == 'all'){
            $taskActionDatas =  'All';
        }else{
            $taskActionDatas =  $this->Menu_model->getTaskAction($task_action_id)[0]->name;
        }
  
   $taskDatadatas   =  $this->Report_model->GetTeamWisePlanedTaskType($uid,$sdate,$edate,$task_action_id,$admin_id_filter,$rm_filter);
        
    $groupedData = [];
    foreach ($taskDatadatas as $item) {
        $planner_user_id = $item->planner_user_id;
        if (!isset($groupedData[$planner_user_id])) {
            $groupedData[$planner_user_id] = [];
        }
        $groupedData[$planner_user_id][] = $item;
    }
    $getTotalTasks   = $this->Menu_model->GetTotalClusterTaskOnSelfOrOtherFunnelTask($uid,$sdate,$edate,$task_action_id,$admin_id_filter,$rm_filter);
    $getTotalTasksFrequncy   = $this->Menu_model->GetTotalClusterTaskOnSelfOrOtherFunnelTaskFrequency($uid,$sdate,$edate,$task_action_id,$admin_id_filter,$rm_filter);
    $getTotalTasksStatus   = $this->Menu_model->GetTotalClusterTaskOnSelfOrOtherFunnelTaskBYStatus($uid,$sdate,$edate,$task_action_id,$admin_id_filter,$rm_filter);
    
    $getTasks   = $this->Menu_model->GetClusterTaskOnSelfOrOtherFunnelTask($uid,$sdate,$edate,$task_action_id,$admin_id_filter,$rm_filter);
    $bdconversions   = $this->Menu_model->GetTeamConversionBetweenDatesDatas($uid,$sdate,$edate,$task_action_id,$admin_id_filter,$rm_filter);
   
    $task_type_Datas = $this->Report_model->GetActiveTaskData(1);
    if(!empty($user)){
        $this->load->view('Reports/GetTeamTaskOnSelfOrOtherFunnelTask',['uid'=>$uid,'user'=>$user,'getTasks'=>$getTasks,'user_tupe_id'=>$uyid,'sdate'=>$sdate,'edate'=>$edate,'task_action_id'=>$task_action_id,'taskActionDatas'=>$taskActionDatas,'task_action_id'=> $task_action_id,'admin_id_filter'=>$admin_id_filter,'rm_filter'=>$rm_filter,'dep_name'=>$dep_name,'groupedData'=>$groupedData,'tasktypeDatas'=>$task_type_Datas,'getTotalTasks'=>$getTotalTasks,'getTotalTasksFrequncy'=>$getTotalTasksFrequncy,'getTotalTasksStatus'=>$getTotalTasksStatus,'bdconversions'=>$bdconversions]);
    }else{
        redirect('Menu/main');
    }
}
public function TeamConversionBetweenDatesData(){
    $user           = $this->session->userdata('user');
    if(empty($user) || empty($user['user_id'])){ redirect('login'); return; }
    // session guard added 28 May 2026 to fix HTTP 500 on null session
    $data['user']   = $user;
    $uid            = $user['user_id'];
    $uyid           =  $user['type_id'];
    $this->load->model('Menu_model');
    $dt=$this->Menu_model->get_utype($uyid);
    $dep_name = (is_array($dt) && !empty($dt) && isset($dt[0]->name)) ? $dt[0]->name : 'home';
    $admin_id_filter    = $this->input->Post('admin_id_filter');
    $rm_filter          = $this->input->Post('rm_filter');
    $task_action_id     = $this->input->Post('task_action');
    if(isset($_POST['sdate'])){
        $sdate                  = $this->input->Post('sdate');
        $edate                  = $this->input->Post('edate');
        if(isset($admin_id_filter)){
            if($admin_id_filter == 'all'){
                $admin_id_filter = 'all';
            }else{
                $uid = $admin_id_filter;
            }
            if(isset($rm_filter)){
                if($rm_filter =='all'){
                    $rm_filter = 'all';
                }else{
                    $uid = $rm_filter;
                }
            }
        }
    }else{
        $sdate              = date('Y-m-d');
        $edate              = date('Y-m-d');
        $rm_filter          = 'all';
        $task_action_id     = 'all';
        $admin_id_filter    = $uid;
    }
        if($rm_filter !=='all'){
           $uid = $rm_filter;
        }else{
          $uid = $admin_id_filter;
        }
        if($task_action_id == 'all'){
            $taskActionDatas =  'All';
        }else{
            $taskActionDatas =  $this->Menu_model->getTaskAction($task_action_id)[0]->name;
        }
  
   $taskDatadatas   =  $this->Report_model->GetTeamWisePlanedTaskType($uid,$sdate,$edate,$task_action_id,$admin_id_filter,$rm_filter);
        
    $groupedData = [];
    foreach ($taskDatadatas as $item) {
        $planner_user_id = $item->planner_user_id;
        if (!isset($groupedData[$planner_user_id])) {
            $groupedData[$planner_user_id] = [];
        }
        $groupedData[$planner_user_id][] = $item;
    }
    $getTotalTasksStatus   = $this->Menu_model->GetTotalClusterTaskOnSelfOrOtherFunnelTaskBYStatus($uid,$sdate,$edate,$task_action_id,$admin_id_filter,$rm_filter);
    
    $bdconversions   = $this->Menu_model->GetTeamConversionBetweenDatesDatas($uid,$sdate,$edate,$task_action_id,$admin_id_filter,$rm_filter);
   
    $task_type_Datas = $this->Report_model->GetActiveTaskData(1);
    if(!empty($user)){
        $this->load->view('Reports/TeamConversionBetweenDatesData',['uid'=>$uid,'user'=>$user,'getTasks'=>$getTasks,'user_tupe_id'=>$uyid,'sdate'=>$sdate,'edate'=>$edate,'task_action_id'=>$task_action_id,'taskActionDatas'=>$taskActionDatas,'task_action_id'=> $task_action_id,'admin_id_filter'=>$admin_id_filter,'rm_filter'=>$rm_filter,'dep_name'=>$dep_name,'groupedData'=>$groupedData,'tasktypeDatas'=>$task_type_Datas,'getTotalTasks'=>$getTotalTasks,'getTotalTasksFrequncy'=>$getTotalTasksFrequncy,'getTotalTasksStatus'=>$getTotalTasksStatus,'bdconversions'=>$bdconversions]);
    }else{
        redirect('Menu/main');
    }
}
public function TeamPlannerReport(){
    $user           = $this->session->userdata('user');
    if(empty($user) || empty($user['user_id'])){ redirect('login'); return; }
    // session guard added 28 May 2026 to fix HTTP 500 on null session
    $data['user']   = $user;
    $uid            = $user['user_id'];
    $uyid           =  $user['type_id'];
    $this->load->model('Menu_model');
    $this->load->model('Report_model');
    $dt=$this->Menu_model->get_utype($uyid);
    $dep_name = (is_array($dt) && !empty($dt) && isset($dt[0]->name)) ? $dt[0]->name : 'home';
    $admin_id_filter    = $this->input->Post('admin_id_filter');
    $rm_filter          = $this->input->Post('rm_filter');
    $task_action_id     = $this->input->Post('task_action');
    if(isset($_POST['sdate'])){
        $sdate                  = $this->input->Post('sdate');
        $edate                  = $this->input->Post('edate');
        if(isset($admin_id_filter)){
            if($admin_id_filter == 'all'){
                $admin_id_filter = 'all';
            }else{
                $uid = $admin_id_filter;
            }
            if(isset($rm_filter)){
                if($rm_filter =='all'){
                    $rm_filter = 'all';
                }else{
                    $uid = $rm_filter;
                }
            }
        }
    }else{
        $sdate              = date('Y-m-d');
        $edate              = date('Y-m-d');
        $rm_filter          = 'all';
        $task_action_id     = 'all';
        $admin_id_filter    = $uid;
    }
        if($rm_filter !=='all'){
           $uid = $rm_filter;
        }else{
          $uid = $admin_id_filter;
        }
        if($task_action_id == 'all'){
            $taskActionDatas =  'All';
        }else{
            $taskActionDatas =  $this->Menu_model->getTaskAction($task_action_id)[0]->name;
        }
  
       
       
    $taskDatadatasDB   =  $this->Report_model->GetTeamWisePlanedTaskTypeUsingPlanner($uid,$sdate,$edate,$task_action_id,$admin_id_filter,$rm_filter);
    $plannerReportDatas   =  $this->Report_model->GetPlannerAndUserSummury($uid,$sdate,$edate,$admin_id_filter,$rm_filter);
    $plannerTasksDatas = $this->Report_model->GetUserPlannerReportBetweenDate($uid,$sdate,$edate,$admin_id_filter,$rm_filter);
    $totalUserDatas      = $this->Report_model->GetAllUserByReportingManager($uid,$admin_id_filter,$rm_filter,$sdate);
    // echo $this->db->last_query();
    //  dd($totalUserDatas);
    $taskDatadatas     = $taskDatadatasDB['totalTasksUserByDatas'];
        
    $groupedData = [];
    foreach ($taskDatadatas as $item) {
        $planner_user_id = $item->planner_user_id;
        if (!isset($groupedData[$planner_user_id])) {
            $groupedData[$planner_user_id] = [];
        }
        $groupedData[$planner_user_id][] = $item;
    }
    // dd($groupedData);
    $task_type_Datas = $this->Report_model->GetActiveTaskData(1);
    if(!empty($user)){
        $this->load->view('Reports/TeamPlannerReport',['uid'=>$uid,'user'=>$user,'user_tupe_id'=>$uyid,'sdate'=>$sdate,'edate'=>$edate,'task_action_id'=>$task_action_id,'taskActionDatas'=>$taskActionDatas,'task_action_id'=> $task_action_id,'admin_id_filter'=>$admin_id_filter,'rm_filter'=>$rm_filter,'dep_name'=>$dep_name,'groupedData'=>$groupedData,'tasktypeDatas'=>$task_type_Datas,'taskDatadatasDB'=>$taskDatadatasDB,'plannerReportDatas'=>$plannerReportDatas,'plannerTasksDatas'=>$plannerTasksDatas,'totalUserDatas'=>$totalUserDatas]);
    }else{
        redirect('Menu/main');
    }
}
public function UserRequestDetails($mtypes,$target_uid,$sdate,$edate,$task_action_id,$userwise =NULL){
    $user           = $this->session->userdata('user');
    if(empty($user) || empty($user['user_id'])){ redirect('login'); return; }
    // session guard added 28 May 2026 to fix HTTP 500 on null session
    $data['user']   = $user;
    $uid            = $user['user_id'];
    $uyid           =  $user['type_id'];
    $this->load->model('Menu_model');
    ini_set('max_execution_time', 18000);
    $dt=$this->Menu_model->get_utype($uyid);
    $dep_name = (is_array($dt) && !empty($dt) && isset($dt[0]->name)) ? $dt[0]->name : 'home';
    if(isset($_POST['sdate'])){
    $sdate = $_POST['sdate'];
    $edate = $_POST['edate'];
    }
    if(is_null($userwise)){
        $userwise = '';
    }
    if($task_action_id == 'all'){
            $taskActionDatas =  'All';
    }else{
        $taskActionDatas =  $this->Menu_model->getTaskAction($task_action_id)[0]->name;
    }
    $totalTasksUserByDatas   = $this->Report_model->GetUserRequestDetails($target_uid,$sdate,$edate,$mtypes,$uid,$task_action_id,$userwise);
    if(!empty($user)){
        $this->load->view('Reports/UserRequestDetails',['uid'=>$uid,'user'=>$user,'sdate'=>$sdate,'edate'=>$edate,'sd'=>$sdate,'ed'=>$edate,'totalTasksUserByDatas'=>$totalTasksUserByDatas,'mtypes'=>$mtypes,'comming_user_types'=>$uyid,'mtypes'=>$mtypes,'target_uid'=>$target_uid,'taskActionDatas'=>$taskActionDatas,'dep_name'=>$dep_name]);
    }else{
        redirect('Menu/main');
    }
}
public function PlannerReportDataBYDate($target_uid,$sdate,$edate,$mtypes,$userwise =NULL){
    $user           = $this->session->userdata('user');
    if(empty($user) || empty($user['user_id'])){ redirect('login'); return; }
    // session guard added 28 May 2026 to fix HTTP 500 on null session
    $data['user']   = $user;
    $uid            = $user['user_id'];
    $uyid           =  $user['type_id'];
    $this->load->model('Menu_model');
    ini_set('max_execution_time', 18000);
    $dt=$this->Menu_model->get_utype($uyid);
    $dep_name = (is_array($dt) && !empty($dt) && isset($dt[0]->name)) ? $dt[0]->name : 'home';
    if(isset($_POST['sdate'])){
    $sdate = $_POST['sdate'];
    $edate = $_POST['edate'];
    }
    if(is_null($userwise)){
        $userwise = '';
    }
    $totalTasksUserByDatas   = $this->Report_model->GetPlannerAndUserSummuryDatas($target_uid,$sdate,$edate,$mtypes,$userwise);
    
    // echo sizeof($totalTasksUserByDatas);
    // dd($totalTasksUserByDatas);
    if(!empty($user)){
        $this->load->view('Reports/PlannerReportDataBYDate',['uid'=>$uid,'user'=>$user,'sdate'=>$sdate,'edate'=>$edate,'sd'=>$sdate,'ed'=>$edate,'totalTasksUserByDatas'=>$totalTasksUserByDatas,'mtypes'=>$mtypes,'comming_user_types'=>$uyid,'mtypes'=>$mtypes,'target_uid'=>$target_uid,'dep_name'=>$dep_name]);
    }else{
        redirect('Menu/main');
    }
}
public function PlannerApprovedReportDataBYDate($target_uid,$sdate,$edate,$mtypes,$userwise =NULL){
    $user           = $this->session->userdata('user');
    if(empty($user) || empty($user['user_id'])){ redirect('login'); return; }
    // session guard added 28 May 2026 to fix HTTP 500 on null session
    $data['user']   = $user;
    $uid            = $user['user_id'];
    $uyid           =  $user['type_id'];
    $this->load->model('Menu_model');
    ini_set('max_execution_time', 18000);
    $dt=$this->Menu_model->get_utype($uyid);
    $dep_name = (is_array($dt) && !empty($dt) && isset($dt[0]->name)) ? $dt[0]->name : 'home';
    if(isset($_POST['sdate'])){
    $sdate = $_POST['sdate'];
    $edate = $_POST['edate'];
    }
    if(is_null($userwise)){
        $userwise = '';
    }
    $totalTasksUserByDatas   = $this->Report_model->GetPlannerApprovedAndUserSummuryDatas($target_uid,$sdate,$edate,$mtypes,$userwise);
    if(!empty($user)){
        $this->load->view('Reports/PlannerApprovedReportDataBYDate',['uid'=>$uid,'user'=>$user,'sdate'=>$sdate,'edate'=>$edate,'sd'=>$sdate,'ed'=>$edate,'totalTasksUserByDatas'=>$totalTasksUserByDatas,'mtypes'=>$mtypes,'comming_user_types'=>$uyid,'mtypes'=>$mtypes,'target_uid'=>$target_uid,'dep_name'=>$dep_name]);
    }else{
        redirect('Menu/main');
    }
}
public function GetTeamTaskOnSelfOrOtherFunnelTaskLists($mtypes,$target_uid,$sdate,$edate,$task_action_id,$userwise =NULL){
    $user           = $this->session->userdata('user');
    if(empty($user) || empty($user['user_id'])){ redirect('login'); return; }
    // session guard added 28 May 2026 to fix HTTP 500 on null session
    $data['user']   = $user;
    $uid            = $user['user_id'];
    $uyid           =  $user['type_id'];
    $this->load->model('Menu_model');
    ini_set('max_execution_time', 18000);
    $dt=$this->Menu_model->get_utype($uyid);
    $dep_name = (is_array($dt) && !empty($dt) && isset($dt[0]->name)) ? $dt[0]->name : 'home';
    if(isset($_POST['sdate'])){
    $sdate = $_POST['sdate'];
    $edate = $_POST['edate'];
    }
    if(is_null($userwise)){
        $userwise = '';
    }
    if($task_action_id == 'all'){
            $taskActionDatas =  'All';
    }else{
        $taskActionDatas =  $this->Menu_model->getTaskAction($task_action_id)[0]->name;
    }
    $totalTasksUserByDatas   = $this->Menu_model->GetTeamTaskOnSelfOrOtherFunnelTaskListsDats($target_uid,$sdate,$edate,$mtypes,$uid,$task_action_id,$userwise);
    // if($mtypes == 'positive_conversions'){
    //     echo sizeof($totalTasksUserByDatas);
    //     die;
    // }
    
    if(!empty($user)){
        $this->load->view('Reports/GetTeamTaskOnSelfOrOtherFunnelTaskLists',['uid'=>$uid,'user'=>$user,'sdate'=>$sdate,'edate'=>$edate,'sd'=>$sdate,'ed'=>$edate,'totalTasksUserByDatas'=>$totalTasksUserByDatas,'mtypes'=>$mtypes,'comming_user_types'=>$uyid,'mtypes'=>$mtypes,'target_uid'=>$target_uid,'taskActionDatas'=>$taskActionDatas,'dep_name'=>$dep_name]);
    }else{
        redirect('Menu/main');
    }
}
public function SpecialRemarksLeadsCurrentFY(){
    $user           = $this->session->userdata('user');
    if(empty($user) || empty($user['user_id'])){ redirect('login'); return; }
    // session guard added 28 May 2026 to fix HTTP 500 on null session
    $data['user']   = $user;
    $uid            = $user['user_id'];
    $uyid           =  $user['type_id'];
    $this->load->model('Menu_model');
    $this->load->model('Report_model');
    $dt=$this->Menu_model->get_utype($uyid);
    $dep_name = (is_array($dt) && !empty($dt) && isset($dt[0]->name)) ? $dt[0]->name : 'home';
    $admin_id_filter    = $this->input->Post('admin_id_filter');
    $rm_filter          = $this->input->Post('rm_filter');
    $task_action_id     = $this->input->Post('task_action');
    if(isset($_POST['sdate'])){
        $sdate                  = $this->input->Post('sdate');
        $edate                  = $this->input->Post('edate');
        if(isset($admin_id_filter)){
            if($admin_id_filter == 'all'){
                $admin_id_filter = 'all';
            }else{
                $uid = $admin_id_filter;
            }
            if(isset($rm_filter)){
                if($rm_filter =='all'){
                    $rm_filter = 'all';
                }else{
                    $uid = $rm_filter;
                }
            }
        }
    }else{
        $sdate              = date('Y-m-d');
        $edate              = date('Y-m-d');
        $rm_filter          = 'all';
        $task_action_id     = 'all';
        $admin_id_filter    = $uid;
    }
        $task_action_id = 1;
        if($task_action_id == 'all'){
            $taskActionDatas =  'All';
        }else{
            $task_action_id = 1;
            $taskActionDatas =  $this->Menu_model->getTaskAction($task_action_id)[0]->name;
        }
  
   
     $specialRemakrsDatas   = $this->Report_model->GetSpecialRemarksTaskLists($uid,$sdate,$edate,$task_action_id,$admin_id_filter,$rm_filter);
    $task_action_id =1;
    $task_type_Datas = $this->Report_model->GetActiveTaskData($task_action_id);
    if(!empty($user)){
        $this->load->view('Reports/SpecialRemarksLeadsCurrentFY',['uid'=>$uid,'user'=>$user,'user_tupe_id'=>$uyid,'sdate'=>$sdate,'edate'=>$edate,'task_action_id'=>$task_action_id,'taskActionDatas'=>$taskActionDatas,'task_action_id'=> $task_action_id,'admin_id_filter'=>$admin_id_filter,'rm_filter'=>$rm_filter,'dep_name'=>$dep_name,'tasktypeDatas'=>$task_type_Datas,'specialRemakrsDatas'=>$specialRemakrsDatas]);
    }else{
        redirect('Menu/main');
    }
}
public function AdminRemarksLeadsCurrentFY(){
    $user           = $this->session->userdata('user');
    if(empty($user) || empty($user['user_id'])){ redirect('login'); return; }
    // session guard added 28 May 2026 to fix HTTP 500 on null session
    $data['user']   = $user;
    $uid            = $user['user_id'];
    $uyid           =  $user['type_id'];
    $this->load->model('Menu_model');
    $this->load->model('Report_model');
    $dt=$this->Menu_model->get_utype($uyid);
    $dep_name = (is_array($dt) && !empty($dt) && isset($dt[0]->name)) ? $dt[0]->name : 'home';
    $admin_id_filter    = $this->input->Post('admin_id_filter');
    $rm_filter          = $this->input->Post('rm_filter');
    $task_action_id     = $this->input->Post('task_action');
    if(isset($_POST['sdate'])){
        $sdate                  = $this->input->Post('sdate');
        $edate                  = $this->input->Post('edate');
          if(isset($admin_id_filter)){
            if($admin_id_filter == 'all'){
                $admin_id_filter = 'all';
            }else{
                $uid = $admin_id_filter;
            }
            if(isset($rm_filter)){
                if($rm_filter =='all'){
                    $rm_filter = 'all';
                }else{
                    $uid = $rm_filter;
                }
            }
        }
    }else{
        $sdate              = date('Y-m-d');
        $sdate              = "2026-04-01";
        $edate              = date('Y-m-d');
        $admin_id_filter = $uid;
    }
    $task_action_id = 1;
    $taskActionDatas =  $this->Menu_model->getTaskAction($task_action_id)[0]->name;
 
  
    $specialRemakrsDatas   = $this->Report_model->GetAdminSpecialRemarksTaskLists($uid,$sdate,$edate,$task_action_id,$admin_id_filter);
    $task_action_id =1;
    $task_type_Datas = $this->Report_model->GetActiveTaskData($task_action_id);
    if(!empty($user)){
        $this->load->view('Reports/AdminRemarksLeadsCurrentFY',['uid'=>$uid,'user'=>$user,'user_tupe_id'=>$uyid,'sdate'=>$sdate,'edate'=>$edate,'task_action_id'=>$task_action_id,'taskActionDatas'=>$taskActionDatas,'task_action_id'=> $task_action_id,'admin_id_filter'=>$admin_id_filter,'rm_filter'=>$rm_filter,'dep_name'=>$dep_name,'tasktypeDatas'=>$task_type_Datas,'specialRemakrsDatas'=>$specialRemakrsDatas]);
    }else{
        redirect('Menu/main');
    }
}
public function LineManagerCallingonRPLeads(){
    $user           = $this->session->userdata('user');
    if(empty($user) || empty($user['user_id'])){ redirect('login'); return; }
    // session guard added 28 May 2026 to fix HTTP 500 on null session
    $data['user']   = $user;
    $uid            = $user['user_id'];
    $uyid           =  $user['type_id'];
    $this->load->model('Menu_model');
    $this->load->model('Report_model');
    $dt=$this->Menu_model->get_utype($uyid);
    $dep_name = (is_array($dt) && !empty($dt) && isset($dt[0]->name)) ? $dt[0]->name : 'home';
    $admin_id_filter    = $this->input->Post('admin_id_filter');
    $rm_filter          = $this->input->Post('rm_filter');
    $task_action_id     = $this->input->Post('task_action');
    if(isset($_POST['sdate'])){
        $sdate                  = $this->input->Post('sdate');
        $edate                  = $this->input->Post('edate');
          if(isset($admin_id_filter)){
            if($admin_id_filter == 'all'){
                $admin_id_filter = 'all';
            }else{
                $uid = $admin_id_filter;
            }
            if(isset($rm_filter)){
                if($rm_filter =='all'){
                    $rm_filter = 'all';
                }else{
                    $uid = $rm_filter;
                }
            }
        }
    }else{
        $sdate              = date('Y-m-d');
        $sdate              = "2025-09-01";
        $edate              = date('Y-m-d');
        $admin_id_filter = $uid;
    }
    $task_action_id = 1;
    $taskActionDatas =  $this->Menu_model->getTaskAction($task_action_id)[0]->name;
 
    $specialRemakrsDatas   = $this->Report_model->LineManagerCallingonRPLeadsLists($uid,$sdate,$edate,$task_action_id,$admin_id_filter);
    if(in_array($uyid,[3,4,13,24,15])){
        $specialRemakrsDatasonBDCM   = $this->Report_model->LineManagerCallingonRPLeadsListsonBDCM($uid,$sdate,$edate,$task_action_id,$admin_id_filter);
        // dd($specialRemakrsDatasonBDCM);
    }else{
        $specialRemakrsDatasonBDCM = [];
    }
    $task_action_id =1;
    $task_type_Datas = $this->Report_model->GetActiveTaskData($task_action_id);
    if(!empty($user)){
        $this->load->view('Reports/LineManagerCallingonRPLeads',['uid'=>$uid,'user'=>$user,'user_tupe_id'=>$uyid,'uyid'=>$uyid,'sdate'=>$sdate,'edate'=>$edate,'task_action_id'=>$task_action_id,'taskActionDatas'=>$taskActionDatas,'task_action_id'=> $task_action_id,'admin_id_filter'=>$admin_id_filter,'rm_filter'=>$rm_filter,'dep_name'=>$dep_name,'tasktypeDatas'=>$task_type_Datas,'specialRemakrsDatas'=>$specialRemakrsDatas,'specialRemakrsDatasonBDCM'=>$specialRemakrsDatasonBDCM]);
    }else{
        redirect('Menu/main');
    }
}
public function DayManagementStarRating(){
    $user           = $this->session->userdata('user');
    if(empty($user) || empty($user['user_id'])){ redirect('login'); return; }
    // session guard added 28 May 2026 to fix HTTP 500 on null session
    $data['user']   = $user;
    $uid            = $user['user_id'];
    $uyid           =  $user['type_id'];
    $this->load->model('Menu_model');
    $this->load->model('Report_model');
    $dt=$this->Menu_model->get_utype($uyid);
    $dep_name = (is_array($dt) && !empty($dt) && isset($dt[0]->name)) ? $dt[0]->name : 'home';
    $admin_id_filter    = $this->input->Post('admin_id_filter');
    $rm_filter          = $this->input->Post('rm_filter');
    if(isset($_POST['sdate'])){
        $sdate                  = $this->input->Post('sdate');
        $edate                  = $this->input->Post('edate');
         if(isset($admin_id_filter)){
            if($admin_id_filter == 'all'){
                $admin_id_filter = 'all';
            }else{
                $uid = $admin_id_filter;
            }
            if(isset($rm_filter)){
                if($rm_filter =='all'){
                    $rm_filter = 'all';
                }else{
                    $uid = $rm_filter;
                }
            }
        }
    }else{
        // $sdate              = date('Y-m-d');
        // $sdate = date('Y-m-d', strtotime('-7 days', strtotime($sdate)));  
        // $edate              = date('Y-m-d');
        $currentDate= date("Y-m-d");
        $sdate      =  $this->Menu_model->findSpecialDate($currentDate);
        $edate      =  $sdate;
        $rm_filter          = 'all';
        $admin_id_filter    = $uid;
    }
    $specialRemakrsDatas   = $this->Report_model->DayManagementStarRatingLists($uid,$sdate,$edate,$admin_id_filter,$rm_filter);
    if(!empty($user)){
        $this->load->view('Reports/DayManagementStarRating',['uid'=>$uid,'user'=>$user,'user_tupe_id'=>$uyid,'sdate'=>$sdate,'edate'=>$edate,'admin_id_filter'=>$admin_id_filter,'rm_filter'=>$rm_filter,'dep_name'=>$dep_name,'specialRemakrsDatas'=>$specialRemakrsDatas]);
    }else{
        redirect('Menu/main');
    }
}
public function TaskCheckingManagementStarRating(){
    $user           = $this->session->userdata('user');
    if(empty($user) || empty($user['user_id'])){ redirect('login'); return; }
    // session guard added 28 May 2026 to fix HTTP 500 on null session
    $data['user']   = $user;
    $uid            = $user['user_id'];
    $uyid           =  $user['type_id'];
    $this->load->model('Menu_model');
    $this->load->model('Report_model');
    $dt=$this->Menu_model->get_utype($uyid);
    $dep_name = (is_array($dt) && !empty($dt) && isset($dt[0]->name)) ? $dt[0]->name : 'home';
    $admin_id_filter    = $this->input->Post('admin_id_filter');
    $rm_filter          = $this->input->Post('rm_filter');
    if(isset($_POST['sdate'])){
        $sdate                  = $this->input->Post('sdate');
        $edate                  = $this->input->Post('edate');
         if(isset($admin_id_filter)){
            if($admin_id_filter == 'all'){
                $admin_id_filter = 'all';
            }else{
                $uid = $admin_id_filter;
            }
            if(isset($rm_filter)){
                if($rm_filter =='all'){
                    $rm_filter = 'all';
                }else{
                    $uid = $rm_filter;
                }
            }
        }
    }else{
        // $sdate              = date('Y-m-d');
        // $sdate = date('Y-m-d', strtotime('-7 days', strtotime($sdate)));  
        // $edate              = date('Y-m-d');
        $currentDate    = date("Y-m-d");
        // $currentDate      = '2025-09-03';
        $sdate      =  $this->Menu_model->findSpecialDate($currentDate);
       
        $edate      =  $sdate;
        $rm_filter          = 'all';
        $admin_id_filter    = $uid;
    }
    $specialRemakrsDatas   = $this->Report_model->TaskManagementStarRatingLists($uid,$sdate,$edate,$admin_id_filter,$rm_filter);
    
    $sessiontime   = $this->Report_model->GetSeesionTimeTakeBYSConTaskCheck($uid,$sdate,$edate,$admin_id_filter,$rm_filter);

    if(!empty($user)){
        $this->load->view('Reports/TaskCheckingManagementStarRating',['uid'=>$uid,'user'=>$user,'user_tupe_id'=>$uyid,'sdate'=>$sdate,'edate'=>$edate,'admin_id_filter'=>$admin_id_filter,'rm_filter'=>$rm_filter,'dep_name'=>$dep_name,'specialRemakrsDatas'=>$specialRemakrsDatas,'sessiontime'=>$sessiontime]);
    }else{
        redirect('Menu/main');
    }
}
public function TaskCheckingSessionManagement($targetUID,$sdate,$edate,$rtype){
    $user           = $this->session->userdata('user');
    if(empty($user) || empty($user['user_id'])){ redirect('login'); return; }
    // session guard added 28 May 2026 to fix HTTP 500 on null session
    $data['user']   = $user;
    $uid            = $user['user_id'];
    $uyid           =  $user['type_id'];
    $this->load->model('Menu_model');
    $this->load->model('Report_model');
    $dt=$this->Menu_model->get_utype($uyid);
    $dep_name = (is_array($dt) && !empty($dt) && isset($dt[0]->name)) ? $dt[0]->name : 'home';

    $rm_filter              = 'all';
    $admin_id_filter        = $targetUID;

    $totalComplete   = $this->Report_model->GetTaskCheckComplete($targetUID,$sdate,$edate);

    if(!empty($user)){
        $this->load->view('Reports/TaskCheckingSessionManagement',['uid'=>$uid,'user'=>$user,'user_tupe_id'=>$uyid,'sdate'=>$sdate,'edate'=>$edate,'admin_id_filter'=>$admin_id_filter,'rm_filter'=>$rm_filter,'rtype'=>$rtype,'dep_name'=>$dep_name,'totalComplete'=>$totalComplete]);
    }else{
        redirect('Menu/main');
    }


}
public function TaskCheckingManagementStarRatingDetails($targetuid,$sdate,$edate,$approved_by = NULL){
    $user           = $this->session->userdata('user');
    if(empty($user) || empty($user['user_id'])){ redirect('login'); return; }
    // session guard added 28 May 2026 to fix HTTP 500 on null session
    $data['user']   = $user;
    $uid            = $user['user_id'];
    $uyid           =  $user['type_id'];
    $this->load->model('Menu_model');
    $this->load->model('Report_model');
    $dt=$this->Menu_model->get_utype($uyid);
    $dep_name = (is_array($dt) && !empty($dt) && isset($dt[0]->name)) ? $dt[0]->name : 'home';
    $targetDetails = $this->Menu_model->get_userbyid($targetuid); 
    $specialRemakrsDatas   = $this->Report_model->TaskCheckingManagementStarRatingDetailsLists($targetuid,$sdate,$edate,$approved_by);
    $sessiontime   = $this->Report_model->GetSeesionTimeTakeBYSConTaskCheckBySingleUser($targetuid,$sdate,$edate,$approved_by);
    // dd($sessiontime);
    if(!empty($user)){
        $this->load->view('Reports/TaskCheckingManagementStarRatingDetails',['uid'=>$uid,'targetuid'=>$targetuid,'user'=>$user,'user_tupe_id'=>$uyid,'sdate'=>$sdate,'edate'=>$edate,'dep_name'=>$dep_name,'specialRemakrsDatas'=>$specialRemakrsDatas,'targetDetails'=>$targetDetails,'approved_by'=>$approved_by,'sessiontime'=>$sessiontime]);
    }else{
        redirect('Menu/main');
    }
}
public function TaskCheckingManagementStarRatingSingleDetails($task_id,$sdate,$edate){
    $user           = $this->session->userdata('user');
    if(empty($user) || empty($user['user_id'])){ redirect('login'); return; }
    // session guard added 28 May 2026 to fix HTTP 500 on null session
    $data['user']   = $user;
    $uid            = $user['user_id'];
    $uyid           =  $user['type_id'];
    $this->load->model('Menu_model');
    $this->load->model('Report_model');
    $dt=$this->Menu_model->get_utype($uyid);
    $dep_name = (is_array($dt) && !empty($dt) && isset($dt[0]->name)) ? $dt[0]->name : 'home';
    $specialRemakrsDatas    = $this->Report_model->TaskCheckingManagementStarRatingSingleDetailsLists($task_id,$sdate,$edate);
    $liveMettingsTasks      = $this->Report_model->GetTBLTaskUsingSinleTaskID($task_id);  

    $reminderMessageTasks   = $this->Report_model->GetTBLTaskReminderCommentsByTaskIDs($task_id);  
    // dd($liveMettingsTasks);
    if(!empty($user)){
        $this->load->view('Reports/TaskCheckingManagementStarRatingSingleDetails',['uid'=>$uid,'task_id'=>$task_id,'user'=>$user,'user_tupe_id'=>$uyid,'sdate'=>$sdate,'edate'=>$edate,'dep_name'=>$dep_name,'specialRemakrsDatas'=>$specialRemakrsDatas,'liveMettingsTasks'=>$liveMettingsTasks,'reminderMessageTasks'=>$reminderMessageTasks]);
    }else{
        redirect('Menu/main');
    }
}
public function DayManagementStarRatingDetails($targetuid,$sdate,$edate,$approved_by =NULL){
    $user           = $this->session->userdata('user');
    if(empty($user) || empty($user['user_id'])){ redirect('login'); return; }
    // session guard added 28 May 2026 to fix HTTP 500 on null session
    $data['user']   = $user;
    $uid            = $user['user_id'];
    $uyid           =  $user['type_id'];
    $this->load->model('Menu_model');
    $this->load->model('Report_model');
    $dt=$this->Menu_model->get_utype($uyid);
    $dep_name = (is_array($dt) && !empty($dt) && isset($dt[0]->name)) ? $dt[0]->name : 'home';
    $targetDetails = $this->Menu_model->get_userbyid($targetuid); 
    $specialRemakrsDatas   = $this->Report_model->DayManagementStarRatingListsDetails($targetuid,$sdate,$edate,$approved_by);
    if(!empty($user)){
        $this->load->view('Reports/DayManagementStarRatingDetails',['uid'=>$uid,'targetuid'=>$targetuid,'user'=>$user,'user_tupe_id'=>$uyid,'sdate'=>$sdate,'edate'=>$edate,'dep_name'=>$dep_name,'specialRemakrsDatas'=>$specialRemakrsDatas,'targetDetails'=>$targetDetails,'approved_by'=>$approved_by]);
    }else{
        redirect('Menu/main');
    }
}
public function SpecialRemarksLeadsCurrentFYLists($tuid,$sdate,$edate){
    $user           = $this->session->userdata('user');
    if(empty($user) || empty($user['user_id'])){ redirect('login'); return; }
    // session guard added 28 May 2026 to fix HTTP 500 on null session
    $data['user']   = $user;
    $uid            = $user['user_id'];
    $uyid           =  $user['type_id'];
    $this->load->model('Menu_model');
    $this->load->model('Report_model');
    $dt=$this->Menu_model->get_utype($uyid);
    $dep_name = (is_array($dt) && !empty($dt) && isset($dt[0]->name)) ? $dt[0]->name : 'home';
    $task_action_id = 1;
   
     $specialRemakrsDatas  = $this->Report_model->GetSpecialRemarksTaskListsByUser($tuid,$sdate,$edate,$task_action_id);
     $taskActionDatas =  $this->Menu_model->getTaskAction($task_action_id)[0]->name;
    if(!empty($user)){
        $this->load->view('Reports/SpecialRemarksLeadsCurrentFYLists',['uid'=>$uid,'user'=>$user,'user_tupe_id'=>$uyid,'sdate'=>$sdate,'edate'=>$edate,'task_action_id'=>$task_action_id,'task_action_id'=> $task_action_id,'dep_name'=>$dep_name,'specialRemakrsDatas'=>$specialRemakrsDatas,'taskActionDatas'=>$taskActionDatas]);
    }else{
        redirect('Menu/main');
    }
}
public function LineManagerCallingRemarksonRPLeads($tuid,$sdate,$edate){
    $user           = $this->session->userdata('user');
    if(empty($user) || empty($user['user_id'])){ redirect('login'); return; }
    // session guard added 28 May 2026 to fix HTTP 500 on null session
    $data['user']   = $user;
    $uid            = $user['user_id'];
    $uyid           =  $user['type_id'];
    $this->load->model('Menu_model');
    $this->load->model('Report_model');
    $dt=$this->Menu_model->get_utype($uyid);
    $dep_name = (is_array($dt) && !empty($dt) && isset($dt[0]->name)) ? $dt[0]->name : 'home';
    $task_action_id = 1;
   
     $specialRemakrsDatas  = $this->Report_model->LineManagerCallingRemarksonRPLeadsLists($tuid,$sdate,$edate,$task_action_id);
     $taskActionDatas =  $this->Menu_model->getTaskAction($task_action_id)[0]->name;
    if(!empty($user)){
        $this->load->view('Reports/LineManagerCallingRemarksonRPLeads',['uid'=>$uid,'user'=>$user,'user_tupe_id'=>$uyid,'sdate'=>$sdate,'edate'=>$edate,'task_action_id'=>$task_action_id,'task_action_id'=> $task_action_id,'dep_name'=>$dep_name,'specialRemakrsDatas'=>$specialRemakrsDatas,'taskActionDatas'=>$taskActionDatas]);
    }else{
        redirect('Menu/main');
    }
}
public function LineManagerCallingRemarksonRPBDLeads($tuid,$sdate,$edate){
    $user           = $this->session->userdata('user');
    if(empty($user) || empty($user['user_id'])){ redirect('login'); return; }
    // session guard added 28 May 2026 to fix HTTP 500 on null session
    $data['user']   = $user;
    $uid            = $user['user_id'];
    $uyid           =  $user['type_id'];
    $this->load->model('Menu_model');
    $this->load->model('Report_model');
    $dt=$this->Menu_model->get_utype($uyid);
    $dep_name = (is_array($dt) && !empty($dt) && isset($dt[0]->name)) ? $dt[0]->name : 'home';
    $task_action_id = 1;
   
     $specialRemakrsDatas  = $this->Report_model->LineManagerCallingRemarksonRPLeadsBDLists($tuid,$sdate,$edate,$task_action_id,$uid);
     $taskActionDatas =  $this->Menu_model->getTaskAction($task_action_id)[0]->name;
    if(!empty($user)){
        $this->load->view('Reports/LineManagerCallingRemarksonRPBDLeads',['uid'=>$uid,'user'=>$user,'user_tupe_id'=>$uyid,'sdate'=>$sdate,'edate'=>$edate,'task_action_id'=>$task_action_id,'task_action_id'=> $task_action_id,'dep_name'=>$dep_name,'specialRemakrsDatas'=>$specialRemakrsDatas,'taskActionDatas'=>$taskActionDatas]);
    }else{
        redirect('Menu/main');
    }
}
public function AdminRemarksLeadsCurrentFYLists($tuid,$sdate,$edate){
    $user           = $this->session->userdata('user');
    if(empty($user) || empty($user['user_id'])){ redirect('login'); return; }
    // session guard added 28 May 2026 to fix HTTP 500 on null session
    $data['user']   = $user;
    $uid            = $user['user_id'];
    $uyid           =  $user['type_id'];
    $this->load->model('Menu_model');
    $this->load->model('Report_model');
    $dt=$this->Menu_model->get_utype($uyid);
    $dep_name = (is_array($dt) && !empty($dt) && isset($dt[0]->name)) ? $dt[0]->name : 'home';
    $task_action_id = 1;
   
     $specialRemakrsDatas  = $this->Report_model->GetAdminTaskListsByUser($tuid,$sdate,$edate,$task_action_id);
     $taskActionDatas =  $this->Menu_model->getTaskAction($task_action_id)[0]->name;
    if(!empty($user)){
        $this->load->view('Reports/AdminRemarksLeadsCurrentFYLists',['uid'=>$uid,'user'=>$user,'user_tupe_id'=>$uyid,'sdate'=>$sdate,'edate'=>$edate,'task_action_id'=>$task_action_id,'task_action_id'=> $task_action_id,'dep_name'=>$dep_name,'specialRemakrsDatas'=>$specialRemakrsDatas,'taskActionDatas'=>$taskActionDatas]);
    }else{
        redirect('Menu/main');
    }
}
public function TeamPlannerReportLists($mtypes,$target_uid,$sdate,$edate,$task_action_id,$userwise =NULL){
    $user           = $this->session->userdata('user');
    if(empty($user) || empty($user['user_id'])){ redirect('login'); return; }
    // session guard added 28 May 2026 to fix HTTP 500 on null session
    $data['user']   = $user;
    $uid            = $user['user_id'];
    $uyid           =  $user['type_id'];
    $this->load->model('Menu_model');
    ini_set('max_execution_time', 18000);
    $dt=$this->Menu_model->get_utype($uyid);
    $dep_name = (is_array($dt) && !empty($dt) && isset($dt[0]->name)) ? $dt[0]->name : 'home';
    if(isset($_POST['sdate'])){
    $sdate = $_POST['sdate'];
    $edate = $_POST['edate'];
    }
    if(is_null($userwise)){
        $userwise = '';
    }
    if($task_action_id == 'all'){
            $taskActionDatas =  'All';
    }else{
        $taskActionDatas =  $this->Menu_model->getTaskAction($task_action_id)[0]->name;
    }
    $totalTasksUserByDatas   = $this->Report_model->GetTeamWisePlanedTaskTypeUsingPlannerDataLists($target_uid,$sdate,$edate,$mtypes,$uid,$task_action_id,$userwise);
   
    // dd($totalTasksUserByDatas);
    if(!empty($user)){
        $this->load->view('Reports/TeamPlannerReportLists',['uid'=>$uid,'user'=>$user,'sdate'=>$sdate,'edate'=>$edate,'sd'=>$sdate,'ed'=>$edate,'totalTasksUserByDatas'=>$totalTasksUserByDatas,'mtypes'=>$mtypes,'comming_user_types'=>$uyid,'mtypes'=>$mtypes,'target_uid'=>$target_uid,'taskActionDatas'=>$taskActionDatas,'dep_name'=>$dep_name]);
    }else{
        redirect('Menu/main');
    }
}
public function OurTeamFunnal(){
    $user           = $this->session->userdata('user');
    if(empty($user) || empty($user['user_id'])){
        redirect('login');
        return;
    }
    $data['user']   = $user;
    $uid            = $user['user_id'];
    $uyid           = isset($user['type_id']) ? $user['type_id'] : 0;
    $this->load->model('Menu_model');
    $dt             =  $this->Menu_model->get_utype($uyid);
    $dep_name       = (is_array($dt) && !empty($dt) && isset($dt[0]->name)) ? $dt[0]->name : 'home';
    $admin_id_filter    = $this->input->Post('admin_id_filter');
    $rm_filter          = $this->input->Post('rm_filter');
    if(isset($_POST['admin_id_filter'])){
        $sdate                  = $this->input->Post('sdate');
        $edate                  = $this->input->Post('edate');
        if(isset($admin_id_filter)){
            if($admin_id_filter == 'all'){
                $admin_id_filter = 'all';
            }else{
                $uid = $admin_id_filter;
            }
            if(isset($rm_filter)){
                if($rm_filter =='all'){
                    $rm_filter = 'all';
                }else{
                    $uid = $rm_filter;
                }
            }
        }
    }else{
        $sdate              = date('Y-m-d');
        $edate              = date('Y-m-d');
        $rm_filter          = 'all';
        $admin_id_filter    = $uid;
    }
        if($rm_filter !=='all'){
           $uid = $rm_filter;
        }else{
          $uid = $admin_id_filter;
        }
    if(isset($_POST['category'])){
        $category   = $_POST['category'];
    }else{
        $category = '';
        }
    $totalFunnnels = $this->Report_model->getAllCompanyBYRollesMaiing($uid,$admin_id_filter,$rm_filter);
    $this->load->view('Reports/OurTeamFunnal',[
        'user'=>$user,
        'uid'=>$uid,
        'category'=>$category,
        'dep_name'=>$dep_name,
        'totalFunnnels'=>$totalFunnnels,
        'admin_id_filter'=>$admin_id_filter,
        'rm_filter'=>$rm_filter,
        'sdate'=>$sdate,
        'edate'=>$edate,
    ]);
}


public function FunnelDetails($mtypes,$target_uid,$userwise =NULL){
    $user           = $this->session->userdata('user');
    if(empty($user) || empty($user['user_id'])){ redirect('login'); return; }
    // session guard added 28 May 2026 to fix HTTP 500 on null session
    $data['user']   = $user;
    $uid            = $user['user_id'];
    $uyid           =  $user['type_id'];
    $this->load->model('Menu_model');
    $dt=$this->Menu_model->get_utype($uyid);
    $dep_name = (is_array($dt) && !empty($dt) && isset($dt[0]->name)) ? $dt[0]->name : 'home';
    if(isset($_POST['sdate'])){
    $sdate = $_POST['sdate'];
    $edate = $_POST['edate'];
    }
    if(is_null($userwise)){
        $userwise = '';
    }
    $totalTasksUserByDatas   = $this->Report_model->GetFunnelDetails($target_uid,$mtypes,$userwise);
    // dd($totalTasksUserByDatas);
    if(!empty($user)){
        $this->load->view('Reports/FunnelDetails',['uid'=>$uid,'user'=>$user,'sdate'=>$sdate,'edate'=>$edate,'sd'=>$sdate,'ed'=>$edate,'totalTasksUserByDatas'=>$totalTasksUserByDatas,'mtypes'=>$mtypes,'comming_user_types'=>$uyid,'mtypes'=>$mtypes,'target_uid'=>$target_uid,'dep_name'=>$dep_name]);
    }else{
        redirect('Menu/main');
    }
}



public function MoreThenNDaysNoActivityDone(){
    $user           = $this->session->userdata('user');
    if(empty($user) || empty($user['user_id'])){ redirect('login'); return; }
    // session guard added 28 May 2026 to fix HTTP 500 on null session
    $data['user']   = $user;
    $uid            = $user['user_id'];
    $uyid           =  $user['type_id'];
    $this->load->model('Menu_model');
    $dt             =  $this->Menu_model->get_utype($uyid);
    $dep_name = (is_array($dt) && !empty($dt) && isset($dt[0]->name)) ? $dt[0]->name : 'home';
    $admin_id_filter    = $this->input->Post('admin_id_filter');
    $rm_filter          = $this->input->Post('rm_filter');
    if(isset($_POST['admin_id_filter'])){
        $sdate                  = $this->input->Post('sdate');
        $edate                  = $this->input->Post('edate');
        if(isset($admin_id_filter)){
            if($admin_id_filter == 'all'){
                $admin_id_filter = 'all';
            }else{
                $uid = $admin_id_filter;
            }
            if(isset($rm_filter)){
                if($rm_filter =='all'){
                    $rm_filter = 'all';
                }else{
                    $uid = $rm_filter;
                }
            }
        }
    }else{
        $sdate              = date('Y-m-d');
        $edate              = date('Y-m-d');
        $rm_filter          = 'all';
        $admin_id_filter    = $uid;
    }
        if($rm_filter !=='all'){
           $uid = $rm_filter;
        }else{
          $uid = $admin_id_filter;
        }
    if(isset($_POST['category'])){
        $category   = $_POST['category'];
    }else{
        $category = '';
        }
    $totalFunnnels = $this->Report_model->MoreThenNDaysNoActivityDoneBYUser($uid,$admin_id_filter,$rm_filter);


    $this->load->view('Reports/MoreThenNDaysNoActivityDone',[
        'user'              => $user,
        'uid'               => $uid,
        'category'          => $category,
        'dep_name'          => $dep_name,
        'totalFunnnels'     => $totalFunnnels,
        'admin_id_filter'   => $admin_id_filter,
        'rm_filter'         => $rm_filter,
        'sdate'             => $sdate,
        'edate'             => $edate
    ]);
}

public function MoreThenNDaysNoActivityDoneDetails($tarFilter,$target_uid,$userwise,$status = NULL){
    $user           = $this->session->userdata('user');
    if(empty($user) || empty($user['user_id'])){ redirect('login'); return; }
    // session guard added 28 May 2026 to fix HTTP 500 on null session
    $data['user']   = $user;
    $uid            = $user['user_id'];
    $uyid           =  $user['type_id'];
    $this->load->model('Menu_model');
    $dt             =  $this->Menu_model->get_utype($uyid);
    $dep_name = (is_array($dt) && !empty($dt) && isset($dt[0]->name)) ? $dt[0]->name : 'home';

    $totalTasksUserByDatas   = $this->Report_model->MoreThenNDaysNoActivityDoneBYUserCompanyDetails($tarFilter,$target_uid,$userwise,$status);

    $this->load->view('Reports/MoreThenNDaysNoActivityDoneDetails',[
        'user'                  => $user,
        'uid'                   => $uid,
        'category'              => $category,
        'dep_name'              => $dep_name,
        'totalTasksUserByDatas' => $totalTasksUserByDatas,
        'tarFilter'             => $tarFilter,
        'ctarget_uid'           => $target_uid,
        'userwise'              => $userwise,
        'companyStatus'                => $status
    ]);
}


public function MoreThenNDaysNoActivityDoneByLineManager(){
    $user           = $this->session->userdata('user');
    if(empty($user) || empty($user['user_id'])){ redirect('login'); return; }
    // session guard added 28 May 2026 to fix HTTP 500 on null session
    $data['user']   = $user;
    $uid            = $user['user_id'];
    $uyid           =  $user['type_id'];
    $this->load->model('Menu_model');
    $dt             =  $this->Menu_model->get_utype($uyid);
    $dep_name = (is_array($dt) && !empty($dt) && isset($dt[0]->name)) ? $dt[0]->name : 'home';
    $admin_id_filter    = $this->input->Post('admin_id_filter');
    $rm_filter          = $this->input->Post('rm_filter');
    if(isset($_POST['admin_id_filter'])){
        $sdate                  = $this->input->Post('sdate');
        $edate                  = $this->input->Post('edate');
        if(isset($admin_id_filter)){
            if($admin_id_filter == 'all'){
                $admin_id_filter = 'all';
            }else{
                $uid = $admin_id_filter;
            }
            if(isset($rm_filter)){
                if($rm_filter =='all'){
                    $rm_filter = 'all';
                }else{
                    $uid = $rm_filter;
                }
            }
        }
    }else{
        $sdate              = date('Y-m-d');
        $edate              = date('Y-m-d');
        $rm_filter          = 'all';
        $admin_id_filter    = $uid;
    }
        if($rm_filter !=='all'){
           $uid = $rm_filter;
        }else{
          $uid = $admin_id_filter;
        }
    if(isset($_POST['category'])){
        $category   = $_POST['category'];
    }else{
        $category = '';
        }
    $totalFunnnels = $this->Report_model->MoreThenNDaysNoActivityDoneBYUserLineManager($uid,$admin_id_filter,$rm_filter);


    $this->load->view('Reports/MoreThenNDaysNoActivityDoneByLineManager',[
        'user'              => $user,
        'uid'               => $uid,
        'category'          => $category,
        'dep_name'          => $dep_name,
        'totalFunnnels'     => $totalFunnnels,
        'admin_id_filter'   => $admin_id_filter,
        'rm_filter'         => $rm_filter,
        'sdate'             => $sdate,
        'edate'             => $edate
    ]);
}

public function MoreThenNDaysNoActivityDoneByLineManagerDetails($tarFilter,$target_uid,$userwise,$status = NULL){
    $user           = $this->session->userdata('user');
    if(empty($user) || empty($user['user_id'])){ redirect('login'); return; }
    // session guard added 28 May 2026 to fix HTTP 500 on null session
    $data['user']   = $user;
    $uid            = $user['user_id'];
    $uyid           =  $user['type_id'];
    $this->load->model('Menu_model');
    $dt             =  $this->Menu_model->get_utype($uyid);
    $dep_name = (is_array($dt) && !empty($dt) && isset($dt[0]->name)) ? $dt[0]->name : 'home';

    $totalTasksUserByDatas   = $this->Report_model->MoreThenNDaysNoActivityDoneBYUserCompanyDetailsLineManager($tarFilter,$target_uid,$userwise,$status);

    $this->load->view('Reports/MoreThenNDaysNoActivityDoneByLineManagerDetails',[
        'user'                  => $user,
        'uid'                   => $uid,
        'category'              => $category,
        'dep_name'              => $dep_name,
        'totalTasksUserByDatas' => $totalTasksUserByDatas,
        'tarFilter'             => $tarFilter,
        'ctarget_uid'           => $target_uid,
        'userwise'              => $userwise,
        'companyStatus'                => $status
    ]);
}






public function MoreThenNDaysNoActivityDoneByLineManagerWhereProposalSent(){
    $user           = $this->session->userdata('user');
    if(empty($user) || empty($user['user_id'])){ redirect('login'); return; }
    // session guard added 28 May 2026 to fix HTTP 500 on null session
    $data['user']   = $user;
    $uid            = $user['user_id'];
    $uyid           =  $user['type_id'];
    $this->load->model('Menu_model');
    $dt             =  $this->Menu_model->get_utype($uyid);
    $dep_name = (is_array($dt) && !empty($dt) && isset($dt[0]->name)) ? $dt[0]->name : 'home';
    $admin_id_filter    = $this->input->Post('admin_id_filter');
    $rm_filter          = $this->input->Post('rm_filter');
    if(isset($_POST['admin_id_filter'])){
        $sdate                  = $this->input->Post('sdate');
        $edate                  = $this->input->Post('edate');
        if(isset($admin_id_filter)){
            if($admin_id_filter == 'all'){
                $admin_id_filter = 'all';
            }else{
                $uid = $admin_id_filter;
            }
            if(isset($rm_filter)){
                if($rm_filter =='all'){
                    $rm_filter = 'all';
                }else{
                    $uid = $rm_filter;
                }
            }
        }
    }else{
        $sdate              = date('Y-m-d');
        $edate              = date('Y-m-d');
        $rm_filter          = 'all';
        $admin_id_filter    = $uid;
    }
        if($rm_filter !=='all'){
           $uid = $rm_filter;
        }else{
          $uid = $admin_id_filter;
        }
    if(isset($_POST['category'])){
        $category   = $_POST['category'];
    }else{
        $category = '';
        }
    $totalFunnnels = $this->Report_model->MoreThenNDaysNoActivityDoneBYUserLineManagerWhereProposalSent($uid,$admin_id_filter,$rm_filter);
    
    $this->load->view('Reports/MoreThenNDaysNoActivityDoneByLineManagerWhereProposalSent',[
        'user'              => $user,
        'uid'               => $uid,
        'category'          => $category,
        'dep_name'          => $dep_name,
        'totalFunnnels'     => $totalFunnnels,
        'admin_id_filter'   => $admin_id_filter,
        'rm_filter'         => $rm_filter,
        'sdate'             => $sdate,
        'edate'             => $edate
    ]);
}


public function MoreThenNDaysNoActivityDoneByLineManagerDetailsWhereProposalSent($tarFilter,$target_uid,$userwise,$status = NULL){
    $user           = $this->session->userdata('user');
    if(empty($user) || empty($user['user_id'])){ redirect('login'); return; }
    // session guard added 28 May 2026 to fix HTTP 500 on null session
    $data['user']   = $user;
    $uid            = $user['user_id'];
    $uyid           =  $user['type_id'];
    $this->load->model('Menu_model');
    $dt             =  $this->Menu_model->get_utype($uyid);
    $dep_name = (is_array($dt) && !empty($dt) && isset($dt[0]->name)) ? $dt[0]->name : 'home';

    $totalTasksUserByDatas   = $this->Report_model->MoreThenNDaysNoActivityDoneBYUserCompanyDetailsLineManagerWhereProposalSent($tarFilter,$target_uid,$userwise,$status);


    $this->load->view('Reports/MoreThenNDaysNoActivityDoneByLineManagerDetailsWhereProposalSent',[
        'user'                  => $user,
        'uid'                   => $uid,
        'category'              => $category,
        'dep_name'              => $dep_name,
        'totalTasksUserByDatas' => $totalTasksUserByDatas,
        'tarFilter'             => $tarFilter,
        'ctarget_uid'           => $target_uid,
        'userwise'              => $userwise,
        'companyStatus'                => $status
    ]);
}


public function MoreThenNDaysNoActivityDoneByLineManagerWhereRPMeetingDone(){
    $user           = $this->session->userdata('user');
    if(empty($user) || empty($user['user_id'])){ redirect('login'); return; }
    // session guard added 28 May 2026 to fix HTTP 500 on null session
    $data['user']   = $user;
    $uid            = $user['user_id'];
    $uyid           =  $user['type_id'];
    $this->load->model('Menu_model');
    $dt             =  $this->Menu_model->get_utype($uyid);
    $dep_name = (is_array($dt) && !empty($dt) && isset($dt[0]->name)) ? $dt[0]->name : 'home';
    $admin_id_filter    = $this->input->Post('admin_id_filter');
    $rm_filter          = $this->input->Post('rm_filter');
    if(isset($_POST['admin_id_filter'])){
        $sdate                  = $this->input->Post('sdate');
        $edate                  = $this->input->Post('edate');
        if(isset($admin_id_filter)){
            if($admin_id_filter == 'all'){
                $admin_id_filter = 'all';
            }else{
                $uid = $admin_id_filter;
            }
            if(isset($rm_filter)){
                if($rm_filter =='all'){
                    $rm_filter = 'all';
                }else{
                    $uid = $rm_filter;
                }
            }
        }
    }else{
        $sdate              = date('Y-m-d');
        $edate              = date('Y-m-d');
        $rm_filter          = 'all';
        $admin_id_filter    = $uid;
    }
        if($rm_filter !=='all'){
           $uid = $rm_filter;
        }else{
          $uid = $admin_id_filter;
        }
    if(isset($_POST['category'])){
        $category   = $_POST['category'];
    }else{
        $category = '';
        }
    $totalFunnnels = $this->Report_model->MoreThenNDaysNoActivityDoneBYUserLineManagerWhereRPMeetingDone($uid,$admin_id_filter,$rm_filter);
    
    $this->load->view('Reports/MoreThenNDaysNoActivityDoneByLineManagerWhereRPMeetingDone',[
        'user'              => $user,
        'uid'               => $uid,
        'category'          => $category,
        'dep_name'          => $dep_name,
        'totalFunnnels'     => $totalFunnnels,
        'admin_id_filter'   => $admin_id_filter,
        'rm_filter'         => $rm_filter,
        'sdate'             => $sdate,
        'edate'             => $edate
    ]);
}



public function MoreThenNDaysNoActivityDoneByLineManagerDetailsWhereRPMeetingDone($tarFilter,$target_uid,$userwise,$status = NULL){
    $user           = $this->session->userdata('user');
    if(empty($user) || empty($user['user_id'])){ redirect('login'); return; }
    // session guard added 28 May 2026 to fix HTTP 500 on null session
    $data['user']   = $user;
    $uid            = $user['user_id'];
    $uyid           =  $user['type_id'];
    $this->load->model('Menu_model');
    $dt             =  $this->Menu_model->get_utype($uyid);
    $dep_name = (is_array($dt) && !empty($dt) && isset($dt[0]->name)) ? $dt[0]->name : 'home';

    $totalTasksUserByDatas   = $this->Report_model->MoreThenNDaysNoActivityDoneBYUserCompanyDetailsLineManagerWhereRPMeetingDone($tarFilter,$target_uid,$userwise,$status);


    $this->load->view('Reports/MoreThenNDaysNoActivityDoneByLineManagerDetailsWhereRPMeetingDone',[
        'user'                  => $user,
        'uid'                   => $uid,
        'category'              => $category,
        'dep_name'              => $dep_name,
        'totalTasksUserByDatas' => $totalTasksUserByDatas,
        'tarFilter'             => $tarFilter,
        'ctarget_uid'           => $target_uid,
        'userwise'              => $userwise,
        'companyStatus'                => $status
    ]);
}

public function FunnelDetailsWithCluster($mtypes,$target_uid,$userwise =NULL){
    $user           = $this->session->userdata('user');
    if(empty($user) || empty($user['user_id'])){ redirect('login'); return; }
    // session guard added 28 May 2026 to fix HTTP 500 on null session
    $data['user']   = $user;
    $uid            = $user['user_id'];
    $uyid           =  $user['type_id'];
    $this->load->model('Menu_model');
    $dt=$this->Menu_model->get_utype($uyid);
    $dep_name = (is_array($dt) && !empty($dt) && isset($dt[0]->name)) ? $dt[0]->name : 'home';
    if(isset($_POST['sdate'])){
    $sdate = $_POST['sdate'];
    $edate = $_POST['edate'];
    }
    if(is_null($userwise)){
        $userwise = '';
    }
    $totalTasksUserByDatas   = $this->Report_model->GetFunnelDetailsWithCluster($target_uid,$mtypes,$userwise);
    // dd($totalTasksUserByDatas);
    if(!empty($user)){
        $this->load->view('Reports/FunnelDetailsWithCluster',['uid'=>$uid,'user'=>$user,'sdate'=>$sdate,'edate'=>$edate,'sd'=>$sdate,'ed'=>$edate,'totalTasksUserByDatas'=>$totalTasksUserByDatas,'mtypes'=>$mtypes,'comming_user_types'=>$uyid,'mtypes'=>$mtypes,'target_uid'=>$target_uid,'dep_name'=>$dep_name]);
    }else{
        redirect('Menu/main');
    }
}
public function FunnelDetailsWithClusterID($mtypes,$target_uid,$clusterID,$userwise =NULL,$status = NULL){
    $user           = $this->session->userdata('user');
    if(empty($user) || empty($user['user_id'])){ redirect('login'); return; }
    // session guard added 28 May 2026 to fix HTTP 500 on null session
    $data['user']   = $user;
    $uid            = $user['user_id'];
    $uyid           =  $user['type_id'];
    $this->load->model('Menu_model');
    $dt=$this->Menu_model->get_utype($uyid);
    $dep_name = (is_array($dt) && !empty($dt) && isset($dt[0]->name)) ? $dt[0]->name : 'home';
    if(isset($_POST['sdate'])){
    $sdate = $_POST['sdate'];
    $edate = $_POST['edate'];
    }
    if(is_null($userwise)){
        $userwise = '';
    }
    $totalTasksUserByDatas   = $this->Report_model->GetFunnelDetailsWithClusterID($target_uid,$mtypes,$clusterID,$userwise,$status);
    // dd($totalTasksUserByDatas);
    if(!empty($user)){
        $this->load->view('Reports/FunnelDetailsWithCluster',['uid'=>$uid,'user'=>$user,'sdate'=>$sdate,'edate'=>$edate,'sd'=>$sdate,'ed'=>$edate,'totalTasksUserByDatas'=>$totalTasksUserByDatas,'mtypes'=>$mtypes,'comming_user_types'=>$uyid,'mtypes'=>$mtypes,'target_uid'=>$target_uid,'dep_name'=>$dep_name]);
    }else{
        redirect('Menu/main');
    }
}
public function ProposalDetails(){
    $user           = $this->session->userdata('user');
    if(empty($user) || empty($user['user_id'])){ redirect('login'); return; }
    // session guard added 28 May 2026 to fix HTTP 500 on null session
    $data['user']   = $user;
    $uid            = $user['user_id'];
    $uyid           =  $user['type_id'];
    $this->load->model('Menu_model');
    $dt=$this->Menu_model->get_utype($uyid);
    $dep_name = (is_array($dt) && !empty($dt) && isset($dt[0]->name)) ? $dt[0]->name : 'home';
    $admin_id_filter    = $this->input->Post('admin_id_filter');
    $rm_filter          = $this->input->Post('rm_filter');
    $task_action_id     = $this->input->Post('task_action');
    if(isset($_POST['sdate'])){
        $sdate                  = $this->input->Post('sdate');
        $edate                  = $this->input->Post('edate');
        if(isset($admin_id_filter)){
            if($admin_id_filter == 'all'){
                $admin_id_filter = 'all';
            }else{
                $uid = $admin_id_filter;
            }
            if(isset($rm_filter)){
                if($rm_filter =='all'){
                    $rm_filter = 'all';
                }else{
                    $uid = $rm_filter;
                }
            }
        }
    }else{
        $sdate              = date('Y-m-d');
        $edate              = date('Y-m-d');
        $rm_filter          = 'all';
        $task_action_id     = 'all';
        $admin_id_filter    = $uid;
    }
        if($rm_filter !=='all'){
           $uid = $rm_filter;
        }else{
          $uid = $admin_id_filter;
        }
        $task_action_id = 7;
        
        if($task_action_id == 'all'){
            $taskActionDatas =  'All';
        }else{
            $taskActionDatas =  $this->Menu_model->getTaskAction($task_action_id)[0]->name;
        }
  
        $aprtype = 1;
        // echo "uid =".$uid."<br/>";
        // echo "task_action_id =".$task_action_id."<br/>";
        // echo "admin_id_filter =".$admin_id_filter."<br/>";
        // echo "rm_filter =".$rm_filter."<br/>";
        // die;
        $getTotalTasks = $this->Report_model->GetProposalDetailbyDateByRoles($uid,$sdate,$edate,$task_action_id,$admin_id_filter,$rm_filter);
    // $getTotalTasks   = $this->Menu_model->GetTotalClusterTaskOnSelfOrOtherFunnelTask($uid,$sdate,$edate,$task_action_id,$admin_id_filter,$rm_filter);
    // dd($getTotalTasks);
    if(!empty($user)){
        $this->load->view('Reports/ProposalDetails',['uid'=>$uid,'user'=>$user,'user_tupe_id'=>$uyid,'sdate'=>$sdate,'edate'=>$edate,'task_action_id'=>$task_action_id,'taskActionDatas'=>$taskActionDatas,'task_action_id'=> $task_action_id,'admin_id_filter'=>$admin_id_filter,'rm_filter'=>$rm_filter,'dep_name'=>$dep_name,'getTotalTasks'=>$getTotalTasks]);
    }else{
        redirect('Menu/main');
    }
}
public function ProposalDetailsData($mtypes,$target_uid,$sdate,$edate,$task_action_id,$userwise =NULL){
    $user           = $this->session->userdata('user');
    if(empty($user) || empty($user['user_id'])){ redirect('login'); return; }
    // session guard added 28 May 2026 to fix HTTP 500 on null session
    $data['user']   = $user;
    $uid            = $user['user_id'];
    $uyid           =  $user['type_id'];
    $this->load->model('Menu_model');
    $comming_user_types = $uyid;
    $dt=$this->Menu_model->get_utype($uyid);
    $dep_name = (is_array($dt) && !empty($dt) && isset($dt[0]->name)) ? $dt[0]->name : 'home';
    if(isset($_POST['sdate'])){
    $sdate = $_POST['sdate'];
    $edate = $_POST['edate'];
    }
    if(is_null($userwise)){
        $userwise = '';
    }
    if($task_action_id == 'all'){
            $taskActionDatas =  'All';
    }else{
        $taskActionDatas =  $this->Menu_model->getTaskAction($task_action_id)[0]->name;
    }
    $totalTasksUserByDatas   = $this->Report_model->GetTeamProposalTaskListsDatas($target_uid,$sdate,$edate,$mtypes,$uid,$task_action_id,$userwise);
    // echo sizeof($totalTasksUserByDatas);
    // die;
    if(!empty($user)){
        $this->load->view('Reports/ProposalDetailsData',['uid'=>$uid,'user'=>$user,'sdate'=>$sdate,'edate'=>$edate,'sd'=>$sdate,'ed'=>$edate,'totalTasksUserByDatas'=>$totalTasksUserByDatas,'mtypes'=>$mtypes,'comming_user_types'=>$uyid,'mtypes'=>$mtypes,'target_uid'=>$target_uid,'taskActionDatas'=>$taskActionDatas,'dep_name'=>$dep_name,'comming_user_types'=>$comming_user_types]);
    }else{
        redirect('Menu/main');
    }
}
public function ViewProposalDetails($pid){
    $user           = $this->session->userdata('user');
    if(empty($user) || empty($user['user_id'])){ redirect('login'); return; }
    // session guard added 28 May 2026 to fix HTTP 500 on null session
    $data['user']   = $user;
    $uid            = $user['user_id'];
    $uyid           = $user['type_id'];
    $this->load->model('Menu_model');
    $dt             =  $this->Menu_model->get_utype($uyid);
    $dep_name = (is_array($dt) && !empty($dt) && isset($dt[0]->name)) ? $dt[0]->name : 'home';
 $comming_user_types = $uyid;
    $proposalDatas   = $this->Report_model->GetProposalDetaislByPID($pid);
  
    if(!empty($user)){
        $this->load->view('Reports/ViewProposalDetails',['uid'=>$uid,'dep_name'=>$dep_name,'user'=>$user,'pid'=>$pid,'proposalDatas'=>$proposalDatas,'comming_user_types'=>$comming_user_types]);
    }else{
        redirect('Menu/main');
    }
}
public function CheckPraposalApprovedOrRejectByManager(){
    $user               = $this->session->userdata('user');
    if(empty($user) || empty($user['user_id'])){ redirect('login'); return; }
    // session guard added 28 May 2026 to fix HTTP 500 on null session
    $data['user']       = $user;
    $uid                = $user['user_id'];
    $uyid               =  $user['type_id'];
    $this->load->model('Menu_model');
    $pid          = $this->input->post('pid');
 
    $proposalDatas  = $this->Report_model->GetProposalDetaislByPID($pid);
    $task_id        = $proposalDatas[0]->task_id;
    $req_taskid     = $task_id;
 
    $remarks      = $this->input->post('remarks');
    $btntext      = $this->input->post('btntext');  // Approved  Reject
    $updateddate  = date("Y-m-d H:i:s");
    if($btntext == 'Approved'){
        $reqtaskData          = $this->Menu_model->getTBLTaskByID($task_id);
        $aftertask            = $reqtaskData[0]->aftertask;
        $appointmentdatetime  = $reqtaskData[0]->appointmentdatetime;
        $actontaken           = $reqtaskData[0]->actontaken;
        $purpose_achieved     = $reqtaskData[0]->purpose_achieved;
        $user_id              = $reqtaskData[0]->user_id;
        $actiontype_id        = $reqtaskData[0]->actiontype_id;
        $purpose_id           = $reqtaskData[0]->purpose_id;
        $cid_id               = $reqtaskData[0]->cid_id;
        $reqCmpData           = $this->Menu_model->get_cmpbyinid($cid_id);
        $cmpidid              = $reqCmpData[0]->cmpid_id;
        $cstatus              = $reqCmpData[0]->cstatus;
        $praposalData         = $this->Menu_model->GetPraposalByID($pid);
        $praposaluser         = $praposalData[0]->user_id;
        $praposaltid          = $praposalData[0]->tid;
        $partner              = $praposalData[0]->partner;
        $noofsc               = $praposalData[0]->noofsc;
        $pbudgetme            = $praposalData[0]->pbudgetme;
        $propasal_types       = $praposalData[0]->propasal_types;
        $tomorrow             = new DateTime('tomorrow');
        $tomorrowtime         = $tomorrow->format('Y-m-d H:i:s');
     
        $data1 = [
            'lastCFID'              =>  $praposaltid,
            'nextCFID'              =>  0,
            'actontaken'            =>  'no',
            'purpose_achieved'      =>  'no',
            'nextCFID'              =>  0,
            'fwd_date'              =>  $updateddate,
            'appointmentdatetime'   =>  $tomorrowtime,
            'actiontype_id'         =>  2,
            'auto_plan'             =>  1,
            'assignedto_id'         =>  $praposaluser,
            'user_id'               =>  $praposaluser,
            'purpose_id'            =>  26,
            'autotask'              =>  '0',
            'plan'                  =>  '1',
            'remarks'               =>  $remarks,
            'approved_status'       =>  1,
            'approved_by'           =>  $uid,
            'cid_id'                =>  $cid_id,
            'status_id'             =>  $cstatus,
            'selectby'              =>  "Task Create After Proposal Approved",
            'aftertask'             =>  $task_id,
            'assignedto_by'         =>  $uid
        ];
        $datainsert         = $this->db->insert('tblcallevents',$data1);
        $ntid               =  $this->db->insert_id($datainsert);
        $query2   =  $this->db->query("UPDATE `proposal` SET `aprby`='$uid',`remark`='$remarks',`apr`='1',`apr_date`='$updateddate',`aprdatet`='$updateddate',`new_task_after_check`='$ntid' WHERE id = $pid");
        if($propasal_types == 'Customize'){
            $praposaluserData = $this->Menu_model->get_userbyid($praposaluser);
            $praposalusername = $praposaluserData[0]->name;
            $praposaluser_sales_co = $praposaluserData[0]->sales_co;
            if($praposaluser_sales_co !== 0){
            $taskquery     =  $this->db->query("SELECT * FROM `tblcallevents` WHERE id = '$praposaltid'");
            $taskqueryData =  $taskquery->result();
            $tbl_task_cid  =  $taskqueryData[0]->cid_id;
            $taskcquery      =  $this->db->query("SELECT * FROM `init_call` WHERE id = '$tbl_task_cid'");
            $taskcqueryData  =  $taskcquery->result();
            $cmp_cstatus     =  $taskcqueryData[0]->cstatus;
            $remarks = $praposalusername.','. "Dear Sir, please send the customized proposal for 'this $partner' partner.";
            $current_appointmentdatetime = date("Y-m-d H:i:s");
            $data = array(
                'lastCFID'              => '0',
                'nextCFID'              => '0',
                'draft'                 => 'customized proposal',
                'fwd_date'              => $current_appointmentdatetime,
                'actontaken'            => 'no',
                'purpose_achieved'      => 'no',
                'nextaction'            => '7',
                'appointmentdatetime'   => $current_appointmentdatetime,
                'actiontype_id'         => '7',
                'assignedto_id'         => $praposaluser_sales_co,
                'cid_id'                => $tbl_task_cid,
                'purpose_id'            => '91',
                'remarks'               => $remarks,
                'status_id'             => $cmp_cstatus,
                'user_id'               => $praposaluser_sales_co,
                'date'                  => $current_appointmentdatetime,
                'plan'                  => '1',
                'selectby'              => "Send the customized proposal as requested by $praposalusername",
                'filter_by'             => "Send the customized proposal as requested by $praposalusername",
                'approved_status'       => 1,
                'approved_by'           => $praposaluser,
                'comment_by'            => $praposaluser,
                'comments'              => $remarks,
                'assignedto_by'         => $praposaluser,
                'aftertask'             => $praposaltid
            );
            $this->db->insert('tblcallevents', $data);
        }
        }
    }
    if($btntext == 'Reject'){
        $reqtaskData          = $this->Menu_model->getTBLTaskByID($req_taskid);
        $aftertask            = $reqtaskData[0]->aftertask;
        $appointmentdatetime  = $reqtaskData[0]->appointmentdatetime;
        $actontaken           = $reqtaskData[0]->actontaken;
        $purpose_achieved     = $reqtaskData[0]->purpose_achieved;
        $user_id              = $reqtaskData[0]->user_id;
        $actiontype_id        = $reqtaskData[0]->actiontype_id;
        $purpose_id           = $reqtaskData[0]->purpose_id;
        $cid_id               = $reqtaskData[0]->cid_id;
        $reqCmpData           = $this->Menu_model->get_cmpbyinid($cid_id);
        $cmpidid              = $reqCmpData[0]->cmpid_id;
        $cstatus              = $reqCmpData[0]->cstatus;
        $praposalData         = $this->Menu_model->GetPraposalByID($pid);
        $praposaluser         = $praposalData[0]->user_id;
        $praposaltid          = $praposalData[0]->tid;
        $tomorrow             = new DateTime('tomorrow');
        $tomorrowtime         = $tomorrow->format('Y-m-d 10:00:00');
        $pquery               = $this->db->query("SELECT * FROM `tblcallevents` WHERE (user_id = '$praposaluser' AND assignedto_id = '$praposaluser') AND nextCFID =0 AND cid_id = '$cid_id' AND (approved_status = '' OR approved_status = 1)");
        $propasalTaskDatas    =  $pquery->result();
        if(sizeof($propasalTaskDatas) == 0){
             $data1 = [
            'lastCFID'              =>  $praposaltid,
            'nextCFID'              =>  0,
            'actontaken'            =>  'no',
            'purpose_achieved'      =>  'no',
            'nextCFID'              =>  0,
            'fwd_date'              =>  $updateddate,
            'appointmentdatetime'   =>  $tomorrowtime,
            'actiontype_id'         =>  7,
            'auto_plan'             =>  1,
            'assignedto_id'         =>  $praposaluser,
            'user_id'               =>  $praposaluser,
            'purpose_id'            =>  91,
            'autotask'              =>  '0',
            'plan'                  =>  '1',
            'remarks'               =>  $remarks,
            'approved_status'       =>  1,
            'approved_by'           =>  $uid,
            'cid_id'                =>  $cid_id,
            'status_id'             =>  $cstatus,
            'selectby'              =>  "Task Create After Proposal Rejected",
            'aftertask'             =>  $task_id,
            'assignedto_by'         =>  $uid
        ];
        $datainsert         = $this->db->insert('tblcallevents',$data1);
        $ntid               =  $this->db->insert_id($datainsert);
        }else{
            $ntid = 0;
        }
        $query1             =  $this->db->query("UPDATE `tblcallevents` SET `nextCFID`='$task_id',`actontaken`='yes',`purpose_achieved`='no',`comment_by`='$uid',`comments`='$remarks' WHERE id= $task_id");
        $query2             =  $this->db->query("UPDATE `proposal` SET `aprby`='$uid',`remark`='$remarks',`apr`='2',`apr_date`='$updateddate', `aprdatet`='$updateddate',`new_task_after_check`='$ntid' WHERE id = $pid");
    }
    $this->load->library('session');
    $this->session->set_flashdata('success_message', "After Check Proposal $btntext Successfully.");
    redirect('Reports/ViewProposalDetails/'.$pid);
}
// End Praposal Check || Handover Check
public function WorkAfterProposalApprovedReject($pid){
    $user           = $this->session->userdata('user');
    if(empty($user) || empty($user['user_id'])){ redirect('login'); return; }
    // session guard added 28 May 2026 to fix HTTP 500 on null session
    $data['user']   = $user;
    $uid            = $user['user_id'];
    $uyid           =  $user['type_id'];
    $this->load->model('Menu_model');
    $dt=$this->Menu_model->get_utype($uyid);
    $dep_name = (is_array($dt) && !empty($dt) && isset($dt[0]->name)) ? $dt[0]->name : 'home';
    $proposalDatas  = $this->Report_model->GetProposalDetaislByPID($pid);
    $init_call_id   =  $proposalDatas[0]->init_call_id;
    $propasal_apr_date   =  $proposalDatas[0]->propasal_apr_date;
   
    $totalTasksUserByDatas   = $this->Report_model->GetWorkAfterProposalApprovedOrRejectByPID($pid,$init_call_id,$propasal_apr_date);
    // dd($totalTasksUserByDatas);
    if(!empty($user)){
        $this->load->view('Reports/WorkAfterProposalApprovedReject',['uid'=>$uid,'user'=>$user,'pid'=>$pid,'proposalDatas'=>$proposalDatas,'totalTasksUserByDatas'=>$totalTasksUserByDatas,'comming_user_types'=>$uyid,'dep_name'=>$dep_name]);
    }else{
        redirect('Menu/main');
    }
}
public function MeetingDoneProposalOverviewClosureStatus(){
    $user           = $this->session->userdata('user');
    if(empty($user) || empty($user['user_id'])){ redirect('login'); return; }
    // session guard added 28 May 2026 to fix HTTP 500 on null session
    $data['user']   = $user;
    $uid            = $user['user_id'];
    $uyid           =  $user['type_id'];
    $this->load->model('Menu_model');
    $dt = $this->Menu_model->get_utype($uyid);
    $dep_name = (is_array($dt) && !empty($dt) && isset($dt[0]->name)) ? $dt[0]->name : 'home';
    $admin_id_filter    = $this->input->Post('admin_id_filter');
    $rm_filter          = $this->input->Post('rm_filter');
    if(isset($_POST['sdate'])){
        $sdate                  = $this->input->Post('sdate');
        $edate                  = $this->input->Post('edate');
        if(isset($admin_id_filter)){
            if($admin_id_filter == 'all'){
                $admin_id_filter = 'all';
            }else{
                $uid = $admin_id_filter;
            }
            if(isset($rm_filter)){
                if($rm_filter =='all'){
                    $rm_filter = 'all';
                }else{
                    $uid = $rm_filter;
                }
            }
        }
    }else{
        $sdate              = date('Y-m-d');
        $edate              = date('Y-m-d');
        $rm_filter          = 'all';
        $admin_id_filter    = $uid;
    }
    if($rm_filter !=='all'){
           $uid = $rm_filter;
    }else{
          $uid = $admin_id_filter;
    }
  
  $totalMeetingsUserByDatas   = $this->Menu_model->GetTotalMeetingDoneProposalDoneOrNot($uid,$sdate,$edate,$admin_id_filter,$rm_filter);



    // echo $this->db->last_query();
    // die;
    if(!empty($user)){
        $this->load->view('Reports/MeetingDoneProposalOverviewClosureStatus',[
            'uid'=>$uid,
            'user'=>$user,
            'sdate'=>$sdate,
            'edate'=>$edate,
            'sd'=>$sdate,
            'ed'=>$edate,
            'admin_id_filter'=>$admin_id_filter,
            'rm_filter'=>$rm_filter,
            'dep_name'=>$dep_name,
            'totalMeetingsUserByDatas'=>$totalMeetingsUserByDatas
        ]);
    }else{
        redirect('Menu/main');
    }
}
public function MeetingDoneProposalOverviewClosureStatusLists($mtypes,$target_uid,$sdate,$edate,$userwise =NULL){
    $user           = $this->session->userdata('user');
    if(empty($user) || empty($user['user_id'])){ redirect('login'); return; }
    // session guard added 28 May 2026 to fix HTTP 500 on null session
    $data['user']   = $user;
    $uid            = $user['user_id'];
    $uyid           =  $user['type_id'];
    $this->load->model('Menu_model');
    $dt=$this->Menu_model->get_utype($uyid);
    $dep_name = (is_array($dt) && !empty($dt) && isset($dt[0]->name)) ? $dt[0]->name : 'home';
    if(isset($_POST['sdate'])){
    $sdate = $_POST['sdate'];
    $edate = $_POST['edate'];
    }
    if(is_null($userwise)){
        $userwise = '';
    }
    $totalMeetingsUserByDatas   = $this->Menu_model->TotalMeetingProposalDetailsDatasNewUpdate($target_uid,$sdate,$edate,$mtypes,$uid,$userwise);

    // echo $this->db->last_query();
    // die;
    
    if(!empty($user)){
        $this->load->view('Reports/MeetingDoneProposalOverviewClosureStatusLists',['uid'=>$uid,'user'=>$user,'sdate'=>$sdate,'edate'=>$edate,'sd'=>$sdate,'ed'=>$edate,'totalMeetingsUserByDatas'=>$totalMeetingsUserByDatas,'mtypes'=>$mtypes,'comming_user_types'=>$uyid,'mtypes'=>$mtypes,'target_uid'=>$target_uid,'dep_name'=>$dep_name]);
    }else{
        redirect('Menu/main');
    }
}
public function MeetingDoneProposalOverviewClosureStatusListsAfterReview($mtypes,$target_uid,$sdate,$edate,$checkstartrDate){
    $user           = $this->session->userdata('user');
    if(empty($user) || empty($user['user_id'])){ redirect('login'); return; }
    // session guard added 28 May 2026 to fix HTTP 500 on null session
    $data['user']   = $user;
    $uid            = $user['user_id'];
    $uyid           =  $user['type_id'];
    $this->load->model('Menu_model');
    $dt=$this->Menu_model->get_utype($uyid);
    $dep_name = (is_array($dt) && !empty($dt) && isset($dt[0]->name)) ? $dt[0]->name : 'home';
    if(isset($_POST['sdate'])){
    $sdate = $_POST['sdate'];
    $edate = $_POST['edate'];
    }
    if(is_null($userwise)){
        $userwise = '';
    }
    $totalMeetingsUserByDatas   = $this->Menu_model->TotalMeetingProposalDetailsDatasAfterReview($target_uid,$sdate,$edate,$mtypes,$uid,$checkstartrDate,$userwise);

      if(isset($_POST['sdate'])){
    
        }else{
            $sdate  = $checkstartrDate;
        }
    if(!empty($user)){
        $this->load->view('Reports/MeetingDoneProposalOverviewClosureStatusLists',['uid'=>$uid,'user'=>$user,'sdate'=>$sdate,'edate'=>$edate,'sd'=>$sdate,'ed'=>$edate,'totalMeetingsUserByDatas'=>$totalMeetingsUserByDatas,'mtypes'=>$mtypes,'comming_user_types'=>$uyid,'mtypes'=>$mtypes,'target_uid'=>$target_uid,'dep_name'=>$dep_name]);
    }else{
        redirect('Menu/main');
    }
}
public function IndiaLocationDetails(){
    $user           = $this->session->userdata('user');
    $data['user']   = $user;
    $uid            = $user['user_id'];
    $uyid           =  $user['type_id'];
    $this->load->model('Menu_model');
     if(empty($user)){ redirect('Menu/main');}
    $dt=$this->Menu_model->get_utype($uyid);
    $dep_name = (is_array($dt) && !empty($dt) && isset($dt[0]->name)) ? $dt[0]->name : 'home';
    $getLocations = $this->Report_model->GetTotalLocations();
    $this->load->view('Reports/IndiaLocationDetails',[
            'uid'           => $uid,
            'user'          => $user,
            'dep_name'      => $dep_name,
            'getLocations'  => $getLocations
    ]);
}
public function AddNewState(){
    $user           = $this->session->userdata('user');
    $data['user']   = $user;
    $uid            = $user['user_id'];
    $uyid           =  $user['type_id'];
    $this->load->model('Menu_model');
    if(empty($user)){ redirect('Menu/main');}
    $new_state_name = $this->input->post('new_state_name');
    if($new_state_name !== ''){
        $data = [
            'state_title' => "$new_state_name",
            'status' => 'Active'
        ];
        $this->db->insert('in_state', $data);
        $insert_id = $this->db->insert_id();
        if ($insert_id) {
              $this->session->set_flashdata('success_message'," New State ($new_state_name) Added Successfully.");
                redirect('Reports/IndiaLocationDetails');
        } else {
            $this->session->set_flashdata('error_message',' * State Not Added ');
            redirect('Reports/IndiaLocationDetails');
        }
    }
}
public function AddNewDistrict(){
    $user           = $this->session->userdata('user');
    $data['user']   = $user;
    $uid            = $user['user_id'];
    $uyid           =  $user['type_id'];
    $this->load->model('Menu_model');
    if(empty($user)){ redirect('Menu/main');}
    $in_state_id        = $this->input->post('in_state_id');
    $new_district_name  = $this->input->post('new_district_name');
    if($in_state_id !== ''){
        
        $data = array(
            'district_title'       => $new_district_name,  
            'state_id'             => $in_state_id , 
            'district_status'      => 'Active' 
        );
        $this->db->insert('in_district', $data);
        $insert_id = $this->db->insert_id();
        if ($insert_id) {
              $this->session->set_flashdata('success_message'," New District ( $new_district_name ) Added Successfully.");
                redirect('Reports/IndiaLocationDetails');
        } else {
            $this->session->set_flashdata('error_message',' * District Not Added ');
            redirect('Reports/IndiaLocationDetails');
        }
    }
}
public function AddNewCity(){
    $user           = $this->session->userdata('user');
    $data['user']   = $user;
    $uid            = $user['user_id'];
    $uyid           =  $user['type_id'];
    $this->load->model('Menu_model');
    if(empty($user)){ redirect('Menu/main');}
    $in_state_id        = $this->input->post('in_state_id');
    $in_district_id     = $this->input->post('in_district_id');
    $new_city_name      = $this->input->post('new_city_name');
 
    if($in_state_id !== ''){
        
        $data = array(
            'name'        => $new_city_name,
            'districtid'  => $in_district_id,
            'state_id'    => $in_state_id,
            'status'      => 'Active'
        );
        $this->db->insert('in_city', $data);
        $insert_id = $this->db->insert_id();
        if ($insert_id) {
            $this->session->set_flashdata('success_message'," New City ( $new_city_name ) Added Successfully.");
            redirect('Reports/IndiaLocationDetails');
        } else {
            $this->session->set_flashdata('error_message',' * City Not Added ');
            redirect('Reports/IndiaLocationDetails');
        }
    }
}
public function IndiaLocationDetailsByData($keys){
    $user           = $this->session->userdata('user');
    $data['user']   = $user;
    $uid            = $user['user_id'];
    $uyid           =  $user['type_id'];
    $this->load->model('Menu_model');
     if(empty($user)){ redirect('Menu/main');}
    $dt=$this->Menu_model->get_utype($uyid);
    $dep_name = (is_array($dt) && !empty($dt) && isset($dt[0]->name)) ? $dt[0]->name : 'home';
    $getLocations = $this->Report_model->GetTotalLocationsBYKeysPoint($keys);
    // dd($getLocations);
    $this->load->view('Reports/IndiaLocationDetailsByData',[
            'uid'           => $uid,
            'user'          => $user,
            'dep_name'      => $dep_name,
            'getLocations'  => $getLocations,
            'keys'          => $keys
    ]);
}
public function IndiaLocationDetailsByDatas($keys,$ids){
    $user           = $this->session->userdata('user');
    $data['user']   = $user;
    $uid            = $user['user_id'];
    $uyid           =  $user['type_id'];
    $this->load->model('Menu_model');
     if(empty($user)){ redirect('Menu/main');}
    $dt=$this->Menu_model->get_utype($uyid);
    $dep_name = (is_array($dt) && !empty($dt) && isset($dt[0]->name)) ? $dt[0]->name : 'home';
    $getLocations = $this->Report_model->GetTotalLocationsBYKeysPoints($keys,$ids);
    // dd($getLocations);
    $this->load->view('Reports/IndiaLocationDetailsByDatas',[
            'uid'           => $uid,
            'user'          => $user,
            'dep_name'      => $dep_name,
            'getLocations'  => $getLocations,
            'keys'          => $keys
    ]);
}
public function FutureTask(){
    $user           = $this->session->userdata('user');
    if(empty($user) || empty($user['user_id'])){ redirect('login'); return; }
    // session guard added 28 May 2026 to fix HTTP 500 on null session
    $data['user']   = $user;
    $uid            = $user['user_id'];
    $uyid           =  $user['type_id'];
    $this->load->model('Menu_model');
    $dt             =  $this->Menu_model->get_utype($uyid);
    $dep_name = (is_array($dt) && !empty($dt) && isset($dt[0]->name)) ? $dt[0]->name : 'home';
    $admin_id_filter    = $this->input->Post('admin_id_filter');
    $rm_filter          = $this->input->Post('rm_filter');
    if(isset($_POST['admin_id_filter'])){
        $sdate                  = $this->input->Post('sdate');
        $edate                  = $this->input->Post('edate');
        if(isset($admin_id_filter)){
            if($admin_id_filter == 'all'){
                $admin_id_filter = 'all';
            }else{
                $uid = $admin_id_filter;
            }
            if(isset($rm_filter)){
                if($rm_filter =='all'){
                    $rm_filter = 'all';
                }else{
                    $uid = $rm_filter;
                }
            }
        }
    }else{
        $sdate              = date('Y-m-d');
        $edate              = date('Y-m-d');
        $rm_filter          = 'all';
        $admin_id_filter    = $uid;
    }
        if($rm_filter !=='all'){
           $uid = $rm_filter;
        }else{
          $uid = $admin_id_filter;
        }
    if(isset($_POST['category'])){
        $category   = $_POST['category'];
    }else{
        $category = '';
        }
    $futuretasks = $this->Report_model->GetOurFutureTaskListsByRoles($uid,$admin_id_filter,$rm_filter);
    $this->load->view('Reports/FutureTask',[
        'user'=>$user,
        'uid'=>$uid,
        'category'=>$category,
        'dep_name'=>$dep_name,
        'futuretasks'=>$futuretasks,
        'admin_id_filter'=>$admin_id_filter,
        'rm_filter'=>$rm_filter,
        'sdate'=>$sdate,
        'edate'=>$edate,
    ]);
}
    public function DayManagementChecking(){
        $user               = $this->session->userdata('user');
        $data['user']       = $user;
        $uid                = $user['user_id'];
        $uyid               =  $user['type_id'];
        if(empty($user)){redirect('Menu/main');}
        $this->load->model('Menu_model');
        
        $dt                 = $this->Menu_model->get_utype($uyid);
        $dep_name = (is_array($dt) && !empty($dt) && isset($dt[0]->name)) ? $dt[0]->name : 'home';
            $userwise = '';
            $admin_id_filter    = $this->input->Post('admin_id_filter');
            $rm_filter          = $this->input->Post('rm_filter');
            $rm_input          = $this->input->Post('rm_input');
            if(isset($_POST['admin_id_filter'])){
                $sdate                  = $this->input->Post('sdate');
                $edate                  = $this->input->Post('edate');
                if(isset($admin_id_filter)){
                    if($admin_id_filter == 'all'){
                        $admin_id_filter = 'all';
                    }else{
                        $uid = $admin_id_filter;
                    }
                    if(isset($rm_filter)){
                        if($rm_filter =='all'){
                            $rm_filter = 'all';
                        }else{
                            $uid = $rm_filter;
                        }
                    }
                }
                
            }else{
                $sdate              = date('Y-m-d');
                $edate              = date('Y-m-d');
               
                // $edate      =  $sdate;
                $rm_filter          = 'all';
                $admin_id_filter    = $uid;
                // $date = new DateTime($sdate);
                // $date->modify('-1 day');
                // $sdate = $date->format('Y-m-d');
                
                 $sdate      =  $this->Menu_model->findSpecialDate($sdate);
            }
                if($rm_filter !=='all'){
                $uid = $rm_filter;
                }else{
                $uid = $admin_id_filter;
                }
                // $sdate = "2025-08-02";
             
                
            $edate = $sdate;
            if(isset($_POST['rm_filter'])){
                 if($rm_filter !== 'all'){
                      if($rm_input == 'Team Wise Input'){
                      }else if($rm_input == 'User Wise Input'){
                        $userwise = 'User_Wise';
                      }
                      $liveDayManagement        = $this->Report_model->DayManagementCheckingDatas($rm_filter,$userwise,$sdate);
                      $userWholeDayActivity     = $this->Report_model->UserWholDayActivity($rm_filter,$userwise,$sdate);
                    }else if($rm_filter == 'all'){
                      $liveDayManagement        = $this->Report_model->DayManagementCheckingDatas($rm_filter,$userwise,$sdate);
                      $userWholeDayActivity     = $this->Report_model->UserWholDayActivity($rm_filter,$userwise,$sdate);
                    }
                }else{
                     $liveDayManagement         = $this->Report_model->DayManagementCheckingDatas($admin_id_filter,$userwise,$sdate);
                     $userWholeDayActivity      = $this->Report_model->UserWholDayActivity($admin_id_filter,$userwise,$sdate);
                      
                }
        $this->load->view('Functions/DayManagementChecking',[
        'uid'               =>  $uid,
        'user'              =>  $user,
        'dep_name'          =>  $dep_name,
        'liveDayManagement' =>  $liveDayManagement,
        'admin_id_filter'   => $admin_id_filter,
        'rm_filter'         => $rm_filter,
        'sdate'             =>$sdate,
        'userWholeDayActivity' =>$userWholeDayActivity
        ]
    );
    }
    public function DayStartLive(){
        $user               = $this->session->userdata('user');
        $data['user']       = $user;
        $uid                = $user['user_id'];
        $uyid               =  $user['type_id'];
        if(empty($user)){redirect('Menu/main');}
        $this->load->model('Menu_model');
        $dt                 = $this->Menu_model->get_utype($uyid);
        $dep_name = (is_array($dt) && !empty($dt) && isset($dt[0]->name)) ? $dt[0]->name : 'home';
            $userwise = '';
            $admin_id_filter    = $this->input->Post('admin_id_filter');
            $rm_filter          = $this->input->Post('rm_filter');
            $rm_input          = $this->input->Post('rm_input');
            if(isset($_POST['admin_id_filter'])){
                $sdate                  = $this->input->Post('sdate');
                $edate                  = $this->input->Post('edate');
                if(isset($admin_id_filter)){
                    if($admin_id_filter == 'all'){
                        $admin_id_filter = 'all';
                    }else{
                        $uid = $admin_id_filter;
                    }
                    if(isset($rm_filter)){
                        if($rm_filter =='all'){
                            $rm_filter = 'all';
                        }else{
                            $uid = $rm_filter;
                        }
                    }
                }
            }else{
                $sdate              = date('Y-m-d');
                $edate              = date('Y-m-d');
                $rm_filter          = 'all';
                $admin_id_filter    = $uid;
            }
                if($rm_filter !=='all'){
                $uid = $rm_filter;
                }else{
                $uid = $admin_id_filter;
                }
                // $sdate = "2025-08-02";
             
            $edate = $sdate;
            if(isset($_POST['rm_filter'])){
                 if($rm_filter !== 'all'){
                      if($rm_input == 'Team Wise Input'){
                      }else if($rm_input == 'User Wise Input'){
                        $userwise = 'User_Wise';
                      }
                      $liveDayManagement        = $this->Report_model->DayManagementCheckingDatas($rm_filter,$userwise,$sdate);
                      $userWholeDayActivity     = $this->Report_model->UserWholDayActivity($rm_filter,$userwise,$sdate);
                    }else if($rm_filter == 'all'){
                      $liveDayManagement        = $this->Report_model->DayManagementCheckingDatas($rm_filter,$userwise,$sdate);
                      $userWholeDayActivity     = $this->Report_model->UserWholDayActivity($rm_filter,$userwise,$sdate);
                    }
                }else{
                     $liveDayManagement         = $this->Report_model->DayManagementCheckingDatas($admin_id_filter,$userwise,$sdate);
                     $userWholeDayActivity      = $this->Report_model->UserWholDayActivity($admin_id_filter,$userwise,$sdate);
                }
                // dd($userWholeDayActivity);
        $this->load->view('Functions/DayStartLive',[
        'uid'               =>  $uid,
        'user'              =>  $user,
        'dep_name'          =>  $dep_name,
        'liveDayManagement' =>  $liveDayManagement,
        'admin_id_filter'   => $admin_id_filter,
        'rm_filter'         => $rm_filter,
        'sdate'             =>$sdate,
        'userWholeDayActivity' =>$userWholeDayActivity
        ]
    );
    }
    public function DayEndLive(){
        $user               = $this->session->userdata('user');
        $data['user']       = $user;
        $uid                = $user['user_id'];
        $uyid               =  $user['type_id'];
        if(empty($user)){redirect('Menu/main');}
        $this->load->model('Menu_model');
        $dt                 = $this->Menu_model->get_utype($uyid);
        $dep_name = (is_array($dt) && !empty($dt) && isset($dt[0]->name)) ? $dt[0]->name : 'home';
            $userwise = '';
            $admin_id_filter    = $this->input->Post('admin_id_filter');
            $rm_filter          = $this->input->Post('rm_filter');
            $rm_input          = $this->input->Post('rm_input');
            if(isset($_POST['admin_id_filter'])){
                $sdate                  = $this->input->Post('sdate');
                $edate                  = $this->input->Post('edate');
                if(isset($admin_id_filter)){
                    if($admin_id_filter == 'all'){
                        $admin_id_filter = 'all';
                    }else{
                        $uid = $admin_id_filter;
                    }
                    if(isset($rm_filter)){
                        if($rm_filter =='all'){
                            $rm_filter = 'all';
                        }else{
                            $uid = $rm_filter;
                        }
                    }
                }
            }else{
                $sdate              = date('Y-m-d');
                $edate              = date('Y-m-d');
                $rm_filter          = 'all';
                $admin_id_filter    = $uid;
            }
                if($rm_filter !=='all'){
                $uid = $rm_filter;
                }else{
                $uid = $admin_id_filter;
                }
                // $sdate = "2025-08-02";
             
            $edate = $sdate;
            $currentDate    = $sdate;
            $tommrowdate    = date('Y-m-d', strtotime($currentDate . ' +1 day'));
            $plannerDate    = $this->Report_model->checkNextPlannerDate($tommrowdate);
            if(isset($_POST['rm_filter'])){
                 if($rm_filter !== 'all'){
                      if($rm_input == 'Team Wise Input'){
                      }else if($rm_input == 'User Wise Input'){
                        $userwise = 'User_Wise';
                      }
                      $liveDayManagement        = $this->Report_model->DayendManagementCheckingDatas($rm_filter,$userwise,$sdate,$plannerDate);
                      $userWholeDayActivity     = $this->Report_model->UserWholDayENDActivity($rm_filter,$userwise,$sdate,$plannerDate);
                       
                    }else if($rm_filter == 'all'){
                      $liveDayManagement        = $this->Report_model->DayendManagementCheckingDatas($rm_filter,$userwise,$sdate,$plannerDate);
                      $userWholeDayActivity     = $this->Report_model->UserWholDayENDActivity($rm_filter,$userwise,$sdate,$plannerDate);
                    //   echo $this->db->last_query();
                    }
                }else{
                     $liveDayManagement         = $this->Report_model->DayendManagementCheckingDatas($admin_id_filter,$userwise,$sdate,$plannerDate);
                    
                     $userWholeDayActivity      = $this->Report_model->UserWholDayENDActivity($admin_id_filter,$userwise,$sdate,$plannerDate);
                     
                }
                // dd($userWholeDayActivity);
        $this->load->view('Functions/DayEndLive',[
        'uid'               =>  $uid,
        'user'              =>  $user,
        'dep_name'          =>  $dep_name,
        'liveDayManagement' =>  $liveDayManagement,
        'admin_id_filter'   => $admin_id_filter,
        'rm_filter'         => $rm_filter,
        'sdate'             =>$sdate,
        'userWholeDayActivity' =>$userWholeDayActivity,
        'plannerDate'       =>$plannerDate
        ]
    );
    }
    public function TaskPlannerLive(){
        $user               = $this->session->userdata('user');
        $data['user']       = $user;
        $uid                = $user['user_id'];
        $uyid               =  $user['type_id'];
        if(empty($user)){redirect('Menu/main');}
        $this->load->model('Menu_model');
        $dt                 = $this->Menu_model->get_utype($uyid);
        $dep_name = (is_array($dt) && !empty($dt) && isset($dt[0]->name)) ? $dt[0]->name : 'home';
            $userwise = '';
            $admin_id_filter    = $this->input->Post('admin_id_filter');
            $rm_filter          = $this->input->Post('rm_filter');
            $rm_input          = $this->input->Post('rm_input');
            if(isset($_POST['admin_id_filter'])){
                $sdate                  = $this->input->Post('sdate');
                $edate                  = $this->input->Post('edate');
                if(isset($admin_id_filter)){
                    if($admin_id_filter == 'all'){
                        $admin_id_filter = 'all';
                    }else{
                        $uid = $admin_id_filter;
                    }
                    if(isset($rm_filter)){
                        if($rm_filter =='all'){
                            $rm_filter = 'all';
                        }else{
                            $uid = $rm_filter;
                        }
                    }
                }
            }else{
                $sdate              = date('Y-m-d');
                $edate              = date('Y-m-d');
                $rm_filter          = 'all';
                $admin_id_filter    = $uid;
            }
                if($rm_filter !=='all'){
                $uid = $rm_filter;
                }else{
                $uid = $admin_id_filter;
                }
                // $sdate = "2025-08-02";
             
            $edate = $sdate;
            $currentDate    = $sdate;
            $tommrowdate    = date('Y-m-d', strtotime($currentDate . ' +1 day'));
            $plannerDate    = $this->Report_model->checkNextPlannerDate($tommrowdate);
            if(isset($_POST['rm_filter'])){
                 if($rm_filter !== 'all'){
                      if($rm_input == 'Team Wise Input'){
                      }else if($rm_input == 'User Wise Input'){
                        $userwise = 'User_Wise';
                      }
                      $liveDayManagement        = $this->Report_model->DayendManagementCheckingDatas($rm_filter,$userwise,$sdate,$plannerDate);
                      $userWholeDayActivity     = $this->Report_model->UserWholDayENDActivity($rm_filter,$userwise,$sdate,$plannerDate);
                       
                    }else if($rm_filter == 'all'){
                      $liveDayManagement        = $this->Report_model->DayendManagementCheckingDatas($rm_filter,$userwise,$sdate,$plannerDate);
                      $userWholeDayActivity     = $this->Report_model->UserWholDayENDActivity($rm_filter,$userwise,$sdate,$plannerDate);
                    //   echo $this->db->last_query();
                    }
                }else{
                     $liveDayManagement         = $this->Report_model->DayendManagementCheckingDatas($admin_id_filter,$userwise,$sdate,$plannerDate);
                    
                     $userWholeDayActivity      = $this->Report_model->UserWholDayENDActivity($admin_id_filter,$userwise,$sdate,$plannerDate);
                     
                }
                // dd($userWholeDayActivity);
        $this->load->view('Functions/TaskPlannerLive',[
        'uid'               =>  $uid,
        'user'              =>  $user,
        'dep_name'          =>  $dep_name,
        'liveDayManagement' =>  $liveDayManagement,
        'admin_id_filter'   => $admin_id_filter,
        'rm_filter'         => $rm_filter,
        'sdate'             =>$sdate,
        'userWholeDayActivity' =>$userWholeDayActivity,
        'plannerDate'       =>$plannerDate
        ]
    );
    }
    public function TodaysTaskPlannerLive(){
        $user               = $this->session->userdata('user');
        $data['user']       = $user;
        $uid                = $user['user_id'];
        $uyid               =  $user['type_id'];
        if(empty($user)){redirect('Menu/main');}
        $this->load->model('Menu_model');
        $dt                 = $this->Menu_model->get_utype($uyid);
        $dep_name = (is_array($dt) && !empty($dt) && isset($dt[0]->name)) ? $dt[0]->name : 'home';
            $userwise = '';
            $admin_id_filter    = $this->input->Post('admin_id_filter');
            $rm_filter          = $this->input->Post('rm_filter');
            $rm_input          = $this->input->Post('rm_input');
            if(isset($_POST['admin_id_filter'])){
                $sdate                  = $this->input->Post('sdate');
                $edate                  = $this->input->Post('edate');
                if(isset($admin_id_filter)){
                    if($admin_id_filter == 'all'){
                        $admin_id_filter = 'all';
                    }else{
                        $uid = $admin_id_filter;
                    }
                    if(isset($rm_filter)){
                        if($rm_filter =='all'){
                            $rm_filter = 'all';
                        }else{
                            $uid = $rm_filter;
                        }
                    }
                }
            }else{
                $sdate              = date('Y-m-d');
                $edate              = date('Y-m-d');
                $rm_filter          = 'all';
                $admin_id_filter    = $uid;
            }
                if($rm_filter !=='all'){
                $uid = $rm_filter;
                }else{
                $uid = $admin_id_filter;
                }
                // $sdate = "2025-08-02";
             
                
            $edate = $sdate;
            $plannerDate = $sdate;
            // $currentDate    = $sdate;
            // $tommrowdate    = date('Y-m-d', strtotime($currentDate . ' +1 day'));
            // $plannerDate    = $this->Report_model->checkNextPlannerDate($tommrowdate);
            if(isset($_POST['rm_filter'])){
                 if($rm_filter !== 'all'){
                      if($rm_input == 'Team Wise Input'){
                      }else if($rm_input == 'User Wise Input'){
                        $userwise = 'User_Wise';
                      }
                      $liveDayManagement        = $this->Report_model->DayendManagementCheckingDatas($rm_filter,$userwise,$sdate,$plannerDate);
                      $userWholeDayActivity     = $this->Report_model->UserWholSameDayActivity($rm_filter,$userwise,$sdate,$plannerDate);
                       
                    }else if($rm_filter == 'all'){
                      $liveDayManagement        = $this->Report_model->DayendManagementCheckingDatas($rm_filter,$userwise,$sdate,$plannerDate);
                      $userWholeDayActivity     = $this->Report_model->UserWholSameDayActivity($rm_filter,$userwise,$sdate,$plannerDate);
                    //   echo $this->db->last_query();
                    }
                }else{
                  
                     $liveDayManagement         = $this->Report_model->DayendManagementCheckingDatas($admin_id_filter,$userwise,$sdate,$plannerDate);
                    
                     $userWholeDayActivity      = $this->Report_model->UserWholSameDayActivity($admin_id_filter,$userwise,$sdate,$plannerDate);
                     
                }
                // dd($userWholeDayActivity);
        $this->load->view('Functions/TodaysTaskPlannerLive',[
        'uid'               =>  $uid,
        'user'              =>  $user,
        'dep_name'          =>  $dep_name,
        'liveDayManagement' =>  $liveDayManagement,
        'admin_id_filter'   => $admin_id_filter,
        'rm_filter'         => $rm_filter,
        'sdate'             =>$sdate,
        'userWholeDayActivity' =>$userWholeDayActivity,
        'plannerDate'       =>$plannerDate
        ]
    );
    }
 public function UserDetails($targetUID){
        $user               = $this->session->userdata('user');
        $data['user']       = $user;
        $uid                = $user['user_id'];
        $uyid               =  $user['type_id'];
        
        if(empty($user)){redirect('Menu/main');}
        $this->load->model('Menu_model');
        $this->load->model('Report_model');
        $dt                 = $this->Menu_model->get_utype($uyid);
        $dep_name = (is_array($dt) && !empty($dt) && isset($dt[0]->name)) ? $dt[0]->name : 'home';
        $targetUserDatas    = $this->Menu_model->GetUserDetailsByUid($targetUID);
        
        if(empty($targetUserDatas)){redirect('Menu/Dashboard');}
        $curefinancialyear      = $this->Menu_model->getCurrentFinancialYearStart();
        $cmpTasksDatas          = $this->Menu_model->GetTaskOnCompanyByUserID($targetUID);
        $cmpReviewsDatas        = $this->Menu_model->GetAllReviewDatasOnCompanyByUserID($targetUID);
        $conversion             = 'positive_conversions';
        $cmpPositiveTaskDatas   = $this->Menu_model->GetTaskConversionByUserID($targetUID,$conversion);
        $conversion             = 'negative_conversions';
        $cmpNegativeTaskDatas   = $this->Menu_model->GetTaskConversionByUserID($targetUID,$conversion);
        $conversion             = 'other_conversions';
        $cmpOtherTaskDatas      = $this->Menu_model->GetTaskConversionByUserID($targetUID,$conversion);
        $targeteamUserDatas     = $this->Menu_model->GetUserTeamDetailsByUid($targetUID);
        $totalFunnnels          = $this->Report_model->getAllCompanyBYRollesMaiingByUid($targetUID);
        
        $curFinancialDate       = $this->Menu_model->getFinancialYearRange();
        $curFinancialStartDate  = $curFinancialDate['start_date'];
        $curTillDate            = date("Y-m-d");
     
        $meetingsUserByDatas    = $this->Menu_model->MeetingDetailsByRolesUidonUserPage($targetUID,$curFinancialStartDate,$curTillDate);
        $totalMeetingsUserByDatas   = $this->Menu_model->GetTotalMeetingDoneProposalDoneOrNotonUserPage($targetUID,$curFinancialStartDate,$curTillDate);
        $getTotalTasksFrequncy   = $this->Menu_model->GetTotalClusterTaskOnSelfOrOtherFunnelTaskFrequencyonUserPage($targetUID,$curFinancialStartDate,$curTillDate);
        // Group by role_name
        $groupedTeamDatas = [];
        foreach ($targeteamUserDatas as $item) {
            $role = $item->role_name ?: 'Unknown'; // fallback if role_name is empty
            $groupedTeamDatas[$role][ $item->user_id] = $item->name;
        }
        $sequence = [
            'SuperAdmin',
            'Admin',
            'AdminS',
            'RM East',
            'RM North',
            'RM',
            'Assistant Sales Head (NAE)',
            'Assistant Sales Head (W)',
            'Assistant Sales Head (S)',
            'Sales Coordinator',
            'PST',
            'BDPST',
            'Cluster Manager',
            'Assistant Cluster Manager',
            'Sales Person',
            'MailSupport',
            'ResearchSupport',
            'Project Coordinator',
            'Corporate Partnership Executive',
            'Executive Assistant',
            'Data Analysis',
        ];
       $teamSorted = [];
        foreach ($sequence as $role) {
            if (isset($groupedTeamDatas[$role])) {
                $teamSorted[$role] = $groupedTeamDatas[$role];
            }
        }
        $this->load->view('Functions/UserDetails',[
        'uid'                       => $uid,
        'user'                      => $user,
        'dep_name'                  => $dep_name,
        'targetUID'                 => $targetUID,
        'curefinancialyear'         => $curefinancialyear,
        'targetUserDatas'           => $targetUserDatas,
        'cmpTasksDatas'             =>  $cmpTasksDatas,
        'cmpReviewsDatas'           =>  $cmpReviewsDatas,
        'cmpPositiveTaskDatas'      =>  $cmpPositiveTaskDatas,
        'cmpNegativeTaskDatas'      =>  $cmpNegativeTaskDatas,
        'cmpOtherTaskDatas'         =>  $cmpOtherTaskDatas,
        'targeteamUserDatas'        =>  $targeteamUserDatas,
        'totalFunnnels'             =>  $totalFunnnels,
        'groupedTeamDatas'          =>  $teamSorted,
        'meetingsUserByDatas'       =>  $meetingsUserByDatas,
        'curFinancialStartDate'     =>  $curFinancialStartDate,
        'curTillDate'               =>  $curTillDate,
        'totalMeetingsUserByDatas'  =>  $totalMeetingsUserByDatas,
        'getTotalTasksFrequncy'     =>  $getTotalTasksFrequncy,
        ]
    );
    }
public function SameStatusSinceDays(){
    $user           = $this->session->userdata('user');
    if(empty($user) || empty($user['user_id'])){ redirect('login'); return; }
    // session guard added 28 May 2026 to fix HTTP 500 on null session
    $data['user']   = $user;
    $uid            = $user['user_id'];
    $uyid           =  $user['type_id'];
    $this->load->model('Menu_model');
     $this->load->model('Report_model');
    $dt = $this->Menu_model->get_utype($uyid);
    $dep_name = (is_array($dt) && !empty($dt) && isset($dt[0]->name)) ? $dt[0]->name : 'home';
    $admin_id_filter    = $this->input->Post('admin_id_filter');
    $rm_filter          = $this->input->Post('rm_filter');
    $days_filter        = $this->input->Post('days_filter');
    if(isset($_POST['days_filter'])){
        $sdate                  = $this->input->Post('sdate');
        $edate                  = $this->input->Post('edate');
        if(isset($admin_id_filter)){
            if($admin_id_filter == 'all'){
                $admin_id_filter = 'all';
            }else{
                $uid = $admin_id_filter;
            }
            if(isset($rm_filter)){
                if($rm_filter =='all'){
                    $rm_filter = 'all';
                }else{
                    $uid = $rm_filter;
                }
            }
        }
        if($days_filter !== ''){
            $edate              = date('Y-m-d');
            if($days_filter == '8 Days'){
                $sdate          = date('Y-m-d', strtotime('-8 days', strtotime($edate)));
                $days_inclusive = 8;
            }else if($days_filter == '15 Days'){
                $sdate          = date('Y-m-d', strtotime('-15 days', strtotime($edate)));
                $days_inclusive = 15;
            }else if($days_filter == '30 Days'){
                $sdate          = date('Y-m-d', strtotime('-30 days', strtotime($edate)));
                $days_inclusive = 30;
            }else if($days_filter == 'more than 30 Days'){
                $sdate          = date('Y-m-d', strtotime('-31 days', strtotime($edate)));
                $days_inclusive = 31;
            }else{
                $sdate          = date('Y-m-d', strtotime('-8 days', strtotime($edate)));
                $days_inclusive = 8;
            }   
        }
    }else{
        $sdate              = date('Y-m-d');
        $edate              = date('Y-m-d');
        $rm_filter          = 'all';
        $admin_id_filter    = $uid;
        $sdate = date('Y-m-d', strtotime('-8 days', strtotime($edate)));
        $days_inclusive = 8;
    }
        if($rm_filter !=='all'){
           $uid = $rm_filter;
        }else{
          $uid = $admin_id_filter;
        }
  
      
    $sameStatusSinceDaysDatas    = $this->Report_model->GetCompanySameStatusSinceDays($uid,$sdate,$edate,$admin_id_filter,$rm_filter,$days_inclusive);
    
    $statusCounts = [];
    foreach ($sameStatusSinceDaysDatas as $row) {
        $status = $row->current_status;
        if (!isset($statusCounts[$status])) {
            $statusCounts[$status] = 0;
        }
        $statusCounts[$status]++;
    }
   $mainBDCounts = [];
        foreach ($sameStatusSinceDaysDatas as $row) {
            $main_bd_name = $row->main_bd_name;
            if (!isset($mainBDCounts[$main_bd_name])) {
                $mainBDCounts[$main_bd_name] = 0;
            }
            $mainBDCounts[$main_bd_name]++;
        }
        $mainBDStatusCounts = [];
        foreach ($sameStatusSinceDaysDatas as $row) {
            $bd = $row->main_bd_name;
            $status = $row->current_status;
            if (!isset($mainBDStatusCounts[$bd])) {
                $mainBDStatusCounts[$bd] = [];
            }
            if (!isset($mainBDStatusCounts[$bd][$status])) {
                $mainBDStatusCounts[$bd][$status] = 0;
            }
            $mainBDStatusCounts[$bd][$status]++;
        }
    if(!empty($user)){
        $this->load->view('Reports/SameStatusSinceDays',[
            'uid'                       => $uid,
            'user'                      => $user,
            'sdate'                     => $sdate,
            'edate'                     => $edate,
            'sd'                        => $sdate,
            'ed'                        => $edate,
            'sameStatusSinceDaysDatas'  => $sameStatusSinceDaysDatas,
            'admin_id_filter'           => $admin_id_filter,
            'rm_filter'                 => $rm_filter,
            'dep_name'                  => $dep_name,
            'days_inclusive'            => $days_inclusive,
            'days_filter'               => $days_filter,
            'statusCounts'              => $statusCounts,
            'mainBDCounts'              => $mainBDCounts,
            'mainBDStatusCounts'        => $mainBDStatusCounts
        ]);
    }else{
        redirect('Menu/main');
    }
}
public function AfterAssignSameStatusSinceDays(){
    $user           = $this->session->userdata('user');
    if(empty($user) || empty($user['user_id'])){ redirect('login'); return; }
    // session guard added 28 May 2026 to fix HTTP 500 on null session
    $data['user']   = $user;
    $uid            = $user['user_id'];
    $uyid           =  $user['type_id'];
    $this->load->model('Menu_model');
     $this->load->model('Report_model');
    $dt = $this->Menu_model->get_utype($uyid);
    $dep_name = (is_array($dt) && !empty($dt) && isset($dt[0]->name)) ? $dt[0]->name : 'home';
    $admin_id_filter    = $this->input->Post('admin_id_filter');
    $rm_filter          = $this->input->Post('rm_filter');
    $days_filter        = $this->input->Post('days_filter');
    if(isset($_POST['days_filter'])){
        $sdate                  = $this->input->Post('sdate');
        $edate                  = $this->input->Post('edate');
        if(isset($admin_id_filter)){
            if($admin_id_filter == 'all'){
                $admin_id_filter = 'all';
            }else{
                $uid = $admin_id_filter;
            }
            if(isset($rm_filter)){
                if($rm_filter =='all'){
                    $rm_filter = 'all';
                }else{
                    $uid = $rm_filter;
                }
            }
        }
        if($days_filter !== ''){
            $edate              = date('Y-m-d');
            if($days_filter == '8 Days'){
                $sdate          = date('Y-m-d', strtotime('-8 days', strtotime($edate)));
                $days_inclusive = 8;
            }else if($days_filter == '15 Days'){
                $sdate          = date('Y-m-d', strtotime('-15 days', strtotime($edate)));
                $days_inclusive = 15;
            }else if($days_filter == '30 Days'){
                $sdate          = date('Y-m-d', strtotime('-30 days', strtotime($edate)));
                $days_inclusive = 30;
            }else if($days_filter == 'more than 30 Days'){
                $sdate          = date('Y-m-d', strtotime('-31 days', strtotime($edate)));
                $days_inclusive = 31;
            }else{
                $sdate          = date('Y-m-d', strtotime('-8 days', strtotime($edate)));
                $days_inclusive = 8;
            }   
        }
    }else{
        $sdate              = date('Y-m-d');
        $edate              = date('Y-m-d');
        $rm_filter          = 'all';
        $admin_id_filter    = $uid;
        $sdate = date('Y-m-d', strtotime('-8 days', strtotime($edate)));
        $days_inclusive = 8;
    }
        if($rm_filter !=='all'){
           $uid = $rm_filter;
        }else{
          $uid = $admin_id_filter;
        }
    $sameStatusSinceDaysDatas    = $this->Report_model->GetCompanyAfterAssignSameStatusSinceDays($uid,$sdate,$edate,$admin_id_filter,$rm_filter,$days_inclusive);
    
    
    $statusCounts = [];
    foreach ($sameStatusSinceDaysDatas as $row) {
        $status = $row->current_status;
        if (!isset($statusCounts[$status])) {
            $statusCounts[$status] = 0;
        }
        $statusCounts[$status]++;
    }
   $mainBDCounts = [];
        foreach ($sameStatusSinceDaysDatas as $row) {
            $main_bd_name = $row->main_bd_name;
            if (!isset($mainBDCounts[$main_bd_name])) {
                $mainBDCounts[$main_bd_name] = 0;
            }
            $mainBDCounts[$main_bd_name]++;
        }
        $mainBDStatusCounts = [];
        foreach ($sameStatusSinceDaysDatas as $row) {
            $bd = $row->main_bd_name;
            $status = $row->current_status;
            if (!isset($mainBDStatusCounts[$bd])) {
                $mainBDStatusCounts[$bd] = [];
            }
            if (!isset($mainBDStatusCounts[$bd][$status])) {
                $mainBDStatusCounts[$bd][$status] = 0;
            }
            $mainBDStatusCounts[$bd][$status]++;
        }
    if(!empty($user)){
        $this->load->view('Reports/AfterAssignSameStatusSinceDays',[
            'uid'                       => $uid,
            'user'                      => $user,
            'sdate'                     => $sdate,
            'edate'                     => $edate,
            'sd'                        => $sdate,
            'ed'                        => $edate,
            'sameStatusSinceDaysDatas'  => $sameStatusSinceDaysDatas,
            'admin_id_filter'           => $admin_id_filter,
            'rm_filter'                 => $rm_filter,
            'dep_name'                  => $dep_name,
            'days_inclusive'            => $days_inclusive,
            'days_filter'               => $days_filter,
            'statusCounts'              => $statusCounts,
            'mainBDCounts'              => $mainBDCounts,
            'mainBDStatusCounts'        => $mainBDStatusCounts
        ]);
    }else{
        redirect('Menu/main');
    }
}
public function AfterAssignLineManagerSameStatusSinceDays(){
    $user           = $this->session->userdata('user');
    if(empty($user) || empty($user['user_id'])){ redirect('login'); return; }
    // session guard added 28 May 2026 to fix HTTP 500 on null session
    $data['user']   = $user;
    $uid            = $user['user_id'];
    $uyid           =  $user['type_id'];
    $this->load->model('Menu_model');
     $this->load->model('Report_model');
    $dt = $this->Menu_model->get_utype($uyid);
    $dep_name = (is_array($dt) && !empty($dt) && isset($dt[0]->name)) ? $dt[0]->name : 'home';
    $admin_id_filter    = $this->input->Post('admin_id_filter');
    $rm_filter          = $this->input->Post('rm_filter');
    $days_filter        = $this->input->Post('days_filter');
    if(isset($_POST['days_filter'])){
        $sdate                  = $this->input->Post('sdate');
        $edate                  = $this->input->Post('edate');
        if(isset($admin_id_filter)){
            if($admin_id_filter == 'all'){
                $admin_id_filter = 'all';
            }else{
                $uid = $admin_id_filter;
            }
            if(isset($rm_filter)){
                if($rm_filter =='all'){
                    $rm_filter = 'all';
                }else{
                    $uid = $rm_filter;
                }
            }
        }
        if($days_filter !== ''){
            $edate              = date('Y-m-d');
            if($days_filter == '8 Days'){
                $sdate          = date('Y-m-d', strtotime('-8 days', strtotime($edate)));
                $days_inclusive = 8;
            }else if($days_filter == '15 Days'){
                $sdate          = date('Y-m-d', strtotime('-15 days', strtotime($edate)));
                $days_inclusive = 15;
            }else if($days_filter == '30 Days'){
                $sdate          = date('Y-m-d', strtotime('-30 days', strtotime($edate)));
                $days_inclusive = 30;
            }else if($days_filter == 'more than 30 Days'){
                $sdate          = date('Y-m-d', strtotime('-31 days', strtotime($edate)));
                $days_inclusive = 31;
            }else{
                $sdate          = date('Y-m-d', strtotime('-8 days', strtotime($edate)));
                $days_inclusive = 8;
            }   
        }
    }else{
        $sdate              = date('Y-m-d');
        $edate              = date('Y-m-d');
        $rm_filter          = 'all';
        $admin_id_filter    = $uid;
        $sdate = date('Y-m-d', strtotime('-8 days', strtotime($edate)));
        $days_inclusive = 8;
    }
        if($rm_filter !=='all'){
           $uid = $rm_filter;
        }else{
          $uid = $admin_id_filter;
        }
    $sameStatusSinceDaysDatas    = $this->Report_model->GetCompanyAfterLMAssignSameStatusSinceDays($uid,$sdate,$edate,$admin_id_filter,$rm_filter,$days_inclusive);
    // dd($sameStatusSinceDaysDatas);
    $fields = [
    'cluster_manager_name',
    'pst_name',
    'ash_nae_co_name',
    'ash_w_co_name',
    'ash_s_co_name',
    'rm_east_co_name',
    'rm_north_co_name',
    'acm_co_name'
];
    $resultNumberOfDays = [
        "1_8"   => 0,
        "9_15"  => 0,
        "16_30" => 0,
        "31_50" => 0,
        "50_+"   => 0
    ];
$nameCounts = [];
foreach ($sameStatusSinceDaysDatas as $obj) {
    foreach ($fields as $field) {
        if (!empty($obj->$field)) {
            $name = trim($obj->$field);
            if (!isset($nameCounts[$name])) {
                $nameCounts[$name] = 0;
            }
            $nameCounts[$name]++;
        }
    }
     $days = (int)$obj->days;
      if ($days >= 1 && $days <= 8) {
        $resultNumberOfDays["1_8"]++;
    } elseif ($days >= 9 && $days <= 15) {
        $resultNumberOfDays["9_15"]++;
    } elseif ($days >= 16 && $days <= 30) {
        $resultNumberOfDays["16_30"]++;
    } elseif ($days >= 31 && $days <= 50) {
        $resultNumberOfDays["31_50"]++;
    } else {
        $resultNumberOfDays["50_+"]++;
    }
}
    $statusCounts = [];
    foreach ($sameStatusSinceDaysDatas as $row) {
        $status = $row->current_status;
        if (!isset($statusCounts[$status])) {
            $statusCounts[$status] = 0;
        }
        $statusCounts[$status]++;
    }
    if(!empty($user)){
        $this->load->view('Reports/AfterAssignLineManagerSameStatusSinceDays',[
            'uid'                       => $uid,
            'user'                      => $user,
            'sdate'                     => $sdate,
            'edate'                     => $edate,
            'sd'                        => $sdate,
            'ed'                        => $edate,
            'sameStatusSinceDaysDatas'  => $sameStatusSinceDaysDatas,
            'admin_id_filter'           => $admin_id_filter,
            'rm_filter'                 => $rm_filter,
            'dep_name'                  => $dep_name,
            'days_inclusive'            => $days_inclusive,
            'days_filter'               => $days_filter,
            'statusCounts'              => $statusCounts,
            // 'mainBDCounts'              => $mainBDCounts,
            // 'mainBDStatusCounts'        => $mainBDStatusCounts,
            'nameCounts'                => $nameCounts,
            'resultNumberOfDays'                => $resultNumberOfDays,
        ]);
    }else{
        redirect('Menu/main');
    }
}
public function AfterAssignLineManagerSameStatusSinceDaysLists($sdate,$edate,$admin_id_filter,$rm_filter,$days_inclusive){
    $user           = $this->session->userdata('user');
    if(empty($user) || empty($user['user_id'])){ redirect('login'); return; }
    // session guard added 28 May 2026 to fix HTTP 500 on null session
    $data['user']   = $user;
    $uid            = $user['user_id'];
    $uyid           =  $user['type_id'];
    $this->load->model('Menu_model');
     $this->load->model('Report_model');
    $dt = $this->Menu_model->get_utype($uyid);
    $dep_name = (is_array($dt) && !empty($dt) && isset($dt[0]->name)) ? $dt[0]->name : 'home';
    
        if($rm_filter !=='all'){
           $uid = $rm_filter;
        }else{
          $uid = $admin_id_filter;
        }
        if(in_array($days_inclusive,['1_8','9_15','16_30','31_50'])){
            list($firstDays, $secondDays) = explode("_", $days_inclusive);
        }else if(in_array($days_inclusive,['50_'])){
            $firstDays = 50;
            $secondDays = 365;
        }
       
    $sameStatusSinceDaysDatas    = $this->Report_model->GetCompanyAfterLMAssignSameStatusSinceDaysLists($uid,$sdate,$edate,$admin_id_filter,$rm_filter,$firstDays,$secondDays);
    $fields = [
    'cluster_manager_name',
    'pst_name',
    'ash_nae_co_name',
    'ash_w_co_name',
    'ash_s_co_name',
    'rm_east_co_name',
    'rm_north_co_name',
    'acm_co_name'
];
    $resultNumberOfDays = [
        "1_8"   => 0,
        "9_15"  => 0,
        "16_30" => 0,
        "31_50" => 0,
        "50_+"   => 0
    ];
$nameCounts = [];
foreach ($sameStatusSinceDaysDatas as $obj) {
    foreach ($fields as $field) {
        if (!empty($obj->$field)) {
            $name = trim($obj->$field);
            if (!isset($nameCounts[$name])) {
                $nameCounts[$name] = 0;
            }
            $nameCounts[$name]++;
        }
    }
     $days = (int)$obj->days;
      if ($days >= 0 && $days <= 8) {
        $resultNumberOfDays["1_8"]++;
    } elseif ($days >= 9 && $days <= 15) {
        $resultNumberOfDays["9_15"]++;
    } elseif ($days >= 16 && $days <= 30) {
        $resultNumberOfDays["16_30"]++;
    } elseif ($days >= 31 && $days <= 50) {
        $resultNumberOfDays["31_50"]++;
    } else {
        $resultNumberOfDays["50_+"]++;
    }
}
    $statusCounts = [];
    foreach ($sameStatusSinceDaysDatas as $row) {
        $status = $row->current_status;
        if (!isset($statusCounts[$status])) {
            $statusCounts[$status] = 0;
        }
        $statusCounts[$status]++;
    }
    if(!empty($user)){
        $this->load->view('Reports/AfterAssignLineManagerSameStatusSinceDaysLists',[
            'uid'                       => $uid,
            'user'                      => $user,
            'sdate'                     => $sdate,
            'edate'                     => $edate,
            'sd'                        => $sdate,
            'ed'                        => $edate,
            'sameStatusSinceDaysDatas'  => $sameStatusSinceDaysDatas,
            'admin_id_filter'           => $admin_id_filter,
            'rm_filter'                 => $rm_filter,
            'dep_name'                  => $dep_name,
            'days_inclusive'            => $days_inclusive,
            'days_filter'               => $days_filter,
            'statusCounts'              => $statusCounts,
            // 'mainBDCounts'              => $mainBDCounts,
            // 'mainBDStatusCounts'        => $mainBDStatusCounts,
            'nameCounts'                => $nameCounts,
            'resultNumberOfDays'                => $resultNumberOfDays,
        ]);
    }else{
        redirect('Menu/main');
    }
}
public function AfterAssignLineManagerSameStatusSinceDaysTaskLog(){
    $user           = $this->session->userdata('user');
    if(empty($user) || empty($user['user_id'])){ redirect('login'); return; }
    // session guard added 28 May 2026 to fix HTTP 500 on null session
    $data['user']   = $user;
    $uid            = $user['user_id'];
    $uyid           =  $user['type_id'];
    $this->load->model('Menu_model');
     $this->load->model('Report_model');
    $dt = $this->Menu_model->get_utype($uyid);
    $dep_name = (is_array($dt) && !empty($dt) && isset($dt[0]->name)) ? $dt[0]->name : 'home';
    $admin_id_filter    = $this->input->Post('admin_id_filter');
    $rm_filter          = $this->input->Post('rm_filter');
    $days_filter        = $this->input->Post('days_filter');
    if(isset($_POST['days_filter'])){
        $sdate                  = $this->input->Post('sdate');
        $edate                  = $this->input->Post('edate');
        if(isset($admin_id_filter)){
            if($admin_id_filter == 'all'){
                $admin_id_filter = 'all';
            }else{
                $uid = $admin_id_filter;
            }
            if(isset($rm_filter)){
                if($rm_filter =='all'){
                    $rm_filter = 'all';
                }else{
                    $uid = $rm_filter;
                }
            }
        }
        if($days_filter !== ''){
            $edate              = date('Y-m-d');
            if($days_filter == '8 Days'){
                $sdate          = date('Y-m-d', strtotime('-8 days', strtotime($edate)));
                $days_inclusive = 8;
            }else if($days_filter == '15 Days'){
                $sdate          = date('Y-m-d', strtotime('-15 days', strtotime($edate)));
                $days_inclusive = 15;
            }else if($days_filter == '30 Days'){
                $sdate          = date('Y-m-d', strtotime('-30 days', strtotime($edate)));
                $days_inclusive = 30;
            }else if($days_filter == 'more than 30 Days'){
                $sdate          = date('Y-m-d', strtotime('-31 days', strtotime($edate)));
                $days_inclusive = 31;
            }else{
                $sdate          = date('Y-m-d', strtotime('-8 days', strtotime($edate)));
                $days_inclusive = 8;
            }   
        }
    }else{
        $sdate              = date('Y-m-d');
        $edate              = date('Y-m-d');
        $rm_filter          = 'all';
        $admin_id_filter    = $uid;
        $sdate = date('Y-m-d', strtotime('-8 days', strtotime($edate)));
        $days_inclusive = 8;
    }
        if($rm_filter !=='all'){
           $uid = $rm_filter;
        }else{
          $uid = $admin_id_filter;
        }
    $sameStatusSinceDaysDatas    = $this->Report_model->GetCompanyAfterLMAssignSameStatusSinceDaysTaskLogsCount($uid,$sdate,$edate,$admin_id_filter,$rm_filter,$days_inclusive);
    // echo $this->db->last_query();
    // die;
    // dd($sameStatusSinceDaysDatas);
    if(!empty($user)){
        $this->load->view('Reports/AfterAssignLineManagerSameStatusSinceDaysTaskLog',[
            'uid'                       => $uid,
            'user'                      => $user,
            'sdate'                     => $sdate,
            'edate'                     => $edate,
            'sd'                        => $sdate,
            'ed'                        => $edate,
            'sameStatusSinceDaysDatas'  => $sameStatusSinceDaysDatas,
            'admin_id_filter'           => $admin_id_filter,
            'rm_filter'                 => $rm_filter,
            'dep_name'                  => $dep_name,
            'days_inclusive'            => $days_inclusive,
            'days_filter'               => $days_filter,
        ]);
    }else{
        redirect('Menu/main');
    }
}
public function FunnelTransferDetails(){
    $user           = $this->session->userdata('user');
    if(empty($user) || empty($user['user_id'])){ redirect('login'); return; }
    // session guard added 28 May 2026 to fix HTTP 500 on null session
    $data['user']   = $user;
    $uid            = $user['user_id'];
    $uyid           =  $user['type_id'];
    $this->load->model('Menu_model');
    $this->load->model('Report_model');
    $dt=$this->Menu_model->get_utype($uyid);
    $dep_name = (is_array($dt) && !empty($dt) && isset($dt[0]->name)) ? $dt[0]->name : 'home';
    $admin_id_filter    = $this->input->Post('admin_id_filter');
    $rm_filter          = $this->input->Post('rm_filter');
    if(isset($_POST['sdate'])){
        $sdate                  = $this->input->Post('sdate');
        $edate                  = $this->input->Post('edate');
        if(isset($admin_id_filter)){
            if($admin_id_filter == 'all'){
                $admin_id_filter = 'all';
            }else{
                $uid = $admin_id_filter;
            }
            if(isset($rm_filter)){
                if($rm_filter =='all'){
                    $rm_filter = 'all';
                }else{
                    $uid = $rm_filter;
                }
            }
        }
    }else{
        $edate = date('Y-m-d'); // today
        $sdate = date('Y-m-d', strtotime('-3 month', strtotime($edate))); 
        $rm_filter          = 'all';
        $task_action_id     = 'all';
        $admin_id_filter    = $uid;
    }
        if($rm_filter !=='all'){
           $uid = $rm_filter;
        }else{
          $uid = $admin_id_filter;
        }
     $funneltransferLogs   = $this->Report_model->GetFunnelTransferLogDatas($uid,$sdate,$edate,$admin_id_filter,$rm_filter);
    if(!empty($user)){
        $this->load->view('Reports/FunnelTransferDetails',['uid'=>$uid,'user'=>$user,'user_tupe_id'=>$uyid,'sdate'=>$sdate,'edate'=>$edate,'task_action_id'=>$task_action_id,'task_action_id'=> $task_action_id,'admin_id_filter'=>$admin_id_filter,'rm_filter'=>$rm_filter,'dep_name'=>$dep_name,'funneltransferLogs'=>$funneltransferLogs]);
    }else{
        redirect('Menu/main');
    }
}
public function TodaysConversionDatas($taruser_id,$sdate,$edate,$statusChange,$taskaction = NULL){
    $user           = $this->session->userdata('user');
    if(empty($user) || empty($user['user_id'])){ redirect('login'); return; }
    // session guard added 28 May 2026 to fix HTTP 500 on null session
    $data['user']   = $user;
    $uid            = $user['user_id'];
    $uyid           =  $user['type_id'];
    $this->load->model('Menu_model');
    $this->load->model('Report_model');
    
    $dt=$this->Menu_model->get_utype($uyid);
    $dep_name = (is_array($dt) && !empty($dt) && isset($dt[0]->name)) ? $dt[0]->name : 'home';
    $statusArray = explode("_", $statusChange);
    $from_status = $statusArray[0]; 
    $to_status   = $statusArray[1]; 
    if (is_null($taskaction)) {
        $taskaction = 'all';
    }
    
  $statusChnageLogs   = $this->Menu_model->TodaysConversionDatasDetails($taruser_id,$sdate,$edate,$from_status,$to_status,$taskaction);
    if(!empty($user)){
        $this->load->view('Reports/TodaysConversionDatas',['uid'=>$uid,'user'=>$user,'user_tupe_id'=>$uyid,'sdate'=>$sdate,'edate'=>$edate,'dep_name'=>$dep_name,'statusChnageLogs'=>$statusChnageLogs]);
    }else{
        redirect('Menu/main');
    }
}
public function TodaysConversionWithCIDDatas($cid,$sdate,$edate,$statusChange){
    $user           = $this->session->userdata('user');
    if(empty($user) || empty($user['user_id'])){ redirect('login'); return; }
    // session guard added 28 May 2026 to fix HTTP 500 on null session
    $data['user']   = $user;
    $uid            = $user['user_id'];
    $uyid           =  $user['type_id'];
    $this->load->model('Menu_model');
    $this->load->model('Report_model');
    
    $dt=$this->Menu_model->get_utype($uyid);
    $dep_name = (is_array($dt) && !empty($dt) && isset($dt[0]->name)) ? $dt[0]->name : 'home';
    $statusArray = explode("_", $statusChange);
    $from_status = $statusArray[0]; 
    $to_status   = $statusArray[1]; 
 
  $statusChnageLogs   = $this->Menu_model->TodaysConversionWithCIDDatasDetails($cid,$sdate,$edate,$from_status,$to_status);
    if(!empty($user)){
        $this->load->view('Reports/TodaysConversionDatas',['uid'=>$uid,'user'=>$user,'user_tupe_id'=>$uyid,'sdate'=>$sdate,'edate'=>$edate,'dep_name'=>$dep_name,'statusChnageLogs'=>$statusChnageLogs]);
    }else{
        redirect('Menu/main');
    }
}
public function CheckRemoveCompanyLogsBetweenDate(){
    $this->load->model('Menu_model');
    $this->load->model('Report_model');
    if(isset($_POST['sdate'])){
        $sdate      = $_POST['sdate'];
        $edate      = $_POST['edate'];
    }else{
        $sdate      = date('Y-m-d');
        $edate      = date('Y-m-d');
        $sdate      = date('Y-m-d', strtotime('-30 days', strtotime($edate)));
    }
    $sd             =   $sdate;
    $ed             =   $edate;
    $user           =   $this->session->userdata('user');
    if(empty($user) || empty($user['user_id'])){ redirect('login'); return; }
    // session guard added 28 May 2026 to fix HTTP 500 on null session
    $data['user']   =   $user;
    $uid            =   $user['user_id'];
    $uyid           =   $user['type_id'];
    $this->load->model('Menu_model');
    $dt             =   $this->Menu_model->get_utype($uyid);
    $dep_name = (is_array($dt) && !empty($dt) && isset($dt[0]->name)) ? $dt[0]->name : 'home';
    $companyInfoCBD = $this->Report_model->GetRemoveCompanyLogs($uid,$sd,$ed);
    if(!empty($user)){
   
        $this->load->view('Functions/CheckRemoveCompanyLogsBetweenDate',['uid'=>$uid,'user'=>$user,'sdate'=>$sdate, 'edate'=>$edate,'sd'=>$sd,'ed'=>$ed,'companyInfoCBDs'=>$companyInfoCBD,'dep_name'=>$dep_name]);
    }else{
        redirect('Menu/main');
    }
}
public function CheckRemoveCompanyLogsBetweenDateLogs($targetuid,$sdate,$edate){
    $this->load->model('Menu_model');
    $this->load->model('Report_model');
    $sd             =   $sdate;
    $ed             =   $edate;
    $user           =   $this->session->userdata('user');
    if(empty($user) || empty($user['user_id'])){ redirect('login'); return; }
    // session guard added 28 May 2026 to fix HTTP 500 on null session
    $data['user']   =   $user;
    $uid            =   $user['user_id'];
    $uyid           =   $user['type_id'];
    $this->load->model('Menu_model');
    $dt             =   $this->Menu_model->get_utype($uyid);
    $dep_name = (is_array($dt) && !empty($dt) && isset($dt[0]->name)) ? $dt[0]->name : 'home';
    $targetuidDatas = $this->Menu_model->get_userbyid($targetuid);
    $companyInfoCBD = $this->Report_model->GetRemoveCompanyLogslists($targetuid,$sd,$ed);
    if(!empty($user)){
   
        $this->load->view('Functions/CheckRemoveCompanyLogsBetweenDateLogs',['uid'=>$uid,'user'=>$user,'sdate'=>$sdate, 'edate'=>$edate,'sd'=>$sd,'ed'=>$ed,'companyInfoCBDs'=>$companyInfoCBD,'dep_name'=>$dep_name,'targetuidDatas'=>$targetuidDatas]);
    }else{
        redirect('Menu/main');
    }
}







public function DownloadMeetingsMomDatas($mtypes,$target_uid,$sdate,$edate,$userwise =NULL){
    $user           = $this->session->userdata('user');
    if(empty($user) || empty($user['user_id'])){ redirect('login'); return; }
    // session guard added 28 May 2026 to fix HTTP 500 on null session
    $data['user']   = $user;
    $uid            = $user['user_id'];
    $uyid           =  $user['type_id'];
    $this->load->model('Menu_model');
    $dt=$this->Menu_model->get_utype($uyid);
    $dep_name = (is_array($dt) && !empty($dt) && isset($dt[0]->name)) ? $dt[0]->name : 'home';
    if(isset($_POST['sdate'])){
    $sdate = $_POST['sdate'];
    $edate = $_POST['edate'];
    }
    if(is_null($userwise)){
        $userwise = '';
    }
    $totalMeetingsUserByDatas   = $this->Menu_model->TotalMeetingDetailsDatas($target_uid,$sdate,$edate,$mtypes,$uid,$userwise);
  
    dd($totalMeetingsUserByDatas);



}




 public function AdvanceVSRPMeetingCountWithNextNewAdvance() {
        $user = $this->session->userdata('user');
        if (empty($user)) {
            redirect('Menu/main');
        }
        $uid        = $user['user_id'];
        $uyid       = $user['type_id'];
        $dt         = $this->Menu_model->get_utype($uyid);
        $dep_name   = isset($dt[0]->name) ? $dt[0]->name : 'default';
        $cdate      = date("Y-m-d");
        $admin_id_filter    = $this->input->Post('admin_id_filter');
        $rm_filter          = $this->input->Post('rm_filter');
    if(isset($_POST['admin_id_filter'])){
        // $sdate                  = $this->input->Post('sdate');
        // $edate                  = $this->input->Post('edate');

        $sdate              = date('Y-m-d');
        $edate              = date('Y-m-d');

        if(isset($admin_id_filter)){
            if($admin_id_filter == 'all'){
                $admin_id_filter = 'all';
            }else{
                $uid = $admin_id_filter;
            }
            if(isset($rm_filter)){
                if($rm_filter =='all'){
                    $rm_filter = 'all';
                }else{
                    $uid = $rm_filter;
                }
            }
        }
    }else{
        $sdate              = date('Y-m-d');
        $edate              = date('Y-m-d');
        $rm_filter          = 'all';
        $admin_id_filter    = $uid;
    }
        if($rm_filter !=='all'){
           $uid = $rm_filter;
        }else{
          $uid = $admin_id_filter;
        }
        $mdata      = $this->Report_model->GetAllUserByReportingManager($uid,$admin_id_filter,$rm_filter,$sdate);
      
        $this->load->view('Reports/AdvanceVSRPMeetingCountWithNextNewAdvance', [
            'uid'       => $uid,
            'dep_name'  =>$dt,
            'ctype_id'  =>$uyid,
            'user'      => $user,
            'dep_name'  =>$dep_name,
            'cdate'     =>$cdate,
            'mdata'     =>$mdata,
            'sdate'     =>$sdate,
            'edate'     =>$edate,
            'admin_id_filter'=>$admin_id_filter,
            'rm_filter'=>$rm_filter,
            
        ]);
    }
 public function AdvanceVSRPMeetingCountWithNextNewAdvanceDetails($target_uid){
        $user = $this->session->userdata('user');
        // if (empty($user)) {
        //     redirect('Menu/main');
        // }
        $uid        = $user['user_id'];
        $uyid       = $user['type_id'];
        $dt         = $this->Menu_model->get_utype($uyid);

        $dep_name   = isset($dt[0]->name) ? $dt[0]->name : 'Guest';
 
        $targetUdetail          = $this->Menu_model->get_userbyid($target_uid); 

        $travelCashData         = $this->Report_model->GetAllTravelAdvancedBySingleUid($target_uid);
        $travelCashExpenseData  = $this->Report_model->GetAllCashExpenseBySingleUid($target_uid);
        $code                   = 8; // AND Line Maanager Approved | Admin Approved |  AND Account Approved
        $ocashreq               = $this->Report_model->GetAllTravelAdvancedByCodeSingleUser($code,$target_uid);
      
        $this->load->view('Reports/AdvanceVSRPMeetingCountWithNextNewAdvanceDetails', [
            'uid'                       =>  $uid,
            'dep_name'                  =>  $dt,
            'ctype_id'                  =>  $uyid,
            'user'                      =>  $user,
            'dep_name'                  =>  $dep_name,
            'cdate'                     =>  $cdate,
            'targetUdetail'             =>  $targetUdetail,
            'sdate'                     =>  $sdate,
            'edate'                     =>  $edate,
            'travelCashData'            =>  $travelCashData,
            'travelCashExpenseData'     =>  $travelCashExpenseData,
            'ocashreq'                  =>  $ocashreq,
            'target_uid'                =>  $target_uid,
        ]);
    }







    public function ClosurePipeLine(){
    $user           = $this->session->userdata('user');
    if(empty($user) || empty($user['user_id'])){ redirect('login'); return; }
    // session guard added 28 May 2026 to fix HTTP 500 on null session
    $data['user']   = $user;
    $uid            = $user['user_id'];
    $uyid           =  $user['type_id'];
    $this->load->model('Menu_model');

    ini_set('max_execution_time', 0);  // unlimited time
    ini_set('memory_limit', '1024M');  // 1GB memory


    $dt             =  $this->Menu_model->get_utype($uyid);
    $dep_name = (is_array($dt) && !empty($dt) && isset($dt[0]->name)) ? $dt[0]->name : 'home';
    $admin_id_filter    = $this->input->Post('admin_id_filter');
    $rm_filter          = $this->input->Post('rm_filter');
    if(isset($_POST['admin_id_filter'])){
        $sdate                  = $this->input->Post('sdate');
        $edate                  = $this->input->Post('edate');
        if(isset($admin_id_filter)){
            if($admin_id_filter == 'all'){
                $admin_id_filter = 'all';
            }else{
                $uid = $admin_id_filter;
            }
            if(isset($rm_filter)){
                if($rm_filter =='all'){
                    $rm_filter = 'all';
                }else{
                    $uid = $rm_filter;
                }
            }
        }
    }else{
        $sdate              = date('Y-m-d');
        $sdate              = '2026-04-01';
        $edate              = date('Y-m-d');
        $rm_filter          = 'all';
        $admin_id_filter    = $uid;
    }
        if($rm_filter !=='all'){
           $uid = $rm_filter;
        }else{
          $uid = $admin_id_filter;
        }
    if(isset($_POST['category'])){
        $category   = $_POST['category'];
    }else{
        $category = '';
    }
    $totalFunnnels = $this->Report_model->getAllCompanyBYRollesMaiingClosurePipeLine($uid,$admin_id_filter,$rm_filter,$sdate,$edate);
    $this->load->view('Reports/ClosurePipeLine',[
        'user'=>$user,
        'uid'=>$uid,
        'category'=>$category,
        'dep_name'=>$dep_name,
        'totalFunnnels'=>$totalFunnnels,
        'admin_id_filter'=>$admin_id_filter,
        'rm_filter'=>$rm_filter,
        'sdate'=>$sdate,
        'edate'=>$edate,
    ]);
}
    public function ClosurePipeLineDetails($ftype,$target_uid,$sdate,$edate,$userwise = NULL){
    $user           = $this->session->userdata('user');
    if(empty($user) || empty($user['user_id'])){ redirect('login'); return; }
    // session guard added 28 May 2026 to fix HTTP 500 on null session
    $data['user']   = $user;
    $uid            = $user['user_id'];
    $uyid           =  $user['type_id'];
    $this->load->model('Menu_model');
    $dt             =  $this->Menu_model->get_utype($uyid);
    $dep_name = (is_array($dt) && !empty($dt) && isset($dt[0]->name)) ? $dt[0]->name : 'home';

    $udetail = $this->Menu_model->get_userbyid($target_uid);

    $totalClosurePipeLineDatas = $this->Report_model->getAllCompanyBYRollesMaiingClosurePipeLineDetails($target_uid,$ftype,$sdate,$edate,$userwise);

    $this->load->view('Reports/ClosurePipeLineDetails',[
        'user'=>$user,
        'uid'=>$uid,
        'category'=>$category,
        'dep_name'=>$dep_name,
        'totalClosurePipeLineDatas'=>$totalClosurePipeLineDatas,
        'admin_id_filter'=>$admin_id_filter,
        'rm_filter'=>$rm_filter,
        'sdate'=>$sdate,
        'edate'=>$edate,
        'ftype'=>$ftype,
        'udetail'=>$udetail
    ]);
}
    public function ClosurePipeLineDetailsByCompany($ftype,$cid,$sdate,$edate){
    $user           = $this->session->userdata('user');
    if(empty($user) || empty($user['user_id'])){ redirect('login'); return; }
    // session guard added 28 May 2026 to fix HTTP 500 on null session
    $data['user']   = $user;
    $uid            = $user['user_id'];
    $uyid           =  $user['type_id'];
    $this->load->model('Menu_model');
    $dt             =  $this->Menu_model->get_utype($uyid);
    $cmpDatas         =  $this->Menu_model->getCompanyDetailsByCID($cid);
    $dep_name = (is_array($dt) && !empty($dt) && isset($dt[0]->name)) ? $dt[0]->name : 'home';

    // dd($cmpDatas);
    $totalClosurePipeLineDatas = $this->Report_model->getAllCompanyBYRollesMaiingClosurePipeLineDetailsBYCID($cid,$ftype,$sdate,$edate);

    $this->load->view('Reports/ClosurePipeLineDetailsByCompany',[
        'user'=>$user,
        'uid'=>$uid,
        'category'=>$category,
        'dep_name'=>$dep_name,
        'totalClosurePipeLineDatas'=>$totalClosurePipeLineDatas,
        'admin_id_filter'=>$admin_id_filter,
        'rm_filter'=>$rm_filter,
        'sdate'=>$sdate,
        'edate'=>$edate,
        'ftype'=>$ftype,
        'cmpDatas'=>$cmpDatas
    ]);
}


public function TeamVisitInTravelCluster(){
    $user           = $this->session->userdata('user');
    if(empty($user) || empty($user['user_id'])){ redirect('login'); return; }
    // session guard added 28 May 2026 to fix HTTP 500 on null session
    $data['user']   = $user;
    $uid            = $user['user_id'];
    $uyid           =  $user['type_id'];
    $this->load->model('Menu_model');
    $dt             =  $this->Menu_model->get_utype($uyid);
    $dep_name = (is_array($dt) && !empty($dt) && isset($dt[0]->name)) ? $dt[0]->name : 'home';
    $admin_id_filter    = $this->input->Post('admin_id_filter');
    $rm_filter          = $this->input->Post('rm_filter');
    if(isset($_POST['admin_id_filter'])){
        $sdate                  = $this->input->Post('sdate');
        $edate                  = $this->input->Post('edate');
        if(isset($admin_id_filter)){
            if($admin_id_filter == 'all'){
                $admin_id_filter = 'all';
            }else{
                $uid = $admin_id_filter;
            }
            if(isset($rm_filter)){
                if($rm_filter =='all'){
                    $rm_filter = 'all';
                }else{
                    $uid = $rm_filter;
                }
            }
        }
    }else{
        $sdate              = date('Y-m-d');
        $sdate              = "2026-04-01";
        $edate              = date('Y-m-d');
        $rm_filter          = 'all';
        $admin_id_filter    = $uid;
    }
        if($rm_filter !=='all'){
           $uid = $rm_filter;
        }else{
          $uid = $admin_id_filter;
        }
    if(isset($_POST['category'])){
        $category   = $_POST['category'];
    }else{
        $category = '';
        }
    $totalFunnnels = $this->Report_model->getAllCompanyBYRollesMaiingBYMeeting($uid,$admin_id_filter,$rm_filter,$sdate,$edate);

    $cfData = $this->Menu_model->getCurrentFinancialYearAndQuarter();
    $currentQuarter = "Q".$cfData['quarter'];


    $this->load->view('Reports/TeamVisitInTravelCluster',[
        'user'=>$user,
        'uid'=>$uid,
        'category'=>$category,
        'dep_name'=>$dep_name,
        'totalFunnnels'=>$totalFunnnels,
        'admin_id_filter'=>$admin_id_filter,
        'rm_filter'=>$rm_filter,
        'sdate'=>$sdate,
        'edate'=>$edate,
        'currentQuarter'=>$currentQuarter
    ]);
}




public function FunnelDetailsWithClusterIDList($mtypes,$target_uid,$clusterID,$userwise,$sdate,$edate){
    $user           = $this->session->userdata('user');
    if(empty($user) || empty($user['user_id'])){ redirect('login'); return; }
    // session guard added 28 May 2026 to fix HTTP 500 on null session
    $data['user']   = $user;
    $uid            = $user['user_id'];
    $uyid           =  $user['type_id'];
    $this->load->model('Menu_model');
    $dt=$this->Menu_model->get_utype($uyid);
    $dep_name = (is_array($dt) && !empty($dt) && isset($dt[0]->name)) ? $dt[0]->name : 'home';
    if(is_null($userwise)){
        $userwise = '';
    }

    $cfData = $this->Menu_model->getCurrentFinancialYearAndQuarter();
    $currentQuarter = "Q".$cfData['quarter'];

    $totalTasksUserByDatas   = $this->Report_model->GetFunnelDetailsWithClusterIDMeetingDetails($target_uid,$mtypes,$clusterID,$userwise,$sdate,$edate);
    
    // echo $this->db->last_query();
    // die;

    if(!empty($user)){
        $this->load->view('Reports/FunnelDetailsWithClusterIDList',['uid'=>$uid,'user'=>$user,'sdate'=>$sdate,'edate'=>$edate,'sd'=>$sdate,'ed'=>$edate,'totalTasksUserByDatas'=>$totalTasksUserByDatas,'mtypes'=>$mtypes,'comming_user_types'=>$uyid,'mtypes'=>$mtypes,'target_uid'=>$target_uid,'dep_name'=>$dep_name,'currentQuarter'=>$currentQuarter]);
    }else{
        redirect('Menu/main');
    }
}



public function TodaysOperationTeamTaskPlannedByOurTeamProject(){

    $this->load->model('Menu_model');
    $user           = $this->session->userdata('user');
    $data['user']   = $user;
    $uid            = $user['user_id'];
    $uyid           = $user['type_id'];
    $dt             = $this->Menu_model->get_utype($uyid);
    $dep_name = (is_array($dt) && !empty($dt) && isset($dt[0]->name)) ? $dt[0]->name : 'home';

    if(empty($user)){redirect('Menu/main');}

    if(isset($_POST['sdate'])){

        $sdate    = $this->input->Post('sdate');
        $edate    = $this->input->Post('edate');

    }else{
        $sdate    = date('Y-m-d');
        $edate    = date('Y-m-d');
    }

    $totalTasksUserByDatas   = $this->Report_model->GetTodaysTaskPlannedByOperationTeam($uid,$sdate,$edate);

    $this->load->view('Reports/TodaysOperationTeamTaskPlannedByOurTeamProject',[
        'uid'                   =>  $uid,
        'user'                  =>  $user,
        'sdate'                 =>  $sdate,
        'edate'                 =>  $edate,
        'totalTasksUserByDatas' =>  $totalTasksUserByDatas,
        'dep_name'              =>  $dep_name
    ]);
  
}




public function AllStatusChangedRequiredRequest(){

    $this->load->model('Menu_model');
    $user           = $this->session->userdata('user');
    $data['user']   = $user;
    $uid            = $user['user_id'];
    $uyid           = $user['type_id'];
    $dt             = $this->Menu_model->get_utype($uyid);
    $dep_name = (is_array($dt) && !empty($dt) && isset($dt[0]->name)) ? $dt[0]->name : 'home';

    if(empty($user)){redirect('Menu/main');}

    if(isset($_POST['sdate'])){

        $sdate    = $this->input->Post('sdate');
        $edate    = $this->input->Post('edate');

    }else{
        $sdate    = date('Y-m-d');
        $edate    = date('Y-m-d');
    }

    $totalTasksUserByDatas   = $this->Report_model->GetAllStatusChangedRequiredRequest($uid,$sdate,$edate);

    $this->load->view('Reports/AllStatusChangedRequiredRequest',[
        'uid'                   =>  $uid,
        'user'                  =>  $user,
        'sdate'                 =>  $sdate,
        'edate'                 =>  $edate,
        'totalTasksUserByDatas' =>  $totalTasksUserByDatas,
        'dep_name'              =>  $dep_name
    ]);
  
}






  public function Calling_Outcome_AND_RP_Proposal_Conversion_Report() {
        $user = $this->session->userdata('user');
        if (empty($user)) {
            redirect('Menu/main');
        }
        $uid        = $user['user_id'];
        $uyid       = $user['type_id'];
        $dt         = $this->Menu_model->get_utype($uyid);
        $dep_name   = isset($dt[0]->name) ? $dt[0]->name : 'default';
        $cdate      = date("Y-m-d");
        $admin_id_filter    = $this->input->Post('admin_id_filter');
        $rm_filter          = $this->input->Post('rm_filter');
        $curFinancialDate       = $this->Menu_model->getFinancialYearRange();
    if(isset($_POST['admin_id_filter'])){
        $sdate                  = $this->input->Post('sdate');
        $edate                  = $this->input->Post('edate');
        if(isset($admin_id_filter)){
            if($admin_id_filter == 'all'){
                $admin_id_filter = 'all';
            }else{
                $uid = $admin_id_filter;
            }
            if(isset($rm_filter)){
                if($rm_filter =='all'){
                    $rm_filter = 'all';
                }else{
                    $uid = $rm_filter;
                }
            }
        }
    }else{
        $sdate              = date('Y-m-d', strtotime('-1 month'));
        $sdate              = "2026-04-01";
        $edate              = date('Y-m-d');

       
        $start_financial_date   = $curFinancialDate['start_date'];
        // $edate                  = $curFinancialDate['end_date'];


        $rm_filter          = 'all';
        $admin_id_filter    = $uid;
    }
        if($rm_filter !=='all'){
           $uid = $rm_filter;
        }else{
          $uid = $admin_id_filter;
        }

        $mdata      = $this->Report_model->RMCMCallingOutcomeANDRPProposalConversionDatas($uid,$admin_id_filter,$rm_filter,$sdate,$edate);
      
        $this->load->view('Reports/Calling_Outcome_AND_RP_Proposal_Conversion_Report', [
            'uid'       => $uid,
            'dep_name'  =>$dt,
            'ctype_id'  =>$uyid,
            'user'      => $user,
            'dep_name'  =>$dep_name,
            'cdate'     =>$cdate,
            'mdata'     =>$mdata,
            'sdate'     =>$sdate,
            'edate'     =>$edate,
            'admin_id_filter'=>$admin_id_filter,
            'rm_filter'=>$rm_filter,
            
        ]);
    }


  public function Calling_Outcome_AND_RP_Proposal_Conversion_ReportList($cid,$sdate,$edate,$rtype) {
        $user = $this->session->userdata('user');
        if (empty($user)) {
            redirect('Menu/main');
        }
        $uid        = $user['user_id'];
        $uyid       = $user['type_id'];
        $dt         = $this->Menu_model->get_utype($uyid);
        $dep_name   = isset($dt[0]->name) ? $dt[0]->name : 'default';

        $cmpDatas   = $this->Menu_model->GetCompanyDataUsingCID($cid);
        
        $mdata      = $this->Report_model->RMCMCallingOutcomeANDRPProposalConversionDatasLists($cid,$sdate,$edate,$rtype);

        $this->load->view('Reports/Calling_Outcome_AND_RP_Proposal_Conversion_ReportList', [
            'uid'       => $uid,
            'dep_name'  => $dt,
            'ctype_id'  => $uyid,
            'user'      => $user,
            'dep_name'  => $dep_name,
            'cdate'     => $cdate,
            'mdata'     => $mdata,
            'sdate'     => $sdate,
            'edate'     => $edate,
            'rtype'     => $rtype,
            'cid'       => $cid,
            'cmpDatas'  => $cmpDatas
            
        ]);

    }





// *************************** Start Report BD wise **************************

/**
 * ReviewReport BD wise
 *
 * This function is used to generate the report based on the
 * Reporting Manager and the date range provided by the user.
 *
 * @return void
 */
  public function ReviewReportBDwise() {
        $user = $this->session->userdata('user');
        if (empty($user)) {
            redirect('Menu/main');
        }
        $uid        = $user['user_id'];
        $uyid       = $user['type_id'];
        $dt         = $this->Menu_model->get_utype($uyid);
        $dep_name   = isset($dt[0]->name) ? $dt[0]->name : 'default';
        $cdate      = date("Y-m-d");

        $this->load->model('SalesReviews_model');

        if(isset($_POST['sdate'])){

            $sdate                          = $this->input->Post('sdate');
            $edate                          = $this->input->Post('edate');
            $selected_review_types          = $this->input->Post('selected_review_types');

        }else{
            $selected_review_types          = 'all';
            $current_date                   = date('Y-m-d');
            $sdate                          = date('Y-m-d', strtotime('-1 month', strtotime($current_date)));
            $edate                          = date('Y-m-d');

        }


        $review_types       = $this->SalesReviews_model->GetAllReviewTypesonTable1();
        $mdata              = $this->Report_model->ReviewReportBDwiseDetails($uid,$sdate,$edate,$selected_review_types);
      
        $this->load->view('Reports/ReviewReportBDwise', [
            'uid'               => $uid,
            'dep_name'          => $dt,
            'ctype_id'          => $uyid,
            'user'              => $user,
            'dep_name'          => $dep_name,
            'cdate'             => $cdate,
            'mdata'             => $mdata,
            'sdate'             => $sdate,
            'edate'             => $edate,
            'review_types'      => $review_types,
            'selected_review_types'      => $selected_review_types
            
        ]);
    }



  public function SingleReviewReportBDwiseDetails($reviewID) {
        $user = $this->session->userdata('user');
        if (empty($user)) {
            redirect('Menu/main');
        }
        $uid        = $user['user_id'];
        $uyid       = $user['type_id'];
        $dt         = $this->Menu_model->get_utype($uyid);
        $dep_name   = isset($dt[0]->name) ? $dt[0]->name : 'default';
        $cdate      = date("Y-m-d");

        $this->load->model('Report_model');
        $this->load->model('SalesReviews_model');

        $mdata              = $this->Report_model->ReviewReportBDwiseDetailsSingleReviewID($reviewID);

        // dd($mdata);

        $this->load->view('Reports/SingleReviewReportBDwiseDetails', [
            'uid'               => $uid,
            'dep_name'          => $dt,
            'ctype_id'          => $uyid,
            'user'              => $user,
            'dep_name'          => $dep_name,
            'mdata'             => $mdata
            
        ]);
    }



      public function ReviewReportBDwiseDetails($reviewID,$to_uid) {
        $user = $this->session->userdata('user');
        if (empty($user)) {
            redirect('Menu/main');
        }
        $uid        = $user['user_id'];
        $uyid       = $user['type_id'];
        $dt         = $this->Menu_model->get_utype($uyid);
        $dep_name   = isset($dt[0]->name) ? $dt[0]->name : 'default';
        $cdate      = date("Y-m-d");

        $this->load->model('Report_model');
        $this->load->model('SalesReviews_model');

        $mdata              = $this->Report_model->ReviewReportBDwiseDetailsID($reviewID,$to_uid);
        
        // dd($mdata);

        $this->load->view('Reports/ReviewReportBDwiseDetails', [
            'uid'               => $uid,
            'dep_name'          => $dt,
            'ctype_id'          => $uyid,
            'user'              => $user,
            'dep_name'          => $dep_name,
            'mdata'             => $mdata
            
        ]);
    }


// *************************** End Report BD wise ****************************


















}
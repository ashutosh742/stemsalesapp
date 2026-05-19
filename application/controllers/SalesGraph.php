<?php
defined('BASEPATH') OR exit('No direct script access allowed');

date_default_timezone_set("Asia/Kolkata");
include 'Menu.php';
class SalesGraph extends Menu {


    public function phpinfoprint() {
        phpinfo();
        exit;
    }



    public function Dashboard(){

        $user           = $this->session->userdata('user');
        $data['user']   = $user;
        $uid            = $user['user_id'];
        $uyid           = $user['type_id'];
        $this->load->model('Menu_model');
        $this->load->model('SalesGraph_Model');
        $graphFolder = 'Data Analysis';
        $dt             =   $this->Menu_model->get_utype($uyid);
        $dep_name       = $dt[0]->name;
        $tdate          = date("Y-m-d");
         if(!empty($user)){
            $this->load->view($graphFolder.'/index',['uid'=>$uid,'user'=>$user]);
        }else{
           redirect('Menu/main');
         }
    }


    public function CompanyWhichNoStatusChange(){

        $user           = $this->session->userdata('user');
        $data['user']   = $user;
        $uid            = $user['user_id'];
        $uyid           = $user['type_id'];
        $this->load->model('Menu_model');
        $this->load->model('SalesGraph_Model');
      
        $graphFolder = 'Data Analysis';

        $dt             =   $this->Menu_model->get_utype($uyid);
        $category       =   $this->Menu_model->GetCategories();
        $dep_name       = $dt[0]->name;

        $tdate          = date("Y-m-d");
        $alllogData     =   $this->SalesGraph_Model->GetAllCompulsiveAndNeedYourAttentioninGraph($uid,$tdate);

         if(!empty($user)){
            $this->load->view($graphFolder.'/CompanyWhichNoStatusChange',['uid'=>$uid,'user'=>$user,'alllogData'=>$alllogData,'category'=>$category]);
        }else{
             redirect('Menu/main');
        }
    }


    public function GetAllUserCurrentDayActivityOnAPPGraph(){
   
        $user           = $this->session->userdata('user');
        $data['user']   = $user;
        $uid            = $user['user_id'];
        $uyid           = $user['type_id'];
        $pdate          = date("Y-m-d");
        $this->load->model('Menu_model');
        $this->load->library('session');
        $dt             = $this->Menu_model->get_utype($uyid);
        $dep_name       = $dt[0]->name;
        $tdate = date("Y-m-d");

        $getAllUserData    = $this->Menu_model->GetDailyTeamPlannerSummaryWwithLocationShareNew($uid,$tdate);
        // $getAllUserData    = $this->SalesGraph_Model->GetDailyTeamPlannerSummaryWwithLocationShareNewGraph($uid,$tdate);

        if(!empty($user)){
            $this->load->view('Data Analysis/GetAllUserCurrentDayActivityOnAPPGraph',['uid'=>$uid,'user'=>$user,'getAllUserData'=>$getAllUserData]);
        }else{
            redirect('Menu/main');
        }
    }


    
    public function TaskwiseDetailsAnalysis(){
   
        $user           = $this->session->userdata('user');
        $data['user']   = $user;
        $uid            = $user['user_id'];
        $uyid           = $user['type_id'];
        $pdate          = date("Y-m-d");
        $this->load->model('Menu_model');
        $this->load->model('SalesGraph_Model');
        $this->load->library('session');
        $dt             = $this->Menu_model->get_utype($uyid);
        $dep_name       = $dt[0]->name;
        $tdate = date("Y-m-d");
        $graphFolder = 'Data Analysis';

        $getAllUserData    = $this->SalesGraph_Model->GetTotalTaskDataByUserIdDaily($uid,$tdate);

        // $getAllUserData    = $this->SalesGraph_Model->GetDailyTeamPlannerSummaryWwithLocationShareNewGraph($uid,$tdate);

        if(!empty($user)){
            $this->load->view($graphFolder.'/TaskwiseDetailsAnalysis',['uid'=>$uid,'user'=>$user,'getAllUserData'=>$getAllUserData]);
        }else{
            redirect('Menu/main');
        }
    }


    public function AllTimesTaskwiseDetailsAnalysis(){
   
        $user           = $this->session->userdata('user');
        $data['user']   = $user;
        $uid            = $user['user_id'];
        $uyid           = $user['type_id'];
        $pdate          = date("Y-m-d");
        $this->load->model('Menu_model');
        $this->load->model('SalesGraph_Model');
        $this->load->library('session');
        $dt             = $this->Menu_model->get_utype($uyid);
        $dep_name       = $dt[0]->name;
        $tdate = date("Y-m-d");
        $graphFolder = 'Data Analysis';

        $getAllUserData    = $this->SalesGraph_Model->AllTimesTaskwiseDetailsAnalysisData($uid,$tdate);

        // $getAllUserData    = $this->SalesGraph_Model->GetDailyTeamPlannerSummaryWwithLocationShareNewGraph($uid,$tdate);

        if(!empty($user)){
            $this->load->view($graphFolder.'/AllTimesTaskwiseDetailsAnalysis',['uid'=>$uid,'user'=>$user,'getAllUserData'=>$getAllUserData]);
        }else{
            redirect('Menu/main');
        }
    }
    public function GetAllUserAllTimeActivityOnAPPGraph(){
   
        $user           = $this->session->userdata('user');
        $data['user']   = $user;
        $uid            = $user['user_id'];
        $uyid           = $user['type_id'];
        $pdate          = date("Y-m-d");
        $this->load->model('Menu_model');
        $this->load->model('SalesGraph_Model');
        $this->load->library('session');
        $dt             = $this->Menu_model->get_utype($uyid);
        $dep_name       = $dt[0]->name;
        $tdate = date("Y-m-d");
        $graphFolder = 'Data Analysis';

        $getAllUserData    = $this->SalesGraph_Model->GetAllTeamPlannerSummaryWwithLocationShareNewGraph($uid,$tdate);
        // echo $this->db->last_query();
        // die;
        // $getAllUserData    = $this->SalesGraph_Model->GetDailyTeamPlannerSummaryWwithLocationShareNewGraph($uid,$tdate);

        if(!empty($user)){
            $this->load->view($graphFolder.'/GetAllUserAllTimeActivityOnAPPGraph',['uid'=>$uid,'user'=>$user,'getAllUserData'=>$getAllUserData]);
        }else{
            redirect('Menu/main');
        }
    }
    public function GetTodaysAllUserAllTimeActivityOnAPPGraph(){
   
        $user           = $this->session->userdata('user');
        $data['user']   = $user;
        $uid            = $user['user_id'];
        $uyid           = $user['type_id'];
        $pdate          = date("Y-m-d");
        $this->load->model('Menu_model');
        $this->load->model('SalesGraph_Model');
        $this->load->library('session');
        $dt             = $this->Menu_model->get_utype($uyid);
        $dep_name       = $dt[0]->name;
        $tdate = date("Y-m-d");
        $graphFolder = 'Data Analysis';
        // $tdate = '2025-03-15';
        $getAllUserData    = $this->SalesGraph_Model->GetTodaysAllTeamPlannerSummaryWwithLocationShareNewGraph($uid,$tdate);
        // echo $this->db->last_query();
        // die;
        // $getAllUserData    = $this->SalesGraph_Model->GetDailyTeamPlannerSummaryWwithLocationShareNewGraph($uid,$tdate);

        if(!empty($user)){
            $this->load->view($graphFolder.'/GetTodaysAllUserAllTimeActivityOnAPPGraph',['uid'=>$uid,'user'=>$user,'getAllUserData'=>$getAllUserData]);
        }else{
            redirect('Menu/main');
        }
    }

    public function TeamWiseTaskGraph(){
   
        $user           = $this->session->userdata('user');
        $data['user']   = $user;
        $uid            = $user['user_id'];
        $uyid           = $user['type_id'];
        $pdate          = date("Y-m-d");
        $this->load->model('Menu_model');
        $this->load->model('SalesGraph_Model');
        $this->load->library('session');
        $dt             = $this->Menu_model->get_utype($uyid);
        $dep_name       = $dt[0]->name;
        $tdate = date("Y-m-d");
        $graphFolder = 'Data Analysis';
        // $tdate = '2025-03-15';
        $getAllUserData    = $this->SalesGraph_Model->GetTodaysAllTeamPlannerSummaryWwithLocationShareNewGraph($uid,$tdate);


        $fields_to_sum = [
            'total_task', 'approved_task', 'pending_approved', 'complete_task',
            'pending_task', 'total_autotask', 'after_task', 'complete_autotask',
            'pending_autotask', 'total_reject','pending_for_assign_after_reject_task','admin_create_request_for_self_assign','task_assignd_by_admin_after_reject','task_assignd_by_user_after_reject', 'action_yes_purpose_yes', 
            'action_yes_purpose_no', 'action_no_purpose_no', 'call_task',
            'email_task', 'scheduled_meeting_task', 'barg_meeting_task', 
            'whatsapp_activity', 'write_mom', 'write_proposal',
            // 'total_task_time',
            // 'total_plan_time_on_planner',
            // 'total_plan_time_with_autotask'
          ];
        $sums = array_fill_keys($fields_to_sum, 0);
              
        // Sum values
        foreach ($getAllUserData as $obj) {
          foreach ($fields_to_sum as $field) {
              if (isset($obj->$field)) {
                  $sums[$field] += $obj->$field;
              }
          }
        }


        // echo $this->db->last_query();
        // die;
        // $getAllUserData    = $this->SalesGraph_Model->GetDailyTeamPlannerSummaryWwithLocationShareNewGraph($uid,$tdate);

        if(!empty($user)){
            $this->load->view($graphFolder.'/TeamWiseTaskGraph',['uid'=>$uid,'user'=>$user,'getAllUserData'=>$getAllUserData,'sums'=>$sums]);
        }else{
            redirect('Menu/main');
        }
    }



    public function TodaysStatusWiseTask(){
   
        $user           = $this->session->userdata('user');
        $data['user']   = $user;
        $uid            = $user['user_id'];
        $uyid           = $user['type_id'];
        $pdate          = date("Y-m-d");
        $this->load->model('Menu_model');
        $this->load->model('SalesGraph_Model');
        $this->load->library('session');
        $dt             = $this->Menu_model->get_utype($uyid);
        $dep_name       = $dt[0]->name;
        $tdate = date("Y-m-d");
        $graphFolder = 'Data Analysis';
        // $tdate = '2025-03-15';
        $getAllUserData    = $this->SalesGraph_Model->GetTodaysStatusWiseTaskCount($uid,$tdate);
      
       
        if(!empty($user)){
            $this->load->view($graphFolder.'/TodaysStatusWiseTask',['uid'=>$uid,'user'=>$user,'getAllUserData'=>$getAllUserData]);
        }else{
            redirect('Menu/main');
        }
    }
    public function TodaysActionWiseTask(){
   
        $user           = $this->session->userdata('user');
        $data['user']   = $user;
        $uid            = $user['user_id'];
        $uyid           = $user['type_id'];
        $pdate          = date("Y-m-d");
        $this->load->model('Menu_model');
        $this->load->model('SalesGraph_Model');
        $this->load->library('session');
        $dt             = $this->Menu_model->get_utype($uyid);
        $dep_name       = $dt[0]->name;
        $tdate = date("Y-m-d");
        $graphFolder = 'Data Analysis';
        // $tdate = '2025-03-15';
        $getAllUserData    = $this->SalesGraph_Model->GetTodaysActionWiseTaskCount($uid,$tdate);
      
        // dd($getAllUserData);
       
        if(!empty($user)){
            $this->load->view($graphFolder.'/TodaysActionWiseTask',['uid'=>$uid,'user'=>$user,'getAllUserData'=>$getAllUserData]);
        }else{
            redirect('Menu/main');
        }
    }
    public function TodaysTimeSlotWiseFunnelAnalysis(){
   
        $user           = $this->session->userdata('user');
        $data['user']   = $user;
        $uid            = $user['user_id'];
        $uyid           = $user['type_id'];
        $pdate          = date("Y-m-d");
        $this->load->model('Menu_model');
        $this->load->model('SalesGraph_Model');
        $this->load->library('session');
        $dt             = $this->Menu_model->get_utype($uyid);
        $dep_name       = $dt[0]->name;
        $tdate = date("Y-m-d");
        $graphFolder = 'Data Analysis';
        // $tdate = '2025-03-15';
        $getAllUserData    = $this->SalesGraph_Model->GetTodaysTimeSlotWiseTaskCount($uid,$tdate);
      
        // dd($getAllUserData);
       
        if(!empty($user)){
            $this->load->view($graphFolder.'/TodaysTimeSlotWiseFunnelAnalysis',['uid'=>$uid,'user'=>$user,'getAllUserData'=>$getAllUserData]);
        }else{
            redirect('Menu/main');
        }
    }
    public function TodaysStatusConversionWiseTaskAnalysis(){
   
        $user           = $this->session->userdata('user');
        $data['user']   = $user;
        $uid            = $user['user_id'];
        $uyid           = $user['type_id'];
        $pdate          = date("Y-m-d");
        $this->load->model('Menu_model');
        $this->load->model('SalesGraph_Model');
        $this->load->library('session');
        $dt             = $this->Menu_model->get_utype($uyid);
        $dep_name       = $dt[0]->name;
        $tdate = date("Y-m-d");
        $graphFolder = 'Data Analysis';
        // $tdate = '2025-03-15';
        $getAllUserData    = $this->SalesGraph_Model->GetTodaysStatusConversionWiseTaskCount($uid,$tdate);
      
        if(!empty($user)){
            $this->load->view($graphFolder.'/TodaysStatusConversionWiseTaskAnalysis',['uid'=>$uid,'user'=>$user,'getAllUserData'=>$getAllUserData]);
        }else{
            redirect('Menu/main');
        }
    }


    public function TodaysStatusConversionWithDateTaskAnalysis(){
   
        $user           = $this->session->userdata('user');
        $data['user']   = $user;
        $uid            = $user['user_id'];
        $uyid           = $user['type_id'];
        $pdate          = date("Y-m-d");
        $this->load->model('Menu_model');
        $this->load->model('SalesGraph_Model');
        $this->load->library('session');
        $dt             = $this->Menu_model->get_utype($uyid);
        $dep_name       = $dt[0]->name;
        $tdate = date("Y-m-d");
        $graphFolder = 'Data Analysis';
        // $tdate = '2025-03-15';
        $getAllUserData    = $this->SalesGraph_Model->GetTodaysStatusConversionWiseTaskWithDateCount($uid,$tdate);

        // dd($getAllUserData);
      
        if(!empty($user)){
            $this->load->view($graphFolder.'/TodaysStatusConversionWithDateTaskAnalysis',['uid'=>$uid,'user'=>$user,'getAllUserData'=>$getAllUserData]);
        }else{
            redirect('Menu/main');
        }
    }


    public function CheckTodaysAllTodaysPlannerLog(){
   
        $user           = $this->session->userdata('user');
        $data['user']   = $user;
        $uid            = $user['user_id'];
        $uyid           = $user['type_id'];
        $pdate          = date("Y-m-d");
        $this->load->model('Menu_model');
        $this->load->model('SalesGraph_Model');
        $this->load->library('session');
        $dt             = $this->Menu_model->get_utype($uyid);
        $dep_name       = $dt[0]->name;
        $tdate = date("Y-m-d");
        $graphFolder = 'Data Analysis';
        // $tdate = '2025-03-15';
        $getAllUserData    = $this->SalesGraph_Model->GetTodaysAllTodaysPlannerLog($uid,$tdate);

        // dd($getAllUserData);
      
        if(!empty($user)){
            $this->load->view($graphFolder.'/CheckTodaysAllTodaysPlannerLog',['uid'=>$uid,'user'=>$user,'getAllUserData'=>$getAllUserData]);
        }else{
            redirect('Menu/main');
        }
    }


    public function CheckTodaysPlannerCurrentYearFocusFunnelsLogByuser(){
   
        $user           = $this->session->userdata('user');
        $data['user']   = $user;
        $uid            = $user['user_id'];
        $uyid           = $user['type_id'];
        $pdate          = date("Y-m-d");
        $this->load->model('Menu_model');
        $this->load->model('SalesGraph_Model');
        $this->load->library('session');
        $dt             = $this->Menu_model->get_utype($uyid);
        $dep_name       = $dt[0]->name;
        $tdate = date("Y-m-d");
        $graphFolder = 'Data Analysis';
        // $tdate = '2025-03-15';

        $oneYearAgo = date("Y", strtotime("-1 year"));
        $cdate = Date("Y-m-d");

        $getAllUserData    = $this->SalesGraph_Model->GetTodaysPlannerCurrentYearFocusFunnelsLogByuser($uid,$oneYearAgo,$cdate);

        // dd($getAllUserData);
      
        if(!empty($user)){
            $this->load->view($graphFolder.'/CheckTodaysPlannerCurrentYearFocusFunnelsLogByuser',['uid'=>$uid,'user'=>$user,'getAllUserData'=>$getAllUserData]);
        }else{
            redirect('Menu/main');
        }
    }
    public function CheckAllPlannerCurrentYearFocusFunnelsLogByuser(){
   
        $user           = $this->session->userdata('user');
        $data['user']   = $user;
        $uid            = $user['user_id'];
        $uyid           = $user['type_id'];
        $pdate          = date("Y-m-d");
        $this->load->model('Menu_model');
        $this->load->model('SalesGraph_Model');
        $this->load->library('session');
        $dt             = $this->Menu_model->get_utype($uyid);
        $dep_name       = $dt[0]->name;
        $tdate = date("Y-m-d");
        $graphFolder = 'Data Analysis';
        // $tdate = '2025-03-15';

        $oneYearAgo = date("Y", strtotime("-1 year"));
        $cdate = Date("Y-m-d");

        $getAllUserData    = $this->SalesGraph_Model->GetAllPlannerCurrentYearFocusFunnelsLogByuser($uid,$oneYearAgo,$cdate);

        // dd($getAllUserData);
      
        if(!empty($user)){
            $this->load->view($graphFolder.'/CheckAllPlannerCurrentYearFocusFunnelsLogByuser',['uid'=>$uid,'user'=>$user,'getAllUserData'=>$getAllUserData]);
        }else{
            redirect('Menu/main');
        }
    }
    public function TaskVsCompanyStatusAnalysis(){
   
        $user           = $this->session->userdata('user');
        $data['user']   = $user;
        $uid            = $user['user_id'];
        $uyid           = $user['type_id'];
        $pdate          = date("Y-m-d");
        $this->load->model('Menu_model');
        $this->load->model('SalesGraph_Model');
        $this->load->library('session');
        $dt             = $this->Menu_model->get_utype($uyid);
        $dep_name       = $dt[0]->name;
        $tdate = date("Y-m-d");
        $graphFolder = 'Data Analysis';
        // $tdate = '2025-03-15';

        $oneYearAgo = date("Y", strtotime("-1 year"));
        $cdate = Date("Y-m-d");

        $getAllUserData    = $this->SalesGraph_Model->getTodaysTaskVsCompanyStatusAnalysis($uid,$cdate);
        // echo $this->db->last_query();
        // dd($getAllUserData);
      
        if(!empty($user)){
            $this->load->view($graphFolder.'/TaskVsCompanyStatusAnalysis',['uid'=>$uid,'user'=>$user,'getAllUserData'=>$getAllUserData]);
        }else{
            redirect('Menu/main');
        }
    }
    public function CurrentYearTaskVsCompanyStatusAnalysis(){
   
        $user           = $this->session->userdata('user');
        $data['user']   = $user;
        $uid            = $user['user_id'];
        $uyid           = $user['type_id'];
        $pdate          = date("Y-m-d");
        $this->load->model('Menu_model');
        $this->load->model('SalesGraph_Model');
        $this->load->library('session');
        $dt             = $this->Menu_model->get_utype($uyid);
        $dep_name       = $dt[0]->name;
        $tdate = date("Y-m-d");
        $graphFolder = 'Data Analysis';
        // $tdate = '2025-03-15';

        $oneYearAgo = date("Y", strtotime("-1 year"));
        $cdate = Date("Y-m-d");

        $getAllUserData    = $this->SalesGraph_Model->getAlltimesTaskVsCompanyStatusAnalysis($uid,$cdate);
        // echo $this->db->last_query();
        // dd($getAllUserData);
      
        if(!empty($user)){
            $this->load->view($graphFolder.'/CurrentYearTaskVsCompanyStatusAnalysis',['uid'=>$uid,'user'=>$user,'getAllUserData'=>$getAllUserData]);
        }else{
            redirect('Menu/main');
        }
    }


    public function CurrentYearStatusConversionTaskVsCompanyStatusAnalysis(){
   
        $user           = $this->session->userdata('user');
        $data['user']   = $user;
        $uid            = $user['user_id'];
        $uyid           = $user['type_id'];
        $pdate          = date("Y-m-d");
        $this->load->model('Menu_model');
        $this->load->model('SalesGraph_Model');
        $this->load->library('session');
        $dt             = $this->Menu_model->get_utype($uyid);
        $dep_name       = $dt[0]->name;
        $tdate = date("Y-m-d");
        $graphFolder = 'Data Analysis';
        // $tdate = '2025-03-15';

        $oneYearAgo = date("Y", strtotime("-1 year"));
        $cdate = Date("Y-m-d");

        $getAllUserData    = $this->SalesGraph_Model->GetCurrentYearsStatusConversionWiseTaskCount($uid,$cdate);
        // echo $this->db->last_query();
        // dd($getAllUserData);
      
        if(!empty($user)){
            $this->load->view($graphFolder.'/CurrentYearStatusConversionTaskVsCompanyStatusAnalysis',['uid'=>$uid,'user'=>$user,'getAllUserData'=>$getAllUserData]);
        }else{
            redirect('Menu/main');
        }
    }


    public function CheckDateWiseTaskCompanyStatusConversionRate(){
   
        $user           = $this->session->userdata('user');
        $data['user']   = $user;
        $uid            = $user['user_id'];
        $uyid           = $user['type_id'];
        $pdate          = date("Y-m-d");
        $this->load->model('Menu_model');
        $this->load->model('SalesGraph_Model');
        $this->load->library('session');
        $dt             = $this->Menu_model->get_utype($uyid);
        $dep_name       = $dt[0]->name;
        $tdate = date("Y-m-d");
        $graphFolder = 'Data Analysis';
        // $tdate = '2025-03-15';

        $oneYearAgo = date("Y", strtotime("-1 year"));
        $cdate = Date("Y-m-d");

        $getAllUserData    = $this->SalesGraph_Model->GetDateWiseTaskCompanyStatusConversionRate($uid,$cdate);
        // echo $this->db->last_query();
        // dd($getAllUserData);
      
        if(!empty($user)){
            $this->load->view($graphFolder.'/CheckDateWiseTaskCompanyStatusConversionRate',['uid'=>$uid,'user'=>$user,'getAllUserData'=>$getAllUserData]);
        }else{
            redirect('Menu/main');
        }
    }
    public function GetTodaysCompanyStatusConversionAnalysis(){
   
        $user           = $this->session->userdata('user');
        $data['user']   = $user;
        $uid            = $user['user_id'];
        $uyid           = $user['type_id'];
        $pdate          = date("Y-m-d");
        $this->load->model('Menu_model');
        $this->load->model('SalesGraph_Model');
        $this->load->library('session');
        $dt             = $this->Menu_model->get_utype($uyid);
        $dep_name       = $dt[0]->name;
        $cdate = date("Y-m-d");
        $graphFolder = 'Data Analysis';

        $oneYearAgo = date("Y", strtotime("-1 year"));

        $getAllUserData    = $this->SalesGraph_Model->GetTodaysCompanyStatusConversionWiseTaskCount($uid,$cdate);
        // echo $this->db->last_query();
        // dd($getAllUserData);
      
        if(!empty($user)){
            $this->load->view($graphFolder.'/GetTodaysCompanyStatusConversionAnalysis',['uid'=>$uid,'user'=>$user,'getAllUserData'=>$getAllUserData]);
        }else{
            redirect('Menu/main');
        }
    }



    public function CheckCurrentYearsCompanyStatusConversionRate(){
   
        $user           = $this->session->userdata('user');
        $data['user']   = $user;
        $uid            = $user['user_id'];
        $uyid           = $user['type_id'];
        $pdate          = date("Y-m-d");
        $this->load->model('Menu_model');
        $this->load->model('SalesGraph_Model');
        $this->load->library('session');
        $dt             = $this->Menu_model->get_utype($uyid);
        $dep_name       = $dt[0]->name;
        $cdate = date("Y-m-d");
        $graphFolder = 'Data Analysis';
      

        $oneYearAgo = date("Y", strtotime("-1 year"));

        $getAllUserData    = $this->SalesGraph_Model->GetCompanyStatusConversionRate($uid,$cdate);
        // echo $this->db->last_query();
        // dd($getAllUserData);
      
        if(!empty($user)){
            $this->load->view($graphFolder.'/CheckCurrentYearsCompanyStatusConversionRate',['uid'=>$uid,'user'=>$user,'getAllUserData'=>$getAllUserData]);
        }else{
            redirect('Menu/main');
        }
    }
    public function CheckTodaysCompanyStatusConversionRate(){
   
        $user           = $this->session->userdata('user');
        $data['user']   = $user;
        $uid            = $user['user_id'];
        $uyid           = $user['type_id'];
        $pdate          = date("Y-m-d");
        $this->load->model('Menu_model');
        $this->load->model('SalesGraph_Model');
        $this->load->library('session');
        $dt             = $this->Menu_model->get_utype($uyid);
        $dep_name       = $dt[0]->name;
        $cdate = date("Y-m-d");
        $graphFolder = 'Data Analysis';
       
        $oneYearAgo = date("Y", strtotime("-1 year"));

        $getAllUserData    = $this->SalesGraph_Model->GetTodaysCompanyStatusConversionRate($uid,$cdate);
        // echo $this->db->last_query();
        // dd($getAllUserData);
      
        if(!empty($user)){
            $this->load->view($graphFolder.'/CheckTodaysCompanyStatusConversionRate',['uid'=>$uid,'user'=>$user,'getAllUserData'=>$getAllUserData]);
        }else{
            redirect('Menu/main');
        }
    }



    public function TodaysUserDayAnalysis(){
   
        $user           = $this->session->userdata('user');
        $data['user']   = $user;
        $uid            = $user['user_id'];
        $uyid           = $user['type_id'];
        $pdate          = date("Y-m-d");
        $this->load->model('Menu_model');
        $this->load->model('SalesGraph_Model');
        $this->load->library('session');
        $dt             = $this->Menu_model->get_utype($uyid);
        $dep_name       = $dt[0]->name;
        $cdate = date("Y-m-d");
        $graphFolder = 'Data Analysis';
       
        $oneYearAgo = date("Y", strtotime("-1 year"));

        $getAllUserData    = $this->SalesGraph_Model->GetTodaysUserDayManagement($uid,$cdate);
        // echo $this->db->last_query();
        // dd($getAllUserData);
      
        if(!empty($user)){
            $this->load->view($graphFolder.'/TodaysUserDayAnalysis',['uid'=>$uid,'user'=>$user,'getAllUserData'=>$getAllUserData]);
        }else{
            redirect('Menu/main');
        }
    }

    public function TodaysUserDayMapAnalysis(){
   
        $user           = $this->session->userdata('user');
        $data['user']   = $user;
        $uid            = $user['user_id'];
        $uyid           = $user['type_id'];
        $pdate          = date("Y-m-d");
        $this->load->model('Menu_model');
        $this->load->model('SalesGraph_Model');
        $this->load->library('session');
        $dt             = $this->Menu_model->get_utype($uyid);
        $dep_name       = $dt[0]->name;
        $cdate = date("Y-m-d");
        $graphFolder = 'Data Analysis';
       
        $oneYearAgo = date("Y", strtotime("-1 year"));

        $getAllUserData    = $this->SalesGraph_Model->GetTodaysUserDayManagement($uid,$cdate);
        // echo $this->db->last_query();
        // dd($getAllUserData);
      
        if(!empty($user)){
            $this->load->view($graphFolder.'/TodaysUserDayMapAnalysis',['uid'=>$uid,'user'=>$user,'getAllUserData'=>$getAllUserData]);
        }else{
            redirect('Menu/main');
        }
    }



    public function CurrerntYearDayAnalysis(){
   
        $user           = $this->session->userdata('user');
        $data['user']   = $user;
        $uid            = $user['user_id'];
        $uyid           = $user['type_id'];
        $pdate          = date("Y-m-d");
        $this->load->model('Menu_model');
        $this->load->model('SalesGraph_Model');
        $this->load->library('session');
        $dt             = $this->Menu_model->get_utype($uyid);
        $dep_name       = $dt[0]->name;
        $cdate = date("Y-m-d");
        $graphFolder = 'Data Analysis';
       
        $oneYearAgo = date("Y", strtotime("-1 year"));

        $getAllUserData    = $this->SalesGraph_Model->GetCurrentYearUserDayManagement($uid,$cdate);
        // echo $this->db->last_query();
        // dd($getAllUserData);
      
        if(!empty($user)){
            $this->load->view($graphFolder.'/CurrerntYearDayAnalysis',['uid'=>$uid,'user'=>$user,'getAllUserData'=>$getAllUserData]);
        }else{
            redirect('Menu/main');
        }
    }

    
    public function CurrerntYearDayMapAnalysis(){
   
        $user           = $this->session->userdata('user');
        $data['user']   = $user;
        $uid            = $user['user_id'];
        $uyid           = $user['type_id'];
        $pdate          = date("Y-m-d");
        $this->load->model('Menu_model');
        $this->load->model('SalesGraph_Model');
        $this->load->library('session');
        $dt             = $this->Menu_model->get_utype($uyid);
        $dep_name       = $dt[0]->name;
        $cdate = date("Y-m-d");
        $graphFolder = 'Data Analysis';
       
        $oneYearAgo = date("Y", strtotime("-1 year"));

        $getAllUserData    = $this->SalesGraph_Model->GetCurrentYearUserDayManagement($uid,$cdate);
        // echo $this->db->last_query();
        // dd($getAllUserData);
      
        if(!empty($user)){
            $this->load->view($graphFolder.'/CurrerntYearDayMapAnalysis',['uid'=>$uid,'user'=>$user,'getAllUserData'=>$getAllUserData]);
        }else{
            redirect('Menu/main');
        }
    }




    public function CompaniesListThatDoNotHavePrimaryContactAnalysis(){
   
        $user           = $this->session->userdata('user');
        $data['user']   = $user;
        $uid            = $user['user_id'];
        $uyid           = $user['type_id'];
        $pdate          = date("Y-m-d");
        $this->load->model('Menu_model');
        $this->load->model('SalesGraph_Model');
        $this->load->library('session');
        $dt             = $this->Menu_model->get_utype($uyid);
        $dep_name       = $dt[0]->name;
        $cdate = date("Y-m-d");
        $graphFolder = 'Data Analysis';
       
        $oneYearAgo = date("Y", strtotime("-1 year"));

        $getAllUserData    = $this->Menu_model->GetAllCompaniesThatDoNotHavePrimaryContactDetails($uid,$cdate);
        // echo $this->db->last_query();
        // dd($getAllUserData);
      
        if(!empty($user)){
            $this->load->view($graphFolder.'/CompaniesListThatDoNotHavePrimaryContactAnalysis',['uid'=>$uid,'user'=>$user,'getAllUserData'=>$getAllUserData]);
        }else{
            redirect('Menu/main');
        }
    }


    public function CompanyThatHasPrimaryContactAnalysis(){
   
        $user           = $this->session->userdata('user');
        $data['user']   = $user;
        $uid            = $user['user_id'];
        $uyid           = $user['type_id'];
        $pdate          = date("Y-m-d");
        $this->load->model('Menu_model');
        $this->load->model('SalesGraph_Model');
        $this->load->library('session');
        $dt             = $this->Menu_model->get_utype($uyid);
        $dep_name       = $dt[0]->name;
        $cdate = date("Y-m-d");
        $graphFolder = 'Data Analysis';
       
        $oneYearAgo = date("Y", strtotime("-1 year"));

        $getAllUserData    = $this->SalesGraph_Model->GetAllCompaniesThatHasPrimaryContactDetails($uid,$cdate);
        // echo $this->db->last_query();
        // dd($getAllUserData);
      
        if(!empty($user)){
            $this->load->view($graphFolder.'/CompanyThatHasPrimaryContactAnalysis',['uid'=>$uid,'user'=>$user,'getAllUserData'=>$getAllUserData]);
        }else{
            redirect('Menu/main');
        }
    }






    // Task Planner Analysis Start Here .............. 

    public function TodaysUserPlannerManagementAnalysis(){
   
        $user           = $this->session->userdata('user');
        $data['user']   = $user;
        $uid            = $user['user_id'];
        $uyid           = $user['type_id'];
        $pdate          = date("Y-m-d");
        $this->load->model('Menu_model');
        $this->load->model('SalesGraph_Model');
        $this->load->library('session');
        $dt             = $this->Menu_model->get_utype($uyid);
        $dep_name       = $dt[0]->name;
        $cdate = date("Y-m-d");
        $graphFolder = 'Data Analysis';
        $cdate = '2025-04-19';

        if(isset($_POST['rm_filter'])){
            $rm_filter = $_POST['rm_filter'];
            $uid =  $rm_filter;
        }
        if(isset($_POST['admin_id_filter'])){
            $admin_id_filter = $_POST['admin_id_filter'];
            $uid =  $admin_id_filter;
            // echo $uid;
            // die;
        }
        if(isset($_POST['zone_filter'])){
            $zone_filter        = $_POST['zone_filter'];
            $target_user        = $_POST['target_user'];
            $commaSeparatedZone = implode(', ', $zone_filter);
            $uid =  $target_user;
        }

        
        $oneYearAgo = date("Y", strtotime("-1 year"));

        $getAllUserData    = $this->SalesGraph_Model->GetTodaysAllTeamPlannerSummaryWwithLocationShareNewGraph($uid,$cdate,$commaSeparatedZone);
        // echo $this->db->last_query();
   
        if(!empty($user)){
            $this->load->view($graphFolder.'/TodaysUserPlannerManagementAnalysis',['uid'=>$uid,'user'=>$user,'getAllUserData'=>$getAllUserData]);
        }else{
            redirect('Menu/main');
        }
    }


    public function TodaysTaskPlannerManagementAnalysis(){
   
        $user           = $this->session->userdata('user');
        $data['user']   = $user;
        $uid            = $user['user_id'];
        $uyid           = $user['type_id'];
        $pdate          = date("Y-m-d");
        $this->load->model('Menu_model');
        $this->load->model('SalesGraph_Model');
        $this->load->library('session');
        
        $dt             = $this->Menu_model->get_utype($uyid);
        $dep_name       = $dt[0]->name;
        $cdate          = date("Y-m-d");
        $graphFolder    = 'Data Analysis';
        // $cdate          = '2025-05-01';
       
        
        $admin_id_filter    = $_POST['admin_id_filter'];
        $selectedDate       = $_POST['selectedDate'];
        $rm_filter          = $_POST['rm_filter'];

        

        if(isset($_POST['rm_filter'])){
            $rm_filter = $_POST['rm_filter'];
            if($rm_filter !=='all'){
                $uid =  $rm_filter;
            }else{
                $rm_filter = 'all';
            }

            $udata = $this->Menu_model->get_userbyid($uid);
            $admin_id = $udata[0]->admin_id;
            $admin_id_filter  = $admin_id;
      
        }else{
            $rm_filter = 'all';
        }

        if(isset($_POST['selectedDate'])){
            $cdate = $_POST['selectedDate'];
        }else{
            $cdate = date("Y-m-d");
        }

        if(!isset($_POST['admin_id_filter'])){
            $admin_id_filter = $uid;
        }else{
            $admin_id_filter = $_POST['admin_id_filter'];
            if($admin_id_filter == 'all'){
                $admin_filter_type = $_POST['admin_id_filter'];
            }else{
                $admin_filter_type = '';
                $uid = $_POST['admin_id_filter'];

                if($rm_filter !=='all'){
                    $uid =  $rm_filter;
                }
            }
        }
             
        $oneYearAgo = date("Y", strtotime("-1 year"));

        $taskDatadatas    = $this->SalesGraph_Model->GetTodaysPlanedTaskTypeBYRoles($uid,$cdate,$admin_filter_type,$rm_filter);

        $groupedData = [];
        foreach ($taskDatadatas as $item) {
            $planner_user_id = $item->planner_user_id;
            if (!isset($groupedData[$planner_user_id])) {
                $groupedData[$planner_user_id] = [];
            }
            $groupedData[$planner_user_id][] = $item;
        }


  

        foreach ($groupedData as $userId => $tasks) {
            $totalPlanTaskTime = '';
            $totalReviewPlanTime = '';
        
            foreach ($tasks as $task) {
                if (!empty($task->total_plan_task_time)) {
                    $totalPlanTaskTime = $task->total_plan_task_time;
                }
                if (!empty($task->total_review_plan_time)) {
                    $totalReviewPlanTime = $task->total_review_plan_time;
                }
                if (!empty($task->name)) {
                    $name = $task->name;
                }
                if (!empty($task->user_type_name)) {
                    $user_type_name = $task->user_type_name;
                }
                if (!empty($task->tasktype)) {
                    $tasktype = $task->tasktype;
                }
                if (!empty($task->zone_name)) {
                    $zone_name = $task->zone_name;
                }
        
                // Remove the total_plan_task_time and total_review_plan_time from the task object
                unset($task->total_plan_task_time);
                unset($task->total_review_plan_time);
                // unset($task->name);
                // unset($task->user_type_name);
                // unset($task->zone_name);
                // unset($task->planner_user_id);
                // unset($task->action_id);
            }
        
            // Append the total times as the last element
            $groupedData[$userId][] = (object)[
                'name' => $name,
                'user_type_name' => $user_type_name,
                'zone_name' => $zone_name,
                'total_plan_task_time' => $totalPlanTaskTime,
                'total_review_plan_time' => $totalReviewPlanTime
            ];
        }
           
        if(!empty($user)){
            $this->load->view($graphFolder.'/TodaysTaskPlannerManagementAnalysis',['uid'=>$uid,'user'=>$user,'groupedData'=>$groupedData,'cdate'=>$cdate,'admin_id_filter'=>$admin_id_filter]);
        }else{
            redirect('Menu/main');
        }
    }

    public function MeetingsReportAnalysis(){
   
        $user           = $this->session->userdata('user');
        $data['user']   = $user;
        $uid            = $user['user_id'];
        $uyid           = $user['type_id'];
        $pdate          = date("Y-m-d");
        $this->load->model('Menu_model');
        $this->load->model('SalesGraph_Model');
        $this->load->library('session');
        
        $dt             = $this->Menu_model->get_utype($uyid);
        $dep_name       = $dt[0]->name;
        $cdate          = date("Y-m-d");
        $graphFolder    = 'Data Analysis';
        // $cdate          = '2025-05-01';
       
        
        $admin_id_filter    = $_POST['admin_id_filter'];
        $selectedDate       = $_POST['selectedDate'];
        $rm_filter          = $_POST['rm_filter'];

        

        if(isset($_POST['rm_filter'])){
            $rm_filter = $_POST['rm_filter'];
            if($rm_filter !=='all'){
                $uid =  $rm_filter;
            }else{
                $rm_filter = 'all';
            }

            $udata = $this->Menu_model->get_userbyid($uid);
            $admin_id = $udata[0]->admin_id;
            $admin_id_filter  = $admin_id;
      
        }else{
            $rm_filter = 'all';
        }

        if(isset($_POST['selectedDate'])){
            $cdate = $_POST['selectedDate'];
        }else{
            $cdate = date("Y-m-d");
        }

        if(!isset($_POST['admin_id_filter'])){
            $admin_id_filter = $uid;
        }else{
            $admin_id_filter = $_POST['admin_id_filter'];
            if($admin_id_filter == 'all'){
                $admin_filter_type = $_POST['admin_id_filter'];
            }else{
                $admin_filter_type = '';
                $uid = $_POST['admin_id_filter'];

                if($rm_filter !=='all'){
                    $uid =  $rm_filter;
                }
            }
        }
             
          if(isset($_POST['sdate'])){

            $sdate = $_POST['sdate'];
            $edate = $_POST['edate'];

            }else{
                $sdate = date('Y-m-d');
                $edate = date('Y-m-d');
            }

    
        $taskDatadatas    = $this->SalesGraph_Model->MeetingDetailsByRolesUidInSaleGraph($uid,$sdate,$edate,$admin_filter_type,$rm_filter);
        
        // dd($taskDatadatas);
        if(!empty($user)){
            $this->load->view($graphFolder.'/MeetingsReportAnalysis',['uid'=>$uid,'user'=>$user,'taskDatadatas'=>$taskDatadatas,'cdate'=>$cdate,'admin_id_filter'=>$admin_id_filter,'sdate'=>$sdate,'edate'=>$edate ]);
        }else{
            redirect('Menu/main');
        }
    }


    public function CompleteMeetingsReportAnalysis(){
   
        $user           = $this->session->userdata('user');
        $data['user']   = $user;
        $uid            = $user['user_id'];
        $uyid           = $user['type_id'];
        $pdate          = date("Y-m-d");
        $this->load->model('Menu_model');
        $this->load->model('SalesGraph_Model');
        $this->load->library('session');
        
        $dt             = $this->Menu_model->get_utype($uyid);
        $dep_name       = $dt[0]->name;
        $cdate          = date("Y-m-d");
        $graphFolder    = 'Data Analysis';
        // $cdate          = '2025-05-01';
       
        
        $admin_id_filter    = $_POST['admin_id_filter'];
        $selectedDate       = $_POST['selectedDate'];
        $rm_filter          = $_POST['rm_filter'];

        

        if(isset($_POST['rm_filter'])){
            $rm_filter = $_POST['rm_filter'];
            if($rm_filter !=='all'){
                $uid =  $rm_filter;
            }else{
                $rm_filter = 'all';
            }

            $udata = $this->Menu_model->get_userbyid($uid);
            $admin_id = $udata[0]->admin_id;
            $admin_id_filter  = $admin_id;
      
        }else{
            $rm_filter = 'all';
        }

        if(isset($_POST['selectedDate'])){
            $cdate = $_POST['selectedDate'];
        }else{
            $cdate = date("Y-m-d");
        }

        if(!isset($_POST['admin_id_filter'])){
            $admin_id_filter = $uid;
        }else{
            $admin_id_filter = $_POST['admin_id_filter'];
            if($admin_id_filter == 'all'){
                $admin_filter_type = $_POST['admin_id_filter'];
            }else{
                $admin_filter_type = '';
                $uid = $_POST['admin_id_filter'];

                if($rm_filter !=='all'){
                    $uid =  $rm_filter;
                }
            }
        }
             
          if(isset($_POST['sdate'])){

            $sdate = $_POST['sdate'];
            $edate = $_POST['edate'];

            }else{
                $sdate = date('Y-m-d');
                $edate = date('Y-m-d');
            }


        $mtypes = 'complete_meetings';
        
        $target_uid = $uid;

        $taskDatadatas   = $this->SalesGraph_Model->TotalMeetingDetailsDatasSalesGraph($target_uid,$sdate,$edate,$mtypes,$uid,$admin_filter_type,$rm_filter);

        if(!empty($user)){
            $this->load->view($graphFolder.'/CompleteMeetingsReportAnalysis',['uid'=>$uid,'user'=>$user,'taskDatadatas'=>$taskDatadatas,'cdate'=>$cdate,'admin_id_filter'=>$admin_id_filter,'sdate'=>$sdate,'edate'=>$edate ]);
        }else{
            redirect('Menu/main');
        }
    }


        public function CompleteMeetingsDataReportAnalysis(){

        $user           = $this->session->userdata('user');
        $data['user']   = $user;
        $uid            = $user['user_id'];
        $uyid           = $user['type_id'];
        $this->load->model('Menu_model');
        $this->load->model('SalesGraph_Model');
        $graphFolder = 'Data Analysis';
        $dt             =   $this->Menu_model->get_utype($uyid);
        $dep_name       = $dt[0]->name;
        $tdate          = date("Y-m-d");
         if(!empty($user)){
            $this->load->view($graphFolder.'/CompleteMeetingsDataReportAnalysis',['uid'=>$uid,'user'=>$user]);
        }else{
           redirect('Menu/main');
         }
    }



    

    public function TodaysStatusWisePlannerAnalysis(){
   
        $user           = $this->session->userdata('user');
        $data['user']   = $user;
        $uid            = $user['user_id'];
        $uyid           = $user['type_id'];
        $pdate          = date("Y-m-d");
        $this->load->model('Menu_model');
        $this->load->model('SalesGraph_Model');
        $this->load->library('session');
        $dt             = $this->Menu_model->get_utype($uyid);
        $dep_name       = $dt[0]->name;
        $cdate = date("Y-m-d");
        $graphFolder = 'Data Analysis';
       
        $oneYearAgo = date("Y", strtotime("-1 year"));

        $filter_types       = 'CurrentDate';
        // $filter_types       = 'BETWEEN7Days';
        // $filter_types       = 'BETWEEN1Month';
        // $filter_types       = 'BETWEEN3Month';
        // $filter_types       = 'BETWEEN6Month';
        // $filter_types       = 'BETWEEN12Month';


        if (isset($_POST['filter_types_option']) && !empty($_POST['filter_types_option'])) {
            $filter_types = $_POST['filter_types_option'];
        } else {
            $filter_types = 'CurrentDate';
        }
        

        $getAllUserData     = $this->SalesGraph_Model->TodaysStatusWisePlannerAnalysisData($uid,$cdate,$filter_types);
  
        if(!empty($user)){
            $this->load->view($graphFolder.'/TodaysStatusWisePlannerAnalysis',['uid'=>$uid,'user'=>$user,'getAllUserData'=>$getAllUserData,'filter_types'=>$filter_types]);
        }else{
            redirect('Menu/main');
        }
    }
    public function TodaysCategoryWisePlannerAnalysis(){
   
        $user           = $this->session->userdata('user');
        $data['user']   = $user;
        $uid            = $user['user_id'];
        $uyid           = $user['type_id'];
        $pdate          = date("Y-m-d");
        $this->load->model('Menu_model');
        $this->load->model('SalesGraph_Model');
        $this->load->library('session');
        $dt             = $this->Menu_model->get_utype($uyid);
        $dep_name       = $dt[0]->name;
        $cdate = date("Y-m-d");
        $graphFolder = 'Data Analysis';
       
        $oneYearAgo = date("Y", strtotime("-1 year"));

        $filter_types       = 'CurrentDate';
        // $filter_types       = 'BETWEEN7Days';
        // $filter_types       = 'BETWEEN1Month';
        // $filter_types       = 'BETWEEN3Month';
        // $filter_types       = 'BETWEEN6Month';
        // $filter_types       = 'BETWEEN12Month';


        if (isset($_POST['filter_types_option']) && !empty($_POST['filter_types_option'])) {
            $filter_types = $_POST['filter_types_option'];
        } else {
            $filter_types = 'CurrentDate';
        }
        

        $getAllUserData     = $this->SalesGraph_Model->TodaysCategoryWisePlannerAnalysisData($uid,$cdate,$filter_types);
  
        if(!empty($user)){
            $this->load->view($graphFolder.'/TodaysCategoryWisePlannerAnalysis',['uid'=>$uid,'user'=>$user,'getAllUserData'=>$getAllUserData,'filter_types'=>$filter_types]);
        }else{
            redirect('Menu/main');
        }
    }
    public function TodaysCategoryDevideWisePlannerAnalysis(){
   
        $user           = $this->session->userdata('user');
        $data['user']   = $user;
        $uid            = $user['user_id'];
        $uyid           = $user['type_id'];
        $pdate          = date("Y-m-d");
        $this->load->model('Menu_model');
        $this->load->model('SalesGraph_Model');
        $this->load->library('session');
        $dt             = $this->Menu_model->get_utype($uyid);
        $dep_name       = $dt[0]->name;
        $cdate = date("Y-m-d");
        $graphFolder = 'Data Analysis';
       
        $oneYearAgo = date("Y", strtotime("-1 year"));

        $filter_types       = 'CurrentDate';
        // $filter_types       = 'BETWEEN7Days';
        // $filter_types       = 'BETWEEN1Month';
        // $filter_types       = 'BETWEEN3Month';
        // $filter_types       = 'BETWEEN6Month';
        // $filter_types       = 'BETWEEN12Month';


        if (isset($_POST['filter_types_option']) && !empty($_POST['filter_types_option'])) {
            $filter_types = $_POST['filter_types_option'];
        } else {
            $filter_types = 'CurrentDate';
        }
        

        $getAllUserData     = $this->SalesGraph_Model->TodaysCategoryWisePlannerAnalysisData($uid,$cdate,$filter_types);
  
        if(!empty($user)){
            $this->load->view($graphFolder.'/TodaysCategoryDevideWisePlannerAnalysis',['uid'=>$uid,'user'=>$user,'getAllUserData'=>$getAllUserData,'filter_types'=>$filter_types]);
        }else{
            redirect('Menu/main');
        }
    }


    public function TodaysCompanyWisePlannerAnalysis(){
   
        $user           = $this->session->userdata('user');
        $data['user']   = $user;
        $uid            = $user['user_id'];
        $uyid           = $user['type_id'];
        $pdate          = date("Y-m-d");
        $this->load->model('Menu_model');
        $this->load->model('SalesGraph_Model');
        $this->load->library('session');
        $dt             = $this->Menu_model->get_utype($uyid);
        $dep_name       = $dt[0]->name;
        $cdate = date("Y-m-d");
        $graphFolder = 'Data Analysis';
       
        $oneYearAgo = date("Y", strtotime("-1 year"));

        $filter_types       = 'CurrentDate';
        // $filter_types       = 'BETWEEN7Days';
        // $filter_types       = 'BETWEEN1Month';
        // $filter_types       = 'BETWEEN3Month';
        // $filter_types       = 'BETWEEN6Month';
        // $filter_types       = 'BETWEEN12Month';


        if (isset($_POST['filter_types_option']) && !empty($_POST['filter_types_option'])) {
            $filter_types = $_POST['filter_types_option'];
        } else {
            $filter_types = 'CurrentDate';
        }
        

        $getAllUserData     = $this->SalesGraph_Model->TodaysCompanyNameWisePlannerAnalysisData($uid,$cdate,$filter_types);
        // echo $this->db->last_query();
        // dd($getAllUserData);
        if(!empty($user)){
            $this->load->view($graphFolder.'/TodaysCompanyWisePlannerAnalysis',['uid'=>$uid,'user'=>$user,'getAllUserData'=>$getAllUserData,'filter_types'=>$filter_types]);
        }else{
            redirect('Menu/main');
        }
    }





}

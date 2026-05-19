<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title> Planner Task Details Page | STEM APP | WebAPP</title>
    <!-- Google Font: Source Sans Pro -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.0/css/all.min.css">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="<?=base_url();?>assets/css/all.min.css">
    <!-- Ionicons -->
    <link rel="stylesheet" href="https://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css">
    <!-- Tempusdominus Bootstrap 4 -->
    <link rel="stylesheet" href="<?=base_url();?>assets/css/tempusdominus-bootstrap-4.min.css">
    <!-- iCheck -->
    <link rel="stylesheet" href="<?=base_url();?>assets/css/icheck-bootstrap.min.css">
    <!-- JQVMap -->
    <link rel="stylesheet" href="<?=base_url();?>assets/css/jqvmap.min.css">
    <!-- Theme style -->
    <link rel="stylesheet" href="<?=base_url();?>assets/css/adminlte.min.css">
    <!-- overlayScrollbars -->
    <link rel="stylesheet" href="<?=base_url();?>assets/css/OverlayScrollbars.min.css">
    <!-- Daterange picker -->
    <link rel="stylesheet" href="<?=base_url();?>assets/css/daterangepicker.css">
    <!-- summernote -->
    <link rel="stylesheet" href="<?=base_url();?>assets/css/summernote-bs4.min.css">
    <!-- DataTables -->
    <link rel="stylesheet" href="<?=base_url();?>assets/css/dataTables.bootstrap4.min.css">
    <link rel="stylesheet" href="<?=base_url();?>assets/css/responsive.bootstrap4.min.css">
    <link rel="stylesheet" href="<?=base_url();?>assets/css/buttons.bootstrap4.min.css">
    

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2.2.0"></script>

    <style>
    .scrollme {
    overflow-x: auto;
    }
    .taskaprroveform.text-right {
    align-items: revert;
    justify-content: right;
    display: flex;
}
.formselect {
    margin-top: 10px;
}
span.tsby {
    background: bisque;
    padding: 1px 6px;
    margin-top: 4px;
}
.card-header, .col.card.coladjust1 select, .col.card.coladjust31 select, .container-fluid.body-content.mt-4, .each, .form-row>.col, .form-row>[class*=col-], .table-striped tbody tr:nth-of-type(odd), input.form-control, table.table-bordered.dataTable {
    box-shadow: inset 4px 4px 7px rgba(55, 84, 170, .15), inset -4px -4px 10px #fff, 0 0 2px rgba(255, 255, 255, .2);
    transition: box-shadow .2s ease-in-out;
}
.each {
    background: #f1f3f6;
    font-family: Arial, Helvetica, sans-serif;
    border: 5px solid #eaeef5;
}
.text-center.bg-color.p-3 {
    background: navy;
    color: white;
}
table tbody tr:nth-child(odd) {
    background-color: #f9f9f9; /* Light Gray for Odd rows */
  }
  table tbody tr:nth-child(even) {
    background-color: #e3f2fd; /* Light Blue for Even rows */
  }
table tbody tr:hover {
    background-color: #bbdefb; /* Optional: Hover effect */
  }
    </style>
  </head>
  <body class="hold-transition sidebar-mini layout-fixed">
    <div class="wrapper">
      <!-- Preloader -->
      
      <!-- Navbar -->
      <?php require('nav.php');?>
      <!-- /.navbar -->
      
      <!-- Content Wrapper. Contains page content -->
      <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <div class="content-header">
          <div class="container-fluid">
            <?php if ($this->session->flashdata('success_message')): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
              <strong> <?php echo $this->session->flashdata('success_message'); ?></strong>
              <button type="button" class="close" data-dismiss="alert" aria-label="Close">
              <span aria-hidden="true">&times;</span>
              </button>
            </div>
            <?php endif; ?>
            <?php if ($this->session->flashdata('error_message')): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
              <strong> <?php echo $this->session->flashdata('error_message'); ?></strong>
              <button type="button" class="close" data-dismiss="alert" aria-label="Close">
              <span aria-hidden="true">&times;</span>
              </button>
            </div>
            <?php endif; ?>
            <?php $uid=$user['user_id']; ?>
        
                  </div><!-- /.container-fluid -->
                </div>
                <!-- /.content-header -->
                <?php
                $bd = $this->Menu_model->get_userbyaid($uid);
                ?>
                <!-- Main content -->
                <section class="content">
                  <div class="container-fluid">
                    <div class="dateform text-end mb-3 p-3" style="background: #f8f9fa; border-radius: 10px; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">
                      <form method="post" action="<?= base_url(); ?>Menu/CheckTaskDetailsByUser/<?= $tuser_uid ?>/<?= $taskdate ?>" class="form-inline justify-content-end">
                        <div class="form-group me-2">
                          <input 
                            type="date" 
                            class="form-control" 
                            name="adate" 
                            value="<?= $taskdate ?>" 
                            id="plandate" 
                            max="<?= date('Y-m-d', strtotime('+1 day')) ?>">
                        </div>
                        &nbsp;&nbsp;
                        <button type="submit" class="btn btn-primary">Set Date</button>
                      </form>
                    </div>
                    <div class="row">
                      <div class="col-12">
                      <div class="card">
                        
                        <!-- /.card-header -->
                        <div class="card-body">
                          <div class="body-content">
                            <div class="page-header">
                            <?php 
                      
                                        $totalttaskdata      = $this->Menu_model->getTotalUserTaskDetailsOnPlanner($tuser_uid,$taskdate);
                                        // dd($totalttaskdata);
                                        $autotasktimenewData = $this->Menu_model->autotasktimenew($tuser_uid,$taskdate);
                                        // echo $this->db->last_query();
                                     

                                       

                                        // dd($autotasktimenewData);
                                        $taskplanmincount = 0;
                                        $new_datetimemin = '';
                                       
                                        $apprvtask    = 0;
                                        $rejecttask   = 0;
                                        $rejetbutnta  = 0;
                                        $rejetbutnau  = 0;
                                        $rejetbutnasd = 0;
                                        $pendingForApproved = 0;

                                        $alertmessage = '';

                                        // Function to convert time string to seconds
                                        function timeToSeconds($time) {
                                          $parts = explode(':', $time);
                                          return ($parts[0] * 3600) + ($parts[1] * 60) + $parts[2];
                                        }

                                        // Function to convert seconds to time string (hours and minutes)
                                        // Function to convert seconds to time string (hours, minutes, and seconds)
                                          function secondsToTime($seconds) {
                                            $hours = floor($seconds / 3600);
                                            $minutes = floor(($seconds % 3600) / 60);
                                            $seconds = $seconds % 60;
                                            return sprintf('%d hours, %d minutes, and %d seconds', $hours, $minutes, $seconds);
                                          }

                                      $totalPlaningConsumeInSeconds = 0;

                                        foreach($totalttaskdata as $taskdata){ 
                                          
                                        if(empty($taskdata->approved_status)) {
                                            $pendingForApproved+=1;
                                        }else if($taskdata->approved_status == 1) {
                                          $apprvtask +=1;
                                        }else if(($taskdata->approved_status == 0 && $taskdata->self_assign == '')) {
                                          $rejecttask +=1;
                                        }else if($taskdata->approved_status == 0 && $taskdata->self_assign == 1) {
                                            $rejetbutnau +=1;
                                        }elseif($taskdata->approved_status == 0 && $taskdata->self_assign == 2) {
                                            $rejetbutnta +=1;
                                        }else if($taskdata->approved_status == 0 && $taskdata->self_assign == 3) {
                                            $rejetbutnasd +=1;
                                        }


                                            $actiontype_id = $taskdata->actiontype_id;
                                            $status = $taskdata->approved_status;
                                            $task_ptime = $taskdata->tptime;

                                            $totalPlaningConsumeInSeconds += timeToSeconds($task_ptime);

                                            // if($status !=0){
                                            // if($actiontype_id ==5 || $actiontype_id ==8 || $actiontype_id ==9 || $actiontype_id ==1 || $actiontype_id ==10 || $actiontype_id ==15 || $actiontype_id ==18){
                                            //     $taskplanmincount += 5;
                                            // }else if($actiontype_id ==2 || $actiontype_id ==6 || $actiontype_id ==19 || $actiontype_id ==20 || $actiontype_id ==21){
                                            //     $taskplanmincount += 10;
                                            // }else if($actiontype_id ==3 || $actiontype_id ==4 || $actiontype_id ==12 || $actiontype_id ==17){
                                            //     $taskplanmincount += 60;
                                            // }else if($actiontype_id ==7){
                                            //     $taskplanmincount += 15;
                                            // }else if($actiontype_id ==11 || $actiontype_id ==13 || $actiontype_id ==14){
                                            //     $taskplanmincount += 2;
                                            // }
                                          // }


                                        }




                                        $totalConsumeTimeonPlanning         = secondsToTime($totalPlaningConsumeInSeconds);
                                        $totalConsumeTimeonPlanning_message = "The total time is $totalConsumeTimeonPlanning";
                                        // echo $totalPlaningConsumeInSeconds;
                                        // Calculate average time per task
                                          $totalttaskdataCnt        = sizeof($totalttaskdata);
                                          $averageSecondsPerTask  = $totalPlaningConsumeInSeconds / $totalttaskdataCnt;
                                          $averageTimePerTask = secondsToTime($averageSecondsPerTask);

                                          $averageTimePerTask_message = "The average time per task is $averageTimePerTask";

                                        $totalttaskdata1 = $this->Menu_model->getUserTotalTaskTimeForTodays1($tuser_uid,$taskdate);
                                        $taskplanmincount = $totalttaskdata1[0]->ttime;
                                       
                                        $toatluserplannedtimeonplanner = $taskplanmincount;

                                        $reviewaDatas    = $this->Menu_model->GetAllPlannedReviewByUserByDate($tuser_uid,$taskdate);
                                        // dd($reviewaDatas);
                                        $reviewaDatascnt = sizeof($reviewaDatas);
                                    
                                        if($reviewaDatascnt > 0){
                                          $totalReviewTimeinMinutes = 0;
                                          foreach($reviewaDatas as $reviewaData){

                                            $review_create_date           = $reviewaData->sdatet;
                                            $review_plant_date_time       = $reviewaData->plant;
                                            $review_review_close_time     = $reviewaData->review_close_time;
                                            $review_fix_time              = date('H:i:s', strtotime($review_review_close_time));
                                            
                                            if ($review_fix_time === '00:00:00') {
                                              $review_time =  0;
                                            } else {

                                              $startReviewTimestamp = new DateTime($review_plant_date_time);
                                              $endReviewTimestamp = new DateTime($review_review_close_time);

                                              $revieinterval = $startReviewTimestamp->diff($endReviewTimestamp);

                                              $reviewHours = $revieinterval->h + ($revieinterval->d * 24); // Add days as hours if any
                                              $reviewMinutes = $revieinterval->i;

                                              // Convert total time to minutes
                                              $totalReviewMinutes = ($revieinterval->days * 24 * 60) + ($revieinterval->h * 60) + $revieinterval->i;
                                             
                                            //   echo "{$reviewHours} hours and {$reviewMinutes} minutes";

                                            // // Convert datetime strings to timestamps
                                            // $startReviewTimestamp       = strtotime($review_plant_date_time);
                                            // $endReviewTimestamp         = strtotime($review_review_close_time);
                                            // // Calculate the difference in seconds
                                            // $differenceInSecondsReview  = $endReviewTimestamp - $startReviewTimestamp;
                                            // // Convert the difference to minutes
                                            // $differenceInMinutesReview  = $differenceInSecondsReview / 60;
                                            // // Calculate hours and minutes
                                            // $reviewHours                = floor($differenceInSecondsReview / 3600);
                                            // $reviewMinutes              = floor(($differenceInSecondsReview % 3600) / 60);
                                            
                                            $totalReviewTimeinMinutes  += $totalReviewMinutes;

                                            }
                                          }

                                          // echo 'totalReviewTimeinMinutes ='.$totalReviewTimeinMinutes;
                                          
                                          $totalPlannedReviewTimeinMinutes = $totalReviewTimeinMinutes;

                                          $taskplanmincount                 = $taskplanmincount + $totalReviewTimeinMinutes;

                                        }else{
                                          $review_time                      =  0;
                                          $totalPlannedReviewTimeinMinutes  = 0;
                                        }

                                     


                                        $exactplanedtime = $taskplanmincount;
                                    
                                        $lunchtime      = 30;      // Lunch Time 45 Miniute
                                        $autoTasktime   = 90;  // 90 Minutes For Auto Task
                                        $topp           = 60; // 60 Minutes For Tommorow Planner Planning
                                        $texpense_time  = $lunchtime + $autoTasktime + $topp; // totol expense time
                                        $nine_hours_planning =540; // 9 hours Planning = 9* 60 = 540 Minutes 
                                        $userplanetime = $nine_hours_planning - $texpense_time; // total plan time  - 345 minutes
                                        $plannerremTime = $userplanetime - $taskplanmincount;
                                        $srlData = $this->Menu_model->GetTodaysApprovedSpecialRequestforLeave($tuser_uid,$taskdate);
                                       
                                       ?>


                             
                              <form method="post" action="<?=base_url();?>Menu/approveDailyTask">
                              <fieldset>
                                 <div class="card">
                                  
                                  <?php 
                                    $request    = $this->Menu_model->GetTodaysPlannerRequest($tuser_uid);
                                    $requestcnt = sizeof($request);
                                    if($requestcnt > 0){
                                    $apr_time       = $request[0]->apr_time;
                                    $request_time   = $request[0]->created_at;
                                    
                                    $req_datetime1  = new DateTime($request_time);
                                    $req_datetime2  = new DateTime($apr_time);
                                    // Calculate the difference in request approved
                                    $req_interval   = $req_datetime1->diff($req_datetime2);
                                    // Get the difference in hours and minutes in request approved
                                    $apr_hours      = $req_interval->h + ($req_interval->days * 24); // Total hours
                                    $apr_minutes    = $req_interval->i; // Remaining minutes
                                    $reqlateapr     = "$apr_hours hours and $apr_minutes minutes";
                                    $tsk_initialTime    = $apr_time;
                                    $tsk_dateTime       = new DateTime($tsk_initialTime);
                                    $tsk_dateTime->modify('+60 minutes');
                                    $tskinittime        = $tsk_dateTime->format('Y-m-d H:i:s');
                                    $alertmessage = 'Planner request approved time is : '.$apr_time .', and user planner time is 1 hour. Based on this time, user need to plan the task after '.$tskinittime .'.';
                                    if(!is_null($apr_time)):
                                    $dateTime = new DateTime($apr_time);
                                    $dateTime->modify('+60 minutes');
                                    $newTime = $dateTime->format('Y-m-d H:i:s');
                         
                                    $todaysDateTime = date("Y-m-d") . ' 10:00:00';
                                    $todaysDateTime = DateTime::createFromFormat('Y-m-d H:i:s', $todaysDateTime); // Corrected format string
                                    $apr_times = $apr_time;
                                    $apr_time = new DateTime($apr_time);
                                    $interval = $todaysDateTime->diff($apr_time);
                                    
                                    // Get the difference in total minutes
                                    $diffInMinutes = ($interval->h * 60) + $interval->i;
                                    
                                    $rmautoTasktime = 30;
                               
                                    $plannerremTime = $plannerremTime - $diffInMinutes;
                                    // $plannerremTime = $plannerremTime + $rmautoTasktime;
                                    $userplanetime = $userplanetime - $diffInMinutes;
                                    // $userplanetime = $userplanetime + $rmautoTasktime;
                                    endif;
                                  }
                               
                                  if($taskplanmincount >= $userplanetime){    
                                    $background = 'bg-success';                           
                                  ?>
                                  
                                 <div class="taskaprroveform text-right" style=" background: linear-gradient(to right, #f0f4f8, #ffffff); padding: 15px 20px; border-radius: 12px; box-shadow: 0 6px 18px rgba(0, 0, 0, 0.1); display: flex; align-items: center; justify-content: flex-end; gap: 10px; flex-wrap: wrap; ">
                                      <input type="hidden" name="suser" value="<?=$tuser_uid;?>" >
                                        <select class="form-control form-select formselect" aria-label="Default select example" name="status" style="width:300px;" >
                                          <option selected value="" >Select Approve/Reject</option>
                                          <option value="Approve">Approve</option>
                                          <option value="Reject">Reject</option>
                                        </select>
                                        <button class="btn btn-primary mt-2" type="submit" >Submit</button>
                                    </form>
                                  </div>
                                    <?php }else{
                                      $background = 'bg-danger';
                                    } ?>
                                  <?php if($taskplanmincount <= $userplanetime){  ?>         
                                    <marquee class="p-2 mt-1 <?=$background?>" onMouseOver="this.stop()" onMouseOut="this.start()" width="100%" behavior="left" 
                                          style="background: linear-gradient(to right, #ff4081,rgb(239, 30, 100)); color: #fff;">
                                          <h6> 
                                            <?php 
                                                $uprhours = floor( $plannerremTime / 60);
                                                $uprremainingMinutes =  $plannerremTime % 60;
                                            ?>
                                            <p><?=$uprhours?> hour <?=$uprremainingMinutes ?> Minute remaining for task plan to enable Task Approval Function.</p>
                                          </h6>
                                        </marquee>
                                  <?php }?> 
                                  <marquee class="p-2 mt-1" width="100%" onMouseOver="this.stop()" onMouseOut="this.start()" behavior="left" 
                                      style="background: linear-gradient(to right, #6a1b9a, #4caf50); color: #fff;">
                                      <h6>Lunch Time : <?= $lunchtime ?> Minute || Auto Task Time : <?= $autoTasktime?> Minutes || Tomorrow Planner Planning : <?=$topp ?> Minutes || 9 hours Planning = 9* 60 = 540 Minutes || Total Time For (Lunch + Auto Task + Tomorrow Planner) : <?=$texpense_time?> Minutes || Task Planner Should be <?php echo 540 - $texpense_time;?> Minutes</h6>
                                    </marquee>


                                 <hr>
                                
                                 <div class="row">
                                      <div class="col-md-12">
                                        <div class="text-center p-3"
                                            style="background: linear-gradient(to right,rgb(30, 178, 0), #ff6f00, #4b006e);
                                                    color: #fff;
                                                    border-radius: 12px;
                                                    box-shadow: 
                                                      inset 0 0 10px rgba(255, 255, 255, 0.1),
                                                      0 0 0 3px rgba(255, 111, 0, 0.5),
                                                      0 8px 30px rgba(0, 0, 0, 0.3);">
                                          <h5><i>Calendar Plan By - 
                                            <?php
                                            $dt = $taskdate; 
                                            $udetail = $this->Menu_model->get_userbyid($tuser_uid);
                                            echo $udetail[0]->name;
                                            ?>  
                                          </i></h5>
                                          <p><i><b><?= $dt ?></b></i></p>
                                          <p>
                                            <b>
                                              <?php
                                              $hours = floor($exactplanedtime / 60);
                                              $remainingMinutes = $exactplanedtime % 60;
                                              echo " User Planned $hours hours and $remainingMinutes minutes.";
                                              ?>
                                            </b>
                                          </p>
                                        </div>
                                        <hr>
                                      </div>
                                    </div>
                                  <?php  
                                  // $requestcnt = 0;
                                  if($requestcnt == 0){ 
                                     $plannerRequestTime = 'Planner Request';
                                      $request_time = 'No';
                                      $apr_times    = 'No';
                                      $reqlateapr   = 'No';

                                      $pllanercolinc = "col-md-12";

                                  }else if($requestcnt > 0){ 

                                    $plannerRequestTime = 'Planner Request Time';
                                    $pllanercolinc = "col-md-3";
                                    if(is_null($apr_time)){
                                        $apr_times    = 'Pending';
                                        $reqlateapr    = 'Pending';
                                    }

                                  }
                                  
                             
                                    ?>
                                 <div class="row">
                                    <div class="<?= $pllanercolinc ?>">
                                      <div class="card" style="background: linear-gradient(135deg, #2193b0, #6dd5ed); color: #fff;">
                                        <div class="card-header text-center">
                                          <h6><?= $plannerRequestTime ?></h6>
                                          <span><?= $request_time; ?></span>
                                        </div>
                                      </div>
                                    </div>
                                    <?php if ($request_time !== 'No'): ?>
                                      <div class="col-md-3">
                                        <div class="card" style="background: linear-gradient(135deg, #00b09b, #96c93d); color: #fff;">
                                          <div class="card-header text-center">
                                            <h6>Planner Approved Time</h6>
                                            <span><?= $apr_times; ?></span>
                                          </div>
                                        </div>
                                      </div>
                                      <div class="col-md-3">
                                        <div class="card" style="background: linear-gradient(135deg, #ff4e50, #f9d423); color: #fff;">
                                          <div class="card-header text-center">
                                            <h6>Late Approved Time</h6>
                                            <span><?= $reqlateapr; ?></span>
                                          </div>
                                        </div>
                                      </div>
                                      <div class="col-md-3">
                                        <div class="card" style="background: linear-gradient(135deg, #1d976c, #93f9b9); color: #fff;">
                                          <div class="card-header text-center">
                                            <h6>Time Available For Planning</h6>
                                            <?php 
                                              $uphours = floor($userplanetime / 60);
                                              $upremainingMinutes = $userplanetime % 60;
                                            ?>
                                            <span><?php echo "$uphours hours and $upremainingMinutes minutes."; ?></span>
                                          </div>
                                        </div>
                                      </div>
                                    <?php endif; ?>
                                  </div>

                                      <?php 
                                     
                                      if($alertmessage !==''){ ?>
                                        <marquee class="p-2 mt-1" width="100%" onMouseOver="this.stop()" onMouseOut="this.start()" behavior="alternate" 
                                      style="background: linear-gradient(to right, #4e73df, #1cc88a); color: #fff;">
                                      <small><span><?= $alertmessage; ?></span></small>
                                    </marquee>
                                     <?php } ?>
                                   

                                      <?php 
                                      $planSessionData  = $this->Menu_model->TodaysPlannerSession($tuser_uid,$taskdate);
                                      
                                      $planSessionmin  = $this->Menu_model->TodaysTotalsPlannerSessioninMinute($tuser_uid);
                                      // echo $this->db->last_query();
                                      ?>
                                    <div class="row mt-2 mb-2">
                                      <div class="col-md-4">
                                        <div class="card p-2 m-1 flexitemscenter" style="background: linear-gradient(135deg, #f7971e, #ffd200); color: #fff;">
                                          
                                          <div class="card-header text-center">
                                            <h6>Planner Session</h6>
                                            <span><?php 
                                            if($totalConsumeTimeonPlanning == '0 hours, 0 minutes, and 0 seconds'){
                                              echo 0;
                                            }else{
                                              echo (sizeof($planSessionData) == 0) ? 1 : sizeof($planSessionData);
                                            }
                                            
                                            
                                            ?></span>
                                          </div>
                                        </div>
                                      </div>
                                      <div class="col-md-4">
                                        <div class="card p-2 m-1 flexitemscenter" style="background: linear-gradient(135deg, #00c6ff, #0072ff); color: #fff;">
                                          <div class="card-header text-center">
                                            <h6>Time Consume on Planning</h6>
                                            <span>
                                              <?php 
                                                if ($totalConsumeTimeonPlanning == '0 hours, 0 minutes, and 0 seconds') {
                                                  $planSessionmininsecond = timeToSeconds($planSessionmin);
                                                  $totalConsumeTimeonPlanning = secondsToTime($planSessionmininsecond);
                                                  echo $totalConsumeTimeonPlanning;
                                                  $averageSecondsPerTask = $planSessionmininsecond / $totalttaskdataCnt;
                                                  $averageTimePerTask = secondsToTime($averageSecondsPerTask);
                                                } else {
                                                  echo $totalConsumeTimeonPlanning;
                                                }
                                              ?>
                                            </span>
                                          </div>
                                        </div>
                                      </div>
                                      <div class="col-md-4">
                                        <div class="card p-2 m-1 flexitemscenter" style="background: linear-gradient(135deg, #43cea2, #185a9d); color: #fff;">
                                          <div class="card-header text-center">
                                            <h6>The Average Time Per Task is</h6>
                                            <span><?= $averageTimePerTask; ?></span>
                                          </div>
                                        </div>
                                      </div>
                                    </div>


                                      <style>
                                        .card.p-2.bg-info.m-1.flexitemscenter {
                                          height: 90px;
                                          align-items: center;
                                          justify-content: center;
                                          display: flex;
                                      }
                                      </style>

                                      
                                  <div class="card p-2" style="background: linear-gradient(135deg, #0f2027, #2c5364); color: #fff;">
                                    <div class="row">
                                      <div class="col-md-4">
                                        <?php 
                                          $upchours = floor($exactplanedtime / 60);
                                          $upcremainingMinutes = $exactplanedtime % 60;
                                        ?>
                                        <div class="card-header text-center p-2 m-2" style="background: rgba(255, 255, 255, 0.08); border-radius: 10px; backdrop-filter: blur(5px);">
                                          <?php 
                                          // Task Planned Time
                                          $hours_toatluserplannedtimeonplanner = floor($toatluserplannedtimeonplanner / 60);
                                          $remainingMinutes_toatluserplannedtimeonplanner = $toatluserplannedtimeonplanner % 60; 
                                          // Review Planned Time
                                          $hours_reviewdplaned = floor($totalPlannedReviewTimeinMinutes / 60);
                                          $minutes_reviewdplaned = $totalPlannedReviewTimeinMinutes % 60;

                                          if(sizeof($autotasktimenewData) > 0){
                                            $autotask_stime = $autotasktimenewData[0]->stime;
                                            $autotask_etime = $autotasktimenewData[0]->etime;
                                          }

                                          ?>
                                          <h6>Planned Details</h6>
                                          <hr style="background-color:#fff;">
                                          <span> <span style="color:#00ff3a">Task Planned :</span> <?= "$hours_toatluserplannedtimeonplanner hour and $remainingMinutes_toatluserplannedtimeonplanner minute"; ?></span> <br/>

                                          <span><span style="color:#00ff3a"> Review Planned :</span> <?= "{$reviewHours} hours and {$reviewMinutes} minutes"; ?></span><br/>

                                          <span><span style="color:#00ff3a"> Auto Task + Tommorow Planner Time :</span> <?= "3 hour "; ?></span>



                                        </div>
                                      </div>
                                      <div class="col-md-4">
                                        <?php 
                                          $upchours = floor($exactplanedtime / 60);
                                          $upcremainingMinutes = $exactplanedtime % 60;
                                        ?>
                                        <div class="card-header text-center p-2 m-2" style="background: rgba(255, 255, 255, 0.08); border-radius: 10px; backdrop-filter: blur(5px);height: 90%;align-items: center; justify-content: center; display: flex ;">
                                          <div>
                                          <h6>Total Planned Time</h6>
                                          <hr style="background-color:#fff;">
                                          <span><?php echo "$upchours hours and $upcremainingMinutes minutes."; ?></span>
                                          </div>
                                        </div>
                                      </div>

                                      <div class="col-md-4">
                                        <?php 
                                          $uprhours = floor($plannerremTime / 60);
                                          $uprremainingMinutes = $plannerremTime % 60;
                                          if($taskplanmincount <= $userplanetime){  
                                        ?>
                                          <div class="card-header text-center p-2 m-2" style="background: rgba(255, 255, 255, 0.08); border-radius: 10px; backdrop-filter: blur(5px);height: 90%;align-items: center; justify-content: center; display: flex ;">
                                            <div>
                                            <h6>Remaining Time For Plan Time</h6>
                                            <hr style="background-color:#fff;">
                                            <span><?php echo "$uprhours hours and $uprremainingMinutes minutes."; ?></span>
                                            </div>
                                          </div>
                                        <?php } else { ?>
                                          <div class="card-header text-center p-2 m-2" style="background: rgba(255, 255, 255, 0.08); border-radius: 10px; backdrop-filter: blur(5px);height: 90%;align-items: center; justify-content: center; display: flex ;">
                                            <div>
                                            <h6>Successfully Achieved</h6>
                                            <hr style="background-color:#fff;">
                                            <span>Yes</span>
                                            </div>
                                          </div>
                                        <?php } ?> 
                                      </div>
                                    </div>
                                  </div>


                                    <hr>
                                <div class="table-responsive-task-table">

                                <div class="row">
                                      <div class="col-md-3">
                                        <div class="card" style="background: linear-gradient(135deg, #f7971e, #ffd200); color: #000;">
                                          <div class="card-header text-center">
                                            <h6>Total Task</h6>
                                            <span><?=sizeof($totalttaskdata); ?></span>
                                          </div>
                                        </div>
                                      </div>
                                      <div class="col-md-3">
                                        <div class="card" style="background: linear-gradient(135deg, #36d1dc, #5b86e5); color: #fff;">
                                          <div class="card-header text-center">
                                            <h6>Approved Task</h6>
                                            <span><?= $apprvtask; ?></span>
                                          </div>
                                        </div>
                                      </div>
                                      <div class="col-md-3">
                                        <div class="card" style="background: linear-gradient(135deg, #43cea2, #185a9d); color: #fff;">
                                          <div class="card-header text-center">
                                            <h6>Pending For Approved Task</h6>
                                            <span><?= $pendingForApproved; ?></span>
                                          </div>
                                        </div>
                                      </div>
                                      <div class="col-md-3">
                                        <div class="card" style="background: linear-gradient(135deg, #ff512f, #dd2476); color: #fff;">
                                          <div class="card-header text-center">
                                            <h6>Reject Task</h6>
                                            <span><?= $rejecttask; ?></span>
                                          </div>
                                        </div>
                                      </div>
                                    </div>

                                    <div class="row mt-3">
                                      <div class="col-md-3">
                                        <div class="card" style="background: linear-gradient(135deg, #1f4037, #99f2c8); color: #000;">
                                          <div class="card-header text-center">
                                            <h6>Pending for Assign After Reject Task</h6>
                                            <span><?= $rejecttask ?></span>
                                          </div>
                                        </div>
                                      </div>
                                      <div class="col-md-3">
                                        <div class="card" style="background: linear-gradient(135deg, #ffecd2, #fcb69f); color: #000;">
                                          <div class="card-header text-center">
                                            <h6 class="text-capitalize">admin create request for self assign</h6>
                                            <span><?= $rejetbutnau ?></span>
                                          </div>
                                        </div>
                                      </div>
                                      <div class="col-md-3">
                                        <div class="card" style="background: linear-gradient(135deg, #b92b27, #1565c0); color: #fff;">
                                          <div class="card-header text-center">
                                            <h6 class="text-capitalize">task assignd by admin after reject</h6>
                                            <span><?= $rejetbutnta ?></span>
                                          </div>
                                        </div>
                                      </div>
                                      <div class="col-md-3">
                                        <div class="card" style="background: linear-gradient(135deg, #c33764, #1d2671); color: #fff;">
                                          <div class="card-header text-center">
                                            <h6 class="text-capitalize">task assignd by user after reject</h6>
                                            <span><?= $rejetbutnasd ?></span>
                                          </div>
                                        </div>
                                      </div>
                                    </div>

                                    <?php 
                          $checkYPlannerStartOrNot    = $this->Menu_model->CheckYesterdayPlannerStartOrNot($tuser_uid,$taskdate);
                          $checkYPlannerStartOrNotCnt = sizeof($checkYPlannerStartOrNot);
                          
                            if($checkYPlannerStartOrNotCnt > 0){

                              $yester_planner_start_time_date = $checkYPlannerStartOrNot[0]->created_at;
                              $yester_planner_start_time = date('Y-m-d', strtotime($yester_planner_start_time_date));

                              $ctpstyd    = $this->Menu_model->CheckTodaysPlannerStartTimeOnYesterDayDate($tuser_uid,$taskdate);
                              $ctpstydcnt = sizeof($ctpstyd); 

                              if($ctpstydcnt > 0){
                                $start_tttpft     = $ctpstyd[0]->start_tttpft;
                                $start_tttpft    = $yester_planner_start_time.' '.$start_tttpft;
                                $yestTommPlannerDateTime = "The user set the planner time to $start_tttpft, but actually started planning at $yester_planner_start_time_date for that date - $taskdate.";

                              }else{
                                $yestTommPlannerDateTime = '';
                              }
                              ?>
                              <marquee class="p-2 mt-1" width="100%" onmouseover="this.stop()" onmouseout="this.start()" behavior="alternate" 
                          style="background: linear-gradient(90deg, #ff512f, #dd2476); color: white;">
                            <h6 class='text-capitalize mb-0'><?= $yestTommPlannerDateTime ?></h6>
                          </marquee>
                          <hr>
                            <?php }   ?>
                                <div class="text-center p-3" style="background: linear-gradient(135deg, #11998e, #38ef7d); color: #fff;">
                                  <h3>Details of Task Planner For Date - <?= $taskdate ?></h3>
                                </div>          
                                  <hr>
                                  <div class="table-responsive text-nowrap">
                                    <div class="pdf-viwer">
                                        <table id="example1" class="table table-striped table-bordered" cellspacing="0" width="100%">
                                            <thead class="thead-dark">
                                                <tr>
                                                    <th>Sr. no</th>
                                                    <th>CID</th>
                                                    <th>Company Name</th>
                                                    <th>Action Name</th>
                                                    <th>Status</th>
                                                    <th>Task Time</th>
                                                    <th>Task Appointment Date Time</th>
                                                    <th>Plan By</th>
                                                    <th>Filter Used</th>
                                                    <!-- <th>Filter By</th> -->
                                                    <th>Task Work Status</th>
                                                    <th>Action Status</th>
                                                    <th>Approved By</th>
                                                    <th>Action</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php 
                                                $i = 1;
                                               
                                                $totalttask =$this->Menu_model->getTotalUserTaskDetailsOnPlanner($tuser_uid, $dt);
                                                
                                                foreach ($totalttask as $taskdata) { 
                                               /*   
                                                  if($taskdata->approved_status == 1 ) {
                                                        continue;
                                                    }
                                                    if($taskdata->approved_status == 0 && $taskdata->self_assign !=='') {
                                                      continue;
                                                  }*/
                                                //   if($taskdata->actontaken  == 'yes' ) {
                                                //     continue;
                                                // }
                                                    $taid = $taskdata->actiontype_id;
                                                    $tblId = $taskdata->id;
                                                    $taid = $this->Menu_model->get_actionbyid($taid);
                                                    $time = $taskdata->appointmentdatetime;
                                                    $reminder = $taskdata->reminder;
                                                    $rimby = $taskdata->reminderby;
                                                    $rimat = $taskdata->reminderat;
                                                    $assignedto_id = $taskdata->assignedto_id;
                                                    $status = $taskdata->approved_status;
                                                    $self_assign = $taskdata->self_assign;
                                                    $approver = $taskdata->approved_by;
                                                    $filter_by = json_decode($taskdata->filter_by, true);
                                                    $selectby = $taskdata->selectby;
                                                    $rimbyname = $this->Menu_model->get_userbyid($rimby);
                                                    $time = date('h:i a', strtotime($time));
                                                    $taskuser = $this->Menu_model->get_userbyid($assignedto_id);
                                                    $taskuname = $taskuser[0]->name;
                
                                                ?>
                                                <tr>
                                                    <td><?= $i; ?></td>
                                                    <td><a href="<?=base_url()?>Menu/CompanyDetails/<?=$taskdata->cid;?>"><?=$taskdata->cid;?></a></td>
                                                    <td><a href="<?=base_url()?>Menu/CompanyDetails/<?=$taskdata->cid;?>"><?= $taskdata->compname ?></a></td>
                                                    <td><?= $taid[0]->name ?></td>
                                                    <td><span class="p-3" style="color:<?= $taskdata->color ?>;"><?= $taskdata->name ?></span></td>
                                                    <td><?= $time ?></td>
                                                    <td><?= $taskdata->appointmentdatetime ?></td>
                                                    <td><?= $taskuname ?></td>
                                                    <td><?php 
                                                    foreach ($filter_by as $key => $value) :
                                                      if($key == 'Plan_BY'){
                                                        echo $value."<br/>";
                                                      }elseif($key == 'Filter_By'){
                                                        echo $value."<br/>";
                                                      }else{
                                                      if($key == 'comp_status'){
                                                        $tstatus = $this->Menu_model->get_statusbyid($value);
                                                        $tstatusname = $tstatus[0]->name;
                                                        echo "<span class='p-1'>Status&nbsp;:&nbsp;".$tstatusname."</span><br/>";
                                                      }elseif($key == 'task'){
                                                        $taction = $this->Menu_model->get_actionbyid($value);
                                                        $tactionname = $taction[0]->name;
                                                        echo "<span class='p-1'>Task&nbsp;:&nbsp;".$tactionname."</span><br/>";
                                                      }elseif($key == 'taskActionbyuser'){
                                                        echo "<span class='p-1'>Action&nbsp;Taken:&nbsp;".$value."</span><br/>";
                                                      }else{
                                                          echo $selectby;
                                                        }
                                                      }
                                                    endforeach;
                                                    ?></td>
                                                     <?php /*
                                                    <td>
                                                        <?php 
                                                        if (is_array($filter_by)) : ?>
                                                            <?php foreach ($filter_by as $key => $value) :
                                                               if($key == 'Plan_BY'){
                                                                echo $value."<br/>";
                                                              }elseif($key == 'Filter_By'){
                                                                echo $value."<br/>";
                                                              }elseif($key == 'comp_status'){
                                                                $tstatus = $this->Menu_model->get_statusbyid($value);
                                                                $tstatusname = $tstatus[0]->name;
                                                                echo "<span class='p-1'>Status&nbsp;:&nbsp;".$tstatusname."</span><br/>";
                                                              }elseif($key == 'task'){
                                                                $taction = $this->Menu_model->get_actionbyid($value);
                                                                $tactionname = $taction[0]->name;
                                                                echo "<span class='p-1'>Task&nbsp;:&nbsp;".$tactionname."</span><br/>";
                                                              }elseif($key == 'taskActionbyuser'){
                                                                echo "<span class='p-1'>Action&nbsp;Taken:&nbsp;".$value."</span><br/>";
                                                              }else{
                                                                echo $key."<br/>";
                                                                echo $value;
                                                              }
                                                             endforeach; ?>
                                                        <?php endif; ?>
                                                    </td>
                                                    <?php */ ?>
                                                    <td>
                                                    <?php 
                                                   
                                                    if($taskdata->nextCFID == '0'){?>
                                                     <span class="p-1 bg-warning mr-2">Pending</span>
                                                      <?php }else{ ?>
                                                        <span class="p-1 bg-success mr-2">Complete</span>
                                                      <?php } ?>
                                                    </td>
                                                      <td>
                                                          <span class="p-1 bg-<?= ($status == 1) ? 'success' : (($status == '') ? 'warning' : 'danger');?> mr-2">
                                                          <?= ($status == 1) ? 'Approved' : (($status == '') ? 'Pending' : 'Rejected'); ?>
                                                          </span>
                                                      </td>
                                                      <td><?php
                                                      if($approver ==''){ ?>
                                                         <span class="p-1 bg-warning mr-2">Pending</span>
                                                      <?php }else{
                                                        if($approver == 'System'){ ?>
                                                         <span class="p-1 bg-success mr-2"><?= $approver ?></span>
                                                       <?php }else{
                                                          $userData = $this->Menu_model->get_userbyid($approver);
                                                          $name = $userData[0]->name;
                                                          echo $name;
                                                        }
                                                      
                                                      }
                                                       ?></td>
                                                      <td>
                                                      <?php if($status ==''){?>
                                                        <div>
                                                            <input type="checkbox" id="scales" value="<?= $taskdata->id ?>" name="tid[]" />
                                                        </div>
                                                        <?php }elseif($status == 0){
                                                          if($self_assign == ''){ ?>
                                                          <div>
                                                            <button type="button" class="btn btn-primary" onclick="window.location.href='<?= base_url(); ?>Menu/AssignTaskById/<?= $tblId ?>/<?= $taskdate ?>'">Assign Task</button> <hr>
                                                            <button type="button" class="btn btn-secondary" id="self_assign" style="margin-left: 10px;" onclick="window.location.href='<?= base_url(); ?>Menu/selfAssign/<?= $tblId ?>/1/<?= $taskdate ?>'" <?= ($self_assign == 1) ? 'disabled' : '' ?>>Self Assign</button>
                                                          </div>
                                                         <?php  }else if($self_assign == 1){ ?>
                                                          <span class="p-1 bg-success mr-2">Admin&nbsp;Created&nbsp;Self-Assign&nbsp;Request</span>
                                                          <?php } else if($self_assign == 2){?>
                                                          <span class="p-1 bg-success mr-2">Task&nbsp;successfully&nbsp;assigned&nbsp;by&nbsp;admin!</span>
                                                            <?php }else if($self_assign == 3){ ?>
                                                              <span class="p-1 bg-success mr-2">Task&nbsp;successfully&nbsp;assigned&nbsp;by&nbsp;user!</span>
                                                           <?php }else if($self_assign == 4){ ?>
                                                              <span class="p-1 bg-success mr-2">Task&nbsp;Approved&nbsp;&nbsp;by&nbsp;System!</span>
                                                           <?php }?>
                                                        <?php }elseif($status == 1){ ?>
                                                          <?php if($self_assign == 4){ ?>
                                                            <span class="p-1 bg-success mr-2">Task&nbsp;Approved&nbsp;&nbsp;by&nbsp;System!</span>
                                                          <?php }else{ ?>
                                                            <span class="p-1 bg-success mr-2">Approved</span> 
                                                         <?php }  ?>
                                                       <?php }?>
                                                    </td>
                                                      <?php $i++; } ?>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                  </div>
                                </fieldset>
                                </form>
                              </div>
                            
                        
                              <style>
                                 .chart-container {
                                  background: #fff;
                                  padding: 20px;
                                  box-shadow: 0 0 10px rgba(0,0,0,0.2);
                                  border-radius: 15px;
                                  /* width: 500px; */
                                }
                              </style>

                            <?php 
                            $graphTaskDatas =$this->Menu_model->getTaskDetailsOnPlannerGraphDatas($tuser_uid, $taskdate);
                            $statusTask    = $graphTaskDatas['statusTask'];
                            $actionTask    = $graphTaskDatas['actionTask'];
                            $categoryTask  = $graphTaskDatas['categoryTask'];
                            // dd($categoryTask);
                            ?>

                              <div class="row">
                                <div class="col-md-4">
                                    <div class="chart-container">
                                      <canvas id="taskPieChart"></canvas>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="chart-container">
                                      <canvas id="taskPieChartAction"></canvas>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="chart-container">
                                       <canvas id="funnelPieChart"></canvas>
                                    </div>
                                </div>
                              </div>

                     <script>
                        const rawData = <?= json_encode($statusTask) ?>;
                        const labels = rawData.map(item => item.name);
                        const counts = rawData.map(item => Number(item.task_count)); // convert to number
                        const total = counts.reduce((sum, val) => sum + val, 0);
                        const data = {
                          labels: labels,
                          datasets: [{
                            label: "Task Count",
                            data: counts,
                            backgroundColor: [
                              "#3366CC",  // Blue
                              "#109618",  // Green
                              "#DC3912",  // Red
                              "#FF9900",  // Orange
                              "#990099",  // Purple
                              "#0099C6",  // Light Blue
                              "#DD4477"   // Pink (7th color added)
                            ],
                            borderColor: "#fff",
                            borderWidth: 1
                          }]
                        };

                        const config = {
                          type: 'pie',
                          data: data,
                          options: {
                            responsive: true,
                            plugins: {
                              legend: {
                                position: 'right'
                              },
                              title: {
                                display: true,
                                text: 'Task Status Distribution',
                                font: { size: 18 }
                              },
                              datalabels: {
                                color: '#fff',
                                formatter: (value) => {
                                  const percentage = ((value / total) * 100).toFixed(1);
                                  return `${value} (${percentage}%)`;
                                },
                                font: {
                                  weight: 'bold',
                                  size: 13
                                }
                              }
                            }
                          },
                          plugins: [ChartDataLabels]
                        };

                        new Chart(document.getElementById('taskPieChart'), config);


                        const rawData_actionTask = <?= json_encode($actionTask) ?>;
                        const labels_action = rawData_actionTask.map(item => item.name);
                        const counts_action = rawData_actionTask.map(item => Number(item.task_count)); // convert to number
                        const total_action = counts_action.reduce((sum, val) => sum + val, 0);
                        const data_action = {
                          labels: labels_action,
                          datasets: [{
                            label: "Task Count",
                            data: counts_action,
                            backgroundColor: [
                              "#3366CC",  // Blue
                              "#109618",  // Green
                              "#DC3912",  // Red
                              "#FF9900",  // Orange
                              "#990099",  // Purple
                              "#0099C6",  // Light Blue
                              "#DD4477"   // Pink (7th color added)
                            ],
                            borderColor: "#fff",
                            borderWidth: 1
                          }]
                        };

                        const config_action = {
                          type: 'pie',
                          data: data_action,
                          options: {
                            responsive: true,
                            plugins: {
                              legend: {
                                position: 'right'
                              },
                              title: {
                                display: true,
                                text: 'Task Action Distribution ',
                                font: { size: 18 }
                              },
                              datalabels: {
                                color: '#fff',
                                formatter: (value) => {
                                  const percentage = ((value / total) * 100).toFixed(1);
                                  return `${value} (${percentage}%)`;
                                },
                                font: {
                                  weight: 'bold',
                                  size: 13
                                }
                              }
                            }
                          },
                          plugins: [ChartDataLabels]
                        };

                        new Chart(document.getElementById('taskPieChartAction'), config_action);

                          <?php 
                          // Convert object to associative array
                          $raw = (array) $categoryTask[0];

                          // Filter out values with 0 count
                          $filtered = array_filter($raw, function ($v) {
                              return $v > 0;
                          });

                          // Format labels: replace underscores with spaces
                          $labels = array_map(function($label) {
                              return str_replace('_', ' ', $label);
                          }, array_keys($filtered));

                          $counts = array_values($filtered);
                          ?>

                            const labels_Funnels = <?= json_encode($labels) ?>;
                            const counts_Funnels = <?= json_encode($counts) ?>;
                            const total_Funnels = counts.reduce(function (sum, val) { return sum + val; }, 0);

                            const data_funnel = {
                              labels: labels_Funnels,
                              datasets: [{
                                label: 'Client Funnels',
                                data: counts_Funnels,
                                backgroundColor: [
                                  '#3366CC', '#DC3912', '#FF9900', '#109618', '#990099',
                                  '#0099C6', '#DD4477', '#66AA00', '#B82E2E', '#316395',
                                  '#994499', '#22AA99', '#AAAA11', '#6633CC'
                                ],
                                borderColor: '#fff',
                                borderWidth: 1
                              }]
                            };

                            const config_funnel = {
                              type: 'pie',
                              data: data_funnel,
                              options: {
                                responsive: true,
                                plugins: {
                                  legend: {
                                    position: 'right'
                                  },
                                  title: {
                                    display: true,
                                    text: 'Funnel Category',
                                    font: {
                                      size: 18
                                    }
                                  },
                                  datalabels: {
                                    color: '#fff',
                                    formatter: function(value) {
                                      const percentage = ((value / total_Funnels) * 100).toFixed(1);
                                      return value + ' (' + percentage + '%)';
                                    },
                                    font: {
                                      size: 13,
                                      weight: 'bold'
                                    }
                                  }
                                }
                              },
                              plugins: [ChartDataLabels]
                            };

                            new Chart(
                              document.getElementById('funnelPieChart'),
                              config_funnel
                            );



                      </script>
                  
                              <?php  
                              $todaysDate = date("Y-m-d");
                              $getAllUserDataCurrentData    = $this->Menu_model->GetDailyTeamPlannerSummaryWwithLocationShareNew1($tuser_uid,$taskdate);
                              $getAllUserDataCurrentDatas   = $getAllUserDataCurrentData[0];
                              $task_plan_for_today_request            = $getAllUserDataCurrentDatas->task_plan_for_today_request;
                              $user_create_special_request_for_leave  = $getAllUserDataCurrentDatas->user_create_special_request_for_leave;
                              $user_request_any_reminder              = $getAllUserDataCurrentDatas->user_request_any_reminder;
                              $pending_task_planner_request           = $getAllUserDataCurrentDatas->pending_task_planner_request;
                              ?>
                          <hr>


                         <div class="card">
                              <div class="row">
                                <div class="col-md-3">
                                  <div class="card text-center p-2" style="background: linear-gradient(135deg, #FFD700, #FFA500); color: #fff;">
                                    <div class="card-header text-center">
                                      <h6>Create Request to Task Plan For Today</h6>
                                      <hr style="background-color: #fff;">
                                      <span><?=$task_plan_for_today_request?></span>
                                    </div>
                                  </div>
                                </div>
                                <div class="col-md-3">
                                  <div class="card text-center p-2" style="background: linear-gradient(135deg, #4B0082, #6A5ACD); color: #fff;">
                                  <div class="card-header text-center">
                                      <h6>Create Special Request For Leave</h6>
                                      <hr style="background-color: #fff;">
                                      <span><?=$user_create_special_request_for_leave?></span>
                                    </div>
                                  </div>
                                </div>
                                <div class="col-md-3">
                                  <div class="card text-center p-2" style="background: linear-gradient(135deg, #006400, #00FF7F); color: #fff;">
                                  <div class="card-header text-center">
                                      <h6>Create Request Any Reminder</h6>
                                      <hr style="background-color: #fff;">
                                      <span><?=$user_request_any_reminder?></span>
                                    </div>
                                  </div>
                                </div>
                                <div class="col-md-3">
                                  <div class="card text-center p-2" style="background: linear-gradient(135deg, #1E90FF, #4169E1); color: #fff;">
                                  <div class="card-header text-center">
                                      <h6>Pending Task Planner Request</h6>
                                      <hr style="background-color: #fff;">
                                      <span><?=$pending_task_planner_request?></span>
                                    </div>
                                  </div>
                                </div>
                              </div>
                            </div>
                            <hr>

                          
                        
                  <div class="card">
                  <div class="card-body">
                  <div class="card-header" style="align-items: center; justify-content: center; display: flex; background: linear-gradient(135deg, #6a11cb, #2575fc); color: #fff;">
                    <h3>Review Planned By Users</h3>
                  </div>
                      <div class="table-responsive text-nowrap">
                            <table id="example13" class="table table-striped table-bordered" cellspacing="0" width="100%">
                              <thead class="thead-dark">
                                <tr>
                                  <!-- <th>Review Report</th>-->
                                  <th>#</th>
                                  <th>By User</th>
			                            <th>To User</t>
                                  <th>Review Type</th>
                                  <th>Plan Time Time</th>
                                  <th>Close Date Time</th>
                                  <th>System Remarks</th>
                                  <th>Review Time</th>
                                </tr>
                                </tr>
                              </thead>
                              <tbody>
                                <?php $i=1;
                                  foreach($reviewaDatas as $md){
                                      $startt=$md->startt;
                                      $closet=$md->closet;
                                      $reviewstartplant = $md->plant;
                                      $review_close_time =$md->review_close_time;
                                      $byuserid = $md->uid;
                                      $byuserData= $this->Menu_model->get_userbyid($byuserid);
                                      $byuser = $byuserData[0]->name;             
                                      $bdname = $md->bdid;
                                      $bdiduidData = $this->Menu_model->get_userbyid($bdname);
                                      $touser = $bdiduidData[0]->name;

                                      if(!is_null($startt)){
                                        $nstartt= date('d-m-Y  h:i A', strtotime($startt));
                                                          
                                     }else{
                                      $nstartt= "<span class='bg-warning p-2'>Not Start</span>";
                                      }
                                      if(!is_null($closet)){
                                        $ncloset = date('d-m-Y  h:i A', strtotime($closet));
                                        $tdiff = $this->Menu_model->timediff($startt,$closet);
                                      }else{
                                        $ncloset = "<span class='bg-warning p-2'>Not Close</span>";
                                        $closetcur = date("Y-m-d H:i:s");
                                        $tdiff = $this->Menu_model->timediff($startt,$closetcur);
                                         }
                                      
                                         $review_close_fix_time    = date('H:i:s', strtotime($review_close_time));
                                         if ($review_close_fix_time === '00:00:00') {
                                          $review_time =  '';
                                        } else {

                                        // Convert datetime strings to timestamps
                                        $startReviewTimestamp       = strtotime($reviewstartplant);
                                        $endReviewTimestamp         = strtotime($review_review_close_time);
                                        // Calculate the difference in seconds
                                        $differenceInSecondsReview  = $endReviewTimestamp - $startReviewTimestamp;
                                        // Convert the difference to minutes
                                        $differenceInMinutesReview  = $differenceInSecondsReview / 60;
                                        // Calculate hours and minutes
                                        $reviewHours                = floor($differenceInSecondsReview / 3600);
                                        $reviewMinutes              = floor(($differenceInSecondsReview % 3600) / 60);
                                        
                                        $totalReviewTimeinMinutes  = $differenceInMinutesReview;

                                        $review_time =  "$reviewHours Hours and  $reviewMinutes Minutes";

                                        }
                                      ?>
                                <tr>
                                  <td><?=$i?></td>
                                  <td><?=$byuser;?></td>
				                          <td><?=$touser; ?></td>
                                  <td><?=$md->reviewtype?></td>
                                  <td><?=$reviewstartplant?></td>
                                  <td><?=$review_close_time?></td>
                                  <td><?=$md->plan_time_remarks?></td>
                                  <td><?=$review_time?></td>
                                </tr>
                                </a>
                                <?php $i++;} ?>
                              </tbody>
                            </table>    
                      </div>
                  </div>
                </div>
                                  <hr>
                                  <?php 
                            $todaysPlannerRequestDatas    = $this->Menu_model->GetTodaysPlannerRequestNewByDate($tuser_uid,$taskdate);
                            ?>
                      <div class="table-responsive">
                      <div class="card-header text-center" style="background: linear-gradient(135deg, #667eea, #764ba2); color: #fff;">
                        <h3 class="mb-0">Task Approval Request</h3>
                      </div>

                            <table id="exampledata14" class="table table-striped table-bordered" cellspacing="0" width="100%">
                              <thead class="thead-dark">
                                <tr>
                                  <th scope="col">#</th>
                                  <th scope="col">User Name</th>
                                  <th scope="col">Date</th>
                                  <th scope="col">Request Message</th>
                                  <th scope="col">Approvel Status</th>
                                  <th scope="col">Remarks</th>
                                  <th scope="col">Action</th>
                                </tr>
                              </thead>
                              <tbody>
                                <?php
                                $j =1;
                                foreach($todaysPlannerRequestDatas as $data){ ?>
                                <tr>
                                  <th><?= $j ?></th>
                                  <td><?= $this->Menu_model->get_userbyid($data->user_id)[0]->name ?></td>
                                  <td><?= $data->date ?></td>
                                  <td><?= $data->request_remarks ?></td>
                                  <td>
                                    <?php
                                    if($data->approvel_status == ''){ ?>
                                    <span class="p-1 bg-warning mr-2">Pending</span>
                                    <?php }else if($data->approvel_status == 'Approved'){ ?>
                                    <span class="p-1 bg-success mr-2">Approved</span>
                                    <?php }else{ ?>
                                    <span class="p-1 bg-danger mr-2">Reject</span>
                                    <?php }?>
                                  </td>
                                  <td><?=$data->remarks ?></td>
                                  <td>
                                    
                                    <?php
                                    if($data->approvel_status == ''){ ?>
                                    
                                    <span class="p-1 bg-warning mr-2">Pending</span>
                                    
                                    <?php }else if($data->approvel_status == 'Approved'){ ?>
                                    <span class="p-1 bg-success mr-2">Approved</span>
                                    <?php }else{ ?>
                                    <span class="p-1 bg-danger mr-2">Reject</span>
                                    <?php }?>
                                    
                                  </td>
                                </tr>
                                <?php $j++; } ?>
                              </tbody>
                            </table>
                          </div>

                  <hr>


                  <div class="card">
                  <?php 
                            $todaysOldPlannerRequestDatas    = $this->Menu_model->GetAllTodaysCreatePlannerRequestByUserID($tuser_uid,$taskdate);
                            ?>
                  <div class="table-responsive text-nowrap">
                  <div class="card-header text-center" style="background: linear-gradient(135deg, #11998e, #38ef7d); color: #fff;">
                      <h3 class="mb-0">Today's Old / Planned But Not Initiated Task Planner Request</h3>
                     
                    </div>

                      <table id="exampledata" class="table table-striped table-bordered" cellspacing="0" width="100%">
                        <thead class="thead-dark">
                          <tr>
                            <th scope="col">#</th>
                            <th scope="col">User Name</th>
                            <th scope="col">Date</th>
                            <th scope="col">Request Type</th>
                            <th scope="col">Task Count for Planning at Request Time</th>
                            <th scope="col">Current Time Pending Task Count</th>
                            <th scope="col">Request Message</th>
                            <th scope="col">Approvel Status</th>
                            <th scope="col">Approvel By</th>
                            <th scope="col">Approved / Reject Remarks</th>
                            <th scope="col">Action</th>
                          </tr>
                        </thead>
                        <tbody>
                          <?php
                            $j =1;
                            
                            foreach($todaysOldPlannerRequestDatas as $pendatareg){
                              $request_user_id = $pendatareg->request_user_id;
                              $request_type    = $pendatareg->request_type;
                              if($request_type == 'Old Pending Task'){
                                  $getoldPendingTask = $this->Menu_model->get_OLDPendingTask($request_user_id);
                                  $getpendingTaskcnt = sizeof($getoldPendingTask);
                              }elseif($request_type == 'Plan But Not Initiated'){
                                $getPendingTask = $this->Menu_model->get_PendingTask($request_user_id);
                                $getpendingTaskcnt = sizeof($getPendingTask);
                              }
                              ?>
                          <tr>
                            <th><?= $j ?></th>
                            <td><?= $pendatareg->request_name ?></td>
                            <td><?= $pendatareg->created_at ?></td>
                            <td><?= $pendatareg->request_type ?></td>
                            <td><?= $pendatareg->task_count ?></td>
                            <td>
                              <a href="<?=base_url().'Menu/CheckUserPendingTaskList/'.$pendatareg->id.'/'.$pendatareg->request_user_id.'/'.$pendatareg->request_date?>" target="_BLANK"><?=$getpendingTaskcnt;?> Task</a>
                            </td>
                            <td><?= $pendatareg->request_remarks ?></td>
                            <td>
                              <?php
                                if($pendatareg->approved == 0){ ?>
                              <span class="p-1 bg-warning mr-2">Pending</span>
                              <?php }else if($pendatareg->approved == 1){ ?>
                              <span class="p-1 bg-success mr-2">Approved</span>
                              <?php }else if($pendatareg->approved == 2){ ?>
                              <span class="p-1 bg-danger mr-2">Reject</span>
                              <?php }?>
                            </td>
                            <td><?php if($pendatareg->approved_message !== ''){echo $pendatareg->approved_by_name;}else{echo " <span class='p-1 bg-warning mr-2'>Pending</span>";} ?></td>
                            <td><?php if($pendatareg->approved_message !== ''){echo $pendatareg->approved_message;}else{echo " <span class='p-1 bg-warning mr-2'>Pending</span>";} ?></td>
                            <td>
                              <?php
                                if($pendatareg->approved ==0){ ?>
                             <span class="p-1 bg-warning mr-2">Pending</span>
                              <?php }else if($pendatareg->approved == 1){ ?>
                              <span class="p-1 bg-success mr-2">Approved&nbsp;</span>
                              <?php }else if($pendatareg->approved == 2){ ?>
                              <span class="p-1 bg-danger mr-2">Rejected&nbsp;</span>
                              <?php }?>
                            </td>
                          </tr>
                          <?php $j++; } ?>
                        </tbody>
                      </table>
                    </div>
                  </div>

                                  <hr>


                                  <?php $alllogData   =   $this->Menu_model->GetAllTodaysPlannerLogByUid($tuser_uid,$taskdate);  ?>
                                  <div class="card">
                                  <div class="card-header" style="align-items: center; justify-content: center; display: flex; background: linear-gradient(135deg, #8E44AD, #DDA0DD); color: #fff;">
                                    <h3>All Planner Log Planned By Users</h3>
                                  </div>

                                      <br>
                                      <div class="table-responsive text-nowrap">
                                      <table id="example12" class="table table-striped table-bordered" cellspacing="0" wi$dth="100%">
                                          <thead class="thead-dark">
                                          <tr>
                                              <th>S.No.</th>
                                              <th>BD Name</th>
                                              <th>Log By</th>
                                              <th>Company Name</th>
                                              <th>Task Name</th>
                                              <th>Orginal Task Create Date</th>
                                              <th>Recent Task Update Date</th>
                                              <th>Cureent Status</th>
                                              <th>Replaned Times </th>
                                              <th>Time Difference</th>
                                          </tr>
                                          </thead>
                                          <tbody>
                                          <?php $i=1; foreach($alllogData as $data): ?>
                                          <tr>
                                              <td><?= $i; ?></td>
                                              <td><?= $data->to_user_name; ?></td>
                                              <td><?= $data->remarks; ?></td>
                                              <td><a href="<?=base_url();?>Menu/CompanyDetails/<?=$data->cid;  ?>" target="_BLANK"><?= $data->company_name; ?></a></td>
                                              <td><?= $data->task_name; ?></td>
                                              <td><?= $data->org_task_date; ?></td>
                                              <td><?= $data->last_create_date; ?></td>
                                              <td><?= $data->name; ?></td>
                                              <td><a href="<?=base_url();?>Menu/ReplanedLog/<?=$data->task_id;  ?>" target="_BLANK"><?= $data->duplicate_count; ?></a></td>
                                              <td><?= $data->time_difference; ?></td>
                                          </tr>
                                          <?php $i++; endforeach; ?>
                                          </tbody>
                                      </table>
                                      </div>
                                  </div>



                            </div>
                          </div></div></div>
                          </div>
                          <!-- /.card-body -->
                        </div>
                        <!-- /.card -->
                      </div>
                      <!-- /.col -->
                    </div>
                    <!-- /.row -->
                  </div>
                  <!-- /.container-fluid -->
                  <div class="modal fade" id="exampleModalCenterdata" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered" role="document">
                      <div class="modal-content">
                        <div class="modal-header">
                          <h5 class="modal-title" id="exampleModalLongTitle">Add Reject Remark</h5>
                          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                          <span aria-hidden="true">&times;</span>
                          </button>
                        </div>
                        <div class="modal-body">
                          <form action="<?=base_url();?>Menu/momReject" method="post" >
                            <input type="hidden" id="rejectid" value="" name="reject">
                            <div class="mb-3 mt-3">
                              <textarea id="rejectreamrk" name="rejectreamrk" cols="30" placeholder="Add Remark " class="form-control"  rows="4"></textarea>
                            </div>
                            <div class="form-group text-center">
                              <button type="submit" class="btn btn-success mt-2">Submit</button>
                            </div>
                          </form>
                        </div>
                      </div>
                    </div>
                  </div>
                </section>
                
                
                <footer class="main-footer">
                  <strong>Copyright &copy; 2021-2022 <a href="<?=base_url();?>">Stemlearning</a>.</strong>
                  All rights reserved.
                  <div class="float-right d-none d-sm-inline-block">
                    <b>Version</b> 1.0
                  </div>
                </footer>
                <!-- Control Sidebar -->
                <aside class="control-sidebar control-sidebar-dark">
                  <!-- Control sidebar content goes here -->
                </aside>
                <!-- /.control-sidebar -->
              </div>
              <!-- ./wrapper -->
              <!-- jQuery -->
              <script src="<?=base_url();?>assets/js/jquery.min.js"></script>
              <!-- jQuery UI 1.11.4 -->
              <script src="<?=base_url();?>assets/js/jquery-ui.min.js"></script>
              <!-- Resolve conflict in jQuery UI tooltip with Bootstrap tooltip -->
              <script>
              $.widget.bridge('uibutton', $.ui.button)
              </script>
              <!-- Bootstrap 4 -->
              <script src="<?=base_url();?>assets/js/bootstrap.bundle.min.js"></script>
              <!-- ChartJS -->
              <script src="<?=base_url();?>assets/js/Chart.min.js"></script>
              <!-- Sparkline -->
              <script src="<?=base_url();?>assets/js/sparkline.js"></script>
              <!-- JQVMap -->
              <script src="<?=base_url();?>assets/js/jquery.vmap.min.js"></script>
              <script src="<?=base_url();?>assets/js/jquery.vmap.usa.js"></script>
              <!-- jQuery Knob Chart -->
              <script src="plugins/jquery-knob/jquery.knob.min.js"></script>
              <!-- daterangepicker -->
              <script src="<?=base_url();?>assets/js/moment.min.js"></script>
              <script src="<?=base_url();?>assets/js/daterangepicker.js"></script>
              <!-- Tempusdominus Bootstrap 4 -->
              <script src="<?=base_url();?>assets/js/tempusdominus-bootstrap-4.min.js"></script>
              <!-- Summernote -->
              <script src="<?=base_url();?>assets/js/summernote-bs4.min.js"></script>
              <!-- overlayScrollbars -->
              <script src="<?=base_url();?>assets/js/jquery.overlayScrollbars.min.js"></script>
              <!-- DataTables  & Plugins -->
              <script src="<?=base_url();?>assets/js/jquery.dataTables.min.js"></script>
              <script src="<?=base_url();?>assets/js/dataTables.bootstrap4.min.js"></script>
              <script src="<?=base_url();?>assets/js/dataTables.responsive.min.js"></script>
              <script src="<?=base_url();?>assets/js/responsive.bootstrap4.min.js"></script>
              <script src="<?=base_url();?>assets/js/dataTables.buttons.min.js"></script>
              <script src="<?=base_url();?>assets/js/buttons.bootstrap4.min.js"></script>
              <script src="<?=base_url();?>assets/js/jszip.min.js"></script>
              <script src="<?=base_url();?>assets/js/pdfmake.min.js"></script>
              <script src="<?=base_url();?>assets/js/vfs_fonts.js"></script>
              <script src="<?=base_url();?>assets/js/buttons.html5.min.js"></script>
              <script src="<?=base_url();?>assets/js/buttons.print.min.js"></script>
              <script src="<?=base_url();?>assets/js/buttons.colVis.min.js"></script>
              <!-- AdminLTE App -->
              <script src="<?=base_url();?>assets/js/adminlte.js"></script>
              <!-- AdminLTE dashboard demo (This is only for demo purposes) -->
              <script src="<?=base_url();?>assets/js/dashboard.js"></script>
              <script>
                $(document).ready(function() {


                $("#example1").DataTable({
                    "responsive": false,
                    "lengthChange": false,
                    "autoWidth": false,
                    "buttons": ["csv", "excel", "pdf", "print"],
                    "columnDefs": [
                        { "width": "200px", "targets": 0 },  // Change the width of the first column
                        { "width": "300px", "targets": 7 }   // Change the width of the eighth column
                        // Add more columns if needed
                    ]
                }).buttons().container().appendTo('#example1_wrapper .col-md-6:eq(0)');

                $("#example12").DataTable({
                    "responsive": false,
                    "lengthChange": false,
                    "autoWidth": false,
                    "buttons": ["csv", "excel", "pdf", "print"],
                    "columnDefs": [
                        { "width": "200px", "targets": 0 },  // Change the width of the first column
                        { "width": "300px", "targets": 7 }   // Change the width of the eighth column
                        // Add more columns if needed
                    ]
                }).buttons().container().appendTo('#example12_wrapper .col-md-6:eq(0)');

                $("#example13").DataTable({
                    "responsive": false,
                    "lengthChange": false,
                    "autoWidth": false,
                    "buttons": ["csv", "excel", "pdf", "print"],
                    "columnDefs": [
                        { "width": "200px", "targets": 0 },  // Change the width of the first column
                        { "width": "300px", "targets": 7 }   // Change the width of the eighth column
                        // Add more columns if needed
                    ]
                }).buttons().container().appendTo('#example13_wrapper .col-md-6:eq(0)');

                $("#exampledata14").DataTable({
                    "responsive": false,
                    "lengthChange": false,
                    "autoWidth": false,
                    "buttons": ["csv", "excel", "pdf", "print"],
                    "columnDefs": [
                        { "width": "200px", "targets": 0 },  // Change the width of the first column
                        { "width": "300px", "targets": 7 }   // Change the width of the eighth column
                        // Add more columns if needed
                    ]
                }).buttons().container().appendTo('#example14_wrapper .col-md-6:eq(0)');

                    
                    
                    // $('#self_assign').on('click', function() {
                    //     $(this).hide();
                    //     window.location.href = '<?= base_url(); ?>Menu/selfAssign/<?= $tblId ?>/1/<?= $taskdate ?>';
                    // });
                });
                </script>
              <script type='text/javascript'>
              function RejectButton(mid,id,val){
              $('#exampleModalCenter'+mid).modal('show');
              $('#exampleModalCenter'+mid+' #rejectid').val(id);
              }
              
              function Reject(mid,id,val){
              $('#exampleModalCenterdata').modal('show');
              $('#rejectid').val(id);
              }
              $('#plandate').val($dt);
              </script>
            </body>
          </html>
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
            <div class="row mb-2">
              <div class="col-sm-6">
                <h4 class="m-0"> <i>Planned Task Status</i> </h4>
                </div><!-- /.col -->
                <div class="col-sm-6">
                  <ol class="breadcrumb float-sm-right">
                    <h4><?php $uid=$user['user_id']; ?></h4>
                  </ol>
                  </div><!-- /.col -->
                  </div><!-- /.row -->
                  </div><!-- /.container-fluid -->
                </div>
                <!-- /.content-header -->
                <?php
                $bd = $this->Menu_model->get_userbyaid($uid);
                ?>
                <!-- Main content -->
                <section class="content">
                  <div class="container-fluid">
                    <div class="row">
                    <div class="col-md-4">
                    <form class="setpaldate" action="<?=base_url();?>Menu/UserTaskViewPage" method="post">
                      <input type="date" class=" m-2" name="adate" value="<?=$date?>" required="" id="plandate"  max="<?= date('Y-m-d') ?>">
                      <input type="submit" class="btn-warning m-2" value="Set Date">
                    </form>
                    </div>
                    </div>  
                    <?php 
                      $totalttaskdata =$this->Menu_model->getTotalUserTaskDetailsOnPlanner($uid,$date);
                      $taskplanmincount = 0;
                      $new_datetimemin = '';
                      
                      $totalttaskdata1 = $this->Menu_model->getUserTotalTaskTimeForTodays1($uid,$date);
                      $taskplanmincount = $totalttaskdata1[0]->ttime;

                      
                      $reviewaDatas    = $this->Menu_model->GetAllPlannedReviewByUserByDate($uid,$date);
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

                          // Convert datetime strings to timestamps
                          $startReviewTimestamp       = strtotime($review_plant_date_time);
                          $endReviewTimestamp         = strtotime($review_review_close_time);
                          // Calculate the difference in seconds
                          $differenceInSecondsReview  = $endReviewTimestamp - $startReviewTimestamp;
                          // Convert the difference to minutes
                          $differenceInMinutesReview  = $differenceInSecondsReview / 60;
                          // Calculate hours and minutes
                          $reviewHours                = floor($differenceInSecondsReview / 3600);
                          $reviewMinutes              = floor(($differenceInSecondsReview % 3600) / 60);
                          
                          $totalReviewTimeinMinutes  += $differenceInMinutesReview;

                          }
                        }

                        $taskplanmincount           = $taskplanmincount + $totalReviewTimeinMinutes;

                      }else{
                        $review_time =  0;
                      }






                      // echo $this->db->last_query();
                      // echo "taskplanmincount = ".$taskplanmincount;
                      // echo $taskplanmincount;
                      $lunchtime      = 30;      // Lunch Time 45 Miniute
                      $autoTasktime   = 90;  // 90 Minutes For Auto Task
                      $topp           = 60; // 60 Minutes For Tommorow Planner Planning
                      $texpense_time  = $lunchtime + $autoTasktime + $topp; // totol expense time
                      $nine_hours_planning =540; // 9 hours Planning = 9* 60 = 540 Minutes 
                      $userplanetime = $nine_hours_planning - $texpense_time; // total plan time  - 345 minutes
                      $plannerremTime = $userplanetime - $taskplanmincount; 
                      $request    = $this->Menu_model->GetTodaysPlannerRequest($uid);
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
                    //echo $taskplanmincount;
                    // echo "<br/>";
                    // echo $userplanetime;
              if($taskplanmincount >= $userplanetime){    
                        $background = 'bg-success';                           
                    }else{
                        $background = 'bg-danger';
                      } 
                   if($taskplanmincount <= $userplanetime){  ?>         
                <marquee class="p-2 mt-1 <?=$background?>" onMouseOver="this.stop()" onMouseOut="this.start()" width="100%" behavior="left" bgcolor="pink">
                  <h6> 
                  <?php 
                      $uprhours = floor( $plannerremTime / 60);
                      $uprremainingMinutes =  $plannerremTime % 60;
                    ?>
                    <p><?=$uprhours?> hour <?=$uprremainingMinutes ?> Minute remaining for task plan to enable Task Approval Function.</p>
                  </h6>
                </marquee>
                <?php }else{  ?>  
                  <marquee class="p-2 mt-1 <?=$background?>" onMouseOver="this.stop()" onMouseOut="this.start()" width="100%" behavior="left" bgcolor="pink">
                  <h6> 
                    <p>Congratulations! You've successfully achieved your planning time. Your dedication and hard work are truly commendable. Keep up the great momentum! 🎉</p>
                  </h6>
                </marquee>
                <?php }?>
                <marquee class="p-2 mt-1" width="100%" onMouseOver="this.stop()" onMouseOut="this.start()" behavior="left" bgcolor="pink">
                  <h6> Lunch Time : <?= $lunchtime ?>  Miniute || Auto Task Time : <?= $autoTasktime?> Minutes || Tommorow Planner Planning : <?=$topp ?>  Minutes || 9 hours Planning = 9* 60 = 540 Minutes || Total Time For (Lunch + Auto Task + Tommorow Planner) : <?=$texpense_time?>  Minutes || Task Planner Should be <?php echo 540 - $texpense_time;?> Minutes</h6>
                </marquee>
                <?php  if($requestcnt > 0){ 
                                     if(!is_null($apr_time)){
                                    ?>
                                 <div class="row">
                                   <div class="col-md-4">
                                      <div class="card bg-info">
                                        <div class="card-header text-center">
                                          <h6>Planner Request Time :</h6>
                                          <span><?= $request_time; ?></span>
                                        </div>
                                      </div>
                                    </div>
                                    <div class="col-md-4">
                                      <div class="card bg-success">
                                        <div class="card-header text-center">
                                          <h6>Planner Approved Time :</h6>
                                          <span><?= $apr_times; ?></span>
                                        </div>
                                      </div>
                                    </div>
                                    <div class="col-md-4">
                                      <div class="card bg-danger">
                                        <div class="card-header text-center">
                                          <h6>Late Approved Time:</h6>
                                          <span><?= $reqlateapr; ?></span>
                                        </div>
                                      </div>
                                    </div>
                                    <marquee class="p-2 mt-1" width="100%"  onMouseOver="this.stop()" onMouseOut="this.start()" behavior="left" bgcolor="pink">
                                    <small><span><?= $alertmessage; ?></span></small>
                                  </marquee>
                                   </div>
                                   <?php }} ?>
                      <div class="card">
                        <div class="card-header">
                          <h3 class="card-title"></h3>
                        </div>
                        <!-- /.card-header -->
                        <div class="card-body">
                          <div class="body-content">
                            <div class="page-header">
                              <!-- <form method="post" action="<?=base_url();?>Menu/UserTaskViewPage"> -->
                              <!-- <input type="hidden" name="selected_date" id="selected_date" value="<?= $date ?>">
                              //<button class="btn btn-primary m-2" type="submit" >Submit</button> -->
                              <fieldset>
                                 <div class="card">
                                    <div class="text-center p-2 bg-info">
                                       <h5><i>Task Planned By - 
                                        <?php  
                                        $udetail = $this->Menu_model->get_userbyid($uid);
                                        echo $udetail[0]->name;
                                       ?>  </i></h5>
                                       <b>
                                          <?php
                                          $hours = floor($taskplanmincount / 60);
                                          $remainingMinutes = $taskplanmincount % 60;
                                          echo " User Planned $hours hours and $remainingMinutes minutes.";
                                            ?>
                                        </b>
                                    </div>
                                    <hr>
                                    <div class="card p-2 <?= $background?>">
                                        <div class="row">
                                          <div class="col-md-4">
                                            <?php 
                                             $uphours = floor($userplanetime / 60);
                                             $upremainingMinutes = $userplanetime % 60;
                                            ?>
                                            <p>Planning Time : <?php  echo "$uphours hours and $upremainingMinutes minutes."; ?></p>
                                          </div>
                                          <div class="col-md-4">
                                            <?php 
                                             $upchours = floor($taskplanmincount / 60);
                                             $upcremainingMinutes = $taskplanmincount % 60;
                                            ?>
                                            <p>User Plan Time : <?php  echo "$upchours hours and $upcremainingMinutes minutes."; ?></p>
                                          </div>
                                          <div class="col-md-4">
                                            <?php 
                                             $uprhours = floor( $plannerremTime / 60);
                                             $uprremainingMinutes =  $plannerremTime % 60;
                                             if($taskplanmincount <= $userplanetime){  
                                            ?>
                                            <p>Remaing Time For Plan Time : <?php  echo "$uprhours hours and $uprremainingMinutes minutes."; ?></p>
                                           <?php } else{
                                              echo "successfully achieved their planning time.";
                                             } ?>  
                                          </div>
                                        </div>
                                    </div>
                                    <hr>
                                <div class="table-responsive">
                                  <div class="table-responsive text-nowrap">
                                    <div class="pdf-viwer">
                                        <table id="example1" class="table table-striped table-bordered" cellspacing="0" width="100%">
                                            <thead class="thead-dark">
                                                <tr>
                                                    <th>Sr. no</th>
                                                    <th>Company Name</th>
                                                    <th>Action Name</th>
                                                    <th>Status</th>
                                                    <th>Task Time</th>
                                                    <th>Task Appointment date time</th>
                                                    <th>Plan By</th>
                                                    <th>Filter Used</th>
                                                    <!-- <th>Filter By</th> -->
                                                    <th>Task Work Status</th>
                                                    <th>Action Status</th>
                                                    <th>Approved/Rejected By</th>
                                                    <th>Action</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                            <?php 
                                                $i = 1;
                                                $selectDate = isset($selected_date) ? $selected_date: $date;
                                                $totalttaskdata = $this->Menu_model->getTotalUserTaskDetailsOnPlanner($uid, $selectDate);
                                                foreach ($totalttaskdata as $taskdata) { 
                                            /*  if($taskdata->approved_status == 1) {
                                                    continue;
                                                }
                                              if($taskdata->approved_status == 0 && $taskdata->self_assign == 2 || $taskdata->self_assign == 3) {
                                                  continue;
                                              }
                                           */
                                                    $taid = $taskdata->actiontype_id;
                                                    $tblId = $taskdata->id;
                                                    $taid = $this->Menu_model->get_actionbyid($taid);
                                                    $time = $taskdata->appointmentdatetime;
                                                    $reminder = $taskdata->reminder;
                                                    $rimby = $taskdata->reminderby;
                                                    $rimat = $taskdata->reminderat;
                                                    $assignedto_id = $taskdata->assignedto_id;
                                                    $status = $taskdata->approved_status;
                                                    $approver = $taskdata->approved_by;
                                                    $filter_by = json_decode($taskdata->filter_by, true);
                                                    $selectby = $taskdata->selectby;
                                                    $selfAssign = $taskdata->self_assign;
                                                    $rimbyname = $this->Menu_model->get_userbyid($rimby);
                                                    $time = date('h:i a', strtotime($time));
                                                    $taskuser = $this->Menu_model->get_userbyid($assignedto_id);
                                                    $taskuname = $taskuser[0]->name;
                                                ?>
                                                <tr>
                                                    <td><?= $i; ?></td>
                                                    <td><?= $taskdata->compname ?></td>
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
                                                    if($taskdata->nextCFID == 0){?>
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
                                                      <?php if($selfAssign == 1){?>
                                                          <div>                                                         
                                                          <button type="button" class="btn btn-primary" onclick="window.location.href='<?= base_url(); ?>Menu/selfTaskAssignPage/<?= $tblId ?>'">Self Assign task</button>
                                                          </div>
                                                        <?php }else if($selfAssign == 2){ ?>
                                                          <span class="p-1 bg-success mr-2">Task&nbsp;successfully&nbsp;assigned&nbsp;by&nbsp;admin!</span>
                                                       <?php }else if($selfAssign == 3){ ?>
                                                          <span class="p-1 bg-success mr-2">User&nbsp;Assigned&nbsp;Task&nbsp;via&nbsp;Admin&nbsp;Request</span>
                                                       <?php }else if($status ==1 && $selfAssign == 4){?>
                                                        <span class="p-1 bg-success mr-2">Task&nbsp;Approved&nbsp;&nbsp;by&nbsp;System!</span>
                                                       <?php }else if($status ==1 && $selfAssign == ''){?>
                                                          <span class="p-1 bg-success mr-2">Approved</span>
                                                       <?php }else if($status ==0 && $selfAssign == ''){ ?>
                                                        <span class="p-1 bg-warning mr-2">Pending</span>
                                                       <?php } ?>
                                                    </td>
                                                <?php $i++; }?>  
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                  </div>
                                </fieldset>
                              </div>
                            </div></div></div></div>



                            
                          <hr>
                  <div class="card">
                  <div class="card-body">
                      <div class="card-header bg-primary" style="align-items: center; justify-content: center; display: flex ;">
                      <h3 class="card-title">Review Planned By Users</h3>
                    </div>
                      <div class="table-responsive">
                            <table id="example13" class="table table-striped table-bordered" cellspacing="0" width="100%">
                              <thead class="thead-dark">
                                <tr>
                                  <!-- <th>Review Report</th>-->
                                  <th>S.No.</th>
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
                            { "width": "300px", "targets": 7 }   // Change the width of the second column
                            // Add more columns if needed
                        ]
                    }).buttons().container().appendTo('#example1_wrapper .col-md-6:eq(0)');
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
              // function setSelectedDate(date) {
              // document.getElementById('selected_date').value = date;
              // }
              </script>
            <!-- <script>
                $(document).ready(function() {
                    $('#plandate').on('change', function() {
                        var selectedDate = $(this).val();
                       alert(selectedDate); return false;
                        $.ajax({
                            url: '<?= base_url(); ?>Menu/UserTaskViewPage',
                            type: 'POST',
                            data: selectedDate,
                            success: function(response) {
                                // Handle the response from the server
                                console.log('Data sent successfully');
                                // You can also update your page content here based on the response
                            },
                            error: function(error) {
                                // Handle any errors
                                console.log('Error in sending data');
                            }
                        });
                    });
                });
            </script> -->
            </body>
          </html>

<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Live Day Planner | STEM APP | WebAPP</title>
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
      tr,td{
      font-weight: bold;
      }
      .card-header{
      background: floralwhite;
      }
    </style>
  </head>
  <body class="hold-transition sidebar-mini layout-fixed">
    <div class="wrapper">
      <!-- Preloader -->
      <!-- Navbar -->
      <?php $this->load->view($dep_name.'/nav.php');?>
      <style>
        .gradient-text,.gradient-text1{color:transparent;animation:5s infinite gradientAnimation}.gradient-text{background:linear-gradient(90deg,#ff8a00,#e52e71,#1e90ff);background-size:300% 300%;-webkit-background-clip:text;background-clip:text}.gradient-text1{background:linear-gradient(90deg,#e113aa,#1500ff,#1e90ff);background-size:300% 300%;-webkit-background-clip:text;background-clip:text}@keyframes gradientAnimation{0%,100%{background-position:0 50%}50%{background-position:100% 50%}}.card-height{height:600px}.card-height1{height:300px}.maparea{max-width:100%;border-radius:20px;padding:8px;background:linear-gradient(135deg,#e3f2fd,#fce4ec);border:3px solid transparent;background-clip:padding-box;position:relative;overflow:hidden;transition:transform .3s,box-shadow .3s;align-items:center;justify-content:center;display:flex;height:100%}.maparea:hover{box-shadow:0 12px 35px rgba(0,0,0,.25)}.custom-btn{width:130px;height:40px;color:#fff;border-radius:5px;padding:10px 25px;font-family:Lato,sans-serif;font-weight:500;background:0 0;cursor:pointer;transition:.3s;position:relative;display:inline-block;box-shadow:inset 2px 2px 2px 0 rgba(255,255,255,.5),7px 7px 20px 0 rgba(0,0,0,.1),4px 4px 5px 0 rgba(0,0,0,.1);outline:0}.btn-11{border:none;background:#212ffb;background:linear-gradient(0deg,#3e21fb 0,#4c5cea 100%);color:#fff;overflow:hidden}.btn-11:hover{text-decoration:none;color:#fff;opacity:.7}.btn-11:before{position:absolute;content:'';display:inline-block;top:-180px;left:0;width:30px;height:100%;background-color:#fff;animation:3s ease-in-out infinite shiny-btn1}.btn-11:active{box-shadow:4px 4px 6px 0 rgba(255,255,255,.3),-4px -4px 6px 0 rgba(116,125,136,.2),inset -4px -4px 6px 0 rgba(255,255,255,.2),inset 4px 4px 6px 0 rgba(0,0,0,.2)}@keyframes shiny-btn1{0%{transform:scale(0) rotate(45deg);opacity:0}80%{transform:scale(0) rotate(45deg);opacity:.5}81%{transform:scale(4) rotate(45deg);opacity:1}100%{transform:scale(50) rotate(45deg);opacity:0}}
        .cardline{border-top: 1px solid rgb(4 4 4 / 84%);}
        /* .card .meetingslist-card:hover {
        transition: 0.4s ease-in-out;
        box-shadow: rgba(50, 50, 93, 0.25) 0px 30px 60px -12px inset,
        rgba(0, 0, 0, 0.3) 0px 18px 36px -18px inset !important;
        } */
        .live-badge{display:inline-flex;align-items:center;background-color:red;color:#fff;font-weight:700;padding:4px 10px;border-radius:20px;font-size:14px;font-family:Arial,sans-serif;animation:1s infinite pulse;box-shadow:0 0 5px red}.live-dot{width:8px;height:8px;background-color:#fff;border-radius:50%;margin-right:6px;animation:1s infinite blink}@keyframes blink{0%,100%,50%{opacity:1}25%,75%{opacity:0}}@keyframes pulse{0%,100%{box-shadow:0 0 5px red}50%{box-shadow:0 0 20px red}}
        p.mb-1 {
        font-size: large;
        }  
        .custom-btn {
        /* width: 130px;
        height: 40px; */
        color: #fff;
        border-radius: 5px;
        /* padding: 10px 25px; */
        font-family: Lato, sans-serif;
        font-weight: 500;
        background: 0 0;
        cursor: pointer;
        transition: .3s;
        position: relative;
        display: inline-block;
        box-shadow: rgba(9, 30, 66, 0.25) 0px 4px 8px -2px, rgba(9, 30, 66, 0.08) 0px 0px 0px 1px;
        outline: 0;
        }
        .btn-11 {
        border: none;
        background: #212ffb;
        background: linear-gradient(0deg, #3e21fb 0, #4c5cea 100%);
        color: #fff;
        overflow: hidden;
        width: fit-content;
        }
        .btn-11:hover {
        text-decoration: none;
        color: #fff;
        opacity: .7;
        }
        .btn-11:before {
        position: absolute;
        content: '';
        display: inline-block;
        top: -180px;
        left: 0;
        width: 30px;
        height: 100%;
        background-color: #fff;
        animation: 3s ease-in-out infinite shiny-btn1;
        }
        .btn-11:active {
        box-shadow: 4px 4px 6px 0 rgba(255,255,255,.3), -4px -4px 6px 0 rgba(116,125,136,.2), inset -4px -4px 6px 0 rgba(255,255,255,.2), inset 4px 4px 6px 0 rgba(0,0,0,.2);
        }
        @keyframes shiny-btn1 {
        0% { transform: scale(0) rotate(45deg); opacity: 0; }
        80% { transform: scale(0) rotate(45deg); opacity: .5; }
        81% { transform: scale(4) rotate(45deg); opacity: 1; }
        100% { transform: scale(50) rotate(45deg); opacity: 0; }
        }
        .btn-11.partnearray{
        background: navy!important;
        }
        .btn-11.categoryarray{
        background: #ff007f!important;
        }
        tr,td{
        font-weight: 400;
        }
        .star-rating {
        direction: rtl;
        display: inline-block;
        padding: 10px;
        }
        .rating-question {
        font-size: 18px;
        margin-bottom: 10px;
        display: block;
        }
      </style>
      <!-- /.navbar -->
      <!-- Content Wrapper. Contains page content -->
      <div class="content-wrapper">
        <section class="content">
          <div class="container-fluid">
            <div class="row">
              <div class="col-12">
                <div class="card">
                  <div class="card-header" style="background: linear-gradient(to right, #ede7f6, #ffffff); border-radius: 12px; box-shadow: 0 4px 8px rgba(0,0,0,0.1); padding: 20px;">
                    <h3 class="text-center m-3"> 📅📝 Task Planner Live ⏳✅ </h3>
                    <center><mark>📅📝 Task Planner Live ⏳✅ helps you stay organized and productive every day. Easily plan, track, and manage your tasks with real-time updates. Monitor start ⏰ and end 🏁 times, ensure punctuality ⏱️, and keep your team aligned for maximum efficiency 🚀. Perfect for managing daily schedules 📆, this tool ensures smooth workflow, better collaboration, and consistent progress tracking for individuals and teams alike.</mark></center>
                    <div class="text-center">
                      <mark><?=$sdate?></mark>
                    </div>
                  </div>
                  <hr>



                  <?php 
                  
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

                  ?>


                  <div class="row mb-2">
                    <hr>
                    <div class="text-right-data text-center" style="background: linear-gradient(to right, #b2d6ff, #ffffff); border-radius: 12px; box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1); padding: 20px; width: 60%;">
                      <?php 
                        $clusterPstDatas  = $this->Menu_model->GetAdminActiveTeam($user['user_id']);
                        $taskActions      = $this->Menu_model->get_action();
                        $userRoles        = $this->Menu_model->GetActiveDepartmentList($uid);
                        ?>
                      <div class="col text-center formcenterData">
                        <div>
                          <hr>
                          <form action="<?=base_url()?>Reports/TaskPlannerLive" method="post" class="mt-3">
                            <div class="row mb-4">
                              <div class="col">
                                <label for="selectedDate">Start Date</label>
                                <input type="date" value="<?=$sdate;?>" id="selectedDate" max="<?=date('Y-m-d')?>" name="sdate" style="width: 200px;" class="form-control">
                              </div>
                              <!-- <div class="col">
                                <label for="selectedDate">End Date</label>
                                <input type="date" value="<?=$edate;?>" id="selectedDate" name="edate" style="width: 200px;" max="<?=date('Y-m-d')?>" class="form-control">
                                </div> -->
                              <div class="col">
                                <label for="selectedDate">Select Admin</label>
                                <select id="admin_id_filter" name="admin_id_filter" class="form-control">
                                  <?php if($user['type_id'] == 2){ ?> 
                                  <option value="all">All</option>
                                  <?php } ?>
                                  <?php foreach ($clusterPstDatas as $clusterPstData) { ?>
                                  <option value="<?= $clusterPstData->user_id; ?>" <?= ($clusterPstData->user_id == $admin_id_filter) ? 'selected' : ''; ?>>
                                    <?= $clusterPstData->name; ?>
                                  </option>
                                  <?php } ?>
                                </select>
                              </div>
                              <div class="col">
                                <?php $getTeamsDatas = $this->Menu_model->GetClusterAndPSTActiveTeam($admin_id_filter); ?>
                                <label for="selectedDate">Select RM / Specific User</label>
                                <select id="rm_filter" name="rm_filter" class="form-control">
                                  <?php if($user['type_id'] !== 3){ ?> 
                                  <option value="all" <?= ($rm_filter == 'all') ? 'selected' : ''; ?>>All</option>
                                  <?php } ?>
                                  <?php foreach ($getTeamsDatas as $getTeamsData) { ?>
                                  <option value="<?= $getTeamsData->user_id; ?>" <?= ($getTeamsData->user_id == $rm_filter) ? 'selected' : ''; ?>>
                                    <?= $getTeamsData->name; ?> (Team Wise)
                                  </option>
                                  <?php }?>
                                  <?php $getcTeamsDatas = $this->Menu_model->GetAllUserDataByAdminID($user['user_id']); ?>
                                  <?php foreach ($getcTeamsDatas as $getcTeamsData) { ?>
                                  <option value="<?= $getcTeamsData->user_id; ?>" <?= ($getTeamsData->user_id == $rm_filter) ? 'selected' : ''; ?> >
                                    <?= $getcTeamsData->name; ?> (user Wise)
                                  </option>
                                  <?php }?>
                                </select>
                                <input type="hidden" id="rm_input" name="rm_input" class="form-control" placeholder="Enter value" />
                              </div>
                              <div class="col text-center">
                                <div class="form-group">
                                  <button type="submit" class="custom-btn btn-11 partnearray p-2" style="margin-top: 33px; width: 100px;">Filter</button>
                                </div>
                              </div>
                            </div>
                          </form>
                          <hr>
                        </div>
                      </div>
                    </div>
                    <hr>
                  </div>
                  <div class="row mt-3 mb-2">
                    <div class="col-md-12 text-center">
                      <button class="custom-btn btn-11 partnearray p-2" id="downloadPdf">Download PDF</button>
                    </div>
                  </div>
                  <?php 
                    $plannerReportDatas     = $liveDayManagement['plannerReportDatas'];
                    $totalTasksUserByDatas  = $liveDayManagement['totalTasksUserByDatas'];
                    
                    ?>
                  <?php
                    // Define unique gradients
                    $gradients = [
                        'linear-gradient(to right, #ffecd2, #fcb69f)',
                        'linear-gradient(to right, #c2e9fb, #a1c4fd)',
                        'linear-gradient(to right, #fbc2eb, #a6c1ee)',
                        'linear-gradient(to right, #fdfbfb, #ebedee)',
                    ];
                    $gradientIndex = 0;
                    ?>
                  <hr>
                  <div class="text-center" style="background: linear-gradient(to right, #ede7f6, #ffffff); border-radius: 12px; box-shadow: 0 4px 8px rgba(0,0,0,0.1); padding: 20px;">
                    <h5><i>📊 User Activity and Planner Set Report</i></h5>
                    <small>
                    📅 This Data represents a comprehensive report on user activity and attendance metrics. 📅 It includes details such as the report date, total number of users, present and absent users, and their work locations categorized into office 🏢, field 🌾, and field office 🏡. Additionally, it tracks planning activities, distinguishing between same-day and previous-day planning, as well as instances where planning was not set. 📝 The report also captures scenarios where planning started on a previous day but tasks were created on the same day, providing insights into user productivity and planning efficiency. 📈
                    </small>
                  </div>
                  <br>
                  <?php
                    $i = 1;
                    foreach ($plannerReportDatas as $row) {
                        $r = rand(150, 255);
                        $g = rand(150, 255);
                        $b = rand(150, 255);
                        $backgroundColor = "rgba($r, $g, $b, 0.2)";
                        $hue = rand(0, 360);
                        $saturation = rand(60, 100);
                        $lightness = rand(30, 45);
                        $backgroundColorNew = "hsl($hue, $saturation%, $lightness%)";
                    ?>



                  <div class="row">
                    <div class="col-md-3">
                      <div class="p-2 text-center mb-1" style="background-color: rgba(183, 184, 255, 0.7);">
                        <h5 class="p-2">👥 Total Active User</h5>
                        <p><a style="color: inherit;" href="<?= base_url() ?>/Reports/PlannerReportDataBYDate/<?= $uid ?>/<?= $row->report_date ?>/<?= $row->report_date ?>/total_user" target="_blank"><?= htmlspecialchars($row->total_user); ?></a></p>
                      </div>
                    </div>
                    <div class="col-md-3">
                      <div class="p-2 text-center mb-1" style="background-color: rgba(255, 255, 183, 0.7);">
                        <h5 class="p-2">👤 Total Present User </h5>
                        <p><a style="color: inherit;" href="<?= base_url() ?>/Reports/PlannerReportDataBYDate/<?= $uid ?>/<?= $row->report_date ?>/<?= $row->report_date ?>/total_present_user" target="_blank"><?= htmlspecialchars($row->total_present_user); ?></a></p>
                      </div>
                    </div>

                    <div class="col-md-3">
                      <div class="p-2 text-center mb-1" style="background-color: rgba(183, 255, 220, 0.7);">
                        <h5 class="p-2">📝 Total Planner Set</h5>
                        <p><a style="color: inherit;" href="<?= base_url() ?>/Reports/PlannerReportDataBYDate/<?= $uid ?>/<?= $plannerDate ?>/<?= $plannerDate ?>/total_planning_set" target="_blank"><?= htmlspecialchars($row->total_planning_set); ?></a></p>
                      </div>
                    </div>
                    
                    
                    <div class="col-md-3">
                      <div class="p-2 text-center mb-1" style="background-color: rgba(255, 183, 220, 0.7);">
                        <h5 class="p-2">❌ Planner Not Set</h5>
                        <p><a style="color: inherit;" href="<?= base_url() ?>/Reports/PlannerReportDataBYDate/<?= $uid ?>/<?= $plannerDate ?>/<?= $plannerDate ?>/planner_not_set" target="_blank"><?= htmlspecialchars($row->planner_not_set); ?></a></p>
                      </div>
                    </div>
                   
                  </div>

                      <hr>

                  
                  <?php $i++; } ?>
                  <?php 
                    // dd($totalTasksUserByDatas);
                    
                    
                    $early = 0;
                    $on_time = 0;
                    $late = 0;
                    
                    foreach ($totalTasksUserByDatas as $usertime) {
                        $start_time = date('H:i', strtotime($usertime->user_start));
                    
                        if ($start_time < '10:00') {
                            $early++;
                        } elseif ($start_time >= '10:00' && $start_time <= '10:15') {
                            $on_time++;
                        } else {
                            $late++;
                        }
                    }
                    
                    ?>
          
                  <hr>
                  <div class="text-center" style="background: linear-gradient(to right, #ede7f6, #ffffff); border-radius: 12px; box-shadow: 0 4px 8px rgba(0,0,0,0.1); padding: 20px;">
                    <h5>📅 Planner Management Overview</h5>
                    <small>
                   Planner Management Overview provides a complete snapshot of all your planned activities 📅, deadlines ⏰, and task priorities 📝 in one place. It enables managers and teams to monitor progress ✅, allocate resources effectively 📊, and ensure timely completion 🏁. With clear insights 🔍 and real-time updates ⏳, it simplifies daily planning, boosts productivity 🚀, and ensures smooth coordination for achieving goals efficiently and without stress.
                    </small>
                  </div>
                  <br>
                  <?php
                    $gradientColors = [
                        'linear-gradient(to right,rgb(255, 223, 246),rgb(255, 255, 255))',
                        'linear-gradient(to right,rgb(233, 255, 182),rgb(249, 255, 250))',
                        'linear-gradient(to right,rgb(203, 255, 222),rgb(248, 253, 255))',
                        'linear-gradient(to right,rgb(255, 229, 198),rgb(255, 255, 255))',
                        'linear-gradient(to right,rgb(238, 255, 191),rgb(249, 252, 255))',
                        'linear-gradient(to right,rgb(203, 255, 191),rgb(249, 252, 255))',
                        'linear-gradient(to right,rgb(255, 203, 188),rgb(249, 252, 255))',
                        'linear-gradient(to right,rgb(255, 191, 191),rgb(249, 252, 255))',
                        'linear-gradient(to right,rgb(255, 251, 191),rgb(249, 252, 255))',
                        'linear-gradient(to right,rgb(191, 236, 255),rgb(249, 252, 255))',
                        'linear-gradient(to right,rgb(251, 193, 221),rgb(249, 252, 255))',
                        'linear-gradient(to right,rgb(221, 214, 240),rgb(249, 252, 255))',
                    ];
                    
                    $index = 0;
                    //  dd($userWholeDayActivity);
                    $userWholeDays = $userWholeDayActivity['userWholeDays'];
                    // dd($userWholeDays);
                    ?>
                  <div class="row">
                    <?php 
                      $j = 1; 

                       
                      foreach($userWholeDays as $dayActivity):
                          $user_type_id = $dayActivity->user_type_id;
                         if($user_type_id == 15){continue;}

                        $targetuid = $dayActivity->user_id;

                        $slatitude = $dayActivity->slatitude;
                        $slongitude = $dayActivity->slongitude;
                        $startMap = "$slatitude,$slongitude";

                        $clatitude  = $dayActivity->clatitude;
                        $clongitude = $dayActivity->clongitude;
                        $closedMap  = "$clatitude,$clongitude";


                        $taskDatadatas   =  $this->Menu_model->GetTodaysLivePlanedTaskType($targetuid,$plannerDate);

                          
        
                        $groupedData = [];
                        

                        foreach ($taskDatadatas as $item) {
                            $planner_user_id = $item->planner_user_id;
                            if (!isset($groupedData[$planner_user_id])) {
                                $groupedData[$planner_user_id] = [];
                            }
                            $groupedData[$planner_user_id][] = $item;

                             $task_ptime = $item->tptime;
                             $totalPlaningConsumeInSeconds += timeToSeconds($task_ptime);
                        }

                        $totalttask =$this->Menu_model->getTotalUserTaskDetailsOnPlanner($targetuid, $plannerDate);  
                        $totalPlaningConsumeInSeconds = 0;  
                        foreach ($totalttask as $itemDatas) {
                             $task_ptime                    = $itemDatas->tptime;
                             $totalPlaningConsumeInSeconds += timeToSeconds($task_ptime);
                        }

                       
                    $data                               = $groupedData;
                    $data1                              = $groupedData;
                    $totalConsumeTimeonPlanning         = secondsToTime($totalPlaningConsumeInSeconds);
                    $totalConsumeTimeonPlanning_message = "The total time is $totalConsumeTimeonPlanning";

                    $totalttaskdataCnt      = sizeof($totalttask);
                    $averageSecondsPerTask  = $totalPlaningConsumeInSeconds / $totalttaskdataCnt;
                    $averageTimePerTask = secondsToTime($averageSecondsPerTask);

                    $averageTimePerTask_message = "The average time per task is $averageTimePerTask";
                        ?>

                         
                         
                         
                         <?php  


            $autotasktimeDatas = $this->Menu_model->ChekPlannerStartSetOrNot($targetuid,$sdate);
            if(sizeof($autotasktimeDatas) > 0){

              $autoTask_Start_time            = $autotasktimeDatas[0]->stime;                    
              $autoTask_End_time              = $autotasktimeDatas[0]->etime;                    
              $tommrow_planner_start_time     = $autotasktimeDatas[0]->start_tttpft;                    
              $tommrow_planning_close_time    = $autotasktimeDatas[0]->end_tttpft;  

            }


                        $alertClass = '';
                          if (!empty($dayActivity->start_planning_on_date)){
                                if($dayActivity->user_create_planner_approved_request == 'No'){
                                   $alertClass  = "animated-green-border";
                                }else{
                                  $alertClass = "";
                                }
                            }else{
                              $current_time = date("H:i:s"); // Current time in 24-hour format
                              if (strtotime($current_time) > strtotime($tommrow_planner_start_time)) {
                                $alertClass  = "blinking-shadow-effect red-border blinking-background-effect";
                              }
                              ?>
                              <style>
                                .blinking-shadow-effect .mb-1, .blinking-shadow-effect .my-1, .blinking-shadow-effect a {color: red;}
                              </style>
                              <?php 
                            }
                            ?>


                    <div class="col-md-4 mb-4">
                      <div class="card meetingslist-card <?=$alertClass;?>" style="background: <?= $gradientColors[$index++ % count($gradientColors)] ?>; border-radius: 14px; box-shadow: 0 4px 12px rgba(0,0,0,0.1); padding: 20px;">
                        <div class="text-left">
                          <center>
                            <h3 class="mb-1 badge badge-pill badge-dark" style="font-weight: 600; font-size: 1.1rem;"><?= htmlspecialchars($j) ?></h3>
                            <hr style="width: 20%;" class="cardline <?=$hrAClass?>">
                            <h3 class="mb-1" style="font-weight: 600;">👤 <?=$dayActivity->username?></h3>
                            <hr style="width: 60%;" class="cardline <?=$hrAClass?>">
                            <h5 class="d-block mb-2"> 📌 <?=$dayActivity->user_roles?> <hr> <b>Planner Date: <?=$plannerDate?></b> </h5>

                             <?php  
                            if (!empty($dayActivity->start_planning_on_date)){
                                if($dayActivity->user_create_planner_approved_request == 'No'){
                                    ?>
                                    <div class="live-badge mt-1">
                                        <span class="live-dot"></span> LIVE
                                    </div>
                                    <?php 
                                }
                            }
                            ?>
                          
                          <hr class="cardline <?=$hrAClass?>">
                          <?php  if($user['user_id'] !== $targetuid){
                            ?> 
                          <a href="javascript:void(0)" class="btn btn-sm btn-outline-dark position-absolute openCommentModal" 
                            data-task-id="<?= $targetuid ?>" style="top: 10px; right: 10px;">
                          ➕ Add Comment
                          </a>
                          <?php } ?>





<?php

                          // Initialize task count array for each user
    $taskCounts = array();
    foreach ($data as $userId => $userTasks) {
        foreach ($userTasks as $task) {
            $taskCounts[$userId][$task->tasktype] = 
                (isset($taskCounts[$userId][$task->tasktype])) 
                ? $taskCounts[$userId][$task->tasktype] + $task->task_count 
                : $task->task_count;
        }
    }

    $i = 1;
    foreach ($groupedData as $userIdPlanner => $userTasksPlanner) {
        $userPallnerData = $userTasksPlanner[0];

        // Find total_review_plan_time
        $total_review_plan_time = '';
        foreach ($userTasksPlanner as $userTasksPlanners) {
            if (!empty($userTasksPlanners->total_review_plan_time)) {
                $total_review_plan_time = $userTasksPlanners->total_review_plan_time;
            }
        }

        // Color for plan task time
        $total_plan_task_time_data = !empty($userPallnerData->total_plan_task_time) 
            ? $userPallnerData->total_plan_task_time 
            : 'N/A';

        $hours_colors_text = '';
        if($total_plan_task_time_data !== 'N/A'){
            preg_match('/(\d+)\s+hours/', $userPallnerData->total_plan_task_time, $matches);
            $hours_colors = isset($matches[1]) ? (int)$matches[1] : 0;
            $hours_colors_text = $hours_colors >= 6 ? 'text-success' : 'text-danger';
        }
    ?>
       
           <h5 class="d-block mb-2 text-success">📝 Total Task: <span class="badge badge-pill badge-success" style="font-size: 1.1rem;"><?=$userPallnerData->total_task_count?></span></h5>
           <hr style="width:60%;" class="cardline <?=$hrAClass?>">
           <h5 class="d-block mb-2">🕓 Total Task Planned Time: <?=$total_plan_task_time_data?></h5>
           <hr style="width:60%;" class="cardline <?=$hrAClass?>">
            <?php if($total_review_plan_time !== ''){ ?>
           <h5 class="d-block mb-2 text-success">🕓 Total Review Plan Time: <?=$total_review_plan_time?></h5>
           <hr style="width:60%;" class="cardline <?=$hrAClass?>">
            <?php } ?>
           
               <?php } ?>


               <?php 
                if (!empty($dayActivity->start_planning_on_date)){
                $planSessionData  = $this->Menu_model->TodaysPlannerSession($targetuid,$plannerDate);
                $planSessionmin   = $this->Menu_model->TodaysTotalsPlannerSessioninMinuteByUidAndDate($targetuid,$plannerDate);
               ?>
               
                <div class="row">
                    <div class="col-md-6">
                        <div class="card-header text-center" style="background: linear-gradient(to right, #e8f5e9, #ffffff); border-radius: 5px; box-shadow: 0 4px 8px rgba(0,0,0,0.1);height: 90px;">
                            <h6>Planner Session</h6>
                            <span>
                                <?php 
                                if ($totalConsumeTimeonPlanning === '0 hours, 0 minutes, and 0 seconds') {
                                    echo 0;
                                } else {
                                    echo (count($planSessionData) === 0) ? 1 : count($planSessionData);
                                }
                                
                                ?>
                            </span>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="card-header text-center" style="background: linear-gradient(to right, #e8f5e9, #ffffff); border-radius: 5px; box-shadow: 0 4px 8px rgba(0,0,0,0.1);height: 90px;">
                            <h6>Time Consume on Planning</h6>
                            <span>
                                <?php 
                                if ($totalConsumeTimeonPlanning === '0 hours, 0 minutes, and 0 seconds') {
                                    $planSessionSeconds = timeToSeconds($planSessionmin);
                                    $totalConsumeTimeonPlanning = secondsToTime($planSessionSeconds);
                                    echo $totalConsumeTimeonPlanning;
                                    if (!empty($taskDatadatas)) {
                                        $averageSecondsPerTask = $planSessionSeconds / count($taskDatadatas);
                                        $averageTimePerTask = secondsToTime($averageSecondsPerTask);
                                        // If you need to display it, echo here
                                    }
                                } else {
                                    echo $totalConsumeTimeonPlanning;
                                }
                                ?>
                            </span>
                        </div>
                    </div>
                     <div class="col-md-12 mt-1">
                        <div class="card-header text-center" style="background: linear-gradient(to right, #e8f5e9, #ffffff); border-radius: 5px; box-shadow: 0 4px 8px rgba(0,0,0,0.1);">
                            <h6>The Average Time Per Task is</h6>
                            <span><?= $averageTimePerTask; ?></span>
                        </div>
                    </div>
                </div>
             <hr class="cardline">                    
                <?php } ?>


                 <a class="btn btn-info" href="<?= base_url() ?>Menu/CheckTaskDetailsByUser/<?=$targetuid?>/<?=$plannerDate?>" target="_BLANK">🎯 View Planner Details</a>
                 <hr>
</center>

           <center>✨➡️ <b style="color:#af04e4">Next Days Planning Status ( <?=$plannerDate?> )</b> ⬅️✨  </center>

           <?php if(sizeof($autotasktimeDatas) > 0){ ?>

              <?php /*
               <hr class="cardline">
              <p class="mb-1"><strong>🕒 Todays Auto Task Start Time:</strong> <?= date("h:i A", strtotime($autoTask_Start_time)); ?></p>
              <hr class="cardline">

              <p class="mb-1"><strong>🕔 Todays Auto Task End Time:</strong> <?= date("h:i A", strtotime($autoTask_End_time)); ?></p>
              <hr class="cardline">
              */ ?>

              <hr class="cardline">
              <p class="mb-1 text-success"><strong>📅 Tomorrow Planner Start Time:</strong> <?= date("h:i A", strtotime($tommrow_planner_start_time)); ?></p>
              <hr class="cardline">

              <p class="mb-1 xt-success"><strong>📅 Tomorrow Planning Close Time:</strong> <?= date("h:i A", strtotime($tommrow_planning_close_time)); ?></p>
              <hr class="cardline">

              
              <?php }else{ ?>  <hr class="cardline">
                  <p class="mb-1" style="color: red;"><strong>📅 Today's AutoTask Time: ❌ Not Set</strong></p>
                  <hr class="cardline">
                  <p class="mb-1" style="color: red;"><strong>📅 Tomorrow's Planner Start Time: ❌ Not Set</strong></p>
                  <hr class="cardline">
                <?php } ?>
       
        
            <!-- Start Work Form As Planning -->
                <?php if (!empty($dayActivity->start_planning_on_date)){ ?>
                    <p class="mb-1"><strong>📅 Start Planning Of Date:</strong> <?= $dayActivity->start_planning_on_date ?></p>
                    <hr class="cardline">
             

           <!-- Start Work Form As Planning -->
            <?php if (!empty($dayActivity->start_planning_on_day)): ?>
                <p class="mb-1"><strong>📅 Started Planning On Date:</strong> <?= $dayActivity->start_planning_on_day ?></p>
                <hr class="cardline">
            <?php endif; ?>

            <!-- Start Work Location As Planning -->
            <?php if (!empty($dayActivity->planning_userworkfrom)): ?>
                <p class="mb-1"><strong>🌍 Planning Work From:</strong> <?= $dayActivity->planning_userworkfrom ?></p>
                <hr class="cardline">
            <?php endif; ?>


          




            <center>✨➡️ <b style="color:#af04e4">Pending Meetings Delete Request</b> ⬅️✨</center>  <hr class="cardline">

             <!-- Pending Meetings Delete Request -->

                <p class="mb-1"><strong>📌 Pending Meetings Delete Request:</strong> <?= !empty($dayActivity->pending_meetings_request == 'Yes') ? $dayActivity->pending_meetings_request : '✅ No Request' ?>
                </p>
                <hr class="cardline">

            <!-- Pending Meetings Delete Request -->
            <?php 
            if (!empty($dayActivity->pending_meetings_request)){
            if (!empty($dayActivity->task_planner_date)): ?>
                <p class="mb-1"><strong>📌 Pending Meetings Delete Request Planner Date:</strong> <?= $dayActivity->task_planner_date ?></p>
                <hr class="cardline">
            <?php endif; ?>

            <!-- Pending Meetings Request Task IDs -->
            <?php if (!empty($dayActivity->pending_meetings_request_task_ids)): ?>
                <p class="mb-1"><strong>🆔 Pending Meetings Request Task IDs:</strong>  <br>
                    <?php 
                    $getTaskReqLists = $this->Menu_model->GetTblCallEventsTasksList($dayActivity->pending_meetings_request_task_ids);
                    $l = 0;
                    $m = 1;
                    $requestRemarksarrays = explode(",", $getPMTDRequest->remarks);
                    ?>

                    <ul style="list-style-type: none; padding-left: 0; margin: 0;">
                    <?php foreach ($getTaskReqLists as $value) { ?>
                        <li style="background: azure; padding: 10px; margin-bottom: 8px; border-radius: 6px; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
                            <p>🔢 <b>#<?= $m; ?> - CID:</b> <?= $value->cid ?></p>
                            <p>🏢 <b>Company Name:</b> <?= $value->company_name ?></p>
                            <p>📅 <b>Appointment Datetime:</b> <?= $value->appointmentdatetime ?></p>
                            <p>📝 <b>Request Remarks:</b> <?= $requestRemarksarrays[$l]; ?></p>
                            <p>
                                🗑️ <b>Delete Status:</b> 
                                <?php if ($value->delete_request > 0) { ?>
                                    <span class="bg-success p-1 text-white rounded">✅ Deleted</span>
                                <?php } else { ?>
                                    <span class="bg-warning p-1 text-dark rounded">⏳ Pending</span>
                                <?php } ?>
                            </p>
                            <p>
                                💬 <b>Delete Remarks:</b> 
                                <?php if ($value->delete_request > 0) { 
                                    echo $value->delete_remarks; 
                                } else { ?>
                                    <span class="bg-warning p-1 text-dark rounded">⏳ Pending</span>
                                <?php } ?>
                            </p>
                        </li>
                    <?php $m++; $l++; } ?>
                    </ul>

    
            
            </p>
                <hr class="cardline">
            <?php endif; ?>

            <!-- Pending Meetings Request Task Count -->
            <?php if (!empty($dayActivity->pending_meetings_request_task_count)): ?>
                <p class="mb-1"><strong>🔢 Pending Meetings Request Task Count:</strong> <?= $dayActivity->pending_meetings_request_task_count ?></p>
                <hr class="cardline">
            <?php endif; ?>

            <!-- Pending Meetings Request User Request Remarks -->
            <?php if (!empty($dayActivity->pending_meetings_request_user_request_remarks)): ?>
                <p class="mb-1"><strong>🗣️ Pending Meetings Request User Request Remarks:</strong> <?= $dayActivity->pending_meetings_request_user_request_remarks ?></p>
                <hr class="cardline">
            <?php endif; ?>

            <!-- Pending Meetings Request Approved Status -->
            <?php if (!empty($dayActivity->pending_meetings_request_approved_status)): ?>
                <p class="mb-1"><strong>✅ Pending Meetings Request Approved Status:</strong> <?= $dayActivity->pending_meetings_request_approved_status ?></p>
                <hr class="cardline">
            <?php endif; ?>

            <!-- Pending Meetings Request Approved By -->
            <?php if (!empty($dayActivity->pending_meetings_request_approved_by)): ?>
                <p class="mb-1"><strong>👤 Pending Meetings Request Approved By:</strong> <?= $dayActivity->pending_meetings_request_approved_by ?></p>
                <hr class="cardline">
            <?php endif; ?>

            <!-- Pending Meetings Request Approve Date -->
            <?php if (!empty($dayActivity->pending_meetings_request_approve_date)): ?>
                <p class="mb-1"><strong>📅 Pending Meetings Request Approve Date:</strong> <?= $dayActivity->pending_meetings_request_approve_date ?></p>
                <hr class="cardline">
            <?php endif; ?>

            <!-- Pending Meetings Request Admin Remarks -->
            <?php if (!empty($dayActivity->pending_meetings_request_admin_remarks)): ?>
                <p class="mb-1"><strong>🗨️ Pending Meetings Request Admin Remarks:</strong> <?= $dayActivity->pending_meetings_request_admin_remarks ?></p>
                <hr class="cardline">
            <?php endif; ?>

            <!-- Pending Meetings Request Create Time -->
            <?php if (!empty($dayActivity->pending_meetings_request_create_time)): ?>
                <p class="mb-1"><strong>🕓 Pending Meetings Request Create Time:</strong> <?= $dayActivity->pending_meetings_request_create_time ?></p>
                <hr class="cardline">
            <?php endif;
            }
            ?>




            <center>✨➡️ <b style="color:#af04e4">Pending Task Planning Request</b> ⬅️✨</center>  <hr class="cardline">
             
        
            <!-- Request Pending Task Planning -->
            <?php if (!empty($dayActivity->request_pending_task_planning)): ?>
                <p class="mb-1"><strong>📋 Request Pending Task Planning:</strong> <?= !empty($dayActivity->request_pending_task_planning == 'Yes') ? $dayActivity->request_pending_task_planning : '✅ No Request' ?>
</p>
                <hr class="cardline">
            <?php endif; ?>

            <!-- Request Pending Task Planning Request Type -->
            <?php if (!empty($dayActivity->request_pending_task_planning_request_type)): ?>
                <p class="mb-1"><strong>📑 Request Pending Task Planning Request Type:</strong> <?= $dayActivity->request_pending_task_planning_request_type ?></p>
                <hr class="cardline">
            <?php endif; ?>

            <!-- Request Pending Task Planning Task Count -->
            <?php if (!empty($dayActivity->request_pending_task_planning_task_count)): ?>
                <p class="mb-1"><strong>🔢 Request Pending Task Planning Task Count:</strong> <?= $dayActivity->request_pending_task_planning_task_count ?></p>
                <hr class="cardline">
            <?php endif; ?>

            <!-- Request Pending Task Planning User Request Remarks -->
            <?php if (!empty($dayActivity->request_pending_task_planning_user_request_remarks)): ?>
                <p class="mb-1"><strong>🗣️ Request Pending Task Planning User Request Remarks:</strong> <?= $dayActivity->request_pending_task_planning_user_request_remarks ?></p>
                <hr class="cardline">
            <?php endif; ?>

            <!-- Request Pending Task Planning Approved Status -->
            <?php if (isset($dayActivity->request_pending_task_planning_approved_status)): ?>
                <p class="mb-1"><strong>✅ Request Pending Task Planning Approved Status:</strong>
                    <?= $dayActivity->request_pending_task_planning_approved_status == 1 ? "<span class='bg-success p-1'>Approved</span>" : "<span class='bg-warning p-1'>Pending</span>" ?>
                </p>
                <hr class="cardline">
            <?php endif; ?>

            <!-- Request Pending Task Planning Approved By -->
            <?php if (!empty($dayActivity->request_pending_task_planning_approved_by)): ?>
                <p class="mb-1"><strong>👤 Request Pending Task Planning Approved By:</strong> <?= $dayActivity->request_pending_task_planning_approved_by ?></p>
                <hr class="cardline">
            <?php endif; ?>

            <!-- Request Pending Task Planning Approve Date -->
            <?php if (!empty($dayActivity->request_pending_task_planning_approve_date)): ?>
                <p class="mb-1"><strong>📅 Request Pending Task Planning Approve Date:</strong> <?= $dayActivity->request_pending_task_planning_approve_date ?></p>
                <hr class="cardline">
            <?php endif; ?>

            <!-- Request Pending Task Planning Admin Remarks -->
            <?php if (!empty($dayActivity->request_pending_task_planning_admin_remarks)): ?>
                <p class="mb-1"><strong>🗨️ Request Pending Task Planning Admin Remarks:</strong> <?= $dayActivity->request_pending_task_planning_admin_remarks ?></p>
                <hr class="cardline">
            <?php endif; ?>

            <!-- Request Pending Task Planning Create Time -->
            <?php if (!empty($dayActivity->request_pending_task_planning_create_time)): ?>
                <p class="mb-1"><strong>🕓 Request Pending Task Planning Time:</strong> <?= $dayActivity->request_pending_task_planning_create_time ?></p>
                <hr class="cardline">
            <?php endif; ?>


              <?php } else { ?> 
            <p class="mb-1">
                <span style="color: red;"><strong>🚫 No Planning Started on <?= $plannerDate ?>:</strong> ✅ Not Started</span>
            </p>
            <hr class="cardline">
        <?php } ?>

         <center>✨➡️ <b style="color:#af04e4"> Planner Approval Request - (<?=$sdate?>) </b> ⬅️✨</center>  <hr class="cardline">

                 <?php if($dayActivity->user_create_planner_approved_request == 'Yes'){ ?>
          
                <p class="mb-1"><strong>📑 User Request To Planner Approved :</strong> <?= $dayActivity->user_create_planner_approved_request ?></p>
                <hr class="cardline">
                <p class="mb-1"><strong>🧾 Planner Request Message :</strong> <?= $dayActivity->planner_request_message ?></p>
                <hr class="cardline">
                <p class="mb-1"><strong>✅ Planner Approved Status :</strong> 
                <?php if($dayActivity->planner_approved_status == 1){
                    echo "<span class='bg-success p-1'>Approved</span>";
                }else if($dayActivity->planner_approved_status == 0){
                    echo "<span class='bg-warning p-1'>Pending</span>";
                } ?></p>
                <hr class="cardline">
                <p class="mb-1"><strong>👤 Planner Approved By :</strong> <?= !empty($dayActivity->planner_approved_by) ? $dayActivity->planner_approved_by : 'NA' ?></p>
                <hr class="cardline">
                <p class="mb-1"><strong>🕓 Planner Request Time :</strong> <?= $dayActivity->planner_request_time ?></p>
                <hr class="cardline">
                <p class="mb-1"><strong>📌 Planner Approved Date :</strong><?= !empty($dayActivity->planner_approved_date) ? $dayActivity->planner_approved_date : 'NA' ?></p>
                <hr class="cardline">

                <p class="mb-1"><strong>⏳ Time Taken in Planner Approval :</strong><?= !empty($dayActivity->time_taken_in_planner_approved) ? $dayActivity->time_taken_in_planner_approved : 'NA' ?></p>
                <hr class="cardline">
                 <?php }else{ ?> 
                     <p class="mb-1">
                        <span style="color: red;"><strong>🚫 No Planning Request on <?= $plannerDate ?>:</strong> ❌ No Request</span>  <hr class="cardline">
                    </p>
                    <?php } ?>


                    <?php 

                  
                    $totalttaskCount = sizeof($totalttask);
                    if($totalttaskCount > 0){
              $task_counts = [];
              $filterused = [];
              $selectbyFilter = [];
        foreach ($totalttask as $row) {
            $task_name = $row->task_name;

              $taid = $row->actiontype_id;
              $task_name = $this->Menu_model->get_actionbyid($taid)[0]->name;
        
            if (!isset($task_counts[$task_name])) {
                $task_counts[$task_name] = 1;
            } else {
                $task_counts[$task_name]++;
            }

                $filter_by = json_decode($row->filter_by, true);
                $selectby = $row->selectby;
                
                if($selectby !==''){
                         if (!isset($selectbyFilter[$selectby])) {
                                $selectbyFilter[$selectby] = 1;
                            } else {
                                $selectbyFilter[$selectby]++;
                            }
                              //  echo "other = ".$selectby.' - Task ID : '.$row->task_id.'<br/>';
                    }else if ($row->reviewtype !='' && !is_int($row->reviewtype)){
                          if (!isset($selectbyFilter[$row->reviewtype])) {
                                $selectbyFilter[$row->reviewtype] = 1;
                            } else {
                                $selectbyFilter[$row->reviewtype]++;
                          }
                            //  echo "other = ".$reviewtype.' - Task ID : '.$row->task_id.'<br/>';
                    }else if ($row->comments !='' && !is_null($row->comments)){
                          if (!isset($selectbyFilter[$row->comments])) {
                                $selectbyFilter[$row->comments] = 1;
                            } else {
                                $selectbyFilter[$row->comments]++;
                          }
                            //  echo "comments = ".$selectby.' - Task ID : '.$row->task_id.'<br/>';
                    }else{
                      if (!isset($selectbyFilter['other'])) {
                                $selectbyFilter['other'] = 1;
                            } else {
                                $selectbyFilter['other']++;
                          }
                    }
        
                if(sizeof($filter_by) > 0){
                    foreach ($filter_by as $key => $value) :
                        if($key == 'Plan_BY'){
                          
                          if (!isset($filterused[$value])) {
                              $filterused[$value] = 1;
                          } else {
                              $filterused[$value]++;
                          }

                        }elseif($key == 'Filter_By'){
                          if (!isset($filterused[$value])) {
                              $filterused[$value] = 1;
                          } else {
                              $filterused[$value]++;
                          }
                        }else{
                        if($key == 'comp_status'){
                        $tstatus = $this->Menu_model->get_statusbyid($value);
                        $tstatusname = $tstatus[0]->name;

                          if (!isset($filterused[$tstatusname])) {
                              $filterused[$tstatusname] = 1;
                          } else {
                              $filterused[$tstatusname]++;
                          }

                        }elseif($key == 'task'){
                        $taction = $this->Menu_model->get_actionbyid($value);
                        $tactionname = $taction[0]->name;

                        if (!isset($filterused[$tactionname])) {
                              $filterused[$tactionname] = 1;
                          } else {
                              $filterused[$tactionname]++;
                          }
                        }elseif($key == 'taskActionbyuser'){
                          // echo "<span class='p-1'>Action&nbsp;Taken:&nbsp;".$value."</span><br/>";
                          if (!isset($filterused[$value])) {
                              $filterused[$value] = 1;
                          } else {
                              $filterused[$value]++;
                          }
                        }else{
                            if($selectby !==''){
                              
                              if (!isset($filterused[$selectby])) {
                                  $filterused[$selectby] = 1;
                              } else {
                                  $filterused[$selectby]++;
                              }

                            }else if ($row->comments !==''){
                                if (!isset($filterused[$row->comments])) {
                                    $filterused[$row->comments] = 1;
                                } else {
                                    $filterused[$row->comments]++;
                                }
                            }else{;
                                if (!isset($filterused[$reviewtype])) {
                                    $filterused[$reviewtype] = 1;
                                } else {
                                    $filterused[$reviewtype]++;
                                }
                            }
                        }
                        }
                    endforeach;
                }

                      }
                      
                    }else{
                            $task_counts        = [];
                            $filterused         = [];
                            $selectbyFilter     = [];
                    }   
                    ?>


                      <center>✨➡️ <b style="color:#af04e4"> 📝 Planner Task Activity Summary - (<?=$sdate?>) </b> ⬅️✨</center>  <hr class="cardline">

                      <?php
                    $colors = [
                    "#e3f2fd", // blue
                    "#fce4ec", // pink
                    "#e8f5e9", // green
                    "#fff3e0", // orange
                    "#ede7f6", // purple
                    "#f5facbff", // lime
                    "#f3e5f5", // light purple
                    "#c4c2f3ff",  // teal
                    "#cdf3f1ff",  // teal
                    "#f5cae8ff",  // teal
                    "#edf6c1ff"  // teal
                    ];
                    $index = 0; ?>

                  
                    <div class="row">
                         <div class="col mb-1">
                          <div class="rounded p-3" style="background-color: #fce4ec;box-shadow: rgba(9, 30, 66, 0.25) 0px 4px 8px -2px, rgba(9, 30, 66, 0.08) 0px 0px 0px 1px;height: 140px;">
                            <h6 class="mb-1 font-weight-bold text-center">Total Task</h6>
                            <hr>
                            <div class="text-center">
                              <span class="badge badge-pill badge-dark" style="font-size: 1.1rem;"><?= $totalttaskCount;?></span>
                            </div>
                          </div>
                        </div>
                  <?php foreach ($task_counts as $label => $value): ?>
                        <?php
                          $bgColor = $colors[$index % count($colors)];
                          $desc = isset($descriptions[$label]) ? $descriptions[$label] : '';
                          $index++;
                          ?>
                        <div class="col mb-1">
                          <div class="rounded p-3" style="background-color: <?= $bgColor ?>;box-shadow: rgba(9, 30, 66, 0.25) 0px 4px 8px -2px, rgba(9, 30, 66, 0.08) 0px 0px 0px 1px;height: 140px;">
                            <h6 class="mb-1 font-weight-bold text-center"><?= $label ?></h6>
                            <hr>
                            <div class="text-center">
                              <span class="badge badge-pill badge-dark" style="font-size: 1.1rem;"><?= $value ?></span>
                            </div>
                          </div>
                        </div>
                        <?php endforeach; ?>
                    </div>

             

                    <?php if(sizeof($selectbyFilter)){ ?>
                    <hr class="cardline"><center>✨➡️ <b style="color:#af04e4"> 🧩🔍 Total Filters Used for Plan Tasks </b> ⬅️✨</center>  <hr class="cardline">
                   
                    <div class="row">
                        <?php foreach ($selectbyFilter as $label => $value): ?>
                        <?php
                          $bgColor = $colors[$index % count($colors)];
                          $desc = isset($descriptions[$label]) ? $descriptions[$label] : '';
                          $index++;
                          ?>
                        <div class="col-md-3 mb-3">
                          <div class="rounded p-3" style="height:150px;background-color: <?= $bgColor ?>;box-shadow: rgba(50, 50, 93, 0.25) 0px 2px 5px -1px, rgba(0, 0, 0, 0.3) 0px 1px 3px -1px;">
                            <h6 class="mb-1 font-weight-bold text-center"><?= $label ?></h6>
                            <hr>
                            <div class="text-center">
                              <span class="badge badge-pill badge-dark" style="font-size: 1.1rem;"><?= $value ?></span>
                            </div>
                          </div>
                        </div>
                        <?php endforeach; ?>
                      </div>
                      <?php } ?>



                          <?php $reminderComments =  $this->Menu_model->GetDayReminderCommets($targetuid,$sdate,'Day Planner');?>
                   
                            <hr class="cardline"><center>✨➡️ <b style="color:#af04e4"> 🔔 Reminder Comment </b> ⬅️✨</center> 
                          <hr style="width: 50%;" class="<?=$hrAClass?>">
                          <span id="reminder_remarks_<?=$targetuid?>">
                            <?php  if(sizeof($reminderComments) > 0){ ?>
                            <?php foreach($reminderComments as $reminderComment): ?>
                            - 📝 <?=$reminderComment->reminder_remarks.' — 👤 '.$reminderComment->reminderby_user.' ⏰ Time: '.$reminderComment->created_at; ?> 
                            <hr>
                            <?php endforeach; ?>
                            <?php } ?>
                          </span>
                          <hr class="cardline <?=$hrAClass?>">
                        </div>
                      </div>
                    </div>
                    <?php 
                      $j++; 
                      endforeach;
                       ?>      
                  </div>
                </div>
              </div>
            </div>
            <!-- /.card-body -->
          </div>
          <!-- Button trigger modal -->
          <!-- Modal -->
          <div class="modal fade" id="commentModalModalCenter" tabindex="-1" role="dialog" aria-labelledby="commentModalModalCenterTitle" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" role="document">
              <div class="modal-content" style="background: linear-gradient(to right,rgb(255, 223, 246),rgb(255, 255, 255)); border-radius: 14px; box-shadow: 0 4px 12px rgba(0,0,0,0.1); padding: 20px;">
                <div class="modal-header">
                  <h5 class="modal-title" id="commentModalLabel">📝 Add Comment</h5>
                  <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">&times;</span>
                  </button>
                </div>
                <div class="modal-body">
                  <form id="commentForm">
                    <input type="hidden" name="task_id" id="modal_task_id">
                    <div class="mb-3">
                      <label for="commentText" class="form-label">Comment</label>
                      <textarea class="form-control" id="commentText" name="comment" rows="4" placeholder="Write your comment here..."></textarea>
                    </div>
                    <center><button type="submit" class="btn btn-primary w-50">Submit Comment</button></center>
                  </form>
                </div>
              </div>
            </div>
          </div>
          <!-- /.card -->





              <!-- Modal -->
                <div class="modal fade" id="exampleModalCenterCardData" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered" role="document">
                    <div class="modal-content" style="background: linear-gradient(to right, #fcd3ff, #ffffff); border-radius: 12px; box-shadow: 0 4px 8px rgba(0,0,0,0.1); padding: 20px;">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalCenterTitle"></h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div id="modalContentArea">
                               
                        </div>
                    </div>
                    <!-- <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="button" class="btn btn-primary">Save changes</button>
                    </div> -->
                    </div>
                </div>
                </div>


      </div>
      <!-- /.col -->
    </div>
    <!-- /.row -->
    </div>
    <!-- /.container-fluid -->
    </section>
     <style>
    /* Style for the red border */
    .red-border {
        border: 2px solid red;
        border-radius: 14px;
        padding: 20px;
        /* background: linear-gradient(to right, rgb(255, 223, 246), rgb(255 138 138))!important; */
    }

    /* Keyframes for the blinking box shadow effect */
    @keyframes blinkShadow {
        0%, 100% {
            box-shadow: 0 0 10px rgba(128, 0, 0, 0.6); /* Dark red shadow */
        }
        50% {
            box-shadow: 0 0 20px rgba(128, 0, 0, 0.9); /* Darker and larger red shadow */
        }
    }

    /* Apply the blinking box shadow effect */
    .blinking-shadow-effect {
        animation: blinkShadow 1s infinite;
    }

    /* Blinking effect for the card background */
    @keyframes blinkBackground {
        0% {
          background: linear-gradient(to right, rgb(255, 223, 246), rgb(255 138 138));
        }
        
        100% {
          background: linear-gradient(to right, rgb(255, 223, 246), rgb(255 138 138));
        }
    }
.hr_alert_class{
  border-top: 1px solid rgb(255 0 0);
}
    /* .blinking-background-effect {
        animation: blinkBackground 1.5s infinite;
    } */




/* Animated green border */
/* Animated green border */
.animated-green-border {
    position: relative;
    border-radius: 14px;
    padding: 3px; /* space for the border to show */
    background: linear-gradient(90deg, #a1ffad, #06ff0605, #00ff00, #000000, #c56c08) !important;
    background-size: 300% 300%!important;
    animation: runningBorder 4s linear infinite!important;
}

@keyframes runningBorder {
    0% { background-position: 0% 50%; }
    50% { background-position: 100% 50%; }
    100% { background-position: 0% 50%; }
}
</style>
    </div>    
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
    <script>
      $(document).ready(function () {
        $('#rm_filter').on('change', function () {
          var selectedText = $(this).find('option:selected').text().toLowerCase();
      
          if (selectedText.includes('team wise')) {
            $('#rm_input').val('Team Wise Input');
          } else if (selectedText.includes('user wise')) {
            $('#rm_input').val('User Wise Input');
          } else {
            $('#rm_input').val('');
          }
        });
      
        // Trigger once on page load
        $('#rm_filter').trigger('change');
      });
      
      
      
      
      $(document).ready(function() {
      $('.openCommentModal').click(function() {
        var taskId = $(this).data('task-id');
        $('#modal_task_id').val(taskId);
        $('#commentModalModalCenter').modal('show');
      });
      
        $('#commentForm').submit(function(e) {
          e.preventDefault();
          var taskId = $('#modal_task_id').val();
          var comment = $('#commentText').val();
          $('#commentModalModalCenter').modal('hide');
          $.ajax({
              url:'<?=base_url();?>Menu/AddDayPlannerCommentOnTBLTask',
              method: 'post',
              data: {taskId: taskId,comment: comment,date:'<?=$sdate?>'},
              success: function(result){
                  // $("#reminder_remarks_"+taskId).text(comment);
      
                  $.ajax({
                    url:'<?=base_url();?>Menu/GetDayStartReminderComments',
                    method: 'post',
                    data: {taskId: taskId,date:'<?=$sdate?>',rtype:'Day Planner'},
                    success: function(result){
                        $("#reminder_remarks_"+taskId).text('');
                        $("#reminder_remarks_"+taskId).html('');
                        $("#reminder_remarks_"+taskId).html(result);
                    }
                });
      
              }
          });
      
          $(this).trigger("reset");
        });
      });











      $(document).ready(function () {

    // Image rotation viewer logic
    var rotation = 0;

    $(document).on('click', '.view-data', function () {
        var content = $(this).data('content');
        var title = $(this).data('title');
        var rowID = $(this).data('rid');

        if (title == 'usimg') {
            $("#modalContentArea").html('');
            $("#exampleModalCenterTitle").text("Start Image");
            var imgID = 'userStartImage' + rowID;
            $("#modalContentArea").append(
                '<button id="rotateBtn" data-imgid="' + imgID + '" title="Rotate Image" style="position: absolute; top: 10px; right: 10px; z-index: 10; background: #fff; border: none; border-radius: 50%; padding: 6px;">🔄</button>'
            );
            var imgTag = '<img src="' + content + '" id="' + imgID + '" class="img-fluid" alt="User Start Image" style="transition: transform 0.3s;">';
            $("#modalContentArea").append(imgTag);
            $('.modal-dialog').removeAttr('style');
            $("#exampleModalCenterCardData").modal('show');
        }
        if (title == 'ucimg') {
            $("#modalContentArea").html('');
            $("#exampleModalCenterTitle").text("Closed Image");
            var imgID = 'userStartImage' + rowID;
            $("#modalContentArea").append(
                '<button id="rotateBtn" data-imgid="' + imgID + '" title="Rotate Image" style="position: absolute; top: 10px; right: 10px; z-index: 10; background: #fff; border: none; border-radius: 50%; padding: 6px;">🔄</button>'
            );
            var imgTag = '<img src="' + content + '" id="' + imgID + '" class="img-fluid" alt="User Start Image" style="transition: transform 0.3s;">';
            $("#modalContentArea").append(imgTag);
            $('.modal-dialog').removeAttr('style');
             $("#exampleModalCenterCardData").modal('show');
        }
        if (title == 'startLocation' || title == 'closeLocation') {
            $("#modalContentArea").html('');
           
            if(title == 'startLocation'){
                $("#exampleModalCenterTitle").text("Start Location");
                $('.modal-dialog').attr('style', 'max-height: 100vh !important; max-width: 100% !important;');
            }
           if(title == 'closeLocation'){
                $("#exampleModalCenterTitle").text("Closed Location");
                $('.modal-dialog').attr('style', 'max-height: 100vh !important; max-width: 100% !important;');
            }

            var mapIframe = '<iframe width="100%" height="450" frameborder="0" style="border:0" ' +
                    'src="https://www.google.com/maps?q=' + content + '&hl=es;z=14&output=embed" allowfullscreen>' +
                    '</iframe>';
            $('#modalContentArea').html(mapIframe);
             $("#exampleModalCenterCardData").modal('show');
        }

        rotation = 0;
       
    });

    $(document).on("click", "#rotateBtn", function () {
        rotation = (rotation + 90) % 360;
        var imgID = $(this).data('imgid');
        $("#" + imgID).css("transform", `rotate(${rotation}deg)`);
    });

});

 

    </script>
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
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.4.0/jspdf.umd.min.js"></script>
    <script>
      var totalUserCount = <?= sizeof($userWholeDays) ?>;
      
       for (var i = 1; i <= totalUserCount; i++) {
         $("#example" + i).DataTable({
           "responsive": false,
           "lengthChange": false,
           "autoWidth": false,
           "ordering": false,       // Optional: remove if you want sorting
           "info": false,           // Optional: remove if you want bottom info
           "pageLength": 50,      
           "buttons": ["csv", "excel", "pdf"]
         }).buttons().container().appendTo('#example' + i + '_wrapper .col-md-6:eq(0)');
       }
      
           
           $(document).ready(function () {
             $('#downloadPdf').click(function () {
               const button = $(this);
               button.prop('disabled', true).text('Please wait...');
           
               const { jsPDF } = window.jspdf;
               const doc = new jsPDF('p', 'mm', 'a4');
               const content = document.querySelector('.content-wrapper');
           
               html2canvas(content, {
                 scale: 2,
               }).then(canvas => {
                 const imgData = canvas.toDataURL('image/png');
                 const imgWidth = 210;
                 const pageHeight = 297;
                 const imgHeight = canvas.height * imgWidth / canvas.width;
           
                 let heightLeft = imgHeight;
                 let position = 0;
           
                 doc.addImage(imgData, 'PNG', 0, position, imgWidth, imgHeight);
                 heightLeft -= pageHeight;
           
                 while (heightLeft > 0) {
                   position -= pageHeight;
                   doc.addPage();
                   doc.addImage(imgData, 'PNG', 0, position, imgWidth, imgHeight);
                   heightLeft -= pageHeight;
                 }
           
                 doc.save('dashboard.pdf');
                 // Revert button
                 button.prop('disabled', false).text('Download PDF');
               }).catch(error => {
                 console.error('PDF generation failed:', error);
                 button.prop('disabled', false).text('Download PDF');
               });
             });
           });
           
           
           
         
    </script>
  </body>
</html>
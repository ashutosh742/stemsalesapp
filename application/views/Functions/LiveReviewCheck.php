<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="wi$dth=device-wi$dth, initial-scale=1">
    <title>Live Review | STEM APP | WebAPP</title>
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
            box-shadow: inset 2px 2px 2px 0 rgba(255,255,255,.5), 7px 7px 20px 0 rgba(0,0,0,.1), 4px 4px 5px 0 rgba(0,0,0,.1);
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
                    <h3 class="text-center m-3">🏷️ Track Pending Review</h3>
                    <div class="text-center"><?=$sdate?></div>
                  </div>
                  <hr>



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
                    <form action="<?=base_url()?>Menu/LiveReviewCheck" method="post" class="mt-3">
                      <div class="row mb-4">
                        <!-- <div class="col">
                          <label for="selectedDate">Start Date</label>
                          <input type="date" value="<?=$sdate;?>" id="selectedDate" max="<?=date('Y-m-d')?>" name="sdate" style="width: 200px;" class="form-control">
                          </div> -->
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
                  // dd($liveMettingsTasks);
              

    


// Classification containers
$complete_meeting = [];
$ongoing_meeting = [];
$pending_for_initiate_meeting = [];

// Timing counters
$onTime = 0;
$delayed = 0;

foreach ($liveMettingsTasks as $entry) {

    $start_reviews = $entry->start_review;
 


      if (!is_null($close_review)) {
          $complete_meeting[] = $entry;

      } else if ((!is_null($start_reviews) && $start_reviews != "") && is_null($close_review)) {
          $ongoing_meeting[] = $entry;

      } else if (is_null($start_reviews) || $start_reviews == "") {
          $pending_for_initiate_meeting[] = $entry;
      }






      if (!is_null($start_reviews)) {

        $start_review = strtotime($entry->start_review);
        $plan_date    = strtotime($entry->plan_date);

            if ($start_review == $plan_date) {
            $onTime++;
            } else {
                $delayed++;
            }
        }

    
}

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
          <h5>📌 Review Task Type Summary</h5>
          <small>
               This section presents the distribution of your Review task types for better planning insight. 📌 Review: Represents the total number of impromptu or quickly arranged Review. 📌 Review: Reflects the count of Review that were pre-planned and scheduled in advance. Understanding this breakdown helps optimize scheduling strategies and balance between spontaneous and organized interactions.
          </small>
      </div>
<hr>
<div class="row">
 
           <div class="col-md-4">
            <div class="card text-center shadow text-dark" style="background: linear-gradient(to right, #ffecd2,rgb(169, 247, 157)); border-radius: 12px; box-shadow: 0 4px 12px rgba(0,0,0,0.1); padding: 20px;">

                  <h5 class="card-title"><b>📌 Total Review</b></h5>
                  <small>total plan review.</small> 
                  <hr>
                  <h3 class="card-text"><?= sizeof($liveMettingsTasks); ?></h3>
        
              </div>
          </div>
           <div class="col-md-4">
            <div class="card text-center shadow text-dark" style="background: linear-gradient(to right, #ffecd2,rgb(169, 247, 157)); border-radius: 12px; box-shadow: 0 4px 12px rgba(0,0,0,0.1); padding: 20px;">

                  <h5 class="card-title"><b>🟢 On Time Start</b></h5>
                  <small>Review started exactly at the scheduled time without any delay.</small>
                  <hr>
                  <h3 class="card-text"><?=$onTime?></h3>
        
              </div>
          </div>
          <div class="col-md-4">
            <div class="card text-center shadow text-dark" style="background: linear-gradient(to right, #ffecd2,rgb(251, 132, 132)); border-radius: 12px; box-shadow: 0 4px 12px rgba(0,0,0,0.1); padding: 20px;">
              
                  <h5 class="card-title"><b>🔴 Delayed Start</b></h5>
                  <small>Review began after scheduled time; participant was not punctual.</small> 
                  <hr>
                  <h3 class="card-text"><?=$delayed?></h3>
              
              </div>
          </div>
</div>







<div class="text-center" style="background: linear-gradient(to right, #ede7f6, #ffffff); border-radius: 12px; box-shadow: 0 4px 8px rgba(0,0,0,0.1); padding: 20px;">
          <h5>⚡ Review Status Overview</h5>
          <small>
                This summary provides a quick snapshot of your Review progress. ✅ Complete Review indicate successful sessions with all discussions concluded (6). 🕒 Ongoing Review are actively in progress (2), while ⏳ Only Initiated Review have started but lack substantial discussion (0). ❌ Pending for Initiate Review are yet to begin (0). This helps in tracking progress, identifying delays, and ensuring timely initiation and completion of all planned discussions. 🚀
          </small>
      </div>
  <hr>
<div class="row">
     <!-- <div class="col-md-4">
                      <div class="card text-center shadow text-dark" style="background: linear-gradient(to right, #ffecd2,rgb(142, 251, 132)); border-radius: 12px; box-shadow: 0 4px 12px rgba(0,0,0,0.1); padding: 20px;">
                       
                            <h5 class="card-title"><b>✅ Complete Review</b></h5>
                            <small>✅ Complete Review: Successfully finished meeting with all planned discussions covered.</small> 
                            <hr>
                            <h3 class="card-text"><?=count($complete_meeting)?></h3>
                        
                        </div>
                    </div> -->
 <div class="col-md-6">
                      <div class="card text-center shadow text-dark" style="background: linear-gradient(to right, #ffecd2,rgb(155, 255, 163)); border-radius: 12px; box-shadow: 0 4px 12px rgba(0,0,0,0.1); padding: 20px;">
                       
                            <h5 class="card-title"><b>⏳ Started Review</b></h5>
                            <small>🕒 Ongoing Review: Review currently in progress, with discussions actively taking place.</small> 
                            <hr>
                            <h3 class="card-text"><?=count($ongoing_meeting)?></h3>
                        
                        </div>
                    </div>
                    <div class="col-md-6">
                      <div class="card text-center shadow text-dark" style="background: linear-gradient(to right, #ffecd2,rgb(251, 132, 148)); border-radius: 12px; box-shadow: 0 4px 12px rgba(0,0,0,0.1); padding: 20px;">
                       
                            <h5 class="card-title"><b>❌ Pending for Start Review</b></h5>
                            <small>❌ Pending for Start Review: Review not yet started; awaiting initiation or scheduling.</small> 
                            <hr>
                            <h3 class="card-text"><?=count($pending_for_initiate_meeting)?></h3>
                        
                        </div>
                    </div>
</div>



<hr>
<div class="text-center" style="background: linear-gradient(to right, #ede7f6, #ffffff); border-radius: 12px; box-shadow: 0 4px 8px rgba(0,0,0,0.1); padding: 20px;">
  <h5><i>📌 Live Task Tracking (Review)</i></h5>
  <small>This section outlines ongoing meeting-related tasks, categorized into personal and team funnels with detailed subcategories for better visibility and tracking.</small>
</div>
<hr>
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
// dd($liveMettingsTasks);
?>

<div class="row">
<?php
$j=1;
foreach ($liveMettingsTasks as $task):
    // if($task->nextCFID !=0){
    //     continue;
    // }

  
              $alertClass = '';
              $hrAClass = '';
              if (!is_null($task->start_review) && is_null($task->close_review)) {
                  $alertClass = 'blinking-shadow-effect red-border blinking-background-effect';
                  $hrAClass = 'hr_alert_class';
                   ?>
                  <style>
                    .blinking-shadow-effect .mb-1, .blinking-shadow-effect .my-1, .blinking-shadow-effect a {color: red;}
                  </style>
                  <?php
                } else {
                  $alertClass = '';
                  $hrAClass = '';
                  
                }
            

    ?>
    <div class="col-md-4 mb-4">
        <div class="card meetingslist-card <?=$alertClass;?>" style="background: <?= $gradientColors[$index++ % count($gradientColors)] ?>; border-radius: 14px; box-shadow: 0 4px 12px rgba(0,0,0,0.1); padding: 20px;">
            <div class="text-left">
                <center>
                    <h3 class="mb-1 badge badge-pill badge-dark" style="font-weight: 600; font-size: 1.1rem;"><?= htmlspecialchars($j) ?></h3>
                    <hr style="width: 20%;" class="cardline <?=$hrAClass?>">
                    <h3 class="mb-1" style="font-weight: 600;">🏷️ <?= htmlspecialchars($task->reviewtype) ?> Review</h3>
                    <hr style="width: 60%;" class="cardline <?=$hrAClass?>">
                <h5 class="d-block mb-2">🧑‍💼 
                  <strong> <?= $task->to_name ?></strong>
                  (<strong> <?= $task->by_user_type ?></strong>)
                <hr>
                <?php 
                 if (!is_null($task->start_review) && is_null($task->close_review)) {
                ?>
                   <div class="live-badge">
                    <span class="live-dot"></span> LIVE
                  </div>
                  <?php } ?>
                 
            </h5>
                </center>
                <hr class="cardline <?=$hrAClass?>">



                <?php
                /*
                if($user['user_id'] !== $task->by_uid){ ?> 
                <a href="javascript:void(0)" class="btn btn-sm btn-outline-dark position-absolute openCommentModal" 
                data-task-id="<?= $task->task_id ?>" style="top: 10px; right: 10px;">
                    ➕ Add Comment
                </a>
                <?php }
                */
                ?>

                
                

            <p class="mb-1"><strong>📅 Plan Date & Time:</strong> <?= $task->plan_date ?></p> <hr class="cardline <?=$hrAClass?>">
            
            <p class="mb-1">
            <strong>▶️ Start Date & Time:</strong> 
            <?php if (!empty($task->start_review)) : ?>
                <?= $task->start_review ?>
            <?php else: ?>
                <span class="bg-warning p-1">Pending</span>
            <?php endif; ?>
        </p>
        <hr class="cardline <?= $hrAClass ?>">


         <p class="mb-1">
            <strong>▶️ Close Date & Time:</strong> 
            <?php if (!empty($task->close_review)) : ?>
                <?= $task->close_review ?>
            <?php else: ?>
                <span class="bg-warning p-1">Pending</span>
            <?php endif; ?>
        </p>
        <hr class="cardline <?= $hrAClass ?>">



          <?php if (!is_null($task->close_review) ) { ?>
              <p class="mb-1"><strong>✅ Close Date & Time:</strong> <?= $task->close_review ?></p> <hr class="cardline <?=$hrAClass?>">
              <p class="mb-1"><strong>💬 Close Remarks:</strong> <?= $task->close_remarks ?></p> <hr class="cardline <?=$hrAClass?>">
          <?php } ?>

           
            <p class="mb-1"><strong>⏳ Review Close System Time:</strong> <?= $task->review_close_time ?></p> <hr class="cardline <?=$hrAClass?>">
            <p class="mb-1"><strong>📝 Plan Time Remarks:</strong> <?= $task->plan_time_remarks ?></p> <hr class="cardline <?=$hrAClass?>">

             
            <p class="mb-1"><strong>⏱️ Planning → Start Difference:</strong> <?= $task->planning_to_start_diffrence ?></p> <hr class="cardline <?=$hrAClass?>">
            <p class="mb-1"><strong>🔄 Started but Not Closed Till Date:</strong> <?= $task->start_and_not_close_till_date ?></p> <hr class="cardline <?=$hrAClass?>">
            <p class="mb-1"><strong>📌 Planned & Started but Not Closed Till Date:</strong> <?= $task->plan_and_start_and_not_close_till_date ?></p> <hr class="cardline <?=$hrAClass?>">
            
             <?php if (is_null($task->start_review) ) { ?>
                <p class="mb-1"><strong>📍 Planned but Not Started Till Date:</strong> <?= $task->plan_and_not_start_till_date ?></p> <hr class="cardline <?=$hrAClass?>">
              <?php } ?>

              <?php if (!is_null($task->close_review) ) { ?>
              <p class="mb-1"><strong>🎯 Target Setting:</strong> <?= $task->target_settings ?></p> <hr class="cardline <?=$hrAClass?>">
              <?php } ?>




                <?php /*
                <hr class="cardline <?=$hrAClass?>">
                <?php $reminderComments =  $this->Menu_model->GetReminderCommets($task->task_id);?>
                <p class="mb-1 text-center"><strong>🔔 Reminder Comment:</strong> </p>
                <hr style="width: 50%;" class="<?=$hrAClass?>">
                  <span id="reminder_remarks_<?=$task->task_id?>">
                        <?php  if(sizeof($reminderComments) > 0){ ?>
                          <?php foreach($reminderComments as $reminderComment): ?>
                           - 📝 <?=$reminderComment->reminder_remarks.' — 👤 '.$reminderComment->reminderby_user.' ⏰ Time: '.$reminderComment->created_at; ?> <hr>
                          <?php endforeach; ?>
                        <?php } ?>
                </span> 
               <hr class="cardline <?=$hrAClass?>">

               */ ?>
                
            </div>
        </div>
    </div>
<?php $j++; endforeach; ?>
</div>

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
</style>

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




                  <hr>
                  <div class="text-center" style="background: linear-gradient(to right, #ede7f6, #ffffff); border-radius: 12px; box-shadow: 0 4px 8px rgba(0,0,0,0.1); padding: 20px;">
                    <h5><i>📌 Today's Task Tracking (Review)</i></h5>
                    <small>This section highlights today's Review-related tasks, organized under personal and team funnels with detailed subcategories for clear visibility and effective tracking.</small>
                    </div>
                  <hr>
                    <div class="table-responsive text-nowrap">
                  <table id="example2" class="table table-striped table-bordered" cellspacing="0" width="100%">
                  <thead class="thead-dark">
                   <tr>
                    <th>🔢 Sr. No</th>
                  
                    <th>🙋 By Name</th>
       
                    <th>🙇 To Name</th>
                    <th>📋 Review Type</th>
                    <th>📅 Plan Date</th>
                    <th>🗄️ Store Date</th>
                    <th>⏯️ Start Review</th>
                    <th>✅ Close Review</th>
                    <th>💬 Close Remarks</th>
                    <th>🔗 Meeting Link</th>
                    <th>⏰ Review Close Time</th>
                    <th>📝 Plan Time Remarks</th>
                    <th>📊 Base Review</th>
                    <th>🎯 Target Settings</th>
                    <th>⏳ Planning → Start Difference</th>
                    <th>⏯️➡️❌ Start & Not Close Till Date</th>
                    <th>🗓️⏯️❌ Plan + Start & Not Close Till Date</th>
                    <th>🗓️❌ Plan & Not Start Till Date</th>
                </tr>

            </thead>
            <tbody>
                <?php
                $i = 1;
                foreach ($liveMettingsTasks as $review) {

                  $review_id = $review->review_id;

                  $r = rand(150, 255);
                  $g = rand(150, 255);
                  $b = rand(150, 255);
                  $backgroundColor = "rgba($r, $g, $b,.2)";

                  $hue        = rand(0, 360); // Full color wheel
                  $saturation = rand(60, 100); // High saturation for rich colors
                  $lightness  = rand(30, 45); // Low lightness for a deeper tone
                  $backgroundColorNew = "hsl($hue, $saturation%, $lightness%)";

                  ?>
                  
                  <tr style="background-color: <?php echo $backgroundColor; ?>;color:<?=$backgroundColorNew;?>!important" title="<?=$review_id?>">

                         <td><?= $i; ?></td>
                          <td><?= $review->by_name; ?></td>
                          <td><?= $review->to_name; ?></td>
                          <td><?= $review->reviewtype; ?></td>
                          <td><?= $review->plan_date; ?></td>
                          <td><?= $review->store_date; ?></td>
                          <td><?= $review->start_review; ?></td>
                          <td><?= $review->close_review; ?></td>
                          <td><?= $review->close_remarks; ?></td>
                          <td><?= $review->meeting_link; ?></td>
                          <td><?= $review->review_close_time; ?></td>
                          <td><?= $review->plan_time_remarks; ?></td>
                          <td><?= $review->base_review; ?></td>
                          <td><?= $review->target_settings; ?></td>
                          <td><?= $review->planning_to_start_diffrence; ?></td>
                          <td><?= $review->start_and_not_close_till_date; ?></td>
                          <td><?= $review->plan_and_start_and_not_close_till_date; ?></td>
                          <td><?= $review->plan_and_not_start_till_date; ?></td>
                       
                    </tr>
              <?php $i++; } ?>
            </tbody>
                  </table>
                </div>






                </div>
              </div>
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
    </section>
 


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
        url:'<?=base_url();?>Menu/AddCommentOnTBLTask',
        method: 'post',
        data: {taskId: taskId,comment: comment},
        success: function(result){
            // $("#reminder_remarks_"+taskId).text(comment);

            $.ajax({
              url:'<?=base_url();?>Menu/GetReminderComments',
              method: 'post',
              data: {taskId: taskId},
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
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.4.0/jspdf.umd.min.js"></script>
    <script>
      $("#example1").DataTable({
        "responsive": false, "lengthChange": false, "autoWidth": false,
        "buttons": ["csv", "excel", "pdf"]
      }).buttons().container().appendTo('#example1_wrapper .col-md-6:eq(0)');
      $("#example2").DataTable({
        "responsive": false, "lengthChange": false, "autoWidth": false,
        "buttons": ["csv", "excel", "pdf"]
      }).buttons().container().appendTo('#example2_wrapper .col-md-6:eq(0)');


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
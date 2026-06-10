<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>🗓️ Team Planner Reports | STEM APP | WebAPP</title>
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
      .card-bg-1 { background-color: #FFD700!important; } /* Gold */
      .card-bg-2 { background-color: #C0C0C0!important; } /* Silver */
      .card-bg-3 { background-color: #CD7F32!important; } /* Bronze */
      .card-bg-4 { background-color: #4682B4!important; } /* Steel Blue */
      .card-bg-5 { background-color: #556B2F!important; } /* Dark Olive Green */
      .card-bg-6 { background-color: #8B0000!important; } /* Dark Red */
      .card-bg-7 { background-color: #4B0082!important; } /* Indigo */
      .card-bg-8 { background-color: #2E8B57!important; } /* Sea Green */
      .card-bg-9 { background-color: #FF6347!important; } /* Tomato */
      .card-bg-10 { background-color: #9932CC!important; } /* Dark Orchid */
      .card-bg-11 { background-color: #8B4513!important; } /* Saddle Brown */
      .card-bg-12 { background-color: #20B2AA!important; } /* Light Sea Green */
      .card-bg-13 { background-color: #FFDAB9!important; } /* Peach Puff */
      .card-bg-14 { background-color: #6A5ACD!important; } /* Slate Blue */
      .card-bg-15 { background-color: #FF69B4!important; } /* Hot Pink */
      .card-bg-16 { background-color: #00BFFF!important; } /* Deep Sky Blue */
      .text-light {
      color: white !important;
      }
      .text-dark {
      color: black !important;
      }
      .form-container {
      display: flex;
      align-items: center;
      gap: 10px;
      }
      .form-container input, .form-container button {
      padding: 8px;
      border-radius: 4px;
      border: 1px solid #ccc;
      }
      .form-container button {
      background-color: #007bff;
      color: white;
      border: none;
      cursor: pointer;
      }
      .col-sm-6.text-right-data {
      align-items: right;
      justify-content: right;
      display: flex;
      }
      .row-equal-height {
      display: flex;
      flex-wrap: wrap;
      }
      .row-equal-height > [class*='col-'] {
      display: flex;
      flex-direction: column;
      }
      .card {
      margin-bottom: 20px;
      flex: 1;
      display: flex;
      flex-direction: column;
      }
      .card-body {
      flex: 1;
      display: flex;
      flex-direction: column;
      justify-content: center; /* Center content vertically */
      }
      .card-bg-1 { border: 2px solid #D4AF37;  } /* Gold border */
      .card-bg-2 { border: 2px solid #A9A9A9; } /* Silver border */
      .card-bg-3 { border: 2px solid #8B4513; } /* Bronze border */
      .card-bg-4 { border: 2px solid #1E90FF; } /* Steel Blue border */
      .card-bg-5 { border: 2px solid #556B2F; } /* Dark Olive Green border */
      .card-bg-6 { border: 2px solid #800000; } /* Dark Red border */
      .card-bg-7 { border: 2px solid #4B0082; } /* Indigo border */
      .card-bg-8 { border: 2px solid #2E8B57; } /* Sea Green border */
      .card-bg-9 { border: 2px solid #FF6347; } /* Tomato border */
      .card-bg-10 { border: 2px solid #9932CC; } /* Dark Orchid border */
      .card-bg-11 { border: 2px solid #8B4513; } /* Saddle Brown border */
      .card-bg-12 { border: 2px solid #20B2AA; } /* Light Sea Green border */
      .card-bg-13 { border: 2px solid #FFDAB9; } /* Peach Puff border */
      .card-bg-14 { border: 2px solid #6A5ACD; } /* Slate Blue border */
      .card-bg-15 { border: 2px solid #FF69B4; } /* Hot Pink border */
      .card-bg-16 { border: 2px solid #00BFFF; } /* Deep Sky Blue border */
      /* Multiple layer frame styles */
      .frame-layer-1 {
      padding: 5px;
      background: linear-gradient(145deg, #f0f0f0, #d9d9d9);
      border-radius: 15px;
      flex: 1;
      display: flex;
      flex-direction: column;
      margin-bottom: 2px;
      }
      .frame-layer-2 {
      padding: 10px;
      background: linear-gradient(145deg, #e6e6e6, #cccccc);
      border-radius: 10px;
      flex: 1;
      display: flex;
      flex-direction: column;
      margin-bottom: 2px;
      }
      .frame-layer-3 {
      padding: 15px;
      background: linear-gradient(145deg, #f5f5f5, #e0e0e0);
      border-radius: 5px;
      flex: 1;
      display: flex;
      flex-direction: column;
      margin-bottom: 2px;
      }
      .card.text-center{
      align-items: center;
      justify-content: center;
      display: flex;
      }
      .card-group {
      margin-bottom: 20px;
      }
      .card-group-title {
      width: 100%;
      text-align: center;
      margin-bottom: 10px;
      font-weight: bold;
      font-size: 1.2em;
      }
      @media (min-width: 576px) {
      .card-group {
      display: -ms-flexbox;
      display: unset;
      -ms-flex-flow: row wrap;
      flex-flow: row wrap;
      }
      }
      .row-color-1 { background-color: #ffdddd; }
      .row-color-2 { background-color: #ddffdd; }
      .row-color-3 { background-color: #ddddff; }
      .row-color-4 { background-color: #ffffdd; }
      .row-color-5 { background-color: #ffddff; }
      .row-color-6 { background-color: #d1ffd1; }
      .row-color-7 { background-color: #ffd1d1; }
      .row-color-8 { background-color: #d1d1ff; }
      .row-color-9 { background-color: #ffefd1; }
      .row-color-10 { background-color: #d1ffe7; }
      .row-color-11 { background-color: #ffd1f9; }
      .row-color-12 { background-color: #d1f9ff; }
      .row-color-13 { background-color: #f9d1ff; }
      .row-color-14 { background-color: #d1ffd9; }
      .row-color-15 { background-color: #ffd9d1; }
      .row-color-16 { background-color: #d9ffd1; }
      .row-color-17 { background-color: #d1d9ff; }
      .row-color-18 { background-color: #ffd1d9; }
      .row-color-19 { background-color: #d9d1ff; }
      .row-color-20 { background-color: #ffd1e7; }
      .row-color-21 { background-color: #d1ffe7; }
      .row-color-22 { background-color: #e7d1ff; }
      .row-color-23 { background-color: #d1ffd1; }
      .row-color-24 { background-color: #ffd1ff; }
      .row-color-25 { background-color: #d1e7ff; }
      .row-color-26 { background-color: #ffd1d1; }
      .row-color-27 { background-color: #d1ffd9; }
      .row-color-28 { background-color: #d9d1ff; }
      .row-color-29 { background-color: #ffd9d1; }
      .row-color-30 { background-color: #d1d9ff; }
      .row-color-31 { background-color: #ffd1e7; }
      .row-color-32 { background-color: #e7d1ff; }
      .row-color-33 { background-color: #d1ffd1; }
      .row-color-34 { background-color: #ffd1ff; }
      .row-color-35 { background-color: #d1e7ff; }
      .row-color-36 { background-color: #ffd1d1; }
      .row-color-37 { background-color: #d1ffd9; }
      .row-color-38 { background-color: #d9d1ff; }
      .row-color-39 { background-color: #ffd9d1; }
      .row-color-40 { background-color: #d1d9ff; }
      .row-color-41 { background-color: #ffd1e7; }
      .row-color-42 { background-color: #e7d1ff; }
      .row-color-43 { background-color: #d1ffd1; }
      .row-color-44 { background-color: #ffd1ff; }
      .row-color-45 { background-color: #d1e7ff; }
      .row-color-46 { background-color: #ffd1d1; }
      .row-color-47 { background-color: #d1ffd9; }
      .row-color-48 { background-color: #d9d1ff; }
      .row-color-49 { background-color: #ffd9d1; }
      .row-color-50 { background-color: #d1d9ff; }
      tr{
      font-weight: bold;
      }
      .beautiful-textarea {
      padding: 10px;
      border: 2px solid #ccc;
      border-radius: 10px;
      box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
      font-family: Arial, sans-serif;
      font-size: 16px;
      resize: none; /* Prevents resizing */
      outline: none; /* Removes the default focus outline */
      transition: border-color 0.3s, box-shadow 0.3s;
      }
      .beautiful-textarea:focus {
      border-color: #66afe9;
      box-shadow: 0 4px 12px rgba(102, 175, 233, 0.4);
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
  </head>
  <body class="hold-transition sidebar-mini layout-fixed">
    <div class="wrapper">
      <!-- Preloader -->
      <!-- Navbar -->
      <?php $this->load->view($dep_name.'/nav.php');?>
      <!-- /.navbar -->
      <!-- Content Wrapper. Contains page content -->
      <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <div class="content-header">
          <div class="container-fluid">
            <div class="row mb-2">
              <div class="col-md-12 text-center">
                <div class="frame-layer-1">
                  <div class="frame-layer-2">
                  <div class="frame-layer-3">
                        <h1 class="m-0 premium-color-1" style="color: #ff009b;">🗓️ Team Planner Reports</h1>
                        <p class="premium-color-2" style="color: #ff0000;">
                            This section provides a comprehensive overview of all user tasks, including statistics on calls, emails, meetings, their statuses, and other key details.
                        </p>
                        <p class="premium-color-2" style="color: #2c00ee;">
                            <strong><mark><?= $sdate ?> to <?= $edate ?></mark></strong>
                        </p>
                        <p class="premium-color-2" style="color: #2c00ee;">
                            <strong>Task: <mark><?= $taskActionDatas ?></mark></strong>
                        </p>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <br>
            <div class="row mb-2">
              <hr>
              <div class="text-right-data text-center" style="background: linear-gradient(to right, #ceffb2, #ffffff); border-radius: 12px; box-shadow: 0 4px 8px rgba(0,0,0,0.1); padding: 20px;">
                <?php 
                  $clusterPstDatas  = $this->Menu_model->GetAdminActiveTeam($user['user_id']);
                  $taskActions      = $this->Menu_model->get_action();
                  $userRoles        = $this->Menu_model->GetActiveDepartmentList($uid);
                  ?>
                <div class="col text-center formcenterData">
                  <div>
                    <hr>
                    <form action="<?=base_url()?>Reports/TeamPlannerReport" method="post" class="mt-3">
                      <div class="row mb-4">
                        <div class="col">
                          <label for="selectedDate">Start Date</label>
                          <input type="date" value="<?=$sdate;?>" id="selectedDate" max="<?=date('Y-m-d')?>" name="sdate" style="width: 200px;" class="form-control">
                        </div>
                        <div class="col">
                          <label for="selectedDate">End Date</label>
                          <input type="date" value="<?=$edate;?>" id="selectedDate" name="edate" style="width: 200px;" max="<?=date('Y-m-d')?>" class="form-control">
                        </div>
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
                          <label for="selectedDate">Select RM</label>
                          <select id="rm_filter" name="rm_filter" class="form-control">
                            <?php if($user['type_id'] !== 3){ ?> 
                            <option value="all" <?= ($rm_filter == 'all') ? 'selected' : ''; ?>>All</option>
                            <?php } ?>
                            <?php foreach ($getTeamsDatas as $getTeamsData) { ?>
                            <option value="<?= $getTeamsData->user_id; ?>" <?= ($getTeamsData->user_id == $rm_filter) ? 'selected' : ''; ?>>
                              <?= $getTeamsData->name; ?>
                            </option>
                            <?php }?>
                          </select>
                        </div>
                        
                        <div class="col">
                          <label for="selectedDate">Select Task</label>
                          <select class="form-control" name="task_action" style="width: fit-content;">
                            <option value="" disabled>Select Task</option>
                            <option value="all" <?= ($task_action_id == 'all') ? 'selected' : ''; ?>>All</option>
                            <?php foreach ($taskActions as $taskAction): ?>
                            <?php 
                              if($taskAction->action_status == 0){
                                continue;
                              } ?>
                            <?php if($task_action_id == $taskAction->id ){
                              $selectedtext =  'selected';
                              }else{
                              $selectedtext = '';
                              } ?>
                            <option <?= $selectedtext; ?> value="<?= $taskAction->id; ?>"><?= htmlspecialchars($taskAction->name); ?></option>
                            <?php endforeach; ?>
                          </select>
                        </div>


                        <div class="col">
                          <div class="form-group">
                            <button type="submit" class="custom-btn btn-11 partnearray p-2" style="margin-top: 36px; width: 100px;">Filter</button>
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
              if ($this->session->flashdata('success_message')): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
              <strong> <?php echo $this->session->flashdata('success_message'); ?></strong>
              <button type="button" class="close" data-dismiss="alert" aria-label="Close">
              <span aria-hidden="true">&times;</span>
              </button>
            </div>
            <?php endif; ?>
            <?php
              if ($this->session->flashdata('error_message')): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
              <strong> <?php echo $this->session->flashdata('error_message'); ?></strong>
              <button type="button" class="close" data-dismiss="alert" aria-label="Close">
              <span aria-hidden="true">&times;</span>
              </button>
            </div>
            <?php endif; ?>
          </div>
        </div>
        <!-- Main content -->
        <section class="content">
          <div class="container-fluid">
            <?php 


              function GetBackgroundColor(){
                      $r = rand(150, 255);
                      $g = rand(150, 255);
                      $b = rand(150, 255);
                      $backgroundColor = "rgba($r, $g, $b,.3)";
                      return $backgroundColor;
              }
              function GetTextColor(){
                      $hue = rand(0, 360);
                      $saturation = rand(60, 100);
                      $lightness = rand(30, 45);
                      $textColor = "hsl($hue, $saturation%, $lightness%)";
                      return $textColor;
              }

              $sameDaysPlanning             = $taskDatadatasDB['sameDaysPlanning'];
              $sameDaysPlanningByUser       = $taskDatadatasDB['sameDaysPlanningByUser'];

              $pendingPlanning              = $taskDatadatasDB['pendingPlanning'];
              $pendingPlanningByUser        = $taskDatadatasDB['pendingPlanningByUser'];

              $pendingMeetingsDelete        = $taskDatadatasDB['pendingMeetingsDelete'];
              $pendingMeetingsDeleteByUser  = $taskDatadatasDB['pendingMeetingsDeleteByUser'];

              $specialLeaveRequest          = $taskDatadatasDB['specialLeaveRequest'];
              $specialLeaveRequestByUser    = $taskDatadatasDB['specialLeaveRequestByUser'];

              $userplanningTime             = $taskDatadatasDB['userplanningTime'];
              $userplanningTimeByUser       = $taskDatadatasDB['userplanningTimeByUser'];

              $reviewPlanned                = $taskDatadatasDB['reviewPlanned'];
              $reviewPlannedByUser          = $taskDatadatasDB['reviewPlannedByUser'];

              $totalTasks                   = $taskDatadatasDB['totalTasks'];
              $totalTasksByUser             = $taskDatadatasDB['totalTasksByUser'];

              $teamDailyPlannedByUser       = $taskDatadatasDB['teamDailyPlannedByUser'];
   
              
              $same_day_planning = $sameDaysPlanning[0]->same_day_planning;

              $pending_task_planning_request  = $pendingPlanning[0]->pending_task_planning_request;
              $plan_but_not_initiated_request = $pendingPlanning[0]->plan_but_not_initiated_request;
              $old_pending_task_request       = $pendingPlanning[0]->old_pending_task_request;

              $pending_meetings_task_request      = $pendingMeetingsDelete[0]->pending_meetings_task_request;

              $special_request_for_leave_request  = $specialLeaveRequest[0]->special_request_for_leave_request;

              $session_counts                     = $userplanningTime[0]->session_count;
              $total_time                         = $userplanningTime[0]->total_time;


              $total_review_count             = $reviewPlanned[0]->total_review_count;
              $for_him_self_review            = $reviewPlanned[0]->for_him_self_review;
              $for_other_review               = $reviewPlanned[0]->for_other_review;


         

              $useActiveInActive      = $totalUserDatas['data2'];
              $total_user             = $useActiveInActive[0]->total_user;
              $total_active_user      = $useActiveInActive[0]->total_active_user;
              $total_inactive_user    = $useActiveInActive[0]->total_inactive_user;

              // dd($plannerReportDatas);
              // dd($totalTasks);
      
          
              $result = [];
              $get_total_task_count = 0;

              foreach ($groupedData as $user_id => $tasks) {
                  if (!empty($tasks)) {
                      $count = $tasks[0]->total_task_count;
                      $result[$user_id] = $count;
                      $get_total_task_count += $count;
                  }
              }


              ?>
       
            <div class="text-center" style="background: linear-gradient(to right, #ede7f6, #ffffff); border-radius: 12px; box-shadow: 0 4px 8px rgba(0,0,0,0.1); padding: 20px;">
            <h5><i>🗂️ Task Plan Overview</i></h5>
                <small>
                📅 Includes same-day planning 🕒, pending task requests ⏳, and meeting delete actions 🗑️ — helping track real-time task flow and decision status.
                </small>
            </div>
            <hr>
            <div class="row">
               <div class="col-md-3">
                <div class="card text-center shadow text-dark" style="background-color: rgb(242 153 245 / 30%); color:hsl(104, 80%, 45%);   height: 210px;">
                  <div class="card-body" style="color:hsl(240, 99%, 34%)!important">
                  <h5 class="card-title"><b>📅 Same Day Planning Request</b></h5>
                    <small>
                    ⏱️ Tasks requested and planned on the same day — urgent or just-in-time plans.
                    </small>
                    <hr>
                   <a target="_BLANK" class="card-count-heading-new"  href="<?=base_url()?>Reports/UserRequestDetails/same_day_planning/<?=$uid?>/<?=$sdate?>/<?=$edate?>/<?=$task_action_id?>">
                      <h3 class="card-text"><?=$same_day_planning ?></h3>
                    </a>
                  </div>
                </div>
              </div>
                 <div class="col-md-3">
                    <div class="card text-center shadow text-dark" style="background-color: rgb(208 239 255 / 30%); color:hsl(245, 91%, 38%);height: 210px;">
                    <div class="card-body" style="color:hsl(191, 66%, 43%)!important">
                    <h5 class="card-title"><b>⏳ Pending Task Plan Request</b></h5>
                        <small>
                        📝 Tasks submitted for planning but not yet approved or finalized.
                        </small>
                        <hr>
                        <a target="_BLANK" class="card-count-heading-new"  href="<?=base_url()?>Reports/UserRequestDetails/pending_task_planning_request/<?=$uid?>/<?=$sdate?>/<?=$edate?>/<?=$task_action_id?>">
                        <h3 class="card-text"><?=$pending_task_planning_request?></h3>
                        </a>
                    </div>
                    </div>
                </div>
              <div class="col-md-3">
               <div class="card text-center shadow text-dark" style="background-color: rgb(243 249 158 / 30%); color:hsl(6, 97%, 38%);    height: 210px;">
                  <div class="card-body" style="color:hsl(256, 83%, 38%)!important">
                  <h5 class="card-title"><b>🗑️ Meeting Task Delete Request</b></h5>
                    <small>
                    ❌ Requests to remove meeting tasks — typically canceled or no longer needed.
                    </small>
                    <hr>
                   <a target="_BLANK" class="card-count-heading-new"  href="<?=base_url()?>Reports/UserRequestDetails/pending_meetings_task_request/<?=$uid?>/<?=$sdate?>/<?=$edate?>/<?=$task_action_id?>">
                      <h3 class="card-text"><?=$pending_meetings_task_request?></h3>
                    </a>
                  </div>
                </div>
              </div>
              <div class="col-md-3">
               <div class="card text-center shadow text-dark" style="background-color: rgb(184 241 162 / 30%); color:hsl(6, 97%, 38%);    height: 210px;">
                  <div class="card-body" style="color:hsl(256, 83%, 38%)!important">
                  <h5 class="card-title"><b>📝 Special Request for Leave</b></h5>
                    <small>
                        🗓️ Requests to skip or cancel tasks due to leave or valid unavailability.
                    </small>
                    <hr>
                   <a target="_BLANK" class="card-count-heading-new"  href="<?=base_url()?>Reports/UserRequestDetails/special_request_for_leave_request/<?=$uid?>/<?=$sdate?>/<?=$edate?>/<?=$task_action_id?>">
                      <h3 class="card-text"><?=$special_request_for_leave_request;?></h3>
                    </a>
                  </div>
                </div>
              </div>
              </div>
              <hr>

              <div class="row">
                <div class="col-md-4">
                <div class="card text-center shadow text-dark" style="background-color:rgba(203, 193, 189,.3); color:hsl(131, 66%, 43%);">
                  <div class="card-body" style="color:hsl(324, 86%, 35%)!important">
                    <h5 class="card-title"><b>⏳ Pending Task Plan Request</b></h5>
                    <small>📝 Tasks submitted for planning but not yet approved or finalized.</small> 
                    <hr>
                    <a target="_BLANK" class="card-count-heading-new" href="<?=base_url()?>Reports/UserRequestDetails/pending_task_planning_request/<?=$uid?>/<?=$sdate?>/<?=$edate?>/<?=$task_action_id?>">
                       <h3 class="card-text"><?=$pending_task_planning_request?></h3>
                    </a>
                  </div>
                </div>
              </div>

              <div class="col-md-4">
                <div class="card text-center shadow text-dark" style="background-color:rgba(246, 228, 169,.3); color:hsl(15, 86%, 30%);">
                  <div class="card-body" style="color:hsl(157, 74%, 45%)!important">
                  <h5 class="card-title"><b>🛑 Plan But Not Initiated</b></h5>
                <small>📋 Tasks that were planned but haven't started or shown progress yet.</small>
                    <hr>
                    <a target="_BLANK" class="card-count-heading-new"  href="<?=base_url()?>Reports/UserRequestDetails/plan_but_not_initiated_request/<?=$uid?>/<?=$sdate?>/<?=$edate?>/<?=$task_action_id?>">
                     <h3 class="card-text"><?=$plan_but_not_initiated_request?></h3>
                    </a>
                  </div>
                </div>
              </div>

               <div class="col-md-4">
               <div class="card text-center shadow text-dark" style="background-color:rgba(201, 193, 203,.3); color:hsl(42, 71%, 37%);">
                  <div class="card-body" style="color:hsl(102, 81%, 35%)!important">
                    <h5 class="card-title"><b>⏳ Old Pending Task</b></h5>
                    <small>🕒 Tasks pending for a long time, with status updates made by the user.</small>
                    <hr>
                    <a target="_BLANK" class="card-count-heading-new"  href="<?=base_url()?>Reports/UserRequestDetails/old_pending_task_request/<?=$uid?>/<?=$sdate?>/<?=$edate?>/<?=$task_action_id?>">
                      <h3 class="card-text"><?=$old_pending_task_request?></h3>
                    </a>
                  </div>
                </div>
              </div>
              </div>


             
              <hr>
              <div class="row">
                <div class="col-md-4">
                <div class="card text-center shadow text-dark" style="background-color:rgb(131 213 224 / 30%); color:hsl(131, 66%, 43%);height:230px;">
                  <div class="card-body" style="color:hsl(324, 86%, 35%)!important">
                    <h5 class="card-title"><b>⏳ Total Task (Pending & Approved)</b></h5>
                    <small>📝 Tasks submitted for planning.</small> 
                    <hr>
                     <a target="_BLANK" class="card-count-heading-new" href="<?=base_url()?>Reports/TeamPlannerReportLists/total_task_count/<?=$uid?>/<?=$sdate?>/<?=$edate?>/<?=$task_action_id?>"> 
                       <h3 class="card-text"><?=$get_total_task_count?></h3>
                    </a>
                  </div>
                </div>
              </div>
              <div class="col-md-4">
                <div class="card text-center shadow text-dark" style="background-color:rgba(246, 228, 169,.3); color:hsl(15, 86%, 30%);height:230px;">
                  <div class="card-body" style="color:hsl(157, 74%, 45%)!important">
                  <h5 class="card-title"><b>⏲️ Total Session</b></h5>
                    <small>📊 Displays the total number and duration of all recorded sessions, including meetings, task executions, or tracked activity blocks. ⏰ Helps analyze user engagement, active time, and time management patterns across selected dates.</small>
                    <hr>
                    <!-- <a target="_BLANK" class="card-count-heading-new"  href="<?=base_url()?>Reports/UserRequestDetails/plan_but_not_initiated_request/<?=$uid?>/<?=$sdate?>/<?=$edate?>/<?=$task_action_id?>"> -->
                     <h3 class="card-text"><?=$session_counts;?></h3>
                    <!-- </a> -->
                  </div>
                </div>
              </div>
              <div class="col-md-4">
                <div class="card text-center shadow text-dark" style="background-color:rgba(246, 228, 169,.3); color:hsl(15, 86%, 30%);height:230px;">
                  <div class="card-body" style="color:hsl(157, 74%, 45%)!important">
                  <h5 class="card-title"><b>⏱️ Time Consumed on Planning</b></h5>
                    <small>🕒 Tracks time spent on tasks that were planned but haven't started or progressed yet. Useful for identifying delays or inactive plans.</small>
                    <hr>
                    <!-- <a target="_BLANK" class="card-count-heading-new"  href="<?=base_url()?>Reports/UserRequestDetails/plan_but_not_initiated_request/<?=$uid?>/<?=$sdate?>/<?=$edate?>/<?=$task_action_id?>"> -->
                     <h3 class="card-text"><?=$total_time?></h3>
                    <!-- </a> -->
                  </div>
                </div>
              </div>
              </div>






              <hr>
              <div class="row">
                <div class="col-md-4">
                <div class="card text-center shadow text-dark" style="background-color:rgb(131 213 224 / 30%); color:hsl(131, 66%, 43%);height:230px;">
                  <div class="card-body" style="color:hsl(324, 86%, 35%)!important">
                    <h5 class="card-title"><b>🧾Total Review</b></h5>
                    <small>📝 Tasks submitted for planning.</small> 
                    <hr>
                    <a target="_BLANK" class="card-count-heading-new" href="<?=base_url()?>Reports/UserRequestDetails/total_review_count/<?=$uid?>/<?=$sdate?>/<?=$edate?>/<?=$task_action_id?>">
                       <h3 class="card-text"><?=$total_review_count?></h3>
                    </a>
                  </div>
                </div>
              </div>
              <div class="col-md-4">
                <div class="card text-center shadow text-dark" style="background-color:rgba(246, 228, 169,.3); color:hsl(15, 86%, 30%);height:230px;">
                  <div class="card-body" style="color:hsl(157, 74%, 45%)!important">
                  <h5 class="card-title">🙋 <b>For Himself Review</b></h5>
                    <small>📝 Reviews created by the user for themselves.</small>
                    <hr>
                    <a target="_BLANK" class="card-count-heading-new"  href="<?=base_url()?>Reports/UserRequestDetails/for_him_self_review/<?=$uid?>/<?=$sdate?>/<?=$edate?>/<?=$task_action_id?>">
                     <h3 class="card-text"><?=$for_him_self_review?></h3>
                    </a>
                  </div>
                </div>
              </div>
              <div class="col-md-4">
                <div class="card text-center shadow text-dark" style="background-color:rgba(246, 228, 169,.3); color:hsl(15, 86%, 30%);height:230px;">
                  <div class="card-body" style="color:hsl(157, 74%, 45%)!important">
                  <h5 class="card-title"><b>👥 For Others Review</b></h5>
                    <small>📝 Reviews assigned by the user to others.</small>
                    <hr>
                    <a target="_BLANK" class="card-count-heading-new"  href="<?=base_url()?>Reports/UserRequestDetails/for_other_review/<?=$uid?>/<?=$sdate?>/<?=$edate?>/<?=$task_action_id?>">
                     <h3 class="card-text"><?=$for_other_review?></h3>
                    </a>
                  </div>
                </div>
              </div>
              </div>
          








<hr>
            <?php // dd($total_old_category); ?>
            <div class="text-center" style="background: linear-gradient(to right, #ede7f6, #ffffff); border-radius: 12px; box-shadow: 0 4px 8px rgba(0,0,0,0.1); padding: 20px;">
              <h5>
                <i>📊 Task Management Summary Overview</i><br>
                <small>This summary provides a detailed breakdown of the overall task lifecycle in the system. ✅ Many tasks have been carefully planned and approved, ensuring structured execution. 🛠️ A large portion has been successfully completed, including automatically generated tasks 🤖 that boost efficiency. 📋 Some tasks are still pending completion or awaiting final actions. 🚫 Rejections, deletions, and reassignment activities 🔁 are also tracked to maintain workflow clarity and accountability.</small>
              </h5>
            </div>
            <hr>
            <div class="row">
              <?php  
               $descriptions = [
                        'total_task' => 'Total number of tasks assigned.',
                        'total_planned_task' => 'Tasks that have been planned.',
                        'total_autotask' => 'Auto-generated tasks in the system.',
                        'total_approved_task' => 'Tasks that have been approved.',
                        'total_pending_for_approved' => 'Tasks waiting for approval.',
                        'total_reject' => 'Tasks that have been rejected.',
                        'total_deleted_task' => 'Tasks that were deleted.',
                        'pending_for_assign_after_reject_task' => 'Rejected tasks that are pending reassignment.',
                        'admin_create_request_for_self_assign' => 'Requests created by admin for self-assignment.',
                        'task_assignd_by_admin_after_reject' => 'Rejected tasks reassigned by admin.',
                        'task_assignd_by_user_after_reject' => 'Rejected tasks reassigned by user.',
                        'complete_task' => 'Tasks that have been completed.',
                        'pending_for_complete_task' => 'Tasks still pending completion.',
                        'after_task' => 'Tasks assigned after initial planning or rejection.',
                        'complete_autotask' => 'Auto-generated tasks that are completed.',
                        'pending_for_complete_autotask' => 'Auto-generated tasks still pending completion.'
                    ];
                foreach ($totalTasks  as $tasktypeData) {
                    foreach ($tasktypeData as $key => $value) {
                        if (in_array($key, ['user_id', 'username'])) continue; // Skip these
                
                        $r = rand(50, 255);
                        $g = rand(150, 255);
                        $b = rand(150, 255);
                        $backgroundColor = "rgba($r, $g, $b,.3)";
                
                        $hue = rand(100, 360);
                        $saturation = rand(60, 100);
                        $lightness = rand(30, 45);
                        $backgroundColorNew = "hsl($hue, $saturation%, $lightness%)";
                
                        // Format heading nicely
                        $heading = ucwords(str_replace('_', ' ', $key));
                        $description = isset($descriptions[$key]) ? $descriptions[$key] : 'Task metric description not available.';
                        ?>
              <div class="col-md-3 mb-3">
                <div class="card text-center shadow" style="background-color: <?= $backgroundColor ?>; color: <?= $backgroundColorNew ?>;">
                  <div class="card-body" style="color:<?=$backgroundColorNew;?>!important">
                    <h5 class="card-title"><b><?= $heading ?></b></h5>
                    <small><?= $description ?></small> 
                    <hr>
                    <a target="_BLANK" class="card-count-heading-new" style="color:<?=$backgroundColorNew;?>!important" href="<?=base_url()?>Reports/TeamPlannerReportLists/<?=$key?>/<?=$uid?>/<?=$sdate?>/<?=$edate?>/<?=$task_action_id?>">
                      <h3 class="card-text"><?= $value ?></h3>
                    </a>
                  </div>
                </div>
              </div>
              <?php
                }
                }
                ?>
            </div>
            <br>

            <hr>

            <div class="text-center" style="background: linear-gradient(to right, #ede7f6, #ffffff); border-radius: 12px; box-shadow: 0 4px 8px rgba(0,0,0,0.1); padding: 20px;">
                <h5>
                  <i>📊 Total Active & Inactive User</i><br>
                  <small>
                    This summary provides a comprehensive overview of user engagement in the system. ✅ Active users are consistently participating in planned and approved tasks, contributing to efficient workflow execution. 🛠️ Their involvement includes handling both manual and system-generated tasks 🤖. 📋 In contrast, inactive users show little or no recent activity, indicating possible disengagement. 🚫 System logs also track user deactivation, deletion, or reassignment 🔁 to ensure clarity and maintain accountability within the platform.
                  </small>
                  <br>
                  <small><a href="<?=base_url()?>/Reports/BDDetails" target="_BLANK">view Details</a></small>
                </h5>
            </div>
 <div class="row" style="background: azure; padding: 25px; border-radius: 20px;">
  <div class="col-md-4">
    <div class="card" style="background: linear-gradient(to right, #dfe9f3, #ffffff); border-radius: 12px; box-shadow: 0 4px 8px rgba(0,0,0,0.1); padding: 20px;">
      <div class="text-center">
        <h5 style="color: black;">Total Users</h5>
        <small style="color: #555;">All registered users in the system</small>
        <hr>
            <h3>
                <a class="card-count-heading-new" style="color:<?=$backgroundColorNew;?>!important" href="javascript:void(0)">
                <?=$total_user?>
              </a>
            </h3>
      </div>
    </div>
  </div>
  <div class="col-md-4">
    <div class="card" style="background: linear-gradient(to right, #e0f7fa, #ffffff); border-radius: 12px; box-shadow: 0 4px 8px rgba(0,0,0,0.1); padding: 20px;">
      <div class="text-center">
        <h5 style="color: black;">Total Active Users</h5>
        <small style="color: #555;">Currently active and working</small>
        <hr>
        <h3> <a class="card-count-heading-new" style="color:<?=$backgroundColorNew;?>!important" href="javascript:void(0)"><?=$total_active_user?></a></h3>
      </div>
    </div>
  </div>
  <div class="col-md-4">
    <div class="card" style="background: linear-gradient(to right, #fce4ec, #ffffff); border-radius: 12px; box-shadow: 0 4px 8px rgba(0,0,0,0.1); padding: 20px;">
      <div class="text-center">
        <h5 style="color: black;">Total Inactive Users</h5>
        <small style="color: #555;">Users not active currently</small>
        <hr>
        <h3> <a class="card-count-heading-new" style="color:<?=$backgroundColorNew;?>!important" href="javascript:void(0)"><?=$total_inactive_user?></a></h3>
      </div>
    </div>
  </div>
  </div>
  <hr>








                <?php //  dd($totalTasksByUser); ?>


              <div class="text-center" style="background: linear-gradient(to right, #ede7f6, #ffffff); border-radius: 12px; box-shadow: 0 4px 8px rgba(0,0,0,0.1); padding: 20px;">
            <h5><i>📊 Task Management Summary By User Overview</i></h5>
                <small>
                This summary provides a detailed breakdown of the user overall task lifecycle in the system. ✅ Many tasks have been carefully planned and approved, ensuring structured execution. 🛠️ A large portion has been successfully completed, including automatically generated tasks 🤖 that boost efficiency. 📋 Some tasks are still pending completion or awaiting final actions. 🚫 Rejections, deletions, and reassignment activities 🔁 are also tracked to maintain workflow clarity and accountability.
                </small>
            </div>

            <div class="table-responsive text-nowrap card each">
                          <table class="table table-striped table-bordered" id="example13" style="background: linear-gradient(to right, #e8f5e9, #ffffff); border-radius: 12px; box-shadow: 0 4px 8px rgba(0,0,0,0.1); padding: 20px;">
                            <thead class="thead-dark">
                              <tr class="text-center">
                                  <th>#</th>
                                  <th title="Username">👤 Username</th>
                                  <th title="Total number of tasks assigned.">📋 Total Task</th>
                                  <th title="Tasks that have been planned.">🗓️ Planned Task</th>
                                  <th title="Auto-generated tasks in the system.">🤖 Auto Task</th>
                                  <th title="Tasks that have been approved.">✅ Approved Task</th>
                                  <th title="Tasks waiting for approval.">⏳ Pending for Approval</th>
                                  <th title="Tasks that have been rejected.">❌ Rejected Task</th>
                                  <th title="Tasks that were deleted.">🗑️ Deleted Task</th>
                                  <th title="Rejected tasks that are pending reassignment.">🔁 Pending Assign After Rejection</th>
                                  <th title="Requests created by admin for self-assignment.">📝 Admin Self-Assign Request</th>
                                  <th title="Rejected tasks reassigned by admin.">📤 Admin Reassigned After Rejection</th>
                                  <th title="Rejected tasks reassigned by user.">🙋‍♂️ User Reassigned After Rejection</th>
                                  <th title="Tasks that have been completed.">✔️ Completed Task</th>
                                  <th title="Tasks still pending completion.">📌 Pending Completion</th>
                                  <th title="Tasks assigned after initial planning or rejection.">🔄 After Tasks</th>
                                  <th title="Auto-generated tasks that are completed.">🤖✔️ Completed Auto Task</th>
                                  <th title="Auto-generated tasks still pending completion.">🤖⏳ Pending Auto Task</th>
                              </tr>
                            </thead>
                            <tbody>
                            <?php
                              $i = 1;
                              foreach ($totalTasksByUser as $row) {

                                $r = rand(150, 255);
                                $g = rand(150, 255);
                                $b = rand(150, 255);
                                $backgroundColor = "rgba($r, $g, $b,.2)";
                                $hue = rand(0, 360); // Full color wheel
                                $saturation = rand(60, 100); // High saturation for rich colors
                                $lightness = rand(30, 45); // Low lightness for a deeper tone
                                $backgroundColorNew = "hsl($hue, $saturation%, $lightness%)";

                                ?>
                            <tr style="background-color: <?php echo $backgroundColor; ?>;color:<?=$backgroundColorNew;?>!important" class="text-center">
                              <td><?= $i; ?></td>
                              <td class="text-capitalize"><?php echo htmlspecialchars($row->username); ?></td>
                              <td>
                                <a style="color:<?=$backgroundColorNew;?>!important" class='font-weight-bold' target='_BLANK' href='<?=base_url()?>Reports/TeamPlannerReportLists/total_user_task/<?=$row->user_id?>/<?=$sdate?>/<?=$edate?>/<?=$task_action_id?>/userwise'>
                                <?=$row->total_user_task?>
                                </a>
                              </td>
                              <td>
                                <a style="color:<?=$backgroundColorNew;?>!important" class='font-weight-bold' target='_BLANK' href='<?=base_url()?>Reports/TeamPlannerReportLists/total_planned_task/<?=$row->user_id?>/<?=$sdate?>/<?=$edate?>/<?=$task_action_id?>/userwise'>
                                <?=$row->total_planned_task?>
                                </a>
                              </td>
                              <td>
                                <a style="color:<?=$backgroundColorNew;?>!important" class='font-weight-bold' target='_BLANK' href='<?=base_url()?>Reports/TeamPlannerReportLists/total_autotask/<?=$row->user_id?>/<?=$sdate?>/<?=$edate?>/<?=$task_action_id?>/userwise'>
                                <?=$row->total_autotask?>
                                </a>
                              </td>
                              <td>
                                <a style="color:<?=$backgroundColorNew;?>!important" class='font-weight-bold' target='_BLANK' href='<?=base_url()?>Reports/TeamPlannerReportLists/total_approved_task/<?=$row->user_id?>/<?=$sdate?>/<?=$edate?>/<?=$task_action_id?>/userwise'>
                                <?=$row->total_approved_task?>
                                </a>
                              </td>
                              <td>
                                <a style="color:<?=$backgroundColorNew;?>!important" class='font-weight-bold' target='_BLANK' href='<?=base_url()?>Reports/TeamPlannerReportLists/total_pending_for_approved/<?=$row->user_id?>/<?=$sdate?>/<?=$edate?>/<?=$task_action_id?>/userwise'>
                                <?=$row->total_pending_for_approved?>
                                </a>
                              </td>
                              <td>
                                <a style="color:<?=$backgroundColorNew;?>!important" class='font-weight-bold' target='_BLANK' href='<?=base_url()?>Reports/TeamPlannerReportLists/total_reject/<?=$row->user_id?>/<?=$sdate?>/<?=$edate?>/<?=$task_action_id?>/userwise'>
                                <?=$row->total_reject?>
                                </a>
                              </td>
                              <td>
                                <a style="color:<?=$backgroundColorNew;?>!important" class='font-weight-bold' target='_BLANK' href='<?=base_url()?>Reports/TeamPlannerReportLists/total_deleted_task/<?=$row->user_id?>/<?=$sdate?>/<?=$edate?>/<?=$task_action_id?>/userwise'>
                                <?=$row->total_deleted_task?>
                                </a>
                              </td>
                              <td>
                                <a style="color:<?=$backgroundColorNew;?>!important" class='font-weight-bold' target='_BLANK' href='<?=base_url()?>Reports/TeamPlannerReportLists/pending_for_assign_after_reject_task/<?=$row->user_id?>/<?=$sdate?>/<?=$edate?>/<?=$task_action_id?>/userwise'>
                                <?=$row->pending_for_assign_after_reject_task?>
                                </a>
                              </td>
                              <td>
                                <a style="color:<?=$backgroundColorNew;?>!important" class='font-weight-bold' target='_BLANK' href='<?=base_url()?>Reports/TeamPlannerReportLists/admin_create_request_for_self_assign/<?=$row->user_id?>/<?=$sdate?>/<?=$edate?>/<?=$task_action_id?>/userwise'>
                                <?=$row->admin_create_request_for_self_assign?>
                                </a>
                              </td>
                              <td>
                                <a style="color:<?=$backgroundColorNew;?>!important" class='font-weight-bold' target='_BLANK' href='<?=base_url()?>Reports/TeamPlannerReportLists/task_assignd_by_admin_after_reject/<?=$row->user_id?>/<?=$sdate?>/<?=$edate?>/<?=$task_action_id?>/userwise'>
                                <?=$row->task_assignd_by_admin_after_reject?>
                                </a>
                              </td>
                              <td>
                                <a style="color:<?=$backgroundColorNew;?>!important" class='font-weight-bold' target='_BLANK' href='<?=base_url()?>Reports/TeamPlannerReportLists/task_assignd_by_user_after_reject/<?=$row->user_id?>/<?=$sdate?>/<?=$edate?>/<?=$task_action_id?>/userwise'>
                                <?=$row->task_assignd_by_user_after_reject?>
                                </a>
                              </td>
                              <td>
                                <a style="color:<?=$backgroundColorNew;?>!important" class='font-weight-bold' target='_BLANK' href='<?=base_url()?>Reports/TeamPlannerReportLists/user_complete_task/<?=$row->user_id?>/<?=$sdate?>/<?=$edate?>/<?=$task_action_id?>/userwise'>
                                <?=$row->user_complete_task?>
                                </a>
                              </td>
                              <td>
                                <a style="color:<?=$backgroundColorNew;?>!important" class='font-weight-bold' target='_BLANK' href='<?=base_url()?>Reports/TeamPlannerReportLists/user_pending_for_complete_task/<?=$row->user_id?>/<?=$sdate?>/<?=$edate?>/<?=$task_action_id?>/userwise'>
                                <?=$row->user_pending_for_complete_task?>
                                </a>
                              </td>
                              <td>
                                <a style="color:<?=$backgroundColorNew;?>!important" class='font-weight-bold' target='_BLANK' href='<?=base_url()?>Reports/TeamPlannerReportLists/after_task/<?=$row->user_id?>/<?=$sdate?>/<?=$edate?>/<?=$task_action_id?>/userwise'>
                                <?=$row->after_task?>
                                </a>
                              </td>
                              <td>
                                <a style="color:<?=$backgroundColorNew;?>!important" class='font-weight-bold' target='_BLANK' href='<?=base_url()?>Reports/TeamPlannerReportLists/complete_autotask/<?=$row->user_id?>/<?=$sdate?>/<?=$edate?>/<?=$task_action_id?>/userwise'>
                                <?=$row->complete_autotask?>
                                </a>
                              </td>
                              <td>
                                <a style="color:<?=$backgroundColorNew;?>!important" class='font-weight-bold' target='_BLANK' href='<?=base_url()?>Reports/TeamPlannerReportLists/pending_for_complete_autotask/<?=$row->user_id?>/<?=$sdate?>/<?=$edate?>/<?=$task_action_id?>/userwise'>
                                <?=$row->pending_for_complete_autotask?>
                                </a>
                              </td>
                             
                              <?php $i++; } ?>
                            </tbody>
                          </table>
                        </div>
            <hr>












              <div class="text-center" style="background: linear-gradient(to right, #ede7f6, #ffffff); border-radius: 12px; box-shadow: 0 4px 8px rgba(0,0,0,0.1); padding: 20px;">
            <h5><i>📊 User Activity and Attendance Report</i></h5>
                <small>
                📅 This Data represents a comprehensive report on user activity and attendance metrics. 📅 It includes details such as the report date, total number of users, present and absent users, and their work locations categorized into office 🏢, field 🌾, and field office 🏡. Additionally, it tracks planning activities, distinguishing between same-day and previous-day planning, as well as instances where planning was not set. 📝 The report also captures scenarios where planning started on a previous day but tasks were created on the same day, providing insights into user productivity and planning efficiency. 📈
                </small>
            </div>

            <div class="table-responsive text-nowrap card each">
                          <table class="table table-striped table-bordered" id="example10" style="background: linear-gradient(to right, #e8f5e9, #ffffff); border-radius: 12px; box-shadow: 0 4px 8px rgba(0,0,0,0.1); padding: 20px;">
                            <thead class="thead-dark">
                              <tr class="text-center">
                                  <th>#</th>
                                  <th>📅 Report Date</th>
                                  <th>👥 Total Active User</th>
                                  <th>👤 Total Present User</th>
                                  <!-- <th>👤 Total Users Who Were Present & Closed Their Day</th>
                                  <th>👤 Total Users Who Were Present & Not Closed Their Day</th> -->
                                  <th>👤 Total Absent User</th>
                                  <th>🏢 Total Work From Office</th>
                                  <th>🌾 Total Work From Field</th>
                                  <th>🏢🌾 Total Work From Field + Office</th>
                                  <th>📝 Total Planner Set</th>
                                  <th>🔄 Same Day Planning</th>
                                  <th>🔙 Previous Day Planning</th>
                                  <th>❌ Planner Not Set</th>
                                  <th>🔄 Started planning previous day but still created request on same day</th>
                              </tr>
                            </thead>
                            <tbody>
                            <?php
                              $i = 1;
                              foreach ($plannerReportDatas as $row) {

                                $r = rand(150, 255);
                                $g = rand(150, 255);
                                $b = rand(150, 255);
                                $backgroundColor = "rgba($r, $g, $b,.2)";
                                $hue = rand(0, 360); // Full color wheel
                                $saturation = rand(60, 100); // High saturation for rich colors
                                $lightness = rand(30, 45); // Low lightness for a deeper tone
                                $backgroundColorNew = "hsl($hue, $saturation%, $lightness%)";
                                ?>
                            <tr style="background-color: <?php echo $backgroundColor; ?>;color:<?=$backgroundColorNew;?>!important" class="text-center">
                              <td><?= $i; ?></td>
                              <td class="text-capitalize"><?php echo htmlspecialchars($row->report_date); ?></td>
                              <td class="text-capitalize">
                                <a style="color:<?=$backgroundColorNew;?>!important" href="<?=base_url()?>/Reports/PlannerReportDataBYDate/<?=$uid?>/<?=$row->report_date?>/<?=$row->report_date?>/total_user" target="_blank">
                                  <?php echo htmlspecialchars($row->total_user); ?>
                                </a>
                            </td>

                              <td class="text-capitalize">
                                <a style="color:<?=$backgroundColorNew;?>!important" href="<?=base_url()?>/Reports/PlannerReportDataBYDate/<?=$uid?>/<?=$row->report_date?>/<?=$row->report_date?>/total_present_user" target="_blank">
                                  <?php echo htmlspecialchars($row->total_present_user); ?>
                                   </a>
                              </td>
                                <?php /*
                              <td class="text-capitalize">
                                <a style="color: inherit;" href="<?= base_url() ?>/Reports/PlannerReportDataBYDate/<?= $uid ?>/<?= $row->report_date ?>/<?= $row->report_date ?>/total_present_and_closed_day_user" target="_blank"><?= htmlspecialchars($row->total_present_and_closed_day_user); ?></a>
                              </td>
                              <td class="text-capitalize">
                                <a style="color: inherit;" href="<?= base_url() ?>/Reports/PlannerReportDataBYDate/<?= $uid ?>/<?= $row->report_date ?>/<?= $row->report_date ?>/total_present_and_not_closed_day_user" target="_blank"><?= htmlspecialchars($row->total_present_and_not_closed_day_user); ?></a>
                              </td>
                              */ ?>
                               
                              <td class="text-capitalize">
                                <a style="color:<?=$backgroundColorNew;?>!important" href="<?=base_url()?>/Reports/PlannerReportDataBYDate/<?=$uid?>/<?=$row->report_date?>/<?=$row->report_date?>/total_absent_user" target="_blank">
                                  <?php echo htmlspecialchars($row->total_absent_user); ?>
                                </a>
                              </td>

                            
                              <td class="text-capitalize">
                                <a style="color:<?=$backgroundColorNew;?>!important" href="<?=base_url()?>/Reports/PlannerReportDataBYDate/<?=$uid?>/<?=$row->report_date?>/<?=$row->report_date?>/total_work_from_office" target="_blank">
                                  <?php echo htmlspecialchars($row->total_work_from_office); ?>
                                </a>
                                </td>
                              <td class="text-capitalize">
                                <a style="color:<?=$backgroundColorNew;?>!important" href="<?=base_url()?>/Reports/PlannerReportDataBYDate/<?=$uid?>/<?=$row->report_date?>/<?=$row->report_date?>/total_work_from_field" target="_blank">
                                  <?php echo htmlspecialchars($row->total_work_from_field); ?>
                                </a>
                                </td>
                              <td class="text-capitalize">
                                <a style="color:<?=$backgroundColorNew;?>!important" href="<?=base_url()?>/Reports/PlannerReportDataBYDate/<?=$uid?>/<?=$row->report_date?>/<?=$row->report_date?>/total_work_from_field_office" target="_blank">
                                  <?php echo htmlspecialchars($row->total_work_from_field_office); ?>
                                </a>
                                </td>
                              <td class="text-capitalize">
                                <a style="color:<?=$backgroundColorNew;?>!important" href="<?=base_url()?>/Reports/PlannerReportDataBYDate/<?=$uid?>/<?=$row->report_date?>/<?=$row->report_date?>/total_planning_set" target="_blank">
                                  <?php echo htmlspecialchars($row->total_planning_set); ?>
                                </a>
                                </td>
                              <td class="text-capitalize">
                                <a style="color:<?=$backgroundColorNew;?>!important" href="<?=base_url()?>/Reports/PlannerReportDataBYDate/<?=$uid?>/<?=$row->report_date?>/<?=$row->report_date?>/same_day_planning" target="_blank">
                                  <?php echo htmlspecialchars($row->same_day_planning); ?>
                                </a>
                              </td>
                              <td class="text-capitalize">
                                <a style="color:<?=$backgroundColorNew;?>!important" href="<?=base_url()?>/Reports/PlannerReportDataBYDate/<?=$uid?>/<?=$row->report_date?>/<?=$row->report_date?>/previous_day_planning" target="_blank">
                                  <?php echo htmlspecialchars($row->previous_day_planning); ?>
                                </a>
                                </td>
                              <td class="text-capitalize">
                                <a style="color:<?=$backgroundColorNew;?>!important" href="<?=base_url()?>/Reports/PlannerReportDataBYDate/<?=$uid?>/<?=$row->report_date?>/<?=$row->report_date?>/planner_not_set" target="_blank">
                                  <?php echo htmlspecialchars($row->planner_not_set); ?>
                                </a>
                                </td>
                              <td class="text-capitalize">
                                <a style="color:<?=$backgroundColorNew;?>!important" href="<?=base_url()?>/Reports/PlannerReportDataBYDate/<?=$uid?>/<?=$row->report_date?>/<?=$row->report_date?>/spdplanning_then_create_same_day_request" target="_blank">
                                  <?php echo htmlspecialchars($row->spdplanning_then_create_same_day_request); ?>
                                </a>
                              </td>
                             </tr>
                              <?php $i++; } ?>
                            </tbody>
                          </table>
                        </div>
            <hr>




              <div class="text-center" style="background: linear-gradient(to right, #ede7f6, #ffffff); border-radius: 12px; box-shadow: 0 4px 8px rgba(0,0,0,0.1); padding: 20px;">
            <h5><i>✅ User Daily Planner Request </i></h5>
                <small>
               📋 This section provides a detailed summary of user-submitted planner reports that have been ✅ approved. 🔍 Review the planned tasks 🗓️, their scheduled dates ⏰, approval status 🟢, and user-level activity insights 👤. 📈 Stay on top of task progress and ensure timely execution 🚀 of approved plans.
                </small>
            </div>

            <div class="table-responsive text-nowrap card each">
                          <table class="table table-striped table-bordered" id="example14" style="background: linear-gradient(to right, #e8f5e9, #ffffff); border-radius: 12px; box-shadow: 0 4px 8px rgba(0,0,0,0.1); padding: 20px;">
                            <thead class="thead-dark">
                             <tr class="text-center">
                                <th>#️⃣</th>
                                <th>📅 Request Date</th>
                                <th>✅ Request Request</th>
                                <th>✅ Request Approved</th>
                                <th>⏳ Request Pending</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php
                              $i = 1;
                              foreach ($teamDailyPlannedByUser as $row) {

                                $r = rand(150, 255);
                                $g = rand(150, 255);
                                $b = rand(150, 255);
                                $backgroundColor = "rgba($r, $g, $b,.2)";
                                $hue = rand(0, 360); // Full color wheel
                                $saturation = rand(60, 100); // High saturation for rich colors
                                $lightness = rand(30, 45); // Low lightness for a deeper tone
                                $backgroundColorNew = "hsl($hue, $saturation%, $lightness%)";

                                ?>

                            <tr style="background-color: <?php echo $backgroundColor; ?>;color:<?=$backgroundColorNew;?>!important" class="text-center">
                              
                              <td><?= $i;?></td>
                              <td class="text-capitalize"><?php echo htmlspecialchars($row->request_date); ?></td>
                              <td class="text-capitalize">
                                <a style="color:<?=$backgroundColorNew;?>!important" href="<?=base_url()?>/Reports/PlannerApprovedReportDataBYDate/<?=$uid?>/<?=$row->request_date?>/<?=$row->request_date?>/total_request" target="_blank">
                                  <?php echo htmlspecialchars($row->total_request); ?>
                                </a>
                              </td>
                              <td class="text-capitalize">
                                <a style="color:<?=$backgroundColorNew;?>!important" href="<?=base_url()?>/Reports/PlannerApprovedReportDataBYDate/<?=$uid?>/<?=$row->request_date?>/<?=$row->request_date?>/total_approved" target="_blank">
                                  <?php echo htmlspecialchars($row->total_approved); ?>
                                </a>
                              </td>
                              <td class="text-capitalize">
                                <a style="color:<?=$backgroundColorNew;?>!important" href="<?=base_url()?>/Reports/PlannerApprovedReportDataBYDate/<?=$uid?>/<?=$row->request_date?>/<?=$row->request_date?>/total_pending" target="_blank">
                                  <?php echo htmlspecialchars($row->total_pending); ?>
                                </a>
                              </td>

                             </tr>
                              <?php $i++; } ?>
                            </tbody>
                          </table>
                        </div>
            <hr>





            <div class="body-content">
              <div class="card-body each">
                <div class="body-content">
                  <div class="page-header">
                    <?php
                      $data = $groupedData;
                      $data1 = $groupedData;
                      ?>
                    <fieldset>
                      <div class="card" >
                      <div class="text-center" style="background: linear-gradient(to right, #ede7f6, #ffffff); border-radius: 12px; box-shadow: 0 4px 8px rgba(0,0,0,0.1); padding: 20px;">
                      <h5><i>🗂️🗓️ Team Task Planner Reports</i></h5>
                        <small>
                        🗂️🗓️ This section provides a comprehensive overview of team and individual task planning. 📅 It enables users to track assigned, completed, and pending tasks within a selected timeframe. 📊 Category-wise performance insights help evaluate progress, identify gaps, and optimize planning strategies. 📋 Ideal for managers and team leads to monitor execution, ensure accountability, and drive productivity through data-driven decisions and real-time task visibility. ✅
                        </small>
                        </div>
                        <br>
                        <div class="table-responsive text-nowrap card each">
                          <table class="table table-striped table-bordered" id="example1" style="background: linear-gradient(to right, #e8f5e9, #ffffff); border-radius: 12px; box-shadow: 0 4px 8px rgba(0,0,0,0.1); padding: 20px;">
                            <thead class="thead-dark">
                              <tr>
                                <th>#</th>
                                <th>Full Name</th>
                                <th>Total Task</th>
                                <?php
                                  // Extract unique task types
                                  $taskTypes = array();
                                  foreach ($data as $userTasks) {
                                    foreach ($userTasks as $task) {
                                      $taskTypes[$task->tasktype] = true;
                                    }
                                  }
                                  // Print unique task types as table headers
                                  foreach (array_keys($taskTypes) as $taskType) {
                                    echo "<th>$taskType</th>";
                                  }
                                  ?>
                                <th>Total Task Planned Time</th>
                              </tr>
                            </thead>
                            <tbody>
                              <?php
                                // Initialize task count array for each user
                                $taskCounts = array();
                                foreach ($data as $userId => $userTasks) {
                                  foreach ($userTasks as $task) {
                                    $taskCounts[$userId][$task->tasktype] = (isset($taskCounts[$userId][$task->tasktype])) ? $taskCounts[$userId][$task->tasktype] + $task->task_count : $task->task_count;
                                  }
                                }
                                $i = 1;
                                // Print table rows
                                foreach ($groupedData as $userIdPlanner => $userTasksPlanner) {
                                  $userPallnerData = $userTasksPlanner[0];
                                  foreach ($userTasksPlanner as $userTasksPlanners) {
                                    if (!empty($userTasksPlanners->total_review_plan_time)) {
                                         $total_review_plan_time = $userTasksPlanners->total_review_plan_time; 
                                    }else{
                                        $total_review_plan_time = '';
                                    }
                                }
                                  echo "<tr>";
                                  echo "<td style='color:rgb(14, 3, 228) !important;'>" . $i . "</td>";
                                  echo "<td style='color: #e403d9 !important;'>" . htmlspecialchars($userPallnerData->name) . "</td>";
                                
                                   echo "<td><a class='font-weight-bold' target='_BLANK' href='" . base_url() . "Reports/TeamPlannerReportLists/total_task_count/$userPallnerData->planner_user_id/$sdate/$edate/all/userwise'>".htmlspecialchars($userPallnerData->total_task_count)."</a></td>";
                                  foreach (array_keys($taskTypes) as $taskType) {
                                     $task_type_id = $this->Report_model->GetTaskIDUsingTaskName($taskType)[0]->id;
                                
                                      echo "<td><a class='font-weight-bold' target='_BLANK' href='" . base_url() . "Reports/TeamPlannerReportLists/Check_task_type/$userPallnerData->planner_user_id/$sdate/$edate/$task_type_id/userwise'>".
                                      (isset($taskCounts[$userIdPlanner][$taskType]) ? "<span class='text-success font-weight-bold'>" . $taskCounts[$userIdPlanner][$taskType] . "</span>" : "<span class='text-warning font-weight-bold'>0</span>")
                                      ."</a></td>";
                                
                                  }
                                
                                  $total_plan_task_time_data = !empty($userPallnerData->total_plan_task_time) ? $userPallnerData->total_plan_task_time : 'N/A';
                                  if($total_plan_task_time_data !== 'N/A'){
                                    preg_match('/(\d+)\s+hours/', $userPallnerData->total_plan_task_time, $matches);
                                    $hours_colors = isset($matches[1]) ? (int)$matches[1] : 0;
                                    $hours_colors_text = $hours_colors >= 6 ? 'text-success' : 'text-danger';
                                    echo "<td class='" . $hours_colors_text . " font-weight-bold'>" . htmlspecialchars($userPallnerData->total_plan_task_time) . "</td>";
                                  }else{
                                    echo "<td class='font-weight-bold'>" . htmlspecialchars($total_plan_task_time_data) . "</td>";
                                  }
                                  echo "</tr>";
                                  $i++;
                                }
                                ?>
                            </tbody>
                          </table>
                        </div>
                      </div>
                    </fieldset>
                  </div>
                </div>
              </div>

              <?php 
              $totalTasksUserByDatas  = $this->Report_model->GetTeamWisePlanedTaskTypeUsingPlannerDataLists($uid,$sdate,$edate,$mtypes,$uid,$task_action_id,$userwise);
              $task_counts = [];
              $filterused = [];
              $selectbyFilter = [];
        foreach ($totalTasksUserByDatas as $row) {
            $task_name = $row->task_name;
        
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
                    }
                    else if ($row->reviewtype !='' && !is_int($row->reviewtype)){
                          if (!isset($selectbyFilter[$row->reviewtype])) {
                                $selectbyFilter[$row->reviewtype] = 1;
                            } else {
                                $selectbyFilter[$row->reviewtype]++;
                          }
                            //  echo "other = ".$reviewtype.' - Task ID : '.$row->task_id.'<br/>';
                    }
                    else if ($row->comments !='' && !is_null($row->comments)){
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
                      //  echo "other = ".$selectby.' - Task ID : '.$row->task_id.'<br/>';
                    }
        








               if (!empty($filter_by) && is_array($filter_by)) {

                  foreach ($filter_by as $key => $value) {

                      if ($key == 'Plan_BY' || $key == 'Filter_By') {

                          $filterused[$value] = ($filterused[$value] ?? 0) + 1;

                      } elseif ($key == 'comp_status') {

                          $tstatus = $this->Menu_model->get_statusbyid($value);
                          $tstatusname = $tstatus[0]->name ?? '';

                          if ($tstatusname !== '') {
                              $filterused[$tstatusname] = ($filterused[$tstatusname] ?? 0) + 1;
                          }

                      } elseif ($key == 'task') {

                          $taction = $this->Menu_model->get_actionbyid($value);
                          $tactionname = $taction[0]->name ?? '';

                          if ($tactionname !== '') {
                              $filterused[$tactionname] = ($filterused[$tactionname] ?? 0) + 1;
                          }

                      } elseif ($key == 'taskActionbyuser') {

                          $filterused[$value] = ($filterused[$value] ?? 0) + 1;

                      } else {

                          if (!empty($selectby)) {

                              $filterused[$selectby] = ($filterused[$selectby] ?? 0) + 1;

                          } elseif (!empty($row->comments)) {

                              $filterused[$row->comments] = ($filterused[$row->comments] ?? 0) + 1;

                          } else {

                              $filterused[$reviewtype] = ($filterused[$reviewtype] ?? 0) + 1;
                          }
                      }
                  }
              }
















                      }
                      
                      
                     //  dd($selectbyFilter);
                      // dd($filterused);

                      ?>



      
              <div class="taskcountcard">
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
                    $index = 0;
                    ?>
                  <div class="card shadow-sm border-0 rounded-4 mb-4">
                    <div class="card-header text-center" style="background: linear-gradient(to right, #f6e7f3, #ffffff); border-radius: 12px; box-shadow: 0 4px 8px rgba(0,0,0,0.1); padding: 20px;">
                      <h5 class="mb-0">📝 Planner Task Activity Summary</h5> 
                      <small class="d-block">📊 Overview of your engagement and planning activities</small>
                    </div>
                    <div class="card-body">
                      <div class="row">
                        <div class="col-md-3 mb-3">
                          <div class="rounded p-3" style="background-color: #abf58f;box-shadow: rgba(9, 30, 66, 0.25) 0px 4px 8px -2px, rgba(9, 30, 66, 0.08) 0px 0px 0px 1px;">
                            <h6 class="mb-1 font-weight-bold text-center">Total Task</h6>
                            <hr>
                            <div class="text-center">
                              <span class="badge badge-pill badge-dark" style="font-size: 1.1rem;"><?= sizeof($totalTasksUserByDatas) ?></span>
                            </div>
                          </div>
                        </div>
                        <?php foreach ($task_counts as $label => $value): ?>
                        <?php
                          $bgColor = $colors[$index % count($colors)];
                          $desc = isset($descriptions[$label]) ? $descriptions[$label] : '';
                          $index++;
                          ?>
                        <div class="col-md-3 mb-3">
                          <div class="rounded p-3" style="background-color: <?= $bgColor ?>;box-shadow: rgba(9, 30, 66, 0.25) 0px 4px 8px -2px, rgba(9, 30, 66, 0.08) 0px 0px 0px 1px;">
                            <h6 class="mb-1 font-weight-bold text-center"><?= $label ?></h6>
                            <hr>
                            <div class="text-center">
                              <span class="badge badge-pill badge-dark" style="font-size: 1.1rem;"><?= $value ?></span>
                            </div>
                          </div>
                        </div>
                        <?php endforeach; ?>
                      </div>
                    </div>
                  </div>
                </div>


                <hr>


                 <div class="taskcountcard">
                  <?php
                    //  dd($selectbyFilter);
                      // dd($filterused);
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
                    $index = 0;
                    ?>
                  <div class="card shadow-sm border-0 rounded-4 mb-4">
                    <div class="card-header text-center" style="background: linear-gradient(to right, #f6e7f3, #ffffff); border-radius: 12px; box-shadow: 0 4px 8px rgba(0,0,0,0.1); padding: 20px;">
                      <h5 class="m-0 premium-color-1" style="color: #ff009b;">🧩🔍 Total Filters Used for Plan Tasks</h5> 
                    <small class="m-2">
                      The "Total Filters Used for Plan Tasks" metric represents the number of filters applied by users while organizing, viewing, or managing planned tasks. 📅 This metric helps assess how users interact with task planning features and how frequently they narrow down data to find specific insights. 🎯 By analyzing filter usage, organizations can identify user preferences, improve UI/UX for planning tools, and optimize task management workflows. ⚙️ It also supports smarter decision-making by revealing how users refine and explore their task data. 📊
                    </small>
                    </div>
                    <div class="card-body">
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
                    </div>
                  </div>
                </div>







              <hr>
              <div class="card">
              <div class="text-center" style="background: linear-gradient(to right, #ede7f6, #ffffff); border-radius: 12px; box-shadow: 0 4px 8px rgba(0,0,0,0.1); padding: 20px;">
              <h5>
              <i>📅 Review Summary</i><br>
              <small>
              🧾 This section provides a breakdown of the total number of reviews handled by the user. It distinguishes between ✍️ self-created reviews and 📤 reviews assigned to others. 📊 This helps analyze user engagement, accountability, and team collaboration. Tracking both self and delegated reviews reveals patterns in contributions and leadership behavior, offering insights into individual performance and responsibility within the system. ✅ Useful for performance reviews and team evaluations.
              </small>
                </h5>
              </div>
              <hr>
              <div class="page-header" style="background: linear-gradient(to right, #ffffff, #ffffff); border-radius: 12px; box-shadow: 0 4px 8px rgba(0,0,0,0.1); padding: 20px;">
                <div class="table-responsive text-nowrap">
                  <table id="example7" class="table table-striped table-bordered" cellspacing="0" width="100%">
                    <thead class="thead-dark">
                        <tr>
                            <th>🔢 Sr.No</th>
                            <th>👤 BD Name</th>
                            <th>📝 Total Review</th>
                            <th>🙋 For Himself Review</th>
                            <th>👥 For Others Review</th>
                        </tr>
                    </thead>
                    <tbody>
                      <?php
                        $i = 1;
                        foreach ($reviewPlannedByUser as $row) {
                          $meeting_user_id = $row->user_id;
                        
                          $r = rand(150, 255);
                          $g = rand(150, 255);
                          $b = rand(150, 255);
                          $backgroundColor = "rgba($r, $g, $b,.2)";
                          $hue = rand(0, 360); // Full color wheel
                          $saturation = rand(60, 100); // High saturation for rich colors
                          $lightness = rand(30, 45); // Low lightness for a deeper tone
                          $backgroundColorNew = "hsl($hue, $saturation%, $lightness%)";
                          ?>
                      <tr style="background-color: <?php echo $backgroundColor; ?>;color:<?=$backgroundColorNew;?>!important">
                        <td><?= $i; ?></td>
                        <td class="text-capitalize"><?php echo htmlspecialchars($row->username); ?></td>
                        <td>
                          <a target="_BLANK" class="card-count-heading-new" style="color:<?=$backgroundColorNew;?>!important" href="<?=base_url()?>Reports/UserRequestDetails/total_review_count/<?=$meeting_user_id?>/<?=$sdate?>/<?=$edate?>/<?=$task_action_id?>/userwise">
                          <?= $row->total_review_count; ?>
                          </a>
                        </td>
                        <td>
                          <a target="_BLANK" class="card-count-heading-new" style="color:<?=$backgroundColorNew;?>!important" href="<?=base_url()?>Reports/UserRequestDetails/for_him_self_review/<?=$meeting_user_id?>/<?=$sdate?>/<?=$edate?>/<?=$task_action_id?>/userwise">
                          <?= $row->for_him_self_review; ?>
                          </a>
                        </td>
                        <td>
                          <a target="_BLANK" class="card-count-heading-new" style="color:<?=$backgroundColorNew;?>!important" href="<?=base_url()?>Reports/UserRequestDetails/for_other_review/<?=$meeting_user_id?>/<?=$sdate?>/<?=$edate?>/<?=$task_action_id?>/userwise">
                          <?= $row->for_other_review; ?>
                          </a>
                        </td>
                      </tr>
                      <?php $i++; } ?>
                    </tbody>
                  </table>
                </div>
              </div>
              </div>


              <hr>
              <div class="card">
              <div class="text-center" style="background: linear-gradient(to right, #ede7f6, #ffffff); border-radius: 12px; box-shadow: 0 4px 8px rgba(0,0,0,0.1); padding: 20px;">
              <h5>
                <i>⏱️ Time Consumed on Planning</i><br>
                <small>
                  🕒 This section focuses on the time duration between when a task was planned and when it actually began or showed measurable progress. It helps identify delays, inefficiencies, or bottlenecks in execution after planning. 📊 Tasks that remain in a planned state without movement indicate possible oversight, resource unavailability, or misalignment in priorities. 🔍 Managers can use this insight to follow up, improve planning accuracy, and ensure timely task initiation. 🚀 Essential for optimizing workflow responsiveness and accountability.
                </small>
              </h5>
              </div>
              <hr>
              <div class="page-header" style="background: linear-gradient(to right, #ffffff, #ffffff); border-radius: 12px; box-shadow: 0 4px 8px rgba(0,0,0,0.1); padding: 20px;">
                <div class="table-responsive text-nowrap">
                  <table id="example6" class="table table-striped table-bordered" cellspacing="0" width="100%">
                    <thead class="thead-dark">
                        <tr>
                            <th>🔢 Sr.No</th>
                            <th>👤 BD Name</th>
                            <th>⏲️ Session Count</th>
                            <th>⏱️ Time Consume on Planning</th>
                        </tr>
                    </thead>
                    <tbody>
                      <?php
                        $i = 1;
                        foreach ($userplanningTimeByUser as $row) {
                          $meeting_user_id = $row->user_id;
                        
                          $r = rand(150, 255);
                          $g = rand(150, 255);
                          $b = rand(150, 255);
                          $backgroundColor = "rgba($r, $g, $b,.2)";
                        
                          $hue = rand(0, 360); // Full color wheel
                          $saturation = rand(60, 100); // High saturation for rich colors
                          $lightness = rand(30, 45); // Low lightness for a deeper tone
                          
                          $backgroundColorNew = "hsl($hue, $saturation%, $lightness%)";
                          
                          ?>
                      <tr style="background-color: <?php echo $backgroundColor; ?>;color:<?=$backgroundColorNew;?>!important">
                        <td><?= $i; ?></td>
                        <td class="text-capitalize"><?php echo htmlspecialchars($row->username); ?></td>
                        <td class="text-capitalize"><?php echo htmlspecialchars($row->session_count); ?></td>
                        <td>
                          <a target="_BLANK" class="card-count-heading-new" style="color:<?=$backgroundColorNew;?>!important" href="<?=base_url()?>Reports/UserRequestDetails/total_time/<?=$meeting_user_id?>/<?=$sdate?>/<?=$edate?>/<?=$task_action_id?>/userwise">
                          <?= $row->total_time; ?>
                          </a>
                        </td>
                      </tr>
                      <?php $i++; } ?>
                    </tbody>
                  </table>
                </div>
              </div>
              </div>

        
              <hr>
              <div class="card">
              <div class="text-center" style="background: linear-gradient(to right, #ede7f6, #ffffff); border-radius: 12px; box-shadow: 0 4px 8px rgba(0,0,0,0.1); padding: 20px;">
              <h5>
                <i>📅 Same Day Planning By User</i><br>
                <small>📅 This section highlights the tasks that were both planned and completed by the user on the same day. It helps track timely execution and reflects the user's responsiveness and efficiency. 🔍 By analyzing these entries, managers can assess short-term planning effectiveness and identify patterns in quick task closures, enabling better support, feedback, and recognition for proactive work behavior. ✅ Ideal for performance and planning alignment insights.</small>
                </h5>
              </div>
              <hr>
              <div class="page-header" style="background: linear-gradient(to right, #ffffff, #ffffff); border-radius: 12px; box-shadow: 0 4px 8px rgba(0,0,0,0.1); padding: 20px;">
                <div class="table-responsive text-nowrap">
                  <table id="example2" class="table table-striped table-bordered" cellspacing="0" width="100%">
                    <thead class="thead-dark">
                        <tr>
                            <th>🔢 Sr.No</th>
                            <th>👤 BD Name</th>
                            <th>📅 Same Day Planning</th>
                        </tr>
                    </thead>
                    <tbody>
                      <?php
                        $i = 1;
                        foreach ($sameDaysPlanningByUser as $row) {
                          $meeting_user_id = $row->user_id;
                        
                          $r = rand(150, 255);
                          $g = rand(150, 255);
                          $b = rand(150, 255);
                          $backgroundColor = "rgba($r, $g, $b,.2)";
                        
                          $hue = rand(0, 360); // Full color wheel
                          $saturation = rand(60, 100); // High saturation for rich colors
                          $lightness = rand(30, 45); // Low lightness for a deeper tone
                          
                          $backgroundColorNew = "hsl($hue, $saturation%, $lightness%)";
                          
                          ?>
                      <tr style="background-color: <?php echo $backgroundColor; ?>;color:<?=$backgroundColorNew;?>!important">
                        <td><?= $i; ?></td>
                        <td class="text-capitalize"><?php echo htmlspecialchars($row->username); ?></td>
                        <td>
                          <a target="_BLANK" class="card-count-heading-new" style="color:<?=$backgroundColorNew;?>!important" href="<?=base_url()?>Reports/UserRequestDetails/same_day_planning/<?=$meeting_user_id?>/<?=$sdate?>/<?=$edate?>/<?=$task_action_id?>/userwise">
                          <?= $row->same_day_planning; ?>
                          </a>
                        </td>
                      </tr>
                      <?php $i++; } ?>
                    </tbody>
                  </table>
                </div>
              </div>
              </div>
              <hr>

            

              <hr>
              <div class="card">
              <div class="text-center" style="background: linear-gradient(to right, #ede7f6, #ffffff); border-radius: 12px; box-shadow: 0 4px 8px rgba(0,0,0,0.1); padding: 20px;">
              <h5>
                <i>📅 ⏳ Pending Task Plan Request</i><br>
                <small>📅 ⏳ This section lists tasks that users have submitted for planning but are still awaiting approval or finalization. 📝 It highlights pending actions that need review by managers or approvers. These requests represent planned intent but require confirmation before execution. 🔄 Useful for monitoring workflow bottlenecks and ensuring timely planning decisions, this view helps teams stay aligned and avoid delays in task execution. 🚦</small>
                </h5>
              </div>
              <hr>
              <div class="page-header" style="background: linear-gradient(to right, #ffffff, #ffffff); border-radius: 12px; box-shadow: 0 4px 8px rgba(0,0,0,0.1); padding: 20px;">
                <div class="table-responsive text-nowrap">
                  <table id="example3" class="table table-striped table-bordered" cellspacing="0" width="100%">
                    <thead class="thead-dark">
                        <tr>
                            <th>🔢 Sr.No</th>
                            <th>👤 BD Name</th>
                            <th>⏳ Pending Task Plan Request</th>
                            <th>🛑 Plan But Not Initiated</th>
                            <th>⏳ Old Pending Task</th>
                        </tr>
                    </thead>
                    <tbody>
                      <?php
                        $i = 1;
                        foreach ($pendingPlanningByUser as $row) {
                          $meeting_user_id = $row->user_id;
                        
                          $r = rand(150, 255);
                          $g = rand(150, 255);
                          $b = rand(150, 255);
                          $backgroundColor = "rgba($r, $g, $b,.2)";
                        
                          $hue = rand(0, 360); // Full color wheel
                          $saturation = rand(60, 100); // High saturation for rich colors
                          $lightness = rand(30, 45); // Low lightness for a deeper tone
                          
                          $backgroundColorNew = "hsl($hue, $saturation%, $lightness%)";
                          
                          ?>
                      <tr style="background-color: <?php echo $backgroundColor; ?>;color:<?=$backgroundColorNew;?>!important">
                        <td><?= $i; ?></td>
                        <td class="text-capitalize"><?php echo htmlspecialchars($row->username); ?></td>
                        <td>
                          <a target="_BLANK" class="card-count-heading-new" style="color:<?=$backgroundColorNew;?>!important" href="<?=base_url()?>Reports/UserRequestDetails/pending_task_planning_request/<?=$meeting_user_id?>/<?=$sdate?>/<?=$edate?>/<?=$task_action_id?>/userwise">
                          <?= $row->pending_task_planning_request; ?>
                          </a>
                        </td>
                        <td>
                          <a target="_BLANK" class="card-count-heading-new" style="color:<?=$backgroundColorNew;?>!important" href="<?=base_url()?>Reports/UserRequestDetails/plan_but_not_initiated_request/<?=$meeting_user_id?>/<?=$sdate?>/<?=$edate?>/<?=$task_action_id?>/userwise">
                          <?= $row->plan_but_not_initiated_request; ?>
                          </a>
                        </td>
                        <td>
                          <a target="_BLANK" class="card-count-heading-new" style="color:<?=$backgroundColorNew;?>!important" href="<?=base_url()?>Reports/UserRequestDetails/old_pending_task_request/<?=$meeting_user_id?>/<?=$sdate?>/<?=$edate?>/<?=$task_action_id?>/userwise">
                          <?= $row->old_pending_task_request; ?>
                          </a>
                        </td>
                      </tr>
                      <?php $i++; } ?>
                    </tbody>
                  </table>
                </div>
              </div>
              </div>





              <hr>
              <div class="card">
              <div class="text-center" style="background: linear-gradient(to right, #ede7f6, #ffffff); border-radius: 12px; box-shadow: 0 4px 8px rgba(0,0,0,0.1); padding: 20px;">
              <h5>
                <i>📅 🗑️ Meeting Task Delete Request</i><br>
                <small>📅 🗑️ This section displays user-submitted requests to delete scheduled meeting tasks. ❌ These tasks are often canceled, rescheduled, or deemed unnecessary. It provides visibility into meeting adjustments and helps managers review and validate the deletion rationale. 🔍 Ensuring accountability and minimizing missed communications, this view is crucial for maintaining accurate schedules and preventing clutter from outdated or redundant meeting entries. 🔄 Keeps planning data clean and relevant.</small>
                </h5>
              </div>
              <hr>
              <div class="page-header" style="background: linear-gradient(to right, #ffffff, #ffffff); border-radius: 12px; box-shadow: 0 4px 8px rgba(0,0,0,0.1); padding: 20px;">
                <div class="table-responsive text-nowrap">
                  <table id="example4" class="table table-striped table-bordered" cellspacing="0" width="100%">
                    <thead class="thead-dark">
                        <tr>
                            <th>🔢 Sr.No</th>
                            <th>👤 BD Name</th>
                            <th>⏳ Meeting Task Delete Request</th>
                        </tr>
                    </thead>
                    <tbody>
                      <?php
                        $i = 1;
                        foreach ($pendingMeetingsDeleteByUser as $row) {
                          $meeting_user_id = $row->user_id;
                        
                          $r = rand(150, 255);
                          $g = rand(150, 255);
                          $b = rand(150, 255);
                          $backgroundColor = "rgba($r, $g, $b,.2)";
                        
                          $hue = rand(0, 360); // Full color wheel
                          $saturation = rand(60, 100); // High saturation for rich colors
                          $lightness = rand(30, 45); // Low lightness for a deeper tone
                          
                          $backgroundColorNew = "hsl($hue, $saturation%, $lightness%)";
                          
                          ?>
                      <tr style="background-color: <?php echo $backgroundColor; ?>;color:<?=$backgroundColorNew;?>!important">
                        <td><?= $i; ?></td>
                        <td class="text-capitalize"><?php echo htmlspecialchars($row->username); ?></td>
                        <td>
                          <a target="_BLANK" class="card-count-heading-new" style="color:<?=$backgroundColorNew;?>!important" href="<?=base_url()?>Reports/UserRequestDetails/pending_meetings_task_request/<?=$meeting_user_id?>/<?=$sdate?>/<?=$edate?>/<?=$task_action_id?>/userwise">
                          <?= $row->pending_meetings_task_request; ?>
                          </a>
                        </td>
                       
                      </tr>
                      <?php $i++; } ?>
                    </tbody>
                  </table>
                </div>
              </div>
              </div>

        
              <hr>
              <div class="card">
              <div class="text-center" style="background: linear-gradient(to right, #ede7f6, #ffffff); border-radius: 12px; box-shadow: 0 4px 8px rgba(0,0,0,0.1); padding: 20px;">
              <h5>
              <i>📅 📝 Special Request for Leave</i><br>
              <small style="width: 70%;">📅 📝 This section captures special requests made by users to skip or cancel tasks due to personal leave, emergencies, or valid unavailability. 🗓️ It ensures that planned activities are adjusted responsibly without affecting overall workflow. ✅ Managers can review, approve, or reschedule tasks based on these requests, maintaining transparency and fairness in task assignments while accommodating genuine availability constraints. 🤝 Supports balanced workload and team coordination.</small>
                </h5>
              </div>
              <hr>
              <div class="page-header" style="background: linear-gradient(to right, #ffffff, #ffffff); border-radius: 12px; box-shadow: 0 4px 8px rgba(0,0,0,0.1); padding: 20px;">
                <div class="table-responsive text-nowrap">
                  <table id="example5" class="table table-striped table-bordered" cellspacing="0" width="100%">
                    <thead class="thead-dark">
                        <tr>
                            <th>🔢 Sr.No</th>
                            <th>👤 BD Name</th>
                            <th>📝 Special Request for Leave</th>
                        </tr>
                    </thead>
                    <tbody>
                      <?php
                        $i = 1;
                        foreach ($specialLeaveRequestByUser as $row) {
                          $meeting_user_id = $row->user_id;
                        
                          $r = rand(150, 255);
                          $g = rand(150, 255);
                          $b = rand(150, 255);
                          $backgroundColor = "rgba($r, $g, $b,.2)";
                        
                          $hue = rand(0, 360); // Full color wheel
                          $saturation = rand(60, 100); // High saturation for rich colors
                          $lightness = rand(30, 45); // Low lightness for a deeper tone
                          
                          $backgroundColorNew = "hsl($hue, $saturation%, $lightness%)";
                          
                          ?>
                      <tr style="background-color: <?php echo $backgroundColor; ?>;color:<?=$backgroundColorNew;?>!important">
                        <td><?= $i; ?></td>
                        <td class="text-capitalize"><?php echo htmlspecialchars($row->username); ?></td>
                        <td>
                          <a target="_BLANK" class="card-count-heading-new" style="color:<?=$backgroundColorNew;?>!important" href="<?=base_url()?>Reports/UserRequestDetails/special_request_for_leave_request/<?=$meeting_user_id?>/<?=$sdate?>/<?=$edate?>/<?=$task_action_id?>/userwise">
                          <?= $row->special_request_for_leave_request; ?>
                          </a>
                        </td>
                      </tr>
                      <?php $i++; } ?>
                    </tbody>
                  </table>
                </div>
              </div>
              </div>


              
            


              <br>
              <br>
              <br>
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
    <script>
      function Reject(mid,id,val){
             // alert(mid);
             // alert('#exampleModalCenterdata'+mid);
             $('#exampleModalCenterdata').modal('show');
             $('#rejectid').val(id);
         // $('#exampleModalCenterdata'+mid).modal('show');
         // $('#exampleModalCenterdata'+mid+' #rejectid').val(id);
         }
    </script>
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
      $("#example3").DataTable({
        "responsive": false, "lengthChange": false, "autoWidth": false,
        "buttons": ["csv", "excel", "pdf"]
      }).buttons().container().appendTo('#example3_wrapper .col-md-6:eq(0)');
      $("#example4").DataTable({
        "responsive": false, "lengthChange": false, "autoWidth": false,
        "buttons": ["csv", "excel", "pdf"]
      }).buttons().container().appendTo('#example4_wrapper .col-md-6:eq(0)');
      $("#example5").DataTable({
        "responsive": false, "lengthChange": false, "autoWidth": false,
        "buttons": ["csv", "excel", "pdf"]
      }).buttons().container().appendTo('#example5_wrapper .col-md-6:eq(0)');

      $("#example6").DataTable({
        "responsive": false, "lengthChange": false, "autoWidth": false,
        "buttons": ["csv", "excel", "pdf"]
      }).buttons().container().appendTo('#example6_wrapper .col-md-6:eq(0)');
      
      $("#example7").DataTable({
        "responsive": false, "lengthChange": false, "autoWidth": false,
        "buttons": ["csv", "excel", "pdf"]
      }).buttons().container().appendTo('#example7_wrapper .col-md-6:eq(0)');
      $("#example8").DataTable({
        "responsive": false, "lengthChange": false, "autoWidth": false,
        "buttons": ["csv", "excel", "pdf"]
      }).buttons().container().appendTo('#example8_wrapper .col-md-6:eq(0)');
      $("#example9").DataTable({
        "responsive": false, "lengthChange": false, "autoWidth": false,
        "buttons": ["csv", "excel", "pdf"]
      }).buttons().container().appendTo('#example9_wrapper .col-md-6:eq(0)');

      $("#example13").DataTable({
        "responsive": false, "lengthChange": false, "autoWidth": false,
        "buttons": ["csv", "excel", "pdf"]
      }).buttons().container().appendTo('#example13_wrapper .col-md-6:eq(0)');

      $("#example14").DataTable({
        "responsive": false, "lengthChange": false, "autoWidth": false,
        "buttons": ["csv", "excel", "pdf"]
      }).buttons().container().appendTo('#example14_wrapper .col-md-6:eq(0)');

      // $("#example10").DataTable({
      //   "responsive": false, "lengthChange": false, "autoWidth": false,
      //   "buttons": ["csv", "excel", "pdf"]
      // }).buttons().container().appendTo('#example10_wrapper .col-md-6:eq(0)');

      $("#example10").DataTable({
  responsive: false,
  lengthChange: false,
  autoWidth: false,
  buttons: [
    "csv",
    "excel",
    {
      extend: 'pdfHtml5',
      orientation: 'landscape',      // Landscape mode
      pageSize: 'A4',                // A4 size
      title: 'User Activity and Attendance Report',     // Optional title
      exportOptions: {
        columns: ':visible'          // Export only visible columns
      },
      customize: function (doc) {
        doc.styles.tableHeader.alignment = 'left';
        doc.styles.tableBodyEven.alignment = 'left';
        doc.styles.tableBodyOdd.alignment = 'left';
        doc.defaultStyle.fontSize = 8; // Adjust font size to fit table
      }
    }
  ]
}).buttons().container().appendTo('#example10_wrapper .col-md-6:eq(0)');



    </script>
    <script>
      document.addEventListener('DOMContentLoaded', function() {
        const cards = document.querySelectorAll('.card');
      
        cards.forEach(card => {
          const bgColor = window.getComputedStyle(card).backgroundColor;
          const rgb = bgColor.match(/\d+/g);
          const brightness = (rgb[0] * 299 + rgb[1] * 587 + rgb[2] * 114) / 1000;
      
          if (brightness > 128) {
            card.classList.add('text-dark');
          } else {
            card.classList.add('text-light');
          }
        });
      });



  $(document).ready(function () {
    $('#downloadPdf').click(function () {
      const { jsPDF } = window.jspdf;
      const doc = new jsPDF('p', 'mm', 'a4'); // portrait, millimeters, A4
      const content = document.querySelector('.content-wrapper');

      html2canvas(content, {
        scale: 2, // Higher scale for better quality
      }).then(canvas => {
        const imgData = canvas.toDataURL('image/png');
        const imgWidth = 210; // A4 width in mm
        const pageHeight = 297; // A4 height in mm
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
      });
    });
  });




    </script>
  </body>
</html>
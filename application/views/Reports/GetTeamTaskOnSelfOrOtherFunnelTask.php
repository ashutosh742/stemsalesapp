<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Task On Self Or Other Funnel Details | STEM APP | WebAPP</title>
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
                      <h1 class="m-0 premium-color-1" style="color: #ff009b;">Task Details</h1>
                      <p class="premium-color-2" style="color: #ff0000;">This section provides a comprehensive overview of all task details by user, including statistics on various types of call, email, meetings, etc their statuses, and other relevant details.</p>
                      <p class="premium-color-2" style="color: #2c00ee;"><strong><mark><?=$sdate ?> To <?=$edate ?></mark></strong></p>
                      <p class="premium-color-2" style="color: #2c00ee;"><strong>Task : <mark><?=$taskActionDatas ?></mark></strong></p>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <br>
            <div class="row mb-2">
              <hr>
              <div class="text-right-data text-center" style="background: linear-gradient(to right, #ffe0b2, #ffffff); border-radius: 12px; box-shadow: 0 4px 8px rgba(0,0,0,0.1); padding: 20px;">
                <?php 
                  $clusterPstDatas  = $this->Menu_model->GetAdminActiveTeam($user['user_id']);
                  $taskActions      = $this->Menu_model->get_action();
                  $userRoles        = $this->Menu_model->GetActiveDepartmentList($uid);
                  ?>
                <div class="col text-center formcenterData">
                  <div>
                    <hr>
                    <form action="<?=base_url()?>Reports/GetTeamTaskOnSelfOrOtherFunnelTask" method="post" class="mt-3">
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
                            <button type="submit" class="btn btn-primary p-1" style="margin-top: 36px; width: 100px;">Filter</button>
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
              $total_status               = $getTotalTasksStatus['total_status'];
              $user_wise_status           = $getTotalTasksStatus['user_wise_status'];
              $total_category             = $getTotalTasksStatus['total_category'];
              $user_category              = $getTotalTasksStatus['user_category'];
              
              $total_time                 = $getTotalTasksStatus['total_time'];
              $user_time                  = $getTotalTasksStatus['user_time'];
              
              $total_old_category         = $getTotalTasksStatus['total_old_category'];
              $user_user_old_categorytime = $getTotalTasksStatus['user_old_category'];

              $total_conversions          = $getTotalTasksStatus['total_conversions'];
              $user_wise_conversions      = $getTotalTasksStatus['user_wise_conversions'];

              $partnerWiseData_total      = $getTotalTasksStatus['partnerWiseData_total'];
              $partnerWiseData_user_wise  = $getTotalTasksStatus['partnerWiseData_user_wise'];

          


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

              
                  //  dd($user_wise_conversions); 
              ?>
            <div class="text-center" style="background: linear-gradient(to right, #ede7f6, #ffffff); border-radius: 12px; box-shadow: 0 4px 8px rgba(0,0,0,0.1); padding: 20px;">
              <h5><i>📝 Total Team Task Details</i></h5>
              <small>
                📊 This section provides detailed information about tasks being tracked or reviewed, divided into 👤 personal and 👥 team funnels with various subcategories for clear analysis.
              </small>
            </div>
            <hr>
           
              <?php  
                $total_task                              = $getTotalTasks[0]->total_task;
                $total_complete_task                     = $getTotalTasks[0]->total_complete_task;
                $total_pending_task                      = $getTotalTasks[0]->total_pending_task;
                $total_status_change_task                = $getTotalTasks[0]->total_status_change_task;

                $total_own_funnel_task                    = $getTotalTasks[0]->total_own_funnel_task;
                $total_own_funnel_complete_task           = $getTotalTasks[0]->total_own_funnel_complete_task;
                $total_own_funnel_pending_task            = $getTotalTasks[0]->total_own_funnel_pending_task;
                $total_own_funnel_status_change_task      = $getTotalTasks[0]->total_own_funnel_status_change_task;

                $total_team_funnel_task                   = $getTotalTasks[0]->total_team_funnel_task;
                $total_team_funnel_complete_task          = $getTotalTasks[0]->total_team_funnel_complete_task;
                $total_team_funnel_pending_task           = $getTotalTasks[0]->total_team_funnel_pending_task;
                $total_team_funnel_status_change_task     = $getTotalTasks[0]->total_team_funnel_status_change_task;

                $total_own_aypy_funnel_complete_task         = $getTotalTasks[0]->total_own_aypy_funnel_complete_task;
                $total_own_aypn_funnel_complete_task         = $getTotalTasks[0]->total_own_aypn_funnel_complete_task;
                $total_own_funnel_aypy_status_change_task    = $getTotalTasks[0]->total_own_funnel_aypy_status_change_task;

                $total_team_aypy_funnel_complete_task        = $getTotalTasks[0]->total_team_aypy_funnel_complete_task;
                $total_team_aypn_funnel_complete_task        = $getTotalTasks[0]->total_team_aypn_funnel_complete_task;
                $total_team_funnel_aypy_status_change_task   = $getTotalTasks[0]->total_team_funnel_aypy_status_change_task;

                $total_own_anpn_funnel_complete_task          = $getTotalTasks[0]->total_own_anpn_funnel_complete_task;
                $total_team_anpn_funnel_complete_task         = $getTotalTasks[0]->total_team_anpn_funnel_complete_task;

                $total_own_anpy_funnel_complete_task          = $getTotalTasks[0]->total_own_anpy_funnel_complete_task;
                $total_team_anpy_funnel_complete_task         = $getTotalTasks[0]->total_team_anpy_funnel_complete_task;
                $total_special_remarks_complete_task          = $getTotalTasks[0]->total_special_remarks_complete_task;

                $total_next_step_confirmation                 = $getTotalTasks[0]->total_next_step_confirmation;

          

                // echo 'total_own_anpy_funnel_complete_task = '.$total_own_anpy_funnel_complete_task."<br/>";
                // echo 'total_team_anpy_funnel_complete_task = '.$total_team_anpy_funnel_complete_task;


                // echo "total_special_remarks_complete_task = ".$total_special_remarks_complete_task;

              ?>
               <div class="row">
               <div class="col-md-4">
                <div class="card text-center shadow text-dark" style="background-color:<?=GetBackgroundColor();?>; color:<?=GetTextColor();?>;">
                  <div class="card-body" style="color:<?=GetTextColor();?>!important">
                    <h5 class="card-title"><b>Total Task</b></h5>
                    <small>All tasks assigned to this user.</small> 
                    <hr>
                   <a target="_BLANK" class="card-count-heading-new" style="color:<?=$backgroundColorNew;?>!important" href="<?=base_url()?>Reports/GetTeamTaskOnSelfOrOtherFunnelTaskLists/total_task/<?=$uid?>/<?=$sdate?>/<?=$edate?>/<?=$task_action_id?>">
                      <h3 class="card-text"><?=$total_task;?></h3>
                    </a>
                  </div>
                </div>
              </div>
                 <div class="col-md-4">
                <div class="card text-center shadow text-dark" style="background-color:<?=GetBackgroundColor();?>; color:<?=GetTextColor();?>;">
                  <div class="card-body" style="color:<?=GetTextColor();?>!important">
                    <h5 class="card-title"><b>Total Complete Task</b></h5>
                    <small>Tasks that are completed.</small> 
                    <hr>
                    <a target="_BLANK" class="card-count-heading-new" style="color:<?=$backgroundColorNew;?>!important" href="<?=base_url()?>Reports/GetTeamTaskOnSelfOrOtherFunnelTaskLists/total_complete_task/<?=$uid?>/<?=$sdate?>/<?=$edate?>/<?=$task_action_id?>">
                       <h3 class="card-text"><?=$total_complete_task;?></h3>
                    </a>
                  </div>
                </div>
              </div>
              <div class="col-md-4">
               <div class="card text-center shadow text-dark" style="background-color:<?=GetBackgroundColor();?>; color:<?=GetTextColor();?>;">
                  <div class="card-body" style="color:<?=GetTextColor();?>!important">
                    <h5 class="card-title"><b>Total Pending Task</b></h5>
                    <small>Tasks still pending completion.</small> 
                    <hr>
                   <a target="_BLANK" class="card-count-heading-new" style="color:<?=$backgroundColorNew;?>!important" href="<?=base_url()?>Reports/GetTeamTaskOnSelfOrOtherFunnelTaskLists/total_pending_task/<?=$uid?>/<?=$sdate?>/<?=$edate?>/<?=$task_action_id?>">
                      <h3 class="card-text"><?=$total_pending_task;?></h3>
                    </a>
                  </div>
                </div>
              </div>
             
              </div>

              <hr>

              <div class="text-center" style="background: linear-gradient(to right, #ede7f6, #ffffff); border-radius: 12px; box-shadow: 0 4px 8px rgba(0,0,0,0.1); padding: 20px;">
             <h5>⚡ Performance Metrics Overview</h5>
              <small>
                📊 This section provides a comprehensive overview of task performance metrics, focusing on ✅ completion rates and 🔄 status changes. It includes total tasks completed, along with changes in task statuses individually and within team funnels. These metrics help assess productivity and workflow efficiency, offering insights into task management effectiveness and areas for improvement. 🚀
              </small>

            </div>
            <br>

               <div class="row">

                <div class="col-md-3">
                <div class="card text-center shadow text-dark" style="background-color:<?=GetBackgroundColor();?>; color:<?=GetTextColor();?>;">
                  <div class="card-body" style="color:<?=GetTextColor();?>!important">
                    <h5 class="card-title"><b>Total Complete Task</b></h5>
                    <small>Tasks that are completed.</small> 
                    <hr>
                    <a target="_BLANK" class="card-count-heading-new" style="color:<?=$backgroundColorNew;?>!important" href="<?=base_url()?>Reports/GetTeamTaskOnSelfOrOtherFunnelTaskLists/total_complete_task/<?=$uid?>/<?=$sdate?>/<?=$edate?>/<?=$task_action_id?>">
                       <h3 class="card-text"><?=$total_complete_task;?></h3>
                    </a>
                  </div>
                </div>
              </div>


              <div class="col-md-3">
                <div class="card text-center shadow text-dark" style="background-color:<?=GetBackgroundColor();?>; color:<?=GetTextColor();?>;">
                  <div class="card-body" style="color:<?=GetTextColor();?>!important">
                    <h5 class="card-title"><b>Total Status Change Task</b></h5>
                    <small>Tasks with any status change.</small> 
                    <hr>
                    <a target="_BLANK" class="card-count-heading-new" style="color:<?=$backgroundColorNew;?>!important" href="<?=base_url()?>Reports/GetTeamTaskOnSelfOrOtherFunnelTaskLists/total_status_change_task/<?=$uid?>/<?=$sdate?>/<?=$edate?>/<?=$task_action_id?>">
                     <h3 class="card-text"><?=$total_status_change_task;?></h3>
                    </a>
                  </div>
                </div>
              </div>

               <div class="col-md-3">
               <div class="card text-center shadow text-dark" style="background-color:<?=GetBackgroundColor();?>; color:<?=GetTextColor();?>;">
                  <div class="card-body" style="color:<?=GetTextColor();?>!important">
                    <h5 class="card-title"><b>Total Own Funnel Status Change Task</b></h5>
                    <small>Status-changed tasks by the user.</small> 
                    <hr>
                    <a target="_BLANK" class="card-count-heading-new" style="color:<?=$backgroundColorNew;?>!important" href="<?=base_url()?>Reports/GetTeamTaskOnSelfOrOtherFunnelTaskLists/total_own_funnel_status_change_task/<?=$uid?>/<?=$sdate?>/<?=$edate?>/<?=$task_action_id?>">
                      <h3 class="card-text"><?=$total_own_funnel_status_change_task;?></h3>
                    </a>
                  </div>
                </div>
              </div>

                <div class="col-md-3">
                <div class="card text-center shadow text-dark" style="background-color:<?=GetBackgroundColor();?>; color:<?=GetTextColor();?>;">
                  <div class="card-body" style="color:<?=GetTextColor();?>!important">
                    <h5 class="card-title"><b>Total Team Funnel Status Change Task</b></h5>
                    <small>Team tasks with changed statuses.</small> 
                    <hr>
                    <a target="_BLANK" class="card-count-heading-new" style="color:<?=$backgroundColorNew;?>!important" href="<?=base_url()?>Reports/GetTeamTaskOnSelfOrOtherFunnelTaskLists/total_team_funnel_status_change_task/<?=$uid?>/<?=$sdate?>/<?=$edate?>/<?=$task_action_id?>">
                     <h3 class="card-text"><?=$total_team_funnel_status_change_task;?></h3>
                    </a>
                  </div>
                </div>
              </div>


               <div class="col-md-6">
                <div class="card text-center shadow text-dark" style="background-color:<?=GetBackgroundColor();?>; color:<?=GetTextColor();?>;">
                    <div class="card-body" style="color:<?=GetTextColor();?>!important">
                      <h5 class="card-title"><b>Total Special Remarks Complete Task</b></h5>
                      <small>Tasks still complete completion.</small> 
                      <hr>
                    <a target="_BLANK" class="card-count-heading-new" style="color:<?=$backgroundColorNew;?>!important" href="<?=base_url()?>Reports/GetTeamTaskOnSelfOrOtherFunnelTaskLists/total_special_remarks_complete_task/<?=$uid?>/<?=$sdate?>/<?=$edate?>/<?=$task_action_id?>">
                        <h3 class="card-text"><?=$total_special_remarks_complete_task;?></h3>
                      </a>
                    </div>
                  </div>
                </div>
               <div class="col-md-6">
                <div class="card text-center shadow text-dark" style="background-color:<?=GetBackgroundColor();?>; color:<?=GetTextColor();?>;">
                    <div class="card-body" style="color:<?=GetTextColor();?>!important">
                      <h5 class="card-title"><b>Next-Step Confirmation</b></h5>
                      <small>Tasks on Next-Step Confirmation.</small> 
                      <hr>
                    <a target="_BLANK" class="card-count-heading-new" style="color:<?=$backgroundColorNew;?>!important" href="<?=base_url()?>Reports/GetTeamTaskOnSelfOrOtherFunnelTaskLists/total_next_step_confirmation/<?=$uid?>/<?=$sdate?>/<?=$edate?>/<?=$task_action_id?>">
                        <h3 class="card-text"><?=$total_next_step_confirmation;?></h3>
                      </a>
                    </div>
                  </div>
                </div>



              </div>

               <hr>


<div class="text-center" style="background: linear-gradient(to right, #ede7f6, #ffffff); border-radius: 12px; box-shadow: 0 4px 8px rgba(0,0,0,0.1); padding: 20px;">
           <h5>Total Own Funnel Task Overview</h5>
<small>This section summarizes your total funnel tasks, including completed, pending, and status-changed tasks initiated by you.</small>

            </div>
            <br>

              <div class="row">
                <div class="col-md-3">
                <div class="card text-center shadow text-dark" style="background-color:<?=GetBackgroundColor();?>; color:<?=GetTextColor();?>;">
                  <div class="card-body" style="color:<?=GetTextColor();?>!important">
                    <h5 class="card-title"><b>Total Own Funnel Task</b></h5>
                    <small>Tasks created by the user.</small> 
                    <hr>
                     <a target="_BLANK" class="card-count-heading-new" style="color:<?=$backgroundColorNew;?>!important" href="<?=base_url()?>Reports/GetTeamTaskOnSelfOrOtherFunnelTaskLists/total_own_funnel_task/<?=$uid?>/<?=$sdate?>/<?=$edate?>/<?=$task_action_id?>">
                      <h3 class="card-text"><?=$total_own_funnel_task;?></h3>
                    </a>
                  </div>
                </div>
              </div>
                <div class="col-md-3">
                <div class="card text-center shadow text-dark" style="background-color:<?=GetBackgroundColor();?>; color:<?=GetTextColor();?>;">
                  <div class="card-body" style="color:<?=GetTextColor();?>!important">
                    <h5 class="card-title"><b>Total Own Funnel Complete Task</b></h5>
                    <small>User-created tasks that are done.</small> 
                    <hr>
                   <a target="_BLANK" class="card-count-heading-new" style="color:<?=$backgroundColorNew;?>!important" href="<?=base_url()?>Reports/GetTeamTaskOnSelfOrOtherFunnelTaskLists/total_own_funnel_complete_task/<?=$uid?>/<?=$sdate?>/<?=$edate?>/<?=$task_action_id?>">
                      <h3 class="card-text"><?=$total_own_funnel_complete_task;?></h3>
                    </a>
                  </div>
                </div>
              </div>
               <div class="col-md-3">
                <div class="card text-center shadow text-dark" style="background-color:<?=GetBackgroundColor();?>; color:<?=GetTextColor();?>;">
                  <div class="card-body" style="color:<?=GetTextColor();?>!important">
                    <h5 class="card-title"><b>Total Own Funnel Pending Task</b></h5>
                    <small>User-created tasks still pending.</small> 
                    <hr>
                    <a target="_BLANK" class="card-count-heading-new" style="color:<?=$backgroundColorNew;?>!important" href="<?=base_url()?>Reports/GetTeamTaskOnSelfOrOtherFunnelTaskLists/total_own_funnel_pending_task/<?=$uid?>/<?=$sdate?>/<?=$edate?>/<?=$task_action_id?>">
                      <h3 class="card-text"><?=$total_own_funnel_pending_task;?></h3>
                    </a>
                  </div>
                </div>
              </div>
               <div class="col-md-3">
               <div class="card text-center shadow text-dark" style="background-color:<?=GetBackgroundColor();?>; color:<?=GetTextColor();?>;">
                  <div class="card-body" style="color:<?=GetTextColor();?>!important">
                    <h5 class="card-title"><b>Total Own Funnel Status Change Task</b></h5>
                    <small>Status-changed tasks by the user.</small> 
                    <hr>
                    <a target="_BLANK" class="card-count-heading-new" style="color:<?=$backgroundColorNew;?>!important" href="<?=base_url()?>Reports/GetTeamTaskOnSelfOrOtherFunnelTaskLists/total_own_funnel_status_change_task/<?=$uid?>/<?=$sdate?>/<?=$edate?>/<?=$task_action_id?>">
                      <h3 class="card-text"><?=$total_own_funnel_status_change_task;?></h3>
                    </a>
                  </div>
                </div>
              </div>
              </div>


              <hr>
              <div class="text-center" style="background: linear-gradient(to right, #ede7f6, #ffffff); border-radius: 12px; box-shadow: 0 4px 8px rgba(0,0,0,0.1); padding: 20px;">
              <h5>📊 Total Team Funnel Overview</h5>
                <small>
                  📋 This summary includes total tasks, ✅ completed tasks, ⏳ pending tasks, and 🔄 status-changed tasks across the team funnel.
                </small>

            </div>
            <br>
              <div class="row">
             <div class="col-md-3">
                <div class="card text-center shadow text-dark" style="background-color:<?=GetBackgroundColor();?>; color:<?=GetTextColor();?>;">
                  <div class="card-body" style="color:<?=GetTextColor();?>!important">
                    <h5 class="card-title"><b>Total Team Funnel Task</b></h5>
                    <small>Tasks delegated to team by the user.</small> 
                    <hr>
                    <a target="_BLANK" class="card-count-heading-new" style="color:<?=$backgroundColorNew;?>!important" href="<?=base_url()?>Reports/GetTeamTaskOnSelfOrOtherFunnelTaskLists/total_team_funnel_task/<?=$uid?>/<?=$sdate?>/<?=$edate?>/<?=$task_action_id?>">
                      <h3 class="card-text"><?=$total_team_funnel_task;?></h3>
                    </a>
                  </div>
                </div>
              </div>
              <div class="col-md-3">
                <div class="card text-center shadow text-dark" style="background-color:<?=GetBackgroundColor();?>; color:<?=GetTextColor();?>;">
                  <div class="card-body" style="color:<?=GetTextColor();?>!important">
                    <h5 class="card-title"><b>Total Team Funnel Complete Task</b></h5>
                    <small>Team tasks completed.</small> 
                    <hr>
                   <a target="_BLANK" class="card-count-heading-new" style="color:<?=$backgroundColorNew;?>!important" href="<?=base_url()?>Reports/GetTeamTaskOnSelfOrOtherFunnelTaskLists/total_team_funnel_complete_task/<?=$uid?>/<?=$sdate?>/<?=$edate?>/<?=$task_action_id?>">
                      <h3 class="card-text"><?=$total_team_funnel_complete_task;?></h3>
                    </a>
                  </div>
                </div>
              </div>
               <div class="col-md-3">
                <div class="card text-center shadow text-dark" style="background-color:<?=GetBackgroundColor();?>; color:<?=GetTextColor();?>;">
                  <div class="card-body" style="color:<?=GetTextColor();?>!important">
                    <h5 class="card-title"><b>Total Team Funnel Pending Task</b></h5>
                    <small>Team tasks pending.</small> 
                    <hr>
                     <a target="_BLANK" class="card-count-heading-new" style="color:<?=$backgroundColorNew;?>!important" href="<?=base_url()?>Reports/GetTeamTaskOnSelfOrOtherFunnelTaskLists/total_team_funnel_pending_task/<?=$uid?>/<?=$sdate?>/<?=$edate?>/<?=$task_action_id?>">
                      <h3 class="card-text"><?=$total_team_funnel_pending_task;?></h3>
                    </a>
                  </div>
                </div>
              </div>
              
              <div class="col-md-3">
                <div class="card text-center shadow text-dark" style="background-color:<?=GetBackgroundColor();?>; color:<?=GetTextColor();?>;">
                  <div class="card-body" style="color:<?=GetTextColor();?>!important">
                    <h5 class="card-title"><b>Total Team Funnel Status Change Task</b></h5>
                    <small>Team tasks with changed statuses.</small> 
                    <hr>
                    <a target="_BLANK" class="card-count-heading-new" style="color:<?=$backgroundColorNew;?>!important" href="<?=base_url()?>Reports/GetTeamTaskOnSelfOrOtherFunnelTaskLists/total_team_funnel_status_change_task/<?=$uid?>/<?=$sdate?>/<?=$edate?>/<?=$task_action_id?>">
                     <h3 class="card-text"><?=$total_team_funnel_status_change_task;?></h3>
                    </a>
                  </div>
                </div>
              </div>
            </div>
            <hr>

            <div class="text-center" style="background: linear-gradient(to right, #ede7f6, #ffffff); border-radius: 12px; box-shadow: 0 4px 8px rgba(0,0,0,0.1); padding: 20px;">
               <h5>✅ Own Funnel Completion Summary</h5>
                <small>
                  This includes total completed tasks across all funnel types: 🏠 Own Funnel, 🔄 AYPY Funnel, 🔄 AYPN Funnel, and 🔄 ANPN Funnel.
                </small>

            </div>
            <br>
              <div class="row">

              <div class="col-md-3">
                <div class="card text-center shadow text-dark" style="background-color:<?=GetBackgroundColor();?>; color:<?=GetTextColor();?>;">
                  <div class="card-body" style="color:<?=GetTextColor();?>!important">
                    <h5 class="card-title"><b>Total Own Funnel Complete Task</b></h5>
                    <small>User-created tasks that are done.</small> 
                    <hr>
                   <a target="_BLANK" class="card-count-heading-new" style="color:<?=$backgroundColorNew;?>!important" href="<?=base_url()?>Reports/GetTeamTaskOnSelfOrOtherFunnelTaskLists/total_own_funnel_complete_task/<?=$uid?>/<?=$sdate?>/<?=$edate?>/<?=$task_action_id?>">
                      <h3 class="card-text"><?=$total_own_funnel_complete_task;?></h3>
                    </a>
                  </div>
                </div>
              </div>
              <div class="col-md-3">
                <div class="card text-center shadow text-dark" style="background-color:<?=GetBackgroundColor();?>; color:<?=GetTextColor();?>;">
                  <div class="card-body" style="color:<?=GetTextColor();?>!important">
                    <h5 class="card-title"><b>Total Own Aypy Funnel Complete Task</b></h5>
                    <small>AYPY completed tasks by user.</small> 
                    <hr>
                    <a target="_BLANK" class="card-count-heading-new" style="color:<?=$backgroundColorNew;?>!important" href="<?=base_url()?>Reports/GetTeamTaskOnSelfOrOtherFunnelTaskLists/total_own_aypy_funnel_complete_task/<?=$uid?>/<?=$sdate?>/<?=$edate?>/<?=$task_action_id?>">
                      <h3 class="card-text"><?=$total_own_aypy_funnel_complete_task;?></h3>
                    </a>
                  </div>
                </div>
              </div>
               <div class="col-md-3">
                <div class="card text-center shadow text-dark" style="background-color:<?=GetBackgroundColor();?>; color:<?=GetTextColor();?>;">
                  <div class="card-body" style="color:<?=GetTextColor();?>!important">
                    <h5 class="card-title"><b>Total Own AYPN Funnel Complete Task</b></h5>
                    <small>AYPN completed tasks by user.</small> 
                    <hr>
                    <a target="_BLANK" class="card-count-heading-new" style="color:<?=$backgroundColorNew;?>!important" href="<?=base_url()?>Reports/GetTeamTaskOnSelfOrOtherFunnelTaskLists/total_own_aypn_funnel_complete_task/<?=$uid?>/<?=$sdate?>/<?=$edate?>/<?=$task_action_id?>">
                     <h3 class="card-text"><?=$total_own_aypn_funnel_complete_task;?></h3>
                    </a>
                  </div>
                </div>
              </div>
               <!-- <div class="col-md-3">
                <div class="card text-center shadow text-dark" style="background-color:<?=GetBackgroundColor();?>; color:<?=GetTextColor();?>;">
                  <div class="card-body" style="color:<?=GetTextColor();?>!important">
                    <h5 class="card-title"><b>Total Own ANPY Funnel Complete Task</b></h5>
                    <small>AYPN completed tasks by user.</small> 
                    <hr>
                    <a target="_BLANK" class="card-count-heading-new" style="color:<?=$backgroundColorNew;?>!important" href="<?=base_url()?>Reports/GetTeamTaskOnSelfOrOtherFunnelTaskLists/total_own_aypn_funnel_complete_task/<?=$uid?>/<?=$sdate?>/<?=$edate?>/<?=$task_action_id?>">
                     <h3 class="card-text"><?=$total_own_anpy_funnel_complete_task;?></h3>
                    </a>
                  </div>
                </div>
              </div> -->
                  <div class="col-md-3">
                <div class="card text-center shadow text-dark" style="background-color:<?=GetBackgroundColor();?>; color:<?=GetTextColor();?>;">
                  <div class="card-body" style="color:<?=GetTextColor();?>!important">
                    <h5 class="card-title"><b>Total Own Funnel ANPN Complete Task</b></h5>
                    <small>Team AYPY tasks completed.</small> 
                    <hr>
                   <a target="_BLANK" class="card-count-heading-new" style="color:<?=$backgroundColorNew;?>!important" href="<?=base_url()?>Reports/GetTeamTaskOnSelfOrOtherFunnelTaskLists/total_own_anpn_funnel_complete_task/<?=$uid?>/<?=$sdate?>/<?=$edate?>/<?=$task_action_id?>">
                       <h3 class="card-text"><?=$total_own_anpn_funnel_complete_task;?></h3>
                    </a>
                  </div>
                </div>
              </div>
        
              </div>
              <hr>

              <div class="text-center" style="background: linear-gradient(to right, #ede7f6, #ffffff); border-radius: 12px; box-shadow: 0 4px 8px rgba(0,0,0,0.1); padding: 20px;">
              <h5>✅ Total Team Funnel Completion Overview</h5>
              <small>
                Includes overall completed tasks from 👥 Team Funnel, 🔄 AYPY Funnel, 🔄 AYPN Funnel, and 🔄 ANPN Funnel efforts.
              </small>
            </div>
            <br>
              <div class="row">
<div class="col-md-3">
                <div class="card text-center shadow text-dark" style="background-color:<?=GetBackgroundColor();?>; color:<?=GetTextColor();?>;">
                  <div class="card-body" style="color:<?=GetTextColor();?>!important">
                    <h5 class="card-title"><b>Total Team Funnel Complete Task</b></h5>
                    <small>Team tasks completed.</small> 
                    <hr>
                   <a target="_BLANK" class="card-count-heading-new" style="color:<?=$backgroundColorNew;?>!important" href="<?=base_url()?>Reports/GetTeamTaskOnSelfOrOtherFunnelTaskLists/total_team_funnel_complete_task/<?=$uid?>/<?=$sdate?>/<?=$edate?>/<?=$task_action_id?>">
                      <h3 class="card-text"><?=$total_team_funnel_complete_task;?></h3>
                    </a>
                  </div>
                </div>
              </div>
        <div class="col-md-3">
                <div class="card text-center shadow text-dark" style="background-color:<?=GetBackgroundColor();?>; color:<?=GetTextColor();?>;">
                  <div class="card-body" style="color:<?=GetTextColor();?>!important">
                    <h5 class="card-title"><b>Total Team Aypy Funnel Complete Task</b></h5>
                    <small>Team AYPY tasks completed.</small> 
                    <hr>
                   <a target="_BLANK" class="card-count-heading-new" style="color:<?=$backgroundColorNew;?>!important" href="<?=base_url()?>Reports/GetTeamTaskOnSelfOrOtherFunnelTaskLists/total_team_aypy_funnel_complete_task/<?=$uid?>/<?=$sdate?>/<?=$edate?>/<?=$task_action_id?>">
                       <h3 class="card-text"><?=$total_team_aypy_funnel_complete_task;?></h3>
                    </a>
                  </div>
                </div>
              </div>
            <div class="col-md-3">
                <div class="card text-center shadow text-dark" style="background-color:<?=GetBackgroundColor();?>; color:<?=GetTextColor();?>;">
                  <div class="card-body" style="color:<?=GetTextColor();?>!important">
                    <h5 class="card-title"><b>Total Team Aypn Funnel Complete Task</b></h5>
                    <small>Team AYPN tasks completed.</small> 
                    <hr>
                   <a target="_BLANK" class="card-count-heading-new" style="color:<?=$backgroundColorNew;?>!important" href="<?=base_url()?>Reports/GetTeamTaskOnSelfOrOtherFunnelTaskLists/total_team_aypn_funnel_complete_task/<?=$uid?>/<?=$sdate?>/<?=$edate?>/<?=$task_action_id?>">
                       <h3 class="card-text"><?=$total_team_aypn_funnel_complete_task;?></h3>
                    </a>
                  </div>
                </div>
              </div>
              <!-- <div class="col-md-2">
                <div class="card text-center shadow text-dark" style="background-color:<?=GetBackgroundColor();?>; color:<?=GetTextColor();?>;">
                  <div class="card-body" style="color:<?=GetTextColor();?>!important">
                    <h5 class="card-title"><b>Total Team Funnel ANPY Complete Task</b></h5>
                    <small>Team ANPY tasks completed.</small> 
                    <hr>
                   <a target="_BLANK" class="card-count-heading-new" style="color:<?=$backgroundColorNew;?>!important" href="<?=base_url()?>Reports/GetTeamTaskOnSelfOrOtherFunnelTaskLists/total_own_anpn_funnel_complete_task/<?=$uid?>/<?=$sdate?>/<?=$edate?>/<?=$task_action_id?>">
                       <h3 class="card-text"><?=$total_team_anpy_funnel_complete_task;?></h3>
                    </a>
                  </div>
                </div>
              </div> -->
               <div class="col-md-3">
                <div class="card text-center shadow text-dark" style="background-color:<?=GetBackgroundColor();?>; color:<?=GetTextColor();?>;">
                  <div class="card-body" style="color:<?=GetTextColor();?>!important">
                    <h5 class="card-title"><b>Total Team Funnel ANPN Complete Task</b></h5>
                    <small>Team AYPN tasks completed.</small> 
                    <hr>
                   <a target="_BLANK" class="card-count-heading-new" style="color:<?=$backgroundColorNew;?>!important" href="<?=base_url()?>Reports/GetTeamTaskOnSelfOrOtherFunnelTaskLists/total_team_anpn_funnel_complete_task/<?=$uid?>/<?=$sdate?>/<?=$edate?>/<?=$task_action_id?>">
                       <h3 class="card-text"><?=$total_team_anpn_funnel_complete_task;?></h3>
                    </a>
                  </div>
                </div>
              </div>
        
            </div>
            <hr>


              <div class="text-center" style="background: linear-gradient(to right, #ede7f6, #ffffff); border-radius: 12px; box-shadow: 0 4px 8px rgba(0,0,0,0.1); padding: 20px;">
                 <h5>🔄 AYPY Status Change Tasks</h5>
                <small>
                  Includes both 🏠 Total Own Funnel and 👥 Total Team Funnel AYPY status change tasks.
                </small>

            </div>
            <br>
            <div class="row">
                <div class="col-md-6">
                <div class="card text-center shadow text-dark" style="background-color:<?=GetBackgroundColor();?>; color:<?=GetTextColor();?>;">
                  <div class="card-body" style="color:<?=GetTextColor();?>!important">
                    <h5 class="card-title"><b>Total Own Funnel AYPY Status Change Task</b></h5>
                    <small>Team AYPY status changes.</small> 
                    <hr>
                    <a target="_BLANK" class="card-count-heading-new" style="color:<?=$backgroundColorNew;?>!important" href="<?=base_url()?>Reports/GetTeamTaskOnSelfOrOtherFunnelTaskLists/total_own_funnel_aypy_status_change_task/<?=$uid?>/<?=$sdate?>/<?=$edate?>/<?=$task_action_id?>">
                     <h3 class="card-text"><?=$total_own_funnel_aypy_status_change_task;?></h3>
                    </a>
                  </div>
                </div>
              </div>
                <div class="col-md-6">
                <div class="card text-center shadow text-dark" style="background-color:<?=GetBackgroundColor();?>; color:<?=GetTextColor();?>;">
                  <div class="card-body" style="color:<?=GetTextColor();?>!important">
                    <h5 class="card-title"><b>Total Team Funnel AYPY Status Change Task</b></h5>
                    <small>Team AYPY status changes.</small> 
                    <hr>
                    <a target="_BLANK" class="card-count-heading-new" style="color:<?=$backgroundColorNew;?>!important" href="<?=base_url()?>Reports/GetTeamTaskOnSelfOrOtherFunnelTaskLists/total_team_funnel_aypy_status_change_task/<?=$uid?>/<?=$sdate?>/<?=$edate?>/<?=$task_action_id?>">
                     <h3 class="card-text"><?=$total_team_funnel_aypy_status_change_task;?></h3>
                    </a>
                  </div>
                </div>
              </div>
            </div>


           

            <hr>
            <div class="text-center" style="background: linear-gradient(to right, #ede7f6, #ffffff); border-radius: 12px; box-shadow: 0 4px 8px rgba(0,0,0,0.1); padding: 20px;">
              <h5>
  <i>✅ Total Complete Status Wise Task Details</i> <br>
  <small>📊 This section provides a breakdown of tasks based on their completion status.</small>
</h5>

            </div>
            <hr>
            <div class="row">
              <?php  
                $descriptions = [
                  'open' => 'Leads or contacts that are still open for follow-up.',
                  'reachout' => 'Leads contacted or attempted to contact.',
                  'tentative' => 'Leads that showed interest but have not confirmed.',
                  'will_do_later' => 'Leads postponed for future action.',
                  'not_interested' => 'Leads that explicitly declined the offer.',
                  'positive' => 'Leads with a positive response but not yet closed.',
                  'closure' => 'Leads successfully closed with a final outcome.',
                  'open_rpem' => 'Open leads under RPM or RP-EM funnel.',
                  'very_positive' => 'Leads that showed strong positive intent.',
                  'ttd_reachout' => 'Reachouts done under "TTD" funnel or phase.',
                  'wno_reachout' => 'Reachouts made under "WNO" funnel or status.',
                  'positive_nap' => 'Positive leads under the NAP (Not Acted Positively) status.',
                  'very_positive_nap' => 'Highly positive leads under NAP status.',
                  'on_boarded' => 'Leads that successfully completed onboarding.'
                ];
                foreach ($total_status as $tasktypeData) {
                  foreach ($tasktypeData as $key => $value) {
                      if (in_array($key, ['user_id', 'username'])) continue; // Skip these
                
                      $r = rand(150, 255);
                      $g = rand(150, 255);
                      $b = rand(150, 255);
                      $backgroundColor = "rgba($r, $g, $b,.3)";
                
                      $hue = rand(0, 360);
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
                    <a target="_BLANK" class="card-count-heading-new" style="color:<?=$backgroundColorNew;?>!important" href="<?=base_url()?>Reports/GetTeamTaskOnSelfOrOtherFunnelTaskLists/<?=$key?>/<?=$uid?>/<?=$sdate?>/<?=$edate?>/<?=$task_action_id?>">
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



<?php 
// dd($partnerWiseData_total);
?>

              <hr>
            <div class="text-center" style="background: linear-gradient(to right, #ede7f6, #ffffff); border-radius: 12px; box-shadow: 0 4px 8px rgba(0,0,0,0.1); padding: 20px;">
              <h5>
                <i>Total Complete Partner Wise Task Details</i><br>
                <small>This section provides a breakdown of tasks based on their completion partner.</small>
              </h5>
            </div>
            <hr>
            <div class="row">
              <?php  
                $descriptions = [
                      'corporate' => 'Leads or contacts from corporate organizations or companies.',
                      'PSU' => 'Leads associated with Public Sector Undertakings (PSUs).',
                      'NGO' => 'Leads from Non-Governmental Organizations (NGOs).',
                      'Private_School' => 'Leads from private schools and educational institutions.',
                      'Individual' => 'Independent individuals showing interest or engagement.',
                      'Govt_Off' => 'Government officials or representatives as leads.',
                      'Other' => 'Leads that do not fall under any defined category.',
                      'Online' => 'Leads generated or contacted through online channels.',
                      'STEM_School' => 'Leads from STEM-focused (Science, Technology, Engineering, Math) schools.',
                      'Foundation' => 'Leads from charitable foundations or grant-making bodies.',
                      'MNC' => 'Leads from multinational corporations.',
                      'HO_Leads' => 'Leads passed from Head Office or central team.',
                      'DMF' => 'Leads related to District Mineral Foundation (DMF) initiatives.',
                      'Elected_Representatives' => 'Leads from elected political representatives or their offices.'
                  ];
                foreach ($partnerWiseData_total as $tasktypeData) {
                  foreach ($tasktypeData as $key => $value) {
                      if (in_array($key, ['user_id', 'username'])) continue; // Skip these
                
                      $r = rand(150, 255);
                      $g = rand(150, 255);
                      $b = rand(150, 255);
                      $backgroundColor = "rgba($r, $g, $b,.3)";
                
                      $hue = rand(0, 360);
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
                    <a target="_BLANK" class="card-count-heading-new" style="color:<?=$backgroundColorNew;?>!important" href="<?=base_url()?>Reports/GetTeamTaskOnSelfOrOtherFunnelTaskLists/<?=$key?>/<?=$uid?>/<?=$sdate?>/<?=$edate?>/<?=$task_action_id?>">
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







            <!-- dd($total_time); -->
            <hr>
            <div class="text-center" style="background: linear-gradient(to right, #ede7f6, #ffffff); border-radius: 12px; box-shadow: 0 4px 8px rgba(0,0,0,0.1); padding: 20px;">
              <h5>
                <i>Total Complete Between Time</i>
                <br>
                <small>This section provides a breakdown of tasks based on their completion category.</small>
              </h5>
            </div>
            <hr>
            <div class="row">
              <?php  
                $descriptions = [
                'zero_to_fifty_nine_seconds' => 'Tasks completed within 0 to 59 seconds.',
                'one_to_five_minutes' => 'Tasks completed in 1 to 5 minutes.',
                'five_to_ten_minutes' => 'Tasks completed in 5 to 10 minutes.',
                'ten_to_fifteen_minutes' => 'Tasks completed in 10 to 15 minutes.',
                'sixteen_to_thirty_minutes' => 'Tasks completed in 16 to 30 minutes.',
                'thirty_one_to_fifty_minutes' => 'Tasks completed in 31 to 50 minutes.',
                'fifty_one_to_sixty_minutes' => 'Tasks completed in 51 to 60 minutes.',
                'sixty_one_to_ninety_minutes' => 'Tasks completed in 61 to 90 minutes.',
                'ninety_one_to_one_twenty_minutes' => 'Tasks completed in 91 to 120 minutes.',
                'greater_than_one_twenty_minutes' => 'Tasks taking more than 120 minutes to complete.'
                ];
                
                
                    foreach ($total_time as $tasktypeData) {
                        foreach ($tasktypeData as $key => $value) {
                            if (in_array($key, ['user_id', 'username'])) continue; // Skip these
                
                            $r = rand(150, 255);
                            $g = rand(150, 255);
                            $b = rand(150, 255);
                            $backgroundColor = "rgba($r, $g, $b,.3)";
                
                            $hue = rand(0, 360);
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
                    <a target="_BLANK" class="card-count-heading-new" style="color:<?=$backgroundColorNew;?>!important" href="<?=base_url()?>Reports/GetTeamTaskOnSelfOrOtherFunnelTaskLists/<?=$key?>/<?=$uid?>/<?=$sdate?>/<?=$edate?>/<?=$task_action_id?>">
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
            <hr>
            <div class="text-center" style="background: linear-gradient(to right, #ede7f6, #ffffff); border-radius: 12px; box-shadow: 0 4px 8px rgba(0,0,0,0.1); padding: 20px;">
              <h5>
                <i>Total Complete in Category</i><br>
                <small>This section provides a breakdown of tasks based on their completion category.</small>
              </h5>
            </div>
            <hr>
            <div class="row">
              <?php  
                $descriptions = [
                          'total_in_q1_twetenty_closure_funnel' => 'Companies assigned to the Q1 Twenty Closure Funnel.',
                          'total_in_potential_funnel_for_fy' => 'Companies marked as potential funnel for the financial year.',
                          'total_in_to_be_nurtured_for_fy' => 'Companies categorized as "To Be Nurtured" for the financial year.',
                          'total_in_fifity_new_lead_funnel' => 'Companies included in the Fifty New Lead Funnel.',
                      ];
                
                foreach ($total_category as $tasktypeData) {
                    foreach ($tasktypeData as $key => $value) {
                        if (in_array($key, ['user_id', 'username'])) continue; // Skip these
                
                        $r = rand(150, 255);
                        $g = rand(150, 255);
                        $b = rand(150, 255);
                        $backgroundColor = "rgba($r, $g, $b,.3)";
                
                        $hue = rand(0, 360);
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
                    <a target="_BLANK" class="card-count-heading-new" style="color:<?=$backgroundColorNew;?>!important" href="<?=base_url()?>Reports/GetTeamTaskOnSelfOrOtherFunnelTaskLists/<?=$key?>/<?=$uid?>/<?=$sdate?>/<?=$edate?>/<?=$task_action_id?>">
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
            <hr>
            <?php // dd($total_old_category); ?>
            <div class="text-center" style="background: linear-gradient(to right, #ede7f6, #ffffff); border-radius: 12px; box-shadow: 0 4px 8px rgba(0,0,0,0.1); padding: 20px;">
              <h5>
                <i>Total Complete in Category</i><br>
                <small>This section provides a breakdown of tasks based on their completion category.</small>
              </h5>
            </div>
            <hr>
            <div class="row">
              <?php  
                $descriptions = [
                'potential' => 'Total companies marked as potential clients.',
                'topspender' => 'Companies that are top spenders.',
                'upsell_client' => 'Clients identified for upsell opportunities.',
                'focus_funnel' => 'Companies in the focus funnel.',
                'keycompany' => 'Key or high-priority companies.',
                'pkclient' => 'Clients classified under PK segment.',
                'priorityc' => 'Companies marked with priority status.'
                ];
                
                
                foreach ($total_old_category as $tasktypeData) {
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
                    <a target="_BLANK" class="card-count-heading-new" style="color:<?=$backgroundColorNew;?>!important" href="<?=base_url()?>Reports/GetTeamTaskOnSelfOrOtherFunnelTaskLists/<?=$key?>/<?=$uid?>/<?=$sdate?>/<?=$edate?>/<?=$task_action_id?>">
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
                <i>Average Task Frequency Per Day</i><br>
                <small>This shows how frequently tasks are planned or completed on average per day.</small>
              </h5>
            </div>
            <hr>
            <div class="row">
              <?php  
                $descriptions = [
                    'average_total_task_frequency_per_day' => 'Average number of all tasks per day.',
                    'average_total_task_AY_PY_frequency_per_day' => 'Average number of AYPY tasks per day.',
                    'average_total_task_AY_PN_frequency_per_day' => 'Average number of AYPN tasks per day.',
                    'average_total_task_AN_PN_frequency_per_day' => 'Average number of ANPN tasks per day.',
                    'average_total_task_AY_PY_Same_Status_frequency_per_day' => 'Average AYPY tasks per day with same status maintained.',
                    'average_total_funnel_status_change_frequency_per_day' => 'Average number of funnel status changes per day.',
                    'average_task_own_funnel_status_change_task_frequency_per_day' => 'Average status changes per day in user-created funnel.',
                    'average_task_team_funnel_status_change_task_frequency_per_day' => 'Average status changes per day in team funnel.',
                    'average_complete_task_frequency_per_day_on_team_funnel' => 'Average completed tasks per day in team funnel.',
                    'average_complete_task_frequency_per_day_on_own_funnel' => 'Average completed tasks per day in user\'s own funnel.',
                    'average_AY_PY_task_frequency_per_day_on_own_funnel' => 'Average AYPY tasks per day in user\'s funnel.',
                    'average_AY_PY_task_frequency_per_day_on_team_funnel' => 'Average AYPY tasks per day in team funnel.',
                    'average_AY_PY_status_change_task_frequency_per_day_on_own_funnel' => 'Average status changes per day for AYPY tasks in user\'s funnel.',
                    'average_AY_PY_status_change_task_frequency_per_day_on_team_funnel' => 'Average status changes per day for AYPY tasks in team funnel.',
                    'average_pending_task_frequency_per_day_on_own_funnel' => 'Average number of pending tasks per day in user\'s funnel.',
                    'average_pending_task_frequency_per_day_on_team_funnel' => 'Average number of pending tasks per day in team funnel.',
                ];
                
                
                foreach ($getTotalTasksFrequncy as $tasktypeData) {
                foreach ($tasktypeData as $key => $value) {
                    if (in_array($key, ['user_id', 'username'])) continue; // Skip these
                
                    $r = rand(150, 255);
                    $g = rand(150, 255);
                    $b = rand(150, 255);
                    $backgroundColor = "rgba($r, $g, $b,.3)";
                
                    $hue = rand(0, 360);
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
                    <h3 class="card-text"><?= $value ?></h3>
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
                <i>Task Conversion Summary Descriptions</i><br>
                <small>This summary tracks user task outcomes. total_conversions shows all conversions, while positive_conversions and negative_conversions reflect successful or failed outcomes. other conversions captures neutral or uncategorized results, helping assess user performance and engagement impact.</small>
              </h5>
            </div>
            <hr>
            <div class="row">
              <?php  
                $descriptions = [
                      'user_id' => 'Unique identifier for the user.',
                      'username' => 'Name of the task owner.',
                      'total_conversions' => 'Total number of conversions made by the user.',
                      'positive_conversions' => 'Conversions that resulted in a positive outcome.',
                      'negative_conversions' => 'Conversions that resulted in a negative outcome.',
                      'other_conversions' => 'Conversions that do not fall under positive or negative.'
                  ];

                
                
                foreach ($total_conversions as $tasktypeData) {
                foreach ($tasktypeData as $key => $value) {
                    if (in_array($key, ['user_id', 'username'])) continue; // Skip these
                
                    $r = rand(150, 255);
                    $g = rand(150, 255);
                    $b = rand(150, 255);
                    $backgroundColor = "rgba($r, $g, $b,.3)";
                
                    $hue = rand(0, 360);
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
                     <a target="_BLANK" class="card-count-heading-new" style="color:<?=$backgroundColorNew;?>!important" href="<?=base_url()?>Reports/GetTeamTaskOnSelfOrOtherFunnelTaskLists/<?=$key?>/<?=$uid?>/<?=$sdate?>/<?=$edate?>/<?=$task_action_id?>">
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
            <div class="body-content">
              <div class="card-body each">
                <div class="body-content">
                  <div class="page-header">
                    <?php
                      $data = $groupedData;
                      $data1 = $groupedData;
                      ?>
                    <fieldset>
                      <div class="card">
                        <div class="text-center" style="background: linear-gradient(to right, #ede7f6, #ffffff); border-radius: 12px; box-shadow: 0 4px 8px rgba(0,0,0,0.1); padding: 20px;">
                          <h5><i>Total Task Details</i></h5>
                          <small>This section provides a summary of all assigned and completed tasks.</small> <br>
                          <p><i><b><?= $sdate ?> To <?= $edate ?></b></i></p>
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
                                
                                   echo "<td><a class='font-weight-bold' target='_BLANK' href='" . base_url() . "Reports/GetTeamTaskOnSelfOrOtherFunnelTaskLists/total_task_count/$userPallnerData->planner_user_id/$sdate/$edate/all/userwise'>".htmlspecialchars($userPallnerData->total_task_count)."</a></td>";
                                  foreach (array_keys($taskTypes) as $taskType) {
                                     $task_type_id = $this->Report_model->GetTaskIDUsingTaskName($taskType)[0]->id;
                                
                                      echo "<td><a class='font-weight-bold' target='_BLANK' href='" . base_url() . "Reports/GetTeamTaskOnSelfOrOtherFunnelTaskLists/Check_task_type/$userPallnerData->planner_user_id/$sdate/$edate/$task_type_id/userwise'>".
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
             


              <hr>
              <div class="card">
                <div class="text-center" style="background: linear-gradient(to right, #ede7f6, #ffffff); border-radius: 12px; box-shadow: 0 4px 8px rgba(0,0,0,0.1); padding: 20px;">
                  <h5>
                    <i>Total Complete / Pending Task Details</i><br>
                    <small>This section provides a summary of all completed tasks with relevant details.</small>
                  </h5>
                  <p><i><b><?= $sdate ?> To <?= $edate ?></b></i></p>
                </div>
                <hr>
                <div class="table-responsive text-nowrap">
                  <table id="example2" class="table table-striped table-bordered" cellspacing="0" width="100%">
                    <thead class="thead-dark">
                      <tr>
                          <th class="text-capitalize">#️⃣ Sr. NO</th>
                          <th class="text-capitalize">👤 Username</th>
                          <th class="text-capitalize">📋 Total Task</th>
                          <th class="text-capitalize">🧑‍💼 Total Own Funnel Task</th>
                          <th class="text-capitalize">👥 Total Team Funnel Task</th>
                          <th class="text-capitalize">✅ Total Complete Task</th>
                          <th class="text-capitalize">✅ Total Special Remarks Complete Task</th>
                          <th class="text-capitalize">🧑‍💼✅ Total Own Funnel Complete Task</th>
                          <th class="text-capitalize">👥✅ Total Team Funnel Complete Task</th>
                          <th class="text-capitalize">⏳ Total Pending Task</th>
                          <th class="text-capitalize">🧑‍💼⏳ Total Own Funnel Pending Task</th>
                          <th class="text-capitalize">👥⏳ Total Team Funnel Pending Task</th>
                          <th class="text-capitalize">🔄 Total Status Change Task</th>
                          <th class="text-capitalize">🧑‍💼🔄 Total Own Funnel Status Change Task</th>
                          <th class="text-capitalize">👥🔄 Total Team Funnel Status Change Task</th>
                          <th class="text-capitalize">🧑‍💼📊 Total Own Funnel AY PY Task</th>
                          <th class="text-capitalize">👥📊 Total Team Funnel AY PY Task</th>
                          <th class="text-capitalize">🧑‍💼📈 Total Own Funnel AY PY Status Change Task</th>
                          <th class="text-capitalize">👥📈 Total Team Funnel AY PY Status Change Task</th>
                          <th class="text-capitalize">🧑‍💼📊 Total Own Funnel AY PN Task</th>
                          <th class="text-capitalize">👥📊 Total Team Funnel AY PN Task</th>
                          <th class="text-capitalize">📊 Next-Step Confirmation</th>
                        </tr>
                    </thead>
                    <tbody>
                      <?php
                        $i = 1;
                        foreach ($getTasks as $row) {
                          $task_user_id = $row->user_id;
                        
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
                        <td><?= htmlspecialchars($i) ?></td>
                        <td class="text-capitalize"><?= htmlspecialchars($row->username) ?></td>
                        <td>
                          <a target="_BLANK" class="card-count-heading-new" style="color:<?=$backgroundColorNew;?>!important" href="<?=base_url()?>Reports/GetTeamTaskOnSelfOrOtherFunnelTaskLists/total_task/<?=$task_user_id?>/<?=$sdate?>/<?=$edate?>/<?=$task_action_id?>/userwise">
                          <?= htmlspecialchars($row->total_task) ?>
                          </a>
                        </td>
                        <td>
                          <a target="_BLANK" class="card-count-heading-new" style="color:<?=$backgroundColorNew;?>!important" href="<?=base_url()?>Reports/GetTeamTaskOnSelfOrOtherFunnelTaskLists/total_own_funnel_task/<?=$task_user_id?>/<?=$sdate?>/<?=$edate?>/<?=$task_action_id?>/userwise">
                          <?= htmlspecialchars($row->total_own_funnel_task) ?>
                          </a>
                        </td>
                        <td>
                          <a target="_BLANK" class="card-count-heading-new" style="color:<?=$backgroundColorNew;?>!important" href="<?=base_url()?>Reports/GetTeamTaskOnSelfOrOtherFunnelTaskLists/total_team_funnel_task/<?=$task_user_id?>/<?=$sdate?>/<?=$edate?>/<?=$task_action_id?>/userwise">
                          <?= htmlspecialchars($row->total_team_funnel_task) ?>
                          </a>
                        </td>
                        <td>
                          <a target="_BLANK" class="card-count-heading-new" style="color:<?=$backgroundColorNew;?>!important" href="<?=base_url()?>Reports/GetTeamTaskOnSelfOrOtherFunnelTaskLists/total_complete_task/<?=$task_user_id?>/<?=$sdate?>/<?=$edate?>/<?=$task_action_id?>/userwise">
                          <?= htmlspecialchars($row->total_complete_task) ?>
                          </a>
                        </td>
                        <td>
                          <a target="_BLANK" class="card-count-heading-new" style="color:<?=$backgroundColorNew;?>!important" href="<?=base_url()?>Reports/GetTeamTaskOnSelfOrOtherFunnelTaskLists/total_special_remarks_complete_task/<?=$task_user_id?>/<?=$sdate?>/<?=$edate?>/<?=$task_action_id?>/userwise">
                          <?= htmlspecialchars($row->total_special_remarks_complete_task) ?>
                          </a>
                        </td>
                        <td>
                          <a target="_BLANK" class="card-count-heading-new" style="color:<?=$backgroundColorNew;?>!important" href="<?=base_url()?>Reports/GetTeamTaskOnSelfOrOtherFunnelTaskLists/total_own_funnel_complete_task/<?=$task_user_id?>/<?=$sdate?>/<?=$edate?>/<?=$task_action_id?>/userwise">
                          <?= htmlspecialchars($row->total_own_funnel_complete_task) ?>
                          </a>
                        </td>
                        <td>
                          <a target="_BLANK" class="card-count-heading-new" style="color:<?=$backgroundColorNew;?>!important" href="<?=base_url()?>Reports/GetTeamTaskOnSelfOrOtherFunnelTaskLists/total_team_funnel_complete_task/<?=$task_user_id?>/<?=$sdate?>/<?=$edate?>/<?=$task_action_id?>/userwise">
                          <?= htmlspecialchars($row->total_team_funnel_complete_task) ?>
                          </a>
                        </td>
                        <td>
                          <a target="_BLANK" class="card-count-heading-new" style="color:<?=$backgroundColorNew;?>!important" href="<?=base_url()?>Reports/GetTeamTaskOnSelfOrOtherFunnelTaskLists/total_pending_task/<?=$task_user_id?>/<?=$sdate?>/<?=$edate?>/<?=$task_action_id?>/userwise">
                          <?= htmlspecialchars($row->total_pending_task) ?>
                          </a>
                        </td>
                        <td>
                          <a target="_BLANK" class="card-count-heading-new" style="color:<?=$backgroundColorNew;?>!important" href="<?=base_url()?>Reports/GetTeamTaskOnSelfOrOtherFunnelTaskLists/total_own_funnel_pending_task/<?=$task_user_id?>/<?=$sdate?>/<?=$edate?>/<?=$task_action_id?>/userwise">
                          <?= htmlspecialchars($row->total_own_funnel_pending_task) ?>
                          </a>
                        </td>
                        <td>
                          <a target="_BLANK" class="card-count-heading-new" style="color:<?=$backgroundColorNew;?>!important" href="<?=base_url()?>Reports/GetTeamTaskOnSelfOrOtherFunnelTaskLists/total_team_funnel_pending_task/<?=$task_user_id?>/<?=$sdate?>/<?=$edate?>/<?=$task_action_id?>/userwise">
                          <?= htmlspecialchars($row->total_team_funnel_pending_task) ?>
                          </a>
                        </td>
                        <td>
                          <a target="_BLANK" class="card-count-heading-new" style="color:<?=$backgroundColorNew;?>!important" href="<?=base_url()?>Reports/GetTeamTaskOnSelfOrOtherFunnelTaskLists/total_status_change_task/<?=$task_user_id?>/<?=$sdate?>/<?=$edate?>/<?=$task_action_id?>/<?=$task_action_id?>/userwise">
                          <?= htmlspecialchars($row->total_status_change_task) ?>
                          </a>
                        </td>
                        <td>
                          <a target="_BLANK" class="card-count-heading-new" style="color:<?=$backgroundColorNew;?>!important" href="<?=base_url()?>Reports/GetTeamTaskOnSelfOrOtherFunnelTaskLists/total_own_funnel_status_change_task/<?=$task_user_id?>/<?=$sdate?>/<?=$edate?>/<?=$task_action_id?>/userwise">
                          <?= htmlspecialchars($row->total_own_funnel_status_change_task) ?>
                          </a>
                        </td>
                        <td>
                          <a target="_BLANK" class="card-count-heading-new" style="color:<?=$backgroundColorNew;?>!important" href="<?=base_url()?>Reports/GetTeamTaskOnSelfOrOtherFunnelTaskLists/total_team_funnel_status_change_task/<?=$task_user_id?>/<?=$sdate?>/<?=$edate?>/<?=$task_action_id?>/userwise">
                          <?= htmlspecialchars($row->total_team_funnel_status_change_task) ?>
                          </a>
                        </td>
                        <td>
                          <a target="_BLANK" class="card-count-heading-new" style="color:<?=$backgroundColorNew;?>!important" href="<?=base_url()?>Reports/GetTeamTaskOnSelfOrOtherFunnelTaskLists/total_own_aypy_funnel_complete_task/<?=$task_user_id?>/<?=$sdate?>/<?=$edate?>/<?=$task_action_id?>/userwise">
                          <?= htmlspecialchars($row->total_own_aypy_funnel_complete_task) ?>
                          </a>
                        </td>
                        <td>
                          <a target="_BLANK" class="card-count-heading-new" style="color:<?=$backgroundColorNew;?>!important" href="<?=base_url()?>Reports/GetTeamTaskOnSelfOrOtherFunnelTaskLists/total_team_aypy_funnel_complete_task/<?=$task_user_id?>/<?=$sdate?>/<?=$edate?>/<?=$task_action_id?>/userwise">
                          <?= htmlspecialchars($row->total_team_aypy_funnel_complete_task) ?>
                          </a>
                        </td>
                        <td>
                          <a target="_BLANK" class="card-count-heading-new" style="color:<?=$backgroundColorNew;?>!important" href="<?=base_url()?>Reports/GetTeamTaskOnSelfOrOtherFunnelTaskLists/total_own_funnel_aypy_status_change_task/<?=$task_user_id?>/<?=$sdate?>/<?=$edate?>/<?=$task_action_id?>/userwise">
                          <?= htmlspecialchars($row->total_own_funnel_aypy_status_change_task) ?>
                          </a>
                        </td>
                        <td>
                          <a target="_BLANK" class="card-count-heading-new" style="color:<?=$backgroundColorNew;?>!important" href="<?=base_url()?>Reports/GetTeamTaskOnSelfOrOtherFunnelTaskLists/total_team_funnel_aypy_status_change_task/<?=$task_user_id?>/<?=$sdate?>/<?=$edate?>/<?=$task_action_id?>/userwise">
                          <?= htmlspecialchars($row->total_team_funnel_aypy_status_change_task) ?>
                          </a>
                        </td>
                        <td>
                          <a target="_BLANK" class="card-count-heading-new" style="color:<?=$backgroundColorNew;?>!important" href="<?=base_url()?>Reports/GetTeamTaskOnSelfOrOtherFunnelTaskLists/total_own_aypn_funnel_complete_task/<?=$task_user_id?>/<?=$sdate?>/<?=$edate?>/<?=$task_action_id?>/userwise">
                          <?= htmlspecialchars($row->total_own_aypn_funnel_complete_task) ?>
                          </a>
                        </td>
                        <td>
                          <a target="_BLANK" class="card-count-heading-new" style="color:<?=$backgroundColorNew;?>!important" href="<?=base_url()?>Reports/GetTeamTaskOnSelfOrOtherFunnelTaskLists/total_team_aypn_funnel_complete_task/<?=$task_user_id?>/<?=$sdate?>/<?=$edate?>/<?=$task_action_id?>/userwise">
                          <?= htmlspecialchars($row->total_team_aypn_funnel_complete_task) ?>
                          </a>
                        </td>
                        <td>
                          <a target="_BLANK" class="card-count-heading-new" style="color:<?=$backgroundColorNew;?>!important" href="<?=base_url()?>Reports/GetTeamTaskOnSelfOrOtherFunnelTaskLists/total_next_step_confirmation/<?=$task_user_id?>/<?=$sdate?>/<?=$edate?>/<?=$task_action_id?>/userwise">
                          <?= htmlspecialchars($row->total_next_step_confirmation) ?>
                          </a>
                        </td>
                      </tr>
                      <?php $i++; } ?>
                    </tbody>
                  </table>
                </div>
                <hr />
              </div>















                <hr>
              <div class="card">
                <div class="text-center" style="background: linear-gradient(to right, #ede7f6, #ffffff); border-radius: 12px; box-shadow: 0 4px 8px rgba(0,0,0,0.1); padding: 20px;">
                     <h5>
                      <i>Task Conversion Summary By User</i><br>
                      <small>This summary tracks user task outcomes. total_conversions shows all conversions, while positive_conversions and negative_conversions reflect successful or failed outcomes. other conversions captures neutral or uncategorized results, helping assess user performance and engagement impact.</small>
                    </h5>
                  <p><i><b><?= $sdate ?> To <?= $edate ?></b></i></p>
                </div>
                <hr>
                <div class="table-responsive text-nowrap">
                  <table id="example8" class="table table-striped table-bordered" cellspacing="0" width="100%">
                    <thead class="thead-dark">
                     <tr>
                        <th class="text-capitalize">#️⃣ Sr. NO</th>
                        <th class="text-capitalize">👤 Username</th>
                        <th class="text-capitalize">🔄 Total Conversions</th>
                        <th class="text-capitalize">✅ Positive Conversions</th>
                        <th class="text-capitalize">❌ Negative Conversions</th>
                        <th class="text-capitalize">⚪ Other Conversions</th>
                      </tr>
                    </thead>
                    <tbody>
                      <?php
                        $i = 1;
                        foreach ($user_wise_conversions as $row) {
                          $task_user_id = $row->user_id;
                        
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
                        <td><?= htmlspecialchars($i) ?></td>
                        <td class="text-capitalize"><?= htmlspecialchars($row->username) ?></td>
                        <td>
                          <a target="_BLANK" class="card-count-heading-new" style="color:<?=$backgroundColorNew;?>!important" href="<?=base_url()?>Reports/GetTeamTaskOnSelfOrOtherFunnelTaskLists/total_conversions/<?=$task_user_id?>/<?=$sdate?>/<?=$edate?>/<?=$task_action_id?>/userwise">
                          <?= htmlspecialchars($row->total_conversions) ?>
                          </a>
                        </td>
                        <td>
                          <a target="_BLANK" class="card-count-heading-new" style="color:<?=$backgroundColorNew;?>!important" href="<?=base_url()?>Reports/GetTeamTaskOnSelfOrOtherFunnelTaskLists/positive_conversions/<?=$task_user_id?>/<?=$sdate?>/<?=$edate?>/<?=$task_action_id?>/userwise">
                          <?= htmlspecialchars($row->positive_conversions) ?>
                          </a>
                        </td>
                        <td>
                          <a target="_BLANK" class="card-count-heading-new" style="color:<?=$backgroundColorNew;?>!important" href="<?=base_url()?>Reports/GetTeamTaskOnSelfOrOtherFunnelTaskLists/negative_conversions/<?=$task_user_id?>/<?=$sdate?>/<?=$edate?>/<?=$task_action_id?>/userwise">
                          <?= htmlspecialchars($row->negative_conversions) ?>
                          </a>
                        </td>
                        <td>
                          <a target="_BLANK" class="card-count-heading-new" style="color:<?=$backgroundColorNew;?>!important" href="<?=base_url()?>Reports/GetTeamTaskOnSelfOrOtherFunnelTaskLists/other_conversions/<?=$task_user_id?>/<?=$sdate?>/<?=$edate?>/<?=$task_action_id?>/userwise">
                          <?= htmlspecialchars($row->other_conversions) ?>
                          </a>
                        </td>
                       
                      </tr>
                      <?php $i++; } ?>
                    </tbody>
                  </table>
                </div>
                <hr />
              </div>



                <hr>
              <div class="card">
                <div class="text-center" style="background: linear-gradient(to right, #ede7f6, #ffffff); border-radius: 12px; box-shadow: 0 4px 8px rgba(0,0,0,0.1); padding: 20px;">
                     <h5>
                      <i>🔄 Our & Team Status Conversion</i><br>
                      <small>🔄 Our & Team Status Conversion brings clear visibility to progress 📊, tracks every stage 🔍, boosts teamwork 🤝, and ensures goals 🎯 are achieved faster ⚡ with smooth workflow 🔧 and smart insights 💡.</small>
                    </h5>
                  <p><i><b><?= $sdate ?> To <?= $edate ?></b></i></p>
                </div>
                <hr>
                <div class="table-responsive text-nowrap">

                  <table id="example21" class="table table-striped table-bordered" cellspacing="0" width="100%">
                          <thead class="thead-dark">
                              <tr class="text-center">
                                  <th scope="col">🔄 Status Change</th>
                                  <th scope="col">✅ Total Change</th>
                              </tr>
                          </thead>
                          <tbody>
                              <?php 
                                if(sizeof($bdconversions) > 0):
                                  foreach($bdconversions as $bdconversion): ?>
                                      <tr class="text-center">
                                          <td>
                                              <a target="_BLANK" class="p-2" style="color:navy; font-weight: 500;"
                                              href="<?=base_url()?>Reports/TodaysConversionDatas/<?=$uid?>/<?=$sdate?>/<?=$edate?>/<?= $bdconversion->status_change_id;?>/<?= $task_action_id;?>">
                                                  <?= $bdconversion->status_change ?>
                                              </a>
                                          </td>
                                          <td>
                                              <a target="_BLANK" class="badge badge-pill badge-primary p-2" 
                                              href="<?=base_url()?>Reports/TodaysConversionDatas/<?=$uid?>/<?=$sdate?>/<?=$edate?>/<?= $bdconversion->status_change_id;?>/<?= $task_action_id;?>">
                                                  <?= $bdconversion->total_changes ?>
                                              </a>
                                          </td>
                                      </tr>
                                  <?php endforeach;
                              endif;
                              ?>
                          </tbody>
                 
                  </table>
                </div>
                <hr />
              </div>











              <hr>
              <div class="card">
              <div class="text-center" style="background: linear-gradient(to right, #ede7f6, #ffffff); border-radius: 12px; box-shadow: 0 4px 8px rgba(0,0,0,0.1); padding: 20px;">
                <h5>
                  <i>Total Complete in New Category</i><br>
                  <small>This section provides a breakdown of tasks based on their completion status.</small>
                </h5>
                <p><i><b><?= $sdate ?> To <?= $edate ?></b></i></p>
              </div>
              <hr>
              <div class="page-header" style="background: linear-gradient(to right, #ffffff, #ffffff); border-radius: 12px; box-shadow: 0 4px 8px rgba(0,0,0,0.1); padding: 20px;">
                <div class="table-responsive text-nowrap">
                  <table id="example4" class="table table-striped table-bordered" cellspacing="0" width="100%">
                    <thead class="thead-dark">
                     <tr>
                    <th>Sr.No</th>
                    <th>👤 Name</th>
                    <th>🏁 Total Company in Q1 20 Closure Funnel</th>
                    <th>✅ Complete Task in - Q1 20 Closure Funnel</th>
                    <th>🎯 Total Company in Potential Funnel For FY</th>
                    <th>✅ Complete Task in - Potential Funnel For FY</th>
                    <th>🌱 Total Company in To be Nurtured for FY</th>
                    <th>✅ Complete Task in - To be Nurtured for FY</th>
                    <th>🆕 Total Company in 50 New Lead Funnel</th>
                    <th>✅ Complete Task in - 50 New Lead Funnel</th>
                  </tr>
                    </thead>
                    <tbody>
                      <?php
                        $i = 1;
                        foreach ($user_category as $row) {
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
                          <a target="_BLANK" class="card-count-heading-new" style="color:<?=$backgroundColorNew;?>!important" href="<?=base_url()?>Reports/GetTeamTaskOnSelfOrOtherFunnelTaskLists/total_company_in_q1_twetenty_closure_funnel/<?=$meeting_user_id?>/<?=$sdate?>/<?=$edate?>/<?=$task_action_id?>/userwise">
                          <?= $row->total_company_in_q1_twetenty_closure_funnel; ?>
                          </a>
                        </td>
                        <td>
                          <a target="_BLANK" class="card-count-heading-new" style="color:<?=$backgroundColorNew;?>!important" href="<?=base_url()?>Reports/GetTeamTaskOnSelfOrOtherFunnelTaskLists/total_in_q1_twetenty_closure_funnel/<?=$meeting_user_id?>/<?=$sdate?>/<?=$edate?>/<?=$task_action_id?>/userwise">
                          <?= $row->total_in_q1_twetenty_closure_funnel; ?>
                          </a>
                        </td>
                        <td>
                          <a target="_BLANK" class="card-count-heading-new" style="color:<?=$backgroundColorNew;?>!important" href="<?=base_url()?>Reports/GetTeamTaskOnSelfOrOtherFunnelTaskLists/total_company_in_potential_funnel_for_fy/<?=$meeting_user_id?>/<?=$sdate?>/<?=$edate?>/<?=$task_action_id?>/userwise">
                          <?= $row->total_company_in_potential_funnel_for_fy; ?>
                          </a>
                        </td>
                        <td>
                          <a target="_BLANK" class="card-count-heading-new" style="color:<?=$backgroundColorNew;?>!important" href="<?=base_url()?>Reports/GetTeamTaskOnSelfOrOtherFunnelTaskLists/total_in_potential_funnel_for_fy/<?=$meeting_user_id?>/<?=$sdate?>/<?=$edate?>/<?=$task_action_id?>/userwise">
                          <?= $row->total_in_potential_funnel_for_fy; ?>
                          </a>
                        </td>
                        <td>
                          <a target="_BLANK" class="card-count-heading-new" style="color:<?=$backgroundColorNew;?>!important" href="<?=base_url()?>Reports/GetTeamTaskOnSelfOrOtherFunnelTaskLists/total_company_in_to_be_nurtured_for_fy/<?=$meeting_user_id?>/<?=$sdate?>/<?=$edate?>/<?=$task_action_id?>/userwise">
                          <?= $row->total_company_in_to_be_nurtured_for_fy; ?>
                          </a>
                        </td>
                        <td>
                          <a target="_BLANK" class="card-count-heading-new" style="color:<?=$backgroundColorNew;?>!important" href="<?=base_url()?>Reports/GetTeamTaskOnSelfOrOtherFunnelTaskLists/total_in_to_be_nurtured_for_fy/<?=$meeting_user_id?>/<?=$sdate?>/<?=$edate?>/<?=$task_action_id?>/userwise">
                          <?= $row->total_in_to_be_nurtured_for_fy; ?>
                          </a>
                        </td>
                        <td>
                          <a target="_BLANK" class="card-count-heading-new" style="color:<?=$backgroundColorNew;?>!important" href="<?=base_url()?>Reports/GetTeamTaskOnSelfOrOtherFunnelTaskLists/total_company_in_fifity_new_lead_funnel/<?=$meeting_user_id?>/<?=$sdate?>/<?=$edate?>/<?=$task_action_id?>/userwise">
                          <?= $row->total_company_in_fifity_new_lead_funnel; ?>
                          </a>
                        </td>
                        <td>
                          <a target="_BLANK" class="card-count-heading-new" style="color:<?=$backgroundColorNew;?>!important" href="<?=base_url()?>Reports/GetTeamTaskOnSelfOrOtherFunnelTaskLists/total_in_fifity_new_lead_funnel/<?=$meeting_user_id?>/<?=$sdate?>/<?=$edate?>/<?=$task_action_id?>/userwise">
                          <?= $row->total_in_fifity_new_lead_funnel; ?>
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
                  <i>Total Complete in Category</i><br>
                  <small>This section provides a breakdown of tasks based on their completion category.</small>
                </h5>
                <p><i><b><?= $sdate ?> To <?= $edate ?></b></i></p>
              </div>
              <hr>
              <div class="page-header" style="background: linear-gradient(to right, #ffffff, #ffffff); border-radius: 12px; box-shadow: 0 4px 8px rgba(0,0,0,0.1); padding: 20px;">
                <div class="table-responsive text-nowrap">
                  <table id="example7" class="table table-striped table-bordered" cellspacing="0" width="100%">
                    <thead class="thead-dark">
                      <tr>
                      <th>Sr.No</th>
                      <th>👤 Name</th>
                      <th>🎯 Potential Client</th>
                      <th>💰 Top Spender Client</th>
                      <th>⬆️ Upsell Client</th>
                      <th>🚀 Focus Funnel Client</th>
                      <th>🔑 Key Client</th>
                      <th>👍 Positive Key Client</th>
                      <th>⚡ Priority Client</th>
                    </tr>
                    </thead>
                    <tbody>
                      <?php
                        $i = 1;
                        foreach ($user_user_old_categorytime as $row) {
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
                          <a target="_BLANK" class="card-count-heading-new" style="color:<?=$backgroundColorNew;?>!important" href="<?=base_url()?>Reports/GetTeamTaskOnSelfOrOtherFunnelTaskLists/potential/<?=$meeting_user_id?>/<?=$sdate?>/<?=$edate?>/<?=$task_action_id?>/userwise">
                          <?= $row->potential; ?>
                          </a>
                        </td>
                        <td>
                          <a target="_BLANK" class="card-count-heading-new" style="color:<?=$backgroundColorNew;?>!important" href="<?=base_url()?>Reports/GetTeamTaskOnSelfOrOtherFunnelTaskLists/topspender/<?=$meeting_user_id?>/<?=$sdate?>/<?=$edate?>/<?=$task_action_id?>/userwise">
                          <?= $row->topspender; ?>
                          </a>
                        </td>
                        <td>
                          <a target="_BLANK" class="card-count-heading-new" style="color:<?=$backgroundColorNew;?>!important" href="<?=base_url()?>Reports/GetTeamTaskOnSelfOrOtherFunnelTaskLists/upsell_client/<?=$meeting_user_id?>/<?=$sdate?>/<?=$edate?>/<?=$task_action_id?>/userwise">
                          <?= $row->upsell_client; ?>
                          </a>
                        </td>
                        <td>
                          <a target="_BLANK" class="card-count-heading-new" style="color:<?=$backgroundColorNew;?>!important" href="<?=base_url()?>Reports/GetTeamTaskOnSelfOrOtherFunnelTaskLists/focus_funnel/<?=$meeting_user_id?>/<?=$sdate?>/<?=$edate?>/<?=$task_action_id?>/userwise">
                          <?= $row->focus_funnel; ?>
                          </a>
                        </td>
                        <td>
                          <a target="_BLANK" class="card-count-heading-new" style="color:<?=$backgroundColorNew;?>!important" href="<?=base_url()?>Reports/GetTeamTaskOnSelfOrOtherFunnelTaskLists/keycompany/<?=$meeting_user_id?>/<?=$sdate?>/<?=$edate?>/<?=$task_action_id?>/userwise">
                          <?= $row->keycompany; ?>
                          </a>
                        </td>
                        <td>
                          <a target="_BLANK" class="card-count-heading-new" style="color:<?=$backgroundColorNew;?>!important" href="<?=base_url()?>Reports/GetTeamTaskOnSelfOrOtherFunnelTaskLists/pkclient/<?=$meeting_user_id?>/<?=$sdate?>/<?=$edate?>/<?=$task_action_id?>/userwise">
                          <?= $row->pkclient; ?>
                          </a>
                        </td>
                        <td>
                          <a target="_BLANK" class="card-count-heading-new" style="color:<?=$backgroundColorNew;?>!important" href="<?=base_url()?>Reports/GetTeamTaskOnSelfOrOtherFunnelTaskLists/priorityc/<?=$meeting_user_id?>/<?=$sdate?>/<?=$edate?>/<?=$task_action_id?>/userwise">
                          <?= $row->priorityc; ?>
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
                  <i>Total Complete Status Wise Task Details</i><br>
                  <small>This section provides a breakdown of tasks based on their completion status.</small>
                </h5>
                <p><i><b><?= $sdate ?> To <?= $edate ?></b></i></p>
              </div>
              <hr>
              <div class="page-header" style="background: linear-gradient(to right, #ffffff, #ffffff); border-radius: 12px; box-shadow: 0 4px 8px rgba(0,0,0,0.1); padding: 20px;">
                <div class="table-responsive text-nowrap">
                  <table id="example3" class="table table-striped table-bordered" cellspacing="0" width="100%">
                    <thead class="thead-dark">
                     <tr>
                        <th>Sr.No</th>
                        <th>👤 Name</th>
                        <th>📂 Open</th>
                        <th>📞 Reachout</th>
                        <th>📅 Tentative</th>
                        <th>⏳ Will do Later</th>
                        <th>❌ Not Interested</th>
                        <th>👍 Positive</th>
                        <th>✅ Closure</th>
                        <th>🔓 OPEN RPEM</th>
                        <th>🌟 Very Positive</th>
                        <th>📲 TTD-Reachout</th>
                        <th>📴 WNO-Reachout</th>
                        <th>📈 Positive-NAP</th>
                        <th>🚀 Very Positive-NAP</th>
                        <th>🎉 On-Boarded</th>
                      </tr>
                    </thead>
                    <tbody>
                      <?php
                        $i = 1;
                        foreach ($user_wise_status as $row) {
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
                          <a target="_BLANK" class="card-count-heading-new" style="color:<?=$backgroundColorNew;?>!important" href="<?=base_url()?>Reports/GetTeamTaskOnSelfOrOtherFunnelTaskLists/total_open/<?=$meeting_user_id?>/<?=$sdate?>/<?=$edate?>/<?=$task_action_id?>/userwise">
                          <?= $row->open; ?>
                          </a>
                        </td>
                        <td>
                          <a target="_BLANK" class="card-count-heading-new" style="color:<?=$backgroundColorNew;?>!important" href="<?=base_url()?>Reports/GetTeamTaskOnSelfOrOtherFunnelTaskLists/total_reachout/<?=$meeting_user_id?>/<?=$sdate?>/<?=$edate?>/<?=$task_action_id?>/userwise">
                          <?= $row->reachout; ?>
                          </a>
                        </td>
                        <td>
                          <a target="_BLANK" class="card-count-heading-new" style="color:<?=$backgroundColorNew;?>!important" href="<?=base_url()?>Reports/GetTeamTaskOnSelfOrOtherFunnelTaskLists/total_tentative/<?=$meeting_user_id?>/<?=$sdate?>/<?=$edate?>/<?=$task_action_id?>/userwise">
                          <?= $row->tentative; ?>
                          </a>
                        </td>
                        <td>
                          <a target="_BLANK" class="card-count-heading-new" style="color:<?=$backgroundColorNew;?>!important" href="<?=base_url()?>Reports/GetTeamTaskOnSelfOrOtherFunnelTaskLists/total_will_do_later/<?=$meeting_user_id?>/<?=$sdate?>/<?=$edate?>/<?=$task_action_id?>/userwise">
                          <?= $row->will_do_later; ?>
                          </a>
                        </td>
                        <td>
                          <a target="_BLANK" class="card-count-heading-new" style="color:<?=$backgroundColorNew;?>!important" href="<?=base_url()?>Reports/GetTeamTaskOnSelfOrOtherFunnelTaskLists/total_not_interested/<?=$meeting_user_id?>/<?=$sdate?>/<?=$edate?>/<?=$task_action_id?>/userwise">
                          <?= $row->not_interested; ?>
                          </a>
                        </td>
                        <td>
                          <a target="_BLANK" class="card-count-heading-new" style="color:<?=$backgroundColorNew;?>!important" href="<?=base_url()?>Reports/GetTeamTaskOnSelfOrOtherFunnelTaskLists/total_positive/<?=$meeting_user_id?>/<?=$sdate?>/<?=$edate?>/<?=$task_action_id?>/userwise">
                          <?= $row->positive; ?>
                          </a>
                        </td>
                        <td>
                          <a target="_BLANK" class="card-count-heading-new" style="color:<?=$backgroundColorNew;?>!important" href="<?=base_url()?>Reports/GetTeamTaskOnSelfOrOtherFunnelTaskLists/total_closure/<?=$meeting_user_id?>/<?=$sdate?>/<?=$edate?>/<?=$task_action_id?>/userwise">
                          <?= $row->closure; ?>
                          </a>
                        </td>
                        <td>
                          <a target="_BLANK" class="card-count-heading-new" style="color:<?=$backgroundColorNew;?>!important" href="<?=base_url()?>Reports/GetTeamTaskOnSelfOrOtherFunnelTaskLists/total_open_rpem/<?=$meeting_user_id?>/<?=$sdate?>/<?=$edate?>/<?=$task_action_id?>/userwise">
                          <?= $row->open_rpem; ?>
                          </a>
                        </td>
                        <td>
                          <a target="_BLANK" class="card-count-heading-new" style="color:<?=$backgroundColorNew;?>!important" href="<?=base_url()?>Reports/GetTeamTaskOnSelfOrOtherFunnelTaskLists/total_very_positive/<?=$meeting_user_id?>/<?=$sdate?>/<?=$edate?>/<?=$task_action_id?>/userwise">
                          <?= $row->very_positive; ?>
                          </a>
                        </td>
                        <td>
                          <a target="_BLANK" class="card-count-heading-new" style="color:<?=$backgroundColorNew;?>!important" href="<?=base_url()?>Reports/GetTeamTaskOnSelfOrOtherFunnelTaskLists/total_ttd_reachout/<?=$meeting_user_id?>/<?=$sdate?>/<?=$edate?>/<?=$task_action_id?>/userwise">
                          <?= $row->ttd_reachout; ?>
                          </a>
                        </td>
                        <td>
                          <a target="_BLANK" class="card-count-heading-new" style="color:<?=$backgroundColorNew;?>!important" href="<?=base_url()?>Reports/GetTeamTaskOnSelfOrOtherFunnelTaskLists/total_wno_reachout/<?=$meeting_user_id?>/<?=$sdate?>/<?=$edate?>/<?=$task_action_id?>/userwise">
                          <?= $row->wno_reachout; ?>
                          </a>
                        </td>
                        <td>
                          <a target="_BLANK" class="card-count-heading-new" style="color:<?=$backgroundColorNew;?>!important" href="<?=base_url()?>Reports/GetTeamTaskOnSelfOrOtherFunnelTaskLists/total_positive_nap/<?=$meeting_user_id?>/<?=$sdate?>/<?=$edate?>/<?=$task_action_id?>/userwise">
                          <?= $row->positive_nap; ?>
                          </a>
                        </td>
                        <td>
                          <a target="_BLANK" class="card-count-heading-new" style="color:<?=$backgroundColorNew;?>!important" href="<?=base_url()?>Reports/GetTeamTaskOnSelfOrOtherFunnelTaskLists/total_very_positive_nap/<?=$meeting_user_id?>/<?=$sdate?>/<?=$edate?>/<?=$task_action_id?>/userwise">
                          <?= $row->very_positive_nap; ?>
                          </a>
                        </td>
                        <td>
                          <a target="_BLANK" class="card-count-heading-new" style="color:<?=$backgroundColorNew;?>!important" href="<?=base_url()?>Reports/GetTeamTaskOnSelfOrOtherFunnelTaskLists/total_on_boarded/<?=$meeting_user_id?>/<?=$sdate?>/<?=$edate?>/<?=$task_action_id?>/userwise">
                          <?= $row->on_boarded; ?>
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
                  <i>Total Complete Partner Wise Task Details</i><br>
                  <small>This section provides a breakdown of tasks based on their completion partner.</small>
                </h5>
                <p><i><b><?= $sdate ?> To <?= $edate ?></b></i></p>
              </div>
              <hr>
              <div class="page-header" style="background: linear-gradient(to right, #ffffff, #ffffff); border-radius: 12px; box-shadow: 0 4px 8px rgba(0,0,0,0.1); padding: 20px;">
                <div class="table-responsive text-nowrap">
                  <table id="example9" class="table table-striped table-bordered" cellspacing="0" width="100%">
                    <thead class="thead-dark">
                     <tr>
                        <th>Sr.No</th>
                        <th>👤 Name</th>
                        <th>🏢 Corporate</th>
                        <th>🏛 PSU</th>
                        <th>🤝 NGO</th>
                        <th>🏫 Private School</th>
                        <th>🧍 Individual</th>
                        <th>🏛️ Govt Off</th>
                        <th>📦 Other</th>
                        <th>💻 Online</th>
                        <th>🧪 STEM School</th>
                        <th>🏛 Foundation</th>
                        <th>🌐 MNC</th>
                        <th>📞 HO Leads</th>
                        <th>🏞 DMF</th>
                        <th>🎖️ Elected Representatives</th>
                      </tr>
                    </thead>
                    <tbody>
                      <?php
                        $i = 1;
                        foreach ($partnerWiseData_user_wise as $row) {
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
                          <a target="_BLANK" class="card-count-heading-new" style="color:<?=$backgroundColorNew;?>!important" href="<?=base_url()?>Reports/GetTeamTaskOnSelfOrOtherFunnelTaskLists/corporate/<?=$meeting_user_id?>/<?=$sdate?>/<?=$edate?>/<?=$task_action_id?>/userwise">
                          <?= $row->corporate; ?>
                          </a>
                        </td>
                        <td>
                          <a target="_BLANK" class="card-count-heading-new" style="color:<?=$backgroundColorNew;?>!important" href="<?=base_url()?>Reports/GetTeamTaskOnSelfOrOtherFunnelTaskLists/PSU/<?=$meeting_user_id?>/<?=$sdate?>/<?=$edate?>/<?=$task_action_id?>/userwise">
                          <?= $row->PSU; ?>
                          </a>
                        </td>
                        <td>
                          <a target="_BLANK" class="card-count-heading-new" style="color:<?=$backgroundColorNew;?>!important" href="<?=base_url()?>Reports/GetTeamTaskOnSelfOrOtherFunnelTaskLists/NGO/<?=$meeting_user_id?>/<?=$sdate?>/<?=$edate?>/<?=$task_action_id?>/userwise">
                          <?= $row->NGO; ?>
                          </a>
                        </td>
                        <td>
                          <a target="_BLANK" class="card-count-heading-new" style="color:<?=$backgroundColorNew;?>!important" href="<?=base_url()?>Reports/GetTeamTaskOnSelfOrOtherFunnelTaskLists/Private_School/<?=$meeting_user_id?>/<?=$sdate?>/<?=$edate?>/<?=$task_action_id?>/userwise">
                          <?= $row->Private_School; ?>
                          </a>
                        </td>
                        <td>
                          <a target="_BLANK" class="card-count-heading-new" style="color:<?=$backgroundColorNew;?>!important" href="<?=base_url()?>Reports/GetTeamTaskOnSelfOrOtherFunnelTaskLists/Individual/<?=$meeting_user_id?>/<?=$sdate?>/<?=$edate?>/<?=$task_action_id?>/userwise">
                          <?= $row->Individual; ?>
                          </a>
                        </td>
                        <td>
                          <a target="_BLANK" class="card-count-heading-new" style="color:<?=$backgroundColorNew;?>!important" href="<?=base_url()?>Reports/GetTeamTaskOnSelfOrOtherFunnelTaskLists/Govt_Off/<?=$meeting_user_id?>/<?=$sdate?>/<?=$edate?>/<?=$task_action_id?>/userwise">
                          <?= $row->Govt_Off; ?>
                          </a>
                        </td>
                        <td>
                          <a target="_BLANK" class="card-count-heading-new" style="color:<?=$backgroundColorNew;?>!important" href="<?=base_url()?>Reports/GetTeamTaskOnSelfOrOtherFunnelTaskLists/Other/<?=$meeting_user_id?>/<?=$sdate?>/<?=$edate?>/<?=$task_action_id?>/userwise">
                          <?= $row->Other; ?>
                          </a>
                        </td>
                        <td>
                          <a target="_BLANK" class="card-count-heading-new" style="color:<?=$backgroundColorNew;?>!important" href="<?=base_url()?>Reports/GetTeamTaskOnSelfOrOtherFunnelTaskLists/Online/<?=$meeting_user_id?>/<?=$sdate?>/<?=$edate?>/<?=$task_action_id?>/userwise">
                          <?= $row->Online; ?>
                          </a>
                        </td>
                        <td>
                          <a target="_BLANK" class="card-count-heading-new" style="color:<?=$backgroundColorNew;?>!important" href="<?=base_url()?>Reports/GetTeamTaskOnSelfOrOtherFunnelTaskLists/STEM_School/<?=$meeting_user_id?>/<?=$sdate?>/<?=$edate?>/<?=$task_action_id?>/userwise">
                          <?= $row->STEM_School; ?>
                          </a>
                        </td>
                        <td>
                          <a target="_BLANK" class="card-count-heading-new" style="color:<?=$backgroundColorNew;?>!important" href="<?=base_url()?>Reports/GetTeamTaskOnSelfOrOtherFunnelTaskLists/Foundation/<?=$meeting_user_id?>/<?=$sdate?>/<?=$edate?>/<?=$task_action_id?>/userwise">
                          <?= $row->Foundation; ?>
                          </a>
                        </td>
                        <td>
                          <a target="_BLANK" class="card-count-heading-new" style="color:<?=$backgroundColorNew;?>!important" href="<?=base_url()?>Reports/GetTeamTaskOnSelfOrOtherFunnelTaskLists/MNC/<?=$meeting_user_id?>/<?=$sdate?>/<?=$edate?>/<?=$task_action_id?>/userwise">
                          <?= $row->MNC; ?>
                          </a>
                        </td>
                        <td>
                          <a target="_BLANK" class="card-count-heading-new" style="color:<?=$backgroundColorNew;?>!important" href="<?=base_url()?>Reports/GetTeamTaskOnSelfOrOtherFunnelTaskLists/HO_Leads/<?=$meeting_user_id?>/<?=$sdate?>/<?=$edate?>/<?=$task_action_id?>/userwise">
                          <?= $row->HO_Leads; ?>
                          </a>
                        </td>
                        <td>
                          <a target="_BLANK" class="card-count-heading-new" style="color:<?=$backgroundColorNew;?>!important" href="<?=base_url()?>Reports/GetTeamTaskOnSelfOrOtherFunnelTaskLists/DMF/<?=$meeting_user_id?>/<?=$sdate?>/<?=$edate?>/<?=$task_action_id?>/userwise">
                          <?= $row->DMF; ?>
                          </a>
                        </td>
                        <td>
                          <a target="_BLANK" class="card-count-heading-new" style="color:<?=$backgroundColorNew;?>!important" href="<?=base_url()?>Reports/GetTeamTaskOnSelfOrOtherFunnelTaskLists/Elected_Representatives/<?=$meeting_user_id?>/<?=$sdate?>/<?=$edate?>/<?=$task_action_id?>/userwise">
                          <?= $row->Elected_Representatives; ?>
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
                  <i>Task Complete Between Time</i><br>
                  <small>This section shows the tasks that were completed within the specified time intervals.</small>
                </h5>
              </div>
              <hr>
              <?php // dd($user_time); ?>
              <div class="page-header" style="background: linear-gradient(to right, #ffffff, #ffffff); border-radius: 12px; box-shadow: 0 4px 8px rgba(0,0,0,0.1); padding: 20px;">
                <div class="table-responsive text-nowrap">
                  <table id="example5" class="table table-striped table-bordered" cellspacing="0" width="100%">
                    <thead class="thead-dark">
                    <tr>
                        <th>🔢 Sr.No</th>
                        <th>👤 Name</th>
                        <th>⏱️ 0 to 59 Seconds</th>
                        <th>🕐 1 to 5 Minutes</th>
                        <th>🕒 5 to 10 Minutes</th>
                        <th>🕓 10 to 15 Minutes</th>
                        <th>🕔 16 to 30 Minutes</th>
                        <th>🕕 30 to 50 Minutes</th>
                        <th>🕖 50 to 60 Minutes</th>
                        <th>⏳ More than 60 Minutes</th>
                      </tr>
                    </thead>
                    <tbody>
                      <?php
                        $i = 1;
                        foreach ($user_time as $row) {
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
                          <a target="_BLANK" class="card-count-heading-new" style="color:<?=$backgroundColorNew;?>!important" href="<?=base_url()?>Reports/GetTeamTaskOnSelfOrOtherFunnelTaskLists/zero_to_fifty_nine_seconds/<?=$meeting_user_id?>/<?=$sdate?>/<?=$edate?>/<?=$task_action_id?>/userwise">
                          <?= $row->zero_to_fifty_nine_seconds; ?>
                          </a>
                        </td>
                        <td>
                          <a target="_BLANK" class="card-count-heading-new" style="color:<?=$backgroundColorNew;?>!important" href="<?=base_url()?>Reports/GetTeamTaskOnSelfOrOtherFunnelTaskLists/one_to_five_minutes/<?=$meeting_user_id?>/<?=$sdate?>/<?=$edate?>/<?=$task_action_id?>/userwise">
                          <?= $row->one_to_five_minutes; ?>
                          </a>
                        </td>
                        <td>
                          <a target="_BLANK" class="card-count-heading-new" style="color:<?=$backgroundColorNew;?>!important" href="<?=base_url()?>Reports/GetTeamTaskOnSelfOrOtherFunnelTaskLists/five_to_ten_minutes/<?=$meeting_user_id?>/<?=$sdate?>/<?=$edate?>/<?=$task_action_id?>/userwise">
                          <?= $row->five_to_ten_minutes; ?>
                          </a>
                        </td>
                        <td>
                          <a target="_BLANK" class="card-count-heading-new" style="color:<?=$backgroundColorNew;?>!important" href="<?=base_url()?>Reports/GetTeamTaskOnSelfOrOtherFunnelTaskLists/ten_to_fifteen_minutes/<?=$meeting_user_id?>/<?=$sdate?>/<?=$edate?>/<?=$task_action_id?>/userwise">
                          <?= $row->ten_to_fifteen_minutes; ?>
                          </a>
                        </td>
                        <td>
                          <a target="_BLANK" class="card-count-heading-new" style="color:<?=$backgroundColorNew;?>!important" href="<?=base_url()?>Reports/GetTeamTaskOnSelfOrOtherFunnelTaskLists/sixteen_to_thirty_minutes/<?=$meeting_user_id?>/<?=$sdate?>/<?=$edate?>/<?=$task_action_id?>/userwise">
                          <?= $row->sixteen_to_thirty_minutes; ?>
                          </a>
                        </td>
                        <td>
                          <a target="_BLANK" class="card-count-heading-new" style="color:<?=$backgroundColorNew;?>!important" href="<?=base_url()?>Reports/GetTeamTaskOnSelfOrOtherFunnelTaskLists/thirty_one_to_fifty_minutes/<?=$meeting_user_id?>/<?=$sdate?>/<?=$edate?>/<?=$task_action_id?>/userwise">
                          <?= $row->thirty_one_to_fifty_minutes; ?>
                          </a>
                        </td>
                        <td>
                          <a target="_BLANK" class="card-count-heading-new" style="color:<?=$backgroundColorNew;?>!important" href="<?=base_url()?>Reports/GetTeamTaskOnSelfOrOtherFunnelTaskLists/fifty_one_to_sixty_minutes/<?=$meeting_user_id?>/<?=$sdate?>/<?=$edate?>/<?=$task_action_id?>/userwise">
                          <?= $row->fifty_one_to_sixty_minutes; ?>
                          </a>
                        </td>
                        <td>
                          <a target="_BLANK" class="card-count-heading-new" style="color:<?=$backgroundColorNew;?>!important" href="<?=base_url()?>Reports/GetTeamTaskOnSelfOrOtherFunnelTaskLists/greater_than_one_sixty_minutes/<?=$meeting_user_id?>/<?=$sdate?>/<?=$edate?>/<?=$task_action_id?>/userwise">
                          <?= $row->greater_than_one_sixty_minutes; ?>
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
              <div class="conatiner mt-1">
                <div class="text-center" style="background: linear-gradient(to right, #ede7f6, #ffffff); border-radius: 12px; box-shadow: 0 4px 8px rgba(0,0,0,0.1); padding: 20px;">
                  <h5><i>Task Type Details</i></h5>
                  <small>This section provides detailed information about the type of tasks being tracked or reviewed.</small>
                </div>
                <hr>
                <div class="row">
                  <?php  
                    foreach($tasktypeDatas as $tasktypeData){
                    $r = rand(150, 255);
                    $g = rand(150, 255);
                    $b = rand(150, 255);
                    $backgroundColor = "rgba($r, $g, $b,.4)";
                    
                    $hue                = rand(0, 360); // Full color wheel
                    $saturation         = rand(60, 100); // High saturation for rich colors
                    $lightness          = rand(30, 45); // Low lightness for a deeper tone
                    $backgroundColorNew = "hsl($hue, $saturation%, $lightness%)";
                      
                      ?>
                  <div class="col-md-2 mb-3">
                    <div class="card text-center shadow" style="background-color: <?= $backgroundColor ?>; color: <?= $backgroundColorNew ?>;">
                      <div class="card-body" style="color:<?=$backgroundColorNew;?>!important">
                        <h4 class="card-title"><b><?=$tasktypeData->name?></b></h4>
                        <br>
                        <h5 class="card-text"><?=$tasktypeData->yest?> Minutes</h5>
                      </div>
                    </div>
                  </div>
                  <?php }?>
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
      $("#example21").DataTable({
        "responsive": false, "lengthChange": false, "autoWidth": false,
        "buttons": ["csv", "excel", "pdf"]
      }).buttons().container().appendTo('#example21_wrapper .col-md-6:eq(0)');
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
    </script>
  </body>
</html>
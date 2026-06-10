<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Task Check Management Star Rating | STEM APP | WebAPP</title>
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
      th.text-capitalize:nth-child(1),
      td:nth-child(1),
      th.text-capitalize:nth-child(2),
      td:nth-child(2) {
      position: sticky;
      left: 0;
      color: white;
      z-index: 10;
      }
      tbody tr td:nth-child(1),
      tbody tr td:nth-child(2) {
      background-color: white;
      color: black;
      }
      .card.text-center {
      min-height: 300px;
      }
       .star-rating {
        direction: rtl;
        display: inline-block;
        unicode-bidi: bidi-override;
        }
        .star-rating > input {
        display: none;
        }
        .star-rating > label {
        color: #ccc;
        font-size: 1.5em;
        cursor: pointer;
        }
        .star-rating > input:checked ~ label,
        .star-rating > input:checked ~ label ~ label {
        color: #ffc107;
        }
        .star-rating > label:hover,
        .star-rating > label:hover ~ label {
        color: #ffc107;
        }
        strong {
    color: navy;
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
                      <h1 class="m-0 premium-color-1" style="color: #ff009b;">
                        🌟 Task Check Management Star Rating ✨
                      </h1>
                      <p class="premium-color-2" style="color: #ff0000;">
                        ✅ Efficiently track and rate your daily productivity with a star-based system! 🌟 
                        Organize tasks, set priorities, and celebrate progress—all in one place. 
                        🚀 Boost focus, reduce stress, and achieve more every day!
                      </p>
                      <p class="premium-color-2" style="color: #2c00ee;">
                        <strong>📅 <mark><?=$sdate ?> To <?=$edate ?></mark></strong>
                      </p>
                    </div>

                  </div>
                </div>
              </div>
            </div>
            <br>
            <?php 
   
            /*
              <div class="row mb-2">
                <hr>
                <div class="text-right-data text-center" style="background: linear-gradient(to right, #d7faff, #ffffff); border-radius: 12px; box-shadow: 0 4px 8px rgba(0,0,0,0.1); padding: 20px;">
                  <?php 
                $clusterPstDatas  = $this->Menu_model->GetAdminActiveTeam($user['user_id']);
                $taskActions      = $this->Menu_model->get_action();
                $userRoles        = $this->Menu_model->GetActiveDepartmentList($uid);
                ?>
            <div class="col text-center formcenterData">
              <div>
                <hr>
                <form action="<?=base_url()?>Reports/DayManagementStarRating" method="post" class="mt-3">
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
        */ ?>
        <div class="row mt-3 mb-2">
          <div class="col-md-12 text-center">
            <button class="custom-btn btn-11 partnearray p-2" id="downloadPdf">Download PDF</button>
          </div>
        </div>
        <hr>
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
      // dd($liveMettingsTasks);
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
          
            
        $taskAddedStarListsCID  = $specialRemakrsDatas['taskAddedStarListsCID'];

          $totalStar = 0;
          foreach ($taskAddedStarListsCID as $obj) {
              $totalStar += (int)$obj->star;
          }

          ?>
        
        <div class="text-center" style="background: linear-gradient(to right, #ede7f6, #ffffff); border-radius: 12px; box-shadow: 0 4px 8px rgba(0,0,0,0.1); padding: 20px;">
          <h5><i>✨ Task Star Rating Details ✨</i></h5>
          <small>
          Efficiently track and rate your daily productivity with a star-based system! 🌟 Organize tasks, set priorities, and celebrate progress—all in one place. Boost focus, reduce stress, and achieve more every day! 🚀
          </small>
          <?php 
            
            
             ?>
          <br>
          <p><small><mark><?=$sdate;?></mark> - to - <mark><?=$edate;?></mark></small></p>
          <p><b><mark><?=$taskAddedStarListsCID[0]->task_user_name?></mark></b></p>
          <p><b><mark><?=$taskAddedStarListsCID[0]->compname?> (<?=$taskAddedStarListsCID[0]->cid?>) </mark> </b></p>
          <p><b><mark><?=$taskAddedStarListsCID[0]->task_name?></mark></b></p>
        </div>
        <hr>
        <br>
        <hr>
        <div class="body-content">
          <?php
            $taskDetails = $taskAddedStarListsCID[0];
                ?>
          <div class="card" style="background: linear-gradient(to right, #ede7f6, #ffffff); border-radius: 12px; box-shadow: 0 4px 8px rgba(0,0,0,0.1); padding: 20px;">
            <div class="text-center">
            <h3>🗂️ Task Details</h3>
            <hr>
            <h5 class="card-text"><strong>👤 User Name:</strong> <?php echo $taskDetails->task_user_name; ?></h5>
            <hr>
          </div>
            <div class="card-body">
              <div class="row">
               <div class="col-md-4">
                  <p class="card-text"><strong>🏢 Company Name:</strong> <?php echo $taskDetails->compname; ?></p>
                  <p class="card-text"><strong>📝 Task Name:</strong> <?php echo $taskDetails->task_name; ?></p>
                  <p class="card-text"><strong>✅ Action Taken:</strong> <?php echo $taskDetails->actontaken; ?></p>
                  <p class="card-text"><strong>🎯 Purpose Achieved:</strong> <?php echo $taskDetails->purpose_achieved; ?></p>
                  <p class="card-text"><strong>💬 Task Remarks:</strong> <?php echo $taskDetails->task_remarks; ?></p>
                </div>
                <div class="col-md-4">
                  <p class="card-text"><strong>📅 Appointment Date & Time:</strong> <?php echo $taskDetails->appointmentdatetime; ?></p>
                  <p class="card-text"><strong>⏳ Initiated Date & Time:</strong> <?php echo $taskDetails->initiateddt; ?></p>
                  <p class="card-text"><strong>🔄 Updated At:</strong> <?php echo $taskDetails->updated_at; ?></p>
                </div>
                <div class="col-md-4">
                  <p class="card-text"><strong>🗓️ Planned On Status:</strong> <?php echo $taskDetails->planned_status; ?></p>
                  <p class="card-text"><strong>📌 Update On Status:</strong> <?php echo $taskDetails->update_new_status; ?></p>
                  <p class="card-text"><strong>🚦 Current Status:</strong> <?php echo $taskDetails->current_status; ?></p>
                  <?php if(in_array($taskDetails->actiontype_id,[3,4,17,22])){ ?>
                    <p class="card-text"><strong>🤝 Meeting Type:</strong> <?php echo $taskDetails->mtype; ?></p>
                    <p class="card-text"><strong>📝 Meeting MOM:</strong> <?php echo $taskDetails->mom; ?></p>
                  <?php } ?>
                  <p class="card-text"><strong>🏁 Task Complete Status:</strong> 
                    <?php if($taskDetails->nextCFID == 0){ ?>
                      <span class='bg-warning p-1'> ⏳ Pending</span>
                    <?php }else{ ?> 
                      <span class='bg-success p-1'> ✅ Complete</span>
                    <?php } ?>
                  </p>
                </div>

                <div class="col-md-12 text-center">
                <hr>
                  <p class="text-center">
                    <strong>⭐ Total Star:</strong> <?php echo $totalStar; ?> 🌟
                  </p>
                  <hr>
                </div>
               
                <div class="col-md-12 text-center">
                  <p class="text-center"><strong>📩 Reminder Message:</strong></p><hr>
                  <?php 
                  if(sizeof($reminderMessageTasks) > 0){ ?>


                  <div class="table-responsive">
  <table class="table table-striped table-bordered table-hover table-sm">
    <thead class="thead-dark">
       <tr>
        <th>#</th>
        <th>📩 Reminder Message</th>
        <th>👤 Reminder By</th>
        <th>⏰ Reminder Time</th>
      </tr>
    </thead>
    <tbody>
      <?php $z=1; foreach($reminderMessageTasks as $reminderMessageTask){ ?> 
        <tr>
          <td><?= $z ?></td>
          <td><?= $reminderMessageTask->reminder_remarks ?></td>
          <td><?= $reminderMessageTask->reminderby_name ?></td>
          <td><?= $reminderMessageTask->created_at ?></td>
        </tr>
      <?php $z++; } ?> 
    </tbody>
  </table>
</div>


                
                    

                  <?php  }else{ ?> 
                   <p>💭 No Reminder</p>
                  <?php }?>
                </div>
              </div>
              <div>
              </div>
            </div>
          </div>
       
          <div class="table-responsive text-nowrap">
            <table id="example6" class="table table-striped table-bordered" cellspacing="0" width="100%">
              <thead class="thead-dark">
               <tr>
                <th class="text-capitalize">🔢 Sr. No</th>
                <th class="text-capitalize">❓ Question</th>
                <th class="text-capitalize">⭐ Total Star</th>
                <th class="text-capitalize">💬 Remarks</th>
                <th class="text-capitalize">👤 Star Given By</th>
              </tr>
              </thead>
              <tbody>
                <?php
                  $i = 1;
                  foreach ($taskAddedStarListsCID as $row) {
                  
                    $r = rand(150, 255);
                    $g = rand(150, 255);
                    $b = rand(150, 255);
                    $backgroundColor = "rgba($r, $g, $b,.2)";
                  
                    $hue = rand(0, 360); // Full color wheel
                    $saturation = rand(60, 100); // High saturation for rich colors
                    $lightness = rand(30, 45); // Low lightness for a deeper tone
                    
                    $backgroundColorNew = "hsl($hue, $saturation%, $lightness%)";
                  
                    ?>
                <tr style="background-color: <?php echo $backgroundColor; ?>;color:<?=$backgroundColorNew;?>!important" title="<?=$meeting_task_id?>">
                  <td><?= htmlspecialchars($i) ?></td>
                  <td class="text-capitalize"><?= htmlspecialchars($row->question) ?></td>
                  <td><?= htmlspecialchars($row->star) ?></td>
                  <td><?= htmlspecialchars($row->remarks) ?></td>
                  <td><?= htmlspecialchars($row->star_given_by) ?></td>
                </tr>
                <?php $i++; } ?>
              </tbody>
            </table>
          </div>
          <br>
          <br>
          <br>
        </div>










        

<div id="totalContentArea">
                  <style>
                    .taskusercardData {
                    transition: transform 0.3s, box-shadow 0.3s;
                    border-radius: 10px;
                    border: none;
                    cursor: pointer;
                    }
                    .taskusercardData:hover {
                    transform: translateY(-5px);
                    box-shadow: 0 10px 20px rgba(0, 0, 0, 0.2);
                    }
                    .card-body {
                    padding: 20px;
                    }
                    .tab-content {
                    display: none;
                    padding: 20px;
                    background-color: #f8f9fa;
                    border-radius: 10px;
                    margin-top: 20px;
                    animation: fadeIn 0.5s;
                    }
                    .tab-content.active {
                    display: block;
                    }
                    @keyframes fadeIn {
                    from {
                    opacity: 0;
                    }
                    to {
                    opacity: 1;
                    }
                    }
                  </style>
                
                  <div class="tab-content11">
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
                    
                      ?>
                      <?php 
  // dd($liveMettingsTasks);
?>

                    <div class="row">
                      <?php
                        $j=1;
                        foreach ($liveMettingsTasks as $task):
                        
                          
                          $selectby = $task->selectby;
                        
                        ?>
                      <div class="col-md-12 mb-2">
                        <div class="card meetingslist-card <?=$alertClass;?>" style="background: <?= $gradientColors[$index++ % count($gradientColors)] ?>; border-radius: 14px; box-shadow: 0 4px 12px rgba(0,0,0,0.1); padding: 20px;">
                          <div class="text-left">
                            <center>
                              <h3 class="mb-1 badge badge-pill badge-dark" style="font-weight: 600; font-size: 1.1rem;"><?= htmlspecialchars($j) ?></h3>
                              <hr style="width: 20%;" class="cardline <?=$hrAClass?>">
                              <h4 class="mb-1" style="font-weight: 600;">🏷️ 
                                <a target="_BLANK" href="<?=base_url();?>/Menu/CompanyDetails/<?=$task->cid?>">
                                <?=$task->compname?> (<?=$task->cid?>)
                                </a>
                              </h4>
                              <hr style="width: 40%;" class="cardline <?=$hrAClass?>">
                              <h5 class="mb-1" style="font-weight: 600;">🏷️ <?=$task->task_name?></h5>
                              <hr style="width: 60%;" class="cardline <?=$hrAClass?>">
                            </center>
                            <div class="table-responsive">
                              <table id="table_id__1333" class="table table-striped table-bordered" cellspacing="0" width="100%">
                                <thead class="thead-dark">
                                 <tr>
                                  <th>📅 Date</th>
                                  <th>❓ Question</th>
                                  <th style="width: 200px;">⭐ Star Rating</th>
                                </tr>
                                </thead>
                                <tbody>
                                  <!-- Task User -->
                                  <tr>
                                    <td><strong>Task User:</strong>  <?= htmlspecialchars($task->task_username) ?> (<span style="color: navy;"><?= $task->task_user_id ?></span>)</td>
                                    <td></td>
                                    <td></td>
                                  </tr>
                                  <!-- Plan Date Time -->
                                  <tr>
                                    <td><strong>Plan Date Time:</strong> <?= $task->appointmentdatetime ?></td>
                                    <td></td>
                                    <td></td>
                                  </tr>
                                  <!-- Initiate Task -->
                                  <tr title="<?=$task->task_id?>">
                                    <td><strong>Initiate <?= $task->task_name ?>:</strong> 
                                      <?php 
                                        if (empty($task->task_initiateddt) || is_null($task->task_initiateddt)) {
                                          $appointment = new DateTime($task->task_updateddate);
                                          $appointment->modify("-2 minutes");
                                          $task_initiateddt = $appointment->format("Y-m-d H:i:s");
                                        
                                          } else {
                                              $task_initiateddt = $task->task_initiateddt;
                                          }
                                        
                                        ?>
                                      <?= $task_initiateddt ?>
                                    </td>
                                    <td></td>
                                    <td></td>
                                  </tr>
                                  <?php if($task->nextCFID !=0){ ?>
                                  <!-- Complete Task -->
                                  <tr>
                                    <td><strong>Complete <?= $task->task_name ?>:</strong> <?= $task->nextCFID != 0 ? $task->task_updateddate : "NA" ?></td>
                                    <td></td>
                                    <td>
                                    </td>
                                  </tr>
                                  <?php } ?>
                                  <?php if($task->nextCFID !=0){ ?>
                                  <!-- Complete Task -->
                                  <tr>
                                    <td><strong>Time Diffrence Between Plan VS Initiate</td>
                                    <td>
                                      <?php 
                                        $start_ap = new DateTime($task->appointmentdatetime);
                                        // $start = new DateTime($task->task_updateddate);
                                        $end_ap = new DateTime($task_initiateddt);
                                        
                                        $diff_pi = $end_ap->diff($start_ap);
                                        
                                        echo $diff_pi->format('%h hours %i minutes %s seconds');
                                        // Output: 0 hours 2 minutes 0 seconds
                                        ?>
                                    </td>
                                    <td>
                                      <?php 
                                        $checkStar1 = $this->Menu_model->GetGivedTaskStarRatingsCountAfterCheck($task->task_id,$sdate,"How would you rate the completion of the task?");
  
                                        if($checkStar1 == 0){
                                        ?>
                                      <span class="star-rating" data-paragraph-text="Complete <?= $task->task_name ?>: <?= $task->nextCFID != 0 ? $task->task_updateddate : 'NA' ?>" data-paragraph-question="How would you rate the completion of the task?">
                                      <?php for ($i = 5; $i >= 1; $i--): ?>
                                      <input type="radio" id="star<?= $i ?>_<?= $task->task_id ?>_Diffrence_Between_Initiate_task" name="rating_<?= $task->task_id ?>_Diffrence_Between_Initiate_task" value="<?= $i ?>" data-task-user-id="<?= $task->task_id ?>" />
                                      <label for="star<?= $i ?>_<?= $task->task_id ?>_Diffrence_Between_Initiate_task" title="<?= $i ?> stars">★</label>
                                      <?php endfor; ?>
                                      </span>
                                      <?php }else{ 
                                        echo "<span class='text-warning'>";
                                        for($star = 1; $star <= $checkStar1; $star++){
                                            echo "★";
                                        }
                                         echo "</span>";
                                        } ?>
                                    </td>
                                  </tr>
                                  <?php } ?>
                                  <?php if($task->nextCFID !=0){ ?>
                                  <!-- Complete Task -->
                                  <tr>
                                    <td><strong>Time Diffrence Between Initiate VS completed ?</td>
                                    <td>
                                      <?php 
                                        $start_ap = new DateTime($task->task_updateddate);
                                        $end_ap = new DateTime($task_initiateddt);
                                        
                                        $diff_pi = $end_ap->diff($start_ap);
                                        
                                        echo $diff_pi->format('%h hours %i minutes %s seconds');
                                        // Output: 0 hours 2 minutes 0 seconds
                                        ?>
                                    </td>
                                    <td>
                                      <?php 
                                        $checkStar1 = $this->Menu_model->GetGivedTaskStarRatingsCountAfterCheck($task->task_id,$sdate,"How would you rate the time difference between Initiated and Completed?");
                                        if($checkStar1 == 0){
                                        ?>
                                      <span class="star-rating" data-paragraph-text="Complete <?= $task->task_name ?>: <?= $task->nextCFID != 0 ? $task->task_updateddate : 'NA' ?>" data-paragraph-question="How would you rate the time difference between Initiated and Completed?">
                                      <?php for ($i = 5; $i >= 1; $i--): ?>
                                      <input type="radio" id="star<?= $i ?>_<?= $task->task_id ?>_complete_task" name="rating_<?= $task->task_id ?>_complete_task" value="<?= $i ?>" data-task-user-id="<?= $task->task_id ?>" />
                                      <label for="star<?= $i ?>_<?= $task->task_id ?>_complete_task" title="<?= $i ?> stars">★</label>
                                      <?php endfor; ?>
                                      </span>
                                      <?php }else{ 
                                        echo "<span class='text-warning'>";
                                        for($star = 1; $star <= $checkStar1; $star++){
                                            echo "★";
                                        }
                                         echo "</span>";
                                        } ?>
                                    </td>
                                  </tr>
                                  <?php } ?>
                                  <!-- Planner Filter Used -->
                                  <tr>
                                    <td><strong>Planner Filter Used:</strong>
                                      <?= !empty($selectby) ? $selectby : 'NA'; ?>
                                    </td>
                                    <td></td>
                                    <td>
                                    </td>
                                  </tr>
                                  <!-- Current Status -->
                                  <tr>
                                    <td><strong>Current Status:</strong> <?= $task->current_status ?></td>
                                    <td></td>
                                    <td></td>
                                  </tr>
                                  <!-- Current Status -->
                                  <tr>
                                    <td><strong>Task Planned on Status:</strong> <?= $task->task_time_status ?></td>
                                    <td></td>
                                    <td></td>
                                  </tr>


                                   <?php if($task->nextCFID != 0){ ?>
                                  <!-- Update Status -->
                                  <?php if($task->task_time_new_status !==''): ?>
                                  <tr>
                                    <td><strong>Update Status:</strong> <?= !empty($task->task_time_new_status) ? $task->task_time_new_status : 'NA' ?></td>
                                    <td></td>
                                    <td></td>
                                  </tr>
                                  <?php endif; } ?>
                                  <!-- Approved By -->



                                  <tr>
                                    <td><strong>Approved By:</strong> <?= !empty($task->task_approved_by) ? $task->task_approved_by : 'NA' ?></td>
                                    <td></td>
                                    <td></td>
                                  </tr>
                                  <!-- Approved Date -->
                                  <tr>
                                    <td><strong>Approved Date:</strong>  <?= !empty($task->task_approved_date) ? $task->task_approved_date : 'NA' ?></td>
                                    <td></td>
                                    <td></td>
                                  </tr>
                                  <?php  
                                    $aftertasklists = $this->Menu_model->GetAfterTasksLists($task->task_id);
                                    if(sizeof($aftertasklists) > 0){ 
                                    ?>
                                  <tr>
                                    <td>
                                      <?php foreach($aftertasklists as $aftertasklist){ ?> 
                                      <strong>After task :</strong>  <?= $aftertasklist->after_task_name ?> <br>
                                      <strong>Task Completed :</strong>  
                                      <?php if($aftertasklist->nextCFID > 0){ ?> 
                                      <span class="bg-success p-1">Complete</span>
                                      <?php }else{ ?>
                                      <span class="bg-warning p-1">Pending</span> 
                                      <?php } ?>
                                      <br>
                                      <strong>Action Taken :</strong> <?=$aftertasklist->actontaken;?> <br>
                                      <strong>Purpose Achieved  :</strong> <?=$aftertasklist->purpose_achieved;?> <br>
                                      <strong>Appointment datetime :</strong> <?=$aftertasklist->appointmentdatetime;?> <br>
                                      <?php if($aftertasklist->nextCFID > 0){ ?> 
                                      <strong>Complete datetime :</strong> <?=$aftertasklist->updateddate;?> <br>
                                      <strong>Complete Remarks :</strong> <?=$aftertasklist->remarks;?> <br>
                                      <?php } ?>
                                      <hr>
                                      <?php } ?>
                                    </td>
                                    <td>How would you rate the auto task update?</td>
                                    <td>
                                      <?php 
                                        $checkStar1 = $this->Menu_model->GetGivedTaskStarRatingsCountAfterCheck($task->task_id,$sdate,"How would you rate the auto task update?");
                                        if($checkStar1 == 0){
                                        ?>
                                      <span class="star-rating" data-paragraph-text="Complete <?= $task->task_name ?>: <?= $task->nextCFID != 0 ? $task->task_updateddate : 'NA' ?>" data-paragraph-question="How would you rate the auto task update?">
                                      <?php for ($i = 5; $i >= 1; $i--): ?>
                                      <input type="radio" id="star<?= $i ?>_<?= $task->task_id ?>_autotaskc_task" name="rating_<?= $task->task_id ?>_autotaskc_task" value="<?= $i ?>" data-task-user-id="<?= $task->task_id ?>" />
                                      <label for="star<?= $i ?>_<?= $task->task_id ?>_autotaskc_task" title="<?= $i ?> stars">★</label>
                                      <?php endfor; ?>
                                      </span>
                                      <?php }else{ 
                                        echo "<span class='text-warning'>";
                                        for($star = 1; $star <= $checkStar1; $star++){
                                            echo "★";
                                        }
                                         echo "</span>";
                                        } ?>
                                    </td>
                                  </tr>
                                  <?php } ?>
                                  <!-- Task Status -->
                                  <tr>
                                    <td><strong>Task Status:</strong> <?= $task->nextCFID == 0 ? "<span class='p-1 bg-warning'>Pending</span>" : "<span class='p-1 bg-success'>Complete</span>" ?></td>
                                    <td></td>
                                    <td></td>
                                  </tr>


                                <?php if($task->nextCFID == 0){ ?>
                                  <tr>
                                    <td><strong>Why was not the task completed? :</strong> <span></span></td>
                                    <td>What star rating should we give for this?</td>
                                    <td>
                                      <?php 
                                        $checkStar1 = $this->Menu_model->GetGivedTaskStarRatingsCountAfterCheck($task->task_id,$sdate,"Why was not the task completed? What star rating should we give for this?");
                                        if($checkStar1 == 0){
                                        ?>
                                      <span class="star-rating" data-paragraph-text="Why was not the task completed? What star rating should we give for this?: <?= $task->late_remarks_message ?>" data-paragraph-question="Why was not the task completed? What star rating should we give for this?">
                                      <?php for ($i = 5; $i >= 1; $i--): ?>
                                      <input type="radio" id="star<?= $i ?>_<?= $task->task_id ?>_late_remarks" name="rating_<?= $task->task_id ?>_late_remarks" value="<?= $i ?>" data-task-user-id="<?= $task->task_id ?>" />
                                      <label for="star<?= $i ?>_<?= $task->task_id ?>_late_remarks" title="<?= $i ?> stars">★</label>
                                      <?php endfor; ?>
                                      </span>
                                      <?php }else{ 
                                        echo "<span class='text-warning'>";
                                        for($star = 1; $star <= $checkStar1; $star++){
                                            echo "★";
                                        }
                                         echo "</span>";
                                        } ?>
                                    </td>
                                  </tr>
                                <?php } ?>






                                  <?php if ($task->delete_request > 0) { ?>
                                  <tr style="background-color: orangered">
                                    <td><strong> Task Is Deleted By User : <span class="bg-success p-1">Yes</span></strong></td>
                                    <td>How would you rate the task that was deleted by the user?</td>
                                    <td>
                                      <?php 
                                        $checkStar1 = $this->Menu_model->GetGivedTaskStarRatingsCountAfterCheck($task->task_id,$sdate,"How would you rate the task that was deleted by the user?");
                                        if($checkStar1 == 0){

                                          echo "This Task is Deleted";
                                          /*
                                        ?>
                                      <span class="star-rating" data-paragraph-text="Complete <?= $task->task_name ?>: <?= $task->nextCFID != 0 ? $task->task_updateddate : 'NA' ?>" data-paragraph-question="How would you rate the completion of the task?">
                                      <?php for ($i = 5; $i >= 1; $i--): ?>
                                      <input type="radio" id="star<?= $i ?>_<?= $task->task_id ?>_complete_task" name="rating_<?= $task->task_id ?>_complete_task" value="<?= $i ?>" data-task-user-id="<?= $task->task_id ?>" />
                                      <label for="star<?= $i ?>_<?= $task->task_id ?>_complete_task" title="<?= $i ?> stars">★</label>
                                      <?php endfor; ?>
                                      </span>
                                      <?php 
                                      */
                                    }else{ 
                                        echo "<span class='text-warning'>";
                                        for($star = 1; $star <= $checkStar1; $star++){
                                            echo "★";
                                        }
                                         echo "</span>";
                                        } ?>
                                    </td>
                                  </tr>
                                  <?php } ?>
                                  <!-- Action Taken -->
                                  <?php if($task->nextCFID != 0): ?>
                                  <tr>
                                    <td>
                                      <strong>Action Taken:</strong> <span class='p-1 bg-success'><?= $task->actontaken ?></span> 
                                      <hr>
                                      <strong>Purpose Achieved:</strong> <span class='p-1 bg-success'><?= $task->purpose_achieved ?></span>
                                    </td>
                                    <td>How would you rate the accuracy of the action and purpose updates?</td>
                                    <td>
                                      <?php 
                                        $checkStar1 = $this->Menu_model->GetGivedTaskStarRatingsCountAfterCheck($task->task_id,$sdate,"How would you rate the accuracy of the action and purpose updates?");
                                        if($checkStar1 == 0){
                                        ?>
                                      <span class="star-rating" data-paragraph-text="Purpose Achieved: <?= $task->purpose_achieved ?>" data-paragraph-question="How would you rate the accuracy of the action and purpose updates?">
                                      <?php for ($i = 5; $i >= 1; $i--): ?>
                                      <input type="radio" id="star<?= $i ?>_<?= $task->task_id ?>_purpose_achieved" name="rating_<?= $task->task_id ?>_purpose_achieved" value="<?= $i ?>" data-task-user-id="<?= $task->task_id ?>" />
                                      <label for="star<?= $i ?>_<?= $task->task_id ?>_purpose_achieved" title="<?= $i ?> stars">★</label>
                                      <?php endfor; ?>
                                      </span>
                                      <?php }else{ 
                                        echo "<span class='text-warning'>";
                                        for($star = 1; $star <= $checkStar1; $star++){
                                            echo "★";
                                        }
                                         echo "</span>";
                                        } ?>
                                    </td>
                                  </tr>




                                  <?php if($task->actiontype_id == 2): ?>
                                   <tr>
                                    <td>
                                      <strong>Email looping with Sales Coordinator ?</strong></span>
                                    </td>
                                    <td>In email looping with sc or not ?</td>
                                    <td>
                                      <?php 
                                        $checkStar1 = $this->Menu_model->GetGivedTaskStarRatingsCountAfterCheck($task->task_id,$sdate,"In email looping with sc or not ?");
                                        if($checkStar1 == 0){
                                        ?>
                                      <span class="star-rating" data-paragraph-text="Purpose Achieved: <?= $task->purpose_achieved ?>" data-paragraph-question="In email looping with sc or not ?">
                                      <?php for ($i = 5; $i >= 1; $i--): ?>
                                      <input type="radio" id="star<?= $i ?>_<?= $task->task_id ?>_purpose_achieved" name="rating_<?= $task->task_id ?>_purpose_achieved" value="<?= $i ?>" data-task-user-id="<?= $task->task_id ?>" />
                                      <label for="star<?= $i ?>_<?= $task->task_id ?>_purpose_achieved" title="<?= $i ?> stars">★</label>
                                      <?php endfor; ?>
                                      </span>
                                      <?php }else{ 
                                        echo "<span class='text-warning'>";
                                        for($star = 1; $star <= $checkStar1; $star++){
                                            echo "★";
                                        }
                                         echo "</span>";
                                        } ?>
                                    </td>
                                  </tr>
                                  <?php endif; ?>



                                  <?php endif; ?>
                                  <?php if(in_array($task->actiontype_id,[3,4])){
                                    ?>
                                  <!-- Meeting Type -->
                                  <?php if($task->mtype !== '' && !is_null($task->mtype) && !empty($task->mtype)): ?>
                                  <tr>
                                    <td><strong>Meeting Type:</strong> <span class="b-1 p-1" style="background-color: navy; color:white"><?= $task->mtype ?></span></td>
                                    <td>How would you rate the meeting type?</td>
                                    <td>
                                      <?php 
                                        $checkStar1 = $this->Menu_model->GetGivedTaskStarRatingsCountAfterCheck($task->task_id,$sdate,"How would you rate the meeting type?");
                                        if($checkStar1 == 0){
                                        ?>
                                      <span class="star-rating" data-paragraph-text="Meeting Type: <?= $task->mtype ?>" data-paragraph-question="How would you rate the meeting type?">
                                      <?php for ($i = 5; $i >= 1; $i--): ?>
                                      <input type="radio" id="star<?= $i ?>_<?= $task->task_id ?>_meeting_type" name="rating_<?= $task->task_id ?>_meeting_type" value="<?= $i ?>" data-task-user-id="<?= $task->task_id ?>" />
                                      <label for="star<?= $i ?>_<?= $task->task_id ?>_meeting_type" title="<?= $i ?> stars">★</label>
                                      <?php endfor; ?>
                                      </span>
                                      <?php }else{ 
                                        echo "<span class='text-warning'>";
                                        for($star = 1; $star <= $checkStar1; $star++){
                                            echo "★";
                                        }
                                         echo "</span>";
                                        } ?>
                                    </td>
                                  </tr>
                                  <?php endif; ?>
                                  <tr>
                                    <td><strong> View Meeting Details</strong> - <a class="bg-success p-1" href="<?=base_url()?>/Reports/ViewMeetingDetails/<?=$task->task_id?>" target="_blank">View</a></td>
                                    <td>Check Meeting Details</td>
                                    <td>
                                      <a class="bg-success p-1" href="<?=base_url()?>/Reports/ViewMeetingDetails/<?=$task->task_id?>" target="_blank">View</a>
                                    </td>
                                  </tr>
                                  <?php if($task->nextCFID !=0){ ?>    
                                  <tr>
                                    <td><strong> Initiate Meeting Photo Star Rating :</strong> </td>
                                    <td>How would you rate the Initiate Meeting Photo?</td>
                                    <td>
                                      <?php 
                                        $checkStar1 = $this->Menu_model->GetGivedTaskStarRatingsCountAfterCheck($task->task_id,$sdate,"How would you rate the Initiate Meeting Photo?");
                                        if($checkStar1 == 0){
                                        ?>
                                      <span class="star-rating" data-paragraph-text="Meeting Type: <?= $task->mtype ?>" data-paragraph-question="How would you rate the Initiate Meeting Photo?">
                                      <?php for ($i = 5; $i >= 1; $i--): ?>
                                      <input type="radio" id="star<?= $i ?>_<?= $task->task_id ?>_initiate_meeting_photo" name="rating_<?= $task->task_id ?>_initiate_meeting_photo" value="<?= $i ?>" data-task-user-id="<?= $task->task_id ?>" />
                                      <label for="star<?= $i ?>_<?= $task->task_id ?>_initiate_meeting_photo" title="<?= $i ?> stars">★</label>
                                      <?php endfor; ?>
                                      </span>
                                      <?php }else{ 
                                        echo "<span class='text-warning'>";
                                        for($star = 1; $star <= $checkStar1; $star++){
                                            echo "★";
                                        }
                                         echo "</span>";
                                        } ?>
                                    </td>
                                  </tr>
                                  <tr>
                                    <td><strong>Meeting Start Photo Star Rating :</strong> </td>
                                    <td>How would you rate the Start Meeting Photo?</td>
                                    <td>
                                      <?php 
                                        $checkStar1 = $this->Menu_model->GetGivedTaskStarRatingsCountAfterCheck($task->task_id,$sdate,"How would you rate the Start Meeting Photo?");
                                        if($checkStar1 == 0){
                                        ?>
                                      <span class="star-rating" data-paragraph-text="Meeting Type: <?= $task->mtype ?>" data-paragraph-question="How would you rate the Start Meeting Photo?">
                                      <?php for ($i = 5; $i >= 1; $i--): ?>
                                      <input type="radio" id="star<?= $i ?>_<?= $task->task_id ?>_start_meeting_photo" name="rating_<?= $task->task_id ?>_start_meeting_photo" value="<?= $i ?>" data-task-user-id="<?= $task->task_id ?>" />
                                      <label for="star<?= $i ?>_<?= $task->task_id ?>_start_meeting_photo" title="<?= $i ?> stars">★</label>
                                      <?php endfor; ?>
                                      </span>
                                      <?php }else{ 
                                        echo "<span class='text-warning'>";
                                        for($star = 1; $star <= $checkStar1; $star++){
                                            echo "★";
                                        }
                                         echo "</span>";
                                        } ?>
                                    </td>
                                  </tr>
                                  <?php 
                                    $meetingsDatas  = $this->Menu_model->GETMeetingsDataByID($task->task_id);
                                    $minitiateTime  = $meetingsDatas[0]->initiateTime;
                                    $mstartm        = $meetingsDatas[0]->startm;
                                    $mclosem        = $meetingsDatas[0]->closem;
                                    
                                    // Initiate → Start
                                    $diffInitiateStart = (new DateTime($mstartm))->diff(new DateTime($minitiateTime));
                                    $diffInitiateStartText =   $diffInitiateStart->format('%h hours %i minutes %s seconds') . "\n";
                                    
                                    // Start → Close
                                    $diffStartClose = (new DateTime($mclosem))->diff(new DateTime($mstartm));
                                    $diffStartCloseText =  $diffStartClose->format('%h hours %i minutes %s seconds') . "\n";
                                    
                                    // Initiate → Close
                                    $diffInitiateClose = (new DateTime($mclosem))->diff(new DateTime($minitiateTime));
                                    $diffInitiateCloseText =  $diffInitiateClose->format('%h hours %i minutes %s seconds') . "\n";
                                    
                                    
                                    
                                    ?>
                                  <tr>
                                    <td><strong> Meeting Initiate to Start :<?= $diffInitiateStartText ?></td>
                                    <td>How would you rate the Meeting Initiate to Start?</td>
                                    <td>
                                      <?php 
                                        $checkStar1 = $this->Menu_model->GetGivedTaskStarRatingsCountAfterCheck($task->task_id,$sdate,"How would you rate the Meeting Initiate to Start?");
                                        if($checkStar1 == 0){
                                        ?>
                                      <span class="star-rating" data-paragraph-text="Meeting Type: <?= $task->mtype ?>" data-paragraph-question="How would you rate the Meeting Initiate to Start?">
                                      <?php for ($i = 5; $i >= 1; $i--): ?>
                                      <input type="radio" id="star<?= $i ?>_<?= $task->task_id ?>_initiate_meeting_time" name="rating_<?= $task->task_id ?>_initiate_meeting_time" value="<?= $i ?>" data-task-user-id="<?= $task->task_id ?>" />
                                      <label for="star<?= $i ?>_<?= $task->task_id ?>_initiate_meeting_time" title="<?= $i ?> stars">★</label>
                                      <?php endfor; ?>
                                      </span>
                                      <?php }else{ 
                                        echo "<span class='text-warning'>";
                                        for($star = 1; $star <= $checkStar1; $star++){
                                            echo "★";
                                        }
                                         echo "</span>";
                                        } ?>
                                    </td>
                                  </tr>
                                  <tr>
                                    <td><strong> Meeting Start to Close: :</strong> <?= $diffStartCloseText ?></td>
                                    <td>How would you rate the Meeting Start to Close?</td>
                                    <td>
                                      <?php 
                                        $checkStar1 = $this->Menu_model->GetGivedTaskStarRatingsCountAfterCheck($task->task_id,$sdate,"How would you rate the Meeting Start to Close?");
                                        if($checkStar1 == 0){
                                        ?>
                                      <span class="star-rating" data-paragraph-text="Meeting Type: <?= $task->mtype ?>" data-paragraph-question="How would you rate the Meeting Start to Close?">
                                      <?php for ($i = 5; $i >= 1; $i--): ?>
                                      <input type="radio" id="star<?= $i ?>_<?= $task->task_id ?>_start_meeting_time" name="rating_<?= $task->task_id ?>_start_meeting_time" value="<?= $i ?>" data-task-user-id="<?= $task->task_id ?>" />
                                      <label for="star<?= $i ?>_<?= $task->task_id ?>_start_meeting_time" title="<?= $i ?> stars">★</label>
                                      <?php endfor; ?>
                                      </span>
                                      <?php }else{ 
                                        echo "<span class='text-warning'>";
                                        for($star = 1; $star <= $checkStar1; $star++){
                                            echo "★";
                                        }
                                         echo "</span>";
                                        } ?>
                                    </td>
                                  </tr>
                                  <tr>
                                    <td><strong> Meeting Initiate to Close :</strong> <?= $diffInitiateCloseText ?></td>
                                    <td>How would you rate the Meeting Initiate to Close?</td>
                                    <td>
                                      <?php 
                                        $checkStar1 = $this->Menu_model->GetGivedTaskStarRatingsCountAfterCheck($task->task_id,$sdate,"How would you rate the Meeting Initiate to Close?");
                                        if($checkStar1 == 0){
                                        ?>
                                      <span class="star-rating" data-paragraph-text="Meeting Type: <?= $task->mtype ?>" data-paragraph-question="How would you rate the Meeting Initiate to Close?">
                                      <?php for ($i = 5; $i >= 1; $i--): ?>
                                      <input type="radio" id="star<?= $i ?>_<?= $task->task_id ?>_meeting_close_time" name="rating_<?= $task->task_id ?>_meeting_close_time" value="<?= $i ?>" data-task-user-id="<?= $task->task_id ?>" />
                                      <label for="star<?= $i ?>_<?= $task->task_id ?>_meeting_close_time" title="<?= $i ?> stars">★</label>
                                      <?php endfor; ?>
                                      </span>
                                      <?php }else{ 
                                        echo "<span class='text-warning'>";
                                        for($star = 1; $star <= $checkStar1; $star++){
                                            echo "★";
                                        }
                                         echo "</span>";
                                        } ?>
                                    </td>
                                  </tr>
                                  <?php } } ?>
                                  <!-- Remarks -->
                                  <?php if($task->nextCFID != 0): ?>
                                  <tr>
                                    <td><strong>Remarks:</strong> <span><?= $task->remarks ?></span></td>
                                    <td>How would you rate the remarks?</td>
                                    <td>
                                      <?php 
                                        $checkStar1 = $this->Menu_model->GetGivedTaskStarRatingsCountAfterCheck($task->task_id,$sdate,"How would you rate the remarks?");
                                        if($checkStar1 == 0){
                                        ?>
                                      <span class="star-rating" data-paragraph-text="Remarks: <?= $task->remarks ?>" data-paragraph-question="How would you rate the remarks?">
                                      <?php for ($i = 5; $i >= 1; $i--): ?>
                                      <input type="radio" id="star<?= $i ?>_<?= $task->task_id ?>_remarks" name="rating_<?= $task->task_id ?>_remarks" value="<?= $i ?>" data-task-user-id="<?= $task->task_id ?>" />
                                      <label for="star<?= $i ?>_<?= $task->task_id ?>_remarks" title="<?= $i ?> stars">★</label>
                                      <?php endfor; ?>
                                      </span>
                                      <?php }else{ 
                                        echo "<span class='text-warning'>";
                                        for($star = 1; $star <= $checkStar1; $star++){
                                            echo "★";
                                        }
                                         echo "</span>";
                                        } ?>
                                    </td>
                                  </tr>
                                  <?php endif; ?>
                                  <!-- Late/Early Remarks -->
                                  <?php if(!in_array($task->actiontype_id,[3,4])){ ?>
                                    <?php if($task->nextCFID != 0){ ?>
                                  <tr>
                                    <td><strong>Late/Early Remarks:</strong> <span><?= $task->late_remarks_message ?></span></td>
                                    <td>How would you rate the late/early remarks?</td>
                                    <td>
                                      <?php 
                                        $checkStar1 = $this->Menu_model->GetGivedTaskStarRatingsCountAfterCheck($task->task_id,$sdate,"How would you rate the late/early remarks?");
                                        if($checkStar1 == 0){
                                        ?>
                                      <span class="star-rating" data-paragraph-text="Late/Early Remarks: <?= $task->late_remarks_message ?>" data-paragraph-question="How would you rate the late/early remarks?">
                                      <?php for ($i = 5; $i >= 1; $i--): ?>
                                      <input type="radio" id="star<?= $i ?>_<?= $task->task_id ?>_late_remarks" name="rating_<?= $task->task_id ?>_late_remarks" value="<?= $i ?>" data-task-user-id="<?= $task->task_id ?>" />
                                      <label for="star<?= $i ?>_<?= $task->task_id ?>_late_remarks" title="<?= $i ?> stars">★</label>
                                      <?php endfor; ?>
                                      </span>
                                      <?php }else{ 
                                        echo "<span class='text-warning'>";
                                        for($star = 1; $star <= $checkStar1; $star++){
                                            echo "★";
                                        }
                                         echo "</span>";
                                        } ?>
                                    </td>
                                  </tr>
                                  <?php }} ?>
                                </tbody>
                              </table>
                            </div>
                          </div>
                        </div>
                      </div>
                      <?php $j++; endforeach; ?>
                    </div>
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
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.4.0/jspdf.umd.min.js"></script>
    <script>
      //   $("#example1").DataTable({
      //     "responsive": false, "lengthChange": false, "autoWidth": false,
      //     "buttons": ["csv", "excel", "pdf"]
      //   }).buttons().container().appendTo('#example1_wrapper .col-md-6:eq(0)');
      
      $("#example5").DataTable({
          "responsive": false, "lengthChange": false, "autoWidth": false,
          "buttons": ["csv", "excel", "pdf"]
        }).buttons().container().appendTo('#example5_wrapper .col-md-6:eq(0)');

   $("#example6").DataTable({
  "responsive": false,
  "lengthChange": false,
  "autoWidth": false,
  "pageLength": 50,
  "buttons": ["csv", "excel", "pdf"]
}).buttons().container().appendTo('#example6_wrapper .col-md-6:eq(0)');
   $("#table_id__1333").DataTable({
  "responsive": false,
  "lengthChange": false,
  "autoWidth": false,
  "pageLength": 500,
  "buttons": ["csv", "excel", "pdf"]
}).buttons().container().appendTo('#table_id__1333_wrapper .col-md-6:eq(0)');


      
      $("#example1").DataTable({
      "responsive": false,
      "lengthChange": false,
      "autoWidth": false,
      "buttons": [
          {
              extend: 'csv',
              text: 'Export CSV',
              title: 'Your Table Title', // Optional title for the CSV file
          },
          {
              extend: 'excel',
              text: 'Export Excel',
              title: 'Your Table Title', // Optional title for the Excel file
          },
          {
              extend: 'pdf',
              text: 'Export PDF',
              title: 'Your Table Title', // Optional title for the PDF file
              exportOptions: {
                  // This ensures that all data is exported, not just the visible data
                  modifier: {
                      page: 'all' // Export all pages
                  }
              }
          }
      ]
      }).buttons().container().appendTo('#example1_wrapper .col-md-6:eq(0)');
      
      
      $("#example4").DataTable({
      "responsive": false,
      "lengthChange": false,
      "autoWidth": false,
      "buttons": [
          {
              extend: 'csv',
              text: 'Export CSV',
              title: 'Your Table Title', // Optional title for the CSV file
          },
          {
              extend: 'excel',
              text: 'Export Excel',
              title: 'Your Table Title', // Optional title for the Excel file
          },
          {
              extend: 'pdf',
              text: 'Export PDF',
              title: 'Your Table Title', // Optional title for the PDF file
              exportOptions: {
                  // This ensures that all data is exported, not just the visible data
                  modifier: {
                      page: 'all' // Export all pages
                  }
              }
          }
      ]
      }).buttons().container().appendTo('#example4_wrapper .col-md-6:eq(0)');
      
      $("#example2").DataTable({
      "responsive": false,
      "lengthChange": false,
      "autoWidth": false,
      "buttons": [
          {
              extend: 'csv',
              text: 'Export CSV',
              title: 'Your Table Title', // Optional title for the CSV file
          },
          {
              extend: 'excel',
              text: 'Export Excel',
              title: 'Your Table Title', // Optional title for the Excel file
          },
          {
              extend: 'pdf',
              text: 'Export PDF',
              title: 'Your Table Title', // Optional title for the PDF file
              exportOptions: {
                  // This ensures that all data is exported, not just the visible data
                  modifier: {
                      page: 'all' // Export all pages
                  }
              }
          }
      ]
      }).buttons().container().appendTo('#example2_wrapper .col-md-6:eq(0)');
      
      
    </script>
    <script>
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
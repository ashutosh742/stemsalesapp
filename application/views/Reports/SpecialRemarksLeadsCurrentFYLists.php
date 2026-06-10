<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Task Details With Special Remarks Leads | STEM APP | WebAPP</title>
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
          .qa-container {
            width: 80%;
            margin: 20px auto;
            border-collapse: collapse;
            font-family: Arial, sans-serif;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }
        .qa-container th, .qa-container td {
            padding: 12px 15px;
            border-bottom: 1px solid #ddd;
        }
        .qa-container th {
            background: #4CAF50;
            color: #fff;
            text-align: left;
            font-size: 16px;
        }
        .qa-container td {
            font-size: 15px;
        }
        .question {
            font-weight: bold;
            color: #333;
            width: 50%;
        }
        .answer {
            color: #555;
            width: 50%;
        }
        .qa-container tr:hover {
            background-color: #f9f9f9;
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
        <div class="content-header" style="padding-bottom: 0;">
          <div class="container-fluid" style="padding-bottom: 0;">
            <?php 
              $totalTasksUserByDatas = $specialRemakrsDatas['totalTasksUserByDatas'];
               ?>
            <div class="row">
              <div class="col-md-12 text-center">
                <div class="frame-layer-1">
                  <div class="frame-layer-2">
                    <div class="frame-layer-3">
                      <h1 class="m-0 premium-color-1" style="color: #ff009b;">📋 Task Details With Special Remarks Leads</h1>
                      <p class="premium-color-2" style="color: #ff0000;">📝 Capture task details clearly with special remarks on leads. 📋 Enhance tracking, ensure accountability, and highlight important notes 🔍 for better decision-making, productivity ⚡, and effective communication 🤝.</p>
                      <p class="premium-color-2" style="color: #2c00ee;"><strong><mark><?=$sdate ?> To <?=$edate ?></mark></strong></p>
                      <p class="premium-color-2" style="color: #2c00ee;"><strong>Task : <mark><?=$taskActionDatas ?></mark></strong></p>
                      <p class="premium-color-2" style="color: #2c00ee;"><strong>BD Name : <mark><?=$totalTasksUserByDatas[0]->task_username ?></mark></strong></p>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <br>
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
            <div class="text-center" style="background: linear-gradient(to right, #ede7f6, #ffffff); border-radius: 12px; box-shadow: 0 4px 8px rgba(0,0,0,0.1); padding: 20px;">
              <h5><i>📝 Special Remarks Details</i></h5>
              <small>
              📊 This section provides detailed information about tasks being tracked or reviewed, divided into 👤 personal and 👥 team funnels with various subcategories for clear analysis.
              </small>
            </div>
            <hr>
              <div class="row mt-3 mb-2">
                    <div class="col-md-12 text-center">
                      <button class="custom-btn btn-11 partnearray p-2" id="downloadPdf">Download PDF</button>
                    </div>
                  </div>
                  <hr>
            <div class="row">
              <?php 
              function safeValue($val) {
                    return (isset($val) && trim($val) !== "" && strtoupper(trim($val)) !== "NULL") ? $val : "NA";
                }
                $j=1;
                foreach($totalTasksUserByDatas as $keys=>$task):
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
                
                  
                  ?>            
              <div class="col-md-4 mb-4" title="Task ID - <?=$task->task_id?>">
                <div class="card meetingslist-card" style="background: <?= $gradientColors[$index++ % count($gradientColors)] ?>; border-radius: 14px; box-shadow: 0 4px 12px rgba(0,0,0,0.1); padding: 20px;">
                  <div class="text-left">
                    <center>
                    <h3 class="mb-1 badge badge-pill badge-dark" style="font-weight: 600; font-size: 1.1rem;"><?= htmlspecialchars($j) ?></h3>
                    <hr style="width: 20%;" class="cardline">
                    <h4 class="mb-1" style="font-weight: 600;font-size: 18px;"><a target="_BLANK" href="<?=base_url()?>Menu/CompanyDetails/<?=$task->cid?>">    
                      <?= htmlspecialchars($task->compname) ?> (CID: <?= $task->cid ?>)
                      </a>  
                      <hr> 
                      <span style="color: #ff007bff;">Current Status:</span> <?= $task->current_status ?>
                    </h4>
                    <hr style="width: 60%;" class="cardline <?=$hrAClass?>">
                    <h5><span>🎯 <?= $task->task_name ?></span></h5> <hr>
                    <h5><span>⏳ <?=$task->task_time_status?> ➡️ <?=$task->task_time_new_status?></span></h5> <hr>
                    <h6><span>📅 <?= $task->appointmentdatetime ?></span></h6> <hr>
                    <span>💬 <?= $task->remarks ?></span> <hr>
                    



                    <?php 
                    $special_remarks = $task->special_remarks;
                    $special_remarks_decode = json_decode($special_remarks, true); 
                    if (!empty($special_remarks_decode) && is_array($special_remarks_decode)) {
                        echo "<div class'card' style='font-family: Arial, sans-serif;'>";
                        echo "<table class='table table-striped'>";
                        echo "<thead>
                                <tr style='background: #07ba0aff; color: white; text-align: left;'>
                                    <th style='padding: 10px; border: 1px solid #ccc;'>Question</th>
                                    <th style='padding: 10px; border: 1px solid #ccc;'>Answer</th>
                                </tr>
                            </thead>";
                        echo "<tbody>";

                        foreach ($special_remarks_decode as $question => $answer) {
                            $question = htmlspecialchars($question);
                            $answer   = htmlspecialchars($answer);

                            echo "<tr>";
                            echo "<td style='padding: 10px; border: 1px solid #ccc; color:#ff1e7c; font-weight:bold;'>$question</td>";
                            echo "<td style='padding: 10px; border: 1px solid #ccc; color:#2E8B57;'>$answer</td>";
                            echo "</tr>";
                        }

                        echo "</tbody>";
                        echo "</table>";
                        echo "</div>";  }
                        
                        ?>

                        <hr>
                        <div class="card1" style="background: linear-gradient(to right, #ede7f6, #ffffff); border-radius: 12px; box-shadow: 0 4px 8px rgba(0,0,0,0.1); padding: 20px;">
                            <?php 
                            $description = "
                        <b>Task By:</b> " . safeValue($task->task_username) . 
                        "<b> for company </b>" . safeValue($task->compname) . 
                        ". <b>The task type is </b>" . safeValue($task->task_name) . 
                        " <b>with current status </b>'" . safeValue($task->current_status) . "'. " .
                        "<b>Forward date is</b> " . safeValue($task->fwd_date) . 
                        ", <b>appointment scheduled at </b>" . safeValue($task->appointmentdatetime) . ". " .
                        "<b>Remarks: </b>" . safeValue($task->remarks) . ". " .
                        "<b>Late remarks: </b>" . safeValue($task->late_remarks_message) . ". " .
                        "<b>Main BD: </b>" . safeValue($task->main_bd_name) . 
                        ".";

                        echo $description;
                            ?>
                        </div>

                  </div>
                </div>
              </div>
              <?php 
                $j++;
                endforeach; ?>  
            </div>



            <hr>
 
            </div>


            <hr>
            <div class="body-content">
              <div class="table-responsive text-nowrap" style="overflow-x: auto;">
                <table id="example1" class="table table-striped table-bordered" cellspacing="0" width="100%">
                  <thead class="thead-dark">
                    <tr>
                      <th class="text-capitalize">🔢 Sr. NO</th>
                      <th class="text-capitalize">👤 Task Username</th>
                      <th class="text-capitalize">🆔 CID</th>
                      <th class="text-capitalize">🏢 Company Name</th>
                      <th class="text-capitalize">👨‍💼 Main BD</th>
                      <th class="text-capitalize">👩‍💼 Cluster Manager</th>
                      <th class="text-capitalize">🧑‍💼 PST</th>
                      <th class="text-capitalize">🧭 Assistant Sales Head (NAE)</th>
                      <th class="text-capitalize">🧭 Assistant Sales Head (W)</th>
                      <th class="text-capitalize">🧭 Assistant Sales Head (S)</th>
                      <th class="text-capitalize">🧑‍🏫 RM East</th>
                      <th class="text-capitalize">🧑‍🏫 RM North</th>
                      <th class="text-capitalize">👥 Assistant Cluster Manager</th>
                      <th class="text-capitalize">📌 Current Status</th>
                      <?php if($mtypes == 'total_in_q1_twetenty_closure_funnel'){ ?>
                      <th class="text-capitalize">🚀 Q1 20 Closure Funnel</th>
                      <?php }?>
                      <?php if($mtypes == 'total_in_potential_funnel_for_fy'){ ?>
                      <th class="text-capitalize">🎯 Potential Funnel For FY</th>
                      <?php }?>
                      <?php if($mtypes == 'total_in_to_be_nurtured_for_fy'){ ?>
                      <th class="text-capitalize">🌱 To be Nurtured for FY</th>
                      <?php }?>
                      <?php if($mtypes == 'total_in_fifity_new_lead_funnel'){ ?>
                      <th class="text-capitalize">🆕 50 New Lead Funnel</th>
                      <?php }?>
                      <th class="text-capitalize">📋 Task Name</th>
                      <th class="text-capitalize">🔖 Task Status</th>
                      <th class="text-capitalize">🎬 Action</th>
                      <th class="text-capitalize">🎯 Purpose</th>
                      <th class="text-capitalize">📅 Planned On Status</th>
                      <th class="text-capitalize">🕒 Change On Status</th>
                      <th class="text-capitalize">🗓️ Original Task Date</th>
                      <th class="text-capitalize">📆 Appointment DateTime</th>
                      <th class="text-capitalize">🚀 Initiated DateTime</th>
                      <th class="text-capitalize">⏱️ Updated DateTime</th>
                      <th class="text-capitalize">⌛ Total Time Taken</th>
                      <th class="text-capitalize">💬 Late Remarks Message</th>
                      <th class="text-capitalize">📝 Task Approved By</th>
                      <th class="text-capitalize">🗣️ Remarks</th>
                      <th class="text-capitalize">⭐ Special Remarks</th>
                      <th class="text-capitalize">📍 Closing Timeline</th>
                      <th class="text-capitalize">📍 Proposal Require</th>
                      <th class="text-capitalize">🤝 Partner Name</th>
                      <th class="text-capitalize">📈 Potential Client</th>
                      <th class="text-capitalize">💰 Top Spender Client</th>
                      <th class="text-capitalize">🔄 Upsell Client</th>
                      <th class="text-capitalize">🚀 Focus Funnel Client</th>
                      <th class="text-capitalize">🏷️ Key Client</th>
                      <th class="text-capitalize">👍 Positive Key Client</th>
                      <th class="text-capitalize">📣 Priorityc Client</th>
                      <th class="text-capitalize">🚀 Q1 20 Closure Funnel</th>
                      <th class="text-capitalize">🎯 Potential Funnel For FY</th>
                      <th class="text-capitalize">🌱 To be Nurtured for FY</th>
                      <th class="text-capitalize">🆕 50 New Lead Funnel</th>
                      <th class="text-capitalize">🔁 Task RePlanned Count</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php
                      $i = 1;
                      foreach ($totalTasksUserByDatas as $row) {
                        $meeting_user_id      = $row->user_id;
                        $meeting_task_id      = $row->task_id;
                        $calling_proposal_tid = $row->calling_proposal_tid;
                      
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
                      <td class="text-capitalize"><?= htmlspecialchars($row->task_username) ?></td>
                      <td><a style="color:<?=$backgroundColorNew;?>!important" target="_BLANK" href="<?=base_url();?>/Menu/CompanyDetails/<?=$row->cid?>"><?= htmlspecialchars($row->cid) ?></a></td>
                      <td class="text-capitalize"><a style="color:<?=$backgroundColorNew;?>!important" target="_BLANK" href="<?=base_url();?>/Menu/CompanyDetails/<?=$row->cid?>"><?= htmlspecialchars($row->compname) ?></a></td>
                      <td><?= htmlspecialchars($row->main_bd_name) ?></td>
                      <td><?= htmlspecialchars($row->main_bd_manager_name) ?></td>
                      <td><?php 
                        if(!empty($row->pst_name) && !is_null($row->pst_name)){
                            echo htmlspecialchars($row->pst_name);
                        }else{
                            echo 'NA';
                        }
                         ?></td>
                      <td><?php 
                        if(!empty($row->ash_nae_co_id_name) && !is_null($row->ash_nae_co_id_name)){
                            echo htmlspecialchars($row->ash_nae_co_id_name);
                        }else{
                            echo 'NA';
                        }
                         ?></td>
                      <td><?php 
                        if(!empty($row->ash_w_co_id_name) && !is_null($row->ash_w_co_id_name)){
                            echo htmlspecialchars($row->ash_w_co_id_name);
                        }else{
                            echo 'NA';
                        }
                         ?></td>
                      <td><?php 
                        if(!empty($row->ash_s_co_id_name) && !is_null($row->ash_s_co_id_name)){
                            echo htmlspecialchars($row->ash_s_co_id_name);
                        }else{
                            echo 'NA';
                        }
                         ?></td>
                      <td><?php 
                        if(!empty($row->rm_east_co_id_name) && !is_null($row->rm_east_co_id_name)){
                            echo htmlspecialchars($row->rm_east_co_id_name);
                        }else{
                            echo 'NA';
                        }
                         ?></td>
                      <td><?php 
                        if(!empty($row->rm_north_co_id_name) && !is_null($row->rm_north_co_id_name)){
                            echo htmlspecialchars($row->rm_north_co_id_name);
                        }else{
                            echo 'NA';
                        }
                         ?></td>
                      <td><?php 
                        if(!empty($row->acm_co_id_name) && !is_null($row->acm_co_id_name)){
                            echo htmlspecialchars($row->acm_co_id_name);
                        }else{
                            echo 'NA';
                        }
                         ?></td>
                      <td><?= htmlspecialchars($row->current_status) ?></td>
                      <?php if($mtypes == 'total_in_q1_twetenty_closure_funnel'){ ?>
                      <td class="text-capitalize"><?= htmlspecialchars($row->q1_twetenty_closure_funnel) ?></td>
                      <?php }?>
                      <?php if($mtypes == 'total_in_potential_funnel_for_fy'){ ?>
                      <td class="text-capitalize"><?= htmlspecialchars($row->potential_funnel_for_fy) ?></td>
                      <?php }?>
                      <?php if($mtypes == 'total_in_to_be_nurtured_for_fy'){ ?>
                      <td class="text-capitalize"><?= htmlspecialchars($row->to_be_nurtured_for_fy) ?></td>
                      <?php }?>
                      <?php if($mtypes == 'total_in_fifity_new_lead_funnel'){ ?>
                      <td class="text-capitalize"><?= htmlspecialchars($row->fifity_new_lead_funnel) ?></td>
                      <?php }?>
                      <td><?= htmlspecialchars($row->task_name) ?></td>
                      <td><?php 
                        if($row->nextCFID == 0){
                            echo "<span class='bg-warning p-1'> Pending </span>"; 
                        }else{
                            echo "<span class='bg-success p-1'> Complete </span>"; 
                        }
                        ?></td>
                      <td class="text-capitalize"><?= htmlspecialchars($row->actontaken) ?></td>
                      <td class="text-capitalize"><?= htmlspecialchars($row->purpose_achieved) ?></td>
                      <td><?= htmlspecialchars($row->task_time_status) ?></td>
                      <td><?= htmlspecialchars($row->task_time_new_status) ?></td>
                      <td><?= htmlspecialchars($row->fwd_date) ?></td>
                      <td><?= htmlspecialchars($row->appointmentdatetime) ?></td>
                      <td><?= htmlspecialchars($row->initiateddt) ?></td>
                      <td><?= htmlspecialchars($row->updated_at) ?></td>
                      <td><?= htmlspecialchars($row->total_time_taken) ?></td>
                      <td><?= htmlspecialchars($row->late_remarks_message) ?></td>
                      <td><?= htmlspecialchars($row->task_approved_by) ?></td>
                      <td><?= htmlspecialchars($row->remarks) ?></td>
                      <td><?php
                        if (
                            !empty($row->special_remarks) &&
                            $row->special_remarks !== null &&
                            $row->special_remarks !== 'null' &&
                            $row->special_remarks !== '[]'
                        ) { ?>
                        <button type="button" class="btn btn-primary"  onclick="SpecialRemarks(<?=$meeting_task_id ?>)">view</button>
                        <?php }else{
                          echo 'NA';
                          }
                          
                          
                          ?>
                      </td>
                      <td><?= htmlspecialchars($row->closing_timeline) ?></td>
                      <td><?= htmlspecialchars($row->proposal_require) ?></td>
                      <td class="text-capitalize"><?= htmlspecialchars($row->partner_name) ?></td>
                      <td class="text-capitalize"><?= htmlspecialchars($row->potential) ?></td>
                      <td class="text-capitalize"><?= htmlspecialchars($row->topspender) ?></td>
                      <td class="text-capitalize"><?= htmlspecialchars($row->upsell_client) ?></td>
                      <td class="text-capitalize"><?= htmlspecialchars($row->focus_funnel) ?></td>
                      <td class="text-capitalize"><?= htmlspecialchars($row->keycompany) ?></td>
                      <td class="text-capitalize"><?= htmlspecialchars($row->pkclient) ?></td>
                      <td class="text-capitalize"><?= htmlspecialchars($row->priorityc) ?></td>
                      <td class="text-capitalize"><?php 
                        if (isset($row->q1_twetenty_closure_funnel) && $row->q1_twetenty_closure_funnel !== '' && $row->q1_twetenty_closure_funnel !== null) {
                            if($row->q1_twetenty_closure_funnel == 'NULL'){
                              echo 'NA';
                            }else{
                              echo htmlspecialchars($row->q1_twetenty_closure_funnel);
                            }
                        }else{
                            echo 'NA';
                        }
                         ?></td>
                      <td class="text-capitalize"><?php 
                        if (isset($row->potential_funnel_for_fy) && $row->potential_funnel_for_fy !== '' && $row->potential_funnel_for_fy !== null) {
                         if($row->potential_funnel_for_fy == 'NULL'){
                           echo 'NA';
                         }else{
                           echo htmlspecialchars($row->potential_funnel_for_fy);
                         }
                        }else{
                         echo 'NA';
                        }
                        ?></td>
                      <td class="text-capitalize"><?php 
                        if (isset($row->to_be_nurtured_for_fy) && $row->to_be_nurtured_for_fy !== '' && $row->to_be_nurtured_for_fy !== null) {
                          if($row->to_be_nurtured_for_fy == 'NULL'){
                            echo 'NA';
                          }else{
                            echo htmlspecialchars($row->to_be_nurtured_for_fy);
                          }
                        } else {
                            echo 'NA';
                        }
                        ?></td>
                      <td class="text-capitalize"><?php 
                        if (isset($row->fifity_new_lead_funnel) && $row->fifity_new_lead_funnel !== '' && $row->fifity_new_lead_funnel !== null) {
                         if($row->fifity_new_lead_funnel == 'NULL'){
                           echo 'NA';
                         }else{
                           echo htmlspecialchars($row->fifity_new_lead_funnel);
                         }
                        }else{
                         echo 'NA';
                        }
                        
                        ?></td>
                      <td> 
                        <a style="color:<?=$backgroundColorNew;?>!important" href="<?=base_url();?>Menu/ReplanedLog/<?=$row->task_id;  ?>" target="_BLANK"><?= htmlspecialchars($row->plan_count) ?></a>
                      </td>
                    </tr>
                    <?php $i++; } ?>
                  </tbody>
                </table>
              </div>
              <br>
              <br>
              <br>
            </div>
          </div>
      </div>
      <div class="modal fade" id="specialRemarksModal" tabindex="-1" role="dialog" aria-labelledby="specialRemarksTitle" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered" role="document">
      <div class="modal-content">
      <div class="modal-header">
      <h5 class="modal-title" id="specialRemarksTitle">Special Remarks</h5>
      <button type="button" class="close" data-dismiss="modal" aria-label="Close">
      <span aria-hidden="true">&times;</span>
      </button>
      </div>
      <div class="modal-body">
      <div id="specialContent">
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
    <script>
      function SpecialRemarks(id){
                
                    $.ajax({
                    url:'<?=base_url();?>Menu/GetSpecialRemarksUsingTaskID',
                    type: "POST",
                    data: {
                    taskid: id
                    },
                    cache: false,
                    success: function a(result){
                      $('#specialRemarksModal').modal('show');
                      $("#specialContent").html(result);
                    }
                  });
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
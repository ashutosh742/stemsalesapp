<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Task Details With Remarks Leads | STEM APP | WebAPP</title>
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
                      <h1 class="m-0 premium-color-1" style="color: #ff009b;">📋 Task Details With Remarks Leads</h1>
                      <p class="premium-color-2" style="color: #ff0000;">📝 Capture task details clearly with special remarks on leads. 📋 Enhance tracking, ensure accountability, and highlight important notes 🔍 for better decision-making, productivity ⚡, and effective communication 🤝.</p>
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
              <div class="text-right-data text-center" style="background: linear-gradient(to right, #d7faff, #ffffff); border-radius: 12px; box-shadow: 0 4px 8px rgba(0,0,0,0.1); padding: 20px;">
                <?php 
                  $clusterPstDatas  = $this->Menu_model->GetAdminActiveTeam($user['user_id']);
                  $taskActions      = $this->Menu_model->get_action();
                  $userRoles        = $this->Menu_model->GetActiveDepartmentList($uid);
                  ?>
                <div class="col text-center formcenterData">
                  <div>
                    <hr>
                    <form action="<?=base_url()?>Reports/AdminRemarksLeadsCurrentFY" method="post" class="mt-3">
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
                            <?php if(in_array($user['type_id'],[1])){ ?> 
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

            $userwiseCount = $specialRemakrsDatas['userwiseCount'];
            $conversions = $specialRemakrsDatas['conversions'];

            $conversionsArray = [];
            foreach ($conversions as $item) {
                $conversionsArray[$item->task_user_id][] = $item;
            }


          


            $groupedCountByUser = [];

            foreach ($userwiseCount as $item) {
                $user = $item->user_name;
                $status = $item->planned_status_name;
                $count = $item->total_task;
                $task_user_id = $item->task_user_id;

                if (!isset($groupedCountByUser[$user])) {
                    $groupedCountByUser[$user][0] = ['task_user_id' => $task_user_id, 'total' => 0];
                }

                // Add status as key
                $groupedCountByUser[$user][0][$status] = $count;

                // Add to total
                $groupedCountByUser[$user][0]['total'] += $count;
            }

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
              <h5><i>📝 Total Task Details</i></h5>
              <small>
                📊 This section provides detailed information about tasks being tracked or reviewed, divided into 👤 personal and 👥 team funnels with various subcategories for clear analysis.
              </small>
            </div>
    
            <hr>

           <div class="row">
<?php  
foreach ($groupedCountByUser as $username => $userData) {
    // each $userData = array with index [0]
    $details = $userData[0]; 

    $r = rand(150, 255);
    $g = rand(150, 255);
    $b = rand(150, 255);
    $backgroundColor = "rgba($r, $g, $b,.3)";

    $hue = rand(0, 360);
    $saturation = rand(60, 100);
    $lightness = rand(30, 45);
    $backgroundColorNew = "hsl($hue, $saturation%, $lightness%)";

    ?>
    <div class="col-md-3 mb-3">
      <div class="card text-center shadow" style="background-color: <?= $backgroundColor ?>; color: <?= $backgroundColorNew ?>;">
        <div class="card-body" style="color:<?=$backgroundColorNew;?>!important">
          
          <!-- Username as card title -->
          <div class="text-center">
            <h5><b><?= $username ?></b></h5> <hr>
            <h4>
                <a target="_BLANK" class="card-count-heading-new" 
             style="color:<?=$backgroundColorNew;?>!important" 
             href="<?=base_url()?>Reports/AdminRemarksLeadsCurrentFYLists/<?=$details['task_user_id']?>/<?=$sdate?>/<?=$edate?>">
              
             <span style="box-shadow: rgba(0, 0, 0, 0.12) 0px 1px 3px, rgba(0, 0, 0, 0.24) 0px 1px 2px;width: 40px; height: 40px;" 
                    class="p-2 badge badge-pill badge-primary mt-1">
               <?= $details['total'] ?>
              </span>
                </a>
            </h4>
            <hr>
          <?php  
          foreach ($details as $statusname => $statusValue) { 
              if($statusname == "task_user_id" || $statusname == 'total') continue; // skip id field
              ?>
              <span style="box-shadow: rgba(0, 0, 0, 0.12) 0px 1px 3px, rgba(0, 0, 0, 0.24) 0px 1px 2px;" 
                    class="p-2 badge badge-pill badge-dark mt-1">
                <strong><?= $statusname ?>: </strong><?= $statusValue ?>
              </span>
          <?php } ?>
            <hr>

            <?php 
            $positive_conversions = $conversionsArray[$details['task_user_id']][0]->positive_conversions;
            $negative_conversions = $conversionsArray[$details['task_user_id']][0]->negative_conversions;
            
            ?>
            <span style="box-shadow: rgba(0, 0, 0, 0.12) 0px 1px 3px, rgba(0, 0, 0, 0.24) 0px 1px 2px;" 
                    class="p-2 badge badge-pill badge-success mt-1">
                <strong>Positive Conversions: </strong><?= $positive_conversions ?>
              </span>
            <span style="box-shadow: rgba(0, 0, 0, 0.12) 0px 1px 3px, rgba(0, 0, 0, 0.24) 0px 1px 2px;" 
                    class="p-2 badge badge-pill badge-danger mt-1">
                <strong>Negative Conversions: </strong><?= $negative_conversions ?>
              </span>
        

          </div>

         
          
          </a>
        </div>
      </div>
    </div>
    <?php
}
?>
</div>



<?php 
// dd();

$totalTasksUserByDatas = $specialRemakrsDatas['totalTasksUserByDatas'];





// dd($grouped);
?>
            <br>
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
                       
                        
                        ?></td>
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
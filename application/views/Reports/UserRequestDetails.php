<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
      <title>Get Team Task On Self Or Other Funnel Task Details | STEM APP | WebAPP</title>
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
        .userrequestbg{
          background: linear-gradient(to right, #ecfffd, #ffffff); 
          border-radius: 12px; 
          box-shadow: rgba(60, 64, 67, 0.3) 0px 1px 2px 0px, rgba(60, 64, 67, 0.15) 0px 1px 3px 1px;
          padding: 5px;
          margin-bottom: 2px;
          border-radius: 4px;
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
                    <div class="frame-layer-3" style="background: linear-gradient(to right, #ede7f6, #ffffff); border-radius: 12px; box-shadow: 0 4px 8px rgba(0,0,0,0.1); padding: 20px;">
                      <?php if($mtypes == 'total_review_count' || $mtypes == 'for_him_self_review' || $mtypes == 'for_other_review'){ ?> 
                        <h1 class="m-0 premium-color-1" style="color: #ff009b;">📋 User Review Planned Details</h1>
                        <small>
                            🔍 This section provides a breakdown of the total number of reviews handled by the user.  
                            🧾 It distinguishes between reviews created for themselves and 👥 those assigned to others.  
                            📈 This helps analyze engagement, accountability, and collaboration.  
                            ✅ Tracking both self and delegated reviews offers insight into patterns, contribution levels, and leadership behavior—enabling better performance evaluation within the system.
                        </small>
                         <?php }else{ ?> 
                          <h1 class="m-0 premium-color-1" style="color: #ff009b;">📋 User Request Details</h1>
                          <p class="premium-color-2" style="color: #ff0000;">
                        📊 This section offers a detailed overview of all user-submitted meeting requests, including types, statuses, and key metrics. 🔍 It helps in tracking participation, cancellations, completions, and pending approvals — enabling better planning and follow-up across the team.
                        </p>
                           <?php } ?>
                   
                        
                      <p class="premium-color-2" style="color: #2c00ee;"><strong><mark><?=$sdate ?> To <?=$edate ?></mark></strong></p>

                      <?php 

                      $target_uidData     = $this->Menu_model->get_userbyid($target_uid);
                      $target_uidDataName = $target_uidData[0]->name;
                      if($mtypes == 'special_request_for_leave_request'){
                        $filter = 'Special Request For Leave Some Time';
                      }else{
                        $filter = ucwords(str_replace('_', ' ', $mtypes));
                      }
                      ?>
                      <p class="premium-color-2 text-capitalize" style="color:rgb(16, 238, 0);"><strong><mark><?=$filter ?></mark></strong></p>
                      <p class="premium-color-2 text-capitalize p-3"><strong><mark style="background-color:rgb(198, 238, 0);"><?=$target_uidDataName ?></mark></strong></p>
                    </div>
                  </div>
                </div>
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
            <hr>
            <div class="body-content">
              <div class="page-header">
                <div class="table-responsive text-nowrap">

                
                <?php if($mtypes == 'same_day_planning'){ ?>
                  <table id="example1" class="table table-striped table-bordered" cellspacing="0" width="100%">
                        <thead class="thead-dark">
                                <tr>
                                    <th class="text-capitalize">🔢 Sr. NO</th>
                                    <th class="text-capitalize">👤 Username</th>
                                    <th class="text-capitalize">📌 Request Date</th>
                                    <th class="text-capitalize">📌 Request Type</th>
                                    <th class="text-capitalize">💬 Request Message</th>
                                    <th class="text-capitalize">✅ Approval Status</th>
                                    <th class="text-capitalize">📝 Approval Remarks</th>
                                    <th class="text-capitalize">📝 Approval Time</th>
                                    <th class="text-capitalize">📅 Created Date</th>
                                    </tr>
                            </thead>
                            <tbody>
                                <?php
                                $i = 1;
                                foreach ($totalTasksUserByDatas as $row) {

                                $r = rand(150, 255);
                                $g = rand(150, 255);
                                $b = rand(150, 255);
                                $backgroundColor = "rgba($r, $g, $b,.2)";
                                $hue = rand(0, 360); // Full color wheel
                                $saturation = rand(60, 100); // High saturation for rich colors
                                $lightness = rand(30, 45); // Low lightness for a deeper tone
                                $backgroundColorNew = "hsl($hue, $saturation%, $lightness%)";
                                ?>
                                <tr style="background-color: <?php echo $backgroundColor; ?>;color:<?=$backgroundColorNew;?>!important" >
                                        <td><?= htmlspecialchars($i) ?></td>
                                        <td class="text-capitalize"><?= htmlspecialchars($row->user_name) ?></td>
                                        <td class="text-capitalize"><?= htmlspecialchars($row->date) ?></td>
                                        <td class="text-capitalize"><?= htmlspecialchars($row->would_you_want) ?></td>
                                        <td class="text-capitalize"><?= htmlspecialchars($row->request_remarks) ?></td>
                                        <td class="text-capitalize"><?= htmlspecialchars($row->approvel_status) ?></td>
                                        <td class="text-capitalize"><?= htmlspecialchars($row->remarks) ?></td>
                                        <td class="text-capitalize"><?= htmlspecialchars($row->apr_time) ?></td>
                                        <td class="text-capitalize"><?= htmlspecialchars($row->created_at) ?></td>
                                    </tr>
                            <?php $i++; } ?>
                            </tbody>
                        </table>
                  
                        <?php }else if($mtypes == 'pending_task_planning_request' || $mtypes == 'plan_but_not_initiated_request' || $mtypes == 'old_pending_task_request'){ ?>

                            <?php // dd($totalTasksUserByDatas); ?>
                            <table id="example2" class="table table-striped table-bordered" cellspacing="0" width="100%">
                        <thead class="thead-dark">
                                <tr>
                                    <th class="text-capitalize">🔢 Sr. NO</th>
                                    <th class="text-capitalize">👤 Username</th>
                                    <th class="text-capitalize">📌 Request Date</th>
                                    <th class="text-capitalize">📌 Request Type</th>
                                    <th class="text-capitalize">📌 Request Task Count</th>
                                    <th class="text-capitalize">💬 Request Message</th>
                                    <th class="text-capitalize">✅ Approval Status</th>
                                    <th class="text-capitalize">📅 Request Created Date</th>
                                    <th class="text-capitalize">📝 Approval BY</th>
                                    <th class="text-capitalize">📝 Approval Time</th>
                                    <th class="text-capitalize">📝 Approval Message</th>
                                    </tr>
                            </thead>
                            <tbody>
                                <?php
                                $i = 1;
                                foreach ($totalTasksUserByDatas as $row) {

                                $r = rand(150, 255);
                                $g = rand(150, 255);
                                $b = rand(150, 255);
                                $backgroundColor = "rgba($r, $g, $b,.2)";
                                $hue = rand(0, 360); // Full color wheel
                                $saturation = rand(60, 100); // High saturation for rich colors
                                $lightness = rand(30, 45); // Low lightness for a deeper tone
                                $backgroundColorNew = "hsl($hue, $saturation%, $lightness%)";
                                ?>
                                <tr style="background-color: <?php echo $backgroundColor; ?>;color:<?=$backgroundColorNew;?>!important" >
                                        <td><?= htmlspecialchars($i) ?></td>
                                        <td class="text-capitalize"><?= htmlspecialchars($row->user_name) ?></td>
                                        <td class="text-capitalize"><?= htmlspecialchars($row->request_date) ?></td>
                                        <td class="text-capitalize"><?= htmlspecialchars($row->request_type) ?></td>
                                        <td class="text-capitalize"><?= htmlspecialchars($row->task_count) ?></td>
                                        <td class="text-capitalize"><?= htmlspecialchars($row->request_remarks) ?></td>
                                        <td class="text-capitalize"><?php 
                                        
                                        if($row->approved ==1){
                                            echo "<span class='bg-success p-1'>Approved</span>";
                                        }else if($row->approved ==2){
                                            echo "<span class='bg-danger p-1'>Rejected</span>";
                                        }else if($row->approved ==0){
                                            echo "<span class='bg-warning p-1'>Pending</span>";
                                        }
                                        ?></td>
                                        <td class="text-capitalize"><?= htmlspecialchars($row->created_at) ?></td>
                                        <td class="text-capitalize"><?= htmlspecialchars($row->approved_by_name) ?></td>
                                        <td class="text-capitalize"><?= htmlspecialchars($row->approved_date) ?></td>
                                        <td class="text-capitalize"><?= htmlspecialchars($row->approved_message) ?></td>
                                      
                                    </tr>
                            <?php $i++; } ?>
                            </tbody>
                        </table>

       
                       <?php }else if($mtypes == 'pending_meetings_task_request'){ ?>


                        <?php  // dd($totalTasksUserByDatas); ?>
                            <table id="example2" class="table table-striped table-bordered" cellspacing="0" width="100%">
                        <thead class="thead-dark">
                            <tr>
                              <th scope="col">#️⃣</th>
                              <th class="text-capitalize">👤 Task User Name</th>
                              <th class="text-capitalize">📝 Number of Tasks</th>
                              <th class="text-capitalize">📅 Planner Request Date</th>
                              <th class="text-capitalize">💬 Request Message</th>
                              <th class="text-capitalize">✅ Approved Status</th>
                              <th class="text-capitalize">🗓️ Approved Date</th>
                              <th class="text-capitalize">🧑‍💼 Approved By</th>
                              <th class="text-capitalize">🕒 Created Date</th>
                      
                            </tr>
                            </thead>
                            <tbody>
                                <?php
                                $i = 1;
                      
                                  foreach($totalTasksUserByDatas as $getPMTDRequest){
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
                                    <td><?= $i ?></td>
                                    <td class="text-capitalize"><?= $getPMTDRequest->request_user_name ?></td>
                                    <td><?= $getPMTDRequest->request_task_count ?></td>
                                    <td><?= $getPMTDRequest->request_date ?></td>
                                    <td>
                                <?php 
                                $getTaskReqLists = $this->Menu_model->GetTblCallEventsTasksList($getPMTDRequest->task_ids);
                                $l = 0;
                                $m=1;
                                $requestRemarksarrays = explode(",", $getPMTDRequest->remarks);
                                foreach ($getTaskReqLists as $value){ ?>
                                  <div class="userrequestbg">
                                    <span style="color: navy;"> <b> #<?=$m;?> - CID:</b>  <?= $value->cid?> </span> |
                                    <span style="color: #22850e;"> <b>Company Name</b>:  <?= $value->company_name?> </span> |
                                    <span style="color:rgb(18, 14, 133);"> <b>Appointment Datetime</b>:  <?= $value->appointmentdatetime?> </span> |
                                    <span style="color: #eb2727;"> <b>Request Reamrks:</b>  <?= $requestRemarksarrays[$l]; ?> </span> | 
                                    <span style="color:rgb(32, 4, 4);"> <b>Delete Remarks:</b>  <?php 
                                    if($value->delete_request > 0){
                                    echo $value->delete_remarks;
                                    }else{
                                      echo "<span class='bg-warning p-1'>Pending</span>";
                                    }
                                    ?> </span> | 
                                     <span style="color: #eb2727;"> <b>Delete Status:</b>  <?php 
                                    if($value->delete_request > 0){
                                    echo "<span class='bg-success p-1'>Deleted</span>";
                                    }else{
                                      echo "<span class='bg-warning p-1'>Pending</span>";
                                    }
                                    ?> </span>
                                    </div>
                                 
                                <?php $m++; $l++; } ?>
                                </td>
                                <td>
                                    <?php 
                                    if($getPMTDRequest->apr_status == 1){
                                        echo "<span class='bg-success p-1'>Approved</span>";
                                    }else if($getPMTDRequest->apr_status == 0){
                                        echo "<span class='bg-warning p-1'>Pending</span>";
                                    }
                                    ?>
                                </td>
                                <td>
                                  <?php 
                                  if($getPMTDRequest->apr_date == ''){
                                      echo "<span class='bg-warning p-1'>Pending</span>";
                                  }else if($getPMTDRequest->apr_date !== ''){
                                      echo $getPMTDRequest->apr_date;
                                  }
                                  ?>
                                </td>
                              <td>
                                <?php 
                                  if($getPMTDRequest->apr_by_name == ''){
                                      echo "<span class='bg-warning p-1'>Pending</span>";
                                  }else if($getPMTDRequest->apr_by_name !== ''){
                                      echo $getPMTDRequest->apr_by_name;
                                  }
                                  ?>
                              </td>
                              <td><?= $getPMTDRequest->created_at ?></td>
                              
                            </tr>
                            <?php $i++; } ?>
                            </tbody>
                        </table>

                            
                        <?php }else if($mtypes == 'special_request_for_leave_request'){ ?>

                          <?php  // dd($totalTasksUserByDatas); ?>
                            <table id="example2" class="table table-striped table-bordered" cellspacing="0" width="100%">
                        <thead class="thead-dark">
                                <tr>
                                    <th class="text-capitalize">🔢 Sr. NO</th>
                                    <th class="text-capitalize">👤 Username</th>
                                    <th class="text-capitalize">📅 Request Date</th>
                                    <th class="text-capitalize">💬 Request Prupose</th>
                                    <th class="text-capitalize">⏰ Start Time</th>
                                    <th class="text-capitalize">🕔 End Time</th>
                                    <th class="text-capitalize">✅ Approval Status</th>
                                    <th class="text-capitalize">📅 Request Created Date 🕔 Time</th>
                                    <th class="text-capitalize">📝 Approval BY</th>
                                    <th class="text-capitalize">📝 Approval Time</th>
                                    <th class="text-capitalize">📝 Approval Message</th>
                                    </tr>
                            </thead>
                            <tbody>
                                <?php
                                $i = 1;
                                foreach ($totalTasksUserByDatas as $row) {

                                $r = rand(150, 255);
                                $g = rand(150, 255);
                                $b = rand(150, 255);
                                $backgroundColor = "rgba($r, $g, $b,.2)";
                                $hue = rand(0, 360); // Full color wheel
                                $saturation = rand(60, 100); // High saturation for rich colors
                                $lightness = rand(30, 45); // Low lightness for a deeper tone
                                $backgroundColorNew = "hsl($hue, $saturation%, $lightness%)";
                                ?>
                                <tr style="background-color: <?php echo $backgroundColor; ?>;color:<?=$backgroundColorNew;?>!important" >
                                        <td><?= htmlspecialchars($i) ?></td>
                                        <td class="text-capitalize"><?= htmlspecialchars($row->username) ?></td>
                                        <td class="text-capitalize"><?= htmlspecialchars($row->date) ?></td>
                                        <td class="text-capitalize"><?= htmlspecialchars($row->prupose) ?></td>
                                        <td class="text-capitalize"><?= htmlspecialchars($row->stime) ?></td>
                                        <td class="text-capitalize"><?= htmlspecialchars($row->etime) ?></td>
                                        <td class="text-capitalize"><?php 
                                        if($row->approve_status =='Approved'){
                                            echo "<span class='bg-success p-1'>Approved</span>";
                                        }else if($row->approve_status =='Rejected' || $row->approve_status =='Reject'){
                                            echo "<span class='bg-danger p-1'>Rejected</span>";
                                        }else if($row->approve_status ==''){
                                            echo "<span class='bg-warning p-1'>Pending</span>";
                                        }
                                        ?></td>
                                        <td class="text-capitalize"><?= htmlspecialchars($row->created_at) ?></td>
                                        <td class="text-capitalize"><?php 
                                          if($row->approved_by ==''){
                                          echo "<span class='bg-warning p-1'>Pending</span>";
                                          }else{
                                            echo htmlspecialchars($row->approved_by);
                                          }
                                        ?></td>
                                        <td class="text-capitalize"><?php 
                                          if($row->approve_date ==''){
                                          echo "<span class='bg-warning p-1'>Pending</span>";
                                          }else{
                                            echo htmlspecialchars($row->approve_date);
                                          }
                                        ?></td>
                                        <td class="text-capitalize"><?php 
                                          if($row->approve_remarks ==''){
                                          echo "<span class='bg-warning p-1'>Pending</span>";
                                          }else{
                                            echo htmlspecialchars($row->approve_remarks);
                                          }
                                        ?></td>
                                    </tr>
                            <?php $i++; } ?>
                            </tbody>
                        </table>
                        <?php }else if($mtypes == 'total_review_count' || $mtypes == 'for_him_self_review' || $mtypes == 'for_other_review'){ ?> 
                          
                          <?php  // dd($totalTasksUserByDatas); ?>

                          <table id="example2" class="table table-striped table-bordered" cellspacing="0" width="100%">
                        <thead class="thead-dark">
                        <tr>
                          <th class="text-capitalize">🔢 Sr. No</th>
                          <th class="text-capitalize">🧾 Review Type</th>
                          <th class="text-capitalize">👤 Created By</th>
                          <th class="text-capitalize">👥 Assigned To</th>
                          <th class="text-capitalize">📅 Review Date</th>
                          <th class="text-capitalize">⏳ Start Date</th>
                          <th class="text-capitalize">✅ Close Date</th>
                          <th class="text-capitalize">💬 Close Remarks</th>
                          <th class="text-capitalize">📝 Create Remarks</th>
                          <th class="text-capitalize">🎯 Target Setting</th>
                          <th class="text-capitalize">🔍 After Review</th>
                        </tr>
                            </thead>
                            <tbody>
                                <?php
                                $i = 1;
                                foreach ($totalTasksUserByDatas as $row) {

                                $r = rand(150, 255);
                                $g = rand(150, 255);
                                $b = rand(150, 255);
                                $backgroundColor = "rgba($r, $g, $b,.2)";
                                $hue = rand(0, 360); // Full color wheel
                                $saturation = rand(60, 100); // High saturation for rich colors
                                $lightness = rand(30, 45); // Low lightness for a deeper tone
                                $backgroundColorNew = "hsl($hue, $saturation%, $lightness%)";
                                ?>
                                <tr style="background-color: <?php echo $backgroundColor; ?>;color:<?=$backgroundColorNew;?>!important" title="<?=$row->id?>">
                                        <td><?= htmlspecialchars($i) ?></td>
                                        <td class="text-capitalize"><?= htmlspecialchars($row->reviewtype) ?></td>
                                        <td class="text-capitalize"><?= htmlspecialchars($row->by_username) ?></td>
                                        <td class="text-capitalize"><?= htmlspecialchars($row->to_username) ?></td>
                                        <td class="text-capitalize"><?= htmlspecialchars($row->plant) ?></td>
                                        <td class="text-capitalize"><?php 
                                        if($row->startt == ''){
                                          echo "<span class='bg-warning p-1'>Pending</span>";
                                        }else{
                                          echo $row->startt;
                                        }
                                        ?></td>
                                        <td class="text-capitalize"><?php 
                                        if($row->closet == ''){
                                          echo "<span class='bg-warning p-1'>Pending</span>";
                                        }else{
                                          echo $row->closet;
                                        }
                                        ?></td>
                                        <td class="text-capitalize"><?php 
                                        if($row->cremark == ''){
                                          echo "<span class='bg-warning p-1'>Pending</span>";
                                        }else{
                                          echo $row->cremark;
                                        }
                                        ?></td>
                                        <td class="text-capitalize"><?php 
                                        if($row->plan_time_remarks == ''){
                                          echo "NA";
                                        }else{
                                          echo $row->plan_time_remarks;
                                        }
                                        ?></td>
                                        <td class="text-capitalize"><?php 
                                        if($row->target_settings == 'no'){
                                          echo "NA";
                                        }else{ ?>
                                          <a target="_blank" class="p-1 bg-success" href="<?=base_url()?>Menu/target_vs_achievement/<?=$row->target_settings?>">view</a>
                                       <?php  }
                                        ?></td>
                                        <td class="text-capitalize"><?php 
                                        if($row->after_reviewtype == ''){
                                          echo "NA";
                                        }else{
                                          echo $row->after_reviewtype;
                                        }
                                        ?></td>
                                    </tr>
                            <?php $i++; } ?>
                            </tbody>
                        </table>
                          
                          
                          <?php } ?>
                </div>
                <hr />
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
        $("#example1").DataTable({
          "responsive": false, "lengthChange": false, "autoWidth": false,
          "buttons": ["csv", "excel", "pdf"]
        }).buttons().container().appendTo('#example1_wrapper .col-md-6:eq(0)');
        $("#example2").DataTable({
          "responsive": false, "lengthChange": false, "autoWidth": false,
          "buttons": ["csv", "excel", "pdf"]
        }).buttons().container().appendTo('#example2_wrapper .col-md-6:eq(0)');
      </script>
    
    </body>
</html>

<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Meeting Details | STEM APP | WebAPP</title>
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
      <?php require('nav.php');?>
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
                      <h1 class="m-0 premium-color-1" style="color: #ff009b;">Meeting Details</h1>
                      <p class="premium-color-2" style="color: #ff0000;">This section provides a comprehensive overview of all meetings, including statistics on various types of meetings, their statuses, and other relevant details.</p>
                      <p class="premium-color-2" style="color: #2c00ee;"><strong><mark><?=$sdate ?> To <?=$edate ?></mark></strong></p>

                      <?php 

                      $target_uidData     = $this->Menu_model->get_userbyid($target_uid);
                      $target_uidDataName = $target_uidData[0]->name;

                      if($mtypes == 'total_plan_meetings'){
                        $filter = 'Total Plan Meetings';
                      }else if($mtypes == 'complete_meetings'){
                        $filter = ' Total complete meetings';
                      }else if($mtypes == 'not_complete_meetings'){
                        $filter = ' Total not complete meetings';
                      }else if($mtypes == 'total_RP_meetings'){
                        $filter = ' Total RP meetings';
                      }else if($mtypes == 'total_NO_RP_meetings'){
                        $filter = ' Total No RP meetings';
                      }else if($mtypes == 'total_Only_Got_details_meetings'){
                        $filter = ' Total Only Got details meetings';
                      }else if($mtypes == 'total_fresh_meetings'){
                        $filter = ' Total Fresh meetings';
                      }else if($mtypes == 'total_re_meetings'){
                        $filter = ' Total Re meetings';
                      }else if($mtypes == 'total_scheduled_meetings'){
                        $filter = ' Total Scheduled meetings';
                      }else if($mtypes == 'total_complete_scheduled_meetings'){
                          $filter = ' Total complete Scheduled meetings';
                      }else if($mtypes == 'not_complete_scheduled_meetings'){
                        $filter = ' Total not complete Scheduled meetings';
                      }else if($mtypes == 'total_Scheduled_RP_meetings'){
                        $filter = ' total Scheduled RP meetings';
                      }else if($mtypes == 'total_Scheduled_NO_RP_meetings'){
                          $filter = ' total Scheduled No RP meetings';
                      }else if($mtypes == 'total_Scheduled_Only_Got_details_meetings'){
                        $filter = ' total Scheduled Only Got details meetings';
                      }else if($mtypes == 'total_barg_meetings'){
                        $filter = ' Total Barg meetings';
                      }else if($mtypes == 'total_complete_barg_meetings'){
                        $filter = ' Total complete Barg meetings';
                      }else if($mtypes == 'not_complete_barg_meetings'){
                        $filter = ' Total not complete Barg meetings';
                      }else if($mtypes == 'total_Barge_RP_meetings'){
                        $filter = ' total Barge RP meetings';
                      }else if($mtypes == 'total_Barge_NO_RP_meetings'){
                        $filter = ' total Barge No RP meetings';
                      }else if($mtypes == 'total_Barge_Only_Got_details_meetings'){
                        $filter = ' total Barge Only Got details meetings';
                      }else if($mtypes == 'total_potential_meetings'){
                        $filter = ' total potential meetings';
                      }else if($mtypes == 'total_non_potential_meetings'){
                        $filter = ' total non potential meetings';
                      }else if($mtypes == 'total_topspender_meetings'){
                        $filter = ' total topspender meetings';
                      }else if($mtypes == 'total_upsell_client_meetings'){
                        $filter = ' total upsell client meetings';
                      }else if($mtypes == 'total_focus_funnel_meetings'){
                        $filter = ' total focus funnel meetings';
                      }else if($mtypes == 'total_keycompany_meetings'){
                        $filter = ' total Key Client meetings';
                      }else if($mtypes == 'total_pkclient_meetings'){
                        $filter = ' total positive key client meetings';
                      }else if($mtypes == 'total_priorityc_meetings'){
                        $filter = ' total Priority Calling meetings';
                      }else if($mtypes == 'total_write_mom_rp_meetings'){
                        $filter = ' Total Write MOM RP Meetings';
                      }else if($mtypes == 'total_pending_for_write_mom_rp_meeting'){
                        $filter = ' Total Pending For Write MOM RP Meeting';
                      }else if($mtypes == 'total_mom_data'){
                        $filter = ' Total MOM Data';
                      }else if($mtypes == 'total_approved_after_check_mom_data'){
                        $filter = ' Total Approved After Check MOM Data';
                      }else if($mtypes == 'total_reject_after_check_mom_data'){
                        $filter = ' Total Reject After Check MOM Data';
                      }else if($mtypes == 'total_NO_RP_after_check_mom_data'){
                        $filter = ' Total NO RP After Check MOM Data';
                      }else if($mtypes == 'total_pending_for_check_mom_data'){
                        $filter = ' Total Pending For Check MOM Data';
                      }else if ($mtypes == 'one_to_fifteen') {
                          $filter = 'Update in 1 to 15 Minutes';
                      }else if ($mtypes == 'fifteen_to_thirteen') {
                          $filter = 'Update in 15 to 30 Minutes';
                      }else if ($mtypes == 'thirteen_to_fifty') {
                          $filter = 'Update in 30 to 50 Minutes';
                      }else if ($mtypes == 'fifty_to_ninety') {
                          $filter = 'Update in 50 to 90 Minutes';
                      }else if ($mtypes == 'ninety_to_one_twenty') {
                          $filter = 'Update in 90 to 120 Minutes';
                      }else if ($mtypes == 'gretter_than_one_twenty') {
                          $filter = 'Update in Update in Gretter than 120';
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
                  <table id="example1" class="table table-striped table-bordered" cellspacing="0" width="100%">
                  <thead class="thead-dark">
                <tr>
                    <th class="text-capitalize">Sr. NO</th>
                    <th class="text-capitalize">Task Username</th>
                    <th class="text-capitalize">CID</th>
                    <th class="text-capitalize">Company Name</th>
                    <th class="text-capitalize">Current Status</th>
                    <th class="text-capitalize">Task Name</th>
                    <th class="text-capitalize">Task Status</th>
                    <th class="text-capitalize">original Task Date</th>
                    <th class="text-capitalize">Appointment DateTime</th>
                    <!-- <th class="text-capitalize">Action Taken</th>
                    <th class="text-capitalize">Purpose Achieved</th> -->
                    <th class="text-capitalize">Start Meeting</th>
                    <th class="text-capitalize">Closed Meeting</th>
                    <th class="text-capitalize">Company As</th>
                    <th class="text-capitalize">Company Descriptions</th>
                    <th class="text-capitalize">meetings status</th>
                    <?php if($mtypes == 'total_potential_meetings'){ ?>
                      <th class="text-capitalize">Potential Meetings</th>
                    <?php }?>
                    <?php if($mtypes == 'total_non_potential_meetings'){ ?>
                      <th class="text-capitalize">Non Potential Meetings</th>
                    <?php }?>
                    <?php if($mtypes == 'total_topspender_meetings'){ ?>
                      <th class="text-capitalize">topspender</th>
                    <?php }?>
                    <?php if($mtypes == 'total_upsell_client_meetings'){ ?>
                      <th class="text-capitalize">upsell client</th>
                    <?php }?>
                    <?php if($mtypes == 'total_focus_funnel_meetings'){ ?>
                      <th class="text-capitalize">focus funnel</th>
                    <?php }?>
                    <?php if($mtypes == 'total_keycompany_meetings'){ ?>
                      <th class="text-capitalize">Key Client</th>
                    <?php }?>
                    <?php if($mtypes == 'total_pkclient_meetings'){ ?>
                      <th class="text-capitalize">Positive Key Client</th>
                    <?php }?>
                    <?php if($mtypes == 'total_priorityc_meetings'){ ?>
                      <th class="text-capitalize">Priority Calling</th>
                    <?php }?>
                    <th class="text-capitalize">Remarks</th>
                    <th class="text-capitalize">meetings Type</th>
                    <?php if($comming_user_types == 2){ ?> 
                    <th class="text-capitalize">RP to No RP Convert</th>
                    <?php } ?>
                    <th class="text-capitalize">View MOM</th>
                    <th class="text-capitalize">Short mom</th>
                    <th class="text-capitalize">Task RePlanned Count</th>
                    <th class="text-capitalize">View Details</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $i = 1;
                foreach ($totalMeetingsUserByDatas as $row) {
                  $meeting_user_id = $row->user_id;
                  $meeting_task_id = $row->task_id;

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
                        <td><?= htmlspecialchars($row->current_status) ?></td>
                        <td><?= htmlspecialchars($row->task_name) ?></td>
                        <td><?php 
                        if($row->nextCFID == 0){
                            echo "<span class='bg-warning p-1'> Pending </span>"; 
                        }else{
                            echo "<span class='bg-success p-1'> Complete </span>"; 
                        }
                        ?></td>
                        <td><?= htmlspecialchars($row->fwd_date) ?></td>
                        <td><?= htmlspecialchars($row->appointmentdatetime) ?></td>
                        <?php /*
                        <td><?= htmlspecialchars($row->actontaken) ?></td>
                        <td><?= htmlspecialchars($row->purpose_achieved) ?></td>
                        */ ?>
                        <td><?= htmlspecialchars($row->startm) ?></td>
                        <td><?= htmlspecialchars($row->closem) ?></td>
                        <td><?= htmlspecialchars($row->company_as) ?></td>
                        <td><?= htmlspecialchars($row->company_descri) ?></td>
                        <td><?php 
                        if($row->meetings_status == 'Pending'){
                            echo "<span class='bg-warning p-1'> Pending </span>"; 
                        }else if($row->meetings_status == 'RPClose' || $row->meetings_status == 'Close' || $row->meetings_status == 'RP'){
                            echo "<span class='bg-success p-1'> Complete </span>"; 
                        }else if($row->meetings_status == 'Start'){
                          echo "<span class='bg-success p-1'> Start </span>"; 
                        }else if($row->meetings_status == 'Initiated'){
                          echo "<span class='bg-success p-1'> Initiated </span>"; 
                        }
                        ?></td>

                        <?php if($mtypes == 'total_potential_meetings' || $mtypes == 'total_non_potential_meetings'){ ?>
                          <td class="text-capitalize"><?= htmlspecialchars($row->potential) ?></td>
                        <?php }?>
                        <?php if($mtypes == 'total_topspender_meetings'){ ?>
                          <td class="text-capitalize"><?= htmlspecialchars($row->topspender) ?></td>
                        <?php }?>
                        <?php if($mtypes == 'total_upsell_client_meetings'){ ?>
                          <td class="text-capitalize"><?= htmlspecialchars($row->upsell_client) ?></td>
                        <?php }?>
                        <?php if($mtypes == 'total_focus_funnel_meetings'){ ?>
                          <td class="text-capitalize"><?= htmlspecialchars($row->focus_funnel) ?></td>
                        <?php }?>
                        <?php if($mtypes == 'total_keycompany_meetings'){ ?>
                          <td class="text-capitalize"><?= htmlspecialchars($row->keycompany) ?></td>
                        <?php }?>
                        <?php if($mtypes == 'total_pkclient_meetings'){ ?>
                          <td class="text-capitalize"><?= htmlspecialchars($row->pkclient) ?></td>
                        <?php }?>
                        <?php if($mtypes == 'total_priorityc_meetings'){ ?>
                          <td class="text-capitalize"><?= htmlspecialchars($row->priorityc) ?></td>
                        <?php }?>
                         <td><?= htmlspecialchars($row->remarks) ?></td>
                         <td><?= htmlspecialchars($row->mtype) ?></td>
                         <?php if($comming_user_types == 2){ ?> 
                         <td>
                          <?php if($row->mtype == 'RP' || $row->mtype == 'RPClose'){ ?> 
                         <span type="button" class="bg-danger p-1"  onclick="Reject(<?= $i ?>,<?= $meeting_task_id ?>,'NO RP')">Click</span>
                         <?php } ?>
                         </td>
                         <?php } ?>
                         <td>
                          <?php 
                          $momtask_mom = '';
                          $momtaskDatas = $this->Menu_model->GetTBLMomTaskByTaskId($meeting_task_id);
                          if(sizeof($momtaskDatas) > 0){
                             $momtask_id = $momtaskDatas[0]->id;
                             $momtask_mom = $momtaskDatas[0]->mom;
                             $momdatas = $this->Menu_model->GetMomDataByTaskId($momtask_id);
                             if(sizeof($momdatas) > 0){
                                $mom_id = $momdatas[0]->id;
                                echo '<a style="color:<?=$backgroundColorNew;?>!important" class="p-1 bg-success" target="_BLANK" href="' . base_url() . 'Management/MomDataCheckInAdmin/' . $mom_id . '/' . $row->cid . '">view</a>'; 
                             }else{
                              echo "<span class='p-1 bg-warning'>Pending For Write</span>";
                             }
                          }else{
                            echo "NA";
                          }
                           ?>
                          </td>
                        <td><?= $momtask_mom; ?></td>
                        <td> 
                            <a style="color:<?=$backgroundColorNew;?>!important" href="<?=base_url();?>Menu/ReplanedLog/<?=$row->task_id;  ?>" target="_BLANK"><?= htmlspecialchars($row->plan_count) ?></a>
                        </td>
                        <td> 
                          <?php 
                           if($row->nextCFID > 0) { ?>
                            <a class="bg-success p-1" href="<?= base_url(); ?>Menu/ViewMeetingDetails/<?= $meeting_task_id; ?>" target="_blank">View Details</a>
                        <?php } else { ?>
                            <span class='p-1 bg-warning'>Pending</span>
                        <?php } ?>
                        </td>
                    </tr>
              <?php $i++; } ?>
            </tbody>
                  </table>
                </div>
                <hr />
              </div>
            </div>
            </div>
          </div>
        </section>
        <?php if($comming_user_types == 2){ ?> 
        <div class="modal fade" id="exampleModalCenterdata" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered" role="document">
                                <div class="modal-content">
                                  <div class="modal-header" style="background-color: #d1e7ff;">
                                    <h5 class="modal-title text-center" style="color: red;" id="exampleModalLongTitle">RP to No RP Convert</h5>
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                      <span aria-hidden="true">&times;</span>
                                    </button>
                                  </div>
                                  <div class="modal-body" style="background-color: #ffc4c45c;">
                                   <form action="<?=base_url();?>Menu/NoRPConvertAfterChecking" method="post" >

                                        <input type="hidden" value="<?=$mtypes;?>" required name="mtypes">
                                        <input type="hidden" value="<?=$target_uid;?>" required name="target_uid">
                                        <input type="hidden" value="<?=$sdate;?>" required name="sdate">
                                        <input type="hidden" value="<?=$edate;?>" required name="edate">
                                        <input type="hidden" id="rejectid" value="" required name="no_rp_task_id">

                                         <div class="mb-3 mt-3">
                                          <textarea name="no_rp_remarks" cols="30" placeholder="Write Reason To Convert Meeting in No RP " class="form-control beautiful-textarea" required  rows="4"></textarea>
                                        </div>
                                        <div class="form-group text-center">
                                            <button type="submit" class="btn btn-danger mt-2">Convert Meeting in No RP</button>
                                        </div>
                                   </form>
                                  </div>
                                </div>
                              </div>
                            </div>
          <?php } ?>

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
          "buttons": ["copy", "csv", "excel", "pdf", "print", "colvis"]
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
      </script>
    </body>
</html>

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
           .card-body.box-profile {
      background: cadetblue;
      }
       body {
            background-color: #e0f7fa; /* A beautiful light cyan background */
            /* padding: 20px; */
        }
        .card {
            margin-bottom: 20px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        .card-header {
                background-color: navy;
            color: white;
        }
        .card {
    /* align-items: center; */
    text-align: center;
}
 .card .card-header {
    text-align: center;
    justify-content: center;
    display: flex;
}
p.card-text {
    font-weight: 600;
    color: #ff147f;
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

       <?php 
                      $data = $proposalDatas[0];

// Extract each field into separate variables
$task_user_id = $data->task_user_id;
$proposal_id = $data->proposal_id;
$task_username = $data->task_username;
$compname = $data->compname;
$cid = $data->cid;
$task_id = $data->task_id;
$nextCFID = $data->nextCFID;
$fwd_date = $data->fwd_date;
$appointmentdatetime = $data->appointmentdatetime;
$initiateddt = $data->initiateddt;
$updated_at = $data->updated_at;
$total_time_taken = $data->total_time_taken;
$late_remarks_message = $data->late_remarks_message;
$actontaken = $data->actontaken;
$purpose_achieved = $data->purpose_achieved;
$meeting_type = $data->meeting_type;
$actiontype_id = $data->actiontype_id;
$assignedto_id = $data->assignedto_id;
$cid_id = $data->cid_id;
$remarks = $data->remarks;
$selectby = $data->selectby;
$task_approved_status = $data->task_approved_status;
$task_approved_by = $data->task_approved_by;
$task_assignedto_by = $data->task_assignedto_by;
$task_aftertask = $data->task_aftertask;
$task_name = $data->task_name;
$current_status = $data->current_status;
$plan_count = $data->plan_count;
$potential = $data->potential;
$topspender = $data->topspender;
$upsell_client = $data->upsell_client;
$focus_funnel = $data->focus_funnel;
$keycompany = $data->keycompany;
$pkclient = $data->pkclient;
$priorityc = $data->priorityc;
$q1_twetenty_closure_funnel = $data->q1_twetenty_closure_funnel;
$potential_funnel_for_fy = $data->potential_funnel_for_fy;
$to_be_nurtured_for_fy = $data->to_be_nurtured_for_fy;
$fifity_new_lead_funnel = $data->fifity_new_lead_funnel;
$main_bd_name = $data->main_bd_name;
$main_bd_manager_name = $data->main_bd_manager_name;
$task_time_status = $data->task_time_status;
$task_time_new_status = $data->task_time_new_status;
$partner_name = $data->partner_name;
$proattach = $data->proattach;
$proposal_partner = $data->proposal_partner;
$propasal_types = $data->propasal_types;
$propasal_noofsc = $data->propasal_noofsc;
$propasal_budgetme = $data->propasal_budgetme;
$apr_status = $data->apr_status;
$propasal_apr_date = $data->propasal_apr_date;
$propasal_apr_remarks = $data->propasal_apr_remarks;
                      ?>
        <!-- Content Header (Page header) -->
        <div class="content-header">
          <div class="container-fluid">
            <div class="row mb-2">
              <div class="col-md-12 text-center">
                <div class="frame-layer-1">
                  <div class="frame-layer-2">
                    <div class="frame-layer-3" style="background: linear-gradient(to right, #ede7f6, #ffffff); border-radius: 12px; box-shadow: 0 4px 8px rgba(0,0,0,0.1); padding: 20px;">
                      <h1 class="m-0 premium-color-1 pb-2 mt-2" style="color: #ff009b;">
                        
                        <?php 
                          if($apr_status == 1){
                                echo $message = '<span class="bg-success p-1">Work After Proposal Approved</span>';
                                $tmessage = "Once a proposal is approved, the team initiates the implementation phase. Tasks include finalizing contracts, allocating resources, setting timelines, assigning responsibilities, and initiating the project kick-off. Regular monitoring, communication with stakeholders, and adherence to approved plans ensure timely progress, quality control, and achievement of project goals.";
                                $txtcolor = 'text-success';
                                $tttt = "Approved";
                              }
                              else if($apr_status == 2){ 
                                $txtcolor = 'text-danger';
                                 echo $message = '<span class="bg-danger p-1">Work After Proposal Rejected</span>';
                                 $tttt = "Rejected";
                                $tmessage = 'After a proposal is rejected, review the feedback carefully to identify areas of improvement. Revise your strategy or idea accordingly, strengthen weak points, and address concerns. Communicate with stakeholders to rebuild confidence. Use the rejection as a learning opportunity and stay persistent in refining and resubmitting or exploring alternative solutions.';
                              }
                        ?>
    
                    </h1>
                  
                    <center>
                        <p style="width: 80%;" class="premium-color-2 <?= $txtcolor;?>"><?=$tmessage;?></p>
                    </center>


                   

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



              <div class="row">
              
                         <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title">Proposal <?= $tttt;?> Date</h5>
                    </div>
                    <div class="card-body">
                        <p class="card-text"><?php 
                        
                        
                        
                        if (isset($propasal_apr_date) && $propasal_apr_date !== '' && $propasal_apr_date !== null) {
                            if($propasal_apr_date == 'NULL'){
                              echo 'NA';
                            }else{
                                if($propasal_apr_date == '0000-00-00 00:00:00'){
                                    echo 'NA';
                                }else{
                                    echo htmlspecialchars($propasal_apr_date);
                                }
                            }
                        }else{
                            echo 'NA';
                        }
                        
                        ?></p>
                    </div>
                </div>
            </div>
                    
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title">Company Name (cid)</h5>
                    </div>
                    <div class="card-body">
                        <p class="card-text"><?php echo htmlspecialchars($compname); ?> (<?php echo htmlspecialchars($cid); ?>)</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title">Current Status</h5>
                    </div>
                    <div class="card-body">
                        <p class="card-text"><?php echo htmlspecialchars($current_status); ?></p>
                    </div>
                </div>
            </div>
        </div>
  <div class="row">
    <div class="col-md-2">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title">Task Name</h5>
                    </div>
                    <div class="card-body">
                        <p class="card-text"><?php echo htmlspecialchars($task_name); ?></p>
                    </div>
                </div>
            </div>

          <div class="col-md-2">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title">Task Username</h5>
                    </div>
                    <div class="card-body">
                        <p class="card-text"><?php echo htmlspecialchars($task_username); ?></p>
                    </div>
                </div>
            </div>
            <div class="col-md-2">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title">Orignal Date</h5>
                    </div>
                    <div class="card-body">
                        <p class="card-text"><?php echo htmlspecialchars($fwd_date); ?></p>
                    </div>
                </div>
            </div>
            <div class="col-md-2">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title">Appointment Date</h5>
                    </div>
                    <div class="card-body">
                        <p class="card-text"><?php echo htmlspecialchars($appointmentdatetime); ?></p>
                    </div>
                </div>
            </div>

             <div class="col-md-2">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title">Initiated Date</h5>
                    </div>
                    <div class="card-body">
                        <p class="card-text"><?php echo htmlspecialchars($initiateddt); ?></p>
                    </div>
                </div>
            </div>
            <div class="col-md-2">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title">Updated At</h5>
                    </div>
                    <div class="card-body">
                        <p class="card-text"><?php echo htmlspecialchars($updated_at); ?></p>
                    </div>
                </div>
            </div>
             <!-- <div class="col-md-2">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title">Total Time Taken</h5>
                    </div>
                    <div class="card-body">
                        <p class="card-text"><?php echo htmlspecialchars($total_time_taken); ?></p>
                    </div>
                </div>
            </div> -->


        </div>
        
        <div class="row">
             
            <div class="col-md-4">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title">Action Taken</h5>
                    </div>
                    <div class="card-body">
                        <p class="card-text"><?php echo htmlspecialchars($actontaken); ?></p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title">Purpose Achieved</h5>
                    </div>
                    <div class="card-body">
                        <p class="card-text"><?php echo htmlspecialchars($purpose_achieved); ?></p>
                    </div>
                </div>
            </div>
             <div class="col-md-4">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title">Remarks</h5>
                    </div>
                    <div class="card-body">
                        <p class="card-text"><?php echo htmlspecialchars($remarks); ?></p>
                    </div>
                </div>
            </div>
        </div>
       
    
        <div class="row">
            <div class="col-md-3">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title">Task Approved Status</h5>
                    </div>
                    <div class="card-body">
                        <p class="card-text"><?php if(htmlspecialchars($task_approved_status) ==1){
                          echo "Approved";
                        }else{
                            if(htmlspecialchars($task_approved_status) ==''){
                                echo "Pending";
                            }else if(htmlspecialchars($task_approved_status) ==0){
                               echo "Rejected";
                            }
                            
                        } ?></p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title">Task Approved By</h5>
                    </div>
                    <div class="card-body">
                        <p class="card-text"><?php echo htmlspecialchars($task_approved_by); ?></p>
                    </div>
                </div>
            </div>
  <div class="col-md-3">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title">Task Time Status</h5>
                    </div>
                    <div class="card-body">
                        <p class="card-text"><?php echo htmlspecialchars($task_time_status); ?></p>
                    </div>
                </div>
            </div>
             <div class="col-md-2">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title">Selected By</h5>
                    </div>
                    <div class="card-body">
                        <p class="card-text"><?php echo htmlspecialchars($selectby); ?></p>
                    </div>
                </div>
            </div>
            <div class="col-md-1">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title">Plan Count</h5>
                    </div>
                    <div class="card-body">
                        <p class="card-text"><?php echo htmlspecialchars($plan_count); ?></p>
                    </div>
                </div>
            </div>
           
        </div>

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
                    <th class="text-capitalize">Main BD</th>
                    <th class="text-capitalize">Cluster Manager</th>

                    <th class="text-capitalize">PST</th>

                    <th class="text-capitalize">Assistant Sales Head (NAE)</th>
                    <th class="text-capitalize">Assistant Sales Head (W)</th>
                    <th class="text-capitalize">Assistant Sales Head (S)</th>
                    <th class="text-capitalize">RM East</th>
                    <th class="text-capitalize">RM North</th>
                    <th class="text-capitalize">Assistant Cluster Manager</th>



                    <th class="text-capitalize">Current Status</th>
                     <?php if($mtypes == 'total_in_q1_twetenty_closure_funnel'){ ?>
                      <th class="text-capitalize">Q1 20 Closure Funnel</th>
                    <?php }?>
                    <?php if($mtypes == 'total_in_potential_funnel_for_fy'){ ?>
                      <th class="text-capitalize">Potential Funnel For FY</th>
                    <?php }?>
                    <?php if($mtypes == 'total_in_to_be_nurtured_for_fy'){ ?>
                      <th class="text-capitalize">To be Nurtured for FY</th>
                    <?php }?>
                    <?php if($mtypes == 'total_in_fifity_new_lead_funnel'){ ?>
                      <th class="text-capitalize">50 New Lead Funnel</th>
                    <?php }?>



                    <th class="text-capitalize">Task Name</th>
                    <th class="text-capitalize">Task Status</th>
                    <th class="text-capitalize">Action</th>
                    <th class="text-capitalize">Purpose</th>
                    <th class="text-capitalize">Planned On Status</th>
                    <th class="text-capitalize">Change On Status</th>
                    <th class="text-capitalize">original Task Date</th>
                    <th class="text-capitalize">Appointment DateTime</th>
                    <th class="text-capitalize">Initiated DateTime</th>
                    <th class="text-capitalize">Updated DateTime</th>
                    <th class="text-capitalize">Total Time Taken</th>
                    <th class="text-capitalize">Late Remarks Message</th>
                    <th class="text-capitalize">Task Approved By</th>
                    <th class="text-capitalize">Remarks</th>
                    <th class="text-capitalize">Special Remarks</th>


                    <th class="text-capitalize">Partner Name</th>
                    <th class="text-capitalize">Potential Client</th>
                    <th class="text-capitalize">Top Spender Client</th>
                    <th class="text-capitalize">Upsell Client</th>
                    <th class="text-capitalize">Focus Funnel Client</th>
                    <th class="text-capitalize">Key Client</th>
                    <th class="text-capitalize">Positive Key Client</th>
                    <th class="text-capitalize">Priorityc Client</th>


                    <th class="text-capitalize">Q1 20 Closure Funnel	</th>
                    <th class="text-capitalize">Potential Funnel For FY</th>
                    <th class="text-capitalize">To be Nurtured for FY</th>
                    <th class="text-capitalize">50 New Lead Funnel</th>




                    <th class="text-capitalize">Task RePlanned Count</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $i = 1;
                foreach ($totalTasksUserByDatas as $row) {
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
                <hr />
              </div>
            </div>




             <div class="row">
               <div class="col-md-3">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title">Partner Name</h5>
                    </div>
                    <div class="card-body">
                        <p class="card-text"><?php echo htmlspecialchars($partner_name); ?></p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title">Proposal Partner</h5>
                    </div>
                    <div class="card-body">
                        <p class="card-text"><?php echo htmlspecialchars($proposal_partner); ?></p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title">Proposal Types</h5>
                    </div>
                    <div class="card-body">
                        <p class="card-text"><?php echo htmlspecialchars($propasal_types); ?></p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title">Proposal Number of SCHOOL</h5>
                    </div>
                    <div class="card-body">
                        <p class="card-text"><?php echo htmlspecialchars($propasal_noofsc); ?></p>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-3">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title">Proposal Budget</h5>
                    </div>
                    <div class="card-body">
                        <p class="card-text"><?php echo htmlspecialchars($propasal_budgetme); ?> ₹</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title">Proposal APR Status</h5>
                    </div>
                    <div class="card-body">
                        <p class="card-text"><?php 
                   
                         if (isset($apr_status) && $apr_status !== '' && $apr_status !== null) {
                            if($apr_status == 'NULL'){
                              echo 'NA';
                            }else{
                              if($apr_status == 0){
                                echo '<span class="p-1 bg-warning">Pending For Approve</span>';
                              }else if($apr_status == 1){
                                echo '<span class="p-1 bg-success">Approved</span>';
                              }else  if($apr_status == 2){
                                echo '<span class="p-1 bg-danger">Rejected</span>';
                              }
                            }
                        }else{
                            echo 'NA';
                        }
                        
                        ?></p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title">Proposal APR Date</h5>
                    </div>
                    <div class="card-body">
                        <p class="card-text"><?php 
                        
                        
                        
                        if (isset($propasal_apr_date) && $propasal_apr_date !== '' && $propasal_apr_date !== null) {
                            if($propasal_apr_date == 'NULL'){
                              echo 'NA';
                            }else{
                                if($propasal_apr_date == '0000-00-00 00:00:00'){
                                    echo 'NA';
                                }else{
                                    echo htmlspecialchars($propasal_apr_date);
                                }
                            }
                        }else{
                            echo 'NA';
                        }
                        
                        ?></p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title">Proposal Approved/Reject Remarks</h5>
                    </div>
                    <div class="card-body">
                        <p class="card-text"><?php 
                    
                        if (isset($propasal_apr_remarks) && $propasal_apr_remarks !== '' && $propasal_apr_remarks !== null) {
                            if($propasal_apr_remarks == 'NULL'){
                              echo 'NA';
                            }else{
                                if($propasal_apr_remarks == '0000-00-00 00:00:00'){
                                    echo 'NA';
                                }else{
                                    echo htmlspecialchars($propasal_apr_remarks);
                                }
                            }
                        }else{
                            echo 'NA';
                        }
                        
                        ?></p>
                    </div>
                </div>
            </div>
        </div>

                          <div class="row">
                             <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title">Proposal Attachment</h5>
                    </div>
                    <div class="card-body">
                       <a class="p-1" style="background: #f4ff0f; color: red;" target="_BLANK" href="<?=base_url().htmlspecialchars($proattach)?>">view proposal</a>
                      </p>
                    </div>
                </div>
            </div>
           </div>









            </div>
          </div>
        </section>
     


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

<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Team day before task review checking in CRM app (daily basis) | STEM APP | WebAPP</title>
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
      #file-preview img {
            display: block;
            margin: 5px 0;
        }

  #file-preview {
    text-align: center;
  }
  #file-preview {
    align-items: center;
    justify-content: center;
    display: flex;
    flex-wrap: wrap;
    background: #bdb76b42;
    padding: 10px;
}
.previewimg{
    box-shadow: rgba(0, 0, 0, 0.16) 0px 3px 6px, rgba(0, 0, 0, 0.23) 0px 3px 6px;
    padding: 10px;
}
textarea.form-control {
    box-shadow: rgba(9, 30, 66, 0.25) 0px 1px 1px, rgba(9, 30, 66, 0.13) 0px 0px 1px 1px;
    outline: none;
    border: none;
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
              <div class="col-sm-6">
                <h1 class="m-0"></h1>
              </div>
              <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                  <h4></h4>
                </ol>
              </div>
            </div>
          </div>
        </div>
        <section class="content">
          <div class="container-fluid">
          <?php if ($this->session->flashdata('pending_message')): ?>
              <div class="alert alert-success alert-dismissible">
            <button type="button" class="close" data-dismiss="alert">&times;</button>
            <?= $this->session->flashdata('pending_message'); ?>
          </div>
          <?php endif; ?>
          <?php if ($this->session->flashdata('success_message')): ?>
              <div class="alert alert-success alert-dismissible">
            <button type="button" class="close" data-dismiss="alert">&times;</button>
            <?= $this->session->flashdata('success_message'); ?>
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
            <div class="row">
              <div class="col-12">
                
                  <?php if($ctaskid != ''){ ?>
                    <div class="card">
                    <?php 
                    $getSCTaskByTaskID  = $this->Menu_model->GetSCTaskByTaskID($ctaskid);
                    $file_required      = $getSCTaskByTaskID[0]->file_required;
                    $unique_kpa         = $getSCTaskByTaskID[0]->unique_kpa;
                    $type_of_task       = $getSCTaskByTaskID[0]->type_of_task;
                    $category           = $getSCTaskByTaskID[0]->category;
                    $appointment_datetime = $getSCTaskByTaskID[0]->appointment_datetime;
                    $initiated_datetime   = $getSCTaskByTaskID[0]->initiated_datetime;
                    $created_at           = $getSCTaskByTaskID[0]->created_at;
                    $given_time_task_time = $getSCTaskByTaskID[0]->task_time;
                    $fwd_date             = $getSCTaskByTaskID[0]->fwd_date;
                    $current_time         = date("H:i:s");
                    $appointment_date     = date("Y-m-d", strtotime($appointment_datetime));
                    $fwd_dates            = date("Y-m-d", strtotime($fwd_date));
                    if($appointment_date == $fwd_dates){
                      $otaskDate = "";
                    }else{
                      $otaskDate = "<h6 style='color:rgb(51, 0, 255) !important;' >Original Task Date = ".$fwd_date."</h6>";
                    }
                     
                    if(is_null($given_time_task_time) || $given_time_task_time == '00:00:00'){
                      $agttime = '';
                    }else{
                      $agttime = "<h6 style='color:rgb(51, 0, 255) !important;' >Task Time Defined by Admin = ".$given_time_task_time."</h6>";
                    }

                    $rqueryDatacnt = sizeof($rqueryData);
                    $request_status = $rqueryData[0]->status;
                    if($request_status == 1){
                      $screqueststatus = "<span style='color: rgb(51, 0, 255) !important;'>Your Request Approved Successfully.</span>";
                    }
                    ?>
                  <div class="card-header">
                    <h3 class="text-center m-3 text-capitalize" style="color: #ff00ac !important;"> <?= $unique_kpa; ?> </h3>
                    <hr style="width:450px">
                    <div class="text-center">
                        <?=$agttime;?>
                        <?=$otaskDate;?>
                        <h6> <span style="color: #ff00ac !important;">Todays Task Appointment Date : <?=$appointment_datetime;?></span></h6>
                        <h6> <span style="color:rgb(255, 72, 0) !important;">Task Initiate Time : <?=$initiated_datetime;?></span></h6>
                        <?=$screqueststatus;?>
                    </div>
                  </div>
                  <!-- /.card-header -->
                  <div class="card-body">
                    <div class="body-content">
                      <div class="page-header">
                        <div class="form-group">
                          <fieldset>
                            <?php 
                            if ($current_time > $given_time_task_time && 
                            (!is_null($given_time_task_time) && $given_time_task_time !== '00:00:00') && 
                            ($request_status == 0 || $request_status == 2)) {
                         ?>
                                  <?php if($rqueryDatacnt == 0){ ?>
                                  <div class="text-center">
                                      <h4 class="text-center text-danger text-capitalize"> * The Task had to be submitted by the specified time of : <?=$given_time_task_time?></h4>
                                      <hr>
                                      <center>
                                          <form action="<?=base_url().'Menu/CreateRequestForScPerformTask'?>" method="POST" enctype="multipart/form-data">
                                            <input type="hidden" name="taskid" value="<?= $ctaskid ?>">
                                            <div class="form-group">
                                              <label for="exampleFormControlTextarea1" style="font-size:20px;">Type reason for late</label>
                                              <hr style="width:200px">
                                              <textarea required class="card p-2 form-control" name="reason_remarks" placeholder="* Please Type reason for late." rows="3" style="height:250px;"></textarea>
                                            </div>
                                            <hr>
                                            <button type="submit" class="btn btn-primary">Create Request</button>
                                          </form>
                                        </center>
                                    </div>
                                    <style>
                                      .btn.btn-primary{background: crimson; outline: navajowhite; border: navajowhite;}
                                      textarea.form-control{border: 1px solid red;}
                                    </style>
                                   <?php }else{ ?>
                                        <table class="table table-hover">
                                            <tbody>
                                              <tr class="table-primary">
                                                <th>Request Message</th>
                                                <td><?=$rqueryData[0]->remarks;?></td>
                                              </tr>
                                              <tr class="table-success">
                                                <th>Request Create Time</th>
                                                <td><?=$rqueryData[0]->created_at;?></td>
                                              </tr>
                                              <tr class="table-warning">
                                                <th>Request Status</th>
                                                <td><?php
                                                if($rqueryData[0]->status == 0){
                                                  echo "<span class='bg-warning p-1'>Pending</span>";
                                                }else if($rqueryData[0]->status == 1){
                                                  echo "<span class='bg-success p-1'>Approved</span>";
                                                }else if($rqueryData[0]->status == 2){
                                                  echo "<span class='bg-danger p-1'>Rejected</span>";
                                                }
                                                ?></td>
                                              </tr>
                                              <?php if($rqueryData[0]->status == 2){?>
                                                <tr class="table-success">
                                                <th>Reject Time</th>
                                                <td><?=$rqueryData[0]->apr_date;?></td>
                                              </tr>
                                                <tr class="table-success">
                                                <th>Reject Remarks</th>
                                                <td><?=$rqueryData[0]->apr_remarks;?></td>
                                              </tr>
                                               <?php } ?>
                                            </tbody>
                                          </table>
                                          <?php if($rqueryData[0]->status == 0){?>
                                          <div class="text-center">
                                                  <strong> * Please wait for approval. </strong>
                                          </div>
                                          <?php } ?>
                                   <?php }?>
                            <?php } else { ?>





                              <div class="text-right mt-2">
            <button class="btn btn-info" id="printPage">Print Page</button> <br>
          </div>
          <div class="container-fluid">
            <div class="row">
              <div class="col-md-12 text-center p-2">
                <div class="card-header bg-primary">
                  <h4 class="text-center">  Reports</h4>
                  <h5><?= $sd;?></h5>
                </div>
              </div>
            </div>
            <?php 
              //dd($getAllUserData);
              
              $fields_to_sum = [
                'total_task', 'approved_task', 'pending_approved', 'complete_task',
                'pending_task', 'total_autotask', 'after_task', 'complete_autotask',
                'pending_autotask', 'total_reject','pending_for_assign_after_reject_task','admin_create_request_for_self_assign','task_assignd_by_admin_after_reject','task_assignd_by_user_after_reject', 'action_yes_purpose_yes', 
                'action_yes_purpose_no', 'action_no_purpose_no', 'call_task',
                'email_task', 'scheduled_meeting_task', 'barg_meeting_task', 
                'whatsapp_activity', 'write_mom', 'write_proposal',
                // 'total_task_time',
                // 'total_plan_time_on_planner',
                // 'total_plan_time_with_autotask'
              ];
              
              $sums = array_fill_keys($fields_to_sum, 0);
              
              // Sum values
              foreach ($getAllUserData as $obj) {
                foreach ($fields_to_sum as $field) {
                    if (isset($obj->$field)) {
                        $sums[$field] += $obj->$field;
                    }
                }
              }
              
              $half_sums = ceil(count($sums) / 2);
              $firstHalf_sums = array_slice($sums, 0, $half_sums, true);
              $secondHalf_sums = array_slice($sums, $half_sums, null, true);
              
              ?>
            <div class="card p-3">
            <div class="text-center">
                      <h3>Total <?= sizeof($getAllUserData); ?> User</h3>
                      <hr/>
              </div>
              <div class="row">
                <div class="col-md-6">
                  <div class="card p-2">
                    <table class="table table-striped">
                      <tbody>
                        <?php foreach ($firstHalf_sums as $key_sums => $value): 
                          $readableKey_field_sums = ucwords(str_replace('_', ' ', $key_sums));
                          ?>
                        <tr>
                          <td><?php echo htmlspecialchars($readableKey_field_sums); ?></td>
                          <td>
                            <a href="<?=base_url();?>Menu/CheckUserCurrentDayActivityOnAPP/<?=$key_sums;?>/<?= $sd;?>/Team">
                            <?php echo htmlspecialchars($value); ?>
                            </a>
                          </td>
                        </tr>
                        <?php endforeach; ?>
                      </tbody>
                    </table>
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="card p-2">
                    <table class="table table-striped">
                      <tbody>
                        <?php foreach ($secondHalf_sums as $key_sums => $value):
                          $readableKey_field_sums = ucwords(str_replace('_', ' ', $key_sums));
                          ?>
                        <tr>
                          <td><?php echo htmlspecialchars($readableKey_field_sums); ?></td>
                          <td>
                            <a href="<?=base_url();?>Menu/CheckUserCurrentDayActivityOnAPP/<?=$key_sums;?>/<?= $sd;?>/Team">
                            <?php echo htmlspecialchars($value); ?>
                            </a>
                          </td>
                        </tr>
                        <?php endforeach; ?>
                      </tbody>
                    </table>
                  </div>
                </div>
              </div>
            </div>
            <div class="card p-3">
            <form action="<?=base_url().'Menu/TeamDayBeforeTaskReviewCheckingInCRMAppFormSubmit'?>" method="POST" enctype="multipart/form-data">
              <div class="row">
                <?php  //  dd($getAllUserData); ?>
                <?php
                  // Function to display a table for a specific range of data
                  function displayTable($keys, $values, $start, $end, $userType,$target_user_id) {
                      ?>
                <table class='table table-striped' style="background: aliceblue;">
                  <tbody>
                    <?php 
                      for ($i = $start; $i < $end; $i++) {
                          if (!isset($keys[$i])) break; // Prevent errors if $i exceeds array bounds
                          $readableKey = ucwords(str_replace('_', ' ', $keys[$i]));

                          ?>
                          <input type="hidden" name="<?= $keys[$i] ?>[]" value="<?= $values[$i]; ?>">
                          <?php
                          // Skipping specific keys based on conditions
                          if (
                              $keys[$i] == 'annual_review_target_task' || 
                              $keys[$i] == 'documentation_task' || 
                              ($userType == 3 && $keys[$i] == 'join_meeting_task') || 
                              ($userType == 3 && $keys[$i] == 'mom_check_task') || 
                              ($userType == 3 && $keys[$i] == 'praposal_check_task')
                          ) {
                              continue;
                          }
                      
                          $thisvalue = $values[$i];
                          if($keys[$i] == 'total_task_time' || $keys[$i] == 'total_plan_time_on_planner' || $keys[$i] == 'total_plan_time_with_autotask'){
                               // Calculate hours and remaining minutes
                               $total_task_time_thisvalue_hours      = floor($thisvalue / 60); 
                               $total_task_time_thisvalue_minutes    = $thisvalue % 60;     // Get the remaining minutes
                               $thisvalue  = "$total_task_time_thisvalue_hours hours and $total_task_time_thisvalue_minutes minutes";
                          }
                          ?>
                    <tr>
                      <td><strong><?= $readableKey; ?></strong></td>
                      <td>
                      <?php 
                      if ($keys[$i] !== 'name' 
                      && $keys[$i] !== 'user_id' 
                      && $keys[$i] !== 'type_name'
                      && $keys[$i] !== 'userworkfrom'
                      && $keys[$i] !== 'total_task_time'
                      && $keys[$i] !== 'total_plan_time_on_planner'
                      && $keys[$i] !== 'total_plan_time_with_autotask'
                      && $keys[$i] !== 'task_plan_for_today_request'
                      && $keys[$i] !== 'user_create_special_request_for_leave'
                      && $keys[$i] !== 'user_request_any_reminder'
                      && $keys[$i] !== 'pending_task_planner_request'
                      && $keys[$i] !== 'session_count'
                      ) {
                          ?>
                       
                          <a href="<?= base_url(); ?>Menu/CheckUserCurrentDayActivityOnAPP/<?= $keys[$i]; ?>/<?= date("Y-m-d"); ?>/User/<?= $target_user_id; ?>">
                              <?= $thisvalue; ?>
                          </a>
                      <?php } else { ?>
                          <?= $thisvalue; ?>
                      <?php  }  ?>
                      </td>
                    </tr>
                    <?php } ?>
                  </tbody>
                </table>
                <?php
                  }
                  ?>
                  
                <?php
                
                  foreach ($getAllUserData as $index => $userTaskData) { 
                      $name                              = $userTaskData->name;
                      $target_user_id                         = $userTaskData->user_id;
                      // $type_name                              = $userTaskData->type_name;
                      // $userworkfrom                           = $userTaskData->userworkfrom;
                      // $total_task                             = $userTaskData->total_task;
                      // $approved_task                          = $userTaskData->approved_task;
                      // $pending_approved                       = $userTaskData->pending_approved;
                      // $complete_task                          = $userTaskData->complete_task;
                      // $pending_task                           = $userTaskData->pending_task;
                      // $total_autotask                         = $userTaskData->total_autotask;
                      // $after_task                             = $userTaskData->after_task;
                      // $complete_autotask                      = $userTaskData->complete_autotask;
                      // $pending_autotask                       = $userTaskData->pending_autotask;
                      // $total_reject                           = $userTaskData->total_reject;
                      // $pending_for_assign_after_reject_task   = $userTaskData->pending_for_assign_after_reject_task;
                      // $admin_create_request_for_self_assign   = $userTaskData->admin_create_request_for_self_assign;
                      // $task_assignd_by_admin_after_reject     = $userTaskData->task_assignd_by_admin_after_reject;
                      // $task_assignd_by_user_after_reject      = $userTaskData->task_assignd_by_user_after_reject;
                      // $action_yes_purpose_yes                 = $userTaskData->action_yes_purpose_yes;
                      // $action_yes_purpose_no                  = $userTaskData->action_yes_purpose_no;
                      // $action_no_purpose_no                   = $userTaskData->action_no_purpose_no;
                      // $call_task                              = $userTaskData->call_task;
                      // $email_task                             = $userTaskData->email_task;
                      // $scheduled_meeting_task                 = $userTaskData->scheduled_meeting_task;
                      // $barg_meeting_task                      = $userTaskData->barg_meeting_task;
                      // $whatsapp_activity                      = $userTaskData->whatsapp_activity;
                      // $write_mom                              = $userTaskData->write_mom;
                      // $write_proposal                         = $userTaskData->write_proposal;
                      // $research_task                          = $userTaskData->research_task;
                      // $documentation_task                     = $userTaskData->documentation_task;
                      // $social_networking_task                 = $userTaskData->social_networking_task;
                      // $social_activity_task                   = $userTaskData->social_activity_task;
                      // $annual_review_target_task              = $userTaskData->annual_review_target_task;
                      // $join_meeting_task                      = $userTaskData->join_meeting_task;
                      // $mom_check_task                         = $userTaskData->mom_check_task;
                      // $create_bd_request_task                 = $userTaskData->create_bd_request_task;
                      // $submit_handover_task                   = $userTaskData->submit_handover_task;
                      // $praposal_check_task                    = $userTaskData->praposal_check_task;
                      // $total_task_time                        = $userTaskData->total_task_time;
                      // $total_plan_time_on_planner             = $userTaskData->total_plan_time_on_planner;
                      // $total_plan_time_with_autotask          = $userTaskData->total_plan_time_with_autotask;
                      // $task_plan_for_today_request            = $userTaskData->task_plan_for_today_request;
                      // $user_create_special_request_for_leave  = $userTaskData->user_create_special_request_for_leave;
                      // $user_request_any_reminder              = $userTaskData->user_request_any_reminder;
                      // $session_count                          = $userTaskData->session_count;
                  
                      // Calculate hours and remaining minutes
                      $total_task_time_hours      = floor($total_task_time / 60); 
                      $total_task_time_minutes    = $total_task_time % 60;     // Get the remaining minutes
                      $total_task_time            = "$total_task_time_hours hours and $total_task_time_minutes minutes";
                  
                      // Calculate hours and remaining minutes
                      $total_plan_time_on_planner_hours   = floor($total_plan_time_on_planner / 60); 
                      $total_plan_time_on_planner_minutes = $total_plan_time_on_planner % 60;     // Get the remaining minutes
                      $total_plan_time_on_planner         = "$total_plan_time_on_planner_hours hours and $total_plan_time_on_planner_minutes minutes";
                  
                  
                       // Calculate hours and remaining minutes
                       $total_plan_time_with_autotask_hours   = floor($total_plan_time_with_autotask / 60); 
                       $total_plan_time_with_autotask_minutes = $total_plan_time_with_autotask % 60;     // Get the remaining minutes
                       $total_plan_time_with_autotask         = "$total_plan_time_with_autotask_hours hours and $total_plan_time_with_autotask_minutes minutes";
                  
                    //   style="background:rgb(95, 25, 55); font-weight: 500; color: white;"
                      
                      ?>
                <hr>
                <?php 
                  $getslsctuserData = $this->Menu_model->get_userbyid($userTaskData->user_id);
                  $getslsctuserData_utype = $getslsctuserData[0]->type_id;
                  
                  $dataCount = count((array)$userTaskData);
                  $thirdCount = ceil($dataCount / 3); // Divide data into three parts
                  $keys = array_keys((array)$userTaskData);
                  $values = array_values((array)$userTaskData);
                  ?>
                <div class="col-md-12">
                  <div class="card text-center p-2" style="background: #ff006f; font-weight: 500; color: white;">
                    <h3>Name : <?= $userTaskData->name ?></h3>
                  </div>
                  <div class="form-group">
                  <input type="hidden" name="target_user_id[]" class="form-control" value="<?=$target_user_id?>">
                  <label for="exampleFormControlTextarea1" >Add Remarks</label>
                  <textarea required class="form-control" name="final_task_remarks[]" placeholder="* Please Add Remarks" rows="3"></textarea>
                </div>
                </div>
                <div class="col-md-4">
                  <!-- First Table -->
                  <?php displayTable($keys, $values, 0, $thirdCount, $getslsctuserData_utype,$target_user_id); ?>
                </div>
                <div class="col-md-4">
                  <!-- Second Table -->
                  <?php displayTable($keys, $values, $thirdCount, $thirdCount * 2, $getslsctuserData_utype,$target_user_id); ?>
                </div>
                <div class="col-md-4">
                  <!-- Third Table -->
                  <?php displayTable($keys, $values, $thirdCount * 2, $dataCount, $getslsctuserData_utype,$target_user_id); ?>
                </div>

                
                <hr/>
                <?php } ?>
              </div>
            </div>
          </div>

                            <br>
                            <div class="card p-4">
                              <center>
                               
                                  <input type="hidden" name="taskid" value="<?= $ctaskid ?>">
                                  <?php if($file_required == 'yes'){ ?> 
                                  <div class="form-group">
                                    <label for="exampleFormControlTextarea1">Upload Report</label>
                                    <input type="file" name="filname[]" id="filname" class="form-control" multiple required>
                                  </div>
                                  <?php } ?>
                                  <div class="form-group">
                                    <label for="exampleFormControlTextarea1">Add Remarks</label>
                                    <textarea required class="form-control" name="remarks" placeholder="* Please Add Daily Team Planner Summary With Location Remarks"  id="exampleFormControlTextarea1" rows="3"></textarea>
                                  </div>
                                  <hr>
                                  <div id="file-preview"></div>
                                  <hr>
                                  <button type="submit" class="btn btn-primary">Submit</button>
                                </form>
                              </center>
                            </div>
                            <?php } ?>
                          </fieldset>
                        </div>
                        <hr />
                      </div>
                    </div>
                  </div>
                </div>
                <?php }  ?>
              </div>
            </div>
          </div>
      </div>
    </div>
    </div>
    </section>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
    <script>
         $(document).ready(function () {
      $('#filname').on('change', function (event) {
          const files = event.target.files;
          const previewContainer = $('#file-preview');
          previewContainer.empty(); // Clear previous previews
      
          Array.from(files).forEach((file) => {
              const fileType = file.type;
              const fileReader = new FileReader();
      
              fileReader.onload = function (e) {
                  if (fileType.startsWith('image/')) {
                      // Display image preview
                      previewContainer.append(
                          `<img class="previewimg" src="${e.target.result}" alt="${file.name}" style="max-width: 250px; margin: 10px;">`
                      );
                  } else if (fileType === 'application/pdf') {
                      // Display PDF preview
                      previewContainer.append(
                          `<div>
                              <embed src="${e.target.result}" type="application/pdf" width="300" height="200" />
                           </div>`
                      );
                  } else {
                      previewContainer.append(
                          `<p>Unsupported file type: ${file.name}</p>`
                      );
                  }
              };
      
              fileReader.readAsDataURL(file);
          });
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
    <script>
      $(document).ready(function () {
        $("#printPage").click(function() {
              window.print();
          });
        })
    </script>
  </body>
</html>
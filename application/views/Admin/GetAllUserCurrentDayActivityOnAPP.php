<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Current Day Activity On APP | STEM APP | WebAPP</title>
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
     .scrollme {overflow-x: auto;}.content {margin: 20px;padding: 20px;border: 1px solid #ccc;}.card.card-fordetails{height: 100px;}.card.taskcntcard {height: 80px;align-items: center;justify-content: center;display: flex;box-shadow: rgba(60, 64, 67, 0.3) 0px 1px 2px 0px, rgba(60, 64, 67, 0.15) 0px 2px 6px 2px;}@media print {#printPage {display: none;}}table.table.table-striped {box-shadow: rgba(50, 50, 93, 0.25) 0px 30px 60px -12px inset, rgba(0, 0, 0, 0.3) 0px 18px 36px -18px inset;}
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
        <!-- /.content-header -->
        <?php 
          $bd = $this->Menu_model->get_userbyaid($uid);
          ?>
        <!-- Main content -->
        <section class="content" id="pdf-content">
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
                <div class="table-responsive">
                  <table id="example1111111" class="table table-striped table-bordered" cellspacing="0" width="100%">
                    <thead>
                      <tr class="thead-dark">
                        <th>Sr. no</th>
                        <th>Company Name</th>
                        <th>Action Name</th>
                        <!-- <th>Action Taken</th>
                          <th>Purpose Achieved</th> -->
                        <th>Status</th>
                        <th>Task Time</th>
                        <th>Task Appointment time</th>
                        <!-- <th>Task initiated time</th>
                          <th>Task Update time</th> -->
                        <th>Plan By</th>
                        <th>Filter Used</th>
                        <th>Task Work Status</th>
                        <th>Action Status</th>
                        <th>Approved By</th>
                        <th>Action</th>
                      </tr>
                    </thead>
                    <tbody>
                      <?php 
                        $i = 1;
                        $totalttask =$this->Menu_model->getTotalUserTaskDetailsOnPlannerONReport($target_user_id, $sd);
                        foreach ($totalttask as $taskdata) { 
                        
                            $taid = $taskdata->actiontype_id;
                            $tblId = $taskdata->id;
                            $taid = $this->Menu_model->get_actionbyid($taid);
                            $time = $taskdata->appointmentdatetime;
                            $reminder = $taskdata->reminder;
                            $rimby = $taskdata->reminderby;
                            $rimat = $taskdata->reminderat;
                            $assignedto_id = $taskdata->assignedto_id;
                            $status = $taskdata->approved_status;
                            $self_assign = $taskdata->self_assign;
                            $approver = $taskdata->approved_by;
                            $filter_by = json_decode($taskdata->filter_by, true);
                            $selectby = $taskdata->selectby;
                            $reviewtype = $taskdata->reviewtype;
                            $rimbyname = $this->Menu_model->get_userbyid($rimby);
                            $time = date('h:i a', strtotime($time));
                            $taskuser = $this->Menu_model->get_userbyid($assignedto_id);
                            $taskuname = $taskuser[0]->name;
                        
                            $actontaken = $taskdata->actontaken;
                            $purpose_achieved = $taskdata->purpose_achieved;
                            $remarks = $taskdata->remarks;
                            $initiateddt = $taskdata->initiateddt;
                            $updateddate = $taskdata->updateddate;
                        
                        ?>
                      <tr>
                        <td><?= $i; ?></td>
                        <td><?= $taskdata->compname ?></td>
                        <td><?= $taid[0]->name ?></td>
                        <?php /* <td><?= $actontaken ?></td>
                        <td><?= $purpose_achieved ?></td>
                        */ ?>
                        <td><span class="p-3" style="color:<?= $taskdata->color ?>;"><?= $taskdata->name ?></span></td>
                        <td><?= $time ?></td>
                        <td><?= $taskdata->appointmentdatetime ?></td>
                        <?php /* <td><?= $initiateddt ?></td>
                        <td><?= $updateddate ?></td>
                        */ ?>
                        <td><?= $taskuname ?></td>
                        <td><?php 
                          if(sizeof($filter_by) > 0){
                          foreach ($filter_by as $key => $value) {
                            if($key == 'Plan_BY'){
                              echo $value."<br/>";
                             
                            }elseif($key == 'Filter_By'){
                              echo $value."<br/>";
                            }else{
                            if($key == 'comp_status'){
                              $tstatus = $this->Menu_model->get_statusbyid($value);
                              $tstatusname = $tstatus[0]->name;
                              echo "<span class='p-1'>Status&nbsp;:&nbsp;".$tstatusname."</span><br/>";
                            }elseif($key == 'task'){
                               
                              $taction = $this->Menu_model->get_actionbyid($value);
                              $tactionname = $taction[0]->name;
                              echo "<span class='p-1'>Task&nbsp;:&nbsp;".$tactionname."</span><br/>";
                            }elseif($key == 'taskActionbyuser'){
                                
                              echo "<span class='p-1'>Action&nbsp;Taken:&nbsp;".$value."</span><br/>";
                            }else{
                                echo $selectby;
                              }
                            }
                          }
                          }else{
                            if(!is_null($reviewtype)){
                                echo $reviewtype." Review";
                            }else if($self_assign == 4){ 
                                echo "AutoTask";
                                 }else{
                                   if($taid == 3 || $taid == 4){
                                        echo "Task Actions";
                                   }else{
                                    echo "Task Actions";
                                   }
                                 }
                          }
                          ?></td>
                        <td>
                          <?php 
                            if($taskdata->actontaken == 'no'){?>
                          <span class="p-1 bg-warning mr-2">Pending</span>
                          <?php }else{ ?>
                          <span class="p-1 bg-success mr-2">Complete</span>
                          <?php } ?>
                        </td>
                        <td>
                          <span class="p-1 bg-<?= ($status == 1) ? 'success' : (($status == '') ? 'warning' : 'danger');?> mr-2">
                          <?= ($status == 1) ? 'Approved' : (($status == '') ? 'Pending' : 'Rejected'); ?>
                          </span>
                        </td>
                        <td><?php
                          if($approver ==''){ ?>
                          <span class="p-1 bg-warning mr-2">Pending</span>
                          <?php }else{
                            if($approver == 'System'){ ?>
                          <span class="p-1 bg-success mr-2"><?= $approver ?></span>
                          <?php }else{
                            $userData = $this->Menu_model->get_userbyid($approver);
                            $name = $userData[0]->name;
                            echo $name;
                            }
                            }
                            ?>
                        </td>
                        <td>
                          <?php if($status ==''){?>
                          <div>
                            <span class="p-1 bg-warning mr-2">Pending</span>
                          </div>
                          <?php }elseif($status == 0){
                            if($self_assign == ''){ ?>
                          <div>
                            <button type="button" class="btn btn-primary" onclick="window.location.href='<?= base_url(); ?>Menu/AssignTaskById/<?= $tblId ?>/<?= $taskdate ?>'">Assign Task</button> 
                            <hr>
                            <button type="button" class="btn btn-secondary" id="self_assign" style="margin-left: 10px;" onclick="window.location.href='<?= base_url(); ?>Menu/selfAssign/<?= $tblId ?>/1/<?= $taskdate ?>'" <?= ($self_assign == 1) ? 'disabled' : '' ?>>Self Assign</button>
                          </div>
                          <?php  }else if($self_assign == 1){ ?>
                          <span class="p-1 bg-success mr-2">Admin&nbsp;Created&nbsp;Self-Assign&nbsp;Request</span>
                          <?php } else if($self_assign == 2){?>
                          <span class="p-1 bg-success mr-2">Task&nbsp;successfully&nbsp;assigned&nbsp;by&nbsp;admin!</span>
                          <?php }else if($self_assign == 3){ ?>
                          <span class="p-1 bg-success mr-2">Task&nbsp;successfully&nbsp;assigned&nbsp;by&nbsp;user!</span>
                          <?php }else if($self_assign == 4){ ?>
                          <span class="p-1 bg-success mr-2">Task&nbsp;Approved&nbsp;&nbsp;by&nbsp;System!</span>
                          <?php }?>
                          <?php }elseif($status == 1){ ?>
                          <?php if($self_assign == 4){ ?>
                          <span class="p-1 bg-success mr-2">This&nbsp;is&nbsp;Autotask!</span>
                          <?php }else{ ?>
                          <span class="p-1 bg-success mr-2">Approved</span> 
                          <?php }  ?>
                          <?php }?>
                        </td>
                        <?php $i++; } ?>
                      </tr>
                    </tbody>
                  </table>
                </div>
                <hr/>
                <?php } ?>
              </div>
            </div>
          </div>
      </div>
      </section>
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
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    <script src="https://html2canvas.hertzen.com/dist/html2canvas.min.js"></script>
    <script src="https://code.jquery.com/ui/1.13.2/jquery-ui.min.js"></script>
    <script>
      $(document).ready(function() {
          // Define an array of colors
      //     var colors = [
      //     "#FF5733", "#33FF57", "#3357FF", "#FF33A1", "#F5B700", "#7BFF33", 
      //     "#FF5733", "#33FF57", "#3357FF", "#FF33A1", "#F5B700", "#7BFF33", 
      //     "#FF5733", "#33FF57", "#3357FF", "#FF33A1", "#F5B700", "#7BFF33", 
      //     "#FF5733", "#33FF57", "#3357FF", "#FF33A1", "#F5B700", "#7BFF33", 
      //     "#FF5733", "#33FF57", "#3357FF", "#FF33A1", "#F5B700", "#7BFF33", 
      // ];
      
      
      //     var colors = [
      //     "#FF5733", "#33FF57", "#3357FF", "#FF33A1", "#F5B700", "#7BFF33", 
      //     "#D32F2F", "#C2185B", "#1976D2", "#0288D1", "#388E3C", "#7B1FA2", 
      //     "#8E24AA", "#FBC02D", "#F57C00", "#0097A7", "#B39DDB", "#64B5F6", 
      //     "#81C784", "#4CAF50", "#FFEB3B", "#FF9800", "#795548", "#607D8B", 
      //     "#3F51B5", "#9C27B0", "#8BC34A", "#FF4081", "#03A9F4", "#CDDC39", 
      //     "#9E9D24", "#FF5722", "#455A64", "#546E7A", "#FF1744", "#00E5FF", 
      //     "#1DE9B6", "#3D5AFE", "#FF6D00", "#00B8D4", "#FF9100", "#9E1B32"
      // ];
      
          // Loop through each card and assign a random color from the array
          // $(".card.taskcntcard").each(function(index) {
          //     // Ensure the index does not go out of bounds
          //     if (index < colors.length) {
          //         $(this).css("background-color", colors[index]);
          //     }
          // });
      
          var colors = [
          "#fff", "##fff", "##fff", "##fff", "##fff", "##fff", 
          "#fff", "##fff", "##fff", "##fff", "##fff", "##fff", 
          "#fff", "##fff", "##fff", "##fff", "##fff", "##fff", 
          "#fff", "##fff", "##fff", "##fff", "##fff", "##fff", 
          "#fff", "##fff", "##fff", "##fff", "##fff", "##fff", 
      ];
      
          $(".card.taskcntcard").each(function(index) {
            if (index < colors.length) {
                // Delay based on index to stagger the animation for each card
                $(this).show().delay(index * 200).animate({
                    backgroundColor: colors[index]
                }, 1000); // Animate over 1 second
            }
        });
      
      });
      
            $(document).ready(function () {
            
                $.ajax({
                url: '<?= base_url(); ?>Menu/CheckPlannerlogReportExistsOrNot',
                type: "POST",
                data: {
                  filetypesname: 'AllMomDetails',
                },
                cache: false,
                success: function(result) {
                    if (result !== 'exists') {
                        let clickTriggered = false;
                        setTimeout(function() {
                            if (!clickTriggered) {
                                $("#generate-pdf").click();
                                clickTriggered = true;
                            }
                        }, 2000);
                    }else{
                        console.log("Planner Log Report Allready Store");
                    }
                }
            });

            $("#generate-pdf").click();
            
            $('#generate-pdf').on('click', function () {
            const { jsPDF } = window.jspdf;
            $(this).css("background-color", "green");
            
            // Use html2canvas to capture the content as an image
            html2canvas(document.querySelector('#pdf-content')).then(function (canvas) {
                const imgData = canvas.toDataURL('image/png'); // Convert canvas to image
                const pdf = new jsPDF('p', 'mm', 'a4');
            
                const pdfWidth = pdf.internal.pageSize.getWidth(); // A4 page width
                const pdfHeight = pdf.internal.pageSize.getHeight(); // A4 page height
            
                const canvasWidth = canvas.width; // Image width
                const canvasHeight = canvas.height; // Image height
            
                // Scale image to full page width, calculate the height maintaining aspect ratio
                const ratio = pdfWidth / canvasWidth; // Scaling factor based on full page width
                const newWidth = pdfWidth; // Full page width
                const newHeight = canvasHeight * ratio; // Scale height based on the aspect ratio
            
                let yOffset = 0; // Start at the top of the first page
            
                // Loop to add multiple pages if the content exceeds the page height
                while (yOffset < newHeight) {
                    // If not the first page, add a new page
                    if (yOffset > 0) {
                        pdf.addPage();
                    }
            
                    // Add the image to the PDF at the current offset
                    pdf.addImage(imgData, 'PNG', 0, -yOffset, newWidth, newHeight);
            
                    // Move the offset down by the height of the current page
                    yOffset += pdfHeight;
            
                    // If the content overflows the current page, add it to the next page
                    if (yOffset + pdfHeight > newHeight) {
                        const remainingHeight = newHeight - yOffset;
                        pdf.addImage(imgData, 'PNG', 0, -yOffset, newWidth, remainingHeight);
                        break; // Stop adding more content
                    }
                }
            
                const pdfBase64 = pdf.output('datauristring');
            
                $.ajax({
                    url: '<?=base_url();?>Menu/UploadCRMReports',
                    type: 'POST',
                    data: {
                        pdf: pdfBase64.split(',')[1], // Only send the Base64 content
                        filename: 'AllMomDetails.pdf',
                        filetypesname: 'AllMomDetails',
                    },
                    success: function (response) {
                        console.log('PDF uploaded successfully! Server Response: ' + response);
                    },
                    error: function (xhr, status, error) {
                        console.error('Upload error: ' + error);
                    },
                });
            });
            });
            });
    </script>
    <script>
      $(document).ready(function () {
        $("#printPage").click(function() {
              window.print();
          });
        })
    </script>
  </body>
</html>
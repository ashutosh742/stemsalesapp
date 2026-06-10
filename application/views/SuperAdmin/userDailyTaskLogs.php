<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="wi$dth=device-wi$dth, initial-scale=1">
    <title>STEM APP | WebAPP</title>
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
      table.table.table-striped {
    box-shadow: rgba(50, 50, 93, 0.25) 0px 30px 60px -12px inset, rgba(0, 0, 0, 0.3) 0px 18px 36px -18px inset;
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
              <!-- /.col -->
              <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                  <h4></h4>
                </ol>
              </div>
              <!-- /.col -->
            </div>
            <!-- /.row -->
          </div>
          <!-- /.container-fluid -->
        </div>
        <!-- /.content-header -->
        <?php 
          $result = [];
          
          // Group and count by 'aname'
          foreach ($report_content as $item) {
              $aname = $item->aname;
              if (isset($result[$aname])) {
                  $result[$aname]++;
              } else {
                  $result[$aname] = 1;
              }
          }
          

          $staskuser = $this->Menu_model->get_userbyid($report_content[0]->user_id);
          
          $staskuname = $staskuser[0]->name;
          
          $taruserid = $report_content[0]->user_id;
          $allrequest = $this->Menu_model->GetAllTypeRequestCreateByUserWithPlannerHours($taruserid,$target_date);
          //dd($result); ?>
        <!-- Main content -->
        <section class="content">
          <div class="text-right mt-2">
            <button class="btn btn-info" id="printPage">Print Page</button> <br/><br/>
          </div>
          <div class="container-fluid">
            <div class="card text-center">
              <h3><?= $staskuname; ?></h3>
            </div>
            <div class="row">
              <div class="col-md-6">
                <div class="card p-2">
                  <table class="table table-striped">
                    <tr>
                      <td><strong>Total Task</strong></td>
                      <td><strong><?=sizeof($report_content);?></strong></td>
                    </tr>
                    <?php foreach($result as $tkeys=>$tvalue): ?>
                    <tr>
                      <td><strong><?=$tkeys?></strong></td>
                      <td><strong><?=$tvalue?></strong></td>
                    </tr>
                    <?php endforeach; ?>
                  </table>
                </div>
              </div>
              <div class="col-md-6">
                <div class="card p-2">
                  <table class="table table-striped">
                    <?php
                      // dd($allrequest);
                       $allrequests = $allrequest[0];
                       foreach($allrequests as $trkeys=>$trvalue):
                           
                           $readableKeytr = ucwords(str_replace('_', ' ', $trkeys));
                      
                           if (
                               $trkeys == 'total_task_time' || 
                               $trkeys == 'total_plan_time_with_autotask'
                           ) {
                               continue;
                           }
                      
                          


                      
                           if($trkeys == 'total_plan_time_on_planner'){
                               $total_task_time_thisvalue_hours      = floor($trvalue / 60); 
                               $total_task_time_thisvalue_minutes    = $trvalue % 60;     // Get the remaining minutes
                               $trvalue  = "$total_task_time_thisvalue_hours hours and $total_task_time_thisvalue_minutes minutes";
                           }
                      
                           
                      
                      
                           ?>
                    <tr>
                      <td><strong><?=$readableKeytr?></strong></td>
                      <td><strong><?=$trvalue?></strong></td>
                    </tr>


<?php 
  if ($trkeys == 'task_plan_for_today_request'){
    if($trvalue == 'Yes'){
           $gtplrequest = $this->Menu_model->GetTodaysPlannerRequestNew($taruserid);
    }
 }


?>




                    <?php endforeach; ?>
                  </table>
                </div>
              </div>

              <div class="col-md-12">
                <div class="card">
                  <div class="h5 text-center">
                    <strong>Task Plan for Today Request</strong>
                  </div>
               
              <?php  
              if(sizeof($gtplrequest) > 0){
                ?>
                <table class="table table-striped">
                  <?php
                  $gtplrequestrt = $gtplrequest[0];
                 foreach($gtplrequestrt as $pkeys => $prvalue){

                  if (
                    $pkeys == 'id' || 
                    $pkeys == 'user_id' || 
                    $pkeys == 'admin_id' || 
                    $pkeys == 'date' || 
                    $pkeys == 'updated_at' || 
                    $pkeys == 'taskcnt' 
                ) {
                    continue;
                }


                $pkeysr = ucwords(str_replace('_', ' ', $pkeys));
                 ?>
                  
                            <tr>
                            <td><strong><?=$pkeysr?></strong></td>
                            <td><strong><?=$prvalue?></strong></td>
                          </tr>
      
                 <?php } ?>
                </table>
              <?php } ?>
            </div>
            </div>
            </div>
            <div class="row">
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
                      <!-- <th>Action Status</th>
                        <th>Approved By</th>
                        <th>Action</th> -->
                    </tr>
                  </thead>
                  <tbody>
                    <?php 
                      $i = 1;
                      // $totalttask =$this->Menu_model->getTotalUserTaskDetailsOnPlannerONReport($target_user_id, $sd);
                      // echo $this->db->last_query();
                      foreach ($report_content as $taskdata) { 
                      
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
                      <?php $i++; } ?>
                    </tr>
                  </tbody>
                </table>
              </div>
            </div>
            <!-- /.card-body -->
          </div>
          <!-- /.card -->
      </div>
      <!-- /.col -->
    </div>
    <!-- /.row -->
    </div>
    <!-- /.container-fluid -->
    </section>
    </div>    
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
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
    <script>
      $(document).ready(function () {
       $("#printPage").click(function() {
          window.print();
        });
      });
    </script>
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
        "buttons": ["csv", "excel", "pdf", "print"]
      }).buttons().container().appendTo('#example1_wrapper .col-md-6:eq(0)');
    </script>
  </body>
</html>
<?php exit(); ?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Planner Task Approval Page | STEM APP | WebAPP</title>
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
    .text-success {
      color: #28a745 !important;
    }
    .text-danger {
      color: #dc3545 !important;
    }
    .text-warning {
      color: #ffc107 !important;
    }
    .font-weight-bold {
      font-weight: bold;
    }
    .card-header, .col.card.coladjust1 select, .col.card.coladjust31 select, .container-fluid.body-content.mt-4, .each, .form-row>.col, .form-row>[class*=col-], .table-striped tbody tr:nth-of-type(odd), input.form-control, table.table-bordered.dataTable {
    box-shadow: inset 4px 4px 7px rgba(55, 84, 170, .15), inset -4px -4px 10px #fff, 0 0 2px rgba(255, 255, 255, .2);
    transition: box-shadow .2s ease-in-out;
}
.each {
    background: #f1f3f6;
    font-family: Arial, Helvetica, sans-serif;
    border: 5px solid #eaeef5;
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

  </style>
</head>
<body class="hold-transition sidebar-mini layout-fixed">
  <div class="wrapper">
    <!-- Navbar -->
   <?php $this->load->view($dep_name.'/nav.php');?>
    <!-- /.navbar -->

    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
      <!-- Content Header (Page header) -->
      <div class="content-header">
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
          <div class="row mb-2">
            <div class="col-sm-6">
              <ol class="breadcrumb float-sm-right">
                <h4><?php $uid=$user['user_id']; ?></h4>
              </ol>
            </div>
          </div>
        </div>
      </div>
      <!-- Main content -->
      <section class="content">
        <div class="container-fluid">
          <div class="row">
           <div class="col-12" style="margin-top:10px;">

            <div style="background:#fff; padding:15px; border-radius:8px; box-shadow:0 2px 6px rgba(0,0,0,0.1);">
              <form method="POST" action="<?=base_url()?>Menu/PlannerTaskApprovelPageTestData" 
                    style="display:flex; align-items:center; gap:15px;">
                <label style="font-weight:600; font-size:28px;">📅</label>
                <input type="date" name="sdate" value="<?= $sdate ?>" 
                      style="padding:6px 10px; border:1px solid #ccc; border-radius:6px; font-size:14px;">
                <button type="submit" 
                        style="background:#0d6efd; color:white; border:none; padding:7px 15px; border-radius:6px; cursor:pointer; font-size:14px;">
                 🔍 Filter
                </button>
              </form>
            </div>

            <hr>

              <div class="card-details">
               
                <!-- /.card-header -->
                <div class="card-body each">
                  <div class="body-content--1">
                    <div class="page-header--2">
                      <?php
                      $data = $groupedData;
                      $data1 = $groupedData;

                      ?>
                      <fieldset>
                        <div class="card" style="background: linear-gradient(to right, #dfe9f3, #ffffff); border-radius: 12px; box-shadow: 0 4px 8px rgba(0,0,0,0.1); padding: 20px;">
                          <div class="text-center p-2" style="background: linear-gradient(to right, #e0f7fa, #ffffff); border-radius: 12px; box-shadow: 0 4px 8px rgba(0,0,0,0.1); padding: 4px;">
                            <h5 style="font-size:18px; margin:0; color:#333;">
                                <strong>Planner Task Details 📋</strong>
                            </h5>
                            <p style="margin:4px 0; font-style:italic; color:#555;">
                                <b><?= $sdate ?></b>
                            </p>
                          </div>
                          <br>
                          <div class="table-responsive text-nowrap each" style="overflow-x: auto;" style="background: linear-gradient(to right, #e8f5e9, #ffffff); border-radius: 12px; box-shadow: 0 4px 8px rgba(0,0,0,0.1); padding: 20px;">
                            <table class="table table-striped table-bordered" id="example1">
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
                                  <th>Total Review Plan Time</th>
                                  <th>View Details</th>
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
                                //   echo "<pre>";
                                //   print_r('total_review_plan_time = '.$userPallnerData->total_review_plan_time);
                                //  echo 'total_plan_task_time = '.$userPallnerData->total_plan_task_time."<br/>";
                                //   echo 'total_review_plan_time = '.$userPallnerData->total_review_plan_time."<br/>";
                                //   print_r($userTasksPlanner->total_review_plan_time);

                                  foreach ($userTasksPlanner as $userTasksPlanners) {
                                    if (!empty($userTasksPlanners->total_review_plan_time)) {
                                         $total_review_plan_time = $userTasksPlanners->total_review_plan_time; 
                                    }else{
                                        $total_review_plan_time = '';
                                    }
                                }

                                  $planner_user_id        = $userPallnerData->planner_user_id;
                                  $comingUserDetailDatas  = $this->Menu_model->get_userbyid($planner_user_id); 
                                  $comingTypeId           = $comingUserDetailDatas[0]->type_id;

                                  echo "<tr>";
                                  echo "<td style='color:rgb(14, 3, 228) !important;'>" . $i."</td>";
                                  echo "<td style='color: #e403d9 !important;'>" . htmlspecialchars($userPallnerData->name) . "</td>";
                                  echo "<td class='text-danger font-weight-bold'>" . htmlspecialchars($userPallnerData->total_task_count) . "</td>";
                                  foreach (array_keys($taskTypes) as $taskType) {
                                    echo "<td>" . (isset($taskCounts[$userIdPlanner][$taskType]) ? "<span class='text-success font-weight-bold'>" . $taskCounts[$userIdPlanner][$taskType] . "</span>" : "<span class='text-warning font-weight-bold'>0</span>") . "</td>";
                                  }

                                  $total_plan_task_time_data = !empty($userPallnerData->total_plan_task_time) ? $userPallnerData->total_plan_task_time : 'N/A';
                                  if($total_plan_task_time_data !== 'N/A'){
                                    preg_match('/(\d+)\s+hours/', $userPallnerData->total_plan_task_time, $matches);
                                    $hours_colors = isset($matches[1]) ? (int)$matches[1] : 0;

                                    if(in_array($comingTypeId, [13])){
                                    $hours_colors_text = $hours_colors >= 4 ? 'text-success' : 'text-danger';
                                    }else if(in_array($comingTypeId, [22,23])){
                                    $hours_colors_text = $hours_colors >= 3 ? 'text-success' : 'text-danger';
                                    }else{
                                      $hours_colors_text = $hours_colors >= 6 ? 'text-success' : 'text-danger';
                                    }   
                                    echo "<td class='" . $hours_colors_text . " font-weight-bold'>" . htmlspecialchars($userPallnerData->total_plan_task_time) . "</td>";
                                  }else{
                                    echo "<td class='font-weight-bold'>" . htmlspecialchars($total_plan_task_time_data) . "</td>";
                                  }
                              
                                  // Debugging: Print the value of total_review_plan_time
                                  echo "<td class='font-weight-bold'>" . htmlspecialchars($total_review_plan_time) . "</td>";

                                  echo "<td><a class='font-weight-bold bg-primary p-1' target='_BLANK' href='" . base_url() . "Menu/CheckTaskDetailsByUser/$userPallnerData->planner_user_id/$sdate'>View</a></td>";
                                  echo "</tr>";
                                  $i++;
                                }
                                ?>
                              </tbody>
                            </table>
                          </div>
                          <!-- <br> -->
                        </div>
                      </fieldset>
                    </div>
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
        </div>
        <!-- /.container-fluid -->
        <div class="modal fade" id="exampleModalCenterdata" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
          <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
              <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLongTitle">Add Reject Remark</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">&times;</span>
                </button>
              </div>
              <div class="modal-body">
                <form action="<?=base_url();?>Menu/momReject" method="post">
                  <input type="hidden" id="rejectid" value="" name="reject">
                  <div class="mb-3 mt-3">
                    <textarea id="rejectreamrk" name="rejectreamrk" cols="30" placeholder="Add Remark" class="form-control" rows="4"></textarea>
                  </div>
                  <div class="form-group text-center">
                    <button type="submit" class="btn btn-success mt-2">Submit</button>
                  </div>
                </form>
              </div>
            </div>
          </div>
        </div>
      </section>

      <footer class="main-footer">
        <strong>Copyright &copy; 2021-<?= date("Y"); ?> <a href="<?=base_url();?>">Stemlearning</a>.</strong>
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
        "buttons": ["copy", "csv", "excel", "pdf", "print", "colvis"]
      }).buttons().container().appendTo('#example1_wrapper .col-md-6:eq(0)');
    </script>
    <script type='text/javascript'>
      function RejectButton(mid, id, val) {
        $('#exampleModalCenter' + mid).modal('show');
        $('#exampleModalCenter' + mid + ' #rejectid').val(id);
      }

      function Reject(mid, id, val) {
        $('#exampleModalCenterdata').modal('show');
        $('#rejectid').val(id);
      }
    </script>
  </body>
</html>

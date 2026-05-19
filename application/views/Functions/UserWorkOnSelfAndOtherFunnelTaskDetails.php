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
    /* td {
    font-weight: 700;
} */
    </style>
  </head>
  <body class="hold-transition sidebar-mini layout-fixed">
    <div class="wrapper">
      <!-- Preloader -->

      <!-- Navbar -->
         <?php $this->load->view($dep_name.'/nav.php');?>
      <!-- /.navbar -->
       <style>
        /* .content-card {
        background-image: url('https://raw.githubusercontent.com/mobalti/open-props-interfaces/refs/heads/main/ai-hero-chat-popover/assets/bg-gradient.jpg');
        background-size: cover;
        background-position: center;
        }
        .glass-card {
    background: rgba(255, 255, 255, 0.15);
    border-radius: 15px;
    backdrop-filter: blur(5px);
    -webkit-backdrop-filter: blur(10px);
    box-shadow: 0 4px 30px rgba(0, 0, 0, 0.1);
} */

/* .content-card  {
  background-image: url('https://images.unsplash.com/photo-1508144753681-9986d4df99b3?q=80&w=2070&auto=format&fit=crop&ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D');
  background-size: cover;
  background-position: center;
}



table {
    background: rgba(255, 255, 255, 0.15);
    border-radius: 15px;
    padding: 20px;
    width: 100%;
    backdrop-filter: blur(5px);
    -webkit-backdrop-filter: blur(10px);
    box-shadow: 0 4px 30px rgba(0, 0, 0, 0.1);
    border: 1px solid rgba(255, 255, 255, 0.3);
} */
       </style>

      <!-- Content Wrapper. Contains page content -->
      <div class="content-wrapper">
                <section class="content">
                  <div class="container-fluid">
                    <div class="row">
                      <div class="col-12">
                        <div class="card content-card mt-2" style="background: linear-gradient(to right, #e1f3ffff, #ffffff); border-radius: 12px; box-shadow: 0 4px 8px rgba(0,0,0,0.1); padding: 20px;">
                          <div class="card-header text-center">
                            <?php
                             $utype = $this->Menu_model->get_userbyid($task_user_id);
                             $task_user_name = $utype[0]->name;
                             
                             if($task_type == 'total_task'){
                                $filter = 'SELF OR OTHER FUNNEL';
                            }else if($task_type == 'self_funnel'){
                                $filter = 'SELF FUNNEL';
                            }else if($task_type == 'other_funnel'){
                                $filter = 'OTHER FUNNEL';
                            }else{
                                $filter = '';
                            }
                             
                             ?>
                            <h3 class="text-center m-3" style="color: #f7008d !important;">WORK ON <?=$filter;?></h3>
                            <span class="text-success"><strong><?=$task_user_name;?></strong></span><br>
                            <span class="text-success"><strong><?=$start_date.' - '.$end_date;?></strong></span>
                          </div>
                          <div class="card-body glass-card">
                            <div class="body-content">
                              <div class="page-header">
                                <div class="form-group">
                                        <div class="table-responsive text-nowrap">
                                            <table id="example1" class="table table-striped table-bordered" cellspacing="0" width="100%">
                                              <thead class="thead-dark">
                                                <tr>
                                                  <th>S.No</th>
                                                  <th>Task Perform BY</th>
                                                  <th>Main BD Name</th>
                                                  <th>Comppany Name</th>
                                                  <th>CID</th>
                                                  <th>Task Name</th>
                                                  <th>Action Taken</th>
                                                  <th>Purpose Achieved </th>
                                                  <th>Task Date</th>
                                                  <th>Remarks</th>
                                                  <th>Task Time Company Status</th>
                                                  <th>Task Time New Status</th>
                                                  <th>Company Current Status</th>
                                                  <th>Company Last Status</th>
                                                  <th>Task Status</th>
                                                </tr>
                                              </thead>
                                              <tbody>
                                                <?php
                                                $i=1;
                                                foreach($selfandotherworkData as $taskData){
                                                    ?>
                                                <tr>
                                                  <td><?=$i?></td>
                                                  <td><?=$taskData->task_perform_by_name;?></td>
                                                  <td><?=$taskData->mainbd_name;?></td>
                                                  <td> <a href="<?= base_url().'Menu/CompanyDetails/'.$taskData->cid;?>" target="_blank"><?=$taskData->compname;?></a> </td>
                                                  <td><a href="<?= base_url().'Menu/CompanyDetails/'.$taskData->cid;?>" target="_blank"><?=$taskData->cid;?></a> </td>
                                                  <td><?=$taskData->action_name;?></td>
                                                  <td><?=$taskData->action_taken;?></td>
                                                  <td><?=$taskData->purpose_taken;?></td>
                                                  <td><?=$taskData->appointmentdatetime;?></td>
                                                  <td><?=$taskData->remarks;?></td>

                                                  <td><?=$taskData->task_time_status;?></td>
                                                  <td><?=$taskData->task_time_new_status;?></td>
                                                  <td><?=$taskData->company_current_status;?></td>
                                                  <td><?=$taskData->company_last_status;?></td>
                                                  <td><?php 
                                                  if($taskData->nextCFID == 0){
                                                    echo "<span class='bg-warning p-1'>Pending</span>";
                                                  }else{
                                                    echo "<span class='bg-success p-1'>Done</span>";
                                                  }
                                                  ?>
                                                  </td>
                                                 
                                                </tr>
                                                <?php $i++;} ?>
                                              </tbody>
                                            </table>
                                    </div>
                                    <hr />
                                  </div>
                                </div></div></div></div>
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
                  "buttons": ["csv", "excel", "pdf", "print"]
                  }).buttons().container().appendTo('#example1_wrapper .col-md-6:eq(0)');
                  </script>
                </body>
              </html>
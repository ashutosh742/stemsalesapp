<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Todays Old / Plan But Not initiated Pending Task Planner Request | STEM APP| WebAPP</title>
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
    <style>
      .ApprovedStatus{
      padding:6px 10px;
      border-radius:4px;
      }
      .ApprovedStatus.Pending{
      background:orange;
      color:white;
      }
      .ApprovedStatus.Reject{
      background:red;
      color:white;
      }
      .ApprovedStatus.approved{
      background:green;
      color:white;
      }
      .colapsboxsha{
      border-radius:22px;
      box-shadow: rgba(0, 0, 0, 0.25) 0px 0.0625em 0.0625em, rgba(0, 0, 0, 0.25) 0px 0.125em 0.5em, rgba(255, 255, 255, 0.1) 0px 0px 0px 1px inset;
      }
      .modal.fade.show{
      background: rgba(100, 200, 150, 0.4);
      }
      #plandate{
      width:300px;
      }
      .setpaldate{
      display:flex;
      }
      tr,td{
        font-weight: 600;
      }
      form#add_target_form {
    background: beige;
    padding: 20px;
}
    </style>
  </head>
  <body class="hold-transition sidebar-mini layout-fixed">
    <div class="wrapper">
      <!-- Preloader -->
      <!-- Navbar -->
      <?php 
      $this->load->view($dep_name.'/nav.php');
      ?>
      <!-- /.navbar -->
      <!-- Content Wrapper. Contains page content -->
      <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <div class="content-header">
          <div class="container-fluid">
            <div class="row mb-2">
              <div class="col-sm-6">
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
        <section class="content">
          <div class="container-fluid">
            <?php //dd($getreqData); ?>
            <div class="row p-3">
              <div class="col-sm col-md-12 col-lg-12 m-auto">
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
                <div class="card card-primary card-outline">
                  <div class="card-body box-profile">
                    
                    <div class="text-center p-2 text-white" style="background-color: #ff0076 !important;">
                      <h4><i>Todays Pending Meetings Task</i></h4>
                      <h6><?=$tdate;?></h6>
                    </div>
                    <hr>
              
 
                    <div class="table-responsive text-nowrap">
                      <table id="exampledata" class="table table-striped table-bordered" cellspacing="0" width="100%">
                        <thead class="thead-dark">
                          <tr>
                            <th scope="col">#</th>
                            <th scope="col">Task User Name</th>
                            <th scope="col">CID</th>
                            <th scope="col">Comapny Name</th>
                            <th scope="col">Task Name</th>
                            <th class="text-capitalize">original Task Date</th>
                            <th scope="col">Appointment Datetime</th>
                            <th scope="col">Task Time Status</th>
                            <th scope="col">Current Status</th>
                            <th scope="col">Task Complete Status</th>
                            <th scope="col">Approve status</th>
                            <th scope="col">Approve By</th>
                            <th class="text-capitalize">Task RePlanned Count</th>
                            <th class="text-capitalize">Delete Status</th>
                            <th class="text-capitalize">Delete Remarks</th>
                           
                          </tr>
                        </thead>
                        <tbody>
                          <?php
                            $j =1;
                            foreach($totalTasksUserByDatas as $pendatareg){
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
                            <th><?= $j ?></th>
                            <td class="text-capitalize"><?= $pendatareg->task_bd_name ?></td>
                            <td><a style="color:<?=$backgroundColorNew;?>!important" target="_BLANK" href="<?=base_url();?>/Menu/CompanyDetails/<?=$pendatareg->cid?>"><?= htmlspecialchars($pendatareg->cid) ?></a></td>
                            <td class="text-capitalize"><a style="color:<?=$backgroundColorNew;?>!important" target="_BLANK" href="<?=base_url();?>/Menu/CompanyDetails/<?=$pendatareg->cid?>"><?= htmlspecialchars($pendatareg->company_name) ?></a></td>
                            <td><?= $pendatareg->task_name ?></td>
                            <td><?= $pendatareg->fwd_date ?></td>
                            <td><?= $pendatareg->appointmentdatetime ?></td>
                            <td><?= $pendatareg->task_time_status ?></td>
                            <td><?= $pendatareg->current_status ?></td>
                            <td><?php 
                            if($pendatareg->nextCFID > 0){
                                echo "<span class='bg-success p-1'>Complete</span>";
                            }else if($pendatareg->nextCFID == 0){
                                echo "<span class='bg-warning p-1'>Pending&nbsp;For&nbsp;Complete</span>";
                            }
                            ?></td>
                            <td><?php 
                            if($pendatareg->approved_status == 1){
                                echo "<span class='bg-success p-1'>Approved</span>";
                            }else if($pendatareg->approved_status == ''){
                                echo "<span class='bg-warning p-1'>Pending&nbsp;For&nbsp;Approved</span>";
                            }
                            ?></td>
                            <td><?php 
                            
                            if(!empty($pendatareg->task_approved_by) || !is_null($pendatareg->task_approved_by)){
                               echo $pendatareg->task_approved_by;
                            }else if(empty($pendatareg->task_approved_by) || is_null($pendatareg->task_approved_by)){
                                echo "<span class='bg-warning p-1'>Pending</span>";
                            }else{
                                 echo "<span class='bg-warning p-1'>Pending</span>";
                            }
                            ?></td>
                             <td> 
                            <?php if($pendatareg->task_plan_count > 0){
                                $alertmessage = 'bg-danger';
                            }else{
                                $alertmessage = '';
                            } ?>
                            <a class="<?=$alertmessage?> p-1" href="<?=base_url();?>Menu/ReplanedLog/<?=$pendatareg->task_id;  ?>" target="_BLANK"><?= htmlspecialchars($pendatareg->task_plan_count); ?></a>
                        </td>

                         <td><?php 
                            if($pendatareg->delete_request > 0){
                                echo "<span class='bg-success p-1'>Complete</span>";
                            }
                            ?></td>
                         <td><?php 
                            if($pendatareg->delete_request > 0){
                                echo $pendatareg->delete_remarks;
                            }
                            ?></td>


                          </tr>
                          <?php $j++; } ?>
                        </tbody>
                      </table>
                    </div>
                   <hr>
         
                  </div>
                </div>
              </div>
            </div>
          </div>

<hr>
 
 <br>
 <br>
 <br>

        </section>
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
        <script type='text/javascript'>
        
        </script>
        <!-- /.row (main row) -->
      </div>
    </div>
    <!-- /.content-wrapper -->
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
      $("#exampledata").DataTable({
      "responsive": false, "lengthChange": false, "autoWidth": false,
      "buttons": ["copy", "csv", "excel", "pdf", "print"]
      }).buttons().container().appendTo('#exampledata_wrapper .col-md-6:eq(0)');
      
    </script>
  </body>
</html>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>BD Request Details | STEM APP | WebAPP</title>
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
      tr,td{
      font-weight: bold;
      }
      .card-header{
      background: floralwhite;
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

                <?php if ($this->session->flashdata('error_message')): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <strong> <?php echo $this->session->flashdata('error_message'); ?></strong>
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
                </button>
                </div>
                <?php endif; ?>
                <?php if ($this->session->flashdata('errors_message')): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <strong> <?php echo $this->session->flashdata('errors_message'); ?></strong>
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
                </button>
                </div>
                <?php endif; ?>
            <div class="row">
              <div class="col-12">
                <div class="card">
                  <div class="card-header" style="background: linear-gradient(to right, #ede7f6, #ffffff); border-radius: 12px; box-shadow: 0 4px 8px rgba(0,0,0,0.1); padding: 20px;">
                    <h3 class="text-center m-3">🏷️ All BD Request Details </h3>
                    <hr style="width:25%;">
         
                <div class="table-responsive text-nowrap">
                  <table id="example1" class="table table-striped table-bordered" cellspacing="0" width="100%">
                    <thead class="thead-dark">
                        <tr>
                            <th>🔢 S No.</th>
                            <th>🏷️ Type of Request</th>
                            <th>📊 Total Request</th>

                            <th>📊 Total Pending For Approved BY Line Manager</th>
                            <th>📊 Total Approved BY Line Manager</th>
                            <th>📊 Total Rejected BY Line Manager</th>

                            <th>📊 Total Pending For Approved BY Admin</th>
                            <th>📊 Total Approved BY Admin</th>
                            <th>📊 Total Rejected BY Admin</th>

                            <th>⏳ Assign Pending</th>
                            <th>🟢 Open</th>
                            <th>✅ Close</th>
                            </tr>
                    </thead>
                    <tbody>
                     
                     <?php $i=1; foreach($allBDRequestDatas as $key => $allBDRequestData){
                        $r = rand(150, 255);
                        $g = rand(150, 255);
                        $b = rand(150, 255);
                        $backgroundColor = "rgba($r, $g, $b,.2)";
                        $hue        = rand(0, 360); // Full color wheel
                        $saturation = rand(60, 100); // High saturation for rich colors
                        $lightness  = rand(30, 45); // Low lightness for a deeper tone
                        $backgroundColorNew = "hsl($hue, $saturation%, $lightness%)";

                        ?>
                    <tr style="background-color: <?php echo $backgroundColor; ?>;color:<?=$backgroundColorNew;?>!important">
                        <td><?=$i?></td>
                        <td><?=$allBDRequestData->request_type?></td>
                        <td><a href="<?=base_url();?>Menu/BDRequestListDetails/<?=$uid?>/<?=$allBDRequestData->rysn?>/total_request"><?=$allBDRequestData->total_request?></a></td>

                        <td><a href="<?=base_url();?>Menu/BDRequestListDetails/<?=$uid?>/<?=$allBDRequestData->rysn?>/approve_status_pending"><?=$allBDRequestData->approve_status_pending?></a></td>
                        <td><a href="<?=base_url();?>Menu/BDRequestListDetails/<?=$uid?>/<?=$allBDRequestData->rysn?>/approve_status_approved"><?=$allBDRequestData->approve_status_approved?></a></td>
                        <td><a href="<?=base_url();?>Menu/BDRequestListDetails/<?=$uid?>/<?=$allBDRequestData->rysn?>/approve_status_rejected"><?=$allBDRequestData->approve_status_rejected?></a></td>

                        <td><a href="<?=base_url();?>Menu/BDRequestListDetails/<?=$uid?>/<?=$allBDRequestData->rysn?>/approve_admin_status_pending"><?=$allBDRequestData->approve_admin_status_pending?></a></td>
                        <td><a href="<?=base_url();?>Menu/BDRequestListDetails/<?=$uid?>/<?=$allBDRequestData->rysn?>/approve_admin_status_approved"><?=$allBDRequestData->approve_admin_status_approved?></a></td>
                        <td><a href="<?=base_url();?>Menu/BDRequestListDetails/<?=$uid?>/<?=$allBDRequestData->rysn?>/approve_admin_status_rejected"><?=$allBDRequestData->approve_admin_status_rejected?></a></td>

                        <td><a href="<?=base_url();?>Menu/BDRequestListDetails/<?=$uid?>/<?=$allBDRequestData->rysn?>/pending"><?=$allBDRequestData->pending?></a></td>
                        <td><a href="<?=base_url();?>Menu/BDRequestListDetails/<?=$uid?>/<?=$allBDRequestData->rysn?>/open"><?=$allBDRequestData->open?></a></td>
                        <td><a href="<?=base_url();?>Menu/BDRequestListDetails/<?=$uid?>/<?=$allBDRequestData->rysn?>/closed"><?=$allBDRequestData->closed?></a></td>
                    </tr>
                     <?php } ?>   
                    </tbody>
                  </table>
                </div>

                   </div>
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
    <!-- /.container-fluid -->
    </section>

    </div>    
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
    
    <footer class="main-footer">
      <strong>Copyright &copy; 2021-<?=date("Y")?> <a href="<?=base_url();?>">Stemlearning</a>.</strong>
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
    </script>
  </body>
</html>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
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
      tr,td{
        font-weight: 500;
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

        <?php     // dd($pendingForWriteMomMeetingLists); ?>

        <!-- Main content -->
        <section class="content">
          <div class="container-fluid">
  
             <div class="row">
                   <div class="col-12">
            <?php if ($this->session->flashdata('error_message')) { ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <strong><?php echo $this->session->flashdata('error_message'); ?></strong>
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
            </div>
            <?php } ?>
            <?php if ($this->session->flashdata('success_message')) { ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
            <strong><?php echo $this->session->flashdata('success_message'); ?></strong>
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
            </div>
            <?php } ?>
            </div>
             </div>
            <div class="row">
              <div class="col-12">
                <div class="card">
                  <div class="card-header">
                    <h3 class="text-center m-3 bg-primary p-2">MoM Writing Pending for RP Meetings</h3>
                  </div>
                  <!-- /.card-header -->
                  <div class="card-body">
                    <div class="container-fluid body-content">
                      <div class="page-header">
                        <div class="form-group">
                          <fieldset>
                            <div class="table-responsive">
                              <div class="table-responsive text-nowrap">
                                <table id="example1" class="table table-striped table-bordered" cellspacing="0" width="100%">
                                  <thead class="thead-dark">
                                    <tr>
                                      <th>S.No</th>
                                      <th>Task ID</th>
                                      <th>BD Name</th>
                                      <th>CID</th>
                                      <th>Company Name</th>
                                      
                                      <th>Meetings Date</th>
                                      <th>Meetings Type</th>
                                      <th>Task By User</th>
                                      <th>Task Name</th>
                                      <th>Task Approved By</th>
                                      <!-- <th>Task Planned Time Status</th>
                                      <th>Task Completed Time New Status</th> -->
                                      <th>Current Status</th>
                                      <th>Mom</th>
                                      <th>View Meetings Details</th>
                                      <?php if($user_tupe_id == 3 || $user_tupe_id == 13 || $user_tupe_id == 4 || $user_tupe_id == 24){?> 
                                         <th>Update Leads</th>
                                        <?php  } ?>
                                    </tr>
                                  </thead>
                                  <tbody>
                                    <?php 
                                      $i=1;
                                      foreach($pendingForWriteMomMeetingLists as $dt){
                                        
                                        $r = rand(150, 255);
                                        $g = rand(150, 255);
                                        $b = rand(150, 255);
                                        $backgroundColor = "rgba($r, $g, $b,.2)";

                                        $hue = rand(0, 360); // Full color wheel
                                        $saturation = rand(60, 100); // High saturation for rich colors
                                        $lightness = rand(30, 45); // Low lightness for a deeper tone
                                        
                                        $backgroundColorNew = "hsl($hue, $saturation%, $lightness%)";

                                        $meeting_id = $dt->meeting_id;

                                        ?>
                                    <tr style="background-color: <?php echo $backgroundColor; ?>;color:<?=$backgroundColorNew;?>!important">
                                      <td><?=$i?></td>
                                      <td><?=$dt->tbl_task_id;?></td>
                                      <td><?=$dt->user_name?></td>
                                      <td><a style="color:<?=$backgroundColorNew;?>!important" href="<?= base_url().'Menu/CompanyDetails/'.$dt->cid ?>"><?= $dt->cid ?></a></td>
                                      <td><a style="color:<?=$backgroundColorNew;?>!important" href="<?= base_url().'Menu/CompanyDetails/'.$dt->cid ?>"><?=$dt->company_name?></a></td>
                                      <td><?=$dt->storedt?></td>
                                      <td><?=$dt->mtype?></td>
                                      <td><?=$dt->task_user_name?></td>
                                      <td><?=$dt->task_name?></td>
                                      <td><?=$dt->task_approved_by?></td>
                                      <td><?=$dt->current_status?></td>
                                    
                                      <td><?php 
                                      if(is_null($dt->write_mom_id)){
                                        echo '<span class="bg-warning p-2">Pending</span>';
                                      }
                                      ?></td>

                                        <td><a class="bg-primary p-1 text-white" style="color:#fff!important" href="<?= base_url().'Menu/ViewMeetingDetails/'.$dt->task_id ?>">View</td>

                                        <?php if($user_tupe_id == 3 || $user_tupe_id == 13 || $user_tupe_id == 4 || $user_tupe_id == 24){?> 
                                        <td><a class="bg-primary p-1" href="<?= base_url().'Menu/BMNewLead/'.$meeting_id;?>">Update Lead</a></td>
                                        <?php } ?>
                                    </tr>
                                    <?php $i++;} ?>
                                  </tbody>
                                </table>
                              </div>
                            </div>
                            <!--END OF FORM ^^-->
                          </fieldset>
                        </div>
                        <hr />
                      </div>
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
      
        "buttons": ["copy", "csv", "excel", "pdf", "print", "colvis"]
      
      }).buttons().container().appendTo('#example1_wrapper .col-md-6:eq(0)');
      
    </script>
  </body>
</html>
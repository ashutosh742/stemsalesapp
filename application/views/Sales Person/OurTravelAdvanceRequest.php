<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="wi$dth=device-wi$dth, initial-scale=1">
    <title>Our Travel Advance Request  | STEM APP | WebAPP</title>
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
      .commentbox{
    background: #28a745;
    padding: 2px;
}
.modal-content {
        box-shadow: 0 .5rem 1rem rgba(0, 0, 0, .5);
        box-shadow: rgba(0, 0, 0, 0.17) 0px -23px 25px 0px inset, rgba(0, 0, 0, 0.15) 0px -36px 30px 0px inset, rgba(0, 0, 0, 0.1) 0px -79px 40px 0px inset, rgba(0, 0, 0, 0.06) 0px 2px 1px, rgba(0, 0, 0, 0.09) 0px 4px 2px, rgba(0, 0, 0, 0.09) 0px 8px 4px, rgba(0, 0, 0, 0.09) 0px 16px 8px, rgba(0, 0, 0, 0.09) 0px 32px 16px;
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
<?php 
 date_default_timezone_set("Asia/Calcutta"); 

?>
        <section class="content">
          <div class="container-fluid">
            <div class="row">
              <div class="col-12">
                <div class="card">
                  <div class="card-header bg-success">
                    <h3 class="text-center">
                      <center><b>Our Travel Advance Request</b></center>
                    </h3>
                  </div>
                  <!-- /.card-header -->
                  <div class="card-body">
                    <div class="body-content">
                      <div class="page-header">
                        <fieldset>
                          <div class="table-responsive">
                            <div class="table-responsive">
                              <div class="pdf-viwer">
                                <table id="example1" class="table table-striped table-bordered" cellspacing="0" width="100%">
                                  <thead class="thead-dark">
                                  <tr>
                                      <th>S.No.</th>
                                      <th>Name</th>
                                      <th>Ammount</th>
                                      <th>Purpose</th>
                                      <th>Request Date</th>
                                      <th>Manager Approved</th>
                                      <th>Manager Approved Name</th>
                                      <th>Manager Remarks</th>
                                      <th>Admin Approved</th>
                                      <th>Account Approved</th>
                                      <th>View Details</th>

                                    </tr>
                                  </thead>
                                  <tbody>
                                    <?php 
                                      $i=1; foreach($ocashreq as $cash){   ?>
                                    <tr>
                                    <td><?=$i?></td>
                                    <td><?= $cash->name;?></td>
                                    <td><?= $cash->cash;?> ₹</td>
                                    <td><?= $cash->purpose;?></td>
                                    <td><?= $cash->date;?></td>
                                    <td>
                                    <?php 
                                    if($cash->cluster_apr == 0){
                                        $cluster_apr =  "<span class='bg-warning p-1'>Pending</span>";
                                    }else if($cash->cluster_apr == 1){
                                      $cluster_apr =  "<span class='bg-success p-1'>Approved</span>";
                                    }else if($cash->cluster_apr == 2){
                                      $cluster_apr =  "<span class='bg-danger p-1'>Rejected</span>";
                                    }
                                   echo $cluster_apr;
                                    ?>
                                    </td>
                                    <td>
                                        <?php 
                                        if($cash->cluster_by != 0){echo $this->Menu_model->get_userbyid($cash->cluster_by)[0]->name; }else{echo "<span class='bg-warning p-1'>Pending</span>";}
                                        ?>
                                    </td>
                                    <td>
                                        <?php 
                                        if($cash->cluster_msg !==''){echo $cash->cluster_msg;}else{echo "<span class='bg-warning p-1'>Pending</span>";}
                                        ?>
                                    </td>
                                    <td>
                                    <?php 
                                    if($cash->admin_apr == 0){
                                        $admin_apr =  "<span class='bg-warning p-1'>Pending</span>";
                                    }else if($cash->admin_apr == 1){
                                      $admin_apr =  "<span class='bg-success p-1'>Approved</span>";
                                    }else if($cash->admin_apr == 2){
                                      $admin_apr =  "<span class='bg-danger p-1'>Rejected</span>";
                                    }else if($cash->admin_apr == 3){
                                       $admin_apr =  "<span class='bg-warning p-1'>Suspected</span>";
                                    }else if($cash->admin_apr == 3){
                                       $admin_apr =  "<span class='bg-danger p-1'>Suspected</span>";
                                    }
                                   echo $admin_apr;
                                    ?>
                                    </td>
                                    <td>
                                    <?php 
                                    if($cash->account_apr == 0){
                                        $account_apr =  "<span class='bg-warning p-1'>Pending</span>";
                                    }else if($cash->account_apr == 1){
                                      $account_apr =  "<span class='bg-success p-1'>Approved</span>";
                                    }else if($cash->account_apr == 2){
                                      $account_apr =  "<span class='bg-danger p-1'>Rejected</span>";
                                    }else if($cash->account_apr == 3){
                                      $account_apr =  "<span class='bg-danger p-1'>Suspected</span>";
                                    }else if($cash->account_apr == 3){
                                      $account_apr =  "<span class='bg-danger p-1'>Suspected</span>";
                                    }
                                   echo $account_apr;
                                    ?>
                                    </td>
                                    <td>
                      <p><a href="<?= base_url().'Menu/TravelCashAdvancedDetails/'.$cash->id; ?>">View</a></p>
                    </td>
                                    <?php $i++;} ?>
                                  </tbody>
                                </table>
                              </div>
                            </div>
                            </form>            <!--END OF FORM ^^-->
                        </fieldset>
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
    <script src="https://cdn.tiny.cloud/1/qagffr3pkuv17a8on1afax661irst1hbr4e6tbv888sz91jc/tinymce/6/tinymce.min.js"></script>
    <script>
      $("#example1").DataTable({
        "responsive": false, "lengthChange": false, "autoWidth": false,
        "buttons": ["csv", "excel"]
      }).buttons().container().appendTo('#example1_wrapper .col-md-6:eq(0)');

</script>



  </body>
</html>
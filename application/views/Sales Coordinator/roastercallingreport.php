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
          </div><!-- /.col -->
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <h4></h4>
            </ol>
          </div><!-- /.col -->
        </div><!-- /.row -->
      </div><!-- /.container-fluid -->
    </div>
    <!-- /.content-header -->


<?php
$sd = $sdate->format('Y-m-d'); 
$ed = $edate->format('Y-m-d'); 
?>
<div class="container-fluid p-3">
    <form action="<?=base_url();?>Menu/roastercallingreport" method="POST" >
    <input class="ml-2" type="date" name="sdate" value="<?=$sd?>">
    <input class="ml-2" type="date" name="edate" value="<?=$ed?>">
    <input type="submit">
    </form>
</div>

    <!-- Main content -->
    <section class="content">
      <div class="container-fluid">
        <div class="row">
          <div class="col-12">
    <div class="card">
              <div class="card-header">
                <h3 class="text-center m-3 text-secondary">Roster Calling Detail</h3>
              </div>
              <!-- /.card-header -->
              <div class="card-body">
                  <div class="container body-content">
        <div class="page-header">
            <div class="form-group">
                <fieldset>
                    <form action="" class="form-group" method="post">
                        <div class="table-responsive">
                            <div class="table-responsive">     
                                <table id="example1" class="table table-striped table-bordered" cellspacing="0" width="100%">
                                    <thead>
                                        <tr>
                                            <th>S.No.</th>
                                            <th>BD Name</th>
                                            <th>Total Reachout</th>
                                            <th>topspender Yes</th>
                                            <th>topspender No</th>
                                            <th>Reachout by Call</th>
                                            <th>Reachout by Email</th>
                                            <th>Reachout by Meet</th>
                                            <th>Reachout by Barg in Meeting</th>
                                            <th>Reachout by Whatsapp Activity</th>
                                            <th>Corporate</th>
                                            <th>PSU</th>
                                            <th>NGO</th>
                                            <th>Private School</th>
                                            <th>Individual</th>
                                            <th>Club</th>
                                            <th>Govt Off</th>
                                            <th>Other</th>
                                            <th>Online</th>
                                            <th>STEM School</th>
                                            <th>Foundation</th>
                                            <th>MNC</th>
                                            <th>HO Leads</th>
                                            <th>FOCUSED</th>
                                            <th>DMF</th>
                                            <th>Last weeks meetings planned and completed</th>	
                                            <th>Current weeks planning</th>	
                                            <th>Broadcast List and No. added</th>	
                                            <th>Proposal Shared</th>	
                                            <th>Support for successful client conversion</th>	
                                            <th>RP Meeting Calling status ( WDL, NI, Wrong Number, not responding)</th>	
                                            <th>Next WeekTravel Plan</th>	
                                            <th>Positive Clients update</th>	
                                            <th>Expected closure for that week</th>	
                                            <th>Meeting Preparedness (Research, Documents, Presentation,Pitching, different product pitch)</th>	
                                        </tr>
                                    </thead>
                                    <tbody>
                                      <?php 
                                        $i=1;
                                        foreach($alluser as $au){if($au->type_id =='3' && $au->status == 'active'){
                                            $user=$au->user_id;
                                            $md=$this->Menu_model->get_rcreport($sd,$ed,$user);
                                        ?>
                                    <tr>
                                         <td><?=$i?></td>
                                         <td><?=$au->name?></td>
                                         <td><?=$md[0]->a?></td>
                                         <td><?=$md[0]->g?></td>
                                         <td><?=$md[0]->h?></td>
                                         <td><?=$md[0]->b?></td>
                                         <td><?=$md[0]->c?></td>
                                         <td><?=$md[0]->d?></td>
                                         <td><?=$md[0]->e?></td>
                                         <td><?=$md[0]->f?></td>
                                         <td><?=$md[0]->i?></td>
                                         <td><?=$md[0]->j?></td>
                                         <td><?=$md[0]->k?></td>
                                         <td><?=$md[0]->l?></td>
                                         <td><?=$md[0]->m?></td>
                                         <td><?=$md[0]->n?></td>
                                         <td><?=$md[0]->o?></td>
                                         <td><?=$md[0]->p?></td>
                                         <td><?=$md[0]->q?></td>
                                         <td><?=$md[0]->r?></td>
                                         <td><?=$md[0]->s?></td>
                                         <td><?=$md[0]->t?></td>
                                         <td><?=$md[0]->u?></td>
                                         <td><?=$md[0]->v?></td>
                                         <td><?=$md[0]->w?></td>
                                         <td></td>
                                         <td></td>
                                         <td></td>
                                         <td></td>
                                         <td></td>
                                         <td></td>
                                         <td></td>
                                         <td></td>
                                         <td></td>
                                         <td></td>
                                     </tr>
                                     <?php $i++;}} ?>
                                  </tbody>
                                </table>
                            </div>
                        </div>
                    </form>            <!--END OF FORM ^^-->
                </fieldset>
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
      "responsive": false, "lengthChange": false, "autoWi$dth": false,
      "buttons": ["copy", "csv", "excel", "pdf", "print", "colvis"]
    }).buttons().container().appen$dto('#example1_wrapper .col-md-6:eq(0)');
</script>
</body>
</html>
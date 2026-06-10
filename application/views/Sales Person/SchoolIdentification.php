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
            <h1 class="m-0">School Identification</h1>
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

    <!-- Main content -->
    <section class="content">
      <div class="container-fluid">
        <div class="row">
          <div class="col-12">
    <div class="card">
              <div class="card-header">
              </div>
              <!-- /.card-header -->
              <div class="card-body text-center">
                  <div class="container body-content">
                    <div class="row">
                        
                        <?php
                            $request = $this->Menu_model->get_bdrbyid($tid);
                            $bdtask = $this->Menu_model->get_bdtaskbybdrid($tid);
                            $visit = $this->Menu_model->get_visitsbybdrid($tid);
                            $visitc = sizeof($visit);
                            $call = $this->Menu_model->get_callsbybdrid($tid);
                            $callc = sizeof($call);
                            $research = $this->Menu_model->get_researchbybdrid($tid);
                            $researchc = sizeof($research);
                        ?>
                        
                        <div class="col-sm-12 col-md-4 col-lg-4 mb-4">
                          <div class="card p-3 border rounded border-success hover-div d-flex flex-column align-items-stretch h-100">
                            <a href="<?=base_url();?>Menu/BDCreatedRequest/<?=$tid?>">
                                <b>BD Created Request</b><hr>
                                Requested by<br><b><?=$request[0]->bd_name?></b><hr>
                                Requested Date<br><b><?=$request[0]->sdatet?></b>
                            </a>
                          </div>
                        </div>
                        <div class="col-sm-12 col-md-4 col-lg-4 mb-4">
                          <div class="card p-3 border rounded border-success hover-div d-flex flex-column align-items-stretch h-100">
                            <a href="<?=base_url();?>Menu/BDRAssignPIA/<?=$tid?>">  
                                <b>Assign PIA</b><hr>
                                Assign by<br><b><?=$bdtask[0]->assignby?></b><hr>
                                Assign Date<br><b><?=$bdtask[0]->asdate?></b><hr>
                                Assign to<br><b><?=$bdtask[0]->pianame?></b>
                            </a>
                          </div>
                        </div>
                        <div class="col-sm-12 col-md-4 col-lg-4 mb-4">
                          <div class="card p-3 border rounded border-success hover-div d-flex flex-column align-items-stretch h-100">
                            <a href="<?=base_url();?>Menu/BDRAssignPIA/<?=$tid?>">  
                                <b>Join Call with PIA</b><hr>
                            </a>
                          </div>
                        </div>
                        <div class="col-sm-12 col-md-4 col-lg-4 mb-4">
                          <div class="card p-3 border rounded border-success hover-div d-flex flex-column align-items-stretch h-100">
                            <a href="<?=base_url();?>Menu/BDRResearchSchool/<?=$tid?>">
                               <b>School Research</b><hr>
                               Researched School<br><b><?=$researchc?></b><hr>
                               Target Date<br><b>Not Data</b>
                           </a>
                          </div>
                        </div>
                        <div class="col-sm-12 col-md-4 col-lg-4 mb-4">
                          <div class="card p-3 border rounded border-success hover-div d-flex flex-column align-items-stretch h-100">
                           <a href="<?=base_url();?>Menu/BDRResearchDM/<?=$tid?>">
                               <b>DEO/DM Research</b><hr>
                               Researched School<br><b><?=$callc?></b><hr>
                               Target Date<br><b>Not Data</b>
                           </a>
                          </div>
                        </div>
                        <div class="col-sm-12 col-md-4 col-lg-4 mb-4">
                          <div class="card p-3 border rounded border-success hover-div d-flex flex-column align-items-stretch h-100">
                           <a href="<?=base_url();?>Menu/BDRCallSchool/<?=$tid?>">
                               <b>Call School</b><hr>
                               Called School<br><b><?=$callc?></b><hr>
                               Target Date<br><b>Not Data</b>
                           </a>
                          </div>
                        </div>
                        
                        <div class="col-sm-12 col-md-4 col-lg-4 mb-4">
                          <div class="card p-3 border rounded border-success hover-div d-flex flex-column align-items-stretch h-100">
                           <a href="<?=base_url();?>Menu/BDRVisitSchool/<?=$tid?>">
                               <b>Visit School</b><hr>
                               Visited School<br><b><?=$visitc?></b><hr>
                               Last Visit Date<br><b><?=$bdtask[0]->assignby?></b><hr>
                               Assign to<br><b><?=$bdtask[0]->pianame?></b>
                           </a>
                          </div>
                        </div>
                        
                        <div class="col-sm-12 col-md-4 col-lg-4 mb-4">
                          <div class="card p-3 border rounded border-success hover-div d-flex flex-column align-items-stretch h-100">
                           <a href="<?=base_url();?>Menu/BDRVisitDM/<?=$tid?>">
                               <b>Visit DEO/DM</b><hr>
                               Visited DEO/DM<br><b><?=$visitc?></b><hr>
                               Last Visit Date<br><b><?=$bdtask[0]->assignby?></b><hr>
                               Target Date<br><b>Not Data</b>
                           </a>
                          </div>
                        </div>
                        
                        <div class="col-sm-12 col-md-4 col-lg-4 mb-4">
                          <div class="card p-3 border rounded border-success hover-div d-flex flex-column align-items-stretch h-100">
                           <a href="<?=base_url();?>Menu/BDRPZReview/<?=$tid?>">
                              <b>Review by PM/ZH</b><hr>
                               Visited DEO/DM<br><b><?=$visitc?></b><hr>
                               Last Visit Date<br><b><?=$bdtask[0]->assignby?></b><hr>
                               Target Date<br><b>Not Data</b>
                           </a>
                          </div>
                        </div>
                        
                        <div class="col-sm-12 col-md-4 col-lg-4 mb-4">
                          <div class="card p-3 border rounded border-success hover-div d-flex flex-column align-items-stretch h-100">
                           <a href="<?=base_url();?>Menu/BDRBDReview/<?=$tid?>">
                              <b>Review by BD </b><hr>
                               Visited DEO/DM<br><b><?=$visitc?></b><hr>
                               Last Visit Date<br><b><?=$bdtask[0]->assignby?></b><hr>
                               Target Date<br><b>Not Data</b><hr>
                               Target Date<br><b>Not Data</b>
                           </a>
                          </div>
                        </div>
                        
                        <div class="col-sm-12 col-md-4 col-lg-4 mb-4">
                          <div class="card p-3 border rounded border-success hover-div d-flex flex-column align-items-stretch h-100">
                           <a href="<?=base_url();?>Menu/BDRBDClose/<?=$tid?>">
                              <b>Close by BD </b><hr>
                               Visited DEO/DM<br><b><?=$visitc?></b><hr>
                               Last Visit Date<br><b><?=$bdtask[0]->assignby?></b><hr>
                               Target Date<br><b>Not Data</b>
                           </a>
                          </div>
                        </div>
                        
                        
                    </div>
                </div>
            </div>
        </div>      
    
    </div>
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
    
<style>

.hover-div:hover {
  box-shadow: 0 2px 2px;
}
</style>    
    

<script>
    const hoverDiv = document.querySelector('.hover-div');
    const additionalInfo = hoverDiv.querySelector('.additional-info');

    hoverDiv.addEventListener('mouseenter', () => {
      additionalInfo.style.display = 'block';
    });

    hoverDiv.addEventListener('mouseleave', () => {
      additionalInfo.style.display = 'none';
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
    $("#example1").DataTable({
      "responsive": false, "lengthChange": false, "autoWidth": false,
      "buttons": ["copy", "csv", "excel", "pdf", "print", "colvis"]
    }).buttons().container().appendTo('#example1_wrapper .col-md-6:eq(0)');
</script>
</body>
</html>
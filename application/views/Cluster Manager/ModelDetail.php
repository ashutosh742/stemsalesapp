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


    <!-- Main content -->
    <section class="content">
      <div class="container-fluid">
        <div class="row">
          <div class="col-12">
                <div class="card">
                  <div class="card-header">
                    <h3 class="text-center m-3 text-secondary">RID Detail</h3>
                  </div>
                          <div class="card-body p-3 text-center">
                                    <div class="row m-auto">
                                        <?php
                                        $sidrid = $this->Menu_model->get_sidrid($sid);
                                        $tidrid = $this->Menu_model->get_tidrid($tid);
                                        $sname = $sidrid[0]->sname;
                                        $projectcode = $sidrid[0]->projectcode;
                                        $code = $chid."-".$sid."-".$tid;
                                        $sdatet = $tidrid[0]->sdatet;
                                        $cdate = date('Y-m-d H:i:s');
                                        $ftime = $this->Menu_model->timediff($sdatet,$cdate);
                                        $txt = "Request ID : ".$code." | ".$projectcode." | ".$sname." | ".$tidrid[0]->fullname?>
                                        
                                        
                                        <div class="border card col-lg-12 col-sm bg-danger p-3"><?=$txt?></div>
                                        
                                        
                                        <?php  foreach($sidrid as $srid){
                                            $type = $srid->type;
                                            $model_name = $srid->model_name;
                                            $part_name = $srid->part_name;
                                            $remark = $srid->remark;
                                            $midelrid = $this->Menu_model->get_midelrid($projectcode,$model_name);
                                            
                                            ?>
                                        <div class="border card col-lg-3 col-sm bg-danger p-3 p-3">
                                            <?=$type?><hr><?=$model_name?><br><?=$part_name?><br><?=$remark?><hr><?=$sdatet?><br><?=$ftime?>
                                            <hr>
                                            <?php 
                                            $pat = "https://stemoppapp.in/".$srid->modelimg;
                                            
                                            $ext = pathinfo($pat, PATHINFO_EXTENSION); if($ext=='mp4'){?>
                                               <a href="<?=$pat?>" target="_blank">
                                                  <video class="embed-responsive-item img-thumbnail" src="<?=$pat?>" height="300" muted controls></video>
                                               </a>
                                               <?php }else{?>
                                               <a href="<?=$pat?>" target="_blank">
                                                  <img src="<?=$pat?>"  class="img-thumbnail" width="100%">
                                               </a>
                                            <?php }?>
                                            
                                        </div>
                                        <?php }?>
                                    </div>
                                    
                                    <div class="row">
                                        <div class="border card col-lg-3 col-sm bg-success p-3">
                                            Request Detail<hr>
                                            <b><?=$sdatet?></b><hr>
                                            <b><?=$tidrid[0]->fullname?></b><hr>
                                        </div>
                                        <div class="border card col-lg-3 col-sm bg-success p-3">
                                            RM Apr Detail<hr>
                                            <b><?=$sdatet?></b><hr>
                                            Ravindra Yadav<hr>
                                        </div>
                                        <div class="border card col-lg-3 col-sm bg-danger p-3">
                                            PM Apr Detail<hr>
                                            <b>Pending From <?=$ftime?></b></b><hr>
                                        </div>
                                        <div class="border card col-lg-3 col-sm p-3">
                                            FM Assing Detail<hr>
                                            <b>No Data</b><hr>
                                        </div>
                                        <div class="border card col-lg-3 col-sm p-3">
                                            Packing Detail<hr>
                                            <b>No Data</b><hr>
                                        </div>
                                        <div class="border card col-lg-3 col-sm p-3">
                                            Packing Check Detail<hr>
                                            <b>No Data</b><hr>
                                        </div>
                                        <div class="border card col-lg-3 col-sm p-3">
                                            Dispatch Detail<hr>
                                            <b>No Data</b><hr>
                                        </div>
                                        <div class="border card col-lg-3 col-sm p-3">
                                            Delivery Detail<hr>
                                            <b>No Data</b><hr>
                                        </div>
                                        <div class="border card col-lg-3 col-sm p-3">
                                            Receive Detail<hr>
                                            <b>No Data</b><hr>
                                        </div>
                                        <div class="border card col-lg-3 col-sm p-3">
                                            Replace/Repair Detail<hr>
                                            <b>No Data</b><hr>
                                        </div>
                                        <div class="border card col-lg-3 col-sm p-3">
                                            No Wrking Model Send to Factory Detail<hr>
                                            <b>No Data</b><hr>
                                        </div>
                                        <div class="border card col-lg-3 col-sm p-3">
                                            No Wrking Model Receive in Factory<hr>
                                            <b>No Data</b><hr>
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
      "buttons": ["copy", "csv", "excel", "pdf", "print", "colvis;
    }).buttons().container().appen$dto('#example1_wrapper .col-md-6:eq(0)');
</script>
</body>
</html>
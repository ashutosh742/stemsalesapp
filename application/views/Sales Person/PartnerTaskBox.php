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
          <div class="card col-12 p-3 text-center">
              <?php $ptn = $this->Menu_model->get_partnerbyid($pt);?>
              <center><h5>Completed <?=$ptn[0]->name?> Partner Type Task | Date : <?php echo $tdate="2023-08-08";?></h5></center><hr>
             <div class="row">
                 <?php
                 $mtdata = $this->Menu_model->new_ttptwdetail($pt,$uid,$tdate,1);
                 foreach($mtdata as $md){
                 $bd = $md->user_id;
                 $tid = $md->id;
                 $caid = $md->actiontype_id;
                 $inid  = $md->cid_id;
                    $bdname = $this->Menu_model->get_userbyid($bd);
                    $inid = $this->Menu_model->get_initbyid($inid);
                    $mtd = $this->Menu_model->get_ccitblall($tid);
                    $lsid = $mtd[0]->status_id;
                    $csid = $mtd[0]->nstatus_id;
                    $s1 = $this->Menu_model->get_statusbyid($lsid);
                    if($s1){$s1=$s1[0]->name;}else{$s1='';}
                    $s2 = $this->Menu_model->get_statusbyid($csid);
                    if($s2){$s2=$s2[0]->name;}else{$s2='';}
                     $action = $this->Menu_model->get_actionbyid($caid);
                     $pltime = $md->appointmentdatetime;
                     $cdate=date('Y-m-d H:i:s');
                     $timediff = $this->Menu_model->timediff($pltime,$cdate);
                 ?>
                 <div class="col-lg-3 col-md-6 col-sm p-3 <?=$action[0]->color?> rounded border">
                    BD Name<br><b><?=$bdname[0]->name?></b><hr>
                    Company Name<br><b><?=$mtd[0]->compname?></b><hr>
                    Partner Type<br><b><?=$ptn[0]->name?></b><hr>
                    Action Type<br><b><?=$action[0]->name?></b><hr>
                    Plan Time<br><b><?=date('d-m-Y h:i A', strtotime($pltime = $md->appointmentdatetime));?></b><hr>
                    Initiated Time<br><b><?=date('d-m-Y h:i A', strtotime($intime = $md->initiateddt));?></b><hr>
                    Plan and Initiated Time Diff<br><b><?=$this->Menu_model->timediff($pltime,$intime)?></b><hr>
                    Completed Time<br><b><?=date('d-m-Y h:i A', strtotime($uptime = $md->updateddate));?></b><hr>
                    Plan and Completed Time Diff<br><b><?=$this->Menu_model->timediff($pltime,$uptime)?></b><hr>
                    Initiated and Completed Time Diff<br><b><?=$this->Menu_model->timediff($intime,$uptime)?></b><hr>        
                    Current_Task_Remarks<br><b><?=$md->remarks?></b><hr>
                    Last Status<br><b><?=$s1?></b><hr>
                    Current Status<br><b><?=$s2?></b><hr>
                    Review Remark<br><b><?=$md->rremark?></b><hr>


                    
                    
                 </div>
                 <?php } ?>
              </div>
          </div>
      </div>
    </section>
   </div>
</div> 
    
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

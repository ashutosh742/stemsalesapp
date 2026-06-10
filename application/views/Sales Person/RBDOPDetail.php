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
            <h1 class="m-0">Open BD Request</h1>
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
          
            <div class="container-fluid card p-5" id="data-container">
                <div class="row text-center" id="grid-view">
                    
                    <?php $BDRequest = $this->Menu_model->get_BDRequestbyrid($rid);
                    foreach($BDRequest as $bdr){
                        
                        if($BDRequest[0]->rysn=='SSCHOOLID'){$cp=2;}
                        if($BDRequest[0]->rysn=='NCSV'){$cp=2;}
                        if($BDRequest[0]->rysn=='Inauguration'){$cp=2;}
                        if($BDRequest[0]->rysn=='Demo'){$cp=2;}
                    ?>
                        <div class="col-sm-12 col-md-4 col-lg-4 mb-4">
                          <div class="card p-3 border rounded border-success hover-div d-flex flex-column align-items-stretch h-100 text-dark">
                              Request Type<br><b><?=$bdr->request_type?></b><hr>
                              Client Type<br><b><?php if($bdr->onnew=='1'){echo 'New Client';}else{echo 'On Borded';}?></b><hr>
                              Client Name<br><b style="color:#808080"><?=$bdr->cname?></b><hr>
                              BD Name<br><b><?=$bdr->bd_name?></b><hr>
                              Request Date<br><b><?=$this->Menu_model->get_dformat($bdr->sdatet)?></b><hr>
                              Target Date<br><b><?=$this->Menu_model->get_dformat($bdr->targetd)?></b><hr>
                              Remark<br><b><?=$bdr->remark?></b><hr>
                              Other Detail<br><b>
                              <?php if($bdr->noofschool!=''){?>
                              No of School : <?=$bdr->noofschool?><br>
                              <?php }if($bdr->vlocation!=''){?>
                              Location : <?=$bdr->vlocation?><br>
                              <?php }if($bdr->ngoletter!=''){?>
                              NGO Letter : <?=$bdr->ngoletter?><br>
                              <?php }if($bdr->schooltype!=''){?>
                              School Type : <?=$bdr->schooltype?><br>
                              <?php }?>
                              </b><hr>
                                <div class="rounded-circle bg-danger" style="position: absolute;bottom: -10px; left: 40%; transform: translateX(-50%); width: 20px; height: 20px;"></div>
                                <div class="rounded-circle bg-danger" style="position: absolute;bottom: -10px; left: 60%; transform: translateX(-50%); width: 20px; height: 20px;"></div>
                          </div>
                        </div>
                       <?php } ?>
                
           
            <div class="col-sm-12 col-md-8 col-lg-8 mb-4">
                <?php $i=1; 
                     $hiprocess = $this->Menu_model->get_htiprocessbytid($cp);
                     foreach($hiprocess as $hip){
                        $mhtid = $hip->tid;
                        $exdate = $this->Menu_model->get_mtupbyrbdid($rid,$mhtid)
                     ?>
                      <div class="card col-sm-12 col-md-12 col-lg-12 mb-4 text-dark text-center">
                        <div class="card-header" id="heading<?=$i?>">
                          <h5 class="mb-0">
                            <button class="btn btn-link text-dark" data-toggle="collapse" data-target="#collapse<?=$i?>" aria-expanded="true" aria-controls="collapseOne">
                              <h5><?=$hip->tmane?> (<?php if($exdate){echo $this->Menu_model->get_odformat($exdate[0]->expacteddt);}?>)</h5>
                            </button>
                          </h5>
                        </div>
                    
                        <div>
                          <div class="card-body">
                              <div class="row">
                              <?php 
                                  $tid=$hip->tid; 
                                  $htimsprocess = $this->Menu_model->get_htimsprocess($tid);
                                  foreach($htimsprocess as $himsp){
                                  $stid = $himsp->tid;
                                  $mtdada = $this->Menu_model->get_mstubycntsid($rid,$tid,$stid);
                                  if($mtdada){
                                      $taskby = $mtdada[0]->tby;
                                      $taskdate = $mtdada[0]->tdate;
                                      if(is_numeric($taskby)==1){
                                          $bdname = $this->Menu_model->get_bdnamebyid($taskby);
                                          $taskby = $bdname[0]->name;
                                      }else{$taskby=$mtdada[0]->tby;}
                                  }
                                  
                                  if($himsp->slink!=''){$link = base_url().'Menu/'.$himsp->slink.'/'.$rid;}else{$link = '#';}
                                  
                              ?>
                                <div class="col-sm-12 col-md-4 col-lg-4 mb-4">
                                    <div class="card p-3 border rounded border-success hover-div d-flex flex-column align-items-stretch h-100 text-dark">
                                        <a href="<?=$link?>">
                                        <b><?=$himsp->tname?></b><hr>
                                        Perform By<br><b><?php if($taskby!=''){echo $taskby;}?></b><hr>
                                        Perform Date<br><b><?php if($taskdate!=''){echo $this->Menu_model->get_dformat($taskdate);}?></b><hr>
                                        </a>
                                        <div class="rounded-circle bg-danger" style="position: absolute;bottom: -10px; left: 40%; transform: translateX(-50%); width: 20px; height: 20px;"></div>
                                        <div class="rounded-circle bg-danger" style="position: absolute;bottom: -10px; left: 60%; transform: translateX(-50%); width: 20px; height: 20px;"></div>
                                    </div>
                                </div> 
                                
                                
                              <?php $taskby='';$taskdate='';} ?>
                              
                              <div class="col-sm-12 col-md-4 col-lg-4 mb-4">
                                    <div class="card p-3 border rounded border-success hover-div d-flex flex-column align-items-stretch h-100 text-dark">
                                        <b>Reminder</b><hr>
                                        <div class="rounded-circle bg-danger" style="position: absolute;bottom: -10px; left: 40%; transform: translateX(-50%); width: 20px; height: 20px;"></div>
                                        <div class="rounded-circle bg-danger" style="position: absolute;bottom: -10px; left: 60%; transform: translateX(-50%); width: 20px; height: 20px;"></div>
                                    </div>
                              </div>
                              
                              <div class="col-sm-12 col-md-4 col-lg-4 mb-4">
                                    <div class="card p-3 border rounded border-success hover-div d-flex flex-column align-items-stretch h-100 text-dark">
                                        <b>Warning</b><hr>
                                        <div class="rounded-circle bg-danger" style="position: absolute;bottom: -10px; left: 40%; transform: translateX(-50%); width: 20px; height: 20px;"></div>
                                        <div class="rounded-circle bg-danger" style="position: absolute;bottom: -10px; left: 60%; transform: translateX(-50%); width: 20px; height: 20px;"></div>
                                    </div>
                              </div>
                                
                              </div>
                           </div>
                        </div>
                      </div>
                      
                      <?php $i++;} ?>
                    
                
      </div>
    </section>
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    <script>
        document.getElementById("grid-view-btn").addEventListener("click", function () {
            document.getElementById("grid-view").style.display = "block";
            document.getElementById("list-view").style.display = "none";
            document.getElementById("list-view-btn").classList.remove('btn-info');
            document.getElementById("grid-view-btn").classList.add('btn-info');
        });
    
        document.getElementById("list-view-btn").addEventListener("click", function () {
            document.getElementById("grid-view").style.display = "none";
            document.getElementById("list-view").style.display = "block";
            document.getElementById("grid-view-btn").classList.remove('btn-info');
            document.getElementById("list-view-btn").classList.add('btn-info');
        });
        
        document.getElementById("tabular-view-btn").addEventListener("click", function () {
            document.getElementById("grid-view").style.display = "none";
            document.getElementById("tabular-view").style.display = "block";
            document.getElementById("grid-view-btn").classList.remove('btn-info');
            document.getElementById("tabular-view-btn").classList.add('btn-info');
        });

    </script>
    
    
    
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

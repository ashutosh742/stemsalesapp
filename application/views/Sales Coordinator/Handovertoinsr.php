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
          <div class="card col-12  p-3 text-center">
              <center><h5>New Handover</h5></center><hr>
             <div class="row">
                 <?php 
                    $client = $this->Menu_model->get_clientbyid($pid);
                    $bdid=$client[0]->bd_id;
                    $pcode=$client[0]->projectcode;
                    $bddata = $this->Menu_model->get_bdnamebyid($bdid);
                    $bdyear = $this->Menu_model->get_yearbybd($bdid);
                    $mdata = $this->Menu_model->get_Programbypcode($pcode);
                    $fgtask = $this->Menu_model->get_fmtaskAssing($pcode);
                    $joincall = $this->Menu_model->get_joincallpcode($pcode);
                    if($joincall){
                        $cdate = date('Y-m-d');
                        $dud = $joincall[0]->dud;
                        $dudc = $this->Menu_model->timediff($dud,$cdate);
                        $dad = $joincall[0]->dad;
                        $dadc = $this->Menu_model->timediff($dad,$cdate);
                        $pd = $joincall[0]->pd;
                        $pdc = $this->Menu_model->timediff($pd,$cdate);
                        $pbpd = $joincall[0]->pbpd;
                        $pbpdc = $this->Menu_model->timediff($pbpd,$cdate);
                        $pad = $joincall[0]->pad;
                        $padc = $this->Menu_model->timediff($pad,$cdate);
                        $disd = $joincall[0]->disd;
                        $disdc = $this->Menu_model->timediff($disd,$cdate);
                        $insd = $joincall[0]->insd;
                        $insdc = $this->Menu_model->timediff($insd,$cdate);
                        $insrd = $joincall[0]->insrd;
                        $insrdc = $this->Menu_model->timediff($insrd,$cdate);
                ?>
                
                  <div class="col-lg-12 col-md-12 col-sm p-3 rounded border border-info">
                        <b>BD Name : <?=$bddata[0]->name?> | Client Name : <?=$client[0]->client_name?> | Project Code : <?=$pcode?> | Project Code : <?=$pcode?> | Total School : <?=$mdata[0]->tspd?> | PIA Involve : <?=$mdata[0]->pia?> | IMP Involve : <?=$mdata[0]->imp?></b>
                  </div>
                  
                  
                  <div class="col-lg-12 col-md-12 col-sm p-3 rounded border border-info p-3">
                        <center><h4>Handover Process</h4></center>
                  </div>
                  
                  
                  <div class="row p-3 text-center m-auto">
                      <div class="col-sm-12 col-md-4 col-lg-4 mb-4">
                          <div class="card p-3 border rounded border-success hover-div d-flex flex-column align-items-stretch h-100">
                            <a href="<?=base_url();?>Menu/HandoverForm/<?=$pid?>">
                            <b>Handover to PM</b><hr>
                            <b><?=$client[0]->htpm?></b><hr>
                            <b><?=$bddata[0]->name?></b></a>
                          </div>
                      </div>
                      <div class="col-sm-12 col-md-4 col-lg-4 mb-4">
                          <div class="card p-3 border rounded border-success hover-div d-flex flex-column align-items-stretch h-100">
                            <b>Handover to Account</b><hr>
                            <b><?=$client[0]->htac?></b><hr>
                            <b><?=$bddata[0]->name?></b></a>
                      </div>
                      </div>
                      <div class="col-sm-12 col-md-4 col-lg-4 mb-4">
                          <div class="card p-3 border rounded border-success hover-div d-flex flex-column align-items-stretch h-100">
                            <b>Project code Created</b><hr>
                            <b><?=$client[0]->sdatet?></b><hr>
                            <b>Account_Omkar</b></a>
                      </div>
                      </div>
                      <div class="col-sm-12 col-md-4 col-lg-4 mb-4">
                          <div class="card p-3 border rounded border-success hover-div d-flex flex-column align-items-stretch h-100">
                            <b>Join Call</b><hr>
                            <b><?=$joincall[0]->startt?></b><hr>
                            <b><?=$joincall[0]->closet?></b><hr>
                            <b>Vikash Panchal</b><hr>
                      </div>
                      </div>
                      <div class="col-sm-12 col-md-4 col-lg-4 mb-4">
                          <div class="card p-3 border rounded border-success hover-div d-flex flex-column align-items-stretch h-100">
                          <b>PIA Assign</b><hr>
                          <b><?=$client[0]->sdatet?></b><hr>
                          <b>Vikash Panchal</b><hr>
                      </div>
                      </div>
                      <div class="col-sm-12 col-md-4 col-lg-4 mb-4">
                          <div class="card p-3 border rounded border-success hover-div d-flex flex-column align-items-stretch h-100">
                          <b>Handover to FM</b><hr>
                          <b><?=$client[0]->sdatet?></b><hr>
                          <b>Vikash Panchal</b><hr>
                      </div>
                      </div>
                  </div>
                  
                  
                  <div class="col-lg-12 col-md-12 col-sm p-3 rounded border border-info p-3">
                        <center><h4><a target="_blank" href="<?=base_url();?>Menu/DesignProcess/<?=$pid?>">Design Process (<?=$dud?>)</a></h4></center>
                  </div>
                  
                  
                  <div class="row p-3 text-center m-auto">
                      <div class="col-sm-12 col-md-4 col-lg-6 mb-4">
                          <div class="card p-3 border rounded border-success hover-div d-flex flex-column align-items-stretch h-100">
                            <b>Design Process</b><hr>
                            <?php $fgtask = $this->Menu_model->get_fmtaskAssing($pcode);?>
                            <b><?=$fgtask[0]->assign_dt?></b><hr>
                            <b>Rajib Dutta</b><hr>
                      </div></div>
                      <div class="col-sm-12 col-md-4 col-lg-4 mb-4">
                          <div class="card p-3 border rounded border-success hover-div d-flex flex-column align-items-stretch h-100">
                            <b>Design Approve By BD<br>(<?=$dad?>)</b><hr>
                            <?php $dbdapr = $this->Menu_model->get_designstart($pid);?>
                            <b><?=$dbdapr[0]->sdatet?></b><hr>
                            <b><?=$bddata[0]->name?></b><hr>
                      </div></div>
                  </div>
                  
                  
                  <div class="col-lg-12 col-md-12 col-sm p-3 rounded border border-info p-3">
                        <center><h4>Printing Process (<?=$pd?>)</h4></center>
                  </div>
                  <div class="row p-3 text-center m-auto">
                      <div class="col-sm-12 col-md-4 col-lg-4 mb-4">
                          <div class="card p-3 border rounded border-success hover-div d-flex flex-column align-items-stretch h-100">
                          <a target="_blank" href="<?=base_url();?>Menu/BackdropPrinting/<?=$pid?>"><b>Backdrop Printing</b></a>
                      </div></div>
                      <div class="col-sm-12 col-md-4 col-lg-4 mb-4">
                          <div class="card p-3 border rounded border-success hover-div d-flex flex-column align-items-stretch h-100">
                          <a target="_blank" href="<?=base_url();?>Menu/UManualPrinting/<?=$pid?>"><b>User Manual Printing</b></a>
                      </div></div>
                      <div class="col-sm-12 col-md-4 col-lg-4 mb-4">
                          <div class="card p-3 border rounded border-success hover-div d-flex flex-column align-items-stretch h-100">
                          <a target="_blank" href="<?=base_url();?>Menu/TManualPrinting/<?=$pid?>"><b>Training Manual Printing</b></a>
                      </div></div>
                  </div>
                  
                  
                  <div class="col-lg-12 col-md-12 col-sm p-3 rounded border border-info p-3">
                        <center><h4>Purchase Process (<?=$pbpd?>)</h4></center>
                  </div>
                  <div class="row p-3 text-center m-auto">
                      <div class="col-sm-12 col-md-4 col-lg-4 mb-4">
                          <div class="card p-3 border rounded border-success hover-div d-flex flex-column align-items-stretch h-100">
                            <a target="_blank" href="<?=base_url();?>Menu/PurchaseDetail/<?=$pid?>"><b>Purchase Inisated</b></a>
                      </div></div>
                      <div class="col-sm-12 col-md-4 col-lg-4 mb-4">
                          <div class="card p-3 border rounded border-success hover-div d-flex flex-column align-items-stretch h-100">
                            <b>Partical Board Delivered</b>
                      </div></div>
                  </div>
                  
                  <div class="col-lg-12 col-md-12 col-sm p-3 rounded border border-info p-3">
                        <center><h4>Packing Process  (<?=$pad?>)</h4></center>
                  </div>
                  <div class="row p-3 text-center m-auto">
                      <div class="col-sm-12 col-md-4 col-lg-4 mb-4">
                          <div class="card p-3 border rounded border-success hover-div d-flex flex-column align-items-stretch h-100">
                            <a target="_blank" href="<?=base_url();?>Menu/Preparing/<?=$pid?>"><b>Preparing</b></a>
                      </div></div>
                      <div class="col-sm-12 col-md-4 col-lg-4 mb-4">
                          <div class="card p-3 border rounded border-success hover-div d-flex flex-column align-items-stretch h-100">
                            <a target="_blank" href="<?=base_url();?>Menu/Packing/<?=$pid?>"><b>Packing</b></a>
                      </div></div>
                  </div>
                  
                  <div class="col-lg-12 col-md-12 col-sm p-3 rounded border border-info p-3">
                        <center><h4>Pre Installation Process</h4></center>
                  </div>
                  <div class="row p-3 text-center m-auto">
                      <div class="col-sm-12 col-md-4 col-lg-4 mb-4">
                          <div class="card p-3 border rounded border-success hover-div d-flex flex-column align-items-stretch h-100">
                            <a target="_blank" href="<?=base_url();?>Menu/Packing/<?=$pid?>"><b>School Readiness Call</b></a>
                      </div></div>
                      <div class="col-sm-12 col-md-4 col-lg-4 mb-4">
                          <div class="card p-3 border rounded border-success hover-div d-flex flex-column align-items-stretch h-100">
                            <a target="_blank" href="<?=base_url();?>Menu/Packing/<?=$pid?>"><b>Installation Scheduling Call</b></a>
                      </div></div>
                  </div>
                  
                  
                    <div class="col-lg-12 col-md-12 col-sm p-3 rounded border border-info p-3">
                        <center><h4>Dispace Process (<?=$disd?>)</h4></center>
                    </div>
                    <div class="row p-3 text-center m-auto">
                      <div class="col-sm-12 col-md-4 col-lg-4 mb-4">
                          <div class="card p-3 border rounded border-success hover-div d-flex flex-column align-items-stretch h-100">
                            <a target="_blank" href="<?=base_url();?>Menu/Packing/<?=$pid?>"><b>Dispace Process</b></a>
                      </div></div>
                    </div> 
                    
                    
                    <div class="col-lg-12 col-md-12 col-sm p-3 rounded border border-info p-3">
                        <center><h4>Dilivery Process (<?=$disd?>)</h4></center>
                    </div>
                    <div class="row p-3 text-center m-auto">
                      <div class="col-sm-12 col-md-4 col-lg-4 mb-4">
                          <div class="card p-3 border rounded border-success hover-div d-flex flex-column align-items-stretch h-100">
                            <a target="_blank" href="<?=base_url();?>Menu/Packing/<?=$pid?>"><b>Logistic Info Updated</b></a>
                      </div></div>
                      <div class="col-sm-12 col-md-4 col-lg-4 mb-4">
                          <div class="card p-3 border rounded border-success hover-div d-flex flex-column align-items-stretch h-100">
                            <a target="_blank" href="<?=base_url();?>Menu/Packing/<?=$pid?>"><b>eWay Bill Generated</b></a>
                      </div></div>
                      <div class="col-sm-12 col-md-4 col-lg-4 mb-4">
                          <div class="card p-3 border rounded border-success hover-div d-flex flex-column align-items-stretch h-100">
                            <a target="_blank" href="<?=base_url();?>Menu/Packing/<?=$pid?>"><b>Dilivery Process</b></a>
                      </div></div>
                    </div> 
                    
                    
                    <div class="col-lg-12 col-md-12 col-sm p-3 rounded border border-info p-3">
                        <center><h4>Transit Process</h4></center>
                    </div>
                    <div class="row p-3 text-center m-auto">
                      <div class="col-sm-12 col-md-4 col-lg-4 mb-4">
                          <div class="card p-3 border rounded border-success hover-div d-flex flex-column align-items-stretch h-100">
                            <a target="_blank" href="<?=base_url();?>Menu/Packing/<?=$pid?>"><b>Recive by Installation Person</b></a>
                      </div></div>
                      <div class="col-sm-12 col-md-4 col-lg-4 mb-4">
                          <div class="card p-3 border rounded border-success hover-div d-flex flex-column align-items-stretch h-100">
                            <a target="_blank" href="<?=base_url();?>Menu/Packing/<?=$pid?>"><b>Diliver in School</b></a>
                      </div></div>
                    </div> 
                    
                    
                    <div class="col-lg-12 col-md-12 col-sm p-3 rounded border border-info p-3">
                        <center><h4>Installation Process (<?=$insd?>)</h4></center>
                    </div>
                    <div class="row p-3 text-center m-auto">
                      <div class="col-sm-12 col-md-4 col-lg-4 mb-4">
                          <div class="card p-3 border rounded border-success hover-div d-flex flex-column align-items-stretch h-100">
                            <a target="_blank" href="<?=base_url();?>Menu/Packing/<?=$pid?>"><b>Installation Visit</b></a>
                      </div></div>
                      <div class="col-sm-12 col-md-4 col-lg-4 mb-4">
                          <div class="card p-3 border rounded border-success hover-div d-flex flex-column align-items-stretch h-100">
                            <a target="_blank" href="<?=base_url();?>Menu/Packing/<?=$pid?>"><b>Report Upload  (<?=$insrd?>)</b></a>
                      </div></div>
                      <div class="col-sm-12 col-md-4 col-lg-4 mb-4">
                          <div class="card p-3 border rounded border-success hover-div d-flex flex-column align-items-stretch h-100">
                            <a target="_blank" href="<?=base_url();?>Menu/Packing/<?=$pid?>"><b>Installation Review</b></a>
                      </div></div>
                      <div class="col-sm-12 col-md-4 col-lg-4 mb-4">
                          <div class="card p-3 border rounded border-success hover-div d-flex flex-column align-items-stretch h-100">
                            <a target="_blank" href="<?=base_url();?>Menu/Packing/<?=$pid?>"><b>School Added in SPD</b></a>
                      </div></div>
                    </div>
                    
                    <div class="col-lg-12 col-md-12 col-sm p-3 rounded border border-info p-3">
                        <center><h4>RID Detail</h4></center>
                    </div>
                    <div class="row p-3 text-center m-auto">
                      <div class="col-sm-12 col-md-4 col-lg-4 mb-4">
                          <div class="card p-3 border rounded border-success hover-div d-flex flex-column align-items-stretch h-100">
                            <b>Total Repair</b>
                      </div></div>
                      <div class="col-sm-12 col-md-4 col-lg-4 mb-4">
                          <div class="card p-3 border rounded border-success hover-div d-flex flex-column align-items-stretch h-100">
                            <b>Total Replacement</b>
                      </div></div>
                    </div> 
                    
                    
                                 
                                            
            </div>
        </div>
                        
                        
                        <?php }else{
                        echo "<div class='p-3 col-4 m-auto'><h2>First You Need to Take Join Call</h2></div>";
                    } ?>
                    
              </div>
          </div>
      </div>
    </section>
   </div>
</div> 


<style>

.hover-div:hover {
  box-shadow: 0 2px 2px;
}
</style> 
    
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

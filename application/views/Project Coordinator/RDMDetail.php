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
            <h1 class="m-0">RID Pending Detail</h1>
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
          <button id="grid-view-btn" class="btn border">Grid View</button>
          <button id="list-view-btn" class="btn border">Xls View</button>
          <button id="tabular-view-btn" class="btn border">Tabular View</button>
            <div class="container-fluid card p-5" id="data-container">
                <div class="row text-center" id="grid-view">
                    
                    <?php 
                    
                        $sidrid = $this->Menu_model->get_sidrid($sid);
                        $tidrid = $this->Menu_model->get_tidrid($tid);
                        $tidrid = $this->Menu_model->get_tidrid($tid);
                        $sname = $sidrid[0]->sname;
                        $pyear = $sidrid[0]->project_year;
                        $pcode = $sidrid[0]->project_code;
                        $code = "$chid-$sid-$tid";
                        
                        $sname = $sidrid[0]->sname;
                        $projectcode = $sidrid[0]->projectcode;
                        $code = $chid."-".$sid."-".$tid;
                        $sdatet = $tidrid[0]->sdatet;
                        $cdate = date('Y-m-d H:i:s');
                        $ftime = $this->Menu_model->timediff($sdatet,$cdate);
                    
                    ?>
                        <div class="col-sm-12 col-md-4 col-lg-4 mb-4">
                          <a target="_blank" href="RDMDetail/<?=$chid?>/<?=$sid?>/<?=$tid?>">
                          <div class="card p-3 border rounded border-success hover-div d-flex flex-column align-items-stretch h-100 text-dark">
                                Request ID<br><b><?= $code ?></b><hr>
                                Project Code<br><b style="color:#D4AC0D"><?= $pcode ?></b><hr>
                                Project Year<br><b><?= $pyear ?></b><hr>
                                <?php if($tidrid){?>
                                Task Type<br><b style="color:#2471A3"><?= $tidrid[0]->taskname ?></b><hr>
                                Task By<br><b><?= $tidrid[0]->fullname ?></b><hr>
                                <?php } ?>
                                School Name<br><b style="color:#FA8072"><?= $sname ?></b><hr>
                                <div class="rounded-circle bg-danger" style="position: absolute;bottom: -10px; left: 40%; transform: translateX(-50%); width: 20px; height: 20px;"></div>
                                <div class="rounded-circle bg-danger" style="position: absolute;bottom: -10px; left: 60%; transform: translateX(-50%); width: 20px; height: 20px;"></div>
                          </div>
                          </a>
                        </div>
                        
                        
                        
                        <div class="col-sm-12 col-md-8 col-lg-8 mb-4">
                            
                            <div class="row">
                                <?php  foreach($sidrid as $srid){$type = $srid->type;
                                    $model_name = $srid->model_name;
                                    $part_name = $srid->part_name;
                                    $remark = $srid->remark;
                                    $midelrid = $this->Menu_model->get_midelrid($projectcode,$model_name);
                                    
                                ?>
                                <div class="col-sm-12 col-md-6 col-lg-4 mb-6">
                                  <div class="card p-3 border rounded border-success hover-div d-flex flex-column align-items-stretch h-100 text-dark">
                                        <b><?=$type?><hr><?=$model_name?><br><?=$part_name?><br><?=$remark?><hr><?=$sdatet?><br><?=$ftime?></b>
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
                                        <div class="rounded-circle bg-danger" style="position: absolute;bottom: -10px; left: 40%; transform: translateX(-50%); width: 20px; height: 20px;"></div>
                                        <div class="rounded-circle bg-danger" style="position: absolute;bottom: -10px; left: 60%; transform: translateX(-50%); width: 20px; height: 20px;"></div>
                                  </div>
                                </div>
                                <?php }?>
                            </div>
                        </div>  
                </div>
                
                
                                <div class="container-fluid card p-5">
                                    <div class="row text-center">
                                        <div class="col-sm-12 col-md-4 col-lg-4 mb-3">
                                            <div class="card p-3 border rounded border-success hover-div d-flex flex-column align-items-stretch h-100 text-dark">
                                                Request Detail<hr>
                                                <b><?=$sdatet?></b><hr>
                                                <b><?=$tidrid[0]->fullname?></b><hr>
                                                <div class="rounded-circle bg-danger" style="position: absolute;bottom: -10px; left: 40%; transform: translateX(-50%); width: 20px; height: 20px;"></div>
                                                <div class="rounded-circle bg-danger" style="position: absolute;bottom: -10px; left: 60%; transform: translateX(-50%); width: 20px; height: 20px;"></div>
                                            </div>
                                        </div>
                                        
                                        <div class="col-sm-12 col-md-4 col-lg-4 mb-3">
                                            <div class="card p-3 border rounded border-success hover-div d-flex flex-column align-items-stretch h-100 text-dark">
                                                RM Apr Detail<hr>
                                                <b><?=$sdatet?></b><hr>
                                                Ravindra Yadav<hr>
                                                <div class="rounded-circle bg-danger" style="position: absolute;bottom: -10px; left: 40%; transform: translateX(-50%); width: 20px; height: 20px;"></div>
                                                <div class="rounded-circle bg-danger" style="position: absolute;bottom: -10px; left: 60%; transform: translateX(-50%); width: 20px; height: 20px;"></div>
                                            </div>
                                        </div>
                                  
                                        <div class="col-sm-12 col-md-4 col-lg-4 mb-3">
                                            <div class="card p-3 border rounded border-success hover-div d-flex flex-column align-items-stretch h-100 text-dark">
                                            
                                                PM Apr Detail<hr>
                                                <b>Pending From <?=$ftime?></b></b><hr>
                                                <div class="rounded-circle bg-danger" style="position: absolute;bottom: -10px; left: 40%; transform: translateX(-50%); width: 20px; height: 20px;"></div>
                                                <div class="rounded-circle bg-danger" style="position: absolute;bottom: -10px; left: 60%; transform: translateX(-50%); width: 20px; height: 20px;"></div>
                                            </div>
                                        </div>
                                  
                                        <div class="col-sm-12 col-md-4 col-lg-4 mb-3">
                                            <div class="card p-3 border rounded border-success hover-div d-flex flex-column align-items-stretch h-100 text-dark">
                                            
                                                FM Assing Detail<hr>
                                                <b>No Data</b><hr>
                                                <div class="rounded-circle bg-danger" style="position: absolute;bottom: -10px; left: 40%; transform: translateX(-50%); width: 20px; height: 20px;"></div>
                                                <div class="rounded-circle bg-danger" style="position: absolute;bottom: -10px; left: 60%; transform: translateX(-50%); width: 20px; height: 20px;"></div>
                                            </div>
                                        </div>
                                  
                                        <div class="col-sm-12 col-md-4 col-lg-4 mb-3">
                                            <div class="card p-3 border rounded border-success hover-div d-flex flex-column align-items-stretch h-100 text-dark">
                                            
                                                Packing Detail<hr>
                                                <b>No Data</b><hr>
                                                <div class="rounded-circle bg-danger" style="position: absolute;bottom: -10px; left: 40%; transform: translateX(-50%); width: 20px; height: 20px;"></div>
                                                <div class="rounded-circle bg-danger" style="position: absolute;bottom: -10px; left: 60%; transform: translateX(-50%); width: 20px; height: 20px;"></div>
                                            </div>
                                        </div>
                                  
                                        <div class="col-sm-12 col-md-4 col-lg-4 mb-3">
                                            <div class="card p-3 border rounded border-success hover-div d-flex flex-column align-items-stretch h-100 text-dark">
                                            
                                                Packing Check Detail<hr>
                                                <b>No Data</b><hr>
                                                <div class="rounded-circle bg-danger" style="position: absolute;bottom: -10px; left: 40%; transform: translateX(-50%); width: 20px; height: 20px;"></div>
                                                <div class="rounded-circle bg-danger" style="position: absolute;bottom: -10px; left: 60%; transform: translateX(-50%); width: 20px; height: 20px;"></div>
                                            </div>
                                        </div>
                                  
                                        <div class="col-sm-12 col-md-4 col-lg-4 mb-3">
                                            <div class="card p-3 border rounded border-success hover-div d-flex flex-column align-items-stretch h-100 text-dark">
                                            
                                                Dispatch Detail<hr>
                                                <b>No Data</b><hr>
                                                <div class="rounded-circle bg-danger" style="position: absolute;bottom: -10px; left: 40%; transform: translateX(-50%); width: 20px; height: 20px;"></div>
                                                <div class="rounded-circle bg-danger" style="position: absolute;bottom: -10px; left: 60%; transform: translateX(-50%); width: 20px; height: 20px;"></div>
                                            </div>
                                        </div>
                                  
                                        <div class="col-sm-12 col-md-4 col-lg-4 mb-3">
                                            <div class="card p-3 border rounded border-success hover-div d-flex flex-column align-items-stretch h-100 text-dark">
                                            
                                                Delivery Detail<hr>
                                                <b>No Data</b><hr>
                                                <div class="rounded-circle bg-danger" style="position: absolute;bottom: -10px; left: 40%; transform: translateX(-50%); width: 20px; height: 20px;"></div>
                                                <div class="rounded-circle bg-danger" style="position: absolute;bottom: -10px; left: 60%; transform: translateX(-50%); width: 20px; height: 20px;"></div>
                                            </div>
                                        </div>
                                  
                                        <div class="col-sm-12 col-md-4 col-lg-4 mb-3">
                                            <div class="card p-3 border rounded border-success hover-div d-flex flex-column align-items-stretch h-100 text-dark">
                                            
                                                Receive Detail<hr>
                                                <b>No Data</b><hr>
                                                <div class="rounded-circle bg-danger" style="position: absolute;bottom: -10px; left: 40%; transform: translateX(-50%); width: 20px; height: 20px;"></div>
                                                <div class="rounded-circle bg-danger" style="position: absolute;bottom: -10px; left: 60%; transform: translateX(-50%); width: 20px; height: 20px;"></div>
                                            </div>
                                        </div>
                                  
                                  
                                        <div class="col-sm-12 col-md-4 col-lg-4 mb-3">
                                            <div class="card p-3 border rounded border-success hover-div d-flex flex-column align-items-stretch h-100 text-dark">
                                            
                                                Replace/Repair Detail<hr>
                                                <b>No Data</b><hr>
                                                <div class="rounded-circle bg-danger" style="position: absolute;bottom: -10px; left: 40%; transform: translateX(-50%); width: 20px; height: 20px;"></div>
                                                <div class="rounded-circle bg-danger" style="position: absolute;bottom: -10px; left: 60%; transform: translateX(-50%); width: 20px; height: 20px;"></div>
                                            </div>
                                        </div>
                                  
                                  
                                  
                                        <div class="col-sm-12 col-md-4 col-lg-4 mb-3">
                                            <div class="card p-3 border rounded border-success hover-div d-flex flex-column align-items-stretch h-100 text-dark">
                                                No Wrking Model Send to Factory Detail<hr>
                                                <b>No Data</b><hr>
                                                <div class="rounded-circle bg-danger" style="position: absolute;bottom: -10px; left: 40%; transform: translateX(-50%); width: 20px; height: 20px;"></div>
                                                <div class="rounded-circle bg-danger" style="position: absolute;bottom: -10px; left: 60%; transform: translateX(-50%); width: 20px; height: 20px;"></div>
                                            </div>
                                        </div>
                                  
                                        <div class="col-sm-12 col-md-4 col-lg-4 mb-3">
                                            <div class="card p-3 border rounded border-success hover-div d-flex flex-column align-items-stretch h-100 text-dark">
                                                No Wrking Model Receive in Factory<hr>
                                                <b>No Data</b><hr>
                                                <div class="rounded-circle bg-danger" style="position: absolute;bottom: -10px; left: 40%; transform: translateX(-50%); width: 20px; height: 20px;"></div>
                                                <div class="rounded-circle bg-danger" style="position: absolute;bottom: -10px; left: 60%; transform: translateX(-50%); width: 20px; height: 20px;"></div>
                                            </div>
                                        </div>
                   </div>
                    
                           
            </div>
        </section> 
          
              
                
          </div>
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

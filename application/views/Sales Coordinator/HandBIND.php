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
            <h1 class="m-0">Handover to Installation</h1>
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
                    
                    <?php $HandBIND = $this->Menu_model->get_HandBINDs($uid);
                    foreach($HandBIND as $cp){
                    $bdid = $cp->bd_id;
                    $project = $cp->projectcode;
                    $mdata = $this->Menu_model->get_Programbypcode($project);
                    $bddata = $this->Menu_model->get_bdnamebyid($bdid);
                    ?>
                        <div class="col-sm-12 col-md-4 col-lg-4 mb-4">
                          <a href="<?=base_url();?>Menu/HandBINDDetail/<?=$cp->cid?>" target="_blank">
                          <div class="card p-3 border rounded border-success hover-div d-flex flex-column align-items-stretch h-100 text-dark">
                              Client Name<br><b style="color:#808080"><?=$cp->cname?></b><hr>
                              Project Code<br><b style="color:#D4AC0D"><?=$project?></b><hr>
                              <b>Total School : <?php if($mdata){echo $mdata[0]->tspd;}?></b><hr>
                              BD Name<br><b><?=$bddata[0]->name?></b><hr>
                              PIA Involve<br><b style="color:#2471A3"><?php if($mdata){echo $mdata[0]->pia;}?></b><hr>
                              IMP Involve<br><b style="color:#2471A3"><?php if($mdata){echo $mdata[0]->imp;}?></b><hr>
                              Year<br><b><?=$cp->pyear?></b><hr>
                              Last Action by Operation<br><b style="color:#2471A3">Installation Call<br><?=$cp->ltask?></b><hr>
                              Last Action by Factory<br><b style="color:#FA8072">Printing Assign<br><?=$cp->ltask?></b><hr>
                              Total Reminder<br><b style="color:#682642">0</b><hr>
                              Total Worning<br><b style="color:#682642">0</b><hr>
                                <?php if($cp->tdiff>15){$tcolor = "bg-danger";}else{$tcolor = "bg-success";}?>
                                <div class="rounded-circle bg-danger" style="position: absolute;bottom: -10px; left: 40%; transform: translateX(-50%); width: 20px; height: 20px;"></div>
                                <div class="rounded-circle bg-danger" style="position: absolute;bottom: -10px; left: 60%; transform: translateX(-50%); width: 20px; height: 20px;"></div>
                          </div>
                          </a>
                        </div>
                       <?php } ?>
                </div>
                <div id="list-view" style="display: none;">
                    2
                </div>
                <div id="tabular-view" style="display: none;">
                    3
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

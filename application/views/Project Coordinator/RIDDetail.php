<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="wi$dth=device-wi$dth, initial-scale=1">
  <title>STEM APP | WebAPP</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.0/css/all.min.css">
  <link rel="stylesheet" href="<?=base_url();?>assets/css/all.min.css">
  <link rel="stylesheet" href="https://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css">
  <link rel="stylesheet" href="<?=base_url();?>assets/css/tempusdominus-bootstrap-4.min.css">
  <link rel="stylesheet" href="<?=base_url();?>assets/css/icheck-bootstrap.min.css">
  <link rel="stylesheet" href="<?=base_url();?>assets/css/jqvmap.min.css">
  <link rel="stylesheet" href="<?=base_url();?>assets/css/adminlte.min.css">
  <link rel="stylesheet" href="<?=base_url();?>assets/css/OverlayScrollbars.min.css">
  <link rel="stylesheet" href="<?=base_url();?>assets/css/daterangepicker.css">
  <link rel="stylesheet" href="<?=base_url();?>assets/css/summernote-bs4.min.css">
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
  <?php require('nav.php');?>
  <div class="content-wrapper">
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
              <div class="card-body">
                  <?php 
                    $rid = $this->Menu_model->get_rid($uid);
                    $uniquePY = [];
                    foreach ($rid as $ri) {
                        $uniquePY[] =  $ri->project_year; 
                    }
                    $uniquePY = array_unique($uniquePY);
                    
                    $uniqueM = [];
                    foreach ($rid as $ri) {
                        $uniqueM[] =  $ri->month_name; 
                    }
                    $uniqueM = array_unique($uniqueM);
                  ?>
                        <div class="filters">
                                <button type="button" class="btn btn-primary" class="filter-button" data-filter="all">All</button>
                                <?php foreach ($uniquePY as $index => $year) {?>
                                    <button type="button" class="btn btn-primary" class="filter-button" data-filter="<?=$year?>"><?=$year?></button>
                                <?php } ?> 
                                <?php foreach ($uniqueM as $index => $month) {?>
                                    <button type="button" class="btn btn-primary" class="filter-button" data-filter="<?=$month?>"><?=$month?></button>
                                <?php } ?>
                                
                            </div><hr>
                            <div class="card-gallery">
                                <div class="row m-auto">
                                    <?php
                                    $i = 1;
                                    foreach ($rid as $ri) {
                                        $rdate = $ri->rdate;
                                        $tid = $ri->tid;
                                        $tidrid = $this->Menu_model->get_tidrid($tid);
                                        $sid = $ri->sid;
                                        $chid = $ri->chid;
                                        $sname = $ri->sname;
                                        $noofmodel = $ri->noofmodel;
                                        $pyear = $ri->project_year;
                                        $pcode = $ri->project_code;
                                        $code = "$chid-$sid-$tid";
                                    ?>
                                        <div class="border border-4 card col-lg-3 col-sm bg-danger p-3 text-center filter-item <?=$pyear?> <?= $ri->month_name ?>">
                                            <a target="_blank" href="ModelDetail/<?=$chid?>/<?=$sid?>/<?=$tid?>">
                                                <?= $i++ ?><br>
                                                Request ID<br><b><?= $code ?></b><hr>
                                                Project Code<br><b><?= $pcode ?></b><hr>
                                                Project Year<br><b><?= $pyear ?></b><hr>
                                                Task Date<br><b><?= $ri->rdate ?></b><hr>
                                                <?php if($tidrid){?>
                                                Task Type<br><b><?= $tidrid[0]->taskname ?></b><hr>
                                                Task By<br><b><?= $tidrid[0]->fullname ?></b><hr>
                                                <?php } ?>
                                                School Name<br><b><?= $sname ?></b><hr>
                                                No of Model<br><b><?= $noofmodel ?></b>
                                            </a>
                                            <hr>
                                            <center><button type="button" class="btn btn-light">Comment</button></center>
                                        </div>
                                    <?php } ?>
                                </div>
                            </div>
                            
                            <script>
                                const filterItems = document.querySelectorAll('.filter-item');
                                const filters = document.querySelector('.filters');
                                filters.addEventListener('click', (event) => {
                                    const filterValue = event.target.getAttribute('data-filter');
                                    if (filterValue === 'all') {
                                        filterItems.forEach(item => {
                                            item.style.display = 'block';
                                        });
                                    } else {
                                        filterItems.forEach(item => {
                                            if (item.classList.contains(filterValue)) {
                                                item.style.display = 'block';
                                            } else {
                                                item.style.display = 'none';
                                            }
                                        });
                                    }
                                });
                            </script>
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
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
            <h1 class="m-0">RID Pending</h1>
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
            <button type="button" class="btn btn-primary" class="filter-button" data-filter="Maintenance">Maintenance</button>
            <button type="button" class="btn btn-primary" class="filter-button" data-filter="Installation">Installation</button>
        </div>
        <hr>
        
        
        
        
        
          <button id="grid-view-btn" class="btn border">Grid View</button>
          <button id="list-view-btn" class="btn border">Xls View</button>
          <button id="tabular-view-btn" class="btn border">Tabular View</button>
            <div class="container-fluid card p-5" id="data-container">
                <div class="row text-center" id="grid-view">
                    
                    <?php $rid = $this->Menu_model->get_rid($uid);
                    foreach($rid as $ri){
                        $rdate = $ri->rdate;
                        $tid = $ri->tid;
                        $tidrid = $this->Menu_model->get_tidrid($tid);
                        $sid = $ri->sid;
                        $chid = $ri->chid;
                        $sname = $ri->sname;
                        $stname = $ri->stname;
                        $noofmodel = $ri->noofmodel;
                        $pyear = $ri->project_year;
                        $pcode = $ri->project_code;
                        $code = "$chid-$sid-$tid";
                    
                    ?>
                        <div class="col-sm-12 col-md-4 col-lg-4 mb-4 filter-item <?=$pyear?> <?= $ri->month_name ?> <?php if($tidrid){?><?=$tidrid[0]->taskname;}?>">
                          <a target="_blank" href="RDMDetail/<?=$chid?>/<?=$sid?>/<?=$tid?>">
                          <div class="card p-3 border rounded border-success hover-div d-flex flex-column align-items-stretch h-100 text-dark">
                                Request ID<br><b><?= $code ?></b><hr>
                                Project Code<br><b style="color:#D4AC0D"><?= $pcode ?></b><hr>
                                Project Year<br><b><?= $pyear ?></b><hr>
                                Task Date<br><b><?= $ri->rdate?></b><hr>
                                <?php if($tidrid){?>
                                Task Type<br><b style="color:#2471A3"><?= $tidrid[0]->taskname ?></b><hr>
                                Task By<br><b><?= $tidrid[0]->fullname ?></b><hr>
                                Task Assign By<br><b><?=$tidrid[0]->assignby?></b><hr>
                                <?php } ?>
                                School Name<br><b style="color:#FA8072"><?= $sname ?></b><hr>
                                School Status<br><b style="color:#D4AC0D"><?=$stname?></b><hr>
                                No of Model<br><b><?= $noofmodel ?></b>
                                <div class="rounded-circle bg-danger" style="position: absolute;bottom: -10px; left: 40%; transform: translateX(-50%); width: 20px; height: 20px;"></div>
                                <div class="rounded-circle bg-danger" style="position: absolute;bottom: -10px; left: 60%; transform: translateX(-50%); width: 20px; height: 20px;"></div>
                          </div>
                          </a>
                        </div>
                       <?php } ?>
                </div>
                <div id="list-view" style="display: none;">
                    	<div class="table-responsive">
                    		<table id="example1" class="table table-striped table-bordered" cellspacing="0" width="100%">
                				<thead>
                					<tr>
                						<th>Request ID</th>
                						<th>Project Code</th>
                						<th>Project Year</th>
                						<th>Task Date</th>
                						<th>Task Type</th>
                						<th>Task By</th>
                						<th>Task Assign By</th>
                						<th>School Name</th>
                						<th>School Status</th>
                						<th>No of Model</th>
                                    </tr>
                				</thead>
                				<tbody>
                				    <?php $rid = $this->Menu_model->get_rid($uid);
                                        foreach($rid as $ri){
                                            $rdate = $ri->rdate;
                                            $tid = $ri->tid;
                                            $tidrid = $this->Menu_model->get_tidrid($tid);
                                            $sid = $ri->sid;
                                            $chid = $ri->chid;
                                            $sname = $ri->sname;
                                            $stname = $ri->stname;
                                            $noofmodel = $ri->noofmodel;
                                            $pyear = $ri->project_year;
                                            $pcode = $ri->project_code;
                                            $code = "$chid-$sid-$tid";
                                        ?>
                					<tr>
                        				<td><?= $code ?></td>
                                        <td style="color:#D4AC0D"><?= $pcode ?></td>
                                        <td><?= $pyear ?></td>
                                        <td><?= $ri->rdate?></td>
                                        <?php if($tidrid){$taskname=$tidrid[0]->taskname;$fullname=$tidrid[0]->fullname;$assignby=$tidrid[0]->assignby;}else{$taskname='';$fullname='';$assignby='';}?>
                                        <td style="color:#2471A3"><?=$taskname?></td>
                                        <td><?=$fullname?></td>
                                        <td><?=$assignby?></td>
                                        <td style="color:#FA8072"><?= $sname ?></td>
                                        <td style="color:#D4AC0D"><?=$stname?></td>
                                        <td><?=$noofmodel?></td>	
                					</tr>
                					<?php } ?>
                				</tbody>
                    		</table> 
                    	</div>
                </div>
                <div id="tabular-view" style="display: none;">
                    3
                </div>    
        </section>  
          
              
                
          </div>
      </div>
    </section>
    
    
    
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
    
    
        document.getElementById("grid-view-btn").addEventListener("click", function () {
            document.getElementById("grid-view").style.display = "block";
            document.getElementById("list-view").style.display = "none";
            document.getElementById("list-view-btn").classList.remove('btn-info');
            document.getElementById("tabular-view-btn").classList.remove('btn-info');
            document.getElementById("grid-view-btn").classList.add('btn-info');
        });
    
        document.getElementById("list-view-btn").addEventListener("click", function () {
            document.getElementById("grid-view").style.display = "none";
            document.getElementById("list-view").style.display = "block";
            document.getElementById("grid-view-btn").classList.remove('btn-info');
            document.getElementById("tabular-view-btn").classList.remove('btn-info');
            document.getElementById("list-view-btn").classList.add('btn-info');
        });
        
        document.getElementById("tabular-view-btn").addEventListener("click", function () {
            document.getElementById("grid-view").style.display = "none";
            document.getElementById("list-view").style.display = "none";
            document.getElementById("tabular-view").style.display = "block";
            document.getElementById("grid-view-btn").classList.remove('btn-info');
            document.getElementById("list-view-btn").classList.remove('btn-info');
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
      "responsive": false, "lengthChange": false, "autoWidth": false,
      "buttons": ["copy", "csv", "excel", "pdf", "print", "colvis"]
    }).buttons().container().appendTo('#example1_wrapper .col-md-6:eq(0)');
</script>
</body>
</html>

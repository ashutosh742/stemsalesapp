!DOCTYPE html>
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
<h1 class="m-0">our Team Review Report(PST)</h1>
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
<form class="p-3" method="POST" action="<?=base_url();?>/Menu/PSTAllReviewData">
<input type="date" name="sdate" class="mr-2" value="<?=$sd?>">
<input type="date" name="edate" class="mr-2" value="<?=$ed?>">
<select name="pstname">
<option value="<?=$uid?>"><?=$user['name']?></option>
</select>

<button type="submit" class="bg-primary text-light">Filter</button>
</form>
<!-- Main content -->
<section class="content">
<div class="container-fluid">
<div class="row">
  <div class="col-12">
    <div class="card">
      <div class="card-header">
      </div>
      <!-- /.card-header -->
      <div class="card-body">
        <div class="container body-content">
          <div class="page-header">
            <fieldset>
              
              <div class="table-responsive">
                <div class="table-responsive">
                  <div class="pdf-viwer">
                    <table id="example1" class="table table-striped table-bordered" cellspacing="0" width="100%">
                      <thead>
                        <tr>
                          <tr>
                            <th>S.No.</th>
                            <th>PST Name</th>
                            <th>BD Name</th>
                            <th>Company Name</th>
                            <th>Review Date</th>
                            <th>Review Remark</th>
                            <th>Status</th>
                            <th>Next Task Target Date</th>
                            <th>Action</th>
                            <th>Purpose</th>
                            <th>Review Next Action Status</th>
                          </tr>
                        </tr>
                      </thead>
                      <tbody>
                        <?php $i=1;
                        foreach($mdata as $md){
                        $bd = $md->tuid;
                        $bdname = $this->Menu_model->get_userbyid($bd);
                        $tid = $md->tid;
                        $cid_id = $md->cid;
                        $inid  = $md->cid;
                        $sid  = $md->sid;
                        $nsid  = $md->nsid;
                        $sid = $this->Menu_model->get_statusbyid($sid);
                        $nsid = $this->Menu_model->get_statusbyid($nsid);
                        $inid = $this->Menu_model->get_initbyid($inid);
                        
                        $mainbd = $inid[0]->mainbd;
                        $mainbd = $this->Menu_model->get_userbyid($mainbd);
                        
                        $mtd = $this->Menu_model->get_ccitblall($tid);
                        $ltime = $mtd[0]->last_task_date;
                        $ctime = $mtd[0]->appointmentdatetime;
                        $ntime = $mtd[0]->next_task_date;
                        if($ntime==''){$tdiff='';}else{$tdiff = $this->Menu_model->timediff($ctime,$ntime);}
                        $nltime = date('d-m-Y  h:i A', strtotime($ltime));
                        $nctime = date('d-m-Y  h:i A', strtotime($ctime));
                        $nntime = date('d-m-Y  h:i A', strtotime($ntime));?>
                        <tr>
                          <td><?=$i?></td>
                          <td><?=$bdname[0]->name?></td>
                          <td><?=$mainbd[0]->name?></td>
                          <td><a href="<?=base_url();?>/Menu/CompanyDetails/<?=$inid[0]->cmpid_id?>"><?=$mtd[0]->compname?></a></td>
                          <td><?=date('d-m-Y h:i A', strtotime($uptime = $md->updateddate));?></td>
                          <td><?=$mtd[0]->remarks?></td>
                          <td><?=$sid[0]->name?></td>
                          <?php
                          $ntid = $this->Menu_model->get_ttominid($tid,$cid_id);
                          $ntid = $ntid[0]->ntid;
                          $ntdetail = $this->Menu_model->get_ccctd($ntid);
                          $naid = $ntdetail[0]->actiontype_id;
                          $naid = $this->Menu_model->get_actionbyid($naid);
                          $naname = $naid[0]->name;
                          
                          $puid = $ntdetail[0]->purpose_id;
                          $puid = $this->Menu_model->get_purposebyid($puid);
                          $puname = $puid[0]->name;
                          
                          ?>
                          
                          <td><?=date('d-m-Y  h:i A', strtotime($ntdetail[0]->appointmentdatetime))?></td>
                          <td><?=$naname?></td>
                          <td><?=$puname?></td>
                          <td><?php if($ntdetail[0]->nextCFID==0){echo 'Open';}else{echo 'Close';}?></td>
                          <?php $i++;} ?>
                        </tbody>
                      </table>
                    </div>
                  </div>
                  </form>            <!--END OF FORM ^^-->
                </fieldset>
                
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
"responsive": false, "lengthChange": false, "autoWidth": false,
"buttons": ["copy", "csv", "excel", "pdf", "print", "colvis"]
}).buttons().container().appendTo('#example1_wrapper .col-md-6:eq(0)');
</script>
</body>
</html>
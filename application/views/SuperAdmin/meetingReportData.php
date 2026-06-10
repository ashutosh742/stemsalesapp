<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="wi$dth=device-wi$dth, initial-scale=1">
<title>STEM APP | WebAPP</title>
<!-- Google Font: Source Sans Pro -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.0/css/all.min.css">
<!-- Font Awesome -->
<link rel="stylesheet" href="<?=base_url(); ?>assets/css/all.min.css">
<!-- Ionicons -->
<link rel="stylesheet" href="https://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css">
<!-- Tempusdominus Bootstrap 4 -->
<link rel="stylesheet" href="<?=base_url(); ?>assets/css/tempusdominus-bootstrap-4.min.css">
<!-- iCheck -->
<link rel="stylesheet" href="<?=base_url(); ?>assets/css/icheck-bootstrap.min.css">
<!-- JQVMap -->
<link rel="stylesheet" href="<?=base_url(); ?>assets/css/jqvmap.min.css">
<!-- Theme style -->
<link rel="stylesheet" href="<?=base_url(); ?>assets/css/adminlte.min.css">
<!-- overlayScrollbars -->
<link rel="stylesheet" href="<?=base_url(); ?>assets/css/OverlayScrollbars.min.css">
<!-- Daterange picker -->
<link rel="stylesheet" href="<?=base_url(); ?>assets/css/daterangepicker.css">
<!-- summernote -->
<link rel="stylesheet" href="<?=base_url(); ?>assets/css/summernote-bs4.min.css">
<!-- DataTables -->
<link rel="stylesheet" href="<?=base_url(); ?>assets/css/dataTables.bootstrap4.min.css">
<link rel="stylesheet" href="<?=base_url(); ?>assets/css/responsive.bootstrap4.min.css">
<link rel="stylesheet" href="<?=base_url(); ?>assets/css/buttons.bootstrap4.min.css">
<style>
    .summary-highlight {
    background-color: #f8f9fa;
    border: 1px solid #dee2e6;
    padding: 15px;
    margin-bottom: 20px;
    border-radius: 5px;
    font-size: 1.1rem;
    font-weight: bold;
    color: #333;
}
.summary-highlight ul {
    padding-left: 20px;
    list-style-type: disc;
}
.summary-highlight h3 {
    margin-bottom: 10px;
    color: #007bff;
}

.scrollme {
overflow-x: auto;
}
</style>
</head>
<body class="hold-transition sidebar-mini layout-fixed">
<div class="wrapper">
<!-- Preloader -->

<!-- Navbar -->
<?php require ('nav.php'); ?>
<!-- /.navbar -->
<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
<!-- Content Header (Page header) -->
<div class="content-header">
<div class="container-fluid">
<div class="row mb-2">
<div class="col-sm-6">
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
<form class="p-3" method="POST" action="">
   <?php 
   //dd($meetingSummaryData);
  // exit;
      foreach($meetingSummaryData as $userid_key => $val){
        if(count($val) > 0){
          //  dd($val);
    ?>
    <div class="summary-highlight">
        <h3>Meeting Report for <?= getUserNameById($userid_key); ?></h3>
        <p><strong>Meeting Summary:</strong></p>
        <ul>
        <?php
        foreach($val as $k => $v){
           // dd($val);exit;
            if(is_array($v) && !empty($val)){
                if($k =='TotalMeetingData') {
                    echo "<li>Total Meetings: " . (isset($v['Total']) ? $v['Total'] : "0") . "</li>";
                }
                if($k =='BarginMeetingData') {
                    echo "<li>Bargin Meetings: " . (isset($v['Total']) ? $v['Total'] : "0") . "</li>";
                }
                if($k =='ScheduleMeetingData') {
                    echo "<li>Schedule Meetings: " . (isset($v['Total']) ? $v['Total'] : "0") . "</li>";
                }
                if($k =='RPMeetingData') {
                    echo "<li>RP Meetings: " . (isset($v['Total']) ? $v['Total'] : "0") . "</li>";
                }
                if($k =='NoRPMeetingData') {
                    echo "<li>No RP Meetings: " . (isset($v['Total']) ? $v['Total'] : "0") . "</li>";
                }
                if($k =='OnlyGotDetailsMeetingData') {
                    echo "<li>Only Got Details Meetings: " . (isset($v['Total']) ? $v['Total'] : "0") . "</li>";
                }
            }
        }
        ?>
        </ul>
    </div>
    <?php
        }
      }
    ?>
</form>

<div class="pdf-viwer mt-4">
    <?php 
    if(isset($val['MeetingData'])){
    ?>
    <table id="example1" class="table table-striped table-bordered" cellspacing="0" width="100%">
        <thead>
            <tr>
                <th>S.No.</th>
                <th>CIN</th>
                <th>Company Name</th>
                <th>Started At</th>
                <th>Close At</th>
                <th>Start Location</th>
                <th>Close Location</th>
                <th>RP Yes/No</th>
                <th>Potential Yes/No</th>
                <th>MOM Yes/No</th>
                <th>Thanks Mail Yes/No</th>
                <th>PST Assign Yes/No</th>
                <th>Review</th>
            </tr>
        </thead>
        <tbody>
        <?php 
        $i = 1;
       // dd($val['MeetingData']);exit;
        foreach($val['MeetingData'] as $value){
        ?>
            <tr>
                <td><?= $i++; ?></td>
                <td><?= $value['CIN']; ?></td>
                <td><?= $value['Company_Name']; ?></td>
                <td><?= $value['startm']; ?></td>
                <td><?= $value['closem']; ?></td>
                <td>
                    <a href="https://www.google.com/maps?q=<?= $value['slatitude']; ?>,<?= $value['slongitude']; ?>">
                        <i class="fas fa-map-marker-alt" style="font-size:24px"></i>
                    </a>
                </td>
                <td>
                    <a href="https://www.google.com/maps?q=<?= $value['clatitude']; ?>,<?= $value['clongitude']; ?>">
                        <i class="fas fa-map-marker-alt" style="font-size:24px"></i>
                    </a>
                </td>
                <td><?= $value['mtype']; ?></td>
                <td><?= $value['comp_business_potential']; ?></td>
                <td><?= $momc ? 'yes' : 'no'; ?></td>
                <td><?= $emailc ? 'yes' : 'no'; ?></td>
                <td><?= $psta ? 'yes' : 'no'; ?></td>
                <td><?= $value['queans']; ?><hr><?= $value['mcomment']; ?></td>
            </tr>
        <?php } ?>
        </tbody>
    </table>
    <?php } ?>
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


<!-- Control Sidebar -->
<aside class="control-sidebar control-sidebar-dark">
<!-- Control sidebar content goes here -->
</aside>
<!-- /.control-sidebar -->
</div>
<!-- ./wrapper -->
<!-- jQuery -->
<script src="<?=base_url(); ?>assets/js/jquery.min.js"></script>
<!-- jQuery UI 1.11.4 -->
<script src="<?=base_url(); ?>assets/js/jquery-ui.min.js"></script>
<!-- Resolve conflict in jQuery UI tooltip with Bootstrap tooltip -->
<script>
$.widget.bridge('uibutton', $.ui.button)
</script>
<!-- Bootstrap 4 -->
<script src="<?=base_url(); ?>assets/js/bootstrap.bundle.min.js"></script>
<!-- ChartJS -->
<script src="<?=base_url(); ?>assets/js/Chart.min.js"></script>
<!-- Sparkline -->
<script src="<?=base_url(); ?>assets/js/sparkline.js"></script>
<!-- JQVMap -->
<script src="<?=base_url(); ?>assets/js/jquery.vmap.min.js"></script>
<script src="<?=base_url(); ?>assets/js/jquery.vmap.usa.js"></script>
<!-- jQuery Knob Chart -->
<script src="plugins/jquery-knob/jquery.knob.min.js"></script>
<!-- daterangepicker -->
<script src="<?=base_url(); ?>assets/js/moment.min.js"></script>
<script src="<?=base_url(); ?>assets/js/daterangepicker.js"></script>
<!-- Tempusdominus Bootstrap 4 -->
<script src="<?=base_url(); ?>assets/js/tempusdominus-bootstrap-4.min.js"></script>
<!-- Summernote -->
<script src="<?=base_url(); ?>assets/js/summernote-bs4.min.js"></script>
<!-- overlayScrollbars -->
<script src="<?=base_url(); ?>assets/js/jquery.overlayScrollbars.min.js"></script>
<!-- DataTables  & Plugins -->
<script src="<?=base_url(); ?>assets/js/jquery.dataTables.min.js"></script>
<script src="<?=base_url(); ?>assets/js/dataTables.bootstrap4.min.js"></script>
<script src="<?=base_url(); ?>assets/js/dataTables.responsive.min.js"></script>
<script src="<?=base_url(); ?>assets/js/responsive.bootstrap4.min.js"></script>
<script src="<?=base_url(); ?>assets/js/dataTables.buttons.min.js"></script>
<script src="<?=base_url(); ?>assets/js/buttons.bootstrap4.min.js"></script>
<script src="<?=base_url(); ?>assets/js/jszip.min.js"></script>
<script src="<?=base_url(); ?>assets/js/pdfmake.min.js"></script>
<script src="<?=base_url(); ?>assets/js/vfs_fonts.js"></script>
<script src="<?=base_url(); ?>assets/js/buttons.html5.min.js"></script>
<script src="<?=base_url(); ?>assets/js/buttons.print.min.js"></script>
<script src="<?=base_url(); ?>assets/js/buttons.colVis.min.js"></script>
<!-- AdminLTE App -->
<script src="<?=base_url(); ?>assets/js/adminlte.js"></script>
<!-- AdminLTE dashboard demo (This is only for demo purposes) -->
<script src="<?=base_url(); ?>assets/js/dashboard.js"></script>
<script>
$("#example1").DataTable({
"responsive": false, "lengthChange": false, "autoWidth": false,
"buttons": ["csv", "excel", "pdf","colvis"]
}).buttons().container().appendTo('#example1_wrapper .col-md-6:eq(0)');
</script>
</body>
</html>

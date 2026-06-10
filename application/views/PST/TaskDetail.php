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
<?php
$action = $this->Menu_model->get_actionbyid($atid);
?>
<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
<!-- Content Header (Page header) -->
<div class="content-header">
<div class="container-fluid">
<div class="row mb-2">
<div class="col-sm-6">
<h1 class="m-0">Report</h1>
</div><!-- /.col -->
<div class="col-sm-6">
<ol class="breadcrumb float-sm-right">
<h4>HI! <?php $uid=$user['user_id']; ?></h4>
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
    <h3 class="card-title">Report</h3>
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
                                        <th>S.No.</th>
                                        <th>BD Name</th>
                                        <th>Company Name</th>
                                        <th>Last_Task Date</th>
                                        <th>Current_Task Date</th>
                                        <th>Next_Task Date</th>
                                        <th>Current task planned after time difference</th>
                                        <th>Next task planned after time difference</th>
                                        <th>UpdateDate</th>
                                        <th>Last_Task_Activity</th>
                                        <th>Current_Task_Activity</th>
                                        <th>Next_Task_Activity</th>
                                        <th>Last_Task_Remarks</th>
                                        <th>Current_Task_Remarks</th>
                                        <th>Next_Task_Planned</th>
                                        <th>Current_Task_Purpose</th>
                                        <th>Current Purpose Achieved</th>
                                        <th>Next_Task_Purpose</th>
                                        <th>Last_Status</th>
                                        <th>Current_Status</th>
                                        <th>Last_ActionTaken</th>
                                        <th>Current_ActionTaken</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $i=1;
                                   
                                    $mtdata = $this->Menu_model->get_alltaskdbyad($code,$atid,$uid,$tdate,1);
                                    // echo $str = $this->db->last_query();
                                    // echo "<pre>";
                                    // print_r($mtdata);
                                    foreach($mtdata as $md){
                                    $tid = $md->id;
                                    $inid  = $md->cid_id;
                                    $inid = $this->Menu_model->get_initbyid($inid);
                                    $mtd = $this->Menu_model->get_ccitblall($tid);
                                    $ltime = $mtd[0]->last_task_date;
                                    $ctime = $mtd[0]->appointmentdatetime;
                                    $ntime = $mtd[0]->next_task_date;
                                    if($ntime==''){$tdiff='';}else{$tdiff = $this->Menu_model->timediff($ctime,$ntime);}
                                    $nltime = date('d-m-Y h:i A', strtotime($ltime));
                                    $nctime = date('d-m-Y h:i A', strtotime($ctime));
                                    $nntime = date('d-m-Y h:i A', strtotime($ntime));
                                    ?>
                                    <tr>
                                        <td><?=$i?></td>
                                        <td><?=$user['name']?></td>
                                        <td><a href="../../../../CompanyDetails/<?=$inid[0]->cmpid_id?>"><?=$mtd[0]->compname?></a></td>
                                        <td><?=$nltime?></td>
                                        <td><?=$nctime?></td>
                                        <td><?=$nntime?></td>
                                        <td><?=$this->Menu_model->timediff($ltime,$ctime)?></td>
                                        <td><?=$tdiff?></td>
                                        <td><?=$mtd[0]->fwd_date?></td>
                                        <td><?=$mtd[0]->last_task_activity?></td>
                                        <td><?=$mtd[0]->current_action_type?></td>
                                        <td><?=$mtd[0]->next_task_activity?></td>
                                        <td><?=$mtd[0]->remarks?></td>
                                        <td><?=$mtd[0]->next_remarks?></td>
                                    <th><?=$mtd[0]->nextaction?></td>
                                    <td><?=$mtd[0]->last_purpose?></td>
                                    <td>NA</td>
                                    <td><?=$mtd[0]->next_purpose?></td>
                                    <td><?=$mtd[0]->last_status?></td>
                                    <td><?=$mtd[0]->current_status?></td>
                                    <td><?=$mtd[0]->last_action_taken?></td>
                                    <td><?=$mtd[0]->actontaken?></td>
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
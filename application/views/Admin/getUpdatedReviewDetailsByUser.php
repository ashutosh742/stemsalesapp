<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Annual Comany Report | STEM APP| WebAPP</title>
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
<style>
.ApprovedStatus{
padding:6px 10px;
border-radius:4px;
}
.ApprovedStatus.Pending{
background:orange;
color:white;
}
.ApprovedStatus.Reject{
background:red;
color:white;
}
.ApprovedStatus.approved{
background:green;
color:white;
}
.colapsboxsha{
border-radius:22px;
box-shadow: rgba(0, 0, 0, 0.25) 0px 0.0625em 0.0625em, rgba(0, 0, 0, 0.25) 0px 0.125em 0.5em, rgba(255, 255, 255, 0.1) 0px 0px 0px 1px inset;
}
.modal.fade.show{
background: rgba(100, 200, 150, 0.4);
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
</div>
<!-- /.col -->
<div class="col-sm-6">
<ol class="breadcrumb float-sm-right">
<h4></h4>
</ol>
</div>
<!-- /.col -->
</div>
<!-- /.row -->
</div>
<!-- /.container-fluid -->
</div>
<!-- /.content-header -->
<section class="content">
<div class="container-fluid">
<div class="row p-3">
<div class="col-sm col-md-12 col-lg-12 m-auto">
<?php if ($this->session->flashdata('action_message')): ?>
<div class="alert alert-success alert-dismissible fade show" role="alert">
<strong> <?php echo $this->session->flashdata('action_message'); ?></strong>
<button type="button" class="close" data-dismiss="alert" aria-label="Close">
<span aria-hidden="true">&times;</span>
</button>
</div>
<?php endif;

//  $udata = $this->Menu_model->getReviewDetailsByUser(100059,3);
// echo "<pre>";
// print_r($udata);
// die;


?>

<div class="card card-primary card-outline">
      <div class="card-body box-profile">
             <div class="card p-2 bg-dark">
                    <center>
                      <h3>Annual Company Review Report</h3>
                    </center>
              </div>
              <?php $getbdteam = $this->Menu_model->getTeamDetailsAdmin($uid);
                            //   var_dump($getbdteam); exit;
?>
                    <button class="btn btn-primary colapsboxsha col-md-12 col-lg-12 mt-2 d-flex justify-content-between align-items-center" type="button" data-toggle="collapse" data-target="#collapseExample90" aria-expanded="false" aria-controls="collapseExample">
                    Summary Of Financial year 2024-25
                  </button>
                  <div class="collapse show" id="collapseExample90">
                          <div class="card card-body">
                                  
                                  <div class="table-responsive">
                                        <table id="examplereview1" class="table table-striped table-bordered" cellspacing="0" width="100%">
                                              <thead>
                                                    <tr>
                                                          <th>sr. no</th>
                                                          <th>User Name</th>
                                                          <th>Top Spender</th>
                                                          <th>Upsell Client</th>
                                                          <th>Focus And Positive Key Client</th>
                                                          <th>Focus For Quarter 1</th>
                                                          <th>Focus For Quarter 2</th>
                                                          <th>Focus For Quarter 3</th>
                                                          <th>Focus For Quarter 4</th>
                                                          <th>Transfer Company</th>
                                                          <th>Open</th>
                                                          <th>Open RPM</th>
                                                          <th>Total Companies</th>
                                                          <th>Total Schools</th>
                                                          <th>Total Revenue</th>
                                                    </tr>
                                              </thead>
                                              <tbody>
                <?php $k=1;
                                                        foreach($getbdteam as $t){
                                                        $udata = $this->Menu_model->getUpdatedReviewDetailsByUsers($t->user_id,$t->type_id);
                                                        // echo "<pre>";
                                                        //     print_r($udata['topspender'][$t->user_id]);
                                                          
                    ?>
                                                        <tr>
                        <td><?=$k++?></td>
                        <td><?=$t->name?></td>
                        <td><a href="<?=base_url();?>Menu/newFinancialYearDataUpdated/<?=$t->user_id?>/topspender"><?=$udata['topspender_yes'][$t->user_id]?></a></td>
                        <td><a href="<?=base_url();?>Menu/newFinancialYearDataUpdated/<?=$t->user_id?>/upsell"><?=$udata['upsell_yes'][$t->user_id]?></a></td>
                        <td><a href="<?=base_url();?>Menu/newFinancialYearDataUpdated/<?=$t->user_id?>/focusPositiveKeyClient"><?=$udata['focusPositiveKeyClient'][$t->user_id]?></a></td>
                        <td><a href="<?=base_url();?>Menu/newFinancialYearDataUpdated/<?=$t->user_id?>/quarter1"><?=$udata['focusQuarter1'][$t->user_id]?></a></td>
                        <td><a href="<?=base_url();?>Menu/newFinancialYearDataUpdated/<?=$t->user_id?>/quarter2"><?=$udata['focusQuarter2'][$t->user_id]?></a></td>
                        <td><a href="<?=base_url();?>Menu/newFinancialYearDataUpdated/<?=$t->user_id?>/quarter3"><?=$udata['focusQuarter3'][$t->user_id]?></a></td>
                        <td><a href="<?=base_url();?>Menu/newFinancialYearDataUpdated/<?=$t->user_id?>/quarter4"><?=$udata['focusQuarter4'][$t->user_id]?></a></td>
                        <td><a href="<?=base_url();?>Menu/newFinancialYearDataUpdated/<?=$t->user_id?>/transfer"><?=$udata['keepcompany'][$t->user_id]?></a></td>
                        <td><a href="<?=base_url();?>Menu/newFinancialYearDataUpdated/<?=$t->user_id?>/open"><?=$udata['open'][$t->user_id]?></a></td>
                        <td><a href="<?=base_url();?>Menu/newFinancialYearDataUpdated/<?=$t->user_id?>/openrpm"><?=$udata['openrpm'][$t->user_id]?></a></td>
                        <td><?=$udata['totalcmp'][$t->user_id]?></td>
                        <td><?=number_format($udata['totalSchool'][$t->user_id])?></td>
                        <td><?=number_format($udata['revenue'][$t->user_id])?></td>
                                                        </tr>
                    <?php } ?>
                                                  </tbody>
                                            </table>
            <!-- Modal -->
                        
                                      </div>
                              </div>
                        </div>
                        <br/>
                      
                        
                </form>
          </div>
    </div>
</div>
</div>
</div>
</section>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
<script type='text/javascript'>
function RejectButton(mid,id,val){
$('#exampleModalCenter'+mid).modal('show');
$('#exampleModalCenter'+mid+' #rejectid').val(id);
}

function Reject(mid,id,val){
// alert(mid);
// alert('#exampleModalCenterdata'+mid);
$('#exampleModalCenterdata').modal('show');
$('#rejectid').val(id);
// $('#exampleModalCenterdata'+mid).modal('show');
// $('#exampleModalCenterdata'+mid+' #rejectid').val(id);
}
</script>
<!-- /.row (main row) -->
</div>
<!-- /.container-fluid -->
<!-- </section> -->
<!-- /.content -->
</div>
<!-- /.content-wrapper -->
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
$( document ).ready(function() {
var compcount = $("#compcount").val();

for(var i = 1; i <= compcount; i++)
{
var str = 'exampledata'+i+'_wrapper';
$("#exampledata"+i).DataTable({
"responsive": true, 
"lengthChange": true, 
"autoWidth": true,
"searching": true,
"ordering": true,
"info": true,
"paging": true,
"dom": 'Bfrtip', 
"buttons": [
'copy', 'csv', 'excel', 'pdf', 'print'
]
}).buttons().container().appendTo('#'+str+' .col-md-6:eq(0)');
}

});


$("#example1").DataTable({
"responsive": false, "lengthChange": false, "autoWidth": false,
"buttons": ["copy", "csv", "excel", "pdf", "print"]
}).buttons().container().appendTo('#example1_wrapper .col-md-6:eq(0)');
$("#examplereview1").DataTable({
"responsive": false, "lengthChange": false, "autoWidth": false,
"buttons": ["copy", "csv", "excel", "pdf", "print"]
}).buttons().container().appendTo('#examplereview1_wrapper .col-md-6:eq(0)');

$("#ourreview").DataTable({
"responsive": false, "lengthChange": false, "autoWidth": false,
"buttons": ["copy", "csv", "excel", "pdf", "print"]
}).buttons().container().appendTo('#ourreview_wrapper .col-md-6:eq(0)');
</script>
</body>
</html>
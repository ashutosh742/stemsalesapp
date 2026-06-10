<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>STEM Operation | WebAPP</title>

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
    <section class="content">
      <div class="container-fluid">
       <div class="row p-3">
           
           
     <div class="col-sm col-md-6 col-lg-6 m-auto">
         <div class="card card-primary card-outline">
              <div class="card-body box-profile">
                  <h4 class="text-center">Target Setting</h4>
                  <hr>
                    <div class="was-validated">
                    <div class="form-group">
                        <input type="hidden" name="uidaa" value="<?=$uid?>">
                        <?php date_default_timezone_set("Asia/Kolkata"); ?>
                        <input type="hidden" id="bdid" value="<?=$uid?>">
                        <input type="hidden" id="fdate" value="2023-04-01">
                        
                        
                        <div class="mt-4">
                            <select class="form-control" id="categories" required="">
                                <option value="0">All</option>
                                <option value="keycompany">Key Client</option>
                                <option value="pkclient">Positive Key Client</option>
                                <option value="focus_funnel">Focus Funnel</option>
                                <option value="topspender">Top Spender</option>
                                <option value="Review">Review</option>
                            </select>
                            <div class="invalid-feedback">Please Create Plan First.</div>
                            <div class="valid-feedback">Looks good!</div>
                        </div>
                        
                        <div class="mt-4">
                            <select class="form-control" id="statusid" name="statusid" required="">
                                <option value="">Select Status</option>
                                <option value="1">OPEN</option>
                                <option value="8">OPEN[RPEM]</option>
                                <option value="2">Reachout</option>
                                <option value="10">TTD-Reachout</option>
                                <option value="11">WNO-Reachout</option>
                                <option value="12">Positive-NAP</option>
                                <option value="13">Very Positive-NAP</option>
                            </select>
                            <div class="invalid-feedback">Please Create Plan First.</div>
                            <div class="valid-feedback">Looks good!</div>
                        </div>
                        <form action="<?=base_url();?>Menu/tsetting" method="post">
                            <input type="hidden" name="bdid" value="<?=$uid?>">
                        <div class="mt-4">
                            <lable>note: showing only those companies which are not involved PST</lable>
                            <select class="form-control" name="inid" required="" id="inid">
                            </select>
                            <div class="invalid-feedback">Please Select Status First.</div>
                            <div class="valid-feedback">Looks good!</div>
                        </div>
                        <div class="mt-4">
                           <lable>Expected Status</lable>
                           <select class="form-control" id="exsid" name="exsid" required="">
                                <option value="">Select Status</option>
                                <?php $status = $this->Menu_model->get_status($uid);
                                foreach($status as $st){
                                ?>
                                <option value="<?=$st->id?>"><?=$st->name?></option>
                                <?php } ?>
                            </select>
                        </div>
                        <div class="mt-4">
                            <lable>Expected Date</lable>
                            <input type="date" id="exdate" name="exdate" value="" class="form-control" required="" min="<?=date('Y-m-d');?>">
                        </div>
                        <div class="mt-4">
                            <div class="form-group text-center mt-3">
                                <button type="submit" class="btn btn-success" onclick="this.form.submit(); this.disabled = true;">Submit</button>
                            </div>
                        </div>
                        </form>
                    </div>
              </div>
              
              
         </div>
     </div></div>
     <div class="col-sm col-md-12 col-lg-12 m-auto">
         <div class="card card-primary card-outline">
              <div class="card-body box-profile">
                  <div class="table-responsive">
                            <div class="table-responsive">
                                <table id="example1" class="table table-striped table-bordered" cellspacing="0" width="100%">
                                    <thead>
                                        <tr>								
                                            <th>SNO</th>
                                            <th>Updated By</th>
                                            <th>Assign Date</th>
                                            <th>Updated Date</th>
                                            <th>Current Action</th>
                                            <th>Action Taken</th>
                                            <th>Purpose Achieved</th>
                                            <th>Remarks</th>
                                            <th>MOM</th>
                                            <th>Last Status</th>
                                            <th>Current Status</th>
                                            <th>Next Status</th>
                                        </tr>
                                    </thead>
                                
                                <tbody id="cmplogs">
                                    
                                </tbody>
                                </table>
                            </div>
                        </div>
              </div>
     </div></div>
    </section>
  
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
<script type='text/javascript'>


$('#statusid').on('change', function b() {
var stid = this.value;
var bdid = document.getElementById("bdid").value;
$.ajax({
url:'<?=base_url();?>Menu/getbdcmpts',
type: "POST",
data: {
stid: stid,
bdid: bdid
},
cache: false,
success: function a(result){
$("#inid").html(result);
}
});
});


$('#categories').on('change', function b() {
var categories = this.value;
var bdid = document.getElementById("bdid").value;
$.ajax({
url:'<?=base_url();?>Menu/getcmptscat',
type: "POST",
data: {
categories : categories,
bdid : bdid
},
cache: false,
success: function a(result){
$("#inid").html(result);
}
});
});


$('#inid').on('change', function b() {
var inid = this.value;
var fdate = document.getElementById("fdate").value;
$.ajax({
url:'<?=base_url();?>Menu/getcmplogs',
type: "POST",
data: {
inid: inid,
fdate: fdate
},
cache: false,
success: function a(result){
$("#cmplogs").html(result);
}
});
});



</script>

          
        <!-- /.row (main row) -->
      </div><!-- /.container-fluid -->
    </section>
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
    $("#example1").DataTable({
      "responsive": false, "lengthChange": false, "autoWidth": false,
      "buttons": ["copy", "csv", "excel", "pdf", "print", "colvis"]
    }).buttons().container().appendTo('#example1_wrapper .col-md-6:eq(0)');
</script>
</body>
</html>
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
                <div class="modal-body">
                    <div class="card card-form col-md-12">
                        <div class="card-header bg-info text-center"><b>Outstation Travel Expenses</b></div>
                                <lable>How Many Bargin</lable>
                            <input type="number" name="tphmlocation" id="tphmlocation" class="form-control mt-2"><hr>
                            </div></div>
                            <div class="accordion" id="accordionExample">
                              <div class="card">
                                <div class="card-header" id="heading1">
                                  <h2 class="mb-0">
                                    <button class="btn btn-link" type="button" data-toggle="collapse" data-target="#tpcollapse1" aria-expanded="true" aria-controls="tpcollapse1">
                                      TYPE OF EXPENSES : Train
                                      <input type="hidden" name="type[]" value="Train">
                                    </button>
                                  </h2>
                                </div>
                            
                                <div id="tpcollapse1" class="collapse show" aria-labelledby="heading1" data-parent="#accordionExample">
                                  <div class="card-body">
                                    <input type="text" class="form-control" placeholder="DESCRIPTION OF EXPENSES">
                                    <input type="text" class="form-control" placeholder="DAILY EXPENSES">
                                    <input type="text" class="form-control" placeholder="NO OF DAYS">
                                   </div>
                                </div>
                              </div>
                              <div class="card">
                                <div class="card-header" id="heading2">
                                  <h2 class="mb-0">
                                    <button class="btn btn-link collapsed" type="button" data-toggle="collapse" data-target="#tpcollapse2" aria-expanded="false" aria-controls="tpcollapse2">
                                      TYPE OF EXPENSES : Taxi
                                      <input type="hidden" name="type[]" value="Taxi">
                                    </button>
                                  </h2>
                                </div>
                                <div id="tpcollapse2" class="collapse" aria-labelledby="heading2" data-parent="#accordionExample">
                                  <div class="card-body">
                                    <input type="text" class="form-control" placeholder="DESCRIPTION OF EXPENSES">
                                    <input type="text" class="form-control" placeholder="DAILY EXPENSES">
                                    <input type="text" class="form-control" placeholder="NO OF DAYS">
                                  
                                  </div>
                                </div>
                              </div>
                              <div class="card">
                                <div class="card-header" id="heading3">
                                  <h2 class="mb-0">
                                    <button class="btn btn-link collapsed" type="button" data-toggle="collapse" data-target="#tpcollapse3" aria-expanded="false" aria-controls="tpcollapse3">
                                      CTYPE OF EXPENSES : Bus
                                      <input type="hidden" name="type[]" value="Train">
                                    </button>
                                  </h2>
                                </div>
                                <div id="tpcollapse3" class="collapse" aria-labelledby="heading3" data-parent="#accordionExample">
                                  <div class="card-body">
                                    <input type="text" class="form-control" placeholder="DESCRIPTION OF EXPENSES">
                                    <input type="text" class="form-control" placeholder="DAILY EXPENSES">
                                    <input type="text" class="form-control" placeholder="NO OF DAYS">
                                  
                                  </div>
                                </div>
                              </div>
                              <div class="card">
                                <div class="card-header" id="heading4">
                                  <h2 class="mb-0">
                                    <button class="btn btn-link collapsed" type="button" data-toggle="collapse" data-target="#tpcollapse4" aria-expanded="false" aria-controls="tpcollapse4">
                                      CTYPE OF EXPENSES : Tender Amount
                                      <input type="hidden" name="type[]" value="Train">
                                    </button>
                                  </h2>
                                </div>
                                <div id="tpcollapse4" class="collapse" aria-labelledby="heading4" data-parent="#accordionExample">
                                  <div class="card-body">
                                    <input type="text" class="form-control" placeholder="DESCRIPTION OF EXPENSES">
                                    <input type="text" class="form-control" placeholder="DAILY EXPENSES">
                                    <input type="text" class="form-control" placeholder="NO OF DAYS">
                                  
                                  </div>
                                </div>
                              </div>
                              <div class="card">
                                <div class="card-header" id="heading5">
                                  <h2 class="mb-0">
                                    <button class="btn btn-link collapsed" type="button" data-toggle="collapse" data-target="#tpcollapse5" aria-expanded="false" aria-controls="tpcollapse5">
                                      CTYPE OF EXPENSES : Ground Transportation
                                      <input type="hidden" name="type[]" value="Train">
                                    </button>
                                  </h2>
                                </div>
                                <div id="tpcollapse5" class="collapse" aria-labelledby="heading5" data-parent="#accordionExample">
                                  <div class="card-body">
                                    <input type="text" class="form-control" placeholder="DESCRIPTION OF EXPENSES">
                                    <input type="text" class="form-control" placeholder="DAILY EXPENSES">
                                    <input type="text" class="form-control" placeholder="NO OF DAYS">
                                  
                                  </div>
                                </div>
                              </div>
                              <div class="card">
                                <div class="card-header" id="heading6">
                                  <h2 class="mb-0">
                                    <button class="btn btn-link collapsed" type="button" data-toggle="collapse" data-target="#tpcollapse6" aria-expanded="false" aria-controls="tpcollapse6">
                                      CTYPE OF EXPENSES : Lodging
                                      <input type="hidden" name="type[]" value="Train">
                                    </button>
                                  </h2>
                                </div>
                                <div id="tpcollapse6" class="collapse" aria-labelledby="heading6" data-parent="#accordionExample">
                                  <div class="card-body">
                                    <input type="text" class="form-control" placeholder="DESCRIPTION OF EXPENSES">
                                    <input type="text" class="form-control" placeholder="DAILY EXPENSES">
                                    <input type="text" class="form-control" placeholder="NO OF DAYS">
                                  
                                  </div>
                                </div>
                              </div>
                              <div class="card">
                                <div class="card-header" id="heading7">
                                  <h2 class="mb-0">
                                    <button class="btn btn-link collapsed" type="button" data-toggle="collapse" data-target="#tpcollapse7" aria-expanded="false" aria-controls="tpcollapse7">
                                      CTYPE OF EXPENSES : Food
                                      <input type="hidden" name="type[]" value="Train">
                                    </button>
                                  </h2>
                                </div>
                                <div id="tpcollapse7" class="collapse" aria-labelledby="heading7" data-parent="#accordionExample">
                                  <div class="card-body">
                                    <input type="text" class="form-control" placeholder="DESCRIPTION OF EXPENSES">
                                    <input type="text" class="form-control" placeholder="DAILY EXPENSES">
                                    <input type="text" class="form-control" placeholder="NO OF DAYS">
                                  
                                  </div>
                                </div>
                              </div>
                              <div class="card">
                                <div class="card-header" id="heading8">
                                  <h2 class="mb-0">
                                    <button class="btn btn-link collapsed" type="button" data-toggle="collapse" data-target="#tpcollapse8" aria-expanded="false" aria-controls="tpcollapse8">
                                      CTYPE OF EXPENSES : Miscellaneious
                                      <input type="hidden" name="type[]" value="Train">
                                    </button>
                                  </h2>
                                </div>
                                <div id="tpcollapse8" class="collapse" aria-labelledby="heading8" data-parent="#accordionExample">
                                  <div class="card-body">
                                     <input type="text" class="form-control" placeholder="DESCRIPTION OF EXPENSES">
                                    <input type="text" class="form-control" placeholder="DAILY EXPENSES">
                                    <input type="text" class="form-control" placeholder="NO OF DAYS">
                                  </div>
                                </div>
                              </div>
                            </div>
                            
                            </div></div>
                            <div class="card-footer">
                           <button type="submit" class="btn btn-primary" onclick="this.form.submit(); this.disabled = true;">Submit</button>
                           </div>
                           </form>
            <!--END OF FORM ^^-->
            </fieldset>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
<script type="text/javascript">

$('#tphmlocation').on('change', function a() {
    var tphl = this.value;
    for(var i=1;i<tphl;i++){
      document.getElementById("headshow").html="Day"+i;
      var tpscity = document.getElementById("tpscity");
      var scload = document.getElementById("scload");
      tpscity.appendChild(scload.cloneNode(true));
      
    }
});


$('#tpstate').on('change', function f() {
    var stid = this.value;
    $.ajax({
    url:'<?=base_url();?>Menu/getcity',
    type: "POST",
    data: {
    stid: stid
    },
    cache: false,
    success: function a(result){
    $("#tpcity").html(result);
    }
    });
});
</script>          
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
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="wi$dth=device-wi$dth, initial-scale=1">
  <title>STEM HR APP</title>
<link href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.5.2/css/bootstrap.css">
<link href="https://cdn.datatables.net/1.11.3/css/dataTables.bootstrap4.min.css">
<link href="https://cdn.datatables.net/buttons/2.1.0/css/buttons.bootstrap4.min.css">
<script src="https://code.jquery.com/jquery-3.5.1.js"></script>
<script src="https://cdn.datatables.net/1.11.3/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.11.3/js/dataTables.bootstrap4.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.1.0/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.1.0/js/buttons.bootstrap4.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
<script src="https://cdn.datatables.net/buttons/2.1.0/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.1.0/js/buttons.print.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.1.0/js/buttons.colVis.min.js"></script>
<link href="https://demo.dashboardpack.com/architectui-html-free/main.css" rel="stylesheet">

<!------------>

  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.0/css/all.min.css">
  <!-- Font Awesome -->
  <link rel="stylesheet" href="https://stemlearning.in/opp/assets/css/all.min.css">
  <!-- Ionicons -->
  <link rel="stylesheet" href="https://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css">
  <!-- Tempusdominus Bootstrap 4 -->
  <link rel="stylesheet" href="https://stemlearning.in/opp/assets/css/tempusdominus-bootstrap-4.min.css">
  <!-- iCheck -->
  <link rel="stylesheet" href="https://stemlearning.in/opp/assets/css/icheck-bootstrap.min.css">
  <!-- JQVMap -->
  <link rel="stylesheet" href="https://stemlearning.in/opp/assets/css/jqvmap.min.css">
  <!-- Theme style -->
  <link rel="stylesheet" href="https://stemlearning.in/opp/assets/css/adminlte.min.css">
  <!-- overlayScrollbars -->
  <link rel="stylesheet" href="https://stemlearning.in/opp/assets/css/OverlayScrollbars.min.css">
  <!-- Daterange picker -->
  <link rel="stylesheet" href="https://stemlearning.in/opp/assets/css/daterangepicker.css">
  <!-- summernote -->
  <link rel="stylesheet" href="https://stemlearning.in/opp/assets/css/summernote-bs4.min.css">
   <!-- DataTables -->
  <link rel="stylesheet" href="https://stemlearning.in/opp/assets/css/dataTables.bootstrap4.min.css">
  <link rel="stylesheet" href="https://stemlearning.in/opp/assets/css/responsive.bootstrap4.min.css">
  <link rel="stylesheet" href="https://stemlearning.in/opp/assets/css/buttons.bootstrap4.min.css">


<!------------------->


<link href="https://fonts.googleapis.com/css?family=Nunito:200,600" rel="stylesheet">
<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" >
    
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/gh/bbbootstrap/libraries@main/choices.min.css">
<script src="https://cdn.jsdelivr.net/gh/bbbootstrap/libraries@main/choices.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/tableexport.jquery.plugin@1.10.21/tableExport.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/tableexport.jquery.plugin@1.10.21/libs/jsPDF/jspdf.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/tableexport.jquery.plugin@1.10.21/libs/jsPDF-AutoTable/jspdf.plugin.autotable.js"></script>
<script src="https://unpkg.com/bootstrap-table@1.19.1/dist/bootstrap-table.min.js"></script>
<script src="https://unpkg.com/bootstrap-table@1.19.1/dist/extensions/export/bootstrap-table-export.min.js"></script>


<link href="//maxcdn.bootstrapcdn.com/bootstrap/4.1.1/css/bootstrap.min.css" rel="stylesheet" id="bootstrap-css">
<script src="//maxcdn.bootstrapcdn.com/bootstrap/4.1.1/js/bootstrap.min.js"></script>
<script src="//cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
<!------ Include the above in your HEAD tag ---------->

<!-- Datatable Dependency start -->
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/v/bs4/jszip-2.5.0/dt-1.10.20/b-1.6.1/b-colvis-1.6.1/b-html5-1.6.1/b-print-1.6.1/r-2.2.3/datatables.min.css" />
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.36/pdfmake.min.js"></script>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.36/vfs_fonts.js"></script>
<script type="text/javascript" src="https://cdn.datatables.net/v/bs4/jszip-2.5.0/dt-1.10.20/b-1.6.1/b-colvis-1.6.1/b-html5-1.6.1/b-print-1.6.1/r-2.2.3/datatables.min.js"></script>

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
            <h1 class="m-0">My Target</h1>
          </div><!-- /.col -->
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <h4> <?php $uid=$user['id']; $id = $user['type_id']; $did=$this->Menu_model->get_utype($id);  $did[0]->name;?> </h4>
            </ol>
          </div><!-- /.col -->
        </div><!-- /.row -->
      </div><!-- /.container-fluid -->
    </div>
    <!-- /.content-header -->
    <section class="content">
      <div class="container-fluid">
        <!-- Small boxes (Stat box) -->
        <div class="row p-3">
            <?php $ai=1;foreach($mytarget as $mr){?>
            <div class="card p-3 col-12">
                <div class="row p-3">
                    <div class="card p-3 col-6">
                        <?=$mr->type?> | <?=$mr->task?> | <?=$mr->tdatet?>
                    </div>
                    <div class="card p-3 col-2">
                        <?php if($mr->stdatet!=''){ echo "Started@ ".$mr->stdatet; } else{ ?>
                        <a href="MytargetStart/<?=$uid?>/<?=$mr->id?>"><button>Target Start</button></a>
                        <?php } ?>
                    </div>
                    <div class="card p-3 col-2">
                        <button id="close_meeting<?=$ai?>" value="<?=$mr->id?>">Target Close</button>
                    </div>
                    <div class="card p-3 col-2">
                        <button id="change_ttime<?=$ai?>" value="<?=$mr->id?>">Change Target Time</button>
                    </div>
                </div>
            </div>
            <?php $ai++;} ?>
        </div>
      </div>
    </section>   
  
  
<div id="doaction1" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="modal-standard-title" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modal-standard-title1"></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div> <!-- // END .modal-header -->
            <div class="modal-body">
                <div class="card card-form col-md-12">
                   <div class="card-header bg-info">Change Target Time Request</div>
                   <h6 class="text-center mt-1" id="cmpname"></h6>
                    <div class="col-lg-12 card-body">
                       <form action="<?=base_url();?>Menu/MytargetClose" method="post" enctype="multipart/form-data">
                           <div class="was-validated">
                       <input type="hidden" id="taskid" name="taskid">
                       <input type="file" name="attch[]" class="form-control" multiple required>
                       <div class="invalid-feedback">Please provide a File.</div>
                       <div class="valid-feedback">Looks good!</div>
                       <textarea class="form-control" name="mcremark" placeholder="Remark..." required></textarea>
                       <div class="invalid-feedback">Please provide a Remark.</div>
                       <div class="valid-feedback">Looks good!</div>
                       <button type="submit" class="btn btn-primary mt-3" onclick="this.form.submit(); this.disabled = true;">Submit</button>
                       </div>
                       </form>
                       
                    </div>
                    </div>
                        </div>
                    </div>
            </div>
</div>
<div id="doaction2" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="modal-standard-title" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modal-standard-title1"></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div> <!-- // END .modal-header -->
            <div class="modal-body">
                <div class="card card-form col-md-12">
                   <div class="card-header bg-info">Target Change Request</div>
                   <h6 class="text-center mt-1" id="cmpname"></h6>
                    <div class="col-lg-12 card-body">
                       <?=form_open('Menu/Mytargetchange')?>
                       <input type="hidden" id="ttaskid" name="ttaskid">
                       <input type="datetime-local" name="ttchange" class="form-control">
                       <textarea class="form-control" name="cremark" placeholder="Remark..."></textarea>
                       <button type="submit" class="btn btn-primary mt-3" onclick="this.form.submit(); this.disabled = true;">Submit</button>
                       </form>
                    </div>
                    </div>
                        </div>
                    </div>
            </div>
</div></div></div>

    
<script type='text/javascript'>

$('[id^="close_meeting"]').on('click', function() {
    var tid = this.value;
    $('#doaction1').modal('show');
    document.getElementById("taskid").value = tid;
});

$('[id^="change_ttime"]').on('click', function() {
    var tid = this.value;
    $('#doaction2').modal('show');
    document.getElementById("ttaskid").value = tid;
    
});
</script>             
            
        <!-- /.row (main row) -->
      </div><!-- /.container-fluid -->
    </section>
    
    </div></div></div>

    
    
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
<!-- AdminLTE App -->
<script src="<?=base_url();?>assets/js/adminlte.js"></script>

<!-- AdminLTE dashboard demo (This is only for demo purposes) -->
<script src="<?=base_url();?>assets/js/dashboard.js"></script>

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


<script>
    $("#example1").DataTable({
      "responsive": false, "lengthChange": false, "autoWidth": false,
      "buttons": ["excel", "pdf"]
    }).buttons().container().appendTo('#example1_wrapper .col-md-6:eq(0)');
</script>
</body>
</html>
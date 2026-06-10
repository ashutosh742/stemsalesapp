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
    <form class="p-3" method="POST" action="<?=base_url();?>/Menu/MeetingDetail/1/<?=$uid?>">
        <input type="date" name="sdate" class="mr-2" value="<?=$sd?>">
        <input type="date" name="edate" class="mr-2" value="<?=$ed?>">
        <button type="submit" class="bg-primary text-light">Filter</button>
    </form>
    <!-- Main content -->
    <section class="content">
      <div class="container-fluid">
          <div class="card col-12 p-3 text-center">
              <h4>Task Wise and Status Wise – Combination</h4><hr>
             <div class="row">
                 <div class="col-lg-4 col-md-6 col-sm p-3 bg-info rounded border">
                     <b>Prospecting</b><hr>
                     Open – Open (RPEM)<br>
                     <b>20</b><hr>
                     Research<br>
                     <b>5</b><hr>
                     Calls<br>
                     <b>15</b><hr>
                     Mails<br>
                     <b>0</b><hr>
                 </div>
                 <div class="col-lg-4 col-md-6 col-sm p-3 bg-info rounded border">
                     <b>Pitching</b><hr>
                     Open (RPEM) – Reach out<br>
                     <b>20</b><hr>
                     Reach out – Tentative<br>
                     <b>20</b><hr>
                     Research<br>
                     <b>5</b><hr>
                     Calls<br>
                     <b>15</b><hr>
                     Barg ins<br>
                     <b>0</b><hr>
                 </div>
                 <div class="col-lg-4 col-md-6 col-sm p-3 bg-info rounded border">
                     <b>Presentation</b><hr>
                     Tentative – Positive<br>
                     <b>20</b><hr>
                     Tentative – Very Positive<br>
                     <b>20</b><hr>
                     Sending Proposals<br>
                     <b>5</b><hr>
                     Apr in One Time<br>
                     <b>15</b><hr>
                     Apr in More Then One Time<br>
                     <b>0</b><hr>
                 </div>
                 <div class="col-lg-4 col-md-6 col-sm p-3 bg-info rounded border">
                     <b>Objection Handling</b><hr>
                     Positive NAP<br>
                     <b>20</b><hr>
                     Very Positive NAP<br>
                     <b>20</b><hr>
                     Calls<br>
                     <b>15</b><hr>
                     Mails<br>
                     <b>0</b><hr>
                     Meetings<br>
                     <b>0</b><hr>
                 </div>
                 <div class="col-lg-4 col-md-6 col-sm p-3 bg-info rounded border">
                     <b>Closing/ Closing Support</b><hr>
                     Positive NAP - Closure<br>
                     <b>20</b><hr>
                     Very Positive NAP - Closure<br>
                     <b>20</b><hr>
                     Calls<br>
                     <b>15</b><hr>
                     Mails<br>
                     <b>0</b><hr>
                     Meetings<br>
                     <b>0</b><hr>
                 </div>
                 <div class="col-lg-4 col-md-6 col-sm p-3 bg-info rounded border">
                     <b>Closure</b><hr>
                     Closure<br>
                     <b>20</b><hr>
                     On Boarding<br>
                     <b>20</b><hr>
                     Calls<br>
                     <b>15</b><hr>
                     Mails<br>
                     <b>0</b><hr>
                 </div>
              </div>
          </div>
      </div>
    </section>             
                                    
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
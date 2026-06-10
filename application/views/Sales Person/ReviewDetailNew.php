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
      form.p-3 {
      display: flex;
      width: 75%;
      }
      input.form-control {
      margin: 2px;
      }
      select.form-control {
      margin: 2px;
      }
      button.btn.btn-primary.text-light {
      margin: 3px;
      }
      .card-body {
    background: beige;
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

      <?php 
      $reviewtype = $mreviews[0]->reviewtype;
      $revuid = $mreviews[0]->uid;
      $bdid = $mreviews[0]->bdid;
      $revuser1 = '';
      if($revuid == $bdid){
        $revudetail = $this->Menu_model->get_userbyid($revuid);
        $revuser1 .= $revudetail[0]->name;
      }else{
        $revudetail1 = $this->Menu_model->get_userbyid($revuid);
        $revuser1 .= $revudetail1[0]->name;
        $revuser1  .= ' | ';
        $revudetail2 = $this->Menu_model->get_userbyid($revuid);
        $revuser1 .= $revudetail2[0]->name;
      }
      ?>
        <div class="content-header">
          <div class="container-fluid">
            <div class="row">
              <div class="col-sm-12 card bg-primary text-center p-2">
                <h1>Review Details</h1>
                <h4><?= $reviewtype; ?></h4>
                <h5><?= $revuser1; ?></h5>
              </div>
              <!-- /.col -->
            </div>
            <!-- /.row -->
          </div>
          <!-- /.container-fluid -->
        </div>
        <?php //
          $status = $this->Menu_model->get_status();
        ?>
        <section class="content">
          <div class="container-fluid">
            <div class="row">
              <div class="col-md-12">
                <div class="card">
                  <div class="card-body">
                      <div class="table-responsive">
                            <table id="example1" class="table table-striped table-bordered" cellspacing="0" width="100%">
                              <thead>
                                <tr>
                               <?php
                                foreach($status as $s){
                                    if($s->id != 7 && $s->id != 14){ ?>
                                        <th><?= $s->name;  ?></th>
                                    <?php }} ?>
                                </tr>
                              </thead>
                              <tbody>
                                <tr>
                                    <?php 
                                    foreach ($mdata as $item) {
                                        foreach ($item as $key => $value) {
                                            if (preg_match('/_(\d+)$/', $key, $matches)) {
                                                $key_id = (int)$matches[1];
                                            }
                                            ?>
                                           <td><a href="<?=base_url();?>Menu/ReviewDetailNewDetails/<?=$rev_id; ?>/<?= $key_id; ?>"><?= $value; ?></a></td>
                                      <?php  }
                                    }
                                    ?>
                                </tr>
                               
                              </tbody>
                            </table>    
                      </div>
                  </div>
                </div>
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
    <script src="<?=base_url();?>assets/js/jquery.min.js"></script>
    <script src="<?=base_url();?>assets/js/jquery-ui.min.js"></script>
    <script>
      $.widget.bridge('uibutton', $.ui.button);
      
$( document ).ready(function() {
    $("#teamuser").hide();
});
      $('#adminvalue').change(function() {
        var selectedValue = $(this).val(); // Get the selected value
        $.ajax({
            url:'<?=base_url();?>Menu/GetAdminReviewUser',
            type: "POST",
            data: {
            admin_id: selectedValue
            },
            cache: false,
            success: function a(result){
                $("#teamuser").html(result);
                $("#teamuser").show();
                var optionCount = $('#teamuser').find('option').length;
                optionCount = optionCount-1;
                }
            });
      });
      
      
      
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
        "buttons": ["csv", "excel"]
      }).buttons().container().appendTo('#example1_wrapper .col-md-6:eq(0)');
    </script>
  </body>
</html>
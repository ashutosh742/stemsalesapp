<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>All Active User By Role | STEM APP | WebAPP</title>
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

        <!-- /.content-header -->
        <div class="container-fluid">
          <?php
            if ($this->session->flashdata('success_message')): ?>
          <div class="alert alert-success alert-dismissible fade show" role="alert">
            <strong> <?php echo $this->session->flashdata('success_message'); ?></strong>
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <?php endif; ?>
          <?php
            if ($this->session->flashdata('error_message')): ?>
          <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <strong> <?php echo $this->session->flashdata('error_message'); ?></strong>
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <?php endif; ?>
        </div>
        <!-- Main content -->
        <section class="content">
          <div class="container-fluid">
            <div class="row">
              <div class="col-12">
                <div class="card">
                  <div class="card-body">
                    <div class="container-fluid body-content">
                      
                      <div class="row">
                        <div class="col-md-12">
                          <div class="card text-center">
                            <div class="card-header">
                              <h3 style="color: #2400fa;text-transform: uppercase;">User Details</h3>
                            </div>
                          </div>
                        </div>
                      </div>
                      <div class="page-header">
                        <div class="form-group">
                          <div class="table-responsive">
                            <table id="example1" class="table table-striped table-bordered" cellspacing="0" width="100%">
                              <thead>
                                <tr>
                                  <th>SL.#</th>
                                  <th>Name</th>
                                  <th>Email</th>
                                  <th>Phone No.</th>
                                  <th>Username</th>
                                  <th>DOJ</th>
                                  <th>Type</th>
                                  <th>User ID</th>
                                  <th>Zone</th>
                                  <th>Admin</th>
                                  <th>Sales-Coordinator</th>
                                  <th>PST</th>
                                  <th>Status</th>
                                  <th>Inside</th>
                                  <th>Admin-Manager</th>
                                  <th>Action</th>
                                </tr>
                              </thead>
                              <tbody>
                                <?php $i = 1;
                                  foreach($userDetails as $val){?>
                                <tr>
                                  <td><?=$i?></td>
                                  <td><a href="<?=base_url();?>Menu/UserProfile/<?=$val->user_id?>"><?=$val->name?></a></td>
                                  <td><?=$val->email?></td>
                                  <td><?=$val->phoneno?></td>
                                  <td><?=$val->username?></td>
                                  <td><?=$val->usercreateDate?></td>
                                  <td><?=$val->type_name?></td>
                                  <td><?=$val->user_id?></td>
                                  <td><?=$val->zone_name?></td>
                                  <td><?=$val->admin_name?></td>
                                  <td><?=$val->sales_co_name?></td>
                                  <td><?=$val->pst_co_name?></td>
                                  <td><?=$val->status == 'active' ? 'Active' : 'Inactive'?></td>
                                  <td><?=$val->inside == 1 ? 'Yes' : 'No'?></td>
                                  <td><?=$val->aadmin_name?></td>
                                  <td>
                                    <!-- Action buttons or links can go here -->
                                    <a href="<?=base_url()?>Menu/EditUserInformationsAndMapping/<?=$val->user_id?>" class="btn btn-primary">Edit</a>
                                    <a href="<?=base_url()?>Menu/UserDeleteAction/<?=$val->user_id?>" class="btn btn-danger" onclick="return confirmDelete()">Delete</a>
                                  </td>
                                </tr>
                                <?php $i++;} ?>
                              </tbody>
                            </table>
                          </div>
                        </div>
                        <hr />
                      </div>
                    </div>
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
    <div id="dcompany" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="modal-standard-title" aria-hidden="true">
      <div class="modal-dialog" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="modal-standard-title1"></h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <!-- // END .modal-header -->
        </div>
      </div>
      <!-- // END .modal-body -->
    </div>
    </div></div>
    </div></div>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
    <script>
      function confirmDelete() {
          return confirm("Are you sure you want to delete this data?");
      }
    </script>
    <script type="text/javascript">
      $('[id^="add_Rremark"]').on('click', function() {
      var cmpid = this.value;
      $('#doaction').modal('show');
      document.getElementById("cmpid").value = cmpid;
      });
      $('[id^="DeleteC"]').on('click', function() {
      var cmpid = this.value;
      $('#dcompany').modal('show');
      document.getElementById("cmpida").value = cmpid;
      });
      $('#ntaction').on('change', function f() {
      var sid = document.getElementById("ntstatus").value;
      var aid = document.getElementById("ntaction").value;
      $.ajax({
      url:'<?=base_url();?>Menu/getpurpose',
      type: "POST",
      data: {
      sid: sid,
      aid: aid
      },
      cache: false,
      success: function a(result){
      $("#ntppose").html(result);
      }
      });
      });
      $('#ntppose').on('change', function f() {
      var pid = document.getElementById("ntppose").value;
      $.ajax({
      url:'<?=base_url();?>Menu/getnextaction',
      type: "POST",
      data: {
      pid: pid
      },
      cache: false,
      success: function a(result){
      $("#nextaction").html(result);
      }
      });
      });
    </script>
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
      "buttons": ["csv", "excel", "pdf"]
      }).buttons().container().appendTo('#example1_wrapper .col-md-6:eq(0)');
    </script>
  </body>
</html>
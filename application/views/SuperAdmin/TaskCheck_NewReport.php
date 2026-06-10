<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="wi$dth=device-wi$dth, initial-scale=1">
    <title>TaskCheck Report | STEM APP | WebAPP</title>
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
        <!-- Main content -->
        <section class="content">
          <div class="container-fluid">
            <div class="row">
              <div class="col-12">
                <div class="card">
                  <div class="card-header">
                    <h3 class="text-center">
                      <center><b>Task Check Management Report</b></center>
                    </h3>
                    <div class="card-body FilterSection">
                        <form method="POST" action="<?= base_url(); ?>Menu/TaskCheck_NewReport">
                            <div class="row">
                                <div class="col-lg-3 col-sm-6">
                                    <label for="startDate">Start Date</label>
                                    <input id="startDate" name="startDate" class="form-control" type="date"
                                        value="<?= $sdate ?>" />
                                </div>
                                <div class="col-lg-3 col-sm-6">
                                    <label for="endDate">End Date</label>
                                    <input id="endDate" class="form-control" name="endDate" type="date"
                                        value="<?= $edate ?>" />
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>Select User</label>
                                        <select class="custom-select rounded-0" name="user[]" id="user" required>
                                            <option value="select_all">Select All</option>
                                            <?php foreach ($users as $users) { ?>
                                                <option value="<?= $users->user_id ?>" <?= in_array($users->user_id, $selected_user) ? 'selected' : '' ?>>
                                                    <?= htmlspecialchars($users->name) ?></option>
                                                <!-- <option value="<?= $users->user_id ?>"><?= $users->name ?></option> -->
                                            <?php } ?>
                                        </select>
                                    </div>
                                </div>
                                <!-- Partner Type -->                                    
                                <!-- Cluster -->
                                
                                <!--Cluster Users -->
                                
                                <div class="col-lg-3 col-sm-6">
                                    <br>
                                    <button type="submit" name="submit" class="btn btn-primary"> Filter</button>
                                </div>
                            </div>
                        </form>
                    </div>
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
                                            <th>Name</th>
                                            <th>Date</th>
                                            <th>Action</th>
                                            <th>Company Name</th>
                                            <th>Action & Purpose</th>
                                            <th>Initiate Time</th>
                                            <th>Updated Time</th>
                                            <th>Status Change</th>
                                            <!-- <th>Period</th> -->
                                            <th>Question</th>
                                            <th>Rating</th>
                                            <th>Remark</th>
                                            <th>Feedback By</th>

                                        </tr>
                                    </thead>
                                    <tbody>
                                    <?php 
                                      $i=1;                                      
                                        foreach($getReportbyUser as $singleReport){ ?>
                                        <tr>
                                            <td><?=$i?></td>
                                            <td><?=$singleReport->userName?></td>
                                            <td><?=$singleReport->date?></td>
                                            <td><?=$singleReport->actionName?></td>
                                            <td><?=$singleReport->compname?></td>
                                            <td>
                                                <b>Action :</b> <?=$singleReport->actontaken?>
                                                <b>Purpose :</b> <?=$singleReport->purpose_achieved?>
                                            </td>
                                            <td><?=$singleReport->initiateddt?></td>
                                            <!-- <td><?=$singleReport->initiateddt?></td> -->
                                            <td><?=$singleReport->updateddate?></td>
                                            <td>
                                                <b><?=$singleReport->newStatus?></b> To <b><?=$singleReport->oldStatus?></b>
                                            </td>
                                            <td>
                                                <?=$singleReport->question?>
                                            </td>
                                            <!-- <td><?=$singleReport->updateddate?></td> -->

                                            <td>
                                            <?=$singleReport->star?>
                                            <?php   
                                                $totalStars = 5;
                                                for($j = 0; $j < $singleReport->star; $j++) {
                                                    echo "<i class='fas fa-star' style='color:#f39c12;'></i>"; // filled star
                                                }
                                                for ($j = $singleReport->star; $j < $totalStars; $j++) {
                                                    echo "<i class='far fa-star'></i>"; // empty star
                                                } ?>
                                            </td>
                                            <!-- <td><?=$singleReport->star?></td> -->

                                            <td><?=$singleReport->remarks?></td>

                                            <td><?=$singleReport->feedbackBy?></td>
                                        </tr>
                                        <?php $i++;} ?>
                                    </tbody>
                                </table>
                              </div>
                            </div>
                            </form>            <!--END OF FORM ^^-->
                        </fieldset>
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
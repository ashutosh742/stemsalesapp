<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
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
      body {
          font-family: 'Source Sans Pro', sans-serif;
          background-color: #f8f9fa;
      }

      .content-wrapper {
          background-color: #ffffff;
          box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
          border-radius: 10px;
          margin: 20px;
          padding: 20px;
      }

      .card {
          box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
          border: none;
          border-radius: 10px;
          overflow: hidden;
      }

      .card-header {
          background-color: #6c757d;
          color: #ffffff;
          padding: 15px;
          border-bottom: none;
      }

      .card-body {
          padding: 20px;
      }

   

      .table thead th {
          background-color: #343a40;
          color: #ffffff;
      }

      .table tbody tr:nth-child(even) {
          background-color: #f8f9fa;
      }

      .table tbody tr:hover {
          background-color: #e9ecef;
      }

      .btn-primary {
          background-color: #007bff;
          border-color: #007bff;
      }

      .btn-primary:hover {
          background-color: #0069d9;
          border-color: #0062cc;
      }

      .form-control {
          border-radius: 5px;
          border: 1px solid #ced4da;
      }

      .page-header {
          margin-bottom: 20px;
      }

      .page-header h1 {
          color: #343a40;
      }

      footer {
          background-color: #343a40;
          color: #ffffff;
          padding: 10px;
          text-align: center;
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
            <h1 class="m-0 text-dark">Proposal Details</h1>
          </div><!-- /.col -->
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="#">Home</a></li>
              <li class="breadcrumb-item active">Proposal Details</li>
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
                <h3 class="card-title">Filter Proposals</h3>
              </div>
              <!-- /.card-header -->
              <div class="card-body">
                <form class="p-3" method="POST" action="<?=base_url();?>/Menu/ProposalDetailbydate">
                  <div class="form-row">
                    <div class="form-group col-md-3">
                      <label for="sdate">Start Date</label>
                      <input type="date" name="sdate" class="form-control" value="<?=$sd?>">
                    </div>
                    <div class="form-group col-md-3">
                      <label for="edate">End Date</label>
                      <input type="date" name="edate" class="form-control" value="<?=$ed?>">
                    </div>
                    <div class="form-group col-md-3">
                      <label for="aprtype">Approval Type</label>
                      <select name="aprtype" class="form-control">
                        <option value="1">Approved</option>
                        <option value="2">Rejected</option>
                        <option value="0">Waiting</option>
                      </select>
                    </div>
                    <div class="form-group col-md-3">
                      <label>&nbsp;</label>
                      <button type="submit" class="btn btn-primary btn-block">Filter</button>
                    </div>
                  </div>
                </form>
              </div>
              <!-- /.card-body -->
            </div>
            <!-- /.card -->

            <div class="card">
              <div class="card-header">
                <h3 class="card-title">Proposal Details</h3>
              </div>
              <!-- /.card-header -->
              <div class="card-body">
                <div class="table-responsive text-nowrap">
                  <table id="example1" class="table table-striped table-bordered" cellspacing="0" width="100%">
                    <thead class="thead-dark">
                      <tr>
                        <th>S.No.</th>
                        <th>Request Date</th>
                        <th>Apr/Reject Date</th>
                        <th>Status</th>
                        <th>CID</th>
                        <th>Company Name</th>
                        <th>BD Name</th>
                        <th>PST Name</th>
                        <th>Propos Partner</th>
                        <th>No of School</th>
                        <th>Amount</th>
                        <th>Remark</th>
                        <th>Link</th>
                      </tr>
                    </thead>
                    <tbody>
                      <?php $i=1;
                      foreach($mdata as $dt){?>
                      <tr>
                        <td><?=$i?></td>
                        <td><?=$dt->sdatet?></td>
                        <td><?=$dt->aprdatet?></td>
                        <td><?php if($dt->apr==1){echo 'Approved';}else{if($dt->apr==0){echo 'Waiting';}else{echo 'Rejected';}}?></td>
                        <td><?=$dt->cid?></td>
                        <td><a target="_blank" href="<?=base_url();?>Menu/CompanyDetails/<?=$dt->cid?>"><?=$dt->compname?></a></td>
                        <td><?=$dt->bdname?></td>
                        <td><?=$dt->pstname?></td>
                        <td><?=$dt->partner?></td>
                        <td><?=$dt->noofsc?></td>
                        <td><?=$dt->pbudgetme?></td>
                        <td><?=$dt->remark?></td>
                        <td><a href="<?=base_url();?><?=$dt->proattach?>" target="_blank">Link</a></td>
                      </tr>
                      <?php $i++;} ?>
                    </tbody>
                  </table>
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
      </div>
      <!-- /.container-fluid -->
    </section>
    <!-- /.content -->

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

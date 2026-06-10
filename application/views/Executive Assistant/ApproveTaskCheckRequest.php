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
<section class="content request_section">
    <div class="container-fluid">
        <div class="row p-3">
            <div class="col-sm col-md-12 col-lg-12 m-auto">
                <table class="table table-striped table-bordered" cellspacing="0" width="100%">
                    <thead>
                        <tr>
                            <th>Request By</th>
                            <th>Reason for Request</th>
                            <th>Requested At</th>
                            <th>Status</th>
                            <th>Approved By</th>
                            <th>Approved At</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                            $i=1; foreach($getRequests as $data):
                            
                        ?>
                        <tr>
                            <td><?=$data->request_by?></td>
                            <td><?=$data->reason?></td>
                            <td><?=$data->created_at?></td>
                            <td><?=$data->status?></td>
                            <td><?=$data->approved_by?></td>
                            <td><?=$data->approved_at?></td>
                            <td>
                                <?php 

                                    if (empty($data->status)) { ?>
                                        
                                        <button class="btn btn-success" onclick="handleAction('approve', <?=$data->id?>)">Approve</button>
                                        <button class="btn btn-danger" onclick="handleAction('reject', <?=$data->id?>)">Reject</button>
                                
                                <?php    }  ?>
                            </td>
                        </tr>
                        <?php $i++; endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
        </div>
</section>
                
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
                
                
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
    <script type="text/javascript">

    function handleAction(action, id) {

        var confirmation = false;

        if (action === 'approve') {
            confirmation = confirm("Are you sure you want to approve this request?");
        } else if (action === 'reject') {
            confirmation = confirm("Are you sure you want to reject this request?");
        }

        if (confirmation) {
            $.ajax({
                url: '<?=base_url();?>Menu/ApproveRequest',
                type: 'POST',
                data: {
                    action: action,
                    id: id
                },
                success: function(response) {

                    // alert(response.message);
                    // location.reload();

                },
                error: function(xhr, status, error) {
                    // Handle errors
                    alert('An error occurred: ' + error);
                }
            });
        }
    }
</script>

</body>
</html>
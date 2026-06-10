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
                <h1 class="m-0">Proposal Approval</h1>
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
                <input type="hidden" id="adid" value="<?=$uid?>">
                <!-- Main content -->
                <section class="content">
                    <div class="container-fluid">
                        <div class="row">
                            <div class="col-12">
                                <div class="card p-5">
                                  <?php 
                                  // echo "<pre>";print_r($mdata);
                                  ?>
                                    <?php foreach($mdata as $md){?>
                                    <div class="alert alert-light" role="alert" id="<?=$md->aprid?>">
                                        <b><?=$md->compname?> | <?=$md->name?> |
                                        <a href="<?=base_url();?><?=$md->proattach?>" target='_blank' class="text-primary">View</a> |
                                        <span onclick="Apr('<?=$md->aprid?>');" id="<?=$md->aprid?>">Apr</span> |
                                        <span onclick="Rej('<?=$md->aprid?>');" id="<?=$md->aprid?>">Reject</span>
                                        </b>
                                    </div>
                                    <?php } ?>
                                </div>
                            </div></div></div>
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
        <div id="aprrr" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="modal-standard-title" aria-hidden="true">
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
                                <div class="card-header bg-info">Add Reject Remark</div>
                                <div class="col-lg-12 card-body">
                                    <input type="hidden" id="aprrid">
                                    <input type="hidden" id="admid">
                                    <input type="hidden" id="aprr">
                                    <textarea rows="10" id="remark" class="form-control"></textarea><hr>
                                    <button type="submit" class="btn btn-primary mt-3" onclick="Rejwr();">Submit</button>
                                </div>
                            </div>
                        </div>
                    </div>
                    </div> <!-- // END .modal-body -->
                </div></div></div>
                <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
                <script type='text/javascript'>
                function Apr(aprid) {
                document.getElementById(aprid).style.display = "none";
                var adid = document.getElementById('adid').value;
                var apr = 1;
                $.ajax({
                url:'<?=base_url();?>Menu/ProApr',
                type: "POST",
                data: {
                aprid: aprid,
                adid: adid,
                apr: apr
                },
                cache: false,
                success: function b(result){
                $("#cinfo").html(result);
                }
                });
                }
                function Rej(aprid) {
                var adid = document.getElementById('adid').value;
                var apr = 2;
                $('#aprrr').modal('show');
                document.getElementById("aprrid").value = aprid;
                document.getElementById("admid").value = adid;
                document.getElementById("aprr").value = apr;
                }
                function Rejwr() {
                var aprid = document.getElementById("aprrid").value;
                var adid = document.getElementById("admid").value;
                var apr = document.getElementById("aprr").value;
                var remark = document.getElementById("remark").value;
                $.ajax({
                url:'<?=base_url();?>Menu/ProApr',
                type: "POST",
                data: {
                aprid: aprid,
                adid: adid,
                apr: apr,
                remark: remark
                },
                cache: false,
                success: function b(result){
                $("#cinfo").html(result);
                }
                });
                $('#aprrr').modal('hide');
                document.getElementById(aprid).style.display = "none";
                }
                </script>
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
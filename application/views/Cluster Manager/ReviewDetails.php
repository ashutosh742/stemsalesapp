<!DOCTYPE html>
        <html lang="en">
        <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>STEM APP| WebAPP</title>

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
                        <!--<form  action="<?=base_url();?>Menu/startAnnualReview" method="post">-->
                        <input type="hidden" name="user_id" value="<?=$user['user_id']?>">
                            <center> <h3>Funel Analysis</h3> </center>
                            <hr>
                            <div class="was-validated">
                                <div class="form-group">
                                    <div class="table-responsive">
                                        <table id="" class="table table-striped table-bordered" cellspacing="0" width="">
                                            <thead>
                                                <tr>							    <th width="10%">Sr No</th>
                                                    <th>Status</th>
                                                    <th>Review Done</th>
                                                    <th>Review Pending</th>
                                                    <th>Total Company</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php 
                                                $i=1; 
                                                foreach($totalcmp as $cd => $val){
                                                ?>
                                                <tr>
                                                    <td><?=$i++?></td>
                                                    <td><?=$this->Menu_model->get_statusbyid($cd)[0]->name?></td>
                                                    <td><?=$pending[$cd]?></td>
                                                    <td><?=$val - $pending[$cd]?></td>
                                                    <td><?=$val?></td>
                                                </tr>
                                                <?php }?>
                                            </tbody>
                                        </table>
                                    
                                </div>
                            </div>
                            <center><a href="<?=base_url();?>Menu/AnnualReviewDet" class="btn btn-success">Self Review</a><br></center>
                        <!--</form>           -->
                    </div>
                </div>
            </div>
        </div>
        
    </div>
</section>
        
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
<script type='text/javascript'>

        
    var cityCount = 0;
    $('#statusid').on('change', function b() {
        var stid = document.getElementById("statusid").value;
        $('#loader').show();
        $.ajax({
            url:'<?=base_url();?>Menu/getAnuualRevirewCmp',
            type: "POST",
            data: {
                stid: stid
            },
            cache: false,
            success: function a(result){
                $("#inid").html(result);
                $('#loader').hide();
                // Count the number of options returned
                var count = $("#inid option").length;
                $('#totalcmp').html('Total Companies Pending for Review : <span style="color:red">'+(count - 1) +'</span>');

                // Show the count in an alert or in any other way you prefer
                // alert("Result count: " + count);

            }
        });
    });



        $('#inid').on('change', function b() {
        var inid = document.getElementById("inid").value;
        var fdate = document.getElementById("fdate").value;
        $.ajax({
        url:'<?=base_url();?>Menu/getcmpnlog',
        type: "POST",
        data: {
        inid: inid,
        fdate: fdate
        },
        cache: false,
        success: function a(result){
        $("#cmpdata").html(result);
        }
        });
        });

        document.getElementById("nstr").style.display = "none";
        $('#exsid').on('change', function b() {
        var exsid = this.value;
        if(exsid=='7'){
            document.getElementById("nstr").style.display = "block";
        }else{
        document.getElementById("nstr").style.display = "none";
        }

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
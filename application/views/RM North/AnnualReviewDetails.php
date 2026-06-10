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
        <style>
            .ApprovedStatus{
                padding:6px 10px;
                border-radius:4px;
            }
            .ApprovedStatus.Pending{
            background:orange;
            color:white;
            }
            .ApprovedStatus.Reject{
            background:red;
            color:white;
            }
            .ApprovedStatus.approved{
            background:green;
            color:white;
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
                            <div class="col-sm col-md-12 col-lg-12 m-auto">
                                <div class="card card-primary card-outline">
                                    <div class="card-body box-profile">
                                        <!--<form action="<?=base_url();?>Menu/startAnnualReview" method="post">-->
                                            <input type="hidden" name="user_id" value="<?=$user['user_id']?>">
                                            <center> <h3>Annual Review Report Details</h3> </center>
                                            <?php 
                                            // echo "<pre>";
                                            // print_r($annualTarget);
                                            // die;
                                            
                                            ?>
                                            <div class="was-validated">
                                                <?php $j=1; foreach($annualTarget as $a){?>
                                                <div class="btn btn-primary col-md-12 col-lg-12 mt-5 d-flex justify-content-between align-items-center">
                                                <?=$this->Menu_model->get_cdbyid($a->company_id)[0]->compname?>
                                                
                                                </div>
                                                <!--<div class="collapse" id="collapseExample<?=$j?>">-->
                                                    <div class="card card-body">
                                                        <div class="ApprovedStatus">
                                                           <h4 class="ApprovedStatus Pending">Changes For Academic year 2024-24</h4> 
                                                           <div class="table-responsive">
                                                           <table id="" class="table table-striped table-bordered" cellspacing="0" width="">
                                                                <?php if($a->keep_company == "yes"){?>
                                                                <thead>
                                                                    <tr><th>Sr No</th>
                                                                        <th>No of Schools</th>
                                                                        <th>Focus Funnel</th>
                                                                        <th>Top Spender</th>
                                                                        <th>Partner Type</th>
                                                                        <th>Focus Quarter</th>
                                                                        <th>Revenue</th>
                                                                        <?php if(isset($a->reachout)){?>
                                                                        <th>Reachout</th><?php }?>
                                                                        <?php if(isset($a->tentative)){?>
                                                                        <th>Tentative</th><?php }?>
                                                                        <?php if(isset($a->positive)){?>
                                                                        <th>Positive</th><?php }?>
                                                                        <?php if(isset($a->positivenap)){?>
                                                                        <th>Positive NAP</th><?php }?>
                                                                        <?php if(isset($a->very_positive)){?>
                                                                        <th>Very Positive </th><?php }?>
                                                                        <?php if(isset($a->closure)){?>
                                                                        <th>Closure</th><?php }?>
                                                                    </tr>
                                                                </thead>
                                                                <tbody>
                                                                    <td><?=$j++;?></td>
                                                                    <td><?=$a->noofschool?></td>
                                                                    <td><?=$a->focusfunnel?></td>
                                                                    <td><?=$a->topspender?></td>
                                                                    <td><?=$this->Menu_model->get_partnerbyid($a->partnertype)[0]->name?></td>
                                                                    <td><?=$a->focusquarter?></td>
                                                                    <td><?=$a->revenue?></td>
                                                                    <?php if(isset($a->reachout)){?>
                                                                        <td><?=$a->reachout?></td>
                                                                    <?php }?>
                                                                    <?php if(isset($a->tentative)){?>
                                                                        <td><?=$a->tentative?></td>
                                                                    <?php }?>
                                                                    <?php if(isset($a->positive)){?>
                                                                        <td><?=$a->positive?></td>
                                                                    <?php }?>
                                                                    <?php if(isset($a->positivenap)){?>
                                                                        <td><?=$a->positivenap?></td><?php }?>
                                                                    <?php if(isset($a->very_positive)){?>
                                                                    <td><?=$a->very_positive?></td><?php }?>
                                                                    <?php if(isset($a->closure)){?>
                                                                    <td><?=$a->closure?></td><?php }?>
                                                                </tbody>
                                                                <?php } ?>
                                                                <?php if($a->keep_company == "no"){?>
                                                                <thead>
                                                                    <tr><th>Sr No</th>
                                                                        <th>Keep Company</th>
                                                                        <th>Remark</th>
                                                                    </tr>
                                                                    
                                                                </thead>
                                                                <tbody>
                                                                    <tr>
                                                                        <td><?=$j++?></td>
                                                                        <td><?=$a->keep_company?></td>
                                                                        <td><?=$a->keepremark?></td>
                                                                    </tr>
                                                                </tbody>
                                                                <?php } ?>
                                                            </table>
                                                           </div>
                                                        </div><br>
                                                        
                                                        <h4 class="ApprovedStatus Pending">Annual Review Remarks</h4> 
                                                        <div class="table-responsive">
                                                            <table id="" class="table table-striped table-bordered" cellspacing="0" width="">
                                                                <thead>
                                                                    <tr><th>Sr Number</th>
                                                                        <th>Question</th>
                                                                        <th>Answer</th>
                                                                    </tr>
                                                                </thead>
                                                                <tbody>
                                                                    <?php $i=1;foreach($annualdata as $ad){
                                                                        if($ad->question == ""){ continue;}
                                                                    ?>
                                                                        <tr>
                                                                            <td><?=$i++;?></td>
                                                                            <td><?=$ad->question?></td>
                                                                            <td><?=$ad->answer?></td>
                                                                            
                                                                        </tr>
                                                                    <?php }?>
                                                                </tbody>
                                                            </table>
                                                        </div>
                                                        
                                                    </div>
                                                    <!-- </div> -->
                                                <!--</div>-->
                                            </div>
                                            <?php }?>
                                        <!--</form>           -->
                                    </div>
                                </div>
                            </div>
                        </div>
            
                    </div>
                </section>
                <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
                <script type='text/javascript'>
$(document).ready(function(){
    $('#bd').on('change', function() {
      var bd =  $('#bd').val();
      $.ajax({
        url:'<?=base_url();?>Menu/getannualreviewcmpbybd',
        type: "POST",
        data: {
          user: bd
        },
        cache: false,
        success: function a(result){
        $("#setcmp").html(result);
        }
            });
    });
    
  });

                </script>
                <!-- /.row (main row) -->
            </div><!-- /.container-fluid -->
            <!-- </section> -->
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
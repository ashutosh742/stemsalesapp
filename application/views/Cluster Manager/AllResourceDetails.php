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
            
            
            <div class="col-sm col-md-12 col-lg-12 m-auto">
                <div class="card card-primary card-outline">
                    <div class="card-body box-profile">
                        <!--<form  action="<?=base_url();?>Menu/startAnnualReview" method="post">-->
                        <input type="hidden" name="user_id" value="<?=$user['user_id']?>">
                            <center> <h3>
                                <?php if($type == "salesppt"){ echo "Corporate PPT";}
                                else if($type == "salespitch"){echo "Sales Pitch";}
                                else if($type == "quarter1-ppt"){echo "Quarter1  PPT";}
                                else if($type == "proposal"){echo "Proposal Of Unique";}
                                else if($type == "whatsapp"){echo "Whats App";}
                                else if($type == "district-permission"){echo "District Permission Letter";}
                                else if($type == "email"){echo "Email";}
                                else if($type == "annualreview"){echo "Annual Review";}
                                else if($type == "regional-case-study"){echo "Regional Case Study";}
                                else if($type == "presentation"){echo "Presentation";}
                                else if($type == "school"){echo "School";}
                                else{
                                    echo "FAQ";
                                }
                                ?>
                                Details</h3> </center>
                            <hr>
                            <div class="was-validated">
                                <div class="form-group">
                                    <div class="table-responsive">
                                        <table id="example1" class="table table-striped table-bordered" cellspacing="0" width="">
                                            <thead>
                                                <tr>							    
                                                    <th width="10%">Sr No</th>
                                                    <?php if($type == "faq"){?>
                                                    <th>Question</th>
                                                    <?php } ?>
                                                    <?php if($type == "salesppt"){?>
                                                        <th>Partner Type</th>
                                                      <th>What changes do you suggest in the PPT?</th>
                                                      <th>Please Mention The Slide Number</th>
                                                      <th>Reference</th>
                                                      <th>Download PPT</th>
                                                    <?php } ?>
                                                    <?php if($type == "salespitch"){?>
                                                        <th>Partner Type</th>
                                                      <th>Pitch</th>
                                                      <th>Voice Note</th>
                                                    <?php } ?>
                                                    <?php if($type == "presentation"){?>
                                                    <th>What changes do you suggest in the Presentation?</th>
                                                      <th>Presentation Product Type</th>
                                                      <th>Reference</th>
                                                      <th>Play Role Video</th>
                                                    <?php }?>
                                                    <?php if($type == "proposal"){?>
                                                        <th>Proposal Name</th>
                                                      <th>Download Proposal</th>
                                                    <?php }?>
                                                    <?php if($type == "email"){?>
                                                    <th>Email Draft</th>
                                                    <th>Download Email Draft</th>
                                                    <?php }?>
                                                    <?php if($type == "whatsapp"){?>
                                                    <th>Whats App Message Draft</th>
                                                    <th>Download Whats App Message Draft</th>
                                                    <?php }?>
                                                    <?php if($type == "whatsapp"){?>
                                                    <th>Whats App Message Draft</th>
                                                    <th>Download Whats App Message Draft</th>
                                                    <?php }?>
                                                    
                                                    <?php if($type == "regional-case-study"){?>
                                                        <th>Regional Case Study</th>
                                                        <th>Download Case Study</th>
                                                    <?php }?>
                                                    
                                                    <?php if($type == "regional-case-study"){?>
                                                        <th>Regional Case Study</th>
                                                        <th>Download Case Study</th>
                                                    <?php }?>
                                                    <?php if($type == "quarter1-ppt"){?>
                                                        <th>Slide No</th>
                                                        <th>PPT</th>
                                                    <?php }?>
                                                    <?php if($type == "annualreview"){?>
                                                        <th>Points Cover</th>
                                                      <th>Partner Type</th>
                                                      <th>Reference</th>
                                                      <th>Slide No</th>
                                                      <th>Download PPT</th>
                                                    <?php }?>
                                                    
                                                    <?php if($type == "district-permission"){?>
                                                        <th>Department Details</th>
                                                        <th>Department Type</th>
                                                        <th>Permission Letter</th>
                                                    <?php }?>
                                                    <?php if($type == "school"){?>
                                                        <th>Client Name</th>
                                                      <th>District</th>
                                                      <th>School Location</th>
                                                      <th>No Of Schools</th>
                                                    <?php }?>
                                                    <!--<th>Review Done</th>-->
                                                    <!--<th>Review Pending</th>-->
                                                    <!--<th>Total Company</th>-->
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php 
                                                $i=1; 
                                                foreach($revDatatar as $a){
                                                ?>
                                                <tr>
                                                    <td><?=$i++?></td>
                                                    <!--<td><?=$this->Menu_model->get_statusbyid($a)[0]->name?></td>-->
                                                    <!--<td><?=$pending[$a]?></td>-->
                                                    <!--<td><?=$val - $pending[$a]?></td>-->
                                                    <?php if($type == "faq"){?>
                                                    <td><?=$a->question?></td>
                                                    <?php }?>
                                                    <?php if($type == "salesppt"){?>
                                                        <td><?=$this->Menu_model->get_partnerbyid($a->partnertype)[0]->name?></td>
                                                        <td><?=$a->pointscover?></td>
                                                        <td><?=$a->slideno?></td>
                                                        <td><?=$a->reference?></td>
                                                    <?php }?>
                                                    <?php if($type == "salespitch"){?>
                                                        <td><?=$this->Menu_model->get_partnerbyid($a->partnertype)[0]->name?></td>
                                                        <td><?=$a->message?></td>
                                                   <?php }?>
                                                    <?php if($type == "presentation"){?>
                                                        <td><?=$a->pointscover?></td>
                                                        <td><?=$a->presentationtype?></td>
                                                    <?php }?>
                                                    <?php if($type == "proposal"){?>
                                                        <td><?=$a->proposalname?></td>
                                                    <?php }?>
                                                    <?php if($type == "proposal"){?>
                                                        <td><?=$a->message?></td>
                                                    <?php }?>
                                                     <?php if($type == "whatsapp"){?>
                                                        <td><?=$a->message?></td>
                                                    <?php }?>
                                                    <?php if($type == "regional-case-study"){?>
                                                        <td><?=$a->message?></td>
                                                    <?php }?>
                                                    <?php if($type == "quarter1-ppt"){?>
                                                        <td><?=$a->slideno?></td>
                                                    <?php }?>
                                                    <?php if($type == "annualreview"){?>
                                                        <td><?=$a->pointscover?></td>
                                                        <td><?=$this->Menu_model->get_partnerbyid($a->partnertype)[0]->name?></td>
                                                        <td><a href="<?=$a->reference?>"><?=$a->reference?></a></td>
                                                        <td><?=$a->slideno?></td>
                                                    <?php }?>
                                                    <?php if($type == "district-permission"){?>
                                                        <td><?=$a->deptdetails?></td>
                                                        <td><?=$a->depttype?></td>
                                                    <?php }?>
                                                    <?php if($type == "school"){?>
                                                        <td><?=$a->client_name?></td>
                                                        <td><?=$a->district?></td>
                                                        <td><?=$a->location?></td>
                                                        <td><?=$a->noofschools?></td>
                                                    <?php }?>
                                                    <?php if(!in_array($type, ["faq", "school"])){?>
                                                        <td><a href="<?=base_url()?><?=$a->filesupload?>"><?=$a->filesupload?></a></td>
                                                    <?php }?>
                                                </tr>
                                                <?php }?>
                                            </tbody>
                                        </table>
                                    
                                </div>
                            </div>
                           
                    </div>
                </div>
            </div>
        </div>
        
    </div>
</section>
        
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
<script type='text/javascript'>
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
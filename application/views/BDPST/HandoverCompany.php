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

            <h1 class="m-0">Handover</h1>

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





    <!-- Main content -->

    <section class="content">

      <div class="container-fluid">

        <div class="row">

          <div class="col-12">

    <div class="card">

              <div class="card-header">

                <h3 class="card-title"></h3>

              </div>

              <!-- /.card-header -->

              <div class="card-body">

                  <div class="container body-content">

        <div class="page-header">

            <div class="form-group">

                <fieldset>

                    <form action="" class="form-group" method="post">

                        <div class="table-responsive">

                            <div class="table-responsive">     

                                <table id="example1" class="table table-striped table-bordered" cellspacing="0" width="100%">

                                    <thead>

                                        <tr>

                                            <th>S.No.</th>

                                            <th>BD Name</th>

                                            <th>PST</th>

                                            <th>Company ID</th>

                                            <th>Company Name</th>

                                            <th>Total Logs</th>	

                                            <th>State</th>	

                                            <th>City</th>	

                                            <th>Draft</th>	

                                            <th>Budget</th>	

                                            <th>Address</th>	

                                            <th>Website</th>

                                            <th>Partner Type</th>

                                            <th>Focus Funnel</th>

                                            <th>Upsell Client</th>

                                            <th>Contact Person</th>	

                                            <th>Contact Number</th>	

                                            <th>Email</th>	

                                            <th>Designation</th>

                                            <th>Created By</th>	

                                            <th>Created At</th>	

                                            <th>Next Action Date</th>	

                                            <th>Next Action Type</th>	

                                            <th>Current Status</th>	

                                            <th>Current Remark</th>

                                            <th></th>

                                        </tr>

                                    </thead>

                                    <tbody>

                                        

                                        <?php 

                                        $i=1;

                                        foreach($mdata as $dt){

                                           $cid = $dt->cmpid_id;

                                            $cd=$this->Menu_model->get_cdbyid($cid);

                                            $ccd=$this->Menu_model->get_ccdbyid($cid);

                                            if($ccd){$contactperson=$ccd[0]->contactperson;$phoneno=$ccd[0]->phoneno;$emailid=$ccd[0]->emailid;$designation=$ccd[0]->designation;}

                                            else{$contactperson="";$phoneno="";$emailid="";$designation="";}

                                            $init=$this->Menu_model->get_initcallbyid($cid);

                                            $ciid = $init[0]->id;

                                            $pid = $init[0]->apst;

                                            $tblc=$this->Menu_model->get_tblcalleventsbyid($ciid);

                                            $logs = sizeof($tblc);

                                            // if($logs>0){

                                            $sid = $tblc[0]->status_id;

                                            if($sid){$sid=$this->Menu_model->get_statusbyid($sid);$sid=$sid[0]->name;}

                                            else{$sid='';}

                                            if($pid){$pstname=$this->Menu_model->get_userbyid($pid);$pstname=$pstname[0]->name;}

                                            else{$pstname='';}

                                        ?>

                                    <tr>

                                         <td><?=$i?></td>

                                         <td><?=$user['name']?></td>

                                         <td><?=$pstname?></td>

                                         <td><?=$cd[0]->id?></td>

                                         <td><a href="CompanyDetails/<?=$cid?>"><?=$cd[0]->compname?></a></td>

                                         <td><?=$logs?></td>

                                         <td><?=$cd[0]->state?></td>

                                         <td><?=$cd[0]->city?></td>

                                         <td><?=$cd[0]->draft?></td>

                                         <td><?=$cd[0]->budget?></td>

                                         <td><?=$cd[0]->address?></td>

                                         <td><?=$cd[0]->website?></td>

                                         <td><?php if(isset($cd[0]->partnerType_id)){echo $cd[0]->partnerType_id;}?></td>

                                         <td><?php if(isset($cd[0]->focus_funnel)){echo $cd[0]->focus_funnel;}?></td>

                                         <td><?php if(isset($cd[0]->upsell_client)){echo $cd[0]->upsell_client;}?></td>

                                         <td><?=$contactperson?></td>

                                         <td><?=$phoneno?></td>

                                         <td><?=$emailid?></td>

                                         <td><?=$designation?></td>

                                         <td><?=$cd[0]->createddate?></td>

                                         <td><?=$init[0]->creator_id?></td>

                                         <td><?=$tblc[0]->appointmentdatetime?></td>

                                         <td><?=$tblc[0]->nextaction?></td>

                                         <td><?=$sid?></td>

                                         <td><?=$tblc[0]->remarks?></td>

                                         <td><a href="handover/<?=$cid?>">Handover</a></td>

                                     </tr>

                                     <?php $i++;}
                                    // }
                                     ?>

                                  </tbody>

                                </table>

                            </div>

                        </div>

                    </form>            <!--END OF FORM ^^-->

                </fieldset>

            </div>

            <hr />

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


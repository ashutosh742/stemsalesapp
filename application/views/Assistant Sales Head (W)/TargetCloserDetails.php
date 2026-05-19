<?php 
              $slct_user_id             = $targetData[0]->user_id;
              $no_of_meeting            = $targetData[0]->no_of_meeting;
              $num_of_barg_meeting      = $targetData[0]->num_of_barg_meeting;
              $no_of_proposal           = $targetData[0]->no_of_proposal;
              $no_of_prospective        = $targetData[0]->no_of_prospective;
              $revenue                  = $targetData[0]->revenue;
              $school                   = $targetData[0]->school;
              $partner_id               = $targetData[0]->partner_id;
              $category_ids             = $targetData[0]->category_id;
              $out_station_meeting      = $targetData[0]->out_station_meeting;
              $local_station_meeting    = $targetData[0]->local_station_meeting;
              $start_date               = $targetData[0]->start_date;
              $end_date                 = $targetData[0]->end_date;
              $target_by                = $targetData[0]->target_by;
              $created_at               = $targetData[0]->created_at;
              $to_user                  = $targetData[0]->to_user;
              $by_user                  = $targetData[0]->by_user;
             
            

              $allWorkOrders             = $this->Menu_model->GetWorkOrderRevenueAndSchoolBetweenDateDetails($slct_user_id,$start_date,$end_date);
                
     


              ?>

<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="wi$dth=device-wi$dth, initial-scale=1">
    <title>Target vs Achievement OF  <?= $to_user.' - '.$by_user; ?> - <?= $start_date.' - '.$end_date; ?> | STEM APP | WebAPP</title>
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
      .col.card.coladjust1 select,.col.card.coladjust31 select,.container-fluid.body-content.mt-4,.each,.form-row>.col,.form-row>[class*=col-],.table-striped tbody tr:nth-of-type(odd),input.form-control,table.table-bordered.dataTable{box-shadow:inset 4px 4px 7px rgba(55,84,170,.15),inset -4px -4px 10px #fff,0 0 2px rgba(255,255,255,.2);transition:box-shadow .2s ease-in-out}.scrollme{overflow-x:auto}.card-body{background:azure}.formSection{align-items:center;justify-content:center;display:flex}.form-control{width:200px!important}.modal-content{background:#faebd7}.coladjust{padding-right:5px;padding-left:5px;height:143px;align-items:center;justify-content:center;color:#000}.col.card.coladjust1{color:#000;text-align:center;align-items:center}th.sorting{background:#008b8b;color:#fff}.modal-header{background:#5f9ea0;color:#fff}.form-control.is-invalid,.was-validated .form-control:invalid{background-image:none!important}.form-control.is-valid,.was-validated .form-control:valid{background-image:none!important}.col.card.coladjust1 select{width:100%!important;height:100%!important;background:#f0fff0}.each{background:#f1f3f6;font-family:Arial,Helvetica,sans-serif;border:5px solid #eaeef5}.col.card.coladjust1.p-2.m-1{background:beige}.container-fluid.body-content.mt-4{padding:20px}.col.card.coladjust.m-1{background:#fff8dc}tbody{font-weight:500}.input-group{margin-bottom:10px;display:table-cell}.remove-btn{cursor:pointer;color:red}.coladjust121{min-height:143px;align-items:center;justify-content:center;color:#000;padding:20px}.col.card.coladjust31.p-2.m-1{align-items:center}.col.card.coladjust31 select{background:#f0fff0}.meetingbackground{background:#bdb76b}
      @import url('https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css');
      @import url('https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700&display=swap');
.cta-btn,.tab-link{cursor:pointer;font-weight:500}.tabs-container{height:auto;border-radius:20px;box-shadow:0 20px 40px rgba(0,0,0,.15);overflow:hidden;position:relative;box-shadow:inset 4px 4px 7px rgba(55,84,170,.15),inset -4px -4px 10px #fff,0 0 2px rgba(255,255,255,.2);transition:box-shadow .2s ease-in-out}.tabs{padding:10px 20px 15px}.tab-links{border-bottom:1px solid #f0f0f0}.btn-group>.btn-group:not(:first-child)>.btn,.btn-group>.btn:not(:first-child),.cta-btn,.tab-link{border:none}[type=button]:not(:disabled),[type=reset]:not(:disabled),[type=submit]:not(:disabled),button:not(:disabled){color:navy;padding:10px;margin:5px;font-size:18px;font-weight:600;background:#ffebcd;box-shadow:inset 4px 4px 7px rgba(55,84,170,.15),inset -4px -4px 10px #fff,0 0 2px rgba(255,255,255,.2);transition:box-shadow .2s ease-in-out}.tab-link{background:0 0;font-size:16px;padding:15px 30px;color:#ccc;position:relative;transition:color .3s}.cta-btn,.tab-link::after{background:linear-gradient(45deg,#b84de5,#7d41ff)}.tab-link.active,.tab-link:hover{color:#b84de5;padding:10px 24px;margin:5px;font-size:18px;font-weight:600;transition:.3s ease-in-out}.tab-link i{margin-right:10px}.tab-link::after{content:'';position:absolute;width:0;height:3px;bottom:-1px;left:50%;transition:.4s}.tab-link.active::after{width:100%;left:0}.tab-content{display:none;animation:.5s fadeInUp;padding:5px 10px 15px}@keyframes fadeInUp{from{opacity:0;transform:translateY(20px)}to{opacity:1;transform:translateY(0)}}.cta-btn{display:inline-block;padding:12px 25px;color:#fff;border-radius:50px;transition:background .4s;margin-top:20px}.cta-btn:hover{background:linear-gradient(45deg,#9c3bce,#6b3ee8)}@media screen and (max-width:600px){.tab-links{flex-direction:column;align-items:center}.tab-link{text-align:center;width:100%;padding:15px 0}}.tab-content.active{display:block;background-color:#008b8b14;margin-top:16px;box-shadow:inset 4px 4px 7px rgba(55,84,170,.15),inset -4px -4px 10px #fff,0 0 2px rgba(255,255,255,.2);transition:box-shadow .2s ease-in-out;border-radius:10px}h4.each.p-2.text-center{color:#ff1493;font-family:system-ui;font-weight:700;letter-spacing:2px}.row.meetings-data{background:beige;padding:10px}
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
          <?php if($this->session->flashdata('success_message')): ?>
          <div class="alert alert-success">
            <?php echo $this->session->flashdata('success_message'); ?>
          </div>
          <?php endif; ?>
          <div class="container-fluid">
          <?php // echo $category_ids ?>
            <div class="card-body">
              <div class="each p-2 bg-info1 text-center">
                <h2 class="text-center text-uppercase mt-3" style="font-weight: 700;" >Target <span class="text-danger">VS</span> Achievement</h2>
                <hr>
                <h4 class="text-center text-uppercase" style="font-weight: 700;" ><?=$message?></h4>
                <hr>
                <h5><?= $to_user.' - '.$by_user; ?></h5>
                <hr>
                <h6><?= $start_date.' - '.$end_date; ?></h6>
                <hr>
                <h6>Target Clouser Details</h6>
              </div>
            </div>
            
            <hr>

            <div class="row">
               
              <div class="col-12">
                <!-- /.card-header -->
                <div class="card-body">
                  <div class="each body-content p-4">
                    <div class="table-responsive text-nowrap">
                      <table id="example1" class="table table-striped table-bordered" cellspacing="0" width="100%">
                        <thead>
                          <tr>
                            <th>S.No</th>
                            <th>Closer User Name</th>
                            <th>Main BD Name</th>
                            <th>CID</th>
                            <th>Company Name</th>
                            <th>Company Partner Type</th>
                            <th>Current Satus</th>
                            <th>No Of School</th>
                            <th>Revenue</th>
                            <th>Closer Date</th>
                            <th>View Attachment</th>
                          </tr>
                        </thead>
                        <tbody>
                          <?php $i = 1; foreach($allWorkOrders  as $data): ?>
                          <tr>
                            <td><?= $i; ?></td>
                            <td><?= $data->username; ?></td>
                            <td><?= $data->mainbd_name; ?></td>
                       
                            <td><a href="https://stemapp.in//Menu/CompanyDetails/<?= $data->cid; ?>" target="_BLANK" ><?= $data->cid; ?></a></td>
                            <td><a href="https://stemapp.in//Menu/CompanyDetails/<?= $data->cid; ?>" target="_BLANK" ><?= $data->compname; ?></a></td>
                            <td> <?php  $getpartner_name = $this->Menu_model->get_partnerbyid($data->partnerType_id)[0]->name; echo $getpartner_name;?></td>

                            <td><?= $data->current_status_name; ?></td>
                            <td><?= $data->created_at; ?></td>
                            <td><?= $data->attachment_flink; ?></td>
                            <td><?php  $attachment_flink = $data->attachment_flink; ?>
                                <a href="https://stemapp.in/<?=$attachment_flink ?>" target="_BLANK" >View</a>
                            </td>
                        </tr>
                          <?php $i++; endforeach; ?>
                        </tbody>
                      </table>
                    </div>
                  </div>
                </div>
              </div>
            </div>









            <center><button class="each btn btn-info" id="printPage">Print Page</button> <br> </center>
            <br>
          </div>
          <!-- /.card -->
      </div>
      <!-- /.col -->
    </div>
    <!-- /.row -->
    </div>
    <!-- /.container-fluid -->
    </section>
    </div>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>

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
      const tabLinks = document.querySelectorAll('.tab-link');
      const tabContents = document.querySelectorAll('.tab-content');
      
      tabLinks.forEach(link => {
      link.addEventListener('click', () => {
      // Remove active class from all links
      tabLinks.forEach(link => link.classList.remove('active'));
      // Add active class to clicked link
      link.classList.add('active');
      
      // Hide all tab contents
      tabContents.forEach(content => content.classList.remove('active'));
      
      // Show current tab content
      const targetTab = document.getElementById(link.dataset.tab);
      targetTab.classList.add('active');
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
      "buttons": ["csv", "excel", "pdf"]
      }).buttons().container().appendTo('#example1_wrapper .col-md-6:eq(0)');
      $("#printPage").click(function() {
        window.print();
      });
      
      
      
      
    </script>
  </body>
</html>
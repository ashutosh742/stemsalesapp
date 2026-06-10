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
            <h1 class="m-0">Dashboard</h1>
          </div><!-- /.col -->
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
                  <h4>HI!  <?=$user['name']?></h4> 
            </ol>
          </div><!-- /.col -->
        </div><!-- /.row -->
      </div><!-- /.container-fluid -->
    </div>
    <!-- /.content-header -->

    <!-- Main content -->
    <section class="content">
      <div class="container-fluid">
        <!-- Small boxes (Stat box) -->
        <div class="row">
          <div class="col-12">
            <!-- Default box -->
    <!-- Main content -->
    <section class="content">
      <div class="container-fluid">
        <!-- Small boxes (Stat box) -->
        <div class="row">
          <!-- ./col -->
          <div class="col-lg-3 col-6">
            <!-- small box -->
            <div class="small-box bg-light text-secondary">
              <div class="inner">
                <h5>Task of Team</h5>
                
              </div>
              <div class="icon">
                <i class="ion ion-person-add"></i>
              </div>
              <a href="totaltask" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
            </div>
          </div>
          <!-- ./col -->
          <div class="col-lg-3 col-6">
            <!-- small box -->
            <div class="small-box bg-light text-secondary">
              <div class="inner">
                <h5>PM Task</h5>
                
              </div>
              <div class="icon">
                <i class="ion ion-pie-graph"></i>
              </div>
              <a href="#" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
            </div>
          </div>
          <!-- ./col -->
          <div class="col-lg-3 col-6">
            <!-- small box -->
            <div class="small-box bg-light text-secondary">
              <div class="inner">
                <h5>Request</h5>
               
              </div>
              <div class="icon">
                <i class="ion ion-bag"></i>
              </div>
              <a href="#" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
            </div>
          </div>
          <!-- ./col -->
          <div class="col-lg-3 col-6">
            <!-- small box -->
            <div class="small-box bg-light text-secondary">
              <div class="inner">
                <h5>ZH Task</h5>
                
              </div>
              <div class="icon">
                <i class="ion ion-stats-bars"></i>
              </div>
              <a href="#" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
            </div>
          </div>
          <!-- ./col -->
          
         
            
        <!-- /.row -->
        
        
    
            
          </div>
        <!-- /.row (main row) -->
            
            
<div class="row">
          <div class="col-lg-8 col-sm">
            <div class="card card-primary card-outline card-outline-tabs">
                <h4 class="p-3">Today's Task</h4>
              <div class="card-header p-0 border-bottom-0">
                <ul class="nav nav-tabs" id="custom-tabs-four-tab" role="tablist">
                  <li class="nav-item">
                    <a class="nav-link active" id="custom-tabs-four-home-tab" data-toggle="pill" href="#custom-tabs-four-home" role="tab" aria-controls="custom-tabs-four-home" aria-selected="true">
                        All <span class="badge badge-success"></span>
                    </a>
                    
                  </li>
                  <li class="nav-item">
                    <a class="nav-link" id="custom-tabs-four-profile-tab" data-toggle="pill" href="#custom-tabs-four-profile" role="tab" aria-controls="custom-tabs-four-profile" aria-selected="false">
                        Visit <span class="badge badge-success"></span>
                    </a>
                  </li>
                  <li class="nav-item">
                    <a class="nav-link" id="custom-tabs-four-call-tab" data-toggle="pill" href="#custom-tabs-four-call" role="tab" aria-controls="custom-tabs-four-call" aria-selected="false">
                        Call <span class="badge badge-success"></span>
                    </a>
                  </li>
                  <li class="nav-item">
                    <a class="nav-link" id="custom-tabs-four-email-tab" data-toggle="pill" href="#custom-tabs-four-email" role="tab" aria-controls="custom-tabs-four-email" aria-selected="false">
                        Email <span class="badge badge-success"></span>
                    </a>
                  </li>
                  <li class="nav-item">
                    <a class="nav-link" id="custom-tabs-four-whatsapp-tab" data-toggle="pill" href="#custom-tabs-four-whatsapp" role="tab" aria-controls="custom-tabs-whatsapp" aria-selected="false">
                        Whatsapp<span class="badge badge-success"></span>
                    </a>
                  </li>
                  <li class="nav-item">
                    <a class="nav-link" id="custom-tabs-four-report-tab" data-toggle="pill" href="#custom-tabs-four-report" role="tab" aria-controls="custom-tabs-four-report" aria-selected="false">
                        Report <span class="badge badge-success"></span>
                    </a>
                  </li>
                  
                </ul>
                
              </div>
              <div class="card-body">
                <div class="tab-content" id="custom-tabs-four-tabContent">
                  <div class="tab-pane fade show active" id="custom-tabs-four-home" role="tabpanel" aria-labelledby="custom-tabs-four-home-tab">
                      
                      <div>
                          <b>
                          
                          <small class="badge badge-secondary text-right"></small>
                          </b>
                          <hr>
                      </div>
                      
                  </div>
                  <div class="tab-pane fade" id="custom-tabs-four-profile" role="tabpanel" aria-labelledby="custom-tabs-four-profile-tab">
                      <div>
                          <b>
                          
                          <small class="badge badge-secondary text-right"></small>
                          </b>
                          <hr>
                      </div>
                  </div>
                  <div class="tab-pane fade" id="custom-tabs-four-call" role="tabpanel" aria-labelledby="custom-tabs-four-call-tab">
                      <div>
                          <b>
                          
                          <small class="badge badge-secondary text-right"></small>
                          </b>
                          <hr>
                      </div>
                  </div>
                  <div class="tab-pane fade" id="custom-tabs-four-email" role="tabpanel" aria-labelledby="custom-tabs-four-email-tab">
                      <div>
                          <b>
                          
                          <small class="badge badge-secondary text-right"></small>
                          </b>
                          <hr>
                      </div>
                  </div>
                  <div class="tab-pane fade" id="custom-tabs-four-whatsapp" role="tabpanel" aria-labelledby="custom-tabs-four-whatsapp-tab">
                      <div>
                          <b>
                          
                          <small class="badge badge-secondary text-right"></small>
                          </b>
                          <hr>
                      </div>
                  </div>
                  
                  <div class="tab-pane fade" id="custom-tabs-report" role="tabpanel" aria-labelledby="custom-tabs-four-report-tab">
                      <div>
                          <b>
                          
                          <small class="badge badge-secondary text-right"></small>
                          </b>
                          <hr>
                      </div>
                  </div>
                  
                </div>
              </div>
              <!-- /.card -->
            </div>
            
            <div class="card">
              <div class="card-header">
                <h3 class="card-title">
                  <i class="ion ion-clipboard mr-1"></i>
                  Activity to be Schedule
                </h3>
              </div>
              <!-- /.card-header -->
              <div class="card-body">
                <ul class="todo-list" data-widget="todo-list">
                    
                    
                      <li>
                          <span class="text">
                            <span class="handle">
                              <i class="fas fa-ellipsis-v"></i>
                              <i class="fas fa-ellipsis-v"></i>
                            </span>
                          
                          <small class="badge badge-secondary"><button type="button" id="other_action" class="btn btn-primary float-right" >Plan Action</button></small>
                          
                      </li>
                </ul>
              </div>
              <!-- /.card-body -->
              <div class="card-footer clearfix">
                
              </div>
            </div>
            <!-- /.card -->
            </div>
            <div class="col-lg-4 col-sm">
            <div class="card card-primary card-outline card-outline-tabs">
                <h5 class="p-3">Today's Task</h5><hr>
              <div class="card-header p-0 border-bottom-0"> </div>
              <div class="card-body">
                <div class="tab-content" id="custom-tabs-four-tabContent">
                    <div class="table-responsive">
                            <div class="table-responsive">     
                                <table id="example1" class="table table-striped table-bordered" cellspacing="0" width="100%">
                                    <thead>
                                        <tr><th>Task</th> </tr>
                                    </thead>
                                    <tbody>
                  
                    <tr><td><div><i class="fa fa-thumb-tack" aria-hidden="true"></i> <b></b></div><div class="text-right">Task assign by </div>
                    
                    </td></tr>
                  </tbody>
                                </table>
                            </div>
                        </div>
                </div>
              </div>
              <!-- /.card -->
            </div>
            
            
              
          </div>
        </div>
</div>
 </div>
        </div>
</div>
        
            
            
            
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
<!-- AdminLTE App -->
<script src="<?=base_url();?>assets/js/adminlte.js"></script>

<!-- AdminLTE dashboard demo (This is only for demo purposes) -->
<script src="<?=base_url();?>assets/js/dashboard.js"></script>

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

<script>
    $("#example1").DataTable({
      "responsive": false, "lengthChange": false, "autoWidth": false,
      "buttons": ["excel", "pdf"]
    }).buttons().container().appendTo('#example1_wrapper .col-md-6:eq(0)');
</script>
</body>
</html>

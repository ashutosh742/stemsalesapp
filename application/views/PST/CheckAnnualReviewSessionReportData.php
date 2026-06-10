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
      .text-secondary1 {
      color: #ab0101 !important;
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
        <?php 
          // dd($bd_annualData);
          ?>
        <!-- Main content -->
        <section class="content">
          <div class="container-fluid">
            <?php 
  
    $seesion_count = 0;
    $totalMinutes = 0;

foreach ($review_session as $item) {
    if (!empty($item->time_difference)) {
        preg_match('/(\d+) Days/', $item->time_difference, $days);
        preg_match('/(\d+) Hours/', $item->time_difference, $hours);
        preg_match('/(\d+) Minutes/', $item->time_difference, $minutes);

        $days = isset($days[1]) ? (int)$days[1] : 0;
        $hours = isset($hours[1]) ? (int)$hours[1] : 0;
        $minutes = isset($minutes[1]) ? (int)$minutes[1] : 0;

        // Convert everything to minutes
        $totalMinutes += ($days * 24 * 60) + ($hours * 60) + $minutes;
        $seesion_count++;
    }
}

// Convert total minutes back to days, hours, and minutes
$totalDays = floor($totalMinutes / (24 * 60));
$remainingMinutes = $totalMinutes % (24 * 60);
$totalHours = floor($remainingMinutes / 60);
$totalMinutes = $remainingMinutes % 60;

$totalTimeFormatted = "Total Time: {$totalDays} Days, {$totalHours} Hours, {$totalMinutes} Minutes";






$timeString = $totalTimeFormatted;
// Convert time to total minutes
preg_match_all('/(\d+)\s*(Days?|Hours?|Minutes?)/i', $timeString, $matches);
$totalMinutes = 0;

foreach ($matches[1] as $index => $value) {
    $unit = strtolower($matches[2][$index]);
    if (strpos($unit, 'day') !== false) {
        $totalMinutes += $value * 24 * 60;
    } elseif (strpos($unit, 'hour') !== false) {
        $totalMinutes += $value * 60;
    } elseif (strpos($unit, 'minute') !== false) {
        $totalMinutes += $value;
    }
}
// Divide by 5 to get the average time
$averageMinutes = $totalMinutes / sizeof($review_session);
// Convert minutes back to days, hours, and minutes
$days = floor($averageMinutes / (24 * 60));
$remainingMinutes = $averageMinutes % (24 * 60);
$hours = floor($remainingMinutes / 60);
$minutes = $remainingMinutes % 60;

$averageTimeFormatted =  "{$days} Days, {$hours} Hours, {$minutes} Minutes";

$ctimeString = $totalTimeFormatted;
// Convert time to total minutes
preg_match_all('/(\d+)\s*(Days?|Hours?|Minutes?)/i', $ctimeString, $matches);
$totalMinutes = 0;

foreach ($matches[1] as $index => $value) {
    $unit = strtolower($matches[2][$index]);
    if (strpos($unit, 'day') !== false) {
        $totalMinutes += $value * 24 * 60;
    } elseif (strpos($unit, 'hour') !== false) {
        $totalMinutes += $value * 60;
    } elseif (strpos($unit, 'minute') !== false) {
        $totalMinutes += $value;
    }
}
// Divide by 5 to get the average time
$caverageMinutes = $totalMinutes / sizeof($review_company);
// Convert minutes back to days, hours, and minutes
$cdays = floor($caverageMinutes / (24 * 60));
$cremainingMinutes = $caverageMinutes % (24 * 60);
$chours = floor($cremainingMinutes / 60);
$cminutes = $cremainingMinutes % 60;

$average_review_per_company =  "{$cdays} Days, {$chours} Hours, {$cminutes} Minutes";


            ?>


            <div class="row">
              <div class="col-12">
                <div class="card">
                  <div class="card-header">
                    <div class="text-center bg-primary p-2">
                      <h3>Annual Review Session</h3>
                      <h4 class="text-center m-3"><?= $fyear; ?></h4>
                      <h5 class="text-center m-3"><?= $tar_name?></h5>
                    </div>
                    <br>
                  </div>
                  <?php $fyear = date("Y", strtotime($financial_Year['start_date'])); ?>
                  <!-- /.card-header -->
                  <?php 
                    // dd($review_session);
                    ?>
                  <div class="card-body">
                    <div class="container-fluid body-content">
                      <div class="page-header">
                        <div class="row">
                          <div class="col-md-4">
                            <div class="card text-center bg-primary">
                              <h6>Total Session</h6>
                              <h6><?=sizeof($review_session);?></h6>
                            </div>
                          </div>
                          <div class="col-md-4">
                            <div class="card text-center bg-primary">
                              <h5>Total Time in Session</h5>
                              <h6><?=$totalTimeFormatted;?></h6>
                            </div>
                          </div>
                        
                          <div class="col-md-4">
                            <div class="card text-center bg-primary">
                              <h6>Average Time in Session</h6>
                              <h6><?= $averageTimeFormatted;?></h6>
                            </div>
                          </div>
                          <div class="col-md-6">
                            <div class="card text-center bg-primary">
                              <h5>Complete Company in Session</h5>
                              <h6><?=sizeof($review_company);?></h6>
                            </div>
                          </div>
                          <div class="col-md-6">
                            <div class="card text-center bg-primary">
                              <h6>Average review time per company</h6>
                              <h6><?= $average_review_per_company;?></h6>
                            </div>
                          </div>
                        </div>
                        <div class="table-responsive">
                          <table id="example1" class="table table-striped table-bordered" cellspacing="0" width="100%">
                            <thead class="thead-dark">
                              <tr>
                                <th>S.No</th>
                                <th>Session</th>
                                <th>Start</th>
                                <th>Close</th>
                                <!-- <th>Total time Session</th> -->
                                <th>Session Status</th>
                                <th>Total time Session</th>
                              </tr>
                            </thead>
                            <tbody>
                              <?php $i=1; ?>
                              <?php foreach($review_session as $session){ ?>
                              <tr>
                                <td><?=$i?></td>
                                <td>Session <?=$i?></td>
                                <td><?=$session->psdatetime?></td>
                                <td><?php 
                                if($session->pcdatetime == ''){
                                    echo "<span class='bg-warning p-1'>Pending</span>";
                                }else{
                                    echo $session->pcdatetime;
                                }
                                ?></td>
                                
                                <td><?=$session->main_review_on?></td>
                             
                                <td><?php 
                                if($session->time_difference == ''){
                                    echo "<span class='bg-warning p-1'>Pending</span>";
                                }else{
                                    echo $session->time_difference;
                                }
                                ?></td>
                              </tr>
                              <?php $i++;} ?>
                            </tbody>
                          </table>
                        </div>
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
    </div>    
    <?php
      $startYear = date("Y");
      $endYear = $startYear + 1;
      
      ?>
    <footer class="main-footer">
      <strong>Copyright &copy; <?php echo "$startYear-$endYear"; ?> <a href="<?=base_url();?>">Stemlearning</a>.</strong>
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
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
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
        "responsive": false,
        "lengthChange": false,
        "autoWidth": false,
        "pageLength": 50, // Set default pagination to 30
        "buttons": ["copy", "csv", "excel", "pdf", "print", "colvis"]
      }).buttons().container().appendTo('#example1_wrapper .col-md-6:eq(0)');
    </script>
  </body>
</html>
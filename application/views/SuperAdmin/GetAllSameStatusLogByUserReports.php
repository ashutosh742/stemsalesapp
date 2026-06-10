<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>All Same Status Log ByUser Reports | STEM APP | WebAPP</title>
    <!-- Google Font: Source Sans Pro -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.0/css/all.min.css">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="<?=base_url();?>assets/css/all.min.css">
    <!-- Ionicons -->
    <link rel="stylesheet" href="https://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css">
    <!-- Tempusdominus Bootstrap 4 -->
    <link rel="stylesheet" href="<?=base_url();?>assets/css/tempusdominus-bootstrap-4.min.css">
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
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
    <link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.min.css">
    <!-- DataTables -->
    <link rel="stylesheet" href="<?=base_url();?>assets/css/dataTables.bootstrap4.min.css">
    <link rel="stylesheet" href="<?=base_url();?>assets/css/responsive.bootstrap4.min.css">
    <link rel="stylesheet" href="<?=base_url();?>assets/css/buttons.bootstrap4.min.css">
    <style>
      .scrollme {
      overflow-x: auto;
      }
      .content {
      margin: 20px;
      padding: 20px;
      border: 1px solid #ccc;
      }
      img {
      max-width: 100%;
      }
    </style>
  </head>
  <body class="hold-transition sidebar-mini layout-fixed">
    <div class="wrapper">
      <!-- Preloader -->
      <!-- Navbar -->
      <?php require('nav.php');?>
      <?php //include 'addlog.php';?>
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
       
    <section class="content" id="pdf-content">
    <div class="text-right mt-2"><button class="btn btn-info" id="printPage">Print Page</button> <br><br>

          <div class="container-fluid">
            <form class="p-3" method="POST" action="">
              <select name="days" id="">
                <option value="">Select Days</option>
                <option value="8">8 Days</option>
                <option value="10">10 Days</option>
                <option value="15">15 Days</option>
                <option value="20">20 Days</option>
                <option value="25">25 Days</option>
                <option value="28">30 Days</option>
                <option value="40">40 Days</option>
              </select>
              <button type="submit" class="bg-primary text-light" fdprocessedid="l104xo">Filter</button>
            </form>
            <div class="row">
              <div class="col-md-10 text-center p-2">
                <div class="card-header bg-primary" style="align-items: center; justify-content: center; display: flex ;">
                  <h2>All Same Status Log By User Reports</h2>
                </div>
              </div>
              <div class="col-md-1 p-2" style="align-items: center; justify-content: center; display: flex ;">
                <div class="text-center">
                  <?= Date("Y-m-d H:i:s"); ?>
                </div>
              </div>
             
            </div>
            <div class="row">
              <div class="col-12">
                <div class="card">
                  <!-- <div class="card-header bg-primary" style="align-items: center; justify-content: center; display: flex ;">
                    <h3 class="card-title">All Planner Log Planned By Users</h3>
                    </div> -->
                  <div class="card-body">
                    <div class="body-content">
                      <div class="page-header">
                        <?php 
                          $target_date = Date("Y-m-d");
                          $days = 8;
                          if(isset($_POST['days'])){
                              if($_POST['days'] !==''){
                                  $days = $_POST['days'];
                              }
                          }
                          $getalluser = $this->Menu_model->get_userbyaaid($uid);
                          foreach($getalluser as $tuser){
                              $slsctuser = $tuser->user_id;
                              $sameData   =   $this->Menu_model->CheckNoStatusChangeBetweenDaysByuid($slsctuser,$days);
                              // echo $this->db->last_query();
                              $sameDatacnt = sizeof($sameData);
                              if($sameDatacnt > 0){
                                  $slsctusername = $tuser->name;
                          
                          ?>
                        <div class="table-responsive text-nowrap">
                          <div class="text-center bg-warning">
                            <h4 class="p-2"> <?= $slsctusername; ?></h4>
                          </div>
                          <table id="example1" class="table table-striped table-bordered" cellspacing="0" wi$dth="100%">
                            <thead style="background: #ff0000; color: white;">
                              <tr>
                                <th>S.No.</th>
                                <th>Company Name</th>
                                <th>Partner Name</th>
                                <th>Current Status</th>
                                <th>Last Status</th>
                                <th>Last Task Name</th>
                                <th>Last Task Date</th>
                                <th>Task Count Last <?=$days;?> Days</th>
                                <th>Time Difference</th>
                              </tr>
                            </thead>
                            <tbody>
                              <?php $i=1; foreach($sameData as $data): ?>
                              <tr>
                                <td><?= $i; ?></td>
                                <td><?= $data->compname; ?></td>
                                <td><?= $data->pname; ?></td>
                                <td><?= $data->current_status; ?></td>
                                <td><?= $data->last_status; ?></td>
                                <td><?= $data->last_task_name; ?></td>
                                <td><?= $data->last_task_date; ?></td>
                                <td><?= $data->task_count_last_days; ?></td>
                                <td><?= $data->time_difference; ?></td>
                              </tr>
                              <?php $i++; endforeach; ?>
                            </tbody>
                          </table>
                        </div>
                        <?php 
                          }
                          }
                             ?>
                        <hr/>
                        <br/>
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
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    <script src="https://html2canvas.hertzen.com/dist/html2canvas.min.js"></script>
 <script>
      $(document).ready(function () {
        $("#printPage").click(function() {
              window.print();
          });
});
</script>
    <script>
      $(document).ready(function () {
      
          $.ajax({
          url: '<?= base_url(); ?>Menu/CheckPlannerlogReportExistsOrNot',
          type: "POST",
          data: {
            filetypesname: 'AllPlannerLogPlannedByUsers',
          },
          cache: false,
          success: function(result) {
              if (result !== 'exists') {
                  let clickTriggered = false;
                  setTimeout(function() {
                      if (!clickTriggered) {
                          $("#generate-pdf").click();
                          clickTriggered = true;
                      }
                  }, 2000);
              }else{
                  console.log("Log Report Allready Store");
              }
          }
      });
      
      
      $("#generate-pdf").click();
      
      $('#generate-pdf').on('click', function () {
      const { jsPDF } = window.jspdf;
      $(this).css("background-color", "green");
      
      // Use html2canvas to capture the content as an image
      html2canvas(document.querySelector('#pdf-content')).then(function (canvas) {
          const imgData = canvas.toDataURL('image/png'); // Convert canvas to image
          const pdf = new jsPDF('p', 'mm', 'a4');
      
          const pdfWidth = pdf.internal.pageSize.getWidth(); // A4 page width
          const pdfHeight = pdf.internal.pageSize.getHeight(); // A4 page height
      
          const canvasWidth = canvas.width; // Image width
          const canvasHeight = canvas.height; // Image height
      
          // Scale image to full page width, calculate the height maintaining aspect ratio
          const ratio = pdfWidth / canvasWidth; // Scaling factor based on full page width
          const newWidth = pdfWidth; // Full page width
          const newHeight = canvasHeight * ratio; // Scale height based on the aspect ratio
      
          let yOffset = 0; // Start at the top of the first page
      
          // Loop to add multiple pages if the content exceeds the page height
          while (yOffset < newHeight) {
              // If not the first page, add a new page
              if (yOffset > 0) {
                  pdf.addPage();
              }
      
              // Add the image to the PDF at the current offset
              pdf.addImage(imgData, 'PNG', 0, -yOffset, newWidth, newHeight);
      
              // Move the offset down by the height of the current page
              yOffset += pdfHeight;
      
              // If the content overflows the current page, add it to the next page
              if (yOffset + pdfHeight > newHeight) {
                  const remainingHeight = newHeight - yOffset;
                  pdf.addImage(imgData, 'PNG', 0, -yOffset, newWidth, remainingHeight);
                  break; // Stop adding more content
              }
          }
      
          const pdfBase64 = pdf.output('datauristring');
      
          $.ajax({
              url: '<?=base_url();?>Menu/UploadCRMReports',
              type: 'POST',
              data: {
                  pdf: pdfBase64.split(',')[1], // Only send the Base64 content
                  filename: 'AllSameStatusLogByUserReports.pdf',
                  filetypesname: 'AllSameStatusLogByUserReports',
              },
              success: function (response) {
                  console.log('PDF uploaded successfully! Server Response: ' + response);
              },
              error: function (xhr, status, error) {
                  console.error('Upload error: ' + error);
              },
          });
      });
      });
      
      
      
      });
    </script>
  </body>
</html>
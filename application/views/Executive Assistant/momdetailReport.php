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
      .content {
      margin: 20px;
      padding: 20px;
      border: 1px solid #ccc;
      }
      .card.card-fordetails {
    height: 100px;
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
        <!-- /.content-header -->
        <?php 
          $bd = $this->Menu_model->get_userbyaid($uid);
          ?>
        <!-- Main content -->
        <section class="content" id="pdf-content">
          <div class="text-right mt-2">
            <button class="btn btn-info" id="printPage">Print Page</button> <br><br>
            <div class="container-fluid">
              <div class="row">
                <div class="col-md-12 text-center p-2">
                  <div class="card-header bg-primary">
                    <h4 class="text-center">MOM Reports</h4>
                    <h5><?= $sd;?></h5>
                  </div>
                </div>
              </div>
              <div class="row">
                <?php 
                  // $sd = '2024-11-01';
                  $momd = $this->Menu_model->get_mombtdate($sd,$ed,$uid,0);
		
                  $totalApproved = 0;
                  $totalReject   = 0;
                  $totalNORP = 0;
                  $totalPending = 0;
                  foreach($momd as $momdcnt){ 
                    if($momdcnt->mom_approved_status == 'Approved'){
                      $totalApproved++;
                  }else if($momdcnt->mom_approved_status == 'Reject'){
                    $totalReject++;
                  }else if($momdcnt->mom_approved_status == 'NO RP'){
                    $totalNORP++;
                  }elseif(is_null($momdcnt->mom_approved_status)){
                    $totalPending++;
                  }
                  }
                  $totalmomdatastore = $this->Menu_model->get_mombtdate($sd,$ed,$uid,2);
                  $totalmomdatastorecnt = sizeof($totalmomdatastore);

                  $momdpendingnotwrite = $this->Menu_model->get_mombtdate($sd,$ed,$uid,1);
                  $momdpendingnotwritecnt = sizeof($momdpendingnotwrite);
                    ?>


                 
                 <div class="row">
  <table class="table table-bordered text-center">
    <thead class="thead-light">
      <tr>
        <th>Total MOM</th>
        <th>MOM Not Submitted</th>
        <th>Total Approved</th>
        <th>Reject For Reupdate</th>
        <th>NO RP FOUND</th>
        <th>Pending For Check</th>
      </tr>
    </thead>
    <tbody>
      <tr>
        <td><?= $totalmomdatastorecnt; ?></td>
        <td><?= $momdpendingnotwritecnt; ?></td>
        <td><?= $totalApproved; ?></td>
        <td><?= $totalReject; ?></td>
        <td><?= $totalNORP; ?></td>
        <td><?= $totalPending; ?></td>
      </tr>
    </tbody>
  </table>
</div>

                <div class="col-12">
                  <div class="card">
                    <div class="card-header">
                      <h3 class="card-title"></h3>
                    </div>
                    <div class="card-body">
                      <div class="body-content">
                        <div class="page-header">
                          <div class="table-responsive">
                            <div class="pdf-viwer">
                              <table id="example1" class="table table-striped table-bordered" cellspacing="0" width="100%">
                                <thead>
                                  <tr>
                                    <th>#</th>
                                    <th>CIN</th>
                                    <th>Company Name</th>
                                    <th>BD Name</th>
                                    <th>MOM Date</th>
                                    <th>MOM</th>
                                    <th>PST</th>
                                    <th>Current Status</th>
                                    <th>MOM Approved Status</th>
                                    <!-- <th>View Details</th> -->
                                  </tr>
                                </thead>
                                <tbody>
                                  <?php $i=1; foreach($momd as $momd){ ?>
                                  <tr>
                                    <td><?=$i?></td>
                                    <td><?=$momd->cmpid_id?></td>
                                    <td>
                                      <a href="<?=base_url();?>/Menu/CompanyDetails/<?=$momd->cmpid_id?>">
                                        <?=$momd->cname?>
                                    </td>
                                    </td>
                                    <td><?=$momd->name?></td>
                                    <td><?=date('d-m-Y h:i A', strtotime($momd->updateddate));?></td>
                                    <td><?=$momd->mom?></td>
                                    <td><?php if($momd->pst){echo $momd->pst;}else{echo 'PST Not Assign';}?></td>
                                    <td><?=$momd->current_status?></td> 
                                    <td><?php 
                                      if($momd->mom_approved_status == 'Approved'){
                                          echo "<span class='bg-success p-1'>Approved</span>";
                                      }else if($momd->mom_approved_status == 'Reject'){
                                        echo "<span class='bg-danger p-1'>Rejected</span>";
                                      }else if($momd->mom_approved_status == 'NO RP'){
                                        echo "<span class='bg-info p-1'>NO RP</span>";
                                      }elseif(is_null($momd->mom_approved_status)){
                                        echo "<span class='bg-warning p-1'>Pending</span>";
                                      }
                                      
                                      
                                      ?></td> 
                                    <?php /*
                                      <td><a href="<?=base_url();?>/Management/MomDataCheckInAdmin/<?=$momd->mom_id?>/<?=$momd->cmpid_id?>">view</td></td>
                                    */ ?>
                                  </tr>
                                  <?php $i++; } ?>  
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
      // $("#example1").DataTable({
      //   "responsive": false, "lengthChange": false, "autoWidth": false,
      //   "buttons": ["copy", "csv", "excel", "pdf", "print", "colvis"]
      // }).buttons().container().appendTo('#example1_wrapper .col-md-6:eq(0)');
    </script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    <script src="https://html2canvas.hertzen.com/dist/html2canvas.min.js"></script>
    <script>
      $(document).ready(function () {
      
          $.ajax({
          url: '<?= base_url(); ?>Menu/CheckPlannerlogReportExistsOrNot',
          type: "POST",
          data: {
            filetypesname: 'AllMomDetails',
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
                  console.log("Planner Log Report Allready Store");
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
                  filename: 'AllMomDetails.pdf',
                  filetypesname: 'AllMomDetails',
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
    <script>
      $(document).ready(function () {
        $("#printPage").click(function() {
              window.print();
          });
        })
    </script>
  </body>
</html>
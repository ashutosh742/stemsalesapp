<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="wi$dth=device-wi$dth, initial-scale=1">
    <title>TaskCheck Report | STEM APP | WebAPP</title>
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
        <section class="content" id="pdf-content">
<div class="text-right mt-2"><button class="btn btn-info" id="printPage">Print Page</button> <br><br>
          <div class="container-fluid">
       
            <div class="row">
              <div class="col-12">
                <div class="card">
                  <div class="card-header">
                    <h3 class="text-center">
                      <center><b>Task Check Management Report</b></center>
                    </h3>
                    <center>Date - <?=$sdate?></center>
                  </div>
                  <!-- /.card-header -->
                  <div class="card-body">
                    <div class="body-content">
                      <div class="page-header">
                        <?php 


                          // Main heading
                                foreach ($groupedData as $mainHeading => $subArray) {
                                    $slsuser = $this->Menu_model->get_userbyid($mainHeading);
                                    $slctname = $slsuser[0]->name; ?>
                                     <div class="card text-center" >
                                    <div class="card text-center" style="background: crimson;color: white;">
                                        <h3><?= $slctname; ?></h3>
                                    </div>
                                   <?php 

                                    // Sub-heading
                                    foreach ($subArray as $subHeading => $items) {
                                        $taskData            = $this->Menu_model->GetTaskDetailsByTid($subHeading);
                                  // dd($taskData);
                                        $compname            = $taskData[0]->compname;
                                        $current_status_name = $taskData[0]->current_status_name;
                                        $tasktime_status     = $taskData[0]->tasktime_status;
                                        $task_name           = $taskData[0]->task_name;
                                        $appointmentdatetime           = $taskData[0]->appointmentdatetime;
                                        $updateddate           = $taskData[0]->updateddate;
                                        $initiateddt           = $taskData[0]->initiateddt;
                                        $actontaken           = $taskData[0]->actontaken;
                                        $purpose_achieved           = $taskData[0]->purpose_achieved;
                                        $tasktime_new_status           = $taskData[0]->tasktime_new_status;
                                        $task_user_name           = $taskData[0]->task_user_name;
                                        $task_approve_user_name           = $taskData[0]->task_approve_user_name;
                                        $feedbackBy           = $items[0]->feedbackBy; 
                                        
                                        ?>
                        <table class="table">
                          <thead class="thead-dark">
                            <tr>
                              <th >#</th>
                              <th >BD Name</th>
                              <th >Task Name</th>
                              <th >Company Name</th>
                              <th >Appointment Datetime</th>
                              <th >Initiated Datetime</th>
                              <th >Update Datetime</th>
                              <th >Action Taken</th>
                              <th >Purpose Achieved</th>
                              <th >Current Status</th>
                              <th >Task Time Status</th>
                              <th >Status Change</th>
                              <th >Task Approved Name</th>
                              <th >Feedback By</th>

                            </tr>
                          </thead>
                          <tbody>
                            <tr>
                              <th scope="row">1</th>
                              <td><?= $task_user_name; ?></td>
                              <td><?= $task_name; ?></td>
                              <td><?= $compname; ?></td>
                              <td><?= $appointmentdatetime; ?></td>
                              <td><?= $initiateddt; ?></td>
                              <td><?= $updateddate; ?></td>
                              <td><?= $actontaken; ?></td>
                              <td><?= $purpose_achieved; ?></td>
                              <td><?= $current_status_name; ?></td>
                              <td><?= $tasktime_status; ?></td>
                              <td><?php 
                              if (!empty($tasktime_new_status)) {
                                echo $tasktime_new_status;
                            } else {
                                echo "<span class='bg-warning p-1'>No&nbsp;Status&nbsp;Change</span>";
                            }
                            
                              ?></td>
                               <td><?= $task_approve_user_name; ?></td>
                               <td><?= $feedbackBy; ?></td>

                            </tr>
                          </tbody>
                        </table>
                        <?php
                          // Display data in a table
                          echo "<table class='table table-bordered'>";
                          echo "<thead class=''>
                                  <tr>
                                              <th >#</th>
                                              <th >Question</th>
                                              <th >Rating </th>
                                              <th >Late Remarks If Any</th>
                                  </tr>
                                </thead>";
                          echo "<tbody>";
                          $i=1;
                          foreach ($items as $item) {                          
                                  echo "<tr>
                                          <td>    $i</td>
                                          <td>    $item->question</td>
                                          <td>" . $item->star . "</td>
                                          <td>" . $item->late_remarks_message . "</td>
                                        </tr>";
                            $i++;  
                          }
                          
                          echo "</tbody>";
                          echo "</table>";
                          }
                          echo "</div>";
                          }
                          ?>
                      
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
    /*
        $.ajax({
        url: '<?= base_url(); ?>Menu/CheckPlannerlogReportExistsOrNot',
        type: "POST",
        data: {
          filetypesname: 'TaskCheckManagementReport',
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
                filename: 'TaskCheckManagementReport.pdf',
                filetypesname: 'TaskCheckManagementReport',
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
*/


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
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="wi$dth=device-wi$dth, initial-scale=1">
    <title>All Compulsive AndNeed YourAttention Log | STEM APP | WebAPP</title>
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
      
    </style>
  </head>
  <body class="hold-transition sidebar-mini layout-fixed">
    <div class="wrapper">
      <!-- Preloader -->
      <!-- Navbar -->
      <?php require('nav.php');?>
      <?php //include 'addlog.php';?>

      <style>
        .card.pinkbackground{background: #ff1d58!important; color: white;}
      </style>

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
            <div class="row">
              <div class="col-md-10 text-center p-2">
                <div class="card-header bg-primary" style="align-items: center; justify-content: center; display: flex ;">
                  <h2>Todays Compulsive & Need Your Attention Log</h2>
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
                  <div class="card-body">
                    <div class="text-left">
                    <?php 
                       $getAllUser       = 0;
                       $totalcompanylog  = 0;
                       $compulsive       = 0;
                       $needYourAtten    = 0;
                       $target_date = Date("Y-m-d");
                      foreach ($allUsers as $susers){
                        $susers_userid = $susers->user_id;
                        $alllogData   =   $this->Menu_model->GetAllCompulsiveAndNeedYourAttentionByuid($susers_userid,$target_date);
                        $alllogDatacnt = sizeof($alllogData);
                        if($alllogDatacnt > 0){
                           $getAllUser++;
                           $totalcompanylog += sizeof($alllogData);
                           foreach($alllogData as $datalog):
                            $alllogData_remarks = $datalog->remarks;
                            if($alllogData_remarks == 'Need Your Attension'){$needYourAtten++;}
                            if($alllogData_remarks == 'Compulsive Task'){$compulsive++;}
                           endforeach;
                           
                        }
                      }
                        ?>
                    </div>
                    <div class="body-content">

                    <div class="row">
                      <div class="col-md-3">
                        <div class="card text-center table-primary p-2 pinkbackground">
                          <h4>Total User</h4>
                          <hr>
                           <h5><?=$getAllUser?></h5>
                        </div>
                      </div>
                      <div class="col-md-3">
                      <div class="card text-center table-primary p-2 pinkbackground">
                          <h4>Total Log</h4>
                          <hr>
                           <h5><?=$totalcompanylog?></h5>
                        </div>
                      </div>
                      <div class="col-md-3">
                      <div class="card text-center table-primary p-2 pinkbackground">
                          <h4>Total Compulsive Task</h4>
                          <hr>
                           <h5><?=$compulsive?></h5>
                        </div>
                      </div>
                      <div class="col-md-3">
                      <div class="card text-center table-primary p-2 pinkbackground">
                          <h4>Total Need Your Attension</h4>
                          <hr>
                           <h5><?=$needYourAtten?></h5>
                        </div>
                      </div>
                    </div>

                      <div class="page-header">
                        <?php 
                          $target_date = Date("Y-m-d");
                          foreach ($allUsers as $susers){
                            $susers_userid = $susers->user_id;
                            $alllogData   =   $this->Menu_model->GetAllCompulsiveAndNeedYourAttentionByuid($susers_userid,$target_date);
                            $alllogDatacnt = sizeof($alllogData);
                            if($alllogDatacnt > 0){
                              $getAllUser++;
                              
                                // $susers_typesid = $susers->type_id;
                                // $susers_typename   =   $this->Menu_model->get_user_types($susers_typesid)[0]->name;
                            ?>
                        <div class="table-responsive">
                          <table id="example1" class="table table-striped table-bordered" cellspacing="0" wi$dth="100%">
                            <thead style="background: #ff0000; color: white;">
                              <tr>
                                <th>S.No.</th>
                                <th>BD Name</th>
                                <th>Apply Filter By</th>
                                <th>Filter Name</th>
                                <th>Company Name</th>
                                <th>Log Create</th>
                                <th>Total Log </th>
                                <th>Last Task By User Name</th>
                                <th>Last Task Name</th>
                                <th>Last Task Update</th>
                                <th>Last Task Days</th>
                                <th>Last Task Remarks</th>
                                <th>Cureent Status</th>
                              </tr>
                            </thead>
                            <tbody>
                              <?php $i=1; foreach($alllogData as $data):
                                $udetail = $this->Menu_model->get_userbyid($data->user_id);
                                $apply_log = $udetail[0]->name;
                                ?>
                              <tr>
                                <td><?= $i; ?></td>
                                <td><?= $data->mainbd_name; ?></td>
                                <td><?= $apply_log; ?></td>
                                <td><?= $data->remarks; ?></td>
                                <td><a href="<?=base_url();?>Menu/CompanyDetails/<?=$data->cid;  ?>" target="_BLANK"><?= $data->compname; ?></a></td>
                                <td><?= $data->create_date; ?></td>
                                <td><?= $data->init_id_count; ?></td>
                                <td><?= $data->last_task_byuser; ?></td>
                                <td><?= $data->last_task_name; ?></td>
                                <td><?= $data->last_task_update; ?></td>
                                <td><?= $data->last_task_days; ?></td>
                                <td><?= $data->last_task_remarks; ?></td>
                                <td><?php echo $statusName = $this->Menu_model->get_statusbyid($data->cstatus)[0]->name;?></td>
                              </tr>
                              <?php $i++; endforeach; ?>
                            </tbody>
                          </table>
                        </div>
                        <?php }} ?>
                        <hr />
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
      </div>
    </div>
    </div>
    </section>
    <footer class="main-footer">
      <strong>Copyright &copy; 2021-2022 <a href="<?=base_url();?>">Stemlearning</a>.</strong>
      All rights reserved.
      <div class="float-right d-none d-sm-inline-block">
        <b>Version</b> 1.0
      </div>
    </footer>
    <aside class="control-sidebar control-sidebar-dark">
    </aside>
    </div>
    <script src="<?=base_url();?>assets/js/jquery.min.js"></script>
    <!-- jQuery UI 1.11.4 -->
    <script src="<?=base_url();?>assets/js/jquery-ui.min.js"></script>
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

    <script src="<?=base_url();?>assets/js/adminlte.js"></script>
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
          filetypesname: 'AllCompulsiveAndNeedYourAttentionLog',
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
                filename: 'AllPlannerLogPlannedByUsers.pdf',
                filetypesname: 'AllCompulsiveAndNeedYourAttentionLog',
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
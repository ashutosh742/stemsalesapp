<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="wi$dth=device-wi$dth, initial-scale=1">
  <title>Get All Planner Log Planned By Users | STEM APP | WebAPP</title>

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
 <?php $this->load->view($dep_name.'/nav.php');?>
  <?php //include 'addlog.php';?>
  <!-- /.navbar -->

  <style>
    .dataTables_wrapper .dataTables_paginate .paginate_button {
   padding: 0;
    }
       .custom-btn {
        /* width: 130px;
        height: 40px; */
        color: #fff;
        border-radius: 5px;
        /* padding: 10px 25px; */
        font-family: Lato, sans-serif;
        font-weight: 500;
        background: 0 0;
        cursor: pointer;
        transition: .3s;
        position: relative;
        display: inline-block;
        box-shadow: rgba(9, 30, 66, 0.25) 0px 4px 8px -2px, rgba(9, 30, 66, 0.08) 0px 0px 0px 1px;
        outline: 0;
        }
        .btn-11 {
        border: none;
        background: #212ffb;
        background: linear-gradient(0deg, #3e21fb 0, #4c5cea 100%);
        color: #fff;
        overflow: hidden;
        width: fit-content;
        }
        .btn-11:hover {
        text-decoration: none;
        color: #fff;
        opacity: .7;
        }
        .btn-11:before {
        position: absolute;
        content: '';
        display: inline-block;
        top: -180px;
        left: 0;
        width: 30px;
        height: 100%;
        background-color: #fff;
        animation: 3s ease-in-out infinite shiny-btn1;
        }
        .btn-11:active {
        box-shadow: 4px 4px 6px 0 rgba(255,255,255,.3), -4px -4px 6px 0 rgba(116,125,136,.2), inset -4px -4px 6px 0 rgba(255,255,255,.2), inset 4px 4px 6px 0 rgba(0,0,0,.2);
        }
        @keyframes shiny-btn1 {
        0% { transform: scale(0) rotate(45deg); opacity: 0; }
        80% { transform: scale(0) rotate(45deg); opacity: .5; }
        81% { transform: scale(4) rotate(45deg); opacity: 1; }
        100% { transform: scale(50) rotate(45deg); opacity: 0; }
        }
        .btn-11.partnearray{
        background: navy!important;
        }
        .btn-11.categoryarray{
        background: #ff007f!important;
        }
  </style>

  

  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1 class="m-0"></h1>
          </div><!-- /.col -->
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <h4></h4>
            </ol>
          </div><!-- /.col -->
        </div><!-- /.row -->
      </div><!-- /.container-fluid -->
    </div>

    <section class="content">
      <div class="container-fluid" style="background: linear-gradient(to right, #fff8e1, #ffffff); border-radius: 12px; box-shadow: 0 4px 8px rgba(0,0,0,0.1); padding: 20px;">

        <?php // dd($alllogData); ?>

        <div class="row">
          <div class="col-12">
            <div class="card" style="background: linear-gradient(to right, #dfe9f3, #ffffff); border-radius: 12px; box-shadow: 0 4px 8px rgba(0,0,0,0.1); padding: 20px;">
              <div class="card-header bg-primary p-2" style="align-items: center; justify-content: center; display: flex ;">
                <h3 class="card-title">All Planner Log Planned By Users</h3>
               
              </div>
               <?php if($targetuidDatas  !=0){ ?>
                  <hr>
                 <center> <mark><?=$targetuidDatas[0]->name;?></mark></center>
                  <?php } ?>
              <div class="card-body">
                <div class="text-center">
                <?php 
            $ctype_id = $user['type_id'];
            // $bddata = $this->Menu_model->GetAllUserByAdminRole($ctype_id,$uid);
            // dd($uniqueUsers);
            ?>

              <center>
                 <form class="p-3" method="POST" action="<?=base_url();?>/Menu/GetAllPlannerLogPlannedByUsers">
                  <div class="row">
                    <div class="col-md-4" style="align-items: center; justify-content: center; display: flex ;">
                    <select name="bdid" class="form-control">
                      <option value="">Select User</option>
                      <?php foreach ($uniqueUsers as $keyid => $userinfo) {
                          ?>
                          <option value="<?= $keyid ?>">
                              <?= $userinfo ?> 
                          </option>
                      <?php } ?>
                  </select>
                    </div>
                    <div class="col-md-2" style="align-items: left; justify-content: left; display: flex ;">
                    <button type="submit" class="btn btn-primary text-light">Filter</button>
                    </div>
                
                  </div>
                </form>
              </center>


                </div>
                  <div class="body-content">
  <div class="row mt-3 mb-2">
                    <div class="col-md-12 text-center">
                      <button class="custom-btn btn-11 partnearray p-2" id="downloadPdf">Download PDF</button>
                    </div>
                  </div>
                  <hr>
                  <?php 
                  
                  // dd($resultCount);
                  
                  ?>

                  <div class="row">
                      <?php foreach ($resultCount as $key => $resultCounts) { 
                          $r = rand(150, 255);
                          $g = rand(150, 255);
                          $b = rand(150, 255);
                          $backgroundColor = "rgba($r, $g, $b,.3)";

                          $hue = rand(0, 360);
                          $saturation = rand(60, 100);
                          $lightness = rand(30, 45);
                          $backgroundColorNew = "hsl($hue, $saturation%, $lightness%)";

                          $to_user = $resultCounts['to_user'];
                      ?>
                          <div class="col-md-3 mb-3">
                              <div class="card text-center shadow" style="background-color: <?= $backgroundColor ?>;">
                                  <div class="card-body" style="color: <?= $backgroundColorNew ?>;">
                                      <h5 class="mb-3"><b><?= $resultCounts['to_user_name'] ?></b></h5>
                                      <hr>
                                      <a target="_blank" 
                                        class="card-count-heading-new" 
                                        style="color: <?= $backgroundColorNew ?> !important" 
                                        href="<?= base_url() ?>Menu/GetAllPlannerLogPlannedByUsers/<?= $to_user ?>">
                                          <h3 class="card-textbadge badge-pill badge-dark"><?= $resultCounts['task_count'] ?></h3>
                                      </a>
                                  </div>
                              </div>
                          </div>
                      <?php } ?>
                  </div>



                 
                  <hr>












            <div class="page-header">
            <div class="form-group">
                <fieldset>
                    <form action="" class="form-group" method="post">
                        <div class="table-responsive">
                            <div class="table-responsive text-nowrap">     
                                <table id="example1" class="table table-striped table-bordered" cellspacing="0" wi$dth="100%">
                                    <thead class="thead-dark">
                                        <tr>
                                            <th>S.No.</th>
                                            <th>BD Name</th>
                                            <th>Log By</th>
                                            <th>Company Name</th>
                                            <th>Task Name</th>
                                            <th>Orginal Task Create Date</th>
                                            <th>Recent Task Update Date</th>
                                            <th>Current Status</th>
                                            <th>Replaned Times </th>
                                            <th>Time Difference</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php $i=1; foreach($alllogData as $data): ?>
                                        <tr>
                                            <td><?= $i; ?></td>
                                            <td><?= $data->to_user_name; ?></td>
                                            <td><?= $data->remarks; ?></td>
                                            <td><a href="<?=base_url();?>Menu/CompanyDetails/<?=$data->cid;  ?>" target="_BLANK"><?= $data->company_name; ?></a></td>
                                            <td><?= $data->task_name; ?></td>
                                            <td><?= $data->org_task_date; ?></td>
                                            <td><?= $data->last_create_date; ?></td>
                                            <td><?= $data->name; ?></td>
                                            <td><a href="<?=base_url();?>Menu/ReplanedLog/<?=$data->task_id;  ?>" target="_BLANK"><?= $data->duplicate_count; ?></a></td>
                                            <td><?= $data->time_difference; ?></td>
                                        </tr>
                                        <?php $i++; endforeach; ?>
                                  </tbody>
                                </table>
                            </div>
                        </div>
                    </form>           
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


     <?php if($targetuidDatas  !=0){ 
            $targetuidDatasName =  $targetuidDatas[0]->name; 
     }else{
      $targetuidDatasName =  $user['name'].'_All'; 
     } ?>
    
    
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
        <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.4.0/jspdf.umd.min.js"></script>
<script>
//     $("#example1").DataTable({
// "responsive": false, "lengthChange": false, "autoWidth": false,
// "buttons": ["copy", "csv", "excel"]
// }).buttons().container().appendTo('#example1_wrapper .col-md-6:eq(0)');


$("#example1").DataTable({
    "responsive": false,
    "lengthChange": false,
    "autoWidth": false,
    "buttons": [
        {
            extend: 'csv',
            text: 'Export CSV',
            title: '<?=$targetuidDatasName?>_Replanned_Planner_Log', // Optional title for the CSV file
        },
        {
            extend: 'excel',
            text: 'Export Excel',
            title: '<?=$targetuidDatasName?>_Replanned_Planner_Log', // Optional title for the Excel file
        },
        {
            extend: 'pdf',
            text: 'Export PDF',
            title: '<?=$targetuidDatasName?>_Replanned_Planner_Log', // Optional title for the PDF file
            exportOptions: {
                // This ensures that all data is exported, not just the visible data
                modifier: {
                    page: 'all' // Export all pages
                }
            }
        }
    ]
}).buttons().container().appendTo('#example1_wrapper .col-md-6:eq(0)');




</script>
 <script>

       $(document).ready(function () {
             $('#downloadPdf').click(function () {
               const button = $(this);
               button.prop('disabled', true).text('Please wait...');
           
               const { jsPDF } = window.jspdf;
               const doc = new jsPDF('p', 'mm', 'a4');
               const content = document.querySelector('.content-wrapper');
           
               html2canvas(content, {
                 scale: 2,
               }).then(canvas => {
                 const imgData = canvas.toDataURL('image/png');
                 const imgWidth = 210;
                 const pageHeight = 297;
                 const imgHeight = canvas.height * imgWidth / canvas.width;
           
                 let heightLeft = imgHeight;
                 let position = 0;
           
                 doc.addImage(imgData, 'PNG', 0, position, imgWidth, imgHeight);
                 heightLeft -= pageHeight;
           
                 while (heightLeft > 0) {
                   position -= pageHeight;
                   doc.addPage();
                   doc.addImage(imgData, 'PNG', 0, position, imgWidth, imgHeight);
                   heightLeft -= pageHeight;
                 }
           
                 doc.save('dashboard.pdf');
                 // Revert button
                 button.prop('disabled', false).text('Download PDF');
               }).catch(error => {
                 console.error('PDF generation failed:', error);
                 button.prop('disabled', false).text('Download PDF');
               });
             });
           });


    </script>
</body>
</html>
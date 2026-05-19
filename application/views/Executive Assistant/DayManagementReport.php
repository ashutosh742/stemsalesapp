
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
      
        .dataTables_wrapper .dt-buttons .btn {
            /* background-color: #007bff; Change button background color */
            color: white; /* Change button text color */
            border: none; /* Remove border */
            padding: 10px 20px; /* Add padding */
            border-radius: 5px; /* Add rounded corners */
            margin-bottom:5px;
        }

        .dataTables_wrapper .dt-buttons .btn:hover {
            background-color: #0056b3; /* Change button color on hover */
        }

        .buttons-csv {
            margin-right: 10px; /* Adjust the value as needed */
        }
        .buttons-excel{
            margin-right: 10px; /* Adjust the value as needed */
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
        <section class="content">
          <div class="container-fluid">
            <div class="row">
              <div class="col-12">
                <div class="card">
                  <div class="card-header">
                    <h3 class="text-center">
                      <center><b>Day Check Management Report</b></center>
                    </h3>
                    <div class="card-body FilterSection">
                            <form method="POST"
                                action="<?= base_url(); ?>Management/DayManagementReport/<?php ?>">
                                <div class="row">
                                    <div class="col-lg-3 col-sm-6">
                                        <label for="startDate">Start Date</label>
                                        <input id="startDate" name="startDate" class="form-control" type="date"
                                            value="<?= $sdate ?>" />
                                    </div>
                                    <div class="col-lg-3 col-sm-6">
                                        <label for="endDate">End Date</label>
                                        <input id="endDate" class="form-control" name="endDate" type="date"
                                            value="<?= $edate ?>" />
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label>Select User</label>
                                            <select class="custom-select rounded-0" name="user[]" id="user" required>
                                                <option value="select_all">Select All</option>
                                                <?php foreach ($users as $users) { ?>
                                                    <option value="<?= $users->user_id ?>" <?= in_array($users->user_id, $selected_user) ? 'selected' : '' ?>>
                                                        <?= htmlspecialchars($users->name) ?></option>
                                                <?php } ?>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-lg-3 col-sm-6">
                                        <br>
                                        <button type="submit" name="submit" class="btn btn-primary"> Filter</button>
                                    </div>
                                </div>
                            </form>
                    </div>
                  </div>
                  <!-- /.card-header -->

                    <?php 
                    
                    // var_dump($getReportbyUser);die;
                    // var_dump($getReportbyUser);die;
                    // echo $getReportbyUser->date;
                    
                    ?>
                    <div class="card">
                        <!-- <div class="card card-header">
                            <center><h5><?php //if(!empty($getReportbyUser)){}?></h5></center> 
                            <center><h5>(Morning Period Rating)</center><br>
                        </div> -->
                        <div class="card-body">
                            <div class="container body-content">
                                <div class="page-header">
                                <fieldset>
                                    <div class="table-responsive">
                                        <div class="table-responsive">
                                            <div class="pdf-viwer">
                                            <?php
                                             
if (!empty($getReportbyUser) && is_array($getReportbyUser)) {
    $i = 1;
                                                foreach ($getReportbyUser as $date => $periods):
                                                ?>
                                                    <center><h2>Date: <?php echo htmlspecialchars($date); ?></h2></center>
                                                        <?php foreach ($periods as $period => $records): ?>
                                                            <h4>Period: <?php echo htmlspecialchars($period); ?></h4>
                                                                <table class="table data-table" id="example_<?= $i ?>">
                                                                    <thead>
                                                                        <tr>
                                                                            <th>Question</th>
                                                                            <th>Star Rating</th>
                                                                            <th>Remarks</th>
                                                                            <th>Feedback By</th>
                                                                        </tr>
                                                                    </thead>
                                                                    <tbody>
                                                                        <?php foreach ($records as $record): ?>
                                                                            <tr>
                                                                                <td><?php echo htmlspecialchars($record->question); ?></td>
                                                                                <td><?php echo htmlspecialchars($record->star); ?></td>
                                                                                <td><?php echo htmlspecialchars($record->remarks); ?></td>
                                                                                <td><?php echo htmlspecialchars($record->feedbackBy); ?></td>
                                                                            </tr>
                                                                        <?php endforeach; ?>
                                                                    </tbody>
                                                                </table>
                                                        <?php endforeach; ?>
                                                <?php
                                                    $i++;
                                                endforeach;
}
                                                ?>

                                            </div>
                                        </div>
                                    </div>  
                                    </fieldset>
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
    
    <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.16.9/xlsx.full.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.4.0/jspdf.umd.min.js"></script>
 <script>
$(document).ready(function() {
    // Initialize DataTables with Export Buttons for each table
    $('table[id^="example_"]').each(function() {
        $(this).DataTable({
            dom: 'Bfrtip',
            buttons: [
                 'excel', 'pdf'
            ],
            // Add your other DataTables options here if needed
        });
    });
});
</script> 
// <script>
//     $(document).ready(function() {
//         // const userName = "<?php // echo htmlspecialchars($getReportbyUser[0]->userName); ?>";
//         const userName = "test";

//         // Initialize DataTables with export buttons for all tables with class 'data-table'
//         $('.data-table').each(function() {
//             $(this).DataTable({
//                 dom: 'Bfrtip',
//                 buttons: [
//                     {
//                         extend: 'csvHtml5',
//                         className: 'btn btn-info',
//                         text: '<i class="fa fa-file-csv"></i> Export All Tables to CSV',
//                         action: function (e, dt, button, config) {
//                             exportAllTables('csv');
//                         }
//                     },
//                     {
//                         extend: 'excelHtml5',
//                         className: 'btn btn-info',
//                         text: '<i class="fa fa-file-excel"></i> Export All Tables to Excel',
//                         action: function (e, dt, button, config) {
//                             exportAllTables('excel');
//                         }
//                     },
//                     {
//                         extend: 'pdfHtml5',
//                         className: 'btn btn-info',
//                         text: '<i class="fa fa-file-pdf"></i> Export All Tables to PDF',
//                         action: function (e, dt, button, config) {
//                             exportAllTables('pdf');
//                         }
//                     }
//                 ]
//             });
//         });

//         function exportAllTables(type) {
//             let allData = [];

//             // Collect data from all tables with class 'data-table'
//             $('.data-table').each(function() {
//                 const table = $(this).DataTable();
//                 let tableData = [];
                
//                 if (table) {
//                     let headers = table.columns().header().toArray().map(header => $(header).text());
//                     tableData.push(headers);

//                     table.rows({ search: 'applied' }).every(function() {
//                         tableData.push(this.data());
//                     });
//                 }
//                 allData = allData.concat(tableData);
//             });

//             if (type === 'csv') {
//                 exportToCSV(allData);
//             } else if (type === 'excel') {
//                 exportToExcel(allData);
//             } else if (type === 'pdf') {
//                 exportToPDF(allData);
//             }
//         }

//         function exportToCSV(data) {
//             const csv = data.map(row => row.join(',')).join('\n');
//             const csvBlob = new Blob([csv], { type: 'text/csv' });
//             const url = URL.createObjectURL(csvBlob);
//             const a = document.createElement('a');
//             a.href = url;
//             a.download = `${userName}_.csv`;
//             a.click();
//         }

//         function exportToExcel(data) {
//             const ws = XLSX.utils.aoa_to_sheet(data);
//             const wb = XLSX.utils.book_new();
//             XLSX.utils.book_append_sheet(wb, ws, "Merged Tables");
//             XLSX.writeFile(wb, `${userName}_.xlsx`);
//         }

//         function exportToPDF(data) {
//             const { jsPDF } = window.jspdf;
//             const doc = new jsPDF();
//             let y = 10;
            
//             data.forEach(row => {
//                 doc.text(row.join(' '), 10, y);
//                 y += 10;
//             });

//             doc.save(`${userName}_.pdf`);
//         }
//     });
// </script>
  </body>
</html>

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
        body {
            font-family: 'Source Sans Pro', sans-serif;
            background-color: #f4f6f9;
        }
        .scrollme {
            overflow-x: auto;
        }
        .card {
            border: none;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1), 0 1px 3px rgba(0, 0, 0, 0.08);
            border-radius: 10px;
            transition: box-shadow 0.3s ease-in-out;
        }
        .card:hover {
            box-shadow: 0 8px 12px rgba(0, 0, 0, 0.15), 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        .card-header {
            background-color: #007bff;
            color: white;
            border-top-left-radius: 10px;
            border-top-right-radius: 10px;
        }
        .main-footer {
            background-color: #343a40;
            color: white;
            padding: 15px;
            border-top: 1px solid #495057;
        }
        .main-footer a {
            color: #007bff;
        }
        .content-wrapper {
            padding: 20px;
        }
        .card-body {
            padding: 20px;
        }
        .page-header h3 {
            font-size: 1.75rem;
            font-weight: 600;
        }
        .btn-primary {
            background-color: #007bff;
            border-color: #007bff;
        }
        .btn-primary:hover {
            background-color: #0056b3;
            border-color: #0056b3;
        }
        .filter-form {
            position: absolute;
            top: 20px;
            right: 20px;
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
            <br>
            <!-- Filter Form -->

            <!-- /.content-header -->
            <!-- Main content -->
            <section class="content">
                <div class="container-fluid">
<?php
// Initialize an empty array to store unique user data
$uniqueUsers = [];

// Iterate through the data array
foreach ($meetDatass as $item) {
    // Check if the user_id is already in the uniqueUsers array
    $userExists = false;
    foreach ($uniqueUsers as $user) {
        if ($user->user_id == $item->user_id) {
            $userExists = true;
            break;
        }
    }

    // If the user_id is not already in the uniqueUsers array, add it
    if (!$userExists) {
        $uniqueUsers[] = (object) [
            'user_id' => $item->user_id,
            'name' => $item->name
        ];
    }
}

?>
                    <div class="row">
                        <div class="col-12">
                            <div class="card">
                           <div class="text-left">
                           <form class="p-3" method="POST" action="<?=base_url()?>/Menu/TodaysUserTotalTimeSpentByUrlPath">
                                <input type="date" name="tdate" class="mr-2" value="<?=$tdate?>">

                                <select name="tuser_id" class='p-1' id="tuser_id">
                                  <option value="all">All</option>
                                  <?php foreach ($uniqueUsers as $uniqueUser) { ?>
                                  <option value="<?= $uniqueUser->user_id; ?>"><?= $uniqueUser->name; ?></option>
                                  <?php } ?>
                                </select>
                                <button type="submit" class="bg-primary text-light">Filter</button>
                            </form>
                            </div>
                                <div class="card-header">
                                    <h3 class="text-center m-3">User Time Spent By Single URL Path</h3>
                                    <h4 class="text-center m-3"><?=$tdate?></h4>

                                </div>
                                <!-- /.card-header -->
                                <div class="card-body">
                                    <div class="body-content">
                                        <div class="page-header">
                                            <div class="table-responsive text-nowrap">
                                                <table id="example1" class="table table-striped table-bordered" cellspacing="0" width="100%">
                                                    <thead class='thead-dark'>
                                                        <tr>
                                                            <th>S.No</th>
                                                            <th>Name</th>
                                                            <th>Event Date</th>
                                                            <th>Link</th>
                                                            <th>Event Start Time</th>
                                                            <th>Event Close Time</th>
                                                            <th>Total Spent Time</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <?php
                                                        $i = 1;
                                                        foreach ($meetDatas as $meetData) {
                                                        ?>
                                                            <tr>
                                                                <td><?= $i ?></td>
                                                                <td><?= $meetData->name ?></td>
                                                                <td><?= $meetData->event_date ?></td>
                                                                <td><?= $meetData->url_path ?></td>
                                                                <td><?= $meetData->event_start_time ?></td>
                                                                <td><?= $meetData->event_close_time ?></td>
                                                                <td><?= $meetData->total_time_formatted ?></td>
                                                            </tr>
                                                        <?php $i++;
                                                        } ?>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- /.card-body -->
                    </div>

                    <div class="row">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header">
                                    <h3 class="text-center m-3">User Total Time Spent By URL Path</h3>
                                    <h4 class="text-center m-3"><?=$tdate?></h4>
                                </div>
                                <!-- /.card-header -->
                                <div class="card-body">
                                    <div class="body-content">
                                        <div class="page-header">
                                            <div class="table-responsive text-nowrap">
                                                <table id="example2" class="table table-striped table-bordered" cellspacing="0" width="100%">
                                                    <thead class='thead-dark'>
                                                        <tr>
                                                            <th>S.No</th>
                                                            <th>Name</th>
                                                            <th>Event Date</th>
                                                            <th>Link</th>
                                                            <th>Event Start Time</th>
                                                            <th>Event Close Time</th>
                                                            <th>Total Spent Time</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <?php
                                                        $i = 1;
                                                        foreach ($meetDatass as $meetData) {
                                                        ?>
                                                            <tr>
                                                                <td><?= $i ?></td>
                                                                <td><?= $meetData->name ?></td>
                                                                <td><?= $meetData->event_date ?></td>
                                                                <td><?= $meetData->url_path ?></td>
                                                                <td><?= $meetData->event_start_time ?></td>
                                                                <td><?= $meetData->event_close_time ?></td>
                                                                <td><?= $meetData->total_time_formatted ?></td>
                                                            </tr>
                                                        <?php $i++;
                                                        } ?>
                                                    </tbody>
                                                </table>
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
        <strong>Copyright &copy; 2021-2022 <a href="<?= base_url(); ?>">Stemlearning</a>.</strong>
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
<script src="<?= base_url(); ?>assets/js/jquery.min.js"></script>
<!-- jQuery UI 1.11.4 -->
<script src="<?= base_url(); ?>assets/js/jquery-ui.min.js"></script>
<!-- Resolve conflict in jQuery UI tooltip with Bootstrap tooltip -->
<script>
    $.widget.bridge('uibutton', $.ui.button)
</script>
<!-- Bootstrap 4 -->
<script src="<?= base_url(); ?>assets/js/bootstrap.bundle.min.js"></script>
<!-- ChartJS -->
<script src="<?= base_url(); ?>assets/js/Chart.min.js"></script>
<!-- Sparkline -->
<script src="<?= base_url(); ?>assets/js/sparkline.js"></script>
<!-- JQVMap -->
<script src="<?= base_url(); ?>assets/js/jquery.vmap.min.js"></script>
<script src="<?= base_url(); ?>assets/js/jquery.vmap.usa.js"></script>
<!-- jQuery Knob Chart -->
<script src="plugins/jquery-knob/jquery.knob.min.js"></script>
<!-- daterangepicker -->
<script src="<?= base_url(); ?>assets/js/moment.min.js"></script>
<script src="<?= base_url(); ?>assets/js/daterangepicker.js"></script>
<!-- Tempusdominus Bootstrap 4 -->
<script src="<?= base_url(); ?>assets/js/tempusdominus-bootstrap-4.min.js"></script>
<!-- Summernote -->
<script src="<?= base_url(); ?>assets/js/summernote-bs4.min.js"></script>
<!-- overlayScrollbars -->
<script src="<?= base_url(); ?>assets/js/jquery.overlayScrollbars.min.js"></script>
<!-- DataTables  & Plugins -->
<script src="<?= base_url(); ?>assets/js/jquery.dataTables.min.js"></script>
<script src="<?= base_url(); ?>assets/js/dataTables.bootstrap4.min.js"></script>
<script src="<?= base_url(); ?>assets/js/dataTables.responsive.min.js"></script>
<script src="<?= base_url(); ?>assets/js/responsive.bootstrap4.min.js"></script>
<script src="<?= base_url(); ?>assets/js/dataTables.buttons.min.js"></script>
<script src="<?= base_url(); ?>assets/js/buttons.bootstrap4.min.js"></script>
<script src="<?= base_url(); ?>assets/js/jszip.min.js"></script>
<script src="<?= base_url(); ?>assets/js/pdfmake.min.js"></script>
<script src="<?= base_url(); ?>assets/js/vfs_fonts.js"></script>
<script src="<?= base_url(); ?>assets/js/buttons.html5.min.js"></script>
<script src="<?= base_url(); ?>assets/js/buttons.print.min.js"></script>
<script src="<?= base_url(); ?>assets/js/buttons.colVis.min.js"></script>
<!-- AdminLTE App -->
<script src="<?= base_url(); ?>assets/js/adminlte.js"></script>
<!-- AdminLTE dashboard demo (This is only for demo purposes) -->
<script src="<?= base_url(); ?>assets/js/dashboard.js"></script>
<script>
    $(function () {
        $('#datepicker').datetimepicker({
            format: 'L'
        });
    });

    $("#example1").DataTable({
        "responsive": false, "lengthChange": false, "autoWidth": false,
        "buttons": ["csv", "excel", "pdf", "print"]
    }).buttons().container().appendTo('#example1_wrapper .col-md-6:eq(0)');
    $("#example2").DataTable({
        "responsive": false, "lengthChange": false, "autoWidth": false,
        "buttons": ["csv", "excel", "pdf", "print"]
    }).buttons().container().appendTo('#example2_wrapper .col-md-6:eq(0)');

    $(document).ready(function() {
        // Function to generate a random color
        function getRandomColor() {
            var letters = '0123456789ABCDEF';
            var color = '#';
            for (var i = 0; i < 6; i++) {
                color += letters[Math.floor(Math.random() * 16)];
            }
            return color;
        }

        // Function to calculate the luminance of a color
        function getLuminance(hex) {
            // Remove the hash if present
            hex = hex.replace(/#/g, '');

            // Convert the hex to RGB
            var r = parseInt(hex.substr(0, 2), 16) / 255;
            var g = parseInt(hex.substr(2, 2), 16) / 255;
            var b = parseInt(hex.substr(4, 2), 16) / 255;

            // Calculate the luminance
            var rgb = [r, g, b].map(function(c) {
                if (c <= 0.03928) {
                    return c / 12.92;
                }
                return Math.pow((c + 0.055) / 1.055, 2.4);
            });

            return 0.2126 * rgb[0] + 0.7152 * rgb[1] + 0.0722 * rgb[2];
        }

        // Function to determine the text color based on the background color
        function getTextColor(bgColor) {
            return getLuminance(bgColor) > 0.5 ? '#000000' : '#FFFFFF';
        }

        // Iterate over each row in the table body and assign a unique color
        $('#example1 tbody tr').each(function() {
            var bgColor = getRandomColor();
            var textColor = getTextColor(bgColor);
            $(this).css('background-color', bgColor);
            $(this).css('color', textColor);
        });

        // Repeat for the second table
        $('#example2 tbody tr').each(function() {
            var bgColor = getRandomColor();
            var textColor = getTextColor(bgColor);
            $(this).css('background-color', bgColor);
            $(this).css('color', textColor);
        });
    });
</script>
</body>
</html>

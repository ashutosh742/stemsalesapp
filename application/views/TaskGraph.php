<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="wi$dth=device-wi$dth, initial-scale=1">
  <title>STEM APP | WebAPP</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.0/css/all.min.css">
  <link rel="stylesheet" href="<?=base_url();?>assets/css/all.min.css">
  <link rel="stylesheet" href="https://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css">
  <link rel="stylesheet" href="<?=base_url();?>assets/css/tempusdominus-bootstrap-4.min.css">
  <link rel="stylesheet" href="<?=base_url();?>assets/css/icheck-bootstrap.min.css">
  <link rel="stylesheet" href="<?=base_url();?>assets/css/jqvmap.min.css">
  <link rel="stylesheet" href="<?=base_url();?>assets/css/adminlte.min.css">
  <link rel="stylesheet" href="<?=base_url();?>assets/css/OverlayScrollbars.min.css">
  <link rel="stylesheet" href="<?=base_url();?>assets/css/daterangepicker.css">
  <link rel="stylesheet" href="<?=base_url();?>assets/css/summernote-bs4.min.css">
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

  <?php require('nav.php');?>

  

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
    <!-- /.content-header -->
    

    <!-- Main content -->
    <section class="content">
      <div class="container-fluid">
          <div class="card col-12  p-3 text-center">
              <center><h5>My Funnel</h5></center><hr>
              
              
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sales Graph</title>
    <!-- Include Chart.js library -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.7.0/chart.min.js"></script>
</head>
<body>
    <div style="width: 80%; margin: 0 auto;">
        <!-- Combined Sales Graph -->
        <canvas id="combinedSalesChart"></canvas>
    </div>

    <script>
        // PHP code to fetch data from your database
        <?php
        $hostname = "216.10.249.116";
        $username = "stemapp_stemapp";
        $password = "stemapp_stemapp";
        $database = "stemapp_stemapp";

        $conn = new mysqli($hostname, $username, $password, $database);

        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }

        // Calculate the date range for April 2023 to March 2024
        $startDate = "2023-04-01";
        $endDate = "2024-03-31";

        // Query for Appointments within the specified date range
        $sqlAppointments = "SELECT MONTH(appointmentdatetime) AS month, COUNT(*) AS appointments
                            FROM tblcallevents
                            WHERE appointmentdatetime BETWEEN '$startDate' AND '$endDate' AND tblcallevents.user_id='100087' AND nextCFID!='0'
                            GROUP BY MONTH(appointmentdatetime)
                            ORDER BY MONTH(appointmentdatetime)";

        // Query for Initiations within the specified date range
        $sqlInitiations = "SELECT MONTH(initiateddt) AS month, COUNT(*) AS initiations
                            FROM tblcallevents
                            WHERE initiateddt BETWEEN '$startDate' AND '$endDate' AND tblcallevents.user_id='100087' AND nextCFID!='0'
                            GROUP BY MONTH(initiateddt)
                            ORDER BY MONTH(initiateddt)";

        // Query for Updates within the specified date range
        $sqlUpdates = "SELECT MONTH(updateddate) AS month, COUNT(*) AS updates
                        FROM tblcallevents
                        WHERE updateddate BETWEEN '$startDate' AND '$endDate' AND tblcallevents.user_id='100087' AND nextCFID!='0'
                        GROUP BY MONTH(updateddate)
                        ORDER BY MONTH(updateddate)";
                        
        // Query for Action Types within the specified date range
        $sqlActionTypes = "SELECT MONTH(appointmentdatetime) AS month, actiontype_id, COUNT(*) AS action_type_count
                            FROM tblcallevents
                            WHERE appointmentdatetime BETWEEN '$startDate' AND '$endDate' AND tblcallevents.user_id='100087' AND nextCFID!='0'
                            GROUP BY MONTH(appointmentdatetime), actiontype_id
                            ORDER BY MONTH(appointmentdatetime)";

        $resultAppointments = $conn->query($sqlAppointments);
        $resultInitiations = $conn->query($sqlInitiations);
        $resultUpdates = $conn->query($sqlUpdates);
        $resultActionTypes = $conn->query($sqlActionTypes);

        $months = [];
        $appointmentData = [];
        $initiationData = [];
        $updateData = [];
        $actionTypeData = []; // New data for action types
        $actionTypes = []; // Array to store unique action types

        // Initialize empty data arrays for all months in the date range
        function initializeEmptyDataArray($months) {
            $data = [];
            foreach ($months as $month) {
                $data[] = 0;
            }
            return $data;
        }

        // Initialize months array
        $months = [
            'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December',
            'January', 'February', 'March',
        ];

        // Initialize empty data arrays
        $appointmentData = initializeEmptyDataArray($months);
        $initiationData = initializeEmptyDataArray($months);
        $updateData = initializeEmptyDataArray($months);

        // Process action types data
        if ($resultActionTypes->num_rows > 0) {
            while ($row = $resultActionTypes->fetch_assoc()) {
                $actionTypeMonth = date("F", mktime(0, 0, 0, $row['month'], 1));
                $actionType = $row['actiontype_id'];
                $count = $row['action_type_count'];

                // Initialize the action type data array
                if (!isset($actionTypeData[$actionType])) {
                    $actionTypeData[$actionType] = initializeEmptyDataArray($months);
                }

                // Store the count in the corresponding month's data
                $index = array_search($actionTypeMonth, $months);
                if ($index !== false) {
                    $actionTypeData[$actionType][$index] = $count;
                }

                // Store the action type for legend
                if (!in_array($actionType, $actionTypes)) {
                    $actionTypes[] = $actionType;
                }
            }
        }

        $conn->close();
        ?>

        // JavaScript code to create the combined sales graph
        const ctx = document.getElementById("combinedSalesChart").getContext("2d");
        new Chart(ctx, {
            type: "bar",
            data: {
                labels: <?php echo json_encode($months); ?>,
                datasets: [
                    {
                        label: "Appointments",
                        backgroundColor: "rgba(75, 192, 192, 0.2)",
                        borderColor: "rgba(75, 192, 192, 1)",
                        borderWidth: 1,
                        data: <?php echo json_encode($appointmentData); ?>,
                    },
                    {
                        label: "Initiations",
                        backgroundColor: "rgba(255, 99, 132, 0.2)",
                        borderColor: "rgba(255, 99, 132, 1)",
                        borderWidth: 1,
                        data: <?php echo json_encode($initiationData); ?>,
                    },
                    {
                        label: "Updates",
                        backgroundColor: "rgba(54, 162, 235, 0.2)",
                        borderColor: "rgba(54, 162, 235, 1)",
                        borderWidth: 1,
                        data: <?php echo json_encode($updateData); ?>,
                    }
                ],
            },
            options: {
                scales: {
                    x: {
                        stacked: true,
                    },
                    y: {
                        stacked: true,
                        beginAtZero: true,
                    },
                },
            },
        });

        // JavaScript code to create the action types bar chart
        const actionTypesCanvas = document.createElement('canvas');
        actionTypesCanvas.id = 'actionTypesChart';
        document.body.appendChild(actionTypesCanvas);

        const actionTypesCtx = actionTypesCanvas.getContext('2d');
        new Chart(actionTypesCtx, {
            type: 'bar',
            data: {
                labels: <?php echo json_encode($months); ?>,
                datasets: [
                    <?php
                    foreach ($actionTypes as $actionType) {
                        echo "{\n";
                        echo "    label: 'Action Type $actionType',\n";
                        echo "    backgroundColor: 'rgba(" . rand(0, 255) . ", " . rand(0, 255) . ", " . rand(0, 255) . ", 0.2)',\n";
                        echo "    borderColor: 'rgba(" . rand(0, 255) . ", " . rand(0, 255) . ", " . rand(0, 255) . ", 1)',\n";
                        echo "    borderWidth: 1,\n";
                        echo "    data: " . json_encode($actionTypeData[$actionType]) . ",\n";
                        echo "},\n";
                    }
                    ?>
                ],
            },
            options: {
                scales: {
                    x: {
                        stacked: true,
                    },
                    y: {
                        stacked: true,
                        beginAtZero: true,
                    },
                },
            },
        });
    </script>
</body>
</html>

          </div>
      </div>
    </section>
   </div>
</div> 
    
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
<script src="<?=base_url();?>assets/js/jquery-ui.min.js"></script>
<script>
  $.widget.bridge('uibutton', $.ui.button)
</script>
<script src="<?=base_url();?>assets/js/bootstrap.bundle.min.js"></script>
<script src="<?=base_url();?>assets/js/Chart.min.js"></script>
<script src="<?=base_url();?>assets/js/sparkline.js"></script>
<script src="<?=base_url();?>assets/js/jquery.vmap.min.js"></script>
<script src="<?=base_url();?>assets/js/jquery.vmap.usa.js"></script>
<script src="plugins/jquery-knob/jquery.knob.min.js"></script>
<script src="<?=base_url();?>assets/js/moment.min.js"></script>
<script src="<?=base_url();?>assets/js/daterangepicker.js"></script>
<script src="<?=base_url();?>assets/js/tempusdominus-bootstrap-4.min.js"></script>
<script src="<?=base_url();?>assets/js/summernote-bs4.min.js"></script>
<script src="<?=base_url();?>assets/js/jquery.overlayScrollbars.min.js"></script>
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

<script>
    $("#example1").DataTable({
      "responsive": false, "lengthChange": false, "autoWi$dth": false,
      "buttons": ["copy", "csv", "excel", "pdf", "print", "colvis"]
    }).buttons().container().appen$dto('#example1_wrapper .col-md-6:eq(0)');
</script>
</body>
</html>

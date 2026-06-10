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
              

            
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.1/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.0.1/dist/chart.umd.min.js"></script>
<?php
$servername = "216.10.249.116";
$username = "stemapp_stemapp";
$password = "stemapp_stemapp";
$dbname = "stemapp_stemapp";
$conn = new mysqli($servername, $username, $password, $dbname);

// Check database connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Monthly Sales Volume
$query1 = "SELECT MONTH(ce.initiateddt) as month, COUNT(ce.id) as count FROM tblcallevents ce GROUP BY MONTH(ce.initiateddt)";
$result1 = $conn->query($query1);

// Check query execution
if (!$result1) {
    die("Query 1 failed: " . $conn->error);
}

$monthly_sales = [];
while ($row = $result1->fetch_assoc()) {
    $monthly_sales[$row['month']] = $row['count'];
}




?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sales Dashboard</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>

<body>

<h2>Monthly Sales Volume</h2>
<canvas id="monthlySalesChart" width="400" height="200"></canvas>

<h2>Sales by Product</h2>
<canvas id="productSalesChart" width="400" height="200"></canvas>

<script>
    var ctx1 = document.getElementById('monthlySalesChart').getContext('2d');
    var monthlySalesChart = new Chart(ctx1, {
        type: 'bar',
        data: {
            labels: <?php echo json_encode(array_keys($monthly_sales)); ?>,
            datasets: [{
                label: 'Sales',
                data: <?php echo json_encode(array_values($monthly_sales)); ?>,
                backgroundColor: 'rgba(75, 192, 192, 0.2)',
                borderColor: 'rgba(75, 192, 192, 1)',
                borderWidth: 1
            }]
        }
    });

    var ctx2 = document.getElementById('productSalesChart').getContext('2d');
    var productSalesChart = new Chart(ctx2, {
        type: 'pie',
        data: {
            labels: <?php echo json_encode(array_keys($sales_by_product)); ?>,
            datasets: [{
                data: <?php echo json_encode(array_values($sales_by_product)); ?>,
                backgroundColor: ['rgba(255, 99, 132, 0.2)', 'rgba(255, 159, 64, 0.2)', 'rgba(255, 206, 86, 0.2)', 'rgba(75, 192, 192, 0.2)', 'rgba(54, 162, 235, 0.2)', 'rgba(153, 102, 255, 0.2)', 'rgba(201, 203, 207, 0.2)'],
                borderColor: ['rgba(255, 99, 132, 1)', 'rgba(255, 159, 64, 1)', 'rgba(255, 206, 86, 1)', 'rgba(75, 192, 192, 1)', 'rgba(54, 162, 235, 1)', 'rgba(153, 102, 255, 1)', 'rgba(201, 203, 207, 1)'],
                borderWidth: 1
            }]
        }
    });
</script>

</body



<?php
$servername = "216.10.249.116";
$username = "stemapp_stemapp";
$password = "stemapp_stemapp";
$dbname = "stemapp_stemapp";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$query = "
    SELECT
        MONTH(ce.initiateddt) as month,
        ce.actiontype_id as action_taken,
        ce.nstatus_id as sales_stage,
        ce.status_id as status,
        COUNT(ce.id) as count
    FROM tblcallevents ce
    GROUP BY MONTH(ce.initiateddt), ce.actiontype_id, ce.nstatus_id, ce.status_id
";

$result = $conn->query($query);

if (!$result) {
    die("Query failed: " . $conn->error);
}

$data = [];
while ($row = $result->fetch_assoc()) {
    $data[$row['month']][$row['action_taken']][$row['sales_stage']][$row['status']] = $row['count'];
}

$months = array_keys($data);

$datasets = [];

foreach ($data as $month => $monthData) {
    foreach ($monthData as $action => $actionData) {
        foreach ($actionData as $stage => $stageData) {
            foreach ($stageData as $status => $count) {
                $label = ucfirst($action) . ' - ' . ucfirst($stage) . ' - ' . ucfirst($status);
                if (!isset($datasets[$label])) {
                    $datasets[$label] = [
                        'label' => $label,
                        'data' => [],
                        'backgroundColor' => 'rgba(' . rand(0, 255) . ',' . rand(0, 255) . ',' . rand(0, 255) . ',0.2)',
                        'borderColor' => 'rgba(' . rand(0, 255) . ',' . rand(0, 255) . ',' . rand(0, 255) . ',1)',
                        'borderWidth' => 1
                    ];
                }
                $datasets[$label]['data'][] = $count;
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Stacked Bar Chart</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
<canvas id="myChart" width="400" height="200"></canvas>
<script>
    var ctx = document.getElementById('myChart').getContext('2d');
    var myChart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: <?php echo json_encode($months); ?>,
            datasets: <?php echo json_encode(array_values($datasets)); ?>
        },
        options: {
            scales: {
                x: {
                    stacked: true,
                },
                y: {
                    stacked: true,
                    beginAtZero: true
                }
            }
        }
    });
</script>
</body>
</html>




    <div style="width: 80%; margin: auto;">
        <canvas id="stackedChartID1"></canvas>
    </div>

    <script>
        var myContext = document.getElementById("stackedChartID1").getContext('2d');
        var myChart1 = new Chart(myContext, {
            type: 'bar',
            data: {
                labels: [
                    
                    <?php   $currentDate = new DateTime();
                            $financialYear = ($currentDate->format('m') >= 4) ? $currentDate->format('Y') : ($currentDate->format('Y') - 1);
                            for ($month = 4; $month <= 15; $month++) {
                                $adjustedMonth = ($month <= 12) ? $month : ($month - 12);
                                $year = ($month <= 12) ? $financialYear : ($financialYear + 1);
                                $monthName = DateTime::createFromFormat('!m', $adjustedMonth)->format('F');?>
                    "<?=$monthName?> (<?=$year?>)", <?php } ?>],
                datasets: [
                    
                    <?php 
                    $colors = array('red','blue','green','yellow','purple','orange','pink','brown','cyan','magenta','teal','lime','violet','indigo','gray'); ?>
                    
                    {
                        label: '',
                        backgroundColor: "red",
                        data: [
                            
                            <?php $currentDate = new DateTime();
                        $financialYear = ($currentDate->format('m') >= 4) ? $currentDate->format('Y') : ($currentDate->format('Y') - 1);
                        for ($month = 4; $month <= 15; $month++) {
                            $adjustedMonth = ($month <= 12) ? $month : ($month - 12);
                            $year = ($month <= 12) ? $financialYear : ($financialYear + 1);
                            $accont = $this->Menu_model->get_mypnutask($uid,$month,$year);?>
                            <?=$accont[0]->plandt?>,<?php } ?>],
                        stack: 'Stack 0',
                    },
                    
                    {
                        label: '',
                        backgroundColor: "yellow",
                        data: [
                            
                            <?php $currentDate = new DateTime();
                        $financialYear = ($currentDate->format('m') >= 4) ? $currentDate->format('Y') : ($currentDate->format('Y') - 1);
                        for ($month = 4; $month <= 15; $month++) {
                            $adjustedMonth = ($month <= 12) ? $month : ($month - 12);
                            $year = ($month <= 12) ? $financialYear : ($financialYear + 1);
                            $accont = $this->Menu_model->get_mypnutask($uid,$month,$year);?>
                            <?=$accont[0]->insdt?>,<?php } ?>],
                        stack: 'Stack 0',
                    },
                    
                    {
                        label: '',
                        backgroundColor: "green",
                        data: [
                            
                            <?php $currentDate = new DateTime();
                        $financialYear = ($currentDate->format('m') >= 4) ? $currentDate->format('Y') : ($currentDate->format('Y') - 1);
                        for ($month = 4; $month <= 15; $month++) {
                            $adjustedMonth = ($month <= 12) ? $month : ($month - 12);
                            $year = ($month <= 12) ? $financialYear : ($financialYear + 1);
                            $accont = $this->Menu_model->get_mypnutask($uid,$month,$year);?>
                            <?=$accont[0]->updt?>,<?php } ?>],
                        stack: 'Stack 0',
                    },
                    
                    
                    <?php $status = $this->Menu_model->get_status(); $i=0; foreach($status as $st){
                        $stid = $st->id; ?>
                    
                    {
                        label: '<?=$st->name?>',
                        backgroundColor: "<?=$colors[$i]?>",
                        data: [
                            <?php $currentDate = new DateTime();
                        $financialYear = ($currentDate->format('m') >= 4) ? $currentDate->format('Y') : ($currentDate->format('Y') - 1);
                        for ($month = 4; $month <= 15; $month++) {
                            $adjustedMonth = ($month <= 12) ? $month : ($month - 12);
                            $year = ($month <= 12) ? $financialYear : ($financialYear + 1);
                            $accont = $this->Menu_model->get_mypnutaskactionplan($uid,$month,$year,$stid);?>
                            <?=$accont[0]->cont?>,<?php } ?>
                            
                            ],
                        stack: 'Stack 1'
                    },
                    
                    <?php $i++;} ?>
                    
                    
                    <?php $status = $this->Menu_model->get_status(); $i=0; foreach($status as $st){
                        $stid = $st->id; ?>
                    
                    {
                        label: '<?=$st->name?>',
                        backgroundColor: "<?=$colors[$i]?>",
                        data: [
                            <?php $currentDate = new DateTime();
                        $financialYear = ($currentDate->format('m') >= 4) ? $currentDate->format('Y') : ($currentDate->format('Y') - 1);
                        for ($month = 4; $month <= 15; $month++) {
                            $adjustedMonth = ($month <= 12) ? $month : ($month - 12);
                            $year = ($month <= 12) ? $financialYear : ($financialYear + 1);
                            $accont = $this->Menu_model->get_mypnutaskactionin($uid,$month,$year,$stid);?>
                            <?=$accont[0]->cont?>,<?php } ?>
                            
                            ],
                        stack: 'Stack 2'
                    },
                    
                    <?php $i++;} ?>
                    
                    
                    <?php $status = $this->Menu_model->get_status(); $i=0; foreach($status as $st){
                        $stid = $st->id; ?>
                    
                    {
                        label: '<?=$st->name?>',
                        backgroundColor: "<?=$colors[$i]?>",
                        data: [
                            <?php $currentDate = new DateTime();
                        $financialYear = ($currentDate->format('m') >= 4) ? $currentDate->format('Y') : ($currentDate->format('Y') - 1);
                        for ($month = 4; $month <= 15; $month++) {
                            $adjustedMonth = ($month <= 12) ? $month : ($month - 12);
                            $year = ($month <= 12) ? $financialYear : ($financialYear + 1);
                            $accont = $this->Menu_model->get_mypnutaskactionup($uid,$month,$year,$stid);?>
                            <?=$accont[0]->cont?>,<?php } ?>
                            
                            ],
                        stack: 'Stack 3'
                    },
                    
                    <?php $i++;} ?>
                    
                    
                    
                    <?php $action = $this->Menu_model->get_action(); $i=0; foreach($action as $ac){
                        $acid = $ac->id; ?>
                    
                    {
                        label: '<?=$ac->name?> ANPN',
                        backgroundColor: "<?=$colors[$i]?>",
                        data: [
                            <?php $currentDate = new DateTime();
                        $financialYear = ($currentDate->format('m') >= 4) ? $currentDate->format('Y') : ($currentDate->format('Y') - 1);
                        for ($month = 4; $month <= 15; $month++) {
                            $adjustedMonth = ($month <= 12) ? $month : ($month - 12);
                            $year = ($month <= 12) ? $financialYear : ($financialYear + 1);
                            $accont = $this->Menu_model->get_mypnutaskactionanpn($uid,$month,$year,$acid);?>
                            <?=$accont[0]->cont?>,<?php } ?>
                            
                            ],
                        stack: 'Stack 4'
                    },
                    
                    <?php $i++;} ?>
                    
                    
                    <?php $action = $this->Menu_model->get_action(); $i=0; foreach($action as $ac){
                        $acid = $ac->id; ?>
                    
                    {
                        label: '<?=$ac->name?> ANPY',
                        backgroundColor: "<?=$colors[$i]?>",
                        data: [
                            <?php $currentDate = new DateTime();
                        $financialYear = ($currentDate->format('m') >= 4) ? $currentDate->format('Y') : ($currentDate->format('Y') - 1);
                        for ($month = 4; $month <= 15; $month++) {
                            $adjustedMonth = ($month <= 12) ? $month : ($month - 12);
                            $year = ($month <= 12) ? $financialYear : ($financialYear + 1);
                            $accont = $this->Menu_model->get_mypnutaskactionaypn($uid,$month,$year,$acid);?>
                            <?=$accont[0]->cont?>,<?php } ?>
                            
                            ],
                        stack: 'Stack 4'
                    },
                    
                    <?php $i++;} ?>
                    
                    
                    <?php $action = $this->Menu_model->get_action(); $i=0; foreach($action as $ac){
                        $acid = $ac->id; ?>
                    
                    {
                        label: '<?=$ac->name?> AYPY',
                        backgroundColor: "<?=$colors[$i]?>",
                        data: [
                            <?php $currentDate = new DateTime();
                        $financialYear = ($currentDate->format('m') >= 4) ? $currentDate->format('Y') : ($currentDate->format('Y') - 1);
                        for ($month = 4; $month <= 15; $month++) {
                            $adjustedMonth = ($month <= 12) ? $month : ($month - 12);
                            $year = ($month <= 12) ? $financialYear : ($financialYear + 1);
                            $accont = $this->Menu_model->get_mypnutaskactionaypy($uid,$month,$year,$acid);?>
                            <?=$accont[0]->cont?>,<?php } ?>
                            
                            ],
                        stack: 'Stack 4'
                    },
                    
                    <?php $i++;} ?>
                    
                    
                    
                    
                    
                   
                ],
            },
            options: {
                
                plugins: {
                    legend: {
                display: false, // Hide the legend
            },
                    title: {
                        display: true,
                        text: 'Month Wise Type of Task Count And Partner Type Count by it Self (ANPN)'
                    },
                    
                
            },
            onClick: function (evt, activeElements) {
                if (activeElements && activeElements.length) {
                    const clickedDatasetIndex = activeElements[0].datasetIndex;

                    let allOthersHidden = true;
                    for (let i = 0; i < myChart1.data.datasets.length; i++) {
                        if (i !== clickedDatasetIndex && !myChart1.getDatasetMeta(i).hidden) {
                            allOthersHidden = false;
                            break;
                        }
                    }

                    if (allOthersHidden) {
                        for (let i = 0; i < myChart1.data.datasets.length; i++) {
                            myChart1.getDatasetMeta(i).hidden = false;
                        }
                    } else {
                        for (let i = 0; i < myChart1.data.datasets.length; i++) {
                            myChart1.getDatasetMeta(i).hidden = (i !== clickedDatasetIndex);
                        }
                    }

                    myChart1.update();
                }
            }
        }
    });
    </script>







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

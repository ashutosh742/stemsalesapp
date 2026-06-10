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
              
              
              <style>
#chartdiv {
  width: 100%;
  height: 500px;
}
</style>

<!-- Resources -->
<script src="https://cdn.amcharts.com/lib/5/index.js"></script>
<script src="https://cdn.amcharts.com/lib/5/percent.js"></script>
<script src="https://cdn.amcharts.com/lib/5/themes/Animated.js"></script>


<form class="p-3" method="POST" action="TaskGraph5">
    <input type="date" name="sdate" class="mr-2" value="<?=$sd?>" min="2023-04-01">
    <input type="date" name="edate" class="mr-2" value="<?=$ed?>">
    <button type="submit" class="bg-primary text-light">Filter</button>
</form>



<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.7.0/chart.min.js"></script>
<?php  $colors = array('red','blue','green','yellow','purple','orange','pink','brown','cyan','magenta','teal','lime','violet','indigo','gray'); ?>
    
    <div style="width: 80%; margin: auto;">
        <canvas id="stackedChart"></canvas>
    </div>

    <script>
        var stackedData = {
            labels: [
                
                <?php $currentDate = new DateTime();
                        $financialYear = ($currentDate->format('m') >= 4) ? $currentDate->format('Y') : ($currentDate->format('Y') - 1);
                        for ($month = 4; $month <= 15; $month++) {
                            $adjustedMonth = ($month <= 12) ? $month : ($month - 12);
                            $year = ($month <= 12) ? $financialYear : ($financialYear + 1);
                             $monthName = DateTime::createFromFormat('!m', $adjustedMonth)->format('F');?>
                            
                            '<?=$monthName?> (<?=$year?>)',
                            
                            <?php } ?>
                ],
            datasets: [
                
                <?php $i=0; $action = $this->Menu_model->get_action(); $i=0; foreach($action as $ac){
                        $acid = $ac->id; ?>
                {
                    label: '<?=$ac->name?>',
                    backgroundColor: '<?=$colors[$i]?>',
                    data: [
                        <?php
                        $currentDate = new DateTime();
                        $financialYear = ($currentDate->format('m') >= 4) ? $currentDate->format('Y') : ($currentDate->format('Y') - 1);
                        for ($month = 4; $month <= 15; $month++) {
                          $adjustedMonth = ($month <= 12) ? $month : ($month - 12);
                          $year = ($month <= 12) ? $financialYear : ($financialYear + 1);
                          $task = $this->Menu_model->get_taskubyacaypy($uid,$month,$year,$acid); foreach($task as $ta){?>
                        <?=$ta->cont?>,          
                        <?php }} ?>          
                        
                        
                        
                        ],
                    stack: 'Tasks'
                },
                
                
                <?php $i++;} ?> 
                
                
                
                
                
                <?php  
                
                $action = $this->Menu_model->get_action(); $i=0; foreach($action as $ac){
                $acid = $ac->id;
                $i=0;
                $status = $this->Menu_model->get_status(); $i=0; foreach($status as $st){
                $stid = $st->id; 
                
                
                ?>
                {
                    label: '<?=$st->name?> (<?=$ac->name?>)',
                    backgroundColor: '<?=$colors[$i]?>',
                    data: [
                        <?php
                        $currentDate = new DateTime();
                        $financialYear = ($currentDate->format('m') >= 4) ? $currentDate->format('Y') : ($currentDate->format('Y') - 1);
                        for ($month = 4; $month <= 15; $month++) {
                          $adjustedMonth = ($month <= 12) ? $month : ($month - 12);
                          $year = ($month <= 12) ? $financialYear : ($financialYear + 1);
                          $task = $this->Menu_model->get_taskubystaypy($uid,$month,$year,$stid,$acid); foreach($task as $ta){?>
                        <?=$ta->cont?>,          
                        <?php }} ?>          
                        
                        ],
                    stack: 'Status'
                },
                
                <?php $i++;}} ?> 
                
            ]
        };

        var stackedCtx = document.getElementById("stackedChart").getContext('2d');
        var stackedChart = new Chart(stackedCtx, {
            type: 'bar',
            data: stackedData,
            options: {
                plugins: {
                    title: {
                        display: true,
                        text: 'Updated Task - Task Type and Status Wise (ANPN)',
                    },
                },
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
    
    
    
    
<div style="width: 80%; margin: auto;">
    <canvas id="stackedChart1"></canvas>
</div>

    <script>
        var stackedData = {
            labels: [
                
                <?php $currentDate = new DateTime();
                        $financialYear = ($currentDate->format('m') >= 4) ? $currentDate->format('Y') : ($currentDate->format('Y') - 1);
                        for ($month = 4; $month <= 15; $month++) {
                            $adjustedMonth = ($month <= 12) ? $month : ($month - 12);
                            $year = ($month <= 12) ? $financialYear : ($financialYear + 1);
                             $monthName = DateTime::createFromFormat('!m', $adjustedMonth)->format('F');?>
                            
                            '<?=$monthName?> (<?=$year?>)',
                            
                            <?php } ?>
                ],
            datasets: [
                
                <?php $i=0; $action = $this->Menu_model->get_action(); $i=0; foreach($action as $ac){
                        $acid = $ac->id; ?>
                {
                    label: '<?=$ac->name?>',
                    backgroundColor: '<?=$colors[$i]?>',
                    data: [
                        <?php
                        $currentDate = new DateTime();
                        $financialYear = ($currentDate->format('m') >= 4) ? $currentDate->format('Y') : ($currentDate->format('Y') - 1);
                        for ($month = 4; $month <= 15; $month++) {
                          $adjustedMonth = ($month <= 12) ? $month : ($month - 12);
                          $year = ($month <= 12) ? $financialYear : ($financialYear + 1);
                          $task = $this->Menu_model->get_taskubyacaypy($uid,$month,$year,$acid); foreach($task as $ta){?>
                        <?=$ta->cont?>,          
                        <?php }} ?>          
                        
                        
                        
                        ],
                    stack: 'Tasks'
                },
                
                
                <?php $i++;} ?> 
                
                
                
                
                
                <?php  
                
                $action = $this->Menu_model->get_action(); $i=0; foreach($action as $ac){
                $acid = $ac->id;
                $i=0;
                $status = $this->Menu_model->get_status(); $i=0; foreach($status as $st){
                $stid = $st->id; 
                
                
                ?>
                {
                    label: '<?=$st->name?> (<?=$ac->name?>)',
                    backgroundColor: '<?=$colors[$i]?>',
                    data: [
                        <?php
                        $currentDate = new DateTime();
                        $financialYear = ($currentDate->format('m') >= 4) ? $currentDate->format('Y') : ($currentDate->format('Y') - 1);
                        for ($month = 4; $month <= 15; $month++) {
                          $adjustedMonth = ($month <= 12) ? $month : ($month - 12);
                          $year = ($month <= 12) ? $financialYear : ($financialYear + 1);
                          $task = $this->Menu_model->get_taskubystaypn($uid,$month,$year,$stid,$acid); foreach($task as $ta){?>
                        <?=$ta->cont?>,          
                        <?php }} ?>          
                        
                        ],
                    stack: 'Status'
                },
                
                <?php $i++;}} ?> 
                
            ]
        };

        var stackedCtx = document.getElementById("stackedChart1").getContext('2d');
        var stackedChart = new Chart(stackedCtx, {
            type: 'bar',
            data: stackedData,
            options: {
                plugins: {
                    title: {
                        display: true,
                        text: 'Updated Task - Task Type and Status Wise (AYPN)',
                    },
                },
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
    
    
    
<div style="width: 80%; margin: auto;">
    <canvas id="stackedChart2"></canvas>
</div>

    <script>
        var stackedData = {
            labels: [
                
                <?php $currentDate = new DateTime();
                        $financialYear = ($currentDate->format('m') >= 4) ? $currentDate->format('Y') : ($currentDate->format('Y') - 1);
                        for ($month = 4; $month <= 15; $month++) {
                            $adjustedMonth = ($month <= 12) ? $month : ($month - 12);
                            $year = ($month <= 12) ? $financialYear : ($financialYear + 1);
                             $monthName = DateTime::createFromFormat('!m', $adjustedMonth)->format('F');?>
                            
                            '<?=$monthName?> (<?=$year?>)',
                            
                            <?php } ?>
                ],
            datasets: [
                
                <?php $i=0; $action = $this->Menu_model->get_action(); $i=0; foreach($action as $ac){
                        $acid = $ac->id; ?>
                {
                    label: '<?=$ac->name?>',
                    backgroundColor: '<?=$colors[$i]?>',
                    data: [
                        <?php
                        $currentDate = new DateTime();
                        $financialYear = ($currentDate->format('m') >= 4) ? $currentDate->format('Y') : ($currentDate->format('Y') - 1);
                        for ($month = 4; $month <= 15; $month++) {
                          $adjustedMonth = ($month <= 12) ? $month : ($month - 12);
                          $year = ($month <= 12) ? $financialYear : ($financialYear + 1);
                          $task = $this->Menu_model->get_taskubyacanpn($uid,$month,$year,$acid); foreach($task as $ta){?>
                        <?=$ta->cont?>,          
                        <?php }} ?>          
                        
                        
                        
                        ],
                    stack: 'Tasks'
                },
                
                
                <?php $i++;} ?> 
                
                
                
                
                
                <?php  
                
                $action = $this->Menu_model->get_action(); $i=0; foreach($action as $ac){
                $acid = $ac->id;
                $i=0;
                $status = $this->Menu_model->get_status(); $i=0; foreach($status as $st){
                $stid = $st->id; 
                
                
                ?>
                {
                    label: '<?=$st->name?> (<?=$ac->name?>)',
                    backgroundColor: '<?=$colors[$i]?>',
                    data: [
                        <?php
                        $currentDate = new DateTime();
                        $financialYear = ($currentDate->format('m') >= 4) ? $currentDate->format('Y') : ($currentDate->format('Y') - 1);
                        for ($month = 4; $month <= 15; $month++) {
                          $adjustedMonth = ($month <= 12) ? $month : ($month - 12);
                          $year = ($month <= 12) ? $financialYear : ($financialYear + 1);
                          $task = $this->Menu_model->get_taskubystaypn($uid,$month,$year,$stid,$acid); foreach($task as $ta){?>
                        <?=$ta->cont?>,          
                        <?php }} ?>          
                        
                        ],
                    stack: 'Status'
                },
                
                <?php $i++;}} ?> 
                
            ]
        };

        var stackedCtx = document.getElementById("stackedChart2").getContext('2d');
        var stackedChart = new Chart(stackedCtx, {
            type: 'bar',
            data: stackedData,
            options: {
                plugins: {
                    title: {
                        display: true,
                        text: 'Updated Task - Task Type and Status Wise (ANPN)',
                    },
                },
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
    
    
<div style="width: 80%; margin: auto;">
        <canvas id="stackedChart3"></canvas>
    </div>

    <script>
        var stackedData = {
            labels: [
                <?php $currentDate = new DateTime();
                        $financialYear = ($currentDate->format('m') >= 4) ? $currentDate->format('Y') : ($currentDate->format('Y') - 1);
                        for ($month = 4; $month <= 15; $month++) {
                            $adjustedMonth = ($month <= 12) ? $month : ($month - 12);
                            $year = ($month <= 12) ? $financialYear : ($financialYear + 1);
                             $monthName = DateTime::createFromFormat('!m', $adjustedMonth)->format('F');?>
                            
                            '<?=$monthName?> (<?=$year?>)',
                            
                            <?php } ?>
                ],
            datasets: [
                
                <?php $i=0; $action = $this->Menu_model->get_action(); $i=0; foreach($action as $ac){
                        $acid = $ac->id; ?>
                {
                    label: '<?=$ac->name?>',
                    backgroundColor: '<?=$colors[$i]?>',
                    data: [
                        <?php
                        $currentDate = new DateTime();
                        $financialYear = ($currentDate->format('m') >= 4) ? $currentDate->format('Y') : ($currentDate->format('Y') - 1);
                        for ($month = 4; $month <= 15; $month++) {
                          $adjustedMonth = ($month <= 12) ? $month : ($month - 12);
                          $year = ($month <= 12) ? $financialYear : ($financialYear + 1);
                          $task = $this->Menu_model->get_taskPendingbya($uid,$month,$year,$acid); foreach($task as $ta){?>
                        <?=$ta->cont?>,          
                        <?php }} ?>    
                        ],
                    stack: 'Tasks'
                },
                <?php $i++;} ?> 
                
                <?php
                    $action = $this->Menu_model->get_action(); $i=0; foreach($action as $ac){
                    $acid = $ac->id;
                    $i=0;
                    $status = $this->Menu_model->get_status(); $i=0; foreach($status as $st){
                    $stid = $st->id; 
                ?>
                {
                    label: '<?=$st->name?> (<?=$ac->name?>)',
                    backgroundColor: '<?=$colors[$i]?>',
                    data: [
                        <?php
                        $currentDate = new DateTime();
                        $financialYear = ($currentDate->format('m') >= 4) ? $currentDate->format('Y') : ($currentDate->format('Y') - 1);
                        for ($month = 4; $month <= 15; $month++) {
                          $adjustedMonth = ($month <= 12) ? $month : ($month - 12);
                          $year = ($month <= 12) ? $financialYear : ($financialYear + 1);
                          $task = $this->Menu_model->get_taskPendingbys($uid,$month,$year,$stid,$acid); foreach($task as $ta){?>
                        <?=$ta->cont?>,          
                        <?php }} ?>          
                        
                        ],
                    stack: 'Status'
                },
                
                <?php $i++;}} ?> 
                
            ]
        };

        var stackedCtx = document.getElementById("stackedChart3").getContext('2d');
        var stackedChart3 = new Chart(stackedCtx, {
            type: 'bar',
            data: stackedData,
            options: {
                plugins: {
                    title: {
                        display: true,
                        text: 'Pending Task - Task Type and Status Wise',
                    },
                },
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
    




<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Performance Dot Chart</title>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.7.0/chart.min.js"></script>
</head>

<body>
    <div style="width: 80%; margin: auto;">
        <canvas id="performanceChart"></canvas>
    </div>

    <script>
        const dataValues = [85, 50, 30, 65, 75, 40, 20];
        const colors = dataValues.map(value => {
            if (value >= 75) return 'green';
            if (value >= 50) return 'yellow';
            return 'red';
        });

        var ctx = document.getElementById("performanceChart").getContext('2d');
        var performanceChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'],
                datasets: [{
                    label: 'Performance',
                    data: dataValues,
                    backgroundColor: colors,
                    borderColor: colors,
                    borderWidth: 1,
                    pointRadius: 6,
                    fill: false
                }]
            },
            options: {
                plugins: {
                    title: {
                        display: true,
                        text: 'Performance Dot Chart',
                    },
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        max: 100
                    }
                },
                elements: {
                    line: {
                        tension: 0
                    }
                }
            }
        });
    </script>
</body>

</html>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gantt Chart</title>
    <style>
        .gantt-container {
            width: 100%;
            height: 40px;
            position: relative;
            border-top: 2px solid black;
            border-bottom: 2px solid black;
            margin: 20px 0;
        }
        .activity {
            position: absolute;
            height: 36px; /* Fits within the borders */
            background-color: #3498db;
            top: 1px; /* Centers between borders */
            text-align: center;
            color: white;
            border-radius: 5px;
        }
        .time-labels {
            display: flex;
            justify-content: space-between;
            width: 100%;
        }
    </style>
</head>
<body>

<h2>Gantt Chart</h2>

<div class="gantt-container">
    <div class="activity" style="left: 10%; width: 20%;">Breakfast</div>
    <div class="activity" style="left: 40%; width: 10%;">Meeting</div>
    <div class="activity" style="left: 65%; width: 25%;">Project Work</div>
</div>

<div class="time-labels">
    <span>9:00 AM</span>
    <span>12:00 PM</span>
    <span>3:00 PM</span>
    <span>6:00 PM</span>
</div>

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
You have made no changes to save.
</script>
</body>
</html>

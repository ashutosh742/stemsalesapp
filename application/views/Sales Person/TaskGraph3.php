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


<form class="p-3" method="POST" action="TaskGraph1">
    <input type="date" name="sdate" class="mr-2" value="<?=$sd?>" min="2023-04-01">
    <input type="date" name="edate" class="mr-2" value="<?=$ed?>">
    <button type="submit" class="bg-primary text-light">Filter</button>
</form>



<!DOCTYPE html>
<html>
<head>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.1/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.0.1/dist/chart.umd.min.js"></script>
</head>
<?php $colors = array('red','blue','green','yellow','purple','orange','pink','brown','cyan','magenta','teal','lime','violet','indigo','gray');?>










<div id="columnchart10" class="col-12"></div>
<script>
google.charts.load("current", {packages:['corechart']});
    google.charts.setOnLoadCallback(drawChart);
    function drawChart() {
      var data = google.visualization.arrayToDataTable([
        ["Conversion", "Days", { role: "style" } ],
        <?php 
        
            $stage1 = $this->Menu_model->get_daysofstatuscon($uid,$sd,$ed,'1','8');
            $stage2 = $this->Menu_model->get_daysofstatuscon($uid,$sd,$ed,'8','2');
            $stage3 = $this->Menu_model->get_daysofstatuscon($uid,$sd,$ed,'1','2');
            $stage4 = $this->Menu_model->get_daysofstatuscon($uid,$sd,$ed,'2','3');
            $stage5 = $this->Menu_model->get_daysofstatuscon($uid,$sd,$ed,'3','6');
            $stage6 = $this->Menu_model->get_daysofstatuscon($uid,$sd,$ed,'6','9');
            $stage7 = $this->Menu_model->get_daysofstatuscon($uid,$sd,$ed,'9','7');
            $tdays = $stage1[0]->days+$stage2[0]->days+$stage3[0]->days+$stage4[0]->days+$stage5[0]->days+$stage6[0]->days+$stage7[0]->days;
        ?>
        
        ["Open-RPEM", <?=$stage1[0]->days?>, "#76A7FA"],
        ["RPEM-Reachout", <?=$stage2[0]->days?>, "#76A7FA"],
        ["Open-Reachout", <?=$stage3[0]->days?>, "76A7FA"],
        ["Reachout-Tentative", <?=$stage4[0]->days?>, "76A7FA"],
        ["Tentative-Positive", <?=$stage5[0]->days?>, "color: #76A7FA"],
        ["Positive-VPositive", <?=$stage6[0]->days?>, "color: #76A7FA"],
        ["VPositive-Clouser", <?=$stage7[0]->days?>, "color: #76A7FA"],
      ]);
      

      var view = new google.visualization.DataView(data);
      view.setColumns([0, 1,
                       { calc: "stringify",
                         sourceColumn: 1,
                         type: "string",
                         role: "annotation" },
                       2]);

      var options = {
        title: "Average Days Sales Cycle | No of <?=$tdays?> Days",
        height: 500,
        bar: {groupWidth: "95%"},
        legend: { position: "none" },
      };
      var chart = new google.visualization.ColumnChart(document.getElementById("columnchart10"));
      chart.draw(view, options);
  }
</script>















<div style="width: 80%; margin: auto;">
    <canvas id="combinedChartID11"></canvas>
</div>

<script>
    var combinedData11 = {
        labels: [
            <?php
            $timeslots = $this->Menu_model->get_timeslot();
            foreach ($timeslots as $tm) {
                echo "'$tm->time1-$tm->time2', ";
            }
            ?>
        ],
        datasets: [
            <?php
            $actions = $this->Menu_model->get_action();
            foreach ($actions as $ac) {
                $acid = $ac->id;
                echo "{\n";
                echo "label: '$ac->name',\n";
                echo "backgroundColor: '$colors[$acid]',\n";
                echo "data: [\n";
                $timeslots = $this->Menu_model->get_timeslot();
                foreach ($timeslots as $tm) {
                    $t1 = $tm->time1;
                    $t2 = $tm->time2;
                    $tasks = $this->Menu_model->get_taskplantimewise($uid, $sd, $ed, $acid, $t1, $t2);
                    $total = 0;
                    foreach ($tasks as $ts) {
                        $total += $ts->cont;
                    }
                    echo "$total, ";
                }
                echo "],\n";
                echo "stack: 'Stack 0',\n";
                echo "},\n";
            }
            ?>
            
            
            <?php $timeslot = $this->Menu_model->get_timeslot(); $i=1; foreach($timeslot as $tm){ $t1=$tm->time1; $t2=$tm->time2;?>
                {
                    label: '<?=$t1?>-<?=$t2?>',
                    backgroundColor: ['<?=$colors[$i]?>'],
                    data: [
                        <?php
                         $action = $this->Menu_model->get_action(); foreach($action as $ac){ $acid = $ac->id;
                         $status = $this->Menu_model->get_status(); foreach($status as $st){ $stid = $st->id;
                         $taskstatus = $this->Menu_model->get_taskplantimewisestatus($uid,$acid,$stid,$sd,$ed,$t1,$t2);foreach($taskstatus as $tasks){?>
                        <?=$tasks->cont?>,
                        <?php }}} ?>
                    ],
                    stack: 'Stack 1',
                },
            <?php $i++;} ?>
            
            
            
            
           

            
        ],
    };

    var combinedCtx = document.getElementById("combinedChartID11").getContext('2d');
    var combinedChart = new Chart(combinedCtx, {
        type: 'bar',
        data: combinedData11,
        options: {
            plugins: {
                title: {
                    display: true,
                    text: 'Time Wise Task Plan',
                },
            },
            scales: {
                x: {
                    stacked: true, // Enable full stacking for X-axis
                },
                y: {
                    beginAtZero: true,
                },
            },
        },
    });
</script>







<div style="width: 80%; margin: auto;">
    <canvas id="combinedChartID12"></canvas>
</div>

<script>
    var combinedData12 = {
        labels: [
            <?php
            $timeslots = $this->Menu_model->get_timeslot();
            foreach ($timeslots as $tm) {
                echo "'$tm->time1-$tm->time2', ";
            }
            ?>
        ],
        datasets: [
            <?php
            $actions = $this->Menu_model->get_action();
            foreach ($actions as $ac) {
                $acid = $ac->id;
                echo "{\n";
                echo "label: '$ac->name',\n";
                echo "backgroundColor: '$colors[$acid]',\n";
                echo "data: [\n";
                $timeslots = $this->Menu_model->get_timeslot();
                foreach ($timeslots as $tm) {
                    $t1 = $tm->time1;
                    $t2 = $tm->time2;
                    $tasks = $this->Menu_model->get_taskupdatetimewise($uid, $sd, $ed, $acid, $t1, $t2);
                    $total = 0;
                    foreach ($tasks as $ts) {
                        $total += $ts->cont;
                    }
                    echo "$total, ";
                }
                echo "],\n";
                echo "stack: 'Stack 0',\n";
                echo "},\n";
            }
            ?>
            
            <?php $status = $this->Menu_model->get_status(); $i=1; foreach($status as $st){ $stid = $st->id;?>
                {
                    label: '<?=$st->name?>',
                    backgroundColor: ['<?=$colors[$i]?>'],
                    data: [
                        
                        <?php 
                        
                        $action = $this->Menu_model->get_action(); foreach($action as $ac){ $acid = $ac->id;
                        $taskstatus = $this->Menu_model->get_tasktypependingstatusaypybys($uid,$sd,$ed,$stid,$acid);foreach($taskstatus as $tasks){?>
                        
                        <?=$tasks->cont?>,
                        <?php }} ?>
                        
                        
                        
                    ],
                    stack: 'Stack 1',
                },
                <?php $i++;} ?>
            
        ],
    };

    var combinedCtx = document.getElementById("combinedChartID12").getContext('2d');
    var combinedChart = new Chart(combinedCtx, {
        type: 'bar',
        data: combinedData12,
        options: {
            plugins: {
                title: {
                    display: true,
                    text: 'Time Wise Task Update',
                },
            },
            scales: {
                x: {
                    stacked: true, // Enable full stacking for X-axis
                },
                y: {
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
You have made no changes to save.
</script>
</body>
</html>

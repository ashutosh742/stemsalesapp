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

  <div class="content-wrapper">
    <div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1 class="m-0"></h1>
          </div>
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <h4></h4>
            </ol>
          </div>
        </div>
      </div>
    </div>
    

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


<form class="p-3" method="POST" action="TaskGraph6">
    <input type="date" name="sdate" class="mr-2" value="<?=$sd?>" min="2023-04-01">
    <input type="date" name="edate" class="mr-2" value="<?=$ed?>">
    <button type="submit" class="bg-primary text-light">Filter</button>
</form>



<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.7.0/chart.min.js"></script>
<?php  $colors = array('red','blue','green','yellow','purple','orange','pink','brown','cyan','magenta','teal','lime','violet','indigo','gray'); ?>
    
    
        
        
        
  <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
  <script type="text/javascript">
    google.charts.load("current", {packages:['corechart']});google.charts.setOnLoadCallback(drawChart1);
      function drawChart1() {
        var data = google.visualization.arrayToDataTable([
          ['Status', 'No of Compnay'],
            <?php $status = $this->Menu_model->get_taskremarkwise($uid,$sd,$ed);
             foreach($status as $st){?>
             ["<?=$st->remarks?> (<?=$st->cont?>)", <?=$st->cont?>],
    	    <?php } ?>
        ]);

        var options = {
          title: 'Task Remark Wise',
          is3D: true,
        };

        var chart = new google.visualization.PieChart(document.getElementById('piechart3d1'));
        chart.draw(data, options);
      }
    </script>
    
    
    
   
      
      
      
      
 <div class="row">
 <div id="piechart3d1" class="col-6 mt-50"></div>
 <div id="piechart3d2" class="col-6 mt-50"></div>
 <div id="piechart3d3" class="col-6 mt-50"></div>
 <div id="piechart3d4" class="col-6 mt-50"></div>
</div>






<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.7.0/chart.min.js"></script>
<?php  $colors = array('red','blue','green','yellow','purple','orange','pink','brown','cyan','magenta','teal','lime','violet','indigo','gray'); ?>
    
    <div style="width: 80%; margin: auto;">
        <canvas id="stackedChart"></canvas>
    </div>

    <script>
        var stackedData = {
            labels: [
                
                <?php $timeslot = $this->Menu_model->get_timeslot(); foreach($timeslot as $tl){$t1=$tl->time1;$t2=$tl->time2;?>
                    '<?=$t1?> to <?=$t2?>',
                <?php } ?>
                ],
            datasets: [
                
                <?php $i=0; $action = $this->Menu_model->get_action(); $i=0; foreach($action as $ac){
                        $acid = $ac->id; ?>
                {
                    label: '<?=$ac->name?>',
                    backgroundColor: '<?=$colors[$i]?>',
                    data: [
                        <?php $timeslot = $this->Menu_model->get_timeslot(); foreach($timeslot as $tl){$t1=$tl->time1;$t2=$tl->time2;
                          $task = $this->Menu_model->get_timewtplanac($uid,$sd,$ed,$t1,$t2,$acid); foreach($task as $ta){?>
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
                        <?php $timeslot = $this->Menu_model->get_timeslot(); foreach($timeslot as $tl){$t1=$tl->time1;$t2=$tl->time2;
                          $task = $this->Menu_model->get_timewtplanacst($uid,$sd,$ed,$t1,$t2,$stid,$acid); foreach($task as $ta){?>
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
                        text: 'Time Wise Task Plan',
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
                
                <?php $timeslot = $this->Menu_model->get_timeslot(); foreach($timeslot as $tl){$t1=$tl->time1;$t2=$tl->time2;?>
                    '<?=$t1?> to <?=$t2?>',
                <?php } ?>
                ],
            datasets: [
                
                <?php $i=0; $action = $this->Menu_model->get_action(); $i=0; foreach($action as $ac){
                        $acid = $ac->id; ?>
                {
                    label: '<?=$ac->name?>',
                    backgroundColor: '<?=$colors[$i]?>',
                    data: [
                        <?php $timeslot = $this->Menu_model->get_timeslot(); foreach($timeslot as $tl){$t1=$tl->time1;$t2=$tl->time2;
                          $task = $this->Menu_model->get_timewtiniac($uid,$sd,$ed,$t1,$t2,$acid); foreach($task as $ta){?>
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
                        <?php $timeslot = $this->Menu_model->get_timeslot(); foreach($timeslot as $tl){$t1=$tl->time1;$t2=$tl->time2;
                          $task = $this->Menu_model->get_timewtiniacst($uid,$sd,$ed,$t1,$t2,$stid,$acid); foreach($task as $ta){?>
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
                        text: 'Time Wise Task Initiated',
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
                
                <?php $timeslot = $this->Menu_model->get_timeslot(); foreach($timeslot as $tl){$t1=$tl->time1;$t2=$tl->time2;?>
                    '<?=$t1?> to <?=$t2?>',
                <?php } ?>
                ],
            datasets: [
                
                <?php $i=0; $action = $this->Menu_model->get_action(); $i=0; foreach($action as $ac){
                        $acid = $ac->id; ?>
                {
                    label: '<?=$ac->name?>',
                    backgroundColor: '<?=$colors[$i]?>',
                    data: [
                        <?php $timeslot = $this->Menu_model->get_timeslot(); foreach($timeslot as $tl){$t1=$tl->time1;$t2=$tl->time2;
                          $task = $this->Menu_model->get_timewtupac($uid,$sd,$ed,$t1,$t2,$acid); foreach($task as $ta){?>
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
                        <?php $timeslot = $this->Menu_model->get_timeslot(); foreach($timeslot as $tl){$t1=$tl->time1;$t2=$tl->time2;
                          $task = $this->Menu_model->get_timewtupacst($uid,$sd,$ed,$t1,$t2,$stid,$acid); foreach($task as $ta){?>
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
                        text: 'Time Wise Task Update',
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

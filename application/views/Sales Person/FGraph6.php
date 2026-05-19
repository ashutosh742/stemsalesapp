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
              <center><h5>Stage Wise Funnel Graph</h5></center><hr>

          
<div class="row">
<div id="chartdiv" style="width: 100%; height: 500px;"></div>
</div>

<div class="row">
<div id="columnchart_values1" class="col-6 mt-12"></div>
<div id="columnchart_values2" class="col-6 mt-12"></div> 

</div>

<div class="row">
<div id="columnchart_values3" class="col-6 mt-50" ></div>
<div id="columnchart_values4" class="col-6 mt-50"></div>
</div> 


<script src="https://cdn.amcharts.com/lib/5/index.js"></script>
<script src="https://cdn.amcharts.com/lib/5/percent.js"></script>
<script src="https://cdn.amcharts.com/lib/5/themes/Animated.js"></script>
<script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
<script>
    am5.ready(function() {
        var root = am5.Root.new("chartdiv");
        root.setThemes([
          am5themes_Animated.new(root)
        ]);
        
        var chart = root.container.children.push(am5percent.SlicedChart.new(root, {
          layout: root.verticalLayout
        }));
        
        var series = chart.series.push(am5percent.FunnelSeries.new(root, {
          alignLabels: false,
          orientation: "vertical",
          valueField: "value",
          categoryField: "category"
        }));
        
        series.data.setAll([
            
            <?php $stage = $this->Menu_model->get_fannals($uid,'1');
                 foreach($stage as $dt){?>
                 { value: <?=$dt->stage1?>, category: "Qualification Stage (<?=$dt->stage1?>)" },
                 { value: 0, category: "<?=round(($dt->stage2/$dt->stage1)*100)?>%" },
                 { value: <?=$dt->stage2?>, category: "Meeting (<?=$dt->stage2?>)" },
                 { value: 0, category: "<?=round(($dt->stage3/$dt->stage2)*100)?>%" },
                 { value: <?=$dt->stage3?>, category: "Proposal (<?=$dt->stage3?>)" },
                 { value: 0, category: "<?=round(($dt->stage4/$dt->stage3)*100)?>%" },
                 { value: <?=$dt->stage4?>, category: "Closure (<?=$dt->stage4?>)" },
        	<?php } ?>
        ]);
        
        series.appear();
        
        var legend = chart.children.push(am5.Legend.new(root, {
          centerX: am5.p50,
          x: am5.p50,
          marginTop: 15,
          marginBottom: 15
        }));
        
        legend.data.setAll(series.dataItems);
        
        chart.appear(1000, 100);
    });
    
    

    google.charts.load("current", { packages: ['corechart'] });
    google.charts.setOnLoadCallback(drawChart1);
    function drawChart1() {
      var data = google.visualization.arrayToDataTable([
        ["Element", "Companies", { role: "style" },"uid","stid"],
        <?php $mdata = $this->Menu_model->get_fannalstatus($uid,'1');
         foreach($mdata as $dt){?>
         ["Open", <?=$dt->cont?>, "#b87333","<?=$uid?>","1"],
	    <?php } ?>
	    <?php $mdata = $this->Menu_model->get_fannalstatus($uid,'8');
         foreach($mdata as $dt){?>
         ["Open RPEM", <?=$dt->cont?>, "silver","<?=$uid?>","8"],
	    <?php } ?>
	    <?php $mdata = $this->Menu_model->get_fannalstatus($uid,'2');
         foreach($mdata as $dt){?>
         ["Reachout", <?=$dt->cont?>, "gold","<?=$uid?>","2"],
	    <?php } ?>
	    <?php $mdata = $this->Menu_model->get_fannalstatus($uid,'10');
         foreach($mdata as $dt){?>
         ["TTD Reachout", <?=$dt->cont?>, "#e5e4e2","<?=$uid?>","10"],
	    <?php } ?>
	    <?php $mdata = $this->Menu_model->get_fannalstatus($uid,'11');
         foreach($mdata as $dt){?>
         ["WNO Reachout", <?=$dt->cont?>, "##7F2E1C","<?=$uid?>","11"],
	    <?php } ?>
      ]);

      var view = new google.visualization.DataView(data);
      view.setColumns([0, 1, { calc: "stringify", sourceColumn: 1, type: "string", role: "annotation" }, 2]);

      var options = {
        title: "Qualification Stage: Open, Open RPEM, Reachout, TTD Reachout, WNO Reachout",
        width: 600,
        height: 400,
        bar: { groupWidth: "95%" },
        legend: { position: "none" },
      };

      var chart = new google.visualization.ColumnChart(document.getElementById('columnchart_values1'));

      google.visualization.events.addListener(chart, 'select', chartClickHandler);

      chart.draw(view, options);

      function chartClickHandler() {
        var selection = chart.getSelection()[0];
        if (selection) {
          var uuid = data.getValue(selection.row, 3);
          var stid = data.getValue(selection.row, 4);
          var code = 1;
              $.ajax({
                url:'<?=base_url();?>Menu/gdata',
                type: "POST",
                data: {
                    stid: stid,
                    uuid: uuid,
                    code: code
                },
                cache: false,
                success: function a(result){
                $("#grid-view").html(result);
                }
                });
                
                $.ajax({
                url:'<?=base_url();?>Menu/tbdata',
                type: "POST",
                data: {
                    stid: stid,
                    uuid: uuid,
                    code: code
                },
                cache: false,
                success: function a(result){
                $("#tbdata").html(result);
                }
                });
        }
      }
    }
    
    
  
  google.charts.load("current", {packages:['corechart']});
    google.charts.setOnLoadCallback(drawChart2);
    function drawChart2() {
      var data = google.visualization.arrayToDataTable([
        ["Element", "Companies", { role: "style" },"uid","stid" ],
        <?php $mdata = $this->Menu_model->get_fannalstatus($uid,'3');
         foreach($mdata as $dt){?>
         ["Tentative", <?=$dt->cont?>, "#b87333","<?=$uid?>","3"],
	    <?php } ?>
      ]);

      var view = new google.visualization.DataView(data);
      view.setColumns([0, 1,
                       { calc: "stringify",
                         sourceColumn: 1,
                         type: "string",
                         role: "annotation" },
                       2]);

      var options = {
        title: "Meeting Stage (<?=$stage[0]->stage2?>): Tentative",
        width: 600,
        height: 400,
        bar: {groupWidth: "95%"},
        legend: { position: "none" },
      };
      var chart = new google.visualization.ColumnChart(document.getElementById('columnchart_values2'));

      google.visualization.events.addListener(chart, 'select', chartClickHandler);

      chart.draw(view, options);

      function chartClickHandler() {
        var selection = chart.getSelection()[0];
        if (selection) {
          var uuid = data.getValue(selection.row, 3);
          var stid = data.getValue(selection.row, 4);
          var code = 1;
              $.ajax({
                url:'<?=base_url();?>Menu/gdata',
                type: "POST",
                data: {
                    stid: stid,
                    uuid: uuid,
                    code: code
                },
                cache: false,
                success: function a(result){
                $("#grid-view").html(result);
                }
                });
                
                $.ajax({
                url:'<?=base_url();?>Menu/tbdata',
                type: "POST",
                data: {
                    stid: stid,
                    uuid: uuid,
                    code: code
                },
                cache: false,
                success: function a(result){
                $("#tbdata").html(result);
                }
                });
        }
      }
    }
  
  google.charts.load("current", {packages:['corechart']});
  google.charts.setOnLoadCallback(drawChart3);
    function drawChart3() {
      var data = google.visualization.arrayToDataTable([
        ["Element", "Companies", { role: "style" },"uid","stid" ],
        <?php $mdata = $this->Menu_model->get_fannalstatus($uid,'6');
         foreach($mdata as $dt){?>
         ["Positive", <?=$dt->cont?>, "#b87333","<?=$uid?>","6"],
	    <?php } ?>
	    <?php $mdata = $this->Menu_model->get_fannalstatus($uid,'9');
         foreach($mdata as $dt){?>
         ["Very Positive", <?=$dt->cont?>, "silver","<?=$uid?>","9"],
	    <?php } ?>
	    <?php $mdata = $this->Menu_model->get_fannalstatus($uid,'12');
         foreach($mdata as $dt){?>
         ["Positive NAP", <?=$dt->cont?>, "gold","<?=$uid?>","12"],
	    <?php } ?>
	    <?php $mdata = $this->Menu_model->get_fannalstatus($uid,'13');
         foreach($mdata as $dt){?>
         ["Very Positive NAP", <?=$dt->cont?>, "#e5e4e2","<?=$uid?>","13"],
	    <?php } ?>
      ]);

      var view = new google.visualization.DataView(data);
      view.setColumns([0, 1,
                       { calc: "stringify",
                         sourceColumn: 1,
                         type: "string",
                         role: "annotation" },
                       2]);

      var options = {
        title: "Proposal (<?=$stage[0]->stage3?>): Positive, Positive NAP, Very Positive, Very Positive NAP",
        width: 600,
        height: 400,
        bar: {groupWidth: "95%"},
        legend: { position: "none" },
      };
      var chart = new google.visualization.ColumnChart(document.getElementById('columnchart_values3'));

      google.visualization.events.addListener(chart, 'select', chartClickHandler);

      chart.draw(view, options);

      function chartClickHandler() {
        var selection = chart.getSelection()[0];
        if (selection) {
          var uuid = data.getValue(selection.row, 3);
          var stid = data.getValue(selection.row, 4);
          var code = 1;
              $.ajax({
                url:'<?=base_url();?>Menu/gdata',
                type: "POST",
                data: {
                    stid: stid,
                    uuid: uuid,
                    code: code
                },
                cache: false,
                success: function a(result){
                $("#grid-view").html(result);
                }
                });
                
                $.ajax({
                url:'<?=base_url();?>Menu/tbdata',
                type: "POST",
                data: {
                    stid: stid,
                    uuid: uuid,
                    code: code
                },
                cache: false,
                success: function a(result){
                $("#tbdata").html(result);
                }
                });
        }
      }
    }
  
  
  google.charts.load("current", {packages:['corechart']});
    google.charts.setOnLoadCallback(drawChart4);
    function drawChart4() {
      var data = google.visualization.arrayToDataTable([
        ["Element", "Companies", { role: "style" },"uid","stid" ],
        <?php $mdata = $this->Menu_model->get_fannalstatus($uid,'7');
         foreach($mdata as $dt){?>
         ["Closure", <?=$dt->cont?>, "#b87333","<?=$uid?>","7"],
	    <?php } ?>
      ]);

      var view = new google.visualization.DataView(data);
      view.setColumns([0, 1,
                       { calc: "stringify",
                         sourceColumn: 1,
                         type: "string",
                         role: "annotation" },
                       2]);

      var options = {
        title: "Closure (<?=$stage[0]->stage4?>): Closure",
        width: 600,
        height: 400,
        bar: {groupWidth: "95%"},
        legend: { position: "none" },
      };
      var chart = new google.visualization.ColumnChart(document.getElementById('columnchart_values4'));

      google.visualization.events.addListener(chart, 'select', chartClickHandler);

      chart.draw(view, options);

      function chartClickHandler() {
        var selection = chart.getSelection()[0];
        if (selection) {
          var uuid = data.getValue(selection.row, 3);
          var stid = data.getValue(selection.row, 4);
          var code = 1;
              $.ajax({
                url:'<?=base_url();?>Menu/gdata',
                type: "POST",
                data: {
                    stid: stid,
                    uuid: uuid,
                    code: code
                },
                cache: false,
                success: function a(result){
                $("#grid-view").html(result);
                }
                });
                
                $.ajax({
                url:'<?=base_url();?>Menu/tbdata',
                type: "POST",
                data: {
                    stid: stid,
                    uuid: uuid,
                    code: code
                },
                cache: false,
                success: function a(result){
                $("#tbdata").html(result);
                }
                });
        }
      }
    }
</script>



      
          </div>
      </div>
    </section>
    
    
    
    <section class="content">
      <div class="container-fluid">
          <div class="card col-12  p-3 text-center">
              <div class="container">
                  <div class="row">
                      <button id="grid-view-btn" class="btn border">Grid View</button>
                      <button id="list-view-btn" class="btn border">Xls View</button>
                      <button id="tabular-view-btn" class="btn border">Tabular View</button>
                  </div>
                </div>
              <div class="container-fluid card p-5" id="data-container">
                <div class="row text-center" id="grid-view">
                    
                    <?php 
                    $sttid = $this->Menu_model->get_status();
                    $uniquePY = [];
                    foreach ($sttid as $sttid) {
                        $uniqueST[] =  $sttid->id; 
                    }
                    $uniqueST = array_unique($uniqueST);
                    $funnal = $this->Menu_model->get_fannal($uid);
                ?>
                    <div class="filters">
                        <button type="button" class="btn btn-primary" class="filter-button" data-filter="all">All (<?=sizeof($funnal)?>)</button>
                        <?php foreach ($uniqueST as $index => $ST) {
                        $STname=$this->Menu_model->get_statusbyid($ST);
                        $sfunnal = $this->Menu_model->get_bdcombystatus($uid,$ST);
                        ?>
                          <button type="button" class="btn <?=$STname[0]->color?> m-1" class="filter-button" data-filter="<?=$ST?>"><?=$STname[0]->name?> (<?=sizeof($sfunnal)?>)</button>
                        <?php } ?>
                    </div><hr><hr><hr>
                    
                    
                    
                    
                    
                    <?php 
                         $mdata = $this->Menu_model->get_fannal($uid);
                         foreach($mdata as $dt){
                         $cid = $dt->cmpid_id;
                         $init=$this->Menu_model->get_initcallbyid($cid);
                         $ciid = $init[0]->id;
                         $ldscd=$this->Menu_model->get_laststatuschangedate($ciid);
                         $updatedt = $ldscd[0]->updatedt;
                         $stlogs = $ldscd[0]->cont;
                         $cdate=date('Y-m-d H:i:s');
                         $timediff = $this->Menu_model->timediff($updatedt,$cdate);
                         $pid = $init[0]->apst;  
                         $patid = $dt->partnerType_id;
                         if($patid){$patrid = $this->Menu_model->get_partnerbyid($patid);$patid = $patrid[0]->name;$pclr=$patrid[0]->clr;} else{$patid='';$pclr='';}
                         if($patid){$sid = $dt->cstatus;$stid=$this->Menu_model->get_statusbyid($sid);$sid=$stid[0]->name;$sclr=$stid[0]->clr;}
                         else{$sid='';$sclr='';}
                         $tblc=$this->Menu_model->get_tblbyidwithremark($ciid);
                         $logs = sizeof($tblc);
                         if($logs>0){$appoint = $tblc[0]->appointmentdatetime;
                         $nextaction = $tblc[0]->nextaction;
                         $remarks = $tblc[0]->remarks;}else{$appoint='';$nextaction='';$remarks='';} 
                    ?>
                                     
                        <div class="col-sm-12 col-md-4 col-lg-4 mb-4 filter-item">
                          <div class="card p-3 border rounded border-success hover-div d-flex flex-column align-items-stretch h-100 text-dark">
                                 Current Status<br><b style="color:<?=$sclr?>"><?=$sid?></b><hr>
                                 Company Name<br><b style="color:<?=$pclr?>"><?=$dt->compname?></b><hr>
                                 Address<br><b><?=$dt->address?></b><hr>
                                 City<br><b><?=$dt->city?></b><hr>
                                 State<br><b><?=$dt->state?></b><hr>
                                 Partner Type<br><b style="color:<?=$pclr?>"><?=$patid?></b><hr>
                                 Category<br><b><?php if($dt->focus_funnel=='yes'){echo 'Focus Funnel, ';} if($dt->upsell_client=='yes'){echo 'Upsell Client, ';} if($dt->keycompany=='yes'){echo 'Key Company';}?></b> <hr>
                                 Current Remark<br><b><?=$remarks?></b></a><hr>
                                 Total Logs on Same Status<br><b><?=$stlogs?></b></a><hr>
                                 Current Status of from whitch date<br><b><?=$ldscd[0]->updatedt?></b></a><hr>
                                 Same Status from Current Time<br><b><?=$timediff?></b>
                                <div class="rounded-circle bg-danger" style="position: absolute;bottom: -10px; left: 40%; transform: translateX(-50%); width: 20px; height: 20px;"></div>
                                <div class="rounded-circle bg-danger" style="position: absolute;bottom: -10px; left: 60%; transform: translateX(-50%); width: 20px; height: 20px;"></div>
                          </div>
                        </div>
                       <?php } ?>
                    
                    
                        
                </div>
                <div id="list-view" style="display: none;">
                    	<div class="table-responsive" id="tbdata">
                    		<table id="example1" class="table table-striped table-bordered" cellspacing="0" width="100%">
                				<thead>
                					<tr>
                						<th>S NO</th>
                						<th>Current Status</th>
                						<th>Company Name</th>
                						<th>Address</th>
                						<th>City</th>
                						<th>State</th>
                						<th>Partner Type</th>
                						<th>Category</th>
                						<th>Current Remark</th>
                						<th>Total Logs on Same Status</th>
                						<th>Current Status of from whitch date</th>
                						<th>Same Status from Current Time</th>
                                    </tr>
                				</thead>
                				<tbody>
                				    
                				    <?php 
                				    $i=1;
                                     $mdata = $this->Menu_model->get_fannal($uid);
                                     foreach($mdata as $dt){
                                     $cid = $dt->cmpid_id;
                                     $init=$this->Menu_model->get_initcallbyid($cid);
                                     $ciid = $init[0]->id;
                                     $ldscd=$this->Menu_model->get_laststatuschangedate($ciid);
                                     $updatedt = $ldscd[0]->updatedt;
                                     $stlogs = $ldscd[0]->cont;
                                     $cdate=date('Y-m-d H:i:s');
                                     $timediff = $this->Menu_model->timediff($updatedt,$cdate);
                                     $pid = $init[0]->apst;  
                                     $patid = $dt->partnerType_id;
                                     if($patid){$patrid = $this->Menu_model->get_partnerbyid($patid);$patid = $patrid[0]->name;$pclr=$patrid[0]->clr;} else{$patid='';$pclr='';}
                                     if($patid){$sid = $dt->cstatus;$stid=$this->Menu_model->get_statusbyid($sid);$sid=$stid[0]->name;$sclr=$stid[0]->clr;}
                                     else{$sid='';$sclr='';}
                                     $tblc=$this->Menu_model->get_tblbyidwithremark($ciid);
                                     $logs = sizeof($tblc);
                                     if($logs>0){$appoint = $tblc[0]->appointmentdatetime;
                                     $nextaction = $tblc[0]->nextaction;
                                     $remarks = $tblc[0]->remarks;}else{$appoint='';$nextaction='';$remarks='';} 
                                    ?>
                                    
                                    <tr>
                                         <td><?=$i++?></td>
                                         <td style="color:<?=$sclr?>"><?=$sid?></td>
                                         <td style="color:<?=$pclr?>"><?=$dt->compname?></td>
                                         <td><?=$dt->address?></td>
                                         <td><?=$dt->city?></td>
                                         <td><?=$dt->state?></td>
                                         <td style="color:<?=$pclr?>"><?=$patid?></td>
                                         <td><?php if($dt->focus_funnel=='yes'){echo 'Focus Funnel, ';} if($dt->upsell_client=='yes'){echo 'Upsell Client, ';} if($dt->keycompany=='yes'){echo 'Key Company';}?></td>
                                         <td><?=$remarks?></td>
                                         <td><?=$stlogs?></td>
                                         <td><?=$ldscd[0]->updatedt?></td>
                                         <td><?=$timediff?></td>
                                    </tr>
                                    
                                    <?php } ?>
                				    
                				    
                				</tbody>
                    		</table> 
                    	</div>
                </div>
                <div id="tabular-view" style="display: none;">
                    <div class="card p-3 col-lg-4 col-sm m-auto bg-light">
                    <?php
                      $status = $this->Menu_model->get_fannalstwise($uid);
                      foreach ($status as $st) {?>
                      <b><?=$st->stname?> - <?=$st->cont?></b><hr>
                      <?php } ?>
                      </div>
                </div>  
              
              
              
              
              
              
              
              
              
              
              
              <script>
                document.getElementById("grid-view-btn").addEventListener("click", function () {
                    document.getElementById("grid-view").style.display = "block";
                    document.getElementById("list-view").style.display = "none";
                    document.getElementById("tabular-view").style.display = "none";
                    document.getElementById("list-view-btn").classList.remove('btn-info');
                    document.getElementById("tabular-view-btn").classList.remove('btn-info');
                    document.getElementById("grid-view-btn").classList.add('btn-info');
                });
                document.getElementById("list-view-btn").addEventListener("click", function () {
                    document.getElementById("grid-view").style.display = "none";
                    document.getElementById("tabular-view").style.display = "none";
                    document.getElementById("list-view").style.display = "block";
                    document.getElementById("grid-view-btn").classList.remove('btn-info');
                    document.getElementById("tabular-view-btn").classList.remove('btn-info');
                    document.getElementById("list-view-btn").classList.add('btn-info');
                });
                document.getElementById("tabular-view-btn").addEventListener("click", function () {
                    document.getElementById("grid-view").style.display = "none";
                    document.getElementById("list-view").style.display = "none";
                    document.getElementById("tabular-view").style.display = "block";
                    document.getElementById("grid-view-btn").classList.remove('btn-info');
                    document.getElementById("list-view-btn").classList.remove('btn-info');
                    document.getElementById("tabular-view-btn").classList.add('btn-info');
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
      "responsive": false, "lengthChange": false, "autoWidth": false,
      "buttons": ["copy", "csv", "excel", "pdf", "print", "colvis"]
    }).buttons().container().appendTo('#example1_wrapper .col-md-6:eq(0)');
</script>
</body>
</html>
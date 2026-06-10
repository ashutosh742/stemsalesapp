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



<div id="chartdiv"></div>


<div class="row">
<div id="columnchart_values1" class="col-6 mt-12"></div>
<div id="columnchart_values2" class="col-6 mt-12"></div> 

</div>

<div class="row">
<div id="columnchart_values3" class="col-6 mt-50" ></div>
<div id="columnchart_values4" class="col-6 mt-50"></div>
</div>       
             
             
<div class="row">
 <div id="piechart3d1" class="col-6 mt-50"></div>
 <div id="piechart3d2" class="col-6 mt-50"></div>
 <div id="piechart3d3" class="col-6 mt-50"></div>
 <div id="piechart3d4" class="col-6 mt-50"></div>
</div>




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
    
    <?php $stage = $this->Menu_model->get_pstfannals($uid,'1');
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
</script>




<script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
  <script type="text/javascript">
    google.charts.load("current", {packages:['corechart']});
    google.charts.setOnLoadCallback(drawChart1);
    function drawChart1() {
      var data = google.visualization.arrayToDataTable([
        ["Element", "Companies", { role: "style" } ],
        <?php $mdata = $this->Menu_model->get_pstfannalstatus($uid,'1');
         foreach($mdata as $dt){?>
         ["Open", <?=$dt->cont?>, "#b87333"],
	    <?php } ?>
	    <?php $mdata = $this->Menu_model->get_pstfannalstatus($uid,'8');
         foreach($mdata as $dt){?>
         ["Open RPEM", <?=$dt->cont?>, "silver"],
	    <?php } ?>
	    <?php $mdata = $this->Menu_model->get_pstfannalstatus($uid,'2');
         foreach($mdata as $dt){?>
         ["Reachout", <?=$dt->cont?>, "gold"],
	    <?php } ?>
	    <?php $mdata = $this->Menu_model->get_pstfannalstatus($uid,'10');
         foreach($mdata as $dt){?>
         ["TTD Reachout", <?=$dt->cont?>, "#e5e4e2"],
	    <?php } ?>
	    <?php $mdata = $this->Menu_model->get_pstfannalstatus($uid,'11');
         foreach($mdata as $dt){?>
         ["WNO Reachout", <?=$dt->cont?>, "##7F2E1C"],
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
        title: "Qualification Stage (<?=$stage[0]->stage1?>): Open, Open RPEM, Reachout, TTD Reachout, WNO Reachout",
        width: 600,
        height: 400,
        bar: {groupWidth: "95%"},
        legend: { position: "none" },
      };
      var chart = new google.visualization.ColumnChart(document.getElementById("columnchart_values1"));
      chart.draw(view, options);
  }
  
  google.charts.load("current", {packages:['corechart']});
    google.charts.setOnLoadCallback(drawChart2);
    function drawChart2() {
      var data = google.visualization.arrayToDataTable([
        ["Element", "Companies", { role: "style" } ],
        <?php $mdata = $this->Menu_model->get_fannalstatus($uid,'3');
         foreach($mdata as $dt){?>
         ["Tentative", <?=$dt->cont?>, "#b87333"],
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
      var chart = new google.visualization.ColumnChart(document.getElementById("columnchart_values2"));
      chart.draw(view, options);
  }
  
  google.charts.load("current", {packages:['corechart']});
  google.charts.setOnLoadCallback(drawChart3);
    function drawChart3() {
      var data = google.visualization.arrayToDataTable([
        ["Element", "Companies", { role: "style" } ],
        <?php $mdata = $this->Menu_model->get_fannalstatus($uid,'6');
         foreach($mdata as $dt){?>
         ["Positive", <?=$dt->cont?>, "#b87333"],
	    <?php } ?>
	    <?php $mdata = $this->Menu_model->get_fannalstatus($uid,'9');
         foreach($mdata as $dt){?>
         ["Very Positive", <?=$dt->cont?>, "silver"],
	    <?php } ?>
	    <?php $mdata = $this->Menu_model->get_fannalstatus($uid,'12');
         foreach($mdata as $dt){?>
         ["Positive NAP", <?=$dt->cont?>, "gold"],
	    <?php } ?>
	    <?php $mdata = $this->Menu_model->get_fannalstatus($uid,'13');
         foreach($mdata as $dt){?>
         ["Very Positive NAP", <?=$dt->cont?>, "#e5e4e2"],
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
      var chart = new google.visualization.ColumnChart(document.getElementById("columnchart_values3"));
      chart.draw(view, options);
  }
  
  
  google.charts.load("current", {packages:['corechart']});
    google.charts.setOnLoadCallback(drawChart4);
    function drawChart4() {
      var data = google.visualization.arrayToDataTable([
        ["Element", "Companies", { role: "style" } ],
        <?php $mdata = $this->Menu_model->get_fannalstatus($uid,'7');
         foreach($mdata as $dt){?>
         ["Closure", <?=$dt->cont?>, "#b87333"],
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
      var chart = new google.visualization.ColumnChart(document.getElementById("columnchart_values4"));
      chart.draw(view, options);
  }
  
  
      google.charts.load("current", {packages:["corechart"]});
      google.charts.setOnLoadCallback(drawChart10);
      function drawChart10() {
        var data = google.visualization.arrayToDataTable([
          ['Partner Type', 'No of Compnay'],
        <?php $partner = $this->Menu_model->get_fannalpartner($uid);
         foreach($partner as $pa){?>
         ["<?=$pa->pname?> (<?=$pa->cont?>)", <?=$pa->cont?>],
	    <?php } ?>
	    
        ]);

        var options = {
          title: 'Partner Type wise funnel',
          is3D: true,
        };

        var chart = new google.visualization.PieChart(document.getElementById('piechart3d1'));
        chart.draw(data, options);
      }
      
      
      google.charts.setOnLoadCallback(drawChart11);
      function drawChart11() {
        var data = google.visualization.arrayToDataTable([
          ['Category', 'No of Compnay'],
          <?php $cat = $this->Menu_model->get_fannalcat($uid);?>
         ["No Category (<?=$cat[0]->nocat?>)", <?=$cat[0]->nocat?>],
         ["Top Spender (<?=$cat[0]->topspender?>)", <?=$cat[0]->topspender?>],
         ["Focus Funnel (<?=$cat[0]->focus_funnel?>)", <?=$cat[0]->focus_funnel?>],
         ["Upsell Client (<?=$cat[0]->upsell_client?>)", <?=$cat[0]->upsell_client?>],
         ["Key Client (<?=$cat[0]->keycompany?>)", <?=$cat[0]->keycompany?>],
         ["Positive Key Client (<?=$cat[0]->pkclient?>)", <?=$cat[0]->pkclient?>],
         ["Priority Client (<?=$cat[0]->priorityc?>)", <?=$cat[0]->priorityc?>],
         
        ]);

        var options = {
          title: 'Category wise funnel',
          is3D: true,
        };

        var chart = new google.visualization.PieChart(document.getElementById('piechart3d2'));
        chart.draw(data, options);
      }
      
      google.charts.setOnLoadCallback(drawChart12);
      function drawChart12() {
        var data = google.visualization.arrayToDataTable([
          ['Status', 'No of Compnay'],
            <?php $status = $this->Menu_model->get_fannalstwise($uid);
             foreach($status as $st){?>
             ["<?=$st->stname?> (<?=$st->cont?>)", <?=$st->cont?>],
    	    <?php } ?>
        ]);

        var options = {
          title: 'Status wise funnel',
          is3D: true,
        };

        var chart = new google.visualization.PieChart(document.getElementById('piechart3d3'));
        chart.draw(data, options);
      }
      
      google.charts.setOnLoadCallback(drawChart13);
      function drawChart13() {
        var data = google.visualization.arrayToDataTable([
          ['City', 'No of Compnay'],
            <?php $city = $this->Menu_model->get_fannalcitywise($uid);
             foreach($city as $ci){?>
             ["<?=$ci->city?> (<?=$ci->cont?>)", <?=$ci->cont?>],
    	    <?php } ?>
        ]);

        var options = {
          title: 'City wise funnel',
          is3D: true,
        };

        var chart = new google.visualization.PieChart(document.getElementById('piechart3d4'));
        chart.draw(data, options);
      }
      
      
      
      
      
        google.charts.load('current', {packages: ['corechart']});
        google.charts.setOnLoadCallback(drawStackedColumnChart);
        function drawStackedColumnChart() {
          var data = new google.visualization.DataTable();
          data.addColumn('string', 'Status');
          data.addColumn('number', '0-10');
          data.addColumn({type: 'string', role: 'annotation'});
          data.addColumn('number', '10-20');
          data.addColumn({type: 'string', role: 'annotation'});
          data.addColumn('number', '20-30');
          data.addColumn({type: 'string', role: 'annotation'});
          data.addColumn('number', '30+');
          data.addColumn({type: 'string', role: 'annotation'});
          data.addColumn({type: 'string', role: 'annotationText'}); // Added for hyperlink URLs
          data.addRows([
            <?php
            $status = $this->Menu_model->get_status();
            foreach($status as $st){
              $stid = $st->id;
              if($st->id!='14'){
                $opensday = $this->Menu_model->get_opensday($uid,$stid,$sd,$ed);
                $countLessThan10 = 0;
                $countLessThan20 = 0;
                $countLessThan30 = 0;
                $countMoreThan30 = 0;
                foreach ($opensday as $object) {
                  if ($object->opensday <= 10) { $countLessThan10++; }
                  if ($object->opensday > 10 && $object->opensday <=20) { $countLessThan20++; }
                  if ($object->opensday > 20 && $object->opensday <=30) { $countLessThan30++; }
                  if ($object->opensday > 30) { $countMoreThan30++; }
                }
                $url="https://stemapp.in/Menu";
                ?>
                ['<?=$st->name?>', <?=$countLessThan10?>, '<?=$countLessThan10?>', <?=$countLessThan20?>, '<?=$countLessThan20?>', <?=$countLessThan30?>, '<?=$countLessThan30?>', <?=$countMoreThan30?>, '<?=$countMoreThan30?>', '<?=$url?>'],
                <?php
              }
            }
            ?>
          ]);
          
          var options = {
            title: 'Companies with same status till day',
            width: 1280,
            height: 1000,
            legend: { position: 'right' },
            isStacked: true,
            hAxis: {
              title: 'Status',
            },
            vAxis: {
              title: 'Count',
              minValue: 0,
            },
            tooltip: {
              trigger: 'both',
              isHtml: true,
            },
            annotations: {
              alwaysOutside: false,
              textStyle: {
                fontSize: 8,
              },
            },
          };
        
          var chart = new google.visualization.ColumnChart(document.getElementById('chart_div'));
          google.visualization.events.addListener(chart, 'select', function() {
            var selection = chart.getSelection();
            if (selection.length > 0) {
              var rowIndex = selection[0].row;
              var hyperlink = data.getValue(rowIndex, 9);
              if (hyperlink) {
                window.location.href = hyperlink;
              }
            }
          });
        
          chart.draw(data, options);
          
        }
        
        google.charts.setOnLoadCallback(drawChart25);
        function drawChart25() {
          var data = new google.visualization.DataTable();
          data.addColumn('string', 'All Action');
          data.addColumn('number', 'Call');
          data.addColumn({type: 'string', role: 'annotation'});
          data.addColumn('number', 'Email');
          data.addColumn({type: 'string', role: 'annotation'});
          data.addColumn('number', 'Scheduled Meeting');
          data.addColumn({type: 'string', role: 'annotation'});
          data.addColumn('number', 'Barg in Meeting');
          data.addColumn({type: 'string', role: 'annotation'});
          data.addColumn('number', 'Whatsapp Activity');
          data.addColumn({type: 'string', role: 'annotation'});
          data.addColumn('number', 'Write MOM');
          data.addColumn({type: 'string', role: 'annotation'});
          data.addColumn('number', 'Write Proposal');
          data.addColumn({type: 'string', role: 'annotation'});
          data.addColumn('number', 'Research');
          data.addColumn({type: 'string', role: 'annotation'});
          data.addColumn('number', 'Mail Check');
          data.addColumn({type: 'string', role: 'annotation'});
          data.addColumn('number', 'Social Networking');
          data.addColumn({type: 'string', role: 'annotation'});
          data.addColumn('number', 'Social Activity');
          data.addColumn({type: 'string', role: 'annotation'});
          data.addColumn({type: 'string', role: 'annotationText'});
        
          <?php
          $status = $this->Menu_model->get_status();
          foreach($status as $st){ 
            $stid = $st->id;
            $swtc = $this->Menu_model->get_statuswisetaskandaction($uid,$stid,$sd,$ed);
            foreach($swtc as $sw){
                $url="https://stemapp.in/Menu";
              ?>
              data.addRow(['<?=$st->name?> ( <?=$sw->cont?>)',
              <?=$sw->a?>, '<?php if($sw->a == 0){echo "";}else{ echo $sw->a;}?>',
              <?=$sw->b?>, '<?php if($sw->b == 0){echo "";}else{ echo $sw->b;}?>',
              <?=$sw->c?>, '<?php if($sw->c == 0){echo "";}else{ echo $sw->c;}?>',
              <?=$sw->d?>, '<?php if($sw->d == 0){echo "";}else{ echo $sw->d;}?>',
              <?=$sw->e?>, '<?php if($sw->e == 0){echo "";}else{ echo $sw->e;}?>',
              <?=$sw->f?>, '<?php if($sw->f == 0){echo "";}else{ echo $sw->f;}?>',
              <?=$sw->g?>, '<?php if($sw->g == 0){echo "";}else{ echo $sw->g;}?>',
              <?=$sw->h?>, '<?php if($sw->h == 0){echo "";}else{ echo $sw->h;}?>',
              <?=$sw->i?>, '<?php if($sw->i == 0){echo "";}else{ echo $sw->i;}?>',
              <?=$sw->j?>, '<?php if($sw->j == 0){echo "";}else{ echo $sw->j;}?>',
              <?=$sw->k?>, '<?php if($sw->k == 0){echo "";}else{ echo $sw->k;}?>','<?=$url?>']);
              <?php
            }
          }
          ?>
        
          var options_fullStacked = {
            isStacked: 'percent',
            height: 600,
            legend: {position: 'top', maxLines: 3},
            vAxis: {
              minValue: 0,
              ticks: [0, 0.3, 0.6, 0.9, 1]
            },
            annotations: {
              alwaysOutside: false,
              textStyle: {
                fontSize: 10,
              },
            },
          };
        
          var chart = new google.visualization.ColumnChart(document.getElementById('chart_div2'));
          google.visualization.events.addListener(chart, 'select', function() {
            var selection = chart.getSelection();
            if (selection.length > 0) {
              var rowIndex = selection[0].row;
              var hyperlink = data.getValue(rowIndex, 23);
              if (hyperlink) {
                window.location.href = hyperlink;
              }
            }
          });
        
          chart.draw(data, options_fullStacked);
        }
        
        
        
        
        

        google.charts.setOnLoadCallback(drawChart41);
        function drawChart41() {
          var data = new google.visualization.DataTable();
          data.addColumn('string', 'All Action');
          data.addColumn('number', 'Call');
          data.addColumn({type: 'string', role: 'annotation'});
          data.addColumn('number', 'Email');
          data.addColumn({type: 'string', role: 'annotation'});
          data.addColumn('number', 'Scheduled Meeting');
          data.addColumn({type: 'string', role: 'annotation'});
          data.addColumn('number', 'Barg in Meeting');
          data.addColumn({type: 'string', role: 'annotation'});
          data.addColumn('number', 'Whatsapp Activity');
          data.addColumn({type: 'string', role: 'annotation'});
          data.addColumn('number', 'Write MOM');
          data.addColumn({type: 'string', role: 'annotation'});
          data.addColumn('number', 'Write Proposal');
          data.addColumn({type: 'string', role: 'annotation'});
          data.addColumn('number', 'Research');
          data.addColumn({type: 'string', role: 'annotation'});
          data.addColumn('number', 'Mail Check');
          data.addColumn({type: 'string', role: 'annotation'});
          data.addColumn('number', 'Social Networking');
          data.addColumn({type: 'string', role: 'annotation'});
          data.addColumn('number', 'Social Activity');
          data.addColumn({type: 'string', role: 'annotation'});
          data.addColumn({type: 'string', role: 'annotationText'});
        
          <?php
            $currentDate = new DateTime();
            $financialYear = ($currentDate->format('m') >= 4) ? $currentDate->format('Y') : ($currentDate->format('Y') - 1);
            for ($month = 4; $month <= 12; $month++) {
            $monthName = DateTime::createFromFormat('!m', $month)->format('F');
            $swtc = $this->Menu_model->get_monthwsaction($uid,$month,$financialYear);
            foreach($swtc as $sw){ $url="https://stemapp.in/Menu"; ?>
              data.addRow(['<?=$monthName?> (<?=$sw->cont?>)',
              <?=$sw->a?>, '<?php if($sw->a == 0){echo "";}else{ echo $sw->a;}?>',
              <?=$sw->b?>, '<?php if($sw->b == 0){echo "";}else{ echo $sw->b;}?>',
              <?=$sw->c?>, '<?php if($sw->c == 0){echo "";}else{ echo $sw->c;}?>',
              <?=$sw->d?>, '<?php if($sw->d == 0){echo "";}else{ echo $sw->d;}?>',
              <?=$sw->e?>, '<?php if($sw->e == 0){echo "";}else{ echo $sw->e;}?>',
              <?=$sw->f?>, '<?php if($sw->f == 0){echo "";}else{ echo $sw->f;}?>',
              <?=$sw->g?>, '<?php if($sw->g == 0){echo "";}else{ echo $sw->g;}?>',
              <?=$sw->h?>, '<?php if($sw->h == 0){echo "";}else{ echo $sw->h;}?>',
              <?=$sw->i?>, '<?php if($sw->i == 0){echo "";}else{ echo $sw->i;}?>',
              <?=$sw->j?>, '<?php if($sw->j == 0){echo "";}else{ echo $sw->j;}?>',
              <?=$sw->k?>, '<?php if($sw->k == 0){echo "";}else{ echo $sw->k;}?>','<?=$url?>']);
              <?php }}?>
        
          var options_fullStacked = {
            isStacked: 'percent',
            height: 600,
            legend: {position: 'top', maxLines: 3},
            vAxis: {
              minValue: 0,
              ticks: [0, 0.3, 0.6, 0.9, 1]
            },
            annotations: {
              alwaysOutside: false,
              textStyle: {
                fontSize: 10,
              },
            },
          };
        
          var chart = new google.visualization.ColumnChart(document.getElementById('chart_div41'));
          google.visualization.events.addListener(chart, 'select', function() {
            var selection = chart.getSelection();
            if (selection.length > 0) {
              var rowIndex = selection[0].row;
              var hyperlink = data.getValue(rowIndex, 23);
              if (hyperlink) {
                window.location.href = hyperlink;
              }
            }
          });
        
          chart.draw(data, options_fullStacked);
        }
        
        
        google.charts.setOnLoadCallback(drawChart42);
        function drawChart42() {
          var data = new google.visualization.DataTable();
          data.addColumn('string', 'Action');
          data.addColumn('number', 'Ontime');
          data.addColumn({type: 'string', role: 'annotation'});
          data.addColumn('number', 'Late');
          data.addColumn({type: 'string', role: 'annotation'});
        
          <?php
            $status = $this->Menu_model->get_status();
            foreach($status as $st){ 
            $stid = $st->id;
            $swtc = $this->Menu_model->get_taskolsna($uid,$stid,'1',$sd,$ed);
            foreach($swtc as $sw){ $url="https://stemapp.in/Menu"; ?>
              data.addRow(['',
              <?=$sw->a?>, '<?php if($sw->a == 0){echo "";}else{ echo $sw->a;}?>',
              <?=$sw->b?>, '<?php if($sw->b == 0){echo "";}else{ echo $sw->b;}?>']);
              <?php }}?>
        
          var options_fullStacked = {
            isStacked: 'percent',
            height: 600,
            legend: {position: 'top', maxLines: 3},
            vAxis: {
              minValue: 0,
              ticks: [0, 0.3, 0.6, 0.9, 1]
            },
            annotations: {
              alwaysOutside: false,
              textStyle: {
                fontSize: 10,
              },
            },
          };
        
          var chart = new google.visualization.ColumnChart(document.getElementById('chart_div42'));
          google.visualization.events.addListener(chart, 'select', function() {
            var selection = chart.getSelection();
            if (selection.length > 0) {
              var rowIndex = selection[0].row;
              var hyperlink = data.getValue(rowIndex, 23);
              if (hyperlink) {
                window.location.href = hyperlink;
              }
            }
          });
        
          chart.draw(data, options_fullStacked);
        }
        
        
        
        
        
        
google.charts.setOnLoadCallback(drawChart43);
function drawChart43() {
  var data = new google.visualization.DataTable();
  data.addColumn('string', 'Action');
  data.addColumn('number', 'Ontime');
  data.addColumn({type: 'string', role: 'annotation'});
  data.addColumn('number', 'Late');
  data.addColumn({type: 'string', role: 'annotation'});

  <?php
    $status = $this->Menu_model->get_status();
    foreach($status as $st){ 
    $stid = $st->id;
    $swtc = $this->Menu_model->get_taskolsna($uid,$stid,'10',$sd,$ed);
    foreach($swtc as $sw){ $url="https://stemapp.in/Menu"; ?>
      data.addRow(['<?=$st->name?> (<?=$sw->cont?>)',
      <?=$sw->a?>, '<?php if($sw->a == 0){echo "";}else{ echo $sw->a;}?>',
      <?=$sw->b?>, '<?php if($sw->b == 0){echo "";}else{ echo $sw->b;}?>']);
      <?php }}?>

  var options_fullStacked = {
    isStacked: 'percent',
    height: 600,
    legend: {position: 'top', maxLines: 3},
    vAxis: {
      minValue: 0,
      ticks: [0, 0.3, 0.6, 0.9, 1]
    },
    annotations: {
      alwaysOutside: false,
      textStyle: {
        fontSize: 10,
      },
    },
  };

  var chart = new google.visualization.ColumnChart(document.getElementById('chart_div43'));
  google.visualization.events.addListener(chart, 'select', function() {
    var selection = chart.getSelection();
    if (selection.length > 0) {
      var rowIndex = selection[0].row;
      var hyperlink = data.getValue(rowIndex, 23);
      if (hyperlink) {
        window.location.href = hyperlink;
      }
    }
  });

  chart.draw(data, options_fullStacked);
}




google.charts.setOnLoadCallback(drawChart44);
function drawChart44() {
  var data = new google.visualization.DataTable();
  data.addColumn('string', 'Action');
  data.addColumn('number', 'Ontime');
  data.addColumn({type: 'string', role: 'annotation'});
  data.addColumn('number', 'Late');
  data.addColumn({type: 'string', role: 'annotation'});

  <?php
    $status = $this->Menu_model->get_status();
    foreach($status as $st){ 
    $stid = $st->id;
    $swtc = $this->Menu_model->get_taskolsna($uid,$stid,'2',$sd,$ed);
    foreach($swtc as $sw){ $url="https://stemapp.in/Menu"; ?>
      data.addRow(['<?=$st->name?> (<?=$sw->cont?>)',
      <?=$sw->a?>, '<?php if($sw->a == 0){echo "";}else{ echo $sw->a;}?>',
      <?=$sw->b?>, '<?php if($sw->b == 0){echo "";}else{ echo $sw->b;}?>']);
      <?php }}?>

  var options_fullStacked = {
    isStacked: 'percent',
    height: 600,
    legend: {position: 'top', maxLines: 3},
    vAxis: {
      minValue: 0,
      ticks: [0, 0.3, 0.6, 0.9, 1]
    },
    annotations: {
      alwaysOutside: false,
      textStyle: {
        fontSize: 10,
      },
    },
  };

  var chart = new google.visualization.ColumnChart(document.getElementById('chart_div44'));
  google.visualization.events.addListener(chart, 'select', function() {
    var selection = chart.getSelection();
    if (selection.length > 0) {
      var rowIndex = selection[0].row;
      var hyperlink = data.getValue(rowIndex, 23);
      if (hyperlink) {
        window.location.href = hyperlink;
      }
    }
  });

  chart.draw(data, options_fullStacked);
}




google.charts.setOnLoadCallback(drawChart45);
function drawChart45() {
  var data = new google.visualization.DataTable();
  data.addColumn('string', 'Action');
  data.addColumn('number', 'Ontime');
  data.addColumn({type: 'string', role: 'annotation'});
  data.addColumn('number', 'Late');
  data.addColumn({type: 'string', role: 'annotation'});

  <?php
    $status = $this->Menu_model->get_status();
    foreach($status as $st){ 
    $stid = $st->id;
    $swtc = $this->Menu_model->get_taskolsna($uid,$stid,'3',$sd,$ed);
    foreach($swtc as $sw){ $url="https://stemapp.in/Menu"; ?>
      data.addRow(['<?=$st->name?> (<?=$sw->cont?>)',
      <?=$sw->a?>, '<?php if($sw->a == 0){echo "";}else{ echo $sw->a;}?>',
      <?=$sw->b?>, '<?php if($sw->b == 0){echo "";}else{ echo $sw->b;}?>']);
      <?php }}?>

  var options_fullStacked = {
    isStacked: 'percent',
    height: 600,
    legend: {position: 'top', maxLines: 3},
    vAxis: {
      minValue: 0,
      ticks: [0, 0.3, 0.6, 0.9, 1]
    },
    annotations: {
      alwaysOutside: false,
      textStyle: {
        fontSize: 10,
      },
    },
  };

  var chart = new google.visualization.ColumnChart(document.getElementById('chart_div45'));
  google.visualization.events.addListener(chart, 'select', function() {
    var selection = chart.getSelection();
    if (selection.length > 0) {
      var rowIndex = selection[0].row;
      var hyperlink = data.getValue(rowIndex, 23);
      if (hyperlink) {
        window.location.href = hyperlink;
      }
    }
  });

  chart.draw(data, options_fullStacked);
}



google.charts.setOnLoadCallback(drawChart46);
function drawChart46() {
  var data = new google.visualization.DataTable();
  data.addColumn('string', 'Action');
  data.addColumn('number', 'Ontime');
  data.addColumn({type: 'string', role: 'annotation'});
  data.addColumn('number', 'Late');
  data.addColumn({type: 'string', role: 'annotation'});

  <?php
    $status = $this->Menu_model->get_status();
    foreach($status as $st){ 
    $stid = $st->id;
    $swtc = $this->Menu_model->get_taskolsna($uid,$stid,'4',$sd,$ed);
    foreach($swtc as $sw){ $url="https://stemapp.in/Menu"; ?>
      data.addRow(['<?=$st->name?> (<?=$sw->cont?>)',
      <?=$sw->a?>, '<?php if($sw->a == 0){echo "";}else{ echo $sw->a;}?>',
      <?=$sw->b?>, '<?php if($sw->b == 0){echo "";}else{ echo $sw->b;}?>']);
      <?php }}?>

  var options_fullStacked = {
    isStacked: 'percent',
    height: 600,
    legend: {position: 'top', maxLines: 3},
    vAxis: {
      minValue: 0,
      ticks: [0, 0.3, 0.6, 0.9, 1]
    },
    annotations: {
      alwaysOutside: false,
      textStyle: {
        fontSize: 10,
      },
    },
  };

  var chart = new google.visualization.ColumnChart(document.getElementById('chart_div46'));
  google.visualization.events.addListener(chart, 'select', function() {
    var selection = chart.getSelection();
    if (selection.length > 0) {
      var rowIndex = selection[0].row;
      var hyperlink = data.getValue(rowIndex, 23);
      if (hyperlink) {
        window.location.href = hyperlink;
      }
    }
  });

  chart.draw(data, options_fullStacked);
}



google.charts.setOnLoadCallback(drawChart47);
function drawChart47() {
  var data = new google.visualization.DataTable();
  data.addColumn('string', 'Action');
  data.addColumn('number', 'Ontime');
  data.addColumn({type: 'string', role: 'annotation'});
  data.addColumn('number', 'Late');
  data.addColumn({type: 'string', role: 'annotation'});

  <?php
    $status = $this->Menu_model->get_status();
    foreach($status as $st){ 
    $stid = $st->id;
    $swtc = $this->Menu_model->get_taskolsna($uid,$stid,'5',$sd,$ed);
    foreach($swtc as $sw){ $url="https://stemapp.in/Menu"; ?>
      data.addRow(['<?=$st->name?> (<?=$sw->cont?>)',
      <?=$sw->a?>, '<?php if($sw->a == 0){echo "";}else{ echo $sw->a;}?>',
      <?=$sw->b?>, '<?php if($sw->b == 0){echo "";}else{ echo $sw->b;}?>']);
      <?php }}?>

  var options_fullStacked = {
    isStacked: 'percent',
    height: 600,
    legend: {position: 'top', maxLines: 3},
    vAxis: {
      minValue: 0,
      ticks: [0, 0.3, 0.6, 0.9, 1]
    },
    annotations: {
      alwaysOutside: false,
      textStyle: {
        fontSize: 10,
      },
    },
  };

  var chart = new google.visualization.ColumnChart(document.getElementById('chart_div47'));
  google.visualization.events.addListener(chart, 'select', function() {
    var selection = chart.getSelection();
    if (selection.length > 0) {
      var rowIndex = selection[0].row;
      var hyperlink = data.getValue(rowIndex, 23);
      if (hyperlink) {
        window.location.href = hyperlink;
      }
    }
  });

  chart.draw(data, options_fullStacked);
}


google.charts.setOnLoadCallback(drawChart48);
function drawChart48() {
  var data = new google.visualization.DataTable();
  data.addColumn('string', 'Action');
  data.addColumn('number', 'Ontime');
  data.addColumn({type: 'string', role: 'annotation'});
  data.addColumn('number', 'Late');
  data.addColumn({type: 'string', role: 'annotation'});

  <?php
    $status = $this->Menu_model->get_status();
    foreach($status as $st){ 
    $stid = $st->id;
    $swtc = $this->Menu_model->get_taskolsna($uid,$stid,'6',$sd,$ed);
    foreach($swtc as $sw){ $url="https://stemapp.in/Menu"; ?>
      data.addRow(['<?=$st->name?> (<?=$sw->cont?>)',
      <?=$sw->a?>, '<?php if($sw->a == 0){echo "";}else{ echo $sw->a;}?>',
      <?=$sw->b?>, '<?php if($sw->b == 0){echo "";}else{ echo $sw->b;}?>']);
      <?php }}?>

  var options_fullStacked = {
    isStacked: 'percent',
    height: 600,
    legend: {position: 'top', maxLines: 3},
    vAxis: {
      minValue: 0,
      ticks: [0, 0.3, 0.6, 0.9, 1]
    },
    annotations: {
      alwaysOutside: false,
      textStyle: {
        fontSize: 10,
      },
    },
  };

  var chart = new google.visualization.ColumnChart(document.getElementById('chart_div48'));
  google.visualization.events.addListener(chart, 'select', function() {
    var selection = chart.getSelection();
    if (selection.length > 0) {
      var rowIndex = selection[0].row;
      var hyperlink = data.getValue(rowIndex, 23);
      if (hyperlink) {
        window.location.href = hyperlink;
      }
    }
  });

  chart.draw(data, options_fullStacked);
}



google.charts.setOnLoadCallback(drawChart49);
function drawChart49() {
  var data = new google.visualization.DataTable();
  data.addColumn('string', 'Action');
  data.addColumn('number', 'Ontime');
  data.addColumn({type: 'string', role: 'annotation'});
  data.addColumn('number', 'Late');
  data.addColumn({type: 'string', role: 'annotation'});

  <?php
    $status = $this->Menu_model->get_status();
    foreach($status as $st){ 
    $stid = $st->id;
    $swtc = $this->Menu_model->get_taskolsna($uid,$stid,'7',$sd,$ed);
    foreach($swtc as $sw){ $url="https://stemapp.in/Menu"; ?>
      data.addRow(['<?=$st->name?> (<?=$sw->cont?>)',
      <?=$sw->a?>, '<?php if($sw->a == 0){echo "";}else{ echo $sw->a;}?>',
      <?=$sw->b?>, '<?php if($sw->b == 0){echo "";}else{ echo $sw->b;}?>']);
      <?php }}?>

  var options_fullStacked = {
    isStacked: 'percent',
    height: 600,
    legend: {position: 'top', maxLines: 3},
    vAxis: {
      minValue: 0,
      ticks: [0, 0.3, 0.6, 0.9, 1]
    },
    annotations: {
      alwaysOutside: false,
      textStyle: {
        fontSize: 10,
      },
    },
  };

  var chart = new google.visualization.ColumnChart(document.getElementById('chart_div49'));
  google.visualization.events.addListener(chart, 'select', function() {
    var selection = chart.getSelection();
    if (selection.length > 0) {
      var rowIndex = selection[0].row;
      var hyperlink = data.getValue(rowIndex, 23);
      if (hyperlink) {
        window.location.href = hyperlink;
      }
    }
  });

  chart.draw(data, options_fullStacked);
}

        

  
  
  
</script>




<div class="row p-3">
                 
                 
                 
                 <div id="columnchart1" class="col-12 p-5"></div>  
                 
                 <div id="chart_div"  class="col-12 "></div>
                 <div id="chart_div2"  class="col-12 "></div>
                 <div id="chart_div4"  class="col-12 "></div>
                 <div id="chart_div3"  class="col-12 "></div><hr>
                 <div id="chart_div5"  class="col-12 "></div>
                 <div id="chart_div6"  class="col-12 "></div>
                 <lable>Other User Working on my Funnal AYPY</lable>
                 <div id="chart_div7"  class="col-12 "></div>
                 <lable>Other User Working on my Funnal AYPN</lable>
                 <div id="chart_div8"  class="col-12 "></div>
                 
                 <lable>Other User Action on my Funnal AYPY</lable>
                 <div id="chart_div9"  class="col-12 "></div>
                 
                 <lable>Other User Action on my Funnal AYPN</lable>
                 <div id="chart_div10"  class="col-12 "></div>
                 
                 <lable>Status Conversion Days</lable>
                 <div id="columnchart10" class="col-12"></div>
                 
                 
                 <lable>Status Conversion Task</lable>
                 <div id="columnchart11" class="col-12"></div>
                 
                 <lable>Successfull Conversion task status wise perform different user on my funnel</lable>
                 <div id="chart_div11"  class="col-12"></div>
                 
                 <lable>Task status wise perform different user on my funnel AYPN</lable>
                 <div id="chart_div12"  class="col-12"></div>
                 
                 
                 <lable>New</lable>
                 <div id="chart_div41"  class="col-12"></div>
                 
                 <lable>Call Task Ontime/Late</lable>
                 <div id="chart_div42"  class="col-12"></div>
                 
                 <lable>Research Task Ontime/Late</lable>
                 <div id="chart_div43"  class="col-12"></div>
                 
                 <lable>Email Task Ontime/Late</lable>
                 <div id="chart_div44"  class="col-12"></div>
                 
                 <lable>Schedule Meeting Task Ontime/Late</lable>
                 <div id="chart_div45"  class="col-12"></div>
                 
                 <lable>Bargin Meeting Task Ontime/Late</lable>
                 <div id="chart_div46"  class="col-12"></div>
                 
                 <lable>Whatsapp Activity Task Ontime/Late</lable>
                 <div id="chart_div47"  class="col-12"></div>
                 
                 <lable>Write MOM Task Ontime/Late</lable>
                 <div id="chart_div48"  class="col-12"></div>
                 
                 <lable>Proposal Task Ontime/Late</lable>
                 <div id="chart_div49"  class="col-12"></div>

            
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
                 if($patid){$patid = $this->Menu_model->get_partnerbyid($patid);$patid = $patid[0]->name;} else{$patid='';}
                 
                 if($patid){$sid = $dt->cstatus;$stid=$this->Menu_model->get_statusbyid($sid);$sid=$stid[0]->name;$color=$stid[0]->color;}
                 else{$sid='';}
                 
                 $tblc=$this->Menu_model->get_tblbyidwithremark($ciid);
                 $logs = sizeof($tblc);
                 
                 if($logs>0){$appoint = $tblc[0]->appointmentdatetime;
                     $nextaction = $tblc[0]->nextaction;
                     $remarks = $tblc[0]->remarks;}else{$appoint='';$nextaction='';$remarks='';}
                     
                 ?>
                 <div class="col-lg-3 col-md-6 col-sm p-3 <?=$color?> rounded border filter-item <?=$dt->cstatus?>">
                     <center><?=$i++?></center>
                     <a target="_blank" href="<?=base_url();?>Menu/CompanyTaskBox/<?=$cid?>">Current Status<br><b><?=$sid?></b><hr>
                     Company Name<br><b><?=$dt->compname?></b><hr>
                     Address<br><b><?=$dt->address?></b><hr>
                     City<br><b><?=$dt->city?></b><hr>
                     State<br><b><?=$dt->state?></b><hr>
                     Partner Type<br><b><?=$patid?></b><hr>
                     Focus Funnel<br><b><?=$dt->focus_funnel?></b><hr>
                     Upsell Client<br><b><?=$dt->upsell_client?></b><hr>
                     Key Client<br><b><?=$dt->keycompany?></b><hr>
                     Current Remark<br><b><?=$remarks?></b></a><hr>
                     Total Logs on Same Status<br><b><?=$stlogs?></b></a><hr>
                     Current Status of from whitch date<br><b><?=$ldscd[0]->updatedt?></b></a><hr>
                     Same Status from Current Time<br><b><?=$timediff?></b>
                     
                 </div>
                 <?php } ?>
              </div>
              
              <script>
                const filterItems = document.querySelectorAll('.filter-item');
                const filters = document.querySelector('.filters');
                filters.addEventListener('click', (event) => {
                    const filterValue = event.target.getAttribute('data-filter');
                    if (filterValue === 'all') {
                        filterItems.forEach(item => {
                            item.style.display = 'block';
                        });
                    } else {
                        filterItems.forEach(item => {
                            if (item.classList.contains(filterValue)) {
                                item.style.display = 'block';
                            } else {
                                item.style.display = 'none';
                            }
                        });
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
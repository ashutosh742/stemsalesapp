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
              <center><h5>Month Wise Activity Funnel Graph</h5></center><hr>

          
<div class="row">
<div id="chart_div41" style="width: 100%; height: 600px;"></div>
</div>



  <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
  <script type="text/javascript">
    google.charts.load("current", { packages: ['corechart'] });
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
                            $oldd = "";$newd="";
                            $gdata = $this->Menu_model->get_usertask($uid);
                            foreach ($gdata as $gd) { if($oldd==''){$oldd = $gd->updateddate;} $newd = $gd->updateddate;?>
                            
                            
                                        <div class="col-sm-12 col-md-4 col-lg-4 mb-4">
                                        <div class="card p-3 border rounded border-success hover-div d-flex flex-column align-items-stretch h-100">
                                            <div class="custom-border-card">
                                            <span class="custom-border-text"><h5>Time Diff Between to Task : <?=$this->Menu_model->timediff($oldd, $newd);?></h5></span>
                                            <div class="card-body">
                                            
                                            Action<br><b style="color:<?=$gd->aclr?>"><?=$gd->acname?></b><hr>
                                            Befour Status<br><b style="color:<?=$gd->bsclr?>"><?=$gd->bstatus?></b><hr>
                                            After Status<br><b style="color:<?=$gd->asclr?>"><?=$gd->astatus?></b><hr>
                                            Company Name<br><a href="<?=base_url();?>Menu/CompanyDetails/<?=$gd->cid?>"><b style="color:<?=$gd->pclr?>"><?=$gd->compname?><a></a><br>(<?=$gd->pname?>)</b><hr>
                                            Task By<br><b><?=$gd->uname?></b><hr>
                                            Task Plan<br><b><?=$this->Menu_model->get_dformat($gd->appointmentdatetime)?></b><hr>
                                            Task Inistaed<br><b><?=$this->Menu_model->get_dformat($gd->initiateddt)?></b><br>
                                            Time Diff : <?=$this->Menu_model->timediff($gd->appointmentdatetime,$gd->initiateddt);?><hr>
                                            Task Updated<br><b><?=$this->Menu_model->get_dformat($gd->updateddate)?></b><br>
                                            Time Diff : <?=$this->Menu_model->timediff($gd->initiateddt,$gd->updateddate);?><hr>
                                            Remark/MOM <br><b><?=$gd->remarks?><?=$gd->mom?></b><hr>
                                            <div class="rounded-circle bg-danger" style="position: absolute;
                                                bottom: -10px; left: 40%; transform: translateX(-50%); width: 20px; height: 20px;"></div>
                                            <div class="rounded-circle bg-danger" style="position: absolute;
                                                bottom: -10px; left: 60%; transform: translateX(-50%); width: 20px; height: 20px;"></div>
                                                
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            
                            
                                
                                
                            <?php $oldd = $gd->updateddate;} ?>
                    
                    
                        
                </div>
                <div id="list-view" style="display: none;">
                    	<div class="table-responsive" id="tbdata">   
                                <table id="example1" class="table table-striped table-bordered" cellspacing="0" width="100%">
                                    <thead>
                                        <tr>
                                            <th>S.No.</th>
                                            <th>Action</th>
                                            <th>Before Status</th>
                                            <th>After Status</th>
                                            <th>Company Name</th>
                                            <th>Partner Type</th>
                                            <th>Task By</th>
                                            <th>Task Plan</th>
                                            <th>Task Initiated</th>
                                            <th>Time Diff</th>
                                            <th>Task Updated</th>
                                            <th>Time Diff</th>
                                            <th>Remark</th>
                                            <th>MOM</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    <?php $i=1;
                            foreach ($gdata as $gd) {
                                ?>
                                
                                        <tr>
                                         <td><?=$i?></td>
                                         <td><b style="color:<?=$gd->aclr?>"><?=$gd->acname?></b></td>
                                         <td><b style="color:<?=$gd->bsclr?>"><?=$gd->bstatus?></b></td>
                                         <td><b style="color:<?=$gd->asclr?>"><?=$gd->astatus?></b></td>
                                         <td><a href="<?=base_url();?>Menu/CompanyDetails/<?=$gd->cid?>"><b style="color:<?=$gd->pclr?>"><?=$gd->compname?></b></a></td>
                                         <td><b style="color:<?=$gd->pclr?>"><?=$gd->pname?></b></td>
                                         <td><?=$gd->uname?></td>
                                         <td><?=$this->Menu_model->get_dformat($gd->appointmentdatetime)?></td>
                                         <td><?=$this->Menu_model->get_dformat($gd->initiateddt)?></td>
                                         <td><?=$this->Menu_model->timediff($gd->appointmentdatetime,$gd->initiateddt)?></td>
                                         <td><?=$this->Menu_model->get_dformat($gd->updateddate)?></td>
                                         <td><?=$this->Menu_model->timediff($gd->initiateddt,$gd->updateddate)?></td>
                                         <td><?=$gd->remarks?></td>
                                         <td><?=$gd->mom?></td>
                                     </tr>
                                     <?php $i++;} ?>
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
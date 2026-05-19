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
              <center><h5>Companies with same status till day</h5></center><hr>

          
<div class="row">
<div id="piechart3d3" style="width: 100%; "></div>
</div>



  <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
  <script type="text/javascript">
    google.charts.load("current", { packages: ['corechart'] });
    
    
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
          data.addColumn({type: 'string', role: 'annotationText'});
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
                  elseif ($object->opensday > 10 || $object->opensday <=20) { $countLessThan20++; }
                  elseif ($object->opensday > 20 || $object->opensday <=30) { $countLessThan30++; }
                  elseif ($object->opensday > 30) { $countMoreThan30++; }
                  else{}
                }
                ?>
                ['<?=$st->name?>', <?=$countLessThan10?>, '<?=$countLessThan10?>', <?=$countLessThan20?>, '<?=$countLessThan20?>', <?=$countLessThan30?>, '<?=$countLessThan30?>', <?=$countMoreThan30?>, '<?=$countMoreThan30?>', '<?=$stid?>'],
                <?php
              }
            }
            ?>
          ]);
          
          var options = {
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
        
          
        
        
      var chart = new google.visualization.ColumnChart(document.getElementById('piechart3d3'));
            google.visualization.events.addListener(chart, 'select', function () {
                var selectedItem = chart.getSelection()[0];
                if (selectedItem) {
                    var row = selectedItem.row;
                    var column = selectedItem.column;
                    var value = data.getValue(row, column);
                    var stid = data.getValue(row, 9);
                    var uuid = <?=$uid?>;
                    var code = 5;
                    var stid = stid+'A'+column;
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
            });

            chart.draw(data, options);
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
                         $mdata = $this->Menu_model->get_fannalbycode($uid,$sttid,$code);
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
                                     $mdata = $this->Menu_model->get_fannalbycode($uid,$sttid,$code);
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
                              elseif ($object->opensday > 10 || $object->opensday <=20) { $countLessThan20++; }
                              elseif ($object->opensday > 20  || $object->opensday <=30) { $countLessThan30++; }
                              elseif ($object->opensday > 30) { $countMoreThan30++; }
                              else{}
                            } ?>
                            
                            <?=$st->name?>
                            <b><a href="<?=base_url();?>Menu/FGraph5/<?=$st->id?>A1">0-10 (<?=$countLessThan10?>)</a> | 
                            <a href="<?=base_url();?>Menu/FGraph5/<?=$st->id?>A3">10-20 (<?=$countLessThan20?>)</a> | 
                            <a href="<?=base_url();?>Menu/FGraph5/<?=$st->id?>A5">20-30 (<?=$countLessThan30?>)</a> | 
                            <a href="<?=base_url();?>Menu/FGraph5/<?=$st->id?>A7">30+ (<?=$countMoreThan30?>)</a></b>
                            
                            <hr>
                            
                            
                            <?php } } ?>
                    
                    
                    
                    
                    
                      <b><a href="<?=base_url();?>Menu/FGraph5/0/">View All</a></b>
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
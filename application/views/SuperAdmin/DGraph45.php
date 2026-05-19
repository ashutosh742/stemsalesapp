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
              <center><h5>Next Day Plan Action/Status/Hour Wise</h5></center><hr>
              
              

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.1/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.0.1/dist/chart.umd.min.js"></script>

<?php $colors = array('red','blue','green','yellow','purple','orange','pink','brown','cyan','magenta','teal','lime','violet','indigo','gray');?>


<div style="width: 80%; margin: auto;">
   <canvas id="combinedChartID1"></canvas>
</div>   


    <script>
    <?php 
    
    $currentDate = date('Y-m-d');
    $sd = date('Y-m-d', strtotime($currentDate . ' +1 day'));
    
    $ed=date('Y-m-d');?>
    
        var combinedData1 = {
            labels: [<?php $action = $this->Menu_model->get_action(); $i=1; foreach($action as $ac){ $acid = $ac->id;?> '<?=$ac->name?>', <?php } ?>],
            datasets: [
                {
                    label: 'Action',
                    backgroundColor: ['red','red','red','red','red','red','red','red','red','red','red','red','red','red'],
                    data: [
                        <?php 
                        $action = $this->Menu_model->get_action(); $i=1; foreach($action as $ac){ $acid = $ac->id;
                        $task = $this->Menu_model->get_tasktypenextdplan($uid,$sd,$ed,$acid);
                        foreach($task as $ts){
                        ?>
                        <?=$ts->cont?>,
                        <?php }} ?>],
                        stack: 'Stack 0'
                },
                
                <?php  $status = $this->Menu_model->get_status(); $i=1; foreach($status as $st){ $stid = $st->id;?>
                {
                    label: '<?=$st->name?>',
                    backgroundColor: ['<?=$colors[$i]?>'],
                    data: [
                        <?php 
                        $action = $this->Menu_model->get_action();  foreach($action as $ac){ $acid = $ac->id;
                        $task = $this->Menu_model->get_tasktypenextdplanbys($uid,$sd,$ed,$acid,$stid);
                        foreach($task as $ts){
                        ?>
                        <?=$ts->cont?>,
                        <?php }} ?>],
                        stack: 'Stack 1'
                        
                        <?php $i++; ?>
                },
                
                <?php } ?>
                
                
                {
                    label: 'Mint',
                    backgroundColor: ['blue','blue','blue','blue','blue','blue','blue','blue','blue','blue','blue','blue','blue','blue'],
                    data: [
                        <?php 
                        $action = $this->Menu_model->get_action(); $i=1; foreach($action as $ac){ $acid = $ac->id;
                        $task = $this->Menu_model->get_tasktypenextdplanwt($uid,$sd,$ed,$acid);
                        foreach($task as $ts){
                        ?>
                        <?=$ts->time?>,
                        <?php }} ?>],
                        stack: 'Stack 2'
                },
                
                
                
            ]
        };

        var combinedCtx = document.getElementById("combinedChartID1").getContext('2d');
        var combinedChart = new Chart(combinedCtx, {
            type: 'bar',
            data: combinedData1,
            options: {
                plugins: {
                    title: {
                        display: true,
                        text: 'Completed Task Vs Review'
                    },
                },
                scales: {
                    x: {
                        stacked: true,
                    },
                    y: {
                        beginAtZero: true,
                    }
                }
            }
        });
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
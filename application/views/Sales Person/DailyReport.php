<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>STEM APP | WebAPP</title>

  <!-- Google Font: Source Sans Pro -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.0/css/all.min.css">
  <!-- Font Awesome -->
  <link rel="stylesheet" href="<?=base_url();?>assets/css/all.min.css">
  <!-- Ionicons -->
  <link rel="stylesheet" href="https://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css">
  <!-- Tempusdominus Bootstrap 4 -->
  <link rel="stylesheet" href="<?=base_url();?>assets/css/tempusdominus-bootstrap-4.min.css">
  <!-- iCheck -->
  <link rel="stylesheet" href="<?=base_url();?>assets/css/icheck-bootstrap.min.css">
  <!-- JQVMap -->
  <link rel="stylesheet" href="<?=base_url();?>assets/css/jqvmap.min.css">
  <!-- Theme style -->
  <link rel="stylesheet" href="<?=base_url();?>assets/css/adminlte.min.css">
  <!-- overlayScrollbars -->
  <link rel="stylesheet" href="<?=base_url();?>assets/css/OverlayScrollbars.min.css">
  <!-- Daterange picker -->
  <link rel="stylesheet" href="<?=base_url();?>assets/css/daterangepicker.css">
  <!-- summernote -->
  <link rel="stylesheet" href="<?=base_url();?>assets/css/summernote-bs4.min.css">
  <link rel="stylesheet" href="<?=base_url();?>assets/css/mystyle.css">
   <!-- DataTables -->
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
  <!-- Navbar -->
  <?php require('nav.php');?>
  <!-- /.navbar -->
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
    <div id="maindata" class="collapse" aria-labelledby="maindata" data-parent="#accordion">
    <!-- Main content -->
    <section class="content">
      <div class="container-fluid">
              
        </div><!-- /.row (main row) -->
      </div><!-- /.container-fluid -->
    </section>
    
<script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
<script type="text/javascript">
  google.charts.load('current', {'packages':['corechart']});
  google.charts.setOnLoadCallback(drawChart);

  function drawChart() {

    var data = google.visualization.arrayToDataTable([
      ['Funnal', 'Status'],
      ['Open - <?=$mbdc[0]->b?>', <?=$mbdc[0]->b?>],
    ['Open [RPEM] - <?=$mbdc[0]->i?>', <?=$mbdc[0]->i?>],
    ['Reachout - <?=$mbdc[0]->c?>', <?=$mbdc[0]->c?>],
    ['Tentative - <?=$mbdc[0]->d?>', <?=$mbdc[0]->d?>],
    ['Will-Do-Later - <?=$mbdc[0]->e?>', <?=$mbdc[0]->e?>],
    ['Not-Interest - <?=$mbdc[0]->f?>', <?=$mbdc[0]->f?>],
    ['TTD-Reachout - <?=$mbdc[0]->k?>', <?=$mbdc[0]->k?>],
    ['WNO-Reachout - <?=$mbdc[0]->l?>', <?=$mbdc[0]->l?>],
    ['Positive - <?=$mbdc[0]->g?>', <?=$mbdc[0]->g?>],
    ['Very Positive - <?=$mbdc[0]->j?>', <?=$mbdc[0]->j?>],
    ['Closure - <?=$mbdc[0]->h?>', <?=$mbdc[0]->h?>],
    ['Focus Funnel - <?=$mbdc[0]->m?>', <?=$mbdc[0]->m?>],
    ['Upsell Client - <?=$mbdc[0]->n?>', <?=$mbdc[0]->n?>],
    ['EX-BD Tf - <?=$mbdc[0]->o?>', <?=$mbdc[0]->o?>]
    ]);

    var options = {
      title: 'Total Funnal Detail (<?=$mbdc[0]->a?>)'
    };

    var chart = new google.visualization.PieChart(document.getElementById('funnal'));

    chart.draw(data, options);
  }
  
  google.charts.load('current', {'packages':['corechart']});
  google.charts.setOnLoadCallback(Fun);
  function Fun() {

    var data = google.visualization.arrayToDataTable([
        ['Task', 'Count'],
        <?php foreach($ttd as $task){?>
        ['Assign/Planned - <?=$task->a?>', <?=$task->a?>],
        ['Pending - <?=$task->b?>', <?=$task->b?>],
        ['Completed - <?=$task->c?>', <?=$task->c?>],
        ['Call A/P- <?=$task->d?>', <?=$task->d?>],
        ['Email A/P- <?=$task->f?>', <?=$task->f?>],
        ['Meeting A/P- <?=$task->h?>', <?=$task->h?>],
        ['Barg in A/P- <?=$task->j?>', <?=$task->j?>],
        ['Whatsapp A/P- <?=$task->l?>', <?=$task->l?>],
        ['MOM A/P- <?=$task->n?>', <?=$task->n?>],
        ['Proposal A/P- <?=$task->p?>', <?=$task->r?>],
        ['Action Yes - <?=$task->s?>', <?=$task->s?>],
        ['Action No - <?=$task->s?>', <?=$task->s?>],
        ['Purpose Yes - <?=$task->t?>', <?=$task->t?>],
        ['Purpose No - <?=$task->u?>', <?=$task->u?>]
        <?php } ?>
    ]);
    
    

    var options = {
      title: 'Total Task Detail (<?=$upt[0]->a?>)'
    };

    var chart = new google.visualization.PieChart(document.getElementById('fun'));

    chart.draw(data, options);
  }
  
  
  
  
  google.charts.load("current", {packages:['corechart']});
    google.charts.setOnLoadCallback(taskh);
    function taskh() {
      var data = google.visualization.arrayToDataTable([
        ['Time', 'Call', 'Email', 'Whatsapp', 'Meeting',
         'MOM', 'Bargin', { role: 'annotation' } ],
        <?php $ted = $this->Menu_model->get_ttbytimedc($uid,'2022-12-02','09:00:00','11:00:00'); ?>
        ['9am-11am', <?=$ted[0]->a?>, <?=$ted[0]->b?>, <?=$ted[0]->e?>, <?=$ted[0]->c?>, <?=$ted[0]->f?>, <?=$ted[0]->g?>, '<?=$ted[0]->d?>'],
        <?php $ted = $this->Menu_model->get_ttbytimedc($uid,'2022-12-02','09:00:00','11:00:00'); ?>
        ['11am-1pm', <?=$ted[0]->a?>, <?=$ted[0]->b?>, <?=$ted[0]->e?>, <?=$ted[0]->c?>, <?=$ted[0]->f?>, <?=$ted[0]->g?>, '<?=$ted[0]->d?>'],
        <?php $ted = $this->Menu_model->get_ttbytimedc($uid,'2022-12-02','09:00:00','11:00:00'); ?>
        ['1pm-3pm', <?=$ted[0]->a?>, <?=$ted[0]->b?>, <?=$ted[0]->e?>, <?=$ted[0]->c?>, <?=$ted[0]->f?>, <?=$ted[0]->g?>, '<?=$ted[0]->d?>'],
        <?php $ted = $this->Menu_model->get_ttbytimedc($uid,'2022-12-02','09:00:00','11:00:00'); ?>
        ['3pm-5pm', <?=$ted[0]->a?>, <?=$ted[0]->b?>, <?=$ted[0]->e?>, <?=$ted[0]->c?>, <?=$ted[0]->f?>, <?=$ted[0]->g?>, '<?=$ted[0]->d?>'],
        <?php $ted = $this->Menu_model->get_ttbytimedc($uid,'2022-12-02','09:00:00','11:00:00'); ?>
        ['5pm-7pm', <?=$ted[0]->a?>, <?=$ted[0]->b?>, <?=$ted[0]->e?>, <?=$ted[0]->c?>, <?=$ted[0]->f?>, <?=$ted[0]->g?>, '<?=$ted[0]->d?>'],
        <?php $ted = $this->Menu_model->get_ttbytimedc($uid,'2022-12-02','09:00:00','11:00:00'); ?>
        ['7pm-9pm', <?=$ted[0]->a?>, <?=$ted[0]->b?>, <?=$ted[0]->e?>, <?=$ted[0]->c?>, <?=$ted[0]->f?>, <?=$ted[0]->g?>, '<?=$ted[0]->d?>']
      ]);

      var view = new google.visualization.DataView(data);
      var options_stacked = {
          isStacked: true,
          height: 300,
          legend: {position: 'top', maxLines: 3},
          vAxis: {minValue: 0}
        };

      var options = {
        title: "Task Details on Time Based",
        legend: { position: 'top', maxLines: 3 },
        bar: { groupWidth: '75%' },
        isStacked: true,
      };
      var chart = new google.visualization.ColumnChart(document.getElementById("taskh"));
      chart.draw(view, options);
  }
  
  
  
  
</script>
    
    <div class="row">
        <div class="col-lg-5 col-sm">
            <div id="funnal" style="width: 100%; height: 400px;" class="m-3 p-3"></div>
        </div>
        <div class="col-lg-5 col-sm">
            <div id="fun" style="width: 100%; height: 400px;" class="m-3 p-3"></div>
        </div>
    </div>
    <div class="row">
        <div class="col-lg-5 col-sm">
            <div id="taskh" style="width: 100%; height: 400px;" class="m-3 p-3"></div>
        </div>
    </div>
    
    <div class="card m-3 p-3">
        <center><h5>Total Funnel </h5></center>
        <div class="table-responsive">
         <div class="table-responsive">  
    <table class="table">
        
        <tr>
            <td>Total Companies</td>
            <td>Open</td>
            <td>Open [RPEM]</td>
            <td>Reachout</td>
            <td>Tentative</td>
            <td>Will-Do-Later</td>
            <td>Not-Interest</td>
            <td>TTD-Reachout</td>
            <td>WNO-Reachout</td>
            <td>Positive</td>
            <td>Very Positive</td>
            <td>Closure</td>
            <td>Focus Funnel</td>
            <td>Upsell Client</td>
            <td>EX-BD Tf</td>
        </tr>
        <tr>
            <?php foreach($mbdc as $mc){?>
            <td><?=$mc->a?></td>
            <td><?=$mc->b?></td>
            <td><?=$mc->i?></td>
            <td><?=$mc->c?></td>
            <td><?=$mc->d?></td>
            <td><?=$mc->e?></td>
            <td><?=$mc->f?></td>
            <td><?=$mc->k?></td>
            <td><?=$mc->l?></td>
            <td><?=$mc->g?></td>
            <td><?=$mc->j?></td>
            <td><?=$mc->h?></td>
            <td><?=$mc->m?></td>
            <td><?=$mc->n?></td>
            <td><?=$mc->o?></td>
            <?php } ?>
        </tr>
    </table></div>
    </div></div>
    
    
    
        <div class="card m-3 p-3">
        <div class="table-responsive">
         <div class="table-responsive">  
    <table class="table">
        <tr>
            <td>Pending Task to be Schedule</td>
            <td>Total Task Assign/Planned</td>
            <td>Total Task Pending</td>
            <td>Total Task Completed</td>
            <td>Call Assign/Planned</td>
            <td>Email Assign/Planned</td>
            <td>Meeting Assign/Planned</td>
            <td>Barg in Assign/Planned</td>
            <td>Whatsapp Assign/Planned</td>
            <td>MOM Assign/Planned</td>
            <td>Proposal Assign/Planned</td>
            <td>Action Taken Yes</td>
            <td>Action Taken No</td>
            <td>Purpose Achieved Yes</td>
            <td>Purpose Achieved No</td>
        </tr>
        <tr>
            <?php foreach($ttd as $tttd){?>
            <td><?=$upt[0]->a?></td>
            <td><?=$tttd->a?></td>
            <td><?=$tttd->b?></td>
            <td><?=$tttd->c?></td>
            <td><?=$tttd->d?></td>
            <td><?=$tttd->f?></td>
            <td><?=$tttd->h?></td>
            <td><?=$tttd->j?></td>
            <td><?=$tttd->l?></td>
            <td><?=$tttd->n?></td>
            <td><?=$tttd->p?></td>
            <td><?=$tttd->r?></td>
            <td><?=$tttd->s?></td>
            <td><?=$tttd->t?></td>
            <td><?=$tttd->u?></td>
            <?php } ?>
        </tr>
    </table></div>
    </div></div>
    
    <div class="card m-3 p-3">
        <table class="table">
      <tr>
          <th>Day Started @</th>
          <th>Day Close @</th>
          <th>Day wffo</th>
      </tr>
      <tr>
          <?php foreach ($daym as $dm){?>
          <th><?=$dm->ustart?></th>
          <th><?=$dm->uclose?></th>
          <th><?=$dm->wffo?></th>
          <th></th>
          <?php } ?>
      </tr>
    </table></div>
    <div class="card m-3 p-3">
        <table class="table">
        <tr>
            <th>Type</th>
            <th>Call</th>
            <th>Email</th>
            <th>Meeting</th>
            <th>Barg in Meeting</th>
            <th>Whatsapp</th>
            <th>MOM Writing</th>
            <th>Proposal Writing</th>
            <th>Total</th>
        </tr>
        <tr>
            <?php $t=0;$ta=0;foreach($ttd as $tda){?>
            <th>Planned</th>
            <th><?=$ta=$tda->d*15;$t+=$ta;?>M</th>
            <th><?=$ta=$tda->f*10;$t+=$ta;?>M</th>
            <th><?=$ta=$tda->h*30;$t+=$ta;?>M</th>
            <th><?=$ta=$tda->j*30;$t+=$ta;?>M</th>
            <th><?=$ta=$tda->l*5;$t+=$ta;?>M</th>
            <th><?=$ta=$tda->n*10;$t+=$ta;?>M</th>
            <th><?=$ta=$tda->p*15;$t+=$ta;?>M</th>
            <th><?=$t?>M</th>
            <?php } ?>
        </tr>
        <tr>
            <?php $tb=0;$tc=0;foreach($ttd as $tdb){?>
            <th>Executed</th>
            <th><?=$tc=($tdb->d-$tdb->e)*15;$tb+=$tc;?>M</th>
            <th><?=$tc=($tdb->f-$tdb->g)*10;$tb+=$tc;?>M</th>
            <th><?=$tc=($tdb->h-$tdb->i)*30;$tb+=$tc;?>M</th>
            <th><?=$tc=($tdb->j-$tdb->k)*30;$tb+=$tc;?>M</th>
            <th><?=$tc=($tdb->l-$tdb->m)*5;$tb+=$tc;?>M</th>
            <th><?=$tc=($tdb->n-$tdb->o)*10;$tb+=$tc;?>M</th>
            <th><?=$tc=($tdb->p-$tdb->q)*15;$tb+=$tc;?>M</th>
            <th><?=$tb?>M</th>
            <?php } ?>
        </tr>
    </table></div>
    <?php 
      $tdate = date('Y-m-d');
      $or=0;$ort=0;$rr=0;$rt=0;$tp=0;$pvp=0;$vph=0;$other=0;
      foreach($sc as $m){
        $lid = $m->lastCFID;
        $csid = $m->status_id;
        $ltbl = $this->Menu_model->get_tbldata($lid);
        $lsid = $ltbl[0]->status_id;
        if($lsid!=$csid){
        if($lsid==1 && $csid ==8){$or++;}
        elseif($lsid==8 && $csid ==2){$ort++;}
        elseif($lsid==2 && $csid ==3){$rr++;}
        elseif($lsid==3 && $csid ==6){$rt++;}
        elseif($lsid==6 && $csid ==9){$tp++;}
        elseif($lsid==9 && $csid ==7){$pvp++;}
        elseif($lsid==7 && $csid ==12){$vph++;}
        else{$other++;}
    }}?>
    <div class="card m-3 p-3">
    <table class="table">
        <tr>
            <td>Open To Open [RPEM]</td>
            <td>Open [RPEM] to Reachout</td>
            <td>Reachout to Tentative</td>
            <td>Tentative to Positive</td>
            <td>Positive To Very Positive</td>
            <td>Positive to Closure</td>
            <td>Very Positive To Closure</td>
            <td>Others</td>
        </tr>
        <tr>
            <td><?=$or?></td>
            <td><?=$ort?></td>
            <td><?=$rr?></td>
            <td><?=$rt?></td>
            <td><?=$tp?></td>
            <td><?=$pvp?></td>
            <td><?=$vph?></td>
            <td><?=$other?></td>
        </tr>
    </table></div>
    <div class="card m-3 p-3">
        <div class="table-responsive">
         <div class="table-responsive">  
    <table class="table">
        <tr>
            <td>Pending Task to be Schedule</td>
            <td>Total Task Assign/Planned</td>
            <td>Total Task Pending</td>
            <td>Total Task Completed</td>
            <td>Call Assign/Planned</td>
            <td>Email Assign/Planned</td>
            <td>Meeting Assign/Planned</td>
            <td>Barg in Assign/Planned</td>
            <td>Whatsapp Assign/Planned</td>
            <td>MOM Assign/Planned</td>
            <td>Proposal Assign/Planned</td>
            <td>Action Taken Yes</td>
            <td>Action Taken No</td>
            <td>Purpose Achieved Yes</td>
            <td>Purpose Achieved No</td>
        </tr>
        <tr>
            <?php foreach($ttd as $tttd){?>
            <td><?=$upt[0]->a?></td>
            <td><?=$tttd->a?></td>
            <td><?=$tttd->b?></td>
            <td><?=$tttd->c?></td>
            <td><?=$tttd->d?></td>
            <td><?=$tttd->f?></td>
            <td><?=$tttd->h?></td>
            <td><?=$tttd->j?></td>
            <td><?=$tttd->l?></td>
            <td><?=$tttd->n?></td>
            <td><?=$tttd->p?></td>
            <td><?=$tttd->r?></td>
            <td><?=$tttd->s?></td>
            <td><?=$tttd->t?></td>
            <td><?=$tttd->u?></td>
            <?php } ?>
        </tr>
    </table></div>
    </div></div>
    
    
    <div class="card m-3 p-3">
        <center><h5>Today's Task Against Status</h5></center>
        <div class="table-responsive">
         <div class="table-responsive">  
    <table class="table">
        <tr>
            <td>Open</td>
            <td>Open [RPEM]</td>
            <td>Reachout</td>
            <td>Tentative</td>
            <td>Will-Do-Later</td>
            <td>Not-Interest</td>
            <td>Positive</td>
            <td>Very Positive</td>
            <td>Closure</td>
        </tr>
        <tr>
            <?php 
                  $open=0;
                  $oprpm=0;
                  $Reachout=0;
                  $Tentative=0;
                  $Will=0;
                  $Positive=0;
                  $vPositive=0;
                  $Closure=0;
                  $NotInterested=0;
                  foreach($tsww as $tw){
                      $ltid = $tw->lastCFID;
                      $ltask = $this->Menu_model->get_tbldata($ltid);
                      if($ltask){
                      if($ltask[0]->status_id==1){$open++;}
                      if($ltask[0]->status_id==8){$oprpm++;}
                      if($ltask[0]->status_id==2){$Reachout++;}
                      if($ltask[0]->status_id==3){$Tentative++;}
                      if($ltask[0]->status_id==4){$Will++;}
                      if($ltask[0]->status_id==5){$NotInterested++;}
                      if($ltask[0]->status_id==6){$Positive++;}
                      if($ltask[0]->status_id==7){$Closure++;}
                      if($ltask[0]->status_id==9){$vPositive++;}
                  }} ?>
                <td><?=$open?></td>
                <td><?=$oprpm?></td>
                <td><?=$Reachout?></td>
                <td><?=$Tentative?></td>
                <td><?=$Will?></td>
                <td><?=$NotInterested?></td>
                <td><?=$Positive?></td>
                <td><?=$vPositive?></td>
                <td><?=$Closure?></td></td>
        </tr>
    </table></div>
    </div></div>
    
    <div class="card m-3 p-3">
        <center><h5>Meeting</h5></center><hr>
        <div class="table-responsive">
         <div class="table-responsive">  
    <table class="table">
        
        <tr>
            <td>Total Non RP Meeting</td>
            <td>Total RP Meeting</td>
            <td>Total RP Priority</td>
            <td>Total RP Not Priority</td>
            <td>Total PST Assign</td>
            <td>Total PST Assign Pending</td>
            <td>RP Priority</td>
        </tr>
        <tr>
            <td>0</td>
            <td>0</td>
            <td>0</td>
            <td>0</td>
            <td>0</td>
            <td>0</td>
            <td>0</td>
        </tr>
    </table></div>
    </div></div>
    
    
    <div class="card m-3 p-3">
        <center><h5>Meeting</h5></center><hr>
        <div class="table-responsive">
         <div class="table-responsive">  
    <table class="table">
        
        <tr>
            <td>Total Non RP Meeting</td>
            <td>Total RP Meeting</td>
            <td>Total RP Priority</td>
            <td>Total RP Not Priority</td>
            <td>Total PST Assign</td>
            <td>Total PST Assign Pending</td>
            <td>RP Priority</td>
        </tr>
        <tr>
            <td>0</td>
            <td>0</td>
            <td>0</td>
            <td>0</td>
            <td>0</td>
            <td>0</td>
            <td>0</td>
        </tr>
    </table></div>
    </div></div>
    
    <!-- /.content -->
  </div></div>
  <!-- /.content-wrapper -->
  <footer class="main-footer">
    <strong>Copyright &copy; 2021-2022 <a href="<?=base_url();?>">Stemlearning</a>.</strong>
    All rights reserved.
    <div class="float-right d-none d-sm-inline-block">
      <b>Version</b> 1.0
    </div>
  </footer>

  <!-- Control Sidebar -->
  <aside class="control-sidebar control-sidebar-dark">
    <!-- Control sidebar content goes here -->
  </aside>
  <!-- /.control-sidebar -->
</div>
<!-- ./wrapper -->





<!-- jQuery -->
<script src="<?=base_url();?>assets/js/jquery.min.js"></script>
<!-- jQuery UI 1.11.4 -->
<script src="<?=base_url();?>assets/js/jquery-ui.min.js"></script>
<!-- Resolve conflict in jQuery UI tooltip with Bootstrap tooltip -->
<script>
  $.widget.bridge('uibutton', $.ui.button)
</script>
<!-- Bootstrap 4 -->
<script src="<?=base_url();?>assets/js/bootstrap.bundle.min.js"></script>
<!-- ChartJS -->
<script src="<?=base_url();?>assets/js/Chart.min.js"></script>
<!-- Sparkline -->
<script src="<?=base_url();?>assets/js/sparkline.js"></script>
<!-- JQVMap -->
<script src="<?=base_url();?>assets/js/jquery.vmap.min.js"></script>
<script src="<?=base_url();?>assets/js/jquery.vmap.usa.js"></script>
<!-- jQuery Knob Chart -->
<script src="plugins/jquery-knob/jquery.knob.min.js"></script>
<!-- daterangepicker -->
<script src="<?=base_url();?>assets/js/moment.min.js"></script>
<script src="<?=base_url();?>assets/js/daterangepicker.js"></script>
<!-- Tempusdominus Bootstrap 4 -->
<script src="<?=base_url();?>assets/js/tempusdominus-bootstrap-4.min.js"></script>
<!-- Summernote -->
<script src="<?=base_url();?>assets/js/summernote-bs4.min.js"></script>
<!-- overlayScrollbars -->
<script src="<?=base_url();?>assets/js/jquery.overlayScrollbars.min.js"></script>
<!-- DataTables  & Plugins -->
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
<!-- AdminLTE App -->
<script src="<?=base_url();?>assets/js/adminlte.js"></script>

<!-- AdminLTE dashboard demo (This is only for demo purposes) -->
<script src="<?=base_url();?>assets/js/dashboard.js"></script>

<script>
    $("#example1").DataTable({
      "responsive": false, "lengthChange": false, "autoWidth": false
    }).buttons().container().appendTo('#example1_wrapper .col-md-6:eq(0)');
    
    $("#example2").DataTable({
      "responsive": false, "lengthChange": false, "autoWidth": false
    }).buttons().container().appendTo('#example1_wrapper .col-md-6:eq(0)');
</script>


</body>
</html>

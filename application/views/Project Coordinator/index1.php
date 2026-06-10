<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>STEM APP | WebAPP</title>
<script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script> 
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

  <!-- Preloader -->
  

  <!-- Navbar -->
  <?php require('nav.php');?>
  <?php include 'addpop.php';?>
  <?php include 'popupbox.php';?>
  <!-- /.navbar -->


<?php
 
$dataPoints1 = array(
	array("label"=> "2010", "y"=> 36.12),
	array("label"=> "2011", "y"=> 34.87),
	array("label"=> "2012", "y"=> 40.30),
	array("label"=> "2013", "y"=> 35.30),
	array("label"=> "2014", "y"=> 39.50),
	array("label"=> "2015", "y"=> 50.82),
	array("label"=> "2016", "y"=> 74.70)
);
$dataPoints2 = array(
	array("label"=> "2010", "y"=> 64.61),
	array("label"=> "2011", "y"=> 70.55),
	array("label"=> "2012", "y"=> 72.50),
	array("label"=> "2013", "y"=> 81.30),
	array("label"=> "2014", "y"=> 63.60),
	array("label"=> "2015", "y"=> 69.38),
	array("label"=> "2016", "y"=> 98.70)
);
	
?>
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
<button class="btn btn-link" data-toggle="collapse" data-target="#maindata" aria-expanded="true" aria-controls="maindata">
      <i class="fas fa-bars"></i> Dashboard Data Analysis
    </button>

    <div id="maindata" class="collapse" aria-labelledby="maindata" data-parent="#accordion">
    <!-- Main content -->
    <section class="content">
      <div class="container-fluid">
        <!-- Small boxes (Stat box) -->
        <div class="row">
          <div class="col-12">
              <form action="Dashboard1" method="POST">
                  <input type="date" name="tdate"><input type="submit">
              </form>
                <section class="content">
                  <div class="container-fluid">
                    <div class=" card mt-3 row">
                        <div class="table-responsive">
                            <div class="table-responsive">
                                <table id="" class="table table-striped table-bordered" cellspacing="0" width="100%">
                            <tr>
                                <th>Type</th>
                                <th>Call (5M)</th>
                                <th>Email (10M)</th>
                                <th>Meeting (30M)</th>
                                <th>Barg in Meeting (30M)</th>
                                <th>Whatsapp (5M)</th>
                                <th>MOM Writing (10M)</th>
                                <th>Proposal Writing (15M)</th>
                                <th>Total (115M)</th>
                            </tr>
                            <tr>
                                <?php $t=0;$ta=0;foreach($ttd as $tda){?>
                                <th>Planned</th>
                                <th><?=$ta=$tda->d*5;$t+=$ta;?>M (<?=$tda->d?>)</th>
                                <th><?=$ta=$tda->f*10;$t+=$ta;?>M (<?=$tda->f?>)</th>
                                <th><?=$ta=$tda->h*30;$t+=$ta;?>M (<?=$tda->h?>)</th>
                                <th><?=$ta=$tda->j*30;$t+=$ta;?>M (<?=$tda->j?>)</th>
                                <th><?=$ta=$tda->l*5;$t+=$ta;?>M (<?=$tda->l?>)</th>
                                <th><?=$ta=$tda->n*10;$t+=$ta;?>M (<?=$tda->n?>)</th>
                                <th><?=$ta=$tda->p*15;$t+=$ta;?>M (<?=$tda->p?>)</th>
                                <th><?=$t?>M (<?=$tda->d+$tda->f+$tda->h+$tda->j+$tda->l+$tda->n+$tda->p?>)</th>
                                <?php } ?>
                            </tr>
                            <tr>
                                <?php $tb=0;$tc=0;foreach($ttd as $tdb){?>
                                <th>Executed</th>
                                <th><?=$tc=($tdb->d-$tdb->e)*5;$tb+=$tc;?>M (<?=$tda->e?>)</th>
                                <th><?=$tc=($tdb->f-$tdb->g)*5;$tb+=$tc;?>M (<?=$tda->g?>)</th>
                                <th><?=$tc=($tdb->h-$tdb->i)*30;$tb+=$tc;?>M (<?=$tda->i?>)</th>
                                <th><?=$tc=($tdb->j-$tdb->k)*30;$tb+=$tc;?>M (<?=$tda->k?>)</th>
                                <th><?=$tc=($tdb->l-$tdb->m)*5;$tb+=$tc;?>M (<?=$tda->m?>)</th>
                                <th><?=$tc=($tdb->n-$tdb->o)*10;$tb+=$tc;?>M (<?=$tda->o?>)</th>
                                <th><?=$tc=($tdb->p-$tdb->q)*15;$tb+=$tc;?>M (<?=$tda->q?>)</th>
                                <th><?=$tb?>M (<?=$tda->e+$tda->g+$tda->i+$tda->k+$tda->m+$tda->o+$tda->q?>)</th>
                                <?php } ?>
                            </tr>
                        </table>
                    </div>
                  </div>
                </section>
              
          
          
        <div class="row">
          <!-- ./col -->
          <div class="col-lg-3 col-md-6 col-sm-12">
            <!-- small box -->
            <div class="small-box bg-light text-secondary">
                <?php
                  $sca = $this->Menu_model->final_scon1($uid,$tdate,$tdate,0);
                  ?>
              <div class="inner">
                  <center><h5>Today's Team Status Conversion</h5></center><hr>
                  <?php  foreach($sca as $scid){
                        $string = $scid->scname;
                        $parts = explode(" -to- ", $string);
                        $firstS = $parts[0];
                        $secondS = $parts[1];
                        $firstS = $this->Menu_model->get_statusbyname($firstS);
                        $fsid = $firstS[0]->id;
                        $secondS = $this->Menu_model->get_statusbyname($secondS);
                        $ssid = $secondS[0]->id; ?>
                        <a href="FinalSConversion/<?=$uid?>/<?=$tdate?>/<?=$tdate?>/<?=$fsid?>/<?=$ssid?>">
                           <?=$string?> - <b><?=$this->Menu_model->final_scon2($uid,$tdate,$tdate,0,$fsid,$ssid);?></b><hr>
                        </a>
                    <?php } ?>
                </div>
              <div class="icon">
                <i class="ion ion-person-add"></i>
              </div>
              <a href="SConversion" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
            </div>
          </div>
          <!-- ./col -->
          <div class="col-lg-3 col-md-6 col-sm-12">
            <!-- small box -->
            <div class="small-box bg-light text-secondary"> 
              <div class="inner">
                <center><h5>Today's Task Detail</h5></center><hr>
                <?php foreach($ttd as $tttd){?>
                    <p><a href="ATaskDetail/4/<?=$uid?>/1/<?=$tdate?>/<?=$tdate?>/1">Total Task Assign/Planned - <b><?=$tttd->a?></b></p></a><hr>
                    <p><a href="ATaskDetail/5/<?=$uid?>/1/<?=$tdate?>/<?=$tdate?>/1">Total Task Pending - <b><?=$tttd->b?></b></p></a><hr>
                    <p><a href="ATaskDetail/6/<?=$uid?>/1/<?=$tdate?>/<?=$tdate?>/1">Total Task Completed - <b><?=$tttd->c?></b></p></a><hr>
                    <a href="ATaskDetail/3/<?=$uid?>/1/<?=$tdate?>/<?=$tdate?>/1"><p>Call Done- <b><?=$tttd->d-$tttd->e?></b></a>
                    <a style="font-size:10px;color:red;margin-left:20px" data-toggle="collapse" href="#collapse2" role="button" aria-expanded="false" aria-controls="collapse2">Read More</a></p><hr>
                <div class="collapse" id="collapse2">
                    <a href="ATaskDetail/3/<?=$uid?>/2/<?=$tdate?>/<?=$tdate?>/1"><p>Email Done- <b><?=$tttd->f-$tttd->g?></b></p></a><hr>
                    <a href="ATaskDetail/3/<?=$uid?>/3/<?=$tdate?>/<?=$tdate?>/1"><p>Meeting Done- <b><?=$tttd->h-$tttd->i?></b></p></a><hr>
                    <a href="ATaskDetail/3/<?=$uid?>/4/<?=$tdate?>/<?=$tdate?>/1"><p>Barg in Done- <b><?=$tttd->j-$tttd->k?></b></p></a><hr>
                    <a href="ATaskDetail/3/<?=$uid?>/5/<?=$tdate?>/<?=$tdate?>/1"><p>Whatsapp Done- <b><?=$tttd->l-$tttd->m?></b></p></a><hr>
                    <a href="ATaskDetail/3/<?=$uid?>/6/<?=$tdate?>/<?=$tdate?>/1"><p>MOM Done- <b><?=$tttd->n-$tttd->o?></b></p></a><hr>
                    <a href="ATaskDetail/3/<?=$uid?>/7/<?=$tdate?>/<?=$tdate?>/1"><p>Proposal Done- <b><?=$tttd->p-$tttd->q?></b></p></a><hr>
                    <a href="ATaskDetail/7/<?=$uid?>/1/<?=$tdate?>/<?=$tdate?>/1"><p>Action Taken Yes- <b><?=$tttd->r?></b></p></a><hr>
                    <a href="ATaskDetail/8/<?=$uid?>/1/<?=$tdate?>/<?=$tdate?>/1"><p>Action Taken No- <b><?=$tttd->s?></b></p></a><hr>
                    <a href="ATaskDetail/9/<?=$uid?>/1/<?=$tdate?>/<?=$tdate?>/1"><p>Purpose Achieved Yes- <b><?=$tttd->t?></b></p></a><hr>
                    <a href="ATaskDetail/10/<?=$uid?>/1/<?=$tdate?>/<?=$tdate?>/1"><p>Purpose Achieved No- <b><?=$tttd->u?></b></p></a>
                <?php }?>
              </div></div>
              <div class="icon">
                <i class="ion ion-pie-graph"></i>
              </div>
              <a href="TDetail/<?=$tdate?>" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
            </div>
          </div>
          <!-- ./col -->
          <div class="col-lg-3 col-md-6 col-sm-12">
            <!-- small box -->
            <div class="small-box bg-light text-secondary">
              <div class="inner">
                  <?php 
                  foreach($mbdc as $mc){
                  ?>
                <center><h5>Total Funnel</h5></center><hr>
                    <p><a href="companies/0">Total Companies - <b><?=$mc->a?></b></p></a><hr>
                    <p><a href="companies/1">Open - <b><?=$mc->b?></b></a><a style="font-size:10px;color:red;margin-left:20px" data-toggle="collapse" href="#collapse3" role="button" aria-expanded="false" aria-controls="collapse3">Read More</a></p><hr>
                    <div class="collapse" id="collapse3">
                    <p><a href="companies/8">Open [RPEM] - <b><?=$mc->i?></b></p></a><hr>
                    <p><a href="companies/2">Reachout - <b><?=$mc->c?></b></a><hr>
                        <p><a href="companies/3">Tentative - <b><?=$mc->d?></b></p></a><hr>
                        <p><a href="companies/4">Will-Do-Later - <b><?=$mc->e?></b></p></a><hr>
                        <p><a href="companies/5">Not-Interest - <b><?=$mc->f?></b></p></a><hr>
                        <p><a href="companies/10">TTD-Reachout - <b><?=$mc->k?></b></p></a><hr>
                        <p><a href="companies/11">WNO-Reachout - <b><?=$mc->l?></b></p></a><hr>
                        <p><a href="companies/6">Positive - <b><?=$mc->g?></b></p></a><hr>
                        <p><a href="companies/9">Very Positive - <b><?=$mc->j?></b></p></a><hr>
                        <p><a href="companies/12">Positive NAP - <b><?=$mc->o?></b></p></a><hr>
                        <p><a href="companies/13">Very Positive NAP - <b><?=$mc->p?></b></p></a><hr>
                        <p><a href="companies/7">Closure - <b><?=$mc->h?></b></p></a><hr>
                        <p><a href="companies/14">Focus Funnel - <b><?=$mc->m?></b></p></a><hr>
                        <p><a href="companies/15">Upsell Client - <b><?=$mc->n?></b></p></a><hr>
                        <p><a href="companies/16">Key Client - <b><?=$mc->q?></b></p></a><hr>
                        <p><a href="companies/17">Positive Key Client - <b><?=$mc->r?></b></p></a><hr>
                        <p><a href="companies/18">Priority Calling - <b><?=$mc->s?></b></p></a><hr>
                        <?php } ?>
                    </div></div>
              <div class="icon">
                <i class="ion ion-bag"></i>
              </div>
              <a href="companies/0" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
            </div>
          </div>
          <!-- ./col -->
          <div class="col-lg-3 col-md-6 col-sm-12">
            <div class="small-box bg-light text-secondary">
              <div class="inner">
                  <?php $ttswwork = $this->Menu_model->new_ttsw($uid,$tdate,1);
                  foreach($ttswwork as $tw){?>
                <center><h5>Today's Task Against Status</h5></center><hr>
                <p><a href="StatusTask/<?=$uid?>/<?=$tdate?>/1/0">Open - <b><?=$tw->a?></b></p></a><hr>
                <p><a href="StatusTask/<?=$uid?>/<?=$tdate?>/8/0">Open [RPEM] - <b><?=$tw->b?></b></p></a><hr>
                <p><a href="StatusTask/<?=$uid?>/<?=$tdate?>/2/0">Reachout - <b><?=$tw->c?></b></p></a><hr>
                <p><a href="StatusTask/<?=$uid?>/<?=$tdate?>/3/0">Tentative - <b><?=$tw->d?></b></a><span style="font-size:10px;color:red;margin-left:20px" data-toggle="collapse" data-target="#collapse14" aria-expanded="true" aria-controls="collapse14">Read More</span></p><hr>
                <div class="collapse" id="collapse14">
                <p><a href="StatusTask/<?=$uid?>/<?=$tdate?>/4/0">Will-Do-Later - <b><?=$tw->e?></b></p></a><hr>
                <p><a href="StatusTask/<?=$uid?>/<?=$tdate?>/5/0">Not-Interest - <b><?=$tw->f?></b></p></a><hr>
                <p><a href="StatusTask/<?=$uid?>/<?=$tdate?>/10/0">TTD-Reachout - <b><?=$tw->j?></b></p></a><hr>
                <p><a href="StatusTask/<?=$uid?>/<?=$tdate?>/11/0">WNO-Reachout - <b><?=$tw->k?></b></p></a><hr>
                <p><a href="StatusTask/<?=$uid?>/<?=$tdate?>/6/0">Positive - <b><?=$tw->g?></b></p></a><hr>
                <p><a href="StatusTask/<?=$uid?>/<?=$tdate?>/12/0">Positive NAP - <b><?=$tw->l?></b></p></a><hr>
                <p><a href="StatusTask/<?=$uid?>/<?=$tdate?>/9/0">Very Positive - <b><?=$tw->i?></b></p></a><hr>
                <p><a href="StatusTask/<?=$uid?>/<?=$tdate?>/13/0">Very Positive NAP - <b><?=$tw->m?></b></p></a><hr>
                <p><a href="StatusTask/<?=$uid?>/<?=$tdate?>/7/0">Closure - <b><?=$tw->h?></b></p></a><hr>
                <?php } ?>
              </div></div>
              <div class="icon">
                <i class="ion ion-bag"></i>
              </div>
              <a href="TaskAStatus" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
            </div>
          </div>
          
          <div class="col-lg-3 col-md-6 col-sm-12">
            
                <div class="small-box bg-light text-secondary">
                  <div class="inner">
                      <?php $ttswwork = $this->Menu_model->new_ttswplan($uid,$tdate,1);
                      foreach($ttswwork as $tw){?>
                    <center><h5>Today's Task Plan Against Status</h5></center><hr>
                    <p><a href="StatusTask/<?=$uid?>/<?=$tdate?>/1/0">Open - <b><?=$tw->a?></b></p></a><hr>
                    <p><a href="StatusTask/<?=$uid?>/<?=$tdate?>/8/0">Open [RPEM] - <b><?=$tw->b?></b></p></a><hr>
                    <p><a href="StatusTask/<?=$uid?>/<?=$tdate?>/2/0">Reachout - <b><?=$tw->c?></b></p></a><hr>
                    <p><a href="StatusTask/<?=$uid?>/<?=$tdate?>/3/0">Tentative - <b><?=$tw->d?></b></a><span style="font-size:10px;color:red;margin-left:20px" data-toggle="collapse" data-target="#collapse54" aria-expanded="true" aria-controls="collapse54">Read More</span></p><hr>
                    <div class="collapse" id="collapse54">
                    <p><a href="StatusTask/<?=$uid?>/<?=$tdate?>/4/0">Will-Do-Later - <b><?=$tw->e?></b></p></a><hr>
                    <p><a href="StatusTask/<?=$uid?>/<?=$tdate?>/5/0">Not-Interest - <b><?=$tw->f?></b></p></a><hr>
                    <p><a href="StatusTask/<?=$uid?>/<?=$tdate?>/10/0">TTD-Reachout - <b><?=$tw->j?></b></p></a><hr>
                    <p><a href="StatusTask/<?=$uid?>/<?=$tdate?>/11/0">WNO-Reachout - <b><?=$tw->k?></b></p></a><hr>
                    <p><a href="StatusTask/<?=$uid?>/<?=$tdate?>/6/0">Positive - <b><?=$tw->g?></b></p></a><hr>
                    <p><a href="StatusTask/<?=$uid?>/<?=$tdate?>/12/0">Positive NAP - <b><?=$tw->l?></b></p></a><hr>
                    <p><a href="StatusTask/<?=$uid?>/<?=$tdate?>/9/0">Very Positive - <b><?=$tw->i?></b></p></a><hr>
                    <p><a href="StatusTask/<?=$uid?>/<?=$tdate?>/13/0">Very Positive NAP - <b><?=$tw->m?></b></p></a><hr>
                    <p><a href="StatusTask/<?=$uid?>/<?=$tdate?>/7/0">Closure - <b><?=$tw->h?></b></p></a><hr>
                    <?php } ?>
                  </div></div>
                  <div class="icon">
                    <i class="ion ion-bag"></i>
                  </div>
                  <a href="PlanTaskAStatus" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
                </div>
              </div>
          </div>
          
          
          
          
          
        <div class="row">
            
            <div class="col-lg-3 col-md-6 col-sm-12">
            <!-- small box -->
            <div class="small-box bg-light text-secondary">
              <div class="inner">
                <center><h5>Team Bargin Meeting Detail</h5></center><hr>
                <?php 
                $tbmeetd = $this->Menu_model->get_tbmeetdbyaid($uid,$tdate);
                foreach($tbmeetd as $tmd){ ?>
                <a href="#"><span id="cbim">Create Barge-in Meeting</span></a><hr>
                <p><a href="momdetail">MOM Detail</b></a><a style="font-size:10px;color:red;margin-left:20px" data-toggle="collapse" href="#collapse15" role="button" aria-expanded="false" aria-controls="collapse15">Read More</a></p><hr>
                <div class="collapse" id="collapse15">
                    <p><a href="TBMDF">Total RP Meeting</b></a></p><hr>
                    <p><a href="MeetingDetail/1/<?=$uid?>/<?=$tdate?>"><b>Meeting Detail</b></a></p><hr>
                    <p><a href="AllProposalDetail"><b>Proposal Detail</b></a></p><hr>
                    <p><a href="ProposalDetailbybd"><b>Proposal Detail by Date</b></a></p><hr>
                    <p><a href="TBMD/1/<?=$uid?>/<?=$tdate?>/<?=$tdate?>">Total Bargin Plan - <b><?=$tmd->ab?></b></a></p><hr>
                    <p><a href="TBMD/2/<?=$uid?>/<?=$tdate?>/<?=$tdate?>">Not Started Bargin - <b><?=$tmd->a?></b></a></p><hr>
                    <p><a href="TBMD/2/<?=$uid?>/<?=$tdate?>/<?=$tdate?>">Started Bargin - <b><?=$tmd->i?></b></a></p><hr>
                    <p><a href="TBMD/3/<?=$uid?>/<?=$tdate?>/<?=$tdate?>">Not Close Bargin - <b><?=$tmd->b?></b></a></p><hr>
                    <p><a href="TBMD/3/<?=$uid?>/<?=$tdate?>/<?=$tdate?>">Close Bargin - <b><?=$tmd->b?></b></a></p><hr>
                    <p><a href="TBMD/3/<?=$uid?>/<?=$tdate?>/<?=$tdate?>">Lead not Updated - <b><?=$tmd->h?></b></a></p><hr>
                    <p><a href="TBMD/4/<?=$uid?>/<?=$tdate?>/<?=$tdate?>">Completed Bargin - <b><?=$tmd->c?></b></a></p><hr>
                    <p><a href="TBMD/5/<?=$uid?>/<?=$tdate?>/<?=$tdate?>">Total RP Meeting - <b><?=$tmd->d?></b></a></p><hr>
                    <p><a href="TBMD/6/<?=$uid?>/<?=$tdate?>/<?=$tdate?>">Total Non RP Meeting - <b><?=$tmd->e?></b></a></p><hr>
                    <p><a href="TBMD/7/<?=$uid?>/<?=$tdate?>/<?=$tdate?>">Total RP Priority - <b><?=$tmd->f?></b></a></p><hr>
                    <p><a href="TBMD/8/<?=$uid?>/<?=$tdate?>/<?=$tdate?>">Total RP Not Priority - <b><?=$tmd->g?></b></a></p><hr>
                    <p><a href="TBMD/9/<?=$uid?>/<?=$tdate?>/<?=$tdate?>">Total Only Got Detail - <b><?=$tmd->k?></b></a></p><hr>
                
                <?php } ?>
              </div>
              </div>
              <div class="icon">
                <i class="ion ion-pie-graph"></i>
              </div>
              <a href="TBMDetail" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
            </div>
          </div>
            
          
          
          
          
          <!-- ./col -->
          <div class="col-lg-3 col-md-6 col-sm-12">
            <!-- small box -->
            <div class="small-box bg-light text-secondary">
              <div class="inner">
                <center><h5>Today's Traction by PST</h5></center><hr>
                
                
              </div>
              <div class="icon">
                <i class="ion ion-stats-bars"></i>
              </div>
              <a href="pstwork" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
            </div>

          </div>
          <!-- ./col -->
          
          
          
          <div class="col-lg-3 col-md-6 col-sm-12">
            
          </div>
          <!-- ./col -->
          <div class="col-lg-3 col-md-6 col-sm-12">
            <!-- small box -->
            <div class="small-box bg-light text-secondary">
              <div class="inner">
                  <?php 
                  foreach($mbdc as $mc){
                  ?>
                <center><h5>Preparation for review</h5></center><hr>
                <p><a href="BDNotWorkDD"><b>No Work B/W Date</b></p></a><hr>
                <p><a href="BDWorkBWD"><b>Work B/W Date</b></p></a><hr>
                <p><a href="companies/14">Focus Funnel - <b><?=$mc->m?></b></p></a><hr>
                <p><a href="companies/15">Upsell Client - <b><?=$mc->n?></b><a style="font-size:10px;color:red;margin-left:20px" data-toggle="collapse" href="#collapse13" role="button" aria-expanded="false" aria-controls="collapse13">Read More</a></p><hr>
                <div class="collapse" id="collapse13">
                    
                        <p><a href="companies/16">Key Client - <b><?=$mc->q?></b></p></a><hr>
                        <p><a href="companies/17">Positive Key Client - <b><?=$mc->r?></b></p></a><hr>
                        <p><a href="companies/18">Priority Calling - <b><?=$mc->s?></b></p></a><hr>
                        <?php } ?>
                    </div></div>
              <div class="icon">
                <i class="ion ion-bag"></i>
              </div>
              <a href="companies/0" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
            </div>
          </div>
          
          <!-- ./col -->
          </div>
          <div class="row">
          <div class="col-lg-3 col-md-6 col-sm-12">
            <!-- small box -->
            <div class="small-box bg-light text-secondary">
              <div class="inner">
                <center><h5>On Boarded Client</h5></center><hr>
                <a href="HandoverCompany"><p>Handover to Operation</p></a><hr>
                <a href="artworkapr"><p>Artwork Apr</p></a><hr>
                <a href="ProgramDetail"><p>Total Handover</a></p><hr>
                <a href="HandoverDetail"><p>Boarded Client Detail</a></p><hr>
                </div>
                <div class="icon">
                <i class="ion ion-bag"></i>
              </div>
              <a href="HandoverDetail" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
            </div>
          </div>
          <!-- ./col -->
          <?php $vpd = $this->Menu_model->get_pvpdetail($uid)?>
          <div class="col-lg-3 col-md-6 col-sm-12">
            <!-- small box -->
            <div class="small-box bg-light text-secondary">
              <div class="inner">
                <center><h5>Positive School</h5></center><hr>
                <p><a href="totalcdetail/1">Total No of Positive - <b><?=$vpd[0]->tp?></b></a></p><hr>
                <p><a href="totalcdetail/2">Total No of Very Positive - <b><?=$vpd[0]->tvp?></b></a></p><hr>
                <p><a href="totalcdetail/3">Total positive No of School - <b><?=$vpd[0]->pos?></b></a></p><hr>
                <p><a href="totalcdetail/4">Total Very positive No of School - <b><?=$vpd[0]->vpos?></b></a><br><a style="font-size:10px;color:red;margin-left:20px" data-toggle="collapse" href="#collapse11" role="button" aria-expanded="false" aria-controls="collapse11">Read More</a></p></a><hr>
                <div class="collapse" id="collapse11">
                <p><a href="totalcdetail/5">Total Positive Revenue - <b><?=$vpd[0]->pfb?></b></a></p><hr>
                <p><a href="totalcdetail/6">Total Very Positive Revenue - <b><?=$vpd[0]->vpfb?></b></a></p><hr>
                <p><a href="totalcdetail/7">Total Positive NAP - <b><?=$vpd[0]->tpnap?></b></a></p><hr>
                <p><a href="totalcdetail/8">Total Very Positive NAP - <b><?=$vpd[0]->tvpnap?></b></a></p><hr>
                <p><a href="totalcdetail/9">Total Positive NAP School - <b><?=$vpd[0]->pnapos?></b></a></p><hr>
                <p><a href="totalcdetail/10">Total Very Positive NAP School - <b><?=$vpd[0]->vpnapos?></b></a></p><hr>
                <p><a href="totalcdetail/11">Total Positive NAP Revenue - <b><?=$vpd[0]->pnapfb?></b></a></p><hr>
                <p><a href="totalcdetail/12">Total Very Positive NAP Revenue - <b><?=$vpd[0]->vpnapfb?></b></a></p><hr>
              </div>
              </div>
              <div class="icon">
                <i class="ion ion-bag"></i>
              </div>
              <a href="totalcdetail/1" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
            </div>
          </div>
          <!-- ./col -->
          
          
          
          </div>
        <!-- /.row (main row) -->
            </div>
  </div></div>
  </div>
            
        <div class="row p-3">
          <div class="col-lg-8 col-sm">
              <div class="row">
                  <div class="col-lg-12 col-sm">
            <div class="card card-primary card-outline card-outline-tabs">
                <h4 class="p-3">Today's Task Planned</h4>
              <div class="card-header p-0 border-bottom-0">
                <ul class="nav nav-tabs" id="custom-tabs-four-tab" role="tablist">
                  <li class="nav-item">
                      <?php $ttbyd = $this->Menu_model->get_ttbyd($uid,$tdate);
                      ?>
                    <a class="nav-link active" id="custom-tabs-four-home-tab" data-toggle="pill" href="#custom-tabs-four-home" role="tab" aria-controls="custom-tabs-four-home" aria-selected="true">
                        All <span class="badge badge-success"><?=$ttbyd[0]->ab?></span>
                    </a>
                  </li>
                  <li class="nav-item">
                    <a style="color:#E74C3C;" class="nav-link" id="custom-tabs-four-call-tab" data-toggle="pill" href="#custom-tabs-four-call" role="tab" aria-controls="custom-tabs-four-call" aria-selected="false">
                       <b> Call (<?=$ttbyd[0]->a?>) </b>
                    </a>
                  </li>
                  <li class="nav-item">
                    <a style="color:#808080;" class="nav-link" id="custom-tabs-four-research-tab" data-toggle="pill" href="#custom-tabs-four-research" role="tab" aria-controls="custom-tabs-four-research" aria-selected="false">
                        <b> Research (<?=$ttbyd[0]->h?>)</b>
                    </a>
                  </li>
                  <li class="nav-item">
                    <a style="color:#DE3163;" class="nav-link" id="custom-tabs-four-socialn-tab" data-toggle="pill" href="#custom-tabs-four-socialn" role="tab" aria-controls="custom-tabs-four-socialn" aria-selected="false">
                        <b> Social Networking (<?=$ttbyd[0]->i?>)</b>
                    </a>
                  </li>
                  <li class="nav-item">
                    <a style="color:#682642;" class="nav-link" id="custom-tabs-four-sociala-tab" data-toggle="pill" href="#custom-tabs-four-sociala" role="tab" aria-controls="custom-tabs-four-sociala" aria-selected="false">
                        <b> Social Activity (<?=$ttbyd[0]->j?>)</b>
                    </a>
                  </li>
                  <li class="nav-item">
                    <a style="color:#7D3C98;" class="nav-link" id="custom-tabs-four-email-tab" data-toggle="pill" href="#custom-tabs-four-email" role="tab" aria-controls="custom-tabs-four-email" aria-selected="false">
                        <b> Email (<?=$ttbyd[0]->b?>)</b>
                    </a>
                  </li>
                  <li class="nav-item">
                    <a style="color:#2471A3;" class="nav-link" id="custom-tabs-four-meeting-tab" data-toggle="pill" href="#custom-tabs-four-meeting" role="tab" aria-controls="custom-tabs-four-meeting" aria-selected="false">
                      <b> Scheduled Meeting (<?=$ttbyd[0]->c?>)</b>
                    </a>
                  </li>
                  <li class="nav-item">
                    <a style="color:#27AE60;" class="nav-link" id="custom-tabs-four-whatsapp-tab" data-toggle="pill" href="#custom-tabs-four-whatsapp" role="tab" aria-controls="custom-tabs-whatsapp" aria-selected="false">
                        <b> WA (<?=$ttbyd[0]->e?>)</b>
                    </a>
                  </li>
                  <li class="nav-item">
                    <a style="color:#D4AC0D;" class="nav-link" id="custom-tabs-four-mom-tab" data-toggle="pill" href="#custom-tabs-four-mom" role="tab" aria-controls="custom-tabs-mom" aria-selected="false">
                        <b> MOM (<?=$ttbyd[0]->f?>)</b>
                    </a>
                  </li>
                  <li class="nav-item">
                    <a style="color:#2E4053;" class="nav-link" id="custom-tabs-four-proposal-tab" data-toggle="pill" href="#custom-tabs-four-proposal" role="tab" aria-controls="custom-tabs-proposal" aria-selected="false">
                        <b> Proposal (<?=$ttbyd[0]->g?>)</b>
                    </a>
                  </li>
                   <li class="nav-item">
                    <a style="color:#1ABC9C;" class="nav-link" id="custom-tabs-four-barg-tab" data-toggle="pill" href="#custom-tabs-four-barg" role="tab" aria-controls="custom-tabs-four-barg" aria-selected="false">
                        <b> Visit Meeting (
                        
                        <?php 
                            $bm=0;
                            foreach($barg as $bgs){
                                $bs = $bgs->status;
                                if($bs=='Pending'){$bm++;}
                            }
                            ?>
                            <?=$bm?>)</b>
                    </a>
                  </li>
                  
                </ul>
                
              </div>
              <div class="card-body">
                <div class="tab-content" id="custom-tabs-four-tabContent">
                  <div class="tab-pane fade show active" id="custom-tabs-four-home" role="tabpanel" aria-labelledby="custom-tabs-four-home-tab">
                      <div id="accordion">
                          
                          <?php $timeslot = $this->Menu_model->get_timeslot(); foreach($timeslot as $tl){$t1=$tl->time1;$t2=$tl->time2;?>
                            <div class="card border border-info">
                                <div class="card-header">
                                    <b><?=date("h:i A", strtotime($tl->time1));?> to <?=date("h:i A", strtotime($tl->time2));?></b>
                                    </br>
                                    <?php  $ted = $this->Menu_model->get_ttbytimedaction($uid,$tdate,$t1,$t2); foreach($ted as $ted){?>
                                        <span class="badge text-white" style="background-color:<?=$ted->aclr?>"><?=$ted->acname?> <span class="badge badge-light text-dark"><?=$ted->cont?></span></span>
                                    <?php } ?>
                                    <hr>
                                    <?php $ted = $this->Menu_model->get_ttbytimedstatus($uid,$tdate,$t1,$t2); foreach($ted as $ted){?>
                                        <span class="badge text-white" style="background-color:<?=$ted->sclr?>"><?=$ted->stname?> <span class="badge badge-light text-dark"><?=$ted->cont?></span></span>
                                    <?php } ?>
                                </div>
                                <?php $totaltaktimep = $this->Menu_model->get_totaltaktimepbyh($uid,$tdate,$t1,$t2); 
                                $ttime = $totaltaktimep[0]->ttime;
                                if($ttime>'120'){$bcolor="bg-success"; $msg="Great! You have been scheduled for full-time utilization.";}
                                elseif($ttime=='0'){$bcolor="bg-danger";$msg="Caution! Make sure to plan for this period.";}
                                else{$bcolor="bg-warning";$msg="Nice job! Consider planning additional tasks.";}
                                ?>
                                <div class="card-footer <?=$bcolor?>"><b><?=$msg?></b></div>
                            </div>
                        <?php } ?>
                            
                            </div>
                  </div>
                  
                  <div class="tab-pane fade" id="custom-tabs-four-socialn" role="tabpanel" aria-labelledby="custom-tabs-four-call-tab">
                      <?php $aai=0;foreach($totalt as $tt){if($tt->plan==1){if($tt->actiontype_id=='13'){
                          $taid = $tt->actiontype_id;
                          $taid=$this->Menu_model->get_actionbyid($taid);
                          $time = $tt->appointmentdatetime;
                          $time = date('h:i a', strtotime($time));
                      ?>
                        <div class="list-group-item list-group-item-action">
                            <button id="add_act<?=$aai?>" value="<?=$tt->id?>" style="background: none;color: inherit;border: none;padding: 0;font: inherit;cursor: pointer;outline: inherit;">
                          <input type="hidden" value="<?=$tt->id?>" id="tid">
                           <span class="mr-3 align-items-center">
                              <i class="fa-solid fa-circle"></i>
                           </span>
                           <span class="flex"><?=$taid[0]->name?> | 
                               <strong class="text-secondary mr-1"><?=$tt->compname?></strong><br>
                               <small class="text-muted">Task Time:- <?=$time?></small>
                            </span>
                            <span class="p-3" style="color:<?=$tt->color?>;"><?=$tt->name?>
                            </span>
                            <span class="text-right">
                                <i class="fa-solid fa-forward"></i>
                            </span>
                        </button></div>
                      <?php $aai++;}}} ?>
                  </div>
                  <div class="tab-pane fade" id="custom-tabs-four-sociala" role="tabpanel" aria-labelledby="custom-tabs-four-call-tab">
                      <?php $aai=0;foreach($totalt as $tt){if($tt->plan==1){if($tt->actiontype_id=='14'){
                          $taid = $tt->actiontype_id;
                          $taid=$this->Menu_model->get_actionbyid($taid);
                          $time = $tt->appointmentdatetime;
                          $time = date('h:i a', strtotime($time));
                      ?>
                        <div class="list-group-item list-group-item-action">
                            <button id="add_act<?=$aai?>" value="<?=$tt->id?>" style="background: none;color: inherit;border: none;padding: 0;font: inherit;cursor: pointer;outline: inherit;">
                          <input type="hidden" value="<?=$tt->id?>" id="tid">
                           <span class="mr-3 align-items-center">
                              <i class="fa-solid fa-circle"></i>
                           </span>
                           <span class="flex"><?=$taid[0]->name?> | 
                               <strong class="text-secondary mr-1"><?=$tt->compname?></strong><br>
                               <small class="text-muted">Task Time:- <?=$time?></small>
                            </span>
                            <span class="p-3" style="color:<?=$tt->color?>;"><?=$tt->name?>
                            </span>
                            <span class="text-right">
                                <i class="fa-solid fa-forward"></i>
                            </span>
                        </button></div>
                      <?php $aai++;}}} ?>
                  </div>
                  <div class="tab-pane fade" id="custom-tabs-four-call" role="tabpanel" aria-labelledby="custom-tabs-four-call-tab">
                      <?php $aai=0;foreach($totalt as $tt){if($tt->plan==1){if($tt->actiontype_id=='1'){
                          $taid = $tt->actiontype_id;
                          $taid=$this->Menu_model->get_actionbyid($taid);
                          $time = $tt->appointmentdatetime;
                          $time = date('h:i a', strtotime($time));
                      ?>
                        <div class="list-group-item list-group-item-action">
                            <button id="add_act<?=$aai?>" value="<?=$tt->id?>" style="background: none;color: inherit;border: none;padding: 0;font: inherit;cursor: pointer;outline: inherit;">
                          <input type="hidden" value="<?=$tt->id?>" id="tid">
                           <span class="mr-3 align-items-center">
                             <i class="fa-sharp fa-solid fa-circle fa-beat" style="color: #d90d2b;"></i>
                           </span>
                           <span class="flex"><?=$taid[0]->name?> | 
                               <strong class="text-secondary mr-1"><?=$tt->compname?></strong><br>
                               <small class="text-muted">Task Time:- <?=$time?></small>
                            </span>
                            <span class="m-1" style="background-color:<?=$tt->clr?>;color:#FFFFFF;"><?=$tt->name?>
                            </span>
                            <span class="text-right">
                                <i class="fa-solid fa-forward"></i>
                            </span>
                        </button></div>
                      <?php $aai++;}}} ?>
                  </div>
                  
                  <div class="tab-pane fade" id="custom-tabs-four-research" role="tabpanel" aria-labelledby="custom-tabs-four-research-tab">
                      <?php $aai=0;foreach($totalt as $tt){if($tt->plan==1){if($tt->actiontype_id=='10'){
                          $taid = $tt->actiontype_id;
                          $taid=$this->Menu_model->get_actionbyid($taid);
                          $time = $tt->appointmentdatetime;
                          $time = date('h:i a', strtotime($time));
                      ?>
                        
                        <div class="list-group-item list-group-item-action">
                            <button id="add_act<?=$aai?>" value="<?=$tt->id?>" style="background: none;color: inherit;border: none;padding: 0;font: inherit;cursor: pointer;outline: inherit;">
                          <input type="hidden" value="<?=$tt->id?>" id="tid">
                           <span class="mr-3 align-items-center">
                              <i class="fa-solid fa-circle"></i>
                           </span>
                           <span class="flex"><?=$taid[0]->name?> | 
                               <strong class="text-secondary mr-1"><?=$tt->compname?></strong><br>
                               <small class="text-muted">Task Time:- <?=$time?></small>
                            </span>
                            <span class="p-3" style="color:<?=$tt->color?>;"><?=$tt->name?>
                            </span>
                            <span class="text-right">
                                <i class="fa-solid fa-forward"></i>
                            </span>
                        </button></div>
                      <?php $aai++;}}} ?>
                  </div>
                  
                  <div class="tab-pane fade" id="custom-tabs-four-email" role="tabpanel" aria-labelledby="custom-tabs-four-email-tab">
                      <?php foreach($totalt as $tt){if($tt->plan==1){if($tt->actiontype_id=='2'){
                      ?>
                        <div class="list-group-item list-group-item-action">
                        <button id="add_act<?=$aai?>" value="<?=$tt->id?>" style="background: none;color: inherit;border: none;padding: 0;font: inherit;cursor: pointer;outline: inherit;">
                        
                           <span class="mr-3 align-items-center">
                              <i class="fa-solid fa-circle"></i>
                           </span>
                           <span class="flex">
                               <strong class="text-secondary mr-1"><?=$tt->compname?></strong><br>
                               <small class="text-muted">Next Task:- </small>
                            </span>
                            <span class="p-3" style="color:<?=$tt->color?>;"><?=$tt->name?>
                            </span>
                            <span class="text-right">
                                <i class="fa-solid fa-forward"></i>
                            </span>
                        </button></div>
                      <?php $aai++;}}} ?>
                  </div>
                  <div class="tab-pane fade" id="custom-tabs-four-meeting" role="tabpanel" aria-labelledby="custom-tabs-four-meeting-tab">
                      <?php foreach($totalt as $tt){if($tt->plan==1){if($tt->actiontype_id=='3'){
                      ?>
                        <div class="list-group-item list-group-item-action">
                        <button id="add_act<?=$aai?>" value="<?=$tt->id?>" style="background: none;color: inherit;border: none;padding: 0;font: inherit;cursor: pointer;outline: inherit;">
                        
                           <span class="mr-3 align-items-center">
                              <i class="fa-solid fa-circle"></i>
                           </span>
                           <span class="flex">
                               <strong class="text-secondary mr-1"><?=$tt->compname?></strong><br>
                               <small class="text-muted">Next Task:- </small>
                            </span>
                            <span class="p-3" style="color:<?=$tt->color?>;"><?=$tt->name?>
                            </span>
                            <span class="text-right">
                                <i class="fa-solid fa-forward"></i>
                            </span>
                        </button></div>
                      <?php $aai++;}}} ?>
                  </div>
                  <div class="tab-pane fade" id="custom-tabs-four-whatsapp" role="tabpanel" aria-labelledby="custom-tabs-four-whatsapp-tab">
                      <?php foreach($totalt as $tt){if($tt->plan==1){if($tt->actiontype_id=='5'){
                      ?>
                        <div class="list-group-item list-group-item-action">
                        <button id="add_act<?=$aai?>" value="<?=$tt->id?>" style="background: none;color: inherit;border: none;padding: 0;font: inherit;cursor: pointer;outline: inherit;">
                           <span class="mr-3 align-items-center">
                              <i class="fa-solid fa-circle"></i>
                           </span>
                           <span class="flex">
                               <strong class="text-secondary mr-1"><?=$tt->compname?></strong><br>
                               <small class="text-muted">Next Task:- </small>
                            </span>
                            <span class="p-3" style="color:<?=$tt->color?>;"><?=$tt->name?>
                            </span>
                            <span class="text-right">
                                <i class="fa-solid fa-forward"></i>
                            </button></span>
                        </div>
                      <?php $aai++;}}} ?>
                  </div>
                  
                  <div class="tab-pane fade" id="custom-tabs-four-mom" role="tabpanel" aria-labelledby="custom-tabs-four-mom-tab">
                      <?php foreach($totalt as $tt){if($tt->plan==1){if($tt->actiontype_id=='6'){
                      ?>
                        <div class="list-group-item list-group-item-action">
                        <button id="add_act<?=$aai?>" value="<?=$tt->id?>" style="background: none;color: inherit;border: none;padding: 0;font: inherit;cursor: pointer;outline: inherit;">
                           <span class="mr-3 align-items-center">
                              <i class="fa-solid fa-circle"></i>
                           </span>
                           <span class="flex">
                               <strong class="text-secondary mr-1"><?=$tt->compname?></strong><br>
                               <small class="text-muted">Next Task:- </small>
                            </span>
                            <span class="p-3" style="color:<?=$tt->color?>;"><?=$tt->name?>
                            </span>
                            <span class="text-right">
                                <i class="fa-solid fa-forward"></i>
                            </button></span>
                        </div>
                      <?php $aai++;}}} ?>
                  </div>
                  
                  <div class="tab-pane fade" id="custom-tabs-four-proposal" role="tabpanel" aria-labelledby="custom-tabs-four-proposal-tab">
                      <?php foreach($totalt as $tt){if($tt->plan==1){if($tt->actiontype_id=='7'){
                      ?>
                        <div class="list-group-item list-group-item-action">
                        <button id="add_act<?=$aai?>" value="<?=$tt->id?>" style="background: none;color: inherit;border: none;padding: 0;font: inherit;cursor: pointer;outline: inherit;">
                           <span class="mr-3 align-items-center">
                              <i class="fa-solid fa-circle"></i>
                           </span>
                           <span class="flex">
                               <strong class="text-secondary mr-1"><?=$tt->compname?></strong><br>
                               <small class="text-muted">Next Task:- </small>
                            </span>
                            <span class="p-3" style="color:<?=$tt->color?>;"><?=$tt->name?>
                            </span>
                            <span class="text-right">
                                <i class="fa-solid fa-forward"></i>
                            </button></span>
                        </div>
                      <?php $aai++;}}} ?>
                  </div>
                  
                  <div class="tab-pane fade" id="custom-tabs-four-barg" role="tabpanel" aria-labelledby="custom-tabs-four-barg-tab">
                      
                            <?php 
                            $bl=0;
                            foreach($barg as $brg){
                                $bs = $brg->status;
                                $cid = $brg->cid;
                                $cd = $this->Menu_model->get_cdbyid($cid);
                                ?>
                                <?php if($bs=='Pending'){?>
                                    <button style="background: none;color: inherit;border: none;padding: 0;font: inherit;cursor: pointer;outline: inherit;" value="<?=$brg->id?>" id="startm<?=$bl?>">
                                        <div class="list-group-item list-group-item-action">
                                        <?=$cd[0]->compname?> | 
                                        <?=$cd[0]->city?> | 
                                        <?=$cd[0]->state?> | 
                                        <?=$brg->storedt?> | 
                                        <b class="text-success">Start Meeting</b>
                                    </div></button>
                                <?php }if($bs=='Start'){?>
                                    <button style="background: none;color: inherit;border: none;padding: 0;font: inherit;cursor: pointer;outline: inherit;" id="closem<?=$bl?>" value="<?=$brg->id?>">
                                        <div class="list-group-item list-group-item-action">
                                        <?=$cd[0]->compname?> | 
                                        <?=$cd[0]->city?> | 
                                        <?=$cd[0]->state?> | 
                                        <?=$brg->storedt?> |
                                        <b class="text-danger">Close Meeting</b>
                                    </div>
                            <?php $bl++;}} ?>
                        </div>
                            <?php foreach($barg as $br){if($br->status=='RPClose'){
                                $cid = $br->cid;
                                $cd = $this->Menu_model->get_cdbyid($cid);?>
                            <a href="BMNewLead/<?=$br->id?>"><div class="list-group-item list-group-item-action">
                                        <?=$cd[0]->compname?> | 
                                        <?=$cd[0]->city?> | 
                                        <?=$cd[0]->state?> | 
                                        <?=$brg->storedt?> |
                                        <b>Update Lead</b>
                                    </div></a>
                            <?php }} ?>
                        </div>
                  </div>
                </div>
            </div>
              <!-- /.card -->
            </div>
            <div class="col-lg-12 col-sm">
                <div class="card card-primary card-outline card-outline-tabs">
                  <h4 class="p-3">Today's Task Initiated</h4>
                  <div class="row p-3">
                      <div class="col-lg-6 col-sm">
                          <div id="tistatusgraph"></div>
                            <script>
                                google.charts.load("current", {packages:["corechart"]});
                                google.charts.setOnLoadCallback(drawChart);
                                function drawChart() {
                                    var data = google.visualization.arrayToDataTable([
                                        ['Status', 'No of Compnay'],
                                        <?php
                                        $gdata = $this->Menu_model->get_todaytis($uid, $tdate);
                                        foreach ($gdata as $gd) {
                                            echo '["' . $gd->stame . ' (' . $gd->cont . ')",' . $gd->cont . '],';
                                        }
                                        ?>
                                    ]);
                                    var options = {
                                        title:'Task Initiated Againt Status',
                                        width: '100%',
                                        is3D: true,
                                        slices: {
                                            <?php $i=0; $gdata = $this->Menu_model->get_todaytis($uid, $tdate);foreach ($gdata as $gd) {?>
                                            <?=$i?>: { color: '<?=$gd->sclr?>' },
                                            <?php $i++;} ?>
                                        }
                                    };
                                    var chart = new google.visualization.PieChart(document.getElementById('tistatusgraph'));
                                    chart.draw(data, options);
                                }
                            </script>
                          
                      </div>
                      <div class="col-lg-6 col-sm">
                          <div id="tiactiongraph"></div>
                            <script>
                                google.charts.load("current", {packages:["corechart"]});
                                google.charts.setOnLoadCallback(drawChart);
                                function drawChart() {
                                    var data = google.visualization.arrayToDataTable([
                                        ['Action', 'No of Compnay'],
                                        <?php
                                        $gdata = $this->Menu_model->get_todaytia($uid, $tdate);
                                        foreach ($gdata as $gd) {
                                            echo '["' . $gd->acname . ' (' . $gd->cont . ')",' . $gd->cont . '],';
                                        }
                                        ?>
                                    ]);
                                    var options = {
                                        title:'Task Initiated Againt Action',
                                        width: '100%',
                                        is3D: true,
                                        slices: {
                                            <?php $i=0; $gdata = $this->Menu_model->get_todaytia($uid, $tdate);foreach ($gdata as $gd) {?>
                                            <?=$i?>: { color: '<?=$gd->aclr?>' },
                                            <?php $i++;} ?>
                                        }
                                    };
                                    var chart = new google.visualization.PieChart(document.getElementById('tiactiongraph'));
                                    chart.draw(data, options);
                                }
                            </script>
                      
                      </div>
                      <div class="col-lg-6 col-sm">
                          <div id="tipartnergraph"></div>
                            <script>
                                google.charts.load("current", {packages:["corechart"]});
                                google.charts.setOnLoadCallback(drawChart);
                                function drawChart() {
                                    var data = google.visualization.arrayToDataTable([
                                        ['Action', 'No of Compnay'],
                                        <?php
                                        $gdata = $this->Menu_model->get_todaytip($uid, $tdate);
                                        foreach ($gdata as $gd) {
                                            echo '["' . $gd->pname . ' (' . $gd->cont . ')",' . $gd->cont . '],';
                                        }
                                        ?>
                                    ]);
                                    var options = {
                                        title:'Task Initiated Againt Partner',
                                        width: '100%',
                                        is3D: true,
                                        slices: {
                                            <?php $i=0; $gdata = $this->Menu_model->get_todaytip($uid, $tdate);foreach ($gdata as $gd) {?>
                                            <?=$i?>: { color: '<?=$gd->pclr?>' },
                                            <?php $i++;} ?>
                                        }
                                    };
                                    var chart = new google.visualization.PieChart(document.getElementById('tipartnergraph'));
                                    chart.draw(data, options);
                                }
                            </script>
                      </div>
                      
                      
                      
                      <div class="col-lg-6 col-sm">
                            <div id="tilateontime"></div>
                            <script>
                                google.charts.load("current", { packages: ["corechart"] });
                                google.charts.setOnLoadCallback(drawChart);
                        
                                function drawChart() {
                                    // Sample data for demonstration
                                    var sampleData = [
                                        ['Action', 'No of Company', 'URL'],
                                        <?php
                                        $gdata = $this->Menu_model->get_todaytiol($uid, $tdate);?>
                                        ["Late (<?=$gdata[0]->late?>)",'<?=$gdata[0]->late?>',"<?=base_url();?>Menu/InitiatedTask/<?=$uid?>/<?=$tdate?>/<?=$tdate?>/1"],
                                        ["Ontime (<?=$gdata[0]->ontime?>)",'<?=$gdata[0]->ontime?>',"<?=base_url();?>Menu/InitiatedTask/<?=$uid?>/<?=$tdate?>/<?=$tdate?>/2"],
                                    ];
                        
                                    var data = google.visualization.arrayToDataTable(sampleData);
                        
                                    var options = {
                                        title: 'Task Initiated Ontime/Late',
                                        width: '100%',
                                        is3D: true,
                                        slices: {
                                            0: { color: 'red' },
                                            1: { color: 'green' },
                                        }
                                    };
                        
                                    var chart = new google.visualization.PieChart(document.getElementById('tilateontime'));
                                    google.visualization.events.addListener(chart, 'select', chartSliceClick);
                                    chart.draw(data, options);
                                    function chartSliceClick() {
                                        var selectedItem = chart.getSelection()[0];
                                        if (selectedItem) {
                                            var selectedSliceURL = data.getValue(selectedItem.row, 2);
                                            if (selectedSliceURL) {
                                                window.open(selectedSliceURL, '_blank');
                                            }
                                        }
                                    }
                                }
                            </script>

                      </div>
                  </div>
                    
                </div>
                
                    <div class="col-lg-12 col-sm">
                        <div class="card card-primary card-outline card-outline-tabs">
                          <h4 class="p-3">Today's Task Updated</h4>
                            <div class="row p-3">
                      <div class="col-lg-6 col-sm">
                          <div id="tistatusgraphu"></div>
                            <script>
                                google.charts.load("current", {packages:["corechart"]});
                                google.charts.setOnLoadCallback(drawChart);
                                function drawChart() {
                                    var data = google.visualization.arrayToDataTable([
                                        ['Status', 'No of Compnay'],
                                        <?php
                                        $gdata = $this->Menu_model->get_todaytis($uid, $tdate);
                                        foreach ($gdata as $gd) {
                                            echo '["' . $gd->stame . ' (' . $gd->cont . ')",' . $gd->cont . '],';
                                        }
                                        ?>
                                    ]);
                                    var options = {
                                        title:'Task Updated Againt Status',
                                        width: '100%',
                                        is3D: true,
                                        slices: {
                                            <?php $i=0; $gdata = $this->Menu_model->get_todaytis($uid, $tdate);foreach ($gdata as $gd) {?>
                                            <?=$i?>: { color: '<?=$gd->sclr?>' },
                                            <?php $i++;} ?>
                                        }
                                    };
                                    var chart = new google.visualization.PieChart(document.getElementById('tistatusgraphu'));
                                    chart.draw(data, options);
                                }
                            </script>
                          
                      </div>
                      <div class="col-lg-6 col-sm">
                          <div id="tiactiongraphu"></div>
                            <script>
                                google.charts.load("current", {packages:["corechart"]});
                                google.charts.setOnLoadCallback(drawChart);
                                function drawChart() {
                                    var data = google.visualization.arrayToDataTable([
                                        ['Action', 'No of Compnay'],
                                        <?php
                                        $gdata = $this->Menu_model->get_todaytia($uid, $tdate);
                                        foreach ($gdata as $gd) {
                                            echo '["' . $gd->acname . ' (' . $gd->cont . ')",' . $gd->cont . '],';
                                        }
                                        ?>
                                    ]);
                                    var options = {
                                        title:'Task Updated Againt Action',
                                        width: '100%',
                                        is3D: true,
                                        slices: {
                                            <?php $i=0; $gdata = $this->Menu_model->get_todaytia($uid, $tdate);foreach ($gdata as $gd) {?>
                                            <?=$i?>: { color: '<?=$gd->aclr?>' },
                                            <?php $i++;} ?>
                                        }
                                    };
                                    var chart = new google.visualization.PieChart(document.getElementById('tiactiongraphu'));
                                    chart.draw(data, options);
                                }
                            </script>
                      
                      </div>
                      <div class="col-lg-6 col-sm">
                          <div id="tipartnergraphu"></div>
                            <script>
                                google.charts.load("current", {packages:["corechart"]});
                                google.charts.setOnLoadCallback(drawChart);
                                function drawChart() {
                                    var data = google.visualization.arrayToDataTable([
                                        ['Action', 'No of Compnay'],
                                        <?php
                                        $gdata = $this->Menu_model->get_todaytip($uid, $tdate);
                                        foreach ($gdata as $gd) {
                                            echo '["' . $gd->pname . ' (' . $gd->cont . ')",' . $gd->cont . '],';
                                        }
                                        ?>
                                    ]);
                                    var options = {
                                        title:'Task Updated Againt Partner',
                                        width: '100%',
                                        is3D: true,
                                        slices: {
                                            <?php $i=0; $gdata = $this->Menu_model->get_todaytip($uid, $tdate);foreach ($gdata as $gd) {?>
                                            <?=$i?>: { color: '<?=$gd->pclr?>' },
                                            <?php $i++;} ?>
                                        }
                                    };
                                    var chart = new google.visualization.PieChart(document.getElementById('tipartnergraphu'));
                                    chart.draw(data, options);
                                }
                            </script>
                      </div>
                      
                      
                      
                      <div class="col-lg-6 col-sm">
                            <div id="tilateontimeu"></div>
                            <script>
                                google.charts.load("current", { packages: ["corechart"] });
                                google.charts.setOnLoadCallback(drawChart);
                                function drawChart() {
                                    var sampleData = [
                                        ['Action', 'No of Company', 'URL'],
                                        <?php
                                        $gdata = $this->Menu_model->get_todaytiol($uid, $tdate);?>
                                        ["Late (<?=$gdata[0]->late?>)",'<?=$gdata[0]->late?>',"<?=base_url();?>Menu/OntimeLate/<?=$uid?>/<?=$tdate?>/<?=$tdate?>/1"],
                                        ["Ontime (<?=$gdata[0]->ontime?>)",'<?=$gdata[0]->ontime?>',"<?=base_url();?>Menu/OntimeLate/<?=$uid?>/<?=$tdate?>/<?=$tdate?>/2"],
                                    ];
                                    var data = google.visualization.arrayToDataTable(sampleData);
                                    var options = {
                                        title: 'Task Updated Ontime/Late',
                                        width: '100%',
                                        is3D: true,
                                        slices: {
                                            0: { color: 'red' },
                                            1: { color: 'green' },
                                        }
                                    };
                        
                                    var chart = new google.visualization.PieChart(document.getElementById('tilateontimeu'));
                                    google.visualization.events.addListener(chart, 'select', chartSliceClick);
                                    chart.draw(data, options);
                                    function chartSliceClick() {
                                        var selectedItem = chart.getSelection()[0];
                                        if (selectedItem) {
                                            var selectedSliceURL = data.getValue(selectedItem.row, 2);
                                            if (selectedSliceURL) {
                                                window.open(selectedSliceURL, '_blank');
                                            }
                                        }
                                    }
                                }
                            </script>

                      </div>
                      
                      
                      <div class="col-lg-6 col-sm">
                            <div id="converstiontvu"></div>
                            <script>
                                google.charts.load("current", { packages: ["corechart"] });
                                google.charts.setOnLoadCallback(drawChart);
                        
                                function drawChart() {
                                    // Sample data for demonstration
                                    var sampleData = [
                                        ['Action', 'No of Company', 'URL'],
                                        <?php
                                        $gdata = $this->Menu_model->get_todaytiol($uid, $tdate);?>
                                        ["Late (<?=$gdata[0]->late?>)",'<?=$gdata[0]->late?>',"<?=base_url();?>Menu/OntimeLate/<?=$uid?>/<?=$tdate?>/<?=$tdate?>/1"],
                                        ["Ontime (<?=$gdata[0]->ontime?>)",'<?=$gdata[0]->ontime?>',"<?=base_url();?>Menu/OntimeLate/<?=$uid?>/<?=$tdate?>/<?=$tdate?>/2"],
                                    ];
                        
                                    var data = google.visualization.arrayToDataTable(sampleData);
                        
                                    var options = {
                                        title: 'Task Vs Convertion',
                                        width: '100%',
                                        is3D: true,
                                        slices: {
                                            0: { color: 'red' },
                                            1: { color: 'green' },
                                        }
                                    };
                        
                                    var chart = new google.visualization.PieChart(document.getElementById('converstiontvu'));
                                    google.visualization.events.addListener(chart, 'select', chartSliceClick);
                                    chart.draw(data, options);
                                    function chartSliceClick() {
                                        var selectedItem = chart.getSelection()[0];
                                        if (selectedItem) {
                                            var selectedSliceURL = data.getValue(selectedItem.row, 2);
                                            if (selectedSliceURL) {
                                                window.open(selectedSliceURL, '_blank');
                                            }
                                        }
                                    }
                                }
                            </script>

                      </div>
                      
                      
                      
                      <div class="col-lg-6 col-sm">
                            <div id="converstionu"></div>
                            <script>
                                google.charts.load("current", {packages:["corechart"]});
                                google.charts.setOnLoadCallback(drawChart);
                                function drawChart() {
                                    var data = google.visualization.arrayToDataTable([
                                        ['Status', 'No of Compnay'],
                                        <?php
                                        $gdata = $this->Menu_model->get_todaytis($uid, $tdate);
                                        foreach ($gdata as $gd) {
                                            echo '["' . $gd->stame . ' (' . $gd->cont . ')",' . $gd->cont . '],';
                                        }
                                        ?>
                                    ]);
                                    var options = {
                                        title:'Status Wise Task Converstion',
                                        width: '100%',
                                        is3D: true,
                                        slices: {
                                            <?php $i=0; $gdata = $this->Menu_model->get_todaytis($uid, $tdate);foreach ($gdata as $gd) {?>
                                            <?=$i?>: { color: '<?=$gd->sclr?>' },
                                            <?php $i++;} ?>
                                        }
                                    };
                                    var chart = new google.visualization.PieChart(document.getElementById('converstionu'));
                                    chart.draw(data, options);
                                }
                            </script>

                      </div>
                      
                      
                      
                      
                      
                      
                      
                      
                      
                      
                      
                      
                      
                      
                      
                      
                      
                      
                      
                  </div>
                          
                          
                          
                          
                          
                          
                          
                        </div>
                    </div>
            </div>
        </div>
            <div class="col-lg-4 col-sm">
                <div class="card card-primary card-outline card-outline-tabs">
                    <div class="card-header text-center "><b>Status Wise Plan</b></div>
                    <div class="card-body">
                        
                            <div id="statusgraph"></div>
                            <script>
                                google.charts.load("current", {packages:["corechart"]});
                                google.charts.setOnLoadCallback(drawChart);
                                
                                function drawChart() {
                                    var data = google.visualization.arrayToDataTable([
                                        ['Status', 'No of Compnay'],
                                        <?php
                                        $gdata = $this->Menu_model->get_todayts($uid, $tdate);
                                        foreach ($gdata as $gd) {
                                            echo '["' . $gd->stame . ' (' . $gd->cont . ')",' . $gd->cont . '],';
                                        }
                                        ?>
                                    ]);
                            
                                    var options = {
                                        width: '100%',
                                        is3D: true,
                                        slices: {
                                            <?php $i=0; $gdata = $this->Menu_model->get_todayts($uid, $tdate);foreach ($gdata as $gd) {?>
                                            <?=$i?>: { color: '<?=$gd->sclr?>' },
                                            <?php $i++;} ?>
                                        }
                                        
                                    };
                            
                                    var chart = new google.visualization.PieChart(document.getElementById('statusgraph'));
                                    chart.draw(data, options);
                                }
                            </script>

                    </div>
                </div>
            
                <div class="card card-primary card-outline card-outline-tabs">
                    <div class="card-header text-center "><b>Action Wise Plan</b></div>
                    <div class="card-body">
                        <div id="actiongraph"></div>
                            <script>
                                google.charts.load("current", {packages:["corechart"]});
                                google.charts.setOnLoadCallback(drawChart);
                                function drawChart() {
                                    var data = google.visualization.arrayToDataTable([
                                        ['Status', 'No of Compnay'],
                                        <?php
                                        $gdata = $this->Menu_model->get_todayta($uid, $tdate);
                                        foreach ($gdata as $gd) {
                                            echo '["' . $gd->acname . ' (' . $gd->cont . ')",' . $gd->cont . '],';
                                        }
                                        ?>
                                    ]);
                                    var options = {
                                        width: '100%',
                                        is3D: true,
                                        slices: {
                                            <?php $i=0; $gdata = $this->Menu_model->get_todayta($uid, $tdate);foreach ($gdata as $gd) {?>
                                            <?=$i?>: { color: '<?=$gd->aclr?>' },
                                            <?php $i++;} ?>
                                        }
                                    };
                                    var chart = new google.visualization.PieChart(document.getElementById('actiongraph'));
                                    chart.draw(data, options);
                                }
                            </script>
                    </div>
                </div>
            
            
                <div class="card card-primary card-outline card-outline-tabs">
                    <div class="card-header text-center "><b>Partner Wise Plan</b></div>
                    <div class="card-body">
                        
                        <div id="partnergraph"></div>
                            <script>
                                google.charts.load("current", {packages:["corechart"]});
                                google.charts.setOnLoadCallback(drawChart);
                                function drawChart() {
                                    var data = google.visualization.arrayToDataTable([
                                        ['Status', 'No of Compnay'],
                                        <?php
                                        $gdata = $this->Menu_model->get_todaytp($uid, $tdate);
                                        foreach ($gdata as $gd) {
                                            echo '["' . $gd->pname . ' (' . $gd->cont . ')",' . $gd->cont . '],';
                                        }
                                        ?>
                                    ]);
                                    var options = {
                                        width: '100%',
                                        is3D: true,
                                        slices: {
                                            <?php $i=0; $gdata = $this->Menu_model->get_todaytp($uid, $tdate);foreach ($gdata as $gd) {?>
                                            <?=$i?>: { color: '<?=$gd->pclr?>' },
                                            <?php $i++;} ?>
                                        }
                                    };
                                    var chart = new google.visualization.PieChart(document.getElementById('partnergraph'));
                                    chart.draw(data, options);
                                }
                            </script>
                        
                        
                        
                        
                    </div>
                </div>
                
                
            </div>
            
            
            
        </div>
</div>
 </div>
        </div>
</div>
   <!-- /.row (main row) -->
      </div><!-- /.container-fluid -->
    </section>
    <!-- /.content -->
  </div>
  <!-- /.content-wrapper -->
  
<style>

#myInput {
  background-position: 10px 12px;
  background-repeat: no-repeat;
  width: 100%;
  font-size: 16px;
  padding: 12px 20px 12px 40px;
  border: 1px solid #ddd;
  margin-bottom: 12px;
}

#myUL {
  list-style-type: none;
  padding: 0;
  margin: 0;
}

#myUL li a {
  border: 1px solid #ddd;
  margin-top: -1px; /* Prevent double borders */
  background-color: #f6f6f6;
  padding: 12px;
  text-decoration: none;
  font-size: 18px;
  color: black;
  display: block
}

#myUL li a:hover:not(.header) {
  background-color: #eee;
}
</style>


<script>
function myFunction() {
    var input, filter, ul, li, a, i, txtValue;
    input = document.getElementById("myInput");
    filter = input.value.toUpperCase();
    ul = document.getElementById("myUL");
    li = ul.getElementsByTagName("li");
    for (i = 0; i < li.length; i++) {
        a = li[i].getElementsByTagName("a")[0];
        txtValue = a.textContent || a.innerText;
        if (txtValue.toUpperCase().indexOf(filter) > -1) {
            li[i].style.display = "";
        } else {
            li[i].style.display = "none";
        }
    }
}
</script>
  
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
      "responsive": false, "lengthChange": false, "autoWidth": false,
      "buttons": ["copy", "csv", "excel", "pdf", "print", "colvis"]
    }).buttons().container().appendTo('#example1_wrapper .col-md-6:eq(0)');
</script>
</body>
</html>
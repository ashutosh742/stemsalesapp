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
    <!-- DataTables -->
    <link rel="stylesheet" href="<?=base_url();?>assets/css/dataTables.bootstrap4.min.css">
    <link rel="stylesheet" href="<?=base_url();?>assets/css/responsive.bootstrap4.min.css">
    <link rel="stylesheet" href="<?=base_url();?>assets/css/buttons.bootstrap4.min.css">
    <style>
      .scrollme {
      overflow-x: auto;
      }
      .content-wrapper>.content {
      background: azure;
      }
      .inner h5{
      background: blanchedalmond;
      line-height: 35px;
      font-size: 17px;
      border-radius: 26px;
      box-shadow: rgba(0, 0, 0, 0.05) 0px 0px 0px 1px, rgb(209, 213, 219) 0px 0px 0px 1px inset;
      font-weight: 700;
      }
      .bg-light, .bg-light>a {
      color: #1f2d3d !important;
      background: #ebf5cb !important;
      border-radius: 40px;
      position: relative;
      overflow: hidden;
      /* box-shadow: rgba(0, 0, 0, .1) 0 1px 2px 0; */
      /* cursor: pointer; */
      font-size: 19px;
      text-align: left;
      }
      .small-box>.small-box-footer {
      background: #c5eb4d !important;
      font-weight: 500;
      }
    </style>
  </head>
  <body class="hold-transition sidebar-mini layout-fixed">
    <div class="wrapper">
      <!-- Preloader -->
      <!-- Navbar -->
  <?php
      // echo "admid".$uid;
      // die;
      $admid = $uid;
      $myid = $uid;
      $admid = $uid;

      // echo "admid = ".$admid;
      // die;
  ?>
      <?php require('nav.php');?>
      <?php require('addpop.php');?>
      <!-- /.navbar -->

        <style>
    .card {
  transition: box-shadow 0.2s ease-in-out;
  background: linear-gradient(to right, #e0f7fa, #ffffff);
  border-radius: 12px;
  box-shadow: 0 4px 8px rgba(0,0,0,0.1);
  padding: 20px;
  color: black !important;
}

/* For child divs inside card */
.card > div:nth-child(even) {
  background: linear-gradient(to right, #f1f8e9, #ffffff);
    color:black!important;
}

.card > div:nth-child(odd) {
  background: linear-gradient(to right, #ffffff, #f6e5eaff);
  color:black!important;
}
.list-group-item {background: linear-gradient(to right, #fff8e1, #ffffff); border-radius: 12px; box-shadow: 0 4px 8px rgba(0,0,0,0.1); padding: 10px; margin-bottom: 3px;}
</style>


      <?php
        $udetail = $this->Menu_model->get_userbyid($uid);
        $admid = $udetail[0]->aadmin;
        $bd = $this->Menu_model->get_userbyaSCid($admid);
        
        $day = $this->Menu_model->get_daydbyad1($admid,$tdate);
        $tttd = $this->Menu_model->get_tteamtdSC($admid,$tdate);
       
        $tbmeetd = $this->Menu_model->get_tbmeetdbyaid($uid,$tdate);

        $teamfu = $this->Menu_model->get_mbdcbyaidSC($admid);
        $mytaskd = $this->Menu_model->get_admintteamtd($myid,$tdate);
        
        /* echo "<pre>";
         print_r($mytaskd);
        die; */
        
        ?>
      <!-- Content Wrapper. Contains page content -->
      <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <div class="content-header">
          <div class="container-fluid">
            <div class="row mb-2">
              <div class="col-sm-6">
                <h1 class="m-0"></h1>
              </div>
              <!-- /.col -->
              <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                  <h4></h4>
                </ol>
              </div>
              <!-- /.col -->
            </div>
            <!-- /.row -->
          </div>
          <!-- /.container-fluid -->
        </div>
        <!-- /.content-header -->
        <!-- Main content -->
        <section class="content">
        <div class="container-fluid">
        <?php if ($this->session->flashdata('pending_message')): ?>
              <div class="alert alert-success alert-dismissible">
            <button type="button" class="close" data-dismiss="alert">&times;</button>
            <?= $this->session->flashdata('pending_message'); ?>
          </div>
          <?php endif; ?>
          <?php if ($this->session->flashdata('success_message')): ?>
              <div class="alert alert-success alert-dismissible">
            <button type="button" class="close" data-dismiss="alert">&times;</button>
            <?= $this->session->flashdata('success_message'); ?>
          </div>
          <?php endif; ?>
            <?php
              if ($this->session->flashdata('error_message')): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
              <strong> <?php echo $this->session->flashdata('error_message'); ?></strong>
              <button type="button" class="close" data-dismiss="alert" aria-label="Close">
              <span aria-hidden="true">&times;</span>
              </button>
            </div>
          <?php endif; ?>
          <div class="row">
            <div class="col-12">
              <!-- Default box -->
              <!-- Main content -->
              <button class="btn btn-link" data-toggle="collapse" data-target="#maindata" aria-expanded="true" aria-controls="maindata">
              <i class="fas fa-bars"></i> Dashboard Data Analysis
              </button>
              <div id="maindata" class="collapse" aria-labelledby="maindata" data-parent="#accordion">
                <section class="content">
                  <div class="container-fluid">

<?php $this->load->view('AllLinks/AllPageLinks.php');?>
                  



















                  <?php /* 
                    <!-- Small boxes (Stat box) -->
                    <div class="row">
                      <div class="col-lg-3 col-md-6 col-sm-12">
                        <!-- small box -->
                        <div class="small-box bg-light text-secondary">
                          <div class="inner">
                            <center>
                              <h5>Team Detail</h5>
                            </center>
                            <hr>
                            <p><a href="BDDetail/<?=$admid?>">Total BD - <b><?=$day[0]->a?></b></p>
                            </a>
                            <hr>
                            <p><a href="BDDayDetail/<?=$tdate?>/1">Total BD Present - <b><?=$day[0]->b?></b></p>
                            </a>
                            <hr>
                            <p><a href="BDDayDetail/<?=$tdate?>/2">Total Work in Office - <b><?=$day[0]->c?></b></a><span style="font-size:10px;color:red;margin-left:20px" data-toggle="collapse" data-target="#collapsethree" aria-expanded="true" aria-controls="collapsethree">Read More</span></p>
                            <hr>
                            <div id="accordion">
                              <div class="icon">
                                <div id="collapsethree" class="collapse hide" aria-labelledby="headingOne" data-parent="#accordion">
                                  <div class="card-body">
                                    <p><a href="BDDayDetail/<?=$tdate?>/3">Total Work in Field - <b><?=$day[0]->d?></b></a></p>
                                    <hr>
                                    <p><a href="BDDayDetail/<?=$tdate?>/4">Total Work From Field+Office - <b><?=$day[0]->e?></b></a></p>
                                  </div>
                                </div>
                              </div>
                            </div>
                          </div>
                          <div class="icon">
                            <i class="ion ion-stats-bars"></i>
                          </div>
                          <a href="TeamDetail" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
                        </div>
                      </div>
                      <!-- ./col -->
                      <div class="col-lg-3 col-md-6 col-sm-12">
                        <!-- small box -->
                        <div class="small-box bg-light text-secondary">
                          <div class="inner">
                            <center>
                              <h5>Today's Team Task Detail</h5>
                            </center>
                            <hr>
                            <?php 
                              foreach($tttd as $tttd){?>
                            <p><a href="ATaskDetail/4/<?=$admid?>/1/<?=$tdate?>/<?=$tdate?>/0">Total Task Assign/Planned - <b><?=$tttd->a?></b></p>
                            </a>
                            <hr>
                            <p><a href="ATaskDetail/5/<?=$admid?>/1/<?=$tdate?>/<?=$tdate?>/0">Total Task Pending - <b><?=$tttd->b?></b></p>
                            </a>
                            <hr>
                            <p><a href="ATaskDetail/6/<?=$admid?>/1/<?=$tdate?>/<?=$tdate?>/0">Total Task Completed - <b><?=$tttd->c?></b>
                              <a style="font-size:10px;color:red;margin-left:20px" data-toggle="collapse" href="#collapse2" role="button" aria-expanded="false" aria-controls="collapse2">Read More</a>
                            </p>
                            <hr>
                            <div class="collapse" id="collapse2">
                              <a href="ATaskDetail/3/<?=$admid?>/1/<?=$tdate?>/<?=$tdate?>/0">
                                <p>
                                  Call Done- <b><?=$tttd->d-$tttd->e?></b>
                              </a>
                              <a href="ATaskDetail/3/<?=$admid?>/2/<?=$tdate?>/<?=$tdate?>/0"><p>Email Done- <b><?=$tttd->f-$tttd->g?></b></p></a>
                              <hr>
                              <a href="ATaskDetail/3/<?=$admid?>/3/<?=$tdate?>/<?=$tdate?>/0">
                                <p>Meeting Done- <b><?=$tttd->h-$tttd->i?></b></p>
                              </a>
                              <hr>
                              <a href="ATaskDetail/3/<?=$admid?>/4/<?=$tdate?>/<?=$tdate?>/0">
                                <p>Barg in Done- <b><?=$tttd->j-$tttd->k?></b></p>
                              </a>
                              <hr>
                              <a href="ATaskDetail/3/<?=$admid?>/5/<?=$tdate?>/<?=$tdate?>/0">
                                <p>Whatsapp Done- <b><?=$tttd->l-$tttd->m?></b></p>
                              </a>
                              <hr>
                              <a href="ATaskDetail/3/<?=$admid?>/6/<?=$tdate?>/<?=$tdate?>/0">
                                <p>MOM Done- <b><?=$tttd->n-$tttd->o?></b></p>
                              </a>
                              <hr>
                              <a href="ATaskDetail/3/<?=$admid?>/7/<?=$tdate?>/<?=$tdate?>/0">
                                <p>Proposal Done- <b><?=$tttd->p-$tttd->q?></b></p>
                              </a>
                              <hr>
                              <a href="ATaskDetail/7/<?=$admid?>/1/<?=$tdate?>/<?=$tdate?>/0">
                                <p>Action Taken Yes- <b><?=$tttd->r?></b></p>
                              </a>
                              <hr>
                              <a href="ATaskDetail/8/<?=$admid?>/1/<?=$tdate?>/<?=$tdate?>/0">
                                <p>Action Taken No- <b><?=$tttd->s?></b></p>
                              </a>
                              <hr>
                              <a href="ATaskDetail/9/<?=$admid?>/1/<?=$tdate?>/<?=$tdate?>/0">
                                <p>Purpose Achieved Yes- <b><?=$tttd->t?></b></p>
                              </a>
                              <hr>
                              <a href="ATaskDetail/10/<?=$admid?>/1/<?=$tdate?>/<?=$tdate?>/0">
                                <p>Purpose Achieved No- <b><?=$tttd->u?></b></p>
                              </a>
                              <?php }?>
                            </div>
                          </div>
                          <div class="icon">
                            <i class="ion ion-stats-bars"></i>
                          </div>
                          <a href="TeamWork" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
                        </div>
                      </div>
                      <!-- ./col -->
                      <div class="col-lg-3 col-md-6 col-sm-12">
                        <!-- small box -->
                        <div class="small-box bg-light text-secondary">
                          <div class="inner">
                            <?php $ttswwork = $this->Menu_model->new_ttsw($user['user_id'],$tdate,0);
                              foreach($ttswwork as $tw){?>
                            <center>
                              <h5>Today's Team Completed Task Against Status</h5>
                            </center>
                            <hr>
                            <p><a href="StatusTask/<?=$admid?>/<?=$tdate?>/1/0">Open - <b><?=$tw->a?></b></p>
                            </a>
                            <hr>
                            <p><a href="StatusTask/<?=$admid?>/<?=$tdate?>/8/0">Open [RPEM] - <b><?=$tw->b?></b></p>
                            </a>
                            <hr>
                            <p><a href="StatusTask/<?=$admid?>/<?=$tdate?>/2/0">Reachout - <b><?=$tw->c?></b><a style="font-size:10px;color:red;margin-left:20px" data-toggle="collapse" href="#collapse2" role="button" aria-expanded="false" aria-controls="collapse2">Read More</a></p>
                            <hr>
                            <div class="collapse" id="collapse2">
                              <p><a href="StatusTask/<?=$admid?>/<?=$tdate?>/3/0">Tentative - <b><?=$tw->d?></b></a></p>
                              <hr>
                              <p><a href="StatusTask/<?=$admid?>/<?=$tdate?>/4/0">Will-Do-Later - <b><?=$tw->e?></b></p>
                              </a>
                              <hr>
                              <p><a href="StatusTask/<?=$admid?>/<?=$tdate?>/5/0">Not-Interest - <b><?=$tw->f?></b></p>
                              </a>
                              <hr>
                              <p><a href="StatusTask/<?=$admid?>/<?=$tdate?>/10/0">TTD-Reachout - <b><?=$tw->j?></b></p>
                              </a>
                              <hr>
                              <p><a href="StatusTask/<?=$admid?>/<?=$tdate?>/11/0">WNO-Reachout - <b><?=$tw->k?></b></p>
                              </a>
                              <hr>
                              <p><a href="StatusTask/<?=$admid?>/<?=$tdate?>/6/0">Positive - <b><?=$tw->g?></b></p>
                              </a>
                              <hr>
                              <p><a href="StatusTask/<?=$admid?>/<?=$tdate?>/12/0">Positive NAP - <b><?=$tw->l?></b></p>
                              </a>
                              <hr>
                              <p><a href="StatusTask/<?=$admid?>/<?=$tdate?>/9/0">Very Positive - <b><?=$tw->i?></b></p>
                              </a>
                              <hr>
                              <p><a href="StatusTask/<?=$admid?>/<?=$tdate?>/13/0">Very Positive NAP - <b><?=$tw->m?></b></p>
                              </a>
                              <hr>
                              <p><a href="StatusTask/<?=$admid?>/<?=$tdate?>/7/0">Closure - <b><?=$tw->h?></b></p>
                              </a>
                              <hr>
                              <?php } ?>
                            </div>
                          </div>
                          <div class="icon">
                            <i class="ion ion-stats-bars"></i>
                          </div>
                          <a href="StatusTaskD" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
                        </div>
                      </div>
                      <!-- ./col -->
                      <div class="col-lg-3 col-md-6 col-sm-12">
                        <!-- small box -->
                        <div class="small-box bg-light text-secondary">
                          <div class="inner">
                            <?php
                            
                              $sca = $this->Menu_model->get_sconbyadmin($admid,$tdate);
                              $tdate = date('Y-m-d');
                              $or=0;$ort=0;$rr=0;$rt=0;$tp=0;$pvp=0;$vph=0;$other=0;
                              foreach($sca as $m){
                                $lsid = $m->status_id;
                                $csid = $m->nstatus_id;
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
                            <center>
                              <h5>Today's Team Status Conversion</h5>
                            </center>
                            <hr>
                            <p><a href="FinalSConversion/<?=$admid?>/<?=$tdate?>/1/8/0">Open To Open [RPEM] - <b><?=$or?></b></a></p>
                            <hr>
                            <p><a href="FinalSConversion/<?=$admid?>/<?=$tdate?>/8/2/0">Open [RPEM] to Reachout - <b><?=$ort?></b></a></p>
                            <hr>
                            <p><a href="FinalSConversion/<?=$admid?>/<?=$tdate?>/2/3/0">Reachout to Tentative - <b><?=$rr?></b>
                              <a style="font-size:10px;color:red;margin-left:20px" data-toggle="collapse" href="#collapse1" role="button" aria-expanded="false" aria-controls="collapse1">Read More</a>
                            </p>
                            <hr>
                            <div class="collapse" id="collapse1">
                              <p><a href="FinalSConversion/<?=$admid?>/<?=$tdate?>/3/6/0">Tentative to Positive - <b><?=$rt?></b></a></p>
                              <hr>
                              <p><a href="FinalSConversion/<?=$admid?>/<?=$tdate?>/6/9/0">Positive To Very Positive - <b><?=$tp?></b></a></p>
                              <hr>
                              <p><a href="FinalSConversion/<?=$admid?>/<?=$tdate?>/6/7/0">Positive to Closure - <b><?=$pvp?></b></a></p>
                              <hr>
                              <p><a href="FinalSConversion/<?=$admid?>/<?=$tdate?>/9/7/0">Very Positive To Closure - <b><?=$vph?></b></a></p>
                              <hr>
                              <p><a href="FinalSConversion/<?=$admid?>/<?=$tdate?>/0/0/0">Others - <b><?=$other?></b></a></p>
                            </div>
                          </div>
                          <div class="icon">
                            <i class="ion ion-person-add"></i>
                          </div>
                          <a href="SConversion" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
                        </div>
                      </div>
                      <!-- ./col -->
                    </div>





                    <div class="row">
                      <!-- ./col -->
                      <div class="col-lg-3 col-md-6 col-sm-12">
                        <!-- small box -->
                        <!-- small box -->
                        <div class="small-box bg-light text-secondary">
                          <div class="inner">
                            <center>
                              <h5>New Client Request</h5>
                            </center>
                            <hr>
                            <?php 
                              $cont=0;$pass=0;$tass=0;$ini=0;$pend=0;$close=0;
                              
                              foreach ($bd as $b){
                               
                                  $bdid = $b->user_id;
                                  $bdallrequest=$this->Menu_model->bdrequest($bdid);
                                  foreach($bdallrequest as $bdar){
                                      $cont = $cont + $bdar->cont;
                                      $pass = $pass + $bdar->pass;
                                      $ini = $ini + $bdar->ini;
                                      $pend = $pend + $bdar->pend;
                                      $close = $close + $bdar->close;
                                  }
                              }
                              ?>
                            <p><a href="TBDRequest/1">Total Request - <b><?=$cont;?></b></a></p>
                            <hr>
                            <p><a href="TotalRequest">Pending Apr - <b><?=$pass;?></b></a></p>
                            <hr>
                            <p><a href="TotalBDRequest/3">Total Apr - <b><?=$cont-$pass;?></b></a></p>
                            <hr>
                            <p><a href="TotalBDRequest/5">Total Request Pending - <b><?=$pend;?></b></a><br><a style="font-size:10px;color:red;margin-left:20px" data-toggle="collapse" href="#collapse6" role="button" aria-expanded="false" aria-controls="collapse6">Read More</a></p>
                            <hr>
                            <div class="collapse" id="collapse6">
                              <p><a href="TotalBDRequest/6">Total Request Completed - <b><?=$close;?></b></a></p>
                              <p><a href="CreateRequest">Create Request</a></p>
                            </div>
                          </div>
                          <div class="icon">
                            <i class="ion ion-person-add"></i>
                          </div>
                          <a href="#" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
                        </div>
                      </div>
                      <!-- ./col -->
                      <div class="col-lg-3 col-md-6 col-sm-12">
                        <!-- small box -->
                        <div class="small-box bg-light text-secondary">
                          <div class="inner">
                            <?php 
                              foreach($teamfu as $mc){
                              ?>
                            <center>
                              <h5>Total Funnel </h5>
                            </center>
                            <hr>
                            <p><a href="BDTOBDCTF"><b>Company TF BD to Other BD</b></a></p>
                            <hr>
                            <p><a href="BDFunnal/<?=$admid?>">All BD Funnel</p>
                            </a>
                            <hr>
                             <p><a href="UserWorkOnSelfAndOtherFunnel/<?=$uid?>"><b>CLUSTER WORK ON SELF AND OTHER FUNNEL</b></p></a>
                            <hr>
                            <p><a href="ProposalApr"><b>Proposal Approval</b>
                              <a style="font-size:10px;color:red;margin-left:20px" data-toggle="collapse" href="#collapse3" role="button" aria-expanded="false" aria-controls="collapse3">Read More</a>
                            </p>
                            <hr>
                           
                            <div class="collapse" id="collapse3">
                            <p><a href="AllProposalDetail"><b>Proposal Detail</b></p></a><hr>
                            <p><a href="ProposalDetailbydate"><b>Proposal by Date</b></p></a><hr>
                              <p><a href="BDNotWorkDD"><b>No Work B/W Date</b></p>
                              </a>
                              <hr>
                              <p><a href="BDWorkBWD"><b>Work B/W Date</b></p>
                              </a>
                              <hr>
                              <p><a href="companies/0">Total Companies - <b><?=$mc->a?></b></p>
                              </a>
                              <hr>
                              <p><a href="companies/1">Open - <b><?=$mc->b?></b></p>
                              </a>
                              <hr>
                              <p><a href="companies/8">Open [RPEM] - <b><?=$mc->i?></b></p>
                              </a>
                              <hr>
                              <p><a href="companies/2">Reachout - <b><?=$mc->c?></b></a>
                              <p><a href="companies/3">Tentative - <b><?=$mc->d?></b></p>
                              </a>
                              <hr>
                              <p><a href="companies/4">Will-Do-Later - <b><?=$mc->e?></b></p>
                              </a>
                              <hr>
                              <p><a href="companies/5">Not-Interest - <b><?=$mc->f?></b></p>
                              </a>
                              <hr>
                              <p><a href="companies/10">TTD-Reachout - <b><?=$mc->k?></b></p>
                              </a>
                              <hr>
                              <p><a href="companies/11">WNO-Reachout - <b><?=$mc->l?></b></p>
                              </a>
                              <hr>
                              <p><a href="companies/6">Positive - <b><?=$mc->g?></b></p>
                              </a>
                              <hr>
                              <p><a href="companies/9">Very Positive - <b><?=$mc->j?></b></p>
                              </a>
                              <hr>
                              <p><a href="companies/7">Closure - <b><?=$mc->h?></b></p>
                              </a>
                              <hr>
                              <p><a href="">Focus Funnel - <b><?=$mc->m?></b></p>
                              </a>
                              <hr>
                              <p><a href="">Upsell Client - <b><?=$mc->n?></b></p>
                              </a>
                              <hr>
                              <p><a href="">EX-BD Tf - <b><?=$mc->o?></b></p>
                              </a>
                              <hr>
                              <p><a href="companies/20">MP's/MLA - <b><?=$mc->p?></b></p>
                              </a>
                              <hr>
                              <p><a href="companies/21">Potential Partner BD - <b><?=$mc->q?></b></p>
                              </a>
                              <hr>
                              <p><a href="companies/25">Non Potential Partner BD - <b><?=$mc->u?></b></p>
                              </a>
                              <hr>
                              <p><a href="companies/26">Pending Potential Marking - <b><?=$mc->v?></b></p>
                              </a>
                              <hr>
                              <p><a href="companies/22">Potential Partner PST - <b><?=$mc->r?></b></p>
                              </a>
                              <hr>
                              <p><a href="companies/23">Potential Partner This QTR - <b><?=$mc->s?></b></p>
                              </a>
                              <hr>
                              <p><a href="companies/24">Potential Partner This FY - <b><?=$mc->t?></b></p>
                              </a>
                              <hr>
                              <?php } ?>
                              <p><a href="NewLead">Add New Lead</a></p>
                            </div>
                          </div>
                          <div class="icon">
                            <i class="ion ion-stats-bars"></i>
                          </div>
                          <a href="BDFunnal/<?=$admid?>" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
                        </div>
                      </div>
                      <!-- ./col -->
                      <div class="col-lg-3 col-md-6 col-sm-12">
                        <!-- small box -->
                        <div class="small-box bg-light text-secondary">
                          <div class="inner">
                            <?php 
                              $apstc=$this->Menu_model->get_apstcSC($admid);
                              foreach($apstc as $mc){
                              ?>
                            <center>
                              <h5>PST Total Funnel </h5>
                            </center>
                            <hr>
                            <p><a href="pstcompanies/0">Total Companies - <b><?=$mc->a?></b></p>
                            </a>
                            <hr>
                            <p><a href="pstcompanies/1">Open - <b><?=$mc->b?></b></p>
                            </a>
                            <hr>
                            <p><a href="pstcompanies/8">Open [RPEM] - <b><?=$mc->i?></b><a style="font-size:10px;color:red;margin-left:20px" data-toggle="collapse" href="#collapse3" role="button" aria-expanded="false" aria-controls="collapse3">Read More</a></p>
                            <hr>
                            <div class="collapse" id="collapse3">
                              <p><a href="pstcompanies/2">Reachout - <b><?=$mc->c?></b></p>
                              </a>
                              <hr>
                              <p><a href="pstcompanies/3">Tentative - <b><?=$mc->d?></b></p>
                              </a>
                              <hr>
                              <p><a href="pstcompanies/4">Will-Do-Later - <b><?=$mc->e?></b></p>
                              </a>
                              <hr>
                              <p><a href="pstcompanies/5">Not-Interest - <b><?=$mc->f?></b></p>
                              </a>
                              <hr>
                              <p><a href="pstcompanies/10">TTD-Reachout - <b><?=$mc->k?></b></p>
                              </a>
                              <hr>
                              <p><a href="pstcompanies/5">WNO-Reachout - <b><?=$mc->l?></b></p>
                              </a>
                              <hr>
                              <p><a href="pstcompanies/6">Positive - <b><?=$mc->g?></b></p>
                              </a>
                              <hr>
                              <p><a href="pstcompanies/9">Very Positive - <b><?=$mc->j?></b></p>
                              </a>
                              <hr>
                              <p><a href="pstcompanies/7">Closure - <b><?=$mc->h?></b></p>
                              </a>
                              <hr>
                              <p><a href="">Focus Funnel - <b><?=$mc->m?></b></p>
                              </a>
                              <hr>
                              <p><a href="">Upsell Client - <b><?=$mc->n?></b></p>
                              </a>
                              <hr>
                              <?php } ?>
                            </div>
                          </div>
                          <div class="icon">
                            <i class="ion ion-stats-bars"></i>
                          </div>
                          <a href="#" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
                        </div>
                      </div>
                      <div class="col-lg-3 col-md-6 col-sm-12">
                        <!-- small box -->
                        <div class="small-box bg-light text-secondary">
                          <div class="inner">
                            <center>
                              <h5>Team Bargin Meeting Detail</h5>
                            </center>
                            <hr>
                            <p><a href="PMTFTOBD"><b>Meeting TF to Other BD</b></a></p>
                            <hr>
                            <?php foreach($tbmeetd as $tmd){ ?>
                            <p><a href="plannerareport">Action Planner</b></a></p>
                            <hr>
                            <p><a href="plannerreport">Status Planner</b><a style="font-size:10px;color:red;margin-left:20px" data-toggle="collapse" href="#collapse5" role="button" aria-expanded="false" aria-controls="collapse5">Read More</a></p>
                            <hr>
                            <div class="collapse" id="collapse5">
                              <p><a href="momdetail">MOM Detail</b></a></p>
                              <hr>
                              <p><a href="TBMDF">Total RP Meeting</b></a></p>
                              <hr>
                              <!-- <p><a href="MeetingDetail/1/<?=$uid?>/<?=$tdate?>"><b>Meeting Detail</b></a></p><hr> -->

                               <p><a href="<?=base_url()?>Menu/MeetingDetailNew"><b>Meeting Detail</b></a></p><hr>

                              <p><a href="TBMD/1/<?=$uid?>/<?=$tdate?>/<?=$tdate?>">Total Bargin Plan - <b><?=$tmd->ab?></b></a></p>
                              <hr>
                              <p><a href="TBMD/2/<?=$uid?>/<?=$tdate?>/<?=$tdate?>">Not Started Bargin - <b><?=$tmd->a?></b></a></p>
                              <hr>
                              <p><a href="TBMD/2/<?=$uid?>/<?=$tdate?>/<?=$tdate?>">Started Bargin - <b><?=$tmd->i?></b></a></p>
                              <hr>
                              <p><a href="TBMD/3/<?=$uid?>/<?=$tdate?>/<?=$tdate?>">Not Close Bargin - <b><?=$tmd->b?></b></a></p>
                              <hr>
                              <p><a href="TBMD/3/<?=$uid?>/<?=$tdate?>/<?=$tdate?>">Close Bargin - <b><?=$tmd->b?></b></a></p>
                              <hr>
                              <p><a href="TBMD/3/<?=$uid?>/<?=$tdate?>/<?=$tdate?>">Lead not Updated - <b><?=$tmd->h?></b></a></p>
                              <hr>
                              <p><a href="TBMD/4/<?=$uid?>/<?=$tdate?>/<?=$tdate?>">Completed Bargin - <b><?=$tmd->c?></b></a></p>
                              <hr>
                              <p><a href="TBMD/5/<?=$uid?>/<?=$tdate?>/<?=$tdate?>">Total RP Meeting - <b><?=$tmd->d?></b></a></p>
                              <hr>
                              <p><a href="TBMD/6/<?=$uid?>/<?=$tdate?>/<?=$tdate?>">Total Non RP Meeting - <b><?=$tmd->e?></b></a></p>
                              <hr>
                              <p><a href="TBMD/7/<?=$uid?>/<?=$tdate?>/<?=$tdate?>">Total RP Priority - <b><?=$tmd->f?></b></a></p>
                              <hr>
                              <p><a href="TBMD/8/<?=$uid?>/<?=$tdate?>/<?=$tdate?>">Total RP Not Priority - <b><?=$tmd->g?></b></a></p>
                              <hr>
                              <p><a href="TBMD/9/<?=$uid?>/<?=$tdate?>/<?=$tdate?>">Total Only Got Detail - <b><?=$tmd->k?></b></a></p>
                              <hr>
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
                            <center>
                              <h5>Today's BD Task on PST Funnel</h5>
                            </center>
                            <hr>
                            <?php 
                              $bdtofpstf = $this->Menu_model->get_bdtofpstf($admid,$tdate);
                              foreach($bdtofpstf as $tttd){?>
                            <p><a href="#">Total Task Assign/Planned - <b><?=$tttd->a?></b></p>
                            </a>
                            <hr>
                            <p><a href="#">Total Task Pending - <b><?=$tttd->b?></b></p>
                            </a>
                            <hr>
                            <p><a href="#">Total Task Completed - <b><?=$tttd->c?></b><a style="font-size:10px;color:red;margin-left:20px" data-toggle="collapse" href="#collapse2" role="button" aria-expanded="false" aria-controls="collapse2">Read More</a></p>
                            <hr>
                            <div class="collapse" id="collapse2">
                              <a href="ATaskDetail/3/<?=$admid?>/1/<?=$tdate?>">
                                <p>
                                  Call Done- <b><?=$tttd->d-$tttd->e?></b>
                              </a>
                              <a href="ATaskDetail/3/<?=$admid?>/2/<?=$tdate?>"><p>Email Done- <b><?=$tttd->f-$tttd->g?></b></p></a>
                              <hr>
                              <a href="ATaskDetail/3/<?=$admid?>/3/<?=$tdate?>">
                                <p>Meeting Done- <b><?=$tttd->h-$tttd->i?></b></p>
                              </a>
                              <hr>
                              <a href="ATaskDetail/3/<?=$admid?>/4/<?=$tdate?>">
                                <p>Barg in Done- <b><?=$tttd->j-$tttd->k?></b></p>
                              </a>
                              <hr>
                              <a href="ATaskDetail/3/<?=$admid?>/5/<?=$tdate?>">
                                <p>Whatsapp Done- <b><?=$tttd->l-$tttd->m?></b></p>
                              </a>
                              <hr>
                              <a href="ATaskDetail/3/<?=$admid?>/6/<?=$tdate?>">
                                <p>MOM Done- <b><?=$tttd->n-$tttd->o?></b></p>
                              </a>
                              <hr>
                              <a href="ATaskDetail/3/<?=$admid?>/7/<?=$tdate?>">
                                <p>Proposal Done- <b><?=$tttd->p-$tttd->q?></b></p>
                              </a>
                              <hr>
                              <a href="#">
                                <p>Action Taken Yes- <b><?=$tttd->r?></b></p>
                              </a>
                              <hr>
                              <a href="#">
                                <p>Action Taken No- <b><?=$tttd->s?></b></p>
                              </a>
                              <hr>
                              <a href="#">
                                <p>Purpose Achieved Yes- <b><?=$tttd->t?></b></p>
                              </a>
                              <hr>
                              <a href="#">
                                <p>Purpose Achieved No- <b><?=$tttd->u?></b></p>
                              </a>
                              <?php }?>
                            </div>
                          </div>
                          <div class="icon">
                            <i class="ion ion-stats-bars"></i>
                          </div>
                          <a href="PSTData" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
                        </div>
                      </div>
                      <div class="col-lg-3 col-md-6 col-sm-12">
                        <div class="small-box bg-light text-secondary">
                          <div class="inner">
                            <center>
                              <h5>Today's MY Task Detail</h5>
                            </center>
                            <hr>
                            <?php 
                              foreach($mytaskd as $mytd){?>
                            <p><a href="<?=base_url();?>Menu/AMyTaskDetail/4/<?=$uid?>/1/<?=$tdate?>/<?=$tdate?>/0">Total Task Assign/Planned - <b><?=$mytd->a?></b></p>
                            </a>
                            <hr>
                            <p><a href="<?=base_url();?>Menu/AMyTaskDetail/5/<?=$uid?>/1/<?=$tdate?>/<?=$tdate?>/0">Total Task Pending - <b><?=$mytd->b?></b></p>
                            </a>
                            <hr>
                            <p><a href="<?=base_url();?>Menu/AMyTaskDetail/6/<?=$uid?>/1/<?=$tdate?>/<?=$tdate?>/0">Total Task Completed - <b><?=$mytd->c?></b></a><span style="font-size:10px;color:red;margin-left:20px" data-toggle="collapse" href="#collapse25" role="button" aria-expanded="false" aria-controls="collapse25">Read More</p>
                            <hr>
                            <div class="collapse" id="collapse25">
                              <a href="<?=base_url();?>Menu/AMyTaskDetail/3/<?=$uid?>/1/<?=$tdate?>/<?=$tdate?>/0">
                                <p>Call Done- <b><?=$mytd->d-$mytd->e?></b></p>
                              </a>
                              <hr>
                              <a href="<?=base_url();?>Menu/AMyTaskDetail/3/<?=$uid?>/2/<?=$tdate?>/<?=$tdate?>/0">
                                <p>Email Done- <b><?=$mytd->f-$mytd->g?></b></p>
                              </a>
                              <hr>
                              <a href="<?=base_url();?>Menu/AMyTaskDetail/7/<?=$uid?>/1/<?=$tdate?>/<?=$tdate?>/0">
                                <p>Action Taken Yes- <b><?=$mytd->r?></b></p>
                              </a>
                              <hr>
                              <a href="<?=base_url();?>Menu/AMyTaskDetail/8/<?=$uid?>/1/<?=$tdate?>/<?=$tdate?>/0">
                                <p>Action Taken No- <b><?=$mytd->s?></b></p>
                              </a>
                              <hr>
                              <a href="<?=base_url();?>Menu/AMyTaskDetail/9/<?=$uid?>/1/<?=$tdate?>/<?=$tdate?>/0">
                                <p>Purpose Achieved Yes- <b><?=$mytd->t?></b></p>
                              </a>
                              <hr>
                              <a href="<?=base_url();?>Menu/AMyTaskDetail/10/<?=$uid?>/1/<?=$tdate?>/<?=$tdate?>/0">
                                <p>Purpose Achieved No- <b><?=$mytd->u?></b></p>
                              </a>
                              <?php }?>
                            </div>
                          </div>
                          <div class="icon">
                            <i class="ion ion-stats-bars"></i>
                          </div>
                          <a href="MyWork" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
                        </div>
                      </div>
                      <!-- ./col -->
                      <div class="col-lg-3 col-md-6 col-sm-12">
                        <!-- small box -->
                      </div>
                      <!-- ./col -->
                    </div>

                    */ ?>
















                  </div>
              </div>
            </div>
            <div class="row">
            <div class="col-lg-8 col-sm">
            <div class="row">
            <div class="col-lg-12 col-sm">
            <div class="card card-primary card-outline card-outline-tabs">
            <h4 class="p-3">Today's Task Calendar</h4>
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
            <a class="nav-link" id="custom-tabs-four-call-tab" data-toggle="pill" href="#custom-tabs-four-call" role="tab" aria-controls="custom-tabs-four-call" aria-selected="false">
            Call <span class="badge badge-success"><?=$ttbyd[0]->a?></span>
            </a>
            </li>
            <li class="nav-item">
            <a class="nav-link" id="custom-tabs-four-email-tab" data-toggle="pill" href="#custom-tabs-four-email" role="tab" aria-controls="custom-tabs-four-email" aria-selected="false">
            Email <span class="badge badge-success"><?=$ttbyd[0]->b?></span>
            </a>
            </li>
            <li class="nav-item">
            <a class="nav-link" id="custom-tabs-four-meeting-tab" data-toggle="pill" href="#custom-tabs-four-meeting" role="tab" aria-controls="custom-tabs-four-meeting" aria-selected="false">
            Meeting <span class="badge badge-success"><?=$ttbyd[0]->c?></span>
            </a>
            </li>
            <li class="nav-item">
            <a class="nav-link" id="custom-tabs-four-whatsapp-tab" data-toggle="pill" href="#custom-tabs-four-whatsapp" role="tab" aria-controls="custom-tabs-whatsapp" aria-selected="false">
            WA<span class="badge badge-success"><?=$ttbyd[0]->e?></span>
            </a>
            </li>
            <li class="nav-item">
            <a class="nav-link" id="custom-tabs-four-review-tab" data-toggle="pill" href="#custom-tabs-four-review" role="tab" aria-controls="custom-tabs-review" aria-selected="false">
            Review<span class="badge badge-success"><?=$ttbyd[0]->f?></span>
            </a>
            </li>
            <li class="nav-item">
            <a class="nav-link" id="custom-tabs-four-proposal-tab" data-toggle="pill" href="#custom-tabs-four-proposal" role="tab" aria-controls="custom-tabs-proposal" aria-selected="false">
            Proposal<span class="badge badge-success"><?=$ttbyd[0]->g?></span>
            </a>
            </li>
            <li class="nav-item">
            <a class="nav-link" id="custom-tabs-four-barg-tab" data-toggle="pill" href="#custom-tabs-four-barg" role="tab" aria-controls="custom-tabs-four-barg" aria-selected="false">
            Visit Meeting <span class="badge badge-success"><?=$ttbyd[0]->d?></span>
            </a>
            </li>


            <!-- <li class="nav-item">
            <a class="nav-link" href="<?= base_url(); ?>Menu/TaskCheck_New" target="_blank">
            Team Task Check Management
            </a>
            </li> -->

             <li class="nav-item">
              <a class="nav-link" id="custom-tabs-four-taskcheck-tab" data-toggle="pill" href="#custom-tabs-four-taskcheck" role="tab" aria-controls="custom-tabs-taskcheck" aria-selected="false">
                <?php 
                $gettodaysmom = $this->Menu_model->geTodaysMOMCheckTask($uid,$tdate);
                $gettodaysmomcnt = sizeof($gettodaysmom);
                  ?>
              Task Check <span class="badge badge-success"><?=$gettodaysmomcnt;?></span>
              </a>
            </li>

             <li class="nav-item">
              <a class="nav-link" id="custom-tabs-four-bdrequest-tab" data-toggle="pill" href="#custom-tabs-four-bdrequest" role="tab" aria-controls="custom-tabs-four-call" aria-selected="false">
              Specials Task <span class="badge badge-success"><?=$ttbyd[0]->pmc?></span>
              </a>
            </li>


            <?php $sctaskcnts = $this->Menu_model->gettodaysAllSCTaskCount($uid,$tdate);
           // echo $this->db->last_query();
            ?>
            <?php foreach($sctaskcnts as $sctaskcnt):?>
            <li class="nav-item">
            <a class="nav-link" id="<?=$sctaskcnt->type_of_task;?>_task_tab" data-toggle="pill" href="#custom_<?=$sctaskcnt->type_of_task;?>_task" role="tab" aria-controls="<?= $sctaskcnt->type_of_task; ?>_task_tab" aria-selected="false">
            <?= $sctaskcnt->type_of_task; ?> <span class="badge badge-success"><?= $sctaskcnt->task_count;?></span>
            </a>
            </li>
            <?php endforeach; ?>
            </ul>
            </div>
            <div class="card-body">
            <div class="tab-content" id="custom-tabs-four-tabContent">
            <?php foreach($sctaskcnts as $sctaskcnt):?>
            <div class="tab-pane fade" id="custom_<?=$sctaskcnt->type_of_task;?>_task" role="tabpanel" aria-labelledby="<?=$sctaskcnt->type_of_task;?>_task_tab">
            <?php 
              $sctasklists        = $this->Menu_model->gettodaysAllSCTaskList($uid,$tdate);
              
              $sctasklistcnt     = sizeof($sctasklists);
              if($sctasklistcnt > 0){
               
                $slct_type_of_task = $sctaskcnt->type_of_task;
                
                ?>
            <ul class="list-group">
            <?php $i=1; foreach($sctasklists as $sctasklist){
              $type_of_task = $sctasklist->type_of_task;
              $appointment_datetime = $sctasklist->appointment_datetime;
              $sstid = $sctasklist->sc_task_id;

              $appointment_date     = date("Y-m-d", strtotime($appointment_datetime));
              $fwd_date             = $sctasklist->fwd_date;
              $fwd_dates             = date("Y-m-d", strtotime($fwd_date));

              if($appointment_date == $fwd_dates){
                $taskDate = "";
                $access_url = base_url().$sctasklist->access_url;
              }else{
                
                $taskDate = "- ( Task Date = ".$fwd_date.") ";
                if($sstid == 3 || $sstid == 8 || $sstid == 6){
                  $access_url = base_url().$sctasklist->access_url;
                }else{
                  $access_url = base_url()."Menu/SalesCoordinatorTaskAction";
                }
               
              }

              if($slct_type_of_task == $type_of_task){
                
                ?>
            <li class="list-group-item card sccardlist"> 
            <a class="scataglist" href="<?= $access_url;?>/<?=$sctasklist->id;?>">(<?= $i; ?>) - <?=$sctasklist->unique_kpa;?> - (<?=$appointment_datetime?>) <?=$taskDate?> (<?=$sctasklist->self_team;?>)</a>
            </li>
            <?php $i++; }
              }
              ?>
            </ul>
            <?php }?> 
            <!-- Lorem ipsum dolor sit amet consectetur adipisicing elit. A, rerum.<?=$sctaskcnt->type_of_task;?> -->
            </div>
            <?php endforeach; ?>
            <style>
            .scataglist{color: #ff00ba;font-size: 14px;}.sccardlist{padding: 5px; margin-bottom: 7px !important;}
            </style>






      <div class="tab-pane fade show active" id="custom-tabs-four-home" role="tabpanel" aria-labelledby="custom-tabs-four-home-tab">
                      <div id="accordion">
                            
                            <?php 
                              $timeSlotDatas = $this->Menu_model->get_timeslot();
                              $loop = 1001;
                              foreach($timeSlotDatas as $timeSlotData){

                                  if($timeSlotData->id == 6){continue;}

                                      $timeslot1  =  $timeSlotData->time1;
                                      $timeslot2  =  $timeSlotData->time2;
                                      $ttbytime   = $this->Menu_model->get_ttbytime($uid,$tdate,$timeslot1,$timeslot2);
                                      $ted        = $this->Menu_model->get_ttbytimed($uid,$tdate,$timeslot1,$timeslot2);

                                      $formattedTime1 = date("h:i A", strtotime($timeslot1));
                                      $formattedTime2 = date("h:i A", strtotime($timeslot2));

                                      if($loop == 1001){$collapseShow = 'show';}else{$collapseShow = '';}

                                      ?>
                                      <div class="card">
                                        <div class="card-header bg-info" id="headingOne" data-toggle="collapse" data-target="#collapse<?=$loop?>" aria-expanded="true" aria-controls="collapse<?=$loop?>">
                                          <b>
                                            <span style="color:#ff00bc">📝 Planned Task : </span>
                                            <span style="color:navy;">🕒 <?=$formattedTime1?> to <?=$formattedTime2?></span>
                                          </b>
                                          <hr>
                                          📊 Total Task: <?=$ted[0]->ab?> |
                                          📞 Call(<?=$ted[0]->a?>) |
                                          📧 Email(<?=$ted[0]->b?>) |
                                          💬 Whatsapp(<?=$ted[0]->e?>) |
                                          🏢 Meeting(<?=$ted[0]->c + $ted[0]->d?>) |
                                          🗂️ MOM(<?=$ted[0]->f?>) |
                                          📄 Proposal(<?=$ted[0]->g?>)
                                        </div>
                                        <div id="collapse<?=$loop?>" class="collapse" aria-labelledby="headingOne" data-parent="#accordion">
                                          <div class="card-body">
                                            <?php
                                            $taskLoop = 1;
                                            foreach($ttbytime as $tt){
                                              $task_name = $tt->task_name;
                                              $time = $tt->appointmentdatetime;
                                              $taskActionIds = $tt->actiontype_id;
                                              $time = date('h:i a', strtotime($time));

                                              if(in_array($taskActionIds, [1,2,5,6,7,10,12,13,14])){
                                                $r = rand(150, 255);
                                                $g = rand(150, 255);
                                                $b = rand(150, 255);
                                                $backgroundColor = "rgba($r, $g, $b,.2)";

                                                $hue = rand(0, 360);
                                                $saturation = rand(60, 100);
                                                $lightness = rand(30, 45);
                                                $backgroundColorNew = "hsl($hue, $saturation%, $lightness%)";
                                            ?>
                                            <div class="list-group-item list-group-item-action" style="background-color: <?php echo $backgroundColor; ?>; color: <?=$backgroundColorNew;?>!important">
                                              <button id="add_act<?=$loop?>" value="<?=$tt->id?>" class="text-left" style="background: none; color: inherit; border: none; padding: 0; font: inherit; cursor: pointer; outline: inherit;">
                                                <input type="hidden" value="<?=$tt->id?>" id="tid">
                                                <span class="flex">
                                                  🔹<?=$taskLoop?>). <b>🗒️ Task Name:</b> <?=$task_name?> | <strong>🏢 <?=$tt->compname?></strong><br>
                                                  <small class="text-muted"><b>⏰ Task Planned Time:</b> <?=$time?></small> |
                                                </span>
                                                <span class="p-3">📝 <?=$tt->name?></span>
                                                <span class="text-right"><i class="fa-solid fa-forward"></i></span>
                                              </button>
                                            </div>
                                            <?php } ?>
                                            <?php $taskLoop++; } ?>
                                          </div>
                                        </div>
                                      </div>
                                    <?php $loop++; } ?>


                                    <?php
                                      $pentask = sizeof($autotasktimenew);
                                      if($pentask > 0){
                                      $autoTaskStartTime = $autotasktimenew[0]->stime;
                                      $autoTaskEndTime   = $autotasktimenew[0]->etime;

                                      $pendingListAutoTasksLists       = $this->Menu_model->GetUserTotalPendingAutoTaskList($uid,$tdate,$autoTaskStartTime,$autoTaskEndTime);
                                      $pendingListAutoTasksCounts      = $this->Menu_model->GetUserTotalPendingAutoTaskCountList($uid,$tdate,$autoTaskStartTime,$autoTaskEndTime);
                                      
                                  ?>
                             <div class="card">
                                <div class="card-header bg-info" id="headingOne" data-toggle="collapse" data-target="#collapsePlannedAutotak" aria-expanded="true" aria-controls="collapsePlannedAutotak">
                                  <b>
                                    <span style="color:#ff00bc">🤖 Auto Planned Task:</span>
                                    <span style="color:navy;">🕒 <?=$autoTaskStartTime?> to <?=$autoTaskEndTime?></span>
                                  </b>
                                  <hr>
                                  📋 <strong>Total Pending Auto Task: </strong>
                                  <?php foreach ($pendingListAutoTasksCounts as $pendingListAutoTasksCount): 
                                      // echo '🔹 ';
                                      if ($pendingListAutoTasksCount->task_name == 'Call') {
                                          echo '📞 ';
                                      }else if ($pendingListAutoTasksCount->task_name == 'Email') {
                                          echo '📧 ';
                                      }

                                      echo $pendingListAutoTasksCount->task_name . ' (' . $pendingListAutoTasksCount->total_calls . ') | ';
                                  endforeach;  ?>
                                </div>

                                <div id="collapsePlannedAutotak" class="collapse" aria-labelledby="headingThree31" data-parent="#accordion">
                                  <div class="card-body">
                                    <?php $taskLoop = 1;
                                    foreach($pendingListAutoTasksLists as $tt){ 

                                      $task_name            = $tt->task_name;
                                      $time                 = $tt->appointmentdatetime;
                                      $taskActionIds        = $tt->actiontype_id;
                                      $time                 = date('h:i a', strtotime($time));

                                      $r = rand(150, 255);
                                      $g = rand(150, 255);
                                      $b = rand(150, 255);
                                      $backgroundColor = "rgba($r, $g, $b,.2)";

                                      $hue = rand(0, 360); // Full color wheel
                                      $saturation = rand(60, 100); // High saturation for rich colors
                                      $lightness = rand(30, 45); // Low lightness for a deeper tone
                                      
                                      $backgroundColorNew = "hsl($hue, $saturation%, $lightness%)";
                                      ?>

                                      <div class="list-group-item list-group-item-action" style="background-color: <?php echo $backgroundColor; ?>;color:<?=$backgroundColorNew;?>!important">
                                        <button id="add_act<?=$loop?>" value="<?=$tt->id?>" class="text-left" style="background: none;color: inherit;border: none;padding: 0;font: inherit;cursor: pointer;outline: inherit;">
                                          <input type="hidden" value="<?=$tt->id?>" id="tid">
                                            <span class="flex">
                                              🔹<?=$taskLoop?>).  
                                              <b>📌 Task Name:</b> <?=$task_name?> | 🏢 <strong><?=$tt->compname?></strong> 
                                              <br>
                                              <small class="text-muted">⏰ <b>Task Time:</b> - <?=$time?></small> | 
                                            </span>
                                            <span class="p-3">📝 <?=$tt->name?></span>
                                            <span class="text-right">➡️</span>
                                        </button>
                                      </div>
                                    <?php $taskLoop++; } ?>
                                  </div>
                                </div>
                              </div>
                            <?php } ?>
                        </div>
                  </div> 





            <?php /*
            <div class="tab-pane fade show active" id="custom-tabs-four-home" role="tabpanel" aria-labelledby="custom-tabs-four-home-tab">
            <div id="accordion">
            <div class="card">
            <div class="card-header bg-info" id="headingOne" data-toggle="collapse" data-target="#collapse0911" aria-expanded="true" aria-controls="collapse0911">
            <?php $ttbytime = $this->Menu_model->get_ttbytime($uid,$tdate,'09:00:00','11:00:00');
              $ted = $this->Menu_model->get_ttbytimed($uid,$tdate,'09:00:00','11:00:00');
              ?>
            <b>9:00 AM to 11:00 AM</b><br>
            Total Task <?=$ted[0]->ab?> | Call(<?=$ted[0]->a?>) | Email(<?=$ted[0]->b?>) | Whatsapp(<?=$ted[0]->e?>) | Meeting(<?=$ted[0]->c+$ted[0]->d?>) | Proposal(<?=$ted[0]->g?>)
            </div>
            <div id="collapse0911" class="collapse show" aria-labelledby="headingOne" data-parent="#accordion">
            <div class="card-body">
            <?php 
              foreach($ttbytime as $tt){
              $taid = $tt->actiontype_id;
              $taid=$this->Menu_model->get_action($taid);
              $time = $tt->appointmentdatetime;
              $time = date('h:i a', strtotime($time));
              ?>
            <div class="list-group-item list-group-item-action">
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
            </div>
            <?php } ?>
            </div>
            </div>
            </div>
            <div class="card">
            <div class="card-header bg-light" id="headingTwo" data-toggle="collapse" data-target="#collapse1113" aria-expanded="false" aria-controls="collapse1113">
            <?php $ttbytime = $this->Menu_model->get_ttbytime($uid,$tdate,'11:00:00','13:00:00');
              $ted = $this->Menu_model->get_ttbytimed($uid,$tdate,'11:00:00','13:00:00');
              ?>
            <b>11:00 AM to 01:00 PM</b><br>
            Total Task <?=$ted[0]->ab?> | Call(<?=$ted[0]->a?>) | Email(<?=$ted[0]->b?>) | Whatsapp(<?=$ted[0]->e?>) | Meeting(<?=$ted[0]->c+$ted[0]->d?>) | Proposal(<?=$ted[0]->g?>)
            </div>
            <div id="collapse1113" class="collapse" aria-labelledby="headingTwo" data-parent="#accordion">
            <div class="card-body">
            <?php 
              foreach($ttbytime as $tt){
              $taid = $tt->actiontype_id;
              $taid=$this->Menu_model->get_action($taid);
              $time = $tt->appointmentdatetime;
              $time = date('h:i a', strtotime($time));
              ?>
            <div class="list-group-item list-group-item-action">
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
            </div>
            <?php } ?>
            </div>
            </div>
            </div>
            <div class="card">
            <div class="card-header bg-info" id="headingThree" data-toggle="collapse" data-target="#collapse1315" aria-expanded="false" aria-controls="collapse1315">
            <?php $ttbytime = $this->Menu_model->get_ttbytime($uid,$tdate,'13:00:00','15:00:00');
              $ted = $this->Menu_model->get_ttbytimed($uid,$tdate,'13:00:00','15:00:00');
              ?>
            <b>01:00 PM to 03:00 PM</b><br>
            Total Task <?=$ted[0]->ab?> | Call(<?=$ted[0]->a?>) | Email(<?=$ted[0]->b?>) | Whatsapp(<?=$ted[0]->e?>) | Meeting(<?=$ted[0]->c+$ted[0]->d?>) | Proposal(<?=$ted[0]->g?>)
            </div>
            <div id="collapse1315" class="collapse" aria-labelledby="headingThree" data-parent="#accordion">
            <div class="card-body">
            <?php 
              foreach($ttbytime as $tt){
              $taid = $tt->actiontype_id;
              $taid=$this->Menu_model->get_action($taid);
              $time = $tt->appointmentdatetime;
              $time = date('h:i a', strtotime($time));
              ?>
            <div class="list-group-item list-group-item-action">
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
            </div>
            <?php } ?>
            </div>
            </div>
            </div>
            <div class="card">
            <div class="card-header bg-light" id="headingThree" data-toggle="collapse" data-target="#collapse1517" aria-expanded="false" aria-controls="collapse1517">
            <?php $ttbytime = $this->Menu_model->get_ttbytime($uid,$tdate,'15:00:00','17:00:00');
              $ted = $this->Menu_model->get_ttbytimed($uid,$tdate,'15:00:00','17:00:00');
              ?>
            <b>03:00 PM to 05:00 PM</b><br>
            Total Task <?=$ted[0]->ab?> | Call(<?=$ted[0]->a?>) | Email(<?=$ted[0]->b?>) | Whatsapp(<?=$ted[0]->e?>) | Meeting(<?=$ted[0]->c+$ted[0]->d?>) |  Proposal(<?=$ted[0]->g?>)
            </div>
            <div id="collapse1517" class="collapse" aria-labelledby="headingThree" data-parent="#accordion">
            <div class="card-body">
            <?php 
              foreach($ttbytime as $tt){
              $taid = $tt->actiontype_id;
              $taid=$this->Menu_model->get_action($taid);
              $time = $tt->appointmentdatetime;
              $time = date('h:i a', strtotime($time));
              ?>
            <div class="list-group-item list-group-item-action">
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
            </div>
            <?php } ?>
            </div>
            </div>
            </div>
            <div class="card">
            <div class="card-header bg-info" id="headingThree" data-toggle="collapse" data-target="#collapse1719" aria-expanded="false" aria-controls="collapse1719">
            <?php  $ttbytime = $this->Menu_model->get_ttbytime($uid,$tdate,'17:00:00','19:00:00');
              $ted = $this->Menu_model->get_ttbytimed($uid,$tdate,'17:00:00','19:00:00'); ?>
            <b>05:00 PM to 07:00 PM</b></br>
            Total Task <?=$ted[0]->ab?> | Call(<?=$ted[0]->a?>) | Email(<?=$ted[0]->b?>) | Whatsapp(<?=$ted[0]->e?>) | Meeting(<?=$ted[0]->c+$ted[0]->d?>) |  Proposal(<?=$ted[0]->g?>)
            </div>
            <div id="collapse1719" class="collapse" aria-labelledby="headingThree" data-parent="#accordion">
            <div class="card-body">
            <?php 
              foreach($ttbytime as $tt){
              $taid = $tt->actiontype_id;
              $taid=$this->Menu_model->get_action($taid);
              $time = $tt->appointmentdatetime;
              $time = date('h:i a', strtotime($time));
              ?>
            <div class="list-group-item list-group-item-action">
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
            </div>
            <?php } ?>
            </div>
            </div>
            </div>
            <div class="card">
            <div class="card-header bg-light" id="headingThree" data-toggle="collapse" data-target="#collapse9121" aria-expanded="false" aria-controls="collapse9121">
            <?php $ttbytime = $this->Menu_model->get_ttbytime($uid,$tdate,'19:00:00','21:00:00'); 
              $ted = $this->Menu_model->get_ttbytimed($uid,$tdate,'19:00:00','21:00:00');
              ?>
            <b>19:00 PM to 21:00 PM</b><br>
            Total Task <?=$ted[0]->ab?> | Call(<?=$ted[0]->a?>) | Email(<?=$ted[0]->b?>) | Whatsapp(<?=$ted[0]->e?>) | Meeting(<?=$ted[0]->c+$ted[0]->d?>) |  Proposal(<?=$ted[0]->g?>)
            </div>
            <div id="collapse9121" class="collapse" aria-labelledby="headingThree" data-parent="#accordion">
            <div class="card-body">
            <?php 
              foreach($ttbytime as $tt){
              $taid = $tt->actiontype_id;
              $taid=$this->Menu_model->get_action($taid);
              $time = $tt->appointmentdatetime;
              $time = date('h:i a', strtotime($time));
              ?>
            <div class="list-group-item list-group-item-action">
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
            </div>
            <?php } ?>
            </div>
            </div>
            </div>
            </div>
            </div>


              */ ?>
















            
            <div class="tab-pane fade" id="custom-tabs-four-call" role="tabpanel" aria-labelledby="custom-tabs-four-call-tab">
            <?php $aai=0;foreach($totalt as $tt){if($tt->plan==1){if($tt->actiontype_id=='1'){
              $taid = $tt->actiontype_id;
              $taid=$this->Menu_model->get_action($taid);
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
            </div></button>



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



     <div class="tab-pane fade" id="custom-tabs-four-taskcheck" role="tabpanel" aria-labelledby="custom-tabs-four-taskcheck-tab">
                  <div class="card-header text-center bg-light" style="border-radius:unset" >
            <p>
              <?php 
            
                $groupedByActionTypes = [];
                
                // if($uid == 100182 ){
                //     echo "<pre>";
                //     var_dump($gettodaysmom);
                // }
                
                foreach ( $gettodaysmom as $objects) {
                  $actionTypeId = $objects->actiontype_id;
                  if (!isset($groupedByActionTypes[$actionTypeId])) {
                      $groupedByActionTypes[$actionTypeId] = [];
                  }
                  $groupedByActionTypes[$actionTypeId][] = $objects;
                }
            
            foreach ($groupedByActionTypes as $key => $getchk){
                  $taid=$this->Menu_model->get_actionbyid($key);
                  $checkname = $taid[0]->name;
                  $getSize = sizeof($getchk); ?>
                <a class="btn btn-primary checkingreport" data-toggle="collapse" href="#collapseCheck<?=$key?>" role="button" aria-expanded="false" aria-controls="collapseCheck<?=$key?>">
                  <?= $checkname.' ('.$getSize.')'; ?>
                </a>
          <?php } ?>
          <?php
             foreach ($groupedByActionTypes as $key => $checkTasks) { ?>
              <div class="collapse multi-collapse"  id="collapseCheck<?=$key?>">
              <div class="card card-body">
              <?php 
              foreach ($checkTasks as $checkTask) {
                  $ce_tskid = $checkTask->id;
                  $tskid = $checkTask->actiontype_id;
                  $reviewtype = $checkTask->reviewtype;
                  $taid=$this->Menu_model->get_actionbyid($tskid);

                  $time = $checkTask->appointmentdatetime;
                  $time = date('h:i a', strtotime($time));
                 
                  if($tskid == 18){
                    $reqmom = $this->Menu_model->getRequestMOMBYID($reviewtype);
                    $reqmom_user = $reqmom[0]->user_id;
                    $reqmom_uname = $this->Menu_model->get_userbyid($reqmom_user)[0]->name;
                  }
                  if($tskid == 19){
                    $reqmom_uname = '';
                  }
              ?>
              <a href="<?= base_url(); ?>Management/MomDataCheck/<?=$reviewtype?>/<?=$ce_tskid?>">
                  <div class="list-group-item list-group-item-action checkrepoData">
                      <span class="flex-wrap">
                          <strong class="text-secondary mr-1" style="font-size: 14px;" ><?=$checkTask->compname?> - ( <?= $checkname; ?> )</strong><br>
                          <small class="text-secondary mr-1"> Request Name - <?=$reqmom_uname?></small><br>
                          <small class="text-muted">Check Time:- <?=$time?></small>
                      </span>
                  </div>
              </a>
               <?php  } ?>
               </div>
              </div>
              <?php }  ?>
            </div>
            <hr>
            <style>
              .checkingreport{
                margin: 4px;
              }
              .checkrepoData{
                margin: 4px;
                background: beige;
                color: white !important;
              }
            </style>
                  </div>




    <div class="tab-pane fade" id="custom-tabs-four-bdrequest" role="tabpanel" aria-labelledby="custom-tabs-four-bdrequest-tab">
                      <?php $aai=0;
                    
                      foreach($totalt as $tt){if($tt->plan==1){if($tt->actiontype_id=='19' || $tt->actiontype_id=='20' || $tt->actiontype_id=='21' || $tt->actiontype_id=='22'){
                          $taid=$this->Menu_model->get_actionbyid($tt->actiontype_id);
                       
                          $time = $tt->appointmentdatetime;
                          $initiateddtime = $tt->initiateddt;
                          $remarks = $tt->remarks;
                          $time = date('h:i a', strtotime($time));
                          if($remarks == 'Create BD Request'){
                            $backgroundclr      = "style='background: #b8ffc7;'";
                            $atag               = base_url()."Menu/CreateRequestWithTaskID/".$tt->id;

                            $bdreData           = $this->Menu_model->specialbdrequesttaskbyTID($tt->id);
                            $bdr_client_id      = $bdreData[0]->client_id;
                            $bdr_request_id     = $bdreData[0]->request_id;
                            $bdrClientData      = $this->Menu_model->GetSpecialBdrequestTypeByID($bdr_client_id);
                            $bdrClientDataname  = $bdrClientData[0]->type;
                            $bdrrequestData     = $this->Menu_model->GetSpecialBdrequestByID($bdr_request_id);
                            $bdrrequestDataName = $bdrrequestData[0]->request_type;
                            $bdrmessage         = $bdrClientDataname.' | '.$bdrrequestDataName;

                            $reqCmpData         = $this->Menu_model->get_cmpbyinid($tt->cid_id);
                            $cmpidid            = $reqCmpData[0]->cmpid_id;
                            
                          }elseif($remarks == "Submit Handover"){
                            $backgroundclr  = "style='background: #efef55;'";
                            $reqCmpData     = $this->Menu_model->get_cmpbyinid($tt->cid_id);
                            $cmpidid        = $reqCmpData[0]->cmpid_id;
                            $atag           = base_url()."Menu/Handover_New/$cmpidid/".$tt->id;
                            $bdrmessage     = '';
                          }elseif($remarks == "Praposal Check"){
                            $backgroundclr  = "style='background: #00b4fc52'";
                            $reqCmpData     = $this->Menu_model->get_cmpbyinid($tt->cid_id);
                            $cmpidid        = $reqCmpData[0]->cmpid_id;
                            $aftertask      = $tt->aftertask;
                            $praposalData   = $this->Menu_model->GetPraposalByTID($aftertask);
                            $praposaluser   = $praposalData[0]->prauser;
                            $praposal_id    = $praposalData[0]->id;
                            $atag           = base_url()."Menu/PraposalCheckByManager/$praposal_id/".$tt->id;
                            $bdrmessage     = 'Praposal Send By : '.$praposaluser;
                           
                          }

                          if($initiateddtime == ''){
                            $initiateddtime ='';
                            $starttask = "Start";
                          }else{
                            $starttask = 'Close';
                            $initiateddtime = ' | Task initial Time:-'.$initiateddtime;
                          }
                      ?>
                        <div class="list-group-item list-group-item-action m-1" <?= $backgroundclr; ?> >
                          <button value="<?=$tt->id?>" style="background: none;color: inherit;border: none;padding: 0;font: inherit;cursor: pointer;outline: inherit;">
                          <input type="hidden" value="<?=$tt->id?>" id="tid">
                           <span class="mr-3 align-items-center">
                              <i class="fa-solid fa-circle"></i>
                           </span>
                           <span class="flex"><?=$taid[0]->name?> |
                               <strong class="text-secondary mr-1"><?= $tt->compname; ?></strong><br>
                               <small class="text-muted">Task Time:- <?=$time?></small> 
                               <small class="text-muted"><?=$initiateddtime?></small>
                            </span>
                            <span class="p-3" style="color:<?=$tt->color?>;"><?=$tt->name?>
                            </span>
                            <span class="text-right">
                              <a href="<?=$atag; ?>">
                               <small><?= $starttask; ?></small> <i class="fa-solid fa-forward"></i>
                              </a>
                            </span> <br>
                            <span class="text-center">
                               <small><?= $bdrmessage; ?></small>
                            </span>
                        </button>
                      </div>
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
            <div class="tab-pane fade" id="custom-tabs-four-review" role="tabpanel" aria-labelledby="custom-tabs-four-review-tab">
            <?php $pr = $this->Menu_model->get_pstreview($uid);
              foreach($pr as $pr){?>
            <a href="BDUFunnel/<?=$pr->bdid?>"><div class="list-group-item list-group-item-action">
            <span class="mr-3 align-items-center">
            <i class="fa-solid fa-circle"></i>
            </span>
            <span class="flex">
            <strong class="text-secondary mr-1"><?=$pr->meetinglink?></strong><br>
            <small class="text-muted">BD Name :- <?=$pr->bdid?></small>
            <small class="text-muted">Date Time :- <?=$pr->sdatet?></small>
            </span>
            <span class="text-right">
            <i class="fa-solid fa-forward"></i>
            </span>
            </div></a>
            <?php } ?>
            </div>
            <div class="tab-pane fade" id="custom-tabs-four-barg" role="tabpanel" aria-labelledby="custom-tabs-four-barg-tab">
            <div class="list-group-item list-group-item-action">
            <?php 
              foreach($barg as $brg){
                  $bs = $brg->status;?>
            <?php if($bs=='Pending'){?>
            <button type="button" value="<?=$brg->id?>" class="btn btn-success" id="startm">Start Meeting</button>
            <?php }if($bs=='Start'){?>
            <button type="button" value="<?=$brg->id?>" class="btn btn-info" id="closem">Close Meeting</button>
            <?php }} ?>
            </div>
            <div class="list-group-item list-group-item-action">
            <?php foreach($barg as $br){if($br->status=='RPClose'){;?>
            <a href="BMNewLead/<?=$br->id?>"><?=$br->company_name?><button type="button" class="btn btn-danger">Create New Lead</button></a>
            <?php }} ?>
            </div>
            </div>
            </div>
            </div>
            <!-- /.card -->
            </div><!-- /.card -->
            </div>
            <div class="col-lg-12 col-sm">
            <div class="card card-primary card-outline card-outline-tabs">
            <h4 class="p-3">Today's Task Completed</h4>
            <div class="card-header p-0 border-bottom-0">
            <ul class="nav nav-tabs" id="custom-tabs-four-tab" role="tablist">
            <li class="nav-item">
            <?php $ttbydc = $this->Menu_model->get_ttbydc($uid,$tdate);
              ?>
            <a class="nav-link active" id="custom-tabs-four-homea-tab" data-toggle="pill" href="#custom-tabs-four-homea" role="tab" aria-controls="custom-tabs-four-homea" aria-selected="true">
            All <span class="badge badge-success"><?=$ttbydc[0]->ab?></span>
            </a>
            </li>
            <li class="nav-item">
            <a class="nav-link" id="custom-tabs-four-calla-tab" data-toggle="pill" href="#custom-tabs-four-calla" role="tab" aria-controls="custom-tabs-four-calla" aria-selected="false">
            Call <span class="badge badge-success"><?=$ttbydc[0]->a?></span>
            </a>
            </li>
            <li class="nav-item">
            <a class="nav-link" id="custom-tabs-four-emaila-tab" data-toggle="pill" href="#custom-tabs-four-emaila" role="tab" aria-controls="custom-tabs-four-emaila" aria-selected="false">
            Email <span class="badge badge-success"><?=$ttbydc[0]->b?></span>
            </a>
            </li>
            <li class="nav-item">
            <a class="nav-link" id="custom-tabs-four-meetinga-tab" data-toggle="pill" href="#custom-tabs-four-meetinga" role="tab" aria-controls="custom-tabs-four-meetinga" aria-selected="false">
            Meeting <span class="badge badge-success"><?=$ttbydc[0]->c?></span>
            </a>
            </li>
            <li class="nav-item">
            <a class="nav-link" id="custom-tabs-four-whatsappa-tab" data-toggle="pill" href="#custom-tabs-four-whatsappa" role="tab" aria-controls="custom-tabs-whatsappa" aria-selected="false">
            WA<span class="badge badge-success"><?=$ttbydc[0]->e?></span>
            </a>
            </li>
            <li class="nav-item">
            <a class="nav-link" id="custom-tabs-four-reviewa-tab" data-toggle="pill" href="#custom-tabs-four-reviewa" role="tab" aria-controls="custom-tabs-reviewa" aria-selected="false">
            Review<span class="badge badge-success"><?=$ttbydc[0]->f?></span>
            </a>
            </li>
            <li class="nav-item">
            <a class="nav-link" id="custom-tabs-four-proposala-tab" data-toggle="pill" href="#custom-tabs-four-proposala" role="tab" aria-controls="custom-tabs-proposala" aria-selected="false">
            Proposal<span class="badge badge-success"><?=$ttbydc[0]->g?></span>
            </a>
            </li>
            <li class="nav-item">
            <a class="nav-link" id="custom-tabs-four-barg-taba" data-toggle="pill" href="#custom-tabs-four-barga" role="tab" aria-controls="custom-tabs-four-barga" aria-selected="false">
            Visit Meeting <span class="badge badge-success"><?=$ttbydc[0]->d?></span>
            </a>
            </li>
            </ul>
            </div>
            <div class="card-body">
            <div class="tab-content" id="custom-tabs-four-tabContent">
            <div class="tab-pane fade show active" id="custom-tabs-four-homea" role="tabpanel" aria-labelledby="custom-tabs-four-homea-tab">
            <div id="accordion">
            <div class="card">
            <div class="card-header bg-info" id="headingOne" data-toggle="collapse" data-target="#collapse0911" aria-expanded="true" aria-controls="collapse0911">
            <?php $ttbytime = $this->Menu_model->get_ttbytimec($uid,$tdate,'09:00:00','11:00:00');
              $ted = $this->Menu_model->get_ttbytimedc($uid,$tdate,'09:00:00','11:00:00');
              ?>
            <b>9:00 AM to 11:00 AM</b><br>
            Total Task <?=$ted[0]->ab?> | Call(<?=$ted[0]->a?>) | Email(<?=$ted[0]->b?>) | Whatsapp(<?=$ted[0]->e?>) | Meeting(<?=$ted[0]->c+$ted[0]->d?>) |  Proposal(<?=$ted[0]->g?>)
            </div>
            <div id="collapse0911" class="collapse show" aria-labelledby="headingOne" data-parent="#accordion">
            <div class="card-body">
            <?php 
              foreach($ttbytime as $tt){
              $taid = $tt->actiontype_id;
              $taid=$this->Menu_model->get_action($taid);
              $time = $tt->appointmentdatetime;
              $time = date('h:i a', strtotime($time));
              ?>
            <div class="list-group-item list-group-item-action">
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
            </div>
            <?php } ?>
            </div>
            </div>
            </div>
            <div class="card">
            <div class="card-header bg-light" id="headingTwo" data-toggle="collapse" data-target="#collapse1113" aria-expanded="false" aria-controls="collapse1113">
            <?php $ttbytime = $this->Menu_model->get_ttbytimec($uid,$tdate,'11:00:00','13:00:00');
              $ted = $this->Menu_model->get_ttbytimedc($uid,$tdate,'11:00:00','13:00:00');
              ?>
            <b>11:00 AM to 01:00 PM</b><br>
            Total Task <?=$ted[0]->ab?> | Call(<?=$ted[0]->a?>) | Email(<?=$ted[0]->b?>) | Whatsapp(<?=$ted[0]->e?>) | Meeting(<?=$ted[0]->c+$ted[0]->d?>) |  Proposal(<?=$ted[0]->g?>)
            </div>
            <div id="collapse1113" class="collapse" aria-labelledby="headingTwo" data-parent="#accordion">
            <div class="card-body">
            <?php 
              foreach($ttbytime as $tt){
              $taid = $tt->actiontype_id;
              $taid=$this->Menu_model->get_action($taid);
              $time = $tt->appointmentdatetime;
              $time = date('h:i a', strtotime($time));
              ?>
            <div class="list-group-item list-group-item-action">
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
            </div>
            <?php } ?>
            </div>
            </div>
            </div>
            <div class="card">
            <div class="card-header bg-info" id="headingThree" data-toggle="collapse" data-target="#collapse1315" aria-expanded="false" aria-controls="collapse1315">
            <?php $ttbytime = $this->Menu_model->get_ttbytimec($uid,$tdate,'13:00:00','15:00:00');
              $ted = $this->Menu_model->get_ttbytimedc($uid,$tdate,'13:00:00','15:00:00');
              ?>
            <b>01:00 PM to 03:00 PM</b><br>
            Total Task <?=$ted[0]->ab?> | Call(<?=$ted[0]->a?>) | Email(<?=$ted[0]->b?>) | Whatsapp(<?=$ted[0]->e?>) | Meeting(<?=$ted[0]->c+$ted[0]->d?>) |  Proposal(<?=$ted[0]->g?>)
            </div>
            <div id="collapse1315" class="collapse" aria-labelledby="headingThree" data-parent="#accordion">
            <div class="card-body">
            <?php 
              foreach($ttbytime as $tt){
              $taid = $tt->actiontype_id;
              $taid=$this->Menu_model->get_action($taid);
              $time = $tt->appointmentdatetime;
              $time = date('h:i a', strtotime($time));
              ?>
            <div class="list-group-item list-group-item-action">
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
            </div>
            <?php } ?>
            </div>
            </div>
            </div>
            <div class="card">
            <div class="card-header bg-light" id="headingThree" data-toggle="collapse" data-target="#collapse1517" aria-expanded="false" aria-controls="collapse1517">
            <?php $ttbytime = $this->Menu_model->get_ttbytimec($uid,$tdate,'15:00:00','17:00:00');
              $ted = $this->Menu_model->get_ttbytimedc($uid,$tdate,'15:00:00','17:00:00');
              ?>
            <b>03:00 PM to 05:00 PM</b><br>
            Total Task <?=$ted[0]->ab?> | Call(<?=$ted[0]->a?>) | Email(<?=$ted[0]->b?>) | Whatsapp(<?=$ted[0]->e?>) | Meeting(<?=$ted[0]->c+$ted[0]->d?>) |  Proposal(<?=$ted[0]->g?>)
            </div>
            <div id="collapse1517" class="collapse" aria-labelledby="headingThree" data-parent="#accordion">
            <div class="card-body">
            <?php 
              foreach($ttbytime as $tt){
              $taid = $tt->actiontype_id;
              $taid=$this->Menu_model->get_action($taid);
              $time = $tt->appointmentdatetime;
              $time = date('h:i a', strtotime($time));
              ?>
            <div class="list-group-item list-group-item-action">
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
            </div>
            <?php } ?>
            </div>
            </div>
            </div>
            <div class="card">
            <div class="card-header bg-info" id="headingThree" data-toggle="collapse" data-target="#collapse1719" aria-expanded="false" aria-controls="collapse1719">
            <?php $ttbytime = $this->Menu_model->get_ttbytimec($uid,$tdate,'17:00:00','19:00:00');
              $ted = $this->Menu_model->get_ttbytimedc($uid,$tdate,'17:00:00','19:00:00'); ?>
            <b>05:00 PM to 07:00 PM</b></br>
            Total Task <?=$ted[0]->ab?> | Call(<?=$ted[0]->a?>) | Email(<?=$ted[0]->b?>) | Whatsapp(<?=$ted[0]->e?>) | Meeting(<?=$ted[0]->c+$ted[0]->d?>) |  Proposal(<?=$ted[0]->g?>)
            </div>
            <div id="collapse1719" class="collapse" aria-labelledby="headingThree" data-parent="#accordion">
            <div class="card-body">
            <?php 
              foreach($ttbytime as $tt){
              $taid = $tt->actiontype_id;
              $taid=$this->Menu_model->get_action($taid);
              $time = $tt->appointmentdatetime;
              $time = date('h:i a', strtotime($time));
              ?>
            <div class="list-group-item list-group-item-action">
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
            </div>
            <?php } ?>
            </div>
            </div>
            </div>
            <div class="card">
            <div class="card-header bg-light" id="headingThree" data-toggle="collapse" data-target="#collapse9121" aria-expanded="false" aria-controls="collapse9121">
            <?php $ttbytime = $this->Menu_model->get_ttbytimec($uid,$tdate,'19:00:00','21:00:00'); 
              $ted = $this->Menu_model->get_ttbytimedc($uid,$tdate,'19:00:00','21:00:00');
              ?>
            <b>19:00 PM to 21:00 PM</b><br>
            Total Task <?=$ted[0]->ab?> | Call(<?=$ted[0]->a?>) | Email(<?=$ted[0]->b?>) | Whatsapp(<?=$ted[0]->e?>) | Meeting(<?=$ted[0]->c+$ted[0]->d?>) |  Proposal(<?=$ted[0]->g?>)
            </div>
            <div id="collapse9121" class="collapse" aria-labelledby="headingThree" data-parent="#accordion">
            <div class="card-body">
            <?php 
              foreach($ttbytime as $tt){
              $taid = $tt->actiontype_id;
              $taid=$this->Menu_model->get_action($taid);
              $time = $tt->appointmentdatetime;
              $time = date('h:i a', strtotime($time));
              ?>
            <div class="list-group-item list-group-item-action">
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
            </div>
            <?php } ?>
            </div>
            </div>
            </div>
            </div>
            </div>
            <div class="tab-pane fade" id="custom-tabs-four-calla" role="tabpanel" aria-labelledby="custom-tabs-four-calla-tab">
            <?php foreach($ttdone as $tt){if($tt->plan==1){if($tt->actiontype_id=='1'){
              $taid = $tt->actiontype_id;
              $taid=$this->Menu_model->get_action($taid);
              $time = $tt->appointmentdatetime;
              $time = date('h:i a', strtotime($time));
              ?>
            <div class="list-group-item list-group-item-action">
            <button id="comp_task<?=$aai?>" value="<?=$tt->id?>" style="background: none;color: inherit;border: none;padding: 0;font: inherit;cursor: pointer;outline: inherit;">
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
            </div></button>
            <?php $aai++;}}} ?>
            </div>
            <div class="tab-pane fade" id="custom-tabs-four-emaila" role="tabpanel" aria-labelledby="custom-tabs-four-emaila-tab">
            <?php foreach($ttdone as $tt){if($tt->plan==1){if($tt->actiontype_id=='2'){
              ?>
            <a href="CompanyDetails/<?=$tt->cid?>/?tid=<?=$tt->id?>"><div class="list-group-item list-group-item-action">
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
            </div></a>
            <?php }}} ?>
            </div>
            <div class="tab-pane fade" id="custom-tabs-four-meetinga" role="tabpanel" aria-labelledby="custom-tabs-four-meetinga-tab">
            <?php foreach($ttdone as $tt){if($tt->plan==1){if($tt->actiontype_id=='3'){
              ?>
            <a href="CompanyDetails/<?=$tt->cid?>/?tid=<?=$tt->id?>"><div class="list-group-item list-group-item-action">
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
            </div></a>
            <?php }}} ?>
            </div>
            <div class="tab-pane fade" id="custom-tabs-four-whatsappa" role="tabpanel" aria-labelledby="custom-tabs-four-whatsappa-tab">
            <?php foreach($ttdone as $tt){if($tt->plan==1){if($tt->actiontype_id=='5'){
              ?>
            <a href="CompanyDetails/<?=$tt->cid?>/?tid=<?=$tt->id?>"><div class="list-group-item list-group-item-action">
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
            </div></a>
            <?php }}} ?>
            </div>
            <div class="tab-pane fade" id="custom-tabs-four-barga" role="tabpanel" aria-labelledby="custom-tabs-four-barga-tab">
            </div>
            </div>
            </div>
            </div></div></div></div>
            <div class="col-lg-4 col-sm">
            <div class="card p-3">
            <?php 
              $user_day_planner  = $this->Menu_model->get_daystarted($uid,date("Y-m-d"));
              $pinitiate_time = $user_day_planner[0]->planner_initiate_time;
              $textmessage = $pinitiate_time == '' ? "Start" : "Resume";
              ?>
            <div class="card">
            <div class="card-header bg-primary" id="start_planning1" data-toggle="collapse" data-target="#start_planning2" aria-expanded="false" aria-controls="collapse9121">
            <b><?= $textmessage; ?> Planning </b>   
            </div>
            <div id="start_planning2" class="collapse show" aria-labelledby="start_planning1" data-parent="#accordion">
            <div class="card-body">
            <div class="list-group-item list-group-item-action ">
            <center>
            <button type="button" class="btn btn-success font-weight-bold" style="padding:6px 70px;" onclick="handleReminderCreation()">
            <?= $textmessage; ?>  <i class="fa-solid fa-forward"></i>
            </button>
            </center>
            </div>
            </div>
            </div>
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
    <script>
      window.onload = function () {
       
      var chart = new CanvasJS.Chart("chartContainer", {
      	animationEnabled: true,
      	theme: "light1",
      	title:{
      		text: "Goals of This Month"
      	},
      	axisY:{
      		includeZero: true
      	},
      	legend:{
      		cursor: "pointer",
      		verticalAlign: "center",
      		horizontalAlign: "right",
      		itemclick: toggleDataSeries
      	},
      	data: [{
      		type: "column",
      		name: "Real Trees",
      		indexLabel: "{y}",
      		yValueFormatString: "$#0.##",
      		showInLegend: true,
      		dataPoints: <?php echo json_encode($dataPoints1, JSON_NUMERIC_CHECK); ?>
      	},{
      		type: "column",
      		name: "Artificial Trees",
      		indexLabel: "{y}",
      		yValueFormatString: "$#0.##",
      		showInLegend: true,
      		dataPoints: <?php echo json_encode($dataPoints2, JSON_NUMERIC_CHECK); ?>
      	}]
      });
      chart.render();
       
      function toggleDataSeries(e){
      	if (typeof(e.dataSeries.visible) === "undefined" || e.dataSeries.visible) {
      		e.dataSeries.visible = false;
      	}
      	else{
      		e.dataSeries.visible = true;
      	}
      	chart.render();
      }
       
      }
    </script>
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
      function handleReminderCreation() {
            
            $.ajax({
            url:'<?=base_url();?>Menu/CheckTaskPlanningTime',
            type: "POST",
            data: {
              'checkplantime': 'checkplantime'
            },
            cache: false,
            success: function a(result){
             
              if(result =='false'){
                var redURL = "<?=base_url();?>Menu/TaskPlanner2/<?= date("Y-m-d") ?>";
                window.location.href = redURL;
              }else if(result =='true'){
              
                <?php 
        $todaydate = new DateTime();
        $todaydate->modify('+1 day');
        $tomorrowDate = $todaydate->format('Y-m-d');
        ?>
                  var redURL = "<?=base_url();?>Menu/TaskPlanner2/<?= $tomorrowDate; ?>";
                  window.location.href = redURL;
              }
            }
            });
      }
    </script>
    <script>
			$(document).ready(function() {
			    $('.reviewbtn').click(function() {
			        var reviewId  = $(this).attr('value');
			        var startt    = '<?= date('Y-m-d H:i:s') ?>';  
			        $.ajax({
			            url: '<?= base_url('Menu/startreview') ?>',
			            method: 'POST',
			            data: {
			                startt: startt,
			                reviewid: reviewId 
			            },
			            success: function(response) {
                    if(response =='Start'){
                       alert("✅ Review Started Successfully");
                        window.location.href = '<?= base_url('Menu/AllReviewPlaing') ?>';
                    }else if(response =='Allready Start'){
                       alert("✅ You have already started a review. Please close it first.");
                        window.location.href = '<?= base_url('Menu/AllReviewPlaing') ?>';
                    }else{
                      alert(response);
                    }
			            }
			        });
			    });
			});
		</script>
    <script>
      $("#example1").DataTable({
        "responsive": true, "lengthChange": false, "autoWidth": false
      }).buttons().container().appendTo('#example1_wrapper .col-md-6:eq(0)');
      
      $("#example2").DataTable({
        "responsive": true, "lengthChange": false, "autoWidth": false
      }).buttons().container().appendTo('#example1_wrapper .col-md-6:eq(0)');
    </script>
  </body>
</html>
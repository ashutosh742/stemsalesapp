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

table {
  width: 90%;
}

  </style>
  

  
</head>

<?php 
$bd = $this->Menu_model->get_userbyaid($uid);
$day = $this->Menu_model->get_daydbyad($uid,$tdate);
$tttd = $this->Menu_model->get_tteamtd($uid,$tdate);
$tbmeetd = $this->Menu_model->get_tbmeetdbyaid($uid,$tdate);
$teamfu = $this->Menu_model->get_mbdcbyaid($uid);
?>
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

    <!-- Main content -->
    <section class="content">
      <div class="container-fluid">
        <div class="row">
            <center><h1 class="m-2">Daily Report (<?=$tdate?>)</h1></center>
            <div class="col-12 m-3">
            <center><h5>Team Detail</h5></center><hr>
            <table class="table table-striped table-bordered" cellspacing="0" width="100%">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Total BD</th>
                        <th>Total BD Present</th>
                        <th>Total Work in Office</th>
                        <th>Total Work in Field</th>
                        <th>Total Work From Field+Office</th>
                    </tr>
                </thead>
                <tbody>
                <tr>
                    <td><?=$tdate?></td>
                    <td><?=$day[0]->a?></a></td>
                    <td><?=$day[0]->b?></a></td>
                    <td><?=$day[0]->c?></a></td>
                    <td><?=$day[0]->d?></a></td>
                    <td><?=$day[0]->e?></a></td>
                </tr>
              </tbody>
            </table>
            <table class="table table-striped table-bordered" cellspacing="0" width="100%">
                <thead>
                    <tr>
                        <th>S.No</th>
                        <th>BD Name</th>
                        <th>Started Day @</th>
                        <th>Close Day @</th>
                        <th>Start to Close Time Difference</th>
                    </tr>
                </thead>
                <tbody>
                  <?php 
                  $i=1;
                  $mdata = $this->Menu_model->get_BDdaydbyad($uid,$tdate,'1');
                    foreach($mdata as $dt){
                        $start=$dt->start;
                        $close=$dt->close;
                    ?>
                <tr>
                     <td><?=$i?></td>
                     <td><?=$dt->bdname?></td>
                     <td><?=date('h:i A', strtotime($start));?></td>
                     <td><?php if($close){echo date('h:i A', strtotime($close));}?></td>
                     <td><?php if($close){echo $this->Menu_model->timediff($start,$close);}?></td>
                </tr>
                 <?php $i++;} ?>
              </tbody>
            </table>
            </div>
            
            <div class="col-12 m-3">
            <center><h5>Team Team Task Funnal</h5></center><hr>
            <div class="">
                    <div class="">
                            <table class="table table-striped table-bordered" cellspacing="0" width="100%">
                            <thead>
                                <tr>
                                    <th>S.No</th>
                                    <th>BD Name</th>
                                    <th>Total Companies</th>
                                    <th>Open</th>
                                    <th>Open [RPEM]</th>
                                    <th>Reachout</th>
                                    <th>Tentative</th>
                                    <th>Will-Do-Later</th>
                                    <th>Not-Interest</th>
                                    <th>TTD-Reachout</th>
                                    <th>WNO-Reachout</th>
                                    <th>Positive</th>
                                    <th>Very Positive</th>
                                    <th>Closure</th>
                                </tr>
                            </thead>
                            <tbody>
                              <?php 
                              $i=1;
                                $mdata = $this->Menu_model->get_userbyaid($uid);
                                foreach($mdata as $dt){
                                    $bdid = $dt->user_id;
                                    $mbdc=$this->Menu_model->get_mbdc($bdid);
                                    foreach($mbdc as $mc){
                                ?>
                            <tr>
                                <td><?=$i?></td>
                                <td><?=$dt->name?></td>
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
                             </tr>
                             <?php $i++;}} ?>
                          </tbody>
                        </table>
                    </div>
                </div>
                </div>
            
            <div class="col-12 m-3">
            <center><h5>Team Task Detail</h5></center><hr>
            <div class="">
                <div class="">
            <b>note: A (Assign/Planned) / C (Completed) / P (Pending)</b>
            <table class="table table-striped table-bordered" cellspacing="0" width="100%">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Total Task Assign/Planned</th>
                        <th>Total Task Pending</th>
                        <th>Total Task Completed</th>
                        <th>Call<br/>A/C/P (Action Y/N)(Purpose Y/N)</th>
                        <th>Email A/C/P<br/>A/C/P (Action Y/N)(Purpose Y/N)</th>
                        <th>Scheduled Meeting A/C/P<br/>A/C/P (Action Y/N)(Purpose Y/N)</th>
                        <th>Barg in Meeting A/C/P<br/>A/C/P (Action Y/N)(Purpose Y/N)</th>
                        <th>Whatsapp A/C/P<br/>A/C/P (Action Y/N)(Purpose Y/N)</th>
                        <th>MOM A/C/P<br/>A/C/P (Action Y/N)(Purpose Y/N)</th>
                        <th>Proposal A/C/P<br/>A/C/P (Action Y/N)(Purpose Y/N)</th>
                        <th>Action Taken Yes/No</th>
                        <th>Purpose Achieved Yes/No</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                        $tttd = $this->Menu_model->new_tteamtd($uid,$tdate);
                        foreach($tttd as $ttd){?>
                <tr>
                    <td><?=$tdate?></td>
                    <td><?=$ttd->a?></td>
                    <td><?=$ttd->b?></td>
                    <td><?=$ttd->c?></td>
                    <td><?=$ttd->d?>/<?=$ttd->d-$ttd->e?>/<?=$ttd->e?></br>(<?=$ttd->d1?>/<?=$ttd->d2?>) (<?=$ttd->d3?>/<?=$ttd->d4?>)</td>
                    <td><?=$ttd->f?>/<?=$ttd->f-$ttd->g?>/<?=$ttd->g?></br>(<?=$ttd->f1?>/<?=$ttd->f2?>) (<?=$ttd->f3?>/<?=$ttd->f4?>)</td>
                    <td><?=$ttd->h?>/<?=$ttd->h-$ttd->i?>/<?=$ttd->i?></br>(<?=$ttd->h1?>/<?=$ttd->h2?>) (<?=$ttd->h3?>/<?=$ttd->h4?>)</td>
                    <td><?=$ttd->j?>/<?=$ttd->j-$ttd->k?>/<?=$ttd->k?></br>(<?=$ttd->j1?>/<?=$ttd->j2?>) (<?=$ttd->j3?>/<?=$ttd->j4?>)</td>
                    <td><?=$ttd->l?>/<?=$ttd->l-$ttd->m?>/<?=$ttd->m?></br>(<?=$ttd->l1?>/<?=$ttd->l2?>) (<?=$ttd->l3?>/<?=$ttd->l4?>)</td>
                    <td><?=$ttd->n?>/<?=$ttd->n-$ttd->o?>/<?=$ttd->o?></br>(<?=$ttd->n1?>/<?=$ttd->n2?>) (<?=$ttd->n3?>/<?=$ttd->n4?>)</td>
                    <td><?=$ttd->p?>/<?=$ttd->p-$ttd->q?>/<?=$ttd->q?></br>(<?=$ttd->p1?>/<?=$ttd->p2?>) (<?=$ttd->p3?>/<?=$ttd->p4?>)</td>
                    <td><?=$ttd->r?>/<?=$ttd->s?></td>
                    <td><?=$ttd->t?>/<?=$ttd->u?></td>
                </tr>
                <?php } ?>  
              </tbody>
            </table>
            </div></div>
            
            
            <div class="">
                <div class="">
                        <table class="table table-striped table-bordered" cellspacing="0" width="100%">
                        <thead>
                            <tr>
                                <th>BD Name</th>
                                <th>Total Task Assign/Planned</th>
                                <th>Total Task Pending</th>
                                <th>Total Task Completed</th>
                                <th>Call A/C/P<br/>A/C/P (Action Y/N)(Purpose Y/N)</th>
                                <th>Email A/C/P<br/>A/C/P (Action Y/N)(Purpose Y/N)</th>
                                <th>Scheduled Meeting A/C/P<br/>A/C/P (Action Y/N)(Purpose Y/N)</th>
                                <th>Barg in Meeting A/C/P<br/>A/C/P (Action Y/N)(Purpose Y/N)</th>
                                <th>Whatsapp A/C/P<br/>A/C/P (Action Y/N)(Purpose Y/N)</th>
                                <th>MOM A/C/P<br/>A/C/P (Action Y/N)(Purpose Y/N)</th>
                                <th>Proposal A/C/P<br/>A/C/P (Action Y/N)(Purpose Y/N)</th>
                                <th>Action Taken Yes/No</th>
                                <th>Purpose Achieved Yes/No</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            $bd = $this->Menu_model->get_userbyaid($uid);
                            foreach($bd as $bd){
                            $bdname = $bd->name;
                            $bdid = $bd->user_id;
                            $bdtd = $this->Menu_model->new_totaltd($bdid,$tdate);
                            foreach($bdtd as $ttd){
                            if($ttd->a!=0){
                            ?>
                        <tr>
                            <td><?=$bdname?></td>
                            <td><?=$ttd->a?></td>
                            <td><?=$ttd->b?></td>
                            <td><?=$ttd->c?></td>
                            <td><?=$ttd->d?>/<?=$ttd->d-$ttd->e?>/<?=$ttd->e?></br>(<?=$ttd->d1?>/<?=$ttd->d2?>) (<?=$ttd->d3?>/<?=$ttd->d4?>)</td>
                            <td><?=$ttd->f?>/<?=$ttd->f-$ttd->g?>/<?=$ttd->g?></br>(<?=$ttd->f1?>/<?=$ttd->f2?>) (<?=$ttd->f3?>/<?=$ttd->f4?>)</td>
                            <td><?=$ttd->h?>/<?=$ttd->h-$ttd->i?>/<?=$ttd->i?></br>(<?=$ttd->h1?>/<?=$ttd->h2?>) (<?=$ttd->h3?>/<?=$ttd->h4?>)</td>
                            <td><?=$ttd->j?>/<?=$ttd->j-$ttd->k?>/<?=$ttd->k?></br>(<?=$ttd->j1?>/<?=$ttd->j2?>) (<?=$ttd->j3?>/<?=$ttd->j4?>)</td>
                            <td><?=$ttd->l?>/<?=$ttd->l-$ttd->m?>/<?=$ttd->m?></br>(<?=$ttd->l1?>/<?=$ttd->l2?>) (<?=$ttd->l3?>/<?=$ttd->l4?>)</td>
                            <td><?=$ttd->n?>/<?=$ttd->n-$ttd->o?>/<?=$ttd->o?></br>(<?=$ttd->n1?>/<?=$ttd->n2?>) (<?=$ttd->n3?>/<?=$ttd->n4?>)</td>
                            <td><?=$ttd->p?>/<?=$ttd->p-$ttd->q?>/<?=$ttd->q?></br>(<?=$ttd->p1?>/<?=$ttd->p2?>) (<?=$ttd->p3?>/<?=$ttd->p4?>)</td>
                            <td><?=$ttd->r?>/<?=$ttd->s?></td>
                            <td><?=$ttd->t?>/<?=$ttd->u?></td>
                        </tr>
                        <?php }}} ?>  
                      </tbody>
                    </table> 
                </div>
            </div>
            
            
            
            <div class="">
                <div class="">
                        <table class="table table-striped table-bordered" cellspacing="0" width="100%">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>09:00 AM to 11:00 AM</th>
                                <th>11:00 AM to 01:00 PM</th>
                                <th>01:00 PM to 03:00 PM</th>
                                <th>03:00 PM to 05:00 PM</th>
                                <th>05:00 PM to 07:00 PM</th>
                                <th>07:00 PM to 09:00 PM</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $bd = $this->Menu_model->get_userbyaid($uid);
                            foreach($bd as $bd){
                            $bdname = $bd->name;
                            $bdid = $bd->user_id;
                              $ted1 = $this->Menu_model->get_ttbytimedc($bdid,$tdate,'09:00:00','11:00:00');
                              $ted2 = $this->Menu_model->get_ttbytimedc($bdid,$tdate,'11:00:00','13:00:00'); 
                              $ted3 = $this->Menu_model->get_ttbytimedc($bdid,$tdate,'13:00:00','15:00:00');
                              $ted4 = $this->Menu_model->get_ttbytimedc($bdid,$tdate,'15:00:00','17:00:00');
                              $ted5 = $this->Menu_model->get_ttbytimedc($bdid,$tdate,'17:00:00','19:00:00');
                              $ted6 = $this->Menu_model->get_ttbytimedc($bdid,$tdate,'19:00:00','21:00:00');
                            ?>
                        <tr>
                            <td><?=$bdname?></td>
                            <td>Call(<?=$ted1[0]->a?>) | Email(<?=$ted1[0]->b?>) | Whatsapp(<?=$ted1[0]->e?>) | Meeting(<?=$ted1[0]->c+$ted1[0]->d?>) | Whatsapp(<?=$ted1[0]->e?>) | MOM(<?=$ted1[0]->f?>) | Proposal(<?=$ted1[0]->g?>)</td>
                            <td>Call(<?=$ted2[0]->a?>) | Email(<?=$ted2[0]->b?>) | Whatsapp(<?=$ted2[0]->e?>) | Meeting(<?=$ted2[0]->c+$ted2[0]->d?>) | Whatsapp(<?=$ted2[0]->e?>) | MOM(<?=$ted2[0]->f?>) |   Proposal(<?=$ted2[0]->g?>)</td>
                            <td>Call(<?=$ted3[0]->a?>) | Email(<?=$ted3[0]->b?>) | Whatsapp(<?=$ted4[0]->e?>) | Meeting(<?=$ted3[0]->c+$ted3[0]->d?>) | Whatsapp(<?=$ted3[0]->e?>) | MOM(<?=$ted3[0]->f?>) |   Proposal(<?=$ted3[0]->g?>)</td>
                            <td>Call(<?=$ted4[0]->a?>) | Email(<?=$ted3[0]->b?>) | Whatsapp(<?=$ted3[0]->e?>) | Meeting(<?=$ted3[0]->c+$ted3[0]->d?>) | Whatsapp(<?=$ted4[0]->e?>) | MOM(<?=$ted4[0]->f?>) |   Proposal(<?=$ted3[0]->g?>)</td>
                            <td>Call(<?=$ted5[0]->a?>) | Email(<?=$ted3[0]->b?>) | Whatsapp(<?=$ted3[0]->e?>) | Meeting(<?=$ted3[0]->c+$ted3[0]->d?>) | Whatsapp(<?=$ted5[0]->e?>) | MOM(<?=$ted5[0]->f?>) |   Proposal(<?=$ted3[0]->g?>)</td>
                            <td>Call(<?=$ted6[0]->a?>) | Email(<?=$ted3[0]->b?>) | Whatsapp(<?=$ted3[0]->e?>) | Meeting(<?=$ted3[0]->c+$ted3[0]->d?>) | Whatsapp(<?=$ted6[0]->e?>) | MOM(<?=$ted6[0]->f?>) |   Proposal(<?=$ted3[0]->g?>)</td>
                        </tr> 
                        <?php }?>
                      </tbody>
                    </table> 
                </div>
            </div>
            
            
            
            
            
            
            
            
            
            
            
            <div class="">
                <div class="">
                    <center><b class="m-3">Completed Task Detail</b></center>
                        <table class="table table-striped table-bordered" cellspacing="0" width="100%">
                        <thead>
                            <tr>
                                <th>S.No.</th>
                                <th>BD Name</th>
                                <th>Company Name</th>
                                <th>Plan Time</th>
                                <th>Initiated Time</th>
                                <th>Plan and Initiated Time Diff</th>
                                <th>Completed Time</th>
                                <th>Plan and Completed Time Diff</th>
                                <th>Initiated and Completed Time Diff</th>
                                <th>Task Activity</th>
                                <th>Action/Purpose</th>
                                <th>Last_Status</th>
                                <th>Current_Status</th>
                                <th>Task Remarks</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            $i=1;
                            $mtdata = $this->Menu_model->get_alltaskdbyad('6','0',$uid,$tdate,'1');
                                foreach($mtdata as $md){
                                $bd = $md->user_id;
                                $bdname = $this->Menu_model->get_userbyid($bd);
                                $tid = $md->id;
                                $inid  = $md->cid_id;
                                $inid = $this->Menu_model->get_initbyid($inid);
                                $mtd = $this->Menu_model->get_ccitblall($tid);
                                $ltime = $mtd[0]->last_task_date;
                                $ctime = $mtd[0]->appointmentdatetime;
                                $ntime = $mtd[0]->next_task_date;
                                $lsid = $mtd[0]->status_id;
                                $csid = $mtd[0]->nstatus_id;
                                $s1 = $this->Menu_model->get_statusbyid($lsid);
                                if($s1){$s1=$s1[0]->name;}else{$s1='';}
                                $s2 = $this->Menu_model->get_statusbyid($csid);
                                if($s2){$s2=$s2[0]->name;}else{$s2='';}
                                if($ntime==''){$tdiff='';}else{$tdiff = $this->Menu_model->timediff($ctime,$ntime);}
                                $nltime = date('d-m-Y  h:i A', strtotime($ltime));
                                $nctime = date('d-m-Y  h:i A', strtotime($ctime));
                                $nntime = date('d-m-Y  h:i A', strtotime($ntime));
                                $pltime = $md->appointmentdatetime;
                                $intime = $md->initiateddt;
                            ?>
                        <tr>
                            <td><?=$i?></td>
                            <td><?=$bdname[0]->name?></td>
                            <td><?=$mtd[0]->compname?></td>
                            <td><?php if($pltime){echo date('d-m-Y h:i A', strtotime($pltime));}?></td>
                            <td><?php if($intime){ echo date('d-m-Y h:i A', strtotime($intime));}?></td>
                            <td><?php if($intime){ echo $this->Menu_model->timediff($pltime,$intime);}?></td>
                            <td><?=date('d-m-Y h:i A', strtotime($uptime = $md->updateddate));?></td>
                            <td><?=$this->Menu_model->timediff($pltime,$uptime)?></td>
                            <td><?php if($intime){ echo $this->Menu_model->timediff($intime,$uptime);}?></td>
                            <td><?=$mtd[0]->current_action_type?></td>
                            <td><?=$mtd[0]->actontaken?>/<?=$mtd[0]->purpose_achieved?></td>
                            <td><?=$s1?></td>
                            <td><?=$s2?></td>
                            <td><?=$mtd[0]->remarks?></td>
                        <?php $i++;} ?>
                      </tbody>
                    </table> 
                </div>
            </div>
            <div class="">
                <div class="">
                    <center><b class="m-3">Pending Task Detail</b></center>
                        <table class="table table-striped table-bordered" cellspacing="0" width="100%">
                        <thead>
                            <tr>
                                <th>S.No.</th>
                                <th>BD Name</th>
                                <th>Company Name</th>
                                <th>Plan Time</th>
                                <th>Initiated Time</th>
                                <th>Plan and Initiated Time Diff</th>
                                <th>Task Activity</th>
                                <th>Last_Status</th>
                                <th>Current_Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            $i=1;
                            $mtdata = $this->Menu_model->get_alltaskdbyad('5','0',$uid,$tdate,'1');
                                foreach($mtdata as $md){
                                $bd = $md->user_id;
                                $bdname = $this->Menu_model->get_userbyid($bd);
                                $tid = $md->id;
                                $inid  = $md->cid_id;
                                $inid = $this->Menu_model->get_initbyid($inid);
                                $mtd = $this->Menu_model->get_ccitblall($tid);
                                $ltime = $mtd[0]->last_task_date;
                                $ctime = $mtd[0]->appointmentdatetime;
                                $ntime = $mtd[0]->next_task_date;
                                $lsid = $mtd[0]->status_id;
                                $csid = $mtd[0]->nstatus_id;
                                $s1 = $this->Menu_model->get_statusbyid($lsid);
                                if($s1){$s1=$s1[0]->name;}else{$s1='';}
                                $s2 = $this->Menu_model->get_statusbyid($csid);
                                if($s2){$s2=$s2[0]->name;}else{$s2='';}
                                if($ntime==''){$tdiff='';}else{$tdiff = $this->Menu_model->timediff($ctime,$ntime);}
                                $nltime = date('d-m-Y  h:i A', strtotime($ltime));
                                $nctime = date('d-m-Y  h:i A', strtotime($ctime));
                                $nntime = date('d-m-Y  h:i A', strtotime($ntime));
                                $pltime = $md->appointmentdatetime;
                                $intime = $md->initiateddt;
                            ?>
                        <tr>
                            <td><?=$i?></td>
                            <td><?=$bdname[0]->name?></td>
                            <td><?=$mtd[0]->compname?></td>
                            <td><?php if($pltime){echo date('d-m-Y h:i A', strtotime($pltime));}?></td>
                            <td><?php if($intime){ echo date('d-m-Y h:i A', strtotime($intime));}?></td>
                            <td><?php if($intime){ echo $this->Menu_model->timediff($pltime,$intime);}?></td>
                            <td><?=$mtd[0]->current_action_type?></td>
                            <td><?=$s1?></td>
                            <td><?=$s2?></td>
                        <?php $i++;} ?>
                      </tbody>
                    </table> 
                </div>
            </div>
            
            </div>
            <div class="col-12 m-3">
            <center><h5>Team Completed Task Against Status</h5></center><hr>
             <div class="">
                <div class="">
                    <div class="pdf-viwer">
                        <table class="table table-striped table-bordered" cellspacing="0" width="100%">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Open</th>
                                <th>Open [RPEM]</th>
                                <th>Reachout</th>
                                <th>Tentative</th>
                                <th>Will-Do-Later</th>
                                <th>Not-Interest</th>
                                <th>TTD-Reachout</th>
                                <th>WNO-Reachout </th>
                                <th>Positive</th>
                                <th>Positive NAP</th>
                                <th>Very Positive</th>
                                <th>Very Positive NAP</th>
                                <th>Closure</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                                $tttd = $this->Menu_model->new_ttsw($uid,$tdate,0);
                                foreach($tttd as $tw){?>
                        <tr>
                            <td><?=$tdate?></td>                  
                            <td><?=$tw->a?></td>
                            <td><?=$tw->b?></td>
                            <td><?=$tw->c?></td>
                            <td><?=$tw->d?></td>
                            <td><?=$tw->e?></td>
                            <td><?=$tw->f?></td>
                            <td><?=$tw->j?></td>
                            <td><?=$tw->k?></td>
                            <td><?=$tw->g?></td>
                            <td><?=$tw->l?></td>
                            <td><?=$tw->i?></td>
                            <td><?=$tw->m?></td>
                            <td><?=$tw->h?></td>
                        </tr>
                        <?php } ?>  
                      </tbody>
                    </table> 
                </div>
            </div>
            </div>
            </div>
            <div class="col-12 m-3">
            <center><h5>Team Status Conversion</h5></center><hr>
            
            <div class="">
                <div class="">
                    <div class="pdf-viwer">
                        <table class="table table-striped table-bordered" cellspacing="0" width="100%">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Open To Open [RPEM]</th>
                                <th>Open To Tentative</th>
                                <th>Open [RPEM] to Reachout</th>
                                <th>Reachout to Tentative</th>
                                <th>Tentative to Positive</th>
                                <th>Positive To Very Positive</th>
                                <th>Positive to Closure</th>
                                <th>Very Positive To Closure</th>
                                <th>Others</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                                $or=0;$ort=0;$rr=0;$rt=0;$tp=0;$pvp=0;$vph=0;$other=0;$opt=0;$ot=0;
                                $sc=$this->Menu_model->new_scon($uid,$tdate,0);
                                foreach($sc as $m){
                                    $lsid = $m->status_id;
                                    $csid = $m->nstatus_id;
                                    if($lsid!=$csid){
                                    if($lsid==1 && $csid ==8){$or++;}
                                        elseif($lsid==1 && $csid ==3){$ot++;}
                                        elseif($lsid==8 && $csid ==2){$ort++;}
                                        elseif($lsid==2 && $csid ==3){$rr++;}
                                        elseif($lsid==3 && $csid ==6){$rt++;}
                                        elseif($lsid==6 && $csid ==9){$tp++;}
                                        elseif($lsid==9 && $csid ==7){$pvp++;}
                                        elseif($lsid==7 && $csid ==12){$vph++;}
                                    else{$other++;}
                                }}
                                ?>
                        <tr>
                            <td><?=$tdate?></td>
                            <td><?=$or?></td>
                            <td><?=$ot?></td>
                            <td><?=$ort?></td>
                            <td><?=$rr?></td>
                            <td><?=$rt?></td>
                            <td><?=$tp?></td>
                            <td><?=$pvp?></td>
                            <td><?=$vph?></td>
                            <td><?=$other?></td>
                        </tr> 
                      </tbody>
                    </table> 
                </div>
            </div>
            </div>
            
            <div class="">
                <div class="">
                        <table class="table table-striped table-bordered" cellspacing="0" width="100%">
                        <thead>
                            <tr>
                                <th>BD Name</th>
                                <th>Open To Open [RPEM]</th>
                                <th>Open To Tentative</th>
                                <th>Open [RPEM] to Reachout</th>
                                <th>Reachout to Tentative</th>
                                <th>Tentative to Positive</th>
                                <th>Positive To Very Positive</th>
                                <th>Positive to Closure</th>
                                <th>Very Positive To Closure</th>
                                <th>Others</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            $bd = $this->Menu_model->get_userbyaid($uid);
                            foreach($bd as $bd){
                            $bdname = $bd->name;
                            $bdid = $bd->user_id;
                                $sc=$this->Menu_model->new_scon($bdid,$tdate,'1');
                                $or=0;$ort=0;$rr=0;$rt=0;$tp=0;$pvp=0;$vph=0;$other=0;
                                if($sc){
                                foreach($sc as $m){
                                    $lsid = $m->status_id;
                                    $csid = $m->nstatus_id;
                                    if($lsid!=$csid){
                                    if($lsid==1 && $csid ==8){$or++;}
                                    elseif($lsid==1 && $csid ==3){$ot++;}
                                    elseif($lsid==8 && $csid ==2){$ort++;}
                                    elseif($lsid==2 && $csid ==3){$rr++;}
                                    elseif($lsid==3 && $csid ==6){$rt++;}
                                    elseif($lsid==6 && $csid ==9){$tp++;}
                                    elseif($lsid==9 && $csid ==7){$pvp++;}
                                    elseif($lsid==7 && $csid ==12){$vph++;}
                                    else{$other++;}
                                }}
                            ?>
                        <tr>
                            <td><?=$bdname?></td>
                            <td><?=$or?></td>
                            <td><?=$ot?></td>
                            <td><?=$ort?></td>
                            <td><?=$rr?></td>
                            <td><?=$rt?></td>
                            <td><?=$tp?></td>
                            <td><?=$pvp?></td>
                            <td><?=$vph?></td>
                            <td><?=$other?></td>
                        </tr>
                        <?php }} ?>  
                      </tbody>
                    </table> 
                </div>
            </div>
            
            
            <div class="">
                <div class="">
                    <b>Open to Open[RPEM]</b>   
                    <table class="table table-striped table-bordered" cellspacing="0" wi$dth="100%">
                        <thead>
                            <tr>
                                <th>S.No.</th>
                                <th>Status Conversion By</th>
                                <th>Last Status</th>
                                <th>Current Status</th>
                                <th>Company Name</th>
                                <th>BD Name</th>
                                <th>Action Type</th>	
                                <th>Remark</th>	
                                <th>Total Logs</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            $i=1;
                            $mdata = $this->Menu_model->new_scon($uid,$tdate,'1');
                            foreach($mdata as $m){
                                $lsid = $m->status_id;
                                $csid = $m->nstatus_id;
                                $cid = $m->cid_id;
                                
                                $s1 = $this->Menu_model->get_statusbyid($lsid);
                                if($s1){$s1=$s1[0]->name;}else{$s1='';}
                                $s2 = $this->Menu_model->get_statusbyid($csid);
                                if($s2){$s2=$s2[0]->name;}else{$s2='';}
                                
                                if($lsid==1 && $csid ==8){
                                    $user = $m->user_id;
                                    $user = $this->Menu_model->get_userbyid($user); 
                                    $cd = $this->Menu_model->get_cdbyinid($cid);  
                                    $bd = $cd[0]->creator_id;
                                    $bd = $this->Menu_model->get_userbyid($bd);
                                    $tlog = $this->Menu_model->get_tblcalleventsbyid($cid);
                                    $aid  = $m->actiontype_id;
                                    $aid = $this->Menu_model->get_actionbyid($aid);
                                ?> 
                                <tr>
                                <td><?=$i?></td>
                                <td><?=$user[0]->name?></td>
                                <td><?=$s1?></td>
                                <td><?=$s2?></td>
                                <td><?=$cd[0]->compname?></td>
                                <td><?=$bd[0]->name?></td>
                                <td><?=$aid[0]->name?></td>
                                <td><?=$m->remarks?></td>
                                <td><?=sizeof($tlog)?></td>
                                
                             </tr>
                         <?php $i++;}}?>
                      </tbody>
                    </table>
                </div>
            </div>
            
            
            <div class="">
                <div class="">
                    <b>Open to Tentative</b>   
                    <table class="table table-striped table-bordered" cellspacing="0" wi$dth="100%">
                        <thead>
                            <tr>
                                <th>S.No.</th>
                                <th>Status Conversion By</th>
                                <th>Last Status</th>
                                <th>Current Status</th>
                                <th>Company Name</th>
                                <th>BD Name</th>
                                <th>Action Type</th>	
                                <th>Remark</th>	
                                <th>Total Logs</th>	
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            $i=1;
                            $mdata = $this->Menu_model->new_scon($uid,$tdate,'1');
                            foreach($mdata as $m){
                                $lsid = $m->status_id;
                                $csid = $m->nstatus_id;
                                $cid = $m->cid_id;
                                
                                $s1 = $this->Menu_model->get_statusbyid($lsid);
                                if($s1){$s1=$s1[0]->name;}else{$s1='';}
                                $s2 = $this->Menu_model->get_statusbyid($csid);
                                if($s2){$s2=$s2[0]->name;}else{$s2='';}
                                
                                if($lsid==1 && $csid ==3){
                                    $user = $m->user_id;
                                    $user = $this->Menu_model->get_userbyid($user); 
                                    $cd = $this->Menu_model->get_cdbyinid($cid);  
                                    $bd = $cd[0]->creator_id;
                                    $bd = $this->Menu_model->get_userbyid($bd);
                                    $tlog = $this->Menu_model->get_tblcalleventsbyid($cid);
                                    $aid  = $m->actiontype_id;
                                    $aid = $this->Menu_model->get_actionbyid($aid);
                                ?> 
                                <tr>
                                <td><?=$i?></td>
                                <td><?=$user[0]->name?></td>
                                <td><?=$s1?></td>
                                <td><?=$s2?></td>
                                <td><?=$cd[0]->compname?></td>
                                <td><?=$bd[0]->name?></td>
                                <td><?=$aid[0]->name?></td>
                                <td><?=$m->remarks?></td>
                                <td><?=sizeof($tlog)?></td>
                             </tr>
                         <?php $i++;}}?>
                      </tbody>
                    </table>
                </div>
            </div>
            
            
            
            <div class="">
                <div class="">
                    <b>Open [RPEM] to Reachout</b>   
                    <table class="table table-striped table-bordered" cellspacing="0" wi$dth="100%">
                        <thead>
                            <tr>
                                <th>S.No.</th>
                                <th>Status Conversion By</th>
                                <th>Last Status</th>
                                <th>Current Status</th>
                                <th>Company Name</th>
                                <th>BD Name</th>
                                <th>Action Type</th>	
                                <th>Remark</th>	
                                <th>Total Logs</th>	
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            $i=1;
                            $mdata = $this->Menu_model->new_scon($uid,$tdate,'1');
                            foreach($mdata as $m){
                                $lsid = $m->status_id;
                                $csid = $m->nstatus_id;
                                $cid = $m->cid_id;
                                
                                $s1 = $this->Menu_model->get_statusbyid($lsid);
                                if($s1){$s1=$s1[0]->name;}else{$s1='';}
                                $s2 = $this->Menu_model->get_statusbyid($csid);
                                if($s2){$s2=$s2[0]->name;}else{$s2='';}
                                
                                if($lsid==8 && $csid ==2){
                                    $user = $m->user_id;
                                    $user = $this->Menu_model->get_userbyid($user); 
                                    $cd = $this->Menu_model->get_cdbyinid($cid);  
                                    $bd = $cd[0]->creator_id;
                                    $bd = $this->Menu_model->get_userbyid($bd);
                                    $tlog = $this->Menu_model->get_tblcalleventsbyid($cid);
                                    $aid  = $m->actiontype_id;
                                    $aid = $this->Menu_model->get_actionbyid($aid);
                                ?> 
                                <tr>
                                <td><?=$i?></td>
                                <td><?=$user[0]->name?></td>
                                <td><?=$s1?></td>
                                <td><?=$s2?></td>
                                <td><?=$cd[0]->compname?></td>
                                <td><?=$bd[0]->name?></td>
                                <td><?=$aid[0]->name?></td>
                                <td><?=$m->remarks?></td>
                                <td><?=sizeof($tlog)?></td>
                             </tr>
                         <?php $i++;}}?>
                      </tbody>
                    </table>
                </div>
            </div>
            
            
            <div class="">
                <div class="">
                    <b>Reachout to Tentative</b>   
                    <table class="table table-striped table-bordered" cellspacing="0" wi$dth="100%">
                        <thead>
                            <tr>
                                <th>S.No.</th>
                                <th>Status Conversion By</th>
                                <th>Last Status</th>
                                <th>Current Status</th>
                                <th>Company Name</th>
                                <th>BD Name</th>
                                <th>Action Type</th>	
                                <th>Remark</th>	
                                <th>Total Logs</th>	
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            $i=1;
                            $mdata = $this->Menu_model->new_scon($uid,$tdate,'1');
                            foreach($mdata as $m){
                                $lsid = $m->status_id;
                                $csid = $m->nstatus_id;
                                $cid = $m->cid_id;
                                
                                $s1 = $this->Menu_model->get_statusbyid($lsid);
                                if($s1){$s1=$s1[0]->name;}else{$s1='';}
                                $s2 = $this->Menu_model->get_statusbyid($csid);
                                if($s2){$s2=$s2[0]->name;}else{$s2='';}
                                
                                if($lsid==2 && $csid ==3){
                                    $user = $m->user_id;
                                    $user = $this->Menu_model->get_userbyid($user); 
                                    $cd = $this->Menu_model->get_cdbyinid($cid);  
                                    $bd = $cd[0]->creator_id;
                                    $bd = $this->Menu_model->get_userbyid($bd);
                                    $tlog = $this->Menu_model->get_tblcalleventsbyid($cid);
                                    $aid  = $m->actiontype_id;
                                    $aid = $this->Menu_model->get_actionbyid($aid);
                                ?> 
                                <tr>
                                <td><?=$i?></td>
                                <td><?=$user[0]->name?></td>
                                <td><?=$s1?></td>
                                <td><?=$s2?></td>
                                <td><?=$cd[0]->compname?></td>
                                <td><?=$bd[0]->name?></td>
                                <td><?=$aid[0]->name?></td>
                                <td><?=$m->remarks?></td>
                                <td><?=sizeof($tlog)?></td>
                             </tr>
                         <?php $i++;}}?>
                      </tbody>
                    </table>
                </div>
            </div>
            
            <div class="">
                <div class="">
                    <b>Tentative to Positive</b>   
                    <table class="table table-striped table-bordered" cellspacing="0" wi$dth="100%">
                        <thead>
                            <tr>
                                <th>S.No.</th>
                                <th>Status Conversion By</th>
                                <th>Last Status</th>
                                <th>Current Status</th>
                                <th>Company Name</th>
                                <th>BD Name</th>
                                <th>Action Type</th>	
                                <th>Remark</th>	
                                <th>Total Logs</th>	
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            $i=1;
                            $mdata = $this->Menu_model->new_scon($uid,$tdate,'1');
                            foreach($mdata as $m){
                                $lsid = $m->status_id;
                                $csid = $m->nstatus_id;
                                $cid = $m->cid_id;
                                
                                $s1 = $this->Menu_model->get_statusbyid($lsid);
                                if($s1){$s1=$s1[0]->name;}else{$s1='';}
                                $s2 = $this->Menu_model->get_statusbyid($csid);
                                if($s2){$s2=$s2[0]->name;}else{$s2='';}
                                
                                if($lsid==3 && $csid ==6){
                                    $user = $m->user_id;
                                    $user = $this->Menu_model->get_userbyid($user); 
                                    $cd = $this->Menu_model->get_cdbyinid($cid);  
                                    $bd = $cd[0]->creator_id;
                                    $bd = $this->Menu_model->get_userbyid($bd);
                                    $tlog = $this->Menu_model->get_tblcalleventsbyid($cid);
                                    $aid  = $m->actiontype_id;
                                    $aid = $this->Menu_model->get_actionbyid($aid);
                                ?> 
                                <tr>
                                <td><?=$i?></td>
                                <td><?=$user[0]->name?></td>
                                <td><?=$s1?></td>
                                <td><?=$s2?></td>
                                <td><?=$cd[0]->compname?></td>
                                <td><?=$bd[0]->name?></td>
                                <td><?=$aid[0]->name?></td>
                                <td><?=$m->remarks?></td>
                                <td><?=sizeof($tlog)?></td>
                             </tr>
                         <?php $i++;}}?>
                      </tbody>
                    </table>
                </div>
            </div>
            
            
            <div class="">
                <div class="">
                    <b>Positive To Very Positive</b>   
                    <table class="table table-striped table-bordered" cellspacing="0" wi$dth="100%">
                        <thead>
                            <tr>
                                <th>S.No.</th>
                                <th>Status Conversion By</th>
                                <th>Last Status</th>
                                <th>Current Status</th>
                                <th>Company Name</th>
                                <th>BD Name</th>
                                <th>Action Type</th>	
                                <th>Remark</th>	
                                <th>Total Logs</th>	
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            $i=1;
                            $mdata = $this->Menu_model->new_scon($uid,$tdate,'1');
                            foreach($mdata as $m){
                                $lsid = $m->status_id;
                                $csid = $m->nstatus_id;
                                $cid = $m->cid_id;
                                
                                $s1 = $this->Menu_model->get_statusbyid($lsid);
                                if($s1){$s1=$s1[0]->name;}else{$s1='';}
                                $s2 = $this->Menu_model->get_statusbyid($csid);
                                if($s2){$s2=$s2[0]->name;}else{$s2='';}
                                
                                if($lsid==6 && $csid ==9){
                                    $user = $m->user_id;
                                    $user = $this->Menu_model->get_userbyid($user); 
                                    $cd = $this->Menu_model->get_cdbyinid($cid);  
                                    $bd = $cd[0]->creator_id;
                                    $bd = $this->Menu_model->get_userbyid($bd);
                                    $tlog = $this->Menu_model->get_tblcalleventsbyid($cid);
                                    $aid  = $m->actiontype_id;
                                    $aid = $this->Menu_model->get_actionbyid($aid);
                                ?> 
                                <tr>
                                <td><?=$i?></td>
                                <td><?=$user[0]->name?></td>
                                <td><?=$s1?></td>
                                <td><?=$s2?></td>
                                <td><?=$cd[0]->compname?></td>
                                <td><?=$bd[0]->name?></td>
                                <td><?=$aid[0]->name?></td>
                                <td><?=$m->remarks?></td>
                                <td><?=sizeof($tlog)?></td>
                             </tr>
                         <?php $i++;}}?>
                      </tbody>
                    </table>
                </div>
            </div>
            
            
            <div class="">
                <div class="">
                    <b>Positive to Closure</b>   
                    <table class="table table-striped table-bordered" cellspacing="0" wi$dth="100%">
                        <thead>
                            <tr>
                                <th>S.No.</th>
                                <th>Status Conversion By</th>
                                <th>Last Status</th>
                                <th>Current Status</th>
                                <th>Company Name</th>
                                <th>BD Name</th>
                                <th>Action Type</th>	
                                <th>Remark</th>	
                                <th>Total Logs</th>	
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            $i=1;
                            $mdata = $this->Menu_model->new_scon($uid,$tdate,'1');
                            foreach($mdata as $m){
                                $lsid = $m->status_id;
                                $csid = $m->nstatus_id;
                                $cid = $m->cid_id;
                                
                                $s1 = $this->Menu_model->get_statusbyid($lsid);
                                if($s1){$s1=$s1[0]->name;}else{$s1='';}
                                $s2 = $this->Menu_model->get_statusbyid($csid);
                                if($s2){$s2=$s2[0]->name;}else{$s2='';}
                                
                                if($lsid==6 && $csid ==7){
                                    $user = $m->user_id;
                                    $user = $this->Menu_model->get_userbyid($user); 
                                    $cd = $this->Menu_model->get_cdbyinid($cid);  
                                    $bd = $cd[0]->creator_id;
                                    $bd = $this->Menu_model->get_userbyid($bd);
                                    $tlog = $this->Menu_model->get_tblcalleventsbyid($cid);
                                    $aid  = $m->actiontype_id;
                                    $aid = $this->Menu_model->get_actionbyid($aid);
                                ?> 
                                <tr>
                                <td><?=$i?></td>
                                <td><?=$user[0]->name?></td>
                                <td><?=$s1?></td>
                                <td><?=$s2?></td>
                                <td><?=$cd[0]->compname?></td>
                                <td><?=$bd[0]->name?></td>
                                <td><?=$aid[0]->name?></td>
                                <td><?=$m->remarks?></td>
                                <td><?=sizeof($tlog)?></td>
                             </tr>
                         <?php $i++;}}?>
                      </tbody>
                    </table>
                </div>
            </div>
            
            <div class="">
                <div class="">
                    <b>Very Positive To Closure</b>   
                    <table class="table table-striped table-bordered" cellspacing="0" wi$dth="100%">
                        <thead>
                            <tr>
                                <th>S.No.</th>
                                <th>Status Conversion By</th>
                                <th>Last Status</th>
                                <th>Current Status</th>
                                <th>Company Name</th>
                                <th>BD Name</th>
                                <th>Action Type</th>	
                                <th>Remark</th>	
                                <th>Total Logs</th>	
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            $i=1;
                            $mdata = $this->Menu_model->new_scon($uid,$tdate,'1');
                            foreach($mdata as $m){
                                $lsid = $m->status_id;
                                $csid = $m->nstatus_id;
                                $cid = $m->cid_id;
                                
                                $s1 = $this->Menu_model->get_statusbyid($lsid);
                                if($s1){$s1=$s1[0]->name;}else{$s1='';}
                                $s2 = $this->Menu_model->get_statusbyid($csid);
                                if($s2){$s2=$s2[0]->name;}else{$s2='';}
                                
                                if($lsid==9 && $csid ==7){
                                    $user = $m->user_id;
                                    $user = $this->Menu_model->get_userbyid($user); 
                                    $cd = $this->Menu_model->get_cdbyinid($cid);  
                                    $bd = $cd[0]->creator_id;
                                    $bd = $this->Menu_model->get_userbyid($bd);
                                    $tlog = $this->Menu_model->get_tblcalleventsbyid($cid);
                                    $aid  = $m->actiontype_id;
                                    $aid = $this->Menu_model->get_actionbyid($aid);
                                ?> 
                                <tr>
                                <td><?=$i?></td>
                                <td><?=$user[0]->name?></td>
                                <td><?=$s1?></td>
                                <td><?=$s2?></td>
                                <td><?=$cd[0]->compname?></td>
                                <td><?=$bd[0]->name?></td>
                                <td><?=$aid[0]->name?></td>
                                <td><?=$m->remarks?></td>
                                <td><?=sizeof($tlog)?></td>
                             </tr>
                         <?php $i++;}}?>
                      </tbody>
                    </table>
                </div>
            </div>
            
            
            
            
            <div class="">
                <div class="">
                    <b>Other Conversion</b>   
                    <table class="table table-striped table-bordered" cellspacing="0" wi$dth="100%">
                        <thead>
                            <tr>
                                <th>S.No.</th>
                                <th>Status Conversion By</th>
                                <th>Last Status</th>
                                <th>Current Status</th>
                                <th>Company Name</th>
                                <th>BD Name</th>
                                <th>Action Type</th>	
                                <th>Remark</th>	
                                <th>Total Logs</th>	
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            $i=1;
                            $mdata = $this->Menu_model->new_scon($uid,$tdate,'1');
                            foreach($mdata as $m){
                                $lsid = $m->status_id;
                                $csid = $m->nstatus_id;
                                $cid = $m->cid_id;
                                
                                $s1 = $this->Menu_model->get_statusbyid($lsid);
                                if($s1){$s1=$s1[0]->name;}else{$s1='';}
                                $s2 = $this->Menu_model->get_statusbyid($csid);
                                if($s2){$s2=$s2[0]->name;}else{$s2='';}
                                    if($lsid!=$csid){
                                        if($lsid==1 && $csid ==8){}
                                        elseif($lsid==1 && $csid ==3){}
                                        elseif($lsid==8 && $csid ==2){}
                                        elseif($lsid==2 && $csid ==3){}
                                        elseif($lsid==3 && $csid ==6){}
                                        elseif($lsid==6 && $csid ==9){}
                                        elseif($lsid==6 && $csid ==7){}
                                        elseif($lsid==9 && $csid ==7){}
                                        elseif($lsid==7 && $csid ==12){}
                                        else{
                                    $user = $m->user_id;
                                    $user = $this->Menu_model->get_userbyid($user); 
                                    $cd = $this->Menu_model->get_cdbyinid($cid);  
                                    $bd = $cd[0]->creator_id;
                                    $bd = $this->Menu_model->get_userbyid($bd);
                                    $tlog = $this->Menu_model->get_tblcalleventsbyid($cid);
                                    $aid  = $m->actiontype_id;
                                    $aid = $this->Menu_model->get_actionbyid($aid);
                                ?> 
                                <tr>
                                <td><?=$i?></td>
                                <td><?=$user[0]->name?></td>
                                <td><?=$s1?></td>
                                <td><?=$s2?></td>
                                <td><?=$cd[0]->compname?></td>
                                <td><?=$bd[0]->name?></td>
                                <td><?=$aid[0]->name?></td>
                                <td><?=$m->remarks?></td>
                                <td><?=sizeof($tlog)?></td>
                             </tr>
                         <?php $i++;}}}?>
                      </tbody>
                    </table>
                </div>
            </div>
            
            </div>
            <div class="col-12 m-3">
            <center><h5>Team Meeting Detail</h5></center><hr>
            <div class="">
                <div class="">
                    <div class="pdf-viwer">
                        <table class="table table-striped table-bordered" cellspacing="0" width="100%">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Total Bargin Plan</th>
                                <th>Not Started Bargin</th>
                                <th>Started Bargin</th>
                                <th>Not Close Bargin</th>
                                <th>Close Bargin</th>
                                <th>Lead not Updated</th>
                                <th>Completed Bargin</th>
                                <th>Total RP Meeting</th>
                                <th>Total Non RP Meeting</th>
                                <th>Total RP Priority</th>
                                <th>Total RP Not Priority</th>
                                <th>Total Only Got Detail</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                                $tbmeetd = $this->Menu_model->get_tbmeetdbyaid($uid,$tdate);
                                foreach($tbmeetd as $tmd){?>
                        <tr>
                            <td><?=$tdate?></td>
                            <td><?=$tmd->ab?></td>
                            <td><?=$tmd->a?></td>
                            <td><?=$tmd->i?></td>
                            <td><?=$tmd->b?></td>
                            <td><?=$tmd->b?></td>
                            <td><?=$tmd->h?></td>
                            <td><?=$tmd->c?></td>
                            <td><?=$tmd->d?></td>
                            <td><?=$tmd->e?></td>
                            <td><?=$tmd->f?></td>
                            <td><?=$tmd->g?></td>
                            <td><?=$tmd->k?></td>
                        </tr>
                        <?php } ?>
                      </tbody>
                    </table> 
                </div>
            </div>
            
            <div class="">
                <div class="">
                    <div class="pdf-viwer">
                        <table class="table table-striped table-bordered" cellspacing="0" width="100%">
                        <thead>
                            <tr>
                                <th>S.No.</th>
                                <th>BD Name</th>
                                <th>Company Name</th>
                                <th>Photo</th>
                                <th>Started @</th>
                                <th>Close @</th>
                                <th>Start Location</th>
                                <th>Close Location</th>
                                <th>RP Yes/No</th>
                                <th>Priority Yes/No</th>
                                <th>MOM Yes/No</th>
                                <th>Thanks Mail Yes/No</th>
                                <th>PST Assign Yes/No</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $i=1;
                            $mdata = $this->Menu_model->get_tbmd('1',$uid,$tdate,$tdate);
                            foreach($mdata as $dt){
                            $cid = $dt->cid;
                            $tid = $dt->tid;
                            $momc = $this->Menu_model->get_momyn($cid,$tid);
                            if($momc){$momc='yes';}else{$momc='no';}
                            $emailc = $this->Menu_model->get_temailyn($cid,$tid);
                            if($emailc){$emailc='yes';}else{$emailc='no';}
                            $psta = $this->Menu_model->get_psta($cid);
                            if($psta){$psta='yes';}else{$psta='no';}
                            
                            ?>
                            
                        <tr>
                            <td><?=$i?></td>
                            <td><?=$dt->name?></td>
                            <td><?=$dt->company_name?></td>
                            <td>Image</td>
                            <td><?=$time1=$dt->startm?></td>
                            <td><?=$time2=$dt->closem?></td>
                            <td><a href="https://www.google.com/maps?q=<?=$dt->slatitude?>,<?=$dt->slongitude?>"><i class="fas fa-map-marker-alt" style="font-size:36px" aria-hidden="true"></i></a></td>
                            <td><a href="https://www.google.com/maps?q=<?=$dt->clatitude?>,<?=$dt->clongitude?>"><i class="fas fa-map-marker-alt" style="font-size:36px" aria-hidden="true"></i></a></td>
                            <td><?=$dt->mtype?></td>
                            <td><?=$dt->priority?></td>
                            <td><?=$momc?></td>
                            <td><?=$emailc?></td>
                            <td><?=$psta?></td>
                        <?php $i++;} ?>
                      </tbody>
                    </table> 
                </div>
            </div>
            <div class="">
                <div class="">
                        <table class="table table-striped table-bordered" cellspacing="0" width="100%">
                        <thead>
                            <tr>
                                <th>S.No.</th>
                                <th>BD Name</th>
                                <th>Total meeting</th>
                                <th>Total RP</th>
                                <th>Fresh Meeting</th>
                                <th>Total Re-meeting</th>
                                <th>MOM pending in app</th>
                                <th>Remarks</th>
                                <th>Meeting Conversion Ratio</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            $i=1;
                            $bd = $this->Menu_model->get_userbyaid($uid);
                            foreach($bd as $bd){
                            $bdname = $bd->name;
                            $bdid = $bd->user_id;
                            $mdata = $this->Menu_model->get_tbmd('1',$bdid,$tdate,$tdate);
                            $tt=0;$trp=0;$tfm=0;$trm=0;$pmom=0;$fmeet=0;$rmeet=0;$ymom=0;$nmom=0;$rpm=0;
                            foreach($mdata as $dt){
                                $tt++;
                                $cmpid = $dt->cmpid;
                                $cid = $dt->cid;
                                $tid = $dt->tid;
                                $rp = $this->Menu_model->get_checkrpbytid($tid);
                                if($rp){
                                    $comp = $this->Menu_model->get_bargdetailcid($cmpid);
                                    $cmprp = $this->Menu_model->get_tblrpcid($cid);
                                    $cmrp = $cmprp[0]->cont;
                                    $comp = sizeof($comp);
                                    if($cmrp==1){$fmeet++;}else{$rmeet++;}
                                    $rpm++;
                                    $momc = $this->Menu_model->get_momyn($cid,$tid);
                                    if($momc){$momc='yes';$ymom++;}else{$momc='no';$pmom++;$nmom++;}
                                }
                                if($fmeet<=2){$remark='Bad performance';$color = 'text-danger';}elseif($fmeet<=4){$remark='Average Performance';$color = 'text-warning';}else{$remark='Good Performance';$color = 'text-success';}
                            }
                            if($tt>0){
                            $a = ($fmeet/$tt)*100;
                            $ratio = sprintf ("%.2f", $a).' %';
                            ?> 
                            
                            
                        <tr>
                            <td><?=$i?></td>
                            <td><?=$bdname?></td>
                            <td><?=$tt?></td>
                            <td><?=$rpm?></td>
                            <td><?=$fmeet?></td>
                            <td><?=$rmeet?></td>
                            <td><?=$nmom?></td>
                            <td class="<?=$color?>"><?=$remark?></td>
                            <td><?=$ratio?></td>
                        </tr>
                        <?php $i++;}} ?>
                      </tbody>
                    </table> 
                </div>
            </div>
            
            </div>
            
            
            
            
            
            
           
          
          
          <!-- ./col -->
              
        </div><!-- /.row (main row) -->
      </div><!-- /.container-fluid -->
</section>


    
<script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
<script type="text/javascript">

</script>
    
   
    
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

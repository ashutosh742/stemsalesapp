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
    <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css" rel="stylesheet" />
    <style>
      .select2 {
      width: 100%; /* Set the desired width */
      }
      .modal-content {
      background: #edf0f3 !important;
      font-size: 14px;
      }
      .modal-content .card-header.bg-info {
      margin-top: 4px !important;
      height: 20px;
      align-items: center;
      justify-content: center;
      display: flex;
      background: #df338a !important;
      }
      .modal-content .card-body {
      background: beige !important;
      margin-bottom: 4px;
      }
      .momhbox{
      background: antiquewhite;
      align-items: center;
      justify-content: center;
      display: flex;
      box-shadow: rgba(9, 30, 66, 0.25) 0px 1px 1px, rgba(9, 30, 66, 0.13) 0px 0px 1px 1px;
      }
      .identify_school_box {
      background: aliceblue;
      padding: 10px;
      box-shadow: rgba(9, 30, 66, 0.25) 0px 1px 1px, rgba(9, 30, 66, 0.13) 0px 0px 1px 1px;
      }
      .select2 {
      width: 100%; 
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
          <section class="content">
              <?php $totalt = $this->Menu_model->get_ttbytimeAutotaskData($uid); ?>
                  <?php
                      $pentask = sizeof($autotasktimenew);
                      if($pentask > 0){
                        $ast1=$autotasktimenew[0]->stime;
                        $aet2=$autotasktimenew[0]->etime;
                      }
              ?>
              <div class="row p-3">
                <div class="col-lg-12 col-sm">
                  <div class="row">
                    <div class="col-lg-12 col-sm mt-2">
                      <div class="card card-primary card-outline card-outline-tabs">
                              <div class="bg-info p-2">
                              <center><h4 class="p-3">Pending Auto Task</h4></center>
                              </div>
                        <div class="card-header p-0 border-bottom-0 mt-2">
                          <ul class="nav nav-tabs" id="custom-tabs-four-tab" role="tablist">
                            <li class="nav-item">
                              <?php 
                              $ttbyd = $this->Menu_model->get_ttbytimedAutotaskCount($uid);
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
                              <a class="nav-link" id="custom-tabs-four-research-tab" data-toggle="pill" href="#custom-tabs-four-research" role="tab" aria-controls="custom-tabs-four-research" aria-selected="false">
                              <b> Research (<?=$ttbyd[0]->h?>)</b>
                              </a>
                            </li>
                            <li class="nav-item">
                              <a class="nav-link" id="custom-tabs-four-socialn-tab" data-toggle="pill" href="#custom-tabs-four-socialn" role="tab" aria-controls="custom-tabs-four-socialn" aria-selected="false">
                              <b> Social Networking (<?=$ttbyd[0]->i?>)</b>
                              </a>
                            </li>
                            <li class="nav-item">
                              <a class="nav-link" id="custom-tabs-four-sociala-tab" data-toggle="pill" href="#custom-tabs-four-sociala" role="tab" aria-controls="custom-tabs-four-sociala" aria-selected="false">
                              <b> Social Activity (<?=$ttbyd[0]->j?>)</b>
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
                              <a class="nav-link" id="custom-tabs-four-mom-tab" data-toggle="pill" href="#custom-tabs-four-mom" role="tab" aria-controls="custom-tabs-mom" aria-selected="false">
                              MOM<span class="badge badge-success"><?=$ttbyd[0]->f?></span>
                              </a>
                            </li>
                            <li class="nav-item">
                              <a class="nav-link" id="custom-tabs-four-proposal-tab" data-toggle="pill" href="#custom-tabs-four-proposal" role="tab" aria-controls="custom-tabs-proposal" aria-selected="false">
                              Proposal<span class="badge badge-success"><?=$ttbyd[0]->g?></span>
                              </a>
                            </li>

                            <li class="nav-item">
                              <a class="nav-link" id="custom-tabs-four-taskcheck-tab" data-toggle="pill" href="#custom-tabs-four-taskcheck" role="tab" aria-controls="custom-tabs-taskcheck" aria-selected="false">
                                <?php 
                                $gettodaysmom = $this->Menu_model->gePendingMOMCheckTask($uid,$tdate);
                                $gettodaysmomcnt = sizeof($gettodaysmom);
                                ?>
                              Task Check <span class="badge badge-success"><?=$gettodaysmomcnt;?></span>
                              </a>
                            </li>
                        
                          </ul>
                        </div>

                        <div class="card-body">
                <div class="tab-content" id="custom-tabs-four-tabContent">
   
                <div class="tab-pane fade show active" id="custom-tabs-four-home" role="tabpanel" aria-labelledby="custom-tabs-four-home-tab">
                  <div id="accordion">
                      <div class="card">
                          <?php 
                          $atai = 1; 
                          foreach($totalt as $tt){
                            $taid = $tt->actiontype_id;
                            $taid=$this->Menu_model->get_actionbyid($taid);
                            $time = $tt->appointmentdatetime;
                            $reminder = $tt->reminder;
                            $time = date('h:i a', strtotime($time));
                            if($tt->actiontype_id != 18){
                           
                            ?>
                          <button id="add_act<?=$atai?>" value="<?=$tt->id?>" style="background: none;color: inherit;border: none;padding: 0;font: inherit;cursor: pointer;outline: inherit;width: fit-content; padding: 10px;">
                                  <span class="mr-3 align-items-center">
                                   ( <?= $atai; ?> )
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
                            <b><?php if($reminder>0){echo 'Reminder for This Task';}?></b>
                            </button>
                            <?php } $atai++; }?>
                      </div>
                  </div>
                </div>



                  <div class="tab-pane fade" id="custom-tabs-four-call" role="tabpanel" aria-labelledby="custom-tabs-four-call-tab">
                      <?php $aai=0;foreach($totalt as $tt){if($tt->plan==1){if($tt->actiontype_id=='1'){
                          $taid = $tt->actiontype_id;
                          $taid=$this->Menu_model->get_action($taid);
                          $time = $tt->appointmentdatetime;
                          $reminder = $tt->reminder;
                          $rimby = $tt->reminderby;
                          $rimat = $tt->reminderat;
                          $rimbyname = $this->Menu_model->get_userbyid($rimby);

                          $time = date('h:i a', strtotime($time));
                      ?>
                        <div class="list-group-item list-group-item-action">
                          <span class="text-danger"><b><?php if($reminder>0){echo 'Reminder for This Task by '.$rimbyname[0]->name." at ".$rimat;}?></b></span><br>
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



                  <div class="tab-pane fade" id="custom-tabs-four-socialn" role="tabpanel" aria-labelledby="custom-tabs-four-socialn-tab">
                      <?php $aai=0;foreach($totalt as $tt){if($tt->plan==1){if($tt->actiontype_id=='13'){
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


                  <div class="tab-pane fade" id="custom-tabs-four-sociala" role="tabpanel" aria-labelledby="custom-tabs-four-sociala-tab">
                      <?php $aai=0;foreach($totalt as $tt){if($tt->plan==1){if($tt->actiontype_id=='14'){
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
                        if($tt->autotask == 1){$style = 'background: antiquewhite;'; $titletask = 'Auto Task';}else{$style =''; $titletask='';}
                      ?>
                        <div class="list-group-item list-group-item-action" title="<?=$titletask ?>" style="<?= $style ?>" >
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
                      <?php foreach($totalt as $tt){
                      
                        
                        if($tt->plan==1){if($tt->actiontype_id=='7'){
                       
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

                  <div class="tab-pane fade" id="custom-tabs-four-taskcheck" role="tabpanel" aria-labelledby="custom-tabs-four-taskcheck-tab">
                  <div class="card-header text-center bg-light" style="border-radius:unset" >
            <p>
              <?php 
            
              $groupedByActionTypes = [];
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
              <a href="<?=base_url();?>Management/MomDataCheck/<?=$reviewtype?>/<?=$ce_tskid?>">
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





                  <div class="tab-pane fade" id="custom-tabs-four-barg" role="tabpanel" aria-labelledby="custom-tabs-four-barg-tab">

                             <?php
                            $bl=0;
                            foreach($barg as $brg){
                                $bs = $brg->status;
                                $cid = $brg->cid;

                                $tid = $brg->tid;

                                $action_id = $this->Menu_model->get_actionIdfromTblCallEvent($tid);
                                if($action_id == 3){
                                  $meet_name = 'Sheduled Meeting';
                                }
                                if($action_id == 4){
                                  $meet_name = 'Barg in Meeting';
                                }
                                if($action_id == 17){
                                  $meet_name = 'Join Meeting';
                                }

                                $cd = $this->Menu_model->get_cdbyid($cid);
                                ?>
                                <?php if($bs=='Pending'){?>
                                    <button style="background: none;color: inherit;border: none;padding: 0;font: inherit;cursor: pointer;outline: inherit;" value="<?=$brg->id?>" id="startm<?=$bl?>">
                                        <div class="list-group-item list-group-item-action">
                                        <?=$cd[0]->compname?> |
                                        <?=$cd[0]->city?> |
                                        <?=$cd[0]->state?> |
                                        <?=$brg->storedt?> |
                                        <b class="text-success">Start Meeting (<?=$meet_name; ?>)</b>
                                    </div></button>
                                <?php }if($bs=='Start'){?>
                                    <button style="background: none;color: inherit;border: none;padding: 0;font: inherit;cursor: pointer;outline: inherit;" id="closem<?=$bl?>" value="<?=$brg->id?>">
                                        <div class="list-group-item list-group-item-action">
                                        <?=$cd[0]->compname?> |
                                        <?=$cd[0]->city?> |
                                        <?=$cd[0]->state?> |
                                        <?=$brg->storedt?> |
                                        <b class="text-danger">Close Meeting (<?=$meet_name; ?>)</b>
                                    </div>
                            <?php $bl++;}} ?>
                        </div>
                            <?php foreach($barg as $br){if($br->status=='RPClose'){
                              
                                // echo "<pre>";
                                // print_r($br);
                                $cid = $br->cid;
                                $cd = $this->Menu_model->get_cdbyid($cid);?>
                            <a href="BMNewLead/<?=$br->id?>"><div class="list-group-item list-group-item-action">
                                        <?=$cd[0]->compname?> |
                                        <?=$cd[0]->city?> |
                                        <?=$cd[0]->state?> |
                                        <?=$br->storedt?> |
                                        <b>Update Lead</b>
                                    </div></a>
                            <?php }} ?>
                        </div>
                  </div>
                      </div>
                    </div>
                    <!-- /.card -->
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
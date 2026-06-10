<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="wi$dth=device-wi$dth, initial-scale=1">
    <title>Day Management System | STEM APP | WebAPP</title>
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
     .scrollme {overflow-x: auto;}.card.bg-graywe {background: #175456;height: 100px;align-items: center;justify-content: center;display: flex;}.card.bg-graywe {transition: background 0.3s ease-in-out;}.card.bg-graywe:hover {background: #172556;}.card-body {min-height:650px;background: #e0f0f1;}.card a {color: white;}.uimage {background: #47758b;margin: 4px;padding: 4px;box-shadow: rgba(0, 0, 0, 0.25) 0px 0.0625em 0.0625em, rgba(0, 0, 0, 0.25) 0px 0.125em 0.5em, rgba(255, 255, 255, 0.1) 0px 0px 0px 1px inset;}.dot {height: 18px;width: 18px;background-color: blue;border-radius: 50%;display: inline-block;position: relative;border: 3px solid #fff;top: -48px;left: 186px;z-index: 1000;}.name{margin-top: -21px;font-size: 18px;}.fw-500{font-weight: 500 !important;}.start{color: green;}.stop{color: red;}.rate{border-bottom-right-radius: 12px;border-bottom-left-radius: 12px;}.rating {display: flex;flex-direction: row-reverse;justify-content: center }.rating>input {display: none }.rating>label {position: relative;width: 1em;font-size: 30px;font-weight: 300;color: #FFD600;cursor: pointer }.rating>label::before {content: "\2605";position: absolute;opacity: 0 }.rating>label:hover:before, .rating>label:hover~label:before {opacity: 1 !important }.rating>input:checked~label:before {opacity: 1 }.rating:hover>input:checked~label:before {opacity: 0.4 }.buttons{top: 36px;position: relative;}.rating-submit{border-radius: 15px;color: #fff;height: 49px;}.rating-submit:hover{color: #fff;}div#exampleModalCenter {background: rgba(0, 0, 0, 0.9);}.modal-content {background: azure;}.modal-content {border: none;}.modal-open .modal { background: rgba(0, 0, 0, .2)!important;}
    </style>
  </head>
  <body class="hold-transition sidebar-mini layout-fixed">
    <div class="wrapper">
      <!-- Preloader -->
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
        <?php
        $bd = $this->Menu_model->get_alluserbyaid($uid);
        // dd($bd);
         ?>
        <section class="content">
          <div class="container-fluid">
            <div class="row">
              <div class="col-12">
                <div class="card">
                  <div class="bg-primary card-header">
                    <h3 class="text-center m-3 text-white text-secondary">Day Management System</h3>
                  </div>
                  <!-- /.card-header -->
                    
                  <?php if ($this->session->flashdata('success_message')): ?>
                    <hr>
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <strong> <?php echo $this->session->flashdata('success_message'); ?></strong>
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                        </button>
                        </div>
                        <hr>
                    <?php endif; ?>

                  <div class="card-body">
                    <div class="container-fluid body-content">
                      <div class="page-header bg-graywe11">
                            <div class="table-responsive">
                                <div class="pdf-viwer">
                                    <table id="example1" class="table table-striped table-bordered" cellspacing="0" width="100%">
                                    <thead>
                                        <tr>
                                          <th>#</th>
                                          <th>Name</th>
                                          <th>Start Time</th>
                                          <th>Start Image</th>
                                          <th>Start Google Map </th>
                                          <th>Close Time</th>
                                          <th>Close Image</th>
                                          <th>Close Google Map</th>
                                          <th>Wfo</th>
                                          <th>Start Comment</th>
                                          <th>End Comment</th>
                                          <th>Todays AutoTask Time</th>
                                          <th>Todays Task Plan Request</th>
                                          <th>Todays Consume Time</th>
                                          <th> Todays Mornings FeedBack</th>
                                          <th style="background:#fff;"></th>
                                          <th>Yesterday Day Manage Time</th>
                                          <th>Yesterday AutoTask Time</th>
                                          <th>Yesterday Task Plan Request</th>
                                          <th>Yesterday Start Image</th>
                                          <th>Yesterday Start Comment</th>
                                          <th>Yesterday Close Image</th>
                                          <th>Yesterday End Comment</th>
                                          <th>Yesterday Close Google Map </th>
                                          <th>Yesterday Evening FeedBack</th>
                                          <th style="background:#fff;"></th>
                                          <th>Yesterday Total Plan Task</th>
                                          <th>Yesterday Total Pending Task</th>
                                          <th>Yesterday Total Autotask Task</th>
                                          <th>Yesterday Total Done Task</th>
                                          <th>Yesterday Total Consume Time</th>
                                          <th>Yesterday Task FeedBack</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php 
                                        $i=1; foreach($dayData as $data):
                                       $teamuid = $data->user_id;
                                        ?>
                                        <tr>
                                            <td><?= $i; ?></td>        
                                            <td><?= $data->name; ?></td>        
                                            <td><?= $data->ustart; ?></td>    
                                            <td>
                                                <a href="<?=base_url().'/'.$data->usimg;?>">
                                                    <img class="uimage" height="100px" alt="image not found" src="<?=base_url().'/'.$data->usimg;?>">
                                                </a>
                                            </td>
                                            <td>
                                            <?php
                                            $latitude = $data->slatitude;;
                                            $longitude = $data->slongitude;;
                                            ?>                     
                                           <div class="img-thumbnail" style="height: 300px"><iframe width="300px"  height="100%" src="https://maps.google.com/?q=<?=$latitude?>,<?=$longitude?>&t=k&z=13&ie=UTF8&iwloc=&output=embed"></iframe></div>
                                            <div class="text-center">
                                            <?php 
                                            $googleMapsUrl = "https://www.google.com/maps/search/?api=1&query={$latitude},{$longitude}";
                                            echo "<a style='color:green' href='{$googleMapsUrl}' target='_blank'>Open in Google Maps</a>";
                                            ?>
                                            </div>
                                           </td> 
                                            <td>
                                            <?php if(isset($data->uclose)){ ?>
                                                <?= $data->uclose; ?>
                                                <?php }else{ ?>
                                                    <span class="bg-warning p-1"> Pending</span>
                                                <?php }?>
                                        </td>    
                                          <td>
                                                <?php if(isset($data->ucimg)){ ?>
                                                <a href="<?=base_url().'/'.$data->ucimg;?>">
                                                    <img class="uimage" height="100px" alt="image not found" src="<?=base_url().'/'.$data->ucimg;?>">
                                                </a>
                                                <?php }else{ ?>
                                                    <span class="bg-warning p-1"> Pending</span>
                                                <?php }?>
                                            </td>  
                                            <td>
                                           <?php 
                                           $clatitude = $data->clatitude;;
                                           $clongitude = $data->clongitude;;
                                           if(isset($clatitude)){ ?>
                                           <div class="img-thumbnail" style="height: 300px"><iframe width="300px"  height="100%" src="https://maps.google.com/?q=<?=$clatitude?>,<?=$clongitude?>&t=k&z=13&ie=UTF8&iwloc=&output=embed"></iframe></div>
                                            <div class="text-center">
                                            <?php 
                                            $googleMapsUrl = "https://www.google.com/maps/search/?api=1&query={$clatitude},{$clongitude}";
                                            echo "<a style='color:green' href='{$googleMapsUrl}' target='_blank'>Open in Google Maps</a>";
                                            ?>
                                            </div>
                                            <?php }else{ ?>
                                                <span class="bg-warning p-1"> Pending</span>
                                           <?php } ?>
                                           </td>   
                                            <td><?php 
                                            if($data->wffo ==1){ ?>
                                                <span class="bg-warning1 p-1"> Work&nbsp;From&nbsp;Office</span>
                                          <?php  }elseif($data->wffo ==2){ ?>
                                            <span class="bg-warning1 p-1"> Work&nbsp;From&nbsp;Field</span>
                                          <?php }elseif($data->wffo ==3){ ?>
                                            <span class="bg-warning1 p-1"> Work&nbsp;From&nbsp;Field+Office </span>
                                          <?php  } ?></td>    
                                            <td>
                                                <?php if(isset($data->scomment)){ ?>
                                                    <?= $data->scomment ?>
                                                <?php }else{ ?>
                                                    <span class="bg-danger p-1"> Not Set</span>
                                                <?php }?>
                                            </td> 
                                            <td>
                                                <?php if(isset($data->ccomment)){ ?>
                                                    <?= $data->ccomment ?>
                                                <?php }else{ ?>
                                                    <span class="bg-danger p-1"> Not Set</span>
                                                <?php }?>
                                            </td> 
                                            <td>
                                                <?php 
                                                $checkaTime = $this->Management_model->CheckAutoTaskTime($teamuid,$cdate);
                                                if(sizeof($checkaTime) > 0){
                                                    echo '<b>Start Time : </b>'.$checkaTime[0]->stime;
                                                    echo "<hr>";
                                                    echo '<b>End Time : </b>'.$checkaTime[0]->etime ;
                                                    echo "<br/> <hr/>";
                                                    $start = new DateTime($checkaTime[0]->stime);
                                                    $end = new DateTime($checkaTime[0]->etime);
                                                    $interval = $start->diff($end);
                                                    $minutes = ($interval->h * 60) + $interval->i;
                                                    echo "Time Difference: $minutes minutes</b>";
                                                }
                                                ?>
                                            </td> 
                                            <td>
                                                <?php 
                                                $checkReqTime = $this->Management_model->CheckTaskPlanRequest($teamuid,$cdate);
                                                if(sizeof($checkReqTime) > 0){ ?>
                                                  <b>Request Reamarks : </b><?= $checkReqTime[0]->request_remarks ?> <hr>
                                                  <b>Approvel Status :<br/><br> </b> 
                                                  <?php if($checkReqTime[0]->approvel_status == 'Approved'){ ?>
                                                  <span class="bg-success p-1"> <?= $checkReqTime[0]->approvel_status ?></span>
                                                  <?php } ?>

                                                  <?php if($checkReqTime[0]->approvel_status == 'Reject'){ ?>
                                                  <span class="bg-danger p-1"> <?= $checkReqTime[0]->approvel_status ?></span>
                                                  <?php } ?>
                                                  <?php if($checkReqTime[0]->approvel_status == ''){ ?>
                                                    <span class="bg-warning p-1"> Pending</span>
                                                  <?php } ?>
                                                  <hr>
                                                  <b>Admin Reamarks : </b> <?= $checkReqTime[0]->remarks ?>
                                                <?php }else{
                                                    echo "No Request";
                                                }
                                                ?>
                                            </td> 
                                            <td>
                                        <?php 
                                        $dayconsumetime = $this->Management_model->CheckingYesterDayConsumeTime($teamuid,$cdate);
                                        date_default_timezone_set('Asia/Kolkata');
                                        $ustart = $dayconsumetime[0]->ustart;
                                        $uclose = $dayconsumetime[0]->uclose;
                                        $sst = (isset($ustart)) ? $ustart : 'Not Set';
                                        $eet = (isset($uclose)) ? $uclose : 'Pendings';
                                        echo '<b>Start Time : </b>'.$sst;
                                        echo "<hr>";
                                        if(!isset($uclose)){ ?>
                                            <span class="bg-warning p-1"> <b>End&nbsp;Time&nbsp;:&nbsp;</b>&nbsp;&nbsp;Pending</span> <hr>
                                       <?php  }else{
                                        echo '<b>End Time : </b>'.$eet ;
                                        } ?>
                                        <?php
                                        if($eet == 'Pendings'){
                                            $uclose = date("Y-m-d H:i:s"); 
                                        }else{
                                            $uclose = new DateTime($uclose);
                                        }
                                        $start = new DateTime($ustart);
                                        $end = new DateTime($uclose);
                                        $interval = $start->diff($end);
                                        $minutesd = ($interval->h * 60) + $interval->i;
                                        $hours = floor($minutesd / 60);
                                        // Calculate the remaining minutes
                                        $minutes = $minutesd % 60;
                                        echo "<b>Till now Consume Time : </b> <hr/>";
                                        echo "$hours hours and $minutes minutes.\n";
                                        ?>
                                        </td>  
                                        <td>
                                        <?php 
                                        $chkStarRating = $this->Management_model->CheckStarRatingsExistorNot($teamuid,$cdate);
                                        if(sizeof($chkStarRating) == 0){
                                        ?>
                                        <p><a href="javascript:void(0)" onclick="feedBackButton(<?= $i ?>,<?= $teamuid?>,'MorningsfeedBack')" class="btn" style="background:#02a1d8" >Add Todays Mornings FeedBack</a></p>
                                        <?php  }else{
                                            $feedbackby ='' ;
                                            $totalStar = '';
                                           foreach($chkStarRating as $star){  
                                            $feedbackby = $star->feedback_by;
                                            $totalStar += $star->star;
                                            $feedbackrem = $star->remarks;
                                           ?>
                                            <span class="text-success12"> <b><?= $star->question ?> : </b>  <?= $star->star ?> Star </span> <hr>
                                          <?php }
                                         $udetail = $this->Menu_model->get_userbyid($feedbackby);
                                         $feedbackbyname = $udetail[0]->name;
                                         echo "<span class='text-success12'><b>Feedback By</b> : ".$feedbackbyname."</span><hr/>";
                                         echo "<span class='bg-success p-1'><b>Feedback&nbsp;Status</b>&nbsp;:&nbsp;Suceess</span><hr/>";
                                         echo "<span class='text-success12'><b>Remarks</b> : ".$feedbackrem."</span><hr/>";
                                         echo "<span class='text-success12'><b>Total Star</b> : ".$totalStar." Star</span> <hr/>";
                                        }  ?>
                                        </td>
                                        <td style="background:#fff;"></td>
                                        <td>
                                        <?php 
                                        $dayconsumetime = $this->Management_model->CheckingYesterDayConsumeTime($teamuid,$previousDate);
                                        $ustart = $dayconsumetime[0]->ustart;
                                        $uclose = $dayconsumetime[0]->uclose;
                                        $sst = (isset($ustart)) ? $ustart : 'Not Set';
                                        $eet = (isset($uclose)) ? $uclose : '';
                                        echo '<b>Start Time : </b>'.$sst;
                                        echo "<hr>";
                                        echo '<b>End Time : </b>'.$eet ;
                                        echo "<br/> <hr/>";
                                        $start = new DateTime($ustart);
                                        $end = new DateTime($uclose);
                                        $interval = $start->diff($end);
                                        $minutesd = ($interval->h * 60) + $interval->i;
                                        $hours = floor($minutesd / 60);
                                        // Calculate the remaining minutes
                                        $minutes = $minutesd % 60;
                                        if(isset($uclose)){
                                            echo "$hours hours and $minutes minutes.\n";
                                        }else{ ?>
                                            <span class="bg-danger p-1"> <b>End&nbsp;Time&nbsp;:&nbsp;</b>&nbsp;Not&nbsp;Set</span>
                                        <?php } ?>
                                        </td> 
                                        <td>
                                        <?php 
                                        $checkaTime = $this->Management_model->CheckAutoTaskTime($teamuid,$previousDate);
                                    
                                        if(sizeof($checkaTime) > 0){

                                            echo '<b>Start Time : </b>'.$checkaTime[0]->stime;
                                            echo "<hr>";
                                            echo '<b>End Time : </b>'.$checkaTime[0]->etime ;
                                            echo "<br/> <hr/>";

                                            $start = new DateTime($checkaTime[0]->stime);
                                            $end = new DateTime($checkaTime[0]->etime);
                                            $interval = $start->diff($end);
                                            $minutes = ($interval->h * 60) + $interval->i;
                                            echo "Time Difference: $minutes minutes</b>";
                                        }
                                        ?>
                                            </td> 
                                            <td>
                                                <?php 
                                                $checkReqTime = $this->Management_model->CheckTaskPlanRequest($teamuid,$previousDate);
                                                if(sizeof($checkReqTime) > 0){ ?>
                                                  <b>Request Reamarks : </b><?= $checkReqTime[0]->request_remarks ?> <hr>
                                                  <b>Approvel Status :<br/><br> </b> 
                                                  <?php if($checkReqTime[0]->approvel_status == 'Approved'){ ?>
                                                  <span class="bg-success p-1"> <?= $checkReqTime[0]->approvel_status ?></span>
                                                  <?php } ?>

                                                  <?php if($checkReqTime[0]->approvel_status == 'Reject'){ ?>
                                                  <span class="bg-danger p-1"> <?= $checkReqTime[0]->approvel_status ?></span>
                                                  <?php } ?>
                                                  <?php if($checkReqTime[0]->approvel_status == ''){ ?>
                                                    <span class="bg-warning p-1"> Pending</span>
                                                  <?php } ?>
                                                  <hr>
                                                  <b>Admin Reamarks : </b> <?= $checkReqTime[0]->remarks ?>
                                                <?php }else{
                                                    echo "No Request";
                                                }
                                                ?>
                                            </td> 
                                            <td>
                                                <?php 
                                                $checkcloseday = $this->Management_model->CheckingYesterDayConsumeTime($teamuid,$previousDate);
                                                if(isset($checkcloseday[0]->usimg)){ ?>
                                                <a href="<?=base_url().'/'.$checkcloseday[0]->usimg;?>">
                                                    <img class="uimage" height="100px" alt="image not found" src="<?=base_url().'/'.$checkcloseday[0]->usimg;?>">
                                                </a>
                                                <?php }else{ ?>
                                                    <span class="bg-danger p-1"> Not Set</span>
                                                <?php }?>
                                            </td> 
                                            <td>
                                                <?php if(isset($checkcloseday[0]->scomment)){ ?>
                                                    <?= $checkcloseday[0]->scomment ?>
                                                <?php }else{ ?>
                                                    <span class="bg-danger p-1"> Not Set</span>
                                                <?php }?>
                                            </td> 
                                            <td>
                                                <?php 
                                                if(isset($checkcloseday[0]->ucimg)){ ?>
                                                <a href="<?=base_url().'/'.$checkcloseday[0]->ucimg;?>">
                                                    <img class="uimage" height="100px" alt="image not found" src="<?=base_url().'/'.$checkcloseday[0]->ucimg;?>">
                                                </a>
                                                <?php }else{ ?>
                                                    <span class="bg-danger p-1"> Not Set</span>
                                                <?php }?>
                                            </td> 
                                            <td>
                                                <?php if(isset($checkcloseday[0]->ccomment)){ ?>
                                                    <?= $checkcloseday[0]->ccomment ?>
                                                <?php }else{ ?>
                                                    <span class="bg-danger p-1"> Not Set</span>
                                                <?php }?>
                                            </td> 
                                            <td>
                                           <?php 
                                           $clatitude = $checkcloseday[0]->clatitude;;
                                           $clongitude = $checkcloseday[0]->clongitude;;
                                           if(isset($clatitude)){ ?>
                                           <div class="img-thumbnail" style="height: 300px"><iframe width="300px"  height="100%" src="https://maps.google.com/?q=<?=$clatitude?>,<?=$clongitude?>&t=k&z=13&ie=UTF8&iwloc=&output=embed"></iframe></div>
                                            <div class="text-center">
                                            <?php 
                                            $googleMapsUrl = "https://www.google.com/maps/search/?api=1&query={$clatitude},{$clongitude}";
                                            echo "<a style='color:green' href='{$googleMapsUrl}' target='_blank'>Open in Google Maps</a>";
                                            ?>
                                            </div>
                                            <?php }else{ ?>
                                                <span class="bg-danger p-1"> Not Set</span>
                                           <?php } ?>
                                           </td> 
                                           <td>
                                       

                                        <?php 
                                            $chkStarRating = $this->Management_model->CheckEveningStarRatingsExistorNot($teamuid,$previousDate);
                                            if(sizeof($chkStarRating) == 0){
                                            ?>
                                            <p><a href="javascript:void(0)" onclick="feedBackButton(<?= $i ?>,<?= $teamuid?>,'yesterdayEveningfeedBack')" class="btn" style="background:#6a0d83">Add Yesterday Evening FeedBack</a></p>
                                            <?php  }else{
                                                $feedbackby ='' ;
                                                $totalStar = '';
                                            foreach($chkStarRating as $star){  
                                                $feedbackby = $star->feedback_by;
                                                $totalStar += $star->star;
                                                $feedbackrem = $star->remarks;
                                            ?>
                                                <span class="text-success12"> <b><?= $star->question ?> : </b>  <?= $star->star ?> Star </span> <hr>
                                            <?php }
                                            $udetail = $this->Menu_model->get_userbyid($feedbackby);
                                            $feedbackbyname = $udetail[0]->name;
                                            echo "<span class='text-success12'><b>Feedback By</b> : ".$feedbackbyname."</span><hr/>";
                                            echo "<span class='bg-success p-1'><b>Feedback&nbsp;Status</b>&nbsp;:&nbsp;Suceess</span><hr/>";
                                            echo "<span class='text-success12'><b>Remarks</b> : ".$feedbackrem."</span><hr/>";
                                            echo "<span class='text-success12'><b>Total Star</b> : ".$totalStar." Star</span> <hr/>";
                                            }  ?>
                                        </td>
                                        <td style="background:#fff;"></td>
                                         <td>
                                            <?php $dayData = $this->Management_model->CheckingYesterDayTaskStatus($teamuid); ?>
                                                <a class="text-primary" target="_BLANK" href="<?= base_url().'Management/CheckingYesterDayTask/total/'.$teamuid.'/'.$cdate ?>"><?= $dayData[0]->plan ?></a>
                                            </td>    
                                            <td>
                                            <a class="text-primary" target="_BLANK" href="<?= base_url().'Management/CheckingYesterDayTask/Pending/'.$teamuid.'/'.$cdate ?>"><?= $dayData[0]->pending ?></a>
                                            </td>    
                                            <td>
                                            <a class="text-primary" target="_BLANK" href="<?= base_url().'Management/CheckingYesterDayTask/autotask/'.$teamuid.'/'.$cdate ?>"><?= $dayData[0]->autotask ?></a>
                                        </td> 
                                        <td>   
                                            <a class="text-primary" target="_BLANK" href="<?= base_url().'Management/CheckingYesterDayTask/done/'.$teamuid.'/'.$cdate ?>"><?= $dayData[0]->done ?></a>
                                        </td>  
                                        <td>
                                        <?php 
                                        $dayconsumetime = $this->Management_model->CheckingYesterDayConsumeTime($teamuid,$previousDate);

                                        $ustart = $dayconsumetime[0]->ustart;
                                        $uclose = $dayconsumetime[0]->uclose;
                                        $sst = (isset($ustart)) ? $ustart : 'Not Set';
                                        $eet = (isset($uclose)) ? $uclose : 'Not Set';
                                        // echo '<b>Start Time : </b>'.$sst;
                                        // echo "<hr>";
                                        // echo '<b>End Time : </b>'.$eet ;
                                        // echo "<br/> <hr/>";
                                        $start = new DateTime($ustart);
                                        $end = new DateTime($uclose);
                                        $interval = $start->diff($end);
                                        $minutesd = ($interval->h * 60) + $interval->i;
                                        $hours = floor($minutesd / 60);
                                        // Calculate the remaining minutes
                                        $minutes = $minutesd % 60;
                                        if(isset($uclose)){
                                            echo "$hours hours and $minutes minutes.\n";
                                        }else{ ?>
                                            <p class="text-danger p-2 ">* Time will be Calculate, if user set his End Time.</p>
                                            <span class="bg-danger p-1"> <b>End&nbsp;Time&nbsp;:&nbsp;</b>&nbsp;Not&nbsp;Set</span>
                                        <?php }  ?>
                                        </td>  
                                        <td>
                                        
                                        <?php 
                                            $chkStarRating = $this->Management_model->CheckYestTaskStarRatingsExistorNot($teamuid,$previousDate);
                                            if(sizeof($chkStarRating) == 0){
                                            ?>
                                           <p><a href="javascript:void(0)" onclick="feedBackButton(<?= $i ?>,<?= $teamuid?>,'yesterdayTaskfeedBack')" class="btn" style="background:#db2244" >Add Yesterday Task FeedBack</a></p>
                                            <?php  }else{
                                                $feedbackby ='' ;
                                                $totalStar = '';
                                            foreach($chkStarRating as $star){  
                                                $feedbackby = $star->feedback_by;
                                                $totalStar += $star->star;
                                                $feedbackrem = $star->remarks;
                                            ?>
                                                <span class="text-success12"> <b><?= $star->question ?> : </b>  <?= $star->star ?> Star </span> <hr>
                                            <?php }
                                            $udetail = $this->Menu_model->get_userbyid($feedbackby);
                                            $feedbackbyname = $udetail[0]->name;
                                            echo "<span class='text-success12'><b>Feedback By</b> : ".$feedbackbyname."</span><hr/>";
                                            echo "<span class='bg-success p-1'><b>Feedback&nbsp;Status</b>&nbsp;:&nbsp;Suceess</span><hr/>";
                                            echo "<span class='text-success12'><b>Remarks</b> : ".$feedbackrem."</span><hr/>";
                                            echo "<span class='text-success12'><b>Total Star</b> : ".$totalStar." Star</span> <hr/>";
                                            }  ?>
                                    
                                    
                                    
                                    </td>
                                        </tr>      
                                        <?php $i++; endforeach; ?>
                                  </tbody>
                                </table> 
                            </div>
                        </div>


                <!-- Morning Modal -->
                <div class="modal fade" id="exampleModalCenter" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                    <div class="modal-header bg-success text-center text-white">
                        <h5 class="modal-title" id="exampleModalCenterTitle">Add Todays Mornings FeedBack</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                        </button>
                    </div>

                    <div class="modal-body" style="background: #02a1d8;color: white;" >
                    <form action="<?=base_url();?>Management/checkdayswithStar" method="post">
                     <input type="hidden" id="selectedusermorning" name="udid" value="">
                     <input type="hidden" id="periods" name="periods" value="Mornings">
                     <input type="hidden" id="cdate" name="cdate" value="<?=$cdate ?>">
                     <input type="hidden" id="previousDate" name="previousDate" value="<?=$previousDate ?>">
                     <div class="from-group">
                                <h5 class="text-center"><input type="hidden" name="que[]" value="Started at Good Time">Started at Good Time</h5>
                                <div class="rating">
                                    <input type="radio" name="rat1" value="5" id="5"><label for="5">☆</label>
                                    <input type="radio" name="rat1" value="4" id="4"><label for="4">☆</label>
                                    <input type="radio" name="rat1" value="3" id="3"><label for="3">☆</label>
                                    <input type="radio" name="rat1" value="2" id="2"><label for="2">☆</label>
                                    <input type="radio" name="rat1" value="1" id="1"><label for="1">☆</label>
                                </div>
                            </div>
                            <div class="from-group">
                                <h5 class="text-center"><input type="hidden" name="que[]" value="Today Working as per Plan">Today Working as per Plan</h5>
                                <div class="rating">
                                    <input type="radio" name="rat2" value="5" id="10"><label for="10">☆</label>
                                    <input type="radio" name="rat2" value="4" id="9"><label for="9">☆</label>
                                    <input type="radio" name="rat2" value="3" id="8"><label for="8">☆</label>
                                    <input type="radio" name="rat2" value="2" id="7"><label for="7">☆</label>
                                    <input type="radio" name="rat2" value="1" id="6"><label for="6">☆</label>
                                </div>
                            </div>
                            <div class="from-group">
                                <h5 class="text-center"><input type="hidden" name="que[]" value="Day Start Image is Good">Day Start Image is Good</h5>
                                <div class="rating">
                                    <input type="radio" name="rat3" value="5" id="11"><label for="11">☆</label>
                                    <input type="radio" name="rat3" value="4" id="12"><label for="12">☆</label>
                                    <input type="radio" name="rat3" value="3" id="13"><label for="13">☆</label>
                                    <input type="radio" name="rat3" value="2" id="15"><label for="14">☆</label>
                                    <input type="radio" name="rat3" value="1" id="14"><label for="15">☆</label>
                                </div>
                            </div>
                            <div class="from-group">
                                <h5 class="text-center"><input type="hidden" name="que[]" value="Day Start Location as per Plan">Day Start Location as per Plan</h5>
                                <div class="rating">
                                    <input type="radio" name="rat4" value="5" id="16"><label for="16">☆</label>
                                    <input type="radio" name="rat4" value="4" id="17"><label for="17">☆</label>
                                    <input type="radio" name="rat4" value="3" id="18"><label for="18">☆</label>
                                    <input type="radio" name="rat4" value="2" id="19"><label for="19">☆</label>
                                    <input type="radio" name="rat4" value="1" id="20"><label for="20">☆</label>
                                </div>
                            </div>
                            <div class="from-group">
                                <h5 class="text-center"><input type="hidden" name="que[]" value="Auto task time entered correctly">Auto task time entered correctly </h5>
                                <div class="rating">
                                    <input type="radio" name="rat5" value="5" id="21"><label for="21">☆</label>
                                    <input type="radio" name="rat5" value="4" id="22"><label for="22">☆</label>
                                    <input type="radio" name="rat5" value="3" id="23"><label for="23">☆</label>
                                    <input type="radio" name="rat5" value="2" id="24"><label for="24">☆</label>
                                    <input type="radio" name="rat5" value="1" id="25"><label for="25">☆</label>
                                </div>
                            </div>
                            <div class="from-group">
                                <h5 class="text-center"><input type="hidden" name="que[]" value="Did you request today's work plan?">Did you request today's work plan? </h5>
                                <div class="rating">
                                    <input type="radio" name="rat6" value="5" id="26"><label for="26">☆</label>
                                    <input type="radio" name="rat6" value="4" id="27"><label for="27">☆</label>
                                    <input type="radio" name="rat6" value="3" id="28"><label for="28">☆</label>
                                    <input type="radio" name="rat6" value="2" id="29"><label for="29">☆</label>
                                    <input type="radio" name="rat6" value="1" id="30"><label for="30">☆</label>
                                </div>
                            </div>


                            <textarea class="form-control" name="sremark" placeholder="Remark" required=""></textarea>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-success">Submit</button>
                        <br>
                    </div>
                    </form>
                    </div>
                </div>
                </div>



  <!-- YesterDay Evening Modal -->
  <div class="modal fade" id="yesterDayeveningModalCenter" tabindex="1" role="dialog" aria-labelledby="yesterDayeveningexampleModalCenterTitle" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered" role="document">
                    <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title text-center" id="yesterDayeveningexampleModalCenterTitle">Add Yesterday Evening FeedBack</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body" style="background: #6a0d83;color:white;" >
                    <form action="<?=base_url();?>Management/checkdayswithStar" method="post">
                     <input type="hidden" id="selecteduserYevening" name="udid" value="">
                     <input type="hidden" id="cdate" name="cdate" value="<?=$cdate ?>">
                     <input type="hidden" id="periods" name="periods" value="Yesterday Evening">
                     <input type="hidden" id="previousDate" name="previousDate" value="<?=$previousDate ?>">
                     <div class="from-group">
                                <h5 class="text-center"><input type="hidden" name="que[]" value="The Day Ended at a good time">The Day Ended at a good time</h5>
                                <div class="rating">
                                    <input type="radio" name="raty1" value="5" id="yeste5"><label for="yeste5">☆</label>
                                    <input type="radio" name="rat1" value="4" id="yeste4"><label for="yeste4">☆</label>
                                    <input type="radio" name="rat1" value="3" id="yeste3"><label for="yeste3">☆</label>
                                    <input type="radio" name="rat1" value="2" id="yeste2"><label for="yeste2">☆</label>
                                    <input type="radio" name="rat1" value="1" id="yeste1"><label for="yeste1">☆</label>
                                </div>
                            </div>
                            <div class="from-group">
                                <h5 class="text-center"><input type="hidden" name="que[]" value="The Day Ended Working as per Plan">The Day Ended Working as per Plan</h5>
                                <div class="rating">
                                    <input type="radio" name="rat2" value="5" id="yeste6"><label for="yeste6">☆</label>
                                    <input type="radio" name="rat2" value="4" id="yeste7"><label for="yeste7">☆</label>
                                    <input type="radio" name="rat2" value="3" id="yeste8"><label for="yeste8">☆</label>
                                    <input type="radio" name="rat2" value="2" id="yeste9"><label for="yeste9">☆</label>
                                    <input type="radio" name="rat2" value="1" id="yeste10"><label for="yeste10">☆</label>
                                </div>
                            </div>
                            <div class="from-group">
                                <h5 class="text-center"><input type="hidden" name="que[]" value="Day End Image is Good">Day End Image is Good</h5>
                                <div class="rating">
                                    <input type="radio" name="rat3" value="5" id="yeste11"><label for="yeste11">☆</label>
                                    <input type="radio" name="rat3" value="4" id="yeste12"><label for="yeste12">☆</label>
                                    <input type="radio" name="rat3" value="3" id="yeste13"><label for="yeste13">☆</label>
                                    <input type="radio" name="rat3" value="2" id="yeste15"><label for="yeste14">☆</label>
                                    <input type="radio" name="rat3" value="1" id="yeste14"><label for="yeste15">☆</label>
                                </div>
                            </div>
                            <div class="from-group">
                                <h5 class="text-center"><input type="hidden" name="que[]" value="Day End Location as per Plan">Day End Location as per Plan</h5>
                                <div class="rating">
                                    <input type="radio" name="rat4" value="5" id="yeste16"><label for="yeste16">☆</label>
                                    <input type="radio" name="rat4" value="4" id="yeste17"><label for="yeste17">☆</label>
                                    <input type="radio" name="rat4" value="3" id="yeste18"><label for="yeste18">☆</label>
                                    <input type="radio" name="rat4" value="2" id="yeste19"><label for="yeste19">☆</label>
                                    <input type="radio" name="rat4" value="1" id="yeste20"><label for="yeste20">☆</label>
                                </div>
                            </div>
                            <textarea class="form-control" name="sremark" placeholder="Remark" required=""></textarea>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-success">Submit</button>
                    </div>
                    </form>
                    </div>
                </div>
                </div>


                <!-- YesterDay Evening Modal -->
  <div class="modal fade" id="taskModalCenter" tabindex="1" role="dialog" aria-labelledby="taskModalCenterTitle" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered" role="document">
                    <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="taskModalCenterTitle">Add Yesterday Task FeedBack</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body" style="background:#db2244;color:white">
                    <form action="<?=base_url();?>Management/checkdayswithStar" method="post">
                     <input type="hidden" id="selecteduserytask" name="udid" value="">
                     <input type="hidden" id="cdate" name="cdate" value="<?=$cdate ?>">
                     <input type="hidden" id="periods" name="periods" value="Yesterday Task">
                     <input type="hidden" id="previousDate" name="previousDate" value="<?=$previousDate ?>">
                     <div class="from-group">
                                <h5 class="text-center"><input type="hidden" name="que[]" value="Yesterday Total Plan Task is Good">Yesterday Total Plan Task is Good</h5>
                                <div class="rating">
                                    <input type="radio" name="rat1" value="5" id="yesttask5"><label for="yesttask5">☆</label>
                                    <input type="radio" name="rat1" value="4" id="yesttask4"><label for="yesttask4">☆</label>
                                    <input type="radio" name="rat1" value="3" id="yesttask3"><label for="yesttask3">☆</label>
                                    <input type="radio" name="rat1" value="2" id="yesttask2"><label for="yesttask2">☆</label>
                                    <input type="radio" name="rat1" value="1" id="yesttask1"><label for="yesttask1">☆</label>
                                </div>
                            </div>
                            <div class="from-group">
                                <h5 class="text-center"><input type="hidden" name="que[]" value="Yesterday Total Pending Task is Good">Yesterday Total Pending Task is Good</h5>
                                <div class="rating">
                                    <input type="radio" name="rat2" value="5" id="yesttask10"><label for="yesttask10">☆</label>
                                    <input type="radio" name="rat2" value="4" id="yesttask9"><label for="yesttask9">☆</label>
                                    <input type="radio" name="rat2" value="3" id="yesttask8"><label for="yesttask8">☆</label>
                                    <input type="radio" name="rat2" value="2" id="yesttask7"><label for="yesttask7">☆</label>
                                    <input type="radio" name="rat2" value="1" id="yesttask6"><label for="yesttask6">☆</label>
                                </div>
                            </div>
                            <div class="from-group">
                                <h5 class="text-center"><input type="hidden" name="que[]" value="Yesterday Total Autotask Task is Good">Yesterday Total Autotask Task is Good</h5>
                                <div class="rating">
                                    <input type="radio" name="rat3" value="5" id="yesttask11"><label for="yesttask11">☆</label>
                                    <input type="radio" name="rat3" value="4" id="yesttask12"><label for="yesttask12">☆</label>
                                    <input type="radio" name="rat3" value="3" id="yesttask13"><label for="yesttask13">☆</label>
                                    <input type="radio" name="rat3" value="2" id="yesttask15"><label for="yesttask14">☆</label>
                                    <input type="radio" name="rat3" value="1" id="yesttask14"><label for="yesttask15">☆</label>
                                </div>
                            </div>
                            <div class="from-group">
                                <h5 class="text-center"><input type="hidden" name="que[]" value="Yesterday Total Done Task is Good">Yesterday Total Done Task is Good</h5>
                                <div class="rating">
                                    <input type="radio" name="rat4" value="5" id="yesttask16"><label for="yesttask16">☆</label>
                                    <input type="radio" name="rat4" value="4" id="yesttask17"><label for="yesttask17">☆</label>
                                    <input type="radio" name="rat4" value="3" id="yesttask18"><label for="yesttask18">☆</label>
                                    <input type="radio" name="rat4" value="2" id="yesttask19"><label for="yesttask19">☆</label>
                                    <input type="radio" name="rat4" value="1" id="yesttask20"><label for="yesttask20">☆</label>
                                </div>
                            </div>
                            <textarea class="form-control" name="sremark" placeholder="Remark" required=""></textarea>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-success">Submit</button>
                    </div>
                    </form>
                    </div>
                </div>
                </div>


                    </div>
                  </div>
                </div>
              </div>
            </div>
            <!-- /.card-body -->
          </div>
          <!-- /.card -->
      </div>
      <!-- /.col -->
    </div>
    <!-- /.row -->
    </div>
    <!-- /.container-fluid -->
    </section>
    </div>    
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
  

    <script type='text/javascript'>

function feedBackButton(mid,id,val){
    // alert(val); // MorningsfeedBack
    if(val == 'MorningsfeedBack'){
        $('#exampleModalCenter').modal('show');
        $('#selectedusermorning').val(id);
    }

    if(val == 'yesterdayEveningfeedBack'){
        // alert(val);
        $('#yesterDayeveningModalCenter').modal('show');
        $('#selecteduserYevening').val(id);
    }
    if(val == 'yesterdayTaskfeedBack'){
        // alert(val);
        $('#taskModalCenter').modal('show');
        $('#selecteduserytask').val(id);
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
        "responsive": false, "lengthChange": false, "autoWidth": false,
        "buttons": ["csv", "excel", "pdf", "print"]
      }).buttons().container().appendTo('#example1_wrapper .col-md-6:eq(0)');
    </script>
  </body>
</html>
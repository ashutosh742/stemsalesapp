<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Task Check | STEM Operation | WebAPP</title>
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
</head>
<style>


.circle-image img{

	border: 6px solid #fff;
    border-radius: 100%;
    padding: 0px;
    top: -28px;
    position: relative;
    width: 70px;
    height: 70px;
    border-radius: 100%;
    z-index: 1;
    background: #e7d184;
    cursor: pointer;

}

.dot {
      height: 18px;
    width: 18px;
    background-color: blue;
    border-radius: 50%;
    display: inline-block;
    position: relative;
    border: 3px solid #fff;
    top: -48px;
    left: 186px;
    z-index: 1000;
}

.name{
	margin-top: -21px;
	font-size: 18px;
}


.fw-500{
	font-weight: 500 !important;
}


.start{

	color: green;
}

.stop{
	color: red;
}

.success-message {
    /* color: green; */
    display: none;
    /* margin-top: 10px; */
}
#tabs .nav-tabs .nav-item.show .nav-link, .nav-tabs .nav-link.active {
        color: black;
        background-color: #ffc107;
        border-color: transparent transparent #f3f3f3;
        border-bottom: 3px solid !important;
        font-size: 16px;
        font-weight: bolder;
    }
    .nav-tabs .nav-link{
        background-color: lightgray;
        color: black;
        font-weight: 600;
    }
    .nav-tabs .nav-link:hover{
        background-color: #3498db;
        color: black;
        border-bottom: 3px solid !important;
        font-size: 16px;
        font-weight: bolder;
    }
    .question{
        color: black;
        font-weight: 600;
    }
    .star-rating {
            color: #f39c12;
            font-size:20px;
    }
    .rate{border-bottom-right-radius: 12px;border-bottom-left-radius: 12px;}.rating {display: flex;flex-direction: row-reverse;justify-content: left }.rating>input {display: none }.rating>label {position: relative;width: 1em;font-size: 30px;font-weight: 300;color: #f39c12;cursor: pointer }.rating>label::before {content: "\2605";position: absolute;opacity: 0 }.rating>label:hover:before, .rating>label:hover~label:before {opacity: 1 !important }.rating>input:checked~label:before {opacity: 1 }.rating:hover>input:checked~label:before {opacity: 0.4 }

    .MoMrate{border-bottom-right-radius: 12px;border-bottom-left-radius: 12px;}.MoMrating {display: flex;flex-direction: row-reverse;justify-content: left }.MoMrating>input {display: none }.MoMrating>label {position: relative;width: 1em;font-size: 30px;font-weight: 300;color: #f39c12;cursor: pointer }.MoMrating>label::before {content: "\2605";position: absolute;opacity: 0 }.MoMrating>label:hover:before, .MoMrating>label:hover~label:before {opacity: 1 !important }.MoMrating>input:checked~label:before {opacity: 1 }.MoMrating:hover>input:checked~label:before {opacity: 1 }

    .remark-box {
            /* display: hidden; Hidden by default */
            visibility: hidden;
            margin-top: 10px;
        }

        .remark-box textarea {
            width: 100%;
        }

        .disabled-fieldset {
            pointer-events: none; /* Disable interactions */
            opacity: 1; /* Visually indicate disabled state */
        }
        
        .remark-box.visible {
            visibility: visible; /* Show the remark box */
        }
    /* Star Rating CSS */

tr,td{
    font-weight: 600;
}
</style>

<body class="hold-transition sidebar-mini layout-fixed">
<div class="wrapper">

  <!-- Preloader -->
  

  <!-- Navbar -->
  <?php require('nav.php');
  
  ?>
  <!-- /.navbar -->
  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            
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
 <?php // var_dump($selectedUser);die; ?>
    <section class="content">
      <div class="container-fluid">
      <div class="alert alert-success" id="success-message" style="display: none;">Thank you for your rating!</div>
       <div class="row p-3">
           <div class="col-sm col-md-12 col-lg-12 m-auto">
              	<div class="card card-success card-outline">
					<div class="card-body box-profile ">
                        <h3 class="text-center">Task Check</h3>
                        <h6 class="text-center"><?=$cdate?></h6>
                        <hr>
                        <form action="<?=base_url();?>Menu/TaskCheck_New" method="post" id="taskForm">
                            <div class="row">
                            <?php if($ctaskid != '' && !is_null($ctaskid)){ ?>
                                <input type="hidden" name="taskid" value="<?= $ctaskid ?>">
                            <?php } ?>
                                <div class="col-md-4">
                                    <select class="form-control" name="userId" <?php if (!empty($selectedUser)) echo 'disabled'; ?>>
                                        <option selected disabled>--Select--</option>
                                    <?php 
                                    foreach($userList as $user){?>

                                        <option value="<?=$user->user_id?>" <?php if ($user->user_id == $selectedUser) echo ' selected="selected"';?>><?=$user->name?></option>

                                    <?php } ?>
                                    </select>    
                                </div>
                                <div class="col-md-4">
                                    <button type="submit" class="btn btn-primary">Filter</button>
                                </div>
                                <div class="col-md-4">
                                    <button type="button" class="btn btn-danger" onclick="clearSelectedUser()">Final Submit</button>
                                </div>
                            </div> 
                        </form>

                        <br>

                        <div class="table-responsive text-nowrap" id="tbdata">
                            <table id="PlannerTable" class="table table-striped table-bordered" cellspacing="0" width="100%">
                                <thead class="thead-dark">
                                    <tr>
                                        <!-- <th>#</th> -->
                                        <th>Action Planned</th>
                                        <th>CID</th>
                                        <th>Company Name</th>
                                        <th>Planner Filter Used</th>
                                        <th>Planned Date-time</th>
                                        <th>Initiated Date-time</th>
                                        <th>Update Date-time</th>
                                        <th>Action And Purpose </th>
                                        <th>Status</th>
                                        <!-- <th>Remark</th> -->
                                        <th>Same Status Since</th>
                                        <th>Number of tasks on same status</th>
                                        <!-- <th>Auto task Details</th> -->
                                        <!-- <th>MoM Details</th> -->
                                    </tr>
                                </thead>
                                <tbody>
                                <?php 
                                    $i=1; 
                                    foreach($taskList as $task){

                                        $r = rand(150, 255);
                  $g = rand(150, 255);
                  $b = rand(150, 255);
                  $backgroundColor = "rgba($r, $g, $b,.2)";

                  $hue = rand(0, 360); // Full color wheel
                  $saturation = rand(60, 100); // High saturation for rich colors
                  $lightness = rand(30, 45); // Low lightness for a deeper tone
                  
                  $backgroundColorNew = "hsl($hue, $saturation%, $lightness%)";

                                        $getLastActionDetails = getLastActionDetails($task->tid,$task->user_id,$cdate);

                                        $getSameStatusSince = getSameStatusSince($task->tid,$cdate);
                                        if (!empty($getSameStatusSince)) {

                                            $SameStatusSince_1 = $getSameStatusSince->days_difference;
                                            $NoOfTaskSinceStatusChange = $getSameStatusSince->Taskcount;
                                        }else{

                                            $SameStatusSince_1 = '0';
                                            $NoOfTaskSinceStatusChange = '0';
                                        }

                                        if ($task->nextCFID != 0) {
                                            
                                            $getNextActionDetails= $this->Menu_model->getActionDetails($task->nextCFID);
                                            $nextAction = $getNextActionDetails->action_name;
                                            
                                            if ($getNextActionDetails->autotask == 1) {
                                                
                                                $TaskType = ' - It is autotask';

                                            }else{
                                                $TaskType = 'This is not autotask ';
                                            }

                                        }else {
                                            $nextAction = '';
                                        }

                                        if($task->autotask == 1){
                                            $OGTaskType = ' - It is autotask';
                                        }else{
                                            $OGTaskType = ''; 
                                        }
                                        $filterUsed = ($task->filter_by);
                                        $filterUsed = json_decode($filterUsed, true);
                                        
                                        $SinglefilterUsedFinal = '';
                                        if (is_array($filterUsed)) {

                                            foreach ($filterUsed as $key => $SinglefilterUsed) {

                                                if ($key === 'Plan_BY') {

                                                    $SinglefilterUsedFinal = 'Plan By - '.$SinglefilterUsed;

                                                }elseif ($key === 'comp_status' ) {

                                                    $SinglefilterUsedFinal = get_CompanyStatus($task->company_id,$SinglefilterUsed);

                                                    $SinglefilterUsedFinal = 'Company Status - '.$SinglefilterUsedFinal->name;
                                                }
                                                elseif ($key === 'task' ) {
                                                    //$SinglefilterUsedFinal = get_CompanyStatus($task->company_id,$SinglefilterUsed);
                                                    // $SinglefilterUsedFinal = 'Company Status - '.$SinglefilterUsedFinal->name;
                                                    $tactionarray = $this->Menu_model->get_actionbyid($SinglefilterUsed);
                                                    $tactionname  = $tactionarray[0]->name;
                                                    $SinglefilterUsedFinal = 'Task - '. $tactionname;
                                                    
                                                }
                                                else{
                                                    
                                                    $SinglefilterUsedFinal = $SinglefilterUsed;
                                                }
                                                // if 
                                            }
                                        }                                        
                                ?>
                                    <tr style="background-color: <?php echo $backgroundColor; ?>;color:<?=$backgroundColorNew;?>!important">
                                        <!-- <td><?= $i; ?></td> -->
                                        <td><?= $task->action_name; ?>
                                            <b><?= $OGTaskType; ?></b> <hr>
                                            <?php
                                                if ($task->actiontype_id == 3 || $task->actiontype_id == 4 ) { ?>
                                                    <button type="button" class="btn btn-primary" onclick="OpenModal(<?=$task->tid?>,<?=$task->user_id?>)">View Meeting Details</button>
                                            <?php   }  ?>
                                            <?php
                                                if ($task->actiontype_id == 8 ) { ?>
                                                    <button type="button" class="btn btn-primary" onclick="OpenReviewModal(<?=$task->tid?>,<?=$task->user_id?>)">View Review Details</button>
                                            <?php   }  ?>
                                            <?php 

                                                if ($task->actiontype_id == 2 && $task->autotask == 1) { ?>
                                                   
                                                    <p class="question">Was email sent..??</p>
                                                <?php    $chkStarRating = $this->Menu_model->CheckTaskStarRatingsExistorNot_New($task->user_id,'was email sent',$task->tid);
                                           
                                                if(sizeof($chkStarRating) == 0){ ?>

                                                    <div class="rating" data-question="was email sent" data-userid="<?= $task->user_id; ?>" data-taskid="<?= $task->tid; ?>" >
                                                        <input type="radio" name="rat20_<?= $task->user_id; ?>_<?= $task->tid; ?>" value="5" id="90_<?= $task->tid; ?>"><label for="90_<?= $task->tid; ?>">☆</label>
                                                        <input type="radio" name="rat20_<?= $task->user_id; ?>_<?= $task->tid; ?>" value="4" id="91_<?= $task->tid; ?>"><label for="91_<?= $task->tid; ?>">☆</label>
                                                        <input type="radio" name="rat20_<?= $task->user_id; ?>_<?= $task->tid; ?>" value="3" id="92_<?= $task->tid; ?>"><label for="92_<?= $task->tid; ?>">☆</label>
                                                        <input type="radio" name="rat20_<?= $task->user_id; ?>_<?= $task->tid; ?>" value="2" id="93_<?= $task->tid; ?>"><label for="93_<?= $task->tid; ?>">☆</label>
                                                        <input type="radio" name="rat20_<?= $task->user_id; ?>_<?= $task->tid; ?>" value="1" id="94_<?= $task->tid; ?>"><label for="94_<?= $task->tid; ?>">☆</label>
                                                    </div>

                                            <?php }else{
                                                foreach($chkStarRating as $star){
                                               
                                                    $starRating = $star->star;
                                                    $starRemark = $star->remarks;
                                                    
                                                }
                                                echo "<hr>";
                                                echo "<span class='text-dark font-weight-normal'><b>Total Star Given</b> :</span>";
                                                echo "<div class='star-rating'>";
                                                $totalStars = 5;
                                                for ($i = 0; $i < $starRating; $i++) {
                                                    echo "<i class='fas fa-star'></i>"; // filled star
                                                }
                                                for ($i = $starRating; $i < $totalStars; $i++) {
                                                    echo "<i class='far fa-star'></i>"; // empty star
                                                }
                                                echo "</div><br><span class='text-dark font-weight-normal'><b>Remark</b> :".$starRemark."</span>";
                                            }  

                                                }
                                                
                                            
                                            ?>
                                        </td> 
                                        <td><a target="_BLANK" href="<?=base_url()?>Menu/CompanyDetails/<?= $task->cid; ?>"><?= $task->cid; ?></a></td>
                                        <td><a target="_BLANK" href="<?=base_url()?>Menu/CompanyDetails/<?= $task->cid; ?>"><?= $task->compname;?></a></td>
                                        <td><?php 
                                        if($SinglefilterUsedFinal == ""){
                                            echo $task->selectby;
                                        }else{
                                            echo $SinglefilterUsedFinal;
                                        }
                                         ?>
                                            <br><hr>
                                            <p class="question">Was Correct filters used..??</p>

                                            <?php 

                                                $chkStarRating = $this->Menu_model->CheckTaskStarRatingsExistorNot_New($task->user_id,'Was Correct filters used',$task->tid);
                                           
                                                if(sizeof($chkStarRating) == 0){ ?>

                                                    <div class="rating" data-question="Was Correct filters used" data-userid="<?= $task->user_id; ?>" data-taskid="<?= $task->tid; ?>" >
                                                        <input type="radio" name="rat8_<?= $task->user_id; ?>_<?= $task->tid; ?>" value="5" id="40_<?= $task->tid; ?>"><label for="40_<?= $task->tid; ?>">☆</label>
                                                        <input type="radio" name="rat8_<?= $task->user_id; ?>_<?= $task->tid; ?>" value="4" id="39_<?= $task->tid; ?>"><label for="39_<?= $task->tid; ?>">☆</label>
                                                        <input type="radio" name="rat8_<?= $task->user_id; ?>_<?= $task->tid; ?>" value="3" id="38_<?= $task->tid; ?>"><label for="38_<?= $task->tid; ?>">☆</label>
                                                        <input type="radio" name="rat8_<?= $task->user_id; ?>_<?= $task->tid; ?>" value="2" id="37_<?= $task->tid; ?>"><label for="37_<?= $task->tid; ?>">☆</label>
                                                        <input type="radio" name="rat8_<?= $task->user_id; ?>_<?= $task->tid; ?>" value="1" id="36_<?= $task->tid; ?>"><label for="36_<?= $task->tid; ?>">☆</label>
                                                    </div>

                                            <?php }else{
                                                foreach($chkStarRating as $star){
                                               
                                                    $starRating = $star->star;
                                                    $starRemark = $star->remarks;
                                                    
                                                }
                                                echo "<hr>";
                                                echo "<span class='text-dark font-weight-normal'><b>Total Star Given</b> :</span>";
                                                echo "<div class='star-rating'>";
                                                $totalStars = 5;
                                                for ($i = 0; $i < $starRating; $i++) {
                                                    echo "<i class='fas fa-star'></i>"; // filled star
                                                }
                                                for ($i = $starRating; $i < $totalStars; $i++) {
                                                    echo "<i class='far fa-star'></i>"; // empty star
                                                }
                                                echo "</div><br><span class='text-dark font-weight-normal'><b>Remark</b> :".$starRemark."</span>";
                                            }  ?>
                                        </td>
                                                                           
                                        <td >
                                            
                                        <b>Date : </b><?= $task->plan_date; ?>
                                            <br><br><hr>
                                            <p class="question">Was Task Planned on time..??</p>
                                            <?php 

                                                $chkStarRating = $this->Menu_model->CheckTaskStarRatingsExistorNot_New($task->user_id,'Was Task Planned on time',$task->tid);
                                           
                                                if(sizeof($chkStarRating) == 0){ ?>

                                                    <div class="rating" data-question="Was Task Planned on time" data-userid="<?= $task->user_id; ?>" data-taskid="<?= $task->tid; ?>">
                                                        <input type="radio" name="rat1_<?= $task->user_id; ?>_<?= $task->tid; ?>" value="5" id="5_<?= $task->tid; ?>"><label for="5_<?= $task->tid; ?>">☆</label>
                                                        <input type="radio" name="rat1_<?= $task->user_id; ?>_<?= $task->tid; ?>" value="4" id="4_<?= $task->tid; ?>"><label for="4_<?= $task->tid; ?>">☆</label>
                                                        <input type="radio" name="rat1_<?= $task->user_id; ?>_<?= $task->tid; ?>" value="3" id="3_<?= $task->tid; ?>"><label for="3_<?= $task->tid;?>">☆</label>
                                                        <input type="radio" name="rat1_<?= $task->user_id; ?>_<?= $task->tid; ?>" value="2" id="2_<?= $task->tid; ?>"><label for="2_<?= $task->tid; ?>">☆</label>
                                                        <input type="radio" name="rat1_<?= $task->user_id; ?>_<?= $task->tid; ?>" value="1" id="1_<?= $task->tid; ?>"><label for="1_<?= $task->tid; ?>">☆</label>
                                                    </div>

                                            <?php }else{
                                                foreach($chkStarRating as $star){
                                               
                                                    $starRating = $star->star;
                                                    $starRemark = $star->remarks;
                                                    
                                                }
                                                echo "<hr>";
                                                echo "<span class='text-dark font-weight-normal'><b>Total Star Given</b> :</span>";
                                                echo "<div class='star-rating'>";
                                                $totalStars = 5;
                                                for ($i = 0; $i < $starRating; $i++) {
                                                    echo "<i class='fas fa-star'></i>"; // filled star
                                                }
                                                for ($i = $starRating; $i < $totalStars; $i++) {
                                                    echo "<i class='far fa-star'></i>"; // empty star
                                                }
                                                echo "</div><br><span class='text-dark font-weight-normal'><b>Remark</b> :".$starRemark."</span>";
                                            }  ?>
                                            
                                            <?php ?>
                                        </td>
                                        <td ><b>Date : </b><?php 
                                       $meetinTypeArray = array('3','4','6','17','18');
                                            if(in_array($task->actiontype_id,$meetinTypeArray)){
                                                echo $task->initiateTime;
						                      $time_diff_plan_initiate = $task->time_diff_meetingupdateVsInitiat;
                                              $time_diff_InitiatVsClose = $task->time_diff_InitiatVsCloseMeeting;
                                             // $avgTimeTaken = get_AvgMeetingTime($task->actiontype_id);
                                              $avgTimeTaken = "";
                                            }
                                            else{
                                                echo $task->start_time;
                                                $time_diff_plan_initiate = $task->time_diff_updateVsInitiat;
                                                $time_diff_InitiatVsClose = $task->time_diff_InitiatVsClose;
                                                $avgTimeTaken = get_AvgTime($task->actiontype_id);
                                            }
                                         ?>
                                            <br><br>
                                            Time taken from planning to initiate : <b> <?= $time_diff_plan_initiate; ?></b>
                                        <br><br><hr>
                                        <?php

                                            if (!empty($task->late_remarks_message)) { ?>
                                                Why Task Started Late : <b> <?= $task->late_remarks_message; ?></b>
                                            <br><br><hr>
                                        <?php  } ?>
                                            <p class="question">Was Task Initiated on time..??</p>
                                        <?php 
                                            $chkStarRating = $this->Menu_model->CheckTaskStarRatingsExistorNot_New($task->user_id,'Was Task Initiated on time',$task->tid);
                                       
                                            if(sizeof($chkStarRating) == 0){ ?>

                                                <div class="rating" data-question="Was Task Initiated on time" data-userid="<?= $task->user_id; ?>" data-taskid="<?= $task->tid; ?>">
                                                    <input type="radio" name="rat2_<?= $task->user_id; ?>_<?= $task->tid; ?>" value="5" id="10_<?=$task->tid; ?>"><label for="10_<?= $task->tid; ?>">☆</label>
                                                    <input type="radio" name="rat2_<?= $task->user_id; ?>_<?= $task->tid; ?>" value="4" id="9_<?= $task->tid; ?>"><label for="9_<?= $task->tid; ?>">☆</label>
                                                    <input type="radio" name="rat2_<?= $task->user_id; ?>_<?= $task->tid; ?>" value="3" id="8_<?= $task->tid; ?>"><label for="8_<?= $task->tid; ?>">☆</label>
                                                    <input type="radio" name="rat2_<?= $task->user_id; ?>_<?= $task->tid; ?>" value="2" id="7_<?= $task->tid;?>"><label for="7_<?= $task->tid; ?>">☆</label>
                                                    <input type="radio" name="rat2_<?= $task->user_id; ?>_<?= $task->tid; ?>" value="1" id="6_<?= $task->tid; ?>"><label for="6_<?= $task->tid;?>">☆</label>
                                                </div>

                                        <?php }else{
                                            foreach($chkStarRating as $star){
                                           
                                                $starRating = $star->star;
                                                $starRemark = $star->remarks;
                                                
                                            }
                                            echo "<hr>";
                                            echo "<span class='text-dark font-weight-normal'><b>Total Star Given</b> :</span>";
                                            echo "<div class='star-rating'>";
                                            $totalStars = 5;
                                            for ($i = 0; $i < $starRating; $i++) {
                                                echo "<i class='fas fa-star'></i>"; // filled star
                                            }
                                            for ($i = $starRating; $i < $totalStars; $i++) {
                                                echo "<i class='far fa-star'></i>"; // empty star
                                            }
                                            echo "</div><br><span class='text-dark font-weight-normal'><b>Remark</b> :".$starRemark."</span>";
                                        }  ?>
                                        </td>
                                        <td><b>Date : </b><?= $task->end_time; ?>
                                        <br><br>
                                            <?php 
                                           
                                            // var_dump($avgTimeTaken);
                                            ?>
                                            Time taken from initiating to update : <b> <?=$time_diff_InitiatVsClose; ?></b><hr>
                                            Avg time taken to complete this task : <b><?=$avgTimeTaken?></b>
                                            <br><hr>

                                            <p class="question">Was Task Updated on time..??</p>

                                            <?php 

                                            $chkStarRating = $this->Menu_model->CheckTaskStarRatingsExistorNot_New($task->user_id,'Was Task Updated on time',$task->tid);

                                            if(sizeof($chkStarRating) == 0){ ?> 
                                                <div class="rating" data-question="Was Task Updated on time" data-userid="<?= $task->user_id; ?>" data-taskid="<?= $task->tid; ?>">
                                                    <input type="radio" name="rat3_<?= $task->user_id; ?>_<?= $task->tid; ?>" value="5" id="15_<?=$task->tid; ?>"><label for="15_<?=  $task->tid; ?>">☆</label>
                                                    <input type="radio" name="rat3_<?= $task->user_id; ?>_<?= $task->tid; ?>" value="4" id="14_<?=$task->tid; ?>"><label for="14_<?=  $task->tid; ?>">☆</label>
                                                    <input type="radio" name="rat3_<?= $task->user_id; ?>_<?= $task->tid; ?>" value="3" id="13_<?=$task->tid; ?>"><label for="13_<?=  $task->tid; ?>">☆</label>
                                                    <input type="radio" name="rat3_<?= $task->user_id; ?>_<?= $task->tid; ?>" value="2" id="12_<?=$task->tid; ?>"><label for="12_<?=  $task->tid; ?>">☆</label>
                                                    <input type="radio" name="rat3_<?= $task->user_id; ?>_<?= $task->tid; ?>" value="1" id="11_<?=$task->tid; ?>"><label for="11_<?=  $task->tid; ?>">☆</label>
                                                </div> 
                                            <?php }else{

                                                foreach($chkStarRating as $star){
                                               
                                                    $starRating = $star->star;
                                                    $starRemark = $star->remarks;
                                                    
                                                }
                                                echo "<hr>";
                                                echo "<span class='text-dark font-weight-normal'><b>Total Star Given</b> :</span>";
                                                echo "<div class='star-rating'>";
                                                $totalStars = 5;
                                                for ($i = 0; $i < $starRating; $i++) {
                                                    echo "<i class='fas fa-star'></i>"; // filled star
                                                }
                                                for ($i = $starRating; $i < $totalStars; $i++) {
                                                    echo "<i class='far fa-star'></i>"; // empty star
                                                }
                                                echo "</div><br><span class='text-dark font-weight-normal'><b>Remark</b> :".$starRemark."</span>";
                                            }  ?>

                                        </td>
                                        <td >
                                            Action Taken - <b><?= $task->actontaken; ?></b>  <br> <hr> Purpose Achieved - <b> <?= $task->purpose_achieved; ?></b> 
                                            
                                            <br><hr>
                                            <p class="question">Was purpose achieved for the task..??</p>

                                            <?php 

                                                $chkStarRating = $this->Menu_model->CheckTaskStarRatingsExistorNot_New($task->user_id,'Was purpose achieved for the task',$task->tid);
                                           
                                                if(sizeof($chkStarRating) == 0){ ?>

                                                    <div class="rating" data-question="Was purpose achieved for the task" data-userid="<?= $task->user_id; ?>" data-taskid="<?= $task->tid; ?>">
                                                        <input type="radio" name="rat9_<?= $task->user_id; ?>_<?= $task->tid; ?>" value="5" id="45_<?= $task->tid;?>"><label for="45_<?= $task->tid; ?>">☆</label>
                                                        <input type="radio" name="rat9_<?= $task->user_id; ?>_<?= $task->tid; ?>" value="4" id="44_<?= $task->tid;?>"><label for="44_<?= $task->tid; ?>">☆</label>
                                                        <input type="radio" name="rat9_<?= $task->user_id; ?>_<?= $task->tid; ?>" value="3" id="43_<?= $task->tid;?>"><label for="43_<?= $task->tid;?>">☆</label>
                                                        <input type="radio" name="rat9_<?= $task->user_id; ?>_<?= $task->tid; ?>" value="2" id="42_<?= $task->tid;?>"><label for="42_<?= $task->tid; ?>">☆</label>
                                                        <input type="radio" name="rat9_<?= $task->user_id; ?>_<?= $task->tid; ?>" value="1" id="41_<?= $task->tid;?>"><label for="41_<?= $task->tid;?>">☆</label>
                                                    </div>

                                            <?php }else{
                                                foreach($chkStarRating as $star){
                                               
                                                    $starRating = $star->star;
                                                    $starRemark = $star->remarks;
                                                    
                                                }
                                                echo "<hr>";
                                                echo "<span class='text-dark font-weight-normal'><b>Total Star Given</b> :</span>";
                                                echo "<div class='star-rating'>";
                                                $totalStars = 5;
                                                for ($i = 0; $i < $starRating; $i++) {
                                                    echo "<i class='fas fa-star'></i>"; // filled star
                                                }
                                                for ($i = $starRating; $i < $totalStars; $i++) {
                                                    echo "<i class='far fa-star'></i>"; // empty star
                                                }
                                                echo "</div><br><span class='text-dark font-weight-normal'><b>Remark</b> :".$starRemark."</span>";
                                            }  ?>

                                            <hr>

                                            <b>Task Remarks - </b><?= $task->remarks; ?>
                                           <?php if (in_array($task->actiontype_id, [3, 4, 17])): ?>
                                                    <?php 
                                                        $momtask_mom = '';
                                                        $momtaskDatas = $this->Menu_model->GetTBLMomTaskByTaskId($task->tid);

                                                        if (!empty($momtaskDatas)) {
                                                            
                                                            $momtask_id = $momtaskDatas[0]->id;
                                                            $momtask_mom = $momtaskDatas[0]->mom;

                                                            $momdatas = $this->Menu_model->GetMomDataByTaskId($momtask_id);
                                                            if (!empty($momdatas)) {
                                                                $mom_id = $momdatas[0]->id;
                                                            echo "<br/><b>Short MOM</b> : $momtask_mom<br/>";    
                                                                // Show view link
                                                        echo '<hr/> <a class="p-1 bg-success text-white" target="_blank" href="' 
                                                                    . base_url() . 'Management/MomDataCheckInAdmin/' . $mom_id . '/' . $task->cid . '">View Full MOM</a>';
                                                                    echo "<br/>";
                                                            } else {
                                                                echo "<span class='p-1 bg-warning text-dark'>Pending For Write</span>";
                                                            }
                                                        } else {
                                                            echo "NA";
                                                        }
                                                    ?>
                                                <?php endif; ?>

                                            <?php
                                            /*
                                            if ($task->actiontype_id == 6 ) {
                                                   
                                                    $getMomData = getMomData($task->tid); ?>
                                                    <br>
                                                    <!-- <b>MoM remark - </b> <?= $getMomData->momremark; ?> -->
                                                    <br>
                                                    <b>MoM Status - </b><?= $getMomData->momStatus; ?>
                                                    <br>
                                                    <b>MoM Status Update at - </b> <?= $getMomData->approvedDate; ?>
                                                    <br>
                                                <?php  } */ ?>

                                               <?php  if ($task->actiontype_id == 7 ) {
                                                                                                
                                                    $getProposalData = getProposalData($task->tid); ?>
                                                    <br>
                                                    <br>
                                                    <b>Proposal Remark - </b><?= $getProposalData->momStatus; ?>
                                                    <br>
                                                    <br>
                                                    <b>Proposal Status - </b> <?= $getProposalData->pro_status; ?>
                                                    <br>
                                                    <br>
                                                    <b>Proposal Status Change At - </b> <?= $getProposalData->pro_approvedDate; ?>
                                                    <?php 

                                                        if ($getProposalData->pro_apr == 1) { ?>
                                                            
                                                            <a href="<?= base_url() . $getProposalData->pro_attachment . '/'?>" class="btn btn-primary" target="_blank">View Document</a>

                                            <?php       }
 
                                                    }
                                                ?>

                                            <hr>
                                            <p class="question">Was correct remark entered..??</p>

                                            <?php 
                                            $chkStarRating = $this->Menu_model->CheckTaskStarRatingsExistorNot_New($task->user_id,'correct remark entered',$task->tid);
                                            if(sizeof($chkStarRating) == 0){ ?> 
                                                <div class="rating" data-question="correct remark entered" data-userid="<?= $task->user_id; ?>" data-taskid="<?= $task->tid; ?>">
                                                    <input type="radio" name="rat6_<?= $task->user_id; ?>_<?= $task->tid; ?>" value="5" id="30_<?= $task->tid; ?>"><label for="30_<?= $task->tid;  ?>">☆</label>
                                                    <input type="radio" name="rat6_<?= $task->user_id; ?>_<?= $task->tid; ?>" value="4" id="29_<?= $task->tid;  ?>"><label for="29_<?= $task->tid;  ?>">☆</label>
                                                    <input type="radio" name="rat6_<?= $task->user_id; ?>_<?= $task->tid; ?>" value="3" id="28_<?= $task->tid;  ?>"><label for="28_<?= $task->tid;  ?>">☆</label>
                                                    <input type="radio" name="rat6_<?= $task->user_id; ?>_<?= $task->tid; ?>" value="2" id="27_<?= $task->tid;  ?>"><label for="27_<?= $task->tid;  ?>">☆</label>
                                                    <input type="radio" name="rat6_<?= $task->user_id; ?>_<?= $task->tid; ?>" value="1" id="26_<?= $task->tid;  ?>"><label for="26_<?= $task->tid; ?>">☆</label>
                                                </div>
                                                <?php }else{
                                                        foreach($chkStarRating as $star){
                                                       
                                                            $starRating = $star->star;
                                                            $starRemark = $star->remarks;
                                                            
                                                        }
                                                        echo "<hr>";
                                                        echo "<span class='text-dark font-weight-normal'><b>Total Star Given</b> :</span>";
                                                        echo "<div class='star-rating'>";
                                                        $totalStars = 5;
                                                        for ($i = 0; $i < $starRating; $i++) {
                                                            echo "<i class='fas fa-star'></i>"; // filled star
                                                        }
                                                        for ($i = $starRating; $i < $totalStars; $i++) {
                                                            echo "<i class='far fa-star'></i>"; // empty star
                                                        }
                                                        echo "</div><br><span class='text-dark font-weight-normal'><b>Remark</b> :".$starRemark."</span>";
                                                    }  ?>

                                        </td>
                                        <td ><?=$task->old_status?> to <?=$task->new_status?>
                                            <br><hr>

                                            <p class="question">Status change was correct..??</p>

                                            <?php 
                                            $chkStarRating = $this->Menu_model->CheckTaskStarRatingsExistorNot_New($task->user_id,'status change was correct',$task->tid);

                                            if(sizeof($chkStarRating) == 0){ ?> 
                                                <div class="rating" data-question="status change was correct" data-userid="<?= $task->user_id; ?>" data-taskid="<?= $task->tid; ?>">
                                                    <input type="radio" name="rat5_<?= $task->user_id; ?>_<?= $task->tid; ?>" value="5" id="25_<?= $task->tid; ?>"><label for="25_<?= $task->tid;  ?>">☆</label>
                                                    <input type="radio" name="rat5_<?= $task->user_id; ?>_<?= $task->tid; ?>" value="4" id="24_<?= $task->tid; ?>"><label for="24_<?= $task->tid;  ?>">☆</label>
                                                    <input type="radio" name="rat5_<?= $task->user_id; ?>_<?= $task->tid; ?>" value="3" id="23_<?= $task->tid; ?>"><label for="23_<?= $task->tid;  ?>">☆</label>
                                                    <input type="radio" name="rat5_<?= $task->user_id; ?>_<?= $task->tid; ?>" value="2" id="22_<?= $task->tid; ?>"><label for="22_<?= $task->tid; ?>">☆</label>
                                                    <input type="radio" name="rat5_<?= $task->user_id; ?>_<?= $task->tid; ?>" value="1" id="21_<?= $task->tid; ?>"><label for="21_<?=$task->tid; ?>">☆</label>
                                                </div>
                                                <?php }else{
                                                        foreach($chkStarRating as $star){
                                                       
                                                            $starRating = $star->star;
                                                            $starRemark = $star->remarks;
                                                            
                                                        }
                                                        echo "<hr>";
                                                        echo "<span class='text-dark font-weight-normal'><b>Total Star Given</b> :</span>";
                                                        echo "<div class='star-rating'>";
                                                        $totalStars = 5;
                                                        for ($i = 0; $i < $starRating; $i++) {
                                                            echo "<i class='fas fa-star'></i>"; // filled star
                                                        }
                                                        for ($i = $starRating; $i < $totalStars; $i++) {
                                                            echo "<i class='far fa-star'></i>"; // empty star
                                                        }
                                                        echo "</div><br><span class='text-dark font-weight-normal'><b>Remark</b> :".$starRemark."</span>";
                                                    }  ?>
                                        </td>
                                        
                                        <td><?=$SameStatusSince_1 .' Days';?></td>
                                        <td><?=$NoOfTaskSinceStatusChange ;?></td>
                                        <!-- <td></td> -->
                                    </tr>
                                <?php $i++; } ?>
                                    
                                </tbody>
                            </table>
                        </div>
						

                        <?php if($ctaskid != ''){ ?>
                        <br>
                        <br>
                        <div class="card p-2">
                            <center>
                            <form action="<?=base_url().'Menu/UpdateScTaskWithIdWithRemarks'?>" method="POST" enctype="multipart/form-data">
                                <input type="hidden" name="taskid" value="<?= $ctaskid ?>">
                                <?php  
                                $getSCTaskByTaskID = $this->Menu_model->GetSCTaskByTaskID($ctaskid);
                                $file_required = $getSCTaskByTaskID[0]->file_required;
                                ?>
                              <?php if($file_required == 'yes'){ ?> 
                                <div class="form-group">
                                  <label for="exampleFormControlTextarea1">Upload Report</label>
                                  <input type="file" name="filname[]" id="filname" class="form-control" multiple required>
                                </div>
                                <?php } ?>
                                <div class="form-group">
                                    <label for="exampleFormControlTextarea1">Add Remarks</label>
                                    <textarea required class="form-control" name="remarks" placeholder="* Please Add Daily Team Planner Summary With Location Remarks"  id="exampleFormControlTextarea1" rows="3"></textarea>
                                </div>
                                <div id="file-preview"></div>
                                <button type="submit" class="btn btn-primary">Submit</button>
                            </form>
                            </center>
                            </div>
                            <?php } ?>



					</div>
                </div>
                  
            </div>
          </div>
      </div>   
     </div>     
    </section>


        <div class="modal fade bd-example-modal-lg" id="ReviewModal" tabindex="-1" role="dialog" aria-labelledby="modelTitleId" aria-hidden="true">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header bg-primary">
                        <h5 class="modal-title">Review Details</h5>
                    </div>
                    <div class="modal-body">
                        <form >
                            <input type="hidden" name="taskID" id="taskID">
                            <input type="hidden" name="userID" id="userID">
                            <div class="row">
                                <div class="col-md-6">
                                    <p><strong>Review Remark:</strong> <span id="reviewRemark"></span></p>

                                    <p><b> Is review remark correct..??</b></p>
                                    <fieldset class="MoMrating" data-question="Did review remarks entered correctly" data-userid="" data-taskid="">
                                        <input type="radio" name="Reviewrat2_01" value="5" id="01_Reviewrating_01"><label for="01_Reviewrating_01">☆</label>
                                        <input type="radio" name="Reviewrat2_01" value="4" id="02_Reviewrating_01"><label for="02_Reviewrating_01">☆</label>
                                        <input type="radio" name="Reviewrat2_01" value="3" id="03_Reviewrating_01"><label for="03_Reviewrating_01">☆</label>
                                        <input type="radio" name="Reviewrat2_01" value="2" id="04_Reviewrating_01"><label for="04_Reviewrating_01">☆</label>
                                        <input type="radio" name="Reviewrat2_01" value="1" id="05_Reviewrating_01"><label for="05_Reviewrating_01">☆</label>

                                        <div class="remark-box">
                                            <label for="remark_01">Please provide additional remarks:</label>
                                            <textarea id="remark_01" rows="4" cols="50"></textarea>
                                        </div>

                                    </fieldset>
                                    <hr>
                                </div>
                                
                                <div class="col-md-6">
                                    <p><strong>Potential / Non Potential:</strong> <span id="potential"></span></p>

                                    <fieldset class="MoMrating" data-question="potential or non marked correctly" data-userid="" data-taskid="">
                                        <input type="radio" name="Reviewrat2_07" value="5" id="35_Reviewrating_01"><label for="35_Reviewrating_01">☆</label>
                                        <input type="radio" name="Reviewrat2_07" value="4" id="34_Reviewrating_01"><label for="34_Reviewrating_01">☆</label>
                                        <input type="radio" name="Reviewrat2_07" value="3" id="33_Reviewrating_01"><label for="33_Reviewrating_01">☆</label>
                                        <input type="radio" name="Reviewrat2_07" value="2" id="32_Reviewrating_01"><label for="32_Reviewrating_01">☆</label>
                                        <input type="radio" name="Reviewrat2_07" value="1" id="31_Reviewrating_01"><label for="31_Reviewrating_01">☆</label> 

                                        <div class="remark-box">
                                            <label for="remark_06">Please provide additional remarks:</label>
                                            <textarea id="remark_06" rows="4" cols="50"></textarea>
                                        </div>
                                    </fieldset>
                                    <hr>
                                </div>
                                <div class="col-md-6">
                                    <p><strong>Fix Date:</strong> <span id="FixDate"></span></p>
                                    
                                    <hr>
                                </div>
                                <div class="col-md-6">
                                    <p><strong>Existing Status:</strong> <span id="ExistingStatus"></span></p>
                                    <hr>
                                </div>
                                <div class="col-md-6">
                                    <p><strong>Current Status:</strong> <span id="CurrentStatus"></span></p>
                                    <hr>
                                </div>
                                <div class="col-md-6">
                                    <p><strong>Call Remark:</strong> <span id="CallRemark"></span></p>
                                    <fieldset class="MoMrating" data-question="call remark marked correctly" data-userid="" data-taskid="">
                                        <input type="radio" name="Reviewrat2_08" value="5" id="40_Reviewrating_01"><label for="40_Reviewrating_01">☆</label>
                                        <input type="radio" name="Reviewrat2_08" value="4" id="39_Reviewrating_01"><label for="39_Reviewrating_01">☆</label>
                                        <input type="radio" name="Reviewrat2_08" value="3" id="38_Reviewrating_01"><label for="38_Reviewrating_01">☆</label>
                                        <input type="radio" name="Reviewrat2_08" value="2" id="37_Reviewrating_01"><label for="37_Reviewrating_01">☆</label>
                                        <input type="radio" name="Reviewrat2_08" value="1" id="36_Reviewrating_01"><label for="36_Reviewrating_01">☆</label> 

                                        <div class="remark-box">
                                            <label for="remark_07">Please provide additional remarks:</label>
                                            <textarea id="remark_07" rows="4" cols="50"></textarea>
                                        </div>
                                    </fieldset>
                                    <hr>
                                </div>
                                <div class="col-md-6">
                                    <p><strong>Email Remark:</strong> <span id="EmailRemark"></span></p>
                                    <fieldset class="MoMrating" data-question="email remark marked correctly" data-userid="" data-taskid="">
                                        <input type="radio" name="Reviewrat2_09" value="5" id="45_Reviewrating_01"><label for="45_Reviewrating_01">☆</label>
                                        <input type="radio" name="Reviewrat2_09" value="4" id="44_Reviewrating_01"><label for="44_Reviewrating_01">☆</label>
                                        <input type="radio" name="Reviewrat2_09" value="3" id="43_Reviewrating_01"><label for="43_Reviewrating_01">☆</label>
                                        <input type="radio" name="Reviewrat2_09" value="2" id="42_Reviewrating_01"><label for="42_Reviewrating_01">☆</label>
                                        <input type="radio" name="Reviewrat2_09" value="1" id="41_Reviewrating_01"><label for="41_Reviewrating_01">☆</label> 

                                        <div class="remark-box">
                                            <label for="remark_08">Please provide additional remarks:</label>
                                            <textarea id="remark_08" rows="4" cols="50"></textarea>
                                        </div>
                                    </fieldset>
                                    <hr>
                                </div>
                                <div class="col-md-6">
                                    <p><strong>Meeting Remark:</strong> <span id="MeetingRemark"></span></p>
                                    <fieldset class="MoMrating" data-question="meeting remark marked correctly" data-userid="" data-taskid="">
                                        <input type="radio" name="Reviewrat2_10" value="5" id="50_Reviewrating_01"><label for="50_Reviewrating_01">☆</label>
                                        <input type="radio" name="Reviewrat2_10" value="4" id="49_Reviewrating_01"><label for="49_Reviewrating_01">☆</label>
                                        <input type="radio" name="Reviewrat2_10" value="3" id="48_Reviewrating_01"><label for="48_Reviewrating_01">☆</label>
                                        <input type="radio" name="Reviewrat2_10" value="2" id="47_Reviewrating_01"><label for="47_Reviewrating_01">☆</label>
                                        <input type="radio" name="Reviewrat2_10" value="1" id="46_Reviewrating_01"><label for="46_Reviewrating_01">☆</label> 

                                        <div class="remark-box">
                                            <label for="remark_09">Please provide additional remarks:</label>
                                            <textarea id="remark_09" rows="4" cols="50"></textarea>
                                        </div>
                                    </fieldset>
                                    <hr>
                                </div>
                                <div class="col-md-6">
                                    <p><strong>Top Spender:</strong> <span id="topSpender"></span></p>
                                    <hr>
                                </div>
                                <div class="col-md-6">
                                    <p><strong>Key Client:</strong> <span id="keyClient"></span></p>
                                    <hr>
                                </div>
                                <div class="col-md-6">
                                    <p><strong>Positive Key Client:</strong> <span id="PositiveKeyClient"></span></p>
                                    <hr>
                                </div>
                                <div class="col-md-6">
                                    <p><strong>Priority Client:</strong> <span id="PriorityClient"></span></p>
                                    <hr>
                                </div>
                                <div class="col-md-6">
                                    <p><strong>Upsell Client:</strong> <span id="UpsellClient"></span></p>
                                    <hr>
                                </div>
                                <div class="col-md-6">
                                    <p><strong>Focus Funnel:</strong> <span id="FocusFunnel"></span></p>
                                    <hr>
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="button" class="btn btn-primary" onclick="submitReviewModal()">Submit</button>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="modal fade bd-example-modal-lg" id="ManyReviewModal" tabindex="-1" role="dialog" aria-labelledby="modelTitleId" aria-hidden="true">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header bg-primary">
                        <h5 class="modal-title">Review Details</h5>
                    </div>
                    <div class="modal-body">
                        <form >
                            <input type="hidden" name="taskManyID" id="taskManyID">
                            <input type="hidden" name="userManyID" id="userManyID">
                            <div class="row">
                                <div class="col-md-6">
                                    <p><strong>How many task were done? :</strong> <span id="howManyTaskDone"></span></p>
                                    <!-- <p><b>How many task were done..??</b></p> -->
                                    <fieldset class="MoMrating" data-question="How many task were done" data-userid="" data-taskid="">
                                        <input type="radio" name="Reviewrat3_08" value="5" id="20_Reviewrating_03"><label for="20_Reviewrating_03">☆</label>
                                        <input type="radio" name="Reviewrat3_08" value="4" id="19_Reviewrating_03"><label for="19_Reviewrating_03">☆</label>
                                        <input type="radio" name="Reviewrat3_08" value="3" id="18_Reviewrating_03"><label for="18_Reviewrating_03">☆</label>
                                        <input type="radio" name="Reviewrat3_08" value="2" id="17_Reviewrating_03"><label for="17_Reviewrating_03">☆</label>
                                        <input type="radio" name="Reviewrat3_08" value="1" id="16_Reviewrating_03"><label for="16_Reviewrating_03">☆</label> 

                                        <div class="remark-box">
                                            <label for="remark_112">Please provide additional remarks:</label>
                                            <textarea id="remark_112" rows="4" cols="50"></textarea>
                                        </div>
                                    </fieldset>
                                    
                                    <hr>
                                </div>
                                <div class="col-md-6">
                                    <p><strong>RP Meeting done?:</strong> <span id="rpMeetingdone"></span></p>
                                    <!-- <p><b>RP Meeting done..??</b></p> -->
                                    <fieldset class="MoMrating" data-question="RP Meeting done" data-userid="" data-taskid="">
                                        <input type="radio" name="Reviewrat3_09" value="5" id="30_Reviewrating_03"><label for="30_Reviewrating_03">☆</label>
                                        <input type="radio" name="Reviewrat3_09" value="4" id="29_Reviewrating_03"><label for="29_Reviewrating_03">☆</label>
                                        <input type="radio" name="Reviewrat3_09" value="3" id="28_Reviewrating_03"><label for="28_Reviewrating_03">☆</label>
                                        <input type="radio" name="Reviewrat3_09" value="2" id="27_Reviewrating_03"><label for="27_Reviewrating_03">☆</label>
                                        <input type="radio" name="Reviewrat3_09" value="1" id="26_Reviewrating_03"><label for="26_Reviewrating_03">☆</label> 

                                        <div class="remark-box">
                                            <label for="remark_114">Please provide additional remarks:</label>
                                            <textarea id="remark_114" rows="4" cols="50"></textarea>
                                        </div>
                                    </fieldset>
                                    <hr>
                                </div>
                                <div class="col-md-6">
                                    <p><strong>Write MoM Done?:</strong> <span id="writeMoMDone"></span></p>
                                    <!-- <p><b>Write MoM Done..??</b></p> -->
                                    <fieldset class="MoMrating" data-question="write MoM done" data-userid="" data-taskid="">
                                        <input type="radio" name="Reviewrat3_10" value="5" id="35_Reviewrating_03"><label for="35_Reviewrating_03">☆</label>
                                        <input type="radio" name="Reviewrat3_10" value="4" id="34_Reviewrating_03"><label for="34_Reviewrating_03">☆</label>
                                        <input type="radio" name="Reviewrat3_10" value="3" id="33_Reviewrating_03"><label for="33_Reviewrating_03">☆</label>
                                        <input type="radio" name="Reviewrat3_10" value="2" id="32_Reviewrating_03"><label for="32_Reviewrating_03">☆</label>
                                        <input type="radio" name="Reviewrat3_10" value="1" id="31_Reviewrating_03"><label for="31_Reviewrating_03">☆</label> 

                                        <div class="remark-box">
                                            <label for="remark_115">Please provide additional remarks:</label>
                                            <textarea id="remark_115" rows="4" cols="50"></textarea>
                                        </div>
                                    </fieldset>
                                    <hr>
                                </div>
                                <div class="col-md-6">
                                    <p><strong>Social networking done?:</strong> <span id="socialNetworking"></span></p>
                                    <!-- <p><b>Social networking done correctly..??</b></p> -->
                                    <fieldset class="MoMrating" data-question="social networking done" data-userid="" data-taskid="">
                                        <input type="radio" name="Reviewrat3_11" value="5" id="40_Reviewrating_03"><label for="40_Reviewrating_03">☆</label>
                                        <input type="radio" name="Reviewrat3_11" value="4" id="39_Reviewrating_03"><label for="39_Reviewrating_03">☆</label>
                                        <input type="radio" name="Reviewrat3_11" value="3" id="38_Reviewrating_03"><label for="38_Reviewrating_03">☆</label>
                                        <input type="radio" name="Reviewrat3_11" value="2" id="37_Reviewrating_03"><label for="37_Reviewrating_03">☆</label>
                                        <input type="radio" name="Reviewrat3_11" value="1" id="36_Reviewrating_03"><label for="36_Reviewrating_03">☆</label> 

                                        <div class="remark-box">
                                            <label for="remark_116">Please provide additional remarks:</label>
                                            <textarea id="remark_116" rows="4" cols="50"></textarea>
                                        </div>
                                    </fieldset>
                                    <hr>
                                </div>
                                <div class="col-md-6">
                                    <p><strong>Was the tasks closed on the target date : </strong> <span id="categoryRight"></span></p>
                                    <!-- <p><b>Was the tasks closed on the target date..??</b></p> -->
                                    <fieldset class="MoMrating" data-question="Was the tasks closed on the target date" data-userid="" data-taskid="">
                                        <input type="radio" name="Reviewrat3_12" value="5" id="45_Reviewrating_03"><label for="45_Reviewrating_03">☆</label>
                                        <input type="radio" name="Reviewrat3_12" value="4" id="44_Reviewrating_03"><label for="44_Reviewrating_03">☆</label>
                                        <input type="radio" name="Reviewrat3_12" value="3" id="43_Reviewrating_03"><label for="43_Reviewrating_03">☆</label>
                                        <input type="radio" name="Reviewrat3_12" value="2" id="42_Reviewrating_03"><label for="42_Reviewrating_03">☆</label>
                                        <input type="radio" name="Reviewrat3_12" value="1" id="41_Reviewrating_03"><label for="41_Reviewrating_03">☆</label> 

                                        <div class="remark-box">
                                            <label for="remark_117">Please provide additional remarks:</label>
                                            <textarea id="remark_117" rows="4" cols="50"></textarea>
                                        </div>
                                    </fieldset>
                                    <hr>
                                </div>
                                <div class="col-md-6">
                                    <p><strong>Schedule meeting was done or not?</strong> <span id="schedule_meeting"></span></p>
                                    <!-- <p><b>Do you agree schedule meeting was not done</b></p> -->
                                    <fieldset class="MoMrating" data-question="Do you agree schedule meeting was not done" data-userid="" data-taskid="">
                                        <input type="radio" name="Reviewrat3_13" value="5" id="50_Reviewrating_03"><label for="50_Reviewrating_03">☆</label>
                                        <input type="radio" name="Reviewrat3_13" value="4" id="49_Reviewrating_03"><label for="49_Reviewrating_03">☆</label>
                                        <input type="radio" name="Reviewrat3_13" value="3" id="48_Reviewrating_03"><label for="48_Reviewrating_03">☆</label>
                                        <input type="radio" name="Reviewrat3_13" value="2" id="47_Reviewrating_03"><label for="47_Reviewrating_03">☆</label>
                                        <input type="radio" name="Reviewrat3_13" value="1" id="46_Reviewrating_03"><label for="46_Reviewrating_03">☆</label> 

                                        <div class="remark-box">
                                            <label for="remark_118">Please provide additional remarks:</label>
                                            <textarea id="remark_118" rows="4" cols="50"></textarea>
                                        </div>
                                    </fieldset>
                                    <hr>
                                </div>
                                <div class="col-md-6">
                                    <p><strong>Research Prospecting:</strong> <span id="researchProspecting"></span></p>
                                    <!-- <p><b>Research Prospecting done..??</b></p> -->
                                    <fieldset class="MoMrating" data-question="research prospecting done" data-userid="" data-taskid="">
                                        <input type="radio" name="Reviewrat3_15" value="5" id="60_Reviewrating_03"><label for="60_Reviewrating_03">☆</label>
                                        <input type="radio" name="Reviewrat3_15" value="4" id="59_Reviewrating_03"><label for="59_Reviewrating_03">☆</label>
                                        <input type="radio" name="Reviewrat3_15" value="3" id="58_Reviewrating_03"><label for="58_Reviewrating_03">☆</label>
                                        <input type="radio" name="Reviewrat3_15" value="2" id="57_Reviewrating_03"><label for="57_Reviewrating_03">☆</label>
                                        <input type="radio" name="Reviewrat3_15" value="1" id="56_Reviewrating_03"><label for="56_Reviewrating_03">☆</label> 

                                        <div class="remark-box">
                                            <label for="remark_120">Please provide additional remarks:</label>
                                            <textarea id="remark_120" rows="4" cols="50"></textarea>
                                        </div>
                                    </fieldset>
                                    <hr>
                                </div>
                                <div class="col-md-6">
                                    <p><strong>Mail check was done?:</strong> <span id="mailCheck"></span></p>
                                    <!-- <p><b>Do you agree Mail Check was not done..??</b></p> -->
                                    <fieldset class="MoMrating" data-question="Do you agree Mail Check was not done" data-userid="" data-taskid="">
                                        <input type="radio" name="Reviewrat3_16" value="5" id="65_Reviewrating_03"><label for="65_Reviewrating_03">☆</label>
                                        <input type="radio" name="Reviewrat3_16" value="4" id="64_Reviewrating_03"><label for="64_Reviewrating_03">☆</label>
                                        <input type="radio" name="Reviewrat3_16" value="3" id="63_Reviewrating_03"><label for="63_Reviewrating_03">☆</label>
                                        <input type="radio" name="Reviewrat3_16" value="2" id="62_Reviewrating_03"><label for="62_Reviewrating_03">☆</label>
                                        <input type="radio" name="Reviewrat3_16" value="1" id="61_Reviewrating_03"><label for="61_Reviewrating_03">☆</label> 

                                        <div class="remark-box">
                                            <label for="remark_121">Please provide additional remarks:</label>
                                            <textarea id="remark_121" rows="4" cols="50"></textarea>
                                        </div>
                                    </fieldset>
                                    <hr>
                                </div>
                                <div class="col-md-6">
                                    <p><strong>Roaster Calling was done?:</strong> <span id="roasterCalling"></span></p>
                                    <!-- <p><b>Do you agree Roster Calling was not done..??</b></p> -->
                                    <fieldset class="MoMrating" data-question="Do you agree Roster Calling was not done" data-userid="" data-taskid="">
                                        <input type="radio" name="Reviewrat3_17" value="5" id="70_Reviewrating_03"><label for="70_Reviewrating_03">☆</label>
                                        <input type="radio" name="Reviewrat3_17" value="4" id="69_Reviewrating_03"><label for="69_Reviewrating_03">☆</label>
                                        <input type="radio" name="Reviewrat3_17" value="3" id="68_Reviewrating_03"><label for="68_Reviewrating_03">☆</label>
                                        <input type="radio" name="Reviewrat3_17" value="2" id="67_Reviewrating_03"><label for="67_Reviewrating_03">☆</label>
                                        <input type="radio" name="Reviewrat3_17" value="1" id="66_Reviewrating_03"><label for="66_Reviewrating_03">☆</label> 

                                        <div class="remark-box">
                                            <label for="remark_122">Please provide additional remarks:</label>
                                            <textarea id="remark_122" rows="4" cols="50"></textarea>
                                        </div>
                                    </fieldset>
                                    <hr>
                                </div>
                                <div class="col-md-6">
                                    <p><strong>Documentation was done :</strong> <span id="documentation"></span></p>
                                    <!-- <p><b>Do you agree Documentation was not done..??</b></p> -->
                                    <fieldset class="MoMrating" data-question="Proposal done" data-userid="" data-taskid="">
                                        <input type="radio" name="Reviewrat3_18" value="5" id="75_Reviewrating_03"><label for="75_Reviewrating_03">☆</label>
                                        <input type="radio" name="Reviewrat3_18" value="4" id="74_Reviewrating_03"><label for="74_Reviewrating_03">☆</label>
                                        <input type="radio" name="Reviewrat3_18" value="3" id="73_Reviewrating_03"><label for="73_Reviewrating_03">☆</label>
                                        <input type="radio" name="Reviewrat3_18" value="2" id="72_Reviewrating_03"><label for="72_Reviewrating_03">☆</label>
                                        <input type="radio" name="Reviewrat3_18" value="1" id="71_Reviewrating_03"><label for="71_Reviewrating_03">☆</label> 

                                        <div class="remark-box">
                                            <label for="remark_123">Please provide additional remarks:</label>
                                            <textarea id="remark_123" rows="4" cols="50"></textarea>
                                        </div>
                                    </fieldset>
                                    <hr>
                                </div>
                                <div class="col-md-6">
                                    <p><strong>Number of Task Performed Against Each Status :</strong> <span id="noOftaskPerformed"></span></p>
                                    <!-- <p><b>Number of Task Performed Against Each Status</b></p> -->
                                    <fieldset class="MoMrating" data-question="Number of Task Performed Against Each Status" data-userid="" data-taskid="">
                                        <input type="radio" name="Reviewrat3_19" value="5" id="80_Reviewrating_03"><label for="80_Reviewrating_03">☆</label>
                                        <input type="radio" name="Reviewrat3_19" value="4" id="79_Reviewrating_03"><label for="79_Reviewrating_03">☆</label>
                                        <input type="radio" name="Reviewrat3_19" value="3" id="78_Reviewrating_03"><label for="78_Reviewrating_03">☆</label>
                                        <input type="radio" name="Reviewrat3_19" value="2" id="77_Reviewrating_03"><label for="77_Reviewrating_03">☆</label>
                                        <input type="radio" name="Reviewrat3_19" value="1" id="76_Reviewrating_03"><label for="76_Reviewrating_03">☆</label> 

                                        <div class="remark-box">
                                            <label for="remark_124">Please provide additional remarks:</label>
                                            <textarea id="remark_124" rows="4" cols="50"></textarea>
                                        </div>
                                    </fieldset>
                                    <hr>
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="button" class="btn btn-primary" onclick="submitManyReviewModal()">Submit</button>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade bd-example-modal-lg" id="MoMModal" tabindex="-1" role="dialog" aria-labelledby="modelTitleId" aria-hidden="true">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header bg-primary">
                        <h5 class="modal-title">Meeting Details</h5>
                    </div>
                    <div class="modal-body">
                        <form>
                            <!-- <hr> -->
                            <input type="hidden" name="taskID" id="taskID" >
                            <input type="hidden" name="userID" id="userID" >
                            <div class="row">

                                <div class="col-md-6">
                                    <p><strong>Meeting Type:</strong> <span id="meetingType"></span></p>
                                    <hr>
                                </div>

                                <div class="col-md-6">
                                    <p><strong>Meeting From:</strong> <span id="meetingFrom"></span></p>
                                    <hr>
                                    
                                </div>
                                
                                <div class="col-md-6">
                                    <p><strong>Planned Time:</strong> <span id="modalPlannedTime"></span></p>
                                    <p><strong>Initiate Time:</strong> <span id="modalInitiateTime"></span></p>
                                    <p><strong>Start Time:</strong> <span id="modalStartTime"></span></p>

                                    <p><b> Did the meeting started on right time..??</b></p>
                                    <fieldset class="MoMrating" data-question="Did the meeting started on right time" data-userid="" data-taskid="">
                                        <input type="radio" name="momrat2_10" value="5" id="50_rating_10"><label for="50_rating_10">☆</label>
                                        <input type="radio" name="momrat2_10" value="4" id="49_rating_10"><label for="49_rating_10">☆</label>
                                        <input type="radio" name="momrat2_10" value="3" id="48_rating_10"><label for="48_rating_10">☆</label>
                                        <input type="radio" name="momrat2_10" value="2" id="47_rating_10"><label for="47_rating_10">☆</label>
                                        <input type="radio" name="momrat2_10" value="1" id="46_rating_10"><label for="46_rating_10">☆</label>
                                        <div class="remark-box">
                                            <label for="remark_10">Please provide additional remarks:</label>
                                            <textarea id="remark_10" rows="4" cols="50"></textarea>
                                        </div>
                                    </fieldset>
                                    <hr>
                                </div>

                       

                                <div class="col-md-6">
                                    <p> <strong>End Time:</strong> <span id="modalEndTime"></span></p>
                                    <p><b> Did the meeting ended on right time..??</b></p>

                                    <fieldset class="MoMrating" data-question="Did the meeting ended on right time" data-userid="" data-taskid="">
                                        <input type="radio" name="momrat2_3" value="5" id="15_rating_2"><label for="15_rating_2">☆</label>
                                        <input type="radio" name="momrat2_3" value="4" id="14_rating_2"><label for="14_rating_2">☆</label>
                                        <input type="radio" name="momrat2_3" value="3" id="13_rating_2"><label for="13_rating_2">☆</label>
                                        <input type="radio" name="momrat2_3" value="2" id="12_rating_2"><label for="12_rating_2">☆</label>
                                        <input type="radio" name="momrat2_3" value="1" id="11_rating_2"><label for="11_rating_2">☆</label>
                                        <div class="remark-box">
                                            <label for="remark_3">Please provide additional remarks:</label>
                                            <textarea id="remark_3" rows="4" cols="50"></textarea>
                                        </div>
                                    </fieldset>
                                    <hr>
                                </div>
                                
                                <div class="col-md-6">
                                    <p><strong>RP/No RP:</strong> <span id="modalMomType"></span> &nbsp;&nbsp;&nbsp; MOM : <div id="momdetails"></div> </p>

                                    <p> <strong>Duration of Meeting :</strong> <span id="modalDuration"> mins</span></p>

                                    <p><b> Was it RP or No RP..??</b></p>
                                    <fieldset class="MoMrating" data-question="Was it RP or No RP" data-userid="" data-taskid="">
                                        <?php 
                                            // $chkStarRating = $this->Menu_model->CheckTaskStarRatingsExistorNot_New($user_id,'Was it RP or No RP',$tid);
                                            // if(sizeof($chkStarRating) == 0){ }
                                        ?>
                                        <input type="radio" name="momrat2_1" value="5" id="5_rating_1"><label for="5_rating_1">☆</label>
                                        <input type="radio" name="momrat2_1" value="4" id="4_rating_1"><label for="4_rating_1">☆</label>
                                        <input type="radio" name="momrat2_1" value="3" id="3_rating_1"><label for="3_rating_1">☆</label>
                                        <input type="radio" name="momrat2_1" value="2" id="2_rating_1"><label for="2_rating_1">☆</label>
                                        <input type="radio" name="momrat2_1" value="1" id="1_rating_1"><label for="1_rating_1">☆</label>
                                        <div class="remark-box">
                                            <label for="remark_1">Please provide additional remarks:</label>
                                            <textarea id="remark_1" rows="4" cols="50"></textarea>
                                        </div>
                                    </fieldset>
                                
                                    <hr>
                                </div>
                                
                                <!-- <div class="col-md-6"></div> -->

                                <div class="col-md-6">
                                    <strong>Start Location:</strong> 
                                        <div class="img-thumbnail" style="height: 150px">
                                            <iframe id="startMap" width="150px"  height="100%" frameborder="0" style="border:0" allowfullscreen></iframe>
                                        </div>
                                    <p><b>Was meeting start location correct..??</b> </p>  

                                    <fieldset class="MoMrating" data-question="Was meeting start location correct" data-userid="" data-taskid="">
                                        <input type="radio" name="momrat2_2" value="5" id="10_rating_2"><label for="10_rating_2">☆</label>
                                        <input type="radio" name="momrat2_2" value="4" id="9_rating_2"><label for="9_rating_2">☆</label>
                                        <input type="radio" name="momrat2_2" value="3" id="8_rating_2"><label for="8_rating_2">☆</label>
                                        <input type="radio" name="momrat2_2" value="2" id="7_rating_2"><label for="7_rating_2">☆</label>
                                        <input type="radio" name="momrat2_2" value="1" id="6_rating_2"><label for="6_rating_2">☆</label>
                                        <div class="remark-box">
                                            <label for="remark_2">Please provide additional remarks:</label>
                                            <textarea id="remark_2" rows="4" cols="50"></textarea>
                                        </div>
                                    </fieldset>
                                <hr>
                                </div>
                                
                                <div class="col-md-6">
                                    <strong>End Location:</strong> 
                                    <div class="img-thumbnail" style="height: 150px">
                                        <iframe id="endMap"  width="150px"  height="100%" frameborder="0" style="border:0" allowfullscreen></iframe>
                                    </div>

                                    <p><b>Was meeting end location correct..??</b> </p>  
                                    <fieldset class="MoMrating" data-question="Was meeting end location correct" data-userid="" data-taskid="">
                                        <input type="radio" name="momrat2_4" value="5" id="20_rating_2"><label for="20_rating_2">☆</label>
                                        <input type="radio" name="momrat2_4" value="4" id="19_rating_2"><label for="19_rating_2">☆</label>
                                        <input type="radio" name="momrat2_4" value="3" id="18_rating_2"><label for="18_rating_2">☆</label>
                                        <input type="radio" name="momrat2_4" value="2" id="17_rating_2"><label for="17_rating_2">☆</label>
                                        <input type="radio" name="momrat2_4" value="1" id="16_rating_2"><label for="16_rating_2">☆</label>
                                        <div class="remark-box">
                                            <label for="remark_4">Please provide additional remarks:</label>
                                            <textarea id="remark_4" rows="4" cols="50"></textarea>
                                        </div>
                                    </fieldset>
                                    <hr>
                                </div>

                                <div class="col-md-6">
                                    <p> <strong>Photo:</strong> <img id="modalPhoto" src="" alt="Photo" class="img-fluid"> </p>

                                    <p><b>Was company photo is right..??</b> </p>
                                    <fieldset class="MoMrating" data-question="Was company photo is right" data-userid="" data-taskid="">
                                        <input type="radio" name="momrat2_5" value="5" id="25_rating_2"><label for="25_rating_2">☆</label>
                                        <input type="radio" name="momrat2_5" value="4" id="24_rating_2"><label for="24_rating_2">☆</label>
                                        <input type="radio" name="momrat2_5" value="3" id="23_rating_2"><label for="23_rating_2">☆</label>
                                        <input type="radio" name="momrat2_5" value="2" id="22_rating_2"><label for="22_rating_2">☆</label>
                                        <input type="radio" name="momrat2_5" value="1" id="21_rating_2"><label for="21_rating_2">☆</label>
                                        <div class="remark-box">
                                            <label for="remark_5">Please provide additional remarks:</label>
                                            <textarea id="remark_5" rows="4" cols="50"></textarea>
                                        </div>
                                    </fieldset>
                                </div>

                                
                                <div class="col-md-6">

                                    <p><strong>Is Potential?:</strong> <span id="potential"></span></p>
                            
                                    <p><b>Is Potential marked correctly..??</b> </p>
                                    <fieldset class="MoMrating" data-question="Potential marked correctly" data-userid="" data-taskid="">
                                        <input type="radio" name="momrat2_8" value="5" id="40_rating_2"><label for="40_rating_2">☆</label>
                                        <input type="radio" name="momrat2_8" value="4" id="39_rating_2"><label for="39_rating_2">☆</label>
                                        <input type="radio" name="momrat2_8" value="3" id="38_rating_2"><label for="38_rating_2">☆</label>
                                        <input type="radio" name="momrat2_8" value="2" id="37_rating_2"><label for="37_rating_2">☆</label>
                                        <input type="radio" name="momrat2_8" value="1" id="36_rating_2"><label for="36_rating_2">☆</label>
                                        <div class="remark-box">
                                            <label for="remark_8">Please provide additional remarks:</label>
                                            <textarea id="remark_8" rows="4" cols="50"></textarea>
                                        </div>
                                    </fieldset>
                                    
                                </div>
                                                            
                            </div>                            
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="button" class="btn btn-primary" onclick="submitMeetingReview()">Submit</button>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade" id="RatingReviewModal" tabindex="-1" role="dialog" aria-labelledby="modelTitleId" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <!-- <h5 class="modal-title">Modal title</h5> -->
                    </div>
                    <div class="modal-body">
                    <form >
                        <input type="hidden" name="starID" id="starID">
                        <textarea class="form-control" name="remark" id="remark" placeholder="Remark" required="true"></textarea>
                    </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="button" class="btn btn-primary" onclick="submitReview()">Submit</button>
                    </div>
                </div>
            </div>
        </div>
        

        

    </div>
</div>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
          
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

<!-- jQuery -->
<script src="<?=base_url();?>assets/js/jquery.min.js"></script>
    <!-- jQuery UI 1.11.4 -->
    <script src="<?=base_url();?>assets/js/jquery-ui.min.js"></script>
    <!-- Resolve conflict in jQuery UI tooltip with Bootstrap tooltip -->
    <!-- <script>
      $.widget.bridge('uibutton', $.ui.button)
    </script> -->
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
    <!-- <script src="plugins/jquery-knob/jquery.knob.min.js"></script> -->
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
    <!-- <script src="<?=base_url();?>assets/js/adminlte.js"></script> -->
    <!-- AdminLTE dashboard demo (This is only for demo purposes) -->
    <!-- <script src="<?=base_url();?>assets/js/dashboard.js"></script> -->

<script>
    $(document).ready(function() {
        $("#PlannerTable").DataTable({
            "responsive": false,
            "lengthChange": false,
            "autoWidth": false,
            "buttons": ["csv", "excel", "pdf", "print"]
        });
    });

</script>

<script>
    $(".rating input").on("click", function() {

        var $rating = $(this);

        // Get other data attributes
        var taskId = $rating.closest('.rating').data('taskid');
        var userId = $rating.closest('.rating').data('userid');
        var question = $rating.closest('.rating').data('question');
        var ratingValue = $rating.val();
       // alert(ratingValue);
        // console.log("Extracted ID: " + extractedId);
        // alert("Task ID: " + taskId + "\nUser ID: " + userId + "\nExtracted ID: " + extractedId + "\nRating Value: " + ratingValue);
        
        // Prevent the default action
        // return false;

        $.ajax({
        url: '<?=base_url();?>Menu/TaskCheckStarNew',
        type: 'POST',
        data: {
            rating: ratingValue,
            question: question,
            userId: userId,
            taskId: taskId
            // cdate:cdate
            },
            success: function(response) {
                console.log('Response:', response); 

                if (ratingValue <= 2) {

                    

                    $('#starID').val(response);
                    $('#RatingReviewModal').modal('show');

                }else{
                    $('#RatingReviewModal').modal('hide');
                    $('#success-message').show();

                   /* $('html, body').animate({
                        scrollTop: $('#success-message').offset().top
                    }, 1000);*/

                    setTimeout(function() {

                        $('this').closest('.rating').fadeOut('slow', function() {
                          //  location.reload();
                        });
                    }, 3000);
                }
            },
            error: function() {
                alert("There was an error submitting the rating.");
            }
        });
    });
</script>

<script>
    function parseDate(dateString) {

        const formattedDateString = dateString.replace(' ', 'T');
        return new Date(formattedDateString);

    }

    function OpenModal(id,userID){

        document.getElementById('taskID').value = id;
        document.getElementById('userID').value = userID;


        $.ajax({
            url: '<?=base_url();?>Menu/getMoMData',
            type: 'POST',
            data: {
                taskID: id,
                userID: userID
                },
                success: function(response) {

                    // console.log('Response:', JSON.parse(response)); 
                    var data = JSON.parse(response);
                    // alert(response)
        // Update modal content
                    
        // Calculate the difference in milliseconds
                    const startTime = parseDate(data.start_time);
                    const endTime = parseDate(data.end_time);

                    // Calculate the difference in milliseconds
                    const timeDifference = endTime - startTime;
                    // Convert the difference to minutes (or any other unit you prefer)
                    const differenceInMinutes = Math.floor(timeDifference / (1000 * 60)) + ' mins';
                    // const differenceInSeconds = Math.floor(timeDifference / 1000);
                    // alert(differenceInMinutes);
                    var Meetingimage = "https://stemapp.in/" +  data.cphoto;
                    // console.log(Meetingimage);
                    document.getElementById('modalPhoto').src = Meetingimage; 
                    document.getElementById('modalPlannedTime').textContent = data.plannedTime;
                    document.getElementById('modalInitiateTime').textContent = data.initiateTime;
                    document.getElementById('modalStartTime').textContent = data.start_time;
                    document.getElementById('modalEndTime').textContent = data.end_time;
                    document.getElementById('modalDuration').textContent = differenceInMinutes;
                    document.getElementById('modalMomType').textContent = data.momType || 'Not Updated';
                    document.getElementById('modalEndTime').textContent = data.end_time;
                    // document.getElementById('modalPartnerType').textContent = data.mompartner;
                    // document.getElementById('momRemark').textContent = data.momremark;
                    document.getElementById('meetingType').textContent = data.actionName;
                    document.getElementById('meetingFrom').textContent = data.funnel;
                    // document.getElementById('status').textContent = data.statusAtMeeting;
                    document.getElementById('potential').textContent = data.potentialOrNot;

                    var startLat = data.start_lat;
                    var startLong = data.start_long;
                    var endLat = data.end_lat;
                    var endLong = data.end_long;

                    document.getElementById('startMap').src = `https://maps.google.com/?q=${startLat},${startLong}&t=k&z=13&ie=UTF8&iwloc=&output=embed`;
                    
                    // Update the end location map and link

                    document.getElementById('endMap').src = `https://maps.google.com/?q=${endLat},${endLong}&t=k&z=13&ie=UTF8&iwloc=&output=embed`;

                    $('#MoMModal').modal('show');
                },
                error: function() {
                    alert("There was an error submitting the rating.");
                }
            });


            $.ajax({
            url: '<?=base_url();?>Menu/getMoMTaskDataonTaskCheckModal',
            type: 'POST',
            data: {
                taskID: id,
                userID: userID
                },
                success: function(response) {
                    $("#momdetails").html(response);
                }
            });



    }


    function OpenReviewModal(id,userID){

        document.getElementById('taskID').value = id;
        document.getElementById('taskManyID').value = id;

        document.getElementById('userID').value = userID;
        document.getElementById('userManyID').value = userID;


        // document.getElementById('userID').value = userID;


        $.ajax({
            url: '<?=base_url();?>Menu/getReviewData',
            type: 'POST',
            data: {
                taskID: id,
                // userID: userID
                },
                success: function(response) {

                    // console.log(JSON.parse(response)); 
                    var data = JSON.parse(response);
                    // alert(response)
                    // data.manyreview = 1;

                    if (data.manyreview == 0) {
                        
                        document.getElementById('reviewRemark').textContent = data.remark; 
                        // document.getElementById('TaskUpdate').textContent = data.TaskUpdate; 
                        // document.getElementById('ReviewPlanTime').textContent = data.reviewPlan_time;
                        // document.getElementById('ReviewStartTime').textContent = data.reviewStart_time;
                        // document.getElementById('ReviewEndTime').textContent = data.reviewEnd_time || 'Not Set';
                        // document.getElementById('modalMomType').textContent = data.momType || 'Not Updated';
                        // document.getElementById('ReviewType').textContent = data.reviewType;
                        // document.getElementById('BDName').textContent = data.BDName;
                        // document.getElementById('meetingFrom').textContent = data.funnel;
                        document.getElementById('FixDate').textContent = data.fixdate;
                        document.getElementById('ExistingStatus').textContent = data.exStatus;
                        document.getElementById('CurrentStatus').textContent = data.currentStatus;
                        document.getElementById('CallRemark').textContent = data.callRemark;
                        document.getElementById('EmailRemark').textContent = data.emailRemark;
                        document.getElementById('MeetingRemark').textContent = data.meetingRemark;

                        document.getElementById('topSpender').textContent = data.topspender;
                        document.getElementById('keyClient').textContent = data.keyclient;
                        document.getElementById('PositiveKeyClient').textContent = data.pkeyclient;
                        document.getElementById('PriorityClient').textContent = data.priorityclient;
                        document.getElementById('UpsellClient').textContent = data.upsellclient;
                        document.getElementById('FocusFunnel').textContent = data.focusyclient;
                        document.getElementById('potential').textContent = data.potential;
                        
                        $('#ReviewModal').modal('show');

                    } else {

                        // document.getElementById('reviewRemarkMany').textContent = data.review_remark; 
                        // document.getElementById('howManyTaskDone').textContent = data.NoOfTask; 
                        // document.getElementById('frequencyOfTask').textContent = data.frequency_of_the_task;
                        // document.getElementById('rpMeetingdone').textContent = data.RPMeeting;
                        // document.getElementById('writeMoMDone').textContent = data.mom_done;
                        // document.getElementById('socialNetworking').textContent = data.SN_done;
                        // document.getElementById('categoryRight').textContent = data.category_right;
                        // document.getElementById('isCurrentStatusRight').textContent = data.currentStatus_right;
                        // document.getElementById('howManyBargMeeting').textContent = data.many_times_barge_meeting;
                        // document.getElementById('researchProspecting').textContent = data.research_prospecting;
                        // document.getElementById('BaseLocationOrOutStationTravel').textContent = data.base_or_travel_location;
                        // // document.getElementById('isPrioritytoClouser').textContent = data.fixdate;
                        // document.getElementById('partnerType').textContent = data.partnerType;
                        // document.getElementById('needSupport').textContent = data.suppert;

                        $('#ManyReviewModal').modal('show');
                    }
                    // alert(response)

                    

                },
                error: function() {
                    alert("There was an error submitting the rating.");
                }
            });

    }
</script>

<script>
    function submitReview(){

        var remark = document.getElementById("remark").value;
        var starID = document.getElementById("starID").value;

        $.ajax({
            url: '<?=base_url();?>Menu/updateTaskCheckRemark',
            type: 'POST',
            data: {
                remark: remark,
                starID: starID,    
            },
            success: function(response) {
                $('#ReviewModal').modal('hide');

                $('#success-message').show();

              /*  $('html, body').animate({
                    scrollTop: $('#success-message').offset().top
                }, 1000);*/

                setTimeout(function() {
                    $('#success-message').fadeOut('slow');
                }, 3000);

                  //  location.reload();
            },
            error: function() {
                alert("There was an error submitting the rating.");
            }
        });
    }
</script>

<script>
        document.addEventListener('DOMContentLoaded', () => {
            // Get all fieldsets within the modal
            const fieldsets = document.querySelectorAll('.MoMrating');

            fieldsets.forEach(fieldset => {
                // Add event listener to each radio button in the fieldset
                fieldset.addEventListener('change', (event) => {

                    const selectedRadio = event.target;
                    const remarkBox = fieldset.querySelector('.remark-box');

                    // console.log(selectedRadio.value)

                    // Disable the entire fieldset once a radio button is selected
                    // fieldset.classList.add('disabled-fieldset');
                    // fieldset.classList.add('disabled-fieldset');

                    if (selectedRadio.type === 'radio') {
                        if (parseInt(selectedRadio.value, 10) <= 2) {
                            // Show the remark box
                            remarkBox.classList.add('visible'); 
                            // remarkBox.classList.remove('disabled');
                        } else {
                            // Hide the remark box
                            remarkBox.classList.remove('visible');
                            // remarkBox.classList.remove('visible');
                        }

                        const radios = fieldset.querySelectorAll('input[type="radio"]');
                        radios.forEach(radio => {
                            radio.disabled = true;
                        });
                    }
                });
            });


        });

        function submitMeetingReview() {
            const form = document.getElementById('momForm');
            const data = [];

            // var taskID = document.getElementById('taskID');
            var taskID = document.getElementById('taskID').value.trim();
            var userID = document.getElementById('userID').value.trim();

            // alert(taskID);

            // Collect ratings and associated questions
            document.querySelectorAll('.MoMrating').forEach(fieldset => {
                const question = fieldset.getAttribute('data-question');
                const selectedRating = fieldset.querySelector('input[type="radio"]:checked');
                const ratingValue = selectedRating ? selectedRating.value : null;
                const remarkBox = fieldset.querySelector('.remark-box');
                const remarkTextarea = remarkBox ? remarkBox.querySelector('textarea') : null;
                const remarkValue = remarkTextarea ? remarkTextarea.value.trim() : null;
                
                

                if (question && ratingValue) {
                // Push the formatted object into the data array
                data.push({
                    question: question,
                    star: ratingValue,
                    remarks: remarkValue,
                    task_id: taskID,
                    user_id : userID
                });
            }
            });

            // data['taskID'] = taskID;

            // Convert data to JSON (or another format if needed)
            const jsonData = JSON.stringify(data);

            alert(jsonData);
            // Log data for debugging
            console.log('Submitted Data:', data);

            // Example of sending data via fetch
            $.ajax({
                url: '<?=base_url();?>Menu/submitMoMRating',
                method: 'POST',
                contentType: 'application/json',
                data: jsonData,
                dataType: 'json',
                success: function(result) {
                    console.log('Success:', result);
                    // Handle success (e.g., show a success message, close the modal, etc.)
                    $('#MoMModal').modal('hide');
                },
                error: function(xhr, status, error) {
                    console.error('Error:', error);
                    // Handle error (e.g., show an error message)
                }
            });
        }


        function submitManyReviewModal() {
            // const form = document.getElementById('momForm');
            const data = [];

            // var taskID = document.getElementById('taskID');
            var taskID = document.getElementById('taskManyID').value.trim();
            var userID = document.getElementById('userManyID').value.trim();

            // alert(taskID);

            // Collect ratings and associated questions
            document.querySelectorAll('.MoMrating').forEach(fieldset => {
                const question = fieldset.getAttribute('data-question');
                const selectedRating = fieldset.querySelector('input[type="radio"]:checked');
                const ratingValue = selectedRating ? selectedRating.value : null;
                const remarkBox = fieldset.querySelector('.remark-box');
                const remarkTextarea = remarkBox ? remarkBox.querySelector('textarea') : null;
                const remarkValue = remarkTextarea ? remarkTextarea.value.trim() : null;
                
                

                if (question && ratingValue) {
                // Push the formatted object into the data array
                data.push({
                    question: question,
                    star: ratingValue,
                    remarks: remarkValue,
                    task_id: taskID,
                    user_id : userID
                });
            }
            });

            // data['taskID'] = taskID;

            // Convert data to JSON (or another format if needed)
            const jsonData = JSON.stringify(data);

            // alert(jsonData);
            // Log data for debugging
            // console.log('Submitted Data:', data);

            // Example of sending data via fetch
            $.ajax({
                url: '<?=base_url();?>Menu/submitMoMRating',
                method: 'POST',
                contentType: 'application/json',
                data: jsonData,
                dataType: 'json',
                success: function(result) {
                    // console.log('Success:', result);
                    // Handle success (e.g., show a success message, close the modal, etc.)
                 //   location.reload();
                    $('#ManyReviewModal').modal('hide');
                },
                error: function(xhr, status, error) {
                    console.error('Error:', error);
                    // Handle error (e.g., show an error message)
                }
            });
        }


        function submitReviewModal() {
            // const form = document.getElementById('momForm');
            const data = [];

            // var taskID = document.getElementById('taskID');
            var taskID = document.getElementById('taskID').value.trim();
            var userID = document.getElementById('userID').value.trim();

            // alert(taskID);

            // Collect ratings and associated questions
            document.querySelectorAll('.MoMrating').forEach(fieldset => {
                const question = fieldset.getAttribute('data-question');
                const selectedRating = fieldset.querySelector('input[type="radio"]:checked');
                const ratingValue = selectedRating ? selectedRating.value : null;
                const remarkBox = fieldset.querySelector('.remark-box');
                const remarkTextarea = remarkBox ? remarkBox.querySelector('textarea') : null;
                const remarkValue = remarkTextarea ? remarkTextarea.value.trim() : null;
                
                

                if (question && ratingValue) {
                // Push the formatted object into the data array
                data.push({
                    question: question,
                    star: ratingValue,
                    remarks: remarkValue,
                    task_id: taskID,
                    user_id : userID
                });
            }
            });

            // data['taskID'] = taskID;

            // Convert data to JSON (or another format if needed)
            const jsonData = JSON.stringify(data);

            // alert(jsonData);
            // Log data for debugging
            // console.log('Submitted Data:', data);

            // Example of sending data via fetch
            $.ajax({
                url: '<?=base_url();?>Menu/submitMoMRating',
                method: 'POST',
                contentType: 'application/json',
                data: jsonData,
                dataType: 'json',
                success: function(result) {
                    // console.log('Success:', result);
                    // Handle success (e.g., show a success message, close the modal, etc.)
                   // location.reload();
                    $('#ReviewModal').modal('hide');
                },
                // error: function(xhr, status, error) {
                //     console.error('Error:', error);
                //     // Handle error (e.g., show an error message)
                // }
            });
        }

</script>

<script>
    function clearSelectedUser() {
    // Clear the selected user value
        if (confirm("Are you sure you want to submit?")) {
            // Clear the selected user value
            document.querySelector('select[name="userId"]').selectedIndex = 0; // Reset to the first option
            // Optionally, enable the select again
            document.querySelector('select[name="userId"]').disabled = false; 
            // Reload the page
            location.reload(); 
            // Submit the form if needed
            document.getElementById('taskForm').submit();
        }

    }
</script>

</body>
</html>
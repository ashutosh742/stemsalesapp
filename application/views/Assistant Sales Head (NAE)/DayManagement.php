
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Day Management | STEM Sales | WebAPP</title>
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
    <style>
      .profileimgae{
      box-shadow: rgba(0, 0, 0, 0.17) 0px -23px 25px 0px inset, rgba(0, 0, 0, 0.15) 0px -36px 30px 0px inset, rgba(0, 0, 0, 0.1) 0px -79px 40px 0px inset, rgba(0, 0, 0, 0.06) 0px 2px 1px, rgba(0, 0, 0, 0.09) 0px 4px 2px, rgba(0, 0, 0, 0.09) 0px 8px 4px, rgba(0, 0, 0, 0.09) 0px 16px 8px, rgba(0, 0, 0, 0.09) 0px 32px 16px;
      padding: 10px;
      }
      .blink {
      animation: blinker 1.5s linear infinite;
      color: red;
      font-family: sans-serif;
      }
      @keyframes blinker {
      50% {
      opacity: 0;
      }
      }
      .textarea_message{
      background: cornsilk;
      width: 80%;
      }
      .hrclass{
      box-shadow: rgba(0, 0, 0, 0.17) 0px -23px 25px 0px inset, rgba(0, 0, 0, 0.15) 0px -36px 30px 0px inset, rgba(0, 0, 0, 0.1) 0px -79px 40px 0px inset, rgba(0, 0, 0, 0.06) 0px 2px 1px, rgba(0, 0, 0, 0.09) 0px 4px 2px, rgba(0, 0, 0, 0.09) 0px 8px 4px, rgba(0, 0, 0, 0.09) 0px 16px 8px, rgba(0, 0, 0, 0.09) 0px 32px 16px;
      }
      marquee.p-2.mt-1 {
      box-shadow: rgba(9, 30, 66, 0.25) 0px 1px 1px, rgba(9, 30, 66, 0.13) 0px 0px 1px 1px;
      }
      #goodmessage{color:green;}
    </style>

<script>
      function validateTimeInputAuto(event) {
          const input = event.target;
          const timeValue = input.value;
          const minTime = "16:00";
          const maxTime = "19:00";
      
          if (timeValue < minTime || timeValue > maxTime) {
              alert("Please enter a time between 04:00 PM and 7:00 PM.");
              input.value = "";
          }
      }

      document.addEventListener('DOMContentLoaded', function() {
          const timeInput = document.getElementById('start-time');
          timeInput.setAttribute('min', '16:00');
          timeInput.setAttribute('max', '19:00');
          timeInput.addEventListener('change', validateTimeInputAuto);
      });
      document.addEventListener('DOMContentLoaded', function() {
          const timeInput = document.getElementById('end-time');
          timeInput.setAttribute('min', '16:00');
          timeInput.setAttribute('max', '19:00');
          timeInput.addEventListener('change', validateTimeInputAuto);
      });
</script>


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
    <?php
    $tom_autotask_time = sizeof($gettoAutoTaskTime);
    // $tom_autotask_time = 1;
    ?>
    <section class="content">
      <div class="container-fluid">
        <?php
          if ($this->session->flashdata('error_message')): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
          <strong> <?php echo $this->session->flashdata('error_message'); ?></strong>
          <button type="button" class="close" data-dismiss="alert" aria-label="Close">
          <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <?php endif; ?>
        <?php
          if ($this->session->flashdata('success_message')): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
          <strong> <?php echo $this->session->flashdata('success_message'); ?></strong>
          <button type="button" class="close" data-dismiss="alert" aria-label="Close">
          <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <?php endif; ?>
        <?php  
          $yestdatacnt = sizeof($yestdata);
          $uystart_id = $yestdata[0]->id;
          $uystart = $yestdata[0]->ustart;
          $uyclose = $yestdata[0]->uclose;

          // Check Yesterday Day Close or Not
          if ($yestdatacnt == 1) {?>
        <div class="row">
          <div class="col-sm-12 col-md-12 col-lg-12 m-auto">
            <div class="card card-primary card-outline">
              <div class="card-body box-profile">
                <div class="blink">
                  <h3 class="text-center">You forgot to close your day yesterday</h3>
                </div>
                <marquee class="p-2 mt-1" width="100%" behavior="alternate" bgcolor="pink">
                  <h6> * Please submit a request to close yesterday's day and begin today's</h6>
                </marquee>
                <hr>
                <div class="row">
                  <div class="col-md-3 text-center">
                    <div class="card bg-success p-2">
                      <p>You have Started your Day at </p>
                      <hr>
                      <b><?=$uystart ?></b>
                    </div>
                  </div>
                  <div class="col-md-3 text-center">
                    <div class="card bg-danger p-2">
                      <p>But you have not closed your day yet.</p>
                      <hr>
                      <b>0000-00-00 00:00:00</b>
                    </div>
                  </div>
                  <div class="col-md-3 text-center">
                    <div class="card bg-warning p-2">
                      <p>Time diffrence is </p>
                      <hr>
                      <b><?=$this->Menu_model->timediff($uystart,$ctdate);?></b>
                    </div>
                  </div>
                  <div class="col-md-3 text-center">
                    <div class="card bg-warning p-2">
                      <p>Yesterday Pending Task </p>
                      <hr>
                      <b><?php 
                          $getoldPendingTask = $this->Menu_model->get_OLDPendingTask($uid);
                          echo $getoldPendingTaskcnt = sizeof($getoldPendingTask);
                      ?></b>
                    </div>
                  </div>
                </div>
                <hr class="hrclass" style="width: 500px;"/>
                <?php          
                  $getDayCloseRequest = $this->Menu_model->GetDayCloseRequest($uid,$tdate);
                  $getDayCloseRequescnt = sizeof($getDayCloseRequest);

                  if($getDayCloseRequescnt  == 0){ ?>
                <form action="<?=base_url();?>Menu/dayscRequest" method="post" enctype="multipart/form-data">
                  <center>
                    <div class="row">
                      <div class="col">

                      <?php    
                    $gecurAutoTaskTime = sizeof($gecurAutoTaskTime);
                    $message = ($gecurAutoTaskTime) ? "selected" : "disabled";
                    ?>

                        <label for="validationServer04" class="form-label">
                        * Why did you not close your day yesterday?
                        </label>
                        <input type="hidden" value="<?= $uystart_id ?>" name="req_id">
                        <select class="form-control is-invalid" id="validationServer04" aria-describedby="validationServer04Feedback" name="would_you_want" required style="width:500px;" >
                          <option selected disabled value="">Choose...</option>
                          <option <?=$message?> value="I was caught up with an urgent task and lost track of time.">I was caught up with an urgent task and lost track of time.</option>
                          <option value="I encountered unexpected issues that took longer to resolve than planned.">I encountered unexpected issues that took longer to resolve than planned.</option>
                          <option value="I had a personal emergency that required my immediate attention.">I had a personal emergency that required my immediate attention.</option>
                          <option value="I forgot to update the system at the end of the day.">I forgot to update the system at the end of the day.</option>
                          <option value="I had difficulty accessing the system due to technical issues.">I had difficulty accessing the system due to technical issues.</option>
                          <option value="I had a backlog of work and wasn't able to finish everything on time.">I had a backlog of work and wasn't able to finish everything on time.</option>
                          <option value="I was working late on a high-priority project and didn't get a chance to update the records.">I was working late on a high-priority project and didn't get a chance to update the records.</option>
                          <option value="I was out of the office and unable to complete the update remotely.">I was out of the office and unable to complete the update remotely.</option>
                          <option value="We forgot to set our next day planner.">We forgot to set our next day planner</option>
                        </select>
                        <div id="validationServer04Feedback" class="invalid-feedback">
                          * Please select a valid state.
                        </div>
                      </div>
                    </div>

                   
                    <input type="hidden" name="autotasktimeisset" value="<?=$gecurAutoTaskTime?>">
                    <?php
                    if($gecurAutoTaskTime == 0){  ?>
                      <hr class="hrclass" style="width: 600px;"/>
                    <div class="card">
              <div class="card-body" id="mainboxAutoTask1">
                <h5><i>Today's Auto Task and Planned Time</i></h5>
                <hr/>
               
                <div class="row">
                  
                <div class="col-sm-6 col-sm mt-3">
                <marquee class="p-2 mt-1" width="100%"  onMouseOver="this.stop()" onMouseOut="this.start()" behavior="left" bgcolor="pink">
                  <h6> Auto task time should be between 4:00 PM to 7:00 PM and maximum duration of 90 minutes. </h6>
                </marquee>

                  <div class="form-group">
                    <label for="start-time">Enter Start Time</label>
                    <input type="time" id="start-time" name="startautotasktime" class="form-control is-invalid" required>
                  </div>

                  <div class="form-group">
                    <label for="end-time">Enter End Time</label>
                    <input type="time" id="end-time" name="endautotasktime" class="form-control is-invalid" required>
                  </div>
                  </div>
                  <div class="col-sm-6 col-sm mt-3">
                  <marquee class="p-2 mt-1" width="100%"  onMouseOver="this.stop()" onMouseOut="this.start()" behavior="left" bgcolor="pink">
                  <h6>Plan Your day for Today,You will get max 1 hour to plan all the tasks for the day.</h6>
                </marquee>
                      <div class="form-group">
                        <label for="end-time">Today's Planner Time start.</label>
                        <input type="time" readonly id="start_tttpft" name="start_tttpft" class="form-control is-invalid" required>
                      </div>

                      <div class="form-group">
                        <label for="end-time">Today's Planner Time End.</label>
                        <input type="time" readonly id="end_tttpft" name="end_tttpft" class="form-control is-invalid" required>
                      </div>
                  </div>
                </div>
              </div>
            </div>
    <?php } ?>



                    <hr class="hrclass" style="width: 600px;"/>
                    <div class="mb-3">
                      <label for="requestForTodaysTaskPlan" class="form-label">* Please specify the reason : </label>
                      <textarea class="form-control textarea_message" name="requestForTodaysTaskPlan" id="requestForTodaysTaskPlan" placeholde="* Please specify the reason." required rows="3"></textarea>
                      <div class="invalid-feedback">* Invalid Message</div>
                    </div>
                  </center>
                  <br>
                  <div class="col1 text-center">
                    <button type="submit" class="btn btn-danger">Create Request</button>
                  </div>
                </form>
                <?php }else{
                  $approved_status = $getDayCloseRequest[0]->approved_status;
                  if($approved_status == '' || $approved_status == 'Reject'){
                  ?>
                <div class="table-responsive">
                  <table class="table table-bordered">
                    <thead class="bg-primary">
                      <tr>
                        <th scope="col">#</th>
                        <th scope="col">Name</th>
                        <th scope="col">Request Date</th>
                        <th scope="col">Why Did You</th>
                        <th scope="col">Request Message</th>
                        <th scope="col">Approvel Status</th>
                        <th scope="col"><?= $approved_status ?> By</th>
                        <th scope="col"><?= $approved_status ?> Remarks</th>
                      </tr>
                    </thead>
                    <tbody>
                      <?php $i=1; foreach($getDayCloseRequest as $data){ ?>
                      <tr>
                        <td><?= $i; ?></td>
                        <td><?= $this->Menu_model->get_userbyid($data->user_id)[0]->name ?></td>
                        <td><?= $data->req_date ?></td>
                        <td><?= $data->why_did_you ?></td>
                        <td><?= $data->req_remarks ?></td>
                        <td>
                          <?php
                            if($data->approved_status == 'Approved'){
                              echo "<span class='bg-success p-2'>Approved</span>";
                            }else if($data->approved_status == 'Reject'){
                                echo "<span class='bg-danger p-2'>Reject</span>";
                              }else{
                                echo "<span class='bg-warning p-2'>Pending</span>";
                              }
                            ?>
                        </td>
                        <td><?= $this->Menu_model->get_userbyid($data->approved_by)[0]->name ?></td>
                        <td><?= $data->approved_remarks ?></td>
                      </tr>
                      <?php $i++; } ?>
                    </tbody>
                  </table>
                </div>
                <?php }else{ ?>
                <div class="row p-3">
                  <div class="col-sm col-md-6 col-lg-6 m-auto">
                    <div class="card card-primary card-outline">
                      <div class="card-body box-profile">
                        <h3 class=" bg-info p-2 text-center">Close Your Yesterday Day</h3>
                        <hr class="hrclass" style="width: 300px;"/>
                        <form action="<?=base_url();?>Menu/YesterdayDayClose" method="post" enctype="multipart/form-data">
                          <div class="form-group">
                            <input type="hidden" value="<?= $uystart_id ?>" name="req_id">
                            <input type="hidden" name="user_id" value="<?=$uid?>">
                            <center>
                            <b class="text-info">Today's Date : <?=date('d-m-Y');?> </b>
                            <hr class="hrclass" style="width: 300px;"/>
                            <p>You have Started your Day at <b><?=$uystart?></b></p>
                            <hr class="hrclass" style="width: 300px;"/>
                            <p>You are Closing your Day at <b><?=$cdate=date('H:i:s');?></b></p>
                            <hr class="hrclass" style="width: 300px;"/>
                            <p>Time diffrence is <b><?=$this->Menu_model->timediff($uystart,$cdate);?></b></p>
                            <hr class="hrclass" style="width: 300px;"/>
                            <div class="mb-4 d-flex justify-content-center">
                              <img class="border profileimgae" id="blah" src="https://cdn-icons-png.flaticon.com/512/9187/9187604.png" alt="your image" style="width:250px;height:250px"/>
                            </div>
                            <div class="d-flex justify-content-center">
                              <div class="btn btn-info btn-rounded">
                                <label class="form-label text-white m-1" for="imgInp">Take Selfie</label>
                                <input type="file" class="form-control d-none" id="imgInp" name="filname" accept="image/*" capture required/>
                              </div>
                            </div>
                            <input type="hidden" id="lat" name="lat">
                            <input type="hidden" id="lng" name="lng">
                          </div>
                          <div id="location">
                            <div id="map-container-google-3" class="z-depth-1-half map-container-3 p-3 m-3 border">
                              <iframe style="width:100%;height:200px;" id="mylocation" src="" frameborder="0" style="border:0" allowfullscreen></iframe>
                            </div>
                            <div class="form-group text-center">
                              <button type="submit" class="btn btn-danger" id="closebtn" onclick="this.form.submit(); this.disabled = true;">Close Your Day</button>
                            </div>
                          </div>
                        </form>
                      </div>
                    </div>
                  </div>
                </div>
                <?php } } ?>
              </div>
            </div>
          </div>
        </div>
        <?php }else{ ?>
        <?php if($do==0){?>
        <div class="row p-3">
          <div class="col-sm col-md-12 col-lg-12 m-auto">
            <div class="card card-primary card-outline">
              <div class="card-body box-profile">
                <h3 class="text-center">Start Your Day</h3>
                <hr>
                <form action="<?=base_url();?>Menu/daysc" method="post" enctype="multipart/form-data">
                  <div class="form-group">
                    <input type="hidden" name="user_id" value="<?=$uid?>">
                    <center>
                    <b class="text-info">Today's Date : <?=date('d-m-Y');?> </b>
                    <?php date_default_timezone_set("Asia/Kolkata"); ?>
                    <input type="hidden" name="ustart" value="<?=date('Y-d-m H:i:s')?>">
                    <p>You Are Starting Day at <b><?=date('H:i:s');?></b><br><br>
                    <div class="mb-4">
                      <select class="form-control" name="wffo" id="wffo" style="width:400px" required>
                      <option value="">Start Your Day</option>
                        <?php $userdfrom = $this->Menu_model->userworkfrom() ?>
                            <?php foreach($userdfrom as $udfrom){ ?>
                            <option value="<?= $udfrom->ID; ?>"><?= $udfrom->TYPE; ?></option>
                            <?php } ?>
                      </select>
                    </div>
                    <div class="mb-4 d-flex justify-content-center">
                      <img class="border profileimgae" id="blah" src="https://cdn-icons-png.flaticon.com/512/9187/9187604.png" alt="your image" style="width:250px;height:250px"/>
                    </div>
                    <div class="d-flex justify-content-center">
                      <div class="btn btn-info btn-rounded">
                        <label class="form-label text-white m-1" for="imgInp">Take Selfie</label>
                        <input type="file" class="form-control d-none" id="imgInp" name="filname" accept="image/*" capture required/>
                      </div>
                    </div>
                    <input type="hidden" id="lat" name="lat">
                    <input type="hidden" id="lng" name="lng">
                    <input type="hidden" name="do" value="<?=$do?>">
                  </div>
                  <div id="location">
                    <div id="map-container-google-3" class="z-depth-1-half map-container-3 p-3 m-3 border">
                      <iframe style="width:100%;height:200px;" id="mylocation" src="" frameborder="0" style="border:0" allowfullscreen></iframe>
                    </div>
                  </div>
                  <p class="form-group text-center">
                    <button type="submit" class="btn btn-success" id="submitButton" >Start Your Day</button>
                   <center>
                   <p id="goodmessage"></p>
                   </center>
                  </div>
                </form>


                <?php 
                $geturdata = $this->Menu_model->change_user_day_request($uid);
                $geturdatacnt = sizeof($geturdata);
                if($geturdatacnt > 0){ ?>
<hr>
<div class="card p-5">
<h5 class="bg-info p-2 text-center">Your Request to change the start your Days</h5>

<table class="table table-striped">
    <thead class="thead-dark">
        <tr>
            <th>ID</th>
            <th>Name</th>
            <th>Date</th>
            <th>Want To Start</th>
            <th>Message</th>
            <th>Approved By</th>
            <th>Approval Status</th>
            <th>Admin Message</th>  
        </tr>
    </thead>
    <tbody>
        <?php 
        $i=1;
        foreach ($geturdata as $row): ?>
        <tr>
            <td><?php echo $i; ?></td>
            <td><?php 
             $udetail = $this->Menu_model->get_userbyid($row->user_id);
             $username = $udetail[0]->name;
             echo $username;
            ?></td>
            <td><?php echo $row->date; ?></td>
            <td><?php 
            echo $this->Menu_model->userworkfrombyid($row->user_want_start)[0]->TYPE;
           ?></td>
            <td><?php echo $row->message; ?></td>
            <td><?php 
            if($row->apr_by == 0){
              echo "<span class='p-1 bg-warning'>Pending</span>";
            }else{
              $udetail = $this->Menu_model->get_userbyid($row->apr_by);
              $admidname = $udetail[0]->name;
              echo $admidname;
            }
            ?></td>
            <td><?php 
             if($row->apr_status == 0){
              echo "<span class='p-1 bg-warning'>Pending</span>";
            }elseif($row->apr_status == 1){
              echo "<span class='p-1 bg-success'>Approved</span>";
            }elseif($row->apr_status == 2){
              echo "<span class='p-1 bg-danger'>Reject</span>";
            }
            ?></td>
            <td><?php 
            if($row->amessage == ''){
              echo "<span class='p-1 bg-warning'>Pending</span>";
            }else{
              echo $row->amessage; 
            }
            ?></td>
        </tr>
        <?php $i++; endforeach; ?>
    </tbody>
</table>
</div>
               <?php } ?>


              </div>
            </div>
          </div>
        </div>


        <div class="modal fade" id="exampleModalReminder" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                <div class="modal-dialog" role="document">
                <div class="modal-content">
                <div class="modal-header bg-info text-center">
                <h5 class="modal-title" id="exampleModalLabel">Create a request to change the start your Days</h5>

                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
                </button>
                </div>
                <div class="modal-body">
                <p id="changemessage" class="text-danger text-center" ></p>
                <hr>
                <form action="<?=base_url();?>Management/SendRequestForDayStartChnage" method="post">
                <input type="hidden" id="user_want_start" name="user_want_start" class="form-control d-none" />
                <div class="form-group">
                <label>Write down why you want to change : </label>
                <textarea class="form-control" name="message" rows="3"></textarea>
                </div>
                <div class="form-group text-center">
                <button type="submit" class="btn btn-primary">Send Request</button>
                </div>
                </form>
                </div>

                </div>
                </div>
                </div>




    </section>
    <?php } if($do==1){?>
    <section class="content">
    <div class="container-fluid">
    <div class="row p-3">
    <div class="col-sm col-md-6 col-lg-6 m-auto">
    <div class="card card-primary card-outline">

    <?php 
    if($tom_autotask_time !== 0 ){
    ?>
    <div class="card-body box-profile">
    <h3 class="text-center">Close Your Day</h3>
    <hr>
    <?php $adate=date('Y-m-d', strtotime('+3 day')); 
      $totaltaktimep = $this->Menu_model->get_totaltaktimep($uid,$adate); 
      
      $ttime = $totaltaktimep[0]->ttime; 
      // if($ttime>=420){
        ?>
    <form action="<?=base_url();?>Menu/daysc" method="post" enctype="multipart/form-data">
    <div class="form-group">
    <input type="hidden" name="user_id" value="<?=$uid?>">
    <center><b class="text-info">Today's Date : <?=date('d-m-Y');?> </b>
    <p>You have Started your Day at <b><?=$ustart=$mdata[0]->ustart?></b></p>
    <p>You have Closing your Day at <b><?=$cdate=date('H:i:s');?></b></p>
    <p>Time diffrence is <b><?=$this->Menu_model->timediff($ustart,$cdate);?></b></p>
    <div class="mb-4 d-flex justify-content-center">
    <img class="border profileimgae" id="blah" src="https://cdn-icons-png.flaticon.com/512/9187/9187604.png" alt="your image" style="width:250px;height:250px"/>
    </div>
    <div class="d-flex justify-content-center">
    <div class="btn btn-info btn-rounded">
    <label class="form-label text-white m-1" for="imgInp">Take Selfie</label>
    <input type="file" class="form-control d-none" id="imgInp" name="filname" accept="image/*" capture required/>
    </div>
    </div>
    <input type="hidden" id="lat" name="lat">
    <input type="hidden" id="lng" name="lng">
    <input type="hidden" name="do" value="<?=$do?>">
    </div>
    <div id="location">
    <div id="map-container-google-3" class="z-depth-1-half map-container-3 p-3 m-3 border">
    <iframe style="width:100%;height:200px;" id="mylocation" src="" frameborder="0" style="border:0" allowfullscreen></iframe>
    </div>
    <div class="form-group text-center">
    <button type="submit" class="btn btn-danger" id="closebtn" onclick="this.form.submit(); this.disabled = true;">Close Your Day</button>
    </div>
    </div>
    </form>
    <?php 
      // } else{echo "<center><h5 class='text-danger'>Make sure to schedule at least 7 hours of tasks for the next day.<h5></center>";}     
      ?>
    </div>

    <?php  } else{  ?>
      <h4 class="p-2 text-center" >First Set Your Next Days Planner,Only after that you can close your day</h4>
<?php  }?>
    </div>
    </div>   
    </div>     
    </section>
    <?php } if($do==2){?>
    <section class="content">
    <div class="container-fluid">
    <div class="row p-3">
    <div class="col-sm col-md-6 col-lg-6 m-auto">
    <div class="card card-primary card-outline">
    <div class="card-body box-profile">
    <h3 class="text-center">Manage Your Day</h3>
    <hr>
    <div class="form-group">
    <input type="hidden" name="user_id" value="<?=$uid?>">
    <center><b class="text-info">Today's Date : <?=date('d-m-Y');?> </b>
    <p>You Are Started Day at <b><?=$mdata[0]->ustart?></b></p>
    <p>You Are Closed Day at <b><?=$mdata[0]->uclose?></b></p>
    </div>
    </div>
    </div>
    </div>   
    <?php } }?>
    </div>     
    </section>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
    <script type='text/javascript'>
      document.getElementById("location").style.display = "none";
      imgInp.onchange = evt => {
        const [file] = imgInp.files
        if (file) {
          blah.src = URL.createObjectURL(file);
          document.getElementById("location").style.display = "block";
        }
      }
      var x = document.getElementById("lat");
      var y = document.getElementById("lng");
      var z = document.getElementById("mylocation");
      $(document).ready(function(){
          if (navigator.geolocation) {
          navigator.geolocation.getCurrentPosition(showPosition);
        } else { 
          x.value = "Geolocation is not supported by this browser.";
        }
      });
      function showPosition(position) {
        x.value = position.coords.latitude; 
        y.value = position.coords.longitude;
        var a = position.coords.latitude;
        var b = position.coords.longitude;
        mylocation.src = "https://maps.google.com/?q="+a+","+b+"&t=k&z=13&ie=UTF8&iwloc=&output=embed";
      }
      $('#lat').on('change', function() {
         document.getElementById("closebtn").disabled = true;
      });
    </script>
    <script>
      $(document).ready(function() {
          $('#submitButton').click(function(event) {
              var fileInput = $('#imgInp');
              if (fileInput.val() === '') {
                  alert('Please Select Your Picture.');
                  event.preventDefault();
                  return false;
              }
          });



          $('#end-time').on('change', function() {
              var startTime = $('#start-time').val();
              if (startTime === '') {
                  alert("Please Enter Start Time");
                  $('#end-time').val('');
              } else {
                  var endTime = $(this).val();
                  var startTimeMinutes = convertTimeToMinutes(startTime);
                  var endTimeMinutes = convertTimeToMinutes(endTime);
                  // Check if the difference is more than 90 minutes
                 if ((endTimeMinutes - startTimeMinutes) > 90 || (endTimeMinutes - startTimeMinutes) < 90) {
                      alert('Auto Task Max Time is Only 90 Minutes');
                      $('#end-time').val('');
                  }
              }
          });

          function convertTimeToMinutes(time) {
                          var timeParts = time.split(':');
                          var hours = parseInt(timeParts[0], 10);
                          var minutes = parseInt(timeParts[1], 10);
                          return (hours * 60) + minutes;
                      }

                      $('#end-time').on('change', function() {
        let endTime = $(this).val();

        // if (endTime) {
        //     // Convert endTime to a Date object
        //     let endDateTime = new Date('1970-01-01T' + endTime + ':00');

        //     // Increment by 1 minute for start_tttpft
        //     // let startDateTime = new Date(endDateTime.getTime() + 1 * 60000);
        //     let startDateTime = new Date(endDateTime.getTime() + 0 * 60000);
        //     let startHours = ('0' + startDateTime.getHours()).slice(-2);
        //     let startMinutes = ('0' + startDateTime.getMinutes()).slice(-2);
        //     $('#start_tttpft').val(startHours + ':' + startMinutes);

        //     // Increment by 1 hour for end_tttpft
        //     let endTttPftDateTime = new Date(endDateTime.getTime() + 1 * 3600000);
        //     let endTttPftHours = ('0' + endTttPftDateTime.getHours()).slice(-2);
        //     let endTttPftMinutes = ('0' + endTttPftDateTime.getMinutes()).slice(-2);
        //     $('#end_tttpft').val(endTttPftHours + ':' + endTttPftMinutes);
        // }

        if (endTime) {
              // Convert endTime to a Date object
              let endDateTime = new Date('1970-01-01T' + endTime + ':00');

              // Increment by 0 minutes for start_tttpft (same as endTime)
              let startDateTime = new Date(endDateTime.getTime() + 0 * 60000);
              let startHours = ('0' + startDateTime.getHours()).slice(-2);
              let startMinutes = ('0' + startDateTime.getMinutes()).slice(-2);
              $('#start_tttpft').val(startHours + ':' + startMinutes);

              // Set end_tttpft to a fixed time of 19:00
              let endTttPftDateTime = new Date('1970-01-01T19:00:00');
              let endTttPftHours = ('0' + endTttPftDateTime.getHours()).slice(-2);
              let endTttPftMinutes = ('0' + endTttPftDateTime.getMinutes()).slice(-2);
              $('#end_tttpft').val(endTttPftHours + ':' + endTttPftMinutes);
        }


    });

    $('#wffo').on('change', function() {
              var wffo = $('#wffo').val();
              $.ajax({
                url:'<?=base_url();?>Menu/CheckuserDayAccardingPlanner',
                type: "POST",
                data: {
                  wffo: wffo,
                },
                cache: false,
                success: function a(result){
                  if(result !==''){
                    var recnt = <?= sizeof($geturdata); ?>;
                    var recntapr = <?= empty($geturdata[0]->apr_status) ? 0 : $geturdata[0]->apr_status; ?>;
                    if(recnt == 0){
                    var selectedText = $('#wffo option:selected').text();
                    $("#user_want_start").val(wffo);
                    var result ='You planed to start their day : <b>'+ result+'</b>';
                    var selectedText = ' But You want to start : <b>'+selectedText+'</b>';
                    var message = result+selectedText;
                    $("#changemessage").html(message);
                    $('#submitButton').prop('disabled', true);
                    $('#exampleModalReminder').modal('show');
                    }else{
                      if(recntapr == 0 || recntapr ==''){
                        $('#submitButton').prop('disabled', true);
                        $('#goodmessage').text("* Please Wait !, While Your Request was Approved.").css('color', 'red');
                      }else if(recntapr == 1){
                        $('#goodmessage').text("* You are able to change your day because your manager has approved your day change request.").css('color', 'green');
                        $('#submitButton').prop('disabled', false);
                      }else if(recntapr == 2){
                        $('#submitButton').prop('disabled', true);
                        $('#goodmessage').text("* You are not able to change your day because your manager has reject your day change request.").css('color', 'red');
                      }
                    }
                  }else{
                 
                    // $('#goodmessage').text("* Good Plan As Your Days According to Planner.").css('color', 'green');
                    $('#submitButton').prop('disabled', false);
                  }
                }
                });
          });
      });
    </script>
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
    <!-- AdminLTE App -->
    <script src="<?=base_url();?>assets/js/adminlte.js"></script>
    <!-- jquery-validation -->
    <script src="<?=base_url();?>assets/js/jquery.validate.min.js"></script>
    <script src="<?=base_url();?>assets/js/additional-methods.min.js"></script>
    <!-- AdminLTE dashboard demo (This is only for demo purposes) -->
    <script src="<?=base_url();?>assets/js/dashboard.js"></script>
    <script>
      $(function() {
        $.validator.setDefaults({
          submitHandler: function () {
            alert( "Form successful submitted!" );
          }
        });
      });
    </script>
  </body>
</html>

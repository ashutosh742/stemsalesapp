<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
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
     .scrollme {overflow-x: auto;}.card.bg-graywe {background: #175456;height: 100px;align-items: center;justify-content: center;display: flex;}.card.bg-graywe {transition: background 0.3s ease-in-out;}.card.bg-graywe:hover {background: #172556;}.card-body {min-height:650px;background: #FFFF;}.card a {color: white;}.uimage {background: #47758b;margin: 4px;padding: 4px;box-shadow: rgba(0, 0, 0, 0.25) 0px 0.0625em 0.0625em, rgba(0, 0, 0, 0.25) 0px 0.125em 0.5em, rgba(255, 255, 255, 0.1) 0px 0px 0px 1px inset;}.dot {height: 18px;width: 18px;background-color: blue;border-radius: 50%;display: inline-block;position: relative;border: 3px solid #fff;top: -48px;left: 186px;z-index: 1000;}.name{margin-top: -21px;font-size: 18px;}.fw-500{font-weight: 500 !important;}.start{color: green;}.stop{color: red;}.rate{border-bottom-right-radius: 12px;border-bottom-left-radius: 12px;}.rating {display: flex;flex-direction: row-reverse;justify-content: left }.rating>input {display: none }.rating>label {position: relative;width: 1em;font-size: 30px;font-weight: 300;color: #f39c12;cursor: pointer }.rating>label::before {content: "\2605";position: absolute;opacity: 0 }.rating>label:hover:before, .rating>label:hover~label:before {opacity: 1 !important }.rating>input:checked~label:before {opacity: 1 }.rating:hover>input:checked~label:before {opacity: 0.4 }.buttons{top: 36px;position: relative;}.rating-submit{border-radius: 15px;color: #fff;height: 49px;}.rating-submit:hover{color: #fff;}div#exampleModalCenter {background: rgba(0, 0, 0, 0.9);}.modal-content {background: azure;}.modal-content {border: none;}.modal-open .modal { background: rgba(0, 0, 0, .2)!important;}
    #tabs .nav-tabs .nav-item.show .nav-link, .nav-tabs .nav-link.active {
        /* color: #0062cc; */
        background-color: #ffc107;
        border-color: transparent transparent #f3f3f3;
        border-bottom: 3px solid !important;
        font-size: 16px;
        font-weight: bolder;
    }
    .nav-tabs .nav-link{
        background-color: white;
        color: black;
        font-weight: 600;
    }
    .question{
        color: black;
        font-weight: 600;
    }
    .star-rating {
            color: #f39c12;
            font-size:20px;
    }
    .success-message {
            /* color: green; */
            display: none;
            /* margin-top: 10px; */
    }
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
        //for testing
        // $previousDate = '2024-07-19';
        // $cdate = '2024-07-20';
         ?>
        <section class="content">
          <div class="container-fluid">
          <div class="alert alert-success" id="success-message" style="display: none;">Thank you for your rating!</div>
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
                                    <table id="YesterdayTaskTable" class="table table-striped table-bordered" cellspacing="0" width="100%">
                                        <thead>
                                            <tr>
                                                <!-- <th>#</th> -->
                                                <th>Name</th>
                                                <th>Today Day Start Rating</th>
                                                <th>Yesterday Evening Rating</th>
                                                <th>Yesterday Task Rating</th>

                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                                // var_dump($userList);die;
                                                $i = 1;
                                                foreach ($userList as $data){
                                                    $teamuid = $data->user_id;
                                                ?>
                                            <tr>
                                                <td><?= $data->name; ?></td>
                                                <td><button type="button" class="btn btn-primary" onclick="OpenTodayModal(<?=$teamuid?>,'<?php echo date('Y-m-d')?>')">View Today Day Start Details</button></td>

                                                <td><button type="button" class="btn btn-warning" onclick="OpenYesterdayModal(<?=$teamuid?>,'<?php echo date('Y-m-d')?>')">Yesterday Evening Details</button></td>
                                                
                                                <td><button type="button" class="btn btn-secondary" onclick="OpenYesterTaskModal(<?=$teamuid?>)">View Yesterday Task Details</button></td>
                                            <?php } ?>
                                        </tbody>
                                    </table>
                                <br>

                                </div>
                            </div>
                            <!-- Button trigger modal -->
                            
                            <!-- Modal -->
                            <div class="modal fade bd-example-modal-lg" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true" id="ReviewModal" data-backdrop="static">
                                <div class="modal-dialog modal-lg" role="document" >
                                    <form method="POST" action="submitDayCheck" onsubmit="return validateForm()">
                                        
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                Name : <span id="userName"></span>
                                            </div>
                                            <div class="modal-body">
                                            
                                                <input type="hidden" name="userID" id="userID" readonly>
                                                <div class="row">
                                                    <div class="col-md-4">
                                                        <b>Planned Day Start : </b>
                                                        <input type="text" id="planned_day_start" name="planned_day_start" class="form-control" readonly>
                                                            <!-- <span id="planned_day_start"></span> -->
                                                            <hr>
                                                        <b>Actual Day Start :</b>
                                                        <input type="text" id="actual_day_start" name="actual_day_start" class="form-control" readonly>
                                                        <!-- <span id="actual_day_start"></span>    -->
                                                        <br>
                                                        <p><b> Did the day started as planned..??</b></p>
                                                        <!-- <fieldset class="MoMrating" data-question="Did the day started as planned" data-userid="" data-taskid=""> -->
                                                            <div class="rating">
                                                                <input type="radio" name="Did the day started as planned" value="5" id="05"><label for="05" required>☆</label>
                                                                <input type="radio" name="Did the day started as planned" value="4" id="04"><label for="04" required>☆</label>
                                                                <input type="radio" name="Did the day started as planned" value="3" id="03"><label for="03" required>☆</label>
                                                                <input type="radio" name="Did the day started as planned" value="2" id="02"><label for="02" required>☆</label>
                                                                <input type="radio" name="Did the day started as planned" value="1" id="01"><label for="01" required>☆</label>
                                                            </div>
                                                            <div class="remark-box">
                                                                <label for="remark_01">Please provide additional remarks:</label>
                                                                <textarea id="remark_01"></textarea>
                                                            </div>
                                                        <!-- </fieldset> -->
                                                    </div>
                                                    <div class="col-md-4">
                                                        <b>Day Start Time : </b>
                                                        <!-- <span id="day_start_time"></span>  -->
                                                        <input type="text" id="day_start_time" name="day_start_time" class="form-control" readonly>
                                                        <br>
                                                        <p><b> Did the day started on right time..??</b></p>
                                                        <!-- <fieldset class="MoMrating" data-question="Did the day started on right time" data-userid="" data-taskid=""> -->
                                                            <div class="rating">
                                                                <input type="radio" name="Did the day started on right time" value="5" id="10"><label for="10" required>☆</label>
                                                                <input type="radio" name="Did the day started on right time" value="4" id="09"><label for="09" required>☆</label>
                                                                <input type="radio" name="Did the day started on right time" value="3" id="08"><label for="08" required>☆</label>
                                                                <input type="radio" name="Did the day started on right time" value="2" id="07"><label for="07" required>☆</label>
                                                                <input type="radio" name="Did the day started on right time" value="1" id="06"><label for="06" required>☆</label>
                                                            </div>
                                                        <!-- </fieldset> -->
                                                    </div>
                                                    <div class="col-md-4">
                                                        <b>Task Start Time : </b>
                                                        <!-- <span id="task_start_time"></span>  -->
                                                        <input type="text" id="task_start_time" class="form-control" readonly>
                                                        <br>
                                                        <p><b> Did the task started on right time..??</b></p>
                                                        <!-- <fieldset class="MoMrating" data-question="Did the task started on right time" data-userid="" data-taskid=""> -->
                                                            <div class="rating">
                                                                <input type="radio" name="Did the task started on right time" value="5" id="15" required><label for="15">☆</label>
                                                                <input type="radio" name="Did the task started on right time" value="4" id="14" required><label for="14">☆</label>
                                                                <input type="radio" name="Did the task started on right time" value="3" id="13" required><label for="13">☆</label>
                                                                <input type="radio" name="Did the task started on right time" value="2" id="12" required><label for="12">☆</label>
                                                                <input type="radio" name="Did the task started on right time" value="1" id="11" required><label for="11">☆</label>
                                                            </div>
                                                        <!-- </fieldset> -->
                                                    </div>
                                                    
                                                </div>
                                                <hr>
                                                <div class="row">
                                                    <div class="col-md-4">
                                                        <b>Day Start Image : </b>
                                                        <img id="startImage" src="" alt="Photo" class="img-fluid">
                                                        <br>
                                                        <p><b> Is Day start image good..??</b></p>
                                                        <!-- <fieldset class="MoMrating" data-question="Did the task started on right time" data-userid="" data-taskid=""> -->
                                                            <div class="rating">
                                                                <input type="radio" name="Is Day start image good" value="5" id="20" required><label for="20">☆</label>
                                                                <input type="radio" name="Is Day start image good" value="4" id="19" required><label for="19">☆</label>
                                                                <input type="radio" name="Is Day start image good" value="3" id="18" required><label for="18">☆</label>
                                                                <input type="radio" name="Is Day start image good" value="2" id="17" required><label for="17">☆</label>
                                                                <input type="radio" name="Is Day start image good" value="1" id="16" required><label for="16">☆</label>
                                                            </div>
                                                        <!-- </fieldset> -->
                                                    </div>
                                                    <div class="col-md-4">
                                                        <strong>Day Start Location:</strong> 
                                                        <div class="img-thumbnail" >
                                                            <iframe id="startLocation"  width="100%" frameborder="0" style="border:0" allowfullscreen></iframe>
                                                        </div> 
                                                        <br>
                                                        <p><b> Day Start Location as per Plan..??</b></p>
                                                        <!-- <fieldset class="MoMrating" data-question="Did the task started on right time" data-userid="" data-taskid=""> -->
                                                            <div class="rating">
                                                                <input type="radio" name="Day Start Location as per Plan" value="5" id="25" required><label for="25">☆</label>
                                                                <input type="radio" name="Day Start Location as per Plan" value="4" id="24" required><label for="24">☆</label>
                                                                <input type="radio" name="Day Start Location as per Plan" value="3" id="23" required><label for="23">☆</label>
                                                                <input type="radio" name="Day Start Location as per Plan" value="2" id="22" required><label for="22">☆</label>
                                                                <input type="radio" name="Day Start Location as per Plan" value="1" id="21" required><label for="21">☆</label>
                                                            </div>
                                                        <!-- </fieldset> -->
                                                    </div>
                                                    <div class="col-md-4">
                                                        <!-- <strong>Auto Task Time:</strong>  -->
                                                        <b>Auto Task Start Time : </b> 
                                                        <!-- <span id="autoTaskStartTime"></span> -->
                                                        <input type="text" id="autoTaskStartTime" name="autoTaskStartTime" class="form-control" readonly>
                                                        <b>Auto Task End Time : </b>
                                                         <!-- <span id="autoTaskEndTime"></span> -->
                                                        <input type="text" id="autoTaskEndTime" name="autoTaskEndTime" class="form-control" readonly>

                                                        <b>Time Difference : </b><span id="difference"></span> 
                                                        <br>
                                                        <p><b> Auto task time entered correctly..??</b></p>
                                                        <!-- <fieldset class="MoMrating" data-question="Did the task started on right time" data-userid="" data-taskid=""> -->
                                                            <div class="rating">
                                                                <input type="radio" name="Auto task time entered correctly" value="5" id="30" required><label for="30">☆</label>
                                                                <input type="radio" name="Auto task time entered correctly" value="4" id="29" required><label for="29">☆</label>
                                                                <input type="radio" name="Auto task time entered correctly" value="3" id="28" required><label for="28">☆</label>
                                                                <input type="radio" name="Auto task time entered correctly" value="2" id="27" required><label for="27">☆</label>
                                                                <input type="radio" name="Auto task time entered correctly" value="1" id="26" required><label for="26">☆</label>
                                                            </div>
                                                        <!-- </fieldset> -->
                                                    </div>
                                                    
                                                </div>
                                                <hr>
                                                <div class="row">
                                                    <div class="col-md-4">
                                                        <div id="plannerApproval">

                                                        </div>
                                                        <div id="plannerApprovalDone">

                                                        </div>
                                                        <b>Requested At : </b>
                                                        <!-- <span id="planner_created_at"></span>  -->
                                                        <input type="text" class="form-control" id="planner_created_at" name="planner_created_at" readonly> <hr>
                                                        <b>Request Reamarks : </b>
                                                        <!-- <span id="planner_request_remarks"></span> -->
                                                        <input type="text" class="form-control" id="planner_request_remarks" name="planner_request_remarks" readonly> <hr>
                                                        <b>Approval Status : </b>
                                                        <!-- <span id="planner_approvel_status"></span> -->
                                                        <input type="text" class="form-control" id="planner_approvel_status" name="planner_approvel_status" readonly> <hr>
                                                        <b>Planner Approved at : </b>
                                                        <!-- <span id="planner_approvel_time"></span>  -->
                                                        <input type="text" class="form-control" id="planner_approvel_time" name="planner_approvel_time" readonly> <hr>
                                                        <b>Planner Approved by : </b>
                                                        <!-- <span id="approver_Name"></span>  -->
                                                        <input type="text" class="form-control" id="approver_Name" name="approver_Name" readonly> <hr>
                                                        <br>
                                                        <p><b> Planner requested correctly..??</b></p>
                                                        <!-- <fieldset class="MoMrating" data-question="Did the task started on right time" data-userid="" data-taskid=""> -->
                                                            <div class="rating">
                                                                <input type="radio" name="Planner requested correctly" value="5" id="35" required><label for="35">☆</label>
                                                                <input type="radio" name="Planner requested correctly" value="4" id="34" required><label for="34">☆</label>
                                                                <input type="radio" name="Planner requested correctly" value="3" id="33" required><label for="33">☆</label>
                                                                <input type="radio" name="Planner requested correctly" value="2" id="32" required><label for="32">☆</label>
                                                                <input type="radio" name="Planner requested correctly" value="1" id="31" required><label for="31">☆</label>
                                                            </div>
                                                        <!-- </fieldset> -->
                                                    </div>
                                                </div>
                                            
                                            </div>
                                            <div class="modal-footer">
                                                <!-- <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button> -->
                                                <button type="submit" class="btn btn-primary">Submit</button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>

                            <div class="modal fade bd-example-modal-lg" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true" id="YesterdayReviewModal" data-backdrop="static">
                                <div class="modal-dialog modal-lg" role="document" >
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            Name : <input type="text" id="userNameyesterday" class="form-control" readonly>
                                        </div>
                                        <div class="modal-body">
                                        <form method="POST" action="submitDayCheck" onsubmit="return validateForm()">
                                            <input type="hidden" name="userID" id="yesteruserID" readonly>
                                            <div class="row">
                                                <div class="col-md-4">
                                                    <b>Start Time : </b>
                                                        <input type="text" id="start_time" class="form-control" readonly>
                                                        <!-- <span id="start_time"></span> -->
                                                        <hr>
                                                    <b>End Time :</b>
                                                        <!-- <span id="end_time"></span>    -->
                                                        <input type="text" id="end_time" name="end_time" class="form-control" readonly>
                                                    <hr>
                                                    <!-- <b>End Time :</b> -->
                                                        <span id="difference1"></span>   
                                                    <br>
                                                    <p><b> The Day Ended at a good time..??</b></p>
                                                    <!-- <fieldset class="MoMrating" data-question="Did the day started as planned" data-userid="" data-taskid=""> -->
                                                        <div class="rating">
                                                            <input type="radio" name="The Day Ended at a good time" value="5" id="40" required><label for="40">☆</label>
                                                            <input type="radio" name="The Day Ended at a good time" value="4" id="39" required><label for="39">☆</label>
                                                            <input type="radio" name="The Day Ended at a good time" value="3" id="38" required><label for="38">☆</label>
                                                            <input type="radio" name="The Day Ended at a good time" value="2" id="37" required><label for="37">☆</label>
                                                            <input type="radio" name="The Day Ended at a good time" value="1" id="36" required><label for="36">☆</label>
                                                        </div>
                                                        <div class="remark-box">
                                                            <label for="remark_08">Please provide additional remarks:</label>
                                                            <textarea id="remark_08"></textarea>
                                                        </div>
                                                    <!-- </fieldset> -->
                                                </div>
                                                <div class="col-md-4">
                                                    <b>Yesterday day close image : </b>
                                                    <img id="user_close_image" src="" alt="Photo" class="img-fluid">
                                                    <br>
                                                    <p><b> Did the yesterday day close image was right..??</b></p>
                                                    <!-- <fieldset class="MoMrating" data-question="Did the day started on right time" data-userid="" data-taskid=""> -->
                                                        <div class="rating">
                                                            <input type="radio" name="Did the yesterday day close image was right" value="5" id="45" required><label for="45">☆</label>
                                                            <input type="radio" name="Did the yesterday day close image was right" value="4" id="44" required><label for="44">☆</label>
                                                            <input type="radio" name="Did the yesterday day close image was right" value="3" id="43" required><label for="43">☆</label>
                                                            <input type="radio" name="Did the yesterday day close image was right" value="2" id="42" required><label for="42">☆</label>
                                                            <input type="radio" name="Did the yesterday day close image was right" value="1" id="41" required><label for="41">☆</label>
                                                        </div>
                                                    <!-- </fieldset> -->
                                                </div>
                                                <div class="col-md-4">
                                                    <b>Autotask Start Time : </b> 
                                                    <input type="text" id="yesterday_autotaskstartTime" name="yesterday_autotaskstartTime" class="form-control" readonly>
                                                    <!-- <span id="yesterday_autotaskstartTime"></span>  -->
                                                    <hr>
                                                    <b>Autotask End Time : </b> 
                                                    <input type="text" id="yesterday_autotaskendTime" name="yesterday_autotaskendTime" class="form-control" readonly>
                                                    <!-- <span id="yesterday_autotaskendTime"></span> -->
                                                    <hr>
                                                    <b>Autotask Time Difference: : </b> 
                                                    <!-- <input type="text" id="yesterday_autotaskendTime" name="yesterday_autotaskendTime" class="form-control" readonly> -->
                                                    <span id="yesterday_autotaskTimediff"></span> 
                                                    <br>
                                                    <p><b> Autotask added on correct time..??</b></p>
                                                    <!-- <fieldset class="MoMrating" data-question="Did the task started on right time" data-userid="" data-taskid=""> -->
                                                        <div class="rating">
                                                            <input type="radio" name="Autotask added on correct time" value="5" id="50" required><label for="50">☆</label>
                                                            <input type="radio" name="Autotask added on correct time" value="4" id="49" required><label for="49">☆</label>
                                                            <input type="radio" name="Autotask added on correct time" value="3" id="48" required><label for="48">☆</label>
                                                            <input type="radio" name="Autotask added on correct time" value="2" id="47" required><label for="47">☆</label>
                                                            <input type="radio" name="Autotask added on correct time" value="1" id="46" required><label for="46">☆</label>
                                                        </div>
                                                    <!-- </fieldset> -->
                                                </div>
                                                
                                            </div>
                                            <hr>
                                            <div class="row">
                                                <div class="col-md-4">
                                                    <b>Total Time Taken for Planner : </b>
                                                    <!-- <span id="total_timeTakeFor_planner"></span>  -->
                                                    <input type="text" class="form-control" id="total_timeTakeFor_planner" name="total_timeTakeFor_planner" readonly>
                                                    <hr>
                                                    <p><b> Time taken to plan the planner..??</b></p>
                                                    <!-- <fieldset class="MoMrating" data-question="Did the task started on right time" data-userid="" data-taskid=""> -->
                                                        <div class="rating">
                                                            <input type="radio" name="Time taken to plan the planner" value="5" id="55" required><label for="55">☆</label>
                                                            <input type="radio" name="Time taken to plan the planner" value="4" id="54" required><label for="54">☆</label>
                                                            <input type="radio" name="Time taken to plan the planner" value="3" id="53" required><label for="53">☆</label>
                                                            <input type="radio" name="Time taken to plan the planner" value="2" id="52" required><label for="52">☆</label>
                                                            <input type="radio" name="Time taken to plan the planner" value="1" id="51" required><label for="51">☆</label>
                                                        </div>
                                                    <!-- </fieldset> -->
                                                </div>
                                                
                                                <div class="col-md-4">
                                                    <!-- <strong>Auto Task Time:</strong>  -->
                                                    <div id="dayClose">
                                                        <b>Day Close Request Remark : </b> 
                                                        <input type="text" class="form-control" id="dayCloseRemark" name="dayCloseRemark" readonly>
                                                        <!-- <span id="dayCloseRemark"></span> -->
                                                        <hr>
                                                        <b>Day Close Approval Status : </b> 
                                                        <input type="text" class="form-control" id="dayCloseApproveStatus" name="dayCloseApproveStatus" readonly>
                                                        <!-- <span id="dayCloseApproveStatus"></span> -->
                                                        <hr>
                                                        <b>Day Close Approval Remark : </b> 
                                                        <input type="text" class="form-control" id="dayCloseApproveRemark" name="dayCloseApproveRemark" readonly>
                                                        <!-- <span id="dayCloseApproveRemark"></span> -->
                                                        <hr>
                                                    </div>
                                                    <div id="dayNotClose">
                                                        <b>Not Closed Yet..!!</b>
                                                    </div>
                                                    
                                                    <!-- <b>Auto Task End Time : </b> <span id="autoTaskEndTime"></span>
                                                    <b>Time Difference : </b><span id="difference"></span>  -->
                                                    <br>
                                                    <p><b> Was day ended on good time..??</b></p>
                                                    <!-- <fieldset class="MoMrating" data-question="Did the task started on right time" data-userid="" data-taskid=""> -->
                                                        <div class="rating">
                                                            <input type="radio" name="Was day ended on good time" value="5" id="60" required ><label for="60">☆</label>
                                                            <input type="radio" name="Was day ended on good time" value="4" id="59" required ><label for="59">☆</label>
                                                            <input type="radio" name="Was day ended on good time" value="3" id="56" required ><label for="58">☆</label>
                                                            <input type="radio" name="Was day ended on good time" value="2" id="57" required ><label for="57">☆</label>
                                                            <input type="radio" name="Was day ended on good time" value="1" id="56" required ><label for="56">☆</label>
                                                        </div>
                                                    <!-- </fieldset> -->
                                                </div>
                                                <div class="col-md-4">
                                                    <strong>Day End Location:</strong> 
                                                    <div class="img-thumbnail" >
                                                        <iframe id="endLocation"  width="100%" frameborder="0" style="border:0" allowfullscreen></iframe>
                                                    </div> 
                                                    <br>
                                                    <p><b> Day Ended Location as per Plan..??</b></p>
                                                    <!-- <fieldset class="MoMrating" data-question="Did the task started on right time" data-userid="" data-taskid=""> -->
                                                        <div class="rating">
                                                            <input type="radio" name="Day Start Location as per Plan" value="5" id="65" required ><label for="65">☆</label>
                                                            <input type="radio" name="Day Start Location as per Plan" value="4" id="64" required ><label for="64">☆</label>
                                                            <input type="radio" name="Day Start Location as per Plan" value="3" id="63" required ><label for="63">☆</label>
                                                            <input type="radio" name="Day Start Location as per Plan" value="2" id="62" required ><label for="62">☆</label>
                                                            <input type="radio" name="Day Start Location as per Plan" value="1" id="61" required ><label for="61">☆</label>
                                                        </div>
                                                    <!-- </fieldset> -->
                                                </div>
                                            </div>
                                            <hr>
                                            <div class="row">
                                                <div class="col-md-4">
                                                    <!-- <b>Was there any special request</b> -->
                                                    <!-- <div id="plannerApproval">
                                                        <b>No Special Request..!!</b>
                                                    </div> -->
                                                    <div id="plannerApprovalDone">
                                                        <b>Leave Start At : </b>
                                                        <span id="specialRequest_created_at"></span> 
                                                        <input type="text" id="specialRequest_created_at" name="specialRequest_created_at" class="form-control" readonly>
                                                        <!-- <hr> -->
                                                        <b>Leave End At : </b>
                                                        <span id="specialRequest_end_at"></span> 
                                                        <input type="text" id="specialRequest_end_at" name="specialRequest_end_at" class="form-control" readonly>
                                                        <!-- <hr> -->
                                                        <b>Request Reamarks : </b>
                                                        <span id="specialRequest_request_remarks"></span>
                                                        <input type="text" id="specialRequest_request_remarks" name="specialRequest_request_remarks" class="form-control" readonly>
                                                        <!-- <hr> -->
                                                        <b>Approval Status : </b>
                                                        <span id="specialRequest_approvel_status"></span>
                                                        <input type="text" id="specialRequest_approvel_status" name="specialRequest_approvel_status" class="form-control" readonly>
                                                        <!-- <hr> -->

                                                        <!-- <b>Planner Approved at : </b><span id="specialRequest_approvel_time"></span> <hr>
                                                        <b>Planner Approved by : </b><span id="specialRequest_Name"></span> <hr> -->
                                                        
                                                    </div>
                                                        <p><b> Planner requested correctly..??</b></p>
                                                    <!-- <br> -->
                                                    <!-- <fieldset class="MoMrating" data-question="Did the task started on right time" data-userid="" data-taskid=""> -->
                                                        <div class="rating">
                                                            <input type="radio" name="Planner requested correctly" value="5" id="70" required ><label for="70">☆</label>
                                                            <input type="radio" name="Planner requested correctly" value="4" id="69" required ><label for="69">☆</label>
                                                            <input type="radio" name="Planner requested correctly" value="3" id="68" required ><label for="68">☆</label>
                                                            <input type="radio" name="Planner requested correctly" value="2" id="67" required ><label for="67">☆</label>
                                                            <input type="radio" name="Planner requested correctly" value="1" id="66" required ><label for="66">☆</label>
                                                        </div>
                                                    <!-- </fieldset> -->
                                                </div>
                                            </div>
                                        
                                        </div>
                                        <div class="modal-footer">
                                            <!-- <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button> -->
                                            <button type="submit" class="btn btn-primary">Submit</button>
                                        </div>
                                    </div>
                                    </form>
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
    <script src="<?=base_url();?>assets/js/adminlte.js"></script>
    <!-- AdminLTE dashboard demo (This is only for demo purposes) -->
    <script src="<?=base_url();?>assets/js/dashboard.js"></script>
    <script>
        function OpenTodayModal(userID,date){

            $.ajax({
                url: "CheckingDayManagement_New1",
                method: "POST",
                data: { user_id: userID, date: date,period:'today'},
                success: function (response) {

                    var data = JSON.parse(response); 
                    // alert(data[0].name);return false;
                    // console.log('Response:',response);
                    // console.log('Data:',data);
                    
// Response: [{"user_id":"100059","name":"Abinash Tarai","type_id":"3","user_start_time":"2024-10-08 10:32:12","user_start_image":"uploads\/day\/Screenshot 2024-02-13 101903.png","user_start_comment":null,"user_start_lat":"19.0248739","user_start_long":"72.8375045","user_close_time":"2024-10-08 19:53:35","user_close_image":"uploads\/day\/Screenshot 2024-02-13 101903.png","user_end_comment":null,"user_end_lat":null,"user_end_long":null,"date":"2024-10-08","planner_request_remarks":"test request","planner_created_at":"2024-10-08 14:42:51","planner_request_approval_date":"2024-10-08 11:42:51","planner_approvel_status":"Approved","planner_approvel_time":"2024-10-08 11:42:51","user_planner_start_time":"2024-10-08 14:53:53","approver_Name":"Sayantan Maitra","close_day_request_Remark":"I had a personal emergency that required my immediate attention.","close_day_req_remarks":"please test","close_day_approved_status":"Approved","close_day_approved_remarks":"","task_start_time":"2024-10-08 11:00:00","total_timeTakeFor_planner":"77:42:21","userWorkFrom":"Work From Field","userWorkFromActual":"Work From Office","reasonFor_Request":null,"leave_StartTime":null,"leave_EndTime":null,"autoTask_startTime":"17:00:00","autoTask_endTime":"18:00:00"}]

                    document.getElementById('planned_day_start').value = data[0].userWorkFrom;
                    document.getElementById('actual_day_start').value = data[0].userWorkFromActual;
                    document.getElementById('userName').value = data[0].name;
                    document.getElementById('userID').value = data[0].user_id;
                    // document.getElementById('planned_day_start').textContent = data[0].userWorkFrom;
                    // document.getElementById('actual_day_start').textContent = data[0].userWorkFromActual;
                    document.getElementById('day_start_time').value = data[0].user_start_time;
                    document.getElementById('task_start_time').value = data[0].task_start_time;
                    document.getElementById('startImage').src = data[0].user_start_image; 
                    document.getElementById('planner_created_at').value = data[0].planner_created_at; 
                    document.getElementById('planner_request_remarks').value = data[0].planner_request_remarks; 
                    document.getElementById('planner_approvel_status').value = data[0].planner_approvel_status; 
                    document.getElementById('planner_approvel_time').value = data[0].planner_approvel_time; 
                    document.getElementById('approver_Name').value = data[0].approver_Name; 

                    var startLat = data[0].user_start_lat;
                    var startLong = data[0].user_start_long;
                    var endLat = data[0].user_end_lat;
                    var endLong = data[0].user_end_long;

                    let startTime = new Date(`1970-01-01T${data[0].autoTask_startTime}Z`);
                    let endTime = new Date(`1970-01-01T${data[0].autoTask_endTime}Z`);

                    // Calculate the time difference in milliseconds
                    let timeDifference = endTime - startTime;

                    // Convert milliseconds to minutes
                    let minutes = timeDifference / (1000 * 60)

                    document.getElementById('startLocation').src = 'https://maps.google.com/?q=${startLat},${startLong}&t=k&z=13&ie=UTF8&iwloc=&output=embed';
                    
                    document.getElementById('autoTaskStartTime').value = data[0].autoTask_startTime;
                    document.getElementById('autoTaskEndTime').value = data[0].autoTask_endTime;
                    document.getElementById('difference').value = data[0].minutes;
                    // Update the end location map and link

                    // document.getElementById('endMap').src = 'https://maps.google.com/?q=${endLat},${endLong}&t=k&z=13&ie=UTF8&iwloc=&output=embed';
                    // planned_day_start
                    $('#ReviewModal').modal('show'); // Show modal after both calls
                },
                error: function () {
                    alert("Error fetching rating data.");
                }
            });
        }
    </script>  

<script>
        function OpenYesterdayModal(userID,date){

            $.ajax({
                url: "CheckingDayManagement_New1",
                method: "POST",
                data: { user_id: userID, date: date,period:'yesterday'},
                success: function (response) {

                    var data = JSON.parse(response); 
                    // alert(data[0].autoTask_endTime); return false;
                    console.log('Response:',response);
                    // console.log('Data:',data);
                    // alert(data[0].name);return false;
// Response: [{"user_id":"100059","name":"Abinash Tarai","type_id":"3","user_start_time":"2024-10-07 15:45:04","user_close_time":"2024-10-07 10:31:45","user_close_image":"uploads\/day\/Screenshot 2024-02-13 101903.png","user_end_comment":null,"user_end_lat":"19.0248739","user_end_long":"72.8375045","date":null,"planner_request_remarks":null,"planner_created_at":null,"planner_request_approval_date":null,"planner_approvel_status":null,"planner_approvel_time":null,"user_planner_start_time":"2024-10-07 18:03:25","approver_Name":null,"close_day_request_Remark":"I had a personal emergency that required my immediate attention.","close_day_req_remarks":"please test","close_day_approved_status":"Approved","close_day_approved_remarks":"","task_start_time":null,"total_timeTakeFor_planner":"07:40:10","userWorkFrom":null,"userWorkFromActual":"Work From Office","reasonFor_Request":null,"leave_StartTime":null,"leave_EndTime":null}]
                    document.getElementById('yesteruserID').value = data[0].user_id;
                    document.getElementById('userNameyesterday').value = data[0].name;
                    document.getElementById('planned_day_start').value = data[0].userWorkFrom;
                    document.getElementById('actual_day_start').value = data[0].userWorkFromActual;
                    document.getElementById('start_time').value = data[0].user_start_time;
                    document.getElementById('end_time').value = data[0].user_close_time;
                    
                    document.getElementById('user_close_image').src = data[0].user_close_image; 
                    document.getElementById('planner_created_at').value = data[0].planner_created_at; 
                    document.getElementById('dayCloseRemark').value = data[0].close_day_request_Remark; 
                    document.getElementById('dayCloseApproveStatus').value = data[0].close_day_approved_status; 
                    document.getElementById('dayCloseApproveRemark').value = data[0].close_day_approved_remarks;

                    document.getElementById('total_timeTakeFor_planner').value = data[0].total_timeTakeFor_planner; 

                    // var startLat = data[0].user_start_lat;
                    // var startLong = data[0].user_start_long;
                    var endLat = data[0].user_end_lat;
                    var endLong = data[0].user_end_long;

                    let startTime = new Date(`1970-01-01T${data[0].autoTask_startTime}Z`);
                    let endTime = new Date(`1970-01-01T${data[0].autoTask_endTime}Z`);

                    // Calculate the time difference in milliseconds
                    let timeDifference = endTime - startTime;

                    // Convert milliseconds to minutes
                    let minutes = timeDifference / (1000 * 60);

                    document.getElementById('yesterday_autotaskTimediff').textContent = minutes;
                    document.getElementById('yesterday_autotaskstartTime').value = data[0].autoTask_startTime;
                    document.getElementById('yesterday_autotaskendTime').value = data[0].autoTask_endTime;
                    
//////////////////////////////////////////////////////////////////////////////////

                    let daystartTime = new Date(`1970-01-01T${data[0].user_start_time}Z`);
                    let dayendTime = new Date(`1970-01-01T${data[0].user_close_time}Z`);

                    // Calculate the time difference in milliseconds
                    let daytimeDifference = endTime - startTime;

                    // Convert milliseconds to minutes
                    let dayminutes = timeDifference / (1000 * 60)

                    document.getElementById('difference1').textContent = data[0].dayminutes;

                    document.getElementById('endLocation').src = 'https://maps.google.com/?q=${endLat},${endLong}&t=k&z=13&ie=UTF8&iwloc=&output=embed';
                    
                    document.getElementById('autoTaskStartTime').value = data[0].autoTask_startTime;
                    document.getElementById('autoTaskEndTime').value = data[0].autoTask_endTime;
                    // document.getElementById('difference').value = data[0].minutes;
                    // Update the end location map and link

                    if(data[0].close_day_request_Remark == ''){

                        $('#dayClose').hide();
                        $('#dayNotClose').show();
                    }else{
                        $('#dayNotClose').hide();
                        $('#dayClose').show();
                    }
                    // alert(data[0].leave_StartTime);
                    // return false;

                    // <div id="plannerApproval">
                    //                                     <b>No Special Request..!!</b>
                    //                                 </div>

                    if(data[0].leave_StartTime == null){
                        // alert(data[0].leave_StartTime);
                        // return false;
                        $('#plannerApprovalDone').hide();
                        $('#plannerApproval').show();
                    }else{
                        $('#plannerApproval').hide();
                        $('#plannerApprovalDone').show();
                        document.getElementById('specialRequest_created_at').value = data[0].leave_StartTime; 
                        document.getElementById('specialRequest_end_at').value = data[0].leave_EndTime; 
                        document.getElementById('specialRequest_request_remarks').value = data[0].reasonFor_Request; 
                    }

                    $('#YesterdayReviewModal').modal('show'); // Show modal after both calls
                },
                error: function () {
                    alert("Error fetching rating data.");
                }
            });
        }
    </script>  
    <script>
    function validateForm() {
        // Validate required radio buttons
        let radios = document.querySelectorAll('input[type="radio"]:checked');
        if (radios.length === 0) {
            alert("Please answer all required questions.");
            return false;
        }
        return true;
    }
</script>
  </body>
</html>
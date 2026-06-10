<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>TaskPlanner2.0 | STEM APP | WebAPP</title>
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
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap/dist/css/bootstrap.min.css">
    <script src="<?=base_url();?>assets/js/web.js" referrerpolicy="no-referrer"></script>
    <script>
      function validateTimeInput(event) {
          const input = event.target;
          const timeValue = input.value;
          const minTime = "10:00";
          const maxTime = "19:00";
      
          if (timeValue < minTime || timeValue > maxTime) {
              alert("Please enter a time between 10:00 AM and 7:00 PM.");
              input.value = "";
          }
      }
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
          const timeInput = document.getElementById('start-time1');
          timeInput.setAttribute('min', '10:00');
          timeInput.setAttribute('max', '19:00');
          timeInput.addEventListener('change', validateTimeInput);
      });
      document.addEventListener('DOMContentLoaded', function() {
          const timeInput = document.getElementById('end-time1');
          timeInput.setAttribute('min', '10:00');
          timeInput.setAttribute('max', '19:00');
          timeInput.addEventListener('change', validateTimeInput);
      });
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
      document.addEventListener('DOMContentLoaded', function() {
          const timeInput = document.getElementById('meetingtimerequest1');
          timeInput.setAttribute('min', '10:00');
          timeInput.setAttribute('max', '19:00');
          timeInput.addEventListener('change', validateTimeInput);
      });
      document.addEventListener('DOMContentLoaded', function() {
          const timeInput = document.getElementById('meetingtimerequest2');
          timeInput.setAttribute('min', '10:00');
          timeInput.setAttribute('max', '19:00');
          timeInput.addEventListener('change', validateTimeInput);
      });
      document.addEventListener('DOMContentLoaded', function() {
          const timeInput = document.getElementById('meetingtimerequest3');
          timeInput.setAttribute('min', '10:00');
          timeInput.setAttribute('max', '19:00');
          timeInput.addEventListener('change', validateTimeInput);
      });
      document.addEventListener('DOMContentLoaded', function() {
          const timeInput = document.getElementById('review_plantime');
          timeInput.setAttribute('min', '10:00');
          timeInput.setAttribute('max', '19:00');
          timeInput.addEventListener('change', validateTimeInput);
      });
      
    </script>
    <?php
      if(sizeof($getplandt) == 1){
          $stime = explode(":", $getplandt[0]->stime);
          $endtime = explode(":", $getplandt[0]->etime);
      
          $starttime = $stime[0].':'.$stime[1];
          $endtime = $endtime[0].':'.$endtime[1];
      
      ?>
    <script>
      function validateTimeInputMeet(event) {
           const input = event.target;
           const timeValue = input.value;
           const minTime = "10:00";
           const maxTime = "19:00";
           const restrictedStartTime = "<?=$starttime?>";
           const restrictedEndTime = "<?=$endtime ?>";
           
           if ((timeValue >= restrictedStartTime && timeValue <= restrictedEndTime) || timeValue < minTime || timeValue > maxTime) {
               alert("Try to Diffrent between 10:00 AM to 7:00 PM (<?=$starttime?> to <?=$endtime ?> time is booked for auto task)");
               input.value = "";
           }else{
            $.ajax({
                url:'<?=base_url();?>Menu/GetCheckExistsTaskTime',
                type: "POST",
                data: {
                timeValue: timeValue,
                pdate:"<?=$adate?>"
                },
                cache: false,
                success: function a(result){
                  if(result > 0){
                    alert("You have already plan task for this time, please enter another time");
                    input.value = "";
                  }else{
                    var slsctadate = document.getElementById('rev_plandate').value;
                    $.ajax({
                    url:'<?=base_url();?>Menu/CheckReviewTimeonPlanner',
                    type: "POST",
                    data: {
                    timeValue: timeValue,
                    pdate:slsctadate
                    },
                    cache: false,
                    success: function a(result){
                      if(result !== ''){
                          input.value = "";
                        let currentLocation = window.location.toString();
                        var jsonArray = JSON.parse(result);
                        var dataValue = jsonArray.data;
                        var redirect  = jsonArray.redirect;
                        // var revpage   = jsonArray.revpage.toString();
                        var rev_uid   = jsonArray.rev_uid;
                        var rev_bdid  = jsonArray.rev_bdid;
                        var by_type_id = jsonArray.by_type_id;
                        var to_type_id = jsonArray.to_type_id;
                        var cur_type_id = jsonArray.cur_type_id;
                        let timerInterval;
                        var ckrevcount = jsonArray.length;
                  
                        Swal.fire({
                        title: "<u>Review Planned By</u>",
                        html: dataValue,
                        timer: 5000,
                        timerProgressBar: true,
                        didOpen: () => {
                            Swal.showLoading();
                            const timer = Swal.getPopup().querySelector("b");
                            timerInterval = setInterval(() => {
                            timer.textContent = `${Swal.getTimerLeft()}`;
                            }, 100);
                        },
                        willClose: () => {
                            clearInterval(timerInterval);
                        }
                        }).then((result) => {
                          if (result.dismiss === Swal.DismissReason.timer) {
                            input.value = "";
                          }
                        });
                        }
                      }
                    });
                  }
                    }
                });
           }
       }
       document.addEventListener('DOMContentLoaded', function() {
           const timeInput = document.getElementById('meeting-time');
           timeInput.setAttribute('min', '10:00');
           timeInput.setAttribute('max', '19:00');
           timeInput.addEventListener('change', validateTimeInputMeet);
       });
       function validateTimeInputMeetReview(event) {
           const input = event.target;
           const timeValue = input.value;
           const minTime = "10:00";
           const maxTime = "19:00";
           const restrictedStartTime = "<?=$starttime?>";
           const restrictedEndTime = "<?=$endtime ?>";
           var slsctadate = document.getElementById('rev_plandate').value;
           
           if ((timeValue >= restrictedStartTime && timeValue <= restrictedEndTime) || timeValue < minTime || timeValue > maxTime) {
               alert("Try to Diffrent between 10:00 AM to 7:00 PM (<?=$starttime?> to <?=$endtime ?> time is booked for auto task)");
               input.value = "";
           }else{
            $.ajax({
                    url:'<?=base_url();?>Menu/CheckReviewTimeonPlanner',
                    type: "POST",
                    data: {
                    timeValue: timeValue,
                    pdate:slsctadate
                    },
                    cache: false,
                    success: function a(result){
                        if(result !== ''){
                           input.value = "";
                        let currentLocation = window.location.toString();
                        var jsonArray = JSON.parse(result);
                        var ckrevcount = jsonArray.length;
                      
                        var dataValue = jsonArray.data;
                        var redirect  = jsonArray.redirect;
                        // var revpage   = jsonArray.revpage.toString();
                        var rev_uid   = jsonArray.rev_uid;
                        var rev_bdid  = jsonArray.rev_bdid;
                        var by_type_id = jsonArray.by_type_id;
                        var to_type_id = jsonArray.to_type_id;
                        var cur_type_id = jsonArray.cur_type_id;
                        let timerInterval;
                      
                        Swal.fire({
                        title: "<u>Review Planned By</u>",
                        html: dataValue,
                        timer: 5000,
                        timerProgressBar: true,
                        didOpen: () => {
                            Swal.showLoading();
                            const timer = Swal.getPopup().querySelector("b");
                            timerInterval = setInterval(() => {
                            timer.textContent = `${Swal.getTimerLeft()}`;
                            }, 100);
                        },
                        willClose: () => {
                            clearInterval(timerInterval);
                        }
                        }).then((result) => {
                          if (result.dismiss === Swal.DismissReason.timer) {
                             input.value = "";
                          }
                        });
                        }else{
                          $.ajax({
                            url:'<?=base_url();?>Menu/GetCheckExistsTaskTime',
                            type: "POST",
                            data: {
                            timeValue: timeValue,
                            pdate:slsctadate
                            },
                            cache: false,
                            success: function a(result){
                              if(result > 0){
                                alert("You have already plan task for this time, please enter another time");
                                input.value = "";
                              }
                            }
                            });
                        }
                      }
                    });
           }
       }
      
       document.addEventListener('DOMContentLoaded', function() {
           const timeInput = document.getElementById('review_plantime');
           timeInput.setAttribute('min', '10:00');
           timeInput.setAttribute('max', '19:00');
           timeInput.addEventListener('change', validateTimeInputMeetReview);
       });
    </script>
    <?php
      }
      ?>
    <style>
      .scrollme{overflow-x:auto;h1{font-size:36px;color:#333}p{font-size:24px;color:#666;margin:10px}.container{background-color:#fff;padding:20px;border-radius:10px;box-shadow:0 0 10px rgb(0 0 0 / .2)}}.custom-card{padding:20px;border:1px solid #e0e0e0;border-radius:5px}.custom-card-header{background-color:#007bff;color:#fff;padding:10px 20px;border-radius:5px 5px 0 0}.custom-radio-label{font-weight:700}.card.container-fluid{background-color:honeydew}p#totalcompany{font-size:12px;padding:10px;color:green;font-weight:700;font-family:sans-serif}label{font-size:12px!important}div#maintaskcard{background:antiquewhite}div#selectCategory{background:#fff6dd}div#actionnotplaned{background:#434630;color:#fff}.card.p-4.taskselectionarea,#companyLocationdatacard,#selectCategory{background:#4bb1ac;background-image:linear-gradient(-225deg,#FF057C 0%,#8D0B93 50%,#321575 100%);color:#fff;}div#selectCategory{background:#4bb1ac;background-image:linear-gradient(-225deg,#FF057C 0%,#8D0B93 50%,#321575 100%);color:#fff}.modal-footer{justify-content:center!important}div#pstAssignCard,div#taskActionCard,div#partnertype,div#actionPlanned,div#review_planning_card,div#companyLocationdatacard,div#clusterLocactionFiltercard,div#sameStatusLastLimitDays,div#becauseofplanchange,div#becauseof_topsender_pst_meet,div#planbutnotinitiatedcard,div#actionnotplaned_NeedYour,div#planbutnotinitiatedcardold,div#auto_assign,div#mandatory_task_card,div#compulsive_task_card,div#pendingAutotaskCard,div#need_your_attention,div#firstQuarter1,div#tommarowAssignTaskype,div#reviewTargetDate{background:#4bb1ac;background-image:linear-gradient(-225deg,#FF057C 0%,#8D0B93 50%,#321575 100%);color:#fff}div#maintaskcard{background:#bfbfbf}.card-header.custom-card-header{border-radius:43px;text-align:center;padding:2px}.custom-card{background:#efb2b2}span.alertmessagecmp{font-size:14px;padding:2px;color:red}div#plantimerBox{background:linear-gradient(to right,#a80077,#66ff00);border-radius: 56px;box-shadow: rgba(0, 0, 0, 0.17) 0px -23px 25px 0px inset, rgba(0, 0, 0, 0.15) 0px -36px 30px 0px inset, rgba(0, 0, 0, 0.1) 0px -79px 40px 0px inset, rgba(0, 0, 0, 0.06) 0px 2px 1px, rgba(0, 0, 0, 0.09) 0px 4px 2px, rgba(0, 0, 0, 0.09) 0px 8px 4px, rgba(0, 0, 0, 0.09) 0px 16px 8px, rgba(0, 0, 0, 0.09) 0px 32px 16px;}span#timer{font-size:38px;color:#fff}.stopbtntimer{align-items:center;justify-content:center;display:flex}button#stop{padding: 7px 12px;} table.dataTable>thead>tr>th:not(.sorting_disabled), table.dataTable>thead>tr>td:not(.sorting_disabled) {padding-right: 30px; background: #851241;color: white;}
      .hrclass{box-shadow: rgba(0, 0, 0, 0.17) 0px -23px 25px 0px inset, rgba(0, 0, 0, 0.15) 0px -36px 30px 0px inset, rgba(0, 0, 0, 0.1) 0px -79px 40px 0px inset, rgba(0, 0, 0, 0.06) 0px 2px 1px, rgba(0, 0, 0, 0.09) 0px 4px 2px, rgba(0, 0, 0, 0.09) 0px 8px 4px, rgba(0, 0, 0, 0.09) 0px 16px 8px, rgba(0, 0, 0, 0.09) 0px 32px 16px;}
      .bgresactive{background: #263b0d!important;}
      .form-control.is-invalid, .was-validated .form-control:invalid {background-image: none !important;}
      .form-control.is-valid, .was-validated .form-control:valid {background-image: none !important;}   
      .select-readonly { background-color: #f5f5f5; color: #666;cursor: not-allowed;}
      lable{font-size: 14px!important; font-weight: 500!important;font-family: system-ui!important;}
      .boxshadownew{box-shadow: rgba(0, 0, 0, 0.17) 0px -23px 25px 0px inset, rgba(0, 0, 0, 0.15) 0px -36px 30px 0px inset, rgba(0, 0, 0, 0.1) 0px -79px 40px 0px inset, rgba(0, 0, 0, 0.06) 0px 2px 1px, rgba(0, 0, 0, 0.09) 0px 4px 2px, rgba(0, 0, 0, 0.09) 0px 8px 4px, rgba(0, 0, 0, 0.09) 0px 16px 8px, rgba(0, 0, 0, 0.09) 0px 32px 16px;border-radius: 25px;}span#tasktype {color: green;}   
      .pllanerseesioncnt {align-items: center;justify-content: center;display: flex;}
      .findslot{background-color: green; color: white; padding: 4px; border-radius: 8px; box-shadow: rgba(50, 50, 93, 0.25) 0px 2px 5px -1px, rgba(0, 0, 0, 0.3) 0px 1px 3px -1px; font-size: 12px;margin: 4px;}
      div#findbookedslot,div#freeaslotDisplay{
      justify-content: center; 
      display: flex; flex-wrap: wrap;
      }
      .findbookedslottime{
      background-color: red; 
      color: white;
      padding: 4px; 
      border-radius: 8px; 
      box-shadow: rgba(50, 50, 93, 0.25) 0px 2px 5px -1px, rgba(0, 0, 0, 0.3) 0px 1px 3px -1px; 
      font-size: 12px;
      margin: 4px;
      }
      button#resteFilter {
      font-size: 14px;
      font-weight: 700;
      box-shadow: rgba(9, 30, 66, 0.25) 0px 1px 1px, rgba(9, 30, 66, 0.13) 0px 0px 1px 1px;
      border-radius: 20px;
      }
      
      .cat1{background:#effaf8!important}.cat2{background:#fef2f4!important}.cat3{background:#fffaef!important}.cat4{background:#eefbf5!important}.cat5{background:#f7f3ff!important}.cat6{background:#fff7ef!important}.cat7{background:#f1fbff!important}.cat8{background:#fff0f8!important}
 
 #chart_div table {
  border-radius: 50%;
  background: linear-gradient(90deg, hsla(339, 100%, 55%, 1) 0%, hsla(197, 100%, 64%, 1) 100%);
  animation: gradientShift2 1s ease-in-out infinite alternate;
  transition: 0.4s all ease-in-out;
}
@keyframes gradientShift2 {
  0% {background: linear-gradient(90deg, hsla(339, 100%, 55%, 1) 0%, hsla(197, 100%, 64%, 1) 100%);}
  25% {background: linear-gradient(180deg, hsla(339, 100%, 55%, 1) 0%, hsla(197, 100%, 64%, 1) 100%);}
  50% {background: linear-gradient(270deg, hsla(339, 100%, 55%, 1) 0%, hsla(197, 100%, 64%, 1) 100%);}
  75% {background: linear-gradient(360deg, hsla(339, 100%, 55%, 1) 0%, hsla(197, 100%, 64%, 1) 100%);}
  100% {background: linear-gradient(90deg, hsla(339, 100%, 55%, 1) 0%, hsla(197, 100%, 64%, 1) 100%);}
}
div#findCompanyByCard{
  background-image: linear-gradient(-225deg, #ffffff 0%, #ffffff 29%, #ff92b6 67%, #FFF800 100%);
}
    p#meetingrelmsg {
    text-align: center;
    font-weight: 500;
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
            <?php 
              $action = $this->Menu_model->get_action();
              if($type_id == 3){
                foreach ($action as $key => $value) {
                  if ($value->id == 17 || $value->id == 18) {
                      unset($action[$key]);
                  }
                }
                $action = array_values($action);
              }
               ?>
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
              if ($this->session->flashdata('error_message')): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
              <strong> <?php echo $this->session->flashdata('error_message'); ?></strong>
              <button type="button" class="close" data-dismiss="alert" aria-label="Close">
              <span aria-hidden="true">&times;</span>
              </button>
            </div>
            <?php endif; ?>
            <?php
              if ($this->session->flashdata('success_message_plan')): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
              <strong> <?php echo $this->session->flashdata('success_message_plan'); ?></strong>
              <button type="button" class="close" data-dismiss="alert" aria-label="Close">
              <span aria-hidden="true">&times;</span>
              </button>
            </div>
            <?php endif; ?>
            <?php
              if ($this->session->flashdata('error_message_plan')): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
              <strong> <?php echo $this->session->flashdata('error_message_plan'); ?></strong>
              <button type="button" class="close" data-dismiss="alert" aria-label="Close">
              <span aria-hidden="true">&times;</span>
              </button>
            </div>
            <?php endif; ?>
            <?php
              if ($this->session->flashdata('auto_success_message')): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
              <strong> <?php echo $this->session->flashdata('auto_success_message'); ?></strong>
              <button type="button" class="close" data-dismiss="alert" aria-label="Close">
              <span aria-hidden="true">&times;</span>
              </button>
            </div>
            <?php endif; ?>
            <div class="row mb-2">
              <div class="col-sm-6">
                <h1 class="m-0"></h1>
              </div>
              <!-- /.col -->
              <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                  <h4><?php $uid=$user['user_id']?></h4>
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
        <style>
          #plandate{width:300px;}.setpaldate{display:flex;}.planerdflex{align-items: center; justify-content: center; display: flex;}
        </style>
        <div class="container-fluid">
          <?php
            // $totalttaskdata = $this->Menu_model->getUserTotalTaskTimeForTodays($uid,$adate);
            $totalttaskdata = $this->Menu_model->getUserTotalTaskTimeForTodays1($uid,$adate);
            $totalttasktime = $totalttaskdata[0]->ttime;

            $reviewaDatas    = $this->Menu_model->GetAllPlannedReviewByUserByDate($uid,$adate);
            $reviewaDatascnt = sizeof($reviewaDatas);
            if($reviewaDatascnt > 0){
              $totalReviewTimeinMinutes = 0;
              foreach($reviewaDatas as $reviewaData){
                $review_create_date           = $reviewaData->sdatet;
                $review_plant_date_time       = $reviewaData->plant;
                $review_review_close_time     = $reviewaData->review_close_time;
                $review_fix_time              = date('H:i:s', strtotime($review_review_close_time));
                
                if ($review_fix_time === '00:00:00') {
                  $review_time =  0;
                } else {
                // Convert datetime strings to timestamps
                $startReviewTimestamp       = strtotime($review_plant_date_time);
                $endReviewTimestamp         = strtotime($review_review_close_time);
                // Calculate the difference in seconds
                $differenceInSecondsReview  = $endReviewTimestamp - $startReviewTimestamp;
                // Convert the difference to minutes
                $differenceInMinutesReview  = $differenceInSecondsReview / 60;
                // Calculate hours and minutes
                $reviewHours                = floor($differenceInSecondsReview / 3600);
                $reviewMinutes              = floor(($differenceInSecondsReview % 3600) / 60);
                $totalReviewTimeinMinutes  += $differenceInMinutesReview;
                }
              }
              $totalttasktime           = $totalttasktime + $totalReviewTimeinMinutes;
              // echo 'totalttasktime = ' .$totalttasktime.'<br/>';
              // echo 'totalReviewTimeinMinutes = ' .$totalReviewTimeinMinutes;
            }else{
              $review_time =  0;
            }


     


            $taskplanmincount =0;
            $lunchtime      = 30;      // Lunch Time 45 Miniute
            $autoTasktime   = 90;  // 90 Minutes For Auto Task
            $topp           = 60; // 60 Minutes For Tommorow Planner Planning
            $texpense_time  = $lunchtime + $autoTasktime + $topp; // totol expense time
            $nine_hours_planning =540; // 9 hours Planning = 9* 60 = 540 Minutes 

            if(in_array($user['type_id'], [16])){
              $nine_hours_planning = 10; // 9 hours Planning = 9* 60 = 540 Minutes 
            }


            $userplanetime = $nine_hours_planning - $texpense_time; // total plan time  - 345 minutes
            $plannerremTime = $userplanetime - $taskplanmincount; 
            $plrequest    = $this->Menu_model->GetTodaysPlannerRequestByDate($uid,$adate);
            $plrequestcnt = sizeof($plrequest);
            if($plrequestcnt > 0){
            $apr_time       = $plrequest[0]->apr_time;
            $request_time   = $plrequest[0]->created_at;
            
            $req_datetime1  = new DateTime($request_time);
            $req_datetime2  = new DateTime($apr_time);
            // Calculate the difference in request approved
            $req_interval   = $req_datetime1->diff($req_datetime2);
            // Get the difference in hours and minutes in request approved
            $apr_hours      = $req_interval->h + ($req_interval->days * 24); // Total hours
            $apr_minutes    = $req_interval->i; // Remaining minutes
            $reqlateapr     = "$apr_hours hours and $apr_minutes minutes";
            $tsk_initialTime    = $apr_time;
            $tsk_dateTime       = new DateTime($tsk_initialTime);
            $tsk_dateTime->modify('+60 minutes');
            $tskinittime        = $tsk_dateTime->format('Y-m-d H:i:s');
            $alertmessage = 'Your Planner request approved time is : '.$apr_time .', and user planner time is 1 hour. Based on this time, user need to plan the task after '.$tskinittime .'.';
            if(!is_null($apr_time)):
            $dateTime = new DateTime($apr_time);
            $dateTime->modify('+60 minutes');
            $newTime = $dateTime->format('Y-m-d H:i:s');
            $todaysDateTime = date("Y-m-d") . ' 10:00:00';
            $todaysDateTime = DateTime::createFromFormat('Y-m-d H:i:s', $todaysDateTime); // Corrected format string
            $apr_times = $apr_time;
            $apr_time = new DateTime($apr_time);
            $interval = $todaysDateTime->diff($apr_time);
            
            // Get the difference in total minutes
            $diffInMinutes = ($interval->h * 60) + $interval->i;
            
            $rmautoTasktime = 30;
            
            $plannerremTime = $plannerremTime - $diffInMinutes;
            //$plannerremTime = $plannerremTime + $rmautoTasktime;
            $userplanetime = $userplanetime - $diffInMinutes;
            // $userplanetime = $userplanetime + $rmautoTasktime;
            endif;
            }
            $checkHalfDayLeave = checkHalfDayLeave($uid,$adate);
            
            if(sizeof($checkHalfDayLeave) == 1){
            
                $userplaetime = $userplaetime/2;
                $plannerremTime = $plannerremTime/2;
            
            }
            if($totalttasktime >= $plannerremTime){
              $aid=$this->Menu_model->getAdminId($uid);
              $admin_id=$aid[0]->aadmin;
              $uDetails=$this->Menu_model->get_userName($uid);
              $uName=$uDetails[0]->name;
            
              $type=2;
              $msg="Task approval request received from $uName";
            
              $isPresent = $this->Menu_model->findNotification($uid, $type, $adate);
              //dd($isPresent);
              if(empty($isPresent)){
                $this->Menu_model->triggerNotification($uid, $admin_id, $type, $msg, $adate);
              }
              $hours_tasktime = floor($totalttasktime / 60);
              $remainingMinutes_tasktime = $totalttasktime % 60;
              $timetexts =  "$hours_tasktime hours and $remainingMinutes_tasktime minutes";
              $timecolor = "green";
            }else{
              $remaintaktimeis = $plannerremTime - $totalttasktime;
              $hours_tasktime = floor($totalttasktime / 60);;
              $remainingMinutes_tasktime = $totalttasktime % 60;
              $timetexts = "$hours_tasktime hours and $remainingMinutes_tasktime minutes";
              $timecolor = "red";
            }

             $pltrequest    = $this->Menu_model->GetPlannerApprovelRequest($uid,$adate);
          $pltrequestcnt =  sizeof($pltrequest);

                    ?>
          <div card="card card3444 text-center">
            <center>
              <div class="text-effect" data-content="<?= $timetexts ?>" style="background:green;color:white;">
                <span class="text-uppercase">Planned Time : <?= $timetexts ?></span>
              </div>
              <?php if($timecolor == "red"){ ?>
              <div class="text-effect1 mb-1 mt-1">
                <span class="text-capitalize font-weight-bold">
                <?php 
                  $hours_tasktime = floor($remaintaktimeis / 60);;
                  $remainingMinutes_tasktime = $remaintaktimeis % 60;
                  $remaintaktime_is = "$hours_tasktime hours and $remainingMinutes_tasktime minutes";
                   echo "<mark> Remaining Time to Enable Task Approval Feature :<span class='remening_time_cnt'> ".$remaintaktime_is."<span></mark>";
                  ?></span>
              </div>
              <?php } ?>
            </center>
          </div>

          <?php 
          if($timecolor == "green"){
        
          if($pltrequestcnt == 0){ 

             $plrequest_message = "We have scheduled a total of $timetexts for the planned tasks as outlined in our current plan  on date $adate. Kindly review and approve the proposed time allocation so that we can proceed as per the schedule. Please let us know if any adjustments or clarifications are required. Looking forward to your approval.";
            ?> 
            <div class="form-container" style="max-width: 600px; margin: 50px auto; background: white; padding: 30px; border-radius: 10px; box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);">
              <h4 class="mb-4 text-center">Planner Task Approved Request</h4>
              <form method="post" action="<?=base_url()?>Menu/RequestForPlannerApproval">
                <div class="mb-3">
                  <input type="hidden" value="<?=$adate?>" name="plrequest_date" required>
                  <label for="approved_status" class="form-label">Approval Message</label>
                 <textarea class="form-control" name="plrequest_message" id="plrequest_message" required rows="4" readonly><?=$plrequest_message;?></textarea>
                </div>
                <button type="submit" class="btn btn-primary w-100">Request For Approval</button>
              </form>
            </div>
          <?php } }?>

          <style>
            .text-effect{
            color: <?= $timecolor; ?>;
            font-family: 'Dosis', sans-serif;
            font-size:24px;
            font-weight: 700;
            text-align: center;
            position: relative;
            }
            .remening_time_cnt{color:red;}
            mark{background:yellow;padding:4px;}
            @media only screen and (max-width: 990px){
            .text-effect{ font-size: 24px; }
            }
            @media only screen and (max-width: 767px){
            .text-effect{ font-size: 22px; }
            }
            @media only screen and (max-width: 576px){
            .text-effect{ font-size: 20px; }
            }
          </style>
          <marquee class="p-2 mt-1" width="100%"  onMouseOver="this.stop()" onMouseOut="this.start()" behavior="left" bgcolor="pink">
            <h6> Lunch Time : <?= $lunchtime ?>  Miniute || Auto Task Time : <?= $autoTasktime?> Minutes || Tommorow Planner Planning : <?=$topp ?>  Minutes || 9 hours Planning = 9* 60 = 540 Minutes || Total Time For (Lunch + Auto Task + Tommorow Planner) : <?=$texpense_time?>  Minutes || Task Planner Should be <?php echo 540 - $texpense_time;?> Minutes</h6>
          </marquee>
          <marquee class="p-2 mt-1" width="100%"  onMouseOver="this.stop()" onMouseOut="this.start()" behavior="left" bgcolor="pink">
            <small><span><?= $alertmessage; ?></span></small>
          </marquee>
          <div class="card p-2 bg-primary">
            <div class="row">
              <div class="col-md-4 planerdflex">
                <?php
                  $aptime = $this->Menu_model->GetTodaysAutoTaskANDPlanningTime($uid,date("Y-m-d"));
                  $aptimecnt = sizeof($aptime);
                  if($aptimecnt > 0){
                    $start_tttpft = $aptime[0]->start_tttpft;
                    $end_tttpft = $aptime[0]->end_tttpft;
                    $timeArray = explode(':', $start_tttpft);
                    // Assign the components to variables
                    $phours = $timeArray[0];
                    $pminutes = $timeArray[1];
                    $pseconds = $timeArray[2];
                    ?>
                <div class="mt-3">
                  <p><b> <span id="yndpt">Todays Planner Time is :</span> <?=$start_tttpft; ?></b></p>
                </div>
                <?php } ?>
              </div>
              <div class="col-md-2 planerdflex">
                <strong> Plan Date : <?=$adate ?></strong>
              </div>
              <div class="col-md-4 planerdflex">
                <?php 
                  $adatess        = date("Y-m-d");
                  $next_date      = date('Y-m-d', strtotime('+1 day', strtotime($adatess)));
                  $user_day       = $this->Menu_model->get_daystarted($uid,date("Y-m-d"));
                  if(sizeof($user_day) > 0){
                    $pinitiate_time = $user_day[0]->planner_initiate_time;
                  ?>
                <strong> Planner Initiated Time : <?=$pinitiate_time ?></strong>
                <!-- <input placeholder="Select Date" class="form-control m-2" type="text" onfocus="(this.type='date')" onblur="(this.type='text')" name="adate" required="" id="plandate"  min="<?= date('Y-m-d') ?>" max="<?= $next_date ?>" />
                  <input type="submit" class="btn btn-warning m-2" value="Set Date"> -->
                <?php } ?>
              </div>
              <div class="col-md-2 planerdflex">
                <span class="btn btn-primary" data-toggle="modal" data-target="#viewTodaysPlannerDetails"> <strong>🎯 View Planner Details</strong> </span>
              </div>
            </div>
          </div>




           <div class="modal fade" id="viewTodaysPlannerDetails" tabindex="-1" role="dialog" aria-labelledby="exampleModalLongTitle" aria-hidden="true">
                    <div class="modal-dialog" role="document" style="max-width: 100%; height: 100%; margin: 0; padding: 0;">
                      <div class="modal-content" style="height: 100%; border: none; border-radius: 0;">
                        <div class="modal-header" style="background: linear-gradient(to right, #fff8e1, #ffffff); border-radius: 12px; box-shadow: 0 4px 8px rgba(0,0,0,0.1); padding: 20px;">
                          <h5 class="modal-title text-dark" id="exampleModalLongTitle">
                            <center>
                              🎯 <?=$adate?> Planner Details
                            </center>
                          </h5>
                          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                          </button>
                        </div>
                        <div class="modal-body" style="height: calc(100% - 120px); overflow-y: auto;">
                          <!-- Modal content here -->
                        <div class="iframe_card" style="
                                max-width: 100%;
                                margin: 20px auto;
                                padding: 10px;
                                background: #ffffff;
                                border-radius: 16px;
                                box-shadow: 0 8px 20px rgba(0, 0, 0, 0.15);
                                transition: transform 0.3s ease, box-shadow 0.3s ease;
                                overflow: hidden;
                              ">
                          <iframe id="myIframe" src="<?= base_url() ?>Menu/CheckTaskDetailsByUser/<?=$uid?>/<?=$adate?>" style="
                                width: 100%;
                                height: 100vh;
                                border: none;
                                border-radius: 12px;
                                box-shadow: 0 0 10px rgba(0, 0, 0, 0.15);
                                transition: all 0.3s ease-in-out;
                            " loading="lazy"></iframe>

                            <script>
                              document.getElementById('myIframe').addEventListener('load', function () {
                                const iframe = document.getElementById('myIframe');
                                const innerDoc = iframe.contentDocument || iframe.contentWindow.document;
                                const hideSidebar = () => {
                                  if (!innerDoc) {
                                    console.log('Iframe document not accessible yet.');
                                    setTimeout(hideSidebar, 200);
                                    return;
                                  }
                                  const sidebar = innerDoc.querySelector('.main-sidebar.sidebar-dark-primary.elevation-4');
                                  const mainHeader = innerDoc.querySelector('.main-header.navbar.navbar-expand.navbar-white.navbar-light');
                                  const contentWrapper = innerDoc.querySelector('.content-wrapper');
                                  const mainFooter = innerDoc.querySelector('.main-footer');

                                  if (sidebar || mainHeader) {
                                    if (sidebar) sidebar.remove();
                                    if (mainHeader) mainHeader.remove();

                                    // Adjust layout elements inside the iframe after removing sidebar/header
                                    if (contentWrapper) contentWrapper.style.marginLeft = '0';
                                    if (mainFooter) mainFooter.style.marginLeft = '0';
                                    // console.log('Sidebar and header removed successfully.');
                                  } else {
                                    // console.log('Sidebar or header not found. Retrying...');
                                    setTimeout(hideSidebar, 200);
                                  }
                                };
                                hideSidebar();
                              });
                            </script>
                              </div>
                              <style>
                                .iframe_card:hover {
                                  transform: scale(1.01);
                                  box-shadow: 0 12px 28px rgba(0, 0, 0, 0.25);
                                }
                              </style>
                        </div>
                      </div>
                    </div>
                  </div>

          <?php
            $reqCount = sizeof($getreqData);
            $getAutoTaskTime = sizeof($getAutoTaskTime);
            $approvel_status = $getreqData[0]->approvel_status;
            $oldPendTaskcnt = sizeof($oldPendTask);


                // if($pendingtask !== 0){
                // echo "<pre>";
                //   print_r($reqCount);
                //   die; 
            // $approvel_status = 'Approved';
            
            
            if($adate == date("Y-m-d") && $getAutoTaskTime == 0 || $adate !== date("Y-m-d")){ 
                if($getAutoTaskTime !==1){
                ?>
          <div class="justify-content-center col-lg-12 col-md-12 col-sm-4 col-sm m-auto p-3">
            <div class="card">
              <div class="card-body" id="mainboxAutoTask1">

                <div class="row">
                    <div class="col-md-6 card">
                    <center><h5><i>First Set Auto Task Time </i></h5></center>
                <hr/>
                <marquee class="p-2 mt-1" width="100%"  onMouseOver="this.stop()" onMouseOut="this.start()" behavior="left" bgcolor="pink">
                  <h6> Auto task time should be between 4:00 PM to 7:00 PM and maximum duration of 90 minutes. </h6>
                </marquee>
                <form method="post" action="<?=base_url();?>Menu/updateAtotaskTime">
                  <div class="was-validated">
                    <input type="hidden" id="userid" value="<?=$uid?>" name="bdid" required="">
                    <div class="col-md-12 col-sm mt-3">
                      <input type="hidden" class="form-control" id="ttype" name="ttype" Value="Auto Task" required="">
                      <input type="hidden" class="form-control" name="pdate" value="<?=$adate?>" required="">
                      <input type="hidden" name="ntuid" value="<?=$uid?>" required="">
                      <div class="form-group">
                        <label for="start-time">Enter Start Time</label>
                        <input type="time" id="start-time" name="startautotasktime" class="form-control" required>
                      </div>
                      <div class="form-group">
                        <label for="end-time">Enter End Time</label>
                        <input type="time" id="end-time" name="endautotasktime" class="form-control" required>
                      </div>
                      <hr>
                      <?php 
                        if($adate !== date("Y-m-d")){ 
                         $userdfrom = $this->Menu_model->userworkfrom() ?>
                      <div class="form-group">
                        <label>Select Your Tomorrow  Day</label>
                        <select name="userworkfrom" class="form-control">
                          <?php foreach($userdfrom as $udfrom){ ?>
                          <option value="<?= $udfrom->ID; ?>"><?= $udfrom->TYPE; ?></option>
                          <?php } ?>
                        </select>
                      </div>
                      <?php } ?>
                      <hr>
                      <marquee class="p-2 mt-1" width="100%"  onMouseOver="this.stop()" onMouseOut="this.start()" behavior="left" bgcolor="pink">
                        <h6> Todays is the Time to plan for check tomorrow. Its maximum of 1 hour. After Auto Task</h6>
                      </marquee>
                      <div class="form-group">
                        <label for="end-time">Today is the start time to plan for tomorrow.</label>
                        <input type="time" readonly id="start_tttpft" name="start_tttpft" class="form-control" required>
                      </div>
                      <div class="form-group">
                        <label for="end-time">Today is the end time to plan for tomorrow.</label>
                        <input type="time" readonly id="end_tttpft" name="end_tttpft" class="form-control" required>
                      </div>
                      <center><button class="btn btn-primary m-3" type="submit" id="autoplan_submit">Submit</button></center>
                    </div>
                </form>
                </div>
                    </div>
                    <div class="col-md-6" style="align-items: center; justify-content: center; display: flex ;">
                        <img src="https://stemapp.in/assets/image/autotask3.png" class="img-fluid" alt="auto task image">
                    </div>
                </div>


              </div>
            </div>
            <?php }}else if($adate == date("Y-m-d") || $approvel_status == '' || $approvel_status =='Reject'){
              if($reqCount !== 1 && $adate == date("Y-m-d")){
                  $getPendingTaskreq = $this->Menu_model->GetUserRequestForPendingTask($uid,$adate);
                  $getPendingTaskreqcnt = sizeof($getPendingTaskreq);
                  if($getPendingTaskreqcnt > 0){
                      $getPendingTaskreqappr = $getPendingTaskreq[0]->approvel_status;
                    }
                    ?>
            <div class="card p-2 bg-dark text-center">
              <h5><i>If you want to plan task for today, you need to take approval first.</i></h5>
            </div>

            <div class="row">
              <div class="col-md-6">
                    <div class="card" style="align-items: center; height: 100%; justify-content: center;">
                      <div class="planner-request-form">
                      <form class="was-validated" action="<?=base_url();?>Menu/RequestForTodaysTaskPlan/<?=$adate ?>" method="post">
              <input type="hidden" value="<?= $adate?>" name="setdatebyuser">
              <input type="hidden" value="<?= $oldPendTaskcnt?>" name="taskcnt">
               <div class="row">
                <div class="col-md-12">
                <?php 
                $checkYPlannerStartOrNot    = $this->Menu_model->CheckYesterdayPlannerStartOrNot($uid,$adate);
                $checkYPlannerStartOrNotCnt = sizeof($checkYPlannerStartOrNot);
                // $checkYPlannerStartOrNotCnt = 0;
                // $totalttasktime = 100;
                if($totalttasktime < 360){
                  if($checkYPlannerStartOrNotCnt > 0){

                    $yester_planner_start_time_date = $checkYPlannerStartOrNot[0]->created_at;
                    $yester_planner_start_time = date('Y-m-d', strtotime($yester_planner_start_time_date));
  
                    $ctpstyd    = $this->Menu_model->CheckTodaysPlannerStartTimeOnYesterDayDate($uid,$yester_planner_start_time);
                    $ctpstydcnt = sizeof($ctpstyd); 
                    if($ctpstydcnt > 0){
                      $start_tttpft     = $ctpstyd[0]->start_tttpft;
                      $start_tttpft    = $yester_planner_start_time.' '.$start_tttpft;
                      $yestTommPlannerDateTime = "My planner time was set to $start_tttpft , but I started the actual planning at  $yester_planner_start_time_date for the date $adate";
  
                    }else{
                      $yestTommPlannerDateTime = '';
                    }
     
                    $remaining_message_part1 = "Unfortunately, todays planner is not fully set. Yesterdays planner was only partially completed";
                    $remaining_message_part2 = "Unfortunately, todays planner is not fully set. Yesterdays planner was only partially completed — we planned tasks for just $timetexts. Remaining time to plan tasks: $remaintaktime_is. $yestTommPlannerDateTime";
                    $textareapartially = 'readonly';
                    ?>
                    <hr>
                    <input type="hidden" value="<?=$remaining_message_part2?>" name="unfortunately_message">
                  <marquee class="p-2 mt-1" width="100%" onmouseover="this.stop()" onmouseout="this.start()" behavior="alternate" style="background: red; color: white;">
                  <h6><?= $remaining_message_part1 ?></h6>
                </marquee>
                <hr>
                  <?php
                    $tpRequestOptions = [
                      $remaining_message_part1
                  ];
                  }else{

                     $checkYesterDayPlannerSetOrNot = $this->Menu_model->CheckYesterDayPlannerSetOrNot($uid,$adate);
                        $no_one_pending = $checkYesterDayPlannerSetOrNot[0]->achieve;
                        $aut_id  = $checkYesterDayPlannerSetOrNot[0]->aut_id;
                        if($aut_id == ''){
                          if ($no_one_pending == 'yes') {
                              $clreason = " * Please Clarify the reason why Planner is not set because there are no pending tasks.";
                              $remaining_message_part2 = "$clreason All {$checkYesterDayPlannerSetOrNot[0]->complete} tasks are completed. ";
                            
                              if($checkYesterDayPlannerSetOrNot[0]->last_actiontype_id !==''){
                                $last_task_name_data = $this->Menu_model->getTaskAction($checkYesterDayPlannerSetOrNot[0]->last_actiontype_id);
                                $remaining_message_part2 .= " Your Last Task Name is : {$last_task_name_data[0]->name}). ";
                                $remaining_message_part2 .= " and last task updated on {$checkYesterDayPlannerSetOrNot[0]->last_task_update}";
                                $textareapartially = 'readonly';
                              }
                          }else{
                              $remaining_message_part2 = " * Please Clarify the reason why there your pending tasks is {$checkYesterDayPlannerSetOrNot[0]->pending} .";
                              $textareapartially = 'readonly';
                              $tpRequestOptions = [
                                  "Not planned yesterday due to an urgent meeting",
                                  "Not planned yesterday due to network issues",
                                  "Not planned yesterday due to health issues",
                                   "Not planned yesterday due to urgent personal work",
                                  "Forgot to set up the planner yesterday"
                              ];
                          }
                        }else{

                           $tpRequestOptions = [
                              "Planning urgent tasks for today",
                              "Planning yesterday pending tasks",
                              "Not planned yesterday due to network issues",
                              "Not planned yesterday due to health issues",
                              "Not planned yesterday due to an urgent meeting",
                              "Forgot to set up the planner yesterday"
                          ];
                          $remaining_message_part2 = '';
                          $textareapartially = '';

                        }
                  }
                }else{
                  $tpRequestOptions = [
                    "Planning urgent tasks for today",
                    "Planning yesterday pending tasks",
                    "Not planned yesterday due to network issues",
                    "Not planned yesterday due to health issues",
                    "Not planned yesterday due to an urgent meeting",
                    "Forgot to set up the planner yesterday"
                ];
                $remaining_message_part2 = '';
                $textareapartially = '';
                }
                ?>
                <input type="hidden" value="<?=$remaining_message_part2?>" name="unfortunately_message">
                  <label for="validationServer04" class="form-label">
                  Why would you want to set up todays planner?
                  </label>
                  <select class="form-select is-invalid" id="validationServer04" aria-describedby="validationServer04Feedback" name="would_you_want" required>
                  <?php foreach ($tpRequestOptions as $tpRequestOption): ?>
                        <option value="<?= $tpRequestOption; ?>">
                            <?= $tpRequestOption; ?>
                        </option>
                    <?php endforeach; ?>
                  </select>
                </div>
              </div>
              <?php if($remaining_message_part2 !==''){ ?> 
                <hr>
                  <div class="card">
                    <?= $remaining_message_part2?>
                  </div>
                <hr>
                <?php } ?>
           
              <div class="mb-3">
                <label for="requestForTodaysTaskPlan" class="form-label">Type Reason : </label>
                <textarea class="form-control" name="requestForTodaysTaskPlan" id="requestForTodaysTaskPlan" required rows="3"></textarea>
                <div class="invalid-feedback">* Invalid Message</div>
              </div>
              <hr>
              <div class="mb-3 text-center">
                <input type="submit" class="btn btn-warning" onclick="this.form.submit(); this.disabled = true;" value="Send Request">
              </div>
              <hr>
            </form>
                      </div>
                    </div>
              </div>
              <div class="col-md-6">
                <div class="card">
                  <img src="<?=base_url()?>assets/image/Planner/3.jpg" class="img-fluid" alt="">
                </div>
              </div>
            </div>
              <br>

            



            <?php if($oldPendTaskcnt > 0 && ($getPendingTaskreqappr !== '1')){ ?>
            <hr>
            <div class="card p-2 text-center bg-danger">
              <h3>Yesterday's Pending Task </h3>
            </div>
            <hr>
            <div class="table-responsive text-nowrap">
              <table id="example10" class="table table-striped table-bordered" cellspacing="0" width="100%">
                <thead>
                  <tr>
                    <th scope="col">#</th>
                    <th scope="col">Company Name</th>
                    <th scope="col">Company Status</th>
                    <th scope="col">Task Type</th>
                    <th scope="col">Task Date</th>
                    <th scope="col">Action Taken</th>
                    <th scope="col">Purpose Taken</th>
                  </tr>
                </thead>
                <tbody>
                  <?php $i=1; foreach($oldPendTask as $data){
                    $compname = $this->Menu_model->get_cmpbyinid($data->cid_id)[0]->compname;
                    $compsname = $this->Menu_model->get_statusbyid($data->status_id)[0]->name;
                    $actname = $this->Menu_model->get_actionbyid($data->actiontype_id)[0]->name;
                    ?>
                  <tr>
                    <th><?=$i?></th>
                    <td><?= $compname ?></td>
                    <td><?=$compsname ?></td>
                    <td><?= $actname ?></td>
                    <td><?= $data->appointmentdatetime ?></td>
                    <td><?=$data->actontaken ?></td>
                    <td><?=$data->purpose_achieved ?></td>
                  </tr>
                  <?php $i++; } ?>
                </tbody>
              </table>
            </div>
            <?php } ?>
            <?php } 
              }
                if($reqCount == 1 && $approvel_status == '' || $approvel_status =='Reject' ){
                ?>

          <hr>
           <div class="table-responsive">
           <table class="table table-striped table-dark">
              <thead>
                <tr>
                  <th scope="col">#</th>
                  <th scope="col">Name</th>
                  <th scope="col">Date</th>
                  <th scope="col">Request Type</th>
                  <th scope="col">Request Message</th>
                  <th scope="col">Approvel Status</th>
                  <th scope="col">Remarks</th>
                </tr>
              </thead>
              <tbody>
                <?php foreach($getreqData as $data){ ?>
                <tr>
                  <th>1</th>
                  <td><?= $this->Menu_model->get_userbyid($data->user_id)[0]->name ?></td>
                  <td><?= $data->created_at ?></td>
                  <td><?= $data->would_you_want ?></td>
                  <td><?= $data->request_remarks ?></td>
                  <td>
                    <?php
                      if($data->approvel_status == ''){ ?>
                    <span class="p-1 bg-warning mr-2">Pending</span>
                    <?php }else if($data->approvel_status == 'Approved'){ ?>
                    <span class="p-1 bg-success mr-2">Approved</span>
                    <?php }else{ ?>
                    <span class="p-1 bg-danger mr-2">Reject</span>
                    <?php }?>
                  </td>
                  <td><?=$data->remarks ?></td>
                </tr>
                <?php } ?>
              </tbody>
            </table>
           </div>
              <hr>        


            <?php }else if($getAutoTaskTime ==1 && $reqCount == 1 && $approvel_status == 'Approved' || $adate !== date("Y-m-d")){   
              if($getAutoTaskTime == 1){ ?>
          </div>
          <section class="content">
            <div class="card container-fluid" id="backgroundchange">
              <br>
              <div class="col-md-12 plantimer text-center p-2 mb-2" id="plantimerBox">
                <div class="row">
                  <div class="col-md-2 pllanerseesioncnt">
                    <h3 id="PlannerSessionStimer" class="text-white d-flex"></h3>
                  </div>
                  <div class="col-md-8">
                    <span id="timer">00:00:00</span>
                  </div>
                  <div class="col-md-2 stopbtntimer">
                    <button type="button" class="btn btn-danger" id="stop">Stop Planning</button>
                  </div>
                </div>
              </div>
              <center>
                <hr class="hrclass" style="width: 600px;"/>
              </center>
              <div class="row">
                <div class="justify-content-center col-md-8" id="planningStartbtn" >
                  <div class="card" style="min-height:100px;align-items: center; justify-content: center; display: flex;" >
                    <div class="planningTime">
                      <button type="button" class="btn btn-primary" id="start">Start Planning</button>
                    </div>
                  </div>
                  <div class="table-responsive">
                    <?php $planSessionData  = $this->Menu_model->TodaysPlannerSession($uid); ?>
                    <?php $planSessionmin  = $this->Menu_model->TodaysTotalsPlannerSessioninMinute($uid); ?>
                    <p class="text-center" > <b>Today's Total Time Spent in Planning : <?=$planSessionmin; ?></b> </p>
                    <table id="example1" class="table table-striped table-bordered" cellspacing="0" width="100%">
                      <thead>
                        <tr>
                          <th>S.No</th>
                          <th>Name</th>
                          <th>Start Date</th>
                          <th>Start Time</th>
                          <th>End Time</th>
                          <th>End Date</th>
                          <th>Total Consume Time</th>
                          <th>Total Task</th>
                          <th>Average time per task</th>
                        </tr>
                      </thead>
                      <tbody>
                        <?php 
                          $i =1;
                          $planSessionData  = $this->Menu_model->TodaysPlannerSession($uid);
                          $planSessionDatacnt = sizeof($planSessionData);
                          if($planSessionDatacnt == 0){
                            $planSessionDatacnt = 1;
                          }
                          $ky = 1;
                          foreach($planSessionData as $req){
                            $username =  $this->Menu_model->get_userbyid($req->user_id)[0]->name;
                          
                            $total_task = $this->Menu_model->TotalTaskBetweenTime($req->user_id,$adate,$req->pstime,$req->pctime);
                            $total_taskcnt = sizeof($total_task);




                            // // Convert total time to seconds
                            // $totaltime = $req->totaltime;
                            // list($hours, $minutes, $seconds) = explode(":", $totaltime);
                            // $totaltime_in_seconds = $hours * 3600 + $minutes * 60 + $seconds;
                            // // Calculate average time per task
                            // $average_time_per_task = $totaltime_in_seconds / $total_taskcnt;
                            // // Convert average time back to hours, minutes, and seconds
                            // $average_hours = floor($average_time_per_task / 3600);
                            // $average_minutes = floor(($average_time_per_task % 3600) / 60);
                            // $average_seconds = round($average_time_per_task % 60);

                            $totaltime = (string)($req->totaltime ?? '00:00:00');

$timeParts = explode(':', $totaltime);

// Ensure proper HH:MM:SS format
$hours   = isset($timeParts[0]) ? (int)$timeParts[0] : 0;
$minutes = isset($timeParts[1]) ? (int)$timeParts[1] : 0;
$seconds = isset($timeParts[2]) ? (int)$timeParts[2] : 0;

// Convert total time into seconds
$totaltime_in_seconds = ($hours * 3600) + ($minutes * 60) + $seconds;

// Prevent division by zero
$total_taskcnt = (int)$total_taskcnt;

if ($total_taskcnt > 0) {

    // Calculate average time per task
    $average_time_per_task = $totaltime_in_seconds / $total_taskcnt;

    // Convert back to HH:MM:SS
    $average_hours = floor($average_time_per_task / 3600);
    $average_minutes = floor(($average_time_per_task % 3600) / 60);
    $average_seconds = floor($average_time_per_task % 60);

    $average_time = sprintf(
        '%02d:%02d:%02d',
        $average_hours,
        $average_minutes,
        $average_seconds
    );

} else {

    $average_time = '00:00:00';
}
                            
                            if($i==8){
                              $ky=1;
                            }
                            ?>
                        <tr class="cat<?=$ky;?>">
                          <td><?=$i; ?></td>
                          <td><?=$username ?></td>
                          <td><?=$req->psdatetime ?></td>
                          <td><?=$req->pstime ?></td>
                          <td><?=$req->pctime ?></td>
                          <td><?=$req->pcdatetime ?></td>
                          <td><?=$req->totaltime ?></td>
                          <td> <a href="<?=base_url();?>Menu/CheckPlanTaskBetweenTimes/<?=$req->pstime ?>/<?=$req->pctime ?>"> <?=$total_taskcnt?></a></td>
                          <td><?= sprintf("%02d:%02d:%02d", $average_hours, $average_minutes, $average_seconds); ?></td>
                        </tr>
                        <?php $i++; $ky++; } ?>
                      </tbody>
                    </table>
                  </div>
                </div>
                <div class="justify-content-center col-lg-4 col-sm-4" id="planningStart1">
                  <div class="card custom-card">
                    <div class="card-header custom-card-header">
                      <h5>Task Planner</h5>
                    </div>
                    <div class="card-body">
                      <div class="form-check" id="note">
                        <?php 
                          $dayStartFrom = getUserDayStartStatus($uid);
                          // var_dump($dayStartFrom[0]->wffo);
                          $wffo = $dayStartFrom[0]->wffo;
                          
                          if($wffo == 1){
                              $daystartedFrom = 'Office';
                          }elseif ($wffo == 2) {
                              $daystartedFrom = 'Field';
                          }else{
                              $daystartedFrom = 'Field + Office';
                          }
                          
                          ?>
                        <span><strong>** (You started you day from <span style="color:blue;"><?=$daystartedFrom?></span>. Filters will be available accordingly..!!)</strong></span>
                      </div>
                      <hr>

                      

                      <?php  
                        $current_date = date("Y-m-d");
                        $tomorrow_date = date('Y-m-d', strtotime($current_date . ' +1 day'));
                        if($adate == $tomorrow_date){
                        
                        $planchangetask = $this->Menu_model->GetTaskBecauseOfPlanChange($uid,$tomorrow_date);
                        $planchangetaskcnt = sizeof($planchangetask);
                        if($planchangetaskcnt > 0){
                          $cssct = 'red';
                          
                        ?>
                      <div class="form-check">
                        <label class="form-check-label custom-radio-label">
                        <input type="radio" class="form-check-input" name="optradio" value="Because of Plan Change" > 
                        <span style="<?='color:'.$cssct?>" data-toggle="tooltip" data-placement="left" title="This filter is activated due to a change in a plan. Only those tasks will appear that are to be rescheduled within the specified time frame."> Because of Plan Change (<?=$planchangetaskcnt ?>) </span>
                        </label>
                      </div>
                      <?php }} ?>
                      <?php 
                        $getPendingTask = $this->Menu_model->get_PendingTask($uid);
                        $getoldPendingTask = $this->Menu_model->get_OLDPendingTask($uid);
                        $getmomTask = $this->Menu_model->getTaskAfterMomApproved($uid);
                        $getmomTaskcnt = sizeof($getmomTask);
                        
                        $getpendSize = sizeof($getPendingTask);
                        $getoldPendingTaskcnt = sizeof($getoldPendingTask);
                        // $reviewtask  = $this->Menu_model->GetTommrowReviewTask($uid);
                        $reviewtask    = [];
                        $reviewtaskcnt = sizeof($reviewtask);

                        // $mandatory_task     = $this->Menu_model->AllMandatoryRestrictionCompanywithuid($uid,$adate);
                        $mandatory_task     = [];
                        $mandatory_task_cnt = sizeof($mandatory_task);
                        $mandatory_task_cnt = 0;
                        // $mandatory_task_cnt = 1;
                        
                        // $reviews = $this->Menu_model->GetPendingReviewForPlan($uid,$adate);
                        $reviews = [];
                        $filtered_reviews = array_filter($reviews, function($review) {
                            return $review->review_count == 0;
                        });
                        $reviewCount = sizeof($filtered_reviews);
                        // $planbutnotinitedcnt = 0;
                        // $reviewtaskcnt = 0;


                        // $reviewCount = 0;
                        $curudetail = $this->Menu_model->get_userbyid($uid);
                        $admid      = $curudetail[0]->admin_id;
                     
                        $reviewCount = 0;
                     
                       
                          
                        $tomAssigntask  = $this->Menu_model->GetTommrowAssignedTask($uid);
                        $tomAssigntaskcnt = sizeof($tomAssigntask); 
                        
                        if($tomAssigntaskcnt > 0){
                            if($adate !== date("Y-m-d")){
                            ?>
                      <div class="form-check" id="review_target_date_filter">
                        <label class="form-check-label custom-radio-label">
                        <input type="radio" class="form-check-input" name="optradio" value="Assign Task" > <span style="color:navy;"> Assign Task <?= '('.$tomAssigntaskcnt.')' ?></span>
                        </label>
                      </div>
                      <?php }} 
                          $curudetail = $this->Menu_model->get_userbyid($uid);
                          $admid      = $curudetail[0]->admin_id;

                      ?>
                      <?php if($planbutnotinitedcnt > 0 && $adate !== date("Y-m-d")){ ?>

                        <?php
                        $task_request_type  = 'Plan But Not Initiated';
$plandate = date("Y-m-d");
                        $getPBNI            = $this->Menu_model->GetCreatePlannerRequestByUser($uid,$task_request_type,date("Y-m-d"));
                        $getPBNI_cnt        = sizeof($getPBNI);

                        $getPBNI_approved   = $getPBNI[0]->approved;
                        if($getPBNI_cnt > 0 && $getPBNI_approved == 0 || $getPBNI_approved == 2){ 
                          $getPBNI_request_id       = $getPBNI[0]->id;
                          $getPBNI_request_type       = $getPBNI[0]->request_type;
                          $getPBNI_approved           = $getPBNI[0]->approved;
                          $getPBNI_request_date       = $getPBNI[0]->request_date;
                          $getPBNI_created_at         = $getPBNI[0]->created_at;
                          $getPBNI_task_count         = $getPBNI[0]->task_count;
                          $getPBNI_request_remarks    = $getPBNI[0]->request_remarks;
                          $getPBNI_approved_by        = $getPBNI[0]->approved_by;
                          $getPBNI_approved_date      = $getPBNI[0]->approved_date;
                          $getPBNI_request_user_id    = $getPBNI[0]->request_user_id;
                          $getPBNI_approved_message   = $getPBNI[0]->approved_message;

                           ?>
                           <hr>
                        <div class="card p-2">
                        <label class="text-center text-danger">* You still have a total of <?=$getpendSize;?> pending tasks.</label>
                          <table class="table" style="font-size: 13px;">
                            <tr class="table-primary">
                              <td><strong>Request BY -</strong></td>
                              <td><?= $this->Menu_model->get_userbyid($getPBNI_request_user_id)[0]->name;?></td>
                            </tr>
                            <tr class="table-primary">
                              <td><strong>Request Date -</strong></td>
                              <td><?=$getPBNI_created_at;?></td>
                            </tr>
                            <tr class="table-secondary">
                              <td><strong>Request Type -</strong></td>
                              <td><?=$getPBNI_request_type;?></td>
                            </tr>
                            <tr class="table-success">
                              <td><strong>Task Count for Planning at Request Time -</strong></td>
                              <td><?=$getPBNI_task_count;?> Task</td>
                            </tr>
                            <tr class="table-success">
                              <td><strong>Check Task -</strong></td>
                              <td>
                              <a href="<?=base_url().'Menu/CheckUserPendingTaskList/'.$getPBNI_request_id.'/'.$getPBNI_request_user_id.'/'.$getPBNI_request_date?>" target="_BLANK"><?=$getpendSize;?> Task</a>
                              </td>
                            </tr>
                            <tr class="table-info">
                              <td><strong>Request Remarks -</strong></td>
                              <td><?=$getPBNI_request_remarks;?></td>
                            </tr>
                            <tr class="table-primary">
                              <td><strong>Request Status -</strong></td>
                              <td><?php 
                              if($getPBNI_approved == 0){echo "<span class='bg-warning p-1'>Pending</span>";}elseif($getPBNI_approved == 2){echo "<span class='bg-danger p-1'>Reject</span>";}
                              ?></td>
                            </tr>
                            <tr class="table-primary">
                              <td><strong>Send Reminder -</strong></td>
                              <td>
                              <span class="bg-primary p-1 create-a-reminder" data-toggle="modal" data-target="#exampleModalReminder" style="cursor: pointer;">
                              <i class="fa-solid fa-bell"></i> Send&nbsp;Reminder
                              </span>
                              </td>
                            </tr>
                            <?php if($getPBNI_approved == 2) { ?>
                              <tr class="table-primary">
                              <td><strong>Rejected By -</strong></td>
                              <td><?= $this->Menu_model->get_userbyid($getPBNI_approved_by)[0]->name;?></td>
                            </tr>
                              <tr class="table-primary">
                              <td><strong>Rejected Date -</strong></td>
                              <td><?= $getPBNI_approved_date;?></td>
                            </tr>
                              <tr class="table-primary">
                              <td><strong>Manager Message -</strong></td>
                              <td><?= $getPBNI_approved_message;?></td>
                            </tr>
                            <?php } ?>
                          </table>
                          <?php 
                          if($getPBNI_approved == 0){?>
                            <label class="text-center text-danger">* Please wait for approval; otherwise, complete your pending tasks from the calendar page.</label>
                          <?php }else if($getPBNI_approved == 2){ ?>
                            <label class="text-center text-danger">* Your manager has rejected your request. Please complete your pending tasks on the calendar page.</label>
                         <?php } ?>
                         
                        </div>
                       <?php }else if($getPBNI_cnt == 0){ ?>
                        <hr>
                         <form class="card text-center" method="post" action="<?=base_url().'Menu/CreatePlannerRequest'?>">
                            <div class="form-group">
                              <input type="hidden" name="request_type" value="Plan But Not Initiated">
                              <input type="hidden" name="oldPendTaskcnt" value="<?=$getpendSize;?>">
                              <input type="hidden" name="oldPendTask_date" value="<?=$adate;?>">
                              <label class="text-center text-danger">* You have total <?=$getpendSize;?> pending tasks. Create a request for approval to plan your tasks; otherwise, complete your pending tasks from the calendar page.</label>
                              <textarea class="form-control" rows="3" name="task_request_remarks" placeholder="* Type reson for approval to plan your old pending tasks." required></textarea>
                            </div>
                            <button type="submit" class="btn btn-primary mb-2" style="height: 35px;box-shadow: rgba(50, 50, 93, 0.25) 0px 30px 60px -12px inset, rgba(0, 0, 0, 0.3) 0px 18px 36px -18px inset;">Create Request</button>
                          </form>
                        <?php }else{?>
                          <div class="form-check">
                            <label class="form-check-label custom-radio-label">
                            <input type="radio" class="form-check-input" name="optradio" value="Plan But Not Initiated" >
                            <span style="color:red;" data-toggle="tooltip" data-placement="left" title="This filter is active due to If user have any Today's pending task (User Planned But Not Initiated) " >Today's Pending Task - Plan But Not Initiated (<?= $getpendSize; ?>)</span>
                            </label>
                        </div>
                      <?php } ?>
                      <?php } else{?>
                      <?php if($oldPendTaskcnt > 0){ 

                        $task_request_type  = 'Old Pending Task';
                        $plandate = date("Y-m-d");

                        $getOPTR            = $this->Menu_model->GetCreatePlannerRequestByUser($uid,$task_request_type,$plandate);
                        $getOPTR_cnt        = sizeof($getOPTR);
                        $getOPTR_approved             = $getOPTR[0]->approved;
                        if($getOPTR_cnt > 0 && $getOPTR_approved == 0 || $getOPTR_approved == 2){
                          $getOPTR_request_id         = $getOPTR[0]->id;
                          $getOPTR_request_type       = $getOPTR[0]->request_type;
                          $getOPTR_approved           = $getOPTR[0]->approved;
                          $getOPTR_request_date       = $getOPTR[0]->request_date;
                          $getOPTR_created_at         = $getOPTR[0]->created_at;
                          $getOPTR_task_count         = $getOPTR[0]->task_count;
                          $getOPTR_request_remarks    = $getOPTR[0]->request_remarks;
                          $getOPTR_approved_by        = $getOPTR[0]->approved_by;
                          $getOPTR_approved_date      = $getOPTR[0]->approved_date;
                          $getOPTR_request_user_id    = $getOPTR[0]->request_user_id;
                          $getOPTR_approved_message   = $getOPTR[0]->approved_message;
                           ?>
                           <hr>
                        <div class="card p-2">
                        <label class="text-center text-danger">* You still have a total of <?=$oldPendTaskcnt;?> old pending tasks.</label>
                          <table class="table" style="font-size: 13px;">
                            <tr class="table-primary">
                              <td><strong>Request BY -</strong></td>
                              <td><?= $this->Menu_model->get_userbyid($getOPTR_request_user_id)[0]->name;?></td>
                            </tr>
                            <tr class="table-primary">
                              <td><strong>Request Date -</strong></td>
                              <td><?=$getOPTR_created_at;?></td>
                            </tr>
                            <tr class="table-secondary">
                              <td><strong>Request Type -</strong></td>
                              <td><?=$getOPTR_request_type;?></td>
                            </tr>
                            <tr class="table-success">
                              <td><strong>Task Count for Planning at Request Time -</strong></td>
                              <td><?=$getOPTR_task_count;?> Task</td>
                            </tr>
                            <tr class="table-success">
                              <td><strong>Check Task -</strong></td>
                              <td>
                              <a href="<?=base_url().'Menu/CheckUserPendingTaskList/'.$getOPTR_request_id.'/'.$getOPTR_request_user_id.'/'.$getOPTR_request_date?>" target="_BLANK"><?=$oldPendTaskcnt;?> Task</a>
                              </td>
                            </tr>
                            <tr class="table-info">
                              <td><strong>Request Remarks -</strong></td>
                              <td><?=$getOPTR_request_remarks;?></td>
                            </tr>
                            <tr class="table-primary">
                              <td><strong>Request Status -</strong></td>
                              <td><?php 
                              if($getOPTR_approved == 0){echo "<span class='bg-warning p-1'>Pending</span>";}elseif($getOPTR_approved == 2){echo "<span class='bg-danger p-1'>Reject</span>";}
                              ?></td>
                            </tr>
                            <tr class="table-primary">
                              <td><strong>Send Reminder -</strong></td>
                              <td>
                              <span class="bg-primary p-1 create-a-reminder" data-toggle="modal" data-target="#exampleModalReminder" style="cursor: pointer;">
                              <i class="fa-solid fa-bell"></i> Send&nbsp;Reminder
                              </span>
                              </td>
                            </tr>
                            <?php if($getOPTR_approved == 2) { ?>
                              <tr class="table-primary">
                              <td><strong>Rejected By -</strong></td>
                              <td><?= $this->Menu_model->get_userbyid($getOPTR_approved_by)[0]->name;?></td>
                            </tr>
                              <tr class="table-primary">
                              <td><strong>Rejected Date -</strong></td>
                              <td><?= $getOPTR_approved_date;?></td>
                            </tr>
                            </tr>
                              <tr class="table-primary">
                              <td><strong>Rejected Message -</strong></td>
                              <td><?= $getOPTR_approved_message;?></td>
                            </tr>
                            <?php } ?>
                          </table>
                          <?php 
                          if($getOPTR_approved == 0){?>
                            <label class="text-center text-danger">* Please wait for approval.</label>
                          <?php }else if($getOPTR_approved == 2){ ?>
                            <label class="text-center text-danger">* Your manager has rejected your request.</label>
                         <?php } ?>
                        </div>
                       <?php }else if($getOPTR_cnt == 0){ ?>
                        <hr>
                         <form class="card text-center" method="post" action="<?=base_url().'Menu/CreatePlannerRequest'?>">
                            <div class="form-group">
                              <input type="hidden" name="request_type" value="Old Pending Task">
                              <input type="hidden" name="oldPendTaskcnt" value="<?=$oldPendTaskcnt;?>">
                              <input type="hidden" name="oldPendTask_date" value="<?=$adate;?>">
                              <label class="text-center text-danger">* You have total <?=$oldPendTaskcnt;?> old pending tasks. Create a request for approval to plan your tasks.</label>
                              <textarea class="form-control" rows="3" name="task_request_remarks" placeholder="* Type reson for approval to plan your old pending tasks." required></textarea>
                            </div>
                            <button type="submit" class="btn btn-primary mb-2" style="height: 35px;box-shadow: rgba(50, 50, 93, 0.25) 0px 30px 60px -12px inset, rgba(0, 0, 0, 0.3) 0px 18px 36px -18px inset;">Create Request</button>
                          </form>
                        <?php }else{?>
                          <div class="form-check">
                            <label class="form-check-label custom-radio-label">
                            <input type="radio" class="form-check-input" name="optradio" value="Plan But Not Initiated Old">
                            <span style="color:red;" data-toggle="tooltip" data-placement="left" title="This filter is active due to If user have any old pending task (User Planned But Not Completed) ">Old Pending Task (<?= $getoldPendingTaskcnt; ?>)</span>
                            </label>
                          </div>
                      <?php } ?>

                      <?php }else{ ?>

                        <div class="form-check" id="company_name_filter">
                          <label class="form-check-label custom-radio-label">
                            <input type="radio" class="form-check-input" name="optradio" value="Compnay Name" >Company Name
                          </label>
                        </div>

                        <!-- <div class="form-check" id="quater_strategy">
                          <label class="form-check-label custom-radio-label">
                            <input type="radio" class="form-check-input" name="optradio" value="Reserach New School"> <span>Reserach New School</span> 
                          </label>
                        </div> -->
                        <div class="form-check" id="quater_strategy">
                          <label class="form-check-label custom-radio-label">
                            <input type="radio" class="form-check-input" name="optradio" value="Call On School"> <span>Call On School</span> 
                          </label>
                        </div>

          
                     <?php } }?>




              
                      
                      <hr>
                      <p id="meetingrelmsg"></p>
                      <div class="card-header boxshadownew text-center">
                        <b>Let's Start Creating Task for <span id="tasktype"></span></b>
                        <!-- <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#exampleModalCenter">
                          <b>Create a Special Request For Plan Change </b>
                          </button> -->
                      </div>
                    </div>
                  </div>
                </div>
                <!-- Modal -->
                <div class="modal fade" id="exampleModalCenter" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
                  <div class="modal-dialog modal-dialog-centered" role="document">
                    <form method="post" action="<?=base_url();?>Menu/SpecialRequestForLeave">
                      <div class="was-validated">
                        <div class="modal-content">
                          <div class="modal-header" styel="background: #fbff00;" >
                            <h5 class="modal-title" id="exampleModalLongTitle">Special Request For Plan Change</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                            </button>
                          </div>
                          <div class="modal-body" style="background: darkslategrey;color: white;" >
                            <input type="hidden" id="pdate" value="<?=$adate?>" name="pdate" required=""> 
                            <lable>Todays Start Time : </lable>
                            <input type="time" id="meetingtimerequest1" name="start_meeting_time" min="10:00" max="19:00" class="form-control" required=""> 
                            <lable>Todays End Time : </lable>
                            <input type="time" id="meetingtimerequest2" name="end_meeting_time" min="10:00" max="19:00" class="form-control" required=""> 
                            <hr>
                            <div id="taskcounttable">
                            </div>
                            <hr>
                            <lable>Tomorrow Start Time : </lable>
                            <input type="time" id="meetingtimerequest3" name="start_tommorow_task_time" min="10:00" max="19:00" class="form-control" required=""> 
                            <hr>
                            <lable>Purpose For Plan Change : </lable>
                            <textarea name="purpose" class="form-control" placeholder="Please Enter Purpose" required="" ></textarea>
                          </div>
                          <div class="modal-footer text-center" style="background: #2f4f4f;" >
                            <center>
                              <button class="btn btn-primary m-3" type="submit">Send Request For Approval</button>
                            </center>
                          </div>
                        </div>
                      </div>
                    </form>
                  </div>
                </div>
                <div class="col-lg-4 col-sm-4" id="planningStart2" >



                  <div class="card p-4 taskselectionarea">
                    <div id="actionPlanned" class ="card p-2">
                      <div id="becauseof_topsender_pst_meet" class="p-2">
                        <div class="form-group">
                          <label>Planning for own / other  funnel</label>
                          <select id="planning_funnel" class="form-control">
                            <option value="">Select funnel</option>
                            <option value="Own funnel">own funnel</option>
                            <option value="Other funnel">Other funnel</option>
                          </select>
                        </div>
                        <div class="form-group" id="becauseofTPM_TaskDatacard">
                          <label>Select On </label>
                          <select id="becauseofTPM_TaskData" class="form-control">
                            <option value="">Select On</option>
                            <option value="On BD">ON BD</option>
                            <option value="On PST">ON PST</option>
                            <option value="both">Both</option>
                          </select>
                        </div>
                        <div class="form-group" id="becauseofTPM_TaskDatacard_new">
                          <label>Because Of </label>
                          <select id="becauseofTPM_new" class="form-control">
                            <option value="">Select Because Of</option>
                            <option value="Top Spender">Top Spender</option>
                            <option value="Meeting">Meeting</option>
                          </select>
                        </div>
                      </div>
                      <div id="selectcompanyname" class="form-group">
                        <lable>Enter Company Name Or CID</lable>
                        <?php $allCmpData = $this->Menu_model->GetAllCompanyByUserID($uid); ?>
                        <input type="search" class="form-control" class="search" id="search_company" placeholder="Search" list="data">
                        <datalist id="data">
                          <?php foreach($allCmpData as $cmp){ ?>
                          <option value="<?=$cmp->com_id?> - <?= $cmp->compname?>" />
                            <?php } ?>
                        </datalist>
                      </div>
                      <?php 
                        $allStatus = $this->Menu_model->get_status();
                        ?>
                      <input type="hidden" name="selectbyuser" id="selectbyuser" value="">
                      <div class="form-group" id="selectstatus" >
                      <lable class="text-left">Select Company Status : </lable>
                      <select class="form-control" id="selectstatusbyuser">
                      <option selected disabled>Select Status</option>
                      <option value="all">All</option>
                      <?php foreach($allStatus as $getstatus): ?>
                      <option value="<?= $getstatus->id ?>"><?= $getstatus->name ?></option>
                      <?php endforeach; ?>
                      </select>
                      </div>
                      <hr>
                      <select id="tasktaction" name="tasktaction" class="form-control" >
                        <option selected disabled >Select Previous Action Taken</option>
                        <option value="all">All</option>
                        <?php
                          foreach($action as $a){if($a->id!=4 && $a->id!=6 && $a->id!=8 && $a->id!=9 && $a->id!=11 && $a->id!=12){
                          ?>
                        <option value="<?=$a->id?>"><?=$a->name?></option>
                        <?php }} ?>
                      </select>
                      <hr>
                      <!-- <div class="form-group" id="select_past_question"> -->
                        <!-- <lable>Select Past Action</lable> -->
                      <select class="form-control" id="taskActionbyuser">
                      <option selected disabled>Select Previous Action Taken</option>
                        <option value="yes">Yes</option>
                        <option value="no">No</option>
                      </select>
                      <!-- </div> -->
                      <hr>
                      <select class="form-control" id="taskPurposebyuser">
                      <option selected disabled>Select Previous Task Action Purpose</option>
                        <option value="yes">Yes</option>
                        <option value="no">No</option>
                      </select>
                      <hr>
                      <select class="form-control" id="slct_days_status">
                        <option selected disabled>Select Days</option>
                        <option value="1-3">1-3 Days</option>
                        <option value="3-5">3-5 Days</option>
                        <option value="5-7">5-7 Days</option>
                      </select>
                    </div>
                  </div>







                  <div id="actionnotplaned" class="card p-4" >
                    <input type="hidden" name="selectbyuser" id="selectbyuser" value=""> 
                    <div class="form-group" id="selectstatus" >
                      <lable>Select Company Status : </lable>
                      <hr>
                      <select class="form-control" id="selectstatusbyusernotplaned">
                        <option selected disabled>Select Status</option>
                        <option value="all">All</option>
                        <?php foreach($allStatus as $getstatus): ?>
                        <option value="<?= $getstatus->id ?>"><?= $getstatus->name ?></option>
                        <?php endforeach; ?>
                      </select>
                    </div>
                    <div id="task_actionCard">
                      <div class="form-group">
                        <label>Task/Action</label>
                        <select id="task_action" class="form-control" name="task_action">
                          <option value="">Select Task</option>
                          <option value="all">All</option>
                          <?php
                            foreach($action as $a){
                            ?>
                          <option value="<?=$a->id?>"><?=$a->name?></option>
                          <?php } ?>
                        </select>
                      </div>
                    </div>
                    <div class="form-group" id="daysfiltercard_anp">
                      <label>Days</label>
                      <select id="daysfiltercard_anp_date" class="form-control" name="days">
                        <option value="">Select Days</option>
                        <option value="all">All </option>
                        <option value="8">8 days </option>
                        <option value="15">15 days </option>
                        <option value="more">more than 15 days </option>
                      </select>
                    </div>
                  </div>










                  <div id="actionnotplaned_NeedYour" class="card p-4">
                    <lable>Select Company Status : </lable>
                    <hr>
                    <select class="form-control" id="selectstatusbyusernotplanedCompany">
                      <option value="">Select Task</option>
                      <?php 
                        $getBDNoWorkedcnts = [];
                          foreach ($getBDNoWorked as $objects) {
                              $cstatus = $objects->cstatus;
                              if (!isset($getBDNoWorkedcnts[$cstatus])) {
                                  $getBDNoWorkedcnts[$cstatus] = [];
                              }
                              $getBDNoWorkedcnts[$cstatus][] = $objects;
                          }
                          $getBDNoWorkedcnts_wdlnl = [];
                          foreach ($getBDNoWorked_wdl_nl as $objects_wdl_nl) {
                              $cstatus = $objects_wdl_nl->cstatus;
                              if (!isset($getBDNoWorkedcnts_wdlnl[$cstatus])) {
                                  $getBDNoWorkedcnts_wdlnl[$cstatus] = [];
                              }
                              $getBDNoWorkedcnts_wdlnl[$cstatus][] = $objects_wdl_nl;
                          }
                        ?>
                      <?php 
                        foreach($getBDNoWorkedcnts as $key => $npstatus){
                          $getstatus_name = $this->Menu_model->get_statusbyid($key)[0]->name;
                          $npstatuscnt = sizeof($npstatus);
                          echo "<option class='text-danger' value='$key'>$getstatus_name($npstatuscnt)</options>";
                        }
                        foreach($getBDNoWorkedcnts_wdlnl as $key => $npstatus){
                          $getstatus_name = $this->Menu_model->get_statusbyid($key)[0]->name;
                          $npstatuscnt = sizeof($npstatus);
                          echo "<option class='text-danger' value='$key'>$getstatus_name($npstatuscnt)</options>";
                        }
                        ?>
                    </select>
                  </div>











                  <div id="need_your_attention" class="card p-4">
                    <lable>Select Company Status : </lable>
                    <hr>
                    <select class="form-control" id="need_your_attention_slsct">
                      <option value="">Select Task</option>
                      <?php 
                        $needyouratte = [];
                          foreach ($need_your_atte as $objects) {
                              $cstatus = $objects->cstatus;
                              if (!isset($needyouratte[$cstatus])) {
                                  $needyouratte[$cstatus] = [];
                              }
                              $needyouratte[$cstatus][] = $objects;
                          }
                        ?>
                      <?php 
                        foreach($needyouratte as $key => $npstatus){
                          $getstatus_name = $this->Menu_model->get_statusbyid($key)[0]->name;
                          $npstatuscnt = sizeof($npstatus);
                          echo "<option class='text-danger' value='$key'>$getstatus_name($npstatuscnt)</options>";
                        }
                        ?>
                    </select>
                  </div>






                  <div id="selectCategory" class="card p-4" >
                    <input type="hidden" name="selectbyuser" id="selectbyuser" value=""> 
                    <div class="form-group" id="selectCategorybyuser" >
                      <lable>Select Category : </lable>
                      <hr>
                      <select class="form-control" id="selectdcategory">
                        <option selected disabled >Choose one</option>
                        <option value="topspender">Top Spender</option>
                        <option value="upsell_client">Upsell Client</option>
                        <option value="focus_funnel">Focus Funnel</option>
                        <!-- <option value="keycompany">Key Company</option> -->
                        <option value="pkclient">P Key Client</option>
                      </select>
                      <small><span id="slcategorycompanys"></span></small>
                    </div>
                    <div class="form-group" id="statusfiltercardCategory">
                      <label>Select Status</label>
                      <select class="form-control" id="statusfiltercardCat">
                        <option selected disabled>Select Status</option>
                        <option value="all">All</option>
                        <?php foreach($allStatus as $getstatus): ?>
                        <option value="<?= $getstatus->id ?>"><?= $getstatus->name ?></option>
                        <?php endforeach; ?>
                      </select>
                    </div>
                    <div class="form-group" id="taskActionbyusercatcatcard">
                      <label>Select Previous Action Taken</label>
                      <select class="form-control" id="taskActionbyusercat">
                        <option selected disabled>Previous Action Taken</option>
                        <option value="yes">Yes</option>
                        <option value="no">No</option>
                      </select>
                    </div>
                    <div class="form-group" id="taskPurposebyusercatcard">
                      <label>Select Previous Task Action Purpose</label>
                      <select class="form-control" id="taskPurposebyusercatdata">
                        <option selected disabled>Previous Action Purpose</option>
                        <option value="yes">Yes</option>
                        <option value="no">No</option>
                      </select>
                    </div>
                    <div class="form-group" id="taskPurposebyusercatcard_days">
                      <label>Select Days</label>
                      <select class="form-control" id="taskPurposebyusercatdata_days">
                        <option selected disabled>Select Days</option>
                        <option value="1-3">1-3 Days</option>
                        <option value="3-5">3-5 Days</option>
                        <option value="5-7">5-7 Days</option>
                      </select>
                    </div>
                  </div>







                  <div id="clusterLocactionFiltercard" class="card p-4" >
                    <input type="hidden" name="selectbyuser" id="selectbyuser" value=""> 
                    <div class="form-group" id="selectCategorybyuser" >
                      <lable>Select Cluster Location : </lable>
                      <hr>
                      <select class="form-control" id="clusterNameLocaction"></select>
                    </div>
                    <div class="form-group" id="statusfiltercardCluster">
                      <label>Select Status</label>
                      <select class="form-control" id="statusfilterCluster">
                        <option selected disabled>Select Status</option>
                        <option value="all">All</option>
                        <?php 
                          foreach($allStatus as $getstatus):
                           ?>
                        <option value="<?= $getstatus->id ?>"><?= $getstatus->name ?></option>
                        <?php
                          endforeach;
                           ?>
                      </select>
                    </div>
                    <div class="form-group" id="taskActionbyuserCluster">
                      <label>Select Previous Action Taken</label>
                      <select class="form-control" id="taskActionbyCluster">
                        <option selected disabled>Previous Action Taken</option>
                        <option value="yes">Yes</option>
                        <option value="no">No</option>
                      </select>
                    </div>
                    <div class="form-group" id="taskPurposebyuserCluster">
                      <label>Select Previous Task Action Purpose</label>
                      <select class="form-control" id="taskPurposebyCluster">
                        <option selected disabled>Previous Action Purpose</option>
                        <option value="yes">Yes</option>
                        <option value="no">No</option>
                      </select>
                    </div>
                    <div class="form-group" id="taskPurposebyuserCluster_days">
                      <label>Select Days</label>
                      <select class="form-control" id="taskPurposebyCluster_days">
                        <option selected disabled>Select Days</option>
                        <option value="1-3">1-3 Days</option>
                        <option value="3-5">3-5 Days</option>
                        <option value="5-7">5-7 Days</option>
                      </select>
                    </div>
                  </div>







                  <div id="companyLocationdatacard" class="card p-4" >
                    <input type="hidden" name="selectbyuser" id="selectbyuser" value=""> 
                    <div class="form-group" id="companyLocationdata" >
                      <label>Select Compnay</label>
                      <select class="form-control" id="companyLocation"></select>
                    </div>
                    <div class="form-group" id="selectactionplanecard" >
                      <label>Select Action</label>
                      <select class="form-control" id="selectactionplane">
                        <option selected value="">Select Action </option>
                        <!-- <option value="actionplaned">Action Planed</option> -->
                        <option value="actionnotplaned">Action not Planed</option>
                      </select>
                    </div>
                    <div class="form-group" id="daysfiltercard">
                      <label>Days</label>
                      <select id="daysfilter" class="form-control" name="days">
                        <option value="">All </option>
                        <option value="8">8 days </option>
                        <option value="15">15 days </option>
                        <option value="more">more than 15 days </option>
                      </select>
                    </div>
                  </div>









                  <div id="pstAssignCard" class="card p-4">
                    <!-- <h5>PST Assign Company</h5> -->
                    <hr>
                    <div class="form-group" id="pstAssignCarddiv" >
                      <label>Assign Company</label>
                      <select class="form-control" id="pstAssignCardData">
                        <option selected value="">Select One </option>
                        <option value="pst_assign">PST Assign</option>
                        <option value="other_assign">Cluster Assign</option>
                        <option value="admin_assign">Admin Assign</option>
                      </select>
                    </div>
                  </div>








                  <div id="taskActionCard" class="card p-4">
                    <div class="form-group" id="taskaction_card_area">
                      <div class="form-group">
                        <label>Task/Action</label>
                        <select id="task_action_filter" class="form-control" name="task_action_filter">
                          <option value="">Select Task</option>
                          <option value="all">All</option>
                          <?php foreach($action as $a){if($a->id!=9 && $a->id!=15 && $a->id!=6 && $a->id!=8){ ?>
                          <option value="<?=$a->id?>"><?=$a->name?></option>
                          <?php }} ?>
                        </select>
                      </div>
                    </div>
                    <div id="selectbarginCompanyType" class="form-group">
                      <select id="bcytpe" name="bcytpe" class="form-control mt-2">
                        <option value="">Select Bargin Company Type</option>
                        <option value="From Funnel">From Funnel Bargin</option>
                        <option value="Other">Other Bargin (New Bargin Meeting )</option>
                      </select>
                    </div>

             
                    <div id="bargmeetingcluster" class="form-group">
                    <hr>
                        <div class="form-group" id="status_taskaction_card_for_smeeting" >
                          <lable>Type of Travel : </lable>
                          <select class="form-control" id="travelType_taskaction_slct_for_smeeting">
                            <option value="" Selected disabled>Select Location</option>
                            <option value="base">Base Location</option>
                            <option value="outStation">Out Station</option>
                          </select>
                      </div>
                    <hr>   
                    <label>Select Cluster</label>
                      <select id="bargmeetingcluster_slct" name="bargmeetingcluster" class="form-control mt-2">
                      </select>
                      <hr>
                        <div class="form-group" id="status_taskaction_card_for_smeeting" >
                          <lable>Select Status : </lable>
                          <select class="form-control" id="status_taskaction_slct_for_smeeting">
                            <option value="" selected disabled>Select Status</option>
                            <option value="all">All</option>
                            <?php foreach($allStatus as $getstatus):
                              if($getstatus->id == 1){continue;}
                              ?>
                            <option value="<?= $getstatus->id ?>"><?= $getstatus->name ?></option>
                            <?php endforeach; ?>
                          </select>
                      </div>
                      <?php if(in_array($user['type_id'],[4,13,19,20,21,22,23,24])): ?>
                      <hr>
                      <?php $ptotalTeams     = $this->Menu_model->GetTotalTeam($uid); ?>
                        <div class="form-group" id="bd_taskaction_card_for_smeeting" >
                          <lable>Select BD: </lable>
                          <select class="form-control" id="bd_taskaction_slct_for_smeeting">
                            <option value="" Selected disabled>Select BD</option>
                            <option value="all">All</option>
                            <?php foreach($ptotalTeams as $ptotalTeam):
                              // if($ptotalTeam->user_id == $user['user_id']){continue;}
                               ?>
                            <option value="<?=$ptotalTeam->user_id;?>"><?=$ptotalTeam->name;?></option>
                            <?php endforeach; ?>
                          </select>
                      </div>
                      <?php endif; ?>
                      <hr>
                      <div class="form-group" id="new_category_filter_card_cluster">
                        <label>New Category</label>
                        <select id="new_category_filter_select_cluster" class="form-control" name="new_category_filter_select_cluster">
                        <option value="">Select New Category</option>
                        <option value="Q1 20 Closure Funnel">Q1 20 Closure Funnel</option>
                        <option value="Potential Funnel For FY">Potential Funnel For FY</option>
                        <option value="To be Nurtured for FY">To be Nurtured for FY</option>
                        <option value="50 New Lead Funnel">50 New Lead Funnel</option>
                        <option value="BD Marked Business Potential">Marked Business Potential</option>
                        <option value="Anchor Clients">Anchor Clients</option>
                        </select>
                        </div>
                    </div>

                    <div id="selectReseachCompanyType" class="form-group">
                      <select id="researchType" name="researchType" class="form-control mt-2">
                        <option value="">Select Research Type</option>
                        <option value="From Funnel">From Funnel Research</option>
                        <option value="Other">Other Research (New Research)</option>
                      </select>
                    </div>
                    <div class="form-group" id="status_taskaction_card" >
                      <lable>Select Company Status : </lable>
                      <hr>
                      <select class="form-control" id="status_taskaction">
                        <option selected disabled>Select Status</option>
                        <option value="all">All</option>
                        <?php foreach($allStatus as $getstatus): ?>
                        <option value="<?= $getstatus->id ?>"><?= $getstatus->name ?></option>
                        <?php endforeach; ?>
                      </select>
                    </div>
                    <div class="form-group" id="taskActionbyuserCard">
                      <label>Select Previous Action Taken</label>
                      <select class="form-control" id="taskActionbyuserCardData">
                      <option selected disabled>Previous Action Taken</option>
                        <option value="yes">Yes</option>
                        <option value="no">No</option>
                      </select>
                    </div>
                    <div class="form-group" id="taskPurposebyuserCard">
                      <label>Select Previous Task Action Purpose</label>
                      <select class="form-control" id="taskPurposebyuserCardData">
                      <option selected disabled>Previous Action Purpose</option>
                        <option value="yes">Yes</option>
                        <option value="no">No</option>
                      </select>
                    </div>
                    <div class="form-group" id="taskPurposebyuserCard_days">
                      <label>Select Days</label>
                      <select class="form-control" id="taskPurposebyuserCardData_days">
                        <option selected disabled>Select Days</option>
                        <option value="1-3">1-3 Days</option>
                        <option value="3-5">3-5 Days</option>
                        <option value="5-7">5-7 Days</option>
                      </select>
                    </div>
                    <div id="partnertype" class="card p-4">
                      <div class="form-group">
                        <label>Partner Type</label>
                        <select id="partnertype_select" class="form-control" name="partnertype_select">
                          <option value="">Select Partner Type</option>
                          <option value="all">All</option>
                          <?php $get_partner = $this->Menu_model->get_partner();
                            foreach($get_partner as $a){
                            ?>
                          <option value="<?=$a->id?>"><?=$a->name?></option>
                          <?php } ?>
                        </select>
                      </div>
                      <div class="form-group" id="partnertype_cstatus" >
                        <lable class="text-left">Select Company Status : </lable>
                        <select class="form-control" id="partnertype_cstatusData">
                          <option selected disabled>Select Status</option>
                          <?php foreach($allStatus as $getstatus): ?>
                          <option value="<?= $getstatus->id ?>"><?= $getstatus->name ?></option>
                          <?php endforeach; ?>
                        </select>
                      </div>
                      <div id="partnertype_task">
                        <div class="form-group">
                          <label>Task/Action</label>
                          <select id="partnertype_taskData" class="form-control" name="task_action">
                            <option value="">Select Task</option>
                            <option value="all">All</option>
                            <?php foreach($action as $a){ ?>
                            <option value="<?=$a->id?>"><?=$a->name?></option>
                            <?php } ?>
                          </select>
                        </div>
                      </div>
                      <div class="form-group" id="partnertype_taskAction">
                        <label>Select Previous Action Taken</label>
                        <select class="form-control" id="partnertype_taskActionData">
                          <option selected disabled>Previous Action Taken</option>
                          <option value="yes">Yes</option>
                          <option value="no">No</option>
                        </select>
                      </div>
                      <div class="form-group" id="partnertype_taskPurpose">
                        <label>Select Previous Task Action Purpose</label>
                        <select class="form-control" id="partnertype_taskPurposeData">
                          <option selected disabled>Previous Action Purpose</option>
                          <option value="yes">Yes</option>
                          <option value="no">No</option>
                        </select>
                      </div>
                      <div class="form-group" id="partnertype_taskPurpose_days">
                        <label>Select Days</label>
                        <select class="form-control" id="partnertype_taskPurposeData_days">
                          <option selected disabled>Select Days</option>
                          <option value="1-3">1-3 Days</option>
                          <option value="3-5">3-5 Days</option>
                          <option value="5-7">5-7 Days</option>
                        </select>
                      </div>
                    </div>
                    <div id="sameStatusLastLimitDays" class="card p-4" >
                      <div class="form-group" id="samestatuslast15days">
                        <lable class="text-left">Select Company Status : </lable>
                        <hr>
                        <select class="form-control" id="samestatuslast15daysData">
                          <option selected disabled>Select Status</option>
                          <option value="all">All</option>
                          <?php foreach($allStatus as $getstatus): ?>
                          <option value="<?= $getstatus->id ?>"><?= $getstatus->name ?></option>
                          <?php endforeach; ?>
                        </select>
                      </div>
                      <div class="form-group" id="partnertype_planbut" >
                        <label>Partner Type</label>
                        <select id="partnertype_planbutData" class="form-control" name="partnertype_planbut">
                          <option value="">Select Partner Type</option>
                          <option value="all">All</option>
                          <?php $get_partner = $this->Menu_model->get_partner();
                            foreach($get_partner as $a){
                            ?>
                          <option value="<?=$a->id?>"><?=$a->name?></option>
                          <?php } ?>
                        </select>
                      </div>
                      <div class="form-group" id="daysfiltercard_planbut">
                        <label>Days</label>
                        <select id="daysfilter2_samedays" class="form-control" name="days">
                          <option value="">Select Days </option>
                          <option value="all">All </option>
                          <option value="8">8 days </option>
                          <option value="15">15 days </option>
                          <option value="more">more than 15 days </option>
                        </select>
                      </div>
                      <div class="form-group" id="planbut_taskActioncard">
                        <label>Select Previous Action Taken</label>
                        <select class="form-control" id="planbut_taskActionData">
                          <option selected disabled>Previous Action Taken</option>
                          <option value="yes">Yes</option>
                          <option value="no">No</option>
                        </select>
                      </div>
                      <div class="form-group" id="planbut_taskPurposecard">
                        <label>Select Previous Task Action Purpose</label>
                        <select class="form-control" id="planbut_taskPurposeData">
                          <option selected disabled>Previous Action Purpose</option>
                          <option value="yes">Yes</option>
                          <option value="no">No</option>
                        </select>
                      </div>
                    </div>
                    <div id="planbutnotinitiatedcard" class="card p-4" >
                      <div class="form-group">
                        <label>Task/Action</label>
                        <select id="planbutnoinit_TaskData" class="form-control" name="task_action">
                          <option value="">Select Task</option>
                          <?php 
                            $groupedByActionTypes = [];
                            foreach ($getPendingTask as $objects) {
                                $actionTypeId = $objects->actiontype_id;
                                if (!isset($groupedByActionTypes[$actionTypeId])) {
                                    $groupedByActionTypes[$actionTypeId] = [];
                                }
                                $groupedByActionTypes[$actionTypeId][] = $objects;
                            }
                            ?>
                          <?php 
                            foreach($groupedByActionTypes as $key => $petotaskData){
                              $getaction_name = $this->Menu_model->get_actionbyid($key)[0]->name;
                              $getaction_namecnts = sizeof($petotaskData);
                              echo "<option value='$key'>$getaction_name($getaction_namecnts)</options>";
                            }
                            ?>
                        </select>
                      </div>
                      <p class="p-2 text-white" id="plancomp"></p>
                    </div>
                    <div id="becauseofplanchange" class="card p-4" >
                      <div class="form-group">
                        <label>Task/Action</label>
                        <select id="planChange_TaskData" class="form-control" name="task_action">
                          <option value="">Select Task</option>
                          <?php 
                            $groupedByActionTypespc = [];
                            foreach ($planchangetask as $planchange) {
                                $actionTypeId = $planchange->actiontype_id;
                                if (!isset($groupedByActionTypespc[$actionTypeId])) {
                                    $groupedByActionTypespc[$actionTypeId] = [];
                                }
                                $groupedByActionTypespc[$actionTypeId][] = $planchange;
                            }
                            ?>
                          <?php 
                            foreach($groupedByActionTypespc as $key => $plan_change){
                              $getaction_name = $this->Menu_model->get_actionbyid($key)[0]->name;
                              $getaction_namecnts = sizeof($plan_change);
                              echo "<option value='$key'>$getaction_name($getaction_namecnts)</options>";
                            }
                            ?>
                        </select>
                      </div>
                    </div>
                    <div id="planbutnotinitiatedcardold" class="card p-4" >
                      <div class="form-group">
                        <label>Task/Action (Old Pending Task)</label>
                        <select id="planbutnoinit_TaskDataold" class="form-control" name="task_action">
                          <option value="">Select Task</option>
                          <?php 
                            $groupedByActionTypes = [];
                            foreach ($getoldPendingTask as $objects) {
                                $actionTypeId = $objects->actiontype_id;
                                if (!isset($groupedByActionTypes[$actionTypeId])) {
                                    $groupedByActionTypes[$actionTypeId] = [];
                                }
                                $groupedByActionTypes[$actionTypeId][] = $objects;
                            }
                            ?>
                          <?php 
                            foreach($groupedByActionTypes as $key => $petotaskData){
                              $getaction_name = $this->Menu_model->get_actionbyid($key)[0]->name;
                              $getaction_namecnts = sizeof($petotaskData);
                              echo "<option value='$key'>$getaction_name($getaction_namecnts)</options>";
                            }
                            ?>
                        </select>
                      </div>
                      <p class="p-2 text-white" id="plancompold"></p>
                    </div>
                  </div>


















                  <div id="firstQuarter1" class="card p-4" >
                    <div class="form-group" id="firstQuarter1cstatys" >
                      <lable class="text-left">Select Company Status : </lable>
                      <hr>
                      <select class="form-control" id="firstQuarter1cstatysData">
                        <option selected disabled>Select Status</option>
                        <option value="all">All</option>
                        <?php foreach($allStatus as $getstatus): ?>
                        <option value="<?= $getstatus->id ?>"><?= $getstatus->name ?></option>
                        <?php endforeach; ?>
                      </select>
                    </div>
                    <select id="firstQuarter1cstatysDataTask" name="" class="form-control">
                      <option selectedd disbaled value="" >Types of Task</option>
                      <option value="all">All</option>
                      <?php foreach($action as $a){if($a->id!=4 && $a->id!=6 && $a->id!=8 && $a->id!=9 && $a->id!=11 && $a->id!=12){ ?>
                      <option value="<?=$a->id?>"><?=$a->name?></option>
                      <?php }} ?>
                    </select>
                    <br>
                    <select class="form-control" id="firstQuarter1taskActionbyuser">
                      <option selected disabled>Action</option>
                      <option value="yes">Yes</option>
                      <option value="no">No</option>
                    </select>
                    <br>
                    <select class="form-control" id="firstQuarter1taskPurposebyuser">
                      <option selected disabled>Purpose</option>
                      <option value="yes">Yes</option>
                      <option value="no">No</option>
                    </select>
                  </div>











                  <div id="reviewTargetDate" class="card p-4">
                    <?php 
                      $reviewtask  = $this->Menu_model->GetTommrowReviewTask($uid);
                      $reviewtaskcnt = sizeof($reviewtask);
                      ?>
                    <div class="form-group" id="reviewTargetreviewtype">
                      <label>Review Task/Action </label>
                      <select id="reviewTargetreviewtypeData" class="form-control" name="task_action">
                        <option value="">Select Task</option>
                        <?php 
                          $groupedByActionTypes_rev = [];
                          foreach ($reviewtask as $objects) {
                              $actionTypeId = $objects->actiontype_id;
                              if (!isset($groupedByActionTypes_rev[$actionTypeId])) {
                                  $groupedByActionTypes_rev[$actionTypeId] = [];
                              }
                              $groupedByActionTypes_rev[$actionTypeId][] = $objects;
                          }
                          ?>
                        <?php 
                          foreach($groupedByActionTypes_rev as $key => $rev_totaskData){
                            $getaction_name = $this->Menu_model->get_actionbyid($key)[0]->name;
                            $getaction_namecnts = sizeof($rev_totaskData);
                            echo "<option value='$key'>$getaction_name($getaction_namecnts)</options>";
                          }
                          ?>
                      </select>
                    </div>
                    <!-- <div class="form-group" id="reviewTargetreviewtype" >
                      <select class="form-control" name="reviewtype" required="" id="reviewTargetreviewtypeData">
                      <option selected disabled>Select Review Time</option>
                          <option value="Self Weekly">Weekly</option>
                          <option value="Self Fortnightly">Fortnightly</option>
                          <option value="Self Monthly">Monthly</option>
                          <option value="Self Quarterly">Quarterly</option>
                      </select>
                      </div>
                      <div class="form-group" id="reviewTargetReviewSelf" >
                      <select class="form-control" name="reviewtype" required="" id="reviewTargetReviewSelfData">
                      <option selected disabled>Select Self / Other Review</option>
                          <option value="Self Review">Self Review</option>
                          <option value="Admin Review">Admin Review</option>
                          <option value="Cluster Review">Cluster Review</option>
                          <option value="PST Review">PST Review</option>
                      </select>
                      </div> -->
                  </div>












                  <div class="card p-4 form-group" id="tommarowAssignTaskype">
                    <label>Assign Task/Action </label>
                    <select id="tommarowAssignTaskypeData" class="form-control" name="task_action">
                      <option value="">Select Task</option>
                      <?php 
                        $tomAssigntask_data = [];
                        foreach ($tomAssigntask as $objects) {
                            $actionTypeId = $objects->actiontype_id;
                            if (!isset($tomAssigntask_data[$actionTypeId])) {
                                $tomAssigntask_data[$actionTypeId] = [];
                            }
                            $tomAssigntask_data[$actionTypeId][] = $objects;
                        }
                        ?>
                      <?php 
                        foreach($tomAssigntask_data as $key => $assign_totaskData){
                          $getaction_name = $this->Menu_model->get_actionbyid($key)[0]->name;
                          $getaction_namecnts = sizeof($assign_totaskData);
                          echo "<option value='$key'>$getaction_name($getaction_namecnts)</options>";
                        }
                        ?>
                    </select>
                  </div>








                  

                  <div class="card p-4 form-group" id="mandatory_task_card">
                          <label>Mandatory Task/Action </label>
                          <select id="mandatory_task_select" class="form-control" name="mandatory_task_card">
                            <option value="">Select Task</option>
                                <?php 
                                $groupedArrayActions = [];
                                foreach ($mandatory_task as $key => $value) {
                                    $groupedArrayActions[$value['task_actions']][] = $value;
                                }
                                ?>
                            <?php 
                              foreach($groupedArrayActions as $key => $mantask_totaskData){
                                $getaction_name = $this->Menu_model->get_actionbyid($key)[0]->name;
                                $getaction_namecnts = sizeof($mantask_totaskData);
                                echo "<option value='$key'>$getaction_name($getaction_namecnts)</options>";
                              }
                            ?>
                          </select>
                        </div>






 <div class="card p-4 form-group" id="new_category_filter_card" style="background: linear-gradient(to right, #fff8e1, #ffffff); border-radius: 12px; box-shadow: 0 4px 8px rgba(0,0,0,0.1); padding: 20px;min-height:200px;justify-content: center;">
                          <label>New Category</label>
                          <select id="new_category_filter_select" class="form-control" name="new_category_filter_select">
                            <option value="">Select New Category</option>
                            <option value="Q1 20 Closure Funnel">Q1 20 Closure Funnel</option>
                            <option value="Potential Funnel For FY">Potential Funnel For FY</option>
                            <option value="To be Nurtured for FY">To be Nurtured for FY</option>
                            <option value="50 New Lead Funnel">50 New Lead Funnel</option>
                            <option value="BD Marked Business Potential">Marked Business Potential</option>
                            <option value="Anchor Clients">Anchor Clients</option>
                          </select>
                          <hr>
                          <small style="color: crimson; font-weight: bold;">
                            This "New Category" dropdown allows users to filter leads based on their business funnel classification. Options include "Q1 20 Closure Funnel," "Potential Funnel For FY," "To be Nurtured for FY," "50 New Lead Funnel," and "BD Marked Business Potential." It helps in segmenting leads by their current status or business potential, enabling better prioritization, tracking, and planning for follow-up actions and conversions.
                          </small>
                        </div>

                        <div class="card p-4 form-group" id="current_quarter_filter_card" style="background: linear-gradient(to right, #fff8e1, #ffffff); border-radius: 12px; box-shadow: 0 4px 8px rgba(0,0,0,0.1); padding: 20px;min-height:200px;justify-content: center;">
                          <?php 
                            $cfData = $this->Menu_model->getCurrentFinancialYearAndQuarter();
                            $currentQuarterNumber = $cfData['quarter'];
                            $currentQuarter = "Q".$currentQuarterNumber;
                            $curFinancialDate       = $this->Menu_model->getFinancialYearRange();
                            $start_financial_date   = $curFinancialDate['start_date'];
                            $start_financial_date_year  = new DateTime($start_financial_date);
                            $cfyear                       = $start_financial_date_year->format('Y');
                          ?>
                          <label>Marked In Quarter <?=$currentQuarterNumber;?> in <?=$cfyear;?></label>
                          <select id="current_quarter_filter_select" class="form-control" name="current_quarter_filter_select">
                            <option value="">Select Category in Current Quarter <?=$currentQuarterNumber;?> in <?=$cfyear;?></option>
                            <option value="20 Closure Funnel in <?=$currentQuarter;?>">20 Closure Funnel	in <?=$currentQuarter;?></option>
                            <option value="Potential Funnel For <?=$currentQuarter;?>">Potential Funnel For <?=$currentQuarter;?></option>
                            <option value="To be Nurtured for <?=$currentQuarter;?>">To be Nurtured for <?=$currentQuarter;?></option>
                          </select>
                          <hr>
                          <small style="color: crimson; font-weight: bold;">
                            This dropdown labeled "Marked In Quarter <?=$currentQuarterNumber;?> in <?=$cfyear;?>" enables users to filter leads based on their current status in <?=$currentQuarter?>. Available options include "20 Closure Funnel in <?=$currentQuarter?>," "Potential Funnel For <?=$currentQuarter?>," and "To be Nurtured for <?=$currentQuarter?>." It helps in categorizing leads for the second quarter, allowing users to analyze progress, prioritize follow-ups, and plan strategic actions for conversion and nurturing during the specified quarter.
                          </small>
                        </div>


                        <div class="card p-4 form-group" id="closing_timeline_filter_card" style="background: linear-gradient(to right, #fff8e1, #ffffff); border-radius: 12px; box-shadow: 0 4px 8px rgba(0,0,0,0.1); padding: 20px;min-height:200px;justify-content: center;">
                          <label>Select Status</label>
                          <select id="closing_timeline_filter_select_status" class="form-control" name="closing_timeline_filter_select_status">
                            <option value="">Select Status</option>
                            <option value="3">Tentative</option>
                            <option value="6">Positive</option>
                          </select>
                          
                          <div id="propose_or_clarify_filter_select_card">
                            <hr>
                             <label>Propose or Clarify Timeline</label>
                            <select class="form-control" id="propose_or_clarify_filter_select">
                                <option value="Propose	Day 6 to 8">Propose	Day 6 to 8</option>
                                <option value="Clarify	Day 8 to 12">Clarify	Day 8 to 12</option>
                                <option value="Nudge Day 12 to 14">Nudge Day 12 to 14</option>
                                <option value="Support	Day 15 to 18">Support	Day 15 to 18</option>
                            </select>
                          </div>

                          <div id="closing_timeline_filter_select_card">
                            <hr>
                              <label>Closing Timeline</label>
                              <select class="form-control" id="closing_timeline_filter_select">
                                  <option value="In 8 Days">In 8 Days</option>
                                  <option value="In 8 to 15 Days">In 8 to 15 Days</option>
                                  <option value="In 15 to 30 Days">In 15 to 30 Days</option>
                                  <option value="More Than 30 Days">More Than 30 Days</option>
                              </select>
                          </div>
                          <hr>
                          <small style="color: crimson; font-weight: bold;">
                           This form section is designed to help users filter and analyze task statuses based on progress and expected completion time. It begins with a "Select Status" dropdown offering Tentative and Positive options. Based on this selection, users can refine their filter using two additional dropdowns: "Propose or Clarify Timeline" (e.g., Propose Day 6 to 8, Clarify Day 8 to 12) and "Closing Timeline" (e.g., In 8 Days, In 15 to 30 Days), enhancing planning accuracy.
                          </small>
                        </div>














                        <div class="card p-4 form-group" id="findCompanyByCard">
                          <label> Find Company After </label>
                          <select id="findCompanyBySelect" class="form-control" name="findCompanyBy">
                            <option value="">Select</option>
                            <option value="Created Date">Created Date</option>
                            <option value="Approved Date">Approved Date</option>  
                          </select>
                          <div class="findCompanyByDateCard mt-1" id="findCompanyByDateCard">
                            <label>Select After Date</label>
                            <input type="date" id="findCompanyByDate" class="form-control" name="findCompanyByDate" max="<?=$adate?>">
                          </div>
                          <div class="findCompanyByTaskCard mt-1" id="findCompanyByTaskCard">
                            <label>Select Task</label>
                            <select id="findCompanyByTaskSelect" class="form-control" name="findCompanyByTaskSelect">
                            <option value="">Select</option>
                            <!-- <option value="all">All</option> -->
                            <?php foreach($action as $fca){
                              if($fca->id == 4 || $fca->id == 10){
                              ?>
                            <option value="<?=$fca->id?>"><?=$fca->name?></option>
                            <?php }} ?>
                          </select>
                          </div>
                          <div class="findCompanyByStatusCard mt-1" id="findCompanyByStatusCard">
                            <label>Select Status</label>
                            <select class="form-control" id="findCompanyByStatusSelect">
                                <option selected disabled>Select Current Status</option>
                                <option value="all">All</option>
                                <?php foreach($allStatus as $getstatus): ?>
                                <option value="<?= $getstatus->id ?>"><?= $getstatus->name ?></option>
                                <?php endforeach; ?>
                            </select>
                          </div>
                        </div>



                              <?php 
                              // $projectCpodelists = $this->Menu_model->GetAllProjectCodeOnHandoverTable();
                              $mappedProjectCpodelists = $this->Menu_model->GetAllProjectCodeMappedWithProjectCoordinator($uid);
                               ?>
                              <div class="card p-4 form-group" id="projectCodeListsCard">
                                <label> Select Project Code </label>
                                <select id="project_code_lists" class="form-control" name="project_code_lists">
                                  <option value="">-- Select Project Code --</option>
                                  <?php foreach($mappedProjectCpodelists as $projectCpodelist): ?>
                                    <option value="<?= $projectCpodelist->project_code ?>"><?= $projectCpodelist->project_code ?></option>
                                  <?php endforeach; ?>
                                </select>
                              </div>


                  <div id="auto_assign" class="card p-4" >
                    <div class="form-group">
                      <label>Select Assign</label>
                      <select class="form-control" id="slct_auto_assign_task">
                        <option value="" >Select Assign</option>
                        <?php 
                          if($type_id ==13 || $type_id ==4){ 
                          $getall_autoassign_task  = $this->Menu_model->GetAllAutoAssignTask($uid);
                          $autotask_taskcnt = 0;
                          foreach($getall_autoassign_task as $key =>$autaskcnt){ ?>
                        <option value="<?= $key; ?>"> <?= $key.' ('.$autaskcnt.') '; ?></option>
                        <?php } } ?>
                        <!-- <option value="Self Assign">Self Assign</option> -->
                        <!-- <option value="Other Assign">Other Assign</option> -->
                        <!-- <option value="Call Assign on Tentive Status">Call Assign on Tentive Status</option> -->
                        <!-- <option value="Mom Check">MOM Check</option> -->
                        <!-- <option value="Praposal Check">Praposal Check</option> -->
                        <!-- <option value="Handover Check">Handover Check</option> -->
                        <!-- <option value="BD Request Check">BD Request Check</option> -->
                        <!-- <option value="Praposal write">BD Request Check</option> -->
                      </select>
                    </div>
                    <div class="form-group">
                      <select class="form-control" id="slct_auto_assign_task_type">
                      </select>
                    </div>
                    <hr>
                    <p id="pendingdata_message"></p>
                  </div>
                  <div id="compulsive_task_card" class="card p-4" >
                    <div class="form-group" id="compulsive_task_card1">
                      <label>Select Status</label>
                      <?php  if($status_nochangecnt > 0){ ?>
                      <select class="form-control" id="statusnochanhecomp_status">
                        <option value="">Select Task</option>
                        <?php 
                          $statusnochanhecomp = [];
                            foreach ($statusnochangecmp as $objects) {
                                $cstatus = $objects->cstatus;
                                if (!isset($statusnochanhecomp[$cstatus])) {
                                    $statusnochanhecomp[$cstatus] = [];
                                }
                                $statusnochanhecomp[$cstatus][] = $objects;
                            }
                            $statusnochanhecomp_wdl = [];
                            foreach ($statusnochangecmp_wdl_nl as $objects_wdl_nl) {
                                $cstatus = $objects_wdl_nl->cstatus;
                                if (!isset($statusnochanhecomp_wdl[$cstatus])) {
                                    $statusnochanhecomp_wdl[$cstatus] = [];
                                }
                                $statusnochanhecomp_wdl[$cstatus][] = $objects_wdl_nl;
                            }
                          ?>
                        <?php 
                          foreach($statusnochanhecomp as $key => $npstatus){
                            $getstatus_name = $this->Menu_model->get_statusbyid($key)[0]->name;
                            $npstatuscnt = sizeof($npstatus);
                            echo "<option class='text-danger' value='$key'>$getstatus_name($npstatuscnt)</options>";
                          }
                          foreach($statusnochanhecomp_wdl as $key => $npstatus){
                            $getstatus_name = $this->Menu_model->get_statusbyid($key)[0]->name;
                            $npstatuscnt = sizeof($npstatus);
                            echo "<option class='text-danger' value='$key'>$getstatus_name($npstatuscnt)</options>";
                          }
                          ?>
                      </select>
                      <?php } ?>
                    </div>
                  </div>
                  <div id="create_bd_request" class="card p-4">
                    <form action="<?=base_url();?>/Menu/NewBDRequestTask" method="post">
                      <div class="was-validated">
                        <h4 class="text-center" id="bdrequestheadings">CREATE BD REQUEST</h4>
                        <hr>
                        <div class="form-group">
                          <input type="hidden" name="uid" value="<?=$uid?>">
                          <input type="hidden" name="request_type_slct" id="request_type_slct" value="">
                          <input type="hidden" name="req_actiontypeid" id="req_actiontypeid" value="">
                          <lable>Plan Date : </lable>
                          <input type="date" name="plandate" value="<?=$adate?>" min="<?=$adate?>" class="form-control" readonly required="">
                          <hr>
                          <lable>Select Request : </lable>
                          <?php $bdrequesttypes = $this->Menu_model->GetSpecialBdrequestType();?>
                          <div>
                            <select class="form-control" name="bd_Select_Request" id="bd_Select_Request" required="">
                              <option value="">Select Request Type</option>
                              <option value="CREATE BD REQUEST">CREATE BD REQUEST</option>
                              <option value="Submit Handover">Submit Handover</option>
                            </select>
                          </div>
                          <div id="whenbdrequest">
                            <hr>
                            <lable>Client Type : </lable>
                            <?php $bdrequesttypes = $this->Menu_model->GetSpecialBdrequestType();?>
                            <div>
                              <select class="form-control" name="bd_client_type" id="bd_client_type" required="">
                                <option value="">Select Client Type</option>
                                <?php foreach($bdrequesttypes as $bdrtypes): ?>
                                <option value="<?=$bdrtypes->id;?>"><?=$bdrtypes->type; ?></option>
                                <?php endforeach; ?>
                              </select>
                            </div>
                            <div id="bd_request_rype_card">
                              <hr>
                              <lable>Request Type : </lable>
                              <select class="form-control" name="bd_request_rype" id="bd_request_rype" required=""></select>
                            </div>
                          </div>
                        </div>
                        <div id= "selectrequestcompanyCard">
                          <hr>
                          <lable>Select Company :</lable>
                          <select name="selectrequestcompany[]" id="selectrequestcompany" class="form-control" multiple="" placeholder="Choose Company" data-allow-clear="1">
                          </select>
                          <small id="selectrequestcompanycnt"></small>
                        </div>
                        <hr>
                        <lable>Plan Time : </lable>
                        <input type="time" id="bdreq_plantime"  name="bdreq_plantime" class="form-control" required="">
                        <div class="invalid-feedback text-white"> * Please provide Plan Time.</div>
                        <div class="valid-feedback">Looks good!</div>
                        <div class="form-group text-center">
                          <hr>
                          <button type="submit" class="btn btn-success" id="requestbutton" onclick="this.form.submit(); this.disabled = true;">Create Request</button>
                        </div>
                      </div>
                    </form>
                  </div>
                  <style>
                    div#create_bd_request {background-image: linear-gradient(to right, #f83600 0%, #f9d423 100%);}
                  </style>
                  <div id="review_planning_card" class="card p-4">
                    <form action="<?=base_url();?>/Menu/PlanningForReview" method="post" id="create-plan-form">
                      <div class="was-validated">
                        <div class="form-group">
                          <input type="hidden" name="uid" value="<?=$uid?>">
                          <input type="hidden" name="cur_review_remarks" id="cur_review_remarks">
                          <div class="mt-0">
                            <lable>Review Type</lable>
                            <select class="form-control" name="reviewtype" required="" id="reviewtype">
                            </select>
                          </div>
                          <lable>Review Period</lable>
                          <?php 
                            $year = date("Y"); // Get the current year
                            $revdate = sprintf("%s-04-01", $year); // Format the date with the dynamic year 
                            ?>
                          <input type="date" name="fixdate" id="fixdate" value="<?= $revdate ?>" min="<?= $revdate ?>" class="form-control" required="" readonly="">
                          <lable>Review Start Date</lable>
                          <?php 
                          $nextDay2 = new DateTime($adate); 
                          $nextDay2->modify('+2 days');
                          $fourDaysAgo = $nextDay2->format('Y-m-d');
                          ?>
                          <input type="date" name="plandate" id="rev_plandate" value="<?=$adate;?>" min="<?=$adate;?>" max="<?= $adate; ?>" class="form-control" required="">
                          <lable>Review Start Time</lable>
                          <input type="time" id="review_plantime"  name="review_plantime" class="form-control" required="">
                          <div class="invalid-feedback text-white"> * Please provide Plan Date Time.</div>
                          <?php if($type_id ==3){ ?>
                          <div class="mt-0">
                            <lable>Select User</lable>
                            <select class="form-control" name="bdid" id="review_bd" required="">
                              <option value="<?=$uid?>"><?=$user['name']?></option>
                            </select>
                          </div>
                          <?php }?>
                          <?php if($type_id ==13 || $type_id ==4){ ?>
                          <div class="mt-0">
                            <lable>Select User</lable>
                            <select class="form-control" name="bdid" id="review_bd" required="">
                              <option value="<?=$uid?>"><?=$user['name']?></option>
                              <?php $bd = $this->Menu_model->get_userbyaaid($uid);
                                foreach($bd as $bd){?>
                              <option value="<?=$bd->user_id?>"><?=$bd->name?></option>
                              <?php } ?>
                            </select>
                          </div>
                          <?php }?>
                          <lable>Review Close Time</lable>
                          <input type="time" id="review_plantime_close"  name="review_plantime_close" class="form-control" readonly required="">
                          <small id="rev_cnt_message"></small>
                          <div class="form-group" id="selectcompany_rev">
                            <hr>
                            <select class="form-control" required="" multiple placeholder="Choose Company" data-allow-clear="1" name="selectrevcmp_status[]" id="selectrevcmp_status" disabled="">
                            </select>
                            <small id="totalrevcmpcnt"></small>
                          </div>
                          <?php if($type_id ==13 || $type_id ==4){ ?>
                          <div class="mt-4">
                            <input type="text" name="meetlink" id="rev_meetlink" placeholder="Meeting Link" class="form-control" required="">
                            <div class="invalid-feedback text-white">* Please provide Meeting LInk.</div>
                            <div class="valid-feedback">Looks good!</div>
                          </div>
                          <?php } ?>
                        </div>
                        <div class="form-group text-center">
                          <button type="submit" class="btn btn-success" id="rev_create_plan">Create Plan</button>
                        </div>
                      </div>
                    </form>
                  </div>
                  <button type="button" class="form-control btn btn-info btn-sm" id="resteFilter">Reset Filter</button>
                </div>
                <div class="card col-lg-4 col-sm-4 p-2" id="content">
                  <div class="card" id="taskplanningimg"  >
                    <img src="https://stemapp.in//assets/image/planning1.jpg" alt="" >
                  </div>
                  <div class="card p-4" id="maintaskcard">
                    <p id="demo" class="text-center text-white hrclass p-2">Time Spent in Task Planning: 00:00:00</p>

                    <?php /*
                    <form method="post" action="<?=base_url();?>Menu/addplantask12" id="myForm" >
                      <div class="was-validated">
                        <input type="hidden" id="curuserid" value="<?=$uid?>" name="bdid" required=""> 
                        <input type="hidden" id="pdate" value="<?=$adate?>" name="pdate" required=""> 
                        <input type="hidden" readonly class="form-control" id="tptime" name="tptime" required=""> 
                        <div class="form-group">
                          <select class="form-control" id="getAvailableTime">
                            <option selected disabled>Get Available Time</option>
                            <option value="1">10:00 AM To 11:00 AM</option>
                            <option value="2">11:00 AM To 12:00 PM</option>
                            <option value="3">12:00 PM To 01:00 PM</option>
                            <option value="4">01:00 PM To 02:00 PM</option>
                            <option value="5">02:00 PM To 03:00 PM</option>
                            <option value="6">03:00 PM To 04:00 PM</option>
                            <option value="7">04:00 PM To 05:00 PM</option>
                          </select>
                          <div id="freeaslotDisplay" class="mt-2"></div>
                          <div id="findbookedslot" class="mt-2"></div>
                        </div>
                        <hr>
                        <input type="time" id="meeting-time" name="ptime" min="10:00" max="19:00" class="form-control" required=""> 
                        <hr>
                        <div class="form-group" id="selectcompany">
                          <small>** You can select multiple companies by pressing Ctrl button. **</small>
                          <lable><span class="alertmessagecmp"><b><small>** You can only 3 company plans at a time **</small></b></span></lable>
                          <select class="form-control" required="" multiple placeholder="Choose Company" data-allow-clear="1" name="selectcompanybyuser[]" id="selectcompanybyuser">
                            <option selected disabled>Select Company</option>
                            <option value="all">All</option>
                          </select>
                          <p id="totalcompany"></p>
                        </div>
                        <div class="form-group">
                          <select id="ntactionnew" name="ntaction" class="form-control" required="">
                            <option value="">Select Task Action</option>
                            <?php  foreach($action as $a){if($a->id!=2 && $a->id!=3 && $a->id!=4 && $a->id!=6 && $a->id!=8 && $a->id!=9 && $a->id!=11 && $a->id!=12 && $a->id!=17 && $a->id!=15 && $a->id!=18 && $a->id!=19 && $a->id!=20 && $a->id!=21){ ?>
                            <option value="<?=$a->id;?>"><?=$a->name;?></option>
                            <?php }} ?>
                          </select>
                        </div>
                        <div class="form-group">
                          <?php $clusters = $this->Menu_model->getClusterByUserId($uid); ?>
                          <select id="select_cluster" name="select_cluster" class="form-control">
                            <option selected value="">Select Cluster</option>
                            <?php  foreach($clusters as $cluster){ ?>
                            <option value="<?=$cluster->id;?>"><?=$cluster->clustername;?></option>
                            <?php } ?>
                          </select>
                        </div>
                        <input type="hidden" id="hiddenSelectStatus" name="selectstatusbyuser">
                        <input type="hidden" id="hiddenTaskAction" name="tasktaction">
                        <input type="hidden" id="hiddenTaskActionByUser" name="taskActionbyuser">
                        <input type="hidden" id="hiddenTaskPurposeByUser" name="taskPurposebyuser">
                        <input type="hidden" id="hiddenSelectStatusByUserNotPlanned" name="selectstatusbyusernotplaned">
                        <input type="hidden" id="hiddenTask_Action" name="task_action">
                        <input type="hidden" id="hiddenDaysFilterCardAnpDate" name="daysfiltercard_anp_date">
                        <input type="hidden" id="hiddenSelectdCategory" name="selectdcategory">
                        <input type="hidden" id="hiddenStatusFilterCardCat" name="statusfiltercardCat">
                        <input type="hidden" id="hiddenTaskActionByUserCat" name="taskActionbyusercat">
                        <input type="hidden" id="hiddenTaskPurposeByUserCatData" name="taskPurposebyusercatdata">
                        <input type="hidden" id="hiddenClusterNameLocation" name="clusterNameLocaction">
                        <input type="hidden" id="hiddenStatusFilterCluster" name="statusfilterCluster">
                        <input type="hidden" id="hiddenTaskActionByCluster" name="taskActionbyCluster">
                        <input type="hidden" id="hiddenTaskPurposeByCluster" name="taskPurposebyCluster">
                        <input type="hidden" id="hiddenCompanyLocation" name="companyLocation">
                        <input type="hidden" id="hiddenSelectActionPlane" name="selectactionplane">
                        <input type="hidden" id="hiddenDaysFilter" name="daysfilter">
                        <input type="hidden" id="hiddenPstAssignCardData" name="pstAssignCardData">
                        <input type="hidden" id="hiddenStatusTaskAction" name="status_taskaction">
                        <input type="hidden" id="hiddenTaskActionFilter" name="task_action_filter">
                        <input type="hidden" id="hiddenTaskActionByUserCardData" name="taskActionbyuserCardData">
                        <input type="hidden" id="hiddenTaskPurposeByUserCardData" name="taskPurposebyuserCardData">
                        <input type="hidden" id="hiddenPartnerTypeSelect" name="partnertype_select">
                        <input type="hidden" id="hiddenPartnerTypeCStatusData" name="partnertype_cstatusData">
                        <input type="hidden" id="hiddenPartnerTypeTaskData" name="partnertype_taskData">
                        <input type="hidden" id="hiddenPartnerTypeTaskActionData" name="partnertype_taskActionData">
                        <input type="hidden" id="hiddenPartnerTypeTaskPurposeData" name="partnertype_taskPurposeData">
                        <input type="hidden" id="hiddenSameStatusLast15DaysData" name="samestatuslast15daysData">
                        <input type="hidden" id="hiddenPartnerTypePlanButData" name="partnertype_planbutData">
                        <input type="hidden" id="hiddenDaysFilter2SameDays" name="daysfilter2_samedays">
                        <input type="hidden" id="hiddenPlanButTaskActionData" name="planbut_taskActionData">
                        <input type="hidden" id="hiddenPlanButTaskPurposeData" name="planbut_taskPurposeData">
                        <input type="hidden" id="hiddenPlanButNoInitTaskData" name="planbutnoinit_TaskData">
                        <input type="hidden" id="hiddenFirstQuarter1CStatusData" name="firstQuarter1cstatysData">
                        <input type="hidden" id="hiddenFirstQuarter1CStatusDataTask" name="firstQuarter1cstatysDataTask">
                        <input type="hidden" id="hiddenFirstQuarter1TaskActionByUser" name="firstQuarter1taskActionbyuser">
                        <input type="hidden" id="hiddenFirstQuarter1TaskPurposeByUser" name="firstQuarter1taskPurposebyuser">
                        <input type="hidden" id="hiddenReviewTargetReviewTypeData" name="reviewTargetreviewtypeData">
                        <input type="hidden" id="hiddenReviewTargetReviewSelfData" name="reviewTargetReviewSelfData">
                        <div class="form-group">
                          <select id="ntppose" class="form-control" name="ntppose" required="">
                            <option value='' disabled>Select Purpose</option>
                          </select>
                        </div>
                        <input type="hidden" class="form-control" value="" id="selectby" name="selectby">
                        <input type="hidden" class="form-control" value="" id="check_data" name="check_data">
                        <?php 
                          if($timecolor == "red"){
                            $button_text = "Submit";
                          }elseif($timecolor == "green"){
                            $button_text = "Request For Approval";
                          }else{
                            $button_text = "Submit";
                          }
                          ?>
                        <center><button class="btn btn-primary m-3" type="submit" id="planbtn1"><?= $button_text; ?></button></center>
                      </div>
                    </form>

                    */?>

                     <form method="post" action="<?=base_url();?>Menu/addplantask12" id="myForm" >
                      <div class="was-validated">
                        <input type="hidden" id="curuserid" value="<?=$uid?>" name="bdid" required=""> 
                        <input type="hidden" id="pdate" value="<?=$adate?>" name="pdate" required=""> 
                        <input type="hidden" readonly class="form-control" id="tptime" name="tptime" required=""> 
                        <div class="form-group">
                          <lable>Planning For Which Time Slot</lable>
                          <select class="form-control" id="getAvailableTime">
                            <option selected >Time Slot</option>
                            <option value="1">10:00 AM To 11:00 AM</option>
                            <option value="2">11:00 AM To 12:00 PM</option>
                            <option value="3">12:00 PM To 01:00 PM</option>
                            <option value="4">01:00 PM To 02:00 PM</option>
                            <option value="5">02:00 PM To 03:00 PM</option>
                            <option value="6">03:00 PM To 04:00 PM</option>
                            <!-- <option value="7">04:00 PM To 05:00 PM</option> -->
                          </select>
                          <div id="freeaslotDisplay" class="mt-2"></div>
                          <div id="findbookedslot" class="mt-2"></div>
                        </div>
                         <hr>
                         <div class="form-group">
                          <select id="ntactionnew" name="ntaction" class="form-control" required="">
                            <option value="">Select Action</option>
                            <?php  foreach($action as $a){if($a->id!=2 && $a->id!=3 && $a->id!=12 && $a->id!=4 && $a->id!=6 && $a->id!=7 && $a->id!=8 && $a->id!=9 && $a->id!=11 && $a->id!=17 && $a->id!=15 && $a->id!=18 && $a->id!=19 && $a->id!=20 && $a->id!=21){ ?>
                            <option value="<?=$a->id;?>"><?=$a->name;?></option>
                            <?php }} ?>
                          </select>
                        </div>
                       
                        <!-- <input type="time" id="meeting-time" name="ptime" min="10:00" max="19:00" class="form-control" required="">  -->
                         <div class="form-group">
                          <select class="form-control" id="meeting-time" name="ptime">
                          </select>
                        </div>

                        <hr>
                        <div class="form-group" id="selectcompany">
                          <small>** You can select multiple companies by pressing Ctrl button. **</small>
                          <lable><span class="alertmessagecmp"><b><small>** You can only 3 company plans at a time **</small></b></span></lable>
                          <select class="form-control" style="height: 250px;" required="" multiple placeholder="Choose Company" data-allow-clear="1" name="selectcompanybyuser[]" id="selectcompanybyuser">
                            <option selected disabled>Select Company</option>
                            <option value="all">All</option>
                          </select>
                          <p id="totalcompany"></p>
                        </div>
                       
                        <div class="form-group">
                          <?php $clusters = $this->Menu_model->getClusterByUserId($uid); ?>
                          <select id="select_cluster" name="select_cluster" class="form-control">
                            <option selected value="">Select Cluster</option>
                            <?php  foreach($clusters as $cluster){ ?>
                            <option value="<?=$cluster->id;?>"><?=$cluster->clustername;?></option>
                            <?php } ?>
                          </select>
                        </div>
                        <input type="hidden" id="hiddenSelectStatus" name="selectstatusbyuser">
                        <input type="hidden" id="hiddenTaskAction" name="tasktaction">
                        <input type="hidden" id="hiddenTaskActionByUser" name="taskActionbyuser">
                        <input type="hidden" id="hiddenTaskPurposeByUser" name="taskPurposebyuser">
                        <input type="hidden" id="hiddenSelectStatusByUserNotPlanned" name="selectstatusbyusernotplaned">
                        <input type="hidden" id="hiddenTask_Action" name="task_action">
                        <input type="hidden" id="hiddenDaysFilterCardAnpDate" name="daysfiltercard_anp_date">
                        <input type="hidden" id="hiddenSelectdCategory" name="selectdcategory">
                        <input type="hidden" id="hiddenStatusFilterCardCat" name="statusfiltercardCat">
                        <input type="hidden" id="hiddenTaskActionByUserCat" name="taskActionbyusercat">
                        <input type="hidden" id="hiddenTaskPurposeByUserCatData" name="taskPurposebyusercatdata">
                        <input type="hidden" id="hiddenClusterNameLocation" name="clusterNameLocaction">
                        <input type="hidden" id="hiddenStatusFilterCluster" name="statusfilterCluster">
                        <input type="hidden" id="hiddenTaskActionByCluster" name="taskActionbyCluster">
                        <input type="hidden" id="hiddenTaskPurposeByCluster" name="taskPurposebyCluster">
                        <input type="hidden" id="hiddenCompanyLocation" name="companyLocation">
                        <input type="hidden" id="hiddenSelectActionPlane" name="selectactionplane">
                        <input type="hidden" id="hiddenDaysFilter" name="daysfilter">
                        <input type="hidden" id="hiddenPstAssignCardData" name="pstAssignCardData">
                        <input type="hidden" id="hiddenStatusTaskAction" name="status_taskaction">
                        <input type="hidden" id="hiddenTaskActionFilter" name="task_action_filter">
                        <input type="hidden" id="hiddenTaskActionByUserCardData" name="taskActionbyuserCardData">
                        <input type="hidden" id="hiddenTaskPurposeByUserCardData" name="taskPurposebyuserCardData">
                        <input type="hidden" id="hiddenPartnerTypeSelect" name="partnertype_select">
                        <input type="hidden" id="hiddenPartnerTypeCStatusData" name="partnertype_cstatusData">
                        <input type="hidden" id="hiddenPartnerTypeTaskData" name="partnertype_taskData">
                        <input type="hidden" id="hiddenPartnerTypeTaskActionData" name="partnertype_taskActionData">
                        <input type="hidden" id="hiddenPartnerTypeTaskPurposeData" name="partnertype_taskPurposeData">
                        <input type="hidden" id="hiddenSameStatusLast15DaysData" name="samestatuslast15daysData">
                        <input type="hidden" id="hiddenPartnerTypePlanButData" name="partnertype_planbutData">
                        <input type="hidden" id="hiddenDaysFilter2SameDays" name="daysfilter2_samedays">
                        <input type="hidden" id="hiddenPlanButTaskActionData" name="planbut_taskActionData">
                        <input type="hidden" id="hiddenPlanButTaskPurposeData" name="planbut_taskPurposeData">
                        <input type="hidden" id="hiddenPlanButNoInitTaskData" name="planbutnoinit_TaskData">
                        <input type="hidden" id="hiddenFirstQuarter1CStatusData" name="firstQuarter1cstatysData">
                        <input type="hidden" id="hiddenFirstQuarter1CStatusDataTask" name="firstQuarter1cstatysDataTask">
                        <input type="hidden" id="hiddenFirstQuarter1TaskActionByUser" name="firstQuarter1taskActionbyuser">
                        <input type="hidden" id="hiddenFirstQuarter1TaskPurposeByUser" name="firstQuarter1taskPurposebyuser">
                        <input type="hidden" id="hiddenReviewTargetReviewTypeData" name="reviewTargetreviewtypeData">
                        <input type="hidden" id="hiddenReviewTargetReviewSelfData" name="reviewTargetReviewSelfData">
                        <div class="form-group">
                          <select id="ntppose" class="form-control" name="ntppose" required="">
                            <option value='' disabled>Select Purpose</option>
                          </select>
                        </div>
                        <input type="hidden" class="form-control" value="" id="selectby" name="selectby">
                        <input type="hidden" class="form-control" value="" id="check_data" name="check_data">
                        <?php 
                          if($timecolor == "red"){
                            $button_text = "Submit";
                          }elseif($timecolor == "green"){
                            $button_text = "Request For Approval";
                          }else{
                            $button_text = "Submit";
                          }
                          ?>
                        <center><button class="btn btn-primary m-3" type="submit" id="planbtn1"><?= $button_text; ?></button></center>
                      </div>
                    </form>



                  </div>
                </div>
                <?php  
                  $rstData = $this->Menu_model->GetActiveTaskPlannerRestrication($adate);
                  foreach ($rstData as $obj) {
                    if ($obj->user_types == $type_id && ($obj->user_ids === '' || $obj->user_ids == $uid)) {
                        $found = true;
                        break; // Exit the loop once a match is found
                    }
                  }
                  if($found){
                  ?>
                <div class="card col-lg-12 col-sm-12">
                  <div class="table-responsive mt-2">
                    <table id="example4" class="table table-striped table-bordered" cellspacing="0" width="100%">
                      <thead class="bg-danger">
                        <tr>
                          <th class="bgresactive">S.No</th>
                          <th class="bgresactive">User Type</th>
                          <th class="bgresactive">Name</th>
                          <th class="bgresactive">Company Status</th>
                          <th class="bgresactive">Partner Type</th>
                          <th class="bgresactive">Company Category</th>
                          <th class="bgresactive">Restrication</th>
                          <th class="bgresactive">Start&nbsp;Date</th>
                          <th class="bgresactive">End&nbsp;Date</th>
                          <th class="bgresactive">Status</th>
                        </tr>
                      </thead>
                      <tbody>
                        <?php 
                          $i=1;
                          foreach($rstData as $data){
                            $chk_user_types = explode(',', $data->user_types);
                            $rsuser_ids = $data->user_ids;
                            $user_ids_arr = explode(',', $rsuser_ids);
                           
                            if(in_array($type_id, $chk_user_types)) {
                           
                                if (empty($user_ids_arr[0]) || in_array($uid, $user_ids_arr)) {
                          ?>
                        <tr>
                          <td><?= $i ?></td>
                          <td>
                            <?php 
                              if($data->user_types !=='all'){
                                  $auser_types = $this->Menu_model->get_user_types($data->user_types);
                                  foreach($auser_types as $types){
                                     $utypes = $types->name;
                                      $utypes = str_replace(' ', '&nbsp;', $utypes);
                                      echo "<span class='p-2 bg-success m-1' style='line-height: 50px;' >$utypes</span>";
                                  }
                              }else{
                                  echo "<span class='p-2 bg-success m-1' style='line-height: 50px;' >All</span>";
                              }
                              ?>
                          </td>
                          <td>
                            <?php 
                              if($data->user_ids !==''){
                                  $rusers = $this->Menu_model->get_userbyids($data->user_ids);
                                  $k=1;
                                  foreach($rusers as $ruser){
                                      if($k == 6){
                                          echo "<br/>";
                                          $k=1;
                                      }
                                      $ruser_name = $ruser->name;
                                      $ruser_name = str_replace(' ', '&nbsp;', $ruser_name);
                                      echo "<span class='p-2 bg-success m-1 text-capitalize' style='line-height: 50px;' >$ruser_name</span>";
                                      $k++;
                                   }
                              }else{
                                  echo "<span class='p-2 bg-warning m-1' style='line-height: 50px;' >All</span>";
                              }
                              ?>
                          </td>
                          <td> 
                            <?php 
                              if($data->company_status !==''){
                                  $acompany_status = $this->Menu_model->get_statusbyMultiid($data->company_status);
                                  foreach($acompany_status as $c_statuss){
                                      $com_status = $c_statuss->name;
                                      $com_status = str_replace(' ', '&nbsp;', $com_status);
                                      echo "<span class='p-2 bg-success m-1' style='line-height: 50px;' >$com_status</span>";
                                  }
                              }else{
                                  if($data->company_status ==''){
                                      echo "<span class='p-2 bg-warning m-1' style='line-height: 50px;' >All</span>";
                                  }
                              }
                              ?>
                          </td>
                          <td> 
                            <?php 
                              if($data->partner_types !==''){
                                  $apartner_types = $this->Menu_model->get_partnerbyMultiid($data->partner_types);
                                  foreach($apartner_types as $cprt_statuss){
                                      $comp_partnername = $cprt_statuss->name;
                                      $comp_partnername = str_replace(' ', '&nbsp;', $comp_partnername);
                                      echo "<span class='p-2 bg-success m-1' style='line-height: 50px;' >$comp_partnername</span>";
                                  }
                              }else{
                                  if($data->partner_types ==''){
                                      echo "<span class='p-2 bg-warning m-1' style='line-height: 50px;' >All</span>";
                                  }
                              }
                              ?>
                          </td>
                          <td> 
                            <?php 
                              if($data->categorys ==''){
                                echo "<span class='p-2 bg-warning m-1' style='line-height: 50px;' >All</span>";
                              }else{
                                $arrays = explode(',', $data->categorys);
                                foreach($arrays as $arr){
                                    if($arr == 'topspender'){echo "<span class='p-2 bg-success m-1' style='line-height: 50px;' >Top&nbsp;Spender</span>";}
                                    if($arr == 'upsell_client'){echo "<span class='p-2 bg-success m-1' style='line-height: 50px;' >Upsell&nbsp;Client</span>";}
                                    if($arr == 'focus_funnel'){echo "<span class='p-2 bg-success m-1' style='line-height: 50px;' >Focus&nbsp;Funnel</span>";}
                                    if($arr == 'keycompany'){echo "<span class='p-2 bg-success m-1' style='line-height: 50px;' >Key&nbsp;Company</span>";}
                                    if($arr == 'pkclient'){echo "<span class='p-2 bg-success m-1' style='line-height: 50px;' >Positive&nbsp;Key&nbsp;Client</span>";}
                                    if($arr == 'all'){echo "<span class='p-2 bg-success m-1' style='line-height: 50px;' >All</span>";}
                                }
                              }
                              ?>
                          </td>
                          <td>
                            <?php
                              if($data->action_id !=='all'){
                                  $actiondata = $this->Menu_model->get_actionbyids($data->action_id);
                                  foreach($actiondata as $actiondatasp){
                                   $actiondataname = $actiondatasp->name;
                                   $actiondataname = str_replace(' ', '&nbsp;', $actiondataname);
                                  echo "<span class='p-2 bg-success m-1' style='line-height: 50px;' >$actiondataname</span>";
                                  }
                              }else{
                                  echo "<span class='p-2 bg-success m-1' style='line-height: 50px;' >All</span>";
                              }
                               ?>
                          </td>
                          <td>
                            <?= $data->sdate ?> 
                          </td>
                          <td>
                            <?php
                              $renddate = $data->edate;
                              $currentDate = new DateTime();
                              $givenDateObj = new DateTime($renddate);
                              // Remove the time part from the current date for accurate comparison
                              $currentDate->setTime(0, 0, 0);
                              $givenDateObj->setTime(0, 0, 0);
                              if ($givenDateObj < $currentDate) {
                                  echo "<span class='p-2 bg-danger m-1' style='line-height: 50px;' >$renddate</span><br>"; 
                                  echo "<span class='p-2 bg-danger m-1' style='line-height: 50px;' >The&nbsp;Restriction&nbsp;has&nbsp;expired.</span>";
                              } elseif ($givenDateObj == $currentDate) {
                                  echo "<span class='p-2 bg-warning m-1' style='line-height: 50px;' >$renddate</span><br>"; 
                                  echo "<span class='p-2 bg-warning m-1' style='line-height: 50px;' >The&nbsp;Restriction&nbsp;expires&nbsp;today.</span>";
                              } else {
                                  echo "<span class='p-2 bg-success m-1' style='line-height: 50px;' >$renddate</span><br>"; 
                                  echo "<span class='p-2 bg-success m-1' style='line-height: 50px;' >The&nbsp;Restriction&nbsp;is&nbsp;still&nbsp;active.</span>";
                              }
                              ?>
                          </td>
                          <td>
                            <?php 
                              if($data->status ==1){
                                  echo "<span class='p-2 bg-success'>Active</span>";
                              }else{
                                   echo "<span class='p-2 bg-danger'>Inactive</span>";
                              }
                              ?>
                          </td>
                        </tr>
                        <?php $i++; } }} ?>
                      </tbody>
                    </table>
                  </div>
                </div>
                <?php } ?>



                <?php if(sizeof($pltrequest) > 0):?>
                <div class="card col-lg-12 col-sm-12">
                   <div class="text-center" style="background: linear-gradient(to right, #ede7f6, #ffffff); border-radius: 12px; box-shadow: 0 4px 8px rgba(0,0,0,0.1); padding: 20px;">
                        <h5>
                          <i>🗂️ Planner Task Approval Request</i><br>
                          <small>📋 This section contains details of the daily planner task submission awaiting approval. 🕒 It outlines the scheduled hours, type of request, and a brief message from the requester for review and validation. ✅</small>
                      </h5>
                    </div>
                  <div class="table-responsive mt-2">
                    <table id="example47" class="table table-striped table-bordered" cellspacing="0" width="100%">
                      <thead class="thead-dark">
                        <tr>
                            <th>Requested By</th>
                            <th>Request Date</th>
                            <th>Type</th>
                            <th>Message</th>
                            <th>Status</th>
                            <th>Approved By</th>
                            <th>Approved Date</th>
                            <th>Created At</th>
                        </tr>
                      </thead>
                      <tbody>
                        <?php foreach ($pltrequest as $row): ?>
                            <tr>
                                <td><?= $row->request_username ?></td>
                                <td><?= $row->request_date ?></td>
                                <td><?= $row->request_type ?></td>
                                <td class="message"><?= $row->request_message ?></td>
                                <td><?= $row->approved_status == '' ? 'Pending' : ($row->approved_status == 1 ? 'Approved' : '-') ?></td>
                                <td><?= $row->request_approved_name ?: '-' ?></td>
                                <td><?= $row->approved_date ?: '-' ?></td>
                                <td><?= $row->created_at ?></td>
                            </tr>
                        <?php endforeach; ?>
                      </tbody>
                    </table>
                  </div>
                </div>
                <?php endif;?>



                        <?php 
                $futuretasks = $this->Menu_model->GetOurFutureTaskLists($uid,$adate);
                if(sizeof($futuretasks) > 0):?>
                
                <div class="card col-lg-12 col-sm-12">
                   <div class="text-center" style="background: linear-gradient(to right, #ede7f6, #ffffff); border-radius: 12px; box-shadow: 0 4px 8px rgba(0,0,0,0.1); padding: 20px;">
                       <h5>
                      <i>📅 Upcoming Future Task</i><br>
                      <small>📝 Upcoming Future Task refers to a planned activity or assignment that is scheduled to occur in the near future. ⏳ It serves as a reminder for preparation and ensures timely execution of responsibilities. ✅ Tracking such tasks helps with prioritization, workflow management, and meeting deadlines effectively. ⏱️</small>
                  </h5>

                    </div>
                  <div class="table-responsive mt-2">
                    <table id="example46" class="table table-striped table-bordered" cellspacing="0" width="100%">
                      <thead class="thead-dark">
                        <tr>
                          <th>S.No</th>
                          <th>CID</th>
                          <th>Company Name</th>
                          <th>Task User Name</th>
                          <th>Appointment Date</th>
                          <th>Task Name</th>
                          <th> + Days</th>
                          <th> Planned Time Status</th>
                          <th> Current Status</th>
                          <th> On Review</th>
                        </tr>
                      </thead>
                      <tbody>
                        <?php $ftasksc = 1; foreach($futuretasks as $futuretask): ?>
                        <tr>
                          <td><?= $ftasksc; ?></td>
                          <td><a target="_BLANK" href="<?=base_url()?>/Menu/CompanyDetails/<?= $futuretask->cid; ?>"><?= $futuretask->cid; ?></a></td>
                           <td><a target="_BLANK" href="<?=base_url()?>/Menu/CompanyDetails/<?= $futuretask->cid; ?>"><?= $futuretask->compname; ?></a></td>
                          <td><?= $futuretask->task_user_name; ?></td>
                          <td><?= $futuretask->appointmentdatetime; ?></td>
                          <td><?= $futuretask->task_name; ?></td>
                          <td><?= $futuretask->marked_meeting_days; ?></td>
                          <td><?= $futuretask->planned_on_status; ?></td>
                          <td><?= $futuretask->current_status; ?></td>
                          <td><?= $futuretask->reviewtype; ?></td>
                        </tr>
                        <?php $ftasksc++; endforeach; ?>
                      </tbody>
                    </table>
                  </div>
                </div>
                <?php endif;?>



                <?php 
                $freviewPendinftasks = $this->Menu_model->GetOurPendingFutureReviewTaskLists($uid);
                if(sizeof($freviewPendinftasks) > 0):?>
                <div class="card col-lg-12 col-sm-12">
                  <div class="text-center" style="background: linear-gradient(to right, #ede7f6, #ffffff); border-radius: 12px; box-shadow: 0 4px 8px rgba(0,0,0,0.1); padding: 20px;">
                        <h5>
                            <i>Up Comming Review</i><br>
                            <small>Upcoming Review refers to a scheduled evaluation or assessment that is set to take place soon. It may involve performance checks, progress tracking, or strategic discussions. Keeping track of upcoming reviews ensures preparedness, timely follow-up, and alignment with organizational goals or deadlines.</small>
                        </h5>
                    </div>
                  <div class="table-responsive mt-2">
                    <table id="example45" class="table table-striped table-bordered" cellspacing="0" width="100%">
                      <thead class="thead-dark">
                        <tr>
                          <th>S.No</th>
                          <th>Review By</th>
                          <th>To User</th>
                          <th>Review Type</th>
                          <th>Review Date</th>
                          <!-- <th>Review Remarks</th> -->
                        </tr>
                      </thead>
                      <tbody>
                        <?php $xy = 1; foreach($freviewPendinftasks as $frevews): ?>
                        <tr>
                          <td><?= $xy; ?></td>
                          <td><?= $frevews->by_review; ?></td>
                          <td><?= $frevews->to_user; ?></td>
                          <td><?= $frevews->reviewtype; ?></td>
                          <td><?= $frevews->plant; ?></td>
                          <!-- <td><?= $frevews->plan_time_remarks; ?></td> -->
                        </tr>
                        <?php $xy++; endforeach; ?>
                      </tbody>
                    </table>
                  </div>
                </div>
                <?php endif; ?>



                <?php  if($plrequestcnt > 0){ 
                  if(!is_null($apr_time)){
                  ?>
                <div class="row mb-2 mt-2">
                  <div class="col-md-4">
                    <div class="card bg-info">
                      <div class="card-header text-center">
                        <h6>Planner Request Time :</h6>
                        <span><?= $request_time; ?></span>
                      </div>
                    </div>
                  </div>
                  <div class="col-md-4">
                    <div class="card bg-success">
                      <div class="card-header text-center">
                        <h6>Planner Approved Time :</h6>
                        <span><?php 
                          if($apr_times == ''){
                            echo 'Pending';
                          }else{
                            echo $apr_times;
                          }
                          ?></span>
                      </div>
                    </div>
                  </div>
                  <div class="col-md-4">
                    <div class="card bg-danger">
                      <div class="card-header text-center">
                        <h6>Late Approved Time:</h6>
                        <span><?= $reqlateapr; ?></span>
                      </div>
                    </div>
                  </div>
                </div>
                <?php }} ?>

                <?php 
                $allplannerlogData    =   $this->Menu_model->GetAllTodaysPlannerLogByUid($uid,$adate);
                $allplannerlogDatacnt = sizeof($allplannerlogData);
                if($allplannerlogDatacnt > 0){?>
                <div class="card">
                <div class="card-header bg-primary" style="align-items: center; justify-content: center; display: flex ;">
                    <h3 class="card-title">All Planner Log Planned By Users</h3>
                    </div>
                    <br>
                    <div class="table-responsive">
                    <table id="example51" class="table table-striped table-bordered" cellspacing="0" wi$dth="100%">
                        <thead>
                        <tr>
                            <th>S.No.</th>
                            <th>BD Name</th>
                            <th>Log By</th>
                            <th>Company Name</th>
                            <th>Task Name</th>
                            <th>Orginal Task Create Date</th>
                            <th>Recent Task Update Date</th>
                            <th>Current  Status</th>
                            <th>Replaned Times </th>
                            <th>Time Difference</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php $ilog=1; foreach($allplannerlogData as $datalog): ?>
                        <tr>
                            <td><?= $ilog; ?></td>
                            <td><?= $datalog->to_user_name; ?></td>
                            <td><?= $datalog->remarks; ?></td>
                            <td><a href="<?=base_url();?>Menu/CompanyDetails/<?=$datalog->cid;  ?>" target="_BLANK"><?= $datalog->company_name; ?></a></td>
                            <td><?= $datalog->task_name; ?></td>
                            <td><?= $datalog->org_task_date; ?></td>
                            <td><?= $datalog->last_create_date; ?></td>
                            <td><?= $datalog->name; ?></td>
                            <td><a href="<?=base_url();?>Menu/ReplanedLog/<?=$datalog->task_id;  ?>" target="_BLANK"><?= $datalog->duplicate_count; ?></a></td>
                            <td><?= $datalog->time_difference; ?></td>
                        </tr>
                        <?php $ilog++; endforeach; ?>
                        </tbody>
                    </table>
                    </div>
                </div>
                <?php } ?>



                <div class="card col-lg-12 col-sm-12" id="content">
                  <div class="row mt-2">
                    <div class="text-center"> <b><i>Total Time Spent in Task Planning : <?php echo $planSessionmin == '' ? "00:00:00" : $planSessionmin; ?></i></b></div>
                    <div class="col-lg-4 col-sm-12">
                      <center>
                        <p class="m-auto" id="chart_div"></p>
                      </center>
                    </div>
                    <div class="col-lg-4 col-sm-12" id="piechart1"></div>
                    <div class="col-lg-4 col-sm-12" id="piechart2"></div>
                  </div>
                  <script>
                    <?php 
                      $totaltaktimep = $this->Menu_model->getUserTotalTaskTimeForTodays($uid,$adate); 
                      $ttime = $totaltaktimep[0]->ttime; 
                      $ttime = $ttime/60;
                      $getPlannerSession = $this->Menu_model->GetPlannerSession($uid);
                      $getPlannerSessioncnt = sizeof($getPlannerSession);
                      if($getPlannerSessioncnt != 0){
                      ?>
                    var pageLoadTime = new Date().getTime() - 0;
                    var x = setInterval(function() {
                    var now = new Date().getTime();
                    var timeSpent = now - pageLoadTime;
                    var hours = Math.floor((timeSpent / 1000) / 3600);
                    var minutes = Math.floor(((timeSpent / 1000) % 3600) / 60);
                    var seconds = Math.floor((timeSpent / 1000) % 60);
                    var formattedTimeSpent =
                    (hours < 10 ? "0" : "") + hours + ":" +
                    (minutes < 10 ? "0" : "") + minutes + ":" +
                    (seconds < 10 ? "0" : "") + seconds;
                    document.getElementById("demo").innerHTML = "Time Spent in Task Planning: " + formattedTimeSpent;
                    document.getElementById("tptime").value=formattedTimeSpent;
                    }, 1000);
                    <?php
                      }
                      ?>
                  </script>
                  <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
                  <script type="text/javascript">
                    google.charts.load('current', {'packages':['gauge']});
                    google.charts.setOnLoadCallback(drawChart);
                    function drawChart() {
                    var data = google.visualization.arrayToDataTable([
                    ['Label', 'Value'],
                    ['Planning', <?=$ttime?>]
                    ]);
                    var options = {
                    redFrom: 0,
                    redTo: 3,
                    yellowFrom: 3,
                    yellowTo: 6,
                    greenFrom: 6,
                    greenTo: 8,
                    minorTicks: 4,
                    max: 8
                    };
                    var chart = new google.visualization.Gauge(document.getElementById('chart_div'));
                    chart.draw(data, options);
                    }
                    google.charts.load("current", {packages:["corechart"]});
                    google.charts.setOnLoadCallback(drawChart2);
                    function drawChart2() {
                    var data = google.visualization.arrayToDataTable([
                    ['Status', 'No of Task'],
                    <?php $action = $this->Menu_model->get_tttbytimedaction($uid,$adate);
                      foreach($action as $ac){?>
                    ["<?=$ac->acname?> (<?=$ac->cont?>)", <?=$ac->cont?>],
                    <?php } ?>
                    ]);
                    var options = {
                    is3D: false,
                    };
                    var chart = new google.visualization.PieChart(document.getElementById('piechart1'));
                    chart.draw(data, options);
                    }
                    google.charts.load("current", {packages:["corechart"]});
                    google.charts.setOnLoadCallback(drawChart4);
                    function drawChart4() {
                    var data = google.visualization.arrayToDataTable([
                    ['Status', 'No of Task'],
                    <?php $status = $this->Menu_model->get_tttbytimedstatus($uid,$adate);
                      foreach($status as $st){?>
                    ["<?=$st->stname?> (<?=$st->cont?>)", <?=$st->cont?>],
                    <?php } ?>
                    ]);
                    var options = {
                    is3D: false,
                    };
                    var chart = new google.visualization.PieChart(document.getElementById('piechart2'));
                    chart.draw(data, options);
                    }
                  </script>
                  <hr>
                  <div>
                    <div id="accordion">
                      <div class="card">
                        <div class="card-header" id="headingOne">
                          <button class="btn btn-link" data-toggle="collapse" data-target="#collapseOne" aria-controls="collapseOne">
                            <h6>Tasks Planned for <?=$adate?></h6>
                          </button>
                        </div>

                        <div id="collapseOne" class="collapse show" aria-labelledby="headingOne" data-parent="#accordion">
                          <div class="card-body">
                            <?php $tted = $this->Menu_model->get_tttbytimedaction($uid,$adate); foreach($tted as $ted){?>
                            <span class="badge"  style="background-color:<?=$ted->aclr?>"><?=$ted->acname?> <span class="badge badge-light text-dark"><?=$ted->cont?></span></span>
                            <?php } ?>
                            <hr>
                            <?php $tted = $this->Menu_model->get_tttbytimedstatus($uid,$adate); foreach($tted as $ted){?>
                            <span class="badge" style="background-color:<?=$ted->sclr?>"><?=$ted->stname?> <span class="badge badge-light text-dark"><?=$ted->cont?></span></span>
                            <?php } ?>
                            <hr>
                            <?php $tted = $this->Menu_model->get_ttbytimed_partner_master_all($uid,$adate); 
                            foreach($tted as $teds){ ?>
                                <span class="badge" style="background-color:<?=$teds->clr?>"><?=$teds->name?> <span class="badge badge-light text-dark"><?=$teds->task_cont?></span></span>
                            <?php } ?>
                            <hr>
                            <?php $tted = $this->Menu_model->get_ttbytimed_category_all($uid,$adate); 
                             if (!empty($tted)) {
                              foreach ($tted as $key => $value) {
                                ?>
                                    <span class="badge bg-primary me-1">
                                        <?= ucwords(str_replace('_', ' ', $key)); ?> 
                                        <span class="badge bg-light text-dark ms-1"><?= $value ?></span>
                                    </span>
                                <?php 
                                    }
                                }
                                ?>
                            <hr>
                            <?php $timeslot = $this->Menu_model->get_timeslot(); $jk=1; foreach($timeslot as $tl){
                              $t1=$tl->time1;$t2=$tl->time2;
                              if($jk==8){
                                $jk=1;
                              }
                              ?>
                            <div class="card border border-info">
                              <div class="card-header cat<?=$jk;?>">
                                <h5 class='text-left mb-2' style="color:hsl(309, 83%, 39%)!important"><?=date("h:i A", strtotime($tl->time1));?> to <?=date("h:i A", strtotime($tl->time2));?></h5>
                              </hr>
                                <?php  $ted = $this->Menu_model->get_ttbytimedaction($uid,$adate,$t1,$t2); foreach($ted as $ted){
                                  ?>
                                <?php if($ted){?>
                                <input type="hidden" id="timeslot-alloted_s" name="timeslot-alloted_s" value="<?=$tl->time1?>">
                                <input type="hidden" id="timeslot-alloted_e" name="timeslot-alloted_e" value="<?=$tl->time2?>">
                                <?php } ?>
                                <span class="badge" style="background-color:<?=$ted->aclr?>"><?=$ted->acname?> <span class="badge badge-light text-dark"><?=$ted->cont?></span></span>
                                <?php } ?>
                                <hr>
                                <?php $ted = $this->Menu_model->get_ttbytimedstatus($uid,$adate,$t1,$t2); foreach($ted as $ted){?>
                                <span class="badge" style="background-color:<?=$ted->sclr?>"><?=$ted->stname?> <span class="badge badge-light text-dark"><?=$ted->cont?></span></span>
                                <?php } ?>

                                <hr>
                                <?php $ted = $this->Menu_model->get_ttbytimed_partner_master($uid,$adate,$t1,$t2); foreach($ted as $ted){?>
                                <span class="badge" style="background-color:<?=$ted->clr?>"><?=$ted->name?> <span class="badge badge-light text-dark"><?=$ted->task_cont?></span></span>
                                <?php } ?>
                                <hr>
                                <?php 
                                  $ted = $this->Menu_model->get_ttbytimed_category($uid, $adate, $t1, $t2);
                                  if (!empty($ted)) {
                                      foreach ($ted as $key => $value) {
                                  ?>
                                      <span class="badge bg-primary me-1">
                                          <?= ucwords(str_replace('_', ' ', $key)); ?> 
                                          <span class="badge bg-light text-dark ms-1"><?= $value ?></span>
                                      </span>
                                  <?php 
                                      }
                                  }
                                ?>
                              </div>
                              <?php $totaltaktimep = $this->Menu_model->get_totaltaktimepbyh($uid,$adate,$t1,$t2);
                                $ttime = $totaltaktimep[0]->ttime;
                                if($ttime>'120'){$bcolor="bg-success"; $msg="Great! You have been scheduled for full-time utilization.";}
                                elseif($ttime=='0'){$bcolor="bg-danger";$msg="Caution! Make sure to plan for this period.";}
                                else{$bcolor="bg-warning";$msg="Nice job! Consider planning additional tasks.";}
                                ?>
                              <div class="card-footer <?=$bcolor?>"><b><?=$msg?></b></div>
                            </div>
                            <?php  $jk++; } ?>
                            <br>
                            <?php if(sizeof($getplandt) > 0){ ?>
                            <div class="card border border-info">
                              <div class="card-header">
                                <b>AutoTask Time: <?=date("h:i A", strtotime($getplandt[0]->stime));?> to <?=date("h:i A", strtotime($getplandt[0]->etime));?></b>
                                </br>
                                <?php
                                  $t1=$getplandt[0]->stime;
                                  $t2=$getplandt[0]->etime;
                                  $ted = $this->Menu_model->get_ttbytimedactionAutoTask($uid,$adate,$t1,$t2);
                                  foreach($ted as $ted){
                                  ?>
                                <?php if($ted){?>
                                <input type="hidden" id="timeslot-alloted_s" name="timeslot-alloted_s" value="<?=$tl->time1?>">
                                <input type="hidden" id="timeslot-alloted_e" name="timeslot-alloted_e" value="<?=$tl->time2?>">
                                <?php } ?>
                                <span class="badge" style="background-color:<?=$ted->aclr?>"><?=$ted->acname?> <span class="badge badge-light text-dark"><?=$ted->cont?></span></span>
                                <?php } ?>
                                <hr>
                                <?php
                                  $ted = $this->Menu_model->get_ttbytimedstatusAutoTask($uid,$adate,$t1,$t2);
                                  foreach($ted as $ted){
                                      ?>
                                <span class="badge" style="background-color:<?=$ted->sclr?>"><?=$ted->stname?> <span class="badge badge-light text-dark"><?=$ted->cont?></span></span>
                                <?php } ?>
                              </div>
                              <?php
                                $totaltaktimep = $this->Menu_model->get_totaltaktimepbyh($uid,$adate,$t1,$t2);
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
                      </div>
                      <center>
                        <button class="btn btn-info" id="printPage">Print Page</button> <br><br>
                        <p> <b> <span id="rtsyndp">Remaining Time to Start Your Next Days Planing:</span> <span  id="planertime"></span></b></p>
                      </center>
                    </div>
                  </div>
                </div>
                <section class="content">
                  <div class="container-fluid">
                    <div class="p-3" id="logs"></div>
                  </div>
                </section>
              </div>
            </div>
          </section>
          <?php  }
            }
            ?>
        </div>
      </div>
    </div>

     <!-- Modal -->
     <div class="modal fade" id="exampleModalReminder" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                              <div class="modal-dialog" role="document">
                                <div class="modal-content">
                                  <div class="modal-header bg-info text-center">
                                    <h5 class="modal-title" id="exampleModalLabel">Create Reminder</h5>
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                      <span aria-hidden="true">&times;</span>
                                    </button>
                                  </div>
                                  <div class="modal-body">
                                      <form action="<?=base_url();?>Management/SendReminder" method="post">
                                        <div class="form-group">
                                          <label>Select Reminder Type : </label>
                                          <select class="form-control" name="reminder_type" required>
                                          <option>Select Reminder</option>
                                          <?php 
                                            $rmmess = $this->Menu_model->remindermessage();
                                            foreach($rmmess as $mess){
                                              // if($mess->id ==6){$requestselected ='selected';}else{$requestselected ='disabled';}
                                            ?>
                                            <option value="<?=$mess->id; ?>"><?=$mess->message; ?></option>
                                              <?php } ?>
                                          </select>
                                        </div>
                                          <div class="form-group">
                                            <label>Reminder Message: </label>
                                            <textarea class="form-control" name="reminder_message" rows="3" required></textarea>
                                          </div>
                                          <div class="form-group text-center">
                                          <button type="submit" class="btn btn-primary">Send Reminder</button>
                                          </div>
                                      </form>
                                  </div>
                                </div>
                              </div>
                            </div>



    <input type="hidden" value="<?=$adate?>" id = "uplanedate">
    <input type="hidden" value="<?=$phours?>" id = "phours">
    <input type="hidden" value="<?=$pminutes?>" id = "pminutes">
    <input type="hidden" value="<?=$pseconds?>" id = "pseconds">
    <input type="hidden" value="<?=$totalttasktime?>" id = "totalttasktime">
    <input type="hidden" value="<?=$plannerremTime?>" id = "plannerremTime">
    <script type='text/javascript'>
      var currentTime = new Date();
      var currentHours = currentTime.getHours();
      var currentMinutes = currentTime.getMinutes();
      currentHours = (currentHours < 10 ? "0" : "") + currentHours;
      currentMinutes = (currentMinutes < 10 ? "0" : "") + currentMinutes;
      var currentTimeString = currentHours + ":" + currentMinutes;
      var year = currentTime.getFullYear();
      var month = ("0" + (currentTime.getMonth() + 1)).slice(-2); // Month is zero-based, so we add 1 and pad with leading zero if needed
      var day = ("0" + currentTime.getDate()).slice(-2); // Pad with leading zero if needed
      // Construct the desired date format (YYYY-MM-DD)
      var formattedDate = year + "-" + month + "-" + day;
      var plandate = document.getElementById("plandate").value;
      if(plandate == formattedDate){
      document.getElementById("meeting-time").min = currentTimeString;
      document.getElementById("tasktimeplan").min = currentTimeString;
      }
      //   document.getElementById("meeting-time").min = currentTimeString;
      //   document.getElementById("tasktimeplan").min = currentTimeString;
      $(document).ready(function() {
      $('#meeting-time').on('change', function() {
      var enteredTime = $(this).val();
      var time = new Date('1970-01-01T' + enteredTime);
      
      // Get hours, minutes, and seconds
      var hours = ('0' + time.getHours()).slice(-2); // Ensure leading zero
      var minutes = ('0' + time.getMinutes()).slice(-2); // Ensure leading zero
      var seconds = ('0' + time.getSeconds()).slice(-2);
      var formattedTime = hours + ':' + minutes + ':' + seconds;
      var starttime = $('#timeslot-alloted_s').val();
      var endtime = $('#timeslot-alloted_e').val();
      var startMs = convertToMilliseconds($('#timeslot-alloted_s').val());
      var endMs = convertToMilliseconds($('#timeslot-alloted_e').val());
      var enteredMs = convertToMilliseconds(enteredTime);
      // Check if entered time is between start and end time frames
      if(plandate == formattedDate){
      if (enteredMs >= startMs && enteredMs <= endMs) {
      alert("You already have plan for this time slot");
      $('#planbtn1').css('display', 'none');
      } else {
      $('#planbtn1').css('display', '');
      }
      }
      });
      
      function convertToMilliseconds(timeStr) {
      var parts = timeStr.split(':');
      var hours = parseInt(parts[0], 10);
      var minutes = parseInt(parts[1], 10);
      var seconds = parseInt(parts[2], 10) || 0; // Default to 0 seconds if not provided
      return hours * 3600000 + minutes * 60000 + seconds * 1000;
      }
      });
      
      $("#cmpdetail,#statusmdetail,#locationdetail,#categorydetail,#notask15ddetail,#tobescheduled,#samesfld,#preplanned,#reviewtarget").hide();
      
    </script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
    <!-- <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/js/select2.min.js"></script> -->
    <script type='text/javascript'>
      $(document).ready(function() {
       $('#selectcompanyname').hide();
       $("#selectstatus").hide();
       $("#daysByTask").hide();
       $("#selectcompanybyuser").hide();
       $("#tasktaction").hide();
       $('#taskActionbyuser').hide();
       $('#taskPurposebyuser').hide();
       $("#actionnotplaned").hide();
       $("#tptime").hide();
       $('#ntactionnew').hide();
       $('#ntppose').hide();
       $('#meeting-time').hide();
       $('#planbtn1').hide();
       $('#companyLocation').hide();
       $('#companyLocationdata').hide();
       $('#companyLocation').hide();
       $('#selectCategory').hide();
       $("#maintaskcard").hide();
       $("#selectactionplanecard").hide();
       $("#daysfiltercard").hide();
       $("#companyLocationdatacard").hide();
       $("#daysfiltercard_anp").hide();
       $("#taskActionbyusercatcatcard").hide();
       $("#taskPurposebyusercatcard").hide();
       $("#clusterLocactionFiltercard").hide();
       $("#statusfiltercardCluster").hide();
       $("#taskActionbyuserCluster").hide();
       $("#taskPurposebyuserCluster").hide();
       $("#task_actionCard").hide();
       $("#pstAssignCard").hide();
       $('#taskActionCard').hide();
       $('#status_taskaction_card').hide();
       $('#taskaction_card_area').hide();
       $('#taskActionbyuserCard').hide();
       $('#taskPurposebyuserCard').hide();
       $('#partnertype').hide();
       $('#partnertype_cstatus').hide();
       $('#partnertype_task').hide();
       $('#partnertype_taskAction').hide();
       $('#partnertype_taskPurpose').hide();
       $('#sameStatusLastLimitDays').hide();
       $('#partnertype_planbut').hide();
       $('#daysfiltercard_planbut').hide();
       $('#planbut_taskActioncard').hide();
       $('#planbut_taskPurposecard').hide();
       $('#planbutnotinitiatedcard').hide();
       $('#planbutnotinitiatedcardold').hide();
       $('#create_barg_in_meeting').hide();
       $('#firstQuarter1').hide();
       $('#firstQuarter1cstatys').hide();
       $('#firstQuarter1cstatysDataTask').hide();
       $('#firstQuarter1taskActionbyuser').hide();
       $('#firstQuarter1taskPurposebyuser').hide();
       $('#reviewTargetDate').hide();
       $('#reviewTargetDate_typeoftaskCard').hide();
       $('#reviewTargetDate_Action').hide();
       $('#reviewTargetDate_Purpose').hide();
       $('#reviewTargetReviewSelf').hide();
       $('#reviewTargetReviewSelf').hide();
       $('#select_cluster').hide();
       $('#selectcompanyname_barg').hide();
       $('#status_taskaction_card').hide();
       $('#auto_assign').hide();
       $('#selectbarginCompanyType').hide();
       $('#bargmeetingcluster').hide();
       $('#selectReseachCompanyType').hide();
       $('#slct_auto_assign_task_type').hide();
       $('#pendingdata_message').hide();
       $('#compulsive_task_card').hide();
       $('#actionnotplaned_NeedYour').hide();
       $("#review_planning_card").hide();
       $("#need_your_attention").hide();
       $("#becauseofplanchange").hide();
       $("#becauseof_topsender_pst_meet").hide();
       $("#becauseofTPM_TaskDatacard").hide();
       $("#becauseofTPM_TaskDatacard_new").hide();
      //  New Changes By Deepak 
      $('#slct_days_status').hide();
      $('#taskPurposebyuserCard_days').hide();
      $('#taskPurposebyuserCluster_days').hide();
      $('#taskPurposebyusercatcard_days').hide();
      $('#partnertype_taskPurpose_days').hide();
      $('#projectCodeListsCard').hide();
      
      
      
      $('#create_bd_request').hide();
      $('#bd_request_rype_card').hide();
      $('#whenbdrequest').hide();
      
      $('#selectrequestcompanyCard').hide();
      // Assign task
      $('#tommarowAssignTaskype').hide();
      $('#mandatory_task_card').hide();

      $('#new_category_filter_card').hide();
      $('#current_quarter_filter_card').hide();
      $('#closing_timeline_filter_card').hide();

      $("#findCompanyByCard").hide();
      $("#findCompanyByDateCard").hide();
      $("#findCompanyByTaskCard").hide();
      $("#findCompanyByStatusCard").hide();
      
      });        
      $("#mainbox").hide();$("#ScheduledBox").hide();
      $("#box0").hide();$("#box1").hide();$("#box2").hide();$("#box3").hide();$("#box4").hide();$("#box5").hide();
      $('#ntactionnew').on('change', function f() {    
      var inid = document.getElementById("selectcompanybyuser").value;
      var aid = document.getElementById("ntactionnew").value;
      
      var inidids = '';
      $('#selectcompanybyuser :selected').each(function(i, sel){
          inidids += $(sel).val()+',';
      });
      if(aid==3){
        $('#select_cluster').show().attr('required', '');
      }else{
        $('#select_cluster').hide().removeAttr('required');
      }
      var selectby = $('#selectby').val();
      $.ajax({
      url:'<?=base_url();?>Menu/getpurposebyinidnew',
      type: "POST",
      data: {
      inid: inidids,
      aid: aid,
      selectby:selectby
      },
      cache: false,
      success: function a(result){
          console.log(result);
      $("#ntppose").html(result);
      }
      });
 
      });

      
      var radioButtons = document.querySelectorAll('input[name="optradio"]');
      radioButtons.forEach(function(radio) {
      radio.addEventListener('change', function() {
      var val = radio.value;
      $("#selectby").val(val);
      if(val =='actionNotPlannedNeed'){
        $("#tasktype").text('No Work Days');
      }else if (val == 'actionNotPlanned'){
        $("#tasktype").text('Action Not Planned');
      }else{
        $("#tasktype").text(val);
      }
      
      if(val=='Compnay Name'){
          var userslcttype = <?php echo $type_id; ?>;
          if(userslcttype == 13){
            $('#selectcompanyname').hide();
            $("#selectstatus").hide();
            $("#becauseof_topsender_pst_meet").show();
          
            $('#planning_funnel').on('change', function() {
                var planning_funnel = $(this).val();
                if(planning_funnel == 'Own funnel'){
                  $('#becauseofTPM_TaskDatacard').hide();
                  $('#becauseofTPM_TaskDatacard_new').hide();
                  $('#selectcompanyname').show();
                  $("#selectstatus").hide();
              $("#selectcompanybyuser").html('');
                $.ajax({
                url:'<?=base_url();?>Menu/getownfunnel',
                type: "POST",
                data: {
                sid:'all',
                uid: uid
                },
                cache: false,
                success: function a(result){
                $("#maintaskcard").show();    
                $("#selectcompanybyuser").html(result);
                $("#selectcompanybyuser").show();
                var optionCount = $('#selectcompanybyuser').find('option').length;
                optionCount = optionCount-1;
                $("#totalcompany").text('Total Company :'+ optionCount);
                $("#tptime").val('');
                $("#tptime").show();
                $('#ntactionnew').show();
                $('#ntppose').show();
                $('#meeting-time').show();
                $('#planbtn1').show();
                $("#taskplanningimg").hide();
                }
                });
                }else if(planning_funnel == 'Other funnel'){
                  $('#becauseofTPM_TaskDatacard').show();
                  $('#selectcompanyname').hide();
                  $("#selectstatus").hide();
                  $("#tasktaction").hide();
                  $("#taskActionbyuser").hide();
                  $("#taskPurposebyuser").hide();
                  $("#taskplanningimg").show();
                  var becauseofTPM_TaskData_img = $("#becauseofTPM_TaskData").val();
                  var becauseofTPM_new_img = $("#becauseofTPM_new").val();
                  var planning_funnel_data = $("#planning_funnel").val();
                  if(becauseofTPM_TaskData_img == ''){
                    $("#taskplanningimg").show();
                  }else{
                    $("#taskplanningimg").hide();
                  }
                  if(becauseofTPM_new_img == ''){
                    $("#taskplanningimg").show();
                  }else{
                    $("#taskplanningimg").hide();
                  }
                  if(planning_funnel_data == '' && becauseofTPM_TaskData_img !==''){
                    $("#taskplanningimg").hide();
                  }else{
                    $("#taskplanningimg").hide();
                  }
                }else if(planning_funnel == ''){
                  $('#becauseofTPM_TaskDatacard').hide();
                  $('#becauseofTPM_TaskDatacard_new').hide();
                  $("#taskplanningimg").show();
                  $('#selectcompanyname').hide();
                  $("#selectstatus").hide();
                  $("#maintaskcard").hide();
                  $("#tasktaction").hide();
                  $("#taskActionbyuser").hide();
                  $("#taskPurposebyuser").hide();
                }
            });
          $('#becauseofTPM_TaskData').on('change', function() {
              
            var becauseofTPM = $(this).val();
            $('#becauseofTPM_TaskDatacard_new').show();
              $("#selectcompanybyuser").html('');
              $.ajax({
              url:'<?=base_url();?>Menu/getstatuscmp_becauseoftpm',
              type: "POST",
              data: {
              becauseofTPM: becauseofTPM,
              uid: uid
              },
              cache: false,
              success: function a(result){
              $("#maintaskcard").show();    
              $("#selectcompanybyuser").html(result);
              $("#selectcompanybyuser").show();
              var optionCount = $('#selectcompanybyuser').find('option').length;
              optionCount = optionCount-1;
              $("#totalcompany").text('Total Company :'+ optionCount);
              $("#tptime").val('');
              $("#tptime").show();
              $('#ntactionnew').show();
              $('#ntppose').show();
              $('#meeting-time').show();
              $('#planbtn1').show();
              $("#taskplanningimg").hide();
              }
              });
            });
            $('#becauseofTPM_new').on('change', function() {
              
              var usertypes = $('#becauseofTPM_TaskData').val();
              var becauseofTPM = $(this).val();
                $("#selectcompanybyuser").html('');
                $.ajax({
                url:'<?=base_url();?>Menu/get_becauseoftpm',
                type: "POST",
                data: {
                usertypes: usertypes,
                becauseofTPM: becauseofTPM,
                uid: uid
                },
                cache: false,
                success: function a(result){
                $("#maintaskcard").show();    
                $("#selectcompanybyuser").html(result);
                $("#selectcompanybyuser").show();
                var optionCount = $('#selectcompanybyuser').find('option').length;
                optionCount = optionCount-1;
                $("#totalcompany").text('Total Company :'+ optionCount);
                $("#tptime").val('');
                $("#tptime").show();
                $('#ntactionnew').show();
                $('#ntppose').show();
                $('#meeting-time').show();
                $('#planbtn1').show();
                $("#taskplanningimg").hide();
                }
                });
              });
          }else{
            $('#selectcompanyname').show();
            $("#selectstatus").hide();
          }
          $("#maintaskcard").hide();
          $("#actionPlanned").show();
          $("#actionnotplaned").hide();
          $('#ntactionnew').hide();
          $('#companyLocation').hide();
          $('#tasktaction').hide();
          $('#taskActionbyuser').hide();
          $('#taskPurposebyuser').hide();
          var uid = $("#curuserid").val();
          
          $('#search_company').on('input', function() {
                var inputVal = $(this).val();
                var options = $('#data').find('option').map(function() {
                    return $(this).val();
                }).get();
                var selectedId = null;
                options.forEach(function(option) {
                    if (option.startsWith(inputVal)) {
                        selectedId = option.split(' ')[0];
                    }
                });
                if (selectedId) {
                  $.ajax({
                    url:'<?=base_url();?>Menu/getUserCompBy_cmp_id',
                    type: "POST",
                    data: {
                    company_val: selectedId,
                    uid: uid
                    },
                    cache: false,
                    success: function a(result){
                    $("#maintaskcard").show();    
                    $("#selectcompanybyuser").html(result);
                    $("#selectcompanybyuser").show();
                    var optionCount = $('#selectcompanybyuser').find('option').length;
                    optionCount = optionCount-1;
                    $("#totalcompany").text('Total Company :'+ optionCount);
                    // $("#tasktaction").show();
                    $("#tptime").val('');
                    $("#tptime").show();
                    $('#ntactionnew').show();
                    $('#ntppose').show();
                    $('#meeting-time').show();
                    $('#planbtn1').show();
                    $("#taskplanningimg").hide();
                    }
                    });
                }
            });
          $('#selectstatusbyuser').on('change', function() {
          var selectedValue = $(this).val();
              $("#daysByTask").show();
              $("#selectcompanybyuser").html('');
              $.ajax({
              url:'<?=base_url();?>Menu/getstatuscmp',
              type: "POST",
              data: {
              sid: selectedValue,
              uid: uid
              },
              cache: false,
              success: function a(result){
              $("#maintaskcard").show();    
              $("#selectcompanybyuser").html(result);
              $("#selectcompanybyuser").show();
              var optionCount = $('#selectcompanybyuser').find('option').length;
              optionCount = optionCount-1;
              $("#totalcompany").text('Total Company :'+ optionCount);
              $("#tasktaction").show();
              $("#tptime").val('');
              $("#tptime").show();
              $('#ntactionnew').show();
              $('#ntppose').show();
              $('#meeting-time').show();
              $('#planbtn1').show();
              $("#taskplanningimg").hide();
              }
              });
          });
          $('#tasktaction').on('change', function() {
              var uid = $("#curuserid").val();
              var tasktaction = $(this).val();
              var selectedValue = $('#selectstatusbyuser').val();
              $.ajax({
              url:'<?=base_url();?>Menu/getstatuscmpwithtasktaction',
              type: "POST",
              data: {
              sid: selectedValue,
              tasktaction: tasktaction,
              uid: uid
              },
              cache: false,
              success: function a(result){
                  $("#selectcompanybyuser").html(result);
                  $("#selectcompanybyuser").show();
                  var optionCount = $('#selectcompanybyuser').find('option').length;
              optionCount = optionCount-1;
              $("#totalcompany").text('Total Company :'+ optionCount);
              $('#taskActionbyuser').show();
                  }
              });
              });
      
          $('#taskActionbyuser').on('change', function() {
              var uid = $("#curuserid").val();
              var taskActionbyuser = $(this).val();
              var tasktaction = $('#tasktaction').val();
              var selectedValue = $('#selectstatusbyuser').val();
              $.ajax({
              url:'<?=base_url();?>Menu/getTaskActionYesorNobyuser',
              type: "POST",
              data: {
              sid: selectedValue,
              tasktaction: tasktaction,
              taskActionbyuser: taskActionbyuser,
              uid: uid
              },
              cache: false,
              success: function a(result){
                  $("#selectcompanybyuser").html(result);
                  $("#selectcompanybyuser").show();
                  var optionCount = $('#selectcompanybyuser').find('option').length;
              optionCount = optionCount-1;
              $("#totalcompany").text('Total Company :'+ optionCount);
              $('#taskPurposebyuser').show();
                  }
              });
              });
      
              $('#taskPurposebyuser').on('change', function() {
              var uid = $("#curuserid").val();
              var taskPurposebyuser = $(this).val();
              var tasktaction = $('#tasktaction').val();
              var selectedValue = $('#selectstatusbyuser').val();
              var taskActionbyuser = $('#taskActionbyuser').val();
              $.ajax({
              url:'<?=base_url();?>Menu/getTaskPurposeYesorNobyuser',
              type: "POST",
              data: {
              sid: selectedValue,
              tasktaction: tasktaction,
              taskActionbyuser: taskActionbyuser,
              taskPurposebyuser: taskPurposebyuser,
              uid: uid
              },
              cache: false,
              success: function a(result){
                  $("#selectcompanybyuser").html(result);
                  $("#selectcompanybyuser").show();
                  var optionCount = $('#selectcompanybyuser').find('option').length;
              optionCount = optionCount-1;
              $("#totalcompany").text('Total Company :'+ optionCount);
                  }
              });
              });
      }else{
        $('#selectcompanyname').hide();
      }
      if(val == 'actionNotPlanned'){
          $("#maintaskcard").hide();
          $("#actionPlanned").hide();
          $("#actionnotplaned").show();
          $('#companyLocationdata').hide();
          $('#tasktaction').hide();
          $('#taskActionbyuser').hide();
          $('#taskPurposebyuser').hide();
          $('#selectstatusbyusernotplaned').on('change', function() {
          var selectstatusbyusernotplaned = $(this).val();
          $("#selectcompanybyuser").html('');
          $("#totalcompany").text('');
          $("#taskplanningimg").hide('');
          var uid = $("#curuserid").val();
      
              $.ajax({
              url:'<?=base_url();?>Menu/getstatuscmpnotplaned',
              type: "POST",
              data: {
              sid: selectstatusbyusernotplaned,
              uid: uid
              },
              cache: false,
              success: function a(result){
              $("#maintaskcard").show();
              $("#selectcompanybyuser").html(result);
              $("#selectcompanybyuser").show();
              var optionCount = $('#selectcompanybyuser').find('option').length;
              optionCount = optionCount-1;
              $("#totalcompany").text('Total Company :'+ optionCount);
              $("#tptime").show();
              $('#ntactionnew').show();
              $('#ntppose').show();
              $('#meeting-time').show();
              $('#planbtn1').show();
              // $('#daysfiltercard_anp').show();
              $('#task_actionCard').show();
              }
              });
          });
          $('#task_action').on('change', function() {
          var selectstatusbyusernotplaned = $('#selectstatusbyusernotplaned').val();
          var task_action = $(this).val();
          $("#selectcompanybyuser").html('');
          $("#totalcompany").text('');
          var uid = $("#curuserid").val();
              $.ajax({
              url:'<?=base_url();?>Menu/taskactionnotplan',
              type: "POST",
              data: {
              sid: selectstatusbyusernotplaned,
              task_action: task_action,
              uid: uid
              },
              cache: false,
              success: function a(result){
              $("#maintaskcard").show();
              $("#selectcompanybyuser").html(result);
              $("#selectcompanybyuser").show();
              var optionCount = $('#selectcompanybyuser').find('option').length;
              optionCount = optionCount-1;
              $("#totalcompany").text('Total Company :'+ optionCount);
              $("#tptime").show();
              $('#ntactionnew').show();
              $('#ntppose').show();
              $('#meeting-time').show();
              $('#planbtn1').show();
              $('#daysfiltercard_anp').show();
              }
              });
          });
      
          $('#daysfiltercard_anp_date').on('change', function() {
          var selectstatusbyusernotplaned = $('#selectstatusbyusernotplaned').val();
          var task_action = $('#task_action').val();
          var daysfiltercard_anp_date = $(this).val();
          
          $("#selectcompanybyuser").html('');
          $("#totalcompany").text('');
      
          var uid = $("#curuserid").val();
              $.ajax({
              url:'<?=base_url();?>Menu/taskactionnotplanwithdays',
              type: "POST",
              data: {
              sid: selectstatusbyusernotplaned,
              task_action: task_action,
              daysfiltercard_anp_date: daysfiltercard_anp_date,
              uid: uid
              },
              cache: false,
              success: function a(result){
              $("#maintaskcard").show();
              $("#selectcompanybyuser").html(result);
              $("#selectcompanybyuser").show();
              var optionCount = $('#selectcompanybyuser').find('option').length;
              optionCount = optionCount-1;
              $("#totalcompany").text('Total Company :'+ optionCount);
              $("#tptime").show();
              $('#ntactionnew').show();
              $('#ntppose').show();
              $('#meeting-time').show();
              $('#planbtn1').show();
              $('#daysfiltercard_anp').show();
              }
              });
          });
      }else{
          $("#actionnotplaned").hide();
          $("#daysfiltercard_anp").hide();
          $('#ntactionnew option').prop('disabled', false);
      }
      if(val=='Status'){
          $("#maintaskcard").hide();
          $("#becauseof_topsender_pst_meet").hide();
          $("#actionPlanned").show();
          $("#actionnotplaned").hide();
          $('#ntactionnew').hide();
          $('#companyLocation').hide();
          $('#tasktaction').hide();
          $('#taskActionbyuser').hide();
          $('#taskPurposebyuser').hide();
          var uid = $("#curuserid").val();
          $("#selectstatus").show();
      
          $('#selectstatusbyuser').on('change', function() {
              var uid = $("#curuserid").val();
              $("#selectcompanybyuser").html('');
          var selectedValue = $(this).val();
              $("#daysByTask").show();
              $.ajax({
              url:'<?=base_url();?>Menu/getstatuscmp',
              type: "POST",
              data: {
              sid: selectedValue,
              uid: uid
              },
              cache: false,
              success: function a(result){
              $("#maintaskcard").show();
              $("#selectcompanybyuser").html(result);
              $("#selectcompanybyuser").show();
              var optionCount = $('#selectcompanybyuser').find('option').length;
              optionCount = optionCount-1;
              $("#totalcompany").text('Total Company :'+ optionCount);
              $("#tasktaction").show();
              $("#tptime").val('');
              $("#tptime").show();
              $('#ntactionnew').show();
              $('#ntppose').show();
              $('#meeting-time').show();
              $('#planbtn1').show();
              $("#taskplanningimg").hide();
              }
              });
          });
      
          $('#tasktaction').on('change', function() {
              var tasktaction = $(this).val();
              var selectedValue = $('#selectstatusbyuser').val();
              $.ajax({
              url:'<?=base_url();?>Menu/getstatuscmpwithtasktaction',
              type: "POST",
              data: {
              sid: selectedValue,
              tasktaction: tasktaction,
              uid: uid
              },
              cache: false,
              success: function a(result){
                  $("#selectcompanybyuser").html(result);
                  $("#selectcompanybyuser").show();
                  var optionCount = $('#selectcompanybyuser').find('option').length;
              optionCount = optionCount-1;
              $("#totalcompany").text('Total Company :'+ optionCount);
              $('#taskActionbyuser').show();
                  }
              });
              });
          $('#taskActionbyuser').on('change', function() {
      
              var taskActionbyuser = $(this).val();
              var tasktaction = $('#tasktaction').val();
              var selectedValue = $('#selectstatusbyuser').val();
              $.ajax({
              url:'<?=base_url();?>Menu/getTaskActionYesorNobyuser',
              type: "POST",
              data: {
              sid: selectedValue,
              tasktaction: tasktaction,
              taskActionbyuser: taskActionbyuser,
              uid: uid
              },
              cache: false,
              success: function a(result){
                  $("#selectcompanybyuser").html(result);
                  $("#selectcompanybyuser").show();
                  var optionCount = $('#selectcompanybyuser').find('option').length;
              optionCount = optionCount-1;
              $("#totalcompany").text('Total Company :'+ optionCount);
              $('#taskPurposebyuser').show();
                  }
              });
              });
      
              $('#taskPurposebyuser').on('change', function() {
              var taskPurposebyuser = $(this).val();
              var tasktaction = $('#tasktaction').val();
              var selectedValue = $('#selectstatusbyuser').val();
              var taskActionbyuser = $('#taskActionbyuser').val();
      
              $.ajax({
              url:'<?=base_url();?>Menu/getTaskPurposeYesorNobyuser',
              type: "POST",
              data: {
              sid: selectedValue,
              tasktaction: tasktaction,
              taskActionbyuser: taskActionbyuser,
              taskPurposebyuser: taskPurposebyuser,
              uid: uid
              },
              cache: false,
              success: function a(result){
                  $("#selectcompanybyuser").html(result);
                  $("#selectcompanybyuser").show();
                  $('#slct_days_status').show();
                  var optionCount = $('#selectcompanybyuser').find('option').length;
              optionCount = optionCount-1;
              $("#totalcompany").text('Total Company :'+ optionCount);
                  }
              });
              });
      
      
              $('#slct_days_status').on('change', function() {
              var slct_days_status = $(this).val();
              var uid = $("#curuserid").val();
              var tasktaction = $('#tasktaction').val();
              var selectedValue = $('#selectstatusbyuser').val();
              var taskActionbyuser = $('#taskActionbyuser').val();
              var taskPurposebyuser = $('#taskPurposebyuser').val();
              $.ajax({
              url:'<?=base_url();?>Menu/getTaskPurposeYesorNobyuser_status',
              type: "POST",
              data: {
              sid: selectedValue,
              tasktaction: tasktaction,
              taskActionbyuser: taskActionbyuser,
              taskPurposebyuser: taskPurposebyuser,
              slct_days_status: slct_days_status,
              uid: uid
              },
              cache: false,
              success: function a(result){
                  $("#selectcompanybyuser").html(result);
                  $("#selectcompanybyuser").show();
                  var optionCount = $('#selectcompanybyuser').find('option').length;
                  optionCount = optionCount-1;
                  $("#totalcompany").text('Total Company :'+ optionCount);
                  }
              });
              });
      
      
      
      }else{
        $('#slct_days_status').hide();
      }
      
      if(val=='Location'){
          $("#maintaskcard").hide();
          $("#actionPlanned").show();
          $("#actionnotplaned").hide();
          $('#ntactionnew').hide();
          $('#tasktaction').hide();
          $('#taskActionbyuser').hide();
          $('#taskPurposebyuser').hide();
          $("#selectstatus").hide();
          $('#companyLocationdata').show();
           $('#companyLocation').show();
           $("#companyLocationdatacard").show();
           $(".taskselectionarea").hide();
           var uid = $("#curuserid").val();
          $.ajax({
          url:'<?=base_url();?>Menu/getcmpbybylocation',
          type: "POST",
          data: {
          bylocation: 'bylocation',
          uid: uid
          },
          cache: false,
          success: function a(result){
          $("#companyLocation").html(result);
          }
          });
      
          $('#companyLocation').on('change', function() {
      
              var uid = $("#curuserid").val();
              $("#selectcompanybyuser").html('');
             var companyLocation = $(this).val();
              $.ajax({
              url:'<?=base_url();?>Menu/getcmpbyloc',
              type: "POST",
              data: {
              bylocation: companyLocation,
              uid: uid
              },
              cache: false,
              success: function a(result){
              $("#maintaskcard").show();
              $("#selectcompanybyuser").html(result);
              $("#selectcompanybyuser").show();
              var optionCount = $('#selectcompanybyuser').find('option').length;
              optionCount = optionCount-1;
              $("#totalcompany").text('Total Company :'+ optionCount);
              $("#tasktaction").hide();
              $("#tptime").val('');
              $("#tptime").show();
              $('#ntactionnew').show();
              $('#ntppose').show();
              $('#meeting-time').show();
              $('#planbtn1').show();
              $("#selectactionplanecard").show();
              $("#taskplanningimg").hide();
              }
              });
          });
      
          $('#selectactionplane').on('change', function() {
          var uid = $("#curuserid").val();
          $("#selectcompanybyuser").html('');
          var selectactionplane = $(this).val();
          if(selectactionplane !== 'Select Action'){
          var companyLocation = $('#companyLocation').val();
          $.ajax({
          url:'<?=base_url();?>Menu/getcmpbylocaction',
          type: "POST",
          data: {
          selectactionplane: selectactionplane,
          bylocation: companyLocation,
          uid: uid
          },
          cache: false,
          success: function a(result){
          $("#maintaskcard").show();
          $("#selectcompanybyuser").html(result);
          $("#selectcompanybyuser").show();
          var optionCount = $('#selectcompanybyuser').find('option').length;
          optionCount = optionCount-1;
          $("#totalcompany").text('Total Company :'+ optionCount);
          $("#tasktaction").hide();
          $("#tptime").val('');
          $("#tptime").show();
          $('#ntactionnew').show();
          $('#ntppose').show();
          $('#meeting-time').show();
          $('#planbtn1').show();
          $('#daysfiltercard').show();
          }
          });
      }else{
      }
        });
      }else{
          $("#companyLocationdatacard").hide();
          $(".taskselectionarea").show();
          $("#companyLocation").html('');
          $("#selectactionplanecard").hide();
          $("#daysfiltercard").hide();
      }
      
      if(val=='Category'){
      
      $(".taskselectionarea").hide();
      $("#maintaskcard").hide();
      $('#selectCategory').show();
      $("#maintaskcard").hide();
      $("#actionPlanned").hide();
      $("#actionnotplaned").hide();
      $('#ntactionnew').hide();
      $('#tasktaction').hide();
      $("#selectstatus").hide();
      $('#companyLocationdata').hide();
      $('#companyLocation').hide();
      $('#statusfiltercardCategory').hide();
          $('#selectdcategory').on('change', function() {
              var uid = $("#curuserid").val();
              $("#selectcompanybyuser").html('');
             var selectdcategory = $(this).val();
              $.ajax({
              url:'<?=base_url();?>Menu/getcmpbycategory',
              type: "POST",
              data: {
              category: selectdcategory,
              uid: uid
              },
              cache: false,
              success: function a(result){
              $("#maintaskcard").show();
              $("#selectcompanybyuser").html(result);
              $("#selectcompanybyuser").show();
              var optionCount = $('#selectcompanybyuser').find('option').length;
              // optionCount = optionCount-1;
              $("#totalcompany").text('Total Company :'+ optionCount);
              $("#slcategorycompanys").text('Total Company :'+ optionCount);
              $("#tasktaction").hide();
      
              $("#tptime").val('');
              $("#tptime").show();
              $('#ntactionnew').show();
              $('#ntppose').show();
              $('#meeting-time').show();
              $('#planbtn1').show();
              $('#statusfiltercardCategory').show();
              $("#taskplanningimg").hide();
              }
              });
          });
      
          $('#statusfiltercardCat').on('change', function() {
            var uid = $("#curuserid").val();
             var  selectdcategory = $('#selectdcategory').val();
             var statusfiltercardCat = $(this).val();
              $.ajax({
              url:'<?=base_url();?>Menu/getcmpbycategory12',
              type: "POST",
              data: {
              statusfiltercardCat: statusfiltercardCat,
              category: selectdcategory,
              uid: uid
              },
              cache: false,
              success: function a(result){
              $("#maintaskcard").show();
              $("#selectcompanybyuser").html(result);
              $("#selectcompanybyuser").show();
              var optionCount = $('#selectcompanybyuser').find('option').length;
              // optionCount = optionCount-1;
              $("#totalcompany").text('Total Company :'+ optionCount);
              $("#tasktaction").hide();
      
              $("#tptime").val('');
              $("#tptime").show();
              $('#ntactionnew').show();
              $('#ntppose').show();
              $('#meeting-time').show();
              $('#planbtn1').show();
              $('#taskActionbyusercatcatcard').show();
              }
              });
          });
      
          $('#taskActionbyusercat').on('change', function() {
              var uid = $("#curuserid").val();
              $("#selectcompanybyuser").html('');
             var selectdcategory = $('#selectdcategory').val();
             var statusfiltercardCat = $('#statusfiltercardCat').val();
             var taskActionbyusercatcat = $(this).val();
              $.ajax({
              url:'<?=base_url();?>Menu/getcmpbycategory13',
              type: "POST",
              data: {
              taskActionbyusercatcat: taskActionbyusercatcat,
              statusfiltercardCat: statusfiltercardCat,
              category: selectdcategory,
              uid: uid
              },
              cache: false,
              success: function a(result){
              $("#maintaskcard").show();
              $("#selectcompanybyuser").html(result);
              $("#selectcompanybyuser").show();
              var optionCount = $('#selectcompanybyuser').find('option').length;
              // optionCount = optionCount-1;
              $("#totalcompany").text('Total Company :'+ optionCount);
              $("#tasktaction").hide();
      
              $("#tptime").val('');
              $("#tptime").show();
              $('#ntactionnew').show();
              $('#ntppose').show();
              $('#meeting-time').show();
              $('#planbtn1').show();
              $('#taskPurposebyusercatcard').show();
              }
              });
          });
      
      
          $('#taskPurposebyusercatdata').on('change', function() {
              var uid = $("#curuserid").val();
              $("#selectcompanybyuser").html('');
             var taskActionbyusercat = $('#taskActionbyusercat').val();
             var selectdcategory = $('#selectdcategory').val();
             var statusfiltercardCat = $('#statusfiltercardCat').val();
             var taskPurposebyusercat = $(this).val();
             $.ajax({
              url:'<?=base_url();?>Menu/getcmpbycategory13',
              type: "POST",
              data: {
              taskPurposebyusercat: taskPurposebyusercat,
              taskActionbyusercatcat: taskActionbyusercat,
              statusfiltercardCat: statusfiltercardCat,
              category: selectdcategory,
              uid: uid
              },
              cache: false,
              success: function a(result){
              $("#maintaskcard").show();
              $("#selectcompanybyuser").html(result);
              $("#selectcompanybyuser").show();
              $("#taskPurposebyusercatcard_days").show();
              var optionCount = $('#selectcompanybyuser').find('option').length;
              // optionCount = optionCount-1;
              $("#totalcompany").text('Total Company :'+ optionCount);
              $("#tasktaction").hide();
              $("#tptime").val('');
              $("#tptime").show();
              $('#ntactionnew').show();
              $('#ntppose').show();
              $('#meeting-time').show();
              $('#planbtn1').show();   
              }
              });
          });
      
      
          $('#taskPurposebyusercatdata_days').on('change', function() {
              var slct_days_status = $(this).val();
              var uid = $("#curuserid").val();
      
              var selectedValue = $('#statusfiltercardCat').val();
              var taskActionbyuser = $('#taskActionbyusercat').val();
              var taskPurposebyuser = $('#taskPurposebyusercatdata').val();
              var selectdcategory = $('#selectdcategory').val();
              $.ajax({
              url:'<?=base_url();?>Menu/getTaskPurposeYesorNobyuser_status',
              type: "POST",
              data: {
              sid: selectedValue,
              selectdcategory: selectdcategory,
              taskActionbyuser: taskActionbyuser,
              taskPurposebyuser: taskPurposebyuser,
              slct_days_status: slct_days_status,
              uid: uid
              },
              cache: false,
              success: function a(result){
                  $("#selectcompanybyuser").html(result);
                  $("#selectcompanybyuser").show();
                  var optionCount = $('#selectcompanybyuser').find('option').length;
                  optionCount = optionCount-1;
                  $("#totalcompany").text('Total Company :'+ optionCount);
                  }
              });
              });
      
      
      
      
      }else{
          $('#selectCategory').hide();
          $("#taskPurposebyusercatcard_days").hide();
      }
      
      if(val == 'Cluster Location'){
      $(".taskselectionarea").hide();
      $("#maintaskcard").hide();
      $('#selectCategory').hide();
      $("#maintaskcard").hide();
      $("#actionPlanned").hide();
      $("#actionnotplaned").hide();
      $('#ntactionnew').hide();
      $('#tasktaction').hide();
      $("#selectstatus").hide();
      $('#companyLocationdata').hide();
      $('#companyLocation').hide();
      $('#statusfiltercardCategory').hide();
      $('#clusterLocactionFiltercard').show();
      $('#clusterNameLocaction').html('');
      $("#selectcompanybyuser").html('');
      
      $(".taskselectionarea").hide();
      var uid = $("#curuserid").val();
      $.ajax({
      url:'<?=base_url();?>Menu/getcmpbyClusterLocation',
      type: "POST",
      data: {
      bylocation: 'byclusterlocation',
      uid: uid
      },
      cache: false,
      success: function a(result){
      $("#clusterNameLocaction").html(result);
      }
      });
      
      $('#clusterNameLocaction').on('change', function() {
          var uid = $("#curuserid").val();
          $("#selectcompanybyuser").html('');
          var clusterid= $(this).val();
          var allowedValues = ['1', '2'];
          if ($(this).val() === '') {
            $('#ntactionnew').find('option').prop('disabled', false);
              } else {
                  $('#ntactionnew').find('option').each(function() {
                      var option = $(this);
                      if (allowedValues.includes(option.val())) {
                          option.prop('disabled', false);
                      } else {
                          option.prop('disabled', true);
                      }
                  });
              }
          $.ajax({
          url:'<?=base_url();?>Menu/getcmpbyClusterLocationid',
          type: "POST",
          data: {
          clusterid: clusterid,
          uid: uid
          },
          cache: false,
          success: function a(result){
          $("#maintaskcard").show();
          $("#selectcompanybyuser").html(result);
          $("#selectcompanybyuser").show();
          var optionCount = $('#selectcompanybyuser').find('option').length;
          if(optionCount == 1){
              optionCount = 0;
          }
          // optionCount = optionCount-1;
          $("#totalcompany").text('Total Company :'+ optionCount);
          $("#tasktaction").hide();
          $("#tptime").val('');
          $("#tptime").show();
          $('#ntactionnew').show();
          $('#ntppose').show();
          $('#meeting-time').show();
          $('#planbtn1').show();
          $('#statusfiltercardCluster').show();
          $("#taskplanningimg").hide();
          }
          });
          });
      
          $('#statusfilterCluster').on('change', function() {
          var uid = $("#curuserid").val();
          $("#selectcompanybyuser").html('');
          var clusterid= $('#clusterNameLocaction').val();
          var statusfilterCluster= $(this).val();
          $.ajax({
          url:'<?=base_url();?>Menu/getcmpbyClusteridWithStatus',
          type: "POST",
          data: {
          clusterid: clusterid,
          statusfilterCluster: statusfilterCluster,
          uid: uid
          },
          cache: false,
          success: function a(result){
          $("#maintaskcard").show();
          $("#selectcompanybyuser").html(result);
          $("#selectcompanybyuser").show();
          var optionCount = $('#selectcompanybyuser').find('option').length;
          if(optionCount == 1){
              optionCount = 0;
          }
          // optionCount = optionCount-1;
          $("#totalcompany").text('Total Company :'+ optionCount);
          $("#tasktaction").hide();
          $("#tptime").val('');
          $("#tptime").show();
          $('#ntactionnew').show();
          $('#ntppose').show();
          $('#meeting-time').show();
          $('#planbtn1').show();
          $('#taskActionbyuserCluster').show();
          }
          });
          });
      
          $('#taskActionbyCluster').on('change', function() {
          var uid = $("#curuserid").val();
          $("#selectcompanybyuser").html('');
          var clusterid= $('#clusterNameLocaction').val();
          var statusfilterCluster= $('#statusfilterCluster').val();
          var taskActionbyCluster= $(this).val();
          $.ajax({
          url:'<?=base_url();?>Menu/getcmpbyClusteridWithStatusWithAction',
          type: "POST",
          data: {
          clusterid: clusterid,
          statusfilterCluster: statusfilterCluster,
          taskActionbyCluster: taskActionbyCluster,
          uid: uid
          },
          cache: false,
          success: function a(result){
          $("#maintaskcard").show();
          $("#selectcompanybyuser").html(result);
          $("#selectcompanybyuser").show();
          var optionCount = $('#selectcompanybyuser').find('option').length;
          if(optionCount == 1){
              optionCount = 0;
          }
          // optionCount = optionCount-1;
          $("#totalcompany").text('Total Company :'+ optionCount);
          $("#tasktaction").hide();
      
          $("#tptime").val('');
          $("#tptime").show();
          $('#ntactionnew').show();
          $('#ntppose').show();
          $('#meeting-time').show();
          $('#planbtn1').show();
          $('#taskPurposebyuserCluster').show();
          }
          });
          });
          $('#taskPurposebyCluster').on('change', function() {
          var uid = $("#curuserid").val();
          $("#selectcompanybyuser").html('');
          var taskActionbyCluster= $('#taskActionbyCluster').val();
          var clusterid= $('#clusterNameLocaction').val();
          var statusfilterCluster= $('#statusfilterCluster').val();
          var taskPurposebyCluster= $(this).val();
          $.ajax({
          url:'<?=base_url();?>Menu/getcmpbyClusteridWithStatusWithActionPurpose',
          type: "POST",
          data: {
          clusterid: clusterid,
          statusfilterCluster: statusfilterCluster,
          taskActionbyCluster: taskActionbyCluster,
          taskPurposebyCluster: taskPurposebyCluster,
          uid: uid
          },
          cache: false,
          success: function a(result){
          $("#maintaskcard").show();
          $("#selectcompanybyuser").html(result);
          $("#selectcompanybyuser").show();
          var optionCount = $('#selectcompanybyuser').find('option').length;
          if(optionCount == 1){
              optionCount = 0;
          }
          // optionCount = optionCount-1;
          $("#totalcompany").text('Total Company :'+ optionCount);
          $("#tasktaction").hide();
      
          $("#tptime").val('');
          $("#tptime").show();
          $('#ntactionnew').show();
          $('#ntppose').show();
          $('#meeting-time').show();
          $('#planbtn1').show();
          $('#taskPurposebyuserCluster_days').show();
          }
          });
          });
      
      
          $('#taskPurposebyCluster_days').on('change', function() {
              var slct_days_status = $(this).val();
              var uid = $("#curuserid").val();
              var selectedValue = $('#statusfilterCluster').val();
              var clusterid = $('#clusterNameLocaction').val();
              var taskActionbyuser = $('#taskActionbyCluster').val();
              var taskPurposebyuser = $('#taskPurposebyCluster').val();
              $.ajax({
              url:'<?=base_url();?>Menu/getTaskPurposeYesorNobyuser_status',
              type: "POST",
              data: {
              sid: selectedValue,
              clusterid: clusterid,
              taskActionbyuser: taskActionbyuser,
              taskPurposebyuser: taskPurposebyuser,
              slct_days_status: slct_days_status,
              uid: uid
              },
              cache: false,
              success: function a(result){
                  $("#selectcompanybyuser").html(result);
                  $("#selectcompanybyuser").show();
                  var optionCount = $('#selectcompanybyuser').find('option').length;
                  optionCount = optionCount-1;
                  $("#totalcompany").text('Total Company :'+ optionCount);
                  }
              });
              });
      
      
      
      }else{
          $('#clusterLocactionFiltercard').hide();
          $('#taskPurposebyuserCluster_days').hide();
      }
      
      if(val == 'PST Assign'){
          var uid = $("#curuserid").val();
          $(".taskselectionarea").hide();
          $('#pstAssignCard').show();
          $('#pstAssignCardData').on('change', function() {
              var pstAssignfilter= $(this).val();
                  $.ajax({
                  url:'<?=base_url();?>Menu/getpstassigncmp',
                  type: "POST",
                  data: {
                  pstassign: pstAssignfilter,
                  uid: uid
                  },
                  cache: false,
                  success: function a(result){
                  $("#maintaskcard").show();
                  $("#selectcompanybyuser").html(result);
                  $("#selectcompanybyuser").show();
                  var optionCount = $('#selectcompanybyuser').find('option').length;
                  if(optionCount == 1){
                      optionCount = 0;
                  }
                  // optionCount = optionCount-1;
                  $("#totalcompany").text('Total Company :'+ optionCount);
                  $("#tasktaction").hide();
                  $("#tptime").val('');
                  $("#tptime").show();
                  $('#ntactionnew').show();
                  $('#ntppose').show();
                  $('#meeting-time').show();
                  $('#planbtn1').show();
                  $("#taskplanningimg").hide();
                  }
                  });
          });
      }else{
          $('#pstAssignCard').hide();
          
      }
      if(val == 'PST Assigned Not Worked'){
          $('#taskActionCard').show();
          var uid = $("#curuserid").val();
          $(".taskselectionarea").hide();
          $('#pstAssignCard').show();
      }
      
      if(val !== 'Task Action'){
        $('#ntactionnew option[value="3"]').remove();
        $('#ntactionnew option[value="4"]').remove();
      }
      if(val == 'Task Action'){
          var uid = $("#curuserid").val();
          $(".taskselectionarea").hide();
          $('#taskActionCard').show();
          $('#maintaskcard').hide();
          $('#taskaction_card_area').show();
          $('#task_action_filter').show();
          // $('#taskaction_card_area').hide();
          $('#taskPurposebyuserCard').hide();
          $('#taskActionbyuserCard').hide();
          $('#status_taskaction').hide();
          $.ajax({
            url:'<?=base_url();?>Menu/CheckCashIsAvailable',
            type: "POST",
            data: {
              slctactval:'TaskAction',
            },
            cache: false,
            success: function a(result){
              if(result == 0){
                $('#task_action_filter option[value="3"]').prop('disabled', true);
                $('#task_action_filter option[value="4"]').prop('disabled', true);
                $('#task_action_filter option[value="17"]').prop('disabled', true);
                $('#meetingrelmsg').text('* Please Purchase cash for create meeting task').css('color', 'red');
              }else{
                $('#task_action_filter option[value="3"]').prop('disabled', false);
                $('#task_action_filter option[value="4"]').prop('disabled', false);
                $('#task_action_filter option[value="17"]').prop('disabled', false);
                $('#meetingrelmsg').text('');
              }
            }
          });
          $('#task_action_filter').on('change', function() {
              $("#taskplanningimg").hide();
              $("#selectcompanybyuser").html('');
              $("#totalcompany").text('');
              var uid = $("#curuserid").val();
              var tasktaction = $(this).val();
              if(tasktaction ==3){


                  var avltimeslct     =   $('#getAvailableTime').val();
          var ntactionnew_val = tasktaction;

          if (avltimeslct !== null) {

                $.ajax({
                  url:'<?=base_url();?>Menu/getTaskAvailableTime',
                  type: "POST",
                  data: {
                    sdate: '<?= $adate; ?>',
                    avltimeslct: avltimeslct,
                    ntactionnew_val: ntactionnew_val
                  },
                  cache: false,
                  success: function a(result){
                    
                    $('#freeaslotDisplay').html(result).slideDown("fast");
                    $('#freeaslotDisplay span').css({
                        'background-color': 'rgb(10, 162, 18)',
                        'border-radius': '10px'
                    });
                    }
                  });


                  $.ajax({
                  url:'<?=base_url();?>Menu/getTaskAvailableTimeByTaskID',
                  type: "POST",
                  data: {
                    sdate: '<?= $adate; ?>',
                    avltimeslct: avltimeslct,
                    ntactionnew_val: ntactionnew_val
                  },
                  cache: false,
                  success: function a(result){
                    $('#meeting-time').html("");
                    $('#meeting-time').html(result);
                    $('#meeting-time option.text-success').css({ 'color': 'green' });
                    $('#meeting-time option.text-danger').css({ 'color': 'red' });
                  }
                  });
            }else{
                $.ajax({
                  url:'<?=base_url();?>Menu/getTaskAvailableTimeByTaskID',
                  type: "POST",
                  data: {
                    sdate: '<?= $adate; ?>',
                    avltimeslct: avltimeslct,
                    ntactionnew_val: ntactionnew_val
                  },
                  cache: false,
                  success: function a(result){
                    $('#meeting-time').html("");
                    $('#meeting-time').html(result);
                    $('#meeting-time option.text-success').css({ 'color': 'green' });
                    $('#meeting-time option.text-danger').css({ 'color': 'red' });
                  }
                  });
            }



                $("#ntppose").html("<option value=''>Select Purpose</option>");
                $('#status_taskaction_card').hide();
                $('#status_taskaction').hide();
                $('#taskActionbyuserCard').hide();
                $('#taskPurposebyuserCard').hide();
                $("#selectcompany").show();
                $("#bargmeetingcluster").show();
                $("#bcytpe").hide();
                $('#selectReseachCompanyType').hide();
                $.ajax({
                url:'<?=base_url();?>Menu/get_SheduledMeetCompany',
                type: "POST",
                data: {
                uid: uid
                },
                cache: false,
                success: function a(result){
                  $("#maintaskcard").show();
                  $("#selectcompanybyuser").html(result);
                  $("#selectcompanybyuser").show();
                  $("#select_cluster").show();
                  var optionCount = $('#selectcompanybyuser').find('option').length;
                  optionCount = optionCount-1;
                  $("#totalcompany").text('Total Company :'+ optionCount);
                  $("#tasktaction").show();
                  $("#tptime").val('');
                  $("#tptime").show();
                  $('#ntactionnew').show();
                  $('#ntppose').show();
                  $('#meeting-time').show();
                  $('#planbtn1').show();
                  $("#taskplanningimg").hide();
                var newOption = $('<option>', {
                    value: '3',
                    text: 'Scheduled Meeting',
                    selected: true,
                });
                $('#ntactionnew').append(newOption);
                $('#ntactionnew option').prop('disabled', true);
                $('#ntactionnew option[value="3"]').prop('disabled', false);
                var inidids = '';
                  $('#selectcompanybyuser').change(function() {
                  var inidids = '';
                  $('#selectcompanybyuser :selected').each(function(i, sel){
                      inidids += $(sel).val() + ',';
                  });
                  inidids = inidids.slice(0, -1);
                  $.ajax({
                    url:'<?=base_url();?>Menu/getpurposebyinidnew',
                    type: "POST",
                    data: {
                    inid: inidids,
                    aid: 3
                    },
                    cache: false,
                    success: function a(result){
                      console.log("2"+result);
                      $("#ntppose").html(result);
                    }
                    });
              });
                }
                });

              

                $('#bargmeetingcluster_slct').on('change', function() {
                var slsct_task_action_filter = $("#task_action_filter").val();
                if(slsct_task_action_filter == 3){

                  var meetingcluster_slct = $(this).val();
                  if(meetingcluster_slct == 'all' || meetingcluster_slct == ''){
                    $('#select_cluster option').prop('disabled', false);
                  }else if(meetingcluster_slct == 'no'){
                    $('#select_cluster option').prop('disabled', false);
                  }else{

                      $.ajax({
                      url:'<?=base_url();?>Menu/GetClusterOptionsOnPlanner',
                      type: "POST",
                      data: {
                      uid: uid,
                      travelType:'MainClusterFilter',
                      },
                      cache: false,
                      success: function a(result){
                        $("#select_cluster").html('');
                        $("#select_cluster").html(result);
                        $('#select_cluster').val(meetingcluster_slct);
                        $('#select_cluster option').not(':selected').prop('disabled', true);
                      }
                      });
                  }
               
                  $.ajax({
                  url:'<?=base_url();?>Menu/get_SheduledMeetCompany_with_cluster',
                  type: "POST",
                  data: {
                  uid: uid,
                  meetingcluster_slct: meetingcluster_slct,
                  task_action_filter : slsct_task_action_filter
                  },
                  cache: false,
                  success: function a(result){
                    $("#maintaskcard").show();
                    $("#selectcompanybyuser").html(result);
                    $("#selectcompanybyuser").show();
                    $("#select_cluster").show();
                    var optionCount = $('#selectcompanybyuser').find('option').length;
                    optionCount = optionCount-1;
                    $("#totalcompany").text('Total Company :'+ optionCount);
                    $("#tasktaction").show();
                    $("#tptime").val('');
                    $("#tptime").show();
                    $('#ntactionnew').show();
                    $('#ntppose').show();
                    $('#meeting-time').show();
                    $('#planbtn1').show();
                    $("#taskplanningimg").hide();
              
                  $('#ntactionnew option').prop('disabled', true);
                  $('#ntactionnew option[value="3"]').prop('disabled', false);
                  var inidids = '';
                    $('#selectcompanybyuser').change(function() {
                      var inidids = '';
                      $('#selectcompanybyuser :selected').each(function(i, sel){
                          inidids += $(sel).val() + ',';
                      });
                    inidids = inidids.slice(0, -1);
                      $.ajax({
                        url:'<?=base_url();?>Menu/getpurposebyinidnew',
                        type: "POST",
                        data: {
                        inid: inidids,
                        aid: 3
                        },
                        cache: false,
                        success: function a(result){
                          // console.log("3"+result);
                          $("#ntppose").html(result);
                        }
                        });
                    });
                  }
                  });
                }
                });


                $('#travelType_taskaction_slct_for_smeeting').on('change', function() {

                    var slsct_task_action_filter  = $("#task_action_filter").val();
                    var meetingcluster_slct       = $("#bargmeetingcluster_slct").val();

                    $("#bargmeetingcluster_slct, #status_taskaction_slct_for_smeeting, #bd_taskaction_slct_for_smeeting").val('');
                    

                    if(slsct_task_action_filter == 3){

                      var travelType_taskaction_slct_for_smeeting = $(this).val();

                          $.ajax({
                            url:'<?=base_url();?>Menu/GetClusterOptionsOnPlanner',
                            type: "POST",
                            data: {
                            uid: uid,
                            travelType: travelType_taskaction_slct_for_smeeting,
                            },
                            cache: false,
                            success: function a(result){
                              $("#bargmeetingcluster_slct").html('');
                              $("#bargmeetingcluster_slct").html(result);
                            }
                            });

                      if(meetingcluster_slct == 'all' || meetingcluster_slct == ''){
                        $('#select_cluster option').prop('disabled', false);
                      }else if(meetingcluster_slct == 'no'){
                        $('#select_cluster option').prop('disabled', false);
                      }else{
                        $('#select_cluster').val(meetingcluster_slct);
                        $('#select_cluster option').not(':selected').prop('disabled', true);
                      }

                      $.ajax({
                      url:'<?=base_url();?>Menu/get_SheduledMeetCompany_with_cluster_travel_type',
                      type: "POST",
                      data: {
                      uid: uid,
                      meetingcluster_slct: meetingcluster_slct,
                      travelType: travelType_taskaction_slct_for_smeeting,
                      task_action_filter : slsct_task_action_filter
                      },
                      cache: false,
                      success: function a(result){
                        $("#selectcompanybyuser").html('');
                        $("#maintaskcard").show();
                        $("#selectcompanybyuser").html(result);
                        $("#selectcompanybyuser").show();
                        $("#select_cluster").show();
                        var optionCount = $('#selectcompanybyuser').find('option').length;
                        optionCount = optionCount-1;
                        $("#totalcompany").text('Total Company :'+ optionCount);
                        $("#tasktaction").show();
                        $("#tptime").val('');
                        $("#tptime").show();
                        $('#ntactionnew').show();
                        $('#ntppose').show();
                        $('#meeting-time').show();
                        $('#planbtn1').show();
                        $("#taskplanningimg").hide();

                      $('#ntactionnew option').prop('disabled', true);
                      $('#ntactionnew option[value="3"]').prop('disabled', false);
                      var inidids = '';
                        $('#selectcompanybyuser').change(function() {
                          var inidids = '';
                          $('#selectcompanybyuser :selected').each(function(i, sel){
                              inidids += $(sel).val() + ',';
                          });
                        inidids = inidids.slice(0, -1);
                          $.ajax({
                            url:'<?=base_url();?>Menu/getpurposebyinidnew',
                            type: "POST",
                            data: {
                            inid: inidids,
                            aid: 3
                            },
                            cache: false,
                            success: function a(result){
                              // console.log("3"+result);
                              $("#ntppose").html(result);
                            }
                            });
                        });
                      }
                      });
                    }
                    });

                $('#status_taskaction_slct_for_smeeting').on('change', function() {

                    var slsct_task_action_filter   = $("#task_action_filter").val();
                    var meetingcluster_slct        = $("#bargmeetingcluster_slct").val();
                    var travelType                 = $("#travelType_taskaction_slct_for_smeeting").val();

                    if(slsct_task_action_filter == 3){

                      var status_taskaction_slct_for_smeeting = $(this).val();

                      if(meetingcluster_slct == 'all' || meetingcluster_slct == ''){
                        $('#select_cluster option').prop('disabled', false);
                      }else if(meetingcluster_slct == 'no'){
                        $('#select_cluster option').prop('disabled', false);
                      }else{
                        $('#select_cluster').val(meetingcluster_slct);
                        $('#select_cluster option').not(':selected').prop('disabled', true);
                      }

                      if(travelType == ''){
                        alert("Please Select Travel Type");
                        return false;
                      }
                      
                      $.ajax({
                      url:'<?=base_url();?>Menu/get_SheduledMeetCompany_with_cluster_status',
                      type: "POST",
                      data: {
                      uid: uid,
                      meetingcluster_slct: meetingcluster_slct,
                      status_taskaction_slct_for_smeeting: status_taskaction_slct_for_smeeting,
                      travelType : travelType,
                      task_action_filter : slsct_task_action_filter
                      },
                      cache: false,
                      success: function a(result){
                        $("#selectcompanybyuser").html('');
                        $("#maintaskcard").show();
                        $("#selectcompanybyuser").html(result);
                        $("#selectcompanybyuser").show();
                        $("#select_cluster").show();
                        var optionCount = $('#selectcompanybyuser').find('option').length;
                        optionCount = optionCount-1;
                        $("#totalcompany").text('Total Company :'+ optionCount);
                        $("#tasktaction").show();
                        $("#tptime").val('');
                        $("#tptime").show();
                        $('#ntactionnew').show();
                        $('#ntppose').show();
                        $('#meeting-time').show();
                        $('#planbtn1').show();
                        $("#taskplanningimg").hide();

                      $('#ntactionnew option').prop('disabled', true);
                      $('#ntactionnew option[value="3"]').prop('disabled', false);
                      var inidids = '';
                        $('#selectcompanybyuser').change(function() {
                          var inidids = '';
                          $('#selectcompanybyuser :selected').each(function(i, sel){
                              inidids += $(sel).val() + ',';
                          });
                        inidids = inidids.slice(0, -1);
                          $.ajax({
                            url:'<?=base_url();?>Menu/getpurposebyinidnew',
                            type: "POST",
                            data: {
                            inid: inidids,
                            aid: 3
                            },
                            cache: false,
                            success: function a(result){
                              // console.log("3"+result);
                              $("#ntppose").html(result);
                            }
                            });
                        });
                      }
                      });
                    }
                    });

                    $('#bd_taskaction_slct_for_smeeting').on('change', function() {

                        var slsct_task_action_filter  = $("#task_action_filter").val();
                        var meetingcluster_slct       = $("#bargmeetingcluster_slct").val();
                        var status_taskaction_slct_for_smeeting = $("#status_taskaction_slct_for_smeeting").val();
                        var travelType                = $("#travelType_taskaction_slct_for_smeeting").val();

                        if(slsct_task_action_filter == 3){

                          var bd_taskaction_slct_for_smeeting = $(this).val();

                          if(meetingcluster_slct == 'all' || meetingcluster_slct == ''){
                            $('#select_cluster option').prop('disabled', false);
                          }else if(meetingcluster_slct == 'no'){
                            $('#select_cluster option').prop('disabled', false);
                          }else{
                            $('#select_cluster').val(meetingcluster_slct);
                            $('#select_cluster option').not(':selected').prop('disabled', true);
                          }
                          if(bd_taskaction_slct_for_smeeting == ''){
                            alert("Please Select BD");
                            return false;
                          }
                
                          $.ajax({
                          url:'<?=base_url();?>Menu/get_SheduledMeetCompany_with_cluster_status_BD',
                          type: "POST",
                          data: {
                          uid: uid,
                          meetingcluster_slct: meetingcluster_slct,
                          status_taskaction_slct_for_smeeting: status_taskaction_slct_for_smeeting,
                          bd_taskaction_slct_for_smeeting: bd_taskaction_slct_for_smeeting,
                          travelType : travelType,
                          task_action_filter : slsct_task_action_filter
                          },
                          cache: false,
                          success: function a(result){
                            $("#selectcompanybyuser").html('');
                            $("#maintaskcard").show();
                            $("#selectcompanybyuser").html(result);
                            $("#selectcompanybyuser").show();
                            $("#select_cluster").show();
                            var optionCount = $('#selectcompanybyuser').find('option').length;
                            optionCount = optionCount-1;
                            $("#totalcompany").text('Total Company :'+ optionCount);
                            $("#tasktaction").show();
                            $("#tptime").val('');
                            $("#tptime").show();
                            $('#ntactionnew').show();
                            $('#ntppose').show();
                            $('#meeting-time').show();
                            $('#planbtn1').show();
                            $("#taskplanningimg").hide();

                          $('#ntactionnew option').prop('disabled', true);
                          $('#ntactionnew option[value="3"]').prop('disabled', false);
                          var inidids = '';
                            $('#selectcompanybyuser').change(function() {
                              var inidids = '';
                              $('#selectcompanybyuser :selected').each(function(i, sel){
                                  inidids += $(sel).val() + ',';
                              });
                            inidids = inidids.slice(0, -1);
                              $.ajax({
                                url:'<?=base_url();?>Menu/getpurposebyinidnew',
                                type: "POST",
                                data: {
                                inid: inidids,
                                aid: 3
                                },
                                cache: false,
                                success: function a(result){
                                  // console.log("3"+result);
                                  $("#ntppose").html(result);
                                }
                                });
                            });
                          }
                          });
                        }
                        });

$('#new_category_filter_select_cluster').on('change', function() {

                        var slsct_task_action_filter  = $("#task_action_filter").val();
                        var meetingcluster_slct       = $("#bargmeetingcluster_slct").val();
                        var status_taskaction_slct_for_smeeting = $("#status_taskaction_slct_for_smeeting").val();
                        var travelType                = $("#travelType_taskaction_slct_for_smeeting").val();

                        if(slsct_task_action_filter == 3){

                          var bd_taskaction_slct_for_smeeting = $("#bd_taskaction_slct_for_smeeting").val();
                          var new_category_filter_select_cluster = $(this).val();

                          if(meetingcluster_slct == 'all' || meetingcluster_slct == ''){
                            $('#select_cluster option').prop('disabled', false);
                          }else if(meetingcluster_slct == 'no'){
                            $('#select_cluster option').prop('disabled', false);
                          }else{
                            $('#select_cluster').val(meetingcluster_slct);
                            $('#select_cluster option').not(':selected').prop('disabled', true);
                          }
                          if(bd_taskaction_slct_for_smeeting == ''){
                            alert("Please Select BD");
                            return false;
                          }
                
                          $.ajax({
                          url:'<?=base_url();?>Menu/get_SheduledMeetCompany_with_cluster_status_BD_newCategory',
                          type: "POST",
                          data: {
                          uid: uid,
                          meetingcluster_slct: meetingcluster_slct,
                          status_taskaction_slct_for_smeeting: status_taskaction_slct_for_smeeting,
                          bd_taskaction_slct_for_smeeting: bd_taskaction_slct_for_smeeting,
                          travelType : travelType,
                          task_action_filter : slsct_task_action_filter,
                          new_category_filter_select_cluster : new_category_filter_select_cluster
                          },
                          cache: false,
                          success: function a(result){
                            $("#selectcompanybyuser").html('');
                            $("#maintaskcard").show();
                            $("#selectcompanybyuser").html(result);
                            $("#selectcompanybyuser").show();
                            $("#select_cluster").show();
                            var optionCount = $('#selectcompanybyuser').find('option').length;
                            optionCount = optionCount-1;
                            $("#totalcompany").text('Total Company :'+ optionCount);
                            $("#tasktaction").show();
                            $("#tptime").val('');
                            $("#tptime").show();
                            $('#ntactionnew').show();
                            $('#ntppose').show();
                            $('#meeting-time').show();
                            $('#planbtn1').show();
                            $("#taskplanningimg").hide();

                          $('#ntactionnew option').prop('disabled', true);
                          $('#ntactionnew option[value="3"]').prop('disabled', false);
                          var inidids = '';
                            $('#selectcompanybyuser').change(function() {
                              var inidids = '';
                              $('#selectcompanybyuser :selected').each(function(i, sel){
                                  inidids += $(sel).val() + ',';
                              });
                            inidids = inidids.slice(0, -1);
                              $.ajax({
                                url:'<?=base_url();?>Menu/getpurposebyinidnew',
                                type: "POST",
                                data: {
                                inid: inidids,
                                aid: 3
                                },
                                cache: false,
                                success: function a(result){
                                  // console.log("3"+result);
                                  $("#ntppose").html(result);
                                }
                                });
                            });
                          }
                          });
                        }
                        });






                
              }else if(tasktaction ==4){

          var avltimeslct     =   $('#getAvailableTime').val();
          var ntactionnew_val = tasktaction;

          if (avltimeslct !== null) {

                $.ajax({
                  url:'<?=base_url();?>Menu/getTaskAvailableTime',
                  type: "POST",
                  data: {
                    sdate: '<?= $adate; ?>',
                    avltimeslct: avltimeslct,
                    ntactionnew_val: ntactionnew_val
                  },
                  cache: false,
                  success: function a(result){
                    
                    $('#freeaslotDisplay').html(result).slideDown("fast");
                    $('#freeaslotDisplay span').css({
                        'background-color': 'rgb(10, 162, 18)',
                        'border-radius': '10px'
                    });
                    }
                  });


                  $.ajax({
                  url:'<?=base_url();?>Menu/getTaskAvailableTimeByTaskID',
                  type: "POST",
                  data: {
                    sdate: '<?= $adate; ?>',
                    avltimeslct: avltimeslct,
                    ntactionnew_val: ntactionnew_val
                  },
                  cache: false,
                  success: function a(result){
                    $('#meeting-time').html("");
                    $('#meeting-time').html(result);
                    $('#meeting-time option.text-success').css({ 'color': 'green' });
                    $('#meeting-time option.text-danger').css({ 'color': 'red' });
                  }
                  });
            }else{
                $.ajax({
                  url:'<?=base_url();?>Menu/getTaskAvailableTimeByTaskID',
                  type: "POST",
                  data: {
                    sdate: '<?= $adate; ?>',
                    avltimeslct: avltimeslct,
                    ntactionnew_val: ntactionnew_val
                  },
                  cache: false,
                  success: function a(result){
                    $('#meeting-time').html("");
                    $('#meeting-time').html(result);
                    $('#meeting-time option.text-success').css({ 'color': 'green' });
                    $('#meeting-time option.text-danger').css({ 'color': 'red' });
                  }
                  });
            }


                $("#bargmeetingcluster").hide();
                $("#ntppose").html("<option value='34'>Fresh Meeting</option>");
                $('#status_taskaction_card').hide();
                $('#status_taskaction').hide();
                $('#taskActionbyuserCard').hide();
                $('#taskPurposebyuserCard').hide();
                $('#maintaskcard').hide();
                $("#bcytpe").show();
                $('#selectbarginCompanyType').show();
                $('#selectReseachCompanyType').hide();
                $("#taskplanningimg").show();
                
                $('#bcytpe').on('change', function() {
                  var bcytpe = $(this).val();
                if(bcytpe == 'From Funnel'){
                  $("#taskplanningimg").hide();
                  $('#maintaskcard').show();
                  $("#selectcompany").show();
                  $("#bargmeetingcluster").show();
                $.ajax({
                url:'<?=base_url();?>Menu/get_BargeMeetCompany',
                type: "POST",
                data: {
                uid: uid
                },
                cache: false,
                success: function a(result){
                  $("#maintaskcard").show();
                  $("#selectcompanybyuser").html(result);
                  $("#selectcompanybyuser").show();
                  $("#select_cluster").show();
                  var optionCount = $('#selectcompanybyuser').find('option').length;
                  optionCount = optionCount-1;
                  $("#totalcompany").text('Total Company :'+ optionCount);
                  $("#tasktaction").show();
                  $("#tptime").val('');
                  $("#tptime").show();
                  $('#ntactionnew').show();
                  $('#ntppose').show();
                  $('#meeting-time').show();
                  $('#planbtn1').show();
                  $('#ntactionnew option[value="4"]').first().remove();
                var newOption = $('<option>', {
                    value: '4',
                    text: 'Barg in Meeting',
                    selected: true,
                });
                $('#ntactionnew').append(newOption);
                $("#ntppose").html("<option value='135'>Remeeting</option>");
                $('#ntactionnew option').prop('disabled', true);
                // $('#ntactionnew option[value="4"]').prop('disabled', false);
                // $('#ntactionnew option:selected').prop('disabled', false); 
                 $('#ntactionnew option').prop('disabled', true);
                  $('#ntactionnew option[value="4"]').prop('disabled', false);
                }
                });

               
              
               
                $('#bargmeetingcluster_slct').on('change', function() {
                var slsct_task_action_filter = $("#task_action_filter").val();
                if(slsct_task_action_filter == 4){

                  var meetingcluster_slct = $(this).val();
                  if(meetingcluster_slct == 'all' || meetingcluster_slct == ''){
                    $('#select_cluster option').prop('disabled', false);
                  }else if(meetingcluster_slct == 'no'){
                    $('#select_cluster option').prop('disabled', false);
                  }else{

                      $.ajax({
                      url:'<?=base_url();?>Menu/GetClusterOptionsOnPlanner',
                      type: "POST",
                      data: {
                      uid: uid,
                      travelType:'MainClusterFilter'
                      },
                      cache: false,
                      success: function a(result){
                        $("#select_cluster").html('');
                        $("#select_cluster").html(result);
                        $('#select_cluster').val(meetingcluster_slct);
                        $('#select_cluster option').not(':selected').prop('disabled', true);

                        // $('#ntactionnew option:selected').prop('disabled', false); 
                        $('#ntactionnew option').prop('disabled', true); // Disable all options
                        $('#ntactionnew option:selected').prop('disabled', false); // Enable only the selected one
                      }
                      });
                  }
               
                  $.ajax({
                  url:'<?=base_url();?>Menu/get_SheduledMeetCompany_with_cluster',
                  type: "POST",
                  data: {
                  uid: uid,
                  meetingcluster_slct: meetingcluster_slct,
                  task_action_filter : slsct_task_action_filter
                  },
                  cache: false,
                  success: function a(result){
                    $("#maintaskcard").show();
                    $("#selectcompanybyuser").html(result);
                    $("#selectcompanybyuser").show();
                    $("#select_cluster").show();
                    var optionCount = $('#selectcompanybyuser').find('option').length;
                    optionCount = optionCount-1;
                    $("#totalcompany").text('Total Company :'+ optionCount);
                    $("#tasktaction").show();
                    $("#tptime").val('');
                    $("#tptime").show();
                    $('#ntactionnew').show();
                    $('#ntppose').show();
                    $('#meeting-time').show();
                    $('#planbtn1').show();
                    $("#taskplanningimg").hide();
              
                    $('#ntactionnew option').prop('disabled', true);
                    $('#ntactionnew option[value="4"]').prop('disabled', false);
                  var inidids = '';
                    $('#selectcompanybyuser').change(function() {
                      var inidids = '';
                      $('#selectcompanybyuser :selected').each(function(i, sel){
                          inidids += $(sel).val() + ',';
                      });
                    inidids = inidids.slice(0, -1);
                      $.ajax({
                        url:'<?=base_url();?>Menu/getpurposebyinidnew',
                        type: "POST",
                        data: {
                        inid: inidids,
                        aid: 3
                        },
                        cache: false,
                        success: function a(result){
                          // console.log("3"+result);
                          $("#ntppose").html(result);
                        }
                        });
                    });
                  }
                  });
                }
                });


                $('#travelType_taskaction_slct_for_smeeting').on('change', function() {

                    var slsct_task_action_filter  = $("#task_action_filter").val();
                    var meetingcluster_slct       = $("#bargmeetingcluster_slct").val();

                    $("#bargmeetingcluster_slct, #status_taskaction_slct_for_smeeting, #bd_taskaction_slct_for_smeeting").val('');
                    

                    if(slsct_task_action_filter == 4){

                      var travelType_taskaction_slct_for_smeeting = $(this).val();

                          $.ajax({
                            url:'<?=base_url();?>Menu/GetClusterOptionsOnPlanner',
                            type: "POST",
                            data: {
                            uid: uid,
                            travelType: travelType_taskaction_slct_for_smeeting
                            },
                            cache: false,
                            success: function a(result){
                              $("#bargmeetingcluster_slct").html('');
                              $("#bargmeetingcluster_slct").html(result);
                            }
                            });

                      if(meetingcluster_slct == 'all' || meetingcluster_slct == ''){
                        $('#select_cluster option').prop('disabled', false);
                      }else if(meetingcluster_slct == 'no'){
                        $('#select_cluster option').prop('disabled', false);
                      }else{
                        $('#select_cluster').val(meetingcluster_slct);
                        $('#select_cluster option').not(':selected').prop('disabled', true);
                      }

                      $.ajax({
                      url:'<?=base_url();?>Menu/get_SheduledMeetCompany_with_cluster_travel_type',
                      type: "POST",
                      data: {
                      uid: uid,
                      meetingcluster_slct: meetingcluster_slct,
                      travelType: travelType_taskaction_slct_for_smeeting,
                      task_action_filter : slsct_task_action_filter
                      },
                      cache: false,
                      success: function a(result){
                        $("#selectcompanybyuser").html('');
                        $("#maintaskcard").show();
                        $("#selectcompanybyuser").html(result);
                        $("#selectcompanybyuser").show();
                        $("#select_cluster").show();
                        var optionCount = $('#selectcompanybyuser').find('option').length;
                        optionCount = optionCount-1;
                        $("#totalcompany").text('Total Company :'+ optionCount);
                        $("#tasktaction").show();
                        $("#tptime").val('');
                        $("#tptime").show();
                        $('#ntactionnew').show();
                        $('#ntppose').show();
                        $('#meeting-time').show();
                        $('#planbtn1').show();
                        $("#taskplanningimg").hide();

                        $('#ntactionnew option').prop('disabled', true);
                        $('#ntactionnew option[value="4"]').prop('disabled', false);
                      var inidids = '';
                        $('#selectcompanybyuser').change(function() {
                          var inidids = '';
                          $('#selectcompanybyuser :selected').each(function(i, sel){
                              inidids += $(sel).val() + ',';
                          });
                        inidids = inidids.slice(0, -1);
                          $.ajax({
                            url:'<?=base_url();?>Menu/getpurposebyinidnew',
                            type: "POST",
                            data: {
                            inid: inidids,
                            aid: 3
                            },
                            cache: false,
                            success: function a(result){
                              // console.log("3"+result);
                              $("#ntppose").html(result);
                            }
                            });
                        });
                      }
                      });
                    }
                    });

                $('#status_taskaction_slct_for_smeeting').on('change', function() {

                    var slsct_task_action_filter   = $("#task_action_filter").val();
                    var meetingcluster_slct        = $("#bargmeetingcluster_slct").val();
                    var travelType                 = $("#travelType_taskaction_slct_for_smeeting").val();

                    if(slsct_task_action_filter == 4){

                      var status_taskaction_slct_for_smeeting = $(this).val();

                      if(meetingcluster_slct == 'all' || meetingcluster_slct == ''){
                        $('#select_cluster option').prop('disabled', false);
                      }else if(meetingcluster_slct == 'no'){
                        $('#select_cluster option').prop('disabled', false);
                      }else{
                        $('#select_cluster').val(meetingcluster_slct);
                        $('#select_cluster option').not(':selected').prop('disabled', true);
                      }

                      if(travelType == ''){
                        alert("Please Select Travel Type");
                        return false;
                      }
                      
                      $.ajax({
                      url:'<?=base_url();?>Menu/get_SheduledMeetCompany_with_cluster_status',
                      type: "POST",
                      data: {
                      uid: uid,
                      meetingcluster_slct: meetingcluster_slct,
                      status_taskaction_slct_for_smeeting: status_taskaction_slct_for_smeeting,
                      travelType : travelType,
                      task_action_filter : slsct_task_action_filter
                      },
                      cache: false,
                      success: function a(result){
                        $("#selectcompanybyuser").html('');
                        $("#maintaskcard").show();
                        $("#selectcompanybyuser").html(result);
                        $("#selectcompanybyuser").show();
                        $("#select_cluster").show();
                        var optionCount = $('#selectcompanybyuser').find('option').length;
                        optionCount = optionCount-1;
                        $("#totalcompany").text('Total Company :'+ optionCount);
                        $("#tasktaction").show();
                        $("#tptime").val('');
                        $("#tptime").show();
                        $('#ntactionnew').show();
                        $('#ntppose').show();
                        $('#meeting-time').show();
                        $('#planbtn1').show();
                        $("#taskplanningimg").hide();

                          $('#ntactionnew option').prop('disabled', true);
                          $('#ntactionnew option[value="4"]').prop('disabled', false);
                      var inidids = '';
                        $('#selectcompanybyuser').change(function() {
                          var inidids = '';
                          $('#selectcompanybyuser :selected').each(function(i, sel){
                              inidids += $(sel).val() + ',';
                          });
                        inidids = inidids.slice(0, -1);
                          $.ajax({
                            url:'<?=base_url();?>Menu/getpurposebyinidnew',
                            type: "POST",
                            data: {
                            inid: inidids,
                            aid: 3
                            },
                            cache: false,
                            success: function a(result){
                              // console.log("3"+result);
                              $("#ntppose").html(result);
                            }
                            });
                        });
                      }
                      });
                    }
                    });

                    $('#bd_taskaction_slct_for_smeeting').on('change', function() {

                        var slsct_task_action_filter  = $("#task_action_filter").val();
                        var meetingcluster_slct       = $("#bargmeetingcluster_slct").val();
                        var status_taskaction_slct_for_smeeting = $("#status_taskaction_slct_for_smeeting").val();
                        var travelType                = $("#travelType_taskaction_slct_for_smeeting").val();

                        if(slsct_task_action_filter == 4){

                          var bd_taskaction_slct_for_smeeting = $(this).val();

                          if(meetingcluster_slct == 'all' || meetingcluster_slct == ''){
                            $('#select_cluster option').prop('disabled', false);
                          }else if(meetingcluster_slct == 'no'){
                            $('#select_cluster option').prop('disabled', false);
                          }else{
                            $('#select_cluster').val(meetingcluster_slct);
                            $('#select_cluster option').not(':selected').prop('disabled', true);
                          }
                          if(bd_taskaction_slct_for_smeeting == ''){
                            alert("Please Select BD");
                            return false;
                          }
                
                          $.ajax({
                          url:'<?=base_url();?>Menu/get_SheduledMeetCompany_with_cluster_status_BD',
                          type: "POST",
                          data: {
                          uid: uid,
                          meetingcluster_slct: meetingcluster_slct,
                          status_taskaction_slct_for_smeeting: status_taskaction_slct_for_smeeting,
                          bd_taskaction_slct_for_smeeting: bd_taskaction_slct_for_smeeting,
                          travelType : travelType,
                          task_action_filter : slsct_task_action_filter
                          },
                          cache: false,
                          success: function a(result){
                            $("#selectcompanybyuser").html('');
                            $("#maintaskcard").show();
                            $("#selectcompanybyuser").html(result);
                            $("#selectcompanybyuser").show();
                            $("#select_cluster").show();
                            var optionCount = $('#selectcompanybyuser').find('option').length;
                            optionCount = optionCount-1;
                            $("#totalcompany").text('Total Company :'+ optionCount);
                            $("#tasktaction").show();
                            $("#tptime").val('');
                            $("#tptime").show();
                            $('#ntactionnew').show();
                            $('#ntppose').show();
                            $('#meeting-time').show();
                            $('#planbtn1').show();
                            $("#taskplanningimg").hide();

                            $('#ntactionnew option').prop('disabled', true);
                            $('#ntactionnew option[value="4"]').prop('disabled', false);
                          var inidids = '';
                            $('#selectcompanybyuser').change(function() {
                              var inidids = '';
                              $('#selectcompanybyuser :selected').each(function(i, sel){
                                  inidids += $(sel).val() + ',';
                              });
                            inidids = inidids.slice(0, -1);
                              $.ajax({
                                url:'<?=base_url();?>Menu/getpurposebyinidnew',
                                type: "POST",
                                data: {
                                inid: inidids,
                                aid: 3
                                },
                                cache: false,
                                success: function a(result){
                                  // console.log("3"+result);
                                  $("#ntppose").html(result);
                                }
                                });
                            });
                          }
                          });
                        }
                        });

                
                 
                        $('#new_category_filter_select_cluster').on('change', function() {

                        var slsct_task_action_filter  = $("#task_action_filter").val();
                        var meetingcluster_slct       = $("#bargmeetingcluster_slct").val();
                        var status_taskaction_slct_for_smeeting = $("#status_taskaction_slct_for_smeeting").val();
                        var travelType                = $("#travelType_taskaction_slct_for_smeeting").val();

                          var bd_taskaction_slct_for_smeeting = $("#bd_taskaction_slct_for_smeeting").val();
                          var new_category_filter_select_cluster = $(this).val();

                          if(meetingcluster_slct == 'all' || meetingcluster_slct == ''){
                            $('#select_cluster option').prop('disabled', false);
                          }else if(meetingcluster_slct == 'no'){
                            $('#select_cluster option').prop('disabled', false);
                          }else{
                            $('#select_cluster').val(meetingcluster_slct);
                            $('#select_cluster option').not(':selected').prop('disabled', true);
                          }
                          if(bd_taskaction_slct_for_smeeting == ''){
                            alert("Please Select BD");
                            return false;
                          }
                
                          $.ajax({
                          url:'<?=base_url();?>Menu/get_SheduledMeetCompany_with_cluster_status_BD_newCategory',
                          type: "POST",
                          data: {
                          uid: uid,
                          meetingcluster_slct: meetingcluster_slct,
                          status_taskaction_slct_for_smeeting: status_taskaction_slct_for_smeeting,
                          bd_taskaction_slct_for_smeeting: bd_taskaction_slct_for_smeeting,
                          travelType : travelType,
                          task_action_filter : slsct_task_action_filter,
                          new_category_filter_select_cluster : new_category_filter_select_cluster
                          },
                          cache: false,
                          success: function a(result){
                            $("#selectcompanybyuser").html('');
                            $("#maintaskcard").show();
                            $("#selectcompanybyuser").html(result);
                            $("#selectcompanybyuser").show();
                            $("#select_cluster").show();
                            var optionCount = $('#selectcompanybyuser').find('option').length;
                            optionCount = optionCount-1;
                            $("#totalcompany").text('Total Company :'+ optionCount);
                            $("#tasktaction").show();
                            $("#tptime").val('');
                            $("#tptime").show();
                            $('#ntactionnew').show();
                            $('#ntppose').show();
                            $('#meeting-time').show();
                            $('#planbtn1').show();
                            $("#taskplanningimg").hide();

                          $('#ntactionnew option').prop('disabled', true);
                          $('#ntactionnew option[value="3"]').prop('disabled', false);
                          var inidids = '';
                            $('#selectcompanybyuser').change(function() {
                              var inidids = '';
                              $('#selectcompanybyuser :selected').each(function(i, sel){
                                  inidids += $(sel).val() + ',';
                              });
                            inidids = inidids.slice(0, -1);
                              $.ajax({
                                url:'<?=base_url();?>Menu/getpurposebyinidnew',
                                type: "POST",
                                data: {
                                inid: inidids,
                                aid: 3
                                },
                                cache: false,
                                success: function a(result){
                                  // console.log("3"+result);
                                  $("#ntppose").html(result);
                                }
                                });
                            });
                          }
                          });
                        });
                
                

                  }else if(bcytpe == 'Other'){
                    $("#bargmeetingcluster").hide();
                    $("#taskplanningimg").hide();
                    $("#maintaskcard").show();
                    $("#selectcompany").hide();
                    $("#select_cluster").show();
                    $("#tasktaction").show();
                    $("#tptime").val('');
                    $("#tptime").show();
                    $('#ntactionnew').show();
                    $('#ntppose').show();
                    $('#meeting-time').show();
                    $('#planbtn1').show();
                    $('#select_cluster option').prop('disabled', false);
                    $('#ntactionnew option[value="4"]').first().remove();
                    var newOption = $('<option>', {
                    value: '4',
                    text: 'Barg in Meeting',
                    selected: true,
                });
                $('#ntactionnew option').prop('disabled', true);
                $('#ntactionnew option[value="4"]').prop('disabled', false);
                $('#ntactionnew').append(newOption);
                    $("#ntppose").html("<option value='34'>Fresh Meeting</option>");
                    $('#selectcompanybyuser').removeAttr('required');
                  }
                });
              }else if(tasktaction ==17){
                $("#ntppose").html("<option value=''>Select Purpose</option>");
                $('#status_taskaction_card').hide();
                $('#status_taskaction').hide();
                $('#taskActionbyuserCard').hide();
                $('#taskPurposebyuserCard').hide();
                $("#selectcompany").show();
                $("#bcytpe").hide();
                $('#selectReseachCompanyType').hide();
                $('#ntactionnew option').prop('disabled', true);
                $('#ntactionnew option[value="17"]').prop('disabled', false);
                
                $.ajax({
                url:'<?=base_url();?>Menu/get_JoinMeetingsCompany',
                type: "POST",
                data: {
                uid: uid
                },
                cache: false,
                success: function a(result){
                  $("#maintaskcard").show();
                  $("#selectcompanybyuser").html(result);
                  $("#selectcompanybyuser").show();
                  $("#select_cluster").show();
                  var optionCount = $('#selectcompanybyuser').find('option').length;
                  optionCount = optionCount-1;
                  $("#totalcompany").text('Total Company :'+ optionCount);
                  $("#tasktaction").show();
                  $("#tptime").val('');
                  $("#tptime").show();
                  $('#ntactionnew').show();
                  $('#ntppose').show();
                  $('#meeting-time').show();
                  $('#planbtn1').show();
                  $('#select_with_bd_pst').show();
                var newOption = $('<option>', {
                    value: '17',
                    text: 'Join Meeting',
                    selected: true,
                });
                $('#ntactionnew option').prop('disabled', true);
                $('#ntactionnew option[value="17"]').prop('disabled', false);
                $('#ntactionnew').append(newOption);
                  var inidids = '';
                  $('#selectcompanybyuser').change(function() {
                  var inidids = '';
                  $('#selectcompanybyuser :selected').each(function(i, sel){
                      inidids += $(sel).val() + ',';
                  });
                  inidids = inidids.slice(0, -1);
                  $.ajax({
                    url:'<?=base_url();?>Menu/getpurposebyinidnew',
                    type: "POST",
                    data: {
                    inid: inidids,
                    aid: 3
                    },
                    cache: false,
                    success: function a(result){
                      console.log("4"+result);
                      $("#ntppose").html(result);
                    }
                    });
                   });
                }
                });
              }else if(tasktaction ==10){

                var avltimeslct     =   $('#getAvailableTime').val();
          var ntactionnew_val = tasktaction;

          if (avltimeslct !== null) {

                $.ajax({
                  url:'<?=base_url();?>Menu/getTaskAvailableTime',
                  type: "POST",
                  data: {
                    sdate: '<?= $adate; ?>',
                    avltimeslct: avltimeslct,
                    ntactionnew_val: ntactionnew_val
                  },
                  cache: false,
                  success: function a(result){
                    
                    $('#freeaslotDisplay').html(result).slideDown("fast");
                    $('#freeaslotDisplay span').css({
                        'background-color': 'rgb(10, 162, 18)',
                        'border-radius': '10px'
                    });
                    }
                  });


                  $.ajax({
                  url:'<?=base_url();?>Menu/getTaskAvailableTimeByTaskID',
                  type: "POST",
                  data: {
                    sdate: '<?= $adate; ?>',
                    avltimeslct: avltimeslct,
                    ntactionnew_val: ntactionnew_val
                  },
                  cache: false,
                  success: function a(result){
                    $('#meeting-time').html("");
                    $('#meeting-time').html(result);
                    $('#meeting-time option.text-success').css({ 'color': 'green' });
                    $('#meeting-time option.text-danger').css({ 'color': 'red' });
                  }
                  });
            }else{
                $.ajax({
                  url:'<?=base_url();?>Menu/getTaskAvailableTimeByTaskID',
                  type: "POST",
                  data: {
                    sdate: '<?= $adate; ?>',
                    avltimeslct: avltimeslct,
                    ntactionnew_val: ntactionnew_val
                  },
                  cache: false,
                  success: function a(result){
                    $('#meeting-time').html("");
                    $('#meeting-time').html(result);
                    $('#meeting-time option.text-success').css({ 'color': 'green' });
                    $('#meeting-time option.text-danger').css({ 'color': 'red' });
                  }
                  });
            }



                $('#selectReseachCompanyType').show();
                $('#bcytpe').hide();
                $('#researchType').on('change', function() {
                  $("#taskplanningimg").hide();
                  var researchType = $(this).val();
                if(researchType == 'From Funnel'){
                  $('#maintaskcard').show();
                  $("#selectcompany").show();
                  $.ajax({
                    url:'<?=base_url();?>Menu/getstatuscmp',
                    type: "POST",
                    data: {
                    sid:1,
                    uid: uid
                    },
                    cache: false,
                    success: function a(result){
                
                  $("#maintaskcard").show();
                  $("#selectcompanybyuser").html(result);
                  $("#selectcompanybyuser").show();
                  $("#select_cluster").hide();
                  var optionCount = $('#selectcompanybyuser').find('option').length;
                  optionCount = optionCount-1;
                  $("#totalcompany").text('Total Company :'+ optionCount);
                  $("#tasktaction").show();
                  $("#tptime").val('');
                  $("#tptime").show();
                  $('#ntactionnew').show();
                  $('#ntppose').show();
                  $('#meeting-time').show();
                  $('#planbtn1').show();
                  $('#ntactionnew option').prop('disabled', true);
                  $('#ntactionnew option[value="10"]').prop('disabled', false).prop('selected', true);
                  $("#ntppose").html("<option value='94'>Research & Data Collection</option>");


                    }
                    });
                  }
                  
                  else if(researchType == 'Other'){
                    $("#maintaskcard").show();
                    $("#selectcompany").hide();
                    $("#select_cluster").hide();
                    $("#tasktaction").show();
                    $("#tptime").val('');
                    $("#tptime").show();
                    $('#ntactionnew').show();
                    $('#ntppose').show();
                    $('#meeting-time').show();
                    $('#planbtn1').show();
                    $('#selectcompanybyuser').removeAttr('required');
                    $('#ntactionnew option').prop('disabled', true);
                    $('#ntactionnew option[value="10"]').prop('disabled', false).prop('selected', true);
                    $("#ntppose").html("<option value='94'>Research & Data Collection</option>");
                  }
              });
              }else{
                $('#ntactionnew option[value="3"]').remove();
                $('#ntactionnew option[value="4"]').remove();
                $('#ntactionnew option[value="17"]').remove();
                $('#selectbarginCompanyType').hide();
                $("#select_cluster").hide();
                $('#selectReseachCompanyType').hide();
                $("#bargmeetingcluster").hide();
                $('#ntactionnew option').prop('disabled', false);
                $("#ntppose").html("<option value=''>Select Purpose</option>");
                  $.ajax({
                  url:'<?=base_url();?>Menu/taskactionnotplan_filter',
                  type: "POST",
                  data: {
                  tasktaction: tasktaction,
                  uid: uid
                  },
                  cache: false,
                  success: function a(result){
                  $("#maintaskcard").show();
                  $("#selectcompanybyuser").html(result);
                  $("#selectcompanybyuser").show();
                  var optionCount = $('#selectcompanybyuser').find('option').length;
                  optionCount = optionCount-1;
                  $("#totalcompany").text('Total Company :'+ optionCount);
                  $("#tptime").show();
                  $('#ntactionnew').show();
                  $('#ntppose').show();
                  $('#meeting-time').show();
                  $('#planbtn1').show();
                  // $('#daysfiltercard_anp').show();
                  $('#status_taskaction_card').show();
                  $('#status_taskaction').show();
                      }
                  });
              }
              });
          $('#status_taskaction').on('change', function() {
          var selectstatusbyusernotplaned = $(this).val();
          $("#selectcompanybyuser").html('');
          $("#totalcompany").text('');
          var uid = $("#curuserid").val();
          var tasktaction =  $('#task_action_filter').val();
              $.ajax({
              url:'<?=base_url();?>Menu/getstatuscmpnotplaned',
              type: "POST",
              data: {
              sid: selectstatusbyusernotplaned,
              tasktaction: tasktaction,
              uid: uid
              },
              cache: false,
              success: function a(result){
                  $("#selectcompanybyuser").html(result);
                    $("#selectcompanybyuser").show();
                    var optionCount = $('#selectcompanybyuser').find('option').length;
                optionCount = optionCount-1;
                $("#totalcompany").text('Total Company :'+ optionCount);
                $('#taskActionbyuserCard').show();
              }
              });
          });
          $('#taskActionbyuserCardData').on('change', function() {
              var taskActionbyuserCardData = $(this).val();
              var selectedValue1 = $('#status_taskaction').val();
              var tasktaction =  $('#task_action_filter').val();
              $.ajax({
              url:'<?=base_url();?>Menu/taskactionnotplan_filter_action',
              type: "POST",
              data: {
              sid: selectedValue1,
              tasktaction: tasktaction,
              tasktactionData: taskActionbyuserCardData,
              uid: uid
              },
              cache: false,
              success: function a(result){
                  $("#selectcompanybyuser").html(result);
                  $("#selectcompanybyuser").show();
                  var optionCount = $('#selectcompanybyuser').find('option').length;
              optionCount = optionCount-1;
              $("#totalcompany").text('Total Company :'+ optionCount);
              $('#taskPurposebyuserCard').show();
                  }
              });
              });
      
          $('#taskPurposebyuserCardData').on('change', function() {
              var taskPurposebyuserCardData = $(this).val();
              var taskActionbyuserCardData =  $('#taskActionbyuserCardData').val();
              var selectedValue1 = $('#status_taskaction').val();
              var tasktaction =  $('#task_action_filter').val();
              $.ajax({
              url:'<?=base_url();?>Menu/taskactionnotplan_filter_purpose',
              type: "POST",
              data: {
              sid: selectedValue1,
              tasktaction: tasktaction,
              tasktactionData: taskActionbyuserCardData,
              taskPurposeData: taskPurposebyuserCardData,
              uid: uid
              },
              cache: false,
              success: function a(result){
                  $("#selectcompanybyuser").html(result);
                  $("#selectcompanybyuser").show();
                  var optionCount = $('#selectcompanybyuser').find('option').length;
              optionCount = optionCount-1;
              $("#totalcompany").text('Total Company :'+ optionCount);
              $('#taskPurposebyuserCard').show();
              $('#taskPurposebyuserCard_days').show();
                  }
              });
      
              });
      
          $('#taskPurposebyuserCardData_days').on('change', function() {
              var slct_days_status = $(this).val();
              var taskPurposebyuserCardData = $("#taskPurposebyuserCardData").val();
              var taskActionbyuserCardData =  $('#taskActionbyuserCardData').val();
              var selectedValue1 = $('#status_taskaction').val();
              var tasktaction =  $('#task_action_filter').val();
              $.ajax({
              url:'<?=base_url();?>Menu/getTaskPurposeYesorNobyuser_status',
              type: "POST",
              data: {
              sid: selectedValue1,
              tasktaction: tasktaction,
              taskActionbyuser: taskActionbyuserCardData,
              taskPurposebyuser: taskPurposebyuserCardData,
              slct_days_status: slct_days_status,
              uid: uid
              },
              cache: false,
              success: function a(result){
                  $("#selectcompanybyuser").html(result);
                  $("#selectcompanybyuser").show();
                  var optionCount = $('#selectcompanybyuser').find('option').length;
                  if(optionCount !== 0){
                    optionCount = optionCount-1;
                  }else{
                    optionCount = 0;
                  }
              
              $("#totalcompany").text('Total Company :'+ optionCount);
              $('#taskPurposebyuserCard').show();
                  }
              });
      
              });
      }else{
          $('#taskActionCard').hide();
          $('#taskPurposebyuserCard_days').hide();
          $("#bargmeetingcluster").hide();
      }
      
      if(val =='Partner Type'){
      
          var uid = $("#curuserid").val();
          $(".taskselectionarea").hide();
          $('#taskActionCard').show();
          $('#partnertype').show();
          $('#maintaskcard').hide();
          $('#status_taskaction_card').hide();
          $('#taskaction_card_area').hide();
          $('#taskActionbyuserCard').hide();
          $('#taskPurposebyuserCard').hide();
          $('#partnertype_select').on('change', function() {
          var partnertype = $(this).val();
          $("#selectcompanybyuser").html('');
          $("#totalcompany").text('');
          var uid = $("#curuserid").val();
              $.ajax({
              url:'<?=base_url();?>Menu/getcmp_partnertype',
              type: "POST",
              data: {
              partnertype: partnertype,
              uid: uid
              },
              cache: false,
              success: function a(result){
              $("#maintaskcard").show();
              $("#selectcompanybyuser").html(result);
              $("#selectcompanybyuser").show();
              var optionCount = $('#selectcompanybyuser').find('option').length;
              optionCount = optionCount-1;
              $("#totalcompany").text('Total Company :'+ optionCount);
              $("#tptime").show();
              $('#ntactionnew').show();
              $('#ntppose').show();
              $('#meeting-time').show();
              $('#planbtn1').show();
              $('#partnertype_cstatus').show();
              $("#taskplanningimg").hide();
              }
              });
          });
      
      
          $('#partnertype_cstatusData').on('change', function() {
          var partnertype_cstatus = $(this).val();
          $("#selectcompanybyuser").html('');
          $("#totalcompany").text('');
          var uid = $("#curuserid").val();
          var partnertype = $('#partnertype_select').val();
              $.ajax({
              url:'<?=base_url();?>Menu/getcmp_partnertypeCstatus',
              type: "POST",
              data: {
              partnertype: partnertype,
              partnertype_cstatus: partnertype_cstatus,
              uid: uid
              },
              cache: false,
              success: function a(result){
              $("#maintaskcard").show();
              $("#selectcompanybyuser").html(result);
              $("#selectcompanybyuser").show();
              var optionCount = $('#selectcompanybyuser').find('option').length;
              optionCount = optionCount-1;
              $("#totalcompany").text('Total Company :'+ optionCount);
              $("#tptime").show();
              $('#ntactionnew').show();
              $('#ntppose').show();
              $('#meeting-time').show();
              $('#planbtn1').show();
              $('#partnertype_task').show();
              }
              });
          });
      
      
          $('#partnertype_taskData').on('change', function() {
          var partnertype_task = $(this).val();
          $("#selectcompanybyuser").html('');
          $("#totalcompany").text('');
          var uid = $("#curuserid").val();
          var partnertype = $('#partnertype_select').val();
          var partnertype_cstatus = $('#partnertype_cstatusData').val();
              $.ajax({
              url:'<?=base_url();?>Menu/getcmp_partnertypeCstatusTask',
              type: "POST",
              data: {
              partnertype: partnertype,
              partnertype_cstatus: partnertype_cstatus,
              partnertype_task: partnertype_task,
              uid: uid
              },
              cache: false,
              success: function a(result){
              $("#maintaskcard").show();
              $("#selectcompanybyuser").html(result);
              $("#selectcompanybyuser").show();
              var optionCount = $('#selectcompanybyuser').find('option').length;
              optionCount = optionCount-1;
              $("#totalcompany").text('Total Company :'+ optionCount);
              $("#tptime").show();
              $('#ntactionnew').show();
              $('#ntppose').show();
              $('#meeting-time').show();
              $('#planbtn1').show();
              $('#partnertype_taskAction').show();
              }
              });
          });
      
      
          $('#partnertype_taskActionData').on('change', function() {
          var partnertype_taskAction = $(this).val();
          $("#selectcompanybyuser").html('');
          $("#totalcompany").text('');
          var uid = $("#curuserid").val();
          var partnertype = $('#partnertype_select').val();
          var partnertype_cstatus = $('#partnertype_cstatusData').val();
          var partnertype_task = $('#partnertype_taskData').val();
              $.ajax({
              url:'<?=base_url();?>Menu/getcmp_partnertypeCstatusTaskAction',
              type: "POST",
              data: {
              partnertype: partnertype,
              partnertype_cstatus: partnertype_cstatus,
              partnertype_task: partnertype_task,
              partnertype_taskAction: partnertype_taskAction,
              uid: uid
              },
              cache: false,
              success: function a(result){
              $("#maintaskcard").show();
              $("#selectcompanybyuser").html(result);
              $("#selectcompanybyuser").show();
              var optionCount = $('#selectcompanybyuser').find('option').length;
              optionCount = optionCount-1;
              $("#totalcompany").text('Total Company :'+ optionCount);
              $("#tptime").show();
              $('#ntactionnew').show();
              $('#ntppose').show();
              $('#meeting-time').show();
              $('#planbtn1').show();
              $('#partnertype_taskPurpose').show();
      
              }
              });
          });
      
          $('#partnertype_taskPurposeData').on('change', function() {
          var partnertype_taskPurpose = $(this).val();
          $("#selectcompanybyuser").html('');
          $("#totalcompany").text('');
          var uid = $("#curuserid").val();
          var partnertype = $('#partnertype_select').val();
          var partnertype_cstatus = $('#partnertype_cstatusData').val();
          var partnertype_task = $('#partnertype_taskData').val();
          var partnertype_taskAction = $('#partnertype_taskActionData').val();
              $.ajax({
              url:'<?=base_url();?>Menu/getcmp_partnertypeCstatusTaskPurpose',
              type: "POST",
              data: {
              partnertype: partnertype,
              partnertype_cstatus: partnertype_cstatus,
              partnertype_task: partnertype_task,
              partnertype_taskAction: partnertype_taskAction,
              partnertype_taskPurpose: partnertype_taskPurpose,
              uid: uid
              },
              cache: false,
              success: function a(result){
              $("#maintaskcard").show();
              $("#selectcompanybyuser").html(result);
              $("#selectcompanybyuser").show();
              $("#partnertype_taskPurpose_days").show();
              var optionCount = $('#selectcompanybyuser').find('option').length;
              optionCount = optionCount-1;
              $("#totalcompany").text('Total Company :'+ optionCount);
              $("#tptime").show();
              $('#ntactionnew').show();
              $('#ntppose').show();
              $('#meeting-time').show();
              $('#planbtn1').show();
              $('#partnertype_taskPurpose').show();
      
              }
              });
          });
      
          $('#partnertype_taskPurposeData_days').on('change', function() {
              var slct_days_status = $(this).val();
              var uid = $("#curuserid").val();
      
              var slctprtnrtype = $('#partnertype_select').val();
              var selectedValue = $('#partnertype_cstatusData').val();
              var taskdata = $('#partnertype_taskData').val();
              var taskActionbyuser = $('#partnertype_taskActionData').val();
              var taskPurposebyuser = $('#partnertype_taskPurposeData').val();
              $.ajax({
              url:'<?=base_url();?>Menu/getTaskPurposeYesorNobyuser_status',
              type: "POST",
              data: {
              sid: selectedValue,
              slctprtnrtype: slctprtnrtype,
              taskActionbyuser: taskActionbyuser,
              taskPurposebyuser: taskPurposebyuser,
              slct_days_status: slct_days_status,
              slctprtnrtype_taskdata: taskdata,
              uid: uid
              },
              cache: false,
              success: function a(result){
                  $("#selectcompanybyuser").html(result);
                  $("#selectcompanybyuser").show();
                  var optionCount = $('#selectcompanybyuser').find('option').length;
                  optionCount = optionCount-1;
                  $("#totalcompany").text('Total Company :'+ optionCount);
                  }
              });
              });
      
      
      
      
          }else{
              $('#partnertype').hide();
              $("#partnertype_taskPurpose_days").hide();
          }
      
          if(val == 'Plan But Not Initiated'){
          $('#taskActionCard').show();
          $('#planbutnotinitiatedcard').show();
          var uid = $("#curuserid").val();
          $(".taskselectionarea").hide();
          $('#partnertype').hide();
          $('#maintaskcard').hide();
          $('#status_taskaction_card').hide();
          $('#taskaction_card_area').hide();
          $('#taskActionbyuserCard').hide();
          $('#taskPurposebyuserCard').hide();
          
          $.ajax({
              url:'<?=base_url();?>Menu/getallcmp_planbutnotinited',
              type: "POST",
              data: {
              taskaction:'all',
              uid: uid
              },
              cache: false,
              success: function a(result){
                  $("#plancomp").text('Plan But Not Initiated = '+result);
              }
              });
      
          $('#planbutnoinit_TaskData').on('change', function() {
          var planbutnotinitiated_taskaction = $(this).val();

          var avltimeslct     =   $('#getAvailableTime').val();
          var ntactionnew_val = planbutnotinitiated_taskaction;

          if (avltimeslct !== null) {

                $.ajax({
                  url:'<?=base_url();?>Menu/getTaskAvailableTime',
                  type: "POST",
                  data: {
                    sdate: '<?= $adate; ?>',
                    avltimeslct: avltimeslct,
                    ntactionnew_val: ntactionnew_val
                  },
                  cache: false,
                  success: function a(result){
                    
                    $('#freeaslotDisplay').html(result).slideDown("fast");
                    $('#freeaslotDisplay span').css({
                        'background-color': 'rgb(10, 162, 18)',
                        'border-radius': '10px'
                    });
                    }
                  });


                  $.ajax({
                  url:'<?=base_url();?>Menu/getTaskAvailableTimeByTaskID',
                  type: "POST",
                  data: {
                    sdate: '<?= $adate; ?>',
                    avltimeslct: avltimeslct,
                    ntactionnew_val: ntactionnew_val
                  },
                  cache: false,
                  success: function a(result){
                    $('#meeting-time').html("");
                    $('#meeting-time').html(result);
                    $('#meeting-time option.text-success').css({ 'color': 'green' });
                    $('#meeting-time option.text-danger').css({ 'color': 'red' });
                  }
                  });
            }else{
                $.ajax({
                  url:'<?=base_url();?>Menu/getTaskAvailableTimeByTaskID',
                  type: "POST",
                  data: {
                    sdate: '<?= $adate; ?>',
                    avltimeslct: avltimeslct,
                    ntactionnew_val: ntactionnew_val
                  },
                  cache: false,
                  success: function a(result){
                    $('#meeting-time').html("");
                    $('#meeting-time').html(result);
                    $('#meeting-time option.text-success').css({ 'color': 'green' });
                    $('#meeting-time option.text-danger').css({ 'color': 'red' });
                  }
                  });
            }

          
          $("#selectcompanybyuser").html('');
          $("#totalcompany").text('');
          var uid = $("#curuserid").val();
              $.ajax({
              url:'<?=base_url();?>Menu/getcmp_planbutnotinited',
              type: "POST",
              data: {
              taskaction: planbutnotinitiated_taskaction,
              uid: uid
              },
              cache: false,
              success: function a(result){
              $("#maintaskcard").show();
              $("#selectcompanybyuser").html(result);
              $("#selectcompanybyuser").show();
              var optionCount = $('#selectcompanybyuser').find('option').length;
              if(optionCount == 0){
                  optionCount = 0;
              }
              $("#totalcompany").text('Total Company :'+ optionCount);
              $("#tptime").show();
              $('#ntactionnew').show();
              $('#ntppose').show();
              $('#meeting-time').show();
              $('#planbtn1').show();
              $('#partnertype_cstatus').show();
              $('#ntactionnew').hide();
              $('#ntppose').hide();
              $("#taskplanningimg").hide();
              $('#planbtn1').click(function() {
                      var newValue = '0';
                      var newText = 'Pending Task Action';
                      $('#ntactionnew').append(new Option(newText, newValue));
                      $('#ntppose').append(new Option(newText, newValue));
                      $('#ntactionnew').val(newValue);
                      $('#ntppose').val(newValue);   
                  });
              }
              });
          });
          }else{
              $('#planbutnotinitiatedcard').hide();
          }
      
          if(val == 'Plan But Not Initiated Old'){

          $('#taskActionCard').show();
          $('#planbutnotinitiatedcardold').show();
          var uid = $("#curuserid").val();
          $(".taskselectionarea").hide();
          $('#partnertype').hide();
          $('#maintaskcard').hide();
          $('#status_taskaction_card').hide();
          $('#taskaction_card_area').hide();
          $('#taskActionbyuserCard').hide();
          $('#taskPurposebyuserCard').hide();
          
          $.ajax({
              url:'<?=base_url();?>Menu/getallcmp_planbutnotinitedold',
              type: "POST",
              data: {
              taskaction:'all',
              uid: uid
              },
              cache: false,
              success: function a(result){
                  $("#plancompold").text('Plan But Not Initiated = '+result);
              }
              });
      
          $('#planbutnoinit_TaskDataold').on('change', function() {
          var planbutnotinitiated_taskaction = $(this).val();

          var avltimeslct     =   $('#getAvailableTime').val();
          var ntactionnew_val = planbutnotinitiated_taskaction;

              if (avltimeslct !== null) {

                    $.ajax({
                      url:'<?=base_url();?>Menu/getTaskAvailableTime',
                      type: "POST",
                      data: {
                        sdate: '<?= $adate; ?>',
                        avltimeslct: avltimeslct,
                        ntactionnew_val: ntactionnew_val
                      },
                      cache: false,
                      success: function a(result){
                        
                        $('#freeaslotDisplay').html(result).slideDown("fast");
                        $('#freeaslotDisplay span').css({
                            'background-color': 'rgb(10, 162, 18)',
                            'border-radius': '10px'
                        });
                        }
                      });


                      $.ajax({
                      url:'<?=base_url();?>Menu/getTaskAvailableTimeByTaskID',
                      type: "POST",
                      data: {
                        sdate: '<?= $adate; ?>',
                        avltimeslct: avltimeslct,
                        ntactionnew_val: ntactionnew_val
                      },
                      cache: false,
                      success: function a(result){
                        $('#meeting-time').html("");
                        $('#meeting-time').html(result);
                        $('#meeting-time option.text-success').css({ 'color': 'green' });
                        $('#meeting-time option.text-danger').css({ 'color': 'red' });
                      }
                      });
                }else{
                    $.ajax({
                      url:'<?=base_url();?>Menu/getTaskAvailableTimeByTaskID',
                      type: "POST",
                      data: {
                        sdate: '<?= $adate; ?>',
                        avltimeslct: avltimeslct,
                        ntactionnew_val: ntactionnew_val
                      },
                      cache: false,
                      success: function a(result){
                        $('#meeting-time').html("");
                        $('#meeting-time').html(result);
                        $('#meeting-time option.text-success').css({ 'color': 'green' });
                        $('#meeting-time option.text-danger').css({ 'color': 'red' });
                      }
                      });
                }


          $("#selectcompanybyuser").html('');
          $("#totalcompany").text('');
          var uid = $("#curuserid").val();
              $.ajax({
              url:'<?=base_url();?>Menu/getcmp_planbutnotinitedOld',
              type: "POST",
              data: {
              taskaction: planbutnotinitiated_taskaction,
              uid: uid
              },
              cache: false,
              success: function a(result){
              $("#maintaskcard").show();
              $("#selectcompanybyuser").html(result);
              $("#selectcompanybyuser").show();
              var optionCount = $('#selectcompanybyuser').find('option').length;
              if(optionCount == 0){
                  optionCount = 0;
              }
              $("#totalcompany").text('Total Company :'+ optionCount);
              $("#tptime").show();
              $('#ntactionnew').show();
              $('#ntppose').show();
              $('#meeting-time').show();
              $('#planbtn1').show();
              $('#partnertype_cstatus').show();
              $("#taskplanningimg").hide();
              $('#ntactionnew').hide();
              $('#ntppose').hide();
              $('#planbtn1').click(function() {
                      var newValue = '0';
                      var newText = 'Pending Task Action';
                      $('#ntactionnew').append(new Option(newText, newValue));
                      $('#ntppose').append(new Option(newText, newValue));
                      $('#ntactionnew').val(newValue);
                      $('#ntppose').val(newValue);
                      val = 'Plan But Not Initiated';
                      $("#selectby").val(val);
                     
                  });
              }
              });
          });
          }else{
              $('#planbutnotinitiatedcardold').hide();
          }
          if(val == 'FirstQuarter1'){
          $('#firstQuarter1cstatys').show();
          $('#taskActionCard').hide();
          $('#firstQuarter1').show();  
          var uid = $("#curuserid").val();
          $(".taskselectionarea").hide();
          $('#partnertype').hide();
          $('#maintaskcard').hide();
          $('#status_taskaction_card').hide();
          $('#taskaction_card_area').hide();
          $('#taskActionbyuserCard').hide();
          $('#taskPurposebyuserCard').hide();
      
          $.ajax({
              url:'<?=base_url();?>Menu/getquarter1company',
              type: "POST",
              data: {
              quarter:'quarter',
              uid: uid
              },
              cache: false,
              success: function a(result){
              $("#maintaskcard").show();
              $("#selectcompanybyuser").html(result);
              $("#selectcompanybyuser").show();
              var optionCount = $('#selectcompanybyuser').find('option').length;
              optionCount = optionCount -1;
              $("#totalcompany").text('Total Company :'+ optionCount);
              $("#tptime").show();
              $('#ntactionnew').show();
              $('#ntppose').show();
              $('#meeting-time').show();
              $('#planbtn1').show();
              $('#partnertype_cstatus').show();
              $('#ntactionnew').hide();
              $('#ntppose').hide();
              $("#taskplanningimg").hide();
              }
              });
              $('#firstQuarter1cstatysData').on('change', function() {
          var selectedValue = $(this).val();
              $("#daysByTask").show();
              $("#selectcompanybyuser").html('');
              $.ajax({
              url:'<?=base_url();?>Menu/getstatuscmp',
              type: "POST",
              data: {
              sid: selectedValue,
              uid: uid
              },
              cache: false,
              success: function a(result){
              $("#maintaskcard").show();    
              $("#selectcompanybyuser").html(result);
              $("#selectcompanybyuser").show();
              var optionCount = $('#selectcompanybyuser').find('option').length;
              optionCount = optionCount-1;
              $("#totalcompany").text('Total Company :'+ optionCount);
              $("#tasktaction").show();
              $("#tptime").val('');
              $("#tptime").show();
              $('#ntactionnew').show();
              $('#ntppose').show();
              $('#meeting-time').show();
              $('#planbtn1').show();
              $('#firstQuarter1cstatysDataTask').show();
              }
              });
          });
          $('#firstQuarter1cstatysDataTask').on('change', function() {
              var uid = $("#curuserid").val();
              var tasktaction = $(this).val();
              var selectedValue = $('#firstQuarter1cstatysData').val();
              $.ajax({
              url:'<?=base_url();?>Menu/getstatuscmpwithtasktaction',
              type: "POST",
              data: {
              sid: selectedValue,
              tasktaction: tasktaction,
              uid: uid
              },
              cache: false,
              success: function a(result){
                  $("#selectcompanybyuser").html(result);
                  $("#selectcompanybyuser").show();
                  var optionCount = $('#selectcompanybyuser').find('option').length;
              optionCount = optionCount-1;
              $("#totalcompany").text('Total Company :'+ optionCount);
              $('#firstQuarter1taskActionbyuser').show();
                  }
              });
              });
              $('#firstQuarter1taskActionbyuser').on('change', function() {
              var uid = $("#curuserid").val();
              var taskActionbyuser = $(this).val();
              var tasktaction = $('#firstQuarter1cstatysDataTask').val();
              var selectedValue = $('#selectstatusbyuser').val();
              $.ajax({
              url:'<?=base_url();?>Menu/getTaskActionYesorNobyuser',
              type: "POST",
              data: {
              sid: selectedValue,
              tasktaction: tasktaction,
              taskActionbyuser: taskActionbyuser,
              uid: uid
              },
              cache: false,
              success: function a(result){
                  $("#selectcompanybyuser").html(result);
                  $("#selectcompanybyuser").show();
                  var optionCount = $('#selectcompanybyuser').find('option').length;
              optionCount = optionCount-1;
              $("#totalcompany").text('Total Company :'+ optionCount);
              // $('#taskPurposebyuser').show();
              $('#firstQuarter1taskPurposebyuser').show();
                  }
              });
      
              });
      
              $('#firstQuarter1taskPurposebyuser').on('change', function() {
                  var uid = $("#curuserid").val();
              var taskPurposebyuser = $(this).val();
              var tasktaction = $('#tasktaction').val();
              var selectedValue = $('#selectstatusbyuser').val();
              var taskActionbyuser = $('#firstQuarter1taskActionbyuser').val();
      
              $.ajax({
              url:'<?=base_url();?>Menu/getTaskPurposeYesorNobyuser',
              type: "POST",
              data: {
              sid: selectedValue,
              tasktaction: tasktaction,
              taskActionbyuser: taskActionbyuser,
              taskPurposebyuser: taskPurposebyuser,
              uid: uid
              },
              cache: false,
              success: function a(result){
                  $("#selectcompanybyuser").html(result);
                  $("#selectcompanybyuser").show();
                  var optionCount = $('#selectcompanybyuser').find('option').length;
              optionCount = optionCount-1;
              $("#totalcompany").text('Total Company :'+ optionCount);
                  }
              });
      
              });
          }else{
              $('#firstQuarter1').hide();  
          }
          if(val == 'Same Status Last Limit Days'){
          $('#taskActionCard').show();
          $('#sameStatusLastLimitDays').show();
      
          var uid = $("#curuserid").val();
          $(".taskselectionarea").hide();
          $('#partnertype').hide();
          $('#maintaskcard').hide();
          $('#status_taskaction_card').hide();
          $('#taskaction_card_area').hide();
          $('#taskActionbyuserCard').hide();
          $('#taskPurposebyuserCard').hide();
          
          $('#samestatuslast15daysData').on('change', function() {
          var samestatuslast15daysData = $(this).val();
          $("#selectcompanybyuser").html('');
          $("#totalcompany").text('');
          var uid = $("#curuserid").val();
              $.ajax({
              url:'<?=base_url();?>Menu/getcmp_samestatuslastdays',
              type: "POST",
              data: {
              samestatus: samestatuslast15daysData,
              uid: uid
              },
              cache: false,
              success: function a(result){
              $("#maintaskcard").show();
              $("#selectcompanybyuser").html(result);
              $("#selectcompanybyuser").show();
              var optionCount = $('#selectcompanybyuser').find('option').length;
              optionCount = optionCount-1;
              $("#totalcompany").text('Total Company :'+ optionCount);
              $("#tptime").show();
              $('#ntactionnew').show();
              $('#ntppose').show();
              $('#meeting-time').show();
              $('#planbtn1').show();
              $('#partnertype_planbut').show();
              $("#taskplanningimg").hide();
              }
              });
          });
      
      
          $('#partnertype_planbutData').on('change', function() {
          var partnertype_planbutData = $(this).val();
          $("#selectcompanybyuser").html('');
          $("#totalcompany").text('');
      
          var uid = $("#curuserid").val();
          var samestatuslast15daysData = $('#samestatuslast15daysData').val();
              $.ajax({
              url:'<?=base_url();?>Menu/getcmp_samestatuslastdaysPartner',
              type: "POST",
              data: {
              samestatus: samestatuslast15daysData,
              partnertype: partnertype_planbutData,
              uid: uid
              },
              cache: false,
              success: function a(result){
              $("#maintaskcard").show();
              $("#selectcompanybyuser").html(result);
              $("#selectcompanybyuser").show();
              var optionCount = $('#selectcompanybyuser').find('option').length;
              optionCount = optionCount-1;
              $("#totalcompany").text('Total Company :'+ optionCount);
              $("#tptime").show();
              $('#ntactionnew').show();
              $('#ntppose').show();
              $('#meeting-time').show();
              $('#planbtn1').show();
              $('#daysfiltercard_planbut').show();
      
              }
              });
          });
      
          $('#daysfilter2_samedays').on('change', function() {
          var daysfilter2_samedays = $(this).val();
          $("#selectcompanybyuser").html('');
          $("#totalcompany").text('');
      
          var uid = $("#curuserid").val();
          var samestatuslast15daysData = $('#samestatuslast15daysData').val();
          var partnertype_planbutData = $('#partnertype_planbutData').val();
      
              $.ajax({
              url:'<?=base_url();?>Menu/getcmp_samestatuslastdaysPartnerDays',
              type: "POST",
              data: {
              samestatus: samestatuslast15daysData,
              partnertype: partnertype_planbutData,
              daysfilter: daysfilter2_samedays,
              uid: uid
              },
              cache: false,
              success: function a(result){
              $("#maintaskcard").show();
              $("#selectcompanybyuser").html(result);
              $("#selectcompanybyuser").show();
              var optionCount = $('#selectcompanybyuser').find('option').length;
              optionCount = optionCount-1;
              $("#totalcompany").text('Total Company :'+ optionCount);
              $("#tptime").show();
              $('#ntactionnew').show();
              $('#ntppose').show();
              $('#meeting-time').show();
              $('#planbtn1').show();
              $('#planbut_taskActioncard').show();
              }
              });
          });
      
      
          $('#planbut_taskActionData').on('change', function() {
          var planbut_taskActioncardData = $(this).val();
          $("#selectcompanybyuser").html('');
          $("#totalcompany").text('');
      
          var uid = $("#curuserid").val();
          var samestatuslast15daysData = $('#samestatuslast15daysData').val();
          var partnertype_planbutData = $('#partnertype_planbutData').val();
          var daysfilter2_samedays = $('#daysfilter2_samedays').val();
      
              $.ajax({
              url:'<?=base_url();?>Menu/getcmp_samestatuslastdaysPartnerDaysAction',
              type: "POST",
              data: {
              samestatus: samestatuslast15daysData,
              partnertype: partnertype_planbutData,
              daysfilter: daysfilter2_samedays,
              taskActioncard: planbut_taskActioncardData,
              uid: uid
              },
              cache: false,
              success: function a(result){
              $("#maintaskcard").show();
              $("#selectcompanybyuser").html(result);
              $("#selectcompanybyuser").show();
              var optionCount = $('#selectcompanybyuser').find('option').length;
              optionCount = optionCount-1;
              $("#totalcompany").text('Total Company :'+ optionCount);
              $("#tptime").show();
              $('#ntactionnew').show();
              $('#ntppose').show();
              $('#meeting-time').show();
              $('#planbtn1').show();
              $('#planbut_taskPurposecard').show();
      
              }
              });
          });
      
          $('#planbut_taskPurposeData').on('change', function() {
          var planbut_taskPurposeData = $(this).val();
          $("#selectcompanybyuser").html('');
          $("#totalcompany").text('');
      
          var uid = $("#curuserid").val();
          var samestatuslast15daysData = $('#samestatuslast15daysData').val();
          var partnertype_planbutData = $('#partnertype_planbutData').val();
          var daysfilter2_samedays = $('#daysfilter2_samedays').val();
          var planbut_taskActioncardData = $('#planbut_taskActionData').val();
      
              $.ajax({
              url:'<?=base_url();?>Menu/getcmp_samestatuslastdaysPartnerDaysPurpose',
              type: "POST",
              data: {
              samestatus: samestatuslast15daysData,
              partnertype: partnertype_planbutData,
              daysfilter: daysfilter2_samedays,
              taskActioncard: planbut_taskActioncardData,
              taskPurposecard: planbut_taskPurposeData,
              uid: uid
              },
              cache: false,
              success: function a(result){
              $("#maintaskcard").show();
              $("#selectcompanybyuser").html(result);
              $("#selectcompanybyuser").show();
              var optionCount = $('#selectcompanybyuser').find('option').length;
              optionCount = optionCount-1;
              $("#totalcompany").text('Total Company :'+ optionCount);
              $("#tptime").show();
              $('#ntactionnew').show();
              $('#ntppose').show();
              $('#meeting-time').show();
              $('#planbtn1').show();
              }
              });
          });
          }else{
              $('#sameStatusLastLimitDays').hide();
          }
          if(val == 'Review Target Date'){
              $('#taskActionCard').hide();
              $('#reviewTargetDate').show();
              var uid = $("#curuserid").val();
              $(".taskselectionarea").hide();
              $('#partnertype').hide();
              $('#maintaskcard').hide();
              $('#status_taskaction_card').hide();
              $('#taskaction_card_area').hide();
              $('#taskActionbyuserCard').hide();
              $('#taskPurposebyuserCard').hide();
              $('#reviewTargetreviewtypeData').on('change', function() {
              var getreviewtype = $(this).val();

               var avltimeslct     =   $('#getAvailableTime').val();
              var ntactionnew_val = getreviewtype;

              if (avltimeslct !== null) {

                    $.ajax({
                      url:'<?=base_url();?>Menu/getTaskAvailableTime',
                      type: "POST",
                      data: {
                        sdate: '<?= $adate; ?>',
                        avltimeslct: avltimeslct,
                        ntactionnew_val: ntactionnew_val
                      },
                      cache: false,
                      success: function a(result){
                        
                        $('#freeaslotDisplay').html(result).slideDown("fast");
                        $('#freeaslotDisplay span').css({
                            'background-color': 'rgb(10, 162, 18)',
                            'border-radius': '10px'
                        });
                        }
                      });


                      $.ajax({
                      url:'<?=base_url();?>Menu/getTaskAvailableTimeByTaskID',
                      type: "POST",
                      data: {
                        sdate: '<?= $adate; ?>',
                        avltimeslct: avltimeslct,
                        ntactionnew_val: ntactionnew_val
                      },
                      cache: false,
                      success: function a(result){
                        $('#meeting-time').html("");
                        $('#meeting-time').html(result);
                        $('#meeting-time option.text-success').css({ 'color': 'green' });
                        $('#meeting-time option.text-danger').css({ 'color': 'red' });
                      }
                      });
                }else{
                    $.ajax({
                      url:'<?=base_url();?>Menu/getTaskAvailableTimeByTaskID',
                      type: "POST",
                      data: {
                        sdate: '<?= $adate; ?>',
                        avltimeslct: avltimeslct,
                        ntactionnew_val: ntactionnew_val
                      },
                      cache: false,
                      success: function a(result){
                        $('#meeting-time').html("");
                        $('#meeting-time').html(result);
                        $('#meeting-time option.text-success').css({ 'color': 'green' });
                        $('#meeting-time option.text-danger').css({ 'color': 'red' });
                      }
                      });
                }


              $("#daysByTask").show();
              $("#selectcompanybyuser").html('');
              $.ajax({
              url:'<?=base_url();?>Menu/getcmp_getreviewtype_new',
              type: "POST",
              data: {
              getreviewtype: getreviewtype,
              uid: uid
              },
              cache: false,
              success: function a(result){
              $("#maintaskcard").show();    
              $("#selectcompanybyuser").html(result);
              $("#selectcompanybyuser").show();
              var optionCount = $('#selectcompanybyuser').find('option').length;
              optionCount = optionCount-1;
              $("#totalcompany").text('Total Company :'+ optionCount);
              $("#tptime").show();
              $('#ntactionnew').show();
              $('#ntppose').show();
              $('#meeting-time').show();
              $('#planbtn1').show();
              $('#ntactionnew').hide();
              $('#ntppose').hide();
              $("#taskplanningimg").hide();
              $('#planbtn1').click(function() {
                      var newValue = '0';
                      var newText = 'Pending Task Action';
                      $('#ntactionnew').append(new Option(newText, newValue));
                      $('#ntppose').append(new Option(newText, newValue));
                      $('#ntactionnew').val(newValue);
                      $('#ntppose').val(newValue);   
                  });
              // $('#reviewTargetReviewSelf').show();
              // $('#reviewTargetDate_typeoftaskCard').show();
              // $("#taskplanningimg").hide();
              }
              });
          });
              $('#reviewTargetReviewSelfData').on('change', function() {
              var getreviewtypeself = $(this).val();
              var getreviewtype = $('#reviewTargetreviewtypeData').val();
              $("#daysByTask").show();
              $("#selectcompanybyuser").html('');
              $.ajax({
              url:'<?=base_url();?>Menu/getcmp_getreviewtype_self',
              type: "POST",
              data: {
              getreviewtypeself: getreviewtypeself,
              getreviewtype: getreviewtype,
              uid: uid
              },
              cache: false,
              success: function a(result){
              $("#maintaskcard").show();    
              $("#selectcompanybyuser").html(result);
              $("#selectcompanybyuser").show();
              var optionCount = $('#selectcompanybyuser').find('option').length;
              $("#totalcompany").text('Total Company :'+ optionCount);
              $("#tasktaction").show();
              $("#tptime").val('');
              $("#tptime").show();
              $('#ntactionnew').show();
              $('#ntppose').show();
              $('#meeting-time').show();
              $('#planbtn1').show();
              $('#reviewTargetReviewSelf').show();
              // $('#reviewTargetDate_typeoftaskCard').show();
              }
              });
          });
              $('#reviewTargetDateData').on('change', function() {
              var selectedValue = $(this).val();
              $("#daysByTask").show();
              $("#selectcompanybyuser").html('');
              $.ajax({
              url:'<?=base_url();?>Menu/getstatuscmp_reviewTarget',
              type: "POST",
              data: {
              sid: selectedValue,
              uid: uid
              },
              cache: false,
              success: function a(result){
              $("#maintaskcard").show();    
              $("#selectcompanybyuser").html(result);
              $("#selectcompanybyuser").show();
              var optionCount = $('#selectcompanybyuser').find('option').length;
              optionCount = optionCount-1;
              $("#totalcompany").text('Total Company :'+ optionCount);
              $("#tasktaction").show();
              $("#tptime").val('');
              $("#tptime").show();
              $('#ntactionnew').show();
              $('#ntppose').show();
              $('#meeting-time').show();
              $('#planbtn1').show();
              $('#reviewTargetDate_typeoftaskCard').show();
              }
              });
          });
          
              $('#reviewTargetDate_typeoftaskData').on('change', function() {
              var reviewTargetDate_typeoftaskData = $(this).val();
              var selectedValue = $('#reviewTargetDateData').val();
              $("#daysByTask").show();
              $("#selectcompanybyuser").html('');
              $.ajax({
              url:'<?=base_url();?>Menu/getcmp_reviewTarget_typeoftaskData',
              type: "POST",
              data: {
              sid: selectedValue,
              typeoftask: reviewTargetDate_typeoftaskData,
              uid: uid
              },
              cache: false,
              success: function a(result){
              $("#maintaskcard").show();    
              $("#selectcompanybyuser").html(result);
              $("#selectcompanybyuser").show();
              var optionCount = $('#selectcompanybyuser').find('option').length;
              optionCount = optionCount-1;
              $("#totalcompany").text('Total Company :'+ optionCount);
              $("#tasktaction").show();
              $("#tptime").val('');
              $("#tptime").show();
              $('#ntactionnew').show();
              $('#ntppose').show();
              $('#meeting-time').show();
              $('#planbtn1').show();
              $('#reviewTargetDate_Action').show();
              }
              });
          });
          }else{
              $('#reviewTargetDate').hide();
          }
          if(val == 'Self Assign'){
          $('#auto_assign').show();
          $(".taskselectionarea").hide();
          $('#partnertype').hide();
          $('#maintaskcard').hide();
          $('#status_taskaction_card').hide();
          $('#taskaction_card_area').hide();
          $('#taskActionbyuserCard').hide();
          $('#taskPurposebyuserCard').hide();
          var uid = $("#curuserid").val();
          $('#slct_auto_assign_task').on('change', function() {
              var selectedValue = $(this).val();
              if(selectedValue == 'Call Assign on Tentive Status'){

              var avltimeslct     =   $('#getAvailableTime').val();
              var ntactionnew_val = 1;

              if (avltimeslct !== null) {

                    $.ajax({
                      url:'<?=base_url();?>Menu/getTaskAvailableTime',
                      type: "POST",
                      data: {
                        sdate: '<?= $adate; ?>',
                        avltimeslct: avltimeslct,
                        ntactionnew_val: ntactionnew_val
                      },
                      cache: false,
                      success: function a(result){
                        
                        $('#freeaslotDisplay').html(result).slideDown("fast");
                        $('#freeaslotDisplay span').css({
                            'background-color': 'rgb(10, 162, 18)',
                            'border-radius': '10px'
                        });
                        }
                      });


                      $.ajax({
                      url:'<?=base_url();?>Menu/getTaskAvailableTimeByTaskID',
                      type: "POST",
                      data: {
                        sdate: '<?= $adate; ?>',
                        avltimeslct: avltimeslct,
                        ntactionnew_val: ntactionnew_val
                      },
                      cache: false,
                      success: function a(result){
                        $('#meeting-time').html("");
                        $('#meeting-time').html(result);
                        $('#meeting-time option.text-success').css({ 'color': 'green' });
                        $('#meeting-time option.text-danger').css({ 'color': 'red' });
                      }
                      });
                }else{
                    $.ajax({
                      url:'<?=base_url();?>Menu/getTaskAvailableTimeByTaskID',
                      type: "POST",
                      data: {
                        sdate: '<?= $adate; ?>',
                        avltimeslct: avltimeslct,
                        ntactionnew_val: ntactionnew_val
                      },
                      cache: false,
                      success: function a(result){
                        $('#meeting-time').html("");
                        $('#meeting-time').html(result);
                        $('#meeting-time option.text-success').css({ 'color': 'green' });
                        $('#meeting-time option.text-danger').css({ 'color': 'red' });
                      }
                      });
                }


                $('#selectby').val('Plan When MOM Approved'); 
                $('#pendingdata_message').hide();
                $('#start_check,#end_check').removeAttr('required');
                $.ajax({
                  url:'<?=base_url();?>Menu/getAutoAssignTask',
                  type: "POST",
                  data: {
                  selectedValue: selectedValue,
                  uid: uid
                  },
                  cache: false,
                  success: function a(result){
                    $("#slct_auto_assign_task_type").show();
                    $("#slct_auto_assign_task_type").html(result);
                  }
                  });
                  $('#slct_auto_assign_task_type').on('change', function() {
                  var tasktype = $(this).val(); //when is mom task
                  var slcttasktype = $('#slct_auto_assign_task').val(); //when is mom task
                  $.ajax({
                  url:'<?=base_url();?>Menu/getAutoAssignTaskWithType',
                  type: "POST",
                  data: {
                  selectedValue: slcttasktype,
                  tasktype: tasktype,
                  uid: uid
                  },
                  cache: false,
                  success: function a(result){
                    // console.log(result);
                  $("#maintaskcard").show();
                  $("#selectcompany").show();
                  $("#selectcompanybyuser").html(result);
                  $("#selectcompanybyuser").show();
                  var optionCount = $('#selectcompanybyuser').find('option').length;
                  if(optionCount == 0){
                      optionCount = 0;
                  }
                  $("#totalcompany").text('Total Company :'+ optionCount);
                  $("#tptime").show();
                  $('#meeting-time').show();
                  $('#planbtn1').show();
                  $('#ntactionnew').show();
                  $('#ntactionnew').hide();
                  $('#ntppose').hide();
                  $("#taskplanningimg").hide();
                  $('#ntppose,#ntactionnew').removeAttr('required');
                    }
              });
            });   
              }else{
                $('#slct_auto_assign_task_type').hide();
                if(selectedValue =='Mom Check'){

                  var avltimeslct     =   $('#getAvailableTime').val();
                var ntactionnew_val = 18;

                if (avltimeslct !== null) {

                      $.ajax({
                        url:'<?=base_url();?>Menu/getTaskAvailableTime',
                        type: "POST",
                        data: {
                          sdate: '<?= $adate; ?>',
                          avltimeslct: avltimeslct,
                          ntactionnew_val: ntactionnew_val
                        },
                        cache: false,
                        success: function a(result){
                          
                          $('#freeaslotDisplay').html(result).slideDown("fast");
                          $('#freeaslotDisplay span').css({
                              'background-color': 'rgb(10, 162, 18)',
                              'border-radius': '10px'
                          });
                          }
                        });


                        $.ajax({
                        url:'<?=base_url();?>Menu/getTaskAvailableTimeByTaskID',
                        type: "POST",
                        data: {
                          sdate: '<?= $adate; ?>',
                          avltimeslct: avltimeslct,
                          ntactionnew_val: ntactionnew_val
                        },
                        cache: false,
                        success: function a(result){
                          $('#meeting-time').html("");
                          $('#meeting-time').html(result);
                          $('#meeting-time option.text-success').css({ 'color': 'green' });
                          $('#meeting-time option.text-danger').css({ 'color': 'red' });
                        }
                        });
                  }else{
                      $.ajax({
                        url:'<?=base_url();?>Menu/getTaskAvailableTimeByTaskID',
                        type: "POST",
                        data: {
                          sdate: '<?= $adate; ?>',
                          avltimeslct: avltimeslct,
                          ntactionnew_val: ntactionnew_val
                        },
                        cache: false,
                        success: function a(result){
                          $('#meeting-time').html("");
                          $('#meeting-time').html(result);
                          $('#meeting-time option.text-success').css({ 'color': 'green' });
                          $('#meeting-time option.text-danger').css({ 'color': 'red' });
                        }
                        });
                  }


                      $.ajax({
                      url:'<?=base_url();?>Menu/getPendingTeamMoM',
                      type: "POST",
                      data: {
                      uid: uid
                      },
                      cache: false,
                      success: function a(result){
                          if(result !== ''){
                          // $("#maintaskcard").show();
                          // $("#selectcompany").hide();
                          // $("#taskplanningimg").hide();
                          // $("#selectcompanybyuser").show();
                          // $("#tptime").show();
                          // $('#meeting-time').show();
                          // $('#planbtn1').show();
                          // $('#ntactionnew').hide();
                          // $('#ntactionnew').hide();
                          // $('#ntppose').hide();
                          // $('#ntppose,#ntactionnew,#selectcompanybyuser').removeAttr('required');
      
                          $("#maintaskcard").show();    
                          $("#selectcompany").show();    
                          $("#selectcompanybyuser").html(result);
                          $("#selectcompanybyuser").show();
                          var optionCount = $('#selectcompanybyuser').find('option').length;
                          if(optionCount > 0){
                            optionCount = optionCount-1;
                          }else{
                            optionCount = 0;
                          }
                          
                          $("#totalcompany").text('Total Company : '+ optionCount);
      
                          $('#pendingdata_message').show();
                          $('#pendingdata_message').text(optionCount +" Company Pending Praposal For Check");
      
                          $("#tptime").show();
                          $('#ntactionnew').show();
                          $('#ntppose').show();
                          $('#meeting-time').show();
                          $('#planbtn1').show();
                          $('#ntactionnew').hide();
                          $('#ntppose').hide();
                          $("#taskplanningimg").hide();
                          $('#planbtn1').click(function() {
                                  var newValue = '0';
                                  var newText = 'Pending Task Action';
                                  $('#ntactionnew').append(new Option(newText, newValue));
                                  $('#ntppose').append(new Option(newText, newValue));
                                  $('#ntactionnew').val(newValue);
                                  $('#ntppose').val(newValue);   
                              });
                        }
                        $('#pendingdata_message').show();
                        $('#pendingdata_message').text(optionCount +" Pending MOM For Check");
                        $('#check_data').val(selectedValue);
                    }
                  });
                }else if(selectedValue =='Proposal Check' || selectedValue =='Handover Check'){


                    var avltimeslct     =   $('#getAvailableTime').val();
                    var ntactionnew_val = 21;

                    if (avltimeslct !== null) {

                          $.ajax({
                            url:'<?=base_url();?>Menu/getTaskAvailableTime',
                            type: "POST",
                            data: {
                              sdate: '<?= $adate; ?>',
                              avltimeslct: avltimeslct,
                              ntactionnew_val: ntactionnew_val
                            },
                            cache: false,
                            success: function a(result){
                              
                              $('#freeaslotDisplay').html(result).slideDown("fast");
                              $('#freeaslotDisplay span').css({
                                  'background-color': 'rgb(10, 162, 18)',
                                  'border-radius': '10px'
                              });
                              }
                            });


                            $.ajax({
                            url:'<?=base_url();?>Menu/getTaskAvailableTimeByTaskID',
                            type: "POST",
                            data: {
                              sdate: '<?= $adate; ?>',
                              avltimeslct: avltimeslct,
                              ntactionnew_val: ntactionnew_val
                            },
                            cache: false,
                            success: function a(result){
                              $('#meeting-time').html("");
                              $('#meeting-time').html(result);
                              $('#meeting-time option.text-success').css({ 'color': 'green' });
                              $('#meeting-time option.text-danger').css({ 'color': 'red' });
                            }
                            });
                      }else{
                          $.ajax({
                            url:'<?=base_url();?>Menu/getTaskAvailableTimeByTaskID',
                            type: "POST",
                            data: {
                              sdate: '<?= $adate; ?>',
                              avltimeslct: avltimeslct,
                              ntactionnew_val: ntactionnew_val
                            },
                            cache: false,
                            success: function a(result){
                              $('#meeting-time').html("");
                              $('#meeting-time').html(result);
                              $('#meeting-time option.text-success').css({ 'color': 'green' });
                              $('#meeting-time option.text-danger').css({ 'color': 'red' });
                            }
                            });
                      }

              
              $.ajax({
                url:'<?=base_url();?>Menu/getPendingPraposalHandover',
                type: "POST",
                data: {
                uid: uid,
                selectedValue:selectedValue
                },
                cache: false,
                success: function a(result){
                  $('#check_data').val(selectedValue);
                    $("#maintaskcard").show();    
                    $("#selectcompany").show();    
                    $("#selectcompanybyuser").html(result);
                    $("#selectcompanybyuser").show();
                    var optionCount = $('#selectcompanybyuser').find('option').length;
                    if(optionCount > 0){
                      optionCount = optionCount-1;
                    }else{
                      optionCount = 0;
                    }
                    
                    $("#totalcompany").text('Total Company : '+ optionCount);
      
                    $('#pendingdata_message').show();
                    $('#pendingdata_message').text(optionCount +" Company Pending Praposal For Check");
      
                    $("#tptime").show();
                    $('#ntactionnew').show();
                    $('#ntppose').show();
                    $('#meeting-time').show();
                    $('#planbtn1').show();
                    $('#ntactionnew').hide();
                    $('#ntppose').hide();
                    $("#taskplanningimg").hide();
                    $('#planbtn1').click(function() {
                            var newValue = '0';
                            var newText = 'Pending Task Action';
                            $('#ntactionnew').append(new Option(newText, newValue));
                            $('#ntppose').append(new Option(newText, newValue));
                            $('#ntactionnew').val(newValue);
                            $('#ntppose').val(newValue);   
                        });
              }
            });
          }
              }
          });
          }else{
            $('#auto_assign').hide();
            $('#ntppose, #ntactionnew, #selectcompanybyuser').attr('required', 'required');
            $("#selectcompany").show();
            $('#pendingdata_message').hide();
      
          }
          if(val == 'Compulsive Task'){
          $('#compulsive_task_card').show();
          $(".taskselectionarea").hide();
          $('#partnertype').hide();
          $('#maintaskcard').hide();
          $('#status_taskaction_card').hide();
          $('#taskaction_card_area').hide();
          $('#taskActionbyuserCard').hide();
          $('#taskPurposebyuserCard').hide();
          var uid = $("#curuserid").val();
          
          $('#statusnochanhecomp_status').on('change', function() {
          var selectstatusbyusernotplaned = $(this).val();
          var adate = "<?php echo $adate;?>";
          $("#selectcompanybyuser").html('');
          $("#totalcompany").text('');
          var uid = $("#curuserid").val();
      
              $.ajax({
              url:'<?=base_url();?>Menu/nostatuschange_indate',
              type: "POST",
              data: {
              sid: selectstatusbyusernotplaned,
              uid: uid,
      adate:adate
              },
              cache: false,
              success: function a(result){
              $("#maintaskcard").show();
              $("#selectcompanybyuser").html(result);
              $("#selectcompanybyuser").show();
              var optionCount = $('#selectcompanybyuser').find('option').length;
              optionCount = optionCount-1;
              $("#totalcompany").text('Total Company :'+ optionCount);
              $("#tptime").show();
              $('#ntactionnew').show();
              $('#ntppose').show();
              $('#meeting-time').show();
              $('#planbtn1').show();
              $("#taskplanningimg").hide();
              $('#ntactionnew option').prop('disabled', true);
              $('#ntactionnew option[value="1"]').prop('disabled', false);
              }
              });
          });
          }else{
            $('#compulsive_task_card').hide();
            $('#ntactionnew option').prop('disabled', false);
            $("#selectcompany").show();
            $('#pendingdata_message').hide();
          }
          if(val == 'actionNotPlannedNeed'){
            $("#actionnotplaned_NeedYour").show();
            $("#actionPlanned").hide();
            $(".taskselectionarea").hide();
            $('#selectstatusbyusernotplanedCompany').on('change', function() {
          var selectstatusbyusernotplaned = $(this).val();
          $("#selectcompanybyuser").html('');
          $("#totalcompany").text('');
          var uid = $("#curuserid").val();
      
              $.ajax({
              url:'<?=base_url();?>Menu/getstatuscmpnotplanedCompany',
              type: "POST",
              data: {
              sid: selectstatusbyusernotplaned,
              uid: uid
              },
              cache: false,
              success: function a(result){
              $("#maintaskcard").show();
              $("#selectcompanybyuser").html(result);
              $("#selectcompanybyuser").show();
              var optionCount = $('#selectcompanybyuser').find('option').length;
              optionCount = optionCount-1;
              $("#totalcompany").text('Total Company :'+ optionCount);
              $("#tptime").show();
              $('#ntactionnew').show();
              $('#ntppose').show();
              $('#meeting-time').show();
              $('#planbtn1').show();
              $("#taskplanningimg").hide();
              $('#ntactionnew option').prop('disabled', true);
              $('#ntactionnew option[value="1"]').prop('disabled', false);
              }
              });
          });
          }else{
          $("#actionnotplaned_NeedYour").hide();
          $("#daysfiltercard_anp").hide();
          $('#ntactionnew option').prop('disabled', false);
          $("#taskplanningimg").show();
          }
          if(val == 'Review Planning'){
            $("#review_planning_card").show();
            $(".taskselectionarea").hide();
            $('#maintaskcard').hide();
          }else{
            $("#review_planning_card").hide();
          }
          if(val == 'Need Your Attention'){
            $("#need_your_attention").show();
      
      
          $(".taskselectionarea").hide();
          $('#partnertype').hide();
          $('#maintaskcard').hide();
          $('#status_taskaction_card').hide();
          $('#taskaction_card_area').hide();
          $('#taskActionbyuserCard').hide();
          $('#taskPurposebyuserCard').hide();
          var uid = $("#curuserid").val();
          $('#need_your_attention_slsct').on('change', function() {
          var selectstatusbyusernotplaned = $(this).val();
          $("#selectcompanybyuser").html('');
          $("#totalcompany").text('');
          var uid = $("#curuserid").val();
              $.ajax({
              url:'<?=base_url();?>Menu/NeedYourAttentionInCompany',
              type: "POST",
              data: {
              sid: selectstatusbyusernotplaned,
              uid: uid,
              adate: '<?=$adate;?>'
              },
              cache: false,
              success: function a(result){
              $("#maintaskcard").show();
              $("#selectcompanybyuser").html(result);
              $("#selectcompanybyuser").show();
              var optionCount = $('#selectcompanybyuser').find('option').length;
              optionCount = optionCount-1;
              $("#totalcompany").text('Total Company :'+ optionCount);
              $("#tptime").show();
              $('#ntactionnew').show();
              $('#ntppose').show();
              $('#meeting-time').show();
              $('#planbtn1').show();
              $("#taskplanningimg").hide();
              $('#ntactionnew option').prop('disabled', true);
              $('#ntactionnew option[value="1"]').prop('disabled', false);
              }
              });
          });
        }else{
          $("#need_your_attention").hide();
        }
      
      if(val == 'Because of Plan Change'){
          $('#taskActionCard').show();
          $('#becauseofplanchange').show();
          var uid = $("#curuserid").val();
          $(".taskselectionarea").hide();
          $('#partnertype').hide();
          $('#maintaskcard').hide();
          $('#status_taskaction_card').hide();
          $('#taskaction_card_area').hide();
          $('#taskActionbyuserCard').hide();
          $('#taskPurposebyuserCard').hide();
          
          $('#planChange_TaskData').on('change', function() {

          var planChange_action = $(this).val();

          var avltimeslct     =   $('#getAvailableTime').val();
          var ntactionnew_val = planChange_action;

          if (avltimeslct !== null) {

                $.ajax({
                  url:'<?=base_url();?>Menu/getTaskAvailableTime',
                  type: "POST",
                  data: {
                    sdate: '<?= $adate; ?>',
                    avltimeslct: avltimeslct,
                    ntactionnew_val: ntactionnew_val
                  },
                  cache: false,
                  success: function a(result){
                    
                    $('#freeaslotDisplay').html(result).slideDown("fast");
                    $('#freeaslotDisplay span').css({
                        'background-color': 'rgb(10, 162, 18)',
                        'border-radius': '10px'
                    });
                    }
                  });


                  $.ajax({
                  url:'<?=base_url();?>Menu/getTaskAvailableTimeByTaskID',
                  type: "POST",
                  data: {
                    sdate: '<?= $adate; ?>',
                    avltimeslct: avltimeslct,
                    ntactionnew_val: ntactionnew_val
                  },
                  cache: false,
                  success: function a(result){
                    $('#meeting-time').html("");
                    $('#meeting-time').html(result);
                    $('#meeting-time option.text-success').css({ 'color': 'green' });
                    $('#meeting-time option.text-danger').css({ 'color': 'red' });
                  }
                  });
            }else{
                $.ajax({
                  url:'<?=base_url();?>Menu/getTaskAvailableTimeByTaskID',
                  type: "POST",
                  data: {
                    sdate: '<?= $adate; ?>',
                    avltimeslct: avltimeslct,
                    ntactionnew_val: ntactionnew_val
                  },
                  cache: false,
                  success: function a(result){
                    $('#meeting-time').html("");
                    $('#meeting-time').html(result);
                    $('#meeting-time option.text-success').css({ 'color': 'green' });
                    $('#meeting-time option.text-danger').css({ 'color': 'red' });
                  }
                  });
            }



          $("#selectcompanybyuser").html('');
          $("#totalcompany").text('');
          var uid = $("#curuserid").val();
              $.ajax({
              url:'<?=base_url();?>Menu/getcmp_becauseofplanchange',
              type: "POST",
              data: {
              taskaction: planChange_action,
              uid: uid
              },
              cache: false,
              success: function a(result){
              $("#maintaskcard").show();
              $("#selectcompanybyuser").html(result);
              $("#selectcompanybyuser").show();
              var optionCount = $('#selectcompanybyuser').find('option').length;
              if(optionCount == 0){
                  optionCount = 0;
              }
              $("#totalcompany").text('Total Company :'+ optionCount);
              $("#tptime").show();
              $('#ntactionnew').show();
              $('#ntppose').show();
              $('#meeting-time').show();
              $('#planbtn1').show();
              $('#partnertype_cstatus').show();
              $('#ntactionnew').hide();
              $('#ntppose').hide();
              $("#taskplanningimg").hide();
              $('#planbtn1').click(function() {
                      var newValue = '0';
                      var newText = 'Pending Task Action';
                      $('#ntactionnew').append(new Option(newText, newValue));
                      $('#ntppose').append(new Option(newText, newValue));
                      $('#ntactionnew').val(newValue);
                      $('#ntppose').val(newValue);   
                  });
              }
              });
          });
          }else{
            $('#becauseofplanchange').hide();
          }
      
          if(val =='Create Emergency Meetings Task'){
            $('#task_action_filter option').each(function() {
                if ($(this).val() !== '3' && $(this).val() !== '4' && $(this).val() !== '') {
                    $(this).prop('disabled', true);
                }
            });
         
            $("#selectby").val(val);
          var uid = $("#curuserid").val();
          $(".taskselectionarea").hide();
          $('#taskActionCard').show();
          $('#maintaskcard').hide();
          $('#taskaction_card_area').show();
          $('#task_action_filter').show();
          // $('#taskaction_card_area').hide();
          $('#taskPurposebyuserCard').hide();
          $('#taskActionbyuserCard').hide();
          $('#status_taskaction').hide();

          $.ajax({
            url:'<?=base_url();?>Menu/CheckCashIsAvailable',
            type: "POST",
            data: {
              slctactval:'TaskAction',
            },
            cache: false,
            success: function a(result){
              if(result == 0){
                $('#task_action_filter option[value="3"]').prop('disabled', true);
                $('#task_action_filter option[value="4"]').prop('disabled', true);
                $('#task_action_filter option[value="17"]').prop('disabled', true);
                $('#meetingrelmsg').text('* Please Purchase cash for create meeting task').css('color', 'red');
              }else{
                $('#task_action_filter option[value="3"]').prop('disabled', false);
                $('#task_action_filter option[value="4"]').prop('disabled', false);
                $('#task_action_filter option[value="17"]').prop('disabled', false);
                $('#meetingrelmsg').text('');
              }
            }
          });

          $('#task_action_filter').on('change', function() {
              $("#taskplanningimg").hide();
              $("#selectcompanybyuser").html('');
              $("#totalcompany").text('');
              var uid = $("#curuserid").val();
              var tasktaction = $(this).val();


              if(tasktaction ==3){


                var avltimeslct     =   $('#getAvailableTime').val();
                var ntactionnew_val = tasktaction;
                if (avltimeslct !== null) {
                      $.ajax({
                        url:'<?=base_url();?>Menu/getTaskAvailableTime',
                        type: "POST",
                        data: {
                          sdate: '<?= $adate; ?>',
                          avltimeslct: avltimeslct,
                          ntactionnew_val: ntactionnew_val
                        },
                        cache: false,
                        success: function a(result){
                          $('#freeaslotDisplay').html(result).slideDown("fast");
                          $('#freeaslotDisplay span').css({
                              'background-color': 'rgb(10, 162, 18)',
                              'border-radius': '10px'
                          });
                          }
                        });

                        $.ajax({
                        url:'<?=base_url();?>Menu/getTaskAvailableTimeByTaskID',
                        type: "POST",
                        data: {
                          sdate: '<?= $adate; ?>',
                          avltimeslct: avltimeslct,
                          ntactionnew_val: ntactionnew_val
                        },
                        cache: false,
                        success: function a(result){
                          $('#meeting-time').html("");
                          $('#meeting-time').html(result);
                          $('#meeting-time option.text-success').css({ 'color': 'green' });
                          $('#meeting-time option.text-danger').css({ 'color': 'red' });
                        }
                        });
                  }else{
                      $.ajax({
                        url:'<?=base_url();?>Menu/getTaskAvailableTimeByTaskID',
                        type: "POST",
                        data: {
                          sdate: '<?= $adate; ?>',
                          avltimeslct: avltimeslct,
                          ntactionnew_val: ntactionnew_val
                        },
                        cache: false,
                        success: function a(result){
                          $('#meeting-time').html("");
                          $('#meeting-time').html(result);
                          $('#meeting-time option.text-success').css({ 'color': 'green' });
                          $('#meeting-time option.text-danger').css({ 'color': 'red' });
                        }
                        });
                  }










                $("#ntppose").html("<option value=''>Select Purpose</option>");
                $('#status_taskaction_card').hide();
                $('#status_taskaction').hide();
                $('#taskActionbyuserCard').hide();
                $('#taskPurposebyuserCard').hide();
                $("#selectcompany").show();
                $("#bargmeetingcluster").show();
                $("#bcytpe").hide();
                $('#selectReseachCompanyType').hide();
                $.ajax({
                url:'<?=base_url();?>Menu/get_SheduledMeetCompany',
                type: "POST",
                data: {
                uid: uid
                },
                cache: false,
                success: function a(result){
                  $("#maintaskcard").show();
                  $("#selectcompanybyuser").html(result);
                  $("#selectcompanybyuser").show();
                  $("#select_cluster").show();
                  var optionCount = $('#selectcompanybyuser').find('option').length;
                  optionCount = optionCount-1;
                  $("#totalcompany").text('Total Company :'+ optionCount);
                  $("#tasktaction").show();
                  $("#tptime").val('');
                  $("#tptime").show();
                  $('#ntactionnew').show();
                  $('#ntppose').show();
                  $('#meeting-time').show();
                  $('#planbtn1').show();
                  $("#taskplanningimg").hide();
                var newOption = $('<option>', {
                    value: '3',
                    text: 'Scheduled Meeting',
                    selected: true,
                });
                $('#ntactionnew').append(newOption);
                $('#ntactionnew option').prop('disabled', true);
                $('#ntactionnew option[value="3"]').prop('disabled', false);
                var inidids = '';
                  $('#selectcompanybyuser').change(function() {
                  var inidids = '';
                  $('#selectcompanybyuser :selected').each(function(i, sel){
                      inidids += $(sel).val() + ',';
                  });
                  inidids = inidids.slice(0, -1);
                  $.ajax({
                    url:'<?=base_url();?>Menu/getpurposebyinidnew',
                    type: "POST",
                    data: {
                    inid: inidids,
                    aid: 3
                    },
                    cache: false,
                    success: function a(result){
                      console.log("5"+result);
                      $("#ntppose").html(result);
                    }
                    });
                    $.ajax({
                    url:'<?=base_url();?>Menu/getClusterNameByiniid',
                    type: "POST",
                    data: {
                    inid: inidids,
                    },
                    cache: false,
                    success: function a(result){
                     $("#select_cluster").removeAttr('required');
                      $("#select_cluster").html(result);
                    }
                    });
              });
                }
                });


                $('#bargmeetingcluster_slct').on('change', function() {
                var slsct_task_action_filter = $("#task_action_filter").val();
                if(slsct_task_action_filter == 3){

                  var meetingcluster_slct = $(this).val();
                  if(meetingcluster_slct == 'all' || meetingcluster_slct == ''){
                    $('#select_cluster option').prop('disabled', false);
                  }else if(meetingcluster_slct == 'no'){
                    $('#select_cluster option').prop('disabled', false);
                  }else{

                      $.ajax({
                      url:'<?=base_url();?>Menu/GetClusterOptionsOnPlanner',
                      type: "POST",
                      data: {
                      uid: uid,
                      travelType:'MainClusterFilter',
                      },
                      cache: false,
                      success: function a(result){
                        $("#select_cluster").html('');
                        $("#select_cluster").html(result);
                        $('#select_cluster').val(meetingcluster_slct);
                        $('#select_cluster option').not(':selected').prop('disabled', true);
                        $('#ntactionnew option').prop('disabled', true); // Disable all options
                        $('#ntactionnew option:selected').prop('disabled', false); // Enable only the selected one 
                      }
                      });
                  }
               
                  $.ajax({
                  url:'<?=base_url();?>Menu/get_SheduledMeetCompany_with_cluster',
                  type: "POST",
                  data: {
                  uid: uid,
                  meetingcluster_slct: meetingcluster_slct,
                  task_action_filter : slsct_task_action_filter
                  },
                  cache: false,
                  success: function a(result){
                    $("#maintaskcard").show();
                    $("#selectcompanybyuser").html(result);
                    $("#selectcompanybyuser").show();
                    $("#select_cluster").show();
                    var optionCount = $('#selectcompanybyuser').find('option').length;
                    optionCount = optionCount-1;
                    $("#totalcompany").text('Total Company :'+ optionCount);
                    $("#tasktaction").show();
                    $("#tptime").val('');
                    $("#tptime").show();
                    $('#ntactionnew').show();
                    $('#ntppose').show();
                    $('#meeting-time').show();
                    $('#planbtn1').show();
                    $("#taskplanningimg").hide();
              
                  $('#ntactionnew option').prop('disabled', true);
                  $('#ntactionnew option[value="3"]').prop('disabled', false);
                  var inidids = '';
                    $('#selectcompanybyuser').change(function() {
                      var inidids = '';
                      $('#selectcompanybyuser :selected').each(function(i, sel){
                          inidids += $(sel).val() + ',';
                      });
                    inidids = inidids.slice(0, -1);
                      $.ajax({
                        url:'<?=base_url();?>Menu/getpurposebyinidnew',
                        type: "POST",
                        data: {
                        inid: inidids,
                        aid: 3
                        },
                        cache: false,
                        success: function a(result){
                          // console.log("3"+result);
                          $("#ntppose").html(result);
                        }
                        });
                    });
                  }
                  });
                }
                });


                $('#travelType_taskaction_slct_for_smeeting').on('change', function() {

                    var slsct_task_action_filter  = $("#task_action_filter").val();
                    var meetingcluster_slct       = $("#bargmeetingcluster_slct").val();

                    $("#bargmeetingcluster_slct, #status_taskaction_slct_for_smeeting, #bd_taskaction_slct_for_smeeting").val('');
                    

                    if(slsct_task_action_filter == 3){

                      var travelType_taskaction_slct_for_smeeting = $(this).val();

                          $.ajax({
                            url:'<?=base_url();?>Menu/GetClusterOptionsOnPlanner',
                            type: "POST",
                            data: {
                            uid: uid,
                            travelType: travelType_taskaction_slct_for_smeeting,
                            },
                            cache: false,
                            success: function a(result){
                              $("#bargmeetingcluster_slct").html('');
                              $("#bargmeetingcluster_slct").html(result);
                            }
                            });

                      if(meetingcluster_slct == 'all' || meetingcluster_slct == ''){
                        $('#select_cluster option').prop('disabled', false);
                      }else if(meetingcluster_slct == 'no'){
                        $('#select_cluster option').prop('disabled', false);
                      }else{
                        $('#select_cluster').val(meetingcluster_slct);
                        $('#select_cluster option').not(':selected').prop('disabled', true);
                      }

                      $.ajax({
                      url:'<?=base_url();?>Menu/get_SheduledMeetCompany_with_cluster_travel_type',
                      type: "POST",
                      data: {
                      uid: uid,
                      meetingcluster_slct: meetingcluster_slct,
                      travelType: travelType_taskaction_slct_for_smeeting,
                      task_action_filter : slsct_task_action_filter
                      },
                      cache: false,
                      success: function a(result){
                        $("#selectcompanybyuser").html('');
                        $("#maintaskcard").show();
                        $("#selectcompanybyuser").html(result);
                        $("#selectcompanybyuser").show();
                        $("#select_cluster").show();
                        var optionCount = $('#selectcompanybyuser').find('option').length;
                        optionCount = optionCount-1;
                        $("#totalcompany").text('Total Company :'+ optionCount);
                        $("#tasktaction").show();
                        $("#tptime").val('');
                        $("#tptime").show();
                        $('#ntactionnew').show();
                        $('#ntppose').show();
                        $('#meeting-time').show();
                        $('#planbtn1').show();
                        $("#taskplanningimg").hide();

                      $('#ntactionnew option').prop('disabled', true);
                      $('#ntactionnew option[value="3"]').prop('disabled', false);
                      var inidids = '';
                        $('#selectcompanybyuser').change(function() {
                          var inidids = '';
                          $('#selectcompanybyuser :selected').each(function(i, sel){
                              inidids += $(sel).val() + ',';
                          });
                        inidids = inidids.slice(0, -1);
                          $.ajax({
                            url:'<?=base_url();?>Menu/getpurposebyinidnew',
                            type: "POST",
                            data: {
                            inid: inidids,
                            aid: 3
                            },
                            cache: false,
                            success: function a(result){
                              // console.log("3"+result);
                              $("#ntppose").html(result);
                            }
                            });
                        });
                      }
                      });
                    }
                    });

                $('#status_taskaction_slct_for_smeeting').on('change', function() {

                    var slsct_task_action_filter   = $("#task_action_filter").val();
                    var meetingcluster_slct        = $("#bargmeetingcluster_slct").val();
                    var travelType                 = $("#travelType_taskaction_slct_for_smeeting").val();

                    if(slsct_task_action_filter == 3){

                      var status_taskaction_slct_for_smeeting = $(this).val();

                      if(meetingcluster_slct == 'all' || meetingcluster_slct == ''){
                        $('#select_cluster option').prop('disabled', false);
                      }else if(meetingcluster_slct == 'no'){
                        $('#select_cluster option').prop('disabled', false);
                      }else{
                        $('#select_cluster').val(meetingcluster_slct);
                        $('#select_cluster option').not(':selected').prop('disabled', true);
                      }

                      if(travelType == ''){
                        alert("Please Select Travel Type");
                        return false;
                      }
                      
                      $.ajax({
                      url:'<?=base_url();?>Menu/get_SheduledMeetCompany_with_cluster_status',
                      type: "POST",
                      data: {
                      uid: uid,
                      meetingcluster_slct: meetingcluster_slct,
                      status_taskaction_slct_for_smeeting: status_taskaction_slct_for_smeeting,
                      travelType : travelType,
                      task_action_filter : slsct_task_action_filter
                      },
                      cache: false,
                      success: function a(result){
                        $("#selectcompanybyuser").html('');
                        $("#maintaskcard").show();
                        $("#selectcompanybyuser").html(result);
                        $("#selectcompanybyuser").show();
                        $("#select_cluster").show();
                        var optionCount = $('#selectcompanybyuser').find('option').length;
                        optionCount = optionCount-1;
                        $("#totalcompany").text('Total Company :'+ optionCount);
                        $("#tasktaction").show();
                        $("#tptime").val('');
                        $("#tptime").show();
                        $('#ntactionnew').show();
                        $('#ntppose').show();
                        $('#meeting-time').show();
                        $('#planbtn1').show();
                        $("#taskplanningimg").hide();

                      $('#ntactionnew option').prop('disabled', true);
                      $('#ntactionnew option[value="3"]').prop('disabled', false);
                      var inidids = '';
                        $('#selectcompanybyuser').change(function() {
                          var inidids = '';
                          $('#selectcompanybyuser :selected').each(function(i, sel){
                              inidids += $(sel).val() + ',';
                          });
                        inidids = inidids.slice(0, -1);
                          $.ajax({
                            url:'<?=base_url();?>Menu/getpurposebyinidnew',
                            type: "POST",
                            data: {
                            inid: inidids,
                            aid: 3
                            },
                            cache: false,
                            success: function a(result){
                              // console.log("3"+result);
                              $("#ntppose").html(result);
                            }
                            });
                        });
                      }
                      });
                    }
                    });

                    $('#bd_taskaction_slct_for_smeeting').on('change', function() {

                        var slsct_task_action_filter  = $("#task_action_filter").val();
                        var meetingcluster_slct       = $("#bargmeetingcluster_slct").val();
                        var status_taskaction_slct_for_smeeting = $("#status_taskaction_slct_for_smeeting").val();
                        var travelType                = $("#travelType_taskaction_slct_for_smeeting").val();

                        if(slsct_task_action_filter == 3){

                          var bd_taskaction_slct_for_smeeting = $(this).val();

                          if(meetingcluster_slct == 'all' || meetingcluster_slct == ''){
                            $('#select_cluster option').prop('disabled', false);
                          }else if(meetingcluster_slct == 'no'){
                            $('#select_cluster option').prop('disabled', false);
                          }else{
                            $('#select_cluster').val(meetingcluster_slct);
                            $('#select_cluster option').not(':selected').prop('disabled', true);
                          }
                          if(bd_taskaction_slct_for_smeeting == ''){
                            alert("Please Select BD");
                            return false;
                          }
                
                          $.ajax({
                          url:'<?=base_url();?>Menu/get_SheduledMeetCompany_with_cluster_status_BD',
                          type: "POST",
                          data: {
                          uid: uid,
                          meetingcluster_slct: meetingcluster_slct,
                          status_taskaction_slct_for_smeeting: status_taskaction_slct_for_smeeting,
                          bd_taskaction_slct_for_smeeting: bd_taskaction_slct_for_smeeting,
                          travelType : travelType,
                          task_action_filter : slsct_task_action_filter
                          },
                          cache: false,
                          success: function a(result){
                            $("#selectcompanybyuser").html('');
                            $("#maintaskcard").show();
                            $("#selectcompanybyuser").html(result);
                            $("#selectcompanybyuser").show();
                            $("#select_cluster").show();
                            var optionCount = $('#selectcompanybyuser').find('option').length;
                            optionCount = optionCount-1;
                            $("#totalcompany").text('Total Company :'+ optionCount);
                            $("#tasktaction").show();
                            $("#tptime").val('');
                            $("#tptime").show();
                            $('#ntactionnew').show();
                            $('#ntppose').show();
                            $('#meeting-time').show();
                            $('#planbtn1').show();
                            $("#taskplanningimg").hide();

                          $('#ntactionnew option').prop('disabled', true);
                          $('#ntactionnew option[value="3"]').prop('disabled', false);
                          var inidids = '';
                            $('#selectcompanybyuser').change(function() {
                              var inidids = '';
                              $('#selectcompanybyuser :selected').each(function(i, sel){
                                  inidids += $(sel).val() + ',';
                              });
                            inidids = inidids.slice(0, -1);
                              $.ajax({
                                url:'<?=base_url();?>Menu/getpurposebyinidnew',
                                type: "POST",
                                data: {
                                inid: inidids,
                                aid: 3
                                },
                                cache: false,
                                success: function a(result){
                                  // console.log("3"+result);
                                  $("#ntppose").html(result);
                                }
                                });
                            });
                          }
                          });
                        }
                        });


                        $('#new_category_filter_select_cluster').on('change', function() {

                        var slsct_task_action_filter  = $("#task_action_filter").val();
                        var meetingcluster_slct       = $("#bargmeetingcluster_slct").val();
                        var status_taskaction_slct_for_smeeting = $("#status_taskaction_slct_for_smeeting").val();
                        var travelType                = $("#travelType_taskaction_slct_for_smeeting").val();

                        if(slsct_task_action_filter == 3){

                          var bd_taskaction_slct_for_smeeting = $("#bd_taskaction_slct_for_smeeting").val();
                          var new_category_filter_select_cluster = $(this).val();

                          if(meetingcluster_slct == 'all' || meetingcluster_slct == ''){
                            $('#select_cluster option').prop('disabled', false);
                          }else if(meetingcluster_slct == 'no'){
                            $('#select_cluster option').prop('disabled', false);
                          }else{
                            $('#select_cluster').val(meetingcluster_slct);
                            $('#select_cluster option').not(':selected').prop('disabled', true);
                          }
                          if(bd_taskaction_slct_for_smeeting == ''){
                            alert("Please Select BD");
                            return false;
                          }
                
                          $.ajax({
                          url:'<?=base_url();?>Menu/get_SheduledMeetCompany_with_cluster_status_BD_newCategory',
                          type: "POST",
                          data: {
                          uid: uid,
                          meetingcluster_slct: meetingcluster_slct,
                          status_taskaction_slct_for_smeeting: status_taskaction_slct_for_smeeting,
                          bd_taskaction_slct_for_smeeting: bd_taskaction_slct_for_smeeting,
                          travelType : travelType,
                          task_action_filter : slsct_task_action_filter,
                          new_category_filter_select_cluster : new_category_filter_select_cluster
                          },
                          cache: false,
                          success: function a(result){
                            $("#selectcompanybyuser").html('');
                            $("#maintaskcard").show();
                            $("#selectcompanybyuser").html(result);
                            $("#selectcompanybyuser").show();
                            $("#select_cluster").show();
                            var optionCount = $('#selectcompanybyuser').find('option').length;
                            optionCount = optionCount-1;
                            $("#totalcompany").text('Total Company :'+ optionCount);
                            $("#tasktaction").show();
                            $("#tptime").val('');
                            $("#tptime").show();
                            $('#ntactionnew').show();
                            $('#ntppose').show();
                            $('#meeting-time').show();
                            $('#planbtn1').show();
                            $("#taskplanningimg").hide();

                          $('#ntactionnew option').prop('disabled', true);
                          $('#ntactionnew option[value="3"]').prop('disabled', false);
                          var inidids = '';
                            $('#selectcompanybyuser').change(function() {
                              var inidids = '';
                              $('#selectcompanybyuser :selected').each(function(i, sel){
                                  inidids += $(sel).val() + ',';
                              });
                            inidids = inidids.slice(0, -1);
                              $.ajax({
                                url:'<?=base_url();?>Menu/getpurposebyinidnew',
                                type: "POST",
                                data: {
                                inid: inidids,
                                aid: 3
                                },
                                cache: false,
                                success: function a(result){
                                  // console.log("3"+result);
                                  $("#ntppose").html(result);
                                }
                                });
                            });
                          }
                          });
                        }
                        });


             
                
              }else if(tasktaction ==4){


                  var avltimeslct     =   $('#getAvailableTime').val();
                  var ntactionnew_val = tasktaction;
                  if (avltimeslct !== null) {
                        $.ajax({
                          url:'<?=base_url();?>Menu/getTaskAvailableTime',
                          type: "POST",
                          data: {
                            sdate: '<?= $adate; ?>',
                            avltimeslct: avltimeslct,
                            ntactionnew_val: ntactionnew_val
                          },
                          cache: false,
                          success: function a(result){
                            
                            $('#freeaslotDisplay').html(result).slideDown("fast");
                            $('#freeaslotDisplay span').css({
                                'background-color': 'rgb(10, 162, 18)',
                                'border-radius': '10px'
                            });
                            }
                          });

                          $.ajax({
                          url:'<?=base_url();?>Menu/getTaskAvailableTimeByTaskID',
                          type: "POST",
                          data: {
                            sdate: '<?= $adate; ?>',
                            avltimeslct: avltimeslct,
                            ntactionnew_val: ntactionnew_val
                          },
                          cache: false,
                          success: function a(result){
                            $('#meeting-time').html("");
                            $('#meeting-time').html(result);
                            $('#meeting-time option.text-success').css({ 'color': 'green' });
                            $('#meeting-time option.text-danger').css({ 'color': 'red' });
                          }
                          });
                    }else{
                        $.ajax({
                          url:'<?=base_url();?>Menu/getTaskAvailableTimeByTaskID',
                          type: "POST",
                          data: {
                            sdate: '<?= $adate; ?>',
                            avltimeslct: avltimeslct,
                            ntactionnew_val: ntactionnew_val
                          },
                          cache: false,
                          success: function a(result){
                            $('#meeting-time').html("");
                            $('#meeting-time').html(result);
                            $('#meeting-time option.text-success').css({ 'color': 'green' });
                            $('#meeting-time option.text-danger').css({ 'color': 'red' });
                          }
                          });
                    }


                $("#bargmeetingcluster").hide();
                $("#ntppose").html("<option value='34'>Fresh Meeting</option>");
                $('#status_taskaction_card').hide();
                $('#status_taskaction').hide();
                $('#taskActionbyuserCard').hide();
                $('#taskPurposebyuserCard').hide();
                $('#maintaskcard').hide();
                $("#bcytpe").show();
                $('#selectbarginCompanyType').show();
                $('#selectReseachCompanyType').hide();
                $("#taskplanningimg").show();
                
                $('#bcytpe').on('change', function() {
                  var bcytpe = $(this).val();
                if(bcytpe == 'From Funnel'){
                  $("#taskplanningimg").hide();
                  $('#maintaskcard').show();
                  $("#selectcompany").show();
                  $("#bargmeetingcluster").show();
                $.ajax({
                url:'<?=base_url();?>Menu/get_BargeMeetCompany',
                type: "POST",
                data: {
                uid: uid
                },
                cache: false,
                success: function a(result){
                  $("#maintaskcard").show();
                  $("#selectcompanybyuser").html(result);
                  $("#selectcompanybyuser").show();
                  $("#select_cluster").show();
                  var optionCount = $('#selectcompanybyuser').find('option').length;
                  optionCount = optionCount-1;
                  $("#totalcompany").text('Total Company :'+ optionCount);
                  $("#tasktaction").show();
                  $("#tptime").val('');
                  $("#tptime").show();
                  $('#ntactionnew').show();
                  $('#ntppose').show();
                  $('#meeting-time').show();
                  $('#planbtn1').show();
                  $('#ntactionnew option[value="4"]').first().remove();
                var newOption = $('<option>', {
                    value: '4',
                    text: 'Barg in Meeting',
                    selected: true,
                });
                $('#ntactionnew').append(newOption);
                $("#ntppose").html("<option value='135'>Remeeting</option>");
                $('#ntactionnew option').prop('disabled', true);
                $('#ntactionnew option[value="4"]').prop('disabled', false);
                 $('#ntactionnew option:selected').prop('disabled', false); 
                }
                });

               
              
               
                  
                  

                $('#bargmeetingcluster_slct').on('change', function() {
                var slsct_task_action_filter = $("#task_action_filter").val();
                if(slsct_task_action_filter == 4){

                  var meetingcluster_slct = $(this).val();
                  if(meetingcluster_slct == 'all' || meetingcluster_slct == ''){
                    $('#select_cluster option').prop('disabled', false);
                  }else if(meetingcluster_slct == 'no'){
                    $('#select_cluster option').prop('disabled', false);
                  }else{

                      $.ajax({
                      url:'<?=base_url();?>Menu/GetClusterOptionsOnPlanner',
                      type: "POST",
                      data: {
                      uid: uid,
                      travelType:'MainClusterFilter'
                      },
                      cache: false,
                      success: function a(result){
                        $("#select_cluster").html('');
                        $("#select_cluster").html(result);
                        $('#select_cluster').val(meetingcluster_slct);
                        // $('#select_cluster option').not(':selected').prop('disabled', true);
                        // $('#ntactionnew option:selected').prop('disabled', false); 

                         $('#ntactionnew option').prop('disabled', true);
                         $('#ntactionnew option[value="4"]').prop('disabled', false);


                      }
                      });
                  }
               
                  $.ajax({
                  url:'<?=base_url();?>Menu/get_SheduledMeetCompany_with_cluster',
                  type: "POST",
                  data: {
                  uid: uid,
                  meetingcluster_slct: meetingcluster_slct,
                  task_action_filter : slsct_task_action_filter
                  },
                  cache: false,
                  success: function a(result){
                    $("#maintaskcard").show();
                    $("#selectcompanybyuser").html(result);
                    $("#selectcompanybyuser").show();
                    $("#select_cluster").show();
                    var optionCount = $('#selectcompanybyuser').find('option').length;
                    optionCount = optionCount-1;
                    $("#totalcompany").text('Total Company :'+ optionCount);
                    $("#tasktaction").show();
                    $("#tptime").val('');
                    $("#tptime").show();
                    $('#ntactionnew').show();
                    $('#ntppose').show();
                    $('#meeting-time').show();
                    $('#planbtn1').show();
                    $("#taskplanningimg").hide();
              

                    $('#ntactionnew option').prop('disabled', true);
                    $('#ntactionnew option[value="4"]').prop('disabled', false);

                  var inidids = '';
                    $('#selectcompanybyuser').change(function() {
                      var inidids = '';
                      $('#selectcompanybyuser :selected').each(function(i, sel){
                          inidids += $(sel).val() + ',';
                      });
                    inidids = inidids.slice(0, -1);
                      $.ajax({
                        url:'<?=base_url();?>Menu/getpurposebyinidnew',
                        type: "POST",
                        data: {
                        inid: inidids,
                        aid: 3
                        },
                        cache: false,
                        success: function a(result){
                          // console.log("3"+result);
                          $("#ntppose").html(result);
                        }
                        });
                    });
                  }
                  });
                }
                });


                $('#travelType_taskaction_slct_for_smeeting').on('change', function() {

                    var slsct_task_action_filter  = $("#task_action_filter").val();
                    var meetingcluster_slct       = $("#bargmeetingcluster_slct").val();

                    $("#bargmeetingcluster_slct, #status_taskaction_slct_for_smeeting, #bd_taskaction_slct_for_smeeting").val('');
                    

                    if(slsct_task_action_filter == 4){

                      var travelType_taskaction_slct_for_smeeting = $(this).val();

                          $.ajax({
                            url:'<?=base_url();?>Menu/GetClusterOptionsOnPlanner',
                            type: "POST",
                            data: {
                            uid: uid,
                            travelType: travelType_taskaction_slct_for_smeeting
                            },
                            cache: false,
                            success: function a(result){
                              $("#bargmeetingcluster_slct").html('');
                              $("#bargmeetingcluster_slct").html(result);
                            }
                            });

                      if(meetingcluster_slct == 'all' || meetingcluster_slct == ''){
                        $('#select_cluster option').prop('disabled', false);
                      }else if(meetingcluster_slct == 'no'){
                        $('#select_cluster option').prop('disabled', false);
                      }else{
                        $('#select_cluster').val(meetingcluster_slct);
                        $('#select_cluster option').not(':selected').prop('disabled', true);
                      }

                      $.ajax({
                      url:'<?=base_url();?>Menu/get_SheduledMeetCompany_with_cluster_travel_type',
                      type: "POST",
                      data: {
                      uid: uid,
                      meetingcluster_slct: meetingcluster_slct,
                      travelType: travelType_taskaction_slct_for_smeeting,
                      task_action_filter : slsct_task_action_filter
                      },
                      cache: false,
                      success: function a(result){
                        $("#selectcompanybyuser").html('');
                        $("#maintaskcard").show();
                        $("#selectcompanybyuser").html(result);
                        $("#selectcompanybyuser").show();
                        $("#select_cluster").show();
                        var optionCount = $('#selectcompanybyuser').find('option').length;
                        optionCount = optionCount-1;
                        $("#totalcompany").text('Total Company :'+ optionCount);
                        $("#tasktaction").show();
                        $("#tptime").val('');
                        $("#tptime").show();
                        $('#ntactionnew').show();
                        $('#ntppose').show();
                        $('#meeting-time').show();
                        $('#planbtn1').show();
                        $("#taskplanningimg").hide();

                        $('#ntactionnew option').prop('disabled', true);
                        $('#ntactionnew option[value="4"]').prop('disabled', false);
                      var inidids = '';
                        $('#selectcompanybyuser').change(function() {
                          var inidids = '';
                          $('#selectcompanybyuser :selected').each(function(i, sel){
                              inidids += $(sel).val() + ',';
                          });
                        inidids = inidids.slice(0, -1);
                          $.ajax({
                            url:'<?=base_url();?>Menu/getpurposebyinidnew',
                            type: "POST",
                            data: {
                            inid: inidids,
                            aid: 3
                            },
                            cache: false,
                            success: function a(result){
                              // console.log("3"+result);
                              $("#ntppose").html(result);
                            }
                            });
                        });
                      }
                      });
                    }
                    });

                $('#status_taskaction_slct_for_smeeting').on('change', function() {

                    var slsct_task_action_filter   = $("#task_action_filter").val();
                    var meetingcluster_slct        = $("#bargmeetingcluster_slct").val();
                    var travelType                 = $("#travelType_taskaction_slct_for_smeeting").val();

                    if(slsct_task_action_filter == 4){

                      var status_taskaction_slct_for_smeeting = $(this).val();

                      if(meetingcluster_slct == 'all' || meetingcluster_slct == ''){
                        $('#select_cluster option').prop('disabled', false);
                      }else if(meetingcluster_slct == 'no'){
                        $('#select_cluster option').prop('disabled', false);
                      }else{
                        $('#select_cluster').val(meetingcluster_slct);
                        $('#select_cluster option').not(':selected').prop('disabled', true);
                      }

                      if(travelType == ''){
                        alert("Please Select Travel Type");
                        return false;
                      }
                      
                      $.ajax({
                      url:'<?=base_url();?>Menu/get_SheduledMeetCompany_with_cluster_status',
                      type: "POST",
                      data: {
                      uid: uid,
                      meetingcluster_slct: meetingcluster_slct,
                      status_taskaction_slct_for_smeeting: status_taskaction_slct_for_smeeting,
                      travelType : travelType,
                      task_action_filter : slsct_task_action_filter
                      },
                      cache: false,
                      success: function a(result){
                        $("#selectcompanybyuser").html('');
                        $("#maintaskcard").show();
                        $("#selectcompanybyuser").html(result);
                        $("#selectcompanybyuser").show();
                        $("#select_cluster").show();
                        var optionCount = $('#selectcompanybyuser').find('option').length;
                        optionCount = optionCount-1;
                        $("#totalcompany").text('Total Company :'+ optionCount);
                        $("#tasktaction").show();
                        $("#tptime").val('');
                        $("#tptime").show();
                        $('#ntactionnew').show();
                        $('#ntppose').show();
                        $('#meeting-time').show();
                        $('#planbtn1').show();
                        $("#taskplanningimg").hide();

                        $('#ntactionnew option').prop('disabled', true);
                        $('#ntactionnew option[value="4"]').prop('disabled', false);


                      var inidids = '';
                        $('#selectcompanybyuser').change(function() {
                          var inidids = '';
                          $('#selectcompanybyuser :selected').each(function(i, sel){
                              inidids += $(sel).val() + ',';
                          });
                        inidids = inidids.slice(0, -1);
                          $.ajax({
                            url:'<?=base_url();?>Menu/getpurposebyinidnew',
                            type: "POST",
                            data: {
                            inid: inidids,
                            aid: 3
                            },
                            cache: false,
                            success: function a(result){
                              // console.log("3"+result);
                              $("#ntppose").html(result);
                            }
                            });
                        });
                      }
                      });
                    }
                    });

                    $('#bd_taskaction_slct_for_smeeting').on('change', function() {

                        var slsct_task_action_filter  = $("#task_action_filter").val();
                        var meetingcluster_slct       = $("#bargmeetingcluster_slct").val();
                        var status_taskaction_slct_for_smeeting = $("#status_taskaction_slct_for_smeeting").val();
                        var travelType                = $("#travelType_taskaction_slct_for_smeeting").val();

                        if(slsct_task_action_filter == 4){

                          var bd_taskaction_slct_for_smeeting = $(this).val();

                          if(meetingcluster_slct == 'all' || meetingcluster_slct == ''){
                            $('#select_cluster option').prop('disabled', false);
                          }else if(meetingcluster_slct == 'no'){
                            $('#select_cluster option').prop('disabled', false);
                          }else{
                            $('#select_cluster').val(meetingcluster_slct);
                            $('#select_cluster option').not(':selected').prop('disabled', true);
                          }
                          if(bd_taskaction_slct_for_smeeting == ''){
                            alert("Please Select BD");
                            return false;
                          }
                
                          $.ajax({
                          url:'<?=base_url();?>Menu/get_SheduledMeetCompany_with_cluster_status_BD',
                          type: "POST",
                          data: {
                          uid: uid,
                          meetingcluster_slct: meetingcluster_slct,
                          status_taskaction_slct_for_smeeting: status_taskaction_slct_for_smeeting,
                          bd_taskaction_slct_for_smeeting: bd_taskaction_slct_for_smeeting,
                          travelType : travelType,
                          task_action_filter : slsct_task_action_filter
                          },
                          cache: false,
                          success: function a(result){
                            $("#selectcompanybyuser").html('');
                            $("#maintaskcard").show();
                            $("#selectcompanybyuser").html(result);
                            $("#selectcompanybyuser").show();
                            $("#select_cluster").show();
                            var optionCount = $('#selectcompanybyuser').find('option').length;
                            optionCount = optionCount-1;
                            $("#totalcompany").text('Total Company :'+ optionCount);
                            $("#tasktaction").show();
                            $("#tptime").val('');
                            $("#tptime").show();
                            $('#ntactionnew').show();
                            $('#ntppose').show();
                            $('#meeting-time').show();
                            $('#planbtn1').show();
                            $("#taskplanningimg").hide();

                          // $('#ntactionnew option').prop('disabled', true);
                          // $('#ntactionnew option[value="3"]').prop('disabled', false);

                           $('#ntactionnew option').prop('disabled', true);
                           $('#ntactionnew option[value="4"]').prop('disabled', false);

                          var inidids = '';
                            $('#selectcompanybyuser').change(function() {
                              var inidids = '';
                              $('#selectcompanybyuser :selected').each(function(i, sel){
                                  inidids += $(sel).val() + ',';
                              });
                            inidids = inidids.slice(0, -1);
                              $.ajax({
                                url:'<?=base_url();?>Menu/getpurposebyinidnew',
                                type: "POST",
                                data: {
                                inid: inidids,
                                aid: 3
                                },
                                cache: false,
                                success: function a(result){
                                  // console.log("3"+result);
                                  $("#ntppose").html(result);
                                }
                                });
                            });
                          }
                          });
                        }
                        });




                 $('#new_category_filter_select_cluster').on('change', function() {

                        var slsct_task_action_filter  = $("#task_action_filter").val();
                        var meetingcluster_slct       = $("#bargmeetingcluster_slct").val();
                        var status_taskaction_slct_for_smeeting = $("#status_taskaction_slct_for_smeeting").val();
                        var travelType                = $("#travelType_taskaction_slct_for_smeeting").val();

                          var bd_taskaction_slct_for_smeeting = $("#bd_taskaction_slct_for_smeeting").val();
                          var new_category_filter_select_cluster = $(this).val();

                          if(meetingcluster_slct == 'all' || meetingcluster_slct == ''){
                            $('#select_cluster option').prop('disabled', false);
                          }else if(meetingcluster_slct == 'no'){
                            $('#select_cluster option').prop('disabled', false);
                          }else{
                            $('#select_cluster').val(meetingcluster_slct);
                            $('#select_cluster option').not(':selected').prop('disabled', true);
                          }
                          if(bd_taskaction_slct_for_smeeting == ''){
                            alert("Please Select BD");
                            return false;
                          }
                
                          $.ajax({
                          url:'<?=base_url();?>Menu/get_SheduledMeetCompany_with_cluster_status_BD_newCategory',
                          type: "POST",
                          data: {
                          uid: uid,
                          meetingcluster_slct: meetingcluster_slct,
                          status_taskaction_slct_for_smeeting: status_taskaction_slct_for_smeeting,
                          bd_taskaction_slct_for_smeeting: bd_taskaction_slct_for_smeeting,
                          travelType : travelType,
                          task_action_filter : slsct_task_action_filter,
                          new_category_filter_select_cluster : new_category_filter_select_cluster
                          },
                          cache: false,
                          success: function a(result){
                            $("#selectcompanybyuser").html('');
                            $("#maintaskcard").show();
                            $("#selectcompanybyuser").html(result);
                            $("#selectcompanybyuser").show();
                            $("#select_cluster").show();
                            var optionCount = $('#selectcompanybyuser').find('option').length;
                            optionCount = optionCount-1;
                            $("#totalcompany").text('Total Company :'+ optionCount);
                            $("#tasktaction").show();
                            $("#tptime").val('');
                            $("#tptime").show();
                            $('#ntactionnew').show();
                            $('#ntppose').show();
                            $('#meeting-time').show();
                            $('#planbtn1').show();
                            $("#taskplanningimg").hide();

                          $('#ntactionnew option').prop('disabled', true);
                          $('#ntactionnew option[value="3"]').prop('disabled', false);
                          var inidids = '';
                            $('#selectcompanybyuser').change(function() {
                              var inidids = '';
                              $('#selectcompanybyuser :selected').each(function(i, sel){
                                  inidids += $(sel).val() + ',';
                              });
                            inidids = inidids.slice(0, -1);
                              $.ajax({
                                url:'<?=base_url();?>Menu/getpurposebyinidnew',
                                type: "POST",
                                data: {
                                inid: inidids,
                                aid: 3
                                },
                                cache: false,
                                success: function a(result){
                                  // console.log("3"+result);
                                  $("#ntppose").html(result);
                                }
                                });
                            });
                          }
                          });
                        });

                
                

                  }else if(bcytpe == 'Other'){
                    $("#bargmeetingcluster").hide();
                    $("#taskplanningimg").hide();
                    $("#maintaskcard").show();
                    $("#selectcompany").hide();
                    $("#select_cluster").show();
                    $("#tasktaction").show();
                    $("#tptime").val('');
                    $("#tptime").show();
                    $('#ntactionnew').show();
                    $('#ntppose').show();
                    $('#meeting-time').show();
                    $('#planbtn1').show();
                    $('#select_cluster option').prop('disabled', false);
                    $('#ntactionnew option[value="4"]').first().remove();
                    var newOption = $('<option>', {
                    value: '4',
                    text: 'Barg in Meeting',
                    selected: true,
                });
                $('#ntactionnew option').prop('disabled', true);
                $('#ntactionnew option[value="4"]').prop('disabled', false);
                $('#ntactionnew').append(newOption);
                    $("#ntppose").html("<option value='34'>Fresh Meeting</option>");
                    $('#selectcompanybyuser').removeAttr('required');
                  }
                });
              }else if(tasktaction ==17){
                $("#bargmeetingcluster").show();
                $("#ntppose").html("<option value=''>Select Purpose</option>");
                $('#status_taskaction_card').hide();
                $('#status_taskaction').hide();
                $('#taskActionbyuserCard').hide();
                $('#taskPurposebyuserCard').hide();
                $("#selectcompany").show();
                $("#bcytpe").hide();
                $('#selectReseachCompanyType').hide();
                $('#ntactionnew option').prop('disabled', true);
                $('#ntactionnew option[value="17"]').prop('disabled', false);
                
                $.ajax({
                url:'<?=base_url();?>Menu/get_JoinMeetingsCompany',
                type: "POST",
                data: {
                uid: uid
                },
                cache: false,
                success: function a(result){
                  $("#maintaskcard").show();
                  $("#selectcompanybyuser").html(result);
                  $("#selectcompanybyuser").show();
                  $("#select_cluster").show();
                  var optionCount = $('#selectcompanybyuser').find('option').length;
                  optionCount = optionCount-1;
                  $("#totalcompany").text('Total Company :'+ optionCount);
                  $("#tasktaction").show();
                  $("#tptime").val('');
                  $("#tptime").show();
                  $('#ntactionnew').show();
                  $('#ntppose').show();
                  $('#meeting-time').show();
                  $('#planbtn1').show();
                  $('#select_with_bd_pst').show();
                var newOption = $('<option>', {
                    value: '17',
                    text: 'Join Meeting',
                    selected: true,
                });
                $('#ntactionnew option').prop('disabled', true);
                $('#ntactionnew option[value="17"]').prop('disabled', false);
                $('#ntactionnew').append(newOption);
                  var inidids = '';
                  $('#selectcompanybyuser').change(function() {
                  var inidids = '';
                  $('#selectcompanybyuser :selected').each(function(i, sel){
                      inidids += $(sel).val() + ',';
                  });
                  inidids = inidids.slice(0, -1);
                  $.ajax({
                    url:'<?=base_url();?>Menu/getpurposebyinidnew',
                    type: "POST",
                    data: {
                    inid: inidids,
                    aid: 3
                    },
                    cache: false,
                    success: function a(result){
                      console.log("7"+result);
                      $("#ntppose").html(result);
                    }
                    });
                   });
                }
                });

                $('#bargmeetingcluster_slct').on('change', function() {
                  var slsct_task_action_filter = $("#task_action_filter").val();
                  if(slsct_task_action_filter == 17){
                    var meetingcluster_slct = $(this).val();
                    $("#selectcompanybyuser").html();
                    if(meetingcluster_slct == 'all' || meetingcluster_slct == ''){
                    $('#select_cluster option').prop('disabled', false);
                    }else if(meetingcluster_slct == 'no'){
                      $('#select_cluster option').prop('disabled', false);
                    }else{
                      $('#select_cluster').val(meetingcluster_slct);
                      $('#select_cluster option').not(':selected').prop('disabled', true);
                    }

                    $.ajax({
                    url:'<?=base_url();?>Menu/get_JoinMeetingsCompanyWithCluster',
                    type: "POST",
                    data: {
                    uid: uid,
                    cluster: meetingcluster_slct
                    },
                    cache: false,
                    success: function a(result){
                      $("#maintaskcard").show();
                      $("#selectcompanybyuser").html(result);
                      $("#selectcompanybyuser").show();
                      $("#select_cluster").show();
                      var optionCount = $('#selectcompanybyuser').find('option').length;
                      optionCount = optionCount-1;
                      $("#totalcompany").text('Total Company :'+ optionCount);
                      $("#tasktaction").show();
                      $("#tptime").val('');
                      $("#tptime").show();
                      $('#ntactionnew').show();
                      $('#ntppose').show();
                      $('#meeting-time').show();
                      $('#planbtn1').show();
                      $('#select_with_bd_pst').show();
                    var newOption = $('<option>', {
                        value: '17',
                        text: 'Join Meeting',
                        selected: true,
                    });
                    $('#ntactionnew option').prop('disabled', true);
                    $('#ntactionnew option[value="17"]').prop('disabled', false);
                    $('#ntactionnew').append(newOption);
                      var inidids = '';
                      $('#selectcompanybyuser').change(function() {
                      var inidids = '';
                      $('#selectcompanybyuser :selected').each(function(i, sel){
                          inidids += $(sel).val() + ',';
                      });
                      inidids = inidids.slice(0, -1);
                      $.ajax({
                        url:'<?=base_url();?>Menu/getpurposebyinidnew',
                        type: "POST",
                        data: {
                        inid: inidids,
                        aid: 3
                        },
                        cache: false,
                        success: function a(result){
                          console.log("8"+result);
                          $("#ntppose").html(result);
                        }
                        });
                      });
                    }
                    });
                  }
                });
                
              }else{
                $('#ntactionnew option[value="3"]').remove();
                $('#ntactionnew option[value="4"]').remove();
                $('#ntactionnew option[value="17"]').remove();
                $('#selectbarginCompanyType').hide();
                $("#select_cluster").hide();
                $('#selectReseachCompanyType').hide();
                $('#ntactionnew option').prop('disabled', false);
                $("#ntppose").html("<option value=''>Select Purpose</option>");
                  $.ajax({
                  url:'<?=base_url();?>Menu/taskactionnotplan_filter',
                  type: "POST",
                  data: {
                  tasktaction: tasktaction,
                  uid: uid
                  },
                  cache: false,
                  success: function a(result){
                  $("#maintaskcard").show();
                  $("#selectcompanybyuser").html(result);
                  $("#selectcompanybyuser").show();
                  var optionCount = $('#selectcompanybyuser').find('option').length;
                  optionCount = optionCount-1;
                  $("#totalcompany").text('Total Company :'+ optionCount);
                  $("#tptime").show();
                  $('#ntactionnew').show();
                  $('#ntppose').show();
                  $('#meeting-time').show();
                  $('#planbtn1').show();
                  // $('#daysfiltercard_anp').show();
                  $('#status_taskaction_card').show();
                  $('#status_taskaction').show();
                      }
                  });
              }
              });
          $('#status_taskaction').on('change', function() {
          var selectstatusbyusernotplaned = $(this).val();
          $("#selectcompanybyuser").html('');
          $("#totalcompany").text('');
          var uid = $("#curuserid").val();
          var tasktaction =  $('#task_action_filter').val();
              $.ajax({
              url:'<?=base_url();?>Menu/getstatuscmpnotplaned',
              type: "POST",
              data: {
              sid: selectstatusbyusernotplaned,
              tasktaction: tasktaction,
              uid: uid
              },
              cache: false,
              success: function a(result){
                  $("#selectcompanybyuser").html(result);
                    $("#selectcompanybyuser").show();
                    var optionCount = $('#selectcompanybyuser').find('option').length;
                optionCount = optionCount-1;
                $("#totalcompany").text('Total Company :'+ optionCount);
                $('#taskActionbyuserCard').show();
              }
              });
          });
      
      }else{
      if(val == 'Task Action'){
            $('#taskaction_card_area').show();
            $('#task_action_filter option').each(function() {
                  $(this).prop('disabled', false);
            });
        }else{
          $('#taskaction_card_area').hide();
        }
        
      }
      
      if(val =='Create BD Request'){
            $("#create_bd_request").show();
            $(".taskselectionarea").hide();
            $('#maintaskcard').hide();
            $('#selectby').val('Create BD Request');
            $('#bd_Select_Request').on('change', function() {
              var bd_Select_Request = $(this).val();
              if(bd_Select_Request =='CREATE BD REQUEST'){
                $('#whenbdrequest').show();
                $("#maintaskcard").hide();
                $("#taskplanningimg").show();
                $("#bdrequestheadings").text(bd_Select_Request);
                $("#requestbutton").text(bd_Select_Request);
                $("#request_type_slct").val('create bd request');
                $('#bd_client_type').val('');
                $("#selectrequestcompanyCard").hide();
                $("#selectrequestcompany").html('');
                $('#bd_client_type').on('change', function() {
                  var bd_client_type = $(this).val();
                    if(bd_client_type !==''){
                      $.ajax({
                          url:'<?=base_url();?>Menu/GetRequestListClientType',
                          type: "POST",
                          data: {
                            bd_client_type: bd_client_type,
                          },
                          cache: false,
                          success: function a(result){
                              $("#bd_request_rype_card").show();
                              $("#bd_request_rype").html(result); 
                          }
                          });
                          $.ajax({
                              url:'<?=base_url();?>Menu/GetRequestCompanyListClientType',
                              type: "POST",
                              data: {
                                bd_client_type: bd_client_type,
                              },
                              cache: false,
                              success: function a(result){
                                  $("#selectrequestcompanyCard").show();
                                  $("#selectrequestcompany").html(result);
                                  var optionCount = $('#selectrequestcompany').find('option').length;
                                  if(optionCount == 0){
                                      optionCount = 0;
                                  }else{
                                    optionCount = optionCount -1;
                                  }
                                  $("#selectrequestcompanycnt").text(optionCount +' Company Found !!'); 
                              }
                              });
                    }else{
                      alert("* Please Select Proper Client Type");
                    }
                });
              }else if(bd_Select_Request == 'Submit Handover'){
                    $('#whenbdrequest').hide();
                    $('#selectby').val('Submit Handover');
                    $("#taskplanningimg").hide();
                    $("#requestbutton").text(bd_Select_Request);
                    $("#request_type_slct").val(bd_Select_Request);
                    $("#taskplanningimg").show();
                    $("#bdrequestheadings").text(bd_Select_Request);
                    $.ajax({
                          url:'<?=base_url();?>Menu/GetHandoverCompanyList',
                          type: "POST",
                          data: {
                            requestType: 'Submit Handover',
                          },
                          cache: false,
                          success: function a(result){
                                  $("#selectrequestcompanyCard").show();
                                  $("#selectrequestcompany").html(result);
                                  var optionCount = $('#selectrequestcompany').find('option').length;
                                  if(optionCount == 0){
                                      optionCount = 0;
                                  }else{
                                    optionCount = optionCount -1;
                                  }
                                  $("#selectrequestcompanycnt").text(optionCount +' Company Found !!');  
                          }
                        });
                    }
              });
      }else{
        $("#create_bd_request").hide();
      }
      if(val == 'Assign Task'){
              $('#taskActionCard').hide();
              $('#tommarowAssignTaskype').show();
              var uid = $("#curuserid").val();
              $(".taskselectionarea").hide();
              $('#partnertype').hide();
              $('#maintaskcard').hide();
              $('#status_taskaction_card').hide();
              $('#taskaction_card_area').hide();
              $('#taskActionbyuserCard').hide();
              $('#taskPurposebyuserCard').hide();
              $('#tommarowAssignTaskypeData').on('change', function() {
              var thisactionid = $(this).val();

              var avltimeslct     =   $('#getAvailableTime').val();
              var ntactionnew_val = thisactionid;

              if (avltimeslct !== null) {

                    $.ajax({
                      url:'<?=base_url();?>Menu/getTaskAvailableTime',
                      type: "POST",
                      data: {
                        sdate: '<?= $adate; ?>',
                        avltimeslct: avltimeslct,
                        ntactionnew_val: ntactionnew_val
                      },
                      cache: false,
                      success: function a(result){
                        
                        $('#freeaslotDisplay').html(result).slideDown("fast");
                        $('#freeaslotDisplay span').css({
                            'background-color': 'rgb(10, 162, 18)',
                            'border-radius': '10px'
                        });
                        }
                      });


                      $.ajax({
                      url:'<?=base_url();?>Menu/getTaskAvailableTimeByTaskID',
                      type: "POST",
                      data: {
                        sdate: '<?= $adate; ?>',
                        avltimeslct: avltimeslct,
                        ntactionnew_val: ntactionnew_val
                      },
                      cache: false,
                      success: function a(result){
                        $('#meeting-time').html("");
                        $('#meeting-time').html(result);
                        $('#meeting-time option.text-success').css({ 'color': 'green' });
                        $('#meeting-time option.text-danger').css({ 'color': 'red' });
                      }
                      });
                }else{
                    $.ajax({
                      url:'<?=base_url();?>Menu/getTaskAvailableTimeByTaskID',
                      type: "POST",
                      data: {
                        sdate: '<?= $adate; ?>',
                        avltimeslct: avltimeslct,
                        ntactionnew_val: ntactionnew_val
                      },
                      cache: false,
                      success: function a(result){
                        $('#meeting-time').html("");
                        $('#meeting-time').html(result);
                        $('#meeting-time option.text-success').css({ 'color': 'green' });
                        $('#meeting-time option.text-danger').css({ 'color': 'red' });
                      }
                      });
                }

              $("#daysByTask").show();
              $("#selectcompanybyuser").html('');
              $.ajax({
              url:'<?=base_url();?>Menu/getcmp_TommrowAssignTask',
              type: "POST",
              data: {
               actionid: thisactionid,
              uid: uid
              },
              cache: false,
              success: function a(result){
              $("#maintaskcard").show();    
              $("#selectcompanybyuser").html(result);
              $("#selectcompanybyuser").show();
              var optionCount = $('#selectcompanybyuser').find('option').length;
              optionCount = optionCount-1;
              $("#totalcompany").text('Total Company :'+ optionCount);
              $("#tptime").show();
              $('#ntactionnew').show();
              $('#ntppose').show();
              $('#meeting-time').show();
              $('#planbtn1').show();
              $('#ntactionnew').hide();
              $('#ntppose').hide();
              $("#taskplanningimg").hide();
              $('#planbtn1').click(function() {
                      var newValue = '0';
                      var newText = 'Pending Task Action';
                      $('#ntactionnew').append(new Option(newText, newValue));
                      $('#ntppose').append(new Option(newText, newValue));
                      $('#ntactionnew').val(newValue);
                      $('#ntppose').val(newValue);   
                  });
              }
              });
          });
          }else{
              $('#tommarowAssignTaskype').hide();
          }
      
          if(val =='Mandatory Task'){
                      $('#mandatory_task_card').show();
                      $('#taskActionCard').hide();
                        var uid = $("#curuserid").val();
                        $(".taskselectionarea").hide();
                        $('#partnertype').hide();
                        $('#maintaskcard').hide();
                        $('#status_taskaction_card').hide();
                        $('#taskaction_card_area').hide();
                        $('#taskActionbyuserCard').hide();
                        $('#taskPurposebyuserCard').hide();
                        $('#mandatory_task_select').on('change', function() {
                          var mandatory_task_select_val = $(this).val();
                          if(mandatory_task_select_val !==''){

                             var avltimeslct     =   $('#getAvailableTime').val();
                          var ntactionnew_val = mandatory_task_select_val;

                          if (avltimeslct !== null) {

                                $.ajax({
                                  url:'<?=base_url();?>Menu/getTaskAvailableTime',
                                  type: "POST",
                                  data: {
                                    sdate: '<?= $adate; ?>',
                                    avltimeslct: avltimeslct,
                                    ntactionnew_val: ntactionnew_val
                                  },
                                  cache: false,
                                  success: function a(result){
                                    
                                    $('#freeaslotDisplay').html(result).slideDown("fast");
                                    $('#freeaslotDisplay span').css({
                                        'background-color': 'rgb(10, 162, 18)',
                                        'border-radius': '10px'
                                    });
                                    }
                                  });


                                  $.ajax({
                                  url:'<?=base_url();?>Menu/getTaskAvailableTimeByTaskID',
                                  type: "POST",
                                  data: {
                                    sdate: '<?= $adate; ?>',
                                    avltimeslct: avltimeslct,
                                    ntactionnew_val: ntactionnew_val
                                  },
                                  cache: false,
                                  success: function a(result){
                                    $('#meeting-time').html("");
                                    $('#meeting-time').html(result);
                                    $('#meeting-time option.text-success').css({ 'color': 'green' });
                                    $('#meeting-time option.text-danger').css({ 'color': 'red' });
                                  }
                                  });
                            }else{
                                $.ajax({
                                  url:'<?=base_url();?>Menu/getTaskAvailableTimeByTaskID',
                                  type: "POST",
                                  data: {
                                    sdate: '<?= $adate; ?>',
                                    avltimeslct: avltimeslct,
                                    ntactionnew_val: ntactionnew_val
                                  },
                                  cache: false,
                                  success: function a(result){
                                    $('#meeting-time').html("");
                                    $('#meeting-time').html(result);
                                    $('#meeting-time option.text-success').css({ 'color': 'green' });
                                    $('#meeting-time option.text-danger').css({ 'color': 'red' });
                                  }
                                  });
                            }


                            $.ajax({
                                url:'<?=base_url();?>Menu/AllMandatoryRestrictionCompany',
                                type: "POST",
                                data: {
                                  user_id: uid,
                                  adate  : '<?= $adate; ?>',
                                  task   :mandatory_task_select_val
                                },
                                cache: false,
                                success: function a(result){
                                    $("#maintaskcard").show();    
                                    $("#selectcompanybyuser").html(result);
                                    $("#selectcompanybyuser").show();
                                    var optionCount = $('#selectcompanybyuser').find('option').length;
                                    optionCount = optionCount-1;
                                    $("#totalcompany").text('Total Company :'+ optionCount);
                                    $("#tptime").val('');
                                    $("#tptime").show();
                                    $('#ntactionnew').show();
                                    $('#ntppose').show();
                                    $('#meeting-time').show();
                                    $('#planbtn1').show();
                                    $("#taskplanningimg").hide();
                                    $('#ntactionnew option').each(function () {
                                        if ($(this).val() == mandatory_task_select_val) {
                                            $(this).prop('disabled', false);
                                        } else {
                                            $(this).prop('disabled', true);
                                        }
                                    });
                                }
                                });
                          }
                          });
                    }else{
                      $('#mandatory_task_card').hide();
                    }

                    if(val =='Find Company By'){

                    $("#maintaskcard").hide();
                    $(".taskselectionarea").hide();
                    $("#findCompanyByCard").show(); 
                    $("#findCompanyByTaskCard").hide();
                    $("#findCompanyByStatusCard").hide();
                    $("#findCompanyByDateCard").hide();

                    $('#findCompanyBySelect').on('change', function() {
                    var findCompanyBySelect = $(this).val();
                    if (findCompanyBySelect !== '') {
                      $("#findCompanyByDateCard").show();
                        var findCompanyByDate = '';
                      $('#findCompanyByDate').on('change', function() {
                        var findCompanyByDate = $(this).val();
                        var findCompanyBySelect = $('#findCompanyBySelect').val();
                        if (findCompanyByDate) {
                          $.ajax({
                            url: '<?=base_url();?>Menu/FindCompanyBy',
                            type: "POST",
                            data: {
                              user_id: '<?=$uid?>',
                              adate: '<?= $adate; ?>',
                              findCompanyByDate: findCompanyByDate,
                              findCompanyBySelect: findCompanyBySelect
                            },
                            cache: false,
                            success: function a(result) {
                              $("#maintaskcard").show();    
                              $("#selectcompanybyuser").html(result);
                              $("#selectcompanybyuser").show();
                              var optionCount = $('#selectcompanybyuser').find('option').length;
                              optionCount = optionCount-1;
                              $("#totalcompany").text('Total Company :'+ optionCount);
                              $("#tptime").val('');
                              $("#tptime").show();
                              $('#ntactionnew').show();
                              $('#ntppose').show();
                              $('#meeting-time').show();
                              $('#planbtn1').show();
                              $("#taskplanningimg").hide();
                              $("#findCompanyByTaskCard").show();
                            }
                          });
                        }
                      });

                      $('#findCompanyByTaskSelect').on('change', function() {
                        var findCompanyByTaskSelect = $(this).val();
                        var findCompanyByDate = $('#findCompanyByDate').val();
                        var findCompanyBySelect = $('#findCompanyBySelect').val();
                        if (findCompanyByDate) {
                          $.ajax({
                            url: '<?=base_url();?>Menu/FindCompanyBy',
                            type: "POST",
                            data: {
                              user_id: '<?=$uid?>',
                              adate: '<?= $adate; ?>',
                              findCompanyByDate: findCompanyByDate,
                              findCompanyBySelect: findCompanyBySelect,
                              findCompanyByTaskSelect:findCompanyByTaskSelect
                            },
                            cache: false,
                            success: function a(result) {
                              $("#maintaskcard").show();    
                              $("#selectcompanybyuser").html(result);
                              $("#selectcompanybyuser").show();
                              var optionCount = $('#selectcompanybyuser').find('option').length;
                              optionCount = optionCount-1;
                              $("#totalcompany").text('Total Company :'+ optionCount);
                              $("#tptime").val('');
                              $("#tptime").show();
                              $('#ntactionnew').show();
                              $('#ntppose').show();
                              $('#meeting-time').show();
                              $('#planbtn1').show();
                              $("#taskplanningimg").hide();
                              $("#findCompanyByTaskCard").show();
                              $("#findCompanyByStatusCard").show();
                            }
                          });
                        }
                      });
                      
                      $('#findCompanyByStatusSelect').on('change', function() {
                        var findCompanyByStatusSelect = $(this).val();
                        var findCompanyByTaskSelect = $('#findCompanyByTaskSelect').val();
                        var findCompanyByDate = $('#findCompanyByDate').val();
                        var findCompanyBySelect = $('#findCompanyBySelect').val();
                        if (findCompanyByDate) {
                          $.ajax({
                            url: '<?=base_url();?>Menu/FindCompanyBy',
                            type: "POST",
                            data: {
                              user_id: '<?=$uid?>',
                              adate: '<?= $adate; ?>',
                              findCompanyByDate: findCompanyByDate,
                              findCompanyBySelect: findCompanyBySelect,
                              findCompanyByTaskSelect:findCompanyByTaskSelect,
                              findCompanyByStatusSelect:findCompanyByStatusSelect
                            },
                            cache: false,
                            success: function a(result) {
                              $("#maintaskcard").show();    
                              $("#selectcompanybyuser").html(result);
                              $("#selectcompanybyuser").show();
                              var optionCount = $('#selectcompanybyuser').find('option').length;
                              optionCount = optionCount-1;
                              $("#totalcompany").text('Total Company :'+ optionCount);
                              $("#tptime").val('');
                              $("#tptime").show();
                              $('#ntactionnew').show();
                              $('#ntppose').show();
                              $('#meeting-time').show();
                              $('#planbtn1').show();
                              $("#taskplanningimg").hide();
                              $("#findCompanyByTaskCard").show();
                              $("#findCompanyByStatusCard").show();
                            }
                          });
                        }
                      });
                    } else {
                      $("#findCompanyByCard").hide(); 
                      $("#findCompanyByTaskCard").hide();
                      $("#findCompanyByStatusCard").hide();
                      $("#findCompanyByDateCard").hide();
                    }
                    });
                    }else{
                    $("#findCompanyByCard").hide();
                    }
      

                    if(val == 'Next Follow Up Date'){
                        $('#next_follow_up_date_card').show();
                        $(".taskselectionarea").hide();
                        $('#partnertype').hide();
                        $('#maintaskcard').hide();
                        $('#status_taskaction_card').hide();
                        $('#taskaction_card_area').hide();
                        $('#taskActionbyuserCard').hide();
                        $('#taskPurposebyuserCard').hide();
                        var uid = $("#curuserid").val();

                        $.ajax({
                                url:'<?=base_url();?>Menu/GetNextFollowupHaveDateData',
                                type: "POST",
                                data: {
                                  user_id: uid,
                                  adate  : '<?= $adate; ?>'
                                },
                                cache: false,
                                success: function a(result){
                                    $("#maintaskcard").show();    
                                    $("#selectcompanybyuser").html(result);
                                    $("#selectcompanybyuser").show();
                                    var optionCount = $('#selectcompanybyuser').find('option').length;
                                    optionCount = optionCount-1;
                                    $("#totalcompany").text('Total Company :'+ optionCount);
                                    $("#tptime").val('');
                                    $("#tptime").show();
                                    $('#ntactionnew').show();
                                    $('#ntppose').show();
                                    $('#meeting-time').show();
                                    $('#planbtn1').show();
                                    $("#taskplanningimg").hide();

                                    $('#ntactionnew option').each(function () {
                                        if ($(this).val() == 1) {
                                            $(this).prop('disabled', false);
                                        } else {
                                            $(this).prop('disabled', true);
                                        }
                                    });
                                  
                                }
                                });
                    }






                     // Start New Changes After 2025-07-08

                    if(val =='New Category'){
                      $('#select_cluster').hide();
                      $('#new_category_filter_card').show();
                      $('#taskActionCard').hide();
                        var uid = $("#curuserid").val();
                        $(".taskselectionarea").hide();
                        $('#partnertype').hide();
                        $('#maintaskcard').hide();
                        $('#status_taskaction_card').hide();
                        $('#taskaction_card_area').hide();
                        $('#taskActionbyuserCard').hide();
                        $('#taskPurposebyuserCard').hide();

                        $('#new_category_filter_select').on('change', function() {

                          var new_category_filter_select = $(this).val();

                          if(new_category_filter_select !==''){
                            $.ajax({
                                url:'<?=base_url();?>Menu/GetAllComapanyOnNewCategory',
                                type: "POST",
                                data: {
                                  user_id: uid,
                                  adate  : '<?= $adate; ?>',
                                  new_category_filter   :new_category_filter_select
                                },
                                cache: false,
                                success: function a(result){
                                  
                                    $("#maintaskcard").show();    
                                    $("#selectcompanybyuser").html(result);
                                    $("#selectcompanybyuser").show();
                                    var optionCount = $('#selectcompanybyuser').find('option').length;
                                    optionCount = optionCount-1;
                                    $("#totalcompany").text('Total Company :'+ optionCount);
                                    $("#tptime").val('');
                                    $("#tptime").show();
                                    $('#ntactionnew').show();
                                    $('#ntppose').show();
                                    $('#meeting-time').show();
                                    $('#planbtn1').show();
                                    $("#taskplanningimg").hide();
                                }
                                });
                          }

                          });
                    }else{
                      $('#new_category_filter_card').hide();
                    }


                    if(val =='Marked In Current Quarter'){
                      $('#select_cluster').hide();
                      $('#current_quarter_filter_card').show();
                      // $('#new_category_filter_card').show();
                      $('#taskActionCard').hide();
                        var uid = $("#curuserid").val();
                        $(".taskselectionarea").hide();
                        $('#partnertype').hide();
                        $('#maintaskcard').hide();
                        $('#status_taskaction_card').hide();
                        $('#taskaction_card_area').hide();
                        $('#taskActionbyuserCard').hide();
                        $('#taskPurposebyuserCard').hide();

                        $('#current_quarter_filter_select').on('change', function() {

                          var current_quarter_filter_select = $(this).val();

                          if(current_quarter_filter_select !==''){
                            $.ajax({
                                url:'<?=base_url();?>Menu/GetAllComapanyOnNewCurrentQCategory',
                                type: "POST",
                                data: {
                                  user_id: uid,
                                  adate  : '<?= $adate; ?>',
                                  new_category_filter   :current_quarter_filter_select
                                },
                                cache: false,
                                success: function a(result){
                                  
                                    $("#maintaskcard").show();    
                                    $("#selectcompanybyuser").html(result);
                                    $("#selectcompanybyuser").show();
                                    var optionCount = $('#selectcompanybyuser').find('option').length;
                                    optionCount = optionCount-1;
                                    $("#totalcompany").text('Total Company :'+ optionCount);
                                    $("#tptime").val('');
                                    $("#tptime").show();
                                    $('#ntactionnew').show();
                                    $('#ntppose').show();
                                    $('#meeting-time').show();
                                    $('#planbtn1').show();
                                    $("#taskplanningimg").hide();
                                }
                                });
                          }

                          });
                    }else{
                         $('#current_quarter_filter_card').hide();
                    }



                     if(val =='No Calling Done After Only Got Details'){
                      $('#select_cluster').hide();
                      $('#taskActionCard').hide();
                        var uid = $("#curuserid").val();
                        $(".taskselectionarea").hide();
                        $('#partnertype').hide();
                        $('#maintaskcard').hide();
                        $('#status_taskaction_card').hide();
                        $('#taskaction_card_area').hide();
                        $('#taskActionbyuserCard').hide();
                        $('#taskPurposebyuserCard').hide();

                         $.ajax({
                                url:'<?=base_url();?>Menu/NoCallingDoneAfterFindOnlyGotDetails',
                                type: "POST",
                                data: {
                                  user_id: uid,
                                  adate  : '<?= $adate; ?>'
                                },
                                cache: false,
                                success: function a(result){
                                  
                                    $("#maintaskcard").show();    
                                    $("#selectcompanybyuser").html(result);
                                    $("#selectcompanybyuser").show();
                                    var optionCount = $('#selectcompanybyuser').find('option').length;
                                    if(optionCount > 1){
                                      optionCount = optionCount-1;
                                    }else{
                                      optionCount = 0;
                                    }
                                    $("#totalcompany").text('Total Company :'+ optionCount);
                                    $("#tptime").val('');
                                    $("#tptime").show();
                                    $('#ntactionnew').show();
                                    $('#ntppose').show();
                                    $('#meeting-time').show();
                                    $('#planbtn1').show();
                                    $("#taskplanningimg").hide();

                                    $('#ntactionnew option').each(function () {
                                        if ($(this).val() == 1 || $(this).val() == '') {
                                            $(this).prop('disabled', false);
                                        } else {
                                            $(this).prop('disabled', true);
                                        }
                                    });
                                }
                                });

                    }


                    if(val =='Closing Timeline'){
                      $('#select_cluster').hide();
                      $('#closing_timeline_filter_card').show();
                      // $('#current_quarter_filter_card').show();
                      $('#taskActionCard').hide();
                        var uid = $("#curuserid").val();
                        $(".taskselectionarea").hide();
                        $('#partnertype').hide();
                        $('#maintaskcard').hide();
                        $('#status_taskaction_card').hide();
                        $('#taskaction_card_area').hide();
                        $('#taskActionbyuserCard').hide();
                        $('#taskPurposebyuserCard').hide();

                        $('#propose_or_clarify_filter_select_card').hide();
                        $('#closing_timeline_filter_select_card').hide();

                        $('#closing_timeline_filter_select_status').on('change', function() {

                          var closing_timeline_filter_select_status = $(this).val();


                          if(closing_timeline_filter_select_status == 3){
                             $('#propose_or_clarify_filter_select_card').show();
                             $('#closing_timeline_filter_select_card').hide();

                             $('#propose_or_clarify_filter_select').on('change', function() {

                              var propose_or_clarify_filter_select = $(this).val();

                                if(propose_or_clarify_filter_select !==''){
                                  $.ajax({
                                      url:'<?=base_url();?>Menu/GetProposeOrClarifyFilter',
                                      type: "POST",
                                      data: {
                                        user_id: uid,
                                        adate  : '<?= $adate; ?>',
                                        clarify_filter   :propose_or_clarify_filter_select,
                                        status   :closing_timeline_filter_select_status
                                      },
                                      cache: false,
                                      success: function a(result){
                                          $("#maintaskcard").show();    
                                          $("#selectcompanybyuser").html(result);
                                          $("#selectcompanybyuser").show();
                                          var optionCount = $('#selectcompanybyuser').find('option').length;
                                          optionCount = optionCount-1;
                                          $("#totalcompany").text('Total Company :'+ optionCount);
                                          $("#tptime").val('');
                                          $("#tptime").show();
                                          $('#ntactionnew').show();
                                          $('#ntppose').show();
                                          $('#meeting-time').show();
                                          $('#planbtn1').show();
                                          $("#taskplanningimg").hide();
                                      }
                                      });
                                  }
                             });
                             


                          }
                          if(closing_timeline_filter_select_status == 6){
                            $('#propose_or_clarify_filter_select_card').hide();
                            $('#closing_timeline_filter_select_card').show();

                            $('#closing_timeline_filter_select').on('change', function() {

                              var closing_timeline_filter_select = $(this).val();

                                if(closing_timeline_filter_select !==''){
                                  $.ajax({
                                      url:'<?=base_url();?>Menu/GetProposeOrClarifyFilter',
                                      type: "POST",
                                      data: {
                                        user_id: uid,
                                        adate  : '<?= $adate; ?>',
                                        clarify_filter   :closing_timeline_filter_select,
                                        status   :closing_timeline_filter_select_status
                                      },
                                      cache: false,
                                      success: function a(result){
                                          $("#maintaskcard").show();    
                                          $("#selectcompanybyuser").html(result);
                                          $("#selectcompanybyuser").show();
                                          var optionCount = $('#selectcompanybyuser').find('option').length;
                                          optionCount = optionCount-1;
                                          $("#totalcompany").text('Total Company :'+ optionCount);
                                          $("#tptime").val('');
                                          $("#tptime").show();
                                          $('#ntactionnew').show();
                                          $('#ntppose').show();
                                          $('#meeting-time').show();
                                          $('#planbtn1').show();
                                          $("#taskplanningimg").hide();
                                      }
                                      });
                                  }
                             });


                          }
                          });
                    }else{
                         $('#closing_timeline_filter_card').hide();
                    }
     if(val =='Future Task'){
     $('#select_cluster').hide();
                      $('#taskActionCard').hide();
                        var uid = $("#curuserid").val();
                        $(".taskselectionarea").hide();
                        $('#partnertype').hide();
                        $('#maintaskcard').hide();
                        $('#status_taskaction_card').hide();
                        $('#taskaction_card_area').hide();
                        $('#taskActionbyuserCard').hide();
                        $('#taskPurposebyuserCard').hide();

                        $.ajax({
                                url:'<?=base_url();?>Menu/GetAllOurFutureTask',
                                type: "POST",
                                data: {
                                  user_id: uid,
                                  adate  : '<?= $adate; ?>'
                                },
                                cache: false,
                                success: function a(result){
                                  
                                    $("#maintaskcard").show();    
                                    $("#selectcompanybyuser").html(result);
                                    $("#selectcompanybyuser").show();
                                    var optionCount = $('#selectcompanybyuser').find('option').length;
                                    optionCount = optionCount-1;
                                    $("#totalcompany").text('Total Company :'+ optionCount);
                                    $("#tptime").val('');
                                    $("#tptime").show();
                                    $('#ntactionnew').hide();
                                    $('#ntppose').hide();

                                    $('#meeting-time').show();
                                    $('#planbtn1').show();
                                    $("#taskplanningimg").hide();

                                    $('#planbtn1').click(function() {
                                      var newValue = '0';
                                      var newText = 'Future Task';
                                      $('#ntactionnew').append(new Option(newText, newValue));
                                      $('#ntppose').append(new Option(newText, newValue));
                                      $('#ntactionnew').val(newValue);
                                      $('#ntppose').val(newValue);
                                      val = 'Future Task';
                                      $("#selectby").val(val);
                                    
                                  });
                                }
                                });
                    }else{
                      // $('#new_category_filter_card').hide();
                    }



                    if(val =='Quater Strategy'){
                      $('#select_cluster').hide();
                      $('#taskActionCard').hide();
                        var uid = $("#curuserid").val();
                        $(".taskselectionarea").hide();
                        $('#partnertype').hide();
                        $('#maintaskcard').hide();
                        $('#status_taskaction_card').hide();
                        $('#taskaction_card_area').hide();
                        $('#taskActionbyuserCard').hide();
                        $('#taskPurposebyuserCard').hide();
                        
                         $.ajax({
                                url:'<?=base_url();?>Menu/GetAllCompanyQuaterStrategyCountsDatasLists',
                                type: "POST",
                                data: {
                                  user_id: uid,
                                  adate  : '<?= $adate; ?>'
                                },
                                cache: false,
                                success: function a(result){
                                  
                                    $("#maintaskcard").show();    
                                    $("#selectcompanybyuser").html(result);
                                    $("#selectcompanybyuser").show();
                                    var optionCount = $('#selectcompanybyuser').find('option').length;
                                    optionCount = optionCount-1;
                                    $("#totalcompany").text('Total Company :'+ optionCount);
                                    $("#tptime").val('');
                                    $("#tptime").show();
                                    $('#ntactionnew').show();
                                    $('#ntppose').show();
                                    $('#meeting-time').show();
                                    $('#planbtn1').show();
                                    $("#taskplanningimg").hide();
                                }
                                });
                       
                    }else{
                     
                    }



                    if(val =='Call On School'){
                        $('#selectby').val(val);
                        $('#projectCodeListsCard').show();
                        $('#taskActionCard').hide();
                        var uid = $("#curuserid").val();
                        $(".taskselectionarea").hide();
                        $('#partnertype').hide();
                        $('#maintaskcard').hide();
                        $('#status_taskaction_card').hide();
                        $('#taskaction_card_area').hide();
                        $('#taskActionbyuserCard').hide();
                        $('#taskPurposebyuserCard').hide();

                        var selectby = $('#selectby').val();

                         if(val =='Call On School'){
                          var action_id = 26;
                         }

                         $('#project_code_lists').on('change', function() {

                              var project_code_lists = $(this).val();
                              if(project_code_lists != ''){
                                      $.ajax({
                                      url:'<?=base_url();?>Menu/GetAllSchoolListsinProjectCode',
                                      type: "POST",
                                      data: {
                                        user_id: uid,
                                        adate  : '<?= $adate; ?>',
                                        project_code_lists  : project_code_lists
                                      },
                                      cache: false,
                                      success: function a(result){
                                                $("#maintaskcard").show();    
                                                $("#selectcompanybyuser").html(result);
                                                $("#selectcompanybyuser").show();
                                                var optionCount = $('#selectcompanybyuser').find('option').length;
                                                optionCount = optionCount-1;
                                                $("#totalcompany").text('Total Company :'+ optionCount);
                                                $("#tptime").val('');
                                                $("#tptime").show();
                                                $('#ntactionnew').show();
                                                $('#ntppose').show();
                                                $('#meeting-time').show();
                                                $('#planbtn1').show();
                                                $("#taskplanningimg").hide();
                                            }
                                      });
                              }
                         });
                    }else{
                     $('#projectCodeListsCard').hide();
                    }

                    // close New Changes 




















      
      
      });
      });
      
      $(document).ready(function() {
      $('#planningStart1').hide();
      $('#planningStart2').hide();
      $('#plantimerBox').hide();
      $('#planningStartbtn').show();
      $('#resteFilter').click(function() {
      // location.reload();
      $('input[name="optradio"]').prop('checked', false);
      $('#actionPlanned').fadeOut();
      $('#actionnotplaned_NeedYour').fadeOut();
      $('#need_your_attention').fadeOut();
      $('#selectCategory').fadeOut();
      $('#clusterLocactionFiltercard').fadeOut();
      $('#companyLocationdatacard').fadeOut();
      $('#pstAssignCard').fadeOut();
      $('#taskActionCard').fadeOut();
      $('#firstQuarter1').fadeOut();
      $('#reviewTargetDate').fadeOut();
      $('#auto_assign').fadeOut();
      $('#compulsive_task_card').fadeOut();
      $('#review_planning_card').fadeOut();
      // $('input[type="text"], input[type="number"], select').val('');
      $('#taskplanningimg').fadeIn();
      $('#maintaskcard').fadeOut();
      });
      var timerInterval;
      var startTime;
      // Function to update the timer every second
      function updateTimer() {
      clearInterval(timerInterval); // Clear any existing interval to prevent multiple intervals
      timerInterval = setInterval(function() {
          var currentTime = new Date();
          var elapsedTime = currentTime - startTime;
          var seconds = Math.floor((elapsedTime / 1000) % 60);
          var minutes = Math.floor((elapsedTime / (1000 * 60)) % 60);
          var hours = Math.floor((elapsedTime / (1000 * 60 * 60)) % 24);
          $('#timer').text(
              (hours < 10 ? "0" + hours : hours) + ":" +
              (minutes < 10 ? "0" + minutes : minutes) + ":" +
              (seconds < 10 ? "0" + seconds : seconds)
          );
      }, 1000);
      }
      // Function to show/hide the form based on timer status
      function toggleFormVisibility() {
      if (startTime) {
        $('#planningStart1').show();
          $('#planningStart2').show();
          $('#plantimerBox').show();
          $('#planningStartbtn').hide();
          var plannersession = <?= $planSessionDatacnt; ?>;
          $("#PlannerSessionStimer").text('Session : ' + plannersession);
          $('nav > ul> li > a').on('click', function(e) {
            e.preventDefault();
            alert('You must click "Stop Planning" before moving to another Page.');
        });
        window.oncontextmenu = function () {
          return false;
          }
          $(document).keydown(function (event) {
          if (event.keyCode == 123) {
          return false;
          }
          else if (event.ctrlKey && event.shiftKey && event.keyCode == 73) {
          return false;
          }
          else if (event.ctrlKey && event.keyCode == 85) {
          return false;
          }
          })
          function onKeyDown() {
          var pressedKey = String.fromCharCode(event.keyCode).toLowerCase();
          if (event.ctrlKey && (pressedKey == "c" || pressedKey == "v" || pressedKey=="j" )) {
          event.returnValue = false;
          }
          }
        $(document).on('keydown', function(e) {
            // 9 is the keycode for "Tab" key
            if (e.keyCode === 9) {
                e.preventDefault();
                alert('You must click "Stop Planning" before switching tabs.');
            }
        });
      
      } else {
         $('#planningStart1').hide();
          $('#planningStart2').hide();
          $('#plantimerBox').hide();
          $('#planningStartbtn').show();
          $('nav > ul> li > a').off('click'); 
          $(document).off('keydown');  // Allow tab switching
          // const targetUrl = "<?=base_url();?>Menu/Dashboard";  // New URL to set
         
      }
      }
      // Initialize the timer from local storage if the start button was previously clicked
      if (localStorage.getItem('timerStartTime')) {
      startTime = new Date(localStorage.getItem('timerStartTime'));
      updateTimer();
      toggleFormVisibility();
      }
      // Start button click event
      $('#start').click(function() {
      // alert(starttime);return false;
      if (!startTime) {
          startTime = new Date();
          localStorage.setItem('timerStartTime', startTime);
          updateTimer();
          toggleFormVisibility();
          alert("Planner Timer started!");
          
          var plannersession = <?= $planSessionDatacnt; ?>;
              plannersession  = plannersession+1;
          $("#PlannerSessionStimer").text('Session :' + plannersession);
          $.ajax({
              url:'<?=base_url();?>Menu/session_plan_time_start',
              type: "POST",
              data: {
                start: 'start',
              },
              cache: false,
              success: function a(result){
              }
              });
            var pageLoadTime = new Date().getTime() - 0;
            var x = setInterval(function() {
            var now = new Date().getTime();
            var timeSpent = now - pageLoadTime;
            var hours = Math.floor((timeSpent / 1000) / 3600);
            var minutes = Math.floor(((timeSpent / 1000) % 3600) / 60);
            var seconds = Math.floor((timeSpent / 1000) % 60);
            var formattedTimeSpent =
            (hours < 10 ? "0" : "") + hours + ":" +
            (minutes < 10 ? "0" : "") + minutes + ":" +
            (seconds < 10 ? "0" : "") + seconds;
            document.getElementById("demo").innerHTML = "Time Spent in Task Planning: " + formattedTimeSpent;
            document.getElementById("tptime").value=formattedTimeSpent;
            }, 1000);
      }
      });
      // Stop button click event
      $('#stop').click(function() {
      if (startTime) {
      
      var totalttasktime = parseInt($("#totalttasktime").val());
      var plannerremTime = parseInt($("#plannerremTime").val());
      if(totalttasktime >= plannerremTime){
        var timerval = $("#timer").text();
          resetTimer();
          alert("Planner Timer stopped and reset!");
          $.ajax({
              url:'<?=base_url();?>Menu/session_plan_time_close',
              type: "POST",
              data: {
                close: timerval,
              },
              cache: false,
              success: function a(result){
                location.reload();
              }
              });
              clearInterval(x);
              document.getElementById("demo").innerHTML = "Time Spent in Task Planning: " + "00:00:00";
      
      }else{
      var avremtime = plannerremTime - totalttasktime;
      if (avremtime < 60) {
      alert("You need to plan "+avremtime +" minutes task to stop the session");
      } else {
      var avre_hours = Math.floor(avremtime / 60);
      var avre_minutes = avremtime % 60;
      alert("You need to plan hours " + avre_hours + " and " + avre_minutes + " minutes task to stop the session");
      }
      
      }
      
      }
      });
      
      // Function to reset the timer
      function resetTimer() {
      clearInterval(timerInterval); // Stop the timer
      startTime = null;
      localStorage.removeItem('timerStartTime'); // Clear the start time from local storage
      $('#timer').text("00:00:00"); // Reset the timer display
      toggleFormVisibility();
      }
      $('#meetingtimerequest2').on('change', function() {
      var mtime1 = $('#meetingtimerequest1').val();
      if(mtime1 == ''){
      alert("Please Enter Start Time");
        $('#meetingtimerequest2').val('');
      }else{
      var mtime2 = $(this).val();
      $.ajax({
              url:'<?=base_url();?>Menu/GetTaskBeetweenUserTime',
              type: "POST",
              data: {
                mtime1: mtime1,
                mtime2: mtime2
              },
              cache: false,
              success: function a(result){
                $('#taskcounttable').html(result);
              }
              });
      }
      });
      $('#end-time').on('change', function() {
      <?php  $checkHalfDayLeave = checkHalfDayLeave($uid,$adate);
        $partOfleave = $checkHalfDayLeave[0]->halfday_leaveType;
        
        echo "var partOfleave = '" . addslashes($partOfleave) . "';";
        ?>
      var autotaskTime = 90;
      
      if (partOfleave == 1 || partOfleave == 2 ) {
          
          autotaskTime = 45;
      }
      
      var startTime = $('#start-time').val();
      if (startTime === '') {
          alert("Please Enter Start Time");
          $('#end-time').val('');
      } else {
          var endTime = $(this).val();
          var startTimeMinutes = convertTimeToMinutes(startTime);
          var endTimeMinutes = convertTimeToMinutes(endTime);
          // Check if the difference is more than 90 minutes
          if ((endTimeMinutes - startTimeMinutes) > autotaskTime || (endTimeMinutes - startTimeMinutes) < autotaskTime) {
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
      var uid = $("#curuserid").val();
      $.ajax({
      url:'<?=base_url();?>Menu/getUserDayStartStatus',
      type: "POST",
      data: {
      uid: uid,
      },
      cache: false,
      success: function a(result){
      if(result ==2){
        $('#compulsive_task_filter input[name="optradio"]').prop('disabled', true);
        // $('#compulsive_task_filter label').css('text-decoration', 'line-through');
        $('#actionNotPlannedNeed_filter input[name="optradio"]').prop('disabled', true);
        $('#company_name_filter input[name="optradio"]').prop('disabled', true);
        $('#company_status_filter input[name="optradio"]').prop('disabled', true);
        $('#cluster_location_fiilter input[name="optradio"]').prop('disabled', true);
        $('#company_category_filter input[name="optradio"]').prop('disabled', true);
        $('#partner_type_filter input[name="optradio"]').prop('disabled', true);
        $('#ssllimit_days_filter input[name="optradio"]').prop('disabled', true);
        $('#other_assign_filter input[name="optradio"]').prop('disabled', true);
        $('#self_assign_filter input[name="optradio"]').prop('disabled', true);
        $('#review_target_date_filter input[name="optradio"]').prop('disabled', true);
      
        $('#review_target_date_filter input[name="optradio"]').prop('disabled', false);
        $('#review_planning_filter input[name="optradio"]').prop('disabled', false);
      
        $('#need_your_attention_filter input[name="optradio"]').prop('disabled', true);
        $('#actionNotPlanned_task_filter input[name="optradio"]').prop('disabled', true);
        $('#task_action_filter option').not('[value="3"], [value="4"]').attr('disabled', true);
      }
      if(result ==3){
        $('#review_target_date_filter input[name="optradio"]').prop('disabled', false);
        $('#review_planning_filter input[name="optradio"]').prop('disabled', false);
      }
      }
      });




      $('#getAvailableTime').on('change', function() {
        $('#freeaslotDisplay').html('').slideUp("fast");
        var avltimeslct = $('#getAvailableTime').val();
        var ntactionnew_val = $('#ntactionnew').val();

        if(ntactionnew_val == ''){
          alert('* Please Select Task Action');
          return false;
        }

          $('#selectcompanybyuser').val([]);

        $.ajax({
          url:'<?=base_url();?>Menu/getTaskAvailableTime',
          type: "POST",
          data: {
            sdate: '<?= $adate; ?>',
            avltimeslct: avltimeslct,
            ntactionnew_val: ntactionnew_val
          },
          cache: false,
          success: function a(result){
            
            $('#freeaslotDisplay').html(result).slideDown("fast");
            $('#freeaslotDisplay span').css({
                            'background-color': 'rgb(10, 162, 18)',
                            'border-radius': '10px'
                        });
            }
          });

          
            $.ajax({
            url:'<?=base_url();?>Menu/getTaskAvailableTimeBySelectTask',
            type: "POST",
            data: {
              sdate: '<?= $adate; ?>',
              avltimeslct: avltimeslct,
              ntactionnew_val: ntactionnew_val
            },
            cache: false,
            success: function a(result){
              $('#meeting-time').html("");
              $('#meeting-time').html(result);
              $('#meeting-time option.text-success').css({ 'color': 'green' });
              $('#meeting-time option.text-danger').css({ 'color': 'red' });
            }
            });
      });

     $('#ntactionnew').on('change', function() {
      $('#freeaslotDisplay').html('').slideUp("fast");

      var avltimeslct     =   $('#getAvailableTime').val();
      var ntactionnew_val = $('#ntactionnew').val();

          if (avltimeslct !== null) {

                $.ajax({
                  url:'<?=base_url();?>Menu/getTaskAvailableTime',
                  type: "POST",
                  data: {
                    sdate: '<?= $adate; ?>',
                    avltimeslct: avltimeslct,
                    ntactionnew_val: ntactionnew_val
                  },
                  cache: false,
                  success: function a(result){
                    
                    $('#freeaslotDisplay').html(result).slideDown("fast");
                    $('#freeaslotDisplay span').css({
                        'background-color': 'rgb(10, 162, 18)',
                        'border-radius': '10px'
                    });
                    }
                  });


                  $.ajax({
                  url:'<?=base_url();?>Menu/getTaskAvailableTimeByTaskID',
                  type: "POST",
                  data: {
                    sdate: '<?= $adate; ?>',
                    avltimeslct: avltimeslct,
                    ntactionnew_val: ntactionnew_val
                  },
                  cache: false,
                  success: function a(result){
                    $('#meeting-time').html("");
                    $('#meeting-time').html(result);
                    $('#meeting-time option.text-success').css({ 'color': 'green' });
                    $('#meeting-time option.text-danger').css({ 'color': 'red' });
                  }
                  });
            }else{
                $.ajax({
                  url:'<?=base_url();?>Menu/getTaskAvailableTimeByTaskID',
                  type: "POST",
                  data: {
                    sdate: '<?= $adate; ?>',
                    avltimeslct: avltimeslct,
                    ntactionnew_val: ntactionnew_val
                  },
                  cache: false,
                  success: function a(result){
                    $('#meeting-time').html("");
                    $('#meeting-time').html(result);
                    $('#meeting-time option.text-success').css({ 'color': 'green' });
                    $('#meeting-time option.text-danger').css({ 'color': 'red' });
                  }
                  });
            }
      });


      
          function getAvailableCountFromTime() {
            const selectedTimeText = $('#meeting-time option:selected').text();
            const match = selectedTimeText.match(/\((\d+)\)/);
            return match ? parseInt(match[1]) : 0;
          }

          // Unselect companies when time changes
          $('#meeting-time').change(function () {
            $('#selectcompanybyuser').val([]);
          });

          // Limit selected companies
          $('#selectcompanybyuser').change(function () {
            const allowedCount = Math.min(getAvailableCountFromTime(), 3); // max 3 or time count
            const selectedOptions = $('#selectcompanybyuser option:selected').not('[value=""]');

            if (selectedOptions.length > allowedCount) {
              alert(`You can select only ${allowedCount} company(ies) based on the selected time.`);

              // Keep only first allowedCount selected
              const allowedValues = selectedOptions.slice(0, allowedCount).map(function () {
                return $(this).val();
              }).get();

              $('#selectcompanybyuser').val(allowedValues);
            }
          });

          // On form submit, final validation
          $('#bookingForm').submit(function (e) {
            const allowedCount = Math.min(getAvailableCountFromTime(), 3);
            const selectedCompanies = $('#selectcompanybyuser option:selected').not('[value=""]').length;

            if (selectedCompanies > allowedCount) {
              alert(`You can select only ${allowedCount} company(ies) before submitting.`);
              e.preventDefault();
            }
          });
        


                 var inidids = '';
                  $('#selectcompanybyuser').change(function() {
                  var inidids = '';
                  $('#selectcompanybyuser :selected').each(function(i, sel){
                      inidids += $(sel).val() + ',';
                  });
                  inidids = inidids.slice(0, -1);
                  var ct_ntactionnew = $("#ntactionnew").val();
                  var selectby = $('#selectby').val();
                  $.ajax({
                    url:'<?=base_url();?>Menu/getpurposebyinidnew',
                    type: "POST",
                    data: {
                    inid: inidids,
                    aid: ct_ntactionnew,
                    selectby:selectby
                    },
                    cache: false,
                    success: function a(result){
                      console.log("2"+result);
                      $("#ntppose").html(result);
                    }
                    });
              });


      
      var slctplanddate = '<?= $adate; ?>';
      const getcurrentDate = new Date();
      const year = getcurrentDate.getFullYear();
      const month = String(getcurrentDate.getMonth() + 1).padStart(2, '0'); // Months are zero-based
      const day = String(getcurrentDate.getDate()).padStart(2, '0');
      const formattedDate = `${year}-${month}-${day}`;


      // if(slctplanddate == formattedDate){
      // $('#meeting-time').on('change', function() {
      // const meetingTime = $(this).val();
      // const now = new Date();
      // const currentHours = now.getHours();
      // const currentMinutes = now.getMinutes();
      // const currentTotalMinutes = currentHours * 60 + currentMinutes;
      // const [meetingHours, meetingMinutes] = meetingTime.split(':').map(Number);
      // var meetingTotalMinutes = meetingHours * 60 + meetingMinutes;
      
      // if (meetingTotalMinutes < currentTotalMinutes) {
      // alert("The meeting time cannot be set for the past time.");
      // return false;
      // }else{
      // $.ajax({
      //   url:'<?=base_url();?>Menu/TodaysPlannerRequest',
      //   type: "POST",
      //   data: {
      //     sdate: '<?= $adate; ?>',
      //     getnewtime:'time'
      //   },
      //   cache: false,
      //   success: function a(result){
      //     var data = JSON.parse(result);
      //     var initTime = data.init_time;
      //     var apr_time = data.apr_time;
      //     if(meetingTime !== ''){
      //       if (meetingTime < initTime) { 
      //       var alertmessage = 'Your planner approved time is :'+apr_time +', and your planner time is 1 hour. Based on this time, you need to plan the task after'+initTime+'.';
      //           alert(alertmessage);
      //           $('#meeting-time').val('');
      //       }
      //     }
          
      //   }
      //   });
      // }
      // });
      // }




      });
    </script>
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
      $(document).ready(function () {
        $('#myForm').on('submit', function () {
            // Populate hidden fields with form input values
            $('#hiddenSelectStatus').val($('#selectstatusbyuser').val());
            $('#hiddenTaskAction').val($('#tasktaction').val());
            $('#hiddenTaskActionByUser').val($('#taskActionbyuser').val());
            $('#hiddenTaskPurposeByUser').val($('#taskPurposebyuser').val());
            $('#hiddenSelectStatusByUserNotPlanned').val($('#selectstatusbyusernotplaned').val());
            $('#hiddenTask_Action').val($('#task_action').val());
            $('#hiddenDaysFilterCardAnpDate').val($('#daysfiltercard_anp_date').val());
            $('#hiddenSelectdCategory').val($('#selectdcategory').val());
            $('#hiddenStatusFilterCardCat').val($('#statusfiltercardCat').val());
            $('#hiddenTaskActionByUserCat').val($('#taskActionbyusercat').val());
            $('#hiddenTaskPurposeByUserCatData').val($('#taskPurposebyusercatdata').val());
            $('#hiddenClusterNameLocation').val($('#clusterNameLocaction').val());
            $('#hiddenStatusFilterCluster').val($('#statusfilterCluster').val());
            $('#hiddenTaskActionByCluster').val($('#taskActionbyCluster').val());
            $('#hiddenTaskPurposeByCluster').val($('#taskPurposebyCluster').val());
            $('#hiddenCompanyLocation').val($('#companyLocation').val());
            $('#hiddenSelectActionPlane').val($('#selectactionplane').val());
            $('#hiddenDaysFilter').val($('#daysfilter').val());
            $('#hiddenPstAssignCardData').val($('#pstAssignCardData').val());
            $('#hiddenStatusTaskAction').val($('#status_taskaction').val());
            $('#hiddenTaskActionFilter').val($('#task_action_filter').val());
            $('#hiddenTaskActionByUserCardData').val($('#taskActionbyuserCardData').val());
            $('#hiddenTaskPurposeByUserCardData').val($('#taskPurposebyuserCardData').val());
            $('#hiddenPartnerTypeSelect').val($('#partnertype_select').val());
            $('#hiddenPartnerTypeCStatusData').val($('#partnertype_cstatusData').val());
            $('#hiddenPartnerTypeTaskData').val($('#partnertype_taskData').val());
            $('#hiddenPartnerTypeTaskActionData').val($('#partnertype_taskActionData').val());
            $('#hiddenPartnerTypeTaskPurposeData').val($('#partnertype_taskPurposeData').val());
            $('#hiddenSameStatusLast15DaysData').val($('#samestatuslast15daysData').val());
            $('#hiddenPartnerTypePlanButData').val($('#partnertype_planbutData').val());
            $('#hiddenDaysFilter2SameDays').val($('#daysfilter2_samedays').val());
            $('#hiddenPlanButTaskActionData').val($('#planbut_taskActionData').val());
            $('#hiddenPlanButTaskPurposeData').val($('#planbut_taskPurposeData').val());
            $('#hiddenPlanButNoInitTaskData').val($('#planbutnoinit_TaskData').val());
            $('#hiddenFirstQuarter1CStatusData').val($('#firstQuarter1cstatysData').val());
            $('#hiddenFirstQuarter1CStatusDataTask').val($('#firstQuarter1cstatysDataTask').val());
            $('#hiddenFirstQuarter1TaskActionByUser').val($('#firstQuarter1taskActionbyuser').val());
            $('#hiddenFirstQuarter1TaskPurposeByUser').val($('#firstQuarter1taskPurposebyuser').val());
            $('#hiddenReviewTargetReviewTypeData').val($('#reviewTargetreviewtypeData').val());
            $('#hiddenReviewTargetReviewSelfData').val($('#reviewTargetReviewSelfData').val());
            // Allow form to be submitted normally
            return true;
        });
      });
    </script>
    <script>
      $(document).ready(function(){
        
              $("#selectstatusbyuser").change(function(){
                var sid = $(this).val();
                if (sid == 1) {
                  $('#ntactionnew option[value="10"]').prop('disabled', false);
                  } else {
                      $('#ntactionnew option[value="10"]').prop('disabled', true);
                  }
              });
              $("#status_taskaction").change(function(){
                var sid = $(this).val();
                if (sid == 1) {
                  $('#ntactionnew option[value="10"]').prop('disabled', false);
                  } else {
                      $('#ntactionnew option[value="10"]').prop('disabled', true);
                  }
              });
              $("#statusfilterCluster").change(function(){
                var sid = $(this).val();
                if (sid == 1) {
                  $('#ntactionnew option[value="10"]').prop('disabled', false);
                  } else {
                      $('#ntactionnew option[value="10"]').prop('disabled', true);
                  }
              });
              $("#statusfiltercardCat").change(function(){
                var sid = $(this).val();
                if (sid == 1) {
                  $('#ntactionnew option[value="10"]').prop('disabled', false);
                  } else {
                      $('#ntactionnew option[value="10"]').prop('disabled', true);
                  }
              });
              $("#partnertype_cstatusData").change(function(){
                var sid = $(this).val();
                if (sid == 1) {
                  $('#ntactionnew option[value="10"]').prop('disabled', false);
                  } else {
                      $('#ntactionnew option[value="10"]').prop('disabled', true);
                  }
              });
              $("#samestatuslast15daysData").change(function(){
                var sid = $(this).val();
                if (sid == 1) {
                  $('#ntactionnew option[value="10"]').prop('disabled', false);
                  } else {
                      $('#ntactionnew option[value="10"]').prop('disabled', true);
                  }
              });
              // $('#selectcompanybyuser').on('change', function() {
              //         var selectedOptions = $(this).find('option:selected');
              //         if (selectedOptions.length > 3) {
              //             alert('You can select only 3 companies at a Time.');
              //             selectedOptions.last().prop('selected', false);
              //         }
              //     });

          
              

                  $("#printPage").click(function() {
              window.print();
          });
       
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
       
          $('#autoplan_submit').click(function(event) {
                  var endTime = $('#end_tttpft').val();
                  var time = new Date('1970-01-01T' + endTime + 'Z');
                  var limitTime = new Date('1970-01-01T19:00:00Z');
                  // Compare the times
                  if (time > limitTime) {
                      event.preventDefault();
                      // Show an alert
                      alert('The time cannot be later than 7 PM.');
                      $('#end_tttpft').css('border', '2px solid red');
                      // $('#end-time').val('');
                  }else{
                    $('#end_tttpft').css('border', '');
                  }
              });
              function updateCountdown() {
                      var now = new Date();
                      var targetTime = new Date();
                      
                      var phours  = $("#phours").val();
                      var pminutes = $("#pminutes").val();
                      var pseconds = $("#pseconds").val();
                      targetTime.setHours(phours);
                      targetTime.setMinutes(pminutes);
                      targetTime.setSeconds(pseconds);
                      
                      var timeDifference = targetTime - now;
                      
                      if (timeDifference <= 0) {
                          $('#planertime').text('Now Start Your Next Day Planning');
                          $('#yndpt').text('Now Plan Your Next Day Task : ');
                          $('#rtsyndp').hide();
                          clearInterval(countdownInterval);
                          return;
                      }
                      
                      var hours = Math.floor(timeDifference / (1000 * 60 * 60));
                      var minutes = Math.floor((timeDifference % (1000 * 60 * 60)) / (1000 * 60));
                      var seconds = Math.floor((timeDifference % (1000 * 60)) / 1000);
                      
                      $('#planertime').text(hours + "h " + minutes + "m " + seconds + "s ");
                  }
                  
                  // Update the countdown every second
                  var countdownInterval = setInterval(updateCountdown, 1000);
                  $(window).on('popstate', function() {
                      alert("You can't go back to the previous page!");
                  }); 
                  window.history.pushState(null, null, window.location.href);
        
          // Start Add Condtions to diabledd Available get time 
          var currentTime = new Date();
          var currentHour = currentTime.getHours();
          var currentMinutes = currentTime.getMinutes();
          // Loop through each option in the dropdown


          // $('#getAvailableTime option').each(function() {
          //     var timeRange = $(this).text().trim();
              
          //     // Extract the start time from the option text (e.g., "10:00 AM")
          //     var timeParts = timeRange.split('To')[0].trim().split(':');
          //     var hour = parseInt(timeParts[0]);
          //     var period = timeRange.split(' ')[1].trim();
              
          //     // Convert to 24-hour format if needed
          //     if (period === 'PM' && hour !== 12) {
          //         hour += 12;
          //     } else if (period === 'AM' && hour === 12) {
          //         hour = 0;
          //     }
          //     // Disable the option if the time has passed
          //     if (hour < currentHour || (hour === currentHour && parseInt(timeParts[1]) <= currentMinutes)) {
          //         $(this).prop('disabled', true);
          //     }
          // });



          // End Condtions to diabledd Available get time 
          $.ajax({
              url:'<?=base_url();?>Menu/GetPendingReviewForPlanUser',
              type: "POST",
              data: {
                review_date: '<?php echo $adate;?>',
              },
              cache: false,
              success: function a(result){
                $("#reviewtype").html(result);
              }
              });
      
              
              var classes = ['cat1', 'cat2', 'cat3', 'cat4', 'cat5', 'cat6', 'cat7', 'cat8'];
              var randomClass = classes[Math.floor(Math.random() * classes.length)];
              $('#backgroundchange').addClass(randomClass);
      
      
      
              // Review Changes start
              $('#reviewtype').on('change', function() {
                      var rev_plandate         = $("#rev_plandate").val();
                      var fdareview_plantimete = $("#review_plantime").val();
                      if(fdareview_plantimete == ''){
                        alert("* Please Type Review Start Time First");
                        $("#reviewtype option:contains('Select Review')").prop("selected", true);
                        return false;
                      }
                      var reviewtype = $(this).val();
                      var review_bd = $('#review_bd').val();
                      var uid = $("#curuserid").val();
                      var fdate = $("#fixdate").val();
                      var rev_plandate         = $("#rev_plandate").val();
                      var fdareview_plantimete = $("#review_plantime").val();
                      var totalrevcmpCount = 0;
                      $("#selectrevcmp_status").html('');
                      $.ajax({
                          url:'<?=base_url();?>Menu/getcmpdbybd_new_onPlanner',
                          type: "POST",
                          data: {
                          reviewtype: reviewtype,
                          uid: uid,
                          review_bd: review_bd,
                          fdate: fdate,
                          rev_plandate:rev_plandate,
                          fdareview_plantimete:fdareview_plantimete
                          },
                          cache: false,
                          success: function a(result){
                            $("#selectrevcmp_status").html(result);
                            $('#selectrevcmp_status option').each(function () {
                            const match = $(this).text().match(/\((\d+)\)/);
                            if (match) {
                              totalrevcmpCount += parseInt(match[1], 10);
                              totalrevcmpCountminutes = totalrevcmpCount * 2;
                              var revtime = new Date("1970-01-01T" + fdareview_plantimete + "Z");
                              revtime.setMinutes(revtime.getMinutes() + totalrevcmpCountminutes);
                              var newcloseTime = revtime.toISOString().substr(11, 8);
                              $("#review_plantime_close").val(newcloseTime);
                              var rev_messagecmp = '* Your review start time is: ' + fdareview_plantimete + '. The total number of companies in this review is: ' + totalrevcmpCount + '. Each company review time is 2 minutes. Based on this, ' + totalrevcmpCount + ' x 2 minutes means your review will close at: ' + newcloseTime + '.';
                              $("#rev_cnt_message").text(rev_messagecmp);
                              $("#cur_review_remarks").val(rev_messagecmp);
                                }
                            });
                            $("#totalrevcmpcnt").text('* Total company in this review : '+ totalrevcmpCount);
                          }
                          });
                    });
            
                    $('#review_bd').on('change', function() {
                      var review_bd = $(this).val();
                      var uid = $("#curuserid").val();
                      var fdate = $("#fixdate").val();
                      var rev_plandate         = $("#rev_plandate").val();
                      var fdareview_plantimete = $("#review_plantime").val();
                      var reviewtype = $('#reviewtype').val();
                      var totalrevcmpCount = 0;
                      $("#selectrevcmp_status").html('');
            
                      if(fdareview_plantimete == ''){
                        alert("* Please Type Review Start Time First");
                        return false;
                      }
            
                      $.ajax({
                          url:'<?=base_url();?>Menu/getcmpdbybd_new_onPlanner',
                          type: "POST",
                          data: {
                          reviewtype: reviewtype,
                          uid: uid,
                          review_bd: review_bd,
                          fdate: fdate,
                          rev_plandate:rev_plandate,
                          fdareview_plantimete:fdareview_plantimete
                          },
                          cache: false,
                          success: function a(result){
                            $("#selectrevcmp_status").html(result);
                            $('#selectrevcmp_status option').each(function () {
                            const match = $(this).text().match(/\((\d+)\)/);
                            if (match) {
                              totalrevcmpCount += parseInt(match[1], 10);
                              totalrevcmpCountminutes = totalrevcmpCount * 2;
                              var revtime = new Date("1970-01-01T" + fdareview_plantimete + "Z");
                              revtime.setMinutes(revtime.getMinutes() + totalrevcmpCountminutes);
                              var newcloseTime = revtime.toISOString().substr(11, 8);
                              $("#review_plantime_close").val(newcloseTime);
                              var rev_messagecmp = '* Your review start time is: ' + fdareview_plantimete + '. The total number of companies in this review is: ' + totalrevcmpCount + '. Each company review time is 2 minutes. Based on this, ' + totalrevcmpCount + ' x 2 minutes means your review will close at: ' + newcloseTime + '.';
                              $("#rev_cnt_message").text(rev_messagecmp);
                              $("#cur_review_remarks").val(rev_messagecmp);
                                }
                            });
                            $("#totalrevcmpcnt").text('* Total company in this review : ' + totalrevcmpCount);
                          }
                          });
                    });
            
            $("#create-plan-form").on("submit", function() {
                var curuserids = $("#curuserid").val();
                var reviewtype = $('#reviewtype').val();
                var rev_meetlink = $('#rev_meetlink').val();
                var review_bd = $('#review_bd').val();
                if (reviewtype == 'Select Review') {
                    alert("* Please Select Review Type First !");
                    return false;
                } 
                
                else if (rev_meetlink == '' && curuserids == review_bd) {
                    return true;
                } else if (rev_meetlink == '' && curuserids !== review_bd) {
                    alert("* Please Add Google Meet Link.");
                    return false;
                } else if (rev_meetlink !== '' && curuserids !== review_bd) {
                    var rev_meetlinkregex = /^https:\/\/meet\.google\.com\/[a-z]{3}-[a-z]{4}-[a-z]{3}$/;
                    if (rev_meetlinkregex.test(rev_meetlink)) {
                        return true;
                    } else {
                        alert("* Please Valid Google Meet Link.");
                        return false;
                    }
                } else {
                    return true;
                }
                
            });

      
             var userid = "<?php echo $uid; ?>";
              var adate = "<?php echo $adate;?>";
              // alert(userid+"=="+date);return false;
      
              $.ajax({
              url: '<?=base_url();?>Menu/checkUsersTasksForTheDay', // The URL to send the request to
              type: 'POST', // The HTTP method (GET, POST, etc.)
              data: {
                      userid: userid,
                      adate:adate
                      },
              success: function(response) {
               // alert(response);return false;
                  // Handle the response here
                  console.log("Email send to your reporting manager");
                  // Perform further actions based on the response
              },
          });
      
      
      
      
      });
    </script>
    <!-- /.row (main row) -->
    </div>
    <!-- /.container-fluid -->
    </section>
    </div></div></div>
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
      $(document).ready(function() {
             var userid = "<?php echo $uid; ?>";
             var adate = "<?php echo $adate;?>";
             // alert(userid+"=="+date);return false;
      // console.log(userid+"=="+date);return false;
         // Your AJAX call
         $.ajax({
             url: '<?=base_url();?>Menu/checkUsersTasksForTheDay', // The URL to send the request to
             type: 'POST', // The HTTP method (GET, POST, etc.)
             data: {
                     userid: userid,
                     adate:adate
                     },
             success: function(response) {
              console.log(response);
                 // Handle the response here
                 console.log("Email send to your reporting manager");
                 // Perform further actions based on the response
             },
         });
      });
      
      $("#example1").DataTable({
      "responsive": false, "lengthChange": false, "autoWidth": false,'pageLength' : 3,
      "buttons": ["excel", "pdf"]
      }).buttons().container().appendTo('#example1_wrapper .col-md-6:eq(0)');
      $("#example10").DataTable({
      "responsive": false, "lengthChange": false, "autoWidth": false,'pageLength' : 10,
      "buttons": ["excel", "pdf"]
      }).buttons().container().appendTo('#example10_wrapper .col-md-6:eq(0)');
      $("#example4").DataTable({
      "responsive": false, "lengthChange": false, "autoWidth": false,'pageLength' : 5,
      "buttons": ["excel", "pdf"]
      }).buttons().container().appendTo('#example4_wrapper .col-md-6:eq(0)');
      $("#example45").DataTable({
      "responsive": false, "lengthChange": false, "autoWidth": false,'pageLength' : 5,
      "buttons": ["excel", "pdf"]
      }).buttons().container().appendTo('#example45_wrapper .col-md-6:eq(0)');

      $("#example51").DataTable({
      "responsive": false, "lengthChange": false, "autoWidth": false,'pageLength' : 5,
      "buttons": ["excel", "pdf"]
      }).buttons().container().appendTo('#example45_wrapper .col-md-6:eq(0)');

      $("#example46").DataTable({
      "responsive": false, "lengthChange": false, "autoWidth": false,'pageLength' : 5,
      "buttons": ["excel", "pdf"]
      }).buttons().container().appendTo('#example46_wrapper .col-md-6:eq(0)');

      $("#example47").DataTable({
      "responsive": false, "lengthChange": false, "autoWidth": false,'pageLength' : 5,
      "buttons": ["excel", "pdf"]
      }).buttons().container().appendTo('#example47_wrapper .col-md-6:eq(0)');
         
    </script>
  </body>
</html>
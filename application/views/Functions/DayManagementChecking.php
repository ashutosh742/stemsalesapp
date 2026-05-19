<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Live Day Management Checking | STEM APP | WebAPP</title>
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
      tr,td{
      font-weight: bold;
      }
      .card-header{
      background: floralwhite;
      }
    </style>
  </head>
  <body class="hold-transition sidebar-mini layout-fixed">
    <div class="wrapper">
      <!-- Preloader -->
      <!-- Navbar -->
      <?php $this->load->view($dep_name.'/nav.php');?>
      <style>
        .gradient-text,.gradient-text1{color:transparent;animation:5s infinite gradientAnimation}.gradient-text{background:linear-gradient(90deg,#ff8a00,#e52e71,#1e90ff);background-size:300% 300%;-webkit-background-clip:text;background-clip:text}.gradient-text1{background:linear-gradient(90deg,#e113aa,#1500ff,#1e90ff);background-size:300% 300%;-webkit-background-clip:text;background-clip:text}@keyframes gradientAnimation{0%,100%{background-position:0 50%}50%{background-position:100% 50%}}.card-height{height:600px}.card-height1{height:300px}.maparea{max-width:100%;border-radius:20px;padding:8px;background:linear-gradient(135deg,#e3f2fd,#fce4ec);border:3px solid transparent;background-clip:padding-box;position:relative;overflow:hidden;transition:transform .3s,box-shadow .3s;align-items:center;justify-content:center;display:flex;height:100%}.maparea:hover{box-shadow:0 12px 35px rgba(0,0,0,.25)}.custom-btn{width:130px;height:40px;color:#fff;border-radius:5px;padding:10px 25px;font-family:Lato,sans-serif;font-weight:500;background:0 0;cursor:pointer;transition:.3s;position:relative;display:inline-block;box-shadow:inset 2px 2px 2px 0 rgba(255,255,255,.5),7px 7px 20px 0 rgba(0,0,0,.1),4px 4px 5px 0 rgba(0,0,0,.1);outline:0}.btn-11{border:none;background:#212ffb;background:linear-gradient(0deg,#3e21fb 0,#4c5cea 100%);color:#fff;overflow:hidden}.btn-11:hover{text-decoration:none;color:#fff;opacity:.7}.btn-11:before{position:absolute;content:'';display:inline-block;top:-180px;left:0;width:30px;height:100%;background-color:#fff;animation:3s ease-in-out infinite shiny-btn1}.btn-11:active{box-shadow:4px 4px 6px 0 rgba(255,255,255,.3),-4px -4px 6px 0 rgba(116,125,136,.2),inset -4px -4px 6px 0 rgba(255,255,255,.2),inset 4px 4px 6px 0 rgba(0,0,0,.2)}@keyframes shiny-btn1{0%{transform:scale(0) rotate(45deg);opacity:0}80%{transform:scale(0) rotate(45deg);opacity:.5}81%{transform:scale(4) rotate(45deg);opacity:1}100%{transform:scale(50) rotate(45deg);opacity:0}}
        .cardline{border-top: 1px solid rgb(4 4 4 / 84%);}
        /* .card .meetingslist-card:hover {
        transition: 0.4s ease-in-out;
        box-shadow: rgba(50, 50, 93, 0.25) 0px 30px 60px -12px inset,
        rgba(0, 0, 0, 0.3) 0px 18px 36px -18px inset !important;
        } */
        .live-badge{display:inline-flex;align-items:center;background-color:red;color:#fff;font-weight:700;padding:4px 10px;border-radius:20px;font-size:14px;font-family:Arial,sans-serif;animation:1s infinite pulse;box-shadow:0 0 5px red}.live-dot{width:8px;height:8px;background-color:#fff;border-radius:50%;margin-right:6px;animation:1s infinite blink}@keyframes blink{0%,100%,50%{opacity:1}25%,75%{opacity:0}}@keyframes pulse{0%,100%{box-shadow:0 0 5px red}50%{box-shadow:0 0 20px red}}
        p.mb-1 {
        font-size: large;
        }  
        .custom-btn {
        /* width: 130px;
        height: 40px; */
        color: #fff;
        border-radius: 5px;
        /* padding: 10px 25px; */
        font-family: Lato, sans-serif;
        font-weight: 500;
        background: 0 0;
        cursor: pointer;
        transition: .3s;
        position: relative;
        display: inline-block;
        box-shadow: rgba(9, 30, 66, 0.25) 0px 4px 8px -2px, rgba(9, 30, 66, 0.08) 0px 0px 0px 1px;
        outline: 0;
        }
        .btn-11 {
        border: none;
        background: #212ffb;
        background: linear-gradient(0deg, #3e21fb 0, #4c5cea 100%);
        color: #fff;
        overflow: hidden;
        width: fit-content;
        }
        .btn-11:hover {
        text-decoration: none;
        color: #fff;
        opacity: .7;
        }
        .btn-11:before {
        position: absolute;
        content: '';
        display: inline-block;
        top: -180px;
        left: 0;
        width: 30px;
        height: 100%;
        background-color: #fff;
        animation: 3s ease-in-out infinite shiny-btn1;
        }
        .btn-11:active {
        box-shadow: 4px 4px 6px 0 rgba(255,255,255,.3), -4px -4px 6px 0 rgba(116,125,136,.2), inset -4px -4px 6px 0 rgba(255,255,255,.2), inset 4px 4px 6px 0 rgba(0,0,0,.2);
        }
        @keyframes shiny-btn1 {
        0% { transform: scale(0) rotate(45deg); opacity: 0; }
        80% { transform: scale(0) rotate(45deg); opacity: .5; }
        81% { transform: scale(4) rotate(45deg); opacity: 1; }
        100% { transform: scale(50) rotate(45deg); opacity: 0; }
        }
        .btn-11.partnearray{
        background: navy!important;
        }
        .btn-11.categoryarray{
        background: #ff007f!important;
        }
        tr,td{
          font-weight: 400;
        }
         .star-rating {
            direction: rtl;
            display: inline-block;
            padding: 10px;
        }
        .rating-question {
            font-size: 18px;
            margin-bottom: 10px;
            display: block;
        }
       
      </style>
      <!-- /.navbar -->
      <!-- Content Wrapper. Contains page content -->
      <div class="content-wrapper">
        <section class="content">
          <div class="container-fluid">
            <br>
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
                <div class="card">
                  <div class="card-header" style="background: linear-gradient(to right, #ede7f6, #ffffff); border-radius: 12px; box-shadow: 0 4px 8px rgba(0,0,0,0.1); padding: 20px;">
                    <h3 class="text-center m-3">Track 🌅 Day Management </h3>
                    <center><mark>Monitor daily task progress 📝, start ⏰ and end 🏁 times, punctuality ⏱️, and overall team productivity 🚀 for effective day-to-day operations 📆.</mark></center>
                    <div class="text-center">
                        <mark><?=$sdate?></mark>
                    </div>
                  </div>
                  <hr>
                  <div class="row mb-2">
                    <hr>
                    <div class="text-right-data text-center" style="background: linear-gradient(to right, #b2d6ff, #ffffff); border-radius: 12px; box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1); padding: 20px; width: 60%;">
                      <?php 
                        $clusterPstDatas  = $this->Menu_model->GetAdminActiveTeam($user['user_id']);
                        $taskActions      = $this->Menu_model->get_action();
                        $userRoles        = $this->Menu_model->GetActiveDepartmentList($uid);
                        ?>
                      <div class="col text-center formcenterData">
                        <div>
                          <hr>
                          <form action="<?=base_url()?>Reports/DayManagementChecking" method="post" class="mt-3">
                            <div class="row mb-4">
                              <div class="col">
                                <label for="selectedDate">Start Date</label>
                                <input type="date" value="<?=$sdate;?>" id="selectedDate" max="<?=date('Y-m-d')?>" name="sdate" style="width: 200px;" class="form-control">
                              </div>
                              <!-- <div class="col">
                                <label for="selectedDate">End Date</label>
                                <input type="date" value="<?=$edate;?>" id="selectedDate" name="edate" style="width: 200px;" max="<?=date('Y-m-d')?>" class="form-control">
                                </div> -->
                              <div class="col">
                                <label for="selectedDate">Select Admin</label>
                                <select id="admin_id_filter" name="admin_id_filter" class="form-control">
                                  <?php if($user['type_id'] == 2){ ?> 
                                  <option value="all">All</option>
                                  <?php } ?>
                                  <?php foreach ($clusterPstDatas as $clusterPstData) { ?>
                                  <option value="<?= $clusterPstData->user_id; ?>" <?= ($clusterPstData->user_id == $admin_id_filter) ? 'selected' : ''; ?>>
                                    <?= $clusterPstData->name; ?>
                                  </option>
                                  <?php } ?>
                                </select>
                              </div>
                              <div class="col">
                                <?php $getTeamsDatas = $this->Menu_model->GetClusterAndPSTActiveTeam($admin_id_filter); ?>
                                <label for="selectedDate">Select RM / Specific User</label>
                                <select id="rm_filter" name="rm_filter" class="form-control">
                                  <?php if($user['type_id'] !== 3){ ?> 
                                  <option value="all" <?= ($rm_filter == 'all') ? 'selected' : ''; ?>>All</option>
                                  <?php } ?>
                                  <?php foreach ($getTeamsDatas as $getTeamsData) { ?>
                                  <option value="<?= $getTeamsData->user_id; ?>" <?= ($getTeamsData->user_id == $rm_filter) ? 'selected' : ''; ?>>
                                    <?= $getTeamsData->name; ?> (Team Wise)
                                  </option>
                                  <?php }?>
                                  <?php $getcTeamsDatas = $this->Menu_model->GetAllUserDataByAdminID($user['user_id']); ?>
                                  <?php foreach ($getcTeamsDatas as $getcTeamsData) { ?>
                                  <option value="<?= $getcTeamsData->user_id; ?>" <?= ($getTeamsData->user_id == $rm_filter) ? 'selected' : ''; ?> >
                                    <?= $getcTeamsData->name; ?> (user Wise)
                                  </option>
                                  <?php }?>
                                </select>
                                <input type="hidden" id="rm_input" name="rm_input" class="form-control" placeholder="Enter value" />
                              </div>
                              <div class="col text-center">
                                <div class="form-group">
                                  <button type="submit" class="custom-btn btn-11 partnearray p-2" style="margin-top: 33px; width: 100px;">Filter</button>
                                </div>
                              </div>
                            </div>
                          </form>
                          <hr>
                        </div>
                      </div>
                    </div>
                    <hr>
                  </div>
                  <div class="row mt-3 mb-2">
                    <div class="col-md-12 text-center">
                      <button class="custom-btn btn-11 partnearray p-2" id="downloadPdf">Download PDF</button>
                    </div>
                  </div>
                  <?php 

                    $plannerReportDatas     = $liveDayManagement['plannerReportDatas'];
                    $totalTasksUserByDatas  = $liveDayManagement['totalTasksUserByDatas'];

                    ?>
                  <?php
                    // Define unique gradients
                    $gradients = [
                        'linear-gradient(to right, #ffecd2, #fcb69f)',
                        'linear-gradient(to right, #c2e9fb, #a1c4fd)',
                        'linear-gradient(to right, #fbc2eb, #a6c1ee)',
                        'linear-gradient(to right, #fdfbfb, #ebedee)',
                    ];
                    $gradientIndex = 0;
                    ?>

                    <hr>

                    <div class="text-center" style="background: linear-gradient(to right, #ede7f6, #ffffff); border-radius: 12px; box-shadow: 0 4px 8px rgba(0,0,0,0.1); padding: 20px;">
        <h5><i>📊 User Activity and Attendance Report</i></h5>
        <small>
            📅 This Data represents a comprehensive report on user activity and attendance metrics. 📅 It includes details such as the report date, total number of users, present and absent users, and their work locations categorized into office 🏢, field 🌾, and field office 🏡. Additionally, it tracks planning activities, distinguishing between same-day and previous-day planning, as well as instances where planning was not set. 📝 The report also captures scenarios where planning started on a previous day but tasks were created on the same day, providing insights into user productivity and planning efficiency. 📈
        </small>
    </div>
    <br>
    <?php
    $i = 1;
    foreach ($plannerReportDatas as $row) {
        $r = rand(150, 255);
        $g = rand(150, 255);
        $b = rand(150, 255);
        $backgroundColor = "rgba($r, $g, $b, 0.2)";
        $hue = rand(0, 360);
        $saturation = rand(60, 100);
        $lightness = rand(30, 45);
        $backgroundColorNew = "hsl($hue, $saturation%, $lightness%)";
    ?>
    <div class="row">
     
        <div class="col-md-3">
        <div class="p-2 text-center mb-1" style="background-color: rgba(183, 184, 255, 0.7);">
            <h5 class="p-2">👥 Total Active User</h5>
            <p><a style="color: inherit;" href="<?= base_url() ?>/Reports/PlannerReportDataBYDate/<?= $uid ?>/<?= $row->report_date ?>/<?= $row->report_date ?>/total_user" target="_blank"><?= htmlspecialchars($row->total_user); ?></a></p>
        </div>
        </div>
           <div class="col-md-3">
        <div class="p-2 text-center mb-1" style="background-color: rgba(255, 255, 183, 0.7);">
            <h5 class="p-2">👤 Total Present User</h5>
            <p><a style="color: inherit;" href="<?= base_url() ?>/Reports/PlannerReportDataBYDate/<?= $uid ?>/<?= $row->report_date ?>/<?= $row->report_date ?>/total_present_user" target="_blank"><?= htmlspecialchars($row->total_present_user); ?></a></p>
        </div>
        </div>
            <div class="col-md-3">
        <div class="p-2 text-center mb-1" style="background-color: rgba(255, 183, 255, 0.7);">
            <h5 class="p-2">👤 Total Absent User</h5>
            <p><a style="color: inherit;" href="<?= base_url() ?>/Reports/PlannerReportDataBYDate/<?= $uid ?>/<?= $row->report_date ?>/<?= $row->report_date ?>/total_absent_user" target="_blank"><?= htmlspecialchars($row->total_absent_user); ?></a></p>
        </div>
        </div>
        <div class="col-md-3">
        <div class="p-2 text-center mb-1" style="background-color: rgba(183, 255, 255, 0.7);">
            <h5 class="p-2">🏢 Total Work From Office</h5>
            <p><a style="color: inherit;" href="<?= base_url() ?>/Reports/PlannerReportDataBYDate/<?= $uid ?>/<?= $row->report_date ?>/<?= $row->report_date ?>/total_work_from_office" target="_blank"><?= htmlspecialchars($row->total_work_from_office); ?></a></p>
        </div>
        </div>
         <div class="col-md-3">
        <div class="p-2 text-center mb-1" style="background-color: rgba(220, 183, 255, 0.7);">
            <h5 class="p-2">🌾 Total Work From Field</h5>
            <p><a style="color: inherit;" href="<?= base_url() ?>/Reports/PlannerReportDataBYDate/<?= $uid ?>/<?= $row->report_date ?>/<?= $row->report_date ?>/total_work_from_field" target="_blank"><?= htmlspecialchars($row->total_work_from_field); ?></a></p>
        </div>
        </div>
         <div class="col-md-3">
        <div class="p-2 text-center mb-1" style="background-color: rgba(255, 220, 183, 0.7);">
            <h5 class="p-2">🏢🌾 Total Work From Field + Office</h5>
            <p><a style="color: inherit;" href="<?= base_url() ?>/Reports/PlannerReportDataBYDate/<?= $uid ?>/<?= $row->report_date ?>/<?= $row->report_date ?>/total_work_from_field_office" target="_blank"><?= htmlspecialchars($row->total_work_from_field_office); ?></a></p>
        </div>
        </div>
        <div class="col-md-3">
        <div class="p-2 text-center mb-1" style="background-color: rgba(183, 255, 220, 0.7);">
            <h5 class="p-2">📝 Total Planner Set</h5>
            <p><a style="color: inherit;" href="<?= base_url() ?>/Reports/PlannerReportDataBYDate/<?= $uid ?>/<?= $row->report_date ?>/<?= $row->report_date ?>/total_planning_set" target="_blank"><?= htmlspecialchars($row->total_planning_set); ?></a></p>
        </div>
        </div>
         <div class="col-md-3">
        <div class="p-2 text-center mb-1" style="background-color: rgba(220, 255, 183, 0.7);">
            <h5 class="p-2">🔄 Same Day Planning</h5>
            <p><a style="color: inherit;" href="<?= base_url() ?>/Reports/PlannerReportDataBYDate/<?= $uid ?>/<?= $row->report_date ?>/<?= $row->report_date ?>/same_day_planning" target="_blank"><?= htmlspecialchars($row->same_day_planning); ?></a></p>
        </div>
        </div>
        <div class="col-md-3">
        <div class="p-2 text-center mb-1" style="background-color: rgba(183, 220, 255, 0.7);">
            <h5 class="p-2">🔙 Previous Day Planning</h5>
            <p><a style="color: inherit;" href="<?= base_url() ?>/Reports/PlannerReportDataBYDate/<?= $uid ?>/<?= $row->report_date ?>/<?= $row->report_date ?>/previous_day_planning" target="_blank"><?= htmlspecialchars($row->previous_day_planning); ?></a></p>
        </div>
        </div>
        <div class="col-md-3">
        <div class="p-2 text-center mb-1" style="background-color: rgba(255, 183, 220, 0.7);">
            <h5 class="p-2">❌ Planner Not Set</h5>
            <p><a style="color: inherit;" href="<?= base_url() ?>/Reports/PlannerReportDataBYDate/<?= $uid ?>/<?= $row->report_date ?>/<?= $row->report_date ?>/planner_not_set" target="_blank"><?= htmlspecialchars($row->planner_not_set); ?></a></p>
        </div>
        </div>
        <div class="col-md-6">
        <div class="p-2 text-center mb-1" style="background-color: rgba(220, 183, 255, 0.7);">
            <h5 class="p-2">🔄 Started planning previous day but still created request on same day</h5>
            <p><a style="color: inherit;" href="<?= base_url() ?>/Reports/PlannerReportDataBYDate/<?= $uid ?>/<?= $row->report_date ?>/<?= $row->report_date ?>/spdplanning_then_create_same_day_request" target="_blank"><?= htmlspecialchars($row->spdplanning_then_create_same_day_request); ?></a></p>
        </div>
        </div>
    </div>
    <?php $i++; } ?>


<?php 

// dd($totalTasksUserByDatas);


$early = 0;
$on_time = 0;
$late = 0;

foreach ($totalTasksUserByDatas as $usertime) {
    $start_time = date('H:i', strtotime($usertime->user_start));

    if ($start_time < '10:00') {
        $early++;
    } elseif ($start_time >= '10:00' && $start_time <= '10:15') {
        $on_time++;
    } else {
        $late++;
    }
}

?>


                  <hr>
                  <div class="text-center" style="background: linear-gradient(to right, #ede7f6, #ffffff); border-radius: 12px; box-shadow: 0 4px 8px rgba(0,0,0,0.1); padding: 20px;">
                    <h5>⏱️ Day Management Punctuality Summary</h5>
                    <small>
                   This summary highlights user punctuality for starting and closing the day 🕒. It tracks delays ❌, timely actions ✅, and patterns in daily performance 📈. By analyzing consistency and discipline 📅, managers can promote time awareness 🧠 and boost overall efficiency ⚙️. Useful insights help in identifying improvement areas 📌 and ensuring a more reliable, time-bound workflow across the team 👥.
                    </small>
                  </div>
                  <hr>
                  <div class="row">
                    <div class="col-md-4">
                      <div class="card text-center shadow text-dark" style="background: linear-gradient(to right, #ffecd2,rgb(249, 252, 159)); border-radius: 12px; box-shadow: 0 4px 12px rgba(0,0,0,0.1); padding: 20px;">
                        <h5 class="card-title"><b>🌅 Early Start</b></h5>
                        <small>🌅 Tracks proactive users who start their day ahead of time..</small>
                        <hr>
                        <h3 class="card-text">🟢 <?=$early?></h3>
                      </div>
                    </div>
                    <div class="col-md-4">
                      <div class="card text-center shadow text-dark" style="background: linear-gradient(to right, #ffecd2,rgb(169, 247, 157)); border-radius: 12px; box-shadow: 0 4px 12px rgba(0,0,0,0.1); padding: 20px;">
                        <h5 class="card-title"><b>✅ On Time Start</b></h5>
                        <small>✅ Tracks users who begin their day exactly on scheduled time..</small>
                        <hr>
                        <h3 class="card-text">🟡 <?=$on_time?></h3>
                      </div>
                    </div>
                    <div class="col-md-4">
                      <div class="card text-center shadow text-dark" style="background: linear-gradient(to right, #ffecd2,rgb(251, 132, 132)); border-radius: 12px; box-shadow: 0 4px 12px rgba(0,0,0,0.1); padding: 20px;">
                        <h5 class="card-title"><b>🔴 Delayed Start</b></h5>
                        <small>🔴 Tracks users who start their day late beyond scheduled time.</small> 
                        <hr>
                        <h3 class="card-text">🔴 <?=$late?></h3>
                      </div>
                    </div>
                  </div>
                  <hr>
                  <div class="text-center" style="background: linear-gradient(to right, #ede7f6, #ffffff); border-radius: 12px; box-shadow: 0 4px 8px rgba(0,0,0,0.1); padding: 20px;">
                    <h5>📅 Day Management Overview</h5>
                    <small>
                    This section offers a smart overview of daily task planning 📝, start ⏰ and close 🏁 activities. It helps ensure timely execution ✅, track pending actions ⏳, and improve accountability 👤. With real-time updates 🔄 and logs 📊, teams can stay organized, avoid delays 🚫, and boost productivity 🚀. A must-have for efficient day-to-day operations and performance monitoring!
                    </small>
                  </div>
                  <br>
                  <?php
                    $gradientColors = [
                        'linear-gradient(to right,rgb(255, 223, 246),rgb(255, 255, 255))',
                        'linear-gradient(to right,rgb(233, 255, 182),rgb(249, 255, 250))',
                        'linear-gradient(to right,rgb(203, 255, 222),rgb(248, 253, 255))',
                        'linear-gradient(to right,rgb(255, 229, 198),rgb(255, 255, 255))',
                        'linear-gradient(to right,rgb(238, 255, 191),rgb(249, 252, 255))',
                        'linear-gradient(to right,rgb(203, 255, 191),rgb(249, 252, 255))',
                        'linear-gradient(to right,rgb(255, 203, 188),rgb(249, 252, 255))',
                        'linear-gradient(to right,rgb(255, 191, 191),rgb(249, 252, 255))',
                        'linear-gradient(to right,rgb(255, 251, 191),rgb(249, 252, 255))',
                        'linear-gradient(to right,rgb(191, 236, 255),rgb(249, 252, 255))',
                        'linear-gradient(to right,rgb(251, 193, 221),rgb(249, 252, 255))',
                        'linear-gradient(to right,rgb(221, 214, 240),rgb(249, 252, 255))',
                    ];
                    
                    $index = 0;
                    //  dd($userWholeDayActivity);
                    $userWholeDays = $userWholeDayActivity['userWholeDays'];
                    // echo "<pre>";
                    // print_r($userWholeDays);
                    ?>
                  <div class="row">

                      <?php 
                      $j = 1; 
                      foreach($userWholeDays as $dayActivity):
                        $targetuid = $dayActivity->user_id;
                       ?>
                    <div class="col-md-12 mb-4">
                      <div class="card meetingslist-card <?=$alertClass;?>" style="background: <?= $gradientColors[$index++ % count($gradientColors)] ?>; border-radius: 14px; box-shadow: 0 4px 12px rgba(0,0,0,0.1); padding: 20px;">
                        <div class="text-left">
                          <center>
                            <h3 class="mb-1 badge badge-pill badge-dark" style="font-weight: 600; font-size: 1.1rem;"><?= htmlspecialchars($j) ?></h3>
                            <hr style="width: 20%;" class="cardline <?=$hrAClass?>">

                            <h3 class="mb-1" style="font-weight: 600;">👤 <?=$dayActivity->username?></h3>
                            <hr style="width: 60%;" class="cardline <?=$hrAClass?>">
                            <h5 class="d-block mb-2"> 📌 <?=$dayActivity->user_roles?> </h5>
                          </center>
                            <hr class="cardline <?=$hrAClass?>">
                            
                          <style>
                         .star-rating input[type="radio"] {
                                display: none;
                            }

                            .star-rating label {
                                color: #ccc;
                                font-size: 20px;
                                cursor: pointer;
                            }

                            .star-rating input[type="radio"]:checked ~ label,
                            .star-rating label:hover,
                            .star-rating label:hover ~ label {
                                color: #ffc107;
                            }

                            .star-rating input[type="radio"]:checked + label ~ label {
                                color: #ffc107;
                            }

                          </style>

<div class="table-responsive text-nowrap">
<table id="example<?=$j;?>" class="table table-striped">
    <thead class="thead-dark">
        <tr>
            <th scope="col">📝 Field</th>
            <th scope="col">📋 Data</th>
            <th scope="col">📋 Questions</th>
            <th scope="col">🌟 Star</th>
        </tr>
    </thead>
    <tr>
        <td><strong>⏰ Start Time:</strong></td>
        <td><?= $dayActivity->user_start ?></td>
        <td>
            <p class="mb-2 fw-semibold">Start Time Is Good or Not?</p>
        </td> 
        <td>
            <?php 
            $checkStar1 = $this->Menu_model->GetGivedStarRatingsCount($targetuid,$sdate,"Start Time Is Good or Not?");
            if($checkStar1 == 0){
            ?>
            <div class="star-rating" data-question="Start Time Is Good or Not?" data-task-id="<?= $targetuid ?>" data-check-date="<?= $sdate ?>" data-task-rid="<?= $j ?>_1">
                <?php for ($i = 5; $i >= 1; $i--): ?>
                    <input type="radio" id="star<?= $i ?>-<?= $j ?>-1" name="rating_<?= $targetuid ?>_<?= $j ?>_1" value="<?= $i ?>" onchange="disableRating(this)">
                    <label for="star<?= $i ?>-<?= $j ?>-1" title="<?= $i ?> star<?= $i > 1 ? 's' : '' ?>">★</label>
                <?php endfor; ?>
            </div>
            <?php }else{ 
                echo "<span class='text-warning'>";
               for($star = 1; $star <= $checkStar1; $star++){
                    echo "★";
                }
                 echo "</span>";
             } ?>
        </td>
    </tr>
    <tr>
        <td><strong>🧑‍💼 Start Selfie:</strong></td>
        <td><span class="view-data custom-btn btn-11 partnearray p-1" data-rid="row_id_<?=$j?>" data-content="<?= base_url() . $dayActivity->usimg ?>" data-title="usimg" style="height: 30px;">View Selfie</span></td>
        <td>
            <p class="mb-2 fw-semibold"> Start Selfie Is Right or Not ?</p>
        </td>
        <td>
            <?php 
            $checkStar1 = $this->Menu_model->GetGivedStarRatingsCount($targetuid,$sdate,"Start Selfie Is Right or Not ?");
            if($checkStar1 == 0){
            ?>
            <div class="star-rating" data-question="Start Selfie Is Right or Not ?" data-task-id="<?= $targetuid ?>" data-check-date="<?= $sdate ?>" data-task-rid="<?= $j ?>_2">
                <?php for ($i = 5; $i >= 1; $i--): ?>
                    <input type="radio" id="star<?= $i ?>-<?= $j ?>-2" name="rating_<?= $targetuid ?>_<?= $j ?>_2" value="<?= $i ?>" onchange="disableRating(this)">
                    <label for="star<?= $i ?>-<?= $j ?>-2" title="<?= $i ?> star<?= $i > 1 ? 's' : '' ?>">★</label>
                <?php endfor; ?>
            </div>
            <?php }else{ 
                echo "<span class='text-warning'>";
               for($star = 1; $star <= $checkStar1; $star++){
                    echo "★";
                }
                 echo "</span>";
             } ?>
        </td>
    </tr>
    <?php
    $slatitude = $dayActivity->slatitude;
    $slongitude = $dayActivity->slongitude;
    $startMap = "$slatitude,$slongitude";
    ?>
    <tr>
        <td><strong>📍 Start Location:</strong></td>
        <td>
            <span class="view-data custom-btn btn-11 partnearray p-1" data-rid="row_id_<?=$j?>" data-content='<?= $startMap ?>' data-title="startLocation" style="height: 30px;">Start Location</span>
        </td>
        <td>
            <p class="mb-2 fw-semibold">Start Location Is Right Or Not?</p>
        </td>
        <td>
              <?php 
            $checkStar1 = $this->Menu_model->GetGivedStarRatingsCount($targetuid,$sdate,"Start Location Is Right Or Not?");
            if($checkStar1 == 0){
            ?>
            <div class="star-rating" data-question="Start Location Is Right Or Not?" data-task-id="<?= $targetuid ?>" data-check-date="<?= $sdate ?>" data-task-rid="<?= $j ?>_3">
                <?php for ($i = 5; $i >= 1; $i--): ?>
                    <input type="radio" id="star<?= $i ?>-<?= $j ?>-3" name="rating_<?= $targetuid ?>_<?= $j ?>_3" value="<?= $i ?>" onchange="disableRating(this)">
                    <label for="star<?= $i ?>-<?= $j ?>-3" title="<?= $i ?> star<?= $i > 1 ? 's' : '' ?>">★</label>
                <?php endfor; ?>
            </div>
             <?php }else{ 
                echo "<span class='text-warning'>";
               for($star = 1; $star <= $checkStar1; $star++){
                    echo "★";
                }
                 echo "</span>";
             } ?>
        </td>
    </tr>
    <tr>
        <td><strong>⏰ User Closed Days:</strong></td>
        <td><?= $dayActivity->user_close_own_day ?></td>
        <td>
            <p class="mb-2 fw-semibold">User Closed His Days Is Right?</p>
        </td>
        <td>
            <?php 
            $checkStar1 = $this->Menu_model->GetGivedStarRatingsCount($targetuid,$sdate,"User Closed His Days Is Right?");
            if($checkStar1 == 0){
            ?>
            <div class="star-rating" data-question="User Closed His Days Is Right?" data-task-id="<?= $targetuid ?>" data-check-date="<?= $sdate ?>" data-task-rid="<?= $j ?>_4">
                <?php for ($i = 5; $i >= 1; $i--): ?>
                    <input type="radio" id="star<?= $i ?>-<?= $j ?>-4" name="rating_<?= $targetuid ?>_<?= $j ?>_4" value="<?= $i ?>" onchange="disableRating(this)">
                    <label for="star<?= $i ?>-<?= $j ?>-4" title="<?= $i ?> star<?= $i > 1 ? 's' : '' ?>">★</label>
                <?php endfor; ?>
            </div>
            <?php }else{ 
                echo "<span class='text-warning'>";
               for($star = 1; $star <= $checkStar1; $star++){
                    echo "★";
                }
                 echo "</span>";
             } ?>
        </td>
    </tr>
    <?php if($dayActivity->user_close !=''){
        $clatitude  = $dayActivity->clatitude;
        $clongitude = $dayActivity->clongitude;
        $closedMap  = "$clatitude,$clongitude";
    ?>
    <tr>
        <td><strong>⏰ Closed Time:</strong></td>
        <td><?= $dayActivity->user_close ?></td>
        <td>
            <p class="mb-2 fw-semibold">Closed Time Is Right Or Not?</p>
        </td>
        <td>
             <?php 
            $checkStar1 = $this->Menu_model->GetGivedStarRatingsCount($targetuid,$sdate,"Closed Time Is Right Or Not?");
            if($checkStar1 == 0){
            ?>
            <div class="star-rating" data-question="Closed Time Is Right Or Not?" data-task-id="<?= $targetuid ?>" data-check-date="<?= $sdate ?>" data-task-rid="<?= $j ?>_5">
                <?php for ($i = 5; $i >= 1; $i--): ?>
                    <input type="radio" id="star<?= $i ?>-<?= $j ?>-5" name="rating_<?= $targetuid ?>_<?= $j ?>_5" value="<?= $i ?>" onchange="disableRating(this)">
                    <label for="star<?= $i ?>-<?= $j ?>-5" title="<?= $i ?> star<?= $i > 1 ? 's' : '' ?>">★</label>
                <?php endfor; ?>
            </div>
            <?php }else{ 
                echo "<span class='text-warning'>";
               for($star = 1; $star <= $checkStar1; $star++){
                    echo "★";
                }
                 echo "</span>";
             } ?>
        </td>
    </tr>
    <tr>
        <td><strong>🧑‍💼 Closed Selfie:</strong></td>
        <td>
            <span class="view-data custom-btn btn-11 partnearray p-1" data-rid="row_id_<?=$j?>" data-content="<?=base_url().$dayActivity->ucimg ?>" data-title="ucimg" style="height: 30px;">View Selfie</span>
        </td>
        <td>
            <p class="mb-2 fw-semibold">Closed Selfie Is Right Or Not?</p>
        </td>
        <td>
            <?php 
            $checkStar1 = $this->Menu_model->GetGivedStarRatingsCount($targetuid,$sdate,"Closed Selfie Is Right Or Not?");
            if($checkStar1 == 0){
            ?>
            <div class="star-rating" data-question="Closed Selfie Is Right Or Not?" data-task-id="<?= $targetuid ?>" data-check-date="<?= $sdate ?>" data-task-rid="<?= $j ?>_6">
                <?php for ($i = 5; $i >= 1; $i--): ?>
                    <input type="radio" id="star<?= $i ?>-<?= $j ?>-6" name="rating_<?= $targetuid ?>_<?= $j ?>_6" value="<?= $i ?>" onchange="disableRating(this)">
                    <label for="star<?= $i ?>-<?= $j ?>-6" title="<?= $i ?> star<?= $i > 1 ? 's' : '' ?>">★</label>
                <?php endfor; ?>
            </div>
            <?php }else{ 
                echo "<span class='text-warning'>";
               for($star = 1; $star <= $checkStar1; $star++){
                    echo "★";
                }
                 echo "</span>";
             } ?>
        </td>
    </tr>
    <tr>
        <td><strong>📍 Closed Location:</strong></td>
        <td>
            <span class="view-data custom-btn btn-11 partnearray p-1" data-rid="row_id_<?=$j?>" data-content='<?= $closedMap ?>' data-title="closeLocation" style="height: 30px;">Closed Location</span>
        </td>
        <td>
            <p class="mb-2 fw-semibold">Closed Location Is Right Or Not?</p>
        </td>
        <td>
             <?php 
            $checkStar1 = $this->Menu_model->GetGivedStarRatingsCount($targetuid,$sdate,"Closed Location Is Right Or Not?");
            if($checkStar1 == 0){
            ?>
            <div class="star-rating" data-question="Closed Location Is Right Or Not?" data-task-id="<?= $targetuid ?>" data-check-date="<?= $sdate ?>" data-task-rid="<?= $j ?>_7">
                <?php for ($i = 5; $i >= 1; $i--): ?>
                    <input type="radio" id="star<?= $i ?>-<?= $j ?>-7" name="rating_<?= $targetuid ?>_<?= $j ?>_7" value="<?= $i ?>" onchange="disableRating(this)">
                    <label for="star<?= $i ?>-<?= $j ?>-7" title="<?= $i ?> star<?= $i > 1 ? 's' : '' ?>">★</label>
                <?php endfor; ?>
            </div>
            <?php }else{ 
                echo "<span class='text-warning'>";
               for($star = 1; $star <= $checkStar1; $star++){
                    echo "★";
                }
                 echo "</span>";
             } ?>
        </td>
    </tr>
    <?php } ?>
    <?php if($dayActivity->planning_userworkfrom == ''){
        $plannerSetOneDayBefore = 'No';
    }else{
        $plannerSetOneDayBefore = 'Yes';
    } ?>
    <tr>
        <td><strong>🏢 Planner set for the day before:</strong></td>
        <td><?= $plannerSetOneDayBefore ?></td>
        <td>
            <p class="mb-2 fw-semibold">Planner set for the day before or not ?</p>
        </td>
        <td>
            <?php 
            $checkStar1 = $this->Menu_model->GetGivedStarRatingsCount($targetuid,$sdate,"Planner set for the day before or not ?");
            if($checkStar1 == 0){
            ?>
            <div class="star-rating" data-question="Planner set for the day before or not ?" data-task-id="<?= $targetuid ?>" data-check-date="<?= $sdate ?>" data-task-rid="<?= $j ?>_8">
                <?php for ($i = 5; $i >= 1; $i--): ?>
                    <input type="radio" id="star<?= $i ?>-<?= $j ?>-8" name="rating_<?= $targetuid ?>_<?= $j ?>_8" value="<?= $i ?>" onchange="disableRating(this)">
                    <label for="star<?= $i ?>-<?= $j ?>-8" title="<?= $i ?> star<?= $i > 1 ? 's' : '' ?>">★</label>
                <?php endfor; ?>
            </div>
            <?php }else{ 
                echo "<span class='text-warning'>";
               for($star = 1; $star <= $checkStar1; $star++){
                    echo "★";
                }
                 echo "</span>";
             } ?>
        </td>
    </tr>
    <tr>
        <td><strong>🏢 Start Work Form:</strong></td>
        <td><?= $dayActivity->start_userworkfrom ?></td>
        <td>
            <p class="mb-2 fw-semibold">Start Work Form Is Right or Not?</p>
        </td>
        <td>
             <?php 
            $checkStar1 = $this->Menu_model->GetGivedStarRatingsCount($targetuid,$sdate,"Start Work Form Is Right or Not?");
            if($checkStar1 == 0){
            ?>
            <div class="star-rating" data-question="Start Work Form Is Right or Not?" data-task-id="<?= $targetuid ?>" data-check-date="<?= $sdate ?>" data-task-rid="<?= $j ?>_9">
                <?php for ($i = 5; $i >= 1; $i--): ?>
                    <input type="radio" id="star<?= $i ?>-<?= $j ?>-9" name="rating_<?= $targetuid ?>_<?= $j ?>_9" value="<?= $i ?>" onchange="disableRating(this)">
                    <label for="star<?= $i ?>-<?= $j ?>-9" title="<?= $i ?> star<?= $i > 1 ? 's' : '' ?>">★</label>
                <?php endfor; ?>
            </div>
             <?php }else{ 
                echo "<span class='text-warning'>";
               for($star = 1; $star <= $checkStar1; $star++){
                    echo "★";
                }
                 echo "</span>";
             } ?>
        </td>
    </tr>
    <?php if($plannerSetOneDayBefore == 'Yes'){ ?>
    <tr>
        <td><strong>🏢 Start Work Form As Planning:</strong></td>
        <td><?= $dayActivity->planning_userworkfrom; ?> </td>
        <td>
            <p class="mb-2 fw-semibold">Start Work Form As Planning Is Right Or Not?</p>
        </td>
        <td>
             <?php 
            $checkStar1 = $this->Menu_model->GetGivedStarRatingsCount($targetuid,$sdate,"Start Work Form As Planning Is Right Or Not?");
            if($checkStar1 == 0){
            ?>
            <div class="star-rating" data-question="Start Work Form As Planning Is Right Or Not?" data-task-id="<?= $targetuid ?>" data-check-date="<?= $sdate ?>" data-task-rid="<?= $j ?>_10">
                <?php for ($i = 5; $i >= 1; $i--): ?>
                    <input type="radio" id="star<?= $i ?>-<?= $j ?>-10" name="rating_<?= $targetuid ?>_<?= $j ?>_10" value="<?= $i ?>" onchange="disableRating(this)">
                    <label for="star<?= $i ?>-<?= $j ?>-10" title="<?= $i ?> star<?= $i > 1 ? 's' : '' ?>">★</label>
                <?php endfor; ?>
            </div>
            <?php }else{ 
                echo "<span class='text-warning'>";
               for($star = 1; $star <= $checkStar1; $star++){
                    echo "★";
                }
                 echo "</span>";
             } ?>
        </td>
    </tr>
    <tr>
        <td><strong>📋 Start as like Planning:</strong></td>
        <td><?= $dayActivity->start_as_like_planning ?></td>
        <td>
            <p class="mb-2 fw-semibold">Start as like Planning Or Not?</p>
        </td>
        <td>
              <?php 
            $checkStar1 = $this->Menu_model->GetGivedStarRatingsCount($targetuid,$sdate,"Start as like Planning Or Not?");
            if($checkStar1 == 0){
            ?>
            <div class="star-rating" data-question="Start as like Planning Or Not?" data-task-id="<?= $targetuid ?>" data-check-date="<?= $sdate ?>" data-task-rid="<?= $j ?>_11">
                <?php for ($i = 5; $i >= 1; $i--): ?>
                    <input type="radio" id="star<?= $i ?>-<?= $j ?>-11" name="rating_<?= $targetuid ?>_<?= $j ?>_11" value="<?= $i ?>" onchange="disableRating(this)">
                    <label for="star<?= $i ?>-<?= $j ?>-11" title="<?= $i ?> star<?= $i > 1 ? 's' : '' ?>">★</label>
                <?php endfor; ?>
            </div>
            <?php }else{ 
                echo "<span class='text-warning'>";
               for($star = 1; $star <= $checkStar1; $star++){
                    echo "★";
                }
                 echo "</span>";
             } ?>
        </td>
    </tr>
    <?php } ?>
    <tr>
        <td><strong>📝 Start Planning:</strong></td>
        <td><?= $dayActivity->start_planning ?></td>
        <td>
            <p class="mb-2 fw-semibold">User Start Planning or Not?</p>
        </td>
        <td>
             <?php 
            $checkStar1 = $this->Menu_model->GetGivedStarRatingsCount($targetuid,$sdate,"User Start Planning or Not?");
            if($checkStar1 == 0){
            ?>
            <div class="star-rating" data-question="User Start Planning or Not?" data-task-id="<?= $targetuid ?>" data-check-date="<?= $sdate ?>" data-task-rid="<?= $j ?>_12">
                <?php for ($i = 5; $i >= 1; $i--): ?>
                    <input type="radio" id="star<?= $i ?>-<?= $j ?>-12" name="rating_<?= $targetuid ?>_<?= $j ?>_12" value="<?= $i ?>" onchange="disableRating(this)">
                    <label for="star<?= $i ?>-<?= $j ?>-12" title="<?= $i ?> star<?= $i > 1 ? 's' : '' ?>">★</label>
                <?php endfor; ?>
            </div>
            <?php }else{ 
                echo "<span class='text-warning'>";
               for($star = 1; $star <= $checkStar1; $star++){
                    echo "★";
                }
                 echo "</span>";
             } ?>
        </td>
    </tr>
    <?php if($dayActivity->start_planning == 'Yes'){ ?>
    <tr>
        <td><strong>📆 Start Planning on Day:</strong></td>
        <td><?= $dayActivity->start_planning_on_day ?></td>
        <td>
            <p class="mb-2 fw-semibold">Start Planning on Day or Not?</p>
        </td>
        <td>
             <?php 
            $checkStar1 = $this->Menu_model->GetGivedStarRatingsCount($targetuid,$sdate,"Start Planning on Day or Not?");
            if($checkStar1 == 0){
            ?>
            <div class="star-rating" data-question="Start Planning on Day or Not?" data-task-id="<?= $targetuid ?>" data-check-date="<?= $sdate ?>" data-task-rid="<?= $j ?>_13">
                <?php for ($i = 5; $i >= 1; $i--): ?>
                    <input type="radio" id="star<?= $i ?>-<?= $j ?>-13" name="rating_<?= $targetuid ?>_<?= $j ?>_13" value="<?= $i ?>" onchange="disableRating(this)">
                    <label for="star<?= $i ?>-<?= $j ?>-13" title="<?= $i ?> star<?= $i > 1 ? 's' : '' ?>">★</label>
                <?php endfor; ?>
            </div>
             <?php }else{ 
                echo "<span class='text-warning'>";
               for($star = 1; $star <= $checkStar1; $star++){
                    echo "★";
                }
                 echo "</span>";
             } ?>
        </td>
    </tr>
    <?php } ?>
    <tr>
        <td><strong>📥 Request Same Day Task Plan:</strong></td>
        <td><?= $dayActivity->request_same_day_task_plan ?></td>
        <td>
            <p class="mb-2 fw-semibold">Request Same Day Task Plan Or Not?</p>
        </td>
        <td>
        <?php 
            $checkStar1 = $this->Menu_model->GetGivedStarRatingsCount($targetuid,$sdate,"Request Same Day Task Plan Or Not?");
            if($checkStar1 == 0){
            ?>
            <div class="star-rating" data-question="Request Same Day Task Plan Or Not?" data-task-id="<?= $targetuid ?>" data-check-date="<?= $sdate ?>" data-task-rid="<?= $j ?>_14">
                <?php for ($i = 5; $i >= 1; $i--): ?>
                    <input type="radio" id="star<?= $i ?>-<?= $j ?>-14" name="rating_<?= $targetuid ?>_<?= $j ?>_14" value="<?= $i ?>" onchange="disableRating(this)">
                    <label for="star<?= $i ?>-<?= $j ?>-14" title="<?= $i ?> star<?= $i > 1 ? 's' : '' ?>">★</label>
                <?php endfor; ?>
            </div>
            <?php }else{ 
                echo "<span class='text-warning'>";
               for($star = 1; $star <= $checkStar1; $star++){
                    echo "★";
                }
                 echo "</span>";
             } ?>
        </td>
    </tr>
    <?php if($dayActivity->request_same_day_task_plan == 'Yes'){ ?>
    <tr>
        <td><strong>🗣️ Request Same Day User Remarks:</strong></td>
        <td><?= $dayActivity->request_same_day_user_remarks ?></td>
        <td>
            <p class="mb-2 fw-semibold">Request Same Day User Remarks Or Not?</p>
        </td>
        <td>
             <?php 
            $checkStar1 = $this->Menu_model->GetGivedStarRatingsCount($targetuid,$sdate,"Request Same Day User Remarks Or Not?");
            if($checkStar1 == 0){
            ?>
            <div class="star-rating" data-question="Request Same Day User Remarks Or Not?" data-task-id="<?= $targetuid ?>" data-check-date="<?= $sdate ?>" data-task-rid="<?= $j ?>_15">
                <?php for ($i = 5; $i >= 1; $i--): ?>
                    <input type="radio" id="star<?= $i ?>-<?= $j ?>-15" name="rating_<?= $targetuid ?>_<?= $j ?>_15" value="<?= $i ?>" onchange="disableRating(this)">
                    <label for="star<?= $i ?>-<?= $j ?>-15" title="<?= $i ?> star<?= $i > 1 ? 's' : '' ?>">★</label>
                <?php endfor; ?>
            </div>
             <?php }else{ 
                echo "<span class='text-warning'>";
               for($star = 1; $star <= $checkStar1; $star++){
                    echo "★";
                }
                 echo "</span>";
             } ?>
        </td>
    </tr>
    <tr>
        <td><strong>✅ Request Same Day Approval Status:</strong></td>
        <td><?= $dayActivity->request_same_day_approvel_status ?></td>
        <td>
            <p class="mb-2 fw-semibold">Request Same Day Approval Status Or Not?</p>
        </td>
        <td>
             <?php 
            $checkStar1 = $this->Menu_model->GetGivedStarRatingsCount($targetuid,$sdate,"Request Same Day Approval Status Or Not?");
            if($checkStar1 == 0){
            ?>
            <div class="star-rating" data-question="Request Same Day Approval Status Or Not?" data-task-id="<?= $targetuid ?>" data-check-date="<?= $sdate ?>" data-task-rid="<?= $j ?>_16">
                <?php for ($i = 5; $i >= 1; $i--): ?>
                    <input type="radio" id="star<?= $i ?>-<?= $j ?>-16" name="rating_<?= $targetuid ?>_<?= $j ?>_16" value="<?= $i ?>" onchange="disableRating(this)">
                    <label for="star<?= $i ?>-<?= $j ?>-16" title="<?= $i ?> star<?= $i > 1 ? 's' : '' ?>">★</label>
                <?php endfor; ?>
            </div>
             <?php }else{ 
                echo "<span class='text-warning'>";
               for($star = 1; $star <= $checkStar1; $star++){
                    echo "★";
                }
                 echo "</span>";
             } ?>
        </td>
    </tr>
    <tr>
        <td><strong>🗨️ Request Same Day Admin Remarks:</strong></td>
        <td><?= $dayActivity->request_same_day_admin_remarks ?></td>
        <td>
            <p class="mb-2 fw-semibold">Request Same Day Admin Remarks is Right or Not?</p>
        </td>
        <td>
              <?php 
            $checkStar1 = $this->Menu_model->GetGivedStarRatingsCount($targetuid,$sdate,"Request Same Day Admin Remarks is Right or Not?");
            if($checkStar1 == 0){
            ?>
            <div class="star-rating" data-question="Request Same Day Admin Remarks is Right or Not?" data-task-id="<?= $targetuid ?>" data-check-date="<?= $sdate ?>" data-task-rid="<?= $j ?>_17">
                <?php for ($i = 5; $i >= 1; $i--): ?>
                    <input type="radio" id="star<?= $i ?>-<?= $j ?>-17" name="rating_<?= $targetuid ?>_<?= $j ?>_17" value="<?= $i ?>" onchange="disableRating(this)">
                    <label for="star<?= $i ?>-<?= $j ?>-17" title="<?= $i ?> star<?= $i > 1 ? 's' : '' ?>">★</label>
                <?php endfor; ?>
            </div>
            <?php }else{ 
                echo "<span class='text-warning'>";
               for($star = 1; $star <= $checkStar1; $star++){
                    echo "★";
                }
                 echo "</span>";
             } ?>
        </td>
    </tr>
    <tr>
        <td><strong>📅 Request Same Day Created At:</strong></td>
        <td><?= $dayActivity->request_same_day_created_at ?></td>
        <td>
            <p class="mb-2 fw-semibold">Request Same Day Created At Or Not?</p>
        </td>
        <td>
            <?php 
            $checkStar1 = $this->Menu_model->GetGivedStarRatingsCount($targetuid,$sdate,"Request Same Day Created At Or Not?");
            if($checkStar1 == 0){
            ?>
            <div class="star-rating" data-question="Request Same Day Created At Or Not?" data-task-id="<?= $targetuid ?>" data-check-date="<?= $sdate ?>" data-task-rid="<?= $j ?>_18">
                <?php for ($i = 5; $i >= 1; $i--): ?>
                    <input type="radio" id="star<?= $i ?>-<?= $j ?>-18" name="rating_<?= $targetuid ?>_<?= $j ?>_18" value="<?= $i ?>" onchange="disableRating(this)">
                    <label for="star<?= $i ?>-<?= $j ?>-18" title="<?= $i ?> star<?= $i > 1 ? 's' : '' ?>">★</label>
                <?php endfor; ?>
            </div>
            <?php }else{ 
                echo "<span class='text-warning'>";
               for($star = 1; $star <= $checkStar1; $star++){
                    echo "★";
                }
                 echo "</span>";
             } ?>
        </td>
    </tr>
    <tr>
        <td><strong>🕒 Request Same Day Planner Approval Time:</strong></td>
        <td><?= $dayActivity->request_same_day_planner_apr_time ?></td>
        <td>
            <p class="mb-2 fw-semibold">Request Same Day Planner Approval Time Is Right or Not?</p>
        </td>
        <td>
              <?php 
            $checkStar1 = $this->Menu_model->GetGivedStarRatingsCount($targetuid,$sdate,"Request Same Day Planner Approval Time Is Right or Not?");
            if($checkStar1 == 0){
            ?>
            <div class="star-rating" data-question="Request Same Day Planner Approval Time Is Right or Not?" data-task-id="<?= $targetuid ?>" data-check-date="<?= $sdate ?>" data-task-rid="<?= $j ?>_19">
                <?php for ($i = 5; $i >= 1; $i--): ?>
                    <input type="radio" id="star<?= $i ?>-<?= $j ?>-19" name="rating_<?= $targetuid ?>_<?= $j ?>_19" value="<?= $i ?>" onchange="disableRating(this)">
                    <label for="star<?= $i ?>-<?= $j ?>-19" title="<?= $i ?> star<?= $i > 1 ? 's' : '' ?>">★</label>
                <?php endfor; ?>
            </div>
            <?php }else{ 
                echo "<span class='text-warning'>";
               for($star = 1; $star <= $checkStar1; $star++){
                    echo "★";
                }
                 echo "</span>";
             } ?>
        </td>
    </tr>
    <?php } ?>
    <tr>
        <td><strong>📑 User Request To Planner Approved:</strong></td>
        <td><?= $dayActivity->user_create_planner_approved_request ?></td>
        <td>
            <p class="mb-2 fw-semibold">User Request To Planner Approved Or Not?</p>
        </td>
        <td>
               <?php 
            $checkStar1 = $this->Menu_model->GetGivedStarRatingsCount($targetuid,$sdate,"User Request To Planner Approved Or Not?");
            if($checkStar1 == 0){
            ?>
            <div class="star-rating" data-question="User Request To Planner Approved Or Not?" data-task-id="<?= $targetuid ?>" data-check-date="<?= $sdate ?>" data-task-rid="<?= $j ?>_20">
                <?php for ($i = 5; $i >= 1; $i--): ?>
                    <input type="radio" id="star<?= $i ?>-<?= $j ?>-20" name="rating_<?= $targetuid ?>_<?= $j ?>_20" value="<?= $i ?>" onchange="disableRating(this)">
                    <label for="star<?= $i ?>-<?= $j ?>-20" title="<?= $i ?> star<?= $i > 1 ? 's' : '' ?>">★</label>
                <?php endfor; ?>
            </div>
             <?php }else{ 
                echo "<span class='text-warning'>";
               for($star = 1; $star <= $checkStar1; $star++){
                    echo "★";
                }
                 echo "</span>";
             } ?>
        </td>
    </tr>
    <?php if($dayActivity->user_create_planner_approved_request == 'Yes'){ ?>
    <tr>
        <td><strong>🧾 Planner Request Message:</strong></td>
        <td><?= $dayActivity->planner_request_message ?></td>
        <td>
            <p class="mb-2 fw-semibold">Planner Request Message Is Right Or Not?</p>
        </td>
        <td>
            <?php 
            $checkStar1 = $this->Menu_model->GetGivedStarRatingsCount($targetuid,$sdate,"Planner Request Message Is Right Or Not?");
            if($checkStar1 == 0){
            ?>
            <div class="star-rating" data-question="Planner Request Message Is Right Or Not?" data-task-id="<?= $targetuid ?>" data-check-date="<?= $sdate ?>" data-task-rid="<?= $j ?>_21">
                <?php for ($i = 5; $i >= 1; $i--): ?>
                    <input type="radio" id="star<?= $i ?>-<?= $j ?>-21" name="rating_<?= $targetuid ?>_<?= $j ?>_21" value="<?= $i ?>" onchange="disableRating(this)">
                    <label for="star<?= $i ?>-<?= $j ?>-21" title="<?= $i ?> star<?= $i > 1 ? 's' : '' ?>">★</label>
                <?php endfor; ?>
            </div>
            <?php }else{ 
                echo "<span class='text-warning'>";
               for($star = 1; $star <= $checkStar1; $star++){
                    echo "★";
                }
                 echo "</span>";
             } ?>
        </td>
    </tr>
    <tr>
        <td><strong>✅ Planner Approved Status:</strong></td>
        <td><?php if($dayActivity->planner_approved_status == 1){
            echo "<span class='bg-success p-1'>Approved</span>";
        }else if($dayActivity->planner_approved_status == 0){
            echo "<span class='bg-warning p-1'>Pending</span>";
        } ?></td>
        <td>
            <p class="mb-2 fw-semibold">Planner Approved Status is Right or Not?</p>
        </td>
        <td>
             <?php 
            $checkStar1 = $this->Menu_model->GetGivedStarRatingsCount($targetuid,$sdate,"Planner Approved Status is Right or Not?");
            if($checkStar1 == 0){
            ?>
            <div class="star-rating" data-question="Planner Approved Status is Right or Not?" data-task-id="<?= $targetuid ?>" data-check-date="<?= $sdate ?>" data-task-rid="<?= $j ?>_22">
                <?php for ($i = 5; $i >= 1; $i--): ?>
                    <input type="radio" id="star<?= $i ?>-<?= $j ?>-22" name="rating_<?= $targetuid ?>_<?= $j ?>_22" value="<?= $i ?>" onchange="disableRating(this)">
                    <label for="star<?= $i ?>-<?= $j ?>-22" title="<?= $i ?> star<?= $i > 1 ? 's' : '' ?>">★</label>
                <?php endfor; ?>
            </div>
            <?php }else{ 
                echo "<span class='text-warning'>";
               for($star = 1; $star <= $checkStar1; $star++){
                    echo "★";
                }
                 echo "</span>";
             } ?>
        </td>
    </tr>
    <tr>
        <td><strong>👤 Planner Approved By:</strong></td>
        <td><?= $dayActivity->planner_approved_by ?></td>
        <td>
            <p class="mb-2 fw-semibold">Planner Approved By Is Right or Not?</p>
        </td>
        <td>
             <?php 
            $checkStar1 = $this->Menu_model->GetGivedStarRatingsCount($targetuid,$sdate,"Planner Approved By Is Right or Not?");
            if($checkStar1 == 0){
            ?>
            <div class="star-rating" data-question="Planner Approved By Is Right or Not?" data-task-id="<?= $targetuid ?>" data-check-date="<?= $sdate ?>" data-task-rid="<?= $j ?>_23">
                <?php for ($i = 5; $i >= 1; $i--): ?>
                    <input type="radio" id="star<?= $i ?>-<?= $j ?>-23" name="rating_<?= $targetuid ?>_<?= $j ?>_23" value="<?= $i ?>" onchange="disableRating(this)">
                    <label for="star<?= $i ?>-<?= $j ?>-23" title="<?= $i ?> star<?= $i > 1 ? 's' : '' ?>">★</label>
                <?php endfor; ?>
            </div>
            <?php }else{ 
                echo "<span class='text-warning'>";
               for($star = 1; $star <= $checkStar1; $star++){
                    echo "★";
                }
                 echo "</span>";
             } ?>
        </td>
    </tr>
    <tr>
        <td><strong>🕓 Planner Request Time:</strong></td>
        <td><?= $dayActivity->planner_request_time ?></td>
        <td>
            <p class="mb-2 fw-semibold">Planner Request Time Is Right or Not?</p>
        </td>
        <td>
             <?php 
            $checkStar1 = $this->Menu_model->GetGivedStarRatingsCount($targetuid,$sdate,"Planner Request Time Is Right or Not?");
            if($checkStar1 == 0){
            ?>
            <div class="star-rating" data-question="Planner Request Time Is Right or Not?" data-task-id="<?= $targetuid ?>" data-check-date="<?= $sdate ?>" data-task-rid="<?= $j ?>_24">
                <?php for ($i = 5; $i >= 1; $i--): ?>
                    <input type="radio" id="star<?= $i ?>-<?= $j ?>-24" name="rating_<?= $targetuid ?>_<?= $j ?>_24" value="<?= $i ?>" onchange="disableRating(this)">
                    <label for="star<?= $i ?>-<?= $j ?>-24" title="<?= $i ?> star<?= $i > 1 ? 's' : '' ?>">★</label>
                <?php endfor; ?>
            </div>
            <?php }else{ 
                echo "<span class='text-warning'>";
               for($star = 1; $star <= $checkStar1; $star++){
                    echo "★";
                }
                 echo "</span>";
             } ?>
        </td>
    </tr>
    <tr>
        <td><strong>📌 Planner Approved Date:</strong></td>
        <td><?= $dayActivity->planner_approved_date ?></td>
        <td>
            <p class="mb-2 fw-semibold">Planner Approved Date Is Right or Not?</p>
        </td>
        <td>
            <?php 
            $checkStar1 = $this->Menu_model->GetGivedStarRatingsCount($targetuid,$sdate,"Planner Approved Date Is Right or Not?");
            if($checkStar1 == 0){
            ?>
            <div class="star-rating" data-question="Planner Approved Date Is Right or Not?" data-task-id="<?= $targetuid ?>" data-check-date="<?= $sdate ?>" data-task-rid="<?= $j ?>_25">
                <?php for ($i = 5; $i >= 1; $i--): ?>
                    <input type="radio" id="star<?= $i ?>-<?= $j ?>-25" name="rating_<?= $targetuid ?>_<?= $j ?>_25" value="<?= $i ?>" onchange="disableRating(this)">
                    <label for="star<?= $i ?>-<?= $j ?>-25" title="<?= $i ?> star<?= $i > 1 ? 's' : '' ?>">★</label>
                <?php endfor; ?>
            </div>
            <?php }else{ 
                echo "<span class='text-warning'>";
               for($star = 1; $star <= $checkStar1; $star++){
                    echo "★";
                }
                 echo "</span>";
             } ?>
        </td>
    </tr>
    <tr>
        <td><strong>⏳ Time Taken in Planner Approval:</strong></td>
        <td><?= $dayActivity->time_taken_in_planner_approved ?></td>
        <td>
            <p class="mb-2 fw-semibold">Time Taken in Planner Approval is Right Or Not ?</p>
        </td>
        <td>
            <?php 
            $checkStar1 = $this->Menu_model->GetGivedStarRatingsCount($targetuid,$sdate,"Time Taken in Planner Approval is Right Or Not ?");
            if($checkStar1 == 0){
            ?>
            <div class="star-rating" data-question="Time Taken in Planner Approval is Right Or Not ?" data-task-id="<?= $targetuid ?>" data-check-date="<?= $sdate ?>" data-task-rid="<?= $j ?>_26">
                <?php for ($i = 5; $i >= 1; $i--): ?>
                    <input type="radio" id="star<?= $i ?>-<?= $j ?>-26" name="rating_<?= $targetuid ?>_<?= $j ?>_26" value="<?= $i ?>" onchange="disableRating(this)">
                    <label for="star<?= $i ?>-<?= $j ?>-26" title="<?= $i ?> star<?= $i > 1 ? 's' : '' ?>">★</label>
                <?php endfor; ?>
            </div>
            <?php }else{ 
                echo "<span class='text-warning'>";
               for($star = 1; $star <= $checkStar1; $star++){
                    echo "★";
                }
                 echo "</span>";
             } ?>
        </td>
    </tr>
    <?php } ?>
    <tr>
        <td><strong>🌴 Special Request for Leave:</strong></td>
        <td><?= $dayActivity->special_request_for_leave ?></td>
        <td>
            <p class="mb-2 fw-semibold">Special Request for Leave is Right Or Not?</p>
        </td>
        <td>
            <?php 
            $checkStar1 = $this->Menu_model->GetGivedStarRatingsCount($targetuid,$sdate,"Special Request for Leave is Right Or Not?");
            if($checkStar1 == 0){
            ?>
            <div class="star-rating" data-question="Special Request for Leave is Right Or Not?" data-task-id="<?= $targetuid ?>" data-check-date="<?= $sdate ?>" data-task-rid="<?= $j ?>_27">
                <?php for ($i = 5; $i >= 1; $i--): ?>
                    <input type="radio" id="star<?= $i ?>-<?= $j ?>-27" name="rating_<?= $targetuid ?>_<?= $j ?>_27" value="<?= $i ?>" onchange="disableRating(this)">
                    <label for="star<?= $i ?>-<?= $j ?>-27" title="<?= $i ?> star<?= $i > 1 ? 's' : '' ?>">★</label>
                <?php endfor; ?>
            </div>
             <?php }else{ 
                echo "<span class='text-warning'>";
               for($star = 1; $star <= $checkStar1; $star++){
                    echo "★";
                }
                 echo "</span>";
             } ?>
        </td>
    </tr>
    <?php if($dayActivity->special_request_for_leave == 'Yes'){ ?>
    <tr>
        <td><strong>🕘 Special Request Start:</strong></td>
        <td><?= $dayActivity->special_request_start ?></td>
        <td>
            <p class="mb-2 fw-semibold">Special Request Start is Right Not?</p>
        </td>
        <td>
            <?php 
            $checkStar1 = $this->Menu_model->GetGivedStarRatingsCount($targetuid,$sdate,"Special Request Start is Right Not?");
            if($checkStar1 == 0){
            ?>
            <div class="star-rating" data-question="Special Request Start is Right Not?" data-task-id="<?= $targetuid ?>" data-check-date="<?= $sdate ?>" data-task-rid="<?= $j ?>_28">
                <?php for ($i = 5; $i >= 1; $i--): ?>
                    <input type="radio" id="star<?= $i ?>-<?= $j ?>-28" name="rating_<?= $targetuid ?>_<?= $j ?>_28" value="<?= $i ?>" onchange="disableRating(this)">
                    <label for="star<?= $i ?>-<?= $j ?>-28" title="<?= $i ?> star<?= $i > 1 ? 's' : '' ?>">★</label>
                <?php endfor; ?>
            </div>
            <?php }else{ 
                echo "<span class='text-warning'>";
               for($star = 1; $star <= $checkStar1; $star++){
                    echo "★";
                }
                 echo "</span>";
             } ?>
        </td>
    </tr>
    <tr>
        <td><strong>🕔 Special Request End:</strong></td>
        <td><?= $dayActivity->special_request_end ?></td>
        <td>
            <p class="mb-2 fw-semibold">Special Request End Is Right Or Not?</p>
        </td>
        <td>
            <?php 
            $checkStar1 = $this->Menu_model->GetGivedStarRatingsCount($targetuid,$sdate,"Special Request End Is Right Or Not?");
            if($checkStar1 == 0){
            ?>
            <div class="star-rating" data-question="Special Request End Is Right Or Not?" data-task-id="<?= $targetuid ?>" data-check-date="<?= $sdate ?>" data-task-rid="<?= $j ?>_29">
                <?php for ($i = 5; $i >= 1; $i--): ?>
                    <input type="radio" id="star<?= $i ?>-<?= $j ?>-29" name="rating_<?= $targetuid ?>_<?= $j ?>_29" value="<?= $i ?>" onchange="disableRating(this)">
                    <label for="star<?= $i ?>-<?= $j ?>-29" title="<?= $i ?> star<?= $i > 1 ? 's' : '' ?>">★</label>
                <?php endfor; ?>
            </div>
            <?php }else{ 
                echo "<span class='text-warning'>";
               for($star = 1; $star <= $checkStar1; $star++){
                    echo "★";
                }
                 echo "</span>";
             } ?>
        </td>
    </tr>
    <tr>
        <td><strong>📝 Special Request Purpose:</strong></td>
        <td><?= $dayActivity->special_request_prupose ?></td>
        <td>
            <p class="mb-2 fw-semibold">Special Request Purpose or Not?</p>
        </td>
        <td>
             <?php 
            $checkStar1 = $this->Menu_model->GetGivedStarRatingsCount($targetuid,$sdate,"Special Request Purpose or Not?");
            if($checkStar1 == 0){
            ?>
            <div class="star-rating" data-question="Special Request Purpose or Not?" data-task-id="<?= $targetuid ?>" data-check-date="<?= $sdate ?>" data-task-rid="<?= $j ?>_30">
                <?php for ($i = 5; $i >= 1; $i--): ?>
                    <input type="radio" id="star<?= $i ?>-<?= $j ?>-30" name="rating_<?= $targetuid ?>_<?= $j ?>_30" value="<?= $i ?>" onchange="disableRating(this)">
                    <label for="star<?= $i ?>-<?= $j ?>-30" title="<?= $i ?> star<?= $i > 1 ? 's' : '' ?>">★</label>
                <?php endfor; ?>
            </div>
             <?php }else{ 
                echo "<span class='text-warning'>";
               for($star = 1; $star <= $checkStar1; $star++){
                    echo "★";
                }
                 echo "</span>";
             } ?>
        </td>
    </tr>
    <tr>
        <td><strong>✅ Special Request Approve Status:</strong></td>
        <td><?= $dayActivity->special_request_approve_status ?></td>
        <td>
            <p class="mb-2 fw-semibold">Special Request Approve Status Is Right or Not?</p>
        </td>
        <td>
            <?php 
            $checkStar1 = $this->Menu_model->GetGivedStarRatingsCount($targetuid,$sdate,"Special Request Approve Status Is Right or Not?");
            if($checkStar1 == 0){
            ?>
            <div class="star-rating" data-question="Special Request Approve Status Is Right or Not?" data-task-id="<?= $targetuid ?>" data-check-date="<?= $sdate ?>" data-task-rid="<?= $j ?>_31">
                <?php for ($i = 5; $i >= 1; $i--): ?>
                    <input type="radio" id="star<?= $i ?>-<?= $j ?>-31" name="rating_<?= $targetuid ?>_<?= $j ?>_31" value="<?= $i ?>" onchange="disableRating(this)">
                    <label for="star<?= $i ?>-<?= $j ?>-31" title="<?= $i ?> star<?= $i > 1 ? 's' : '' ?>">★</label>
                <?php endfor; ?>
            </div>
            <?php }else{ 
                echo "<span class='text-warning'>";
               for($star = 1; $star <= $checkStar1; $star++){
                    echo "★";
                }
                 echo "</span>";
             } ?>
        </td>
    </tr>
    <tr>
        <td><strong>🧑‍💼 Special Request Approve By:</strong></td>
        <td><?= $dayActivity->special_request_approve_by ?></td>
        <td>
            <p class="mb-2 fw-semibold">Special Request Approve By Is Right Or Not?</p>
        </td>
        <td>
             <?php 
            $checkStar1 = $this->Menu_model->GetGivedStarRatingsCount($targetuid,$sdate,"Special Request Approve By Is Right Or Not?");
            if($checkStar1 == 0){
            ?>
            <div class="star-rating" data-question="Special Request Approve By Is Right Or Not?" data-task-id="<?= $targetuid ?>" data-check-date="<?= $sdate ?>" data-task-rid="<?= $j ?>_32">
                <?php for ($i = 5; $i >= 1; $i--): ?>
                    <input type="radio" id="star<?= $i ?>-<?= $j ?>-32" name="rating_<?= $targetuid ?>_<?= $j ?>_32" value="<?= $i ?>" onchange="disableRating(this)">
                    <label for="star<?= $i ?>-<?= $j ?>-32" title="<?= $i ?> star<?= $i > 1 ? 's' : '' ?>">★</label>
                <?php endfor; ?>
            </div>
            <?php }else{ 
                echo "<span class='text-warning'>";
               for($star = 1; $star <= $checkStar1; $star++){
                    echo "★";
                }
                 echo "</span>";
             } ?>
        </td>
    </tr>
    <tr>
        <td><strong>📆 Special Request Approve Date:</strong></td>
        <td><?= $dayActivity->special_request_approve_date ?></td>
        <td>
            <p class="mb-2 fw-semibold">Special Request Approve Date is Right or Not ?</p>
        </td>
        <td>
            <?php 
            $checkStar1 = $this->Menu_model->GetGivedStarRatingsCount($targetuid,$sdate,"Special Request Approve Date is Right or Not ?");
            if($checkStar1 == 0){
            ?>
            <div class="star-rating" data-question="Special Request Approve Date is Right or Not ?" data-task-id="<?= $targetuid ?>" data-check-date="<?= $sdate ?>" data-task-rid="<?= $j ?>_33">
                <?php for ($i = 5; $i >= 1; $i--): ?>
                    <input type="radio" id="star<?= $i ?>-<?= $j ?>-33" name="rating_<?= $targetuid ?>_<?= $j ?>_33" value="<?= $i ?>" onchange="disableRating(this)">
                    <label for="star<?= $i ?>-<?= $j ?>-33" title="<?= $i ?> star<?= $i > 1 ? 's' : '' ?>">★</label>
                <?php endfor; ?>
            </div>
             <?php }else{ 
                echo "<span class='text-warning'>";
               for($star = 1; $star <= $checkStar1; $star++){
                    echo "★";
                }
                 echo "</span>";
             } ?>
        </td>
    </tr>
    <tr>
        <td><strong>🗨️ Special Request Approve Admin Remarks:</strong></td>
        <td><?= $dayActivity->special_request_approve_admin_remarks ?></td>
        <td>
            <p class="mb-2 fw-semibold">Special Request Approve Admin Remarks is Right Or Not?</p>
        </td>
        <td>
            <?php 
            $checkStar1 = $this->Menu_model->GetGivedStarRatingsCount($targetuid,$sdate,"Special Request Approve Admin Remarks is Right Or Not?");
            if($checkStar1 == 0){
            ?>
            <div class="star-rating" data-question="Special Request Approve Admin Remarks is Right Or Not?" data-task-id="<?= $targetuid ?>" data-check-date="<?= $sdate ?>" data-task-rid="<?= $j ?>_34">
                <?php for ($i = 5; $i >= 1; $i--): ?>
                    <input type="radio" id="star<?= $i ?>-<?= $j ?>-34" name="rating_<?= $targetuid ?>_<?= $j ?>_34" value="<?= $i ?>" onchange="disableRating(this)">
                    <label for="star<?= $i ?>-<?= $j ?>-34" title="<?= $i ?> star<?= $i > 1 ? 's' : '' ?>">★</label>
                <?php endfor; ?>
            </div>
            <?php }else{ 
                echo "<span class='text-warning'>";
               for($star = 1; $star <= $checkStar1; $star++){
                    echo "★";
                }
                 echo "</span>";
             } ?>
        </td>
    </tr>
    <tr>
        <td><strong>📅 Special Request Create Time:</strong></td>
        <td><?= $dayActivity->special_request_for_create_time ?></td>
        <td>
            <p class="mb-2 fw-semibold">Special Request Create Time Is Right Or Not?</p>
        </td>
        <td>
               <?php 
            $checkStar1 = $this->Menu_model->GetGivedStarRatingsCount($targetuid,$sdate,"Special Request Create Time Is Right Or Not?");
            if($checkStar1 == 0){
            ?>
            <div class="star-rating" data-question="Special Request Create Time Is Right Or Not?" data-task-id="<?= $targetuid ?>" data-check-date="<?= $sdate ?>" data-task-rid="<?= $j ?>_35">
                <?php for ($i = 5; $i >= 1; $i--): ?>
                    <input type="radio" id="star<?= $i ?>-<?= $j ?>-35" name="rating_<?= $targetuid ?>_<?= $j ?>_35" value="<?= $i ?>" onchange="disableRating(this)">
                    <label for="star<?= $i ?>-<?= $j ?>-35" title="<?= $i ?> star<?= $i > 1 ? 's' : '' ?>">★</label>
                <?php endfor; ?>
            </div>
            <?php }else{ 
                echo "<span class='text-warning'>";
               for($star = 1; $star <= $checkStar1; $star++){
                    echo "★";
                }
                 echo "</span>";
             } ?>
        </td>
    </tr>
    <?php } ?>
    <tr>
        <td><strong>📋 Request Pending Task Planning:</strong></td>
        <td><?= $dayActivity->request_pending_task_planning ?></td>
        <td>
            <p class="mb-2 fw-semibold">Request Pending Task Planning Is Right or Not?</p>
        </td>
        <td>
             <?php 
            $checkStar1 = $this->Menu_model->GetGivedStarRatingsCount($targetuid,$sdate,"Request Pending Task Planning Is Right or Not?");
            if($checkStar1 == 0){
            ?>
            <div class="star-rating" data-question="Request Pending Task Planning Is Right or Not?" data-task-id="<?= $targetuid ?>" data-check-date="<?= $sdate ?>" data-task-rid="<?= $j ?>_36">
                <?php for ($i = 5; $i >= 1; $i--): ?>
                    <input type="radio" id="star<?= $i ?>-<?= $j ?>-36" name="rating_<?= $targetuid ?>_<?= $j ?>_36" value="<?= $i ?>" onchange="disableRating(this)">
                    <label for="star<?= $i ?>-<?= $j ?>-36" title="<?= $i ?> star<?= $i > 1 ? 's' : '' ?>">★</label>
                <?php endfor; ?>
            </div>
            <?php }else{ 
                echo "<span class='text-warning'>";
               for($star = 1; $star <= $checkStar1; $star++){
                    echo "★";
                }
                 echo "</span>";
             } ?>
        </td>
    </tr>
    <?php if($dayActivity->request_pending_task_planning == 'Yes'){ ?>
    <tr>
        <td><strong>📑 Request Pending Task Planning Request Type:</strong></td>
        <td><?= $dayActivity->request_pending_task_planning_request_type ?></td>
        <td>
            <p class="mb-2 fw-semibold">Request Pending Task Planning Request Type Is Right Or Not?</p>
        </td>
        <td>
             <?php 
            $checkStar1 = $this->Menu_model->GetGivedStarRatingsCount($targetuid,$sdate,"Request Pending Task Planning Request Type Is Right Or Not?");
            if($checkStar1 == 0){
            ?>
            <div class="star-rating" data-question="Request Pending Task Planning Request Type Is Right Or Not?" data-task-id="<?= $targetuid ?>" data-check-date="<?= $sdate ?>" data-task-rid="<?= $j ?>_37">
                <?php for ($i = 5; $i >= 1; $i--): ?>
                    <input type="radio" id="star<?= $i ?>-<?= $j ?>-37" name="rating_<?= $targetuid ?>_<?= $j ?>_37" value="<?= $i ?>" onchange="disableRating(this)">
                    <label for="star<?= $i ?>-<?= $j ?>-37" title="<?= $i ?> star<?= $i > 1 ? 's' : '' ?>">★</label>
                <?php endfor; ?>
            </div>
            <?php }else{ 
                echo "<span class='text-warning'>";
               for($star = 1; $star <= $checkStar1; $star++){
                    echo "★";
                }
                 echo "</span>";
             } ?>
        </td>
    </tr>
    <tr>
        <td><strong>🔢 Request Pending Task Planning Task Count:</strong></td>
        <td><?= $dayActivity->request_pending_task_planning_task_count ?></td>
        <td>
            <p class="mb-2 fw-semibold">Request Pending Task Planning Task Count Right Or Not?</p>
        </td>
        <td>
             <?php 
            $checkStar1 = $this->Menu_model->GetGivedStarRatingsCount($targetuid,$sdate,"Request Pending Task Planning Task Count Right Or Not?");
            if($checkStar1 == 0){
            ?>
            <div class="star-rating" data-question="Request Pending Task Planning Task Count Right Or Not?" data-task-id="<?= $targetuid ?>" data-check-date="<?= $sdate ?>" data-task-rid="<?= $j ?>_38">
                <?php for ($i = 5; $i >= 1; $i--): ?>
                    <input type="radio" id="star<?= $i ?>-<?= $j ?>-38" name="rating_<?= $targetuid ?>_<?= $j ?>_38" value="<?= $i ?>" onchange="disableRating(this)">
                    <label for="star<?= $i ?>-<?= $j ?>-38" title="<?= $i ?> star<?= $i > 1 ? 's' : '' ?>">★</label>
                <?php endfor; ?>
            </div>
            <?php }else{ 
                echo "<span class='text-warning'>";
               for($star = 1; $star <= $checkStar1; $star++){
                    echo "★";
                }
                 echo "</span>";
             } ?>
        </td>
    </tr>
    <tr>
        <td><strong>🗣️ Request Pending Task Planning User Request Remarks:</strong></td>
        <td><?= $dayActivity->request_pending_task_planning_user_request_remarks ?></td>
        <td>
            <p class="mb-2 fw-semibold">User Request Pending Task Planning Remarks is Right or Not?</p>
        </td>
        <td>
             <?php 
            $checkStar1 = $this->Menu_model->GetGivedStarRatingsCount($targetuid,$sdate,"User Request Pending Task Planning Remarks is Right or Not?");
            if($checkStar1 == 0){
            ?>
            <div class="star-rating" data-question="User Request Pending Task Planning Remarks is Right or Not?" data-task-id="<?= $targetuid ?>" data-check-date="<?= $sdate ?>" data-task-rid="<?= $j ?>_39">
                <?php for ($i = 5; $i >= 1; $i--): ?>
                    <input type="radio" id="star<?= $i ?>-<?= $j ?>-39" name="rating_<?= $targetuid ?>_<?= $j ?>_39" value="<?= $i ?>" onchange="disableRating(this)">
                    <label for="star<?= $i ?>-<?= $j ?>-39" title="<?= $i ?> star<?= $i > 1 ? 's' : '' ?>">★</label>
                <?php endfor; ?>
            </div>
            <?php }else{ 
                echo "<span class='text-warning'>";
               for($star = 1; $star <= $checkStar1; $star++){
                    echo "★";
                }
                 echo "</span>";
             } ?>
        </td>
    </tr>
    <tr>
        <td><strong>✅ Request Pending Task Planning Approved Status:</strong></td>
        <td>
            <?php if($dayActivity->request_pending_task_planning_approved_status == 1){
                echo "<span class='bg-success p-1'>Approved</span>";
            }else if($dayActivity->request_pending_task_planning_approved_status == 0){
                echo "<span class='bg-warning p-1'>Pending</span>";
            } ?>
        </td>
        <td>
            <p class="mb-2 fw-semibold">Request Pending Task Planning Approved Status Is Right Or Not?</p>
        </td>
        <td>
             <?php 
            $checkStar1 = $this->Menu_model->GetGivedStarRatingsCount($targetuid,$sdate,"Request Pending Task Planning Approved Status Is Right Or Not?");
            if($checkStar1 == 0){
            ?>
            <div class="star-rating" data-question="Request Pending Task Planning Approved Status Is Right Or Not?" data-task-id="<?= $targetuid ?>" data-check-date="<?= $sdate ?>" data-task-rid="<?= $j ?>_40">
                <?php for ($i = 5; $i >= 1; $i--): ?>
                    <input type="radio" id="star<?= $i ?>-<?= $j ?>-40" name="rating_<?= $targetuid ?>_<?= $j ?>_40" value="<?= $i ?>" onchange="disableRating(this)">
                    <label for="star<?= $i ?>-<?= $j ?>-40" title="<?= $i ?> star<?= $i > 1 ? 's' : '' ?>">★</label>
                <?php endfor; ?>
            </div>
            <?php }else{ 
                echo "<span class='text-warning'>";
               for($star = 1; $star <= $checkStar1; $star++){
                    echo "★";
                }
                 echo "</span>";
             } ?>
        </td>
    </tr>
    <tr>
        <td><strong>👤 Request Pending Task Planning Approved By:</strong></td>
        <td><?= $dayActivity->request_pending_task_planning_approved_by ?></td>
        <td>
            <p class="mb-2 fw-semibold">Request Pending Task Planning Approved By Is Right Or Not?</p>
        </td>
        <td>
             <?php 
            $checkStar1 = $this->Menu_model->GetGivedStarRatingsCount($targetuid,$sdate,"Request Pending Task Planning Approved By Is Right Or Not?");
            if($checkStar1 == 0){
            ?>
            <div class="star-rating" data-question="Request Pending Task Planning Approved By Is Right Or Not?" data-task-id="<?= $targetuid ?>" data-check-date="<?= $sdate ?>" data-task-rid="<?= $j ?>_41">
                <?php for ($i = 5; $i >= 1; $i--): ?>
                    <input type="radio" id="star<?= $i ?>-<?= $j ?>-41" name="rating_<?= $targetuid ?>_<?= $j ?>_41" value="<?= $i ?>" onchange="disableRating(this)">
                    <label for="star<?= $i ?>-<?= $j ?>-41" title="<?= $i ?> star<?= $i > 1 ? 's' : '' ?>">★</label>
                <?php endfor; ?>
            </div>
            <?php }else{ 
                echo "<span class='text-warning'>";
               for($star = 1; $star <= $checkStar1; $star++){
                    echo "★";
                }
                 echo "</span>";
             } ?>
        </td>
    </tr>
    <tr>
        <td><strong>📅 Request Pending Task Planning Approve Date:</strong></td>
        <td><?= $dayActivity->request_pending_task_planning_approve_date ?></td>
        <td>
            <p class="mb-2 fw-semibold">Request Pending Task Planning Approve Date Is Right or Not?</p>
        </td>
        <td>
             <?php 
            $checkStar1 = $this->Menu_model->GetGivedStarRatingsCount($targetuid,$sdate,"Request Pending Task Planning Approve Date Is Right or Not?");
            if($checkStar1 == 0){
            ?>
            <div class="star-rating" data-question="Request Pending Task Planning Approve Date Is Right or Not?" data-task-id="<?= $targetuid ?>" data-check-date="<?= $sdate ?>" data-task-rid="<?= $j ?>_42">
                <?php for ($i = 5; $i >= 1; $i--): ?>
                    <input type="radio" id="star<?= $i ?>-<?= $j ?>-42" name="rating_<?= $targetuid ?>_<?= $j ?>_42" value="<?= $i ?>" onchange="disableRating(this)">
                    <label for="star<?= $i ?>-<?= $j ?>-42" title="<?= $i ?> star<?= $i > 1 ? 's' : '' ?>">★</label>
                <?php endfor; ?>
            </div>
             <?php }else{ 
                echo "<span class='text-warning'>";
               for($star = 1; $star <= $checkStar1; $star++){
                    echo "★";
                }
                 echo "</span>";
             } ?>
        </td>
    </tr>
    <tr>
        <td><strong>🗨️ Request Pending Task Planning Admin Remarks:</strong></td>
        <td><?= $dayActivity->request_pending_task_planning_admin_remarks ?></td>
        <td>
            <p class="mb-2 fw-semibold">Request Pending Task Planning Admin Remarks Is Right or Not?</p>
        </td>
        <td>
            <?php 
            $checkStar1 = $this->Menu_model->GetGivedStarRatingsCount($targetuid,$sdate,"Request Pending Task Planning Admin Remarks Is Right or Not?");
            if($checkStar1 == 0){
            ?>
            <div class="star-rating" data-question="Request Pending Task Planning Admin Remarks Is Right or Not?" data-task-id="<?= $targetuid ?>" data-check-date="<?= $sdate ?>" data-task-rid="<?= $j ?>_43">
                <?php for ($i = 5; $i >= 1; $i--): ?>
                    <input type="radio" id="star<?= $i ?>-<?= $j ?>-43" name="rating_<?= $targetuid ?>_<?= $j ?>_43" value="<?= $i ?>" onchange="disableRating(this)">
                    <label for="star<?= $i ?>-<?= $j ?>-43" title="<?= $i ?> star<?= $i > 1 ? 's' : '' ?>">★</label>
                <?php endfor; ?>
            </div>
            <?php }else{ 
                echo "<span class='text-warning'>";
               for($star = 1; $star <= $checkStar1; $star++){
                    echo "★";
                }
                 echo "</span>";
             } ?>
        </td>
    </tr>
    <tr>
        <td><strong>🕓 Request Pending Task Planning Create Time:</strong></td>
        <td><?= $dayActivity->request_pending_task_planning_create_time ?></td>
        <td>
            <p class="mb-2 fw-semibold">Request Pending Task Planning Create Time Is Right or Not?</p>
        </td>
        <td>
            <?php 
            $checkStar1 = $this->Menu_model->GetGivedStarRatingsCount($targetuid,$sdate,"Request Pending Task Planning Create Time Is Right or Not?");
            if($checkStar1 == 0){
            ?>
            <div class="star-rating" data-question="Request Pending Task Planning Create Time Is Right or Not?" data-task-id="<?= $targetuid ?>" data-check-date="<?= $sdate ?>" data-task-rid="<?= $j ?>_44">
                <?php for ($i = 5; $i >= 1; $i--): ?>
                    <input type="radio" id="star<?= $i ?>-<?= $j ?>-44" name="rating_<?= $targetuid ?>_<?= $j ?>_44" value="<?= $i ?>" onchange="disableRating(this)">
                    <label for="star<?= $i ?>-<?= $j ?>-44" title="<?= $i ?> star<?= $i > 1 ? 's' : '' ?>">★</label>
                <?php endfor; ?>
            </div>
            <?php }else{ 
                echo "<span class='text-warning'>";
               for($star = 1; $star <= $checkStar1; $star++){
                    echo "★";
                }
                 echo "</span>";
             } ?>
        </td>
    </tr>
    <?php } ?>
    <tr>
        <td><strong>📌 Pending Meetings Delete Request:</strong></td>
        <td><?= $dayActivity->pending_meetings_request ?></td>
        <td>
            <p class="mb-2 fw-semibold">Pending Meetings Delete Request is Right Or Not?</p>
        </td>
        <td>
            <?php 
            $checkStar1 = $this->Menu_model->GetGivedStarRatingsCount($targetuid,$sdate,"Pending Meetings Delete Request is Right Or Not?");
            if($checkStar1 == 0){
            ?>
            <div class="star-rating" data-question="Pending Meetings Delete Request is Right Or Not?" data-task-id="<?= $targetuid ?>" data-check-date="<?= $sdate ?>" data-task-rid="<?= $j ?>_45">
                <?php for ($i = 5; $i >= 1; $i--): ?>
                    <input type="radio" id="star<?= $i ?>-<?= $j ?>-45" name="rating_<?= $targetuid ?>_<?= $j ?>_45" value="<?= $i ?>" onchange="disableRating(this)">
                    <label for="star<?= $i ?>-<?= $j ?>-45" title="<?= $i ?> star<?= $i > 1 ? 's' : '' ?>">★</label>
                <?php endfor; ?>
            </div>
             <?php }else{ 
                echo "<span class='text-warning'>";
               for($star = 1; $star <= $checkStar1; $star++){
                    echo "★";
                }
                 echo "</span>";
             } ?>
        </td>
    </tr>
    <?php if($dayActivity->pending_meetings_request == 'Yes'){ ?>
    <tr>
        <td><strong>🆔 Pending Meetings Request Task IDs:</strong></td>
        <td><?= $dayActivity->pending_meetings_request_task_ids ?></td>
        <td>
            <p class="mb-2 fw-semibold">Pending Meetings Request Task Is Right Or Not?</p>
        </td>
        <td>
             <?php 
            $checkStar1 = $this->Menu_model->GetGivedStarRatingsCount($targetuid,$sdate,"Pending Meetings Request Task Is Right Or Not?");
            if($checkStar1 == 0){
            ?>
            <div class="star-rating" data-question="Pending Meetings Request Task Is Right Or Not?" data-task-id="<?= $targetuid ?>" data-check-date="<?= $sdate ?>" data-task-rid="<?= $j ?>_46">
                <?php for ($i = 5; $i >= 1; $i--): ?>
                    <input type="radio" id="star<?= $i ?>-<?= $j ?>-46" name="rating_<?= $targetuid ?>_<?= $j ?>_46" value="<?= $i ?>" onchange="disableRating(this)">
                    <label for="star<?= $i ?>-<?= $j ?>-46" title="<?= $i ?> star<?= $i > 1 ? 's' : '' ?>">★</label>
                <?php endfor; ?>
            </div>
            <?php }else{ 
                echo "<span class='text-warning'>";
               for($star = 1; $star <= $checkStar1; $star++){
                    echo "★";
                }
                 echo "</span>";
             } ?>
        </td>
    </tr>
    <tr>
        <td><strong>🔢 Pending Meetings Request Task Count:</strong></td>
        <td><?= $dayActivity->pending_meetings_request_task_count ?></td>
        <td>
            <p class="mb-2 fw-semibold">Pending Meetings Request Task Count Is Right Or Not?</p>
        </td>
        <td>
            <?php 
            $checkStar1 = $this->Menu_model->GetGivedStarRatingsCount($targetuid,$sdate,"Pending Meetings Request Task Count Is Right Or Not?");
            if($checkStar1 == 0){
            ?>
            <div class="star-rating" data-question="Pending Meetings Request Task Count Is Right Or Not?" data-task-id="<?= $targetuid ?>" data-check-date="<?= $sdate ?>" data-task-rid="<?= $j ?>_47">
                <?php for ($i = 5; $i >= 1; $i--): ?>
                    <input type="radio" id="star<?= $i ?>-<?= $j ?>-47" name="rating_<?= $targetuid ?>_<?= $j ?>_47" value="<?= $i ?>" onchange="disableRating(this)">
                    <label for="star<?= $i ?>-<?= $j ?>-47" title="<?= $i ?> star<?= $i > 1 ? 's' : '' ?>">★</label>
                <?php endfor; ?>
            </div>
             <?php }else{ 
                echo "<span class='text-warning'>";
               for($star = 1; $star <= $checkStar1; $star++){
                    echo "★";
                }
                 echo "</span>";
             } ?>
        </td>
    </tr>
    <tr>
        <td><strong>🗣️ Pending Meetings Request User Request Remarks:</strong></td>
        <td><?= $dayActivity->pending_meetings_request_user_request_remarks ?></td>
        <td>
            <p class="mb-2 fw-semibold">Pending Meetings Request User Request Remarks Is Right Or Not?</p>
        </td>
        <td>
             <?php 
            $checkStar1 = $this->Menu_model->GetGivedStarRatingsCount($targetuid,$sdate,"Pending Meetings Request User Request Remarks Is Right Or Not?");
            if($checkStar1 == 0){
            ?>
            <div class="star-rating" data-question="Pending Meetings Request User Request Remarks Is Right Or Not?" data-task-id="<?= $targetuid ?>" data-check-date="<?= $sdate ?>" data-task-rid="<?= $j ?>_48">
                <?php for ($i = 5; $i >= 1; $i--): ?>
                    <input type="radio" id="star<?= $i ?>-<?= $j ?>-48" name="rating_<?= $targetuid ?>_<?= $j ?>_48" value="<?= $i ?>" onchange="disableRating(this)">
                    <label for="star<?= $i ?>-<?= $j ?>-48" title="<?= $i ?> star<?= $i > 1 ? 's' : '' ?>">★</label>
                <?php endfor; ?>
            </div>
            <?php }else{ 
                echo "<span class='text-warning'>";
               for($star = 1; $star <= $checkStar1; $star++){
                    echo "★";
                }
                 echo "</span>";
             } ?>
        </td>
    </tr>
    <tr>
        <td><strong>✅ Pending Meetings Request Approved Status:</strong></td>
        <td><?= $dayActivity->pending_meetings_request_approved_status ?></td>
        <td>
            <p class="mb-2 fw-semibold">Pending Meetings Request Approved Status Is Right Or Not?</p>
        </td>
        <td>
            <?php 
            $checkStar1 = $this->Menu_model->GetGivedStarRatingsCount($targetuid,$sdate,"Pending Meetings Request Approved Status Is Right Or Not?");
            if($checkStar1 == 0){
            ?>
            <div class="star-rating" data-question="Pending Meetings Request Approved Status Is Right Or Not?" data-task-id="<?= $targetuid ?>" data-check-date="<?= $sdate ?>" data-task-rid="<?= $j ?>_49">
                <?php for ($i = 5; $i >= 1; $i--): ?>
                    <input type="radio" id="star<?= $i ?>-<?= $j ?>-49" name="rating_<?= $targetuid ?>_<?= $j ?>_49" value="<?= $i ?>" onchange="disableRating(this)">
                    <label for="star<?= $i ?>-<?= $j ?>-49" title="<?= $i ?> star<?= $i > 1 ? 's' : '' ?>">★</label>
                <?php endfor; ?>
            </div>
            <?php }else{ 
                echo "<span class='text-warning'>";
               for($star = 1; $star <= $checkStar1; $star++){
                    echo "★";
                }
                 echo "</span>";
             } ?>
        </td>
    </tr>
    <tr>
        <td><strong>👤 Pending Meetings Request Approved By:</strong></td>
        <td><?= $dayActivity->pending_meetings_request_approved_by ?></td>
        <td>
            <p class="mb-2 fw-semibold">Pending Meetings Request Approved By Is Right Or Not?</p>
        </td>
        <td>
             <?php 
            $checkStar1 = $this->Menu_model->GetGivedStarRatingsCount($targetuid,$sdate,"Pending Meetings Request Approved By Is Right Or Not?");
            if($checkStar1 == 0){
            ?>
            <div class="star-rating" data-question="Pending Meetings Request Approved By Is Right Or Not?" data-task-id="<?= $targetuid ?>" data-check-date="<?= $sdate ?>" data-task-rid="<?= $j ?>_50">
                <?php for ($i = 5; $i >= 1; $i--): ?>
                    <input type="radio" id="star<?= $i ?>-<?= $j ?>-50" name="rating_<?= $targetuid ?>_<?= $j ?>_50" value="<?= $i ?>" onchange="disableRating(this)">
                    <label for="star<?= $i ?>-<?= $j ?>-50" title="<?= $i ?> star<?= $i > 1 ? 's' : '' ?>">★</label>
                <?php endfor; ?>
            </div>
             <?php }else{ 
                echo "<span class='text-warning'>";
               for($star = 1; $star <= $checkStar1; $star++){
                    echo "★";
                }
                 echo "</span>";
             } ?>
        </td>
    </tr>
    <tr>
        <td><strong>📅 Pending Meetings Request Approve Date:</strong></td>
        <td><?= $dayActivity->pending_meetings_request_approve_date ?></td>
        <td>
            <p class="mb-2 fw-semibold">Pending Meetings Request Approve Date Is Right Or Not?</p>
        </td>
        <td>
            <?php 
            $checkStar1 = $this->Menu_model->GetGivedStarRatingsCount($targetuid,$sdate,"Pending Meetings Request Approve Date Is Right Or Not?");
            if($checkStar1 == 0){
            ?>
            <div class="star-rating" data-question="Pending Meetings Request Approve Date Is Right Or Not?" data-task-id="<?= $targetuid ?>" data-check-date="<?= $sdate ?>" data-task-rid="<?= $j ?>_51">
                <?php for ($i = 5; $i >= 1; $i--): ?>
                    <input type="radio" id="star<?= $i ?>-<?= $j ?>-51" name="rating_<?= $targetuid ?>_<?= $j ?>_51" value="<?= $i ?>" onchange="disableRating(this)">
                    <label for="star<?= $i ?>-<?= $j ?>-51" title="<?= $i ?> star<?= $i > 1 ? 's' : '' ?>">★</label>
                <?php endfor; ?>
            </div>
            <?php }else{ 
                echo "<span class='text-warning'>";
               for($star = 1; $star <= $checkStar1; $star++){
                    echo "★";
                }
                 echo "</span>";
             } ?>
        </td>
    </tr>
    <tr>
        <td><strong>🗨️ Pending Meetings Request Admin Remarks:</strong></td>
        <td><?= $dayActivity->pending_meetings_request_admin_remarks ?></td>
        <td>
            <p class="mb-2 fw-semibold">Pending Meetings Request Admin Remarks Is Right Or Not?</p>
        </td>
        <td>
             <?php 
            $checkStar1 = $this->Menu_model->GetGivedStarRatingsCount($targetuid,$sdate,"Pending Meetings Request Admin Remarks Is Right Or Not?");
            if($checkStar1 == 0){
            ?>
            <div class="star-rating" data-question="Pending Meetings Request Admin Remarks Is Right Or Not?" data-task-id="<?= $targetuid ?>" data-check-date="<?= $sdate ?>" data-task-rid="<?= $j ?>_52">
                <?php for ($i = 5; $i >= 1; $i--): ?>
                    <input type="radio" id="star<?= $i ?>-<?= $j ?>-52" name="rating_<?= $targetuid ?>_<?= $j ?>_52" value="<?= $i ?>" onchange="disableRating(this)">
                    <label for="star<?= $i ?>-<?= $j ?>-52" title="<?= $i ?> star<?= $i > 1 ? 's' : '' ?>">★</label>
                <?php endfor; ?>
            </div>
            <?php }else{ 
                echo "<span class='text-warning'>";
               for($star = 1; $star <= $checkStar1; $star++){
                    echo "★";
                }
                 echo "</span>";
             } ?>
        </td>
    </tr>
    <tr>
        <td><strong>🕓 Pending Meetings Request Create Time:</strong></td>
        <td><?= $dayActivity->pending_meetings_request_create_time ?></td>
        <td>
            <p class="mb-2 fw-semibold">Pending Meetings Request Create Time Is Right Or Not?</p>
        </td>
        <td>
             <?php 
            $checkStar1 = $this->Menu_model->GetGivedStarRatingsCount($targetuid,$sdate,"Pending Meetings Request Create Time Is Right Or Not?");
            if($checkStar1 == 0){
            ?>
            <div class="star-rating" data-question="Pending Meetings Request Create Time Is Right Or Not?" data-task-id="<?= $targetuid ?>" data-check-date="<?= $sdate ?>" data-task-rid="<?= $j ?>_53">
                <?php for ($i = 5; $i >= 1; $i--): ?>
                    <input type="radio" id="star<?= $i ?>-<?= $j ?>-53" name="rating_<?= $targetuid ?>_<?= $j ?>_53" value="<?= $i ?>" onchange="disableRating(this)">
                    <label for="star<?= $i ?>-<?= $j ?>-53" title="<?= $i ?> star<?= $i > 1 ? 's' : '' ?>">★</label>
                <?php endfor; ?>
            </div>
             <?php }else{ 
                echo "<span class='text-warning'>";
               for($star = 1; $star <= $checkStar1; $star++){
                    echo "★";
                }
                 echo "</span>";
             } ?>
        </td>
    </tr>
    <?php } ?>
    <tr>
        <td><strong>✅ Total Complete Task:</strong></td>
        <td><?= $dayActivity->total_complete_task ?></td>
        <td>
            <p class="mb-2 fw-semibold">Total Complete Task Is Right Or Not?</p>
        </td>
        <td>
             <?php 
            $checkStar1 = $this->Menu_model->GetGivedStarRatingsCount($targetuid,$sdate,"Total Complete Task Is Right Or Not?");
            if($checkStar1 == 0){
            ?>
            <div class="star-rating" data-question="Total Complete Task Is Right Or Not?" data-task-id="<?= $targetuid ?>" data-check-date="<?= $sdate ?>" data-task-rid="<?= $j ?>_54">
                <?php for ($i = 5; $i >= 1; $i--): ?>
                    <input type="radio" id="star<?= $i ?>-<?= $j ?>-54" name="rating_<?= $targetuid ?>_<?= $j ?>_54" value="<?= $i ?>" onchange="disableRating(this)">
                    <label for="star<?= $i ?>-<?= $j ?>-54" title="<?= $i ?> star<?= $i > 1 ? 's' : '' ?>">★</label>
                <?php endfor; ?>
            </div>
            <?php }else{ 
                echo "<span class='text-warning'>";
               for($star = 1; $star <= $checkStar1; $star++){
                    echo "★";
                }
                 echo "</span>";
             } ?>
        </td>
    </tr>
    <tr>
        <td><strong>🕗 Total Pending Task:</strong></td>
        <td><?= $dayActivity->total_pending_task ?></td>
        <td>
            <p class="mb-2 fw-semibold">Total Pending Task Is Right Or Not?</p>
        </td>
        <td>
            <?php 
            $checkStar1 = $this->Menu_model->GetGivedStarRatingsCount($targetuid,$sdate,"Total Pending Task Is Right Or Not?");
            if($checkStar1 == 0){
            ?>
            <div class="star-rating" data-question="Total Pending Task Is Right Or Not?" data-task-id="<?= $targetuid ?>" data-check-date="<?= $sdate ?>" data-task-rid="<?= $j ?>_55">
                <?php for ($i = 5; $i >= 1; $i--): ?>
                    <input type="radio" id="star<?= $i ?>-<?= $j ?>-55" name="rating_<?= $targetuid ?>_<?= $j ?>_55" value="<?= $i ?>" onchange="disableRating(this)">
                    <label for="star<?= $i ?>-<?= $j ?>-55" title="<?= $i ?> star<?= $i > 1 ? 's' : '' ?>">★</label>
                <?php endfor; ?>
            </div>
            <?php }else{ 
                echo "<span class='text-warning'>";
               for($star = 1; $star <= $checkStar1; $star++){
                    echo "★";
                }
                 echo "</span>";
             } ?>
        </td>
    </tr>
</table>

                                  </div>


                        </div>
                      </div>
                    </div>
                   <?php 
                   $j++; 
                   endforeach;
                    ?>      
                    
                  </div>
                  <style>
                    /* Style for the red border */
                    .red-border {
                    border: 2px solid red;
                    border-radius: 14px;
                    padding: 20px;
                    /* background: linear-gradient(to right, rgb(255, 223, 246), rgb(255 138 138))!important; */
                    }
                    /* Keyframes for the blinking box shadow effect */
                    @keyframes blinkShadow {
                    0%, 100% {
                    box-shadow: 0 0 10px rgba(128, 0, 0, 0.6); /* Dark red shadow */
                    }
                    50% {
                    box-shadow: 0 0 20px rgba(128, 0, 0, 0.9); /* Darker and larger red shadow */
                    }
                    }
                    /* Apply the blinking box shadow effect */
                    .blinking-shadow-effect {
                    animation: blinkShadow 1s infinite;
                    }
                    /* Blinking effect for the card background */
                    @keyframes blinkBackground {
                    0% {
                    background: linear-gradient(to right, rgb(255, 223, 246), rgb(255 138 138));
                    }
                    100% {
                    background: linear-gradient(to right, rgb(255, 223, 246), rgb(255 138 138));
                    }
                    }
                    .hr_alert_class{
                    border-top: 1px solid rgb(255 0 0);
                    }
                    /* .blinking-background-effect {
                    animation: blinkBackground 1.5s infinite;
                    } */


                      #modalContentArea {
        position: relative;
        max-width: 90%;
        max-height: 80vh;
        overflow: hidden;
        text-align: center;
    }

    .rotatable-image {
        max-width: 100%;
        max-height: 100%;
        transition: transform 0.3s;
        transform-origin: center center;
        display: inline-block;
    }
                  </style>
                </div>
              </div>
            </div>
            <!-- /.card-body -->
          </div>
          

          <!-- Button trigger modal -->

          <!-- Modal -->
              <div class="modal fade" id="ratingModal" tabindex="-1" role="dialog" aria-labelledby="commentModalModalCenterTitle" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered" role="document">
                  <div class="modal-content" style="background: linear-gradient(to right,rgb(255, 223, 246),rgb(255, 255, 255)); border-radius: 14px; box-shadow: 0 4px 12px rgba(0,0,0,0.1); padding: 20px;">
                    <div class="modal-header">
                      <h5 class="modal-title" id="commentModalLabel">🌟 Add Star Rating </h5>
                      <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                      </button>
                    </div>
                    <div class="modal-body">
                       <div id="ratingModalBody">

                       </div>
                    </div>
                  </div>
                </div>
              </div>
           

                <!-- Modal -->
                <div class="modal fade" id="exampleModalCenterCardData" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered" role="document">
                    <div class="modal-content" style="background: linear-gradient(to right, #fcd3ff, #ffffff); border-radius: 12px; box-shadow: 0 4px 8px rgba(0,0,0,0.1); padding: 20px;">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalCenterTitle"></h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div id="modalContentArea">
                               
                        </div>
                    </div>
                    <!-- <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="button" class="btn btn-primary">Save changes</button>
                    </div> -->
                    </div>
                </div>
                </div>


            <!-- Remarks Modal -->
            <div class="modal fade" id="remarksModal" tabindex="-1" role="dialog" aria-labelledby="remarksModalLabel" aria-hidden="true">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="remarksModalLabel">Add Remarks</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                      
                            <input type="hidden" value="" id="question" required>
                            <input type="hidden" value="" id="taskId" required>
                            <input type="hidden" value="" id="checkDate" required>

                            <textarea class="form-control" id="remarksText" required name="remarks" rows="3"></textarea>
                            
                            <br>
                            <center>
                                <button type="button" id="submitRemarks" class="view-data custom-btn btn-11 partnearray p-1">Submit</button>
                            </center>

                        </div>
                    </div>
                </div>
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

    <script>
// function disableRating(selectedRadio) {
//     const radioGroup = selectedRadio.name;
//     const radios = document.getElementsByName(radioGroup);
//     radios.forEach(radio => {
//         if (radio !== selectedRadio) {
//             radio.disabled = true;
//         }
//     });
// }


function disableRating(selectedRadio) {
    const radioGroup = selectedRadio.name;
    const radios = document.getElementsByName(radioGroup);
    radios.forEach(radio => {
        radio.disabled = true; // Disable all radio inputs in the group
    });
    selectedRadio.disabled = false; // Re-enable the selected radio to ensure it stays selected
}

 $(document).ready(function() {
            let ratingCount = {};

            $('.star-rating input[type="radio"]').on('change', function() {
                const taskRid = $(this).parent().data('task-rid');
                const startCount = $(this).val();
              
                if (startCount <= 2) {
                    alert("* Please explain why you are giving only 2 stars or below.");

                    const $starContainer = $(this).closest('.star-rating');
                    const question = $starContainer.data('question');
                    const taskId = $starContainer.data('task-id');
                    const checkDate = $starContainer.data('check-date');

                    $('#remarksModal').modal('show');
                } else {
                    ratingCount[taskRid]++;
                }
            });
        });

        function handleRating(radio) {
            const taskRid = radio.closest('.star-rating').dataset.taskRid;
            const ratingValue = radio.value;
            console.log(`Task RID: ${taskRid}, Rating: ${ratingValue}`);
        }


        $('#submitRemarks').on('click', function () {
            let question  = $('#question').val().trim();
            let taskId    = $('#taskId').val().trim();
            let checkDate = $('#checkDate').val().trim();
            let remarks   = $('#remarksText').val().trim();

            if (question === '' || taskId === '' || checkDate === '') {
                alert("Required fields are missing.");
                return;
            }

            $('#remarksModal').modal('hide');
            $('#remarksText').val('');

            $.ajax({
                url: '<?= base_url("Menu/StoreStarRatingByPage") ?>', // update with actual path
                type: 'POST',
                data: {
                    question: question,
                    taskId: taskId,
                    checkDate: checkDate,
                    remarks: remarks
                },
                success: function (response) {
                    
                }
            });
        });




$(document).ready(function () {



    // Open the modal when star rating trigger is clicked
    // $(document).on('click', '.openCommentModal', function () {
    //     const $trigger = $(this); // Save reference
    //     const taskId = $trigger.data('task-id');
    //     const taskRid = $trigger.data('task-rid');
    //     const question = $trigger.data('question');

    //     const starHtml = `
    //         <div>
    //             <p class="mb-2 fw-semibold">${question}</p>
    //             <div class="star-rating" data-question="${question}" data-task-id="${taskId}" data-task-rid="${taskRid}">
    //                 <input type="radio" id="star5-${taskRid}" name="rating_${taskId}_${taskRid}" value="5" />
    //                 <label for="star5-${taskRid}" title="5 stars">★</label>
    //                 <input type="radio" id="star4-${taskRid}" name="rating_${taskId}_${taskRid}" value="4" />
    //                 <label for="star4-${taskRid}" title="4 stars">★</label>
    //                 <input type="radio" id="star3-${taskRid}" name="rating_${taskId}_${taskRid}" value="3" />
    //                 <label for="star3-${taskRid}" title="3 stars">★</label>
    //                 <input type="radio" id="star2-${taskRid}" name="rating_${taskId}_${taskRid}" value="2" />
    //                 <label for="star2-${taskRid}" title="2 stars">★</label>
    //                 <input type="radio" id="star1-${taskRid}" name="rating_${taskId}_${taskRid}" value="1" />
    //                 <label for="star1-${taskRid}" title="1 star">★</label>
    //             </div>
    //         </div>`;

    //     $('#ratingModalBody').html(starHtml);
    //     $('#ratingModal').modal('show');

    //     // Store the triggering element to replace later
    //     $('#ratingModal').data('triggerElement', $trigger);
    // });

    // Handle star rating selection
    $(document).on('change', '.star-rating input[type="radio"]', function () {
        const rating = parseInt($(this).val());
        const $starContainer = $(this).closest('.star-rating');
        const question = $starContainer.data('question');
        const taskId = $starContainer.data('task-id');
        const taskRid = $starContainer.data('task-rid');
        const checkDate = $starContainer.data('check-date');

        $("#question").val(question);
        $("#taskId").val(taskId);
        $("#taskRid").val(taskRid);
        $("#checkDate").val(checkDate);

        // AJAX to store rating
        fetch('<?=base_url().'Menu/StoreStarRatingByPage'?>', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                question: question,
                rating: rating,
                taskId:taskId,
                checkDate:checkDate
            })
        })
        .then(response => response.json())
        .then(data => {
            console.log('Success:', data);
        })
        // .catch(error => {
        //     console.error('Error:', error);
        // });
    });

    // Image rotation viewer logic
    var rotation = 0;

    $(document).on('click', '.view-data', function () {
        var content = $(this).data('content');
        var title = $(this).data('title');
        var rowID = $(this).data('rid');

        if (title == 'usimg') {
            $("#modalContentArea").html('');
            $("#exampleModalCenterTitle").text("Start Image");
            var imgID = 'userStartImage' + rowID;
            $("#modalContentArea").append(
                '<button id="rotateBtn" data-imgid="' + imgID + '" title="Rotate Image" style="position: absolute; top: 10px; right: 10px; z-index: 10; background: #fff; border: none; border-radius: 50%; padding: 6px;">🔄</button>'
            );
            var imgTag = '<img src="' + content + '" id="' + imgID + '" class="img-fluid" alt="User Start Image" style="transition: transform 0.3s;">';
            $("#modalContentArea").append(imgTag);
            $('.modal-dialog').removeAttr('style');
            $("#exampleModalCenterCardData").modal('show');
        }
        if (title == 'ucimg') {
            $("#modalContentArea").html('');
            $("#exampleModalCenterTitle").text("Closed Image");
            var imgID = 'userStartImage' + rowID;
            $("#modalContentArea").append(
                '<button id="rotateBtn" data-imgid="' + imgID + '" title="Rotate Image" style="position: absolute; top: 10px; right: 10px; z-index: 10; background: #fff; border: none; border-radius: 50%; padding: 6px;">🔄</button>'
            );
            var imgTag = '<img src="' + content + '" id="' + imgID + '" class="img-fluid" alt="User Start Image" style="transition: transform 0.3s;">';
            $("#modalContentArea").append(imgTag);
            $('.modal-dialog').removeAttr('style');
             $("#exampleModalCenterCardData").modal('show');
        }
        if (title == 'startLocation' || title == 'closeLocation') {
            $("#modalContentArea").html('');
           
            if(title == 'startLocation'){
                $("#exampleModalCenterTitle").text("Start Location");
                $('.modal-dialog').attr('style', 'max-height: 100vh !important; max-width: 100% !important;');
            }
           if(title == 'closeLocation'){
                $("#exampleModalCenterTitle").text("Closed Location");
                $('.modal-dialog').attr('style', 'max-height: 100vh !important; max-width: 100% !important;');
            }

            var mapIframe = '<iframe width="100%" height="450" frameborder="0" style="border:0" ' +
                    'src="https://www.google.com/maps?q=' + content + '&hl=es;z=14&output=embed" allowfullscreen>' +
                    '</iframe>';
            $('#modalContentArea').html(mapIframe);
             $("#exampleModalCenterCardData").modal('show');
        }

        rotation = 0;
       
    });

    $(document).on("click", "#rotateBtn", function () {
        rotation = (rotation + 90) % 360;
        var imgID = $(this).data('imgid');
        $("#" + imgID).css("transform", `rotate(${rotation}deg)`);
    });

});

 
        // document.querySelectorAll('.star-rating input[type="radio"]').forEach(input => {
        //     input.addEventListener('change', function() {
        //         const rating = this.value;
        //         const question = this.closest('.star-rating').getAttribute('data-question');

        //         // Send the data to the server using AJAX
        //         fetch('<?=base_url().'Menu/StoreStarRatingByPage'?>', {
        //             method: 'POST',
        //             headers: {
        //                 'Content-Type': 'application/json',
        //             },
        //             body: JSON.stringify({
        //                 question: question,
        //                 rating: rating
        //             })
        //         })
        //         .then(response => response.json())
        //         .then(data => {
        //             console.log('Success:', data);
        //         })
        //         .catch((error) => {
        //             console.error('Error:', error);
        //         });
        //     });
        // });
    </script>

    <script></script>
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
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.4.0/jspdf.umd.min.js"></script>
    <script>
 var totalUserCount = <?= sizeof($userWholeDays) ?>;

  for (var i = 1; i <= totalUserCount; i++) {
    $("#example" + i).DataTable({
      "responsive": false,
      "lengthChange": false,
      "autoWidth": false,
      "ordering": false,       // Optional: remove if you want sorting
      "info": false,           // Optional: remove if you want bottom info
      "pageLength": 50,      
      "buttons": ["csv", "excel", "pdf"]
    }).buttons().container().appendTo('#example' + i + '_wrapper .col-md-6:eq(0)');
  }

      
      $(document).ready(function () {
        $('#downloadPdf').click(function () {
          const button = $(this);
          button.prop('disabled', true).text('Please wait...');
      
          const { jsPDF } = window.jspdf;
          const doc = new jsPDF('p', 'mm', 'a4');
          const content = document.querySelector('.content-wrapper');
      
          html2canvas(content, {
            scale: 2,
          }).then(canvas => {
            const imgData = canvas.toDataURL('image/png');
            const imgWidth = 210;
            const pageHeight = 297;
            const imgHeight = canvas.height * imgWidth / canvas.width;
      
            let heightLeft = imgHeight;
            let position = 0;
      
            doc.addImage(imgData, 'PNG', 0, position, imgWidth, imgHeight);
            heightLeft -= pageHeight;
      
            while (heightLeft > 0) {
              position -= pageHeight;
              doc.addPage();
              doc.addImage(imgData, 'PNG', 0, position, imgWidth, imgHeight);
              heightLeft -= pageHeight;
            }
      
            doc.save('dashboard.pdf');
            // Revert button
            button.prop('disabled', false).text('Download PDF');
          }).catch(error => {
            console.error('PDF generation failed:', error);
            button.prop('disabled', false).text('Download PDF');
          });
        });
      });
      
      
      
    </script>
  </body>
</html>
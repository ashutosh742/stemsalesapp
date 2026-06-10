<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Live Day Start | STEM APP | WebAPP</title>
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
            <div class="row">
              <div class="col-12">
                <div class="card">
                  <div class="card-header" style="background: linear-gradient(to right, #ede7f6, #ffffff); border-radius: 12px; box-shadow: 0 4px 8px rgba(0,0,0,0.1); padding: 20px;">
                    <h3 class="text-center m-3">Track 👤 User 🌅 Day Start </h3>
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
                          <form action="<?=base_url()?>Reports/DayStartLive" method="post" class="mt-3">
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
                        <h5 class="p-2">👤 Total Present User </h5>
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

                      <hr>

                      <div class="row">
                          <div class="col-md-4">
                      <div class="p-2 text-center mb-1" style="background: linear-gradient(to right, #ffecd2,rgb(249, 252, 159)); border-radius: 12px; box-shadow: 0 4px 12px rgba(0,0,0,0.1); padding: 20px;">
                        <h5 class="p-2">👤 Total Present User </h5>
                        <p><a style="color: inherit;" href="<?= base_url() ?>/Reports/PlannerReportDataBYDate/<?= $uid ?>/<?= $row->report_date ?>/<?= $row->report_date ?>/total_present_user" target="_blank"><?= htmlspecialchars($row->total_present_user); ?></a></p>
                      </div>
                    </div>
                    <div class="col-md-4">
                      <div class="p-2 text-center mb-1" style="background: linear-gradient(to right, #ffecd2,rgb(169, 247, 157)); border-radius: 12px; box-shadow: 0 4px 12px rgba(0,0,0,0.1); padding: 20px;">
                        <h5 class="p-2">👤 Total Users Who Were Present & Closed Their Day</h5>
                        <p><a style="color: inherit;" href="<?= base_url() ?>/Reports/PlannerReportDataBYDate/<?= $uid ?>/<?= $row->report_date ?>/<?= $row->report_date ?>/total_present_and_closed_day_user" target="_blank"><?= htmlspecialchars($row->total_present_and_closed_day_user); ?></a></p>
                      </div>
                    </div>
                    <div class="col-md-4">
                      <div class="p-2 text-center mb-1" style="background: linear-gradient(to right, #ffecd2,rgb(251, 132, 132)); border-radius: 12px; box-shadow: 0 4px 12px rgba(0,0,0,0.1); padding: 20px;">
                        <h5 class="p-2">👤 Total Users Who Were Present & Not Closed Their Day</h5>
                        <p><a style="color: inherit;" href="<?= base_url() ?>/Reports/PlannerReportDataBYDate/<?= $uid ?>/<?= $row->report_date ?>/<?= $row->report_date ?>/total_present_and_not_closed_day_user" target="_blank"><?= htmlspecialchars($row->total_present_and_not_closed_day_user); ?></a></p>
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
                    // dd($userWholeDays);
                    ?>
                  <div class="row">
                    <?php 
                      $j = 1; 
                      foreach($userWholeDays as $dayActivity):
                        $targetuid = $dayActivity->user_id;
                       ?>
                    <div class="col-md-4 mb-4">
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
                          <?php  if($user['user_id'] !== $targetuid){
                            ?> 
                          <a href="javascript:void(0)" class="btn btn-sm btn-outline-dark position-absolute openCommentModal" 
                            data-task-id="<?= $targetuid ?>" style="top: 10px; right: 10px;">
                          ➕ Add Comment
                          </a>
                          <?php } ?>






            

            <!-- Start Time -->
            <?php if (!empty($dayActivity->user_start)): ?>
                <p class="mb-1"><strong>⏰ Start Time:</strong> <?= $dayActivity->user_start ?></p>
                <hr class="cardline">
            <?php endif; ?>

            <!-- Start Selfie -->
            <?php if (!empty($dayActivity->usimg)): ?>
                <p class="mb-1"><strong>🧑‍💼 Start Selfie:</strong>
                    <span class="view-data custom-btn btn-11 partnearray p-1" data-rid="row_id_<?= $j ?>" data-content="<?= base_url() . $dayActivity->usimg ?>" data-title="usimg" style="height: 30px;">View Selfie</span>
                </p>
                <hr class="cardline">
            <?php endif; ?>

            <!-- Start Location -->
            <?php if (!empty($startMap)): ?>
                <p class="mb-1"><strong>📍 Start Location:</strong>
                    <span class="view-data custom-btn btn-11 partnearray p-1" data-rid="row_id_<?= $j ?>" data-content='<?= $startMap ?>' data-title="startLocation" style="height: 30px;">Start Location</span>
                </p>
                <hr class="cardline">
            <?php endif; ?>

            <!-- Closed Time -->
            <?php if (!empty($dayActivity->user_close)): ?>
                <p class="mb-1"><strong>⏰ Closed Time:</strong> <?= $dayActivity->user_close ?></p>
                <hr class="cardline">
            <?php endif; ?>

            <!-- Closed Selfie -->
            <?php if (!empty($dayActivity->ucimg)): ?>
                <p class="mb-1"><strong>🧑‍💼 Closed Selfie:</strong>
                    <span class="view-data custom-btn btn-11 partnearray p-1" data-rid="row_id_<?= $j ?>" data-content="<?= base_url() . $dayActivity->ucimg ?>" data-title="ucimg" style="height: 30px;">View Selfie</span>
                </p>
                <hr class="cardline">
            <?php endif; ?>

            <!-- Closed Location -->
            <?php if (!empty($closedMap)): ?>
                <p class="mb-1"><strong>📍 Closed Location:</strong>
                    <span class="view-data custom-btn btn-11 partnearray p-1" data-rid="row_id_<?= $j ?>" data-content='<?= $closedMap ?>' data-title="closeLocation" style="height: 30px;">Closed Location</span>
                </p>
                <hr class="cardline">
            <?php endif; ?>

            <!-- Planner set for the day before -->
            <?php if (!empty($plannerSetOneDayBefore)): ?>
                <p class="mb-1"><strong>🏢 Planner set for the day before:</strong> <?= $plannerSetOneDayBefore ?></p>
                <hr class="cardline">
            <?php endif; ?>

            <!-- Start Work Form -->
            <?php if (!empty($dayActivity->start_userworkfrom)): ?>
                <p class="mb-1"><strong>🏢 Start Work Form:</strong> <?= $dayActivity->start_userworkfrom ?></p>
                <hr class="cardline">
            <?php endif; ?>

            <!-- Start Work Form As Planning -->
            <?php if (!empty($dayActivity->planning_userworkfrom)): ?>
                <p class="mb-1"><strong>🏢 Start Work Form As Planning:</strong> <?= $dayActivity->planning_userworkfrom ?></p>
                <hr class="cardline">
            <?php endif; ?>

            <!-- Start as like Planning -->
            <?php if (!empty($dayActivity->start_as_like_planning)): ?>
                <p class="mb-1"><strong>📋 Start as like Planning:</strong> <?= $dayActivity->start_as_like_planning ?></p>
                <hr class="cardline">
            <?php endif; ?>

            <!-- Start Planning -->
            <?php if (!empty($dayActivity->start_planning)): ?>
                <p class="mb-1"><strong>📝 Start Planning:</strong> <?= $dayActivity->start_planning ?></p>
                <hr class="cardline">
            <?php endif; ?>

            <!-- Start Planning on Day -->
            <?php if (!empty($dayActivity->start_planning_on_day)): ?>
                <p class="mb-1"><strong>📆 Start Planning on Day:</strong> <?= $dayActivity->start_planning_on_day ?></p>
                <hr class="cardline">
            <?php endif; ?>

            <!-- Request Same Day Task Plan -->
            <?php if (!empty($dayActivity->request_same_day_task_plan)): ?>
                <p class="mb-1"><strong>📥 Request Same Day Task Plan:</strong> <?= $dayActivity->request_same_day_task_plan ?></p>
                <hr class="cardline">
            <?php endif; ?>

            <!-- Request Same Day User Remarks -->
            <?php if (!empty($dayActivity->request_same_day_user_remarks)): ?>
                <p class="mb-1"><strong>🗣️ Request Same Day User Remarks:</strong> <?= $dayActivity->request_same_day_user_remarks ?></p>
                <hr class="cardline">
            <?php endif; ?>

            <!-- Request Same Day Approval Status -->
            <?php if (!empty($dayActivity->request_same_day_approvel_status)): ?>
                <p class="mb-1"><strong>✅ Request Same Day Approval Status:</strong> <?= $dayActivity->request_same_day_approvel_status ?></p>
                <hr class="cardline">
            <?php endif; ?>

            <!-- Request Same Day Admin Remarks -->
            <?php if (!empty($dayActivity->request_same_day_admin_remarks)): ?>
                <p class="mb-1"><strong>🗨️ Request Same Day Admin Remarks:</strong> <?= $dayActivity->request_same_day_admin_remarks ?></p>
                <hr class="cardline">
            <?php endif; ?>

            <!-- Request Same Day Created At -->
            <?php if (!empty($dayActivity->request_same_day_created_at)): ?>
                <p class="mb-1"><strong>📅 Request Same Day Created At:</strong> <?= $dayActivity->request_same_day_created_at ?></p>
                <hr class="cardline">
            <?php endif; ?>

            <!-- Request Same Day Planner Approval Time -->
            <?php if (!empty($dayActivity->request_same_day_planner_apr_time)): ?>
                <p class="mb-1"><strong>🕒 Request Same Day Planner Approval Time:</strong> <?= $dayActivity->request_same_day_planner_apr_time ?></p>
                <hr class="cardline">
            <?php endif; ?>

            <!-- User Request To Planner Approved -->
            <?php if (!empty($dayActivity->user_create_planner_approved_request)): ?>
                <p class="mb-1"><strong>📑 User Request To Planner Approved:</strong> <?= $dayActivity->user_create_planner_approved_request ?></p>
                <hr class="cardline">
            <?php endif; ?>

            <!-- Planner Request Message -->
            <?php if (!empty($dayActivity->planner_request_message)): ?>
                <p class="mb-1"><strong>🧾 Planner Request Message:</strong> <?= $dayActivity->planner_request_message ?></p>
                <hr class="cardline">
            <?php endif; ?>

            <!-- Planner Approved Status -->
            <?php if (isset($dayActivity->planner_approved_status)): ?>
                <p class="mb-1"><strong>✅ Planner Approved Status:</strong>
                    <?= $dayActivity->planner_approved_status == 1 ? "<span class='bg-success p-1'>Approved</span>" : "<span class='bg-warning p-1'>Pending</span>" ?>
                </p>
                <hr class="cardline">
            <?php endif; ?>

            <!-- Planner Approved By -->
            <?php if (!empty($dayActivity->planner_approved_by)): ?>
                <p class="mb-1"><strong>👤 Planner Approved By:</strong> <?= $dayActivity->planner_approved_by ?></p>
                <hr class="cardline">
            <?php endif; ?>

            <!-- Planner Request Time -->
            <?php if (!empty($dayActivity->planner_request_time)): ?>
                <p class="mb-1"><strong>🕓 Planner Request Time:</strong> <?= $dayActivity->planner_request_time ?></p>
                <hr class="cardline">
            <?php endif; ?>

            <!-- Planner Approved Date -->
            <?php if (!empty($dayActivity->planner_approved_date)): ?>
                <p class="mb-1"><strong>📌 Planner Approved Date:</strong> <?= $dayActivity->planner_approved_date ?></p>
                <hr class="cardline">
            <?php endif; ?>

            <!-- Time Taken in Planner Approval -->
            <?php if (!empty($dayActivity->time_taken_in_planner_approved)): ?>
                <p class="mb-1"><strong>⏳ Time Taken in Planner Approval:</strong> <?= $dayActivity->time_taken_in_planner_approved ?></p>
                <hr class="cardline">
            <?php endif; ?>

            <!-- Special Request for Leave -->
            <?php if (!empty($dayActivity->special_request_for_leave)): ?>
                <p class="mb-1"><strong>🌴 Special Request for Leave:</strong> <?= $dayActivity->special_request_for_leave ?></p>
                <hr class="cardline">
            <?php endif; ?>

            <!-- Special Request Start -->
            <?php if (!empty($dayActivity->special_request_start)): ?>
                <p class="mb-1"><strong>🕘 Special Request Start:</strong> <?= $dayActivity->special_request_start ?></p>
                <hr class="cardline">
            <?php endif; ?>

            <!-- Special Request End -->
            <?php if (!empty($dayActivity->special_request_end)): ?>
                <p class="mb-1"><strong>🕔 Special Request End:</strong> <?= $dayActivity->special_request_end ?></p>
                <hr class="cardline">
            <?php endif; ?>

            <!-- Special Request Purpose -->
            <?php if (!empty($dayActivity->special_request_prupose)): ?>
                <p class="mb-1"><strong>📝 Special Request Purpose:</strong> <?= $dayActivity->special_request_prupose ?></p>
                <hr class="cardline">
            <?php endif; ?>

            <!-- Special Request Approve Status -->
            <?php if (!empty($dayActivity->special_request_approve_status)): ?>
                <p class="mb-1"><strong>✅ Special Request Approve Status:</strong> <?= $dayActivity->special_request_approve_status ?></p>
                <hr class="cardline">
            <?php endif; ?>

            <!-- Special Request Approve By -->
            <?php if (!empty($dayActivity->special_request_approve_by)): ?>
                <p class="mb-1"><strong>🧑‍💼 Special Request Approve By:</strong> <?= $dayActivity->special_request_approve_by ?></p>
                <hr class="cardline">
            <?php endif; ?>

            <!-- Special Request Approve Date -->
            <?php if (!empty($dayActivity->special_request_approve_date)): ?>
                <p class="mb-1"><strong>📆 Special Request Approve Date:</strong> <?= $dayActivity->special_request_approve_date ?></p>
                <hr class="cardline">
            <?php endif; ?>

            <!-- Special Request Approve Admin Remarks -->
            <?php if (!empty($dayActivity->special_request_approve_admin_remarks)): ?>
                <p class="mb-1"><strong>🗨️ Special Request Approve Admin Remarks:</strong> <?= $dayActivity->special_request_approve_admin_remarks ?></p>
                <hr class="cardline">
            <?php endif; ?>

            <!-- Special Request Create Time -->
            <?php if (!empty($dayActivity->special_request_for_create_time)): ?>
                <p class="mb-1"><strong>📅 Special Request Create Time:</strong> <?= $dayActivity->special_request_for_create_time ?></p>
                <hr class="cardline">
            <?php endif; ?>

            <!-- Request Pending Task Planning -->
            <?php if (!empty($dayActivity->request_pending_task_planning)): ?>
                <p class="mb-1"><strong>📋 Request Pending Task Planning:</strong> <?= $dayActivity->request_pending_task_planning ?></p>
                <hr class="cardline">
            <?php endif; ?>

            <!-- Request Pending Task Planning Request Type -->
            <?php if (!empty($dayActivity->request_pending_task_planning_request_type)): ?>
                <p class="mb-1"><strong>📑 Request Pending Task Planning Request Type:</strong> <?= $dayActivity->request_pending_task_planning_request_type ?></p>
                <hr class="cardline">
            <?php endif; ?>

            <!-- Request Pending Task Planning Task Count -->
            <?php if (!empty($dayActivity->request_pending_task_planning_task_count)): ?>
                <p class="mb-1"><strong>🔢 Request Pending Task Planning Task Count:</strong> <?= $dayActivity->request_pending_task_planning_task_count ?></p>
                <hr class="cardline">
            <?php endif; ?>

            <!-- Request Pending Task Planning User Request Remarks -->
            <?php if (!empty($dayActivity->request_pending_task_planning_user_request_remarks)): ?>
                <p class="mb-1"><strong>🗣️ Request Pending Task Planning User Request Remarks:</strong> <?= $dayActivity->request_pending_task_planning_user_request_remarks ?></p>
                <hr class="cardline">
            <?php endif; ?>

            <!-- Request Pending Task Planning Approved Status -->
            <?php if (isset($dayActivity->request_pending_task_planning_approved_status)): ?>
                <p class="mb-1"><strong>✅ Request Pending Task Planning Approved Status:</strong>
                    <?= $dayActivity->request_pending_task_planning_approved_status == 1 ? "<span class='bg-success p-1'>Approved</span>" : "<span class='bg-warning p-1'>Pending</span>" ?>
                </p>
                <hr class="cardline">
            <?php endif; ?>

            <!-- Request Pending Task Planning Approved By -->
            <?php if (!empty($dayActivity->request_pending_task_planning_approved_by)): ?>
                <p class="mb-1"><strong>👤 Request Pending Task Planning Approved By:</strong> <?= $dayActivity->request_pending_task_planning_approved_by ?></p>
                <hr class="cardline">
            <?php endif; ?>

            <!-- Request Pending Task Planning Approve Date -->
            <?php if (!empty($dayActivity->request_pending_task_planning_approve_date)): ?>
                <p class="mb-1"><strong>📅 Request Pending Task Planning Approve Date:</strong> <?= $dayActivity->request_pending_task_planning_approve_date ?></p>
                <hr class="cardline">
            <?php endif; ?>

            <!-- Request Pending Task Planning Admin Remarks -->
            <?php if (!empty($dayActivity->request_pending_task_planning_admin_remarks)): ?>
                <p class="mb-1"><strong>🗨️ Request Pending Task Planning Admin Remarks:</strong> <?= $dayActivity->request_pending_task_planning_admin_remarks ?></p>
                <hr class="cardline">
            <?php endif; ?>

            <!-- Request Pending Task Planning Create Time -->
            <?php if (!empty($dayActivity->request_pending_task_planning_create_time)): ?>
                <p class="mb-1"><strong>🕓 Request Pending Task Planning Create Time:</strong> <?= $dayActivity->request_pending_task_planning_create_time ?></p>
                <hr class="cardline">
            <?php endif; ?>

            <!-- Pending Meetings Delete Request -->
            <?php if (!empty($dayActivity->pending_meetings_request)): ?>
                <p class="mb-1"><strong>📌 Pending Meetings Delete Request:</strong> <?= $dayActivity->pending_meetings_request ?></p>
                <hr class="cardline">
            <?php endif; ?>

            <!-- Pending Meetings Request Task IDs -->
            <?php if (!empty($dayActivity->pending_meetings_request_task_ids)): ?>
                <p class="mb-1"><strong>🆔 Pending Meetings Request Task IDs:</strong> <?= $dayActivity->pending_meetings_request_task_ids ?></p>
                <hr class="cardline">
            <?php endif; ?>

            <!-- Pending Meetings Request Task Count -->
            <?php if (!empty($dayActivity->pending_meetings_request_task_count)): ?>
                <p class="mb-1"><strong>🔢 Pending Meetings Request Task Count:</strong> <?= $dayActivity->pending_meetings_request_task_count ?></p>
                <hr class="cardline">
            <?php endif; ?>

            <!-- Pending Meetings Request User Request Remarks -->
            <?php if (!empty($dayActivity->pending_meetings_request_user_request_remarks)): ?>
                <p class="mb-1"><strong>🗣️ Pending Meetings Request User Request Remarks:</strong> <?= $dayActivity->pending_meetings_request_user_request_remarks ?></p>
                <hr class="cardline">
            <?php endif; ?>

            <!-- Pending Meetings Request Approved Status -->
            <?php if (!empty($dayActivity->pending_meetings_request_approved_status)): ?>
                <p class="mb-1"><strong>✅ Pending Meetings Request Approved Status:</strong> <?= $dayActivity->pending_meetings_request_approved_status ?></p>
                <hr class="cardline">
            <?php endif; ?>

            <!-- Pending Meetings Request Approved By -->
            <?php if (!empty($dayActivity->pending_meetings_request_approved_by)): ?>
                <p class="mb-1"><strong>👤 Pending Meetings Request Approved By:</strong> <?= $dayActivity->pending_meetings_request_approved_by ?></p>
                <hr class="cardline">
            <?php endif; ?>

            <!-- Pending Meetings Request Approve Date -->
            <?php if (!empty($dayActivity->pending_meetings_request_approve_date)): ?>
                <p class="mb-1"><strong>📅 Pending Meetings Request Approve Date:</strong> <?= $dayActivity->pending_meetings_request_approve_date ?></p>
                <hr class="cardline">
            <?php endif; ?>

            <!-- Pending Meetings Request Admin Remarks -->
            <?php if (!empty($dayActivity->pending_meetings_request_admin_remarks)): ?>
                <p class="mb-1"><strong>🗨️ Pending Meetings Request Admin Remarks:</strong> <?= $dayActivity->pending_meetings_request_admin_remarks ?></p>
                <hr class="cardline">
            <?php endif; ?>

            <!-- Pending Meetings Request Create Time -->
            <?php if (!empty($dayActivity->pending_meetings_request_create_time)): ?>
                <p class="mb-1"><strong>🕓 Pending Meetings Request Create Time:</strong> <?= $dayActivity->pending_meetings_request_create_time ?></p>
                <hr class="cardline">
            <?php endif; ?>

            <!-- Total Complete Task -->
            <?php if (!empty($dayActivity->total_complete_task)): ?>
                <p class="mb-1"><strong>✅ Total Complete Task:</strong> <?= $dayActivity->total_complete_task ?></p>
                <hr class="cardline">
            <?php endif; ?>

            <!-- Total Pending Task -->
            <?php if (!empty($dayActivity->total_pending_task)): ?>
                <p class="mb-1"><strong>🕗 Total Pending Task:</strong> <?= $dayActivity->total_pending_task ?></p>
                <hr class="cardline">
            <?php endif; ?>












                          <?php $reminderComments =  $this->Menu_model->GetDayReminderCommets($targetuid,$sdate,'Day Start');?>
                          <p class="mb-1 text-center"><strong>🔔 Reminder Comment:</strong> </p>
                          <hr style="width: 50%;" class="<?=$hrAClass?>">
                          <span id="reminder_remarks_<?=$targetuid?>">
                            <?php  if(sizeof($reminderComments) > 0){ ?>
                            <?php foreach($reminderComments as $reminderComment): ?>
                            - 📝 <?=$reminderComment->reminder_remarks.' — 👤 '.$reminderComment->reminderby_user.' ⏰ Time: '.$reminderComment->created_at; ?> 
                            <hr>
                            <?php endforeach; ?>
                            <?php } ?>
                          </span>
                          <hr class="cardline <?=$hrAClass?>">
                        </div>
                      </div>
                    </div>
                    <?php 
                      $j++; 
                      endforeach;
                       ?>      
                  </div>
                </div>
              </div>
            </div>
            <!-- /.card-body -->
          </div>
          <!-- Button trigger modal -->
          <!-- Modal -->
          <div class="modal fade" id="commentModalModalCenter" tabindex="-1" role="dialog" aria-labelledby="commentModalModalCenterTitle" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" role="document">
              <div class="modal-content" style="background: linear-gradient(to right,rgb(255, 223, 246),rgb(255, 255, 255)); border-radius: 14px; box-shadow: 0 4px 12px rgba(0,0,0,0.1); padding: 20px;">
                <div class="modal-header">
                  <h5 class="modal-title" id="commentModalLabel">📝 Add Comment</h5>
                  <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">&times;</span>
                  </button>
                </div>
                <div class="modal-body">
                  <form id="commentForm">
                    <input type="hidden" name="task_id" id="modal_task_id">
                    <div class="mb-3">
                      <label for="commentText" class="form-label">Comment</label>
                      <textarea class="form-control" id="commentText" name="comment" rows="4" placeholder="Write your comment here..."></textarea>
                    </div>
                    <center><button type="submit" class="btn btn-primary w-50">Submit Comment</button></center>
                  </form>
                </div>
              </div>
            </div>
          </div>
          <!-- /.card -->





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
      $(document).ready(function () {
        $('#rm_filter').on('change', function () {
          var selectedText = $(this).find('option:selected').text().toLowerCase();
      
          if (selectedText.includes('team wise')) {
            $('#rm_input').val('Team Wise Input');
          } else if (selectedText.includes('user wise')) {
            $('#rm_input').val('User Wise Input');
          } else {
            $('#rm_input').val('');
          }
        });
      
        // Trigger once on page load
        $('#rm_filter').trigger('change');
      });
      
      
      
      
      $(document).ready(function() {
      $('.openCommentModal').click(function() {
        var taskId = $(this).data('task-id');
        $('#modal_task_id').val(taskId);
        $('#commentModalModalCenter').modal('show');
      });
      
        $('#commentForm').submit(function(e) {
          e.preventDefault();
          var taskId = $('#modal_task_id').val();
          var comment = $('#commentText').val();
          $('#commentModalModalCenter').modal('hide');
          $.ajax({
              url:'<?=base_url();?>Menu/AddDayStartCommentOnTBLTask',
              method: 'post',
              data: {taskId: taskId,comment: comment,date:'<?=$sdate?>'},
              success: function(result){
                  // $("#reminder_remarks_"+taskId).text(comment);
      
                  $.ajax({
                    url:'<?=base_url();?>Menu/GetDayStartReminderComments',
                    method: 'post',
                    data: {taskId: taskId,date:'<?=$sdate?>',rtype:'Day Start'},
                    success: function(result){
                        $("#reminder_remarks_"+taskId).text('');
                        $("#reminder_remarks_"+taskId).html('');
                        $("#reminder_remarks_"+taskId).html(result);
                    }
                });
      
              }
          });
      
          $(this).trigger("reset");
        });
      });











      $(document).ready(function () {

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
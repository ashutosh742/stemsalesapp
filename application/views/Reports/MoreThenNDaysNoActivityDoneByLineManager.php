<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>More Then N Days No Activity Done | STEM APP | WebAPP</title>
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
      .card-bg-1 { background-color: #FFD700!important; } /* Gold */
      .card-bg-2 { background-color: #C0C0C0!important; } /* Silver */
      .card-bg-3 { background-color: #CD7F32!important; } /* Bronze */
      .card-bg-4 { background-color: #4682B4!important; } /* Steel Blue */
      .card-bg-5 { background-color: #556B2F!important; } /* Dark Olive Green */
      .card-bg-6 { background-color: #8B0000!important; } /* Dark Red */
      .card-bg-7 { background-color: #4B0082!important; } /* Indigo */
      .card-bg-8 { background-color: #2E8B57!important; } /* Sea Green */
      .card-bg-9 { background-color: #FF6347!important; } /* Tomato */
      .card-bg-10 { background-color: #9932CC!important; } /* Dark Orchid */
      .card-bg-11 { background-color: #8B4513!important; } /* Saddle Brown */
      .card-bg-12 { background-color: #20B2AA!important; } /* Light Sea Green */
      .card-bg-13 { background-color: #FFDAB9!important; } /* Peach Puff */
      .card-bg-14 { background-color: #6A5ACD!important; } /* Slate Blue */
      .card-bg-15 { background-color: #FF69B4!important; } /* Hot Pink */
      .card-bg-16 { background-color: #00BFFF!important; } /* Deep Sky Blue */
      tr{
      font-weight: bold;
      }
      .beautiful-textarea {
      padding: 10px;
      border: 2px solid #ccc;
      border-radius: 10px;
      box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
      font-family: Arial, sans-serif;
      font-size: 16px;
      resize: none; /* Prevents resizing */
      outline: none; /* Removes the default focus outline */
      transition: border-color 0.3s, box-shadow 0.3s;
      }
      .beautiful-textarea:focus {
      border-color: #66afe9;
      box-shadow: 0 4px 12px rgba(102, 175, 233, 0.4);
      }
      .content-wrapper{
      background-image: linear-gradient(248.35deg, #86cdff -11.3%, #f4f4fe 16.44%, #ffffff 28.3%, #ffffff 72.47%, #ebeafe 89.69%, #bec6f7 101.94%);
      }
      .main-content-wrapper{
      background-image: linear-gradient(248.35deg, #86cdff -11.3%, #f4f4fe 16.44%, #ffffff 28.3%, #ffffff 72.47%, #ebeafe 89.69%, #bec6f7 101.94%);
      }

      .card {
      margin-bottom: 20px;
      flex: 1;
      display: flex;
      flex-direction: column;
      }
      .card-body {
      flex: 1;
      display: flex;
      flex-direction: column;
      justify-content: center; /* Center content vertically */
      }

      .card.text-center{
      align-items: center;
      justify-content: center;
      display: flex;
      }
      .card-group {
      margin-bottom: 20px;
      }
      .card-group-title {
      width: 100%;
      text-align: center;
      margin-bottom: 10px;
      font-weight: bold;
      font-size: 1.2em;
      }
      .page-header {
    color: red;
}
    </style>
  </head>
  <body class="hold-transition sidebar-mini layout-fixed">
    <div class="wrapper">
      <!-- Preloader -->
      <!-- Navbar -->
      <?php $this->load->view($dep_name.'/nav.php');?>
      <!-- /.navbar -->
      <!-- Content Wrapper. Contains page content -->
      <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <div class="content-header">
          <div class="container-fluid">
            <div class="row mb-2">
              <hr>
              <div class="text-right-data text-center" style="background: linear-gradient(to right, #b2d6ff, #ffffff); border-radius: 12px; box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1); padding: 20px; width: 80%;">
                <?php 
                  $clusterPstDatas  = $this->Menu_model->GetAdminActiveTeam($user['user_id']);
                  $taskActions      = $this->Menu_model->get_action();
                  $userRoles        = $this->Menu_model->GetActiveDepartmentList($uid);
                  ?>
                <div class="col text-center formcenterData">
                  <div>
                    <hr>
                    <form action="<?=base_url()?>Reports/MoreThenNDaysNoActivityDoneByLineManager" method="post" 
                      class="mt-3" 
                      style="background:#ffffff; padding:20px; border-radius:12px; box-shadow:0 4px 12px rgba(0,0,0,0.1);">
                      <h3 style="color:#ff009b; margin-bottom:20px; font-weight:700;">
                        🚫 More Than N Days No Activity Done By Line Manager
                      </h3>
                      <p class="premium-color-2" style="color: #ff0000; font-size:15px; line-height:22px; margin-top:6px;">
                        ⏳ This section highlights tasks where no activity has been recorded for more than the specified number of days, grouped by role. It helps identify delays, missing follow-ups, and areas needing immediate action.
                      </p>
                      <hr>
                      <div class="row mb-4" style="display:flex; gap:20px;">
                      
                        <!-- Admin -->
                        <div class="col" style="flex:1;">
                          <label for="admin_id_filter" style="font-weight:600;color: #ff009b;">🧑‍💼 Select Admin</label>
                          <select id="admin_id_filter" name="admin_id_filter" class="form-control"
                            style="border-radius:8px; padding:8px;">
                            <?php if($user['type_id'] == 2){ ?> 
                            <option value="all">All</option>
                            <?php } ?>
                            <?php foreach ($clusterPstDatas as $clusterPstData) { ?>
                            <option value="<?= $clusterPstData->user_id; ?>" 
                              <?= ($clusterPstData->user_id == $admin_id_filter) ? 'selected' : ''; ?>>
                              <?= $clusterPstData->name; ?>
                            </option>
                            <?php } ?>
                          </select>
                        </div>
                        <!-- RM -->
                        <div class="col" style="flex:1;">
                          <?php $getTeamsDatas = $this->Menu_model->GetClusterAndPSTActiveTeam($admin_id_filter); ?>
                          <label for="rm_filter" style="font-weight:600;color: #ff009b;">👨‍💼 Select RM</label>
                          <select id="rm_filter" name="rm_filter" class="form-control"
                            style="border-radius:8px; padding:8px;">
                            <?php if($user['type_id'] !== 3){ ?> 
                            <option value="all" <?= ($rm_filter == 'all') ? 'selected' : ''; ?>>All</option>
                            <?php } ?>
                            <?php foreach ($getTeamsDatas as $getTeamsData) { ?>
                            <option value="<?= $getTeamsData->user_id; ?>" 
                              <?= ($getTeamsData->user_id == $rm_filter) ? 'selected' : ''; ?>>
                              <?= $getTeamsData->name; ?>
                            </option>
                            <?php }?>
                          </select>
                        </div>
                        <!-- Button -->
                        <div class="col text-center" style="flex:0.5;">
                          <button type="submit" 
                            class="btn btn-primary"
                            style="margin-top:33px; width:100%; background:#ff009b; border:none; 
                            border-radius:8px; padding:8px; font-weight:600;">
                          🔍 Filter
                          </button>
                        </div>
                      </div>
                    </form>
                    <hr>
                  </div>
                </div>
              </div>
              <hr>
            </div>
          </div>
        </div>
        <!-- Main content -->
        <hr>
        <section class="content">
          <div class="container-fluid main-content-wrapper">


                      <?php 
                      $total_funnel                   = $totalFunnnels['total_funnel'];
                      $total_funnel_by_user           = $totalFunnnels['total_funnel_by_user'];

                      ?>




<?php /*
<div class="card-section" style="background: linear-gradient(to right, #ede7f6, #ffffff); border-radius: 12px; box-shadow: 0 4px 8px rgba(0,0,0,0.1); padding: 20px;">
    <div class="text-center mb-3" style="padding: 20px;">
        <h5>
            <i>📑 Total BD Task Pending By Company Overview by Duration</i><br>
            <small>
                📊 This data outlines various funnel categories representing client segmentation strategies, including total funnel size, targeted client types such as 🏢 key clients, 📈 upsell opportunities, 💸 top spenders, and 📞 those prioritized for outreach—each aimed at enhancing business focus and revenue growth. 🚀
            </small>
        </h5>
    </div>
    <hr>
    <div class="row">
        <?php  
        $descriptions = [
            'total' => 'Total number of companies in the funnel.',
            'task_not_done_in_0_to_8_days' => 'Tasks pending for 0-8 days.',
            'task_not_done_in_9_to_15_days' => 'Tasks pending for 9-15 days.',
            'task_not_done_in_16_to_30_days' => 'Tasks pending for 16-30 days.',
            'task_not_done_in_31_to_50_days' => 'Tasks pending for 31-50 days.',
            'task_not_done_in_more_than_50_days' => 'Tasks pending for more than 50 days.',
            'no_activity_till_date' => 'Clients with no activity recorded till date.'
        ];

        foreach ($total_funnel as $tasktypeData) {
            foreach ($tasktypeData as $key => $value) {
                if (in_array($key, ['user_id', 'username'])) continue;

                // Generate readable background color
                $r = rand(200, 255);
                $g = rand(200, 255);
                $b = rand(200, 255);
                $backgroundColor = "rgba($r, $g, $b, 0.3)";

                // Generate contrasting text color
                $hue = rand(0, 360);
                $saturation = rand(60, 100);
                $lightness = rand(20, 40);
                $textColor = "hsl($hue, $saturation%, $lightness%)";

                // Format heading nicely
                $heading = ucwords(str_replace('_', ' ', $key));
                $description = $descriptions[$key] ?? 'Task metric description not available.';
        ?>
                <div class="col-md-3 col-sm-6 mb-3">
                    <div class="card text-center shadow" style="background-color: <?= $backgroundColor ?>;">
                        <div class="card-body" style="color: <?= $textColor ?>;">
                            <h5 class="card-title"><b><?= $heading ?></b></h5>
                            <small><?= $description ?></small>
                            <hr>
                            <?php 
                            if($key == 'total'){
                              $links = 'FunnelDetails';
                              $allf = "";
                            }else{
                              $links = 'MoreThenNDaysNoActivityDoneByLineManagerDetails';
                              $allf = "/all";
                            }
                            ?>
                            <a target="_BLANK" class="card-count-heading-new" style="color: <?= $textColor ?>;" href="<?= base_url() ?>Reports/<?= $links ?>/<?= $key ?>/<?= $uid ?><?= $allf ?>">
                                <h3 class="card-text"><?= $value ?></h3>
                            </a>
                        </div>
                    </div>
                </div>
        <?php
            }
        }
        ?>
    </div>
</div>

*/ ?>





            <hr>
              <div class="card" style="background-image: linear-gradient(248.35deg, #86cdff -11.3%, #f4f4fe 16.44%, #ffffff 28.3%, #ffffff 72.47%, #ebeafe 89.69%, #bec6f7 101.94%);">
              <div class="page-header" style="background: linear-gradient(to right, #ffffff, #ffffff); border-radius: 12px; box-shadow: 0 4px 8px rgba(0,0,0,0.1); padding: 20px;">
                <div class="text-center">
                    <h5>
                    <i>📋 Line Manager Task Pending Overview by Duration</i> <br>
                    <small>
                    ⏳ This table displays the pending tasks assigned to each Business Developer (BD) across different cluster zones, categorized by the number of days they have been pending: 0–8 Days Pending for recent tasks needing attention soon, 9–15 Days Pending for slightly delayed tasks requiring follow-up, 16–30 Days Pending for medium-term pending tasks that may impact performance, 31–50 Days Pending for long-pending tasks needing urgent action, 📆 More Than 50 Days Pending for critically overdue tasks requiring immediate intervention, and 🚫 No Activity Till Date for tasks with no recorded activity since assignment, highlighting unaddressed responsibilities.
                    </small>
                  </h5>
                </div>
                <div class="table-responsive text-nowrap">
                  <table id="example1" class="table table-striped table-bordered" cellspacing="0" width="100%">
                    <thead class="thead-dark">
                      <tr>
                          <th>🔢 #</th>
                          <th>📝 BD Name</th>
                          <!-- <th>📍 Cluster Zone</th> -->
                          <th>📊 Total Tasks</th>
                          <th>⏳ 0-8 Days Pending</th>
                          <th>⏱️ 9-15 Days Pending</th>
                          <th>⏳ 16-30 Days Pending</th>
                          <th>⏲️ 31-50 Days Pending</th>
                          <th>📆 More Than 50 Days Pending</th>
                          <th>🚫 No Activity Till Date</th>
                      </tr>
                    </thead>
                    <tbody>
                      <?php
                        $i = 1;
                        foreach ($total_funnel_by_user as $row) {
                          $meeting_user_id = $row->user_id;
                        
                          $r = rand(150, 255);
                          $g = rand(150, 255);
                          $b = rand(150, 255);
                          $backgroundColor = "rgba($r, $g, $b,.2)";
                          $hue = rand(0, 360); // Full color wheel
                          $saturation = rand(60, 100); // High saturation for rich colors
                          $lightness = rand(30, 45); // Low lightness for a deeper tone
                          $backgroundColorNew = "hsl($hue, $saturation%, $lightness%)";
                          
                          ?>
                      <tr style="background-color: <?php echo $backgroundColor; ?>;color:<?=$backgroundColorNew;?>!important">
                        <td><?= $i; ?></td>
                        <td class="text-capitalize"><?php echo htmlspecialchars($row->user_name); ?></td>
                        <!-- <td class="text-capitalize"><?php echo htmlspecialchars($row->user_cluster_zone); ?></td> -->

                        <td>
                          <a target="_BLANK" class="card-count-heading-new" style="color:<?=$backgroundColorNew;?>!important" href="<?=base_url()?>Reports/FunnelDetails/total/<?=$meeting_user_id?>/userwise">
                          <?= $row->total; ?>
                          </a>
                        </td>
                        <td>
                          <a target="_BLANK" class="card-count-heading-new" style="color:<?=$backgroundColorNew;?>!important" href="<?=base_url()?>Reports/MoreThenNDaysNoActivityDoneByLineManagerDetails/task_not_done_in_0_to_8_days/<?=$meeting_user_id?>/userwise">
                          <?= $row->task_not_done_in_0_to_8_days; ?>
                          </a>
                        </td>
                        <td>
                          <a target="_BLANK" class="card-count-heading-new" style="color:<?=$backgroundColorNew;?>!important" href="<?=base_url()?>Reports/MoreThenNDaysNoActivityDoneByLineManagerDetails/task_not_done_in_9_to_15_days/<?=$meeting_user_id?>/userwise">
                          <?= $row->task_not_done_in_9_to_15_days; ?>
                          </a>
                        </td>
                        <td>
                          <a target="_BLANK" class="card-count-heading-new" style="color:<?=$backgroundColorNew;?>!important" href="<?=base_url()?>Reports/MoreThenNDaysNoActivityDoneByLineManagerDetails/task_not_done_in_16_to_30_days/<?=$meeting_user_id?>/userwise">
                          <?= $row->task_not_done_in_16_to_30_days; ?>
                          </a>
                        </td>
                        <td>
                          <a target="_BLANK" class="card-count-heading-new" style="color:<?=$backgroundColorNew;?>!important" href="<?=base_url()?>Reports/MoreThenNDaysNoActivityDoneByLineManagerDetails/task_not_done_in_31_to_50_days/<?=$meeting_user_id?>/userwise">
                          <?= $row->task_not_done_in_31_to_50_days; ?>
                          </a>
                        </td>
                        <td>
                          <a target="_BLANK" class="card-count-heading-new" style="color:<?=$backgroundColorNew;?>!important" href="<?=base_url()?>Reports/MoreThenNDaysNoActivityDoneByLineManagerDetails/task_not_done_in_more_than_50_days/<?=$meeting_user_id?>/userwise">
                          <?= $row->task_not_done_in_more_than_50_days; ?>
                          </a>
                        </td>
                        <td>
                          <a target="_BLANK" class="card-count-heading-new" style="color:<?=$backgroundColorNew;?>!important" href="<?=base_url()?>Reports/MoreThenNDaysNoActivityDoneByLineManagerDetails/no_activity_till_date/<?=$meeting_user_id?>/userwise">
                          <?= $row->no_activity_till_date; ?>
                          </a>
                        </td>
            
                  
                      <?php $i++; } ?>
                    </tbody>
                  </table>
                </div>
              </div>
              </div>    






              <?php $totalNotDoneinZeroToEightDays    = $totalFunnnels['totalNotDoneinZeroToEightDays'];

            //   dd($totalNotDoneinZeroToEightDays);
               ?>
               <hr>
              <div class="card" style="background-image: linear-gradient(248.35deg, #86cdff -11.3%, #f4f4fe 16.44%, #ffffff 28.3%, #ffffff 72.47%, #ebeafe 89.69%, #bec6f7 101.94%);">
              <div class="page-header" style="background: linear-gradient(to right, #ffffff, #ffffff); border-radius: 12px; box-shadow: 0 4px 8px rgba(0,0,0,0.1); padding: 20px;">
                <div class="text-center">
                   <h5>
                    <i>📋 0–8 Days Pending for recent tasks needing attention soon</i> <br>
                    <small>
                    ⏳ This table displays the pending tasks assigned to each Business Developer (BD) across different status.
                    </small>
                  </h5>
                </div>
                <div class="table-responsive text-nowrap">
                  <table id="example2" class="table table-striped table-bordered" cellspacing="0" width="100%">
                    <thead class="thead-dark">
                     <tr>
                      <th>🆔</th>
                      <th>🧑‍💼 BD Name</th>
                      <!-- <th>🗺️ User Cluster Zone</th> -->
                      <th>📊 Total Pending</th>
                      <th>📂 Open</th>
                      <th>📌 Open RPEM</th>
                      <th>📞 Reachout</th>
                      <th>🤔 Tentative</th>
                      <th>🕗 Will Do Later</th>
                      <th>🚫 Not Interested</th>
                      <th>👍 Positive</th>
                      <th>🌟 Very Positive</th>
                      <th>📲 TTD Reachout</th>
                      <th>🔕 WNO Reachout</th>
                      <th>😊 Positive NAP</th>
                      <th>🌈 Very Positive NAP</th>
                      <th>✅ Closure</th>
                      <th>🧾 On Boarded</th>
                    </tr>
                    </thead>
                    <tbody>
                      <?php
                        $i = 1;
                        foreach ($totalNotDoneinZeroToEightDays as $row) {
                          $meeting_user_id = $row->user_id;
                        
                          $r = rand(150, 255);
                          $g = rand(150, 255);
                          $b = rand(150, 255);
                          $backgroundColor = "rgba($r, $g, $b,.2)";
                          $hue = rand(0, 360); // Full color wheel
                          $saturation = rand(60, 100); // High saturation for rich colors
                          $lightness = rand(30, 45); // Low lightness for a deeper tone
                          $backgroundColorNew = "hsl($hue, $saturation%, $lightness%)";
                          
                          ?>
                      <tr style="background-color: <?php echo $backgroundColor; ?>;color:<?=$backgroundColorNew;?>!important">
                        <td><?= $i; ?></td>
                        <td class="text-capitalize"><?php echo htmlspecialchars($row->user_name); ?></td>
                        <!-- <td class="text-capitalize"><?php echo htmlspecialchars($row->user_cluster_zone); ?></td> -->

                        <td>
                          <a target="_BLANK" class="card-count-heading-new" style="color:<?=$backgroundColorNew;?>!important" href="<?=base_url()?>Reports/MoreThenNDaysNoActivityDoneByLineManagerDetails/task_not_done_in_0_to_8_days/<?=$meeting_user_id?>/userwise/">
                          <?= $row->total; ?>
                          </a>
                        </td>
                        <td>
                          <a target="_BLANK" class="card-count-heading-new" style="color:<?=$backgroundColorNew;?>!important" href="<?=base_url()?>Reports/MoreThenNDaysNoActivityDoneByLineManagerDetails/task_not_done_in_0_to_8_days/<?=$meeting_user_id?>/userwise/open">
                          <?= $row->open; ?>
                          </a>
                        </td>
                          <td>
                          <a target="_BLANK" class="card-count-heading-new" style="color:<?=$backgroundColorNew;?>!important" href="<?=base_url()?>Reports/MoreThenNDaysNoActivityDoneByLineManagerDetails/task_not_done_in_0_to_8_days/<?=$meeting_user_id?>/userwise/open_rpem">
                          <?= $row->open_rpem; ?>
                          </a>
                        </td>
                        <td>
                          <a target="_BLANK" class="card-count-heading-new" style="color:<?=$backgroundColorNew;?>!important" href="<?=base_url()?>Reports/MoreThenNDaysNoActivityDoneByLineManagerDetails/task_not_done_in_0_to_8_days/<?=$meeting_user_id?>/userwise/reachout">
                          <?= $row->reachout; ?>
                          </a>
                        </td>
                        <td>
                          <a target="_BLANK" class="card-count-heading-new" style="color:<?=$backgroundColorNew;?>!important" href="<?=base_url()?>Reports/MoreThenNDaysNoActivityDoneByLineManagerDetails/task_not_done_in_0_to_8_days/<?=$meeting_user_id?>/userwise/tentative">
                          <?= $row->tentative; ?>
                          </a>
                        </td>
                        <td>
                          <a target="_BLANK" class="card-count-heading-new" style="color:<?=$backgroundColorNew;?>!important" href="<?=base_url()?>Reports/MoreThenNDaysNoActivityDoneByLineManagerDetails/task_not_done_in_0_to_8_days/<?=$meeting_user_id?>/userwise/will_do_later">
                          <?= $row->will_do_later; ?>
                          </a>
                        </td>
                        <td>
                          <a target="_BLANK" class="card-count-heading-new" style="color:<?=$backgroundColorNew;?>!important" href="<?=base_url()?>Reports/MoreThenNDaysNoActivityDoneByLineManagerDetails/task_not_done_in_0_to_8_days/<?=$meeting_user_id?>/userwise/not_interested">
                          <?= $row->not_interested; ?>
                          </a>
                        </td>
                        <td>
                          <a target="_BLANK" class="card-count-heading-new" style="color:<?=$backgroundColorNew;?>!important" href="<?=base_url()?>Reports/MoreThenNDaysNoActivityDoneByLineManagerDetails/task_not_done_in_0_to_8_days/<?=$meeting_user_id?>/userwise/positive">
                          <?= $row->positive; ?>
                          </a>
                        </td>
                        <td>
                          <a target="_BLANK" class="card-count-heading-new" style="color:<?=$backgroundColorNew;?>!important" href="<?=base_url()?>Reports/MoreThenNDaysNoActivityDoneByLineManagerDetails/task_not_done_in_0_to_8_days/<?=$meeting_user_id?>/userwise/very_positive">
                          <?= $row->very_positive; ?>
                          </a>
                        </td>
                        <td>
                          <a target="_BLANK" class="card-count-heading-new" style="color:<?=$backgroundColorNew;?>!important" href="<?=base_url()?>Reports/MoreThenNDaysNoActivityDoneByLineManagerDetails/task_not_done_in_0_to_8_days/<?=$meeting_user_id?>/userwise/ttd_reachout">
                          <?= $row->ttd_reachout; ?>
                          </a>
                        </td>
                        <td>
                          <a target="_BLANK" class="card-count-heading-new" style="color:<?=$backgroundColorNew;?>!important" href="<?=base_url()?>Reports/MoreThenNDaysNoActivityDoneByLineManagerDetails/task_not_done_in_0_to_8_days/<?=$meeting_user_id?>/userwise/wno_reachout">
                          <?= $row->wno_reachout; ?>
                          </a>
                        </td>
                        <td>
                          <a target="_BLANK" class="card-count-heading-new" style="color:<?=$backgroundColorNew;?>!important" href="<?=base_url()?>Reports/MoreThenNDaysNoActivityDoneByLineManagerDetails/task_not_done_in_0_to_8_days/<?=$meeting_user_id?>/userwise/positive_nap">
                          <?= $row->positive_nap; ?>
                          </a>
                        </td>
                        <td>
                          <a target="_BLANK" class="card-count-heading-new" style="color:<?=$backgroundColorNew;?>!important" href="<?=base_url()?>Reports/MoreThenNDaysNoActivityDoneByLineManagerDetails/task_not_done_in_0_to_8_days/<?=$meeting_user_id?>/userwise/very_positive_nap">
                          <?= $row->very_positive_nap; ?>
                          </a>
                        </td>
                        <td>
                          <a target="_BLANK" class="card-count-heading-new" style="color:<?=$backgroundColorNew;?>!important" href="<?=base_url()?>Reports/MoreThenNDaysNoActivityDoneByLineManagerDetails/task_not_done_in_0_to_8_days/<?=$meeting_user_id?>/userwise/closure">
                          <?= $row->closure; ?>
                          </a>
                        </td>
                        <td>
                          <a target="_BLANK" class="card-count-heading-new" style="color:<?=$backgroundColorNew;?>!important" href="<?=base_url()?>Reports/MoreThenNDaysNoActivityDoneByLineManagerDetails/task_not_done_in_0_to_8_days/<?=$meeting_user_id?>/userwise/on_boarded">
                          <?= $row->on_boarded; ?>
                          </a>
                        </td>
                      <?php $i++; } ?>
                    </tbody>
                  </table>
                </div>
              </div>
              </div>    





              
              <?php $totalNotDoneinNineToFifteenDays    = $totalFunnnels['totalNotDoneinNineToFifteenDays']; ?>
               <hr>
              <div class="card" style="background-image: linear-gradient(248.35deg, #86cdff -11.3%, #f4f4fe 16.44%, #ffffff 28.3%, #ffffff 72.47%, #ebeafe 89.69%, #bec6f7 101.94%);">
              <div class="page-header" style="background: linear-gradient(to right, #ffffff, #ffffff); border-radius: 12px; box-shadow: 0 4px 8px rgba(0,0,0,0.1); padding: 20px;">
                <div class="text-center">
                   <h5>
                    <i>📋 9–15 Days Pending for recent tasks needing attention soon</i> <br>
                    <small>
                    ⏳ This table displays the pending tasks assigned to each Business Developer (BD) across different status.
                    </small>
                  </h5>
                </div>
                <div class="table-responsive text-nowrap">
                  <table id="example3" class="table table-striped table-bordered" cellspacing="0" width="100%">
                    <thead class="thead-dark">
                     <tr>
                      <th>🆔</th>
                      <th>🧑‍💼 BD Name</th>
                      <!-- <th>🗺️ User Cluster Zone</th> -->
                      <th>📊 Total Pending</th>
                      <th>📂 Open</th>
                      <th>📌 Open RPEM</th>
                      <th>📞 Reachout</th>
                      <th>🤔 Tentative</th>
                      <th>🕗 Will Do Later</th>
                      <th>🚫 Not Interested</th>
                      <th>👍 Positive</th>
                      <th>🌟 Very Positive</th>
                      <th>📲 TTD Reachout</th>
                      <th>🔕 WNO Reachout</th>
                      <th>😊 Positive NAP</th>
                      <th>🌈 Very Positive NAP</th>
                      <th>✅ Closure</th>
                      <th>🧾 On Boarded</th>
                    </tr>
                    </thead>
                    <tbody>
                      <?php
                        $i = 1;
                        foreach ($totalNotDoneinNineToFifteenDays as $row) {
                          $meeting_user_id = $row->user_id;
                        
                          $r = rand(150, 255);
                          $g = rand(150, 255);
                          $b = rand(150, 255);
                          $backgroundColor = "rgba($r, $g, $b,.2)";
                          $hue = rand(0, 360); // Full color wheel
                          $saturation = rand(60, 100); // High saturation for rich colors
                          $lightness = rand(30, 45); // Low lightness for a deeper tone
                          $backgroundColorNew = "hsl($hue, $saturation%, $lightness%)";
                          
                          ?>
                      <tr style="background-color: <?php echo $backgroundColor; ?>;color:<?=$backgroundColorNew;?>!important">
                        <td><?= $i; ?></td>
                        <td class="text-capitalize"><?php echo htmlspecialchars($row->user_name); ?></td>
                        <!-- <td class="text-capitalize"><?php echo htmlspecialchars($row->user_cluster_zone); ?></td> -->

                        <td>
                          <a target="_BLANK" class="card-count-heading-new" style="color:<?=$backgroundColorNew;?>!important" href="<?=base_url()?>Reports/MoreThenNDaysNoActivityDoneByLineManagerDetails/task_not_done_in_9_to_15_days/<?=$meeting_user_id?>/userwise/allstatus">
                          <?= $row->total; ?>
                          </a>
                        </td>
                        <td>
                          <a target="_BLANK" class="card-count-heading-new" style="color:<?=$backgroundColorNew;?>!important" href="<?=base_url()?>Reports/MoreThenNDaysNoActivityDoneByLineManagerDetails/task_not_done_in_9_to_15_days/<?=$meeting_user_id?>/userwise/open">
                          <?= $row->open; ?>
                          </a>
                        </td>
                          <td>
                          <a target="_BLANK" class="card-count-heading-new" style="color:<?=$backgroundColorNew;?>!important" href="<?=base_url()?>Reports/MoreThenNDaysNoActivityDoneByLineManagerDetails/task_not_done_in_9_to_15_days/<?=$meeting_user_id?>/userwise/open_rpem">
                          <?= $row->open_rpem; ?>
                          </a>
                        </td>
                        <td>
                          <a target="_BLANK" class="card-count-heading-new" style="color:<?=$backgroundColorNew;?>!important" href="<?=base_url()?>Reports/MoreThenNDaysNoActivityDoneByLineManagerDetails/task_not_done_in_9_to_15_days/<?=$meeting_user_id?>/userwise/reachout">
                          <?= $row->reachout; ?>
                          </a>
                        </td>
                        <td>
                          <a target="_BLANK" class="card-count-heading-new" style="color:<?=$backgroundColorNew;?>!important" href="<?=base_url()?>Reports/MoreThenNDaysNoActivityDoneByLineManagerDetails/task_not_done_in_9_to_15_days/<?=$meeting_user_id?>/userwise/tentative">
                          <?= $row->tentative; ?>
                          </a>
                        </td>
                        <td>
                          <a target="_BLANK" class="card-count-heading-new" style="color:<?=$backgroundColorNew;?>!important" href="<?=base_url()?>Reports/MoreThenNDaysNoActivityDoneByLineManagerDetails/task_not_done_in_9_to_15_days/<?=$meeting_user_id?>/userwise/will_do_later">
                          <?= $row->will_do_later; ?>
                          </a>
                        </td>
                        <td>
                          <a target="_BLANK" class="card-count-heading-new" style="color:<?=$backgroundColorNew;?>!important" href="<?=base_url()?>Reports/MoreThenNDaysNoActivityDoneByLineManagerDetails/task_not_done_in_9_to_15_days/<?=$meeting_user_id?>/userwise/not_interested">
                          <?= $row->not_interested; ?>
                          </a>
                        </td>
                        <td>
                          <a target="_BLANK" class="card-count-heading-new" style="color:<?=$backgroundColorNew;?>!important" href="<?=base_url()?>Reports/MoreThenNDaysNoActivityDoneByLineManagerDetails/task_not_done_in_9_to_15_days/<?=$meeting_user_id?>/userwise/positive">
                          <?= $row->positive; ?>
                          </a>
                        </td>
                        <td>
                          <a target="_BLANK" class="card-count-heading-new" style="color:<?=$backgroundColorNew;?>!important" href="<?=base_url()?>Reports/MoreThenNDaysNoActivityDoneByLineManagerDetails/task_not_done_in_9_to_15_days/<?=$meeting_user_id?>/userwise/very_positive">
                          <?= $row->very_positive; ?>
                          </a>
                        </td>
                        <td>
                          <a target="_BLANK" class="card-count-heading-new" style="color:<?=$backgroundColorNew;?>!important" href="<?=base_url()?>Reports/MoreThenNDaysNoActivityDoneByLineManagerDetails/task_not_done_in_9_to_15_days/<?=$meeting_user_id?>/userwise/ttd_reachout">
                          <?= $row->ttd_reachout; ?>
                          </a>
                        </td>
                        <td>
                          <a target="_BLANK" class="card-count-heading-new" style="color:<?=$backgroundColorNew;?>!important" href="<?=base_url()?>Reports/MoreThenNDaysNoActivityDoneByLineManagerDetails/task_not_done_in_9_to_15_days/<?=$meeting_user_id?>/userwise/wno_reachout">
                          <?= $row->wno_reachout; ?>
                          </a>
                        </td>
                        <td>
                          <a target="_BLANK" class="card-count-heading-new" style="color:<?=$backgroundColorNew;?>!important" href="<?=base_url()?>Reports/MoreThenNDaysNoActivityDoneByLineManagerDetails/task_not_done_in_9_to_15_days/<?=$meeting_user_id?>/userwise/positive_nap">
                          <?= $row->positive_nap; ?>
                          </a>
                        </td>
                        <td>
                          <a target="_BLANK" class="card-count-heading-new" style="color:<?=$backgroundColorNew;?>!important" href="<?=base_url()?>Reports/MoreThenNDaysNoActivityDoneByLineManagerDetails/task_not_done_in_0_to_8_days/<?=$meeting_user_id?>/userwise/very_positive_nap">
                          <?= $row->very_positive_nap; ?>
                          </a>
                        </td>
                        <td>
                          <a target="_BLANK" class="card-count-heading-new" style="color:<?=$backgroundColorNew;?>!important" href="<?=base_url()?>Reports/MoreThenNDaysNoActivityDoneByLineManagerDetails/task_not_done_in_9_to_15_days/<?=$meeting_user_id?>/userwise/closure">
                          <?= $row->closure; ?>
                          </a>
                        </td>
                        <td>
                          <a target="_BLANK" class="card-count-heading-new" style="color:<?=$backgroundColorNew;?>!important" href="<?=base_url()?>Reports/MoreThenNDaysNoActivityDoneByLineManagerDetails/task_not_done_in_9_to_15_days/<?=$meeting_user_id?>/userwise/on_boarded">
                          <?= $row->on_boarded; ?>
                          </a>
                        </td>
                      <?php $i++; } ?>
                    </tbody>
                  </table>
                </div>
              </div>
              </div> 



              <?php $totalNotDoneinsixteenToThirtyDays    = $totalFunnnels['totalNotDoneinsixteenToThirtyDays']; ?>
               <hr>
              <div class="card" style="background-image: linear-gradient(248.35deg, #86cdff -11.3%, #f4f4fe 16.44%, #ffffff 28.3%, #ffffff 72.47%, #ebeafe 89.69%, #bec6f7 101.94%);">
              <div class="page-header" style="background: linear-gradient(to right, #ffffff, #ffffff); border-radius: 12px; box-shadow: 0 4px 8px rgba(0,0,0,0.1); padding: 20px;">
                <div class="text-center">
                   <h5>
                    <i>📋 16–30 Days Pending for recent tasks needing attention soon</i> <br>
                    <small>
                    ⏳ This table displays the pending tasks assigned to each Business Developer (BD) across different status.
                    </small>
                  </h5>
                </div>
                <div class="table-responsive text-nowrap">
                  <table id="example4" class="table table-striped table-bordered" cellspacing="0" width="100%">
                    <thead class="thead-dark">
                     <tr>
                      <th>🆔</th>
                      <th>🧑‍💼 BD Name</th>
                      <!-- <th>🗺️ User Cluster Zone</th> -->
                      <th>📊 Total Pending</th>
                      <th>📂 Open</th>
                      <th>📌 Open RPEM</th>
                      <th>📞 Reachout</th>
                      <th>🤔 Tentative</th>
                      <th>🕗 Will Do Later</th>
                      <th>🚫 Not Interested</th>
                      <th>👍 Positive</th>
                      <th>🌟 Very Positive</th>
                      <th>📲 TTD Reachout</th>
                      <th>🔕 WNO Reachout</th>
                      <th>😊 Positive NAP</th>
                      <th>🌈 Very Positive NAP</th>
                      <th>✅ Closure</th>
                      <th>🧾 On Boarded</th>
                    </tr>
                    </thead>
                    <tbody>
                      <?php
                        $i = 1;
                        foreach ($totalNotDoneinsixteenToThirtyDays as $row) {
                          $meeting_user_id = $row->user_id;
                        
                          $r = rand(150, 255);
                          $g = rand(150, 255);
                          $b = rand(150, 255);
                          $backgroundColor = "rgba($r, $g, $b,.2)";
                          $hue = rand(0, 360); // Full color wheel
                          $saturation = rand(60, 100); // High saturation for rich colors
                          $lightness = rand(30, 45); // Low lightness for a deeper tone
                          $backgroundColorNew = "hsl($hue, $saturation%, $lightness%)";
                          
                          ?>
                      <tr style="background-color: <?php echo $backgroundColor; ?>;color:<?=$backgroundColorNew;?>!important">
                        <td><?= $i; ?></td>
                        <td class="text-capitalize"><?php echo htmlspecialchars($row->user_name); ?></td>
                        <!-- <td class="text-capitalize"><?php echo htmlspecialchars($row->user_cluster_zone); ?></td> -->

                        <td>
                          <a target="_BLANK" class="card-count-heading-new" style="color:<?=$backgroundColorNew;?>!important" href="<?=base_url()?>Reports/MoreThenNDaysNoActivityDoneByLineManagerDetails/task_not_done_in_16_to_30_days/<?=$meeting_user_id?>/userwise/allstatus">
                          <?= $row->total; ?>
                          </a>
                        </td>
                        <td>
                          <a target="_BLANK" class="card-count-heading-new" style="color:<?=$backgroundColorNew;?>!important" href="<?=base_url()?>Reports/MoreThenNDaysNoActivityDoneByLineManagerDetails/task_not_done_in_16_to_30_days/<?=$meeting_user_id?>/userwise/open">
                          <?= $row->open; ?>
                          </a>
                        </td>
                          <td>
                          <a target="_BLANK" class="card-count-heading-new" style="color:<?=$backgroundColorNew;?>!important" href="<?=base_url()?>Reports/MoreThenNDaysNoActivityDoneByLineManagerDetails/task_not_done_in_16_to_30_days/<?=$meeting_user_id?>/userwise/open_rpem">
                          <?= $row->open_rpem; ?>
                          </a>
                        </td>
                        <td>
                          <a target="_BLANK" class="card-count-heading-new" style="color:<?=$backgroundColorNew;?>!important" href="<?=base_url()?>Reports/MoreThenNDaysNoActivityDoneByLineManagerDetails/task_not_done_in_16_to_30_days/<?=$meeting_user_id?>/userwise/reachout">
                          <?= $row->reachout; ?>
                          </a>
                        </td>
                        <td>
                          <a target="_BLANK" class="card-count-heading-new" style="color:<?=$backgroundColorNew;?>!important" href="<?=base_url()?>Reports/MoreThenNDaysNoActivityDoneByLineManagerDetails/task_not_done_in_16_to_30_days/<?=$meeting_user_id?>/userwise/tentative">
                          <?= $row->tentative; ?>
                          </a>
                        </td>
                        <td>
                          <a target="_BLANK" class="card-count-heading-new" style="color:<?=$backgroundColorNew;?>!important" href="<?=base_url()?>Reports/MoreThenNDaysNoActivityDoneByLineManagerDetails/task_not_done_in_16_to_30_days/<?=$meeting_user_id?>/userwise/will_do_later">
                          <?= $row->will_do_later; ?>
                          </a>
                        </td>
                        <td>
                          <a target="_BLANK" class="card-count-heading-new" style="color:<?=$backgroundColorNew;?>!important" href="<?=base_url()?>Reports/MoreThenNDaysNoActivityDoneByLineManagerDetails/task_not_done_in_16_to_30_days/<?=$meeting_user_id?>/userwise/not_interested">
                          <?= $row->not_interested; ?>
                          </a>
                        </td>
                        <td>
                          <a target="_BLANK" class="card-count-heading-new" style="color:<?=$backgroundColorNew;?>!important" href="<?=base_url()?>Reports/MoreThenNDaysNoActivityDoneByLineManagerDetails/task_not_done_in_16_to_30_days/<?=$meeting_user_id?>/userwise/positive">
                          <?= $row->positive; ?>
                          </a>
                        </td>
                        <td>
                          <a target="_BLANK" class="card-count-heading-new" style="color:<?=$backgroundColorNew;?>!important" href="<?=base_url()?>Reports/MoreThenNDaysNoActivityDoneByLineManagerDetails/task_not_done_in_16_to_30_days/<?=$meeting_user_id?>/userwise/very_positive">
                          <?= $row->very_positive; ?>
                          </a>
                        </td>
                        <td>
                          <a target="_BLANK" class="card-count-heading-new" style="color:<?=$backgroundColorNew;?>!important" href="<?=base_url()?>Reports/MoreThenNDaysNoActivityDoneByLineManagerDetails/task_not_done_in_16_to_30_days/<?=$meeting_user_id?>/userwise/ttd_reachout">
                          <?= $row->ttd_reachout; ?>
                          </a>
                        </td>
                        <td>
                          <a target="_BLANK" class="card-count-heading-new" style="color:<?=$backgroundColorNew;?>!important" href="<?=base_url()?>Reports/MoreThenNDaysNoActivityDoneByLineManagerDetails/task_not_done_in_16_to_30_days/<?=$meeting_user_id?>/userwise/wno_reachout">
                          <?= $row->wno_reachout; ?>
                          </a>
                        </td>
                        <td>
                          <a target="_BLANK" class="card-count-heading-new" style="color:<?=$backgroundColorNew;?>!important" href="<?=base_url()?>Reports/MoreThenNDaysNoActivityDoneByLineManagerDetails/task_not_done_in_16_to_30_days/<?=$meeting_user_id?>/userwise/positive_nap">
                          <?= $row->positive_nap; ?>
                          </a>
                        </td>
                        <td>
                          <a target="_BLANK" class="card-count-heading-new" style="color:<?=$backgroundColorNew;?>!important" href="<?=base_url()?>Reports/MoreThenNDaysNoActivityDoneByLineManagerDetails/task_not_done_in_16_to_30_days/<?=$meeting_user_id?>/userwise/very_positive_nap">
                          <?= $row->very_positive_nap; ?>
                          </a>
                        </td>
                        <td>
                          <a target="_BLANK" class="card-count-heading-new" style="color:<?=$backgroundColorNew;?>!important" href="<?=base_url()?>Reports/MoreThenNDaysNoActivityDoneByLineManagerDetails/task_not_done_in_16_to_30_days/<?=$meeting_user_id?>/userwise/closure">
                          <?= $row->closure; ?>
                          </a>
                        </td>
                        <td>
                          <a target="_BLANK" class="card-count-heading-new" style="color:<?=$backgroundColorNew;?>!important" href="<?=base_url()?>Reports/MoreThenNDaysNoActivityDoneByLineManagerDetails/task_not_done_in_16_to_30_days/<?=$meeting_user_id?>/userwise/on_boarded">
                          <?= $row->on_boarded; ?>
                          </a>
                        </td>
                      <?php $i++; } ?>
                    </tbody>
                  </table>
                </div>
              </div>
              </div> 




              <?php $totalNotDoneinthirtyOneTofiftyDays    = $totalFunnnels['totalNotDoneinthirtyOneTofiftyDays']; ?>
               <hr>
              <div class="card" style="background-image: linear-gradient(248.35deg, #86cdff -11.3%, #f4f4fe 16.44%, #ffffff 28.3%, #ffffff 72.47%, #ebeafe 89.69%, #bec6f7 101.94%);">
              <div class="page-header" style="background: linear-gradient(to right, #ffffff, #ffffff); border-radius: 12px; box-shadow: 0 4px 8px rgba(0,0,0,0.1); padding: 20px;">
                <div class="text-center">
                   <h5>
                    <i>📋 31–50 Days Pending for recent tasks needing attention soon</i> <br>
                    <small>
                    ⏳ This table displays the pending tasks assigned to each Business Developer (BD) across different status.
                    </small>
                  </h5>
                </div>
                <div class="table-responsive text-nowrap">
                  <table id="example5" class="table table-striped table-bordered" cellspacing="0" width="100%">
                    <thead class="thead-dark">
                     <tr>
                      <th>🆔</th>
                      <th>🧑‍💼 BD Name</th>
                      <!-- <th>🗺️ User Cluster Zone</th> -->
                      <th>📊 Total Pending</th>
                      <th>📂 Open</th>
                      <th>📌 Open RPEM</th>
                      <th>📞 Reachout</th>
                      <th>🤔 Tentative</th>
                      <th>🕗 Will Do Later</th>
                      <th>🚫 Not Interested</th>
                      <th>👍 Positive</th>
                      <th>🌟 Very Positive</th>
                      <th>📲 TTD Reachout</th>
                      <th>🔕 WNO Reachout</th>
                      <th>😊 Positive NAP</th>
                      <th>🌈 Very Positive NAP</th>
                      <th>✅ Closure</th>
                      <th>🧾 On Boarded</th>
                    </tr>
                    </thead>
                    <tbody>
                      <?php
                        $i = 1;
                        foreach ($totalNotDoneinthirtyOneTofiftyDays as $row) {
                          $meeting_user_id = $row->user_id;
                        
                          $r = rand(150, 255);
                          $g = rand(150, 255);
                          $b = rand(150, 255);
                          $backgroundColor = "rgba($r, $g, $b,.2)";
                          $hue = rand(0, 360); // Full color wheel
                          $saturation = rand(60, 100); // High saturation for rich colors
                          $lightness = rand(30, 45); // Low lightness for a deeper tone
                          $backgroundColorNew = "hsl($hue, $saturation%, $lightness%)";
                          
                          ?>
                      <tr style="background-color: <?php echo $backgroundColor; ?>;color:<?=$backgroundColorNew;?>!important">
                        <td><?= $i; ?></td>
                        <td class="text-capitalize"><?php echo htmlspecialchars($row->user_name); ?></td>
                        <!-- <td class="text-capitalize"><?php echo htmlspecialchars($row->user_cluster_zone); ?></td> -->

                        <td>
                          <a target="_BLANK" class="card-count-heading-new" style="color:<?=$backgroundColorNew;?>!important" href="<?=base_url()?>Reports/MoreThenNDaysNoActivityDoneByLineManagerDetails/task_not_done_in_31_to_50_days/<?=$meeting_user_id?>/userwise/allstatus">
                          <?= $row->total; ?>
                          </a>
                        </td>
                        <td>
                          <a target="_BLANK" class="card-count-heading-new" style="color:<?=$backgroundColorNew;?>!important" href="<?=base_url()?>Reports/MoreThenNDaysNoActivityDoneByLineManagerDetails/task_not_done_in_31_to_50_days/<?=$meeting_user_id?>/userwise/open">
                          <?= $row->open; ?>
                          </a>
                        </td>
                          <td>
                          <a target="_BLANK" class="card-count-heading-new" style="color:<?=$backgroundColorNew;?>!important" href="<?=base_url()?>Reports/MoreThenNDaysNoActivityDoneByLineManagerDetails/task_not_done_in_31_to_50_days/<?=$meeting_user_id?>/userwise/open_rpem">
                          <?= $row->open_rpem; ?>
                          </a>
                        </td>
                        <td>
                          <a target="_BLANK" class="card-count-heading-new" style="color:<?=$backgroundColorNew;?>!important" href="<?=base_url()?>Reports/MoreThenNDaysNoActivityDoneByLineManagerDetails/task_not_done_in_31_to_50_days/<?=$meeting_user_id?>/userwise/reachout">
                          <?= $row->reachout; ?>
                          </a>
                        </td>
                        <td>
                          <a target="_BLANK" class="card-count-heading-new" style="color:<?=$backgroundColorNew;?>!important" href="<?=base_url()?>Reports/MoreThenNDaysNoActivityDoneByLineManagerDetails/task_not_done_in_31_to_50_days/<?=$meeting_user_id?>/userwise/tentative">
                          <?= $row->tentative; ?>
                          </a>
                        </td>
                        <td>
                          <a target="_BLANK" class="card-count-heading-new" style="color:<?=$backgroundColorNew;?>!important" href="<?=base_url()?>Reports/MoreThenNDaysNoActivityDoneByLineManagerDetails/task_not_done_in_31_to_50_days/<?=$meeting_user_id?>/userwise/will_do_later">
                          <?= $row->will_do_later; ?>
                          </a>
                        </td>
                        <td>
                          <a target="_BLANK" class="card-count-heading-new" style="color:<?=$backgroundColorNew;?>!important" href="<?=base_url()?>Reports/MoreThenNDaysNoActivityDoneByLineManagerDetails/task_not_done_in_31_to_50_days/<?=$meeting_user_id?>/userwise/not_interested">
                          <?= $row->not_interested; ?>
                          </a>
                        </td>
                        <td>
                          <a target="_BLANK" class="card-count-heading-new" style="color:<?=$backgroundColorNew;?>!important" href="<?=base_url()?>Reports/MoreThenNDaysNoActivityDoneByLineManagerDetails/task_not_done_in_31_to_50_days/<?=$meeting_user_id?>/userwise/positive">
                          <?= $row->positive; ?>
                          </a>
                        </td>
                        <td>
                          <a target="_BLANK" class="card-count-heading-new" style="color:<?=$backgroundColorNew;?>!important" href="<?=base_url()?>Reports/MoreThenNDaysNoActivityDoneByLineManagerDetails/task_not_done_in_31_to_50_days/<?=$meeting_user_id?>/userwise/very_positive">
                          <?= $row->very_positive; ?>
                          </a>
                        </td>
                        <td>
                          <a target="_BLANK" class="card-count-heading-new" style="color:<?=$backgroundColorNew;?>!important" href="<?=base_url()?>Reports/MoreThenNDaysNoActivityDoneByLineManagerDetails/task_not_done_in_31_to_50_days/<?=$meeting_user_id?>/userwise/ttd_reachout">
                          <?= $row->ttd_reachout; ?>
                          </a>
                        </td>
                        <td>
                          <a target="_BLANK" class="card-count-heading-new" style="color:<?=$backgroundColorNew;?>!important" href="<?=base_url()?>Reports/MoreThenNDaysNoActivityDoneByLineManagerDetails/task_not_done_in_31_to_50_days/<?=$meeting_user_id?>/userwise/wno_reachout">
                          <?= $row->wno_reachout; ?>
                          </a>
                        </td>
                        <td>
                          <a target="_BLANK" class="card-count-heading-new" style="color:<?=$backgroundColorNew;?>!important" href="<?=base_url()?>Reports/MoreThenNDaysNoActivityDoneByLineManagerDetails/task_not_done_in_31_to_50_days/<?=$meeting_user_id?>/userwise/positive_nap">
                          <?= $row->positive_nap; ?>
                          </a>
                        </td>
                        <td>
                          <a target="_BLANK" class="card-count-heading-new" style="color:<?=$backgroundColorNew;?>!important" href="<?=base_url()?>Reports/MoreThenNDaysNoActivityDoneByLineManagerDetails/task_not_done_in_31_to_50_days/<?=$meeting_user_id?>/userwise/very_positive_nap">
                          <?= $row->very_positive_nap; ?>
                          </a>
                        </td>
                        <td>
                          <a target="_BLANK" class="card-count-heading-new" style="color:<?=$backgroundColorNew;?>!important" href="<?=base_url()?>Reports/MoreThenNDaysNoActivityDoneByLineManagerDetails/task_not_done_in_31_to_50_days/<?=$meeting_user_id?>/userwise/closure">
                          <?= $row->closure; ?>
                          </a>
                        </td>
                        <td>
                          <a target="_BLANK" class="card-count-heading-new" style="color:<?=$backgroundColorNew;?>!important" href="<?=base_url()?>Reports/MoreThenNDaysNoActivityDoneByLineManagerDetails/task_not_done_in_31_to_50_days/<?=$meeting_user_id?>/userwise/on_boarded">
                          <?= $row->on_boarded; ?>
                          </a>
                        </td>
                      <?php $i++; } ?>
                    </tbody>
                  </table>
                </div>
              </div>
              </div> 








              
              <?php $totalNotDoneinMoreThanfiftyDays    = $totalFunnnels['totalNotDoneinMoreThanfiftyDays']; ?>
               <hr>
              <div class="card" style="background-image: linear-gradient(248.35deg, #86cdff -11.3%, #f4f4fe 16.44%, #ffffff 28.3%, #ffffff 72.47%, #ebeafe 89.69%, #bec6f7 101.94%);">
              <div class="page-header" style="background: linear-gradient(to right, #ffffff, #ffffff); border-radius: 12px; box-shadow: 0 4px 8px rgba(0,0,0,0.1); padding: 20px;">
                <div class="text-center">
                   <h5>
                    <i>📋 More Than 50 Days Pending for recent tasks needing attention soon</i> <br>
                    <small>
                    ⏳ This table displays the pending tasks assigned to each Business Developer (BD) across different status.
                    </small>
                  </h5>
                </div>
                <div class="table-responsive text-nowrap">
                  <table id="example6" class="table table-striped table-bordered" cellspacing="0" width="100%">
                    <thead class="thead-dark">
                     <tr>
                      <th>🆔</th>
                      <th>🧑‍💼 BD Name</th>
                      <!-- <th>🗺️ User Cluster Zone</th> -->
                      <th>📊 Total Pending</th>
                      <th>📂 Open</th>
                      <th>📌 Open RPEM</th>
                      <th>📞 Reachout</th>
                      <th>🤔 Tentative</th>
                      <th>🕗 Will Do Later</th>
                      <th>🚫 Not Interested</th>
                      <th>👍 Positive</th>
                      <th>🌟 Very Positive</th>
                      <th>📲 TTD Reachout</th>
                      <th>🔕 WNO Reachout</th>
                      <th>😊 Positive NAP</th>
                      <th>🌈 Very Positive NAP</th>
                      <th>✅ Closure</th>
                      <th>🧾 On Boarded</th>
                    </tr>
                    </thead>
                    <tbody>
                      <?php
                        $i = 1;
                        foreach ($totalNotDoneinMoreThanfiftyDays as $row) {
                          $meeting_user_id = $row->user_id;
                        
                          $r = rand(150, 255);
                          $g = rand(150, 255);
                          $b = rand(150, 255);
                          $backgroundColor = "rgba($r, $g, $b,.2)";
                          $hue = rand(0, 360); // Full color wheel
                          $saturation = rand(60, 100); // High saturation for rich colors
                          $lightness = rand(30, 45); // Low lightness for a deeper tone
                          $backgroundColorNew = "hsl($hue, $saturation%, $lightness%)";
                          
                          ?>
                      <tr style="background-color: <?php echo $backgroundColor; ?>;color:<?=$backgroundColorNew;?>!important">
                        <td><?= $i; ?></td>
                        <td class="text-capitalize"><?php echo htmlspecialchars($row->user_name); ?></td>
                        <!-- <td class="text-capitalize"><?php echo htmlspecialchars($row->user_cluster_zone); ?></td> -->

                        <td>
                          <a target="_BLANK" class="card-count-heading-new" style="color:<?=$backgroundColorNew;?>!important" href="<?=base_url()?>Reports/MoreThenNDaysNoActivityDoneByLineManagerDetails/task_not_done_in_more_than_50_days/<?=$meeting_user_id?>/userwise/allstatus">
                          <?= $row->total; ?>
                          </a>
                        </td>
                        <td>
                          <a target="_BLANK" class="card-count-heading-new" style="color:<?=$backgroundColorNew;?>!important" href="<?=base_url()?>Reports/MoreThenNDaysNoActivityDoneByLineManagerDetails/task_not_done_in_more_than_50_days/<?=$meeting_user_id?>/userwise/open">
                          <?= $row->open; ?>
                          </a>
                        </td>
                          <td>
                          <a target="_BLANK" class="card-count-heading-new" style="color:<?=$backgroundColorNew;?>!important" href="<?=base_url()?>Reports/MoreThenNDaysNoActivityDoneByLineManagerDetails/task_not_done_in_more_than_50_days/<?=$meeting_user_id?>/userwise/open_rpem">
                          <?= $row->open_rpem; ?>
                          </a>
                        </td>
                        <td>
                          <a target="_BLANK" class="card-count-heading-new" style="color:<?=$backgroundColorNew;?>!important" href="<?=base_url()?>Reports/MoreThenNDaysNoActivityDoneByLineManagerDetails/task_not_done_in_more_than_50_days/<?=$meeting_user_id?>/userwise/reachout">
                          <?= $row->reachout; ?>
                          </a>
                        </td>
                        <td>
                          <a target="_BLANK" class="card-count-heading-new" style="color:<?=$backgroundColorNew;?>!important" href="<?=base_url()?>Reports/MoreThenNDaysNoActivityDoneByLineManagerDetails/task_not_done_in_more_than_50_days/<?=$meeting_user_id?>/userwise/tentative">
                          <?= $row->tentative; ?>
                          </a>
                        </td>
                        <td>
                          <a target="_BLANK" class="card-count-heading-new" style="color:<?=$backgroundColorNew;?>!important" href="<?=base_url()?>Reports/MoreThenNDaysNoActivityDoneByLineManagerDetails/task_not_done_in_more_than_50_days/<?=$meeting_user_id?>/userwise/will_do_later">
                          <?= $row->will_do_later; ?>
                          </a>
                        </td>
                        <td>
                          <a target="_BLANK" class="card-count-heading-new" style="color:<?=$backgroundColorNew;?>!important" href="<?=base_url()?>Reports/MoreThenNDaysNoActivityDoneByLineManagerDetails/task_not_done_in_more_than_50_days/<?=$meeting_user_id?>/userwise/not_interested">
                          <?= $row->not_interested; ?>
                          </a>
                        </td>
                        <td>
                          <a target="_BLANK" class="card-count-heading-new" style="color:<?=$backgroundColorNew;?>!important" href="<?=base_url()?>Reports/MoreThenNDaysNoActivityDoneByLineManagerDetails/task_not_done_in_more_than_50_days/<?=$meeting_user_id?>/userwise/positive">
                          <?= $row->positive; ?>
                          </a>
                        </td>
                        <td>
                          <a target="_BLANK" class="card-count-heading-new" style="color:<?=$backgroundColorNew;?>!important" href="<?=base_url()?>Reports/MoreThenNDaysNoActivityDoneByLineManagerDetails/task_not_done_in_more_than_50_days/<?=$meeting_user_id?>/userwise/very_positive">
                          <?= $row->very_positive; ?>
                          </a>
                        </td>
                        <td>
                          <a target="_BLANK" class="card-count-heading-new" style="color:<?=$backgroundColorNew;?>!important" href="<?=base_url()?>Reports/MoreThenNDaysNoActivityDoneByLineManagerDetails/task_not_done_in_more_than_50_days/<?=$meeting_user_id?>/userwise/ttd_reachout">
                          <?= $row->ttd_reachout; ?>
                          </a>
                        </td>
                        <td>
                          <a target="_BLANK" class="card-count-heading-new" style="color:<?=$backgroundColorNew;?>!important" href="<?=base_url()?>Reports/MoreThenNDaysNoActivityDoneByLineManagerDetails/task_not_done_in_more_than_50_days/<?=$meeting_user_id?>/userwise/wno_reachout">
                          <?= $row->wno_reachout; ?>
                          </a>
                        </td>
                        <td>
                          <a target="_BLANK" class="card-count-heading-new" style="color:<?=$backgroundColorNew;?>!important" href="<?=base_url()?>Reports/MoreThenNDaysNoActivityDoneByLineManagerDetails/task_not_done_in_more_than_50_days/<?=$meeting_user_id?>/userwise/positive_nap">
                          <?= $row->positive_nap; ?>
                          </a>
                        </td>
                        <td>
                          <a target="_BLANK" class="card-count-heading-new" style="color:<?=$backgroundColorNew;?>!important" href="<?=base_url()?>Reports/MoreThenNDaysNoActivityDoneByLineManagerDetails/task_not_done_in_more_than_50_days/<?=$meeting_user_id?>/userwise/very_positive_nap">
                          <?= $row->very_positive_nap; ?>
                          </a>
                        </td>
                        <td>
                          <a target="_BLANK" class="card-count-heading-new" style="color:<?=$backgroundColorNew;?>!important" href="<?=base_url()?>Reports/MoreThenNDaysNoActivityDoneByLineManagerDetails/task_not_done_in_more_than_50_days/<?=$meeting_user_id?>/userwise/closure">
                          <?= $row->closure; ?>
                          </a>
                        </td>
                        <td>
                          <a target="_BLANK" class="card-count-heading-new" style="color:<?=$backgroundColorNew;?>!important" href="<?=base_url()?>Reports/MoreThenNDaysNoActivityDoneByLineManagerDetails/task_not_done_in_more_than_50_days/<?=$meeting_user_id?>/userwise/on_boarded">
                          <?= $row->on_boarded; ?>
                          </a>
                        </td>
                      <?php $i++; } ?>
                    </tbody>
                  </table>
                </div>
              </div>
              </div> 



              <?php $totalNotDoneinMoreThanDays    = $totalFunnnels['totalNotDoneinMoreThanDays']; ?>
               <hr>
              <div class="card" style="background-image: linear-gradient(248.35deg, #86cdff -11.3%, #f4f4fe 16.44%, #ffffff 28.3%, #ffffff 72.47%, #ebeafe 89.69%, #bec6f7 101.94%);">
              <div class="page-header" style="background: linear-gradient(to right, #ffffff, #ffffff); border-radius: 12px; box-shadow: 0 4px 8px rgba(0,0,0,0.1); padding: 20px;">
                <div class="text-center">
                   <h5>
                    <i>📋 No Work in Days Pending for recent tasks needing attention soon</i> <br>
                    <small>
                    ⏳ This table displays the pending tasks assigned to each Business Developer (BD) across different status.
                    </small>
                  </h5>
                </div>
                <div class="table-responsive text-nowrap">
                  <table id="example7" class="table table-striped table-bordered" cellspacing="0" width="100%">
                    <thead class="thead-dark">
                     <tr>
                      <th>🆔</th>
                      <th>🧑‍💼 BD Name</th>
                      <!-- <th>🗺️ User Cluster Zone</th> -->
                      <th>📊 Total Pending</th>
                      <th>📂 Open</th>
                      <th>📌 Open RPEM</th>
                      <th>📞 Reachout</th>
                      <th>🤔 Tentative</th>
                      <th>🕗 Will Do Later</th>
                      <th>🚫 Not Interested</th>
                      <th>👍 Positive</th>
                      <th>🌟 Very Positive</th>
                      <th>📲 TTD Reachout</th>
                      <th>🔕 WNO Reachout</th>
                      <th>😊 Positive NAP</th>
                      <th>🌈 Very Positive NAP</th>
                      <th>✅ Closure</th>
                      <th>🧾 On Boarded</th>
                    </tr>
                    </thead>
                    <tbody>
                      <?php
                        $i = 1;
                        foreach ($totalNotDoneinMoreThanDays as $row) {
                          $meeting_user_id = $row->user_id;
                        
                          $r = rand(150, 255);
                          $g = rand(150, 255);
                          $b = rand(150, 255);
                          $backgroundColor = "rgba($r, $g, $b,.2)";
                          $hue = rand(0, 360); // Full color wheel
                          $saturation = rand(60, 100); // High saturation for rich colors
                          $lightness = rand(30, 45); // Low lightness for a deeper tone
                          $backgroundColorNew = "hsl($hue, $saturation%, $lightness%)";
                          
                          ?>
                      <tr style="background-color: <?php echo $backgroundColor; ?>;color:<?=$backgroundColorNew;?>!important">
                        <td><?= $i; ?></td>
                        <td class="text-capitalize"><?php echo htmlspecialchars($row->user_name); ?></td>
                        <!-- <td class="text-capitalize"><?php echo htmlspecialchars($row->user_cluster_zone); ?></td> -->

                        <td>
                          <a target="_BLANK" class="card-count-heading-new" style="color:<?=$backgroundColorNew;?>!important" href="<?=base_url()?>Reports/MoreThenNDaysNoActivityDoneByLineManagerDetails/no_activity_till_date/<?=$meeting_user_id?>/userwise/allstatus">
                          <?= $row->total; ?>
                          </a>
                        </td>
                        <td>
                          <a target="_BLANK" class="card-count-heading-new" style="color:<?=$backgroundColorNew;?>!important" href="<?=base_url()?>Reports/MoreThenNDaysNoActivityDoneByLineManagerDetails/no_activity_till_date/<?=$meeting_user_id?>/userwise/open">
                          <?= $row->open; ?>
                          </a>
                        </td>
                          <td>
                          <a target="_BLANK" class="card-count-heading-new" style="color:<?=$backgroundColorNew;?>!important" href="<?=base_url()?>Reports/MoreThenNDaysNoActivityDoneByLineManagerDetails/no_activity_till_date/<?=$meeting_user_id?>/userwise/open_rpem">
                          <?= $row->open_rpem; ?>
                          </a>
                        </td>
                        <td>
                          <a target="_BLANK" class="card-count-heading-new" style="color:<?=$backgroundColorNew;?>!important" href="<?=base_url()?>Reports/MoreThenNDaysNoActivityDoneByLineManagerDetails/no_activity_till_date/<?=$meeting_user_id?>/userwise/reachout">
                          <?= $row->reachout; ?>
                          </a>
                        </td>
                        <td>
                          <a target="_BLANK" class="card-count-heading-new" style="color:<?=$backgroundColorNew;?>!important" href="<?=base_url()?>Reports/MoreThenNDaysNoActivityDoneByLineManagerDetails/no_activity_till_date/<?=$meeting_user_id?>/userwise/tentative">
                          <?= $row->tentative; ?>
                          </a>
                        </td>
                        <td>
                          <a target="_BLANK" class="card-count-heading-new" style="color:<?=$backgroundColorNew;?>!important" href="<?=base_url()?>Reports/MoreThenNDaysNoActivityDoneByLineManagerDetails/no_activity_till_date/<?=$meeting_user_id?>/userwise/will_do_later">
                          <?= $row->will_do_later; ?>
                          </a>
                        </td>
                        <td>
                          <a target="_BLANK" class="card-count-heading-new" style="color:<?=$backgroundColorNew;?>!important" href="<?=base_url()?>Reports/MoreThenNDaysNoActivityDoneByLineManagerDetails/no_activity_till_date/<?=$meeting_user_id?>/userwise/not_interested">
                          <?= $row->not_interested; ?>
                          </a>
                        </td>
                        <td>
                          <a target="_BLANK" class="card-count-heading-new" style="color:<?=$backgroundColorNew;?>!important" href="<?=base_url()?>Reports/MoreThenNDaysNoActivityDoneByLineManagerDetails/no_activity_till_date/<?=$meeting_user_id?>/userwise/positive">
                          <?= $row->positive; ?>
                          </a>
                        </td>
                        <td>
                          <a target="_BLANK" class="card-count-heading-new" style="color:<?=$backgroundColorNew;?>!important" href="<?=base_url()?>Reports/MoreThenNDaysNoActivityDoneByLineManagerDetails/no_activity_till_date/<?=$meeting_user_id?>/userwise/very_positive">
                          <?= $row->very_positive; ?>
                          </a>
                        </td>
                        <td>
                          <a target="_BLANK" class="card-count-heading-new" style="color:<?=$backgroundColorNew;?>!important" href="<?=base_url()?>Reports/MoreThenNDaysNoActivityDoneByLineManagerDetails/no_activity_till_date/<?=$meeting_user_id?>/userwise/ttd_reachout">
                          <?= $row->ttd_reachout; ?>
                          </a>
                        </td>
                        <td>
                          <a target="_BLANK" class="card-count-heading-new" style="color:<?=$backgroundColorNew;?>!important" href="<?=base_url()?>Reports/MoreThenNDaysNoActivityDoneByLineManagerDetails/no_activity_till_date/<?=$meeting_user_id?>/userwise/wno_reachout">
                          <?= $row->wno_reachout; ?>
                          </a>
                        </td>
                        <td>
                          <a target="_BLANK" class="card-count-heading-new" style="color:<?=$backgroundColorNew;?>!important" href="<?=base_url()?>Reports/MoreThenNDaysNoActivityDoneByLineManagerDetails/no_activity_till_date/<?=$meeting_user_id?>/userwise/positive_nap">
                          <?= $row->positive_nap; ?>
                          </a>
                        </td>
                        <td>
                          <a target="_BLANK" class="card-count-heading-new" style="color:<?=$backgroundColorNew;?>!important" href="<?=base_url()?>Reports/MoreThenNDaysNoActivityDoneByLineManagerDetails/no_activity_till_date/<?=$meeting_user_id?>/userwise/very_positive_nap">
                          <?= $row->very_positive_nap; ?>
                          </a>
                        </td>
                        <td>
                          <a target="_BLANK" class="card-count-heading-new" style="color:<?=$backgroundColorNew;?>!important" href="<?=base_url()?>Reports/MoreThenNDaysNoActivityDoneByLineManagerDetails/no_activity_till_date/<?=$meeting_user_id?>/userwise/closure">
                          <?= $row->closure; ?>
                          </a>
                        </td>
                        <td>
                          <a target="_BLANK" class="card-count-heading-new" style="color:<?=$backgroundColorNew;?>!important" href="<?=base_url()?>Reports/MoreThenNDaysNoActivityDoneByLineManagerDetails/no_activity_till_date/<?=$meeting_user_id?>/userwise/on_boarded">
                          <?= $row->on_boarded; ?>
                          </a>
                        </td>
                      <?php $i++; } ?>
                    </tbody>
                  </table>
                </div>
              </div>
              </div> 

          </div>
        </section>
      </div>
      <footer class="main-footer">
        <strong>Copyright &copy; 2021-<?=date("Y")?> <a href="<?=base_url();?>">Stemlearning</a>.</strong>
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
    </div>
    <!-- ./wrapper -->
    <!-- jQuery -->
    <script>
      function Reject(mid,id,val){
             // alert(mid);
             // alert('#exampleModalCenterdata'+mid);
             $('#exampleModalCenterdata').modal('show');
             $('#rejectid').val(id);
         // $('#exampleModalCenterdata'+mid).modal('show');
         // $('#exampleModalCenterdata'+mid+' #rejectid').val(id);
         }
    </script>
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
        "buttons": ["csv", "excel", "pdf"]
      }).buttons().container().appendTo('#example1_wrapper .col-md-6:eq(0)');
      $("#example2").DataTable({
        "responsive": false, "lengthChange": false, "autoWidth": false,
        "buttons": ["csv", "excel", "pdf"]
      }).buttons().container().appendTo('#example2_wrapper .col-md-6:eq(0)');
      $("#example3").DataTable({
        "responsive": false, "lengthChange": false, "autoWidth": false,
        "buttons": ["csv", "excel", "pdf"]
      }).buttons().container().appendTo('#example3_wrapper .col-md-6:eq(0)');
      $("#example4").DataTable({
        "responsive": false, "lengthChange": false, "autoWidth": false,
        "buttons": ["csv", "excel", "pdf"]
      }).buttons().container().appendTo('#example4_wrapper .col-md-6:eq(0)');
      $("#example5").DataTable({
        "responsive": false, "lengthChange": false, "autoWidth": false,
        "buttons": ["csv", "excel", "pdf"]
      }).buttons().container().appendTo('#example5_wrapper .col-md-6:eq(0)');
      $("#example6").DataTable({
        "responsive": false, "lengthChange": false, "autoWidth": false,
        "buttons": ["csv", "excel", "pdf"]
      }).buttons().container().appendTo('#example6_wrapper .col-md-6:eq(0)');
      $("#example7").DataTable({
        "responsive": false, "lengthChange": false, "autoWidth": false,
        "buttons": ["csv", "excel", "pdf"]
      }).buttons().container().appendTo('#example7_wrapper .col-md-6:eq(0)');
      $("#example8").DataTable({
        "responsive": false, "lengthChange": false, "autoWidth": false,
        "buttons": ["csv", "excel", "pdf"]
      }).buttons().container().appendTo('#example8_wrapper .col-md-6:eq(0)');
      $("#example9").DataTable({
        "responsive": false, "lengthChange": false, "autoWidth": false,
        "buttons": ["csv", "excel", "pdf"]
      }).buttons().container().appendTo('#example9_wrapper .col-md-6:eq(0)');



      
      $("#example10").DataTable({
        "responsive": false, "lengthChange": false, "autoWidth": false,
        "buttons": ["csv", "excel", "pdf"]
      }).buttons().container().appendTo('#example10_wrapper .col-md-6:eq(0)');
      
      $("#example15").DataTable({
        "responsive": false, "lengthChange": false, "autoWidth": false,
        "buttons": ["csv", "excel", "pdf"]
      }).buttons().container().appendTo('#example15_wrapper .col-md-6:eq(0)');
      $("#example11").DataTable({
        "responsive": false, "lengthChange": false, "autoWidth": false,
        "buttons": ["csv", "excel", "pdf"]
      }).buttons().container().appendTo('#example11_wrapper .col-md-6:eq(0)');
      $("#example12").DataTable({
        "responsive": false, "lengthChange": false, "autoWidth": false,
        "buttons": ["csv", "excel", "pdf"]
      }).buttons().container().appendTo('#example12_wrapper .col-md-6:eq(0)');
      
      $("#example13").DataTable({
        "responsive": false, "lengthChange": false, "autoWidth": false,
        "buttons": ["csv", "excel", "pdf"]
      }).buttons().container().appendTo('#example13_wrapper .col-md-6:eq(0)');
      
    </script>
    <script>
      document.addEventListener('DOMContentLoaded', function() {
        const cards = document.querySelectorAll('.card');
      
        cards.forEach(card => {
          const bgColor = window.getComputedStyle(card).backgroundColor;
          const rgb = bgColor.match(/\d+/g);
          const brightness = (rgb[0] * 299 + rgb[1] * 587 + rgb[2] * 114) / 1000;
      
          if (brightness > 128) {
            card.classList.add('text-dark');
          } else {
            card.classList.add('text-light');
          }
        });
      });
    </script>
  </body>
</html>
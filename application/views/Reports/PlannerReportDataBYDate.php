<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
      <title>🗓️ Team Planner Details | STEM APP | WebAPP</title>
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
      .text-light {
        color: white !important;
      }
      .text-dark {
        color: black !important;
      }
      .form-container {
        display: flex;
        align-items: center;
        gap: 10px;
      }
      .form-container input, .form-container button {
        padding: 8px;
        border-radius: 4px;
        border: 1px solid #ccc;
      }
      .form-container button {
        background-color: #007bff;
        color: white;
        border: none;
        cursor: pointer;
      }
      .col-sm-6.text-right-data {
        align-items: right;
        justify-content: right;
        display: flex;
      }
      .row-equal-height {
        display: flex;
        flex-wrap: wrap;
      }
      .row-equal-height > [class*='col-'] {
        display: flex;
        flex-direction: column;
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
      .card-bg-1 { border: 2px solid #D4AF37;  } /* Gold border */
      .card-bg-2 { border: 2px solid #A9A9A9; } /* Silver border */
      .card-bg-3 { border: 2px solid #8B4513; } /* Bronze border */
      .card-bg-4 { border: 2px solid #1E90FF; } /* Steel Blue border */
      .card-bg-5 { border: 2px solid #556B2F; } /* Dark Olive Green border */
      .card-bg-6 { border: 2px solid #800000; } /* Dark Red border */
      .card-bg-7 { border: 2px solid #4B0082; } /* Indigo border */
      .card-bg-8 { border: 2px solid #2E8B57; } /* Sea Green border */
      .card-bg-9 { border: 2px solid #FF6347; } /* Tomato border */
      .card-bg-10 { border: 2px solid #9932CC; } /* Dark Orchid border */
      .card-bg-11 { border: 2px solid #8B4513; } /* Saddle Brown border */
      .card-bg-12 { border: 2px solid #20B2AA; } /* Light Sea Green border */
      .card-bg-13 { border: 2px solid #FFDAB9; } /* Peach Puff border */
      .card-bg-14 { border: 2px solid #6A5ACD; } /* Slate Blue border */
      .card-bg-15 { border: 2px solid #FF69B4; } /* Hot Pink border */
      .card-bg-16 { border: 2px solid #00BFFF; } /* Deep Sky Blue border */

      /* Multiple layer frame styles */
      .frame-layer-1 {
        padding: 5px;
        background: linear-gradient(145deg, #f0f0f0, #d9d9d9);
        border-radius: 15px;
        flex: 1;
        display: flex;
        flex-direction: column;
        margin-bottom: 2px;
      }
      .frame-layer-2 {
        padding: 10px;
        background: linear-gradient(145deg, #e6e6e6, #cccccc);
        border-radius: 10px;
        flex: 1;
        display: flex;
        flex-direction: column;
        margin-bottom: 2px;
      }
      .frame-layer-3 {
        padding: 15px;
        background: linear-gradient(145deg, #f5f5f5, #e0e0e0);
        border-radius: 5px;
        flex: 1;
        display: flex;
        flex-direction: column;
        margin-bottom: 2px;
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

      @media (min-width: 576px) {
      .card-group {
        display: -ms-flexbox;
        display: unset;
        -ms-flex-flow: row wrap;
        flex-flow: row wrap;
    }
}
.row-color-1 { background-color: #ffdddd; }
    .row-color-2 { background-color: #ddffdd; }
    .row-color-3 { background-color: #ddddff; }
    .row-color-4 { background-color: #ffffdd; }
    .row-color-5 { background-color: #ffddff; }
    .row-color-6 { background-color: #d1ffd1; }
    .row-color-7 { background-color: #ffd1d1; }
    .row-color-8 { background-color: #d1d1ff; }
    .row-color-9 { background-color: #ffefd1; }
    .row-color-10 { background-color: #d1ffe7; }
    .row-color-11 { background-color: #ffd1f9; }
    .row-color-12 { background-color: #d1f9ff; }
    .row-color-13 { background-color: #f9d1ff; }
    .row-color-14 { background-color: #d1ffd9; }
    .row-color-15 { background-color: #ffd9d1; }
    .row-color-16 { background-color: #d9ffd1; }
    .row-color-17 { background-color: #d1d9ff; }
    .row-color-18 { background-color: #ffd1d9; }
    .row-color-19 { background-color: #d9d1ff; }
    .row-color-20 { background-color: #ffd1e7; }
    .row-color-21 { background-color: #d1ffe7; }
    .row-color-22 { background-color: #e7d1ff; }
    .row-color-23 { background-color: #d1ffd1; }
    .row-color-24 { background-color: #ffd1ff; }
    .row-color-25 { background-color: #d1e7ff; }
    .row-color-26 { background-color: #ffd1d1; }
    .row-color-27 { background-color: #d1ffd9; }
    .row-color-28 { background-color: #d9d1ff; }
    .row-color-29 { background-color: #ffd9d1; }
    .row-color-30 { background-color: #d1d9ff; }
    .row-color-31 { background-color: #ffd1e7; }
    .row-color-32 { background-color: #e7d1ff; }
    .row-color-33 { background-color: #d1ffd1; }
    .row-color-34 { background-color: #ffd1ff; }
    .row-color-35 { background-color: #d1e7ff; }
    .row-color-36 { background-color: #ffd1d1; }
    .row-color-37 { background-color: #d1ffd9; }
    .row-color-38 { background-color: #d9d1ff; }
    .row-color-39 { background-color: #ffd9d1; }
    .row-color-40 { background-color: #d1d9ff; }
    .row-color-41 { background-color: #ffd1e7; }
    .row-color-42 { background-color: #e7d1ff; }
    .row-color-43 { background-color: #d1ffd1; }
    .row-color-44 { background-color: #ffd1ff; }
    .row-color-45 { background-color: #d1e7ff; }
    .row-color-46 { background-color: #ffd1d1; }
    .row-color-47 { background-color: #d1ffd9; }
    .row-color-48 { background-color: #d9d1ff; }
    .row-color-49 { background-color: #ffd9d1; }
    .row-color-50 { background-color: #d1d9ff; }
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
        .userrequestbg{
          background: linear-gradient(to right, #ecfffd, #ffffff); 
          border-radius: 12px; 
          box-shadow: rgba(60, 64, 67, 0.3) 0px 1px 2px 0px, rgba(60, 64, 67, 0.15) 0px 1px 3px 1px;
          padding: 5px;
          margin-bottom: 2px;
          border-radius: 4px;
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
              <div class="col-md-12 text-center">
                <div class="frame-layer-1">
                  <div class="frame-layer-2">
                    <div class="frame-layer-3" style="background: linear-gradient(to right, #ede7f6, #ffffff); border-radius: 12px; box-shadow: 0 4px 8px rgba(0,0,0,0.1); padding: 20px;">


                    <?php if($mtypes == 'total_user'){ ?> 
                        <h1 class="m-0 premium-color-1" style="color: #ff009b;">👥 Total Users Overview</h1>
                        <small class="m-2">
                            This metric represents the overall count of active users within the system. 📊 It provides a comprehensive view of all individuals currently engaged and accounted for, offering insights into the scale and scope of user involvement. 🔍 Understanding the total number of users helps in assessing the reach and impact of the system, enabling better resource allocation and strategic planning. 📈
                        </small>
                    <?php } ?>
                    <?php if($mtypes == 'total_present_user'){ ?> 
                        <h1 class="m-0 premium-color-1" style="color: #ff009b;">👤✅ Total Present Users Overview</h1>
                        <small class="m-2">
                           The "Total Present Users" metric indicates the number of users who are currently active or logged into the system on a given day. 📅 This figure is crucial for understanding daily engagement levels and operational activity. 📊 By tracking present users, organizations can gauge real-time participation, optimize resource management, and enhance user interaction strategies. 🔄 This metric is essential for assessing the immediate impact and reach within the system. 🌟
                        </small>
                    <?php } ?>
                    <?php if($mtypes == 'total_absent_user'){ ?> 
                       <h1 class="m-0 premium-color-1" style="color: #ff009b;">👤✅ Total Absent Users Overview</h1> 
                        <small class="m-2">
                          The "Total Absent Users" metric represents the number of users who were not active or did not log into the system on a given day. 📅 This data is vital for identifying engagement gaps, optimizing attendance strategies, and ensuring balanced workload distribution. 📉 By monitoring absent users, organizations can address potential issues, improve participation rates, and strengthen overall operational efficiency. 🔍 This metric provides valuable insight into user behavior and system reach. 📊
                        </small>
                    <?php } ?>
                    <?php if($mtypes == 'total_work_from_office'){ ?> 
                      <h1 class="m-0 premium-color-1" style="color: #ff009b;">👤🏢 Total Work From Office Details</h1> 
                          <small class="m-2">
                            The "Total Work From Office" metric represents the number of users who reported to the physical workplace on a given day. 🏢📅 This data is crucial for monitoring in-office attendance, managing on-site resources efficiently, and ensuring adherence to workplace policies. 📊 Tracking this metric helps organizations optimize space utilization, evaluate employee presence trends, and support effective team collaboration. 🤝
                          </small>
                    <?php } ?>
                    <?php if($mtypes == 'total_work_from_field'){ ?> 
                     <h1 class="m-0 premium-color-1" style="color: #ff009b;">👤🌍 Total Work From Field Details </h1> 
                      <small class="m-2">
                        The "Total Work From Field" metric represents the number of users who performed their tasks from field locations rather than the office on a given day. 🗺️ This data helps organizations monitor on-ground workforce activity, optimize field operations, and ensure effective resource allocation. 🚀 By tracking field-based work, companies can enhance real-time coordination, improve productivity, and gain insights into operational coverage. 📊
                      </small>
                    <?php } ?>
                    <?php if($mtypes == 'total_work_from_field_office'){ ?> 
                    <h1 class="m-0 premium-color-1" style="color: #ff009b;">👤🏢🌐 Total Work From Field + Office Users Details</h1> 
                    <small class="m-2">
                      The "Total Work From Field + Office Users" metric refers to users who are operating both on the field and from the office during the day. 📅 This hybrid work model showcases flexibility and efficient task management. ⚙️ Tracking this data helps organizations understand multi-location workflows, balance resource allocation, and support productivity across dynamic environments. 📊 It also plays a key role in improving communication, coordination, and operational planning. 🚀
                    </small>
                    <?php } ?>
                    <?php if($mtypes == 'total_planning_set'){ ?> 
                    <h1 class="m-0 premium-color-1" style="color: #ff009b;">🗓️✅ Total Planning Set</h1> 
                      <small class="m-2">
                        The "Total Planning Set" metric refers to the number of users who have submitted or scheduled their daily plans within the system. 📝 This metric is essential for tracking task planning compliance, enhancing productivity, and ensuring proactive work management. 📊 By analyzing planning behavior, organizations can monitor preparation trends, reduce delays, and drive accountability across teams. 🔍 It plays a crucial role in operational forecasting and effective resource allocation. 📅
                      </small>
                    <?php } ?>
                    <?php if($mtypes == 'same_day_planning'){ ?> 
                    <h1 class="m-0 premium-color-1" style="color: #ff009b;">📋🕒 Total Same-Day Planning</h1> 
                      <small class="m-2">
                        The "Total Same-Day Planning" metric indicates the number of users who planned and executed their tasks on the same day. 📅 This highlights real-time responsiveness and operational agility within the team. ⚡ Tracking this metric helps organizations evaluate quick decision-making, on-the-spot planning habits, and adaptability in dynamic work environments. 🔄 It’s a valuable indicator for understanding short-term task management and immediate productivity. 📊
                      </small>
                    <?php } ?>
             
                    <?php if($mtypes == 'previous_day_planning'){ ?> 
                   <h1 class="m-0 premium-color-1" style="color: #ff009b;">🗓️📋 Total Previous Day Planning</h1> 
                      <small class="m-2">
                        The "Total Previous Day Planning" metric indicates the number of tasks or plans that were scheduled on the previous day. 📅 This helps organizations evaluate how well planning activities are being executed in advance. ✅ Monitoring this metric supports better task management, enhances workflow efficiency, and ensures timely execution of responsibilities. 🔄 It also offers insights into users' preparedness and proactive engagement. 📊
                      </small>
                    <?php } ?>
                    <?php if($mtypes == 'planner_not_set'){ ?> 
                        <h1 class="m-0 premium-color-1" style="color: #ff009b;">🗓️❌ Total Users Without Planner Set</h1> 
                          <small class="m-2">
                            The "Users Without Planner Set" metric indicates the number of users who have not created or assigned a planner for the day. 🕒 This figure is crucial for tracking planning discipline, identifying workflow gaps, and ensuring task alignment across teams. 📋 By monitoring users without planners, organizations can encourage timely planning, improve accountability, and enhance overall operational efficiency. ⚙️ This metric plays a vital role in proactive task management and resource optimization. 📈
                          </small>
                    <?php } ?>
                    <?php if($mtypes == 'spdplanning_then_create_same_day_request'){ ?> 
                       <h1 class="m-0 premium-color-1" style="color: #ff009b;">📅⚙️ Users Who Started Planning the Previous Day but Submitted Requests on the Same Day</h1> 
                        <small class="m-2">
                          This metric highlights users who began their planning on the previous day but completed or submitted their task requests on the same day. ⏱️ It helps identify delays in task finalization and provides insight into planning behavior. 📊 Monitoring this data allows organizations to improve planning accuracy, streamline workflows, and reduce last-minute request submissions. 🔄 Understanding this pattern supports better task forecasting and operational efficiency. 🚀
                        </small>

                        

                    <?php } ?>



                      <p class="premium-color-2" style="color: #2c00ee;"><strong><mark><?=$sdate ?> To <?=$edate ?></mark></strong></p>
                      <?php 

                      $target_uidData     = $this->Menu_model->get_userbyid($target_uid);
                      $target_uidDataName = $target_uidData[0]->name;
                      if($mtypes == 'spdplanning_then_create_same_day_request'){
                        $filter = 'Started Planning the Previous Day but Submitted Requests on the Same Day';
                      }else{
                        $filter = ucwords(str_replace('_', ' ', $mtypes));
                      }
                      ?>
                      <p class="premium-color-2 text-capitalize" style="color:rgb(16, 238, 0);"><strong><mark><?=$filter ?></mark></strong></p>
                      <p class="premium-color-2 text-capitalize p-2"><strong><mark style="background-color:rgb(198, 238, 0);"><?=$target_uidDataName ?></mark></strong></p>
                    </div>
                  </div>
                </div>
              </div>
            </div>
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
          </div>
        </div>
        <!-- Main content -->
        <section class="content">
          <div class="container-fluid">
            <hr>
            <div class="body-content">
              <div class="page-header">
                <div class="table-responsive text-nowrap">

                
                <?php if($mtypes == 'total_user'){ ?>
                  <table id="example1" class="table table-striped table-bordered" cellspacing="0" width="100%">
                        <thead class="thead-dark">
                                <tr>
                                    <th class="text-capitalize">🔢 Sr. NO</th>
                                    <th class="text-capitalize">👤 Name</th>
                                    <th class="text-capitalize">📌 Roles</th>
                                    </tr>
                            </thead>
                            <tbody>
                                <?php
                                $i = 1;
                                foreach ($totalTasksUserByDatas as $row) {

                                $r = rand(150, 255);
                                $g = rand(150, 255);
                                $b = rand(150, 255);
                                $backgroundColor = "rgba($r, $g, $b,.2)";
                                $hue = rand(0, 360); // Full color wheel
                                $saturation = rand(60, 100); // High saturation for rich colors
                                $lightness = rand(30, 45); // Low lightness for a deeper tone
                                $backgroundColorNew = "hsl($hue, $saturation%, $lightness%)";

                                // $this->Menu_model->get_user_types($row->type_id);
                                ?>
                                <tr style="background-color: <?php echo $backgroundColor; ?>;color:<?=$backgroundColorNew;?>!important" >
                                        <td><?= htmlspecialchars($i) ?></td>
                                        <td class="text-capitalize"><?= htmlspecialchars($row->name) ?></td>
                                        <td class="text-capitalize"><?= htmlspecialchars($row->user_type_name) ?></td>
                                    </tr>
                            <?php $i++; } ?>
                            </tbody>
                        </table>
                  
                        <?php }else if($mtypes == 'total_present_user' || $mtypes == 'total_absent_user' || $mtypes == 'total_work_from_office' || $mtypes == 'total_work_from_field' || $mtypes == 'total_work_from_field_office' || $mtypes == 'total_present_and_closed_day_user' || $mtypes == 'total_present_and_not_closed_day_user'){ ?>
                  <table id="example1" class="table table-striped table-bordered" cellspacing="0" width="100%">
                        <thead class="thead-dark">
                                <tr>
                                    <th class="text-capitalize">🔢 Sr. No</th>
                                    <th class="text-capitalize">👤 Name</th>
                                    <th class="text-capitalize">📌 Roles</th>
                                    <th class="text-capitalize">⏰ Day Start</th>
                                    <th class="text-capitalize">🛑 Day Close</th>
                                    <th class="text-capitalize">🖼️ Start Image</th>
                                    <th class="text-capitalize">🖼️ Close Image</th>
                                    <th class="text-capitalize">🌍 Start Location</th>
                                    <th class="text-capitalize">🌍 Close Location</th>
                                    <th class="text-capitalize">🏢 Work From</th>
                                </tr>

                            </thead>
                            <tbody>
                                <?php
                                $i = 1;
                                foreach ($totalTasksUserByDatas as $row) {

                                $r = rand(150, 255);
                                $g = rand(150, 255);
                                $b = rand(150, 255);
                                $backgroundColor = "rgba($r, $g, $b,.2)";
                                $hue = rand(0, 360); // Full color wheel
                                $saturation = rand(60, 100); // High saturation for rich colors
                                $lightness = rand(30, 45); // Low lightness for a deeper tone
                                $backgroundColorNew = "hsl($hue, $saturation%, $lightness%)";

                                // $this->Menu_model->get_user_types($row->type_id);
                                ?>
                                <tr style="background-color: <?php echo $backgroundColor; ?>;color:<?=$backgroundColorNew;?>!important" >
                                        <td><?= htmlspecialchars($i) ?></td>
                                        <td class="text-capitalize"><?= htmlspecialchars($row->name) ?></td>
                                        <td class="text-capitalize"><?= htmlspecialchars($row->user_type_name) ?></td>
                                        <td class="text-capitalize">
                                          <?php if($row->user_start != ''){ ?>
                                              <?=$row->user_start?>
                                          <?php } else { ?>
                                              ❌ NA
                                          <?php } ?>
                                      </td>
                                        <td class="text-capitalize">
                                          <?php if($row->user_close != ''){ ?>
                                              <?=$row->user_close?>
                                          <?php } else { ?>
                                              ❌ NA
                                          <?php } ?>
                                      </td>
                                        <td class="text-capitalize">
                                          <?php if($row->usimg != ''){ ?>
                                              <a href="<?=base_url()?>/<?=$row->usimg?>" target="_blank">
                                                  📷 View Image
                                              </a>
                                          <?php } else { ?>
                                              ❌ NA
                                          <?php } ?>
                                      </td>
                                        <td class="text-capitalize">
                                          <?php if($row->ucimg != ''){ ?>
                                              <a href="<?=base_url()?>/<?=$row->ucimg?>" target="_blank">
                                                  📷 View Image
                                              </a>
                                          <?php } else { ?>
                                              ❌ NA
                                          <?php } ?>
                                      </td>
                                        <td class="text-capitalize">
                                            <?php if($row->slatitude != ''){ ?>
                                                <a href="https://www.google.com/maps?q=<?=$row->slatitude?>,<?=$row->slongitude?>" target="_blank">
                                                    Google Maps
                                                </a>
                                            <?php }else{echo "❌ NA";} ?>
                                        </td>
                                        <td class="text-capitalize">
                                        <?php if($row->clatitude != ''){ ?>
                                            <a href="https://www.google.com/maps?q=<?=$row->clatitude?>,<?=$row->clongitude?>" target="_blank">
                                                Google Maps
                                            </a>
                                            <?php }else{echo "❌ NA";} ?>
                                        </td>
                                       <td class="text-capitalize">
                                            <?php if($row->user_wffo == '1'){ ?>
                                                🏢 Work From Office
                                            <?php } else if($row->user_wffo == '2'){ ?>
                                                🌍 Work From Field
                                            <?php } else if($row->user_wffo == '3'){ ?>
                                                🔁 Work From Field + Office
                                            <?php } else if($row->user_wffo == ''){ ?>
                                                ❌ NA
                                            <?php } ?>
                                        </td>
                                    </tr>
                            <?php $i++; } ?>
                            </tbody>
                        </table>
                  
                        <?php } else if($mtypes == 'total_planning_set' || $mtypes == 'same_day_planning' || $mtypes == 'previous_day_planning' || $mtypes == 'planner_not_set' || $mtypes == 'spdplanning_then_create_same_day_request'){ 
                          
                          // dd($totalTasksUserByDatas);
                          ?>
                  <table id="example1" class="table table-striped table-bordered" cellspacing="0" width="100%">
                        <thead class="thead-dark">
                                <tr>
                                    <th class="text-capitalize">🔢 Sr. No</th>
                                    <th class="text-capitalize">👤 Name</th>
                                    <th class="text-capitalize">📌 Roles</th>
                                    <th class="text-capitalize">⏰ Planner Date</th>
                                    <th class="text-capitalize">⏰ Auto Task Start Time</th>
                                    <th class="text-capitalize">🛑 Auto Task Close Time</th>
                                    <th class="text-capitalize">⏰ Planning Start Time</th>
                                    <th class="text-capitalize">🛑 Planning Close Time</th>
                                    <th class="text-capitalize">🗓️ Planning Start Date & Time</th>

                                    <?php if($mtypes == 'spdplanning_then_create_same_day_request' || $mtypes == 'same_day_planning'){ ?>
                                    <th class="text-capitalize">📌 Same Day Request Type</th>
                                    <th class="text-capitalize">💬 Same Day Request Message</th>
                                    <th class="text-capitalize">✅ Request Approval Status</th>
                                    <th class="text-capitalize">📝 Request Approval Remarks</th>
                                    <th class="text-capitalize">⏰ Request Approval Time</th>
                                    <th class="text-capitalize">📅 Request Created Date Time</th>
                                      <?php } ?>
                                    <?php /*
                                    <th class="text-capitalize">🏢 Work From</th>
                                    */ ?>
                                </tr>

                            </thead>
                            <tbody>
                                <?php
                                $i = 1;
                                foreach ($totalTasksUserByDatas as $row) {

                                $r = rand(150, 255);
                                $g = rand(150, 255);
                                $b = rand(150, 255);
                                $backgroundColor = "rgba($r, $g, $b,.2)";
                                $hue = rand(0, 360); // Full color wheel
                                $saturation = rand(60, 100); // High saturation for rich colors
                                $lightness = rand(30, 45); // Low lightness for a deeper tone
                                $backgroundColorNew = "hsl($hue, $saturation%, $lightness%)";

                                // $this->Menu_model->get_user_types($row->type_id);
                                ?>
                                <tr style="background-color: <?php echo $backgroundColor; ?>;color:<?=$backgroundColorNew;?>!important" >
                                        <td><?= htmlspecialchars($i) ?></td>
                                        <td class="text-capitalize"><?= htmlspecialchars($row->name) ?></td>
                                        <td class="text-capitalize"><?= htmlspecialchars($row->user_type_name) ?></td>
                                        <td class="text-capitalize"><?= htmlspecialchars($row->report_date) ?></td>
                                        <td class="text-capitalize">
                                          <?php if($row->auto_task_start_time != ''){ ?>
                                              <?=$row->auto_task_start_time?>
                                          <?php } else { ?>
                                              ❌ NA
                                          <?php } ?>
                                      </td>
                                        <td class="text-capitalize">
                                          <?php if($row->auto_task_end_time != ''){ ?>
                                              <?=$row->auto_task_end_time?>
                                          <?php } else { ?>
                                              ❌ NA
                                          <?php } ?>
                                      </td>
                                      <td class="text-capitalize">
                                          <?php if($row->next_day_planning_start_time != ''){ ?>
                                              <?=$row->next_day_planning_start_time?>
                                          <?php } else { ?>
                                              ❌ NA
                                          <?php } ?>
                                      </td>
                                      <td class="text-capitalize">
                                          <?php if($row->next_day_planning_end_time != ''){ ?>
                                              <?=$row->next_day_planning_end_time?>
                                          <?php } else { ?>
                                              ❌ NA
                                          <?php } ?>
                                      </td>
                                      <td class="text-capitalize">
                                          <?php if($row->planner_setting_datetime != ''){ ?>
                                              <?=$row->planner_setting_datetime?>
                                          <?php } else { ?>
                                              ❌ NA
                                          <?php } ?>
                                      </td>
                                      <?php /*
                                        <td class="text-capitalize">
                                            <?php if($row->planner_setting_userworkfrom == '1'){ ?>
                                                🏢 Work From Office
                                            <?php } else if($row->planner_setting_userworkfrom == '2'){ ?>
                                                🌍 Work From Field
                                            <?php } else if($row->planner_setting_userworkfrom == '3'){ ?>
                                                🔁 Work From Field + Office
                                            <?php } else if($row->planner_setting_userworkfrom == ''){ ?>
                                                ❌ NA
                                            <?php } ?>
                                        </td>
                                        */ ?>

                                    <?php if($mtypes == 'spdplanning_then_create_same_day_request' || $mtypes == 'same_day_planning'){ ?>
                                        <td class="text-capitalize">
                                          <?php 
                                          if($row->same_day_request_id !=''){
                                          if($row->plan_would_you_want != ''){ ?>
                                              <?=$row->plan_would_you_want?>
                                          <?php } else { ?>
                                               NA
                                          <?php } }else{ ?>
                                                ❌ NA
                                        <?php  } ?>
                                        </td>
                                        <td class="text-capitalize">
                                          <?php 
                                          if($row->same_day_request_id !=''){
                                          if($row->plan_request_remarks != ''){ ?>
                                              <?=$row->plan_request_remarks?>
                                          <?php } else { ?>
                                               NA
                                          <?php }}else{ ?>
                                                ❌ NA
                                        <?php  } ?>
                                        </td>
                                        <td class="text-capitalize">
                                          <?php 
                                           if($row->same_day_request_id !=''){
                                          if($row->plan_approvel_status != ''){ ?>
                                              <?=$row->plan_approvel_status?>
                                          <?php } else { ?>
                                              <span class="bg-warning p-1">Pending</span>
                                          <?php } }else{ ?>
                                                ❌ NA
                                        <?php  } ?>
                                        </td>
                                        <td class="text-capitalize">
                                          <?php 
                                          if($row->same_day_request_id !=''){
                                          if($row->plan_approvel_remarks != ''){ ?>
                                              <?=$row->plan_approvel_remarks?>
                                          <?php } else { ?>
                                            <span class="bg-warning p-1">Pending</span>
                                          <?php } }else{ ?>
                                                ❌ NA
                                        <?php  } ?>
                                        </td>
                                        <td class="text-capitalize">
                                          <?php
                                          if($row->same_day_request_id !=''){
                                          if($row->plan_apr_time != '' && $row->plan_approvel_status != ''){ ?>
                                              <?=$row->plan_apr_time?>
                                          <?php } else { ?>
                                             <span class="bg-warning p-1">Pending</span>
                                          <?php } }else{ ?>
                                                ❌ NA
                                        <?php  } ?>
                                        </td>
                                        <td class="text-capitalize">
                                          <?php 
                                           if($row->same_day_request_id !=''){
                                          if($row->plan_created_at != ''){ ?>
                                              <?=$row->plan_created_at?>
                                          <?php } else { ?>
                                              ❌ NA
                                          <?php } }else{ ?>
                                                ❌ NA
                                        <?php  } ?>
                                        </td>
                                    <?php } ?>

                                    </tr>
                            <?php $i++; } ?>
                            </tbody>
                        </table>
                  
                        <?php } ?>
                        
                </div>
                <hr/>
              </div>
            </div>
            </div>
          </div>
        </section>
       

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
          "buttons": ["csv", "excel", "pdf"]
        }).buttons().container().appendTo('#example1_wrapper .col-md-6:eq(0)');
        $("#example2").DataTable({
          "responsive": false, "lengthChange": false, "autoWidth": false,
          "buttons": ["csv", "excel", "pdf"]
        }).buttons().container().appendTo('#example2_wrapper .col-md-6:eq(0)');
      </script>
    
    </body>
</html>

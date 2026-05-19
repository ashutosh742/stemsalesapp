<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Funnel Details | STEM APP | WebAPP</title>
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
                      <h1 class="m-0 premium-color-1" style="color: #ff009b;">Funnel Details</h1>
                      <p class="premium-color-2" style="color: #ff0000;">This section provides detailed insights into your funnel performance and metrics.</p>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <br>
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
                    <form action="<?=base_url()?>Reports/OurTeamFunnal" method="post" class="mt-3">
                      <div class="row mb-4">
                        <!-- <div class="col">
                          <label for="selectedDate">Start Date</label>
                          <input type="date" value="<?=$sdate;?>" id="selectedDate" max="<?=date('Y-m-d')?>" name="sdate" style="width: 200px;" class="form-control">
                          </div>
                          <div class="col">
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
                          <label for="selectedDate">Select RM</label>
                          <select id="rm_filter" name="rm_filter" class="form-control">
                            <?php if($user['type_id'] !== 3){ ?> 
                            <option value="all" <?= ($rm_filter == 'all') ? 'selected' : ''; ?>>All</option>
                            <?php } ?>
                            <?php foreach ($getTeamsDatas as $getTeamsData) { ?>
                            <option value="<?= $getTeamsData->user_id; ?>" <?= ($getTeamsData->user_id == $rm_filter) ? 'selected' : ''; ?>>
                              <?= $getTeamsData->name; ?>
                            </option>
                            <?php }?>
                          </select>
                        </div>
                        <div class="col text-center">
                          <div class="form-group">
                            <button type="submit" class="btn btn-primary p-1" style="margin-top: 33px; width: 100px;">Filter</button>
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
          </div>
        </div>
        <!-- Main content -->
        <hr>
        <section class="content">
          <div class="container-fluid">
            <?php 
              $total_funnels_status         = $totalFunnnels['total_funnels_status'];
              $total_funnels_old_category   = $totalFunnnels['total_funnels_old_category'];
              $total_funnels_new_category   = $totalFunnnels['total_funnels_new_category'];
              $total_funnels_assign         = $totalFunnnels['total_funnels_assign'];
              $total_funnel_by_user         = $totalFunnnels['total_funnel_by_user'];
              $total_funnels_partner        = $totalFunnnels['total_funnels_partner'];
              $total_funnels_partner_user   = $totalFunnnels['total_funnels_partner_user'];
              $total_current_quarter        = $totalFunnnels['total_current_quarter'];

              $totalClusterDatas                          = $totalFunnnels['totalClusterDatas'];
              $totalClusterBYBDatas                       = $totalFunnnels['totalClusterBYBDatas'];
              $totalClusterBYClusterNameBYBDDatas         = $totalFunnnels['totalClusterBYClusterNameBYBDDatas'];
              $totalClusterBYStatusDatas         = $totalFunnnels['totalClusterBYStatusDatas'];
              
              //  dd($totalClusterDatas);
              ?>          
                <div class="card-section" style="background: linear-gradient(to right, #ede7f6, #ffffff); border-radius: 12px; box-shadow: 0 4px 8px rgba(0,0,0,0.1); padding: 20px;">
                   <div class="text-center" style="background: linear-gradient(to right, #ede7f6, #ffffff); border-radius: 12px; box-shadow: 0 4px 8px rgba(0,0,0,0.1); padding: 20px;">
                       <h5>
  <i>📋 Total Funnel With Status Wise</i> <br>
  <small>
    📈 This list provides a comprehensive explanation of various funnel status categories used to track and analyze lead progression, from initial contact to final onboarding. Each status highlights a specific stage or outcome in the funnel management process, helping teams monitor performance, prioritize actions, and improve conversion strategies. 🎯
  </small>
</h5>
                    </div>
                    <hr>
                    <div class="row">
                    <?php  
                        $descriptions = [
                            'total' => 'Total number of funnel entries handled.',
                            'open' => 'Funnel entries currently marked as Open.',
                            'reachout' => 'Entries where initial reachout has been made.',
                            'tentative' => 'Entries with a tentative interest or response.',
                            'will_do_later' => 'Entries marked for follow-up or later action.',
                            'not_interested' => 'Prospects who are not interested.',
                            'positive' => 'Entries with a positive response or intent.',
                            'closure' => 'Successfully closed or completed funnel entries.',
                            'open_rpem' => 'Open funnel entries with RPM (Regional Performance Manager) tag.',
                            'very_positive' => 'Highly interested or strong positive response entries.',
                            'ttd_reachout' => 'Reachouts performed through TTD (Track the Deal).',
                            'wno_reachout' => 'Reachouts tagged as WNO (Wrong Number/Out of Scope).',
                            'positive_nap' => 'Positive responses from NAP (Not A Prospect) tagged entries.',
                            'very_positive_nap' => 'Very positive responses from NAP tagged entries.',
                            'on_boarded' => 'Entries that have been successfully onboarded.'
                        ];
                        foreach ($total_funnels_status as $tasktypeData) {
                        foreach ($tasktypeData as $key => $value) {
                            if (in_array($key, ['user_id', 'username'])) continue; // Skip these
                        
                            $r = rand(150, 255);
                            $g = rand(150, 255);
                            $b = rand(150, 255);
                            $backgroundColor = "rgba($r, $g, $b,.3)";
                        
                            $hue = rand(0, 360);
                            $saturation = rand(60, 100);
                            $lightness = rand(30, 45);
                            $backgroundColorNew = "hsl($hue, $saturation%, $lightness%)";
                        
                            // Format heading nicely
                            $heading = ucwords(str_replace('_', ' ', $key));
                            $description = isset($descriptions[$key]) ? $descriptions[$key] : 'Task metric description not available.';
                            ?>
                    <div class="col-md-3 mb-3">
                        <div class="card text-center shadow" style="background-color: <?= $backgroundColor ?>; color: <?= $backgroundColorNew ?>;">
                        <div class="card-body" style="color:<?=$backgroundColorNew;?>!important">
                            <h5 class="card-title"><b><?= $heading ?></b></h5>
                            <small><?= $description ?></small> 
                            <hr>
                            <a target="_BLANK" class="card-count-heading-new" style="color:<?=$backgroundColorNew;?>!important" href="<?=base_url()?>Reports/FunnelDetails/<?=$key?>/<?=$uid?>">
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

                  <hr>

                    <div class="card-section" style="background: linear-gradient(to right, #ede7f6, #ffffff); border-radius: 12px; box-shadow: 0 4px 8px rgba(0,0,0,0.1); padding: 20px;">
                   <div class="text-center" style="background: linear-gradient(to right, #ede7f6, #ffffff); border-radius: 12px; box-shadow: 0 4px 8px rgba(0,0,0,0.1); padding: 20px;">
                      <h5>
                      <i>📑 Funnel Category Descriptions</i> <br>
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
                            'Focus_Funnel' => 'Companies currently targeted in the focus funnel strategy.',
                            'Upsell_Client' => 'Clients with potential for upselling products or services.',
                            'Key_Client' => 'Important clients considered key to business success.',
                            'Positive_Key_Client' => 'Key clients with positive engagement or outcomes.',
                            'Priority_Calling' => 'Clients marked for priority follow-up calls.',
                            'Top_Spender' => 'Clients that are top spenders or highest contributors to revenue.',
                            'Potential_Client' => 'Leads or companies identified as potential future clients.'
                        ];
                        foreach ($total_funnels_old_category as $tasktypeData) {
                        foreach ($tasktypeData as $key => $value) {
                            if (in_array($key, ['user_id', 'username'])) continue; // Skip these
                        
                            $r = rand(150, 255);
                            $g = rand(150, 255);
                            $b = rand(150, 255);
                            $backgroundColor = "rgba($r, $g, $b,.3)";
                        
                            $hue = rand(0, 360);
                            $saturation = rand(60, 100);
                            $lightness = rand(30, 45);
                            $backgroundColorNew = "hsl($hue, $saturation%, $lightness%)";
                        
                            // Format heading nicely
                            $heading = ucwords(str_replace('_', ' ', $key));
                            $description = isset($descriptions[$key]) ? $descriptions[$key] : 'Task metric description not available.';
                            ?>
                    <div class="col-md-3 mb-3">
                        <div class="card text-center shadow" style="background-color: <?= $backgroundColor ?>; color: <?= $backgroundColorNew ?>;">
                        <div class="card-body" style="color:<?=$backgroundColorNew;?>!important">
                            <h5 class="card-title"><b><?= $heading ?></b></h5>
                            <small><?= $description ?></small> 
                            <hr>
                            <a target="_BLANK" class="card-count-heading-new" style="color:<?=$backgroundColorNew;?>!important" href="<?=base_url()?>Reports/FunnelDetails/<?=$key?>/<?=$uid?>">
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


                        <hr>
                        <div class="card-section" style="background: linear-gradient(to right, #ede7f6, #ffffff); border-radius: 12px; box-shadow: 0 4px 8px rgba(0,0,0,0.1); padding: 20px;">
                   <div class="text-center" style="background: linear-gradient(to right, #ede7f6, #ffffff); border-radius: 12px; box-shadow: 0 4px 8px rgba(0,0,0,0.1); padding: 20px;">
                      <h5>
  <i>📂 Funnel Category Overview</i> <br>
  <small>
    🗂️ This dataset presents a categorized summary of companies based on their position within various sales funnel stages. It includes the total number of companies and breaks them down into specific strategic groups, such as 🎯 Q1 20 closure targets, 💰 financial year potential leads, 🌱 nurturing prospects, and 🆕 new leads added under key initiatives.
  </small>
</h5>

                    </div>
                    <hr>
                    <div class="row">
                    <?php  
                     $descriptions = [
                        'total' => 'Total number of companies in all funnel categories.',
                        'q1_twetenty_closure_funnel' => 'Companies targeted for Q1 2020 closure funnel.',
                        'potential_funnel_for_fy' => 'Companies identified as potential funnel for the financial year.',
                        'to_be_nurtured_for_fy' => 'Companies to be nurtured for engagement in the financial year.',
                        'fifity_new_lead_funnel' => 'New leads added under the Fifty New Lead Funnel initiative.'
                    ];

                        foreach ($total_funnels_new_category as $tasktypeData) {
                        foreach ($tasktypeData as $key => $value) {
                            if (in_array($key, ['user_id', 'username'])) continue; // Skip these
                        
                            $r = rand(150, 255);
                            $g = rand(150, 255);
                            $b = rand(150, 255);
                            $backgroundColor = "rgba($r, $g, $b,.3)";
                        
                            $hue = rand(0, 360);
                            $saturation = rand(60, 100);
                            $lightness = rand(30, 45);
                            $backgroundColorNew = "hsl($hue, $saturation%, $lightness%)";
                        
                            // Format heading nicely
                            $heading = ucwords(str_replace('_', ' ', $key));
                            $description = isset($descriptions[$key]) ? $descriptions[$key] : 'Task metric description not available.';
                            ?>
                    <div class="col-md-3 mb-3">
                        <div class="card text-center shadow" style="background-color: <?= $backgroundColor ?>; color: <?= $backgroundColorNew ?>;">
                        <div class="card-body" style="color:<?=$backgroundColorNew;?>!important">
                            <h5 class="card-title"><b><?= $heading ?></b></h5>
                            <small><?= $description ?></small> 
                            <hr>
                            <a target="_BLANK" class="card-count-heading-new" style="color:<?=$backgroundColorNew;?>!important" href="<?=base_url()?>Reports/FunnelDetails/<?=$key?>/<?=$uid?>">
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


                  <hr>
                        <div class="card-section" style="background: linear-gradient(to right, #ede7f6, #ffffff); border-radius: 12px; box-shadow: 0 4px 8px rgba(0,0,0,0.1); padding: 20px;">
                   <div class="text-center" style="background: linear-gradient(to right, #ede7f6, #ffffff); border-radius: 12px; box-shadow: 0 4px 8px rgba(0,0,0,0.1); padding: 20px;">
                     <h5>
  <i>📋 Funnel Assignment Breakdown by Role</i> <br>
  <small>
    📊 This dataset provides a detailed breakdown of funnel assignments across various organizational roles, highlighting the number of funnels allocated by or under specific teams and managerial positions such as 👤 PST, 👥 Cluster Managers, 🧑‍💼 Assistant Sales Heads (NAE/W/S), and 🧭 Regional Managers. It enables insights into how funnel responsibilities are distributed throughout the hierarchy. 🧱
  </small>
</h5>

                    </div>
                    <hr>
                    <div class="row">
                    <?php  
                    $descriptions = [
                        'total' => 'Total number of funnels assigned, regardless of role.',
                        'PST_assign_funnel' => 'Funnels assigned by or to the PST (Pre-Sales Team).',
                        'cluster_manager_assign_funnel' => 'Funnels assigned under the Cluster Manager.',
                        'PST_and_cluster_manager_assign_funnel' => 'Funnels jointly assigned by PST and Cluster Manager.',
                        'Assistant_Sales_Head_(NAE)_assign_funnel' => 'Funnels assigned by Assistant Sales Head (North & East region).',
                        'Assistant_Sales_Head_(W)_assign_funnel' => 'Funnels assigned by Assistant Sales Head (West region).',
                        'Assistant_Sales_Head_(S)_assign_funnel' => 'Funnels assigned by Assistant Sales Head (South region).',
                        'RM_East_assign_funnel' => 'Funnels assigned by Regional Manager (East).',
                        'RM_North_assign_funnel' => 'Funnels assigned by Regional Manager (North).',
                        'Assistant_Cluster_Manager_assign_funnel' => 'Funnels assigned by Assistant Cluster Manager.',
                    ];


                        foreach ($total_funnels_assign as $tasktypeData) {
                        foreach ($tasktypeData as $key => $value) {
                            if (in_array($key, ['user_id', 'username'])) continue; // Skip these
                        
                            $r = rand(150, 255);
                            $g = rand(150, 255);
                            $b = rand(150, 255);
                            $backgroundColor = "rgba($r, $g, $b,.3)";
                        
                            $hue = rand(0, 360);
                            $saturation = rand(60, 100);
                            $lightness = rand(30, 45);
                            $backgroundColorNew = "hsl($hue, $saturation%, $lightness%)";
                        
                            // Format heading nicely
                            $heading = ucwords(str_replace('_', ' ', $key));
                            $description = isset($descriptions[$key]) ? $descriptions[$key] : 'Task metric description not available.';
                            ?>
                    <div class="col-md-3 mb-3">
                        <div class="card text-center shadow" style="background-color: <?= $backgroundColor ?>; color: <?= $backgroundColorNew ?>;">
                        <div class="card-body" style="color:<?=$backgroundColorNew;?>!important">
                            <h5 class="card-title"><b><?= $heading ?></b></h5>
                            <small><?= $description ?></small> 
                            <hr>
                            <a target="_BLANK" class="card-count-heading-new" style="color:<?=$backgroundColorNew;?>!important" href="<?=base_url()?>Reports/FunnelDetails/<?=$key?>/<?=$uid?>">
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



                    <hr>
                        <div class="card-section" style="background: linear-gradient(to right, #ede7f6, #ffffff); border-radius: 12px; box-shadow: 0 4px 8px rgba(0,0,0,0.1); padding: 20px;">
                   <div class="text-center" style="background: linear-gradient(to right, #ede7f6, #ffffff); border-radius: 12px; box-shadow: 0 4px 8px rgba(0,0,0,0.1); padding: 20px;">
                       <h5>
  <i>🤝 Total Partner Funnel</i> <br>
  <small>
    — 📊 Total, 🏢 Corporate, 🏛 PSU, 🤝 NGO, 🏫 Private School, 🧍 Individual, 🏬 Govt Off, 📦 Other, 💻 Online, 🧪 STEM School, 🏛 Foundation, 🌍 MNC, 📞 HO Leads, 🏞️ DMF, 🎖️ Elected Representatives
  </small>
</h5>

                    </div>
                    <hr>
                    <div class="row">
                    <?php  
                    $descriptions = [
                            'total' => 'Total number of leads across all categories.',
                            'corporate' => 'Leads from corporate organizations.',
                            'PSU' => 'Leads from Public Sector Units (PSUs).',
                            'NGO' => 'Leads from Non-Governmental Organizations.',
                            'Private_School' => 'Leads from private schools.',
                            'Individual' => 'Leads from individual persons.',
                            'Govt_Off' => 'Leads from government offices.',
                            'Other' => 'Leads that do not fall under any predefined category.',
                            'Online' => 'Leads received through online platforms.',
                            'STEM_School' => 'Leads from STEM (Science, Technology, Engineering, Math) focused schools.',
                            'Foundation' => 'Leads from foundations or charitable trusts.',
                            'MNC' => 'Leads from multinational corporations.',
                            'HO_Leads' => 'Leads generated directly by the Head Office.',
                            'DMF' => 'Leads from District Mineral Foundation programs.',
                            'Elected_Representatives' => 'Leads from elected government representatives.'
                        ];


                        foreach ($total_funnels_partner as $tasktypeData) {
                        foreach ($tasktypeData as $key => $value) {
                            if (in_array($key, ['user_id', 'username'])) continue; // Skip these
                        
                            $r = rand(150, 255);
                            $g = rand(150, 255);
                            $b = rand(150, 255);
                            $backgroundColor = "rgba($r, $g, $b,.3)";
                        
                            $hue = rand(0, 360);
                            $saturation = rand(60, 100);
                            $lightness = rand(30, 45);
                            $backgroundColorNew = "hsl($hue, $saturation%, $lightness%)";
                        
                            // Format heading nicely
                            $heading = ucwords(str_replace('_', ' ', $key));
                            $description = isset($descriptions[$key]) ? $descriptions[$key] : 'Task metric description not available.';
                            ?>
                    <div class="col-md-3 mb-3">
                        <div class="card text-center shadow" style="background-color: <?= $backgroundColor ?>; color: <?= $backgroundColorNew ?>;">
                        <div class="card-body" style="color:<?=$backgroundColorNew;?>!important">
                            <h5 class="card-title"><b><?= $heading ?></b></h5>
                            <small><?= $description ?></small> 
                            <hr>
                            <a target="_BLANK" class="card-count-heading-new" style="color:<?=$backgroundColorNew;?>!important" href="<?=base_url()?>Reports/FunnelDetails/<?=$key?>/<?=$uid?>">
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





                 <hr>
                        <div class="card-section" style="background: linear-gradient(to right, #ede7f6, #ffffff); border-radius: 12px; box-shadow: 0 4px 8px rgba(0,0,0,0.1); padding: 20px;">
                   <div class="text-center" style="background: linear-gradient(to right, #ede7f6, #ffffff); border-radius: 12px; box-shadow: 0 4px 8px rgba(0,0,0,0.1); padding: 20px;">
                    <?php  
                    $cfData = $this->Menu_model->getCurrentFinancialYearAndQuarter();
                    $currentQuarter = "Quarter ".$cfData['quarter'];
                    ?>
                      <h5><i>Current 📊 <?=$currentQuarter?> Funnel </i><br>
                           <small>📈 Current Quarter Funnel provides a detailed breakdown of various segments and categories that are being tracked within the current quarter. This 🌀 funnel visualization helps in understanding the distribution and flow of leads or engagements across different sectors. Here's a brief description of each category included in the funnel.</small>
                      </h5>
                    </div>
                    <hr>
                    <div class="row">
                    <?php  
                    $descriptions = [
                      'total' => 'Total number of funnel entries.',
                      'current_quarter_twetenty_closure_funnel' => 'Funnel entries expected to close in the current quarter (20% probability).',
                      'current_quarter_potential_funnel_for_fy' => 'Potential funnel entries for the current financial year.',
                      'current_quarter_to_be_nurtured_for_fy' => 'Funnel entries to be nurtured for future conversion in the current financial year.'
                  ];



                        foreach ($total_current_quarter as $tasktypeData) {
                        foreach ($tasktypeData as $key => $value) {
                            if (in_array($key, ['user_id', 'username'])) continue; // Skip these
                        
                            $r = rand(150, 255);
                            $g = rand(150, 255);
                            $b = rand(150, 255);
                            $backgroundColor = "rgba($r, $g, $b,.3)";
                        
                            $hue = rand(0, 360);
                            $saturation = rand(60, 100);
                            $lightness = rand(30, 45);
                            $backgroundColorNew = "hsl($hue, $saturation%, $lightness%)";
                        
                            // Format heading nicely
                            $heading = ucwords(str_replace('_', ' ', $key));
                            $description = isset($descriptions[$key]) ? $descriptions[$key] : 'Task metric description not available.';
                            ?>
                    <div class="col-md-3 mb-3">
                        <div class="card text-center shadow" style="background-color: <?= $backgroundColor ?>; color: <?= $backgroundColorNew ?>;">
                        <div class="card-body" style="color:<?=$backgroundColorNew;?>!important">
                            <h5 class="card-title"><b><?= $heading ?></b></h5>
                            <small><?= $description ?></small> 
                            <hr>
                            <a target="_BLANK" class="card-count-heading-new" style="color:<?=$backgroundColorNew;?>!important" href="<?=base_url()?>Reports/FunnelDetails/<?=$key?>/<?=$uid?>">
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


<?php 
// dd($totalClusterDatas);
?>


                <hr>

                     <div class="card-section" style="background: linear-gradient(to right, #ede7f6, #ffffff); border-radius: 12px; box-shadow: 0 4px 8px rgba(0,0,0,0.1); padding: 20px;">
                   <div class="text-center" style="background: linear-gradient(to right, #ede7f6, #ffffff); border-radius: 12px; box-shadow: 0 4px 8px rgba(0,0,0,0.1); padding: 20px;">
                       <h5>
                    <i>✨ Travel Type Categorization Summary</i> <br>
                    <small>
                     ✨ Travel Type Categorization Summary 🧭 offers a comprehensive overview of how companies are distributed across different travel types. It classifies records into 📍 Base Location, 🧳 OutStation, 🕳️ Marked But Not Set, and ❓ Not Set clusters. This summary helps in identifying travel trends, optimizing field operations, and ensuring accurate cluster tagging for efficient 🗂️ planning, 📊 reporting, and 🚀 decision-making across various teams and regions.
                    </small>
                  </h5>

                    </div>
                    <hr>
                    <div class="row">
                    <?php  
                        $descriptions = [
                            'total' => 'Total number of unique company IDs handled in the dataset.',
                            'base_location' => 'Entries categorized as "base" travel type with valid cluster mapping.',
                            'out_Station_location' => 'Entries marked as "outStation" travel type with valid cluster mapping.',
                            'marked_travel_cluster_but_base_out_not_set' => 'Entries marked but missing proper base or outStation cluster association.',
                            'not_set_travel_cluster' => 'Entries where cluster_id is either NULL or not set (0), indicating no cluster assignment.',
                        ];

                        foreach ($totalClusterDatas as $tasktypeData) {
                        foreach ($tasktypeData as $key => $value) {
                            if (in_array($key, ['user_id', 'username'])) continue; // Skip these
                        
                            $r = rand(150, 255);
                            $g = rand(150, 255);
                            $b = rand(150, 255);
                            $backgroundColor = "rgba($r, $g, $b,.3)";
                        
                            $hue = rand(0, 360);
                            $saturation = rand(60, 100);
                            $lightness = rand(30, 45);
                            $backgroundColorNew = "hsl($hue, $saturation%, $lightness%)";
                        
                            // Format heading nicely
                            $heading = ucwords(str_replace('_', ' ', $key));
                            $description = isset($descriptions[$key]) ? $descriptions[$key] : 'Task metric description not available.';
                            ?>
                    <div class="col-md-3 mb-3">
                        <div class="card text-center shadow" style="background-color: <?= $backgroundColor ?>; color: <?= $backgroundColorNew ?>;height: 230px;">
                        <div class="card-body" style="color:<?=$backgroundColorNew;?>!important">
                            <h5 class="card-title"><b><?= $heading ?></b></h5>
                            <small><?= $description ?></small> 
                            <hr>
                            <a target="_BLANK" class="card-count-heading-new" style="color:<?=$backgroundColorNew;?>!important" href="<?=base_url()?>Reports/FunnelDetailsWithCluster/<?=$key?>/<?=$uid?>">
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
                <hr>
              <div class="card">
              <div class="text-center" style="background: linear-gradient(to right, #ede7f6, #ffffff); border-radius: 12px; box-shadow: 0 4px 8px rgba(0,0,0,0.1); padding: 20px;">
                <h5>
  <i>📊 User Funnel Overview with Status-Wise Insights</i> <br>
  <small>
    📈 This data represents a detailed breakdown of funnel performance and categorization for individual users, highlighting key metrics such as total leads, stage-wise funnel status (e.g., 📂 Open, 📞 Reachout, 📅 Tentative), and strategic classifications (e.g., 🚀 Focus Funnel, 🏢 Key Client, ☎️ Priority Calling). It also includes 🧑‍💼 role-based funnel assignments across different management levels to facilitate performance analysis and strategic follow-up planning. 📌
  </small>
</h5>
              </div>
              <hr>
              <div class="page-header" style="background: linear-gradient(to right, #ffffff, #ffffff); border-radius: 12px; box-shadow: 0 4px 8px rgba(0,0,0,0.1); padding: 20px;">
                <div class="table-responsive text-nowrap">
                  <table id="example1" class="table table-striped table-bordered" cellspacing="0" width="100%">
                    <thead class="thead-dark">
                      <tr>
                          <th>#</th>
                          <th>👤 Name</th>
                          <th>📊 Total</th>
                          <th>📂 Open</th>
                          <th>📞 Reachout</th>
                          <th>🕒 Tentative</th>
                          <th>⏳ Later</th>
                          <th>🚫 Not Interested</th>
                          <th>👍 Positive</th>
                          <th>✅ Closure</th>
                          <th>📁 Open RPEM</th>
                          <th>🌟 Very Positive</th>
                          <th>📬 TTD-Reachout</th>
                          <th>📩 WNO-Reachout</th>
                          <th>🟢 Positive-NAP</th>
                          <th>💚 Very Positive-NAP</th>
                          <th>🛫 On-Boarded</th>
                        </tr>
                    </thead>
                    <tbody>
                      <?php
                        $i = 1;
                        foreach ($total_funnel_by_user as $row) {
                          $meeting_user_id = $row->current_user_id;
                        
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
                        <td>
                          <a target="_BLANK" class="card-count-heading-new" style="color:<?=$backgroundColorNew;?>!important" href="<?=base_url()?>Reports/FunnelDetails/total/<?=$meeting_user_id?>/userwise">
                          <?= $row->total; ?>
                          </a>
                        </td>
                        <td>
                          <a target="_BLANK" class="card-count-heading-new" style="color:<?=$backgroundColorNew;?>!important" href="<?=base_url()?>Reports/FunnelDetails/open/<?=$meeting_user_id?>/userwise">
                          <?= $row->open; ?>
                          </a>
                        </td>
                        <td>
                          <a target="_BLANK" class="card-count-heading-new" style="color:<?=$backgroundColorNew;?>!important" href="<?=base_url()?>Reports/FunnelDetails/reachout/<?=$meeting_user_id?>/userwise">
                          <?= $row->reachout; ?>
                          </a>
                        </td>
                        <td>
                          <a target="_BLANK" class="card-count-heading-new" style="color:<?=$backgroundColorNew;?>!important" href="<?=base_url()?>Reports/FunnelDetails/tentative/<?=$meeting_user_id?>/userwise">
                          <?= $row->tentative; ?>
                          </a>
                        </td>
                        <td>
                          <a target="_BLANK" class="card-count-heading-new" style="color:<?=$backgroundColorNew;?>!important" href="<?=base_url()?>Reports/FunnelDetails/will_do_later/<?=$meeting_user_id?>/userwise">
                          <?= $row->will_do_later; ?>
                          </a>
                        </td>
                        <td>
                          <a target="_BLANK" class="card-count-heading-new" style="color:<?=$backgroundColorNew;?>!important" href="<?=base_url()?>Reports/FunnelDetails/not_interested/<?=$meeting_user_id?>/userwise">
                          <?= $row->not_interested; ?>
                          </a>
                        </td>
                        <td>
                          <a target="_BLANK" class="card-count-heading-new" style="color:<?=$backgroundColorNew;?>!important" href="<?=base_url()?>Reports/FunnelDetails/positive/<?=$meeting_user_id?>/userwise">
                          <?= $row->positive; ?>
                          </a>
                        </td>
                        <td>
                          <a target="_BLANK" class="card-count-heading-new" style="color:<?=$backgroundColorNew;?>!important" href="<?=base_url()?>Reports/FunnelDetails/closure/<?=$meeting_user_id?>/userwise">
                          <?= $row->closure; ?>
                          </a>
                        </td>
                        <td>
                          <a target="_BLANK" class="card-count-heading-new" style="color:<?=$backgroundColorNew;?>!important" href="<?=base_url()?>Reports/FunnelDetails/open_rpem/<?=$meeting_user_id?>/userwise">
                          <?= $row->open_rpem; ?>
                          </a>
                        </td>
                        <td>
                          <a target="_BLANK" class="card-count-heading-new" style="color:<?=$backgroundColorNew;?>!important" href="<?=base_url()?>Reports/FunnelDetails/very_positive/<?=$meeting_user_id?>/userwise">
                          <?= $row->very_positive; ?>
                          </a>
                        </td>
                        <td>
                          <a target="_BLANK" class="card-count-heading-new" style="color:<?=$backgroundColorNew;?>!important" href="<?=base_url()?>Reports/FunnelDetails/ttd_reachout/<?=$meeting_user_id?>/userwise">
                          <?= $row->ttd_reachout; ?>
                          </a>
                        </td>
                        <td>
                          <a target="_BLANK" class="card-count-heading-new" style="color:<?=$backgroundColorNew;?>!important" href="<?=base_url()?>Reports/FunnelDetails/wno_reachout/<?=$meeting_user_id?>/userwise">
                          <?= $row->wno_reachout; ?>
                          </a>
                        </td>
                        <td>
                          <a target="_BLANK" class="card-count-heading-new" style="color:<?=$backgroundColorNew;?>!important" href="<?=base_url()?>Reports/FunnelDetails/positive_nap/<?=$meeting_user_id?>/userwise">
                          <?= $row->positive_nap; ?>
                          </a>
                        </td>
                        <td>
                          <a target="_BLANK" class="card-count-heading-new" style="color:<?=$backgroundColorNew;?>!important" href="<?=base_url()?>Reports/FunnelDetails/very_positive_nap/<?=$meeting_user_id?>/userwise">
                          <?= $row->very_positive_nap; ?>
                          </a>
                        </td>
                        <td>
                          <a target="_BLANK" class="card-count-heading-new" style="color:<?=$backgroundColorNew;?>!important" href="<?=base_url()?>Reports/FunnelDetails/on_boarded/<?=$meeting_user_id?>/userwise">
                          <?= $row->on_boarded; ?>
                          </a>
                        </td>
                      </tr>
                      <?php $i++; } ?>
                    </tbody>
                  </table>
                </div>
              </div>
              </div>




                <hr>
              <div class="card">
              <div class="text-center" style="background: linear-gradient(to right, #ede7f6, #ffffff); border-radius: 12px; box-shadow: 0 4px 8px rgba(0,0,0,0.1); padding: 20px;">
                <h5>
  <i>📊 User Funnel Overview with Category-Wise Insights</i>
  <br>
  <small>
    📈 This data represents a detailed breakdown of funnel performance and categorization for individual users, highlighting key metrics such as total leads, stage-wise funnel status (e.g., 📂 Open, 📞 Reachout, 📅 Tentative), and strategic classifications (e.g., 🚀 Focus Funnel, 🏢 Key Client, 📞 Priority Calling). It also includes 🧑‍💼 role-based funnel assignments across different management levels to facilitate performance analysis and strategic follow-up planning. 📌
  </small>
</h5>

              </div>
              <hr>
              <div class="page-header" style="background: linear-gradient(to right, #ffffff, #ffffff); border-radius: 12px; box-shadow: 0 4px 8px rgba(0,0,0,0.1); padding: 20px;">
                <div class="table-responsive text-nowrap">
                  <table id="example2" class="table table-striped table-bordered" cellspacing="0" width="100%">
                    <thead class="thead-dark">
                      <tr>
                        <th>#</th>
                        <th>👤 Name</th>
                        <th>📊 Total</th>
                        <th>🎯 Focus Funnel</th>
                        <th>📈 Upsell</th>
                        <th>🔑 Key Client</th>
                        <th>➕ Positive Key</th>
                        <th>📞 Priority Call</th>
                        <th>💰 Top Spender</th>
                        <th>🌟 Potential</th>
                      </tr>
                    </thead>
                    <tbody>
                      <?php
                        $i = 1;
                        foreach ($total_funnel_by_user as $row) {
                          $meeting_user_id = $row->current_user_id;
                        
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
                        <td>
                          <a target="_BLANK" class="card-count-heading-new" style="color:<?=$backgroundColorNew;?>!important" href="<?=base_url()?>Reports/FunnelDetails/total/<?=$meeting_user_id?>/userwise">
                          <?= $row->total; ?>
                          </a>
                        </td>
                        <td>
                          <a target="_BLANK" class="card-count-heading-new" style="color:<?=$backgroundColorNew;?>!important" href="<?=base_url()?>Reports/FunnelDetails/Focus_Funnel/<?=$meeting_user_id?>/userwise">
                          <?= $row->Focus_Funnel; ?>
                          </a>
                        </td>
                        <td>
                          <a target="_BLANK" class="card-count-heading-new" style="color:<?=$backgroundColorNew;?>!important" href="<?=base_url()?>Reports/FunnelDetails/Upsell_Client/<?=$meeting_user_id?>/userwise">
                          <?= $row->Upsell_Client; ?>
                          </a>
                        </td>
                        <td>
                          <a target="_BLANK" class="card-count-heading-new" style="color:<?=$backgroundColorNew;?>!important" href="<?=base_url()?>Reports/FunnelDetails/Key_Client/<?=$meeting_user_id?>/userwise">
                          <?= $row->Key_Client; ?>
                          </a>
                        </td>
                        <td>
                          <a target="_BLANK" class="card-count-heading-new" style="color:<?=$backgroundColorNew;?>!important" href="<?=base_url()?>Reports/FunnelDetails/Positive_Key_Client/<?=$meeting_user_id?>/userwise">
                          <?= $row->Positive_Key_Client; ?>
                          </a>
                        </td>
                        <td>
                          <a target="_BLANK" class="card-count-heading-new" style="color:<?=$backgroundColorNew;?>!important" href="<?=base_url()?>Reports/FunnelDetails/Priority_Calling/<?=$meeting_user_id?>/userwise">
                          <?= $row->Priority_Calling; ?>
                          </a>
                        </td>
                        <td>
                          <a target="_BLANK" class="card-count-heading-new" style="color:<?=$backgroundColorNew;?>!important" href="<?=base_url()?>Reports/FunnelDetails/Top_Spender/<?=$meeting_user_id?>/userwise">
                          <?= $row->Top_Spender; ?>
                          </a>
                        </td>
                        <td>
                          <a target="_BLANK" class="card-count-heading-new" style="color:<?=$backgroundColorNew;?>!important" href="<?=base_url()?>Reports/FunnelDetails/Potential_Client/<?=$meeting_user_id?>/userwise">
                          <?= $row->Potential_Client; ?>
                          </a>
                        </td>
                      </tr>
                      <?php $i++; } ?>
                    </tbody>
                  </table>
                </div>
              </div>
              </div>




            <hr>
              <div class="card">
              <div class="text-center" style="background: linear-gradient(to right, #ede7f6, #ffffff); border-radius: 12px; box-shadow: 0 4px 8px rgba(0,0,0,0.1); padding: 20px;">
               <h5>
  <i>📊 User Funnel Overview with New Category-Wise Insights</i> <br>
<small>
  📈 This data breaks down user funnel performance by key categories: 👤 user names, 📊 total leads, 🚀 Q1 20 closure targets, 🎯 financial year potential clients, 🌱 prospects to be nurtured, 🆕 new 50-lead funnel additions, and ⚓ anchor clients. It provides clear insights to optimize strategies and track progress across these important segments.
</small>

</h5>

              </div>
              <hr>
              <div class="page-header" style="background: linear-gradient(to right, #ffffff, #ffffff); border-radius: 12px; box-shadow: 0 4px 8px rgba(0,0,0,0.1); padding: 20px;">
                <div class="table-responsive text-nowrap">
                  <table id="example3" class="table table-striped table-bordered" cellspacing="0" width="100%">
                    <thead class="thead-dark">
                      <tr>
                        <th>#</th>
                        <th>👤 Name</th>
                        <th>📊 Total</th>
                        <th>🚀 Q1 20 Closure</th>
                        <th>🎯 FY Potential</th>
                        <th>🌱 To Be Nurtured</th>
                        <th>🆕 50-Lead Funnel</th>
                        <th>⚓ Anchor Clients</th>
                      </tr>
                    </thead>
                    <tbody>
                      <?php
                        $i = 1;
                        foreach ($total_funnel_by_user as $row) {
                          $meeting_user_id = $row->current_user_id;
                        
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
                        <td>
                          <a target="_BLANK" class="card-count-heading-new" style="color:<?=$backgroundColorNew;?>!important" href="<?=base_url()?>Reports/FunnelDetails/total/<?=$meeting_user_id?>/userwise">
                          <?= $row->total; ?>
                          </a>
                        </td>
                        <td>
                          <a target="_BLANK" class="card-count-heading-new" style="color:<?=$backgroundColorNew;?>!important" href="<?=base_url()?>Reports/FunnelDetails/q1_twetenty_closure_funnel/<?=$meeting_user_id?>/userwise">
                          <?= $row->q1_twetenty_closure_funnel; ?>
                          </a>
                        </td>
                        <td>
                          <a target="_BLANK" class="card-count-heading-new" style="color:<?=$backgroundColorNew;?>!important" href="<?=base_url()?>Reports/FunnelDetails/potential_funnel_for_fy/<?=$meeting_user_id?>/userwise">
                          <?= $row->potential_funnel_for_fy; ?>
                          </a>
                        </td>
                        <td>
                          <a target="_BLANK" class="card-count-heading-new" style="color:<?=$backgroundColorNew;?>!important" href="<?=base_url()?>Reports/FunnelDetails/to_be_nurtured_for_fy/<?=$meeting_user_id?>/userwise">
                          <?= $row->to_be_nurtured_for_fy; ?>
                          </a>
                        </td>
                        <td>
                          <a target="_BLANK" class="card-count-heading-new" style="color:<?=$backgroundColorNew;?>!important" href="<?=base_url()?>Reports/FunnelDetails/fifity_new_lead_funnel/<?=$meeting_user_id?>/userwise">
                          <?= $row->fifity_new_lead_funnel; ?>
                          </a>
                        </td>
                        <td>
                          <a target="_BLANK" class="card-count-heading-new" style="color:<?=$backgroundColorNew;?>!important" href="<?=base_url()?>Reports/FunnelDetails/anchor_clients/<?=$meeting_user_id?>/userwise">
                          <?= $row->anchor_clients; ?>
                          </a>
                        </td>
                      </tr>
                      <?php $i++; } ?>
                    </tbody>
                  </table>
                </div>
              </div>
              </div>







            <hr>
              <div class="card">
              <div class="text-center" style="background: linear-gradient(to right, #ede7f6, #ffffff); border-radius: 12px; box-shadow: 0 4px 8px rgba(0,0,0,0.1); padding: 20px;">
               <h5>
  <i>📊 User Funnel Overview with New Category-Wise (<?=$currentQuarter?>) Insights</i> <br>
  <small>
    👋 Welcome to the User Funnel Overview for <?=$currentQuarter?>! This detailed breakdown provides insights into how users are engaging across various segments. 📊 Understanding these trends helps in optimizing strategies and improving user experiences. 🚀 Here's a look at each category:
  </small>
</h5>

              </div>
              <hr>
              <div class="page-header" style="background: linear-gradient(to right, #ffffff, #ffffff); border-radius: 12px; box-shadow: 0 4px 8px rgba(0,0,0,0.1); padding: 20px;">
                <div class="table-responsive text-nowrap">
                  <table id="example15" class="table table-striped table-bordered" cellspacing="0" width="100%">
                    <thead class="thead-dark">
                    <tr>
                      <th>#</th>
                      <th>👤 Name</th>
                      <th>📊 Total</th>
                      <th>🚀 <?= $currentQuarter ?> Closure Funnel</th>
                      <th>🎯 <?= $currentQuarter ?> FY Potential</th>
                      <th>🌱 <?= $currentQuarter ?> To Be Nurtured</th>

                      <th>🚀 <?= $currentQuarter ?> Prospecting Funnel</th>
                      <th>🧾 <?= $currentQuarter ?> Proposal to Be Sent (Review)</th>
                      <th>🎯 <?= $currentQuarter ?> Proposal to Be Sent - Target</th>
                      <th>🤝 <?= $currentQuarter ?> Closure Pipeline</th>

                    </tr>
                    </thead>
                    <tbody>
                      <?php
                        $i = 1;
                        foreach ($total_funnel_by_user as $row) {
                          $meeting_user_id = $row->current_user_id;
                        
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
                        <td>
                          <a target="_BLANK" class="card-count-heading-new" style="color:<?=$backgroundColorNew;?>!important" href="<?=base_url()?>Reports/FunnelDetails/total/<?=$meeting_user_id?>/userwise">
                          <?= $row->total; ?>
                          </a>
                        </td>
                        <td>
                          <a target="_BLANK" class="card-count-heading-new" style="color:<?=$backgroundColorNew;?>!important" href="<?=base_url()?>Reports/FunnelDetails/current_quarter_twetenty_closure_funnel/<?=$meeting_user_id?>/userwise">
                          <?= $row->current_quarter_twetenty_closure_funnel; ?>
                          </a>
                        </td>
                        <td>
                          <a target="_BLANK" class="card-count-heading-new" style="color:<?=$backgroundColorNew;?>!important" href="<?=base_url()?>Reports/FunnelDetails/current_quarter_potential_funnel_for_fy/<?=$meeting_user_id?>/userwise">
                          <?= $row->current_quarter_potential_funnel_for_fy; ?>
                          </a>
                        </td>
                        <td>
                          <a target="_BLANK" class="card-count-heading-new" style="color:<?=$backgroundColorNew;?>!important" href="<?=base_url()?>Reports/FunnelDetails/current_quarter_to_be_nurtured_for_fy/<?=$meeting_user_id?>/userwise">
                          <?= $row->current_quarter_to_be_nurtured_for_fy; ?>
                          </a>
                        </td>
                        <td>
                          <a target="_BLANK" class="card-count-heading-new" style="color:<?=$backgroundColorNew;?>!important" href="<?=base_url()?>Reports/FunnelDetails/total_prospecting_funnel/<?=$meeting_user_id?>/userwise">
                          <?= $row->total_prospecting_funnel; ?>
                          </a>
                        </td>
                        <td>
                          <a target="_BLANK" class="card-count-heading-new" style="color:<?=$backgroundColorNew;?>!important" href="<?=base_url()?>Reports/FunnelDetails/total_proposal_to_be_sent_review/<?=$meeting_user_id?>/userwise">
                          <?= $row->total_proposal_to_be_sent_review; ?>
                          </a>
                        </td>
                        <td>
                          <a target="_BLANK" class="card-count-heading-new" style="color:<?=$backgroundColorNew;?>!important" href="<?=base_url()?>Reports/FunnelDetails/total_proposal_to_be_sent_target/<?=$meeting_user_id?>/userwise">
                          <?= $row->total_proposal_to_be_sent_target; ?>
                          </a>
                        </td>
                        <td>
                          <a target="_BLANK" class="card-count-heading-new" style="color:<?=$backgroundColorNew;?>!important" href="<?=base_url()?>Reports/FunnelDetails/total_closure_pipeline/<?=$meeting_user_id?>/userwise">
                          <?= $row->total_closure_pipeline; ?>
                          </a>
                        </td>
                   
                      </tr>
                      <?php $i++; } ?>
                    </tbody>
                  </table>
                </div>
              </div>
              </div>
<hr>
              <div class="card">
              <div class="text-center" style="background: linear-gradient(to right, #ede7f6, #ffffff); border-radius: 12px; box-shadow: 0 4px 8px rgba(0,0,0,0.1); padding: 20px;">
                <h5>
  <i>📋 User Funnel Overview with Assign-Wise Insights</i> <br>
  <small>
    📊 This data represents a detailed breakdown of funnel performance and categorization for individual users, highlighting key metrics such as total leads, stage-wise funnel status (e.g., 📂 Open, 📞 Reachout, 📅 Tentative), and strategic classifications (e.g., 🚀 Focus Funnel, 🏢 Key Client, ☎️ Priority Calling). It also includes 🧑‍💼 role-based funnel assignments across different management levels to facilitate performance analysis and strategic follow-up planning. 📌
  </small>
</h5>

              </div>
              <hr>
              <div class="page-header" style="background: linear-gradient(to right, #ffffff, #ffffff); border-radius: 12px; box-shadow: 0 4px 8px rgba(0,0,0,0.1); padding: 20px;">
                <div class="table-responsive text-nowrap">
                  <table id="example4" class="table table-striped table-bordered" cellspacing="0" width="100%">
                    <thead class="thead-dark">
                     <tr>
                      <th>#</th>
                      <th>👤 Name</th>
                      <th>📊 Total</th>
                      <th>🎓 PST Funnel</th>
                      <th>👨‍💼 Cluster Manager Funnel</th>
                      <th>🔄 PST & CM Funnel</th>
                      <th>🧑‍💼 ASH (NAE) Funnel</th>
                      <th>🧑‍💼 ASH (W) Funnel</th>
                      <th>🧑‍💼 ASH (S) Funnel</th>
                      <th>🧭 RM East Funnel</th>
                      <th>🧭 RM North Funnel</th>
                      <th>👥 Assistant Cluster Funnel</th>
                    </tr>

                    </thead>
                    <tbody>
                      <?php
                        $i = 1;
                        foreach ($total_funnel_by_user as $row) {
                          $meeting_user_id = $row->current_user_id;
                        
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
                        <td>
                          <a target="_BLANK" class="card-count-heading-new" style="color:<?=$backgroundColorNew;?>!important" href="<?=base_url()?>Reports/FunnelDetails/total/<?=$meeting_user_id?>/userwise">
                          <?= $row->total; ?>
                          </a>
                        </td>
                        <td>
                          <a target="_BLANK" class="card-count-heading-new" style="color:<?=$backgroundColorNew;?>!important" href="<?=base_url()?>Reports/FunnelDetails/PST_assign_funnel/<?=$meeting_user_id?>/userwise">
                          <?= $row->PST_assign_funnel; ?>
                          </a>
                        </td>
                        <td>
                          <a target="_BLANK" class="card-count-heading-new" style="color:<?=$backgroundColorNew;?>!important" href="<?=base_url()?>Reports/FunnelDetails/cluster_manager_assign_funnel/<?=$meeting_user_id?>/userwise">
                          <?= $row->cluster_manager_assign_funnel; ?>
                          </a>
                        </td>
                        <td>
                          <a target="_BLANK" class="card-count-heading-new" style="color:<?=$backgroundColorNew;?>!important" href="<?=base_url()?>Reports/FunnelDetails/PST_and_cluster_manager_assign_funnel/<?=$meeting_user_id?>/userwise">
                          <?= $row->PST_and_cluster_manager_assign_funnel; ?>
                          </a>
                        </td>
                        <td>
                          <a target="_BLANK" class="card-count-heading-new" style="color:<?=$backgroundColorNew;?>!important" href="<?=base_url()?>Reports/FunnelDetails/Assistant_Sales_Head_NAE_assign_funnel/<?=$meeting_user_id?>/userwise">
                          <?= $row->Assistant_Sales_Head_NAE_assign_funnel; ?>
                          </a>
                        </td>
                        <td>
                          <a target="_BLANK" class="card-count-heading-new" style="color:<?=$backgroundColorNew;?>!important" href="<?=base_url()?>Reports/FunnelDetails/Assistant_Sales_Head_W_assign_funnel/<?=$meeting_user_id?>/userwise">
                          <?= $row->Assistant_Sales_Head_W_assign_funnel; ?>
                          </a>
                        </td>
                        <td>
                          <a target="_BLANK" class="card-count-heading-new" style="color:<?=$backgroundColorNew;?>!important" href="<?=base_url()?>Reports/FunnelDetails/Assistant_Sales_Head_S_assign_funnel/<?=$meeting_user_id?>/userwise">
                          <?= $row->Assistant_Sales_Head_S_assign_funnel; ?>
                          </a>
                        </td>
                        <td>
                          <a target="_BLANK" class="card-count-heading-new" style="color:<?=$backgroundColorNew;?>!important" href="<?=base_url()?>Reports/FunnelDetails/RM_East_assign_funnel/<?=$meeting_user_id?>/userwise">
                          <?= $row->RM_East_assign_funnel; ?>
                          </a>
                        </td>
                        <td>
                          <a target="_BLANK" class="card-count-heading-new" style="color:<?=$backgroundColorNew;?>!important" href="<?=base_url()?>Reports/FunnelDetails/RM_North_assign_funnel/<?=$meeting_user_id?>/userwise">
                          <?= $row->RM_North_assign_funnel; ?>
                          </a>
                        </td>
                        <td>
                          <a target="_BLANK" class="card-count-heading-new" style="color:<?=$backgroundColorNew;?>!important" href="<?=base_url()?>Reports/FunnelDetails/Assistant_Cluster_Manager_assign_funnel/<?=$meeting_user_id?>/userwise">
                          <?= $row->Assistant_Cluster_Manager_assign_funnel; ?>
                          </a>
                        </td>
                      </tr>
                      <?php $i++; } ?>
                    </tbody>
                  </table>
                </div>
              </div>
              </div>        
        <hr>
              <div class="card">
              <div class="text-center" style="background: linear-gradient(to right, #ede7f6, #ffffff); border-radius: 12px; box-shadow: 0 4px 8px rgba(0,0,0,0.1); padding: 20px;">
                <h5>
  <i>🤝 Total Partner Wise Funnel Details</i> <br>
 <small>
  👤 Breakdown of leads by user across diverse categories: 🏢 Corporate, 🏛 PSU, 🤝 NGO, 🏫 Private School, 🧍 Individual, 🏬 Government Offices, 📦 Other sectors, 💻 Online sources, 🧪 STEM Schools, 🏛 Foundations, 🌍 Multinational Corporations (MNC), 📞 Head Office Leads, 🏞️ DMF, and 🎖️ Elected Representatives. This data helps analyze lead distribution by segment and user for targeted strategies.
</small>

</h5>

               
              </div>
              <hr>
              <div class="page-header" style="background: linear-gradient(to right, #ffffff, #ffffff); border-radius: 12px; box-shadow: 0 4px 8px rgba(0,0,0,0.1); padding: 20px;">
                <div class="table-responsive text-nowrap">
                  <table id="example10" class="table table-striped table-bordered" cellspacing="0" width="100%">
                    <thead class="thead-dark">
                     <tr>
                      <th>🔢 Sr.No</th>
                      <th>👤 Name</th>
                      <th>🏢 Corporate</th>
                      <th>🏛 PSU</th>
                      <th>🤝 NGO</th>
                      <th>🏫 Private School</th>
                      <th>🧍 Individual</th>
                      <th>🏬 Govt Off</th>
                      <th>📦 Other</th>
                      <th>💻 Online</th>
                      <th>🧪 STEM School</th>
                      <th>🏛 Foundation</th>
                      <th>🌍 MNC</th>
                      <th>📞 HO Leads</th>
                      <th>🏞️ DMF</th>
                      <th>🎖️ Elected Representatives</th>
                    </tr>
                    </thead>
                    <tbody>
                      <?php
                        $i = 1;
                        foreach ($total_funnels_partner_user as $row) {
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
                        <td class="text-capitalize"><?php echo htmlspecialchars($row->username); ?></td>
                        <td>
                          <a target="_BLANK" class="card-count-heading-new" style="color:<?=$backgroundColorNew;?>!important" href="<?=base_url()?>Reports/FunnelDetails/corporate/<?=$meeting_user_id?>/userwise">
                          <?= $row->corporate; ?>
                          </a>
                        </td>
                        <td>
                          <a target="_BLANK" class="card-count-heading-new" style="color:<?=$backgroundColorNew;?>!important" href="<?=base_url()?>Reports/FunnelDetails/PSU/<?=$meeting_user_id?>/userwise">
                          <?= $row->PSU; ?>
                          </a>
                        </td>
                        <td>
                          <a target="_BLANK" class="card-count-heading-new" style="color:<?=$backgroundColorNew;?>!important" href="<?=base_url()?>Reports/FunnelDetails/NGO/<?=$meeting_user_id?>/userwise">
                          <?= $row->NGO; ?>
                          </a>
                        </td>
                        <td>
                          <a target="_BLANK" class="card-count-heading-new" style="color:<?=$backgroundColorNew;?>!important" href="<?=base_url()?>Reports/FunnelDetails/Private_School/<?=$meeting_user_id?>/userwise">
                          <?= $row->Private_School; ?>
                          </a>
                        </td>
                        <td>
                          <a target="_BLANK" class="card-count-heading-new" style="color:<?=$backgroundColorNew;?>!important" href="<?=base_url()?>Reports/FunnelDetails/Individual/<?=$meeting_user_id?>/userwise">
                          <?= $row->Individual; ?>
                          </a>
                        </td>
                        <td>
                          <a target="_BLANK" class="card-count-heading-new" style="color:<?=$backgroundColorNew;?>!important" href="<?=base_url()?>Reports/FunnelDetails/Govt_Off/<?=$meeting_user_id?>/userwise">
                          <?= $row->Govt_Off; ?>
                          </a>
                        </td>
                        <td>
                          <a target="_BLANK" class="card-count-heading-new" style="color:<?=$backgroundColorNew;?>!important" href="<?=base_url()?>Reports/FunnelDetails/Other/<?=$meeting_user_id?>/userwise">
                          <?= $row->Other; ?>
                          </a>
                        </td>
                        <td>
                          <a target="_BLANK" class="card-count-heading-new" style="color:<?=$backgroundColorNew;?>!important" href="<?=base_url()?>Reports/FunnelDetails/Online/<?=$meeting_user_id?>/userwise">
                          <?= $row->Online; ?>
                          </a>
                        </td>
                        <td>
                          <a target="_BLANK" class="card-count-heading-new" style="color:<?=$backgroundColorNew;?>!important" href="<?=base_url()?>Reports/FunnelDetails/STEM_School/<?=$meeting_user_id?>/userwise">
                          <?= $row->STEM_School; ?>
                          </a>
                        </td>
                        <td>
                          <a target="_BLANK" class="card-count-heading-new" style="color:<?=$backgroundColorNew;?>!important" href="<?=base_url()?>Reports/FunnelDetails/Foundation/<?=$meeting_user_id?>/userwise">
                          <?= $row->Foundation; ?>
                          </a>
                        </td>
                        <td>
                          <a target="_BLANK" class="card-count-heading-new" style="color:<?=$backgroundColorNew;?>!important" href="<?=base_url()?>Reports/FunnelDetails/MNC/<?=$meeting_user_id?>/userwise">
                          <?= $row->MNC; ?>
                          </a>
                        </td>
                        <td>
                          <a target="_BLANK" class="card-count-heading-new" style="color:<?=$backgroundColorNew;?>!important" href="<?=base_url()?>Reports/FunnelDetails/HO_Leads/<?=$meeting_user_id?>/userwise">
                          <?= $row->HO_Leads; ?>
                          </a>
                        </td>
                        <td>
                          <a target="_BLANK" class="card-count-heading-new" style="color:<?=$backgroundColorNew;?>!important" href="<?=base_url()?>Reports/FunnelDetails/DMF/<?=$meeting_user_id?>/userwise">
                          <?= $row->DMF; ?>
                          </a>
                        </td>
                        <td>
                          <a target="_BLANK" class="card-count-heading-new" style="color:<?=$backgroundColorNew;?>!important" href="<?=base_url()?>Reports/FunnelDetails/Elected_Representatives/<?=$meeting_user_id?>/userwise">
                          <?= $row->Elected_Representatives; ?>
                          </a>
                        </td>
                      
                    
                      </tr>
                      <?php $i++; } ?>
                    </tbody>
                  </table>
                </div>
              </div>
              </div>
            <hr>
              <div class="card">
                <div class="text-center" style="background: linear-gradient(to right, #ede7f6, #ffffff); border-radius: 12px; box-shadow: 0 4px 8px rgba(0,0,0,0.1); padding: 20px;">
                  <h5>
              <i>✨ Travel Type Categorization Summary</i> <br>
            <small>
              ✨ Travel Type Categorization Summary 🧭 offers a comprehensive overview of how companies are distributed across different travel types. It classifies records into 📍 Base Location, 🧳 OutStation, 🕳️ Marked But Not Set, and ❓ Not Set clusters. This summary helps in identifying travel trends, optimizing field operations, and ensuring accurate cluster tagging for efficient 🗂️ planning, 📊 reporting, and 🚀 decision-making across various teams and regions.
            </small>

            </h5>

               
              </div>
              <hr>
              <div class="page-header" style="background: linear-gradient(to right, #ffffff, #ffffff); border-radius: 12px; box-shadow: 0 4px 8px rgba(0,0,0,0.1); padding: 20px;">
                <div class="table-responsive text-nowrap">
                  <table id="example11" class="table table-striped table-bordered" cellspacing="0" width="100%">
                    <thead class="thead-dark">
                    <tr>
                      <th>🔢 Sr.No</th>
                      <th>👤 Name</th>
                      <th>📍 Base Location</th>
                      <th>🧳 OutStation Location</th>
                      <th>❓ Not Set Travel Cluster</th>
                    </tr>
                    </thead>
                    <tbody>
                      <?php
                        $i = 1;
                        foreach ($totalClusterBYBDatas as $row) {
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
                        <td class="text-capitalize"><?php echo htmlspecialchars($row->username); ?></td>
                        <td>
                          <a target="_BLANK" class="card-count-heading-new" style="color:<?=$backgroundColorNew;?>!important" href="<?=base_url()?>Reports/FunnelDetailsWithCluster/base_location/<?=$meeting_user_id?>/userwise">
                          <?= $row->base_location; ?>
                          </a>
                        </td>
                        <td>
                          <a target="_BLANK" class="card-count-heading-new" style="color:<?=$backgroundColorNew;?>!important" href="<?=base_url()?>Reports/FunnelDetailsWithCluster/outStation_location/<?=$meeting_user_id?>/userwise">
                          <?= $row->outStation_location; ?>
                          </a>
                        </td>
                        <td>
                          <a target="_BLANK" class="card-count-heading-new" style="color:<?=$backgroundColorNew;?>!important" href="<?=base_url()?>Reports/FunnelDetailsWithCluster/not_set_travel_cluster/<?=$meeting_user_id?>/userwise">
                          <?= $row->not_set_travel_cluster; ?>
                          </a>
                        </td>
                      </tr>
                      <?php $i++; } ?>
                    </tbody>
                  </table>
                </div>
              </div>
              </div>



              <?php  
              // dd($totalClusterBYClusterNameBYBDDatas);
              ?>
            <hr>
              <div class="card">
                <div class="text-center" style="background: linear-gradient(to right, #ede7f6, #ffffff); border-radius: 12px; box-shadow: 0 4px 8px rgba(0,0,0,0.1); padding: 20px;">
                  <h5>
              <i>✨ Travel Cluster Categorization By Cluster Name Summary</i> <br>
            <small>
             ✨ Travel Cluster Categorization By Cluster Name Summary 📍 gives a detailed view of how travel clusters are distributed across each cluster location. It breaks down the total records 🔢 by cluster name 🏢 and categorizes them into 📍 base, 🧳 outstation, ❓ marked but not set, and 🚫 not set clusters. This summary helps in understanding field activity distribution, ensuring better planning 📆, coverage tracking 🗺️, and improving data accuracy 📊.
            </small>

            </h5>
              </div>
              <hr>
              <div class="page-header" style="background: linear-gradient(to right, #ffffff, #ffffff); border-radius: 12px; box-shadow: 0 4px 8px rgba(0,0,0,0.1); padding: 20px;">
                <div class="table-responsive text-nowrap">
                  <table id="example12" class="table table-striped table-bordered" cellspacing="0" width="100%">
                    <thead class="thead-dark">
                    <tr>
                      <th>🔢 Sr.No</th>
                      <th>👤 Name</th>
                      <th>🌍 Cluster Name</th>
                      <th>📍 Total Company</th>
                      <th>📍 Base Location</th>
                      <th>🧳 OutStation Location</th>
                      <!-- <th>❓ Not Set Travel Cluster</th> -->
                    </tr>
                    </thead>
                    <tbody>
                      <?php
                        $i = 1;
                        foreach ($totalClusterBYClusterNameBYBDDatas as $row) {
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
                        <td class="text-capitalize"><?php echo htmlspecialchars($row->username); ?></td>
                        <td>
                          <a target="_BLANK" class="card-count-heading-new" style="color:<?=$backgroundColorNew;?>!important" href="<?=base_url()?>Menu/TravelClusterDetailsByID/<?=$row->cluster_id?>">
                          <?= $row->clustername; ?>
                          </a>
                        </td>
                        <td>
                          <a target="_BLANK" class="card-count-heading-new" style="color:<?=$backgroundColorNew;?>!important" href="<?=base_url()?>Reports/FunnelDetailsWithClusterID/total/<?=$meeting_user_id?>/<?=$row->cluster_id?>/userwise/">
                          <?= $row->total; ?>
                          </a>
                        </td>
                        <td>
                          <a target="_BLANK" class="card-count-heading-new" style="color:<?=$backgroundColorNew;?>!important" href="<?=base_url()?>Reports/FunnelDetailsWithClusterID/base_location/<?=$meeting_user_id?>/<?=$row->cluster_id?>/userwise">
                          <?= $row->base_location; ?>
                          </a>
                        </td>
                    
                        <td>
                          <a target="_BLANK" class="card-count-heading-new" style="color:<?=$backgroundColorNew;?>!important" href="<?=base_url()?>Reports/FunnelDetailsWithClusterID/outStation_location/<?=$meeting_user_id?>/<?=$row->cluster_id?>/userwise">
                          <?= $row->outStation_location; ?>
                          </a>
                        </td>
                      </tr>
                      <?php $i++; } ?>
                    </tbody>
                  </table>
                </div>
              </div>
              </div>




              <?php  
              // dd($totalClusterBYStatusDatas);
              ?>
            <hr>
              <div class="card">
                <div class="text-center" style="background: linear-gradient(to right, #ede7f6, #ffffff); border-radius: 12px; box-shadow: 0 4px 8px rgba(0,0,0,0.1); padding: 20px;">
                  <h5>
              <i> 📌 Travel Cluster Summary by Status</i> <br>
            <small>
              📌 Travel Cluster Summary by Status 📊 provides a clear overview of how companies are distributed across various travel categories based on their current status. It highlights the 🔢 total number of records and breaks them down into 📍 base, 🧳 outstation, and ❓ unset travel clusters. This summary helps identify travel engagement patterns, 🗺️ operational coverage, and potential data gaps, offering valuable insights for 📈 planning, 🧠 decision-making, and 🛠️ improving assignment accuracy.
            </small>

            </h5>
              </div>
              <hr>
              <div class="page-header" style="background: linear-gradient(to right, #ffffff, #ffffff); border-radius: 12px; box-shadow: 0 4px 8px rgba(0,0,0,0.1); padding: 20px;">
                <div class="table-responsive text-nowrap">
                  <table id="example13" class="table table-striped table-bordered" cellspacing="0" width="100%">
                    <thead class="thead-dark">
                    <tr>
                      <th>🔢 Sr.No</th>
                      <th>📍 Status Name</th>
                      <th>📍 Total Company</th>
                      <th>📍 Base Location</th>
                      <th>🧳 OutStation Location</th>
                      <th>❓ Not Set Travel Cluster</th>
                    </tr>
                    </thead>
                    <tbody>
                      <?php
                        $i = 1;
                        foreach ($totalClusterBYStatusDatas as $row) {
                          // $meeting_user_id = $row->user_id;
                          $meeting_user_id = $uid;
                        
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
                       
                        <td>
                          <a target="_BLANK" class="card-count-heading-new" style="color:<?=$backgroundColorNew;?>!important" href="<?=base_url()?>Reports/FunnelDetailsWithClusterID/clustername/<?=$meeting_user_id?>/<?=$row->status_id?>/status">
                          <?= $row->status_name; ?>
                          </a>
                        </td>
                        <td>
                          <a target="_BLANK" class="card-count-heading-new" style="color:<?=$backgroundColorNew;?>!important" href="<?=base_url()?>Reports/FunnelDetailsWithClusterID/total/<?=$meeting_user_id?>/<?=$row->status_id?>/status">
                          <?= $row->total; ?>
                          </a>
                        </td>
                        <td>
                          <a target="_BLANK" class="card-count-heading-new" style="color:<?=$backgroundColorNew;?>!important" href="<?=base_url()?>Reports/FunnelDetailsWithClusterID/base_location/<?=$meeting_user_id?>/<?=$row->status_id?>/status">
                          <?= $row->base_location; ?>
                          </a>
                        </td>
                        <td>
                          <a target="_BLANK" class="card-count-heading-new" style="color:<?=$backgroundColorNew;?>!important" href="<?=base_url()?>Reports/FunnelDetailsWithClusterID/outStation_location/<?=$meeting_user_id?>/<?=$row->status_id?>/status">
                          <?= $row->outStation_location; ?>
                          </a>
                        </td>
                        <td>
                          <a target="_BLANK" class="card-count-heading-new" style="color:<?=$backgroundColorNew;?>!important" href="<?=base_url()?>Reports/FunnelDetailsWithClusterID/not_set_travel_cluster/<?=$meeting_user_id?>/<?=$row->status_id?>/status">
                          <?= $row->not_set_travel_cluster; ?>
                          </a>
                        </td>
                      </tr>
                      <?php $i++; } ?>
                    </tbody>
                  </table>
                </div>
              </div>
              </div>
          </div>
      </div>
      </section>
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
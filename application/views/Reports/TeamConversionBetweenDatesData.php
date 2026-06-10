<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Team  Status Conversion Between Dates | STEM APP | WebAPP</title>
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
                    <div class="frame-layer-3">
                      <h3 class="m-0 premium-color-1" style="color: #ff009b;">Team Status Conversion Between Dates</h3>
                      <p class="premium-color-2" style="color: #ff0000;">📊 Team statys conversion between dates helps track performance 📅, analyze progress 📈, identify patterns 🔍, and measure outcomes ✅, enabling insights 💡 and improvements 🚀 across specific time ranges.</p>
                      <p class="premium-color-2" style="color: #2c00ee;"><strong><mark><?=$sdate ?> To <?=$edate ?></mark></strong></p>
                      <p class="premium-color-2" style="color: #2c00ee;"><strong>Task : <mark><?=$taskActionDatas ?></mark></strong></p>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <br>
            <div class="row mb-2">
              <hr>
              <div class="text-right-data text-center" style="background: linear-gradient(to right, #f7ffb2, #ffffff); border-radius: 12px; box-shadow: 0 4px 8px rgba(0,0,0,0.1); padding: 20px;">
                <?php 
                  $clusterPstDatas  = $this->Menu_model->GetAdminActiveTeam($user['user_id']);
                  $taskActions      = $this->Menu_model->get_action();
                  $userRoles        = $this->Menu_model->GetActiveDepartmentList($uid);
                  ?>
                <div class="col text-center formcenterData">
                  <div>
                    <hr>
                    <form action="<?=base_url()?>Reports/TeamConversionBetweenDatesData" method="post" class="mt-3">
                      <div class="row mb-4">
                        <div class="col">
                          <label for="selectedDate">Start Date</label>
                          <input type="date" value="<?=$sdate;?>" id="selectedDate" max="<?=date('Y-m-d')?>" name="sdate" style="width: 200px;" class="form-control">
                        </div>
                        <div class="col">
                          <label for="selectedDate">End Date</label>
                          <input type="date" value="<?=$edate;?>" id="selectedDate" name="edate" style="width: 200px;" max="<?=date('Y-m-d')?>" class="form-control">
                        </div>
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
                        <div class="col">
                          <label for="selectedDate">Select Task</label>
                          <select class="form-control" name="task_action" style="width: fit-content;">
                            <option value="" disabled>Select Task</option>
                            <option value="all" <?= ($task_action_id == 'all') ? 'selected' : ''; ?>>All</option>
                            <?php foreach ($taskActions as $taskAction): ?>
                            <?php 
                              if($taskAction->action_status == 0){
                                continue;
                              } ?>
                            <?php if($task_action_id == $taskAction->id ){
                              $selectedtext =  'selected';
                              }else{
                              $selectedtext = '';
                              } ?>
                            <option <?= $selectedtext; ?> value="<?= $taskAction->id; ?>"><?= htmlspecialchars($taskAction->name); ?></option>
                            <?php endforeach; ?>
                          </select>
                        </div>
                        <div class="col">
                          <div class="form-group">
                            <button type="submit" class="btn btn-primary p-1" style="margin-top: 36px; width: 100px;">Filter</button>
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
            <?php 

              $total_conversions          = $getTotalTasksStatus['total_conversions'];
              $user_wise_conversions      = $getTotalTasksStatus['user_wise_conversions'];

          
              function GetBackgroundColor(){
                      $r = rand(150, 255);
                      $g = rand(150, 255);
                      $b = rand(150, 255);
                      $backgroundColor = "rgba($r, $g, $b,.3)";
                      return $backgroundColor;
              }
              function GetTextColor(){
                      $hue = rand(0, 360);
                      $saturation = rand(60, 100);
                      $lightness = rand(30, 45);
                      $textColor = "hsl($hue, $saturation%, $lightness%)";
                      return $textColor;
              }

              
                  //  dd($user_wise_conversions); 
              ?>
        



        <?php /*
  <hr>
            <div class="text-center" style="background: linear-gradient(to right, #ede7f6, #ffffff); border-radius: 12px; box-shadow: 0 4px 8px rgba(0,0,0,0.1); padding: 20px;">
              <h5>
                <i>Task Conversion Summary Descriptions</i><br>
                <small>This summary tracks user task outcomes. total_conversions shows all conversions, while positive_conversions and negative_conversions reflect successful or failed outcomes. other conversions captures neutral or uncategorized results, helping assess user performance and engagement impact.</small>
                <small><i><b><?= $sdate ?> To <?= $edate ?></b></i></small>
              </h5>
            </div>
            <hr>
            <div class="row">
              <?php  
                $descriptions = [
                      'user_id' => 'Unique identifier for the user.',
                      'username' => 'Name of the task owner.',
                      'total_conversions' => 'Total number of conversions made by the user.',
                      'positive_conversions' => 'Conversions that resulted in a positive outcome.',
                      'negative_conversions' => 'Conversions that resulted in a negative outcome.',
                      'other_conversions' => 'Conversions that do not fall under positive or negative.'
                  ];

                
                
                foreach ($total_conversions as $tasktypeData) {
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
                     <a target="_BLANK" class="card-count-heading-new" style="color:<?=$backgroundColorNew;?>!important" href="<?=base_url()?>Reports/GetTeamTaskOnSelfOrOtherFunnelTaskLists/<?=$key?>/<?=$uid?>/<?=$sdate?>/<?=$edate?>/<?=$task_action_id?>">
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


           */ ?>
            <hr>
            <div class="body-content">
              <div class="card">
                <div class="text-center" style="background: linear-gradient(to right, #ede7f6, #ffffff); border-radius: 12px; box-shadow: 0 4px 8px rgba(0,0,0,0.1); padding: 20px;">
                     <h5>
                      <i>Task Conversion Summary By User</i><br>
                      <small>This summary tracks user task outcomes. total_conversions shows all conversions, while positive_conversions and negative_conversions reflect successful or failed outcomes. other conversions captures neutral or uncategorized results, helping assess user performance and engagement impact.</small>
                    </h5>
                  <p><i><b><?= $sdate ?> To <?= $edate ?></b></i></p>
                </div>
                <hr>
                <div class="table-responsive text-nowrap">
                  <table id="example8" class="table table-striped table-bordered" cellspacing="0" width="100%">
                    <thead class="thead-dark">
                     <tr>
                        <th class="text-capitalize">#️⃣ Sr. NO</th>
                        <th class="text-capitalize">👤 Username</th>
                        <th class="text-capitalize">🔄 Total Conversions</th>
                        <th class="text-capitalize">✅ Positive Conversions</th>
                        <th class="text-capitalize">❌ Negative Conversions</th>
                        <th class="text-capitalize">⚪ Other Conversions</th>
                      </tr>
                    </thead>
                    <tbody>
                      <?php
                        $i = 1;
                        foreach ($user_wise_conversions as $row) {
                          $task_user_id = $row->user_id;
                        
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
                        <td><?= htmlspecialchars($i) ?></td>
                        <td class="text-capitalize"><?= htmlspecialchars($row->username) ?></td>
                        <td>
                          <a target="_BLANK" class="card-count-heading-new" style="color:<?=$backgroundColorNew;?>!important" href="<?=base_url()?>Reports/GetTeamTaskOnSelfOrOtherFunnelTaskLists/total_conversions/<?=$task_user_id?>/<?=$sdate?>/<?=$edate?>/<?=$task_action_id?>/userwise">
                          <?= htmlspecialchars($row->total_conversions) ?>
                          </a>
                        </td>
                        <td>
                          <a target="_BLANK" class="card-count-heading-new" style="color:<?=$backgroundColorNew;?>!important" href="<?=base_url()?>Reports/GetTeamTaskOnSelfOrOtherFunnelTaskLists/positive_conversions/<?=$task_user_id?>/<?=$sdate?>/<?=$edate?>/<?=$task_action_id?>/userwise">
                          <?= htmlspecialchars($row->positive_conversions) ?>
                          </a>
                        </td>
                        <td>
                          <a target="_BLANK" class="card-count-heading-new" style="color:<?=$backgroundColorNew;?>!important" href="<?=base_url()?>Reports/GetTeamTaskOnSelfOrOtherFunnelTaskLists/negative_conversions/<?=$task_user_id?>/<?=$sdate?>/<?=$edate?>/<?=$task_action_id?>/userwise">
                          <?= htmlspecialchars($row->negative_conversions) ?>
                          </a>
                        </td>
                        <td>
                          <a target="_BLANK" class="card-count-heading-new" style="color:<?=$backgroundColorNew;?>!important" href="<?=base_url()?>Reports/GetTeamTaskOnSelfOrOtherFunnelTaskLists/other_conversions/<?=$task_user_id?>/<?=$sdate?>/<?=$edate?>/<?=$task_action_id?>/userwise">
                          <?= htmlspecialchars($row->other_conversions) ?>
                          </a>
                        </td>
                       
                      </tr>
                      <?php $i++; } ?>
                    </tbody>
                  </table>
                </div>
                <hr />
              </div>



                <hr>
              <div class="card">
                <div class="text-center" style="background: linear-gradient(to right, #ede7f6, #ffffff); border-radius: 12px; box-shadow: 0 4px 8px rgba(0,0,0,0.1); padding: 20px;">
                     <h5>
                      <i>🔄 Our & Team Status Conversion</i><br>
                      <small>🔄 Our & Team Status Conversion brings clear visibility to progress 📊, tracks every stage 🔍, boosts teamwork 🤝, and ensures goals 🎯 are achieved faster ⚡ with smooth workflow 🔧 and smart insights 💡.</small>
                    </h5>
                  <p><i><b><?= $sdate ?> To <?= $edate ?></b></i></p>
                </div>
                <hr>
                <div class="table-responsive text-nowrap">

                  <table id="example21" class="table table-striped table-bordered" cellspacing="0" width="100%">
                          <thead class="thead-dark">
                              <tr class="text-center">
                                  <th scope="col">🔄 Status Change</th>
                                  <th scope="col">✅ Total Change</th>
                              </tr>
                          </thead>
                          <tbody>
                              <?php 
                                if(sizeof($bdconversions) > 0):
                                  foreach($bdconversions as $bdconversion): ?>
                                      <tr class="text-center">
                                          <td>
                                              <a target="_BLANK" class="p-2" style="color:navy; font-weight: 500;"
                                              href="<?=base_url()?>Reports/TodaysConversionDatas/<?=$uid?>/<?=$sdate?>/<?=$edate?>/<?= $bdconversion->status_change_id;?>/<?= $task_action_id;?>">
                                                  <?= $bdconversion->status_change ?>
                                              </a>
                                          </td>
                                          <td>
                                              <a target="_BLANK" class="badge badge-pill badge-primary p-2" 
                                              href="<?=base_url()?>Reports/TodaysConversionDatas/<?=$uid?>/<?=$sdate?>/<?=$edate?>/<?= $bdconversion->status_change_id;?>/<?= $task_action_id;?>">
                                                  <?= $bdconversion->total_changes ?>
                                              </a>
                                          </td>
                                      </tr>
                                  <?php endforeach;
                              endif;
                              ?>
                          </tbody>
                 
                  </table>
                </div>
                <hr />
              </div>

              <hr>
              <div class="container-table mt-4">
                <div class="text-center" style="background: linear-gradient(to right, #ede7f6, #ffffff); border-radius: 12px; box-shadow: 0 4px 8px rgba(0,0,0,0.1); padding: 20px;">
                     <h5>
                      <i>🔄 Conversion Mapping Table</i><br>
                    </h5>
                </div>
  <table id="conversionMappingTable" class="table table-bordered table-hover align-middle">
    <thead class="table-dark text-center">
      <tr>
        <th class="text-success">Positive Conversions ✅</th>
        <th class="text-danger">Negative Conversions ❌</th>
        <th class="text-primary">Other Conversions 🔄</th>
      </tr>
    </thead>
    <tbody>
      <!-- Positive + Negative + Other conversions -->
      <tr><td class="text-success">Open → Reachout</td><td class="text-danger">Will do Later → Open</td><td class="text-primary">Open → Very Positive</td></tr>
      <tr><td class="text-success">Open → Open RPEM</td><td class="text-danger">Not Interested → Open</td><td class="text-primary">Open → Very Positive-NAP</td></tr>
      <tr><td class="text-success">Open → Tentative</td><td class="text-danger">TTD-Reachout → Tentative</td><td class="text-primary">Open → On-Boarded</td></tr>
      <tr><td class="text-success">Open RPEM → Reachout</td><td class="text-danger">TTD-Reachout → Reachout</td><td class="text-primary">Open RPEM → Very Positive</td></tr>
      <tr><td class="text-success">Open RPEM → Positive</td><td class="text-danger">TTD-Reachout → OPEN RPEM</td><td class="text-primary">Open RPEM → Very Positive-NAP</td></tr>
      <tr><td class="text-success">Open RPEM → Tentative</td><td class="text-danger">Open RPEM → Open</td><td class="text-primary">Open RPEM → On-Boarded</td></tr>
      <tr><td class="text-success">Open RPEM → Closure</td><td class="text-danger">Reachout → Open RPEM</td><td class="text-primary">Open RPEM → Very Positive</td></tr>
      <tr><td class="text-success">Reachout → Tentative</td><td class="text-danger">Reachout → Not Interested</td><td class="text-primary">Reachout → On-Boarded</td></tr>
      <tr><td class="text-success">Reachout → Positive</td><td class="text-danger">Reachout → Open</td><td class="text-primary">Reachout → Very Positive</td></tr>
      <tr><td class="text-success">Reachout → Will do Later</td><td class="text-danger">Positive-NAP → Tentative</td><td class="text-primary">Reachout → Very Positive-NAP</td></tr>
      <tr><td class="text-success">Reachout → Closure</td><td class="text-danger">Positive-NAP → Will do Later</td><td class="text-primary">Reachout → On-Boarded</td></tr>
      <tr><td class="text-success">Tentative → Positive-NAP</td><td class="text-danger">Positive-NAP → WNO Reachout</td><td class="text-primary">Tentative → On-Boarded</td></tr>
      <tr><td class="text-success">Tentative → Closure</td><td class="text-danger">Positive-NAP → Open</td><td class="text-primary">Tentative → Very Positive</td></tr>
      <tr><td class="text-success">Tentative → Positive</td><td class="text-danger">Positive → Will do Later</td><td class="text-primary">Tentative → Very Positive-NAP</td></tr>
      <tr><td class="text-success">Tentative → TTD-Reachout</td><td class="text-danger">Positive → Tentative</td><td class="text-primary">Tentative → On-Boarded</td></tr>
      <tr><td class="text-success">Tentative → WNO-Reachout</td><td class="text-danger">Positive → WNO Reachout</td><td class="text-primary">Tentative → Closure</td></tr>
      <tr><td class="text-success">Positive-NAP → Positive</td><td class="text-danger">Positive → TTD Reachout</td><td class="text-primary">Positive-NAP → On-Boarded</td></tr>
      <tr><td class="text-success">Positive-NAP → Closure</td><td class="text-danger">Positive → Not Interested</td><td class="text-primary">Positive-NAP → Very Positive</td></tr>
      <tr><td class="text-success">Positive-NAP → Very Positive-NAP</td><td class="text-danger">Positive → Open</td><td class="text-primary">Positive-NAP → On-Boarded</td></tr>
      <tr><td class="text-success">Positive-NAP → Very Positive</td><td class="text-danger">Very Positive-NAP → Positive</td><td class="text-primary">Positive-NAP → Very Positive</td></tr>
      <tr><td class="text-success">Positive → Very Positive-NAP</td><td class="text-danger">Very Positive-NAP → Will do Later</td><td class="text-primary">Positive → On-Boarded</td></tr>
      <tr><td class="text-success">Positive → Closure</td><td class="text-danger">Very Positive-NAP → WNO Reachout</td><td class="text-primary">Positive → Very Positive</td></tr>
      <tr><td class="text-success">Positive → Positive-NAP</td><td class="text-danger">Very Positive-NAP → TTD Reachout</td><td class="text-primary">Positive → On-Boarded</td></tr>
      <tr><td class="text-success">Positive → Very Positive</td><td class="text-danger">Very Positive → Will do Later</td><td class="text-primary">Positive → Very Positive-NAP</td></tr>
      <tr><td class="text-success">Very Positive-NAP → Very Positive</td><td class="text-danger">Very Positive → WNO Reachout</td><td class="text-primary">Very Positive-NAP → On-Boarded</td></tr>
      <tr><td class="text-success">Very Positive-NAP → Closure</td><td class="text-danger">Very Positive → TTD Reachout</td><td class="text-primary">Very Positive-NAP → Open RPEM</td></tr>
      <tr><td class="text-success">Very Positive → Closure</td><td class="text-danger">Very Positive → Positive</td><td class="text-primary">Very Positive → On-Boarded</td></tr>
      <tr><td class="text-success">Will do Later → Reachout</td><td class="text-danger">Closure → Very Positive</td><td class="text-primary">Will do Later → On-Boarded</td></tr>
      <tr><td class="text-success">Will do Later → Tentative</td><td class="text-danger">Closure → Will do Later</td><td class="text-primary">Will do Later → Very Positive</td></tr>
      <tr><td class="text-success">Will do Later → OPEN RPEM</td><td class="text-danger">Closure → WNO Reachout</td><td class="text-primary">Will do Later → Positive</td></tr>
      <tr><td class="text-success">Will do Later → Closure</td><td class="text-danger">Closure → Tentative</td><td class="text-primary">Will do Later → Very Positive-NAP</td></tr>
      <tr><td class="text-success">Not Interested → Tentative</td><td class="text-danger">Closure → Positive</td><td class="text-primary">Not Interested → On-Boarded</td></tr>
      <tr><td class="text-success">Not Interested → Reachout</td><td class="text-danger">Tentative → Will do Later</td><td class="text-primary">Not Interested → Very Positive</td></tr>
      <tr><td class="text-success">Not Interested → OPEN RPEM</td><td class="text-danger">Tentative → Not Interested</td><td class="text-primary">Not Interested → On-Boarded</td></tr>
      <tr><td class="text-success">OPEN RPEM → On-Boarded</td><td class="text-danger">Tentative → WNO Reachout</td><td class="text-primary">OPEN RPEM → Very Positive</td></tr>
      <tr><td class="text-success">On-Boarded → Closure</td><td class="text-danger">Tentative → Open RPEM</td><td class="text-primary">On-Boarded → Very Positive</td></tr>
      <tr><td>-</td><td class="text-danger">Tentative → Reachout</td><td class="text-primary">On-Boarded → Very Positive-NAP</td></tr>
      <tr><td>-</td><td class="text-danger">Tentative → Open</td><td class="text-primary">On-Boarded → Positive</td></tr>
      <tr><td>-</td><td class="text-danger">WNO-Reachout → Reachout</td><td class="text-primary">On-Boarded → Tentative</td></tr>
    </tbody>
  </table>
</div>


         
              <br>
              <br>
              <br>
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
      $("#example21").DataTable({
        "responsive": false, "lengthChange": false, "autoWidth": false,
        "buttons": ["csv", "excel", "pdf"]
      }).buttons().container().appendTo('#example21_wrapper .col-md-6:eq(0)');

      $("#conversionMappingTable").DataTable({
        "responsive": false, "lengthChange": false, "autoWidth": false,
        "buttons": ["csv", "excel", "pdf"]
      }).buttons().container().appendTo('#conversionMappingTable_wrapper .col-md-6:eq(0)');
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
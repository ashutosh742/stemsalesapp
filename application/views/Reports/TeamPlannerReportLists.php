<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
      <title>Planner Task Details | STEM APP | WebAPP</title>
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
                     <h1 class="m-0 premium-color-1" style="color: #ff009b;">📅 Planner Task Details</h1>
                      <p class="premium-color-2" style="color: #ff0000;">
                        This section offers a comprehensive overview of all planned tasks and meetings, including detailed statistics on meeting types, their current statuses, and other relevant insights. 📊 It helps in tracking progress, analyzing engagement, and streamlining planning activities effectively.
                      </p>
                      <p class="premium-color-2" style="color: #2c00ee;"><strong><mark><?=$sdate ?> To <?=$edate ?></mark></strong></p>

                      <?php 

                      $target_uidData     = $this->Menu_model->get_userbyid($target_uid);
                      $target_uidDataName = $target_uidData[0]->name;

                      if($mtypes == 'total_task'){
                        $filter = 'Total Tasks';
                      }else if($mtypes == 'total_complete_task'){
                        $filter = ' Total complete Task';
                      }else if($mtypes == 'total_pending_task'){
                        $filter = ' Total not complete Task';
                      }else if($mtypes == 'total_status_change_task'){
                        $filter = ' Total Status Change Task';
                      }else if($mtypes == 'total_own_funnel_task'){
                        $filter = ' Total Own Funnel Task';
                      }else if($mtypes == 'total_own_funnel_complete_task'){
                        $filter = ' Total Own Funnel Complete Task';
                      }else if($mtypes == 'total_own_funnel_pending_task'){
                        $filter = ' Total Own Funnel Pending Task';
                      }else if($mtypes == 'total_own_funnel_status_change_task'){
                        $filter = ' Total Own Funnel Status Change Task';
                      }else if($mtypes == 'total_team_funnel_task'){
                        $filter = ' Total Team Funnel Task';
                      }else if($mtypes == 'total_team_funnel_complete_task'){
                        $filter = ' Total Team Funnel Complete Task';
                      }else if($mtypes == 'total_team_funnel_pending_task'){
                        $filter = ' Total Team Funnel Pending Task';
                      } else if($mtypes == 'total_team_funnel_status_change_task'){
                        $filter = ' Total Team Funnel Status Changes Task';
                      }else if($mtypes == 'total_own_aypy_funnel_complete_task'){
                        $filter = ' Total Own Funnel AY PY Task';
                      }else if($mtypes == 'total_team_aypy_funnel_complete_task'){
                        $filter = ' Total Team Funnel AY PY Task';
                      }else if($mtypes == 'total_own_funnel_aypy_status_change_task'){
                        $filter = ' Total Own Funnel AY PY Status Change Task';
                      }else if($mtypes == 'total_team_funnel_aypy_status_change_task'){
                        $filter = ' Total Team Funnel AY PY Status Change Task';
                      }else if($mtypes == 'total_own_aypn_funnel_complete_task'){
                        $filter = ' Total Own Funnel AY PN Task';
                      }else if($mtypes == 'total_team_aypn_funnel_complete_task'){
                        $filter = ' Total Team Funnel AY PN Task';
                      }else if($mtypes == 'Check_task_type'){
                        $filter = $taskActionDatas;
                      }else if($mtypes == 'total_task_count'){
                        $filter = $taskActionDatas;
                      }

                      else if ($mtypes == 'total_open' || $mtypes == 'open') {
                         $filter = 'total in Open Status';
                      }else if ($mtypes == 'total_reachout' || $mtypes == 'reachout') {
                         $filter = 'total in reachout Status';
                      }else if ($mtypes == 'total_tentative' || $mtypes == 'tentative') {
                         $filter = 'total in tentative Status';
                      }else if ($mtypes == 'total_will_do_later' || $mtypes == 'will_do_later') {
                         $filter = 'total in will do later Status';
                      }else if ($mtypes == 'total_not_interested' || $mtypes == 'not_interested') {
                         $filter = 'total in not interested Status';
                      }else if ($mtypes == 'total_positive' || $mtypes == 'positive') {
                         $filter = 'total in positive Status';
                      }else if ($mtypes == 'total_closure' || $mtypes == 'closure') {
                         $filter = 'total in closure Status';
                      }else if ($mtypes == 'total_open_rpem' || $mtypes == 'open_rpem') {
                         $filter = 'total in open rpem Status';
                      }else if ($mtypes == 'total_very_positive' || $mtypes == 'very_positive') {
                         $filter = 'total in very positive Status';
                      }else if ($mtypes == 'total_ttd_reachout' || $mtypes == 'ttd_reachout') {
                         $filter = 'total in ttd reachout Status';
                      }else if ($mtypes == 'total_wno_reachout' || $mtypes == 'wno_reachout') {
                         $filter = 'total in wno reachout Status';
                      }else if ($mtypes == 'total_positive_nap' || $mtypes == 'positive_nap') {
                         $filter = 'total in positive nap Status';
                      }else if ($mtypes == 'total_very_positive_nap' || $mtypes == 'very_positive_nap') {
                         $filter = 'total in very positive nap Status';
                      }else if ($mtypes == 'total_on_boarded' || $mtypes == 'on_boarded') {
                         $filter = 'total in on boarded Status';
                      }else if ($mtypes == 'total_in_q1_twetenty_closure_funnel') {
                          $filter = 'total in q1 twetenty closure funnel';
                      }else if ($mtypes == 'total_in_q1_twetenty_closure_funnel') {
                          $filter = 'total in q1 twetenty closure funnel';
                      }else if ($mtypes == 'total_in_q1_twetenty_closure_funnel') {
                          $filter = 'total in q1 twetenty closure funnel';
                      }else if ($mtypes == 'total_in_q1_twetenty_closure_funnel') {
                          $filter = 'total in q1 twetenty closure funnel';
                      }else if ($mtypes == 'total_in_potential_funnel_for_fy') {
                          $filter = 'total in potential funnel for fy';
                      }else if ($mtypes == 'total_in_to_be_nurtured_for_fy') {
                          $filter = 'total in to be nurtured for fy';
                      }else if ($mtypes == 'total_in_fifity_new_lead_funnel') {
                         $filter = 'total in fifity new lead funnel';
                      }else if ($mtypes == 'zero_to_fifty_nine_seconds') {
                             $filter = 'zero_to_fifty_nine_seconds';
                      }else if ($mtypes == 'one_to_five_minutes') {
                          $filter = 'one_to_five_minutes';
                      }else if ($mtypes == 'five_to_ten_minutes') {
                            $filter = 'five_to_ten_minutes';
                      }else if ($mtypes == 'ten_to_fifteen_minutes') {
                            $filter = 'ten_to_fifteen_minutes';
                      }else if ($mtypes == 'sixteen_to_thirty_minutes') {
                            $filter = 'sixteen_to_thirty_minutes';
                      }else if ($mtypes == 'thirty_one_to_fifty_minutes') {
                          $filter = 'thirty_one_to_fifty_minutes';
                      }else if ($mtypes == 'fifty_one_to_sixty_minutes') {
                          $filter = 'fifty_one_to_sixty_minutes';
                      }else if ($mtypes == 'greater_than_one_sixty_minutes') {
                            $filter = 'greater_than_one_sixty_minutes';
                      }else if ($mtypes == 'potential') {
                          $filter = " Potential Client";
                      }else if ($mtypes == 'topspender') {
                         $filter = " Top Spender Client";
                      }else if ($mtypes == 'upsell_client') {
                          $filter = " Upsell Client";
                      }else if ($mtypes == 'focus_funnel') {
                          $filter = " Focus Funnel Client";
                      }else if ($mtypes == 'keycompany') {
                          $filter = " Key Client";
                      }else if ($mtypes == 'pkclient') {
                          $filter = " Positive Key Client";
                      }else if ($mtypes == 'Priority Calling') {
                         $filter = " Priorityc Client";
                      }else{
                        $filter = $mtypes;
                      }

                      ?>
                      <p class="premium-color-2 text-capitalize" style="color:rgb(16, 238, 0);"><strong><mark><?=$filter ?></mark></strong></p>
                      <p class="premium-color-2 text-capitalize p-1"><strong><mark style="background-color:rgb(198, 238, 0);"><?=$target_uidDataName ?></mark></strong></p>
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


            <?php 
          //  dd($totalTasksUserByDatas);


              $task_counts = [];

              $filterused = [];
              $selectbyFilter = [];
        
        foreach ($totalTasksUserByDatas as $row) {
            $task_name = $row->task_name;
        
            if (!isset($task_counts[$task_name])) {
                $task_counts[$task_name] = 1;
            } else {
                $task_counts[$task_name]++;
            }


                $filter_by = json_decode($row->filter_by, true);
                $selectby = $row->selectby;
                
        
                if($selectby !==''){
                         if (!isset($selectbyFilter[$selectby])) {
                                $selectbyFilter[$selectby] = 1;
                            } else {
                                $selectbyFilter[$selectby]++;
                            }
                              //  echo "other = ".$selectby.' - Task ID : '.$row->task_id.'<br/>';
                    }
                    else if ($row->reviewtype !='' && !is_int($row->reviewtype)){
                          if (!isset($selectbyFilter[$row->reviewtype])) {
                                $selectbyFilter[$row->reviewtype] = 1;
                            } else {
                                $selectbyFilter[$row->reviewtype]++;
                          }
                            //  echo "other = ".$reviewtype.' - Task ID : '.$row->task_id.'<br/>';
                    }
                    else if ($row->comments !='' && !is_null($row->comments)){
                          if (!isset($selectbyFilter[$row->comments])) {
                                $selectbyFilter[$row->comments] = 1;
                            } else {
                                $selectbyFilter[$row->comments]++;
                          }
                            //  echo "comments = ".$selectby.' - Task ID : '.$row->task_id.'<br/>';
                    }else{
                      if (!isset($selectbyFilter['other'])) {
                                $selectbyFilter['other'] = 1;
                            } else {
                                $selectbyFilter['other']++;
                          }
                      //  echo "other = ".$selectby.' - Task ID : '.$row->task_id.'<br/>';
                    }
        
                if(sizeof($filter_by) > 0){
                    foreach ($filter_by as $key => $value) :
                        if($key == 'Plan_BY'){
                          
                          if (!isset($filterused[$value])) {
                              $filterused[$value] = 1;
                          } else {
                              $filterused[$value]++;
                          }

                        }elseif($key == 'Filter_By'){
                          if (!isset($filterused[$value])) {
                              $filterused[$value] = 1;
                          } else {
                              $filterused[$value]++;
                          }
                        }else{
                        if($key == 'comp_status'){
                        $tstatus = $this->Menu_model->get_statusbyid($value);
                        $tstatusname = $tstatus[0]->name;

                          if (!isset($filterused[$tstatusname])) {
                              $filterused[$tstatusname] = 1;
                          } else {
                              $filterused[$tstatusname]++;
                          }

                        }elseif($key == 'task'){
                        $taction = $this->Menu_model->get_actionbyid($value);
                        $tactionname = $taction[0]->name;

                        if (!isset($filterused[$tactionname])) {
                              $filterused[$tactionname] = 1;
                          } else {
                              $filterused[$tactionname]++;
                          }
                        }elseif($key == 'taskActionbyuser'){
                          // echo "<span class='p-1'>Action&nbsp;Taken:&nbsp;".$value."</span><br/>";
                          if (!isset($filterused[$value])) {
                              $filterused[$value] = 1;
                          } else {
                              $filterused[$value]++;
                          }
                        }else{
                            if($selectby !==''){
                              
                              if (!isset($filterused[$selectby])) {
                                  $filterused[$selectby] = 1;
                              } else {
                                  $filterused[$selectby]++;
                              }

                            }else if ($row->comments !==''){
                                if (!isset($filterused[$row->comments])) {
                                    $filterused[$row->comments] = 1;
                                } else {
                                    $filterused[$row->comments]++;
                                }
                            }else{;
                                if (!isset($filterused[$reviewtype])) {
                                    $filterused[$reviewtype] = 1;
                                } else {
                                    $filterused[$reviewtype]++;
                                }
                            }
                        }
                        }
                    endforeach;
                }


                      }
                      
                      
                     //  dd($selectbyFilter);
                      // dd($filterused);

                      ?>



      
              <div class="taskcountcard">
                  <?php
                    $colors = [
                    "#e3f2fd", // blue
                    "#fce4ec", // pink
                    "#e8f5e9", // green
                    "#fff3e0", // orange
                    "#ede7f6", // purple
                    "#f5facbff", // lime
                    "#f3e5f5", // light purple
                    "#c4c2f3ff",  // teal
                    "#cdf3f1ff",  // teal
                    "#f5cae8ff",  // teal
                    "#edf6c1ff"  // teal
                    ];
                    $index = 0;
                    ?>
                  <div class="card shadow-sm border-0 rounded-4 mb-4">
                    <div class="card-header text-center" style="background: linear-gradient(to right, #f6e7f3, #ffffff); border-radius: 12px; box-shadow: 0 4px 8px rgba(0,0,0,0.1); padding: 20px;">
                      <h5 class="mb-0">📝 Planner Task Activity Summary</h5> 
                      <small class="d-block">📊 Overview of your engagement and planning activities</small>
                    </div>
                    <div class="card-body">
                      <div class="row">
                        <div class="col-md-3 mb-3">
                          <div class="rounded p-3" style="background-color: #abf58f;box-shadow: rgba(9, 30, 66, 0.25) 0px 4px 8px -2px, rgba(9, 30, 66, 0.08) 0px 0px 0px 1px;">
                            <h6 class="mb-1 font-weight-bold text-center">Total Task</h6>
                            <hr>
                            <div class="text-center">
                              <span class="badge badge-pill badge-dark" style="font-size: 1.1rem;"><?= sizeof($totalTasksUserByDatas) ?></span>
                            </div>
                          </div>
                        </div>
                        <?php foreach ($task_counts as $label => $value): ?>
                        <?php
                          $bgColor = $colors[$index % count($colors)];
                          $desc = isset($descriptions[$label]) ? $descriptions[$label] : '';
                          $index++;
                          ?>
                        <div class="col-md-3 mb-3">
                          <div class="rounded p-3" style="background-color: <?= $bgColor ?>;box-shadow: rgba(9, 30, 66, 0.25) 0px 4px 8px -2px, rgba(9, 30, 66, 0.08) 0px 0px 0px 1px;">
                            <h6 class="mb-1 font-weight-bold text-center"><?= $label ?></h6>
                            <hr>
                            <div class="text-center">
                              <span class="badge badge-pill badge-dark" style="font-size: 1.1rem;"><?= $value ?></span>
                            </div>
                          </div>
                        </div>
                        <?php endforeach; ?>
                      </div>
                    </div>
                  </div>
                </div>





            <hr>
            <div class="body-content">
              <div class="page-header">
                <div class="table-responsive text-nowrap">
                  <table id="example1" class="table table-striped table-bordered" cellspacing="0" width="100%">
                  <thead class="thead-dark">
                <tr>
                    <th class="text-capitalize">🔢 Sr. NO</th>
                    <th class="text-capitalize">👤 Task Username</th>
                    <th class="text-capitalize">🆔 CID</th>
                    <th class="text-capitalize">🏢 Company Name</th>
                    <th class="text-capitalize">👨‍💼 Main BD</th>
                    <th class="text-capitalize">👩‍💼 Cluster Manager</th>
                    <th class="text-capitalize">🧑‍💼 PST</th>
                    <th class="text-capitalize">🧭 Assistant Sales Head (NAE)</th>
                    <th class="text-capitalize">🧭 Assistant Sales Head (W)</th>
                    <th class="text-capitalize">🧭 Assistant Sales Head (S)</th>
                    <th class="text-capitalize">🧑‍🏫 RM East</th>
                    <th class="text-capitalize">🧑‍🏫 RM North</th>
                    <th class="text-capitalize">👥 Assistant Cluster Manager</th>
                    <th class="text-capitalize">📌 Current Status</th>

                    <?php if($mtypes == 'total_in_q1_twetenty_closure_funnel'){ ?>
                      <th class="text-capitalize">🚀 Q1 20 Closure Funnel</th>
                    <?php }?>
                    <?php if($mtypes == 'total_in_potential_funnel_for_fy'){ ?>
                      <th class="text-capitalize">🎯 Potential Funnel For FY</th>
                    <?php }?>
                    <?php if($mtypes == 'total_in_to_be_nurtured_for_fy'){ ?>
                      <th class="text-capitalize">🌱 To be Nurtured for FY</th>
                    <?php }?>
                    <?php if($mtypes == 'total_in_fifity_new_lead_funnel'){ ?>
                      <th class="text-capitalize">🆕 50 New Lead Funnel</th>
                    <?php }?>

                    <th class="text-capitalize">📋 Task Name</th>
                    <th class="text-capitalize">🔖 Task Status</th>
                    <th class="text-capitalize">🎬 Action</th>
                    <th class="text-capitalize">🎯 Purpose</th>
                    <th class="text-capitalize">📅 Planned On Status</th>
                    <th class="text-capitalize">🕒 Change On Status</th>
                    <th class="text-capitalize">🗓️ Original Task Date</th>
                    <th class="text-capitalize">📆 Appointment DateTime</th>
                    <th class="text-capitalize">🚀 Initiated DateTime</th>
                    <th class="text-capitalize">⏱️ Updated DateTime</th>
                    <th class="text-capitalize">⌛ Total Time Taken</th>
                    <th class="text-capitalize">💬 Late Remarks Message</th>
                    <th class="text-capitalize">📝 Task Approved By</th>
                    <th class="text-capitalize">🗣️ Remarks</th>
                    <th class="text-capitalize">⭐ Special Remarks</th>
                    <th class="text-capitalize">📍 Closing Timeline</th>

                    <th class="text-capitalize">🤝 Partner Name</th>
                    <th class="text-capitalize">📈 Potential Client</th>
                    <th class="text-capitalize">💰 Top Spender Client</th>
                    <th class="text-capitalize">🔄 Upsell Client</th>
                    <th class="text-capitalize">🚀 Focus Funnel Client</th>
                    <th class="text-capitalize">🏷️ Key Client</th>
                    <th class="text-capitalize">👍 Positive Key Client</th>
                    <th class="text-capitalize">📣 Priorityc Client</th>

                    <th class="text-capitalize">🚀 Q1 20 Closure Funnel</th>
                    <th class="text-capitalize">🎯 Potential Funnel For FY</th>
                    <th class="text-capitalize">🌱 To be Nurtured for FY</th>
                    <th class="text-capitalize">🆕 50 New Lead Funnel</th>
                    <th class="text-capitalize">🔍 Filter Used</th>

                    <th class="text-capitalize">🔁 Task RePlanned Count</th>
                  </tr>

            </thead>
            <tbody>
                <?php
                $i = 1;
                foreach ($totalTasksUserByDatas as $row) {
                  $meeting_user_id = $row->user_id;
                  $meeting_task_id = $row->task_id;

                  $r = rand(150, 255);
                  $g = rand(150, 255);
                  $b = rand(150, 255);
                  $backgroundColor = "rgba($r, $g, $b,.2)";

                  $hue = rand(0, 360); // Full color wheel
                  $saturation = rand(60, 100); // High saturation for rich colors
                  $lightness = rand(30, 45); // Low lightness for a deeper tone
                  
                  $backgroundColorNew = "hsl($hue, $saturation%, $lightness%)";

                $filter_by = json_decode($row->filter_by, true);
                $selectby = $row->selectby;
                   
                  ?>
                  
                  <tr style="background-color: <?php echo $backgroundColor; ?>;color:<?=$backgroundColorNew;?>!important" title="<?=$meeting_task_id?>">
                        <td><?= htmlspecialchars($i) ?></td>
                        <td class="text-capitalize"><?= htmlspecialchars($row->task_username) ?></td>
                        <td><a style="color:<?=$backgroundColorNew;?>!important" target="_BLANK" href="<?=base_url();?>/Menu/CompanyDetails/<?=$row->cid?>"><?= htmlspecialchars($row->cid) ?></a></td>
                        <td class="text-capitalize"><a style="color:<?=$backgroundColorNew;?>!important" target="_BLANK" href="<?=base_url();?>/Menu/CompanyDetails/<?=$row->cid?>"><?= htmlspecialchars($row->compname) ?></a></td>
                        
                        <td><?= htmlspecialchars($row->main_bd_name) ?></td>
                        <td><?= htmlspecialchars($row->main_bd_manager_name) ?></td>

                         <td><?php 
                        if(!empty($row->pst_name) && !is_null($row->pst_name)){
                            echo htmlspecialchars($row->pst_name);
                        }else{
                            echo 'NA';
                        }
                         ?></td>

                        <td><?php 
                        if(!empty($row->ash_nae_co_id_name) && !is_null($row->ash_nae_co_id_name)){
                            echo htmlspecialchars($row->ash_nae_co_id_name);
                        }else{
                            echo 'NA';
                        }
                         ?></td>

                        <td><?php 
                        if(!empty($row->ash_w_co_id_name) && !is_null($row->ash_w_co_id_name)){
                            echo htmlspecialchars($row->ash_w_co_id_name);
                        }else{
                            echo 'NA';
                        }
                         ?></td>
                        <td><?php 
                        if(!empty($row->ash_s_co_id_name) && !is_null($row->ash_s_co_id_name)){
                            echo htmlspecialchars($row->ash_s_co_id_name);
                        }else{
                            echo 'NA';
                        }
                         ?></td>
                        <td><?php 
                        if(!empty($row->rm_east_co_id_name) && !is_null($row->rm_east_co_id_name)){
                            echo htmlspecialchars($row->rm_east_co_id_name);
                        }else{
                            echo 'NA';
                        }
                         ?></td>
                        <td><?php 
                        if(!empty($row->rm_north_co_id_name) && !is_null($row->rm_north_co_id_name)){
                            echo htmlspecialchars($row->rm_north_co_id_name);
                        }else{
                            echo 'NA';
                        }
                         ?></td>
                        <td><?php 
                        if(!empty($row->acm_co_id_name) && !is_null($row->acm_co_id_name)){
                            echo htmlspecialchars($row->acm_co_id_name);
                        }else{
                            echo 'NA';
                        }
                         ?></td>
                        
                        <td><?= htmlspecialchars($row->current_status) ?></td>
                         <?php if($mtypes == 'total_in_q1_twetenty_closure_funnel'){ ?>
                          <td class="text-capitalize"><?= htmlspecialchars($row->q1_twetenty_closure_funnel) ?></td>
                        <?php }?>
                        <?php if($mtypes == 'total_in_potential_funnel_for_fy'){ ?>
                          <td class="text-capitalize"><?= htmlspecialchars($row->potential_funnel_for_fy) ?></td>
                        <?php }?>
                        <?php if($mtypes == 'total_in_to_be_nurtured_for_fy'){ ?>
                          <td class="text-capitalize"><?= htmlspecialchars($row->to_be_nurtured_for_fy) ?></td>
                        <?php }?>
                        <?php if($mtypes == 'total_in_fifity_new_lead_funnel'){ ?>
                          <td class="text-capitalize"><?= htmlspecialchars($row->fifity_new_lead_funnel) ?></td>
                        <?php }?>
                        <td><?= htmlspecialchars($row->task_name) ?></td>
                        <td><?php 
                        if($row->nextCFID == 0){
                            echo "<span class='bg-warning p-1'> Pending </span>"; 
                        }else{
                            echo "<span class='bg-success p-1'> Complete </span>"; 
                        }
                        ?></td>
                        <td class="text-capitalize"><?= htmlspecialchars($row->actontaken) ?></td>
                        <td class="text-capitalize"><?= htmlspecialchars($row->purpose_achieved) ?></td>
                        <td><?= htmlspecialchars($row->task_time_status) ?></td>
                        <td><?= htmlspecialchars($row->task_time_new_status) ?></td>
                        <td><?= htmlspecialchars($row->fwd_date) ?></td>
                        <td><?= htmlspecialchars($row->appointmentdatetime) ?></td>
                        <td><?= htmlspecialchars($row->initiateddt) ?></td>
                        <td><?= htmlspecialchars($row->updated_at) ?></td>
                        <td><?= htmlspecialchars($row->total_time_taken) ?></td>
                        <td><?= htmlspecialchars($row->late_remarks_message) ?></td>
                        <td><?= htmlspecialchars($row->task_approved_by) ?></td>
                        <td><?= htmlspecialchars($row->remarks) ?></td>
                        
                        <td><?php
                        if (
                            !empty($row->special_remarks) &&
                            $row->special_remarks !== null &&
                            $row->special_remarks !== 'null' &&
                            $row->special_remarks !== '[]'
                        ) { ?>
                           <button type="button" class="btn btn-primary"  onclick="SpecialRemarks(<?=$meeting_task_id ?>)">view</button>
                       <?php }else{
                          echo 'NA';
                        }
                       
                        
                        ?></td>
                        <td><?= htmlspecialchars($row->closing_timeline) ?></td>
                        <td class="text-capitalize"><?= htmlspecialchars($row->partner_name) ?></td>
                        <td class="text-capitalize"><?= htmlspecialchars($row->potential) ?></td>
                        <td class="text-capitalize"><?= htmlspecialchars($row->topspender) ?></td>
                        <td class="text-capitalize"><?= htmlspecialchars($row->upsell_client) ?></td>
                        <td class="text-capitalize"><?= htmlspecialchars($row->focus_funnel) ?></td>
                        <td class="text-capitalize"><?= htmlspecialchars($row->keycompany) ?></td>
                        <td class="text-capitalize"><?= htmlspecialchars($row->pkclient) ?></td>
                        <td class="text-capitalize"><?= htmlspecialchars($row->priorityc) ?></td>

                        <td class="text-capitalize"><?php 
            
                        if (isset($row->q1_twetenty_closure_funnel) && $row->q1_twetenty_closure_funnel !== '' && $row->q1_twetenty_closure_funnel !== null) {
                            if($row->q1_twetenty_closure_funnel == 'NULL'){
                              echo 'NA';
                            }else{
                              echo htmlspecialchars($row->q1_twetenty_closure_funnel);
                            }
                        }else{
                            echo 'NA';
                        }
                         ?></td>
                        <td class="text-capitalize"><?php 
                           if (isset($row->potential_funnel_for_fy) && $row->potential_funnel_for_fy !== '' && $row->potential_funnel_for_fy !== null) {
                            if($row->potential_funnel_for_fy == 'NULL'){
                              echo 'NA';
                            }else{
                              echo htmlspecialchars($row->potential_funnel_for_fy);
                            }
                        }else{
                            echo 'NA';
                        }
                         ?></td>
                        <td class="text-capitalize"><?php 
                          if (isset($row->to_be_nurtured_for_fy) && $row->to_be_nurtured_for_fy !== '' && $row->to_be_nurtured_for_fy !== null) {
                            if($row->to_be_nurtured_for_fy == 'NULL'){
                              echo 'NA';
                            }else{
                              echo htmlspecialchars($row->to_be_nurtured_for_fy);
                            }
                          } else {
                              echo 'NA';
                          }
                         ?></td>
                        <td class="text-capitalize"><?php 

                           if (isset($row->fifity_new_lead_funnel) && $row->fifity_new_lead_funnel !== '' && $row->fifity_new_lead_funnel !== null) {
                            if($row->fifity_new_lead_funnel == 'NULL'){
                              echo 'NA';
                            }else{
                              echo htmlspecialchars($row->fifity_new_lead_funnel);
                            }
                        }else{
                            echo 'NA';
                        }

                         ?></td>

                        <td><?php 
                        if(sizeof($filter_by) > 0){
                            foreach ($filter_by as $key => $value) :
                                if($key == 'Plan_BY'){
                                echo $value."<br/>";
                                }elseif($key == 'Filter_By'){
                                echo $value."<br/>";
                                }else{
                                if($key == 'comp_status'){
                                $tstatus = $this->Menu_model->get_statusbyid($value);
                                $tstatusname = $tstatus[0]->name;
                                echo "<span class='p-1'>Status&nbsp;:&nbsp;".$tstatusname."</span><br/>";
                                }elseif($key == 'task'){
                                $taction = $this->Menu_model->get_actionbyid($value);
                                $tactionname = $taction[0]->name;
                                echo "<span class='p-1'>Task&nbsp;:&nbsp;".$tactionname."</span><br/>";
                                }elseif($key == 'taskActionbyuser'){
                                echo "<span class='p-1'>Action&nbsp;Taken:&nbsp;".$value."</span><br/>";
                                }else{
                                    if($selectby !==''){
                                        echo $selectby;
                                    }else if ($row->comments !==''){
                                        echo $row->comments;
                                    }else{
                                        $row->reviewtype;
                                    }
                                }
                                }
                            endforeach;
                        }else{
                            if($selectby !==''){
                                echo $selectby;
                            }else if ($row->comments !==''){
                                echo $row->comments;
                            }else{
                                $row->reviewtype;
                            }
                        }
                            ?></td>
                        <td> 
                            <a style="color:<?=$backgroundColorNew;?>!important" href="<?=base_url();?>Menu/ReplanedLog/<?=$row->task_id;  ?>" target="_BLANK"><?= htmlspecialchars($row->plan_count) ?></a>
                        </td>
                      
                    </tr>
              <?php $i++; } ?>
            </tbody>
                  </table>
                </div>
                <hr />
              </div>
            </div>








             <div class="taskcountcard">
                  <?php
                    //  dd($selectbyFilter);
                      // dd($filterused);
                    $colors = [
                    "#e3f2fd", // blue
                    "#fce4ec", // pink
                    "#e8f5e9", // green
                    "#fff3e0", // orange
                    "#ede7f6", // purple
                    "#f5facbff", // lime
                    "#f3e5f5", // light purple
                    "#c4c2f3ff",  // teal
                    "#cdf3f1ff",  // teal
                    "#f5cae8ff",  // teal
                    "#edf6c1ff"  // teal
                    ];
                    $index = 0;
                    ?>
                  <div class="card shadow-sm border-0 rounded-4 mb-4">
                    <div class="card-header text-center" style="background: linear-gradient(to right, #f6e7f3, #ffffff); border-radius: 12px; box-shadow: 0 4px 8px rgba(0,0,0,0.1); padding: 20px;">
                      <h5 class="m-0 premium-color-1" style="color: #ff009b;">🧩🔍 Total Filters Used for Plan Tasks</h5> 
                    <small class="m-2">
                      The "Total Filters Used for Plan Tasks" metric represents the number of filters applied by users while organizing, viewing, or managing planned tasks. 📅 This metric helps assess how users interact with task planning features and how frequently they narrow down data to find specific insights. 🎯 By analyzing filter usage, organizations can identify user preferences, improve UI/UX for planning tools, and optimize task management workflows. ⚙️ It also supports smarter decision-making by revealing how users refine and explore their task data. 📊
                    </small>
                    </div>
                    <div class="card-body">
                      <div class="row">
                        <?php foreach ($selectbyFilter as $label => $value): ?>
                        <?php
                          $bgColor = $colors[$index % count($colors)];
                          $desc = isset($descriptions[$label]) ? $descriptions[$label] : '';
                          $index++;
                          ?>
                        <div class="col-md-3 mb-3">
                          <div class="rounded p-3" style="height:150px;background-color: <?= $bgColor ?>;box-shadow: rgba(50, 50, 93, 0.25) 0px 2px 5px -1px, rgba(0, 0, 0, 0.3) 0px 1px 3px -1px;">
                            <h6 class="mb-1 font-weight-bold text-center"><?= $label ?></h6>
                            <hr>
                            <div class="text-center">
                              <span class="badge badge-pill badge-dark" style="font-size: 1.1rem;"><?= $value ?></span>
                            </div>
                          </div>
                        </div>
                        <?php endforeach; ?>
                      </div>
                    </div>
                  </div>
                </div>











            </div>
          </div>
        </section>
      


              <div class="modal fade" id="specialRemarksModal" tabindex="-1" role="dialog" aria-labelledby="specialRemarksTitle" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered" role="document">
                                <div class="modal-content">
                                  <div class="modal-header">
                                    <h5 class="modal-title" id="specialRemarksTitle">Special Remarks</h5>
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                      <span aria-hidden="true">&times;</span>
                                    </button>
                                  </div>
                                  <div class="modal-body">
                                      <div id="specialContent">

                                      </div>
                                  </div>
                                </div>
                              </div>
                            </div>

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
       <script>


         function SpecialRemarks(id){
              
                  $.ajax({
                  url:'<?=base_url();?>Menu/GetSpecialRemarksUsingTaskID',
                  type: "POST",
                  data: {
                  taskid: id
                  },
                  cache: false,
                  success: function a(result){
                    $('#specialRemarksModal').modal('show');
                    $("#specialContent").html(result);
                  }
                });
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
          "buttons": ["copy", "csv", "excel", "pdf", "print", "colvis"]
        }).buttons().container().appendTo('#example1_wrapper .col-md-6:eq(0)');
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

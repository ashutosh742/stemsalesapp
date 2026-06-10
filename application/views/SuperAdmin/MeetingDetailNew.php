<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Meeting Details | STEM APP | WebAPP</title>
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
    .card-count-heading{
      color: #ff0022;
    text-decoration: none;
    background-color: yellow;
    padding: 4px 10px;
    }
    .content-header{
        padding: 35px 50px 35px 50px;
    border-radius: 10px;
    background: linear-gradient(145deg, #e2e8ec, #ffffff);
    box-shadow: 5px 5px 15px #D1D9E6, -5px -5px 15px #ffffff;
    }
    html {
  scroll-behavior: smooth;
}
.col.text-center.formcenterData {
    align-items: center;
    justify-content: center;
    display: flex
;
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
              <div class="col-md-12 text-center">
                <div class="frame-layer-1">
                  <div class="frame-layer-2">
                    <div class="frame-layer-3">
                      <h1 class="m-0 premium-color-1" style="color: #ff009b;">Meeting Details</h1>
                      <p class="premium-color-2" style="color: #ff0000;">This section provides a comprehensive overview of all meetings, including statistics on various types of meetings, their statuses, and other relevant details.</p>
                      <p class="premium-color-2" style="color: #2c00ee;"><strong><mark><?=$sdate ?> To <?=$edate ?></mark></strong></p>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <div class="row mb-2">
       

             

  <?php $clusterPstDatas = $this->Menu_model->GetAdminActiveTeam($user['user_id']);
  // echo 'admin_id_filter = '.$admin_id_filter;
  ?>
  <div class="col text-center formcenterData">
    <div>
      <hr>
        <form action="<?=base_url()?>Menu/MeetingDetailNew" method="post" class="mt-3">
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
                        <option value="all">All</option>
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
                        <option value="all" <?= ($rm_filter == 'all') ? 'selected' : ''; ?>>All</option>
                        <?php foreach ($getTeamsDatas as $getTeamsData) { ?>
                            <option value="<?= $getTeamsData->user_id; ?>" <?= ($getTeamsData->user_id == $rm_filter) ? 'selected' : ''; ?>>
                                <?= $getTeamsData->name; ?>
                            </option>
                        <?php }?>
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
       
              <!-- <div class="col text-center">
                <div class="text-right-data text-center">
                  <form class="form-container p-3" method="POST" action="<?=base_url(); ?>/Menu/MeetingDetailNew">
                    <input type="date" name="sdate" value="<?=$sdate ?>">
                    <input type="date" name="edate" value="<?=$edate ?>">
                    <button type="submit">Filter</button>
                  </form>
                </div>
              </div> -->
     



              
            </div>
          </div>
        </div>

        <?php
        $data = $totalMeetingsUserByDatas[0];

        $total_plan_meetings                     = $data->total_plan_meetings;
        $complete_meetings                       = $data->complete_meetings;
        $not_complete_meetings                   = $data->not_complete_meetings;

        $total_scheduled_meetings                = $data->total_sheduled_meetings;
        $total_complete_scheduled_meetings       = $data->total_complete_sheduled_meetings;
        $not_complete_scheduled_meetings         = $data->not_complete_sheduled_meetings;

        $total_barg_meetings                     = $data->total_barg_meetings;
        $total_complete_barg_meetings            = $data->total_complete_barg_meetings;
        $not_complete_barg_meetings              = $data->not_complete_barg_meetings;

        $total_join_meetings                     = $data->total_join_meetings;
        $total_complete_join_meetings            = $data->total_complete_join_meetings;
        $not_complete_join_meetings              = $data->not_complete_join_meetings;

        $total_RP_meetings                       = $data->total_RP_meetings;
        $total_NO_RP_meetings                    = $data->total_NO_RP_meetings;
        $total_Only_Got_details_meetings         = $data->total_Only_Got_details_meetings;

        $total_Scheduled_RP_meetings             = $data->total_Sheduled_RP_meetings;
        $total_Scheduled_NO_RP_meetings          = $data->total_Sheduled_NO_RP_meetings;
        $total_Scheduled_Only_Got_details_meetings = $data->total_Sheduled_Only_Got_details_meetings;

        $total_Barge_RP_meetings                 = $data->total_Barge_RP_meetings;
        $total_Barge_NO_RP_meetings              = $data->total_Barge_NO_RP_meetings;
        $total_Barge_Only_Got_details_meetings   = $data->total_Barge_Only_Got_details_meetings;

        $total_potential_meetings                = $data->total_potential_meetings;
        $total_non_potential_meetings            = $data->total_non_potential_meetings;
        $total_topspender_meetings               = $data->total_topspender_meetings;
        $total_upsell_client_meetings            = $data->total_upsell_client_meetings;
        $total_focus_funnel_meetings             = $data->total_focus_funnel_meetings;
        $total_keycompany_meetings               = $data->total_keycompany_meetings;
        $total_pkclient_meetings                 = $data->total_pkclient_meetings;
        $total_priorityc_meetings                = $data->total_priorityc_meetings;
        $total_fresh_meetings                    = $data->total_fresh_meetings;
        $total_re_meetings                       = $data->total_re_meetings;

        $total_write_mom_rp_meetings             = $data->total_write_mom_rp_meetings;
        $total_pending_for_write_mom_rp_meeting  = $data->total_pending_for_write_mom_rp_meeting;

        $total_mom_data                          = $data->total_mom_data;
        $total_approved_after_check_mom_data     = $data->total_approved_after_check_mom_data;
        $total_reject_after_check_mom_data       = $data->total_reject_after_check_mom_data;
        $total_NO_RP_after_check_mom_data        = $data->total_NO_RP_after_check_mom_data;
        $total_pending_for_check_mom_data        = $data->total_pending_for_check_mom_data;

        $total_re_meetings_company               = $data->total_re_meetings_company;
        $total_fresh_meetings_task               = $data->total_fresh_meetings_task;
        $total_uniques_meetings                  = $data->total_uniques_meetings;

        $one_to_fifteen                           = $data->one_to_fifteen;
        $fifteen_to_thirteen                      = $data->fifteen_to_thirteen;
        $thirteen_to_sixty                        = $data->thirteen_to_sixty;
        $gretter_than_sixty                       = $data->gretter_than_sixty;

        $total_in_q1_twetenty_closure_funnel      = $data->total_in_q1_twetenty_closure_funnel;
        $total_in_potential_funnel_for_fy         = $data->total_in_potential_funnel_for_fy;
        $total_in_to_be_nurtured_for_fy           = $data->total_in_to_be_nurtured_for_fy;
        $total_in_fifity_new_lead_funnel          = $data->total_in_fifity_new_lead_funnel;

        $total_open                               = $data->total_open;
        $total_reachout                           = $data->total_reachout;
        $total_tentative                          = $data->total_tentative;
        $total_will_do_later                      = $data->total_will_do_later;
        $total_not_interested                     = $data->total_not_interested;
        $total_positive                           = $data->total_positive;
        $total_closure                            = $data->total_closure;
        $total_open_rpem                          = $data->total_open_rpem;
        $total_very_positive                      = $data->total_very_positive;
        $total_ttd_reachout                       = $data->total_ttd_reachout;
        $total_wno_reachout                       = $data->total_wno_reachout;
        $total_positive_nap                       = $data->total_positive_nap;
        $total_very_positive_nap                  = $data->total_very_positive_nap;
        $total_on_boarded                         = $data->total_on_boarded;


        $total_RP_in_open                               = $data->total_RP_in_open;
        $total_RP_in_reachout                           = $data->total_RP_in_reachout;
        $total_RP_in_tentative                          = $data->total_RP_in_tentative;
        $total_RP_in_will_do_later                      = $data->total_RP_in_will_do_later;
        $total_RP_in_not_interested                     = $data->total_RP_in_not_interested;
        $total_RP_in_positive                           = $data->total_RP_in_positive;
        $total_RP_in_closure                            = $data->total_RP_in_closure;
        $total_RP_in_open_rpem                          = $data->total_RP_in_open_rpem;
        $total_RP_in_very_positive                      = $data->total_RP_in_very_positive;
        $total_RP_in_ttd_reachout                       = $data->total_RP_in_ttd_reachout;
        $total_RP_in_wno_reachout                       = $data->total_RP_in_wno_reachout;
        $total_RP_in_positive_nap                       = $data->total_RP_in_positive_nap;
        $total_RP_in_very_positive_nap                  = $data->total_RP_in_very_positive_nap;
        $total_RP_in_on_boarded                         = $data->total_RP_in_on_boarded;


        $total_NO_RP_in_open                               = $data->total_NO_RP_in_open;
        $total_NO_RP_in_reachout                           = $data->total_NO_RP_in_reachout;
        $total_NO_RP_in_tentative                          = $data->total_NO_RP_in_tentative;
        $total_NO_RP_in_will_do_later                      = $data->total_NO_RP_in_will_do_later;
        $total_NO_RP_in_not_interested                     = $data->total_NO_RP_in_not_interested;
        $total_NO_RP_in_positive                           = $data->total_NO_RP_in_positive;
        $total_NO_RP_in_closure                            = $data->total_NO_RP_in_closure;
        $total_NO_RP_in_open_rpem                          = $data->total_NO_RP_in_open_rpem;
        $total_NO_RP_in_very_positive                      = $data->total_NO_RP_in_very_positive;
        $total_NO_RP_in_ttd_reachout                       = $data->total_NO_RP_in_ttd_reachout;
        $total_NO_RP_in_wno_reachout                       = $data->total_NO_RP_in_wno_reachout;
        $total_NO_RP_in_positive_nap                       = $data->total_NO_RP_in_positive_nap;
        $total_NO_RP_in_very_positive_nap                  = $data->total_NO_RP_in_very_positive_nap;
        $total_NO_RP_in_on_boarded                         = $data->total_NO_RP_in_on_boarded;

        
        $total_OGD_in_open                               = $data->total_OGD_in_open;
        $total_OGD_in_reachout                           = $data->total_OGD_in_reachout;
        $total_OGD_in_tentative                          = $data->total_OGD_in_tentative;
        $total_OGD_in_will_do_later                      = $data->total_OGD_in_will_do_later;
        $total_OGD_in_not_interested                     = $data->total_OGD_in_not_interested;
        $total_OGD_in_positive                           = $data->total_OGD_in_positive;
        $total_OGD_in_closure                            = $data->total_OGD_in_closure;
        $total_OGD_in_open_rpem                          = $data->total_OGD_in_open_rpem;
        $total_OGD_in_very_positive                      = $data->total_OGD_in_very_positive;
        $total_OGD_in_ttd_reachout                       = $data->total_OGD_in_ttd_reachout;
        $total_OGD_in_wno_reachout                       = $data->total_OGD_in_wno_reachout;
        $total_OGD_in_positive_nap                       = $data->total_OGD_in_positive_nap;
        $total_OGD_in_very_positive_nap                  = $data->total_OGD_in_very_positive_nap;
        $total_OGD_in_on_boarded                         = $data->total_OGD_in_on_boarded;


        $total_new_company                              = $data->total_new_company;
        $total_new_company_complete                     = $data->total_new_company_complete;
        $total_new_company_pending                      = $data->total_new_company_pending;
        $total_new_company_RP_meetings                  = $data->total_new_company_RP_meetings;
        $total_new_company_NO_RP_meetings               = $data->total_new_company_NO_RP_meetings;
        $total_new_company_Only_Got_details_meetings    = $data->total_new_company_Only_Got_details_meetings;
        $total_new_company_potential_rp_meetings        = $data->total_new_company_potential_rp_meetings;
        $total_new_company_non_rp_potential_meetings    = $data->total_new_company_non_rp_potential_meetings;

        $task_frequency_per_day                         = $data->task_frequency_per_day;
        $task_RP_frequency_per_day                      = $data->task_RP_frequency_per_day;
        $task_NO_RP_frequency_per_day                   = $data->task_NO_RP_frequency_per_day;
        $task_OGD_frequency_per_day                     = $data->task_OGD_frequency_per_day;


        $uid = $admin_id_filter;

        ?>

        <!-- Main content -->
        <section class="content">
          <div class="container-fluid">
            <!-- Total Plan Meetings Group -->
             <div class="taskfrequency" style="background: linear-gradient(to right, rgb(189 255 157 / 34%), rgb(231 2 2 / 10%)); box-shadow: 0 4px 8px rgba(0,0,0,0.1); padding: 20px;">
            <div class="card-group bgbackground">
              <div class="card-group-title">
                Plan Meetings   
                <br>
                <small>This section shows all scheduled plan meetings, including completed and pending ones.</small>
              </div>

              <div class="row">
                <div class="col-md-4">
                  <div class="frame-layer-1">
                    <div class="frame-layer-2">
                      <div class="frame-layer-3" style="background: linear-gradient(145deg, #add984, #add984);">
                        <div class="card text-center card-bg-1">
                          <div class="card-cotent-data">
                            <h4>Total Meetings</h4>
                            <p>All planned meetings.</p>
                            <h5>
                              <a target="_BLANK" class="card-count-heading" href="<?=base_url()?>Menu/MeetingsDatas/total_plan_meetings/<?=$uid?>/<?=$sdate?>/<?=$edate?>"><?= $total_plan_meetings ?></a>
                            </h5>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
                <div class="col-md-4">
                  <div class="frame-layer-1">
                    <div class="frame-layer-2">
                      <div class="frame-layer-3" style="background: linear-gradient(145deg, #add984, #add984);">
                        <div class="card text-center card-bg-2">
                          <div class="card-cotent-data">
                            <h4>Complete Meetings</h4>
                            <p>Meetings that are completed.</p>
                            <h5>
                              <a target="_BLANK" class="card-count-heading" href="<?=base_url()?>Menu/MeetingsDatas/complete_meetings/<?=$uid?>/<?=$sdate?>/<?=$edate?>"><?= $complete_meetings ?></a>
                            </h5>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
                <div class="col-md-4">
                  <div class="frame-layer-1">
                    <div class="frame-layer-2">
                      <div class="frame-layer-3" style="background: linear-gradient(145deg, #add984, #add984);">
                        <div class="card text-center card-bg-3">
                          <div class="card-cotent-data">
                            <h4>Not Complete Meetings</h4>
                            <p>Meetings that are not completed.</p>
                            <h5>
                              <a target="_BLANK" class="card-count-heading" href="<?=base_url()?>Menu/MeetingsDatas/not_complete_meetings/<?=$uid?>/<?=$sdate?>/<?=$edate?>"><?= $not_complete_meetings ?></a>
                            </h5>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            </div>

                <hr>
               <!-- RP Meetings Group -->
                <div class="taskfrequency" style="background: linear-gradient(to right, rgb(92 169 152 / 34%), rgb(231 2 2 / 9%)); box-shadow: 0 4px 8px rgba(0,0,0,0.1); padding: 20px;">
               <div class="card-group bgbackground">
              <div class="card-group-title">
                RP / NO RP / Only Got Details Meetings <br>
                <small>This includes all meetings categorized as RP, No RP, or Only Got Details.</small>
              </div>

              <div class="row">
                <div class="col-md-3">
                  <div class="frame-layer-1">
                    <div class="frame-layer-2">
                      <div class="frame-layer-3" style="background: linear-gradient(145deg, #add984, #add984);">
                        <div class="card text-center card-bg-2">
                          <div class="card-cotent-data">
                            <h4>Complete Meetings</h4>
                            <p>Meetings that are completed.</p>
                            <h5>
                              <a target="_BLANK" class="card-count-heading" href="<?=base_url()?>Menu/MeetingsDatas/complete_meetings/<?=$uid?>/<?=$sdate?>/<?=$edate?>"><?= $complete_meetings ?></a>
                            </h5>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
                <div class="col-md-3">
                  <div class="frame-layer-1">
                    <div class="frame-layer-2">
                      <div class="frame-layer-3">
                        <div class="card text-center card-bg-12">
                          <div class="card-cotent-data">
                            <h4>Total RP Meetings</h4>
                            <p>Meetings with Resource Persons.</p>
                            <h5>
                              <a target="_BLANK" class="card-count-heading" href="<?=base_url()?>Menu/MeetingsDatas/total_RP_meetings/<?=$uid?>/<?=$sdate?>/<?=$edate?>"><?= $total_RP_meetings ?></a>
                            </h5>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
                <div class="col-md-3">
                  <div class="frame-layer-1">
                    <div class="frame-layer-2">
                      <div class="frame-layer-3">
                        <div class="card text-center card-bg-13">
                          <div class="card-cotent-data">
                            <h4>Total NO RP Meetings</h4>
                            <p>Meetings without Resource Persons.</p>
                            <h5>
                              <a target="_BLANK" class="card-count-heading" href="<?=base_url()?>Menu/MeetingsDatas/total_NO_RP_meetings/<?=$uid?>/<?=$sdate?>/<?=$edate?>"><?= $total_NO_RP_meetings ?></a>
                            </h5>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
                <div class="col-md-3">
                  <div class="frame-layer-1">
                    <div class="frame-layer-2">
                      <div class="frame-layer-3">
                        <div class="card text-center card-bg-14">
                          <div class="card-cotent-data">
                            <h4>Total Only Got Details Meetings</h4>
                            <p>Meetings with only details obtained.</p>
                            <h5>
                              <a target="_BLANK" class="card-count-heading" href="<?=base_url()?>Menu/MeetingsDatas/total_Only_Got_details_meetings/<?=$uid?>/<?=$sdate?>/<?=$edate?>"><?= $total_Only_Got_details_meetings ?></a>
                            </h5>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            </div>


              <hr>
              <!-- RP Meetings Group -->
                <div class="taskfrequency" style="background: linear-gradient(to right, rgb(157 255 233 / 34%), rgb(231 2 2 / 10%)); box-shadow: 0 4px 8px rgba(0,0,0,0.1); padding: 20px;">
               <div class="card-group bgbackground">
              <div class="card-group-title">
                RP Fresh / Re Meetings Details <br>
                <small>This section provides detailed information about the fresh and re-meetings related to Resource Planning (RP). It includes data on new meetings scheduled and follow-up meetings conducted.</small>
              </div>

              <div class="row">
                <div class="col-md-4">
                  <div class="frame-layer-1">
                    <div class="frame-layer-2">
                      <div class="frame-layer-3">
                        <div class="card text-center card-bg-12">
                          <div class="card-cotent-data">
                            <h4>Total RP Meetings</h4>
                            <p>Meetings with Resource Persons.</p>
                             <p class="card-count-heading">total companies <?=$total_uniques_meetings?></p>
                            <h5>
                              <a target="_BLANK" class="card-count-heading" href="<?=base_url()?>Menu/MeetingsDatas/total_RP_meetings/<?=$uid?>/<?=$sdate?>/<?=$edate?>"><?= $total_RP_meetings ?></a>
                            </h5>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              <div class="col-md-4">
                  <div class="frame-layer-1">
                    <div class="frame-layer-2">
                      <div class="frame-layer-3">
                        <div class="card text-center card-bg-13">
                          <div class="card-cotent-data">
                            <h4>Total RP Meetings On Unique Companies</h4>
                            <p>New meetings conducted.</p>
                            
                             <p class="card-count-heading">On companies <?=$total_fresh_meetings?></p>
                            <h5>
                              <a target="_BLANK" class="card-count-heading" href="<?=base_url()?>Menu/MeetingsDatas/total_fresh_meetings/<?=$uid?>/<?=$sdate?>/<?=$edate?>">Task - <?= $total_fresh_meetings_task ?></a>
                            </h5>
                
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
                <div class="col-md-4">
                  <div class="frame-layer-1">
                    <div class="frame-layer-2">
                      <div class="frame-layer-3">
                        <div class="card text-center card-bg-14">
                          <div class="card-cotent-data">
                            <h4>Total RP Re Meetings On Unique Companies</h4>
                            <p>Follow-up meetings conducted.</p>
                            <p class="card-count-heading">On companies <?=$total_re_meetings_company?></p>
                            <h5>
                              <a target="_BLANK" class="card-count-heading" href="<?=base_url()?>Menu/MeetingsDatas/total_re_meetings/<?=$uid?>/<?=$sdate?>/<?=$edate?>">Task <?= $total_re_meetings ?></a>
                            </h5>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            </div>


              <hr>
            <!-- Scheduled Meetings Group -->
                  <div class="taskfrequency" style="background: linear-gradient(to right, rgb(254 255 157 / 63%), rgb(4 2 231 / 3%)); box-shadow: 0 4px 8px rgba(0,0,0,0.1); padding: 20px;">
            <div class="card-group bgbackground">
              <div class="card-group-title">
                Total Task Meetings <br>
                <small>This section provides an overview of the total number of task meetings completed.</small>
              </div>

              <div class="row">
                 <div class="col-md-4">
                  <div class="frame-layer-1">
                    <div class="frame-layer-2">
                      <div class="frame-layer-3" style="background: linear-gradient(145deg, #add984, #add984);">
                        <div class="card text-center card-bg-1">
                          <div class="card-cotent-data">
                            <h4>Total Meetings</h4>
                            <p>All planned meetings.</p>
                            <h5>
                              <a target="_BLANK" class="card-count-heading" href="<?=base_url()?>Menu/MeetingsDatas/total_plan_meetings/<?=$uid?>/<?=$sdate?>/<?=$edate?>"><?= $total_plan_meetings ?></a>
                            </h5>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
                <div class="col-md-4">
                  <div class="frame-layer-1">
                    <div class="frame-layer-2">
                      <div class="frame-layer-3">
                        <div class="card text-center card-bg-4">
                          <div class="card-cotent-data">
                            <h4>Total Scheduled Meetings</h4>
                            <p>All scheduled meetings.</p>
                            <h5>
                              <a target="_BLANK" class="card-count-heading" href="<?=base_url()?>Menu/MeetingsDatas/total_scheduled_meetings/<?=$uid?>/<?=$sdate?>/<?=$edate?>"><?= $total_scheduled_meetings ?></a>
                            </h5>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
                <div class="col-md-4">
                  <div class="frame-layer-1">
                    <div class="frame-layer-2">
                      <div class="frame-layer-3">
                        <div class="card text-center card-bg-6">
                          <div class="card-cotent-data">
                            <h4>Total Barge Meetings</h4>
                            <p>All barge meetings.</p>
                            <h5>
                              <a target="_BLANK" class="card-count-heading" href="<?=base_url()?>Menu/MeetingsDatas/total_barg_meetings/<?=$uid?>/<?=$sdate?>/<?=$edate?>"><?= $total_barg_meetings ?></a>
                            </h5>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            </div>


            <hr>
            <!-- Scheduled Meetings Group -->
               <div class="taskfrequency" style="background: linear-gradient(to right, rgb(254 255 157 / 63%), rgb(4 2 231 / 3%)); box-shadow: 0 4px 8px rgba(0,0,0,0.1); padding: 20px;">
            <div class="card-group bgbackground">
              <div class="card-group-title">
                Total Scheduled Meetings <br>
                <small>This section displays the total number of meetings that have been scheduled.</small>
              </div>

              <div class="row">
             
                <div class="col-md-4">
                  <div class="frame-layer-1">
                    <div class="frame-layer-2">
                      <div class="frame-layer-3">
                        <div class="card text-center card-bg-4">
                          <div class="card-cotent-data">
                            <h4>Total Scheduled Meetings</h4>
                            <p>All scheduled meetings.</p>
                            <h5>
                              <a target="_BLANK" class="card-count-heading" href="<?=base_url()?>Menu/MeetingsDatas/total_scheduled_meetings/<?=$uid?>/<?=$sdate?>/<?=$edate?>"><?= $total_scheduled_meetings ?></a>
                            </h5>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
         
                <div class="col-md-4">
                  <div class="frame-layer-1">
                    <div class="frame-layer-2">
                      <div class="frame-layer-3">
                        <div class="card text-center card-bg-4">
                          <div class="card-cotent-data">
                            <h4>Total Complete Scheduled Meetings</h4>
                            <p>Scheduled meetings that are completed.</p>
                            <h5>
                              <a target="_BLANK" class="card-count-heading" href="<?=base_url()?>Menu/MeetingsDatas/total_complete_scheduled_meetings/<?=$uid?>/<?=$sdate?>/<?=$edate?>"><?= $total_complete_scheduled_meetings ?></a>
                            </h5>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
                <div class="col-md-4">
                  <div class="frame-layer-1">
                    <div class="frame-layer-2">
                      <div class="frame-layer-3">
                        <div class="card text-center card-bg-5">
                          <div class="card-cotent-data">
                            <h4>Not Complete Scheduled Meetings</h4>
                            <p>Scheduled meetings that are not completed.</p>
                            <h5>
                              <a target="_BLANK" class="card-count-heading" href="<?=base_url()?>Menu/MeetingsDatas/not_complete_scheduled_meetings/<?=$uid?>/<?=$sdate?>/<?=$edate?>"><?= $not_complete_scheduled_meetings ?></a>
                            </h5>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            </div>










              <hr>
              <!-- Scheduled RP Meetings Group -->
                <div class="taskfrequency" style="    background: linear-gradient(to right, rgb(254 255 157 / 63%), rgb(113 112 255 / 35%)); box-shadow: 0 4px 8px rgba(0,0,0,0.1); padding: 20px;">
              <div class="card-group bgbackground">
             <div class="card-group-title">
                  RP / NO RP / Only Got Details (Barge Meetings) <br>
                  <small>This section provides insights into the frequency of different types of meetings, including Resource Planning (RP) meetings, Non-Resource Planning (NO RP) meetings, and meetings where only details were gathered.</small>
                </div>

              <div class="row">
                <div class="col-md-3">
                  <div class="frame-layer-1">
                    <div class="frame-layer-2">
                      <div class="frame-layer-3">
                        <div class="card text-center card-bg-4">
                          <div class="card-cotent-data">
                            <h4>Total Complete Scheduled Meetings</h4>
                            <p>Scheduled meetings that are completed.</p>
                            <h5>
                              <a target="_BLANK" class="card-count-heading" href="<?=base_url()?>Menu/MeetingsDatas/total_complete_scheduled_meetings/<?=$uid?>/<?=$sdate?>/<?=$edate?>"><?= $total_complete_scheduled_meetings ?></a>
                            </h5>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
                <div class="col-md-3">
                  <div class="frame-layer-1">
                    <div class="frame-layer-2">
                      <div class="frame-layer-3">
                        <div class="card text-center card-bg-15">
                          <div class="card-cotent-data">
                            <h4>Total Scheduled RP Meetings</h4>
                            <p>Scheduled meetings with Resource Persons.</p>
                            <h5>
                              <a target="_BLANK" class="card-count-heading" href="<?=base_url()?>Menu/MeetingsDatas/total_Scheduled_RP_meetings/<?=$uid?>/<?=$sdate?>/<?=$edate?>"><?= $total_Scheduled_RP_meetings ?></a>
                            </h5>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
                <div class="col-md-3">
                  <div class="frame-layer-1">
                    <div class="frame-layer-2">
                      <div class="frame-layer-3">
                        <div class="card text-center card-bg-16">
                          <div class="card-cotent-data">
                            <h4>Total Scheduled NO RP Meetings</h4>
                            <p>Scheduled meetings without Resource Persons.</p>
                            <h5>
                              <a target="_BLANK" class="card-count-heading" href="<?=base_url()?>Menu/MeetingsDatas/total_Scheduled_NO_RP_meetings/<?=$uid?>/<?=$sdate?>/<?=$edate?>"><?= $total_Scheduled_NO_RP_meetings ?></a>
                            </h5>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
                <div class="col-md-3">
                  <div class="frame-layer-1">
                    <div class="frame-layer-2">
                      <div class="frame-layer-3">
                        <div class="card text-center card-bg-1">
                          <div class="card-cotent-data">
                            <h4>Total Scheduled Only Got Details Meetings</h4>
                            <p>Scheduled meetings with only details obtained.</p>
                            <h5>
                              <a target="_BLANK" class="card-count-heading" href="<?=base_url()?>Menu/MeetingsDatas/total_Scheduled_Only_Got_details_meetings/<?=$uid?>/<?=$sdate?>/<?=$edate?>"><?= $total_Scheduled_Only_Got_details_meetings ?></a>
                            </h5>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            </div>
<hr>
            <!-- Barge Meetings Group -->
              <div class="taskfrequency" style="background: linear-gradient(to right, rgb(197 255 157 / 63%), rgb(235 6 217 / 35%)); box-shadow: 0 4px 8px rgba(0,0,0,0.1); padding: 20px;">
            <div class="card-group bgbackground">
              <div class="card-group-title">
                  Total Barge Meetings <br>
                  <small>This section provides an overview of the total number of barge meetings conducted.</small>
                </div>

              <div class="row">
                <div class="col-md-4">
                  <div class="frame-layer-1">
                    <div class="frame-layer-2">
                      <div class="frame-layer-3">
                        <div class="card text-center card-bg-6">
                          <div class="card-cotent-data">
                            <h4>Total Barge Meetings</h4>
                            <p>All barge meetings.</p>
                            <h5>
                              <a target="_BLANK" class="card-count-heading" href="<?=base_url()?>Menu/MeetingsDatas/total_barg_meetings/<?=$uid?>/<?=$sdate?>/<?=$edate?>"><?= $total_barg_meetings ?></a>
                            </h5>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
                <div class="col-md-4">
                  <div class="frame-layer-1">
                    <div class="frame-layer-2">
                      <div class="frame-layer-3">
                        <div class="card text-center card-bg-7">
                          <div class="card-cotent-data">
                            <h4>Total Complete Barge Meetings</h4>
                            <p>Barge meetings that are completed.</p>
                            <h5>
                              <a target="_BLANK" class="card-count-heading" href="<?=base_url()?>Menu/MeetingsDatas/total_complete_barg_meetings/<?=$uid?>/<?=$sdate?>/<?=$edate?>"><?= $total_complete_barg_meetings ?></a>
                            </h5>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
                <div class="col-md-4">
                  <div class="frame-layer-1">
                    <div class="frame-layer-2">
                      <div class="frame-layer-3">
                        <div class="card text-center card-bg-8">
                          <div class="card-cotent-data">
                            <h4>Not Complete Barge Meetings</h4>
                            <p>Barge meetings that are not completed.</p>
                            <h5>
                              <a target="_BLANK" class="card-count-heading" href="<?=base_url()?>Menu/MeetingsDatas/not_complete_barg_meetings/<?=$uid?>/<?=$sdate?>/<?=$edate?>"><?= $not_complete_barg_meetings ?></a>
                            </h5>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            </div>
            <hr>

            <?php /*
            <!-- Join Meetings Group -->
            <div class="card-group bgbackground">
              <div class="card-group-title">Join Meetings</div>
              <div class="row">
                <div class="col-md-4">
                  <div class="frame-layer-1">
                    <div class="frame-layer-2">
                      <div class="frame-layer-3">
                        <div class="card text-center card-bg-9">
                          <div class="card-cotent-data">
                            <h4>Total Join Meetings</h4>
                            <p>All join meetings.</p>
                            <h5>
                              <a target="_BLANK" class="card-count-heading" href="<?=base_url()?>Menu/MeetingsDatas/total_join_meetings/<?=$uid?>/<?=$sdate?>/<?=$edate?>"><?= $total_join_meetings ?></a>
                            </h5>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
                <div class="col-md-4">
                  <div class="frame-layer-1">
                    <div class="frame-layer-2">
                      <div class="frame-layer-3">
                        <div class="card text-center card-bg-10">
                          <div class="card-cotent-data">
                            <h4>Total Complete Join Meetings</h4>
                            <p>Join meetings that are completed.</p>
                            <h5>
                              <a target="_BLANK" class="card-count-heading" href="<?=base_url()?>Menu/MeetingsDatas/total_complete_join_meetings/<?=$uid?>/<?=$sdate?>/<?=$edate?>"><?= $total_complete_join_meetings ?></a>
                            </h5>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
                <div class="col-md-4">
                  <div class="frame-layer-1">
                    <div class="frame-layer-2">
                      <div class="frame-layer-3">
                        <div class="card text-center card-bg-11">
                          <div class="card-cotent-data">
                            <h4>Not Complete Join Meetings</h4>
                            <p>Join meetings that are not completed.</p>
                            <h5>
                              <a target="_BLANK" class="card-count-heading" href="<?=base_url()?>Menu/MeetingsDatas/not_complete_join_meetings/<?=$uid?>/<?=$sdate?>/<?=$edate?>"><?= $not_complete_join_meetings ?></a>
                            </h5>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            */ ?>
         

          

            <!-- Barge RP Meetings Group -->
              <div class="taskfrequency" style="background: linear-gradient(to right, rgb(157 255 205 / 63%), rgb(255 187 112 / 35%)); box-shadow: 0 4px 8px rgba(0,0,0,0.1); padding: 20px;">
            <div class="card-group bgbackground">
            <div class="card-group-title">
                RP / NO RP / Only Got Details (Barge Meetings) <br>
                <small>This section provides insights into the frequency of different types of meetings, including Resource Planning (RP) meetings, Non-Resource Planning (NO RP) meetings, and meetings where only details were gathered.</small>
              </div>

              <div class="row">
                  <div class="col-md-3">
                  <div class="frame-layer-1">
                    <div class="frame-layer-2">
                      <div class="frame-layer-3">
                        <div class="card text-center card-bg-7">
                          <div class="card-cotent-data">
                            <h4>Total Complete Barge Meetings</h4>
                            <p>Barge meetings that are completed.</p>
                            <h5>
                              <a target="_BLANK" class="card-count-heading" href="<?=base_url()?>Menu/MeetingsDatas/total_complete_barg_meetings/<?=$uid?>/<?=$sdate?>/<?=$edate?>"><?= $total_complete_barg_meetings ?></a>
                            </h5>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
                <div class="col-md-3">
                  <div class="frame-layer-1">
                    <div class="frame-layer-2">
                      <div class="frame-layer-3">
                        <div class="card text-center card-bg-2">
                          <div class="card-cotent-data">
                            <h4>Total Barge RP Meetings</h4>
                            <p>Barge meetings with Resource Persons.</p>
                            <h5>
                              <a target="_BLANK" class="card-count-heading" href="<?=base_url()?>Menu/MeetingsDatas/total_Barge_RP_meetings/<?=$uid?>/<?=$sdate?>/<?=$edate?>"><?= $total_Barge_RP_meetings ?></a>
                            </h5>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
                <div class="col-md-3">
                  <div class="frame-layer-1">
                    <div class="frame-layer-2">
                      <div class="frame-layer-3">
                        <div class="card text-center card-bg-3">
                          <div class="card-cotent-data">
                            <h4>Total Barge NO RP Meetings</h4>
                            <p>Barge meetings without Resource Persons.</p>
                            <h5>
                              <a target="_BLANK" class="card-count-heading" href="<?=base_url()?>Menu/MeetingsDatas/total_Barge_NO_RP_meetings/<?=$uid?>/<?=$sdate?>/<?=$edate?>"><?= $total_Barge_NO_RP_meetings ?></a>
                            </h5>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
                <div class="col-md-3">
                  <div class="frame-layer-1">
                    <div class="frame-layer-2">
                      <div class="frame-layer-3">
                        <div class="card text-center card-bg-4">
                          <div class="card-cotent-data">
                            <h4>Total Barge Only Got Details Meetings</h4>
                            <p>Barge meetings with only details obtained.</p>
                            <h5>
                              <a target="_BLANK" class="card-count-heading" href="<?=base_url()?>Menu/MeetingsDatas/total_Barge_Only_Got_details_meetings/<?=$uid?>/<?=$sdate?>/<?=$edate?>"><?= $total_Barge_Only_Got_details_meetings ?></a>
                            </h5>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            </div>

            <hr>
            <!-- Meeting Types Group -->
              <div class="taskfrequency" style="background: linear-gradient(to right, rgb(157 192 255 / 63%), rgb(183 255 112 / 35%)); box-shadow: 0 4px 8px rgba(0,0,0,0.1); padding: 20px;">
            <div class="card-group bgbackground">
              <div class="card-group-title">Meeting Types <br>
<small>This section provides an overview of the different types of meetings conducted. Each card represents a specific category of meetings, offering insights into their frequency and details.</small></div>

              <div class="row">
                <div class="col-md-4">
                  <div class="frame-layer-1">
                    <div class="frame-layer-2">
                      <div class="frame-layer-3">
                        <div class="card text-center card-bg-5">
                          <div class="card-cotent-data">
                            <h4>Total Potential Meetings</h4>
                            <p>Meetings with potential leads.</p>
                            <h5>
                              <a target="_BLANK" class="card-count-heading" href="<?=base_url()?>Menu/MeetingsDatas/total_potential_meetings/<?=$uid?>/<?=$sdate?>/<?=$edate?>"><?= $total_potential_meetings ?></a>
                            </h5>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
                <div class="col-md-4">
                  <div class="frame-layer-1">
                    <div class="frame-layer-2">
                      <div class="frame-layer-3">
                        <div class="card text-center card-bg-6">
                          <div class="card-cotent-data">
                            <h4>Total Non Potential Meetings</h4>
                            <p>Meetings without potential leads.</p>
                            <h5>
                              <a target="_BLANK" class="card-count-heading" href="<?=base_url()?>Menu/MeetingsDatas/total_non_potential_meetings/<?=$uid?>/<?=$sdate?>/<?=$edate?>"><?= $total_non_potential_meetings ?></a>
                            </h5>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
                <div class="col-md-4">
                  <div class="frame-layer-1">
                    <div class="frame-layer-2">
                      <div class="frame-layer-3">
                        <div class="card text-center card-bg-7">
                          <div class="card-cotent-data">
                            <h4>Total Top Spender Meetings</h4>
                            <p>Meetings with top spenders.</p>
                            <h5>
                              <a target="_BLANK" class="card-count-heading" href="<?=base_url()?>Menu/MeetingsDatas/total_topspender_meetings/<?=$uid?>/<?=$sdate?>/<?=$edate?>"><?= $total_topspender_meetings ?></a>
                            </h5>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            </div>
              <hr>
            <!-- Additional Meeting Types Group -->
              <div class="taskfrequency" style="background: linear-gradient(to right, rgb(213 255 157 / 63%), rgb(255 112 227 / 35%)); box-shadow: 0 4px 8px rgba(0,0,0,0.1); padding: 20px;">
            <div class="card-group bgbackground">
             <div class="card-group-title">Additional Meeting Types <br>
                <small>This section includes various other types of meetings that are tracked and analyzed for comprehensive insights.</small>
              </div>

              <div class="row">
                <div class="col-md-4">
                  <div class="frame-layer-1">
                    <div class="frame-layer-2">
                      <div class="frame-layer-3">
                        <div class="card text-center card-bg-8">
                          <div class="card-cotent-data">
                            <h4>Total Upsell Client Meetings</h4>
                            <p>Meetings with upsell clients.</p>
                            <h5>
                              <a target="_BLANK" class="card-count-heading" href="<?=base_url()?>Menu/MeetingsDatas/total_upsell_client_meetings/<?=$uid?>/<?=$sdate?>/<?=$edate?>"><?= $total_upsell_client_meetings ?></a>
                            </h5>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
                <div class="col-md-4">
                  <div class="frame-layer-1">
                    <div class="frame-layer-2">
                      <div class="frame-layer-3">
                        <div class="card text-center card-bg-9">
                          <div class="card-cotent-data">
                            <h4>Total Focus Funnel Meetings</h4>
                            <p>Meetings with focus funnel.</p>
                            <h5>
                              <a target="_BLANK" class="card-count-heading" href="<?=base_url()?>Menu/MeetingsDatas/total_focus_funnel_meetings/<?=$uid?>/<?=$sdate?>/<?=$edate?>"><?= $total_focus_funnel_meetings ?></a>
                            </h5>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
                <div class="col-md-4">
                  <div class="frame-layer-1">
                    <div class="frame-layer-2">
                      <div class="frame-layer-3">
                        <div class="card text-center card-bg-10">
                          <div class="card-cotent-data">
                            <h4>Total Key Client Meetings</h4>
                            <p>Meetings with Key companies.</p>
                            <h5>
                              <a target="_BLANK" class="card-count-heading" href="<?=base_url()?>Menu/MeetingsDatas/total_keycompany_meetings/<?=$uid?>/<?=$sdate?>/<?=$edate?>"><?= $total_keycompany_meetings ?></a>
                            </h5>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            </div>
              <hr>
            <!-- More Meeting Types Group -->
             <div class="taskfrequency" style="background: linear-gradient(to right, rgb(255 157 157 / 63%), rgb(183 255 112 / 35%)); box-shadow: 0 4px 8px rgba(0,0,0,0.1); padding: 20px;">
            <div class="card-group bgbackground">
              <div class="card-group-title">
                More Meeting Types <br>
                <small>This section provides additional details on various types of meetings.</small>
              </div>

              <div class="row">
                <div class="col-md-6">
                  <div class="frame-layer-1">
                    <div class="frame-layer-2">
                      <div class="frame-layer-3">
                        <div class="card text-center card-bg-11">
                          <div class="card-cotent-data">
                            <h4>Total Positive Key Client Meetings</h4>
                            <p>Meetings with Positive Key clients.</p>
                            <h5>
                              <a target="_BLANK" class="card-count-heading" href="<?=base_url()?>Menu/MeetingsDatas/total_pkclient_meetings/<?=$uid?>/<?=$sdate?>/<?=$edate?>"><?= $total_pkclient_meetings ?></a>
                            </h5>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="frame-layer-1">
                    <div class="frame-layer-2">
                      <div class="frame-layer-3">
                        <div class="card text-center card-bg-12">
                          <div class="card-cotent-data">
                            <h4>Total Priority Calling Meetings</h4>
                            <p>Meetings with priority clients.</p>
                            <h5>
                              <a target="_BLANK" class="card-count-heading" href="<?=base_url()?>Menu/MeetingsDatas/total_priorityc_meetings/<?=$uid?>/<?=$sdate?>/<?=$edate?>"><?= $total_priorityc_meetings ?></a>
                            </h5>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            </div>
 <hr>
            <!-- Final Meeting Types Group -->
              <div class="taskfrequency" style="background: linear-gradient(to right, rgb(255 157 251 / 63%), rgb(183 255 112 / 35%)); box-shadow: 0 4px 8px rgba(0,0,0,0.1); padding: 20px;">
            <div class="card-group bgbackground">
             <div class="card-group-title">Write MOM OF Meeting Types <br>
                <small>This section is dedicated to documenting the Minutes of the Meeting (MOM) for various types of meetings. It helps in keeping track of discussions, decisions, and action items.</small>
              </div>

              <div class="row">
                <div class="col-md-6">
                  <div class="frame-layer-1">
                    <div class="frame-layer-2">
                      <div class="frame-layer-3">
                        <div class="card text-center card-bg-15">
                          <div class="card-cotent-data">
                            <h4>Total Write MOM RP Meetings</h4>
                            <p>Minutes of Meetings with Resource Persons.</p>
                            <h5>
                              <a target="_BLANK" class="card-count-heading" href="<?=base_url()?>Menu/MeetingsDatas/total_write_mom_rp_meetings/<?=$uid?>/<?=$sdate?>/<?=$edate?>"><?= $total_write_mom_rp_meetings ?></a>
                            </h5>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="frame-layer-1">
                    <div class="frame-layer-2">
                      <div class="frame-layer-3">
                        <div class="card text-center card-bg-16">
                          <div class="card-cotent-data">
                            <h4>Total Pending For Write MOM RP Meeting</h4>
                            <p>Pending Minutes of Meetings with Resource Persons.</p>
                            <h5>
                              <a target="_BLANK" class="card-count-heading" href="<?=base_url()?>Menu/MeetingsDatas/total_pending_for_write_mom_rp_meeting/<?=$uid?>/<?=$sdate?>/<?=$edate?>"><?= $total_pending_for_write_mom_rp_meeting ?></a>
                            </h5>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            </div>
              <hr>
            <!-- MOM Data Group -->
             <div class="taskfrequency" style="background: linear-gradient(to right, rgb(157 255 185 / 63%), rgba(86, 66, 12, 0.35)); box-shadow: 0 4px 8px rgba(0,0,0,0.1); padding: 20px;">
            <div class="card-group bgbackground">
              <div class="card-group-title">
                MOM Data <br>
                <small>This section provides detailed information about the Minutes of the Meeting (MOM) data, including summaries, action items, and decisions made during meetings.</small>
              </div>

              <div class="row">
                <div class="col-md-4">
                  <div class="frame-layer-1">
                    <div class="frame-layer-2">
                      <div class="frame-layer-3">
                        <div class="card text-center card-bg-1">
                          <div class="card-cotent-data">
                            <h4>Total MOM Data</h4>
                            <p>Total Minutes of Meetings documented.</p>
                            <h5>
                              <a target="_BLANK" class="card-count-heading" href="<?=base_url()?>Menu/MeetingsDatas/total_mom_data/<?=$uid?>/<?=$sdate?>/<?=$edate?>"><?= $total_mom_data ?></a>
                            </h5>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
                <div class="col-md-4">
                  <div class="frame-layer-1">
                    <div class="frame-layer-2">
                      <div class="frame-layer-3">
                        <div class="card text-center card-bg-2">
                          <div class="card-cotent-data">
                            <h4>Total Approved After Check MOM Data</h4>
                            <p>Approved Minutes of Meetings after check.</p>
                            <h5>
                              <a target="_BLANK" class="card-count-heading" href="<?=base_url()?>Menu/MeetingsDatas/total_approved_after_check_mom_data/<?=$uid?>/<?=$sdate?>/<?=$edate?>"><?= $total_approved_after_check_mom_data ?></a>
                            </h5>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
                <div class="col-md-4">
                  <div class="frame-layer-1">
                    <div class="frame-layer-2">
                      <div class="frame-layer-3">
                        <div class="card text-center card-bg-3">
                          <div class="card-cotent-data">
                            <h4>Total Reject After Check MOM Data</h4>
                            <p>Rejected Minutes of Meetings after check.</p>
                            <h5>
                              <a target="_BLANK" class="card-count-heading" href="<?=base_url()?>Menu/MeetingsDatas/total_reject_after_check_mom_data/<?=$uid?>/<?=$sdate?>/<?=$edate?>"><?= $total_reject_after_check_mom_data ?></a>
                            </h5>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            </div>
            <hr>

            <!-- Final MOM Data Group -->
              <div class="taskfrequency" style="background: linear-gradient(to right, rgb(255 157 251 / 63%), rgba(86, 66, 12, 0.35)); box-shadow: 0 4px 8px rgba(0,0,0,0.1); padding: 20px;">
            <div class="card-group bgbackground">
             <div class="card-group-title">
                Final MOM Data <br>
                <small>This section provides a summary of the Minutes of the Meeting (MOM) data, including key points discussed, decisions made, and action items assigned.</small>
              </div>

              <div class="row">
                <div class="col-md-6">
                  <div class="frame-layer-1">
                    <div class="frame-layer-2">
                      <div class="frame-layer-3">
                        <div class="card text-center card-bg-4">
                          <div class="card-cotent-data">
                            <h4>Total NO RP After Check MOM Data</h4>
                            <p>Minutes of Meetings without Resource Persons after check.</p>
                            <h5>
                              <a target="_BLANK" class="card-count-heading" href="<?=base_url()?>Menu/MeetingsDatas/total_NO_RP_after_check_mom_data/<?=$uid?>/<?=$sdate?>/<?=$edate?>"><?= $total_NO_RP_after_check_mom_data ?></a>
                            </h5>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="frame-layer-1">
                    <div class="frame-layer-2">
                      <div class="frame-layer-3">
                        <div class="card text-center card-bg-5">
                          <div class="card-cotent-data">
                            <h4>Total Pending For Check MOM Data</h4>
                            <p>Pending Minutes of Meetings for check.</p>
                            <h5>
                              <a target="_BLANK" class="card-count-heading" href="<?=base_url()?>Menu/MeetingsDatas/total_pending_for_check_mom_data/<?=$uid?>/<?=$sdate?>/<?=$edate?>"><?= $total_pending_for_check_mom_data ?></a>
                            </h5>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            </div>
          <hr>
         <!-- Scheduled Meetings Group -->
          <div class="taskfrequency" style="background: linear-gradient(to right, rgb(104 0 96 / 28%), #eebd3759); box-shadow: 0 4px 8px rgba(0,0,0,0.1); padding: 20px;">
            <div class="card-group bgbackground">
             <div class="card-group-title">Total Complete Meetings By Status <br>
              <small>This section provides an overview of the total number of meetings completed, categorized by their respective statuses.</small>
            </div>
              <div class="row">
                 <div class="col-md-12">
                  <div class="frame-layer-1">
                    <div class="frame-layer-2">
                      <div class="frame-layer-3" style="background: linear-gradient(145deg, #add984, #add984);">
                        <div class="card text-center card-bg-1">
                          <div class="card-cotent-data">
                            <h4>Total Complete Meetings</h4>
                            <p>Meetings that are completed.</p>
                            <h5>
                               <a target="_BLANK" class="card-count-heading" href="<?=base_url()?>Menu/MeetingsDatas/complete_meetings/<?=$uid?>/<?=$sdate?>/<?=$edate?>"><?= $complete_meetings ?></a>
                            </h5>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
                <div class="col-md-2">
                  <div class="frame-layer-1">
                    <div class="frame-layer-2">
                      <div class="frame-layer-3">
                        <div class="card text-center card-bg-2">
                          <div class="card-cotent-data">
                            <h4> Open	</h4>
                            <p>Complete Meeting.</p>
                            <h5>
                              <a target="_BLANK" class="card-count-heading" href="<?=base_url()?>Menu/MeetingsDatas/total_open/<?=$uid?>/<?=$sdate?>/<?=$edate?>"><?= $total_open ?></a>
                            </h5>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
                <div class="col-md-2">
                  <div class="frame-layer-1">
                    <div class="frame-layer-2">
                      <div class="frame-layer-3">
                        <div class="card text-center card-bg-3">
                          <div class="card-cotent-data">
                            <h4> Reachout</h4>
                            <p>Complete Meeting.</p>
                            <h5>
                              <a target="_BLANK" class="card-count-heading" href="<?=base_url()?>Menu/MeetingsDatas/total_reachout/<?=$uid?>/<?=$sdate?>/<?=$edate?>"><?= $total_reachout ?></a>
                            </h5>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
                <div class="col-md-2">
                  <div class="frame-layer-1">
                    <div class="frame-layer-2">
                      <div class="frame-layer-3">
                        <div class="card text-center card-bg-4">
                          <div class="card-cotent-data">
                            <h4> Tentative </h4>
                            <p>Complete Meeting.</p>
                            <h5>
                              <a target="_BLANK" class="card-count-heading" href="<?=base_url()?>Menu/MeetingsDatas/total_tentative/<?=$uid?>/<?=$sdate?>/<?=$edate?>"><?= $total_tentative ?></a>
                            </h5>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
                <div class="col-md-2">
                  <div class="frame-layer-1">
                    <div class="frame-layer-2">
                      <div class="frame-layer-3">
                        <div class="card text-center card-bg-5">
                          <div class="card-cotent-data">
                            <h4>Will do later</h4>
                            <p>Complete Meeting.</p>
                            <h5>
                              <a target="_BLANK" class="card-count-heading" href="<?=base_url()?>Menu/MeetingsDatas/total_will_do_later/<?=$uid?>/<?=$sdate?>/<?=$edate?>"><?= $total_will_do_later ?></a>
                            </h5>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>


                <div class="col-md-2">
                  <div class="frame-layer-1">
                    <div class="frame-layer-2">
                      <div class="frame-layer-3">
                        <div class="card text-center card-bg-6">
                          <div class="card-cotent-data">
                            <h4>Not Interested</h4>
                            <p>Complete Meeting.</p>
                            <h5>
                              <a target="_BLANK" class="card-count-heading" href="<?=base_url()?>Menu/MeetingsDatas/total_not_interested/<?=$uid?>/<?=$sdate?>/<?=$edate?>"><?= $total_not_interested ?></a>
                            </h5>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
                <div class="col-md-2">
                  <div class="frame-layer-1">
                    <div class="frame-layer-2">
                      <div class="frame-layer-3">
                        <div class="card text-center card-bg-7">
                          <div class="card-cotent-data">
                            <h4>Positive</h4>
                            <p>Complete Meeting.</p>
                            <h5>
                              <a target="_BLANK" class="card-count-heading" href="<?=base_url()?>Menu/MeetingsDatas/total_positive/<?=$uid?>/<?=$sdate?>/<?=$edate?>"><?= $total_positive ?></a>
                            </h5>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
                <div class="col-md-2">
                  <div class="frame-layer-1">
                    <div class="frame-layer-2">
                      <div class="frame-layer-3">
                        <div class="card text-center card-bg-8">
                          <div class="card-cotent-data">
                            <h4>Closure</h4>
                            <p>Complete Meeting.</p>
                            <h5>
                              <a target="_BLANK" class="card-count-heading" href="<?=base_url()?>Menu/MeetingsDatas/total_closure/<?=$uid?>/<?=$sdate?>/<?=$edate?>"><?= $total_closure ?></a>
                            </h5>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
                <div class="col-md-2">
                  <div class="frame-layer-1">
                    <div class="frame-layer-2">
                      <div class="frame-layer-3">
                        <div class="card text-center card-bg-9">
                          <div class="card-cotent-data">
                            <h4>Open RPEM</h4>
                            <p>Complete Meeting.</p>
                            <h5>
                              <a target="_BLANK" class="card-count-heading" href="<?=base_url()?>Menu/MeetingsDatas/total_open_rpem/<?=$uid?>/<?=$sdate?>/<?=$edate?>"><?= $total_open_rpem ?></a>
                            </h5>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
                <div class="col-md-2">
                  <div class="frame-layer-1">
                    <div class="frame-layer-2">
                      <div class="frame-layer-3">
                        <div class="card text-center card-bg-10">
                          <div class="card-cotent-data">
                            <h4>Very Positive</h4>
                            <p>Complete Meeting.</p>
                            <h5>
                              <a target="_BLANK" class="card-count-heading" href="<?=base_url()?>Menu/MeetingsDatas/total_very_positive/<?=$uid?>/<?=$sdate?>/<?=$edate?>"><?= $total_very_positive ?></a>
                            </h5>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
                <div class="col-md-2">
                  <div class="frame-layer-1">
                    <div class="frame-layer-2">
                      <div class="frame-layer-3">
                        <div class="card text-center card-bg-11">
                          <div class="card-cotent-data">
                            <h4>ttd Reachout</h4>
                            <p>Complete Meeting.</p>
                            <h5>
                              <a target="_BLANK" class="card-count-heading" href="<?=base_url()?>Menu/MeetingsDatas/total_ttd_reachout/<?=$uid?>/<?=$sdate?>/<?=$edate?>"><?= $total_ttd_reachout ?></a>
                            </h5>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
                <div class="col-md-2">
                  <div class="frame-layer-1">
                    <div class="frame-layer-2">
                      <div class="frame-layer-3">
                        <div class="card text-center card-bg-12">
                          <div class="card-cotent-data">
                            <h4>WNO Reachout</h4>
                            <p>Complete Meeting.</p>
                            <h5>
                              <a target="_BLANK" class="card-count-heading" href="<?=base_url()?>Menu/MeetingsDatas/total_wno_reachout/<?=$uid?>/<?=$sdate?>/<?=$edate?>"><?= $total_wno_reachout ?></a>
                            </h5>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
                <div class="col-md-2">
                  <div class="frame-layer-1">
                    <div class="frame-layer-2">
                      <div class="frame-layer-3">
                        <div class="card text-center card-bg-13">
                          <div class="card-cotent-data">
                            <h4>Positive Nap</h4>
                            <p>Complete Meeting.</p>
                            <h5>
                              <a target="_BLANK" class="card-count-heading" href="<?=base_url()?>Menu/MeetingsDatas/total_positive_nap/<?=$uid?>/<?=$sdate?>/<?=$edate?>"><?= $total_positive_nap ?></a>
                            </h5>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
                <div class="col-md-2">
                  <div class="frame-layer-1">
                    <div class="frame-layer-2">
                      <div class="frame-layer-3">
                        <div class="card text-center card-bg-14">
                          <div class="card-cotent-data">
                            <h4>Very Positive Nap</h4>
                            <p>Complete Meeting.</p>
                            <h5>
                              <a target="_BLANK" class="card-count-heading" href="<?=base_url()?>Menu/MeetingsDatas/total_very_positive_nap/<?=$uid?>/<?=$sdate?>/<?=$edate?>"><?= $total_very_positive_nap ?></a>
                            </h5>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
                <div class="col-md-2">
                  <div class="frame-layer-1">
                    <div class="frame-layer-2">
                      <div class="frame-layer-3">
                        <div class="card text-center card-bg-15">
                          <div class="card-cotent-data">
                            <h4>On Boarded</h4>
                            <p>Complete Meeting.</p>
                            <h5>
                              <a target="_BLANK" class="card-count-heading" href="<?=base_url()?>Menu/MeetingsDatas/total_on_boarded/<?=$uid?>/<?=$sdate?>/<?=$edate?>"><?= $total_on_boarded ?></a>
                            </h5>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            </div>
            <hr>





             <hr>
         <!-- Scheduled Meetings Group -->
           <div class="taskfrequency" style="background: linear-gradient(to right, #b2deff00, #89ee375e); box-shadow: 0 4px 8px rgba(0,0,0,0.1); padding: 20px;">
            <div class="card-group bgbackground">
             <div class="card-group-title">Total Complete RP Meetings By Status <br>
<small>This section provides an overview of the total Resource Planning (RP) meetings completed, categorized by their respective statuses.</small>
</div>

              <div class="row">
                 <div class="col-md-12">
                  <div class="frame-layer-1">
                    <div class="frame-layer-2">
                      <div class="frame-layer-3">
                        <div class="card text-center card-bg-10">
                          <div class="card-cotent-data">
                            <h4>Total RP Meetings</h4>
                            <p>Meetings with Resource Persons.</p>
                            <h5>
                              <a target="_BLANK" class="card-count-heading" href="<?=base_url()?>Menu/MeetingsDatas/total_RP_meetings/<?=$uid?>/<?=$sdate?>/<?=$edate?>"><?= $total_RP_meetings ?></a>
                            </h5>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
                <div class="col-md-2">
                  <div class="frame-layer-1">
                    <div class="frame-layer-2">
                      <div class="frame-layer-3">
                        <div class="card text-center card-bg-2">
                          <div class="card-cotent-data">
                            <h4> Open	</h4>
                            <p>Complete Meeting.</p>
                            <h5>
                              <a target="_BLANK" class="card-count-heading" href="<?=base_url()?>Menu/MeetingsDatas/total_RP_in_open/<?=$uid?>/<?=$sdate?>/<?=$edate?>"><?= $total_RP_in_open ?></a>
                            </h5>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
                <div class="col-md-2">
                  <div class="frame-layer-1">
                    <div class="frame-layer-2">
                      <div class="frame-layer-3">
                        <div class="card text-center card-bg-3">
                          <div class="card-cotent-data">
                            <h4> Reachout</h4>
                            <p>Complete Meeting.</p>
                            <h5>
                              <a target="_BLANK" class="card-count-heading" href="<?=base_url()?>Menu/MeetingsDatas/total_RP_in_reachout/<?=$uid?>/<?=$sdate?>/<?=$edate?>"><?= $total_RP_in_reachout ?></a>
                            </h5>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
                <div class="col-md-2">
                  <div class="frame-layer-1">
                    <div class="frame-layer-2">
                      <div class="frame-layer-3">
                        <div class="card text-center card-bg-4">
                          <div class="card-cotent-data">
                            <h4> Tentative </h4>
                            <p>Complete Meeting.</p>
                            <h5>
                              <a target="_BLANK" class="card-count-heading" href="<?=base_url()?>Menu/MeetingsDatas/total_RP_in_tentative/<?=$uid?>/<?=$sdate?>/<?=$edate?>"><?= $total_RP_in_tentative ?></a>
                            </h5>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
                <div class="col-md-2">
                  <div class="frame-layer-1">
                    <div class="frame-layer-2">
                      <div class="frame-layer-3">
                        <div class="card text-center card-bg-5">
                          <div class="card-cotent-data">
                            <h4>Will do later</h4>
                            <p>Complete Meeting.</p>
                            <h5>
                              <a target="_BLANK" class="card-count-heading" href="<?=base_url()?>Menu/MeetingsDatas/total_RP_in_will_do_later/<?=$uid?>/<?=$sdate?>/<?=$edate?>"><?= $total_RP_in_will_do_later ?></a>
                            </h5>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>


                <div class="col-md-2">
                  <div class="frame-layer-1">
                    <div class="frame-layer-2">
                      <div class="frame-layer-3">
                        <div class="card text-center card-bg-6">
                          <div class="card-cotent-data">
                            <h4>Not Interested</h4>
                            <p>Complete Meeting.</p>
                            <h5>
                              <a target="_BLANK" class="card-count-heading" href="<?=base_url()?>Menu/MeetingsDatas/total_RP_in_not_interested/<?=$uid?>/<?=$sdate?>/<?=$edate?>"><?= $total_RP_in_not_interested ?></a>
                            </h5>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
                <div class="col-md-2">
                  <div class="frame-layer-1">
                    <div class="frame-layer-2">
                      <div class="frame-layer-3">
                        <div class="card text-center card-bg-7">
                          <div class="card-cotent-data">
                            <h4>Positive</h4>
                            <p>Complete Meeting.</p>
                            <h5>
                              <a target="_BLANK" class="card-count-heading" href="<?=base_url()?>Menu/MeetingsDatas/total_RP_in_positive/<?=$uid?>/<?=$sdate?>/<?=$edate?>"><?= $total_RP_in_positive ?></a>
                            </h5>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
                <div class="col-md-2">
                  <div class="frame-layer-1">
                    <div class="frame-layer-2">
                      <div class="frame-layer-3">
                        <div class="card text-center card-bg-8">
                          <div class="card-cotent-data">
                            <h4>Closure</h4>
                            <p>Complete Meeting.</p>
                            <h5>
                              <a target="_BLANK" class="card-count-heading" href="<?=base_url()?>Menu/MeetingsDatas/total_RP_in_closure/<?=$uid?>/<?=$sdate?>/<?=$edate?>"><?= $total_RP_in_closure ?></a>
                            </h5>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
                <div class="col-md-2">
                  <div class="frame-layer-1">
                    <div class="frame-layer-2">
                      <div class="frame-layer-3">
                        <div class="card text-center card-bg-9">
                          <div class="card-cotent-data">
                            <h4>Open RPEM</h4>
                            <p>Complete Meeting.</p>
                            <h5>
                              <a target="_BLANK" class="card-count-heading" href="<?=base_url()?>Menu/MeetingsDatas/total_RP_in_open_rpem/<?=$uid?>/<?=$sdate?>/<?=$edate?>"><?= $total_RP_in_open_rpem ?></a>
                            </h5>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
                <div class="col-md-2">
                  <div class="frame-layer-1">
                    <div class="frame-layer-2">
                      <div class="frame-layer-3">
                        <div class="card text-center card-bg-10">
                          <div class="card-cotent-data">
                            <h4>Very Positive</h4>
                            <p>Complete Meeting.</p>
                            <h5>
                              <a target="_BLANK" class="card-count-heading" href="<?=base_url()?>Menu/MeetingsDatas/total_RP_in_very_positive/<?=$uid?>/<?=$sdate?>/<?=$edate?>"><?= $total_RP_in_very_positive ?></a>
                            </h5>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
                <div class="col-md-2">
                  <div class="frame-layer-1">
                    <div class="frame-layer-2">
                      <div class="frame-layer-3">
                        <div class="card text-center card-bg-11">
                          <div class="card-cotent-data">
                            <h4>ttd Reachout</h4>
                            <p>Complete Meeting.</p>
                            <h5>
                              <a target="_BLANK" class="card-count-heading" href="<?=base_url()?>Menu/MeetingsDatas/total_RP_in_ttd_reachout/<?=$uid?>/<?=$sdate?>/<?=$edate?>"><?= $total_RP_in_ttd_reachout ?></a>
                            </h5>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
                <div class="col-md-2">
                  <div class="frame-layer-1">
                    <div class="frame-layer-2">
                      <div class="frame-layer-3">
                        <div class="card text-center card-bg-12">
                          <div class="card-cotent-data">
                            <h4>WNO Reachout</h4>
                            <p>Complete Meeting.</p>
                            <h5>
                              <a target="_BLANK" class="card-count-heading" href="<?=base_url()?>Menu/MeetingsDatas/total_RP_in_wno_reachout/<?=$uid?>/<?=$sdate?>/<?=$edate?>"><?= $total_RP_in_wno_reachout ?></a>
                            </h5>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
                <div class="col-md-2">
                  <div class="frame-layer-1">
                    <div class="frame-layer-2">
                      <div class="frame-layer-3">
                        <div class="card text-center card-bg-13">
                          <div class="card-cotent-data">
                            <h4>Positive Nap</h4>
                            <p>Complete Meeting.</p>
                            <h5>
                              <a target="_BLANK" class="card-count-heading" href="<?=base_url()?>Menu/MeetingsDatas/total_RP_in_positive_nap/<?=$uid?>/<?=$sdate?>/<?=$edate?>"><?= $total_RP_in_positive_nap ?></a>
                            </h5>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
                <div class="col-md-2">
                  <div class="frame-layer-1">
                    <div class="frame-layer-2">
                      <div class="frame-layer-3">
                        <div class="card text-center card-bg-14">
                          <div class="card-cotent-data">
                            <h4>Very Positive Nap</h4>
                            <p>Complete Meeting.</p>
                            <h5>
                              <a target="_BLANK" class="card-count-heading" href="<?=base_url()?>Menu/MeetingsDatas/total_RP_in_very_positive_nap/<?=$uid?>/<?=$sdate?>/<?=$edate?>"><?= $total_RP_in_very_positive_nap ?></a>
                            </h5>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
                <div class="col-md-2">
                  <div class="frame-layer-1">
                    <div class="frame-layer-2">
                      <div class="frame-layer-3">
                        <div class="card text-center card-bg-15">
                          <div class="card-cotent-data">
                            <h4>On Boarded</h4>
                            <p>Complete Meeting.</p>
                            <h5>
                              <a target="_BLANK" class="card-count-heading" href="<?=base_url()?>Menu/MeetingsDatas/total_RP_in_on_boarded/<?=$uid?>/<?=$sdate?>/<?=$edate?>"><?= $total_RP_in_on_boarded ?></a>
                            </h5>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            </div>
            <hr>



             <hr>
         <!-- Scheduled Meetings Group -->
           <div class="taskfrequency" style="background: linear-gradient(to right, #b2deff00, #ee37379e); box-shadow: 0 4px 8px rgba(0,0,0,0.1); padding: 20px;">
            <div class="card-group bgbackground">
            <div class="card-group-title">
                  Total Complete NO RP Meetings By Status <br>
                  <small>This section provides an overview of the total number of completed Non-Resource Planning (NO RP) meetings categorized by their status. It helps in analyzing the distribution and outcomes of these meetings.</small>
                </div>

              <div class="row">
                   <div class="col-md-12">
                  <div class="frame-layer-1">
                    <div class="frame-layer-2">
                      <div class="frame-layer-3">
                        <div class="card text-center card-bg-13">
                          <div class="card-cotent-data">
                            <h4>Total NO RP Meetings</h4>
                            <p>Meetings without Resource Persons.</p>
                            <h5>
                              <a target="_BLANK" class="card-count-heading" href="<?=base_url()?>Menu/MeetingsDatas/total_NO_RP_meetings/<?=$uid?>/<?=$sdate?>/<?=$edate?>"><?= $total_NO_RP_meetings ?></a>
                            </h5>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
                <div class="col-md-2">
                  <div class="frame-layer-1">
                    <div class="frame-layer-2">
                      <div class="frame-layer-3">
                        <div class="card text-center card-bg-2">
                          <div class="card-cotent-data">
                            <h4> Open	</h4>
                            <p>Complete Meeting.</p>
                            <h5>
                              <a target="_BLANK" class="card-count-heading" href="<?=base_url()?>Menu/MeetingsDatas/total_NO_RP_in_open/<?=$uid?>/<?=$sdate?>/<?=$edate?>"><?= $total_NO_RP_in_open ?></a>
                            </h5>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
                <div class="col-md-2">
                  <div class="frame-layer-1">
                    <div class="frame-layer-2">
                      <div class="frame-layer-3">
                        <div class="card text-center card-bg-3">
                          <div class="card-cotent-data">
                            <h4> Reachout</h4>
                            <p>Complete Meeting.</p>
                            <h5>
                              <a target="_BLANK" class="card-count-heading" href="<?=base_url()?>Menu/MeetingsDatas/total_NO_RP_in_reachout/<?=$uid?>/<?=$sdate?>/<?=$edate?>"><?= $total_NO_RP_in_reachout ?></a>
                            </h5>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
                <div class="col-md-2">
                  <div class="frame-layer-1">
                    <div class="frame-layer-2">
                      <div class="frame-layer-3">
                        <div class="card text-center card-bg-4">
                          <div class="card-cotent-data">
                            <h4> Tentative </h4>
                            <p>Complete Meeting.</p>
                            <h5>
                              <a target="_BLANK" class="card-count-heading" href="<?=base_url()?>Menu/MeetingsDatas/total_NO_RP_in_tentative/<?=$uid?>/<?=$sdate?>/<?=$edate?>"><?= $total_NO_RP_in_tentative ?></a>
                            </h5>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
                <div class="col-md-2">
                  <div class="frame-layer-1">
                    <div class="frame-layer-2">
                      <div class="frame-layer-3">
                        <div class="card text-center card-bg-5">
                          <div class="card-cotent-data">
                            <h4>Will do later</h4>
                            <p>Complete Meeting.</p>
                            <h5>
                              <a target="_BLANK" class="card-count-heading" href="<?=base_url()?>Menu/MeetingsDatas/total_NO_RP_in_will_do_later/<?=$uid?>/<?=$sdate?>/<?=$edate?>"><?= $total_NO_RP_in_will_do_later ?></a>
                            </h5>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>


                <div class="col-md-2">
                  <div class="frame-layer-1">
                    <div class="frame-layer-2">
                      <div class="frame-layer-3">
                        <div class="card text-center card-bg-6">
                          <div class="card-cotent-data">
                            <h4>Not Interested</h4>
                            <p>Complete Meeting.</p>
                            <h5>
                              <a target="_BLANK" class="card-count-heading" href="<?=base_url()?>Menu/MeetingsDatas/total_NO_RP_in_not_interested/<?=$uid?>/<?=$sdate?>/<?=$edate?>"><?= $total_NO_RP_in_not_interested ?></a>
                            </h5>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
                <div class="col-md-2">
                  <div class="frame-layer-1">
                    <div class="frame-layer-2">
                      <div class="frame-layer-3">
                        <div class="card text-center card-bg-7">
                          <div class="card-cotent-data">
                            <h4>Positive</h4>
                            <p>Complete Meeting.</p>
                            <h5>
                              <a target="_BLANK" class="card-count-heading" href="<?=base_url()?>Menu/MeetingsDatas/total_NO_RP_in_positive/<?=$uid?>/<?=$sdate?>/<?=$edate?>"><?= $total_NO_RP_in_positive ?></a>
                            </h5>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
                <div class="col-md-2">
                  <div class="frame-layer-1">
                    <div class="frame-layer-2">
                      <div class="frame-layer-3">
                        <div class="card text-center card-bg-8">
                          <div class="card-cotent-data">
                            <h4>Closure</h4>
                            <p>Complete Meeting.</p>
                            <h5>
                              <a target="_BLANK" class="card-count-heading" href="<?=base_url()?>Menu/MeetingsDatas/total_NO_RP_in_closure/<?=$uid?>/<?=$sdate?>/<?=$edate?>"><?= $total_NO_RP_in_closure ?></a>
                            </h5>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
                <div class="col-md-2">
                  <div class="frame-layer-1">
                    <div class="frame-layer-2">
                      <div class="frame-layer-3">
                        <div class="card text-center card-bg-9">
                          <div class="card-cotent-data">
                            <h4>Open RPEM</h4>
                            <p>Complete Meeting.</p>
                            <h5>
                              <a target="_BLANK" class="card-count-heading" href="<?=base_url()?>Menu/MeetingsDatas/total_NO_RP_in_open_rpem/<?=$uid?>/<?=$sdate?>/<?=$edate?>"><?= $total_NO_RP_in_open_rpem ?></a>
                            </h5>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
                <div class="col-md-2">
                  <div class="frame-layer-1">
                    <div class="frame-layer-2">
                      <div class="frame-layer-3">
                        <div class="card text-center card-bg-10">
                          <div class="card-cotent-data">
                            <h4>Very Positive</h4>
                            <p>Complete Meeting.</p>
                            <h5>
                              <a target="_BLANK" class="card-count-heading" href="<?=base_url()?>Menu/MeetingsDatas/total_NO_RP_in_very_positive/<?=$uid?>/<?=$sdate?>/<?=$edate?>"><?= $total_NO_RP_in_very_positive ?></a>
                            </h5>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
                <div class="col-md-2">
                  <div class="frame-layer-1">
                    <div class="frame-layer-2">
                      <div class="frame-layer-3">
                        <div class="card text-center card-bg-11">
                          <div class="card-cotent-data">
                            <h4>ttd Reachout</h4>
                            <p>Complete Meeting.</p>
                            <h5>
                              <a target="_BLANK" class="card-count-heading" href="<?=base_url()?>Menu/MeetingsDatas/total_NO_RP_in_ttd_reachout/<?=$uid?>/<?=$sdate?>/<?=$edate?>"><?= $total_NO_RP_in_ttd_reachout ?></a>
                            </h5>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
                <div class="col-md-2">
                  <div class="frame-layer-1">
                    <div class="frame-layer-2">
                      <div class="frame-layer-3">
                        <div class="card text-center card-bg-12">
                          <div class="card-cotent-data">
                            <h4>WNO Reachout</h4>
                            <p>Complete Meeting.</p>
                            <h5>
                              <a target="_BLANK" class="card-count-heading" href="<?=base_url()?>Menu/MeetingsDatas/total_NO_RP_in_wno_reachout/<?=$uid?>/<?=$sdate?>/<?=$edate?>"><?= $total_NO_RP_in_wno_reachout ?></a>
                            </h5>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
                <div class="col-md-2">
                  <div class="frame-layer-1">
                    <div class="frame-layer-2">
                      <div class="frame-layer-3">
                        <div class="card text-center card-bg-13">
                          <div class="card-cotent-data">
                            <h4>Positive Nap</h4>
                            <p>Complete Meeting.</p>
                            <h5>
                              <a target="_BLANK" class="card-count-heading" href="<?=base_url()?>Menu/MeetingsDatas/total_NO_RP_in_positive_nap/<?=$uid?>/<?=$sdate?>/<?=$edate?>"><?= $total_NO_RP_in_positive_nap ?></a>
                            </h5>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
                <div class="col-md-2">
                  <div class="frame-layer-1">
                    <div class="frame-layer-2">
                      <div class="frame-layer-3">
                        <div class="card text-center card-bg-14">
                          <div class="card-cotent-data">
                            <h4>Very Positive Nap</h4>
                            <p>Complete Meeting.</p>
                            <h5>
                              <a target="_BLANK" class="card-count-heading" href="<?=base_url()?>Menu/MeetingsDatas/total_NO_RP_in_very_positive_nap/<?=$uid?>/<?=$sdate?>/<?=$edate?>"><?= $total_NO_RP_in_very_positive_nap ?></a>
                            </h5>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
                <div class="col-md-2">
                  <div class="frame-layer-1">
                    <div class="frame-layer-2">
                      <div class="frame-layer-3">
                        <div class="card text-center card-bg-15">
                          <div class="card-cotent-data">
                            <h4>On Boarded</h4>
                            <p>Complete Meeting.</p>
                            <h5>
                              <a target="_BLANK" class="card-count-heading" href="<?=base_url()?>Menu/MeetingsDatas/total_NO_RP_in_on_boarded/<?=$uid?>/<?=$sdate?>/<?=$edate?>"><?= $total_NO_RP_in_on_boarded ?></a>
                            </h5>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            </div>
            <hr>
            <hr>
             <div class="taskfrequency" style="background: linear-gradient(to right, #b2f9ff6e, #ff81c19e); box-shadow: 0 4px 8px rgba(0,0,0,0.1); padding: 20px;">
         <!-- Scheduled Meetings Group -->
            <div class="card-group bgbackground">
              <div class="card-group-title">
                Total Complete Only Got Detail Meetings By Status <br>
                <small>This section provides an overview of meetings where only details were gathered, categorized by their pallned status. It helps in tracking the progress and outcomes of informational meetings.</small>
              </div>

              <div class="row">
                  <div class="col-md-12">
                  <div class="frame-layer-1">
                    <div class="frame-layer-2">
                      <div class="frame-layer-3">
                        <div class="card text-center card-bg-14">
                          <div class="card-cotent-data">
                            <h4>Total Only Got Details Meetings</h4>
                            <p>Meetings with only details obtained.</p>
                            <h5>
                              <a target="_BLANK" class="card-count-heading" href="<?=base_url()?>Menu/MeetingsDatas/total_Only_Got_details_meetings/<?=$uid?>/<?=$sdate?>/<?=$edate?>"><?= $total_Only_Got_details_meetings ?></a>
                            </h5>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
                <div class="col-md-2">
                  <div class="frame-layer-1">
                    <div class="frame-layer-2">
                      <div class="frame-layer-3">
                        <div class="card text-center card-bg-2">
                          <div class="card-cotent-data">
                            <h4> Open	</h4>
                            <p>Complete Meeting.</p>
                            <h5>
                              <a target="_BLANK" class="card-count-heading" href="<?=base_url()?>Menu/MeetingsDatas/total_OGD_in_open/<?=$uid?>/<?=$sdate?>/<?=$edate?>"><?= $total_OGD_in_open ?></a>
                            </h5>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
                <div class="col-md-2">
                  <div class="frame-layer-1">
                    <div class="frame-layer-2">
                      <div class="frame-layer-3">
                        <div class="card text-center card-bg-3">
                          <div class="card-cotent-data">
                            <h4> Reachout</h4>
                            <p>Complete Meeting.</p>
                            <h5>
                              <a target="_BLANK" class="card-count-heading" href="<?=base_url()?>Menu/MeetingsDatas/total_OGD_in_reachout/<?=$uid?>/<?=$sdate?>/<?=$edate?>"><?= $total_OGD_in_reachout ?></a>
                            </h5>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
                <div class="col-md-2">
                  <div class="frame-layer-1">
                    <div class="frame-layer-2">
                      <div class="frame-layer-3">
                        <div class="card text-center card-bg-4">
                          <div class="card-cotent-data">
                            <h4> Tentative </h4>
                            <p>Complete Meeting.</p>
                            <h5>
                              <a target="_BLANK" class="card-count-heading" href="<?=base_url()?>Menu/MeetingsDatas/total_OGD_in_tentative/<?=$uid?>/<?=$sdate?>/<?=$edate?>"><?= $total_OGD_in_tentative ?></a>
                            </h5>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
                <div class="col-md-2">
                  <div class="frame-layer-1">
                    <div class="frame-layer-2">
                      <div class="frame-layer-3">
                        <div class="card text-center card-bg-5">
                          <div class="card-cotent-data">
                            <h4>Will do later</h4>
                            <p>Complete Meeting.</p>
                            <h5>
                              <a target="_BLANK" class="card-count-heading" href="<?=base_url()?>Menu/MeetingsDatas/total_OGD_in_will_do_later/<?=$uid?>/<?=$sdate?>/<?=$edate?>"><?= $total_OGD_in_will_do_later ?></a>
                            </h5>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>


                <div class="col-md-2">
                  <div class="frame-layer-1">
                    <div class="frame-layer-2">
                      <div class="frame-layer-3">
                        <div class="card text-center card-bg-6">
                          <div class="card-cotent-data">
                            <h4>Not Interested</h4>
                            <p>Complete Meeting.</p>
                            <h5>
                              <a target="_BLANK" class="card-count-heading" href="<?=base_url()?>Menu/MeetingsDatas/total_OGD_in_not_interested/<?=$uid?>/<?=$sdate?>/<?=$edate?>"><?= $total_OGD_in_not_interested ?></a>
                            </h5>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
                <div class="col-md-2">
                  <div class="frame-layer-1">
                    <div class="frame-layer-2">
                      <div class="frame-layer-3">
                        <div class="card text-center card-bg-7">
                          <div class="card-cotent-data">
                            <h4>Positive</h4>
                            <p>Complete Meeting.</p>
                            <h5>
                              <a target="_BLANK" class="card-count-heading" href="<?=base_url()?>Menu/MeetingsDatas/total_OGD_in_positive/<?=$uid?>/<?=$sdate?>/<?=$edate?>"><?= $total_OGD_in_positive ?></a>
                            </h5>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
                <div class="col-md-2">
                  <div class="frame-layer-1">
                    <div class="frame-layer-2">
                      <div class="frame-layer-3">
                        <div class="card text-center card-bg-8">
                          <div class="card-cotent-data">
                            <h4>Closure</h4>
                            <p>Complete Meeting.</p>
                            <h5>
                              <a target="_BLANK" class="card-count-heading" href="<?=base_url()?>Menu/MeetingsDatas/total_OGD_in_closure/<?=$uid?>/<?=$sdate?>/<?=$edate?>"><?= $total_OGD_in_closure ?></a>
                            </h5>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
                <div class="col-md-2">
                  <div class="frame-layer-1">
                    <div class="frame-layer-2">
                      <div class="frame-layer-3">
                        <div class="card text-center card-bg-9">
                          <div class="card-cotent-data">
                            <h4>Open RPEM</h4>
                            <p>Complete Meeting.</p>
                            <h5>
                              <a target="_BLANK" class="card-count-heading" href="<?=base_url()?>Menu/MeetingsDatas/total_OGD_in_open_rpem/<?=$uid?>/<?=$sdate?>/<?=$edate?>"><?= $total_OGD_in_open_rpem ?></a>
                            </h5>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
                <div class="col-md-2">
                  <div class="frame-layer-1">
                    <div class="frame-layer-2">
                      <div class="frame-layer-3">
                        <div class="card text-center card-bg-10">
                          <div class="card-cotent-data">
                            <h4>Very Positive</h4>
                            <p>Complete Meeting.</p>
                            <h5>
                              <a target="_BLANK" class="card-count-heading" href="<?=base_url()?>Menu/MeetingsDatas/total_OGD_in_very_positive/<?=$uid?>/<?=$sdate?>/<?=$edate?>"><?= $total_OGD_in_very_positive ?></a>
                            </h5>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
                <div class="col-md-2">
                  <div class="frame-layer-1">
                    <div class="frame-layer-2">
                      <div class="frame-layer-3">
                        <div class="card text-center card-bg-11">
                          <div class="card-cotent-data">
                            <h4>ttd Reachout</h4>
                            <p>Complete Meeting.</p>
                            <h5>
                              <a target="_BLANK" class="card-count-heading" href="<?=base_url()?>Menu/MeetingsDatas/total_OGD_in_ttd_reachout/<?=$uid?>/<?=$sdate?>/<?=$edate?>"><?= $total_OGD_in_ttd_reachout ?></a>
                            </h5>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
                <div class="col-md-2">
                  <div class="frame-layer-1">
                    <div class="frame-layer-2">
                      <div class="frame-layer-3">
                        <div class="card text-center card-bg-12">
                          <div class="card-cotent-data">
                            <h4>WNO Reachout</h4>
                            <p>Complete Meeting.</p>
                            <h5>
                              <a target="_BLANK" class="card-count-heading" href="<?=base_url()?>Menu/MeetingsDatas/total_OGD_in_wno_reachout/<?=$uid?>/<?=$sdate?>/<?=$edate?>"><?= $total_OGD_in_wno_reachout ?></a>
                            </h5>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
                <div class="col-md-2">
                  <div class="frame-layer-1">
                    <div class="frame-layer-2">
                      <div class="frame-layer-3">
                        <div class="card text-center card-bg-13">
                          <div class="card-cotent-data">
                            <h4>Positive Nap</h4>
                            <p>Complete Meeting.</p>
                            <h5>
                              <a target="_BLANK" class="card-count-heading" href="<?=base_url()?>Menu/MeetingsDatas/total_OGD_in_positive_nap/<?=$uid?>/<?=$sdate?>/<?=$edate?>"><?= $total_OGD_in_positive_nap ?></a>
                            </h5>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
                <div class="col-md-2">
                  <div class="frame-layer-1">
                    <div class="frame-layer-2">
                      <div class="frame-layer-3">
                        <div class="card text-center card-bg-14">
                          <div class="card-cotent-data">
                            <h4>Very Positive Nap</h4>
                            <p>Complete Meeting.</p>
                            <h5>
                              <a target="_BLANK" class="card-count-heading" href="<?=base_url()?>Menu/MeetingsDatas/total_OGD_in_very_positive_nap/<?=$uid?>/<?=$sdate?>/<?=$edate?>"><?= $total_OGD_in_very_positive_nap ?></a>
                            </h5>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
                <div class="col-md-2">
                  <div class="frame-layer-1">
                    <div class="frame-layer-2">
                      <div class="frame-layer-3">
                        <div class="card text-center card-bg-15">
                          <div class="card-cotent-data">
                            <h4>On Boarded</h4>
                            <p>Complete Meeting.</p>
                            <h5>
                              <a target="_BLANK" class="card-count-heading" href="<?=base_url()?>Menu/MeetingsDatas/total_OGD_in_on_boarded/<?=$uid?>/<?=$sdate?>/<?=$edate?>"><?= $total_OGD_in_on_boarded ?></a>
                            </h5>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            </div>
            <hr>





            <hr>
             <div class="taskfrequency" style="background: linear-gradient(to right, #ffb2b2, #81f7ff40); box-shadow: 0 4px 8px rgba(0,0,0,0.1); padding: 20px;">
         <!-- Scheduled Meetings Group -->
            <div class="card-group bgbackground">
             <div class="card-group-title">
                Total Complete New Company Meetings Between Date <br>
                <small>This section displays the total number of new company meetings that have been completed within a specified date range. It helps in tracking and analyzing the frequency of introductory meetings with new companies.</small>
              </div>

              <div class="row">
                 <div class="col-md-12">
                  <div class="frame-layer-1">
                    <div class="frame-layer-2">
                      <div class="frame-layer-3" style="background: linear-gradient(145deg, #add984, #add984);">
                        <div class="card text-center card-bg-1">
                          <div class="card-cotent-data">
                            <h4>Total Complete Meetings</h4>
                            <p>Meetings that are completed.</p>
                            <h5>
                               <a target="_BLANK" class="card-count-heading" href="<?=base_url()?>Menu/MeetingsDatas/complete_meetings/<?=$uid?>/<?=$sdate?>/<?=$edate?>"><?= $complete_meetings ?></a>
                            </h5>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
                <div class="col-md-3">
                  <div class="frame-layer-1">
                    <div class="frame-layer-2">
                      <div class="frame-layer-3">
                        <div class="card text-center card-bg-7">
                          <div class="card-cotent-data">
                            <h4>Total New Company Meeting Complete	</h4>
                            <p>Complete Meeting.</p>
                            <h5>
                              <a target="_BLANK" class="card-count-heading" href="<?=base_url()?>Menu/MeetingsDatas/total_new_company_complete/<?=$uid?>/<?=$sdate?>/<?=$edate?>"><?= $total_new_company_complete ?></a>
                            </h5>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
                <div class="col-md-3">
                  <div class="frame-layer-1">
                    <div class="frame-layer-2">
                      <div class="frame-layer-3">
                        <div class="card text-center card-bg-8">
                          <div class="card-cotent-data">
                            <h4> Total New Company RP meetings  </h4>
                            <p>Complete Meeting.</p>
                            <h5>
                              <a target="_BLANK" class="card-count-heading" href="<?=base_url()?>Menu/MeetingsDatas/total_new_company_RP_meetings/<?=$uid?>/<?=$sdate?>/<?=$edate?>"><?= $total_new_company_RP_meetings ?></a>
                            </h5>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
                <div class="col-md-3">
                  <div class="frame-layer-1">
                    <div class="frame-layer-2">
                      <div class="frame-layer-3">
                        <div class="card text-center card-bg-9">
                          <div class="card-cotent-data">
                            <h4>Total New Company No RP meetings  </h4>
                            <p>Complete Meeting.</p>
                            <h5>
                              <a target="_BLANK" class="card-count-heading" href="<?=base_url()?>Menu/MeetingsDatas/total_new_company_NO_RP_meetings/<?=$uid?>/<?=$sdate?>/<?=$edate?>"><?= $total_new_company_NO_RP_meetings ?></a>
                            </h5>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
                <div class="col-md-3">
                  <div class="frame-layer-1">
                    <div class="frame-layer-2">
                      <div class="frame-layer-3">
                        <div class="card text-center card-bg-4">
                          <div class="card-cotent-data">
                            <h4>  Total New Company Only Got Details Meetings  </h4>
                            <p>Complete Meeting.</p>
                            <h5>
                              <a target="_BLANK" class="card-count-heading" href="<?=base_url()?>Menu/MeetingsDatas/total_new_company_Only_Got_details_meetings/<?=$uid?>/<?=$sdate?>/<?=$edate?>"><?= $total_new_company_Only_Got_details_meetings ?></a>
                            </h5>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>



                <div class="col-md-4">
                  <div class="frame-layer-1">
                    <div class="frame-layer-2">
                      <div class="frame-layer-3">
                        <div class="card text-center card-bg-8">
                          <div class="card-cotent-data">
                            <h4> Total New Company RP meetings  </h4>
                            <p>Complete Meeting.</p>
                            <h5>
                              <a target="_BLANK" class="card-count-heading" href="<?=base_url()?>Menu/MeetingsDatas/total_new_company_RP_meetings/<?=$uid?>/<?=$sdate?>/<?=$edate?>"><?= $total_new_company_RP_meetings ?></a>
                            </h5>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
                <div class="col-md-4">
                  <div class="frame-layer-1">
                    <div class="frame-layer-2">
                      <div class="frame-layer-3">
                        <div class="card text-center card-bg-5">
                          <div class="card-cotent-data">
                            <h4>  Total New Company Potential RP Meetings </h4>
                            <p>Complete Meeting.</p>
                            <h5>
                              <a target="_BLANK" class="card-count-heading" href="<?=base_url()?>Menu/MeetingsDatas/total_new_company_potential_rp_meetings/<?=$uid?>/<?=$sdate?>/<?=$edate?>"><?= $total_new_company_potential_rp_meetings ?></a>
                            </h5>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
                <div class="col-md-4">
                  <div class="frame-layer-1">
                    <div class="frame-layer-2">
                      <div class="frame-layer-3">
                        <div class="card text-center card-bg-6">
                          <div class="card-cotent-data">
                            <h4> Total New Company Non Potential RP Meetings </h4>
                            <p>Complete Meeting.</p>
                            <h5>
                              <a target="_BLANK" class="card-count-heading" href="<?=base_url()?>Menu/MeetingsDatas/total_new_company_non_rp_potential_meetings/<?=$uid?>/<?=$sdate?>/<?=$edate?>"><?= $total_new_company_non_rp_potential_meetings ?></a>
                            </h5>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            </div>
            <hr>


            <hr>
            <div class="taskfrequency" style="background: linear-gradient(to right, #ffe0b2, #81f7ff40); box-shadow: 0 4px 8px rgba(0,0,0,0.1); padding: 20px;">
         <!-- Scheduled Meetings Group -->
            <div class="card-group bgbackground">
             <div class="card-group-title">
                Total Complete Meetings By Category <br>
                <small>This section provides a detailed breakdown of all completed meetings categorized by their respective types. It helps in analyzing the distribution and frequency of different meeting categories over the specified period.</small>
              </div>
              <div class="row">
                 <div class="col-md-3">
                  <div class="frame-layer-1">
                    <div class="frame-layer-2">
                      <div class="frame-layer-3" style="background: linear-gradient(145deg, #add984, #add984);">
                        <div class="card text-center card-bg-1">
                          <div class="card-cotent-data">
                            <h4>Total Complete Meetings</h4>
                            <p>Meetings that are completed.</p>
                            <h5>
                               <a target="_BLANK" class="card-count-heading" href="<?=base_url()?>Menu/MeetingsDatas/complete_meetings/<?=$uid?>/<?=$sdate?>/<?=$edate?>"><?= $complete_meetings ?></a>
                            </h5>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
                <div class="col-md-2">
                  <div class="frame-layer-1">
                    <div class="frame-layer-2">
                      <div class="frame-layer-3">
                        <div class="card text-center card-bg-7">
                          <div class="card-cotent-data">
                            <h4> Q1 20 Closure Funnel	</h4>
                            <p>Complete Meeting.</p>
                            <h5>
                              <a target="_BLANK" class="card-count-heading" href="<?=base_url()?>Menu/MeetingsDatas/total_in_q1_twetenty_closure_funnel/<?=$uid?>/<?=$sdate?>/<?=$edate?>"><?= $total_in_q1_twetenty_closure_funnel ?></a>
                            </h5>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
                <div class="col-md-2">
                  <div class="frame-layer-1">
                    <div class="frame-layer-2">
                      <div class="frame-layer-3">
                        <div class="card text-center card-bg-8">
                          <div class="card-cotent-data">
                            <h4> Potential Funnel For FY </h4>
                            <p>Complete Meeting.</p>
                            <h5>
                              <a target="_BLANK" class="card-count-heading" href="<?=base_url()?>Menu/MeetingsDatas/total_in_potential_funnel_for_fy/<?=$uid?>/<?=$sdate?>/<?=$edate?>"><?= $total_in_potential_funnel_for_fy ?></a>
                            </h5>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
                <div class="col-md-2">
                  <div class="frame-layer-1">
                    <div class="frame-layer-2">
                      <div class="frame-layer-3">
                        <div class="card text-center card-bg-9">
                          <div class="card-cotent-data">
                            <h4> To be Nurtured for FY </h4>
                            <p>Complete Meeting.</p>
                            <h5>
                              <a target="_BLANK" class="card-count-heading" href="<?=base_url()?>Menu/MeetingsDatas/total_in_to_be_nurtured_for_fy/<?=$uid?>/<?=$sdate?>/<?=$edate?>"><?= $total_in_to_be_nurtured_for_fy ?></a>
                            </h5>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
                <div class="col-md-3">
                  <div class="frame-layer-1">
                    <div class="frame-layer-2">
                      <div class="frame-layer-3">
                        <div class="card text-center card-bg-4">
                          <div class="card-cotent-data">
                            <h4> 50 New Lead Funnel </h4>
                            <p>Complete Meeting.</p>
                            <h5>
                              <a target="_BLANK" class="card-count-heading" href="<?=base_url()?>Menu/MeetingsDatas/total_in_fifity_new_lead_funnel/<?=$uid?>/<?=$sdate?>/<?=$edate?>"><?= $total_in_fifity_new_lead_funnel ?></a>
                            </h5>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            </div>
            <hr>



           



                          
<div class="taskfrequency" style="background: linear-gradient(to right, #ffe0b2, #ffffff); border-radius: 12px; box-shadow: 0 4px 8px rgba(0,0,0,0.1); padding: 20px;">
                                     <!-- Scheduled Meetings Group -->
<div class="card-group bgbackground">
<div class="card-group-title">
  Total Complete Meetings Frequency Per Day <br>
  <small>This section provides an overview of the frequency of various types of meetings completed each day within the specified date range. It includes total meetings, RP meetings, non-RP meetings, and meetings where only details were gathered.</small>
</div>

  <div class="row">
    <div class="col-md-3">
      <div class="frame-layer-1">
        <div class="frame-layer-2">
          <div class="frame-layer-3" style="background: linear-gradient(145deg, #add984, #add984);">
            <div class="card text-center card-bg-1">
              <div class="card-cotent-data">
                <h4>Total Meetings Frequency Per Day</h4>
                <p>the total number of meetings completed each day. It provides an overview of daily meeting activities.</p>
                <h5><?= $task_frequency_per_day ?> Task Per Day</h5>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="col-md-3">
      <div class="frame-layer-1">
        <div class="frame-layer-2">
          <div class="frame-layer-3" style="background: linear-gradient(145deg, #ff9999, #ff9999);">
            <div class="card text-center card-bg-15">
              <div class="card-cotent-data">
                <h4>Total RP Meetings Frequency Per Day</h4>
                <p>Task frequency of RP (Right Persons.) meetings completed each day. It helps in tracking RP-specific meeting activities.</p>
                <h5><?= $task_RP_frequency_per_day ?> Task Per Day</h5>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="col-md-3">
      <div class="frame-layer-1">
        <div class="frame-layer-2">
          <div class="frame-layer-3" style="background: linear-gradient(145deg, #99ccff, #99ccff);">
            <div class="card text-center card-bg-16">
              <div class="card-cotent-data">
                <h4>Total NO RP Meetings Frequency Per Day</h4>
                <p>the number of non-RP (Non-Resource Planning) meetings completed each day. It provides insights into other types of meeting activities.</p>
                <h5><?= $task_NO_RP_frequency_per_day ?> Task Per Day</h5>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="col-md-3">
      <div class="frame-layer-1">
        <div class="frame-layer-2">
          <div class="frame-layer-3" style="background: linear-gradient(145deg, #ffcc99, #ffcc99);">
            <div class="card text-center card-bg-10">
              <div class="card-cotent-data">
                <h4>Total Only Got Details Meetings Frequency Per Day</h4>
                <p>the frequency of meetings where only details were gathered each day. It helps in understanding the number of informational meetings conducted.</p>
                <h5><?= $task_OGD_frequency_per_day ?> Task Per Day</h5>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
<hr>

                          </div>



             <hr>
            <div class="bg-primary p-1">
              <h3 class="text-center">
                Total Meeting By User
              </h3>
            </div>
            <hr>
            <div class="body-content">
              <div class="page-header">
                <div class="table-responsive text-nowrap">
                  <table id="example1" class="table table-striped table-bordered" cellspacing="0" width="100%">
                  <thead class="thead-dark">
                <tr>
                    <th>Sr.No</th>
                    <th>Name</th>
                    <th>Total Meetings</th>
                    <th>Complete Meetings</th>
                    <th>Not Complete Meetings</th>
                    <th>Total Scheduled Meetings</th>
                    <th>Total Complete Scheduled Meetings</th>
                    <th>Not Complete Scheduled Meetings</th>
                    <th>Total Barg Meetings</th>
                    <th>Total Complete Barg Meetings</th>
                    <th>Not Complete Barg Meetings</th>

                    <!-- <th>Total Join Meetings</th>
                    <th>Total Complete Join Meetings</th>
                    <th>Not Complete Join Meetings</th> -->

                    <th>Total RP Meetings</th>
                    <th>Total NO RP Meetings</th>
                    <th>Total Only Got Details Meetings</th>
                    <th>Total Scheduled RP Meetings</th>
                    <th>Total Scheduled NO RP Meetings</th>
                    <th>Total Scheduled Only Got Details Meetings</th>
                    <th>Total Barge RP Meetings</th>
                    <th>Total Barge NO RP Meetings</th>
                    <th>Total Barge Only Got Details Meetings</th>
                    <th>Total Potential Meetings</th>
                    <th>Total Non Potential Meetings</th>
                    <th>Total Topspender Meetings</th>
                    <th>Total Upsell Client Meetings</th>
                    <th>Total Focus Funnel Meetings</th>
                    <th>Total Keycompany Meetings</th>
                    <th>Total Pkclient Meetings</th>
                    <th>Total Priorityc Meetings</th>
                    <th>Total Fresh Meetings</th>
                    <th>Total Re Meetings</th>
                    <th>Total Write Mom RP Meetings</th>
                    <th>Total Pending For Write Mom RP Meeting</th>
                    <th>Total Mom Data</th>
                    <th>Total Approved After Check Mom Data</th>
                    <th>Total Reject After Check Mom Data</th>
                    <th>Total NO RP After Check Mom Data</th>
                    <th>Total Pending For Check Mom Data</th>
                    <th>Task Frequency Per Day</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $i = 1;
                foreach ($meetingsUserByDatas as $row) {
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
                      <td class="text-capitalize"><?php echo htmlspecialchars($row->name); ?></td>
                      <td>
                        <a target="_BLANK" class="card-count-heading-new" style="color:<?=$backgroundColorNew;?>!important" href="<?=base_url()?>Menu/MeetingsDatas/total_plan_meetings/<?=$meeting_user_id?>/<?=$sdate?>/<?=$edate?>/userwise">
                         <?= $row->total_plan_meetings; ?>
                        </a>
                      </td>
                      <td>
                        <a target="_BLANK" class="card-count-heading-new" style="color:<?=$backgroundColorNew;?>!important" href="<?=base_url()?>Menu/MeetingsDatas/complete_meetings/<?=$meeting_user_id?>/<?=$sdate?>/<?=$edate?>/userwise">
                         <?= $row->complete_meetings; ?>
                        </a>
                      </td>
                      <td>
                        <a target="_BLANK" class="card-count-heading-new" style="color:<?=$backgroundColorNew;?>!important" href="<?=base_url()?>Menu/MeetingsDatas/not_complete_meetings/<?=$meeting_user_id?>/<?=$sdate?>/<?=$edate?>/userwise">
                         <?= $row->not_complete_meetings; ?>
                        </a>
                      </td>
                      <td>
                        <a target="_BLANK" class="card-count-heading-new" style="color:<?=$backgroundColorNew;?>!important" href="<?=base_url()?>Menu/MeetingsDatas/total_scheduled_meetings/<?=$meeting_user_id?>/<?=$sdate?>/<?=$edate?>/userwise">
                         <?= $row->total_sheduled_meetings; ?>
                        </a>
                      </td>
                      <td>
                        <a target="_BLANK" class="card-count-heading-new" style="color:<?=$backgroundColorNew;?>!important" href="<?=base_url()?>Menu/MeetingsDatas/total_complete_scheduled_meetings/<?=$meeting_user_id?>/<?=$sdate?>/<?=$edate?>/userwise">
                         <?= $row->total_complete_sheduled_meetings; ?>
                        </a>
                      </td>
                      <td>
                        <a target="_BLANK" class="card-count-heading-new" style="color:<?=$backgroundColorNew;?>!important" href="<?=base_url()?>Menu/MeetingsDatas/not_complete_scheduled_meetings/<?=$meeting_user_id?>/<?=$sdate?>/<?=$edate?>/userwise">
                         <?= $row->not_complete_sheduled_meetings; ?>
                        </a>
                      </td>
                      <td>
                        <a target="_BLANK" class="card-count-heading-new" style="color:<?=$backgroundColorNew;?>!important" href="<?=base_url()?>Menu/MeetingsDatas/total_barg_meetings/<?=$meeting_user_id?>/<?=$sdate?>/<?=$edate?>/userwise">
                         <?= $row->total_barg_meetings; ?>
                        </a>
                      </td>
                      <td>
                        <a target="_BLANK" class="card-count-heading-new" style="color:<?=$backgroundColorNew;?>!important" href="<?=base_url()?>Menu/MeetingsDatas/total_complete_barg_meetings/<?=$meeting_user_id?>/<?=$sdate?>/<?=$edate?>/userwise">
                         <?= $row->total_complete_barg_meetings; ?>
                        </a>
                      </td>
                      <td>
                        <a target="_BLANK" class="card-count-heading-new" style="color:<?=$backgroundColorNew;?>!important" href="<?=base_url()?>Menu/MeetingsDatas/not_complete_barg_meetings/<?=$meeting_user_id?>/<?=$sdate?>/<?=$edate?>/userwise">
                         <?= $row->not_complete_barg_meetings; ?>
                        </a>
                      </td>
                     
                      <?php /*
                      <td><?php echo htmlspecialchars($row->total_join_meetings); ?></td>
                      <td><?php echo htmlspecialchars($row->total_complete_join_meetings); ?></td>
                      <td><?php echo htmlspecialchars($row->not_complete_join_meetings); ?></td>
                      */ ?>

                      <td>
                        <a target="_BLANK" class="card-count-heading-new" style="color:<?=$backgroundColorNew;?>!important" href="<?=base_url()?>Menu/MeetingsDatas/total_RP_meetings/<?=$meeting_user_id?>/<?=$sdate?>/<?=$edate?>/userwise">
                         <?= $row->total_RP_meetings; ?>
                        </a>
                      </td>
                      <td>
                        <a target="_BLANK" class="card-count-heading-new" style="color:<?=$backgroundColorNew;?>!important" href="<?=base_url()?>Menu/MeetingsDatas/total_NO_RP_meetings/<?=$meeting_user_id?>/<?=$sdate?>/<?=$edate?>/userwise">
                         <?= $row->total_NO_RP_meetings; ?>
                        </a>
                      </td>
                      <td>
                        <a target="_BLANK" class="card-count-heading-new" style="color:<?=$backgroundColorNew;?>!important" href="<?=base_url()?>Menu/MeetingsDatas/total_Only_Got_details_meetings/<?=$meeting_user_id?>/<?=$sdate?>/<?=$edate?>/userwise">
                         <?= $row->total_Only_Got_details_meetings; ?>
                        </a>
                      </td>
                      <td>
                        <a target="_BLANK" class="card-count-heading-new" style="color:<?=$backgroundColorNew;?>!important" href="<?=base_url()?>Menu/MeetingsDatas/total_Scheduled_RP_meetings/<?=$meeting_user_id?>/<?=$sdate?>/<?=$edate?>/userwise">
                         <?= $row->total_Sheduled_RP_meetings; ?>
                        </a>
                      </td>
                      <td>
                        <a target="_BLANK" class="card-count-heading-new" style="color:<?=$backgroundColorNew;?>!important" href="<?=base_url()?>Menu/MeetingsDatas/total_Scheduled_NO_RP_meetings/<?=$meeting_user_id?>/<?=$sdate?>/<?=$edate?>/userwise">
                         <?= $row->total_Sheduled_NO_RP_meetings; ?>
                        </a>
                      </td>
                      <td>
                        <a target="_BLANK" class="card-count-heading-new" style="color:<?=$backgroundColorNew;?>!important" href="<?=base_url()?>Menu/MeetingsDatas/total_Scheduled_Only_Got_details_meetings/<?=$meeting_user_id?>/<?=$sdate?>/<?=$edate?>/userwise">
                         <?= $row->total_Scheduled_Only_Got_details_meetings; ?>
                        </a>
                      </td>
                      <td>
                        <a target="_BLANK" class="card-count-heading-new" style="color:<?=$backgroundColorNew;?>!important" href="<?=base_url()?>Menu/MeetingsDatas/total_Barge_RP_meetings/<?=$meeting_user_id?>/<?=$sdate?>/<?=$edate?>/userwise">
                         <?= $row->total_Barge_RP_meetings; ?>
                        </a>
                      </td>
                      <td>
                        <a target="_BLANK" class="card-count-heading-new" style="color:<?=$backgroundColorNew;?>!important" href="<?=base_url()?>Menu/MeetingsDatas/total_Barge_NO_RP_meetings/<?=$meeting_user_id?>/<?=$sdate?>/<?=$edate?>/userwise">
                         <?= $row->total_Barge_NO_RP_meetings; ?>
                        </a>
                      </td>
                      <td>
                        <a target="_BLANK" class="card-count-heading-new" style="color:<?=$backgroundColorNew;?>!important" href="<?=base_url()?>Menu/MeetingsDatas/total_Barge_Only_Got_details_meetings/<?=$meeting_user_id?>/<?=$sdate?>/<?=$edate?>/userwise">
                         <?= $row->total_Barge_Only_Got_details_meetings; ?>
                        </a>
                      </td>
                      <td>
                        <a target="_BLANK" class="card-count-heading-new" style="color:<?=$backgroundColorNew;?>!important" href="<?=base_url()?>Menu/MeetingsDatas/total_potential_meetings/<?=$meeting_user_id?>/<?=$sdate?>/<?=$edate?>/userwise">
                         <?= $row->total_potential_meetings; ?>
                        </a>
                      </td>
                      <td>
                        <a target="_BLANK" class="card-count-heading-new" style="color:<?=$backgroundColorNew;?>!important" href="<?=base_url()?>Menu/MeetingsDatas/total_non_potential_meetings/<?=$meeting_user_id?>/<?=$sdate?>/<?=$edate?>/userwise">
                         <?= $row->total_non_potential_meetings; ?>
                        </a>
                      </td>
                      <td>
                        <a target="_BLANK" class="card-count-heading-new" style="color:<?=$backgroundColorNew;?>!important" href="<?=base_url()?>Menu/MeetingsDatas/total_topspender_meetings/<?=$meeting_user_id?>/<?=$sdate?>/<?=$edate?>/userwise">
                         <?= $row->total_topspender_meetings; ?>
                        </a>
                      </td>
                      <td>
                        <a target="_BLANK" class="card-count-heading-new" style="color:<?=$backgroundColorNew;?>!important" href="<?=base_url()?>Menu/MeetingsDatas/total_upsell_client_meetings/<?=$meeting_user_id?>/<?=$sdate?>/<?=$edate?>/userwise">
                         <?= $row->total_upsell_client_meetings; ?>
                        </a>
                      </td>
                      <td>
                        <a target="_BLANK" class="card-count-heading-new" style="color:<?=$backgroundColorNew;?>!important" href="<?=base_url()?>Menu/MeetingsDatas/total_focus_funnel_meetings/<?=$meeting_user_id?>/<?=$sdate?>/<?=$edate?>/userwise">
                         <?= $row->total_focus_funnel_meetings; ?>
                        </a>
                      </td>
                      <td>
                        <a target="_BLANK" class="card-count-heading-new" style="color:<?=$backgroundColorNew;?>!important" href="<?=base_url()?>Menu/MeetingsDatas/total_keycompany_meetings/<?=$meeting_user_id?>/<?=$sdate?>/<?=$edate?>/userwise">
                         <?= $row->total_keycompany_meetings; ?>
                        </a>
                      </td>
                      <td>
                        <a target="_BLANK" class="card-count-heading-new" style="color:<?=$backgroundColorNew;?>!important" href="<?=base_url()?>Menu/MeetingsDatas/total_pkclient_meetings/<?=$meeting_user_id?>/<?=$sdate?>/<?=$edate?>/userwise">
                         <?= $row->total_pkclient_meetings; ?>
                        </a>
                      </td>
                      <td>
                        <a target="_BLANK" class="card-count-heading-new" style="color:<?=$backgroundColorNew;?>!important" href="<?=base_url()?>Menu/MeetingsDatas/total_priorityc_meetings/<?=$meeting_user_id?>/<?=$sdate?>/<?=$edate?>/userwise">
                         <?= $row->total_priorityc_meetings; ?>
                        </a>
                      </td>
                      <td>
                        <a target="_BLANK" class="card-count-heading-new" style="color:<?=$backgroundColorNew;?>!important" href="<?=base_url()?>Menu/MeetingsDatas/total_fresh_meetings/<?=$meeting_user_id?>/<?=$sdate?>/<?=$edate?>/userwise">
                         <?= $row->total_fresh_meetings; ?>
                        </a>
                      </td>
                      <td>
                        <a target="_BLANK" class="card-count-heading-new" style="color:<?=$backgroundColorNew;?>!important" href="<?=base_url()?>Menu/MeetingsDatas/total_re_meetings/<?=$meeting_user_id?>/<?=$sdate?>/<?=$edate?>/userwise">
                         <?= $row->total_re_meetings; ?>
                        </a>
                      </td>
                      <td>
                        <a target="_BLANK" class="card-count-heading-new" style="color:<?=$backgroundColorNew;?>!important" href="<?=base_url()?>Menu/MeetingsDatas/total_write_mom_rp_meetings/<?=$meeting_user_id?>/<?=$sdate?>/<?=$edate?>/userwise">
                         <?= $row->total_write_mom_rp_meetings; ?>
                        </a>
                      </td>
                      <td>
                        <a target="_BLANK" class="card-count-heading-new" style="color:<?=$backgroundColorNew;?>!important" href="<?=base_url()?>Menu/MeetingsDatas/total_pending_for_write_mom_rp_meeting/<?=$meeting_user_id?>/<?=$sdate?>/<?=$edate?>/userwise">
                         <?= $row->total_pending_for_write_mom_rp_meeting; ?>
                        </a>
                      </td>
                      <td>
                        <a target="_BLANK" class="card-count-heading-new" style="color:<?=$backgroundColorNew;?>!important" href="<?=base_url()?>Menu/MeetingsDatas/total_mom_data/<?=$meeting_user_id?>/<?=$sdate?>/<?=$edate?>/userwise">
                         <?= $row->total_mom_data; ?>
                        </a>
                      </td>
                      <td>
                        <a target="_BLANK" class="card-count-heading-new" style="color:<?=$backgroundColorNew;?>!important" href="<?=base_url()?>Menu/MeetingsDatas/total_approved_after_check_mom_data/<?=$meeting_user_id?>/<?=$sdate?>/<?=$edate?>/userwise">
                         <?= $row->total_approved_after_check_mom_data; ?>
                        </a>
                      </td>
                      <td>
                        <a target="_BLANK" class="card-count-heading-new" style="color:<?=$backgroundColorNew;?>!important" href="<?=base_url()?>Menu/MeetingsDatas/total_reject_after_check_mom_data/<?=$meeting_user_id?>/<?=$sdate?>/<?=$edate?>/userwise">
                         <?= $row->total_reject_after_check_mom_data; ?>
                        </a>
                      </td>
                      <td>
                        <a target="_BLANK" class="card-count-heading-new" style="color:<?=$backgroundColorNew;?>!important" href="<?=base_url()?>Menu/MeetingsDatas/total_NO_RP_after_check_mom_data/<?=$meeting_user_id?>/<?=$sdate?>/<?=$edate?>/userwise">
                         <?= $row->total_NO_RP_after_check_mom_data; ?>
                        </a>
                      </td>
                      <td>
                        <a target="_BLANK" class="card-count-heading-new" style="color:<?=$backgroundColorNew;?>!important" href="<?=base_url()?>Menu/MeetingsDatas/total_pending_for_check_mom_data/<?=$meeting_user_id?>/<?=$sdate?>/<?=$edate?>/userwise">
                         <?= $row->total_pending_for_check_mom_data; ?>
                        </a>
                      </td>
                      <td>
                        <?= $row->task_frequency_per_day; ?> Task Per Day
                      </td>
                  </tr>
               <?php $i++; } ?>
            </tbody>
                  </table>
                </div>
                <hr />
              </div>
            </div>




             <hr>
            <div class="bg-primary p-1">
              <h3 class="text-center">
                Company Created Due to Meeting 
                <!-- Between Date <?=$sdate?> To  <?=$edate?>  -->
              </h3>
            </div>
            <hr>

            <div class="table-responsive text-nowrap">
                  <table id="example5" class="table table-striped table-bordered" cellspacing="0" width="100%">
                  <thead class="thead-dark">
                <tr>
                    <th>Sr.No</th>
                    <th>Name</th>
                    <th> Total New Company</th>
                    <th> Total New Company Meeting Complete</th>
                    <th> Total New Company Meeting Pending</th>
                    <th> Total New Company RP meetings </th>
                    <th> Total New Company No RP meetings </th>
                    <th> Total New Company Only Got Details Meetings </th>
                    <th> Total New Company Potential RP Meetings </th>
                    <th> Total New Company Non Potential RP Meetings </th>
                </tr>
            </thead>
            <tbody>
                <?php
                $i = 1;
                foreach ($totalNewCompanyMeetingsUser as $row) {
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
                        <a target="_BLANK" class="card-count-heading-new" style="color:<?=$backgroundColorNew;?>!important" href="<?=base_url()?>Menu/MeetingsDatas/total_new_company/<?=$meeting_user_id?>/<?=$sdate?>/<?=$edate?>/userwise">
                         <?= $row->total_new_company; ?>
                        </a>
                      </td>
                      <td>
                        <a target="_BLANK" class="card-count-heading-new" style="color:<?=$backgroundColorNew;?>!important" href="<?=base_url()?>Menu/MeetingsDatas/total_new_company_complete/<?=$meeting_user_id?>/<?=$sdate?>/<?=$edate?>/userwise">
                         <?= $row->total_new_company_complete; ?>
                        </a>
                      </td>
                      <td>
                        <a target="_BLANK" class="card-count-heading-new" style="color:<?=$backgroundColorNew;?>!important" href="<?=base_url()?>Menu/MeetingsDatas/total_new_company_pending/<?=$meeting_user_id?>/<?=$sdate?>/<?=$edate?>/userwise">
                         <?= $row->total_new_company_pending; ?>
                        </a>
                      </td>
                      <td>
                        <a target="_BLANK" class="card-count-heading-new" style="color:<?=$backgroundColorNew;?>!important" href="<?=base_url()?>Menu/MeetingsDatas/total_new_company_RP_meetings/<?=$meeting_user_id?>/<?=$sdate?>/<?=$edate?>/userwise">
                         <?= $row->total_new_company_RP_meetings; ?>
                        </a>
                      </td>
                      <td>
                        <a target="_BLANK" class="card-count-heading-new" style="color:<?=$backgroundColorNew;?>!important" href="<?=base_url()?>Menu/MeetingsDatas/total_new_company_NO_RP_meetings/<?=$meeting_user_id?>/<?=$sdate?>/<?=$edate?>/userwise">
                         <?= $row->total_new_company_NO_RP_meetings; ?>
                        </a>
                      </td>
                      <td>
                        <a target="_BLANK" class="card-count-heading-new" style="color:<?=$backgroundColorNew;?>!important" href="<?=base_url()?>Menu/MeetingsDatas/total_new_company_Only_Got_details_meetings/<?=$meeting_user_id?>/<?=$sdate?>/<?=$edate?>/userwise">
                         <?= $row->total_new_company_Only_Got_details_meetings; ?>
                        </a>
                      </td>
                      <td>
                        <a target="_BLANK" class="card-count-heading-new" style="color:<?=$backgroundColorNew;?>!important" href="<?=base_url()?>Menu/MeetingsDatas/total_new_company_potential_rp_meetings/<?=$meeting_user_id?>/<?=$sdate?>/<?=$edate?>/userwise">
                         <?= $row->total_new_company_potential_rp_meetings; ?>
                        </a>
                      </td>
                      <td>
                        <a target="_BLANK" class="card-count-heading-new" style="color:<?=$backgroundColorNew;?>!important" href="<?=base_url()?>Menu/MeetingsDatas/total_new_company_non_rp_potential_meetings/<?=$meeting_user_id?>/<?=$sdate?>/<?=$edate?>/userwise">
                         <?= $row->total_new_company_non_rp_potential_meetings; ?>
                        </a>
                      </td>
             
                    
                  </tr>
               <?php $i++; } ?>
            </tbody>
                  </table>
                </div>






            <hr>
            <div class="bg-primary p-1">
              <h3 class="text-center">
                Meeting By Category
              </h3>
            </div>
            <hr>

            <div class="table-responsive text-nowrap">
                  <table id="example2" class="table table-striped table-bordered" cellspacing="0" width="100%">
                  <thead class="thead-dark">
                <tr>
                    <th>Sr.No</th>
                    <th>Name</th>
                    <th>Total Company in Q1 20 Closure Funnel	</th>
                    <th>Complete Meeting in - Q1 20 Closure Funnel	</th>
                    <th>Total Company in Potential Funnel For FY </th>
                    <th>Complete Meeting in - Potential Funnel For FY </th>
                    <th>Total Company in To be Nurtured for FY </th>
                    <th>Complete Meeting in - To be Nurtured for FY </th>
                    <th>Total Company in 50 New Lead Funnel </th>
                    <th>Complete Meeting in - 50 New Lead Funnel </th>
                </tr>
            </thead>
            <tbody>
                <?php
                $i = 1;
                foreach ($totalMeetingsUserByDatasCategory as $row) {
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
                        <a target="_BLANK" class="card-count-heading-new" style="color:<?=$backgroundColorNew;?>!important" href="<?=base_url()?>Menu/MeetingsDatas/total_company_in_q1_twetenty_closure_funnel/<?=$meeting_user_id?>/<?=$sdate?>/<?=$edate?>/userwise">
                         <?= $row->total_company_in_q1_twetenty_closure_funnel; ?>
                        </a>
                      </td>
                      <td>
                        <a target="_BLANK" class="card-count-heading-new" style="color:<?=$backgroundColorNew;?>!important" href="<?=base_url()?>Menu/MeetingsDatas/total_in_q1_twetenty_closure_funnel/<?=$meeting_user_id?>/<?=$sdate?>/<?=$edate?>/userwise">
                         <?= $row->total_in_q1_twetenty_closure_funnel; ?>
                        </a>
                      </td>
                      <td>

                        <a target="_BLANK" class="card-count-heading-new" style="color:<?=$backgroundColorNew;?>!important" href="<?=base_url()?>Menu/MeetingsDatas/total_company_in_potential_funnel_for_fy/<?=$meeting_user_id?>/<?=$sdate?>/<?=$edate?>/userwise">
                         <?= $row->total_company_in_potential_funnel_for_fy; ?>
                        </a>
                      </td>
                      <td>
                        <a target="_BLANK" class="card-count-heading-new" style="color:<?=$backgroundColorNew;?>!important" href="<?=base_url()?>Menu/MeetingsDatas/total_in_potential_funnel_for_fy/<?=$meeting_user_id?>/<?=$sdate?>/<?=$edate?>/userwise">
                         <?= $row->total_in_potential_funnel_for_fy; ?>
                        </a>
                      </td>
                      
                      <td>
                        <a target="_BLANK" class="card-count-heading-new" style="color:<?=$backgroundColorNew;?>!important" href="<?=base_url()?>Menu/MeetingsDatas/total_company_in_to_be_nurtured_for_fy/<?=$meeting_user_id?>/<?=$sdate?>/<?=$edate?>/userwise">
                         <?= $row->total_company_in_to_be_nurtured_for_fy; ?>
                        </a>
                      </td>
                      <td>
                        <a target="_BLANK" class="card-count-heading-new" style="color:<?=$backgroundColorNew;?>!important" href="<?=base_url()?>Menu/MeetingsDatas/total_in_to_be_nurtured_for_fy/<?=$meeting_user_id?>/<?=$sdate?>/<?=$edate?>/userwise">
                         <?= $row->total_in_to_be_nurtured_for_fy; ?>
                        </a>
                      </td>


                      <td>
                        <a target="_BLANK" class="card-count-heading-new" style="color:<?=$backgroundColorNew;?>!important" href="<?=base_url()?>Menu/MeetingsDatas/total_company_in_fifity_new_lead_funnel/<?=$meeting_user_id?>/<?=$sdate?>/<?=$edate?>/userwise">
                         <?= $row->total_company_in_fifity_new_lead_funnel; ?>
                        </a>
                      </td>
                      <td>
                        <a target="_BLANK" class="card-count-heading-new" style="color:<?=$backgroundColorNew;?>!important" href="<?=base_url()?>Menu/MeetingsDatas/total_in_fifity_new_lead_funnel/<?=$meeting_user_id?>/<?=$sdate?>/<?=$edate?>/userwise">
                         <?= $row->total_in_fifity_new_lead_funnel; ?>
                        </a>
                      </td>
                    
                  </tr>
               <?php $i++; } ?>
            </tbody>
                  </table>
                </div>


            <hr>
            <div class="bg-primary p-1">
              <h3 class="text-center">
                Meeting By Status
              </h3>
            </div>
            <hr>

            <div class="table-responsive text-nowrap">
                  <table id="example3" class="table table-striped table-bordered" cellspacing="0" width="100%">
                  <thead class="thead-dark">
                  <tr>
                    <th>Sr.No</th>
                    <th>Name</th>
                    <th>Open</th>
                    <th>Reachout</th>
                    <th>Tentative</th>
                    <th>Will do Later</th>
                    <th>Not Interested</th>
                    <th>Positive</th>
                    <th>Closure</th>
                    <th>OPEN RPEM</th>
                    <th>Very Positive</th>
                    <th>TTD-Reachout</th>
                    <th>WNO-Reachout</th>
                    <th>Positive-NAP</th>
                    <th>Very Positive-NAP</th>
                    <th>On-Boarded</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $i = 1;
                foreach ($totalMeetingsUserByDatasStatus as $row) {
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
                        <a target="_BLANK" class="card-count-heading-new" style="color:<?=$backgroundColorNew;?>!important" href="<?=base_url()?>Menu/MeetingsDatas/total_open/<?=$meeting_user_id?>/<?=$sdate?>/<?=$edate?>/userwise">
                         <?= $row->total_open; ?>
                        </a>
                      </td>
                      <td>
                        <a target="_BLANK" class="card-count-heading-new" style="color:<?=$backgroundColorNew;?>!important" href="<?=base_url()?>Menu/MeetingsDatas/total_reachout/<?=$meeting_user_id?>/<?=$sdate?>/<?=$edate?>/userwise">
                         <?= $row->total_reachout; ?>
                        </a>
                      </td>
                      <td>
                        <a target="_BLANK" class="card-count-heading-new" style="color:<?=$backgroundColorNew;?>!important" href="<?=base_url()?>Menu/MeetingsDatas/total_tentative/<?=$meeting_user_id?>/<?=$sdate?>/<?=$edate?>/userwise">
                         <?= $row->total_tentative; ?>
                        </a>
                      </td>
                      <td>
                        <a target="_BLANK" class="card-count-heading-new" style="color:<?=$backgroundColorNew;?>!important" href="<?=base_url()?>Menu/MeetingsDatas/total_will_do_later/<?=$meeting_user_id?>/<?=$sdate?>/<?=$edate?>/userwise">
                         <?= $row->total_will_do_later; ?>
                        </a>
                      </td>
                      <td>
                        <a target="_BLANK" class="card-count-heading-new" style="color:<?=$backgroundColorNew;?>!important" href="<?=base_url()?>Menu/MeetingsDatas/total_not_interested/<?=$meeting_user_id?>/<?=$sdate?>/<?=$edate?>/userwise">
                         <?= $row->total_not_interested; ?>
                        </a>
                      </td>
                      <td>
                        <a target="_BLANK" class="card-count-heading-new" style="color:<?=$backgroundColorNew;?>!important" href="<?=base_url()?>Menu/MeetingsDatas/total_positive/<?=$meeting_user_id?>/<?=$sdate?>/<?=$edate?>/userwise">
                         <?= $row->total_positive; ?>
                        </a>
                      </td>
                      <td>
                        <a target="_BLANK" class="card-count-heading-new" style="color:<?=$backgroundColorNew;?>!important" href="<?=base_url()?>Menu/MeetingsDatas/total_closure/<?=$meeting_user_id?>/<?=$sdate?>/<?=$edate?>/userwise">
                         <?= $row->total_closure; ?>
                        </a>
                      </td>
                      <td>
                        <a target="_BLANK" class="card-count-heading-new" style="color:<?=$backgroundColorNew;?>!important" href="<?=base_url()?>Menu/MeetingsDatas/total_open_rpem/<?=$meeting_user_id?>/<?=$sdate?>/<?=$edate?>/userwise">
                         <?= $row->total_open_rpem; ?>
                        </a>
                      </td>
                      <td>
                        <a target="_BLANK" class="card-count-heading-new" style="color:<?=$backgroundColorNew;?>!important" href="<?=base_url()?>Menu/MeetingsDatas/total_very_positive/<?=$meeting_user_id?>/<?=$sdate?>/<?=$edate?>/userwise">
                         <?= $row->total_very_positive; ?>
                        </a>
                      </td>
                      <td>
                        <a target="_BLANK" class="card-count-heading-new" style="color:<?=$backgroundColorNew;?>!important" href="<?=base_url()?>Menu/MeetingsDatas/total_ttd_reachout/<?=$meeting_user_id?>/<?=$sdate?>/<?=$edate?>/userwise">
                         <?= $row->total_ttd_reachout; ?>
                        </a>
                      </td>
                      <td>
                        <a target="_BLANK" class="card-count-heading-new" style="color:<?=$backgroundColorNew;?>!important" href="<?=base_url()?>Menu/MeetingsDatas/total_wno_reachout/<?=$meeting_user_id?>/<?=$sdate?>/<?=$edate?>/userwise">
                         <?= $row->total_wno_reachout; ?>
                        </a>
                      </td>
                      <td>
                        <a target="_BLANK" class="card-count-heading-new" style="color:<?=$backgroundColorNew;?>!important" href="<?=base_url()?>Menu/MeetingsDatas/total_positive_nap/<?=$meeting_user_id?>/<?=$sdate?>/<?=$edate?>/userwise">
                         <?= $row->total_positive_nap; ?>
                        </a>
                      </td>
                      <td>
                        <a target="_BLANK" class="card-count-heading-new" style="color:<?=$backgroundColorNew;?>!important" href="<?=base_url()?>Menu/MeetingsDatas/total_very_positive_nap/<?=$meeting_user_id?>/<?=$sdate?>/<?=$edate?>/userwise">
                         <?= $row->total_very_positive_nap; ?>
                        </a>
                      </td>
                      <td>
                        <a target="_BLANK" class="card-count-heading-new" style="color:<?=$backgroundColorNew;?>!important" href="<?=base_url()?>Menu/MeetingsDatas/total_on_boarded/<?=$meeting_user_id?>/<?=$sdate?>/<?=$edate?>/userwise">
                         <?= $row->total_on_boarded; ?>
                        </a>
                      </td>
                     
                     
                    
                  </tr>
               <?php $i++; } ?>
            </tbody>
                  </table>
                </div>



            <hr>
            <div class="bg-primary p-1">
              <h3 class="text-center">
                Meeting Update Between Time
              </h3>
            </div>
            <hr>

            <div class="table-responsive text-nowrap">
                  <table id="example4" class="table table-striped table-bordered" cellspacing="0" width="100%">
                  <thead class="thead-dark">
                <tr>
                    <th>Sr.No</th>
                    <th>Name</th>
                    <th> 1 to 15 Minutes </th>
                    <th> 15 to 30 Minutes </th>
                    <th> 30 to 60 Minutes </th>
                    <th> More than 60 Minutes </th>
                </tr>
            </thead>
            <tbody>
                <?php
                $i = 1;
                foreach ($totalMeetingsUserByDatasTTime as $row) {
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
                        <a target="_BLANK" class="card-count-heading-new" style="color:<?=$backgroundColorNew;?>!important" href="<?=base_url()?>Menu/MeetingsDatas/one_to_fifteen/<?=$meeting_user_id?>/<?=$sdate?>/<?=$edate?>/userwise">
                         <?= $row->one_to_fifteen; ?>
                        </a>
                      </td>
                      <td>
                        <a target="_BLANK" class="card-count-heading-new" style="color:<?=$backgroundColorNew;?>!important" href="<?=base_url()?>Menu/MeetingsDatas/fifteen_to_thirteen/<?=$meeting_user_id?>/<?=$sdate?>/<?=$edate?>/userwise">
                         <?= $row->fifteen_to_thirteen; ?>
                        </a>
                      </td>
                      <td>
                        <a target="_BLANK" class="card-count-heading-new" style="color:<?=$backgroundColorNew;?>!important" href="<?=base_url()?>Menu/MeetingsDatas/thirteen_to_sixty/<?=$meeting_user_id?>/<?=$sdate?>/<?=$edate?>/userwise">
                         <?= $row->thirteen_to_sixty; ?>
                        </a>
                      </td>
                      <td>
                        <a target="_BLANK" class="card-count-heading-new" style="color:<?=$backgroundColorNew;?>!important" href="<?=base_url()?>Menu/MeetingsDatas/gretter_than_sixty/<?=$meeting_user_id?>/<?=$sdate?>/<?=$edate?>/userwise">
                         <?= $row->gretter_than_sixty; ?>
                        </a>
                      </td>
                    
                  </tr>
               <?php $i++; } ?>
            </tbody>
                  </table>
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
          "buttons": ["copy", "csv", "excel", "pdf", "print", "colvis"]
        }).buttons().container().appendTo('#example1_wrapper .col-md-6:eq(0)');
        $("#example2").DataTable({
          "responsive": false, "lengthChange": false, "autoWidth": false,
          "buttons": ["copy", "csv", "excel", "pdf", "print", "colvis"]
        }).buttons().container().appendTo('#example2_wrapper .col-md-6:eq(0)');
        $("#example3").DataTable({
          "responsive": false, "lengthChange": false, "autoWidth": false,
          "buttons": ["copy", "csv", "excel", "pdf", "print", "colvis"]
        }).buttons().container().appendTo('#example3_wrapper .col-md-6:eq(0)');
        $("#example4").DataTable({
          "responsive": false, "lengthChange": false, "autoWidth": false,
          "buttons": ["copy", "csv", "excel", "pdf", "print", "colvis"]
        }).buttons().container().appendTo('#example4_wrapper .col-md-6:eq(0)');
        $("#example5").DataTable({
          "responsive": false, "lengthChange": false, "autoWidth": false,
          "buttons": ["copy", "csv", "excel", "pdf", "print", "colvis"]
        }).buttons().container().appendTo('#example5_wrapper .col-md-6:eq(0)');
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

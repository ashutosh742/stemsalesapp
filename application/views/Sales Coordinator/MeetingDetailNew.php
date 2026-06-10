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
    a.card-count-heading{
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
              <div class="col-md-4"></div>
              <div class="col-md-5">
                <div class="text-right-data text-center">
                  <form class="form-container p-3" method="POST" action="<?=base_url(); ?>/Menu/MeetingDetailNew">
                    <input type="date" name="sdate" value="<?=$sdate ?>">
                    <input type="date" name="edate" value="<?=$edate ?>">
                    <button type="submit">Filter</button>
                  </form>
                </div>
              </div>
              <div class="col-md-3"></div>
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
        $total_pending_for_write_mom_rp_meeting = $data->total_pending_for_write_mom_rp_meeting;

        $total_mom_data                          = $data->total_mom_data;
        $total_approved_after_check_mom_data     = $data->total_approved_after_check_mom_data;
        $total_reject_after_check_mom_data       = $data->total_reject_after_check_mom_data;
        $total_NO_RP_after_check_mom_data        = $data->total_NO_RP_after_check_mom_data;
        $total_pending_for_check_mom_data        = $data->total_pending_for_check_mom_data;
        ?>

        <!-- Main content -->
        <section class="content">
          <div class="container-fluid">
            <!-- Total Plan Meetings Group -->
            <div class="card-group">
              <div class="card-group-title">Plan Meetings</div>
              <div class="row">
                <div class="col-md-4">
                  <div class="frame-layer-1">
                    <div class="frame-layer-2">
                      <div class="frame-layer-3">
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
                      <div class="frame-layer-3">
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


               <!-- RP Meetings Group -->
               <div class="card-group">
              <div class="card-group-title">RP / NO RP / Only Got Details Meetings</div>
              <div class="row">
                <div class="col-md-4">
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
                <div class="col-md-4">
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
                <div class="col-md-4">
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



              <!-- RP Meetings Group -->
               <div class="card-group">
              <div class="card-group-title">RP Fresh / Re Meetings Details</div>
              <div class="row">
              <div class="col-md-6">
                  <div class="frame-layer-1">
                    <div class="frame-layer-2">
                      <div class="frame-layer-3">
                        <div class="card text-center card-bg-13">
                          <div class="card-cotent-data">
                            <h4>Total Fresh RP Meetings</h4>
                            <p>New meetings conducted.</p>
                            <h5>
                              <a target="_BLANK" class="card-count-heading" href="<?=base_url()?>Menu/MeetingsDatas/total_fresh_meetings/<?=$uid?>/<?=$sdate?>/<?=$edate?>"><?= $total_fresh_meetings ?></a>
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
                        <div class="card text-center card-bg-14">
                          <div class="card-cotent-data">
                            <h4>Total RP Re Meetings</h4>
                            <p>Follow-up meetings conducted.</p>
                            <h5>
                              <a target="_BLANK" class="card-count-heading" href="<?=base_url()?>Menu/MeetingsDatas/total_re_meetings/<?=$uid?>/<?=$sdate?>/<?=$edate?>"><?= $total_re_meetings ?></a>
                            </h5>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
               
              </div>
            </div>



            <!-- Scheduled Meetings Group -->
            <div class="card-group">
              <div class="card-group-title">Total Scheduled Meetings</div>
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

              <!-- Scheduled RP Meetings Group -->
              <div class="card-group">
              <div class="card-group-title"> RP / NO RP / Only Got Details (Scheduled Meetings) </div>
              <div class="row">
                <div class="col-md-4">
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
                <div class="col-md-4">
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
                <div class="col-md-4">
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

            <!-- Barge Meetings Group -->
            <div class="card-group">
              <div class="card-group-title">Total Barge Meetings</div>
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

            <?php /*
            <!-- Join Meetings Group -->
            <div class="card-group">
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
            <div class="card-group">
              <div class="card-group-title">RP / NO RP / Only Got Details (Barge Meetings)</div>
              <div class="row">
                <div class="col-md-4">
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
                <div class="col-md-4">
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
                <div class="col-md-4">
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

            <!-- Meeting Types Group -->
            <div class="card-group">
              <div class="card-group-title">Meeting Types</div>
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

            <!-- Additional Meeting Types Group -->
            <div class="card-group">
              <div class="card-group-title">Additional Meeting Types</div>
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

            <!-- More Meeting Types Group -->
            <div class="card-group">
              <div class="card-group-title">More Meeting Types</div>
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

            <!-- Final Meeting Types Group -->
            <div class="card-group">
              <div class="card-group-title">Write MOM OF Meeting Types</div>
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

            <!-- MOM Data Group -->
            <div class="card-group">
              <div class="card-group-title">MOM Data</div>
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

            <!-- Final MOM Data Group -->
            <div class="card-group">
              <div class="card-group-title">Final MOM Data</div>
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

            <hr>
            <div class="body-content">
              <div class="page-header">
                <div class="table-responsive text-nowrap">
                  <table id="example1" class="table table-striped table-bordered" cellspacing="0" width="100%">
                  <thead class="thead-dark">
                <tr>
                    <th>Sr.No</th>
                    <th>Name</th>
                    <th>Total Plan Meetings</th>
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
                        <a target="_BLANK" class="card-count-heading-new" style="color:<?=$backgroundColorNew;?>!important" href="<?=base_url()?>Menu/MeetingsDatas/total_plan_meetings/<?=$meeting_user_id?>/<?=$sdate?>/<?=$edate?>">
                         <?= $row->total_plan_meetings; ?>
                        </a>
                      </td>
                      <td>
                        <a target="_BLANK" class="card-count-heading-new" style="color:<?=$backgroundColorNew;?>!important" href="<?=base_url()?>Menu/MeetingsDatas/complete_meetings/<?=$meeting_user_id?>/<?=$sdate?>/<?=$edate?>">
                         <?= $row->complete_meetings; ?>
                        </a>
                      </td>
                      <td>
                        <a target="_BLANK" class="card-count-heading-new" style="color:<?=$backgroundColorNew;?>!important" href="<?=base_url()?>Menu/MeetingsDatas/not_complete_meetings/<?=$meeting_user_id?>/<?=$sdate?>/<?=$edate?>">
                         <?= $row->not_complete_meetings; ?>
                        </a>
                      </td>
                      <td>
                        <a target="_BLANK" class="card-count-heading-new" style="color:<?=$backgroundColorNew;?>!important" href="<?=base_url()?>Menu/MeetingsDatas/total_scheduled_meetings/<?=$meeting_user_id?>/<?=$sdate?>/<?=$edate?>">
                         <?= $row->total_sheduled_meetings; ?>
                        </a>
                      </td>
                      <td>
                        <a target="_BLANK" class="card-count-heading-new" style="color:<?=$backgroundColorNew;?>!important" href="<?=base_url()?>Menu/MeetingsDatas/total_complete_scheduled_meetings/<?=$meeting_user_id?>/<?=$sdate?>/<?=$edate?>">
                         <?= $row->total_complete_sheduled_meetings; ?>
                        </a>
                      </td>
                      <td>
                        <a target="_BLANK" class="card-count-heading-new" style="color:<?=$backgroundColorNew;?>!important" href="<?=base_url()?>Menu/MeetingsDatas/not_complete_scheduled_meetings/<?=$meeting_user_id?>/<?=$sdate?>/<?=$edate?>">
                         <?= $row->not_complete_sheduled_meetings; ?>
                        </a>
                      </td>
                      <td>
                        <a target="_BLANK" class="card-count-heading-new" style="color:<?=$backgroundColorNew;?>!important" href="<?=base_url()?>Menu/MeetingsDatas/total_barg_meetings/<?=$meeting_user_id?>/<?=$sdate?>/<?=$edate?>">
                         <?= $row->total_barg_meetings; ?>
                        </a>
                      </td>
                      <td>
                        <a target="_BLANK" class="card-count-heading-new" style="color:<?=$backgroundColorNew;?>!important" href="<?=base_url()?>Menu/MeetingsDatas/total_complete_barg_meetings/<?=$meeting_user_id?>/<?=$sdate?>/<?=$edate?>">
                         <?= $row->total_complete_barg_meetings; ?>
                        </a>
                      </td>
                      <td>
                        <a target="_BLANK" class="card-count-heading-new" style="color:<?=$backgroundColorNew;?>!important" href="<?=base_url()?>Menu/MeetingsDatas/not_complete_barg_meetings/<?=$meeting_user_id?>/<?=$sdate?>/<?=$edate?>">
                         <?= $row->not_complete_barg_meetings; ?>
                        </a>
                      </td>
                     
                      <?php /*
                      <td><?php echo htmlspecialchars($row->total_join_meetings); ?></td>
                      <td><?php echo htmlspecialchars($row->total_complete_join_meetings); ?></td>
                      <td><?php echo htmlspecialchars($row->not_complete_join_meetings); ?></td>
                      */ ?>

                      <td>
                        <a target="_BLANK" class="card-count-heading-new" style="color:<?=$backgroundColorNew;?>!important" href="<?=base_url()?>Menu/MeetingsDatas/total_RP_meetings/<?=$meeting_user_id?>/<?=$sdate?>/<?=$edate?>">
                         <?= $row->total_RP_meetings; ?>
                        </a>
                      </td>
                      <td>
                        <a target="_BLANK" class="card-count-heading-new" style="color:<?=$backgroundColorNew;?>!important" href="<?=base_url()?>Menu/MeetingsDatas/total_NO_RP_meetings/<?=$meeting_user_id?>/<?=$sdate?>/<?=$edate?>">
                         <?= $row->total_NO_RP_meetings; ?>
                        </a>
                      </td>
                      <td>
                        <a target="_BLANK" class="card-count-heading-new" style="color:<?=$backgroundColorNew;?>!important" href="<?=base_url()?>Menu/MeetingsDatas/total_Only_Got_details_meetings/<?=$meeting_user_id?>/<?=$sdate?>/<?=$edate?>">
                         <?= $row->total_Only_Got_details_meetings; ?>
                        </a>
                      </td>
                      <td>
                        <a target="_BLANK" class="card-count-heading-new" style="color:<?=$backgroundColorNew;?>!important" href="<?=base_url()?>Menu/MeetingsDatas/total_Scheduled_RP_meetings/<?=$meeting_user_id?>/<?=$sdate?>/<?=$edate?>">
                         <?= $row->total_Sheduled_RP_meetings; ?>
                        </a>
                      </td>
                      <td>
                        <a target="_BLANK" class="card-count-heading-new" style="color:<?=$backgroundColorNew;?>!important" href="<?=base_url()?>Menu/MeetingsDatas/total_Scheduled_NO_RP_meetings/<?=$meeting_user_id?>/<?=$sdate?>/<?=$edate?>">
                         <?= $row->total_Sheduled_NO_RP_meetings; ?>
                        </a>
                      </td>
                      <td>
                        <a target="_BLANK" class="card-count-heading-new" style="color:<?=$backgroundColorNew;?>!important" href="<?=base_url()?>Menu/MeetingsDatas/total_Scheduled_Only_Got_details_meetings/<?=$meeting_user_id?>/<?=$sdate?>/<?=$edate?>">
                         <?= $row->total_Scheduled_Only_Got_details_meetings; ?>
                        </a>
                      </td>
                      <td>
                        <a target="_BLANK" class="card-count-heading-new" style="color:<?=$backgroundColorNew;?>!important" href="<?=base_url()?>Menu/MeetingsDatas/total_Barge_RP_meetings/<?=$meeting_user_id?>/<?=$sdate?>/<?=$edate?>">
                         <?= $row->total_Barge_RP_meetings; ?>
                        </a>
                      </td>
                      <td>
                        <a target="_BLANK" class="card-count-heading-new" style="color:<?=$backgroundColorNew;?>!important" href="<?=base_url()?>Menu/MeetingsDatas/total_Barge_NO_RP_meetings/<?=$meeting_user_id?>/<?=$sdate?>/<?=$edate?>">
                         <?= $row->total_Barge_NO_RP_meetings; ?>
                        </a>
                      </td>
                      <td>
                        <a target="_BLANK" class="card-count-heading-new" style="color:<?=$backgroundColorNew;?>!important" href="<?=base_url()?>Menu/MeetingsDatas/total_Barge_Only_Got_details_meetings/<?=$meeting_user_id?>/<?=$sdate?>/<?=$edate?>">
                         <?= $row->total_Barge_Only_Got_details_meetings; ?>
                        </a>
                      </td>
                      <td>
                        <a target="_BLANK" class="card-count-heading-new" style="color:<?=$backgroundColorNew;?>!important" href="<?=base_url()?>Menu/MeetingsDatas/total_potential_meetings/<?=$meeting_user_id?>/<?=$sdate?>/<?=$edate?>">
                         <?= $row->total_potential_meetings; ?>
                        </a>
                      </td>
                      <td>
                        <a target="_BLANK" class="card-count-heading-new" style="color:<?=$backgroundColorNew;?>!important" href="<?=base_url()?>Menu/MeetingsDatas/total_non_potential_meetings/<?=$meeting_user_id?>/<?=$sdate?>/<?=$edate?>">
                         <?= $row->total_non_potential_meetings; ?>
                        </a>
                      </td>
                      <td>
                        <a target="_BLANK" class="card-count-heading-new" style="color:<?=$backgroundColorNew;?>!important" href="<?=base_url()?>Menu/MeetingsDatas/total_topspender_meetings/<?=$meeting_user_id?>/<?=$sdate?>/<?=$edate?>">
                         <?= $row->total_topspender_meetings; ?>
                        </a>
                      </td>
                      <td>
                        <a target="_BLANK" class="card-count-heading-new" style="color:<?=$backgroundColorNew;?>!important" href="<?=base_url()?>Menu/MeetingsDatas/total_upsell_client_meetings/<?=$meeting_user_id?>/<?=$sdate?>/<?=$edate?>">
                         <?= $row->total_upsell_client_meetings; ?>
                        </a>
                      </td>
                      <td>
                        <a target="_BLANK" class="card-count-heading-new" style="color:<?=$backgroundColorNew;?>!important" href="<?=base_url()?>Menu/MeetingsDatas/total_focus_funnel_meetings/<?=$meeting_user_id?>/<?=$sdate?>/<?=$edate?>">
                         <?= $row->total_focus_funnel_meetings; ?>
                        </a>
                      </td>
                      <td>
                        <a target="_BLANK" class="card-count-heading-new" style="color:<?=$backgroundColorNew;?>!important" href="<?=base_url()?>Menu/MeetingsDatas/total_keycompany_meetings/<?=$meeting_user_id?>/<?=$sdate?>/<?=$edate?>">
                         <?= $row->total_keycompany_meetings; ?>
                        </a>
                      </td>
                      <td>
                        <a target="_BLANK" class="card-count-heading-new" style="color:<?=$backgroundColorNew;?>!important" href="<?=base_url()?>Menu/MeetingsDatas/total_pkclient_meetings/<?=$meeting_user_id?>/<?=$sdate?>/<?=$edate?>">
                         <?= $row->total_pkclient_meetings; ?>
                        </a>
                      </td>
                      <td>
                        <a target="_BLANK" class="card-count-heading-new" style="color:<?=$backgroundColorNew;?>!important" href="<?=base_url()?>Menu/MeetingsDatas/total_priorityc_meetings/<?=$meeting_user_id?>/<?=$sdate?>/<?=$edate?>">
                         <?= $row->total_priorityc_meetings; ?>
                        </a>
                      </td>
                      <td>
                        <a target="_BLANK" class="card-count-heading-new" style="color:<?=$backgroundColorNew;?>!important" href="<?=base_url()?>Menu/MeetingsDatas/total_fresh_meetings/<?=$meeting_user_id?>/<?=$sdate?>/<?=$edate?>">
                         <?= $row->total_fresh_meetings; ?>
                        </a>
                      </td>
                      <td>
                        <a target="_BLANK" class="card-count-heading-new" style="color:<?=$backgroundColorNew;?>!important" href="<?=base_url()?>Menu/MeetingsDatas/total_re_meetings/<?=$meeting_user_id?>/<?=$sdate?>/<?=$edate?>">
                         <?= $row->total_re_meetings; ?>
                        </a>
                      </td>
                      <td>
                        <a target="_BLANK" class="card-count-heading-new" style="color:<?=$backgroundColorNew;?>!important" href="<?=base_url()?>Menu/MeetingsDatas/total_write_mom_rp_meetings/<?=$meeting_user_id?>/<?=$sdate?>/<?=$edate?>">
                         <?= $row->total_write_mom_rp_meetings; ?>
                        </a>
                      </td>
                      <td>
                        <a target="_BLANK" class="card-count-heading-new" style="color:<?=$backgroundColorNew;?>!important" href="<?=base_url()?>Menu/MeetingsDatas/total_pending_for_write_mom_rp_meeting/<?=$meeting_user_id?>/<?=$sdate?>/<?=$edate?>">
                         <?= $row->total_pending_for_write_mom_rp_meeting; ?>
                        </a>
                      </td>
                      <td>
                        <a target="_BLANK" class="card-count-heading-new" style="color:<?=$backgroundColorNew;?>!important" href="<?=base_url()?>Menu/MeetingsDatas/total_mom_data/<?=$meeting_user_id?>/<?=$sdate?>/<?=$edate?>">
                         <?= $row->total_mom_data; ?>
                        </a>
                      </td>
                      <td>
                        <a target="_BLANK" class="card-count-heading-new" style="color:<?=$backgroundColorNew;?>!important" href="<?=base_url()?>Menu/MeetingsDatas/total_approved_after_check_mom_data/<?=$meeting_user_id?>/<?=$sdate?>/<?=$edate?>">
                         <?= $row->total_approved_after_check_mom_data; ?>
                        </a>
                      </td>
                      <td>
                        <a target="_BLANK" class="card-count-heading-new" style="color:<?=$backgroundColorNew;?>!important" href="<?=base_url()?>Menu/MeetingsDatas/total_reject_after_check_mom_data/<?=$meeting_user_id?>/<?=$sdate?>/<?=$edate?>">
                         <?= $row->total_reject_after_check_mom_data; ?>
                        </a>
                      </td>
                      <td>
                        <a target="_BLANK" class="card-count-heading-new" style="color:<?=$backgroundColorNew;?>!important" href="<?=base_url()?>Menu/MeetingsDatas/total_NO_RP_after_check_mom_data/<?=$meeting_user_id?>/<?=$sdate?>/<?=$edate?>">
                         <?= $row->total_NO_RP_after_check_mom_data; ?>
                        </a>
                      </td>
                      <td>
                        <a target="_BLANK" class="card-count-heading-new" style="color:<?=$backgroundColorNew;?>!important" href="<?=base_url()?>Menu/MeetingsDatas/total_pending_for_check_mom_data/<?=$meeting_user_id?>/<?=$sdate?>/<?=$edate?>">
                         <?= $row->total_pending_for_check_mom_data; ?>
                        </a>
                      </td>
                  </tr>
               <?php $i++; } ?>
            </tbody>
                  </table>
                </div>
                <hr />
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

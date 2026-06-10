<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Task On Self Or Other Funnel Details | STEM APP | WebAPP</title>
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

    <link rel="stylesheet" href="https://netdna.bootstrapcdn.com/font-awesome/4.0.3/css/font-awesome.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/animatecss/3.5.2/animate.min.css">
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

        .proposal {
            background-color: #fff;
            margin: 20px 0;
            padding: 20px;
            border-radius: 8px;
            background: linear-gradient(to right, #ede7f6, #ffffff); border-radius: 12px; box-shadow: 0 4px 8px rgba(0,0,0,0.1); padding: 20px;
            text-align: center;
        }
        .proposal h2 {
            margin-top: 0;
            color: #ec5d9e;
            text-align: center;
            padding-bottom: 20px;
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
                    <div class="frame-layer-3" style="background: linear-gradient(to right, #ede7f6, #ffffff); border-radius: 12px; box-shadow: 0 4px 8px rgba(0,0,0,0.1);">

                    <br/>
                      <h1 class="m-0 premium-color-1" style="color: #ff009b;">Proposal Details</h1>
                      <hr>
                      <center>
                        <p style="width: 70%" class="premium-color-2" style="color: #ff0000;">The Proposal Details section provides a comprehensive overview of the proposals shared with clients or stakeholders. It typically includes essential information such as proposal date, client name, services or products offered, pricing, terms, and any custom notes or conditions. This helps track proposal history and client engagement effectively.</p>
                      </center>
                      <p class="premium-color-2" style="color: #2c00ee;"><strong><mark><?=$sdate ?> To <?=$edate ?></mark></strong></p>
                      <p class="premium-color-2" style="color: #2c00ee;"><strong>Task : <mark><?=$taskActionDatas ?></mark></strong></p>
                      <br/>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <br>
            <div class="row mb-2">
              <hr>
              <div class="text-right-data text-center" style="background: linear-gradient(to right, #b2ffbf, #ffffff); border-radius: 12px; box-shadow: 0 4px 8px rgba(0,0,0,0.1); padding: 20px;">
                <?php 
                  $clusterPstDatas  = $this->Menu_model->GetAdminActiveTeam($user['user_id']);
                  $taskActions      = $this->Menu_model->get_action();
                  $userRoles        = $this->Menu_model->GetActiveDepartmentList($uid);
                  ?>
                <div class="col text-center formcenterData">
                  <div>
                    <hr>
                    <form action="<?=base_url()?>Reports/ProposalDetails" method="post" class="mt-3">
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
                            <?php foreach ($taskActions as $taskAction): ?>
                            <?php 
                              if($taskAction->action_status == 0 || $taskAction->id !=7){
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
            
             $totalProposalTasks                            = $getTotalTasks['totalProposalTasks'];
             $totalProposalTasks                            = $totalProposalTasks[0];

            
            $total_proposal_task                            = $totalProposalTasks->total_proposal_task;
            $complete_proposal_task                         = $totalProposalTasks->complete_proposal_task;
            $pending_proposal_task                          = $totalProposalTasks->pending_proposal_task;

            $proposal_approved                              = $totalProposalTasks->proposal_approved;
            $proposal_reject                                = $totalProposalTasks->proposal_reject;
            $pending_for_approved                           = $totalProposalTasks->pending_for_approved;

            $total_proposal_approved_budget                 = $totalProposalTasks->total_proposal_approved_budget;
            $total_proposal_pending_budget                  = $totalProposalTasks->total_proposal_pending_budget;
            $total_proposal_reject_budget                   = $totalProposalTasks->total_proposal_reject_budget;

            $open                                           = $totalProposalTasks->open;
            $reachout                                       = $totalProposalTasks->reachout;
            $tentative                                      = $totalProposalTasks->tentative;
            $will_do_later                                  = $totalProposalTasks->will_do_later;
            $not_interested                                 = $totalProposalTasks->not_interested;
            $positive                                       = $totalProposalTasks->positive;
            $closure                                        = $totalProposalTasks->closure;
            $open_rpem                                      = $totalProposalTasks->open_rpem;
            $very_positive                                  = $totalProposalTasks->very_positive;
            $ttd_reachout                                   = $totalProposalTasks->ttd_reachout;
            $wno_reachout                                   = $totalProposalTasks->wno_reachout;
            $positive_nap                                   = $totalProposalTasks->positive_nap;
            $very_positive_nap                              = $totalProposalTasks->very_positive_nap;
            $on_boarded                                     = $totalProposalTasks->on_boarded;

            $corporate                                      = $totalProposalTasks->corporate;
            $psu                                            = $totalProposalTasks->PSU;
            $ngo                                            = $totalProposalTasks->NGO;
            $private_School                                 = $totalProposalTasks->Private_School;
            $individual                                     = $totalProposalTasks->Individual;
            $govt_Off                                       = $totalProposalTasks->Govt_Off;
            $other                                          = $totalProposalTasks->Other;
            $Online                                         = $totalProposalTasks->Online;
            $sTEM_School                                    = $totalProposalTasks->STEM_School;
            $foundation                                     = $totalProposalTasks->Foundation;
            $mnc                                            = $totalProposalTasks->MNC;
            $hO_Leads                                       = $totalProposalTasks->HO_Leads;
            $dMF                                            = $totalProposalTasks->DMF;
            $elected_Representatives                        = $totalProposalTasks->Elected_Representatives;


            $potential                                      = $totalProposalTasks->Potential;
            $topspender                                     = $totalProposalTasks->Top_Spender;
            $upsell_client                                  = $totalProposalTasks->Upsell_Client;
            $focus_funnel                                   = $totalProposalTasks->Focus_Funnel;
            $keycompany                                     = $totalProposalTasks->Key_Client;
            $pkclient                                       = $totalProposalTasks->Positive_Key_Client;
            $priorityc                                      = $totalProposalTasks->Priority_Calling;

            $proposalFunnelArray = [
                'Potential'     => $totalProposalTasks->Potential,
                'Top_Spender'    => $totalProposalTasks->Top_Spender,
                'Upsell_Client' => $totalProposalTasks->Upsell_Client,
                'Focus_Funnel'  => $totalProposalTasks->Focus_Funnel,
                'Key_Client'    => $totalProposalTasks->Key_Client,
                'Positive_Key_Client'  => $totalProposalTasks->Positive_Key_Client,
                'Priority_Calling'     => $totalProposalTasks->Priority_Calling,
            ];


            $total_proposal_in_q1_twetenty_closure_funnel    = $totalProposalTasks->total_proposal_in_q1_twetenty_closure_funnel;
            $total_proposal_in_potential_funnel_for_fy       = $totalProposalTasks->total_proposal_in_potential_funnel_for_fy;
            $total_proposal_in_to_be_nurtured_for_fy         = $totalProposalTasks->total_proposal_in_to_be_nurtured_for_fy;
            $total_proposal_in_fifity_new_lead_funnel        = $totalProposalTasks->total_company_in_fifity_new_lead_funnel;

         



            $total_in_MSC                                   = $totalProposalTasks->total_in_MSC;
            $total_in_BALA                                  =  $totalProposalTasks->total_in_BALA;
            $total_in_Astronomy                             = $totalProposalTasks->total_in_Astronomy;
            $total_in_Tinkering                             = $totalProposalTasks->total_in_Tinkering;
            $total_in_Big_Laboratory                        = $totalProposalTasks->total_in_Big_Laboratory;
            $total_in_Small_Laboratory                      = $totalProposalTasks->total_in_Small_Laboratory;
            $total_in_Customize                             = $totalProposalTasks->total_in_Customize;
            $total_in_Smart_Class                           = $totalProposalTasks->total_in_Smart_Class;

          
            $total_Reject_in_MSC                            = $totalProposalTasks->total_Reject_in_MSC;
            $total_Reject_in_BALA                           = $totalProposalTasks->total_Reject_in_BALA;
            $total_Reject_in_Astronomy                      = $totalProposalTasks->total_Reject_in_Astronomy;
            $total_Reject_in_Tinkering                      = $totalProposalTasks->total_Reject_in_Tinkering;
            $total_Reject_in_Big_Laboratory                 = $totalProposalTasks->total_Reject_in_Big_Laboratory;
            $total_Reject_in_Small_Laboratory               = $totalProposalTasks->total_Reject_in_Small_Laboratory;
            $total_Reject_in_Customize                      = $totalProposalTasks->total_Reject_in_Customize;
            $total_Reject_in_Smart_Class                    = $totalProposalTasks->total_Reject_in_Smart_Class;


         


            $total_Pending_For_Approve_in_MSC              = $totalProposalTasks->total_Pending_For_Approve_in_MSC;
            $total_Pending_For_Approve_in_BALA             = $totalProposalTasks->total_Pending_For_Approve_in_BALA;
            $total_Pending_For_Approve_in_Astronomy        = $totalProposalTasks->total_Pending_For_Approve_in_Astronomy;
            $total_Pending_For_Approve_in_Tinkering        = $totalProposalTasks->total_Pending_For_Approve_in_Tinkering;
            $total_Pending_For_Approve_in_Big_Laboratory   = $totalProposalTasks->total_Pending_For_Approve_in_Big_Laboratory;
            $total_Pending_For_Approve_in_Small_Laboratory = $totalProposalTasks->total_Pending_For_Approve_in_Small_Laboratory;
            $total_Pending_For_Approve_in_Customize        = $totalProposalTasks->total_Pending_For_Approve_in_Customize;
            $total_Pending_For_Approve_in_Smart_Class      = $totalProposalTasks->total_Pending_For_Approve_in_Smart_Class;

            $proposalTaskSummary = [
                'total_proposal_task'      => $totalProposalTasks->total_proposal_task,
                'complete_proposal_task'   => $totalProposalTasks->complete_proposal_task,
                'pending_proposal_task'    => $totalProposalTasks->pending_proposal_task,
            ];


            $proposalSummary = [
                'complete_proposal_task' => $totalProposalTasks->complete_proposal_task,
                'proposal_approved'      => $totalProposalTasks->proposal_approved,
                'proposal_reject'        => $totalProposalTasks->proposal_reject,
                'pending_for_approved'   => $totalProposalTasks->pending_for_approved,
            ];



            $proposalPbudgetmeSummary = [
                  'total_proposal_approved_budget'    => $totalProposalTasks->total_proposal_approved_budget,
                  'total_proposal_pending_budget'     => $totalProposalTasks->total_proposal_pending_budget,
                  'total_proposal_reject_budget'      => $totalProposalTasks->total_proposal_reject_budget,
              ];


              $proposalStatus = [
                  'open'                  => $totalProposalTasks->open,
                  'reachout'              => $totalProposalTasks->reachout,
                  'tentative'             => $totalProposalTasks->tentative,
                  'will_do_later'         => $totalProposalTasks->will_do_later,
                  'not_interested'        => $totalProposalTasks->not_interested,
                  'positive'              => $totalProposalTasks->positive,
                  'closure'               => $totalProposalTasks->closure,
                  'open_rpem'             => $totalProposalTasks->open_rpem,
                  'very_positive'         => $totalProposalTasks->very_positive,
                  'ttd_reachout'          => $totalProposalTasks->ttd_reachout,
                  'wno_reachout'          => $totalProposalTasks->wno_reachout,
                  'positive_nap'          => $totalProposalTasks->positive_nap,
                  'very_positive_nap'     => $totalProposalTasks->very_positive_nap,
                  'on_boarded'            => $totalProposalTasks->on_boarded
              ];



            
          $proposalSources = [
              'corporate'              => $totalProposalTasks->corporate,
              'psu'                    => $totalProposalTasks->PSU,
              'ngo'                    => $totalProposalTasks->NGO,
              'private_school'         => $totalProposalTasks->Private_School,
              'individual'             => $totalProposalTasks->Individual,
              'govt_off'               => $totalProposalTasks->Govt_Off,
              'other'                  => $totalProposalTasks->Other,
              'online'                 => $totalProposalTasks->Online,
              'stem_school'            => $totalProposalTasks->STEM_School,
              'foundation'             => $totalProposalTasks->Foundation,
              'mnc'                    => $totalProposalTasks->MNC,
              'ho_leads'               => $totalProposalTasks->HO_Leads,
              'dmf'                    => $totalProposalTasks->DMF,
              'elected_representatives'=> $totalProposalTasks->Elected_Representatives,
          ];




          $totalProposalFunnelArray = [
              'total_proposal_in_q1_twetenty_closure_funnel'    => $totalProposalTasks->total_proposal_in_q1_twetenty_closure_funnel,
              'total_proposal_in_potential_funnel_for_fy'       => $totalProposalTasks->total_proposal_in_potential_funnel_for_fy,
              'total_proposal_in_to_be_nurtured_for_fy'         => $totalProposalTasks->total_proposal_in_to_be_nurtured_for_fy,
              'total_proposal_in_fifity_new_lead_funnel'        => $totalProposalTasks->total_proposal_in_fifity_new_lead_funnel
          ];


            $totalProposalInArray = [
                'total_in_MSC'            => $totalProposalTasks->total_in_MSC,
                'total_in_BALA'           => $totalProposalTasks->total_in_BALA,
                'total_in_Astronomy'      => $totalProposalTasks->total_in_Astronomy,
                'total_in_Tinkering'      => $totalProposalTasks->total_in_Tinkering,
                'total_in_Big_Laboratory' => $totalProposalTasks->total_in_Big_Laboratory,
                'total_in_Small_Laboratory' => $totalProposalTasks->total_in_Small_Laboratory,
                'total_in_Customize'      => $totalProposalTasks->total_in_Customize,
                'total_in_Smart_Class'    => $totalProposalTasks->total_in_Smart_Class,
            ];

            $totalApprovals = [
                    'total_Approved_in_MSC'               => $totalProposalTasks->total_Approved_in_MSC,
                    'total_Approved_in_BALA'              => $totalProposalTasks->total_Approved_in_BALA,
                    'total_Approved_in_Astronomy'         => $totalProposalTasks->total_Approved_in_Astronomy,
                    'total_Approved_in_Tinkering'         => $totalProposalTasks->total_Approved_in_Tinkering,
                    'total_Approved_in_Big_Laboratory'    => $totalProposalTasks->total_Approved_in_Big_Laboratory,
                    'total_Approved_in_Small_Laboratory'  => $totalProposalTasks->total_Approved_in_Small_Laboratory,
                    'total_Approved_in_Customize'         => $totalProposalTasks->total_Approved_in_Customize,
                    'total_Approved_in_Smart_Class'       => $totalProposalTasks->total_Approved_in_Smart_Class,
                ];


            $totalRejects = [
              'total_Reject_in_MSC'               => $total_Reject_in_MSC,
              'total_Reject_in_BALA'              => $total_Reject_in_BALA,
              'total_Reject_in_Astronomy'         => $total_Reject_in_Astronomy,
              'total_Reject_in_Tinkering'         => $total_Reject_in_Tinkering,
              'total_Reject_in_Big_Laboratory'    => $total_Reject_in_Big_Laboratory,
              'total_Reject_in_Small_Laboratory'  => $total_Reject_in_Small_Laboratory,
              'total_Reject_in_Customize'         => $total_Reject_in_Customize,
              'total_Reject_in_Smart_Class'       => $total_Reject_in_Smart_Class
          ];

            $totalPendingForApproved = [
                  'total_Pending_For_Approve_in_MSC'              => $total_Pending_For_Approve_in_MSC,
                  'total_Pending_For_Approve_in_BALA'             => $total_Pending_For_Approve_in_BALA,
                  'total_Pending_For_Approve_in_Astronomy'        => $total_Pending_For_Approve_in_Astronomy,
                  'total_Pending_For_Approve_in_Tinkering'        => $total_Pending_For_Approve_in_Tinkering,
                  'total_Pending_For_Approve_in_Big_Laboratory'   => $total_Pending_For_Approve_in_Big_Laboratory,
                  'total_Pending_For_Approve_in_Small_Laboratory' => $total_Pending_For_Approve_in_Small_Laboratory,
                  'total_Pending_For_Approve_in_Customize'        => $total_Pending_For_Approve_in_Customize,
                  'total_Pending_For_Approve_in_Smart_Class'      => $total_Pending_For_Approve_in_Smart_Class
              ];
              

              $total_company_task                                  = $totalProposalTasks->total_company_task;
              $total_company_complete_proposal_task                = $totalProposalTasks->total_company_complete_proposal_task;
              $total_company_pending_proposal_task                 = $totalProposalTasks->total_company_pending_proposal_task;

              $total_company_proposal_approved                     = $totalProposalTasks->total_company_proposal_approved;
              $total_company_proposal_reject                       = $totalProposalTasks->total_company_proposal_reject;
              $total_company_pending_for_approved                  = $totalProposalTasks->total_company_pending_for_approved;

              ?>






 <div class="text-center " style="background: linear-gradient(to right, #ede7f6, #ffffff); border-radius: 12px; box-shadow: 0 4px 8px rgba(0,0,0,0.1); padding: 20px;">
              <h5><i>Proposal Task Summary Overview</i></h5> <hr>
              <small>This summary provides a clear snapshot of the overall progress of proposal-related tasks within the organization. It captures three essential components: the total number of proposal tasks assigned, how many of those have been successfully completed, and how many remain pending. By breaking down the tasks into these categories, the summary enables quick performance assessment and highlights areas that may require attention or follow-up. It is an effective tool for project managers and teams to monitor workload distribution, track efficiency, and ensure that proposal timelines are met without delays. This overview supports data-driven decision-making and better resource planning.</small>
            </div>
            <hr>
            <div class="row">
              <?php  
             $descriptions = [
                          'total_proposal_task' => 'Total number of proposal-related tasks.',
                          'complete_proposal_task' => 'Proposal tasks that have been completed.',
                          'pending_proposal_task' => 'Proposal tasks that are still pending completion.'
                      ];

                $i=1;
                foreach ($proposalTaskSummary as $key =>  $value) {
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

                      if($key == 'total_proposal_task'){
                            $companyCountDatas = $total_company_task;
                      }else  if($key == 'complete_proposal_task'){
                            $companyCountDatas = $total_company_complete_proposal_task;
                      }else  if($key == 'pending_proposal_task'){
                            $companyCountDatas = $total_company_pending_proposal_task;
                      }

                      ?>
              <div class="col-md-4">
                <div class="card text-center shadow span3 wow bounceInUp center" style="background-color: <?= $backgroundColor ?>; color: <?= $backgroundColorNew ?>;">
                  <div class="card-body" style="color:<?=$backgroundColorNew;?>!important">
                    <h5 class="card-title"><b><?= $heading ?></b></h5>
                    <small><?= $description ?></small> 
                    <small>Total Company : <?= $companyCountDatas ?></small> 
                    <hr>
                    <a target="_BLANK" class="card-count-heading-new" style="color:<?=$backgroundColorNew;?>!important" href="<?=base_url()?>Reports/ProposalDetailsData/<?=$key?>/<?=$uid?>/<?=$sdate?>/<?=$edate?>/<?=$task_action_id?>">
                      <h3 class="card-text"><?= $value ?></h3>
                    </a>
                  </div>
                </div>
              </div>
              <?php
                }
                ?>
            </div>
            <hr>





  <div class="text-center " style="background: linear-gradient(to right, #ede7f6, #ffffff); border-radius: 12px; box-shadow: 0 4px 8px rgba(0,0,0,0.1); padding: 20px;">
              <h5><i>Proposal Summary Overview</i></h5> <hr>
              <small>This summary provides a concise overview of the current status of all proposals reviewed. It highlights the total number of proposals that have been approved, those that have been rejected, and those still awaiting approval. The data serves as a valuable performance indicator for project planning and decision-making processes. By tracking these categories, stakeholders can quickly assess progress, identify bottlenecks, and allocate resources more effectively. Understanding the distribution across approved, rejected, and pending proposals enables better forecasting and supports timely follow-ups. This summary ensures transparency and supports continuous improvement in proposal evaluation and execution workflows.</small>
            </div>
            <hr>
            <div class="row">
              <?php  
             $descriptions = [
                'complete_proposal_task' => 'Total number of submit proposals.',
                'proposal_approved' => 'Total number of proposals that have been approved.',
                'proposal_reject' => 'Total number of proposals that have been rejected.',
                'pending_for_approved' => 'Total number of proposals currently awaiting approval.'
              ];


                foreach ($proposalSummary as $key =>  $value) {
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


                      if($key == 'complete_proposal_task'){
                            $companyCountDatas = $total_company_complete_proposal_task;
                      }else if($key == 'proposal_approved'){
                            $companyCountDatas = $total_company_proposal_approved;
                      }else if($key == 'proposal_reject'){
                            $companyCountDatas = $total_company_proposal_reject;
                      }else if($key == 'pending_for_approved'){
                            $companyCountDatas = $total_company_pending_for_approved;
                      }

                      ?>
              <div class="col-md-3">
                <div class="card text-center shadow span3 wow bounceInUp center" style="background-color: <?= $backgroundColor ?>; color: <?= $backgroundColorNew ?>;visibility: visible;-webkit-animation-delay: 0.3s; -moz-animation-delay: 0.3s; animation-delay: 0.3s;">
                  <div class="card-body" style="color:<?=$backgroundColorNew;?>!important">
                    <h5 class="card-title"><b><?= $heading ?></b></h5>
                    <small><?= $description ?></small> 
                    <small>Total Company : <?= $companyCountDatas ?></small> 
                    <hr>
                    <a target="_BLANK" class="card-count-heading-new" style="color:<?=$backgroundColorNew;?>!important" href="<?=base_url()?>Reports/ProposalDetailsData/<?=$key?>/<?=$uid?>/<?=$sdate?>/<?=$edate?>/<?=$task_action_id?>">
                      <h3 class="card-text"><?= $value ?></h3>
                    </a>
                  </div>
                </div>
              </div>
              <?php
                }
                ?>
            </div>
            <hr>











  <div class="text-center " style="background: linear-gradient(to right, #ede7f6, #ffffff); border-radius: 12px; box-shadow: 0 4px 8px rgba(0,0,0,0.1); padding: 20px;">
              <h5><i>Summary of Proposal Budget Approval Status</i></h5> <hr>
              <small>This summary provides a clear overview of the proposal budget approval process. It highlights the number of proposals that have successfully received budget approvals, those currently awaiting approval decisions, and those that were rejected during the evaluation. This information helps stakeholders quickly assess the overall progress and effectiveness of the budget evaluation process. By understanding the distribution between approved, pending, and rejected proposals, teams can identify potential bottlenecks, streamline follow-ups, and allocate resources more efficiently to improve turnaround time and ensure smooth financial planning and project execution.</small>
            </div>
            <hr>
            <div class="row">
              <?php  
              $descriptions = [
                  'total_proposal_approved_budget' => 'Total proposals approved budget.',
                  'total_proposal_pending_budget' => 'Proposals budget that are pending for approval.',
                  'total_proposal_reject_budget' => 'Proposals that have been rejected of budget.',
              ];

                foreach ($proposalPbudgetmeSummary as $key =>  $value) {
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
              <div class="col-md-4">
                <div class="card text-center shadow span3 wow bounceInUp center" style="background-color: <?= $backgroundColor ?>; color: <?= $backgroundColorNew ?>;">
                  <div class="card-body" style="color:<?=$backgroundColorNew;?>!important">
                    <h5 class="card-title"><b><?= $heading ?></b></h5>
                    <small><?= $description ?></small> 
                    <hr>
                    <a target="_BLANK" class="card-count-heading-new" style="color:<?=$backgroundColorNew;?>!important" href="<?=base_url()?>Reports/ProposalDetailsData/<?=$key?>/<?=$uid?>/<?=$sdate?>/<?=$edate?>/<?=$task_action_id?>">
                      <h3 class="card-text"><?= $value ?> ₹</h3>
                    </a>
                  </div>
                </div>
              </div>
              <?php
                }
                ?>
            </div>
            <hr>


      <div class="text-center " style="background: linear-gradient(to right, #ede7f6, #ffffff); border-radius: 12px; box-shadow: 0 4px 8px rgba(0,0,0,0.1); padding: 20px;">
              <h5><i>Approved Proposal Engagement Status Overview</i></h5> <hr>
              <small>This structured list represents the different engagement stages of proposals within a business development lifecycle. Each label denotes a unique interaction level or response from a prospective partner or client. These range from initial outreach and open opportunities to more committed statuses like "positive," "very positive," or "on boarded." Intermediate categories such as "tentative" or "will do later" help track potential delays or uncertainties. Special outreach methods like "ttd reachout" and "wno reachout" indicate tailored engagement strategies. Collectively, this classification enables teams to effectively monitor progress, prioritize actions, and understand where each proposal stands in the conversion journey.</small>
            </div>
            <hr>
            <div class="row">
              <?php  
              $descriptions = [
                        'open'                  => 'Proposals that are on open status.',
                        'reachout'              => 'Proposals that are on reachout status.',
                        'tentative'             => 'Proposals that are on tentative status.',
                        'will_do_later'         => 'Proposals that are on will do later status.',
                        'not_interested'        => 'Proposals that are on not interested status.',
                        'positive'              => 'Proposals that are on positive status.',
                        'closure'               => 'Proposals that are on closure status.',
                        'open_rpem'             => 'Proposals that are on open rpem status.',
                        'very_positive'         => 'Proposals that are on very positive status.',
                        'ttd_reachout'          => 'Proposals that are on ttd reachout status.',
                        'wno_reachout'          => 'Proposals that are on wno reachout status.',
                        'positive_nap'          => 'Proposals that are on positive nap status.',
                        'very_positive_nap'     => 'Proposals that are on very positive nap status.',
                        'on_boarded'            => 'Proposals that are on on boarded status.'
                    ];


                foreach ($proposalStatus   as $key =>  $value) {
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
              <div class="col-md-3">
                <div class="card text-center shadow span3 wow bounceInUp center" style="background-color: <?= $backgroundColor ?>; color: <?= $backgroundColorNew ?>;">
                  <div class="card-body" style="color:<?=$backgroundColorNew;?>!important">
                    <h5 class="card-title"><b><?= $heading ?></b></h5>
                    <small><?= $description ?></small> 
                    <hr>
                    <a target="_BLANK" class="card-count-heading-new" style="color:<?=$backgroundColorNew;?>!important" href="<?=base_url()?>Reports/ProposalDetailsData/<?=$key?>/<?=$uid?>/<?=$sdate?>/<?=$edate?>/<?=$task_action_id?>">
                      <h3 class="card-text"><?= $value ?></h3>
                    </a>
                  </div>
                </div>
              </div>
              <?php
                }
                ?>
            </div>
            <hr>




<div class="text-center " style="background: linear-gradient(to right, #ede7f6, #ffffff); border-radius: 12px; box-shadow: 0 4px 8px rgba(0,0,0,0.1); padding: 20px;">
              <h5><i>Breakdown of Approved Proposal Sources and Partners for Outreach and Engagement</i></h5> <hr>
              <small>The table categorizes various sources for proposal generation and outreach efforts. These sources encompass corporate entities, government offices, and public sector undertakings, as well as non-governmental organizations, private institutions, and STEM schools. Additionally, it includes individuals, foundations, and multinational corporations. The table also accounts for online inquiries, central leads from head offices, and region-specific inputs such as District Mineral Foundations and elected representatives. This categorization helps in identifying active segments and potential areas for growth. By analyzing these sources, strategies can be developed to strengthen partnerships and improve proposal conversion rates across different sectors, ensuring a comprehensive and effective approach to outreach and engagement.</small>
            </div>
            <hr>
            <div class="row">
              <?php  
               $descriptions = [
                      'corporate'               => 'Proposals sourced from corporate entities.',
                      'psu'                     => 'Proposals coming from Public Sector Units (PSUs).',
                      'ngo'                     => 'Proposals submitted by Non-Governmental Organizations.',
                      'private_school'          => 'Proposals originating from private schools.',
                      'individual'              => 'Proposals submitted by individual contributors.',
                      'govt_off'                => 'Proposals routed through government officials.',
                      'other'                   => 'Proposals from miscellaneous or unclassified sources.',
                      'online'                  => 'Proposals received via online platforms.',
                      'stem_school'             => 'Proposals from STEM-affiliated schools.',
                      'foundation'              => 'Proposals submitted by various foundations.',
                      'mnc'                     => 'Proposals initiated by multinational corporations.',
                      'ho_leads'                => 'Leads generated directly from the Head Office.',
                      'dmf'                     => 'Proposals supported or funded by DMF (District Mineral Foundation).',
                      'elected_representatives'=> 'Proposals introduced through elected representatives.'
                  ];

                foreach ($proposalSources  as $key =>  $value) {
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
              <div class="col-md-3">
                <div class="card text-center shadow span3 wow bounceInUp center" style="background-color: <?= $backgroundColor ?>; color: <?= $backgroundColorNew ?>;">
                  <div class="card-body" style="color:<?=$backgroundColorNew;?>!important">
                    <h5 class="card-title"><b><?= $heading ?></b></h5>
                    <small><?= $description ?></small> 
                    <hr>
                    <a target="_BLANK" class="card-count-heading-new" style="color:<?=$backgroundColorNew;?>!important" href="<?=base_url()?>Reports/ProposalDetailsData/<?=$key?>/<?=$uid?>/<?=$sdate?>/<?=$edate?>/<?=$task_action_id?>">
                      <h3 class="card-text"><?= $value ?></h3>
                    </a>
                  </div>
                </div>
              </div>
              <?php
                }
                ?>
            </div>
            <hr>









<div class="text-center " style="background: linear-gradient(to right, #ede7f6, #ffffff); border-radius: 12px; box-shadow: 0 4px 8px rgba(0,0,0,0.1); padding: 20px;">
              <h5><i>Approved Proposal By Category Breakdown</i></h5> <hr>
              <small>This structured breakdown categorizes proposals based on strategic client engagement and business priorities. It highlights potential growth areas, key accounts with significant spending patterns, and clients suitable for upselling. The focus is on nurturing critical funnels, managing high-value relationships, and identifying positive key clients showing active interest. It also emphasizes prioritizing outreach to ensure impactful conversations with decision-makers. By segmenting proposals in this manner, businesses can allocate resources efficiently, tailor communication strategies, and enhance conversion rates. This approach supports a more data-driven and client-centric business development process that aligns with long-term organizational goals and client retention strategies.</small>
            </div>
            <hr>
            <div class="row">
              <?php  
             $descriptions = [
                  'Potential'              => 'Clients with potential for new proposals.',
                  'Top_Spender'            => 'Clients who are among the top spenders.',
                  'Upsell_Client'          => 'Existing clients identified for upselling opportunities.',
                  'Focus_Funnel'           => 'Clients currently in focus for active proposal efforts.',
                  'Key_Client'             => 'Strategically important or high-priority clients.',
                  'Positive_Key_Client'    => 'Key clients showing positive engagement or outcomes.',
                  'Priority_Calling'       => 'Clients marked for priority follow-up or communication.'
                ];

                foreach ($proposalFunnelArray as $key =>  $value) {
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
              <div class="col-md-3">
                <div class="card text-center shadow span3 wow bounceInUp center" style="background-color: <?= $backgroundColor ?>; color: <?= $backgroundColorNew ?>;">
                  <div class="card-body" style="color:<?=$backgroundColorNew;?>!important">
                    <h5 class="card-title"><b><?= $heading ?></b></h5>
                    <small><?= $description ?></small> 
                    <hr>
                    <a target="_BLANK" class="card-count-heading-new" style="color:<?=$backgroundColorNew;?>!important" href="<?=base_url()?>Reports/ProposalDetailsData/<?=$key?>/<?=$uid?>/<?=$sdate?>/<?=$edate?>/<?=$task_action_id?>">
                      <h3 class="card-text"><?= $value ?></h3>
                    </a>
                  </div>
                </div>
              </div>
              <?php
                }
                ?>
            </div>
            <hr>





<div class="text-center " style="background: linear-gradient(to right, #ede7f6, #ffffff); border-radius: 12px; box-shadow: 0 4px 8px rgba(0,0,0,0.1); padding: 20px;">
              <h5><i>Overview of Approved Proposal Of New Category</i></h5> <hr>
              <small>The proposal funnel distribution highlights the key stages of organizational outreach and engagement throughout the fiscal year. It includes closures made in the first quarter, emerging potential leads under evaluation, nurturing efforts for long-term partnerships, and a strong push for identifying fifty fresh opportunities. Each stage represents a focused strategy to convert interest into impact, ensuring that every prospect is guided toward meaningful collaboration. This structured breakdown enables teams to prioritize actions, optimize resource allocation, and maintain momentum across the business development lifecycle while aligning with broader strategic goals and yearly performance benchmarks.</small>
            </div>
            <hr>
            <div class="row">
              <?php  
               $descriptions = [
                  'total_proposal_in_q1_twetenty_closure_funnel' => 'Proposals targeted for Q1 20-closure funnel.',
                  'total_proposal_in_potential_funnel_for_fy'    => 'Proposals in the potential funnel for the current financial year.',
                  'total_proposal_in_to_be_nurtured_for_fy'      => 'Proposals needing nurturing in the current financial year.',
                  'total_proposal_in_fifity_new_lead_funnel'     => 'New company proposals in the Fifty New Lead funnel.'
              ];
                foreach ($totalProposalFunnelArray as $key =>  $value) {
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
              <div class="col-md-3">
                <div class="card text-center shadow span3 wow bounceInUp center" style="background-color: <?= $backgroundColor ?>; color: <?= $backgroundColorNew ?>;">
                  <div class="card-body" style="color:<?=$backgroundColorNew;?>!important">
                    <h5 class="card-title"><b><?= $heading ?></b></h5>
                    <small><?= $description ?></small> 
                    <hr>
                    <a target="_BLANK" class="card-count-heading-new" style="color:<?=$backgroundColorNew;?>!important" href="<?=base_url()?>Reports/ProposalDetailsData/<?=$key?>/<?=$uid?>/<?=$sdate?>/<?=$edate?>/<?=$task_action_id?>">
                      <h3 class="card-text"><?= $value ?></h3>
                    </a>
                  </div>
                </div>
              </div>
              <?php
                }
                ?>
            </div>
            <hr>














 <div class="text-center " style="background: linear-gradient(to right, #ede7f6, #ffffff); border-radius: 12px; box-shadow: 0 4px 8px rgba(0,0,0,0.1); padding: 20px;">
              <h5><i>Total Proposal by Proposal Type</i></h5> <hr>
              <small>This structured summary presents a clear and meaningful breakdown of educational proposal tasks across various thematic areas such as science, innovation, customization, and digital learning. By organizing data into categories like model science centers, astronomy, tinkering labs, and smart classrooms, it highlights the focus areas of development and implementation. This approach supports better planning, monitoring, and evaluation of project efforts. It enables stakeholders to identify priorities, allocate resources effectively, and measure impact across different segments. A categorized view like this also enhances communication among teams and ensures strategic alignment with broader educational and institutional goals.</small>
            </div>
            <hr>
            <div class="row">
              <?php  
               $descriptions = [
                    'total_in_MSC'              => 'Total proposals under MSC category.',
                    'total_in_BALA'             => 'Total proposals under BALA (Building as Learning Aid).',
                    'total_in_Astronomy'        => 'Total proposals related to Astronomy.',
                    'total_in_Tinkering'        => 'Total proposals under Tinkering Labs.',
                    'total_in_Big_Laboratory'   => 'Total proposals for Big Science Laboratories.',
                    'total_in_Small_Laboratory' => 'Total proposals for Small Science Laboratories.',
                    'total_in_Customize'        => 'Total proposals with customized setups.',
                    'total_in_Smart_Class'      => 'Total proposals for Smart Class initiatives.'
                ];

                                
                foreach ($totalProposalInArray as $key =>  $value) {
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
              <div class="col-md-3">
                <div class="card text-center shadow span3 wow bounceInUp center" style="background-color: <?= $backgroundColor ?>; color: <?= $backgroundColorNew ?>;">
                  <div class="card-body" style="color:<?=$backgroundColorNew;?>!important">
                    <h5 class="card-title"><b><?= $heading ?></b></h5>
                    <small><?= $description ?></small> 
                    <hr>
                    <a target="_BLANK" class="card-count-heading-new" style="color:<?=$backgroundColorNew;?>!important" href="<?=base_url()?>Reports/ProposalDetailsData/<?=$key?>/<?=$uid?>/<?=$sdate?>/<?=$edate?>/<?=$task_action_id?>">
                      <h3 class="card-text"><?= $value ?></h3>
                    </a>
                  </div>
                </div>
              </div>
              <?php
                }
                ?>
            </div>
            <hr>











 <div class="text-center " style="background: linear-gradient(to right, #ede7f6, #ffffff); border-radius: 12px; box-shadow: 0 4px 8px rgba(0,0,0,0.1); padding: 20px;">
              <h5><i>Summary of Approved Proposals Type Across Educational Categories</i></h5> <hr>
              <small>This dataset captures the total number of approved proposals categorized by various educational themes such as MSC, BALA, Astronomy, Tinkering, and more. Each key in the $totalApprovals array represents a specific program or infrastructure type, with its corresponding value indicating how many proposals were approved under that category. By structuring the data this way, organizations can easily analyze which focus areas are receiving the most support, enabling data-driven decisions for future planning. This also helps in monitoring project trends, identifying high-impact areas, and aligning strategic goals with stakeholder priorities in educational development.</small>
            </div>
            <hr>
            <div class="row">
              <?php  
                $descriptions = [
                    'total_Approved_in_MSC'              => 'Total proposals approved under MSC category.',
                    'total_Approved_in_BALA'             => 'Total proposals approved under BALA category.',
                    'total_Approved_in_Astronomy'        => 'Total proposals approved under Astronomy category.',
                    'total_Approved_in_Tinkering'        => 'Total proposals approved under Tinkering Lab category.',
                    'total_Approved_in_Big_Laboratory'   => 'Total proposals approved under Big Laboratory setups.',
                    'total_Approved_in_Small_Laboratory' => 'Total proposals approved under Small Laboratory setups.',
                    'total_Approved_in_Customize'        => 'Total proposals approved under Customize Model category.',
                    'total_Approved_in_Smart_Class'      => 'Total proposals approved under Smart Class initiatives.',
                ];

                                
                foreach ($totalApprovals as $key =>  $value) {
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
              <div class="col-md-3">
                <div class="card text-center shadow span3 wow bounceInUp center" style="background-color: <?= $backgroundColor ?>; color: <?= $backgroundColorNew ?>;">
                  <div class="card-body" style="color:<?=$backgroundColorNew;?>!important">
                    <h5 class="card-title"><b><?= $heading ?></b></h5>
                    <small><?= $description ?></small> 
                    <hr>
                    <a target="_BLANK" class="card-count-heading-new" style="color:<?=$backgroundColorNew;?>!important" href="<?=base_url()?>Reports/ProposalDetailsData/<?=$key?>/<?=$uid?>/<?=$sdate?>/<?=$edate?>/<?=$task_action_id?>">
                      <h3 class="card-text"><?= $value ?></h3>
                    </a>
                  </div>
                </div>
              </div>
              <?php
                }
                ?>
            </div>
            <hr>



 <div class="text-center " style="background: linear-gradient(to right, #ede7f6, #ffffff); border-radius: 12px; box-shadow: 0 4px 8px rgba(0,0,0,0.1); padding: 20px;">
              <h5><i>Summary of Rejected Proposal Type on Educational Setups by Category</i></h5> <hr>
              <small>This dataset provides a categorized breakdown of rejected proposals or setups across various educational domains. It includes rejections in key areas such as MSC, BALA, Astronomy, Tinkering, and both Big and Small Laboratories. Additionally, it tracks rejections for customized educational models and smart classrooms. Analyzing this data helps in identifying challenges or gaps in proposal quality, compliance, or feasibility. It is a crucial resource for improving future planning, understanding trends in proposal rejection, and aligning setup standards with institutional expectations. Each category reflects specific programmatic focus areas in educational infrastructure and innovation.</small>
            </div>
            <hr>
            <div class="row">
              <?php  
                $descriptions = [
                    'total_Reject_in_MSC'              => 'Total number of rejections in MSC setup tasks.',
                    'total_Reject_in_BALA'             => 'Total number of rejections in BALA setup tasks.',
                    'total_Reject_in_Astronomy'        => 'Total number of rejections in Astronomy lab setup.',
                    'total_Reject_in_Tinkering'        => 'Total number of rejections in Tinkering lab setup.',
                    'total_Reject_in_Big_Laboratory'   => 'Total number of rejections in Big Laboratory setup.',
                    'total_Reject_in_Small_Laboratory' => 'Total number of rejections in Small Laboratory setup.',
                    'total_Reject_in_Customize'        => 'Total number of rejections in Customized setups.',
                    'total_Reject_in_Smart_Class'      => 'Total number of rejections in Smart Class setup.'
                ];
                                
                foreach ($totalRejects as $key =>  $value) {
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
              <div class="col-md-3">
                <div class="card text-center shadow span3 wow bounceInUp center" style="background-color: <?= $backgroundColor ?>; color: <?= $backgroundColorNew ?>;">
                  <div class="card-body" style="color:<?=$backgroundColorNew;?>!important">
                    <h5 class="card-title"><b><?= $heading ?></b></h5>
                    <small><?= $description ?></small> 
                    <hr>
                    <a target="_BLANK" class="card-count-heading-new" style="color:<?=$backgroundColorNew;?>!important" href="<?=base_url()?>Reports/ProposalDetailsData/<?=$key?>/<?=$uid?>/<?=$sdate?>/<?=$edate?>/<?=$task_action_id?>">
                      <h3 class="card-text"><?= $value ?></h3>
                    </a>
                  </div>
                </div>
              </div>
              <?php
                }
                ?>
            </div>
            <hr>




            <div class="text-center " style="background: linear-gradient(to right, #ede7f6, #ffffff); border-radius: 12px; box-shadow: 0 4px 8px rgba(0,0,0,0.1); padding: 20px;">
              <h5><i>Pending Approvals Overview Of Proposal Type Across Educational Initiatives</i></h5> <hr>
              <small>This data structure summarizes the number of pending approvals for various educational setups and innovations. It includes categories such as MSC, BALA, Astronomy, and Tinkering labs, along with laboratory types (Big and Small), customized learning setups, and Smart Classes. Each key in the array represents a specific initiative, while its corresponding value holds the total count of pending tasks awaiting approval. This structured format helps stakeholders quickly assess where delays are occurring, prioritize follow-ups, and take action to streamline the approval process for diverse educational interventions.</small>
            </div>
            <hr>
            <div class="row">
              <?php  
                $descriptions = [
                      'MSC' => 'Total MSC activities or setups.',
                      'BALA' => 'Total BALA (Building as Learning Aid) setups.',
                      'Astronomy' => 'Total Astronomy-related setups or tasks.',
                      'Tinkering' => 'Total Tinkering lab setups or activities.',
                      'Big_Laboratory' => 'Total setups in Big Laboratories.',
                      'Small_Laboratory' => 'Total setups in Small Laboratories.',
                      'Customize' => 'Total customized setups based on school needs.',
                      'Smart_Class' => 'Total Smart Class installations or activities.'
                  ];
                
                foreach ($totalPendingForApproved as $key =>  $value) {
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
              <div class="col-md-3">
                <div class="card text-center shadow span3 wow bounceInUp center" style="background-color: <?= $backgroundColor ?>; color: <?= $backgroundColorNew ?>;">
                  <div class="card-body" style="color:<?=$backgroundColorNew;?>!important">
                    <h5 class="card-title"><b><?= $heading ?></b></h5>
                    <small><?= $description ?></small> 
                    <hr>
                    <a target="_BLANK" class="card-count-heading-new" style="color:<?=$backgroundColorNew;?>!important" href="<?=base_url()?>Reports/ProposalDetailsData/<?=$key?>/<?=$uid?>/<?=$sdate?>/<?=$edate?>/<?=$task_action_id?>">
                      <h3 class="card-text"><?= $value ?></h3>
                    </a>
                  </div>
                </div>
              </div>
              <?php
                }
                ?>
            </div>
            <hr>

                






<?php  $totalProposalTasksByUser                            = $getTotalTasks['totalProposalTasksByUser']; ?>


          <hr>
              <div class="card">
                <div class="text-center " style="background: linear-gradient(to right, #ede7f6, #ffffff); border-radius: 12px; box-shadow: 0 4px 8px rgba(0,0,0,0.1); padding: 20px;">
              <h5><i>Proposal Task Summary Overview</i></h5> <hr>
              <small>This summary provides a clear snapshot of the overall progress of proposal-related tasks within the organization. It captures three essential components: the total number of proposal tasks assigned, how many of those have been successfully completed, and how many remain pending. By breaking down the tasks into these categories, the summary enables quick performance assessment and highlights areas that may require attention or follow-up. It is an effective tool for project managers and teams to monitor workload distribution, track efficiency, and ensure that proposal timelines are met without delays. This overview supports data-driven decision-making and better resource planning.</small>
            </div>
                <hr>
                <div class="table-responsive text-nowrap">
                  <table id="example1" class="table table-striped table-bordered" cellspacing="0" width="100%">
                    <thead class="thead-dark">
                      <tr>
                        <th class="text-capitalize">Sr. NO</th>
                        <th class="text-capitalize">Username</th>
                        <th class="text-capitalize">Total Proposal Task</th>
                        <th class="text-capitalize">Complete Proposal Task</th>
                        <th class="text-capitalize">Pending Proposal Task</th>
                      </tr>
                    </thead>
                    <tbody>
                      <?php
                        $i = 1;
                        foreach ($totalProposalTasksByUser as $row) {
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
                          <a target="_BLANK" class="card-count-heading-new" style="color:<?=$backgroundColorNew;?>!important" href="<?=base_url()?>Reports/ProposalDetailsData/total_proposal_task/<?=$task_user_id?>/<?=$sdate?>/<?=$edate?>/<?=$task_action_id?>/userwise">
                          <?= htmlspecialchars($row->total_proposal_task) ?>
                          </a>
                        </td>
                        <td>
                          <a target="_BLANK" class="card-count-heading-new" style="color:<?=$backgroundColorNew;?>!important" href="<?=base_url()?>Reports/ProposalDetailsData/complete_proposal_task/<?=$task_user_id?>/<?=$sdate?>/<?=$edate?>/<?=$task_action_id?>/userwise">
                          <?= htmlspecialchars($row->complete_proposal_task) ?>
                          </a>
                        </td>
                        <td>
                          <a target="_BLANK" class="card-count-heading-new" style="color:<?=$backgroundColorNew;?>!important" href="<?=base_url()?>Reports/ProposalDetailsData/pending_proposal_task/<?=$task_user_id?>/<?=$sdate?>/<?=$edate?>/<?=$task_action_id?>/userwise">
                          <?= htmlspecialchars($row->pending_proposal_task) ?>
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
               <div class="text-center " style="background: linear-gradient(to right, #ede7f6, #ffffff); border-radius: 12px; box-shadow: 0 4px 8px rgba(0,0,0,0.1); padding: 20px;">
              <h5><i>Proposal Summary Overview</i></h5> <hr>
              <small>This summary provides a concise overview of the current status of all proposals reviewed. It highlights the total number of proposals that have been approved, those that have been rejected, and those still awaiting approval. The data serves as a valuable performance indicator for project planning and decision-making processes. By tracking these categories, stakeholders can quickly assess progress, identify bottlenecks, and allocate resources more effectively. Understanding the distribution across approved, rejected, and pending proposals enables better forecasting and supports timely follow-ups. This summary ensures transparency and supports continuous improvement in proposal evaluation and execution workflows.</small>
            </div>
                <hr>
                <div class="table-responsive text-nowrap">
                  <table id="example2" class="table table-striped table-bordered" cellspacing="0" width="100%">
                    <thead class="thead-dark">
                      <tr>
                        <th class="text-capitalize">Sr. NO</th>
                        <th class="text-capitalize">Username</th>
                        <th class="text-capitalize">Total Proposal</th>
                        <th class="text-capitalize">Proposal Approved</th>
                        <th class="text-capitalize">Proposal Reject</th>
                        <th class="text-capitalize">Pending For Approved</th>
                      </tr>
                    </thead>
                    <tbody>
                      <?php
                        $i = 1;
                        foreach ($totalProposalTasksByUser as $row) {
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
                          <a target="_BLANK" class="card-count-heading-new" style="color:<?=$backgroundColorNew;?>!important" href="<?=base_url()?>Reports/ProposalDetailsData/total_proposal_task/<?=$task_user_id?>/<?=$sdate?>/<?=$edate?>/<?=$task_action_id?>/userwise">
                          <?= htmlspecialchars($row->total_proposal_task) ?>
                          </a>
                        </td>
                        <td>
                          <a target="_BLANK" class="card-count-heading-new" style="color:<?=$backgroundColorNew;?>!important" href="<?=base_url()?>Reports/ProposalDetailsData/proposal_approved/<?=$task_user_id?>/<?=$sdate?>/<?=$edate?>/<?=$task_action_id?>/userwise">
                          <?= htmlspecialchars($row->proposal_approved) ?>
                          </a>
                        </td>
                        <td>
                          <a target="_BLANK" class="card-count-heading-new" style="color:<?=$backgroundColorNew;?>!important" href="<?=base_url()?>Reports/ProposalDetailsData/proposal_reject/<?=$task_user_id?>/<?=$sdate?>/<?=$edate?>/<?=$task_action_id?>/userwise">
                          <?= htmlspecialchars($row->proposal_reject) ?>
                          </a>
                        </td>
                        <td>
                          <a target="_BLANK" class="card-count-heading-new" style="color:<?=$backgroundColorNew;?>!important" href="<?=base_url()?>Reports/ProposalDetailsData/pending_for_approved/<?=$task_user_id?>/<?=$sdate?>/<?=$edate?>/<?=$task_action_id?>/userwise">
                          <?= htmlspecialchars($row->pending_for_approved) ?>
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
              <div class="text-center " style="background: linear-gradient(to right, #ede7f6, #ffffff); border-radius: 12px; box-shadow: 0 4px 8px rgba(0,0,0,0.1); padding: 20px;">
              <h5><i>Summary of Proposal Budget Approval Status</i></h5> <hr>
              <small>This summary provides a clear overview of the proposal budget approval process. It highlights the number of proposals that have successfully received budget approvals, those currently awaiting approval decisions, and those that were rejected during the evaluation. This information helps stakeholders quickly assess the overall progress and effectiveness of the budget evaluation process. By understanding the distribution between approved, pending, and rejected proposals, teams can identify potential bottlenecks, streamline follow-ups, and allocate resources more efficiently to improve turnaround time and ensure smooth financial planning and project execution.</small>
            </div>
                <hr>
                <div class="table-responsive text-nowrap">
                  <table id="example3" class="table table-striped table-bordered" cellspacing="0" width="100%">
                    <thead class="thead-dark">
                      <tr>
                        <th class="text-capitalize">Sr. NO</th>
                        <th class="text-capitalize">Username</th>

                        <th class="text-capitalize">Total Proposal</th>
                         <th class="text-capitalize">Total Proposal Budget</th>

                        <th class="text-capitalize">Proposal Approved</th>
                        <th class="text-capitalize">Total Proposal Approved Budget</th>

                        <th class="text-capitalize">Proposal Reject</th>
                        <th class="text-capitalize">Total Proposal Reject Budget</th>

                        <th class="text-capitalize">Pending For Approved</th>
                        <th class="text-capitalize">Total Proposal Pending Budget</th>
                      </tr>
                    </thead>
                    <tbody>
                      <?php
                        $i = 1;
                        foreach ($totalProposalTasksByUser as $row) {
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
                          <a target="_BLANK" class="card-count-heading-new" style="color:<?=$backgroundColorNew;?>!important" href="<?=base_url()?>Reports/ProposalDetailsData/total_proposal_task/<?=$task_user_id?>/<?=$sdate?>/<?=$edate?>/<?=$task_action_id?>/userwise">
                          <?= htmlspecialchars($row->total_proposal_task) ?>
                          </a>
                        </td>
                         <td>
                          <a target="_BLANK" class="card-count-heading-new" style="color:<?=$backgroundColorNew;?>!important" href="<?=base_url()?>Reports/ProposalDetailsData/total_proposal_budget/<?=$task_user_id?>/<?=$sdate?>/<?=$edate?>/<?=$task_action_id?>/userwise">
                          <?= htmlspecialchars($row->total_proposal_budget) ?>
                          </a>
                        </td>
                    
                        <td>
                          <a target="_BLANK" class="card-count-heading-new" style="color:<?=$backgroundColorNew;?>!important" href="<?=base_url()?>Reports/ProposalDetailsData/proposal_approved/<?=$task_user_id?>/<?=$sdate?>/<?=$edate?>/<?=$task_action_id?>/userwise">
                          <?= htmlspecialchars($row->proposal_approved) ?>
                          </a>
                        </td>
                        <td>
                          <a target="_BLANK" class="card-count-heading-new" style="color:<?=$backgroundColorNew;?>!important" href="<?=base_url()?>Reports/ProposalDetailsData/total_proposal_approved_budget/<?=$task_user_id?>/<?=$sdate?>/<?=$edate?>/<?=$task_action_id?>/userwise">
                          <?= htmlspecialchars($row->total_proposal_approved_budget) ?>  ₹
                          </a>
                        </td>
                        <td>
                          <a target="_BLANK" class="card-count-heading-new" style="color:<?=$backgroundColorNew;?>!important" href="<?=base_url()?>Reports/ProposalDetailsData/proposal_reject/<?=$task_user_id?>/<?=$sdate?>/<?=$edate?>/<?=$task_action_id?>/userwise">
                          <?= htmlspecialchars($row->proposal_reject) ?>
                          </a>
                        </td>
                        <td>
                          <a target="_BLANK" class="card-count-heading-new" style="color:<?=$backgroundColorNew;?>!important" href="<?=base_url()?>Reports/ProposalDetailsData/total_proposal_reject_budget/<?=$task_user_id?>/<?=$sdate?>/<?=$edate?>/<?=$task_action_id?>/userwise">
                          <?= htmlspecialchars($row->total_proposal_reject_budget) ?>  ₹
                          </a>
                        </td>
                        <td>
                          <a target="_BLANK" class="card-count-heading-new" style="color:<?=$backgroundColorNew;?>!important" href="<?=base_url()?>Reports/ProposalDetailsData/pending_for_approved/<?=$task_user_id?>/<?=$sdate?>/<?=$edate?>/<?=$task_action_id?>/userwise">
                          <?= htmlspecialchars($row->pending_for_approved) ?> 
                          </a>
                        </td>
                        <td>
                          <a target="_BLANK" class="card-count-heading-new" style="color:<?=$backgroundColorNew;?>!important" href="<?=base_url()?>Reports/ProposalDetailsData/total_proposal_pending_budget/<?=$task_user_id?>/<?=$sdate?>/<?=$edate?>/<?=$task_action_id?>/userwise">
                          <?= htmlspecialchars($row->total_proposal_pending_budget) ?>  ₹
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
              <div class="text-center " style="background: linear-gradient(to right, #ede7f6, #ffffff); border-radius: 12px; box-shadow: 0 4px 8px rgba(0,0,0,0.1); padding: 20px;">
              <h5><i>Approved Proposal Engagement Status Overview</i></h5> <hr>
              <small>This structured list represents the different engagement stages of proposals within a business development lifecycle. Each label denotes a unique interaction level or response from a prospective partner or client. These range from initial outreach and open opportunities to more committed statuses like "positive," "very positive," or "on boarded." Intermediate categories such as "tentative" or "will do later" help track potential delays or uncertainties. Special outreach methods like "ttd reachout" and "wno reachout" indicate tailored engagement strategies. Collectively, this classification enables teams to effectively monitor progress, prioritize actions, and understand where each proposal stands in the conversion journey.</small>
            </div>
                <hr>
                <div class="table-responsive text-nowrap">
                  <table id="example4" class="table table-striped table-bordered" cellspacing="0" width="100%">
                    <thead class="thead-dark">
                      <tr>
                        <th class="text-capitalize">Sr. NO</th>
                        <th class="text-capitalize">Username</th>
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
                        foreach ($totalProposalTasksByUser as $row) {
                          $task_user_id = $row->user_id;
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
                        <td><?= htmlspecialchars($i) ?></td>
                        <td class="text-capitalize"><?= htmlspecialchars($row->username) ?></td>

                        <td>
                          <a target="_BLANK" class="card-count-heading-new" style="color:<?=$backgroundColorNew;?>!important" href="<?=base_url()?>Reports/ProposalDetailsData/open/<?=$meeting_user_id?>/<?=$sdate?>/<?=$edate?>/<?=$task_action_id?>/userwise">
                          <?= $row->open; ?>
                          </a>
                        </td>
                        <td>
                          <a target="_BLANK" class="card-count-heading-new" style="color:<?=$backgroundColorNew;?>!important" href="<?=base_url()?>Reports/ProposalDetailsData/reachout/<?=$meeting_user_id?>/<?=$sdate?>/<?=$edate?>/<?=$task_action_id?>/userwise">
                          <?= $row->reachout; ?>
                          </a>
                        </td>
                        <td>
                          <a target="_BLANK" class="card-count-heading-new" style="color:<?=$backgroundColorNew;?>!important" href="<?=base_url()?>Reports/ProposalDetailsData/tentative/<?=$meeting_user_id?>/<?=$sdate?>/<?=$edate?>/<?=$task_action_id?>/userwise">
                          <?= $row->tentative; ?>
                          </a>
                        </td>
                        <td>
                          <a target="_BLANK" class="card-count-heading-new" style="color:<?=$backgroundColorNew;?>!important" href="<?=base_url()?>Reports/ProposalDetailsData/will_do_later/<?=$meeting_user_id?>/<?=$sdate?>/<?=$edate?>/<?=$task_action_id?>/userwise">
                          <?= $row->will_do_later; ?>
                          </a>
                        </td>
                        <td>
                          <a target="_BLANK" class="card-count-heading-new" style="color:<?=$backgroundColorNew;?>!important" href="<?=base_url()?>Reports/ProposalDetailsData/not_interested/<?=$meeting_user_id?>/<?=$sdate?>/<?=$edate?>/<?=$task_action_id?>/userwise">
                          <?= $row->not_interested; ?>
                          </a>
                        </td>
                        <td>
                          <a target="_BLANK" class="card-count-heading-new" style="color:<?=$backgroundColorNew;?>!important" href="<?=base_url()?>Reports/ProposalDetailsData/positive/<?=$meeting_user_id?>/<?=$sdate?>/<?=$edate?>/<?=$task_action_id?>/userwise">
                          <?= $row->positive; ?>
                          </a>
                        </td>
                        <td>
                          <a target="_BLANK" class="card-count-heading-new" style="color:<?=$backgroundColorNew;?>!important" href="<?=base_url()?>Reports/ProposalDetailsData/closure/<?=$meeting_user_id?>/<?=$sdate?>/<?=$edate?>/<?=$task_action_id?>/userwise">
                          <?= $row->closure; ?>
                          </a>
                        </td>
                        <td>
                          <a target="_BLANK" class="card-count-heading-new" style="color:<?=$backgroundColorNew;?>!important" href="<?=base_url()?>Reports/ProposalDetailsData/open_rpem/<?=$meeting_user_id?>/<?=$sdate?>/<?=$edate?>/<?=$task_action_id?>/userwise">
                          <?= $row->open_rpem; ?>
                          </a>
                        </td>
                        <td>
                          <a target="_BLANK" class="card-count-heading-new" style="color:<?=$backgroundColorNew;?>!important" href="<?=base_url()?>Reports/ProposalDetailsData/very_positive/<?=$meeting_user_id?>/<?=$sdate?>/<?=$edate?>/<?=$task_action_id?>/userwise">
                          <?= $row->very_positive; ?>
                          </a>
                        </td>
                        <td>
                          <a target="_BLANK" class="card-count-heading-new" style="color:<?=$backgroundColorNew;?>!important" href="<?=base_url()?>Reports/ProposalDetailsData/ttd_reachout/<?=$meeting_user_id?>/<?=$sdate?>/<?=$edate?>/<?=$task_action_id?>/userwise">
                          <?= $row->ttd_reachout; ?>
                          </a>
                        </td>
                        <td>
                          <a target="_BLANK" class="card-count-heading-new" style="color:<?=$backgroundColorNew;?>!important" href="<?=base_url()?>Reports/ProposalDetailsData/wno_reachout/<?=$meeting_user_id?>/<?=$sdate?>/<?=$edate?>/<?=$task_action_id?>/userwise">
                          <?= $row->wno_reachout; ?>
                          </a>
                        </td>
                        <td>
                          <a target="_BLANK" class="card-count-heading-new" style="color:<?=$backgroundColorNew;?>!important" href="<?=base_url()?>Reports/ProposalDetailsData/positive_nap/<?=$meeting_user_id?>/<?=$sdate?>/<?=$edate?>/<?=$task_action_id?>/userwise">
                          <?= $row->positive_nap; ?>
                          </a>
                        </td>
                        <td>
                          <a target="_BLANK" class="card-count-heading-new" style="color:<?=$backgroundColorNew;?>!important" href="<?=base_url()?>Reports/ProposalDetailsData/very_positive_nap/<?=$meeting_user_id?>/<?=$sdate?>/<?=$edate?>/<?=$task_action_id?>/userwise">
                          <?= $row->very_positive_nap; ?>
                          </a>
                        </td>
                        <td>
                          <a target="_BLANK" class="card-count-heading-new" style="color:<?=$backgroundColorNew;?>!important" href="<?=base_url()?>Reports/ProposalDetailsData/on_boarded/<?=$meeting_user_id?>/<?=$sdate?>/<?=$edate?>/<?=$task_action_id?>/userwise">
                          <?= $row->on_boarded; ?>
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
              <div class="text-center " style="background: linear-gradient(to right, #ede7f6, #ffffff); border-radius: 12px; box-shadow: 0 4px 8px rgba(0,0,0,0.1); padding: 20px;">
              <h5><i>Breakdown of Approved Proposal Sources and Partners for Outreach and Engagement</i></h5> <hr>
              <small>The table categorizes various sources for proposal generation and outreach efforts. These sources encompass corporate entities, government offices, and public sector undertakings, as well as non-governmental organizations, private institutions, and STEM schools. Additionally, it includes individuals, foundations, and multinational corporations. The table also accounts for online inquiries, central leads from head offices, and region-specific inputs such as District Mineral Foundations and elected representatives. This categorization helps in identifying active segments and potential areas for growth. By analyzing these sources, strategies can be developed to strengthen partnerships and improve proposal conversion rates across different sectors, ensuring a comprehensive and effective approach to outreach and engagement.</small>
            </div>
                <hr>
                <div class="table-responsive text-nowrap">
                  <table id="example5" class="table table-striped table-bordered" cellspacing="0" width="100%">
                    <thead class="thead-dark">
                      <tr>
                        <th class="text-capitalize">Sr. NO</th>
                        <th class="text-capitalize">Username</th>
                        <th>Corporate</th>
                        <th>PSU</th>
                        <th>NGO</th>
                        <th>Private School</th>
                        <th>Individual</th>
                        <th>Govt Off</th>
                        <th>Other</th>
                        <th>Online</th>
                        <th>STEM School</th>
                        <th>Foundation</th>
                        <th>MNC</th>
                        <th>HO Leads</th>
                        <th>DMF</th>
                        <th>Elected Representatives</th>
                      </tr>
                    </thead>
                    <tbody>
                      <?php
                        $i = 1;
                        foreach ($totalProposalTasksByUser as $row) {
                          $task_user_id = $row->user_id;
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
                        <td><?= htmlspecialchars($i) ?></td>
                        <td class="text-capitalize"><?= htmlspecialchars($row->username) ?></td>

                      
                       <td>
                          <a target="_BLANK" class="card-count-heading-new" style="color:<?=$backgroundColorNew;?>!important" href="<?=base_url()?>Reports/ProposalDetailsData/corporate/<?=$meeting_user_id?>/<?=$sdate?>/<?=$edate?>/<?=$task_action_id?>/userwise">
                          <?= $row->corporate; ?>
                          </a>
                        </td>
                        <td>
                          <a target="_BLANK" class="card-count-heading-new" style="color:<?=$backgroundColorNew;?>!important" href="<?=base_url()?>Reports/ProposalDetailsData/PSU/<?=$meeting_user_id?>/<?=$sdate?>/<?=$edate?>/<?=$task_action_id?>/userwise">
                          <?= $row->PSU; ?>
                          </a>
                        </td>
                        <td>
                          <a target="_BLANK" class="card-count-heading-new" style="color:<?=$backgroundColorNew;?>!important" href="<?=base_url()?>Reports/ProposalDetailsData/NGO/<?=$meeting_user_id?>/<?=$sdate?>/<?=$edate?>/<?=$task_action_id?>/userwise">
                          <?= $row->NGO; ?>
                          </a>
                        </td>
                        <td>
                          <a target="_BLANK" class="card-count-heading-new" style="color:<?=$backgroundColorNew;?>!important" href="<?=base_url()?>Reports/ProposalDetailsData/Private_School/<?=$meeting_user_id?>/<?=$sdate?>/<?=$edate?>/<?=$task_action_id?>/userwise">
                          <?= $row->Private_School; ?>
                          </a>
                        </td>
                        <td>
                          <a target="_BLANK" class="card-count-heading-new" style="color:<?=$backgroundColorNew;?>!important" href="<?=base_url()?>Reports/ProposalDetailsData/Individual/<?=$meeting_user_id?>/<?=$sdate?>/<?=$edate?>/<?=$task_action_id?>/userwise">
                          <?= $row->Individual; ?>
                          </a>
                        </td>
                        <td>
                          <a target="_BLANK" class="card-count-heading-new" style="color:<?=$backgroundColorNew;?>!important" href="<?=base_url()?>Reports/ProposalDetailsData/Govt_Off/<?=$meeting_user_id?>/<?=$sdate?>/<?=$edate?>/<?=$task_action_id?>/userwise">
                          <?= $row->Govt_Off; ?>
                          </a>
                        </td>
                        <td>
                          <a target="_BLANK" class="card-count-heading-new" style="color:<?=$backgroundColorNew;?>!important" href="<?=base_url()?>Reports/ProposalDetailsData/Other/<?=$meeting_user_id?>/<?=$sdate?>/<?=$edate?>/<?=$task_action_id?>/userwise">
                          <?= $row->Other; ?>
                          </a>
                        </td>
                        <td>
                          <a target="_BLANK" class="card-count-heading-new" style="color:<?=$backgroundColorNew;?>!important" href="<?=base_url()?>Reports/ProposalDetailsData/Online/<?=$meeting_user_id?>/<?=$sdate?>/<?=$edate?>/<?=$task_action_id?>/userwise">
                          <?= $row->Online; ?>
                          </a>
                        </td>
                        <td>
                          <a target="_BLANK" class="card-count-heading-new" style="color:<?=$backgroundColorNew;?>!important" href="<?=base_url()?>Reports/ProposalDetailsData/STEM_School/<?=$meeting_user_id?>/<?=$sdate?>/<?=$edate?>/<?=$task_action_id?>/userwise">
                          <?= $row->STEM_School; ?>
                          </a>
                        </td>
                        <td>
                          <a target="_BLANK" class="card-count-heading-new" style="color:<?=$backgroundColorNew;?>!important" href="<?=base_url()?>Reports/ProposalDetailsData/Foundation/<?=$meeting_user_id?>/<?=$sdate?>/<?=$edate?>/<?=$task_action_id?>/userwise">
                          <?= $row->Foundation; ?>
                          </a>
                        </td>
                        <td>
                          <a target="_BLANK" class="card-count-heading-new" style="color:<?=$backgroundColorNew;?>!important" href="<?=base_url()?>Reports/ProposalDetailsData/MNC/<?=$meeting_user_id?>/<?=$sdate?>/<?=$edate?>/<?=$task_action_id?>/userwise">
                          <?= $row->MNC; ?>
                          </a>
                        </td>
                        <td>
                          <a target="_BLANK" class="card-count-heading-new" style="color:<?=$backgroundColorNew;?>!important" href="<?=base_url()?>Reports/ProposalDetailsData/HO_Leads/<?=$meeting_user_id?>/<?=$sdate?>/<?=$edate?>/<?=$task_action_id?>/userwise">
                          <?= $row->HO_Leads; ?>
                          </a>
                        </td>
                        <td>
                          <a target="_BLANK" class="card-count-heading-new" style="color:<?=$backgroundColorNew;?>!important" href="<?=base_url()?>Reports/ProposalDetailsData/DMF/<?=$meeting_user_id?>/<?=$sdate?>/<?=$edate?>/<?=$task_action_id?>/userwise">
                          <?= $row->DMF; ?>
                          </a>
                        </td>
                        <td>
                          <a target="_BLANK" class="card-count-heading-new" style="color:<?=$backgroundColorNew;?>!important" href="<?=base_url()?>Reports/ProposalDetailsData/Elected_Representatives/<?=$meeting_user_id?>/<?=$sdate?>/<?=$edate?>/<?=$task_action_id?>/userwise">
                          <?= $row->Elected_Representatives; ?>
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
              <div class="text-center " style="background: linear-gradient(to right, #ede7f6, #ffffff); border-radius: 12px; box-shadow: 0 4px 8px rgba(0,0,0,0.1); padding: 20px;">
              <h5><i>Approved Proposal By Category Breakdown</i></h5> <hr>
              <small>This structured breakdown categorizes proposals based on strategic client engagement and business priorities. It highlights potential growth areas, key accounts with significant spending patterns, and clients suitable for upselling. The focus is on nurturing critical funnels, managing high-value relationships, and identifying positive key clients showing active interest. It also emphasizes prioritizing outreach to ensure impactful conversations with decision-makers. By segmenting proposals in this manner, businesses can allocate resources efficiently, tailor communication strategies, and enhance conversion rates. This approach supports a more data-driven and client-centric business development process that aligns with long-term organizational goals and client retention strategies.</small>
            </div>
                <hr>
                <div class="table-responsive text-nowrap">
                  <table id="example6" class="table table-striped table-bordered" cellspacing="0" width="100%">
                    <thead class="thead-dark">
                      <tr>
                        <th class="text-capitalize">Sr. NO</th>
                        <th class="text-capitalize">Username</th>
                        <th>Potential Client</th>
                        <th>Top Spender Client</th>
                        <th>Upsell Client</th>
                        <th>Focus Funnel Client</th>
                        <th>Key Client</th>
                        <th>Positive Key Client</th>
                        <th>Priorityc Client</th>
                      </tr>
                    </thead>
                    <tbody>
                      <?php
                        $i = 1;
                        foreach ($totalProposalTasksByUser as $row) {
                          $task_user_id = $row->user_id;
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
                        <td><?= htmlspecialchars($i) ?></td>
                        <td class="text-capitalize"><?= htmlspecialchars($row->username) ?></td>

                          <td>
                          <a target="_BLANK" class="card-count-heading-new" style="color:<?=$backgroundColorNew;?>!important" href="<?=base_url()?>Reports/ProposalDetailsData/Potential/<?=$meeting_user_id?>/<?=$sdate?>/<?=$edate?>/<?=$task_action_id?>/userwise">
                          <?= $row->Potential; ?>
                          </a>
                        </td>
                        <td>
                          <a target="_BLANK" class="card-count-heading-new" style="color:<?=$backgroundColorNew;?>!important" href="<?=base_url()?>Reports/ProposalDetailsData/Top_Spender/<?=$meeting_user_id?>/<?=$sdate?>/<?=$edate?>/<?=$task_action_id?>/userwise">
                          <?= $row->Top_Spender; ?>
                          </a>
                        </td>
                        <td>
                          <a target="_BLANK" class="card-count-heading-new" style="color:<?=$backgroundColorNew;?>!important" href="<?=base_url()?>Reports/ProposalDetailsData/Upsell_Client/<?=$meeting_user_id?>/<?=$sdate?>/<?=$edate?>/<?=$task_action_id?>/userwise">
                          <?= $row->Upsell_Client; ?>
                          </a>
                        </td>
                        <td>
                          <a target="_BLANK" class="card-count-heading-new" style="color:<?=$backgroundColorNew;?>!important" href="<?=base_url()?>Reports/ProposalDetailsData/Focus_Funnel/<?=$meeting_user_id?>/<?=$sdate?>/<?=$edate?>/<?=$task_action_id?>/userwise">
                          <?= $row->Focus_Funnel; ?>
                          </a>
                        </td>
                        <td>
                          <a target="_BLANK" class="card-count-heading-new" style="color:<?=$backgroundColorNew;?>!important" href="<?=base_url()?>Reports/ProposalDetailsData/Key_Client/<?=$meeting_user_id?>/<?=$sdate?>/<?=$edate?>/<?=$task_action_id?>/userwise">
                          <?= $row->Key_Client; ?>
                          </a>
                        </td>
                        <td>
                          <a target="_BLANK" class="card-count-heading-new" style="color:<?=$backgroundColorNew;?>!important" href="<?=base_url()?>Reports/ProposalDetailsData/Positive_Key_Client/<?=$meeting_user_id?>/<?=$sdate?>/<?=$edate?>/<?=$task_action_id?>/userwise">
                          <?= $row->Positive_Key_Client; ?>
                          </a>
                        </td>
                        <td>
                          <a target="_BLANK" class="card-count-heading-new" style="color:<?=$backgroundColorNew;?>!important" href="<?=base_url()?>Reports/ProposalDetailsData/Priority_Calling/<?=$meeting_user_id?>/<?=$sdate?>/<?=$edate?>/<?=$task_action_id?>/userwise">
                          <?= $row->Priority_Calling; ?>
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
              <div class="text-center " style="background: linear-gradient(to right, #ede7f6, #ffffff); border-radius: 12px; box-shadow: 0 4px 8px rgba(0,0,0,0.1); padding: 20px;">
              <h5><i>Overview of Approved Proposal Of New Category</i></h5> <hr>
              <small>The proposal funnel distribution highlights the key stages of organizational outreach and engagement throughout the fiscal year. It includes closures made in the first quarter, emerging potential leads under evaluation, nurturing efforts for long-term partnerships, and a strong push for identifying fifty fresh opportunities. Each stage represents a focused strategy to convert interest into impact, ensuring that every prospect is guided toward meaningful collaboration. This structured breakdown enables teams to prioritize actions, optimize resource allocation, and maintain momentum across the business development lifecycle while aligning with broader strategic goals and yearly performance benchmarks.</small>


            </div>
                <hr>
                <div class="table-responsive text-nowrap">
                  <table id="example7" class="table table-striped table-bordered" cellspacing="0" width="100%">
                    <thead class="thead-dark">
                      <tr>
                        <th class="text-capitalize">Sr. NO</th>
                        <th class="text-capitalize">Username</th>
                        <th>Q1 20 Closure Funnel	</th>
                        <th>Potential Funnel For FY </th>
                        <th>To be Nurtured for FY </th>
                        <th>Complete Proposal in - 50 New Lead Funnel </th>
                      </tr>
                    </thead>
                    <tbody>
                      <?php
                        $i = 1;
                        foreach ($totalProposalTasksByUser as $row) {
                          $task_user_id = $row->user_id;
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
                        <td><?= htmlspecialchars($i) ?></td>
                        <td class="text-capitalize"><?= htmlspecialchars($row->username) ?></td>

                        <td>
                          <a target="_BLANK" class="card-count-heading-new" style="color:<?=$backgroundColorNew;?>!important" href="<?=base_url()?>Reports/ProposalDetailsData/total_company_in_q1_twetenty_closure_funnel/<?=$meeting_user_id?>/<?=$sdate?>/<?=$edate?>/<?=$task_action_id?>/userwise">
                          <?= $row->total_company_in_q1_twetenty_closure_funnel; ?>
                          </a>
                        </td>
                        <td>
                          <a target="_BLANK" class="card-count-heading-new" style="color:<?=$backgroundColorNew;?>!important" href="<?=base_url()?>Reports/ProposalDetailsData/total_company_in_potential_funnel_for_fy/<?=$meeting_user_id?>/<?=$sdate?>/<?=$edate?>/<?=$task_action_id?>/userwise">
                          <?= $row->total_company_in_potential_funnel_for_fy; ?>
                          </a>
                        </td>
                        <td>
                          <a target="_BLANK" class="card-count-heading-new" style="color:<?=$backgroundColorNew;?>!important" href="<?=base_url()?>Reports/ProposalDetailsData/total_company_in_to_be_nurtured_for_fy/<?=$meeting_user_id?>/<?=$sdate?>/<?=$edate?>/<?=$task_action_id?>/userwise">
                          <?= $row->total_company_in_to_be_nurtured_for_fy; ?>
                          </a>
                        </td>
                        <td>
                          <a target="_BLANK" class="card-count-heading-new" style="color:<?=$backgroundColorNew;?>!important" href="<?=base_url()?>Reports/ProposalDetailsData/total_company_in_fifity_new_lead_funnel/<?=$meeting_user_id?>/<?=$sdate?>/<?=$edate?>/<?=$task_action_id?>/userwise">
                          <?= $row->total_company_in_fifity_new_lead_funnel; ?>
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
              <div class="text-center " style="background: linear-gradient(to right, #ede7f6, #ffffff); border-radius: 12px; box-shadow: 0 4px 8px rgba(0,0,0,0.1); padding: 20px;">
              <h5><i>Total Proposal by Proposal Type</i></h5> <hr>
              <small>This structured summary presents a clear and meaningful breakdown of educational proposal tasks across various thematic areas such as science, innovation, customization, and digital learning. By organizing data into categories like model science centers, astronomy, tinkering labs, and smart classrooms, it highlights the focus areas of development and implementation. This approach supports better planning, monitoring, and evaluation of project efforts. It enables stakeholders to identify priorities, allocate resources effectively, and measure impact across different segments. A categorized view like this also enhances communication among teams and ensures strategic alignment with broader educational and institutional goals.</small>
            </div>
                <hr>
                <div class="table-responsive text-nowrap">
                  <table id="example8" class="table table-striped table-bordered" cellspacing="0" width="100%">
                    <thead class="thead-dark">
                      <tr>
                        <th class="text-capitalize">Sr. NO</th>
                        <th class="text-capitalize">Username</th>
                        <th>Total In MSC</th>
                        <th>Total In BALA</th>
                        <th>Total In Astronomy</th>
                        <th>Total In Tinkering</th>
                        <th>Total In Big Laboratory</th>
                        <th>Total In Small Laboratory</th>
                        <th>Total In Customize</th>
                        <th>Total In Smart Class</th>

                        <th>Total Approved In MSC</th>
                        <th>Total Approved In BALA</th>
                        <th>Total Approved In Astronomy</th>
                        <th>Total Approved In Tinkering</th>
                        <th>Total Approved In Big Laboratory</th>
                        <th>Total Approved In Small Laboratory</th>
                        <th>Total Approved In Customize</th>
                        <th>Total Approved In Smart Class</th>

                        <th>Total Rejected In MSC</th>
                        <th>Total Rejected In BALA</th>
                        <th>Total Rejected In Astronomy</th>
                        <th>Total Rejected In Tinkering</th>
                        <th>Total Rejected In Big Laboratory</th>
                        <th>Total Rejected In Small Laboratory</th>
                        <th>Total Rejected In Customize</th>
                        <th>Total Rejected In Smart Class</th>

                        <th>Total Pending In MSC</th>
                        <th>Total Pending In BALA</th>
                        <th>Total Pending In Astronomy</th>
                        <th>Total Pending In Tinkering</th>
                        <th>Total Pending In Big Laboratory</th>
                        <th>Total Pending In Small Laboratory</th>
                        <th>Total Pending In Customize</th>
                        <th>Total Pending In Smart Class</th>

                      </tr>
                    </thead>
                    <tbody>
                      <?php
                        $i = 1;
                        foreach ($totalProposalTasksByUser as $row) {
                          $task_user_id = $row->user_id;
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
                        <td><?= htmlspecialchars($i) ?></td>
                        <td class="text-capitalize"><?= htmlspecialchars($row->username) ?></td>

                        <td>
                          <a target="_BLANK" class="card-count-heading-new" style="color:<?=$backgroundColorNew;?>!important" href="<?=base_url()?>Reports/ProposalDetailsData/total_Approved_in_MSC/<?=$meeting_user_id?>/<?=$sdate?>/<?=$edate?>/<?=$task_action_id?>/userwise">
                          <?= $row->total_Approved_in_MSC; ?>
                          </a>
                        </td>
                        <td>
                          <a target="_BLANK" class="card-count-heading-new" style="color:<?=$backgroundColorNew;?>!important" href="<?=base_url()?>Reports/ProposalDetailsData/total_in_BALA/<?=$meeting_user_id?>/<?=$sdate?>/<?=$edate?>/<?=$task_action_id?>/userwise">
                          <?= $row->total_in_BALA; ?>
                          </a>
                        </td>
                        <td>
                          <a target="_BLANK" class="card-count-heading-new" style="color:<?=$backgroundColorNew;?>!important" href="<?=base_url()?>Reports/ProposalDetailsData/total_in_Astronomy/<?=$meeting_user_id?>/<?=$sdate?>/<?=$edate?>/<?=$task_action_id?>/userwise">
                          <?= $row->total_in_Astronomy; ?>
                          </a>
                        </td>
                        <td>
                          <a target="_BLANK" class="card-count-heading-new" style="color:<?=$backgroundColorNew;?>!important" href="<?=base_url()?>Reports/ProposalDetailsData/total_in_Tinkering/<?=$meeting_user_id?>/<?=$sdate?>/<?=$edate?>/<?=$task_action_id?>/userwise">
                          <?= $row->total_in_Tinkering; ?>
                          </a>
                        </td>
                        <td>
                          <a target="_BLANK" class="card-count-heading-new" style="color:<?=$backgroundColorNew;?>!important" href="<?=base_url()?>Reports/ProposalDetailsData/total_in_Big_Laboratory/<?=$meeting_user_id?>/<?=$sdate?>/<?=$edate?>/<?=$task_action_id?>/userwise">
                          <?= $row->total_in_Big_Laboratory; ?>
                          </a>
                        </td>
                        <td>
                          <a target="_BLANK" class="card-count-heading-new" style="color:<?=$backgroundColorNew;?>!important" href="<?=base_url()?>Reports/ProposalDetailsData/total_in_Small_Laboratory/<?=$meeting_user_id?>/<?=$sdate?>/<?=$edate?>/<?=$task_action_id?>/userwise">
                          <?= $row->total_in_Small_Laboratory; ?>
                          </a>
                        </td>
                        <td>
                          <a target="_BLANK" class="card-count-heading-new" style="color:<?=$backgroundColorNew;?>!important" href="<?=base_url()?>Reports/ProposalDetailsData/total_in_Customize/<?=$meeting_user_id?>/<?=$sdate?>/<?=$edate?>/<?=$task_action_id?>/userwise">
                          <?= $row->total_in_Customize; ?>
                          </a>
                        </td>
                        <td>
                          <a target="_BLANK" class="card-count-heading-new" style="color:<?=$backgroundColorNew;?>!important" href="<?=base_url()?>Reports/ProposalDetailsData/total_in_Smart_Class/<?=$meeting_user_id?>/<?=$sdate?>/<?=$edate?>/<?=$task_action_id?>/userwise">
                          <?= $row->total_in_Smart_Class; ?>
                          </a>
                        </td>







                           <td>
                          <a target="_BLANK" class="card-count-heading-new" style="color:<?=$backgroundColorNew;?>!important" href="<?=base_url()?>Reports/ProposalDetailsData/total_Approved_in_MSC/<?=$meeting_user_id?>/<?=$sdate?>/<?=$edate?>/<?=$task_action_id?>/userwise">
                          <?= $row->total_Approved_in_MSC; ?>
                          </a>
                        </td>
                        <td>
                          <a target="_BLANK" class="card-count-heading-new" style="color:<?=$backgroundColorNew;?>!important" href="<?=base_url()?>Reports/ProposalDetailsData/total_Approved_in_BALA/<?=$meeting_user_id?>/<?=$sdate?>/<?=$edate?>/<?=$task_action_id?>/userwise">
                          <?= $row->total_Approved_in_BALA; ?>
                          </a>
                        </td>
                        <td>
                          <a target="_BLANK" class="card-count-heading-new" style="color:<?=$backgroundColorNew;?>!important" href="<?=base_url()?>Reports/ProposalDetailsData/total_Approved_in_Astronomy/<?=$meeting_user_id?>/<?=$sdate?>/<?=$edate?>/<?=$task_action_id?>/userwise">
                          <?= $row->total_Approved_in_Astronomy; ?>
                          </a>
                        </td>
                        <td>
                          <a target="_BLANK" class="card-count-heading-new" style="color:<?=$backgroundColorNew;?>!important" href="<?=base_url()?>Reports/ProposalDetailsData/total_Approved_in_Tinkering/<?=$meeting_user_id?>/<?=$sdate?>/<?=$edate?>/<?=$task_action_id?>/userwise">
                          <?= $row->total_Approved_in_Tinkering; ?>
                          </a>
                        </td>
                        <td>
                          <a target="_BLANK" class="card-count-heading-new" style="color:<?=$backgroundColorNew;?>!important" href="<?=base_url()?>Reports/ProposalDetailsData/total_Approved_in_Big_Laboratory/<?=$meeting_user_id?>/<?=$sdate?>/<?=$edate?>/<?=$task_action_id?>/userwise">
                          <?= $row->total_Approved_in_Big_Laboratory; ?>
                          </a>
                        </td>
                        <td>
                          <a target="_BLANK" class="card-count-heading-new" style="color:<?=$backgroundColorNew;?>!important" href="<?=base_url()?>Reports/ProposalDetailsData/total_Approved_in_Small_Laboratory/<?=$meeting_user_id?>/<?=$sdate?>/<?=$edate?>/<?=$task_action_id?>/userwise">
                          <?= $row->total_Approved_in_Small_Laboratory; ?>
                          </a>
                        </td>
                        <td>
                          <a target="_BLANK" class="card-count-heading-new" style="color:<?=$backgroundColorNew;?>!important" href="<?=base_url()?>Reports/ProposalDetailsData/total_Approved_in_Customize/<?=$meeting_user_id?>/<?=$sdate?>/<?=$edate?>/<?=$task_action_id?>/userwise">
                          <?= $row->total_Approved_in_Customize; ?>
                          </a>
                        </td>
                        <td>
                          <a target="_BLANK" class="card-count-heading-new" style="color:<?=$backgroundColorNew;?>!important" href="<?=base_url()?>Reports/ProposalDetailsData/total_Approved_in_Smart_Class/<?=$meeting_user_id?>/<?=$sdate?>/<?=$edate?>/<?=$task_action_id?>/userwise">
                          <?= $row->total_Approved_in_Smart_Class; ?>
                          </a>
                        </td>



                            <td>
                          <a target="_BLANK" class="card-count-heading-new" style="color:<?=$backgroundColorNew;?>!important" href="<?=base_url()?>Reports/ProposalDetailsData/total_Reject_in_MSC/<?=$meeting_user_id?>/<?=$sdate?>/<?=$edate?>/<?=$task_action_id?>/userwise">
                          <?= $row->total_Reject_in_MSC; ?>
                          </a>
                        </td>
                        <td>
                          <a target="_BLANK" class="card-count-heading-new" style="color:<?=$backgroundColorNew;?>!important" href="<?=base_url()?>Reports/ProposalDetailsData/total_Reject_in_BALA/<?=$meeting_user_id?>/<?=$sdate?>/<?=$edate?>/<?=$task_action_id?>/userwise">
                          <?= $row->total_Reject_in_BALA; ?>
                          </a>
                        </td>
                        <td>
                          <a target="_BLANK" class="card-count-heading-new" style="color:<?=$backgroundColorNew;?>!important" href="<?=base_url()?>Reports/ProposalDetailsData/total_Reject_in_Astronomy/<?=$meeting_user_id?>/<?=$sdate?>/<?=$edate?>/<?=$task_action_id?>/userwise">
                          <?= $row->total_Reject_in_Astronomy; ?>
                          </a>
                        </td>
                        <td>
                          <a target="_BLANK" class="card-count-heading-new" style="color:<?=$backgroundColorNew;?>!important" href="<?=base_url()?>Reports/ProposalDetailsData/total_Reject_in_Tinkering/<?=$meeting_user_id?>/<?=$sdate?>/<?=$edate?>/<?=$task_action_id?>/userwise">
                          <?= $row->total_Reject_in_Tinkering; ?>
                          </a>
                        </td>
                        <td>
                          <a target="_BLANK" class="card-count-heading-new" style="color:<?=$backgroundColorNew;?>!important" href="<?=base_url()?>Reports/ProposalDetailsData/total_Reject_in_Big_Laboratory/<?=$meeting_user_id?>/<?=$sdate?>/<?=$edate?>/<?=$task_action_id?>/userwise">
                          <?= $row->total_Reject_in_Big_Laboratory; ?>
                          </a>
                        </td>
                        <td>
                          <a target="_BLANK" class="card-count-heading-new" style="color:<?=$backgroundColorNew;?>!important" href="<?=base_url()?>Reports/ProposalDetailsData/total_Reject_in_Small_Laboratory/<?=$meeting_user_id?>/<?=$sdate?>/<?=$edate?>/<?=$task_action_id?>/userwise">
                          <?= $row->total_Reject_in_Small_Laboratory; ?>
                          </a>
                        </td>
                        <td>
                          <a target="_BLANK" class="card-count-heading-new" style="color:<?=$backgroundColorNew;?>!important" href="<?=base_url()?>Reports/ProposalDetailsData/total_Reject_in_Customize/<?=$meeting_user_id?>/<?=$sdate?>/<?=$edate?>/<?=$task_action_id?>/userwise">
                          <?= $row->total_Reject_in_Customize; ?>
                          </a>
                        </td>
                        <td>
                          <a target="_BLANK" class="card-count-heading-new" style="color:<?=$backgroundColorNew;?>!important" href="<?=base_url()?>Reports/ProposalDetailsData/total_Reject_in_Smart_Class/<?=$meeting_user_id?>/<?=$sdate?>/<?=$edate?>/<?=$task_action_id?>/userwise">
                          <?= $row->total_Reject_in_Smart_Class; ?>
                          </a>
                        </td>



                             <td>
                          <a target="_BLANK" class="card-count-heading-new" style="color:<?=$backgroundColorNew;?>!important" href="<?=base_url()?>Reports/ProposalDetailsData/total_Pending_For_Approve_in_MSC/<?=$meeting_user_id?>/<?=$sdate?>/<?=$edate?>/<?=$task_action_id?>/userwise">
                          <?= $row->total_Pending_For_Approve_in_MSC; ?>
                          </a>
                        </td>
                        <td>
                          <a target="_BLANK" class="card-count-heading-new" style="color:<?=$backgroundColorNew;?>!important" href="<?=base_url()?>Reports/ProposalDetailsData/total_Pending_For_Approve_in_BALA/<?=$meeting_user_id?>/<?=$sdate?>/<?=$edate?>/<?=$task_action_id?>/userwise">
                          <?= $row->total_Pending_For_Approve_in_BALA; ?>
                          </a>
                        </td>
                        <td>
                          <a target="_BLANK" class="card-count-heading-new" style="color:<?=$backgroundColorNew;?>!important" href="<?=base_url()?>Reports/ProposalDetailsData/total_Pending_For_Approve_in_Astronomy/<?=$meeting_user_id?>/<?=$sdate?>/<?=$edate?>/<?=$task_action_id?>/userwise">
                          <?= $row->total_Pending_For_Approve_in_Astronomy; ?>
                          </a>
                        </td>
                        <td>
                          <a target="_BLANK" class="card-count-heading-new" style="color:<?=$backgroundColorNew;?>!important" href="<?=base_url()?>Reports/ProposalDetailsData/total_Pending_For_Approve_in_Tinkering/<?=$meeting_user_id?>/<?=$sdate?>/<?=$edate?>/<?=$task_action_id?>/userwise">
                          <?= $row->total_Pending_For_Approve_in_Tinkering; ?>
                          </a>
                        </td>
                        <td>
                          <a target="_BLANK" class="card-count-heading-new" style="color:<?=$backgroundColorNew;?>!important" href="<?=base_url()?>Reports/ProposalDetailsData/total_Pending_For_Approve_in_Big_Laboratory/<?=$meeting_user_id?>/<?=$sdate?>/<?=$edate?>/<?=$task_action_id?>/userwise">
                          <?= $row->total_Pending_For_Approve_in_Big_Laboratory; ?>
                          </a>
                        </td>
                        <td>
                          <a target="_BLANK" class="card-count-heading-new" style="color:<?=$backgroundColorNew;?>!important" href="<?=base_url()?>Reports/ProposalDetailsData/total_Pending_For_Approve_in_Small_Laboratory/<?=$meeting_user_id?>/<?=$sdate?>/<?=$edate?>/<?=$task_action_id?>/userwise">
                          <?= $row->total_Pending_For_Approve_in_Small_Laboratory; ?>
                          </a>
                        </td>
                        <td>
                          <a target="_BLANK" class="card-count-heading-new" style="color:<?=$backgroundColorNew;?>!important" href="<?=base_url()?>Reports/ProposalDetailsData/total_Pending_For_Approve_in_Customize/<?=$meeting_user_id?>/<?=$sdate?>/<?=$edate?>/<?=$task_action_id?>/userwise">
                          <?= $row->total_Pending_For_Approve_in_Customize; ?>
                          </a>
                        </td>
                        <td>
                          <a target="_BLANK" class="card-count-heading-new" style="color:<?=$backgroundColorNew;?>!important" href="<?=base_url()?>Reports/ProposalDetailsData/total_Pending_For_Approve_in_Smart_Class/<?=$meeting_user_id?>/<?=$sdate?>/<?=$edate?>/<?=$task_action_id?>/userwise">
                          <?= $row->total_Pending_For_Approve_in_Smart_Class; ?>
                          </a>
                        </td>



                     
                      </tr>
                      <?php $i++; } ?>
                    </tbody>
                  </table>
                </div>
                <hr />
              </div>




                
                <div class="content-area text-center">
                  <hr><h1>Educational Proposals</h1><hr>

    <div class="proposal">
        <h2>Proposals under MSC (Mathematics and Science Club) Category<hr></h2>
        <p>The MSC category aims to foster a deep appreciation and understanding of mathematics and science among students. These proposals focus on creating clubs that encourage exploration, experimentation, and innovation in these core subjects. Activities may include math puzzles, science fairs, guest lectures from scientists, and hands-on experiments that make learning engaging and fun.</p>
    </div>

    <div class="proposal">
        <h2>Proposals under BALA (Building as Learning Aid)<hr></h2>
        <p>BALA proposals emphasize the use of the school infrastructure itself as a tool for learning. This initiative transforms the physical environment of the school into an interactive learning space. Ideas include educational murals, informative signage, and architectural designs that incorporate educational themes, thereby making every corner of the school a potential learning opportunity.</p>
    </div>

    <div class="proposal">
        <h2>Proposals Related to Astronomy<hr></h2>
        <p>These proposals are designed to spark interest in the cosmos and the sciences related to space exploration. Initiatives may include setting up school observatories, organizing night sky viewing sessions, integrating astronomy into the curriculum, and hosting workshops with astronomers. The goal is to inspire students to look beyond our planet and understand the universe.</p>
    </div>

    <div class="proposal">
        <h2>Proposals under Tinkering Labs<hr></h2>
        <p>Tinkering Labs provide students with the tools and space to engage in hands-on learning through building and experimentation. Proposals in this category might include setting up labs equipped with materials for robotics, electronics, woodworking, and other DIY projects. These labs encourage creativity, problem-solving, and innovation by allowing students to tinker and invent.</p>
    </div>

    <div class="proposal">
        <h2>Proposals for Big Science Laboratories<hr></h2>
        <p>Big Science Laboratories are designed to provide students with access to advanced scientific equipment and resources. These proposals aim to create state-of-the-art labs where students can conduct complex experiments in fields such as chemistry, physics, and biology. The labs will support high-level research projects and provide a deeper understanding of scientific principles.</p>
    </div>

    <div class="proposal">
        <h2>Proposals for Small Science Laboratories<hr></h2>
        <p>Small Science Laboratories focus on making science accessible and engaging at a basic level. These proposals aim to set up smaller, more localized labs that can serve individual classrooms or small groups of students. The labs will be equipped with essential tools and materials to conduct simple experiments, making science hands-on and interactive for younger students.</p>
    </div>

    <div class="proposal">
        <h2>Proposals with Customized Setups<hr></h2>
        <p>Customized setups are tailored to meet the specific needs and interests of a school or student group. These proposals might include specialized equipment, unique learning spaces, or bespoke educational programs designed to address particular educational goals or challenges. The focus is on creating personalized learning experiences that cater to the diverse needs of students.</p>
    </div>

    <div class="proposal">
        <h2>Proposals for Smart Class Initiatives<hr></h2>
        <p>Smart Class initiatives aim to integrate technology into the classroom to enhance the learning experience. Proposals in this category may include setting up digital classrooms equipped with interactive whiteboards, tablets, and educational software. The goal is to create an interactive and engaging learning environment that leverages technology to improve educational outcomes.</p>
    </div>
                </div>





            <br>
            <hr>
            
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










      (function() {
  var Util,
    __bind = function(fn, me){ return function(){ return fn.apply(me, arguments); }; };

  Util = (function() {
    function Util() {}

    Util.prototype.extend = function(custom, defaults) {
      var key, value;
      for (key in custom) {
        value = custom[key];
        if (value != null) {
          defaults[key] = value;
        }
      }
      return defaults;
    };

    Util.prototype.isMobile = function(agent) {
      return /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(agent);
    };

    return Util;

  })();

  this.WOW = (function() {
    WOW.prototype.defaults = {
      boxClass: 'wow',
      animateClass: 'animated',
      offset: 0,
      mobile: true
    };

    function WOW(options) {
      if (options == null) {
        options = {};
      }
      this.scrollCallback = __bind(this.scrollCallback, this);
      this.scrollHandler = __bind(this.scrollHandler, this);
      this.start = __bind(this.start, this);
      this.scrolled = true;
      this.config = this.util().extend(options, this.defaults);
    }

    WOW.prototype.init = function() {
      var _ref;
      this.element = window.document.documentElement;
      if ((_ref = document.readyState) === "interactive" || _ref === "complete") {
        return this.start();
      } else {
        return document.addEventListener('DOMContentLoaded', this.start);
      }
    };

    WOW.prototype.start = function() {
      var box, _i, _len, _ref;
      this.boxes = this.element.getElementsByClassName(this.config.boxClass);
      if (this.boxes.length) {
        if (this.disabled()) {
          return this.resetStyle();
        } else {
          _ref = this.boxes;
          for (_i = 0, _len = _ref.length; _i < _len; _i++) {
            box = _ref[_i];
            this.applyStyle(box, true);
          }
          window.addEventListener('scroll', this.scrollHandler, false);
          window.addEventListener('resize', this.scrollHandler, false);
          return this.interval = setInterval(this.scrollCallback, 50);
        }
      }
    };

    WOW.prototype.stop = function() {
      window.removeEventListener('scroll', this.scrollHandler, false);
      window.removeEventListener('resize', this.scrollHandler, false);
      if (this.interval != null) {
        return clearInterval(this.interval);
      }
    };

    WOW.prototype.show = function(box) {
      this.applyStyle(box);
      return box.className = "" + box.className + " " + this.config.animateClass;
    };

    WOW.prototype.applyStyle = function(box, hidden) {
      var delay, duration, iteration;
      duration = box.getAttribute('data-wow-duration');
      delay = box.getAttribute('data-wow-delay');
      iteration = box.getAttribute('data-wow-iteration');
      return box.setAttribute('style', this.customStyle(hidden, duration, delay, iteration));
    };

    WOW.prototype.resetStyle = function() {
      var box, _i, _len, _ref, _results;
      _ref = this.boxes;
      _results = [];
      for (_i = 0, _len = _ref.length; _i < _len; _i++) {
        box = _ref[_i];
        _results.push(box.setAttribute('style', 'visibility: visible;'));
      }
      return _results;
    };

    WOW.prototype.customStyle = function(hidden, duration, delay, iteration) {
      var style;
      style = hidden ? "visibility: hidden; -webkit-animation-name: none; -moz-animation-name: none; animation-name: none;" : "visibility: visible;";
      if (duration) {
        style += "-webkit-animation-duration: " + duration + "; -moz-animation-duration: " + duration + "; animation-duration: " + duration + ";";
      }
      if (delay) {
        style += "-webkit-animation-delay: " + delay + "; -moz-animation-delay: " + delay + "; animation-delay: " + delay + ";";
      }
      if (iteration) {
        style += "-webkit-animation-iteration-count: " + iteration + "; -moz-animation-iteration-count: " + iteration + "; animation-iteration-count: " + iteration + ";";
      }
      return style;
    };

    WOW.prototype.scrollHandler = function() {
      return this.scrolled = true;
    };

    WOW.prototype.scrollCallback = function() {
      var box;
      if (this.scrolled) {
        this.scrolled = false;
        this.boxes = (function() {
          var _i, _len, _ref, _results;
          _ref = this.boxes;
          _results = [];
          for (_i = 0, _len = _ref.length; _i < _len; _i++) {
            box = _ref[_i];
            if (!(box)) {
              continue;
            }
            if (this.isVisible(box)) {
              this.show(box);
              continue;
            }
            _results.push(box);
          }
          return _results;
        }).call(this);
        if (!this.boxes.length) {
          return this.stop();
        }
      }
    };

    WOW.prototype.offsetTop = function(element) {
      var top;
      top = element.offsetTop;
      while (element = element.offsetParent) {
        top += element.offsetTop;
      }
      return top;
    };

    WOW.prototype.isVisible = function(box) {
      var bottom, offset, top, viewBottom, viewTop;
      offset = box.getAttribute('data-wow-offset') || this.config.offset;
      viewTop = window.pageYOffset;
      viewBottom = viewTop + this.element.clientHeight - offset;
      top = this.offsetTop(box);
      bottom = top + box.clientHeight;
      return top <= viewBottom && bottom >= viewTop;
    };

    WOW.prototype.util = function() {
      return this._util || (this._util = new Util());
    };

    WOW.prototype.disabled = function() {
      return !this.config.mobile && this.util().isMobile(navigator.userAgent);
    };

    return WOW;

  })();

}).call(this);


wow = new WOW(
  {
    animateClass: 'animated',
    offset: 100
  }
);
wow.init();

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
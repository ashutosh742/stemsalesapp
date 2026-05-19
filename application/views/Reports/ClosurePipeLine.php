

            

<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Closure PipeLine Details | STEM APP | WebAPP</title>
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
   
    </style>
  </head>
  <body class="hold-transition sidebar-mini layout-fixed">
    <div class="wrapper">
      <!-- Preloader -->
      <!-- Navbar -->
      <?php $this->load->view($dep_name.'/nav.php');?>

            <?php 


              $totalClosurePipeLineDatas       = $totalFunnnels['totalClosurePipeLineDatas'];
              $totalClosurePipeLineDatasByuser = $totalFunnnels['totalClosurePipeLineDatasByuser'];

              
               // dd($totalClosurePipeLineDatas);
              ?>          


<?php

// Configurable colors (change these variables to alter look)
$bg = '#0f172a';           // page background
$cardBg = '#0b1220';       // card background
$accent = '#06b6d4';       // accent color (teal-ish)
$text = '#e6eef6';         // primary text color
$muted = '#9aa9bd';        // muted text color

// Helper functions
function pretty_label($key) {
    $label = str_replace('_', ' ', $key);
    $label = ucwords($label);
    $label = str_ireplace('Rp Meeting', 'RP Meeting', $label);
    $label = str_ireplace('Sdatet', 'Date', $label);
    return $label;
}

function is_currency_key($k) {
    return (stripos($k, 'budget') !== false) || (stripos($k, 'amount') !== false);
}

function fmt_value($val, $isCurrency=false) {
    if (is_null($val) || $val === '') return '-';
    if ($isCurrency && is_numeric($val)) {
        $abs = abs((float)$val);
        // Use safe numeric thresholds (no underscores) and simple suffixes:
        // 1e12 => trillion, 1e7 => crore (10 million)
        if ($abs >= 1e12) {
            return '₹' . number_format($val / 1e12, 2) . 'T';
        }
        if ($abs >= 1e7) { // 10,000,000 -> 1 Crore
            return '₹' . number_format($val / 1e7, 2) . 'Cr';
        }
        // default rupee formatting with commas
        return '₹' . number_format($val, 2, '.', ',');
    }

    if (is_numeric($val) && floor($val) == $val) {
        return number_format($val);
    }
    if (is_numeric($val)) {
        return number_format($val, 2);
    }
    return htmlspecialchars((string)$val);
}
?>

      <!-- /.navbar -->
      <!-- Content Wrapper. Contains page content -->
         <style>
       
    :root{
      --page-bg: <?= $bg ?>;
      --card-bg: <?= $cardBg ?>;
      --accent: <?= $accent ?>;
      --text: <?= $text ?>;
      --muted: <?= $muted ?>;
      --radius: 14px;
      --card-padding: 18px;
      --glass: rgba(255,255,255,0.03);
    }
   
 
    .header{
      display:flex;
      gap:16px;
      align-items:center;
      justify-content:space-between;
      margin-bottom:20px;
    }
    .title{
      display:flex;
      flex-direction:column;
      color: wheat;
    }
    .title h1{
      margin:0;
      font-size:20px;
      letter-spacing:0.2px;
    }
    .subtitle{
      color:var(--muted);
      font-size:13px;
      margin-top:6px;
    }

    .grid{
      display:grid;
      grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
      gap:18px;
    }

    .card-mattrix{
      background: linear-gradient(180deg, rgba(255,255,255,0.02), rgba(0,0,0,0.02));
      border-radius: var(--radius);
      padding: var(--card-padding);
      box-shadow: 0 6px 18px rgba(2,6,23,0.6), inset 0 1px 0 rgba(255,255,255,0.02);
      border: 1px solid rgba(255,255,255,0.03);
      transition: transform .18s ease, box-shadow .18s ease;
      display:flex;
      flex-direction:column;
      gap:8px;
      min-height:92px;
      align-items: center;
    }
    .card-mattrix:hover{
      transform: translateY(-6px) scale(1.01);
      box-shadow: 0 18px 40px rgba(2,6,23,0.75), 0 2px 12px rgba(0,0,0,0.6);
    }

    .card-mattrix .label{
      font-size:13px;
      color:var(--muted);
      text-transform:none;
      letter-spacing:0.2px;
      font-weight: 700;
      font-size: 18px;
      color: #08b10a;
    }
    .card-mattrix .value{
      font-size:20px;
      font-weight:700;
      color:var(--text);
      display:flex;
      align-items:baseline;
      gap:8px;
      align-items: center; justify-content: center; display: flex; flex-direction: column;
    }
    .card-mattrix .meta{
      font-size:12px;
      color:var(--muted);
    }

    .accent-pill{
      display:inline-block;
      padding:6px 10px;
      border-radius:999px;
      background:linear-gradient(90deg, rgba(255,255,255,0.03), rgba(255,255,255,0.01));
      border: 1px solid rgba(255,255,255,0.03);
      color:var(--text);
      font-weight:600;
      font-size:12px;
      box-shadow: 0 4px 20px rgba(6,182,212,0.06);
    }

    .card-mattrix.currency .value{
      color: var(--accent);
    }
    .legend{
      display:flex;
      gap:12px;
      align-items:center;
      color:var(--muted);
      font-size:13px;
    }

    @media (max-width:560px){
      .title h1{ font-size:16px; }
      .card-mattrix{ padding:14px; min-height:88px; }
      .card-mattrix .value{ font-size:16px; }
    }
   .container-data-details {
    background: linear-gradient(180deg, var(--page-bg) 0%, #061226 100%);
    padding: 50px;
}
.content-wrapper {
    background: linear-gradient(180deg, var(--page-bg) 0%, #061226 100%);
}

tr,td{
  font-weight:600;
  color: white;
}
div#example1_info {
    color: white;
}
div.dataTables_wrapper div.dataTables_filter label {
    font-weight: normal;
    white-space: nowrap;
    text-align: left;
    color: white;
}
td a {
    color: rgb(24 243 0);
}
.value span small {
    color: #06b6d4;
}
  </style>
      <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <div class="content-header">
          <div class="container-fluid">
            <div class="row mb-2">
              <div class="col-md-12 text-center">
                <div class="frame-layer-1">
                  <div class="frame-layer-2">
                   <div class="frame-layer-3" style="background: linear-gradient(to right, #ffffff1c, #ffffff1c); border-radius: 12px; box-shadow: 0 4px 8px rgba(0,0,0,0.1); padding: 20px;">
                      <h1 class="m-0 premium-color-1" style="color: #1df719ff;">
                        🔍 Closure Pipeline Details
                      </h1>
                      <p class="premium-color-2" style="color: #ffffffff;">
                        📊 A quick snapshot of your current pipeline strength, upcoming closures, and performance highlights.
                      </p>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <br>
            <div class="row mb-2" style="align-items: center; justify-content: center;">
              <div class="text-right-data text-center" style="background: linear-gradient(to right, #b2d6ff, #ffffff); border-radius: 12px; box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1); padding: 20px; width: 60%;">
                <?php 
                  $clusterPstDatas  = $this->Menu_model->GetAdminActiveTeam($user['user_id']);
                  $taskActions      = $this->Menu_model->get_action();
                  $userRoles        = $this->Menu_model->GetActiveDepartmentList($uid);
                  ?>
                <div class="col text-center formcenterData">
                  <div>
                    <hr>
                    <form action="<?=base_url()?>Reports/ClosurePipeLine" method="post" class="mt-3">
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
                        <div class="col text-center">
                          <div class="form-group">
                            <button type="submit" class="btn btn-primary p-1" style="margin-top: 33px; width: 100px;">Filter</button>
                          </div>
                        </div>
                      </div>
                    </form>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
        <!-- Main content -->
       <hr style="background-color:#285d00">
        <section class="content">
          <div class="container-fluid">
      


<?php 

// dd($totalClosurePipeLineDatas);


$total_funnel                                                 = $totalClosurePipeLineDatas[0]->total_funnel;
$total_funnel_complete_rp_meeting                             = $totalClosurePipeLineDatas[0]->total_funnel_complete_rp_meeting;
$total_funnel_pending_for_rp_meeting                          = $totalClosurePipeLineDatas[0]->total__funnel_pending_for_rp_meeting;

$total_call_connected_after_rp_meeting                        = $totalClosurePipeLineDatas[0]->total_call_connected_after_rp_meeting;
$total_call_not_connected_after_rp_meeting                    = $totalClosurePipeLineDatas[0]->total_call_not_connected_after_rp_meeting;

$total_proposal_sent                                          = $totalClosurePipeLineDatas[0]->total_proposal_sent;
$total_pending_for_proposal_sent                              = $totalClosurePipeLineDatas[0]->total_pending_for_proposal_sent;

$total_proposal_sent_without_meeting                          = $totalClosurePipeLineDatas[0]->total_proposal_sent_without_meeting;

$total_closure                                                = $totalClosurePipeLineDatas[0]->total_closure;
$total_direct_closure_without_proposal_sent                   = $totalClosurePipeLineDatas[0]->total_direct_closure_without_proposal_sent;
$total_closure_after_proposal_sent                            = $totalClosurePipeLineDatas[0]->total_closure_after_proposal_sent;

$total_not_closure_after_proposal_sent                        = $totalClosurePipeLineDatas[0]->total_not_closure_after_proposal_sent;

$total_budget_where_proposal_sent                             = $totalClosurePipeLineDatas[0]->total_budget_where_proposal_sent;

$total_closure_budget_where_proposal_sent                     = $totalClosurePipeLineDatas[0]->total_closure_budget_where__proposal_sent;
$total_not_closure_budget_where_proposal_sent                 = $totalClosurePipeLineDatas[0]->total_not_closure_budget_where__proposal_sent;

$total_call_connected_after_proposal_sent                     = $totalClosurePipeLineDatas[0]->total_call_connected_after_proposal_sent;
$total_call_not_connected_after_proposal_sent                 = $totalClosurePipeLineDatas[0]->total_call_not_connected_after_proposal_sent;

$total_call_connected_after_proposal_sent_without_meeting     = $totalClosurePipeLineDatas[0]->total_call_connected_after_proposal_sent_without_meeting;
$total_call_not_connected_after_proposal_sent_without_meeting = $totalClosurePipeLineDatas[0]->total_call_not_connected_after_proposal_sent_without_meeting;

$total_line_manager_call_connected_after_rp_meeting           = $totalClosurePipeLineDatas[0]->total_line_manager_call_connected_after_rp_meeting;
$total_line_manager_call_connected_after_proposal_sent        = $totalClosurePipeLineDatas[0]->total_line_manager_call_connected_after_proposal_sent;

$total_funnel_complete_rp_meeting_by_current_bd               = $totalClosurePipeLineDatas[0]->total_funnel_complete_rp_meeting_by_current_bd;
$total_funnel_complete_rp_meeting_by_other_bd                 = $totalClosurePipeLineDatas[0]->total_funnel_complete_rp_meeting_by_other_bd;


$total_proposal_sent_by_current_bd                            = $totalClosurePipeLineDatas[0]->total_proposal_sent_by_current_bd;
$total_proposal_sent_by_other_bd                              = $totalClosurePipeLineDatas[0]->total_proposal_sent_by_other_bd;
$total_funnel_complete_rp_meeting_by_line_manager             = $totalClosurePipeLineDatas[0]->total_funnel_complete_rp_meeting_by_line_manager;

$total_proposal_sent_remeeting_done                           = $totalClosurePipeLineDatas[0]->total_proposal_sent_remeeting_done;
$total_proposal_sent_remeeting_not_done                       = $totalClosurePipeLineDatas[0]->total_proposal_sent_remeeting_not_done;
$total_wdl_or_ni_after_proposal_sent                          = $totalClosurePipeLineDatas[0]->total_wdl_or_ni_after_proposal_sent;

function formatNumber($num) {
    return number_format($num);
}
function formatIndianUnits($amount, $decimals = 2, $symbol = '₹') {
    // Keep sign
    $sign = $amount < 0 ? '-' : '';
    $abs = abs($amount);

    if ($abs >= 10000000) { // >= 1 crore
        $value = $abs / 10000000;
        $formatted = number_format($value, $decimals, '.', ',') . 'Cr';
    } elseif ($abs >= 100000) { // >= 1 lakh
        $value = $abs / 100000;
        $formatted = number_format($value, $decimals, '.', ',') . 'L';
    } elseif ($abs >= 1000) { // >= 1 thousand
        $value = $abs / 1000;
        $formatted = number_format($value, $decimals, '.', ',') . 'K';
    } else {
        $formatted = number_format($abs, $decimals, '.', ',');
    }

    return $sign . $symbol . $formatted;
}


?>

  <div class="container-data-details">
  <div class="header">
    <div class="title">
      <h1>📈 Sales / RP / Proposal Metrics  </h1>
      <div class="subtitle">📊 Snapshot of the dataset By Team</div>
    </div>
    <div style="display:flex;gap:12px;align-items:center;">
      <div class="legend">
        <span style="color:var(--muted)">Generated: <?= date('j M Y, H:i') ?></span>
      </div>
    </div>
  </div>

<hr style="background-color:#285d00">
  <div class="grid" style="grid-template-columns: repeat(auto-fill, minmax(240px, 1fr));">
    <div class="text-center card-mattrix ">
      <div class="label">📊 Total Funnel</div>
      <div class="value">
        <span>
        <a href="<?=base_url()?>Reports/FunnelDetails/total/<?= $uid; ?>" target="_blank"><?= formatNumber($total_funnel) ?></a>
        </span>
      </div>
    </div>
    <div class="text-center card-mattrix ">
      <div class="label">✅ Total Funnel Complete RP Meeting</div>
      <div class="value">
        <a href="<?=base_url()?>Reports/ClosurePipeLineDetails/total_funnel_complete_rp_meeting/<?=$uid?>/<?=$sdate?>/<?=$edate?>" target="_blank"><?= formatNumber($total_funnel_complete_rp_meeting) ?></a>

         <span>
          <small>
            <?php 
            $percentage = number_format(($total_funnel_complete_rp_meeting / $total_funnel) * 100, 2, '.', ''). '%';
            echo $percentage;
            ?> 
          </small>
        </span>
      </div>
    </div>


    <div class="text-center card-mattrix ">
      <div class="label">✅ Total Funnel Complete RP Meeting By Current BD</div>
      <div class="value">
        <a href="<?=base_url()?>Reports/ClosurePipeLineDetails/total_funnel_complete_rp_meeting_by_current_bd/<?=$uid?>/<?=$sdate?>/<?=$edate?>" target="_blank"><?= formatNumber($total_funnel_complete_rp_meeting_by_current_bd) ?></a>
         <span>
          <small>
            <?php 
            $percentage = number_format(($total_funnel_complete_rp_meeting_by_current_bd / $total_funnel_complete_rp_meeting) * 100, 2, '.', ''). '%';
            echo $percentage;
            ?> 
          </small>
        </span>
      </div>
    </div>

    <div class="text-center card-mattrix ">
      <div class="label">✅ Total Funnel Complete RP Meeting By Change BD</div>
      <div class="value">
        <a href="<?=base_url()?>Reports/ClosurePipeLineDetails/total_funnel_complete_rp_meeting_by_other_bd/<?=$uid?>/<?=$sdate?>/<?=$edate?>" target="_blank"><?= formatNumber($total_funnel_complete_rp_meeting_by_other_bd) ?></a>
         <span>
          <small>
            <?php 
            $percentage = number_format(($total_funnel_complete_rp_meeting_by_other_bd / $total_funnel_complete_rp_meeting) * 100, 2, '.', ''). '%';
            echo $percentage;
            ?> 
          </small>
        </span>
      </div>
    </div>
    <div class="text-center card-mattrix ">
      <div class="label">✅ Total Funnel Complete RP Meeting By Line Manager</div>
      <div class="value">
        <a href="<?=base_url()?>Reports/ClosurePipeLineDetails/total_funnel_complete_rp_meeting_by_line_manager/<?=$uid?>/<?=$sdate?>/<?=$edate?>" target="_blank"><?= formatNumber($total_funnel_complete_rp_meeting_by_line_manager) ?></a>
         <span>
          <small>
            <?php 
            $percentage = number_format(($total_funnel_complete_rp_meeting_by_line_manager / $total_funnel_complete_rp_meeting) * 100, 2, '.', ''). '%';
            echo $percentage;
            ?> 
          </small>
        </span>
      </div>
    </div>


    <div class="text-center card-mattrix ">
      <div class="label">⏳ Total  Funnel Pending For RP Meeting</div>
      <div class="value">
        <a href="<?=base_url()?>Reports/ClosurePipeLineDetails/total_funnel_pending_for_rp_meeting/<?=$uid?>/<?=$sdate?>/<?=$edate?>" target="_blank"><?= formatNumber($total_funnel_pending_for_rp_meeting) ?></a> 
        <span>
          <small>
            <?php 
            $percentage = number_format(($total_funnel_pending_for_rp_meeting / $total_funnel) * 100, 2, '.', ''). '%';
            echo $percentage;
            ?> 
          </small>
        </span>
      </div>
    </div>
    <div class="text-center card-mattrix ">
      <div class="label">📞 Total Call Connected After RP Meeting</div>
      <div class="value">
        <a href="<?=base_url()?>Reports/ClosurePipeLineDetails/total_call_connected_after_rp_meeting/<?=$uid?>/<?=$sdate?>/<?=$edate?>" target="_blank"><?= formatNumber($total_call_connected_after_rp_meeting) ?></a>
        <span>
          <small>
            <?php 
            $percentage = number_format(($total_call_connected_after_rp_meeting / $total_funnel_complete_rp_meeting) * 100, 2, '.', ''). '%';
            echo $percentage;
            ?> 
          </small>
        </span>
      </div>
    </div>
    <div class="text-center card-mattrix ">
      <div class="label">📞 Total Line Manager Call Connected After RP Meeting</div>
      <div class="value">
        <a href="<?=base_url()?>Reports/ClosurePipeLineDetails/total_line_manager_call_connected_after_rp_meeting/<?=$uid?>/<?=$sdate?>/<?=$edate?>" target="_blank"><?= formatNumber($total_line_manager_call_connected_after_rp_meeting) ?></a>
        <span>
          <small>
            <?php 
            $percentage = number_format(($total_line_manager_call_connected_after_rp_meeting / $total_funnel_complete_rp_meeting) * 100, 2, '.', ''). '%';
            echo $percentage;
            ?> 
          </small>
        </span>
      </div>
    </div>
    <div class="text-center card-mattrix ">
      <div class="label">📴 Total Call Not Connected After RP Meeting</div>
      <div class="value">
        <a href="<?=base_url()?>Reports/ClosurePipeLineDetails/total_call_not_connected_after_rp_meeting/<?=$uid?>/<?=$sdate?>/<?=$edate?>" target="_blank"><?= formatNumber($total_call_not_connected_after_rp_meeting) ?></a>
        <span>
          <small>
            <?php 
            $percentage = number_format(($total_call_not_connected_after_rp_meeting / $total_funnel_complete_rp_meeting) * 100, 2, '.', ''). '%';
            echo $percentage;
            ?> 
          </small>
        </span>
      </div>
    </div>
  </div>

<hr style="background-color:#285d00">
  <div class="grid" style="grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));">
  <div class="text-center card-mattrix ">
    <div class="label">✅  Total Funnel Complete RP Meeting</div>
    <div class="value">
      <a href="<?=base_url()?>Reports/ClosurePipeLineDetails/total_funnel_complete_rp_meeting/<?=$uid?>/<?=$sdate?>/<?=$edate?>" target="_blank"><?= formatNumber($total_funnel_complete_rp_meeting) ?></a>
      <span>
          <small>
            <?php 
            $percentage = number_format(($total_funnel_complete_rp_meeting / $total_funnel) * 100, 2, '.', ''). '%';
            echo $percentage;
            ?> 
          </small>
        </span>
    </div>
  </div>
   <div class="text-center card-mattrix ">
      <div class="label">📨 Total Proposal Sent Where RP Meeting</div>
      <div class="value">
        <a href="<?=base_url()?>Reports/ClosurePipeLineDetails/total_proposal_sent/<?=$uid?>/<?=$sdate?>/<?=$edate?>" target="_blank"><?= formatNumber($total_proposal_sent) ?></a>
        <span>
          <small>
            <?php 
            $percentage = number_format(($total_proposal_sent / $total_funnel_complete_rp_meeting) * 100, 2, '.', ''). '%';
            echo $percentage;
            ?> 
          </small>
        </span>
      </div>
    </div>
    <div class="text-center card-mattrix ">
      <div class="label">⏳ Total Pending For Proposal Sent</div>
      <div class="value">
        <a href="<?=base_url()?>Reports/ClosurePipeLineDetails/total_pending_for_proposal_sent/<?=$uid?>/<?=$sdate?>/<?=$edate?>" target="_blank"><?= formatNumber($total_pending_for_proposal_sent) ?></a>
        <span>
          <small>
            <?php 
            $percentage = number_format(($total_pending_for_proposal_sent / $total_funnel_complete_rp_meeting) * 100, 2, '.', ''). '%';
            echo $percentage;
            ?> 
          </small>
        </span>
      </div>
    </div>


    <div class="text-center card-mattrix ">
      <div class="label">📨 Total Proposal Sent Where RP Meeting By Current BD</div>
      <div class="value">
        <a href="<?=base_url()?>Reports/ClosurePipeLineDetails/total_proposal_sent_by_current_bd/<?=$uid?>/<?=$sdate?>/<?=$edate?>" target="_blank"><?= formatNumber($total_proposal_sent_by_current_bd) ?></a>
        <span>
          <small>
            <?php 
            $percentage = number_format(($total_proposal_sent_by_current_bd / $total_proposal_sent) * 100, 2, '.', ''). '%';
            echo $percentage;
            ?> 
          </small>
        </span>
      </div>
    </div>


    <div class="text-center card-mattrix ">
      <div class="label">📨 Total Proposal Sent Where RP Meeting By Change BD</div>
      <div class="value">
        <a href="<?=base_url()?>Reports/ClosurePipeLineDetails/total_proposal_sent_by_other_bd/<?=$uid?>/<?=$sdate?>/<?=$edate?>" target="_blank"><?= formatNumber($total_proposal_sent_by_other_bd) ?></a>
        <span>
          <small>
            <?php 
            $percentage = number_format(($total_proposal_sent_by_other_bd / $total_proposal_sent) * 100, 2, '.', ''). '%';
            echo $percentage;
            ?> 
          </small>
        </span>
      </div>
    </div>



    <div class="text-center card-mattrix ">
      <div class="label">📨 Total Proposal Sent Without Meeting</div>
      <div class="value">
        <a href="<?=base_url()?>Reports/ClosurePipeLineDetails/total_proposal_sent_without_meeting/<?=$uid?>/<?=$sdate?>/<?=$edate?>" target="_blank"><?= formatNumber($total_proposal_sent_without_meeting) ?></a>
        <span>
          <small>
            <?php 
            $percentage = number_format(($total_proposal_sent_without_meeting / $total_funnel) * 100, 2, '.', ''). '%';
            echo $percentage;
            ?> 
          </small>
        </span>
      </div>
    </div>
    <div class="text-center card-mattrix ">
      <div class="label">🔒 Total Closure</div>
      <div class="value">
        <a href="<?=base_url()?>Reports/ClosurePipeLineDetails/total_closure/<?=$uid?>/<?=$sdate?>/<?=$edate?>" target="_blank"><?= formatNumber($total_closure) ?></a>
        <span>
          <small>
            <?php 
            $percentage = number_format(($total_closure / $total_funnel) * 100, 2, '.', ''). '%';
            echo $percentage;
            ?> 
          </small>
        </span>
      </div>
    </div>
    <div class="text-center card-mattrix ">
      <div class="label">🔒 Total Closure After Proposal Sent Where RP Meeting</div>
      <div class="value">
        <a href="<?=base_url()?>Reports/ClosurePipeLineDetails/total_closure_after_proposal_sent/<?=$uid?>/<?=$sdate?>/<?=$edate?>" target="_blank"><?= formatNumber($total_closure_after_proposal_sent) ?></a>
        <span>
          <small>
            <?php 
            $percentage = number_format(($total_closure_after_proposal_sent / $total_proposal_sent) * 100, 2, '.', ''). '%';
            echo $percentage;
            ?> 
          </small>
        </span>
      </div>
    </div>
    <div class="text-center card-mattrix ">
      <div class="label">❗Total Closure Without Proposal Sent</div>
      <div class="value">
        <a href="<?=base_url()?>Reports/ClosurePipeLineDetails/total_direct_closure_without_proposal_sent/<?=$uid?>/<?=$sdate?>/<?=$edate?>" target="_blank"><?= formatNumber($total_direct_closure_without_proposal_sent) ?></a>
        <span>
          <small>
            <?php 
            $percentage = number_format(($total_direct_closure_without_proposal_sent / $total_funnel) * 100, 2, '.', ''). '%';
            echo $percentage;
            ?> 
          </small>
        </span>
      </div>
    </div>
</div>


<hr style="background-color:#285d00">
  <div class="grid">

   <div class="text-center card-mattrix ">
      <div class="label">📨 Total Proposal Sent Where RP Meeting</div>
      <div class="value">
        <a href="<?=base_url()?>Reports/ClosurePipeLineDetails/total_proposal_sent/<?=$uid?>/<?=$sdate?>/<?=$edate?>" target="_blank"><?= formatNumber($total_proposal_sent) ?></a>
        <span>
          <small>
            <?php 
            $percentage = number_format(($total_proposal_sent / $total_funnel_complete_rp_meeting) * 100, 2, '.', ''). '%';
            echo $percentage;
            ?> 
          </small>
        </span>
      </div>
    </div>
      <div class="text-center card-mattrix currency">
      <div class="label">💰 Total Budget Where Proposal Sent</div>
      <div class="value">
        <a href="<?=base_url()?>Reports/ClosurePipeLineDetails/total_budget_where_proposal_sent/<?=$uid?>/<?=$sdate?>/<?=$edate?>" target="_blank"><?= formatIndianUnits($total_budget_where_proposal_sent); ?></a>
      </div>
    </div>
    <div class="text-center card-mattrix currency">
      <div class="label">🔒 Total Closure Budget Where  Proposal Sent</div>
      <div class="value">
        <a href="<?=base_url()?>Reports/ClosurePipeLineDetails/total_closure_budget_where_proposal_sent/<?=$uid?>/<?=$sdate?>/<?=$edate?>" target="_blank"><?= formatIndianUnits($total_closure_budget_where_proposal_sent); ?></a>
         <span>
          <small>
            <?php 
            $percentage = number_format(($total_closure_budget_where_proposal_sent / $total_budget_where_proposal_sent) * 100,2, '.', ''). '%';
            echo $percentage;
            ?> 
          </small>
        </span>
      </div>
    </div>
    <div class="text-center card-mattrix currency">
      <div class="label">🚫 Total Not Closure Budget Where  Proposal Sent</div>
      <div class="value">
        <a href="<?=base_url()?>Reports/ClosurePipeLineDetails/total_not_closure_budget_where_proposal_sent/<?=$uid?>/<?=$sdate?>/<?=$edate?>" target="_blank"><?= formatIndianUnits($total_not_closure_budget_where_proposal_sent); ?></a>
        <span>
          <small>
            <?php 
            $percentage = number_format(($total_not_closure_budget_where_proposal_sent / $total_budget_where_proposal_sent) * 100, 2, '.', ''). '%';
            echo $percentage;
            ?> 
          </small>
        </span>
      </div>
    </div>

</div>



<hr style="background-color:#285d00">
  <div class="grid">
    <div class="text-center card-mattrix ">
      <div class="label">📨 Total Proposal Sent</div>
      <div class="value">
        <a href="<?=base_url()?>Reports/ClosurePipeLineDetails/total_proposal_sent/<?=$uid?>/<?=$sdate?>/<?=$edate?>" target="_blank"><?= formatNumber($total_proposal_sent) ?></a>
         <span>
          <small>
            <?php 
            $percentage = number_format(($total_proposal_sent / $total_funnel_complete_rp_meeting) * 100, 2, '.', ''). '%';
            echo $percentage;
            ?> 
          </small>
        </span>
      </div>
    </div>
    <div class="text-center card-mattrix ">
      <div class="label">📞 Total Call Connected After Proposal Sent</div>
      <div class="value">
        <a href="<?=base_url()?>Reports/ClosurePipeLineDetails/total_call_connected_after_proposal_sent/<?=$uid?>/<?=$sdate?>/<?=$edate?>" target="_blank"><?= formatNumber($total_call_connected_after_proposal_sent) ?></a>
         <span>
          <small>
            <?php 
            $percentage = number_format(($total_call_connected_after_proposal_sent / $total_proposal_sent) * 100, 2, '.', ''). '%';
            echo $percentage;
            ?> 
          </small>
        </span>
      </div>
    </div>
    <div class="text-center card-mattrix ">
      <div class="label">📞 Total Line Manager Call Connected After Proposal Sent</div>
      <div class="value">
        <a href="<?=base_url()?>Reports/ClosurePipeLineDetails/total_line_manager_call_connected_after_proposal_sent/<?=$uid?>/<?=$sdate?>/<?=$edate?>" target="_blank"><?= formatNumber($total_line_manager_call_connected_after_proposal_sent) ?></a>
        <span>
          <small>
            <?php 
            $percentage = number_format(($total_line_manager_call_connected_after_proposal_sent / $total_proposal_sent) * 100, 2, '.', ''). '%';
            echo $percentage;
            ?> 
          </small>
        </span>
      </div>
    </div>
    <div class="text-center card-mattrix ">
      <div class="label">📴 Total Call Not Connected After Proposal Sent</div>
      <div class="value">
        <a href="<?=base_url()?>Reports/ClosurePipeLineDetails/total_call_not_connected_after_proposal_sent/<?=$uid?>/<?=$sdate?>/<?=$edate?>" target="_blank"><?= formatNumber($total_call_not_connected_after_proposal_sent) ?></a>
        <span>
          <small>
            <?php 
            $percentage = number_format(($total_call_not_connected_after_proposal_sent / $total_proposal_sent) * 100, 2, '.', ''). '%';
            echo $percentage;
            ?> 
          </small>
        </span>
      </div>
    </div>
  </div>

<hr style="background-color:#285d00">
  <div class="grid">

 <div class="text-center card-mattrix ">
      <div class="label">📨 Total Proposal Sent Without Meeting</div>
      <div class="value">
        <a href="<?=base_url()?>Reports/ClosurePipeLineDetails/total_proposal_sent_without_meeting/<?=$uid?>/<?=$sdate?>/<?=$edate?>" target="_blank"><?= formatNumber($total_proposal_sent_without_meeting) ?></a>

         <span>
          <small>
            <?php 
            if($total_proposal_sent_without_meeting > 0){
              $percentage = number_format(($total_proposal_sent_without_meeting / $total_funnel) * 100, 2, '.', ''). '%';
              echo $percentage;
              }else{
                echo "0%";
            }
            ?> 
          </small>
        </span>

      </div>
    </div>
    <div class="text-center card-mattrix ">
      <div class="label">📞 Total Call Connected After Proposal Sent Without Meeting</div>
      <div class="value">
        <a href="<?=base_url()?>Reports/ClosurePipeLineDetails/total_call_connected_after_proposal_sent_without_meeting/<?=$uid?>/<?=$sdate?>/<?=$edate?>" target="_blank"><?= $total_call_connected_after_proposal_sent_without_meeting ?></a>
         <span>
          <small>
            <?php 
            if($total_proposal_sent_without_meeting > 0){
              $percentage = number_format(($total_call_connected_after_proposal_sent_without_meeting / $total_proposal_sent_without_meeting) * 100, 2, '.', ''). '%';
              echo $percentage;
              }else{
                echo "0%";
            }
            ?> 
          </small>
        </span>
      </div>
    </div>
    <div class="text-center card-mattrix ">
      <div class="label">📴 Total Call Not Connected After Proposal Sent Without Meeting</div>
      <div class="value">
        <a href="<?=base_url()?>Reports/ClosurePipeLineDetails/total_call_not_connected_after_proposal_sent_without_meeting/<?=$uid?>/<?=$sdate?>/<?=$edate?>" target="_blank"><?= $total_call_not_connected_after_proposal_sent_without_meeting ?></a>
        <span>
          <small>
            <?php 
            if($total_proposal_sent_without_meeting > 0){
            $percentage = number_format(($total_call_not_connected_after_proposal_sent_without_meeting / $total_proposal_sent_without_meeting) * 100, 2, '.', ''). '%';
            echo $percentage;
            }else{
               echo "0%";
            }
            ?> 
          </small>
        </span>
      </div>
    </div>
  </div>



  



<hr style="background-color:#285d00">
  <div class="grid">

 <div class="text-center card-mattrix ">
      <div class="label">📨 Proposal Sent & Re-Meeting Done</div>
      <div class="value">
        <a href="<?=base_url()?>Reports/ClosurePipeLineDetails/total_proposal_sent_remeeting_done/<?=$uid?>/<?=$sdate?>/<?=$edate?>" target="_blank"><?= formatNumber($total_proposal_sent_remeeting_done) ?></a>

         <span>
          <small>
            <?php 
            if($total_proposal_sent_remeeting_done > 0){
              $percentage = number_format(($total_proposal_sent_remeeting_done / $total_proposal_sent) * 100, 2, '.', ''). '%';
              echo $percentage;
              }else{
                echo "0%";
            }
            ?> 
          </small>
        </span>

      </div>
    </div>
    <div class="text-center card-mattrix ">
      <div class="label">📨 Proposal Sent & Re-Meeting Not Done</div>
      <div class="value">
        <a href="<?=base_url()?>Reports/ClosurePipeLineDetails/total_proposal_sent_remeeting_not_done/<?=$uid?>/<?=$sdate?>/<?=$edate?>" target="_blank"><?= $total_proposal_sent_remeeting_not_done ?></a>
         <span>
          <small>
            <?php 
            if($total_proposal_sent_remeeting_not_done > 0){
              $percentage = number_format(($total_proposal_sent_remeeting_not_done / $total_proposal_sent) * 100, 2, '.', ''). '%';
              echo $percentage;
              }else{
                echo "0%";
            }
            ?> 
          </small>
        </span>
      </div>
    </div>
  </div>
<hr style="background-color:#285d00">
  <div class="grid">

 <div class="text-center card-mattrix ">
      <div class="label">📨 Proposal Sent <br> Will do Later / Not Interested</div>
      <div class="value">
        <a href="<?=base_url()?>Reports/ClosurePipeLineDetails/total_wdl_or_ni_after_proposal_sent/<?=$uid?>/<?=$sdate?>/<?=$edate?>" target="_blank"><?= formatNumber($total_wdl_or_ni_after_proposal_sent) ?></a>

         <span>
          <small>
            <?php 
            if($total_wdl_or_ni_after_proposal_sent > 0){
              $percentage = number_format(($total_wdl_or_ni_after_proposal_sent / $total_proposal_sent) * 100, 2, '.', ''). '%';
              echo $percentage;
              }else{
                echo "0%";
            }
            ?> 
          </small>
        </span>

      </div>
    </div>
  </div>










<hr style="background-color:#285d00">

              <div class="card-new-table">
                <div class="header">
              <div class="title">
                  <h1>📈 Sales / RP / Proposal / Closure Metrics  </h1>
                  <div class="subtitle">📊 Snapshot of the dataset By User</div>
              </div>
              <div style="display:flex;gap:12px;align-items:center;">
                <div class="legend">
                  <span style="color:var(--muted)">Generated: <?= date('j M Y, H:i') ?></span>
                </div>
              </div>
            </div>
              <div class="page-header">
                <div class="table-responsive text-nowrap">
                <table class="table table-bordered table-striped text-center" id="example1">
    <thead style="color:#fff;background-color: #000;">
        <tr>
            <th># Sr No</th>
            <th>👤 BD Name</th>
            <th>🧩 User Cluster Zone</th>
            <th>📊 Total Funnel</th>
            <th>📅 Total Funnel Complete RP Meeting</th>
            <th>⏳ Total Funnel Pending For RP Meeting</th>

            <th>📅 Total Funnel Complete RP Meeting By Change BD</th>
            <th>📅 Total Funnel Complete RP Meeting By Line Manager</th>

            <th>📞 Total Call Connected After RP Meeting</th>
            <th>📞 Total Line Manager Call Connected After RP Meeting</th>
            <th>📵 Total Call Not Connected After RP Meeting</th>

            <th>📤 Total Proposal Sent</th>
            <th>🕗 Total Pending For Proposal Sent</th>
            <th>🚫📅 Total Proposal Sent Without Meeting</th>

            <th>📨 Proposal Sent & Re-Meeting Done</th>
            <th>📨 Proposal Sent & Re-Meeting Not Done</th>

            <th>📤 Total Proposal Sent Where RP Meeting By Current BD</th>
            <th>📤 Total Proposal Sent Where RP Meeting By Change BD</th>

            <th>🏁 Total Closure</th>
            <th>⚠️🏁 Total Direct Closure Without Proposal Sent</th>
            <th>📩🏁 Total Closure After Proposal Sent</th>
            <th>📩❌ Total Not Closure After Proposal Sent</th>

            <th>💰 Total Taget Budget </th>
            <th>💰 Total Budget Where Proposal Sent</th>
            <th>💼🏁 Total Closure Budget Where Proposal Sent</th>
            <th>💼❌ Total Not Closure Budget Where Proposal Sent</th>

            <th>💰 Remaining Taget Budget </th>
            <th>💰 Taget Achived ? </th>

             <th>📨 Proposal Sent & Will do Later / Not Interested</th>

            <th>📞📩 Total Call Connected After Proposal Sent</th>
            <th>📞📩 Total Line Manager Call Connected After Proposal Sent</th>

            <th>📵📩 Total Call Not Connected After Proposal Sent</th>
            <th>📞🚫📅 Total Call Connected After Proposal Sent Without Meeting</th>
            <th>📵🚫📅 Total Call Not Connected After Proposal Sent Without Meeting</th>
        </tr>
    </thead>
    <tbody>
        <?php $k=1; foreach ($totalClosurePipeLineDatasByuser as $row) { 

                                  
        ?>
        <tr>
                <td><?= $k ?></td>
                <td><?= $row->username ?></td>
                <td><?= $row->user_cluster_zone ?></td>
                <td> <a href="<?=base_url()?>Reports/FunnelDetails/total/<?= $row->user_id; ?>/userwise" target="_blank"><?= formatNumber($row->total_funnel) ?></a></td>
                <td><a target="_blank" href="<?= base_url()?>Reports/ClosurePipeLineDetails/total_funnel_complete_rp_meeting/<?= $row->user_id; ?>/<?= $sdate; ?>/<?= $edate; ?>/userwise"><?= $row->total_funnel_complete_rp_meeting ?></a></td>

                <td><a target="_blank" href="<?= base_url()?>Reports/ClosurePipeLineDetails/total_funnel_pending_for_rp_meeting/<?= $row->user_id; ?>/<?= $sdate; ?>/<?= $edate; ?>/userwise"><?= $row->total__funnel_pending_for_rp_meeting ?></a></td>

                <td><a target="_blank" href="<?= base_url()?>Reports/ClosurePipeLineDetails/total_funnel_complete_rp_meeting_by_other_bd/<?= $row->user_id; ?>/<?= $sdate; ?>/<?= $edate; ?>/userwise"><?= $row->total_funnel_complete_rp_meeting_by_other_bd ?></a></td>

                <td><a target="_blank" href="<?= base_url()?>Reports/ClosurePipeLineDetails/total_funnel_complete_rp_meeting_by_line_manager/<?= $row->user_id; ?>/<?= $sdate; ?>/<?= $edate; ?>/userwise"><?= $row->total_funnel_complete_rp_meeting_by_line_manager ?></a></td>

                <td><a target="_blank" href="<?= base_url()?>Reports/ClosurePipeLineDetails/total_call_connected_after_rp_meeting/<?= $row->user_id; ?>/<?= $sdate; ?>/<?= $edate; ?>/userwise"><?= $row->total_call_connected_after_rp_meeting ?></a></td>

                <td><a target="_blank" href="<?= base_url()?>Reports/ClosurePipeLineDetails/total_line_manager_call_connected_after_rp_meeting/<?= $row->user_id; ?>/<?= $sdate; ?>/<?= $edate; ?>/userwise"><?= $row->total_line_manager_call_connected_after_rp_meeting ?></a></td>

                <td><a target="_blank" href="<?= base_url()?>Reports/ClosurePipeLineDetails/total_call_not_connected_after_rp_meeting/<?= $row->user_id; ?>/<?= $sdate; ?>/<?= $edate; ?>/userwise"><?= $row->total_call_not_connected_after_rp_meeting ?></a></td>

                <td><a target="_blank" href="<?= base_url()?>Reports/ClosurePipeLineDetails/total_proposal_sent/<?= $row->user_id; ?>/<?= $sdate; ?>/<?= $edate; ?>/userwise"><?= $row->total_proposal_sent ?></a></td>

                <td><a target="_blank" href="<?= base_url()?>Reports/ClosurePipeLineDetails/total_pending_for_proposal_sent/<?= $row->user_id; ?>/<?= $sdate; ?>/<?= $edate; ?>/userwise"><?= $row->total_pending_for_proposal_sent ?></a></td>

                <td><a target="_blank" href="<?= base_url()?>Reports/ClosurePipeLineDetails/total_proposal_sent_without_meeting/<?= $row->user_id; ?>/<?= $sdate; ?>/<?= $edate; ?>/userwise"><?= $row->total_proposal_sent_without_meeting ?></a></td>

                <td><a target="_blank" href="<?= base_url()?>Reports/ClosurePipeLineDetails/total_proposal_sent_remeeting_done/<?= $row->user_id; ?>/<?= $sdate; ?>/<?= $edate; ?>/userwise"><?= $row->total_proposal_sent_remeeting_done ?></a></td>

                <td><a target="_blank" href="<?= base_url()?>Reports/ClosurePipeLineDetails/total_proposal_sent_remeeting_not_done/<?= $row->user_id; ?>/<?= $sdate; ?>/<?= $edate; ?>/userwise"><?= $row->total_proposal_sent_remeeting_not_done ?></a></td>

                <td><a target="_blank" href="<?= base_url()?>Reports/ClosurePipeLineDetails/total_proposal_sent_by_current_bd/<?= $row->user_id; ?>/<?= $sdate; ?>/<?= $edate; ?>/userwise"><?= $row->total_proposal_sent_by_current_bd ?></a></td>

                <td><a target="_blank" href="<?= base_url()?>Reports/ClosurePipeLineDetails/total_proposal_sent_by_other_bd/<?= $row->user_id; ?>/<?= $sdate; ?>/<?= $edate; ?>/userwise"><?= $row->total_proposal_sent_by_other_bd ?></a></td>

                <td><a target="_blank" href="<?= base_url()?>Reports/ClosurePipeLineDetails/total_closure/<?= $row->user_id; ?>/<?= $sdate; ?>/<?= $edate; ?>/userwise"><?= $row->total_closure ?></a></td>

                <td><a target="_blank" href="<?= base_url()?>Reports/ClosurePipeLineDetails/total_direct_closure_without_proposal_sent/<?= $row->user_id; ?>/<?= $sdate; ?>/<?= $edate; ?>/userwise"><?= $row->total_direct_closure_without_proposal_sent ?></a></td>

                <td><a target="_blank" href="<?= base_url()?>Reports/ClosurePipeLineDetails/total_closure_after_proposal_sent/<?= $row->user_id; ?>/<?= $sdate; ?>/<?= $edate; ?>/userwise"><?= $row->total_closure_after_proposal_sent ?></a></td>

                <td><a target="_blank" href="<?= base_url()?>Reports/ClosurePipeLineDetails/total_not_closure_after_proposal_sent/<?= $row->user_id; ?>/<?= $sdate; ?>/<?= $edate; ?>/userwise"><?= $row->total_not_closure_after_proposal_sent ?></a></td>


                <td class='text-white'><?= formatIndianUnits(20000000); ?></td>

                <td><a target="_blank" href="<?= base_url()?>Reports/ClosurePipeLineDetails/total_budget_where_proposal_sent/<?= $row->user_id; ?>/<?= $sdate; ?>/<?= $edate; ?>/userwise"><?= formatIndianUnits($row->total_budget_where_proposal_sent); ?></a></td>

                <td><a target="_blank" href="<?= base_url()?>Reports/ClosurePipeLineDetails/total_closure_budget_where__proposal_sent/<?= $row->user_id; ?>/<?= $sdate; ?>/<?= $edate; ?>/userwise"><?= formatIndianUnits($row->total_closure_budget_where__proposal_sent); ?></a></td>

                <td><a target="_blank" href="<?= base_url()?>Reports/ClosurePipeLineDetails/total_not_closure_budget_where__proposal_sent/<?= $row->user_id; ?>/<?= $sdate; ?>/<?= $edate; ?>/userwise"><?= formatIndianUnits($row->total_not_closure_budget_where__proposal_sent); ?></a></td>

                <td class='text-white'><?php 
                
                $targetbudget = 20000000;
                $targetrembudget = 0;
                if($targetbudget > $row->total_closure_budget_where__proposal_sent){
                  $targetrembudget = $targetbudget - $row->total_closure_budget_where__proposal_sent;
                }else if($row->total_closure_budget_where__proposal_sent > $targetbudget){
                  $targetrembudget = 0;
                }
              
                $targetrembudgetMessage = formatIndianUnits($targetrembudget);
                echo $targetrembudgetMessage;
                 ?></td>
                <td class='text-white'><?php 
                
                $targetbudget = 20000000;
                $targetremmessage = 0;
                if($targetbudget > $row->total_closure_budget_where__proposal_sent){
                  $targetremmessag = "NO";
                }else if($row->total_closure_budget_where__proposal_sent > $targetbudget){
                  $targetremmessag = "Yes";
                }
              
                echo $targetremmessag;
                 ?></td>

                <td><a target="_blank" href="<?= base_url()?>Reports/ClosurePipeLineDetails/total_wdl_or_ni_after_proposal_sent/<?= $row->user_id; ?>/<?= $sdate; ?>/<?= $edate; ?>/userwise"><?= $row->total_wdl_or_ni_after_proposal_sent ?></a></td>

                <td><a target="_blank" href="<?= base_url()?>Reports/ClosurePipeLineDetails/total_call_connected_after_proposal_sent/<?= $row->user_id; ?>/<?= $sdate; ?>/<?= $edate; ?>/userwise"><?= $row->total_call_connected_after_proposal_sent ?></a></td>

                <td><a target="_blank" href="<?= base_url()?>Reports/ClosurePipeLineDetails/total_line_manager_call_connected_after_proposal_sent/<?= $row->user_id; ?>/<?= $sdate; ?>/<?= $edate; ?>/userwise"><?= $row->total_line_manager_call_connected_after_proposal_sent ?></a></td>

                <td><a target="_blank" href="<?= base_url()?>Reports/ClosurePipeLineDetails/total_call_not_connected_after_proposal_sent/<?= $row->user_id; ?>/<?= $sdate; ?>/<?= $edate; ?>/userwise"><?= $row->total_call_not_connected_after_proposal_sent ?></a></td>

                <td><a target="_blank" href="<?= base_url()?>Reports/ClosurePipeLineDetails/total_call_connected_after_proposal_sent_without_meeting/<?= $row->user_id; ?>/<?= $sdate; ?>/<?= $edate; ?>/userwise"><?= $row->total_call_connected_after_proposal_sent_without_meeting ?></a></td>

                <td><a target="_blank" href="<?= base_url()?>Reports/ClosurePipeLineDetails/total_call_not_connected_after_proposal_sent_without_meeting/<?= $row->user_id; ?>/<?= $sdate; ?>/<?= $edate; ?>/userwise"><?= $row->total_call_not_connected_after_proposal_sent_without_meeting ?></a></td>
            </tr>
        <?php $k++; } ?>
    </tbody>
</table>



               
                </div>
              </div>
              </div>
  </div>

            <hr>
   


          
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
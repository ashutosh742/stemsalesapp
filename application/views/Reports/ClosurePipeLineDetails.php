

            

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
                      <hr>
                      <p class="premium-color-2" style="color: #ffffffff;">
                        <?= ucwords(str_replace("_", " ", $ftype)); ?>
                      </p>
                      <p class="premium-color-2" style="color: #ffffffff;">
                        <?= ucwords(str_replace("_", " ", $udetail[0]->name)); ?>
                     <br>
                      <?= $sdate; ?> - <?= $edate; ?></p>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
        <!-- Main content -->
       
        <section class="content">
          <div class="container-fluid">
      


<?php 


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
  
<hr style="background-color:#285d00">

              <div class="card-new-table">
                <div class="header">
              <div class="title">
                <h1>Sales / <span style="color: #1df719ff;"><?= ucwords(str_replace("_", " ", $ftype)); ?> / <?= $sdate; ?> - <?= $edate; ?></p></span></h1>
                <div class="subtitle">Snapshot of the dataset By User </div>
              </div>
              <div style="display:flex;gap:12px;align-items:center;">
                <div class="legend">
                  <span style="color:var(--muted)">Generated: <?= date('j M Y, H:i') ?></span>
                </div>
              </div>
            </div>
              <hr style="background-color:#285d00">
              <div class="page-header">
                <div class="table-responsive text-nowrap">


                <table class="table table-bordered table-striped text-center" id="example1">
    <thead style="color:#fff;background-color: #000;">
       <tr>
            <th># Sr No</th>
            <th>🏢 Company Name</th>
            <th>🆔 CID</th>

            <th>📅 Number Of Days After Meeting</th>   
            <th>📨 Number Of Days After Proposal Sent</th>
            <th>📅 Number Of Days Last Activity </th> 
            

            <th>🧑‍💼 Main BD Name</th>
            <th>🧑‍💼 Cluster Manager Name</th>
            <th>🔎 Current Status</th>
            <th>📅 In Quarter </th> 

            <th>💰 Proposal Budget</th>
            <th>🗓️ Total RP Meeting</th>
            <th>📨 Total Proposal Sent</th>
            <th>📞 Total Call Connected After RP Meeting</th>
            <th>📞 Total AY PY Call Connected After RP Meeting</th>

            <th>📈 Total Positive Conversions Call After RP Meeting</th>
            <th>📉 Total Negative Conversions Call After RP Meeting</th>

            <th>📞 Total Line Manager Call Connected After RP Meeting</th>
            <th>☎️ Total Call Connected After Proposal Sent</th>
            <th>☎️ Total AY PY Call Connected After Proposal Sent</th>
            <th>☎️ Total Line Manager Call Connected After Proposal Sent</th>

           <th>📈 Total Positive Conversions Call After Proposal Sent</th>
           <th>📉 Total Negative Conversions Call After Proposal Sent</th>

            <?php if($ftype == 'total_call_connected_after_proposal_sent_without_meeting'){ ?>
                <th>📤 Total Call Connected After Proposal Sent Without Meeting</th>
            <?php } ?>
           

              <?php if($ftype == 'total_closure'){ ?>
                <th>📤 Total Handover</th>
                <th>📤 Handover Date / Handover Budget</th>
            <?php } ?>
        </tr>
    </thead>
    <tbody>
        <?php $k=1; foreach ($totalClosurePipeLineDatas as $row) {                         
        ?>
        <tr>
                <td><?= $k ?></td>
                <td> <a href="<?=base_url()?>Menu/CompanyDetails/<?= $row->cid; ?>" target="_blank"><?= $row->compname ?></a></td>
                <td><a href="<?=base_url()?>Menu/CompanyDetails/<?= $row->cid; ?>" target="_blank"><?= $row->cid ?></a></td>

                <td><?= $row->after_rp_meeting_days ?> Days</td>
                <td><?= $row->after_proposal_sent_days ?> Days</td>
                <td><?= $row->number_Of_days_last_activity ?> Days</td>

                <td style="color: rgb(24 243 0);"><?= $row->main_bd_name ?></td>
                <td><?= $row->cluster_manager_name ?></td>
                <td style="color: rgb(24 243 0);"><?= $row->current_status_name ?></td>
                <td style="color: rgb(24 243 0);"><?= $row->in_quarter ?></td>
                <td>
                  <span style="color: rgb(24 243 0);"><?= formatIndianUnits($row->proposal_budgetme) ?></span></td>
                <td>
                    <a target="_blank" href="<?= base_url()?>Reports/ClosurePipeLineDetailsByCompany/total_rp_meeting/<?= $row->cid; ?>/<?= $sdate; ?>/<?= $edate; ?>">
                        <?= $row->total_rp_meeting ?>
                    </a>
                </td>
                <td>
                    <a target="_blank" href="<?= base_url()?>Reports/ClosurePipeLineDetailsByCompany/total_proposal_sent/<?= $row->cid; ?>/<?= $sdate; ?>/<?= $edate; ?>">
                        <?= $row->total_proposal_sent ?>
                    </a>
                </td>
                <td>
                    <a target="_blank" href="<?= base_url()?>Reports/ClosurePipeLineDetailsByCompany/total_call_connected_after_rp_meeting/<?= $row->cid; ?>/<?= $sdate; ?>/<?= $edate; ?>">
                        <?= $row->total_call_connected_after_rp_meeting ?>
                    </a>
                </td>
                <td>
                    <a target="_blank" href="<?= base_url()?>Reports/ClosurePipeLineDetailsByCompany/total_AY_PY_call_connected_after_rp_meeting/<?= $row->cid; ?>/<?= $sdate; ?>/<?= $edate; ?>">
                        <?= $row->total_AY_PY_call_connected_after_rp_meeting ?>
                    </a>
                </td>
                <td>
                    <a target="_blank" href="<?= base_url()?>Reports/ClosurePipeLineDetailsByCompany/total_positive_conversions_call_after_rp_meeting/<?= $row->cid; ?>/<?= $sdate; ?>/<?= $edate; ?>">
                        <?= $row->total_positive_conversions_call_after_rp_meeting ?>
                    </a>
                </td>
                <td>
                    <a target="_blank" href="<?= base_url()?>Reports/ClosurePipeLineDetailsByCompany/total_negative_conversions_call_after_rp_meeting/<?= $row->cid; ?>/<?= $sdate; ?>/<?= $edate; ?>">
                        <?= $row->total_negative_conversions_call_after_rp_meeting ?>
                    </a>
                </td>
                <td>
                    <a target="_blank" href="<?= base_url()?>Reports/ClosurePipeLineDetailsByCompany/total_line_manager_call_connected_after_rp_meeting/<?= $row->cid; ?>/<?= $sdate; ?>/<?= $edate; ?>">
                        <?= $row->total_line_manager_call_connected_after_rp_meeting ?>
                    </a>
                </td>
                <td>
                    <a target="_blank" href="<?= base_url()?>Reports/ClosurePipeLineDetailsByCompany/total_call_connected_after_proposal_sent/<?= $row->cid; ?>/<?= $sdate; ?>/<?= $edate; ?>">
                        <?= $row->total_call_connected_after_proposal_sent ?>
                    </a>
                </td>
                <td>
                    <a target="_blank" href="<?= base_url()?>Reports/ClosurePipeLineDetailsByCompany/total_AY_PY_call_connected_after_proposal_sent/<?= $row->cid; ?>/<?= $sdate; ?>/<?= $edate; ?>">
                        <?= $row->total_AY_PY_call_connected_after_proposal_sent ?>
                    </a>
                </td>
                <td>
                    <a target="_blank" href="<?= base_url()?>Reports/ClosurePipeLineDetailsByCompany/total_line_manager_call_connected_after_proposal_sent/<?= $row->cid; ?>/<?= $sdate; ?>/<?= $edate; ?>">
                        <?= $row->total_line_manager_call_connected_after_proposal_sent ?>
                    </a>
                </td>


                <td>
                    <a target="_blank" href="<?= base_url()?>Reports/ClosurePipeLineDetailsByCompany/total_positive_conversions_call_after_proposal_sent/<?= $row->cid; ?>/<?= $sdate; ?>/<?= $edate; ?>">
                        <?= $row->total_positive_conversions_call_after_proposal_sent ?>
                    </a>
                </td>
                <td>
                    <a target="_blank" href="<?= base_url()?>Reports/ClosurePipeLineDetailsByCompany/total_negative_conversions_call_after_proposal_sent/<?= $row->cid; ?>/<?= $sdate; ?>/<?= $edate; ?>">
                        <?= $row->total_negative_conversions_call_after_proposal_sent ?>
                    </a>
                </td>



                <?php if($ftype == 'total_call_connected_after_proposal_sent_without_meeting'){ ?>
                     <td>
                    <a target="_blank" href="<?= base_url()?>Reports/ClosurePipeLineDetailsByCompany/total_call_connected_after_proposal_sent_without_meeting/<?= $row->cid; ?>/<?= $sdate; ?>/<?= $edate; ?>">
                        <?= $row->total_call_connected_after_proposal_sent_without_meeting ?>
                    </a>
                </td>
                <?php } ?>
               
                <?php if($ftype == 'total_closure'){ ?>
                    <td>
                        <?php 
                        $chDatas = $this->Report_model->GetHandoverDetailsByCidBetWeenDate($row->cid,$sdate,$edate);
                        echo sizeof($chDatas);
                        ?>
                    </td>
                    <td style='color: #166228 !important; font-weight: 700;'>
                        <?php 
                        if(sizeof($chDatas) > 0){
                            foreach($chDatas as $chData){
                                $noofschool   = $chDatas[0]->noofschool;    
                                $cAhDatas = $this->Report_model->GetHandoverAccountDetailsByHid($chData->id);
                                $cAhDatasCNT = sizeof($cAhDatas);
                                if($cAhDatasCNT > 0){
                                    $total_budget = $cAhDatas[0]->tbudget;
                                }else{
                                    $total_budget = "NA";
                                }
                                ?>
                                <span class="p-1 m-1 bg-white text-dark"> 
                                    <a class='' target="_blank" href="https://stemoppapp.in/Menu/ProjectProfileDetails/<?= $chData->id ?>/<?=$user['user_id']?>"><?= $chData->client_handover_date ?>

                                    <?php if($total_budget !== 'NA'){?> - ₹ <?= number_format($total_budget, 0, '', ',');?> <?php }else{ ?> <span class='text-danger'> - NA</span> <?php } ?> - <span class='text-success'>No Of School - <?= $noofschool;?></span>
                                    </a>
                                </span>
                                <?php
                            }
                        }
                        ?>
                    </td>
                <?php } ?>
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
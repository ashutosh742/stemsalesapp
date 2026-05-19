<?php 
              $slct_user_id             = $targetData[0]->user_id;
              $no_of_meeting            = $targetData[0]->no_of_meeting;
              $num_of_barg_meeting      = $targetData[0]->num_of_barg_meeting;
              $no_of_proposal           = $targetData[0]->no_of_proposal;
              $no_of_prospective        = $targetData[0]->no_of_prospective;
              $revenue                  = $targetData[0]->revenue;
              $school                   = $targetData[0]->school;
              $partner_id               = $targetData[0]->partner_id;
              $category_ids             = $targetData[0]->category_id;
              $out_station_meeting      = $targetData[0]->out_station_meeting;
              $local_station_meeting    = $targetData[0]->local_station_meeting;
              $start_date               = $targetData[0]->start_date;
              $end_date                 = $targetData[0]->end_date;
              $target_by                = $targetData[0]->target_by;
              $created_at               = $targetData[0]->created_at;
              $to_user                  = $targetData[0]->to_user;
              $by_user                  = $targetData[0]->by_user;

              $currentQuarter           = $targetData[0]->currentQuarter;
              $reviewtype               = $targetData[0]->reviewtype;
              $num_of_sheduled_meeting  = $targetData[0]->num_of_sheduled_meeting;
              $areview_id               = $targetData[0]->review_id;



              $twetenty_closure_funnel  = $targetData[0]->twetenty_closure_funnel;
              $potential_funnel_for_fy  = $targetData[0]->potential_funnel_for_fy;
              $to_be_nurtured_for_fy    = $targetData[0]->to_be_nurtured_for_fy;
              $fifity_new_lead_funnel   = $targetData[0]->fifity_new_lead_funnel;
              $anchor_client_meeting    = $targetData[0]->anchor_client_meeting;

              $proposal_send                          = $targetData[0]->proposal_send;
              $bd_potentional_client_proposal_send    = $targetData[0]->bd_potentional_client_proposal_send;
              $proposals_sent_and_closure             = $targetData[0]->proposals_sent_and_closure;



              

              ?>

<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="wi$dth=device-wi$dth, initial-scale=1">
    <title>Target vs Achievement OF  <?= $to_user.' - '.$by_user; ?> - <?= $start_date.' - '.$end_date; ?> | STEM APP | WebAPP</title>
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
      .col.card.coladjust1 select,.col.card.coladjust31 select,.container-fluid.body-content.mt-4,.each,.form-row>.col,.form-row>[class*=col-],.table-striped tbody tr:nth-of-type(odd),input.form-control,table.table-bordered.dataTable{box-shadow:inset 4px 4px 7px rgba(55,84,170,.15),inset -4px -4px 10px #fff,0 0 2px rgba(255,255,255,.2);transition:box-shadow .2s ease-in-out}.scrollme{overflow-x:auto}.card-body{background:azure}.formSection{align-items:center;justify-content:center;display:flex}.form-control{width:200px!important}.modal-content{background:#faebd7}.coladjust{padding-right:5px;padding-left:5px;height:143px;align-items:center;justify-content:center;color:#000}.col.card.coladjust1{color:#000;text-align:center;align-items:center}th.sorting{background:#008b8b;color:#fff}.modal-header{background:#5f9ea0;color:#fff}.form-control.is-invalid,.was-validated .form-control:invalid{background-image:none!important}.form-control.is-valid,.was-validated .form-control:valid{background-image:none!important}.col.card.coladjust1 select{width:100%!important;height:100%!important;background:#f0fff0}.each{background:#f1f3f6;font-family:Arial,Helvetica,sans-serif;border:5px solid #eaeef5}.col.card.coladjust1.p-2.m-1{background:beige}.container-fluid.body-content.mt-4{padding:20px}.col.card.coladjust.m-1{background:#fff8dc}tbody{font-weight:500}.input-group{margin-bottom:10px;display:table-cell}.remove-btn{cursor:pointer;color:red}.coladjust121{min-height:143px;align-items:center;justify-content:center;color:#000;padding:20px}.col.card.coladjust31.p-2.m-1{align-items:center}.col.card.coladjust31 select{background:#f0fff0}.meetingbackground{background:#bdb76b}
      @import url('https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css');
      @import url('https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700&display=swap');
.cta-btn,.tab-link{cursor:pointer;font-weight:500}.tabs-container{height:auto;border-radius:20px;box-shadow:0 20px 40px rgba(0,0,0,.15);overflow:hidden;position:relative;box-shadow:inset 4px 4px 7px rgba(55,84,170,.15),inset -4px -4px 10px #fff,0 0 2px rgba(255,255,255,.2);transition:box-shadow .2s ease-in-out}.tabs{padding:10px 20px 15px}.tab-links{border-bottom:1px solid #f0f0f0}.btn-group>.btn-group:not(:first-child)>.btn,.btn-group>.btn:not(:first-child),.cta-btn,.tab-link{border:none}[type=button]:not(:disabled),[type=reset]:not(:disabled),[type=submit]:not(:disabled),button:not(:disabled){color:navy;padding:10px;margin:5px;font-size:18px;font-weight:600;background:#ffebcd;box-shadow:inset 4px 4px 7px rgba(55,84,170,.15),inset -4px -4px 10px #fff,0 0 2px rgba(255,255,255,.2);transition:box-shadow .2s ease-in-out}.tab-link{background:0 0;font-size:16px;padding:15px 30px;color:#ccc;position:relative;transition:color .3s}.cta-btn,.tab-link::after{background:linear-gradient(45deg,#b84de5,#7d41ff)}.tab-link.active,.tab-link:hover{color:#b84de5;padding:10px 24px;margin:5px;font-size:18px;font-weight:600;transition:.3s ease-in-out}.tab-link i{margin-right:10px}.tab-link::after{content:'';position:absolute;width:0;height:3px;bottom:-1px;left:50%;transition:.4s}.tab-link.active::after{width:100%;left:0}.tab-content{display:none;animation:.5s fadeInUp;padding:5px 10px 15px}@keyframes fadeInUp{from{opacity:0;transform:translateY(20px)}to{opacity:1;transform:translateY(0)}}.cta-btn{display:inline-block;padding:12px 25px;color:#fff;border-radius:50px;transition:background .4s;margin-top:20px}.cta-btn:hover{background:linear-gradient(45deg,#9c3bce,#6b3ee8)}@media screen and (max-width:600px){.tab-links{flex-direction:column;align-items:center}.tab-link{text-align:center;width:100%;padding:15px 0}}.tab-content.active{display:block;background-color:#008b8b14;margin-top:16px;box-shadow:inset 4px 4px 7px rgba(55,84,170,.15),inset -4px -4px 10px #fff,0 0 2px rgba(255,255,255,.2);transition:box-shadow .2s ease-in-out;border-radius:10px}h4.each.p-2.text-center{color:#ff1493;font-family:system-ui;font-weight:700;letter-spacing:2px}.row.meetings-data{background:beige;padding:10px}
   
   </style>
  </head>
  <body class="hold-transition sidebar-mini layout-fixed">
    <div class="wrapper">
      <!-- Preloader -->
      <!-- Navbar -->
  <?php $this->load->view($dep_name.'/nav.php');?>
  <style>
        .custom-btn {
            width: 130px;
            height: 40px;
            color: #fff;
            border-radius: 5px;
            padding: 10px 25px;
            font-family: Lato, sans-serif;
            font-weight: 500;
            background: 0 0;
            cursor: pointer;
            transition: .3s;
            position: relative;
            display: inline-block;
            box-shadow: inset 2px 2px 2px 0 rgba(255,255,255,.5), 7px 7px 20px 0 rgba(0,0,0,.1), 4px 4px 5px 0 rgba(0,0,0,.1);
            outline: 0;
        }

        .btn-11 {
            border: none;
            background: #212ffb;
            background: linear-gradient(0deg, #3e21fb 0, #4c5cea 100%);
            color: #fff;
            overflow: hidden;
            width: fit-content;
        }

        .btn-11:hover {
            text-decoration: none;
            color: #fff;
            opacity: .7;
        }

        .btn-11:before {
            position: absolute;
            content: '';
            display: inline-block;
            top: -180px;
            left: 0;
            width: 30px;
            height: 100%;
            background-color: #fff;
            animation: 3s ease-in-out infinite shiny-btn1;
        }

        .btn-11:active {
            box-shadow: 4px 4px 6px 0 rgba(255,255,255,.3), -4px -4px 6px 0 rgba(116,125,136,.2), inset -4px -4px 6px 0 rgba(255,255,255,.2), inset 4px 4px 6px 0 rgba(0,0,0,.2);
        }

        @keyframes shiny-btn1 {
            0% { transform: scale(0) rotate(45deg); opacity: 0; }
            80% { transform: scale(0) rotate(45deg); opacity: .5; }
            81% { transform: scale(4) rotate(45deg); opacity: 1; }
            100% { transform: scale(50) rotate(45deg); opacity: 0; }
        }
        tr,td{
          font-weight: 600;
        }
        .btn-11.partnearray{
          background: navy!important;
        }
        .btn-11.categoryarray{
          background: #ff007f!important;
        }
  </style>
      <!-- /.navbar -->
      <!-- Content Wrapper. Contains page content -->
      <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <div class="content-header">
          <div class="container-fluid">
            <div class="row mb-2">
              <div class="col-sm-6">
                <h1 class="m-0"></h1>
              </div>
              <!-- /.col -->
              <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                  <h4></h4>
                </ol>
              </div>
              <!-- /.col -->
            </div>
            <!-- /.row -->
          </div>
          <!-- /.container-fluid -->
        </div>
        <!-- /.content-header -->
        <!-- Main content -->
        <section class="content">
          <?php if($this->session->flashdata('success_message')): ?>
          <div class="alert alert-success">
            <?php echo $this->session->flashdata('success_message'); ?>
          </div>
          <?php endif; ?>
          <div class="container-fluid">
          <?php // echo $category_ids ?>
            <div class="card-body">
              <div class="each p-2 bg-info1 text-center" style="background: linear-gradient(to right, #e0f7fa, #ffffff); border-radius: 12px; box-shadow: 0 4px 8px rgba(0,0,0,0.1); padding: 20px;">
                <h2 class="text-center p-3 gradient-text" style="font-weight: 700;" >🎯 Target vs Achievement</h2>
                <small class="p-2">
                  🎯 Target vs Achievement is a powerful metric used to evaluate progress against planned goals. It highlights the gap between what was set out to be achieved and what was actually accomplished. 📊 This comparison helps teams track performance, identify shortfalls, and celebrate successes. ✅ Regular analysis of targets vs achievements enables better planning, smarter decision-making, and continuous improvement. 📈 Whether in sales, tasks, or milestones, understanding this ratio drives accountability and focus. 🚀 Use it to motivate your team, measure efficiency, and stay aligned with strategic objectives. Every achieved goal brings you one step closer to success! 🏆
                </small>
              <br>
                <mark><?= $to_user.' - '.$by_user; ?></mark>
                <hr>
                <mark><?= $start_date.' - '.$end_date; ?></mark>
              </div>
            </div>
            <div class="row">
              <div class="col-md-12 text-center">
                <button class="btn btn-success" id="downloadPdf">Download PDF</button>
              </div>
            </div>
            <hr>
            <div class="row">
              <div class="col-12">
                <!-- /.card-header -->
                <div class="card-body">
                  <div class="each body-content p-4">
                    <div class="table-responsive text-nowrap">
                      <table id="example1" class="table table-striped table-bordered" cellspacing="0" width="100%">
                            <thead>
                           <tr>
                                <th>S.No</th>
                                <th>📅 Quarter</th>
                                <th>📂 Review Type</th>
                                <th>👤 Target User Name</th>
                                <th>📄 No. of Proposals</th>
                                <th>🌱 New Leads (Prospective)</th>
                                <th>💰 Revenue</th>
                                <th>🏫 No. of Schools</th>
                                <th>🤝 Total Meetings</th>
                                <th>📆 Scheduled Meetings</th>
                                <th>⚡ Bargaining Meetings</th>
                                <th>🤝 By Partner Type</th>
                                <!-- <th>📁 By Category Type</th> -->
                                <th>🛫 Outstation Meetings</th>
                                <th>🏠 Local Meetings</th>
                                <th>🚀 Q1 Closure Funnel</th>
                                <th>🎯 FY Potential Funnel</th>
                                <th>🌾 Leads to Nurture (FY)</th>
                                <th>📌 50-Lead Funnel</th>
                                <th>🏆 Anchor Clients</th>
                                <th>🕒 Start Date</th>
                                <th>🕓 End Date</th>
                                <th>🗓️ Created Date</th>
                                <th>👤 Added By</th>
                              </tr>
                            </thead>
                            <tbody>
                              <?php 
                              
                              $i = 1; foreach($targetData as $data):
                              $r = rand(150, 255);
                              $g = rand(150, 255);
                              $b = rand(150, 255);
                              $backgroundColor = "rgba($r, $g, $b,.2)";
                              $hue        = rand(0, 360); // Full color wheel
                              $saturation = rand(60, 100); // High saturation for rich colors
                              $lightness  = rand(30, 45); // Low lightness for a deeper tone
                              $backgroundColorNew = "hsl($hue, $saturation%, $lightness%)";
                                
                                ?>
                              <tr style="background-color: <?php echo $backgroundColor; ?>;color:<?=$backgroundColorNew;?>!important">
                                <td><?= $i; ?></td>
                                <td><?php 
                                if(!is_null($data->currentQuarter)){
                                  echo $data->currentQuarter;
                                }else{
                                  echo "NA";
                                }
                                ?></td>
                                <td><?php 
                                if(!is_null($data->reviewtype)){
                                  echo $data->reviewtype;
                                  // echo "<span class='custom-btn btn-11'>".$data->reviewtype."</span>";
                                }else{
                                  echo "NA";
                                }
                                ?></td>
                                <td><?= $data->to_user; ?></td>
                                <td><?= $data->no_of_proposal; ?></td>
                                <td><?= $data->no_of_prospective; ?></td>
                                <td><?= $data->revenue; ?></td>
                                <td><?= $data->school; ?></td>
                                <td><?= $data->no_of_meeting; ?></td>
                                <td><?php 
                                  if(!is_null($data->num_of_sheduled_meeting)){
                                  echo $data->num_of_sheduled_meeting;
                                }else{
                                  echo "NA";
                                }
                                ?></td>
                                <td><?= $data->num_of_barg_meeting; ?></td>
                                <td>
                                  <?php
                                    if($data->partner_id !== '' ){
                                      $partnearray = json_decode($data->partner_id, true);
                                      foreach($partnearray as $key => $value){
                                        $getpartner_name = $this->Menu_model->get_partnerbyid($key)[0]->name;
                                        $partner_text = "<span class='custom-btn btn-11 partnearray p-2'>$getpartner_name&nbsp;&nbsp;:&nbsp;&nbsp;$value</span> &nbsp;";
                                        echo $partner_text;
                                      }
                                    
                                    }else{
                                      echo "<span class='bg-warning p-2'>Not&nbsp;Set</span>";
                                    }
                                    ?>
                                </td>
                                <?php /*
                                <td>
                                  <?php
                                    if(!empty($data->category_id) || !is_null($data->category_id)){
                                      $categoryarray = json_decode($data->category_id, true);
                                      foreach($categoryarray as $key => $value){
                                        $getcategory_name = $this->Menu_model->GetCategoriesbydataname($key)[0]->name;
                                        $getcategory_name = str_replace(' ', '&nbsp;', $getcategory_name);
                                        $category_text = "<span class='custom-btn btn-11 categoryarray p-2'>$getcategory_name&nbsp;&nbsp;:&nbsp;&nbsp;$value</span> &nbsp;";
                                        echo $category_text;
                                      }
                                    
                                    }else{
                                      echo "<span class='bg-warning p-2'>Not&nbsp;Set</span>";
                                    }
                                    ?>
                                </td>
                                */?>
                                <td><?= $data->out_station_meeting; ?></td>
                                <td><?= $data->local_station_meeting; ?></td>
                                <td><?php 
                                 if(!is_null($data->twetenty_closure_funnel)){
                                  echo $data->twetenty_closure_funnel;
                                }else{
                                  echo "NA";
                                }
                               ?></td>
                                <td><?php 
                                 if(!is_null($data->potential_funnel_for_fy)){
                                  echo $data->potential_funnel_for_fy;
                                }else{
                                  echo "NA";
                                }
                               ?></td>
                                <td><?php 
                                 if(!is_null($data->to_be_nurtured_for_fy)){
                                  echo $data->to_be_nurtured_for_fy;
                                }else{
                                  echo "NA";
                                }
                               ?></td>
                                <td><?php 
                                 if(!is_null($data->fifity_new_lead_funnel)){
                                  echo $data->fifity_new_lead_funnel;
                                }else{
                                  echo "NA";
                                }
                               ?></td>
                                <td><?php 
                                 if(!is_null($data->anchor_client_meeting)){
                                  echo $data->anchor_client_meeting;
                                }else{
                                  echo "NA";
                                }
                               ?></td>
                                <td><?= $data->start_date; ?></td>
                                <td><?= $data->end_date; ?></td>
                                <td><?= $data->created_at; ?></td>
                                <td><?= $data->by_user; ?></td>
                                
                              </tr>
                              <?php $i++; endforeach; ?>
                            </tbody>
                          </table>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <div class="card-body">
              <div class="each p-2 bg-info1">
                <!-- <h2 class="text-center text-uppercase" style="font-weight: 700;font-family: auto;">Target vs Achievement</h2> -->
                <?php 
                  $totalpartner = 0;
                  $partnearray = json_decode($data->partner_id, true);
                  foreach($partnearray as $key => $value){
                    $totalpartner += $value;
                  }

                  $totalCategory = 0;
                  $categoryarray = json_decode($data->category_id, true);
                  foreach($categoryarray as $key => $value){
                    $totalCategory += $value;
                  }
            
                  
                  // $start_date = '2024-10-01';
                  // $end_date   = date("Y-m-d");
                  
                  $allPropasal              = $this->Menu_model->GetProposalBetweenDate($slct_user_id,$start_date,$end_date);
                  
                  $allMeeting               = $this->Menu_model->GetMeetingBetweenDate($slct_user_id,$start_date,$end_date);
                  // dd($allMeeting);

                  $allMeetingcnt            = sizeof($allMeeting);
                  
                  $groupedPartnerData = [];
                  $groupedClusterData = [];
                  $bargmeetingscount  = 0;

                 // Initialize counters for each group
                    $categoryCounts = [
                      'upsell_client'               => 0,
                      'focus_funnel'                => 0,
                      'keycompany'                  => 0,
                      'pkclient'                    => 0,
                      'priorityc'                   => 0,
                      'topspender'                  => 0,
                      'q1_twetenty_closure_funnel'  => 0,
                      'potential_funnel_for_fy'     => 0,
                      'to_be_nurtured_for_fy'       => 0,
                      'fifity_new_lead_funnel'      => 0,
                      'anchor_clients'              => 0,
                      'sheduled_meeting'            => 0,
                      'base_location'               => 0,
                      'out_location'                => 0
                  ];


                  $curFinancialDate       = $this->Menu_model->getFinancialYearRange();
                  $start_financial_date   = $curFinancialDate['start_date'];
                  $end_financial_date     = $curFinancialDate['end_date'];
                  $start_financial_date_year  = new DateTime($start_financial_date);
                  $fyear                       = $start_financial_date_year->format('Y');



                  foreach ($allMeeting as $item) {

                      $partnerTypeId = $item->partnerType_id; // Get the partnerType_id value
                      if (!isset($groupedPartnerData[$partnerTypeId])) {
                          $groupedPartnerData[$partnerTypeId] = 0; // Initialize count if not already set
                      }
                      $groupedPartnerData[$partnerTypeId]++; // Increment the count


                      $cmp_clusterid = $item->cluster_id; // Get the partnerType_id value
                      if (!isset($groupedClusterData[$cmp_clusterid])) {
                          $groupedClusterData[$cmp_clusterid] = 0; // Initialize count if not already set
                      }
                      $groupedClusterData[$cmp_clusterid]++; // Increment the count

                      $actiontype_id = $item->actiontype_id; // Get the partnerType_id value
                      if($actiontype_id == 4){
                        $bargmeetingscount++;
                      }

                    if ($item->upsell_client === 'yes') {
                        $categoryCounts['upsell_client']++;
                    }
                    if ($item->focus_funnel === 'yes') {
                        $categoryCounts['focus_funnel']++;
                    }
                    if ($item->keycompany === 'yes') {
                        $categoryCounts['keycompany']++;
                    }
                    if ($item->pkclient === 'yes') {
                        $categoryCounts['pkclient']++;
                    }
                    if ($item->priorityc === 'yes') {
                        $categoryCounts['priorityc']++;
                    }
                    if ($item->topspender === 'yes') {
                        $categoryCounts['topspender']++;
                    }

                    if ($item->travelType  == 'base') {
                        $categoryCounts['base_location']++;
                    }
                    if ($item->travelType  == 'outStation') {
                        $categoryCounts['out_location']++;
                    }

                    // if ($item->q1_twetenty_closure_funnel == $fyear) {
                    //     $categoryCounts['q1_twetenty_closure_funnel']++;
                    // }
                    // if ($item->potential_funnel_for_fy == $fyear) {
                    //     $categoryCounts['potential_funnel_for_fy']++;
                    // }
                    // if ($item->to_be_nurtured_for_fy == $fyear) {
                    //     $categoryCounts['to_be_nurtured_for_fy']++;
                    // }

                    if ($item->in_quarter == '20 Closure Funnel in Q2') {
                        $categoryCounts['q1_twetenty_closure_funnel']++;
                    }
                    if ($item->in_quarter == 'Potential Funnel For Q2') {
                        $categoryCounts['potential_funnel_for_fy']++;
                    }
                    if ($item->in_quarter == 'To be Nurtured for Q2') {
                        $categoryCounts['to_be_nurtured_for_fy']++;
                    }

                    if ($item->fifity_new_lead_funnel == $fyear) {
                        $categoryCounts['fifity_new_lead_funnel']++;
                    }
                    if ($item->anchor_clients === 'yes') {
                        $categoryCounts['anchor_clients']++;
                    }

                    if ($actiontype_id == 3) {
                        $categoryCounts['sheduled_meeting']++;
                    }


                  }
                
              // dd($categoryCounts);
                
                  $udetail = $this->Menu_model->get_userbyid($slct_user_id);
                  $base_cluster_of_user = $udetail[0]->base_cluster;
                  $clusterData = $this->Menu_model->getClusterByid($base_cluster_of_user);
                  
                    $cluster_array1 = [];
                    $cluster_array2 = [];
                    if($base_cluster_of_user !==''){
                      foreach ($groupedClusterData as $key => $value) {
                        if ($key == $base_cluster_of_user) {
                            $cluster_array1[$key] = $value;
                        } else {
                            $cluster_array2[$key] = $value;
                        }
                    }
                    }

               
                  $cluster_array1_sum = array_sum($cluster_array1);
                  $cluster_array2_sum = array_sum($cluster_array2);

                  $allNewwLeads             = $this->Menu_model->GetNeewLeadBetweenDate($slct_user_id,$start_date,$end_date);
                  $allNewwLeadscnt          = sizeof($allNewwLeads);
                  
                  $allRevenue               = $this->Menu_model->GetExpectedRevenueBetweenDate($slct_user_id,$start_date,$end_date);
                  $total_revenue            = $allRevenue[0]->total_revenue;
                  
                  $allSchool                = $this->Menu_model->GetExpectedSchoolBetweenDate($slct_user_id,$start_date,$end_date);
                  $total_school             = $allSchool[0]->total_school;
                  
                  $allpropasal              = $this->Menu_model->GetTotalPropasalByUID($slct_user_id,$start_date,$end_date);
                  // echo $this->db->last_query();
                  $allpropasalcnt           = sizeof($allpropasal);
                  
                  $allCloserRevenue         = $this->Menu_model->GetWorkOrderRevenueAndSchoolBetweenDate($slct_user_id,$start_date,$end_date);
                  $total_closer_revenue     = $allCloserRevenue[0]->total_revenue;
                  $total_closer_school      = $allCloserRevenue[0]->total_school;
                  
                  
                  ?>
                <div class="tabs-container">
                  <div class="tabs">
                    <div class="tab-links">
                      <button class="tab-link each active" data-tab="tab-1"><i class="fas fa-info-circle"></i> All</button>
                      <!-- <button class="tab-link each" data-tab="tab-6"><i class="fas fa-info-circle"></i> No of Meeting</button>
                        <button class="tab-link each" data-tab="tab-2"><i class="fas fa-info-circle"></i> No of Poposal</button>
                        <button class="tab-link each" data-tab="tab-3"><i class="fas fa-info-circle"></i> No of Prospective (New Leads)</button>
                        <button class="tab-link each" data-tab="tab-4"><i class="fas fa-info-circle"></i> No of School</button>
                        <button class="tab-link each" data-tab="tab-5"><i class="fas fa-info-circle"></i> No of School</button>
                        <button class="tab-link each" data-tab="tab-7"><i class="fas fa-info-circle"></i> Total Revenue</button> -->
                    </div>
                    <div class="tab-content active" id="tab-1">
                      <div class="row meetings-data">
                        <div class="col-md-12 text-center">
                          <h4 class="bg-info each p-2">Meetings</h4>
                        </div>



                        <div class="col-md-12">
                          <div class='each card p-2'>
                            <table class="table">
                              <thead class="thead-dark">
                               <tr>
                                <th scope="col">#</th>
                                <th scope="col">🎯 Target</th>
                                <th scope="col">📊 Total Target</th>
                                <th scope="col">✅ Completed</th>
                                <th scope="col">📉 Remaining</th>
                                <th scope="col">🏆 Achieved</th>
                              </tr>
                              </thead>
                              <tbody>
                                <tr>
                                  <td>1</td>
                                  <td>Meetings</td>
                                  <td><?= $no_of_meeting; ?></td>
                                  <td>
                                    <a href="<?=base_url()."Menu/TargetMeetingDetails/total_meetings/$target_id"?>" target="_BLANK" ><?= "<span class='bg-success p-2'>$allMeetingcnt</span>"; ?></a>
                                  </td>
                                  <td><?php
                                    if($no_of_meeting > $allMeetingcnt){
                                      $pendingmeeting           = $no_of_meeting - $allMeetingcnt;
                                      echo $pendingmeeting_text = "<span class='bg-warning p-2'>$pendingmeeting</span>";
                                    }else{
                                      echo $pendingmeeting_text = "<span class='bg-warning p-2'>0</span>";
                                    }
                                    ?></td>
                                  <td><?php
                                    if($no_of_meeting > $allMeetingcnt){
                                      echo $pendingmeeting_text = "<span class='bg-danger p-2'>NO</span>";
                                    }else{
                                      echo $pendingmeeting_text = "<span class='bg-success p-2'>YES</span>";
                                    }
                                    ?></td>
                                </tr>
                              </tbody>
                            </table>
                          </div>
                        </div>



                        <div class="col-md-6">
                          <div class='each card p-2'>
                             <h4 class="each p-2 text-center" style="font-size: 16px;">sheduled Meetings Target</h4>
                            <table class="table">
                              <thead class="thead-dark">
                                <tr>
                                  <th scope="col">#</th>
                                  <th scope="col">📌 Meeting Type</th>
                                  <th scope="col">📅 Scheduled Meeting Target</th>
                                  <th scope="col">✅ Completed</th>
                                  <th scope="col">📉 Remaining</th>
                                  <th scope="col">🏆 Achieved</th>
                                </tr>
                              </thead>
                              <tbody>
                                <?php 
                                
                                //  dd($categoryCounts);
                                $total_sheduled_meeting             =  $num_of_sheduled_meeting;
                                $com_sheduled_meeting               =  $categoryCounts['sheduled_meeting'];
                                
                                if($total_sheduled_meeting > $com_sheduled_meeting){
                                  $sheduled_meeting_rem             = $total_sheduled_meeting - $com_sheduled_meeting;
                                  $sheduled_meeting_rem_rem_text    = "<span class='bg-danger p-2'>NO</span>";
                                }else{
                                  $sheduled_meeting_rem             = 0;
                                  $sheduled_meeting_rem_rem_text    = "<span class='bg-success p-2'>YES</span>";
                                }
                                ?>
                                <tr>
                                  <td>1</td>
                                  <td>Sheduled Meetings</td>
                                  <td><?= $total_sheduled_meeting; ?></td>
                                  <td>
                                    <a href="<?=base_url()."Menu/TargetMeetingDetails/sheduled_meeting/$target_id"?>" target="_BLANK" ><?= "<span class='bg-success p-2'>$com_sheduled_meeting</span>"; ?></a>
                                  </td>
                                  <td><?= $sheduled_meeting_rem?></td>
                                  <td><?= $sheduled_meeting_rem_rem_text?></td>
                                </tr>
                              </tbody>
                            </table>
                          </div>
                        </div>
                        <div class="col-md-6">
                          <div class='each card p-2'>
                             <h4 class="each p-2 text-center" style="font-size: 16px;">Barg Meetings Target</h4>
                            <table class="table">
                              <thead class="thead-dark">
                                <tr>
                                  <th scope="col">#</th>
                                  <th scope="col">📌 Meeting Type</th>
                                  <th scope="col">⚡ Bargaining Meeting Target</th>
                                  <th scope="col">✅ Completed</th>
                                  <th scope="col">📉 Remaining</th>
                                  <th scope="col">🏆 Achieved</th>
                                </tr>
                              </thead>
                              <tbody>
                                <tr>
                                  <td>1</td>
                                  <td>Barg Meetings</td>
                                  <td><?= $num_of_barg_meeting; ?></td>
                                  <td>
                                    <a href="<?=base_url()."Menu/TargetMeetingDetails/total_barg_meetings/$target_id"?>" target="_BLANK" ><?= "<span class='bg-success p-2'>$bargmeetingscount</span>"; ?></a>
                                  </td>
                                  <td><?php
                                    if($num_of_barg_meeting > $bargmeetingscount){
                                      $pendingmeeting           = $num_of_barg_meeting - $bargmeetingscount;
                                      echo $pendingmeeting_text = "<span class='bg-warning p-2'>$pendingmeeting</span>";
                                    }else{
                                      echo $pendingmeeting_text = "<span class='bg-warning p-2'>0</span>";
                                    }
                                    ?></td>
                                  <td><?php
                                    if($num_of_barg_meeting > $bargmeetingscount){
                                      echo $pendingmeeting_text = "<span class='bg-danger p-2'>NO</span>";
                                    }else{
                                      echo $pendingmeeting_text = "<span class='bg-success p-2'>YES</span>";
                                    }
                                    ?></td>
                                </tr>
                              </tbody>
                            </table>
                          </div>
                        </div>




                        </div>

                           <hr>         
                        <div class="row">


                        <div class="col-md-12">
                        <div class="each">
                          <h4 class="each p-2 text-center" style="font-size: 16px;">
                            ✅ Complete Meetings by Partner Type – <?= $to_user ?>
                          </h4>
                          </div>
                          <table class="table">
                            <thead class="thead-dark">
                             <tr>
                                <th scope="col">#</th>
                                <th scope="col">🤝 Partner Name</th>
                                <th scope="col">🎯 Total Target</th>
                                <th scope="col">✅ Completed</th>
                                <th scope="col">📉 Remaining</th>
                                <th scope="col">🏆 Achieved</th>
                              </tr>

                            </thead>
                            <tbody>
                              <?php 
                              // dd($partnearray);
                              $i=1; foreach($partnearray as $key => $value){
                                $getpartner_name = $this->Menu_model->get_partnerbyid($key)[0]->name;
                                if (array_key_exists($key, $groupedPartnerData)) {
                                       $meetpartcomp = $groupedPartnerData[$key];
                                }else{
                                     $meetpartcomp   = 0;
                                }
                                if($value > $meetpartcomp){
                                 $meetpartcomp_rem = $value - $meetpartcomp;
                                 $meetpartcomp_rem_text = "<span class='bg-danger p-2'>NO</span>";
                                }else{
                                 $meetpartcomp_rem = 0;
                                 $meetpartcomp_rem_text = "<span class='bg-success p-2'>YES</span>";
                                }
                                 ?>
                              <tr>
                                <td><?= $i; ?></td>
                                <td><?= $getpartner_name; ?></td>
                                <td><?= $value ?></td>
                                <td>
                                  <a href="<?=base_url()."Menu/TargetMeetingDetails/partner_name/$target_id/$key"?>" target="_BLANK" ><?= "<span class='bg-success p-2'>$meetpartcomp</span>"; ?></a>
                                </td>
                                <td><?= "<span class='bg-warning p-2'>$meetpartcomp_rem</span>"; ?></td>
                                <td><?= $meetpartcomp_rem_text ?></td>
                              </tr>
                              <?php $i++; } ?>
                            </tbody>
                          </table>
                        </div>



                        <?php /*
                        <div class="col-md-6">
                        <div class="each">
                            <h4 class="each p-2 text-center" style="font-size: 16px;">Complete Meeting By Category - <?= $to_user ?></h4>
                          </div>
                          <table class="table">
                            <thead class="thead-dark">
                              <tr>
                                <th scope="col">#</th>
                                <th scope="col">Target Category Name</th>
                                <th scope="col">Total Target</th>
                                <th scope="col">Complete</th>
                                <th scope="col">Remaining</th>
                                <th scope="col">Achived</th>
                              </tr>
                            </thead>
                            <tbody>
                              <?php 
                              // dd($partnearray);
                              $i=1; foreach($categoryarray as $key => $catvalue){
                                $getcategory_name = $this->Menu_model->GetCategoriesbydataname($key)[0]->name;
                                if (array_key_exists($key, $categoryCounts)) {
                                       $meetcatecomp = $categoryCounts[$key];
                                }else{
                                     $meetcatecomp   = 0;
                                }
                                if($catvalue > $meetcatecomp){
                                 $meetcatecomp_rem = $catvalue - $meetcatecomp;
                                 $meetcatecomp_rem_text = "<span class='bg-danger p-2'>NO</span>";
                                }else{
                                 $meetcatecomp_rem = 0;
                                 $meetcatecomp_rem_text = "<span class='bg-success p-2'>YES</span>";
                                }
                                 ?>
                              <tr>
                                <td><?= $i; ?></td>
                                <td><?= $getcategory_name; ?></td>
                                <td><?= $catvalue ?></td>
                                <td>
                                  <a href="<?=base_url()."Menu/TargetMeetingDetails/category_name/$target_id/$key"?>" target="_BLANK" ><?= "<span class='bg-success p-2'>$meetcatecomp</span>"; ?></a>
                                </td>
                                <td><?= "<span class='bg-warning p-2'>$meetcatecomp_rem</span>"; ?></td>
                                <td><?= $meetcatecomp_rem_text ?></td>
                              </tr>
                              <?php $i++; } ?>
                            </tbody>
                          </table>
                        </div>

                        */ ?>




                         <div class="col-md-12">
                        <div class="each">
                           <?php 
                          $cfData = $this->Menu_model->getCurrentFinancialYearAndQuarter();
                          $currentQuarter = "Q".$cfData['quarter'];

                          // $currentQFunnels = $this->Menu_model->GetCurrentQuarterFunnelDatas($slct_user_id);
                          // $current_quarter_twetenty_closure_funnel = $currentQFunnels[0]->current_quarter_twetenty_closure_funnel;
                          // $current_quarter_potential_funnel_for_fy = $currentQFunnels[0]->current_quarter_potential_funnel_for_fy;
                          // $current_quarter_to_be_nurtured_for_fy = $currentQFunnels[0]->current_quarter_to_be_nurtured_for_fy;
                          // $anchor_clients = $currentQFunnels[0]->anchor_clients;
                          // $fifity_new_lead_funnel = $currentQFunnels[0]->fifity_new_lead_funnel;
                          // dd($currentQFunnels);

                        ?>
                            <h4 class="each p-2 text-center" style="font-size: 16px;">Complete Meeting By New Category in <?=$currentQuarter?>- <?= $to_user ?></h4>
                          </div>
                          <table class="table">
                            <thead class="thead-dark">
                                  <tr>
                                    <th scope="col">#</th>
                                    <th scope="col">🎯 Target Category Name</th>
                                    <th scope="col">📊 Total Target</th>
                                    <th scope="col">✅ Complete</th>
                                    <th scope="col">📉 Remaining</th>
                                    <th scope="col">🏆 Achieved</th>
                                  </tr>
                            </thead>
                            <tbody>
                              <?php 
                              
                              // dd($categoryCounts);
                                $total_twetenty_closure_funnel                =  $twetenty_closure_funnel;
                                $com_q1_twetenty_closure_funnel               =  $categoryCounts['q1_twetenty_closure_funnel'];
                                
                                if($total_twetenty_closure_funnel > $com_q1_twetenty_closure_funnel){
                                  $q1_twetenty_closure_funnel_rem             = $total_twetenty_closure_funnel - $com_q1_twetenty_closure_funnel;
                                  $q1_twetenty_closure_funnel_rem_rem_text    = "<span class='bg-danger p-2'>NO</span>";
                                }else{
                                  $q1_twetenty_closure_funnel_rem             = 0;
                                  $q1_twetenty_closure_funnel_rem_rem_text    = "<span class='bg-success p-2'>YES</span>";
                                }


                                $total_potential_funnel_for_fy            =  $potential_funnel_for_fy;
                                $com_potential_funnel_for_fy              =  $categoryCounts['potential_funnel_for_fy'];

                                if($total_potential_funnel_for_fy > $com_potential_funnel_for_fy){
                                 $potential_funnel_for_fy_rem             = $total_potential_funnel_for_fy - $com_potential_funnel_for_fy;
                                 $potential_funnel_for_fy_rem_rem_text    = "<span class='bg-danger p-2'>NO</span>";
                                }else{
                                 $potential_funnel_for_fy_rem             = 0;
                                 $potential_funnel_for_fy_rem_rem_text    = "<span class='bg-success p-2'>YES</span>";
                                }


                                $total_to_be_nurtured_for_fy            =  $to_be_nurtured_for_fy;
                                $com_to_be_nurtured_for_fy              =  $categoryCounts['to_be_nurtured_for_fy'];

                                if($total_to_be_nurtured_for_fy > $com_to_be_nurtured_for_fy){
                                  $to_be_nurtured_for_fy_rem            = $total_to_be_nurtured_for_fy - $com_to_be_nurtured_for_fy;
                                  $to_be_nurtured_for_fy_rem_rem_text   = "<span class='bg-danger p-2'>NO</span>";
                                }else{
                                  $to_be_nurtured_for_fy_rem            = 0;
                                  $to_be_nurtured_for_fy_rem_rem_text   = "<span class='bg-success p-2'>YES</span>";
                                }


                                $total_fifity_new_lead_funnel       =  $fifity_new_lead_funnel;
                                $com_fifity_new_lead_funnel         =  $categoryCounts['fifity_new_lead_funnel'];

                                if($total_fifity_new_lead_funnel > $com_fifity_new_lead_funnel){
                                  $fifity_new_lead_funnel_rem       = $total_fifity_new_lead_funnel - $com_fifity_new_lead_funnel;
                                  $fifity_new_lead_funnel_rem_text  = "<span class='bg-danger p-2'>NO</span>";
                                }else{
                                  $fifity_new_lead_funnel_rem       = 0;
                                  $fifity_new_lead_funnel_rem_text  = "<span class='bg-success p-2'>YES</span>";
                                }


                                $total_anchor_client_meeting      =  $anchor_client_meeting;
                                $com_anchor_client_meeting        =  $categoryCounts['anchor_clients'];

                                if($total_anchor_client_meeting > $com_anchor_client_meeting){
                                  $anchor_client_meeting_rem       = $total_anchor_client_meeting - $com_anchor_client_meeting;
                                  $anchor_client_meeting_rem_text  = "<span class='bg-danger p-2'>NO</span>";
                                }else{
                                  $anchor_client_meeting_rem       = 0;
                                  $anchor_client_meeting_rem_text  = "<span class='bg-success p-2'>YES</span>";
                                }




                                 ?>
                              <tr>
                                <td>1</td>
                                <td>20 Closure Funnel in <?=$currentQuarter?></td>
                                <td><?= $total_twetenty_closure_funnel; ?></td>
                                <td>
                                  <a href="<?=base_url()."Menu/TargetMeetingDetails/total_twetenty_closure_funnel/$target_id"?>" target="_BLANK" ><?= "<span class='bg-success p-2'>$com_q1_twetenty_closure_funnel</span>"; ?></a>
                                </td>
                                <td><?= "<span class='bg-warning p-2'>$q1_twetenty_closure_funnel_rem</span>"; ?></td>
                                <td><?= $q1_twetenty_closure_funnel_rem_rem_text ?></td>
                              </tr>

                              <tr>
                                <td>2</td>
                                <td>Potential Funnel For <?=$currentQuarter?></td>
                                <td><?= $total_potential_funnel_for_fy; ?></td>
                                <td>
                                  <a href="<?=base_url()."Menu/TargetMeetingDetails/potential_funnel_for_fy/$target_id"?>" target="_BLANK" ><?= "<span class='bg-success p-2'>$com_potential_funnel_for_fy</span>"; ?></a>
                                </td>
                                <td><?= "<span class='bg-warning p-2'>$potential_funnel_for_fy_rem</span>"; ?></td>
                                <td><?= $potential_funnel_for_fy_rem_rem_text ?></td>
                              </tr>
                              <tr>
                                <td>3</td>
                                <td>To be Nurtured for <?=$currentQuarter?>	</td>
                                <td><?= $total_to_be_nurtured_for_fy; ?></td>
                                <td>
                                  <a href="<?=base_url()."Menu/TargetMeetingDetails/total_to_be_nurtured_for_fy/$target_id"?>" target="_BLANK" ><?= "<span class='bg-success p-2'>$com_to_be_nurtured_for_fy</span>"; ?></a>
                                </td>
                                <td><?= "<span class='bg-warning p-2'>$to_be_nurtured_for_fy_rem</span>"; ?></td>
                                <td><?= $to_be_nurtured_for_fy_rem_rem_text ?></td>
                              </tr>
                              <tr>
                                <td>4</td>
                                <td>	50 New Lead Funnel	</td>
                                <td><?= $total_fifity_new_lead_funnel; ?></td>
                                <td>
                                  <a href="<?=base_url()."Menu/TargetMeetingDetails/total_fifity_new_lead_funnel/$target_id"?>" target="_BLANK" ><?= "<span class='bg-success p-2'>$com_fifity_new_lead_funnel</span>"; ?></a>
                                </td>
                                <td><?= "<span class='bg-warning p-2'>$fifity_new_lead_funnel_rem</span>"; ?></td>
                                <td><?= $fifity_new_lead_funnel_rem_text ?></td>
                              </tr>

                              <tr>
                                <td>5</td>
                                <td>	Anchor Clients Meeting	</td>
                                <td><?= $total_anchor_client_meeting; ?></td>
                                <td>
                                  <a href="<?=base_url()."Menu/TargetMeetingDetails/total_anchor_client_meeting/$target_id"?>" target="_BLANK" ><?= "<span class='bg-success p-2'>$com_anchor_client_meeting</span>"; ?></a>
                                </td>
                                <td><?= "<span class='bg-warning p-2'>$anchor_client_meeting_rem</span>"; ?></td>
                                <td><?= $anchor_client_meeting_rem_text ?></td>
                              </tr>
                          
                            </tbody>
                          </table>
                        </div>

                   



                        <?php 

                          $max_check_date = '2025-02-14';
                          
                          $reviewsDatas       = $this->Menu_model->getReviewByRID($areview_id);
                          $review_start_date  = $reviewsDatas[0]->startt;

                          $getPastQuarterStartDate =  $this->Menu_model->getPastQuarterStartDate($review_start_date);
                          $review_start_date =    $max_check_date;   
                          // $review_start_date =    $getPastQuarterStartDate;   

                          $previousQuarterDate                  = $this->Menu_model->getPreviousQuarterStartDate();
                          $meetingDone                          = $this->Menu_model->GetTotalMeetingDoneProposalDoneOrNot($slct_user_id,$review_start_date,$end_date,$slct_user_id,$slct_user_id);


                          $total_meetings                       = $meetingDone[0]->total_meetings;
                          $proposal_sent                        = $meetingDone[0]->proposal_sent;
                          $proposal_not_sent                    = $meetingDone[0]->proposal_not_sent;
                          $proposal_sent_but_closure_not_done   = $meetingDone[0]->proposal_sent_but_closure_not_done;
                          $bd_potentional_client_proposal_not_sent   = $meetingDone[0]->bd_potentional_client_proposal_not_sent;

                          $proposal_send                          = $targetData[0]->proposal_send;
                          $bd_potentional_client_proposal_send    = $targetData[0]->bd_potentional_client_proposal_send;
                          $proposals_sent_and_closure             = $targetData[0]->proposals_sent_and_closure;

                        $check_review_start_date  = $reviewsDatas[0]->startt;
                        $check_review_start_date = date('Y-m-d', strtotime($check_review_start_date));
                        $meetingDoneAFR                          = $this->Menu_model->GetTotalMeetingDoneProposalDoneOrNotTarget($slct_user_id,$review_start_date,$end_date,$slct_user_id,$slct_user_id,$check_review_start_date);

                          
                        $proposal_sent_after_review                       = $meetingDoneAFR[0]->proposal_sent_after_review;
                        $bd_potentional_client_proposal_sent_after_review = $meetingDoneAFR[0]->bd_potentional_client_proposal_sent_after_review;
                        $proposal_sent_closure_done_after_review          = $meetingDoneAFR[0]->proposal_sent_closure_done_after_review;

                        if($proposal_send > $proposal_sent_after_review){
                            $proposal_send_rem       = $proposal_send - $proposal_sent_after_review;
                            $proposal_send_rem_text  = "<span class='bg-danger p-2'>NO</span>";
                        }else{
                            $proposal_send_rem       = 0;
                            $proposal_send_rem_text  = "<span class='bg-success p-2'>YES</span>";
                        }

                        if($bd_potentional_client_proposal_send > $bd_potentional_client_proposal_sent_after_review){
                            $bd_potentional_client_proposal_send_rem       = $bd_potentional_client_proposal_send - $bd_potentional_client_proposal_sent_after_review;
                            $bd_potentional_client_proposal_send_rem_text  = "<span class='bg-danger p-2'>NO</span>";
                        }else{
                            $bd_potentional_client_proposal_send_rem       = 0;
                            $bd_potentional_client_proposal_send_rem_text  = "<span class='bg-success p-2'>YES</span>";
                        }

                        if($proposals_sent_and_closure > $proposal_sent_closure_done_after_review){
                            $proposals_sent_and_closure_send_rem       = $proposals_sent_and_closure - $proposal_sent_closure_done_after_review;
                            $proposals_sent_and_closure_send_rem_text  = "<span class='bg-danger p-2'>NO</span>";
                        }else{
                            $proposals_sent_and_closure_send_rem       = 0;
                            $proposals_sent_and_closure_send_rem_text  = "<span class='bg-success p-2'>YES</span>";
                        }

                        ?>
                        <div class="col-md-12">
                        <div class="each">
                            <div class="text-center mb-2" style="background: linear-gradient(to right, #ede7f6, #ffffff); border-radius: 12px; box-shadow: 0 4px 8px rgba(0,0,0,0.1); padding: 20px;">
                              <h5>Meeting Done || Proposal Overview || Closure Status  <?=$currentQuarter?>- <?= $to_user ?></h5> 
                                <hr>
                                <small>
                                Summarizes meetings held, proposals shared, and closure progress to track engagement, conversion potential, and identify follow-up opportunities effectively.
                                </small>
                            </div>
                          </div>
                          <table class="table">
                            <thead class="thead-dark">
                                  <tr>
                                    <th scope="col">#</th>
                                    <th scope="col">🎯 Target Name</th>
                                    <th scope="col">📊 In Total </th>
                                    <th scope="col">📊 Total Target</th>
                                    <th scope="col">✅ Complete</th>
                                    <th scope="col">📉 Remaining</th>
                                    <th scope="col">🏆 Achieved</th>
                                  </tr>
                            </thead>
                            <tbody>

                              <tr>
                                <td>1</td>
                                <td> 📤 Total Proposals Not Sent</td>
                                <td>
                                  <a href="<?=base_url()?>Reports/MeetingDoneProposalOverviewClosureStatusLists/proposal_not_sent/<?=$slct_user_id?>/<?=$review_start_date?>/<?=$end_date;?>" target="_BLANK">
                                     <?= $proposal_not_sent?>
                                  </a>
                                </td>
                                <td><?= $proposal_send?></td>
                                <td>
                                  <a href="<?=base_url()?>Reports/MeetingDoneProposalOverviewClosureStatusListsAfterReview/proposal_sent_after_review/<?=$slct_user_id?>/<?=$review_start_date?>/<?=$end_date;?>/<?=$check_review_start_date?>" target="_BLANK">
                                  <span class='bg-success p-2'>
                                   <?=$proposal_sent_after_review?>
                                  </span>
                                </a>
                                </td>
                                 <td><?= "<span class='bg-warning p-2'>$proposal_send_rem</span>"; ?></td>
                                <td><?= $proposal_send_rem_text ?></td>
                              </tr>

                              
                              <tr>
                                <td>2</td>
                                <td> 👥 BD Potential Proposals Not Sent</td>
                                <td>
                                  <a href="<?=base_url()?>Reports/MeetingDoneProposalOverviewClosureStatusLists/bd_potentional_client_proposal_not_sent/<?=$slct_user_id?>/<?=$review_start_date?>/<?=$end_date;?>" target="_BLANK">
                                     <?= $bd_potentional_client_proposal_not_sent?>
                                  </a>
                                </td>
                                <td><?= $bd_potentional_client_proposal_send?></td>
                                <td>
                                  <a href="<?=base_url()?>Reports/MeetingDoneProposalOverviewClosureStatusListsAfterReview/bd_potentional_client_proposal_sent_after_review/<?=$slct_user_id?>/<?=$review_start_date?>/<?=$end_date;?>/<?=$check_review_start_date?>" target="_BLANK">
                                  <span class='bg-success p-2'>
                                   <?=$bd_potentional_client_proposal_sent_after_review?>
                                  </span>
                                </a>
                                </td>
                                 <td><?= "<span class='bg-warning p-2'>$bd_potentional_client_proposal_send_rem</span>"; ?></td>
                                <td><?= $bd_potentional_client_proposal_send_rem_text ?></td>
                              </tr>
                              
                              <tr>
                                <td>2</td>
                                <td> ⏳ Proposals Sent, Closure Pending</td>
                                <td>
                                  <a href="<?=base_url()?>Reports/MeetingDoneProposalOverviewClosureStatusLists/proposal_sent_but_closure_not_done/<?=$slct_user_id?>/<?=$review_start_date?>/<?=$end_date;?>" target="_BLANK">
                                     <?= $proposal_sent_but_closure_not_done?>
                                  </a>
                                </td>
                                <td><?= $proposals_sent_and_closure?></td>
                                <td>
                                  <a href="<?=base_url()?>Reports/MeetingDoneProposalOverviewClosureStatusListsAfterReview/proposal_sent_closure_done_after_review/<?=$slct_user_id?>/<?=$review_start_date?>/<?=$end_date;?>/<?=$check_review_start_date?>" target="_BLANK">
                                  <span class='bg-success p-2'>
                                   <?=$proposal_sent_closure_done_after_review?>
                                  </span>
                                </a>
                                </td>
                                 <td><?= "<span class='bg-warning p-2'>$proposals_sent_and_closure_send_rem</span>"; ?></td>
                                <td><?= $proposals_sent_and_closure_send_rem_text ?></td>
                              </tr>
                            </tbody>
                          </table>
                        </div>
                        <hr>
                        <div class="col-md-6">
                          <div class="each">
                            <h4 class="each p-2 text-center" style="font-size: 16px;">Complete Meeting By Out Station Locations - <?= $to_user ?></h4>
                          </div>
                          <hr>
                          <?php 
                         
                                $total_out_station_meeting      =  $out_station_meeting;
                                $com_out_location               =  $categoryCounts['out_location'];

                                if($total_out_station_meeting > $com_out_location){
                                  $out_location_rem             = $total_out_station_meeting - $com_out_location;
                                  $out_location_rem_text        = "<span class='bg-danger p-2'>NO</span>";
                                }else{
                                  $out_location_rem             = 0;
                                  $out_location_rem_text        = "<span class='bg-success p-2'>YES</span>";
                                }
                           
                             
                             ?>
                          <table class="table">
                            <thead class="thead-dark">
                              <tr>
                                <th scope="col">#</th>
                                <th scope="col">📍 Location</th>
                                <th scope="col">🎯 Total Target</th>
                                <th scope="col">✅ Completed</th>
                                <th scope="col">📉 Remaining</th>
                                <th scope="col">🏆 Achieved</th>
                              </tr>
                            </thead>
                            <tbody>
                              <tr>
                                <td>1</td>
                                <td> Out Station Locations </td>
                                <td><?= $total_out_station_meeting; ?></td>
                                <td>
                                  <a href="<?=base_url()."Menu/TargetMeetingDetails/Out_Station_Locations/$target_id/"?>" target="_BLANK" ><?= "<span class='bg-success p-2'>$com_out_location</span>"; ?></a>
                                </td>
                                <td><?= "<span class='bg-warning p-2'>$out_location_rem</span>"; ; ?></td>
                                <td><?= $out_location_rem_text; ?></td>
                              </tr>
                            </tbody>
                          </table>
                        </div>
                        <div class="col-md-6">
                          <div class="each">
                            <h4 class="each p-2 text-center" style="font-size: 16px;">Complete Meeting By Base Locations - <?= $to_user ?></h4>
                          </div>
                          <hr>
                          <?php 
                             //  dd($categoryCounts);
                                $total_base_location            =  $local_station_meeting;
                                $com_base_location              =  $categoryCounts['base_location'];

                                if($total_base_location > $com_base_location){
                                  $base_location_fy_rem            = $total_base_location - $com_base_location;
                                  $base_location_rem_text   = "<span class='bg-danger p-2'>NO</span>";
                                }else{
                                  $base_location_fy_rem            = 0;
                                  $base_location_rem_text   = "<span class='bg-success p-2'>YES</span>";
                                }
                            ?>
                          <table class="table">
                            <thead class="thead-dark">
                              <tr>
                                <th scope="col">#</th>
                                <th scope="col">📍 Target Location</th>
                                <th scope="col">🎯 Total Target</th>
                                <th scope="col">✅ Completed</th>
                                <th scope="col">📉 Remaining</th>
                                <th scope="col">🏆 Achieved</th>
                              </tr>
                            </thead>
                            <tbody>
                              <tr>
                                <td>1</td>
                                <td> Base Station Locations </td>
                                <td><?= $total_base_location; ?></td>
                                <td>
                                  <a href="<?=base_url()."Menu/TargetMeetingDetails/Base_Station_Locations/$target_id/"?>" target="_BLANK" ><?= "<span class='bg-success p-2'>$com_base_location</span>"; ?></a>
                                </td>
                                <td><?= "<span class='bg-warning p-2'>$base_location_fy_rem</span>"; ?></td>
                                <td><?= $base_location_rem_text; ?></td>
                              </tr>
                            </tbody>
                          </table>
                        </div>


                      </div>
                      <div class="each newleads-data text-center">
                        <h4 class="bg-info each p-2">No Of Prospective (New Leads) </h4>
                        <div class="row">
                          <div class="col-md-12">
                            <?php 
                              if($no_of_prospective > $allNewwLeadscnt){
                                $newlead_remcnt = $no_of_prospective - $allNewwLeadscnt;
                                $newlead_rem_text = "<span class='bg-danger p-2'>NO</span>";
                              }else{
                                $newlead_remcnt = 0;
                                $newlead_rem_text = "<span class='bg-success p-2'>YES</span>";
                              }
                              ?>
                            <table class="each table">
                              <thead class="thead-dark">
                                <tr>
                                  <th scope="col">#</th>
                                  <th scope="col">🎯 Target</th>
                                  <th scope="col">📊 Total Target</th>
                                  <th scope="col">✅ Completed</th>
                                  <th scope="col">📉 Remaining</th>
                                  <th scope="col">🏆 Achieved</th>
                                </tr>
                              </thead>
                              <tbody>
                                <tr>
                                  <td>1</td>
                                  <td> New Leads </td>
                                  <td><?= $no_of_prospective; ?></td>
                          
                                  <td>
                                    <a href="<?=base_url()."Menu/TargetNewLeadDetailsDetails/total_new_leads/$target_id"?>" target="_BLANK" ><?= "<span class='bg-success p-2'>$allNewwLeadscnt</span>"; ?></a>
                                  </td>
                                  <td><?= "<span class='bg-warning p-2'>$newlead_remcnt</span>"; ; ?></td>
                                  <td><?= $newlead_rem_text; ?></td>
                                </tr>
                              </tbody>
                            </table>
                          </div>
                        </div>
                      </div>
                      <div class="each newleads-data text-center">
                        <h4 class="bg-info each p-2">Proposal Target</h4>
                        <div class="row">
                          <div class="col-md-12">
                            <?php 
                              if($no_of_proposal > $allpropasalcnt){
                                $propasal_remcnt = $no_of_proposal - $allpropasalcnt;
                                $propasal_rem_text = "<span class='bg-danger p-2'>NO</span>";
                              }else{
                                $propasal_remcnt = 0;
                                $propasal_rem_text = "<span class='bg-success p-2'>YES</span>";
                              }
                              ?>
                            <table class="each table">
                              <thead class="thead-dark">
                                <tr>
                                  <th scope="col">#</th>
                                  <th scope="col">🎯 Target</th>
                                  <th scope="col">📊 Total Target</th>
                                  <th scope="col">✅ Completed</th>
                                  <th scope="col">📉 Remaining</th>
                                  <th scope="col">🏆 Achieved</th>
                                </tr>
                              </thead>
                              <tbody>
                                <tr>
                                  <td>1</td>
                                  <td> New Proposal </td>
                                  <td><?= $no_of_proposal; ?></td>
                                  <td>
                                    <a href="<?=base_url()."Menu/ProposalTargetDetails/total_target_proposal/$target_id"?>" target="_BLANK" ><?= "<span class='bg-success p-2'>$allpropasalcnt</span>"; ?></a>
                                  </td>
                                  <td><?= "<span class='bg-warning p-2'>$propasal_remcnt</span>"; ; ?></td>
                                  <td><?= $propasal_rem_text; ?></td>
                                </tr>
                              </tbody>
                            </table>
                          </div>
                        </div>
                      </div>
                      <hr>
                      <div class="each newleads-data text-center">
                        <h4 class="bg-info each p-2">Proposal School Target </h4>
                        <div class="row">
                          <div class="col-md-12">
                            <?php 
                              if($total_school == ''){
                                $total_school = 0;
                              }
                              if($school > $total_school){
                               $school_remcnt = $school - $total_school;
                               $school_rem_text = "<span class='bg-danger p-2'>NO</span>";
                              }else{
                               $school_remcnt = 0;
                               $school_rem_text = "<span class='bg-success p-2'>YES</span>";
                              }
                              ?>
                            <table class="each table">
                              <thead class="thead-dark">
                               <tr>
                                <th scope="col">#</th>
                                <th scope="col">🎯 Target</th>
                                <th scope="col">📊 Total</th>
                                <th scope="col">✅ Completed</th>
                                <th scope="col">📉 Remaining</th>
                                <th scope="col">🏆 Achieved</th>
                              </tr>
                              </thead>
                              <tbody>
                                <tr>
                                  <td>1</td>
                                  <td> New School </td>
                                  <td><?= $school; ?></td>
                                  <td>
                                    <a href="<?=base_url()."Menu/ProposalTargetDetails/total_target_proposal/$target_id"?>" target="_BLANK" ><?= "<span class='bg-success p-2'>$total_school</span>"; ?></a>
                                  </td>
                                  <td><?= "<span class='bg-warning p-2'>$school_remcnt</span>"; ; ?></td>
                                  <td><?= $school_rem_text; ?></td>
                                </tr>
                              </tbody>
                            </table>
                          </div>
                        </div>
                      </div>
                      <div class="each newleads-data text-center">
                        <h4 class="bg-info each p-2">Proposal Revenue Target </h4>
                        <div class="row">
                          <div class="col-md-12">
                            <?php 
                              if($total_revenue == ''){
                                $total_revenue = 0;
                              }
                                 if($revenue > $total_revenue){
                                   $revenue_remcnt = $revenue - $total_revenue;
                                   $revenue_rem_text = "<span class='bg-danger p-2'>NO</span>";
                                 }else{
                                   $revenue_remcnt = 0;
                                   $revenue_rem_text = "<span class='bg-success p-2'>YES</span>";
                                 }
                                 ?>
                            <table class="each table">
                              <thead class="thead-dark">
                                <tr>
                                  <th scope="col">#</th>
                                  <th scope="col">🎯 Target</th>
                                  <th scope="col">📊 Total Target</th>
                                  <th scope="col">✅ Complete</th>
                                  <th scope="col">📉 Remaining</th>
                                  <th scope="col">🏆 Achieved</th>
                                </tr>
                              </thead>
                              <tbody>
                                <tr>
                                  <td>1</td>
                                  <td> New Revenue </td>
                                  <td><?= $revenue; ?></td>
                                  <td>
                                    <a href="<?=base_url()."Menu/ProposalTargetDetails/total_target_proposal/$target_id"?>" target="_BLANK" ><?= "<span class='bg-success p-2'>$total_revenue</span>"; ?></a>
                                  </td>
                                  <td><?= "<span class='bg-warning p-2'>$revenue_remcnt</span>"; ; ?></td>
                                  <td><?= $revenue_rem_text; ?></td>
                                </tr>
                              </tbody>
                            </table>
                          </div>
                        </div>
                      </div>
                      <hr>
                      <div class="each newleads-data text-center">
                        <h4 class="bg-info each p-2">Closure School </h4>
                        <div class="row">
                          <div class="col-md-12">
                            <?php 
                              if($total_closer_school == ''){
                                $total_closer_school = 0;
                              }
                              if($school > $total_closer_school){
                               $school_remcnt_closer = $school - $total_closer_school;
                               $school_rem_text_clouser = "<span class='bg-danger p-2'>NO</span>";
                              }else{
                               $school_remcnt_closer = 0;
                               $school_rem_text_clouser = "<span class='bg-success p-2'>YES</span>";
                              }
                              ?>
                            <table class="each table">
                              <thead class="thead-dark">
                               <tr>
                                  <th scope="col">#</th>
                                  <th scope="col">🎯 Target</th>
                                  <th scope="col">📊 Total Target</th>
                                  <th scope="col">✅ Completed</th>
                                  <th scope="col">📉 Remaining</th>
                                  <th scope="col">🏆 Achieved</th>
                                </tr>
                              </thead>
                              <tbody>
                                <tr>
                                  <td>1</td>
                                  <td> New School </td>
                                  <td><?= $school; ?></td>
           
                                  <td>
                                    <a href="<?=base_url()."Menu/TargetCloserDetails/total_target_closer/$target_id"?>" target="_BLANK" ><?= "<span class='bg-success p-2'>$total_closer_school</span>"; ?></a>
                                  </td>

                                  <td><?= "<span class='bg-warning p-2'>$school_remcnt_closer</span>"; ; ?></td>
                                  <td><?= $school_rem_text_clouser; ?></td>
                                </tr>
                              </tbody>
                            </table>
                          </div>
                        </div>
                      </div>
                      <div class="each newleads-data text-center">
                        <h4 class="bg-info each p-2">Closure Revenue  </h4>
                        <div class="row">
                          <div class="col-md-12">
                            <?php 
                              if($total_closer_revenue == ''){
                                $total_closer_revenue = 0;
                              }
                                 if($revenue > $total_closer_revenue){
                                   $revenue_remcnt_clouser = $revenue - $total_closer_revenue;
                                   $revenue_rem_text_clouser = "<span class='bg-danger p-2'>NO</span>";
                                 }else{
                                   $revenue_remcnt_clouser = 0;
                                   $revenue_rem_text_clouser = "<span class='bg-success p-2'>YES</span>";
                                 }
                                 ?>
                            <table class="each table">
                              <thead class="thead-dark">
                               <tr>
                                  <th scope="col">#</th>
                                  <th scope="col">🎯 Target</th>
                                  <th scope="col">📊 Total Target</th>
                                  <th scope="col">✅ Complete</th>
                                  <th scope="col">📉 Remaining</th>
                                  <th scope="col">🏆 Achieved</th>
                                </tr>
                              </thead>
                              <tbody>
                                <tr>
                                  <td>1</td>
                                  <td> New Revenue </td>
                                  <td><?= $revenue; ?></td>
                                 
                                  <td>
                                    <a href="<?=base_url()."Menu/TargetCloserDetails/total_target_closer/$target_id"?>" target="_BLANK" ><?= "<span class='bg-success p-2'>$total_closer_revenue</span>"; ?></a>
                                  </td>
                                  <td><?= "<span class='bg-warning p-2'>$revenue_remcnt_clouser</span>"; ; ?></td>
                                  <td><?= $revenue_rem_text_clouser; ?></td>
                                </tr>
                              </tbody>
                            </table>
                          </div>
                        </div>
                      </div>
                    </div>
                    <div class="tab-content" id="tab-2">
                      <h2>Details</h2>
                      <p>Lorem ipsum dolor sit amet consectetur adipisicing elit. Quibusdam expedita sapiente architecto dignissimos error, fuga quaerat odit ipsam recusandae, modi illo tempore repellendus. Mollitia modi, tempore obcaecati eaque vitae voluptatum qui sapiente magni rem non laboriosam corrupti vel numquam magnam et quod laudantium nostrum ex est odio tempora nisi quae placeat? Quam consequuntur, numquam corporis quisquam eaque quos impedit quidem unde voluptatem dolore iure doloremque atque maiores aliquid error in asperiores dolorum commodi. Assumenda debitis culpa illo inventore unde minus facere fuga dolorum nobis fugit, necessitatibus nulla sapiente quis pariatur corrupti similique voluptatibus eaque quaerat! Sed ipsum recusandae sit consequuntur. 200</p>
                    </div>
                    <div class="tab-content" id="tab-3">
                      <h2>Contact Us</h2>
                      <p>Lorem ipsum dolor sit amet consectetur adipisicing elit. Quibusdam expedita sapiente architecto dignissimos error, fuga quaerat odit ipsam recusandae, modi illo tempore repellendus. Mollitia modi, tempore obcaecati eaque vitae voluptatum qui sapiente magni rem non laboriosam corrupti vel numquam magnam et quod laudantium nostrum ex est odio tempora nisi quae placeat? Quam consequuntur, numquam corporis quisquam eaque quos impedit quidem unde voluptatem dolore iure doloremque atque maiores aliquid error in asperiores dolorum commodi. Assumenda debitis culpa illo inventore unde minus facere fuga dolorum nobis fugit, necessitatibus nulla sapiente quis pariatur corrupti similique voluptatibus eaque quaerat! Sed ipsum recusandae sit consequuntur. 300</p>
                    </div>
                    <div class="tab-content" id="tab-4">
                      <h2>Frequently Asked Questions</h2>
                      <p>Lorem ipsum dolor sit amet consectetur adipisicing elit. Quibusdam expedita sapiente architecto dignissimos error, fuga quaerat odit ipsam recusandae, modi illo tempore repellendus. Mollitia modi, tempore obcaecati eaque vitae voluptatum qui sapiente magni rem non laboriosam corrupti vel numquam magnam et quod laudantium nostrum ex est odio tempora nisi quae placeat? Quam consequuntur, numquam corporis quisquam eaque quos impedit quidem unde voluptatem dolore iure doloremque atque maiores aliquid error in asperiores dolorum commodi. Assumenda debitis culpa illo inventore unde minus facere fuga dolorum nobis fugit, necessitatibus nulla sapiente quis pariatur corrupti similique voluptatibus eaque quaerat! Sed ipsum recusandae sit consequuntur. 400</p>
                    </div>
                    <div class="tab-content" id="tab-5">
                      <h2>Frequently Asked Questions</h2>
                      <p>Lorem ipsum dolor sit amet consectetur adipisicing elit. Quibusdam expedita sapiente architecto dignissimos error, fuga quaerat odit ipsam recusandae, modi illo tempore repellendus. Mollitia modi, tempore obcaecati eaque vitae voluptatum qui sapiente magni rem non laboriosam corrupti vel numquam magnam et quod laudantium nostrum ex est odio tempora nisi quae placeat? Quam consequuntur, numquam corporis quisquam eaque quos impedit quidem unde voluptatem dolore iure doloremque atque maiores aliquid error in asperiores dolorum commodi. Assumenda debitis culpa illo inventore unde minus facere fuga dolorum nobis fugit, necessitatibus nulla sapiente quis pariatur corrupti similique voluptatibus eaque quaerat! Sed ipsum recusandae sit consequuntur. 500</p>
                    </div>
                    <div class="tab-content" id="tab-6">
                      <h2>Frequently Asked Questions</h2>
                      <p>Lorem ipsum dolor sit amet consectetur adipisicing elit. Quibusdam expedita sapiente architecto dignissimos error, fuga quaerat odit ipsam recusandae, modi illo tempore repellendus. Mollitia modi, tempore obcaecati eaque vitae voluptatum qui sapiente magni rem non laboriosam corrupti vel numquam magnam et quod laudantium nostrum ex est odio tempora nisi quae placeat? Quam consequuntur, numquam corporis quisquam eaque quos impedit quidem unde voluptatem dolore iure doloremque atque maiores aliquid error in asperiores dolorum commodi. Assumenda debitis culpa illo inventore unde minus facere fuga dolorum nobis fugit, necessitatibus nulla sapiente quis pariatur corrupti similique voluptatibus eaque quaerat! Sed ipsum recusandae sit consequuntur. 600</p>
                    </div>
                    <div class="tab-content" id="tab-7">
                      <h2>Frequently Asked Questions</h2>
                      <p>Lorem ipsum dolor sit amet consectetur adipisicing elit. Quibusdam expedita sapiente architecto dignissimos error, fuga quaerat odit ipsam recusandae, modi illo tempore repellendus. Mollitia modi, tempore obcaecati eaque vitae voluptatum qui sapiente magni rem non laboriosam corrupti vel numquam magnam et quod laudantium nostrum ex est odio tempora nisi quae placeat? Quam consequuntur, numquam corporis quisquam eaque quos impedit quidem unde voluptatem dolore iure doloremque atque maiores aliquid error in asperiores dolorum commodi. Assumenda debitis culpa illo inventore unde minus facere fuga dolorum nobis fugit, necessitatibus nulla sapiente quis pariatur corrupti similique voluptatibus eaque quaerat! Sed ipsum recusandae sit consequuntur. 700</p>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <center><button class="each btn btn-info" id="printPage">Print Page</button> <br> </center>
            <br>
          </div>
          <!-- /.card -->
      </div>
      <!-- /.col -->
    </div>
    <!-- /.row -->
    </div>
    <!-- /.container-fluid -->
    </section>
    </div>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.4.0/jspdf.umd.min.js"></script>
    <script>
      $('#downloadPdf').click(function() {
       const { jsPDF } = window.jspdf;
       const doc = new jsPDF();
      
       html2canvas(document.body, {
           scale: 0.5 // Adjust scale to fit content
       }).then(canvas => {
           const imgData = canvas.toDataURL('image/png');
           const imgWidth = 210; // A4 width in mm
           const pageHeight = 295; // A4 height in mm
           const imgHeight = canvas.height * imgWidth / canvas.width;
           let heightLeft = imgHeight;
           let position = 0;
      
           doc.addImage(imgData, 'PNG', 0, position, imgWidth, imgHeight);
           heightLeft -= pageHeight;
      
           while (heightLeft >= 0) {
               position -= pageHeight;
               doc.addPage();
               doc.addImage(imgData, 'PNG', 0, position, imgWidth, imgHeight);
               heightLeft -= pageHeight;
           }
           doc.save('dashboard.pdf');
       });
      });
    </script>
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
      const tabLinks = document.querySelectorAll('.tab-link');
      const tabContents = document.querySelectorAll('.tab-content');
      
      tabLinks.forEach(link => {
      link.addEventListener('click', () => {
      // Remove active class from all links
      tabLinks.forEach(link => link.classList.remove('active'));
      // Add active class to clicked link
      link.classList.add('active');
      
      // Hide all tab contents
      tabContents.forEach(content => content.classList.remove('active'));
      
      // Show current tab content
      const targetTab = document.getElementById(link.dataset.tab);
      targetTab.classList.add('active');
      });
      });
      
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
      $("#printPage").click(function() {
        window.print();
      });
      
      
      
      
    </script>
  </body>
</html>
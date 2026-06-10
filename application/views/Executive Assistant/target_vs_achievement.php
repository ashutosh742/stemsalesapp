<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="wi$dth=device-wi$dth, initial-scale=1">
    <title>Target vs Achievement | STEM APP | WebAPP</title>
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
      .tabs-container {
      height: auto;
      border-radius: 20px;
      box-shadow: 0 20px 40px rgba(0, 0, 0, 0.15);
      overflow: hidden;
      position: relative;
      box-shadow:inset 4px 4px 7px rgba(55,84,170,.15),inset -4px -4px 10px #fff,0 0 2px rgba(255,255,255,.2);transition:box-shadow .2s ease-in-out
      }
      .tabs {
      padding: 10px 20px 15px 20px; 
      }
      .tab-links {
      /* display: flex;
      justify-content: space-between; */
      border-bottom: 1px solid #f0f0f0;
      }
      [type=button]:not(:disabled), [type=reset]:not(:disabled), [type=submit]:not(:disabled), button:not(:disabled) {
      color: navy;
      padding: 10px;
      margin: 5px;
      font-size: 18px;
      font-weight: 600;
      background: blanchedalmond;
      box-shadow:inset 4px 4px 7px rgba(55,84,170,.15),inset -4px -4px 10px #fff,0 0 2px rgba(255,255,255,.2);transition:box-shadow .2s ease-in-out
      }
      .btn-group>.btn-group:not(:first-child)>.btn, .btn-group>.btn:not(:first-child) {
      border: none;
      }
      .tab-link {
      background: none;
      border: none;
      font-size: 16px;
      font-weight: 500;
      padding: 15px 30px;
      color: #ccc;
      cursor: pointer;
      position: relative;
      transition: color 0.3s ease;
      }
      .tab-link.active,
      .tab-link.active, .tab-link:hover {
      color: #b84de5;
      padding: 10px 24px;
      margin: 5px;
      font-size: 18px;
      font-weight: 600;
      transition: all 0.3s ease-in-out;
      }
      .tab-link i {
      margin-right: 10px;
      }
      .tab-link::after {
      content: '';
      position: absolute;
      width: 0;
      height: 3px;
      bottom: -1px;
      left: 50%;
      background: linear-gradient(45deg, #b84de5, #7d41ff); /* Purple gradient */
      transition: all 0.4s ease;
      }
      .tab-link.active::after {
      width: 100%;
      left: 0;
      }
      .tab-content {
      display: none;
      animation: fadeInUp 0.5s ease;
      padding: 5px 10px 15px 10px;
      }
      .tab-content.active {
      display: block;
      }
      @keyframes fadeInUp {
      from {
      opacity: 0;
      transform: translateY(20px);
      }
      to {
      opacity: 1;
      transform: translateY(0);
      }
      }
      .cta-btn {
      display: inline-block;
      padding: 12px 25px; 
      background: linear-gradient(45deg, #b84de5, #7d41ff);
      color: white;
      border: none;
      border-radius: 50px;
      cursor: pointer;
      transition: background 0.4s ease;
      font-weight: 500;
      margin-top: 20px;
      }
      .cta-btn:hover {
      background: linear-gradient(45deg, #9c3bce, #6b3ee8); 
      }
      @media screen and (max-width: 600px) {
      .tab-links {
      flex-direction: column;
      align-items: center;
      }
      .tab-link {
      text-align: center;
      width: 100%;
      padding: 15px 0;
      }
      }
      .tab-content.active{
      background-color: #008b8b14;
      margin-top: 16px;
      box-shadow: inset 4px 4px 7px rgba(55, 84, 170, .15), inset -4px -4px 10px #fff, 0 0 2px rgba(255, 255, 255, .2);
      transition: box-shadow .2s ease-in-out;
      border-radius: 10px;
      }
      h4.each.p-2.text-center {
      color: deeppink;
      font-family: system-ui;
      font-weight: 700;
      letter-spacing: 2px;
      }
      .row.meetings-data {
    background: beige;
    padding: 10px;
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
            <?php 
              $slct_user_id             = $targetData[0]->user_id;
              $no_of_meeting            = $targetData[0]->no_of_meeting;
              $no_of_proposal           = $targetData[0]->no_of_proposal;
              $no_of_prospective        = $targetData[0]->no_of_prospective;
              $revenue                  = $targetData[0]->revenue;
              $school                   = $targetData[0]->school;
              $partner_id               = $targetData[0]->partner_id;
              $out_station_meeting      = $targetData[0]->out_station_meeting;
              $local_station_meeting    = $targetData[0]->local_station_meeting;
              $start_date               = $targetData[0]->start_date;
              $end_date                 = $targetData[0]->end_date;
              $target_by                = $targetData[0]->target_by;
              $created_at               = $targetData[0]->created_at;
              $to_user                  = $targetData[0]->to_user;
              $by_user                  = $targetData[0]->by_user;
              ?>
            <div class="card-body">
              <div class="each p-2 bg-info1 text-center">
                <h2 class="text-center text-uppercase" style="font-weight: 700;font-family: auto;" >Target vs Achievement</h2>
                <h5><?= $to_user.' - '.$by_user; ?></h5>
                <hr>
                <h6><?= $start_date.' - '.$end_date; ?></h6>
              </div>
            </div>
            <hr>
            <div class="row">
              <div class="col-12">
                <!-- /.card-header -->
                <div class="card-body">
                  <div class="each body-content p-4">
                    <div class="table-responsive">
                      <table id="example1" class="table table-striped table-bordered" cellspacing="0" width="100%">
                        <thead>
                          <tr>
                            <th>S.No</th>
                            <th>Target User Name</th>
                            <th>No of Poposal</th>
                            <th>No of Prospective (New Leads)</th>
                            <th>Revenue</th>
                            <th>No of School</th>
                            <th>No of Meeting</th>
                            <th>Partner Type</th>
                            <th>Out Station Meeting</th>
                            <th>Local Meeting</th>
                            <th>Start Date</th>
                            <th>End Date</th>
                            <th>Created Date</th>
                            <th>Target By</th>
                            <!-- <th>View</th> -->
                          </tr>
                        </thead>
                        <tbody>
                          <?php $i = 1; foreach($targetData as $data): ?>
                          <tr>
                            <td><?= $i; ?></td>
                            <td><?= $data->to_user; ?></td>
                            <td><?= $data->no_of_proposal; ?></td>
                            <td><?= $data->no_of_prospective; ?></td>
                            <td><?= $data->revenue; ?></td>
                            <td><?= $data->school; ?></td>
                            <td><?= $data->no_of_meeting; ?></td>
                            <td>
                              <?php
                                if($data->partner_id !== '' ){
                                  $partnearray = json_decode($data->partner_id, true);
                                  foreach($partnearray as $key => $value){
                                    $getpartner_name = $this->Menu_model->get_partnerbyid($key)[0]->name;
                                    $partner_text = "<span class='bg-primary p-2'>$getpartner_name&nbsp;&nbsp;:&nbsp;&nbsp;$value</span> <br/><br/>";
                                    echo $partner_text;
                                  }
                                
                                }else{
                                  echo "<span class='bg-warning p-2'>Not&nbsp;Set</span>";
                                }
                                ?>
                            </td>
                            <td><?= $data->out_station_meeting; ?></td>
                            <td><?= $data->local_station_meeting; ?></td>
                            <td><?= $data->start_date; ?></td>
                            <td><?= $data->end_date; ?></td>
                            <td><?= $data->created_at; ?></td>
                            <td><?= $data->by_user; ?></td>
                            <!-- <td> <a href="<?=base_url();?>Menu/target_vs_achievement/<?= $data->id; ?>">View</a> </td> -->
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
                  
                  // $start_date = '2024-10-01';
                  // $end_date   = date("Y-m-d");
                  
                  $allPropasal              = $this->Menu_model->GetProposalBetweenDate($slct_user_id,$start_date,$end_date);
                  $allMeeting               = $this->Menu_model->GetMeetingBetweenDate($slct_user_id,$start_date,$end_date);
                  $allMeetingcnt            = sizeof($allMeeting);
                  
                  $groupedPartnerData = [];
                  foreach ($allMeeting as $item) {
                      $partnerTypeId = $item->partnerType_id; // Get the partnerType_id value
                      if (!isset($groupedPartnerData[$partnerTypeId])) {
                          $groupedPartnerData[$partnerTypeId] = 0; // Initialize count if not already set
                      }
                      $groupedPartnerData[$partnerTypeId]++; // Increment the count
                  }
                  
                  $groupedClusterData = [];
                  foreach ($allMeeting as $item) {
                      $cmp_clusterid = $item->cluster_id; // Get the partnerType_id value
                      if (!isset($groupedClusterData[$cmp_clusterid])) {
                          $groupedClusterData[$cmp_clusterid] = 0; // Initialize count if not already set
                      }
                      $groupedClusterData[$cmp_clusterid]++; // Increment the count
                  }
                  
                  
                  
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
                  
              
                  $allNewwLeads             = $this->Menu_model->GetNeewLeadBetweenDate($slct_user_id,$start_date,$end_date);
                  $allNewwLeadscnt          = sizeof($allNewwLeads);

                  $allRevenue               = $this->Menu_model->GetExpectedRevenueBetweenDate($slct_user_id,$start_date,$end_date);
                  $total_revenue            = $allRevenue[0]->total_revenue;

                  $allSchool                = $this->Menu_model->GetExpectedSchoolBetweenDate($slct_user_id,$start_date,$end_date);
                  $total_school             = $allSchool[0]->total_school;
                
                  $allpropasal              = $this->Menu_model->GetTotalPropasalByUID($slct_user_id,$start_date,$end_date);
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
                        <div class="col-md-6">
                          <div class='each card p-2'>
                            <table class="table">
                              <thead class="thead-dark">
                                <tr>
                                  <th scope="col">#</th>
                                  <th scope="col">Target</th>
                                  <th scope="col">Total Target</th>
                                  <th scope="col">Complete</th>
                                  <th scope="col">Remaining</th>
                                  <th scope="col">Achived</th>
                                </tr>
                              </thead>
                              <tbody>
                                <tr>
                                  <td>1</td>
                                  <td>Meetings</td>
                                  <td><?= $no_of_meeting; ?></td>
                                  <td><?= "<span class='bg-success p-2'>$allMeetingcnt</span>"; ?></td>
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
                          <table class="table">
                            <thead class="thead-dark">
                              <tr>
                                <th scope="col">#</th>
                                <th scope="col">Target Partner Name</th>
                                <th scope="col">Total Target</th>
                                <th scope="col">Complete</th>
                                <th scope="col">Remaining</th>
                                <th scope="col">Achived</th>
                              </tr>
                            </thead>
                            <tbody>
                              <?php $i=1; foreach($partnearray as $key => $value){
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
                                <td><?= "<span class='bg-success p-2'>$meetpartcomp</span>" ?></td>
                                <td><?= "<span class='bg-warning p-2'>$meetpartcomp_rem</span>"; ?></td>
                                <td><?= $meetpartcomp_rem_text ?></td>
                              </tr>
                              <?php $i++; } ?>
                            </tbody>
                          </table>
                        </div>
                        <hr>
                        <div class="col-md-12">
                          <div class="each">
                            <h4 class="each p-2 text-center" style="font-size: 16px;">Complete Meeting By Partner - <?= $to_user ?></h4>
                          </div>
                          <hr>
                          <table class="table">
                            <thead class="thead-dark">
                              <tr>
                                <th scope="col">#</th>
                                <th scope="col">Partner Name</th>
                                <th scope="col">Complete</th>
                              </tr>
                            </thead>
                            <tbody>
                              <?php $i=1; foreach($groupedPartnerData as $key => $value){
                                $getpartner_name = $this->Menu_model->get_partnerbyid($key)[0]->name;
                                 ?>
                              <tr>
                                <td><?= $i; ?></td>
                                <td><?= $getpartner_name; ?></td>
                                <td><?= "<span class='bg-success p-2'>$value</span>"; ?></td>
                              </tr>
                              <?php $i++; } ?>
                            </tbody>
                          </table>
                        </div>
                        <hr>
                        <hr>
                        <div class="col-md-6">
                          <div class="each">
                            <h4 class="each p-2 text-center" style="font-size: 16px;">Complete Meeting By Out Station Locations - <?= $to_user ?></h4>
                          </div>
                          <hr>
                          <?php 
                            if($base_cluster_of_user == ''){
                             echo $alert_message = "<Center><span class='each bg-danger p-2' style='font-size: 12px;'>* Please Add Base Locations Of $to_user For Proper Calculations using the user profile edit page.</span> <br> <hr></center>";
                            }
                             $cluster_array2cnt = count($cluster_array2);
                             if($out_station_meeting > $cluster_array2cnt){
                               $cluster_array2cnt1 = $out_station_meeting - $cluster_array2cnt;
                               $meetclustercomp_rem_text = "<span class='bg-danger p-2'>NO</span>";
                             }else{
                               $cluster_array2cnt1 = 0;
                               $meetclustercomp_rem_text = "<span class='bg-success p-2'>YES</span>";
                             }
                             
                             ?>
                          <table class="table">
                            <thead class="thead-dark">
                              <tr>
                                <th scope="col">#</th>
                                <th scope="col">Target</th>
                                <th scope="col">Total Target</th>
                                <th scope="col">Complete</th>
                                <th scope="col">Remaining</th>
                                <th scope="col">Achived</th>
                              </tr>
                            </thead>
                            <tbody>
                              <tr>
                                <td>1</td>
                                <td> Out Station Locations </td>
                                <td><?= $out_station_meeting; ?></td>
                                <td><?= "<span class='bg-success p-2'>$cluster_array2cnt</span>"; ?></td>
                                <td><?= "<span class='bg-warning p-2'>$cluster_array2cnt1</span>"; ; ?></td>
                                <td><?= $meetclustercomp_rem_text; ?></td>
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
                            if($base_cluster_of_user == ''){
                              echo $alert_message = "<Center><span class='each bg-danger p-2' style='font-size: 12px;'>* Please Add Base Locations Of $to_user For Proper Calculations using the user profile edit page.</span> <br> <hr></center>";
                            }
                            $cluster_array1cnt = count($cluster_array1);
                            if($out_station_meeting > $cluster_array1cnt){
                            $cluster_array1cnt_rem = $out_station_meeting - $cluster_array1cnt;
                            $meetclustercomp1_rem_text = "<span class='bg-danger p-2'>NO</span>";
                            }else{
                            $cluster_array1cnt_rem = 0;
                            $meetclustercomp1_rem_text = "<span class='bg-success p-2'>YES</span>";
                            }
                            
                            ?>
                          <table class="table">
                            <thead class="thead-dark">
                              <tr>
                                <th scope="col">#</th>
                                <th scope="col">Target</th>
                                <th scope="col">Total Target</th>
                                <th scope="col">Complete</th>
                                <th scope="col">Remaining</th>
                                <th scope="col">Achived</th>
                              </tr>
                            </thead>
                            <tbody>
                              <tr>
                                <td>1</td>
                                <td> Base Station Locations </td>
                                <td><?= $local_station_meeting; ?></td>
                                <td><?= "<span class='bg-success p-2'>$cluster_array1cnt</span>"; ?></td>
                                <td><?= "<span class='bg-warning p-2'>$cluster_array1cnt_rem</span>"; ?></td>
                                <td><?= $meetclustercomp1_rem_text; ?></td>
                              </tr>
                            </tbody>
                          </table>
                        </div>
                        <div class="col-md-12">
                          <div class="each">
                            <h4 class="each p-2 text-center" style="font-size: 16px;">Complete Meeting By Locations - <?= $to_user ?></h4>
                          </div>
                          <hr>
                          <table class="table">
                            <thead class="thead-dark">
                              <tr>
                                <th scope="col">#</th>
                                <th scope="col">Cluster Name</th>
                                <th scope="col">Complete</th>
                              </tr>
                            </thead>
                            <tbody>
                              <?php $i=1; 
                                foreach($groupedClusterData as $key => $value){
                                 if($key !== ''){
                                 $meet_clusternameData = $this->Menu_model->getClusterByid($key);
                                 
                                 $clustername_name  = $meet_clusternameData[0]->clustername;
                                 $in_state          = $meet_clusternameData[0]->in_state;
                                 $in_district       = $meet_clusternameData[0]->in_district;
                                 $in_city           = $meet_clusternameData[0]->in_city;
                                 if($in_state !== '' && $in_state !== 'NULL'){
                                      $inState = $this->Menu_model->getInStateById($in_state)[0]->state_title;
                                 }else{
                                     $inState = '';
                                 }
                                
                                 $inDistrict = $this->Menu_model->getInDistricById($in_district);
                                 
                                 $distData = '';
                                 foreach($inDistrict as $dis){
                                 $distData .= $dis->district_title.'  | ';
                                 }
                                 $distData = rtrim($distData, " | ");
                                 
                                 $in_cityid = rtrim($in_city, ',');
                                 $inCity = $this->Menu_model->getInCityById($in_cityid);
                                 
                                 $cityData = '';
                                 foreach($inCity as $cityd){
                                 $cityData .= $cityd->name.'  | ';
                                 }
                                 $cityData = rtrim($cityData, " | ");
                                
                                 $title_message = 'State : '.$inState.' |||||||||| District : '.$distData.' ||||||||||||||||| City : '.$cityData;
                                  ?>
                              <tr title="<?= $title_message; ?>">
                                <td><?= $i; ?></td>
                                <td><?= $clustername_name; ?> - (<?= $inState; ?>)</td>
                                <td><?= "<span class='bg-success p-2'>$value</span>"; ?></td>
                              </tr>
                              <?php $i++; }} ?>
                            </tbody>
                          </table>
                        </div>
                      </div>

                      <div class="each newleads-data text-center">
                        <h4 class="bg-info each p-2">New Leads </h4>
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
                                <th scope="col">Target</th>
                                <th scope="col">Total Target</th>
                                <th scope="col">Complete</th>
                                <th scope="col">Remaining</th>
                                <th scope="col">Achived</th>
                              </tr>
                            </thead>
                            <tbody>
                              <tr>
                                <td>1</td>
                                <td> New Leads </td>
                                <td><?= $no_of_prospective; ?></td>
                                <td><?= "<span class='bg-success p-2'>$allNewwLeadscnt</span>"; ?></td>
                                <td><?= "<span class='bg-warning p-2'>$newlead_remcnt</span>"; ; ?></td>
                                <td><?= $newlead_rem_text; ?></td>
                              </tr>
                            </tbody>
                          </table>
                          </div>
                          </div>
                      </div>

                      <div class="each newleads-data text-center">
                        <h4 class="bg-info each p-2">New Proposal </h4>
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
                                <th scope="col">Target</th>
                                <th scope="col">Total Target</th>
                                <th scope="col">Complete</th>
                                <th scope="col">Remaining</th>
                                <th scope="col">Achived</th>
                              </tr>
                            </thead>
                            <tbody>
                              <tr>
                                <td>1</td>
                                <td> New Proposal </td>
                                <td><?= $no_of_proposal; ?></td>
                                <td><?= "<span class='bg-success p-2'>$allpropasalcnt</span>"; ?></td>
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
                                <th scope="col">Target</th>
                                <th scope="col">Total Target</th>
                                <th scope="col">Complete</th>
                                <th scope="col">Remaining</th>
                                <th scope="col">Achived</th>
                              </tr>
                            </thead>
                            <tbody>
                              <tr>
                                <td>1</td>
                                <td> New School </td>
                                <td><?= $school; ?></td>
                                <td><?= "<span class='bg-success p-2'>$total_school</span>"; ?></td>
                                <td><?= "<span class='bg-warning p-2'>$school_remcnt</span>"; ; ?></td>
                                <td><?= $school_rem_text; ?></td>
                              </tr>
                            </tbody>
                          </table>
                          </div>
                          </div>
                      </div>


                      
                      <div class="each newleads-data text-center">
                        <h4 class="bg-info each p-2">Proposal Revenue  Target </h4>
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
                                <th scope="col">Target</th>
                                <th scope="col">Total Target</th>
                                <th scope="col">Complete</th>
                                <th scope="col">Remaining</th>
                                <th scope="col">Achived</th>
                              </tr>
                            </thead>
                            <tbody>
                              <tr>
                                <td>1</td>
                                <td> New Revenue </td>
                                <td><?= $revenue; ?></td>
                                <td><?= "<span class='bg-success p-2'>$total_revenue</span>"; ?></td>
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
          <th scope="col">Target</th>
          <th scope="col">Total Target</th>
          <th scope="col">Complete</th>
          <th scope="col">Remaining</th>
          <th scope="col">Achived</th>
        </tr>
      </thead>
      <tbody>
        <tr>
          <td>1</td>
          <td> New School </td>
          <td><?= $school; ?></td>
          <td><?= "<span class='bg-success p-2'>$total_closer_school</span>"; ?></td>
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
          <th scope="col">Target</th>
          <th scope="col">Total Target</th>
          <th scope="col">Complete</th>
          <th scope="col">Remaining</th>
          <th scope="col">Achived</th>
        </tr>
      </thead>
      <tbody>
        <tr>
          <td>1</td>
          <td> New Revenue </td>
          <td><?= $revenue; ?></td>
          <td><?= "<span class='bg-success p-2'>$total_closer_revenue</span>"; ?></td>
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
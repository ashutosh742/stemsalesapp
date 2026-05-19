<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title> On RP Meeting Assign Same Status Since (n) Days | STEM APP | WebAPP</title>
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
    display: flex;
}
   
th.text-capitalize:nth-child(1),
td:nth-child(1),
th.text-capitalize:nth-child(2),
td:nth-child(2) {
    position: sticky;
    left: 0;
    color: white;
    z-index: 10;
}

tbody tr td:nth-child(1),
tbody tr td:nth-child(2) {
    background-color: white;
    color: black;
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
                      <h1 class="m-0 premium-color-1" style="color: #ff009b;">📌 After RP Meetings Assign Same Status Since (n) Days (Current Financial Year)</h1>
                      <p class="premium-color-2" style="color: #ff0000;">After RP meetings, tasks stay stuck with the same status for (n) days ⏳—causing delays, no progress, and frustration 😓. Quick resolution is needed for smoother workflow 🚀.</p>
                      <p class="premium-color-2" style="color: #2c00ee;"><strong><mark><?=$sdate ?> To <?=$edate ?></mark></strong></p>
                      <p class="premium-color-2" style="color: #2c00ee;"><strong><mark><?=$days_inclusive ?> Days</mark></strong></p>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <div class="row mb-2">
       

             

  <?php $clusterPstDatas = $this->Menu_model->GetAdminActiveTeam($user['user_id']);?>
  <div class="col text-center formcenterData">
    <div>
      <hr>
        <form action="<?=base_url()?>Reports/AfterAssignLineManagerSameStatusSinceDays" method="post" class="mt-3">
            <div class="row mb-4">


                <!-- <div class="col" style="display: none;">
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


                <div class="col">
                    <label for="selectedDate">Select Days</label>
                    <select id="days_filter" name="days_filter" class="form-control">
                        <option value="8 Days" <?= ($days_filter == "8 Days") ? 'selected' : '' ?>>8 Days</option>
                        <option value="15 Days" <?= ($days_filter == "15 Days") ? 'selected' : '' ?>>15 Days</option>
                        <option value="30 Days" <?= ($days_filter == "30 Days") ? 'selected' : '' ?>>30 Days</option>
                        <option value="more than 30 Days" <?= ($days_filter == "more than 30 Days") ? 'selected' : '' ?>>more than 30 Days</option>
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
          </div>
        </div>

        <hr>

         <div class="card-section" style="background: linear-gradient(to right, #ede7f6, #ffffff); border-radius: 12px; box-shadow: 0 4px 8px rgba(0,0,0,0.1); padding: 20px;">
                   <div class="text-center" style="background: linear-gradient(to right, #ede7f6, #ffffff); border-radius: 12px; box-shadow: 0 4px 8px rgba(0,0,0,0.1); padding: 20px;">
                      <h5>
                      <i>📊 Status Wise</i> <br>
                      <small>
                        📊 Status Wise provides a clear, structured view of progress by categorizing tasks ✅, clients 🏢, or activities 📌 based on their current stage 🔄. It helps identify bottlenecks 🚧, track achievements 🏆, and prioritize actions 🎯 effectively. By organizing data status-wise 📂, decision-making becomes easier 🤝, performance visibility improves 👀, and teams 👥 can focus on the right areas to drive better outcomes 🚀.
                      </small>
                    </h5>

                    </div>
                    <hr>
                    <div class="row">
                    <?php  
                     $descriptions = [
                            'OPEN RPEM'        => 'Companies in the initial OPEN RPEM stage of the funnel.',
                            'Reachout'         => 'Companies where first reachout has been initiated.',
                            'Open'             => 'Companies with an open opportunity in the funnel.',
                            'Not Interested'   => 'Leads that have shown no interest currently.',
                            'TTD-Reachout'     => 'Companies in the targeted TTD reachout process.',
                            'Will do Later'    => 'Leads postponed for future follow-up.',
                            'Tentative'        => 'Companies showing tentative interest in engagement.',
                            'Positive'         => 'Companies with a positive response towards engagement.',
                            'Positive-NAP'     => 'Positive companies, but marked as NAP (Not Active/Pending).',
                            'Very Positive'    => 'Highly engaged companies showing strong intent.',
                            'Closure'          => 'Deals successfully closed with the company.',
                        ];

                        foreach ($statusCounts as $key => $value) {
                        
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
                            <h3 class="card-text"><?= $value ?></h3>
                        </div>
                        </div>
                    </div>
                    <?php
                        }
                        ?>
                    </div>
                </div>








             
                
          
                <hr>
         <div class="card-section" style="background: linear-gradient(to right, #ede7f6, #ffffff); border-radius: 12px; box-shadow: 0 4px 8px rgba(0,0,0,0.1); padding: 20px;">
                   <div class="text-center" style="background: linear-gradient(to right, #ede7f6, #ffffff); border-radius: 12px; box-shadow: 0 4px 8px rgba(0,0,0,0.1); padding: 20px;">
                      <h5>
                      <i>Main BD With Status 👨‍💼✅</i> <br>
                      <small>
                        Main BD with Status 📊✨ shows each Business Development (BD) executive along with the progress of their assigned companies. It reflects funnel stage 🎯, review status 📝, and active tasks 🔄. This helps managers track workload ⚖️, measure performance 🚀, and ensure timely follow-ups ⏰, driving accountability and smoother business growth 📈🤝.
                      </small>
                    </h5>

                    </div>
                    <hr>
                    <div class="row">
                    <?php  
                    
                       
                        foreach ($nameCounts as $key => $value) {
                        
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

                            ?>
                    <div class="col-md-3 mb-3">
                        <div class="card text-center shadow" style="background-color: <?= $backgroundColor ?>; color: <?= $backgroundColorNew ?>;">
                        <div class="card-body" style="color:<?=$backgroundColorNew;?>!important">
                           

                            <div class="text-center">
                                <h5><b><?= $heading ?></b></h5>
                                <h3><?= $value ?></h3>
                            <?php 
                            $mainBDwithStatus = $mainBDStatusCounts[$key];
                            $mainBDwithStatusCnt =  sizeof($mainBDwithStatus);
                            
                            if($mainBDwithStatusCnt > 0){
                               foreach ($mainBDwithStatus as $statusname => $statusValue) { ?>
                                        <span style="box-shadow: rgba(0, 0, 0, 0.12) 0px 1px 3px, rgba(0, 0, 0, 0.24) 0px 1px 2px;" class="p-2 badge badge-pill badge-dark mt-1"><strong><?= $statusname ?>: </strong><?= $statusValue ?></span>
                             <?php  } } ?>
                            </div>
                        </div>
                        </div>
                    </div>
                    <?php
                        }
                        ?>
                    </div>
                </div>

               


<br>
        <!-- Main content -->
        <section class="content">
          <div class="container-fluid">
            <!-- Total Plan Meetings Group -->
             <div class="taskfrequency" style="background: linear-gradient(to right, rgb(189 255 157 / 34%), rgb(231 2 2 / 10%)); box-shadow: 0 4px 8px rgba(0,0,0,0.1); padding: 20px;">
            
            </div>


    
            <hr>
        
            <?php 
            
            // dd($sameStatusSinceDaysDatas);
            ?>


         

     
            <div class="body-content">
              <div class="page-header" style="background: linear-gradient(to right, #ffffff, #ffffff); border-radius: 12px; box-shadow: 0 4px 8px rgba(0,0,0,0.1); padding: 20px;">
              

               <hr>


                  <div class="text-center" style="background: linear-gradient(to right, #ede7f6, #ffffff); border-radius: 12px; box-shadow: 0 4px 8px rgba(0,0,0,0.1); padding: 20px;">
                      <h5>
                      <i>Same Status Since Days With Company Count</i>
                    </h5>
                  </div>
                  <br>

                <div class="table-responsive">
                  
                  <table class="table table-bordered table-striped text-center">
                    <thead class="table-dark">
                        <tr>
                            <?php foreach ($resultNumberOfDays as $range => $count):
                           
                              list($start, $end) = explode("_", $range);
                              $range = $start . " - " . $end . " days";
                              ?>
                                <th><?= $range ?> Days</th>
                            <?php endforeach; ?>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <?php foreach ($resultNumberOfDays as $range => $count):  
                            
                            if($range == '50_+'){
                              $range = '50_';
                            }
                               ?>
                                <td>
                                  <a target="_blank" class="bg-success p-1" href="<?=base_url()?>/Reports/AfterAssignLineManagerSameStatusSinceDaysLists/<?=$sdate?>/<?=$edate?>/<?=$admin_id_filter?>/<?=$rm_filter?>/<?=$range?>">
                                  <?= $count ?>
                                </a>
                                </td>
                            <?php endforeach; ?>
                        </tr>
                    </tbody>
                </table>
                </div>

              <hr>
              
              <div class="table-responsive text-nowrap" style="overflow-x: auto;">
                  <table id="example1" class="table table-striped table-bordered" cellspacing="0" width="100%">
                  <thead class="thead-dark">
                   <tr>
                     <th>#️⃣</th>
                    <!-- <th>🆔 Inid</th> -->
                    <th>🏢 Company Name</th>
                    <th>🆔 CID</th>
                    <th>👤 Main BD Name</th>
                    <th>👤 Cluster Manager </th>
                    <th>👤 Assistant Cluster Manager </th>
                    <th>👤 PST </th>
                    <th>👤 Assistant Sales Head (NAE) </th>
                    <th>👤 Assistant Sales Head (W) </th>
                    <th>👤 Assistant Sales Head (S) </th>
                    <th>👤 RM East </th>
                    <th>👤 RM North </th>

                    <th>🤝 Partner Name</th>
                    <th>📊 Current Status</th>
                    <th>⏳ Days on Same Status</th>
                    
                    <th>📅 Review Date</th>
                    <th>📑 Review Type</th>
                    
                    <th>📆 Review Date</th>

                    <th>📝 Last Task Name</th>
                    <th>👨‍💻 Task Username</th>
                    <th>📌 Last Task On Status</th>
                    <th>🔄 Last Update Status</th>

                    <th>📊 Total Log In Days</th>
                    <th>⏱️ Last Task Date</th>
                    <th>📅 Last Task Days</th>
                    <!-- <th>🔢 Last Task ID</th> -->
                    <th>✅ Last Task Status</th>
                </tr>         
            </thead>
            <tbody>
               <?php
        $i = 1;
        foreach ($sameStatusSinceDaysDatas as $row) {
        $meeting_user_id = $row->user_id;
                  $meeting_task_id = $row->task_id;

                  $r = rand(150, 255);
                  $g = rand(150, 255);
                  $b = rand(150, 255);
                  $backgroundColor = "rgba($r, $g, $b,.2)";

                  $hue        = rand(0, 360); // Full color wheel
                  $saturation = rand(60, 100); // High saturation for rich colors
                  $lightness  = rand(30, 45); // Low lightness for a deeper tone
                  $backgroundColorNew = "hsl($hue, $saturation%, $lightness%)";

                  ?>
                  
                  <tr style="background-color: <?php echo $backgroundColor; ?>;color:<?=$backgroundColorNew;?>!important" >
                <td><?= $i; ?></td>
                <!-- <td><?= $row->inid ?></td> -->
               
                <td><a style="color:<?=$backgroundColorNew;?>!important" target="_BLANK" href="<?=base_url();?>Menu/CompanyDetails/<?=$row->cid?>"><?= $row->compname ?></a></td>

                <td><a style="color:<?=$backgroundColorNew;?>!important" target="_BLANK" href="<?=base_url();?>Menu/CompanyDetails/<?=$row->cid?>"><?= $row->cid ?></a></td>

                <td><?= $row->main_bd_name ?></td>
                <td><?= $row->cluster_manager_name ?></td>
                <td><?= $row->acm_co_name ?></td>
                <td><?= $row->pst_name ?></td>
                <td><?= $row->ash_nae_co_name ?></td>
                <td><?= $row->ash_w_co_name ?></td>
                <td><?= $row->ash_s_co_name ?></td>
                <td><?= $row->rm_east_co_name ?></td>
                <td><?= $row->rm_north_co_name ?></td>

                <td><?= $row->pname ?></td>
                <td><?= $row->current_status ?></td>
                <td><?= $row->days ?></td>
               

                <td><?= $row->main_review_date ?></td>
                <td><?= $row->main_review_rtype ?></td>
                
                <td><?= $row->review_date ?></td>
               

                <td><?= $row->last_task_name ?></td>
                <td><?= $row->task_username ?></td>
                <td><?= $row->last_task_on_status_name ?></td>
                <td><?= $row->last_update_status_name ?></td>
                 <td><?= $row->total_log_in_days ?></td>

                <td><?= $row->last_task_update ?></td>
                <td><?= $row->last_task_days ?></td>
                <!-- <td><?= $row->last_task_id ?></td> -->
                <td><?= $row->last_task_status ?></td>
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
        $("#example6").DataTable({
          "responsive": false, "lengthChange": false, "autoWidth": false,
          "buttons": ["copy", "csv", "excel", "pdf", "print", "colvis"]
        }).buttons().container().appendTo('#example6_wrapper .col-md-6:eq(0)');
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

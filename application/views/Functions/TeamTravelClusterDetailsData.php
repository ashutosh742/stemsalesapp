<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Travel Cluster Details | Stem APP | WebAPP</title>
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
<style>
        .gradient-text,.gradient-text1{color:transparent;animation:5s infinite gradientAnimation}.gradient-text{background:linear-gradient(90deg,#ff8a00,#e52e71,#1e90ff);background-size:300% 300%;-webkit-background-clip:text;background-clip:text}.gradient-text1{background:linear-gradient(90deg,#e113aa,#1500ff,#1e90ff);background-size:300% 300%;-webkit-background-clip:text;background-clip:text}@keyframes gradientAnimation{0%,100%{background-position:0 50%}50%{background-position:100% 50%}}.card-height{height:600px}.card-height1{height:300px}.maparea{max-width:100%;border-radius:20px;padding:8px;background:linear-gradient(135deg,#e3f2fd,#fce4ec);border:3px solid transparent;background-clip:padding-box;position:relative;overflow:hidden;transition:transform .3s,box-shadow .3s;align-items:center;justify-content:center;display:flex;height:100%}.maparea:hover{box-shadow:0 12px 35px rgba(0,0,0,.25)}.custom-btn{width:130px;height:40px;color:#fff;border-radius:5px;padding:10px 25px;font-family:Lato,sans-serif;font-weight:500;background:0 0;cursor:pointer;transition:.3s;position:relative;display:inline-block;box-shadow:inset 2px 2px 2px 0 rgba(255,255,255,.5),7px 7px 20px 0 rgba(0,0,0,.1),4px 4px 5px 0 rgba(0,0,0,.1);outline:0}.btn-11{border:none;background:#212ffb;background:linear-gradient(0deg,#3e21fb 0,#4c5cea 100%);color:#fff;overflow:hidden}.btn-11:hover{text-decoration:none;color:#fff;opacity:.7}.btn-11:before{position:absolute;content:'';display:inline-block;top:-180px;left:0;width:30px;height:100%;background-color:#fff;animation:3s ease-in-out infinite shiny-btn1}.btn-11:active{box-shadow:4px 4px 6px 0 rgba(255,255,255,.3),-4px -4px 6px 0 rgba(116,125,136,.2),inset -4px -4px 6px 0 rgba(255,255,255,.2),inset 4px 4px 6px 0 rgba(0,0,0,.2)}@keyframes shiny-btn1{0%{transform:scale(0) rotate(45deg);opacity:0}80%{transform:scale(0) rotate(45deg);opacity:.5}81%{transform:scale(4) rotate(45deg);opacity:1}100%{transform:scale(50) rotate(45deg);opacity:0}}
      </style>
    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
        <br>
      <!-- Main content -->
      <section class="content">
        <div class="container-fluid">
        <div class="frame-layer-3" style="background: linear-gradient(to right, #b2ffbf, #ffffff); border-radius: 12px; box-shadow: 0 4px 8px rgba(0,0,0,0.1); padding: 20px;">
           <h3 class="m-0 premium-color-1 text-center p-4" style="color: #ff009b;font-family: sans-serif;">
              ✈️ Travel Cluster Details 🌍
          </h3>
          <center>
              <p style="width: 70%;">
                  📌 Travel cluster details represent various stages in the travel planning and approval process.  
                  📊 The total cluster count includes all travel clusters created or assigned.  
                  ✅ Approved clusters are those that have passed review and meet all necessary criteria.  
                  ⏳ Pending for approved clusters are still under evaluation, awaiting final decisions.  
                  ❌ Rejected clusters indicate submissions that did not meet the required standards or were deemed unnecessary.  
                  📈 Monitoring these metrics helps track workflow progress, identify bottlenecks, and maintain quality control in travel-related operations.
              </p>
          </center>

            <?php
                $target_uidData     = $this->Menu_model->get_userbyid($target_uid);
                $target_uidDataName = $target_uidData[0]->name;
              $heading = ucwords(str_replace('_', ' ', $mtypes));
            ?>
        <p style="color:navy; font-weight:bold;" class='text-center'><?=$target_uidDataName ?></p>
        <p style="color:navy; font-weight:bold;" class='text-center'><?=$heading ?></p>
        </div>
          
          <?php  
         
            $totalClusterData   = $clusterData['totalClusterData'];
            // dd($totalCluster);
          ?>
            <hr>
          <?php if ($this->session->flashdata('success_message')): ?>
            <div class="container-fluid">
              <div class="alert alert-success alert-dismissible fade show" role="alert">
                <strong> <?php echo $this->session->flashdata('success_message'); ?></strong>
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
                </button>
              </div>
            </div>
            <?php endif; ?>
             <?php if ($this->session->flashdata('error_message')): ?>
            <div class="container-fluid">
              <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <strong> <?php echo $this->session->flashdata('error_message'); ?></strong>
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
                </button>
              </div>
            </div>
            <?php endif; ?>


         <?php if($mtypes == 'total_unique_cluster'){ ?> 



          <?php if($user['type_id'] == 13){ ?> 
                  <hr>
                      <div class="row">
                        <div class="col-md-12">
                          <div class="col dflex text-white text-center">
                            <div style="background: linear-gradient(to right, rgb(228, 252, 233), #ffffff); border-radius: 12px; box-shadow: 0 4px 8px rgba(0,0,0,0.1); padding: 20px;">
                              <button type="button" id="add_createact" class="custom-btn btn-11" style="width: fit-content;" onclick="AddNewLocation('assigned_bd_in_cluster')">
                                <b>➕ Assign BD in Cluster</b> 🚀
                              </button>
                              <small style="display:block; margin-top: 5px; color:#333;">Add New BD to specific clusters</small>
                            </div>
                          </div>
                        </div>

                        <!-- <div class="col-md-6">
                          <div class="col dflex text-white text-center">
                            <div style="background: linear-gradient(to right, rgb(228, 252, 233), #ffffff); border-radius: 12px; box-shadow: 0 4px 8px rgba(0,0,0,0.1); padding: 20px;">
                              <button type="button" onclick="AddNewLocation('remove_bd_in_cluster')" id="add_createact" class="custom-btn btn-11" style="width: fit-content;">
                                <b>➖ Unassign BD in Cluster</b> ❌
                              </button>
                              <small style="display:block; margin-top: 5px; color:#333;">Remove BD from a cluster</small>
                            </div>
                          </div>
                        </div> -->


                      </div>
                    <hr>
<?php } ?>


          
          <div class="table-responsive">
            <div class="table-responsive text-nowrap">
              <table id="example1" class="table table-striped table-bordered" cellspacing="0" width="100%">
                <thead class="thead-dark" style="background: linear-gradient(to right, #b2ffbf, #ffffff); border-radius: 12px; box-shadow: 0 4px 8px rgba(0,0,0,0.1);">
                 <tr>
                    <th>🆔 ID</th>
                    <th>📌 Cluster ID</th>
                    <th>📍 Base / Out Station</th>
                    <th>🏷️ Cluster Name</th>
                    <th>🗾 Cluster State</th>
                    <th>🏢 Cluster District</th>
                    <th>🌆 Cluster City</th>
                    <th>💬 BD Remarks</th>
                    <th>📅 Created Date</th>
                    <th>♻️ Update Date</th>
                    <th>✅ Approved Status</th>
                    <th>📝 Approved BY</th>
                    <th>📆 Approved Date</th>
                    <th>💬 Approved Remarks</th>
                    <?php if(in_array($user['type_id'],[2,19,13,4,20,21,22,23,15])){ ?>
                        <th>⚙️ Action</th>
                    <?php } ?>
                    <th>✏️ Edit</th>
                </tr>
                </thead>
                <tbody>
                  <?php
                    $k=1;
                    foreach($totalClusterData as $c){
                      $inState = $this->Menu_model->getInStateById($c->in_state);
                      $inDistrict = $this->Menu_model->getInDistricById($c->in_district);
                      $distData = '';
                      foreach($inDistrict as $dis){
                        $distData .= $dis->district_title.' | ';
                      }
                      $distData = rtrim($distData, " | ");
                      $in_cityid = rtrim($c->in_city, ',');
                      $inCity = $this->Menu_model->getInCityById($in_cityid);
                      $cityData = '';
                      foreach($inCity as $cityd){
                        $cityData .= $cityd->name.' | ';
                      }
                      $cityData = rtrim($cityData, " | ");


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
                    <td><?= $k ?></td>
                    <td><?= $c->id;?></td>
                    <td><?php 
                    if(!is_null($c->travelType)){
                        echo $c->travelType;
                    }else{
                        echo "NA";
                    }
                    ?></td>
                    <td>
                      <a target="_BLANK" style="color:hsl(21, 60%, 39%)!important" href="<?=base_url()?>/Menu/TravelClusterDetailsByID/<?=$c->id;?>"> 
                        <?= $c->clustername  ?>
                      </a>
                    </td>
                    <td><?= $inState[0]->state_title ?></td>
                    <td><?= $distData;  ?></td>
                    <td><?= $cityData;  ?></td>
                    <td><?= $c->bd_reamrks;  ?></td>
                    <td><?= $c->created_at;?></td>
                    <td><?= $c->updated_at;?></td>
                    <td><?php 
                    if($c->apr_status == 0){
                        echo "<span class='p-1 bg-warning'> Pending </span>";
                    }else if($c->apr_status == 1){
                        echo "<span class='p-1 bg-success'> Approved </span>";
                    }else if($c->apr_status == 2){
                        echo "<span class='p-1 bg-danger'> Rejected </span>";
                    }
                    ?> </td>
                    <td><?php 
                    if($c->apr_status == 0){
                        echo "<span class='p-1 bg-warning'> Pending </span>";
                    }else if($c->apr_status == 1 || $c->apr_status == 2){
                        echo $c->apr_by_user;
                    }
                    ?> </td>
                    <td><?php
                    if($c->apr_status == 0){
                        echo "<span class='p-1 bg-warning'> Pending </span>";
                    }else if($c->apr_status == 1 || $c->apr_status == 2){
                        echo $c->apr_date;
                    }
                   ?> </td>
                    <td><?php
                    if($c->apr_status == 0){
                        echo "<span class='p-1 bg-warning'> Pending </span>";
                    }else if($c->apr_status == 1 || $c->apr_status == 2){
                        echo $c->apr_remarks;
                    }
                   ?> </td>

                  
                    <?php if(in_array($user['type_id'],[2,19,13,4,20,21,22,23,15])){ ?>
                     <td>
                        <?php if($c->apr_status == 0){ ?> 
                       <span class="p-1 bg-success btn" onclick="TakeRequestAction(<?= $c->id ?>,'Approved')" style="font-weight: 600;"> Approve </span> &nbsp;&nbsp;
                       <span class="p-1 bg-danger btn" onclick="TakeRequestAction(<?= $c->id ?>,'Reject')" style="font-weight: 600;"> Reject </span>
                    <?php  }else{

                            if($c->apr_status == 1){
                                echo "<span class='p-1 bg-success'> Approved </span>";
                            }else if($c->apr_status == 2){
                                echo "<span class='p-1 bg-danger'> Rejected </span>";
                            }
                    }  ?>
                    
                    </td>
                    <?php } ?>

                    <td>
                        <a class="p-1" style="background-color: navy; color: white; font-weight: 600;" href="<?=base_url()?>Menu/EditTravelCluster/<?=$c->id?>">Edit Travel Cluster</a>
                    </td>

                  </tr>
                  <?php $k++; } ?>
                </tbody>
              </table>
            </div>
          </div>
          <?php }else { ?> 
                  <div class="table-responsive">
            <div class="table-responsive text-nowrap">
              <table id="example1" class="table table-striped table-bordered" cellspacing="0" width="100%">
                <thead class="thead-dark" style="background: linear-gradient(to right, #b2ffbf, #ffffff); border-radius: 12px; box-shadow: 0 4px 8px rgba(0,0,0,0.1);">
                 <tr>
                    <th>🆔 ID</th>
                    <th>👤 Username</th>
                    <th>📍 Base / Out Station</th>
                    <th>🗺️ Base / Out Station (BD Mapped)</th>
                    <th>🏷️ Cluster Name</th>
                    <th>🗾 Cluster State</th>
                    <th>🏢 Cluster District</th>
                    <th>🌆 Cluster City</th>
                    <th>💬 BD Remarks</th>
                    <th>📅 Created Date</th>
                    <th>♻️ Update Date</th>
                    <th>✅ Approved Status</th>
                    <th>📝 Approved BY</th>
                    <th>📆 Approved Date</th>
                    <th>💬 Approved Remarks</th>
                    <?php if(in_array($user['type_id'],[2,19,13,4,20,21,22,23,15])){ ?>
                        <th>⚙️ Action</th>
                    <?php } ?>
                </tr>
                </thead>
                <tbody>
                  <?php
                    $k=1;
                    foreach($totalClusterData as $c){
                      $inState = $this->Menu_model->getInStateById($c->in_state);
                      $inDistrict = $this->Menu_model->getInDistricById($c->in_district);
                      $distData = '';
                      foreach($inDistrict as $dis){
                        $distData .= $dis->district_title.' | ';
                      }
                      $distData = rtrim($distData, " | ");
                      $in_cityid = rtrim($c->in_city, ',');
                      $inCity = $this->Menu_model->getInCityById($in_cityid);
                      $cityData = '';
                      foreach($inCity as $cityd){
                        $cityData .= $cityd->name.' | ';
                      }
                      $cityData = rtrim($cityData, " | ");


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
                    <td><?= $k ?></td>

                    <td>
                    <a target="_BLANK" style="color:hsl(21, 60%, 39%)!important" href="<?=base_url()?>/Menu/TravelClusterDetailsByID/<?=$c->id;?>"> 
                        <?= $c->username  ?>
                      </a>
                    </td>
                    <td>
                      
                      
                      <?php 
                    if(!is_null($c->travelType)){
                        echo $c->travelType;
                    }else{
                        echo "NA";
                    }
                    ?></td>

                    <td><?php 
                    if(!is_null($c->cluster_mapping_travel_type)){
                        echo $c->cluster_mapping_travel_type;
                    }else{ ?>
                      <?php if(in_array($user['type_id'],[13,4,20,21,22,23,2])){ ?>
                    <span class="p-1 bg-primary" id="cm_<?=$c->cluster_mapping_id?>" onclick="MapBaseOrOutLocation(<?=$c->cluster_mapping_id?>)">Map Base/Out</span>
                    <?php } ?>
                   <?php }
                    ?></td>
                   
                  
                     <td>
                    <a target="_BLANK" style="color:hsl(21, 60%, 39%)!important" href="<?=base_url()?>/Menu/TravelClusterDetailsByID/<?=$c->id;?>"> 
                        <?= $c->clustername  ?>
                      </a>
                    </td>
                    <td><?= $inState[0]->state_title ?></td>
                    <td><?= $distData;  ?></td>
                    <td><?= $cityData;  ?></td>
                    <td><?= $c->bd_reamrks;  ?></td>
                    <td><?= $c->created_at;?></td>
                    <td><?= $c->updated_at;?></td>
                    <td><?php 
                    if($c->apr_status == 0){
                        echo "<span class='p-1 bg-warning'> Pending </span>";
                    }else if($c->apr_status == 1){
                        echo "<span class='p-1 bg-success'> Approved </span>";
                    }else if($c->apr_status == 2){
                        echo "<span class='p-1 bg-danger'> Rejected </span>";
                    }
                    ?> </td>
                    <td><?php 
                    if($c->apr_status == 0){
                        echo "<span class='p-1 bg-warning'> Pending </span>";
                    }else if($c->apr_status == 1 || $c->apr_status == 2){
                        echo $c->apr_by_user;
                    }
                    ?> </td>
                    <td><?php
                    if($c->apr_status == 0){
                        echo "<span class='p-1 bg-warning'> Pending </span>";
                    }else if($c->apr_status == 1 || $c->apr_status == 2){
                        echo $c->apr_date;
                    }
                   ?> </td>
                    <td><?php
                    if($c->apr_status == 0){
                        echo "<span class='p-1 bg-warning'> Pending </span>";
                    }else if($c->apr_status == 1 || $c->apr_status == 2){
                        echo $c->apr_remarks;
                    }
                   ?> </td>

                  
                    <?php if(in_array($user['type_id'],[2,19,13,4,20,21,22,23,15])){ ?>
                     <td>
                        <?php if($c->apr_status == 0){ ?> 
                       <span class="p-1 bg-success btn" onclick="TakeRequestAction(<?= $c->id ?>,'Approved')" style="font-weight: 600;"> Approve </span> &nbsp;&nbsp;
                       <span class="p-1 bg-danger btn" onclick="TakeRequestAction(<?= $c->id ?>,'Reject')" style="font-weight: 600;"> Reject </span>
                    <?php  }else{

                            if($c->apr_status == 1){
                                echo "<span class='p-1 bg-success'> Approved </span>";
                            }else if($c->apr_status == 2){
                                echo "<span class='p-1 bg-danger'> Rejected </span>";
                            }
                    }  ?>
                    
                    </td>
                    
                    <?php } ?>

                  </tr>
                  <?php $k++; } ?>
                </tbody>
              </table>
            </div>
          </div>
            <?php } ?>

      



        </div>
        <hr>
        <br>

          <div class="modal fade" id="exampleModalCenterdata" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered" role="document">
                                <div class="modal-content">
                                  <div class="modal-header">
                                    <h5 class="modal-title" id="exampleModalLongTitle">Add Remark</h5>
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                      <span aria-hidden="true">&times;</span>
                                    </button>
                                  </div>
                                  <div class="modal-body">
                                   <form action="<?=base_url();?>Menu/ActionByLineManagerOnTravelCluster" method="post" >
                                        <input type="hidden" id="userwise" value="<?=$userwise?>" name="userwise">
                                        <input type="hidden" id="target_uid" value="<?=$target_uid?>" name="target_uid">
                                        <input type="hidden" id="mtypes" value="<?=$mtypes?>" name="mtypes">
                                        <input type="hidden" id="request_id" value="" name="request_id">
                                        <input type="hidden" id="request_type" value="" name="request_type">
                                         <div class="mb-3 mt-3">
                                          <textarea id="request_remarks" name="request_remarks" cols="30" placeholder="Add Remark " class="form-control"  rows="4" required></textarea>
                                        </div>
                                        <div class="form-group text-center">
                                            <button type="submit" id="requestSubmit" class="btn btn-success mt-2">Submit</button>
                                        </div>
                                   </form>
                                  </div>
                                </div>
                              </div>
                            </div>




                                <div class="modal fade" id="exampleModalCenterdataClusterMap" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered" role="document">
                                <div class="modal-content">
                                  <div class="modal-header">
                                    <h5 class="modal-title">Select Base / Out Cluster</h5>
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                      <span aria-hidden="true">&times;</span>
                                    </button>
                                  </div>
                                  <div class="modal-body">
                                   <form action="<?=base_url();?>Menu/ClusterUserMappingProcess" method="post" style="padding:20px;">

                                       <div class="form-group">
                                         <input type="hidden" id="cluster_mapping_id" value="" name="cluster_mapping_id">
                                         <input type="hidden" id="userwise" value="<?=$userwise?>" name="userwise">
                                         <input type="hidden" id="target_uid" value="<?=$target_uid?>" name="target_uid">
                                         <input type="hidden" id="mtypes" value="<?=$mtypes?>" name="mtypes"></div>
                                            <label for="task_type">Type of Travel</label><br>
                                            <select class="form-control" name="typeofTravel" id="typeofTravel" required>
                                              <option value="" Selected disabled>Select Location</option>
                                              <option value="base">Base Location</option>
                                              <option value="outStation">Out Station</option>
                                            </select>
                                          </div>
                                        <div class="form-group text-center">
                                            <button type="submit" id="requestSubmit" class="btn btn-success mt-2">Submit</button>
                                        </div>
                                   </form>
                                  </div>
                                </div>
                              </div>
                            </div>



 <?php if($user['type_id'] == 13){
  

  // dd($ourclusterData);
  
  ?> 
    <div id="assigned_bd_in_cluster_card" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="modal-standard-title" aria-hidden="true">
      <div class="modal-dialog" role="document">
        <div class="modal-content" style="background: linear-gradient(to right,rgb(231, 244, 246), #ffffff); border-radius: 12px; box-shadow: 0 4px 8px rgba(0,0,0,0.1); padding: 20px;">
          <div class="modal-header">
            <h5 class="modal-title" id="modal-standard-title1"></h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <!-- // END .modal-header -->
          <div class="modal-body">
            <div class="card-form col-md-12">
              <div class="row no-gutters">
                <div class="col-lg-12" style="background: linear-gradient(to right, #ede7f6, #ffffff); border-radius: 12px; box-shadow: 0 4px 8px rgba(0,0,0,0.1); padding: 20px;">
                  <center>
                    <h5>Assigned BD In Cluster</h5>
                  </center>
                  <hr>
                  <?=form_open('Menu/AssignBDInCluster')?>

                  <input type="hidden" id="userwise" value="<?=$userwise?>" name="userwise">
                  <input type="hidden" id="target_uid" value="<?=$target_uid?>" name="target_uid">
                  <input type="hidden" id="mtypes" value="<?=$mtypes?>" name="mtypes"></div>
                  <br>
                  <hr>
                        <label>Type of Travel</label><br>
                        <select class="form-control" name="typeofTravel" id="typeofTravel" required>
                          <option value="" Selected disabled>Select Location</option>
                          <option value="base">Base Location</option>
                          <option value="outStation">Out Station</option>
                        </select>
                  <hr>

                  <label>Select Travel Cluster</label><br>
                    <select class="form-control" name="assign_travel_cluster" id="assign_travel_cluster" required>
                      <option value="" Selected disabled>Select Travel Cluster</option>

                      <?php /* foreach($ourclusterData as $ourclusterDat): ?>
                      <option value="<?=$ourclusterDat->id;?>"><?=$ourclusterDat->clustername;?> - <?=$ourclusterDat->travelType;?> - ( Created By - <?=$ourclusterDat->apr_by_user;?> )</option>
                      <?php endforeach; */ ?>

                      <?php foreach($totalClusterData as $ourclusterDat): ?>
                      <option value="<?=$ourclusterDat->id;?>"><?=$ourclusterDat->clustername;?> - <?=$ourclusterDat->travelType;?> - ( Created By - <?=$ourclusterDat->apr_by_user;?> )</option>
                      <?php endforeach; ?>


                  </select>
                 <hr>
                  <lable style="font-weight: 600;">New BD Name : </lable>
                 <select class="form-control" name="assign_bd_in_cluster" id="assign_bd_in_cluster" required>
                      <option value="" Selected disabled>Select BD</option>
                     <?php foreach($ourteamsDatas as $ourteamsData): ?>
                      <option value="<?=$ourteamsData->user_id;?>"><?=$ourteamsData->name;?></option>
                      <?php endforeach; ?>
                  </select>
                  <hr>
                  <center>
                    <span id="assigntravelcluster_message"></span>
                  </center>
                  <center>
                    <button 
                        type="submit" style="width: fit-content;"
                        id="assigntravelclusterbtn" 
                        class="custom-btn btn-11 mt-3" 
                        onclick="return confirm('Are you sure you want to assign BD in Travel Cluster?');">
                        + Assign BD in Travel Cluster
                    </button>

                  </center>
                  </form>
                </div>
              </div>
            </div>
            <br>
          </div>
        </div>
      </div>
    </div>



    <div id="remove_bd_in_cluster_card" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="modal-standard-title" aria-hidden="true">
      <div class="modal-dialog" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="modal-standard-title1"></h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <!-- // END .modal-header -->
          <div class="modal-body">
            <div class="card-form col-md-12">
              <div class="row no-gutters">
                <div class="col-lg-12" style="background: linear-gradient(to right, #ede7f6, #ffffff); border-radius: 12px; box-shadow: 0 4px 8px rgba(0,0,0,0.1); padding: 20px;">
                  <center>
                    <h5>Remove BD from a cluster</h5>
                  </center>
                  <hr>
                  <?=form_open('Menu/UnassignBDInCluster')?>

                  <lable style="font-weight: 600;">Select State</lable>
                    <select id="state_id" name="in_state_id" class="form-control" required>
                        <option value="">Select State</option>
                        <?php $allstate = $this->Menu_model->GetState();
                        foreach($allstate as $state){?>
                            <option value="<?=$state->state_id?>"><?=$state->state_title?></option>
                        <?php } ?>
                    </select>

                  <lable style="font-weight: 600;">New District Name : </lable>
                  <input type="text" id="new_district_name" class="form-control" placeholder="Enter District Name" name="new_district_name" required>
                  <center>
                    <button type="submit" class="custom-btn btn-11 mt-3">+ Add District</button>
                  </center>
                  </form>
                </div>
              </div>
            </div>
             <br>
          </div>
        </div>
      </div>
    </div>


 <?php } ?> 

      </section>
      <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>
      <script type='text/javascript'>
          function TakeRequestAction(id,val){
                $('#exampleModalCenterdata').modal('show');
                $('#request_id').val(id);
                $('#request_type').val(val);
                if(val == 'Approved'){
                    $("#requestSubmit").text("Approved").css("background-color","green");
                }
                if(val == 'Reject'){
                    $("#requestSubmit").text("Reject").css("background-color","red");;
                }
            }

            function MapBaseOrOutLocation(clusterMappingId) {
              $('#cluster_mapping_id').val(clusterMappingId);
              $('#exampleModalCenterdataClusterMap').modal('show');
            }

             function AddNewLocation(val) {
                if(val == 'assigned_bd_in_cluster'){
                    $('#assigned_bd_in_cluster_card').modal('show');
                }else if(val == 'remove_bd_in_cluster'){
                    $('#remove_bd_in_cluster_card').modal('show');
                }
                }


        
              
              // var assign_travel_cluster_val =  $('#assign_travel_cluster').val();
$(document).ready(function() {

  $("#assigntravelclusterbtn").hide();


    $('#assign_bd_in_cluster').on('change', function() {

      var assign_travel_cluster_val =  $('#assign_travel_cluster').val();
      if (!assign_travel_cluster_val) {
            alert("Please select a valid Travel Cluster.");
            $(this).val("");
            return;
        }
   
        var clusterBD_Id = $(this).val(); 
        $.ajax({
            url: '<?=base_url()?>/Menu/CheckClusterInClusterMapping',
            type: 'POST',
            data: { clusterBD_Id: clusterBD_Id,assign_travel_cluster_val:assign_travel_cluster_val },
            success: function(response) {
                if(response == 0){
                  $("#assigntravelcluster_message").text("");
                  $("#assigntravelclusterbtn").show();
                }else if(response == 1){
                  $("#assigntravelcluster_message").text("* Allready BD Assign in this Cluster").css("color", "red");
                  $("#assigntravelclusterbtn").hide();
                }
            }
        });
    });
});
      </script>
      <!-- /.row (main row) -->
    </div>
    <!-- /.container-fluid -->
  </section>
  <!-- /.content -->
</div>
<!-- /.content-wrapper -->
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
      "buttons": ["csv", "excel", "pdf"]
    }).buttons().container().appendTo('#example1_wrapper .col-md-6:eq(0)');
</script>
</body>
</html>
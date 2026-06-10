<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>STEM APP | WebAPP</title>
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
      tr,td{
      font-weight: bold;
      }
      .card-header{
      background: floralwhite;
      }
    </style>
  </head>
  <body class="hold-transition sidebar-mini layout-fixed">
    <div class="wrapper">
      <!-- Preloader -->
      <!-- Navbar -->
      <?php $this->load->view($dep_name.'/nav.php');?>
      <?php 
        $cmpData                   = $cmpDatas[0];
        $ccid                      = $cmpData->cid;
        $compname                  = $cmpData->compname;
        $company_address           = $cmpData->company_address;
        
        $company_draft             = $cmpData->company_draft;
        $company_budget            = $cmpData->company_budget;
        $company_website           = $cmpData->company_website;
        $company_country           = $cmpData->company_country;
        $company_state             = $cmpData->company_state;
        $company_district          = $cmpData->company_district;
        $company_city              = $cmpData->company_city;

        $created_date              = $cmpData->created_date;
        $current_status            = $cmpData->current_status;
        $last_status               = $cmpData->last_status;
        
        $mainbd_name               = $cmpData->mainbd_name;
        $cluster_manager_name      = $cmpData->cluster_manager_name;
        $pst_name                  = $cmpData->pst_name;
        $ash_nae_co_id_name        = $cmpData->ash_nae_co_id_name;
        $ash_w_co_id_name          = $cmpData->ash_w_co_id_name;
        $ash_s_co_id_name          = $cmpData->ash_s_co_id_name;
        $rm_east_co_id_name        = $cmpData->rm_east_co_id_name;
        $rm_north_co_id_name       = $cmpData->rm_north_co_id_name;
        $acm_co_id_name            = $cmpData->acm_co_id_name;
        $inside_sales_name         = $cmpData->inside_sales_name;
        
        $topspender                = $cmpData->topspender;
        $upsell_client             = $cmpData->upsell_client;
        $focus_funnel              = $cmpData->focus_funnel;
        $anchor_clients            = $cmpData->anchor_clients;
        $in_quarter                = $cmpData->in_quarter;
        $keycompany                = $cmpData->keycompany;
        $pkclient                  = $cmpData->pkclient;
        $priorityc                 = $cmpData->priorityc;
        $potential                 = $cmpData->potential;
        $current_status_id         = $cmpData->current_status_id;
        $init_call_id               = $cmpData->init_call_id;
        
        $q1_twetenty_closure_funnel = $cmpData->q1_twetenty_closure_funnel;
        $potential_funnel_for_fy   = $cmpData->potential_funnel_for_fy;
        $to_be_nurtured_for_fy     = $cmpData->to_be_nurtured_for_fy;
        $fifity_new_lead_funnel    = $cmpData->fifity_new_lead_funnel;
        
        $create_after_task         = $cmpData->create_after_task;
        
        $last_updated_at           = $cmpData->last_updated_at;
        $company_is_admin_approved = $cmpData->company_is_admin_approved;
        $company_apr_date          = $cmpData->company_apr_date;
        $company_created_at        = $cmpData->init_created_at;
        
        $partner_name              = $cmpData->partner_name;
        
        $cluster_id                = $cmpData->cluster_id;
        $clustername               = $cmpData->clustername;
        $travelType                = $cmpData->travelType;
        $travel_cluster_create_name = $cmpData->travel_cluster_create_name;
        
        $company_creater_name      = $cmpData->company_creater_name;
        $company_apr_by            = $cmpData->company_apr_by;
        $user_cluster_zone         = $cmpData->user_cluster_zone;

        $need_to_be_monitored      = $cmpData->need_to_be_monitored;

        
        
        $task_counts = [];
        $taskTtimeStatusDatas = [];
        
        foreach ($cmpTasksDatas as $task) {
            $task_name = $task->task_name;
            $tasktimestatus = $task->task_time_status;
        
            if (!isset($task_counts[$task_name])) {
                $task_counts[$task_name] = 1;
            } else {
                $task_counts[$task_name]++;
            }

            if (!isset($taskTtimeStatusDatas[$tasktimestatus])) {
                $taskTtimeStatusDatas[$tasktimestatus] = 1;
            } else {
                $taskTtimeStatusDatas[$tasktimestatus]++;
            }
        }
        

        $oldMainBDs = $this->Menu_model->GetOldMainBD($cid);
        
        if(sizeof($oldMainBDs) > 0){
          $old_user_name =  $oldMainBDs[0]->old_user;
        }else{
          $old_user_name = '';
        }


       

        // dd($cmpReviewsDatas);
        ?>
      <?php 
        if($user['type_id'] == 2 || $user['type_id'] == 1):
         include 'addlog.php';

        //  dd($cmpTasksDatas);

        endif;
        ?>
      <style>
       .gradient-text,.gradient-text1{color:transparent;animation:5s infinite gradientAnimation}.gradient-text{background:linear-gradient(90deg,#ff8a00,#e52e71,#1e90ff);background-size:300% 300%;-webkit-background-clip:text;background-clip:text}.gradient-text1{background:linear-gradient(90deg,#e113aa,#1500ff,#1e90ff);background-size:300% 300%;-webkit-background-clip:text;background-clip:text}@keyframes gradientAnimation{0%,100%{background-position:0 50%}50%{background-position:100% 50%}}.card-height{min-height:600px}.card-height1{height:300px}.maparea{max-width:100%;border-radius:20px;padding:8px;background:linear-gradient(135deg,#e3f2fd,#fce4ec);border:3px solid transparent;background-clip:padding-box;position:relative;overflow:hidden;transition:transform .3s,box-shadow .3s;align-items:center;justify-content:center;display:flex;height:100%}.maparea:hover{box-shadow:0 12px 35px rgba(0,0,0,.25)}.custom-btn{width:130px;height:40px;color:#fff;border-radius:5px;padding:10px 25px;font-family:Lato,sans-serif;font-weight:500;background:0 0;cursor:pointer;transition:.3s;position:relative;display:inline-block;box-shadow:inset 2px 2px 2px 0 rgba(255,255,255,.5),7px 7px 20px 0 rgba(0,0,0,.1),4px 4px 5px 0 rgba(0,0,0,.1);outline:0}.btn-11{border:none;background:#212ffb;background:linear-gradient(0deg,#3e21fb 0,#4c5cea 100%);color:#fff;overflow:hidden}.btn-11:hover{text-decoration:none;color:#fff;opacity:.7}.btn-11:before{position:absolute;content:'';display:inline-block;top:-180px;left:0;width:30px;height:100%;background-color:#fff;animation:3s ease-in-out infinite shiny-btn1}.btn-11:active{box-shadow:4px 4px 6px 0 rgba(255,255,255,.3),-4px -4px 6px 0 rgba(116,125,136,.2),inset -4px -4px 6px 0 rgba(255,255,255,.2),inset 4px 4px 6px 0 rgba(0,0,0,.2)}@keyframes shiny-btn1{0%{transform:scale(0) rotate(45deg);opacity:0}80%{transform:scale(0) rotate(45deg);opacity:.5}81%{transform:scale(4) rotate(45deg);opacity:1}100%{transform:scale(50) rotate(45deg);opacity:0}}
        h4.mb-0 {
            color: #ff299d;
        }


        .live-badge{display:inline-flex;align-items:center;background-color:red;color:#fff;font-weight:700;padding:4px 10px;border-radius:20px;font-size:14px;font-family:Arial,sans-serif;animation:1s infinite pulse;box-shadow:0 0 5px red}.live-dot{width:8px;height:8px;background-color:#fff;border-radius:50%;margin-right:6px;animation:1s infinite blink}@keyframes blink{0%,100%,50%{opacity:1}25%,75%{opacity:0}}@keyframes pulse{0%,100%{box-shadow:0 0 5px red}50%{box-shadow:0 0 20px red}}
   
       
      </style>
      <!-- /.navbar -->
      <!-- Content Wrapper. Contains page content -->
      <div class="content-wrapper">
        <section class="content">
          <div class="container-fluid">
          
           <?php if ($this->session->flashdata('pending_message')): ?>
              <div class="alert alert-success alert-dismissible">
            <button type="button" class="close" data-dismiss="alert">&times;</button>
            <?= $this->session->flashdata('pending_message'); ?>
          </div>
          <?php endif; ?>
          <?php if ($this->session->flashdata('success_message')): ?>
              <div class="alert alert-success alert-dismissible">
            <button type="button" class="close" data-dismiss="alert">&times;</button>
            <?= $this->session->flashdata('success_message'); ?>
          </div>
          <?php endif; ?>

          <?php if ($this->session->flashdata('error_message')): ?>
          <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <strong> <?php echo $this->session->flashdata('error_message'); ?></strong>
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <?php endif; ?>
          <?php if ($this->session->flashdata('errors_message')): ?>
          <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <strong> <?php echo $this->session->flashdata('errors_message'); ?></strong>
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <?php endif; ?>


            <br>
            <div class="row">
              <div class="col-12">
                <div class="card-data">
                  <div class="card-header" style="background: linear-gradient(to right, #d0ffd9, #ffffff);border-radius: 12px;box-shadow: 0 4px 8px rgba(0,0,0,0.1);padding: 20px;">
                    <h3 class="text-center m-3">🏢 <span class="gradient-text"><?=$compname;?> (<?=$cid?>)</span> </h3>
                    <h4 class="text-center m-3">📍 <span class="gradient-text"> <?=$company_address;?></span> </h4>
                    <h5 class="text-center m-3">🌟✨ <span class="gradient-text"><?=$current_status;?></span> ✨🌟</h5>
                    <?php if(!is_null($need_to_be_monitored) OR !empty($need_to_be_monitored)){ ?>
                       <h5 class="text-center m-3"><span class="live-badge text-center m-3"><span class="live-dot"></span> <?= $need_to_be_monitored; ?> </span></h5>
                    <?php }?>
                    
                  </div>

                  


                  <!-- /.card-header -->
                  <div class="card-body">

                  
                    <?php  if($user['type_id'] == 2 || $user['type_id'] == 1): ?>
                    <hr>
                    <div class="col dflex text-white text-center">
                      <button type="button" id="add_createact" class="custom-btn btn-11" style="width: fit-content;"><b>+</b> Create Task</button>
                    </div>
                    <hr>

                      <?php  
                      if($user['type_id'] == 1):   
                        include 'MasterControlSetting.php'; 
                      endif;
                      ?>

                    <?php  endif;?>


                    <?php if($ctype_id !== 3){ ?> 
                    <hr>
                    <div class="row" style="background: azure; padding: 25px; border-radius: 20px;">
                      <div class="col-md-4">
                        <div class="card-height" style="background: linear-gradient(to right, #dfe9f3, #ffffff); border-radius: 12px; box-shadow: 0 4px 8px rgba(0,0,0,0.1); padding: 20px;">
                          <div class="text-center">
                            <h4 class="mb-0">🏢 Company Overview</h4>
                          </div>
                          <hr>
                          
                          <p><strong>👤 Partner Name:</strong> <?= !empty($partner_name) ? $partner_name : 'N/A' ?></p>
                          <p><strong>📄 Draft Status:</strong> <?= $company_draft ?: 'N/A' ?></p>
                          <p><strong>💰 Budget:</strong> <?= $company_budget ?></p>
                          <p><strong>🌐 Website:</strong> <a href="https://<?= $company_website ?>" target="_blank"><?= $company_website ?></a></p>
                          <p><strong>🌍 Country:</strong> <?= $company_country ?></p>
                          <p><strong>🏙️ State:</strong> <?= $company_state ?></p>
                          <p><strong>🏙️ District:</strong> <?= $company_district ?></p>
                          <p><strong>🌆 City:</strong> <?= $company_city ?></p>
                          <p><strong>📍 Address:</strong> <?= $company_address ?></p>
                          <p><strong>🗓️ Created On:</strong> <?= date("d M Y", strtotime($created_date)) ?></p>
                          <p><strong>🗓️ Created By:</strong> <?= $company_creater_name ?: 'N/A' ?></p>
                          <p><strong>📌 Current Status:</strong> <?= $current_status ?></p>
                          <p><strong>📍 Last Status:</strong> <?= $last_status ?></p>
                          <p><strong>✅ Marked for Monitoring:</strong> 
                        <?php 
                          if(is_null($need_to_be_monitored) OR empty($need_to_be_monitored)){
                              echo "<span class=''> NA </span>";
                          }else{ ?>
                              <?= $need_to_be_monitored; ?>
                          <?php }
                        ?>
                        
                        </p>
                          <p><strong>🕓 Created At: </strong> 
                            <?= !empty($company_created_at) ? date("d M Y, h:i A", strtotime($company_created_at)) : 'N/A' ?>
                          </p>
                          <p><strong>🕓 Last Updated At:</strong> 
                            <?= !empty($last_updated_at) ? date("d M Y, h:i A", strtotime($last_updated_at)) : 'N/A' ?>
                          </p>
                         
                          <hr>
                        </div>
                      </div>
                      <div class="col-md-4">
                        <div class="card-height" style="background: linear-gradient(to right, #e0f7fa, #ffffff); border-radius: 12px; box-shadow: 0 4px 8px rgba(0,0,0,0.1); padding: 20px;">
                          <div class="text-center">
                            <h4 class="mb-0">👥 Company Team Members</h4>
                          </div>
                          <hr>
                          <p><strong>🧑‍💼 Main BD:</strong> <?= !empty($mainbd_name) ? $mainbd_name : 'N/A' ?></p>
                          <p><strong>👨‍🔧 Cluster Manager:</strong> <?= !empty($cluster_manager_name) ? $cluster_manager_name : 'N/A' ?></p>
                          <p><strong>🎯 PST:</strong> <?= !empty($pst_name) ? $pst_name : 'N/A' ?></p>
                          <hr>
                          <p><strong>🧭 Assistant Sales Head (NAE):</strong> <?= !empty($ash_nae_co_id_name) ? $ash_nae_co_id_name : 'N/A' ?></p>
                          <p><strong>🌐 Assistant Sales Head (W):</strong> <?= !empty($ash_w_co_id_name) ? $ash_w_co_id_name : 'N/A' ?></p>
                          <p><strong>🏞️ Assistant Sales Head (S):</strong> <?= !empty($ash_s_co_id_name) ? $ash_s_co_id_name : 'N/A' ?></p>
                          <hr>
                          <p><strong>🏯 RM East:</strong> <?= !empty($rm_east_co_id_name) ? $rm_east_co_id_name : 'N/A' ?></p>
                          <p><strong>🏙️ RM North:</strong> <?= !empty($rm_north_co_id_name) ? $rm_north_co_id_name : 'N/A' ?></p>
                          <p><strong>📋 Assistant Cluster Manager:</strong> <?= !empty($acm_co_id_name) ? $acm_co_id_name : 'N/A' ?></p>
                          <p><strong>📋 inside sales name:</strong> <?= !empty($inside_sales_name) ? $inside_sales_name : 'N/A' ?></p>
                          <p><strong>🧑‍💼 Old BD :</strong> <?= $old_user_name; ?></p>
                          <hr>
                          <p><strong>📍 Main BD Cluster Zone :</strong> <?= !empty($user_cluster_zone) ? $user_cluster_zone : 'N/A' ?></p>
                          <br>
                        </div>
                      </div>
                      <div class="col-md-4">
                        <div class="card-height" style="background: linear-gradient(to right, #fce4ec, #ffffff); border-radius: 12px; box-shadow: 0 4px 8px rgba(0,0,0,0.1); padding: 20px;">
                          <div class="text-center">
                            <h4 class="mb-0">📊 Company Classification Details</h4>
                          </div>
                          <hr>
                          <p><strong>💎 Top Spender:</strong> <?= !empty($topspender) ? $topspender : 'N/A' ?></p>
                          <p><strong>📈 Upsell Client:</strong> <?= !empty($upsell_client) ? $upsell_client : 'N/A' ?></p>
                          <p><strong>🔍 Focus Funnel:</strong> <?= !empty($focus_funnel) ? $focus_funnel : 'N/A' ?></p>
                          <p><strong>⚓ Anchor Clients:</strong> <?= !empty($anchor_clients) ? $anchor_clients : 'N/A' ?></p>
                          <hr>
                          <p><strong>🏢 Key Company:</strong> <?= !empty($keycompany) ? $keycompany : 'N/A' ?></p>
                          <p><strong>🎯 Positive Key Client:</strong> <?= !empty($pkclient) ? $pkclient : 'N/A' ?></p>
                          <p><strong>🔰 Priority Calling :</strong> <?= !empty($priorityc) ? $priorityc : 'N/A' ?></p>
                          <p><strong>🚀 Potential Client:</strong> <?= !empty($potential) ? $potential : 'N/A' ?></p>
                          <hr>
                        </div>
                      </div>
                      <div class="col mt-2">
                        <div class="card-height1" style="background: linear-gradient(to right, #feffa8, #ffffff); border-radius: 12px; box-shadow: 0 4px 8px rgba(0,0,0,0.1); padding: 20px;">
                          <div class="text-center">
                            <h4 class="mb-0">🔄 Funnel Insights</h4>
                          </div>
                          <hr>
                          <p><strong>📌 Q1 – 20 Closure Funnel:</strong>
                            <?= ($q1_twetenty_closure_funnel === 'NULL' || $q1_twetenty_closure_funnel === '' || $q1_twetenty_closure_funnel === 0 || $q1_twetenty_closure_funnel === '0') ? 'N/A' : $q1_twetenty_closure_funnel ?>
                          </p>
                          <p><strong>🚀 Potential Funnel for FY:</strong>
                            <?= ($potential_funnel_for_fy === 'NULL' || $potential_funnel_for_fy === '' || $potential_funnel_for_fy === 0 || $potential_funnel_for_fy === '0') ? 'N/A' : $potential_funnel_for_fy ?>
                          </p>
                          <p><strong>🌱 To Be Nurtured for FY:</strong>
                            <?= ($to_be_nurtured_for_fy === 'NULL' || $to_be_nurtured_for_fy === '' || $to_be_nurtured_for_fy === 0 || $to_be_nurtured_for_fy === '0') ? 'N/A' : $to_be_nurtured_for_fy ?>
                          </p>
                          <p><strong>🆕 Fifty New Lead Funnel:</strong>
                            <?= ($fifity_new_lead_funnel === 'NULL' || $fifity_new_lead_funnel === '' || $fifity_new_lead_funnel === 0 || $fifity_new_lead_funnel === '0') ? 'N/A' : $fifity_new_lead_funnel ?>
                          </p>
                          <p><strong>📆 In Quarter:</strong> <?= !empty($in_quarter) ? $in_quarter : 'N/A' ?></p>
                          <hr>
                        </div>
                      </div>
                      <?php /*
                        <div class="col-md-4">
                            <div style="background: linear-gradient(to right, #ecfdaa, #ffffff); border-radius: 12px; box-shadow: 0 4px 8px rgba(0,0,0,0.1); padding: 20px;">
                                <div class="text-center">
                                    <h4 class="mb-0">✅ Update Status</h4>
                                </div>
                                <hr>
                                        <p><strong>🕓 Last Updated At:</strong> 
                                        <?= !empty($last_updated_at) ? date("d M Y, h:i A", strtotime($last_updated_at)) : 'N/A' ?>
                      </p>
                      <p><strong>🔐 Admin Approved:</strong> 
                        <?= isset($company_is_admin_approved) ? ($company_is_admin_approved ? 'Yes' : 'No') : 'N/A' ?>
                      </p>
                      <p><strong>📅 Approval Date:</strong> 
                        <?= !empty($company_apr_date) ? date("d M Y", strtotime($company_apr_date)) : 'N/A' ?>
                      </p>
                      <hr>
                    </div>
                  </div>
                  */ ?>
                  <div class="col mt-2">
                    <div class="card-height1"  class="card-height" style="background: linear-gradient(to right, #ede7f6, #ffffff); border-radius: 12px; box-shadow: 0 4px 8px rgba(0,0,0,0.1); padding: 20px;">
                      <div class="text-center">
                        <h4 class="mb-0">🌍 Cluster & Travel Details</h4>
                      </div>
                      <hr>
                      <p><strong>🆔 Cluster ID:</strong> 
                        <?php if(!empty($cluster_id)){ ?> 
                        <a target="_BLANK" style="color:hsl(174, 95%, 32%)!important" href="<?=base_url()?>Menu/TravelClusterDetailsByID/<?=$cluster_id?>">
                        <?=$cluster_id;?>
                        </a>
                        <?php }else{
                          echo "N/A";
                          } ?>
                      </p>
                      <p><strong>📌 Cluster Name:</strong> 
                        <?php if(!empty($clustername)){ ?> 
                        <a target="_BLANK" style="color:hsl(174, 95%, 32%)!important" href="<?=base_url()?>Menu/TravelClusterDetailsByID/<?=$cluster_id?>">
                        <?=$clustername;?>
                        </a>
                        <?php }else{
                          echo "N/A";
                          } ?>
                      </p>
                      <p><strong>🚗 Travel Type:</strong> <?= !empty($travelType) ? $travelType : 'N/A' ?></p>
                      <p><strong>👤 Travel Cluster Created By:</strong> <?= !empty($travel_cluster_create_name) ? $travel_cluster_create_name : 'N/A' ?></p>
                      <hr>
                    </div>
                  </div>
                </div>
                <hr>
                <div class="text-center" style="background: linear-gradient(to right, #ede7f6, #ffffff); border-radius: 12px; box-shadow: 0 4px 8px rgba(0,0,0,0.1); padding: 20px;">
                  <h5 class="mb-0">📊 Contact Information</h5>
                  <center>
                    <small class="d-block" style="width: 70%;">
                    This section provides key contact details of individuals associated with the project or organization. It includes names, roles, phone numbers, and email addresses—ensuring quick communication, effective coordination, and seamless follow-ups. Use this reference to stay connected and maintain strong stakeholder engagement.
                    </small>
                  </center>
                </div>
                <hr>
                <div class="row">
                  <div class="col-md-8 mt-2">
                    <?php
                      // Combine and encode the location
                      $location = urlencode($compname . ' ' . $company_address);
                      // Generate Google Maps Embed URL
                      $map_url = "https://www.google.com/maps?q=$location&output=embed";
                      ?>
                    <!-- Embed in iframe -->
                    <div class="maparea">
                      <iframe
                        width="100%"
                        height="100%"
                        style="border:0; border-radius: 12px; box-shadow: 0 4px 8px rgba(0,0,0,0.1);"
                        loading="lazy"
                        allowfullscreen
                        referrerpolicy="no-referrer-when-downgrade"
                        src="<?= $map_url ?>">
                      </iframe>
                    </div>
                  </div>
                  <div class="col-md-4 mt-2">
                    <div class="row">
                      <?php if($user['type_id'] == 2 || $user['type_id'] == 1){ ?>
                      <div class="col-md-12 text-center">
                        <hr>
                        <button type="button" id="add_cont" style="width: fit-content;" class="custom-btn btn-11" value="<?=$cid;?>"><b>+</b> Add New Contact</button>
                        <hr>
                      </div>
                      <?php }else{ ?>
                      <div class="col-md-12 text-center">
                        <hr>
                        <a style="width: fit-content;" target="_BLANK" class="custom-btn btn-11" href="<?=base_url()?>Menu/AddPrimaryContactDetailsNew/<?=$cid?>">
                        ✏️ Add OR Edit Contact 
                        </a>
                        <hr>
                      </div>
                      <?php } ?>
                      <?php
                        $gradients = [
                            'linear-gradient(to right, #ede7f6, #ffffff)',
                            'linear-gradient(to right, #fce4ec, #ffffff)',
                            'linear-gradient(to right, #e3f2fd, #ffffff)',
                            'linear-gradient(to right, #e8f5e9, #ffffff)',
                            'linear-gradient(to right, #fff3e0, #ffffff)',
                            'linear-gradient(to right, #f3e5f5, #ffffff)',
                            'linear-gradient(to right, #e0f7fa, #ffffff)',
                            'linear-gradient(to right, #f9fbe7, #ffffff)'
                        ];
                        $index = 0;
                        ?>
                      <?php foreach ($cmpContactsDatas as $contact): ?>
                      <?php
                        $gradient = $gradients[$index % count($gradients)];
                        $index++;
                        ?>
                      <div class="col-md-12 mt-2">
                        <div class="card-height1" style="background: <?= $gradient ?>; border-radius: 12px; box-shadow: 0 4px 8px rgba(0,0,0,0.1); padding: 20px;">
                          <div class="text-center">
                            <h6 class="mb-0">👤 <?= !empty($contact->contactperson) ? $contact->contactperson : 'N/A' ?> 
                              <span class="badge badge-light float-right"><?= ucfirst($contact->type) ?></span>
                            </h6>
                          </div>
                          <hr>
                          <p>
                            <strong>📧 Email:</strong>
                            <?= !empty($contact->emailid) ? '<a href="mailto:' . $contact->emailid . '">' . $contact->emailid . '</a>' : 'N/A' ?>
                          </p>
                          <p>
                            <strong>📞 Phone:</strong>
                            <?= !empty($contact->phoneno) ? '<a href="tel:' . $contact->phoneno . '">' . $contact->phoneno . '</a>' : 'N/A' ?>
                          </p>
                          <p><strong>🏷️ Designation:</strong> <?= !empty($contact->designation) ? $contact->designation : 'N/A' ?></p>
                          <p><strong>🏷️ Linked In Profile:</strong> <?php 
                          if(!empty($contact->linked_in)){
                            echo '<a href="' . $contact->linked_in . '" target="_blank">' . $contact->linked_in . '</a>';
                          }else{
                            echo 'N/A';
                          } ?></p>
                          <p><strong>📆 Added On:</strong> <?= !empty($contact->createddate) ? date("d M Y", strtotime($contact->createddate)) : 'N/A' ?></p>
                          <hr>
                        </div>
                      </div>
                      <?php endforeach; ?>
                      <?php if($user['type_id'] == 2): ?>
                      <div class="col-md-12">
                        <hr>
                        <div class="card-footer text-muted p-3 text-center">
                          <b><a class="custom-btn btn-11" style="width: fit-content;" target="_BLANK" href="<?=base_url()?>Menu/PrimaryContact/<?= $cid ?>"><b>Set Primary Contact</b></a>
                        </div>
                        <hr>
                      </div>
                      <?php endif; ?>        
                    </div>
                  </div>
                </div>
                <hr>
                <?php } ?>
                <div class="taskcountcard">
                  <?php
                    $colors = [
                    "#e3f2fd", // blue
                    "#fce4ec", // pink
                    "#e8f5e9", // green
                    "#fff3e0", // orange
                    "#ede7f6", // purple
                    "#f9fbe7", // lime
                    "#f3e5f5", // light purple
                    "#e0f2f1"  // teal
                    ];
                    
                    $descriptions = [
                    "Call" => "Number of client or team calls made",
                    "Email" => "Total emails sent for communication",
                    "Barg in Meeting" => "Meetings with Barg officials held",
                    "Whatsapp Activity" => "WhatsApp interactions with stakeholders",
                    "Write MOM" => "Minutes of Meeting created",
                    "MOM Check" => "MOM documents reviewed or verified",
                    "Write Proposal" => "Proposals drafted for approval",
                    "Scheduled Meeting" => "Upcoming or fixed meetings planned"
                    ];
                    $index = 0;
                    ?>


                  <div class="card shadow-sm border-0 rounded-4 mb-4">
                    <div class="card-header text-center" style="background: linear-gradient(to right, #f6e7f3, #ffffff); border-radius: 12px; box-shadow: 0 4px 8px rgba(0,0,0,0.1); padding: 20px;">
                      <h5 class="mb-0">📊 Task Activity Summary</h5>
                      <small class="d-block">Overview of your engagement and planning activities</small>
                    </div>
                    <div class="card-body">
                      <div class="row">
                        <?php foreach ($task_counts as $label => $value): ?>
                        <?php
                          $bgColor = $colors[$index % count($colors)];
                          $desc = isset($descriptions[$label]) ? $descriptions[$label] : '';
                          $index++;
                          ?>
                        <div class="col-md-3 mb-3">
                          <div class="rounded p-3" style="background-color: <?= $bgColor ?>;">
                            <h6 class="mb-1 font-weight-bold text-center"><?= $label ?></h6>
                            <p class="small mb-2 text-center text-muted"><?= $desc ?></p>
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


                <div class="card shadow-sm border-0 rounded-4 mb-4">
                    <div class="card-header text-center" style="background: linear-gradient(to right, #f6e7f3, #ffffff); border-radius: 12px; box-shadow: 0 4px 8px rgba(0,0,0,0.1); padding: 20px;">
                      <h5 class="mb-0">📊 Status Activity Summary</h5>
                      <small class="d-block">Overview of your engagement and planning activities</small>
                    </div>
                    <div class="card-body">
                      <div class="row">
                        <?php foreach ($taskTtimeStatusDatas as $label => $value): ?>
                        <?php
                          $bgColor = $colors[$index % count($colors)];

                          $index++;
                          ?>
                        <div class="col-md-3 mb-3">
                          <div class="rounded p-3" style="background-color: <?= $bgColor ?>;">
                            <h6 class="mb-1 font-weight-bold text-center"><?= $label ?></h6>
                            <div class="text-center">
                              <span class="badge badge-pill badge-dark" style="font-size: 1.1rem;"><?= $value ?></span>
                            </div>
                          </div>
                        </div>
                        <?php endforeach; ?>
                      </div>
                    </div>
                  </div>
               

                <hr>



            <style>
              .scrollable-card-body {
    max-height: 400px;     /* adjust as needed */
    overflow-y: auto;
    overflow-x: hidden;
}
            </style>
                 
                <div class="row">



                  <div class="col-md-6">
                    <div class="card shadow-sm border-0 rounded-4 mb-4">
                     <div class="card-header text-center" style="background: linear-gradient(to right, #f6e7f3, #ffffff); border-radius: 12px; box-shadow: 0 4px 8px rgba(0,0,0,0.1); padding: 20px;">
                        <h5 class="mb-0">📊 Next-Step Confirmation in Current Financial Year</h5>
                        <small class="d-block">This feature records and tracks confirmed next actions for the current financial year. Each confirmation is linked to a task, responsible person, nd timestamp, ensuring accountability, structured follow-ups, and smooth execution of planned activities without missing deadlines or losing continuity.</small>
                      </div>
                      <div class="card-body scrollable-card-body">
                          <?php 
                            $curFinancialDate       = $this->Menu_model->getFinancialYearRange();
                            $start_financial_date   = $curFinancialDate['start_date'];
                            $end_financial_date     = $curFinancialDate['end_date'];
                            $nscs_Datas             = $this->Menu_model->GetNextStepConfirmationMessage($init_call_id,$start_financial_date,$end_financial_date);
                            ?>
                          <?php foreach ($nscs_Datas as $value): 
                            $next_step_confirmation_message = $value->next_step_confirmation_message;
                            $confirmation_by                = $value->confirmation_by;
                            $actontaken                     = $value->actontaken;
                            $purpose_achieved               = $value->purpose_achieved;
                            $appointmentdatetime            = $value->appointmentdatetime;
                            $remarks                        = $value->remarks;
                            $special_remarks                = $value->special_remarks;
                            $planned_on_status              = $value->planned_on_status;
                            $updated_at_on_status           = $value->updated_at_on_status;
                            ?>
                            <p>➡️ 
                                <strong>Next Step Confirmation:</strong> 📌
                                <?= htmlspecialchars($next_step_confirmation_message); ?>.  This action was confirmed by <strong><?= htmlspecialchars($confirmation_by); ?></strong> 👤, 
                                where the action taken was
                                <strong><?= htmlspecialchars($actontaken); ?></strong> ✅ and the intended purpose was
                                <strong><?= htmlspecialchars($purpose_achieved); ?></strong> 🎯. The appointment was scheduled on
                                <strong><?= htmlspecialchars($appointmentdatetime); ?></strong> 🗓️. 
                             
                                Remarks noted were
                                <strong><?= htmlspecialchars($remarks); ?></strong> 📝.
                             
                                The task was planned on
                                <strong><?= htmlspecialchars($planned_on_status); ?></strong> 🕒 and last updated on
                                <strong><?= htmlspecialchars($updated_at_on_status); ?></strong> 🔄.
                            </p>
                          <?php  $specialRemakrsArray = json_decode($special_remarks, true); ?>
                            <ul>
                                <?php foreach ($specialRemakrsArray as $question => $answer): ?>
                                    <li>
                                        <strong style='color:navy;'><?= htmlspecialchars($question); ?>:</strong>
                                        <?= htmlspecialchars($answer); ?>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                            <hr>
                      <?php endforeach; ?>
                      </div>
                    </div>
                  </div>
                  <div class="col-md-6">
                    <div class="card shadow-sm border-0 rounded-4 mb-4">
                     <div class="card-header text-center" style="background: linear-gradient(to right, #f6e7f3, #ffffff); border-radius: 12px; box-shadow: 0 4px 8px rgba(0,0,0,0.1); padding: 20px;">
                        <h5 class="mb-0">📊 Next-Step Confirmation All Time</h5>
                        <small class="d-block">This feature records and tracks confirmed next actions . Each confirmation is linked to a task, responsible person, nd timestamp, ensuring accountability, structured follow-ups, and smooth execution of planned activities without missing deadlines or losing continuity.</small>
                      </div>
                      <div class="card-body scrollable-card-body">
                          <?php 
                            $nscs_Datas             = $this->Menu_model->GetNextStepConfirmationMessageAllTime($init_call_id);
                            ?>
                          <?php foreach ($nscs_Datas as $value): 
                            $next_step_confirmation_message = $value->next_step_confirmation_message;
                            $confirmation_by                = $value->confirmation_by;
                            $actontaken                     = $value->actontaken;
                            $purpose_achieved               = $value->purpose_achieved;
                            $appointmentdatetime            = $value->appointmentdatetime;
                            $remarks                        = $value->remarks;
                            $special_remarks                = $value->special_remarks;
                            $planned_on_status              = $value->planned_on_status;
                            $updated_at_on_status           = $value->updated_at_on_status;
                            ?>
                            <p>➡️ 
                                <strong>Next Step Confirmation:</strong> 📌
                                <?= htmlspecialchars($next_step_confirmation_message); ?>.  This action was confirmed by <strong><?= htmlspecialchars($confirmation_by); ?></strong> 👤, 
                                where the action taken was
                                <strong><?= htmlspecialchars($actontaken); ?></strong> ✅ and the intended purpose was
                                <strong><?= htmlspecialchars($purpose_achieved); ?></strong> 🎯. The appointment was scheduled on
                                <strong><?= htmlspecialchars($appointmentdatetime); ?></strong> 🗓️. 
                             
                                Remarks noted were
                                <strong><?= htmlspecialchars($remarks); ?></strong> 📝.
                             
                                The task was planned on
                                <strong><?= htmlspecialchars($planned_on_status); ?></strong> 🕒 and last updated on
                                <strong><?= htmlspecialchars($updated_at_on_status); ?></strong> 🔄.
                            </p>
                          <?php  $specialRemakrsArray = json_decode($special_remarks, true); ?>
                            <ul>
                                <?php foreach ($specialRemakrsArray as $question => $answer): ?>
                                    <li>
                                        <strong style='color:navy;'><?= htmlspecialchars($question); ?>:</strong>
                                        <?= htmlspecialchars($answer); ?>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                            <hr>
                      <?php endforeach; ?>
                      </div>
                    </div>
                  </div>
          



                </div>




                <?php 
                $curefinancialyear    = $this->Menu_model->getCurrentFinancialYearStart();
                $currentfinancialyear = $this->Menu_model->getFinancialYearRange();
               
                // if ('2026-01-05' >= $currentfinancialyear['start_date'] && '2026-01-05' <= $currentfinancialyear['end_date']) {
                //      echo 'Task date is between start and end date';
                // }

                ?>

              <section class="p-2" style="box-shadow: rgba(60, 64, 67, 0.3) 0px 1px 2px 0px, rgba(60, 64, 67, 0.15) 0px 2px 6px 2px;">

           
                 <div class="text-center" style="background: linear-gradient(to right, #f6e7f3, #ffffff); border-radius: 12px; box-shadow: 0 4px 8px rgba(0,0,0,0.1); padding: 20px;">
                 <h5 class="mb-0">📊 Completed Tasks (Current Financial Year)</h5>
                  <center>
                    <small class="d-block" style="width: 70%;">
                      ✅ This section highlights all tasks successfully completed during the current financial year. Gain insights into 📅 timelines, 🎯 key actions, and 📈 outcomes — helping you evaluate performance and plan ahead with clarity and confidence.
                    </small>
                  </center>
                </div>
                <hr>
                <div class="table-responsive text-nowrap">
                  <table id="example7" class="table table-striped table-bordered" cellspacing="0" width="100%">
                    <thead class="thead-dark">
                    <tr>
                      <th class="text-capitalize">🔢 Sr. NO</th>
                      <th class="text-capitalize">👤 Task Username</th>
                      <th class="text-capitalize">🏷️ CID</th>
                      <th class="text-capitalize">🏢 Company Name</th>
                      <th class="text-capitalize">📌 Current Status</th>
                      <th class="text-capitalize">📝 Task Name</th>
                      <th class="text-capitalize">📊 Task Status</th>
                      <th class="text-capitalize">🎯 Action</th>
                      <th class="text-capitalize">🎯 Purpose</th>
                      <th class="text-capitalize">📅 Planned On Status</th>
                      <th class="text-capitalize">🔄 Change On Status</th>
                      <th class="text-capitalize">📆 Original Task Date</th>
                      <th class="text-capitalize">⏰ Appointment DateTime</th>
                      <th class="text-capitalize">🚀 Initiated DateTime</th>
                      <th class="text-capitalize">🔁 Updated DateTime</th>
                      <th class="text-capitalize">⏱️ Total Time Taken</th>
                      <th class="text-capitalize">💬 Late Remarks Message</th>
                      <th class="text-capitalize">✅ Task Approved By</th>
                      <th class="text-capitalize">🗒️ Remarks</th>
                      <th class="text-capitalize">⭐ Special Remarks</th>
                      <th class="text-capitalize">⭐  Next-Step Confirmation</th>
                      <th class="text-capitalize">📍 Closing Timeline</th>
                      <th class="text-capitalize">📝 Proposal Require</th>
                      <th class="text-capitalize">🗑️ Deleted Task</th>
                      <th class="text-capitalize">🔁 Task RePlanned Count</th>
                      <th class="text-capitalize">✏️ Meeting Type</th>
                      <th class="text-capitalize">🧾 MOM</th>
                      <th class="text-capitalize">🔍 View Details</th>
                      <th class="text-capitalize">⭐ Total Star</th>
                    </tr>
                    </thead>
                    <tbody>
                      <?php
                        $i = 1;
                         
                        foreach ($cmpTasksDatas as $row) {
                            $cknextCFID = $row->nextCFID;
                            $appointmentdatetime = $row->appointmentdatetime;
                            $ccfyear = date("Y", strtotime($appointmentdatetime));

                            if($cknextCFID == 0){
                                continue;
                            }

                            $appointmentdate = date('Y-m-d', strtotime($appointmentdatetime));
                            if (
                                strtotime($appointmentdate) < strtotime($currentfinancialyear['start_date']) ||
                                strtotime($appointmentdate) > strtotime($currentfinancialyear['end_date'])
                            ) {
                              continue;
                            }

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
                          ?>
                      <tr style="background-color: <?php echo $backgroundColor; ?>;color:<?=$backgroundColorNew;?>!important" title="<?=$meeting_task_id?>">
                        <td><?= htmlspecialchars($i) ?></td>
                        <td class="text-capitalize"><?= htmlspecialchars($row->task_username) ?></td>
                        <td><a style="color:<?=$backgroundColorNew;?>!important" target="_BLANK" href="<?=base_url();?>/Menu/CompanyDetails/<?=$row->cid?>"><?= htmlspecialchars($row->cid) ?></a></td>
                        <td class="text-capitalize"><a style="color:<?=$backgroundColorNew;?>!important" target="_BLANK" href="<?=base_url();?>/Menu/CompanyDetails/<?=$row->cid?>"><?= htmlspecialchars($row->compname) ?></a></td>
                        <td><?= htmlspecialchars($row->current_status) ?></td>
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
                            ?>
                        </td>
                        <td><?= htmlspecialchars($row->next_step_confirmation) ?></td>
                        <td><?= htmlspecialchars($row->closing_timeline) ?></td>
                        <td><?= htmlspecialchars($row->proposal_require) ?></td>
                        <td><?php if($row->delete_request > 0){ ?> <span class="bg-danger p-1"> Deleted</span> <?php }else{ ?> <?php } ?></td>
                        <td> 
                          <a style="color:<?=$backgroundColorNew;?>!important" href="<?=base_url();?>Menu/ReplanedLog/<?=$row->task_id;  ?>" target="_BLANK"><?= htmlspecialchars($row->plan_count) ?></a>
                        </td>
                        <td><?= htmlspecialchars($row->mtype) ?></td>
                        <td>
                          <?php 
                      
                          if (in_array($row->actiontype_id, [3, 4, 17,22])) {
                          
                          $momtask_mom = '';
                          $momtaskDatas = $this->Menu_model->GetTBLMomTaskByTaskId($row->task_id);
                          if(sizeof($momtaskDatas) > 0){
                              $momtask_id = $momtaskDatas[0]->id;
                              $momtask_mom = $momtaskDatas[0]->mom;
                              $momdatas = $this->Menu_model->GetMomDataByTaskId($momtask_id);
                              if(sizeof($momdatas) > 0){
                              $mom_id = $momdatas[0]->id;
                              echo '<a style="color:<?=$backgroundColorNew;?>!important" class="p-1 bg-success" target="_BLANK" href="' . base_url() . 'Management/MomDataCheckInAdmin/' . $mom_id . '/' . $row->cid . '">view</a>'; 
                              }else{
                              echo "<span class='p-1 bg-warning'>Pending For Write</span>";
                              }
                          }else{
                          echo "NA";
                          }
                          }
                          ?>
                          </td>
                          <td>
                          <?php  if (in_array($row->actiontype_id, [3, 4, 17,22])) {?>
                          <a class="bg-success p-1" href="<?= base_url(); ?>Reports/ViewMeetingDetails/<?= $row->task_id; ?>" target="_blank">View Details</a>
                          <?php }else if(in_array($row->actiontype_id,[23,24])){  ?>
                             <a style="color:<?=$backgroundColorNew;?>!important" href="<?=base_url();?>Menu/TBLTaskDetails/<?=$row->task_id;  ?>" target="_BLANK">View</a>
                            <?php }else{ ?> <?php } ?>
                          </td>


                          <td>
                          <?php  
                          $getFindStar = $this->Menu_model->GetTotalStarFoundAfterCheck($row->task_id);
                           if ($getFindStar > 0){ ?>
                             <a class="bg-dark p-1" 
                                href="<?= base_url('Reports/TaskCheckingManagementStarRatingSingleDetails/' 
                                          . $row->task_id . '/' 
                                          . date('Y-m-d', strtotime($row->appointmentdatetime)) . '/' 
                                          . date('Y-m-d', strtotime($row->appointmentdatetime))) ?>" 
                                target="_blank">
                                  <?= htmlspecialchars($getFindStar) ?> ⭐
                              </a>
                          <?php  }else{ ?>
                            <span class='p-1 bg-warning'>Pending</span>
                          <?php } ?>
                          </td>
                      </tr>
                      <?php $i++; } ?>
                    </tbody>
                  </table>
                </div>

                  <hr>

                   <div class="text-center" style="background: linear-gradient(to right, #f6e7f3, #ffffff); border-radius: 12px; box-shadow: 0 4px 8px rgba(0,0,0,0.1); padding: 20px;">
                 <h5 class="mb-0">📊 Completed Tasks (Current Financial Year Special Remarks)</h5>
                  <center>
                    <small class="d-block" style="width: 70%;">
                      ✅ This section highlights all tasks successfully completed during the current financial year. Gain insights into 📅 timelines, 🎯 key actions, and 📈 outcomes — helping you evaluate performance and plan ahead with clarity and confidence.
                    </small>
                  </center>
                </div>
                <hr>
                <div class="table-responsive text-nowrap">
                  <table id="example8" class="table table-striped table-bordered" cellspacing="0" width="100%">
                    <thead class="thead-dark">
                    <tr>
                        <th class="text-capitalize">🔢 Sr. NO</th>
                        <th class="text-capitalize">👤 Task Username</th>
                        <th class="text-capitalize">🏷️ CID</th>
                        <th class="text-capitalize">🏢 Company Name</th>
                        <th class="text-capitalize">📌 Current Status</th>
                        <th class="text-capitalize">📝 Task Name</th>
                        <th class="text-capitalize">📊 Task Status</th>
                        <th class="text-capitalize">🎯 Action</th>
                        <th class="text-capitalize">🎯 Purpose</th>
                        <th class="text-capitalize">📅 Planned On Status</th>
                        <th class="text-capitalize">🔄 Change On Status</th>
                        <th class="text-capitalize">📆 Original Task Date</th>
                        <th class="text-capitalize">⏰ Appointment DateTime</th>
                        <th class="text-capitalize">🚀 Initiated DateTime</th>
                        <th class="text-capitalize">🔁 Updated DateTime</th>
                        <th class="text-capitalize">⏱️ Total Time Taken</th>
                        <th class="text-capitalize">💬 Late Remarks Message</th>
                        <th class="text-capitalize">✅ Task Approved By</th>
                        <th class="text-capitalize">🗒️ Remarks</th>
                        <th class="text-capitalize">⭐ Special Remarks</th>
                        <th class="text-capitalize">⭐  Next-Step Confirmation</th>
                        <th class="text-capitalize">⭐ Closing Timeline</th>
                        <th class="text-capitalize">📝 Proposal Require</th>
                        <th class="text-capitalize">🗑️ Deleted Task</th>
                        <th class="text-capitalize">🔁 Task RePlanned Count</th>
                        <th class="text-capitalize">⭐ Total Star</th>
                      </tr>
                    </thead>
                    <tbody>
                      <?php
                        $i = 1;
                         
                        foreach ($cmpTasksDatas as $row) {
                            $cknextCFID = $row->nextCFID;
                            $appointmentdatetime = $row->appointmentdatetime;
                            $ccfyear = date("Y", strtotime($appointmentdatetime));

                            if($cknextCFID == 0){
                                continue;
                            }
                             $appointmentdate = date('Y-m-d', strtotime($appointmentdatetime));
                            if (
                                strtotime($appointmentdate) < strtotime($currentfinancialyear['start_date']) ||
                                strtotime($appointmentdate) > strtotime($currentfinancialyear['end_date'])
                            ) {
                              continue;
                            }

                          if (empty($row->special_remarks) || $row->special_remarks == null || $row->special_remarks == 'null' || $row->special_remarks == '[]'){
                          continue;
                          }

                          // echo $row->special_remarks;

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
                          ?>
                      <tr style="background-color: <?php echo $backgroundColor; ?>;color:<?=$backgroundColorNew;?>!important" title="<?=$meeting_task_id?>">
                        <td><?= htmlspecialchars($i) ?></td>
                        <td class="text-capitalize"><?= htmlspecialchars($row->task_username) ?></td>
                        <td><a style="color:<?=$backgroundColorNew;?>!important" target="_BLANK" href="<?=base_url();?>/Menu/CompanyDetails/<?=$row->cid?>"><?= htmlspecialchars($row->cid) ?></a></td>
                        <td class="text-capitalize"><a style="color:<?=$backgroundColorNew;?>!important" target="_BLANK" href="<?=base_url();?>/Menu/CompanyDetails/<?=$row->cid?>"><?= htmlspecialchars($row->compname) ?></a></td>
                        <td><?= htmlspecialchars($row->current_status) ?></td>
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
                            ?>
                        </td>
                        <td><?= htmlspecialchars($row->next_step_confirmation) ?></td>
                        <td><?= htmlspecialchars($row->closing_timeline) ?></td>
                        <td><?= htmlspecialchars($row->proposal_require) ?></td>
                        <td><?php if($row->delete_request > 0){ ?> <span class="bg-danger p-1"> Deleted</span> <?php }else{ ?> <?php } ?></td>
                        <td> 
                          <a style="color:<?=$backgroundColorNew;?>!important" href="<?=base_url();?>Menu/ReplanedLog/<?=$row->task_id;  ?>" target="_BLANK"><?= htmlspecialchars($row->plan_count) ?></a>
                        </td>
                        <td>
                          <?php  
                          $getFindStar = $this->Menu_model->GetTotalStarFoundAfterCheck($row->task_id);
                           if ($getFindStar > 0){ ?>
                             <a class="bg-dark p-1" 
                                href="<?= base_url('Reports/TaskCheckingManagementStarRatingSingleDetails/' 
                                          . $row->task_id . '/' 
                                          . date('Y-m-d', strtotime($row->appointmentdatetime)) . '/' 
                                          . date('Y-m-d', strtotime($row->appointmentdatetime))) ?>" 
                                target="_blank">
                                  <?= htmlspecialchars($getFindStar) ?> ⭐
                              </a>
                          <?php  }else{ ?>
                            <span class='p-1 bg-warning'>Pending</span>
                          <?php } ?>
                          </td>
                      </tr>
                      <?php $i++; } ?>
                    </tbody>
                  </table>
                </div>



                  <hr>
                    <div class="text-center" style="background: linear-gradient(to right, #ede7f6, #ffffff); border-radius: 12px; box-shadow: 0 4px 8px rgba(0,0,0,0.1); padding: 20px;">
                     <h5>
                      <i>🔄 Team Status Conversion (Current Financial Year)</i><br>
                      <small>🔄 Team Status Conversion brings clear visibility to progress 📊, tracks every stage 🔍, boosts teamwork 🤝, and ensures goals 🎯 are achieved faster ⚡ with smooth workflow 🔧 and smart insights 💡.</small>
                    </h5>
                  <p><i><b><?= $start_financial_date ?> To <?= $targetCurDate ?></b></i></p>
                </div>
                <hr>
                <div class="table-responsive text-nowrap">
                  <table id="example21" class="table table-striped table-bordered" cellspacing="0" width="100%">
                    <thead class="thead-dark">
                        <tr class="text-center1">
                            <th scope="col">🔢 Sr. NO</th>
                            <th scope="col">🔄 Status Change</th>
                            <th scope="col">✅ Total Change</th>
                        </tr>
                    </thead>
                    <tbody>
                      <?php
                        $scnv = 1;
                         
                        foreach ($bdconversionsCFyear as $bdconversion) {
                          $r = rand(150, 255);
                          $g = rand(150, 255);
                          $b = rand(150, 255);
                          $backgroundColor = "rgba($r, $g, $b,.2)";
                          $hue = rand(0, 360); // Full color wheel
                          $saturation = rand(60, 100); // High saturation for rich colors
                          $lightness = rand(30, 45); // Low lightness for a deeper tone
                          $backgroundColorNew = "hsl($hue, $saturation%, $lightness%)";
                          ?>
                          <tr style="background-color: <?php echo $backgroundColor; ?>;color:<?=$backgroundColorNew;?>!important" title="<?=$cid?>">
                            <td><?=$scnv;?></td>
                              <td>
                                 <a target="_BLANK" class="p-2" 
                                  href="<?=base_url()?>Reports/TodaysConversionWithCIDDatas/<?=$cid?>/<?=$start_financial_date?>/<?=$targetCurDate?>/<?= $bdconversion->status_change_id;?>">
                                      <?= $bdconversion->status_change ?>
                                  </a>
                              </td>
                              <td>
                                  <a target="_BLANK" class="badge badge-pill badge-primary p-2" 
                                  href="<?=base_url()?>Reports/TodaysConversionWithCIDDatas/<?=$cid?>/<?=$start_financial_date?>/<?=$targetCurDate?>/<?= $bdconversion->status_change_id;?>">
                                      <?= $bdconversion->total_changes ?>
                                  </a>
                              </td>
                          </tr>
                      <?php $scnv++; } ?>
                    </tbody>
                  </table>
                </div>



                     </section> 

                <hr>
                <div class="text-center" style="background: linear-gradient(to right, #f6e7f3, #ffffff); border-radius: 12px; box-shadow: 0 4px 8px rgba(0,0,0,0.1); padding: 20px;">
                  <h5 class="mb-0">📊 Completed Tasks (All Time History)</h5>
                  <center>
                    <small class="d-block" style="width: 70%;">This section provides a detailed snapshot of all tasks you've successfully completed, highlighting key actions, timelines, and outcomes. It helps you monitor progress, measure impact, and maintain accountability in your planning and engagement efforts. Use this overview to evaluate performance and plan future steps with clarity and confidence.</small>
                  </center>
                </div>
                <hr>
                <div class="table-responsive text-nowrap">
                  <table id="example1" class="table table-striped table-bordered" cellspacing="0" width="100%">
                    <thead class="thead-dark">
                    <tr>
                      <th class="text-capitalize">🔢 Sr. NO</th>
                      <th class="text-capitalize">👤 Task Username</th>
                      <th class="text-capitalize">🏷️ CID</th>
                      <th class="text-capitalize">🏢 Company Name</th>
                      <th class="text-capitalize">📌 Current Status</th>
                      <th class="text-capitalize">📝 Task Name</th>
                      <th class="text-capitalize">📊 Task Status</th>
                      <th class="text-capitalize">🎯 Action</th>
                      <th class="text-capitalize">🎯 Purpose</th>
                      <th class="text-capitalize">📅 Planned On Status</th>
                      <th class="text-capitalize">🔄 Change On Status</th>
                      <th class="text-capitalize">📆 Original Task Date</th>
                      <th class="text-capitalize">⏰ Appointment DateTime</th>
                      <th class="text-capitalize">🚀 Initiated DateTime</th>
                      <th class="text-capitalize">🔁 Updated DateTime</th>
                      <th class="text-capitalize">⏱️ Total Time Taken</th>
                      <th class="text-capitalize">💬 Late Remarks Message</th>
                      <th class="text-capitalize">✅ Task Approved By</th>
                      <th class="text-capitalize">🗒️ Remarks</th>
                      <th class="text-capitalize">⭐ Special Remarks</th>
                      <th class="text-capitalize">⭐  Next-Step Confirmation</th>
                      <th class="text-capitalize">⭐ Closing Timeline</th>
                      <th class="text-capitalize">📝 Proposal Require</th>
                      <th class="text-capitalize">🗑️ Deleted Task</th>
                      <th class="text-capitalize">🔁 Task RePlanned Count</th>
                      <th class="text-capitalize">✏️ Meeting Type</th>
                      <th class="text-capitalize">🧾 MOM</th>
                      <th class="text-capitalize">🔍 View Details</th>
                      <th class="text-capitalize">⭐ Total Star</th>
                    </tr>
                    </thead>
                    <tbody>
                      <?php
                        $i = 1;
                        foreach ($cmpTasksDatas as $row) {
                            $cknextCFID = $row->nextCFID;
                            if($cknextCFID == 0){
                                continue;
                            }
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
                          ?>
                      <tr style="background-color: <?php echo $backgroundColor; ?>;color:<?=$backgroundColorNew;?>!important" title="<?=$meeting_task_id?>">
                        <td><?= htmlspecialchars($i) ?></td>
                        <td class="text-capitalize"><?= htmlspecialchars($row->task_username) ?></td>
                        <td><a style="color:<?=$backgroundColorNew;?>!important" target="_BLANK" href="<?=base_url();?>/Menu/CompanyDetails/<?=$row->cid?>"><?= htmlspecialchars($row->cid) ?></a></td>
                        <td class="text-capitalize"><a style="color:<?=$backgroundColorNew;?>!important" target="_BLANK" href="<?=base_url();?>/Menu/CompanyDetails/<?=$row->cid?>"><?= htmlspecialchars($row->compname) ?></a></td>
                        <td><?= htmlspecialchars($row->current_status) ?></td>
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
                            ?>
                        </td>
                        <td><?= htmlspecialchars($row->next_step_confirmation) ?></td>
                        <td><?= htmlspecialchars($row->closing_timeline) ?></td>
                        <td><?= htmlspecialchars($row->proposal_require) ?></td>
                        <td><?php if($row->delete_request > 0){ ?> <span class="bg-danger p-1"> Deleted</span> <?php }else{ ?> <?php } ?></td>
                        <td> 
                          <a style="color:<?=$backgroundColorNew;?>!important" href="<?=base_url();?>Menu/ReplanedLog/<?=$row->task_id;  ?>" target="_BLANK"><?= htmlspecialchars($row->plan_count) ?></a>
                        </td>
                        <td><?= htmlspecialchars($row->mtype) ?></td>
                        <td>
                          <?php 
                      
                          if (in_array($row->actiontype_id, [3, 4, 17,22])) {
                          
                          $momtask_mom = '';
                          $momtaskDatas = $this->Menu_model->GetTBLMomTaskByTaskId($row->task_id);
                          if(sizeof($momtaskDatas) > 0){
                              $momtask_id = $momtaskDatas[0]->id;
                              $momtask_mom = $momtaskDatas[0]->mom;
                              $momdatas = $this->Menu_model->GetMomDataByTaskId($momtask_id);
                              if(sizeof($momdatas) > 0){
                              $mom_id = $momdatas[0]->id;
                              echo '<a style="color:<?=$backgroundColorNew;?>!important" class="p-1 bg-success" target="_BLANK" href="' . base_url() . 'Management/MomDataCheckInAdmin/' . $mom_id . '/' . $row->cid . '">view</a>'; 
                              }else{
                              echo "<span class='p-1 bg-warning'>Pending For Write</span>";
                              }
                          }else{
                          echo "NA";
                          }
                          }
                          ?>
                          </td>
                          <td>
                          <?php  if (in_array($row->actiontype_id, [3, 4, 17,22])) {?>
                          <a class="bg-success p-1" href="<?= base_url(); ?>Reports/ViewMeetingDetails/<?= $row->task_id; ?>" target="_blank">View Details</a>
                          <?php }else if(in_array($row->actiontype_id,[23,24])){  ?>
                             <a style="color:<?=$backgroundColorNew;?>!important" href="<?=base_url();?>Menu/TBLTaskDetails/<?=$row->task_id;  ?>" target="_BLANK">View</a>
                            <?php }else{ ?> <?php } ?>
                          </td>
                          <td>
                          <?php  
                          $getFindStar = $this->Menu_model->GetTotalStarFoundAfterCheck($row->task_id);
                           if ($getFindStar > 0){ ?>
                             <a class="bg-dark p-1" 
                                href="<?= base_url('Reports/TaskCheckingManagementStarRatingSingleDetails/' 
                                          . $row->task_id . '/' 
                                          . date('Y-m-d', strtotime($row->appointmentdatetime)) . '/' 
                                          . date('Y-m-d', strtotime($row->appointmentdatetime))) ?>" 
                                target="_blank">
                                  <?= htmlspecialchars($getFindStar) ?> ⭐
                              </a>
                          <?php  }else{ ?>
                            <span class='p-1 bg-warning'>Pending</span>
                          <?php } ?>
                          </td>
                      </tr>
                      <?php $i++; } ?>
                    </tbody>
                  </table>
                </div>
                <hr>




                 <hr>
                    <div class="text-center" style="background: linear-gradient(to right, #ede7f6, #ffffff); border-radius: 12px; box-shadow: 0 4px 8px rgba(0,0,0,0.1); padding: 20px;">
                     <h5>
                      <i>🔄 Team Status Conversion (All Time)</i><br>
                      <small>🔄 Team Status Conversion brings clear visibility to progress 📊, tracks every stage 🔍, boosts teamwork 🤝, and ensures goals 🎯 are achieved faster ⚡ with smooth workflow 🔧 and smart insights 💡.</small>
                    </h5>
                  <p><i><b><?= $start_financial_date ?> To <?= $targetCurDate ?></b></i></p>
                </div>
                <hr>
                <div class="table-responsive text-nowrap">
                  <table id="example22" class="table table-striped table-bordered" cellspacing="0" width="100%">
                    <thead class="thead-dark">
                        <tr class="text-center1">
                            <th scope="col">🔢 Sr. NO</th>
                            <th scope="col">🔄 Status Change</th>
                            <th scope="col">✅ Total Change</th>
                        </tr>
                    </thead>
                    <tbody>
                      <?php
                        $scnvall = 1;
                         
                        foreach ($bdconversionsAlltimes as $bdconversion) {
                          $r = rand(150, 255);
                          $g = rand(150, 255);
                          $b = rand(150, 255);
                          $backgroundColor = "rgba($r, $g, $b,.2)";
                          $hue = rand(0, 360); // Full color wheel
                          $saturation = rand(60, 100); // High saturation for rich colors
                          $lightness = rand(30, 45); // Low lightness for a deeper tone
                          $backgroundColorNew = "hsl($hue, $saturation%, $lightness%)";
                          ?>
                          <tr style="background-color: <?php echo $backgroundColor; ?>;color:<?=$backgroundColorNew;?>!important" title="<?=$cid?>">
                              <td><?=$scnvall;?></td>
                              <td>
                                 <a target="_BLANK" class="p-2" 
                                  href="<?=base_url()?>Reports/TodaysConversionWithCIDDatas/<?=$cid?>/all/all/<?= $bdconversion->status_change_id;?>">
                                      <?= $bdconversion->status_change ?>
                                  </a>
                              </td>
                              <td>
                                  <a target="_BLANK" class="badge badge-pill badge-primary p-2" 
                                  href="<?=base_url()?>Reports/TodaysConversionWithCIDDatas/<?=$cid?>/all/all/<?= $bdconversion->status_change_id;?>">
                                      <?= $bdconversion->total_changes ?>
                                  </a>
                              </td>
                          </tr>
                      <?php $scnvall++; } ?>
                    </tbody>
                  </table>
                </div>



                      <hr>


                <div class="text-center" style="background: linear-gradient(to right, #ffe274, #ffffff); border-radius: 12px; box-shadow: 0 4px 8px rgba(0,0,0,0.1); padding: 20px;">
                  <h5 class="mb-0">📊 Pending Tasks (All Time History)</h5>
                  <center>
                    <small class="d-block" style="width: 70%;">
                    This section highlights all your ongoing or upcoming tasks that are yet to be completed. It offers insights into scheduled actions, timelines, and responsibilities—helping you stay organized, prioritize effectively, and ensure timely execution of your plans. Use this view to track what’s next and prepare accordingly.
                    </small>
                  </center>
                </div>
                <hr>
                <div class="table-responsive text-nowrap">
                  <table id="example2" class="table table-striped table-bordered" cellspacing="0" width="100%">
                    <thead class="thead-dark">
                    <tr>
                      <th class="text-capitalize">🔢 Sr. NO</th>
                      <th class="text-capitalize">👤 Task Username</th>
                      <th class="text-capitalize">🏷️ CID</th>
                      <th class="text-capitalize">🏢 Company Name</th>
                      <th class="text-capitalize">📌 Current Status</th>
                      <th class="text-capitalize">📝 Task Name</th>
                      <th class="text-capitalize">📊 Task Status</th>
                      <th class="text-capitalize">🎯 Action</th>
                      <th class="text-capitalize">🎯 Purpose</th>
                      <th class="text-capitalize">📅 Planned On Status</th>
                      <th class="text-capitalize">🔄 Change On Status</th>
                      <th class="text-capitalize">📆 Original Task Date</th>
                      <th class="text-capitalize">⏰ Appointment DateTime</th>
                      <th class="text-capitalize">🚀 Initiated DateTime</th>
                      <th class="text-capitalize">🔁 Updated DateTime</th>
                      <th class="text-capitalize">⏱️ Total Time Taken</th>
                      <th class="text-capitalize">💬 Late Remarks Message</th>
                      <th class="text-capitalize">✅ Task Approved By</th>
                      <th class="text-capitalize">🗒️ Remarks</th>
                      <th class="text-capitalize">⭐ Special Remarks</th>
                      <th class="text-capitalize">📉 Closing Timeline</th>
                      <th class="text-capitalize">📝 Proposal Require</th>
                      <th class="text-capitalize">🗑️ Deleted Task</th>
                      <th class="text-capitalize">🔁 Task RePlanned Count</th>
                      <th class="text-capitalize">⭐ Total Star</th>
                    </tr>
                    </thead>
                    <tbody>
                      <?php
                        $i = 1;
                        foreach ($cmpTasksDatas as $row) {
                            $pknextCFID = $row->nextCFID;
                           if($pknextCFID != 0){
                                continue;
                            }
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
                          ?>
                      <tr style="background-color: <?php echo $backgroundColor; ?>;color:<?=$backgroundColorNew;?>!important" title="<?=$meeting_task_id?>">
                        <td><?= htmlspecialchars($i) ?></td>
                        <td class="text-capitalize"><?= htmlspecialchars($row->task_username) ?></td>
                        <td><a style="color:<?=$backgroundColorNew;?>!important" target="_BLANK" href="<?=base_url();?>/Menu/CompanyDetails/<?=$row->cid?>"><?= htmlspecialchars($row->cid) ?></a></td>
                        <td class="text-capitalize"><a style="color:<?=$backgroundColorNew;?>!important" target="_BLANK" href="<?=base_url();?>/Menu/CompanyDetails/<?=$row->cid?>"><?= htmlspecialchars($row->compname) ?></a></td>
                        <td><?= htmlspecialchars($row->current_status) ?></td>
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
                            ?>
                        </td>
                        <td><?= htmlspecialchars($row->closing_timeline) ?></td>
                        <td><?= htmlspecialchars($row->proposal_require) ?></td>
                        <td><?php if($row->delete_request > 0){ ?> <span class="bg-danger p-1"> Deleted</span> <?php }else{ ?> <?php } ?></td>
                        <td> 
                          <a style="color:<?=$backgroundColorNew;?>!important" href="<?=base_url();?>Menu/ReplanedLog/<?=$row->task_id;  ?>" target="_BLANK"><?= htmlspecialchars($row->plan_count) ?></a>
                        </td>
                        <td>
                          <?php  
                          $getFindStar = $this->Menu_model->GetTotalStarFoundAfterCheck($row->task_id);
                           if ($getFindStar > 0){ ?>
                             <a class="bg-dark p-1" 
                                href="<?= base_url('Reports/TaskCheckingManagementStarRatingSingleDetails/' 
                                          . $row->task_id . '/' 
                                          . date('Y-m-d', strtotime($row->appointmentdatetime)) . '/' 
                                          . date('Y-m-d', strtotime($row->appointmentdatetime))) ?>" 
                                target="_blank">
                                  <?= htmlspecialchars($getFindStar) ?> ⭐
                              </a>
                          <?php  }else{ ?>
                            <span class='p-1 bg-warning'>Pending</span>
                          <?php } ?>
                          </td>
                      </tr>
                      <?php $i++; } ?>
                    </tbody>
                  </table>
                </div>
                <hr>
                <hr>
                <div class="text-center" style="background: linear-gradient(to right,rgb(164, 252, 147), #ffffff); border-radius: 12px; box-shadow: 0 4px 8px rgba(0,0,0,0.1); padding: 20px;">
                  <h5 class="mb-0">📊 Positive Conversions Tasks (All Time History)</h5>
                  <center>
                    <small class="d-block" style="width: 70%;">
                    This section showcases tasks that have led to or are expected to result in positive conversions. It helps you monitor impactful actions, track progress, and focus on key follow-ups—ensuring you maximize outcomes and maintain momentum in your conversion journey.
                    </small>
                  </center>
                  </center>
                </div>
                <hr>
                <div class="table-responsive text-nowrap">
                  <table id="example4" class="table table-striped table-bordered" cellspacing="0" width="100%">
                    <thead class="thead-dark">
                    <tr>
                        <th class="text-capitalize">🔢 Sr. NO</th>
                        <th class="text-capitalize">👤 Task Username</th>
                        <th class="text-capitalize">🏷️ CID</th>
                        <th class="text-capitalize">🏢 Company Name</th>
                        <th class="text-capitalize">📌 Current Status</th>
                        <th class="text-capitalize">📝 Task Name</th>
                        <th class="text-capitalize">📊 Task Status</th>
                        <th class="text-capitalize">🎯 Action</th>
                        <th class="text-capitalize">🎯 Purpose</th>
                        <th class="text-capitalize">📅 Planned On Status</th>
                        <th class="text-capitalize">🔄 Change On Status</th>
                        <th class="text-capitalize">📆 Original Task Date</th>
                        <th class="text-capitalize">⏰ Appointment DateTime</th>
                        <th class="text-capitalize">🚀 Initiated DateTime</th>
                        <th class="text-capitalize">🔁 Updated DateTime</th>
                        <th class="text-capitalize">⏱️ Total Time Taken</th>
                        <th class="text-capitalize">💬 Late Remarks Message</th>
                        <th class="text-capitalize">✅ Task Approved By</th>
                        <th class="text-capitalize">🗒️ Remarks</th>
                        <th class="text-capitalize">⭐ Special Remarks</th>
                        <th class="text-capitalize">📉 Closing Timeline</th>
                        <th class="text-capitalize">📝 Proposal Require</th>
                        <th class="text-capitalize">🗑️ Deleted Task</th>
                        <th class="text-capitalize">🔁 Task RePlanned Count</th>
                        <th class="text-capitalize">⭐ Total Star</th>
                      </tr>

                    </thead>
                    <tbody>
                      <?php
                        $i = 1;
                        foreach ($cmpPositiveTaskDatas as $row) {
                          $pknextCFID = $row->nextCFID;
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
                          ?>
                      <tr style="background-color: <?php echo $backgroundColor; ?>;color:<?=$backgroundColorNew;?>!important" title="<?=$meeting_task_id?>">
                        <td><?= htmlspecialchars($i) ?></td>
                        <td class="text-capitalize"><?= htmlspecialchars($row->task_username) ?></td>
                        <td><a style="color:<?=$backgroundColorNew;?>!important" target="_BLANK" href="<?=base_url();?>/Menu/CompanyDetails/<?=$row->cid?>"><?= htmlspecialchars($row->cid) ?></a></td>
                        <td class="text-capitalize"><a style="color:<?=$backgroundColorNew;?>!important" target="_BLANK" href="<?=base_url();?>/Menu/CompanyDetails/<?=$row->cid?>"><?= htmlspecialchars($row->compname) ?></a></td>
                        <td><?= htmlspecialchars($row->current_status) ?></td>
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
                            ?>
                        </td>
                          <td><?= htmlspecialchars($row->closing_timeline) ?></td>
                          <td><?= htmlspecialchars($row->proposal_require) ?></td>
                        <td><?php if($row->delete_request > 0){ ?> <span class="bg-danger p-1"> Deleted</span> <?php }else{ ?> <?php } ?></td>
                        <td> 
                          <a style="color:<?=$backgroundColorNew;?>!important" href="<?=base_url();?>Menu/ReplanedLog/<?=$row->task_id;  ?>" target="_BLANK"><?= htmlspecialchars($row->plan_count) ?></a>
                        </td>
                         <td>
                          <?php  
                          $getFindStar = $this->Menu_model->GetTotalStarFoundAfterCheck($row->task_id);
                           if ($getFindStar > 0){ ?>
                             <a class="bg-dark p-1" 
                                href="<?= base_url('Reports/TaskCheckingManagementStarRatingSingleDetails/' 
                                          . $row->task_id . '/' 
                                          . date('Y-m-d', strtotime($row->appointmentdatetime)) . '/' 
                                          . date('Y-m-d', strtotime($row->appointmentdatetime))) ?>" 
                                target="_blank">
                                  <?= htmlspecialchars($getFindStar) ?> ⭐
                              </a>
                          <?php  }else{ ?>
                            <span class='p-1 bg-warning'>Pending</span>
                          <?php } ?>
                          </td>
                      </tr>
                      <?php $i++; } ?>
                    </tbody>
                  </table>
                </div>
                <hr>
                <div class="text-center" style="background: linear-gradient(to right, #fec7c7, #ffffff); border-radius: 12px; box-shadow: 0 4px 8px rgba(0,0,0,0.1); padding: 20px;">
                  <h5 class="mb-0">📊 Negative Conversions Tasks (All Time History)</h5>
                  <center>
                    <small class="d-block" style="width: 70%;">
                    This section highlights tasks that resulted in or may lead to negative conversions. It helps you identify bottlenecks, analyze causes, and take corrective actions—enabling you to improve future outcomes, reduce risks, and refine your strategies for better conversion success.
                    </small>
                  </center>
                </div>
                <hr>
                <div class="table-responsive text-nowrap">
                  <table id="example5" class="table table-striped table-bordered" cellspacing="0" width="100%">
                    <thead class="thead-dark">
                    <tr>
                        <th class="text-capitalize">🔢 Sr. NO</th>
                        <th class="text-capitalize">👤 Task Username</th>
                        <th class="text-capitalize">🏷️ CID</th>
                        <th class="text-capitalize">🏢 Company Name</th>
                        <th class="text-capitalize">📌 Current Status</th>
                        <th class="text-capitalize">📝 Task Name</th>
                        <th class="text-capitalize">📊 Task Status</th>
                        <th class="text-capitalize">🎯 Action</th>
                        <th class="text-capitalize">🎯 Purpose</th>
                        <th class="text-capitalize">📅 Planned On Status</th>
                        <th class="text-capitalize">🔄 Change On Status</th>
                        <th class="text-capitalize">📆 Original Task Date</th>
                        <th class="text-capitalize">⏰ Appointment DateTime</th>
                        <th class="text-capitalize">🚀 Initiated DateTime</th>
                        <th class="text-capitalize">🔁 Updated DateTime</th>
                        <th class="text-capitalize">⏱️ Total Time Taken</th>
                        <th class="text-capitalize">💬 Late Remarks Message</th>
                        <th class="text-capitalize">✅ Task Approved By</th>
                        <th class="text-capitalize">🗒️ Remarks</th>
                        <th class="text-capitalize">🌟 Special Remarks</th>
                        <th class="text-capitalize">📉 Closing Timeline</th>
                        <th class="text-capitalize">🗑️ Deleted Task</th>
                        <th class="text-capitalize">🔁 Task RePlanned Count</th>
                        <th class="text-capitalize">⭐ Total Star</th>
                      </tr>
                    </thead>
                    <tbody>
                      <?php
                        $i = 1;
                        foreach ($cmpNegativeTaskDatas as $row) {
                          $pknextCFID = $row->nextCFID;
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
                          ?>
                      <tr style="background-color: <?php echo $backgroundColor; ?>;color:<?=$backgroundColorNew;?>!important" title="<?=$meeting_task_id?>">
                        <td><?= htmlspecialchars($i) ?></td>
                        <td class="text-capitalize"><?= htmlspecialchars($row->task_username) ?></td>
                        <td><a style="color:<?=$backgroundColorNew;?>!important" target="_BLANK" href="<?=base_url();?>/Menu/CompanyDetails/<?=$row->cid?>"><?= htmlspecialchars($row->cid) ?></a></td>
                        <td class="text-capitalize"><a style="color:<?=$backgroundColorNew;?>!important" target="_BLANK" href="<?=base_url();?>/Menu/CompanyDetails/<?=$row->cid?>"><?= htmlspecialchars($row->compname) ?></a></td>
                        <td><?= htmlspecialchars($row->current_status) ?></td>
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
                            ?>
                        </td>
                        <td><?= htmlspecialchars($row->closing_timeline) ?></td>
                        <td><?php if($row->delete_request > 0){ ?> <span class="bg-danger p-1"> Deleted</span> <?php }else{ ?> <?php } ?></td>
                        <td> 
                          <a style="color:<?=$backgroundColorNew;?>!important" href="<?=base_url();?>Menu/ReplanedLog/<?=$row->task_id;  ?>" target="_BLANK"><?= htmlspecialchars($row->plan_count) ?></a>
                        </td>
                         <td>
                          <?php  
                          $getFindStar = $this->Menu_model->GetTotalStarFoundAfterCheck($row->task_id);
                           if ($getFindStar > 0){ ?>
                             <a class="bg-dark p-1" 
                                href="<?= base_url('Reports/TaskCheckingManagementStarRatingSingleDetails/' 
                                          . $row->task_id . '/' 
                                          . date('Y-m-d', strtotime($row->appointmentdatetime)) . '/' 
                                          . date('Y-m-d', strtotime($row->appointmentdatetime))) ?>" 
                                target="_blank">
                                  <?= htmlspecialchars($getFindStar) ?> ⭐
                              </a>
                          <?php  }else{ ?>
                            <span class='p-1 bg-warning'>Pending</span>
                          <?php } ?>
                          </td>
                      </tr>
                      <?php $i++; } ?>
                    </tbody>
                  </table>
                </div>
                <hr>
                <div class="text-center" style="background: linear-gradient(to right, #d9fff8, #ffffff); border-radius: 12px; box-shadow: 0 4px 8px rgba(0,0,0,0.1); padding: 20px;">
                  <h5 class="mb-0">📊 Other Conversions Tasks (All Time History)</h5>
                  <center>
                    <small class="d-block" style="width: 70%;">
                    This section includes tasks that don't fall under clearly positive or negative conversions. It helps you review miscellaneous activities, explore uncertain outcomes, and assess potential opportunities or risks—supporting informed decision-making and continuous improvement in your conversion process.
                    </small>
                  </center>
                </div>
                <hr>
                <div class="table-responsive text-nowrap">
                  <table id="example6" class="table table-striped table-bordered" cellspacing="0" width="100%">
                    <thead class="thead-dark">
                    <tr>
                      <th class="text-capitalize">🔢 Sr. NO</th>
                      <th class="text-capitalize">👤 Task Username</th>
                      <th class="text-capitalize">🏷️ CID</th>
                      <th class="text-capitalize">🏢 Company Name</th>
                      <th class="text-capitalize">📌 Current Status</th>
                      <th class="text-capitalize">📝 Task Name</th>
                      <th class="text-capitalize">📊 Task Status</th>
                      <th class="text-capitalize">🎯 Action</th>
                      <th class="text-capitalize">🎯 Purpose</th>
                      <th class="text-capitalize">📅 Planned On Status</th>
                      <th class="text-capitalize">🔄 Change On Status</th>
                      <th class="text-capitalize">📆 Original Task Date</th>
                      <th class="text-capitalize">⏰ Appointment DateTime</th>
                      <th class="text-capitalize">🚀 Initiated DateTime</th>
                      <th class="text-capitalize">🔁 Updated DateTime</th>
                      <th class="text-capitalize">⏱️ Total Time Taken</th>
                      <th class="text-capitalize">💬 Late Remarks Message</th>
                      <th class="text-capitalize">✅ Task Approved By</th>
                      <th class="text-capitalize">🗒️ Remarks</th>
                      <th class="text-capitalize">🌟 Special Remarks</th>
                      <th class="text-capitalize">📉 Closing Timeline</th>
                      <th class="text-capitalize">🗑️ Deleted Task</th>
                      <th class="text-capitalize">🔁 Task RePlanned Count</th>
                      <th class="text-capitalize">⭐ Total Star</th>
                    </tr>
                    </thead>
                    <tbody>
                      <?php
                        $i = 1;
                        foreach ($cmpOtherTaskDatas as $row) {
                          $pknextCFID = $row->nextCFID;
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
                          ?>
                      <tr style="background-color: <?php echo $backgroundColor; ?>;color:<?=$backgroundColorNew;?>!important" title="<?=$meeting_task_id?>">
                        <td><?= htmlspecialchars($i) ?></td>
                        <td class="text-capitalize"><?= htmlspecialchars($row->task_username) ?></td>
                        <td><a style="color:<?=$backgroundColorNew;?>!important" target="_BLANK" href="<?=base_url();?>/Menu/CompanyDetails/<?=$row->cid?>"><?= htmlspecialchars($row->cid) ?></a></td>
                        <td class="text-capitalize"><a style="color:<?=$backgroundColorNew;?>!important" target="_BLANK" href="<?=base_url();?>/Menu/CompanyDetails/<?=$row->cid?>"><?= htmlspecialchars($row->compname) ?></a></td>
                        <td><?= htmlspecialchars($row->current_status) ?></td>
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
                            ?>
                        </td>
                        <td><?= htmlspecialchars($row->closing_timeline) ?></td>
                        <td><?php if($row->delete_request > 0){ ?> <span class="bg-danger p-1"> Deleted</span> <?php }else{ ?> <?php } ?></td>
                        <td> 
                          <a style="color:<?=$backgroundColorNew;?>!important" href="<?=base_url();?>Menu/ReplanedLog/<?=$row->task_id;  ?>" target="_BLANK"><?= htmlspecialchars($row->plan_count) ?></a>
                        </td>
                         <td>
                          <?php  
                          $getFindStar = $this->Menu_model->GetTotalStarFoundAfterCheck($row->task_id);
                           if ($getFindStar > 0){ ?>
                             <a class="bg-dark p-1" 
                                href="<?= base_url('Reports/TaskCheckingManagementStarRatingSingleDetails/' 
                                          . $row->task_id . '/' 
                                          . date('Y-m-d', strtotime($row->appointmentdatetime)) . '/' 
                                          . date('Y-m-d', strtotime($row->appointmentdatetime))) ?>" 
                                target="_blank">
                                  <?= htmlspecialchars($getFindStar) ?> ⭐
                              </a>
                          <?php  }else{ ?>
                            <span class='p-1 bg-warning'>Pending</span>
                          <?php } ?>
                          </td>
                      </tr>
                      <?php $i++; } ?>
                    </tbody>
                  </table>
                </div>
                <hr>
                <div class="text-center" style="background: linear-gradient(to right, #f6e7f3, #ffffff); border-radius: 12px; box-shadow: 0 4px 8px rgba(0,0,0,0.1); padding: 20px;">
                  <h5 class="mb-0">📊 Completed – Under Review</h5>
                  <center>
                    <small class="d-block" style="width: 70%;">
                    This section highlights tasks that have been marked as completed but are currently under review for validation or approval. It offers visibility into recent efforts, pending confirmations, and progress checkpoints—helping ensure that all activities meet expectations before being finalized and closed.
                    </small>
                  </center>
                </div>
                <hr>
                <div class="table-responsive text-nowrap">
                  <?php  
                    if (!empty($cmpReviewsDatas)) : ?>
                  <div class="table-responsive">
                    <table id="example3" class="table table-bordered table-striped table-hover">
                      <thead class="thead-dark">
                      <tr>
                        <th>🔢 #</th>
                        <th>📋 Review Type</th>
                        <th>📅 Review Date</th>
                        <th>🏢 Company Name</th>
                        <th>👤 Review By</th>
                        <th>📝 Task</th>
                        <th>⏰ Appointment Date</th>
                        <th>📊 Task Status</th>
                        <th>🔎 Review On Status</th>
                        <th>🎯 Expected Status</th>
                        <th>📆 Expected Date</th>
                        <th>📌 Current Status</th>
                        <th>💬 Remarks</th>
                        <th>🔍 View</th>
                      </tr>
                      </thead>
                      <tbody>
                        <?php foreach ($cmpReviewsDatas as $index => $row) :
                          $meeting_task_id = $row->review_id;
                          $r = rand(150, 255);
                          $g = rand(150, 255);
                          $b = rand(150, 255);
                          $backgroundColor = "rgba($r, $g, $b,.2)";
                          $hue = rand(0, 360); // Full color wheel
                          $saturation = rand(60, 100); // High saturation for rich colors
                          $lightness = rand(30, 45); // Low lightness for a deeper tone
                          $backgroundColorNew = "hsl($hue, $saturation%, $lightness%)";
                                    ?>
                        <tr style="background-color: <?php echo $backgroundColor; ?>;color:<?=$backgroundColorNew;?>!important" title="<?=$meeting_task_id?>">
                          <td><?= $index + 1 ?></td>
                          <td><?= htmlspecialchars($row->rtype) ?></td>
                          <td><?= htmlspecialchars($row->sdate) ?></td>
                          <td><?= htmlspecialchars($row->compname) ?></td>
                          <td><?= htmlspecialchars($row->review_by_name) ?></td>
                          <td><?= htmlspecialchars($row->review_time_task) ?></td>
                          <td><?= htmlspecialchars($row->appointmentdatetime) ?></td>
                          <td><?php if($row->nextCFID == 0){echo "<span class='bg-warning p-1'>Not Complete</span>";}else {echo "<span class='bg-success p-1'>Complete</span>";} ?></td>
                          <td><?= htmlspecialchars($row->review_time_compnay_status) ?></td>
                          <td><?= htmlspecialchars($row->review_time_expected_status) ?></td>
                          <td><?= date("d M Y", strtotime($row->expected_date)) ?></td>
                          <td><?= htmlspecialchars($row->current_status) ?></td>
                          <td><?= htmlspecialchars($row->remarks) ?></td>
                          <td><a target="_BLANK" class="btn btn-primary" href="<?=base_url()?>Menu/ReviewReportsByUser/<?=$row->rid ?>/<?=$row->review_id ?>">View Reports</a></td>
                        </tr>
                        <?php endforeach; ?>
                      </tbody>
                    </table>
                  </div>
                  <?php else : ?>
                  <p class="text-muted">No review data available.</p>
                  <?php endif; ?>
                </div>
                <br>
                <br>
                <br>
                <br>
                <br>
              </div>
            </div>
          </div>
      </div>
      <!-- /.card-body -->
    </div>
    <!-- /.card -->
    </div>
    <!-- /.col -->
    </div>
    <!-- /.row -->
    </div>
    <!-- /.container-fluid -->
    </section>


       <div class="modal fade" id="specialRemarksModal" tabindex="-1" role="dialog" aria-labelledby="specialRemarksTitle" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered" role="document">
                                <div class="modal-content" style="background: linear-gradient(to right, #f6e7f3, #ffffff); border-radius: 12px; box-shadow: 0 4px 8px rgba(0,0,0,0.1); padding: 20px;">
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






    <input type="hidden" value="<?=$current_status_id?>" id="current_status_id">
    </div>    
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
    <script type='text/javascript'>
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
      $("#example9").DataTable({
        "responsive": false, "lengthChange": false, "autoWidth": false,
        "buttons": ["csv", "excel", "pdf"]
      }).buttons().container().appendTo('#example9_wrapper .col-md-6:eq(0)');
      $("#example10").DataTable({
        "responsive": false, "lengthChange": false, "autoWidth": false,
        "buttons": ["csv", "excel", "pdf"]
      }).buttons().container().appendTo('#example10_wrapper .col-md-6:eq(0)');
      $("#example21").DataTable({
        "responsive": false, "lengthChange": false, "autoWidth": false,
        "buttons": ["csv", "excel", "pdf"]
      }).buttons().container().appendTo('#example21_wrapper .col-md-6:eq(0)');
      $("#example22").DataTable({
        "responsive": false, "lengthChange": false, "autoWidth": false,
        "buttons": ["csv", "excel", "pdf"]
      }).buttons().container().appendTo('#example22_wrapper .col-md-6:eq(0)');

    </script>
  </body>
</html>
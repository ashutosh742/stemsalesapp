<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title> <?=$targetUserDatas[0]->name?> | STEM APP | WebAPP</title>
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
            <style>
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
      <?php 
        // dd($targetUserDatas);
        
          
                  
          $targetDatas                    = $targetUserDatas[0];
          $bd_name                        = $targetDatas->name;
          $role_name                      = $targetDatas->role_name;
          $zone_name                      = $targetDatas->zone_name;
        
          $phoneno                        = $targetDatas->phoneno;
          $email                          = $targetDatas->email;
          $dob                            = $targetDatas->dob;
          $ucash                          = $targetDatas->ucash;
          $tar_type_id                    = $targetDatas->type_id;
          $tar_zone_id                    = $targetDatas->zone_id;
        
          $country                        = $targetDatas->country;
          $state                          = $targetDatas->state;
          $district                       = $targetDatas->district;
          $city                           = $targetDatas->city;
          $address                        = $targetDatas->address;
          $user_id                        = $targetDatas->user_id;
          $usercreateDate                 = $targetDatas->usercreateDate;
          $username                       = $targetDatas->username;
          $password                       = $targetDatas->password;
        
          $cluster_manager_name           = $targetDatas->cluster_manager_name;
          $admin_name                     = $targetDatas->admin_name;
          $super_admin_name               = $targetDatas->super_admin_name;
          $pst_name                       = $targetDatas->pst_name;
          $ash_nae_name                   = $targetDatas->ash_nae_name;
          $ash_w_name                     = $targetDatas->ash_w_name;
          $ash_s_name                     = $targetDatas->ash_s_name;
          $rm_east_name                   = $targetDatas->rm_east_name;
          $rm_north_name                  = $targetDatas->rm_north_name;
          $acm_name                       = $targetDatas->acm_name;
          $sales_co_name                  = $targetDatas->sales_co_name;
        
        
          $task_counts = [];
          $taskTtimeStatusDatas = [];
        
          $task_countsf = [];
          $taskTtimeStatusDatasf = [];
          
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
        
        
              $appointmentdatetimef = $task->appointmentdatetime;
        
              $ccfyearf = date("Y", strtotime($appointmentdatetimef));
              if($curefinancialyear == $ccfyearf){
        
                if (!isset($task_countsf[$task_name])) {
                  $task_countsf[$task_name] = 1;
                } else {
                    $task_countsf[$task_name]++;
                }
        
                if (!isset($taskTtimeStatusDatasf[$tasktimestatus])) {
                    $taskTtimeStatusDatasf[$tasktimestatus] = 1;
                } else {
                    $taskTtimeStatusDatasf[$tasktimestatus]++;
                }
              }
          }
        
         function sortArrayByKeyShort(array $array): array {
                ksort($array); // Sort by key alphabetically
                return $array; // Return sorted array
            }
        
            $task_counts            = sortArrayByKeyShort($task_counts); 
            $taskTtimeStatusDatas   = sortArrayByKeyShort($taskTtimeStatusDatas);     
        
            $task_countsf           = sortArrayByKeyShort($task_countsf); 
            $taskTtimeStatusDatasf  = sortArrayByKeyShort($taskTtimeStatusDatasf); 
        
            
        
        
          
          
                $total_funnels_status         = $totalFunnnels['total_funnels_status'];
                $total_funnels_old_category   = $totalFunnnels['total_funnels_old_category'];
                $total_funnels_new_category   = $totalFunnnels['total_funnels_new_category'];
                $total_funnels_assign         = $totalFunnnels['total_funnels_assign'];
                $total_funnel_by_user         = $totalFunnnels['total_funnel_by_user'];
                $total_funnels_partner        = $totalFunnnels['total_funnels_partner'];
                $total_funnels_partner_user   = $totalFunnnels['total_funnels_partner_user'];
                $total_current_quarter        = $totalFunnnels['total_current_quarter'];
        
                $totalClusterDatas                          = $totalFunnnels['totalClusterDatas'];
                $totalClusterBYBDatas                       = $totalFunnnels['totalClusterBYBDatas'];
                $totalClusterBYClusterNameBYBDDatas         = $totalFunnnels['totalClusterBYClusterNameBYBDDatas'];
                $totalClusterBYStatusDatas                  = $totalFunnnels['totalClusterBYStatusDatas'];
        
        
        
          ?>
      <style>
        .gradient-text,.gradient-text1{color:transparent;animation:5s infinite gradientAnimation}.gradient-text{background:linear-gradient(90deg,#ff8a00,#e52e71,#1e90ff);background-size:300% 300%;-webkit-background-clip:text;background-clip:text}.gradient-text1{background:linear-gradient(90deg,#e113aa,#1500ff,#1e90ff);background-size:300% 300%;-webkit-background-clip:text;background-clip:text}@keyframes gradientAnimation{0%,100%{background-position:0 50%}50%{background-position:100% 50%}}.card-height{min-height:100%}.card-height1{min-height:600px}.maparea{max-width:100%;border-radius:20px;padding:8px;background:linear-gradient(135deg,#e3f2fd,#fce4ec);border:3px solid transparent;background-clip:padding-box;position:relative;overflow:hidden;transition:transform .3s,box-shadow .3s;align-items:center;justify-content:center;display:flex;height:100%}.maparea:hover{box-shadow:0 12px 35px rgba(0,0,0,.25)}.custom-btn{width:130px;height:40px;color:#fff;border-radius:5px;padding:10px 25px;font-family:Lato,sans-serif;font-weight:500;background:0 0;cursor:pointer;transition:.3s;position:relative;display:inline-block;box-shadow:inset 2px 2px 2px 0 rgba(255,255,255,.5),7px 7px 20px 0 rgba(0,0,0,.1),4px 4px 5px 0 rgba(0,0,0,.1);outline:0}.btn-11{border:none;background:#212ffb;background:linear-gradient(0deg,#3e21fb 0,#4c5cea 100%);color:#fff;overflow:hidden}.btn-11:hover{text-decoration:none;color:#fff;opacity:.7}.btn-11:before{position:absolute;content:'';display:inline-block;top:-180px;left:0;width:30px;height:100%;background-color:#fff;animation:3s ease-in-out infinite shiny-btn1}.btn-11:active{box-shadow:4px 4px 6px 0 rgba(255,255,255,.3),-4px -4px 6px 0 rgba(116,125,136,.2),inset -4px -4px 6px 0 rgba(255,255,255,.2),inset 4px 4px 6px 0 rgba(0,0,0,.2)}@keyframes shiny-btn1{0%{transform:scale(0) rotate(45deg);opacity:0}80%{transform:scale(0) rotate(45deg);opacity:.5}81%{transform:scale(4) rotate(45deg);opacity:1}100%{transform:scale(50) rotate(45deg);opacity:0}}
        h4.mb-0 {
        color: #ff299d;
        }
        .rounded.p-3 {
        box-shadow: rgba(50, 50, 93, 0.25) 0px 2px 5px -1px, rgba(0, 0, 0, 0.3) 0px 1px 3px -1px;
        }
        .card.text-center.shadow.taskusercardData {
        height: 200px;
        }
        .card-card-data{
        align-items: center; justify-content: center; display: flex ; flex-direction: column;
        }
         .role-span {
            display: inline-block;
            padding: 5px 10px;
            border-radius: 15px;
            color: #fff;
            margin: 3px;
            font-size: 14px;
            font-weight: 500;
        }
        .card-body.text-center.p-2 {
    align-items: center;
    justify-content: center;
    display: flex;
    flex-direction: column;
}
.card.h-100.border-0.shadow-sm {
    background: linear-gradient(to right, #dfe9f3, #ffffff);
    border-radius: 12px;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
}
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
                    <h3 class="text-center m-3">👤 <span class="gradient-text"><?=$bd_name?></span> </h3>
                    <h4 class="text-center m-3">💼 <span class="gradient-text"> <?=$role_name;?></span> </h4>
                    <h5 class="text-center m-3">🌟✨ <span class="gradient-text"><?=$zone_name;?></span> ✨🌟</h5>
                    <h5 class="text-center m-3">🗓️  <span class="gradient-text"><?=$usercreateDate;?></span></h5>
                  </div>
                  <!-- /.card-header -->
                  <div class="card-body">
                    <hr>
                    <?php if($user['type_id'] == 1): ?>
                    <?php  // include 'MasterControlSetting.php'; ?>
                    <?php endif; ?>
                    <?php if($ctype_id !== 3){ ?> 
                    <hr>
                    <div class="row" style="background: azure; padding: 25px; border-radius: 20px;">
                      <div class="col-md-4">
                        <div class="card-height" style="background: linear-gradient(to right, #dfe9f3, #ffffff); border-radius: 12px; box-shadow: 0 4px 8px rgba(0,0,0,0.1); padding: 20px;">
                          <div class="text-center">
                            <h4 class="mb-0">👤 User Overview</h4>
                          </div>
                          <hr>
                          <p><strong>👤 BD Name:</strong> <?= !empty($bd_name) ? $bd_name : 'N/A' ?></p>
                          <p><strong>🎯 Role Name:</strong> <?= !empty($role_name) ? $role_name : 'N/A' ?></p>
                          <p><strong>📞 Phone No:</strong> <?= !empty($phoneno) ? $phoneno : 'N/A' ?></p>
                          <p><strong>📧 Email:</strong> <?= !empty($email) ? $email : 'N/A' ?></p>
                          <p><strong>🎂 Date of Birth:</strong> <?= !empty($dob) ? $dob : 'N/A' ?></p>
                          <!-- <p><strong>💰 User Cash:</strong> <?= !empty($ucash) ? $ucash : 'N/A' ?></p> -->
                          <p><strong>🗺 Zone Name:</strong> <?= !empty($zone_name) ? $zone_name : 'N/A' ?></p>
                          <p><strong>🌏 Country:</strong> <?= !empty($country) ? $country : 'N/A' ?></p>
                          <p><strong>🏛  State:</strong> <?= !empty($state) ? $state : 'N/A' ?></p>
                          <p><strong>📍 District:</strong> <?= !empty($district) ? $district : 'N/A' ?></p>
                          <p><strong>🏙  City:</strong> <?= !empty($city) ? $city : 'N/A' ?></p>
                          <p><strong>🏠 Address:</strong> <?= !empty($address) ? $address : 'N/A' ?></p>
                          <p><strong>🆔 User ID:</strong> <?= !empty($user_id) ? $user_id : 'N/A' ?></p>
                          <p><strong>📅 User Create Date:</strong> <?= !empty($usercreateDate) ? $usercreateDate : 'N/A' ?></p>
                          <p><strong>👤 Username:</strong> <?= !empty($username) ? $username : 'N/A' ?></p>
                          <?php if($user['user_id'] == $targetUID){ ?> 
                          <p><strong>🔑 Password:</strong> <?= !empty($password) ? $password : 'N/A' ?></p>
                          <?php }else{ ?>
                          <p><strong>🔑 Password:</strong> ***********</p>
                          <?php } ?>
                          <hr>
                        </div>
                      </div>
                      <div class="col-md-8">
                        <div class="card-height" style="background: linear-gradient(to right, #e0f7fa, #ffffff); border-radius: 12px; box-shadow: 0 4px 8px rgba(0,0,0,0.1); padding: 20px;">
                          <div class="text-center">
                            <h4 class="mb-0">🏢 Organization Hierarchy</h4>
                          </div>
                          <style>
                            .tree { padding: 20px; font-family: Arial, sans-serif; } .tree ul { position: relative; padding-left: 1em; list-style: none; } .tree ul ul::before { content: ''; position: absolute; top: 0; left: 0.5em; border-left: 2px solid #ccc; bottom: 0; } .tree li { margin: 0; padding: 0 0 0 1.5em; position: relative; } .tree li::before { content: ''; position: absolute; top: 1em; left: 0; width: 1em; height: 0; border-top: 2px solid #ccc; } .tree .role { background: #f9f9f9; padding: 6px 10px; border-radius: 6px; border: 1px solid #ddd; display: inline-block; } .tree .name { font-weight: normal; color: #555; } .tree .emoji { margin-right: 4px; }
                            .card-body-proposal {
                              flex-direction: column;
                              justify-content: center;
                              align-items: center;
                              justify-content: center;
                              display: flex;
                          }
                          </style>
                          <hr>
                          <div class="tree">
                            <ul>
                              <li>
                                <!-- Super Admin -->
                                <span class="role"><span class="emoji">🛡</span> <strong>Super Admin:</strong> 
                                <?php if (in_array($tar_type_id, [1])){?>
                                <span class="name"><?= !empty($bd_name) ? $bd_name : 'N/A' ?></span>
                                <?php }else{ ?>
                                <span class="name"><?= !empty($super_admin_name) ? $super_admin_name : 'N/A' ?></span>
                                <?php } ?>
                                </span>
                                <?php 
                                  if (in_array($tar_type_id, [2,19,20,21,22,23,4,15,13,24,3])): ?>
                                <ul>
                                  <li>
                                    <!-- Admin -->
                                    <span class="role"><span class="emoji">🏢</span> <strong>Admin:</strong> 
                                    <?php if (in_array($tar_type_id, [2])){?>
                                    <span class="name"><?= !empty($bd_name) ? $bd_name : 'N/A' ?></span>
                                    <?php }else{ ?>
                                    <span class="name"><?= !empty($admin_name) ? $admin_name : 'N/A' ?></span>
                                    <?php } ?>
                                    </span>
                                    <?php if (in_array($tar_type_id, [19,20,21,22,23,4,15,13,24,3])): ?>
                                    <ul>
                                      <?php if(in_array($tar_zone_id,[3,8])){ ?> 
                                      <li><span class="role"><span class="emoji">🧭</span> <strong>Assistant Sales Head (NAE):</strong> 
                                        <?php if (in_array($tar_type_id, [19])){?>
                                        <span class="name"><?= !empty($bd_name) ? $bd_name : 'N/A' ?></span>
                                        <?php }else{ ?>
                                        <span class="name"><?= !empty($ash_nae_name) ? $ash_nae_name : 'N/A' ?></span>
                                        <?php } ?>
                                        </span>
                                      </li>
                                      <?php }else if(in_array($tar_zone_id,[5])){ ?> 
                                      <li><span class="role"><span class="emoji">🌊</span> <strong>Assistant Sales Head (W):</strong> 
                                        <?php if (in_array($tar_type_id, [20])){?>
                                        <span class="name"><?= !empty($bd_name) ? $bd_name : 'N/A' ?></span>
                                        <?php }else{ ?>
                                        <span class="name"><?= !empty($ash_w_name) ? $ash_w_name : 'N/A' ?></span>
                                        <?php } ?>
                                        </span>
                                      </li>
                                      <?php }else if(in_array($tar_zone_id,[4])){ ?> 
                                      <span class="role"><span class="emoji">☀</span> <strong>Assistant Sales Head (S):</strong> 
                                      <?php if (in_array($tar_type_id, [21])){?>
                                      <span class="name"><?= !empty($bd_name) ? $bd_name : 'N/A' ?></span>
                                      <?php }else{ ?>
                                      <span class="name"><?= !empty($rm_east_ash_s_namename) ? $ash_s_name : 'N/A' ?></span>
                                      <?php } ?>
                                      </span>
                                      <?php } ?>
                                      <li>
                                        <?php if (in_array($tar_type_id, [23,22,4,15,13,24,3])): ?>
                                        <ul>
                                          <?php if(in_array($tar_zone_id,[8])){ ?> 
                                          <li><span class="role"><span class="emoji">🌅</span> <strong>RM East:</strong> 
                                            <?php if (in_array($tar_type_id, [22])){?>
                                            <span class="name"><?= !empty($bd_name) ? $bd_name : 'N/A' ?></span>
                                            <?php }else{ ?>
                                            <span class="name"><?= !empty($rm_east_name) ? $rm_east_name : 'N/A' ?></span>
                                            <?php } ?>
                                            </span>
                                          </li>
                                          <?php } if(in_array($tar_zone_id,[3])){ ?> 
                                          <li><span class="role"><span class="emoji">❄</span> <strong>RM North:</strong> 
                                            <?php if (in_array($tar_type_id, [23])){?>
                                            <span class="name"><?= !empty($bd_name) ? $bd_name : 'N/A' ?></span>
                                            <?php }else{ ?>
                                            <span class="name"><?= !empty($rm_north_name) ? $rm_north_name : 'N/A' ?></span>
                                            <?php } ?>
                                            </span>
                                          </li>
                                          <?php } ?> 
                                          <?php if (in_array($tar_type_id, [4,15,13,24,3])): ?>
                                          <li>
                                            <span class="role"><span class="emoji">📌</span> <strong>Sales Coordinator:</strong> 
                                            <?php if (in_array($tar_type_id, [15])){?>
                                            <span class="name"><?= !empty($bd_name) ? $bd_name : 'N/A' ?></span>
                                            <?php }else{ ?>
                                            <span class="name"><?= !empty($sales_co_name) ? $sales_co_name : 'N/A' ?></span>
                                            <?php } ?>
                                            </span>
                                            <?php if (in_array($tar_type_id, [4,13,24,3])): ?>
                                            <ul>
                                              <li>
                                                <span class="role"><span class="emoji">📌</span> <strong>PST:</strong> 
                                                <?php if (in_array($tar_type_id, [4])){?>
                                                <span class="name"><?= !empty($bd_name) ? $bd_name : 'N/A' ?></span>
                                                <?php }else{ ?>
                                                <span class="name"><?= !empty($pst_name) ? $pst_name : 'N/A' ?></span>
                                                <?php } ?>
                                                </span>
                                                <?php if (in_array($tar_type_id, [13,24,3])): ?>
                                                <ul>
                                                  <li>
                                                    <span class="role"><span class="emoji">👔</span> <strong>Cluster Manager:</strong> 
                                                    <?php if (in_array($tar_type_id, [13])){?>
                                                    <span class="name"><?= !empty($bd_name) ? $bd_name : 'N/A' ?></span>
                                                    <?php }else{ ?>
                                                    <span class="name"><?= !empty($cluster_manager_name) ? $cluster_manager_name : 'N/A' ?></span>
                                                    <?php } ?>
                                                    </span>
                                                    <?php if (in_array($tar_type_id, [24,3])): ?>
                                                    <ul>
                                                      <li>
                                                        <span class="role"><span class="emoji">📋</span> <strong>Assistant Cluster Manager:</strong> 
                                                        <?php if (in_array($tar_type_id, [24])){?>
                                                        <span class="name"><?= !empty($bd_name) ? $bd_name : 'N/A' ?></span>
                                                        <?php }else{ ?>
                                                        <span class="name"><?= !empty($acm_name) ? $acm_name : 'N/A' ?></span>
                                                        <?php } ?>
                                                        </span>
                                                        <?php if ($tar_type_id == 3): ?>
                                                        <ul>
                                                          <li>
                                                            <span class="role"><span class="emoji">📋</span> <strong>BD Name:</strong> <span class="name"><?= !empty($bd_name) ? $bd_name : 'N/A' ?></span></span>
                                                          </li>
                                                        </ul>
                                                        <?php endif; ?>
                                                      </li>
                                                    </ul>
                                                    <?php endif; ?>
                                                  </li>
                                                </ul>
                                                <?php endif; ?>
                                              </li>
                                            </ul>
                                            <?php endif; ?>
                                          </li>
                                          <?php endif; ?>
                                        </ul>
                                        <?php endif; ?>
                                      </li>
                                    </ul>
                                    <?php endif; ?>
                                  </li>
                                </ul>
                                <?php endif; ?>
                              </li>
                            </ul>
                          </div>
                          <hr>




                          
           <?php 
$roleColors = [
    "SuperAdmin"                 => "#e63946", // Rich red
    "Admin"                      => "#f77f00", // Vibrant orange
    "AdminS"                     => "#ffd166", // Soft golden
    "RM East"                    => "#118ab2", // Deep sky blue
    "Assistant Sales Head (NAE)" => "#ef476f", // Warm pink-red
    "Assistant Sales Head (W)"   => "#9d4edd", // Royal purple
    "Assistant Sales Head (S)"   => "#06d6a0", // Mint green
    "Sales Coordinator"          => "#ffb703", // Golden orange
    "PST"                        => "#457b9d", // Muted navy blue
    "BDPST"                      => "#ffafcc", // Pastel pink
    "Cluster Manager"            => "#80ed99", // Fresh green
    "Assistant Cluster Manager"  => "#ffd670", // Warm yellow
    "Sales Person"               => "#e132e7", // Light blue
    "Project Coordinator"        => "#f4a261", // Warm tan-orange
    "Data Analysis"              => "#72efdd", // Soft aqua
];
?>

             
             
            <div class="row">
              <?php 
             foreach ($groupedTeamDatas as $role => $members): 
             
              if($role == 'Sales Person'){
                $specifycolumn = 'col-md-12';
              }else{
                $specifycolumn = 'col-md-6';
              }
             
             ?>
            <div class="<?=$specifycolumn?> mb-4">
                <div class="card shadow" style="background: linear-gradient(to right, #dfe9f3, #ffffff);">
                    <div class="card-header text-white text-center" style="background-color: <?= $roleColors[$role] ?? '#333'; ?>;">
                        <strong><?= htmlspecialchars($role) ?></strong>
                    </div>
                    <div class="card-body">
                        <?php foreach ($members as $id => $name): ?>
                          <a target="_BLANK" href="<?=base_url()?>Reports/UserDetails/<?=$id?>">
                            <span class="role-span" style="background-color: <?= $roleColors[$role] ?? '#333'; ?>;">
                                <?= htmlspecialchars($name) ?>
                            </span>
                          </a>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
            </div>


                        </div>
                      </div>
                    




                    <div class="col-md-12 mt-2">
                      <div class="p-1" style="background: linear-gradient(to right, #e0f7fa, #ffffff); border-radius: 12px; box-shadow: 0 4px 8px rgba(0,0,0,0.1); padding: 20px;">
                    <h3 class="text-center" style="color: #ff299d;">Total Meeting</h3>
                    <h6 class="text-center" style="color: #ff299d;"><?= $curFinancialStartDate?> - to - <?= $curTillDate;?></h6>
                  </div>
                  <hr>
<?php 

$sdate = $curFinancialStartDate;
$edate = $curTillDate;
?>
    <div class="row">
      <?php
      $i = 1;
      foreach ($meetingsUserByDatas as $row) {
        $meeting_user_id = $row->user_id;
        $r = rand(150, 255);
        $g = rand(150, 255);
        $b = rand(150, 255);
        $backgroundColor = "rgba($r, $g, $b,.2)";
        $hue = rand(0, 360);
        $saturation = rand(60, 100);
        $lightness = rand(30, 45);
        $backgroundColorNew = "hsl($hue, $saturation%, $lightness%)";
      ?>
      <div class="colmd6">
        <div class="card" style="border-left: 5px solid <?=$backgroundColorNew;?>; box-shadow: 0 4px 8px rgba(0,0,0,0.1);">
          <div class="card-body123">
            <div class="row">
              <!-- Total Meetings -->
              <div class="col-md-3 mb-2">
                <div class="card h-100 border-0 shadow-sm">
                  <div class="card-body text-center p-2">
                    <h6 class="card-subtitle mb-2 text-muted">🔢 Total Meetings</h6>
                    <a href="<?=base_url()?>Reports/MeetingsDatas/total_plan_meetings/<?=$targetUID?>/<?=$sdate?>/<?=$edate?>/userwise" class="card-link" style="color: <?=$backgroundColorNew;?>;">
                      <h4 class="card-title mb-0"><?= $row->total_plan_meetings; ?></h4>
                    </a>
                  </div>
                </div>
              </div>
              <!-- Complete Meetings -->
              <div class="col-md-3 mb-2">
                <div class="card h-100 border-0 shadow-sm">
                  <div class="card-body text-center p-2">
                    <h6 class="card-subtitle mb-2 text-muted">✅ Complete Meetings</h6>
                    <a href="<?=base_url()?>Reports/MeetingsDatas/complete_meetings/<?=$targetUID?>/<?=$sdate?>/<?=$edate?>/userwise" class="card-link" style="color: <?=$backgroundColorNew;?>;">
                      <h4 class="card-title mb-0"><?= $row->complete_meetings; ?></h4>
                    </a>
                  </div>
                </div>
              </div>
              <!-- Not Complete Meetings -->
              <div class="col-md-3 mb-2">
                <div class="card h-100 border-0 shadow-sm">
                  <div class="card-body text-center p-2">
                    <h6 class="card-subtitle mb-2 text-muted">❌ Not Complete Meetings</h6>
                    <a href="<?=base_url()?>Reports/MeetingsDatas/not_complete_meetings/<?=$targetUID?>/<?=$sdate?>/<?=$edate?>/userwise" class="card-link" style="color: <?=$backgroundColorNew;?>;">
                      <h4 class="card-title mb-0"><?= $row->not_complete_meetings; ?></h4>
                    </a>
                  </div>
                </div>
              </div>
              <!-- Total Scheduled Meetings -->
              <div class="col-md-3 mb-2">
                <div class="card h-100 border-0 shadow-sm">
                  <div class="card-body text-center p-2">
                    <h6 class="card-subtitle mb-2 text-muted">📅 Total Scheduled Meetings</h6>
                    <a href="<?=base_url()?>Reports/MeetingsDatas/total_scheduled_meetings/<?=$targetUID?>/<?=$sdate?>/<?=$edate?>/userwise" class="card-link" style="color: <?=$backgroundColorNew;?>;">
                      <h4 class="card-title mb-0"><?= $row->total_sheduled_meetings; ?></h4>
                    </a>
                  </div>
                </div>
              </div>
              <!-- Total Complete Scheduled Meetings -->
              <div class="col-md-3 mb-2">
                <div class="card h-100 border-0 shadow-sm">
                  <div class="card-body text-center p-2">
                    <h6 class="card-subtitle mb-2 text-muted">✅ Total Complete Scheduled Meetings</h6>
                    <a href="<?=base_url()?>Reports/MeetingsDatas/total_complete_scheduled_meetings/<?=$targetUID?>/<?=$sdate?>/<?=$edate?>/userwise" class="card-link" style="color: <?=$backgroundColorNew;?>;">
                      <h4 class="card-title mb-0"><?= $row->total_complete_sheduled_meetings; ?></h4>
                    </a>
                  </div>
                </div>
              </div>
              <!-- Not Complete Scheduled Meetings -->
              <div class="col-md-3 mb-2">
                <div class="card h-100 border-0 shadow-sm">
                  <div class="card-body text-center p-2">
                    <h6 class="card-subtitle mb-2 text-muted">❌ Not Complete Scheduled Meetings</h6>
                    <a href="<?=base_url()?>Reports/MeetingsDatas/not_complete_scheduled_meetings/<?=$targetUID?>/<?=$sdate?>/<?=$edate?>/userwise" class="card-link" style="color: <?=$backgroundColorNew;?>;">
                      <h4 class="card-title mb-0"><?= $row->not_complete_sheduled_meetings; ?></h4>
                    </a>
                  </div>
                </div>
              </div>
              <!-- Total Barg Meetings -->
              <div class="col-md-3 mb-2">
                <div class="card h-100 border-0 shadow-sm">
                  <div class="card-body text-center p-2">
                    <h6 class="card-subtitle mb-2 text-muted">📌 Total Barg Meetings</h6>
                    <a href="<?=base_url()?>Reports/MeetingsDatas/total_barg_meetings/<?=$targetUID?>/<?=$sdate?>/<?=$edate?>/userwise" class="card-link" style="color: <?=$backgroundColorNew;?>;">
                      <h4 class="card-title mb-0"><?= $row->total_barg_meetings; ?></h4>
                    </a>
                  </div>
                </div>
              </div>
              <!-- Total Complete Barg Meetings -->
              <div class="col-md-3 mb-2">
                <div class="card h-100 border-0 shadow-sm">
                  <div class="card-body text-center p-2">
                    <h6 class="card-subtitle mb-2 text-muted">✅ Total Complete Barg Meetings</h6>
                    <a href="<?=base_url()?>Reports/MeetingsDatas/total_complete_barg_meetings/<?=$targetUID?>/<?=$sdate?>/<?=$edate?>/userwise" class="card-link" style="color: <?=$backgroundColorNew;?>;">
                      <h4 class="card-title mb-0"><?= $row->total_complete_barg_meetings; ?></h4>
                    </a>
                  </div>
                </div>
              </div>
              <!-- Not Complete Barg Meetings -->
              <div class="col-md-3 mb-2">
                <div class="card h-100 border-0 shadow-sm">
                  <div class="card-body text-center p-2">
                    <h6 class="card-subtitle mb-2 text-muted">❌ Not Complete Barg Meetings</h6>
                    <a href="<?=base_url()?>Reports/MeetingsDatas/not_complete_barg_meetings/<?=$targetUID?>/<?=$sdate?>/<?=$edate?>/userwise" class="card-link" style="color: <?=$backgroundColorNew;?>;">
                      <h4 class="card-title mb-0"><?= $row->not_complete_barg_meetings; ?></h4>
                    </a>
                  </div>
                </div>
              </div>
              <!-- Total RP Meetings -->
              <div class="col-md-3 mb-2">
                <div class="card h-100 border-0 shadow-sm">
                  <div class="card-body text-center p-2">
                    <h6 class="card-subtitle mb-2 text-muted">🕒 Total RP Meetings</h6>
                    <a href="<?=base_url()?>Reports/MeetingsDatas/total_RP_meetings/<?=$targetUID?>/<?=$sdate?>/<?=$edate?>/userwise" class="card-link" style="color: <?=$backgroundColorNew;?>;">
                      <h4 class="card-title mb-0"><?= $row->total_RP_meetings; ?></h4>
                    </a>
                  </div>
                </div>
              </div>
              <!-- Total NO RP Meetings -->
              <div class="col-md-3 mb-2">
                <div class="card h-100 border-0 shadow-sm">
                  <div class="card-body text-center p-2">
                    <h6 class="card-subtitle mb-2 text-muted">🚫 Total NO RP Meetings</h6>
                    <a href="<?=base_url()?>Reports/MeetingsDatas/total_NO_RP_meetings/<?=$targetUID?>/<?=$sdate?>/<?=$edate?>/userwise" class="card-link" style="color: <?=$backgroundColorNew;?>;">
                      <h4 class="card-title mb-0"><?= $row->total_NO_RP_meetings; ?></h4>
                    </a>
                  </div>
                </div>
              </div>
              <!-- Total Only Got Details Meetings -->
              <div class="col-md-3 mb-2">
                <div class="card h-100 border-0 shadow-sm">
                  <div class="card-body text-center p-2">
                    <h6 class="card-subtitle mb-2 text-muted">📇 Total Only Got Details Meetings</h6>
                    <a href="<?=base_url()?>Reports/MeetingsDatas/total_Only_Got_details_meetings/<?=$targetUID?>/<?=$sdate?>/<?=$edate?>/userwise" class="card-link" style="color: <?=$backgroundColorNew;?>;">
                      <h4 class="card-title mb-0"><?= $row->total_Only_Got_details_meetings; ?></h4>
                    </a>
                  </div>
                </div>
              </div>
              <!-- Total Scheduled RP Meetings -->
              <div class="col-md-3 mb-2">
                <div class="card h-100 border-0 shadow-sm">
                  <div class="card-body text-center p-2">
                    <h6 class="card-subtitle mb-2 text-muted">📅 Total Scheduled RP Meetings</h6>
                    <a href="<?=base_url()?>Reports/MeetingsDatas/total_Scheduled_RP_meetings/<?=$targetUID?>/<?=$sdate?>/<?=$edate?>/userwise" class="card-link" style="color: <?=$backgroundColorNew;?>;">
                      <h4 class="card-title mb-0"><?= $row->total_Sheduled_RP_meetings; ?></h4>
                    </a>
                  </div>
                </div>
              </div>
              <!-- Total Scheduled NO RP Meetings -->
              <div class="col-md-3 mb-2">
                <div class="card h-100 border-0 shadow-sm">
                  <div class="card-body text-center p-2">
                    <h6 class="card-subtitle mb-2 text-muted">📅 Total Scheduled NO RP Meetings</h6>
                    <a href="<?=base_url()?>Reports/MeetingsDatas/total_Scheduled_NO_RP_meetings/<?=$targetUID?>/<?=$sdate?>/<?=$edate?>/userwise" class="card-link" style="color: <?=$backgroundColorNew;?>;">
                      <h4 class="card-title mb-0"><?= $row->total_Sheduled_NO_RP_meetings; ?></h4>
                    </a>
                  </div>
                </div>
              </div>
              <!-- Total Scheduled Only Got Details Meetings -->
              <div class="col-md-3 mb-2">
                <div class="card h-100 border-0 shadow-sm">
                  <div class="card-body text-center p-2">
                    <h6 class="card-subtitle mb-2 text-muted">📅 Total Scheduled Only Got Details Meetings</h6>
                    <a href="<?=base_url()?>Reports/MeetingsDatas/total_Scheduled_Only_Got_details_meetings/<?=$targetUID?>/<?=$sdate?>/<?=$edate?>/userwise" class="card-link" style="color: <?=$backgroundColorNew;?>;">
                      <h4 class="card-title mb-0"><?= $row->total_Scheduled_Only_Got_details_meetings; ?></h4>
                    </a>
                  </div>
                </div>
              </div>
              <!-- Total Barge RP Meetings -->
              <div class="col-md-3 mb-2">
                <div class="card h-100 border-0 shadow-sm">
                  <div class="card-body text-center p-2">
                    <h6 class="card-subtitle mb-2 text-muted">📌 Total Barge RP Meetings</h6>
                    <a href="<?=base_url()?>Reports/MeetingsDatas/total_Barge_RP_meetings/<?=$targetUID?>/<?=$sdate?>/<?=$edate?>/userwise" class="card-link" style="color: <?=$backgroundColorNew;?>;">
                      <h4 class="card-title mb-0"><?= $row->total_Barge_RP_meetings; ?></h4>
                    </a>
                  </div>
                </div>
              </div>
              <!-- Total Barge NO RP Meetings -->
              <div class="col-md-3 mb-2">
                <div class="card h-100 border-0 shadow-sm">
                  <div class="card-body text-center p-2">
                    <h6 class="card-subtitle mb-2 text-muted">📌 Total Barge NO RP Meetings</h6>
                    <a href="<?=base_url()?>Reports/MeetingsDatas/total_Barge_NO_RP_meetings/<?=$targetUID?>/<?=$sdate?>/<?=$edate?>/userwise" class="card-link" style="color: <?=$backgroundColorNew;?>;">
                      <h4 class="card-title mb-0"><?= $row->total_Barge_NO_RP_meetings; ?></h4>
                    </a>
                  </div>
                </div>
              </div>
              <!-- Total Barge Only Got Details Meetings -->
              <div class="col-md-3 mb-2">
                <div class="card h-100 border-0 shadow-sm">
                  <div class="card-body text-center p-2">
                    <h6 class="card-subtitle mb-2 text-muted">📌 Total Barge Only Got Details Meetings</h6>
                    <a href="<?=base_url()?>Reports/MeetingsDatas/total_Barge_Only_Got_details_meetings/<?=$targetUID?>/<?=$sdate?>/<?=$edate?>/userwise" class="card-link" style="color: <?=$backgroundColorNew;?>;">
                      <h4 class="card-title mb-0"><?= $row->total_Barge_Only_Got_details_meetings; ?></h4>
                    </a>
                  </div>
                </div>
              </div>
              <!-- Total Potential Meetings -->
              <div class="col-md-3 mb-2">
                <div class="card h-100 border-0 shadow-sm">
                  <div class="card-body text-center p-2">
                    <h6 class="card-subtitle mb-2 text-muted">🌟 Total Potential Meetings</h6>
                    <a href="<?=base_url()?>Reports/MeetingsDatas/total_potential_meetings/<?=$targetUID?>/<?=$sdate?>/<?=$edate?>/userwise" class="card-link" style="color: <?=$backgroundColorNew;?>;">
                      <h4 class="card-title mb-0"><?= $row->total_potential_meetings; ?></h4>
                    </a>
                  </div>
                </div>
              </div>
              <!-- Total Non Potential Meetings -->
              <div class="col-md-3 mb-2">
                <div class="card h-100 border-0 shadow-sm">
                  <div class="card-body text-center p-2">
                    <h6 class="card-subtitle mb-2 text-muted">🔍 Total Non Potential Meetings</h6>
                    <a href="<?=base_url()?>Reports/MeetingsDatas/total_non_potential_meetings/<?=$targetUID?>/<?=$sdate?>/<?=$edate?>/userwise" class="card-link" style="color: <?=$backgroundColorNew;?>;">
                      <h4 class="card-title mb-0"><?= $row->total_non_potential_meetings; ?></h4>
                    </a>
                  </div>
                </div>
              </div>
              <!-- Total Topspender Meetings -->
              <div class="col-md-3 mb-2">
                <div class="card h-100 border-0 shadow-sm">
                  <div class="card-body text-center p-2">
                    <h6 class="card-subtitle mb-2 text-muted">💰 Total Topspender Meetings</h6>
                    <a href="<?=base_url()?>Reports/MeetingsDatas/total_topspender_meetings/<?=$targetUID?>/<?=$sdate?>/<?=$edate?>/userwise" class="card-link" style="color: <?=$backgroundColorNew;?>;">
                      <h4 class="card-title mb-0"><?= $row->total_topspender_meetings; ?></h4>
                    </a>
                  </div>
                </div>
              </div>
              <!-- Total Upsell Client Meetings -->
              <div class="col-md-3 mb-2">
                <div class="card h-100 border-0 shadow-sm">
                  <div class="card-body text-center p-2">
                    <h6 class="card-subtitle mb-2 text-muted">📈 Total Upsell Client Meetings</h6>
                    <a href="<?=base_url()?>Reports/MeetingsDatas/total_upsell_client_meetings/<?=$targetUID?>/<?=$sdate?>/<?=$edate?>/userwise" class="card-link" style="color: <?=$backgroundColorNew;?>;">
                      <h4 class="card-title mb-0"><?= $row->total_upsell_client_meetings; ?></h4>
                    </a>
                  </div>
                </div>
              </div>
              <!-- Total Focus Funnel Meetings -->
              <div class="col-md-3 mb-2">
                <div class="card h-100 border-0 shadow-sm">
                  <div class="card-body text-center p-2">
                    <h6 class="card-subtitle mb-2 text-muted">🎯 Total Focus Funnel Meetings</h6>
                    <a href="<?=base_url()?>Reports/MeetingsDatas/total_focus_funnel_meetings/<?=$targetUID?>/<?=$sdate?>/<?=$edate?>/userwise" class="card-link" style="color: <?=$backgroundColorNew;?>;">
                      <h4 class="card-title mb-0"><?= $row->total_focus_funnel_meetings; ?></h4>
                    </a>
                  </div>
                </div>
              </div>
              <!-- Total Keycompany Meetings -->
              <div class="col-md-3 mb-2">
                <div class="card h-100 border-0 shadow-sm">
                  <div class="card-body text-center p-2">
                    <h6 class="card-subtitle mb-2 text-muted">🏢 Total Keycompany Meetings</h6>
                    <a href="<?=base_url()?>Reports/MeetingsDatas/total_keycompany_meetings/<?=$targetUID?>/<?=$sdate?>/<?=$edate?>/userwise" class="card-link" style="color: <?=$backgroundColorNew;?>;">
                      <h4 class="card-title mb-0"><?= $row->total_keycompany_meetings; ?></h4>
                    </a>
                  </div>
                </div>
              </div>
              <!-- Total Pkclient Meetings -->
              <div class="col-md-3 mb-2">
                <div class="card h-100 border-0 shadow-sm">
                  <div class="card-body text-center p-2">
                    <h6 class="card-subtitle mb-2 text-muted">➕ Total Pkclient Meetings</h6>
                    <a href="<?=base_url()?>Reports/MeetingsDatas/total_pkclient_meetings/<?=$targetUID?>/<?=$sdate?>/<?=$edate?>/userwise" class="card-link" style="color: <?=$backgroundColorNew;?>;">
                      <h4 class="card-title mb-0"><?= $row->total_pkclient_meetings; ?></h4>
                    </a>
                  </div>
                </div>
              </div>
              <!-- Total Priorityc Meetings -->
              <div class="col-md-3 mb-2">
                <div class="card h-100 border-0 shadow-sm">
                  <div class="card-body text-center p-2">
                    <h6 class="card-subtitle mb-2 text-muted">📞 Total Priorityc Meetings</h6>
                    <a href="<?=base_url()?>Reports/MeetingsDatas/total_priorityc_meetings/<?=$targetUID?>/<?=$sdate?>/<?=$edate?>/userwise" class="card-link" style="color: <?=$backgroundColorNew;?>;">
                      <h4 class="card-title mb-0"><?= $row->total_priorityc_meetings; ?></h4>
                    </a>
                  </div>
                </div>
              </div>
              <!-- Total Fresh Meetings -->
              <div class="col-md-3 mb-2">
                <div class="card h-100 border-0 shadow-sm">
                  <div class="card-body text-center p-2">
                    <h6 class="card-subtitle mb-2 text-muted">🆕 Total Fresh Meetings</h6>
                    <a href="<?=base_url()?>Reports/MeetingsDatas/total_fresh_meetings/<?=$targetUID?>/<?=$sdate?>/<?=$edate?>/userwise" class="card-link" style="color: <?=$backgroundColorNew;?>;">
                      <h4 class="card-title mb-0"><?= $row->total_fresh_meetings; ?></h4>
                    </a>
                  </div>
                </div>
              </div>
              <!-- Total Re Meetings -->
              <div class="col-md-3 mb-2">
                <div class="card h-100 border-0 shadow-sm">
                  <div class="card-body text-center p-2">
                    <h6 class="card-subtitle mb-2 text-muted">🔁 Total Re Meetings</h6>
                    <a href="<?=base_url()?>Reports/MeetingsDatas/total_re_meetings/<?=$targetUID?>/<?=$sdate?>/<?=$edate?>/userwise" class="card-link" style="color: <?=$backgroundColorNew;?>;">
                      <h4 class="card-title mb-0"><?= $row->total_re_meetings; ?></h4>
                    </a>
                  </div>
                </div>
              </div>
              <!-- Total Write Mom RP Meetings -->
              <div class="col-md-3 mb-2">
                <div class="card h-100 border-0 shadow-sm">
                  <div class="card-body text-center p-2">
                    <h6 class="card-subtitle mb-2 text-muted">📝 Total Write Mom RP Meetings</h6>
                    <a href="<?=base_url()?>Reports/MeetingsDatas/total_write_mom_rp_meetings/<?=$targetUID?>/<?=$sdate?>/<?=$edate?>/userwise" class="card-link" style="color: <?=$backgroundColorNew;?>;">
                      <h4 class="card-title mb-0"><?= $row->total_write_mom_rp_meetings; ?></h4>
                    </a>
                  </div>
                </div>
              </div>
              <!-- Total Pending For Write Mom RP Meeting -->
              <div class="col-md-3 mb-2">
                <div class="card h-100 border-0 shadow-sm">
                  <div class="card-body text-center p-2">
                    <h6 class="card-subtitle mb-2 text-muted">⏳ Total Pending For Write Mom RP Meeting</h6>
                    <a href="<?=base_url()?>Reports/MeetingsDatas/total_pending_for_write_mom_rp_meeting/<?=$targetUID?>/<?=$sdate?>/<?=$edate?>/userwise" class="card-link" style="color: <?=$backgroundColorNew;?>;">
                      <h4 class="card-title mb-0"><?= $row->total_pending_for_write_mom_rp_meeting; ?></h4>
                    </a>
                  </div>
                </div>
              </div>
              <!-- Total Mom Data -->
              <div class="col-md-3 mb-2">
                <div class="card h-100 border-0 shadow-sm">
                  <div class="card-body text-center p-2">
                    <h6 class="card-subtitle mb-2 text-muted">📂 Total Mom Data</h6>
                    <a href="<?=base_url()?>Reports/MeetingsDatas/total_mom_data/<?=$targetUID?>/<?=$sdate?>/<?=$edate?>/userwise" class="card-link" style="color: <?=$backgroundColorNew;?>;">
                      <h4 class="card-title mb-0"><?= $row->total_mom_data; ?></h4>
                    </a>
                  </div>
                </div>
              </div>
              <!-- Total Approved After Check Mom Data -->
              <div class="col-md-3 mb-2">
                <div class="card h-100 border-0 shadow-sm">
                  <div class="card-body text-center p-2">
                    <h6 class="card-subtitle mb-2 text-muted">✅ Total Approved After Check Mom Data</h6>
                    <a href="<?=base_url()?>Reports/MeetingsDatas/total_approved_after_check_mom_data/<?=$targetUID?>/<?=$sdate?>/<?=$edate?>/userwise" class="card-link" style="color: <?=$backgroundColorNew;?>;">
                      <h4 class="card-title mb-0"><?= $row->total_approved_after_check_mom_data; ?></h4>
                    </a>
                  </div>
                </div>
              </div>
              <!-- Total Reject After Check Mom Data -->
              <div class="col-md-3 mb-2">
                <div class="card h-100 border-0 shadow-sm">
                  <div class="card-body text-center p-2">
                    <h6 class="card-subtitle mb-2 text-muted">❌ Total Reject After Check Mom Data</h6>
                    <a href="<?=base_url()?>Reports/MeetingsDatas/total_reject_after_check_mom_data/<?=$targetUID?>/<?=$sdate?>/<?=$edate?>/userwise" class="card-link" style="color: <?=$backgroundColorNew;?>;">
                      <h4 class="card-title mb-0"><?= $row->total_reject_after_check_mom_data; ?></h4>
                    </a>
                  </div>
                </div>
              </div>
              <!-- Total NO RP After Check Mom Data -->
              <div class="col-md-3 mb-2">
                <div class="card h-100 border-0 shadow-sm">
                  <div class="card-body text-center p-2">
                    <h6 class="card-subtitle mb-2 text-muted">🚫 Total NO RP After Check Mom Data</h6>
                    <a href="<?=base_url()?>Reports/MeetingsDatas/total_NO_RP_after_check_mom_data/<?=$targetUID?>/<?=$sdate?>/<?=$edate?>/userwise" class="card-link" style="color: <?=$backgroundColorNew;?>;">
                      <h4 class="card-title mb-0"><?= $row->total_NO_RP_after_check_mom_data; ?></h4>
                    </a>
                  </div>
                </div>
              </div>
              <!-- Total Pending For Check Mom Data -->
              <div class="col-md-3 mb-2">
                <div class="card h-100 border-0 shadow-sm">
                  <div class="card-body text-center p-2">
                    <h6 class="card-subtitle mb-2 text-muted">🕵️ Total Pending For Check Mom Data</h6>
                    <a href="<?=base_url()?>Reports/MeetingsDatas/total_pending_for_check_mom_data/<?=$targetUID?>/<?=$sdate?>/<?=$edate?>/userwise" class="card-link" style="color: <?=$backgroundColorNew;?>;">
                      <h4 class="card-title mb-0"><?= $row->total_pending_for_check_mom_data; ?></h4>
                    </a>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div class="card-footer text-muted text-center">
            <small>Task Frequency: <?= $row->task_frequency_per_day; ?> Task Per Day</small>
          </div>
        </div>
      </div>
      <?php $i++; } ?>
    </div>


                    </div>



                    <div class="col-md-12">
                       <hr>
                          <div class="text-center" style="background: linear-gradient(to right, #ede7f6, #ffffff); border-radius: 12px; box-shadow: 0 4px 8px rgba(0,0,0,0.1); padding: 20px;">
                            <h5>
                              <i>Proposal & Meeting Task Summary</i><br>
                            </h5>
                            <center>
                                <span style="width: 20%;"><small>This summary provides a breakdown of total tasks, scheduled and bargaining meetings, and proposal statuses. It highlights how many proposals have been sent or remain pending, along with insight into meetings by type. The final metric tracks proposals sent but still awaiting proper closure or follow-up action.</small></p>
                                 <p class="text-center" style="color: #ff299d;"><strong><?= $curFinancialStartDate?> - to - <?= $curTillDate;?></strong></p>
                              </center>
                          </div>


   <?php 

              $data = $totalMeetingsUserByDatas[0];
              $total_meetings                         = $data->total_meetings;
              $proposal_sent                          = $data->proposal_sent;
              $proposal_not_sent                      = $data->proposal_not_sent;
              $proposal_sent_but_closure_not_done     = $data->proposal_sent_but_closure_not_done;
              $proposal_sent_but_closure_done         = $data->proposal_sent_but_closure_done;
              $bd_potentional_client_proposal_not_sent  = $data->bd_potentional_client_proposal_not_sent;

              // dd($totalMeetingsUserByDatas);
            ?>
<hr>
                             <div class="row">
                 <div class="col-md-3 mb-3">
                <div class="card text-center shadow text-dark" style="background: linear-gradient(to right, #dfe9f3, #ffffff); color: hsl(60, 88%, 38%);">
                  <div class="card-body card-body-proposal" style="color:hsl(60, 88%, 38%)!important">
                    <h5 class="card-title"><b>📊 Total Meetings On Company</b></h5>
                    <small>Total number of tasks (meetings) assigned.</small> 
                    <hr>
                    <a target="_BLANK" class="card-count-heading-new" style="color:hsl(60, 88%, 38%)!important" href="<?=base_url()?>Reports/MeetingsDatas/total_RP_meetings_on_Check_Proposal/<?=$targetUID?>/<?=$sdate?>/<?=$edate?>">
                      <h3 class="card-text"><?=$total_meetings?></h3>
                    </a>
                  </div>
                </div>
              </div>
             <div class="col-md-3 mb-3">
                <div class="card text-center shadow text-dark" style="background: linear-gradient(to right, #dfe9f3, #ffffff); color: hsl(184, 81%, 32%);">
                  <div class="card-body card-body-proposal" style="color:hsl(184, 81%, 32%)!important">
                    <h5 class="card-title"><b>📨 Proposal Sent</b></h5>
                    <small>Number of meetings where proposal has been sent.</small> 
                    <hr>
                    <a target="_BLANK" class="card-count-heading-new" style="color:hsl(184, 81%, 32%)!important" href="<?=base_url()?>Reports/MeetingDoneProposalOverviewClosureStatusLists/proposal_sent/<?=$targetUID?>/<?=$sdate?>/<?=$edate?>">
                      <h3 class="card-text"><?=$proposal_sent?></h3>
                    </a>
                  </div>
                </div>
              </div>
              <div class="col-md-3 mb-3">
                <div class="card text-center shadow text-dark" style="background: linear-gradient(to right, #dfe9f3, #ffffff); color: hsl(347, 91.70%, 52.70%);">
                  <div class="card-body card-body-proposal" style="color:hsl(347, 95.80%, 53.10%)!important">
                    <h5 class="card-title"><b>🚫 Proposal Not Sent</b></h5>
                    <small>Number of meetings without a proposal sent.</small> 
                    <hr>
                    <a target="_BLANK" class="card-count-heading-new" style="color:hsl(347, 95.10%, 52.40%)!important" href="<?=base_url()?>Reports/MeetingDoneProposalOverviewClosureStatusLists/proposal_not_sent/<?=$targetUID?>/<?=$sdate?>/<?=$edate?>">
                      <h3 class="card-text"><?=$proposal_not_sent?></h3>
                    </a>
                  </div>
                </div>
              </div>
              <div class="col-md-3 mb-3">
                <div class="card text-center shadow text-dark" style="background: linear-gradient(to right, #dfe9f3, #ffffff); color: hsl(347, 91.70%, 52.70%);">
                  <div class="card-body card-body-proposal" style="color:hsl(347, 95.80%, 53.10%)!important">
                    <h5 class="card-title"><b>🚫 BD Potentional Client Proposal Not Sent</b></h5>
                    <small>Number of meetings without a proposal sent.</small> 
                    <hr>
                    <a target="_BLANK" class="card-count-heading-new" style="color:hsl(347, 95.10%, 52.40%)!important" href="<?=base_url()?>Reports/MeetingDoneProposalOverviewClosureStatusLists/bd_potentional_client_proposal_not_sent/<?=$targetUID?>/<?=$sdate?>/<?=$edate?>">
                      <h3 class="card-text"><?=$bd_potentional_client_proposal_not_sent?></h3>
                    </a>
                  </div>
                </div>
              </div>
              </div>


              <div class="row">
                  <div class="col-md-4 mb-3">
                <div class="card text-center shadow text-dark" style="background: linear-gradient(to right, #dfe9f3, #ffffff); color: hsl(184, 81%, 32%);">
                  <div class="card-body card-body-proposal" style="color:hsl(184, 81%, 32%)!important">
                    <h5 class="card-title"><b>📨 Proposal Sent</b></h5>
                    <small>Number of meetings where proposal has been sent.</small> 
                    <hr>
                    <a target="_BLANK" class="card-count-heading-new" style="color:hsl(184, 81%, 32%)!important" href="<?=base_url()?>Reports/MeetingDoneProposalOverviewClosureStatusLists/proposal_sent/<?=$targetUID?>/<?=$sdate?>/<?=$edate?>">
                      <h3 class="card-text"><?=$proposal_sent?></h3>
                    </a>
                  </div>
                </div>
              </div>
                <div class="col-md-4 mb-3">
                <div class="card text-center shadow text-dark" style="background: linear-gradient(to right, #dfe9f3, #ffffff); color: hsl(347, 85.20%, 44.90%);">
                  <div class="card-body card-body-proposal" style="color:hsl(347, 85.20%, 44.90%)!important">
                    <h5 class="card-title"><b>⏳ Proposal Sent But Closure Not Done</b></h5>
                    <small>Proposals sent but final closure not completed.</small> 
                    <hr>
                    <a target="_BLANK" class="card-count-heading-new" style="color:hsl(347, 85.20%, 44.90%)!important" href="<?=base_url()?>Reports/MeetingDoneProposalOverviewClosureStatusLists/proposal_sent_but_closure_not_done/<?=$targetUID?>/<?=$sdate?>/<?=$edate?>">
                      <h3 class="card-text"><?=$proposal_sent_but_closure_not_done?></h3>
                    </a>
                  </div>
                </div>
              </div>
                <div class="col-md-4 mb-3">
                <div class="card text-center shadow text-dark" style="background: linear-gradient(to right, #dfe9f3, #ffffff); color: hsl(146, 85%, 45%);">
                  <div class="card-body card-body-proposal" style="color:hsl(146, 85%, 45%)!important">
                    <h5 class="card-title"><b>✅ Proposal Sent Closure Done</b></h5>
                    <small>Proposals sent but final closure completed.</small> 
                    <hr>
                    <a target="_BLANK" class="card-count-heading-new" style="color:hsl(146, 85%, 45%)!important" href="<?=base_url()?>Reports/MeetingDoneProposalOverviewClosureStatusLists/proposal_sent_but_closure_done/<?=$targetUID?>/<?=$sdate?>/<?=$edate?>">
                      <h3 class="card-text"><?=$proposal_sent_but_closure_done?></h3>
                    </a>
                  </div>
                </div>
              </div>
              </div>

            <hr>
                    </div>



                    <div class="col-md-12">
                          <hr>
            <div class="text-center" style="background: linear-gradient(to right, #ede7f6, #ffffff); border-radius: 12px; box-shadow: 0 4px 8px rgba(0,0,0,0.1); padding: 20px;">
              <h5>
                <i>Average Task Frequency Per Day</i><br>
                <small>This shows how frequently tasks are planned or completed on average per day.</small> <br>
                <small style="color: #ff299d;"><strong><?= $curFinancialStartDate?> - to - <?= $curTillDate;?></strong></small>
              </h5>
            </div>
            <hr>
            <div class="row">
              <?php  
                $descriptions = [
                    'average_total_task_frequency_per_day' => 'Average number of all tasks per day.',
                    'average_total_task_AY_PY_frequency_per_day' => 'Average number of AYPY tasks per day.',
                    'average_total_task_AY_PN_frequency_per_day' => 'Average number of AYPN tasks per day.',
                    'average_total_task_AN_PN_frequency_per_day' => 'Average number of ANPN tasks per day.',
                    'average_total_task_AY_PY_Same_Status_frequency_per_day' => 'Average AYPY tasks per day with same status maintained.',
                    'average_total_funnel_status_change_frequency_per_day' => 'Average number of funnel status changes per day.',
                    'average_task_own_funnel_status_change_task_frequency_per_day' => 'Average status changes per day in user-created funnel.',
                    'average_task_team_funnel_status_change_task_frequency_per_day' => 'Average status changes per day in team funnel.',
                    'average_complete_task_frequency_per_day_on_team_funnel' => 'Average completed tasks per day in team funnel.',
                    'average_complete_task_frequency_per_day_on_own_funnel' => 'Average completed tasks per day in user\'s own funnel.',
                    'average_AY_PY_task_frequency_per_day_on_own_funnel' => 'Average AYPY tasks per day in user\'s funnel.',
                    'average_AY_PY_task_frequency_per_day_on_team_funnel' => 'Average AYPY tasks per day in team funnel.',
                    'average_AY_PY_status_change_task_frequency_per_day_on_own_funnel' => 'Average status changes per day for AYPY tasks in user\'s funnel.',
                    'average_AY_PY_status_change_task_frequency_per_day_on_team_funnel' => 'Average status changes per day for AYPY tasks in team funnel.',
                    'average_pending_task_frequency_per_day_on_own_funnel' => 'Average number of pending tasks per day in user\'s funnel.',
                    'average_pending_task_frequency_per_day_on_team_funnel' => 'Average number of pending tasks per day in team funnel.',
                ];
                
                
                foreach ($getTotalTasksFrequncy as $tasktypeData) {
                foreach ($tasktypeData as $key => $value) {
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
                }
                ?>
            </div>


<br>
                    </div>











                      <style>
                        .equal-height {
                        display: flex;
                        flex-wrap: wrap;
                        gap: 15px;
                        }
                        .equal-height > div {
                        flex: 1;
                        min-width: 200px;
                        height: fit-content;
                        background: white;
                        border-radius: 12px;
                        box-shadow: 0 4px 8px rgba(0,0,0,0.1);
                        padding: 15px;
                        display: flex;
                        flex-direction: column;
                        }
                        .equal-height table {
                        flex: 1;
                        }
                      </style>
                      <?php
                        $status        = $total_funnels_status[0] ?? null;
                        $old_category  = $total_funnels_old_category[0] ?? null;
                        $new_category  = $total_funnels_new_category[0] ?? null;
                        $assign        = $total_funnels_assign[0] ?? null;
                        $user_funnel   = $total_funnel_by_user[0] ?? null;
                        $partner       = $total_funnels_partner[0] ?? null;
                        $partner_user  = $total_funnels_partner_user[0] ?? null;
                        $current_q     = $total_current_quarter[0] ?? null;
                        $cluster_data  = $totalClusterDatas[0] ?? null;
                        
                        function printSection($title, $data, $emoji = '',$targetUIDss,$colmd,$headingdescriptions) {
                            if (!$data) return;
                        
                            echo '<div class="'.$colmd.' mb-2">';
                            echo '<div style="background: linear-gradient(to right, #e8f5e9, #ffffff); border-radius: 12px; box-shadow: 0 4px 8px rgba(0,0,0,0.1); padding: 20px;">';
                            echo '<h5 class="text-center" style="margin-top:20px;">' . $emoji . ' ' . $title . '
                            <hr><small>' .$headingdescriptions.'</small></h5><hr>';
                            echo '<div class="equal-height">';
                        
                            foreach ($data as $key => $value) {
                                $r = rand(150, 255);
                                $g = rand(150, 255);
                                $b = rand(150, 255);
                                $backgroundColor = "rgba($r, $g, $b, .3)";
                        
                                if($key  == "Assistant_Sales_Head_(NAE)_assign_funnel"){
                                      $key = "Assistant_Sales_Head_NAE_assign_funnel";
                                }
                        
                                echo '<div class="equalheightwidth">';
                                echo '<div class="card text-center shadow taskusercardData" style="background-color:' . $backgroundColor . '">';
                                echo '<div class="card-body text-center card-card-data">';
                                echo '<h5><strong>' . ucfirst(str_replace('_', ' ', $key)) . '</strong></h5>';
                                
                                echo '<hr>';
                                echo '<span class="badge badge-pill badge-success p-2">' ?>
                      <a class="text-white" target="_BLANK" href="<?=base_url()?>Reports/FunnelDetails/<?=$key;?>/<?= $targetUIDss; ?>/userwise">
                      <?=$value?>
                      </a>
                      <?php '</span>';
                        echo '</div></div></div>';
                        }
                        
                        echo '</div>'; // equal-height
                        echo '</div>'; // gradient container
                        echo '</div>'; // col-md-6
                        }
                        ?>
                      <div class="col-md-12 mt-2">
                        <div style="background: linear-gradient(to right, #fce4ec, #ffffff); border-radius: 12px; 
                          box-shadow: 0 4px 8px rgba(0,0,0,0.1); padding: 20px;">
                          <div class="text-center">
                            <hr>
                            <h4 class="mb-0">📊 Full Funnel Data Overview</h4>
                          </div>
                          <hr>
                          <div class="row">
                            <?php
                              $headingdescriptions = '📊 Funnel Status shows lead stages in a pipeline, tracking 📈 progress, finding 🔍 bottlenecks, and boosting 🎯 conversions. It guides data-driven decisions ⚙️, optimizes performance 🚀, and ensures sustainable growth ✅.';
                              printSection("Funnel Status", $status, "📊",$targetUID,'col-md-6',$headingdescriptions);
                              
                              
                              $headingdescriptions = '🤝 Partner Details capture 🏢 organization, 👤 contact, 📞 communication, and 📍 location info. They ensure 📂 records, smooth 📢 communication, and 🗂️ coordination, fostering ❤️ relationships, ⚡ decisions, and 📈 growth for 🌟 success.';
                              printSection("Partner Details", $partner, "🤝",$targetUID,'col-md-6',$headingdescriptions);
                              
                              $headingdescriptions = '📂 Old Category tracks past 🏷️ classifications, aiding 📜 record-keeping, 🗂️ data organization, and 🔍 analysis. It ensures 🧩 continuity, supports 📊 trends, and preserves 🕰️ insights for 📈 informed, context-rich decisions.';
                              printSection("Old Category", $old_category, "📂",$targetUID,'col-md-6',$headingdescriptions);
                              
                              $headingdescriptions = '🆕 New Category is the latest 🏷️ classification for organizing 📂 data or processes. It reflects 🔄 updates, boosts 📊 tracking, 🔍 analysis, and 🎯 decisions, ensuring 🚀 efficiency, clarity, and 🌟 relevance.';
                              printSection("New Category", $new_category, "🆕",$targetUID,'col-md-6',$headingdescriptions);
                              
                              $headingdescriptions = '📝 Line Manager Assignments delegate 🎯 tasks, set 📋 responsibilities, and align goals. 👨‍💼 Managers oversee 📊 operations, track 📈 performance, and guide teams, ensuring 📢 clarity, ⚙️ efficiency, and 🚀 productivity for 🌱 growth.';
                              printSection("Line Manager Assignments involve", $assign, "📝",$targetUID,'col-md-12',$headingdescriptions);
                              // printSection("Funnel by User", $user_funnel, "👤");
                              
                              // printSection("Partner by User", $partner_user, "🧑‍💼");
                              
                              $headingdescriptions = '💰📅 Current Financial Year Quarter is the active 🗓️ 3-month fiscal period for 📊 performance tracking, 🎯 target achievement, and 📈 growth. It aids 📑 reporting, 🔍 analysis, and 🚀 goal alignment for 🌟 success.';
                              printSection("Current Financial Year Quarter", $current_q, "📅",$targetUID,'col-md-6',$headingdescriptions);
                              
                              $headingdescriptions = '🗺️ Travel Cluster Data groups 📍 regions, 🛣️ routes, or 🌍 zones to 📊 analyze patterns, optimize 🚐 logistics, and improve 🗂️ resources—boosting 📈 efficiency, cutting ⏳ time, and driving 🚀 targeted, cost-effective 💰 operations.';
                              printSection("Travel Cluster Data", $cluster_data, "📍",$targetUID,'col-md-6',$headingdescriptions);
                              ?>
                          </div>
                        </div>
                      </div>
                      <!-- <div class="col-md-3">
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
                        </div> -->
                    </div>
                    <hr>
                    <div class="text-center" style="background: linear-gradient(to right, #ede7f6, #ffffff); border-radius: 12px; box-shadow: 0 4px 8px rgba(0,0,0,0.1); padding: 20px;">
                      <h5 class="mb-0">📊 Map Contact Information</h5>
                      <center>
                        <small class="d-block" style="width: 70%;">
                        This section provides key contact details of individuals associated with the project or organization. It includes names, roles, phone numbers, and email addresses—ensuring quick communication, effective coordination, and seamless follow-ups. Use this reference to stay connected and maintain strong stakeholder engagement.
                        </small>
                      </center>
                    </div>
                    <hr>
                    <div class="row">
                      <div class="col-md-12 mt-2">
                        <?php
                          if (!empty($address)) {
                              $full_location = $address;
                          } elseif (!empty($city)) {
                              $full_location = $city;
                          } elseif (!empty($state)) {
                              $full_location = $state;
                          } elseif (!empty($country)) {
                              $full_location = $country;
                          } else {
                              $full_location = 'India'; // fallback
                          }
                          
                          $location = urlencode(trim($full_location));
                          
                          // Marker version
                          $map_url = "https://maps.google.com/maps?q={$location}&t=&z=13&ie=UTF8&iwloc=B&output=embed";
                          ?>
                        <div class="maparea" style="height: 400px;">
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
                        <div class="card-header text-center" style="background: linear-gradient(to right, #e0f7fa, #ffffff); border-radius: 12px; box-shadow: 0 4px 8px rgba(0,0,0,0.1); padding: 20px;">
                          <h5 class="mb-0">📊All Time Task Wise Activity Summary</h5>
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
                      <hr>
                      <div class="card shadow-sm border-0 rounded-4 mb-4">
                        <div class="card-header text-center" style="background: linear-gradient(to right, #e0f7fa, #ffffff); border-radius: 12px; box-shadow: 0 4px 8px rgba(0,0,0,0.1); padding: 20px;">
                          <h5 class="mb-0">📊All Task Wise Activity Summary In Current Financial Year</h5>
                          <small class="d-block">Overview of your engagement and planning activities</small>
                        </div>
                        <div class="card-body">
                          <div class="row">
                            <?php foreach ($task_countsf as $label => $value): ?>
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
                        <h5 class="mb-0">📊 All Time Status Wise Activity Summary</h5>
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
                    <div class="card shadow-sm border-0 rounded-4 mb-4">
                      <div class="card-header text-center" style="background: linear-gradient(to right, #f6e7f3, #ffffff); border-radius: 12px; box-shadow: 0 4px 8px rgba(0,0,0,0.1); padding: 20px;">
                        <h5 class="mb-0">📊 Status Wise Activity Summary In Current Financial Year</h5>
                        <small class="d-block">Overview of your engagement and planning activities</small>
                      </div>
                      <div class="card-body">
                        <div class="row">
                          <?php foreach ($taskTtimeStatusDatasf as $label => $value): ?>
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
                 
                        <div class="table-responsive text-nowrap" style="overflow-x: auto;">
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
                              <th class="text-capitalize">📍 Closing Timeline</th>
                              <th class="text-capitalize">📝 Proposal Require</th>
                              <th class="text-capitalize">🗑️ Deleted Task</th>
                              <th class="text-capitalize">🔁 Task RePlanned Count</th>
                              <th class="text-capitalize">✏️ Meeting Type</th>
                              <th class="text-capitalize">🧾 MOM</th>
                              <th class="text-capitalize">🔍 View Details</th>
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
                              if($curefinancialyear != $ccfyear){
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
                        <td><?= htmlspecialchars($row->mtype) ?></td>
                        <td>
                          <?php 
                            if($row->actiontype_id == 3 || $row->actiontype_id == 4 || $row->actiontype_id == 17){
                            
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
                          <?php  if($row->actiontype_id == 3 || $row->actiontype_id == 4 || $row->actiontype_id == 17){?>
                          <a class="bg-success p-1" href="<?= base_url(); ?>Reports/ViewMeetingDetails/<?= $row->task_id; ?>" target="_blank">View Details</a>
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
                  <div class="table-responsive text-nowrap" style="overflow-x: auto;">
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
                  <th class="text-capitalize">⭐ Closing Timeline</th>
                  <th class="text-capitalize">📝 Proposal Require</th>
                  <th class="text-capitalize">🗑️ Deleted Task</th>
                  <th class="text-capitalize">🔁 Task RePlanned Count</th>
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
                        if($curefinancialyear != $ccfyear){
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
                  <td><?= htmlspecialchars($row->closing_timeline) ?></td>
                  <td><?= htmlspecialchars($row->proposal_require) ?></td>
                  <td><?php if($row->delete_request > 0){ ?> <span class="bg-danger p-1"> Deleted</span> <?php }else{ ?> <?php } ?></td>
                  <td> 
                  <a style="color:<?=$backgroundColorNew;?>!important" href="<?=base_url();?>Menu/ReplanedLog/<?=$row->task_id;  ?>" target="_BLANK"><?= htmlspecialchars($row->plan_count) ?></a>
                  </td>
                  </tr>
                  <?php $i++; } ?>
                  </tbody>
                  </table>
                  </div>
                  </section> 
                  <?php /* 
                    <hr>
                    <div class="text-center" style="background: linear-gradient(to right, #f6e7f3, #ffffff); border-radius: 12px; box-shadow: 0 4px 8px rgba(0,0,0,0.1); padding: 20px;">
                      <h5 class="mb-0">📊 Completed Tasks (All Time History)</h5>
                      <center>
                        <small class="d-block" style="width: 70%;">This section provides a detailed snapshot of all tasks you've successfully completed, highlighting key actions, timelines, and outcomes. It helps you monitor progress, measure impact, and maintain accountability in your planning and engagement efforts. Use this overview to evaluate performance and plan future steps with clarity and confidence.</small>
                      </center>
                    </div>
                    <hr>
                    
                    
                    
                    
                    
                    <div class="table-responsive text-nowrap" style="overflow-x: auto;">
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
                          <th class="text-capitalize">⭐ Closing Timeline</th>
                          <th class="text-capitalize">📝 Proposal Require</th>
                          <th class="text-capitalize">🗑️ Deleted Task</th>
                          <th class="text-capitalize">🔁 Task RePlanned Count</th>
                          <th class="text-capitalize">✏️ Meeting Type</th>
                          <th class="text-capitalize">🧾 MOM</th>
                          <th class="text-capitalize">🔍 View Details</th>
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
                    <td><?= htmlspecialchars($row->closing_timeline) ?></td>
                    <td><?= htmlspecialchars($row->proposal_require) ?></td>
                    <td><?php if($row->delete_request > 0){ ?> <span class="bg-danger p-1"> Deleted</span> <?php }else{ ?> <?php } ?></td>
                    <td> 
                      <a style="color:<?=$backgroundColorNew;?>!important" href="<?=base_url();?>Menu/ReplanedLog/<?=$row->task_id;  ?>" target="_BLANK"><?= htmlspecialchars($row->plan_count) ?></a>
                    </td>
                    <td><?= htmlspecialchars($row->mtype) ?></td>
                    <td>
                      <?php 
                        if($row->actiontype_id == 3 || $row->actiontype_id == 4 || $row->actiontype_id == 17){
                        
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
                      <?php  if($row->actiontype_id == 3 || $row->actiontype_id == 4 || $row->actiontype_id == 17){?>
                      <a class="bg-success p-1" href="<?= base_url(); ?>Reports/ViewMeetingDetails/<?= $row->task_id; ?>" target="_blank">View Details</a>
                      <?php } ?>
                    </td>
                  </tr>
                  <?php $i++; } ?>
                  </tbody>
                  </table>
                </div>
                */ ?>
                <hr>
                <div class="text-center" style="background: linear-gradient(to right, #ffe274, #ffffff); border-radius: 12px; box-shadow: 0 4px 8px rgba(0,0,0,0.1); padding: 20px;">
                  <h5 class="mb-0">📊 Pending Tasks (Current Financial Year)</h5>
                  <center>
                    <small class="d-block" style="width: 70%;">
                    This section highlights all your ongoing or upcoming tasks that are yet to be completed. It offers insights into scheduled actions, timelines, and responsibilities—helping you stay organized, prioritize effectively, and ensure timely execution of your plans. Use this view to track what’s next and prepare accordingly.
                    </small>
                  </center>
                </div>
                <hr>
                <div class="table-responsive text-nowrap" style="overflow-x: auto;">
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
                      </tr>
                    </thead>
                    <tbody>
                      <?php
                        $i = 1;
                        foreach ($cmpTasksDatas as $row) {
                        
                            $pknextCFID = $row->nextCFID;
                            $appointmentdatetime = $row->appointmentdatetime;
                            $ccfyear = date("Y", strtotime($appointmentdatetime));
                        
                            if($pknextCFID !== 0){
                                continue;
                            }
                            if($curefinancialyear != $ccfyear){
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
                      </tr>
                      <?php $i++; } ?>
                    </tbody>
                  </table>
                </div>
                <hr>
                <div class="text-center" style="background: linear-gradient(to right,rgb(164, 252, 147), #ffffff); border-radius: 12px; box-shadow: 0 4px 8px rgba(0,0,0,0.1); padding: 20px;">
                  <h5 class="mb-0">📊 Positive Conversions Tasks (Current Financial Year)</h5>
                  <center>
                    <small class="d-block" style="width: 70%;">
                    This section showcases tasks that have led to or are expected to result in positive conversions. It helps you monitor impactful actions, track progress, and focus on key follow-ups—ensuring you maximize outcomes and maintain momentum in your conversion journey.
                    </small>
                  </center>
                  </center>
                </div>
                <hr>
                <div class="table-responsive text-nowrap" style="overflow-x: auto;">
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
                      </tr>
                    </thead>
                    <tbody>
                      <?php
                        $i = 1;
                        foreach ($cmpPositiveTaskDatas as $row) {
                          $pknextCFID = $row->nextCFID;
                        
                        
                           $appointmentdatetime = $row->appointmentdatetime;
                            $ccfyear = date("Y", strtotime($appointmentdatetime));
                            if($curefinancialyear != $ccfyear){
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
                      </tr>
                      <?php $i++; } ?>
                    </tbody>
                  </table>
                </div>
                <hr>
                <div class="text-center" style="background: linear-gradient(to right, #fec7c7, #ffffff); border-radius: 12px; box-shadow: 0 4px 8px rgba(0,0,0,0.1); padding: 20px;">
                  <h5 class="mb-0">📊 Negative Conversions Tasks (Current Financial Year)</h5>
                  <center>
                    <small class="d-block" style="width: 70%;">
                    This section highlights tasks that resulted in or may lead to negative conversions. It helps you identify bottlenecks, analyze causes, and take corrective actions—enabling you to improve future outcomes, reduce risks, and refine your strategies for better conversion success.
                    </small>
                  </center>
                </div>
                <hr>
                <div class="table-responsive text-nowrap" style="overflow-x: auto;">
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
                      </tr>
                    </thead>
                    <tbody>
                      <?php
                        $i = 1;
                        foreach ($cmpNegativeTaskDatas as $row) {
                          $pknextCFID = $row->nextCFID;
                        
                            $appointmentdatetime = $row->appointmentdatetime;
                            $ccfyear = date("Y", strtotime($appointmentdatetime));
                            if($curefinancialyear != $ccfyear){
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
                        <td><?php if($row->delete_request > 0){ ?> <span class="bg-danger p-1"> Deleted</span> <?php }else{ ?> <?php } ?></td>
                        <td> 
                          <a style="color:<?=$backgroundColorNew;?>!important" href="<?=base_url();?>Menu/ReplanedLog/<?=$row->task_id;  ?>" target="_BLANK"><?= htmlspecialchars($row->plan_count) ?></a>
                        </td>
                      </tr>
                      <?php $i++; } ?>
                    </tbody>
                  </table>
                </div>
                <hr>
                <div class="text-center" style="background: linear-gradient(to right, #d9fff8, #ffffff); border-radius: 12px; box-shadow: 0 4px 8px rgba(0,0,0,0.1); padding: 20px;">
                  <h5 class="mb-0">📊 Other Conversions Tasks (Current Financial Year)</h5>
                  <center>
                    <small class="d-block" style="width: 70%;">
                    This section includes tasks that don't fall under clearly positive or negative conversions. It helps you review miscellaneous activities, explore uncertain outcomes, and assess potential opportunities or risks—supporting informed decision-making and continuous improvement in your conversion process.
                    </small>
                  </center>
                </div>
                <hr>
                <div class="table-responsive text-nowrap" style="overflow-x: auto;">
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
                      </tr>
                    </thead>
                    <tbody>
                      <?php
                        $i = 1;
                        foreach ($cmpOtherTaskDatas as $row) {
                          $pknextCFID = $row->nextCFID;
                        
                            $appointmentdatetime = $row->appointmentdatetime;
                            $ccfyear = date("Y", strtotime($appointmentdatetime));
                        
                            if($curefinancialyear != $ccfyear){
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
                        <td><?php if($row->delete_request > 0){ ?> <span class="bg-danger p-1"> Deleted</span> <?php }else{ ?> <?php } ?></td>
                        <td> 
                          <a style="color:<?=$backgroundColorNew;?>!important" href="<?=base_url();?>Menu/ReplanedLog/<?=$row->task_id;  ?>" target="_BLANK"><?= htmlspecialchars($row->plan_count) ?></a>
                        </td>
                      </tr>
                      <?php $i++; } ?>
                    </tbody>
                  </table>
                </div>
                <hr>
                <div class="text-center" style="background: linear-gradient(to right, #f6e7f3, #ffffff); border-radius: 12px; box-shadow: 0 4px 8px rgba(0,0,0,0.1); padding: 20px;">
                  <h5 class="mb-0">📊 Completed – Under Review in Current Financial Year </h5>
                  <center>
                    <small class="d-block" style="width: 70%;">
                    This section highlights tasks that have been marked as completed but are currently under review for validation or approval. It offers visibility into recent efforts, pending confirmations, and progress checkpoints—helping ensure that all activities meet expectations before being finalized and closed.
                    </small>
                  </center>
                </div>
                <hr>
                <?php if (!empty($cmpReviewsDatas)) {
                  $reviewTypeWithCount = [];
                  foreach ($cmpReviewsDatas as $index => $row) : 
                  
                    $rtypeName      = $row->rtype;
                    if (!isset($reviewTypeWithCount[$rtypeName])) {
                        $reviewTypeWithCount[$rtypeName] = 1;
                    } else {
                        $reviewTypeWithCount[$rtypeName]++;
                    }
                  
                  endforeach;
                  }
                  ?>
                <div class="card shadow-sm border-0 rounded-4 mb-4">
                  <div class="card-header text-center" style="background: linear-gradient(to right, #e0f7fa, #ffffff); border-radius: 12px; box-shadow: 0 4px 8px rgba(0,0,0,0.1); padding: 20px;">
                    <h5 class="mb-0">📊 Review Type With Count Summary In Current Financial Year</h5>
                    <small class="d-block">Overview of your engagement and planning activities</small>
                  </div>
                  <div class="card-body">
                    <div class="row">
                      <?php foreach ($reviewTypeWithCount as $label => $value): ?>
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
                <hr>
                <div class="table-responsive text-nowrap" style="overflow-x: auto;">
                  <?php if (!empty($cmpReviewsDatas)) : ?>
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
                          
                            $appointmentdatetime = $row->appointmentdatetime;
                            $ccfyear = date("Y", strtotime($appointmentdatetime));
                            if($curefinancialyear != $ccfyear){
                                continue;
                            }
                          
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
      
    </script>
  </body>
</html>
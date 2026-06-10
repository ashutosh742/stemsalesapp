<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Leave Management | STEM Operation | WebAPP</title>
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
  </head>
  <body class="hold-transition sidebar-mini layout-fixed">
    <div class="wrapper">
      <!-- Preloader -->
      <!-- Navbar -->
     <?php $this->load->view($dep_name.'/nav.php');?>
      <style>
       .gradient-text,.gradient-text1{color:transparent;animation:5s infinite gradientAnimation}.gradient-text{background:linear-gradient(90deg,#ff8a00,#e52e71,#1e90ff);background-size:300% 300%;-webkit-background-clip:text;background-clip:text}.gradient-text1{background:linear-gradient(90deg,#e113aa,#1500ff,#1e90ff);background-size:300% 300%;-webkit-background-clip:text;background-clip:text}@keyframes gradientAnimation{0%,100%{background-position:0 50%}50%{background-position:100% 50%}}.card-height{height:600px}.card-height1{height:300px}.maparea{max-width:100%;border-radius:20px;padding:8px;background:linear-gradient(135deg,#e3f2fd,#fce4ec);border:3px solid transparent;background-clip:padding-box;position:relative;overflow:hidden;transition:transform .3s,box-shadow .3s;align-items:center;justify-content:center;display:flex;height:100%}.maparea:hover{box-shadow:0 12px 35px rgba(0,0,0,.25)}.custom-btn{width:130px;height:40px;color:#fff;border-radius:5px;padding:10px 25px;font-family:Lato,sans-serif;font-weight:500;background:0 0;cursor:pointer;transition:.3s;position:relative;display:inline-block;box-shadow:inset 2px 2px 2px 0 rgba(255,255,255,.5),7px 7px 20px 0 rgba(0,0,0,.1),4px 4px 5px 0 rgba(0,0,0,.1);outline:0}.btn-11{border:none;background:#212ffb;background:linear-gradient(0deg,#3e21fb 0,#4c5cea 100%);color:#fff;overflow:hidden}.btn-11:hover{text-decoration:none;color:#fff;opacity:.7}.btn-11:before{position:absolute;content:'';display:inline-block;top:-180px;left:0;width:30px;height:100%;background-color:#fff;animation:3s ease-in-out infinite shiny-btn1}.btn-11:active{box-shadow:4px 4px 6px 0 rgba(255,255,255,.3),-4px -4px 6px 0 rgba(116,125,136,.2),inset -4px -4px 6px 0 rgba(255,255,255,.2),inset 4px 4px 6px 0 rgba(0,0,0,.2)}@keyframes shiny-btn1{0%{transform:scale(0) rotate(45deg);opacity:0}80%{transform:scale(0) rotate(45deg);opacity:.5}81%{transform:scale(4) rotate(45deg);opacity:1}100%{transform:scale(50) rotate(45deg);opacity:0}}

       .cardline{border-top: 1px solid rgb(4 4 4 / 84%);}
       /* .card .meetingslist-card:hover {
        transition: 0.4s ease-in-out;
  box-shadow: rgba(50, 50, 93, 0.25) 0px 30px 60px -12px inset,
              rgba(0, 0, 0, 0.3) 0px 18px 36px -18px inset !important;
} */
.live-badge{display:inline-flex;align-items:center;background-color:red;color:#fff;font-weight:700;padding:4px 10px;border-radius:20px;font-size:14px;font-family:Arial,sans-serif;animation:1s infinite pulse;box-shadow:0 0 5px red}.live-dot{width:8px;height:8px;background-color:#fff;border-radius:50%;margin-right:6px;animation:1s infinite blink}@keyframes blink{0%,100%,50%{opacity:1}25%,75%{opacity:0}}@keyframes pulse{0%,100%{box-shadow:0 0 5px red}50%{box-shadow:0 0 20px red}}
    
  p.mb-1 {
    font-size: large;
}  
     .custom-btn {
            /* width: 130px;
            height: 40px; */
            color: #fff;
            border-radius: 5px;
            /* padding: 10px 25px; */
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
      
        .btn-11.partnearray{
          background: navy!important;
        }
        .btn-11.categoryarray{
          background: #ff007f!important;
        }
         .card-body.text-center.p-2 {
    align-items: center;
    justify-content: center;
    display: flex;
    flex-direction: column;
}

tr,th,td{
  font-weight: 500;
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
        <section class="content request_section">
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

            <div class="text-center" style="background: linear-gradient(to right, #ede7f6, #ffffff); border-radius: 12px; box-shadow: 0 4px 8px rgba(0,0,0,0.1); padding: 20px;">

                <?php 
                if(in_array($user['type_id'],[1,2])){
                    $title_message = 'Holiday';
                }else{
                    $title_message = 'Leave';
                }
                ?>

                <h5>📅 <?=$title_message?> Management</h5>
                <small>📅 <?=$title_message?> Management helps employees apply for leaves 📝, track approvals ✅, and manage balance 📊 easily. It ensures smooth workflow 🔄, transparency 👀, and efficient HR operations 👩‍💼. 🌟</small>
            </div>
            <hr>



            <div class="row">
                <div class="col-md-8">
                    <div style="background: linear-gradient(to right, #ede7f6, #ffffff); border-radius: 12px; box-shadow: 0 4px 8px rgba(0,0,0,0.1); padding: 20px; align-items: center; justify-content: center; display: flex ; height: 100%;">

                    <?php if(in_array($user['type_id'],[1,2])){ ?>
                        <form style="width: 50%; background: linear-gradient(to right, #fff8e1, #ffffff); border-radius: 12px; box-shadow: 0 4px 8px rgba(0,0,0,0.1); padding: 20px;" action="<?=base_url()?>Menu/AddLeaveManagement" method="post" >
                            <input type="hidden" name="byusername" value="by_admin" required>
                            <div class="form-row">
                                <div class="form-group col-md-12">
                                <label for="HolidayName"><span class="text-danger">*</span> Holiday Name</label>
                                <input type="text" class="form-control" id="HolidayName" name="holiday_name" value="" placeholder="* Holiday Name" required>
                                </div>
                            </div>
                            <div class="form-row">
                                <div class="form-group col-md-6">
                                <label for="HolidaySDate"><span class="text-danger">*</span> Holiday Start Date</label>
                                <input type="date" class="form-control" name="leave_sdate" min="<?=date('Y-m-d')?>" id="HolidaySDate" placeholder="* Holiday Date" required>
                                </div>
                                <div class="form-group col-md-6">
                                <label for="HolidayEDate"><span class="text-danger">*</span> Holiday End Date</label>
                                <input type="date" class="form-control" name="leave_edate" min="<?=date('Y-m-d')?>" id="HolidayEDate" placeholder="* Holiday Date" required>
                                </div>
                            </div>
                            <div class="form-row">
                                <div class="form-group col-md-6">
                                <?php $zonesDatas = $this->Menu_model->GetAllZoneDatas();?>
                                <label for="inputState"><span class="text-danger">*</span> Zone</label>
                                <select id="inputState" class="form-control" name="leave_zone" required>
                                    <option value="all">---All--</option>
                                    <?php foreach($zonesDatas as $zonesData): ?>
                                    <option value="<?=$zonesData->id?>"><?=$zonesData->name?></option>
                                    <?php endforeach; ?>
                                </select>
                                </div>
                                <div class="form-group col-md-6">
                                    <div style="align-items: center; justify-content: center; display: flex ; height: 100%;">
                                        <button type="submit" class="custom-btn btn-11 partnearray p-2 mt-4">🏖️ Apply Holiday</button>
                                    </div>
                                </div>
                            </div>
                        </form>
                        <?php }else{ ?>
                            
                             <form style="width: 50%;background: linear-gradient(to right, #fff8e1, #ffffff); border-radius: 12px; box-shadow: 0 4px 8px rgba(0,0,0,0.1); padding: 20px;" action="<?=base_url()?>Menu/AddLeaveManagement" method="post">
                                <input type="hidden" name="byusername" value="by_user" required>
                                    <div class="form-row">
                                        <div class="form-group col-md-6">
                                        <label for="HolidaySDate"><span class="text-danger">*</span> Leave Start Date</label>
                                        <input type="date" class="form-control" name="leave_sdate" min="<?=date('Y-m-d')?>" id="HolidaySDate" placeholder="* Holiday Date" required>
                                        </div>
                                        <div class="form-group col-md-6">
                                        <label for="HolidayEDate"><span class="text-danger">*</span> Leave End Date</label>
                                        <input type="date" class="form-control" name="leave_edate" min="<?=date('Y-m-d')?>" id="HolidayEDate" placeholder="* Holiday Date" required>
                                        </div>
                                    </div>
                                    <div class="form-row">
                                        <div class="form-group col-md-12">
                                            <div class="mb-3">
                                            <label for="LeaveReson"><span class="text-danger">*</span> Leave Reson</label>
                                                <textarea class="form-control" id="LeaveReson" name="leave_reson" rows="3"></textarea>
                                            </div>                             
                                        </div>
                                    </div>
                                    <div class="form-row">
                                        <div class="form-group col-md-12">
                                            <div style="align-items: center; justify-content: center; display: flex ; height: 100%;">
                                                <button type="submit" class="custom-btn btn-11 partnearray p-2">🏖️ Apply Holiday</button>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            <?php } ?>


                    </div>     
                </div>
                <div class="col-md-4">
                      <div style="background: linear-gradient(to right, #ede7f6, #ffffff); border-radius: 12px; box-shadow: 0 4px 8px rgba(0,0,0,0.1); padding: 20px;">
                        <img src="<?=base_url()?>/uploads/profile_pics/leave.png" class="img-fluid" alt="">
                      </div>                  
                </div>
            </div>


            <hr>


             <div class="text-center" style="background: linear-gradient(to right, #ede7f6, #ffffff); border-radius: 12px; box-shadow: 0 4px 8px rgba(0,0,0,0.1); padding: 20px;">

            <div class="text-center" style="background: linear-gradient(to right, #fff8e1, #ffffff); border-radius: 12px; box-shadow: 0 4px 8px rgba(0,0,0,0.1); padding: 20px;">
             
                <?php 
                if(in_array($user['type_id'],[1,2])){
                    $title_message = 'Holiday';
                }else{
                    $title_message = 'Leave';
                }
                ?>

                <h5>📅 <?=$title_message?> Management Overview (<?=$cffYear?>)</h5>
                <small>📅  🌟 <?=$title_message?> Management Overview 📊 provides a complete view of employee leaves 🧑‍💼, including applications 📝, approvals ✅, balances 📅, and holiday schedules 🎉. It ensures smooth workflow 🔄, transparency 👀, and efficient HR management 👩‍💼.</small>
            </div>



<hr>

  <?php 
               $curYearDatasCount   = $leavsDatas['curYearDatasCount'];
               $curYearDatas        = $leavsDatas['curYearDatas'];
               $allyear             = $leavsDatas['allyear'];
               $allYearDatas        = $leavsDatas['allYearDatas'];

               

              $cyear                = $curYearDatasCount[0]->cyear;
              $total_leave          = $curYearDatasCount[0]->total;
              $pending_for_approved = $curYearDatasCount[0]->pending_for_approved;
              $approved             = $curYearDatasCount[0]->approved;

            ?>

  <div class="row">
                 <div class="col-md-4 mb-3">
                <div class="card text-center shadow text-dark" style="background: linear-gradient(to right, #dfe9f3, #ffffff); color: hsl(60, 88%, 38%);">
                  <div class="card-body" style="color:hsl(60, 88%, 38%)!important;flex-direction: column; justify-content: center;align-items: center;display: flex ;">
                    <h5 class="card-title"><b>🏖️ Total Holiday & Leave</b></h5>
                    <small>Total number of holiday & leave.</small> 
                    <hr>
                    <h3 class="card-text"><?=$total_leave?></h3>
                  </div>
                </div>
              </div>
             <div class="col-md-4 mb-3">
                <div class="card text-center shadow text-dark" style="background: linear-gradient(to right, #cff7ccff, #ffffff); color: hsla(111, 81%, 32%, 1.00);">
                  <div class="card-body" style="color:hsla(120, 81%, 32%, 1.00)!important;flex-direction: column; justify-content: center;align-items: center;display: flex ;">
                    <h5 class="card-title"><b>✅ 🏖️ Approved Leave</b></h5>
                    <small>Number of approved leave.</small> 
                    <hr>
                      <h3 class="card-text"><?=$approved?></h3>
                  </div>
                </div>
              </div>
              <div class="col-md-4 mb-3">
              <div class="card text-center shadow text-dark" style="background: linear-gradient(to right, #f7f8abff, #ffffff); color: hsl(60, 88%, 38%);">
                  <div class="card-body" style="color:hsl(60, 88%, 38%)!important;flex-direction: column; justify-content: center;align-items: center;display: flex ;">
                    <h5 class="card-title"><b>⏳ Pending For Approved Leave</b></h5>
                    <small>Number of pending for approved leave.</small> 
                    <hr>
                      <h3 class="card-text"><?=$pending_for_approved?></h3>
                  </div>
                </div>
              </div>
              </div>

               <hr>
                <div class="table-responsive text-nowrap">
                  <table id="example1" class="table table-striped table-bordered" cellspacing="0" width="100%">
                    <thead class="thead-dark">
                      <tr>
                      <th>🆔 ID</th>
                      <th>👤 Leave Add By</th>
                      <th>📋 Leave Name</th>
                      <th>🏷️ Leave Type</th>
                      <th>🌍 Zone</th>
                      <th>📅 Start Date</th>
                      <th>📅 End Date</th>
                      <th>📝 Reason</th>

                      <th>✅ Approved By Line Manager</th>
                      <th>✅ Approved Status By Line Manager</th>
                      <th>⏰ Approved Date By Line Manager</th>

                      <th>✅ Approved By Admin</th>
                      <th>✅ Approved Status By Admin</th>
                      <th>⏰ Approved Date By Admin</th>

                      <th>🕒 Created At</th>
                      <th>🕒 Updated At</th>
                      <?php if(!in_array($user['type_id'],[1,2,3])): ?>
                        <th>⚡ Action</th>
                      <?php endif; ?>
                       <?php if(in_array($user['type_id'],[1,2])): ?>
                        <th>⚡Final Action</th>
                      <?php endif; ?>

                       <?php if(in_array($user['type_id'],[1,2])): ?>
                        <th>🗑️ Delete</th>
                      <?php endif; ?>
                    </tr>
                    </thead>
                    <tbody>
                      <?php
                        $i = 1;
                       foreach ($curYearDatas as $row){

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

                      <td><?= $row->id ?></td>
                      <td><?= $row->add_by_name ?></td>
                      <td><?= $row->leave_name ?></td>
                      <td><?= $row->leave_type ?></td>
                        <td><?php if(!empty($row->leave_zone)){
                         if($row->leave_zone == 'all'){
                              $leave_zone = 'All Zone';
                          }else{
                              $leave_zone = $this->Menu_model->GetAllZoneDatasByID($row->leave_zone)[0]->name;
                          }
                          echo $leave_zone;
                       }?></td>
                      <td><?= $row->leave_sdate ?></td>
                      <td><?= $row->leave_edate ?></td>
                      <td><?= $row->leave_reson ?></td>
                      <td><?= !empty($row->approved_by_name) ? $row->approved_by_name : '<span class="bg-warning p-1 rounded">⏳ Pending</span>' ?></td>
                    
                    <td>
                      <?php 
                      if (!isset($row->leave_apr_status)) {
                        echo '<span class="bg-secondary p-1 rounded text-white">❓ Not Applied</span>';
                      } else if ($row->leave_apr_status == 0) {  
                        echo '<span class="bg-warning p-1 rounded">⏳ Pending</span>';
                      } else if ($row->leave_apr_status == 1) {  
                        echo '<span class="bg-success p-1 rounded text-white">✅ Approved</span>';
                      } else if ($row->leave_apr_status == 2) {  
                        echo '<span class="bg-danger p-1 rounded text-white">❌ Rejected</span>';
                      }
                      ?>
                    </td>

                    <td><?= !empty($row->leave_apr_date) ? $row->leave_apr_date : "—" ?></td>

                    <td><?php 

                        if ($row->leave_apr_status == 2){ 
                             echo '<span class="bg-danger p-1 rounded text-white">❌ Rejected By Line Manager</span>';
                         }else{
                           if(!empty($row->approved_by_admin_name)) {
                              echo $row->approved_by_admin_name;
                            }else{
                               echo '<span class="bg-warning p-1 rounded">⏳ Pending</span>';
                            }
                         }
                     ?></td>
                    <td>
                      <?php 

                      if (!isset($row->leave_apr_status_by_admin)) {
                        echo '<span class="bg-secondary p-1 rounded text-white">❓ Not Applied</span>';
                      } else if ($row->leave_apr_status_by_admin == 0) { 

                         if ($row->leave_apr_status == 2){ 
                             echo '<span class="bg-danger p-1 rounded text-white">❌ Rejected By Line Manager</span>';
                         }else{
                            echo '<span class="bg-warning p-1 rounded">⏳ Pending</span>';
                         }
                      } else if ($row->leave_apr_status_by_admin == 1) {  
                        echo '<span class="bg-success p-1 rounded text-white">✅ Approved</span>';
                      } else if ($row->leave_apr_status_by_admin == 2) {  
                        echo '<span class="bg-danger p-1 rounded text-white">❌ Rejected</span>';
                      }
                      ?>
                    </td>
                    <td>
                      
                    <?php 
                    if ($row->leave_apr_status == 2){ 
                             echo '<span class="bg-danger p-1 rounded text-white">❌ Rejected By Line Manager</span>';
                         }else{
                           if(!empty($row->leave_apr_date_by_admin)) {
                              echo $row->leave_apr_date_by_admin;
                            }else{
                               echo '<span class="bg-warning p-1 rounded">⏳ Pending </span>';
                            }
                         } 
                    
                    ?>
                  </td>

                      <td><?= $row->created_at ?></td>
                      <td><?= $row->updated_at ?></td>



                <?php if(!in_array($user['type_id'],[3])): ?>
                    <td>
                        <?php if (!in_array($user['type_id'], [1, 2, 3])): ?>
                            <?php if ($user['user_id'] !== $row->leave_by): ?>
                                <?php if (!in_array($row->leave_by, [2, 45, 100000])): ?>
                                    <?php if ($row->leave_apr_status == 0): ?>
                                        <div>
                                            <span class="btn btn-success rounded" onclick="ActionApprove(<?= $row->id ?>)">
                                                Approve
                                            </span>
                                        </div>
                                    <?php elseif ($row->leave_apr_status == 1): ?>
                                        <span class="p-1 bg-success mr-2 rounded">Approved</span>
                                    <?php elseif ($row->leave_apr_status == 2): ?>
                                        <span class="p-1 bg-danger mr-2 rounded">Rejected</span>
                                    <?php endif; ?>

                                <?php else: ?>
                                    <span class="bg-success p-1 rounded text-white">✅ Approved</span>
                                <?php endif; ?>

                            <?php else: ?>
                                <?php 
                                    if (!isset($row->leave_apr_status)) {
                                        echo '<span class="bg-secondary p-1 rounded text-white">❓ Not Applied</span>';
                                    } elseif ($row->leave_apr_status == 0) {
                                        echo '<span class="bg-warning p-1 rounded">⏳ Pending</span>';
                                    } elseif ($row->leave_apr_status == 1) {
                                        echo '<span class="bg-success p-1 rounded text-white">✅ Approved</span>';
                                    } elseif ($row->leave_apr_status == 2) {
                                        echo '<span class="bg-danger p-1 rounded text-white">❌ Rejected</span>';
                                    }
                                ?>
                            <?php endif; ?>

                        <?php 
                        
                        elseif (in_array($user['type_id'], [1, 2])): 
                        
                        if ($row->leave_apr_status == 1){ 
                        ?>
                            <?php if ($row->leave_apr_status_by_admin == 0): ?>
                                <div>
                                    <span class="btn btn-success rounded" onclick="FinalActionApprove(<?= $row->id ?>)">
                                        Approve
                                    </span>
                                </div>
                            <?php elseif ($row->leave_apr_status_by_admin == 1): ?>
                                <span class="p-1 bg-success mr-2 rounded">Approved</span>
                            <?php elseif ($row->leave_apr_status_by_admin == 2): ?>
                                <span class="p-1 bg-danger mr-2 rounded">Rejected</span>
                            <?php endif; ?>
                        <?php 
                        }else{
                          if ($row->leave_apr_status == 2){ 
                               echo '<span class="bg-danger p-1 rounded">❌ Rejected</span>';
                          }else{
                            echo '<span class="bg-warning p-1 rounded">⏳ Pending By Line Manager</span>';
                          }
                        }
                      endif; ?>
                        </td>
                       <?php endif; ?>

                      <?php if(in_array($user['type_id'],[1,2])): ?>
                        <td> 
                          <span class="btn btn-danger rounded" onclick="FinalActionDelete(<?= $row->id ?>)">
                              🗑️ Delete
                          </span>
                        </td>
                      <?php endif; ?>
                      
                    </tr>
                      <?php $i++; } ?>
                    </tbody>
                  </table>
                </div>
                <hr>
             </div>


              <div class="modal fade" id="exampleModalCenterdataApprove" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
                      <div class="modal-dialog modal-dialog-centered" role="document">
                        <div class="modal-content" style="background: linear-gradient(to right, #dfe9f3, #ffffff); color: hsla(111, 81%, 32%, 1.00);">
                          <div class="modal-header">
                            <h5 class="modal-title" id="exampleModalLongTitle">✅ Approve By Line Manager</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                            </button>
                          </div>
                          <div class="modal-body">
                           <form action="<?= base_url('Menu/ApproveByLineManager'); ?>" method="post">
                              <input type="hidden" id="approveidlm" name="approveidlm" value="">
                              <input type="hidden" id="approveby" name="approveby" value="">
                              <div class="mb-3 mt-3">
                                <label for="ApproveByLineManager">Approve Status</label>
                                <select class="form-control" id="ApproveByLineManager" name="ApproveByLineManager" required>
                                  <option value="">--Select--</option>
                                  <option value="1">Approved</option>
                                  <option value="2">Rejected</option>
                                </select>
                              <div class="form-group text-center">
                                <button type="submit" class="btn btn-success mt-2" id="approveBtnlm">Approve</button>
                              </div>
                            </form>
                          </div>
                        </div>
                      </div>
                    </div>
              </div>



                  <div class="modal fade" id="exampleModalCenterdataDelete" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
                      <div class="modal-dialog modal-dialog-centered" role="document">
                        <div class="modal-content" style="background: linear-gradient(to right, #f3dfe3ff, #f36767ff); color: hsla(111, 81%, 32%, 1.00);">
                          <div class="modal-header">
                            <h5 class="modal-title" id="exampleModalLongTitle">⚠️ Are you sure?</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                            </button>
                          </div>
                          <div class="modal-body">
                           <form action="<?= base_url('Menu/DeleteByLineManager'); ?>" method="post">
                              <input type="hidden" id="deleteid" name="deleteid" value="">
                              <div class="mb-3 mt-3">
                                <label for="delete_action">❌ You want to delete this record?</label>
                                <select class="form-control" id="delete_action" name="delete_action" required>
                                  <option value="">--Select--</option>
                                  <option value="yes">Yes</option>
                                </select>
                              <div class="form-group text-center">
                                <button type="submit" class="btn btn-danger mt-2" id="deleteBTN">Delete</button>
                              </div>
                            </form>
                          </div>
                        </div>
                      </div>
                    </div>
              </div>

              <br>
             <br>
             <br>


        </section>



        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
        <!-- /.row (main row) -->
      </div>
      <!-- /.container-fluid -->
      </section>
      <!-- /.content -->
    </div>

    <script>

$(document).ready(function(){
  $("#ApproveByLineManager").on("change", function(){
    let vallm = $(this).val();
    let btnlm = $("#approveBtnlm");

    if(vallm == "1"){  
      btnlm.text("Approved ✅").removeClass("btn-danger").addClass("btn-success");
    } 
    else if(vallm == "2"){  
      btnlm.text("Rejected ❌").removeClass("btn-success").addClass("btn-danger");
    } 
    else {
      btnlm.text("Approve").removeClass("btn-success btn-danger").addClass("btn-secondary");
    }
  });
});


  function ActionApprove(id){
          $('#exampleModalCenterdataApprove').modal('show');
          $('#approveidlm').val(id);
          $('#exampleModalLongTitle').text("✅ Approve By Line Manager");
          $('#approveby').val("Line Manager");
          }

  function FinalActionApprove(id){
          $('#exampleModalCenterdataApprove').modal('show');
          $('#approveidlm').val(id);
          $('#exampleModalLongTitle').text("✅ Approve By Admin");
          $('#approveby').val("Admin");
          }

  function FinalActionDelete(id){
          $('#exampleModalCenterdataDelete').modal('show');
          $('#deleteid').val(id);
          }
    </script>
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
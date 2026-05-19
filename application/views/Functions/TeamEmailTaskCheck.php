<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="wi$dth=device-wi$dth, initial-scale=1">
    <title>Team Email Task Validate | STEM APP | WebAPP</title>
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
        .star-rating {
        direction: rtl;
        display: inline-block;
        unicode-bidi: bidi-override;
        }
        .star-rating > input {
        display: none;
        }
        .star-rating > label {
        color: #ccc;
        font-size: 1.5em;
        cursor: pointer;
        }
        .star-rating > input:checked ~ label,
        .star-rating > input:checked ~ label ~ label {
        color: #ffc107;
        }
        .star-rating > label:hover,
        .star-rating > label:hover ~ label {
        color: #ffc107;
        }
      </style>
      <!-- /.navbar -->
      <!-- Content Wrapper. Contains page content -->
      <div class="content-wrapper">
        <section class="content mt-2">
          <div class="container-fluid">


           <br>
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
            <?php
            if ($this->session->flashdata('error_message')): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <strong> <?php echo $this->session->flashdata('error_message'); ?></strong>
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
            </button>
            </div>
            <?php endif; ?>


            <div class="row">
              <div class="col-12">
                <div class="card" style="background: linear-gradient(to right, #e7f6f1, #ffffff);">
                  <div class="card-header" style="background: linear-gradient(to right, #ede7f6, #ffffff); border-radius: 12px; box-shadow: 0 4px 8px rgba(0,0,0,0.1); padding: 20px;">
                   <center>
                      <h3 class="text-center m-3">📝 Team Email Task Validate ✅</h3>
                      <div style="width: 60%;">
                        <hr>
                        <span>
                          <small>
                            ✉️ Team Email Task Validate helps ensure every email task is verified, accurate, and delivered on time ⏰.  
                            Track each team member’s progress 📊, confirm email authenticity 🔍, and maintain communication standards 💬.  
                            Boost efficiency 🚀, avoid errors ❌, and ensure smooth coordination across the entire team 🤝.  
                            Stay compliant, confident, and connected — every email, every time ✅.
                          </small>
                        </span>
                      </div>
                    </center>
                    <div class="text-center mt-2">
                      <b><mark style="box-shadow: rgba(0, 0, 0, 0.12) 0px 1px 3px, rgba(0, 0, 0, 0.24) 0px 1px 2px;"><?=$sdate?></mark></b>
                    </div>
                  </div>
                  <hr>
                  <div class="row mb-2">
                    <hr>
                    <div class="text-right-data text-center" style="background: linear-gradient(to right, #b2d6ff, #ffffff); border-radius: 12px; box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1); padding: 20px; width: 60%;">
                      <?php 
                        $clusterPstDatas  = $this->Menu_model->GetAdminActiveTeam($user['user_id']);
                        $taskActions      = $this->Menu_model->get_action();
                        $userRoles        = $this->Menu_model->GetActiveDepartmentList($uid);
                        ?>
                      <div class="col text-center formcenterData">
                        <div>
                          <hr>
                          <form action="<?=base_url()?>Menu/TeamEmailTaskCheck" method="post" class="mt-3">
                            <div class="row mb-4">
                              <div class="col">
                                <label for="selectedDate">Start Date</label>
                                <?php  
                                $currentDate    = date("Y-m-d");
                                $checkDate      =  $this->Menu_model->findSpecialDate($currentDate);
                                 ?>
                                <input type="date" value="<?=$sdate;?>" id="selectedDate" max="<?=$checkDate?>" name="sdate" style="min-width:300px;" class="form-control">
                              </div>
                              <!-- <div class="col">
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
                                -->
                              <div class="col">
                                <?php $getTeamsDatas = $this->Menu_model->GetClusterAndPSTActiveTeam($admin_id_filter); ?>
                                <label for="selectedDate">Select User</label>
                                <select id="selectedBD" name="selectedBD" class="form-control">
                                  <option value="all" selected> All</option>
                                  <?php 
                                    /*
                                    if($user['type_id'] !== 3){ ?> 
                                  <option value="all" <?= ($rm_filter == 'all') ? 'selected' : ''; ?>>All</option>
                                  <?php } ?>
                                  <?php foreach ($getTeamsDatas as $getTeamsData) { ?>
                                  <option value="<?= $getTeamsData->user_id; ?>" <?= ($getTeamsData->user_id == $rm_filter) ? 'selected' : ''; ?>>
                                    <?= $getTeamsData->name; ?> (Team Wise)
                                  </option>
                                  <?php }
                                    */
                                    ?>
                                  <?php $getcTeamsDatas = $this->Menu_model->GetAllUserDataByAdminID($user['user_id']); ?>
                                  <?php foreach ($getcTeamsDatas as $getcTeamsData) { ?>
                                  <option value="<?= $getcTeamsData->user_id; ?>" <?= ($getcTeamsData->user_id == $selectedBD) ? 'selected' : ''; ?> >
                                    <?= $getcTeamsData->name; ?>
                                  </option>
                                  <?php }?>
                                </select>
                                <input type="hidden" id="rm_input" name="rm_input" class="form-control" placeholder="Enter value" />
                              </div>
                              <div class="col text-left">
                                <div class="form-group">
                                  <button type="submit" class="custom-btn btn-11 partnearray p-2" style="margin-top: 33px; width: 200px;">Filter</button>
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
                  <div class="row mt-3 mb-2">
                    <div class="col-md-12 text-center">
                      <button class="custom-btn btn-11 partnearray p-2" id="downloadPdf">Download PDF</button>
                    </div>
                  </div>
                  <?php 
                    //  dd($liveMettingsTasks);
                    
                    
                    // if($request_type == ''){
                    //     userwise
                    // }
                    
                    $totalResult = [];
                    $totalDoneResult = [];
                    foreach ($liveMettingsTasks as $item) {
                       if (!isset($totalResult[$item->task_user_id])) {
                           $totalResult[$item->task_user_id] = 0;
                       }
                       $totalResult[$item->task_user_id]++;
                    }
                    
                    ?>
                  <?php
                    // Define unique gradients
                    $gradients = [
                        'linear-gradient(to right, #ffecd2, #fcb69f)',
                        'linear-gradient(to right, #c2e9fb, #a1c4fd)',
                        'linear-gradient(to right, #fbc2eb, #a6c1ee)',
                        'linear-gradient(to right, #fdfbfb, #ebedee)',
                    ];
                    $gradientIndex = 0;
                    
                    ?>
                  <hr>
                  <div class="text-center" style="background: linear-gradient(to right, #ede7f6, #ffffff); border-radius: 12px; box-shadow: 0 4px 8px rgba(0,0,0,0.1); padding: 20px;">
                    <center>
                      <h5>🗂️ Task Type Summary 📋</h5>
                      <div style="width: 60%;">
                        <hr>
                        <small>
                        📌 Task Type Summary offers a quick breakdown of tasks by category 📂, helping you understand workload distribution ⚖️. Easily track how many tasks fall under each type ✅, spot trends 📊, and identify priority areas 🎯. This overview ensures better planning 📅, efficient resource allocation ⚙️, and improved team focus 🤝. Perfect for keeping your workflow organized 📋 and achieving goals with clarity and precision 🏆.
                        </small>
                      </div>
                    </center>
                  </div>
                  <hr>
                  <div class="row">
                    <?php  foreach ($totalResult as $key => $value) {
                      $r = rand(150, 255);
                      $g = rand(150, 255);
                      $b = rand(150, 255);
                      $backgroundColor = "rgba($r, $g, $b,.3)";
                      
                      $hue = rand(0, 360);
                      $saturation = rand(60, 100);
                      $lightness = rand(30, 45);
                      $backgroundColorNew = "hsl($hue, $saturation%, $lightness%)";
                      
                      $taskUserDatas      = $this->Menu_model->get_userbyid($key);
                      $taskUserName       = $taskUserDatas[0]->name;
                      $taskUserTypeID     = $taskUserDatas[0]->type_id;
                      $taskUserTypeName   = $this->Menu_model->get_user_types($taskUserTypeID)[0]->name;
                       
                        ?>
                    <div class="col-md-2 mb-2">
                      <div class="card text-center shadow taskusercardData" data-user_id = "<?=$key?>" style="background-color: <?= $backgroundColor ?>; color: <?= $backgroundColorNew ?>;min-height: 200px;">
                        <div class="card-body text-center" style="color:<?=$backgroundColorNew;?>!important">
                          <h5>
                            <?= $taskUserName ?> <br> 
                            <small>
                              <hr>
                              (<?=$taskUserTypeName?>)
                            </small>
                          </h5>
                          <hr>
                          <a target="_BLANK" class="card-count-heading-new" style="color:<?=$backgroundColorNew;?>!important" href="<?=base_url()?>Reports/GetTeamTaskOnSelfOrOtherFunnelTaskLists/total_task/<?=$key?>/<?=$sdate?>/<?=$sdate?>/all/userwise">
                          <span class="badge badge-pill badge-success"><?= $value ?></span>
                          </a>
                          <?php $checkDonesDatas =  $this->Menu_model->GetUserGivedTaskStarRatingsCount($key,$sdate); ?>
                          <hr>
                          <div class="row">
                            <div class="col-md-6">
                              <div class="text-center">
                                <small>Complete <br>
                                <span class="badge badge-pill badge-success"><?= $checkDonesDatas ?></span>
                                </small>
                              </div>
                            </div>
                            <div class="col-md-6">
                              <div class="text-center">
                                <small>Pending <br>
                                <span class="badge badge-pill badge-warning"><?= $value - $checkDonesDatas ?></span>
                                </small>
                              </div>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                    <?php } ?>
                  </div>
                  <div class="text-center" style="background: linear-gradient(to right, #ede7f6, #ffffff); border-radius: 12px; box-shadow: 0 4px 8px rgba(0,0,0,0.1); padding: 20px;">
                    <center>
                      <h5><i>📅 Task Management Overview</i></h5>
                      <div style="width: 60%;">
                        <hr>
                        <small>📊 Task Management Overview provides a clear snapshot of all ongoing, pending, and completed tasks ✅. It helps you prioritize work 🎯, track deadlines ⏳, and allocate resources efficiently 📋. With real-time updates 🔄, you can monitor progress, identify bottlenecks 🚧, and keep your team aligned 🤝. Stay organized, boost productivity 📈, and ensure every task moves smoothly toward completion 🏆 for successful project outcomes 🌟.</small>
                      </div>
                    </center>
                  </div>
                  <hr>


                    <style>
  /* Classical Colorful Animated Gradient */
  #mainreveiwsectionsarea_start {
    min-height: 60vh;
    align-items: center;
    justify-content: center;
    display: flex;
    border-radius: 12px;
    box-shadow: 0 4px 8px rgba(0,0,0,0.1);
    padding: 20px;

    background: linear-gradient(-45deg, 
      #fbc2eb, /* pastel pink */
      #a6c1ee, /* soft blue */
      #fdfd96, /* warm yellow */
      #ffecd2, /* peach */
      #d4fc79  /* light green */
    );
    background-size: 400% 400%;
    animation: gradientMove 12s ease infinite;
  }

  @keyframes gradientMove {
    0% {
      background-position: 0% 50%;
    }
    50% {
      background-position: 100% 50%;
    }
    100% {
      background-position: 0% 50%;
    }
  }

  #main_timer {
    font-size: 1.5rem;
    font-weight: bold;
    display: flex;
    justify-content: center;
    gap: 15px;
    color: #333;
  }

  #main_timer span {
    display: flex;
    align-items: baseline;
    gap: 3px;
  }

  .unit {
    font-size: 0.8rem;
    color: #666;
  }

  /* Planner Box */
  #plantimerBox {
    border-radius: 12px;
    padding: 15px;
    color: #fff;
    background: linear-gradient(-45deg, #ff9a9e, #fad0c4, #a1c4fd, #c2e9fb);
    background-size: 400% 400%;
    animation: gradientMove 12s ease infinite;
    box-shadow: 0 4px 10px rgba(0,0,0,0.1);
  }

  /* Gradient animation */
  @keyframes gradientMove {
    0% { background-position: 0% 50%; }
    50% { background-position: 100% 50%; }
    100% { background-position: 0% 50%; }
  }

  /* Timer Row Styling */
  .pllanerseesioncnt h3 {
    font-size: 1.2rem;
    font-weight: bold;
    margin: 0;
    display: flex;
    align-items: center;
    height: 100%;
  }

  #timer {
    font-size: 1.8rem;
    font-weight: bold;
  }

  .stopbtntimer {
    display: flex;
    align-items: center;
    justify-content: flex-end;
  }

  /* Responsive */
  @media (max-width: 768px) {
    .stopbtntimer {
      justify-content: center;
      margin-top: 10px;
    }
    .pllanerseesioncnt h3 {
      justify-content: center;
    }
  }
  p.functionID_class a {
    color: white !important;
}
</style>

              <div class="col-md-12 plantimer text-center p-2 mb-2" id="plantimerBox">
                <div class="row align-items-center">
                  <div class="col-md-2 pllanerseesioncnt">
                    <h3 id="PlannerSessionStimer">Session Time</h3>
                  </div>
                  <div class="col-md-8">
                    <span id="timer">00:00:00</span>
                  </div>
                  <div class="col-md-2 stopbtntimer">
                    <button type="button" class="custom-btn btn-11" style="background: linear-gradient(0deg, #fb3d21 0, #ea534c 100%);" id="stop">🛑 Pause Task Check Session</button>
                  </div>
                </div>
              </div>

              <div class="col-sm col-md-12 col-lg-12 m-auto card" id="mainreveiwsectionsarea_start">
                <div class="planningTime">
                  <button type="button" class="custom-btn btn-11 partnearray p-2" id="start">🚀 Start Task Check Session</button>
                </div>
              </div>



 <div id="totalContentArea">
                  <style>
                    .taskusercardData {
                    transition: transform 0.3s, box-shadow 0.3s;
                    border-radius: 10px;
                    border: none;
                    cursor: pointer;
                    }
                    .taskusercardData:hover {
                    transform: translateY(-5px);
                    box-shadow: 0 10px 20px rgba(0, 0, 0, 0.2);
                    }
                    .card-body {
                    padding: 20px;
                    }
                    .tab-content {
                    display: none;
                    padding: 20px;
                    background-color: #f8f9fa;
                    border-radius: 10px;
                    margin-top: 20px;
                    animation: fadeIn 0.5s;
                    }
                    .tab-content.active {
                    display: block;
                    }
                    @keyframes fadeIn {
                    from {
                    opacity: 0;
                    }
                    to {
                    opacity: 1;
                    }
                    }
                  </style>
                  <?php foreach ($totalResult as $key => $value) { ?>
                  <div class="tab-content" id="tab-<?=$key?>">
                    <?php 
                      $taskUserDatas      = $this->Menu_model->get_userbyid($key);
                      $taskUserNames       = $taskUserDatas[0]->name;
                      $taskUserTypeIDs     = $taskUserDatas[0]->type_id;
                      $taskUserTypeNames   = $this->Menu_model->get_user_types($taskUserTypeIDs)[0]->name;
                      ?>
                    <hr>
                    <div class="text-center">
                      <center>
                        <h5>
                          <i>📅 Task Check Of <span style="color:#e52e71"><?=$taskUserNames?></span> </i>
                          <span>
                            <hr style="width: 40%;">
                            🎯 <?=$taskUserTypeNames?>
                          </span>
                        </h5>
                        <hr>
                      </center>
                    </div>
                    <?php
                      $gradientColors = [
                          'linear-gradient(to right,rgb(255, 223, 246),rgb(255, 255, 255))',
                          'linear-gradient(to right,rgb(233, 255, 182),rgb(249, 255, 250))',
                          'linear-gradient(to right,rgb(203, 255, 222),rgb(248, 253, 255))',
                          'linear-gradient(to right,rgb(255, 229, 198),rgb(255, 255, 255))',
                          'linear-gradient(to right,rgb(238, 255, 191),rgb(249, 252, 255))',
                          'linear-gradient(to right,rgb(203, 255, 191),rgb(249, 252, 255))',
                          'linear-gradient(to right,rgb(255, 203, 188),rgb(249, 252, 255))',
                          'linear-gradient(to right,rgb(255, 191, 191),rgb(249, 252, 255))',
                          'linear-gradient(to right,rgb(255, 251, 191),rgb(249, 252, 255))',
                          'linear-gradient(to right,rgb(191, 236, 255),rgb(249, 252, 255))',
                          'linear-gradient(to right,rgb(251, 193, 221),rgb(249, 252, 255))',
                          'linear-gradient(to right,rgb(221, 214, 240),rgb(249, 252, 255))',
                      ];
                      
                      $index = 0;
                      // dd($liveMettingsTasks);
                      ?>
                    <div class="row">
                      <?php
                        $j=1;
                        foreach ($liveMettingsTasks as $task):
                        
                          if($task->task_user_id != $key){ continue; }
                          $selectby = $task->selectby;
                        
                        ?>
                      <div class="col-md-6 mb-2">
                        <div class="card meetingslist-card <?=$alertClass;?>" style="background: <?= $gradientColors[$index++ % count($gradientColors)] ?>; border-radius: 14px; box-shadow: 0 4px 12px rgba(0,0,0,0.1); padding: 20px;">
                          <div class="text-left">
                            <center>
                              <h3 class="mb-1 badge badge-pill badge-dark" style="font-weight: 600; font-size: 1.1rem;"><?= htmlspecialchars($j) ?></h3>
                              <hr style="width: 20%;" class="cardline <?=$hrAClass?>">
                              <h4 class="mb-1" style="font-weight: 600;">🏷️ 
                                <a target="_BLANK" href="<?=base_url();?>/Menu/CompanyDetails/<?=$task->cid?>">
                                <?=$task->compname?> (<?=$task->cid?>)
                                </a>
                              </h4>
                              <hr style="width: 40%;" class="cardline <?=$hrAClass?>">
                              <h5 class="mb-1" style="font-weight: 600;">🏷️ <?=$task->task_name?></h5>
                              <hr style="width: 60%;" class="cardline <?=$hrAClass?>">
                              <?= $task->nextCFID == 0 ? "<span class='p-1 bg-warning'>Pending</span>" : "<span class='p-1 bg-success'>Complete</span>" ?>
                            </center>
                            <div class="table-responsive text-nowrap">
                              <table id="table_id_<?=$key?>_<?=$j?>" class="table table-striped table-bordered" cellspacing="0" width="100%">
                                <thead class="thead-dark">
                                  <tr>
                                    <th>Data</th>
                                    <th>Action</th>
                                   
                                  </tr>
                                </thead>
                                <tbody>
                                  <!-- Task User -->
                                  <tr>
                                    <td><strong>Task User:</strong>  </td>
                                    <td><?= htmlspecialchars($task->task_username) ?> (<span style="color: navy;"><?= $task->task_user_id ?></span>)</td>
                              
                                  </tr>
                                  <!-- Plan Date Time -->
                                  <tr>
                                    <td><strong>Plan Date Time:</strong> </td>
                                    <td><?= $task->appointmentdatetime ?></td>
                                   
                                  </tr>
                                  <!-- Initiate Task -->
                                  <tr title="<?=$task->task_id?>">
                                    <td><strong>Initiate <?= $task->task_name ?>:</strong>  </td>
                                    <td> <?php 
                                        if (empty($task->task_initiateddt) || is_null($task->task_initiateddt)) {
                                          $appointment = new DateTime($task->task_updateddate);
                                          $appointment->modify("-2 minutes");
                                          $task_initiateddt = $appointment->format("Y-m-d H:i:s");
                                        
                                          } else {
                                              $task_initiateddt = $task->task_initiateddt;
                                          }
                                        
                                        ?>
                                      <?= $task_initiateddt ?></td>
                                    
                                  </tr>
                                  <?php if($task->nextCFID !=0){ ?>
                                  <!-- Complete Task -->
                                  <tr>
                                    <td><strong>Complete <?= $task->task_name ?>:</td>
                                    <td></strong> <?= $task->nextCFID != 0 ? $task->task_updateddate : "NA" ?></td>
                                    
                                  </tr>
                                  <?php } ?>


                                   <tr>
                                    <td><strong>Task Status:</strong> </td>
                                    <td><?= $task->nextCFID == 0 ? "<span class='p-1 bg-warning'>Pending</span>" : "<span class='p-1 bg-success'>Complete</span>" ?></td>
                                  </tr>
                                  <tr>
                                    <td>
                                      <strong>Action Taken:</strong> <hr>
                                      <strong>Purpose Achieved:</strong> 
                                    </td>
                                    <td>
                                      <span class='p-1 bg-success'><?= $task->actontaken ?></span> <hr>
                                    <span class='p-1 bg-success'><?= $task->purpose_achieved ?></span>
                                      </td>
                                  </tr>
                                 
                                   <tr>
                                    <td>
                                      <strong>Email looping with Sales Coordinator ?</strong></span>
                                    </td>
                                    <td>
                                      <?php if($task->attech == ''){ ?>
                                          <p id="functionID_<?= $task->task_id;?>">
                                            <span class="bg-danger p-1 functionID_class" id="functionID_<?= $task->task_id;?>" onclick="ClickToValidateEmail('<?= $task->task_id;?>')" > Click To Validate </span>
                                          </p>

                                        <?php }else{ ?> 

                                           <p class ="bg-success p-1 functionID_class text-center">
                                            <a href="<?= $task->attech; ?>" target="_blank" class="text-primary fw-bold">
                                              View Validate File
                                            </a>
                                          </p>
                                          <?php }?> 
                                      
                                    </td>
                                  </tr>



                                  <?php if($task->nextCFID !=0){ ?>
                                  <!-- Complete Task -->
                                  <tr>
                                    <td><strong>Time Diffrence Between Plan VS Initiate</td>
                                    <td>
                                      <?php 
                                        $start_ap = new DateTime($task->appointmentdatetime);
                                        // $start = new DateTime($task->task_updateddate);
                                        $end_ap = new DateTime($task_initiateddt);
                                        
                                        $diff_pi = $end_ap->diff($start_ap);
                                        
                                        echo $diff_pi->format('%h hours %i minutes %s seconds');
                                        // Output: 0 hours 2 minutes 0 seconds
                                        ?>
                                    </td>
                                    
                                  </tr>
                                  <?php } ?>
                                  <?php if($task->nextCFID !=0){ ?>
                                  <!-- Complete Task -->
                                  <tr>
                                    <td><strong>Time Diffrence Between Initiate VS completed ?</td>
                                    <td>
                                      <?php 
                                        $start_ap = new DateTime($task->task_updateddate);
                                        $end_ap = new DateTime($task_initiateddt);
                                        
                                        $diff_pi = $end_ap->diff($start_ap);
                                        
                                        echo $diff_pi->format('%h hours %i minutes %s seconds');
                                        // Output: 0 hours 2 minutes 0 seconds
                                        ?>
                                    </td>
                                    
                                  </tr>
                                  <?php } ?>
                                  <!-- Planner Filter Used -->
                                  <tr>
                                    <td><strong>Planner Filter Used:</strong>
                                     
                                    </td>
                                    <td> <?= !empty($selectby) ? $selectby : 'NA'; ?></td>
                                   
                                  </tr>
                                  <!-- Current Status -->
                                  <tr>
                                    <td><strong>Current Status:</strong> </td>
                                    <td><?= $task->current_status ?></td>
                                    
                                  </tr>
                                  <!-- Current Status -->
                                  <tr>
                                    <td><strong>Task Planned on Status:</strong> </td>
                                    <td><?= $task->task_time_status ?></td>
                                    
                                  </tr>


                                   <?php if($task->nextCFID != 0){ ?>
                                  <!-- Update Status -->
                                  <?php if($task->task_time_new_status !==''): ?>
                                  <tr>
                                    <td><strong>Update Status:</strong></td>
                                    <td> <?= !empty($task->task_time_new_status) ? $task->task_time_new_status : 'NA' ?></td>
                                   
                                  </tr>
                                  <?php endif; } ?>
                                  <!-- Approved By -->



                                  <tr>
                                    <td><strong>Approved By:</strong> </td>
                                    <td><?= !empty($task->task_approved_by) ? $task->task_approved_by : 'NA' ?></td>
                                   
                                  </tr>
                                  <!-- Approved Date -->
                                  <tr>
                                    <td><strong>Approved Date:</strong>  </td>
                                    <td><?= !empty($task->task_approved_date) ? $task->task_approved_date : 'NA' ?></td>
                                   
                                  </tr>
                                  <?php  
                                    $aftertasklists = $this->Menu_model->GetAfterTasksLists($task->task_id);
                                    if(sizeof($aftertasklists) > 0){ 
                                    ?>
                                  <tr>
                                    <td>
                                      <?php foreach($aftertasklists as $aftertasklist){ ?> 
                                      <strong>After task :</strong>  <?= $aftertasklist->after_task_name ?> <br>
                                      <strong>Task Completed :</strong>  
                                      <?php if($aftertasklist->nextCFID > 0){ ?> 
                                      <span class="bg-success p-1">Complete</span>
                                      <?php }else{ ?>
                                      <span class="bg-warning p-1">Pending</span> 
                                      <?php } ?>
                                      <br>
                                      <strong>Action Taken :</strong> <?=$aftertasklist->actontaken;?> <br>
                                      <strong>Purpose Achieved  :</strong> <?=$aftertasklist->purpose_achieved;?> <br>
                                      <strong>Appointment datetime :</strong> <?=$aftertasklist->appointmentdatetime;?> <br>
                                      <?php if($aftertasklist->nextCFID > 0){ ?> 
                                      <strong>Complete datetime :</strong> <?=$aftertasklist->updateddate;?> <br>
                                      <strong>Complete Remarks :</strong> <?=$aftertasklist->remarks;?> <br>
                                      <?php } ?>
                                      <hr>
                                      <?php } ?>
                                    </td>
                                    <td></td>
                                    
                                  </tr>
                                  <?php } ?>
                                  <!-- Task Status -->
                                 
                      



                           

                                  <!-- Remarks -->
                                  <?php if($task->nextCFID != 0): ?>
                                  <tr>
                                    <td><strong>Remarks:</strong> <span><?= $task->remarks ?></span></td>
                                    <td></td>
                                   
                                  </tr>
                                  <?php endif; ?>
                                  <!-- Late/Early Remarks -->
                                  <?php if(!in_array($task->actiontype_id,[3,4])){ ?>
                                    <?php if($task->nextCFID != 0){ ?>
                                  <tr>
                                    <td><strong>Late/Early Remarks:</strong> <span><?= $task->late_remarks_message ?></span></td>
                                    <td></td>
                                   
                                  </tr>
                                  <?php }} ?>



                               

             








                                </tbody>
                              </table>
                            </div>
                          </div>
                        </div>
                      </div>
                      <?php $j++; endforeach; ?>
                    </div>
                  </div>
                  <?php } ?>
                </div>

                 <div class="modal fade" id="exampleModalCenterdata" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered" role="document">
                                <div class="modal-content">
                                  <div class="modal-header">
                                    <h5 class="modal-title" id="exampleModalLongTitle">Validate Email Or Sent ?</h5>
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                      <span aria-hidden="true">&times;</span>
                                    </button>
                                  </div>
                                  <div class="modal-body">
                                  <form id="emailForm" action="<?= base_url(); ?>Menu/ValidateEmailOrSent" method="post" enctype="multipart/form-data">
                                        <input type="hidden" id="rejectid" value="" name="email_task_id">
                                        <div class="mb-3">
                                          <label for="formFile" class="form-label">Upload Email Screenshot to Email looping with Sales Coordinator </label>
                                          <input class="form-control" type="file" id="formFile" name="formFile">
                                        </div>
                                         <div class="mb-3 mt-3">
                                          <textarea id="rejectreamrk" name="email_task_remarks" cols="30" placeholder="Add Remark " class="form-control"  rows="4"></textarea>
                                        </div>
                                        <div class="form-group text-center">
                                            <button type="submit" class="btn btn-success mt-2">Submit</button>
                                        </div>
                                   </form>
                                  </div>
                                </div>
                              </div>
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
    <!-- Remarks Modal -->
    <div class="modal fade" id="remarksModal" tabindex="-1" role="dialog" aria-labelledby="remarksModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
    <div class="modal-content">
    <div class="modal-header">
    <h5 class="modal-title" id="remarksModalLabel">Add Remarks</h5>
    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
    <span aria-hidden="true">&times;</span>
    </button>
    </div>
    <div class="modal-body">
    <input type="hidden" value="" id="cquestion" required>
    <input type="hidden" value="" id="ctaskID" required>
    <textarea class="form-control" id="remarksText" required name="remarks" rows="3"></textarea>
    <br>
    <center>
    <button type="button" id="submitRemarks" class="view-data custom-btn btn-11 partnearray p-1">Submit</button>
    </center>
    </div>
    </div>
    </div>
    </div>
    <!-- /.container-fluid -->
    </section>
    </div>    
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
    <script>

      function ClickToValidateEmail(task_id){
                $('#exampleModalCenterdata').modal('show');
                $('#rejectid').val(task_id);
            }


            


     $(document).ready(function() {


       $("#emailForm").on('submit', function(e){
            e.preventDefault(); // prevent normal form submission

            var formData = new FormData(this);

            $.ajax({
              url: $(this).attr('action'),
              type: 'POST',
              data: formData,
              contentType: false,
              processData: false,
              beforeSend: function(){
                // Optional: show loader or disable submit button
                $('button[type="submit"]').prop('disabled', true).text('Submitting...');
              },
              success: function(response){
                // Handle success response
                
                var res = JSON.parse(response);
                if (res.status === 'success') {
                  // ✅ Show uploaded file info
                var email_task_id = $("#rejectid").val();
                $("#functionID_"+email_task_id).html("");

                  var fileTag = `
                    <div class="mt-3 alert alert-success">
                      <strong>${res.message}</strong><br>
                      <a href="${res.file_url}" target="_blank" class="text-primary fw-bold">
                        View Uploaded File
                      </a>
                    </div>
                  `;
                  alert("Form submitted successfully!");
                  $("#emailForm")[0].reset();
                  $('#exampleModalCenterdata').modal('hide');
                  // append or replace result view
                  $("#functionID_"+email_task_id).html(fileTag);
                } else {
                  // ❌ Show alert message
                  alert(res.message);
                }
              },
              error: function(xhr, status, error){
                // Handle error
                alert("Something went wrong!");
                console.log(error);
              },
              complete: function(){
                // Re-enable button
                $('button[type="submit"]').prop('disabled', false).text('Submit');
              }
            });
          });

          // On click, save the userId to localStorage and apply the changes
          $('.taskusercardData').click(function() {
              var userId = $(this).data('user_id');

               var savedUserId = localStorage.getItem('selectedUserId');
              if (savedUserId) {

                    localStorage.removeItem('selectedUserId');
                    localStorage.setItem('selectedUserId', userId);
                    
                    var timerval = $("#timer").text();

                    if (typeof timerval !== 'undefined' && timerval !== '00:00:00') {
                        $.ajax({
                          url:'<?=base_url();?>Menu/session_plan_time_close_task_check',
                          type: "POST",
                          data: {
                            savedUserId: savedUserId,
                            close: timerval,
                            check_type: 'task check',
                            check_date: "<?=$sdate?>"
                          },
                          cache: false,
                          success: function a(result){
                          }
                          });
                      }

                      var savedUserId = localStorage.getItem('selectedUserId');

                        $("#totalContentArea").hide();
                        $("#mainreveiwsectionsarea_start").show();
                        $("#plantimerBox").hide();

                        // Assign New BD 
                        $('.tab-content').removeClass('active');
                        $('#tab-' + userId).addClass('active');
                        $('.taskusercardData').css('background-color', '');
                        $(this).css('background-color', '#ffeb3b');

                       resetTimer();
                      //  clearInterval(x);

              }else{
                  // Store clicked userId in localStorage
                  localStorage.setItem('selectedUserId', userId);
                  $('.tab-content').removeClass('active');
                  $('#tab-' + userId).addClass('active');
                  $('.taskusercardData').css('background-color', '');
                  $(this).css('background-color', '#ffeb3b');
              }

          });


        $("#totalContentArea").hide();
        $("#mainreveiwsectionsarea_start").show();
        $("#plantimerBox").hide();

          var timerInterval;
          var startTime;
            // Function to update the timer every second
            function updateTimer() {
                clearInterval(timerInterval); // Clear any existing interval to prevent multiple intervals
                timerInterval = setInterval(function() {
                    var currentTime = new Date();
                    var elapsedTime = currentTime - startTime;
                    var seconds = Math.floor((elapsedTime / 1000) % 60);
                    var minutes = Math.floor((elapsedTime / (1000 * 60)) % 60);
                    var hours = Math.floor((elapsedTime / (1000 * 60 * 60)) % 24);
                    $('#timer').text(
                        (hours < 10 ? "0" + hours : hours) + ":" +
                        (minutes < 10 ? "0" + minutes : minutes) + ":" +
                        (seconds < 10 ? "0" + seconds : seconds)
                    );
                }, 1000);
            }

             function toggleFormVisibility() {
                if (startTime) {
                  $("#totalContentArea").show();
                  $("#mainreveiwsectionsarea_start").hide();
                  $("#plantimerBox").show();

                   var savedUserId = localStorage.getItem('selectedUserId');
                    if (savedUserId) {
                        $('.tab-content').removeClass('active');
                        $('#tab-' + savedUserId).addClass('active');
                        $('.taskusercardData').css('background-color', '');
                        $('.taskusercardData[data-user_id="' + savedUserId + '"]').css('background-color', '#ffeb3b');
                    }

                    $.ajax({
                        url:'<?=base_url();?>Menu/session_plan_time_start_task_check_count',
                        type: "POST",
                        data: {
                          check_type: 'task check',
                          check_date: "<?=$sdate?>",
                        },
                        cache: false,
                        success: function a(result){
                          var plannersession = parseInt(result)+1;
                          $("#PlannerSessionStimer").text('Session : ' + plannersession);
                        }
                        });

                } else {
                  
                  $("#totalContentArea").hide();
                  $("#mainreveiwsectionsarea_start").show();
                  $("#plantimerBox").hide();

                  $('nav > ul> li > a').off('click'); 
                  $(document).off('keydown'); 
                   
                }
            }


             // Initialize the timer from local storage if the start button was previously clicked
            if (localStorage.getItem('timerStartTime_TaskCheck')) {
                startTime = new Date(localStorage.getItem('timerStartTime_TaskCheck'));
                updateTimer();
                toggleFormVisibility();
            }

            $('#start').click(function() {
                if (!startTime) {

                    var savedUserId = localStorage.getItem('selectedUserId');
                    if (!savedUserId){
                      alert(" * Please Select BD");
                      return false;

                    } 

                    startTime = new Date();
                    localStorage.setItem('timerStartTime_TaskCheck', startTime);
                    updateTimer();
                    toggleFormVisibility();
                    alert("Task Check Timer started!");
                    
                    var plannersession = 1;
                        plannersession  = plannersession+1;
                    $("#PlannerSessionStimer").text('Session :' + plannersession);

                    
                    $.ajax({
                        url:'<?=base_url();?>Menu/session_plan_time_start_taskcheck',
                        type: "POST",
                        data: {
                          savedUserId: savedUserId,
                          start: 'start',
                          check_type: 'task check',
                          check_date: "<?=$sdate?>",
                        },
                        cache: false,
                        success: function a(result){
                        }
                        });

                }
            });


            // Stop button click event
            $('#stop').click(function() {
              
                if (startTime) {
                  var timerval = $("#timer").text();
                  
                    resetTimer();

                    alert("Task Check Timer stopped and reset!");

                    var savedUserId = localStorage.getItem('selectedUserId');

                     $.ajax({
                        url:'<?=base_url();?>Menu/session_plan_time_close_task_check',
                        type: "POST",
                        data: {
                          savedUserId: savedUserId,
                          close: timerval,
                          check_type: 'task check',
                          check_date: "<?=$sdate?>"
                        },
                        cache: false,
                        success: function a(result){
                          // location.reload();
                        }
                        });

                      if (savedUserId) {
                        localStorage.removeItem('selectedUserId');
                        $('.taskusercardData').css('background-color', '');
                        $('.tab-content').removeClass('active');
                      }

                        // document.getElementById("demo").innerHTML = "Time Spent in Task Review: " + "00:00:00";
                        $("#totalContentArea").hide();
                        $("#mainreveiwsectionsarea_start").show();
                        $("#plantimerBox").hide();
                }
            });


               // Function to reset the timer
            function resetTimer() {
                clearInterval(timerInterval); // Stop the timer
                startTime = null;
                localStorage.removeItem('timerStartTime_TaskCheck'); // Clear the start time from local storage
                $('#timer').text("00:00:00"); // Reset the timer display
                toggleFormVisibility();
                  
            }
           
            // $('#closereviewbtn').on('click', function(event) {
            //       resetTimer();
            // });

      });
      
      
      
      document.querySelectorAll('.star-rating input[type="radio"]').forEach(input => {
      input.addEventListener('change', function() {
        const starRatingSpan = this.closest('.star-rating');
        const allInputs = starRatingSpan.querySelectorAll('input[type="radio"]');
      
        allInputs.forEach(radioInput => {
            radioInput.disabled = true;
        });
      });
      });
      
      
    </script>
    <script>
      $('#submitRemarks').on('click', function () {
        let question  = $('#cquestion').val();
        let taskId    = $('#ctaskID').val();
        let remarks   = $('#remarksText').val();
      
        if (question === '' || taskId === '') {
            alert("Required fields are missing.");
            return;
        }
      
        $('#remarksModal').modal('hide');
        $('#remarksText').val('');
      
        $.ajax({
            url: '<?= base_url("Menu/StoreTaskCheckingStarRatingByPage") ?>', // update with actual path
            type: 'POST',
            data: {
                question: question,
                taskId: taskId,
                sdate: "<?=$sdate?>",
                remarks: remarks
            },
            success: function (response) {
                
            }
        });
      });
      $(document).ready(function() {
      $('.star-rating input').on('click', function() {
      var rating = $(this).val();
      var taskID = $(this).data('task-user-id');
      var paragraphText = $(this).closest('.star-rating').data('paragraph-text');
      var question = $(this).closest('.star-rating').data('paragraph-question');
      
      if (rating <= 2) {
      alert("* Please explain why you are giving only 2 stars or below.");
      $('#remarksModal').modal('show');
      $("#ctaskID").val(taskID);
      $("#cquestion").val(question);
      
      }

      if(question == 'Why was not the task completed? What star rating should we give for this?'){
        if(rating > 2){
          alert("Was the task not completed? Can you give it a star rating of 2 or less?");
          return false;
        }
      }
      
      $.ajax({
      url: '<?=base_url().'Menu/StoreTaskCheckingStarRatingByPage'?>', // Replace with your AJAX endpoint
      type: 'POST',
      data: {
      rating: rating,
      taskID: taskID,
      paragraph_text: paragraphText,
      question: question,
      sdate: "<?=$sdate?>"
      },
      success: function(response) {
      console.log('AJAX request successful', response);
      },
      error: function(xhr, status, error) {
      console.error('AJAX request failed', error);
      }
      });
      
      
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
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.4.0/jspdf.umd.min.js"></script>
    <?php
      // build an array of the table IDs that exist in your markup
      $tableIds = [];
      foreach ($totalResult as $key => $value) {
          $jk = 1;
          foreach ($liveMettingsTasks as $task) {
              $tableIds[] = "table_id_{$key}_{$jk}";
              $jk++;
          }
      }
      ?>
    <script>
      jQuery(function($){
        var ids = <?php echo json_encode($tableIds, JSON_UNESCAPED_SLASHES); ?>;
      
        if (typeof $.fn.DataTable !== 'function') {
          console.error('DataTables is not loaded. Include DataTables JS (and Buttons extension).');
          return;
        }
      
        ids.forEach(function(id){
          var $t = $('#' + id);
          if (!$t.length) {
            console.warn('Table not found in DOM:', id);
            return;
          }
      
          // If already initialized, destroy first to avoid duplicate init errors
          if ($.fn.DataTable.isDataTable($t)) {
            try { $t.DataTable().destroy(); } catch (e) { /* ignore */ }
          }
      
          var dt = $t.DataTable({
            "responsive": false,
            "lengthChange": false,
            "autoWidth": false,
            "ordering": false,       // Optional: remove if you want sorting
            "info": false,           // Optional: remove if you want bottom info
            "pageLength": 10,           // ensures Buttons container is created
            buttons: ['csv', 'excel', 'pdf']
          });
      
          // preferred place for buttons (same selector you used before)
          var $place = $('#' + id + '_wrapper .col-md-6:eq(0)');
          if ($place.length) {
            dt.buttons().container().appendTo($place);
          } else {
            // fallback: append to wrapper if the expected column is not present
            dt.buttons().container().appendTo($('#' + id + '_wrapper'));
          }
        });
      });
    </script>
    <script>
      $(document).ready(function () {
        $('#downloadPdf').click(function () {
          const button = $(this);
          button.prop('disabled', true).text('Please wait...');
      
          const { jsPDF } = window.jspdf;
          const doc = new jsPDF('p', 'mm', 'a4');
          const content = document.querySelector('.content-wrapper');
      
          html2canvas(content, {
            scale: 2,
          }).then(canvas => {
            const imgData = canvas.toDataURL('image/png');
            const imgWidth = 210;
            const pageHeight = 297;
            const imgHeight = canvas.height * imgWidth / canvas.width;
      
            let heightLeft = imgHeight;
            let position = 0;
      
            doc.addImage(imgData, 'PNG', 0, position, imgWidth, imgHeight);
            heightLeft -= pageHeight;
      
            while (heightLeft > 0) {
              position -= pageHeight;
              doc.addPage();
              doc.addImage(imgData, 'PNG', 0, position, imgWidth, imgHeight);
              heightLeft -= pageHeight;
            }
      
            doc.save('dashboard.pdf');
            // Revert button
            button.prop('disabled', false).text('Download PDF');
          }).catch(error => {
            console.error('PDF generation failed:', error);
            button.prop('disabled', false).text('Download PDF');
          });
        });
      });
      
      
      
    </script>
  </body>
</html>
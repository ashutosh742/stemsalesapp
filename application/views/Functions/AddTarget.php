<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="wi$dth=device-wi$dth, initial-scale=1">
    <title>Add Target | STEM APP | WebAPP</title>
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
     .card-header,.col.card.coladjust1 select,.col.card.coladjust31 select,.container-fluid.body-content.mt-4,.each,.form-row>.col,.form-row>[class*=col-],.table-striped tbody tr:nth-of-type(odd),input.form-control,table.table-bordered.dataTable{box-shadow:inset 4px 4px 7px rgba(55,84,170,.15),inset -4px -4px 10px #fff,0 0 2px rgba(255,255,255,.2);transition:box-shadow .2s ease-in-out}.scrollme{overflow-x:auto}.card-body{background:azure}.formSection{align-items:center;justify-content:center;display:flex}.form-control{width:200px!important}.modal-content{background:#faebd7}.coladjust{padding-right:5px;padding-left:5px;height:143px;align-items:center;justify-content:center;color:#000}.col.card.coladjust1{color:#000;text-align:center;align-items:center}th.sorting{background:#008b8b;color:#fff}.modal-header{background:#5f9ea0;color:#fff}.form-control.is-invalid,.was-validated .form-control:invalid{background-image:none!important}.form-control.is-valid,.was-validated .form-control:valid{background-image:none!important}.col.card.coladjust1 select{width:100%!important;height:100%!important;background:#f0fff0}.each{background:#f1f3f6;font-family:Arial,Helvetica,sans-serif;border:5px solid #eaeef5}.col.card.coladjust1.p-2.m-1{background:beige}.container-fluid.body-content.mt-4{padding:20px}.col.card.coladjust.m-1{background:#fff8dc}tbody{font-weight:500}.input-group{margin-bottom:10px;display:table-cell}.remove-btn{cursor:pointer;color:red}.coladjust121{min-height:143px;align-items:center;justify-content:center;color:#000;padding:20px}.col.card.coladjust31.p-2.m-1{align-items:center}.col.card.coladjust31 select{background:#f0fff0}.meetingbackground{background:#bdb76b}
    </style>
  </head>
  <body class="hold-transition sidebar-mini layout-fixed">
    <div class="wrapper">
      <!-- Preloader -->
      <!-- Navbar -->
       <?php $this->load->view($dep_name.'/nav.php');?>
       <style>
        .col.card.coladjust1 select {
              background: linear-gradient(to right, #dfe9f3, #ffffff);
          }
          .meetingbackground{
            background: linear-gradient(to right, #fff8e1, #ffffff); 
            border-radius: 12px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1); 
            padding: 20px;
          }

           .modal.modal-dialog {
            width: 100%;
            height: 100%;
            margin: 0;
            padding: 0;
        }
        .modal.modal-content {
            height: auto;
            min-height: 100%;
            border-radius: 0;
        }
        .maintitlebg{
             /* background-image: linear-gradient(to right, #0f2027, #203a43, #2c5364); */
             background-image: linear-gradient(to right top, #00922a, #008c6c, #0081a1, #0071ba, #0058ae, #4c4ea9, #6f429f, #8a328f, #bd3b82, #df5572, #f37865, #fb9f5f);
              border-radius: 12px;
              box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
              padding: 20px;
               background-size: 300% 300%;
         animation: waveBackground 10s ease infinite;
        }

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
.current-Quarter-Target-Overview{
              background-image: linear-gradient(to bottom, #051937, #171228, #190a1a, #12040d, #000000);
              border-radius: 12px;
              box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
              color: white;
              font-size: 18px;
              font-weight: 600;
              padding:10px;
              background-size: 300% 300%;
              animation: waveBackground 10s ease infinite;
}
  @keyframes waveBackground {
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


           <?php 
          //  dd($reviewData);
          $reviewDataCnt =  sizeof($reviewData);
           if($reviewData !==''){
            if(sizeof($reviewData) > 0){
              // dd($reviewData);
              $reviewDatas      = $reviewData[0];
              $review_id        = $reviewDatas->id;
              $review_username  = $reviewDatas->name;
              $reviewtype       = $reviewDatas->reviewtype;
              $review_cremark   = $reviewDatas->cremark;
              $review_startt    = $reviewDatas->startt;
              $review_closet    = $reviewDatas->closet;
              $review_sdatet    = $reviewDatas->sdatet;

              $review_uid       = $reviewDatas->uid;
              $review_bdid      = $reviewDatas->bdid;

             

              $review_closet = date("Y-m-d H:i:s");
              // $max_start_date = date("Y-m-d");
         
              $max_start_date = date('Y-m-d', strtotime($review_startt));

             
         
              if ($reviewtype == 'Weekly' || $reviewtype == 'Self Weekly') {
                  $max_end_date = date("Y-m-d", strtotime("+7 days", strtotime($review_startt)));
              } else if ($reviewtype == 'Fortnightly' || $reviewtype == 'Self Fortnightly') {
                  $max_end_date = date("Y-m-d", strtotime("+15 days", strtotime($review_startt)));
              } else if ($reviewtype == 'Monthly' || $reviewtype == 'Self Monthly') {
                  $max_end_date = date("Y-m-d", strtotime("+1 month", strtotime($review_startt)));
              } else if ($reviewtype == 'Quarterly' || $reviewtype == 'Self Quarterly' || $reviewtype == 'Querterly') {
                  $max_end_date = date("Y-m-d", strtotime("+3 months", strtotime($review_startt)));
              } else if ($reviewtype == 'Half Yearly' || $reviewtype == 'Self Half Yearly') {
                  $max_end_date = date("Y-m-d", strtotime("+6 months", strtotime($review_startt)));
              }
              
        
    
             $reviewMessage = "Review BD Name is : ".$review_username." | Review Type : ".$reviewtype." | Start Date : ".$review_startt." | End Date : ".$review_closet;
            }
           }else{
            $max_start_date = '';
            $max_end_date   = '';
            $reviewMessage  = '';
            $review_id      = '';
            $reviewDataCnt = 0;
           }

           ?>

            <div class="row">
              <div class="col-12">
                <div class="card">
                  <div class="card-header maintitlebg">
                    <h2 class="text-center m-3 text-white">TARGET SETTING</h2>
                  </div>
                  <hr>
                  <div class="text-right">
                      <span class="p-2 bg-success"> 
                        <a href="<?= base_url()?>Menu/AddTargetListByUser" target="_BLANK">
                          Check Target List
                        </a>
                      </span>
                    </div>
                    <hr>


                    <?php 

$targetDatss = $this->Menu_model->GetCurrentQuarterQuarterTarget($user['user_id']);
if(sizeof($targetDatss) > 0){
    $target_id = $targetDatss[0]->id;

?>

                    <!-- Button trigger modal -->
                  <button type="button" class="current-Quarter-Target-Overview" data-toggle="modal" data-target="#exampleModalLong">
                    Current Quarter Target Overview
                  </button>

                  <!-- Modal -->
                  <div class="modal fade" id="exampleModalLong" tabindex="-1" role="dialog" aria-labelledby="exampleModalLongTitle" aria-hidden="true">
                    <div class="modal-dialog" role="document" style="max-width: 100%; height: 100%; margin: 0; padding: 0;">
                      <div class="modal-content" style="height: 100%; border: none; border-radius: 0;">
                        <div class="modal-header" style="background: linear-gradient(to right, #fff8e1, #ffffff); border-radius: 12px; box-shadow: 0 4px 8px rgba(0,0,0,0.1); padding: 20px;">
                          <h5 class="modal-title text-dark" id="exampleModalLongTitle">
                            <center>
                              🎯 Target vs Achievement
                            </center>
                          </h5>
                          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                          </button>
                        </div>
                        <div class="modal-body" style="height: calc(100% - 120px); overflow-y: auto;">
                          <!-- Modal content here -->
                        <div class="iframe_card" style="
                                max-width: 100%;
                                margin: 20px auto;
                                padding: 10px;
                                background: #ffffff;
                                border-radius: 16px;
                                box-shadow: 0 8px 20px rgba(0, 0, 0, 0.15);
                                transition: transform 0.3s ease, box-shadow 0.3s ease;
                                overflow: hidden;
                              ">
                                <!-- <h2 style="
                                  font-family: 'Segoe UI', sans-serif;
                                  text-align: center;
                                  color: #333;
                                  margin-bottom: 16px;
                                ">🎯 Target vs Achievement</h2> -->
                          <iframe id="myIframe" src="<?= base_url() ?>Menu/target_vs_achievement/11" style="
                                width: 100%;
                                height: 100vh;
                                border: none;
                                border-radius: 12px;
                                box-shadow: 0 0 10px rgba(0, 0, 0, 0.15);
                                transition: all 0.3s ease-in-out;
                            " loading="lazy"></iframe>

                            <script>
                              document.getElementById('myIframe').addEventListener('load', function () {
                                const iframe = document.getElementById('myIframe');
                                const innerDoc = iframe.contentDocument || iframe.contentWindow.document;

                                const hideSidebar = () => {
                                  if (!innerDoc) {
                                    console.log('Iframe document not accessible yet.');
                                    setTimeout(hideSidebar, 200);
                                    return;
                                  }

                                  const sidebar = innerDoc.querySelector('.main-sidebar.sidebar-dark-primary.elevation-4');
                                  const mainHeader = innerDoc.querySelector('.main-header.navbar.navbar-expand.navbar-white.navbar-light');
                                  const contentWrapper = innerDoc.querySelector('.content-wrapper');
                                  const mainFooter = innerDoc.querySelector('.main-footer');

                                  if (sidebar || mainHeader) {
                                    if (sidebar) sidebar.remove();
                                    if (mainHeader) mainHeader.remove();

                                    // Adjust layout elements inside the iframe after removing sidebar/header
                                    if (contentWrapper) contentWrapper.style.marginLeft = '0';
                                    if (mainFooter) mainFooter.style.marginLeft = '0';

                                    console.log('Sidebar and header removed successfully.');
                                  } else {
                                    console.log('Sidebar or header not found. Retrying...');
                                    setTimeout(hideSidebar, 200);
                                  }
                                };

                                hideSidebar();
                              });
                            </script>

                              </div>

                              <style>
                                .iframe_card:hover {
                                  transform: scale(1.01);
                                  box-shadow: 0 12px 28px rgba(0, 0, 0, 0.25);
                                }
                              </style>

                        </div>
                      </div>
                    </div>
                  </div>


                  <?php } else{ ?> 
                    <div class="card text-center">
                        <div class="text-danger">
                          <h5>* No Target Setting In this Quarter</h5>
                        </div>
                    </div>
                    
                    <?php } ?>
             
                  <!-- /.card-header -->
                  <div class="card-body">
                  <hr>
                   <center>
                     <span class="p-1" style="color: navy; font-weight: 600;"><?=$reviewMessage; ?></span>
                   </center>
                  <hr>
                    <div class="each p-5 bg-info1" style="background: linear-gradient(to right, #dfe9f3, #ffffff); border-radius: 12px; box-shadow: 0 4px 8px rgba(0,0,0,0.1); padding: 20px;">
                      <div class="was-validated">
                        <form action="<?=base_url();?>Menu/StoreTarget" id="add_target_form" method="post">

                        <?php 
                          $cfData = $this->Menu_model->getCurrentFinancialYearAndQuarter();
                          // echo "Financial Year: " . $cfData['financial_year'] . "\n";
                          $currentQuarter = "Q".$cfData['quarter'];
                        ?>

                          <div class="form-row">
                            <input type="hidden" readonly class="form-control" name="currentQuarter" value="<?=$currentQuarter;?>" required>
                            <input type="hidden" readonly class="form-control" name="review_id" value="<?=$review_id;?>" required>
                          </div>

                          <div class="form-row">
                            <?php $curtype_id = $user['type_id']; ?>
                             <?php if($reviewDataCnt == 0){ ?>
                            <div class="col card coladjust1 p-2 m-1" style="background: linear-gradient(to right, #fff8e1, #ffffff); border-radius: 12px; box-shadow: 0 4px 8px rgba(0,0,0,0.1); padding: 20px;">
                              <lable>Select Target User Type : </lable>
                              <select name="user_type[]" class="form-control" id="user_type" multiple required >
                                <option disabled >Select User Type </option>
                                <?php $get_user_type = $this->Menu_model->get_user_type();
                                  if($curtype_id == 2){
                                      foreach($get_user_type as $at){
                                          if($at->id != 1 && $at->id != 2){
                                      ?>
                                <option value="<?=$at->id?>"><?=$at->name?></option>
                                <?php }}
                                  }elseif($curtype_id == 15){
                                      foreach($get_user_type as $at){
                                          if($at->id == 3 || $at->id == 4 || $at->id == 13){
                                      ?>
                                <option value="<?=$at->id?>"><?=$at->name?></option>
                                <?php }}
                                  }elseif($curtype_id == 4){
                                      foreach($get_user_type as $at){
                                          if($at->id == 3 || $at->id == 13){
                                      ?>
                                <option value="<?=$at->id?>"><?=$at->name?></option>
                                <?php }}
                                  }elseif($curtype_id == 13){
                                      foreach($get_user_type as $at){
                                          if($at->id == 3 || $at->id == 24){
                                      ?>
                                <option value="<?=$at->id?>"><?=$at->name?></option>
                                <?php }}
                                  }elseif($curtype_id == 3){
                                    foreach($get_user_type as $at){
                                        if($at->id == 3){
                                    ?>
                              <option value="<?=$at->id?>"><?=$at->name?></option>
                              <?php }}
                                }elseif($curtype_id == 24){
                                    foreach($get_user_type as $at){
                                        if($at->id == 3){
                                    ?>
                              <option value="<?=$at->id?>"><?=$at->name?></option>
                              <?php }}
                                }
                                  ?>
                              </select>
                              <small id="noofuser_type"></small>
                            </div>
                              <div class="col card coladjust1 p-2 m-1" style="background: linear-gradient(to right, #fff8e1, #ffffff); border-radius: 12px; box-shadow: 0 4px 8px rgba(0,0,0,0.1); padding: 20px;">
                              <lable>Select Target User : </lable>
                              <select name="user_id[]" class="form-control" id="user_data" multiple>
                              </select>
                              <small id="noofuser"></small>
                            </div>
                            <?php } else { 
                             $comeRevewData = $this->Menu_model->getRecordFromAllReviewByID($review_id);
                            //  dd($comeRevewData[0]->bdid);
                            $review_bdid = $comeRevewData[0]->bdid;
                              
                              ?>
                            <div class="col card coladjust1 p-2 m-1" style="background: linear-gradient(to right, #fff8e1, #ffffff); border-radius: 12px; box-shadow: 0 4px 8px rgba(0,0,0,0.1); padding: 20px;">
                              <select name="user_id[]" class="form-control" id="user_data" style="width: 100%; height: 100%;border: none;" multiple>
                                <option value="<?=$review_bdid?>" selected> <?=$review_username?> </option>
                            </select>
                              </div>
                            <?php } ?>
                          </div>

                          <hr>
                            

                        

                           <div class="meeting-card" style="background: linear-gradient(to right, #ecb3b300, #ddffe5); border-radius: 12px; box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1); padding: 40px; margin: 4px; align-items: center;">


                           <div class="text-center mb-2" style="background: linear-gradient(to right, #ede7f6, #ffffff); border-radius: 12px; box-shadow: 0 4px 8px rgba(0,0,0,0.1); padding: 20px;">
                              <h5><i>RP Meeting Target</i></h5> 
                                <hr>
                                <small>
                                Tracks total, completed, and pending RP meetings to monitor progress, ensure alignment with goals, and drive timely execution.
                                </small>
                            </div>

                          <div class="form-row">
                            <div class="col card coladjust31" style="background: linear-gradient(to right, #fff8e1, #f6ddff); border-radius: 12px; box-shadow: 0 4px 8px rgba(0,0,0,0.1); padding: 40px; margin: 4px;align-items: center;">
                                <div>
                                      <lable>No Of RP Meeting</lable>
                                      <input type="number" id="num_of_meeting" name="num_of_meeting" class="form-control" required>
                                      <small id="num_of_meeting_message"></small>
                                </div>
                            </div>
                            <div class="col card coladjust31" style="background: linear-gradient(to right, #fff8e1, #f6ddff); border-radius: 12px; box-shadow: 0 4px 8px rgba(0,0,0,0.1); padding: 40px; margin: 4px;align-items: center;">
                                <div>
                                      <lable>RP Sheduled Meeting </lable>
                                      <input type="number" id="num_of_sheduled_meeting" name="num_of_sheduled_meeting" class="form-control" required>
                                </div>
                            </div>
                            <div class="col card coladjust31" style="background: linear-gradient(to right, #fff8e1, #f6ddff); border-radius: 12px; box-shadow: 0 4px 8px rgba(0,0,0,0.1); padding: 40px;margin: 4px;align-items: center;">
                              <div>
                                  <lable>RP Barg Meeting </lable>
                                  <input type="number" id="num_of_barg_meeting" name="num_of_barg_meeting" class="form-control" required>
                              </div>
                            </div>
                          </div>
                          <hr>
                           <div class="form-row" >
                            <div class="col card coladjust31 p-2 m-1 meetingbackground" style="background: linear-gradient(to right, #75ffa4, #ffffff); border-radius: 12px; box-shadow: 0 4px 8px rgba(0,0,0,0.1); padding: 20px;">
                              <br>
                              <lable>Out Station Meeting </lable>
                              <input type="number" name="out_station_meeting" id="out_station_meeting" class="form-control">
                              <hr>
                              <br>
                            </div>
                            <div class="col card coladjust121 m-1 meetingbackground" style="background: linear-gradient(to right, #75ffa4, #ffffff); border-radius: 12px; box-shadow: 0 4px 8px rgba(0,0,0,0.1); padding: 20px;">
                              <lable>Local Station Meeting </lable>
                              <input type="number" name="local_station_meeting" id="local_station_meeting" class="form-control">
                            </div>
                          </div>
                          <hr>

                          <div class="form-row">
                            <div class="col card coladjust31 p-2 m-1 meetingbackground" style="background: linear-gradient(to right, #e1fff4, #ffffff); border-radius: 12px; box-shadow: 0 4px 8px rgba(0,0,0,0.1); padding: 20px;">
                              <br>
                              <lable>Select Partner </lable>
                              <?php $get_partner = $this->Menu_model->get_partner(); ?>
                              <select class="form-control" id="select_partner_id" name="select_partner_id" style="width: 30% !important;">
                                <option>Select Partner</option>
                                <?php foreach($get_partner as $partner): ?>
                                <option value="<?= $partner->id; ?>"><span style="color:<?= $partner->clr;?>"><?= $partner->name; ?></span></option>
                                <?php endforeach; ?>
                              </select>
                          
                            </div>
                            <div class="col card coladjust121 m-1 meetingbackground" style="background: linear-gradient(to right, #e1fff4, #ffffff); border-radius: 12px; box-shadow: 0 4px 8px rgba(0,0,0,0.1); padding: 20px;">
                              <div class="row" id="input-container" style="padding: 20px;">
                              </div>
                            </div>
                          </div>


                          <?php /*
                          <hr>
                          <div class="form-row">
                            <div class="col card coladjust31 p-2 m-1 meetingbackground" style="background: linear-gradient(to right, #ffc8d2, #ffffff); border-radius: 12px; box-shadow: 0 4px 8px rgba(0,0,0,0.1); padding: 20px;"> 
                            <br> 
                              <lable>Select Category </lable>
                              <?php $get_categorys = $this->Menu_model->GetCategories(); ?>
                              <select class="form-control" id="select_category_id" name="select_category_id" style="width: 30% !important;">
                                <option>Select Category</option>
                                <?php foreach($get_categorys as $get_category): ?>
                                <option value="<?= $get_category->dataname; ?>"><span style="color:<?= $get_category->dataname;?>"><?= $get_category->name; ?></span></option>
                                <?php endforeach; ?>
                              </select>
                              <br>
                            </div>
                            <div class="col card coladjust121 m-1 meetingbackground" style="background: linear-gradient(to right, #ffc8d2, #ffffff); border-radius: 12px; box-shadow: 0 4px 8px rgba(0,0,0,0.1); padding: 20px;">
                              <div class="row" id="input-container-category" style="padding: 20px;">
                              </div>
                            </div>
                          </div>
                          */ ?>

                          <?php 




                          $currentQFunnels = $this->Menu_model->GetCurrentQuarterFunnelDatas($review_bdid);
                          $current_quarter_twetenty_closure_funnel = $currentQFunnels[0]->current_quarter_twetenty_closure_funnel;
                          $current_quarter_potential_funnel_for_fy = $currentQFunnels[0]->current_quarter_potential_funnel_for_fy;
                          $current_quarter_to_be_nurtured_for_fy = $currentQFunnels[0]->current_quarter_to_be_nurtured_for_fy;
                          $anchor_clients = $currentQFunnels[0]->anchor_clients;
                          $fifity_new_lead_funnel = $currentQFunnels[0]->fifity_new_lead_funnel;
                          // dd($currentQFunnels);
                          ?>
                         <hr>
                          <div class="form-row">
                            <div class="col card coladjust121 m-1" style="background:linear-gradient(to right, #ccf0ff, #ffffff); border-radius: 12px; box-shadow: 0 4px 8px rgba(0,0,0,0.1); padding: 20px;">
                              <h5><?=$currentQuarter?> Twetenty Closure Funnel </h5>
                              <a target="_BLANK" class="card-count-heading-new" style="color:hsl(330, 67%, 45%)!important" href="https://stemapp.in/Reports/FunnelDetails/current_quarter_twetenty_closure_funnel/<?=$review_bdid?>/userwise"><?=$current_quarter_twetenty_closure_funnel?></a>
                             
                              <lable>20 Closure Funnel For <?=$currentQuarter?>  (Meeting) </lable>
                              <input type="number" name="twetenty_closure_funnel" id="twetenty_closure_funnel" class="form-control" required>
                            </div>
                            <div class="col card coladjust121 m-1" style="background: linear-gradient(to right, #ccf0ff, #ffffff); border-radius: 12px; box-shadow: 0 4px 8px rgba(0,0,0,0.1); padding: 20px;">
                              <h5><?=$currentQuarter?> Potential Funnel </h5>
                              <a target="_BLANK" class="card-count-heading-new" style="color:hsl(330, 67%, 45%)!important" href="https://stemapp.in/Reports/FunnelDetails/current_quarter_potential_funnel_for_fy/<?=$review_bdid?>/userwise"> <?=$current_quarter_potential_funnel_for_fy?></a>
                              
                              <lable>	Potential Funnel For <?=$currentQuarter?>  (Meeting)</lable>
                              <input type="number" name="potential_funnel_for_fy" id="potential_funnel_for_fy" class="form-control" required>
                            </div>
                            <div class="col card coladjust121 m-1" style="background: linear-gradient(to right, #ccf0ff, #ffffff); border-radius: 12px; box-shadow: 0 4px 8px rgba(0,0,0,0.1); padding: 20px;">

                             <h5><?=$currentQuarter?> Quarter To Be Nurtured </h5>
                              <a target="_BLANK" class="card-count-heading-new" style="color:hsl(330, 67%, 45%)!important" href="https://stemapp.in/Reports/FunnelDetails/current_quarter_to_be_nurtured_for_fy/<?=$review_bdid?>/userwise"><?=$current_quarter_to_be_nurtured_for_fy?></a>
                             
                              <lable>	To be Nurtured For <?=$currentQuarter?>  (Meeting)</lable>
                              <input type="number" name="to_be_nurtured_for_fy" id="to_be_nurtured_for_fy" class="form-control" required>
                            </div>

                            <div class="col card coladjust121 m-1" style="background: linear-gradient(to right, #ccf0ff, #ffffff); border-radius: 12px; box-shadow: 0 4px 8px rgba(0,0,0,0.1); padding: 20px;">

                              <h5>50 New Lead Funnel </h5>
                               <a target="_BLANK" class="card-count-heading-new" style="color:hsl(330, 67%, 45%)!important" href="https://stemapp.in/Reports/FunnelDetails/fifity_new_lead_funnel/<?=$review_bdid?>/userwise"> <?=$fifity_new_lead_funnel?></a>
                              
                              <lable>	50 New Lead Funnel For <?=$currentQuarter?>  (Meeting)</lable>
                              <input type="number" name="fifity_new_lead_funnel" id="fifity_new_lead_funnel" class="form-control" required>
                            </div>

                            <div class="col card coladjust121 m-1" style="background: linear-gradient(to right, #ccf0ff, #ffffff); border-radius: 12px; box-shadow: 0 4px 8px rgba(0,0,0,0.1); padding: 20px;">
                             <h5>Anchor Clients </h5>
                              <a target="_BLANK" class="card-count-heading-new" style="color:hsl(330, 67%, 45%)!important" href="https://stemapp.in/Reports/FunnelDetails/anchor_clients/<?=$review_bdid?>/userwise"><?=$anchor_clients?></a>
                             
                              <lable>	Anchor Client Meeting For <?=$currentQuarter?> </lable>
                              <input type="number" name="anchor_client_meeting" id="anchor_client_meeting" class="form-control" required>
                            </div>
                          </div>
                          <hr>
                          </div>
                        <hr>



                        <div class="proposaltargetlist">
                                   <div class="proposal-card" style="background: linear-gradient(to right, #ecdeb3f7, #ddffe5); border-radius: 12px; box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1); padding: 40px; margin: 4px; align-items: center;">

                           <div class="text-center mb-2" style="background: linear-gradient(to right, #ede7f6, #ffffff); border-radius: 12px; box-shadow: 0 4px 8px rgba(0,0,0,0.1); padding: 20px;">
                              <h5><i>Proposal Target</i></h5> 
                                <hr>
                                <small>
                               Tracks total, completed, and pending proposal tasks to assess progress, identify gaps, and ensure timely achievement of proposal targets.
                                </small>
                            </div>

                          <div class="row">
                              <div class="col card coladjust1 p-2 m-1" style="background: linear-gradient(to right, #54a3cc, #ffffff);border-radius: 12px;box-shadow: 0 4px 8px rgba(0,0,0,0.1);padding: 20px;">
                                <lable>No Of Proposal </lable>
                                <input type="number" name="num_of_proposal" class="form-control" required>
                              </div>
                               <div class="col card coladjust1 p-2 m-1" style="background: linear-gradient(to right, #f3c8ec, #ffffff); border-radius: 12px; box-shadow: 0 4px 8px rgba(0,0,0,0.1); padding: 20px;">
                                    <lable>Revenue </lable>
                                    <input type="number" name="revenue" class="form-control" required>
                                </div>
                               <div class="col card coladjust1 p-2 m-1" style="background: linear-gradient(to right, #c0ffab, #ffffff); border-radius: 12px; box-shadow: 0 4px 8px rgba(0,0,0,0.1); padding: 20px;">
                                    <lable>No Of School </lable>
                                    <input type="number" name="school" class="form-control" required>
                                </div>
                          </div>
                          </div>
                           <hr>
                    
                           <div class="meeting-card" style="background: linear-gradient(to right, #ecb3b300, #ddffe5); border-radius: 12px; box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1); padding: 40px; margin: 4px; align-items: center;">
                           <div class="text-center mb-2" style="background: linear-gradient(to right, #ede7f6, #ffffff); border-radius: 12px; box-shadow: 0 4px 8px rgba(0,0,0,0.1); padding: 20px;">
                             <h5><i>Prospective Target (New Lead) By Research</i></h5> 
                              <hr>
                              <small>
                              Monitors total, contacted, and pending research-generated leads to evaluate outreach effectiveness and support strategic lead generation efforts efficiently.
                              </small>
                            </div>

                            <div class="form-row">
                              <div class="col card coladjust1 p-2 m-1" style="background: linear-gradient(to right, #d0ffd9, #ffffff);border-radius: 12px;box-shadow: 0 4px 8px rgba(0,0,0,0.1);padding: 20px;">
                                <lable>No Of Prospective (New Lead) </lable>
                                <input type="number" name="num_of_prospective" class="form-control" required>
                                </div>
                              </div>
                        </div>
                        </div>

                          <?php 
                          $max_check_date = '2025-02-14';


                          $getPastQuarterStartDate =  $this->Menu_model->getPastQuarterStartDate($max_start_date);
                           
                          // $max_check_date =    $getPastQuarterStartDate;   


                          $previousQuarterDate                  = $this->Menu_model->getPreviousQuarterStartDate();
                          $meetingDone                          = $this->Menu_model->GetTotalMeetingDoneProposalDoneOrNot($review_bdid,$max_check_date,$max_end_date,$review_bdid,$review_bdid);
                          $total_meetings                       = $meetingDone[0]->total_meetings;
                          $proposal_sent                        = $meetingDone[0]->proposal_sent;
                          $proposal_not_sent                    = $meetingDone[0]->proposal_not_sent;
                          $proposal_sent_but_closure_not_done   = $meetingDone[0]->proposal_sent_but_closure_not_done;
                          $bd_potentional_client_proposal_not_sent   = $meetingDone[0]->bd_potentional_client_proposal_not_sent;

            
                          ?>

                      
                          <hr>
                           <div class="meeting-card" style="background: linear-gradient(to right, #ecb3b300, #ddffe5); border-radius: 12px; box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1); padding: 40px; margin: 4px; align-items: center;">

                           <div class="text-center mb-2" style="background: linear-gradient(to right, #ede7f6, #ffffff); border-radius: 12px; box-shadow: 0 4px 8px rgba(0,0,0,0.1); padding: 20px;">
                           <h5>Meeting Done || Proposal Overview || Closure Status</h5> 
                            <hr>
                            <small>
                            Summarizes meetings held, proposals shared, and closure progress to track engagement, conversion potential, and identify follow-up opportunities effectively.
                            </small>
                            </div>
                         <div class="form-row">
                            <div class="col card coladjust31" style="background: linear-gradient(to right, #fff8e1, #f6ddff); border-radius: 12px; box-shadow: 0 4px 8px rgba(0,0,0,0.1); padding: 40px; margin: 4px; align-items: center;">
                                <div class="text-center">
                                    <label>📊 Total Meetings Done</label>
                                    <hr>
                                     <a target="_BLANK" class="card-count-heading-new" style="color:hsl(60, 88%, 38%)!important" href="<?=base_url()?>Reports/MeetingsDatas/total_RP_meetings_on_Check_Proposal/<?=$review_bdid?>/<?=$max_start_date?>/<?=$max_end_date?>">
                                      <h3 class="card-text"><?=$total_meetings?></h3>
                                    </a>
                                </div>
                            </div>
                            <div class="col card coladjust31" style="background: linear-gradient(to right, #fff8e1, #f6ddff); border-radius: 12px; box-shadow: 0 4px 8px rgba(0,0,0,0.1); padding: 40px; margin: 4px; align-items: center;">
                                <div class="text-center">
                                    <label>📨 Proposals Sent</label>
                                    <hr>
                                     <a target="_BLANK" class="card-count-heading-new" style="color:hsl(60, 88%, 38%)!important" href="<?=base_url()?>Reports/MeetingDoneProposalOverviewClosureStatusLists/proposal_sent/<?=$review_bdid?>/<?=$max_start_date?>/<?=$max_end_date?>">
                                      <h3 class="card-text"><?=$proposal_sent?></h3>
                                    </a>
                                </div>
                            </div>

                            <div class="col card coladjust31" style="background: linear-gradient(to right, #fff8e1, #f6ddff); border-radius: 12px; box-shadow: 0 4px 8px rgba(0,0,0,0.1); padding: 40px; margin: 4px; align-items: center;">
                                <div class="text-center">
                                    <label>🚫 Proposals Not Sent</label>
                                    <hr>
                                     <a target="_BLANK" class="card-count-heading-new" style="color:hsl(60, 88%, 38%)!important" href="<?=base_url()?>Reports/MeetingDoneProposalOverviewClosureStatusLists/proposal_not_sent/<?=$review_bdid?>/<?=$max_start_date?>/<?=$max_end_date?>">
                                      <h3 class="card-text"><?=$proposal_not_sent?></h3>
                                    </a>
                                    <hr>
                                    <center>
                                        <input type="number" name="proposal_send" class="form-control" placeholder="Enter number" required>
                                    </center>
                                </div>
                            </div>

                            <div class="col card coladjust31" style="background: linear-gradient(to right, #fff8e1, #f6ddff); border-radius: 12px; box-shadow: 0 4px 8px rgba(0,0,0,0.1); padding: 40px; margin: 4px; align-items: center;">
                                <div class="text-center">
                                    <label>🚫 BD Potentional Client Proposals Not Sent</label>
                                    <hr>
                                     <a target="_BLANK" class="card-count-heading-new" style="color:hsl(60, 88%, 38%)!important" href="<?=base_url()?>Reports/MeetingDoneProposalOverviewClosureStatusLists/bd_potentional_client_proposal_not_sent/<?=$review_bdid?>/<?=$max_start_date?>/<?=$max_end_date?>">
                                      <h3 class="card-text"><?=$bd_potentional_client_proposal_not_sent?></h3>
                                    </a>
                                    <hr>
                                    <center>
                                        <input type="number" name="bd_potentional_client_proposal_send" class="form-control" placeholder="Enter number" required>
                                    </center>
                                </div>
                            </div>

                            <div class="col card coladjust31" style="background: linear-gradient(to right, #fff8e1, #f6ddff); border-radius: 12px; box-shadow: 0 4px 8px rgba(0,0,0,0.1); padding: 40px; margin: 4px; align-items: center;">
                                <div class="text-center">
                                    <label>⏳ Proposals Sent but Closure Not Done</label>
                                    <hr>

                                      <a target="_BLANK" class="card-count-heading-new" style="color:hsl(60, 88%, 38%)!important" href="<?=base_url()?>Reports/MeetingDoneProposalOverviewClosureStatusLists/proposal_sent_but_closure_not_done/<?=$review_bdid?>/<?=$max_start_date?>/<?=$max_end_date?>">
                                      <h3 class="card-text"><?=$proposal_sent_but_closure_not_done?></h3>
                                    </a>
                                    <hr>
                                    <center>
                                        <input type="number" name="proposals_sent_and_closure" class="form-control" placeholder="Enter number" required>
                                    </center>
                                </div>
                            </div>
                        </div>

                        </div>
                   

                          <div class="form-row">
                            <div class="col card coladjust m-1" style="background: linear-gradient(to right, #feffa8, #ffffff); border-radius: 12px; box-shadow: 0 4px 8px rgba(0,0,0,0.1); padding: 20px;">
                              <div>
                                <lable>Target Start Date : </lable>
                                <input type="date" class="form-control" name="sdate" min="<?=$max_start_date?>" max="<?=$max_start_date?>" value="<?=$max_start_date?>" placeholder="Start Date" required >
                              </div>
                            </div>
                            <div class="col card m-1 coladjust" style="background: linear-gradient(to right, #feffa8, #ffffff); border-radius: 12px; box-shadow: 0 4px 8px rgba(0,0,0,0.1); padding: 20px;">
                              <div>
                                <lable>Target End Date : </lable>
                                <input type="date" class="form-control" name="edate" min="<?=$max_end_date?>" max="<?=$max_end_date?>" value="<?=$max_end_date?>" placeholder="End Date" required >
                              </div>
                            </div>
                          </div>
                          <br>
                          <div class="form-row">
                            <div class="col-sm-12 p-3">
                              <center>
                                <button type="submit" id="add_target_btn" class="custom-btn btn-11 mb-2" style="background: #002a1f;color:white;" >Add Target </button>
                              </center>
                            </div>
                          </div>
                        </form>
                      </div>
                    </div>


                  </div>
                </div>
              </div>
            </div>
            <!-- /.card-body -->
          </div>
      </section>
          <!-- /.card -->
      </div>
      <!-- /.col -->
    </div>
    <!-- /.row -->
 
    <!-- /.container-fluid -->

    

   
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
    <script type='text/javascript'>  
      function RestrictionUpdate(id,sdate,edate){
      $('#exampleModalCenterdatamom').modal('show');
      $('#res_id').val(id);
      $('#start_date').val(sdate);
      $('#end_date').val(edate);
      }
      
      $('#user_type').on('change', function() {
      
      var user_type_id = $(this).val();
      $.ajax({
      url: '<?=base_url();?>Management/getAllActiveUserInDepartment',
      type: "POST",
      data: {
          user_type_id: user_type_id
      },
      cache: false,
      success: function a(result) {
      $("#user_data").html(result);
      
      var optionCount = $('#user_data').find('option').length;
      $("#noofuser").text("Total Number of User: " + optionCount);
      $("#noofuser").css({"color": "green"});
      }
      });
      });
      
      $(document).ready(function() {
            $('#select_partner_id').on('change', function() {
                var selectedValue = $(this).val();
                var selectedText = $("#select_partner_id option:selected").text();
                if (selectedValue !== 'Select Partner') {
                    var inputGroup = $('<div class="col input-group p-2"></div>');
                    var label = $('<label classs="p-2"></label>').text(selectedText + ':');
                    var input = $('<input type="text" class="form-control p-2" title="'+selectedText+'" name="partnerid_' + selectedValue + '">');
                    var removeButton = $('<span class="remove-btn p-2">Remove</span>');
                    removeButton.on('click', function() {
                        inputGroup.remove();
                    });
                    inputGroup.append(label, input, removeButton);
                    $('#input-container').append(inputGroup);
                }
            });


            
            $('#select_category_id').on('change', function() {
                var selectedValue = $(this).val();
                var selectedText = $("#select_category_id option:selected").text();
                if (selectedValue !== 'Select Category') {
                    var inputGroup = $('<div class="col input-group p-2"></div>');
                    var label = $('<label classs="p-2"></label>').text(selectedText + ':');
                    var input = $('<input type="text" class="form-control p-2" title="'+selectedText+'" name="categoryid_' + selectedValue + '">');
                    var removeButton = $('<span class="remove-btn p-2">Remove</span>');
                    removeButton.on('click', function() {
                        inputGroup.remove();
                    });
                    inputGroup.append(label, input, removeButton);
                    $('#input-container-category').append(inputGroup);
                }
            });


      
            $("#add_target_form").submit(function() {
             
              var num_of_meeting            = $("#num_of_meeting").val();
              var partner_id                = $("#partner_id").val();
              var out_station_meeting       = $("#out_station_meeting").val();
              var local_station_meeting     = $("#local_station_meeting").val();
      
              let values = [];
              var partnercount = 0;
              $("input[name^='partnerid_']").each(function() {
                  let title = $(this).attr("title"); 
                  let value = $(this).val();  
                  values.push(`${title}: ${value}`); 
                  partnercount += parseInt(value);
              });
              // Display the collected values in an alert

              // let values_categoryid = [];
              // var categorycount = 0;
              // $("input[name^='categoryid_']").each(function() {
              //     let category_title = $(this).attr("title"); 
              //     let categoryid_value = $(this).val();  
              //     values_categoryid.push(`${category_title}: ${categoryid_value}`); 
              //     categorycount += parseInt(categoryid_value);
              // });

              if(out_station_meeting == ''){
                alert("Please Add Out Station Meetings");
                 return false;
              }
      
              if(local_station_meeting == ''){
                alert("Please Add Local Station Meetings ");
                 return false;
              }


              var twetenty_closure_funnel   = $("#twetenty_closure_funnel").val();
              var potential_funnel_for_fy   = $("#potential_funnel_for_fy").val();
              var to_be_nurtured_for_fy     = $("#to_be_nurtured_for_fy").val();
              var fifity_new_lead_funnel    = $("#fifity_new_lead_funnel").val();
              var anchor_client_meeting     = $("#anchor_client_meeting").val();
         
              if(twetenty_closure_funnel == ''){
                alert("Please Add Number Of Twetenty Closure Funnel (Meeting)");
                 return false;
              }
       
              if(potential_funnel_for_fy == ''){
                alert("Please Add Number Of Potential Funnel For Year (Meeting)");
                 return false;
              }

              if(to_be_nurtured_for_fy == ''){
                alert("Please Add Number Of To be Nurtured for FY (Meeting)");
                 return false;
              }

              if(fifity_new_lead_funnel == ''){
                alert("Please Add Number Of 50 New Lead Funnel (Meeting)");
                 return false;
              }

              if(anchor_client_meeting == ''){
                alert("Please Add Number Of Anchor Client Meeting");
                 return false;
              }


              var out_local_partner_meeting = 0;
      
              if(out_station_meeting !== ''){
                out_local_partner_meeting += parseInt(out_station_meeting);
              }
      
              if(local_station_meeting !== ''){
                out_local_partner_meeting +=  parseInt(local_station_meeting);
              }
      

              if(num_of_meeting == ''){
                alert('* Please enter number of meeting');
                  return false;
                }else if (num_of_meeting < partnercount) {
                    var remaining = Math.abs(partnercount - num_of_meeting);
                    var alertmessage = 
                        "* Your meeting count is " + num_of_meeting + 
                        ", and your total partner count is " + partnercount + 
                        ". * You need to increase your meeting count by " + remaining + 
                        ", or remove " + remaining + " partner(s).";
                    alert(alertmessage);
                    return false;
                }else if (num_of_meeting > partnercount) {
                    var remaining_big = Math.abs(num_of_meeting - partnercount);
                    var alertmessage = 
                        "* Your meeting count is " + num_of_meeting + 
                        ", and your total partner count is " + partnercount + 
                        ". * You need to decrease your meeting count or add more " + remaining_big + 
                        " partner(s)";
                    alert(alertmessage);
                    return false;
                } 
                
                else if (num_of_meeting < categorycount) {
                    var remaining = Math.abs(categorycount - num_of_meeting);
                    var alertmessage = 
                        "* Your meeting count is " + num_of_meeting + 
                        ", and your total category count is " + categorycount + 
                        ". * You need to increase your meeting count by " + remaining + 
                        ", or remove " + remaining + " category(s).";
                    alert(alertmessage);
                    return false;
                }
                
                // else if (num_of_meeting > categorycount) {
                //     var remaining_big = Math.abs(num_of_meeting - categorycount);
                //     var alertmessage = 
                //         "* Your meeting count is " + num_of_meeting + 
                //         ", and your total category count is " + categorycount + 
                //         ". * You need to decrease your meeting count or add more " + remaining_big + 
                //         " category(s)";
                //     alert(alertmessage);
                //     return false;
                // } 
                
                else if (num_of_meeting > out_local_partner_meeting) {
                    var alertmessage = 
                          "* Your meeting count is " + num_of_meeting + 
                          ", and your total Out/Local Station Meeting count is " + out_local_partner_meeting+
                          ", no of meeting and total Out/Local Station Meeting does not matched";
                      alert(alertmessage);
                      return false;
                  }else if (out_local_partner_meeting > num_of_meeting) {
                    var alertmessage = 
                          "* Your meeting count is " + num_of_meeting + 
                          ", and your total Out/Local Station Meeting count is " + out_local_partner_meeting+
                          ", no of meeting and total Out/Local Station Meeting does not matched";
                      alert(alertmessage);
                      return false;
                  }



                var twetenty_closure_funnel_num   = parseFloat($("#twetenty_closure_funnel").val()) || 0;
                var potential_funnel_for_fy_num   = parseFloat($("#potential_funnel_for_fy").val()) || 0;
                var to_be_nurtured_for_fy_num     = parseFloat($("#to_be_nurtured_for_fy").val()) || 0;
                var fifity_new_lead_funnel_num    = parseFloat($("#fifity_new_lead_funnel").val()) || 0;
                var anchor_client_meeting_num     = parseFloat($("#anchor_client_meeting").val()) || 0;

                var quarter_total = twetenty_closure_funnel_num + potential_funnel_for_fy_num + to_be_nurtured_for_fy_num + fifity_new_lead_funnel_num + anchor_client_meeting_num;

                if (num_of_meeting > quarter_total) {
                   var alertmessage = 
                          "* Your meeting count is " + num_of_meeting + 
                          ", and your total Quarter Meeting (20 Closure Funnel + Potential Funnel For FY + To be Nurtured for FY + 50 New Lead Funnel + Anchor Client Meeting) count is " + quarter_total+
                          ", no of meeting and total Quarter Meeting Meeting does not matched";
                  alert(alertmessage);
                   return false;
                }else if(quarter_total > num_of_meeting){
                  var alertmessage = 
                          "* Your meeting count is " + num_of_meeting + 
                          ", and your total Quarter Meeting (20 Closure Funnel + Potential Funnel For FY + To be Nurtured for FY + 50 New Lead Funnel + Anchor Client Meeting) count is " + quarter_total+
                          ", no of meeting and total Quarter Meeting Meeting does not matched";
                  alert(alertmessage);
                   return false;
                }

                  
            });


       $('#num_of_meeting').on('blur', function() {
        let num_of_meeting_val = $(this).val();
        // $('#num_of_meeting_message').text('You entered: ' + num_of_meeting_val);
        $.ajax({
          url: '<?=base_url();?>Management/CheckYourLineManagerEnterRpMeetingsinQuarter',
          type: "POST",
          data: {
              num_of_meeting: num_of_meeting_val
          },
          cache: false,
          success: function a(result) {
            if(result !== "No Target Setting"){
              if(result != 0){

              if(result > num_of_meeting_val){
                  var error_message = '* Your Line Manager entered a total of ' + result + ' RP meetings. Please enter a RP Meetings value below ' + result + '.';
                  $('#num_of_meeting_message').text(error_message);
                  $('#num_of_meeting').val('');
                }
              }else if(result == 0){
                var error_message = '* Your Line Manager not Seted Target Setting';
                console.log(error_message);
              }
            }
          }
          });
      });


        });
    </script>
  </body>
</html>
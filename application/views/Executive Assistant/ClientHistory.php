<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="wi$dth=device-wi$dth, initial-scale=1">
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
    <script src="https://kit.fontawesome.com/ddff61d36a.js" crossorigin="anonymous"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">
    <script type="module" src="https://early.webawesome.com/webawesome@3.0.0-alpha.4/dist/webawesome.loader.js"></script>
    <link rel="stylesheet" href="https://early.webawesome.com/webawesome@3.0.0-alpha.4/dist/themes/default.css">
    <style>
      details,wa-details{padding-bottom:20px}.accordion-button::before,wa-details::before{content:"";display:block;position:relative;width:22px;height:22px;outline:DimGrey solid 1px;background-color:#696969;transition:.25s}.scrollme{overflow-x:auto}body{font-family:Poppins;padding:2rem}.hr-line{border-top:1px solid transparent;background:linear-gradient(white,#fff) padding-box,linear-gradient(90deg,#fff,#696969,#fff) border-box}.wa-timeline{max-width:90%;margin:0 auto}.option-1,.option-2{height:20px;width:20px;margin:0;padding:0;transition:.2s}wa-details{--border-width:0;--border-radius:0;border-left:1.5px solid #696969;padding-left:25px}wa-details[open]::part(content){font-weight:300}wa-details::part(header){font-weight:700}wa-details::before{border:3px solid #fff;top:37px;right:37px;color:#000;border-radius:50%}#details-1[open]::before,#details-4[open]::before{background-color:#1e90ff;outline-color:#1e90ff}#details-1[open]::part(base){border-width:0}#details-1[open]::part(summary){color:#1e90ff}#details-1[open]::part(summary-icon){color:#1e90ff}#details-1::part(header){border:1px solid #696969}#details-1[open]::part(header){border:1px solid #1e90ff}#details-2[open]::before,#details-5[open]::before{background-color:Orange;outline-color:Orange}#details-2[open]::part(base){border-width:0}#details-2[open]::part(summary){color:Orange}#details-2[open]::part(summary-icon){color:Orange}#details-2::part(header){border:1px solid #696969}#details-2[open]::part(header){border:1px solid Orange}#details-3[open]::before,#details-6[open]::before{background-color:#ff69b4;outline-color:#ff69b4}#details-3[open]::part(base){border-width:0}#details-3[open]::part(summary){color:#ff69b4}#details-3[open]::part(summary-icon){color:#ff69b4}#details-3::part(header){border:1px solid #696969}#details-3[open]::part(header){border:1px solid #ff69b4}#details-4[open]::part(base){border-width:0}#details-4[open]::part(summary){color:#fff}#details-4[open]::part(summary-icon){color:#fff}#details-4::part(header){border:none;background-color:#eaeaea}#details-4[open]::part(header){background-color:#1e90ff}#details-5[open]::part(base){border-width:0}#details-5[open]::part(summary){color:#fff}#details-5[open]::part(summary-icon){color:#fff}#details-5::part(header){border:none;background-color:#eaeaea}#details-5[open]::part(header){background-color:Orange}#details-6[open]::part(base){border-width:0}#details-6[open]::part(summary){color:#fff}#details-6[open]::part(summary-icon){color:#fff}#details-6::part(header){border:none;background-color:#eaeaea}#details-6[open]::part(header){background-color:#ff69b4}.option-1{background-color:#fff;border:1px solid #696969;border-radius:50%}.option-2{background-color:#dcdcdc;border:0 solid #fff;border-radius:50%}.accordion{--bs-accordion-border-width:0;--bs-accordion-border-radius:0}.bs-timeline{max-width:600px;margin:0 auto}.accordion-item{border-radius:0;border-left:1.5px solid #696969;padding-left:25px;padding-bottom:20px}.accordion-button::before{border:3px solid #fff;right:58px;border-radius:50%}#tl-btn-1:not(.collapsed){color:#1e90ff;background-color:#fff!important;border:1px solid #1e90ff;border-radius:0!important}#tl-btn-2:not(.collapsed){color:Orange;background-color:#fff!important;border:1px solid Orange;border-radius:0!important}#tl-btn-3:not(.collapsed){color:#ff69b4;background-color:#fff!important;border:1px solid #ff69b4;border-radius:0!important}#tl-btn-1.collapsed,#tl-btn-2.collapsed,#tl-btn-3.collapsed{border:1px solid #696969;border-radius:0!important}#tl-btn-4:not(.collapsed){color:#fff;background-color:#1e90ff;border:none;border-radius:0}#tl-btn-5:not(.collapsed){color:#fff;background-color:Orange;border:none;border-radius:0}#tl-btn-6:not(.collapsed){color:#fff;background-color:#ff69b4;border:none;border-radius:0}#tl-btn-4.collapsed,#tl-btn-5.collapsed,#tl-btn-6.collapsed{border:none;border-radius:0;background-color:#eaeaea}@media (hover:hover){#tl-btn-1:hover::before,#tl-btn-4:hover::before{background-color:#1e90ff;outline-color:#1e90ff}#tl-btn-2:hover::before,#tl-btn-5:hover::before{background-color:Orange;outline-color:Orange}#tl-btn-3:hover::before,#tl-btn-6:hover::before{background-color:#ff69b4;outline-color:#ff69b4}}
    .company_creater, .main_bd, .cluster_manager, .pst{font-weight: 600;}.company_creater{color: #0f5d05;}.main_bd { color: blue; }.cluster_manager {color: crimson;}.pst {color: brown;}.cur_status{color: crimson;}.partner_name{color: crimson;}.compname_id{color: crimson;}
   </style>
  </head>
  <body class="hold-transition sidebar-mini layout-fixed">
    <div class="wrapper">
      <?php require('nav.php');?>
      <div class="content-wrapper">
        <div class="content-header">
          <div class="container-fluid">
            <div class="row mb-2">
              <!-- <div class="col-sm-6">
                <h1 class="m-0">Closer Companies</h1>
                </div> -->
              <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                  <h4></h4>
                </ol>
              </div>
            </div>
          </div>
        </div>
        <?php 
            // dd($cmpData); 
            $initId         = $cmpData[0]->id;
            $compname       = $cmpData[0]->compname;
            $current_status = $cmpData[0]->current_status;
            $last_status    = $cmpData[0]->last_status;
            $partner_name   = $cmpData[0]->partner_name;
            $creator_id     = $cmpData[0]->creator_id;
            $mainbd         = $cmpData[0]->mainbd;
            $clm_id         = $cmpData[0]->clm_id;
            $apst           = $cmpData[0]->apst;
            $createDate     = $cmpData[0]->createDate;
            $usertext = '';
            $cmpCreatorData         = $this->Menu_model->get_userbyid($creator_id);
            $cmpCreatorDatacnt      = sizeof($cmpCreatorData); 
            if($cmpCreatorDatacnt > 0){
                $cmpCreatorName     = $cmpCreatorData[0]->name;
                $usertext          .= "<span class='company_creater'>Company Creater:</span> ".$cmpCreatorName." | ";
            }
            
            $cmpMainbdData          = $this->Menu_model->get_userbyid($mainbd);
            $cmpMainbdDatacnt       = sizeof($cmpMainbdData); 
            if($cmpMainbdDatacnt > 0){
                $cmpMainbdDataName      = $cmpMainbdData[0]->name;
                $usertext          .= "<span class='main_bd'>Main BD:</span> ".$cmpMainbdDataName." | ";
            }
            
            $cmpClmIdData           = $this->Menu_model->get_userbyid($clm_id);
            $cmpClmIdDatacnt        = sizeof($cmpClmIdData); 
            if($cmpClmIdDatacnt > 0){
                $cmpClmIdDataName   = $cmpClmIdData[0]->name;
                $usertext          .= "<span class='cluster_manager'>Cluster Manager:</span> ".$cmpClmIdDataName." | ";
            }
            $cmpPSTData             = $this->Menu_model->get_userbyid($apst);
            $cmpPSTDatacnt          = sizeof($cmpPSTData); 
            if($cmpPSTDatacnt > 0){
                $cmpPSTDataName     = $cmpPSTData[0]->name;
                $usertext          .= "<span class='pst'>PST:</span> ".$cmpPSTDataName." | ";
            }
           $usertext                = rtrim($usertext, " |");
        $cmpTaskData             = $this->Menu_model->getAllTaskusingInitID($initId);
        $combinedData = [];
        foreach ($cmpTaskData as $item) {
            // if (isset($item->status_id, $item->nstatus_id) && $item->status_id !== $item->nstatus_id ) {
            if (isset($item->status_id, $item->nstatus_id) && $item->status_id !== $item->nstatus_id ) {
                $combinedData[] = [
                    'id' => $item->id,
                    'status_id' => $item->status_id,
                    'nstatus_id' => $item->nstatus_id,
                    'appointmentdatetime' => $item->appointmentdatetime,
                    'changed' => true
                    // 'data' => $item
                ];
            }
        }


       
      
         


// echo $createDate;
// die;
// print_r($combinedData);
        // dd($combinedData);
          ?>
        <section class="content">
          <div class="container-fluid">
            <h4 class="fw-bold text-center compname_id">
              <i class="fa-solid fa-timeline me-3"></i>
              <?= $compname; ?> 
            </h4>
            <div class="hr-line mt-3 mb-3"></div>
            <p class="fw-bold text-center"><i class="fa-classic fas fa-signal fa-lg margin-right-3xs"></i> <span class="cur_status"><?= $current_status; ?></span> | <span class="partner_name"><?= $partner_name; ?></span></p>
            <p class="fw-bold text-center"><i class="fa-brands fa-square-web-awesome fa-lg me-2"></i> <?= $usertext; ?></p>
            <p class="fw-bold text-center"><i class="fa fa-calendar" aria-hidden="true"></i> <span class="cur_status">Company Create Date :  <?= $createDate; ?></span></p>
            <div class="hr-line mt-4 mb-4"></div>
            <!-- WebAwesome Timeline -->
            <div class="wa-timeline">
                <?php 
                
                foreach  ($combinedData as $items):
                    $status_id              = $items['status_id'];
                    $nstatus_id             = $items['nstatus_id'];
                    $appointmentdatetime    = $items['appointmentdatetime'];
                    $stausChangeTaskID      = $items['id'];
                    $date                   = new DateTime("$appointmentdatetime");
                    $taskdate               = $date->format('d F Y');
                    $taskdatetime           =  date("H:i:s", strtotime($appointmentdatetime));

                    $sstatusData            = $this->Menu_model->get_statusbyid($status_id);
                    $nstatusData            = $this->Menu_model->get_statusbyid($nstatus_id );
                    $sstatusDataName        = $sstatusData[0]->name;
                    $nstatusDataName        = $nstatusData[0]->name;
                    $taskData               = $this->Menu_model->get_tbldata($stausChangeTaskID);

                    $task_actontaken              = $taskData['0']->actontaken;
                    $task_purpose_achieved        = $taskData['0']->purpose_achieved;
                    $task_fwd_date                = $taskData['0']->fwd_date;
                    $task_appointmentdatetime     = $taskData['0']->appointmentdatetime;
                    $task_assignedto_id           = $taskData['0']->assignedto_id;
                    $task_purpose_id              = $taskData['0']->purpose_id;
                    $task_remarks                 = $taskData['0']->remarks;
                    $task_user_id                 = $taskData['0']->user_id;
                    $task_updateddate             = $taskData['0']->updateddate;
                    $task_actiontype_id           = $taskData['0']->actiontype_id;

                    $taskActionData               = $this->Menu_model->getTaskAction($task_actiontype_id);
                    $taskActionDataname           = $taskActionData[0]->name;

                    $taskPurposeDataname          = $this->Menu_model->get_purposeNameById($task_purpose_id);

                    $taskuserdetails              = '';
                    if($task_assignedto_id !== $task_user_id) {
                      $taskassignUser             = $this->Menu_model->get_userbyid($task_assignedto_id);
                      $taskassignUsername         = $taskassignUser[0]->name;

                      $taskUserData               = $this->Menu_model->get_userbyid($task_user_id);
                      $taskUserDataname           = $taskUserData[0]->name;
                      $taskuserdetails            .= "Task Assign By: $taskassignUsername | Task User : $taskUserDataname";
                    }else{
                      $taskUserData               = $this->Menu_model->get_userbyid($task_user_id);
                      $taskassignUsername         = $taskUserData[0]->name;
                      $taskuserdetails           .= "$taskassignUsername";
                    }
                    
                ?>
              <wa-details id="details-1" summary="<?= $taskdate.' '.$taskdatetime ?> - <?= $sstatusDataName.' To '.$nstatusDataName ?>">
              <table class="table">
                  <tbody>
                    <tr class="table-success">
                      <td>Task User </td>
                      <td><?= $taskuserdetails ?></td>
                    </tr>
                    <tr class="table-primary">
                      <td>Task Actions </td>
                      <td><?= $taskActionDataname ?></td>
                    </tr>
                    <tr class="table-primary">
                      <td>Task Purpose </td>
                      <td><?= $taskPurposeDataname ?></td>
                    </tr>
                    <tr class="table-primary">
                      <td>Action Taken </td>
                      <td><?= $task_actontaken ?></td>
                    </tr>
                    <tr class="table-primary">
                      <td>Purpose Achieved </td>
                      <td><?= $task_purpose_achieved ?></td>
                    </tr>
                    
                  </tbody>
              </table>
              
                <!-- Tim Berners-Lee, invents the “World Wide Web” as an easy way to share information. Though we often use the “Internet” and the “Web” interchangeably, they don’t actually refer to the same thing. The Internet hosts the Web, which was Berners-Lee’s breakthrough. -->




              </wa-details>
              <!-- <wa-details id="details-2" summary="2001 - Wikipedia opens to the world">
                The beginning of the end for encyclopedia salesmen. Wikipedia launched with its first edit on January 15, 2001, and fast became the go-to source of information. By 2006, the site had published over 1 million articles and other internet enretpreneurs followed shortly.
              </wa-details>
              <wa-details id="details-3" summary="2020 - Internet users worldwide">
                As of October 2022, there were 5.07 billion active internet users worldwide – almost 59.6 percent of the global population. Ask any of them what life would be like without the net and the answer will likely be either ‘unimaginable’ or ‘very, very boring’.
              </wa-details> -->
              <?php endforeach; ?>
            </div>
            <p class="fw-bold text-center mt-4">Style options</p>
            <div class="d-flex justify-content-center flex-row flex-wrap gap-3">
              <button onclick="change_style_1()" class="option-1" aria-label="Thin" title="Thin">
              </button>
              <button onclick="change_style_2()" class="option-2" aria-label="Solid" title="Solid">
              </button>
            </div>
            <div class="hr-line mt-4 mb-4"></div>
            <p class="footer fs-6 fw-bold text-center text-muted">Powered by:</p>
            <p class="text-center mb-5">
                <img src="https://stemlearning.in/wp-content/uploads/2020/07/stem-new-logo-2-1.png" alt="Bootstrap" width="100" height="30">
            </p>
            <br>
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
      function change_style_1() {
      document.getElementById("details-4").id = "details-1";
      document.getElementById("details-5").id = "details-2";
      document.getElementById("details-6").id = "details-3";
      }
      
      function change_style_2() {
      document.getElementById("details-1").id = "details-4";
      document.getElementById("details-2").id = "details-5";
      document.getElementById("details-3").id = "details-6";
      }
      
      function change_style_3() {
      document.getElementById("tl-btn-4").id = "tl-btn-1";
      document.getElementById("tl-btn-5").id = "tl-btn-2";
      document.getElementById("tl-btn-6").id = "tl-btn-3";
      }
      
      function change_style_4() {
      document.getElementById("tl-btn-1").id = "tl-btn-4";
      document.getElementById("tl-btn-2").id = "tl-btn-5";
      document.getElementById("tl-btn-3").id = "tl-btn-6";
      }
      
    </script>
  </body>
</html>
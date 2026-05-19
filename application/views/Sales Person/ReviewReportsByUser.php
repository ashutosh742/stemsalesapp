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
    <style>
      .scrollme {
      overflow-x: auto;
      }
      form.p-3 {
      display: flex;
      width: 75%;
      }
      input.form-control {
      margin: 2px;
      }
      select.form-control {
      margin: 2px;
      }
      button.btn.btn-primary.text-light {
      margin: 3px;
      }
      .card-body {
    background: beige;
}
    </style>
  </head>
  <body class="hold-transition sidebar-mini layout-fixed">
    <div class="wrapper">
      <!-- Preloader -->
      <!-- Navbar -->
      <?php require('nav.php');?>
      <div class="content-wrapper">
      <?php 
      $compData = $this->Menu_model->GetMainReviewDetailsByUid($mainrid);
     
      $compname = $compData[0]->compname;
      $rtype = $compData[0]->rtype;
      $rtype_inid = $compData[0]->inid;
      ?>
        <div class="content-header">
        <?php 
      $reviewtype = $mreviews[0]->reviewtype;
      $revuid = $mreviews[0]->uid;
      $bdid = $mreviews[0]->bdid;
      $revuser1 = '';
      if($revuid == $bdid){
        $revudetail = $this->Menu_model->get_userbyid($revuid);
        $revuser1 .= $revudetail[0]->name;
      }else{
        $revudetail1 = $this->Menu_model->get_userbyid($revuid);
        $revuser1 .= $revudetail1[0]->name;
        $revuser1  .= ' | ';
        $revudetail2 = $this->Menu_model->get_userbyid($revuid);
        $revuser1 .= $revudetail2[0]->name;
      }

      ?>
          <div class="container-fluid">
            <div class="row">
              <div class="col-sm-12 card bg-primary text-center p-2">
                <h2>Review Report of - <?= $compname; ?></h2>
                <hr>
                <h4>( <?= $reviewtype; ?> )</h4>
                <h5><?= $revuser1; ?></h5>
              </div>
              <!-- /.col -->
            </div>
            <!-- /.row -->
          </div>
          <!-- /.container-fluid -->
        </div>

        <?php //dd($mdatarev); ?>

        <section class="content">
          <div class="container-fluid">
            <div class="row">
              <div class="col-md-12">
                <div class="card">
                  <div class="card-body">
                      <div class="table-responsive">
                            <table id="example1" class="table table-striped table-bordered" cellspacing="0" width="100%">
                              <thead>
                                <tr>
                                  <th>S.No.</th>
                                  <th>Review Question</th>
                                  <th>Answer 1</th>
                                  <th>Answer 2</th>
                                  <th>Answer 3</th>
                                  <th>Answer 4</th>
                                  <th>Answer 5</th>
                                </tr>
                              </thead>
                              <tbody>
                                <?php 
                                $i=1;
                                  foreach($mdatarev as $md){
                                    if($md->question == 'Need to Change Partner Type?'){
                                        if($md->ans1 == 'yes'){
                                            $partid = $md->ans2;
                                            $partners = $this->Menu_model->get_partnerbyid($partid);
                                            $partnersname = $partners[0]->name;
                                            $ans2 = $partnersname;
                                        }else{
                                            $ans2 = $md->ans2;
                                        }
                                    }else{
                                        $ans2 = $md->ans2;
                                    }
                                     ?>
                                <tr>
                                  <td><?=$i?></td>
                                  <td><?=$md->question?></td>
                                  <td><?=$md->ans1;?></td>
                                  <td><?=$ans2;?></td>
                                  <td><?=$md->ans3;?></td>
                                  <td><?=$md->ans4;?></td>
                                  <td><?=$md->ans5;?></td>
                                </tr>
                                </a>
                                <?php $i++;} ?>
                              </tbody>
                            </table>    
                      </div>
                  </div>
                </div>
              </div>
<?php 
 $baseData           = $this->Menu_model->getBaseReviewTargetData($rtype_inid,$rev_id);

 $baseDatacnt        = sizeof($baseData);
 if($baseDatacnt > 0){ 
 $ntid               = $baseData[0]->ntid;
 $sdate              = $baseData[0]->sdate;
 $by_uid             = $baseData[0]->by_uid;
 $for_uid            = $baseData[0]->for_uid;
 $csid               = $baseData[0]->csid;
 $exsid              = $baseData[0]->exsid;
 $exdate             = $baseData[0]->exdate;
 $cdate              = $baseData[0]->cdate;
 $remarks            = $baseData[0]->remarks;
 $rtype              = $baseData[0]->rtype;

 $revCmpStatus       = $this->Menu_model->get_statusbyid($csid);
 $revCmpStatusName   = $revCmpStatus[0]->name; 

 $revExpCmpStatus       = $this->Menu_model->get_statusbyid($exsid);
 $revExpCmpStatusName   = $revExpCmpStatus[0]->name; 

 $by_uidData       = $this->Menu_model->get_userbyid($by_uid);
 $for_uidData      = $this->Menu_model->get_userbyid($for_uid);
 
 if($by_uid == $for_uid){
     $by_uidDataName  = $by_uidData[0]->name;
     $for_uidDataName = '';
 }else{
     $by_uidDataName  = $by_uidData[0]->name;
     $for_uidDataName = $for_uidData[0]->name;
 }

 $initdata   = $this->Menu_model->get_cmpbyinid($rtype_inid);
 $compname   = $initdata[0]->compname;
 $cstatus    = $initdata[0]->cstatus;
 $lstatus    = $initdata[0]->lstatus;
 $clm_id     = $initdata[0]->clm_id;
 $apst       = $initdata[0]->apst;

 $getCurCmpStatus      = $this->Menu_model->get_statusbyid($cstatus);
 $getCurCmpStatusName  = $getCurCmpStatus[0]->name; 
 $getLastCmpStatus     = $this->Menu_model->get_statusbyid($lstatus);
 $geLastCmpStatusName  = $getLastCmpStatus[0]->name; 

 $ntData              = $this->Menu_model->getTBLTaskByID($ntid);
 $actontaken          = $ntData[0]->actontaken;
 $purpose_achieved    = $ntData[0]->purpose_achieved;
 $appointmentdatetime = $ntData[0]->appointmentdatetime;
 $actiontype_id       = $ntData[0]->actiontype_id;
 $purpose_id          = $ntData[0]->purpose_id;
 $updateddate         = $ntData[0]->updateddate;
 $status_id           = $ntData[0]->status_id;

 $getCmpTaskStatus    = $this->Menu_model->get_statusbyid($status_id);
 $getCTStatusname     = $getCmpTaskStatus[0]->name; 

 $getCmpTaskAction    = $this->Menu_model->get_actionbyid($actiontype_id);
 $getCTActionName     = $getCmpTaskAction[0]->name;
 $getCmpTaskPurpose   = $this->Menu_model->get_purposeNameById($purpose_id);

 $html = "";
 
 $html .= "<div class='card p-2'> 
         <table class='table table-bordered'>
             <tbody>
                 <tr class='table-success'>
                     <td>Base Review</td>
                     <td>$rtype</td>
                 </tr>
                 <tr class='table-primary'>
                     <td>Company Name</td>
                     <td>$compname</td>
                 </tr>
                 <tr class='table-secondary'>
                     <td>Company Current Status</td>
                     <td>$getCurCmpStatusName</td>
                 </tr>
                 <tr class='table-success'>
                     <td>Company Last Status</td>
                     <td>$geLastCmpStatusName</td>
                 </tr>
                 <tr class='table-danger'>
                     <td>Task Name</td>
                     <td>$getCTActionName</td>
                 </tr>
                 <tr class='table-warning'>
                     <td>Task Date</td>
                     <td>$appointmentdatetime</td>
                 </tr>
                 <tr class='table-info'>
                     <td>Action</td>
                     <td>$actontaken</td>
                 </tr>
                 <tr class='table-primary'>
                     <td>Purpose</td>
                     <td>$purpose_achieved</td>
                 </tr>
                 <tr class='table-secondary'>
                     <td>Review Time Company Status</td>
                     <td>$revCmpStatusName</td>
                 </tr>
                 <tr class='table-info'>
                     <td>Review Time Expected Status</td>
                     <td>$revExpCmpStatusName</td>
                 </tr>
                 <tr class='table-danger'>
                     <td>Review Time Expected Date</td>
                     <td>$exdate</td>
                 </tr>
             </tbody>
         </table>
     </div>";

 }else{
     $html .= "<p class='font-weight-bold text-center p-4'>* There is no review of this company in this base review.</p>";
 }
?>
              <div class="col-md-12 p-4">
                <div class="card text-center" style="background: azure;">
                    <h3 class="p-1">Target Status</h3>
                    <?= $html; ?>
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
    <script src="<?=base_url();?>assets/js/jquery.min.js"></script>
    <script src="<?=base_url();?>assets/js/jquery-ui.min.js"></script>
    <script>
      $.widget.bridge('uibutton', $.ui.button);
      
$( document ).ready(function() {
    $("#teamuser").hide();
});
      $('#adminvalue').change(function() {
        var selectedValue = $(this).val(); // Get the selected value
        $.ajax({
            url:'<?=base_url();?>Menu/GetAdminReviewUser',
            type: "POST",
            data: {
            admin_id: selectedValue
            },
            cache: false,
            success: function a(result){
                $("#teamuser").html(result);
                $("#teamuser").show();
                var optionCount = $('#teamuser').find('option').length;
                optionCount = optionCount-1;
                }
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
        "buttons": ["csv", "excel"]
      }).buttons().container().appendTo('#example1_wrapper .col-md-6:eq(0)');
    </script>
  </body>
</html>
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
  </style>
</head>
<body class="hold-transition sidebar-mini layout-fixed">
<div class="wrapper">

  <!-- Preloader -->
  

  <!-- Navbar -->
  <?php require('nav.php');?>
  <!-- /.navbar -->

  <?php $HandBIND = $this->Menu_model->get_HandBINDbycid($cid); ?>

  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h5 class="m-0">Handover to Installation Detail of <br><?=$HandBIND[0]->projectcode?></h5>
          </div><!-- /.col -->
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <h4></h4>
            </ol>
          </div><!-- /.col -->
        </div><!-- /.row -->
      </div><!-- /.container-fluid -->
    </div>
    <!-- /.content-header -->
    

    <!-- Main content -->
    <section class="content">
      <div class="container-fluid card p-5 ">
          <div class="row text-center">
                <?php 
                foreach($HandBIND as $cp){
                    $bdid = $cp->bd_id;
                    $project = $cp->projectcode;
                    $mdata = $this->Menu_model->get_Programbypcode($project);
                    $bddata = $this->Menu_model->get_bdnamebyid($bdid);
                    $cname = $cp->cname;
                    $cominfo = $this->Menu_model->get_compbyname($bdid,$cname);
                ?>
                    <div class="col-sm-12 col-md-4 col-lg-4 mb-4">
                      <a href="<?=base_url();?>Menu/HandBINDDetail/<?=$cp->cid?>" target="_blank">
                      <div class="card p-3 border rounded border-success hover-div d-flex flex-column align-items-stretch h-100 text-dark">
                          Client Name<br><b style="color:#808080"><?=$cp->cname?></b><hr>
                          Project Code<br><b style="color:#D4AC0D"><?=$project?> (<?=$cp->pyear?>)</b><hr>
                          <b>Total School: <?=$mdata[0]->tspd?></b><hr>
                          Person Involved
                          <br><b>BD: <?=$bddata[0]->name?></b>
                          <b style="color:#2471A3">PIA: <?=$mdata[0]->pia?></b>
                          <b style="color:#2471A3">IMP: <?=$mdata[0]->imp?></b><hr>
                          Last Action by Operation<br><b style="color:#2471A3">Installation Call<br><?=$this->Menu_model->get_dformat($cp->ltask)?></b><hr>
                          Last Action by Factory<br><b style="color:#FA8072">Printing Assign<br><?=$this->Menu_model->get_dformat($cp->ltask)?></b><hr>
                          Total Reminder<br><b style="color:#682642">0</b><hr>
                          Total Worning<br><b style="color:#682642">0</b><hr>
                            <?php if($cp->tdiff>15){$tcolor = "bg-danger";}else{$tcolor = "bg-success";}?>
                            <div class="rounded-circle <?=$tcolor?>" style="position: absolute;bottom: -10px; left: 40%; transform: translateX(-50%); width: 20px; height: 20px;"></div>
                            <div class="rounded-circle bg-danger" style="position: absolute;bottom: -10px; left: 60%; transform: translateX(-50%); width: 20px; height: 20px;"></div>
                      </div>
                      </a>
                    </div>
                   <?php } ?>
                   
                   <div class="col-sm-12 col-md-8 col-lg-8 mb-4">
                       
                     <div class="row">
                     <div class="col-sm-12 col-md-6 col-lg-6 mb-4">
                      <div class="card p-3 border rounded border-success hover-div d-flex flex-column align-items-stretch h-100 text-dark">
                              Partner Type<br><b><?=$cominfo[0]->pname;?></b><hr>
                              Address<br><b><?=$cominfo[0]->address?></b><hr>
                              City<br><b><?=$cominfo[0]->city?></b><hr>
                              State<br><b><?=$cominfo[0]->state?></b><hr>
                              Contact Person<br><b><?=$cominfo[0]->contactperson?></b><hr>
                              Designation<br><b><?=$cominfo[0]->designation?></b><hr>
                              Phone No<br><b><?=$cominfo[0]->phoneno?></b><hr>
                              Email ID<br><b><?=$cominfo[0]->emailid?></b><hr>
                              <a target="_blank" href="https://stemapp.in/<?=$mdata[0]->logo?>">Logo<br></a>
                              <object data="https://stemapp.in/<?=$mdata[0]->logo?>" type="application/pdf" width="100%" height="200">
                                <p>Your browser does not support PDF embedding. You can download the PDF <a href="https://stemapp.in/<?=$mdata[0]->logo?>">here</a>.</p>
                              </object><hr>
                            <div class="rounded-circle bg-danger" style="position: absolute;bottom: -10px; left: 40%; transform: translateX(-50%); width: 20px; height: 20px;"></div>
                            <div class="rounded-circle bg-danger" style="position: absolute;bottom: -10px; left: 60%; transform: translateX(-50%); width: 20px; height: 20px;"></div>
                      </div>
                    </div>
                    
                    <div class="col-sm-12 col-md-6 col-lg-6 mb-4">
                      <div class="card p-3 border rounded border-success hover-div d-flex flex-column align-items-stretch h-100 text-dark">
                          <?php 
                            $budget=$this->Menu_model->get_budget($cid);
                            $mdata = $this->Menu_model->get_clientbyiid($cid);?>
                            NGO / Mediator if any<br><b><?=$mdata[0]->mediator?></b><hr>
                            School Identification by<br><b><?=$mdata[0]->spd_identify_by?></b><hr>
                            Identification Type<br><b>Post</b><hr>
                            School Infrastructure for MSC (platform)<br><b><?=$mdata[0]->infrastructure?></b><hr>
                            Language on Backdrop<br><b><?=$mdata[0]->language?></b><hr>
                            Expected Installation Date<br><b><?=$this->Menu_model->get_odformat($mdata[0]->expected_installation_date)?></b><hr>
                            Project Tenure (Year)<br><b><?=$mdata[0]->project_tenure?></b><hr>
                            Project Type<br><b><?=$mdata[0]->project_type?></b><hr>
                            Special Requirements / Comments<br><b><?=$mdata[0]->comments?></b><hr>
                            Deliverables<br>
                            <?php foreach($budget as $bud){?>
                                    <b><?=$bud->bname?></b>
                                <?php } ?>
                            <div class="rounded-circle bg-danger" style="position: absolute;bottom: -10px; left: 40%; transform: translateX(-50%); width: 20px; height: 20px;"></div>
                            <div class="rounded-circle bg-danger" style="position: absolute;bottom: -10px; left: 60%; transform: translateX(-50%); width: 20px; height: 20px;"></div>
                      </div>
                    </div>
                    </div>
                     
                       
                   </div>
                   
        
                   
                  </div> 
                   
                   
                    <div id="accordion">
                        
                        <div class="card col-sm-12 col-md-12 col-lg-12 mb-4 text-dark text-center">
                        <div class="card-header" id="heading0">
                          <h5 class="mb-0">
                            <button class="btn btn-link text-dark" data-toggle="collapse" data-target="#collapse0" aria-expanded="true" aria-controls="collapseOne">
                              <h5>School List<h5>
                            </button>
                          </h5>
                        </div>
                    
                        <div id="collapse0" class="collapse" aria-labelledby="heading0" data-parent="#accordion">
                          <div class="card-body">
                              
                              <div class="row">
                              <?php $i=1;$spdbycid = $this->Menu_model->get_spdbycid($cid); foreach($spdbycid as $spd){?>
                                <div class="col-sm-12 col-md-4 col-lg-4 mb-4">
                                    <div class="card p-3 border rounded border-success hover-div d-flex flex-column align-items-stretch h-100 text-dark">
                                        (<?=$i?>) School Name<br><b><?=$spd->sname?></b><hr>
                                        Addess<br><b><?=$spd->saddress?></b><hr>
                                        City<br><b><?=$spd->scity?></b><hr>
                                        State<br><b><?=$spd->sstate?></b><hr>
                                        Last Task<br><b>Task Name</b><hr>
                                        Last Task By<br><b></b><hr>
                                        Last Task Date<br><b></b><hr>
                                        <div class="rounded-circle bg-danger" style="position: absolute;bottom: -10px; left: 40%; transform: translateX(-50%); width: 20px; height: 20px;"></div>
                                        <div class="rounded-circle bg-danger" style="position: absolute;bottom: -10px; left: 60%; transform: translateX(-50%); width: 20px; height: 20px;"></div>
                                    </div>
                                </div>
                              <?php $i++;} ?>
                           </div>
                        </div>
                      </div>
                     </div>
                        
                     <?php $i=1; 
                     
                     $hiprocess = $this->Menu_model->get_htiprocess();
                     foreach($hiprocess as $hip){
                        $mhtid = $hip->tid;
                        if($mhtid<13){if($mhtid!=2){
                        $exdate = $this->Menu_model->get_mtupbycntid($cid,$mhtid);
                     ?>
                      <div class="card col-sm-12 col-md-12 col-lg-12 mb-4 text-dark text-center">
                        <div class="card-header" id="heading<?=$i?>">
                          <h5 class="mb-0">
                            <button class="btn btn-link text-dark" data-toggle="collapse" data-target="#collapse<?=$i?>" aria-expanded="true" aria-controls="collapseOne">
                              <h5><?=$hip->tmane?> (<?php if($exdate){echo $this->Menu_model->get_odformat($exdate[0]->expacteddt);}?>)</h5>
                            </button>
                          </h5>
                        </div>
                    
                        <div id="collapse<?=$i?>" class="collapse" aria-labelledby="heading<?=$i?>" data-parent="#accordion">
                          <div class="card-body">
                              <div class="row">
                              <?php 
                                  $tid=$hip->tid; 
                                  $htimsprocess = $this->Menu_model->get_htimsprocess($tid);
                                  foreach($htimsprocess as $himsp){
                                  $stid = $himsp->tid;
                                  $mtdada = $this->Menu_model->get_mstubycntsid($cid,$tid,$stid);
                                  if($mtdada){
                                      $taskby = $mtdada[0]->tby;
                                      $taskdate = $mtdada[0]->tdate;
                                      if(is_numeric($taskby)==1){
                                          $bdname = $this->Menu_model->get_bdnamebyid($taskby);
                                          $taskby = $bdname[0]->name;
                                      }else{$taskby=$mtdada[0]->tby;}
                                  }
                                  
                                    if ($himsp->slink != '') {
                                        $link = '<a href="' . base_url() . 'Menu/' . $himsp->slink . '/' . $cid . '">';
                                        $lc = '</a>';
                                    } else {
                                        $link = '';
                                        $lc = '';
                                    }
                                  
                              ?>
                                <div class="col-sm-12 col-md-4 col-lg-4 mb-4">
                                    <div class="card p-3 border rounded border-success hover-div d-flex flex-column align-items-stretch h-100 text-dark">
                                        <?=$link?>
                                        <b><?=$himsp->tname?></b><hr>
                                        Perform By<br><b><?php if($taskby!=''){echo $taskby;}?></b><hr>
                                        Perform Date<br><b><?php if($taskdate!=''){echo $this->Menu_model->get_dformat($taskdate);}?></b><hr>
                                        <?=$lc?>
                                        <div class="rounded-circle bg-danger" style="position: absolute;bottom: -10px; left: 40%; transform: translateX(-50%); width: 20px; height: 20px;"></div>
                                        <div class="rounded-circle bg-danger" style="position: absolute;bottom: -10px; left: 60%; transform: translateX(-50%); width: 20px; height: 20px;"></div>
                                    </div>
                                </div>
                              <?php $taskby='';$taskdate='';} ?>
                              
                              <div class="col-sm-12 col-md-4 col-lg-4 mb-4">
                                    <div class="card p-3 border rounded border-success hover-div d-flex flex-column align-items-stretch h-100 text-dark">
                                        <b>Reminder</b><hr>
                                        
                                        
                                        
                                        <button class="btn" id="add_comment<?=$stid?>" value="<?=$tid?>,<?=$cid?>">Reminder</button><br><br>
                                        <div class="rounded-circle bg-danger" style="position: absolute;bottom: -10px; left: 40%; transform: translateX(-50%); width: 20px; height: 20px;"></div>
                                        <div class="rounded-circle bg-danger" style="position: absolute;bottom: -10px; left: 60%; transform: translateX(-50%); width: 20px; height: 20px;"></div>
                                    </div>
                              </div>
                              
                              <div class="col-sm-12 col-md-4 col-lg-4 mb-4">
                                    <div class="card p-3 border rounded border-success hover-div d-flex flex-column align-items-stretch h-100 text-dark">
                                        <b>Warning</b><hr>
                                        
                                        <button class="btn" id="add_Warn<?=$stid?>" value="<?=$tid?>,<?=$cid?>">Warning</button><br><br>
                                        <div class="rounded-circle bg-danger" style="position: absolute;bottom: -10px; left: 40%; transform: translateX(-50%); width: 20px; height: 20px;"></div>
                                        <div class="rounded-circle bg-danger" style="position: absolute;bottom: -10px; left: 60%; transform: translateX(-50%); width: 20px; height: 20px;"></div>
                                    </div>
                              </div>
                                
                              </div>
                           </div>
                        </div>
                      </div>
                      
                      <?php $i++;}}} ?>
                    </div>
                   
         
                   
          </div>
      </div>
    </section>



<div id="ReminderNow" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="modal-standard-title" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modal-standard-title1"></h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div> <!-- // END .modal-header -->
                <div class="modal-body">
                    <div class="card card-form col-md-12">
                       <div class="card-header bg-info">Reminder</div>
                        <div class="col-lg-12 card-body">
                           <?=form_open('Menu/HandBINDReminder')?>
                           <input type="hidden" id="rid" name="rid">
                           <textarea rows="10" name="creminder" class="form-control"></textarea><hr>
                           <button type="submit" class="btn btn-primary mt-3" onclick="this.form.submit(); this.disabled = true;">Submit</button>
                           </form>
                        </div>
                        </div>
                            </div>
                        </div>
                </div> <!-- // END .modal-body -->
</div></div></div>


<div id="WarnNow" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="modal-standard-title" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modal-standard-title1"></h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div> <!-- // END .modal-header -->
                <div class="modal-body">
                    <div class="card card-form col-md-12">
                       <div class="card-header bg-info">Warning</div>
                        <div class="col-lg-12 card-body">
                           <?=form_open('Menu/HandBINDWarn')?>
                           <input type="hidden" id="wid" name="wid">
                           <textarea rows="10" name="cwarning" class="form-control"></textarea><hr>
                           <button type="submit" class="btn btn-primary mt-3" onclick="this.form.submit(); this.disabled = true;">Submit</button>
                           </form>
                        </div>
                        </div>
                            </div>
                        </div>
                </div> <!-- // END .modal-body -->
</div></div></div>
    
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
<script type="text/javascript">
$('[id^="add_comment"]').on('click', function() {
        var rid = this.value;
    $('#ReminderNow').modal('show');
    document.getElementById("rid").value = rid;
});

$('[id^="add_Warn"]').on('click', function() {
        var wid = this.value;
    $('#WarnNow').modal('show');
    document.getElementById("wid").value = wid;
});
  

</script>
    
    
    
   </div>
</div> 
    
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
      "responsive": false, "lengthChange": false, "autoWi$dth": false,
      "buttons": ["copy", "csv", "excel", "pdf", "print", "colvis"]
    }).buttons().container().appen$dto('#example1_wrapper .col-md-6:eq(0)');
</script>
</body>
</html>

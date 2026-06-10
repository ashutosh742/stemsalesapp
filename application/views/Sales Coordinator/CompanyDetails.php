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
  <?php include 'addlog.php';?>
  <!-- /.navbar -->

  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1 class="m-0">Company Details</h1>
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
      <div class="container-fluid">
       <div class="row p-3">
          <div class="col-sm col-lg-8">
            <!-- Profile Image -->
            <div class="card card-primary card-outline">
              <div class="card-body box-profile">
                <?php foreach($cd as $c){ ?>
                <div class="row">
                    <div class="col-sm col-lg-6"><h4><?=$c->compname?></h4></div>
                    <div class="col-sm col-lg-3"><h5><?=$cstatus=$cstatus[0]->name?></h5></div>
                    <div class="col-sm col-lg-3"><button type="button" id="add_createact" class="btn btn-light"><b>+</b> Create Task</button></div>
                </div>
<hr>
               <div class="row">
                    <div class="col-sm col-lg-4">Total Logs:</div>
                    <div class="col-sm col-lg-8"><?=sizeof($tblc)?></div>
                    <div class="col-sm col-lg-4">BD Assigned:</div>
                    <div class="col-sm col-lg-8"><?php $mbd = $this->Menu_model->get_userbyid($mainbd);echo $mbd[0]->name?></div>
                    <div class="col-sm col-lg-4">PST Assigned:</div>
                    <div class="col-sm col-lg-8">
			<?php
			// $apst =$mbd[0]->pst_co;
     
 			if($apst){
			$apst = $this->Menu_model->get_userbyid($apst);echo $apst[0]->name;
			}else{
				echo '';
			}
			?></div>
                    <div class="col-sm col-lg-4">Conversion Details:</div>
                    <div class="col-sm col-lg-8">
                        <?php foreach($status as $st){
                            $status = $st->name;
                            $color = $st->color;
                            ?>
                        <span class="badge p-2 m-1" style="background-color: <?=$color?>;"><?=$status?>&nbsp;&nbsp;<span class="badge bg-light">0</span></span>
                        <?php } ?>
                    </div>
                    <?php if(sizeof($ccd)>0){$contactperson=$ccd[0]->contactperson;$phoneno=$ccd[0]->phoneno;$emailid=$ccd[0]->emailid;$designation=$ccd[0]->designation;}
                    else{$contactperson="";$phoneno="";$emailid="";$designation="";}?>
                    <div class="col-sm col-lg-4">Email:</div>
                    <div class="col-sm col-lg-8"><?=$emailid?></div>
                    
                    <div class="col-sm col-lg-4">Address:</div>
                    <div class="col-sm col-lg-8"><?=$c->address?></div>
                    
                    <div class="col-sm col-lg-4">State:</div>
                    <div class="col-sm col-lg-8"><?=$c->state?></div>
                
                    <div class="col-sm col-lg-4">City:</div>
                    <div class="col-sm col-lg-8"><?=$c->city?></div>
                    
                    <div class="col-sm col-lg-4">Country:</div>
                    <div class="col-sm col-lg-8"><?=$c->country?></div>
                    
                    <div class="col-sm col-lg-4">Website:</div>
                    <div class="col-sm col-lg-8"><?=$c->website?></div>
                    
                    <div class="col-sm col-lg-4">Phone No:</div>
                    <div class="col-sm col-lg-8"><?=$phoneno?>
                        <span>
                            <a href="tel:+91<?=$phoneno?>" target="_blank" style="padding:7px;border-radius:20px;">
                                <img src="https://stemlearning.in/opp/assets/image/icon/call.png" style="height:30px;">
                            </a>
                            <a href="https://wa.me/91<?=$phoneno?>" target="_blank" style="padding:7px;border-radius:20px;">
                                <img src="https://stemlearning.in/opp/assets/image/icon/whatsapp.png" style="height:30px;">
                            </a>
                        </span>
                    </div>
                
                </div>
              </div>
              <div class="card-footer text-muted p-3">
                  <b>Logs</b>&nbsp;&nbsp;&nbsp;&nbsp;|&nbsp;&nbsp;&nbsp;&nbsp;<a href="../PrimaryContact/<?=$c->id?>"><b>Edit Contact</b></a>
              </div>
              <!-- /.card-body -->
            </div>
            <!-- /.card -->
            
            </div>
            <?php } ?>
          <!-- /.col -->
          <div class="col-sm col-lg-4 showdata">
            <div class="card card-primary card-outline">
              <div class="card-body box-profile">
                  <div class="row">
                    <div class="col-sm col-lg-5"><h4>Contact</h4></div>
                    <div class="col-sm col-lg-7">
                      <!-- <button type="button" id="add_cont" class="btn btn-light" value="<?=$c->id?>" style="float:right;"><b>+</b> Add Contact</button> -->
                      <a class="btn btn-primary" href="<?=base_url().'Menu/AddPrimaryContactDetailsNew/'.$target_cid;?>">
                          Add OR Edit Contact 
                      </a>
                    </div>
                </div><hr>
                <?php foreach($pccd as $d){$contactperson=$d->contactperson;$phoneno=$d->phoneno;$emailid=$d->emailid;$designation=$d->designation;$type=$d->type;?>
                    <div class="col-sm"><b>(<?=$type?>)</b></div>
                    <div class="col-sm">Name : <?=$contactperson?></div>
                    <div class="col-sm">Designation : <?=$designation?></div>
                    <div class="col-sm">Email id : <?=$emailid?></div>
                    <div class="col-sm">Phone No : <?=$phoneno?>
                        <span>
                            <a href="tel:+91<?=$phoneno?>" target="_blank" style="padding:7px;border-radius:20px;">
                            <img src="https://stemlearning.in/opp/assets/image/icon/call.png" style="height:30px;"></a>
                            <a href="https://wa.me/91<?=$phoneno?>" target="_blank" style="padding:7px;border-radius:20px;">
                            <img src="https://stemlearning.in/opp/assets/image/icon/whatsapp.png" style="height:30px;"></a>
                        </span>
                        <!-- <br><a href="../EditContact/<?=$d->id?>/<?=$c->id?>">Edit Contact</a> -->
                    </div>
                <?php } ?>
              </div>
              </div>
              <!-- /.card-header -->
              <?php if($tbllast[0]->status_id==6){?>
              <div class="card card-success card-outline">
              <div class="card-body box-profile">
                  <div class="row">
                    <div class="col-sm col-lg-5"><h4>Positive Status</h4></div>
                </div><hr>
                <?php foreach($init as $in){ ?>
                    <div class="col-sm">Proposal Shared BY : </div>
                    <div class="col-sm">No of School : <?=$in->noofschools?></div>
                    <div class="col-sm">Final Budget : <?=$in->fbudget?></div>
                <?php } ?>
              </div>
              </div>
              <?php } ?>
              <!-- /.card-header -->
              
            </div>
            <!-- /.card -->
  </div>
  
  
  <div class="col-12">
    <div class="card">
              <div class="card-header">
                <h3 class="card-title">Completed Task Logs</h3>
              </div>
              <!-- /.card-header -->

          
              <div class="card-body">
                        <div class="table-responsive">
                            <div class="table-responsive">
                                <table id="example1" class="table table-striped table-bordered" cellspacing="0" width="100%">
                                    <thead>
                                        <tr>								
                                            <th>Sr Number</th>
                                            <th>MOM</th>
                                            <th>Remarks</th>
                                            <th>Current Action</th>
                                            <th>Action Taken</th>
                                            <th>Purpose Achieved</th>
                                            <th>Updated Date</th>
                                            <th>Updated By</th>
                                            <th>Last Status</th>
                                            <th>Updated Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                     
                                      <?php 
                                 
                                      $i=1; foreach($tblc as $tb){
                                          if($tb->nextCFID!='0' && $tb->nstatus_id!=''){
                                          $tid = $tb->id;
                                          $uid = $tb->user_id;
                                          $aid = $tb->actiontype_id;
                                          $lastsid = $tb->status_id;
                                          $upsid = $tb->nstatus_id;
                               
                                          $ui=$this->Menu_model->get_userbyid($uid);
                                          $ai=$this->Menu_model->get_actionbyid($aid);
                                          $lsi=$this->Menu_model->get_statusbyid($lastsid);
                                          $usi=$this->Menu_model->get_statusbyid($upsid);
                                      ?>
                                    <tr>
                                        <td><?=$i?></td>
                                        <td><?=$tb->mom?></td>
                                        <td><?=$tb->remarks?></td>
                                        <td><?=$ai[0]->name?></td>
                                        <td><?=$tb->actontaken?></td>
                                        <td><?=$tb->purpose_achieved?></td>
                                        <td><?=$tb->updateddate?></td>
                                        <td><?=$ui[0]->name?></td>
                                        <td><?=$lsi[0]->name;?></td>
                                        <td><?=$usi[0]->name;?></td>
                                    </tr>
                                    <?php $i++;}} ?>
                                  </tbody>
                                </table>
                            </div>
                        </div>
              </div>
              
              
              <div class="col-12">
    <div class="card">
              <div class="card-header bg-danger">
                <h3 class="card-title"><b>Pending Task Logs</b></h3>
              </div>
              <!-- /.card-header -->
              <div class="card-body">
                        <div class="table-responsive">
                            <div class="table-responsive">
                                <table id="example1" class="table table-striped table-bordered" cellspacing="0" width="100%">
                                    <thead>
                                        <tr>								
                                            <th>Sr Number</th>
                                            <th>Current Action</th>
                                            <th>Created Date</th>
                                            <th>Task For</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                      <?php $i=1; foreach($tblc as $tb){
                                          $tid = $tb->id;
                                          $uid = $tb->user_id;
                                          $aid = $tb->actiontype_id;
                                          $ui=$this->Menu_model->get_userbyid($uid);
                                          $ai=$this->Menu_model->get_actionbyid($aid);
                                           if($tb->nextCFID=='0'){
                                      ?>
                                    <tr>
                                        <td><?=$i?></td>
                                        <td><?=$ai[0]->name?></td>
                                        <td><?=$tb->updateddate?></td>
                                        <td><?=$ui[0]->name?></td>
                                    </tr>
                                    <?php $i++;}} ?>
                                  </tbody>
                                </table>
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
<script src="<?=base_url();?>assets/js/myjs.js"></script>

<!-- AdminLTE dashboard demo (This is only for demo purposes) -->
<script src="<?=base_url();?>assets/js/dashboard.js"></script>

<script>
    $("#example1").DataTable({
      "responsive": false, "lengthChange": false, "autoWidth": false,
      "buttons": ["copy", "csv", "excel", "pdf", "print", "colvis"]
    }).buttons().container().appendTo('#example1_wrapper .col-md-6:eq(0)');
</script>
</body>
</html>